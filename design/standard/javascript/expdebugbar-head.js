/* The "Exp Debug" bar, the part that runs at once (expDebugBarReport::assetsHtml() links it without defer, right after
   the report; expdebugbar.js, deferred, does the tabs and the controls). It was an inline script until #571; as a
   file it holds under a Content-Security-Policy with script-src 'self'.
   The markup already has everything the bar shows (expDebugBarReport::barHtml()), so the first paint is final; this
   only sets state: open and close with a click on the header, "Keep open on reload" (localStorage exp-debug-keep and
   exp-debug-open; unticked, the details start closed on every page), the panel height the user left
   (--exp-debug-panel-h), the minimised bar (exp-debug-min) and the bar's height for the page (--exp-debug-bar-h).
   Storage that throws (private windows, blocked site data) is ignored. */
(function () {
  'use strict';
  const header = document.querySelector('#debug > h2 > a');
  const content = document.getElementById('debug-details');
  if (!header || !content) return;
  const root = document.getElementById('debug');
  const bar = header.parentNode;
  const docEl = document.documentElement;
  const read = (key) => { try { return window.localStorage.getItem(key); } catch (e) { return null; } };
  const write = (key, value) => { try { window.localStorage.setItem(key, value); } catch (e) {} };

  // the minimised bar (expdebugbar.js wires the symbol and the tab): set before the first paint, so the bar does not
  // show and then go
  const min = read('exp-debug-min') === '1';
  const restore = root ? root.querySelector(':scope > button.exp-debug-restore') : null;
  if (min && root) {
    root.classList.add('exp-debug-min');
    if (restore) restore.hidden = false;
  }
  else document.body.classList.add('exp-debug-bar');

  const fit = () => docEl.style.setProperty('--exp-debug-bar-h', bar.offsetHeight + 'px');
  fit();
  if (window.ResizeObserver) new ResizeObserver(fit).observe(bar); else window.addEventListener('resize', fit);
  header.addEventListener('click', (e) => e.preventDefault());

  // The panel's height as the user left it (expdebugbar.js resizes it and saves the height), set before the panel
  // opens: for a logged-in user from the user preference (data-exp-debug-panel-height, 0 for the default; the
  // preference is authoritative, localStorage is not looked at), for an anonymous visitor from localStorage
  // (exp-debug-panel-height; read() gives null where storage is blocked). Only a whole number of pixels counts.
  const lowest = parseInt(content.getAttribute('data-exp-debug-panel-min') || '120', 10) || 120;
  const highest = parseInt(content.getAttribute('data-exp-debug-panel-max') || '4000', 10) || 4000;
  const rawHeight = content.getAttribute('data-exp-debug-panel-store') === 'browser'
    ? read('exp-debug-panel-height') : content.getAttribute('data-exp-debug-panel-height');
  const savedHeight = parseInt(rawHeight || '0', 10);
  if (savedHeight > 0 && String(savedHeight) === String(rawHeight).trim()) docEl.style.setProperty('--exp-debug-panel-h', Math.min(highest, Math.max(lowest, savedHeight)) + 'px');

  const keep = bar.querySelector(':scope > label.debug-keep-open > input[type="checkbox"]');
  const setOpen = (open) => {
    header.classList.toggle('active', open);
    content.classList.toggle('active', open);
    header.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.dispatchEvent(new CustomEvent('exp-debug-toggle', { detail: { open: open } }));
  };
  window.expDebugBarSetOpen = (open) => {
    setOpen(open);
    if (keep && keep.checked) write('exp-debug-open', open ? '1' : '0');
  };
  if (keep) {
    keep.checked = read('exp-debug-keep') === '1';
    if (keep.checked && read('exp-debug-open') === '1') setOpen(true);
    keep.addEventListener('change', () => {
      write('exp-debug-keep', keep.checked ? '1' : '0');
      write('exp-debug-open', keep.checked && content.classList.contains('active') ? '1' : '0');
    });
  }
  header.addEventListener('click', () => window.expDebugBarSetOpen(!content.classList.contains('active')));
})();
