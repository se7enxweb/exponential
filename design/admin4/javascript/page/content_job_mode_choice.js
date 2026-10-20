/*
 * The "run now or in the background" choice on a confirmation page (content/job_mode_choice.tpl): a change is
 * remembered as the user's preference admin_content_job_mode_<data-op of the fieldset>, saved through window.a4Pref
 * (a4_head.js: a POST to user/preferences/set_and_exit with the form token), and with data-form the
 * choice is sent with that form, which may stand elsewhere on the page. A file of its own, so the page runs under a
 * Content Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 *
 * The file may be loaded more than once; it sets up every choice the page has, each once: those above it now,
 * any further one when the document is read.
 */
(function () {
  function setup(set) {
    if (set.hasAttribute('data-job-mode-ready')) return;
    set.setAttribute('data-job-mode-ready', '1');
    var op = set.getAttribute('data-op'), formName = set.getAttribute('data-form');
    set.addEventListener('change', function (e) {
      if (!e.target || e.target.name !== 'ContentJobMode' || !op || !window.a4Pref) return;
      window.a4Pref.set('admin_content_job_mode_' + op, e.target.value);
    });
    // the browse page renders this above its form: listen on the document, the form need not exist yet
    if (formName) {
      document.addEventListener('submit', function (e) {
        var f = e.target, c = set.querySelector('input[name="ContentJobMode"]:checked');
        if (!f || f.getAttribute('name') !== formName || !c) return;
        var h = f.querySelector('input[type="hidden"][name="ContentJobMode"]');
        if (!h) { h = document.createElement('input'); h.type = 'hidden'; h.name = 'ContentJobMode'; f.appendChild(h); }
        h.value = c.value;
      });
    }
  }
  function setupAll() {
    var sets = document.querySelectorAll('fieldset.a4-content-job-mode');
    for (var i = 0; i < sets.length; i++) setup(sets[i]);
  }
  setupAll();
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setupAll);
})();
