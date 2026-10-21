---
title: Headless delivery
audience: cms-developer
category: cms-development
order: 30
summary: For CMS developers who deliver Exponential content to apps and front ends of other technologies. It sets out what exists, namely the REST interface, expservices, ezjscore server functions, JSON views of your own and feeds, and how to choose. It also covers the patterns for authentication, caching and rendered output, plus what Exponential 6 does not offer.
keywords: headless, decoupled, API, JSON, REST, expservices, ezjscore, server functions, mobile app, single page application, front end, feeds, JSON Feed, rendered output, OutputFormat, tokens, caching, GraphQL
type: help
---
# Headless delivery

This page is for developers who want content managed in Exponential and shown elsewhere: a mobile app, a single page
application, a kiosk, another website. Exponential 6 is a full CMS with its own templates, and it can serve content
as data next to its pages, or instead of them. This page lists the ways that exist and how to choose.

## Contents

1. [The options](#the-options)
2. [The REST interface](#the-rest-interface)
3. [expservices](#expservices)
4. [ezjscore server functions](#ezjscore-server-functions)
5. [A JSON view of your own](#a-json-view-of-your-own)
6. [Feeds](#feeds)
7. [Choosing](#choosing)
8. [Patterns that work](#patterns-that-work)
9. [What is not there](#what-is-not-there)

## The options

| Way | Address | Authentication | Best for |
|---|---|---|---|
| REST interface (`ezprestapi`) | `/api/ezp/v1/...`, `/api/ezp/v2/...` | personal API key, OAuth2 token | reading nodes, children and fields; creating and removing at v2 |
| `expservices` | `/ezjscore/call/exp<domain>::<method>` | session cookie, or a personal token (`expt_...`) | the administration's functions: content, users, shop, media, layouts and more |
| ezjscore server functions of your own | `/ezjscore/call/<class>::<function>` | the site's session and policies | small JSON calls for your own front end |
| A module view that answers JSON | `/<your module>/<view>/...` | the site's session and policies | a custom endpoint with its own shape |
| Feeds | RSS export, and feed output from `expservices` | public, or as the site allows | lists of content for readers and aggregators |

## The REST interface

The documented content API: nodes, children with offset, limit and sort, fields, objects; writes at v2. At v2
`OutputFormat=xhtml` adds the rendered output of the node's view, so a client can show rich text as HTML drawn by
the site's templates. Details, authentication and limits: [The REST API](the-rest-api.md).

## expservices

The `expservices` extension, shipped in the kernel's `extension/` directory, offers the administration's functions as
named HTTP services with a JSON envelope (`{"ok":true,"data":...,"meta":{...}}`), paged lists, a shell client and
Python examples. A signed-in user creates a personal token with `expsession::tokenCreate`; it is shown once and sent as
`Authorization: Bearer <token>` ([Remote services and apps](../../guides/remote-services-and-apps.md),
[Remote services: expservices](../../features/6.0/remote-services-expservices.md)). Discover what your installation
offers with the catalog service rather than from memory:

```bash
curl -s -H "Authorization: Bearer $EXP_SERVICES_TOKEN" \
  'https://www.example.com/ezjscore/call/expservices::catalog::system?ContentType=json'
```

## ezjscore server functions

For a front end of your own on the same site (a search-as-you-type box, a "load more" button), an ezjscore server
function is a static PHP function registered in `ezjscore.ini`, called at `/ezjscore/call/...` and answered as JSON
with the site's session and policies ([Ajax with ezjscore server functions](../extension-development/part-3-extension-developer/ajax-with-ezjscore-server-functions.md),
[A server function by hand](../extension-development/part-3-extension-developer/tutorials/by-hand-ajax-server-function.md)).

## A JSON view of your own

When a client needs a shape no existing interface gives, a module view in your extension can answer with JSON instead
of a page (no pagelayout, a JSON body, the right content type) and declare a policy function for access
([Answering with JSON or a file](../extension-development/part-3-extension-developer/view-results-and-the-pagelayout.md#answering-with-json-or-a-file),
[Modules and views](../extension-development/part-3-extension-developer/modules-and-views.md)). Build the data with
fetch functions or the kernel API, so permissions apply ([Content from PHP](content-from-php.md)).

## Feeds

The kernel's RSS export publishes a subtree as a feed (**Setup** > **RSS**), and `expservices` can output RSS, Atom
or JSON Feed for any subtree ([RSS feeds](../../guides/rss-feeds.md)). Use feeds for news lists that other systems pull
on a schedule.

## Choosing

| Need | Choose |
|---|---|
| An app reads published content | REST v1 or v2 with a personal key of a read-only account |
| An app lets its users sign in and write as themselves | REST with OAuth2 tokens |
| A tool automates editorial or administrative tasks | `expservices` with a personal token |
| Your own site's JavaScript needs a little data | an ezjscore server function |
| A partner needs one specific JSON shape | a module view of your own |

## Patterns that work

- **One account per client**, with a role that allows exactly what the client needs, and keys that expire.
- **Ask for fields, not pages.** Use `ResponseGroups` and `field/<identifier>` routes to keep answers small.
- **Keep rich text rendering on the server** when the client should look like the site: v2 `OutputFormat=xhtml`.
- **HTTPS only**: `rest.ini [Authentication] RequireHTTPS=enabled` on any reachable installation.
- **Cache on the client** and avoid polling every few seconds; content changes are published, not streamed.

## What is not there

Exponential 6 has no GraphQL endpoint, and the REST interface has no update route yet. Projects that need the Symfony
platform's REST v2 and GraphQL use the Exponential Platform line, which can run next to this kernel through the
legacy bridge ([Coming from eZ Platform or Ibexa](coming-from-ez-platform-or-ibexa.md#two-exponential-destinations),
[Legacy bridge](../../features/6.0/legacy-bridge.md)).

## Related pages

- [The REST API](the-rest-api.md)
- [Application creators: start here](../creators/app-creators/app-creators-start-here.md)
- [API and protocol standards](../../standards/apis-and-protocols.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
