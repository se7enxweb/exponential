---
title: "FAQ: integrations and APIs"
audience: government, project-manager, developer, admin
category: public-sector
order: 120
summary: Answers to the integration questions public bodies ask about Exponential - the REST API, personal API keys, OAuth, RSS feeds, remote services, exports, single sign-on, maps and search - with what exists and what would be custom work.
keywords: integrations FAQ, API, REST API, API keys, OAuth, RSS, remote services, export, CSV, JSON, single sign-on, SSO, SAML, LDAP, maps, Solr, public sector
type: help
---
# FAQ: integrations and APIs

This page answers the integration questions public bodies ask about Exponential. It names what exists, with the page
that documents it, and says where something would be custom work.

## Contents

1. [Is there an API?](#is-there-an-api)
2. [How do scripts authenticate?](#how-do-scripts-authenticate)
3. [Can other sites reuse our content?](#can-other-sites-reuse-our-content)
4. [Can we drive the administration from other systems?](#can-we-drive-the-administration-from-other-systems)
5. [Can we export data in open formats?](#can-we-export-data-in-open-formats)
6. [Is there single sign-on?](#is-there-single-sign-on)
7. [Are there maps?](#are-there-maps)
8. [Can we use an external search engine?](#can-we-use-an-external-search-engine)

## Is there an API?

Yes, a REST content API in versions 1 and 2, both served by the `ezprestapi` extension
([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).

## How do scripts authenticate?

With OAuth, or with personal API keys: a signed-in user makes a key, a script sends it, and the request runs as that
user, limited to what the key was made for; keys can be rotated, revoked and expire
([Personal API keys](../../../guides/api-keys.md)).

## Can other sites reuse our content?

Yes, through RSS exports of any part of the tree ([RSS feeds](../../../guides/rss-feeds.md)) and the REST API. RSS
imports bring other sites' feeds in.

## Can we drive the administration from other systems?

Yes: remote services let scripts, apps and portals call Exponential as an HTTP API
([Remote services and apps](../../../guides/remote-services-and-apps.md)).

## Can we export data in open formats?

Yes: xrowextract exports content per class to CSV, XML or JSON with a manifest; form submissions to CSV; consent
records to CSV; whole databases with the engine's tools ([Migrating away from Exponential](../../../install/migration/leaving-exponential.md#1-what-to-export)).
PDF exports of parts of the tree exist too ([PDF exports](../../../guides/pdf-exports.md)).

## Is there single sign-on?

The documentation describes two-step sign-in and social login handlers in `sevenx_authentication_2fa`
([Two-step sign-in](../../../guides/two-factor-login.md), [Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
Single sign-on with your organization's identity provider (for example SAML) is not documented; treat it as custom
work and ask bidders how they would build and maintain it.

## Are there maps?

The `ezgmaplocation` extension offers a location datatype: the editor finds a point under a Google map, and the
coordinates and address are stored and searchable, with a filter to fetch objects by distance. It needs a Google Maps
key, so it uses a third-party service with its own privacy terms ([ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md)).
Exponential has no GIS features. Mapping beyond a location pin is custom work or a separate system.

## Can we use an external search engine?

Yes: the ezfind extension moves site search to Apache Solr, adding facets, relevance tuning and the content of files
([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)).

## Related pages

- [Government FAQ](README.md)
- [APIs and protocols standards](../../../standards/apis-and-protocols.md)
- [FAQ: search](search-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
