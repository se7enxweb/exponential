---
title: Resetting and rebuilding your study site
audience: student
category: learning
order: 15
summary: How a learner saves a study site before a risky exercise and starts over afterwards - a backup of the SQLite database and the files, a restore, a fresh install with exp:install --force, a dry run first, keeping your own work in Git, and what a reinstall removes.
keywords: reset, rebuild, reinstall, exp:install --force, dry-run, backup, restore, sqlite, snapshot, study site, start over, practice
type: help
---
# Resetting and rebuilding your study site

A study site is meant to be broken. This page shows you how to save it before a risky exercise, how to get it back,
and how to start from a clean installation in a few minutes. All commands come from the installation pages and are
run from the installation root.

## Contents

1. [Three ways back](#three-ways-back)
2. [Save before a risky exercise](#save-before-a-risky-exercise)
3. [Restore a saved state](#restore-a-saved-state)
4. [Start over with a fresh install](#start-over-with-a-fresh-install)
5. [Keep your own work outside the installation](#keep-your-own-work-outside-the-installation)
6. [What a reinstall removes](#what-a-reinstall-removes)

## Three ways back

| You want to | Use | Time |
|---|---|---|
| Undo one content change | The item's versions or the trash ([Publishing, versions and the trash](../publishing-and-versions.md)) | Seconds |
| Return to a state you saved | A backup of the database and the files | Minutes |
| Start again from nothing | `exp:install --force` | A few minutes |

Try the smallest one first. Versions and the trash are part of the editor path, and using them is an exercise in
itself.

## Save before a risky exercise

A complete backup is the database **and** the files: `var/storage` (uploads and image variations),
`settings/override` and `settings/siteaccess` ([Database support](../database-support.md#backup-and-restore)).

For the default SQLite database of `exp:install` (`exponential.db` under `var/storage/sqlite3/`), the database page
gives an online, consistent backup:

```bash
mkdir -p ../study-backups/2026-10-20
sqlite3 var/storage/sqlite3/exponential.db ".backup '../study-backups/2026-10-20/exponential.db'"
cp -a var/storage settings/override settings/siteaccess ../study-backups/2026-10-20/
```

The first two lines follow [Database support](../database-support.md#backup-and-restore); the folder names are
examples. Keep the backups outside the installation, so a reinstall cannot remove them. If you installed with the
setup wizard, your SQLite file has the name you gave it (the default there is `sqlite.db`,
[Installing Exponential 6.0](../../INSTALL.md#the-steps)).

On a study site for other databases, the same page lists the dump command of each.

## Restore a saved state

From [Database support](../database-support.md#backup-and-restore):

1. Switch on maintenance mode: `php bin/php/maintenance.php on --allow-root-user` (leave out `--allow-root-user`
   when you are not `root`).
2. Restore the database: `sqlite3 var/storage/sqlite3/exponential.db ".restore '../study-backups/2026-10-20/exponential.db'"`.
   Never copy a database file that is in use.
3. Copy back the folders you saved.
4. Clear every cache: `php bin/php/ezcache.php --clear-all --allow-root-user`, and with Velocity also
   `php bin/php/console exp:velocity cache clear --allow-root-user`.
5. Switch maintenance mode off again (see [Maintenance mode](../../guides/maintenance-mode.md)).

## Start over with a fresh install

`exp:install` refuses to run over an existing installation unless you add `--force`
([Install in one command](../../features/6.0/install-in-one-command.md#all-options)). Check first what it would do:

```bash
php bin/php/console exp:install --dry-run --random-password --url=http://localhost:8087
php bin/php/console exp:install --force --random-password --url=http://localhost:8087
```

`--dry-run` checks the configuration and the packages and installs nothing. Then the real run replaces the study
site and prints a new administrator password. Add `--email=` with an address at `example.invalid`, so the
administrator has no real mailbox; without it the installer uses its own default address.

For another database, `--db-action=remove` empties the named database first, so use a database that holds nothing
you need ([Getting started](../../guides/getting-started.md#32-other-databases)).

## Keep your own work outside the installation

Everything you want to keep across reinstalls belongs in Git, outside the folders a reinstall writes:

| Your work | Where to keep it |
|---|---|
| A design or a module you wrote | An extension of your own in `extension/<name>`, in its own Git repository ([project 8](project-08-your-first-extension.md)) |
| Notes and the journal | A separate folder and repository ([Keeping a study journal](keeping-a-study-journal.md)) |
| Content you want to install again | A package you export ([Packages](../../guides/packages.md)) |
| Changes to the kernel itself | A branch of your clone ([Installing from Git clones for study](installing-from-git-clones-for-study.md)) |

## What a reinstall removes

When it finds an installation, `exp:install` says that it "would replace its database and settings" and asks for
`--force` (`bin/php/install.php`). So, after a forced reinstall:

- the content and users of the old database are gone;
- the settings the installer writes (in `settings/override/` and `settings/siteaccess/`) are written anew, so a
  setting you changed by hand is lost, and an extension of your own must be switched on again
  ([Extensions](../../guides/extensions.md#4-switch-it-on)).

Your Git branches and repositories are not part of the database or the settings. Treat anything else you need as
something to back up first.

## Related pages

- [Installing a study site in one command](installing-a-study-site-in-one-command.md)
- [Sharing one study site with a class](sharing-one-study-site-with-a-class.md)
- [Database support](../database-support.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
