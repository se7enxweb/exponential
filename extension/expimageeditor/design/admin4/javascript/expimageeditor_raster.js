/*!
 * expimageeditor: the editor for raster images (JPEG, PNG, WebP, GIF), registered as ExpImageEditor 'raster'.
 *
 * The image is a canvas in the browser; every tool changes that canvas and adds a step to the history (undo/redo).
 * Cropper.js 2 (cropper.min.js, loaded before this file) shows it and draws the selection: crop, and the region the
 * region tools (pixelate, blur, watermark; the watermark text in the system font or one of expimageeditor.ini
 * Fonts[], which the frame has loaded) work on. Brightness and contrast are previewed while the slider moves and
 * become a step with Apply. Save hands the canvas over as a Blob in the image's own type when
 * expimageeditor.ini [EditorSettings] OutputMIMETypes[] has it, otherwise as PNG (a JPEG gets a white background
 * where the image is transparent).
 *
 * Coordinates: the selection lives in the coordinates of <cropper-canvas>, the tools work in image pixels; both are
 * converted through the boxes the browser reports for <cropper-image> and the canvas, so zoom and centring need no
 * matrix of their own.
 *
 * Copyright CJW Network and Exponential Foundation. All rights reserved.
 * GNU General Public License v2.0 (or any later version).
 */
