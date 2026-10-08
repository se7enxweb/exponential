/*
 * The "Exp Debug" bar: tabs over the report sections, the filter box, the message level filter, SQL sorting,
 * and the settings and cache controls drawn from the bar's server functions (ezjscore expdebugbar::*).
 *
 * Loaded by the debug report itself (lib/ezutils/classes/expdebugbarreport.php), which prints the markup and
 * a JSON block (#exp-debug-data) this script starts from. The pinned bar and its open/close with "Keep open
 * on reload" are inline in the report and do not depend on this file. Without the markup (an older report)
 * the script does nothing.
 *
 * Words: t() looks the English source up in data.strings (translated by the report, context
 * design/standard/debugbar) and falls back to the source. Every string passed to t() must be listed in
 * expDebugBarReport::scriptStrings().
 */
(function () {
  'use strict';
  const root = document.getElementById('debug');
  if (!root || root.getAttribute('data-exp-debug-bar') !== '2' || root.classList.contains('exp-debug-js')) return;
  root.classList.add('exp-debug-js');

  // A page shown inside a frame (an editor dialog, a preview) has no room for the bar and would cover its own
  // buttons; the page around it shows the debug bar.
  let framed = false;
  try { framed = window.self !== window.top; } catch (e) { framed = true; }
  if (framed) {
    root.hidden = true;
    root.style.display = 'none';
    return;
  }

  let data = {};
  try { data = JSON.parse((document.getElementById('exp-debug-data') || {}).textContent || '{}'); } catch (e) { data = {}; }
  const words = data.strings || {};
  const t = (source, args) => {
    let s = Object.prototype.hasOwnProperty.call(words, source) ? words[source] : source;
    if (args) Object.keys(args).forEach((k) => { s = s.split(k).join(String(args[k])); });
    return s;
  };
  const read = (key) => { try { return window.localStorage.getItem(key); } catch (e) { return null; } };
  const write = (key, value) => { try { window.localStorage.setItem(key, value); } catch (e) {} };

  // el('button', {type: 'button', class: 'x', onclick: fn}, 'text', child...)
  const el = (tag, attrs, ...children) => {
    const node = document.createElement(tag);
    Object.entries(attrs || {}).forEach(([k, v]) => {
      if (v === null || v === undefined || v === false) return;
      if (k.startsWith('on') && typeof v === 'function') node.addEventListener(k.slice(2), v);
      else if (k === 'class') node.className = v;
      else if (k === 'text') node.textContent = v;
      else if (k in node && typeof v !== 'string') node[k] = v;
      else node.setAttribute(k, v === true ? '' : v);
    });
    children.flat().forEach((c) => { if (c !== null && c !== undefined && c !== false) node.append(c instanceof Node ? c : document.createTextNode(String(c))); });
    return node;
  };
  let uid = 0;
  const nextId = (p) => 'exp-debug-' + p + '-' + (++uid);
  const hint = (text, level) => el('p', { class: 'exp-debug-hint' + (level ? ' exp-debug-' + level : ''), role: level === 'high' || level === 'warn' ? 'alert' : null }, text);

  /* ---------------------------------------------------------------- tabs */
  const tabs = Array.from(root.querySelectorAll('.exp-debug-tab'));
  const panelOf = (tab) => document.getElementById(tab.getAttribute('aria-controls'));
  const loaders = {};
  const loaded = {};
  const select = (id, focus) => {
    let found = false;
    tabs.forEach((tab) => {
      const on = tab.getAttribute('data-tab') === id;
      found = found || on;
      tab.setAttribute('aria-selected', on ? 'true' : 'false');
      tab.tabIndex = on ? 0 : -1;
      const panel = panelOf(tab);
      if (panel) panel.hidden = !on;
      if (on && focus) tab.focus();
    });
    if (!found && tabs.length) return select(tabs[0].getAttribute('data-tab'), focus);
    write('exp-debug-tab', id);
    // The settings and cache tabs ask the server only when they are seen, not on every page view.
    const details = document.getElementById('debug-details');
    if (loaders[id] && !loaded[id] && details && details.classList.contains('active')) { loaded[id] = true; loaders[id](); }
  };
  tabs.forEach((tab, i) => {
    tab.addEventListener('click', () => select(tab.getAttribute('data-tab')));
    tab.addEventListener('keydown', (e) => {
      let j = null;
      if (e.key === 'ArrowRight') j = (i + 1) % tabs.length;
      else if (e.key === 'ArrowLeft') j = (i - 1 + tabs.length) % tabs.length;
      else if (e.key === 'Home') j = 0;
      else if (e.key === 'End') j = tabs.length - 1;
      if (j !== null) { e.preventDefault(); select(tabs[j].getAttribute('data-tab'), true); }
    });
  });
  const openPanel = () => { if (window.expDebugBarSetOpen) window.expDebugBarSetOpen(true); };

  // Escape closes the panel and puts the focus back on the bar.
  root.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape' || e.target.closest('input, select, textarea')) return;
    const details = document.getElementById('debug-details');
    if (details && details.classList.contains('active') && window.expDebugBarSetOpen) {
      window.expDebugBarSetOpen(false);
      const a = root.querySelector('h2 > a'); if (a) a.focus();
    }
  });

  /* ---------------------------------------------------------------- message levels */
  const levelBox = root.querySelector('.exp-debug-levels');
  let levelFilter = 'all';
  const applyLevel = () => {
    root.querySelectorAll('#main-debug-table tr[data-level]').forEach((tr) => {
      tr.classList.toggle('exp-debug-level-hidden', levelFilter !== 'all' && tr.getAttribute('data-level') !== levelFilter);
    });
    if (levelBox) levelBox.querySelectorAll('button').forEach((b) => b.setAttribute('aria-pressed', b.getAttribute('data-level') === levelFilter ? 'true' : 'false'));
  };
  if (levelBox) {
    let counts = {};
    try { counts = JSON.parse(levelBox.getAttribute('data-counts') || '{}'); } catch (e) {}
    const names = { error: t('Errors'), warning: t('Warnings'), notice: t('Notices'), debug: t('Debug'), timing: t('Timing points'), strict: t('Strict') };
    levelBox.setAttribute('role', 'group');
    levelBox.setAttribute('aria-label', t('Show messages'));
    const total = Object.values(counts).reduce((a, b) => a + b, 0);
    levelBox.append(el('button', { type: 'button', class: 'exp-debug-button', 'data-level': 'all', onclick: () => { levelFilter = 'all'; applyLevel(); } }, t('All') + ' (' + total + ')'));
    Object.keys(names).forEach((lvl) => {
      if (!counts[lvl]) return;
      levelBox.append(el('button', { type: 'button', class: 'exp-debug-button', 'data-level': lvl, onclick: () => { levelFilter = lvl; applyLevel(); } }, names[lvl] + ' (' + counts[lvl] + ')'));
    });
    applyLevel();
  }

  /* ---------------------------------------------------------------- copy messages */
  // The messages that are shown (level and filter applied) as plain text, as selecting the list would give
  // them: "Level: source<TAB>time", then the message, a blank line between entries.
  const shownMessagesText = () => {
    const entries = [];
    root.querySelectorAll('#main-debug-table tr[data-level]:not(.debugbody)').forEach((tr) => {
      if (tr.classList.contains('exp-debug-level-hidden') || tr.classList.contains('exp-debug-filter-hidden')) return;
      const cells = tr.cells;
      const head = (cells[0] ? cells[0].textContent : '').trim() + (cells[1] ? '\t' + cells[1].textContent.trim() : '');
      const body = tr.nextElementSibling && tr.nextElementSibling.classList.contains('debugbody') ? tr.nextElementSibling : null;
      const text = body ? body.textContent.replace(/\s+$/, '') : '';
      entries.push(text !== '' ? head + '\n' + text : head);
    });
    return { count: entries.length, text: entries.join('\n\n') };
  };
  const copyByTextarea = (text) => {
    const area = el('textarea', { readonly: 'readonly', 'aria-hidden': 'true', tabindex: '-1',
      style: 'position:fixed;top:0;left:-9999px;width:1px;height:1px;opacity:0;' });
    area.value = text;
    document.body.append(area);
    const active = document.activeElement;
    let ok = false;
    try { area.select(); area.setSelectionRange(0, text.length); ok = document.execCommand('copy'); } catch (e) { ok = false; }
    area.remove();
    if (active && active.focus) active.focus();
    return ok;
  };
  const copyText = (text) => {
    if (navigator.clipboard && navigator.clipboard.writeText && window.isSecureContext) {
      return navigator.clipboard.writeText(text).then(() => true, () => copyByTextarea(text));
    }
    return Promise.resolve(copyByTextarea(text));
  };
  const copyButton = root.querySelector('.exp-debug-copy-messages');
  const copyStatus = root.querySelector('.exp-debug-copy-status');
  if (copyButton) {
    let statusTimer = null;
    const say = (text, level) => {
      if (!copyStatus) return;
      copyStatus.textContent = text;
      copyStatus.className = 'exp-debug-copy-status' + (level ? ' exp-debug-' + level : '');
      clearTimeout(statusTimer);
      statusTimer = setTimeout(() => { copyStatus.textContent = ''; copyStatus.className = 'exp-debug-copy-status'; }, 4000);
    };
    copyButton.hidden = false;
    copyButton.addEventListener('click', () => {
      const shown = shownMessagesText();
      if (!shown.count) { say(t('No messages to copy.'), 'warn'); return; }
      copyText(shown.text).then((ok) => {
        if (ok) say(shown.count === 1 ? t('Copied 1 message') : t('Copied %count messages', { '%count': shown.count }), 'ok');
        else say(t('Could not copy the messages.'), 'high');
      });
    });
  }

  /* ---------------------------------------------------------------- summary chips */
  root.querySelectorAll('.exp-debug-chip').forEach((chip) => {
    chip.addEventListener('click', (e) => {
      e.preventDefault(); e.stopPropagation();
      openPanel();
      const key = chip.getAttribute('data-key');
      if (key === 'warnings' || key === 'errors') { levelFilter = key === 'errors' ? 'error' : 'warning'; applyLevel(); }
      select(chip.getAttribute('data-tab'));
    });
  });

  /* ---------------------------------------------------------------- SQL sort */
  const sqlTable = document.getElementById('exp-debug-sql-table');
  const sqlTools = root.querySelector('.exp-debug-sql-tools');
  if (sqlTable && sqlTools) {
    const body = sqlTable.tBodies[0] || sqlTable;
    const pairs = [];
    Array.from(body.rows).forEach((tr) => { if (tr.hasAttribute('data-ms')) pairs.push([tr, tr.nextElementSibling]); });
    let byTime = false;
    const btn = el('button', { type: 'button', class: 'exp-debug-button', 'aria-pressed': 'false' }, t('Slowest first'));
    btn.addEventListener('click', () => {
      byTime = !byTime;
      btn.setAttribute('aria-pressed', byTime ? 'true' : 'false');
      btn.textContent = byTime ? t('Sort by order') : t('Slowest first');
      const sorted = pairs.slice().sort((a, b) => byTime
        ? parseFloat(b[0].getAttribute('data-ms')) - parseFloat(a[0].getAttribute('data-ms'))
        : parseInt(a[0].getAttribute('data-n'), 10) - parseInt(b[0].getAttribute('data-n'), 10));
      sorted.forEach(([h, b2]) => { body.append(h); if (b2) body.append(b2); });
    });
    sqlTools.append(btn);
  }

  /* ---------------------------------------------------------------- filter box */
  const filter = document.getElementById('exp-debug-filter');
  const filterRows = (panel) => {
    const rows = [];
    panel.querySelectorAll('table tr').forEach((tr) => {
      if (tr.querySelector('th') && !tr.querySelector('td')) return;          // header rows stay
      if (tr.classList.contains('debugbody')) return;                          // goes with its header row
      if (tr.closest('.exp-debug-classic, .exp-debug-iplist')) return;
      const partner = tr.hasAttribute('data-level') && tr.nextElementSibling && tr.nextElementSibling.classList.contains('debugbody') ? tr.nextElementSibling : null;
      rows.push({ nodes: partner ? [tr, partner] : [tr] });
    });
    panel.querySelectorAll('.exp-debug-setting, .exp-debug-cache-row').forEach((n) => rows.push({ nodes: [n] }));
    return rows;
  };
  const runFilter = () => {
    const q = (filter.value || '').trim().toLowerCase();
    tabs.forEach((tab) => {
      const panel = panelOf(tab);
      if (!panel) return;
      let shown = 0;
      const rows = filterRows(panel);
      rows.forEach((r) => {
        const text = r.nodes.map((n) => n.textContent).join(' ').toLowerCase();
        const hit = !q || text.indexOf(q) !== -1;
        r.nodes.forEach((n) => n.classList.toggle('exp-debug-filter-hidden', !hit));
        if (hit) shown++;
      });
      if (q && rows.length) tab.setAttribute('data-matches', String(shown)); else tab.removeAttribute('data-matches');
      tab.title = q && rows.length ? t('%count rows match', { '%count': shown }) : '';
      let note = panel.querySelector(':scope > .exp-debug-empty');
      if (q && rows.length && !shown) {
        if (!note) { note = el('p', { class: 'exp-debug-empty', role: 'status' }, t('No rows match the filter.')); panel.insertBefore(note, panel.children[1] || null); }
      } else if (note) note.remove();
    });
  };
  if (filter) {
    filter.setAttribute('aria-controls', tabs.map((tb) => tb.getAttribute('aria-controls')).join(' '));
    filter.addEventListener('input', runFilter);
    filter.addEventListener('keydown', (e) => { if (e.key === 'Escape' && filter.value) { e.stopPropagation(); filter.value = ''; runFilter(); } });
  }

  /* ---------------------------------------------------------------- server functions */
  let token = '';
  const metaToken = document.querySelector('meta[name="csrf-token"]');
  if (metaToken) token = metaToken.getAttribute('content') || '';
  if (!token) { const input = document.querySelector('input[name="ezxform_token"]'); if (input) token = input.value; }
  const callUrl = (data.urls && data.urls.call) || '/ezjscore/call';
  const call = (fn, params, post) => {
    const body = new URLSearchParams();
    Object.entries(params || {}).forEach(([k, v]) => { if (v !== undefined && v !== null) body.append(k, typeof v === 'object' ? JSON.stringify(v) : String(v)); });
    let url = callUrl.replace(/\/$/, '') + '/expdebugbar::' + fn + '?ContentType=json';
    const opts = { credentials: 'same-origin', headers: { Accept: 'application/json' } };
    if (post) {
      if (token) { body.append('ezxform_token', token); opts.headers['X-CSRF-Token'] = token; }
      opts.method = 'POST';
      opts.body = body;
      opts.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
    } else if ([...body.keys()].length) {
      url += '&' + body.toString();
    }
    return fetch(url, opts).then((r) => r.text().then((text) => {
      let json = null;
      try { json = JSON.parse(text); } catch (e) { throw new Error(r.status + ' ' + (r.statusText || 'no JSON')); }
      if (json && json.error_text) throw new Error(json.error_text);
      if (!r.ok) throw new Error(String(r.status));
      return json ? json.content : null;
    }));
  };
  const failure = (box, err, retry) => {
    box.replaceChildren(hint(t('Could not load: %error', { '%error': err && err.message ? err.message : String(err) }), 'warn'),
      retry ? el('button', { type: 'button', class: 'exp-debug-button', onclick: retry }, t('Retry')) : null);
  };
  const reloadNote = () => el('p', { class: 'exp-debug-hint', role: 'status' }, t('Reload the page to see the effect.') + ' ',
    el('button', { type: 'button', class: 'exp-debug-button', onclick: () => window.location.reload() }, t('Reload now')));

  /* ---------------------------------------------------------------- IP helpers (client side validation) */
  const parseV4 = (s) => {
    const m = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/.exec(s);
    if (!m) return null;
    const parts = m.slice(1).map(Number);
    return parts.every((p) => p <= 255) ? parts : null;
  };
  const parseV6 = (s) => {
    if (!/^[0-9a-f:.]+$/i.test(s) || s.indexOf(':') === -1) return null;
    let tail4 = null;
    if (s.indexOf('.') !== -1) { const k = s.lastIndexOf(':'); tail4 = parseV4(s.slice(k + 1)); if (!tail4) return null; s = s.slice(0, k + 1) + '0:0'; }
    const halves = s.split('::');
    if (halves.length > 2) return null;
    const left = halves[0] ? halves[0].split(':') : [];
    const right = halves.length === 2 && halves[1] ? halves[1].split(':') : [];
    if ([...left, ...right].some((g) => !/^[0-9a-f]{1,4}$/i.test(g))) return null;
    const missing = 8 - left.length - right.length;
    if (halves.length === 1 ? missing !== 0 : missing < 1) return null;
    const groups = [...left, ...Array(halves.length === 2 ? missing : 0).fill('0'), ...right].map((g) => parseInt(g, 16));
    if (tail4) { groups[6] = tail4[0] * 256 + tail4[1]; groups[7] = tail4[2] * 256 + tail4[3]; }
    return groups;
  };
  // { ok, family, error } for "address" or "address/prefix"
  const validateCidr = (value) => {
    const v = (value || '').trim();
    if (!v) return { ok: false, error: t('Not a valid IPv4 or IPv6 address or CIDR range.') };
    const [addr, prefix, extra] = v.split('/');
    if (extra !== undefined) return { ok: false, error: t('Not a valid IPv4 or IPv6 address or CIDR range.') };
    const v4 = parseV4(addr);
    const v6 = v4 ? null : parseV6(addr);
    if (!v4 && !v6) return { ok: false, error: t('Not a valid IPv4 or IPv6 address or CIDR range.') };
    if (prefix !== undefined) {
      if (!/^\d{1,3}$/.test(prefix)) return { ok: false, error: t('Not a valid IPv4 or IPv6 address or CIDR range.') };
      const n = parseInt(prefix, 10);
      if (v4 && n > 32) return { ok: false, error: t('A prefix length must be 0 to 32 for IPv4.') };
      if (v6 && n > 128) return { ok: false, error: t('A prefix length must be 0 to 128 for IPv6.') };
    }
    return { ok: true, family: v4 ? 4 : 6 };
  };
  window.expDebugBarValidateCidr = validateCidr;   // for the bar's tests

  /* ---------------------------------------------------------------- settings */
  const settingsBox = root.querySelector('.exp-debug-settings');
  let settingsState = null;

  const valueText = (v) => {
    if (v === null || v === undefined) return t('not set');
    if (Array.isArray(v)) return v.length ? v.join(', ') : '[]';
    return String(v);
  };
  const originLabel = (origin) => {
    if (!origin) return t('not set');
    const kinds = { default: t('default'), global: t('global override'), siteaccess: t('siteaccess'), extension: t('extension') };
    return (kinds[origin.kind] || origin.kind || origin.scope || '') + (origin.kind === 'siteaccess' || origin.kind === 'extension' ? ' ' + String(origin.scope || '').replace(/^[a-z]+:/, '') : '');
  };

  // A write: handles the lock-out / open confirmations, then hands the answer to done().
  const writeSetting = (params, status, done) => {
    status.replaceChildren(t('Loading...'));
    const go = (confirm) => call('set', Object.assign({ siteaccess: settingsState && settingsState.siteaccess }, params, confirm ? { confirm: 1 } : {}), true)
      .then((answer) => {
        if (answer && answer.needs_confirm) {
          if (window.confirm(answer.needs_confirm.message || answer.needs_confirm.reason)) return go(true);
          status.replaceChildren(t('Not changed'));
          return null;
        }
        if (!answer || !answer.ok) throw new Error((answer && (answer.error || answer.message)) || 'refused');
        status.replaceChildren(answer.changed === false ? t('Not changed') : t('Applied'));
        (answer.warnings || []).forEach((w) => status.append(' ', w));
        if (answer.entry) addLog(answer.entry, true);
        if (answer.setting) updateSetting(answer.setting);
        if (answer.changed !== false) status.append(' ', reloadNote());
        if (done) done(answer);
        return answer;
      })
      .catch((err) => { status.replaceChildren(el('span', { class: 'exp-debug-field-error', role: 'alert' }, t('Could not load: %error', { '%error': err.message }))); });
    return go(false);
  };

  // Short option text (the label without its directory); the directory is in the option's title.
  const scopeText = (s) => String(s.label || s.name).replace(/\s*\([^)]*\/[^)]*\)\s*$/, '');
  const scopeSelect = (setting, id) => {
    const select = el('select', { id: id });
    (settingsState.scopes || []).filter((s) => s.writable !== false).forEach((s) => {
      const value = setting && setting.scopes && Object.prototype.hasOwnProperty.call(setting.scopes, s.name) ? setting.scopes[s.name] : undefined;
      select.append(el('option', { value: s.name, selected: s.name === settingsState.default_scope, title: s.dir || null },
        scopeText(s) + (value !== undefined && value !== null ? ' = ' + valueText(value) : '')));
    });
    return select;
  };

  const settingRows = {};
  const updateSetting = (setting) => {
    const idx = settingsState.settings.findIndex((s) => s.id === setting.id);
    if (idx !== -1) settingsState.settings[idx] = setting;
    const old = settingRows[setting.id];
    if (old) {
      // Redrawn with the new value; the status line (what was written, the reload note) stays.
      const keep = old.querySelector(':scope > .exp-debug-setting-status');
      const row = renderSetting(setting);
      const fresh = row.querySelector(':scope > .exp-debug-setting-status');
      if (keep && fresh) fresh.replaceWith(keep);
      old.replaceWith(row);
    }
    if (setting.type === 'iplist' || setting.variable === 'DebugByIP') refreshIp();
  };

  const renderSetting = (s) => {
    const base = nextId('set');
    const status = el('div', { class: 'exp-debug-setting-status', 'aria-live': 'polite' });
    const canWrite = !!settingsState.can_write && !s.secret;
    let control;
    let getValue;
    if (s.type === 'bool') {
      control = el('input', { type: 'checkbox', id: base + '-v', checked: !!s.is_on, disabled: !canWrite });
      getValue = () => (control.checked ? (s.on || 'enabled') : (s.off || 'disabled'));
      control = el('label', { class: 'exp-debug-bool' }, control, ' ', s.is_on ? t('on') : t('off'));
      const box = control.querySelector('input');
      box.addEventListener('change', () => { control.lastChild.textContent = box.checked ? t('on') : t('off'); });
      getValue = () => (box.checked ? (s.on || 'enabled') : (s.off || 'disabled'));
    } else if (s.type === 'enum') {
      control = el('select', { id: base + '-v', disabled: !canWrite });
      (s.values || []).forEach((v) => control.append(el('option', { value: v, selected: v === s.effective }, v)));
      if (s.effective !== null && s.effective !== undefined && (s.values || []).indexOf(s.effective) === -1) control.append(el('option', { value: s.effective, selected: true }, s.effective));
      getValue = () => control.value;
    } else if (s.type === 'list' || s.type === 'userlist') {
      control = el('textarea', { id: base + '-v', rows: 3, disabled: !canWrite, 'aria-describedby': base + '-lh' });
      control.value = Array.isArray(s.effective) ? s.effective.join('\n') : (s.effective || '');
      getValue = () => control.value.split('\n').map((x) => x.trim()).filter(Boolean);
    } else if (s.type === 'iplist') {
      control = null;
    } else {
      control = el('input', { type: 'text', id: base + '-v', value: s.secret ? '' : (s.effective === null || s.effective === undefined ? '' : String(s.effective)), disabled: !canWrite, placeholder: s.secret ? '********' : null });
      getValue = () => control.value;
    }

    const name = el('div', {}, el('label', { class: 'exp-debug-setting-name', for: control ? base + '-v' : null }, s.label || s.variable),
      el('span', { class: 'exp-debug-setting-key' }, s.file + ' [' + s.block + '] ' + s.variable + (s.extension ? ' (' + s.extension + ')' : '')),
      s.help ? el('span', { class: 'exp-debug-setting-help', id: base + '-h' }, s.help) : null);

    const effective = el('div', {},
      el('span', { class: 'exp-debug-sr' }, t('Effective value') + ': '),
      el('span', { class: 'exp-debug-mono' }, s.secret ? '********' : valueText(s.effective)), ' ',
      el('span', { class: 'exp-debug-origin', 'data-origin': s.origin ? s.origin.kind : 'none', title: s.origin && s.origin.path ? s.origin.path : '' },
        t('Comes from') + ': ' + originLabel(s.origin)));

    const row = el('div', { class: 'exp-debug-setting', 'data-setting': s.id, 'data-type': s.type });
    row.append(name);
    if (s.type === 'userlist') {
      const lh = el('span', { class: 'exp-debug-setting-help', id: base + '-lh' }, t('One entry per line'));
      const me = settingsState.user && settingsState.user.id;
      const add = el('button', { type: 'button', class: 'exp-debug-button', disabled: !canWrite || !me, onclick: () => {
        const lines = getValue();
        if (me && lines.indexOf(String(me)) === -1) control.value = lines.concat(String(me)).join('\n');
      } }, t('Add me'));
      const lookup = el('input', { type: 'text', id: base + '-u', placeholder: t('User ID or name'), disabled: !canWrite, inputmode: 'numeric' });
      const addId = el('button', { type: 'button', class: 'exp-debug-button', disabled: !canWrite, onclick: () => {
        const v = lookup.value.trim();
        if (!/^\d+$/.test(v)) { lookup.setAttribute('aria-invalid', 'true'); status.replaceChildren(el('span', { class: 'exp-debug-field-error', role: 'alert' }, t('No user found.'))); return; }
        lookup.removeAttribute('aria-invalid');
        const lines = getValue();
        if (lines.indexOf(v) === -1) control.value = lines.concat(v).join('\n');
        lookup.value = '';
      } }, t('Add'));
      row.append(el('div', {}, control, lh, el('div', { class: 'exp-debug-row' }, add, el('label', { for: base + '-u', class: 'exp-debug-sr' }, t('Find a user')), lookup, addId)));
      const enabled = (settingsState.settings.find((x) => x.variable === 'DebugByUser') || {}).is_on;
      if (enabled && me && Array.isArray(s.effective) && s.effective.indexOf(String(me)) === -1) row.append(hint(t('Warning: you are not in the list. Applying it locks you out of the debug output.'), 'warn'));
    } else if (s.type === 'list') {
      row.append(el('div', {}, control, el('span', { class: 'exp-debug-setting-help', id: base + '-lh' }, t('One entry per line'))));
    } else if (control) {
      row.append(el('div', {}, control));
    }
    row.append(effective);

    if (s.type !== 'iplist') {
      const scopeId = base + '-s';
      const scope = scopeSelect(s, scopeId);
      scope.disabled = !canWrite;
      const apply = el('button', { type: 'button', class: 'exp-debug-button exp-debug-primary', disabled: !canWrite }, t('Apply'));
      apply.addEventListener('click', () => {
        const v = getValue();
        writeSetting(Array.isArray(v) ? { setting: s.id, scope: scope.value, op: 'replace', value: JSON.stringify(v) }
                                      : { setting: s.id, scope: scope.value, op: 'set', value: v }, status);
      });
      const reset = el('button', { type: 'button', class: 'exp-debug-button', disabled: !canWrite, title: t('Reset') }, t('Reset'));
      reset.addEventListener('click', () => writeSetting({ setting: s.id, scope: scope.value, op: 'unset' }, status));
      const syncReset = () => { reset.disabled = !canWrite || !s.scopes || s.scopes[scope.value] === null || s.scopes[scope.value] === undefined; };
      scope.addEventListener('change', syncReset); syncReset();
      row.append(el('div', { class: 'exp-debug-scope' }, el('label', { for: scopeId }, t('Write to')), scope, el('div', {}, apply, reset)));
    } else {
      row.append(el('div', {}));
      row.append(renderIpList(s, status, canWrite));
    }
    row.append(status);
    settingRows[s.id] = row;
    return row;
  };

  /* ---- IP list super controls */
  let ipRefreshers = [];
  const refreshIp = () => {
    call('iptest', {}).then((ip) => { settingsState.ip = ip; ipRefreshers.forEach((f) => f(ip)); }).catch(() => {});
  };
  const networkOf = (ip) => {
    const v4 = parseV4(ip);
    if (v4) return v4.slice(0, 3).join('.') + '.0/24';
    const v6 = parseV6(ip);
    if (v6) return v6.slice(0, 4).map((g) => g.toString(16)).join(':') + '::/64';
    return '';
  };

  const renderIpList = (s, status, canWrite) => {
    const box = el('div', { class: 'exp-debug-iplist' });
    const base = nextId('ip');
    const scopeId = base + '-s';
    const scope = scopeSelect(s, scopeId);
    scope.disabled = !canWrite;
    const send = (op, value) => writeSetting({ setting: s.id, scope: scope.value, op: op, value: typeof value === 'object' ? JSON.stringify(value) : value }, status);

    const info = el('div', { 'aria-live': 'polite' });
    const table = el('table', {}, el('thead', {}, el('tr', {}, el('th', { scope: 'col' }, 'CIDR'), el('th', { scope: 'col' }, t('Label')), el('th', { scope: 'col' }, t('Expires')), el('th', { scope: 'col' }, ''))), el('tbody'));
    const warnings = el('div', {});

    const draw = (ip) => {
      ip = ip || {};
      const me = ip.client_ip || ip.address || (data.client && data.client.ip) || '';
      info.replaceChildren(
        el('p', { class: 'exp-debug-hint' }, t('Your address as the server sees it: %ip', { '%ip': me || '?' }) + (ip.trusts_proxy && ip.remote_addr && ip.remote_addr !== me ? ' (REMOTE_ADDR ' + ip.remote_addr + ', ' + (ip.proxy_header || '') + ')' : '')),
        el('p', { class: 'exp-debug-hint' }, ip.request_match ? t('This request matched: %entry', { '%entry': ip.request_match.line || ip.request_match.address }) : t('This request matched no entry.')));
      const tbody = table.tBodies[0];
      tbody.replaceChildren();
      const entries = ip.entries || (Array.isArray(s.effective) ? s.effective.map((line, index) => ({ index, line, address: line.split(';')[0].trim(), valid: true })) : []);
      entries.forEach((e) => {
        const matched = ip.request_match && ip.request_match.index === e.index;
        tbody.append(el('tr', { class: (matched || e.matches ? 'exp-debug-matched' : '') + (e.expired ? ' exp-debug-expired' : '') },
          el('td', { class: 'exp-debug-mono' }, e.address || e.line, e.valid === false ? el('span', { class: 'exp-debug-field-error' }, ' ' + (e.error || '')) : null),
          el('td', {}, e.label || ''),
          el('td', {}, e.expired ? t('expired') : (e.expires ? t('expires %time', { '%time': e.expires.replace('T', ' ').slice(0, 16) }) : t('Until removed'))),
          el('td', {}, el('button', { type: 'button', class: 'exp-debug-button', disabled: !canWrite, 'aria-label': t('Remove') + ' ' + (e.address || e.line), onclick: () => send('remove', e.line) }, t('Remove')))));
      });
      warnings.replaceChildren(...(ip.warnings || []).map((w) => hint(w.message || w.code, w.code === 'lockout' || w.code === 'open' ? 'high' : 'warn')));
      if (!entries.length) tbody.append(el('tr', {}, el('td', { colspan: 4, class: 'exp-debug-empty' }, t('not set'))));
      if (ip.debug_by_ip && me && !ip.request_match && !(ip.warnings || []).some((w) => w.code === 'lockout')) warnings.append(hint(t('Warning: your own address is not in the list. Applying it locks you out of the debug output.'), 'warn'));
      if (ip.debug_by_ip && !entries.length) warnings.append(hint(t('Warning: the list is empty. With "Debug by IP" on, nobody gets the debug output.'), 'high'));
      entries.forEach((e) => { if (/^(0\.0\.0\.0\/0|::\/0)$/.test((e.address || '').trim())) warnings.append(hint(t('Warning: %entry opens the debug output to everyone.', { '%entry': e.address }), 'high')); });
      quick.replaceChildren();
      const fam = parseV4(me) ? 4 : (parseV6(me) ? 6 : 0);
      if (fam) {
        const single = me + (fam === 4 ? '/32' : '/128');
        const net = (ip.suggest && ip.suggest.network) || networkOf(me);
        quick.append(
          el('button', { type: 'button', class: 'exp-debug-button', disabled: !canWrite, onclick: () => send('add', { address: (ip.suggest && ip.suggest.self) || single, label: label.value.trim(), expires: expiry.value }) },
            (fam === 4 ? t('Add my IPv4 address') : t('Add my IPv6 address')) + ' (' + me + ')'),
          el('button', { type: 'button', class: 'exp-debug-button', disabled: !canWrite, onclick: () => send('add', { address: net, label: label.value.trim(), expires: expiry.value }) },
            (fam === 4 ? t('Add my /24') : t('Add my /64')) + ' (' + net + ')'));
      }
    };

    // add
    const addr = el('input', { type: 'text', id: base + '-a', class: 'exp-debug-mono', size: 28, disabled: !canWrite, 'aria-describedby': base + '-ah ' + base + '-ae', autocomplete: 'off', spellcheck: 'false' });
    const addrErr = el('span', { id: base + '-ae', class: 'exp-debug-field-error', role: 'alert' });
    const label = el('input', { type: 'text', id: base + '-l', size: 14, disabled: !canWrite });
    const expiry = el('select', { id: base + '-x', disabled: !canWrite },
      el('option', { value: '+1h' }, t('For 1 hour')), el('option', { value: 'today' }, t('Today')), el('option', { value: '', selected: true }, t('Until removed')));
    const check = () => {
      if (!addr.value.trim()) { addr.removeAttribute('aria-invalid'); addrErr.textContent = ''; return false; }
      const r = validateCidr(addr.value);
      const lines = Array.isArray(s.effective) ? s.effective.map((l) => l.split(';')[0].trim()) : [];
      if (r.ok && lines.indexOf(addr.value.trim()) !== -1) { r.ok = false; r.error = t('Already in the list.'); }
      addr.setAttribute('aria-invalid', r.ok ? 'false' : 'true');
      addrErr.textContent = r.ok ? '' : r.error;
      return r.ok;
    };
    addr.addEventListener('input', check);
    const add = el('button', { type: 'button', class: 'exp-debug-button exp-debug-primary', disabled: !canWrite, onclick: () => {
      if (!check()) { addr.focus(); if (!addr.value.trim()) { addr.setAttribute('aria-invalid', 'true'); addrErr.textContent = t('Not a valid IPv4 or IPv6 address or CIDR range.'); } return; }
      send('add', { address: addr.value.trim(), label: label.value.trim(), expires: expiry.value });
    } }, t('Add'));
    const quick = el('div', { class: 'exp-debug-row' });

    // test
    const testIn = el('input', { type: 'text', id: base + '-t', class: 'exp-debug-mono', size: 24, autocomplete: 'off', spellcheck: 'false' });
    const testOut = el('span', { 'aria-live': 'polite', class: 'exp-debug-mono' });
    const test = el('button', { type: 'button', class: 'exp-debug-button', onclick: () => {
      const v = testIn.value.trim();
      const r = validateCidr(v);
      if (!r.ok || v.indexOf('/') !== -1) { testIn.setAttribute('aria-invalid', 'true'); testOut.textContent = t('Not a valid IPv4 or IPv6 address or CIDR range.'); return; }
      testIn.removeAttribute('aria-invalid');
      testOut.textContent = t('Loading...');
      call('iptest', { address: v }).then((ans) => {
        testOut.textContent = ans && ans.matched ? t('matches %entry', { '%entry': ans.matched.line || ans.matched.address }) : t('matches no entry');
      }).catch((err) => { testOut.textContent = t('Could not load: %error', { '%error': err.message }); });
    } }, t('Test'));
    testIn.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); test.click(); } });
    addr.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); add.click(); } });

    box.append(info, table, warnings,
      el('fieldset', {}, el('legend', {}, t('Add')),
        el('div', { class: 'exp-debug-row' },
          el('label', { for: base + '-a' }, 'CIDR'), addr,
          el('label', { for: base + '-l' }, t('Label')), label,
          el('label', { for: base + '-x' }, t('Expires')), expiry, add),
        el('p', { class: 'exp-debug-setting-help', id: base + '-ah' }, t('IPv4 or IPv6 address or CIDR range, for example 192.0.2.10, 192.0.2.0/24, 2001:db8::/64')),
        addrErr, quick,
        el('div', { class: 'exp-debug-row exp-debug-scope' }, el('label', { for: scopeId }, t('Write to')), scope)),
      el('div', { class: 'exp-debug-row' }, el('label', { for: base + '-t' }, t('Test an address')), testIn, test, testOut));
    ipRefreshers.push(draw);
    draw(settingsState.ip);
    return box;
  };

  /* ---- presets */
  // Kept across redraws of the tab: the last preset applied (for Revert), the scope picked, the message.
  let lastPresetGroup = null;
  let presetScope = null;
  const presetStatus = el('span', { 'aria-live': 'polite', class: 'exp-debug-hint exp-debug-preset-status' });
  const renderPresets = () => {
    const box = el('div', { class: 'exp-debug-presets', role: 'group', 'aria-label': t('Presets') });
    const status = presetStatus;
    const canWrite = !!settingsState.can_write;
    const scopeId = nextId('ps');
    const scope = el('select', { id: scopeId, disabled: !canWrite });
    (settingsState.scopes || []).filter((s) => s.writable !== false).forEach((s) => scope.append(el('option', { value: s.name, selected: s.name === (presetScope || settingsState.default_scope), title: s.dir || null }, scopeText(s))));
    scope.addEventListener('change', () => { presetScope = scope.value; });
    const revert = el('button', { type: 'button', class: 'exp-debug-button', hidden: !lastPresetGroup, onclick: () => {
      if (!lastPresetGroup) return;
      status.textContent = t('Loading...');
      call('undo', { group: lastPresetGroup }, true).then((ans) => {
        status.textContent = t('Preset reverted.');
        revert.hidden = true; lastPresetGroup = null;
        loadSettings(true);
        status.append(' ', reloadNote());
        return ans;
      }).catch((err) => { status.textContent = t('Could not load: %error', { '%error': err.message }); });
    } }, t('Revert preset'));
    box.append(el('strong', {}, t('Presets') + ':'));
    (settingsState.presets || []).forEach((p) => {
      box.append(el('button', { type: 'button', class: 'exp-debug-button', disabled: !canWrite, 'data-preset': p.id, title: p.description || '', onclick: () => {
        status.textContent = t('Loading...');
        const go = (confirm) => call('preset', Object.assign({ preset: p.id, scope: scope.value, siteaccess: settingsState.siteaccess }, confirm ? { confirm: 1 } : {}), true).then((ans) => {
          if (ans && ans.needs_confirm) {
            if (window.confirm(ans.needs_confirm.message || ans.needs_confirm.reason)) return go(true);
            status.textContent = t('Not changed'); return null;
          }
          if (!ans || !ans.ok) throw new Error((ans && ans.message) || 'refused');
          lastPresetGroup = ans.group || ((ans.entries || [])[0] || {}).group || null;
          revert.hidden = !lastPresetGroup;
          status.textContent = t('Preset "%name" applied.', { '%name': p.name });
          status.append(' ', reloadNote());
          loadSettings(true);
          return ans;
        });
        go(false).catch((err) => { status.textContent = t('Could not load: %error', { '%error': err.message }); });
      } }, p.name));
      // A preset the user saved can be removed again (the shipped ones cannot).
      if (p.source === 'user' && canWrite) box.append(el('button', { type: 'button', class: 'exp-debug-button', 'aria-label': t('Remove') + ': ' + p.name, title: t('Remove'), onclick: () => {
        call('preset', { 'delete': p.id }, true).then(() => loadSettings(true)).catch((err) => { status.textContent = t('Could not load: %error', { '%error': err.message }); });
      } }, 'x'));
    });
    box.append(revert, el('label', { for: scopeId }, t('Write to')), scope);
    if (canWrite) {
      const nameId = nextId('pn');
      const name = el('input', { type: 'text', id: nameId, size: 14, placeholder: t('Preset name') });
      box.append(el('label', { for: nameId, class: 'exp-debug-sr' }, t('Preset name')), name,
        el('button', { type: 'button', class: 'exp-debug-button', onclick: () => {
          if (!name.value.trim()) { name.setAttribute('aria-invalid', 'true'); name.focus(); return; }
          name.removeAttribute('aria-invalid');
          call('preset', { save: name.value.trim(), snapshot: 1 }, true).then(() => { loadSettings(true); }).catch((err) => { status.textContent = t('Could not load: %error', { '%error': err.message }); });
        } }, t('Save current as preset')));
    }
    box.append(status);
    return box;
  };

  /* ---- change log */
  let logList = null;
  const describe = (e) => {
    if (e.op === 'cache') return (e.user || '') + ': ' + t('Cache') + ' ' + (e.names || e.by || '') ;
    return t('%user changed %setting from %old to %new in %scope', {
      '%user': e.user || ('#' + (e.user_id || '?')), '%setting': (e.block ? '[' + e.block + '] ' : '') + (e.variable || e.setting || ''),
      '%old': valueText(e.old), '%new': valueText(e.new), '%scope': e.scope || '' });
  };
  const logItem = (e) => {
    const li = el('li', { 'data-entry': e.id }, el('time', { datetime: e.time || '' }, (e.time || '').replace('T', ' ').slice(0, 19)), ' ', describe(e), ' ');
    if (e.op === 'undo') li.append(el('em', {}, '(' + t('Undo') + ')'));
    else if (e.undone_by) li.append(el('em', {}, '(' + t('Undone') + ')'));
    else if (settingsState && settingsState.can_write && e.op !== 'cache') {
      li.append(el('button', { type: 'button', class: 'exp-debug-button', 'data-undo': e.id, onclick: (ev) => {
        const b = ev.currentTarget; b.disabled = true;
        const go = (force) => call('undo', Object.assign({ entry: e.id }, force ? { force: 1 } : {}), true).then((ans) => {
          if (!ans || !ans.ok) {
            if (ans && ans.needs_force && window.confirm(ans.message || 'force')) return go(true);
            throw new Error((ans && ans.message) || 'refused');
          }
          b.replaceWith(el('em', {}, '(' + t('Undone') + ')'));
          if (ans.entry) addLog(ans.entry, true);
          if (ans.setting) updateSetting(ans.setting);
          li.append(' ', reloadNote());
          return ans;
        });
        const fail = (err) => { b.disabled = false; li.append(' ', el('span', { class: 'exp-debug-field-error', role: 'alert' }, err.message)); };
        // The file changed since that write: the server refuses unless forced; ask first.
        go(false).catch((err) => { if (/force/.test(err.message) && window.confirm(err.message)) return go(true).catch(fail); return fail(err); });
      } }, t('Undo')));
    }
    return li;
  };
  const addLog = (e, top) => {
    if (!logList) return;
    const empty = logList.querySelector('.exp-debug-empty'); if (empty) empty.remove();
    const item = logItem(e);
    if (top) logList.prepend(item); else logList.append(item);
  };

  const renderSettings = (state) => {
    settingsState = state;
    ipRefreshers = [];
    Object.keys(settingRows).forEach((k) => delete settingRows[k]);
    const out = [];
    if (!state.can_write) out.push(hint(t('Sign in with a user who may change settings (setup/setup) to change them here.'), 'warn'));
    if ((state.siteaccesses || []).length > 1) {
      const saId = nextId('sa');
      const sa = el('select', { id: saId, onchange: () => loadSettings(true, sa.value) });
      state.siteaccesses.forEach((n) => sa.append(el('option', { value: n, selected: n === state.siteaccess }, n)));
      out.push(el('div', { class: 'exp-debug-row' }, el('label', { for: saId }, t('siteaccess') + ':'), sa));
    }
    out.push(renderPresets());
    const groups = (state.groups || []).slice();
    const known = groups.map((g) => g.id);
    (state.settings || []).forEach((s) => { if (known.indexOf(s.group) === -1) { known.push(s.group); groups.push({ id: s.group, label: s.group === 'extensions' ? t('Extension switches') : s.group }); } });
    groups.forEach((g) => {
      const list = (state.settings || []).filter((s) => s.group === g.id);
      if (!list.length) return;
      out.push(el('section', { class: 'exp-debug-group', 'data-group': g.id }, el('h3', {}, g.label), ...list.map(renderSetting)));
    });
    logList = el('ol', { class: 'exp-debug-log', reversed: true });
    if (!(state.log || []).length) logList.append(el('li', { class: 'exp-debug-empty' }, t('No changes yet.')));
    out.push(el('section', { class: 'exp-debug-group exp-debug-changelog' }, el('h3', {}, t('Change log')), logList));
    (state.log || []).forEach((e) => addLog(e, false));
    settingsBox.replaceChildren(...out);
    settingsBox.setAttribute('data-state', 'ready');
    if (state.token) token = state.token;
    if (filter && filter.value) runFilter();
  };

  const loadSettings = (keepScroll, siteaccess) => {
    if (!settingsBox) return;
    if (!keepScroll) settingsBox.replaceChildren(el('p', { class: 'exp-debug-hint', role: 'status' }, t('Loading...')));
    settingsBox.setAttribute('data-state', 'loading');
    const sa = siteaccess || (settingsState && settingsState.siteaccess) || '';
    return call('settings' + (sa ? '::' + encodeURIComponent(sa) : ''), {}).then(renderSettings).catch((err) => {
      settingsBox.setAttribute('data-state', 'error');
      settingsBox.replaceChildren(hint(t('The settings service is not available yet. The classic controls below still work.'), 'warn'),
        el('p', { class: 'exp-debug-hint' }, t('Could not load: %error', { '%error': err.message })),
        el('button', { type: 'button', class: 'exp-debug-button', onclick: () => loadSettings() }, t('Retry')));
      const classic = root.querySelector('.exp-debug-classic'); if (classic) classic.open = true;
    });
  };
  const userRights = data.user || {};
  if (settingsBox && !userRights.can_setup) {
    // no policy: no call, no values; the server refuses as well
    settingsBox.replaceChildren(el('p', { class: 'exp-debug-hint exp-debug-warn' }, t('Sign in with setup access to change debug settings')));
    settingsBox.setAttribute('data-state', 'restricted');
  } else if (settingsBox) loaders.settings = () => loadSettings();

  /* ---------------------------------------------------------------- cache */
  const cacheBox = root.querySelector('.exp-debug-cache');
  const velocityBox = root.querySelector('.exp-debug-velocity-cache');
  const page = data.page || {};
  const clearButton = (label, params, status, extra) => el('button', Object.assign({ type: 'button', class: 'exp-debug-button', onclick: (ev) => {
    const b = ev.currentTarget; b.disabled = true;
    status.textContent = t('Clearing...');
    call('cache', Object.assign({ action: 'clear' }, params), true).then((ans) => {
      b.disabled = false;
      status.textContent = (ans && ans.ok === false ? (ans.message || 'refused') : t('Cleared')) + (ans && ans.message && ans.ok !== false ? ': ' + ans.message : '');
      const last = b.closest('.exp-debug-cache-row'); const lc = last && last.querySelector('.exp-debug-last');
      if (lc) lc.textContent = t('Last cleared: %time', { '%time': new Date().toLocaleString() });
    }).catch((err) => { b.disabled = false; status.textContent = t('Could not load: %error', { '%error': err.message }); });
  } }, extra || {}), label);
  // The change log stores the time as ISO 8601 (date('c')); a number is Unix seconds (or milliseconds when
  // it is that large). Anything absent or unreadable reads "never" rather than "Invalid Date".
  const whenText = (time) => {
    if (time === null || time === undefined || time === '') return null;
    const n = typeof time === 'number' ? time : (/^\d+(\.\d+)?$/.test(String(time)) ? parseFloat(time) : NaN);
    const d = !isNaN(n) ? new Date(n < 1e12 ? n * 1000 : n) : new Date(String(time));
    return isNaN(d.getTime()) ? null : d.toLocaleString();
  };
  const lastText = (info) => t('Last cleared: %time', { '%time': (info && whenText(info.time)) || t('never') });

  const renderVelocityCache = (ans) => {
    if (!velocityBox) return;
    const status = el('span', { 'aria-live': 'polite', class: 'exp-debug-hint' });
    const v = ans.velocity || {};
    const d = v.data || {};
    velocityBox.replaceChildren(el('h3', {}, t('Velocity response cache')),
      el('p', { class: 'exp-debug-hint' }, v.message || t('Clears the pages Velocity keeps in memory.')),
      d.files !== undefined ? el('p', { class: 'exp-debug-hint' }, d.files + ' / ' + Math.round((d.bytes || 0) / 1024) + ' KB') : null,
      ans.can_cache ? el('div', { class: 'exp-debug-cache-row' }, clearButton(t('Clear'), { by: 'velocity' }, status, { 'data-cache': 'velocity' }), status) : null);
  };

  const renderCache = (ans) => {
    const out = [];
    const can = !!ans.can_cache;
    if (!can) out.push(hint(t('You may not clear caches (setup/managecache).'), 'warn'));
    const section = (title, desc, params, key, last, offer) => {
      const status = el('span', { 'aria-live': 'polite', class: 'exp-debug-hint' });
      return el('div', { class: 'exp-debug-cache-row', 'data-cache': key },
        el('strong', {}, title), ' ', desc ? el('span', { class: 'exp-debug-setting-help' }, desc) : null,
        el('div', { class: 'exp-debug-row' }, can && offer !== false ? clearButton(t('Clear'), params, status, { 'data-cache': key, 'aria-label': t('Clear') + ': ' + title }) : null,
          el('span', { class: 'exp-debug-last exp-debug-hint' }, last !== undefined ? lastText(last) : ''), status));
    };
    const lc = ans.last_cleared || {};
    const pg = ans.page || page;
    out.push(el('fieldset', {}, el('legend', {}, t('This page only')),
      pg.node_id ? section(t('This page only') + ' (node ' + pg.node_id + ')', t('Clears the view cache of the node this page shows.'), { by: 'node', node_id: pg.node_id, object_id: pg.object_id || '' }, 'node', lc['node:' + pg.node_id])
                 : hint(t('This page is not a content node.'))));
    const v = ans.velocity || {};
    out.push(el('fieldset', {}, el('legend', {}, t('Velocity response cache')),
      section(t('Response cache'), v.message || t('Clears the pages Velocity keeps in memory.'), { by: 'velocity' }, 'velocity', lc.velocity)));
    out.push(el('fieldset', {}, el('legend', {}, t('OPcache')),
      section(t('OPcache'), (ans.opcache && ans.opcache.text) || t('Not available'), { by: 'opcache' }, 'opcache', lc.opcache, !!(ans.opcache && ans.opcache.available))));
    if ((ans.tags || []).length) {
      out.push(el('fieldset', {}, el('legend', {}, t('By tag')),
        ...ans.tags.map((tg) => section(tg.tag, tg.description || (tg.ids || []).join(', '), { by: 'tag', names: tg.tag }, 'tag:' + tg.tag, lc['tag:' + tg.tag]))));
    }
    if ((ans.caches || []).length) {
      out.push(el('fieldset', {}, el('legend', {}, t('By ID')),
        ...ans.caches.map((c) => section(c.name + ' (' + c.id + ')', c.description || '', { by: 'id', names: c.id }, 'id:' + c.id, lc['id:' + c.id]))));
    }
    out.push(el('fieldset', {}, el('legend', {}, t('All caches')), section(t('All caches'), '', { by: 'all' }, 'all', lc.all)));
    cacheBox.replaceChildren(...out);
    cacheBox.setAttribute('data-state', 'ready');
    const classic = root.querySelector('.exp-debug-cache-classic'); if (classic) classic.hidden = true;
    renderVelocityCache(ans);
    if (filter && filter.value) runFilter();
  };
  const loadCache = () => {
    if (!cacheBox) return;
    cacheBox.replaceChildren(el('p', { class: 'exp-debug-hint', role: 'status' }, t('Loading...')));
    call('cache', { action: 'list', node_id: page.node_id || '', object_id: page.object_id || '' }).then(renderCache).catch((err) => {
      cacheBox.setAttribute('data-state', 'error');
      failure(cacheBox, err, loadCache);
    });
  };
  if (cacheBox && !userRights.can_cache) {
    cacheBox.replaceChildren(el('p', { class: 'exp-debug-hint exp-debug-warn' }, t('Sign in with cache access to manage caches')));
    cacheBox.setAttribute('data-state', 'restricted');
  } else if (cacheBox) { loaders.cache = loadCache; loaders.velocity = () => { if (!loaded.cache) { loaded.cache = true; loadCache(); } }; }

  /* ---------------------------------------------------------------- minimise */
  // The Exponential symbol at the left of the bar minimises the whole bar to a small tab in the bottom right
  // corner (like Symfony's toolbar); the tab restores it as it was, open or closed. Remembered in
  // localStorage (exp-debug-min); the page keeps its bottom padding only while the bar is shown.
  const bar = root.querySelector(':scope > h2');
  if (bar) {
    const NS = 'http://www.w3.org/2000/svg';
    const symbol = () => {
      const svg = document.createElementNS(NS, 'svg');
      svg.setAttribute('viewBox', '0 0 24 24'); svg.setAttribute('aria-hidden', 'true'); svg.setAttribute('focusable', 'false');
      svg.setAttribute('class', 'exp-debug-symbol');
      [['2', '2', 'none', 'exp-debug-symbol-back'], ['8.5', '8.5', '#f05a22', 'exp-debug-symbol-front']].forEach(([x, y, fill, cls]) => {
        const r = document.createElementNS(NS, 'rect');
        r.setAttribute('x', x); r.setAttribute('y', y); r.setAttribute('width', '13.5'); r.setAttribute('height', '13.5');
        r.setAttribute('fill', fill); r.setAttribute('stroke', 'currentColor'); r.setAttribute('stroke-width', '2'); r.setAttribute('class', cls);
        svg.appendChild(r);
      });
      return svg;
    };
    const count = (key) => { const c = bar.querySelector('.exp-debug-chip[data-key="' + key + '"]'); return c ? parseInt(c.textContent, 10) || 0 : 0; };
    const errors = count('errors'), warnings = count('warnings');
    const level = errors > 0 ? 'high' : (warnings > 0 ? 'warn' : '');
    const badgeLabel = errors > 0 ? t('%count errors', { '%count': errors }) : (warnings > 0 ? t('%count warnings', { '%count': warnings }) : '');
    const minButton = el('button', { type: 'button', class: 'exp-debug-logo', 'aria-label': t('Minimise debug bar'), title: t('Minimise debug bar') }, symbol());
    const restore = el('button', { type: 'button', class: 'exp-debug-restore', hidden: true,
      'aria-label': t('Show debug bar') + (badgeLabel ? ' (' + badgeLabel + ')' : ''), title: t('Show debug bar') + (badgeLabel ? ' (' + badgeLabel + ')' : '') },
      symbol(), level ? el('span', { class: 'exp-debug-restore-badge', 'data-level': level, 'aria-hidden': 'true' }, String(errors > 0 ? errors : warnings)) : null);
    bar.insertBefore(minButton, bar.firstChild);
    root.appendChild(restore);
    const setMin = (min, focus) => {
      root.classList.toggle('exp-debug-min', min);
      restore.hidden = !min;
      document.body.classList.toggle('exp-debug-bar', !min);
      if (focus) (min ? restore : minButton).focus();
    };
    minButton.addEventListener('click', () => { write('exp-debug-min', '1'); setMin(true, true); });
    restore.addEventListener('click', () => { write('exp-debug-min', '0'); setMin(false, true); });
    if (read('exp-debug-min') === '1') setMin(true, false);
  }

  /* ---------------------------------------------------------------- start */
  select(read('exp-debug-tab') || 'messages');
  document.addEventListener('exp-debug-toggle', (e) => { if (e.detail && e.detail.open) { const cur = tabs.find((tb) => tb.getAttribute('aria-selected') === 'true'); if (cur) select(cur.getAttribute('data-tab')); } });
})();
