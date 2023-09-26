<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\Page;

use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewCollection;
use Shopware\Core\Content\Product\Cart\ProductGatewayInterface;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Content\Property\PropertyGroupCollection;
use Shopware\Core\Content\Property\PropertyGroupEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\CustomField\CustomFieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\Product\Review\ProductReviewLoader;
use Symfony\Component\HttpFoundation\Request;

class CompareProductPageLoader
{
    public const MAX_COMPARE_PRODUCT_ITEMS = 4;

    /**
     * @param EntityRepository<CustomFieldCollection> $customFieldRepository
     */
    public function __construct(
        private readonly ProductGatewayInterface $productGateway,
        private readonly GenericPageLoaderInterface $genericLoader,
        private readonly EntityRepository $customFieldRepository,
        private readonly SystemConfigService $systemConfigService,
        private readonly ProductReviewLoader $productReviewLoader
    ) {
    }

    public function loadPreview(array $productIds, Request $request, SalesChannelContext $salesChannelContext): CompareProductPage
    {
        $productIds = array_filter(\array_slice($productIds, 0, self::MAX_COMPARE_PRODUCT_ITEMS), function ($id) {
            return Uuid::isValid($id);
        });

        $page = $this->genericLoader->load($request, $salesChannelContext);

        $page = CompareProductPage::createFrom($page);

        if (empty($productIds)) {
            $page->setProducts(ProductListingResult::createFrom(new ProductCollection()));

            return $page;
        }

        $criteria = new Criteria();
        $criteria->setIds($productIds)->setLimit(self::MAX_COMPARE_PRODUCT_ITEMS);
        $criteria->addAssociation('featureSet');

        $products = $this->productGateway->get($productIds, $salesChannelContext);

        $result = ProductListingResult::createFrom($products);

        $page->setProducts($result);

        return $page;
    }

    /**
     * @param  list<string>  $productIds
     */
    public function load(array $productIds, Request $request, SalesChannelContext $salesChannelContext): CompareProductPage
    {
        $productIds = array_filter($productIds, function ($id) {
            return Uuid::isValid($id);
        });

        $page = $this->genericLoader->load($request, $salesChannelContext);

        /** @var CompareProductPage $page */
        $page = CompareProductPage::createFrom($page);

        if (empty($productIds)) {
            $page->setProducts(ProductListingResult::createFrom(new ProductCollection()));

            return $page;
        }

        $products = $this->productGateway->get($productIds, $salesChannelContext);

        $result = ProductListingResult::createFrom($products);

        $result = $this->loadProductCompareData($result, $salesChannelContext);

        $page->setProducts($result);

        $properties = $this->loadProperties($result);

        $page->setProperties($properties);
        $page->setCustomFields($this->loadCustomFields($salesChannelContext));

        return $page;
    }

    public function loadProductCompareData(ProductListingResult $products, SalesChannelContext $context): ProductListingResult
    {
        $selectedProperties = [];
        $showSelectedProperties = $this->systemConfigService->getBool('FroshProductCompare.config.showSelectedProperties', $context->getSalesChannelId());

        if ($showSelectedProperties) {
            $selectedProperties = $this->systemConfigService->get('FroshProductCompare.config.selectedProperties', $context->getSalesChannelId());
            $selectedProperties = array_map(function ($property) {
                return $property['id'];
            }, $selectedProperties);
        }

        $reviewAllowed = $this->isReviewAllowed($context);

        /** @var SalesChannelProductEntity $product */
        foreach ($products as $product) {
            if ($reviewAllowed) {
                $product->setProductReviews($this->loadProductReviews($product, $context));
            } else {
                $product->setRatingAverage(null);
                $product->setProductReviews(new ProductReviewCollection());
            }

            $sortedProperties = $this->sortProperties($product, $selectedProperties);

            $product->setSortedProperties($sortedProperties);
        }

        return $products;
    }

    public function loadProperties(ProductListingResult $products): PropertyGroupCollection
    {
        $properties = new PropertyGroupCollection();

        /** @var SalesChannelProductEntity $product */
        foreach ($products as $product) {
            foreach ($product->getSortedProperties() as $group) {
                if ($properties->has($group->getId())) {
                    continue;
                }

                // we don't need more data of the PropertyGroup, so we just set id and translated instead of cloning
                $propertyGroup = new PropertyGroupEntity();
                $propertyGroup->setId($group->getId());
                $propertyGroup->setTranslated($group->getTranslated());

                $properties->add($propertyGroup);
            }
        }

        $properties->sort(function ($a, $b) {
            if ($a->getTranslation('name') === $b->getTranslation('name')) {
                return $a->getTranslation('position') - $b->getTranslation('position');
            }

            return strcasecmp($a->getTranslation('name'), $b->getTranslation('name'));
        });

        return $properties;
    }

    private function isReviewAllowed(SalesChannelContext $context): bool
    {
        if (!$this->systemConfigService->getBool('core.listing.showReview', $context->getSalesChannelId())) {
            return false;
        }

        $hiddenAttributes = $this->systemConfigService->get('FroshProductCompare.config.hideAttributes', $context->getSalesChannelId());

        if (!\is_array($hiddenAttributes)) {
            return true;
        }

        return !\in_array('rating', $hiddenAttributes, true);
    }

    private function sortProperties(SalesChannelProductEntity $product, array $selectedProperties): PropertyGroupCollection
    {
        $properties = $product->getProperties();

        if (!empty($selectedProperties)) {
            $properties = $properties->filter(function (PropertyGroupOptionEntity $property) use ($selectedProperties) {
                return \in_array($property->getGroupId(), $selectedProperties, true);
            });
        }

        if ($properties === null) {
            return new PropertyGroupCollection();
        }

        $sorted = [];

        /** @var PropertyGroupOptionEntity $option */
        foreach ($properties as $option) {
            $group = $sorted[$option->getGroupId()] ?? PropertyGroupEntity::createFrom($option->getGroup());

            if (!$group) {
                continue;
            }

            $options = $group->getOptions();

            if (!$options) {
                $options = new PropertyGroupOptionCollection();
            }

            $options->add($option);

            $group->setOptions($options);

            $sorted[$group->getId()] = $group;
        }

        $propertyGroupCollection = new PropertyGroupCollection($sorted);
        $propertyGroupCollection->sortByPositions();
        $propertyGroupCollection->sortByConfig();

        return $propertyGroupCollection;
    }

    private function loadProductReviews(SalesChannelProductEntity $product, SalesChannelContext $context): ProductReviewCollection
    {
        $request = new Request();
        $request->request->set('parentId', $product->getParentId());
        $request->request->set('productId', $product->getId());
        $reviews = $this->productReviewLoader->load($request, $context);

        return $reviews->getEntities();
    }

    private function loadCustomFields(SalesChannelContext $context): CustomFieldCollection
    {
        $selectedCustomFields = (array) $this->systemConfigService->get('FroshProductCompare.config.selectedCustomFields', $context->getSalesChannelId());

        if (empty($selectedCustomFields)) {
            return new CustomFieldCollection([]);
        }

        $criteria = new Criteria($selectedCustomFields);
        $criteria->addSorting(new FieldSorting('name', 'ASC'));

        return $this->customFieldRepository->search($criteria, $context->getContext())->getEntities();
    }
}
