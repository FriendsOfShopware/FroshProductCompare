class CompareLocalStorageHelper {
    static key = 'compare-widget-added-products';

    static maximumCompareProducts = 4;

    static getAddedProductsList() {
        let products;

        try {
            products = JSON.parse(window.localStorage.getItem(this.key)) || [];
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
            productCount,
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
        window.localStorage.setItem(this.key, JSON.stringify(products));

        document.$emitter.publish('changedProductCompare', { products });
    }

    static clear() {
        window.localStorage.removeItem(this.key);

        document.$emitter.publish('changedProductCompare', { products: [] });
    }

    static _checkCompareProductStorage(products) {
        return products.length <= this.maximumCompareProducts;
    }
}

export default CompareLocalStorageHelper;
