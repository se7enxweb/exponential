---
title: ezjscore server functions for your app
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 340
summary: When an app needs an endpoint no REST route or service offers - what to ask a PHP developer for, how an ezjscore server function is registered and called, its answer wrapper, the permission rules it must follow, authentication from a native app, and when a REST provider of your own is the better choice.
keywords: ezjscore, server function, ezjscServer, ezjscore/call, custom endpoint, JSON, RequirePolicy, FunctionList, ezjscore/call policy, REST provider, brief, mobile app
type: help
---
# ezjscore server functions for your app

This page is for app developers who need an endpoint that does not exist yet, for example one call that gives a home
screen everything it shows. It explains what to ask a PHP developer for and how the app calls the result. The
server-side how-to is [JSON endpoints with ezjscore](../../creators/app-creators/json-endpoints-with-ezjscore.md).

## Contents

1. [When to ask for one](#when-to-ask-for-one)
2. [What gets built](#what-gets-built)
3. [Calling it](#calling-it)
4. [Authentication from a native app](#authentication-from-a-native-app)
5. [A brief for the PHP developer](#a-brief-for-the-php-developer)

## When to ask for one

| Need | Better choice |
|---|---|
| Read nodes, children, fields | the REST API, no new code |
| Search, image alias URLs, updates, languages | an existing remote service, no new code |
| One answer combining several of the above for one screen | a server function, or your own back end ([A back end for your app](../auth/a-backend-for-your-app.md)) |
| Data from a custom table of an extension | a server function or a remote service domain of the extension |
| Routes that should be versioned and use OAuth like the content API | a REST provider of your own ([REST API for applications](../../creators/app-creators/rest-api.md#your-own-rest-provider)) |

## What gets built

A server function is a public static PHP method registered in an `[ezjscServer_<name>]` block of an extension's
`ezjscore.ini.append.php`. The router refuses any name without such a block, calls `<class>::<function>( array $args )`
and wraps the return value ([How a call works](../../creators/app-creators/json-endpoints-with-ezjscore.md#how-a-call-works)).
If the extension writes it as an expservices domain instead (a class extending `expServiceBase`), it gets the
envelope, the catalog entry and API tokens for free
([A service domain of your own](../../creators/app-creators/remote-services-expservices.md#a-service-domain-of-your-own)).

## Calling it

```bash
curl -s 'https://example.com/ezjscore/call/<name>::<function>::<argument>?ContentType=json'
```

```json
{"error_text": "", "content": {"...": "the function's return value"}}
```

A plain server function answers its own value in `content`; an expservices domain answers the
`{"ok":...}` envelope inside `content` ([Calling expservices from an app](calling-expservices-from-an-app.md#the-envelope)).
Check `error_text` first: it is set when the router refused the call.

ezjscore ships a test function you can use to check the interface: `ezjsc::time`.

## Authentication from a native app

A plain server function runs as the current user of the request: a session cookie, or anonymous. It does not
understand REST keys, OAuth tokens or expservices tokens by itself. For an app that is a reason to prefer an
expservices domain, which accepts expservices tokens, or a REST provider, which uses the REST authentication.

Classic server functions can also be guarded by the policy `ezjscore/call` with the `FunctionList` limitation; how
strictly is set by `RequirePolicy` (not set, `disabled` or `enabled`) in the function's block
([expservices reference: access](../../../bc/6.0/backend_ezjscore_services.md#access)).

## A brief for the PHP developer

- The screen and the exact JSON it needs (an example answer).
- The user the call runs as, and which content that user may read.
- Read or write; writes are `POST` only, with the form token for sessions or a bearer token for expservices.
- Paging: `limit` and `offset` as the last arguments.
- Error cases and how the app should see them.
- Caching: how fresh the answer must be.
- "Check permissions in every function; validate and cast every argument; return data, not HTML" are the rules of
  [JSON endpoints with ezjscore: security](../../creators/app-creators/json-endpoints-with-ezjscore.md#security).

## Related pages

- [Which API for your app](../which-api-for-your-app.md)
- [Remote services with expservices](../../creators/app-creators/remote-services-expservices.md)
- [REST and JSON calls (cookbook)](../../../examples/cookbook/rest-json-fetch.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
