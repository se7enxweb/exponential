/*!
 * expimageeditor: the SVG editor. Registers the kind "svg" with the frame (expimageeditor.js), which loads this file
 * after svgcanvas.min.js on first use.
 *
 * The drawing engine is @svgedit/svgcanvas (window.SvgCanvas, built by build/svgcanvas): it draws, selects, moves,
 * resizes and keeps the undo history. Everything around it is ours, in admin4's style and without SVG-Edit's UI:
 *
 * - toolbar: the drawing tools (select, rectangle, ellipse, line, path, freehand, text), arrange (delete,
 *   duplicate, to front, to back), history and view (zoom);
 * - panel: fill, no fill, stroke, stroke width, opacity, and for text the font size, the font (the system font and
 *   those of expimageeditor.ini Fonts[], loaded by the frame) and the text itself; it shows the values of the
 *   selection, or the defaults for new shapes when nothing is selected;
 * - keyboard: v r e l p f t pick a tool, Delete/Backspace delete, mod+z undo, mod+shift+z and mod+y redo, mod+d
 *   duplicates, + - 0 zoom. With the focus on the drawing, N and Shift+N select the next and previous object, the
 *   arrow keys move the selection (Shift: by 10), Escape drops the tool, then the selection, and Enter adds a
 *   shape of the current tool in the middle of the view, so drawing does not need a mouse.
 *
 * Load: the stored SVG is fetched (same origin) and given to svgcanvas, which sanitises it and turns style=""
 * into presentation attributes. Save: svgcanvas serialises the drawing; a layer that svgcanvas added only to
 * hold the content is unwrapped again. The server sanitises the result once more (expSvgSanitizer).
 *
 * Text to paths: with "Save text in added fonts as paths" (in the footer, offered when a configured font has a file
 * opentype.js reads, ticked by TextToPaths), a text whose characters are all in configured fonts is saved as the
 * outlines of its glyphs: a <g> with the text's attributes, role="img", aria-label and <title> (the words), and one
 * <path> per run of characters. The browser lays the text out (getStartPositionOfChar: x, y, dx, dy, text-anchor,
 * kerning, tspans), opentype.js (opentype.min.js, loaded on the first such save) gives each glyph's outline there.
 * Only the saved file changes; the drawing keeps its <text>. A text that cannot be converted as a whole (another
 * font, a character the font lacks, collapsed white space, textPath) stays text.
 *
 * Copyright CJW Network and Exponential Foundation. All rights reserved.
 * GNU General Public License v2.0 (or any later version).
 */
