<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\CrossSellingComparable;

use Shopware\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CrossSellingComparableDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'frosh_cross_selling_comparable';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CrossSellingComparableCollection::class;
    }

    public function getEntityClass(): string
    {
        return CrossSellingComparableEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->setFlags(new PrimaryKey(), new Required()),
            (new FkField('product_cross_selling_id', 'productCrossSellingId', ProductCrossSellingDefinition::class))->addFlags(new Required()),

            new BoolField('is_comparable', 'isComparable'),

            new OneToOneAssociationField('productCrossSelling', 'product_cross_selling_id', 'id', ProductCrossSellingDefinition::class, false),
        ]);
    }
}
