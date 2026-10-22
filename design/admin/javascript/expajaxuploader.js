/*!
 * The admin's "Upload a file" into an object relation, on Exponential UI in plain JavaScript (no jQuery needed):
 * Exp.dialog for the modal window, Exp.upload.create() for the file, Exp.io for the other calls: step by step, with
 * the server calls, the POSTed fields in their order and the configuration the templates give,
 * as follows:
 *
 *   expAjaxUploader(document.querySelectorAll('.simple-relation-upload-new'), {
 *       open:     { action: 'ezajaxuploader::uploadform::ezobjectrelation' },
 *       upload:   { action: 'ezajaxuploader::upload::ezobjectrelation?ContentType=html', form: 'form.ajaxuploader-upload' },
 *       location: { action: 'ezajaxuploader::preview::ezobjectrelation', form: 'form.ajaxuploader-location',
 *                   browse: 'div.ajaxuploader-browse', required: 'Please choose a location' },
 *       preview:  { form: 'form.ajaxuploader-preview', callback: function () { this.lastMetaData; this.close(); } },
 *       title, validationErrorText, parseJSONErrorText
 *   });
 *
 * The buttons: an element, a selector, a NodeList or an array. Where the page has jQuery (Exp.$),
 * $(buttons).expAjaxUploader(conf) does the same (the templates of the older admin design call it).
 * Each button's name (RelationUploadNew<attribute id>-<version>) gives the target posted with every call
 * (AjaxUploadHandlerData[ObjectRelationsAttributeId], AjaxUploadHandlerData[Version]); the page's form token goes
 * with them. A click on the button opens the dialog; the button is shown (its "hide" class removed) once it works.
 * Instance: expAjaxUploader.get(button) (with jQuery also $(button).data('expAjaxUploader')): open(), close(),
 * dialog (Exp.dialog's Dialog), lastMetaData, conf.
 * GNU General Public License v2.0 (or any later version).
 */
