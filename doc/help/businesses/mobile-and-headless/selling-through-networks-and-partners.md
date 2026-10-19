---
title: Selling through networks and partners
audience: businesses, sales-marketing, partner
category: business-solutions
order: 80
summary: How a business uses Exponential to reach customers beyond its own website - partner and dealer sites, apps, marketplaces and data feeds, search engines and social networks - and which feature fits each channel, with the agreements and controls to put in place.
keywords: sell online, partners, dealers, resellers, marketplaces, networks, product feeds, REST API, syndication, xrowextract, sitemaps, Open Graph, partner portal, business
type: help
---
# Selling through networks and partners

This page is for owners, sales leads and partner managers who want to sell or be found outside their own website:
through dealers and resellers, on partner sites, in a mobile app, in marketplaces and comparison sites that take data
files, in search engines and on social networks. It maps each channel to what Exponential provides and lists what
to agree with partners. It does not promise any specific marketplace integration: where the sources describe none,
this page says so.

## Contents

1. [Channels and the matching features](#channels-and-the-matching-features)
2. [Partner and dealer sites](#partner-and-dealer-sites)
3. [Data files for marketplaces and partners](#data-files-for-marketplaces-and-partners)
4. [Search engines and social networks](#search-engines-and-social-networks)
5. [Your own app as a channel](#your-own-app-as-a-channel)
6. [Agreements and controls](#agreements-and-controls)

## Channels and the matching features

| Channel | Feature | Read |
|---|---|---|
| Your website and store | The built-in shop | How businesses sell online, [Stores](../stores/README.md) |
| Partner or dealer sites on Exponential | `syndication`, one way or both ways | [Feeds and syndication](feeds-and-syndication.md#syndication-between-installations) |
| Partner or dealer sites on other systems | RSS or Atom feeds; the REST API; scheduled file exports | below |
| A portal where partners sign in | An extranet; the remote services and sample portals | [Extranets](../extranets/README.md), [Remote services](remote-services.md) |
| Marketplaces and comparison sites that take files | Scheduled exports with `xrowextract` | below |
| Search engines | Sitemaps and page metadata | below |
| Social networks | Open Graph tags | below |
| Your own mobile app | The REST API | [Headless with the REST API](headless-with-the-rest-api.md) |
| Feed readers and podcast apps | RSS, Atom, Apple Podcasts | [Feeds and syndication](feeds-and-syndication.md) |

## Partner and dealer sites

Three ways, depending on what the partner runs:

- **The partner runs Exponential too**: `syndication` exports part of your content tree as a feed that the partner's
  installation imports, kept in step by cron. Its documentation names "portal networks" and "large e-commerce
  networks" among the uses of two-way syndication, and imports can authenticate against a protected source
  ([syndication](../../../features/6.0/extensions/syndication.md)).
- **The partner runs another system**: give them a feed (RSS or Atom) of the products or news they may show, or an
  integration user with a read-only key for the REST API ([Apps and API keys](apps-and-api-keys.md)).
- **The partner signs in to you**: a dealer or partner area on your own site, with registration, approval and
  permissions ([Dealer and supplier portals](../extranets/dealer-and-supplier-portals.md)).

## Data files for marketplaces and partners

Many marketplaces, comparison sites and wholesale partners accept product data as files. `xrowextract`, the
**Export** tab of the administration, "turns content into files and reads files back into content": CSV, JSON, XML and
content packages, with previews, filters, saved presets, background jobs, scheduled runs and delivery to SFTP, S3 and
other destinations ([xrowextract](../../../features/6.0/extensions/xrowextract.md)). The older `ezxmlexport` is kept
for sites that still run it; for new work its page points to `xrowextract`.

> **Note**
> The sources describe no ready-made connector for a specific marketplace. Each marketplace has its own file format
> and rules; your developers map your product fields to it with an export preset, or build an integration on the REST
> API or the remote services. Budget that mapping per marketplace.

## Search engines and social networks

| Need | Feature | Read |
|---|---|---|
| Search engines find all public pages | XML sitemaps per siteaccess, with a multilingual variant (`bcgooglesitemaps`), or sitemaps from `xrowmetadata` | [Sitemaps](../../creators/website-creators/sitemaps.md), [bcgooglesitemaps](../../../features/6.0/extensions/bcgooglesitemaps.md) |
| Titles, descriptions and canonical links per page | `xrowmetadata`'s meta data attribute | [Page metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md) |
| Good previews when a link is shared | Open Graph tags; the simple theme prints `og:title`, `og:site_name` and an `og:image`; `xrowmetadata` adds a per-page Open Graph image | same |
| Basics of being found | Addresses, headings, language versions | [SEO basics for Exponential sites](../../creators/website-creators/seo-basics.md) |

## Your own app as a channel

An app reads your products and content through the REST API; a basket, checkout and order history can be offered
through the remote services, which include products, basket and checkout, orders, payments and shipping
([Remote services](remote-services.md#what-can-be-done-through-it)). Payments themselves go through the payment
gateways of your shop ([Payment gateways for businesses](../stores/payment-gateways-for-businesses.md)). Read
[Mobile strategy](mobile-strategy.md) before committing to an app.

## Agreements and controls

Before content or data leaves your installation for a partner:

- [ ] **What may the partner show?** Name the folders, classes and fields; give access to nothing else.
- [ ] **Which credential?** One per partner, with the smallest scope, revoked when the contract ends
      ([House rules for a business](apps-and-api-keys.md#house-rules-for-a-business)).
- [ ] **Prices and stock**: who is responsible when a partner shows an outdated price? Agree refresh intervals.
- [ ] **Rights**: images and texts you send may be licensed only for your own use; check before you syndicate.
- [ ] **Personal data**: do partners receive customer data? If so, put a data processing agreement in place
      ([Security](../security/README.md)); this page is not legal advice.
- [ ] **Monitoring**: feeds, exports and imports run by cron; someone checks they ran
      ([App backend reliability](app-backend-reliability.md)).
- [ ] **An exit plan**: how a partner's access and copies are removed when you part.

## Related pages

- [Feeds and syndication](feeds-and-syndication.md)
- [Remote services](remote-services.md)
- How businesses sell online
- [Promoting your store](../stores/promoting-your-store.md)
- [Partner portal planning](../extranets/partner-portal-planning.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
