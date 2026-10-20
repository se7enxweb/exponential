---
title: Requirements emphasis for courts
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for a court or tribunal site - personal data in decisions, integrity of published text, accessibility for court users, security, records - and what Exponential provides and leaves to the court.
keywords: requirements, court, personal data, anonymization, pseudonymization, integrity, audit trail, accessibility, plain language, security, records
type: help
---
# Requirements emphasis for courts

This page is for project managers and administrators of a court or tribunal site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Personal data](#personal-data)
2. [Integrity of the published text](#integrity-of-the-published-text)
3. [Accessibility and plain language](#accessibility-and-plain-language)
4. [Security](#security)
5. [Records](#records)

## Personal data

Decisions, hearing lists and correspondence contain personal data, often of special categories (health, criminal
matters, children). In the EU the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) applies to courts with
particular rules for courts acting in their judicial capacity; national law sets what is published.

| Requirement | Exponential | Your work |
|---|---|---|
| Decisions anonymized before publication | none: Exponential stores what you upload | anonymize with people or a tool, before upload |
| A second check before publication | *Approve* event; a review section | reviewers trained on what to look for |
| Quick withdrawal | hide, remove, version removal ([Publishing decisions with care](publishing-decisions-with-care-for-personal-data.md)) | a written withdrawal procedure |
| Limited publication period where the law sets one | `hide.php` with `[HideSettings]` ([Cronjobs](../../../../guides/cronjobs.md)) | the period per decision type |
| Minimal logs | audit pseudonymization, IP masking ([Privacy standards](../../../../standards/privacy.md)) | retention settings |
| No third-party tracking on decision pages | the administration loads nothing from third parties; the public design is yours | no third-party scripts on the site |

## Integrity of the published text

| Requirement | Exponential | Read |
|---|---|---|
| Every change to a published decision recorded | versions; the audit trail, tamper-evident (hash-chained with signed manifests) | [Audit trail](../../../../features/6.0/audit-trail.md) |
| Who may change published decisions limited | roles with section and state limitations | [Structure, sections and roles for courts](structure-sections-and-roles.md) |
| Files not altered on the server | the upgrade check compares kernel files with `share/filelist.md5`; uploaded files are your backups' job | [Upgrade check](../../../../guides/upgrade-check.md) |
| Configuration locked in production | `ManagedByDeployment` | [Managed by deployment](../../../../features/6.0/managed-by-deployment.md) |

Many courts also publish a checksum or a signed PDF of each decision; that is produced outside Exponential and can be
uploaded with the decision.

## Accessibility and plain language

Court users include people without lawyers, people under stress, people with disabilities and people with limited
command of the language. In the EU, courts' websites fall under [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)
as public sector bodies, with limited exceptions. Exponential's status: admin4 administration **partly complies** with
WCAG 2.2 AA; EN 301 549 and Section 508 **unverified**; public designs not audited
([Accessibility standards](../../../../standards/accessibility.md)). Guidance pages in HTML with a short summary help
more than PDFs; plain language guidance is at [plainlanguage.gov](https://www.plainlanguage.gov/).

## Security

| Measure | Read |
|---|---|
| Two-step sign-in, enforced for everyone with a role beyond reading | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Account lockout on (`MaxNumberOfFailedLogin`), off by default | [Security hardening](../../../security-hardening.md) |
| A request shield in front of the site | [Security hardening: request-shield](../../../security-hardening.md#a-shield-in-front-request-shield) |
| Content Security Policy on the site (off on sites by default) | [Content Security Policy](../../../../features/6.0/content-security-policy.md) |
| Audit alert rules (brute force and others) watched | [Audit trail](../../../../features/6.0/audit-trail.md) |

Status against OWASP Top 10: **Partly complies** ([Security standards](../../../../standards/security.md)). The
judiciary is often critical infrastructure; see [NIST CSF 2.0](https://www.nist.gov/cyberframework) or your national
framework.

## Records

The authoritative record of a decision is the court's case file, not the website. The website copy should be
traceable to it (case number, date) and its publication history should be kept (versions, audit). Set retention of
audit records to your records schedule ([Audit trail](../../../../features/6.0/audit-trail.md)).

## Related pages

- [Courts](README.md)
- [The first 90 days for courts](first-90-days.md)
- [Security standards](../../../../standards/security.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
