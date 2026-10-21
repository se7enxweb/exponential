---
title: "API design: REST, ezjscore and headless"
audience: software-engineer, developer
category: software-engineering
order: 800
summary: The interfaces Exponential offers to other systems and front ends - the REST content API of ezprestapi at v1 and v2, OAuth 2.0 and personal API keys, the ezjscore call interface and expservices, outgoing audit webhooks, RSS and Atom, WebDAV and SOAP - with their status, their gaps, and design guidance for headless and hybrid systems and for your own endpoints.
keywords: API, API design, REST, ezprestapi, v1, v2, OAuth, API keys, ezjscore, server functions, expservices, JSON, webhooks, headless, decoupled, OpenAPI, WebDAV, SOAP, software engineer
type: help
---
# API design: REST, ezjscore and headless

This page is for engineers who integrate Exponential with other systems, build a front end on it, or add endpoints of
their own. The status of each protocol, with evidence and gaps, is in [API and protocol standards](../../standards/apis-and-protocols.md).

## Contents

1. [The interfaces](#the-interfaces)
2. [The REST content API](#the-rest-content-api)
3. [The ezjscore call interface](#the-ezjscore-call-interface)
4. [Outgoing events](#outgoing-events)
5. [Headless and hybrid](#headless-and-hybrid)
6. [Designing an endpoint of your own](#designing-an-endpoint-of-your-own)
7. [Known gaps](#known-gaps)

## The interfaces

| Interface | For | Status |
|---|---|---|
| REST content API (`ezprestapi`) | reading and writing content from other systems and apps | partly complies with HTTP semantics |
| OAuth 2.0 token endpoint | applications acting for a user | partly complies (no PKCE, no `state` on the server side) |
| Personal API keys | scripts acting as one user, with scopes | Exponential 6.0.15 |
| `ezjscore` call interface | JSON calls to PHP from JavaScript and apps | complies (JSON) |
| `expservices` | named remote services on top of the call interface | see [Remote services and apps](../../guides/remote-services-and-apps.md) |
| Audit webhook | delivering audit records to another system, HMAC-signed | complies with its own contract |
| RSS and Atom | feeds (Atom export only) | [RSS feeds](../../guides/rss-feeds.md) |
| WebDAV, SOAP | file access, legacy integrations; SOAP is off by default | partly complies |

## The REST content API

- One extension, `ezprestapi`, serves `/api/ezp/v1/...` and `/api/ezp/v2/...`: reads at both versions, writes
  (create, update, delete) at v2 only. `ezprestapiprovider` is deprecated.
- Authentication: an OAuth 2.0 token from `POST /api/auth/v1/oauth/token`, or a personal API key as
  `Authorization: Bearer expk_...`.
- **Every read and write checks the user's policies** (`content/read`, `content/create`, `content/edit`,
  `content/remove`), whatever authenticated the request.
- An API key over its rate limit gets `429 Too Many Requests` with `Retry-After`.
- Settings: `settings/rest.ini`.

Guide with curl examples: [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md). For authors:
[REST API, for application creators](../creators/app-creators/rest-api.md).

## The ezjscore call interface

`/ezjscore/call/<class>::<method>` calls a registered server function and answers JSON. Server functions are PHP
classes registered in `ezjscore.ini`, protected by role policies. Use it for the JavaScript of your own pages and for
small app calls ([JSON endpoints with ezjscore](../creators/app-creators/json-endpoints-with-ezjscore.md),
[Backend ezjscore services](../../bc/6.0/backend_ezjscore_services.md),
[ezjscore server functions reference](../extension-development/part-8-reference/ezjscore-server-functions-reference.md)).

## Outgoing events

Only the audit log sends webhooks: batches of records as JSON with a timestamp and an HMAC-SHA-256 signature,
delivered through a spool with retries, at least once and de-duplicated by event id (`settings/audit.ini
[AuditSink_webhook]`, off until a URL is set). Content events (publish, remove) have no generic outgoing webhook; to
react to them, use a workflow event or a listener in an extension ([Events, extension points and plugins](events-extension-points-and-plugins.md)).

## Headless and hybrid

You keep content classes, editing, versions, translations, workflows and permissions; the front end replaces page
templates, menus and routing. Plan for:

- **reads** through the REST API, with the permissions of the authenticated user;
- **rich text and images**: rendered XHTML and image variations, served with their URLs;
- **preview** of drafts for editors;
- **caching**: the API's own cache settings and an HTTP cache in front for public reads;
- **hybrid**: templates for some pages, the API for apps, on the same content.

Details: [Headless Exponential](../creators/app-creators/headless-exponential.md).

## Designing an endpoint of your own

- Choose the home: a REST route in an extension for resources, an ezjscore server function for small calls from your
  own pages, a module view for HTML.
- Decide permissions with policy functions, never with user ids; check them on every call.
- Accept state changes only by POST (or PUT, PATCH, DELETE), with a form token for browser calls or a token or key for
  machines.
- Return JSON with `json_encode()`; give errors a status code and a stable machine-readable field.
- Keep the shape stable: a field removed or renamed is a break that needs upgrade notes
  ([Backward compatibility policy](backward-compatibility-policy.md)).
- Version the path when the shape must change, as `ezprestapi` does with v1 and v2.
- Log and audit writes; never log tokens or keys.

## Known gaps

From the standard: no OpenAPI description of the kernel's REST API (Velocity publishes one for its own handlers only),
no JSON:API, no conditional requests on writes found, no generic content webhooks, no incoming webhook endpoint, and
the OAuth server lacks PKCE.

## Related pages

- [OAuth and API keys](../creators/app-creators/oauth-and-api-keys.md)
- [Remote services with expservices](../creators/app-creators/remote-services-expservices.md)
- [AJAX with ezjscore server functions](../extension-development/part-3-extension-developer/ajax-with-ezjscore-server-functions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
