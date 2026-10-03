/**
 * Reducers, the store, the mounts. The data flow of the whole portal in one place:
 *   hashchange -> dispatch route -> reducer + effects (service calls) -> dispatch res/* -> reducer -> render
 */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, C = Exp.components, h = Exp.h;

    var reducers = {
        route: function (s, a) { return a.type === 'route' ? a.route : (s || { path: '/', params: {}, query: {} }); },
        user: function (s, a) { return a.type === 'user/set' ? a.user : (s === undefined ? null : s); },
        basket: function (s, a) {
            s = s || { items: [], total: 0, count: 0, status: 'idle' };
            if (a.type === 'basket/set') { var d = a.data, it = d.items || d.products || []; return { items: it, total: d.total !== undefined ? d.total : (d.totals && (d.totals.total_inc_vat || d.totals.total)), count: d.count !== undefined ? d.count : it.length, status: 'ok' }; }
            if (a.type === 'basket/failed') { return { items: [], total: 0, count: 0, status: a.error && a.error.code === 'unavailable' ? 'unavailable' : 'error' }; }
            return s;
        },
        res: function (s, a) {
            s = s || {}; var n;
            if (a.type === 'res/request') { n = Object.assign({}, s); n[a.key] = Object.assign({ status: 'loading' }, s[a.key] && s[a.key].status === 'ok' ? { data: s[a.key].data } : {}); return n; }
            if (a.type === 'res/success') { n = Object.assign({}, s); n[a.key] = { status: 'ok', data: a.data }; return n; }
            if (a.type === 'res/failure') { n = Object.assign({}, s); n[a.key] = { status: a.error && a.error.code === 'unavailable' ? 'unavailable' : 'error', error: a.error || { message: 'Request failed.' } }; return n; }
            return s;
        },
        form: function (s, a) { if (a.type === 'form/set') { var n = Object.assign({}, s || {}); n[a.id] = a.form; return n; } return s || {}; },
        lightbox: function (s, a) { return a.type === 'lightbox/open' ? a.image : (a.type === 'lightbox/close' ? null : (s === undefined ? null : s)); },
        feedOpen: function (s, a) { return a.type === 'feed/open' ? a.feed : (s === undefined ? null : s); }
    };
    Exp.reducers = reducers;

    /** The page: the component the route table names, or the not-found page. */
    function Page(state, dispatch) {
        var m = C.match(state.route.path), s = Object.assign({}, state, { route: Object.assign({}, state.route, { params: m ? m.params : {} }) });
        return (m ? m.route.page : C.NotFound)(s, dispatch);
    }
    Exp.Page = Page;

    $(function () {
        Exp.initChrome();
        var store = Exp.store = Exp.createStore(Exp.combine(reducers)), dispatch = store.dispatch;
        Exp.installEffects(store);

        var mains = [
            Exp.mount(document.getElementById('main'), function () { var s = store.getState(); return h('div', { key: s.route.path }, Page(s, dispatch)); }),
            Exp.mount(document.getElementById('nav'), function () { return C.Nav(store.getState()); }),
            Exp.mount(document.getElementById('account-slot'), function () { return C.Account(store.getState(), dispatch); }),
            Exp.mount(document.getElementById('basket-count'), function () { return String(store.getState().basket.count); })
        ];
        store.subscribe(function () { mains.forEach(function (m) { m.update(); }); });

        var lastPath = null;
        function fromHash() {
            var r = Exp.router.resolve(); // reuse the jQuery design's parser for path + query only
            var route = { path: r.path, params: {}, query: r.query };
            dispatch({ type: 'route', route: route });
            if (lastPath !== r.path) { Exp.afterRender(); lastPath = r.path; }
        }
        $(window).on('hashchange', fromHash);
        Exp.api.loadCatalog().always(function (n) {
            $('#service-status').text(Exp.api.catalogNames ? n + ' services listed.' : 'Service catalogue not available.');
            dispatch({ type: 'boot' }); fromHash();
        });
    });
}(window, jQuery));
