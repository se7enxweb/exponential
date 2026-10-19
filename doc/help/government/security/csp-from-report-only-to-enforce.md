---
title: From report-only to an enforced CSP
audience: government, admin, developer
category: public-sector
order: 130
summary: A staged plan for public bodies to move the Content Security Policy of the administration, and later of public siteaccesses, from report-only to enforce - template audit, collecting reports, one siteaccess first, loosening only what must be, rollback.
keywords: CSP enforce plan, report-only, enforce, Mode, AdminContentSecurityPolicyTest, CSP_ALL_EXTENSIONS, ReportEndpoint, csp.log, rollback, staged rollout, unsafe-inline, TinyMCE 3, public sector
type: help
---
# From report-only to an enforced CSP

This page is for administrators and developers who turn the Content Security Policy from a reporting tool into a
protection. Enforcing is a decision per siteaccess, made after an audit; the project's step-by-step procedure is in
[Switching to enforce, step by step](../../../features/6.0/content-security-policy.md#switching-to-enforce-step-by-step).
This page turns it into a plan with milestones that fits a public body's change process.

## Contents

1. [Phase 1: Audit the templates](#phase-1-audit-the-templates)
2. [Phase 2: Collect real reports](#phase-2-collect-real-reports)
3. [Phase 3: Enforce one siteaccess](#phase-3-enforce-one-siteaccess)
4. [Phase 4: Enforce the administration](#phase-4-enforce-the-administration)
5. [Phase 5: Public siteaccesses](#phase-5-public-siteaccesses)
6. [Rollback](#rollback)
7. [Related pages](#related-pages)

## Phase 1: Audit the templates

Run the project's test with every extension of the installation, as the administration renders them:

```bash
CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
```

Each failure line is a template with inline code that an enforced policy would break. Fix extensions in their own
repositories: scripts into files loaded with `ezscript`, values into `data-*` attributes, handlers into listeners,
menu items into `data-menu-*`.

**Milestone**: the list of remaining findings, each with an owner.

## Phase 2: Collect real reports

Point reports at the site's own collector, in the administration siteaccess's `site.ini.append.php`:

```ini
[ContentSecurityPolicy]
ReportEndpoint=enabled
```

Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and reload PHP (restart Velocity). Use the
administration for a few days: every extension's pages, the editors, dialogs and context menus (handler attributes are
reported only when they run). Read **Setup > Log files** > "Content Security Policy reports" (`csp.log`).

**Milestone**: no report for the pages your staff use, or a decision for each source.

## Phase 3: Enforce one siteaccess

Enforce a second administration siteaccess that only the web team uses, with the same designs and extensions:

```ini
# settings/siteaccess/<test admin siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
Mode=enforce
ReportEndpoint=enabled
```

Work in it for a week. Where something must stay, loosen only that directive for that siteaccess, for example with
`AdditionalSources[]=script-src https://maps.example.org` or, for TinyMCE 3, `'unsafe-inline'` in `script-src` and
`style-src`.

**Milestone**: a week of normal work without breakage.

## Phase 4: Enforce the administration

Set `Mode=enforce` on the main administration siteaccess, clear the INI cache, reload PHP-FPM and restart Velocity
(`./console exp:velocity deploy --allow-root-user` does all of it). Classic designs (`ReportOnlyDesigns[]`) stay
report-only for users who pick them. Keep `ReportEndpoint=enabled` and read the reports weekly.

## Phase 5: Public siteaccesses

Public designs and rich text output (styles from content, embedded players) are not checked by the project's test.
Start each public siteaccess with `Mode=report-only` and `ReportEndpoint=enabled`, fix your design, add the hosts of
embeds you keep, and enforce when the reports are quiet. A stricter companion can measure the next step:
`ReportOnlyDirectives[style-src]='self'` beside an enforced policy that still allows inline styles.

## Rollback

Set `Mode=report-only` in the siteaccess, clear the INI cache, reload PHP-FPM and restart Velocity. Nothing else
changes ([Enforcing: rollback](../../../features/6.0/content-security-policy.md#enforcing-nonce-more-sources-a-stricter-companion-rollback)).
Write the rollback into your change request before you enforce.

## Related pages

- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [Content Security Policy for public sites](content-security-policy.md)
- [Log review routine](log-review-routine.md) (the CSP log is part of it)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
