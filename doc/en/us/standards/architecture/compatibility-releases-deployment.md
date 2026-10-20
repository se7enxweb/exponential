---
title: Compatibility, releases and deployment
audience: developer, admin
category: standards
order: 220
summary: The backward compatibility policy of Exponential 6.0.x, how versions and releases are numbered and published, and the standard order of steps for deploying a change or an upgrade.
keywords: backward compatibility, BC, breaking change, deprecation, upgrade notes, doc/en/us/bc, changelog, release, version, tag, semantic versioning, ezinfo.php, extension.xml, filelist.md5, manifest, deployment, deploy, autoloads, ezcache, PHP-FPM, Velocity, exp:velocity deploy, dbupdate, upgrade check
type: reference
---
# Compatibility, releases and deployment

This page is for developers who change Exponential 6.0.x or an extension and for administrators who upgrade and
deploy it. It answers three questions: what counts as a breaking change and what has to accompany one, how versions
are numbered and released, and in which order a change is deployed so that no server keeps running old code. Each
rule says **must** or **should**, how the repository implements it, and where it is not met yet.

## Backward compatibility policy

### What counts as a break

A change breaks compatibility when an installation or extension that worked before needs an action to keep working
or behaves differently without being asked to. In particular:

| Kind | Examples |
|---|---|
| Settings | a setting removed or renamed, its meaning or default changed, a section moved to another file |
| Templates | a template removed or moved, a variable no longer passed to a template, an override `Source` path changed |
| PHP API | a public class, method or function removed or renamed, a signature or return type changed, a class moved into a namespace without the old name |
| Database | a table or column added, removed or changed (an upgrade needs SQL) |
| Command line | a command, option, output format or exit code changed |
| Behavior | a default changed (security headers, cookies, access checks), a permission now required where none was |
| Files | a file that sites copy or include (views, cronjob parts, scripts) changed in form |

### The rules

- **Every break must have upgrade notes in `doc/en/us/bc/<series>/`.** The directory exists per series, from `doc/en/us/bc/4.1/`
  to `doc/en/us/bc/6.0/` (84 pages in `doc/en/us/bc/6.0/` at the time of writing). A page opens with an **In short** table with
  the rows *What changed*, *Who is affected*, *How to check* and *How to fix*; examples:
  [Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md),
  [exp:ini](../../bc/6.0/console-exp-ini.md). Changes that span many areas are collected per period with a deploy
  order and a checklist table, for example [Behaviour changes of 1 and 2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md).
- **New behavior should be opt-in, bug fixes need not be.** A new feature arrives switched off or with a default that
  keeps the old result; examples are the class-based views ("the old way still works", plain-file views keep running)
  and the Velocity engine options in [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md)
  ("Nothing changes by itself. New, opt-in: ...").
- **New settings must have a safe default** in the shipped `settings/<file>.ini`, a comment there, a section on the
  feature page in `doc/en/us/features/6.0/` and a line in the changelog.
- **Identifiers are not renamed.** Existing `eZ*` and `ezp*` classes, tables, settings and paths stay
  (see [Layering and naming rules](layering-and-naming.md)); the 2026 product rename changed only text
  ([The product is called Exponential](../../features/6.0/rebranding-to-exponential.md)).
- **Moved code keeps its entry point.** When the code of a script, view or cronjob part moved into a class in 6.0.15,
  every file kept its path and became a one-line call, so includes, cron entries and `--help` output did not change.

### Deprecation

- A replaced API stays available and is marked `@deprecated` with the replacement (54 `@deprecated` tags in
  `kernel/` and `lib/`, for example `eZContentObject::contentObjectListRelatingThis()`, replaced by `reverseRelatedObjectList()`, and `eZUser::isLoggedIn()`, which still works and writes a strict-mode deprecation notice through `eZDebug::writeStrict()`).
- A renamed command keeps its old name as a deprecated alias that still works
  ([platform package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md)).
- A removal is announced in the changelog's **Removed** section and in a `doc/en/us/bc/` page.

### Known exceptions

