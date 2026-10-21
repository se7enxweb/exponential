---
title: Open data, RSS and REST
audience: non-profit, government, website-creator, app-creator
category: business-solutions
order: 60
summary: How a non-profit shares its publications and registers in machine-readable form from Exponential - RSS 2.0 and Atom feeds of news and publications, CSV, JSON and XML exports of content kept as registers, the REST API (ezprestapi, v1 and v2) with authentication by default and what to decide before opening it to anonymous readers, and licensing what you share.
keywords: open data, RSS, Atom, feeds, export, CSV, JSON, XML, xrowextract, REST API, ezprestapi, OAuth2, API keys, RequireAuthentication, anonymous, license, re-use, funders, networks, researchers
type: help
---
# Open data, RSS and REST

This page is for the website creator and the communications or data lead. Funders, umbrella organizations, partner
networks and researchers sometimes want your publications, events or grant registers in a form a computer can read.
Exponential offers three channels: feeds, exports and an API.

## Contents

1. [Which channel for which need](#which-channel-for-which-need)
2. [RSS and Atom feeds](#rss-and-atom-feeds)
3. [Exports](#exports)
4. [The REST API](#the-rest-api)
5. [Licensing what you share](#licensing-what-you-share)
6. [Privacy first](#privacy-first)
7. [Checklist](#checklist)

## Which channel for which need

| Need | Channel | Effort |
|---|---|---|
| Supporters and networks follow your news and publications | RSS or Atom feed | Low |
| A funder wants a list of your grants or projects once a year | Export to CSV | Low |
| A partner site shows your events automatically | Feed, or the REST API | Medium |
| A researcher wants structured data regularly | Export or the REST API | Medium |

## RSS and Atom feeds

RSS exports publish RSS 2.0, Atom, OPML or podcast feeds of your content; link them from the page layout so browsers
and feed readers find them ([RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md),
[RSS feeds guide](../../../guides/rss-feeds.md)).

Good feeds for a non-profit:

| Feed | Source folder |
|---|---|
| News | The news folder |
| Publications | The accountability folder (new annual publications, policies) |
| Events | The event calendar ([Events and calendars](../communications/events-and-calendars-for-non-profits.md)) |
| Podcast | A folder of audio files ([RSS and podcast feeds](../../customers/growth/rss-and-podcast-feeds.md)) |

Feed for new publications in detail: [Feeds for new and changed datasets](../../government/open-data/feeds-for-new-and-changed-datasets.md).

## Exports

| Export | What | Source |
|---|---|---|
| `xrowextract` Export tab | Content objects of a class to CSV and other formats | [Exporting content as CSV, JSON, XML](../../government/open-data/exporting-content-as-csv-json-xml.md) |
| `exp:ezcsvexport` | The kernel's CSV export command | same page |
| Collected information CSV | Form submissions (personal data; never publish) | [Collected information](../../../guides/collected-information.md) |

A register kept as content, for example "Grants we gave", exported once a year and published as a file, is often all
a funder needs ([Financial documents](financial-documents.md#three-ways-to-publish)).

## The REST API

The `ezprestapi` extension serves REST v1 and v2 ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)):

- Reads at v1 and v2, writes only at v2; answers are JSON.
- Calls such as `GET /content/node/<id>` and `GET /content/node/<id>/list/offset/<n>/limit/<n>`.
- **Authentication is required by default** (`rest.ini [Authentication] RequireAuthentication=enabled`): an OAuth2
  token or a personal API key. Every read checks the user's `content/read` policy.

Opening it to anonymous readers is a security decision. Before you do, check that Anonymous reads only public
sections, write routes stay closed, there is a rate limit in front of it, and record who approved it
([The REST API as a data interface](../../government/open-data/the-rest-api-as-a-data-interface.md#opening-it-to-anonymous-readers)).

For most non-profits, a partner gets an API key for their integration instead; nothing is opened to everyone
([API keys](../../../guides/api-keys.md), [REST API for owners](../../customers/growth/rest-api-for-owners.md)).

## Licensing what you share

Say under which terms others may re-use what you publish: on the page, in the feed description, and in the file.
See [Licensing datasets](../../government/open-data/licensing-datasets.md). Choose a license with your board; this
help does not recommend one.

## Privacy first

- Never put personal data in a feed, export or public API answer: no donor names, no volunteer contacts, no
  beneficiaries ([Privacy checks before publishing data](../../government/open-data/privacy-checks-before-publishing-data.md)).
- Check what a feed contains after you set it up: open it as a visitor would.
- The audit trail records CSV exports with their row count ([The audit trail](../../../features/6.0/audit-trail.md)).

## Checklist

- [ ] Feeds for news, publications and events, linked from the page layout.
- [ ] Each feed opened as a visitor and checked for personal data.
- [ ] Registers exported and published as files where funders ask.
- [ ] REST API left authenticated; keys issued per partner, if needed.
- [ ] Re-use terms stated.

## Related pages

- [Transparency for non-profits](README.md)
- [Open data in Exponential at a glance](../../government/open-data/open-data-in-exponential-at-a-glance.md)
- [Remote services and apps](../../../guides/remote-services-and-apps.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
