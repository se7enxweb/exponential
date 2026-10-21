---
title: A back end for your app
audience: mobile-app-developer, app-creator, developer, project-manager
category: mobile-apps
order: 230
summary: Why most mobile apps on Exponential should talk to a small back end of their own (a backend for frontend) rather than to Exponential directly - the client secret, read-only credentials, caching, shaping answers and changing behavior without an app release - and what such a back end does, step by step.
keywords: back end, backend for frontend, BFF, proxy, client secret, OAuth, token exchange, caching, API key, server, mobile app, architecture
type: help
---
# A back end for your app

This page is for app developers and technical leads who design the architecture of an app on Exponential. It explains
why a small server between the app and Exponential is usually the right design, and what that server does. It is
general architecture advice; Exponential does not ship such a server.

## Contents

1. [Why a back end](#why-a-back-end)
2. [What it does](#what-it-does)
3. [The sign-in flow through the back end](#the-sign-in-flow-through-the-back-end)
4. [A public app without sign-in](#a-public-app-without-sign-in)
5. [When you can skip it](#when-you-can-skip-it)

## Why a back end

| Reason | Detail |
|---|---|
| The OAuth token endpoint needs the client secret | `client_secret` is required for both grants ([OAuth2 authorization code flow](oauth2-authorization-code-flow.md#limits-of-the-implementation)); a secret in an app is not secret |
| Read-only credentials belong on a server | a public reader app would otherwise ship a key that anyone can extract |
| Answers can be shaped | combine a REST list with image alias URLs from `expimage::url` in one answer for one screen |
| Caching | one cache in front of Exponential serves every phone ([Caching on the server](../offline-and-performance/caching-on-the-server.md)) |
| Changes without an app release | a new field, a new folder or a workaround for a gap goes live at once |
| CORS for web apps | the back end can sit on the same origin as the web app ([Web apps, CORS and CSP](../offline-and-performance/web-apps-cors-and-csp.md)) |

## What it does

- Holds the OAuth client secret, or the read-only integration credential, in its own secret store
  ([Environment settings and secrets](../../creators/app-creators/environment-settings-and-secrets.md) describes the
  same discipline on the Exponential side).
- Exposes a small API of its own, designed for the app's screens.
- Calls Exponential's REST API and remote services with the credential, passes errors on as clear statuses, and
  caches what may be cached.
- Authenticates the app's users if the app has its own accounts, or passes through the user's OAuth token.

It can be written in any language and hosted anywhere that reaches the Exponential installation over HTTPS. It can
also live inside the Exponential installation as an extension with an ezjscore server function or a REST provider;
that is application creator work ([JSON endpoints with ezjscore](../../creators/app-creators/json-endpoints-with-ezjscore.md),
[Your own REST provider](../../creators/app-creators/rest-api.md#your-own-rest-provider)).

## The sign-in flow through the back end

1. The app opens the authorize page in the system browser, with the back end's address as `redirect_uri`
   (registered exactly in **Setup > oAuth admin**).
2. Before that, the back end gives the app a one-time value tied to the app's session; the authorize page does not
   return `state`, so the back end must correlate the callback with the session that started it by other means, for
   example a short-lived cookie set in the same browser session.
3. The site redirects the browser to the back end with `code`. The back end exchanges it at
   `POST /api/auth/v1/oauth/token` with the secret.
4. The back end hands the app a session of its own (or the access token, if you accept tokens on devices), and keeps
   the refresh token server-side.
5. When the access token expires, the back end refreshes it; the app never sees the secret.

The redirect back from the back end into the app (a universal link or app link of your app) is ordinary mobile
platform work, not part of Exponential.

## A public app without sign-in

1. Create an integration user with a role that may only read what the app shows.
2. Give the back end a credential of that user (a personal API key with the `read` scope works and is easy to rotate).
3. The back end calls `GET /api/ezp/v2/...`, caches the answers for a short time, and serves the app.
4. The app has no Exponential credential at all.

## When you can skip it

- Internal apps on managed devices, where each user signs in and the risk of an extracted key is accepted in writing.
- A kiosk with its own read-only key, managed by IT.
- Prototypes and tests against a test installation.

Even then, plan where the back end would go; adding it later changes the app's network layer.

## Related pages

- [Choosing a credential](choosing-a-credential.md)
- [OAuth and API keys](../../creators/app-creators/oauth-and-api-keys.md)
- [App backend reliability (for businesses)](../../businesses/mobile-and-headless/app-backend-reliability.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
