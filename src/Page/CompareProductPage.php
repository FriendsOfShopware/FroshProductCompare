<?php

namespace Justa\SimpleProductCompare\Page;

use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Storefront\Page\Page;

class CompareProductPage extends Page
{
    /**
     * @var ProductListingResult
     */
    protected $products;

    public function getProducts(): ProductListingResult
    {
        return $this->products;
    }

    public function setProducts(ProductListingResult $products): void
    {
        $this->products = $products;
    }
}
