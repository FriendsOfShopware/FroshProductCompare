<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\Subscriber;

use Frosh\FroshProductCompare\CrossSellingComparable\CrossSellingComparableEntity;
use Frosh\FroshProductCompare\Page\CompareProductPageLoader;
use Shopware\Core\Content\Product\Cart\ProductGatewayInterface;
use Shopware\Core\Content\Product\Events\ProductCrossSellingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductCrossSellingIdsCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductCrossSellingsLoadedEvent;
use Shopware\Core\Content\Product\Events\ProductCrossSellingStreamCriteriaEvent;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\CrossSellingElement;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FroshCrossSellingProductListingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CompareProductPageLoader $compareProductPageLoader,
        private readonly ProductGatewayInterface $productGateway
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductCrossSellingStreamCriteriaEvent::class => [
                ['handleCriteriaLoadedRequest', 201],
            ],
            ProductCrossSellingIdsCriteriaEvent::class => [
                ['handleCriteriaLoadedRequest', 201],
            ],
            ProductCrossSellingsLoadedEvent::class => [
                ['handleCrossSellingLoadedEvent', 201],
            ],
        ];
    }

    public function handleCriteriaLoadedRequest(ProductCrossSellingCriteriaEvent $event): void
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
            ->addAssociation('featureSet')
            ->addAssociation('options.group')
            ->addAssociation('properties.group')
            ->addAssociation('properties.media')
            ->addAssociation('mainCategories.category')
            ->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [new EqualsFilter('id', $crossSelling->getProductId())]))
            ->setLimit(CompareProductPageLoader::MAX_COMPARE_PRODUCT_ITEMS);
    }

    public function handleCrossSellingLoadedEvent(ProductCrossSellingsLoadedEvent $event): void
    {
        $crossSellings = $event->getCrossSellings();

        $salesChannelContext = $event->getSalesChannelContext();

        /** @var CrossSellingElement $crossSellingElement */
        foreach ($crossSellings as $crossSellingElement) {
            $crossSelling = $crossSellingElement->getCrossSelling();

            /** @var CrossSellingComparableEntity $crossSellingComparable */
            $crossSellingComparable = $crossSelling->getExtension('crossSellingComparable');

            if (!$crossSellingComparable || !$crossSellingComparable->isComparable()) {
                continue;
            }

            $featureProductId = $crossSelling->getProductId();

            /** @var SalesChannelProductEntity $product */
            foreach ($crossSellingElement->getProducts() as $product) {
                if ($product->getParentId() === $featureProductId) {
                    // if there's at least a variant that in the compare list, remove container product from compare list
                    $featureProductId = null;

                    break;
                }
            }

            $products = new ProductCollection();

            if ($featureProductId) {
                $featureProduct = $this->getFeaturedProduct($featureProductId, $salesChannelContext);
                $products->add($featureProduct);
            }

            $compareProducts = $crossSellingElement->getProducts();

            $products->merge($compareProducts);

            $products = ProductListingResult::createFrom($products);

            $productWithComparableData = $this->compareProductPageLoader->loadProductCompareData($products, $salesChannelContext);

            $crossSellingElement->setProducts(new ProductCollection($productWithComparableData));

            $properties = $this->compareProductPageLoader->loadProperties($products);

            $crossSelling->addExtension('compareProperties', $properties);
        }
    }

    private function getFeaturedProduct(string $productId, SalesChannelContext $context): SalesChannelProductEntity
    {
        $products = $this->productGateway->get([$productId], $context);

        /** @var SalesChannelProductEntity $salesChannelProduct */
        $salesChannelProduct = $products->get($productId);

        return $salesChannelProduct;
    }
}
