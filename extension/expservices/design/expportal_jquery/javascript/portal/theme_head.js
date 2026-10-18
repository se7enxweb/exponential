/**
 * The portal's color mode before the first paint: the theme saved in localStorage ('expportal-theme') is set as
 * data-theme on <html>. Loaded in <head> as a blocking script, before the stylesheet, so a page under a Content
 * Security Policy (script-src 'self') still paints in the saved mode. Shared by expportal_jquery and
 * expportal_reactive.
 */
try {
    var expPortalTheme = localStorage.getItem('expportal-theme');
    if (expPortalTheme) document.documentElement.setAttribute('data-theme', expPortalTheme);
} catch (e) {}
