---
title: Reset a password from the command line
audience: government, admin, hosting
category: public-sector
order: 90
summary: Step by step - set a new password for any account, including a lost administrator password, with bin/php/resetuserpassword.php, generating a random password so nothing lands in the shell history.
keywords: resetuserpassword, reset password, command line, lost admin password, administrator, generate password, shell, root, break glass
type: help
---
# Reset a password from the command line

This task is for system administrators with shell access, typically when the administrator password is lost or an
installation is taken over. `bin/php/resetuserpassword.php` sets a new password for any user; it is described on
[Reset a user password from the command line](../../../../features/6.0/reset-user-password.md).

## Contents
1. [Before you start](#before-you-start)
2. [Reset with a generated password](#reset-with-a-generated-password)
3. [Authorize as a site administrator instead](#authorize-as-a-site-administrator-instead)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- Shell access in the installation root. Either you are the operating system user `root` (then use
  `--allow-root-user`), or you know the login and password of a site administrator.
- An approved request: a shell reset bypasses the service desk, so record why it was done.

## Reset with a generated password

As `root`, generate a new 20-character password for the account `admin`:

```bash
php bin/php/resetuserpassword.php --allow-root-user -u admin -g -l 20
```

The password is printed once. Note it in your password safe, sign in, and change it in the administration if your
policy says so. For another account, give its login with `-u <login>`; for a siteaccess with its own database,
add `-s <siteaccess>`.

## Authorize as a site administrator instead

Without operating system root, the reset is authorized by an account that holds the `Administrator` role:

```bash
php bin/php/resetuserpassword.php -a <admin login> -ap '<admin password>' -u <login> -g
```

## Check it worked

- Sign in with the new password.
- The account's other sessions are signed out on their next request, and a mail "Your password was changed" goes to
  the account's address ([Changing your password](../../../../features/6.0/modern-password-change.md#in-short)).
- The change is in the audit trail; the password is never recorded.

## Pitfalls

| Message | Cause |
|---|---|
| `Admin authentication required` | neither `-a`/`-ap` nor `--allow-root-user` was given |
| `... does not have the Administrator role.` | the authorizing user is not an administrator |
| `Target user not found: <login>` | the login does not exist |
| `Target password does not validate ...` | shorter than `MinPasswordLength` |

A password typed with `-p` (and the administrator's password after `-ap`) is visible in the process list and the
shell history. Prefer `-g`, and clear the history afterwards if you had to type one.

## Related pages
- [Reset a staff password](reset-a-staff-password.md)
- [Default administrator password (help)](../../../default-administrator-password.md)
- [First sign-in and default passwords (hosting)](../../../hosting/security/first-sign-in-and-default-passwords.md)
- [Suspected account compromise](../../operations/incidents/suspected-account-compromise.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
