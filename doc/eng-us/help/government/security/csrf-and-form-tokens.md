---
title: CSRF and form tokens
audience: government, admin, developer
category: public-sector
order: 360
summary: For administrators and developers of public sites - how the ezformtoken extension protects every form against cross-site request forgery, how to check it is active, what custom AJAX code must do, and the form expired page.
keywords: CSRF, cross-site request forgery, form token, ezformtoken, SameSite, AJAX, POST, form expired, ActiveExtensions, Cache-Control private, csp/report, public sector
type: help
---
# CSRF and form tokens

This page is for administrators and developers. In a cross-site request forgery, another site makes a signed-in
editor's browser send a form: publish, delete, change a role. Exponential's `ezformtoken` extension adds a per-session
token to every HTML form and checks it on every POST; a forged form cannot know the token
([13.8 Form tokens against cross-site request forgery](../../../install/13-security-hardening.md#138-form-tokens-against-cross-site-request-forgery)).

## Contents

1. [How it works](#how-it-works)
2. [Keep it active](#keep-it-active)
3. [Custom code](#custom-code)
4. [When a token is refused](#when-a-token-is-refused)
5. [Related pages](#related-pages)

## How it works

- The setup wizard activates `ezformtoken` on every new installation.
- Event listeners add the token to forms in the output and check it on input.
- Session cookies are `SameSite=Lax` as defense in depth.
- A page carrying a token is sent `Cache-Control: private`, so no shared cache stores it.
- Some views must accept browser POSTs without a token by design, such as `csp/report` for Content Security Policy
  reports with their specific content types; the view only writes a log line.

## Keep it active

Check that it is in `[ExtensionSettings] ActiveExtensions[]`:

```bash
php bin/php/console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
```

or **Setup > Extensions**. If it is missing on a live site, every form is open to forgery; add it in the override and
clear caches.

## Custom code

- Custom AJAX code that sends POST requests must send the token too; the kernel's own scripts and ezjscore do. The
  extension's `README.rst` shows how.
- A reverse proxy must not override `Cache-Control: private` on pages with a token.
- Extensions that add their own forms get the token automatically when they render normal HTML forms through the
  templates.

## When a token is refused

The answer is 403 with the "form expired" page; no token is written to the log
([Form expired page](../../../features/6.0/form-expired-page.md)). Common harmless causes: the session timed out while a
form was open, or the user signed in again in another tab. Repeated refusals for many users after a change point to a
proxy or caching problem.

## Related pages

- [13.8 Form tokens](../../../install/13-security-hardening.md#138-form-tokens-against-cross-site-request-forgery)
- [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)
- [Security standards: OWASP cheat sheets](../../../standards/security.md#owasp-cheat-sheets-reflected-in-the-code)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
