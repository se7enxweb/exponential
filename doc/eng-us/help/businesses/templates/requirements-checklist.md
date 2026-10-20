---
title: Requirements checklist
audience: businesses, project-manager, website-creator
category: business-solutions
order: 40
summary: A copy-ready business requirements checklist for an Exponential project, grouped by area (content, editing, users, store, forms, mail, languages, sites, search, apps, operations, security, accessibility), with the built-in feature or help page to check for each line and columns for priority and decision.
keywords: requirements, checklist, scope, must have, should have, features, content, workflow, store, forms, newsletter, multilingual, multisite, search, REST API, hosting, security, accessibility, template
type: help
---
# Requirements checklist

This page is for owners, managers and project leads who must write down what a site must do. Each line names the
help page where you can check how Exponential does it, so the conversation with a partner starts from what is built
in, not from custom code. The project-manager version, with more technical lines, is the requirements checklist in
[Project templates](../../leadership/project-templates.md#requirements-checklist).

## Contents

1. [How to fill it in](#how-to-fill-it-in)
2. [The checklist](#the-checklist)
3. [Lines you add yourself](#lines-you-add-yourself)
4. [After filling it in](#after-filling-it-in)

## How to fill it in

- **Priority**: M = must have, S = should have, C = could have later, N = not needed.
- **Check**: open the linked page, or ask your partner to show it on a test installation.
- **Decision**: built in, extension, custom work, or dropped.
- Delete areas you do not need.

## The checklist

```text
REQUIREMENTS CHECKLIST - <project>   Version <x>, <date>   Owner: <name>
Priority: M / S / C / N     Decision: built-in / extension / custom / dropped

CONTENT AND EDITING                                            Prio  Decision  Notes
[ ] Our own content types (products, events, documents ...)    ___   ________  ______
[ ] Versions and history of every change                       ___   ________  ______
[ ] Approval before publishing; scheduled publishing           ___   ________  ______
[ ] Visual page builder for landing pages                      ___   ________  ______
[ ] Image formats and automatic image sizes                    ___   ________  ______
[ ] Writing assistant for editors (optional)                   ___   ________  ______

USERS AND ACCESS
[ ] Roles with limited rights per section or subtree           ___   ________  ______
[ ] Two-step sign-in for staff                                 ___   ________  ______
[ ] Sign-in with our directory (LDAP)                          ___   ________  ______
[ ] Visitor registration and members-only areas                ___   ________  ______

STORE
[ ] Product catalog, basket, checkout, orders                  ___   ________  ______
[ ] VAT rules, currencies, country groups                      ___   ________  ______
[ ] Payment provider: <name>                                   ___   ________  ______
[ ] Order receipts and confirmation mail                       ___   ________  ______

FORMS AND MAIL
[ ] Contact and lead forms with export                         ___   ________  ______
[ ] Spam protection (form tokens, CAPTCHA)                     ___   ________  ______
[ ] Newsletters with double opt-in                             ___   ________  ______
[ ] Mail consent and one-click unsubscribe                     ___   ________  ______

LANGUAGES AND SITES
[ ] Languages: <list>; right-to-left needed? ___               ___   ________  ______
[ ] Several sites or brands from one installation              ___   ________  ______

SEARCH AND FINDABILITY
[ ] Site search (built-in, or Solr for large sites)            ___   ________  ______
[ ] Friendly URLs, redirects, sitemaps, robots.txt             ___   ________  ______
[ ] Feeds (RSS, Atom, podcasts)                                ___   ________  ______

APPS AND INTEGRATIONS
[ ] REST API for a mobile app or another system                ___   ________  ______
[ ] Connections to: <systems>                                  ___   ________  ______

OPERATIONS
[ ] Hosting model and database chosen                          ___   ________  ______
[ ] Backups with tested restore                                ___   ________  ______
[ ] Maintenance mode for planned work                          ___   ________  ______
[ ] Monitoring and error references                            ___   ________  ______

SECURITY, DATA AND ACCESSIBILITY
[ ] Audit trail of who did what                                ___   ________  ______
[ ] Retention periods set for logs and data                    ___   ________  ______
[ ] Content Security Policy enforced on the public site         ___   ________  ______
[ ] Accessibility standard to meet: <standard>                 ___   ________  ______
```

Where to check each area:

| Area | Help pages |
|---|---|
| Content and editing | [Content model and editing](../../../guides/content-model-and-editing.md), [Content history](../../../guides/content-history.md), [Workflows](../../../guides/workflows.md), [Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md), [Images and image variations](../../creators/website-creators/images-and-image-variations.md), [Exponential Intelligence](../../creators/app-creators/exponential-intelligence.md) |
| Users and access | [Roles and policies](../../../guides/roles-and-policies.md), [Two-step sign-in](../../../guides/two-factor-login.md), [Directory sign-in and LDAP](../intranets/directory-sign-in-and-ldap.md), [Member registration and areas](../../creators/website-creators/member-registration-and-areas.md) |
| Store | [Shop overview](../../creators/website-creators/shop-overview.md), [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md), [Payment gateways](../../creators/website-creators/payment-gateways.md), [Order receipts](../../../features/6.0/order-receipts.md) |
| Forms and mail | [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md), [Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md), [Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md), [Consent and forms](../security/consent-and-forms.md) |
| Languages and sites | [Multilingual sites](../../creators/website-creators/multilingual-sites.md), [Right-to-left sites](../../creators/website-creators/right-to-left-sites.md), [Multisite setup](../../creators/website-creators/multisite-setup.md) |
| Search and findability | [Site search](../../creators/website-creators/site-search.md), [URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md), [Sitemaps](../../creators/website-creators/sitemaps.md), [RSS feeds](../../creators/website-creators/rss-feeds-for-your-site.md) |
| Apps and integrations | [REST API](../../creators/app-creators/rest-api.md), [Headless Exponential](../../creators/app-creators/headless-exponential.md), [OAuth and API keys](../../creators/app-creators/oauth-and-api-keys.md) |
| Operations | [Choosing hosting](../choosing/choosing-hosting.md), [Backups and restores](../../creators/website-creators/backups-and-restores.md), [Maintenance mode](../../../features/6.0/maintenance-mode.md) |
| Security, data, accessibility | [The audit trail](../../../features/6.0/audit-trail.md), [Data retention and deletion](../security/data-retention-and-deletion.md), [Content Security Policy](../../../features/6.0/content-security-policy.md), [Testing accessibility](../../creators/website-creators/testing-accessibility.md) |

## Lines you add yourself

- [ ] Legal and contractual rules specific to your sector (ask your adviser).
- [ ] Integrations with your own systems (ERP, CRM, bookkeeping).
- [ ] Brand and design rules.
- [ ] Performance expectations at your busiest time.
- [ ] Training and documentation for your staff.

## After filling it in

1. Attach it to the [Project brief](project-brief-template.md).
2. Ask each partner to mark, per line, whether it is built in, an extension or custom work, and to show the built-in
   ones on a test installation.
3. Use the answers in the [Vendor evaluation scorecard](vendor-evaluation-scorecard.md).
4. Keep it as the acceptance list for launch ([Quality gates and acceptance](../../leadership/quality-gates-and-acceptance.md)).

## Related pages

- [Project templates](../../leadership/project-templates.md)
- [What you can build](../../creators/website-creators/what-you-can-build.md)
- [Feature highlights for customers](../../business/feature-highlights-for-customers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
