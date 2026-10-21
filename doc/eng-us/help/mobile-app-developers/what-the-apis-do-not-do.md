---
title: What the APIs do not do
audience: mobile-app-developer, app-creator, developer, project-manager
category: mobile-apps
order: 12
summary: The known gaps of Exponential's HTTP interfaces for app developers, each checked in the code - no SDK, no update or search route in REST, no image aliases or uploads in REST answers, no PKCE or public OAuth clients, no state parameter returned, no CORS headers, no push notifications, listAtom answering 500 - with the way around each one.
keywords: limitations, gaps, known issues, not implemented, 501, listAtom, PKCE, state, client secret, CORS, push notifications, SDK, upload, image alias, search, mobile app
type: help
---
# What the APIs do not do

This page is for app developers and the people who plan an app with them. Read it before you design screens and
estimate work: each gap below was checked in the code, and each has a way around it. Nothing here is a reason not to
build an app; it is a list of things to plan instead of discovering them in the last sprint.

## Contents

1. [The REST API](#the-rest-api)
2. [Authentication](#authentication)
3. [Browsers and web apps](#browsers-and-web-apps)
4. [Things outside the APIs](#things-outside-the-apis)
5. [How to report a gap](#how-to-report-a-gap)

## The REST API

| Gap | Evidence | Way around it |
|---|---|---|
| Updating content answers `501` | `doUpdateContentNode()` checks `content/edit`, then answers `not_implemented` | `expnode::update` of expservices ([Writing through expservices](json-endpoints/writing-through-expservices.md)) |
| No search route | the route table of `ezp7xRestApiProvider` has none | `expsearch::search` ([Searching content](json-endpoints/searching-content.md)) |
| Images come as the original file path only | `attributeOutputData()` cuts the `ezimage` string at `\|` | `expimage::url` for an alias ([Images in REST answers](media-and-images/images-in-rest-answers.md)) |
| Rich text comes as plain text | `ezxmltext` values pass through `strip_tags()` | `OutputFormat=xhtml` with a site template, or your own endpoint ([Rich text in apps](content-modeling/rich-text-in-apps.md)) |
| `listAtom` answers `500` at v1 and v2 | [Known issue: listAtom](../../guides/rest-api-v1-and-v2.md#8-known-issue-listatom) | the `list` route |
| No list of drafts or versions | only published content is read | expservices version services, as an editor ([Drafts, preview and publishing](publishing-workflow/drafts-preview-and-publishing.md)) |
| No filter by class in a list | the `list` route takes offset, limit and sort only | `expnode::byClass` or `expnode::children` with a filter |
| Sort order must follow paging in the URL | the route's regular expression: `list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | build URLs in that order ([Listing children and pagination](rest-api/listing-children-and-pagination.md)) |

## Authentication

| Gap | Evidence | Way around it |
|---|---|---|
| The token endpoint needs `client_secret` | `doHandleRequest()` requires `grant_type`, `client_id`, `client_secret` | keep the secret on a server of yours ([A back end for your app](auth/a-backend-for-your-app.md)) |
| Only two grants | `authorization_code` and `refresh_token` (`validateGrantType()`) | no password grant, no client credentials grant; use the code flow |
| No PKCE | the token endpoint reads no `code_verifier` | the back end holds the secret and does the exchange |
| `state` is not returned | the authorize view: "state, not implemented yet" | keep your own correlation value in the app or back end |
| Personal API keys are personal | made by one user at **My account > API access**, shown once | never ship one in an app ([Personal API keys for tools](auth/personal-api-keys-for-tools.md)) |

## Browsers and web apps

| Gap | Evidence | Way around it |
|---|---|---|
| No CORS headers | neither the REST framework, ezjscore nor expservices sends `Access-Control-*` headers | serve the web app from the same origin, or add the headers in the web server or a proxy ([Web apps, CORS and CSP](offline-and-performance/web-apps-cors-and-csp.md)) |
| The service worker is for site pages | `/index.js` caches only same-origin GET navigations without a session cookie | an app needs its own offline store ([Syncing and offline reading](offline-and-performance/syncing-and-offline-reading.md)) |

Native apps are not browsers and are not affected by CORS.

## Things outside the APIs

- **No mobile SDK.** Write a thin client of your own around the routes you use; the
  [expservices reference](../../bc/6.0/backend_ezjscore_services.md#clients) has starting points for Kotlin and Swift
  that its authors could not compile on their build machine, so compile them in your project first.
- **No push notifications.** Nothing in the APIs sends messages to devices. Content events on the server can call
  your push service through a workflow event or a listener written by an application creator
  ([Workflows, triggers and events](../creators/app-creators/workflows-and-events.md)).
- **No uploads.** Neither REST nor expservices documents a way to send an image or file from a device. expservices
  refuses remote writes to `ezimage`, `ezbinaryfile`, `ezmedia`, `ezuser`, `ezpassword` and `ezflowblock` fields
  ([Files and uploads](media-and-images/files-and-uploads.md)).
- **No change feed.** There is no "everything changed since" route for the whole tree; see
  [Syncing and offline reading](offline-and-performance/syncing-and-offline-reading.md) for what there is.

## How to report a gap

If a gap blocks your app, report it with the route, the request, the answer and what you expected. The help's
**Report an error** page and [When something goes wrong](../when-something-goes-wrong.md) say where. Until it is
closed, an application creator can add the route to your installation
([Your own REST provider](../creators/app-creators/rest-api.md#your-own-rest-provider)).

## Related pages

- [Which API for your app](which-api-for-your-app.md)
- [REST API v1 and v2 for businesses: known gaps](../businesses/mobile-and-headless/rest-api-v1-and-v2-for-businesses.md#known-gaps)
- [Mobile app developers FAQ](mobile-app-developers-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
