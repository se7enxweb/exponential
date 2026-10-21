---
title: expservices tokens in apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 250
summary: Authenticate an app against the expservices remote services - session sign-in with expsession::login, making a personal token (expt_) with expsession::tokenCreate, sending it as Bearer and X-Exp-Token, writes without a form token, listing and revoking tokens, and what a 401 means.
keywords: expservices, token, expt, expsession, login, tokenCreate, tokenList, tokenRevoke, whoami, X-Exp-Token, Bearer, session cookie, form token, 401, mobile app
type: help
---
# expservices tokens in apps

This page is for app developers whose app calls the remote services (`expservices`), for example for search, image
alias URLs or updates. The remote services have their own personal tokens, separate from REST keys and OAuth
tokens. Sources: [Remote services and apps, section 5](../../../guides/remote-services-and-apps.md#5-a-personal-api-token-for-scripts-and-apps-3-minutes)
and the [expservices reference](../../../bc/6.0/backend_ezjscore_services.md#authentication-with-an-api-token).

## Contents

1. [Three ways to call](#three-ways-to-call)
2. [Signing in with a session](#signing-in-with-a-session)
3. [Making a token](#making-a-token)
4. [Sending the token](#sending-the-token)
5. [Managing tokens](#managing-tokens)
6. [When the token dies](#when-the-token-dies)

## Three ways to call

| Way | Who | For |
|---|---|---|
| Anonymous | nobody signed in | services whose access is `public` |
| Session cookie | after `expsession::login` | the first sign-in; browsers on the same site |
| Personal token `expt_...` | the token's user | apps and scripts: no cookie, no form token |

## Signing in with a session

```bash
curl -c jar -d 'username=<login>' --data-urlencode 'password=<password>' \
     'https://example.com/ezjscore/call/expsession::login?ContentType=json'
curl -b jar 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

The answer of `login` sets the session cookie and carries the new session's form token (`data.token`). A native app
keeps the cookie in a cookie store for the next step only.

## Making a token

A token can be made only by a session sign-in, not by another token:

```bash
curl -b jar -H 'X-CSRF-Token: <form token>' --data-urlencode 'name=Android app on Pixel 8' \
     -d expires_in_days=90 'https://example.com/ezjscore/call/expsession::tokenCreate?ContentType=json'
```

`expires_in_days` is 1 to 365, default 90. The answer carries the token once, in `data.token`: `expt_` followed by 48
hex characters. Only its SHA-256 hash is stored. Put the token in the device's secure storage at once
([Storing secrets on devices](storing-secrets-on-devices.md)), then end the session with `expsession::logout` and
forget the password: the app does not need it again until the token dies.

If `tokenCreate` answers a database error, the installation lacks the table `expservices_token`; that is a task for
its administrator ([Remote services and apps](../../../guides/remote-services-and-apps.md#5-a-personal-api-token-for-scripts-and-apps-3-minutes)).

## Sending the token

```text
Authorization: Bearer expt_...
X-Exp-Token: expt_...
```

Send both, as the shipped clients do: some proxies and FastCGI setups do not pass `Authorization` to PHP, and
`X-Exp-Token` was the header that worked on the reference installation
([The Authorization header and proxies](authorization-header-and-proxies.md)). The token signs the request in as its
user for that one request; no session is created. Writes still need `POST` but no form token.

## Managing tokens

| Call | Does |
|---|---|
| `expsession::tokenList` | the caller's tokens: id, name, hint, created, last used, expiry, revoked; never the token |
| `expsession::tokenRevoke` (`POST id`) | revokes one of the caller's tokens |

Give every device its own token with a name that says which device, and offer a "sign out" in the app that revokes
the token with `tokenRevoke`. Make, revoke and refusals are in the audit as `access.expservices.token.create`,
`.revoke` and `.failed`.

## When the token dies

An unknown, revoked or expired token, or one whose user is disabled, answers `401` on every service, the public ones
included, so the app notices instead of silently becoming anonymous. On `401`, delete the stored token and send the
user to sign in again.

## Related pages

- [Calling expservices from an app](../json-endpoints/calling-expservices-from-an-app.md)
- [Choosing a credential](choosing-a-credential.md)
- [Remote services with expservices](../../creators/app-creators/remote-services-expservices.md#authentication)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
