---
title: The REST API
audience: dxp-developer
category: dxp-development
order: 25
summary: The REST API v2 of the DXP (ibexa/rest) for developers - where it lives and how to check the prefix, what it covers, how clients authenticate (session, JWT, basic), CORS, and how to try it with curl.
keywords: REST API, ibexa/rest, REST v2, api/ibexa/v2, path prefix, JWT, CORS, curl, headless, content, locations, authentication, Accept header
type: help
---
# The REST API

This page is for developers who read or write content from another application: a mobile app, a JavaScript front
end, an integration. The DXP ships the REST API v2 (`ibexa/rest`), "a headless CRUD REST API" in the skeleton's words.

## Contents

1. [Where it lives](#where-it-lives)
2. [What it covers](#what-it-covers)
3. [Authentication](#authentication)
4. [Trying it](#trying-it)
5. [CORS](#cors)
6. [Before you build a client](#before-you-build-a-client)

## Where it lives

The routes are imported under the prefix parameter `ibexa.rest.path_prefix` (see `config/routes/ibexa_rest.yaml`).
In the installed v5 package its default is **`/api/ibexa/v2`**. The skeleton's guide and README show `/api/ezp/v2/`,
the prefix of the older lines. Check what your project really serves:

```bash
php bin/console debug:container --parameter=ibexa.rest.path_prefix
php bin/console debug:router | grep -m5 "ibexa.rest"
```

Use the value your project prints in every client, and keep it in one configuration value of the client.

## What it covers

The REST API exposes the content repository: content items and their versions, content types, locations and the
tree, sections, object states, languages, users, user groups, roles, URL aliases, trash, bookmarks and search
(views). Each resource has its own media type, and clients ask for JSON or XML with the `Accept` header.

## Authentication

| Method | When | Setup |
|---|---|---|
| Session cookie | A front end on the same host that signs users in | The session of the site |
| JWT | Apps and services | `lexik/jwt-authentication-bundle` with the keypair; enable the JWT firewalls ([JWT authentication](jwt-authentication.md)) |
| HTTP basic | Tests and internal scripts only, over HTTPS | A commented firewall in `security.yaml` |

Anonymous requests see what the Anonymous role may read.

## Trying it

```bash
BASE="https://127.0.0.1:8000/api/ibexa/v2"       # use your prefix
curl -s -H "Accept: application/vnd.ibexa.api.Root+json" "$BASE/"
curl -s -H "Accept: application/vnd.ibexa.api.Location+json" "$BASE/content/locations/1/2"
```

The root resource lists the entry points of the API. If a media type name differs in your version, the root
response shows the names it uses.

## CORS

Browsers on another origin need CORS. The skeleton's guide sets `CORS_ALLOW_ORIGIN` in `.env.local`; widen it only to
the origins of your own clients ([Environment variables](environment-variables.md#search-cache-mail-and-the-rest)).

## Before you build a client

- Decide what a client may write; give its user a role with only those policies.
- Plan caching: anonymous `GET` responses pass through the HTTP cache ([HTTP cache](http-cache-and-varnish.md)).
- Keep the API prefix and media types out of hard-coded strings scattered over the client.
- For read-heavy front ends, compare with GraphQL ([GraphQL and GraphiQL](graphql-and-graphiql.md)).

## Related pages

- [JWT authentication](jwt-authentication.md)
- [Headless with the DXP](headless-with-the-dxp.md)
- [Headless with the REST API (for organizations)](../businesses/mobile-and-headless/headless-with-the-rest-api.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
