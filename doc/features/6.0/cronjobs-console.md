# Cronjobs console

This page is for administrators who want to see, run and debug cronjobs without a shell. **Setup > Cronjobs** runs a
cronjob part, or one script of it, and shows its output while it runs. Nobody has to open a shell to find out why a
job is slow or whether a new one works.

The complete user guide, with every part of the page, scheduling with the crontab, the shell commands, the logs and
troubleshooting, is [Cronjobs: running, scheduling and following them](../../guides/cronjobs.md).

## Run a job from the browser

1. Open `/setup/cronjobs`, or **Setup > Cronjobs** (`Links[cronjobs]` in `settings/menu.ini`). You need the policy
   `setup/managecronjobs` (the same style as `setup/managecache`).
2. Pick the site under **Run for site**.
3. Click **Run part** on a part's card, or open its **Scripts** line and click **Run** beside one script.
4. Watch **Output**: the output of the current or last run, followed as it grows. **Stop running job** ends it.

To run a part from a shell instead, click **Copy** beside its **Shell command** and paste it into a terminal on the
server. The command names the site chosen under **Run for site**.

## What the page shows

From top to bottom:

- **Status bar**: *Idle*, or *Running: part* with the site, the process id and the time it has run so far. Its
  buttons are **Stop running job**, **Clear logs** (empties the log files) and **Pause all parts** (or **Resume all
  parts**); while every part is paused a notice above the bar says since when and by whom. Below it: the log file the
  output comes from and the PHP binary jobs run with.
- **Overview**: how many cronjob parts and scripts there are, how many parts the crontab runs, how many it does not,
  how many **need attention** (blocked, a script missing, or issues in their last run), and the last run started
  from this page with its result.
- **Run and filter** (folded; its summary names the site the run buttons use): **Run for site** (the siteaccess every run button on the page uses), **Find a part or script**
  (matches part names, script names and what the scripts say they do), **Show** (*All*, *Scheduled*, *Not scheduled*,
  *Need attention*), and **Cronjob part** with **Run**: with *All parts* it runs every part that may be run, one after
  another, on every page of the list; with one part chosen it runs that part and shows only its card.
- **Output** (folds): the output of the current or last run. It opens by itself when a job starts.
- **Cronjob parts**, in two views chosen next to the heading and kept per user (the preference
  `admin_cronjobs_view`):
  - **Compact** (the default): one table row per part, every part on one page - the part (the key as its title) with
    *n scripts* below it, which opens the scripts one per line, in columns - *Kernel* or the extension it comes from,
    its path with the file name, what it does and **Docs** - in a row below the part, across
    the table, so several parts can be open at once (without javascript, one below the other in the cell; a missing
    script shows *missing* instead of its path); the badges
    (*Scheduled* or *Not scheduled*, *Blocked*, *n missing*, *Paused*); the schedule in words (the cron expression, or
    the suggested one, as its title) with the next run below it; the last run from here with its result; and
    **Pause** or **Resume** and **Run**. The columns wrap, so the table fits beside the menus. A row is marked at the
    left edge like a card. The search and **Show** filter the rows as they filter the cards.
  - **Detailed**: one card per part, as below, paged.

  In the detailed view, one card per part, in the order of `cronjob.ini`. Each card shows
  - the part's name, its key, *Scheduled* or *Not scheduled* (read from the crontab), and *Activated*, *Blocked*
    (`ForbiddenParts[]`) or *n missing* (scripts not found); *Paused* with **Paused**: since when and by whom, and
    **Pause** or **Resume** next to **Run part** ([pausing cronjob parts](cronjob-pause.md));
  - **Schedule**: the schedule of the crontab line that runs it, in words (*Every 5 minutes*, *Every day at
    03:30*) and as the cron expression; for a part nothing runs, the **Suggested schedule** instead;
  - **Next run**: when that crontab line runs it next, in this server's time; *Only when run by hand* for a part the
    crontab does not run;
  - **Last run from here**: when it was last started from this page, how long it took and *OK* or *n issues*;
  - **Shell command**: the exact command that runs it now, with **Copy**;
  - **Scripts (n)**: the script names on one line; opened, each script with what it does (the `@description` of its
    header) and **Docs** (below), the directory it was found in, **Copy command** (runs that script alone) and **Run**.
  
  A card with a problem has a red edge; the card of the running part a green one. The list is paged (see Settings).
- **Available but not activated** (folds): scripts in a cronjob directory that no part names, so they never run.
- **Pauses** (folds): who paused and resumed which part, and when.
- **Recent runs** (folds): cronjob, site, start time, duration and result. *n issues* counts the lines of the run's
  log that mention an error, a failure or a warning.
- **Crontab** (folds): what is installed in the crontab now, and the suggested entries for the parts nothing runs,
  with **Copy the suggested entries**.

The page works without javascript: every run, stop and clear button submits the form and the page comes back with
the result. With javascript, runs start in place and the output follows live; the search, the **Show** filter, the
**Run** for all parts and the copy buttons need it. Every control has a label, the folding sections are `<details>`
and messages are announced to screen readers.

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
| `settings/cronjob.ini` | `AdminSettings` | `CrontabSchedule_<part>` | `*/5 * * * *` (frequent), `*/15 * * * *` (global), `17 * * * *` (infrequent), `0 * * * *` (others) | global | The schedule the page suggests for a part the crontab does not run |
| `settings/cronjob.ini` | `AdminSettings` | `HistoryLength` | `100` | global | How many runs the history keeps |
| `settings/cronjob.ini` | `PauseSettings` | `Pause`, `Directory` | `enabled`, empty | global | Pausing parts, see [Pausing cronjob parts](cronjob-pause.md) |
| `settings/cronjob.ini` | `ScriptDocumentation` | `Link[<script>]` | empty | global | The **Docs** link of a script, by its file name: a URL, a path below `DocumentationBase`, or `none` for no link. Goes before the `@link` tag of the script's header ([guide](../../guides/cronjobs.md), section 11) |
| `settings/cronjob.ini` | `ScriptDocumentation` | `DocumentationBase` | empty: `https://github.com/se7enxweb/exponential/blob/main/` | global | Where a path in `Link[]` or `@link` is looked up. Only `http` and `https` links are shown |
| `settings/admininterface.ini` | `PaginationSettings` | `ItemsPerPage[setup/cronjobs]` | see [page sizes](../../bc/6.0/pagination-settings.md) | global | How many part cards one page shows |

## Related pages

- The user guide: [Cronjobs: running, scheduling and following them](../../guides/cronjobs.md)
- [Pausing cronjob parts](cronjob-pause.md),
  [leaving out a part from an extension (the filter)](cronjob-part-filter.md)
- The workflow cronjob (`workflow.php`) and the processes it resumes: [Workflows: approvals, triggers and the processes that wait](../../guides/workflows.md)
- [Runnable commands, cronjobs and views (specification)](../../specifications/6.0/runnable-commands-cronjobs-views.md), [commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Content jobs](content-jobs.md), [Velocity scheduler](velocity-scheduler.md), [Notifications (the cronjob part `notification`)](notifications.md)
- [RSS import cleanup cronjob part](../../bc/6.0/cleanuprss.md), [console commands](../../bc/6.0/console.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [September 2026, first half: cronjobs console](../../history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run), [the page redesign of 14 September](../../history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools), [June 2026, first half](../../history/2026/2026-06a.md)
