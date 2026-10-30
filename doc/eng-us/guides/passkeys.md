# Passkeys: signing in with a fingerprint, a face or a security key

This guide explains the passkeys of Exponential 6.0.15. A passkey replaces the password at sign-in: the browser or
the device asks for a fingerprint, a face, the screen lock PIN or a touch of a security key, and the site checks a
signature made with a key that never leaves the device. The site keeps only the public half of that key, so nothing
stored on the server can be used to sign in.

It is written for two readers. **Users** read sections 1 to 4. **Administrators** read sections 5 to 12.

[Guides](README.md) · Related: [Verify it's you](verify-its-you.md) · [Two-step sign-in](two-factor-login.md) ·
[Security and audit](security-and-audit.md) · Change note:
[Passkeys and "Verify it's you"](../bc/6.0/passkeys-and-verify-its-you.md)

## In short

- **My account > Passkeys** (`/user/passkeys`): add a passkey (the browser's passkey dialog, then a name of your
  choice), see which authenticator holds each one ("iCloud Keychain", "Google Password Manager", "YubiKey 5
  Series"), whether it is synced or bound to one device, when it was added and last used; rename and remove them.
- The sign-in form has **Sign in with a passkey** next to the password. Where the browser offers it, the username
  field also lists your passkeys (passkey autofill), and choosing one signs you in.
- A passkey also answers the second step of two-step sign-in and is half of "Verify it's you" (the password and a
  passkey).
- A policy per site, user group or account makes passkeys **optional**, **preferred** (suggested once per session)
  or **required**, and can turn the password sign-in off for accounts that have a passkey.
- Administrators see and revoke the passkeys of other accounts (`/user/passkeys/<user id>`, policy
  `user/passkeys_manage`).
- You get an e-mail when a passkey is added to or removed from your account.
- Passkeys are used on the web pages only. The REST interface, SOAP, WebDAV and the `expservices` login sign in as
  before, and the passkey policy never applies to them.

## Contents

1. [What a passkey is](#1-what-a-passkey-is)
2. [Adding, naming and removing passkeys](#2-adding-naming-and-removing-passkeys)
3. [Signing in with a passkey](#3-signing-in-with-a-passkey)
4. [A lost or replaced device](#4-a-lost-or-replaced-device)
5. [Settings](#5-settings)
6. [Policy: optional, preferred, required](#6-policy-optional-preferred-required)
7. [Another account's passkeys](#7-another-accounts-passkeys)
8. [The table and the upgrade](#8-the-table-and-the-upgrade)
9. [Two-step sign-in and the other sign-in paths](#9-two-step-sign-in-and-the-other-sign-in-paths)
10. [How a passkey is checked](#10-how-a-passkey-is-checked)
11. [Audit and e-mail alerts](#11-audit-and-e-mail-alerts)
12. [For extensions](#12-for-extensions)

---

## 1. What a passkey is

A passkey is a key pair made by your device (a phone, a laptop, a password manager or a hardware security key) for
this site only. The private key stays on the device; many password managers sync it between your devices ("Synced"
in the list). The site stores the public key and checks every sign-in against it. A passkey cannot be phished: the
browser uses it only on the site it was made for.

## 2. Adding, naming and removing passkeys

1. Open **My account** and the card **Passkeys**, or go to `/user/passkeys`.
2. The page first asks you to confirm it is you (see [Verify it's you](verify-its-you.md)).
3. Under **Add a passkey**, type a name you will recognize later (for example "Work laptop" or "Password manager");
   without one the passkey is called "Passkey of <date>".
4. Choose **Add a passkey** and follow the browser: pick where to store it and confirm with your fingerprint, face,
   PIN or key.

The list shows each passkey's name, the authenticator that holds it (when the browser told the site which one it is),
when it was added and last used, whether it is synced or bound to one device or key, and its algorithm (ES256,
RS256 ...). **Rename** changes the name; **Remove** asks once more and then deletes the passkey here. Remove it from
your device or password manager as well. An account has at most 20 passkeys (`MaxPerUser`).

Your last passkey cannot be removed while your account could not sign in without it: when it has no password, when
the site turned the password sign-in off for you, or when your account must have a passkey. Add another passkey
first. Whenever a passkey is added to or removed from your account you get an e-mail, so a passkey someone else added
does not go unnoticed.

If the browser cannot make passkeys, the button stays hidden and the page says so; your password keeps working.

## 3. Signing in with a passkey

On the sign-in form choose **Sign in with a passkey** and confirm on your device. Where the browser supports passkey
autofill, click into the username field: your passkeys for the site appear there, and choosing one signs you in
without typing anything. "Remember me" applies as with the password.

A passkey is refused when the account is disabled or locked after failed sign-ins, when the account may not use the
siteaccess, or when the passkey was removed. The form then says why.

## 4. A lost or replaced device

Sign in with your password or another passkey, open **My account > Passkeys** and remove the passkey of the lost
device. A passkey alone is worthless without the device's screen lock or biometrics, but removing it is the only way
to make sure it can never sign in again. If you cannot sign in any more, ask an administrator: they can revoke the
passkey (section 7) and set a new password.

## 5. Settings

`site.ini [PasskeySettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `Passkeys` | `enabled` | `disabled`: no passkey can be added or used; the stored ones stay |
| `SignInForm` | `enabled` | Offer the passkey button and autofill on the sign-in form |
| `RelyingPartyID` | empty | The host name passkeys are bound to. Empty: the host of the request. A parent domain (`example.com`) lets the passkeys of `www.example.com` work on `admin.example.com` too; it must be the host or a parent of it, else the host is used |
| `RelyingPartyIDMap[]` | empty | A multi-host installation: the relying party id of each host, `RelyingPartyIDMap[shop.example.org]=example.org`. A host not listed uses `RelyingPartyID`; a mapped id that is not the host or a parent of it is ignored |
| `RelyingPartyName` | empty | The name the browser shows; empty: `[SiteSettings] SiteName` |
| `AllowedOrigins[]` | empty | The origins (`scheme://host[:port]`) answers may come from. Empty: the origin of the request. List every host the passkeys are used on when the relying party id is a parent domain |
| `UserVerification` | `required` | `required`, `preferred` or `discouraged` for "Verify it's you". Signing in with a passkey always requires it |
| `Algorithms[]` | `-7`, `-257`, `-8`, `-35`, `-36` | COSE algorithms offered, most preferred first: ES256, RS256, Ed25519 (needs PHP's sodium extension), ES384, ES512 |
| `ChallengeSeconds` | `300` | Seconds a challenge may be answered (30 to 3600) |
| `MaxPerUser` | `20` | Passkeys one account may have |
| `Attestation` | `none` | What a new passkey is asked to tell about its maker: `none`, `indirect` or `direct`. With `indirect` every browser keeps the authenticator's model id (AAGUID), which names it in the list; the attestation statement itself is never relied on |
| `AuthenticatorNames[]` | empty | Names of authenticator models the built-in list lacks or names otherwise: `AuthenticatorNames[<aaguid>]=<name>` |
| `Policy`, `PasswordSignIn`, `GroupPolicy[]`, `GroupPasswordSignIn[]`, `UserPolicy[]`, `UserPasswordSignIn[]`, `PolicyHandlers[]`, `ExemptViews[]` | see section 6 | The passkey policy |
| `ConsoleSlots[]` | empty | Templates added below the list on `user/passkeys` (section 12) |

Passkeys need a secure page: HTTPS, or `http://localhost` while developing. An IP address is not a valid relying
party id.

**The names of authenticators.** The list names the common passkey providers and security keys by the model id
(AAGUID) a new passkey carries: iCloud Keychain, Google Password Manager, Chrome on Mac, Windows Hello, 1Password,
Bitwarden, Dashlane, Samsung Pass, KeePassXC, Proton Pass and the YubiKey 5 series. Firefox blanks the model id when
the site asks for attestation `none`; such passkeys show "Unknown" unless `Attestation=indirect`. The model id names a
product, never a device or a person.

**Site designs with a sign-in template of their own.** The sign-in forms of `design/standard`, `design/admin` and
admin4 have the passkey button; designs that override `user/login.tpl` add it after their own sign-in form with

```
{include uri='design:user/parts/passkey_login.tpl'}
```

That part draws nothing unless passkeys are offered. The browser's passkey autofill additionally needs the
`data-exp-passkey` attributes and `autocomplete="username webauthn"` on the design's own form, as
`design/standard/templates/user/login.tpl` shows.

## 6. Policy: optional, preferred, required

| Setting | Default | Meaning |
|---|---|---|
| `Policy` | `optional` | The site's default: `optional` (users may add passkeys), `preferred` (a user without a passkey is sent to `user/passkeys` once per session after signing in, with **Not now** to go on), `required` (a user without a passkey adds one before any other page) |
| `PasswordSignIn` | `enabled` | `disabled`: the sign-in form refuses the password of an account that **has** a passkey. An account without one still signs in with its password, so nobody is locked out, and with `required` it then adds one |
| `GroupPolicy[<group id>]` | empty | Per user group (content object id). The strictest of the user's groups and `Policy` wins |
| `GroupPasswordSignIn[<group id>]` | empty | `disabled` in any of the user's groups wins |
| `UserPolicy[<user id>]`, `UserPasswordSignIn[<user id>]` | empty | Per account; win over the groups and the default |
| `PolicyHandlers[]` | empty | Classes implementing `expPasskeyPolicyHandler`, asked last (section 12) |
| `ExemptViews[]` | `user/passkeys`, `user/logout`, `user/login`, `user/password`, `user2fa`, `ezjscore`, `user/preferences` | Views (`module` or `module/view`) that stay open while a passkey is required |

For example, administrators and editors must use passkeys and may no longer sign in with a password once they have
one:

```ini
# settings/override/site.ini.append.php
[PasskeySettings]
GroupPolicy[12]=required
GroupPasswordSignIn[12]=disabled
GroupPolicy[13]=required
GroupPasswordSignIn[13]=disabled
```

A refused password is answered exactly like a wrong one, so nobody learns whether an account exists or has passkeys;
where any setting can turn the password sign-in off, the form adds after every refused sign-in alike that accounts
which sign in with passkeys only use the passkey button. A refused password does not count as a failed sign-in of the
account. The policy is applied on the web pages only (index.php and Velocity); REST, SOAP, WebDAV and the
`expservices` login keep their own sign-in.

The policy is enforced right after the password change at next sign-in and before "Verify it's you". Adding the first
passkey asks "Verify it's you" too; a sign-in that already asked for what it would ask (the password of an account
that has nothing more, the password and the authenticator app's code) counts as that confirmation, so a user who just
signed in is not asked again.

## 7. Another account's passkeys

`/user/passkeys/<user id>` shows the passkeys of another account to a viewer with the policy `user/passkeys_manage`
(the account overview links to it). **Revoke** removes one, the last one too: for a lost or stolen device. The owner is
mailed, the audit records `access.passkey.revoke` with the administrator, and the owner then signs in with the
password, which the administrator can set. Nothing can be added or renamed there: a passkey is made on the owner's
device. Opening the page asks "Verify it's you" (users group).

## 8. The table and the upgrade

`exppasskey` holds one row per passkey: `id`, `user_id`, `name`, `credential_id` (base64url), `credential_hash`
(SHA-256, unique, what a sign-in looks the passkey up by), `public_key` (the COSE key, base64url), `algorithm`,
`sign_count`, `user_handle` (random per account, never the user id), `aaguid` (the authenticator model),
`transports`, `backup_eligible`, `backup_state`, `created`, `last_used`, `last_ip`. Nothing in it is secret.

- New installations get it from `share/db_schema.dba` (every engine) or the kernel schemas of MySQL, PostgreSQL and
  SQLite.
- Upgrades: the end of
  `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` creates it only when it is missing, or on any engine,
  Oracle and MongoDB included:

```bash
php update/common/scripts/6.0/createpasskeytable.php --dry-run --allow-root-user   # says exists or missing
php update/common/scripts/6.0/createpasskeytable.php --allow-root-user
```

Until the table exists the passkey page says so and the sign-in form offers no passkey. After the upgrade regenerate
the kernel autoloads, clear the INI, template and template-override caches, reload PHP-FPM and restart Velocity.
Removing an account removes its passkeys.

## 9. Two-step sign-in and the other sign-in paths

A passkey with user verification proves two things at once: something you have (the device) and something you are or
know (biometrics or PIN). So:

- A passkey sign-in is the whole sign-in; the second step of `sevenx_authentication_2fa` is not asked again.
- After a password sign-in, the second step of `sevenx_authentication_2fa` offers **Use a passkey** beside the
  authenticator app's code. An account that must have a second step (`Enforce2FA`) and has a passkey needs no app:
  its second step is the passkey.

Nothing changes on the other paths: REST, SOAP, WebDAV and the `expservices` login sign in with the password (and the
second step where an installation guards them) exactly as before.

## 10. How a passkey is checked

The kernel checks WebAuthn itself (`kernel/classes/webauthn`), without a library, on PHP 8.0 to 8.5:

- Each options request gets a new random challenge, kept in the session with its purpose (register, login, reauth),
  the user and an end time; the first answer that names it uses it up, so no answer can be replayed.
- `clientDataJSON`: the type (`webauthn.create` or `webauthn.get`), the challenge, an allowed origin, not cross-origin.
- Authenticator data: the SHA-256 of the relying party id, user present, user verified where required, a backup state
  only for a credential that can be backed up.
- Registration: the attested credential (an id of at most 1023 bytes) and a COSE public key of an offered algorithm.
- Sign-in: the signature over the authenticator data and the hash of `clientDataJSON` with the stored key (ES256,
  ES384, ES512, RS256, RS384, RS512 with OpenSSL, Ed25519 with sodium), and the signature counter: an authenticator
  that counts must count up, else the passkey may have been copied and is refused.
- Attestation statements are not verified: the site trusts no particular authenticator model.

## 11. Audit and e-mail alerts

| Event | When |
|---|---|
| `access.passkey.add` | a passkey is added (or refused: result failed with the reason) |
| `access.passkey.rename` | a passkey is renamed (before: the old name) |
| `access.passkey.remove` | the owner removes a passkey |
| `access.passkey.revoke` | an administrator revokes a passkey of another account (after: by) |
| `access.passkey.use` | a passkey signs in, answers the second step or confirms "Verify it's you" (after: purpose login or reauth) |
| `access.passkey.use.failed` | a passkey is refused (reason: challenge, origin, signature, counter, unknown, disabled, locked ...) |
| `access.passkey.password_refused` | the password of an account that signs in with passkeys only was refused |
| `access.passkey.required`, `access.passkey.suggested` | the policy sent a user to add a passkey |

The records name the passkey by id, name, algorithm and model, never by its key or credential id. Adding, removing
and revoking a passkey send the owner a security alert by e-mail ("A passkey was added to your account"); the site
turns the alerts off with `[UserSessionSettings] SecurityAlertMail=disabled`.

## 12. For extensions

- `expPasskeyPolicyHandler` (`[PasskeySettings] PolicyHandlers[]`): `passkeyPolicy( eZUser $user, array $policy )`
  gets the decision of the settings (`policy`, `password_sign_in`) and returns it changed, or null.
- `[PasskeySettings] ConsoleSlots[]=design:myext/passkeys_slot.tpl`: a template added below the list on
  `user/passkeys`, with `$account` and `$own`. A sessions section or a recovery section plugs in here.
- `expPasskeyService` (`verifyForUser()`, `reauthOptions()`) checks a passkey of a signed-in or pending user, as the
  second step of `sevenx_authentication_2fa` does.
- `design/standard/javascript/exp_passkey.js` runs the browser side for a form with `data-exp-passkey="register"`,
  `"login"` or `"reauth"` and its options in `data-exp-passkey-options`.
