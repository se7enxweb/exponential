---
title: REST API v1 and v2 for applications
audience: app-creator, developer
category: app-creation
order: 330
summary: Use and extend the REST content API - the ezprestapi routes at v1 and v2, authentication with OAuth2 or personal API keys, permissions, curl examples, configuration in rest.ini, and adding your own REST provider.
keywords: REST, API, ezprestapi, v1, v2, OAuth2, API keys, Bearer, routes, content node, create, delete, rest.ini, REST provider, app creator
type: help
---
# REST API v1 and v2 for applications

This page is for application creators who read or write content from another program: a mobile app, a front end, an
integration. The REST content API comes from the `ezprestapi` extension; reads answer at v1 and v2, writes at v2 only
([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).

## Contents

1. [Routes](#routes)
2. [Authentication](#authentication)
3. [Examples](#examples)
4. [Permissions](#permissions)
5. [Configuration](#configuration)
6. [Your own REST provider](#your-own-rest-provider)

## Routes

Relative to `/api/ezp/<version>`:

| Method | Path | v1 | v2 | Does |
|---|---|---|---|---|
| `GET` | `/content/node/<id>` | yes | yes | one node (`ResponseGroups=Metadata,Locations,Fields`, `OutputFormat=xhtml`) |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)` | yes | yes | the children |
| `GET` | `/content/node/<id>/childrenCount` | yes | yes | the number of children |
| `GET` | `/content/node/<id>/fields`, `/field/<identifier>` | yes | yes | fields |
| `GET` | `/content/object/<id>`, `.../fields`, `.../field/<identifier>` | yes | yes | by object id |
| `POST` | `/content/node/create` | no | yes | create and publish |
| `DELETE` | `/content/node/delete/<id>` | no | yes | remove |

## Authentication

| Credential | For |
|---|---|
| OAuth2 token (`POST /api/auth/v1/oauth/token`, grants `authorization_code` and `refresh_token`) | an application that acts for its users; register it in **Setup > oAuth admin** |
| Personal API key (`Authorization: Bearer expk_...`) | a user's own script ([Personal API keys](../../../guides/api-keys.md)) |

`rest.ini [Authentication] RequireAuthentication=enabled` is the default ([OAuth and API keys](oauth-and-api-keys.md)).

## Examples

```bash
export EXP_API_KEY='expk_...'
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v1/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Fields"
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=<parent node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
```

Keep keys in environment variables or a secret store, never in code.

## Permissions

Every read and write checks the user's policies (`content/read`, `content/create`, `content/edit`, `content/remove`),
whatever the credential, through the kernel's `expRestContentPermission`. Answers: `200`, `201` for a create, `400`,
`401` without valid credentials, `403` when the user may not, `404` for an unknown node.

## Configuration

`settings/rest.ini` holds the REST framework (prefix, routes, versions, authentication, the answer cache, output);
`[OAuthSettings] TokenTTL` the token lifetime; `[ApiKeySettings]` the API keys. Under Apache with FastCGI, make sure the
`Authorization` header reaches PHP.

## Your own REST provider

An extension can add routes: a provider class registered for a prefix, with a route table and controllers, the way
`ezprestapi` registers `ezp7xRestApiProvider` for `ezp` and `ezpl`. The registration points (REST provider, route
filter, request, result and response filters) are listed with their settings in
[Extension points](../../../bc/6.0/rad-extension-points.md#rest-provider). Check permissions in every controller action;
the framework authenticates, it does not authorize.

**Testing against a public demo.** A demo runs in demo mode: password changes and resets for administrator accounts answer HTTP 403 ("This installation is a public demo: ..."); everything else works as on any site. [Demo mode FAQ](../../demo-mode-faq.md).

## Related pages

- [OAuth and API keys](oauth-and-api-keys.md)
- [Headless Exponential](headless-exponential.md)
- [APIs and protocols standard](../../../standards/apis-and-protocols.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
