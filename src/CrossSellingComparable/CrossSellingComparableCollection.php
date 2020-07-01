<?php declare(strict_types=1);

namespace Justa\SimpleProductCompare\CrossSellingComparable;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                              add(CrossSellingComparableEntity $entity)
 * @method void                              set(string $key, CrossSellingComparableEntity $entity)
 * @method CrossSellingComparableEntity[]    getIterator()
 * @method CrossSellingComparableEntity[]    getElements()
 * @method CrossSellingComparableEntity|null get(string $key)
 * @method CrossSellingComparableEntity|null first()
 * @method CrossSellingComparableEntity|null last()
 */
class CrossSellingComparableCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'simple_product_compare_cross_selling_comparable_collection';
    }

    protected function getExpectedClass(): string
    {
        return CrossSellingComparableEntity::class;
    }
}
