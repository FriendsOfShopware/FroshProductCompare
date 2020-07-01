import Plugin from 'src/plugin-system/plugin.class';
import PluginManager from 'src/plugin-system/plugin.manager';
import StoreApiClient from 'src/service/store-api-client.service';
import ElementLoadingIndicatorUtil from 'src/utility/loading-indicator/element-loading-indicator.util';
import CompareLocalStorageHelper from '../helper/compare-local-storage.helper';

export default class CompareWidgetPlugin extends Plugin {
    init() {
        this._client = new StoreApiClient();
        this._clearBtn = this.el.querySelector('.btn-clear');
        this._printBtn = this.el.querySelector('.btn-printer');
        this.insertStoredContent();
        this._registerEvents();
    }

    /**
     * reads the persisted content
     * from the session cache an renders it
     * into the element
     */
    insertStoredContent() {
        this.fetch();
    }

    fetch() {
        const data = {};

        data.productIds = CompareLocalStorageHelper.getAddedProductsList();
        data._csrf_token = document.querySelector('.compare-product-container > input[name=_csrf_token]').value;

        ElementLoadingIndicatorUtil.create(this.el);

        this._client.post(window.router['frontend.compare.content'], JSON.stringify(data), (response) => {
            ElementLoadingIndicatorUtil.remove(this.el);

            this.renderCompareProducts(response);

            this.$emitter.publish('insertStoredContent', { response });
        });
    }

    renderCompareProducts(html) {
        this.el.querySelector('.compare-product-content').innerHTML = html;
        PluginManager.initializePlugin('AddToCompareButton', '.compare-item-remove-button');
        PluginManager.initializePlugin('AddToCart', '.buy-widget');
    }

    _registerEvents() {
        document.$emitter.subscribe('removeCompareProduct', (event) => {
            const table = this.el.querySelector('table');
            const rows = table.rows;

            if (table.querySelectorAll('thead tr td').length === 2) {
                table.style.display = 'none';
                CompareLocalStorageHelper.clear();
                this.insertStoredContent();
                return;
            }

            for (let i = 0; i < rows.length; i += 1) {
                try {
                    rows[i].deleteCell(event.detail.product.productRow);
                } catch (e) {
                    // nth
                }
            }
        });

        this._clearBtn.addEventListener('click', () => {
            CompareLocalStorageHelper.clear();
            this.fetch();
        });

        this._printBtn.addEventListener('click', () => {
            const body = document.body;
            const footer = document.querySelector('footer');
            const logo = document.querySelector('.header-logo-col');

            body.classList.add('hide-on-print');
            footer.classList.add('show-on-print');
            logo.classList.add('show-on-print');

            window.print();

            body.classList.remove('hide-on-print');
            footer.classList.remove('show-on-print');
            logo.classList.remove('show-on-print');
        });
    }
}
