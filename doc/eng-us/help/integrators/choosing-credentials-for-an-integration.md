---
title: Choosing credentials for an integration
audience: integrator, developer, admin
category: integration
order: 40
summary: The credentials Exponential accepts from other systems - OAuth 2.0 tokens, personal API keys, expservices tokens, HTTP basic authentication, session sign-in - what each is for, how it is limited, and how to store and rotate it.
keywords: credentials, authentication, OAuth, token, API key, expk, expt, expservices token, basic authentication, bearer, service account, rotation, revocation, least privilege
type: help
---
# Choosing credentials for an integration

This page is for integration developers and administrators who must decide how another system proves who it is to
Exponential. Every credential in Exponential stands for **one user**, and a request made with it can never do more
than that user's roles allow. The choice is about who that user is, how long the credential lives and how it is
revoked.

## Contents

1. [The credentials](#the-credentials)
2. [Which one for which job](#which-one-for-which-job)
3. [Limits that always apply](#limits-that-always-apply)
4. [Storing and rotating credentials](#storing-and-rotating-credentials)
5. [Checklist](#checklist)

## The credentials

| Credential | Looks like | Sent as | Works for | Made by | Ends |
|---|---|---|---|---|---|
| OAuth 2.0 access token | 40 hexadecimal characters | `Authorization: Bearer <token>` or `Authorization: OAuth <token>` | REST API (`/api/...`) | An app after the user allowed it at `oauth/authorize`; token from `POST /api/auth/v1/oauth/token` | After `rest.ini [OAuthSettings] TokenTTL` (3600 seconds); renewed with the refresh token |
| Personal API key | `expk_` + 12 + `_` + 40 characters | `Authorization: Bearer expk_...` only (refused in the address) | REST API | The user at **My account > API access** (`/apikey/list`), with `apikey/create` | Revoked, expired (`MaxExpiryDays`, default 365), or owner disabled |
| `expservices` token | `expt_` + 48 hexadecimal characters | `Authorization: Bearer <token>` or `X-Exp-Token: <token>` | `/ezjscore/call/exp...` services | The user with `expsession::tokenCreate` | Revoked (`expsession::tokenRevoke`), expired, or owner disabled |
| HTTP basic authentication | login and password | `Authorization: Basic ...` | REST API when `rest.ini [Authentication] AuthenticationStyle=ezpRestBasicAuthStyle`; SOAP | The user account | When the password changes |
| Session sign-in | session cookie plus form token | Cookie and `ezxform_token` | `expservices` and ezjscore calls from a browser or a script that signs in | `user/login` | Session end |

The REST layer resolves a personal API key in `expApiKeyAuthFilter` (`kernel/private/rest/classes/auth/apikey_auth_filter.php`)
for both kernel authentication styles, so keys work whether the site uses OAuth or basic authentication.

## Which one for which job

| Job | Use | Why |
|---|---|---|
| A script or server of your company that reads or publishes content through REST | A **personal API key** of a service account | One key per program, scoped (`read`, `publish`, `edit`, `remove`), revocable on its own, audited |
| An app that lets many people sign in and act as themselves | An **OAuth application** | Each user allows the app; the token acts as that user |
| A script that calls `expservices` (caches, content jobs, users, ...) | An **`expservices` token** of a service account | No form token needed for writes; revocable |
| A browser page on the same site | The **session** | It is already there; writes carry the form token |
| A legacy client that only knows basic authentication | **Basic authentication** over HTTPS, with a dedicated account | Last resort: the password opens every page the account may open |

> **Note**
> A password is the widest credential: it signs in everywhere the account may go, and changing it breaks every
> program at once. Prefer a key or token whenever the surface accepts one
> ([Personal API keys](../../guides/api-keys.md#1-what-a-key-is-and-what-it-is-not)).

## Limits that always apply

- **Never more than the user.** Every REST read and write asks the user's policies through
  `expRestContentPermission`; every `expservices` service declares its access and checks it. A key's scope is
  checked again on each request against the owner's current policies.
- **Rate limit for keys.** `rest.ini [ApiKeySettings] RateLimitPerMinute=120` per key, counted in APCu; over the
  limit the answer is `429` with `Retry-After`.
- **Header only for keys.** A key in `oauth_token`, `access_token` or `api_key` of the address or body is refused
  without a lookup.
- **HTTPS.** `rest.ini [Authentication] RequireHTTPS` can refuse plain HTTP for the REST API; serve every API over
  HTTPS regardless.
- **Proxies.** If a proxy or Apache with PHP-FPM drops the `Authorization` header, keys and tokens fail with 401.
  Pass the header (`CGIPassAuth On`), or use `X-Exp-Token` for `expservices`.

## Storing and rotating credentials

| Do | Do not |
|---|---|
| Keep keys and tokens in the calling system's secret store or environment variables | Put them in Git, in a template, in a URL, or in a shared script |
| Make one credential per program and per environment | Share one key between test and production |
| Rotate by making the new key first, switching, checking **Last used**, then revoking the old one | Revoke first and fix the program afterwards |
| Teach your secret scanner the prefixes `expk_` and `expt_` | Paste keys into tickets or chats |
| Revoke at once when a credential may have been seen | Wait for it to expire |

Every make, revoke, first use and refusal of a key is recorded in the audit trail as `access.apikey.*`;
`expservices` tokens as `access.expservices.token.*`:

```bash
php bin/php/console exp:audit tail --channel=access --name='access.apikey.*' --lines=20 --allow-root-user
```

## Checklist

- [ ] One service account per integration, with a narrow role ([how](service-accounts-roles-and-policies.md)).
- [ ] The credential type chosen per data flow and written into the interface specification.
- [ ] Scopes limited to what the flow needs (a reading integration gets `read` only).
- [ ] Expiry date set and a rotation date in the operations calendar.
- [ ] The credential stored outside Git; its location named in the runbook.
- [ ] The audit events checked after the first run.

## Related pages

- [Personal API keys for integrations](personal-api-keys-for-integrations.md)
- [OAuth applications for integrations](oauth-applications-for-integrations.md)
- [Remote services with expservices](remote-services-with-expservices.md)
- [OAuth and API keys for app creators](../creators/app-creators/oauth-and-api-keys.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
