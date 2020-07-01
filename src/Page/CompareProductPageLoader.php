<?php declare(strict_types=1);

namespace Justa\SimpleProductCompare\Page;

use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewCollection;
use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewEntity;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingLoader;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Content\Property\PropertyGroupCollection;
use Shopware\Core\Content\Property\PropertyGroupEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\FilterAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\MaxAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Query\ScoreQuery;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\Product\Review\ProductReviewLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CompareProductPageLoader
{
    const MAX_COMPARE_PRODUCT_ITEMS = 4;

    /**
     * @var GenericPageLoaderInterface
     */
    private $genericLoader;
    /**
     * @var ProductListingLoader
     */
    private $productListingLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var EntityRepositoryInterface
     */
    private $productReviewRepository;

    public function __construct(
        ProductListingLoader $productListingLoader,
        GenericPageLoaderInterface $genericLoader,
        EventDispatcherInterface $eventDispatcher,
        EntityRepositoryInterface $productReviewRepository
    ) {
        $this->productListingLoader = $productListingLoader;
        $this->genericLoader = $genericLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->productReviewRepository = $productReviewRepository;
    }

    public function loadPreview(array $productIds, Request $request, SalesChannelContext $salesChannelContext): CompareProductPage
    {
        $productIds = array_filter($productIds, function ($id) {
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

        $products = $this->productListingLoader->load($criteria, $salesChannelContext);

        $result = ProductListingResult::createFrom($products);

        $page->setProducts($result);

        return $page;
    }

    /**
     * @param  array  $productIds
     * @param  Request  $request
     * @param  SalesChannelContext  $salesChannelContext
     * @return CompareProductPage
     */
    public function load(array $productIds, Request $request, SalesChannelContext $salesChannelContext): CompareProductPage
    {
        $productIds = array_filter($productIds, function ($id) {
           return Uuid::isValid($id);
        });

        $page = $this->genericLoader->load($request, $salesChannelContext);

        $page = CompareProductPage::createFrom($page);

        if (empty($productIds)) {
            $page->setProducts(ProductListingResult::createFrom(new ProductCollection()));
            return $page;
        }

        $criteria = $this->getCompareProductListCriteria($productIds);

        $this->eventDispatcher->dispatch(
            new ProductListingCriteriaEvent($request, $criteria, $salesChannelContext)
        );

        $products = $this->productListingLoader->load($criteria, $salesChannelContext);

        $result = ProductListingResult::createFrom($products);

        $result = $this->loadProductCompareData($result, $salesChannelContext->getContext());

        $this->eventDispatcher->dispatch(
            new ProductListingResultEvent($request, $result, $salesChannelContext)
        );

        $page->setProducts($result);

        return $page;
    }

    private function sortProperties(SalesChannelProductEntity $product): PropertyGroupCollection
    {
        $properties = $product->getProperties();

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

    public function getCompareProductListCriteria(array $productIds): Criteria
    {
        $criteria = new Criteria();
        $criteria->setIds($productIds)
        ->addAssociation('media')
        ->addAssociation('prices')
        ->addAssociation('manufacturer')
        ->addAssociation('manufacturer.media')
        ->addAssociation('cover')
        ->addAssociation('options.group')
        ->addAssociation('properties.group')
        ->addAssociation('properties.media')
        ->addAssociation('mainCategories.category')
        ->setLimit(self::MAX_COMPARE_PRODUCT_ITEMS);

        return $criteria;
    }

    private function loadProductReviews(array $productIds, Context $context): EntityCollection
    {
        $criteria = new Criteria();
        $criteria->addAggregation(new CountAggregation('count', 'id'));
        $criteria->addFilter(new EqualsFilter('status',  true));
        $criteria->addFilter(
            new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsAnyFilter('product.id', $productIds),
                new EqualsAnyFilter('product.parentId', $productIds),
            ])
        );

        return $this->productReviewRepository->search($criteria, $context)->getEntities();
    }

    public function loadProductCompareData(ProductListingResult $products, Context $context): ProductListingResult
    {
        $productReviews = $this->loadProductReviews($products->getIds(), $context);

        /** @var SalesChannelProductEntity $product */
        foreach ($products as $product) {
            if (!$product->getProductReviews()) {
                $product->setProductReviews(new ProductReviewCollection());
            }

            $productReviews->filter(function (ProductReviewEntity $productReviewEntity) use ($product, $productReviews) {
                if ($productReviewEntity->getProductId() === $product->getId()) {
                    $product->getProductReviews()->add($productReviewEntity);

                    $productReviews->remove($productReviewEntity->getId());
                }
            });

            $sortedProperties = $this->sortProperties($product);

            $product->setSortedProperties($sortedProperties);
        }

        return $products;
    }
}
