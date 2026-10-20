---
title: Maps and embedded content
audience: government, project-manager, editor, developer
category: public-sector
order: 240
summary: For public bodies showing locations, videos and other embedded services - what the ezgmaplocation extension does (a location datatype using Google Maps), the privacy effect of maps and embeds from other services, and two-click and self-hosted alternatives.
keywords: maps, Google Maps, ezgmaplocation, GMapsKey, location datatype, embeds, video, iframe, third party, privacy, consent, two-click, self-hosted, CSP frame-src, accessibility, public sector
type: help
---
# Maps and embedded content

This page is for project managers, editors and developers. Public sites show office locations, event venues and
explainer videos. Each map or video loaded from another service tells that service about the visitor. Exponential does
not include a mapping service of its own; the extension `ezgmaplocation` uses Google Maps
([ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [ezgmaplocation](#ezgmaplocation)
2. [Embeds in general](#embeds-in-general)
3. [Less invasive options](#less-invasive-options)
4. [Related pages](#related-pages)

## ezgmaplocation

- A datatype that stores latitude, longitude and an address on an object; editors find the point by typing an address
  under a Google map in the edit form.
- Needs a Google Maps key in `site.ini [SiteSettings] GMapsKey`.
- A fetch filter can sort objects by distance to a point.
- Privacy: editors' address lookups go to Google; if your templates show maps to visitors, their browsers load Google
  Maps too.

Only these features are documented; Exponential has no GIS functions.

## Embeds in general

Video platforms, social media posts, maps and forms of other services, embedded with an iframe or a script:

- the service receives the visitor's address and browser data, and often sets its own storage;
- consent is usually needed before loading (see "Cookie banners and consent");
- the Content Security Policy must allow the host (`frame-src`, `script-src`) on that siteaccess;
- accessibility depends on the service's player or widget.

## Less invasive options

| Need | Option |
|---|---|
| Show where an office is | a static map image you host, the address in text, and a link "open in a map service" |
| Show a video | host the file with the media datatype (HTML5 video) and captions in your design; or a two-click placeholder |
| Show posts from social media | a link to the account; or a screenshot with text |
| Directions | a link to the map service with the address; the visitor chooses to go there |

Text is always needed anyway: the address and directions in words serve screen reader users and search.

## Related pages

- [ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md)
- [Third-party extensions and privacy](third-party-extensions.md)
- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
