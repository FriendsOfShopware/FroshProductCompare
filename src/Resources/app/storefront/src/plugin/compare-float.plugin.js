import AjaxOffcanvas from 'src/plugin/offcanvas/ajax-offcanvas.plugin';
import PseudoModalUtil from 'src/utility/modal-extension/pseudo-modal.util';
import CompareLocalStorageHelper from '../helper/compare-local-storage.helper';

export default class CompareFloatPlugin extends window.PluginBaseClass {
    static options = {
        buttonSelector: '.js-compare-float-button',
    };

    init() {
        this._button = this.el.querySelector(this.options.buttonSelector);
        this._defaultPadding = window
            .getComputedStyle(this._button)
            .getPropertyValue('bottom');
        this._badge = this._button.querySelector('.badge');

        this._updateButtonCounter(
            CompareLocalStorageHelper.getAddedProductsList()
        );
        this._addBodyPadding();
        this._registerEvents();
    }

    _updateButtonCounter(products) {
        const count = products.length;
        this._badge.innerText = count;

        if (count === 0) {
            this._button.classList.remove('is-visible');
        } else if (!this._button.classList.contains('is-visible')) {
            this._button.classList.add('is-visible');
        }
    }

    /**
     * registers all needed events
     *
     * @private
     */
    _registerEvents() {
        const submitEvent =
            'ontouchstart' in document.documentElement ? 'touchstart' : 'click';

        if (this._button) {
            this._button.addEventListener(submitEvent, (event) => {
                event.preventDefault();
                this._openOffcanvas();
                this.$emitter.publish('onClickCompareFloatButton');
            });
        }

        document.$emitter.subscribe('beforeAddProductCompare', (event) => {
            const { productCount } = event.detail;

            if (
                productCount >= CompareLocalStorageHelper.maximumCompareProducts
            ) {
                const pseudoModal = new PseudoModalUtil(
                    this.options.maximumNumberCompareProductsText
                );

                pseudoModal.open();
            }
        });

        document.$emitter.subscribe('changedProductCompare', (event) => {
            this._updateButtonCounter(event.detail.products);
        });

        document.addEventListener('scroll', this._debouncedOnScroll, false);
        const observer = new MutationObserver(this._addBodyPadding.bind(this));
        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['style'],
        });
    }

    _addBodyPadding() {
        this._button.style.bottom = `calc(${this._defaultPadding} + ${document.body.style.paddingBottom || '0px'})`;
    }

    _openOffcanvas() {
        const formData = new FormData();

        for (const productId of CompareLocalStorageHelper.getAddedProductsList()) {
            formData.append('productIds[]', productId);
        }

        AjaxOffcanvas.open(
            window.router['frontend.compare.offcanvas'],
            formData,
            (response) => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, 'text/html');
                const container = doc.querySelector('[data-valid-product-ids]');
                let removedCount = 0;

                if (container) {
                    const validIds = Object.values(JSON.parse(container.dataset.validProductIds));
                    removedCount = CompareLocalStorageHelper.sync(validIds);
                }

                if (removedCount > 0) {
                    this._showRemovedProductsAlert(removedCount);
                }

                this.$emitter.publish('insertStoredContent', { response });
            }
        );
    }

    _showRemovedProductsAlert(removedCount) {
        const offcanvasContent = document.querySelector('.offcanvas-cart');
        if (!offcanvasContent) {
            return;
        }

        const header = offcanvasContent.querySelector('.offcanvas-cart-header');
        if (!header) {
            return;
        }

        const alert = document.createElement('div');
        alert.setAttribute('role', 'alert');
        alert.setAttribute('aria-live', 'polite');
        alert.className = 'alert alert-info d-flex align-items-center alert-dismissible fade show';
        alert.innerHTML = `
            <div class="alert-content-container">
                ${this.options.productsRemovedText.replace('%count%', removedCount)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
        `;

        header.insertAdjacentElement('afterend', alert);
    }
}
