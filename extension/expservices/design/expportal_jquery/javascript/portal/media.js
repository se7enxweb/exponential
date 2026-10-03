/** Media gallery with a lightbox (native <dialog>, closes on Escape, focus returns). Service: mediaImages. */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, el = Exp.util.el, ui = Exp.ui;

    function lightbox(n, opener) {
        var dlg = el('dialog', { 'aria-label': n.name }, el('img', { src: n.full || n.image, alt: n.name }), el('p', { text: n.name }),
            el('form', { method: 'dialog' }, el('button', { text: 'Close' })));
        dlg.on('close', function () { dlg.remove(); if (opener) { opener.focus(); } });
        $('body').append(dlg); if (dlg[0].showModal) { dlg[0].showModal(); } else { dlg.attr('open', ''); }
    }

    function gallery(ctx) {
        var $v = ctx.$view, off = +ctx.query.offset || 0, lim = Exp.config.pageSize * 2;
        ui.title('Media'); $v.empty().append(el('h1', { text: 'Media' }));
        var $b = el('div'); $v.append($b); ui.loading($b);
        Exp.api.call('mediaImages', [Exp.config.nodes.media, off, lim]).then(function (env) {
            var l = Exp.api.list(env); $b.attr('aria-busy', 'false').empty();
            if (!l.items.length) { return ui.empty($b, 'No images yet.'); }
            $b.append(el('ul', { 'class': 'grid gallery' }, $.map(l.items, function (raw) {
                var n = Exp.util.node(raw); n.full = raw.full_url || raw.original_url || n.image;
                return el('li', { 'class': 'card' }, el('button', { type: 'button', 'class': 'thumb', 'aria-label': 'Open ' + n.name, on: { click: function () { lightbox(n, this); } } },
                    el('img', { src: n.image, alt: n.name, loading: 'lazy' })), el('p', { 'class': 'meta', text: n.name }));
            })), ui.pager({ total: l.total, offset: off, limit: lim }, function (o) { Exp.router.go('/media?offset=' + o); }));
        }, function (e) { ui.failure($b, e); });
    }
    Exp.router.add('/media', gallery);
}(window, jQuery));
