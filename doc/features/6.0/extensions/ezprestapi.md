# ezprestapi: a REST content provider for remote updates

This page is for developers who publish content into Exponential from scripts and other services. `ezprestapi` is a
REST API provider (versions 1 and 2 of the `ezp` API) for **updating content remotely**: other systems list, read, create and
delete nodes over HTTP with an OAuth token. It is a registered provider of the kernel's REST framework.

## How it is wired

The provider classes are named in `rest.ini`:

```ini
[ApiProvider]
ProviderClass[ezp]=ezp7xRestApiProvider
ProviderClass[ezpl]=ezp7xRestApiProvider
```

The routes are served below the REST prefix of the installation and carry a version. Reads answer at `v1` and `v2`,
writes at `v2` only. With the prefixes `ezp` and `ezpl` they answer, for example, at
`/api/ezp/v1/content/node/<id>/list/offset/0/limit/20` and `/api/ezpl/v2/content/node/create`.

`ProviderClass[ezp]` of this extension replaces the one of [ezprestapiprovider](ezprestapiprovider.md), which served
`v1`. Before 1.2.5 that took `v1` away (every `/api/ezp/v1/...` answered 404) although existing clients, a mobile app
among them, read through it; since 1.2.5 this provider serves the `v1` reads itself. That needs the kernel of
Exponential 6.0.15, whose versioned routes take a list of versions; on an older kernel the routes are `v2` only, as
before.

The create, update and delete calls are backed by [nxc_powercontent](nxc_powercontent.md). Authentication is the REST
framework's OAuth2: obtain a token (for example with a password grant) and send it as the `oauth_token` parameter.
A user can also make a [personal API key](../../../guides/api-keys.md) on the site and send it as
`Authorization: Bearer expk_...`; the create, update and delete routes then need the key's `publish`, `edit` or
`remove` scope as well (`rest.ini [ApiKeySettings] RouteScopes[]`, `RouteGuards[]`).

## Permissions

Since 1.2.5 the controller checks the current user's policies before every call, whatever the request was
authenticated with, the way the content module checks them: `content/read` for reads (a list or count also of its
parent node), `content/create` of the class below `parentNodeID` in `languageLocale` (with every limitation),
`content/edit` for an update, `content/remove` of every location of the object and everything below them for a
removal. A refusal answers `403 {"error":"access_denied","error_message":...}` and writes nothing. Before 1.2.5 the
writes asked nothing: an OAuth token could create and remove where its user had no right to. The check is the kernel's
`expRestContentPermission` (Exponential 6.0.15); on an older kernel the extension refuses every write with 403
rather than run it unchecked. The details are in the guide:
[Permissions of the REST interface](../../../guides/api-keys.md#32-permissions-of-the-rest-interface).

| Answer | When |
|---|---|
| `201 {"message":"Created","objectId","nodeId"}` | a create was published |
| `200 {"message":"Removed","nodeId","objectId"}` | a removal is done |
| `400 invalid_request` | `parentNodeID`, `classIdentifier` or `languageLocale` is missing, or names a class or language that does not exist |
| `403 access_denied` | the user may not do it |
| `404 not_found` | the node does not exist |
| `501 not_implemented` | an allowed update: updating the attribute values is not implemented yet, nothing changes |
| `401` | no or an invalid token or key (a `POST` or `DELETE` too; before 6.0.15 they answered 405) |

## Routes

Registered by `ezp7xRestApiProvider` (`classes/rest_provider.php`); all but the Atom feed run in
`ezp7xRestContentController`, so they all get its checks:

| Route | Versions | Purpose |
|---|---|---|
| `GET /content/node/:nodeId` | v1, v2 | One node |
| `GET /content/node/:nodeId/list...` (a regular expression route) | v1, v2 | List children (offset, limit, sort) |
| `GET /content/node/:nodeId/fields`, `.../field/:fieldIdentifier`, `.../childrenCount` | v1, v2 | Fields and counts |
| `GET /content/object/:objectId`, `.../fields`, `.../field/:fieldIdentifier` | v1, v2 | The same by object id |
| `GET /content/node/:nodeId/listAtom` (`ezpRestAtomController`) | v1, v2 | Atom feed |
| `POST /content/node/create` | v2 | Create and publish content |
| `DELETE` or `POST /content/node/delete/:nodeId` | v2 | Remove the node's object |
| `POST /content/node/:nodeId` | v2 | Update (checked, then 501: not implemented yet) |

| Before 1.2.5 and 6.0.15 | Since |
|---|---|
| `/api/ezp/v1/...`: 404 for every route | the reads answer at v1 |
| `POST /api/ezp/v2/content/node/create` without or with a refused token: 405 | 401 or 403 |
| `DELETE /api/ezp/v2/content/node/delete/<id>`: 405 | runs (and `POST`) |
| a delete that worked answered 500 with "has been removed" | 200 with the ids |
| fields and object reads ran in `ezpRestContentController` of ezprestapiprovider | in this extension's controller |

## Call it from the command line

`bin/php/ezrestcall.php` (1.2.0) is an example client built on Guzzle. Guzzle is not part of the distribution: run
`composer require guzzlehttp/guzzle` in the installation that runs the script. It speaks HTTPS to a host:

```bash
# list the children of a node (the script appends /limit/<n> and the token)
php bin/php/ezrestcall.php --host=www.example.com --token=<oauth token> --action=list --uri=/api/ezpl/v2/content/node/2/list
```

| Option | Meaning |
|---|---|
| `--host` | Host name without protocol |
| `--token` | An OAuth token |
| `--action` | `list` (the default), `login` or `create` |
| `--uri` | The path to call |

The `login` and `create` actions are examples to read and adapt. `create` posts a story to
`/api/ezpl/v2/content/node/create` with `parentNodeID`, `classIdentifier`, `languageLocale` and the attribute values.
The values in them belong to the author's own site, so copy the script and replace them before use. Do not keep real
credentials or tokens in a script, or in shell history on shared machines.

## What changed

| Version | Date | Change |
|---|---|---|
| 1.2.0 | 29 October 2024 | Initial import of the solution's dependencies, funding information and the example client. |
| 1.2.1 to 1.2.4 | 27 September to 2 October 2026 | Version, license and website; description; 7x and Exponential Foundation headers on the entry point files; commands and cronjob parts are classes the files call and start through the shared helpers. |
| 1.2.5 | 6 October 2026 | Every read and write checks the current user's policies for every kind of authentication; reads answer at v1 again; delete takes `DELETE`; create and delete answer 201 and 200 with the ids. Needs Exponential 6.0.15 for the checks and v1. |

[ezprestapiprovider](ezprestapiprovider.md) is the sibling package with the provider classes alone.

## Related pages

- [nxc_powercontent](nxc_powercontent.md), [ezprestapiprovider](ezprestapiprovider.md)
- [Remote services (expservices)](../remote-services-expservices.md)
- [Personal API keys](../../../guides/api-keys.md): REST requests with a key of the user
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronicle](../../../history/extensions/ezprestapi.md) and [release notes](../../../changelogs/extensions/ezprestapi.md)
- [Change ledger](../../../history/ledger/ezprestapi.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-10](../../../history/extensions/months/2024-10.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
