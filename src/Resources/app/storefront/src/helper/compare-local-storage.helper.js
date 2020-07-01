import Storage from 'src/helper/storage/storage.helper';

class CompareLocalStorageHelper {
    static key = 'compare-widget-added-products';

    static maximumCompareProducts = 4;

    static getAddedProductsList() {
        let products = Storage.getItem(this.key);

        try {
            products = JSON.parse(products) || [];
        } catch {
            this.clear();
            products = {};
        }

        if (!this._checkCompareProductStorage(products)) {
            this.clear();

            return {};
        }

        return products;
    }

    static add(productId) {
        const products = this.getAddedProductsList();
        const productCount = products.length;

        document.$emitter.publish('beforeAddProductCompare', {
            productCount
        });

        if (productCount + 1 > this.maximumCompareProducts) {
            return false;
        }

        products.push(productId);

        this.persist(products);

        return true;
    }

    static remove(productId) {
        const products = this.getAddedProductsList();

        const index = products.indexOf(productId);

        if (index === -1) {
            return false;
        }

        products.splice(index, 1);

        this.persist(products);

        return true;
    }

    static persist(products) {
        Storage.setItem(this.key, JSON.stringify(products));

        document.$emitter.publish('changedProductCompare', { products });
    }

    static clear() {
        Storage.setItem(this.key, null);
    }

    static _checkCompareProductStorage(products) {
        return products.length <= this.maximumCompareProducts;
    }
}

export default CompareLocalStorageHelper;
