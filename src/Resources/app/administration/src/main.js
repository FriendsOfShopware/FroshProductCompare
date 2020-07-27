import './extension/sw-product/component/sw-product-cross-selling-form';
import './extension/sw-product/view/sw-product-detail-cross-selling';
import './extension/sw-product/page/sw-product-detail';

Shopware.Module.register('frosh-product-compare', {
    type: 'plugin',
    name: 'FroshProductCompare',
    title: 'frosh-product-compare.generalInformation.mainMenuItemGeneral',
    description: 'frosh-product-compare.generalInformation.descriptionTextModule',
    version: '1.0.1',
    targetVersion: '1.0.1',
    color: '#9AA8B5',
    icon: 'default-shopping-paper-bag'
});
