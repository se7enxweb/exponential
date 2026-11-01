---
title: "Migrations for administrators"
audience: admin
category: migrations
order: 3
summary: For administrators who apply migrations that developers wrote - reading the status and the dashboard block, the routine before and after a deploy, running from Setup > Migrations or the console, what a failure looks like and what already happened automatically, when to call the developer, rolling back, who may do what, and a checklist.
keywords: migration, migrations, schema change, database change, deploy, exp:migration:migrate, administrator, Setup > Migrations, dashboard block, run, dry run, rollback, verify it's you, migration/read, migration/run, migration/rollback, lock, maintenance mode, checklist
featured: true
type: help
---
# Migrations for administrators

This page is for administrators who run a site's staging or production installation and apply the migrations that
developers wrote. You do not write migrations; you check what is waiting, run it at the right time, and know what to
do when a run fails. Everything here can be done in **Setup > Migrations** or on the console of the server.

## What a migration is, for you

A developer's change to the site (a new table, a content class, some content, a role, a setting) arrives with the
code as a file in `migrations/`. The installation remembers which ones it has applied. A pending migration is one
this installation has not applied yet. Applying it is a **run**: before it, a **caption** (a backup of the database,
the files in `var/` and the settings) is taken; if something goes wrong, the caption is put back automatically.

## Reading the state

| Where | What you see |
|---|---|
| The dashboard block **Migrations** | **Pending**, **Failed** and **Orphans** as numbers, the **Last run** and a note when a run is going on or the installation is locked |
| **Setup > Migrations** (the status) | every migration with its state (**done**, **pending**, **not needed**, **failed**, **unknown**), the lock, the latest runs |
| **History** | every run with its captions, commit, tag and push; **Resume** and **Roll back** |
| The console | `./console exp:migration:status` shows the same |

**not needed** means the migration is not for this environment or this database; it is fine. The upgrade steps of
the kernel are listed first, as migrations of the source `kernel`.

## The routine around a deploy

Before:

1. Read the status: what is pending, and does a failed run need attention first?
2. Open **Dry run**: each pending migration with what it says it will change. Read it; ask the developer about
   anything you did not expect.
3. Check that the developer told you whether a migration switches **maintenance mode** on; plan the time.

The run:

4. After the new code is in place, press **Run N pending** on the status (or run
   `./console exp:migration:migrate --apply`). Leave the boxes as they are: the defaults are the safe ones.
5. Confirm "Verify it's you". The run starts in the background and its page opens; you can close the page, the run
   goes on.

After:

6. The run's page says **done**. The status shows nothing pending.
7. Look at the site where the change shows (a new section, a new field in the editor).
8. The developer may ask for a deploy of the caches; on the console that is `./console exp:velocity deploy`.

## When a run fails

You see **failed** on the run's page and in the dashboard block. By then, automatically:

- the migration that failed was stopped, and the ones after it were not run;
- its database transaction was rolled back, as far as the database can;
- the failed state was kept in a caption for the developer to look at;
- the caption taken before the run was restored: the site is as it was before the run.

What you do: send the developer the run's id and the message on the run's page (also in its log). Do not run it
again until the developer has fixed the migration; then use **Resume**. Call the developer at once when the page says
the restore failed or that no caption was taken: then the site may be half changed.

## Rolling back

**Setup > Migrations > Rollback** takes back:

| Choice | What happens |
|---|---|
| a run | the caption taken before it is restored: database, storage files, settings |
| one migration | its `down()` undoes it, when the developer wrote one |
| an earlier caption (experts) | that caption is restored; you type its id again to confirm |

A caption of the state now is taken first, so a rollback can itself be taken back. Restoring the database brings back
everything as it was then, including what editors published since: agree on it with the editors first.

## Who may do what

| Policy | Allows |
|---|---|
| `migration/read` | the status, the history, the run pages, the dry run, the dashboard block |
| `migration/run` | run, resume, and simulate |
| `migration/rollback` | roll back |

In a new installation only the Administrator role has them. Give `migration/read` to people who should see the state,
and keep `migration/run` and `migration/rollback` on production to the few who apply changes.

## The lock

Only one run, rollback or upgrade writes at a time. While one is going on, the page says **The installation is
locked** and who holds the lock; wait for it. A lock left by a process that ended is taken over by the next run; you
never delete a lock file.

## Checklist

- [ ] The dashboard block shows nothing failed before a deploy.
- [ ] The dry run was read, and every line makes sense.
- [ ] Maintenance windows are agreed for migrations that switch maintenance mode on.
- [ ] The run was started with the default boxes.
- [ ] After the run: nothing pending, the push went through, the site shows the change.
- [ ] On a failure: the run's id and message went to the developer; nothing was run again by hand.

## Where to go next

- [Migrations in the administration](in-the-administration.md)
- [The status and the history](status-and-history.md), [Run, dry run and rollback](run-dry-run-and-rollback.md)
- [When a migration fails](when-a-migration-fails.md)
- Start now: [the tutorial](../../guides/migrations-tutorial.md)

## Related pages

- [Migrations: the hub](README.md)
- [Migrations: the guide](../../guides/migrations.md)
- [Migrations: the tutorial](../../guides/migrations-tutorial.md)
- [PHP migrations](../../guides/migrations-php.md)
- [Migrations in the administration](in-the-administration.md)
- [The migration commands](commands.md)
- [The migration settings](settings.md)
- [The migration file format](../../specifications/6.0/migration-file-format.md)
- [When a migration fails](when-a-migration-fails.md)
- [Troubleshooting migrations](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](migrations-versus-migrating-a-site.md)
- [A tour of the administration](../admin-tour.md)
- ["Verify it's you"](../../guides/verify-its-you.md), [Maintenance mode](../../guides/maintenance-mode.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
