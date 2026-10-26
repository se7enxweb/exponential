---
title: The REST API for integrators
audience: integrator, developer
category: integration
order: 50
summary: What the REST content API of ezprestapi offers an integration - the routes at v1 and v2, reading lists page by page, creating and removing content, the status codes to handle, the answer cache and the gaps (no update, listAtom) - with curl and Python examples.
keywords: REST, API, ezprestapi, /api/ezp/v1, /api/ezp/v2, content/node, list, offset, limit, ResponseGroups, create, delete, 401, 403, 429, rest.ini, integration client
type: help
---
# The REST API for integrators

This page is for developers of systems that call Exponential over HTTP. The REST content API is served by the
`ezprestapi` extension (required as `~1.2.6` by the root `composer.json`) on the kernel's REST framework
(`kernel/private/rest/`). The complete reference is [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md); this page
picks out what an integration needs and adds the client-side advice.

## Contents

1. [What it can and cannot do](#what-it-can-and-cannot-do)
2. [The routes](#the-routes)
3. [Reading a large list page by page](#reading-a-large-list-page-by-page)
4. [Creating and removing](#creating-and-removing)
5. [Answers your client must handle](#answers-your-client-must-handle)
6. [A small client in Python](#a-small-client-in-python)
7. [Settings that change the behavior](#settings-that-change-the-behavior)

## What it can and cannot do

| Can | Cannot (as of `ezprestapi` 1.2.6) |
|---|---|
| Read a node or an object with metadata, locations and fields | Update content: `POST /content/node/<id>` checks `content/edit`, then answers `501` |
| List the children of a node, with offset, limit and sort | Search by text or filter by field (use `expservices` or a fetch in your own route) |
| Count children, read one field | Answer `listAtom` (answers `500`, a known issue; use the site's RSS export) |
| Create and publish an object below a parent (v2) | Describe itself in OpenAPI |
| Remove an object with all its locations (v2) | Move, hide or change sections |

Reads answer at `v1` and `v2`, writes at `v2` only. Links inside a v1 answer stay in v1.

## The routes

All paths are below `/api/ezp/v1` or `/api/ezp/v2` (the prefix `ezpl` is an alias of `ezp`).

| Method | Path | v1 | v2 |
|---|---|---|---|
| `GET` | `/content/node/<id>` | yes | yes |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | yes | yes |
| `GET` | `/content/node/<id>/childrenCount` | yes | yes |
| `GET` | `/content/node/<id>/fields`, `/content/node/<id>/field/<identifier>` | yes | yes |
| `GET` | `/content/object/<id>`, `.../fields`, `.../field/<identifier>` | yes | yes |
| `POST` | `/content/node/create` | no | yes |
| `DELETE` or `POST` | `/content/node/delete/<id>` | no | yes |

Query parameters of the reads: `ResponseGroups=Metadata,Locations,Fields` chooses the parts of the answer, and
`OutputFormat=xhtml` adds the rendered view in `renderedOutput`.

## Reading a large list page by page

Ask for the count first, then walk the list with a fixed page size. Sort by a stable key so that pages do not
shift while you read.

```bash
export EXP_API_KEY='expk_...'
BASE=https://example.com/api/ezp/v1

curl -s -H "Authorization: Bearer $EXP_API_KEY" "$BASE/content/node/<node id>/childrenCount"
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "$BASE/content/node/<node id>/list/offset/0/limit/50?ResponseGroups=Metadata,Fields"
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "$BASE/content/node/<node id>/list/offset/50/limit/50?ResponseGroups=Metadata,Fields"
```

Only the children of one node are listed. To read a deep tree, walk it level by level, or let a server-side export
write the whole subtree in one run ([Writing an export](writing-an-export-or-outbound-sync.md)).

## Creating and removing

```bash
# create and publish a folder below a parent (v2 only)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=<parent node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From the ERP'
# {"message":"Created","objectId":<object id>,"nodeId":<node id>}

# remove it with all its locations and everything below them (v2 only)
curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/delete/<node id>
```

The create fields are `parentNodeID`, `classIdentifier`, `languageLocale`, then the attribute identifiers of the
class. The create is checked as `content/create` of that class below that parent in that language, and the publish
workflow runs as it does in the administration. The create backend is `nxc_powercontent`.

> **Note**
> The REST create has no `remote_id` field (the controller reads `parentNodeID`, `classIdentifier` and
> `languageLocale` and treats every other field as an attribute). Reads do return the remote ids: the metadata of an
> answer carries `objectRemoteId`, and for a node `nodeRemoteId`. An integration that must find its objects again
> should store the returned `objectId` on its side, or create content from PHP where `remote_id` is supported
> ([Idempotent imports](idempotent-imports-keyed-by-remote-id.md)).

## Answers your client must handle

| Status | Meaning | Client action |
|---|---|---|
| `200`, `201` | Done | Continue |
| `400` | A field of a write is missing, or names an unknown class or language | Fix the request; do not retry unchanged |
| `401` `invalid_token` or `expired_token` | No, unknown, revoked or expired credential; or a proxy dropped the header | Renew the token or alert; do not retry in a loop |
| `403` `access_denied` | The user may not do this to this node | Fix the role, not the client |
| `403` `insufficient_scope` | The key lacks the route's scope, or its owner lost the policy | Give the key the scope, or use another key |
| `404` | Unknown node or object | Treat as "gone" in a sync |
| `405` | The route does not take this method (a write sent to v1) | Use v2 |
| `429` `rate_limited` with `Retry-After` | Over `RateLimitPerMinute` of the key | Wait the seconds given, then retry |
| `500`, `502`, `503` | Server or maintenance (`503` with `Retry-After` during maintenance mode) | Retry with backoff, then alert |

## A small client in Python

Only the standard library:

```python
import json, os, time, urllib.error, urllib.request

BASE = 'https://example.com/api/ezp/v1'
KEY = os.environ['EXP_API_KEY']

def get(path):
    for attempt in range(5):
        request = urllib.request.Request(BASE + path, headers={'Authorization': 'Bearer ' + KEY})
        try:
            with urllib.request.urlopen(request, timeout=30) as answer:
                return json.load(answer)
        except urllib.error.HTTPError as error:
            if error.code in (429, 502, 503):
                time.sleep(int(error.headers.get('Retry-After', 2 ** attempt)))
                continue
            raise
    raise RuntimeError('gave up on ' + path)

print(get('/content/node/2')['metadata']['objectName'])
```

## Settings that change the behavior

`settings/rest.ini`, overridden in `settings/override/rest.ini.append.php`:

| Setting | Default | Effect for an integration |
|---|---|---|
| `[System] ApiPrefix` | `/api` | The path the API answers below |
| `[Authentication] RequireAuthentication` | `enabled` | `disabled` runs every request as `DefaultUserID` or anonymous; policies still apply |
| `[Authentication] AuthenticationStyle` | `ezpRestOauthAuthenticationStyle` | OAuth, or `ezpRestBasicAuthStyle`; API keys work with both |
| `[Authentication] RequireHTTPS` | `disabled` | Refuse plain HTTP |
| `[OAuthSettings] TokenTTL` | `3600` | Lifetime of an OAuth token |
| `[OAuthSettings] RefreshTokenTTL` | `2592000` | Lifetime of a refresh token from the refresh that issued it; `0` = no expiry |
| `[OAuthSettings] AllowTokenInQuery` | `disabled` | `enabled` also takes `?oauth_token=` from the address |
| `[OAuthSettings] AllowTokenInBody` | `enabled` | Take `oauth_token` from a POST form body |
| `[CacheSettings] ApplicationCache` | | The answer cache: one answer per user, `GET` and `HEAD` only |

After a change, clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`), reload PHP-FPM
and restart Velocity if it serves the site.

## Related pages

- [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) and [Personal API keys](../../guides/api-keys.md)
- [ezprestapi](../../features/6.0/extensions/ezprestapi.md), [nxc_powercontent](../../features/6.0/extensions/nxc_powercontent.md)
- [The REST API for app creators](../creators/app-creators/rest-api.md)
- [Choosing credentials for an integration](choosing-credentials-for-an-integration.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
