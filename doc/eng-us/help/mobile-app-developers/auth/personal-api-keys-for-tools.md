---
title: Personal API keys for tools
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 240
summary: Use personal API keys (expk_) for development builds, tests, kiosks, back ends and internal tools around an app - getting one, scopes, lifetimes, the rate limit, rotation and revocation, the audit events - and why a key never goes into a published app.
keywords: API key, personal API key, expk, Bearer, scopes, read, publish, edit, remove, apikey/create, rate limit, RateLimitPerMinute, 429, rotation, revoke, audit, access.apikey, development, CI, mobile app
type: help
---
# Personal API keys for tools

This page is for app developers who need a credential for their own work around an app: a development build, an
automated test, a back end, a content import, a kiosk. A personal API key is the simplest credential Exponential
offers for that. The complete guide is [Personal API keys](../../../guides/api-keys.md).

## Contents

1. [What a key is](#what-a-key-is)
2. [Getting a key](#getting-a-key)
3. [Scopes](#scopes)
4. [Limits](#limits)
5. [Rotating and revoking](#rotating-and-revoking)
6. [Where keys belong](#where-keys-belong)

## What a key is

A key is a string `expk_<12 characters>_<40 characters>` that a user makes for one program of their own. A request
with `Authorization: Bearer expk_...` runs as the key's owner, limited to the key's scopes, and never beyond the
owner's own policies. Only a salted HMAC of the secret is stored, so nobody, not even an administrator, can show a
key again ([In short](../../../guides/api-keys.md#in-short)). Keys are accepted by the REST API, not by the remote
services, which have tokens of their own ([expservices tokens in apps](expservices-tokens-in-apps.md)).

## Getting a key

1. You need an account with the policy `apikey/create`; in a new installation no role has it, so an administrator
   grants it ([Switching keys on for your users](../../../guides/api-keys.md#5-switching-keys-on-for-your-users)).
2. Sign in on the site and open **My account > API access** (`/apikey/list`).
3. Name the key after where it is used ("Android CI tests"), tick the scopes, choose a lifetime (7, 30, 90 or 365 days
   by default).
4. Copy the key from the next page. It is shown once, with `Cache-Control: no-store`.

## Scopes

| Scope | Stands for | Lets the key call |
|---|---|---|
| `read` | `content/read` | every `GET` and `HEAD` route |
| `publish` | `content/create` | `POST /api/ezp/v2/content/node/create` |
| `edit` | `content/edit` | `POST /api/ezp/v2/content/node/<id>` (which answers `501` today) |
| `remove` | `content/remove` | `DELETE /api/ezp/v2/content/node/delete/<id>` |

The mapping is `rest.ini [ApiKeySettings] Scopes[]` and `RouteScopes[]`. A test that only reads gets only `read`.

## Limits

| Setting | Default | Effect for your tool |
|---|---|---|
| `RateLimitPerMinute` | 120 | more requests per key and minute answer `429 rate_limited` with `Retry-After`; counted in APCu per PHP-FPM pool or Velocity server |
| `MaxExpiryDays` | 365 | the longest lifetime |
| `MaxKeysPerUser` | 10 | keys that still work per user |
| `LastUsedInterval` | 60 | how often "last used" is written, in seconds |

A load test or a bulk import must stay under the rate limit or use several users' keys with the administrator's
agreement ([Auditing and limits](../../../guides/api-keys.md#8-auditing-and-limits)).

## Rotating and revoking

Make the new key first, switch the tool to it, check that its **Last used** fills in, then revoke the old one. Revoke
at once when a key may have been seen by someone else, when the tool is retired, or when the machine it lived on is
lost. Revoking takes effect at the next request on every server. Administrators see every key at
**Setup > oAuth admin > API keys** (`/oauthadmin/keys`), and every make, revoke, first use and refusal is recorded in
the audit trail as `access.apikey.*`.

## Where keys belong

| Place | Key? |
|---|---|
| A developer's shell, as an environment variable | yes |
| A CI secret store | yes |
| A back end's secret store | yes |
| A managed kiosk device, with a read-only key of its own | yes, by decision of the site owner |
| A published app, in code, resources or configuration | **no**: anyone can extract it, and it acts as a person |
| A URL | **no**: a key in the query string is refused even when valid |

## Related pages

- [Choosing a credential](choosing-a-credential.md)
- [Storing secrets on devices](storing-secrets-on-devices.md)
- [Personal API keys: troubleshooting](../../../guides/api-keys.md#12-troubleshooting)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
