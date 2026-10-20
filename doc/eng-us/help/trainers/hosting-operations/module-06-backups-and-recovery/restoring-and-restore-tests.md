---
title: "Lesson 6.3: Restoring a site and restore tests"
audience: trainer, hosting, admin
category: training
order: 63
summary: Learners restore a complete site from the backup of lesson 6.1 onto a second installation, in the documented order, and check the result as a restore test. 50 minutes.
keywords: hosting and operations, restore, restore test, maintenance mode, exp:velocity stop, ownership, clear caches, autoloads, checks after restore, second machine, same release
type: help
---
# Lesson 6.3: Restoring a site and restore tests

"A backup that was never restored is a hope, not a backup." In this lesson learners prove their backup from lesson 6.1
by restoring it onto a second training installation, in the order the installation book gives, and checking the
result the way a restore test should.

> **Note**
> Restore onto a second training installation, never over a site someone else uses.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 6: Backups and recovery](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | System administrators and support staff |
| Prerequisites | [Lesson 6.1](what-to-back-up.md) with its backup; a second training installation of the same release on the same database engine |

## Learning objectives

After this lesson, learners can:

- decide what is restored and what will be lost, and back up the current state first;
- restore a site in the documented order: maintenance on, stop the engine, database, files, ownership, caches and
  autoloads, start, clear the response cache, maintenance off;
- explain why the target must run the same release and `composer.lock` as the backup;
- check a restored site: content, images, sign-in, a publish, the logs;
- run and record a restore test on another machine.

## Trainer's notes

### How to present it

Source: [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md) and
[Chapter 10, Restore](../../../../install/10-after-installing.md#restore).

1. **Before you restore** (4 minutes). A restore brings the whole site back to the moment of the backup; for a single
   deleted page, look at the trash and older versions first. Back up the current state. Restore onto the same release;
   an older database needs the upgrade steps afterwards.
2. **The order** (6 minutes). Maintenance on; under Velocity stop the server so no worker holds the old database;
   restore the database with its engine's tool and unpack the files; fix owner and mode; clear every cache and
   regenerate the autoloads; start Velocity, clear its response cache, switch maintenance off. Under PHP-FPM, reload
   the pool if code or settings changed.
3. **Checks** (3 minutes). Front page and a few deep pages, images, administration sign-in, a test publish, the
   newest lines of `error.log`.
4. **Restore tests** (2 minutes). On another machine, at least once and after every major upgrade; record the date,
   the backup used, the time it took and what was checked.

### Common questions

**Restored files belong to root. Is that a problem?** Yes: the web server cannot replace them later and the site fails
in odd places. Run as the site's user, or fix ownership afterwards.

**The restored site shows the wrong address in links.** `settings/override/` came from another installation; on a test
machine, adjust the site URL settings, or restore only `var/storage` and the database.

**How long does a restore take?** Measure it in the test; the policy needs that number.

### Pitfalls

- **Unpacking `settings/override/` over the second installation** replaces its database settings with those of the
  first. For the exercise, unpack only `var/storage` and keep the second installation's settings.
- **Forgetting to stop Velocity** before restoring a SQLite file by replacement.
- **Releases that differ.** Install the second installation from the same release as the first.

## Hands-on exercise

Use a second training installation of the same release and engine (for example `/var/www/training2`), as the site's
user. `<first>` is the path of the installation you backed up in lesson 6.1.

1. In the second installation, take a quick backup of its current database (lesson 3.4) so the exercise can be undone.
2. Switch maintenance mode on: `php bin/php/maintenance.php on --message="Restore practice" --until=30m`.
3. Stop the engine: `php bin/php/console exp:velocity stop`.
4. Restore the database from `<first>/var/training-backup/` with the engine's tool (SQLite: `.restore` into this
   installation's database file).
5. Unpack only the storage files: `tar xzf <first>/var/training-backup/files-<date>.tar.gz var/storage`.
6. Check ownership: `ls -l var/storage | head` (fix with `chown` to the site's user if needed).
7. Clear caches and regenerate autoloads:

   ```bash
   php bin/php/ezcache.php --clear-all
   php bin/php/ezpgenerateautoloads.php -e
   ```

8. Start the engine and clear its response cache:

   ```bash
   php bin/php/console exp:velocity start
   php bin/php/console exp:velocity cache clear
   ```

9. Switch maintenance mode off: `php bin/php/maintenance.php off`.
10. Check: your course folder and articles from the first installation are there; an image shows; you can sign in to
    the administration; publish a test change; `exp:logs --tail=var/log/error.log --lines=10` shows nothing new.
11. Record the restore test: date, backup file, minutes taken, checks passed.

## Expected result

- The second installation shows the content of the first, with its images.
- Sign-in and a test publish work.
- `error.log` has no new errors.
- Each learner has a restore-test record with the time it took.
- The trainer checks by opening one learner's restored course folder on the projector.

## Quiz

1. Before restoring a whole site, what do you check about the lost data?
2. Why is Velocity stopped before the database is restored?
   A. to save memory B. so no worker holds the old database C. to clear the response cache D. it is not needed
3. Why must the target run the same release as the backup?
4. Put in order: clear caches, maintenance on, restore database, maintenance off.
5. What makes a restore test useful for the backup policy?

<details>
<summary>Answers</summary>

1. That content published since the backup will be lost; for a single deleted page, the trash and older versions come
   first.
2. **B.** ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md#the-restore-step-by-step))
3. A database from an older release needs the upgrade steps; code and data must match.
4. Maintenance on, restore database, clear caches, maintenance off.
5. It proves the backup can be restored and measures how long it takes, a number the policy needs.

</details>

## Further reading

- [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)
- [Runbook: restore from backup](../../../hosting/runbooks/restore-from-backup.md)
- [Chapter 10, Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore)
- [Moving a site between hosts](../../../hosting/migrations/moving-a-site-between-hosts.md): the same steps, another
  purpose.

## Related pages

- [Module 6: Backups and recovery](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
