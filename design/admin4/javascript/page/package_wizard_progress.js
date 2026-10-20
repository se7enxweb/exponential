/*
 * admin and admin4 package/create, the step list of the wizard (create/wizard_progress.tpl): the error summary takes
 * the focus, so a screen reader and the keyboard start at the errors. The same file is in design/admin and
 * design/admin4. A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security- policy.md).
 */
(function(){ var s = document.getElementById( "a4-error-summary" ); if ( s ) s.focus(); })();
