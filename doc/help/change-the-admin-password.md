---
title: Change the administrator password
audience: admin, developer
category: administration
order: 30
summary: Change the administrator password in the administration, for another user, or on the command line; how passwords are hashed and checked, what happens to sessions, and how to get back in when nobody can sign in.
keywords: admin password, administrator, change password, reset password, lost password, forgot password, locked out, resetuserpassword, exp:resetuserpassword, HashType, bcrypt, php_default, MinPasswordLength, PasswordSettings, two-step sign-in, 2FA, sessions, break glass, recovery
featured: true
context: user/password, user/setting
type: help
---
# Change the administrator password

This page is for administrators and for the people who run an Exponential installation. It covers every supported way
to change the password of the administrator account (login `admin`) or of any other account: in the administration,
for another user, and on the command line. It explains how passwords are stored and checked, what happens to open
sessions after a change, and what to do when nobody can sign in any more.

For the password a fresh installation starts with, see
[The default administrator password](default-administrator-password.md). For the wider picture, see
[Security hardening](security-hardening.md).

> **Important:** Every new installation must get a new administrator password at once, and should get a personal
> administrator account for daily work. See [Account hygiene](#account-hygiene) below.

## Contents

1. [Change your own password in the administration](#change-your-own-password-in-the-administration)
2. [Change another user's password as an administrator](#change-another-users-password-as-an-administrator)
3. [Change a password on the command line](#change-a-password-on-the-command-line)
4. [How passwords are stored: HashType](#how-passwords-are-stored-hashtype)
5. [Password rules](#password-rules)
6. [What happens after a change: sessions and mail](#what-happens-after-a-change-sessions-and-mail)
7. [Forgot your password?](#forgot-your-password)
8. [Locked out: when no administrator can sign in](#locked-out-when-no-administrator-can-sign-in)
9. [Two-step sign-in and a lost phone](#two-step-sign-in-and-a-lost-phone)
10. [Velocity and PHP-FPM](#velocity-and-php-fpm)
11. [Account hygiene](#account-hygiene)
12. [What not to do any more](#what-not-to-do-any-more)
13. [Sources and credits](#sources-and-credits)

## Change your own password in the administration

The page is `user/password`: `/admin/user/password` with the default URL access (the part before `/user/password` is
your administration siteaccess). It is the same page in every design; only the way to it differs.

| Design | Where to find it |
|---|---|
| **Admin 4** and **Admin 4 L** (the same header) | The user menu (the button with your name in the header) > **Change password**. Or **My account** > the **Password** card > **Change password**. |
| **Classic** (`admin`) | The **Current user** box > **Change password**. |
| **AdminUI** | The user panel in the sidebar > **Change password**. |
| The public site | **Change password** on your profile page, at `/user/password`. |

1. Enter your **Current password**.
2. Enter the **New password** and the same again in **Confirm new password**. The page lists what the new password
   needs (for example *At least 10 characters*) and ticks each requirement off while you type. **Generate a strong
   password** makes one for you; **Show** reveals what you typed.
3. Press **Change password**.

You stay signed in, with a new session id. Your sessions in other browsers and on other devices are signed out at
their next request, and an e-mail "Your password was changed" goes to the account's address. The page says how many
other sessions were ended.

The link is grayed out, or missing, when your role lacks the policy **user/password**. The Administrator role has it.

Details of the page: [Changing your password](../features/6.0/modern-password-change.md).

## Change another user's password as an administrator

`user/password` only ever changes the password of the user who is signed in. To set a password for someone else:

1. Open the user in **Users** (Admin 4: the **Users** tab, or search for the name).
2. Press **Edit**.
3. In the **User account** field, type the new password into **Password** and again into **Confirm password**. Leave
   both untouched to keep the current password: they hold a placeholder, not the password.
4. Press **Send for publishing** (or **Publish**).

The same password rules apply as on `user/password`, and the same things follow: the user's open sessions are signed
out, the user gets the "Your password was changed" mail, and the change is in the audit trail (never the password
itself). Tell the user the new password through a separate, safe channel, and ask them to change it on their first
sign-in. Better still, let them use **Forgot your password?** themselves, so that you never know their password.

The **Username** (login) cannot be changed once an account has one. That is one reason to create a personal
administrator account rather than using `admin` every day ([Account hygiene](#account-hygiene)).

To switch an account off, or to clear its failed sign-ins, use **Configure user account settings** below the
**User account** field on the user's page (`user/setting/<user id>`): **Enable user account**, **Maximum number of
failed login attempts** and **Reset** for the failed attempts.

## Change a password on the command line

The supported tool is `bin/php/resetuserpassword.php`, also available as the console command
`exp:resetuserpassword`. It sets the password through the same code as the user edit form, so the same password
rules, the same hash type and the same session handling apply. It works when nobody can sign in to the administration.

Run it from the installation's root, as the user that owns the installation, or as the operating system's `root`.

**As the operating system root user** (no Exponential sign-in needed):

```bash
# generate a strong 24-character password for "admin" and print it once
./console exp:resetuserpassword --allow-root-user -u admin -g -l 24

# the same with the script itself
php bin/php/resetuserpassword.php --allow-root-user -u admin -g -l 24
```

`--allow-root-user` (short `-r`) is refused unless you really are the operating system user `root`.

**As another administrator of the site** (no root needed):

```bash
./console exp:resetuserpassword -a <your login> -ap '<your password>' -u editor1 -g
```

The login given with `-a` must sign in with the password given with `-ap`, and it must hold the role named
**Administrator**.

| Option | Meaning | Default |
|---|---|---|
| `-u <login>` | the account to change | `admin` |
| `-g`, `--generate` | generate a password (also when neither `-g` nor `-p` is given) | on |
| `-l <length>` | length of a generated password | 16 |
| `-p <password>` | set this password instead | none |
| `-a <login>`, `-ap <password>` | authorize as an administrator of the site | none |
| `-r`, `--allow-root-user` | authorize as the operating system root user | off |
| `-s <siteaccess>` | the siteaccess, and so the database, to use | the default siteaccess |
| `-q` | print only errors and the generated password | off |

What it says, and what that means:

| Message | Meaning |
|---|---|
| `Password reset for user: <login>` and `Generated password: ...` | done; the generated password is shown once |
| `Admin authentication required ...` | give `-a` and `-ap`, or run as root with `--allow-root-user` |
| `Admin authentication failed.` | the password given with `-ap` is wrong |
| `User '<login>' does not have the Administrator role.` | the authorizing account is not an administrator |
| `Target user not found: <login>` | no account has that login |
| `Target password does not validate. ...` | the password breaks a rule, for example `The password must be at least 10 characters long.` |

Good practice:

- **Prefer `-g`.** A password given with `-p` or `-ap` is visible to other users of the machine in the process list
  and stays in your shell history. If you must type one, put a space in front of the command (with Bash's
  `HISTCONTROL=ignorespace`) or clear the history afterwards.
- **Change the generated password** at your first sign-in, in the administration, to one only you know.
- The command changes the password and nothing else. It does **not** enable a disabled account, clear failed sign-in
  attempts, or turn off two-step sign-in; see [Locked out](#locked-out-when-no-administrator-can-sign-in).
- It is the right tool for every database Exponential supports (MySQL and MariaDB, PostgreSQL, SQLite, Oracle,
  MongoDB), because it goes through the kernel and not through SQL.

These behaviors were checked on 18 October 2026 against a throwaway account: a generated password signs in and the
old one no longer does, the stored hash is the configured type, a password shorter than `MinPasswordLength` is
refused, `-q` still prints the generated password, and an authorizing account without the Administrator role is
refused.

More: [Reset a user password from the command line](../features/6.0/reset-user-password.md).

## How passwords are stored: HashType

Exponential never stores a password, only a hash of it, in the table `ezuser` (`password_hash`, and the hash type
in `password_hash_type`). The type used for new and changed passwords is set in `settings/site.ini`:

```ini
[UserSettings]
HashType=php_default
UpdateHash=true
```

| `HashType` | Type id | What it is | Use it? |
|---|---|---|---|
| `php_default` | 7 | PHP's `password_hash()` with `PASSWORD_DEFAULT`: bcrypt today, salted, with a work factor (cost 12 since PHP 8.4) | **yes, the default** |
| `bcrypt` | 6 | bcrypt chosen explicitly | acceptable |
| `md5_user` | 2 | MD5 of login and password, unsalted | no: only for very old data |
| `md5_password` | 1 | MD5 of the password, unsalted | no |
| `md5_site` | 3 | MD5 of site name, login and password; breaks when the site name changes | no |
| `plaintext` | 5 | the password itself | never |
| (MySQL `PASSWORD()`) | 4 | legacy; gone since MySQL 8.0 | never |

`UpdateHash=true` re-hashes an older hash to the current `HashType` at the user's next successful sign-in. Keep both
values as shipped. An installation upgraded from an old version may still hold MD5 hashes for accounts that have not
signed in since; give those users a new password (or let them use **Forgot your password?**) rather than waiting.

Change these settings only in an override (`settings/override/site.ini.append.php`), never in the shipped file.

## Password rules

The rules every form checks (`user/password`, registration, the user edit form, the command line) are:

```ini
[UserSettings]
MinPasswordLength=10      # shipped value; counted in bytes; 3 when the setting is missing
GeneratePasswordLength=16 # generated passwords (the page's button uses at least 12)

[PasswordSettings]        # every rule off by default, so an upgrade refuses nothing new
RequireLowercase=disabled
RequireUppercase=disabled
RequireDigit=disabled
RequireSymbol=disabled
MinCharacterClasses=0     # at least this many of the four kinds above (0 to 4)
ForbidLogin=disabled      # must not contain the login name
ForbidCurrentPassword=disabled
```

For administrators, length beats composition rules: a production policy of `MinPasswordLength=15` with
`ForbidLogin=enabled` and `ForbidCurrentPassword=enabled` is recommended in
[chapter 13.6 of the installation guide](../install/13-security-hardening.md#136-passwords-and-sign-in), which also
explains the reasons (NIST SP 800-63B-4).

Two more settings protect the sign-in itself:

```ini
[UserSettings]
MaxNumberOfFailedLogin=0  # 0: no lockout. 10 locks an account after 10 failed attempts
TrustedIPList[]           # addresses or networks that are never locked out
```

A lockout also lets anyone lock a known account, so it is off by default; the audit trail reports password guessing
either way (`brute_force`, `brute_force_user`). If you turn it on, put the office or VPN network in `TrustedIPList[]`
so that your own administrators keep a way in.

## What happens after a change: sessions and mail

Set in `site.ini [PasswordSettings]`, both on by default:

- **`EndOtherSessions=enabled`**: a sign-in stores a stamp of the password in the session; when the password changes,
  every other session of that user no longer matches and is signed out at its next request, with every session
  handler. The session that made the change gets a new session id. This is what makes a password change useful after
  an account was taken over: whoever holds a stolen session cookie is signed out. It applies to every way of changing a
  password: `user/password`, the user edit form, **Forgot your password?** and the command line.
- **`ChangeNotificationMail=enabled`**: the user gets "Your password was changed" (category *Account security*, which
  nobody can switch off; the mail never contains the password). It is how a user learns of a change they did not make.

To end **all** sessions of everyone (after a suspected compromise), use **Setup** > **Sessions** > **Remove all
sessions** with the database session handler, or see [Sessions](../guides/sessions.md). A new site secret also signs
everyone out once.

## Forgot your password?

The sign-in page offers **Forgot your password?** (`user/forgotpassword`), for every account including administrators:

1. The user enters the e-mail address of the account.
2. Exponential sends a mail with a link. The page answers the same whether or not an account has that address, so
   it cannot be used to find out who has an account.
3. The link works once. Opening it gives the account a new generated password (`GeneratePasswordLength` characters)
   and mails it to the same address. The user signs in and changes it at once.

It only works when the account has a real e-mail address and the site can send mail. Give the administrator account a
real, monitored address right after installing ([chapter 10.1](../install/10-after-installing.md#101-first-login-and-the-administrator-password)).
Every request and reset is recorded in the audit trail (never the key or the password).

## Locked out: when no administrator can sign in

Work through these in order; each one is a supported way. You need shell access to the server.

1. **Another administrator** changes your password (see above) or presses **Reset** for your failed sign-ins on
   **Configure user account settings**.
2. **Forgot your password?**, if your account's e-mail address works.
3. **The command line**, as the operating system's root user:

   ```bash
   ./console exp:resetuserpassword --allow-root-user -u admin -g -l 24
   ```

   Use `-u <your login>` for a personal administrator account.
4. **The account was locked by failed sign-ins** (`MaxNumberOfFailedLogin`), and no other administrator can press
   **Reset**: sign in from an address in `[UserSettings] TrustedIPList[]`, or set `MaxNumberOfFailedLogin=0` in
   `settings/override/site.ini.append.php` for the moment, clear the INI cache and deploy (below), sign in, press
   **Reset**, and put the setting back.
5. **The account is disabled** (**Enable user account** off): another administrator switches it on again. When there
   is none, use the break-glass account of [Account hygiene](#account-hygiene).
6. **Two-step sign-in and the phone is gone**: see the next section.

After any of these: change the password to one only you know, check the audit trail
(`./console exp:audit tail --channel=access --lines=50`) for what happened before, and find out why you were locked
out. A lockout you did not cause can be an attack.

If the database itself is all you have (no PHP on the command line, for example), see the last resort in
[What not to do any more](#what-not-to-do-any-more).

## Two-step sign-in and a lost phone

With the extension `sevenx_authentication_2fa` active, a password alone no longer signs in
([Two-step sign-in](../guides/two-factor-login.md)). A new password does not change the second step.

- **Another administrator** opens the user's object, sets the **Two-step sign-in** field to **E-mail one-time code**
  or **Disabled**, and publishes. The user signs in and sets up the app again.
- **No other administrator can sign in** (the only administrator lost the phone): the second step is asked by the
  login handler `sevenxUser2fa`. As a break-glass measure, take it out of `[UserSettings] LoginHandler[]` in
  `settings/override/site.ini.append.php` (keep `standard`), clear the INI cache and deploy, sign in with the password,
  switch the account's **Two-step sign-in** field to **Disabled** or e-mail codes, then put the handler back and deploy
  again. While it is out, **every** account signs in with its password alone: do it in a short window, ideally with
  the administration restricted to your own address (chapter 13.5) or the site in maintenance mode, and note it in
  your change log. Then set up the app again.

The extension's own recovery details are in its `README.md` and `doc/two-factor-pages.md`.

## Velocity and PHP-FPM

- A password change is a change in the database: it works at once, under Apache with PHP-FPM, nginx, FrankenPHP and
  Exponential Velocity alike. Nothing needs a restart or a cache clear.
- A change of **settings** (`HashType`, `MinPasswordLength`, `[PasswordSettings]`, `MaxNumberOfFailedLogin`,
  `LoginHandler[]`) needs the INI cache cleared, and Velocity's workers started again, which `exp:velocity deploy`
  does in the right order:

  ```bash
  php bin/php/ezcache.php --clear-tag=ini --allow-root-user
  ./console exp:velocity deploy --allow-root-user     # Velocity; reload PHP-FPM yourself without it
  ```

- Run the command-line tools as the user that owns the installation (or as root with `--allow-root-user` where the
  documentation says so). Files a root run creates in `var/` must stay writable for the web server's user
  ([chapter 13.11](../install/13-security-hardening.md#1311-file-permissions-and-ownership)).

## Account hygiene

The account `admin` exists on every installation, so its login name is the first one an attacker tries. Do not use
it for daily work:

1. **Create a personal administrator account.** In **Users** > **Administrator users**, create a **User** with a
   login of your own (not `admin`, not `administrator`, not your public e-mail address if you can avoid it), a real
   e-mail address and a strong password. Accounts in **Administrator users** get the **Administrator** role through
   the group; check it under **Roles and policies** > **Administrator** > **Assignments**. One account per person, so
   the audit trail says who did what.
2. **Turn on two-step sign-in** for it, and require it for administrators
   ([Two-step sign-in](../guides/two-factor-login.md)).
3. **Sign in with the new account** and check that it can do everything you need.
4. **Retire `admin` as a sign-in.** Two supported ways; choose one:
   - **Disable it.** Open the `admin` user > **Configure user account settings** > clear **Enable user account** >
     **OK**. A disabled account cannot sign in, its open sessions end at their next request, and it has no rights.
     **Before you do this**, set `[UserSettings] UserCreatorID` in `settings/override/site.ini.append.php` to the
     object id of an enabled administrator account (your personal one, or a dedicated service account): the shipped
     value `14` is `admin`, and command-line tools such as the trash purge, the subtree removal and the content jobs,
     and self-registration, act as that account. Then clear the INI cache and deploy, and run one of those tools once
     to check.
   - **Keep it enabled, but unusable.** Give it a long random password that nobody keeps
     (`./console exp:resetuserpassword --allow-root-user -u admin -g -l 64`, and do not write it down), keep
     `UserCreatorID=14`, and rely on the break-glass procedure below. Choose this if you cannot change `UserCreatorID`.

   The **Deactivate and move** action of **Check user accounts** (`user/deactivate`, see
   [Check user accounts](../features/6.0/user-hygiene.md)) is meant for ordinary accounts: it refuses the anonymous
   user, `UserCreatorID`, the `admin` account and yourself on purpose, so it is not the way to retire `admin`.
5. **Keep a break-glass procedure, written down and tested.** For example: the root shell of the server, plus
   `./console exp:resetuserpassword --allow-root-user -u <login> -g -l 24` for any administrator account; for a
   disabled `admin`, another administrator switches it on. Keep the procedure (not a password) where the people on
   call can find it, and test it once a year on a copy of the site.
6. **Review accounts regularly**: **Users** > **Check user accounts** lists accounts that never signed in, have not
   been seen for months, are disabled or locked, or share a login or e-mail address. Review the assignments of the
   **Administrator** role as well; the audit trail alerts on new administrator role assignments
   (`admin_role_granted`).

## What not to do any more

Older guides, including the community wiki article this page replaces, list ways that are wrong, unsafe or no longer
work in Exponential 6:

- **Do not compute an MD5 hash in SQL** (`MD5(CONCAT(...))`, `MD5("admin\npassword")`), and do not reset a password to
  the well-known hash of `publish`. MD5 hashes are unsalted and fast to crack; the well-known hash is the first thing an
  attacker tries.
- **Do not edit `eZUser::authenticateHash()`** to accept any password, not even for a minute. Every account then
  signs in with any password, for everybody who tries.
- **Do not set `CheckValidity=true` to "run the setup again".** On an installed site the setup wizard now only shows
  "This site is already installed"; running it again is a reinstall that writes over the database.
- **Do not leave the password in a file**: delete `var/log/initial-admin-password` after the first sign-in, and never
  keep a password in `kickstart.ini`, a shell history or a ticket.

**Last resort, the database only.** If PHP on the command line is not available and you must set a hash by hand,
compute a bcrypt hash outside the database and store it with type 7:

```bash
# reads the password from the keyboard without echo; prints only the hash
read -rs P && printf '%s' "$P" | php -r 'echo password_hash(stream_get_contents(STDIN), PASSWORD_DEFAULT), PHP_EOL;'; unset P
```

```sql
UPDATE ezuser SET password_hash = '<the hash>', password_hash_type = 7 WHERE login = 'admin';
```

Take a backup first. Then clear the user cache (`php bin/php/ezcache.php --clear-id=user_info_cache
--allow-root-user`) so that other sessions of that account end, sign in, and change the password once more in the
administration so that the mail, the audit record and the session handling run as they should. The account also needs
its row in `ezuser_setting` with `is_enabled = 1`: an account without that row cannot sign in.

## Sources and credits

Parts of this page, the list of older reset methods and the database-only procedure, are adapted from the community
wiki article ["Reseting the admin password in eZ publish"](https://ezpedia.exponential.earth/en/solution/reseting_the_admin_password_in_ez_publish)
on eZpedia, written by kracker, xavier, Graham Brookins and other contributors since 2006, and distributed under the
GNU Free Documentation License. The adapted text was rewritten and corrected for Exponential 6 and is distributed under
the GNU Free Documentation License 1.3 or any later version, like the rest of this documentation
([The license of this documentation](../README-LICENSE.md)). Everything else on this page was written from the
Exponential 6 source code.

## Related pages

- [The default administrator password](default-administrator-password.md)
- [Security hardening](security-hardening.md)
- [Your account and signing in](accounts-and-sign-in.md)
- [Changing your password](../features/6.0/modern-password-change.md),
  [Reset a user password from the command line](../features/6.0/reset-user-password.md)
- [Chapter 10.1: First login and the administrator password](../install/10-after-installing.md#101-first-login-and-the-administrator-password),
  [chapter 13: Security hardening for production](../install/13-security-hardening.md)
- [Two-step sign-in](../guides/two-factor-login.md), [Sessions](../guides/sessions.md),
  [Check user accounts](../features/6.0/user-hygiene.md), [Audit](../bc/6.0/audit.md)
