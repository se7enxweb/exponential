# Passkeys: signing in with a fingerprint, a face or a security key

This guide explains the passkeys of Exponential 6.0.15. A passkey replaces the password at sign-in: the browser or
the device asks for a fingerprint, a face, the screen lock PIN or a touch of a security key, and the site checks a
signature made with a key that never leaves the device. The site keeps only the public half of that key, so nothing
stored on the server can be used to sign in.

It is written for two readers. **Users** read sections 1 to 4. **Administrators** read sections 5 to 10.

[Guides](README.md) · Related: [Verify it's you](verify-its-you.md) · [Two-step sign-in](two-factor-login.md) ·
[Security and audit](security-and-audit.md) · Change note:
[Passkeys and "Verify it's you"](../bc/6.0/passkeys-and-verify-its-you.md)

## In short

- **My account > Passkeys** (`/user/passkeys`): add a passkey (the browser's passkey dialog, then a name of your
  choice), see when each was added and last used, rename and remove them.
- The sign-in form has **Sign in with a passkey** next to the password. Where the browser offers it, the username
  field also lists your passkeys (passkey autofill), and choosing one signs you in.
- A passkey sign-in is the whole sign-in: the second step of two-step sign-in is not asked again, because the passkey
  already checks who you are on your device.
- Passkeys are used on the web pages only. The REST interface, SOAP, WebDAV and the `expservices` login sign in as
  before.
- New in 6.0.15: the table `exppasskey`, the view `user/passkeys`, the block `site.ini [PasskeySettings]`, five
  audit events `access.passkey.*`.

## Contents

1. [What a passkey is](#1-what-a-passkey-is)
2. [Adding, naming and removing passkeys](#2-adding-naming-and-removing-passkeys)
3. [Signing in with a passkey](#3-signing-in-with-a-passkey)
4. [A lost or replaced device](#4-a-lost-or-replaced-device)
5. [Settings](#5-settings)
6. [The table and the upgrade](#6-the-table-and-the-upgrade)
7. [Two-step sign-in and the other sign-in paths](#7-two-step-sign-in-and-the-other-sign-in-paths)
8. [How a passkey is checked](#8-how-a-passkey-is-checked)
9. [Audit](#9-audit)
10. [Limits](#10-limits)

---

## 1. What a passkey is

A passkey is a key pair made by your device (a phone, a laptop, a password manager or a hardware security key) for
this site only. The private key stays on the device; many password managers sync it between your devices ("synced"
in the list). The site stores the public key and checks every sign-in against it. A passkey cannot be phished: the
browser uses it only on the site it was made for.

## 2. Adding, naming and removing passkeys

1. Open **My account** and the card **Passkeys**, or go to `/user/passkeys`.
2. The page first asks you to confirm it is you (see [Verify it's you](verify-its-you.md)): enter your password, or
   use a passkey you already have.
3. Under **Add a passkey**, type a name you will recognize later (for example "Work laptop" or "Password manager");
   without one the passkey is called "Passkey of <date>".
4. Choose **Add a passkey** and follow the browser: pick where to store it and confirm with your fingerprint, face,
   PIN or key.

The list shows each passkey's name, when it was added and last used, whether it is synced or bound to one device or
key, and its algorithm (ES256, RS256 ...). **Rename** changes the name; **Remove** asks once more and then deletes
the passkey here. Remove it from your device or password manager as well. An account has at most 20 passkeys
(`MaxPerUser`).

If the browser cannot make passkeys, the button stays hidden and the page says so; your password keeps working.

## 3. Signing in with a passkey

On the sign-in form choose **Sign in with a passkey** and confirm on your device. Where the browser supports passkey
autofill, click into the username field: your passkeys for the site appear there, and choosing one signs you in
without typing anything. "Remember me" applies as with the password.

A passkey is refused when the account is disabled or locked after failed sign-ins, when the account may not use the
siteaccess, or when the passkey was removed. The form then says why, and the password still works.

## 4. A lost or replaced device

Sign in with your password (or another passkey), open **My account > Passkeys** and remove the passkey of the lost
device. A passkey alone is worthless without the device's screen lock or biometrics, but removing it is the only way
to make sure it can never sign in again. An administrator cannot remove another user's passkey from the
administration yet (see [Limits](#10-limits)); removing the account removes its passkeys.

## 5. Settings

`site.ini [PasskeySettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `Passkeys` | `enabled` | `disabled`: no passkey can be added or used; the stored ones stay |
| `SignInForm` | `enabled` | Offer the passkey button and autofill on the sign-in form |
| `RelyingPartyID` | empty | The host name passkeys are bound to. Empty: the host of the request. A parent domain (`example.com`) lets the passkeys of `www.example.com` work on `admin.example.com` too; it must be the host or a parent of it, else the host is used |
| `RelyingPartyName` | empty | The name the browser shows; empty: `[SiteSettings] SiteName` |
| `AllowedOrigins[]` | empty | The origins (`scheme://host[:port]`) answers may come from. Empty: the origin of the request. List every host the passkeys are used on when `RelyingPartyID` is a parent domain, for example `https://www.example.com` and `https://admin.example.com` |
| `UserVerification` | `required` | `required`, `preferred` or `discouraged` for "Verify it's you". Signing in with a passkey always requires it |
| `Algorithms[]` | `-7`, `-257`, `-8`, `-35`, `-36` | COSE algorithms offered, most preferred first: ES256, RS256, Ed25519 (needs PHP's sodium extension), ES384, ES512 |
| `ChallengeSeconds` | `300` | Seconds a challenge may be answered (30 to 3600) |
| `MaxPerUser` | `20` | Passkeys one account may have |

Passkeys need a secure page: HTTPS, or `http://localhost` while developing. An IP address is not a valid relying
party id.

**Site designs with a sign-in template of their own.** The sign-in forms of `design/standard`, `design/admin` and
admin4 have the passkey button. A design that overrides `user/login.tpl` (the media theme, for example) does not
show it by itself; it adds, after its own sign-in form:

```
{include uri='design:user/parts/passkey_login.tpl'}
```

That part (`design/standard/templates/user/parts/passkey_login.tpl`) is a form of its own with the button and draws
nothing unless passkeys are offered. The browser's passkey autofill additionally needs the `data-exp-passkey`
attributes and `autocomplete="username webauthn"` on the design's own form, as
`design/standard/templates/user/login.tpl` shows.

## 6. The table and the upgrade

`exppasskey` holds one row per passkey: `id`, `user_id`, `name`, `credential_id` (base64url), `credential_hash`
(SHA-256, unique, what a sign-in looks the passkey up by), `public_key` (the COSE key, base64url), `algorithm`,
`sign_count`, `user_handle` (random per account, never the user id), `aaguid` (the authenticator model),
`transports`, `backup_eligible`, `backup_state`, `created`, `last_used`, `last_ip`. Nothing in it is secret.

- New installations get it from `share/db_schema.dba` (every engine) or the kernel schemas of MySQL, PostgreSQL and
  SQLite.
- Upgrades: the end of `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` creates it only when it is missing,
  or on any engine, Oracle and MongoDB included:

```bash
php update/common/scripts/6.0/createpasskeytable.php --dry-run --allow-root-user   # says exists or missing
php update/common/scripts/6.0/createpasskeytable.php --allow-root-user
```

Until the table exists the passkey page says so and the sign-in form offers no passkey. After the upgrade regenerate
the kernel autoloads, clear the INI, template and template-override caches, reload PHP-FPM and restart Velocity.
Removing an account removes its passkeys.

## 7. Two-step sign-in and the other sign-in paths

A passkey with user verification proves two things at once: something you have (the device) and something you are or
know (biometrics or PIN). So a passkey sign-in does not ask the second step of `sevenx_authentication_2fa`; the audit
records it as `access.session.login` with the handler `expPasskeyLogin`.

Nothing changes on the other paths: REST, SOAP, WebDAV and the `expservices` login sign in with the password (and the
second step where an installation guards them) exactly as before.

## 8. How a passkey is checked

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
- Attestation is asked as `none`: the site trusts no particular authenticator model, so it does not check attestation
  certificates.

## 9. Audit

| Event | When |
|---|---|
| `access.passkey.add` | a passkey is added (or refused: result failed with the reason) |
| `access.passkey.rename` | a passkey is renamed (before: the old name) |
| `access.passkey.remove` | a passkey is removed |
| `access.passkey.use` | a passkey signs in or confirms "Verify it's you" (after: purpose login or reauth) |
| `access.passkey.use.failed` | a passkey is refused (reason: challenge, origin, signature, counter, unknown, disabled, locked ...) |

The records name the passkey by id, name, algorithm and model, never by its key or credential id.

## 10. Limits

- Administrators cannot yet see or remove another user's passkeys in the administration.
- Attestation is not verified (see above).
- Passkeys are used on the web pages only, not on REST, SOAP or WebDAV.
- Designs with their own `user/login.tpl` show no passkey button until they include
  `design:user/parts/passkey_login.tpl` (see [Settings](#5-settings)); the sevenx media and simple themes and other
  extension designs live in repositories of their own and need that include there.
