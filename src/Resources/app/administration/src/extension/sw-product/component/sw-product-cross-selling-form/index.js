import template from './sw-product-cross-selling-form.html.twig';
import './sw-product-cross-selling-form.scss';
import { MAXIMUM_COMPARE_PRODUCT_ITEMS } from '../../../../constant/frosh-product-compare.constant';

const { Component, Utils } = Shopware;

Component.override('sw-product-cross-selling-form', {
    template,

    data() {
        return {
            originalLimit: this.crossSelling.limit
        }
    },

    watch: {
        'crossSelling.extensions.crossSellingComparable.isComparable': {
            deep: true,
            immediate: true,
            handler(value) {
                this.crossSelling.limit = value ? MAXIMUM_COMPARE_PRODUCT_ITEMS : this.originalLimit;
            }
        }
    },

    created() {
        let crossSellingComparable = Utils.get(this.crossSelling, 'extensions.crossSellingComparable', null);

        if (!crossSellingComparable) {
            crossSellingComparable =
                this.repositoryFactory.create('frosh_cross_selling_comparable').create(Shopware.Context.api);
            crossSellingComparable.isComparable = false;
            this.crossSelling.extensions.crossSellingComparable = crossSellingComparable;
        }

        if (Utils.get(this.crossSelling, 'extensions.crossSellingComparable.isComparable', false)) {
            this.crossSelling.limit = MAXIMUM_COMPARE_PRODUCT_ITEMS;
        }
    }
});
