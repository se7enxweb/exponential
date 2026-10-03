# Security and audit: lock it down, see who did what

This guide is for administrators and operators who answer for the safety of a site. In about 30 minutes you check that a fresh Exponential installation is hardened, hand out access with roles and policies, read the audit trail, switch debug output on for your own address only, and (optionally) add two-factor sign-in. Every step is a command or a click path that exists in 6.0.15. Commands run from the project root; the examples use `https://example.com` for your site.

You need: shell access to the installation, an administrator login, and `curl`.

## 1. Check the hardening that is already on (5 minutes)

Since September 2026 a default installation is safe without extra work. Verify it instead of trusting it.

**Security headers.** Every page, the administration included, must carry five headers:

```bash
curl -sI https://example.com/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
```

Expected output (order may differ):

```
x-content-type-options: nosniff
x-frame-options: SAMEORIGIN
content-security-policy: frame-ancestors 'self'
referrer-policy: strict-origin-when-cross-origin
permissions-policy: camera=(), microphone=(), payment=(), usb=()
```

Change one with `./console exp:ini set` (scope `override`), for example to allow a trusted editor host to frame the admin. The setting is `site.ini [HTTPHeaderSettings]` `SecurityHeaders[Content-Security-Policy]`; read its value first:

```bash
./console exp:ini get 'site.ini/HTTPHeaderSettings/SecurityHeaders[Content-Security-Policy]' --allow-root-user
```

Turn on HSTS only when the whole site is HTTPS-only: `SecurityHeaders[Strict-Transport-Security]=max-age=31536000`.

**Files that must not be served.** Dot files and PHP files below the document root must answer 404:

```bash
for p in .env .git/config extension/expservices/ezinfo.php; do
  curl -s -o /dev/null -w "$p %{http_code}\n" https://example.com/$p
done
```

Expected: `404` three times. If you keep your own copy of `.htaccess`, merge the rules from `.htaccess_root` and `.htaccess_root_static`.

**The session cookie.** Sign in to the admin, open the browser developer tools, Application (or Storage), Cookies: the session cookie must show `HttpOnly`, `SameSite=Lax` and, on HTTPS, `Secure`. These are the code defaults (`site.ini [Session]` keys `CookieSecure=auto`, `CookieHttponly=true`, `CookieSameSite=Lax`; the file only lists them as comments).

**The first administrator password.** A kickstart prints a random 20-character password once and writes it to `var/log/initial-admin-password`. Sign in, change it, and delete that file's content. Forgot it later:

```bash
php bin/php/resetuserpassword.php --allow-root-user -u admin -g
```

`-g` generates a random password and prints it. Details: [reset a user password](../features/6.0/reset-user-password.md).

Depth: [security defaults of September 2026](../specifications/6.0/security-defaults-2026-09.md), [datatype and input hardening](../specifications/6.0/datatype-input-hardening.md), [hardening of 6.0.13](../specifications/6.0/security-hardening-6.0.13.md).

## 2. Give people only the access they need (10 minutes)

Access is decided by **roles** made of **policies** (module, function, limitations), assigned to users or groups. Nobody gets anything that no role gives.

1. Sign in to the admin interface and open the **Users** tab, **Roles and policies** (`/role/list`).
2. Click **New role**, name it `Editor of News` in the editor that opens.
3. Click **New policy**, choose module **content**, function **edit**, then **Grant limited access** and pick the section or subtree the person may edit. **Save** the role with the button at the bottom.
4. Open the role, click **Assign**, and select the user or group.
5. Sign in as that user in a private window: only the allowed content can be edited.

Roles can hold dozens of policies; each policy shows its ID, the headings sort the list, and the editor has up and down buttons (they only tidy the list, the order never changes who may do what): [role policy order](../features/6.0/role-policy-order.md). Admin links already follow permissions, so a user never sees a link to something that would be refused: [admin links follow permissions](../features/6.0/admin-links-follow-permissions.md).

Use the same rules in templates:

```
{if $current_user|has_role( 'Editor' )} ... {/if}
{if $current_user|has_policy( 'content', 'edit' )} ... {/if}
```

All seven operators are in [role and policy checks in templates](../features/6.0/role-and-policy-template-operators.md).

Two rules of thumb: never edit the Administrator role to give a colleague a bit more; make a new role. And keep few administrators, because granting that role raises an alert (step 3).

## 3. Read the audit trail (5 minutes)

The audit is on by default. It records sign-ins and refusals, content changes, settings writes, cache clears, deploys and orders in a tamper-evident log (every record carries the hash of the one before).

```bash
./console exp:audit status --allow-root-user
```

Expected: `Audit: enabled`, then one line per channel (`content`, `access`, `system`, `commerce`, `read`) with files, bytes, records today and `Chain intact`.

Look at the newest sign-ins and verify nothing was edited:

```bash
./console exp:audit tail --channel=access --name='access.session.*' --lines=3 --allow-root-user
./console exp:audit verify --allow-root-user
```

`verify` prints `INTACT` per channel and exits with 1 when a chain is broken.

In the browser: sign in as administrator, open the **Audit** tab (`/audit/dashboard`), sign out and in again, and open **Console**: your sign-in is the newest `access.session.login`. Search by user, IP network or text:

```bash
./console exp:audit search --name='content.*' --result=refused --limit=10 --allow-root-user
./console exp:audit search --query=login --limit=5 --allow-root-user
```

Do these once on a new installation:

