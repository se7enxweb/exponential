---
title: Cost categories for an Exponential site
audience: government, project-manager, hosting
category: public-sector
order: 30
summary: A catalog of the cost categories of owning a website or intranet on Exponential - software, implementation, infrastructure, operations, people, quality, compliance and exit - with what drives each cost and which page of the documentation describes the work behind it.
keywords: cost categories, cost breakdown, budget lines, cost drivers, TCO, hosting cost, implementation cost, support cost, training cost, compliance cost
type: help
---
# Cost categories for an Exponential site

This page is for project managers and finance staff who need a complete list of cost lines before they ask for
quotes. Each category names what drives its cost and the documentation page that describes the work, so you can ask
suppliers to price the right things. It contains no prices.

## Contents
1. [Software](#software)
2. [Implementation](#implementation)
3. [Infrastructure and hosting](#infrastructure-and-hosting)
4. [Operations](#operations)
5. [People](#people)
6. [Quality and compliance](#quality-and-compliance)
7. [Exit](#exit)
8. [A category checklist](#a-category-checklist)

## Software

| Line | Driver | Notes |
|---|---|---|
| Kernel license | none | GNU GPL version 2 or later, no license fee ([License costs](license-costs.md)) |
| Extensions | which extensions you use | most are free software; a commercial extension or service may have its own fee |
| Third-party services | which services the site calls | for example a search service, captcha, analytics or map key; check each provider's terms |
| Development tools | your developers' choices | Git and Composer are free |

## Implementation

| Line | Driver | Documentation |
|---|---|---|
| Content model | number and complexity of content types | [Designing your content model](../../../creators/website-creators/designing-your-content-model.md) |
| Design and templates | custom design versus a shipped theme | [Choosing a design or theme](../../../creators/website-creators/choosing-a-design-or-theme.md) |
| Integrations | other systems to connect | [REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md) |
| Content migration | volume and structure of old content | [Migration hub](../../../../install/migration/README.md) |
| Multisite and languages | number of sites and languages | [Multisite setup](../../../creators/website-creators/multisite-setup.md), [Multilingual sites](../../../creators/website-creators/multilingual-sites.md) |

## Infrastructure and hosting

| Line | Driver | Documentation |
|---|---|---|
| Servers or hosting plan | traffic, number of sites, availability target | [Hosting costs](hosting-costs.md), [Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md) |
| Database | engine choice; SQLite needs no server | [Requirements](../../../../install/02-requirements.md) |
| Search | built-in search or Solr | [Search backends](../../../hosting/basics/search-backends.md) |
| Backups and storage | size of `var/storage`, retention | [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) |
| TLS certificates, domains, CDN | your choices | [TLS and HTTPS](../../../hosting/security/tls-and-https.md), [CDN](../../../hosting/performance/cdn.md) |

## Operations

| Line | Driver | Documentation |
|---|---|---|
| Monitoring and incident handling | availability target | [Monitoring overview](../../../hosting/operations/monitoring-overview.md) |
| Security releases and upgrades | release frequency, custom code | [Upgrade and maintenance costs](upgrade-and-maintenance-costs.md) |
| Support contract | severities and response times | [Support contracts](support-contracts.md) |

## People

| Line | Driver | Documentation |
|---|---|---|
| Web team and editors | content volume, number of departments | [Staff and skills](staff-and-skills.md) |
| Administrators | sites, roles, users | [Site administrators course](../../../trainers/site-administrators/README.md) |
| Training | number of people and roles | [Training costs](training-costs.md) |

## Quality and compliance

| Line | Driver | Documentation |
|---|---|---|
| Accessibility audit and statement | public designs are not audited by the project | [Accessibility standards](../../../../standards/accessibility.md) |
| Security testing | penetration test, re-tests | [Security standards](../../../../standards/security.md) |
| Data protection work | personal data processed, third parties | [Privacy standards](../../../../standards/privacy.md) |
| Records and archiving | retention rules | [Audit trail](../../../../features/6.0/audit-trail.md) |

## Exit

| Line | Driver | Documentation |
|---|---|---|
| Export and verification | content volume, number of classes | [Leaving Exponential](../../../../install/migration/leaving-exponential.md) |
| Redirects and transition | number of addresses | same page, section 4 |
| Parallel running | time the old site stays read-only | same page, checklist |

## A category checklist

```text
[ ] Software: kernel (no fee), extensions, third-party services
[ ] Implementation: content model, design, integrations, migration, sites, languages
[ ] Infrastructure: servers, database, search, backups, TLS, CDN
[ ] Operations: monitoring, releases, upgrades, support
[ ] People: web team, editors, administrators, training
[ ] Quality and compliance: accessibility, security, privacy, records
[ ] Exit: export, redirects, parallel running
[ ] Risk allowance
```

## Related pages
- [Total cost of ownership: overview](README.md)
- [The structure of a TCO model](tco-model-structure.md)
- [TCO worksheet template](tco-worksheet-template.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
