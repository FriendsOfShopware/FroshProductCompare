<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\Page;

use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Content\Property\PropertyGroupCollection;
use Shopware\Core\System\CustomField\CustomFieldCollection;
use Shopware\Storefront\Page\Page;

class CompareProductPage extends Page
{
    protected ProductListingResult $products;

    protected PropertyGroupCollection $properties;

    protected CustomFieldCollection $customFields;

    public function getProducts(): ProductListingResult
    {
        return $this->products;
    }

    public function setProducts(ProductListingResult $products): void
    {
        $this->products = $products;
    }

    public function getProperties(): PropertyGroupCollection
    {
        return $this->properties;
    }

    public function setProperties(PropertyGroupCollection $properties): void
    {
        $this->properties = $properties;
    }

    public function getCustomFields(): CustomFieldCollection
    {
        return $this->customFieldCollection;
    }

    public function setCustomFields(CustomFieldCollection $customFieldCollection): void
    {
        $this->customFieldCollection = $customFieldCollection;
    }
}
