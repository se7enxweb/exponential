---
title: Security for applications
audience: app-creator, developer
category: app-creation
order: 560
summary: The security rules an Exponential application follows - permissions on every entry point, input validation, output escaping, form tokens, safe redirects, uploads, secrets, CSP-ready markup, logging and disclosure - with the kernel feature behind each.
keywords: security, OWASP, permissions, input validation, XSS, CSRF, SQL injection, uploads, SVG, redirects, secrets, CSP, audit, vulnerability disclosure, app creator
type: help
---
# Security for applications

This page is for application creators who want their code as safe as the kernel around it. Each rule names the kernel
feature that helps; the standards page maps them to the OWASP Top 10 ([Security standards](../../../standards/security.md)).

## Contents

1. [The rules](#the-rules)
2. [Entry points to secure](#entry-points-to-secure)
3. [Before you ship](#before-you-ship)
4. [Reporting vulnerabilities](#reporting-vulnerabilities)

## The rules

| Rule | Kernel help | Read |
|---|---|---|
| Check permissions in every view, server function, fetch and service | `functions` in `module.php`, `hasAccessTo()`, `canRead()` | [Access control in code](access-control-in-code.md) |
| Validate and cast all input | `eZHTTPTool`, module actions | [Input validation and output escaping](input-validation-and-escaping.md) |
| Escape all output | `|wash` in templates | same |
| Protect POST forms | `ezformtoken` | [Forms, CSRF protection and HTTP input](forms-csrf-and-http-input.md) |
| SQL only with escaping and casts, or `eZPersistentObject` | `escapeString()` | [Database access and SQL](database-access.md) |
| Redirect only within the site | safe redirect checks | [Safe redirects](../../../features/6.0/safe-redirects.md) |
| Store uploads through the kernel | image and file datatypes; SVG sanitizer | [SVG images](../../../bc/6.0/svg-images.md) |
| No inline scripts or styles | the Content Security Policy | [Content Security Policy for your code](content-security-policy.md) |
| No secrets in code, payloads or logs | `settings/override/` | [Environment settings and secrets](environment-settings-and-secrets.md) |
| Record security-relevant actions | the audit trail | [Audit trail](../../../features/6.0/audit-trail.md) |
| Outgoing requests only to allowed hosts | the link check's private-network rule as a model | [URLs and links](../../../guides/urls-and-aliases.md) |

## Entry points to secure

Every way into your code needs its own check:

- module views (`module.php` functions plus your own checks for limitations);
- ezjscore server functions (ezjscore does not check a policy for you);
- REST controllers (the framework authenticates; you authorize);
- fetch functions and template operators (templates can be called from anywhere);
- cronjobs, commands and queue handlers (run as the right user; validate stored input again).

## Before you ship

- [ ] Every entry point checked with a user who lacks the permission.
- [ ] Every printed value escaped; no raw HTML from users.
- [ ] Forms post with tokens; no state change on GET.
- [ ] Uploads checked by type and size, stored through the kernel.
- [ ] No secrets in the repository (`git log -p` searched).
- [ ] Pages produce no reports in `var/log/csp.log`.
- [ ] Errors do not show stack traces to visitors; debug output off in production.
- [ ] Dependencies current; Composer packages pinned to maintained versions.

## Reporting vulnerabilities

Vulnerabilities in Exponential are reported privately as `SECURITY.md` in the repository describes, not in public
issues ([When something goes wrong](../../when-something-goes-wrong.md)). Give your own extension a `SECURITY.md` with a
private contact, too.

## Related pages

- [Security hardening](../../security-hardening.md)
- [13. Security hardening (installation)](../../../install/13-security-hardening.md)
- [Security and audit](../../../guides/security-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
