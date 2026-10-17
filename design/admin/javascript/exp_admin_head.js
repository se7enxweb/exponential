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
