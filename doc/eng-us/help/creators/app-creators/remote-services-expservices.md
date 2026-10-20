---
title: Remote services with expservices
audience: app-creator, developer
category: app-creation
order: 360
summary: Drive Exponential from scripts, apps and other front ends with expservices - over a thousand named services, discovery, the answer envelope, the shell and Python clients, tokens, policies per service, and adding a service domain of your own.
keywords: expservices, remote services, ezjscore call, discovery, envelope, API token, shell client, Python, services, domain, expServiceBase, app creator
type: help
---
# Remote services with expservices

This page is for application creators who build a remote admin app, a JavaScript front end or a script that drives
the administration's functions. `expservices` turns the admin's functions into over a thousand named services (content,
users, roles, caches, cronjobs, shop, feeds, media, tags, layouts, the audit and more), called through ezjscore
([Remote services and apps](../../../guides/remote-services-and-apps.md),
[Backend ezjscore services](../../../bc/6.0/backend_ezjscore_services.md)).

## Contents

1. [Check it answers](#check-it-answers)
2. [Calling a service](#calling-a-service)
3. [Authentication](#authentication)
4. [A service domain of your own](#a-service-domain-of-your-own)
5. [When to use it](#when-to-use-it)

## Check it answers

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

An envelope that says you are anonymous. 403 on every call means the master switch is off:
`./console exp:ini get expservices.ini/Services/Enabled` must say `enabled`.

## Calling a service

Every answer has one shape inside ezjscore's `content`: `{"ok":true,"data":...,"meta":{...}}` or
`{"ok":false,"error":{...}}`. The shipped clients unwrap it:

```bash
export EXPSERVICES_URL=https://example.com
extension/expservices/bin/expservices-client.sh whoami
extension/expservices/bin/expservices-client.sh call expsystem::version
```

Discovery lists every service with its parameters, so clients do not hard-code the catalog.

## Authentication

Personal API tokens for scripts; a signed-in session for pages of the same site. Each service declares the policies it
needs and checks them as the calling user. Behind Apache with `mod_proxy_fcgi`, make sure the `Authorization` header
reaches PHP. The token table is created from `extension/expservices/sql/<engine>/schema.sql`.

## A service domain of your own

A domain is a class `exp<Domain>Services` extending `expServiceBase`, registered as `[ezjscServer_exp<domain>]` in your
extension's `ezjscore.ini.append.php`, and called as `<root>/ezjscore/call/exp<domain>::<function>`. Follow the shipped
domains in `extension/expservices/classes/` for the envelope, the policy declaration and the discovery entry; read
services can be offered to Exponential Intelligence as tools with `'ai' => true`
([Exponential Intelligence for applications](exponential-intelligence.md)).

## When to use it

| Need | Use |
|---|---|
| A remote admin client, a portal, scripts that manage the site | expservices |
| Content for apps and front ends, OAuth for third parties | REST ([REST API](rest-api.md)) |
| Your own page's JavaScript | an ezjscore server function ([JSON endpoints](json-endpoints-with-ezjscore.md)) |
| AI tools that act inside the administration | Exponential Intelligence tools ([Extending Exponential Intelligence](extending-exponential-intelligence.md)) |

## Related pages

- [OAuth and API keys](oauth-and-api-keys.md)
- [Headless Exponential](headless-exponential.md)
- [Remote services (feature)](../../../features/6.0/remote-services-expservices.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
