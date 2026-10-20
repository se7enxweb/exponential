---
title: CSRF form tokens
audience: government, admin, website-creator, developer
category: public-sector
order: 50
summary: How Exponential protects every POST form against cross-site request forgery with the ezformtoken extension, what visitors see when a token is refused, the secret to set per project, and what to check in your own forms.
keywords: CSRF, cross-site request forgery, form token, ezformtoken, HTMLForms Secret, form expired, 403, warning.log, RefusalLogCollapseSeconds, security
type: help
---
# CSRF form tokens

This page is for administrators, website creators and developers who need to know how public and administration forms
are protected against forged submissions. Exponential adds a secret token to every POST form and refuses posts without
it; this is the `ezformtoken` extension in the kernel. Sources: [Form expired page](../../../../features/6.0/form-expired-page.md),
[Spam protection for forms](../../../creators/website-creators/spam-protection-for-forms.md#form-tokens) and
[Security standards](../../../../standards/security.md).

## Contents

1. [What the token protects against](#what-the-token-protects-against)
2. [What visitors see when a token is refused](#what-visitors-see-when-a-token-is-refused)
3. [Settings](#settings)
4. [Checks for your own forms](#checks-for-your-own-forms)
5. [Troubleshooting](#troubleshooting)

## What the token protects against

Cross-site request forgery: a page on another site makes a visitor's browser post to your site, using the visitor's
session. With a token in every form, a post that did not come from your own page is refused. It protects:

- the administration (every edit, removal, setting);
- signed-in visitors' actions (profile changes, subscriptions);
- public forms (information collector posts).

It is one layer of [OWASP Top 10](https://owasp.org/Top10/) A01 and A08 mitigations listed in
[Security standards](../../../../standards/security.md). It does not stop a person, or a script that loads your form
first, from sending spam; that is what [Spam protection and third parties](spam-protection-and-third-parties.md) covers.

## What visitors see when a token is refused

Since 27 September 2026 a refused post gets a clear page instead of an error:

| Part | Value |
|---|---|
| Message | the form has expired; nothing was saved; reload the form and send it again |
| Status | `403 Forbidden` (kernel error 6, `error.ini`) |
| Headers | `Cache-Control: no-store` and the configured security headers |
| XHR with `Accept: application/json` | a JSON body with the same message |
| Log | one line in `var/log/warning.log` with the check (missing or wrong), method, path and siteaccess, never the token |

The page is `design:error/kernel/6.tpl` in `design/standard`; your design can override it. A form left open for a long
time (a long consultation response) can expire; tell people to copy long texts before sending, or keep forms short.

## Settings

| File | Block | Key | Default | Do |
|---|---|---|---|---|
| `settings/site.ini` | `HTMLForms` | `Secret` | `ThisTokenIsNotSoSecretChangeIt` | set your own value in `settings/override/site.ini.append.php`; keep it out of version control |
| `settings/site.ini` | `HTMLForms` | `RefusalLogCollapseSeconds` | `60` | repeated refusals within the window are counted, not written |
| `settings/error.ini` | `ErrorSettings-kernel` | `HTTPError[6]` | `403` | leave it |

Keep the `ezformtoken` extension active; the installer sets it up.

## Checks for your own forms

- [ ] Forms post with `method="post"`; the token is added to POST forms of the page output.
- [ ] Forms built by JavaScript send the token too (read it from the page's form or meta data, as the shipped
  scripts do).
- [ ] No state-changing action is reachable by GET (a link that removes something).
- [ ] After a deploy, a form opened before still works or shows the "form expired" page, not an error.

## Troubleshooting

| Symptom | Cause |
|---|---|
| Every post is refused after a change | the secret changed while forms were open, or a cache served a page with an old token to many visitors; do not cache pages with forms for signed-in users |
| Refusals from one address in `warning.log` | someone posting without loading the form; usually harmless, possibly an attack worth watching |
| A custom form is always refused | it is built outside the page output (an iframe, a static HTML file) and has no token |

## Related pages

- [Building forms](README.md)
- [Security hardening: CSRF](../../../security-hardening.md#cross-site-request-forgery-csrf)
- [Forms, CSRF protection and HTTP input](../../../creators/app-creators/forms-csrf-and-http-input.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
