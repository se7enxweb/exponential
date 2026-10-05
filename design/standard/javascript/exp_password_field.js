/*
 * exp_password_field.js: progressive enhancement of the user/password form, in any design. Plain JavaScript,
 * no library, no network; the page works without it and the server checks everything again.
 *
 * Hooks (the template contract of user/password):
 *   form[data-exp-password-form][data-exp-password-config="{json}"]  start; config: rules, minLength, strengthMeter,
 *                                       generate, generateLength, login, i18n (all optional)
 *   input[data-exp-password="current|new|confirm"]
 *   button[data-exp-password-toggle="<input id>"][aria-pressed]   show / hide; aria-pressed carries the state, the
 *                                       text (a [data-exp-password-toggle-text] inside, or i18n show) stays
 *   [data-exp-password-rules] li[data-exp-password-rule="<id>"]   gets data-state="met|unmet"; not_current is left alone
 *   [data-exp-password-meter]    strength: data-score 0..4 on it, a <meter> and a [data-exp-password-meter-text] inside
 *   [data-exp-password-match]    match feedback under confirm; data-state="match|nomatch"
 *   button[data-exp-password-generate]  fills new + confirm and shows them
 *   [data-exp-password-summary]  error summary, anywhere in the page; focused on load (else the first [aria-invalid=true] field)
 *   [data-exp-password-status]   optional live region for "generated" (else the match element is used)
 * Controls that need the script (toggles, meter, generate, match) carry the hidden attribute in the markup;
 * the script shows them. The form gets the class exp-pw-js.
 */
