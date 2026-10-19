---
title: Requirements emphasis for national government
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Which requirements weigh most for a ministry or central department site - accessibility, official languages, security and availability, records - and what Exponential provides and leaves to you for each.
keywords: requirements, accessibility, WCAG, EN 301 549, Section 508, official languages, security, NIS2, availability, records, retention, audit, national government
type: help
---
# Requirements emphasis for national government

This page is for project managers and administrators who write or check the requirements of a ministry or central
department site. It says which requirements usually weigh most at national level and, for each, what Exponential
provides (with the page that proves it) and what remains your organization's work.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Accessibility](#accessibility)
2. [Official languages](#official-languages)
3. [Security and availability](#security-and-availability)
4. [Records and history](#records-and-history)
5. [Privacy](#privacy)
6. [A requirements table to start from](#a-requirements-table-to-start-from)

## Accessibility

Public-sector websites are subject to accessibility law in many jurisdictions. In the EU the Web Accessibility
Directive, [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj), applies to public sector
bodies, with the harmonized standard [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf);
in the United States, federal agencies follow [Section 508](https://www.section508.gov/) and the
[Revised 508 Standards](https://www.access-board.gov/ict/). Both build on [WCAG](https://www.w3.org/TR/WCAG22/).

What Exponential states about itself ([Accessibility standards](../../../../standards/accessibility.md)):

| Standard | Status | Meaning for you |
|---|---|---|
| WCAG 2.2 AA | Partly complies | the admin4 and admin4l administration is built and tested against AA criteria; public designs are not audited |
| EN 301 549 | Unverified | no conformance report exists |
| Section 508 | Unverified | no VPAT or Accessibility Conformance Report exists |

So the accessibility of your **public site** depends on your design, templates and content. Budget for an audit of
your own templates, for training editors (alternative texts, headings, link texts, accessible documents) and for an
accessibility statement, which Exponential does not generate ([W3C guidance on statements](https://www.w3.org/WAI/planning/statements/)).
Image fields can require an alternative text ([Content model and editing, 9](../../../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)).

## Official languages

Where several languages are official, each page usually must be available in each before or at publication.

| Need | Exponential | Read |
|---|---|---|
| One object, several languages | translations per object, one siteaccess per language | [Content languages](../../../../guides/content-languages.md) |
| Language switcher and language URLs | siteaccess per language, switcher templates | [Language switcher and language URLs](../../../creators/website-creators/language-switcher-and-language-urls.md) |
| Right-to-left languages | `dir="rtl"` support | [Right-to-left languages](../../../../features/6.0/right-to-left.md) |
| Approval per language | the *Approve* event's **Affected languages** | [Workflows](../../../../guides/workflows.md) |

There is no built-in rule "publish only when all languages exist". Enforce it by process: translators work in drafts,
and an approver checks the translations before approving.

## Security and availability

National sites are frequent targets and must stay up when news breaks. In the EU, many central government entities
fall under the NIS2 Directive, [Directive (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj); the
[NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework) is a common reference elsewhere.

| Topic | What ships | Read |
|---|---|---|
| OWASP Top 10 | mitigations for all ten categories, status "Partly complies" | [Security standards](../../../../standards/security.md) |
| Two-step sign-in for staff | sevenx_authentication_2fa, can be enforced | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Account lockout | available, off by default (`MaxNumberOfFailedLogin`) | [Security hardening](../../../security-hardening.md) |
| Content Security Policy | report-only on the admin by default, off on sites | [Content Security Policy](../../../../features/6.0/content-security-policy.md) |
| Load peaks | view cache, static cache, Velocity response cache | [Performance and caching](../../../creators/website-creators/performance-and-caching.md) |
| Planned downtime | maintenance mode with 503 and `Retry-After` | [Maintenance mode](../../../../guides/maintenance-mode.md) |

Your work: a threat model, hosting with redundancy, backups tested by restoring, monitoring, an incident process and
a vulnerability disclosure route. Exponential ships `SECURITY.md` but no `security.txt`
([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)); publish one yourself.

## Records and history

| Need | Exponential | Gap |
|---|---|---|
| Who changed what, when | versions per object, the audit trail (on by default, hash-chained) | your retention schedule decides how long |
| Earlier wording of a page | archived versions, compare, new draft from an old version | old versions are removed beyond the version history limit (`DefaultVersionHistoryLimit`, 45 in the shipped `settings/content.ini`; `VersionHistoryClass[<class id>]` per class) |
| Withdrawn content still findable | hide, object states, a Withdrawn section | no built-in "superseded by" relation; add an `ezobjectrelation` attribute |

If your records rules need every published version kept, raise the version history limit for those classes or export
publications to your records system. Records management standards such as
[ISO 15489-1](https://www.iso.org/standard/62542.html) describe the principles.

## Privacy

Forms, accounts and logs hold personal data. Exponential provides consent records for e-mail, erasure on account
removal, audit pseudonymization and log masking, and no cookie banner ([Privacy standards](../../../../standards/privacy.md));
the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) applies in the EU.

## A requirements table to start from

| ID | Requirement | Check |
|---|---|---|
| R1 | Public templates meet WCAG 2.2 AA | external audit of your templates |
| R2 | Every page available in each official language | approval checklist |
| R3 | Staff sign in with two steps | `Enforce2FA=enabled`, tested |
| R4 | Every change is attributable | audit trail on, retention set |
| R5 | Site survives the busiest day | load test with caches on |
| R6 | Planned work shows a maintenance page | maintenance mode rehearsed |

The requirement sections of this Governments help go deeper: [accessibility](../../accessibility/README.md), [security](../../security/README.md), [privacy](../../privacy/README.md) and [records and archiving](../../records-and-archiving/README.md).

## Related pages

- [National government](README.md)
- [The first 90 days for national government](first-90-days.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
