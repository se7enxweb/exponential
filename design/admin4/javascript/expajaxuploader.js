/*!
 * The admin's "Upload a file" into an object relation, on Exponential UI (jQuery 4): Exp.dialog for the modal window,
 * $.fn.expUpload for the file, Exp.io for the other calls. It does what ezajaxuploader.js (YUI 3, with
 * ezmodalwindow.js) does, step by step and with the same server calls, the same POSTed fields in the same order and
 * the same configuration, so the templates keep their settings:
 *
 *   $('.simple-relation-upload-new').expAjaxUploader({
 *       open:     { action: 'ezajaxuploader::uploadform::ezobjectrelation' },
 *       upload:   { action: 'ezajaxuploader::upload::ezobjectrelation?ContentType=html', form: 'form.ajaxuploader-upload' },
 *       location: { action: 'ezajaxuploader::preview::ezobjectrelation', form: 'form.ajaxuploader-location',
 *                   browse: 'div.ajaxuploader-browse', required: 'Please choose a location' },
 *       preview:  { form: 'form.ajaxuploader-preview', callback: function () { this.lastMetaData; this.close(); } },
 *       title, validationErrorText, parseJSONErrorText
 *   });
 *
 * Each button's name (RelationUploadNew<attribute id>-<version>) gives the target posted with every call
 * (AjaxUploadHandlerData[ObjectRelationsAttributeId], AjaxUploadHandlerData[Version]); the page's form token goes
 * with them. A click on the button opens the dialog; the button is shown (its "hide" class removed) once it works.
 * Instance: $(button).data('expAjaxUploader'): open(), close(), dialog (Exp.dialog's Dialog), lastMetaData, conf.
 * GNU General Public License v2.0 (or any later version).
 */
