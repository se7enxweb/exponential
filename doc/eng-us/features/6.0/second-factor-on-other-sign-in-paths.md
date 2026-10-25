# Second step on the other sign-in paths

Two-step sign-in (the extension `sevenx_authentication_2fa`) asks for its second step in the web login form. Four
other ways in check a password on their own: the REST interface with basic authentication, WebDAV, SOAP and the
login of the remote services (expservices). An account for which the site requires a second step signs in there with
its password alone. That stays the default: the clients of these paths cannot answer a second step. Since 6.0.15 the
kernel asks the two-step extension on those paths, and a site can **opt each path in**. On an opted-in path a
password alone is refused for such an account, which then uses an **app password** or the **current code of its
authenticator app** instead.

## In short

- **By default nothing changes.** `sevenxauthentication2fa.ini [OtherSignInPaths] Guard[]` is empty: REST (basic
  authentication, API keys, OAuth), SOAP, WebDAV and the expservices login sign an enforced account in with its
  password alone, exactly as before 6.0.15, and the setup page shows no app passwords.
- A site opts a path in with one line each: `Guard[]=expservices`, `Guard[]=webdav`, `Guard[]=rest`,
  `Guard[]=soap`. Then, for an account that `Enforce2FA` applies to (for example the Administrator users and Editors
  groups), a password alone is refused on that path.
- An **app password** made by the user on the two-step sign-in page (`user2fa/setup`) signs in on the opted-in ones of
  REST, WebDAV and SOAP. It is shown once, stored only as a salted hash, works only on the paths it was made for, and
  the user can revoke it at any time. The page offers only opted-in paths.
- The **expservices login** creates a session, which the website accepts too. App passwords never work there. Once it
  is opted in, the account sends the current code of its authenticator app in the POST field `otp` (or the header
  `X-Exp-OTP`) along with the password.
- On an opted-in REST interface clients can also use a **personal API key** or an **OAuth token**. Neither is a
  password alone: the OAuth authorization code is issued after the web login, with its second step.
- Accounts that `Enforce2FA` does not apply to are unchanged. Exempt accounts (`ExemptGroups[]`,
  `ExemptEmailDomains[]`) are unchanged as well. Console commands never ask.
- Each refusal is recorded in the audit trail (`access.user.second_factor.refused`), and so are making, revoking and
  using an app password.
- It works the same under Apache with PHP-FPM and under Exponential Velocity.

## What each opted-in path answers

On a path that is not in `Guard[]` a password alone signs in as before; the table applies once a path is listed.

| Path | Password alone, account needs a second step | What signs in |
|---|---|---|
| REST, `AuthenticationStyle=ezpRestBasicAuthStyle` | `401` with `WWW-Authenticate: Basic realm="Exponential REST"` and `{"error":"second_factor_required","error_description":"..."}`; `403` with `second_factor_setup_required` while the account has no second step yet | an app password (made for `rest`) as the password; a personal API key (`Authorization: Bearer expk_...`); an OAuth token |
| WebDAV (`webdav.php`) | `401` with the server's `WWW-Authenticate` and the reason in `X-Exp-Sign-In-Refused` | an app password (made for `webdav`) as the password |
| SOAP (`soap.php`) | `401` (`403` for `second_factor_setup_required`) with `WWW-Authenticate: Basic realm="Exponential SOAP"`, `X-Exp-Sign-In-Refused` and a SOAP fault (`faultcode` `SOAP-ENV:Client`, `<detail><reason>`) | an app password (made for `soap`) as the password |
| expservices `expsession::login` | `{"ok":false,"error":{"code":401,"message":"...","reason":"second_factor_required"}}` | username, password and `otp` (the authenticator code) |

The reasons:

- `second_factor_required`: the password was right, a second factor is missing.
- `second_factor_invalid`: a code was sent, but it is wrong, was already used, too many wrong codes were sent (the
  code is then locked for `CodeLockSeconds`), or the path takes no codes.
- `second_factor_setup_required`: the account has no second step yet. It signs in on the website, sets one up and
  then makes an app password.

The OAuth token end point of the REST interface takes authorization codes and refresh tokens only. There is no
password grant that could get around the second step.

## App passwords

Once a site has opted in REST, WebDAV or SOAP, a signed-in user with a working second step finds the section **App
passwords** on `user2fa/setup` (with none of them opted in, the section is not shown):

1. **Make an app password**: give it a name (the client it is for), tick where it may be used (the opted-in ones of
   REST interface, WebDAV and SOAP) and confirm with the current code of the authenticator app. A user with e-mail codes needs no code.
