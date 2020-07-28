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

    /**
     * @return string
     */
    public function getProductCrossSellingId(): string
    {
        return $this->productCrossSellingId;
    }

    /**
     * @param  string  $productCrossSellingId
     */
    public function setProductCrossSellingId(string $productCrossSellingId): void
    {
        $this->productCrossSellingId = $productCrossSellingId;
    }

    /**
     * @return ProductCrossSellingEntity
     */
    public function getProductCrossSelling(): ProductCrossSellingEntity
    {
        return $this->productCrossSelling;
    }

    /**
     * @param  ProductCrossSellingEntity  $productCrossSelling
     */
    public function setProductCrossSelling(ProductCrossSellingEntity $productCrossSelling): void
    {
        $this->productCrossSelling = $productCrossSelling;
    }

    /**
     * @return bool|null
     */
    public function isComparable(): ?bool
    {
        return $this->isComparable;
    }

    /**
     * @return bool|null
     */
    public function getIsComparable(): ?bool
    {
        return $this->isComparable;
    }

    /**
     * @param  bool|null  $isComparable
     */
    public function setIsComparable(?bool $isComparable): void
    {
        $this->isComparable = $isComparable;
    }

    /**
     * @param  bool|null  $isComparable
     */
    public function setComparable(?bool $isComparable): void
    {
        $this->isComparable = $isComparable;
    }
}
