---
title: REST and JSON APIs
audience: web-framework-developer, developer
category: web-frameworks
order: 340
summary: The API layers of Exponential for developers who build APIs with API Platform, Laravel API resources, Django REST framework or Spring controllers - the REST content API at v1 and v2 with API keys and OAuth2, your own REST provider, ezjscore server functions for your pages' JavaScript, expservices for driving the administration, and plain JSON from a module view.
keywords: REST, API, JSON, ezprestapi, v1, v2, OAuth2, API keys, Bearer, REST provider, ezjscore, ezjscServer, expservices, JSON response, API Platform, Django REST framework, Laravel API resources, web framework developer
type: help
---
# REST and JSON APIs

This page is for developers who expose JSON from controllers or an API framework. Exponential has three ready API
layers and the option of plain JSON from a module view. Pick the layer by who calls it.

## Contents

1. [Which layer](#which-layer)
2. [The REST content API](#the-rest-content-api)
3. [Your own REST routes](#your-own-rest-routes)
4. [ezjscore server functions](#ezjscore-server-functions)
5. [expservices](#expservices)
6. [Plain JSON from a view](#plain-json-from-a-view)
7. [Compared with API frameworks](#compared-with-api-frameworks)

## Which layer

| Caller | Layer | Authentication |
|---|---|---|
| Other programs reading or writing content: apps, front ends, integrations | REST v1 and v2 (`ezprestapi`) | personal API keys, OAuth2 |
| Your own pages' JavaScript | ezjscore server functions | the session, form token for writes |
| Scripts and apps driving the administration's functions | expservices | API tokens |
| One small endpoint of your module | JSON from a module view | the session or your own check |

## The REST content API

Relative to `/api/ezp/<version>` ([REST API v1 and v2 for applications](../creators/app-creators/rest-api.md)):

| Method | Path | v1 | v2 |
|---|---|---|---|
| `GET` | `/content/node/<id>` | yes | yes |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)` | yes | yes |
| `GET` | `/content/object/<id>`, `.../fields`, `.../field/<identifier>` | yes | yes |
| `POST` | `/content/node/create` | no | yes |
| `DELETE` | `/content/node/delete/<id>` | no | yes |

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2
```

Every call checks the user's policies (`content/read`, `content/create`, `content/edit`, `content/remove`), whatever
the credential. `settings/rest.ini` configures the framework; `[Authentication] RequireAuthentication=enabled` is the
default. Under Apache with FastCGI, make sure the `Authorization` header reaches PHP
([REST API v1 and v2 guide](../../guides/rest-api-v1-and-v2.md), [OAuth and API keys](../creators/app-creators/oauth-and-api-keys.md)).

## Your own REST routes

An extension adds routes with a **REST provider** registered for a prefix, with a route table and controllers, the way
`ezprestapi` registers `ezp7xRestApiProvider` for `ezp` and `ezpl`. Route, request, result and response filters are
further extension points ([Extension points: REST provider](../../bc/6.0/rad-extension-points.md#rest-provider)). The
framework authenticates; your controller must authorize.

## ezjscore server functions

```ini
# extension/expdemo/settings/ezjscore.ini.append.php
<?php /* #?ini charset="utf-8"?
[ezjscServer_expdemo]
Class=expDemoServerFunctions
*/ ?>
```

`<root>/ezjscore/call/expdemo::count::42?ContentType=json` calls `expDemoServerFunctions::count( array( '42' ) )` and
wraps the return value as `{"error_text": "", "content": ...}`. `ezjscore/call` does not check a policy by itself:
every function checks for itself ([JSON endpoints with ezjscore](../creators/app-creators/json-endpoints-with-ezjscore.md)).

## expservices

`expservices` turns the administration's functions into over a thousand named services (content, users, roles,
caches, cronjobs, shop, feeds, media, tags, layouts, the audit and more), called through ezjscore with one answer
envelope, discovery and tokens ([Remote services with expservices](../creators/app-creators/remote-services-expservices.md)).

## Plain JSON from a view

```php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( array( 'ok' => true, 'data' => $data ) );
eZExecution::cleanExit();
```

Fine for one endpoint; for anything larger use a layer above ([Responses](responses-redirects-and-errors.md#json-and-other-formats)).

## Compared with API frameworks

| Feature | API Platform, DRF, Laravel API resources | Exponential |
|---|---|---|
| Resources from models | generated | the content API covers content; your own resources are routes of a REST provider |
| Serialization | serializers, resources | your controller builds arrays; REST returns fields, optionally rendered as XHTML |
| OpenAPI documentation | generated | not generated |
| GraphQL | available as packages | not in Exponential 6; the Symfony platform products have it ([The platform products](the-platform-products.md)) |
| Authentication | tokens, OAuth | API keys, OAuth2, expservices tokens |

## Related pages

- [Headless and decoupled front ends](headless-and-decoupled-front-ends.md)
- [APIs and protocols standard](../../standards/apis-and-protocols.md)
- [Remote services and apps](../../guides/remote-services-and-apps.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
