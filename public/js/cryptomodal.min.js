!function(u){if("undefined"!=typeof window){var e,l=0,m=!1,n=!1,p="message".length,b="[iFrameSizer]",y=b.length,v=null,r=window.requestAnimationFrame,g={max:1,scroll:1,bodyScroll:1,documentElementScroll:1},F={},i=null,h={autoResize:!0,bodyBackground:null,bodyMargin:null,bodyMarginV1:8,bodyPadding:null,checkOrigin:!0,inPageLinks:!1,enablePublicMethods:!0,heightCalculationMethod:"bodyOffset",id:"CryptoGateResizer",interval:32,log:!1,maxHeight:1/0,maxWidth:1/0,minHeight:0,minWidth:0,resizeFrom:"parent",scrolling:!1,sizeHeight:!0,sizeWidth:!1,warningTimeout:5e3,tolerance:0,widthCalculationMethod:"scroll",onClosed:function(){},onInit:function(){},onMessage:function(){O("onMessage function not defined")},onResized:function(){},onScroll:function(){return!0}},I={};window.jQuery&&((e=window.jQuery).fn?e.fn.CryptoGateResize||(e.fn.CryptoGateResize=function(i){return this.filter("iframe").each(function(e,n){d(n,i)}).end()}):z("","Unable to bind to jQuery, it is not fully loaded.")),"function"==typeof define&&define.amd?define([],B):"object"==typeof module&&"object"==typeof module.exports&&(module.exports=B()),window.CryptoGateResize=window.CryptoGateResize||B()}function w(){return window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver}function M(e,n,i){e.addEventListener(n,i,!1)}function x(e,n,i){e.removeEventListener(n,i,!1)}function o(e){return b+"["+(i="Host page: "+(n=e),window.top!==window.self&&(i=window.parentIFrame&&window.parentIFrame.getId?window.parentIFrame.getId()+": "+n:"Nested host page: "+n),i)+"]";var n,i}function t(e){return F[e]?F[e].log:m}function k(e,n){a("log",e,n,t(e))}function z(e,n){a("info",e,n,t(e))}function O(e,n){a("warn",e,n,!0)}function a(e,n,i,t){!0===t&&"object"==typeof window.console&&console[e](o(n),i)}function s(n){function a(){e("Height"),e("Width"),P(function(){j(h),C(w),l("onResized",h)},h,"init")}function e(e){var n=Number(F[w]["max"+e]),i=Number(F[w]["min"+e]),t=e.toLowerCase(),o=Number(h[t]);k(w,"Checking "+t+" is in range "+i+"-"+n),o<i&&(o=i,k(w,"Set "+t+" to min value")),n<o&&(o=n,k(w,"Set "+t+" to max value")),h[t]=""+o}function s(e){return g.substr(g.indexOf(":")+p+e)}function d(i,t){var e,n,o;e=function(){var e,n;H("Send Page Info","pageInfo:"+(e=document.body.getBoundingClientRect(),n=h.iframe.getBoundingClientRect(),JSON.stringify({iframeHeight:n.height,iframeWidth:n.width,clientHeight:Math.max(document.documentElement.clientHeight,window.innerHeight||0),clientWidth:Math.max(document.documentElement.clientWidth,window.innerWidth||0),offsetTop:parseInt(n.top-e.top,10),offsetLeft:parseInt(n.left-e.left,10),scrollTop:window.pageYOffset,scrollLeft:window.pageXOffset})),i,t)},n=32,I[o=t]||(I[o]=setTimeout(function(){I[o]=null,e()},n))}function c(e){var n=e.getBoundingClientRect();return N(w),{x:Math.floor(Number(n.left)+Number(v.x)),y:Math.floor(Number(n.top)+Number(v.y))}}function f(e){var n=e?c(h.iframe):{x:0,y:0},i={x:Number(h.width)+n.x,y:Number(h.height)+n.y};k(w,"Reposition requested from iFrame (offset x:"+n.x+" y:"+n.y+")"),window.top!==window.self?window.parentIFrame?window.parentIFrame["scrollTo"+(e?"Offset":"")](i.x,i.y):O(w,"Unable to scroll to requested position, window.parentIFrame not found"):(v=i,u(),k(w,"--"))}function u(){!1!==l("onScroll",v)?C(w):S()}function l(e,n){return R(w,e,n)}var i,t,o,r,m,g=n.data,h={},w=null;"[CryptoGateResizerChild]Ready"===g?function(){for(var e in F)H("iFrame requested init",A(e),document.getElementById(e),e)}():b===(""+g).substr(0,y)&&g.substr(y).split(":")[0]in F?(m=g.substr(y).split(":"),h={iframe:F[m[0]]&&F[m[0]].iframe,id:m[0],height:m[1],width:m[2],type:m[3]},w=h.id,F[w]&&(F[w].loaded=!0),(r=h.type in{true:1,false:1,undefined:1})&&k(w,"Ignoring init message from meta parent page"),!r&&(o=!0,F[t=w]||(o=!1,O(h.type+" No settings for "+t+". Message was: "+g)),o)&&(k(w,"Received: "+g),i=!0,null===h.iframe&&(O(w,"IFrame ("+h.id+") not found"),i=!1),i&&function(){var e,i=n.origin,t=F[w]&&F[w].checkOrigin;if(t&&""+i!="null"&&!(t.constructor===Array?function(){var e=0,n=!1;for(k(w,"Checking connection is from allowed list of origins: "+t);e<t.length;e++)if(t[e]===i){n=!0;break}return n}():(e=F[w]&&F[w].remoteHost,k(w,"Checking connection is from: "+e),i===e)))throw new Error("Unexpected message received from: "+i+" for "+h.iframe.id+". Message was: "+n.data+". This error can be disabled by setting the checkOrigin: false option or by providing of array of trusted domains.");return!0}()&&function(){switch(F[w]&&F[w].firstRun&&F[w]&&(F[w].firstRun=!1),h.type){case"close":F[w].closeRequeston?R(w,"onCloseRequest",F[w].iframe):E(h.iframe);break;case"message":r=s(6),k(w,"onMessage passed: {iframe: "+h.iframe.id+", message: "+r+"}"),l("onMessage",{iframe:h.iframe,message:JSON.parse(r)}),k(w,"--");break;case"scrollTo":f(!1);break;case"scrollToOffset":f(!0);break;case"pageInfo":d(F[w]&&F[w].iframe,w),function(){function e(n,i){function t(){F[r]?d(F[r].iframe,r):o()}["scroll","resize"].forEach(function(e){k(r,n+e+" listener for sendPageInfo"),i(window,e,t)})}function o(){e("Remove ",x)}var r=w;e("Add ",M),F[r]&&(F[r].stopPageInfo=o)}();break;case"pageInfoStop":F[w]&&F[w].stopPageInfo&&(F[w].stopPageInfo(),delete F[w].stopPageInfo);break;case"inPageLink":e=s(9),i=e.split("#")[1]||"",t=decodeURIComponent(i),(o=document.getElementById(t)||document.getElementsByName(t)[0])?(n=c(o),k(w,"Moving to in page link (#"+i+") at x: "+n.x+" y: "+n.y),v={x:n.x,y:n.y},u(),k(w,"--")):window.top!==window.self?window.parentIFrame?window.parentIFrame.moveToAnchor(i):k(w,"In page link #"+i+" not found and window.parentIFrame not found"):k(w,"In page link #"+i+" not found");break;case"reset":W(h);break;case"init":a(),l("onInit",h.iframe);break;default:a()}var e,n,i,t,o,r}())):z(w,"Ignored: "+g)}function R(e,n,i){var t=null,o=null;if(F[e]){if("function"!=typeof(t=F[e][n]))throw new TypeError(n+" on iFrame["+e+"] is not a function");o=t(i)}return o}function T(e){var n=e.id;delete F[n]}function E(e){var n=e.id;k(n,"Removing iFrame: "+n);try{e.parentNode&&e.parentNode.removeChild(e)}catch(e){O(e)}R(n,"onClosed",n),k(n,"--"),T(e)}function N(e){null===v&&k(e,"Get page position: "+(v={x:window.pageXOffset!==u?window.pageXOffset:document.documentElement.scrollLeft,y:window.pageYOffset!==u?window.pageYOffset:document.documentElement.scrollTop}).x+","+v.y)}function C(e){null!==v&&(window.scrollTo(v.x,v.y),k(e,"Set page position: "+v.x+","+v.y),S())}function S(){v=null}function W(e){k(e.id,"Size reset requested by "+("init"===e.type?"host page":"iFrame")),N(e.id),P(function(){j(e),H("reset","reset",e.iframe,e.id)},e,"reset")}function j(i){function t(e){n||"0"!==i[e]||(n=!0,k(o,"Hidden iFrame detected, creating visibility listener"),function(){function n(){Object.values(F).forEach(function(n){function e(e){return"0px"===(F[n]&&F[n].iframe.style[e])}F[n]&&(i=F[n].iframe,null!==i.offsetParent)&&(e("height")||e("width"))&&H("Visibility change","resize",F[n].iframe,n);var i})}function e(e){k("window","Mutation observed: "+e[0].target+" "+e[0].type),c(n,16)}var i=w();i&&(t=document.querySelector("body"),o={attributes:!0,attributeOldValue:!1,characterData:!0,characterDataOldValue:!1,childList:!0,subtree:!0},new i(e).observe(t,o));var t,o}())}function e(e){var n;n=e,i.id?(i.iframe.style[n]=i[n]+"px",k(i.id,"IFrame ("+o+") "+n+" set to "+i[n]+"px")):k("undefined","messageData id not set"),t(e)}var o=i.iframe.id;F[o]&&(F[o].sizeHeight&&e("height"),F[o].sizeWidth&&e("width"))}function P(e,n,i){i!==n.type&&r?(k(n.id,"Requesting animation frame"),r(e)):e()}function H(e,n,i,t,o){var r,a=!1;t=t||i.id,F[t]&&(i&&"contentWindow"in i&&null!==i.contentWindow?(r=F[t]&&F[t].targetOrigin,k(t,"["+e+"] Sending msg to iframe["+t+"] ("+n+") targetOrigin: "+r),i.contentWindow.postMessage(b+n,r)):O(t,"["+e+"] IFrame("+t+") not found"),o&&F[t]&&F[t].warningTimeout&&(F[t].msgTimeout=setTimeout(function(){!F[t]||F[t].loaded||a||(a=!0,O(t,"IFrame has not responded within "+F[t].warningTimeout/1e3+" seconds. Check CryptoGateResizer.contentWindow.js has been loaded in iFrame. This message can be ignored if everything is working, or you can set the warningTimeout option to a higher value or zero to suppress this warning."))},F[t].warningTimeout)))}function A(e){return e+":"+F[e].bodyMarginV1+":"+F[e].sizeWidth+":"+F[e].log+":"+F[e].interval+":"+F[e].enablePublicMethods+":"+F[e].autoResize+":"+F[e].bodyMargin+":"+F[e].heightCalculationMethod+":"+F[e].bodyBackground+":"+F[e].bodyPadding+":"+F[e].tolerance+":"+F[e].inPageLinks+":"+F[e].resizeFrom+":"+F[e].widthCalculationMethod}function d(i,e){function n(e){var n=e.split("Callback");if(2===n.length){var i="on"+n[0].charAt(0).toUpperCase()+n[0].slice(1);this[i]=this[e],delete this[e],O(f,"Deprecated: '"+e+"' has been renamed '"+i+"'. The old method will be removed in the next major version.")}}var t,o,r,a,s,d,c,f=(""===(t=i.id)&&(i.id=(o=e&&e.id||h.id+l++,null!==document.getElementById(o)&&(o+=l++),t=o),m=(e||{}).log,k(t,"Added missing iframe ID: "+t+" ("+i.src+")")),t);f in F&&"CryptoGateResizer"in i?O(f,"Ignored iFrame, already setup."):(d=(d=e)||{},F[f]={firstRun:!0,iframe:i,remoteHost:i.src.split("/").slice(0,3).join("/")},function(e){if("object"!=typeof e)throw new TypeError("Options is not an object")}(d),Object.keys(d).forEach(n,d),function(e){for(var n in h)Object.prototype.hasOwnProperty.call(h,n)&&(F[f][n]=Object.prototype.hasOwnProperty.call(e,n)?e[n]:h[n])}(d),F[f]&&(F[f].targetOrigin=!0===F[f].checkOrigin?""===(c=F[f].remoteHost)||"file://"===c?"*":c:"*"),function(){switch(k(f,"IFrame scrolling "+(F[f]&&F[f].scrolling?"enabled":"disabled")+" for "+f),i.style.overflow=!1===(F[f]&&F[f].scrolling)?"hidden":"auto",F[f]&&F[f].scrolling){case"omit":break;case!0:i.scrolling="yes";break;case!1:i.scrolling="no";break;default:i.scrolling=F[f]?F[f].scrolling:"no"}}(),function(){function e(e){1/0!==F[f][e]&&0!==F[f][e]&&(i.style[e]=F[f][e]+"px",k(f,"Set "+e+" = "+F[f][e]+"px"))}function n(e){if(F[f]["min"+e]>F[f]["max"+e])throw new Error("Value for min"+e+" can not be greater than max"+e)}n("Height"),n("Width"),e("maxHeight"),e("minHeight"),e("maxWidth"),e("minWidth")}(),"number"!=typeof(F[f]&&F[f].bodyMargin)&&"0"!==(F[f]&&F[f].bodyMargin)||(F[f].bodyMarginV1=F[f].bodyMargin,F[f].bodyMargin=F[f].bodyMargin+"px"),r=A(f),(s=w())&&(a=s,i.parentNode&&new a(function(e){e.forEach(function(e){Array.prototype.slice.call(e.removedNodes).forEach(function(e){e===i&&E(i)})})}).observe(i.parentNode,{childList:!0})),M(i,"load",function(){var e,n;H("iFrame.onload",r,i,u,!0),e=F[f]&&F[f].firstRun,n=F[f]&&F[f].heightCalculationMethod in g,!e&&n&&W({iframe:i,height:0,width:0,type:"init"})}),H("init",r,i,u,!0),F[f]&&(F[f].iframe.CryptoGateResizer={close:E.bind(null,F[f].iframe),removeListeners:T.bind(null,F[f].iframe),resize:H.bind(null,"Window resize","resize",F[f].iframe),moveToAnchor:function(e){H("Move to anchor","moveToAnchor:"+e,F[f].iframe,f)},sendMessage:function(e){H("Send Message","message:"+(e=JSON.stringify(e)),F[f].iframe,f)}}))}function c(e,n){null===i&&(i=setTimeout(function(){i=null,e()},n))}function f(){"hidden"!==document.visibilityState&&(k("document","Trigger event: Visiblity change"),c(function(){q("Tab Visable","resize")},16))}function q(i,t){Object.keys(F).forEach(function(e){var n;F[n=e]&&"parent"===F[n].resizeFrom&&F[n].autoResize&&!F[n].firstRun&&H(i,t,document.getElementById(e),e)})}function L(){M(window,"message",s),M(window,"resize",function(){var e;k("window","Trigger event: "+(e="resize")),c(function(){q("Window "+e,"resize")},16)}),M(document,"visibilitychange",f),M(document,"-webkit-visibilitychange",f)}function B(){function t(e,n){n&&(!function(){if(!n.tagName)throw new TypeError("Object is not a valid DOM element");if("IFRAME"!==n.tagName.toUpperCase())throw new TypeError("Expected <IFRAME> tag, found <"+n.tagName+">")}(),d(n,e),o.push(n))}var o;return function(){var e,n=["moz","webkit","o","ms"];for(e=0;e<n.length&&!r;e+=1)r=window[n[e]+"RequestAnimationFrame"];r||k("setup","RequestAnimationFrame not supported")}(),L(),function(e,n){var i;switch(o=[],(i=e)&&i.enablePublicMethods&&O("enablePublicMethods option has been removed, public methods are now always available in the iFrame"),typeof n){case"undefined":case"string":Array.prototype.forEach.call(document.querySelectorAll(n||"iframe"),t.bind(u,e));break;case"object":t(e,n);break;default:throw new TypeError("Unexpected data type ("+typeof n+")")}return o}}}();