(function () {
    'use strict';
    function re(u, a) { try { return new RegExp(u, 'u'); } catch (e) { return new RegExp(a); } }
    var cls = [re('\\p{Ll}', '[a-z]'), re('\\p{Lu}', '[A-Z]'), re('\\p{Nd}', '[0-9]'), re('[^\\p{L}\\p{Nd}]', '[^A-Za-z0-9]')];
    function classes(v) { var n = 0; for (var i = 0; i < 4; i++) if (cls[i].test(v)) n++; return n; }

    function score(v) {
        if (!v) return 0;
        var s = v.length / 4 + classes(v) - 1;
        if (/(.)\1\1/.test(v)) s--;
        if (/^[0-9]+$/.test(v) || /^[a-z]+$/i.test(v)) s--;
        if (/^(password|passwort|qwert|12345|letmein|admin|welcome|hallo)/i.test(v)) s -= 3;
        s = Math.max(0, Math.min(4, Math.floor(s / 1.5)));
        return v.length < 8 ? Math.min(s, 1) : s;
    }

    function generate(n) {
        var sets = ['abcdefghijkmnpqrstuvwxyz', 'ABCDEFGHJKLMNPQRSTUVWXYZ', '23456789', '-_.!?#%+=*'],
            all = sets.join(''), out = [], r = new Uint32Array(n * 2), i, j, t, set;
        window.crypto.getRandomValues(r);
        for (i = 0; i < n; i++) { set = i < 4 ? sets[i] : all; out.push(set.charAt(r[i] % set.length)); }
        for (i = n - 1; i > 0; i--) { j = r[n + i] % (i + 1); t = out[i]; out[i] = out[j]; out[j] = t; }
        return out.join('');
    }

    function init(form) {
        if (form.getAttribute('data-exp-password-ready')) return;
        form.setAttribute('data-exp-password-ready', '1');
        form.className += ' exp-pw-js';
        var cfg = {}, t, q = function (s) { return form.querySelector(s); }, all = function (s) { return form.querySelectorAll(s); };
        try { cfg = JSON.parse(form.getAttribute('data-exp-password-config') || '{}') || {}; } catch (e) {}
        t = cfg.i18n || {};
        var pw = q('[data-exp-password="new"]'), cf = q('[data-exp-password="confirm"]'),
            rules = all('[data-exp-password-rule]'), meter = q('[data-exp-password-meter]'),
            match = q('[data-exp-password-match]'), gen = q('[data-exp-password-generate]'),
            status = q('[data-exp-password-status]') || match, toggles = all('[data-exp-password-toggle]'),
            min = {}, i, login = String(cfg.login || '').toLowerCase();
        (cfg.rules || []).forEach(function (r) { min[r.id] = r.min; });
        var test = {
            length: function (v) { return v.length >= (min.length || cfg.minLength || 1); },
            lowercase: function (v) { return cls[0].test(v); },
            uppercase: function (v) { return cls[1].test(v); },
            digit: function (v) { return cls[2].test(v); },
            symbol: function (v) { return cls[3].test(v); },
            classes: function (v) { return classes(v) >= (min.classes || 0); },
            not_login: function (v) { return login.length < 3 || v.toLowerCase().indexOf(login) < 0; }
        };

        function setToggle(b, on) {
            var input = document.getElementById(b.getAttribute('data-exp-password-toggle') || b.getAttribute('aria-controls'));
            if (!input) return;
            input.type = on ? 'text' : 'password';
            // the name stays "Show" (the pressed state says whether it is shown), so the visible text and the
            // accessible name never disagree; an empty button gets the i18n text
            b.setAttribute('aria-pressed', on ? 'true' : 'false');
            var txt = b.querySelector('[data-exp-password-toggle-text]') || b;
            if (!txt.textContent.replace(/\s+/g, '') && t.show) txt.textContent = t.show;
        }

        function update() {
            var v = pw ? pw.value : '', j, el, f, s, m;
            for (j = 0; j < rules.length; j++) {
                el = rules[j]; f = test[el.getAttribute('data-exp-password-rule')];
                if (!f) continue;
                el.setAttribute('data-state', v && f(v) ? 'met' : 'unmet');
                m = el.querySelector('[data-exp-password-rule-state]');
                if (m) m.textContent = v && f(v) ? (t.ruleMet || '') : (t.ruleUnmet || '');
            }
            if (meter) {
                s = score(v);
                meter.setAttribute('data-score', v ? String(s) : '');
                m = meter.querySelector('meter');
                if (m) m.value = v ? s + 1 : 0;
                m = meter.querySelector('[data-exp-password-meter-text]');
                if (m) m.textContent = v ? (t['strength' + s] || '') : '';
            }
            if (match && cf) {
                if (!cf.value) { match.hidden = true; match.removeAttribute('data-state'); match.textContent = ''; }
                else {
                    match.hidden = false;
                    match.setAttribute('data-state', cf.value === v ? 'match' : 'nomatch');
                    match.textContent = (cf.value === v ? t.match : t.noMatch) || '';
                }
            }
        }

        for (i = 0; i < toggles.length; i++) (function (b) {
            b.hidden = false;
            setToggle(b, false);
            b.addEventListener('click', function () { setToggle(b, b.getAttribute('aria-pressed') !== 'true'); });
        })(toggles[i]);
        if (pw) pw.addEventListener('input', function () { if (status && status !== match) status.hidden = true; update(); });
        if (cf) cf.addEventListener('input', update);
        if (meter && cfg.strengthMeter !== false) meter.hidden = false;
        if (gen && pw && cfg.generate !== false && window.crypto && window.crypto.getRandomValues) {
            gen.hidden = false;
            gen.addEventListener('click', function () {
                var p = generate(Math.max(12, parseInt(cfg.generateLength, 10) || 16)), k, c;
                pw.value = p;
                if (cf) cf.value = p;
                for (k = 0; k < toggles.length; k++) {
                    c = toggles[k].getAttribute('data-exp-password-toggle');
                    if (c === pw.id || (cf && c === cf.id)) setToggle(toggles[k], true);
                }
                update();
                if (status && t.generated) { status.hidden = false; status.textContent = t.generated; }
                pw.focus();
            });
        }
        update();
        var first = document.querySelector('[data-exp-password-summary]') || q('[aria-invalid="true"]');
        if (first) first.focus();
    }

    function start() {
        var f = document.querySelectorAll('[data-exp-password-form]');
        for (var i = 0; i < f.length; i++) init(f[i]);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
    window.expPasswordField = { init: init, score: score };
})();
