---
title: Authentication, sessions and two-factor sign-in
audience: software-engineer, admin, developer
category: software-engineering
order: 720
summary: How users and machines authenticate to Exponential - password storage and policy, the list of well-known passwords, lockout, session handlers and cookies, two-factor sign-in with sevenx_authentication_2fa, OAuth 2.0 and personal API keys for the REST API - and the engineering decisions each one involves.
keywords: authentication, sign-in, login, password_hash, MinPasswordLength, weak passwords, lockout, sessions, ezpSessionHandlerDB, cookies, SameSite, HttpOnly, two-factor, 2FA, TOTP, sevenx_authentication_2fa, OAuth, API keys, software engineer
type: help
---
# Authentication, sessions and two-factor sign-in

This page is for engineers who design sign-in for a site, integrate machines through the API, or review account
security. It covers people (passwords, two factors, sessions) and machines (OAuth tokens, API keys).

## Contents

1. [Passwords](#passwords)
2. [Sessions](#sessions)
3. [Two-factor sign-in](#two-factor-sign-in)
4. [Machines: OAuth and API keys](#machines-oauth-and-api-keys)
5. [Login handlers](#login-handlers)
6. [Decisions to make](#decisions-to-make)

## Passwords

| Aspect | Shipped behavior | Setting or source |
|---|---|---|
| Storage | PHP's `password_hash()` (bcrypt today), older hashes upgraded at sign-in | `site.ini [UserSettings] HashType=php_default` |
| Minimum length | 10 | `[UserSettings] MinPasswordLength` |
| Well-known passwords | refused everywhere a password is set, with simple variants | `expWeakPasswordList` ([Weak passwords and reset key lifetime](../../bc/6.0/weak-passwords-and-reset-key-lifetime.md)) |
| Forgot-password link | expires | the same page |
| Lockout after failed sign-ins | off (`0`) until you set a number | `[UserSettings] MaxNumberOfFailedLogin` |
| Other sessions on password change | ended | `[PasswordSettings] EndOtherSessions=enabled` |

Unknown logins and wrong passwords cost the same time, and "forgot password" answers are uniform, so an attacker
cannot list accounts ([Security standards](../../standards/security.md)). Since August 2026 an account without an
`ezuser_setting` row cannot sign in: the login fails closed.

## Sessions

- `site.ini [Session] SessionTimeout` (default three days) ends a session after its last use.
- `[Session] Handler` empty (the default) keeps sessions in PHP's own storage; `ezpSessionHandlerDB` keeps them in the
  `ezsession` table, which several web nodes can share and the administration can list and end
  ([Sessions](../../guides/sessions.md)).
- Session cookies are `HttpOnly` and `SameSite` by default since September 2026, and `Secure` over HTTPS
  (`CookieSecure=auto`); the session id is regenerated at sign-in.
- Under Velocity, `$_SESSION` is written and emptied after each request.

## Two-factor sign-in

The extension `sevenx_authentication_2fa`, required by `composer.json` and activated by the installers, adds a second
step after the password: a TOTP code from an authenticator app (RFC 6238, PHP built-ins only) or a one-time code by
e-mail. A datatype on the user class stores each user's method and secret; a login handler checks the password and
then completes the sign-in or redirects to the challenge. Pending challenges are JSON files under `var/`, so it needs
no Redis or Valkey. The same extension carries social-login handlers as skeletons to customize
([Two-step sign-in](../../guides/two-factor-login.md), [sevenx_authentication_2fa](../../features/6.0/extensions/sevenx_authentication_2fa.md)).

## Machines: OAuth and API keys

| Mechanism | Use |
|---|---|
| OAuth 2.0 tokens | `POST /api/auth/v1/oauth/token` with `authorization_code` or `refresh_token`, for applications acting for a user ([OAuth and API keys](../creators/app-creators/oauth-and-api-keys.md), [OAuth authorize policy](../../bc/6.0/oauth-authorize-policy.md)) |
| Personal API keys | `Authorization: Bearer expk_...`; made by a user with `apikey/create`, scoped, expiring, revocable, shown once ([Personal API keys](../../guides/api-keys.md)) |
| `expservices` tokens | remote services and apps ([Remote services and apps](../../guides/remote-services-and-apps.md)) |

Whatever authenticates a REST request, every read and write checks the user's policies
([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)).

## Login handlers

`site.ini [UserSettings] LoginHandler[]` lists the handlers tried in order; the two-factor extension adds its own
before `standard`. Directory services (LDAP and others) plug in the same way ([API and protocol standards](../../standards/apis-and-protocols.md)).

## Decisions to make

- [ ] Is two-factor sign-in required for administrators and editors?
- [ ] Is a lockout number set, and is `TrustedProxies[]` right so it counts the real client addresses?
- [ ] Which session handler: PHP's storage on one node, or the database for several nodes and session control?
- [ ] Which machines get API keys, with which scopes and lifetimes, and who rotates them?
- [ ] Are sign-in events watched (the audit trail's `access.session.*` events and the brute-force alert rule)?

## Related pages

- [Security engineering overview](security-engineering-overview.md)
- [eZUser and access](../extension-development/part-8-reference/ezuser-and-access.md)
- [User hygiene](../../features/6.0/user-hygiene.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
