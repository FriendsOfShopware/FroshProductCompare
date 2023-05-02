<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\CrossSellingComparable;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<CrossSellingComparableEntity>
 */
class CrossSellingComparableCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'frosh_product_compare_cross_selling_comparable_collection';
    }

    protected function getExpectedClass(): string
    {
        return CrossSellingComparableEntity::class;
    }
}
