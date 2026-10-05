# Cronjobs console

This page is for administrators who want to see, run and debug cronjobs without a shell. **Setup > Cronjobs** runs a
cronjob part, or one script of it, and shows its output while it runs. Nobody has to open a shell to find out why a
job is slow or whether a new one works.

## Run a job from the browser

1. Open `/setup/cronjobs`, or **Setup > Cronjobs** (`Links[cronjobs]` in `settings/menu.ini`). You need the policy
   `setup/managecronjobs` (the same style as `setup/managecache`).
2. Pick the site under **Run for site**.
3. Click **Run** on a whole part, or on a single script.
4. Watch **Output**: the output of the current or last run, followed as it grows. **Stop** ends a running job.

## What the page shows

- **One line per cronjob, grouped by part.** Each line has the script, where it was found, its crontab schedule, its
  state and a **Run** button. A **Cronjob part** filter limits the list to one part.
- **Available but not activated**: scripts that exist in a cronjob directory but are named by no part, so they never
  run.
- **Output**: the output of the current or last run.
- **Recent runs**: cronjob, site, start time, duration and error count.
- **Crontab**: what is installed in the crontab now, and suggested entries to add.
- **Clear** empties the log files.

The launch ends in a redirect, so reloading the page only reads the output again. (An early version relaunched a job
on each reload, because the page was the answer to the form post that started it.)

## Run one script from the shell

The console launches `runcronjobs.php`, which gained `--script`:

```bash
php runcronjobs.php --siteaccess=user --script=notification.php --allow-root-user
php runcronjobs.php --siteaccess user --script notification.php --allow-root-user
php runcronjobs.php --help --allow-root-user     # lists --script and --list
```

Only the file name is used, so a path cannot reach a script outside the directories `cronjob.ini` names. The script
takes the same mutex and writes the same output as when it runs as part of its part, so the two cannot collide. Both
`--option value` and `--option=value` now work for every option that takes a value.

## Activate a script

Name it in `[CronjobSettings] Scripts[]` or in a `[CronjobPart-<name>]` block of `cronjob.ini`; see
[Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md).

## Settings

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/cronjob.ini` | `AdminSettings` | `ForbiddenParts[]` | `cluster_maintenance`, `unlock` | global | Parts that cannot be launched from a browser. Both are destructive: one purges cluster storage, the other releases locks other processes hold. |
| `settings/cronjob.ini` | `AdminSettings` | `PhpCliPath` | empty | global | Full path of the PHP command line binary used to run `runcronjobs.php`. Empty tries the usual locations. It must be a CLI binary; the one answering the web request cannot run a script. |
| `settings/cronjob.ini` | `AdminSettings` | `LogFile` | `cronjobs/output.log` | siteaccess | Where a launched job's output goes, relative to the var directory of the siteaccess |
| `settings/cronjob.ini` | `AdminSettings` | `ErrorFile` | `cronjobs/error.log` | siteaccess | Where its errors go |
| `settings/admininterface.ini` | `PaginationSettings` | `ItemsPerPage[setup/cronjobs]` | see [page sizes](../../bc/6.0/pagination-settings.md) | global | Page size of the script list |

## Related pages

- [Runnable commands, cronjobs and views (specification)](../../specifications/6.0/runnable-commands-cronjobs-views.md), [commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Content jobs](content-jobs.md), [Velocity scheduler](velocity-scheduler.md), [Notifications (the cronjob part `notification`)](notifications.md)
- [RSS import cleanup cronjob part](../../bc/6.0/cleanuprss.md), [console commands](../../bc/6.0/console.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [September 2026, first half: cronjobs console](../../history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run), [the page redesign of 14 September](../../history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools), [June 2026, first half](../../history/2026/2026-06a.md)
