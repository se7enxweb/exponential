/** Login, logout, profile. Services: whoami, login, logout, profile. State: Exp.user (null = anonymous). */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal, el = Exp.util.el, ui = Exp.ui;
    Exp.user = null;

    Exp.drawAccount = function () {
        var $s = $('#account-slot').empty();
        if (Exp.user) { $s.append(el('a', { href: '#/profile', text: Exp.user.name || Exp.user.login || 'Profile' }), ' ',
            el('button', { type: 'button', 'class': 'secondary', on: { click: function () {
                Exp.api.call('logout', [], {}).always(function () { Exp.api.token = null; Exp.api.tokenLoaded = false; Exp.user = null; Exp.drawAccount(); Exp.router.go('/'); });
            } }, text: 'Log out' })); }
        else { $s.append(el('a', { href: '#/login', text: 'Log in' })); }
    };
    Exp.loadUser = function () {
        return Exp.api.call('whoami', []).then(function (env) {
            var d = env.data || {}; Exp.user = (d.registered === false || d.anonymous || !d.login) ? null : d; Exp.drawAccount();
        }, function () { Exp.user = null; Exp.drawAccount(); });
    };

    Exp.router.add('/login', function (ctx) {
        var $v = ctx.$view, $msg = el('p', { role: 'alert', 'class': 'state error', hidden: true }), u = el('input', { id: 'lg-u', name: 'login', autocomplete: 'username', required: true }), p = el('input', { id: 'lg-p', name: 'password', type: 'password', autocomplete: 'current-password', required: true });
        ui.title('Log in');
        $v.empty().append(el('h1', { text: 'Log in' }), el('form', { 'class': 'stack', on: { submit: function (e) {
            e.preventDefault(); $msg.prop('hidden', true);
            Exp.api.call('login', [], { username: u.val(), password: p.val() }, { noToken: true }).then(function (env) { var t = env.data && env.data.token; Exp.api.token = t || null; Exp.api.tokenLoaded = !!t; return Exp.loadUser(); }).then(function () { Exp.router.go('/profile'); },
                function (err) { $msg.text(err.code === 'unavailable' ? 'The login service is not available yet.' : 'Login failed. ' + (err.message || '')).prop('hidden', false); });
        } } }, el('label', null, 'User name', u), el('label', null, 'Password', p), el('button', { type: 'submit', text: 'Log in' }), $msg));
    });

    Exp.router.add('/profile', function (ctx) {
        var $v = ctx.$view; ui.title('Profile'); $v.empty().append(el('h1', { text: 'Profile' }));
        if (!Exp.user) { return $v.append(ui.state('empty', 'You are not logged in. '), el('a', { href: '#/login', text: 'Log in' })); }
        var $b = el('div'); $v.append($b); ui.loading($b);
        Exp.api.call('profile', []).then(function (env) {
            var d = env.data || Exp.user; $b.attr('aria-busy', 'false').empty().append(el('dl', null, $.map(['name', 'login', 'email'], function (k) {
                return d[k] ? [el('dt', { text: k }), el('dd', { text: d[k] })] : null; })));
        }, function () { $b.attr('aria-busy', 'false').empty().append(el('dl', null, el('dt', { text: 'name' }), el('dd', { text: Exp.user.name || Exp.user.login || '' }))); });
    });
}(window, jQuery));
