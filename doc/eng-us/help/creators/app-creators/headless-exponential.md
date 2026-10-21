---
title: Headless Exponential
audience: app-creator, developer, project-manager
category: app-creation
order: 370
summary: Use Exponential as a content back end for apps and JavaScript front ends - the REST API, expservices, rendered XHTML, images, URLs, preview, caching, permissions, and what stays easier with the built-in templates.
keywords: headless, decoupled, API, REST, JSON, front end, mobile app, single page application, expservices, images, preview, caching, CORS, app creator
type: help
---
# Headless Exponential

This page is for application creators who want Exponential's content model, editing and permissions without its
templates: a mobile app, a single-page application, a static site generator, another system that shows the content.

## Contents

1. [What you keep and what you replace](#what-you-keep-and-what-you-replace)
2. [Choosing the API](#choosing-the-api)
3. [Reading content](#reading-content)
4. [Rich text, images and links](#rich-text-images-and-links)
5. [Preview and drafts](#preview-and-drafts)
6. [Caching and performance](#caching-and-performance)
7. [Hybrid: templates and API together](#hybrid-templates-and-api-together)

## What you keep and what you replace

| Kept | Replaced by your front end |
|---|---|
| content classes, editing, versions, translations, workflows | page templates, menus, the pagelayout |
| roles and policies (checked on every API call) | routing, URL handling |
| the administration and the layouts editor for editors | rendering of layouts |
| search, media storage, image aliases | client-side state and caching |

## Choosing the API

| API | For |
|---|---|
| REST v1/v2 (`ezprestapi`) | reading nodes, children and fields; creating and removing content; OAuth for apps ([REST API](rest-api.md)) |
| expservices | the administration's functions as services, with discovery and tokens ([Remote services](remote-services-expservices.md)) |
| ezjscore server functions | small JSON endpoints you write yourself ([JSON endpoints](json-endpoints-with-ezjscore.md)) |

A front end that runs in the browser on another host calls the REST API only after CORS is switched on for its
origin in `rest.ini [CorsSettings]`; it is off by default ([CORS for the REST API](../../../bc/6.0/rest-cors.md)).
A browser or mobile app that signs users in is a public OAuth client and uses PKCE
([PKCE and state](../../../bc/6.0/oauth-pkce.md)).

## Reading content

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Metadata,Fields"
```

Public content can be served to anonymous API users only if `rest.ini [Authentication] RequireAuthentication` allows
it; the default requires a user. For public front ends, use a read-only integration user and keep its key on your server.

## Rich text, images and links

- `OutputFormat=xhtml` gives rendered XHTML of rich text fields (`renderedOutput`), so the front end does not parse the
  internal XML.
- Images: fields carry the original and the aliases; define aliases for your front end's sizes
  ([Images and image variations](../website-creators/images-and-image-variations.md)).
- Links inside rich text point to site addresses; map them to your front end's routes by URL alias or node ID.

## Preview and drafts

Editors preview in the administration with the site's templates. A headless front end needs its own preview: read the
draft version through an authenticated, editor-level call and render it in a protected preview route. Plan this early;
it is the part teams forget.

## Caching and performance

- The REST framework has an answer cache (`rest.ini`); the `rest` caches are cleared with the content tag.
- Cache in your front end or CDN; purge on publish with a workflow event or a listener that calls your purge hook
  ([Workflows, triggers and events](workflows-and-events.md)).
- Fetch lists in pages with limits.

## Hybrid: templates and API together

Many projects keep the site in templates and add an app on the API. That keeps preview, layouts and SEO simple for the
site, and gives the app the same content. Start hybrid unless there is a reason not to.

## Related pages

- [REST API v1 and v2 for applications](rest-api.md)
- [OAuth and API keys](oauth-and-api-keys.md)
- [APIs and protocols standard](../../../standards/apis-and-protocols.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
