---
title: OAuth applications for integrations
audience: integrator, developer, admin
category: integration
order: 60
summary: How an external application signs users in and acts for them through Exponential's OAuth 2.0 authorization server - registering the application, the authorization code flow, refreshing tokens, the oauth/authorize policy, and the gaps an integrator must design around.
keywords: OAuth, OAuth 2.0, authorization code, refresh token, oauthadmin, client id, client secret, redirect uri, oauth/authorize, token endpoint, TokenTTL, PKCE, state, client_credentials
type: help
---
# OAuth applications for integrations

This page is for developers of applications that let **many people** sign in with their Exponential account and act
as themselves: a mobile app for reporters, a partner portal, an intranet tool. Exponential is an OAuth 2.0
authorization server for its REST API (`kernel/private/modules/oauth/`, `kernel/private/rest/classes/oauth/`). For a
program of your own that acts as one service account, a [personal API key](personal-api-keys-for-integrations.md) is
simpler.

## Contents

1. [The flow](#the-flow)
2. [Registering the application](#registering-the-application)
3. [Getting and refreshing tokens](#getting-and-refreshing-tokens)
4. [Who may authorize an application](#who-may-authorize-an-application)
5. [Gaps to design around](#gaps-to-design-around)
6. [Checklist](#checklist)

## The flow

```text
 your app                         Exponential (example.com)
 --------                         -------------------------
 1. send the user to  ----------> /oauth/authorize?client_id=..&redirect_uri=..&response_type=code
                                  the user signs in and allows the app (needs oauth/authorize)
 2. receive ?code=..  <---------- redirect to redirect_uri
 3. POST code ------------------> /api/auth/v1/oauth/token  (grant_type=authorization_code)
 4. receive tokens    <---------- access_token, refresh_token, expires_in
 5. call the API -----------------> /api/ezp/v2/...  Authorization: Bearer <access_token>
 6. after expiry: POST ---------> /api/auth/v1/oauth/token  (grant_type=refresh_token)
```

## Registering the application

1. Sign in to the administration as an administrator and open **Setup > oAuth admin** (`/oauthadmin/list`).
2. Press **New application** and give its name and the redirect address (the end point) of your application.
3. The application gets a random client identifier and secret. The secret is folded away on the application's page
   until **Show the secret** is pressed; copy it into your application's secret store.

The **Applications** tab shows, per application, the owner, the end point, how many users authorized it and how many
of its tokens are still valid. Removing an application also removes its authorizations, tokens and codes: this is how
you cut an application off at once.

## Getting and refreshing tokens

Exchange the code (the parameters are form fields of a `POST`):

```bash
curl -s https://example.com/api/auth/v1/oauth/token \
     -d grant_type=authorization_code -d client_id='<client id>' -d client_secret='<client secret>' \
     -d code='<code>' -d redirect_uri='<redirect uri>'
```

Refresh before or when a call answers `401` `expired_token`:

```bash
curl -s https://example.com/api/auth/v1/oauth/token \
     -d grant_type=refresh_token -d client_id='<client id>' -d client_secret='<client secret>' \
     -d refresh_token='<refresh token>'
```

The access token lives for `rest.ini [OAuthSettings] TokenTTL` seconds (3600 by default). Send it as
`Authorization: Bearer <token>`. Every request runs as the user who allowed the application, with that user's
policies.

## Who may authorize an application

The user needs the policy **OAuth / Authorize** (`oauth/authorize`). Without it the site redirects back with
`error=access_denied`. Give the policy only to the roles whose members should use the application
([upgrade note](../../bc/6.0/oauth-authorize-policy.md)). An unknown client, or a `redirect_uri` that is not the
client's end point, gets `400 Bad Request` with no redirect.

## Gaps to design around

From [API and protocol standards](../../standards/apis-and-protocols.md#oauth-20-rfc-6749-and-bearer-tokens-rfc-6750):

| Gap | Consequence | Mitigation |
|---|---|---|
| `state` is not returned to the client | No built-in cross-site request forgery protection of the redirect | Bind the flow to your own session (a value you store before redirecting and check against the code you receive) |
| No PKCE | A public client (a mobile app) cannot prove it started the flow | Keep the code exchange on your server, never in the app |
| Only `authorization_code` and `refresh_token` grants | No `client_credentials` for machine-to-machine | Use a personal API key of a service account |
| `response_type=token` (implicit) still accepted | Tokens in the query string | Do not use it |
| No revocation or introspection endpoint | A token cannot be revoked by the client | Remove the application, or wait for the token's lifetime; keep `TokenTTL` short |
| Token response has no `token_type` | Strict client libraries may complain | Treat the token as a Bearer token |

## Checklist

- [ ] The application registered with the exact redirect address of each environment.
- [ ] The secret stored on the server side of the application, never in a mobile app or a browser.
- [ ] `oauth/authorize` given only to the roles that should use the application.
- [ ] The code exchange and refresh done server side; tokens kept per user.
- [ ] `401` handled by refreshing, and a failed refresh handled by sending the user through the flow again.
- [ ] HTTPS on the site and on the redirect address.

## Related pages

- [REST API v1 and v2, authentication](../../guides/rest-api-v1-and-v2.md#3-authentication)
- [Choosing credentials for an integration](choosing-credentials-for-an-integration.md)
- [The REST API for integrators](rest-api-for-integrators.md)
- [OAuth and API keys for app creators](../creators/app-creators/oauth-and-api-keys.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
