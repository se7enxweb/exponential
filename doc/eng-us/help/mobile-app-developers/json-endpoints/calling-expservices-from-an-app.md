---
title: Calling expservices from an app
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 310
summary: Call the expservices remote services from app code - the URL with arguments joined by ::, percent encoding, the ezjscore wrapper and the envelope inside it, paging with meta.has_more, discovering services from the catalog, error codes inside an HTTP 200, and a client structure to copy.
keywords: expservices, ezjscore/call, envelope, ok, data, meta, error, has_more, total, paging, MaxLimit, catalog, discovery, expservices::catalog, arguments, percent encoding, Kotlin, Swift, client, mobile app
type: help
---
# Calling expservices from an app

This page is for app developers who write the client for the remote services. It sums up the contract every service
follows, from the [clients chapter of the expservices reference](../../../bc/6.0/backend_ezjscore_services.md#clients),
and shows how to fit it into an app's network layer.

## Contents

1. [The request](#the-request)
2. [The envelope](#the-envelope)
3. [Paging](#paging)
4. [Discovering services](#discovering-services)
5. [Errors](#errors)
6. [A client structure](#a-client-structure)

## The request

```text
GET  https://example.com/ezjscore/call/<service>[::<arg>...]?ContentType=json     (reads)
POST https://example.com/ezjscore/call/<service>?ContentType=json                  (writes, arguments as form fields)
Accept: application/json
Authorization: Bearer expt_...
X-Exp-Token: expt_...
```

- Arguments of a read are **positional**, percent-encoded and joined with `::`, in the order of the service's `args`.
  `expnode::children` takes node id, sort, order, limit, offset (and an optional JSON filter):
  `expnode::children::2::name::asc::5::0`.
- A read with no credential runs as the anonymous user, which works only for services whose access is `public`.
- Writes are described in [Writing through expservices](writing-through-expservices.md).

## The envelope

ezjscore wraps every answer:

```json
{"error_text": "", "content": {"ok": true, "data": {}, "meta": {}}}
```

- `error_text` is set only when the call could not be routed (unknown service, extension not active). Then `content`
  is empty.
- `content` is the service's envelope: `{"ok":true,"data":...,"meta":{...}}` or
  `{"ok":false,"error":{"code":403,"message":"..."}}`.

The shipped shell and Python clients unwrap both layers; your client should too, and give the rest of the app only
`data`, `meta` or an error.

## Paging

Services that list take `limit` and `offset` as their last two URL arguments and answer `meta.total`, `meta.offset`,
`meta.limit`, `meta.count` and `meta.has_more`. Loop while `has_more` is true. The default and maximum limit are
`expservices.ini [Paging] DefaultLimit` and `MaxLimit` (25 and 200).

```bash
curl -s -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     'https://example.com/ezjscore/call/expnode::children::<node id>::published::desc::20::0?ContentType=json'
```

## Discovering services

`expservices::catalog` lists every service with its domain, method, summary, `access` (`public`, `user`, or a
`[module, function]` policy), `write`, `args` and `returns`; `expservices::service::<domain>::<method>` gives one
descriptor. The reference states that you can generate a typed client from the catalog. A practical use in app
development: fetch the descriptors of the few services the app uses during the build or in a test, and fail the test
when their arguments change.

```bash
curl -s 'https://example.com/ezjscore/call/expservices::service::expimage::url?ContentType=json'
```

## Errors

HTTP 200 does not mean success; look at `content.ok`:

| `content.error.code` | Meaning | App behavior |
|---|---|---|
| 400 | bad or missing arguments | a bug in the app: log it |
| 401 | login needed, or the token died | delete the token, sign in again |
| 403 | no policy, no POST, or a wrong form token | show "not allowed"; do not loop |
| 404 | no such service, node, object | drop the item |
| 409 | conflict | show the message |
| 422 | the data is not valid | show the message |
| 500 | the service failed | retry later |

A non-200 HTTP status or a body that is not JSON is a transport fault (proxy, TLS, server down): retry with backoff.

## A client structure

```text
ExpServicesClient
  call(service, args[]) -> Result<data, meta>      GET, args percent-encoded, joined with ::
  post(service, fields{}) -> Result<data, meta>    POST form fields
  - adds Accept, Authorization: Bearer, X-Exp-Token
  - checks HTTP status and JSON (transport fault)
  - checks error_text (routing fault)
  - unwraps content.ok / content.error.code into the app's error type
```

The reference has fuller snippets for [Kotlin with OkHttp](../../../bc/6.0/backend_ezjscore_services.md#kotlin--android-okhttp)
and [Swift with URLSession](../../../bc/6.0/backend_ezjscore_services.md#swift--ios-urlsession); it says they were reviewed
by hand but could not be compiled on its build machine, so compile them in your project first.

## Related pages

- [expservices tokens in apps](../auth/expservices-tokens-in-apps.md)
- [Remote services and apps](../../../guides/remote-services-and-apps.md)
- [Which API for your app](../which-api-for-your-app.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
