<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\CrossSellingComparable;

use Shopware\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CrossSellingComparableEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $productCrossSellingId;

    /**
     * @var ProductCrossSellingEntity
     */
    protected $productCrossSelling;

    /**
     * @var bool|null
     */
    protected $isComparable;

    public function getProductCrossSellingId(): string
    {
        return $this->productCrossSellingId;
    }

    public function setProductCrossSellingId(string $productCrossSellingId): void
    {
        $this->productCrossSellingId = $productCrossSellingId;
    }

    public function getProductCrossSelling(): ProductCrossSellingEntity
    {
        return $this->productCrossSelling;
    }

    public function setProductCrossSelling(ProductCrossSellingEntity $productCrossSelling): void
    {
        $this->productCrossSelling = $productCrossSelling;
    }

    public function isComparable(): ?bool
    {
        return $this->isComparable;
    }

    public function getIsComparable(): ?bool
    {
        return $this->isComparable;
    }

    public function setIsComparable(?bool $isComparable): void
    {
        $this->isComparable = $isComparable;
    }

    public function setComparable(?bool $isComparable): void
    {
        $this->isComparable = $isComparable;
    }
}
