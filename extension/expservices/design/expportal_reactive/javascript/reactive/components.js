/**
 * The React-like model, part 3: components. Each is a function ( props ) -> description built with h().
 * They are pure: same state in, same description out; no service calls, no DOM access (except `ref`), no own state.
 * What a user does becomes dispatch( { type, ... } ); what the page shows is read from state.res[ key ]
 * ( { status: 'loading'|'ok'|'error'|'unavailable', data, error } ) which the effects fill.
 */
(function (window) {
    'use strict';
    var Exp = window.ExpPortal, h = Exp.h, C = Exp.components = {}, U = Exp.util;

    function res(state, key) { return state.res[key] || { status: 'idle' }; }
    C.res = res;
    function items(r) { return r.status === 'ok' ? Exp.api.list(r.data) : { items: [], total: 0, offset: 0, limit: 0 }; }
    function href(p) { return '#' + p; }

    /** Wraps a resource: loading, error, "service not available" and empty states, else the body for its items. */
    C.Resource = function (r, body, emptyText) {
        if (r.status === 'idle' || r.status === 'loading') { return h('p', { 'class': 'state loading', 'aria-busy': 'true' }, 'Loading'); }
        if (r.status === 'unavailable') { return h('p', { 'class': 'state unavailable' }, 'This part of the portal needs a service that is not available yet. (' + r.error.message + ')'); }
        if (r.status === 'error') { return h('p', { 'class': 'state error', role: 'alert' }, r.error.message); }
        var l = items(r);
        if (Array.isArray(r.data.data) || (r.data.data && r.data.data.items) ? !l.items.length : false) { return h('p', { 'class': 'state empty' }, emptyText || 'Nothing here yet.'); }
        return body(r.data, l);
    };

    C.Pager = function (l, offset, limit, to) {
        if (l.total <= limit && offset === 0) { return null; }
        return h('nav', { 'class': 'pager', 'aria-label': 'Pages' },
            h('button', { type: 'button', 'class': 'secondary', disabled: offset <= 0, on: { click: function () { to(Math.max(0, offset - limit)); } } }, 'Previous'),
            h('span', { 'class': 'meta' }, (offset + 1) + ' to ' + Math.min(l.total, offset + limit) + ' of ' + l.total),
            h('button', { type: 'button', 'class': 'secondary', disabled: offset + limit >= l.total, on: { click: function () { to(offset + limit); } } }, 'Next'));
    };

    C.NewsCard = function (raw) {
        var n = U.node(raw);
        return h('li', { 'class': 'card', key: n.id }, n.image ? h('img', { src: n.image, alt: '', loading: 'lazy' }) : null,
            h('h3', null, h('a', { href: href('/news/' + n.id) }, n.name)), h('p', { 'class': 'meta' }, U.date(n.date)), n.summary ? h('p', null, String(n.summary).slice(0, 220)) : null);
    };
    C.ProductCard = function (raw) {
        var n = U.node(raw);
        return h('li', { 'class': 'card', key: n.id }, n.image ? h('img', { src: n.image, alt: '', loading: 'lazy' }) : null,
            h('h3', null, h('a', { href: href('/shop/' + n.id) }, n.name)), h('p', { 'class': 'price' }, U.money(n.price)));
    };
    C.ImageCard = function (raw, dispatch) {
        var n = U.node(raw);
        return h('li', { 'class': 'card', key: n.id || n.name }, h('button', { type: 'button', 'class': 'thumb', 'aria-label': 'Open ' + n.name,
            on: { click: function () { dispatch({ type: 'lightbox/open', image: { name: n.name, src: raw.full_url || raw.original_url || n.image } }); } } },
            h('img', { src: n.image, alt: n.name, loading: 'lazy' })), h('p', { 'class': 'meta' }, n.name));
    };

    C.Nav = function (s) {
        return Exp.menu.map(function (m) { var cur = m[0] === '/' ? s.route.path === '/' : s.route.path.indexOf(m[0]) === 0; return h('a', { key: m[0], href: href(m[0]), 'aria-current': cur ? 'page' : null }, m[1]); });
    };
    C.Account = function (s, dispatch) {
        return s.user ? [h('a', { href: '#/profile' }, s.user.name || s.user.login || 'Profile'), ' ', h('button', { type: 'button', 'class': 'secondary', on: { click: function () { dispatch({ type: 'logout/submit' }); } } }, 'Log out')]
            : h('a', { href: '#/login' }, 'Log in');
    };

    C.Home = function (s) {
        function block(title, link, key, card) { return h('section', { key: key }, h('h2', null, h('a', { href: href(link) }, title)), C.Resource(res(s, key), function (d, l) { return h('ul', { 'class': 'grid' }, l.items.map(card)); })); }
        return [h('h1', null, Exp.config.title), block('Latest news', '/news', 'home:news', C.NewsCard), block('From the shop', '/shop', 'home:shop', C.ProductCard),
            block('Media', '/media', 'home:media', function (n) { n = U.node(n); return h('li', { 'class': 'card', key: n.id || n.name }, n.image ? h('img', { src: n.image, alt: n.name, loading: 'lazy' }) : n.name); })];
    };
    C.NewsList = function (s, dispatch) {
        var off = +s.route.query.offset || 0, lim = Exp.config.pageSize;
        return [h('h1', null, 'News'), C.Resource(res(s, 'news:' + off), function (d, l) { return [h('ul', { 'class': 'grid' }, l.items.map(C.NewsCard)), C.Pager(l, off, lim, function (o) { Exp.router.go('/news?offset=' + o); })]; }, 'No news yet.')];
    };
    C.Article = function (s) {
        return C.Resource(res(s, 'view:' + s.route.params.id), function (d) { var n = U.node(d.data);
            return h('article', null, h('p', null, h('a', { href: '#/news' }, 'All news')), h('h1', null, n.name), h('p', { 'class': 'meta' }, U.date(n.date)), n.image ? h('img', { src: n.image, alt: '' }) : null,
                String(n.body || n.summary || '').split(/\n{2,}/).filter(Boolean).map(function (p, i) { return h('p', { key: i }, p); })); });
    };
    C.Catalogue = function (s) {
        var off = +s.route.query.offset || 0, lim = Exp.config.pageSize;
        return [h('h1', null, 'Shop'), C.Resource(res(s, 'shop:' + off), function (d, l) { return [h('ul', { 'class': 'grid' }, l.items.map(C.ProductCard)), C.Pager(l, off, lim, function (o) { Exp.router.go('/shop?offset=' + o); })]; }, 'The catalogue is empty.')];
    };
    C.Product = function (s, dispatch) {
        var f = s.form['basket-add'] || {};
        return C.Resource(res(s, 'product:' + s.route.params.id), function (d) { var n = U.node(d.data);
            return h('article', null, h('p', null, h('a', { href: '#/shop' }, 'Catalogue')), h('h1', null, n.name), n.image ? h('img', { src: n.image, alt: '' }) : null, h('p', null, n.summary || n.body || ''),
                h('p', { 'class': 'price' }, U.money(n.price)),
                h('form', { on: { submit: function (e) { e.preventDefault(); dispatch({ type: 'basket/add', objectId: n.objectId, quantity: e.target.elements.qty.value }); } } },
                    h('label', { 'for': 'qty' }, 'Quantity '), h('input', { id: 'qty', name: 'qty', type: 'number', min: 1, value: 1, style: 'width:5rem' }), ' ',
                    h('button', { type: 'submit', disabled: f.status === 'loading' }, 'Add to basket')), h('p', { role: 'status', 'class': 'meta' }, f.message || ''));
        });
    };
    C.Basket = function (s, dispatch) {
        var b = s.basket;
        if (b.status === 'unavailable') { return [h('h1', null, 'Basket'), h('p', { 'class': 'state unavailable' }, 'The basket service is not available yet.')]; }
        return [h('h1', null, 'Basket'), !b.items.length ? h('p', { 'class': 'state empty' }, 'Your basket is empty.') : [
            h('table', { 'class': 'basket' }, h('thead', null, h('tr', null, h('th', { scope: 'col' }, 'Item'), h('th', { scope: 'col' }, 'Qty'), h('th', { scope: 'col' }, 'Price'), h('th', { scope: 'col' }, h('span', { 'class': 'sr' }, 'Actions')))),
                h('tbody', null, b.items.map(function (it) { var id = it.id || it.item_id; return h('tr', { key: id }, h('td', null, it.name || it.object_name || ''), h('td', null, it.quantity || it.item_count || 1), h('td', null, U.money(it.total || it.price)),
                    h('td', null, h('button', { type: 'button', 'class': 'secondary', 'aria-label': 'Remove ' + (it.name || ''), on: { click: function () { dispatch({ type: 'basket/remove', itemId: id }); } } }, 'Remove'))); }))),
            h('p', { 'class': 'price' }, 'Total ' + U.money(b.total))]];
    };

    /** A form whose submit dispatches post/submit; the effect reports into state.form[ id ]. */
    C.PostForm = function (s, dispatch, id, label, feature, fixed, reload) {
        var f = s.form[id] || {};
        return h('form', { 'class': 'stack', on: { submit: function (e) { e.preventDefault(); var el = e.target.elements;
            dispatch({ type: 'post/submit', id: id, feature: feature, fields: Object.assign({ title: el.title.value, body: el.body.value }, fixed), reload: reload, reset: e.target }); } } },
            h('h2', null, label), h('label', null, 'Subject', h('input', { name: 'title', required: true })), h('label', null, 'Message', h('textarea', { name: 'body', required: true })),
            h('button', { type: 'submit', disabled: f.status === 'loading' }, 'Post'), h('p', { role: 'status', 'class': 'meta' }, f.message || ''));
    };
    C.Forums = function (s) {
        return [h('h1', null, 'Forums'), C.Resource(res(s, 'forums'), function (d, l) { return h('ul', { 'class': 'grid' }, l.items.map(function (n) { n = U.node(n); return h('li', { 'class': 'card', key: n.id }, h('h3', null, h('a', { href: href('/forums/' + n.id) }, n.name)), h('p', { 'class': 'meta' }, n.summary || '')); })); }, 'No forums yet.')];
    };
    C.Topics = function (s, dispatch) {
        var fid = s.route.params.id, off = +s.route.query.offset || 0, lim = Exp.config.pageSize;
        return [h('p', null, h('a', { href: '#/forums' }, 'All forums')), h('h1', null, 'Topics'),
            C.Resource(res(s, 'topics:' + fid + ':' + off), function (d, l) { return [h('ul', { 'class': 'thread' }, l.items.map(function (n) { n = U.node(n); return h('li', { 'class': 'card', key: n.id }, h('a', { href: href('/forums/topic/' + n.id) }, n.name), ' ', h('span', { 'class': 'meta' }, U.date(n.date))); })), C.Pager(l, off, lim, function (o) { Exp.router.go('/forums/' + fid + '?offset=' + o); })]; }, 'No topics yet.'),
            C.PostForm(s, dispatch, 'new-topic', 'New topic', 'forumNewTopic', { forum_node: fid }, 'topics:' + fid + ':' + off)];
    };
    C.Topic = function (s, dispatch) {
        var tid = s.route.params.id;
        return [h('h1', null, 'Topic'), C.Resource(res(s, 'replies:' + tid), function (d, l) { return h('ol', { 'class': 'thread' }, l.items.map(function (n, i) { n = U.node(n); return h('li', { 'class': 'card', key: n.id || i }, h('strong', null, n.name), ' ', h('span', { 'class': 'meta' }, U.date(n.date)), h('p', null, n.body || n.summary || '')); })); }, 'No replies yet.'),
            C.PostForm(s, dispatch, 'reply', 'Reply', 'forumReply', { parent_node: tid }, 'replies:' + tid)];
    };
    C.Gallery = function (s, dispatch) {
        var off = +s.route.query.offset || 0, lim = Exp.config.pageSize * 2, lb = s.lightbox;
        return [h('h1', null, 'Media'), C.Resource(res(s, 'media:' + off), function (d, l) { return [h('ul', { 'class': 'grid gallery' }, l.items.map(function (n) { return C.ImageCard(n, dispatch); })), C.Pager(l, off, lim, function (o) { Exp.router.go('/media?offset=' + o); })]; }, 'No images yet.'),
            lb ? h('dialog', { 'aria-label': lb.name, ref: function (el) { if (!el.open && el.showModal) { el.showModal(); } },
                on: { close: function () { dispatch({ type: 'lightbox/close' }); } } }, h('img', { src: lb.src, alt: lb.name }), h('p', null, lb.name), h('form', { method: 'dialog' }, h('button', null, 'Close'))) : null];
    };
    C.Feeds = function (s, dispatch) {
        var open = s.feedOpen;
        return [h('h1', null, 'Feeds'), C.Resource(res(s, 'feeds'), function (d, l) { return h('ul', { 'class': 'grid' }, l.items.map(function (f) { return h('li', { 'class': 'card', key: f.id || f.title },
            h('h3', null, f.title || f.name || 'Feed'), h('p', { 'class': 'meta' }, f.description || ''), h('button', { type: 'button', on: { click: function () { dispatch({ type: 'feed/open', feed: f }); } } }, 'Show items'), ' ',
            f.url ? h('a', { 'class': 'btn secondary', href: f.url }, 'Subscribe') : null); })); }, 'No feeds are published.'),
            h('div', { 'aria-live': 'polite' }, open ? [h('h2', null, open.title || 'Items'), C.Resource(res(s, 'feed:' + open.id), function (d, l) { return h('ul', { 'class': 'thread' }, l.items.map(function (i, k) { return h('li', { 'class': 'card', key: k }, h('a', { href: i.link || i.url || '#' }, i.title || i.name), ' ', h('span', { 'class': 'meta' }, U.date(i.date || i.published))); })); }, 'This feed has no items.')] : null)];
    };
    C.Search = function (s) {
        var q = s.route.query.q || '', off = +s.route.query.offset || 0, lim = Exp.config.pageSize;
        if (!q) { return [h('h1', null, 'Search'), h('p', { 'class': 'state empty' }, 'Type something in the search box.')]; }
        return [h('h1', null, 'Search: ' + q), C.Resource(res(s, 'search:' + q + ':' + off), function (d, l) { return [h('p', { 'class': 'meta' }, l.total + ' results'), h('ul', { 'class': 'thread' }, l.items.map(function (n) { n = U.node(n); return h('li', { 'class': 'card', key: n.id }, h('h3', null, h('a', { href: href('/news/' + n.id) }, n.name)), h('p', null, n.summary || '')); })), C.Pager(l, off, lim, function (o) { Exp.router.go('/search?q=' + encodeURIComponent(q) + '&offset=' + o); })]; }, 'Nothing found for ' + q + '.')];
    };
    C.Login = function (s, dispatch) {
        var f = s.form.login || {};
        return [h('h1', null, 'Log in'), h('form', { 'class': 'stack', on: { submit: function (e) { e.preventDefault(); dispatch({ type: 'login/submit', login: e.target.elements.login.value, password: e.target.elements.password.value }); } } },
            h('label', null, 'User name', h('input', { name: 'login', autocomplete: 'username', required: true })), h('label', null, 'Password', h('input', { name: 'password', type: 'password', autocomplete: 'current-password', required: true })),
            h('button', { type: 'submit', disabled: f.status === 'loading' }, 'Log in'), f.status === 'error' ? h('p', { role: 'alert', 'class': 'state error' }, f.message) : null)];
    };
    C.Profile = function (s) {
        if (!s.user) { return [h('h1', null, 'Profile'), h('p', { 'class': 'state empty' }, 'You are not logged in. ', h('a', { href: '#/login' }, 'Log in'))]; }
        var d = (res(s, 'profile').status === 'ok' && res(s, 'profile').data.data) || s.user;
        return [h('h1', null, 'Profile'), h('dl', null, ['name', 'login', 'email'].filter(function (k) { return d[k]; }).map(function (k) { return [h('dt', { key: 't' + k }, k), h('dd', { key: 'd' + k }, d[k])]; }))];
    };
    C.NotFound = function (s) { return [h('h1', null, 'Page not found'), h('p', { 'class': 'state empty' }, 'There is no page at ' + s.route.path + '.')]; };

    /** The route table: pattern, page component, and the resources the page needs (effects load what is missing). */
    C.routes = [
        { p: '/', page: C.Home, title: 'Home', needs: function () { var n = Exp.config.nodes; return [['home:news', 'children', [n.news, 0, 4]], ['home:shop', 'products', [n.shop, 0, 4]], ['home:media', 'mediaImages', [n.media, 0, 4]]]; } },
        { p: '/news', page: C.NewsList, title: 'News', needs: function (r) { var o = +r.query.offset || 0; return [['news:' + o, 'children', [Exp.config.nodes.news, o, Exp.config.pageSize]]]; } },
        { p: '/news/:id', page: C.Article, title: 'Article', needs: function (r) { return [['view:' + r.params.id, 'view', [r.params.id]]]; } },
        { p: '/shop', page: C.Catalogue, title: 'Shop', needs: function (r) { var o = +r.query.offset || 0; return [['shop:' + o, 'products', [Exp.config.nodes.shop, o, Exp.config.pageSize]]]; } },
        { p: '/shop/:id', page: C.Product, title: 'Product', needs: function (r) { return [['product:' + r.params.id, 'productGet', [r.params.id]]]; } },
        { p: '/basket', page: C.Basket, title: 'Basket', needs: function () { return []; } },
        { p: '/forums', page: C.Forums, title: 'Forums', needs: function () { return [['forums', 'children', [Exp.config.nodes.forums, 0, 50]]]; } },
        { p: '/forums/topic/:id', page: C.Topic, title: 'Topic', needs: function (r) { return [['replies:' + r.params.id, 'forumReplies', [r.params.id, 0, 100]]]; } },
        { p: '/forums/:id', page: C.Topics, title: 'Topics', needs: function (r) { var o = +r.query.offset || 0; return [['topics:' + r.params.id + ':' + o, 'forumTopics', [r.params.id, o, Exp.config.pageSize]]]; } },
        { p: '/media', page: C.Gallery, title: 'Media', needs: function (r) { var o = +r.query.offset || 0; return [['media:' + o, 'mediaImages', [Exp.config.nodes.media, o, Exp.config.pageSize * 2]]]; } },
        { p: '/feeds', page: C.Feeds, title: 'Feeds', needs: function () { return [['feeds', 'feedList', []]]; } },
        { p: '/search', page: C.Search, title: 'Search', needs: function (r) { if (!r.query.q) { return []; } var o = +r.query.offset || 0; return [['search:' + r.query.q + ':' + o, 'search', [r.query.q, o, Exp.config.pageSize]]]; } },
        { p: '/login', page: C.Login, title: 'Log in', needs: function () { return []; } },
        { p: '/profile', page: C.Profile, title: 'Profile', needs: function () { return [['profile', 'profile', []]]; } }
    ];
    /** Route table lookup. Order matters (/forums/topic/:id before /forums/:id). */
    C.match = function (path) {
        for (var i = 0; i < C.routes.length; i++) {
            var keys = [], re = new RegExp('^' + C.routes[i].p.replace(/:(\w+)/g, function (m, k) { keys.push(k); return '([^/]+)'; }) + '/?$'), m = re.exec(path);
            if (m) { var params = {}; keys.forEach(function (k, j) { params[k] = decodeURIComponent(m[j + 1]); }); return { route: C.routes[i], params: params }; }
        }
        return null;
    };
}(window));
