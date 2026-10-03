/** News: list of articles (children of the news node, paged) and one article. Services: children, view. */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, el = Exp.util.el, ui = Exp.ui;

    function card(n) {
        n = Exp.util.node(n);
        return el('li', { 'class': 'card' },
            n.image ? el('img', { src: n.image, alt: '', loading: 'lazy' }) : null,
            el('h3', null, el('a', { href: '#/news/' + n.id, text: n.name })),
            el('p', { 'class': 'meta', text: Exp.util.date(n.date) }),
            n.summary ? el('p', { text: String(n.summary).slice(0, 220) }) : null);
    }
    Exp.newsCard = card;

    function list(ctx) {
        var off = +ctx.query.offset || 0, lim = Exp.config.pageSize, $v = ctx.$view;
        ui.title('News'); $v.empty().append(el('h1', { text: 'News' }));
        var $body = el('div'); $v.append($body); ui.loading($body);
        Exp.api.call('children', [Exp.config.nodes.news, off, lim]).then(function (env) {
            var l = Exp.api.list(env); $body.attr('aria-busy', 'false').empty();
            if (!l.items.length) { return ui.empty($body, 'No news yet.'); }
            $body.append(el('ul', { 'class': 'grid' }, $.map(l.items, card)),
                ui.pager({ total: l.total, offset: off, limit: lim }, function (o) { Exp.router.go('/news?offset=' + o); }));
            ui.announce(l.total + ' articles');
        }, function (err) { ui.failure($body, err); });
    }

    function article(ctx) {
        var $v = ctx.$view; $v.empty(); ui.loading($v);
        Exp.api.call('view', [ctx.params.id]).then(function (env) {
            var n = Exp.util.node(env.data); ui.title(n.name);
            $v.attr('aria-busy', 'false').empty().append(el('article', null,
                el('p', null, el('a', { href: '#/news', text: 'All news' })),
                el('h1', { text: n.name }), el('p', { 'class': 'meta', text: Exp.util.date(n.date) }),
                n.image ? el('img', { src: n.image, alt: '' }) : null,
                // body arrives as plain text from the service; paragraphs are split here, never injected as HTML
                $.map(String(n.body || n.summary || '').split(/\n{2,}/), function (p) { return p ? el('p', { text: p }) : null; })));
        }, function (err) { ui.failure($v, err); });
    }

    Exp.router.add('/news', list); Exp.router.add('/news/:id', article);
}(window, jQuery));
