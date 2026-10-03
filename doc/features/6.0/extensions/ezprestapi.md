# ezprestapi: a REST content provider for remote updates

`ezprestapi` is a REST API provider (version 1 of the `ezp` API) for **updating Exponential content remotely**: other systems list, read, create and delete
nodes over HTTP with an OAuth token. It is meant for developers who publish content from scripts and other services, and it is a registered provider of the
kernel's REST framework.

## Routes (version 1)

Registered by `ezp7xRestApiProvider` (`classes/rest_provider.php`):

| Route | Controller | Purpose |
|---|---|---|
| `GET /content/node/:nodeId` | `ezp7xRestContentController` | One node |
| `GET /content/node/:nodeId/list...` (a regular expression route) | `ezp7xRestContentController` | List children (offset, limit) |
| `POST /content/node/create` | `ezp7xRestContentController` | Create content |
| `DELETE /content/node/delete/:nodeId` | `ezp7xRestContentController` | Delete a node |
| `GET /content/node/:nodeId/listAtom` | `ezpRestAtomController` | Atom feed |
| `GET /content/node/:nodeId/fields`, `.../field/:fieldIdentifier`, `.../childrenCount` | `ezpRestContentController` | Fields and counts |
| `GET /content/object/:objectId`, `.../fields`, `.../field/:fieldIdentifier` | `ezpRestContentController` | The same by object id |

They are served below the REST prefix of the installation and carry a version: the provider is registered under the prefixes `ezp` and `ezpl`, and
the routes answer for example at `/api/ezp/v1/content/node/<id>/list/offset/0/limit/20` and `/api/ezpl/v2/content/node/create`. The provider classes are
named in `rest.ini`:

```ini
[ApiProvider]
ProviderClass[ezp]=ezp7xRestApiProvider
ProviderClass[ezpl]=ezp7xRestApiProvider
```

The create, update and delete calls are backed by [nxc_powercontent](nxc_powercontent.md). Authentication is the REST framework's OAuth2: obtain a token
(for example with a password grant) and send it as the `oauth_token` parameter.

## Call it from the command line

`bin/php/ezrestcall.php` (1.2.0) is an example client built on Guzzle (not part of the distribution: `composer require guzzlehttp/guzzle` in the
installation that runs it). It speaks HTTPS to a host:

```bash
# list the children of a node (the script appends /limit/<n> and the token)
php bin/php/ezrestcall.php --host=www.example.com --token=<oauth token> --action=list --uri=/api/ezpl/v2/content/node/2/list
```

`--host` is a host name without protocol, `--token` an OAuth token, `--action` is `list` (the default), `login` or `create`, and `--uri` the path to
call. The `login` and `create` actions are examples to read and adapt (`create` posts a story to `/api/ezpl/v2/content/node/create` with
`parentNodeID`, `classIdentifier`, `languageLocale` and the attribute values): the values in them belong to the author's own site, so copy the script
and replace them before use. Do not keep real credentials or tokens in a script or in shell history on shared machines.

## What changed

* 1.2.0 (29 October 2024): initial import of the solution's dependencies, funding information and the example client.
* 1.2.1 to 1.2.4 (27 September to 2 October 2026): version, license and website, description, 7x and Exponential Foundation headers on the entry point
  files, commands and cronjob parts are classes the files call and start through the shared helpers.

[ezprestapiprovider](ezprestapiprovider.md) is the sibling package with the provider classes alone.

## Related

* [nxc_powercontent](nxc_powercontent.md)
* [Chronicle](../../../history/extensions/ezprestapi.md) and [release notes](../../../changelogs/extensions/ezprestapi.md)
