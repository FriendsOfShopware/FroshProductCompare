const { Component } = Shopware;

Component.override('sw-product-detail', {
    computed: {
        productCriteria() {
            const criteria = this.$super('productCriteria');

            criteria.addAssociation('crossSellings.crossSellingComparable');

            return criteria;
        }
    }
});
