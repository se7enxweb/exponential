# sevenx_authentication_2fa: two-factor and social login

This page is for administrators who want stronger sign-in, and for site builders who offer "sign in with Google" and
similar. `sevenx_authentication_2fa` adds **two-factor authentication** and **OAuth social login** to Exponential 6. It
was imported on 21 July 2026 (1.0.0) and has minimal dependencies: it is PHP 8.5.8 compatible, needs only the `hash`
extension for TOTP, and `curl` or `allow_url_fopen` for OAuth.

## What it offers

- **TOTP** (RFC 6238) with PHP built-ins only: Google Authenticator, Authy, Microsoft Authenticator and any
  RFC-compliant app.
- **E-mail one-time code** when TOTP is unavailable or is the chosen method, sent with `eZMail`, with a **resumable
  link** `/user2fa/verify/code/<code>` that completes the login when clicked, even in another browser.
- A **datatype** `sevenxauthentication2fa` on the `user` class, so users configure their own method and secret.
- A **login handler** `sevenxUser2fa` that checks the password and then completes the login or redirects to the
  challenge.
- A **reset 2FA** interface, a back button, and templates for user edit, registration and the forced setup case (a
  user without a configured method who must set one up before using the site).
- **Social login handlers** (skeletons ready to customise) for Google, Facebook, Twitter/X, Instagram, Meta and ID.me,
  a base class, and a command line builder for more providers.
- **No Redis or Valkey needed**: pending challenges are JSON files in `var/site/cache/sevenx_2fa_pending/`, keyed by
  user id and code hash, removed on success and by the cleanup script.

## Set it up

1. Activate the extension and its login handler in `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=sevenx_authentication_2fa
[UserSettings]
ExtensionDirectory[]=sevenx_authentication_2fa
LoginHandler[user2fa]=sevenxUser2fa
LoginHandler[]=standard
```

   The standard handler logs the user in before the challenge can run, so it must be listed **after**
   `sevenxUser2fa`. Remove or comment a `LoginHandler[]=standard` in `settings/site.ini` so the override order takes
   effect.

2. Regenerate autoloads and clear the caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

3. Grant the policies (below).
4. Sign in as a test user and open `/user2fa/setup` to enable TOTP or e-mail.

### Role policies

Module `user2fa` has four functions: `setup`, `verify`, `oauth` and `callback`. Grant them through **Roles and
policies** only:

| View | Grant |
|---|---|
| `/user2fa/oauth/<provider>` | `user2fa/oauth` to the **Anonymous** role |
| `/user2fa/callback/<provider>` | `user2fa/callback` to the **Anonymous** role |
| `/user2fa/verify`, `/user2fa/verify/code/<code>` | `user2fa/verify` to **Anonymous** and to every role that uses 2FA |
| `/user2fa/setup` | `user2fa/setup` to the roles that manage their own 2FA (Member, Editor, Partner, Administrator, custom roles) |

The views check their own state: `verify` processes a request only when a valid pending challenge exists.

## Use it

- **Two-factor:** users open `/user2fa/setup` to enable TOTP or e-mail 2FA. The page shows the secret, an
  `otpauth://` URI and a QR code. The QR image is generated through the Google Chart API; if you do not want an external
  service, use the URI or the plain secret.
- **Social login:** create an OAuth application with the provider, fill the provider block, set
  `[SocialLogin] Enabled=enabled` in `sevenxauthentication2fa.ini` (the master switch, default `disabled`), and link to
  `/user2fa/oauth/<provider>`. The provider returns to `/user2fa/callback/<provider>`.
- **Add a provider:** this creates the handler and appends its INI block; then edit `normalizeUserInfo()`:

```bash
php extension/sevenx_authentication_2fa/bin/php/sevenx2fabuild.php --provider=MyProvider --client-id=... --client-secret=... --authorization-url=... --token-url=... --userinfo-url=... --scope="email profile"
```

- **Set a provider's credentials without editing the INI file:** this writes the matching
  `sevenxauthentication2fa.ini.append.php` in `settings/override` (or in the siteaccess folder) and clears the INI
  cache. It changes settings: run it only when you mean to.

```bash
php extension/sevenx_authentication_2fa/bin/php/sevenx2faconfig.php --provider=Google --siteaccess=<siteaccess> --client-id=... --client-secret=...
```

- **Cleanup:** removes expired challenges (also usable as a cronjob script):

```bash
php extension/sevenx_authentication_2fa/bin/php/sevenx2facleanup.php
```

## Settings

All keys are in `sevenxauthentication2fa.ini`.

| Block | Key | Default | Meaning |
|---|---|---|---|
| `General` | `Enabled` | `enabled` | Two-factor on or off |
| `General` | `Issuer` | `Exponential` | Name shown in the authenticator app |
| `General` | `DefaultMethod` | `disabled` | `totp`, `email` or `disabled` |
| `General` | `Enforce2FA` | `disabled` | Force users without a method to set one up |
| `General` | `AllowEmailFallback` | `enabled` | Allow the e-mail code as fallback |
| `CodeSettings` | `Length`, `TimeStep`, `Window`, `Algorithm` | `6`, `30`, `1`, `SHA1` | TOTP parameters |
| `CodeSettings` | `EmailTTL` | `600` | Seconds an e-mail code is valid |
| `EmailSettings` | `Subject`, `Sender` | `Your login verification code is {code}`, empty | Mail subject and sender |
| `EmailSettings` | `Body` | empty | Optional body with `{code}`, `{expires}`, `{site_url}`, `{verify_url}`; empty uses the template |
| `TOTPSettings` | `SecretLength` | `20` | Length of the secret |
| `SocialLogin` | `Enabled`, `AutoCreateUser` | `disabled`, `disabled` | Social login master switch; create unknown users |
| `SocialLogin` | `DefaultUserGroupNodeID`, `AuthenticationMatch` | `12`, `email` | Group of auto-created users; match accounts by e-mail |
| `<Provider>` (Google, Facebook, TwitterX, Instagram, ...) | `Enabled`, `ClientID`, `ClientSecret`, `Scope`, `AuthorizationURL`, `TokenURL`, `UserInfoURL` | disabled, empty | One block per provider |

The e-mail body is the template `design/standard/templates/mail/2fa_code.tpl` (variables `$code`, `$expires`,
`$site_url`, `$verify_url`); copy it into your site design to brand it. Keep client secrets in `settings/override/`,
never in the extension's own settings file under version control.

## Related pages

- [Reset a user's password](../reset-user-password.md)
- [Audit trail](../audit-trail.md)
- [Chronicle](../../../history/extensions/sevenx_authentication_2fa.md) and [release notes](../../../changelogs/extensions/sevenx_authentication_2fa.md)
- [Change ledger](../../../history/ledger/sevenx_authentication_2fa.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Month: 2026-07 (all extensions)](../../../history/extensions/months/2026-07.md)
