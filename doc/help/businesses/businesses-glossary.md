---
title: Businesses glossary
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 95
summary: Plain-language explanations of the words used in the Businesses section, from siteaccess and section to cache, Velocity, REST API, VAT type, country group, RTO and total cost of ownership, each with a link to where it is explained in full.
keywords: glossary, terms, definitions, siteaccess, section, role, policy, content class, object, node, draft, version, extension, cache, view cache, static cache, Velocity, REST API, OAuth, API key, VAT, country group, workflow, audit trail, SLA, RTO, RPO, TCO, ROI, GDPR, extranet, intranet, headless
type: help
---
# Businesses glossary

The words used in the Businesses section, explained for owners and managers. Each entry links to the page that
explains it in full. The technical glossary of the whole documentation is in [Glossary](../../glossary.md).

## Contents

1. [Content and editing](#content-and-editing)
2. [Sites, users and permissions](#sites-users-and-permissions)
3. [Selling](#selling)
4. [Running the site](#running-the-site)
5. [Business and contract terms](#business-and-contract-terms)

## Content and editing

| Term | Meaning | Read |
|---|---|---|
| Content class | The kind of thing you store (product, article, event, member), with its fields. You define your own | [The content model and editing content](../../guides/content-model-and-editing.md) |
| Content object | One item of a class: one product, one article | same |
| Node (location) | Where an object sits in the content tree. An object can have several locations | same |
| Draft | An unpublished version being edited | [Drafts and pending items](../../guides/drafts-and-pending.md) |
| Version | Each saved state of an object. Older versions can be compared and restored | [Content history](../../guides/content-history.md) |
| Workflow | Steps that run when something happens, for example approval before publishing or payment at checkout | [Workflows](../../guides/workflows.md) |
| Exponential Layouts | The visual page builder: layouts, zones and blocks that editors arrange | [The layouts editor](../layouts-editor.md) |
| Exponential Intelligence | The writing assistant; writes drafts only, never publishes on its own | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| Information collector | Lets a page collect form input from visitors (contact forms, sign-ups) | [Collected information](../../guides/collected-information.md) |

## Sites, users and permissions

| Term | Meaning | Read |
|---|---|---|
| Siteaccess | One way into the installation: a public site, a language version, the administration, a partner portal. Chosen by host name, path or port | [Siteaccess host and language matching](../../features/6.0/siteaccess-host-and-language-matching.md) |
| Multisite | Several sites served from one installation | [Multi-site brands](multisite/README.md) |
| Role | A named set of permissions, assigned to users or groups | [Roles, policies and unactivated users](../../guides/roles-and-policies.md) |
| Policy | One permission inside a role, such as "read content", optionally limited | same |
| Section | A label on part of the content tree used to limit permissions and designs (for example "Partners only") | [Sections](../../guides/sections.md) |
| Object state | A status on content (for example "approved", "archived") that permissions can depend on | [Object states](../../guides/object-states.md) |
| Intranet | A site for staff only | [Intranets](intranets/README.md) |
| Extranet | A site for signed-in outsiders: partners, dealers, suppliers, customers | [Extranets and partner portals](extranets/README.md) |
| Two-factor login | Sign-in with a password and a second code | [Two-factor login](../../guides/two-factor-login.md) |

## Selling

| Term | Meaning | Read |
|---|---|---|
| Shop | The built-in e-commerce: products, basket, orders | [Online stores](stores/README.md) |
| VAT type | A tax rate that products are assigned to | [VAT rules and VAT types](stores/vat-rules-and-vat-types.md) |
| Country group | A set of countries handled the same way, for example for VAT | [Country groups](../../features/6.0/country-groups.md) |
| Payment gateway | The connection to a payment provider, run as a workflow event at checkout | [Payment gateways for businesses](stores/payment-gateways-for-businesses.md) |
| Order status | Where an order is in its life: pending, processing, delivered and others you define | [Orders and order statuses](stores/orders-and-order-statuses.md) |
| Headless | Using the content without the website, for example from an app | [Mobile and headless](mobile-and-headless/README.md) |
| REST API | The interface apps and other systems use to read and write content | [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) |
| API key / OAuth | Two ways an app proves who it is to the REST API | [Personal API keys](../../guides/api-keys.md) |
| Feed (RSS, ATOM) | A machine-readable list of new content that other sites and apps pick up | [RSS feeds](../../guides/rss-feeds.md) |
| Syndication | Sharing part of the content tree with another installation | [syndication](../../features/6.0/extensions/syndication.md) |

## Running the site

| Term | Meaning | Read |
|---|---|---|
| Velocity | Exponential's own application server, which keeps the application loaded between requests | [Velocity for businesses](reliability/velocity-for-businesses.md) |
| Cache | Stored results that make pages fast; cleared when content changes | [Caches](../../guides/caches.md) |
| View cache | The cached output of a content page | [Caching for availability](reliability/caching-for-availability.md) |
| Static cache | Pages written to disk as plain files | [Static and response cache](reliability/static-and-response-cache.md) |
| Cronjob | A task that runs on a schedule, for example sending notifications | [Cronjobs](../../guides/cronjobs.md) |
| Maintenance mode | A page shown to visitors while work is done | [Maintenance mode](../../guides/maintenance-mode.md) |
| Audit trail | A tamper-evident record of who changed what | [The audit trail](../../features/6.0/audit-trail.md) |
| Upgrade check | A check of the database and files against the release before an upgrade | [Upgrade check](../../guides/upgrade-check.md) |
| Extension | A package that adds features | [Extensions](../../guides/extensions.md) |
| Cluster | Several servers sharing the work and the files | [Clustering and high availability](reliability/clustering-and-high-availability.md) |

## Business and contract terms

These are general terms, not specific to Exponential.

| Term | Meaning | Read |
|---|---|---|
| TCO (total cost of ownership) | All the costs of a system over its life, not only the purchase or build | [Total cost of ownership](costs/total-cost-of-ownership.md) |
| ROI (return on investment) | What a system earns or saves compared with what it costs | [ROI of stability](costs/roi-of-stability.md) |
| SLA (service level agreement) | A written promise about availability, response and restore times | [Contracts and SLA basics](choosing/contracts-and-slas-basics.md) |
| RTO (recovery time objective) | How long it may take to be back after a failure | [Backups that restore](reliability/backups-that-restore.md) |
| RPO (recovery point objective) | How much recent data you can afford to lose; decides how often you back up | same |
| GDPR | The European Union's General Data Protection Regulation | [GDPR basics](security/gdpr-basics.md) |
| GPL | The GNU General Public License, the license of Exponential | [Licensing for buyers](../business/licensing-for-buyers.md) |
| GFDL | The GNU Free Documentation License, the license of this documentation | [Free documentation](../free-documentation.md) |

## Related pages

- [Businesses: start here](businesses-start-here.md)
- [Businesses FAQ](businesses-faq.md)
- [Glossary for hosts](../hosting/glossary-for-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
