---
title: Default credentials and first sign-in
audience: bundler, hosting, admin
category: bundling
order: 370
summary: Which administrator password a bundled installation ends up with for each installer, why no bundle may ship a fixed or shared password, how to hand a generated password to the recipient once, how to make the first sign-in lead to a password change, and the database seed password every bundler must know about.
keywords: default password, administrator, admin, initial-admin-password, publish, generated password, MinPasswordLength, well-known passwords, WeakPasswordCheck, first sign-in, hand-over, exp:resetuserpassword, bundle
type: help
---
# Default credentials and first sign-in

This page is for bundlers responsible for how the recipient of a bundle first signs in. Every installation starts
with one administrator, login **`admin`**. What its password is depends on the installer; the full reference is
[The default administrator password](../default-administrator-password.md).

## Contents

1. [The rule](#the-rule)
2. [What each installer does](#what-each-installer-does)
3. [The seed password](#the-seed-password)
4. [Handing the password over](#handing-the-password-over)
5. [Making the first sign-in change it](#making-the-first-sign-in-change-it)
6. [Checklist](#checklist)

## The rule

**No bundle ships a fixed, shared or guessable administrator password**, except a public demo whose password is
published by design and that runs in demo mode ([Demo mode in bundles](demo-mode-in-bundles.md)).

## What each installer does

| Installer | Password after the install |
|---|---|
| Setup wizard | the one typed in the wizard; "there is no default". It refuses passwords shorter than `MinPasswordLength` (10) and well-known ones |
| Kickstarter | `[site_admin] Password` from `kickstart.ini`; "when it is empty, shorter than `MinPasswordLength` or a well-known word, a random 20-character password, printed once and written to `var/log/initial-admin-password`" |
| `exp:install` | `--password` when long enough and not well known; "otherwise a random 24-character password, printed at the end and written to `var/log/initial-admin-password`" |
| Site packages | "no change: packages carry no accounts" |

Source: [In short](../default-administrator-password.md#in-short). `var/log/initial-admin-password` has mode `0600`.

The well-known list is controlled by `site.ini [UserSettings] WeakPasswordCheck=enabled` (shipped), with
`WeakPasswordList[]` and `WeakPasswordFile` for additions such as your company or product name. Add your bundle's name
to that list in the settings your bundle ships, so nobody uses it as a password.

## The seed password

"The kernel's clean database ... has the administrator account with the password **`publish`**" (`share/db_data.dba`,
`kernel/sql/common/cleandata.sql`, `kernel/sql/sqlite/cleandata.sql`). The installers always overwrite it. It is still
the password when a database was loaded from those files by hand without an installer, or when the setup wizard was
told to keep such a database. A bundle that builds its database any other way than through an installer must reset
it:

```bash
php bin/php/console exp:resetuserpassword --allow-root-user -u admin -g -l 24
```

(the command shown on [The shipped database seed](../default-administrator-password.md#the-shipped-database-seed-publish)).

## Handing the password over

| Bundle | Hand-over |
|---|---|
| Panel installer | show it once on the panel's result page or send it through the panel's secure channel; delete the file |
| Server image | print it once on the console or in the platform's instance log, or ask the owner for a password at launch |
| Container | the owner reads `var/log/initial-admin-password` from the volume once, or passes `--password` from a secret store |
| Appliance | the first-start screen shows or asks for it |

Then delete `var/log/initial-admin-password`, as the installer's summary says.

## Making the first sign-in change it

- Tell the recipient, in your getting-started text, to change the password at once:
  [Change the administrator password](../change-the-admin-password.md).
- Give the account a real e-mail (`--email`, `[site_admin] Email`), so **Forgot your password?** reaches someone.
- Recommend a personal administrator account per person and retiring `admin` as a sign-in
  ([Account hygiene](../default-administrator-password.md#account-hygiene)), and two-step sign-in for administrators.
- A setting that forces a password change at the first sign-in is not documented for Exponential 6.0.15; a bundle
  that wants that behavior asks for the password at first start instead of generating one.

## Checklist

- [ ] No fixed password in templates, images, scripts or documentation of a bundle for real sites.
- [ ] The generated password reaches the recipient once and the file is deleted.
- [ ] The administrator e-mail is the recipient's.
- [ ] No database in the bundle still has `publish`.
- [ ] Your getting-started text links the password change and account hygiene.

## Related pages

- [First sign-in and default passwords (hosting)](../hosting/security/first-sign-in-and-default-passwords.md)
- [Chapter 10.1 First login](../../install/10-after-installing.md#101-first-login-and-the-administrator-password)
- [Security defaults of a bundle](security-defaults-of-a-bundle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
