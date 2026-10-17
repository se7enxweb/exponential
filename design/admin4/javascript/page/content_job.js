/*
 * The progress of a content job (content/job.tpl): polls the job's status (data-status-url of #exp-contentjob),
 * shows the counts, the message and the elapsed time, and opens the result or reloads the page when the job's state
 * changes. The texts are data-text-* attributes of #exp-contentjob.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
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
  function poll() {
    fetch(url, {cache: 'no-store', credentials: 'same-origin'}).then(function (r) { return r.json(); }).then(function (s) {
      if (!s.ok) { q('.a4-content-job-message').textContent = s.error || t('waiting', {}); setTimeout(poll, 4000); return; }
      show(s);
      if (s.can_resume && !box.hasAttribute('data-can-resume')) { location.reload(); return; }
      if (s.state === 'done') {
        var done = q('.a4-content-job-done'), a = q('.a4-content-job-open'), n = 3; done.hidden = false;
        if (s.redirect_url) a.href = s.redirect_url;
        (function tick() { q('.a4-content-job-auto').textContent = t('opening', {'%seconds': n}); if (n-- <= 0) location.href = a.href; else setTimeout(tick, 1000); })();
        return;
      }
      if (s.state !== first && s.state !== 'running' && s.state !== 'queued') { location.reload(); return; }
      setTimeout(poll, 2000);
    }).catch(function () { q('.a4-content-job-message').textContent = t('waiting', {}); setTimeout(poll, 4000); });
  }
  poll();
})();
