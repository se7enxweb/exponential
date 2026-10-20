---
title: Content Security Policy for your code
audience: app-creator, developer
category: app-creation
order: 570
summary: Write templates and scripts that run under Exponential's Content Security Policy - no inline scripts, handlers or styles; page scripts as files, data-* attributes and JSON data blocks; adding sources; reading the CSP report log.
keywords: Content Security Policy, CSP, inline script, onclick, style attribute, nonce, data attributes, JSON data block, AdditionalSources, report-only, enforce, csp.log, app creator
type: help
---
# Content Security Policy for your code

This page is for application creators whose templates and scripts must work under a Content Security Policy. The
administration runs under one and blocks inline code once enforced; public sites can turn it on
([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## Contents

1. [The shipped policy](#the-shipped-policy)
2. [What that forbids](#what-that-forbids)
3. [How to write it instead](#how-to-write-it-instead)
4. [Sources from other hosts](#sources-from-other-hosts)
5. [Checking your pages](#checking-your-pages)

## The shipped policy

`site.ini [ContentSecurityPolicy]`: `Mode=auto` (report-only on administration siteaccesses, off on others),
`Directives[]` with `default-src 'self'`, `script-src 'self'`, `style-src 'self'`, `img-src 'self' data: blob:`,
`object-src 'none'`, `frame-ancestors 'self'` and more. With `Nonce=enabled` the administration's policy carries a
per-request nonce.

## What that forbids

| Forbidden | Why |
|---|---|
| `<script>` with code in templates | `script-src 'self'` allows script files only |
| `onclick=`, `onload=` ... attributes | inline handlers |
| `javascript:` links | inline code |
| `<style>` elements, `style=""` attributes | `style-src 'self'` allows stylesheet files only |
| `eval()`, `new Function()` | no unsafe-eval |
| scripts and fonts from other hosts | not in the policy unless added |

## How to write it instead

| Instead of | Write |
|---|---|
| inline page script | a file `design/<design>/javascript/...`, printed with `{ezscript( array( 'page/expdemo_list.js' ) )}` |
| values for a script | `data-*` attributes washed for HTML, or JSON: `data-conf="{json_encode( hash( ... ) )|wash}"` |
| a configuration object | `<script type="application/json" id="...">` read by a script file (data, not code) |
| `onclick` | a class or `data-*` attribute and `addEventListener` in the script file |
| `style="width: 40%"` | a class, or a CSS custom property set from a stylesheet class |
| admin confirm and toggle buttons | the shared `data-a4-*` actions of `a4_actions.js` (`data-a4-confirm`, `data-a4-toggle-checkboxes` ...) |

In admin designs, styles also follow the admin rules: shared components and tokens, module CSS in
`stylesheets/modules/` ([admin4 styling](../../../bc/6.0/admin4-styling.md)).

## Sources from other hosts

```ini
# extension/expdemo/settings/site.ini.append.php
[ContentSecurityPolicy]
AdditionalSources[]=script-src https://maps.example.org
AdditionalSources[]=connect-src https://api.example.org
```

`AdditionalSources[]` is a list, so extensions and siteaccesses each add their hosts.

## Checking your pages

- Open each page with the browser console: violations are reported there.
- Reports reach `csp/report` and are written to `var/log/csp.log`.
- The kernel's `tests/tests/design/AdminContentSecurityPolicyTest.php` checks templates of the admin design chains,
  including active extensions' templates; an extension with inline code shows up there.

## Related pages

- [Stylesheets and scripts for your site](../website-creators/stylesheets-and-scripts.md)
- [Security for applications](security-for-applications.md)
- [Web platform standard](../../../standards/web-platform.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
