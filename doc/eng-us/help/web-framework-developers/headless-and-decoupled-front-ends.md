---
title: Headless and decoupled front ends
audience: web-framework-developer, developer
category: web-frameworks
order: 350
summary: Using Exponential as the content back end of a front end written in another framework - Next.js, Nuxt, a Laravel or Django site, a mobile app - what stays in Exponential, which API to read, rich text and images, preview, caching and purging, and why many teams start hybrid.
keywords: headless, decoupled, API-first, front end, Next.js, Nuxt, SPA, mobile app, static site generator, REST, preview, images, rich text, renderedOutput, CDN, purge, hybrid, web framework developer
type: help
---
# Headless and decoupled front ends

This page is for developers who want Exponential's content model, editing and permissions behind a front end built
with another framework: a JavaScript application, a static site generator, a mobile app, or a site written in Laravel,
Django or Rails. Exponential is not headless-only: it has its own templates, so you choose how much of the front end to
replace ([Headless Exponential](../creators/app-creators/headless-exponential.md)).

## Contents

1. [What you keep and what you replace](#what-you-keep-and-what-you-replace)
2. [Reading content](#reading-content)
3. [Rich text, images and links](#rich-text-images-and-links)
4. [Preview](#preview)
5. [Caching and purging](#caching-and-purging)
6. [Hybrid first](#hybrid-first)

## What you keep and what you replace

| Kept in Exponential | Replaced by your front end |
|---|---|
| content classes, editing, versions, translations, workflows | page templates, menus, the pagelayout |
| roles and policies, checked on every API call | routing, URL handling |
| the administration and the layouts editor for editors | rendering of layouts |
| search, media storage, image aliases | client-side state and caching |

## Reading content

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Metadata,Fields"
```

The REST API requires a user by default (`rest.ini [Authentication] RequireAuthentication`). For a public front end, use
a read-only integration user with a role limited to what the site shows, and keep its key on your server, never in
browser code ([REST and JSON APIs](rest-and-json-apis.md)).

## Rich text, images and links

- `OutputFormat=xhtml` returns rendered XHTML of rich text fields, so the front end does not parse the internal XML.
- Image fields carry the original and the aliases; define aliases for your front end's sizes in `image.ini`
  ([Images and image variations](../creators/website-creators/images-and-image-variations.md)).
- Links inside rich text point to site addresses; map them to your front end's routes by URL alias or node ID.

## Preview

Editors preview in the administration with the site's templates. A headless front end needs its own preview: read the
draft version through an authenticated, editor-level call and render it in a protected preview route. Plan it early.

## Caching and purging

- The REST framework has an answer cache configured in `rest.ini`.
- Cache in your front end or CDN; purge on publish with a workflow event or a `content/cache` listener that calls your
  front end's purge or revalidation hook ([Events and listeners](events-and-listeners.md)).
- Fetch lists in pages with limits.

## Hybrid first

Many projects keep the public site in Exponential's templates and add an app or a JavaScript widget on the API. That
keeps preview, layouts, SEO and URL aliases simple and gives the app the same content. Go fully headless when the front
end really must live elsewhere. If the goal is a Symfony front end with Twig on the same content, look at the legacy
bridge instead of the REST API ([Mixing Exponential and a framework](mixing-exponential-and-a-framework.md)).

## Related pages

- [Headless Exponential](../creators/app-creators/headless-exponential.md)
- [OAuth and API keys](../creators/app-creators/oauth-and-api-keys.md)
- [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
