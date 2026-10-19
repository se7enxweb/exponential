---
title: OAuth and API keys
audience: app-creator, developer, admin
category: app-creation
order: 350
summary: Choose and use the right credential for programs that call Exponential - OAuth2 clients and the authorize policy, personal API keys with scopes, expservices tokens, HTTP basic, lifetimes, revocation and the audit trail.
keywords: OAuth2, OAuth client, oauth/authorize, token, refresh token, API key, expk, scopes, apikey/create, expservices token, authentication, revoke, audit, app creator
type: help
---
# OAuth and API keys

This page is for application creators who connect programs to Exponential. There are three credentials; choose by who
the program acts for.

## Contents

1. [Which credential](#which-credential)
2. [OAuth2 for third-party applications](#oauth2-for-third-party-applications)
3. [Personal API keys](#personal-api-keys)
4. [expservices tokens](#expservices-tokens)
5. [Good practice](#good-practice)

## Which credential

| The program | Use | Read |
|---|---|---|
| acts for many users who sign in themselves (a mobile app) | OAuth2 | [3.1 OAuth2 tokens](../../../guides/rest-api-v1-and-v2.md#31-oauth2-tokens) |
| is one user's own script | a personal API key | [Personal API keys](../../../guides/api-keys.md) |
| drives admin services (apps, portals, shells) | an expservices token | [Remote services and apps](../../../guides/remote-services-and-apps.md) |

## OAuth2 for third-party applications

1. An administrator registers the client in **Setup > oAuth admin** (`/oauthadmin/list`): identifier, secret, redirect
   address.
2. The app sends the user to `/oauth/authorize?client_id=...&redirect_uri=...&response_type=code`; the user signs in
   and allows it. The user needs the policy `oauth/authorize` (optionally limited by siteaccess); without it the site
   redirects with `error=access_denied` ([OAuth authorize policy](../../../bc/6.0/oauth-authorize-policy.md)).
3. The app exchanges the code at `POST /api/auth/v1/oauth/token` (`grant_type=authorization_code`).
4. It sends `Authorization: OAuth <token>`; after `TokenTTL` (one hour by default) it uses `grant_type=refresh_token`.

![oAuth admin: the OAuth clients of the installation and their access](../../images/creators/oauth-admin.webp)

The client secret belongs on a server; an app that cannot keep a secret should go through a back end of yours.

## Personal API keys

- A user with `apikey/create` makes keys in **My account > API access** (`/apikey/list`), with scopes and a lifetime;
  the key `expk_...` is shown once.
- Requests with `Authorization: Bearer expk_...` run as the owner, limited to the key's scopes and never beyond the
  owner's policies.
- Only a salted HMAC of the secret is stored. Keys end when revoked, expired, or when the owner is disabled.
- Administrators see and revoke every key in **Setup > oAuth admin > API keys**; every make, revoke, first use and
  refusal is in the audit trail as `access.apikey.*`.

## expservices tokens

expservices has its own personal tokens for its services, created per user; the guide walks through making one and
calling services from shell and Python ([Remote services and apps](../../../guides/remote-services-and-apps.md)).

## Good practice

- One credential per program, so one can be revoked alone.
- Smallest scope and role that works; a dedicated user for integrations, not the administrator.
- Secrets in environment variables or a secret store; never in a repository, a URL or a log.
- Short lifetimes, rotation, and a check of the audit trail for refusals.

## Related pages

- [REST API v1 and v2 for applications](rest-api.md)
- [Access control in code](access-control-in-code.md)
- [API keys BC note](../../../bc/6.0/api-keys.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
