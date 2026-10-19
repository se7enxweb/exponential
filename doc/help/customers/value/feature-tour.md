---
title: Feature tour
audience: customer, businesses, editor
category: customer-success
order: 50
summary: The main features of Exponential in one tour for site owners - content, pages, languages and sites, commerce, communication, integration, the writing assistant, operations and security - with who on your team uses each and where to read more.
keywords: feature tour, features, what Exponential does, content, layouts, multilingual, multisite, shop, newsletters, REST API, Exponential Intelligence, audit trail, security
type: help
---
# Feature tour

This page is for owners who want to know everything their site can do, not only what it was first built for. Each
area lists the features that ship with Exponential, who on a typical team uses them, and where to read more. The
lists come from the repository's `README.md` ("Main Exponential features" and "Recent improvements"); which of them
your site uses depends on how it was built and which extensions are active.

## Content

| Feature | Who uses it | Read |
|---|---|---|
| Content classes you define (articles, products, events ...) | Partner defines, editors fill | [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md) |
| Version control: every publish keeps the earlier version | Editors | [Publishing and versions](../../publishing-and-versions.md) |
| Content approval and scheduled publication | Editors, approvers | [Workflows](../../../guides/workflows.md) |
| Multimedia with automatic image conversion and scaling | Editors | [Media and uploads](../../media-and-uploads.md) |
| Built-in search engine | Visitors, editors | [Searching](../../searching.md) |
| Fine-grained, role-based permissions | Owner decides, administrator sets | [Roles and policies](../../../guides/roles-and-policies.md) |

## Pages

| Feature | Who uses it | Read |
|---|---|---|
| Exponential Layouts: layouts, zones and blocks arranged by editors, and rules that decide which pages get them | Editors, marketing | [The Layouts editor](../../layouts-editor.md) |
| Shared layouts: a header or footer built once and linked into other layouts | Partner sets up, editors maintain | `README.md`, "Exponential Layouts" |
| Separation of content and presentation, an extendable template engine | Partner, designer | [Template overrides](../../creators/website-creators/template-overrides.md) |

## Languages and sites

| Feature | Who uses it | Read |
|---|---|---|
| Multilingual content, with 219 locales | Translators, editors | [Languages](../../languages.md), [Content languages](../../../guides/content-languages.md) |
| Right-to-left languages | Editors | [Right to left](../../../features/6.0/right-to-left.md) |
| Several sites from one installation | Owner, administrator | [Multisite setup](../../creators/website-creators/multisite-setup.md) |

## Commerce

| Feature | Who uses it | Read |
|---|---|---|
| The built-in shop: products, basket, checkout, orders, VAT, currencies, discounts, wishlist | Store team | [Running your store](../commerce/README.md) |
| The Store dashboard with work to do and next steps | Store manager | [Store reports](../commerce/store-reports.md) |
| Permanent order receipts | Customers, store manager | [Invoices and receipts](../commerce/invoices-and-receipts.md) |

## Communication

| Feature | Who uses it | Read |
|---|---|---|
| Contact forms (the information collector) | Visitors; staff read the submissions | [Collected information](../../../guides/collected-information.md) |
| RSS, ATOM, iTunes RSS and OPML feeds | Readers, partners, podcast listeners | [RSS feeds](../../../guides/rss-feeds.md) |
| E-mail preferences: one page where people choose which mail they get | Visitors, members | [E-mail preferences](../../../features/6.0/mail-preferences.md) |
| Notifications when followed content changes | Editors, members | [Notifications](../../../features/6.0/notifications.md) |
| Newsletters with the `cjw_newsletter` extension | Marketing | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |

## Integration

| Feature | Who uses it | Read |
|---|---|---|
| A headless CRUD REST API | Developers, apps | [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md) |
| Remote services for apps and other systems | Developers | [Remote services and apps](../../../guides/remote-services-and-apps.md) |
| Thousands of open-source extensions through a plugin API | Partner | `README.md`, "Main Exponential features" |

## The writing assistant

Exponential Intelligence writes drafts in the administration and never publishes on its own; every lookup and draft
runs with the editor's own permissions, costs can be capped, and one setting switches it off everywhere
(`README.md`, "Exponential Intelligence"). Who uses it: editors, with the owner deciding whether and with which
provider ([expai](../../../features/6.0/extensions/expai.md)).

## Operations

| Feature | Who uses it | Read |
|---|---|---|
| A tamper-evident audit trail: who did what | Owner, administrator | [The audit trail](../../../features/6.0/audit-trail.md) |
| The content finder: find content by id, address or file path | Administrator, senior editors | [The content finder](../../../features/6.0/content-finder.md) |
| Content jobs: large removes, copies and moves in the background | Editors, administrator | [Content jobs](../../../features/6.0/content-jobs.md) |
| The log viewer | Administrator, host | [The log viewer](../../../features/6.0/log-viewer.md) |
| Maintenance mode with a friendly page | Administrator | [Maintenance mode](../../../guides/maintenance-mode.md) |
| The upgrade check of files and database | Administrator, partner | [Upgrade check](../../../guides/upgrade-check.md) |
| Exponential Velocity, the bundled application server | Host | [Velocity for businesses](../../businesses/reliability/velocity-for-businesses.md) |
| The help system inside the administration (`/exphelp/dashboard`) | Staff whose role has `exphelp/read` | [The help system](../../../features/6.0/help-system.md) |

## Security

| Feature | Who uses it | Read |
|---|---|---|
| Two-step sign-in | Everyone with an account | [Two-factor login](../../../guides/two-factor-login.md) |
| Form tokens against cross-site request forgery | Automatic | ["This form has expired"](../../../features/6.0/form-expired-page.md) |
| Content Security Policy | Host, administrator | [Content Security Policy](../../../features/6.0/content-security-policy.md) |
| Hardening advice for production sites | Host, partner | [Security hardening](../../security-hardening.md) |

## Using the tour

Go through it once a year with your partner, in the [annual value review](annual-value-review.md), and mark each
feature: used, not needed, or worth trying. The "worth trying" list is often the cheapest improvement you can make.

## Related pages

- [Features customers often miss](features-customers-often-miss.md)
- [What the built-in shop does](../../businesses/stores/what-the-built-in-shop-does.md)
- [What you do not pay for](../../businesses/costs/what-you-do-not-pay-for.md)
- [Annual value review](annual-value-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