(function (window, document) {
    'use strict';

    var Exp = window.Exp;
    if (!Exp || !Exp.util || !Exp.dialog || !Exp.io || !Exp.upload || !Exp.upload.create) {
        if (window.console) { window.console.error('expAjaxUploader: Exponential UI (exp::dialog, exp::io, exp::upload) is not on the page'); }
        return;
    }
    var extend = Exp.util.extend;

    var HANDLER_FIELD_NAME = 'AjaxUploadHandlerData';
    var HAD_DEFAULT_VALUE = 'had-default-value';

    var DEFAULTS = {
        requiredInput: 'input.input-required',
        labelErrorClass: 'message-error',
        validationErrorText: 'Some required fields are empty.',
        parseJSONErrorText: 'Unable to parse the JSON response.',
        validationErrorTextElement: '.ajaxuploader-error',
        errorTemplate: '<div class="message-error">%message</div>',
        defaultValuedInputClass: 'has-default-value',
        closeSelector: '.window-cancel',
        width: 650,
        target: {}
    };

    // the instance of each button
    var instances = new WeakMap();

    /** 'class::function::arg::arg' as Exp.io.call()'s name and arguments. */
    function split(action) {
        var parts = String(action).split('::');
        return { fn: parts[0] + '::' + parts[1], args: parts.slice(2) };
    }

    function each(list, fn) { Array.prototype.forEach.call(list, fn); }
    function first(root, selector) { return root ? root.querySelector(selector) : null; }
    function all(root, selector) { return root ? root.querySelectorAll(selector) : []; }
    function setText(el, text) { if (el) { el.textContent = text; } }
    // as jQuery's .removeClass(): an element without the class keeps its class attribute as it is
    function removeClass(el, name) { if (el.classList.contains(name)) { el.classList.remove(name); } }

    /** The value of a field as jQuery's .val() reads it: a select's selected options, null for none. */
    function fieldValue(el) {
        if (el.nodeName.toLowerCase() !== 'select') { return el.value; }
        var chosen = Array.prototype.filter.call(el.options, function (o) {
            return o.selected && !o.disabled && !(o.parentNode.disabled && o.parentNode.nodeName.toLowerCase() === 'optgroup');
        }).map(function (o) { return o.value; });
        if (el.multiple) { return chosen; }
        return chosen.length ? chosen[0] : null;
    }

    /** A form's fields as jQuery's .serializeArray() gives them: [{name, value}] in the form's order. */
    function serializeArray(form) {
        var out = [];
        each(form ? form.elements : [], function (el) {
            var type = el.type;
            if (!el.name || el.matches(':disabled') || !/^(?:input|select|textarea|keygen)/i.test(el.nodeName) ||
                    /^(?:submit|button|image|reset|file)$/i.test(type) || (!el.checked && /^(?:checkbox|radio)$/i.test(type))) {
                return;
            }
            var value = fieldValue(el);
            if (value === null || value === undefined) { return; }
            (Array.isArray(value) ? value : [value]).forEach(function (v) {
                out.push({ name: el.name, value: String(v).replace(/\r?\n/g, '\r\n') });
            });
        });
        return out;
    }

    /**
     * Handlers delegated on root as jQuery delegates them: a handler runs for each element from the event's target up
     * to root that matches its selector, the deepest first, in the order they were added; stop(e) ends the handlers
     * of the elements further up, stopNow(e) every handler after it. A click of another than the main button, and
     * one on a disabled element, reaches none.
     */
    function Delegates(root) {
        this.root = root;
        this.handlers = {};
        this.listeners = {};
        this.flags = null;
    }
    Delegates.prototype.on = function (type, selector, fn) {
        var self = this;
        if (!this.handlers[type]) {
            this.handlers[type] = [];
            this.listeners[type] = function (e) { self.dispatch(type, e); };
            this.root.addEventListener(type, this.listeners[type]);
        }
        this.handlers[type].push({ selector: selector, fn: fn });
    };
    Delegates.prototype.dispatch = function (type, e) {
        var root = this.root, handlers = this.handlers[type] || [], matched = [];
        if (type === 'click' && e.button >= 1) { return; }
        var collect = function (cur) {
            handlers.forEach(function (h) { if (cur.matches(h.selector)) { matched.push({ el: cur, fn: h.fn }); } });
        };
        for (var cur = e.target; cur && cur !== root; cur = cur.parentNode) {
            if (cur.nodeType === 1 && !(type === 'click' && cur.disabled === true)) { collect(cur); }
        }
        var previous = this.flags, flags = this.flags = { stopped: false, now: false, el: null };
        try {
            for (var i = 0; i < matched.length && !flags.now; i++) {
                if (flags.stopped && matched[i].el !== flags.el) { break; }
                matched[i].fn.call(matched[i].el, e);
                if (flags.stopped && !flags.el) { flags.el = matched[i].el; }
            }
        } finally {
            this.flags = previous;
        }
    };
    Delegates.prototype.stop = function (e) { if (this.flags) { this.flags.stopped = true; } e.stopPropagation(); };
    Delegates.prototype.stopNow = function (e) { if (this.flags) { this.flags.stopped = this.flags.now = true; } e.stopImmediatePropagation(); };
    Delegates.prototype.off = function () {
        var root = this.root, listeners = this.listeners;
        Object.keys(listeners).forEach(function (type) { root.removeEventListener(type, listeners[type]); });
        this.handlers = {};
        this.listeners = {};
    };

    function AjaxUploader(conf) {
        this.conf = extend(true, {}, DEFAULTS, conf);
        this.lastMetaData = false;
        this.dialog = null;
        this.upload = null;
        this.delegates = null;
        var self = this;
        // what the callbacks get as this.modalWindow
        this.modalWindow = {
            close: function () { self.close(); },
            setContent: function (html) { self.setContent(html); },
            getContentNode: function () { return self.content(); }
        };
    }

    /** The dialog's content element (null while it is closed). */
    AjaxUploader.prototype.content = function () { return this.dialog ? this.dialog.body : null; };

    AjaxUploader.prototype.setContent = function (html) {
        if (this.upload) { this.upload.destroy(); this.upload = null; }
        this.dialog.setContent(html);
        this.bindUpload();
    };

    /** The error, in the error template, as the whole content (as before). */
    AjaxUploader.prototype.displayError = function (text) {
        var box = document.createElement('div'), escape = document.createElement('div');
        box.innerHTML = this.conf.errorTemplate;
        escape.textContent = String(text);
        var nodes = Array.prototype.slice.call(box.childNodes);
        var html = box.firstChild.innerHTML.replace('%message', escape.innerHTML);
        nodes.forEach(function (n) { if (n.nodeType === 1) { n.innerHTML = html; } });
        this.setContent(nodes);
    };

    AjaxUploader.prototype.waitAjax = function () { if (this.dialog) { this.dialog.busy(true); } };
    AjaxUploader.prototype.endAjax = function () { if (this.dialog) { this.dialog.busy(false); } };

    /** The target as POST fields: AjaxUploadHandlerData[<key>] = <value>. */
    AjaxUploader.prototype.handlerData = function () {
        var target = this.conf.target;
        return Object.keys(target).map(function (k) { return { name: HANDLER_FIELD_NAME + '[' + k + ']', value: target[k] }; });
    };

    /** An ezjscore call whose content is {meta_data, html}: shows the html (or into the element into, when given), keeps the meta data. */
    AjaxUploader.prototype.call = function (action, opts, into) {
        var self = this, a = split(action), dialog = this.dialog, intoGiven = arguments.length > 2;
        // an answer for a dialog closed (or closed and opened again) meanwhile is dropped
        var current = function () { return self.dialog === dialog && dialog && dialog.isOpen; };
        this.waitAjax();
        return Exp.io.call(a.fn, a.args, opts).then(function (content) {
            if (!current()) { return; }
            self.endAjax();
            // an empty answer (or one without its html) is an error, not a step: shown, not thrown
            if (!content || typeof content.html !== 'string') {
                self.lastMetaData = false;
                self.displayError(self.conf.parseJSONErrorText);
                return;
            }
            self.lastMetaData = content.meta_data;
            if (intoGiven) { into.innerHTML = content.html; } else { self.setContent(content.html); }
            return content;
        }, function (error) {
            if (!current()) { return; }
            self.endAjax();
            self.displayError(error && error.message ? error.message : self.conf.parseJSONErrorText);
        });
    };

    /** The step's buttons usable again (after an upload was canceled). */
    AjaxUploader.prototype.enableButtons = function () {
        each(all(this.content(), 'form input[type="submit"]'), function (b) {
            b.disabled = false;
            removeClass(b, 'button-disabled');
            b.classList.add('button');
        });
    };

    /** The step 1 form's file field becomes an Exp.upload: progress while it is sent, cancel, the same fields. */
    AjaxUploader.prototype.bindUpload = function () {
        var self = this, form = first(this.content(), this.conf.upload.form), dialog = this.dialog;
        var file = first(form, 'input[type="file"]');
        if (!file) { return; }
        var current = function () { return self.dialog === dialog && dialog && dialog.isOpen; };
        // not dimmed while the file is sent (not the whole step): its progress and Cancel stay usable
        this.upload = Exp.upload.create(file, {
            url: Exp.config.call + this.conf.upload.action,
            name: file.getAttribute('name'),
            form: form,
            auto: false,
            multiple: false,
            token: false,               // the form carries it
            responseType: 'text',
            onDone: function (text) {
                if (!current()) { return; }
                var json;
                // ContentType=html: the call view sends the JSON as HTML text (quotes as &quot;); it is read
                // it from an iframe, where the browser had decoded it. The same here, with an inert parser.
                try { json = JSON.parse(new window.DOMParser().parseFromString(String(text), 'text/html').body.textContent); } catch (e) {
                    self.displayError(self.conf.parseJSONErrorText);
                    return;
                }
                if (json.error_text) {
                    self.displayError(json.error_text);
                    return;
                }
                // the server URL-encodes the HTML of this answer (see ezjscServerFunctionsAjaxUploader::upload());
                // a malformed sequence (URIError) is an answer that is not the one expected
                var html;
                if (typeof json.html !== 'string') {
                    self.displayError(self.conf.parseJSONErrorText);
                    return;
                }
                try { html = decodeURIComponent(json.html); } catch (e) {
                    self.displayError(self.conf.parseJSONErrorText);
                    return;
                }
                self.setContent(html);
            },
            onFail: function (error) {
                if (!current()) { return; }
                // an answer that is not the JSON expected says so as before; no answer, or signed out, says that
                self.displayError(error && (error.kind === 'signedout' || error.kind === 'timeout' || error.kind === 'network') ? error.message : self.conf.parseJSONErrorText);
            },
            onCancel: function () {
                if (current()) { self.enableButtons(); }
            }
        });
    };

    /** The handlers of the steps, delegated on the dialog's content. */
    AjaxUploader.prototype.delegateWindowEvents = function () {
        var self = this, conf = this.conf, c = this.content(), defaultValues = {};
        var d = this.delegates = new Delegates(c);

        // the name field's hint ("The name will be autogenerated"): cleared on click, back when left empty
        d.on('click', '.' + conf.defaultValuedInputClass, function () {
            defaultValues[this.id || this.name] = this.value;
            this.value = '';
            removeClass(this, conf.defaultValuedInputClass);
            this.classList.add(HAD_DEFAULT_VALUE);
        });
        d.on('focusout', '.' + HAD_DEFAULT_VALUE, function () {
            var key = this.id || this.name;
            if (this.value === '' && defaultValues[key]) {
                this.value = defaultValues[key];
                this.classList.add(conf.defaultValuedInputClass);
            }
        });

        // a location chosen, or a file: the first button is highlighted and the error text cleared
        var highlight = function () {
            var button = first(c, 'input[type="submit"]');
            if (button) { button.classList.add('defaultbutton'); }
            setText(first(c, conf.validationErrorTextElement), '');
        };
        d.on('click', conf.location.browse + ' input[type="radio"]', highlight);
        d.on('change', 'input[type="file"]', highlight);

        // before any submit: required fields, the target and the token as hidden fields
        d.on('click', 'form input[type="submit"]', function (e) {
            var valid = true, hiddenPlace = first(c, 'form p');
            each(all(c, conf.requiredInput), function (input) {
                if (!input.value) {
                    each(all(c, 'label[for="' + input.id + '"]'), function (label) { label.classList.add(conf.labelErrorClass); });
                    valid = false;
                }
            });
            if (!valid) {
                setText(first(c, conf.validationErrorTextElement), conf.validationErrorText);
                e.preventDefault();
                d.stopNow(e);
                return;
            }
            each(all(c, 'label'), function (label) { removeClass(label, conf.labelErrorClass); });
            each(all(c, '.' + conf.defaultValuedInputClass), function (input) { input.value = ''; });
            setText(first(c, conf.validationErrorTextElement), '');
            if (!hiddenPlace) { return; }
            // added once: a second press (a step shown again, a refused submit) replaces them instead of doubling
            each(all(hiddenPlace, 'input[data-exp-uploader-field]'), function (input) { input.parentNode.removeChild(input); });
            var hidden = function (attributes, value) {
                var input = document.createElement('input');
                Object.keys(attributes).forEach(function (k) { input.setAttribute(k, attributes[k]); });
                input.value = value === null || value === undefined ? '' : String(value);
                hiddenPlace.appendChild(input);
            };
            self.handlerData().forEach(function (f) {
                hidden({ type: 'hidden', 'data-exp-uploader-field': '1', name: f.name }, f.value);
            });
            if (conf.token) {
                hidden({ type: 'hidden', name: 'ezxform_token', 'data-exp-uploader-field': '1' }, conf.token);
            }
        });

        // step 1: the upload
        d.on('click', conf.upload.form + ' input[type="submit"]', function (e) {
            e.preventDefault();
            d.stopNow(e);
            var up = self.upload;
            if (!up) { return; }
            if (!up.files().some(function (f) { return f.status === 'queued'; })) {
                var input = first(this.closest('form'), 'input[type="file"]');
                if (input && input.files && input.files.length) { up.add(input.files); }
            }
            up.start();
        });

        // step 2: the location
        d.on('click', conf.location.form + ' input[type="submit"]', function (e) {
            var form = this.closest('form');
            if (!Array.prototype.some.call(all(c, 'input[type="radio"]'), function (r) { return r.checked; })) {
                setText(first(c, conf.validationErrorTextElement), conf.location.required);
                e.preventDefault();
                d.stopNow(e);
                return;
            }
            self.call(conf.location.action, { data: serializeArray(form) });
            e.preventDefault();
            d.stop(e);
        });

        // step 2: browsing for the location (the links' href is the call: ezajaxuploader::browse::<handler>::<node>::<class>;
        // the target goes with it, so the server asks the handler whether the user may upload, as in the other steps)
        d.on('click', conf.location.browse + ' a', function (e) {
            e.preventDefault();
            d.stopNow(e);
            self.call(this.getAttribute('href'), { data: self.handlerData() }, first(c, conf.location.browse)).then(function (content) {
                var button = first(c, 'input[type="submit"]');
                if (content && button) {
                    removeClass(button, 'defaultbutton');
                    button.classList.add('button');
                }
            });
        });

        // step 3: "Add"
        d.on('click', conf.preview.form + ' input[type="submit"]', function (e) {
            e.preventDefault();
            d.stop(e);
            conf.preview.callback.call(self);
        });

        // the last before any submit: no second click
        d.on('click', 'form input[type="submit"]', function () {
            this.classList.add('button-disabled');
            removeClass(this, 'defaultbutton');
            removeClass(this, 'button');
            this.disabled = true;
        });

        // these forms are never sent to their action (Enter in a field clicks the first button, handled above)
        d.on('submit', 'form', function (e) { e.preventDefault(); });
    };

    AjaxUploader.prototype.cleanup = function () {
        if (this.upload) { this.upload.destroy(); this.upload = null; }
        if (this.delegates) { this.delegates.off(); this.delegates = null; }
        this.endAjax();
        this.dialog = null;
    };

    /** Opens the dialog and asks the server for the step 1 form. */
    AjaxUploader.prototype.open = function () {
        var self = this;
        if (this.dialog && this.dialog.isOpen) { return; }
        this.lastMetaData = false;
        this.dialog = Exp.dialog.create({
            title: this.conf.title, width: this.conf.width, className: 'exp-ajaxuploader', closeSelector: this.conf.closeSelector,
            onClose: function () { self.cleanup(); }
        });
        this.delegateWindowEvents();
        this.dialog.open();
        this.call(this.conf.open.action, { data: this.handlerData() });
    };

    AjaxUploader.prototype.close = function () {
        if (this.dialog) { this.dialog.close(null); }
    };

    /** The buttons as a list of elements: an element, a selector, a NodeList, an array or jQuery. */
    function elements(buttons) {
        if (!buttons) { return []; }
        if (typeof buttons === 'string') { return Array.prototype.slice.call(document.querySelectorAll(buttons)); }
        if (buttons.nodeType) { return [buttons]; }
        return Array.prototype.slice.call(buttons);
    }

    /** The uploader of a button, or null. */
    function get(button) {
        var el = elements(button)[0];
        return (el && instances.get(el)) || null;
    }

    /** Sets the uploader up on each button that has none yet; returns their uploaders. */
    function expAjaxUploader(buttons, conf) {
        var token = document.getElementById(Exp.config.tokenElement || 'ezxform_token_js'), jq = Exp.$;
        return elements(buttons).map(function (b) {
            var existing = get(b);
            if (existing) { return existing; }
            var data = String(b.getAttribute('name') || '').replace('RelationUploadNew', '').split('-');
            var c = extend(true, {}, conf, { target: { ObjectRelationsAttributeId: data[0], Version: data[1] } });
            if (token) { c.token = token.getAttribute('title'); }
            var uploader = new AjaxUploader(c);
            instances.set(b, uploader);
            if (jq) { jq(b).data('expAjaxUploader', uploader); }
            b.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploader.open();
            });
            removeClass(b, 'hide');
            return uploader;
        });
    }
    expAjaxUploader.get = get;
    expAjaxUploader.AjaxUploader = AjaxUploader;
    window.expAjaxUploader = expAjaxUploader;

    // the jQuery plugin, where the page has jQuery (the older admin design's templates call it)
    if (Exp.$ && Exp.$.fn) {
        Exp.$.fn.expAjaxUploader = function (conf) {
            expAjaxUploader(this.get(), conf);
            return this;
        };
        Exp.$.fn.expAjaxUploader.AjaxUploader = AjaxUploader;
    }
}(window, document));
