# Cronjobs console

Run a cronjob part, or one script of it, from **Setup > Cronjobs** and read its
output while it runs. Nobody has to open a shell to find out why a job is slow
or whether a new one works.

## What the page shows

- **One line per cronjob, grouped by part.** Each line has the script, where it
  was found, its crontab schedule, its state and a *Run* button. A part can be
  run as a whole, or a single script alone.
- **Available but not activated**: scripts that exist in a cronjob directory
  but are named by no part, so they never run.
- **Output**: the output of the current or last run, followed as it grows.
- **Recent runs**: cronjob, site, start time, duration and error count.
- **Crontab**: what is installed in the crontab now, and suggested entries to
  add. Pick the site under *Run for site*.
- **Stop** ends a running job; **clear** empties the log files.
- A *Cronjob part* filter limits the list to one part.

Open it at `/setup/cronjobs`, or from the Setup menu (`Links[cronjobs]` in
`settings/menu.ini`). The single policy is `setup/managecronjobs`, the same
style as `setup/managecache`.

## Run one script from the shell

The console launches `runcronjobs.php`, which gained `--script`:

```bash
php runcronjobs.php --siteaccess=user --script=notification.php --allow-root-user
php runcronjobs.php --siteaccess user --script notification.php --allow-root-user
php runcronjobs.php --help --allow-root-user     # lists --script and --list
```

Only the file name is used, so a path cannot reach a script outside the
directories `cronjob.ini` names. The script takes the same mutex and writes the
same output as when it runs as part of its part, so it cannot collide with it.
Both `--option value` and `--option=value` now work for every option that takes
a value.

## Settings (`settings/cronjob.ini`, block `[AdminSettings]`)

| Key | Default | Scope | Meaning |
|---|---|---|---|
| `ForbiddenParts[]` | `cluster_maintenance`, `unlock` | global | Parts that cannot be launched from a browser. Both are destructive: one purges cluster storage, the other releases locks other processes hold. |
| `PhpCliPath` | empty | global | Full path of the PHP command line binary used to run `runcronjobs.php`. Empty tries the usual locations. It must be a CLI binary; the one answering the web request cannot run a script. |
| `LogFile` | `cronjobs/output.log` | per siteaccess | Where a launched job's output goes, relative to the var directory of the siteaccess. |
| `ErrorFile` | `cronjobs/error.log` | per siteaccess | Where its errors go. |

To make a script run, name it in `[CronjobSettings] Scripts[]` or in a
`[CronjobPart-<name>]` block; see
[Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md).

## The page does not relaunch a job on reload

An early version relaunched a job each time the page was reloaded, because the
page was the answer to the form post that started it. The launch now ends in a
redirect, so a reload only reads the output again.

## Paging

The list of scripts is paged with
`admininterface.ini [PaginationSettings] ItemsPerPage[setup/cronjobs]`; see
[Where the page sizes live](../../bc/6.0/pagination-settings.md).

## See also

- [September 2026, first half: cronjobs console](../../history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run) and [the page redesign of 14 September](../../history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools)
- [Runnable commands, cronjobs and views (specification)](../../specifications/6.0/runnable-commands-cronjobs-views.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
