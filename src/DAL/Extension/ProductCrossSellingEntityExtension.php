<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\DAL\Extension;

use Frosh\FroshProductCompare\CrossSellingComparable\CrossSellingComparableDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductCrossSellingEntityExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField('crossSellingComparable', 'id', 'product_cross_selling_id', CrossSellingComparableDefinition::class))->addFlags(new CascadeDelete())
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductCrossSellingDefinition::class;
    }
}
