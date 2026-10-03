/** Feeds: the feeds the site publishes and the newest items of one. Services: feedList, feedItems. */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, el = Exp.util.el, ui = Exp.ui;

    function feeds(ctx) {
        var $v = ctx.$view; ui.title('Feeds'); $v.empty().append(el('h1', { text: 'Feeds' }));
        var $b = el('div'), $items = el('div', { 'aria-live': 'polite' }); $v.append($b, $items); ui.loading($b);
        Exp.api.call('feedList', []).then(function (env) {
            var l = Exp.api.list(env); $b.attr('aria-busy', 'false').empty();
            if (!l.items.length) { return ui.empty($b, 'No feeds are published.'); }
            $b.append(el('ul', { 'class': 'grid' }, $.map(l.items, function (f) {
                return el('li', { 'class': 'card' }, el('h3', { text: f.title || f.name || 'Feed' }), el('p', { 'class': 'meta', text: f.description || '' }),
                    el('button', { type: 'button', on: { click: function () { show(f); } }, text: 'Show items' }), ' ',
                    f.url ? el('a', { 'class': 'btn secondary', href: f.url, text: 'Subscribe' }) : null);
            })));
        }, function (e) { ui.failure($b, e); });
        function show(f) {
            ui.loading($items);
            Exp.api.call('feedItems', [f.id, 20]).then(function (env) {
                var l = Exp.api.list(env); $items.attr('aria-busy', 'false').empty().append(el('h2', { text: f.title || 'Items' }));
                if (!l.items.length) { return $items.append(ui.state('empty', 'This feed has no items.')); }
                $items.append(el('ul', { 'class': 'thread' }, $.map(l.items, function (i) {
                    return el('li', { 'class': 'card' }, el('a', { href: i.link || i.url || '#', text: i.title || i.name }), ' ', el('span', { 'class': 'meta', text: Exp.util.date(i.date || i.published) }));
                })));
            }, function (e) { ui.failure($items, e); });
        }
    }
    Exp.router.add('/feeds', feeds);
}(window, jQuery));
