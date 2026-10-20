/*
 * The list of content jobs (content/jobs.tpl): reloads the page every five seconds while a job is active.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () { setTimeout(function () { location.reload(); }, 5000); })();
