---
title: REST and remote services from PHP
audience: php-developer, developer
category: php-development
order: 320
summary: The HTTP APIs of Exponential from the PHP side - the REST kernel behind index_rest.php, rest.ini, providers implementing ezpRestProviderInterface with routes and ezpRestMvcController controllers, authentication with OAuth2 and API keys, permission checks in every action, and how REST, ezjscore and expservices differ.
keywords: REST, index_rest.php, rest.ini, ezpKernelRest, ezpRestProviderInterface, getRoutes, getViewController, ezpRestMvcController, ProviderClass, ApiProvider, route filter, OAuth2, API keys, expRestContentPermission, ezprestapi, expservices, ezjscore
type: help
---
# REST and remote services from PHP

This page is for PHP developers who serve data to other programs: mobile apps, other front ends, integrations. Exponential
has three HTTP layers for that. This page explains how they are built in PHP and how to add to the REST one; the
consumer view is [REST API v1 and v2 for applications](../creators/app-creators/rest-api.md).

## Contents

1. [Three layers](#three-layers)
2. [The REST kernel](#the-rest-kernel)
3. [Adding a provider](#adding-a-provider)
4. [Controllers](#controllers)
5. [Authentication](#authentication)
6. [Authorization is your job](#authorization-is-your-job)
7. [Choosing a layer](#choosing-a-layer)

## Three layers

| Layer | Entry | Built for |
|---|---|---|
| REST | `index_rest.php`, under `/api/` (`rest.ini [System] ApiPrefix=/api`) | content and resources for other programs, OAuth2 for third parties |
| expservices | `ezjscore/call/exp<domain>::<function>` | administration features from scripts and apps, with discovery and API tokens ([Backend services over ezjscore](../../bc/6.0/backend_ezjscore_services.md)) |
| ezjscore server functions | `ezjscore/call/<name>::<function>` | your own pages' JavaScript ([ezjscore JSON endpoints in PHP](ezjscore-json-endpoints-in-php.md)) |

## The REST kernel

`index_rest.php` runs the REST kernel in `kernel/private/rest/` (`ezpkernelrest.php`, the router, the MVC configuration,
renderers, OAuth). It is built on the Zeta Components MVC tools (`zetacomponents/mvc-tools`), so a provider supplies
routes and controllers in that framework's shape. Settings are in `settings/rest.ini`:

| Block | Holds |
|---|---|
| `[System]` | `ApiPrefix`, the prefix filter class |
| `[ApiProvider]` | `ProviderClass[<prefix>]=<class>`, one entry per provider (`auth` is `ezpRestAuthProvider`) |
| `[RouteSettings]` | the route filter (`RouteSettingImpl`) and routes that skip authentication |
| `[Authentication]` | whether requests must authenticate (`RequireAuthentication=enabled` by default) |
| `[OAuthSettings]`, `[ApiKeySettings]` | token lifetime and API keys |

The content API (`/api/ezp/v1/...`, `/api/ezp/v2/...`) comes from the `ezprestapi` extension, whose provider is
registered the same way.

## Adding a provider

A provider implements `ezpRestProviderInterface` (`kernel/private/rest/classes/interfaces/rest_provider.php`):

| Method | Returns |
|---|---|
| `getRoutes()` | the provider's routes, mapping URL patterns and HTTP methods to controller actions |
| `getViewController()` | the view controller that picks a renderer for the result |

Register it for a prefix in your extension's `rest.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ApiProvider]
ProviderClass[acme]=acmeRestProvider
*/ ?>
```

**Setup > RAD** has a tool that writes the skeleton (`/setup/handlerextension/restprovider`); route filters are a
separate extension point (`[RouteSettings] RouteSettingImpl`)
([Extension points: REST provider](../../bc/6.0/rad-extension-points.md#rest-provider)). Read the shipped providers
in `kernel/private/rest/` and in `ezprestapi` before writing your own: their route tables are the best reference for
the exact format.

## Controllers

Controllers extend `ezpRestMvcController` (`kernel/private/rest/classes/controllers/rest_controller.php`), which extends
the Zeta Components `ezcMvcController`. Each action reads its route variables and request, does the work through your
service classes, and returns a result that the view controller renders as JSON. Keep actions thin, as for module views:
the same service should be callable from a command and a test.

## Authentication

| Credential | For |
|---|---|
| OAuth2 access token (`POST /api/auth/v1/oauth/token`, grants `authorization_code` and `refresh_token`) | an application acting for its users; clients are registered in **Setup > oAuth admin** |
| personal API key (`Authorization: Bearer expk_...`) | a user's own script ([Personal API keys](../../guides/api-keys.md)) |

Behind Apache with FastCGI, make sure the `Authorization` header reaches PHP
([OAuth and API keys](../creators/app-creators/oauth-and-api-keys.md)).

## Authorization is your job

The REST framework **authenticates**: it tells you who the user is. It does not decide what the user may do. Every
controller action must check policies for what it reads or writes. The content API does this through the kernel's
`expRestContentPermission` (`content/read`, `content/create`, `content/edit`, `content/remove`) and answers `401` without
valid credentials, `403` when the user may not, `404` for an unknown node. Do the same in your actions, with
`eZUser::currentUser()->hasAccessTo()` and `canRead()`/`canEdit()` on content.

## Choosing a layer

| Need | Layer |
|---|---|
| a third party integrates with your content, with OAuth consent | REST |
| a mobile app reads and creates content | REST (content API) |
| a script automates administration tasks | expservices with an API token |
| your page's JavaScript needs a small answer | ezjscore server function |
| another Exponential installation syncs content | the `syndication` extension, when installed |

## Related pages

- [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)
- [Remote services with expservices](../creators/app-creators/remote-services-expservices.md)
- [Headless Exponential](../creators/app-creators/headless-exponential.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
