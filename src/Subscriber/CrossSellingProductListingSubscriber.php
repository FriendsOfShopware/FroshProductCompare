<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\Subscriber;

use Doctrine\DBAL\Connection;
use Frosh\FroshProductCompare\CrossSellingComparable\CrossSellingComparableEntity;
use Frosh\FroshProductCompare\Page\CompareProductPageLoader;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingLoader;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Product\CrossSelling\CrossSellingElement;
use Shopware\Storefront\Page\Product\CrossSelling\CrossSellingLoadedEvent;
use Shopware\Storefront\Page\Product\CrossSelling\CrossSellingProductCriteriaEvent;
use Shopware\Storefront\Page\Product\CrossSelling\CrossSellingProductListCriteriaEvent;
use Shopware\Storefront\Page\Product\CrossSelling\CrossSellingProductStreamCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CrossSellingProductListingSubscriber implements EventSubscriberInterface
{
    /**
     * @var CompareProductPageLoader
     */
    private $compareProductPageLoader;

    /**
     * @var ProductListingLoader
     */
    private $productListingLoader;

    public function __construct(
        CompareProductPageLoader $compareProductPageLoader,
        ProductListingLoader $productListingLoader
    ) {
        $this->compareProductPageLoader = $compareProductPageLoader;
        $this->productListingLoader = $productListingLoader;
    }

    public static function getSubscribedEvents()
    {
        return [
            CrossSellingProductStreamCriteriaEvent::class => [
                ['handleCriteriaLoadedRequest', 201]
            ],
            CrossSellingProductListCriteriaEvent::class => [
                ['handleCriteriaLoadedRequest', 201]
            ],
            CrossSellingLoadedEvent::class => [
                ['handleCrossSellingLoadedEvent', 201]
            ]
        ];
    }

    public function handleCriteriaLoadedRequest(CrossSellingProductCriteriaEvent $event): void
    {
        $crossSelling = $event->getCrossSelling();
        /** @var CrossSellingComparableEntity $crossSellingComparable */
        $crossSellingComparable = $crossSelling->getExtension('crossSellingComparable');

        if (!$crossSellingComparable || !$crossSellingComparable->isComparable()) {
            return;
        }

        $criteria = $event->getCriteria();

        $criteria
            ->addAssociation('media')
            ->addAssociation('prices')
            ->addAssociation('manufacturer')
            ->addAssociation('manufacturer.media')
            ->addAssociation('cover')
            ->addAssociation('options.group')
            ->addAssociation('properties.group')
            ->addAssociation('properties.media')
            ->addAssociation('mainCategories.category')
            ->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [new EqualsFilter('id', $crossSelling->getProductId())]))
            ->setLimit(CompareProductPageLoader::MAX_COMPARE_PRODUCT_ITEMS - 1);
    }

    public function handleCrossSellingLoadedEvent(CrossSellingLoadedEvent $event)
    {
        $crossSellings = $event->getCrossSellingResult();

        $context = $event->getContext();

        $salesChannelContext =  $event->getSalesChannelContext();

        /** @var CrossSellingElement $crossSellingElement */
        foreach ($crossSellings as $crossSellingElement) {

            $crossSelling = $crossSellingElement->getCrossSelling();

            /** @var CrossSellingComparableEntity $crossSellingComparable */
            $crossSellingComparable = $crossSelling->getExtension('crossSellingComparable');

            if (!$crossSellingComparable || !$crossSellingComparable->isComparable()) {
                continue;
            }

            $featureProduct = $this->getFeaturedProduct($crossSelling->getProductId(), $salesChannelContext);

            $products = new ProductCollection([$featureProduct]);

            $compareProducts = $crossSellingElement->getProducts();

            $products->merge($compareProducts);

            $products = ProductListingResult::createFrom($products);

            $productWithComparableData = $this->compareProductPageLoader->loadProductCompareData($products, $context);

            $crossSellingElement->setProducts(new ProductCollection($productWithComparableData));
        }
    }

    private function getFeaturedProduct(string $productId, SalesChannelContext $context): SalesChannelProductEntity
    {
        $criteria = $this->compareProductPageLoader->getCompareProductListCriteria([$productId]);

        $products = $this->productListingLoader->load($criteria, $context);

        return $products->first();
    }
}
