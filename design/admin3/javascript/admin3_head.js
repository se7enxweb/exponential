/* admin3: the head script, run before the first paint (pagelayout.tpl loads it without defer). A width of the left
   column dragged by hand (data-a4-left-width on <html>, the admin_left_menu_size preference) becomes
   --left-sidebar-width, so the column is drawn at that width from the start. A file of its own, not an inline script,
   so it runs under a Content Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md). */
(function () {
    var r = document.documentElement, w = r.getAttribute('data-a4-left-width');
    if (w && /^\d{2,4}(\.\d+)?px$/.test(w)) r.style.setProperty('--left-sidebar-width', w);
})();
