---
title: Storing secrets on devices
audience: mobile-app-developer, developer
category: mobile-apps
order: 260
summary: Keep Exponential credentials safe on phones and in browsers - what may be stored on a device at all, the platform secure stores (Keychain, Keystore), what never to store, logging and crash reports, backups, sign-out and revocation.
keywords: secrets, secure storage, Keychain, Keystore, EncryptedSharedPreferences, token storage, refresh token, logging, crash reports, backups, sign out, revoke, browser storage, mobile app, security
type: help
---
# Storing secrets on devices

This page is for app developers who decide where tokens live in the app. It combines what the Exponential sources say
about credentials with general platform practice; the platform names are pointers, not instructions for one SDK
version.

## Contents

1. [What may be on a device](#what-may-be-on-a-device)
2. [Where to put it](#where-to-put-it)
3. [Keeping it out of logs](#keeping-it-out-of-logs)
4. [Signing out](#signing-out)
5. [Browsers](#browsers)

## What may be on a device

| Secret | On the device? |
|---|---|
| OAuth client secret | **never**: it identifies your app to the server, and the token endpoint needs it ([A back end for your app](a-backend-for-your-app.md)) |
| A personal API key (`expk_`) of a person or an integration user | **never** in a published app; only on managed kiosks by decision of the site owner |
| An OAuth access token of the signed-in user | acceptable, in secure storage; it lives `TokenTTL` seconds (one hour by default) |
| An OAuth refresh token | prefer to keep it on your back end; if on the device, in secure storage |
| An expservices token (`expt_`) of the signed-in user, one per device | acceptable, in secure storage ([expservices tokens in apps](expservices-tokens-in-apps.md)) |
| The user's password | **never** stored; send it once to make a token, then forget it |

The sources are explicit: "Store the token in your platform's secret store (keychain, keystore, a CI secret), never in
source code or a URL" ([Remote services and apps](../../../guides/remote-services-and-apps.md#5-a-personal-api-token-for-scripts-and-apps-3-minutes)),
and the expservices reference names Keychain, Keystore, KWallet and libsecret
([Authentication with an API token](../../../bc/6.0/backend_ezjscore_services.md#authentication-with-an-api-token)).

## Where to put it

| Platform | Store |
|---|---|
| iOS, iPadOS, macOS | the Keychain, with an accessibility class that fits the app (for example available after first unlock, this device only) |
| Android | the Android Keystore, directly or through an encrypted preferences library that uses it |
| Cross-platform frameworks | a secure storage plugin that maps to Keychain and Keystore; check that it really does on both |
| Desktop Linux | libsecret or KWallet |

Not in plain preferences, files, SQLite tables, or app resources.

## Keeping it out of logs

- Remove the `Authorization` and `X-Exp-Token` headers from HTTP logging interceptors, also in debug builds that
  testers use.
- Never put a credential in a URL: it ends up in server logs, proxy logs and analytics. REST refuses a personal API
  key in the query string even when it is valid.
- Scrub tokens from crash reports and analytics events.
- Every key starts with `expk_` and every expservices token with `expt_`: add both prefixes to your secret scanners
  for the app repository.

## Signing out

- Delete the stored tokens from secure storage.
- Revoke server-side where you can: `expsession::tokenRevoke` for an expservices token. For OAuth, drop the tokens and let
  the access token expire; removing the whole application in **Setup > oAuth admin** also removes its authorizations,
  tokens and codes, for every user.
- Exclude secure storage from device backups if your platform's default would include it, so a restored backup does
  not resurrect a session on another device.

## Browsers

A web app has no secure store comparable to Keychain or Keystore. Prefer a back end on the same origin that keeps
tokens server-side and gives the browser a session cookie of its own
([Web apps, CORS and CSP](../offline-and-performance/web-apps-cors-and-csp.md)). Do not put long-lived tokens in
`localStorage`.

## Related pages

- [Security checklist](../checklists/security-checklist.md)
- [Choosing a credential](choosing-a-credential.md)
- [Security for applications](../../creators/app-creators/security-for-applications.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
