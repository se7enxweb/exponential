/**
 * expportal core (jQuery 4). Shared by expportal_jquery and expportal_reactive.
 *  - ExpPortal.config   read from the data-attributes of <body> (set by pagelayout.tpl)
 *  - ExpPortal.services the one table that maps portal features to expservices calls (change a name here only)
 *  - ExpPortal.api      call(): ezjscore/call/<service>::<arg>::<arg>, GET for reads, POST + form token for writes
 *  - ExpPortal.util     escaping-free DOM helpers (text is always set as text, never as HTML)
 *  - ExpPortal.ui       loading / error / "service not available" / empty states, pager, live announcements
 *  - ExpPortal.router   hash router (#/news/12), a route is a pattern with :params
 *  - ExpPortal.theme    light / dark / auto toggle, remembered in localStorage
 * Reply of ezjscore: { error_text, content }, content being the service envelope { ok, data, meta } or
 * { ok:false, error:{ code, message } }. api.call() resolves with the envelope, rejects with { code, message }.
 */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal = window.ExpPortal || {};
    var body = document.body, ds = body.dataset;

    Exp.config = {
        base: ds.base || '/', call: ds.call || '/ezjscore/call', title: ds.title || 'Portal', design: ds.design || '',
        nodes: { news: +ds.news || 2, shop: +ds.shop || 2, forums: +ds.forums || 2, media: +ds.media || 43 },
        pageSize: +ds.pagesize || 12
    };

    /* Feature to service. [ 'domain::method', [ arg names in URL order ] ] for reads; writes list their POST fields. */
    Exp.services = {
        catalog:      'expservices::catalog',
        whoami:       'expsession::whoami',
        login:        'expsession::login',       // POST username, password (no form token)
        logout:       'expsession::logout',
        token:        'expsession::token',
        children:     'expnode::children',      // portal args: node, offset, limit
        view:         'expnode::get',           // node
        products:     'expproduct::list',       // parent node, offset, limit
        productGet:   'expproduct::view',       // node
        basketGet:    'expbasket::view',
        basketAdd:    'expbasket::add',         // POST object_id, quantity
        basketRemove: 'expbasket::remove',      // POST item_id
        forumTopics:  'expforum::topics',       // forum node, offset, limit
        forumReplies: 'expforum::replies',      // topic node, offset, limit
        forumReply:   'expforum::reply',        // POST parent_node, title, body
        forumNewTopic:'expforum::create_topic', // POST forum_node, title, body
        mediaImages:  'expimage::list',         // parent node, offset, limit
        feedList:     'expfeed::list',
        feedItems:    'expfeed::items',         // feed id, limit
        search:       'expsearch::search',      // text, offset, limit
        profile:      'expuser::profile'
    };
    /* The portal passes ( node, offset, limit ); a few services take their positional arguments in another order.
       argMap turns the portal's order into the service's, so the pages never know. */
    Exp.argMap = {
        children:    function (a) { return [a[0], 'published', 'desc', a[2], a[1]]; },   // node_id, sort, order, limit, offset
        products:    function (a) { return [a[0], a[2], a[1]]; },                         // parent_node_id, limit, offset
        mediaImages: function (a) { return [a[0], a[2], a[1]]; },                         // parent, limit, offset
        search:      function (a) { return [a[0], a[2], a[1]]; }                          // text, limit, offset
    };

    var util = Exp.util = {
        /** el('div', { class:'x', 'data-a':1, on:{ click:fn } }, child, 'text', [ children ]) */
        el: function (tag, attrs) {
            var $e = $(document.createElement(tag));
            $.each(attrs || {}, function (k, v) {
                if (v === null || v === undefined || v === false) { return; }
                if (k === 'on') { $.each(v, function (ev, fn) { $e.on(ev, fn); }); }
                else if (k === 'text') { $e.text(v); }
                else { $e.attr(k, v === true ? k : v); }
            });
            util.append($e, Array.prototype.slice.call(arguments, 2));
            return $e;
        },
        append: function ($e, kids) {
            $.each(kids, function (i, k) {
                if (k === null || k === undefined || k === false) { return; }
                if (Array.isArray(k)) { util.append($e, k); }
                else if (typeof k === 'string' || typeof k === 'number') { $e.append(document.createTextNode(String(k))); }
                else { $e.append(k); }
            });
        },
        debounce: function (fn, ms) { var t; return function () { var a = arguments, s = this; clearTimeout(t); t = setTimeout(function () { fn.apply(s, a); }, ms); }; },
        date: function (v) {
            if (!v) { return ''; }
            var d = typeof v === 'number' || /^\d+$/.test(String(v)) ? new Date(+v * 1000) : new Date(v);
            return isNaN(d) ? String(v) : d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        },
        money: function (v) { return (v === null || v === undefined || v === '') ? '' : (isNaN(+v) ? String(v) : (+v).toFixed(2)); },
        /** Folds the exported node shapes into one: id, name, summary, image, date, url, class, price. */
        node: function (n) {
            n = n || {};
            var a = n.attributes || n.fields || n.data_map || {};
            function attr() { for (var i = 0; i < arguments.length; i++) { var x = a[arguments[i]]; if (x !== undefined && x !== null && x !== '') { return typeof x === 'object' ? (x.text || x.content || x.value || x.url || '') : x; } } return ''; }
            return {
                id: n.node_id || n.id || n.main_node_id || 0, objectId: n.contentobject_id || n.object_id || n.id || 0,
                name: n.name || n.title || attr('title', 'name') || '(untitled)',
                summary: n.summary || n.intro || attr('intro', 'short_description', 'description', 'teaser') ,
                image: n.image_url || n.image || attr('image', 'thumbnail'),
                date: n.published || n.published_at || n.modified || n.created || '',
                url: n.url_alias || n.url || '', cls: n.class_identifier || n.class || '',
                price: (function (p) { return p && typeof p === 'object' ? (p.inc_vat !== undefined ? p.inc_vat : (p.price !== undefined ? p.price : p.ex_vat)) : p; }(n.price !== undefined ? n.price : attr('price'))), body: n.body || attr('body', 'description', 'text'), raw: n
            };
        }
    };

    var api = Exp.api = {
        catalogNames: null,   // set of 'domain::method' once the catalogue loaded; null = unknown, try the call
        token: null, tokenLoaded: false,
        url: function (service, args) {
            return Exp.config.call.replace(/\/$/, '') + '/' + service + (args && args.length ? '::' + $.map(args, function (a) { return encodeURIComponent(a === undefined || a === null ? '' : a); }).join('::') : '');
        },
        available: function (service) { return api.catalogNames === null || !!api.catalogNames[service]; },
        /** call( 'children', [ 2, 0, 12 ] ) or call( 'basketAdd', [], { object_id: 5 } ) (POST). */
        call: function (feature, args, post, opts) {
            opts = opts || {};
            var service = Exp.services[feature] || feature, dfd = $.Deferred();
            if (Exp.argMap[feature]) { args = Exp.argMap[feature](args); }
            if (!api.available(service)) { return dfd.reject({ code: 'unavailable', message: 'Service ' + service + ' is not available.' }).promise(); }
            function send(token) {
                var o = { url: api.url(service, args) + '?ContentType=json', dataType: 'json', cache: false, headers: { 'X-Requested-With': 'XMLHttpRequest' } };
                if (post) { o.method = 'POST'; o.data = opts.noToken ? post : $.extend({ ezxform_token: token || '' }, post); if (token) { o.headers['X-CSRF-Token'] = token; } }
                $.ajax(o).then(function (res) {
                    var env = res && res.content;
                    if (res && res.error_text) { return dfd.reject({ code: /not a valid|no such|not found|unknown/i.test(res.error_text) ? 'unavailable' : 'error', message: res.error_text }); }
                    if (env && env.ok === false) { var e = env.error || {}; return dfd.reject({ code: e.code || 'error', message: e.message || 'Request failed.' }); }
                    dfd.resolve(env && env.ok !== undefined ? env : { ok: true, data: env, meta: {} });
                }, function (xhr) {
                    dfd.reject({ code: xhr.status === 404 ? 'unavailable' : (xhr.status || 'error'), message: xhr.status === 404 ? 'Service ' + service + ' is not available.' : 'Request failed (' + xhr.status + ').' });
                });
            }
            if (post && !opts.noToken && !api.tokenLoaded) { api.fetchToken().always(function () { send(api.token); }); } else { send(api.token); }
            return dfd.promise();
        },
        fetchToken: function () {
            return api.call('token', []).then(function (env) { var d = env.data; api.token = typeof d === 'string' ? d : (d && (d.token || d.form_token)) || null; api.tokenLoaded = true; return api.token; });
        },
        /** Folds a list reply: data is an array, or { items, total } (paged envelope). */
        list: function (env) {
            var d = env && env.data, m = (env && env.meta) || {}, items = Array.isArray(d) ? d : (d && (d.items || d.list || d.nodes)) || [];
            var total = m.total !== undefined ? m.total : (d && d.total !== undefined ? d.total : items.length);
            return { items: items, total: +total, offset: +(m.offset || (d && d.offset) || 0), limit: +(m.limit || (d && d.limit) || items.length) };
        },
        loadCatalog: function () {
            return $.ajax({ url: api.url(Exp.services.catalog) + '?ContentType=json', dataType: 'json', cache: false }).then(function (res) {
                var d = res && res.content && res.content.data, names = {}, n = 0;
                function add(domain, row) {
                    // row.call is 'ezjscore/call/exp<domain>::<method>::<arg>...': the service name is its first two parts
                    var m = row && /call\/([^:]+)::([^:]+)/.exec(row.call || '');
                    if (m) { names[m[1] + '::' + m[2]] = true; n++; } else if (row && row.method && row.domain) { names['exp' + row.domain + '::' + row.method] = true; n++; }
                }
                if (Array.isArray(d)) { $.each(d, function (i, r) { add(r.domain, r); }); }
                else if (d && typeof d === 'object') { $.each(d.services || d, function (k, v) { if (Array.isArray(v)) { $.each(v, function (i, r) { add(k, r); }); } }); }
                api.catalogNames = n ? names : null;
                return n;
            }, function () { api.catalogNames = null; return 0; });
        }
    };

    var ui = Exp.ui = {
        main: function () { return $('#main'); },
        announce: function (msg) { $('#live').text(msg); },
        state: function (kind, msg) { return util.el('p', { 'class': 'state ' + kind, role: kind === 'error' ? 'alert' : null, text: msg }); },
        loading: function ($t) { $t.attr('aria-busy', 'true').empty().append(ui.state('loading', 'Loading')); },
        /** Shows what a rejected call means: unavailable gets its own clear state. */
        failure: function ($t, err) {
            var un = err && err.code === 'unavailable';
            $t.attr('aria-busy', 'false').empty().append(ui.state(un ? 'unavailable' : 'error', un ? 'This part of the portal needs a service that is not available yet. (' + err.message + ')' : (err && err.message) || 'Something went wrong.'));
        },
        empty: function ($t, msg) { $t.attr('aria-busy', 'false').empty().append(ui.state('empty', msg || 'Nothing here yet.')); },
        /** pager( { total, offset, limit }, function ( newOffset ) ) */
        pager: function (p, go) {
            if (p.total <= p.limit && p.offset === 0) { return $(); }
            var last = p.offset + p.limit >= p.total;
            return util.el('nav', { 'class': 'pager', 'aria-label': 'Pages' },
                util.el('button', { type: 'button', 'class': 'secondary', disabled: p.offset <= 0, on: { click: function () { go(Math.max(0, p.offset - p.limit)); } }, text: 'Previous' }),
                util.el('span', { 'class': 'meta', text: (p.offset + 1) + ' to ' + Math.min(p.total, p.offset + p.limit) + ' of ' + p.total }),
                util.el('button', { type: 'button', 'class': 'secondary', disabled: last, on: { click: function () { go(p.offset + p.limit); } }, text: 'Next' }));
        },
        title: function (t) { document.title = t + ' | ' + Exp.config.title; }
    };

    var routes = [], router = Exp.router = {
        add: function (pattern, handler, label) {
            var keys = [], re = new RegExp('^' + pattern.replace(/:(\w+)/g, function (m, k) { keys.push(k); return '([^/]+)'; }) + '/?$');
            routes.push({ re: re, keys: keys, handler: handler, label: label });
        },
        current: function () { return (location.hash || '#/').slice(1) || '/'; },
        go: function (path) { location.hash = '#' + path; },
        resolve: function () {
            var full = router.current(), path = full.split('?')[0], query = {};
            ((full.split('?')[1]) || '').split('&').forEach(function (kv) { if (kv) { var p = kv.split('='); query[decodeURIComponent(p[0])] = decodeURIComponent((p[1] || '').replace(/\+/g, ' ')); } });
            for (var i = 0; i < routes.length; i++) {
                var m = routes[i].re.exec(path);
                if (m) {
                    var params = {}; $.each(routes[i].keys, function (j, k) { params[k] = decodeURIComponent(m[j + 1]); });
                    return { path: path, params: params, query: query, handler: routes[i].handler };
                }
            }
            return { path: path, params: {}, query: query, handler: function (ctx) { ui.title('Not found'); ctx.$view.empty().append(util.el('h1', { text: 'Page not found' }), ui.state('empty', 'There is no page at ' + path + '.')); } };
        },
        start: function (render) {
            function run() { var r = router.resolve(); render(r); }
            $(window).on('hashchange', run); run();
        }
    };

    Exp.theme = {
        init: function () {
            $('.theme-toggle').on('click', function () {
                var cur = document.documentElement.getAttribute('data-theme'), dark = cur === 'dark' || (cur === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches),
                    next = dark ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                try { localStorage.setItem('expportal-theme', next); } catch (e) { /* private mode */ }
            });
        }
    };

    /** Menu shared by both designs. */
    Exp.menu = [ ['/', 'Home'], ['/news', 'News'], ['/shop', 'Shop'], ['/forums', 'Forums'], ['/media', 'Media'], ['/feeds', 'Feeds'] ];
    Exp.drawNav = function (path) {
        var $nav = $('#nav').empty();
        $.each(Exp.menu, function (i, m) {
            var cur = m[0] === '/' ? path === '/' : path.indexOf(m[0]) === 0;
            $nav.append(util.el('a', { href: '#' + m[0], 'aria-current': cur ? 'page' : null, text: m[1] }));
        });
    };
    Exp.initChrome = function () {
        Exp.theme.init();
        $('.nav-toggle').on('click', function () { var o = $('#nav').toggleClass('open').hasClass('open'); $(this).attr('aria-expanded', String(o)); });
        $('#nav').on('click', 'a', function () { $('#nav').removeClass('open'); $('.nav-toggle').attr('aria-expanded', 'false'); });
        $('#search-form').on('submit', function (e) { e.preventDefault(); var q = String($('#search-q').val()).trim(); if (q) { router.go('/search?q=' + encodeURIComponent(q)); } });
    };
    Exp.afterRender = function () { var m = document.getElementById('main'); window.scrollTo(0, 0); if (m) { m.focus({ preventScroll: true }); } };
}(window, jQuery));
