---
title: Which API for your app
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 11
summary: Decide between the REST content API, the expservices remote services and a custom ezjscore server function for each screen of an app - by task, by credential, by answer shape and by what the server team has to do.
keywords: REST, expservices, ezjscore, choose, decision, comparison, mobile app, headless, search, image alias, update, credential, envelope
type: help
---
# Which API for your app

This page is for app developers and technical leads who have to decide, screen by screen, which of the three HTTP
interfaces of Exponential to call. It compares them on the things that matter for an app: what each one can do,
how it authenticates, how its answers look and what the server team must set up.

## Contents

1. [The short answer](#the-short-answer)
2. [By task](#by-task)
3. [By property](#by-property)
4. [Mixing interfaces](#mixing-interfaces)
5. [When nothing fits](#when-nothing-fits)

## The short answer

Read content with the **REST API** (`/api/ezp/v1` or `/api/ezp/v2`). Use the **remote services** (`expservices`) for
what REST does not offer: search, image alias URLs, URL aliases, languages, updating content. Ask a PHP developer for
an **ezjscore server function** only when neither has what a screen needs.

## By task

| Task | REST API | expservices | Read |
|---|---|---|---|
| Read one item with its fields | `GET /content/node/<id>?ResponseGroups=Fields` | `expnode::get`, `expnode::dataMap` | [Reading a node](rest-api/reading-a-node.md) |
| List a folder, paged | `GET /content/node/<id>/list/offset/<n>/limit/<n>` | `expnode::children` | [Listing children and pagination](rest-api/listing-children-and-pagination.md) |
| Count children | `GET /content/node/<id>/childrenCount` | `expnode::childrenCount` | same |
| Search | none | `expsearch::search` | [Searching content](json-endpoints/searching-content.md) |
| Image in a screen size | none (original path only) | `expimage::url`, `expimage::urls` | [Image aliases for screen sizes](media-and-images/image-aliases-for-screen-sizes.md) |
| Item by URL path (deep link) | none | `expnode::getByPath` | [Node ids, remote ids and deep links](content-modeling/node-ids-remote-ids-and-deep-links.md) |
| Create and publish | `POST /api/ezp/v2/content/node/create` | `expnode::create` | [Creating and removing content](rest-api/creating-and-removing-content.md) |
| Update | answers `501` after the permission check | `expnode::update` | [Writing through expservices](json-endpoints/writing-through-expservices.md) |
| Remove | `DELETE /api/ezp/v2/content/node/delete/<id>` | `expnode::remove` | [Creating and removing content](rest-api/creating-and-removing-content.md) |
| Languages of the site | none | `explanguage::prioritized`, `explanguage::languages` | [Multilingual content](content-modeling/multilingual-content.md) |

The expservices names come from the service declarations in `extension/expservices/classes/`; the full catalog is
in the [expservices reference](../../bc/6.0/backend_ezjscore_services.md).

## By property

| | REST API | expservices | Own ezjscore function |
|---|---|---|---|
| Ships with | `ezprestapi` (required by the 6.0.15 root `composer.json`) | `expservices`, part of the default distribution | your extension |
| URL | `/api/ezp/<version>/...` | `/ezjscore/call/exp<domain>::<method>[::arg...]` | `/ezjscore/call/<name>::<function>[::arg...]` |
| Credential | OAuth2 token or personal API key | expservices token, or a session cookie | whatever the function checks, usually the session |
| Anonymous calls | refused by default (`RequireAuthentication=enabled`) | allowed for services marked `public` | up to the function |
| Answer shape | plain JSON objects; errors with HTTP status | `{"error_text":"","content":{"ok":...}}`, errors inside with HTTP 200 | `{"error_text":"","content":<value>}` |
| Paging | `offset`, `limit` in the path; `childrenCount` in metadata | last two URL arguments; `meta.total`, `meta.has_more` | your own |
| Versioned | yes, v1 and v2 | no version in the URL | no |

The difference in error handling matters for your HTTP layer: REST says what went wrong with the status code, while
an expservices call that reaches the service answers HTTP 200 and puts the error in `content.ok` and
`content.error.code` ([The expservices envelope](json-endpoints/calling-expservices-from-an-app.md#the-envelope)).

## Mixing interfaces

Mixing is normal and safe: both run as a user and both check the same roles and policies. Keep two things apart in
the app's code:

- **Two credentials.** A REST OAuth token or `expk_` key is not an expservices token, and an `expt_` token is not
  accepted by REST. Store and refresh them separately ([Choosing a credential](auth/choosing-a-credential.md)).
- **Two error models.** Wrap each interface in its own client class that turns its answers into one error type of
  your app.

## When nothing fits

A screen that needs data no route or service gives (a combined dashboard, a custom calculation) gets its own server
endpoint, written in PHP in an extension: an ezjscore server function or a REST provider of your own. That is
application creator work: [JSON endpoints with ezjscore](../creators/app-creators/json-endpoints-with-ezjscore.md),
[REST API for applications](../creators/app-creators/rest-api.md#your-own-rest-provider). Brief them with the screen,
the fields you need and the user the call runs as.

## Related pages

- [What the APIs do not do](what-the-apis-do-not-do.md)
- [Headless Exponential](../creators/app-creators/headless-exponential.md#choosing-the-api)
- [JSON endpoints with ezjscore: ezjscore, expservices or REST?](../creators/app-creators/json-endpoints-with-ezjscore.md#ezjscore-expservices-or-rest)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
