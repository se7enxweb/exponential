/*!
 * expimageeditor: the image editor of the image attribute in admin4, without jQuery and under the Content Security
 * Policy (no inline script or style). This file is the frame both editors share:
 *
 * - a button.expimageeditor-open beside a stored image (content/datatype/edit/ezimage_editor.tpl) opens a modal
 *   <dialog> with a toolbar, the stage, a panel for the options of the current tool, Cancel and Save;
 * - the editor for the image's type is loaded on first use (data-conf scripts.raster / scripts.svg, files of this
 *   site) and registers itself with ExpImageEditor.register( kind, factory );
 * - Save posts the editor's result (a Blob) with the page's form token to expimageeditor/save/<attribute>/<version>,
 *   which stores it in the draft and answers with the attribute's edit template; its first .block (the current image)
 *   takes the place of the block that holds the button. Cancel leaves the draft untouched.
 *
 * The settings and the translated texts come from #expimageeditor-config (data-conf, data-labels: JSON). A factory
 * gets a context (see Session.prototype.context) and returns, or resolves to, { getBlob(): Promise<Blob>,
 * destroy() }.
 *
 * Fonts (expimageeditor.ini [EditorSettings] Fonts[], data-conf fonts): loaded with the FontFace API when an editor
 * opens, once per page and only those listed; a font that does not load is left out and named in the status line.
 * The editors offer the loaded ones through ctx.fontField().
 *
 * Copyright CJW Network and Exponential Foundation. All rights reserved.
 * GNU General Public License v2.0 (or any later version).
 */