2. The password is shown once, in groups of four (`abcd-efgh-jkmn-pqrs-tuvw-xyz2`). Type it into the client in place
   of the account's password, with the usual username. Upper or lower case and the dashes do not matter.
3. The list shows each app password with its paths, when it was made, when it was last used and when it expires.
   **Revoke** ends it at once.

Further rules:

- An app password belongs to the second step. Removing or resetting the second step revokes all of them. Changing
  from the authenticator app to e-mail codes, or the other way round, keeps them.
- It never signs in through the web login form or the expservices login, and it never makes a session cookie. Each
  request is signed in on its own.
- A disabled account, or one locked by failed sign-ins, is not signed in by an app password.
- It is tried before the password, so a client with an app password never counts as a failed sign-in.
- Only its salted HMAC-SHA-256 is stored, in the account's two-factor field, so no table is added on any engine. It
  has about 119 random bits.

## The code of the authenticator app

On the opted-in paths of `CodeChannels[]` (by default only the expservices login, once `Guard[]=expservices` is set)
the current code counts as the second step:

```bash
curl -s -c jar -d 'username=<user>' --data-urlencode 'password=<password>' -d 'otp=<code>' \
  'https://<site>/ezjscore/call/expsession::login?ContentType=json'
```

Each code works once, here and in the web login form. `[Security] MaxAttempts` wrong codes lock the code for
`CodeLockSeconds` (900 seconds). The account's own count of failed sign-ins cannot do this, because each right
password before a code resets it. REST, WebDAV and SOAP send the credentials with every request, so a code that works
once is a poor fit there. A site can still add them to `CodeChannels[]`, and the header is then `X-Exp-OTP` (`site.ini
[SignInSecondFactorSettings] CodeHeader`).

## Settings

`sevenxauthentication2fa.ini`, in `settings/override` or a siteaccess:

```ini
[OtherSignInPaths]
# the default: no path guarded, every path signs in with the password alone as before
Guard[]
# opt in, one line each:
#Guard[]=expservices
#Guard[]=webdav
#Guard[]=rest
#Guard[]=soap
# also accounts that set up a second step themselves (Enforce2FA does not apply to them)
GuardOptedIn=disabled
CodeChannels[]
CodeChannels[]=expservices
CodeLockSeconds=900

[AppPasswords]
AppPasswords=enabled
# only rest, webdav and soap are possible, and only those also in Guard[] are offered
Channels[]
Channels[]=rest
Channels[]=webdav
Channels[]=soap
MaxPerUser=10
# 0: until revoked
ExpiryDays=365
LastUsedInterval=900
```

The kernel side is `site.ini [SignInSecondFactorSettings]`. `Handlers[]` lists the classes the kernel asks: the
extension adds `sevenxAuthentication2faSignIn` in its own `site.ini.append.php`. `CodeHeader` names the request
header of a code. Without a handler, or with the handler and an empty `Guard[]`, nothing changes.

## For developers

- `expSignInSecondFactor` (`kernel/classes/user/expsigninsecondfactor.php`) is what the paths call: `check()` after
  a right password, `appPasswordUser()` before it, `presentedCode()`, and the answers (`wwwAuthenticate()`,
  `soapFault()`, `sendRefusalHeaders()`; REST: `expRestSecondFactorRequiredStatus`).
- A handler implements `expSignInSecondFactorHandler`: `checkSignIn( eZUser $user, $channel, $code )` returns `null`
  or `array( 'reason' => ..., 'message' => ... )`, and `appPasswordUser( $login, $secret, $channel )` returns the
  account or `null`.
- The paths are the same as those of [Change password at next sign-in](password-change-at-next-sign-in.md). The
  second step is checked first.

## Audit

| Event | When |
|---|---|
| `access.user.second_factor.refused` | a password alone is refused (channel, reason, whether a code was sent) |
| `access.user.app_password.create` | an app password is made (id, name, paths, expiry) |
| `access.user.app_password.revoke` | it is revoked (reason `owner`, `second_step_removed`, `second_step_reset`) |
| `access.user.app_password.use` | it signs in (at most once per `LastUsedInterval`) |

Passwords, codes, hashes and salts are never recorded. The extension also writes its own lines to `var/log/auth.log`.

See also: [Change password at next sign-in](password-change-at-next-sign-in.md), [API keys](../../guides/api-keys.md),
[Audit trail](audit-trail.md), and the extension's `README.md` and `INSTALL.md`.
