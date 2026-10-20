---
title: The REST API as a data interface
audience: government, developer, admin
category: public-sector
order: 110
summary: For developers and administrators who consider the Exponential REST API (v1 and v2, extension ezprestapi) as a machine interface to datasets: what it returns as JSON, how authentication and permissions work, and what to decide before opening it to the public.
keywords: REST API, JSON, API, ezprestapi, v1, v2, OAuth2, API key, RequireAuthentication, anonymous, data interface, DataService, open data
type: help
---
# The REST API as a data interface

This page is for developers and administrators who want machines to read dataset descriptions and file links
directly. Exponential serves a REST content API from one extension, `ezprestapi`, at `/api/ezp/v1/...` and
`/api/ezp/v2/...`. Everything below is from [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md) and
[Personal API keys](../../../guides/api-keys.md).

## Contents

1. [What it returns](#what-it-returns)
2. [Authentication by default](#authentication-by-default)
3. [Opening it to anonymous readers](#opening-it-to-anonymous-readers)
4. [Calls a data client makes](#calls-a-data-client-makes)
5. [What it is not](#what-it-is-not)
6. [Before you announce it](#before-you-announce-it)

## What it returns

| Call | Returns |
|---|---|
| `GET /content/node/<id>` | one node, with metadata, locations and fields on request (`ResponseGroups`) |
| `GET /content/node/<id>/list/offset/<n>/limit/<n>` | the children, paged |
| `GET /content/node/<id>/fields` | every field of the node's object |
| `GET /content/node/<id>/field/<identifier>` | one field, for example `license` |
| `GET /content/node/<id>/childrenCount` | the number of children |

Reads answer at v1 and v2; writes only at v2. Answers are JSON.

A catalog folder read with `list` gives the datasets; each dataset read with `fields` gives its metadata; each
distribution's file field gives the file information.

## Authentication by default

`rest.ini [Authentication] RequireAuthentication=enabled` is the default: every content route needs a user, either
with an OAuth2 token (registered at **Setup > oAuth admin**) or with a personal API key sent as
`Authorization: Bearer expk_...`. Every read checks the user's `content/read` policy, so a client never sees more
than that user may.

## Opening it to anonymous readers

`RequireAuthentication=disabled` lets every request run as `DefaultUserID`, or the anonymous user when that is empty;
the permission checks still apply, so anonymous clients see exactly what anonymous visitors of the site see. Before
you change it:

1. Make sure the Anonymous role reads only public sections ([Visitor permissions](../../creators/website-creators/visitor-permissions.md)).
2. Decide whether write routes must stay closed: they still check `content/create`, `content/edit` and
   `content/remove`, which Anonymous must not have.
3. Plan for load: a public API can be called in bulk. Put rate limits in front of it (web server or request shield,
   [Security hardening](../../security-hardening.md#a-shield-in-front-request-shield)).
4. Set `RequireHTTPS` in `rest.ini` if your deployment allows it.
5. Clear the INI cache after the change (`php bin/php/ezcache.php --clear-tag=ini`).

This is a security decision; record who approved it.

## Calls a data client makes

```bash
# the datasets of the catalog folder, 50 at a time
curl -s "https://www.example.gov/api/ezp/v2/content/node/<catalog node id>/list/offset/0/limit/50"

# the metadata of one dataset
curl -s "https://www.example.gov/api/ezp/v2/content/node/<dataset node id>/fields"

# only its license
curl -s "https://www.example.gov/api/ezp/v2/content/node/<dataset node id>/field/license"
```

Add `-H "Authorization: Bearer expk_..."` while authentication is required.

## What it is not

- **Not a query interface over the data inside your files.** The API serves content objects and their fields; a
  CSV file is a file field, not rows you can filter.
- **Not DCAT.** The JSON follows the API's own structure, not a catalog vocabulary.
- **The Atom route `listAtom` answers 500** (known issue, section 8 of the guide); use **Setup > RSS** for feeds.

## Before you announce it

- [ ] Decide authenticated or anonymous, and document how clients get a key.
- [ ] Publish a short page with the base address, the calls above and the license of the data.
- [ ] Watch the request logs and the audit trail after launch
  ([Request logs](../../../features/6.0/request-logs.md), [Audit trail](../../../features/6.0/audit-trail.md)).

## Related pages

- [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)
- [Personal API keys](../../../guides/api-keys.md)
- [Remote services and apps](../../../guides/remote-services-and-apps.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
