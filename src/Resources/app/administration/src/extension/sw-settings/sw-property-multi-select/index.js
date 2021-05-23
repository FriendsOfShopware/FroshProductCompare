const { Component } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.extend('sw-property-multi-select', 'sw-entity-multi-select', {
    props: {
        entityCollection: {
            type: Array,
            required: true,
            default() {
                return new EntityCollection(
                    '/property_group',
                    'property_group',
                    Shopware.Context.api,
                    new Criteria(1, this.resultLimit)
                );
            }
        },

        entityName: {
            type: String,
            required: false,
            default: 'property_group'
        },
    },
});
