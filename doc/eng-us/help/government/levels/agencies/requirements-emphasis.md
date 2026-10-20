---
title: Requirements emphasis for agencies
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for an agency site - accuracy and traceability of guidance, accessibility, security, personal data in decisions, open data - and what Exponential provides and leaves to the agency.
keywords: requirements, agency, regulator, traceability, guidance history, audit trail, accessibility, security, personal data, enforcement, open data, DCAT
type: help
---
# Requirements emphasis for agencies

This page is for project managers and administrators who set the requirements of a regulator, inspectorate or
executive agency site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Accuracy and traceability](#accuracy-and-traceability)
2. [Accessibility](#accessibility)
3. [Security](#security)
4. [Personal data in decisions](#personal-data-in-decisions)
5. [Open data](#open-data)

## Accuracy and traceability

The question "what did your guidance say on that date?" must be answerable.

| Need | Exponential | Gap or your work |
|---|---|---|
| Every published version kept | archived versions per object | the limit `[VersionManagement] DefaultVersionHistoryLimit` (45 in the shipped `settings/content.ini`) removes the oldest archived version when reached; raise it for the guidance class with `VersionHistoryClass[<class id>]` or archive versions elsewhere ([Content model and editing](../../../../guides/content-model-and-editing.md)) |
| Who published and approved | the audit trail, on by default, hash-chained; collaboration items for approvals | set retention to your records schedule ([Audit trail](../../../../features/6.0/audit-trail.md)) |
| What changed | **Compare** on the versions page; a change note attribute | write change notes |
| A dated copy outside the CMS | PDF export of a subtree; your records system | [PDF exports](../../../../guides/pdf-exports.md) |

Exponential has no built-in public "version archive" view of past versions; past versions are visible in the
administration. If the public must see earlier versions, publish them as separate objects (for example a superseded
Guidance object with its state set to *Superseded*).

## Accessibility

Agency sites are public-sector sites under accessibility law in many jurisdictions: the EU Web Accessibility
Directive, [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj), and in the United States
[Section 508](https://www.section508.gov/) for federal agencies. Exponential's status: WCAG 2.2 AA **Partly
complies** for the admin4 administration, EN 301 549 and Section 508 **Unverified**, public designs not audited, no
VPAT ([Accessibility standards](../../../../standards/accessibility.md)). Long guidance documents need structured
headings in HTML, not only PDF.

## Security

| Topic | Exponential | Read |
|---|---|---|
| Staff accounts | two-step sign-in, enforceable | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Least privilege | roles with section and state limitations | [Roles and policies](../../../../guides/roles-and-policies.md) |
| Monitoring | audit alert rules such as `brute_force`; request logs | [Audit trail](../../../../features/6.0/audit-trail.md), [Request logs](../../../../features/6.0/request-logs.md) |
| Status | OWASP Top 10 **Partly complies**; ASVS **Unverified** | [Security standards](../../../../standards/security.md) |

Agencies are often listed as essential or important entities under NIS2,
[Directive (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj), depending on national law.

## Personal data in decisions

Enforcement decisions can name organizations and sometimes individuals. Agree a publication policy: what is
published, for how long, and what is redacted. Then:

- redact documents before upload; the CMS stores and serves what you upload;
- set a publication end (`hide.php` with `[HideSettings]`) where your policy limits the period ([Cronjobs](../../../../guides/cronjobs.md));
- remember that hidden content is still in the database and in old versions; removal is a separate decision;
- search engines and archives may keep copies after a page is hidden; hiding stops the site serving it to visitors, not copies made elsewhere.

The [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) applies in the EU ([Privacy standards](../../../../standards/privacy.md)).

## Open data

Statistics and registers are often expected as open data under the Open Data Directive,
[Directive (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj), in the EU. Exponential serves data files
as File objects and offers RSS and REST APIs ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)); it
does not produce a [DCAT](https://www.w3.org/TR/vocab-dcat-3/) catalog by itself. Use an open license
([Open Definition](https://opendefinition.org/od/2.1/en/)) and state it on each dataset page.

## Related pages

- [Agencies](README.md)
- [The first 90 days for agencies](first-90-days.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