1. Run the cronjob group `frequent` every few minutes (`php runcronjobs.php frequent`); its `audit` part indexes events, delivers sinks, evaluates alerts and, once a day, rotates, verifies and archives.
2. Check who gets alert mail: `./console exp:audit alerts recipients --allow-root-user` (default: the site's `AdminEmail`).
3. List the alert rules: `./console exp:audit alerts list --allow-root-user`. Out of the box they watch brute-force sign-ins, an administrator role being granted, settings written out of hours, mass deletes, the audit being switched off and a broken chain.
4. Back up `settings/override/audit.ini.append.php`: it holds the signing and pseudonym keys.
5. Decide `OnWriteFailure` (`settings/audit.ini [AuditSettings]`, default `continue`): with `refuse`, security-relevant actions are refused while the log cannot be written, so nothing happens unrecorded.

Who may look: only the Administrator role holds the policies `audit/read` and `audit/manage`; give `audit/read` to an auditor role (optionally limited by channel) in step 2's way. A user without it sees no tab and a typed URL is refused and recorded as `access.permission.refused`.

Depth: [the audit trail](../features/6.0/audit-trail.md), [audit event model](../specifications/6.0/audit-event-model.md), [audit reference (bc)](../bc/6.0/audit.md).

## 4. Debug output for your address only (5 minutes)

Debug output shows file paths, SQL and settings, so it must never be on for every visitor. Exponential asks two lists: `DebugOutput` is the master switch, and with `DebugByIP=enabled` only the addresses in `DebugIPList[]` get the report.

1. Find your address as the server sees it: sign in to the admin, open the Exp Debug bar (it appears once you are allowed to see debug), tab **Settings**, and run the IP test. The test needs the policy `setup/setup`; it names `REMOTE_ADDR`, the entry that matched and warnings such as `lockout`, `open` or `expired`.
2. See where the setting comes from and what is in effect:

   ```bash
   ./console exp:ini where site.ini/DebugSettings/DebugByIP --allow-root-user
   ```

3. Write your address with an expiry so it cleans itself up, in `settings/override/site.ini.append.php`:

   ```ini
   [DebugSettings]
   DebugOutput=enabled
   DebugByIP=enabled
   DebugIPList[]
   DebugIPList[]=203.0.113.7/32 ; My laptop ; expires=2026-12-31T18:00
   ```

   (Use your address, or the label and expiry may be left out.) Entries accept IPv4 and IPv6 networks (`10.0.0.0/8`, `2001:db8::/32`) and `commandline` for the shell.
4. Clear the INI cache and reload the page: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

The bar refuses writes that would lock you out (the list would not contain your address) or open debug to everybody (`/0`, or `DebugByIP` off with `DebugOutput` on), and logs and can undo every change; presets (Template work, SQL tuning, Everything, Off) switch a bundle of settings at once. Switch to **Off** when done.

Depth: [the Exp Debug bar](../features/6.0/exp-debug-bar.md), [debug bar (bc)](../bc/6.0/debug-bar.md), [readable debug output](../features/6.0/debug-output-improvements.md).

## 5. Two-factor sign-in (optional, 10 minutes)

The extension `sevenx_authentication_2fa` adds TOTP (Google Authenticator, Authy and any RFC 6238 app) and e-mail one-time codes to the sign-in.

1. Install and activate it:

   ```bash
   composer require se7enxweb/sevenx_authentication_2fa
   ```

   ```ini
   # settings/override/site.ini.append.php
   [ExtensionSettings]
   ActiveExtensions[]=sevenx_authentication_2fa
   [UserSettings]
   ExtensionDirectory[]=sevenx_authentication_2fa
   LoginHandler[user2fa]=sevenxUser2fa
   LoginHandler[]=standard
   ```

   The standard handler must come after `sevenxUser2fa`; comment a `LoginHandler[]=standard` line in `settings/site.ini` if the order does not take effect.
2. Refresh: `php bin/php/ezpgenerateautoloads.php -e` and `php bin/php/ezcache.php --clear-all --allow-root-user`.
3. Grant the policies in **Roles and policies** (step 2): `user2fa/verify` to the Anonymous role and to every role that uses 2FA, and `user2fa/setup` to the roles that manage their own 2FA.
4. Sign in and open `/user2fa/setup`: choose TOTP, scan the code (or type the secret) into your app, enter the six digits. Sign out and in again: after the password you are asked for the code.
5. To make it mandatory, set `[General] Enforce2FA=enabled` in `settings/override/sevenxauthentication2fa.ini.append.php`; users without a method are sent to the setup page.

Expected: with a wrong code the sign-in is refused and the audit shows the failed attempt. Lost phone: the user can take the e-mail code while `AllowEmailFallback` is `enabled` (the default); the extension also has a reset 2FA interface, described on its feature page. Remove expired challenges with `php extension/sevenx_authentication_2fa/bin/php/sevenx2facleanup.php` (also a cronjob script).

Depth: [two-factor and social login](../features/6.0/extensions/sevenx_authentication_2fa.md) (every setting, social login, adding a provider).

## Checklist

- [ ] Five security headers present, dot files and PHP files answer 404
- [ ] Initial administrator password changed
- [ ] Roles hold only what people need; few administrators
- [ ] `exp:audit status` shows every chain intact, `frequent` cronjob runs, alert recipients are right
- [ ] Debug output only for your own address, with an expiry
- [ ] Two-factor on for administrators

## Related pages

- Control a site from scripts and apps with tokens: [remote services and apps](remote-services-and-apps.md).
- Upgrade impact of the security changes: [behaviour changes of 1-2 October 2026](../bc/6.0/behaviour-changes-2026-10.md), [security hardening of August 2026](../specifications/6.0/security-hardening-2026-08.md), [6.0.15 changelog](../changelogs/6.0/6.0.15.md).
- How it came about: [October 2026 chronicle](../history/2026/2026-10.md), [September 2026, second half](../history/2026/2026-09b.md).
- Other guides: [Operating a site](operating-a-site.md) (logs, backups, repairs), [Extensions](extensions.md) (two-factor and other extensions), [Glossary](../glossary.md).
