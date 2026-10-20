---
title: "Lesson 2.3: One installation per learner with exp:install"
audience: trainer, admin
category: training
order: 23
summary: Install a complete training site with one command, check it first with --print and --dry-run, find the generated administrator password, and repeat the install the same way for every learner. 45 minutes.
keywords: exp:install, console install, kickstarter, SQLite, dry run, print, initial-admin-password, training lab, sevenx_multisite, one command
type: help
---
# Lesson 2.3: One installation per learner with exp:install

The safest lab gives every learner their own installation: nobody can break a neighbour's exercise, and every
learner can be an administrator. Exponential makes this cheap: `exp:install` installs a complete site on SQLite with
one command and no file to write first. This lesson teaches trainers to run it, check it and read its output.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 2: Building the training lab](README.md) |
| Duration | 45 minutes (10 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Trainers who prepare labs |
| Prerequisites | [Lesson 2.2](getting-the-code-for-a-class.md): a directory with the code |

## Learning objectives

After this lesson, learners can:

- install Exponential with `php bin/php/console exp:install` and its most useful options;
- check the configuration with `--print` and the whole run with `--dry-run` before installing;
- find the administrator login and the generated password, and remove the password file after the first login;
- explain why the command refuses to install over an existing installation without `--force`.

## Trainer's notes

### How to present it

1. Show the command in its simplest form (3 minutes): `php bin/php/console exp:install` installs the
   `sevenx_multisite` site package on SQLite (`var/storage/sqlite3/exponential.db`) with the administrator `admin`
   and a generated password, shown once at the end.
2. Show `--print` (2 minutes): it prints the configuration, passwords masked, and writes nothing.
3. Show `--dry-run` (2 minutes): it checks the database and fetches the packages into a temporary repository, then
   stops before anything is written. Success ends with "Dry-run completed: database and remote packages verified.
   Stopped after SiteDetails, nothing written."
4. Explain the password (3 minutes): without `--password` a 24-character password is generated, shown in the
   summary and written to `var/log/initial-admin-password` (mode 0600). After the first login, change it and delete
   the file. Well-known passwords such as `publish` or `admin` are never installed.

### Common questions

**Can I give every learner the same password?** You can pass `--password=` with at least 10 characters that is not
well known. For a classroom it is better to let each installation generate its own and have learners change it as
their first exercise.

**What does `--url` do?** It is the address every siteaccess's links are built from. For a lab served on the same
machine, `--url=http://127.0.0.1:8087` matches the built-in server's default port (lesson 2.4).

**What if the install fails?** It prints which step stopped and why, and leaves the site in maintenance mode.
`var/log/setup.log` has the details; `php bin/php/maintenance.php off` ends maintenance after the cause is fixed.

### Pitfalls

- **Running as root.** Run the command as the user who will serve the site.
- **Re-running in the same directory.** The command refuses: "This directory already holds an installation". That
  is a safety net; lesson 2.6 covers resets.
- **A password in the shell history.** For database passwords prefer the environment variable
  `EXP_INSTALL_DB_PASSWORD` to an option (not needed for SQLite).

## Hands-on exercise

In the `editor-course` directory from lesson 2.2 (or any fresh code directory):

1. `php bin/php/console exp:install --print --url=http://127.0.0.1:8087 --title="Training site"` and read the
   printed configuration: find `Site_package`, `Type=sqlite3` and `EditorAccess`.
2. `php bin/php/console exp:install --dry-run --url=http://127.0.0.1:8087 --title="Training site"`. Wait for the
   "Dry-run completed" line.
3. Install: `php bin/php/console exp:install --url=http://127.0.0.1:8087 --title="Training site"`.
4. Read the summary: the site address, the administration login and the password. Copy the password to a safe place.
5. `ls -l var/log/initial-admin-password` and `ls -l var/storage/sqlite3/`: the password file (owner-only) and the
   database file exist.
6. Run the install command once more without `--force` and read the refusal message.
7. Note how long the install took on this machine; this goes into your course timing.

## Expected result

- The installation summary showed the site address, `admin` as the login and a generated password.
- `var/storage/sqlite3/exponential.db` exists; `var/log/initial-admin-password` exists with mode `-rw-------`.
- The second run without `--force` stopped with "This directory already holds an installation" and changed nothing.

## Quiz

1. What does `exp:install` install when no option is given?
2. What is the difference between `--print` and `--dry-run`?
3. Where is a generated administrator password written, and what do you do with that file after the first login?
4. Why does a second run in the same directory stop?
5. Is `publish` accepted as the administrator password?

<details>
<summary>Answers</summary>

1. The `sevenx_multisite` site package on SQLite, administrator `admin`, a generated password.
2. `--print` shows the configuration without any check of the database or the network; `--dry-run` connects to the
   database and fetches the packages, then stops before writing
   ([The console install, 7.6](../../../../install/07-console-install.md#76-dry-run-and-print)).
3. In `var/log/initial-admin-password`; change the password, then delete the file.
4. `settings/override/site.ini.append.php` already holds `[DatabaseSettings]`; without `--force` the command refuses
   to replace an installation.
5. No. Well-known passwords are replaced by a generated one, and the summary says why.

</details>

## Further reading

- [The console install: exp:install](../../../../install/07-console-install.md): every option.
- [Install in one command](../../../../features/6.0/install-in-one-command.md): the feature page.
- [The kickstarter](../../../../install/06-kickstarter.md): the same install from a reviewed `kickstart.ini`.
- [After installing](../../../../install/10-after-installing.md): what to do next.

## Related pages

- [Module 2: Building the training lab](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
