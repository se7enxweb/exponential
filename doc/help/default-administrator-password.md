---
title: The default administrator password
audience: admin, developer
category: administration
order: 31
summary: Which administrator login and password a fresh installation starts with, for each way of installing (setup wizard, kickstarter, console install, the shipped database seed, packages), and why it must be changed at once.
keywords: default password, admin password, administrator, publish, initial-admin-password, setup wizard, kickstarter, kickstart.ini, exp:install, install, first login, well-known password, cleandata, db_data.dba
featured: true
context: setup/init
type: help
---
# The default administrator password

This page is for whoever installs Exponential, and for administrators taking over an installation. Every
installation has one administrator account to begin with: login **`admin`** (user object 14, in the group
**Administrator users**, with the **Administrator** role). What its password is depends on how the site was
installed. This page lists it for each way, as the code of Exponential 6.0.15 does it.

> **Warning:** Whatever the installer set, change the administrator password right after the first sign-in, give the
> account a real e-mail address, and delete `var/log/initial-admin-password` if it exists. Then create a personal
> administrator account and retire `admin` as a sign-in ([Account hygiene](#account-hygiene)). How to change it:
> [Change the administrator password](change-the-admin-password.md).

## In short

| How the site was installed | Login | Password after the installation |
|---|---|---|
| [Setup wizard](#the-setup-wizard) in the browser | `admin` | the one you typed in the wizard; there is no default |
| [Kickstarter](#the-kickstarter-kickstartini) (`exp:kickstarter run` with `kickstart.ini`) | `admin` | `[site_admin] Password=` from `kickstart.ini`; when it is empty or a well-known word, a random 20-character password, printed once and written to `var/log/initial-admin-password` |
| [Console install](#the-console-install-expinstall) (`exp:install`) | `admin` | `--password=` when it is long enough and not a well-known word; otherwise a random 24-character password, printed at the end and written to `var/log/initial-admin-password` |
| [The shipped database seed](#the-shipped-database-seed-publish), loaded by hand without an installer | `admin` | **`publish`**: the historical default, known to everyone |
| [Site packages](#site-packages-and-demo-content) (the 7x and community packages) | `admin` | no change: packages carry no accounts; the installer above sets the password |

No installer of Exponential 6 leaves `publish` in place. It survives only where the database was created from the
seed without an installer, or where the wizard was told to keep an existing database that still had it.

## The setup wizard

The web setup wizard (`setup/init`, [chapter 5](../install/05-setup-wizard.md)) has a step for the administrator:
first name, last name, e-mail address, and the password typed twice. The login is always `admin`.

- There is **no default password**. The step refuses an empty password, two passwords that differ, and one shorter
  than `[UserSettings] MinPasswordLength` (10 as shipped).
- It does **not** check for well-known words beyond the length: `publish` (7 characters) is refused because it is too
  short, but a common word of 10 or more characters, such as `password12`, is accepted. Choose a long, random password;
  a password manager can generate one.
- If you chose to **keep the existing data** of a database, the step is skipped and the administrator account stays
  as that database has it. A database created from the shipped seed then still has `publish`: change it at once.

## The kickstarter (kickstart.ini)

The kickstarter runs the same steps without a browser, from `kickstart.ini` ([chapter 6](../install/06-kickstarter.md)):

```ini
[site_admin]
Continue=true
FirstName=Admin
LastName=User
Email=you@example.com
Password=
```

- **Empty or missing `Password=`** (as in the shipped `kickstart.ini-dist` and `kickstart.ini--example`): the
  installer generates a random 20-character password (letters and digits without the look-alikes), prints it on the
  console and writes it to `var/log/initial-admin-password` with mode `0600`, owner-only.
- **A well-known word** is replaced the same way. The list, in `eZStepSiteAdmin::isWeakDefaultPassword()`, is
  `publish`, `admin`, `password`, `changeme`, `change-me`, `secret`, `exponential`, `demo` and `123456`, in any case.
- **Any other value is installed as it is.** The kickstarter does not apply `MinPasswordLength` to it, so a short
  password in `kickstart.ini` becomes the administrator's password. Leave `Password=` empty and use the generated one,
  or put a long random value there.
- `kickstart.ini` holds passwords in clear text: create it with mode `0600`, never commit it, and remove it after the
  installation. The run's log, `var/log/kickstart.log`, masks passwords.

## The console install (exp:install)

`./console exp:install` (or `php bin/php/install.php`) writes a `kickstart.ini` from its options and runs the
kickstarter ([chapter 7](../install/07-console-install.md#75-the-administrator-password)):

- **Without `--password`**, or with `--random-password`: a random 24-character password.
- **With `--password=<value>`**: used when it is at least `MinPasswordLength` (10) characters long and not one of the
  well-known words above; otherwise it is replaced by a random one, and the summary says why. An empty `--password=`
  is refused.
- A generated password is printed once in the summary at the end and written to `var/log/initial-admin-password`
  (mode `0600`). The summary tells you to change it and delete that file. A password you gave is shown once and stored
  nowhere in clear text.
- `var/log/exp-install-<date>.ini`, the configuration that was used, masks every password.

In a pipeline, take the password from your secret store (`--password="${ADMIN_PASSWORD}"`), or let it be generated and
collect it from the summary; never write it into a script or an image.

## The shipped database seed: publish

The kernel's clean database, the data every installation starts from, has the administrator account with the
password **`publish`**, stored as a bcrypt hash (type 7):

- `share/db_data.dba`
- `kernel/sql/common/cleandata.sql`
- `kernel/sql/sqlite/cleandata.sql`

That is the historical default of the software, and it is in every old guide, so it is the first password anyone
tries. The installers above always overwrite it when they create the site. It is still the password when:

- a database was created by loading those files by hand (with `exp:ezsqlinsertschema`, a database client, or a test
  setup), without running an installer afterwards;
- the setup wizard was told to keep an existing database created that way;
- an old installation (3.x, 4.x, 5.x legacy) was migrated whose administrator never changed it.

Check and change it at once:

```bash
./console exp:resetuserpassword --allow-root-user -u admin -g -l 24
```

No installer of Exponential 6 sets any other fixed password. A common word that merely passes the
10-character rule is just as easy to guess: do not use one on a site that can be reached from outside, not even for a
test.

## Site packages and demo content

The site packages that the installers download (for example the 7x and CJW packages, and the community packages) and
the packages in `var/storage/packages/` carry **no user accounts**: the administrator comes from the database seed and
gets its password from the installer's administrator step. A package that does carry a user account installs it with
the password hash the package contains, so review any package from a third party before installing it.

For developers: the package installer API (`eZSiteInstaller::updateContentObjectAttributes()`) gives a user object it
creates through an install script a random login and the password `publish`. No shipped package uses it for users; a
package of your own that does should set a real password afterwards.

A public demonstration site may publish a sign-in for its visitors. That is the demo's own choice, made after the
installation; such a password must never be reused on a real site.

## After the first sign-in

1. Sign in at the administration (`/admin/user/login` with the default URL access) as `admin` with the password above.
2. Change the password: [Change the administrator password](change-the-admin-password.md).
3. Give the account a real, monitored e-mail address, so that **Forgot your password?** and the "Your password was
   changed" mail reach you.
4. Delete the password file: `rm var/log/initial-admin-password`.
5. Remove `kickstart.ini` if you used one.
6. Follow [Account hygiene](#account-hygiene).

[Chapter 10.1](../install/10-after-installing.md#101-first-login-and-the-administrator-password) has the same steps
in the installation guide.

## Account hygiene

1. Create a personal administrator account with a login other than `admin` (in **Users** > **Administrator users**,
   which carries the **Administrator** role), one per person.
2. Turn on two-step sign-in for every administrator.
3. Retire `admin` as a sign-in: disable it (**Configure user account settings** > clear **Enable user account**,
   after pointing `[UserSettings] UserCreatorID` at an enabled administrator), or keep it enabled with a long random
   password nobody keeps.
4. Keep a written, tested break-glass procedure: root on the server plus
   `./console exp:resetuserpassword --allow-root-user`.

The steps, and why `UserCreatorID` matters, are in
[Change the administrator password: Account hygiene](change-the-admin-password.md#account-hygiene).

## Related pages

- [Change the administrator password](change-the-admin-password.md)
- [Security hardening](security-hardening.md)
- [Chapter 4: Choosing an install method](../install/04-choosing-an-install-method.md),
  [chapter 5: the setup wizard](../install/05-setup-wizard.md), [chapter 6: the kickstarter](../install/06-kickstarter.md),
  [chapter 7: the console install](../install/07-console-install.md)
- [Chapter 13: Security hardening for production](../install/13-security-hardening.md)
