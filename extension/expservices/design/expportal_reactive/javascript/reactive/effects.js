/**
 * The React-like model, part 4: effects. Everything that talks to a service lives here, nothing else does.
 * An effect watches the actions after the reducer ran, calls a service, and reports the outcome by dispatching
 * res/success, res/failure, form/set, user/set ... Components only ever see the resulting state.
 */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, C = Exp.components;

    function failed(err) { return { status: err && err.code === 'unavailable' ? 'unavailable' : 'error', error: err || { message: 'Request failed.' } }; }

    /** load( store, key, feature, args ): the one pattern for a read. */
    function load(store, key, feature, args) {
        store.dispatch({ type: 'res/request', key: key });
        return Exp.api.call(feature, args).then(function (env) { store.dispatch({ type: 'res/success', key: key, data: env }); return env; },
            function (err) { store.dispatch({ type: 'res/failure', key: key, error: err }); });
    }
    Exp.load = load;

    function refreshBasket(store) {
        return Exp.api.call('basketGet', []).then(function (env) { store.dispatch({ type: 'basket/set', data: env.data || {} }); }, function (e) { store.dispatch({ type: 'basket/failed', error: e }); });
    }
    function refreshUser(store) {
        return Exp.api.call('whoami', []).then(function (env) { var d = env.data || {}; store.dispatch({ type: 'user/set', user: (d.registered === false || d.anonymous || !d.login) ? null : d }); },
            function () { store.dispatch({ type: 'user/set', user: null }); });
    }

    Exp.installEffects = function (store) {
        store.effect(function (a) {
            switch (a.type) {
            case 'route':
                var m = C.match(a.route.path);
                Exp.ui.title(m ? m.route.title : 'Not found');
                if (m) { m.route.needs({ params: m.params, query: a.route.query }).forEach(function (n) { load(store, n[0], n[1], n[2]); }); }
                break;
            case 'boot': refreshUser(store).always(function () { refreshBasket(store); }); break;
            case 'basket/add':
                store.dispatch({ type: 'form/set', id: 'basket-add', form: { status: 'loading', message: '' } });
                Exp.api.call('basketAdd', [], { object_id: a.objectId, quantity: a.quantity }).then(function () {
                    store.dispatch({ type: 'form/set', id: 'basket-add', form: { status: 'ok', message: 'Added to the basket.' } }); refreshBasket(store);
                }, function (e) { store.dispatch({ type: 'form/set', id: 'basket-add', form: { status: 'error', message: e.code === 'unavailable' ? 'The basket service is not available yet.' : e.message } }); });
                break;
            case 'basket/remove': Exp.api.call('basketRemove', [], { item_id: a.itemId }).then(function () { refreshBasket(store); }, function (e) { store.dispatch({ type: 'basket/failed', error: e }); }); break;
            case 'post/submit':
                store.dispatch({ type: 'form/set', id: a.id, form: { status: 'loading', message: '' } });
                Exp.api.call(a.feature, [], a.fields).then(function () {
                    if (a.reset) { a.reset.reset(); }
                    store.dispatch({ type: 'form/set', id: a.id, form: { status: 'ok', message: 'Posted.' } });
                    var r = store.getState().route, m2 = C.match(r.path);
                    if (m2) { m2.route.needs({ params: m2.params, query: r.query }).forEach(function (n) { if (n[0] === a.reload) { load(store, n[0], n[1], n[2]); } }); }
                }, function (e) { store.dispatch({ type: 'form/set', id: a.id, form: { status: 'error', message: e.code === 401 || e.code === 403 ? 'Please log in to post.' : (e.code === 'unavailable' ? 'Posting is not available yet.' : e.message) } }); });
                break;
            case 'login/submit':
                store.dispatch({ type: 'form/set', id: 'login', form: { status: 'loading', message: '' } });
                Exp.api.call('login', [], { username: a.login, password: a.password }, { noToken: true }).then(function (env) {
                    var t = env.data && env.data.token; Exp.api.token = t || null; Exp.api.tokenLoaded = !!t;
                    store.dispatch({ type: 'form/set', id: 'login', form: { status: 'ok', message: '' } });
                    return refreshUser(store);
                }).then(function () { if (store.getState().user) { Exp.router.go('/profile'); } },
                    function (e) { store.dispatch({ type: 'form/set', id: 'login', form: { status: 'error', message: e.code === 'unavailable' ? 'The login service is not available yet.' : 'Login failed. ' + (e.message || '') } }); });
                break;
            case 'logout/submit': Exp.api.call('logout', [], {}).always(function () { Exp.api.token = null; Exp.api.tokenLoaded = false; store.dispatch({ type: 'user/set', user: null }); Exp.router.go('/'); }); break;
            case 'feed/open': load(store, 'feed:' + a.feed.id, 'feedItems', [a.feed.id, 20]); break;
            }
        });
    };
}(window, jQuery));