(function (window, document) {
    'use strict';

    /** Steps kept for undo; fewer when the image is large (HISTORY_PIXELS in all) */
    var HISTORY_STEPS = 30;
    var HISTORY_PIXELS = 200 * 1000 * 1000;
    var ZOOM_STEPS = [0.25, 0.5, 0.75, 1, 1.5, 2, 3, 4, 6, 8];

    function newCanvas(width, height) {
        var canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(width));
        canvas.height = Math.max(1, Math.round(height));
        return canvas;
    }

    function copyCanvas(source) {
        var canvas = newCanvas(source.width, source.height);
        canvas.getContext('2d').drawImage(source, 0, 0);
        return canvas;
    }

    function clamp(value, min, max) { return Math.min(max, Math.max(min, value)); }

    function loadImage(src) {
        return new Promise(function (resolve, reject) {
            var image = new window.Image();
            image.decoding = 'async';
            image.addEventListener('load', function () { resolve(image); });
            image.addEventListener('error', reject);
            image.src = src;
        });
    }

    function toBlob(canvas, type, quality) {
        return new Promise(function (resolve, reject) {
            canvas.toBlob(function (blob) { if (blob) { resolve(blob); } else { reject(new Error('toBlob')); } }, type, quality);
        });
    }

    /* ---- pixel operations: each takes the canvas and a region in image pixels, and changes the canvas ---- */

    function eachPixel(canvas, region, change) {
        var context = canvas.getContext('2d');
        var data = context.getImageData(region.x, region.y, region.w, region.h);
        var p = data.data;
        for (var i = 0; i < p.length; i += 4) { change(p, i); }
        context.putImageData(data, region.x, region.y);
    }

    var filters = {
        brightness: function (canvas, region, value) {
            var add = value * 2.55;
            eachPixel(canvas, region, function (p, i) { p[i] += add; p[i + 1] += add; p[i + 2] += add; });
        },
        contrast: function (canvas, region, value) {
            var c = value * 2.55;
            var factor = (259 * (c + 255)) / (255 * (259 - c));
            eachPixel(canvas, region, function (p, i) {
                p[i] = factor * (p[i] - 128) + 128;
                p[i + 1] = factor * (p[i + 1] - 128) + 128;
                p[i + 2] = factor * (p[i + 2] - 128) + 128;
            });
        },
        grayscale: function (canvas, region) {
            eachPixel(canvas, region, function (p, i) {
                var y = 0.2126 * p[i] + 0.7152 * p[i + 1] + 0.0722 * p[i + 2];
                p[i] = p[i + 1] = p[i + 2] = y;
            });
        },
        sepia: function (canvas, region) {
            eachPixel(canvas, region, function (p, i) {
                var r = p[i], g = p[i + 1], b = p[i + 2];
                p[i] = 0.393 * r + 0.769 * g + 0.189 * b;
                p[i + 1] = 0.349 * r + 0.686 * g + 0.168 * b;
                p[i + 2] = 0.272 * r + 0.534 * g + 0.131 * b;
            });
        },
        pixelate: function (canvas, region, size) {
            size = Math.max(2, Math.round(size));
            var context = canvas.getContext('2d');
            var small = newCanvas(Math.ceil(region.w / size), Math.ceil(region.h / size));
            var sc = small.getContext('2d');
            sc.imageSmoothingEnabled = true;
            sc.drawImage(canvas, region.x, region.y, region.w, region.h, 0, 0, small.width, small.height);
            context.save();
            context.imageSmoothingEnabled = false;
            context.clearRect(region.x, region.y, region.w, region.h);
            context.drawImage(small, 0, 0, small.width, small.height, region.x, region.y, small.width * size, small.height * size);
            context.restore();
            // the blocks may reach past the region: cut back to it
            if (small.width * size !== region.w || small.height * size !== region.h) {
                var copy = newCanvas(region.w, region.h);
                copy.getContext('2d').drawImage(canvas, region.x, region.y, region.w, region.h, 0, 0, region.w, region.h);
                context.clearRect(region.x, region.y, small.width * size, small.height * size);
                context.drawImage(copy, region.x, region.y);
            }
        },
        /** Three passes of a box blur, close to a Gaussian blur of the radius */
        blur: function (canvas, region, radius) {
            radius = Math.max(1, Math.round(radius));
            var context = canvas.getContext('2d');
            var data = context.getImageData(region.x, region.y, region.w, region.h);
            var w = region.w, h = region.h, src = data.data, tmp = new Uint8ClampedArray(src.length);
            for (var pass = 0; pass < 3; pass++) {
                boxBlur(src, tmp, w, h, radius, true);
                boxBlur(tmp, src, w, h, radius, false);
            }
            context.putImageData(data, region.x, region.y);
        }
    };

    /** One box blur pass along rows (horizontal) or columns, from src into dst */
    function boxBlur(src, dst, w, h, r, horizontal) {
        var outer = horizontal ? h : w, inner = horizontal ? w : h;
        var step = horizontal ? 4 : w * 4, size = r * 2 + 1;
        for (var o = 0; o < outer; o++) {
            var base = horizontal ? o * w * 4 : o * 4;
            for (var c = 0; c < 4; c++) {
                var sum = 0, k;
                for (k = -r; k <= r; k++) { sum += src[base + clamp(k, 0, inner - 1) * step + c]; }
                for (var i = 0; i < inner; i++) {
                    dst[base + i * step + c] = sum / size;
                    sum += src[base + clamp(i + r + 1, 0, inner - 1) * step + c] - src[base + clamp(i - r, 0, inner - 1) * step + c];
                }
            }
        }
    }

    /* ---- geometry: each returns a new canvas ---- */

    function rotated(canvas, degrees, background) {
        var radians = degrees * Math.PI / 180;
        var sin = Math.abs(Math.sin(radians)), cos = Math.abs(Math.cos(radians));
        // right angles exactly, without a fringe of rounding
        if (degrees % 90 === 0) { sin = Math.round(sin); cos = Math.round(cos); }
        var out = newCanvas(canvas.width * cos + canvas.height * sin, canvas.width * sin + canvas.height * cos);
        var context = out.getContext('2d');
        if (background && degrees % 90 !== 0) {
            context.fillStyle = background;
            context.fillRect(0, 0, out.width, out.height);
        }
        context.translate(out.width / 2, out.height / 2);
        context.rotate(radians);
        context.drawImage(canvas, -canvas.width / 2, -canvas.height / 2);
        return out;
    }

    function flipped(canvas, horizontal) {
        var out = newCanvas(canvas.width, canvas.height);
        var context = out.getContext('2d');
        context.translate(horizontal ? canvas.width : 0, horizontal ? 0 : canvas.height);
        context.scale(horizontal ? -1 : 1, horizontal ? 1 : -1);
        context.drawImage(canvas, 0, 0);
        return out;
    }

    function cropped(canvas, region) {
        var out = newCanvas(region.w, region.h);
        out.getContext('2d').drawImage(canvas, region.x, region.y, region.w, region.h, 0, 0, region.w, region.h);
        return out;
    }

    /** Scaled in halving steps, so that a large reduction stays sharp */
    function resized(canvas, width, height) {
        var current = canvas;
        while (current.width / 2 >= width && current.height / 2 >= height) {
            var half = newCanvas(current.width / 2, current.height / 2);
            var hc = half.getContext('2d');
            hc.imageSmoothingQuality = 'high';
            hc.drawImage(current, 0, 0, half.width, half.height);
            current = half;
        }
        var out = newCanvas(width, height);
        var context = out.getContext('2d');
        context.imageSmoothingQuality = 'high';
        context.drawImage(current, 0, 0, out.width, out.height);
        return out;
    }

    function RasterEditor(ctx) {
        this.ctx = ctx;
        this.t = ctx.t;
        this.history = [];
        this.position = -1;
        this.zoom = 1;
        this.preview = null;
        this.objectURL = null;
        this.tool = null;
        this.watermarkImages = {};
    }

    RasterEditor.prototype.start = function () {
        var self = this, ctx = this.ctx;
        ctx.busy(true, ctx.t('loading'));
        return loadImage(ctx.src).then(function (image) {
            var canvas = newCanvas(image.naturalWidth, image.naturalHeight);
            canvas.getContext('2d').drawImage(image, 0, 0);
            self.push(canvas, '');
            self.buildStage();
            self.buildToolbar();
            return self.show(canvas);
        }, function () {
            throw new Error(ctx.t('image_failed'));
        }).then(function () {
            ctx.busy(false, self.sizeText());
            self.updateButtons();
            return self;
        }, function (error) {
            // a half-built editor (stage, observer, object URL) goes before the frame shows the error
            self.destroy();
            throw error;
        });
    };

    /* ---- history ---- */

    RasterEditor.prototype.current = function () { return this.history[this.position].canvas; };

    RasterEditor.prototype.push = function (canvas, label) {
        this.history = this.history.slice(0, this.position + 1);
        this.history.push({ canvas: canvas, label: label });
        var pixels = 0;
        for (var i = this.history.length - 1; i >= 0; i--) {
            pixels += this.history[i].canvas.width * this.history[i].canvas.height;
            // the first step is the original: it stays as long as it fits
            if ((this.history.length - i > HISTORY_STEPS || pixels > HISTORY_PIXELS) && i > 0) {
                this.history.splice(1, i);
                break;
            }
        }
        this.position = this.history.length - 1;
    };

    /** Makes a canvas the next step and shows it */
    RasterEditor.prototype.commit = function (canvas, label, keepSelection) {
        this.preview = null;
        if (this.effectTimer) { window.clearTimeout(this.effectTimer); this.effectTimer = null; }
        this.push(canvas, label);
        this.ctx.setDirty(this.position > 0);
        if (!keepSelection) { this.clearSelection(); }
        this.updateButtons();
        var self = this;
        return this.show(canvas).then(function () {
            self.ctx.announce(self.t('applied', label) + ' ' + self.sizeText());
            // the panel of the same tool again, without putting its effect over the result at once
            self.justApplied = true;
            self.refreshPanel();
            self.justApplied = false;
        });
    };

    RasterEditor.prototype.undo = function () {
        if (this.position <= 0) { return; }
        var label = this.history[this.position].label;
        this.position--;
        this.afterHistory(this.t('undone', label));
    };

    RasterEditor.prototype.redo = function () {
        if (this.position >= this.history.length - 1) { return; }
        this.position++;
        this.afterHistory(this.t('redone', this.history[this.position].label));
    };

    RasterEditor.prototype.afterHistory = function (text) {
        var self = this;
        this.preview = null;
        this.ctx.setDirty(this.position > 0);
        this.clearSelection();
        this.updateButtons();
        this.show(this.current()).then(function () {
            self.ctx.announce(text + ' ' + self.sizeText());
            self.refreshPanel();
        });
    };

    RasterEditor.prototype.sizeText = function () {
        var canvas = this.current();
        return this.t('image_size', canvas.width, canvas.height);
    };

    /* ---- stage: Cropper.js shows the canvas and the selection ---- */

    RasterEditor.prototype.buildStage = function () {
        var el = this.ctx.el, self = this;
        this.scroller = el('div', { 'class': 'expimageeditor-scroller', tabindex: '-1' });
        this.cropperCanvas = el('cropper-canvas', { background: true });
        this.shade = el('cropper-shade', { hidden: true });
        this.selectHandle = el('cropper-handle', { action: 'select', plain: true });
        this.selection = el('cropper-selection', { movable: true, resizable: true, keyboard: true, outlined: true, x: 0, y: 0, width: 0, height: 0 }, [
            el('cropper-grid', { role: 'grid', bordered: true, covered: true }),
            el('cropper-handle', { action: 'move', 'theme-color': 'rgba(255, 255, 255, 0.35)' })
        ].concat(['n', 'e', 's', 'w', 'ne', 'nw', 'se', 'sw'].map(function (edge) {
            return el('cropper-handle', { action: edge + '-resize' });
        })));
        this.selection.setAttribute('aria-label', this.t('selection_hint'));
        this.cropperCanvas.appendChild(this.shade);
        this.cropperCanvas.appendChild(this.selectHandle);
        this.cropperCanvas.appendChild(this.selection);
        this.scroller.appendChild(this.cropperCanvas);
        this.ctx.stage.appendChild(this.scroller);
        this.selection.addEventListener('change', function () { window.requestAnimationFrame(function () { self.selectionChanged(); }); });
        this.resizeObserver = new window.ResizeObserver(function () { self.layout(); });
        this.resizeObserver.observe(this.ctx.stage);
    };

    /** The size of <cropper-canvas>: the stage, times the zoom */
    RasterEditor.prototype.layout = function () {
        if (!this.cropperCanvas) { return; }
        var region = this.selectedRegion();
        var stage = this.ctx.stage;
        var width = Math.max(100, stage.clientWidth), height = Math.max(100, stage.clientHeight);
        this.cropperCanvas.style.width = Math.round(width * this.zoom) + 'px';
        this.cropperCanvas.style.height = Math.round(height * this.zoom) + 'px';
        var self = this;
        if (this.image) {
            this.image.$ready().then(function () {
                self.image.$center('contain');
                if (region) { self.setSelection(region); }
            });
        }
    };

    /** Shows a canvas (the current step or a preview) */
    RasterEditor.prototype.show = function (canvas) {
        var self = this;
        // toBlob() is asynchronous: of two show() calls in a row only the later one may reach the stage
        var ticket = this.showTicket = (this.showTicket || 0) + 1;
        return toBlob(canvas, 'image/png').then(function (blob) {
            if (ticket !== self.showTicket) { return null; }
            var url = window.URL.createObjectURL(blob);
            var region = self.selectedRegion();
            var image = self.ctx.el('cropper-image', { src: url, alt: self.ctx.name, 'initial-center-size': 'contain' });
            if (self.image) { self.image.replaceWith(image); } else { self.cropperCanvas.insertBefore(image, self.shade); }
            self.image = image;
            if (self.objectURL) { window.URL.revokeObjectURL(self.objectURL); }
            self.objectURL = url;
            self.shownWidth = canvas.width;
            self.shownHeight = canvas.height;
            return image.$ready().then(function () {
                image.$center('contain');
                if (region && canvas.width === self.current().width && canvas.height === self.current().height) {
                    // putting the selection back is no change of the user's: no new preview for it
                    self.restoring = true;
                    self.setSelection(region);
                    window.setTimeout(function () { self.restoring = false; }, 0);
                }
            });
        });
    };

    /** The selection in image pixels, cut to the image, or null when there is none */
    RasterEditor.prototype.selectedRegion = function () {
        if (!this.image || !this.selection || this.selection.width < 2 || this.selection.height < 2) { return null; }
        var box = this.image.getBoundingClientRect(), canvasBox = this.cropperCanvas.getBoundingClientRect();
        if (!box.width || !box.height) { return null; }
        var sx = this.shownWidth / box.width, sy = this.shownHeight / box.height;
        var left = (canvasBox.left + this.selection.x - box.left) * sx;
        var top = (canvasBox.top + this.selection.y - box.top) * sy;
        var right = left + this.selection.width * sx, bottom = top + this.selection.height * sy;
        left = clamp(Math.round(left), 0, this.shownWidth);
        top = clamp(Math.round(top), 0, this.shownHeight);
        right = clamp(Math.round(right), 0, this.shownWidth);
        bottom = clamp(Math.round(bottom), 0, this.shownHeight);
        if (right - left < 1 || bottom - top < 1) { return null; }
        return { x: left, y: top, w: right - left, h: bottom - top };
    };

    /** Draws the selection for a region in image pixels */
    RasterEditor.prototype.setSelection = function (region) {
        if (!this.image) { return; }
        var box = this.image.getBoundingClientRect(), canvasBox = this.cropperCanvas.getBoundingClientRect();
        var sx = box.width / this.shownWidth, sy = box.height / this.shownHeight;
        this.selection.hidden = false;
        this.shade.hidden = false;
        this.selection.$change(box.left - canvasBox.left + region.x * sx, box.top - canvasBox.top + region.y * sy, region.w * sx, region.h * sy);
    };

    RasterEditor.prototype.clearSelection = function () {
        if (this.selection) {
            this.selection.$change(0, 0, 0, 0);
            this.shade.hidden = true;
        }
        this.selectionChanged();
    };

    RasterEditor.prototype.selectionChanged = function () {
        var region = this.selectedRegion();
        if (this.shade) { this.shade.hidden = !region; }
        if (this.selectionFields) {
            var fields = this.selectionFields;
            ['x', 'y', 'w', 'h'].forEach(function (key) {
                if (document.activeElement !== fields[key]) { fields[key].value = region ? region[key] : ''; }
            });
        }
        if (this.cropButton) { this.cropButton.disabled = !region; }
        if (this.regionNote) { this.regionNote.hidden = !!region; }
        // an effect's preview follows the region it works on
        if (this.effectPreview && !this.restoring) {
            var self = this;
            if (this.effectTimer) { window.clearTimeout(this.effectTimer); }
            this.effectTimer = window.setTimeout(function () { self.effectTimer = null; if (self.effectPreview) { self.effectPreview(); } }, 150);
        }
    };

    RasterEditor.prototype.setZoom = function (zoom) {
        this.zoom = clamp(zoom, ZOOM_STEPS[0], ZOOM_STEPS[ZOOM_STEPS.length - 1]);
        this.layout();
        this.updateButtons();
    };

    RasterEditor.prototype.zoomBy = function (direction) {
        var zoom = this.zoom, next = zoom;
        ZOOM_STEPS.forEach(function (step) {
            if (direction > 0 && step > zoom && (next === zoom || step < next)) { next = step; }
            if (direction < 0 && step < zoom && (next === zoom || step > next)) { next = step; }
        });
        this.setZoom(next);
    };

    /* ---- toolbar and panels ---- */

    RasterEditor.prototype.buildToolbar = function () {
        var ctx = this.ctx, t = this.t, self = this;
        var history = ctx.group(t('group_history'));
        this.undoButton = ctx.button(history, { icon: 'arrow-90deg-left', label: t('undo'), shortcut: 'mod+z', onClick: function () { self.undo(); } });
        this.redoButton = ctx.button(history, { icon: 'arrow-90deg-right', label: t('redo'), shortcut: 'mod+shift+z', onClick: function () { self.redo(); } });
        ctx.shortcut('mod+y', function () { self.redo(); });

        var view = ctx.group(t('group_view'));
        this.zoomOutButton = ctx.button(view, { icon: 'zoom-out', label: t('zoom_out'), shortcut: '-', onClick: function () { self.zoomBy(-1); } });
        this.zoomInButton = ctx.button(view, { icon: 'zoom-in', label: t('zoom_in'), shortcut: '+', onClick: function () { self.zoomBy(1); } });
        ctx.button(view, { icon: 'arrows-angle-expand', label: t('zoom_fit'), shortcut: '0', onClick: function () { self.setZoom(1); } });

        var transform = ctx.group(t('group_transform'));
        this.toolButton(transform, 'crop', 'crop', t('crop'), 'c');
        ctx.button(transform, { icon: 'arrow-counterclockwise', label: t('rotate_left'), shortcut: '[', onClick: function () { self.commit(rotated(self.current(), -90), t('rotate_left')); } });
        ctx.button(transform, { icon: 'arrow-clockwise', label: t('rotate_right'), shortcut: ']', onClick: function () { self.commit(rotated(self.current(), 90), t('rotate_right')); } });
        this.toolButton(transform, 'rotate', 'arrow-repeat', t('rotate'), 'a');
        ctx.button(transform, { icon: 'symmetry-vertical', label: t('flip_h'), shortcut: 'h', onClick: function () { self.commit(flipped(self.current(), true), t('flip_h'), false); } });
        ctx.button(transform, { icon: 'symmetry-horizontal', label: t('flip_v'), shortcut: 'v', onClick: function () { self.commit(flipped(self.current(), false), t('flip_v'), false); } });
        this.toolButton(transform, 'resize', 'aspect-ratio', t('resize'), 's');

        var adjust = ctx.group(t('group_adjust'));
        this.toolButton(adjust, 'brightness', 'brightness-high', t('brightness'), 'b');
        this.toolButton(adjust, 'contrast', 'circle-half', t('contrast'), 'k');
        this.toolButton(adjust, 'grayscale', 'droplet-half', t('grayscale'), 'g');
        this.toolButton(adjust, 'sepia', 'sunset', t('sepia'), 'e');

        var region = ctx.group(t('group_region'));
        this.toolButton(region, 'pixelate', 'grid-3x3', t('pixelate'), 'p');
        this.toolButton(region, 'blur', 'eye-slash', t('blur'), 'u');
        this.toolButton(region, 'watermark', 'type', t('watermark'), 'w');

        this.chooseTool('crop', this.toolButtons.crop);
    };

    /** A button that chooses a tool with options in the panel (pressed while chosen) */
    RasterEditor.prototype.toolButton = function (group, id, icon, label, shortcut) {
        var self = this;
        this.toolButtons = this.toolButtons || {};
        this.toolButtons[id] = this.ctx.button(group, {
            id: id, icon: icon, label: label, shortcut: shortcut, pressed: false,
            onClick: function (button) { self.chooseTool(id, button); }
        });
        return this.toolButtons[id];
    };

    RasterEditor.prototype.chooseTool = function (id, button) {
        this.effectPreview = null;
        if (this.effectTimer) { window.clearTimeout(this.effectTimer); this.effectTimer = null; }
        if (this.preview) {
            this.preview = null;
            this.show(this.current());
        }
        this.tool = id;
        this.ctx.press(button);
        this.refreshPanel();
    };

    RasterEditor.prototype.updateButtons = function () {
        if (!this.undoButton) { return; }
        this.undoButton.disabled = this.position <= 0;
        this.redoButton.disabled = this.position >= this.history.length - 1;
        this.zoomOutButton.disabled = this.zoom <= ZOOM_STEPS[0];
        this.zoomInButton.disabled = this.zoom >= ZOOM_STEPS[ZOOM_STEPS.length - 1];
    };

    /** Draws the options of the current tool into the panel */
    RasterEditor.prototype.refreshPanel = function () {
        var ctx = this.ctx, t = this.t, self = this;
        this.selectionFields = null;
        this.cropButton = null;
        this.regionNote = null;
        var build = {
            crop: function () {
                var ratios = [['', t('ratio_free')], [String(self.current().width / self.current().height), t('ratio_original')]]
                    .concat((ctx.conf.ratios || []).map(function (r) { return [String(r.ratio), r.label]; }));
                var fields = {};
                var nodes = [
                    ctx.el('p', { text: t('selection_hint') }),
                    ctx.field({ type: 'radio', label: t('ratio'), value: self.ratio || '', options: ratios, onChange: function (value) { self.setRatio(value); } })
                ];
                ['x', 'y', 'w', 'h'].forEach(function (key) {
                    var label = { x: 'X', y: 'Y', w: t('width'), h: t('height') }[key];
                    var field = ctx.field({ type: 'number', label: label, min: key === 'w' || key === 'h' ? 1 : 0, step: 1, value: '',
                        onChange: function () { self.selectionFromFields(); } });
                    fields[key] = field.querySelector('input');
                    nodes.push(field);
                });
                self.cropButton = ctx.action(t('crop'), function () {
                    var region = self.selectedRegion();
                    if (region) { self.commit(cropped(self.current(), region), t('crop')); }
                }, true);
                nodes.push(ctx.el('div', { 'class': 'expimageeditor-actions' }, [self.cropButton]));
                self.selectionFields = fields;
                return nodes;
            },
            rotate: function () {
                var state = { angle: 0, background: '#ffffff' };
                return [
                    ctx.field({ type: 'range', label: t('angle'), min: -180, max: 180, step: 1, value: 0, onInput: function (v) { state.angle = v; } }),
                    ctx.field({ type: 'color', label: t('background'), value: state.background, onInput: function (v) { state.background = v; } }),
                    ctx.el('div', { 'class': 'expimageeditor-actions' }, [ctx.action(t('apply'), function () {
                        if (state.angle) { self.commit(rotated(self.current(), state.angle, state.background), t('rotate')); }
                    }, true)])
                ];
            },
            resize: function () {
                var canvas = self.current(), ratio = canvas.width / canvas.height;
                var state = { w: canvas.width, h: canvas.height, keep: true };
                var width = ctx.field({ type: 'number', label: t('width'), min: 1, max: 20000, step: 1, value: state.w, onInput: function (v) {
                    state.w = v;
                    if (state.keep && v > 0) { state.h = Math.max(1, Math.round(v / ratio)); height.querySelector('input').value = state.h; }
                } });
                var height = ctx.field({ type: 'number', label: t('height'), min: 1, max: 20000, step: 1, value: state.h, onInput: function (v) {
                    state.h = v;
                    if (state.keep && v > 0) { state.w = Math.max(1, Math.round(v * ratio)); width.querySelector('input').value = state.w; }
                } });
                return [width, height,
                    ctx.field({ type: 'checkbox', label: t('keep_ratio'), value: true, onChange: function (v) { state.keep = v; } }),
                    ctx.el('div', { 'class': 'expimageeditor-actions' }, [ctx.action(t('apply'), function () {
                        var w = Math.round(state.w), h = Math.round(state.h);
                        if (w >= 1 && h >= 1 && w <= 20000 && h <= 20000 && (w !== canvas.width || h !== canvas.height)) {
                            self.commit(resized(self.current(), w, h), t('resize'));
                        }
                    }, true)])
                ];
            },
            brightness: function () { return self.sliderPanel('brightness', t('brightness')); },
            grayscale: function () { return self.effectPanel('grayscale', t('grayscale')); },
            sepia: function () { return self.effectPanel('sepia', t('sepia')); },
            contrast: function () { return self.sliderPanel('contrast', t('contrast')); },
            pixelate: function () { return self.regionPanel('pixelate', t('pixelate'), 4, 64, 12); },
            blur: function () { return self.regionPanel('blur', t('blur'), 1, 40, 8); },
            watermark: function () { return self.watermarkPanel(); }
        }[this.tool];
        var label = this.toolButtons && this.toolButtons[this.tool] ? this.toolButtons[this.tool].getAttribute('aria-label') : '';
        ctx.panel(label, build ? build() : null);
        this.selectionChanged();
    };

    RasterEditor.prototype.setRatio = function (value) {
        this.ratio = value;
        if (value) { this.selection.setAttribute('aspect-ratio', value); } else { this.selection.removeAttribute('aspect-ratio'); }
        var region = this.selectedRegion();
        if (region && value) {
            // the largest region of that shape inside the current one, around its centre
            var ratio = Number(value), w = region.w, h = Math.round(w / ratio);
            if (h > region.h) { h = region.h; w = Math.round(h * ratio); }
            this.setSelection({ x: region.x + Math.round((region.w - w) / 2), y: region.y + Math.round((region.h - h) / 2), w: w, h: h });
        }
    };

    RasterEditor.prototype.selectionFromFields = function () {
        var f = this.selectionFields, canvas = this.current();
        var x = clamp(Number(f.x.value) || 0, 0, canvas.width - 1), y = clamp(Number(f.y.value) || 0, 0, canvas.height - 1);
        var w = clamp(Number(f.w.value) || 0, 0, canvas.width - x), h = clamp(Number(f.h.value) || 0, 0, canvas.height - y);
        if (w >= 1 && h >= 1) { this.setSelection({ x: x, y: y, w: w, h: h }); }
    };

    /** The region a tool works on: the selection, or the whole image */
    RasterEditor.prototype.workRegion = function () {
        var canvas = this.current();
        return this.selectedRegion() || { x: 0, y: 0, w: canvas.width, h: canvas.height };
    };

    RasterEditor.prototype.regionNoteNode = function () {
        this.regionNote = this.ctx.el('p', { text: this.t('whole_image') });
        return this.regionNote;
    };

    RasterEditor.prototype.applyFilter = function (name, label, value) {
        var canvas = copyCanvas(this.current());
        filters[name](canvas, this.workRegion(), value);
        return this.commit(canvas, label, true);
    };

    /**
     * Black and white or sepia: the stage shows the result at once, for the selection or the whole image, and follows
     * a new selection; Apply makes it a step, another tool drops the preview
     */
    RasterEditor.prototype.effectPanel = function (name, label) {
        var ctx = this.ctx, self = this;
        this.effectPreview = function () {
            var canvas = copyCanvas(self.current());
            filters[name](canvas, self.workRegion());
            self.preview = canvas;
            self.show(canvas);
        };
        if (!this.justApplied) { this.effectPreview(); }
        return [ctx.el('p', { text: this.t('effect_hint') }), this.regionNoteNode(), ctx.el('div', { 'class': 'expimageeditor-actions' }, [
            ctx.action(this.t('apply'), function () { self.applyFilter(name, label); }, true)
        ])];
    };

    /** Brightness or contrast: previewed while the slider moves, a step with Apply */
    RasterEditor.prototype.sliderPanel = function (name, label) {
        var ctx = this.ctx, t = this.t, self = this, state = { value: 0 }, timer = null;
        var previewNow = function () {
            timer = null;
            if (!state.value) { self.preview = null; self.show(self.current()); return; }
            var canvas = copyCanvas(self.current());
            filters[name](canvas, self.workRegion(), state.value);
            self.preview = canvas;
            self.show(canvas);
        };
        var slider = ctx.field({ type: 'range', label: label, min: -100, max: 100, step: 1, value: 0, onInput: function (v) {
            state.value = v;
            if (timer) { window.clearTimeout(timer); }
            timer = window.setTimeout(previewNow, 120);
        } });
        return [slider, this.regionNoteNode(), ctx.el('div', { 'class': 'expimageeditor-actions' }, [
            ctx.action(t('apply'), function () {
                if (timer) { window.clearTimeout(timer); timer = null; }
                if (state.value) { self.applyFilter(name, label, state.value); }
            }, true),
            ctx.action(t('reset'), function () {
                state.value = 0;
                var input = slider.querySelector('input');
                input.value = 0;
                input.dispatchEvent(new window.Event('input'));
            })
        ])];
    };

    /** Pixelate or blur the selection (or the whole image) */
    RasterEditor.prototype.regionPanel = function (name, label, min, max, value) {
        var ctx = this.ctx, self = this, state = { value: value };
        return [
            ctx.el('p', { text: this.t('selection_hint') }),
            ctx.field({ type: 'range', label: this.t('strength'), min: min, max: max, step: 1, value: value, onInput: function (v) { state.value = v; } }),
            this.regionNoteNode(),
            ctx.el('div', { 'class': 'expimageeditor-actions' }, [ctx.action(this.t('apply'), function () { self.applyFilter(name, label, state.value); }, true)])
        ];
    };

    RasterEditor.prototype.watermarkPanel = function () {
        var ctx = this.ctx, t = this.t, self = this;
        var marks = ctx.conf.watermarks || [];
        var state = { image: '', text: '', font: '', colour: '#ffffff', opacity: 60 };
        var nodes = [ctx.el('p', { text: t('watermark_hint') })];
        if (marks.length) {
            nodes.push(ctx.field({ type: 'radio', label: t('watermark_image'), value: '',
                options: [['', t('watermark_none')]].concat(marks.map(function (m) { return [m.url, m.label]; })),
                onChange: function (v) { state.image = v; } }));
        }
        nodes.push(ctx.field({ type: 'text', label: t('watermark_text'), value: '', onInput: function (v) { state.text = v; } }));
        // the configured fonts (expimageeditor.ini Fonts[]), loaded by the frame; none without them
        var font = ctx.fontField({ label: t('font'), value: '', onChange: function (v) { state.font = v; } });
        if (font) { nodes.push(font); }
        nodes.push(
            ctx.field({ type: 'color', label: t('colour'), value: state.colour, onInput: function (v) { state.colour = v; } }),
            ctx.field({ type: 'range', label: t('opacity'), min: 5, max: 100, step: 1, value: state.opacity, onInput: function (v) { state.opacity = v; } }),
            ctx.el('div', { 'class': 'expimageeditor-actions' }, [ctx.action(t('apply'), function () { self.applyWatermark(state); }, true)])
        );
        return nodes;
    };

    /** A text or an image, fitted into the selection or the lower right corner */
    RasterEditor.prototype.applyWatermark = function (state) {
        var self = this, canvas = copyCanvas(this.current());
        var region = this.selectedRegion();
        if (!region) {
            var w = Math.round(canvas.width / 4), h = Math.round(canvas.height / 8), margin = Math.round(Math.min(canvas.width, canvas.height) / 40);
            region = { x: canvas.width - w - margin, y: canvas.height - h - margin, w: w, h: h };
        }
        var context = canvas.getContext('2d');
        context.globalAlpha = clamp(state.opacity, 5, 100) / 100;
        var draw = state.image ? (this.watermarkImages[state.image] || (this.watermarkImages[state.image] = loadImage(state.image))) : Promise.resolve(null);
        return draw.then(function (image) {
            if (image) {
                var scale = Math.min(region.w / image.naturalWidth, region.h / image.naturalHeight);
                var iw = image.naturalWidth * scale, ih = image.naturalHeight * scale;
                context.drawImage(image, region.x + (region.w - iw) / 2, region.y + (region.h - ih) / 2, iw, ih);
            } else if (state.text.trim() !== '') {
                // a configured face as it is (one weight), the system font semibold
                var weight = state.font ? '400 ' : '600 ', stack = self.ctx.fontStack(state.font);
                var size = region.h * 0.8;
                context.font = weight + size + 'px ' + stack;
                var width = context.measureText(state.text).width;
                if (width > region.w) { size = size * region.w / width; context.font = weight + size + 'px ' + stack; }
                context.fillStyle = state.colour;
                context.textAlign = 'center';
                context.textBaseline = 'middle';
                context.fillText(state.text, region.x + region.w / 2, region.y + region.h / 2);
            } else {
                return null;
            }
            return self.commit(canvas, self.t('watermark'), true);
        }, function () {
            self.ctx.announce(self.t('image_failed'), true);
        });
    };

    /* ---- what the frame calls ---- */

    RasterEditor.prototype.getBlob = function () {
        var canvas = this.current(), type = this.ctx.outputType();
        var quality = Number(this.ctx.conf.quality) || 0.92;
        if (type === 'image/jpeg') {
            // JPEG has no transparency: white behind it, not black
            var flat = newCanvas(canvas.width, canvas.height), context = flat.getContext('2d');
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, flat.width, flat.height);
            context.drawImage(canvas, 0, 0);
            canvas = flat;
        }
        return toBlob(canvas, type, quality);
    };

    RasterEditor.prototype.destroy = function () {
        if (this.resizeObserver) { this.resizeObserver.disconnect(); }
        if (this.objectURL) { window.URL.revokeObjectURL(this.objectURL); }
        this.history = [];
        this.preview = null;
    };

    window.ExpImageEditor.register('raster', function (ctx) {
        return new RasterEditor(ctx).start();
    });
}(window, document));