(function (window, document) {
    'use strict';

    var factories = {};
    var loaded = {};
    var settings = null;
    var fontsLoaded = null;

    /** The settings and texts of #expimageeditor-config, read once */
    function config() {
        if (settings) { return settings; }
        var node = document.getElementById('expimageeditor-config');
        settings = { conf: {}, labels: {} };
        if (node) {
            try { settings.conf = JSON.parse(node.getAttribute('data-conf') || '{}') || {}; } catch (e) { settings.conf = {}; }
            try { settings.labels = JSON.parse(node.getAttribute('data-labels') || '{}') || {}; } catch (e) { settings.labels = {}; }
        }
        return settings;
    }

    /** A translated text, %1 ... filled in */
    function t(key) {
        var text = Object.prototype.hasOwnProperty.call(config().labels, key) ? String(config().labels[key]) : key;
        for (var i = 1; i < arguments.length; i++) { text = text.split('%' + i).join(String(arguments[i])); }
        return text;
    }

    /** An element with attributes and children (strings become text) */
    function el(name, attributes, children) {
        var node = document.createElement(name);
        Object.keys(attributes || {}).forEach(function (key) {
            var value = attributes[key];
            if (value === null || value === undefined || value === false) { return; }
            if (key === 'text') { node.textContent = value; } else { node.setAttribute(key, value === true ? '' : String(value)); }
        });
        (children || []).forEach(function (child) {
            if (child !== null && child !== undefined) { node.appendChild(typeof child === 'string' ? document.createTextNode(child) : child); }
        });
        return node;
    }

    function icon(name) {
        return el('span', { 'class': 'expimageeditor-icon expimageeditor-icon-' + name, 'aria-hidden': 'true' });
    }

    /** Loads the scripts one after the other, each once per page */
    function loadScripts(urls) {
        return (urls || []).reduce(function (chain, url) {
            return chain.then(function () {
                if (!loaded[url]) {
                    loaded[url] = new Promise(function (resolve, reject) {
                        var script = el('script', { src: url });
                        script.addEventListener('load', resolve);
                        script.addEventListener('error', function () { reject(new Error(t('load_failed'))); });
                        document.head.appendChild(script);
                    });
                }
                return loaded[url];
            });
        }, Promise.resolve());
    }

    /** The factory of an editor kind, once its scripts have registered it */
    function factory(kind) {
        if (factories[kind]) { return Promise.resolve(factories[kind]); }
        var scripts = (config().conf.scripts || {})[kind];
        if (!scripts) { return Promise.reject(new Error(t('load_failed'))); }
        return loadScripts(scripts).then(function () {
            if (!factories[kind]) { throw new Error(t('load_failed')); }
            return factories[kind];
        });
    }

    /** The CSS font-family list of a configured family: the face, then the system font */
    function fontStack(family) {
        return family ? '"' + family + '", system-ui, sans-serif' : 'system-ui, sans-serif';
    }

    /**
     * The fonts of data-conf, loaded: { fonts: [ {label, family, url, outline} ], failed: [label] }. A family is a
     * plain name (no quotes, commas or brackets, so it is safe in a CSS font list) and the file is on this site.
     */
    function loadFonts() {
        if (fontsLoaded) { return fontsLoaded; }
        var failed = [];
        var list = (config().conf.fonts || []).filter(function (font) {
            if (!font || typeof font.url !== 'string' || !/^[\w][\w \-]*$/.test(String(font.family || ''))) { return false; }
            try { return new window.URL(font.url, window.location.href).origin === window.location.origin; } catch (e) { return false; }
        });
        fontsLoaded = Promise.all(list.map(function (font) {
            if (typeof window.FontFace !== 'function' || !document.fonts) {
                failed.push(String(font.label));
                return null;
            }
            var href = new window.URL(font.url, window.location.href).href;
            var face = new window.FontFace(font.family, 'url(' + JSON.stringify(href) + ')');
            return face.load().then(function () {
                document.fonts.add(face);
                return { label: String(font.label || font.family), family: font.family, url: href, outline: font.outline || '' };
            }, function () {
                failed.push(String(font.label || font.family));
                return null;
            });
        })).then(function (fonts) {
            return { fonts: fonts.filter(Boolean), failed: failed };
        });
        return fontsLoaded;
    }

    /** The form token of the page (ezformtoken writes it into the head) */
    function formToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /** "mod+z" style shortcut of a key event */
    function shortcutOf(event) {
        var parts = [];
        if (event.ctrlKey || event.metaKey) { parts.push('mod'); }
        // Shift counts with a named key and with a modifier (mod+shift+z); a letter or sign alone carries it in its key
        if (event.shiftKey && (event.key.length > 1 || event.ctrlKey || event.metaKey)) { parts.push('shift'); }
        if (event.altKey) { parts.push('alt'); }
        parts.push(event.key.length === 1 ? event.key.toLowerCase() : event.key);
        return parts.join('+');
    }

    function isTyping(target) {
        return target && (target.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(target.tagName));
    }

    /**
     * One editing session: the dialog for the image of one button.
     * @param {HTMLElement} button
     */
    function Session(button) {
        this.button = button;
        this.data = {
            save: button.getAttribute('data-expimageeditor-save'),
            src: button.getAttribute('data-expimageeditor-src'),
            type: button.getAttribute('data-expimageeditor-type') || '',
            name: button.getAttribute('data-expimageeditor-name') || ''
        };
        this.kind = this.data.type === 'image/svg+xml' ? 'svg' : 'raster';
        this.dirty = false;
        this.shortcuts = {};
        this.editor = null;
        this.build();
    }

    Session.prototype.build = function () {
        var self = this;
        var titleId = 'expimageeditor-title-' + Date.now();
        this.status = el('p', { 'class': 'expimageeditor-status', role: 'status', 'aria-live': 'polite' });
        this.toolbar = el('div', { 'class': 'expimageeditor-toolbar', role: 'toolbar', 'aria-label': t('tools'), 'aria-controls': 'expimageeditor-stage' });
        this.stage = el('div', { 'class': 'expimageeditor-stage', id: 'expimageeditor-stage' });
        this.panel = el('div', { 'class': 'expimageeditor-panel', role: 'group', hidden: true });
        this.saveButton = el('button', { type: 'button', 'class': 'button defaultbutton expimageeditor-save', disabled: true, text: t('save') });
        this.cancelButton = el('button', { type: 'button', 'class': 'button expimageeditor-cancel', text: t('cancel') });
        this.discardButton = el('button', { type: 'button', 'class': 'button expimageeditor-discard', text: t('discard') });
        this.keepButton = el('button', { type: 'button', 'class': 'button expimageeditor-keep', text: t('keep_editing') });
        this.confirm = el('div', { 'class': 'expimageeditor-confirm', role: 'alert', hidden: true }, [
            el('span', { text: t('discard_question') }), this.discardButton, this.keepButton
        ]);
        this.dialog = el('dialog', { 'class': 'expimageeditor expimageeditor-' + this.kind, 'aria-labelledby': titleId }, [
            el('header', { 'class': 'expimageeditor-head' }, [
                el('h2', { id: titleId, text: t('title', this.data.name) }),
                this.status
            ]),
            this.toolbar,
            el('div', { 'class': 'expimageeditor-body' }, [this.stage, this.panel]),
            el('footer', { 'class': 'expimageeditor-foot' }, [this.confirm, this.cancelButton, this.saveButton])
        ]);

        this.saveButton.addEventListener('click', function () { self.save(); });
        this.cancelButton.addEventListener('click', function () { self.cancel(); });
        this.discardButton.addEventListener('click', function () { self.close(); });
        this.keepButton.addEventListener('click', function () { self.showConfirm(false); });
        // Escape: the same question as Cancel
        this.dialog.addEventListener('cancel', function (event) { event.preventDefault(); self.cancel(); });
        this.dialog.addEventListener('keydown', function (event) { self.keydown(event); });
        this.toolbar.addEventListener('keydown', function (event) { self.toolbarKey(event); });
        document.body.appendChild(this.dialog);
    };

    Session.prototype.open = function () {
        var self = this;
        this.dialog.showModal();
        this.busy(true, t('loading'));
        Promise.all([factory(this.kind), loadFonts()]).then(function (results) {
            self.fonts = results[1];
            return results[0](self.context());
        }).then(function (editor) {
            // closed while it was loading: let go of what it holds (listeners, style sheets, object URLs) at once
            if (self.closed) {
                if (editor && typeof editor.destroy === 'function') { editor.destroy(); }
                return;
            }
            self.editor = editor;
            // an editor that has not said anything yet: the loading notice goes
            self.busy(false, self.status.textContent === t('loading') ? '' : undefined);
            if (self.fonts.failed.length) { self.announce(t('font_failed', self.fonts.failed.join(', ')), true); }
            self.rovingTabindex();
            var first = self.toolbar.querySelector('button:not([disabled])');
            (first || self.cancelButton).focus();
        }).catch(function (error) {
            if (self.closed) { return; }
            self.busy(false, '');
            self.announce(error && error.message ? error.message : t('load_failed'), true);
            self.cancelButton.focus();
        });
    };

    /** What an editor gets to work with */
    Session.prototype.context = function () {
        var self = this;
        var context = {
            t: t,
            el: el,
            icon: icon,
            conf: config().conf,
            /** The configured fonts that have loaded: [ {label, family, url, outline} ] */
            fonts: this.fonts ? this.fonts.fonts : [],
            fontStack: fontStack,
            /** Loads the scripts of data-conf scripts[name] (each once per page) */
            scripts: function (name) {
                var urls = (config().conf.scripts || {})[name];
                return urls ? loadScripts(urls) : Promise.reject(new Error(t('load_failed')));
            },
            src: this.data.src,
            type: this.data.type,
            name: this.data.name,
            stage: this.stage,
            dialog: this.dialog,
            /** A group of tool buttons in the toolbar */
            group: function (label) {
                var group = el('div', { 'class': 'expimageeditor-group', role: 'group', 'aria-label': label });
                self.toolbar.appendChild(group);
                return group;
            },
            /**
             * A tool button: { icon, label, shortcut ("c", "mod+z"), pressed (true/false for a toggle), onClick }.
             * The shortcut is shown in the tooltip and works while the focus is not in a field.
             */
            button: function (group, options) {
                var label = options.label + (options.shortcut ? ' (' + self.shortcutLabel(options.shortcut) + ')' : '');
                var button = el('button', {
                    type: 'button', 'class': 'expimageeditor-tool', title: label, 'aria-label': options.label,
                    'aria-keyshortcuts': options.shortcut ? self.ariaShortcut(options.shortcut) : null,
                    'aria-pressed': typeof options.pressed === 'boolean' ? String(options.pressed) : null,
                    'data-tool': options.id || null
                }, [icon(options.icon)]);
                button.addEventListener('click', function () { if (!button.disabled) { options.onClick(button); } });
                if (options.shortcut) {
                    self.shortcuts[options.shortcut] = function () { if (!button.disabled) { button.click(); } };
                }
                group.appendChild(button);
                return button;
            },
            /** A keyboard shortcut without a button */
            shortcut: function (combo, handler) { self.shortcuts[combo] = handler; },
            /** Marks one toggle button of a set as pressed */
            press: function (button) {
                Array.prototype.forEach.call(self.toolbar.querySelectorAll('.expimageeditor-tool[aria-pressed]'), function (other) {
                    other.setAttribute('aria-pressed', String(other === button));
                });
            },
            /** The options of the current tool, or none (null) */
            panel: function (label, nodes) {
                self.panel.textContent = '';
                if (!nodes || !nodes.length) {
                    self.panel.hidden = true;
                    self.panel.removeAttribute('aria-label');
                    return self.panel;
                }
                self.panel.setAttribute('aria-label', label);
                self.panel.appendChild(el('p', { 'class': 'expimageeditor-panel-title', text: label }));
                nodes.forEach(function (node) { self.panel.appendChild(node); });
                self.panel.hidden = false;
                return self.panel;
            },
            /**
             * A labelled field: { label, type (range, number, color, text, select, radio, checkbox), value, min, max, step,
             * options: [ [value, text] ], onInput(value), onChange(value) }
             */
            field: function (options) {
                var id = 'expimageeditor-field-' + Math.random().toString(36).slice(2);
                var input;
                // a choice is a group of radio buttons, not a <select>: WebKit reports a <select> under
                // style-src 'self' (style-src-attr, from its own controls, also for a <select> in the page's HTML)
                if (options.type === 'radio') {
                    return el('fieldset', { 'class': 'expimageeditor-field expimageeditor-field-radio' },
                        [el('legend', { text: options.label })].concat((options.options || []).map(function (option, index) {
                            var radio = el('input', { type: 'radio', name: id, id: id + '-' + index, value: option[0],
                                checked: String(option[0]) === String(options.value) });
                            radio.addEventListener('change', function () {
                                if (radio.checked && options.onChange) { options.onChange(radio.value); }
                            });
                            return el('div', { 'class': 'expimageeditor-radio' }, [radio, el('label', { 'for': radio.id, text: option[1] })]);
                        })));
                }
                if (options.type === 'select') {
                    input = el('select', { id: id }, (options.options || []).map(function (option) {
                        return el('option', { value: option[0], selected: String(option[0]) === String(options.value), text: option[1] });
                    }));
                } else {
                    input = el('input', {
                        id: id, type: options.type || 'text', min: options.min, max: options.max, step: options.step,
                        value: options.type === 'checkbox' ? null : options.value, checked: options.type === 'checkbox' && !!options.value
                    });
                }
                var output = options.type === 'range' ? el('output', { 'for': id, text: String(options.value) }) : null;
                var read = function () {
                    if (options.type === 'checkbox') { return input.checked; }
                    return /^(range|number)$/.test(options.type) ? Number(input.value) : input.value;
                };
                input.addEventListener('input', function () {
                    if (output) { output.textContent = input.value; }
                    if (options.onInput) { options.onInput(read()); }
                });
                input.addEventListener('change', function () { if (options.onChange) { options.onChange(read()); } });
                var label = el('label', { 'for': id, text: options.label });
                return el('div', { 'class': 'expimageeditor-field expimageeditor-field-' + (options.type || 'text') },
                    options.type === 'checkbox' ? [input, label] : [label, input, output]);
            },
            /**
             * The choice of a font: the system font ('') and the loaded fonts, each shown in its face; { label, value,
             * onChange(family) }. null without configured fonts. Radio buttons up to six choices (no <select>, which
             * WebKit reports under the CSP), a <select> for a longer list, where radio buttons would fill the panel.
             */
            fontField: function (options) {
                if (!context.fonts.length) { return null; }
                var choices = [['', t('font_default')]].concat(context.fonts.map(function (font) { return [font.family, font.label]; }));
                var field = context.field({ type: choices.length > 6 ? 'select' : 'radio', label: options.label,
                    value: options.value || '', options: choices, onChange: options.onChange });
                Array.prototype.forEach.call(field.querySelectorAll('.expimageeditor-radio label, option'), function (node) {
                    var value = node.tagName === 'OPTION' ? node.value : node.previousElementSibling.value;
                    // CSSOM, not a style attribute: allowed under style-src 'self'
                    if (value) { node.style.setProperty('font-family', fontStack(value)); }
                });
                return field;
            },
            /** Selects a value in a choice built by field() (radio or select) without an event */
            setChoice: function (field, value) {
                var select = field.querySelector('select');
                if (select) {
                    if (select !== document.activeElement) { select.value = String(value); }
                    return;
                }
                Array.prototype.forEach.call(field.querySelectorAll('input[type="radio"]'), function (radio) {
                    radio.checked = radio.value === String(value);
                });
            },
            /** A control in the footer, before Cancel and Save (an option of saving) */
            foot: function (node) {
                node.classList.add('expimageeditor-foot-option');
                self.confirm.parentNode.insertBefore(node, self.confirm);
                return node;
            },
            /** A plain button for the panel */
            action: function (text, handler, primary) {
                var button = el('button', { type: 'button', 'class': 'button' + (primary ? ' defaultbutton' : ''), text: text });
                button.addEventListener('click', handler);
                return button;
            },
            setDirty: function (dirty) { self.setDirty(dirty); },
            announce: function (text, isError) { self.announce(text, isError); },
            busy: function (busy, text) { self.busy(busy, text); },
            /** The type a raster image is saved in: its own when OutputMIMETypes[] has it, else PNG */
            outputType: function () {
                var types = config().conf.outputTypes || [];
                return types.indexOf(self.data.type) !== -1 && self.data.type !== 'image/svg+xml' ? self.data.type : 'image/png';
            }
        };
        return context;
    };

    Session.prototype.shortcutLabel = function (combo) {
        var mac = /Mac|iPhone|iPad/.test(window.navigator.platform || '');
        return combo.split('+').map(function (part) {
            if (part === 'mod') { return mac ? '⌘' : 'Ctrl'; }
            if (part === 'shift') { return mac ? '⇧' : 'Shift'; }
            if (part === 'alt') { return mac ? '⌥' : 'Alt'; }
            return part.length === 1 ? part.toUpperCase() : part;
        }).join(mac ? '' : '+');
    };

    Session.prototype.ariaShortcut = function (combo) {
        return combo.split('+').map(function (part) {
            if (part === 'mod') { return /Mac|iPhone|iPad/.test(window.navigator.platform || '') ? 'Meta' : 'Control'; }
            if (part === 'shift') { return 'Shift'; }
            if (part === 'alt') { return 'Alt'; }
            return part.length === 1 ? part.toUpperCase() : part;
        }).join('+');
    };

    Session.prototype.keydown = function (event) {
        if (event.defaultPrevented || this.saving) { return; }
        var combo = shortcutOf(event);
        // in a field only the shortcuts with a modifier, and not the ones the field needs itself (undo in a text field)
        if (isTyping(event.target) && (combo.indexOf('mod+') !== 0 || /^mod\+(shift\+)?[zyacvx]$/.test(combo))) { return; }
        if (this.shortcuts[combo]) {
            event.preventDefault();
            this.shortcuts[combo](event);
        }
    };

    /** The toolbar is one tab stop; the arrow keys, Home and End move between its buttons */
    Session.prototype.rovingTabindex = function () {
        var buttons = this.toolbar.querySelectorAll('.expimageeditor-tool');
        Array.prototype.forEach.call(buttons, function (button, index) { button.tabIndex = index === 0 ? 0 : -1; });
    };

    Session.prototype.toolbarKey = function (event) {
        var buttons = Array.prototype.filter.call(this.toolbar.querySelectorAll('.expimageeditor-tool'), function (b) { return !b.disabled; });
        var index = buttons.indexOf(document.activeElement);
        if (index === -1) { return; }
        var rtl = window.getComputedStyle(this.toolbar).direction === 'rtl';
        var next = { ArrowRight: rtl ? -1 : 1, ArrowLeft: rtl ? 1 : -1, ArrowDown: 1, ArrowUp: -1 }[event.key];
        var target = null;
        if (next) { target = buttons[(index + next + buttons.length) % buttons.length]; }
        if (event.key === 'Home') { target = buttons[0]; }
        if (event.key === 'End') { target = buttons[buttons.length - 1]; }
        if (target) {
            event.preventDefault();
            buttons.forEach(function (b) { b.tabIndex = b === target ? 0 : -1; });
            target.focus();
        }
    };

    Session.prototype.setDirty = function (dirty) {
        this.dirty = !!dirty;
        this.saveButton.disabled = !this.dirty || this.saving;
    };

    Session.prototype.announce = function (text, isError) {
        this.status.textContent = text || '';
        this.status.classList.toggle('is-error', !!isError);
    };

    Session.prototype.busy = function (busy, text) {
        this.dialog.classList.toggle('is-busy', !!busy);
        this.dialog.setAttribute('aria-busy', busy ? 'true' : 'false');
        if (text !== undefined) { this.announce(text); }
    };

    Session.prototype.showConfirm = function (show) {
        this.confirm.hidden = !show;
        this.cancelButton.hidden = show;
        this.saveButton.hidden = show;
        (show ? this.keepButton : this.cancelButton).focus();
    };

    Session.prototype.cancel = function () {
        if (this.saving) { return; }
        if (this.dirty) { this.showConfirm(true); } else { this.close(); }
    };

    Session.prototype.close = function () {
        this.closed = true;
        if (this.editor && typeof this.editor.destroy === 'function') {
            try { this.editor.destroy(); } catch (e) { /* the dialog goes anyway */ }
        }
        this.dialog.close();
        this.dialog.remove();
        if (this.button && this.button.isConnected) { this.button.focus(); }
    };

    Session.prototype.save = function () {
        var self = this;
        if (!this.editor || this.saving || !this.dirty) { return; }
        this.saving = true;
        this.saveButton.disabled = true;
        this.busy(true, t('saving'));
        Promise.resolve(this.editor.getBlob()).then(function (blob) {
            var body = new window.FormData();
            body.append('image', blob, self.data.name || 'image');
            return window.fetch(self.data.save, {
                method: 'POST',
                body: body,
                credentials: 'same-origin',
                headers: { 'X-CSRF-Token': formToken(), 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }
            });
        }).then(function (response) {
            return response.json().catch(function () { return {}; }).then(function (json) {
                if (!response.ok || typeof json.html !== 'string') {
                    throw new Error(json.error || t('save_failed', response.status));
                }
                return json.html;
            });
        }).then(function (html) {
            self.saving = false;
            self.replaceBlock(html);
        }).catch(function (error) {
            self.saving = false;
            self.busy(false);
            self.setDirty(true);
            self.announce(error && error.message ? error.message : t('save_failed', ''), true);
        });
    };

    /** Puts the current image block of the server's answer in place of the button's block, then closes */
    Session.prototype.replaceBlock = function (html) {
        var template = document.createElement('template');
        template.innerHTML = html;
        var fresh = template.content.querySelector('.block');
        var old = this.button.closest('.block');
        var focus = null;
        if (fresh && old) {
            old.replaceWith(fresh);
            focus = fresh.querySelector('.expimageeditor-open');
            if (focus) { focus.hidden = false; }
        }
        this.button = focus || this.button;
        this.dirty = false;
        this.close();
        if (fresh) {
            fresh.dispatchEvent(new window.CustomEvent('expimageeditor:saved', { bubbles: true }));
        }
    };

    window.ExpImageEditor = {
        /** Registers the factory of an editor kind ("raster", "svg") */
        register: function (kind, create) {
            factories[kind] = create;
        },
        /** Opens the editor for the image of a button.expimageeditor-open */
        open: function (button) {
            var session = new Session(button);
            session.open();
            return session;
        },
        t: t
    };

    document.addEventListener('click', function (event) {
        var button = event.target.closest ? event.target.closest('.expimageeditor-open') : null;
        if (!button || button.disabled) { return; }
        event.preventDefault();
        window.ExpImageEditor.open(button);
    });

    // without this script the button stays hidden (the template renders it hidden)
    function reveal() {
        Array.prototype.forEach.call(document.querySelectorAll('.expimageeditor-open[hidden]'), function (button) { button.hidden = false; });
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', reveal); } else { reveal(); }
    document.addEventListener('expimageeditor:saved', reveal);
}(window, document));
