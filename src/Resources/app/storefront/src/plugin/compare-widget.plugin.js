import ElementLoadingIndicatorUtil from 'src/utility/loading-indicator/element-loading-indicator.util';
import CompareLocalStorageHelper from '../helper/compare-local-storage.helper';

export default class CompareWidgetPlugin extends window.PluginBaseClass {
    init() {
        this._clearBtn = this.el.querySelector('.btn-clear');
        this._printBtn = this.el.querySelector('.btn-printer');
        this.registerShowDifferencesBtnEvent();
        this.insertStoredContent();
        this._registerEvents();
    }

    /**
     *
     */
    registerShowDifferencesBtnEvent()
    {
        const btnShowDifferences = document.querySelector('.btn-show-differences');
        btnShowDifferences.addEventListener('change', () => {
            if (btnShowDifferences.checked) {
                const propertyRows = document.querySelectorAll('tbody#specification tr.property:not(:first-child)');
                propertyRows.forEach(row => {
                    const columns = Array.from(row.querySelectorAll('td.properties-value')).map(column => column.textContent.trim());
                    if (columns.every(column => column === columns[0])) {
                        row.style.display = 'none';
                    }
                });
            } else {
                const allPropertyRows = document.querySelectorAll('tbody#specification tr.property');
                allPropertyRows.forEach(row => {
                    row.style.display = 'table-row';
                });
            }
        });

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

        ElementLoadingIndicatorUtil.create(this.el);

        fetch(window.router['frontend.compare.content'], {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data),
        }).then(r => r.text()).then((text) => {
            ElementLoadingIndicatorUtil.remove(this.el);

            this.renderCompareProducts(text);

            this.$emitter.publish('insertStoredContent', { response: text });
        })
    }

    renderCompareProducts(html) {
        this.el.querySelector('.compare-product-content').innerHTML = html;
        window.PluginManager.initializePlugins();
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
            window.print();
        });
    }
}
