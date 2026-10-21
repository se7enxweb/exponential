---
title: The REST content API for apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 100
summary: The landing page of the REST API pages for app developers - what the ezprestapi routes at v1 and v2 give an app, the base URL, the pages in this folder and the order to read them.
keywords: REST, API, ezprestapi, v1, v2, routes, JSON, mobile app, content node, list, fields, create, delete, errors
type: help
---
# The REST content API for apps

This folder is for app developers who read or write content through the REST content API of Exponential. The API
comes from the `ezprestapi` extension; one provider serves the prefixes `ezp` and `ezpl`, reads answer at v1 and
v2, writes at v2 only ([REST API v1 and v2: one extension](../../../guides/rest-api-v1-and-v2.md#in-short)).

## Contents

1. [The base URL](#the-base-url)
2. [What an app can do with it](#what-an-app-can-do-with-it)
3. [Pages in this folder](#pages-in-this-folder)
4. [Before you start](#before-you-start)

## The base URL

```text
https://example.com/api/ezp/v2/<route>
```

`/api` is `rest.ini [System] ApiPrefix`; `ezp` the provider prefix (`ezpl` is an alias with the same routes); `v1` or
`v2` the version. Every example in the guides calls it directly below the host, as above.

## What an app can do with it

| Task | Route | Versions |
|---|---|---|
| Read a node or object, with metadata, locations and fields | `GET /content/node/<id>`, `GET /content/object/<id>` | v1, v2 |
| List the children of a node, paged and sorted | `GET /content/node/<id>/list/...` | v1, v2 |
| Count the children | `GET /content/node/<id>/childrenCount` | v1, v2 |
| Read all fields or one field | `GET .../fields`, `GET .../field/<identifier>` | v1, v2 |
| Create and publish | `POST /content/node/create` | v2 |
| Remove | `DELETE /content/node/delete/<id>` | v2 |

## Pages in this folder

| Page | Answers |
|---|---|
| [Your first request](your-first-request.md) | How to make and check the first authenticated call |
| [Routes reference](routes-reference.md) | Every route with method, versions and controller action |
| [Reading a node](reading-a-node.md) | One node or object, its metadata and links |
| [Response groups](response-groups.md) | How to ask for locations and fields |
| [Listing children and pagination](listing-children-and-pagination.md) | Lists, paging, sorting and counts |
| [Fields and field values](fields-and-field-values.md) | The field object and how values are written per datatype |
| [Rendered XHTML output](rendered-xhtml-output.md) | `OutputFormat=xhtml` and the templates it needs |
| [Creating and removing content](creating-and-removing-content.md) | The two v2 writes and their answers |
| [Errors and status codes](errors-and-status-codes.md) | Every status your client must handle |
| [API versions v1 and v2](api-versions-v1-and-v2.md) | Which version to call and the deprecated provider |

## Before you start

- You need a credential: the API requires authentication by default ([Authentication](../auth/README.md)).
- You need an installation where `ezprestapi` is active: probe it first
  ([Probe before you build](../testing/testing-against-a-demo.md#probe-before-you-build)).
- The routes are the same for every client language; the examples use `curl` so they can be pasted into a shell.

## Related pages

- [REST API v1 and v2 for applications](../../creators/app-creators/rest-api.md)
- [REST API v1 and v2: one extension](../../../guides/rest-api-v1-and-v2.md)
- [ezprestapi](../../../features/6.0/extensions/ezprestapi.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
