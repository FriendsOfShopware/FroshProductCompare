import Storage from 'src/helper/storage/storage.helper';
import HttpClient from 'src/service/http-client.service';

class CompareLocalStorageHelper {
    static key = 'compare-widget-added-products';

    static maximumCompareProducts = 4;

    static verifyAddedProductsList() {
        const products = this.getAddedProductsList();
        const jsonProducts = JSON.stringify({
            productIds: products
        });

        return new Promise(fulfill => {
            new HttpClient().post("/compare/verify-products", jsonProducts, (responseText, response) => {
                const failed = !response || response.status >= 400
                    || !responseText;

                if (failed) {
                    this.persist([]);
                    fulfill();
                    return;
                }

                const productIds = JSON.parse(responseText);
                this.persist(productIds);
                fulfill();
            });
        });
    }

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
