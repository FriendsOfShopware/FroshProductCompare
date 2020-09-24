
const { Component } = Shopware;

Component.override('sw-product-detail-cross-selling', {
    methods: {
        onAddCrossSelling() {
            const crossSellingRepository = this.repositoryFactory.create(
                this.product.crossSellings.entity,
                this.product.crossSellings.source
            );

            this.crossSelling = crossSellingRepository.create(Shopware.Context.api);
            const crossSellingComparableRepo = this.repositoryFactory.create('frosh_cross_selling_comparable');

            const crossSellingComparable = crossSellingComparableRepo.create(Shopware.Context.api);
            crossSellingComparable.isComparable = false;

            this.crossSelling.productId = this.product.id;
            this.crossSelling.position = this.product.crossSellings.length + 1;
            this.crossSelling.type = 'productStream';
            this.crossSelling.sortBy = 'name';
            this.crossSelling.sortDirection = 'ASC';
            this.crossSelling.limit = 24;
            this.crossSelling.extensions.crossSellingComparable = crossSellingComparable;

            this.product.crossSellings.push(this.crossSelling);
        }
    }
});
