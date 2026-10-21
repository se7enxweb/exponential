---
title: JSON endpoints for apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 300
summary: The landing page of the JSON endpoint pages for app developers - the ezjscore call interface, the expservices remote services an app is most likely to use, and when to ask for a server function of your own.
keywords: JSON, ezjscore, ezjscore/call, expservices, remote services, services, search, image alias, server function, envelope, mobile app
type: help
---
# JSON endpoints for apps

This folder is for app developers who need more than the REST content API: search, image sizes, URL lookups,
languages, updates. Those come from the remote services (`expservices`), served through ezjscore's call interface
below `/ezjscore/call/`. A screen that needs something no service gives can get a server function of its own.

## Contents

1. [The call interface](#the-call-interface)
2. [Services an app is likely to use](#services-an-app-is-likely-to-use)
3. [Pages in this folder](#pages-in-this-folder)

## The call interface

```text
GET|POST https://example.com/ezjscore/call/<name>::<function>[::<argument>...]?ContentType=json
```

ezjscore's router (`ezjscServerRouter`) refuses any name without an `[ezjscServer_<name>]` block in `ezjscore.ini`,
calls the registered PHP function and wraps the result as `{"error_text":"","content":<result>}`
([JSON endpoints with ezjscore](../../creators/app-creators/json-endpoints-with-ezjscore.md#how-a-call-works)).
`expservices` registers over a thousand services this way, named `exp<domain>::<method>`.

A quick check that the services are active, anonymously:

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

An envelope that says you are anonymous means they are; `Not a valid ezjscServerRouter argument` in `error_text`
means `expservices` is not active on that installation.

## Services an app is likely to use

| Need | Service | Access |
|---|---|---|
| Search | `expsearch::search` | `content/read` |
| URL of an image in a named size | `expimage::url`, `expimage::urls` | `content/read` |
| The configured image sizes | `expimage::aliases` | public |
| A node by its URL path (deep links) | `expnode::getByPath` | `content/read` |
| Descendants published since a date | `expnode::modifiedSince` | `content/read` |
| The site's languages in priority order | `explanguage::prioritized` | public |
| Update an item | `expnode::update` | `content/edit` |
| Who am I, making and revoking tokens | `expsession::whoami`, `tokenCreate`, `tokenRevoke` | signed in |

The access column is the service's own declaration in `extension/expservices/classes/`.

## Pages in this folder

| Page | Answers |
|---|---|
| [Calling expservices from an app](calling-expservices-from-an-app.md) | URLs, arguments, the envelope, paging, discovery, errors |
| [Searching content](searching-content.md) | `expsearch::search` with text, paging and filters |
| [Writing through expservices](writing-through-expservices.md) | create, update, remove; POST and tokens |
| [ezjscore server functions for your app](ezjscore-server-functions-for-your-app.md) | asking for an endpoint of your own, and calling it |

## Related pages

- [Remote services and apps](../../../guides/remote-services-and-apps.md)
- [Remote services with expservices](../../creators/app-creators/remote-services-expservices.md)
- [Backend services over ezjscore (reference)](../../../bc/6.0/backend_ezjscore_services.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
