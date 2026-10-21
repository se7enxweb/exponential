---
title: Personal API keys for integrations
audience: integrator, developer, admin
category: integration
order: 70
summary: Using personal API keys of a service account for server-to-server integrations over REST - switching keys on for one role, scopes, making and rotating the key, rate limits, audit events, and opening your own REST routes to keys.
keywords: API key, personal API key, expk, service account, apikey/create, scopes, read, publish, edit, remove, RouteScopes, RouteGuards, rate limit, rotation, audit, access.apikey
type: help
---
# Personal API keys for integrations

This page is for integrators who connect a server of their own (an ERP, a PIM, a CI job) to the REST API of
Exponential. The usual answer for machine-to-machine calls is a **personal API key of a service account**: a key
that belongs to one user made for the integration, limited to the scopes the integration needs. The full guide,
written for every reader, is [Personal API keys](../../guides/api-keys.md).

## Contents

1. [The setup in six steps](#the-setup-in-six-steps)
2. [Scopes and the routes they open](#scopes-and-the-routes-they-open)
3. [Rotating without downtime](#rotating-without-downtime)
4. [Limits and audit](#limits-and-audit)
5. [Opening your own REST routes to keys](#opening-your-own-rest-routes-to-keys)
6. [Troubleshooting](#troubleshooting)

## The setup in six steps

1. **Create the service account**, for example `svc-erp-import`, in a user group of its own
   ([Service accounts and roles](service-accounts-roles-and-policies.md)).
2. **Create a role** for it with exactly the content rights the integration needs: `content/read` on the subtrees it
   reads, `content/create` limited by class and subtree for what it publishes, `content/remove` only if it removes.
3. **Add the policy** `apikey/create` to that role, with the limitation **Scope** set to the scopes the key may get.
4. **Sign in as the service account** (once, in a private browser window), open **My account > API access**
   (`/apikey/list`), name the key after the program and the environment ("ERP import, production"), tick the scopes and
   choose a lifetime (7, 30, 90 or 365 days by default).
5. **Copy the key** from the page that shows it once (`expk_` and 53 more characters) into the secret store of the
   calling system. It is never shown again; only an HMAC-SHA-256 hash is stored.
6. **Test it**:

   ```bash
   curl -s -o /dev/null -w '%{http_code}\n' -H "Authorization: Bearer $EXP_API_KEY" \
        https://example.com/api/ezp/v2/content/node/2
   ```

   `200` means the key works and may read node 2; `403` means the account may not read that node.

## Scopes and the routes they open

| Scope | Stands for the policy | Opens |
|---|---|---|
| `read` | `content/read` | Every `GET` and `HEAD` route (`DefaultReadScope=read`) |
| `publish` | `content/create` | `ezp7xRestContentController_CreateContentNode` |
| `edit` | `content/edit` | `ezp7xRestContentController_UpdateContentNode` |
| `remove` | `content/remove` | `ezp7xRestContentController_DeleteContentNode` |

Three checks run on every request: the key has the scope, the owner still holds the scope's policy, and for write
routes listed in `RouteGuards[]` the owner's rights on the node written to. The request then runs as the owner, so
the controller's own permission checks apply as well. A write route of an extension that is not listed in
`RouteScopes[]` is closed to keys.

## Rotating without downtime

1. Make a second key with the same scopes, named with the date ("ERP import, production, 2027-01").
2. Put it in the calling system and run one call.
3. Check that the new key's **Last used** fills in on `/apikey/list`.
4. Revoke the old key. Every request with it is refused from the next one on, on every server.

Plan the rotation before the key expires: put the end date into the operations calendar when you make the key.
Administrators see every key, with filters for "expiring within seven days" and "active but never used", at
**Setup > oAuth admin > API keys** (`/oauthadmin/keys`).

## Limits and audit

| Setting (`rest.ini [ApiKeySettings]`) | Default | Meaning for an integration |
|---|---|---|
| `RateLimitPerMinute` | `120` | Requests per key and minute, counted in APCu per PHP-FPM pool or Velocity server; over it: `429` with `Retry-After` |
| `MaxKeysPerUser` | `10` | Working keys per user |
| `MaxExpiryDays` | `365` | The longest lifetime; `0` allows keys without an end date |
| `LastUsedInterval` | `60` | Seconds between two writes of a key's last use |

A bulk job that needs more than 120 requests a minute is a sign that it belongs on the server as a cronjob part
([Choosing an integration surface](choosing-an-integration-surface.md)), not that the limit should go.

The audit's `access` channel records `access.apikey.create`, `access.apikey.revoke`, the first `access.apikey.use`, and
every refusal as `access.apikey.use.failed` with its reason (`unknown`, `revoked`, `expired`, `scope`, `policy`,
`permission`, `rate_limited` ...).

## Opening your own REST routes to keys

When your extension adds a REST controller, name the scope each write route needs in your extension's
`settings/rest.ini.append.php`:

```ini
[ApiKeySettings]
RouteScopes[myExtRestController_doImport]=publish
```

`GET` and `HEAD` routes that are not listed need `DefaultReadScope`.

## Troubleshooting

| Symptom | Cause |
|---|---|
| No **API access** link on the service account's profile | The role lacks `apikey/create` |
| `401` `invalid_token` with a fresh key | The key was cut while copying (it is 58 characters), sent in the address, or sent without `Bearer` |
| `401` on one server only | A proxy drops the `Authorization` header; pass it (`CGIPassAuth On`) |
| `403` `insufficient_scope` | Missing scope, lost policy, or a write outside the owner's rights; the audit event names which |
| `429` | Over the rate limit; honor `Retry-After` |

## Related pages

- [Personal API keys](../../guides/api-keys.md), the complete guide
- [The REST API for integrators](rest-api-for-integrators.md)
- [Service accounts, roles and policies](service-accounts-roles-and-policies.md)
- [Choosing credentials for an integration](choosing-credentials-for-an-integration.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
