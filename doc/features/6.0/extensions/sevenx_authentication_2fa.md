# sevenx_authentication_2fa: two-factor and social login

`sevenx_authentication_2fa` adds **two-factor authentication** and **OAuth social login** to Exponential 6. It was imported on 21 July 2026 (1.0.0) and is built with
minimal dependencies (PHP 8.5.8 compatible, only the `hash` extension for TOTP; `curl` or `allow_url_fopen` for OAuth).

## Features

* **TOTP** (RFC 6238) with PHP built-ins only: Google Authenticator, Authy, Microsoft Authenticator and any RFC-compliant app.
* **E-mail one-time code** when TOTP is unavailable or is the chosen method, sent with `eZMail`, with a **resumable link** `/user2fa/verify/code/<code>` that
  completes the login when clicked, even in another browser.
* A **datatype** `sevenxauthentication2fa` on the `user` class so users configure their own method and secret, a **login handler** `sevenxUser2fa` that validates the
  password and then completes the login or redirects to the challenge, a **reset 2FA** interface, a back button, and templates for user edit, registration, and the
  forced setup case (a user without a configured method who must set one up before using the site).
* **Social login handlers** (skeletons ready to customise) for Google, Facebook, Twitter/X, Instagram, Meta and ID.me, a base class, and a command line builder for more
  providers.
* **No Redis or Valkey needed**: pending challenges are JSON files in `var/site/cache/sevenx_2fa_pending/`, keyed by user id and code hash, removed on success and by
  the cleanup script.

## Set it up

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=sevenx_authentication_2fa
[UserSettings]
ExtensionDirectory[]=sevenx_authentication_2fa
LoginHandler[user2fa]=sevenxUser2fa
LoginHandler[]=standard
```

The standard handler logs the user in before the challenge can run, so it must be listed **after** `sevenxUser2fa`; remove or comment a `LoginHandler[]=standard` in
`settings/site.ini` so the override order takes effect. Then regenerate autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and clear caches.

### Role policies

Module `user2fa` has four functions: `setup`, `verify`, `oauth` and `callback`. Grant them through **Roles and policies** only:

| View | Grant |
|---|---|
| `/user2fa/oauth/<provider>` | `user2fa/oauth` to the **Anonymous** role |
| `/user2fa/callback/<provider>` | `user2fa/callback` to the **Anonymous** role |
| `/user2fa/verify`, `/user2fa/verify/code/<code>` | `user2fa/verify` to **Anonymous** and to every role that uses 2FA |
| `/user2fa/setup` | `user2fa/setup` to the roles that manage their own 2FA (Member, Editor, Partner, Administrator, custom roles) |

The views check their own state: `verify` processes a request only when a valid pending challenge exists.

## Settings (`sevenxauthentication2fa.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| General | `Enabled` | `enabled` | |
| General | `Issuer` | `Exponential` | Name shown in the authenticator app |
| General | `DefaultMethod` | `disabled` | `totp`, `email` or `disabled` |
| General | `Enforce2FA` | `disabled` | Force users without a method to set one up |
| General | `AllowEmailFallback` | `enabled` | |
| CodeSettings | `Length`, `TimeStep`, `Window`, `Algorithm` | `6`, `30`, `1`, `SHA1` | TOTP parameters |
| CodeSettings | `EmailTTL` | `600` | Seconds an e-mail code is valid |
| EmailSettings | `Subject`, `Sender` | `Your login verification code is {code}`, empty | |
| EmailSettings | `Body` | empty | Optional body with `{code}`, `{expires}`, `{site_url}`, `{verify_url}`; empty uses the template |
| TOTPSettings | `SecretLength` | `20` | |
| SocialLogin | `Enabled`, `AutoCreateUser` | `disabled`, `disabled` | |
| SocialLogin | `DefaultUserGroupNodeID`, `AuthenticationMatch` | `12`, `email` | Group of auto-created users; match accounts by e-mail |
| `<Provider>` (Google, Facebook, TwitterX, Instagram, ...) | `Enabled`, `ClientID`, `ClientSecret`, `Scope`, `AuthorizationURL`, `TokenURL`, `UserInfoURL` | disabled, empty | One block per provider |

The e-mail body is the template `design/standard/templates/mail/2fa_code.tpl` (variables `$code`, `$expires`, `$site_url`, `$verify_url`); copy it into your site design
to brand it. Keep client secrets in `settings/override/`, never in the extension's own settings file under version control.

## Use it

* Users open `/user2fa/setup` to enable TOTP or e-mail 2FA. The page shows the secret, an `otpauth://` URI and a QR code (the QR image is generated through the Google
  Chart API; if you do not want an external service, use the URI or the plain secret).
* Social login: create an OAuth application with the provider, fill the provider block, set `[SocialLogin] Enabled=enabled` in `sevenxauthentication2fa.ini` (the master switch, default `disabled`), and link to `/user2fa/oauth/<provider>` (the provider returns to `/user2fa/callback/<provider>`). The other views of the module are `user2fa/setup` and `user2fa/verify`, each with its own policy function (`setup`, `verify`, `oauth`, `callback`).
* Add a provider: `php extension/sevenx_authentication_2fa/bin/php/sevenx2fabuild.php --provider=MyProvider --client-id=... --client-secret=... --authorization-url=... --token-url=... --userinfo-url=... --scope="email profile"`
  creates the handler and appends its ini block; then edit `normalizeUserInfo()`.
* Set a provider's credentials without editing the ini: `php extension/sevenx_authentication_2fa/bin/php/sevenx2faconfig.php --provider=Google --siteaccess=<siteaccess> --client-id=... --client-secret=...` writes the matching `sevenxauthentication2fa.ini.append.php` in `settings/override` (or in the siteaccess folder) and clears the INI cache. It changes settings: run it only when you mean to.
* Cleanup: `php extension/sevenx_authentication_2fa/bin/php/sevenx2facleanup.php` (also a cronjob script) removes expired challenges.

## Related

* [Chronicle](../../../history/extensions/sevenx_authentication_2fa.md) and [release notes](../../../changelogs/extensions/sevenx_authentication_2fa.md)
