/** Shop: catalogue, product with add to basket, basket with remove. Services: children, productGet, basketGet, basketAdd, basketRemove. */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, el = Exp.util.el, ui = Exp.ui;

    function productCard(n) {
        n = Exp.util.node(n);
        return el('li', { 'class': 'card' },
            n.image ? el('img', { src: n.image, alt: '', loading: 'lazy' }) : null,
            el('h3', null, el('a', { href: '#/shop/' + n.id, text: n.name })),
            el('p', { 'class': 'price', text: Exp.util.money(n.price) }));
    }
    Exp.productCard = productCard;

    Exp.refreshBasketCount = function () {
        return Exp.api.call('basketGet', []).then(function (env) {
            var d = env.data || {}, items = d.items || d.products || [];
            $('#basket-count').text(d.count !== undefined ? d.count : items.length);
            d.total = d.total !== undefined ? d.total : (d.totals && (d.totals.total_inc_vat || d.totals.total));
            return d;
        }, function () { $('#basket-count').text('0'); });
    };

    function catalogue(ctx) {
        var off = +ctx.query.offset || 0, lim = Exp.config.pageSize, $v = ctx.$view;
        ui.title('Shop'); $v.empty().append(el('h1', { text: 'Shop' }));
        var $b = el('div'); $v.append($b); ui.loading($b);
        Exp.api.call('products', [Exp.config.nodes.shop, off, lim]).then(function (env) {
            var l = Exp.api.list(env); $b.attr('aria-busy', 'false').empty();
            if (!l.items.length) { return ui.empty($b, 'The catalogue is empty.'); }
            $b.append(el('ul', { 'class': 'grid' }, $.map(l.items, productCard)),
                ui.pager({ total: l.total, offset: off, limit: lim }, function (o) { Exp.router.go('/shop?offset=' + o); }));
        }, function (err) { ui.failure($b, err); });
    }

    function product(ctx) {
        var $v = ctx.$view; $v.empty(); ui.loading($v);
        Exp.api.call('productGet', [ctx.params.id]).then(function (env) {
            var n = Exp.util.node(env.data); ui.title(n.name);
            var $msg = el('p', { role: 'status', 'class': 'meta' }), qty = el('input', { id: 'qty', type: 'number', min: 1, value: 1, style: 'width:5rem' });
            $v.attr('aria-busy', 'false').empty().append(el('article', null,
                el('p', null, el('a', { href: '#/shop', text: 'Catalogue' })), el('h1', { text: n.name }),
                n.image ? el('img', { src: n.image, alt: '' }) : null, el('p', { text: n.summary || n.body || '' }),
                el('p', { 'class': 'price', text: Exp.util.money(n.price) }),
                el('p', null, el('label', { 'for': 'qty', text: 'Quantity ' }), qty, ' ',
                    el('button', { type: 'button', on: { click: function () {
                        var $btn = $(this).prop('disabled', true);
                        Exp.api.call('basketAdd', [], { object_id: n.objectId, quantity: qty.val() }).then(function () {
                            $msg.text('Added to the basket.'); ui.announce('Added to the basket'); Exp.refreshBasketCount();
                        }, function (e) { $msg.text(e.code === 'unavailable' ? 'The basket service is not available yet.' : e.message); })
                        .always(function () { $btn.prop('disabled', false); });
                    } }, text: 'Add to basket' })), $msg));
        }, function (err) { ui.failure($v, err); });
    }

    function basket(ctx) {
        var $v = ctx.$view; ui.title('Basket'); $v.empty().append(el('h1', { text: 'Basket' }));
        var $b = el('div'); $v.append($b); ui.loading($b);
        function load() {
            Exp.refreshBasketCount().then(function (d) {
                var items = (d && (d.items || d.products)) || [];
                $b.attr('aria-busy', 'false').empty();
                if (!d) { return ui.failure($b, { code: 'unavailable', message: 'expbasket::get' }); }
                if (!items.length) { return ui.empty($b, 'Your basket is empty.'); }
                $b.append(el('table', { 'class': 'basket' },
                    el('thead', null, el('tr', null, el('th', { scope: 'col', text: 'Item' }), el('th', { scope: 'col', text: 'Qty' }), el('th', { scope: 'col', text: 'Price' }), el('th', { scope: 'col' }, el('span', { 'class': 'sr', text: 'Actions' })))),
                    el('tbody', null, $.map(items, function (it) {
                        return el('tr', null, el('td', { text: it.name || it.object_name || '' }), el('td', { text: it.quantity || it.item_count || 1 }),
                            el('td', { text: Exp.util.money(it.total || it.price) }),
                            el('td', null, el('button', { type: 'button', 'class': 'secondary', 'aria-label': 'Remove ' + (it.name || ''), on: { click: function () {
                                Exp.api.call('basketRemove', [], { item_id: it.id || it.item_id }).then(load, function (e) { ui.failure($b, e); });
                            } }, text: 'Remove' })));
                    }))), el('p', { 'class': 'price', text: 'Total ' + Exp.util.money(d.total) }));
            });
        }
        load();
    }
    Exp.router.add('/shop', catalogue); Exp.router.add('/shop/:id', product); Exp.router.add('/basket', basket);
}(window, jQuery));
