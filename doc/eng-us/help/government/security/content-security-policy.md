---
title: Content Security Policy for public sites
audience: government, admin, developer
category: public-sector
order: 120
summary: For public bodies - what Exponential's Content Security Policy does, how it is shipped (report-only on the administration, off on public sites), where violation reports go, and why enforcing it is worth the effort for a public site.
keywords: Content Security Policy, CSP, XSS, cross-site scripting, report-only, enforce, nonce, csp_nonce, csp/report, csp.log, ReportEndpoint, Directives, AdditionalSources, ReportOnlyDesigns, CSP Level 3, public sector
type: help
---
# Content Security Policy for public sites

This page is for administrators and developers of public sites. A Content Security Policy tells the browser which
scripts, styles, images and connections a page may use; with a strict policy a script injected through a stored or
reflected value does not run, even where a template forgets to escape it. Public sites are attractive targets for
defacement and for injecting scripts into trusted pages, so the policy is a strong second line of defense
([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## Contents

1. [How it ships](#how-it-ships)
2. [The settings](#the-settings)
3. [Reports](#reports)
4. [What breaks under an enforced policy](#what-breaks-under-an-enforced-policy)
5. [Related pages](#related-pages)

## How it ships

| Siteaccess | `Mode=auto` gives |
|---|---|
| administration (design begins with `admin`, or an editor siteaccess on admin4l) | **report-only**: the browser reports what it would block and blocks nothing |
| public sites | **no policy** |

The project rates its CSP support **Partly complies** with CSP Level 3 (<https://www.w3.org/TR/CSP3/>); enforcement is
a per-siteaccess decision of the operator ([Security standards: Content Security Policy Level 3](../../../standards/security.md#content-security-policy-level-3)).
Independently, `SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self'` limits framing on every page.

## The settings

`settings/site.ini [ContentSecurityPolicy]`:

| Setting | Shipped | Meaning |
|---|---|---|
| `Mode` | `auto` | `disabled`, `report-only`, `enforce` or `auto` |
| `Directives[]` | `default-src 'self'`, `script-src 'self'`, `style-src 'self'`, `object-src 'none'`, `base-uri 'self'`, `form-action 'self'`, `frame-ancestors 'self'` and others | the policy |
| `AdditionalSources[]` | empty | add hosts to a directive without restating it |
| `Nonce`, `NonceDirectives[]` | `enabled`, `script-src` | a per-request nonce for `<script nonce="{csp_nonce()}">` |
| `ReportOnlyDesigns[]` | `admin`, `admin2`, `admin_classic` | classic designs stay report-only even when enforced |
| `ReportEndpoint` | `disabled` | `enabled` sends reports to the site's own `csp/report` view |
| `ReportOnlyDirectives[]` | empty | with enforce: a stricter report-only companion policy |

## Reports

With `ReportEndpoint=enabled` the browser's reports go to `csp/report`, which writes one line per violation to
`var/log/csp.log`, shown under **Setup > Log files** as "Content Security Policy reports". The view is open to every
visitor (browsers send reports without signing in), rate limited per client and in total, answers without a body, and
writes a flood as one `abuse/csp-report-flood` line a minute to `security.log`. `ReportURI` can name an external
collector instead.

## What breaks under an enforced policy

- Inline scripts and event handler attributes in templates of your design or of extensions.
- Context menu entries of some extensions that use inline scripts (the feature page names xrowextract, ezodf and
  ezmultiupload); they are drawn but do nothing.
- TinyMCE 3, which writes handler attributes; the editor falls back to TinyMCE 8 unless the siteaccess allows
  `'unsafe-inline'`.
- Third-party embeds (maps, video, analytics, CAPTCHAs) until their hosts are added with `AdditionalSources[]`.

The admin4, admin4l and AdminUI administrations are written to run under an enforced policy; a test fails on inline
scripts, handlers, `javascript:` URLs and `style=""` in admin4 and admin4l templates.

## Related pages

- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [Security standards](../../../standards/security.md)
- [Content Security Policy for hosts](../../hosting/security/content-security-policy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
