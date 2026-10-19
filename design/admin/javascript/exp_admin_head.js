/* The classic admin design: the head script of every page (page_head_style.tpl loads it before the stylesheets).
   html.a4-js says the frame's scripts run. It is set before the first paint, so the shared components
   (components.css: .exp-js-only controls such as a list's search field) are drawn in their final place from the start
   instead of appearing, and moving the page down, when the page's own script runs at the end. admin4 sets the same
   class in a4_head.js. A file of its own, not an inline script, so it runs under a Content Security Policy without
   'unsafe-inline'. */
(function () {
    var root = document.documentElement;
    if (root.classList) root.classList.add('a4-js');
})();

/* The header's Help menu (page_header.tpl) is a native <details> disclosure, which the browser opens and closes
   without a script but does not close with Escape or a click elsewhere. Escape closes the open menu and gives the
   focus back to its button (the <summary>); a click outside it closes it too. Delegated on the document, so it works
   whatever the order the page is drawn in. */
(function () {
    var SELECTOR = 'details.header-help-menu[open]';
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape' && event.key !== 'Esc') return;
        var open = document.querySelector(SELECTOR);
        if (!open) return;
        open.removeAttribute('open');
        var summary = open.querySelector('summary');
        if (summary) summary.focus();
        event.preventDefault();
    });
    document.addEventListener('click', function (event) {
        var open = document.querySelector(SELECTOR);
        if (open && !open.contains(event.target)) open.removeAttribute('open');
    });
})();