(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(factory)
    } else if (typeof exports === 'object') {
        module.exports = factory()
    } else {
        root.cryptogatemodal = factory()
    }
}(this, function() {

    /* ----------------------------------------------------------- */
    /* == modal */
    /* ----------------------------------------------------------- */

    var isBusy = false

    function Modal(options) {
        var defaults = {
            onClose: null,
            onOpen: null,
            beforeOpen: null,
            beforeClose: null,
            stickyFooter: false,
            footer: false,
            cssClass: [],
            closeLabel: 'Close',
            closeMethods: ['overlay', 'button', 'escape']
        }

        // extends config
        this.opts = extend({}, defaults, options)

        // init modal
        this.init()
    }

    Modal.prototype.init = function() {
        if (this.modal) {
            return
        }

        _build.call(this)
        _bindEvents.call(this)

        // insert modal in dom
        document.body.insertBefore(this.modal, document.body.firstChild)

        if (this.opts.footer) {
            this.addFooter()
        }

        return this
    }

    Modal.prototype._busy = function(state) {
        isBusy = state
    }

    Modal.prototype._isBusy = function() {
        return isBusy
    }

    Modal.prototype.destroy = function() {
        if (this.modal === null) {
            return
        }

        // restore scrolling
        if (this.isOpen()) {
            this.close(true)
        }

        // unbind all events
        _unbindEvents.call(this)

        // remove modal from dom
        this.modal.parentNode.removeChild(this.modal)

        this.modal = null
    }

    Modal.prototype.isOpen = function() {
        return !!this.modal.classList.contains('cryptogatemodal-modal--visible')
    }

    Modal.prototype.open = function() {
        if(this._isBusy()) return
        this._busy(true)

        var self = this

        // before open callback
        if (typeof self.opts.beforeOpen === 'function') {
            self.opts.beforeOpen()
        }

        if (this.modal.style.removeProperty) {
            this.modal.style.removeProperty('display')
        } else {
            this.modal.style.removeAttribute('display')
        }

        // prevent double scroll
        this._scrollPosition = window.pageYOffset
        document.body.classList.add('cryptogatemodal-enabled')
        document.body.style.top = -this._scrollPosition + 'px'

        // sticky footer
        this.setStickyFooter(this.opts.stickyFooter)

        // show modal
        this.modal.classList.add('cryptogatemodal-modal--visible')

        // onOpen callback
        if (typeof self.opts.onOpen === 'function') {
            self.opts.onOpen.call(self)
        }

        self._busy(false)

        // check if modal is bigger than screen height
        this.checkOverflow()

        return this
    }

    Modal.prototype.close = function(force) {
        if(this._isBusy()) return
        this._busy(true)
        force = force || false

        //  before close
        if (typeof this.opts.beforeClose === 'function') {
            var close = this.opts.beforeClose.call(this)
            if (!close) {
                this._busy(false)
                return
            }
        }

        document.body.classList.remove('cryptogatemodal-enabled')
        window.scrollTo(0, this._scrollPosition)
        document.body.style.top = null

        this.modal.classList.remove('cryptogatemodal-modal--visible')

        // using similar setup as onOpen
        var self = this

        self.modal.style.display = 'none'

        // onClose callback
        if (typeof self.opts.onClose === 'function') {
            self.opts.onClose.call(this)
        }

        // release modal
        self._busy(false)

    }

    Modal.prototype.setContent = function(content) {
        // check type of content : String or Node
        if (typeof content === 'string') {
            this.modalBoxContent.innerHTML = content
        } else {
            this.modalBoxContent.innerHTML = ''
            this.modalBoxContent.appendChild(content)
        }

        if (this.isOpen()) {
            // check if modal is bigger than screen height
            this.checkOverflow()
        }

        return this
    }

    Modal.prototype.getContent = function() {
        return this.modalBoxContent
    }

    Modal.prototype.addFooter = function() {
        // add footer to modal
        _buildFooter.call(this)

        return this
    }

    Modal.prototype.setFooterContent = function(content) {
        // set footer content
        this.modalBoxFooter.innerHTML = content

        return this
    }

    Modal.prototype.getFooterContent = function() {
        return this.modalBoxFooter
    }

    Modal.prototype.setStickyFooter = function(isSticky) {
        // if the modal is smaller than the viewport height, we don't need sticky
        if (!this.isOverflow()) {
            isSticky = false
        }

        if (isSticky) {
            if (this.modalBox.contains(this.modalBoxFooter)) {
                this.modalBox.removeChild(this.modalBoxFooter)
                this.modal.appendChild(this.modalBoxFooter)
                this.modalBoxFooter.classList.add('cryptogatemodal-modal-box__footer--sticky')
                _recalculateFooterPosition.call(this)
                this.modalBoxContent.style['padding-bottom'] = this.modalBoxFooter.clientHeight + 20 + 'px'
            }
        } else if (this.modalBoxFooter) {
            if (!this.modalBox.contains(this.modalBoxFooter)) {
                this.modal.removeChild(this.modalBoxFooter)
                this.modalBox.appendChild(this.modalBoxFooter)
                this.modalBoxFooter.style.width = 'auto'
                this.modalBoxFooter.style.left = ''
                this.modalBoxContent.style['padding-bottom'] = ''
                this.modalBoxFooter.classList.remove('cryptogatemodal-modal-box__footer--sticky')
            }
        }

        return this
    }


    Modal.prototype.addFooterBtn = function(label, cssClass, callback) {
        var btn = document.createElement('button')

        // set label
        btn.innerHTML = label

        // bind callback
        btn.addEventListener('click', callback)

        if (typeof cssClass === 'string' && cssClass.length) {
            // add classes to btn
            cssClass.split(' ').forEach(function(item) {
                btn.classList.add(item)
            })
        }

        this.modalBoxFooter.appendChild(btn)

        return btn
    }

    Modal.prototype.resize = function() {
        // eslint-disable-next-line no-console
        console.warn('Resize is deprecated and will be removed in version 1.0')
    }

    Modal.prototype.isOverflow = function() {
        var viewportHeight = window.innerHeight
        var modalHeight = this.modalBox.clientHeight

        return modalHeight >= viewportHeight
    }

    Modal.prototype.checkOverflow = function() {
        // only if the modal is currently shown
        if (this.modal.classList.contains('cryptogatemodal-modal--visible')) {
            if (this.isOverflow()) {
                this.modal.classList.add('cryptogatemodal-modal--overflow')
            } else {
                this.modal.classList.remove('cryptogatemodal-modal--overflow')
            }

            // tODO: remove offset
            // _offset.call(this);
            if (!this.isOverflow() && this.opts.stickyFooter) {
                this.setStickyFooter(false)
            } else if (this.isOverflow() && this.opts.stickyFooter) {
                _recalculateFooterPosition.call(this)
                this.setStickyFooter(true)
            }
        }
    }


    /* ----------------------------------------------------------- */
    /* == private methods */
    /* ----------------------------------------------------------- */

    function closeIcon() {
        return '<svg viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg"><path d="M.3 9.7c.2.2.4.3.7.3.3 0 .5-.1.7-.3L5 6.4l3.3 3.3c.2.2.5.3.7.3.2 0 .5-.1.7-.3.4-.4.4-1 0-1.4L6.4 5l3.3-3.3c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0L5 3.6 1.7.3C1.3-.1.7-.1.3.3c-.4.4-.4 1 0 1.4L3.6 5 .3 8.3c-.4.4-.4 1 0 1.4z" fill="#000" fill-rule="nonzero"/></svg>'
    }

    function _recalculateFooterPosition() {
        if (!this.modalBoxFooter) {
            return
        }
        this.modalBoxFooter.style.width = this.modalBox.clientWidth + 'px'
        this.modalBoxFooter.style.left = this.modalBox.offsetLeft + 'px'
    }

    function _build() {

        // wrapper
        this.modal = document.createElement('div')
        this.modal.classList.add('cryptogatemodal-modal')

        // remove cusor if no overlay close method
        if (this.opts.closeMethods.length === 0 || this.opts.closeMethods.indexOf('overlay') === -1) {
            this.modal.classList.add('cryptogatemodal-modal--noOverlayClose')
        }

        this.modal.style.display = 'none'

        // custom class
        this.opts.cssClass.forEach(function(item) {
            if (typeof item === 'string') {
                this.modal.classList.add(item)
            }
        }, this)

        // close btn
        if (this.opts.closeMethods.indexOf('button') !== -1) {
            this.modalCloseBtn = document.createElement('button')
            this.modalCloseBtn.type = 'button'
            this.modalCloseBtn.classList.add('cryptogatemodal-modal__close')

            this.modalCloseBtnIcon = document.createElement('span')
            this.modalCloseBtnIcon.classList.add('cryptogatemodal-modal__closeIcon')
            this.modalCloseBtnIcon.innerHTML = closeIcon()

            this.modalCloseBtnLabel = document.createElement('span')
            this.modalCloseBtnLabel.classList.add('cryptogatemodal-modal__closeLabel')
            this.modalCloseBtnLabel.innerHTML = this.opts.closeLabel

            this.modalCloseBtn.appendChild(this.modalCloseBtnIcon)
            this.modalCloseBtn.appendChild(this.modalCloseBtnLabel)
        }

        // modal
        this.modalBox = document.createElement('div')
        this.modalBox.classList.add('cryptogatemodal-modal-box')

        // modal box content
        this.modalBoxContent = document.createElement('div')
        this.modalBoxContent.classList.add('cryptogatemodal-modal-box__content')

        this.modalBox.appendChild(this.modalBoxContent)

        if (this.opts.closeMethods.indexOf('button') !== -1) {
            this.modal.appendChild(this.modalCloseBtn)
        }

        this.modal.appendChild(this.modalBox)

    }

    function _buildFooter() {
        this.modalBoxFooter = document.createElement('div')
        this.modalBoxFooter.classList.add('cryptogatemodal-modal-box__footer')
        this.modalBox.appendChild(this.modalBoxFooter)
    }

    function _bindEvents() {

        this._events = {
            clickCloseBtn: this.close.bind(this),
            //clickOverlay: _handleClickOutside.bind(this),
            resize: this.checkOverflow.bind(this),
            //keyboardNav: _handleKeyboardNav.bind(this)
        }

        if (this.opts.closeMethods.indexOf('button') !== -1) {
            this.modalCloseBtn.addEventListener('click', this._events.clickCloseBtn)
        }

        this.modal.addEventListener('mousedown', this._events.clickOverlay)
        window.addEventListener('resize', this._events.resize)
        document.addEventListener('keydown', this._events.keyboardNav)
    }

    function _handleKeyboardNav(event) {
        // escape key
        if (this.opts.closeMethods.indexOf('escape') !== -1 && event.which === 27 && this.isOpen()) {
            this.close()
        }
    }

    function _handleClickOutside(event) {
        // if click is outside the modal
        if (this.opts.closeMethods.indexOf('overlay') !== -1 && !_findAncestor(event.target, 'cryptogatemodal-modal') &&
            event.clientX < this.modal.clientWidth) {
            this.close()
        }
    }

    function _findAncestor(el, cls) {
        while ((el = el.parentElement) && !el.classList.contains(cls));
        return el
    }

    function _unbindEvents() {
        if (this.opts.closeMethods.indexOf('button') !== -1) {
            this.modalCloseBtn.removeEventListener('click', this._events.clickCloseBtn)
        }
        this.modal.removeEventListener('mousedown', this._events.clickOverlay)
        window.removeEventListener('resize', this._events.resize)
        document.removeEventListener('keydown', this._events.keyboardNav)
    }

    /* ----------------------------------------------------------- */
    /* == helpers */
    /* ----------------------------------------------------------- */

    function extend() {
        for (var i = 1; i < arguments.length; i++) {
            for (var key in arguments[i]) {
                if (arguments[i].hasOwnProperty(key)) {
                    arguments[0][key] = arguments[i][key]
                }
            }
        }
        return arguments[0]
    }

    /* ----------------------------------------------------------- */
    /* == return */
    /* ----------------------------------------------------------- */

    return {
        modal: Modal
    }

}))

