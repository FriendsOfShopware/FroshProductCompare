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
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
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

    public function __construct(
        private readonly ProductGatewayInterface $productGateway,
        private readonly GenericPageLoaderInterface $genericLoader,
        private readonly EntityRepository $customFieldRepository,
        private readonly SystemConfigService $systemConfigService,
        private readonly ProductReviewLoader $productReviewLoader
    ) {
    }

    /**
     * @param array<string> $productIds
     */
    public function loadPreview(array $productIds, Request $request, SalesChannelContext $salesChannelContext): CompareProductPage
    {
        $productIds = array_filter(\array_slice($productIds, 0, self::MAX_COMPARE_PRODUCT_ITEMS), function (string $id) {
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
        $productIds = array_filter($productIds, function (string $id) {
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
        $page->setCustomFields($this->loadCustomFields($salesChannelContext, $products));

        return $page;
    }

    public function loadProductCompareData(ProductListingResult $products, SalesChannelContext $context): ProductListingResult
    {
        $selectedPropertyIds = [];
        $showSelectedProperties = $this->systemConfigService->getBool('FroshProductCompare.config.showSelectedProperties', $context->getSalesChannelId());

        if ($showSelectedProperties) {
            $selectedProperties = $this->systemConfigService->get('FroshProductCompare.config.selectedProperties', $context->getSalesChannelId());

            if (\is_array($selectedProperties)) {
                $selectedPropertyIds = \array_column($selectedProperties, 'id');
            }
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

            $sortedProperties = $this->sortProperties($product, $selectedPropertyIds);

            $product->setSortedProperties($sortedProperties);
        }

        return $products;
    }

    public function loadProperties(ProductListingResult $products): PropertyGroupCollection
    {
        $properties = new PropertyGroupCollection();

        /** @var SalesChannelProductEntity $product */
        foreach ($products as $product) {
            if ($product->getSortedProperties() === null) {
                continue;
            }

            foreach ($product->getSortedProperties() as $group) {
                if ($properties->has($group->getId())) {
                    continue;
                }

                // we don't need more data of the PropertyGroup, so we just set id, translated and visibleOnProductDetailPage instead of cloning
                $propertyGroup = new PropertyGroupEntity();
                $propertyGroup->setId($group->getId());
                $propertyGroup->setTranslated($group->getTranslated());
                $propertyGroup->setVisibleOnProductDetailPage($group->getVisibleOnProductDetailPage());


                $properties->add($propertyGroup);
            }
        }

        $properties->sort(function (PropertyGroupEntity $a, PropertyGroupEntity $b) {
            $nameA = $a->getTranslation('name');
            $nameB = $b->getTranslation('name');

            if (!\is_string($nameA) || !\is_string($nameB)) {
                return 0;
            }

            if ($a->getTranslation('name') === $b->getTranslation('name')) {
                $positionA = $a->getTranslation('position');
                $positionB = $b->getTranslation('position');

                if (!\is_int($positionA) || !\is_int($positionB)) {
                    return 0;
                }

                return $positionA - $positionB;
            }

            return strcasecmp($nameA, $nameB);
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

    /**
     * @param array<string> $selectedPropertyIds
     */
    private function sortProperties(SalesChannelProductEntity $product, array $selectedPropertyIds): PropertyGroupCollection
    {
        $properties = $product->getProperties();

        if ($properties === null) {
            return new PropertyGroupCollection();
        }

        if (!empty($selectedPropertyIds)) {
            $properties = $properties->filter(function (PropertyGroupOptionEntity $property) use ($selectedPropertyIds) {
                return \in_array($property->getGroupId(), $selectedPropertyIds, true);
            });
        }

        $sorted = [];

        /** @var PropertyGroupOptionEntity $option */
        foreach ($properties as $option) {
            $group = $this->getGroupByProperty($sorted, $option);

            if ($group === null) {
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

    /**
     * @param array<string, PropertyGroupEntity> $sorted
     */
    private function getGroupByProperty(array $sorted, PropertyGroupOptionEntity $option): ?PropertyGroupEntity
    {
        if (!empty($sorted[$option->getGroupId()]) && $sorted[$option->getGroupId()] instanceof PropertyGroupEntity) {
            return $sorted[$option->getGroupId()];
        }

        if ($option->getGroup() === null) {
            return null;
        }

        return PropertyGroupEntity::createFrom($option->getGroup());
    }

    private function loadProductReviews(SalesChannelProductEntity $product, SalesChannelContext $context): ProductReviewCollection
    {
        $request = new Request();
        $request->request->set('parentId', $product->getParentId());
        $request->request->set('productId', $product->getId());
        $reviews = $this->productReviewLoader->load($request, $context)->getEntities();

        if ($reviews instanceof ProductReviewCollection) {
            return $reviews;
        }

        return new ProductReviewCollection();
    }

    private function loadCustomFields(SalesChannelContext $context, ProductCollection $products): CustomFieldCollection
    {
        /** @var array<string> $selectedCustomFields */
        $selectedCustomFields = (array) $this->systemConfigService->get('FroshProductCompare.config.selectedCustomFields', $context->getSalesChannelId());

        if (empty($selectedCustomFields)) {
            return new CustomFieldCollection([]);
        }

        $criteria = new Criteria($selectedCustomFields);
        $criteria->addSorting(new FieldSorting('name', 'ASC'));

        if ($this->systemConfigService->getBool('FroshProductCompare.config.hideEmptyCustomFields', $context->getSalesChannelId())) {
            $availableCustomFieldNames = $this->getAvailableCustomFieldNames($products);
            $criteria->addFilter(new EqualsAnyFilter('name', $availableCustomFieldNames));
        }

        $customFields = $this->customFieldRepository->search($criteria, $context->getContext())->getEntities();

        if ($customFields instanceof CustomFieldCollection) {
            return $customFields;
        }

        return new CustomFieldCollection();
    }

    /**
     * @return array<string>
     */
    private function getAvailableCustomFieldNames(ProductCollection $products): array
    {
        $availableCustomFieldNames = [];

        foreach ($products as $product) {
            $productCustomFields = $product->getTranslation('customFields');
            if (!\is_array($productCustomFields)) {
                continue;
            }

            /** @var array<string> $productsCustomFieldNames */
            $productsCustomFieldNames = \array_keys($productCustomFields);

            array_push($availableCustomFieldNames, ...$productsCustomFieldNames);
        }

        return $availableCustomFieldNames;
    }

    /**
     * @param array $productIds
     * @param SalesChannelContext $salesChannelContext
     * @return array<string>
     */
    public function verifyProducts(array $productIds, SalesChannelContext $salesChannelContext): array
    {
        $productIds = array_filter(\array_slice($productIds, 0, self::MAX_COMPARE_PRODUCT_ITEMS), function ($id) {
            return Uuid::isValid($id);
        });

        if (empty($productIds)) {
            return [];
        }

        $products = $this->productGateway->get($productIds, $salesChannelContext);
        if(!$products->count()) {
            return [];
        }

        return array_values($products->map(fn($product) => $product->getId()));
    }
}
