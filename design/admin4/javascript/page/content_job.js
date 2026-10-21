/*
 * The progress of a content job (content/job.tpl): polls the job's status (data-status-url of #exp-contentjob),
 * shows the counts, the message and the elapsed time, and opens the result or reloads the page when the job's state
 * changes. The texts are data-text-* attributes of #exp-contentjob.
 * The polling ends with a message when it cannot go on: the job is gone or not the user's (404, 403: data-text-gone),
 * or the session has ended (the request was redirected, to the login page, or the answer is not JSON:
 * data-text-signed-out), or the answer cannot be shown (a script error: data-text-error); #exp-contentjob then carries
 * data-polling="stopped". A network error or a server error is
 * passing: the next request waits longer each time (4, 8, 16 ... up to 60 seconds) and is back at two seconds after
 * an answer. While the tab is hidden no request is made; the next one goes out when it is shown again.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
  var box = document.getElementById('exp-contentjob'); if (!box || !window.fetch) return;
  var url = box.getAttribute('data-status-url'), first = box.getAttribute('data-state');
  var names = box.getAttribute('data-states').split('|'), keys = ['queued', 'running', 'done', 'failed', 'cancelled'];
  var t = function (k, map) { var s = box.getAttribute('data-text-' + k); for (var m in map) s = s.split(m).join(map[m]); return s; };
  var q = function (sel) { return box.querySelector(sel); };
  function show(s) {
    var badge = q('.a4-content-job-state'); badge.className = 'a4-badge a4-content-job-state ' + s.state; badge.textContent = names[keys.indexOf(s.state)] || s.state;
    q('.a4-content-job-counts').textContent = t('counts', {'%done': s.done, '%total': s.total});
    var bar = q('.a4-content-job-bar'); bar.className = 'a4-meter a4-content-job-bar ' + s.state; bar.setAttribute('aria-valuenow', s.percent); q('.a4-content-job-bar span').className = 'a4-pct-' + Math.max(0, Math.min(100, Math.round(s.percent)));
    q('.a4-content-job-message').textContent = s.cancel_requested && s.state === 'running' ? t('stopping', {}) : (s.message || '');
    var cb = q('.a4-content-job-batch'); if (cb) cb.textContent = s.batch;
    if (s.started) q('.a4-content-job-elapsed').textContent = t('elapsed', {'%seconds': Math.max(0, (s.finished || s.now) - s.started)});
    var log = q('.a4-content-job-log'), atEnd = log.scrollTop + log.clientHeight >= log.scrollHeight - 4;
    log.textContent = s.log || ''; if (atEnd) log.scrollTop = log.scrollHeight;
  }
  var msg = function (text) { q('.a4-content-job-message').textContent = text; };
  var failures = 0, waiting = false, stopped = false;
  function stop(k) { stopped = true; box.setAttribute('data-polling', 'stopped'); msg(t(k, {})); }
  // the next request in ms, or once the tab is shown again
  function later(ms) { if (!stopped) setTimeout(function () { if (document.hidden) waiting = true; else poll(); }, ms); }
  function retry(text) { msg(text || t('waiting', {})); failures++; later(Math.min(60000, 2000 * Math.pow(2, failures))); }
  document.addEventListener('visibilitychange', function () { if (waiting && !document.hidden) { waiting = false; poll(); } });
  function answer(s) {
    failures = 0;
    show(s);
    if (s.can_resume && !box.hasAttribute('data-can-resume')) { location.reload(); return; }
    if (s.state === 'done') {
      var done = q('.a4-content-job-done'), a = q('.a4-content-job-open'), n = 3; done.hidden = false;
      if (s.redirect_url && window.a4SafeURL && window.a4SafeURL(s.redirect_url, 'content_job')) a.href = s.redirect_url;
      (function tick() { q('.a4-content-job-auto').textContent = t('opening', {'%seconds': n}); if (n-- <= 0) { if (window.a4SafeURL && window.a4SafeURL(a.href, 'content_job')) location.href = a.href; } else setTimeout(tick, 1000); })();
      return;
    }
    if (s.state !== first && s.state !== 'running' && s.state !== 'queued') { location.reload(); return; }
    later(2000);
  }
  function poll() {
    fetch(url, {cache: 'no-store', credentials: 'same-origin'}).then(function (r) {
      if (r.status === 404 || r.status === 403) { stop('gone'); return; }
      if (r.redirected || r.status === 401) { stop('signed-out'); return; }
      if (!r.ok) { retry(); return; }
      // a page in place of the JSON: the login page of an ended session
      if ((r.headers.get('Content-Type') || '').indexOf('json') === -1) { stop('signed-out'); return; }
      return r.json().then(function (s) {
        if (!s || !s.ok) { retry(s && s.error); return; }
        answer(s);
      }, function () { stop('signed-out'); });
    }, function () { retry(); }).catch(function (e) {
      // a script error while showing the answer: stop with a message instead of freezing without one
      if (!stopped) stop('error');
      if (window.console && console.error) console.error(e);
    });
  }
  poll();
})();
