---
title: Mobile apps, headless use and networks
audience: businesses, app-creator, project-manager
category: business-solutions
order: 1
summary: The landing page for businesses that reach customers on phones, through their own apps, through partner sites and through feeds - what Exponential offers (responsive designs, REST API v1 and v2, API keys and OAuth, remote services, feeds and syndication), every page in the section and a reading order.
keywords: mobile, apps, headless, REST API, API keys, OAuth, expservices, remote services, feeds, RSS, syndication, partners, networks, responsive, business
type: help
---
# Mobile apps, headless use and networks

This section is for owners, managers and product leads of organizations that sell or publish beyond their own website:
to visitors on phones, through a mobile app of their own, on partner and reseller sites, in feed readers and podcast
directories, or between several installations of their own. It explains what Exponential offers for each channel,
what each one costs you in work and in operations, and where the technical documentation is.

## Contents

1. [What this section covers](#what-this-section-covers)
2. [The channels at a glance](#the-channels-at-a-glance)
3. [Pages in this section](#pages-in-this-section)
4. [Suggested reading order](#suggested-reading-order)
5. [Neighboring sections](#neighboring-sections)

## What this section covers

The project README lists among Exponential's main features "A headless CRUD REST API", RSS, ATOM, iTunes RSS and OPML
feeds, a "SOAP communication library", and "Support for highly available and scalable configurations (multi-server
clusters)". Around those, the 6.0 releases added personal API keys for the REST API, the `expservices` remote services,
a browser navigation cache for repeat visits, and the Velocity application server.

The pages here explain those features for decision makers: what they make possible, and what to arrange so they keep
working.

## The channels at a glance

| Channel | What Exponential offers | Read |
|---|---|---|
| Visitors on phones | Responsive public designs; responsive administration for editors | [Responsive sites](responsive-sites.md) |
| Your own mobile app or front end | The REST content API, read at v1 and v2, write at v2 | [Headless with the REST API](headless-with-the-rest-api.md) |
| Programs and integrations | Personal API keys, OAuth2 for apps, expservices tokens | [Apps and API keys](apps-and-api-keys.md) |
| Remote administration, portals, scripts | Over a thousand named services (expservices) | [Remote services](remote-services.md) |
| Feed readers, podcast directories, aggregators | RSS, Atom, OPML and Apple Podcasts exports | [Feeds and syndication](feeds-and-syndication.md) |
| Your other installations, partner networks | The `syndication` extension, one-way and two-way | [Feeds and syndication](feeds-and-syndication.md) |
| Resellers, partners, marketplaces | Combinations of the above | [Selling through networks and partners](selling-through-networks-and-partners.md) |
| Repeat visitors | An optional navigation cache in the browser | [Offline and repeat visits](offline-and-repeat-visits.md) |

## Pages in this section

| Page | What it answers |
|---|---|
| [Mobile strategy](mobile-strategy.md) | Website, app, or both: choosing channels by what your customers do |
| [Responsive sites](responsive-sites.md) | What makes a site work on phones, and what to test |
| [Headless with the REST API](headless-with-the-rest-api.md) | Using Exponential as the content back end of an app or front end |
| [REST API v1 and v2 for businesses](rest-api-v1-and-v2-for-businesses.md) | What the two API versions do, what changed, and what to check on an installation |
| [Apps and API keys](apps-and-api-keys.md) | Which credential for which program, and how keys are controlled and audited |
| [Remote services](remote-services.md) | expservices: the administration's functions as an HTTP API for portals and scripts |
| [Feeds and syndication](feeds-and-syndication.md) | Publishing feeds and podcasts, and sharing content between installations |
| [Selling through networks and partners](selling-through-networks-and-partners.md) | Reaching customers on partner sites, in apps and through networks |
| [Offline and repeat visits](offline-and-repeat-visits.md) | The browser navigation cache: faster repeat visits, its rules and its off switch |
| [App backend reliability](app-backend-reliability.md) | Keeping the API that your app depends on available |
| [Mobile checklist](mobile-checklist.md) | What to check before an app, a feed or a mobile site goes live |

## Suggested reading order

1. [Mobile strategy](mobile-strategy.md) to choose channels.
2. [Responsive sites](responsive-sites.md); a site that works on phones comes before an app.
3. If you plan an app or a separate front end: [Headless with the REST API](headless-with-the-rest-api.md),
   [Apps and API keys](apps-and-api-keys.md) and [App backend reliability](app-backend-reliability.md).
4. If you sell through others: [Feeds and syndication](feeds-and-syndication.md) and
   [Selling through networks and partners](selling-through-networks-and-partners.md).
5. The [Mobile checklist](mobile-checklist.md) before launch.

> **Note**
> The REST API answers JSON for programs. It does not by itself make a mobile app; the app is built by your developers
> or an agency. These pages help you brief them and judge the result.

## Neighboring sections

- [How businesses sell online](../how-businesses-sell-online.md)
- [Stores](../stores/README.md), for the shop your app or partners sell from
- [Reliability](../reliability/README.md) and [Security](../security/README.md)
- [Several brands on one installation](../multisite/README.md) and
  [Selling in several languages and markets](../multilingual/README.md)
- For developers: [App creators start here](../../creators/app-creators/app-creators-start-here.md)
- Public sector organizations: Governments start here

## Related pages

- [Headless Exponential](../../creators/app-creators/headless-exponential.md)
- [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)
- [Remote services and apps](../../../guides/remote-services-and-apps.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
