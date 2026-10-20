# Pausing cronjob parts

Read this page if you want to hold back cronjob parts for a while - during a release, a data migration or an index
rebuild - without editing the crontab on every server, or if an extension paused parts through
`cronpart_deactivated_<part>.txt` files until now.

## In short

- **Setup > Cronjobs** pauses a part (**Pause** on its card) or every part (**Pause all parts**) until someone
  resumes it. A paused part is left out by `runcronjobs.php`, whoever starts it: the crontab,
  `./console cron:<part>`, Velocity's `cron:` commands or the console. A part paused while it runs finishes the
  script that runs and leaves out the scripts after it.
- A script run alone (`--script`, or **Run** next to a script) is left out while a part that lists it is paused.
- The console shows **Paused** on the card, since when, by whom and the note given with the pause, a notice while
  every part is paused, a **Paused** figure in the overview, and a **Pauses** section with who paused and resumed
  what and why. A paused part cannot be run from the console.
- The tile of **Cronjobs** on the Setup overview (admin4) says how many parts are paused, while any is.
- The notice of `runcronjobs.php` says since when, by whom and why a part is paused.
- The state is a file per part, in the layout such extensions already use, so a site that paused parts that way
  keeps them, and such a page shows the same state. `cronjob.ini [PauseSettings] Pause=disabled` switches the check
  off.

## How it works

`runcronjobs.php` asks `expCronjobPause::notice()` after it has chosen the scripts and before it asks the
[cronjob/part/run filter](cronjob-part-filter.md). When the run is paused it prints a notice and ends with exit
code 0, like a part a listener leaves out:

```
Running cronjob part 'frequent'
Notice: The cronjob part 'frequent' is paused (Setup > Cronjobs). Paused since 2026-10-15 13:05 CEST by admin: Index rebuild until noon.
```

The time is the time of the state file, in the server's time zone; *by* and the note come from the history and are
left out for a pause made another way (see below), and the note when none was given.

Paused means the file `cronpart_deactivated_<part>.txt` holds `1`; any other content, or no file, means the part
runs. `all` pauses every part; the scripts of `[CronjobSettings]` (the part *global* in the console) have the empty
name, `cronpart_deactivated_.txt`. The files are in `<cache directory>/cronjob_control/`, or in the directory
`[PauseSettings] Directory` names below the var directory.

**A pause while a part runs.** `runcronjobs.php` asks again before each further script of the part. The script that
runs when the part is paused goes on to its end (nothing is stopped half way); the scripts after it are left out:

```
Completing cronjobs/notification.php at: 10/15/2026 05:10 am
Notice: The cronjob part 'frequent' is paused (Setup > Cronjobs). Left out: 'workflow.php' and the scripts after it.
```

**Every run a pause leaves out is logged** in `<log directory>/cronjob_skipped.log` (the notice, the part, the script
and the siteaccess), besides the notice in the run's own output, so you can tell afterwards which runs a pause held
back. The console refuses to start a paused part, so its runs never reach that log.

**What a pause does not hold back.** A pause is read by `runcronjobs.php` only. The `cron:` commands of
`./console` and of Velocity run `runcronjobs.php`, so they are held back too; the queue workers of `expqueue` that
run as a service (systemd) are not cronjobs and keep working - stop the service for that. Pausing the part that
starts a worker from the crontab holds back that start.

The check comes before the filter so that a site's listeners still decide about every part that is not paused, and
so that a pause holds even when a site resets `[RunnableSettings] Listeners[]`.

**The directory follows the siteaccess.** The cache and var directories come from the siteaccess a script runs in.
When the crontab runs `runcronjobs.php -s <site>` and that site has another `VarDir` than the administration
siteaccess the console runs in, the console writes files the cronjob never reads, and pausing does nothing. Run the
crontab with the same siteaccess, or set `[PauseSettings] Directory` in `settings/override/cronjob.ini.append.php`
so both find the same directory.

The names `all` and `global` are reserved: `all` pauses every part, and `global` is how the console calls the
scripts of `[CronjobSettings]`. A cronjob part named `all` or `global` cannot be paused on its own.

**Several servers.** The files are local to a server unless the directory is shared. When the crontab runs on
another server than the one the console is served from (a cluster behind a load balancer), set `[PauseSettings]
Directory` to a directory below the var directory that every server shares (for instance on the shared storage of
the cluster), or pause on the server the crontab runs on.

**Users.** The command line and Velocity's workers may run as root, the web server as the site user; both write
these files. A pause is written to a temporary file that is renamed over the old one (so a file the other user owns
is replaced, not opened), and the file and the history get the installation's file mode (`site.ini [FileSettings]
StorageFilePermissions`, within its limits), so the other user can replace and append to them in turn.

**Audit.** Each pause and resume is recorded as `system.cronjob.pause` or `system.cronjob.resume` (object: the part,
`all` or `global`; `after.note` with the note when one was given), see [the audit trail](../../bc/6.0/audit.md). Both
are on by default, in the `system` channel.

**A note.** The console has one field, *Note for a pause or resume (optional)*, below the buttons of the toolbar. The
next **Pause** or **Resume** pressed on the page (a part's, or *Pause all parts*) keeps it with its history line:
why a part is held back, and until when. It is one line of at most 200 characters (`expCronjobPause::NOTE_LENGTH`;
line breaks become spaces) and shows on the paused part, in the notice of a pause of every part, under **Pauses**,
in the notice of `runcronjobs.php` and so in `cronjob_skipped.log`. From code: `expCronjobPause::pause( $part, $user,
$note )`, `resume( $part, $user, $note )`.

**The Setup overview.** The tile of **Cronjobs** shows *1 part paused*, *n parts paused* or *All parts paused* while a
pause holds, and nothing otherwise (`menu.ini [Leftmenu_setup] LinkFigureTemplates[cronjobs]`, the fetch
`fetch( 'setup', 'cronjob_pauses' )`: one look into the directory of the state files). Like every figure there, it
is drawn only on a tile the user may open (`setup/managecronjobs`).

Pausing from the console writes the file through `eZFile::create()` (the installation's file mode limits apply) and
adds a line to `var/<vardir>/log/cronjob_pause.log`: time, part, pause or resume, the user's object id (no copy of the name: the page and the notice
show the user's current login, looked up by the id, so a renamed user shows under the new name; an older line's login only when the user is gone), and
the note (empty when none was given; lines written before notes existed have none). A
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
broken history lines; `Pause=disabled`; `runcronjobs.php` asks the pause before the filter and again before each
further script, and logs both skips (read from its source); a state file that cannot be opened is replaced; the
state file and the history get `StorageFilePermissions`; a note is kept with a pause and a resume, cut to one short
line, and the notice says since when, by whom and why; history lines without a note; the Setup overview's count;
the console's note field and where the note shows, in both designs. `AdminSetupOverviewTest` covers the tile's
figure and its translations.

## Related pages

- [Leaving out a cronjob part (the filter)](cronjob-part-filter.md)
- [Cronjobs console](cronjobs-console.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
