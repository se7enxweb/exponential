---
title: Remote services with expservices
audience: integrator, developer
category: integration
order: 80
summary: Using the expservices extension as an integration API - the JSON envelope, the catalog, tokens, reads and writes, an idempotent upsert by remote id over HTTP, content jobs, and adding a service domain of your own.
keywords: expservices, remote services, ezjscore call, expobject, getByRemoteId, create, update, expnode, expsearch, expcontentjob, catalog, expt token, X-Exp-Token, envelope, upsert, integration API
type: help
---
# Remote services with expservices

This page is for integrators who need more over HTTP than the REST content API offers: updating objects, finding an
object by its remote id, searching, running content jobs, reading users or the audit. The `expservices` extension
turns the administration's functions into named services (about 1100), answered as JSON by the `ezjscore` call
interface at `/ezjscore/call/exp<domain>::<method>`. The tutorial with the shipped clients is
[Remote services and apps](../../guides/remote-services-and-apps.md); the developer reference is
[expservices](../extension-development/part-8-reference/expservices.md).

## Contents

1. [Why integrators use it](#why-integrators-use-it)
2. [The envelope and the rules](#the-envelope-and-the-rules)
3. [Finding the service you need](#finding-the-service-you-need)
4. [An upsert by remote id over HTTP](#an-upsert-by-remote-id-over-http)
5. [Large operations as content jobs](#large-operations-as-content-jobs)
6. [Your own service domain](#your-own-service-domain)
7. [Settings](#settings)

## Why integrators use it

| Need | REST API (`ezprestapi`) | `expservices` |
|---|---|---|
| Read a node, its children, fields | yes | yes (`expnode::get`, `expnode::children`, `expobject::dataMap`) |
| Create and publish | yes (no `remote_id`) | yes, with `remote_id` (`expobject::create`) |
| Update an object with a new published version | no (`501`) | yes (`expobject::update`) |
| Find an object by remote id | no | yes (`expobject::getByRemoteId`) |
| Search | no | yes (`expsearch::search`, `count`, `facets`) |
| Move, hide, copy, state, section on a subtree | no | yes, as content jobs (`expcontentjob::*`) |
| Users, roles, caches, cronjobs, packages, audit | no | yes, each behind its policy |

## The envelope and the rules

Every answer has one shape inside the `ezjscore` wrapper:

```text
{"error_text":"","content":{"ok":true,"data":...,"meta":{...}}}
{"error_text":"","content":{"ok":false,"error":{"code":403,"message":"..."}}}
```

Lists are paged; `meta` carries `total`, `offset`, `limit`, `count` and `has_more` (`expservices.ini [Paging]`
`DefaultLimit=25`, `MaxLimit=200`). Error codes are HTTP-like: 400 arguments, 401 sign in, 403 denied, 404 not found,
409 conflict, 422 invalid.

| Rule | Detail |
|---|---|
| Arguments | Parts after the method, joined with `::` (`expnode::children::2::name::asc::5::0`); write fields as `POST` |
| Access | Each service declares `public`, `user` or a policy `[module, function]`; the same policies the administration checks |
| Writes | `POST`; with a session also the form token (`ezxform_token` field and `X-CSRF-Token` header); with an `expt_...` token no form token |
| Audit | Every write is recorded as `service.<domain>.<method>` (`expservices.ini [Writes] Audit=enabled`) |
| Credentials | Session cookie, or a token from `expsession::tokenCreate` sent as `Authorization: Bearer` or `X-Exp-Token` |

## Finding the service you need

```bash
export EXPSERVICES_URL=https://example.com
cd extension/expservices/bin
./expservices-client.sh catalog object | grep -E '"method"|"summary"'
./expservices-client.sh catalog search
```

Each catalog entry lists the summary, access, `write` flag, arguments and an example. In the administration the
same catalog is under **Setup > RAD** (`/setup/rad`).

## An upsert by remote id over HTTP

When the calling system cannot run code on the Exponential server but must keep one object per external record,
use the remote id. The shipped Python client (`extension/expservices/bin/expservices_client.py`, standard library
only) does the envelope and the token:

```python
import json, os, sys
sys.path.insert(0, "extension/expservices/bin")
from expservices_client import Client, ServiceError

c = Client("https://example.com", api_token=os.environ["EXPSERVICES_TOKEN"])

def upsert(remote_id, parent_node_id, class_identifier, attributes, language="eng-US"):
    try:
        found = c.call("expobject::getByRemoteId", remote_id)
    except ServiceError as e:
        if e.code != 404:
            raise
        found = None
    if found is None:
        return c.post("expobject::create", {
            "parent_node_id": parent_node_id, "class": class_identifier,
            "attributes": json.dumps(attributes), "language": language, "remote_id": remote_id})
    return c.post("expobject::update", {"attributes": json.dumps(attributes), "language": language},
                  args=(found["id"],))

upsert("erp-product-10442", 2, "folder", {"name": "Product 10442"})
```

`expobject::create` refuses a remote id that is already in use with `409`, so a second run creates no duplicate.
Do not run two writers for the same records at the same moment: the check and the create are two steps (on MySQL
and PostgreSQL a unique index on `ezcontentobject.remote_id` stops the second insert; other engines are not
covered by this page). Remote ids set through `expobject::setRemoteId` may contain letters, digits and `_ . : -`, up to
100 characters; use the same alphabet for ids you pass to `create`.

> **Note**
> Every `update` publishes a new version. Compare the incoming values with `expobject::dataMap` first and skip
> unchanged records, or the object's history fills with identical versions
> ([Idempotent imports](idempotent-imports-keyed-by-remote-id.md)).

## Large operations as content jobs

Moving, hiding, copying or removing a large subtree runs as a content job in batches. Over `expservices`:

```bash
./expservices-client.sh post expcontentjob::hide node_id=<node id>
./expservices-client.sh call expcontentjob::progress <job id>
```

`expcontentjob::estimate` says beforehand how many nodes an operation touches and whether it would run at once or as
a job ([Content jobs](../../bc/6.0/content-jobs.md)).

## Your own service domain

An integration that offers its own functions to other systems (for example "the stock level of a product from the
ERP") can add a domain: a class extending `expServiceBase` with its `$services` declarations, registered in your
extension's `settings/ezjscore.ini.append.php` as `[ezjscServer_<domain>]` with `Class=<your class>`. It then gets
the envelope, paging, access checks, the token, the audit and the catalog entry for free
([Writing a service](../extension-development/part-8-reference/expservices.md#writing-a-service)).

## Settings

| File | Block and key | Default | Meaning |
|---|---|---|---|
| `expservices.ini` | `[Services] Enabled` | `enabled` | `disabled`: every service answers 403 |
| `expservices.ini` | `[Paging] DefaultLimit`, `MaxLimit` | `25`, `200` | Page sizes |
| `expservices.ini` | `[Writes] RequireToken` | `enabled` | The form token on session writes; switch off for tests only |
| `expservices.ini` | `[Writes] Audit` | `enabled` | Record every write |

## Related pages

- [Remote services and apps](../../guides/remote-services-and-apps.md)
- [Backend services over ezjscore](../../bc/6.0/backend_ezjscore_services.md)
- [Remote services for app creators](../creators/app-creators/remote-services-expservices.md)
- [ezjscore server functions for integrations](ezjscore-server-functions-for-integrations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