- **Security defaults changed without opt-in.** In September and October 2026 security headers, `HttpOnly` and
  `SameSite` session cookies, a stricter upload check and new access requirements for some remote services were turned
  on by default ([Security defaults](../../specifications/6.0/security-defaults-2026-09.md),
  [Behaviour changes of 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)). Security fixes are
  treated like bug fixes: on by default, documented with how to undo them deliberately.
- **Accounts without an `ezuser_setting` row** cannot sign in since August 2026, and override blocks follow
  `Priority` since July 2026 ([Behaviour changes of July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md)).
- `@deprecated` APIs inherited from eZ Publish 4 have no removal date; no removal is announced for the 6.0 line.

## Releases and versioning

### Version numbers

- The kernel version is in `lib/version.php` (`VERSION_MAJOR = 6`, `VERSION_MINOR = 0`, `VERSION_RELEASE = 15`) and
  in the database row `ezsite_data` `ezpublish-version`, which the update SQL writes (for example `6.0.15stable`).
- Releases of the 6.0 line are tagged `v6.0.<n>`. The newest tag in the repository is `v6.0.14`; the 6.0.15 line is
  in development and has no tag yet ([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).
- **Only the last position counts on.** After `x.y.9` comes `x.y.10`, never `x.(y+1).0`; moving a higher position is a
  decision about what the release means. Tags are sorted with `git tag -l 'v*' --sort=version:refname` (or `sort -V`),
  because a plain name sort puts `0.1.10` before `0.1.8`. The rule is written down for extensions in
  [Extensions, section 7](../../guides/extensions.md) and for the Velocity engine in
  [its changelog](../../../../changelogs/extensions/exponential-velocity.md).

### Tags are permanent

**A published tag must never be moved, deleted or re-cut, and a published release must never be replaced.** The
packages are installed through Composer, which records a version once; a changed tag would give two different
packages the same number. A bad release is corrected by the next one, whose notes say what was wrong. Before tagging:
the working tree is clean, the tests pass, the version fields are bumped, the manifest matches.

### Changelogs

- One page per release in `doc/changelogs/<series>/<version>.md` (`doc/changelogs/6.0/6.0.0.md` to `6.0.15.md`),
  extensions in `doc/changelogs/extensions/`. Entries are grouped as **Added**, **Updated**, **Removed** and
  **Renamed**; 6.0.15 additionally by period. Each page starts with **Upgrading** and links the `doc/en/us/bc/` pages.
- Commit subjects use the same prefixes (`Added:`, `Updated:`, `Fixed:`, `Removed:`, `Renamed:`); the CI job
  *Commit messages* of `.github/workflows/quality.yml` checks them with `.githooks/check-commit-range` (a warning only
  for outside contributors).
- The full per-commit history is kept in `doc/en/us/history/ledger/`.

### Extension versions

An extension release **must** write its version, without the `v`, into `ezinfo.php` (`'Version'`) and
`extension.xml` (`<metadata><version>`), and into `composer.json` if it has a `version`, in the commit that the tag
points at; never tag first and bump afterwards. Check with `php -l ezinfo.php` and `xmllint --noout extension.xml`.
Extensions shipped inside the kernel repository carry the kernel version. Reference:
[extension metadata](../../specifications/6.0/extension-metadata.md).

### The file manifest

`share/filelist.md5` lists the checksum of every shipped file (about 9,500 lines). The admin upgrade check and
`php bin/php/checkmanifest.php --all` compare an installation against it. It **must** match the files of every
commit on the main branch: the CI job *File manifest (share/filelist.md5)* in `.github/workflows/quality.yml` fails on
`main` (and warns on a pull request) when it does not, and `php bin/php/checkmanifest.php --fix` (or
`make manifest-fix`) refreshes it. An extension with its own `share/filelist.md5` refreshes it in the same release.
See [File consistency check](../../features/6.0/file-consistency-check.md).

### Known gaps

- The version constants in `lib/version.php` already say `15` with `VERSION_STATE = 'stable'` while 6.0.15 is not
  released.
- `doc/changelogs/README.md` still points to an old upstream release page instead of `doc/changelogs/6.0/`.

## Deployment standard

The two guides are the procedure; this section is the summary and the reasons for the order.
[Deploying](../../guides/deploying.md) covers serving a site with Apache and PHP-FPM, Velocity or FrankenPHP and
shipping a change; [Upgrading](../../guides/upgrading.md) covers moving from 4.x, 5.x or an earlier 6.0.x.

### Shipping a code change

After a change to a PHP class, an operator or an INI file, the steps **must** run in this order:

1. **Regenerate the class maps.** `php bin/php/ezpgenerateautoloads.php -e` (extensions); with `-k` for the kernel
   when a kernel class was added or renamed. `-k` walks the whole installation, so working copies inside it are
   excluded through `.autoloadignore` or `--exclude=<dir>`.
2. **Clear the settings cache.** `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` (the tag clears both
   the `ini` and the `global_ini` caches).
3. **Clear template caches** when templates or template paths changed: `template`, `template-override`,
   `translation`, `design_base` (`--clear-id=...`).
4. **Load the new code into the web server.** Reload the PHP-FPM service that really serves the site (on a server with
   several PHP versions this is not always the one named `php-fpm`; `velocity.ini [DeploySettings] PhpFpmService`,
   `auto` shipped) and restart Velocity (`./console exp:velocity restart`), whose workers keep the classes loaded at
   warm-up.
5. **Clear the page caches last**: the content view cache (`--clear-id=content`), the HTTP cache, and Velocity's
   response cache (`./console exp:velocity cache clear`). Clearing them before step 4 lets old code cache a page again.

`./console exp:velocity deploy` runs all of this in order (fourteen steps, PASS, FAIL or SKIP per step, exit status
1 on the first failure); `--dry-run` shows the steps, `--kernel` adds the kernel class map, `--packer` clears packed
scripts and styles. Details: [Deploying a PHP change](../../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).
A template or stylesheet change needs only the template caches and Velocity's response cache, not a restart.

### Upgrading an installation

1. **Back up** files and database.
2. **Put the new code in place**, regenerate the class maps and clear all caches
   (`php bin/php/ezcache.php --clear-all --allow-root-user`), then reload PHP-FPM and restart Velocity.
3. **Apply the database update once, whole, in order**: `update/database/<driver>/6.0/dbupdate-6.0.0-6.0.15.sql`
   (MySQL, PostgreSQL and SQLite files exist; from 5.4, `dbupdate-5.4.0-6.0.0.sql` first), then the scripts in
   `update/common/scripts/6.0/` the guide names (`createaudittables.php`, `movetrashrecords.php`; the directory also
   ships `createapikeytable.php`).
4. **Read the checklist of every period passed** in `doc/en/us/bc/6.0/` and the changelogs.
5. **Check extensions**: `./console exp:checkclasses` reports classes PHP refuses to load.
6. **Run the upgrade check**: Setup > System upgrade, *Check file consistency* against `share/filelist.md5` and
   *Check database consistency* against the schema files; both only read. From a shell:
   `php bin/php/checkmanifest.php --all --extensions` ([The upgrade check](../../guides/upgrade-check.md)).

### Rules

- A deployment **must** reach every PHP runtime that serves the site; clearing caches alone leaves PHP-FPM and
  Velocity on the old classes.
- Database changes **must** ship as SQL for every supported driver in `update/database/<driver>/6.0/` together with
  the schema in `share/db_schema.dba`, and **must** say how to proceed on a database that already has some of them
  (for the 6.0.15 file: a statement for an existing column fails with its name, and only that statement is skipped).
- A new class referenced from a shipped INI file **should** be guarded with `class_exists()` in the kernel, because a
  Velocity worker started before the deploy does not know it.

### Known gaps

- The database update files are applied by hand; there is no migration runner that records which statements ran.
- `exp:velocity deploy` covers PHP-FPM and Velocity on one machine; several web servers are deployed one by one.

## Related pages

- [Kernel architecture](kernel-architecture.md), [Layering and naming rules](layering-and-naming.md)
- [Deploying](../../guides/deploying.md), [Upgrading](../../guides/upgrading.md), [The upgrade check](../../guides/upgrade-check.md)
- [Velocity engines](../../bc/6.0/velocity-engines.md), [Caches](../../guides/caches.md)
