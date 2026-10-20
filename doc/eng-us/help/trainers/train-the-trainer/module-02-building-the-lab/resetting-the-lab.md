---
title: "Lesson 2.6: Resetting the lab between sessions"
audience: trainer, admin
category: training
order: 26
summary: Bring a training installation back to a known state before the next group, reinstall with --force after a dry run, keep a copy of the SQLite database, remove training accounts, and never reset a site that holds real data. 35 minutes.
keywords: reset, reinstall, force, dry-run, SQLite, backup, copy, training lab, clean state, remove accounts, maintenance
type: help
---
# Lesson 2.6: Resetting the lab between sessions

After a course, the training sites are full of half-finished articles, test users and changed settings. The next
group needs a clean start. With SQLite and `exp:install` a reset takes minutes, but it also deletes everything, so it
must never be pointed at a site that matters. This lesson teaches a safe routine.

> **Warning**
> A reinstall with `--force` replaces the database and the settings of the installation. Only ever run it in a
> directory that you created for training.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 2: Building the training lab](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers who run repeated courses |
| Prerequisites | [Lesson 2.3](one-install-per-learner.md) |

## Learning objectives

After this lesson, learners can:

- decide between a full reinstall and a restore of a saved database file;
- run `exp:install --dry-run` and then `--force` on a training directory;
- save and restore a "golden" copy of a training database;
- remove training accounts from a shared site.

## Trainer's notes

### How to present it

1. Two strategies (4 minutes):
   - **Reinstall**: `exp:install --force` replaces the database (with the default `--db-action=remove`) and the
     settings. Clean, always the same, takes as long as an install.
   - **Golden copy**: install once, prepare the state you want (sample content, learner accounts), stop the server
     and copy `var/storage/sqlite3/exponential.db` and `var/storage/` aside. To reset, stop the server, copy them back
     and clear the caches.
2. Show the safety net (3 minutes): without `--force` the command refuses an existing installation. Always run
   `--dry-run` first; it works on an existing installation without `--force`.
3. Explain the caches (3 minutes): after a restore, clear them with `php bin/php/ezcache.php --clear-all`.

### Common questions

**Does a reinstall keep my learners' accounts?** No. Everything in the database is replaced. Create accounts again,
or use the golden copy.

**What about uploaded images?** They are files under `var/` (the site's storage directory), not in the database.
Copy the storage directory with the database when you keep a golden copy.

### Pitfalls

- **The wrong directory.** Check `pwd` before every `--force`.
- **The server still running.** Stop the server before copying an SQLite file, or the copy may be inconsistent.
- **Caches.** A restored database with old caches shows old pages. Clear all caches after a restore.

## Hands-on exercise

On the training installation from lesson 2.3:

1. Stop the server: `php bin/php/console exp:velocity stop`.
2. Make a golden copy: `mkdir -p ../golden` and `cp -a var/storage ../golden/`.
3. Start the server, sign in and create one test article anywhere (any title).
4. Stop the server. Restore: `cp -a ../golden/storage/. var/storage/`.
5. Clear the caches: `php bin/php/ezcache.php --clear-all`.
6. Start the server and check that the test article is gone.
7. Now test a full reinstall: `pwd` (check the directory!), then
   `php bin/php/console exp:install --dry-run --url=http://127.0.0.1:8087`.
8. If the dry run passes: `php bin/php/console exp:install --force --url=http://127.0.0.1:8087` and note the new
   generated password.

## Expected result

- After the restore, the test article no longer exists.
- After the reinstall, the site is the fresh `sevenx_multisite` site with a new administrator password, and
  `var/log/initial-admin-password` exists again.
- The trainer adds the reset routine and its duration to the course checklist.

## Quiz

1. What does `exp:install --force` replace?
2. Why stop the server before copying an SQLite database file?
3. What must you run after restoring a database file?
4. Does `--dry-run` need `--force` on an existing installation?

<details>
<summary>Answers</summary>

1. The database (with the default `--db-action=remove`) and the settings of the installation.
2. The server may be writing to the file; a copy made then can be inconsistent.
3. Clear the caches: `php bin/php/ezcache.php --clear-all` ([Caches](../../../../guides/caches.md)).
4. No. A dry run works without `--force` and changes nothing.

</details>

## Further reading

- [The console install](../../../../install/07-console-install.md): `--force`, `--dry-run` and `--db-action`.
- [Caches](../../../../guides/caches.md): what the caches are and how to clear them.
- [Live demo safety](../../../advocates/live-demo-safety.md): preparing a clean demo installation.

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