function CryptoGateModal(url) {

    var modal_css = '.cryptogatemodal-modal--visible{overflow: scroll !important;}.cryptogatemodal-modal *{box-sizing:border-box}.cryptogatemodal-modal{position:fixed;top:0;right:0;bottom:0;left:0;z-index:100000;display:-ms-flexbox;display:flex;visibility:hidden;-ms-flex-direction:column;flex-direction:column;-ms-flex-align:center;align-items:center;overflow:hidden;-webkit-overflow-scrolling:touch;background:rgba(255,255,255,.85);opacity:0;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;cursor:pointer}.cryptogatemodal-modal--noClose .cryptogatemodal-modal__close,.cryptogatemodal-modal__closeLabel{display:none}.cryptogatemodal-modal--confirm .cryptogatemodal-modal-box{text-align:center}.cryptogatemodal-modal--noOverlayClose{cursor:default}.cryptogatemodal-modal__close{position:fixed !important;top:2rem !important;right:2rem !important;z-index:10000 !important;padding:0 !important;width:2rem !important;height:2rem !important;border:none !important;background-color:transparent !important;color:#000 !important;cursor:pointer!important}.cryptogatemodal-modal__close svg *{fill:currentColor}.cryptogatemodal-modal__close:hover{color:grey}.cryptogatemodal-modal-box{position:relative;-ms-flex-negative:0;flex-shrink:0;margin-top:auto;margin-bottom:auto;border-radius:4px;background:transparent;opacity:1;cursor:auto;will-change:transform,opacity}.cryptogatemodal-modal-box__footer{padding:1.5rem 2rem;width:auto;border-bottom-right-radius:4px;border-bottom-left-radius:4px;background-color:#f5f5f5;cursor:auto}.cryptogatemodal-modal-box__footer::after{display:table;clear:both;content:""}.cryptogatemodal-modal-box__footer--sticky{position:fixed;bottom:-200px;z-index:100001;opacity:1;transition:bottom .3s ease-in-out .3s}.cryptogatemodal-enabled{position:fixed;right:0;left:0;overflow:hidden}.cryptogatemodal-modal--visible .cryptogatemodal-modal-box__footer{bottom:0}.cryptogatemodal-enabled .cryptogatemodal-content-wrapper{filter:blur(8px)}.cryptogatemodal-modal--visible{visibility:visible;opacity:1}.cryptogatemodal-modal--visible .cryptogatemodal-modal-box{animation:scale .2s cubic-bezier(.68,-.55,.265,1.55) forwards}.cryptogatemodal-modal--overflow{overflow-y:scroll;padding-top:8vh}.cryptogatemodal-btn{display:inline-block;margin:0 .5rem;padding:1rem 2rem;border:none;background-color:grey;box-shadow:none;color:#fff;vertical-align:middle;text-decoration:none;font-size:inherit;font-family:inherit;line-height:normal;cursor:pointer;transition:background-color .4s ease}.cryptogatemodal-btn--primary{background-color:#3498db}.cryptogatemodal-btn--danger{background-color:#e74c3c}.cryptogatemodal-btn--default{background-color:#34495e}.cryptogatemodal-btn--pull-left{float:left}.cryptogatemodal-btn--pull-right{float:right}@media (max-width :540px){.cryptogatemodal-modal{top:0;display:block;padding-top:60px;width:100%}.cryptogatemodal-modal-box{width:auto;border-radius:0}.cryptogatemodal-modal-box__content{overflow-y:scroll}.cryptogatemodal-modal--noClose{top:0}.cryptogatemodal-modal--noOverlayClose{padding-top:0}.cryptogatemodal-modal-box__footer .cryptogatemodal-btn{display:block;float:none;margin-bottom:1rem;width:100%}.cryptogatemodal-modal__close{top:0;right:0;left:0;display:block;width:100%;height:60px;border:none;background-color:#2c3e50;box-shadow:none;color:#fff}.cryptogatemodal-modal__closeLabel{display:inline-block;vertical-align:middle;font-size:1.6rem;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Helvetica Neue",sans-serif}.cryptogatemodal-modal__closeIcon{display:inline-block;margin-right:.8rem;width:1.6rem;vertical-align:middle;font-size:0}}@supports ((-webkit-backdrop-filter:blur(12px)) or (backdrop-filter:blur(12px))){.cryptogatemodal-modal{-webkit-backdrop-filter:blur(20px);backdrop-filter:blur(20px)}@media (max-width :540px){.cryptogatemodal-modal{-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px)}}.cryptogatemodal-enabled .cryptogatemodal-content-wrapper{filter:none}}@keyframes scale{0%{opacity:0;transform:scale(.9)}100%{opacity:1;transform:scale(1)}}';
    var css = document.createElement('style');

    if (css.styleSheet) css.styleSheet.cssText = modal_css;
    else css.appendChild(document.createTextNode(modal_css));

    document.getElementsByTagName("head")[0].appendChild(css);

    var modal = new cryptogatemodal.modal({
        closeMethods: ['overlay', 'button', 'escape'],
        closeLabel: "Close",
        cssClass: ['custom-class-1', 'custom-class-2'],
        onOpen: function() {
            console.log('modal open');
        },
        onClose: function() {
            console.log('modal closed');
        },
        beforeClose: function() {
            // here's goes some logic
            // e.g. save content before closing the modal
            return true; // close the modal
            return false; // nothing happens
        }
    });

    modal.setContent('<iframe id="cryptomodal-iframe" style="width: 430px; min-width: 100%; transform: scale(0.85); transform-origin: top;" src="'+url+'" frameborder="0" scrolling="yes"></iframe>');
    modal.open();
    CryptoGateResize({ log: false, checkOrigin: false, heightCalculationMethod: 'lowestElement'}, '#cryptomodal-iframe')
}
