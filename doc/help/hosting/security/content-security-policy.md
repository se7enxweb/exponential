---
title: Content Security Policy
audience: hosting, admin
category: hosting
order: 40
summary: The Content Security Policy as a host meets it: the shipped report-only policy on administration siteaccesses, the csp/report collector and csp.log, how to move a customer's siteaccess to enforce, what usually breaks (extensions, TinyMCE 3, external scripts), rollback, and how to offer this as a service.
keywords: Content Security Policy, CSP, report-only, enforce, Mode=auto, csp/report, csp.log, ReportEndpoint, ReportURI, nonce, AdditionalSources, ReportOnlyDesigns, TinyMCE, XSS, cross-site scripting, administration
type: help
---
# Content Security Policy

This page is for system administrators and support staff at a host, and for product managers who consider offering
"CSP enforcement" as part of a managed plan. A Content Security Policy tells the browser which scripts, styles, images
and connections a page may use; with a strict policy, a script injected through a value that a template forgot to
escape does not run. The reference is [Content Security Policy](../../../features/6.0/content-security-policy.md).

## Contents

1. [What ships](#what-ships)
2. [Collecting reports](#collecting-reports)
3. [Moving a siteaccess to enforce](#moving-a-siteaccess-to-enforce)
4. [What usually needs more than self](#what-usually-needs-more-than-self)
5. [Rollback](#rollback)
6. [Offering it as a service](#offering-it-as-a-service)
7. [Checklist](#checklist)

## What ships

The policy is sent from `site.ini [ContentSecurityPolicy]`, beside the five security headers of
`[HTTPHeaderSettings] SecurityHeaders[]`.

| `Mode` | Effect |
|---|---|
| `disabled` | no policy |
| `report-only` | header `Content-Security-Policy-Report-Only`: the browser reports what would be blocked and blocks nothing |
| `enforce` | header `Content-Security-Policy`: the browser blocks what the policy does not allow |
| `auto` (the default) | `report-only` on administration siteaccesses (those whose design begins with `admin`, such as admin, admin3, admin4, admin4l, adminui), `disabled` on all others |

The shipped directives allow files of the site itself only: `script-src 'self'` (no inline `<script>`, no `on...=`
attributes, no `javascript:` links, no `eval()`), `style-src 'self'`, `object-src 'none'`, `base-uri 'self'`,
`form-action 'self'`, `frame-ancestors 'self'`, with `data:` and `blob:` for images and media. With `Nonce=enabled`
(the default) every request gets a nonce for `script-src`.

So **as shipped nothing changes for a visitor**: the administration only reports, the public sites send no policy.
Enforcing is a decision per siteaccess, made after an audit.

## Collecting reports

The kernel collects reports itself. The view `csp/report` takes what the browser sends and writes one line per
violation to `var/log/csp.log`, listed in the log viewer as **Content Security Policy reports** (key `csp`). Point the
policy at it per siteaccess:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
ReportEndpoint=enabled
```

- The view is open to every visitor (browsers send reports without signing in), answers with a status and no body,
  and is never cached.
- It is rate limited per client (`ReportsPerMinute=60`) and in total (`ReportsPerMinuteTotal=600`); a flood becomes one
  `abuse/csp-report-flood` line a minute in `security.log`.
- Addresses are masked as in the request logs; the error digest groups `csp.log` by directive and blocked source.
- `csp.log` is rotated like the request logs: 5 archives, compressed, 10 MB, pruned after 30 days.

See [Reports](../../../features/6.0/content-security-policy.md#reports).

## Moving a siteaccess to enforce

The steps of [Switching to enforce, step by step](../../../features/6.0/content-security-policy.md#switching-to-enforce-step-by-step),
as a host runs them for a customer:

1. **Audit the templates** of the installation, with every extension it uses:

   ```bash
   CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
   ```

   Each failure line is a template with inline code that an enforced policy would break.
2. **Collect reports of real use** for a few days with `ReportEndpoint=enabled`: the pages of every extension, the
   editors, the dialogs and context menus.
3. **Enforce one siteaccess first**, best a second administration siteaccess that only the customer's lead editor
   uses, with the same designs and extensions.
4. **Loosen only what you must**, per siteaccess and per directive, for example a map host:

   ```ini
   [ContentSecurityPolicy]
   Mode=enforce
   AdditionalSources[]=script-src https://maps.example.com
   ```

5. **Clear the INI cache** (`php bin/php/ezcache.php --clear-tag=ini`), reload PHP-FPM and restart Velocity so every
   process sends the new header.

For the public sites, start with `Mode=report-only` in their siteaccess; their designs and the rich text they render
are not covered by the test.

> **Tip**
> `ReportOnlyDirectives[]` sends a stricter report-only policy beside an enforced one, so you can measure the way to
> `style-src 'self'` while the enforced policy still allows `'unsafe-inline'` styles
> ([Enforcing: nonce, more sources, a stricter companion, rollback](../../../features/6.0/content-security-policy.md#enforcing-nonce-more-sources-a-stricter-companion-rollback)).

## What usually needs more than self

| Source | Effect under an enforced policy | Way out |
|---|---|---|
| TinyMCE 3, the default ezoe engine | its edit template and dialogs use inline scripts and handlers | `Directives[script-src]='self' 'unsafe-inline'` and the same for `style-src` on that siteaccess, or switch to TinyMCE 8 |
| TinyMCE 8 | sets styles on its own elements | `'unsafe-inline'` in `style-src` |
| Extensions with inline context menu items | entries are drawn but do nothing, without an error | convert them in the extension, or keep `'unsafe-inline'` for that siteaccess |
| Rich text tables, cell alignment, image borders from content | the `style=""` values are dropped | `'unsafe-inline'` in `style-src` if that matters |
| External scripts (maps, reCAPTCHA, social buttons, ad servers) | blocked | add their hosts with `AdditionalSources[]` |
| `<object>`/`<embed>` players, inline PDF | blocked by `object-src 'none'` | `Directives[object-src]='self'` where needed |

The classic administration designs stay report-only under `Mode=enforce` through `ReportOnlyDesigns[]` (admin,
admin2, admin_classic), so a user who picks one with the UI switch keeps a working page. See
[Before enforcing on the administration](../../../features/6.0/content-security-policy.md#before-enforcing-on-the-administration).

## Rollback

Set `Mode=report-only` in the siteaccess, clear the INI cache, reload PHP-FPM and restart Velocity. Nothing else
changes: the same policy is sent as report-only, and the stricter companion is no longer sent.

```ini
# settings/siteaccess/<siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
Mode=report-only
```

## Offering it as a service

A host can choose to include CSP work in a managed plan. What the sources support as a scope:

- report collection on the administration from day one (`ReportEndpoint=enabled`), with the digest reviewed in the
  monthly operations round ([Monthly operations checklist](../checklists/monthly-operations.md));
- an enforcement project per site: the audit, a reporting period, one siteaccess first, then the rest;
- a rule that customer extensions and designs are reviewed for inline code before they are deployed.

Whether and how to charge for it is the host's decision; see [Designing hosting plans](../business/designing-hosting-plans.md).

## Checklist

- [ ] `ReportEndpoint=enabled` on the administration siteaccess; `csp.log` appears in the log viewer.
- [ ] The template audit run with every extension of the installation.
- [ ] A reporting period with real editing work, including dialogs and context menus.
- [ ] One siteaccess enforced first; extensions and editors checked by use.
- [ ] Exceptions added per siteaccess and directive only, and written down.
- [ ] Rollback tested: `Mode=report-only`, INI cache cleared, PHP-FPM reloaded, Velocity restarted.

## Related pages

- [Security for hosts: overview](README.md)
- [Content Security Policy (feature reference)](../../../features/6.0/content-security-policy.md)
- [Security hardening: Cross-site scripting and the Content Security Policy](../../security-hardening.md#cross-site-scripting-xss-and-the-content-security-policy)
- [Security log](../operations/security-log.md)
- [Hardening for hosts](hardening-for-hosts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
