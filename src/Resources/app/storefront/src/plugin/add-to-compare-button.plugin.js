import Plugin from 'src/plugin-system/plugin.class';
import CompareLocalStorageHelper from '../helper/compare-local-storage.helper';

export default class AddToCompareButtonPlugin extends Plugin {
    static options = {
        isAddedToCompareClass: 'is-added-to-compare'
    };

    init() {
        this.REMOVE_COMPARE_PRODUCT_EVENT = 'removeCompareProduct';

        this._checkAddedProduct();
        this._registerEvents();
    }

    _isAddedProduct(productId) {
        const products = CompareLocalStorageHelper.getAddedProductsList();

        return products.indexOf(productId) > -1;
    }

    _checkAddedProduct() {
        const { productId, addedText, isAddedToCompareClass } = this.options;

        if (this._isAddedProduct(productId) && addedText) {
            this._toggleText(this.el, addedText);
            this.el.classList.add(isAddedToCompareClass);
        }
    }

    _toggleText(el, text) {
        if (this.options.showIconOnly) {
            return;
        }

        // For decorating purpose
        const buttonText = el.querySelector('.compare-button-text');
        if (buttonText) {
            buttonText.textContent = text;
            return
        }

        el.textContent = text;
    }

    _registerCompareButtonSelection() {
        const compareButton = this.el;

        const {
            isAddedToCompareClass,
            productId
        } = this.options;

        compareButton.addEventListener('click', () => {
            try {
                if (compareButton.classList.contains(isAddedToCompareClass)) {
                    this._handleBeforeRemove();

                    CompareLocalStorageHelper.remove(productId);
                } else {
                    const addResult = CompareLocalStorageHelper.add(productId);

                    if (addResult) {
                        this._handleBeforeAdd();
                    }
                }
            } catch (e) {
                CompareLocalStorageHelper.clear();
            }
        });
    }

    _handleBeforeRemove() {
        const {
            defaultText,
            isAddedToCompareClass,
            productId
        } = this.options;

        const product = { productId };

        if (this.el.closest('td')) {
            product.productRow = this.el.closest('td').cellIndex;
        } else if (this.el.closest('.offcanvas-comparison-item')) {
            this.el.closest('.offcanvas-comparison-item').style.display = 'none';
        } else {
            this._toggleText(this.el, defaultText);
            this.el.classList.remove(isAddedToCompareClass);
        }

        document.$emitter.publish(this.REMOVE_COMPARE_PRODUCT_EVENT, { product });
    }

    _handleBeforeAdd() {
        const {
            addedText,
            isAddedToCompareClass
        } = this.options;

        this._toggleText(this.el, addedText);
        this.el.classList.add(isAddedToCompareClass);
    }

    _registerEvents() {
        this._registerCompareButtonSelection();

        const { productId, isAddedToCompareClass, defaultText } = this.options;

        document.$emitter.subscribe(this.REMOVE_COMPARE_PRODUCT_EVENT, event => {
            if (event.detail.product.productId === productId) {
                this._toggleText(this.el, defaultText);
                this.el.classList.remove(isAddedToCompareClass);
            }
        });
    }
}