(function (window, document) {
    'use strict';

    var MARGIN = 24;
    var ZOOM_MIN = 0.05;
    var ZOOM_MAX = 16;
    var ZOOM_STEP = 1.25;

    /** '#rrggbb' of a CSS colour, or the fallback for none, url(...) and what a canvas cannot read */
    var probe = document.createElement('canvas').getContext('2d');
    function toHex(value, fallback) {
        if (!value || value === 'none' || /^url\(/i.test(value)) { return fallback; }
        probe.fillStyle = '#000000';
        probe.fillStyle = String(value);
        return /^#[0-9a-f]{6}$/i.test(probe.fillStyle) ? probe.fillStyle : fallback;
    }

    var ROOT_TAG = /<svg\b((?:[^>"']|"[^"]*"|'[^']*')*)>/;
    var SIZE_ATTRS = /\s(width|height|viewBox)\s*=\s*("[^"]*"|'[^']*')/g;

    var TOKEN = /<!--[\s\S]*?-->|<!\[CDATA\[[\s\S]*?\]\]>|<\?[\s\S]*?\?>|<!DOCTYPE[^>]*>|<(\/?)([A-Za-z][\w:.-]*)((?:[^>"']|"[^"]*"|'[^']*')*)>/g;
    var STYLE_ELEMENT = /<!--[\s\S]*?-->|<style\b(?:[^>"']|"[^"]*"|'[^']*')*?(?:\/>|>[\s\S]*?<\/style\s*>)/g;

    /**
     * style="" as presentation attributes, on the text before svgcanvas parses it. svgcanvas does the same after
     * parsing, but its DOMParser document has the page's CSP and reports every style attribute it meets. A
     * declaration wins over an attribute of the same name, as in CSS; svgcanvas then drops what is not allowed.
     */
    function presentationAttributes(text) {
        return text.replace(TOKEN, function (tag, closing, name, rest) {
            if (!name || closing || !/\sstyle\s*=/.test(rest)) { return tag; }
            var end = /\/\s*$/.test(rest) ? '/' : '';
            var attrs = [];
            var style = '';
            rest.replace(/([^\s=\/]+)\s*=\s*("[^"]*"|'[^']*')/g, function (all, attr, value) {
                if (attr === 'style') { style = value.slice(1, -1); } else { attrs.push([attr, value]); }
                return all;
            });
            style.split(';').forEach(function (declaration) {
                var colon = declaration.indexOf(':');
                var prop = declaration.slice(0, colon).trim().toLowerCase();
                var value = declaration.slice(colon + 1).replace(/!\s*important\s*$/i, '').trim();
                if (colon < 1 || !/^[a-z][a-z-]*$/.test(prop) || !value) { return; }
                attrs = attrs.filter(function (pair) { return pair[0] !== prop; });
                attrs.push([prop, '"' + value.replace(/"/g, '&quot;') + '"']);
            });
            return '<' + name + attrs.map(function (pair) { return ' ' + pair[0] + '=' + pair[1]; }).join('') + end + '>';
        });
    }

    function decodeXml(text) {
        return text.replace(/<!\[CDATA\[([\s\S]*?)\]\]>/g, '$1').replace(/&(#x[0-9a-f]+|#\d+|lt|gt|amp|quot|apos);/gi, function (all, code) {
            var named = { lt: '<', gt: '>', amp: '&', quot: '"', apos: "'" }[code.toLowerCase()];
            if (named) { return named; }
            var n = code.charAt(1).toLowerCase() === 'x' ? parseInt(code.slice(2), 16) : parseInt(code.slice(1), 10);
            return n > 0 && n <= 0x10ffff ? String.fromCodePoint(n) : '';
        });
    }

    /** Selectors that could reach outside the drawing even under its scope (the page root, nesting, relational) */
    var UNSCOPABLE_SELECTOR = /&|:root|:host|:scope|:has\(|:is\(|:where\(|:not\(|\b(html|body)\b/i;

    /** A url() to anything but a fragment of the drawing, or another function that loads from a string */
    var LOADING_VALUE = /url\(\s*(?!['"]?#)|image-set\(|image\(|cross-fade\(|element\(|src\(|expression\(/i;

    /**
     * The rules of the drawing's <style> elements as a constructed style sheet, for the drawing only (CSP restricts
     * <style> elements, not the CSSOM). Only plain style rules are taken, rebuilt from their declarations: no at-rule,
     * no nested rule, no selector that could reach the admin page from inside the scope, no declaration that loads
     * anything. Each selector list is wrapped as "<scope> :is(<list>)". The server sanitises the stored file as well
     * (expSvgSanitizer); this keeps an editing session from styling the page around the drawing.
     */
    function drawingSheet(blocks, scope) {
        if (!blocks.length || typeof window.CSSStyleSheet !== 'function' || !('adoptedStyleSheets' in document)) { return null; }
        var parsed = new window.CSSStyleSheet();
        var sheet = new window.CSSStyleSheet();
        try {
            parsed.replaceSync(blocks.map(function (block) {
                return decodeXml(block.replace(/^<style\b(?:[^>"']|"[^"]*"|'[^']*')*?(\/?)>/, '').replace(/<\/style\s*>$/, ''));
            }).join('\n'));
            sheet.replaceSync(Array.prototype.map.call(parsed.cssRules, function (rule) {
                if (!(rule instanceof window.CSSStyleRule) || (rule.cssRules && rule.cssRules.length) ||
                    !rule.selectorText || UNSCOPABLE_SELECTOR.test(rule.selectorText)) { return ''; }
                var declarations = [];
                for (var i = 0; i < rule.style.length; i++) {
                    var name = rule.style.item(i), value = rule.style.getPropertyValue(name);
                    if (LOADING_VALUE.test(value)) { continue; }
                    declarations.push(name + ': ' + value + (rule.style.getPropertyPriority(name) ? ' !important' : ''));
                }
                return declarations.length ? scope + ' :is(' + rule.selectorText + ') { ' + declarations.join('; ') + ' }' : '';
            }).join('\n'));
        } catch (e) {
            return null;
        }
        document.adoptedStyleSheets = document.adoptedStyleSheets.concat([sheet]);
        return sheet;
    }

    /** width, height and viewBox of the root element (quoted as in the file), read from the text */
    function rootAttributes(text) {
        var tag = ROOT_TAG.exec(text.replace(/<!--[\s\S]*?-->/g, ''));
        var found = {};
        if (!tag) { return found; }
        tag[1].replace(SIZE_ATTRS, function (all, name, value) { found[name] = value; return all; });
        return found;
    }

    /**
     * svgcanvas keeps the drawing in layers (<g class="layer"><title>Layer 1</title> ...) and puts the content of a
     * file without layers into one. That layer is taken out again on save, from the text: svgcanvas writes it
     * as a direct child of the root with exactly this class and a title first.
     */
    function unwrapLayers(svg) {
        var token = new RegExp(TOKEN.source, 'g');
        var parts = [];
        var stack = [];
        var last = 0;
        var m;
        while ((m = token.exec(svg)) !== null) {
            if (!m[2]) { continue; }
            if (m[1]) {
                if (stack.pop() === true) {
                    parts.push(svg.slice(last, m.index));
                    last = token.lastIndex;
                }
                continue;
            }
            if (/\/\s*$/.test(m[3])) { continue; }
            var layer = stack.length === 1 && m[2] === 'g' && /^\s+class="layer"\s*$/.test(m[3]);
            stack.push(layer);
            if (layer) {
                parts.push(svg.slice(last, m.index));
                last = token.lastIndex;
                var title = /^\s*<title>[^<]*<\/title>/.exec(svg.slice(last));
                if (title) {
                    last += title[0].length;
                    token.lastIndex = last;
                }
            }
        }
        parts.push(svg.slice(last));
        return parts.join('');
    }

    /** Attributes that belong to a text only and do not go to the paths that replace it */
    var TEXT_ONLY = /^(x|y|dx|dy|rotate|textlength|lengthadjust|font|font-[a-z-]+|text-anchor|dominant-baseline|alignment-baseline|baseline-shift|letter-spacing|word-spacing|text-decoration|text-rendering|writing-mode|direction|unicode-bidi|white-space|xml:space|role|aria-label|data-expimageeditor-paths)$/i;
    var XML_NS = 'http://www.w3.org/XML/1998/namespace';

    /** The first family of a font-family value, without quotes */
    function firstFamily(value) {
        return String(value || '').split(',')[0].trim().replace(/^(["'])(.*)\1$/, '$2');
    }

    function escapeXml(text) {
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    /**
     * The attributes of an element as XML, without those of TEXT_ONLY, of foreign namespaces and style (in the drawing
     * only svgcanvas' own pointer-events and cursor; a file's style="" became attributes on load)
     */
    function attributesXml(elem) {
        return Array.prototype.filter.call(elem.attributes, function (a) {
            return !TEXT_ONLY.test(a.name) && a.name !== 'style' && !/^se_/.test(a.name === 'class' ? a.value : '') &&
                (!a.namespaceURI || a.namespaceURI === XML_NS);
        }).map(function (a) { return ' ' + a.name + '="' + escapeXml(a.value) + '"'; }).join('');
    }

    /** Turns the points of an opentype.js path by angle degrees around (cx, cy) */
    function rotatePath(path, cx, cy, angle) {
        var r = angle * Math.PI / 180, cos = Math.cos(r), sin = Math.sin(r);
        path.commands.forEach(function (c) {
            [['x', 'y'], ['x1', 'y1'], ['x2', 'y2']].forEach(function (k) {
                if (c[k[0]] === undefined) { return; }
                var dx = c[k[0]] - cx, dy = c[k[1]] - cy;
                c[k[0]] = cx + dx * cos - dy * sin;
                c[k[1]] = cy + dx * sin + dy * cos;
            });
        });
    }

    function input(field) {
        return field.querySelector('input, select');
    }

    /** Sets a field built by ctx.field, unless the user is typing in it */
    function setField(field, value) {
        var node = input(field);
        if (node === document.activeElement && node.type !== 'checkbox') { return; }
        if (node.type === 'checkbox') { node.checked = !!value; } else { node.value = String(value); }
        var output = field.querySelector('output');
        if (output) { output.textContent = String(value); }
    }

    function create(ctx) {
        var t = ctx.t;
        var stage = ctx.stage;
        var host = ctx.el('div', { 'class': 'expimageeditor-svg-host' });
        var canvas;
        var DEFAULT_FAMILY = 'sans-serif';
        var observer = null;
        var hadLayers = false;
        var rootSize = null;
        var styleBlocks = [];
        var sheet = null;
        var snapshot = null;
        var panelKey = '';
        var tools = {};
        var buttons = {};

        stage.tabIndex = 0;
        stage.setAttribute('role', 'group');
        stage.setAttribute('aria-label', t('svg_stage'));
        stage.appendChild(host);

        try {
            canvas = new window.SvgCanvas(host, {
                initFill: { color: '2563EB', opacity: 1 },
                initStroke: { color: '1D2129', opacity: 1, width: 2 },
                text: { stroke_width: 0, font_size: 24, font_family: DEFAULT_FAMILY },
                initOpacity: 1,
                imgPath: '',
                dimensions: [640, 480],
                baseUnit: 'px',
                show_outside_canvas: false,
                selectNew: true,
                gridSnapping: false
            });
        } catch (e) {
            host.remove();
            throw e;
        }
        // no fade-in of new shapes: Firefox reports every frame of an SMIL opacity animation as an inline style
        canvas.getOpacAni = function () { return {}; };

        function selected() {
            return canvas.getSelectedElements().filter(Boolean);
        }

        function mode() {
            return canvas.getMode();
        }

        /** The elements a style attribute goes to: what svgcanvas' own setters change as well */
        function targets(attr) {
            var out = [];
            selected().forEach(function (elem) {
                if (attr !== 'opacity' && elem.tagName === 'g') {
                    Array.prototype.forEach.call(elem.querySelectorAll('*'), function (child) {
                        if (!/^(g|title|desc)$/.test(child.tagName)) { out.push(child); }
                    });
                } else {
                    out.push(elem);
                }
            });
            if (attr === 'fill') { out = out.filter(function (e) { return !/^(line|polyline)$/.test(e.tagName); }); }
            if (attr === 'font-size') { out = out.filter(function (e) { return e.tagName === 'text'; }); }
            return out;
        }

        // --- view ------------------------------------------------------------------------------------------------

        /** Sizes the drawing area to the stage and the zoomed content, and centres it */
        function layout(keepScroll) {
            var zoom = canvas.getZoom();
            var stageW = stage.clientWidth;
            var stageH = stage.clientHeight;
            var w = Math.max(stageW, Math.ceil(canvas.contentW * zoom) + 2 * MARGIN);
            var h = Math.max(stageH, Math.ceil(canvas.contentH * zoom) + 2 * MARGIN);
            host.style.width = w + 'px';
            host.style.height = h + 'px';
            canvas.updateCanvas(w, h);
            selected().forEach(function (elem) { canvas.selectorManager.requestSelector(elem).resize(); });
            if (!keepScroll) {
                stage.scrollLeft = (w - stageW) / 2;
                stage.scrollTop = (h - stageH) / 2;
            }
        }

        function setZoom(zoom, quiet) {
            zoom = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, zoom));
            if (!isFinite(zoom)) { return; }
            canvas.setCurrentZoom(zoom);
            layout(false);
            canvas.pathActions.zoomChange();
            if (!quiet) { ctx.announce(t('svg_zoom', Math.round(zoom * 100))); }
        }

        function fitZoom() {
            var w = stage.clientWidth - 2 * MARGIN;
            var h = stage.clientHeight - 2 * MARGIN;
            return Math.min(w / canvas.contentW, h / canvas.contentH);
        }

        /** The middle of the visible part of the drawing, in user units */
        function viewCentre() {
            var zoom = canvas.getZoom();
            var content = canvas.getSvgContent();
            var x = (stage.scrollLeft + stage.clientWidth / 2 - Number(content.getAttribute('x'))) / zoom;
            var y = (stage.scrollTop + stage.clientHeight / 2 - Number(content.getAttribute('y'))) / zoom;
            return {
                x: Math.round(Math.min(Math.max(x, 0), canvas.contentW)),
                y: Math.round(Math.min(Math.max(y, 0), canvas.contentH))
            };
        }

        // --- state of toolbar and panel ----------------------------------------------------------------------------

        function refresh() {
            var hasSelection = selected().length > 0;
            ['delete', 'duplicate', 'front', 'back'].forEach(function (id) { disable(buttons[id], !hasSelection); });
            disable(buttons.undo, canvas.undoMgr.getUndoStackSize() === 0);
            disable(buttons.redo, canvas.undoMgr.getRedoStackSize() === 0);
            var current = { textedit: 'select', pathedit: 'select', multiselect: 'select', resize: 'select', rotate: 'select' }[mode()] || mode();
            if (tools[current] && tools[current].getAttribute('aria-pressed') !== 'true') { ctx.press(tools[current]); }
            host.setAttribute('data-mode', mode());
            // nothing to save once everything is undone
            ctx.setDirty(canvas.undoMgr.getUndoStackSize() > 0);
        }

        /** Disables a tool button; when it had the focus, the focus goes to the drawing */
        function disable(button, off) {
            if (button.disabled === off) { return; }
            var hadFocus = document.activeElement === button;
            button.disabled = off;
            if (off && button.tabIndex === 0) {
                button.tabIndex = -1;
                tools.select.tabIndex = 0;
            }
            if (hadFocus) { stage.focus(); }
        }

        function kindName(elem) {
            var names = { rect: 'svg_rect', ellipse: 'svg_ellipse', circle: 'svg_ellipse', line: 'svg_line', path: 'svg_path',
                polyline: 'svg_path', polygon: 'svg_path', text: 'svg_text' };
            return names[elem.tagName] ? t(names[elem.tagName]) : t('svg_object');
        }

        var fields = {
            fill: ctx.field({ label: t('svg_fill'), type: 'color', value: '#2563eb',
                onInput: function (v) { preview('fill', v); }, onChange: function (v) { commit('fill', v); } }),
            noFill: ctx.field({ label: t('svg_no_fill'), type: 'checkbox', value: false,
                onChange: function (none) { commit('fill', none ? 'none' : input(fields.fill).value); } }),
            stroke: ctx.field({ label: t('svg_stroke'), type: 'color', value: '#1d2129',
                onInput: function (v) { preview('stroke', v); }, onChange: function (v) { commit('stroke', v); } }),
            strokeWidth: ctx.field({ label: t('svg_stroke_width'), type: 'number', value: 2, min: 0, max: 200, step: 1,
                onChange: function (v) { commit('stroke-width', v); } }),
            opacity: ctx.field({ label: t('svg_opacity'), type: 'range', value: 100, min: 0, max: 100, step: 1,
                onInput: function (v) { preview('opacity', v / 100); }, onChange: function (v) { commit('opacity', v / 100); } }),
            fontSize: ctx.field({ label: t('svg_font_size'), type: 'number', value: 24, min: 4, max: 400, step: 1,
                onChange: function (v) { commit('font-size', v); } }),
            font: ctx.fontField({ label: t('font'), value: '', onChange: function (v) {
                canvas.setFontFamily(v || DEFAULT_FAMILY);
                refresh();
            } }),
            text: ctx.field({ label: t('svg_text_content'), type: 'text', value: '', onInput: typeText })
        };
        var nothingSelected = ctx.el('p', { text: t('svg_nothing_selected') });
        var textInput = input(fields.text);
        textInput.setAttribute('autocomplete', 'off');
        canvas.textActions.setInputElem(textInput);

        /** Shows a value while a colour picker or slider moves, without an undo step */
        function preview(attr, value) {
            var elems = targets(attr);
            if (!elems.length) { return; }
            if (!snapshot || snapshot.attr !== attr) {
                snapshot = { attr: attr, values: elems.map(function (e) { return [e, e.getAttribute(attr)]; }) };
            }
            canvas.changeSelectedAttributeNoUndo(attr, value, elems);
        }

        /** Applies a value with svgcanvas' setters: one undo step, and the default for new shapes */
        function commit(attr, value) {
            if (snapshot) {
                snapshot.values.forEach(function (pair) {
                    if (pair[1] === null) { pair[0].removeAttribute(attr); } else { pair[0].setAttribute(attr, pair[1]); }
                });
                snapshot = null;
            }
            if (attr === 'fill' || attr === 'stroke') {
                canvas.setColor(attr, value);
                if (attr === 'fill' && value !== 'none') { canvas.setCurText('fill', value); }
                if (attr === 'fill') { input(fields.fill).disabled = value === 'none'; }
            } else if (attr === 'stroke-width') {
                value = Math.max(0, Number(value) || 0);
                // a width on an element without a stroke would not show
                if (value > 0 && targets('stroke').some(function (e) { return !e.getAttribute('stroke') || e.getAttribute('stroke') === 'none'; })) {
                    canvas.setColor('stroke', input(fields.stroke).value);
                }
                canvas.setStrokeWidth(value);
            } else if (attr === 'opacity') {
                canvas.setOpacity(value);
            } else if (attr === 'font-size') {
                if (Number(value) > 0) { canvas.setFontSize(Number(value)); }
            }
            refresh();
        }

        /** The value of the font choice for a font-family: a configured family, or '' (the system font) */
        function familyValue(value) {
            var name = firstFamily(value).toLowerCase();
            var font = ctx.fonts.filter(function (f) { return f.family.toLowerCase() === name; })[0];
            return font ? font.family : '';
        }

        function selectedText() {
            var list = selected();
            return list.length === 1 && list[0].tagName === 'text' ? list[0] : null;
        }

        function typeText(value) {
            var text = selectedText();
            if (!text) { return; }
            if (mode() !== 'textedit') { canvas.textActions.start(text); }
            canvas.setTextContent(value);
            refresh();
        }

        /** The panel: the fields that fit the selection, or the current tool when nothing is selected */
        function updatePanel() {
            var list = selected();
            var first = list[0] || null;
            var m = mode();
            var isText = function (e) { return e.tagName === 'text'; };
            var lineOnly = list.length ? list.every(function (e) { return /^(line|polyline)$/.test(e.tagName); }) : m === 'line';
            var showFont = list.length ? list.some(isText) : (m === 'text' || m === 'textedit');
            var showText = list.length ? !!selectedText() : (m === 'text' || m === 'textedit');
            var shape = canvas.getCurShape();
            var fill = first ? first.getAttribute('fill') : (m === 'text' ? canvas.getCurText('fill') : shape.fill);
            var stroke = first ? first.getAttribute('stroke') : shape.stroke;
            var width = first ? first.getAttribute('stroke-width') : shape.stroke_width;
            var opacity = first ? first.getAttribute('opacity') : shape.opacity;

            setField(fields.fill, toHex(fill === null && first ? '#000000' : fill, input(fields.fill).value));
            setField(fields.noFill, fill === 'none' || fill === '');
            input(fields.fill).disabled = fill === 'none' || fill === '';
            setField(fields.stroke, toHex(stroke, input(fields.stroke).value));
            setField(fields.strokeWidth, !stroke || stroke === 'none' ? 0 : (width === null ? 1 : Number(width)));
            setField(fields.opacity, Math.round((opacity === null || opacity === undefined || opacity === '' ? 1 : Number(opacity)) * 100));
            setField(fields.fontSize, Number((first && isText(first) ? first.getAttribute('font-size') : canvas.getCurText('font_size')) || 24));
            setField(fields.text, showText && first ? first.textContent : '');
            if (fields.font) {
                ctx.setChoice(fields.font, familyValue(first && isText(first) ? first.getAttribute('font-family') : canvas.getCurText('font_family')));
            }

            var nodes = list.length ? [] : [nothingSelected];
            if (!lineOnly) { nodes.push(fields.fill, fields.noFill); }
            nodes.push(fields.stroke, fields.strokeWidth, fields.opacity);
            if (showFont) { nodes.push(fields.fontSize); }
            if (showFont && fields.font) { nodes.push(fields.font); }
            if (showText) { nodes.push(fields.text); }
            // rebuilding the panel would take the focus from a field the user is in
            var key = nodes.map(function (n) { return Object.keys(fields).filter(function (k) { return fields[k] === n; })[0] || 'p'; }).join();
            if (key !== panelKey) {
                panelKey = key;
                ctx.panel(t('svg_group_style'), nodes);
            }
        }

        // --- actions ---------------------------------------------------------------------------------------------

        function setTool(name) {
            canvas.setMode(name);
            if (name !== 'select') { canvas.clearSelection(); }
            ctx.press(tools[name]);
            refresh();
            updatePanel();
        }

        function act(fn) {
            return function () {
                if (!selected().length) { return; }
                fn();
                refresh();
                updatePanel();
            };
        }

        function history(redo) {
            var manager = canvas.undoMgr;
            var text = redo ? manager.getNextRedoCommandText() : manager.getNextUndoCommandText();
            if (redo ? !manager.getRedoStackSize() : !manager.getUndoStackSize()) { return; }
            canvas.textActions.clear();
            if (redo) { manager.redo(); } else { manager.undo(); }
            refresh();
            updatePanel();
            ctx.announce(t(redo ? 'redone' : 'undone', text || t(redo ? 'redo' : 'undo')));
        }

        /** Enter on the drawing: a shape of the current tool in the middle of the view */
        function insertShape() {
            var m = mode();
            var c = viewCentre();
            var json = {
                rect: { element: 'rect', attr: { x: c.x - 60, y: c.y - 40, width: 120, height: 80 } },
                ellipse: { element: 'ellipse', attr: { cx: c.x, cy: c.y, rx: 60, ry: 40 } },
                line: { element: 'line', attr: { x1: c.x - 60, y1: c.y, x2: c.x + 60, y2: c.y } },
                text: { element: 'text', attr: {
                    x: c.x, y: c.y, fill: canvas.getCurText('fill'), 'stroke-width': canvas.getCurText('stroke_width'),
                    'font-size': canvas.getCurText('font_size'), 'font-family': canvas.getCurText('font_family'),
                    'text-anchor': 'middle', 'xml:space': 'preserve' } }
            }[m];
            if (!json) { return false; }
            json.curStyles = true;
            json.attr.id = canvas.getNextId();
            var elem = canvas.addSVGElementsFromJson(json);
            elem.setAttribute('opacity', canvas.getCurShape().opacity);
            canvas.addCommandToHistory(new canvas.history.InsertElementCommand(elem));
            if (m === 'text') {
                canvas.selectOnly([elem]);
                canvas.textActions.start(elem);
            } else {
                canvas.selectOnly([elem], true);
                canvas.setMode('select');
            }
            canvas.call('changed', [elem]);
            refresh();
            updatePanel();
            return true;
        }

        function nudge(event) {
            var step = event.shiftKey ? 10 : 1;
            var d = { ArrowLeft: [-step, 0], ArrowRight: [step, 0], ArrowUp: [0, -step], ArrowDown: [0, step] }[event.key];
            if (!d || event.ctrlKey || event.metaKey || event.altKey || !selected().length || mode() !== 'select') { return false; }
            var zoom = canvas.getZoom();
            canvas.moveSelectedElements(d[0] * zoom, d[1] * zoom, true);
            refresh();
            return true;
        }

        // --- toolbar ---------------------------------------------------------------------------------------------

        var draw = ctx.group(t('svg_group_draw'));
        [
            ['select', 'cursor', 'svg_select', 'v'],
            ['rect', 'square', 'svg_rect', 'r'],
            ['ellipse', 'circle', 'svg_ellipse', 'e'],
            ['line', 'slash-lg', 'svg_line', 'l'],
            ['path', 'bezier2', 'svg_path', 'p'],
            ['fhpath', 'pencil', 'svg_freehand', 'f'],
            ['text', 'fonts', 'svg_text', 't']
        ].forEach(function (tool) {
            tools[tool[0]] = ctx.button(draw, {
                id: tool[0], icon: tool[1], label: t(tool[2]), shortcut: tool[3], pressed: tool[0] === 'select',
                onClick: function () { setTool(tool[0]); }
            });
        });

        var arrange = ctx.group(t('svg_group_arrange'));
        buttons['delete'] = ctx.button(arrange, { id: 'delete', icon: 'trash', label: t('svg_delete'), shortcut: 'Delete',
            onClick: act(function () { canvas.deleteSelectedElements(); }) });
        ctx.shortcut('Backspace', function () { buttons['delete'].click(); });
        buttons.duplicate = ctx.button(arrange, { id: 'duplicate', icon: 'files', label: t('svg_duplicate'), shortcut: 'mod+d',
            onClick: act(function () { canvas.cloneSelectedElements(20, 20); }) });
        buttons.front = ctx.button(arrange, { id: 'front', icon: 'front', label: t('svg_front'),
            onClick: act(function () { canvas.moveToTopSelectedElement(); }) });
        buttons.back = ctx.button(arrange, { id: 'back', icon: 'back', label: t('svg_back'),
            onClick: act(function () { canvas.moveToBottomSelectedElement(); }) });

        var historyGroup = ctx.group(t('group_history'));
        buttons.undo = ctx.button(historyGroup, { id: 'undo', icon: 'arrow-90deg-left', label: t('undo'), shortcut: 'mod+z',
            onClick: function () { history(false); } });
        buttons.redo = ctx.button(historyGroup, { id: 'redo', icon: 'arrow-90deg-right', label: t('redo'), shortcut: 'mod+shift+z',
            onClick: function () { history(true); } });
        ctx.shortcut('mod+y', function () { buttons.redo.click(); });

        var view = ctx.group(t('group_view'));
        ctx.button(view, { id: 'zoom-in', icon: 'zoom-in', label: t('zoom_in'), shortcut: '+',
            onClick: function () { setZoom(canvas.getZoom() * ZOOM_STEP); } });
        ctx.shortcut('=', function () { setZoom(canvas.getZoom() * ZOOM_STEP); });
        ctx.button(view, { id: 'zoom-out', icon: 'zoom-out', label: t('zoom_out'), shortcut: '-',
            onClick: function () { setZoom(canvas.getZoom() / ZOOM_STEP); } });
        ctx.button(view, { id: 'zoom-fit', icon: 'arrows-angle-expand', label: t('zoom_fit'), shortcut: '0',
            onClick: function () { setZoom(fitZoom()); } });

        // --- events ----------------------------------------------------------------------------------------------

        var lastSelection = '';
        canvas.bind('selected', function () {
            refresh();
            updatePanel();
            var list = selected();
            var key = list.map(function (e) { return e.id; }).join();
            if (key === lastSelection || mode() === 'textedit') { return; }
            lastSelection = key;
            if (list.length) {
                ctx.announce(t('svg_selected', list.length === 1 ? kindName(list[0]) : t('svg_objects', list.length)));
            }
        });
        canvas.bind('changed', function () { refresh(); });
        canvas.bind('zoomed', function () { /* zoom is ours: the buttons, the keys and Ctrl+wheel */ });

        function onStageKey(event) {
            if (event.target !== stage) { return; }
            var handled = false;
            if (event.key === 'Enter' && !event.ctrlKey && !event.metaKey && !event.altKey) {
                handled = insertShape();
            } else if (event.key === 'Escape' && mode() !== 'select') {
                setTool('select');
                handled = true;
            } else if (event.key === 'Escape' && selected().length) {
                canvas.clearSelection();
                handled = true;
            } else if ((event.key === 'n' || event.key === 'N') && !event.ctrlKey && !event.metaKey && !event.altKey) {
                if (mode() !== 'select') { setTool('select'); }
                canvas.cycleElement(!event.shiftKey);
                handled = true;
            } else {
                handled = nudge(event);
            }
            if (handled) { event.preventDefault(); event.stopPropagation(); }
        }

        function onTextKey(event) {
            if ((event.key === 'Escape' || event.key === 'Enter') && mode() === 'textedit') {
                event.preventDefault();
                event.stopPropagation();
                canvas.textActions.toSelectMode(true);
                stage.focus();
                refresh();
                updatePanel();
            }
        }

        /** svgcanvas prevents the default of mousedown, which keeps the focus off the stage */
        function onPointerDown() {
            if (document.activeElement !== stage && mode() !== 'textedit') { stage.focus({ preventScroll: true }); }
        }

        /** Shift+wheel is svgcanvas' zoom, built for SVG-Edit's page; Ctrl+wheel zooms here */
        function onWheel(event) {
            if (event.type === 'wheel' && (event.ctrlKey || event.metaKey)) {
                event.preventDefault();
                setZoom(canvas.getZoom() * (event.deltaY < 0 ? ZOOM_STEP : 1 / ZOOM_STEP), true);
            }
            if (event.type !== 'wheel' && event.shiftKey) { event.stopPropagation(); }
        }

        stage.addEventListener('keydown', onStageKey);
        stage.addEventListener('mousedown', onPointerDown, true);
        ['wheel', 'mousewheel', 'DOMMouseScroll'].forEach(function (type) {
            stage.addEventListener(type, onWheel, { capture: true, passive: false });
        });
        textInput.addEventListener('keydown', onTextKey);
        if (window.ResizeObserver) {
            observer = new window.ResizeObserver(function () { layout(true); });
        }

        // --- load and save ---------------------------------------------------------------------------------------

        function destroy() {
            if (observer) { observer.disconnect(); }
            stage.removeEventListener('keydown', onStageKey);
            stage.removeEventListener('mousedown', onPointerDown, true);
            ['wheel', 'mousewheel', 'DOMMouseScroll'].forEach(function (type) {
                stage.removeEventListener(type, onWheel, { capture: true });
            });
            try { canvas.textActions.clear(); } catch (e) { /* the drawing goes anyway */ }
            if (sheet) {
                document.adoptedStyleSheets = document.adoptedStyleSheets.filter(function (other) { return other !== sheet; });
            }
            host.remove();
        }

        /** The drawing as SVG text, with the root's size and viewBox of the stored file */
        function serialise() {
            if (mode() === 'textedit') { canvas.textActions.toSelectMode(); }
            canvas.pathActions.clear(true);
            var svg = canvas.getSvgString();
            if (!hadLayers) { svg = unwrapLayers(svg); }
            // svgcanvas leaves identity matrices behind on moved text
            svg = svg.replace(/\s+transform="(?:\s*matrix\(1[ ,]+0[ ,]+0[ ,]+1[ ,]+0[ ,]+0\))+\s*"/g, '');
            if (rootSize && rootSize.viewBox) {
                svg = svg.replace(ROOT_TAG, function (tag, attrs) {
                    attrs = attrs.replace(SIZE_ATTRS, '');
                    ['width', 'height', 'viewBox'].forEach(function (name) {
                        if (rootSize[name]) { attrs = ' ' + name + '=' + rootSize[name] + attrs; }
                    });
                    return '<svg' + attrs + '>';
                });
            }
            if (styleBlocks.length) {
                svg = svg.replace(ROOT_TAG, function (tag) { return tag + '\n ' + styleBlocks.join('\n ') + '\n'; });
            }
            return svg;
        }

        // --- text to paths ---------------------------------------------------------------------------------------

        /** lower-case family => the file opentype.js reads its outlines from (not .woff2) */
        var outlines = {};
        ctx.fonts.forEach(function (font) {
            var url = font.outline || (/\.woff2(?:[?#]|$)/i.test(font.url) ? '' : font.url);
            if (url) { outlines[font.family.toLowerCase()] = url; }
        });
        var toPaths = null;
        if (Object.keys(outlines).length) {
            toPaths = ctx.foot(ctx.field({ type: 'checkbox', label: t('svg_text_to_paths'), value: ctx.conf.textToPaths !== false }));
        }
        var outlineFonts = {};

        /** The parsed font of an outline file, read once (again after a failure) */
        function outlineFont(url) {
            if (!outlineFonts[url]) {
                outlineFonts[url] = window.fetch(url, { credentials: 'same-origin' }).then(function (response) {
                    if (!response.ok) { throw new Error(String(response.status)); }
                    return response.arrayBuffer();
                }).then(function (buffer) {
                    return window.opentype.parse(buffer);
                });
                outlineFonts[url].catch(function () { delete outlineFonts[url]; });
            }
            return outlineFonts[url];
        }

        /**
         * The texts of the drawing that are in configured fonts throughout: [ { elem, runs: [ { owner (the element
         * of the characters), url, size, start (index of the first character), text } ] } ]
         */
        function convertibleTexts() {
            var out = [];
            Array.prototype.forEach.call(canvas.getSvgContent().querySelectorAll('text'), function (text) {
                var content = text.textContent;
                if (!content.trim() || text.closest('defs, symbol, clipPath, mask, pattern, marker') || text.querySelector('textPath') ||
                    text.getNumberOfChars() !== content.length) { return; }
                var runs = [];
                var index = 0;
                var walker = document.createTreeWalker(text, window.NodeFilter.SHOW_TEXT);
                for (var node = walker.nextNode(); node; node = walker.nextNode()) {
                    var owner = node.parentNode;
                    var style = window.getComputedStyle(owner);
                    var url = outlines[firstFamily(style.fontFamily).toLowerCase()];
                    if (!url) { return; }
                    var run = runs[runs.length - 1];
                    if (!run || run.owner !== owner) {
                        run = { owner: owner, url: url, size: parseFloat(style.fontSize) || 16, start: index, text: '' };
                        runs.push(run);
                    }
                    run.text += node.data;
                    index += node.data.length;
                }
                out.push({ elem: text, runs: runs });
            });
            return out;
        }

        /** The markup that replaces a text: a <g> with the words for assistive technology, a <path> per run; null keeps the text */
        function pathMarkup(item, fonts) {
            var text = item.elem;
            var paths = [];
            for (var r = 0; r < item.runs.length; r++) {
                var run = item.runs[r], font = fonts[run.url], data = [];
                if (!font) { return null; }
                for (var i = 0; i < run.text.length; i++) {
                    var code = run.text.codePointAt(i), ch = String.fromCodePoint(code), at = run.start + i;
                    if (code > 0xffff) { i++; }
                    if (/\s/.test(ch)) { continue; }
                    if (!font.charToGlyphIndex(ch)) { return null; }
                    var p = text.getStartPositionOfChar(at);
                    var path = font.charToGlyph(ch).getPath(p.x, p.y, run.size, { hinting: false }, font);
                    var angle = text.getRotationOfChar(at);
                    if (angle) { rotatePath(path, p.x, p.y, angle); }
                    data.push(path.toPathData({ decimalPlaces: 2, flipY: false }));
                }
                if (data.join('')) {
                    paths.push('<path' + (run.owner !== text ? attributesXml(run.owner) : '') + ' d="' + data.join('') + '"/>');
                }
            }
            var words = escapeXml(text.textContent.replace(/\s+/g, ' ').trim());
            return '<g' + attributesXml(text) + ' role="img" aria-label="' + words + '"><title>' + words + '</title>' + paths.join('') + '</g>';
        }

        /** The SVG to save: the texts in configured fonts as paths when the option is ticked */
        function getBlob() {
            var blob = function (svg) { return new window.Blob([svg], { type: 'image/svg+xml' }); };
            if (mode() === 'textedit') { canvas.textActions.toSelectMode(); }
            var items = toPaths && input(toPaths).checked ? convertibleTexts() : [];
            if (!items.length) { return Promise.resolve(blob(serialise())); }
            var urls = items.reduce(function (all, item) {
                item.runs.forEach(function (run) { if (all.indexOf(run.url) === -1) { all.push(run.url); } });
                return all;
            }, []);
            return ctx.scripts('opentype').then(function () {
                return Promise.all(urls.map(outlineFont));
            }).then(function (parsed) {
                var fonts = {};
                urls.forEach(function (url, i) { fonts[url] = parsed[i]; });
                items = convertibleTexts();
                var markups = items.map(function (item, k) {
                    var markup = null;
                    try { markup = pathMarkup(item, fonts); } catch (e) { markup = null; }
                    item.elem.setAttribute('data-expimageeditor-paths', String(k));
                    return markup;
                });
                var svg;
                try {
                    svg = serialise();
                } finally {
                    items.forEach(function (item) { item.elem.removeAttribute('data-expimageeditor-paths'); });
                }
                svg = svg.replace(/<text\b[^>]*?\sdata-expimageeditor-paths="(\d+)"[^>]*>[\s\S]*?<\/text>/g, function (all, k) {
                    return markups[Number(k)] || all.replace(/\sdata-expimageeditor-paths="\d+"/, '');
                });
                return blob(svg);
            }, function (error) {
                throw new Error(t('svg_paths_failed', error && error.message ? error.message : ''));
            });
        }

        return window.fetch(ctx.src, { credentials: 'same-origin' }).then(function (response) {
            if (!response.ok) { throw new Error(String(response.status)); }
            return response.text();
        }).then(function (text) {
            // read as text: a DOMParser document has the page's CSP, and would report every style="" and <style>
            hadLayers = /<g\b(?:[^>"']|"[^"]*"|'[^']*')*\bclass\s*=\s*["'][^"']*\blayer\b/.test(text);
            rootSize = rootAttributes(text);
            // <style> elements stay out of the drawing: shown through a constructed sheet, put back on save
            text = presentationAttributes(text.replace(STYLE_ELEMENT, function (block) {
                if (block.charAt(1) === '!') { return block; }
                styleBlocks.push(block);
                return '';
            }));
            if (!canvas.setSvgString(text, true)) { throw new Error('setSvgString'); }
            sheet = drawingSheet(styleBlocks, '.expimageeditor-svg-host #svgcontent');
            canvas.undoMgr.resetUndoStack();
            setZoom(Math.min(1, fitZoom()), true);
            if (observer) { observer.observe(stage); }
            refresh();
            updatePanel();
            return { getBlob: getBlob, destroy: destroy };
        }, function () {
            throw new Error('fetch');
        }).catch(function () {
            destroy();
            ctx.announce(t('image_failed'), true);
            throw new Error(t('image_failed'));
        });
    }

    window.ExpImageEditor.register('svg', create);
}(window, document));
