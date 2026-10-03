/** Search results (#/search?q=...). Service: search. */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, el = Exp.util.el, ui = Exp.ui;
    Exp.router.add('/search', function (ctx) {
        var q = ctx.query.q || '', off = +ctx.query.offset || 0, lim = Exp.config.pageSize, $v = ctx.$view;
        ui.title('Search'); $('#search-q').val(q); $v.empty().append(el('h1', { text: q ? 'Search: ' + q : 'Search' }));
        if (!q) { return $v.append(ui.state('empty', 'Type something in the search box.')); }
        var $b = el('div'); $v.append($b); ui.loading($b);
        Exp.api.call('search', [q, off, lim]).then(function (env) {
            var l = Exp.api.list(env); $b.attr('aria-busy', 'false').empty();
            if (!l.items.length) { return ui.empty($b, 'Nothing found for ' + q + '.'); }
            $b.append(el('p', { 'class': 'meta', text: l.total + ' results' }), el('ul', { 'class': 'thread' }, $.map(l.items, function (n) { n = Exp.util.node(n);
                return el('li', { 'class': 'card' }, el('h3', null, el('a', { href: '#/news/' + n.id, text: n.name })), el('p', { text: n.summary || '' })); })),
                ui.pager({ total: l.total, offset: off, limit: lim }, function (o) { Exp.router.go('/search?q=' + encodeURIComponent(q) + '&offset=' + o); }));
            ui.announce(l.total + ' results');
        }, function (e) { ui.failure($b, e); });
    });
}(window, jQuery));
