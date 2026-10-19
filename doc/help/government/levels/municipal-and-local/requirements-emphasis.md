---
title: Requirements emphasis for municipal and local government
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for a municipal site - accessibility for every resident, plain language, residents' languages, security run by a small team, records of council business, privacy of forms - and what Exponential provides for each.
keywords: requirements, accessibility, plain language, languages, security, small team, council records, forms, privacy, retention, municipal
type: help
---
# Requirements emphasis for municipal and local government

This page is for project managers and administrators who set the requirements of a city, town or council site. It
names what weighs most at local level and what Exponential provides, with the page that documents each feature.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Accessibility for every resident](#accessibility-for-every-resident)
2. [Plain language and residents' languages](#plain-language-and-residents-languages)
3. [Security with a small team](#security-with-a-small-team)
4. [Records of council business](#records-of-council-business)
5. [Forms and privacy](#forms-and-privacy)

## Accessibility for every resident

Municipal sites serve everyone, including older people and people with disabilities who depend on local services.
In the EU, local authorities are public sector bodies under the Web Accessibility Directive,
[Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj); [WCAG 2.2](https://www.w3.org/TR/WCAG22/)
is the reference most rules point to.

Exponential's administration (admin4, admin4l) **partly complies** with WCAG 2.2 AA; EN 301 549 and Section 508 are
**unverified**; public designs are not audited ([Accessibility standards](../../../../standards/accessibility.md)). For
your site that means:

| Area | Your work |
|---|---|
| Design templates | test with keyboard, screen reader and zoom ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)) |
| Images | switch on **Alternative text is required** on image attributes ([Content model and editing, 9](../../../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)) |
| Forms | labels, error messages, no CAPTCHA without an alternative |
| Documents | an HTML summary on the page; accessible PDFs from the source |
| Accessibility statement | write and publish it ([W3C guidance](https://www.w3.org/WAI/planning/statements/)) |

## Plain language and residents' languages

Service pages work only if residents understand them. Plain language guidance from government is collected at
[plainlanguage.gov](https://www.plainlanguage.gov/) (US federal). Exponential cannot judge plainness; a class with a
short **In short** field and a review process do.

Many municipalities offer key pages in languages residents speak beyond the official one. Translations per object and
a siteaccess per language support this ([Content languages](../../../../guides/content-languages.md)); a partly
translated site should say which pages exist in which language. Right-to-left languages are supported
([Right-to-left languages](../../../../features/6.0/right-to-left.md)).

## Security with a small team

A small team cannot watch the site every hour, so the defaults matter:

| Measure | Exponential | Read |
|---|---|---|
| Two-step sign-in for every staff account | sevenx_authentication_2fa, `Enforce2FA` | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Account lockout after failed sign-ins | `MaxNumberOfFailedLogin`, off by default | [Security hardening](../../../security-hardening.md) |
| CSRF protection of every form | `ezformtoken` | [Form expired page](../../../../features/6.0/form-expired-page.md) |
| Audit of changes | on by default | [Audit trail](../../../../features/6.0/audit-trail.md) |
| Updates | upgrade check and update guide | [Upgrade check](../../../../guides/upgrade-check.md), [Upgrading](../../../../guides/upgrading.md) |

Write down who applies security updates, especially when a supplier hosts the site.

## Records of council business

Agendas, minutes and decisions are records. Exponential keeps versions and an audit trail; the formal archive is
usually your records system. Keep corrections visible (a new version with a note), and decide how long old versions
are kept: `[VersionManagement] DefaultVersionHistoryLimit` is 45 in the shipped `settings/content.ini`, and `VersionHistoryClass[<class id>]` sets a limit per class
([Content model and editing](../../../../guides/content-model-and-editing.md)).

## Forms and privacy

Report and request forms collect names, addresses and e-mail addresses. Exponential stores submissions in
**Setup > Collected information**, exports them as CSV (each export is recorded in the audit) and removes them on
request ([Collected information](../../../../guides/collected-information.md)). It does not delete old submissions by
itself: schedule a review. CAPTCHA extensions (`recaptcha`, `hcaptcha`) are third-party services
([Spam protection for forms](../../../creators/website-creators/spam-protection-for-forms.md)). Exponential ships no
cookie banner ([Privacy standards](../../../../standards/privacy.md)); see the
[GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) in the EU.

## Related pages

- [Municipal and local government](README.md)
- [The first 90 days for municipal and local government](first-90-days.md)
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
