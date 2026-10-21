---
title: Routes reference
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 120
summary: Every route of the ezprestapi content provider with its HTTP method, the versions it answers at, the controller action behind it and the scope a personal API key needs - plus the authentication route and the query parameters the routes understand.
keywords: routes, reference, ezp7xRestApiProvider, ezp7xRestContentController, content node, content object, list, childrenCount, fields, field, create, delete, update, listAtom, oauth token, ResponseGroups, Translation, OutputFormat, scope
type: help
---
# Routes reference

This page is for app developers who want the complete list of routes in one table. It is taken from the route table
of `ezp7xRestApiProvider::getRoutes()` in `extension/ezprestapi/classes/rest_provider.php` and from
`settings/rest.ini`. All content paths are relative to `/api/ezp/<version>` (or the alias prefix `/api/ezpl/<version>`).

## Contents

1. [Content routes](#content-routes)
2. [The authentication route](#the-authentication-route)
3. [Query parameters](#query-parameters)
4. [Scopes for personal API keys](#scopes-for-personal-api-keys)
5. [Method mismatches](#method-mismatches)

## Content routes

| Route name | Method | Path | v1 | v2 | Controller action |
|---|---|---|---|---|---|
| `ezpNode` | `GET` | `/content/node/<id>` | yes | yes | `viewContent` |
| `ezpNodeUpdate` | `POST` | `/content/node/<id>` | no | yes | `UpdateContentNode` (answers `501` after the permission check) |
| `ezpList` | `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | yes | yes | `list` |
| `ezpChildrenCount` | `GET` | `/content/node/<id>/childrenCount` | yes | yes | `countChildren` |
| `ezpFieldsByNode` | `GET` | `/content/node/<id>/fields` | yes | yes | `viewFields` |
| `ezpFieldByNode` | `GET` | `/content/node/<id>/field/<identifier>` | yes | yes | `viewField` |
| `ezpObject` | `GET` | `/content/object/<id>` | yes | yes | `viewContent` |
| `ezpFieldsByObject` | `GET` | `/content/object/<id>/fields` | yes | yes | `viewFields` |
| `ezpFieldByObject` | `GET` | `/content/object/<id>/field/<identifier>` | yes | yes | `viewField` |
| `ezpNodeCreate` | `POST` | `/content/node/create` | no | yes | `CreateContentNode` |
| `ezpNodeDelete` | `POST` or `DELETE` | `/content/node/delete/<id>` | no | yes | `DeleteContentNode` |
| `ezpListAtom` | `GET` | `/content/node/<id>/listAtom` | yes | yes | Atom feed; answers `500`, a [known issue](../../../guides/rest-api-v1-and-v2.md#8-known-issue-listatom) |

Every content action runs in `ezp7xRestContentController`, which checks the user's policies on each call.

## The authentication route

| Method | Path | Does |
|---|---|---|
| `POST` | `/api/auth/v1/oauth/token` | exchanges an authorization code or a refresh token for an access token |

The `auth` prefix belongs to the kernel's `ezpRestAuthProvider` (`rest.ini [ApiProvider] ProviderClass[auth]`). The
authorize page itself is a normal site page, `/oauth/authorize` ([OAuth2 authorization code flow](../auth/oauth2-authorization-code-flow.md)).

## Query parameters

The request parser of the REST framework (`kernel/private/rest/classes/request/http_parser.php`) takes three
parameters from the query string:

| Parameter | Used by | Values |
|---|---|---|
| `ResponseGroups` | node, object, field and list reads | a comma-separated list (`Metadata,Fields`) or `ResponseGroups[]=...` repeated ([Response groups](response-groups.md)) |
| `Translation` | node, object and field reads | a locale, for example `eng-GB` ([Multilingual content](../content-modeling/multilingual-content.md)) |
| `OutputFormat` | node and object reads | `xhtml` ([Rendered XHTML output](rendered-xhtml-output.md)) |

`Translation` and `OutputFormat` are carried into the `links` of the answer, so following a link keeps them.

## Scopes for personal API keys

A personal API key needs a scope for each route (`rest.ini [ApiKeySettings]`):

| Route | Scope |
|---|---|
| any `GET` or `HEAD` route not listed below | `read` (`DefaultReadScope`) |
| `ezp7xRestContentController_CreateContentNode` | `publish` |
| `ezp7xRestContentController_UpdateContentNode` | `edit` |
| `ezp7xRestContentController_DeleteContentNode` | `remove` |

Any other non-`GET` route is refused for keys until its scope is named
([Personal API keys: scopes](../../../guides/api-keys.md#6-scopes)). OAuth tokens have no such scopes; the user's
policies decide.

## Method mismatches

Since 6.0.15, a route whose path matches but which does not take the method no longer ends the search: the router
tries the next routes and answers `405` with an `Allow` header only when none takes the method. A refused write
answers `401` or `403`, never `405` ([Personal API keys, routes](../../../guides/api-keys.md#31-routes-of-the-ezp-provider)).

## Related pages

- [REST API v1 and v2: the routes](../../../guides/rest-api-v1-and-v2.md#2-the-routes)
- [Errors and status codes](errors-and-status-codes.md)
- [REST API v1 and v2 for applications](../../creators/app-creators/rest-api.md#routes)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
