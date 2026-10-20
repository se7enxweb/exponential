---
title: Requirements emphasis for regional and state government
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for a regional or state site - accessibility, regional and minority languages, security, records of assembly decisions, privacy of third-party maps - and what Exponential provides for each.
keywords: requirements, accessibility, regional languages, minority languages, translations, security, records, assembly decisions, privacy, maps, third party
type: help
---
# Requirements emphasis for regional and state government

This page is for project managers and administrators who set the requirements of a regional or state site. It
names the requirements that usually weigh most at this level and what Exponential provides for each, with the page
that documents it.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Accessibility](#accessibility)
2. [Regional and minority languages](#regional-and-minority-languages)
3. [Security](#security)
4. [Records of decisions](#records-of-decisions)
5. [Privacy and third parties](#privacy-and-third-parties)
6. [Summary table](#summary-table)

## Accessibility

Regional and state bodies are public sector bodies under the EU Web Accessibility Directive,
[Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj), in the EU. In the United States,
[Section 508](https://www.section508.gov/) binds federal agencies; state rules vary and many refer to
[WCAG](https://www.w3.org/TR/WCAG22/).

Exponential's own status ([Accessibility standards](../../../../standards/accessibility.md)): WCAG 2.2 AA **Partly
complies** for the admin4 and admin4l administration; EN 301 549 and Section 508 **Unverified**; public designs not
audited; no VPAT and no accessibility statement template. Your public design and your documents are yours to test.

Regional sites publish many plans and maps as PDF. A plan's landing page should carry an HTML summary; a map should
have a text description of what it shows ([Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)).

## Regional and minority languages

| Need | Exponential | Read |
|---|---|---|
| Pages in the national and the regional language | translations per object, a siteaccess per language | [Content languages](../../../../guides/content-languages.md) |
| An interface in the regional language for editors | interface translations; missing strings fall back | [Translations and languages](../../../../features/6.0/translations-and-languages.md) |
| A page only in one language | the object exists only in that language; "always available" decides whether others show it | [Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md) |
| Language tags in the HTML | the siteaccess locale sets `lang` | [Internationalization standards](../../../../standards/internationalization.md) |

If the law requires equal treatment of languages, set the **Affected languages** of the approval so that every
language version is approved, and track missing translations on the object's **Translations** tab.

## Security

| Topic | What ships | Read |
|---|---|---|
| Staff sign-in | two-step sign-in, enforceable | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Accounts of municipal staff (outside your organization) | accounts in a group, regular review | [Check user accounts](../../../../features/6.0/user-hygiene.md) |
| Hardening | settings and server measures | [Security hardening](../../../security-hardening.md) |
| Status against OWASP | Partly complies | [Security standards](../../../../standards/security.md) |

Many regional entities fall under NIS2, [Directive (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj),
in the EU, depending on national transposition.

## Records of decisions

Assembly decisions are records. Exponential keeps versions and an audit trail; the formal archive usually remains
your records system.

| Need | Exponential | Read |
|---|---|---|
| The published text does not change silently | versions; compare | [Versions of an object](../../../../guides/content-history.md) |
| Who published when | the audit trail, on by default, tamper-evident | [Audit trail](../../../../features/6.0/audit-trail.md) |
| Old versions kept | `[VersionManagement] DefaultVersionHistoryLimit` (45 in the shipped `settings/content.ini`) and `VersionHistoryClass[<class id>]` per class | [Content model and editing](../../../../guides/content-model-and-editing.md) |
| Export to an archive | PDF export of a subtree; the files themselves | [PDF exports](../../../../guides/pdf-exports.md) |

## Privacy and third parties

Maps from Google (`ezgmaplocation`), CAPTCHA services (`recaptcha`, `hcaptcha`) and embedded videos send visitor data
to third parties. Exponential ships no cookie banner ([Privacy standards](../../../../standards/privacy.md)). Under the
[GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) and the
[ePrivacy Directive](https://eur-lex.europa.eu/eli/dir/2002/58/oj), decide with your data protection officer which
of these you use and how visitors are informed.

## Summary table

| Requirement | Product support | Your work |
|---|---|---|
| Accessible public site | admin tested; public design yours | audit, statement, editor training |
| Regional language | translations, siteaccesses | translation process |
| Secure staff access | 2FA, roles, audit | enforce, review accounts |
| Decisions as records | versions, audit | retention rules, archive transfer |
| Privacy | consent log for mail, masking in logs | decisions on third parties, privacy notice |

## Related pages

- [Regional and state government](README.md)
- [The first 90 days for regional and state government](first-90-days.md)
- [Languages](../../../languages.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
