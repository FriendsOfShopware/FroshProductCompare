"use strict";(self.webpackChunk=self.webpackChunk||[]).push([["frosh-product-compare"],{2615:(t,e,s)=>{s.d(e,{Z:()=>a});var i=s(3637),o=s(8254),r=s(7906);let n=null;class a extends i.Z{static open(t=!1,e=!1,s=null,o="left",r=!0,n=i.Z.REMOVE_OFF_CANVAS_DELAY(),a=!1,c=""){if(!t)throw new Error("A url must be given!");i.r._removeExistingOffCanvas();const d=i.r._createOffCanvas(o,a,c,r);this.setContent(t,e,s,r,n),i.r._openOffcanvas(d)}static setContent(t,e,s,i,c){const d=new o.Z;super.setContent(`<div class="offcanvas-body">${r.Z.getTemplate()}</div>`,i,c),n&&n.abort();const l=t=>{super.setContent(t,i,c),"function"==typeof s&&s(t)};n=e?d.post(t,e,a.executeCallback.bind(this,l)):d.get(t,a.executeCallback.bind(this,l))}static executeCallback(t,e){"function"==typeof t&&t(e),window.PluginManager.initializePlugins()}}},3637:(t,e,s)=>{s.d(e,{Z:()=>l,r:()=>d});var i=s(9658),o=s(2005),r=s(1966);const n="offcanvas",a=350;class c{constructor(){this.$emitter=new o.Z}open(t,e,s,i,o,r,n){this._removeExistingOffCanvas();const a=this._createOffCanvas(s,r,n,i);this.setContent(t,i,o),this._openOffcanvas(a,e)}setContent(t,e,s){const i=this.getOffCanvas();i[0]&&(i[0].innerHTML=t,this._registerEvents(s))}setAdditionalClassName(t){this.getOffCanvas()[0].classList.add(t)}getOffCanvas(){return document.querySelectorAll(`.${n}`)}close(t){const e=this.getOffCanvas();r.Z.iterate(e,(t=>{bootstrap.Offcanvas.getInstance(t).hide()})),setTimeout((()=>{this.$emitter.publish("onCloseOffcanvas",{offCanvasContent:e})}),t)}goBackInHistory(){window.history.back()}exists(){return this.getOffCanvas().length>0}_openOffcanvas(t,e){c.bsOffcanvas.show(),window.history.pushState("offcanvas-open",""),"function"==typeof e&&e()}_registerEvents(t){const e=i.Z.isTouchDevice()?"touchend":"click",s=this.getOffCanvas();r.Z.iterate(s,(e=>{const i=()=>{setTimeout((()=>{e.remove(),this.$emitter.publish("onCloseOffcanvas",{offCanvasContent:s})}),t),e.removeEventListener("hide.bs.offcanvas",i)};e.addEventListener("hide.bs.offcanvas",i)})),window.addEventListener("popstate",this.close.bind(this,t),{once:!0});const o=document.querySelectorAll(".js-offcanvas-close");r.Z.iterate(o,(s=>s.addEventListener(e,this.close.bind(this,t))))}_removeExistingOffCanvas(){c.bsOffcanvas=null;const t=this.getOffCanvas();return r.Z.iterate(t,(t=>t.remove()))}_getPositionClass(t){return"left"===t?"offcanvas-start":"right"===t?"offcanvas-end":`offcanvas-${t}`}_createOffCanvas(t,e,s,i){const o=document.createElement("div");if(o.classList.add(n),o.classList.add(this._getPositionClass(t)),!0===e&&o.classList.add("is-fullwidth"),s){const t=typeof s;if("string"===t)o.classList.add(s);else{if(!Array.isArray(s))throw new Error(`The type "${t}" is not supported. Please pass an array or a string.`);s.forEach((t=>{o.classList.add(t)}))}}return document.body.appendChild(o),c.bsOffcanvas=new bootstrap.Offcanvas(o,{backdrop:!1!==i||"static"}),o}}const d=Object.freeze(new c);class l{static open(t,e=null,s="left",i=!0,o=350,r=!1,n=""){d.open(t,e,s,i,o,r,n)}static setContent(t,e=!0,s=350){d.setContent(t,e,s)}static setAdditionalClassName(t){d.setAdditionalClassName(t)}static close(t=350){d.close(t)}static exists(){return d.exists()}static getOffCanvas(){return d.getOffCanvas()}static REMOVE_OFF_CANVAS_DELAY(){return a}}},6468:(t,e,s)=>{var i=s(6285),o=s(8254),r=s(4690),n=s(6656);function a(t,e,s){var i;return(e="symbol"==typeof(i=function(t,e){if("object"!=typeof t||!t)return t;var s=t[Symbol.toPrimitive];if(void 0!==s){var i=s.call(t,e||"default");if("object"!=typeof i)return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===e?String:Number)(t)}(e,"string"))?i:String(i))in t?Object.defineProperty(t,e,{value:s,enumerable:!0,configurable:!0,writable:!0}):t[e]=s,t}class c{static getAddedProductsList(){let t=n.Z.getItem(this.key);try{t=JSON.parse(t)||[]}catch(e){this.clear(),t={}}return this._checkCompareProductStorage(t)?t:(this.clear(),{})}static add(t){const e=this.getAddedProductsList(),s=e.length;return document.$emitter.publish("beforeAddProductCompare",{productCount:s}),!(s+1>this.maximumCompareProducts)&&(e.push(t),this.persist(e),!0)}static remove(t){const e=this.getAddedProductsList(),s=e.indexOf(t);return-1!==s&&(e.splice(s,1),this.persist(e),!0)}static persist(t){n.Z.setItem(this.key,JSON.stringify(t)),document.$emitter.publish("changedProductCompare",{products:t})}static clear(){n.Z.setItem(this.key,null)}static _checkCompareProductStorage(t){return t.length<=this.maximumCompareProducts}}a(c,"key","compare-widget-added-products"),a(c,"maximumCompareProducts",4);const d=c;class l extends i.Z{init(){this._client=new o.Z,this._clearBtn=this.el.querySelector(".btn-clear"),this._printBtn=this.el.querySelector(".btn-printer"),this.registerShowDifferencesBtnEvent(),this.insertStoredContent(),this._registerEvents()}registerShowDifferencesBtnEvent(){const t=document.querySelector(".btn-show-differences");t.addEventListener("change",(()=>{if(t.checked){document.querySelectorAll("tbody#specification tr.property:not(:first-child)").forEach((t=>{const e=Array.from(t.querySelectorAll("td.properties-value")).map((t=>t.textContent.trim()));e.every((t=>t===e[0]))&&(t.style.display="none")}))}else{document.querySelectorAll("tbody#specification tr.property").forEach((t=>{t.style.display="table-row"}))}}))}insertStoredContent(){this.fetch()}fetch(){const t={};t.productIds=d.getAddedProductsList(),r.Z.create(this.el),this._client.post(window.router["frontend.compare.content"],JSON.stringify(t),(t=>{r.Z.remove(this.el),this.renderCompareProducts(t),this.$emitter.publish("insertStoredContent",{response:t})}))}renderCompareProducts(t){this.el.querySelector(".compare-product-content").innerHTML=t,window.PluginManager.initializePlugins()}_registerEvents(){document.$emitter.subscribe("removeCompareProduct",(t=>{const e=this.el.querySelector("table"),s=e.rows;if(2===e.querySelectorAll("thead tr td").length)return e.style.display="none",d.clear(),void this.insertStoredContent();for(let e=0;e<s.length;e+=1)try{s[e].deleteCell(t.detail.product.productRow)}catch(t){}})),this._clearBtn.addEventListener("click",(()=>{d.clear(),this.fetch()})),this._printBtn.addEventListener("click",(()=>{window.print()}))}}class u extends i.Z{init(){this.REMOVE_COMPARE_PRODUCT_EVENT="removeCompareProduct",this._checkAddedProduct(),this._registerEvents()}_isAddedProduct(t){return d.getAddedProductsList().indexOf(t)>-1}_checkAddedProduct(){const{productId:t,addedText:e,isAddedToCompareClass:s}=this.options;this._isAddedProduct(t)&&e&&(this._toggleText(this.el,e),this.el.classList.add(s))}_toggleText(t,e){if(this.options.showIconOnly)return;const s=t.querySelector(".compare-button-text");s?s.textContent=e:t.textContent=e}_registerCompareButtonSelection(){const t=this.el,{isAddedToCompareClass:e,productId:s}=this.options;t.addEventListener("click",(()=>{try{if(t.classList.contains(e))this._handleBeforeRemove(),d.remove(s);else{d.add(s)&&this._handleBeforeAdd()}}catch(t){d.clear()}}))}_handleBeforeRemove(){const{defaultText:t,isAddedToCompareClass:e,productId:s}=this.options,i={productId:s};this.el.closest("td")?i.productRow=this.el.closest("td").cellIndex:this.el.closest(".offcanvas-comparison-item")?this.el.closest(".offcanvas-comparison-item").style.display="none":(this._toggleText(this.el,t),this.el.classList.remove(e)),document.$emitter.publish(this.REMOVE_COMPARE_PRODUCT_EVENT,{product:i})}_handleBeforeAdd(){const{addedText:t,isAddedToCompareClass:e}=this.options;this._toggleText(this.el,t),this.el.classList.add(e)}_registerEvents(){this._registerCompareButtonSelection();const{productId:t,isAddedToCompareClass:e,defaultText:s}=this.options;document.$emitter.subscribe(this.REMOVE_COMPARE_PRODUCT_EVENT,(i=>{i.detail.product.productId===t&&(this._toggleText(this.el,s),this.el.classList.remove(e))}))}}var h,f,p,m;h=u,p={isAddedToCompareClass:"is-added-to-compare"},(f="symbol"==typeof(m=function(t,e){if("object"!=typeof t||!t)return t;var s=t[Symbol.toPrimitive];if(void 0!==s){var i=s.call(t,e||"default");if("object"!=typeof i)return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===e?String:Number)(t)}(f="options","string"))?m:String(m))in h?Object.defineProperty(h,f,{value:p,enumerable:!0,configurable:!0,writable:!0}):h[f]=p;var v=s(9658),b=s(2615),g=s(378);class C extends i.Z{init(){this._button=this.el.querySelector(this.options.buttonSelector),this._defaultPadding=window.getComputedStyle(this._button).getPropertyValue("bottom"),this._badge=this._button.querySelector(".badge"),this._updateButtonCounter(d.getAddedProductsList()),this._addBodyPadding(),this._registerEvents()}_updateButtonCounter(t){const e=t.length;this._badge.innerText=e,0===e?this._button.classList.remove("is-visible"):this._button.classList.contains("is-visible")||this._button.classList.add("is-visible")}_registerEvents(){const t=v.Z.isTouchDevice()?"touchstart":"click";this._button&&this._button.addEventListener(t,(()=>{this._openOffcanvas(),this.$emitter.publish("onClickCompareFloatButton")})),document.$emitter.subscribe("beforeAddProductCompare",(t=>{const{productCount:e}=t.detail;if(e>=d.maximumCompareProducts){new g.Z(this.options.maximumNumberCompareProductsText).open()}})),document.$emitter.subscribe("changedProductCompare",(t=>{this._updateButtonCounter(t.detail.products)})),document.addEventListener("scroll",this._debouncedOnScroll,!1);new MutationObserver(this._addBodyPadding.bind(this)).observe(document.body,{attributes:!0,attributeFilter:["style"]})}_addBodyPadding(){this._button.style.bottom=`calc(${this._defaultPadding} + ${document.body.style.paddingBottom||"0px"})`}_openOffcanvas(){const t={productIds:d.getAddedProductsList()};b.Z.open(window.router["frontend.compare.offcanvas"],JSON.stringify(t),(t=>{this.$emitter.publish("insertStoredContent",{response:t})}))}}!function(t,e,s){e=function(t){var e=function(t,e){if("object"!=typeof t||!t)return t;var s=t[Symbol.toPrimitive];if(void 0!==s){var i=s.call(t,e||"default");if("object"!=typeof i)return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===e?String:Number)(t)}(t,"string");return"symbol"==typeof e?e:String(e)}(e),e in t?Object.defineProperty(t,e,{value:s,enumerable:!0,configurable:!0,writable:!0}):t[e]=s}(C,"options",{buttonSelector:".js-compare-float-button"});const y=window.PluginManager;y.register("AddToCompareButton",u,"[data-add-to-compare-button]"),y.register("CompareWidget",l,"[data-compare-widget]"),y.register("CompareFloat",C,"[data-compare-float]")}},t=>{t.O(0,["vendor-node","vendor-shared"],(()=>{return e=6468,t(t.s=e);var e}));t.O()}]);