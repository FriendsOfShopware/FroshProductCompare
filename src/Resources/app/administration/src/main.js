import './extension/sw-product/component/sw-product-cross-selling-form';
import './extension/sw-product/view/sw-product-detail-cross-selling';
import './extension/sw-product/page/sw-product-detail';

Shopware.Module.register('simple-product-compare', {
    type: 'plugin',
    name: 'SimpleProductCompare',
    title: 'simple-product-compare.generalInformation.mainMenuItemGeneral',
    description: 'simple-product-compare.generalInformation.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#9AA8B5',
    icon: 'default-shopping-paper-bag'
});
