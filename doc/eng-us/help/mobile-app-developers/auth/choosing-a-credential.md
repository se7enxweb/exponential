---
title: Choosing a credential
audience: mobile-app-developer, app-creator, developer, project-manager
category: mobile-apps
order: 210
summary: Pick the right credential for each kind of app on Exponential - a public reader app, an app whose users sign in, an internal staff app, a kiosk, a development build - with the reasons, the accounts to create and what never to do.
keywords: credential, choose, OAuth2, API key, expservices token, public app, reader app, staff app, kiosk, integration user, anonymous, back end, mobile app
type: help
---
# Choosing a credential

This page is for app developers and technical leads who decide how an app authenticates. The right choice depends on
whom the app acts for, and on whether a secret can be kept where the code runs.

## Contents

1. [The rule](#the-rule)
2. [By kind of app](#by-kind-of-app)
3. [Accounts to create](#accounts-to-create)
4. [Never](#never)

## The rule

A credential is a user. Whatever an app sends, the server runs the call as one user account and checks that
account's policies ([How Exponential content reaches an app](../how-exponential-content-reaches-an-app.md#what-decides-what-you-see)).
So the question is not "which token format" but "which user should this call run as, and who can see the secret".

## By kind of app

| App | Runs as | Credential | Where the secret lives |
|---|---|---|---|
| Public reader app (news, catalog), no sign-in | one read-only integration user | a personal API key or OAuth token of that user, **held by your back end**, which calls Exponential for the app | your server |
| App whose users sign in with their Exponential account | each user | OAuth2 authorization code flow through your back end | client secret on your server; tokens on the device or on your server |
| Internal staff app on managed devices | each staff member | OAuth2, or expservices tokens made at first sign-in | device secure storage |
| Kiosk or display screen | one read-only user for that screen | a personal API key with the `read` scope, one per device | the device, managed by IT |
| Development and tests | a test user | a personal API key on a test installation | environment variables, a CI secret |

The first row deserves explaining: a published app is public code. Anything bundled in it, a key, a token or a client
secret, can be extracted. A read-only user limits the damage but does not remove it; a back end that holds the
credential, adds caching and can be changed without an app release is the robust design
([A back end for your app](a-backend-for-your-app.md)).

## Accounts to create

- **One integration user per app**, never a person's account and never the administrator.
- **A role per app** with only the policies it needs: `content/read` limited to the sections or subtrees the app shows,
  and write policies only below the folder the app writes to ([Permissions for app users](../publishing-workflow/permissions-for-app-users.md)).
- **One credential per installation of a tool**, so one can be revoked alone
  ([OAuth and API keys: good practice](../../creators/app-creators/oauth-and-api-keys.md#good-practice)).

## Never

- Never ship a personal API key, an expservices token or an OAuth client secret inside an app.
- Never use the administrator account, or any account with `setup` rights, for an app.
- Never send a credential in a URL: an API key in the query string is refused even when valid, and URLs end up in logs.
- Never ask users for their Exponential password inside a native app screen and store it; if your app signs in to
  the remote services with a password, send it once and keep only the token it makes
  ([expservices tokens in apps](expservices-tokens-in-apps.md)).

## Related pages

- [OAuth2 authorization code flow](oauth2-authorization-code-flow.md)
- [Personal API keys for tools](personal-api-keys-for-tools.md)
- [OAuth and API keys: which credential](../../creators/app-creators/oauth-and-api-keys.md#which-credential)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
