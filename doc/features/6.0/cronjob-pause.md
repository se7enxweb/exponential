# Pausing cronjob parts

Read this page if you want to hold back cronjob parts for a while - during a release, a data migration or an index
rebuild - without editing the crontab on every server, or if an extension paused parts through
`cronpart_deactivated_<part>.txt` files until now.

## In short

- **Setup > Cronjobs** pauses a part (**Pause** on its card) or every part (**Pause all parts**) until someone
  resumes it. A paused part is left out by `runcronjobs.php`, whoever starts it: the crontab,
  `./console cron:<part>` or the console. A run that has started goes on to its end.
- A script run alone (`--script`, or **Run** next to a script) is left out while a part that lists it is paused.
- The console shows **Paused** on the card, since when and by whom, a notice while every part is paused, and a
  **Pauses** section with who paused and resumed what. A paused part cannot be run from the console.
- The state is a file per part, in the layout such extensions already use, so a site that paused parts that way
  keeps them, and such a page shows the same state. `cronjob.ini [PauseSettings] Pause=disabled` switches the check
  off.

## How it works

`runcronjobs.php` asks `expCronjobPause::notice()` after it has chosen the scripts and before it asks the
[cronjob/part/run filter](cronjob-part-filter.md). When the run is paused it prints a notice and ends with exit
code 0, like a part a listener leaves out:

```
Running cronjob part 'frequent'
Notice: The cronjob part 'frequent' is paused (Setup > Cronjobs).
```

Paused means the file `cronpart_deactivated_<part>.txt` holds `1`; any other content, or no file, means the part
runs. `all` pauses every part; the scripts of `[CronjobSettings]` (the part *global* in the console) have the empty
name, `cronpart_deactivated_.txt`. The files are in `<cache directory>/cronjob_control/`, or in the directory
`[PauseSettings] Directory` names below the var directory.

The check comes before the filter so that a site's listeners still decide about every part that is not paused, and
so that a pause holds even when a site resets `[RunnableSettings] Listeners[]`.

**The directory follows the siteaccess.** The cache and var directories come from the siteaccess a script runs in.
When the crontab runs `runcronjobs.php -s <site>` and that site has another `VarDir` than the administration
siteaccess the console runs in, the console writes files the cronjob never reads, and pausing does nothing. Run the
crontab with the same siteaccess, or set `[PauseSettings] Directory` in `settings/override/cronjob.ini.append.php`
so both find the same directory.

The names `all` and `global` are reserved: `all` pauses every part, and `global` is how the console calls the
scripts of `[CronjobSettings]`. A cronjob part named `all` or `global` cannot be paused on its own.

Pausing from the console writes the file through `eZFile::create()` (the installation's file mode limits apply) and
adds a line to `var/<vardir>/log/cronjob_pause.log`: time, part, pause or resume, the user's object id and login. A
change made another way - a shell, an extension - has no line there; the card still shows since when
the part is paused (the time of the file), and no name when the file is newer than the last pause recorded here.
The console reads only the end of the log (256 KB), so it may grow without slowing the page; rotate it with the
other logs if you like.

Only part names this installation defines and `all` can be paused from the console; a name is never more than
letters, digits, `_`, `.` and `-`, so it cannot leave the directory.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `settings/cronjob.ini` | `PauseSettings` | `Pause` | `enabled` | `disabled`: `runcronjobs.php` ignores the pause files and the console offers no pause |
| `settings/cronjob.ini` | `PauseSettings` | `Directory` | empty | Directory of the pause files below the var directory; empty: `<cache directory>/cronjob_control` |

## Coming from an extension that paused parts

Such pages relied on a patch of `runcronjobs.php` that Exponential does not carry; on Exponential they switched
files that nothing read, unless the site had its own listener for the filter. Now the kernel reads them:

- A part left paused (a file with `1`) **is left out from now on**. Look at **Setup > Cronjobs** after the
  update and resume what should run.
- A site's own listener that read the same files can stay; it is asked only for parts that are not paused.

## Tests

`expCronjobPauseTest` (no database): nothing is paused at first; pausing and resuming a part, with the history and
who did it; `all`; a script run alone while its part is paused, also with a directory in its name; who paused is
forgotten when the file changed since; state files written by others; part names that would leave the directory;
broken history lines; `Pause=disabled`; `runcronjobs.php` asks the pause before the filter (read from its source).

## Related pages

- [Leaving out a cronjob part (the filter)](cronjob-part-filter.md)
- [Cronjobs console](cronjobs-console.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
