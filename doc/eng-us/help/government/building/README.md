---
title: Building a public-sector site
audience: government, website-creator, project-manager, admin
category: public-sector
order: 10
summary: Index of every building guide of the Governments help - service catalog, forms, citizen accounts, news and alerts, events, documents and publications, search, maps, newsletters, multisite, intranets and extranets - with what each answers and a reading order.
keywords: building, public-sector site, service catalog, forms, accounts, news, alerts, events, documents, search, maps, newsletters, multisite, intranet, extranet
type: help
---
# Building a public-sector site

This section is for website creators, project managers and administrators who build the parts of a public-sector site
with Exponential. Each folder takes one part (services, forms, accounts, news, events, documents, search, maps,
newsletters, several sites, intranets and extranets) and shows what the product provides, how to set it up, and where
it stops. Every product claim is checked against this repository, and every gap is named.

## Contents

1. [The folders](#the-folders)
2. [Reading order](#reading-order)
3. [Common threads](#common-threads)

## The folders

| Folder | The answer in one line |
|---|---|
| [Service catalog](service-catalog/README.md) | A Service class, plain-language pages, life events and topics, findability, contact points, links to transactions, upkeep |
| [Forms](forms/README.md) | The information collector, accessible forms, spam protection and third parties, CSRF tokens, attachments, mail, retention, consultations and polls |
| [Citizen accounts](citizen-accounts/README.md) | Registration, sign-in and sessions, two-step sign-in, password rules, account hygiene, deactivation and removal, support |
| [News and alerts](news-and-alerts/README.md) | News articles, urgent notices, RSS, notifications, newsletters, press releases, archive and expiry, metadata |
| [Events](events/README.md) | What ships (Event, Event calendar, Multicalendar), calendar views, upcoming lists, sign-ups, changes, accessibility, archive |
| [Documents and publications](documents-and-publications/README.md) | Files, a document library, versions, metadata, PDF export, accessible documents, OpenDocument, download permissions |
| [Search](search/README.md) | The built-in engine, searchable fields, Solr with ezfind, statistics and their privacy duties |
| [Maps](maps/README.md) | The `ezgmaplocation` field with Google Maps and what does not exist |
| [Newsletters](newsletters/README.md) | cjw_newsletter, double opt-in, rehearsal, list hygiene, one-click unsubscribe |
| [Multisite](multisite/README.md) | One installation for many departments: sites, designs, shared content, roles, operations |
| [Intranets](intranets/README.md) | A staff-only siteaccess, sections and roles, sign-in options, sessions |
| [Extranets](extranets/README.md) | Contractors and partners with access to their own content only, accounts and their end |

## Reading order

1. **Service catalog**: for most public bodies the core of the site.
2. **Documents and publications** and **News and alerts**: the content that grows fastest.
3. **Forms** and **Citizen accounts**: everything that collects personal data.
4. **Search**, **Events**, **Newsletters** and **Maps** as needed.
5. **Multisite**, **Intranets** and **Extranets** when more than one audience or organization shares the platform.

Up: [Governments: start here](../government-start-here.md). Planning per kind of organization is in
[Levels of government](../levels/README.md).

## Common threads

| Thread | Where it recurs | Read |
|---|---|---|
| Accessibility of what you build | forms, events, documents, notices | [Accessibility standards](../../../standards/accessibility.md) |
| Personal data and retention | forms, accounts, events, newsletters | [Privacy standards](../../../standards/privacy.md) |
| Third parties (CAPTCHA, maps, analytics, social sharing) | forms, maps, news | the privacy guidance of each page |
| Caches and urgent content | notices, events, alerts | [Caches](../../../guides/caches.md) |
| Roles and sections | every folder | [Roles and policies](../../../guides/roles-and-policies.md), [Sections](../../../guides/sections.md) |
| Dates that drive lists and expiry | news, events, notices, services | [Cronjobs](../../../guides/cronjobs.md) |

## Related pages

- [Levels of government](../levels/README.md)
- [Website creators start here](../../creators/website-creators/website-creators-start-here.md)
- [Planning your website](../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
