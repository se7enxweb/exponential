/** Home: a portal front page built from the same calls as the sections: latest news, shop, forums, media. */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, el = Exp.util.el, ui = Exp.ui;

    function block(title, link, feature, node, cardFn, $into) {
        var $s = el('section', { 'aria-labelledby': 'h-' + feature }, el('h2', { id: 'h-' + feature }, el('a', { href: '#' + link, text: title }))), $b = el('div');
        $s.append($b); $into.append($s); ui.loading($b);
        Exp.api.call(feature, [node, 0, 4]).then(function (env) {
            var l = Exp.api.list(env); $b.attr('aria-busy', 'false').empty();
            if (!l.items.length) { return ui.empty($b); }
            $b.append(el('ul', { 'class': 'grid' }, $.map(l.items, cardFn)));
        }, function (e) { ui.failure($b, e); });
    }
    Exp.router.add('/', function (ctx) {
        var $v = ctx.$view, c = Exp.config.nodes; ui.title('Home');
        $v.empty().append(el('h1', { text: Exp.config.title }));
        block('Latest news', '/news', 'children', c.news, Exp.newsCard, $v);
        block('From the shop', '/shop', 'products', c.shop, Exp.productCard, $v);
        block('Media', '/media', 'mediaImages', c.media, function (n) { n = Exp.util.node(n); return el('li', { 'class': 'card' }, n.image ? el('img', { src: n.image, alt: n.name, loading: 'lazy' }) : n.name); }, $v);
    });
}(window, jQuery));
