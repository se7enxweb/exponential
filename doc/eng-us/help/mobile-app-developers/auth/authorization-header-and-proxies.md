---
title: The Authorization header and proxies
audience: mobile-app-developer, app-creator, developer, hosting
category: mobile-apps
order: 270
summary: Why a valid credential can still answer 401 - how the REST layer and expservices read the Authorization header, servers and proxies that drop it (Apache with PHP-FPM), the server settings that pass it, the X-Exp-Token alternative for expservices, and how to tell the cases apart from the app side.
keywords: Authorization header, 401, proxy, FastCGI, PHP-FPM, Apache, mod_proxy_fcgi, CGIPassAuth, SetEnvIf, HTTP_AUTHORIZATION, X-Exp-Token, WWW-Authenticate, CDN, debugging, mobile app
type: help
---
# The Authorization header and proxies

This page is for app developers who get `401` with a credential that works elsewhere, and for the hosting people they
will talk to. The usual cause is not the credential but the path the header takes to PHP.

## Contents

1. [How the server reads the header](#how-the-server-reads-the-header)
2. [Who drops it](#who-drops-it)
3. [The fixes on the server](#the-fixes-on-the-server)
4. [The fix on the client for expservices](#the-fix-on-the-client-for-expservices)
5. [Telling the cases apart](#telling-the-cases-apart)

## How the server reads the header

The REST layer reads `Authorization` from the raw Apache request headers, case-insensitively, and falls back to
`$_SERVER['HTTP_AUTHORIZATION']` (`ezpOauthUtility::getAuthorizationHeader()`). It accepts `OAuth <token>` and
`Bearer <token>`; a personal API key is recognized by its `expk_` prefix. The remote services read
`HTTP_AUTHORIZATION` or `REDIRECT_HTTP_AUTHORIZATION` for `Bearer <token>`, and also the header `X-Exp-Token`
(`expServiceBase`).

## Who drops it

PHP does not expose the `Authorization` header to scripts in every setup. The sources name the common case: Apache
with PHP-FPM through `mod_proxy_fcgi` does not pass it unless configured, and "on the reference installation
`Authorization` does not arrive and `X-Exp-Token` is the header that works" for expservices
([expservices reference](../../../bc/6.0/backend_ezjscore_services.md#authentication-with-an-api-token)). A proxy, load
balancer or CDN in front of the server can also remove it. The symptom: `401` on one server only, or only behind the
production proxy ([Personal API keys: troubleshooting](../../../guides/api-keys.md#12-troubleshooting)).

## The fixes on the server

From the sources, for Apache with PHP-FPM, either of:

```apache
CGIPassAuth On
```

```apache
SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
```

For a proxy or CDN, configure it to forward `Authorization` to the origin and not to cache answers to requests that
carry it. These are hosting tasks; show the people who run the server this page and the guide's troubleshooting
table.

## The fix on the client for expservices

Send the token twice, as the shipped clients do:

```text
Authorization: Bearer expt_...
X-Exp-Token: expt_...
```

The REST API has no such second header; it needs `Authorization` to arrive (an OAuth token, but not an API key, may
also come as the `oauth_token` parameter of a POST form body; in the query string only when the site enables
`rest.ini [OAuthSettings] AllowTokenInQuery`, which this section does not recommend because URLs are logged).

## Telling the cases apart

| Answer | Likely cause |
|---|---|
| `401`, empty body, `WWW-Authenticate: OAuth realm='Exponential REST'` | no credential reached PHP: the header was dropped or not sent |
| `401` `{"error":"invalid_token"}` | the credential arrived and was refused: wrong, revoked, or a key in the URL |
| `401` `{"error":"expired_token"}` | the key arrived and is past its end date |
| works with `curl` on the server, fails from the phone | a proxy or CDN between them |

Compare a request made on the server itself with one made through the public address; if only the second fails, the
header is lost on the way.

## Related pages

- [Errors and status codes](../rest-api/errors-and-status-codes.md)
- [REST API v1 and v2 for applications: configuration](../../creators/app-creators/rest-api.md#configuration)
- [REST and JSON calls (cookbook)](../../../examples/cookbook/rest-json-fetch.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