(function (window, document) {
    'use strict';

    var Exp = window.Exp;
    if (!Exp || !Exp.$ || !Exp.dialog || !Exp.io || !Exp.$.fn.expUpload) {
        if (window.console) { window.console.error('expAjaxUploader: Exponential UI (exp::dialog, exp::io, exp::upload) is not on the page'); }
        return;
    }
    var $ = Exp.$;

    var HANDLER_FIELD_NAME = 'AjaxUploadHandlerData';
    var HAD_DEFAULT_VALUE = 'had-default-value';
    var NS = '.expajaxuploader';

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

    /** 'class::function::arg::arg' as Exp.io.call()'s name and arguments. */
    function split(action) {
        var parts = String(action).split('::');
        return { fn: parts[0] + '::' + parts[1], args: parts.slice(2) };
    }

    function AjaxUploader(conf) {
        this.conf = $.extend(true, {}, DEFAULTS, conf);
        this.lastMetaData = false;
        this.dialog = null;
        this.upload = null;
        var self = this;
        // what the YUI version offered its callbacks as this.modalWindow
        this.modalWindow = {
            close: function () { self.close(); },
            setContent: function (html) { self.setContent(html); },
            getContentNode: function () { return self.content(); }
        };
    }

    AjaxUploader.prototype.content = function () { return this.dialog ? this.dialog.$body : $(); };

    AjaxUploader.prototype.setContent = function (html) {
        if (this.upload) { this.upload.destroy(); this.upload = null; }
        this.dialog.setContent(html);
        this.bindUpload();
    };

    /** The error, in the error template, as the whole content (as the YUI version did). */
    AjaxUploader.prototype.displayError = function (text) {
        var $e = $(this.conf.errorTemplate);
        $e.html($e.html().replace('%message', $('<div></div>').text(String(text)).html()));
        this.setContent($e);
    };

    AjaxUploader.prototype.waitAjax = function () { if (this.dialog) { this.dialog.busy(true); } };
    AjaxUploader.prototype.endAjax = function () { if (this.dialog) { this.dialog.busy(false); } };

    /** The target as POST fields: AjaxUploadHandlerData[<key>] = <value>. */
    AjaxUploader.prototype.handlerData = function () {
        var target = this.conf.target;
        return Object.keys(target).map(function (k) { return { name: HANDLER_FIELD_NAME + '[' + k + ']', value: target[k] }; });
    };

    /** An ezjscore call whose content is {meta_data, html}: shows the html (or into $into), keeps the meta data. */
    AjaxUploader.prototype.call = function (action, opts, $into) {
        var self = this, a = split(action), dialog = this.dialog;
        // an answer for a dialog closed (or closed and opened again) meanwhile is dropped
        var current = function () { return self.dialog === dialog && dialog && dialog.isOpen; };
        this.waitAjax();
        return Exp.io.call(a.fn, a.args, opts).then(function (content) {
            if (!current()) { return; }
            self.endAjax();
            self.lastMetaData = content ? content.meta_data : false;
            if ($into) { $into[0].innerHTML = content.html; } else { self.setContent(content.html); }
            return content;
        }, function (error) {
            if (!current()) { return; }
            self.endAjax();
            self.displayError(error && error.message ? error.message : self.conf.parseJSONErrorText);
        });
    };

    /** The step's buttons usable again (after an upload was canceled). */
    AjaxUploader.prototype.enableButtons = function () {
        this.content().find('form input[type="submit"]').prop('disabled', false).removeClass('button-disabled').addClass('button');
    };

    /** The step 1 form's file field becomes an expUpload: progress while it is sent, cancel, the same fields. */
    AjaxUploader.prototype.bindUpload = function () {
        var self = this, $form = this.content().find(this.conf.upload.form).first(), dialog = this.dialog;
        var $file = $form.find('input[type="file"]').first();
        if (!$file.length) { return; }
        var current = function () { return self.dialog === dialog && dialog && dialog.isOpen; };
        // not dimmed while the file is sent (the YUI version dimmed the whole step): its progress and Cancel stay usable
        $file.expUpload({
            url: Exp.config.call + this.conf.upload.action,
            name: $file.attr('name'),
            form: $form[0],
            auto: false,
            multiple: false,
            token: false,               // the form carries it, as with the YUI version
            responseType: 'text',
            onDone: function (text) {
                if (!current()) { return; }
                var json;
                // ContentType=html: the call view sends the JSON as HTML text (quotes as &quot;); the YUI version read
                // it from an iframe, where the browser had decoded it. The same here, with an inert parser.
                try { json = JSON.parse(new window.DOMParser().parseFromString(String(text), 'text/html').body.textContent); } catch (e) {
                    self.displayError(self.conf.parseJSONErrorText);
                    return;
                }
                if (json.error_text) {
                    self.displayError(json.error_text);
                } else {
                    // the server URL-encodes the HTML of this answer (see ezjscServerFunctionsAjaxUploader::upload())
                    self.setContent(decodeURIComponent(json.html));
                }
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
        this.upload = $file.data('expUpload');
    };

    /** The handlers of the YUI version, in the same order, delegated on the dialog's content. */
    AjaxUploader.prototype.delegateWindowEvents = function () {
        var self = this, conf = this.conf, $c = this.content(), defaultValues = {};

        // the name field's hint ("The name will be autogenerated"): cleared on click, back when left empty
        $c.on('click' + NS, '.' + conf.defaultValuedInputClass, function () {
            defaultValues[this.id || this.name] = this.value;
            this.value = '';
            $(this).removeClass(conf.defaultValuedInputClass).addClass(HAD_DEFAULT_VALUE);
        });
        $c.on('focusout' + NS, '.' + HAD_DEFAULT_VALUE, function () {
            var key = this.id || this.name;
            if (this.value === '' && defaultValues[key]) {
                this.value = defaultValues[key];
                $(this).addClass(conf.defaultValuedInputClass);
            }
        });

        // a location chosen, or a file: the first button is highlighted and the error text cleared
        var highlight = function () {
            $c.find('input[type="submit"]').first().addClass('defaultbutton');
            $c.find(conf.validationErrorTextElement).first().text('');
        };
        $c.on('click' + NS, conf.location.browse + ' input[type="radio"]', highlight);
        $c.on('change' + NS, 'input[type="file"]', highlight);

        // before any submit: required fields, the target and the token as hidden fields
        $c.on('click' + NS, 'form input[type="submit"]', function (e) {
            var valid = true, $hiddenPlace = $c.find('form p').first();
            $c.find(conf.requiredInput).each(function () {
                if (!this.value) {
                    $c.find('label[for="' + this.id + '"]').addClass(conf.labelErrorClass);
                    valid = false;
                }
            });
            if (!valid) {
                $c.find(conf.validationErrorTextElement).first().text(conf.validationErrorText);
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }
            $c.find('label').removeClass(conf.labelErrorClass);
            $c.find('.' + conf.defaultValuedInputClass).val('');
            $c.find(conf.validationErrorTextElement).first().text('');
            self.handlerData().forEach(function (f) {
                $hiddenPlace.append($('<input type="hidden">').attr('name', f.name).val(f.value));
            });
            if (conf.token) {
                $hiddenPlace.append($('<input type="hidden" name="ezxform_token">').val(conf.token));
            }
        });

        // step 1: the upload
        $c.on('click' + NS, conf.upload.form + ' input[type="submit"]', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var up = self.upload;
            if (!up) { return; }
            if (!up.files().some(function (f) { return f.status === 'queued'; })) {
                var input = $(this).closest('form').find('input[type="file"]')[0];
                if (input && input.files && input.files.length) { up.add(input.files); }
            }
            up.start();
        });

        // step 2: the location
        $c.on('click' + NS, conf.location.form + ' input[type="submit"]', function (e) {
            var $form = $(this).closest('form');
            if (!$c.find('input[type="radio"]').filter(function () { return this.checked; }).length) {
                $c.find(conf.validationErrorTextElement).first().text(conf.location.required);
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }
            self.call(conf.location.action, { data: $form.serializeArray() });
            e.preventDefault();
            e.stopPropagation();
        });

        // step 2: browsing for the location (the links' href is the call: ezajaxuploader::browse::<node>::<class>)
        $c.on('click' + NS, conf.location.browse + ' a', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var $place = $c.find(conf.location.browse).first();
            self.call(this.getAttribute('href'), { method: 'GET' }, $place).then(function (content) {
                if (content) { $c.find('input[type="submit"]').first().removeClass('defaultbutton').addClass('button'); }
            });
        });

        // step 3: "Add"
        $c.on('click' + NS, conf.preview.form + ' input[type="submit"]', function (e) {
            e.preventDefault();
            e.stopPropagation();
            conf.preview.callback.call(self);
        });

        // the last before any submit: no second click
        $c.on('click' + NS, 'form input[type="submit"]', function () {
            $(this).addClass('button-disabled').removeClass('defaultbutton').removeClass('button').prop('disabled', true);
        });

        // these forms are never sent to their action (Enter in a field clicks the first button, handled above)
        $c.on('submit' + NS, 'form', function (e) { e.preventDefault(); });
    };

    AjaxUploader.prototype.cleanup = function () {
        if (this.upload) { this.upload.destroy(); this.upload = null; }
        this.content().off(NS);
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

    $.fn.expAjaxUploader = function (conf) {
        var token = document.getElementById(Exp.config.tokenElement || 'ezxform_token_js');
        return this.each(function () {
            var $b = $(this);
            if ($b.data('expAjaxUploader')) { return; }
            var data = String($b.attr('name') || '').replace('RelationUploadNew', '').split('-');
            var c = $.extend(true, {}, conf, { target: { ObjectRelationsAttributeId: data[0], Version: data[1] } });
            if (token) { c.token = token.getAttribute('title'); }
            var uploader = new AjaxUploader(c);
            $b.data('expAjaxUploader', uploader);
            $b.on('click' + NS, function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploader.open();
            });
            $b.removeClass('hide');
        });
    };
    $.fn.expAjaxUploader.AjaxUploader = AjaxUploader;
}(window, document));
