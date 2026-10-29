# Upgrade note: the installers write the cron job (6.0.15)

A new installation activates extensions whose work runs from cronjob parts: the job queue (`expqueue`), the imports'
schedules (`sqliimport`) and, in the administration, the AI queue (`expai`). The kernel's own parts send the
notifications, run the workflows, the content jobs, the audit and the search index. Until now nothing ran any of them:
somebody had to add `runcronjobs.php` to a crontab by hand, and on most new sites nobody did, so queued jobs and
scheduled imports waited for ever.

From 6.0.15 on, every installer writes the cron job of the new installation, and a command adds, shows or removes it
on an installation that exists. The dashboard warns when the parts do not run.

## What the installers do

The setup wizard, the kickstarter and `exp:install` (which runs the kickstarter) write the crontab lines in their last
step (`eZStepFinal`, code `kernel/classes/expcrontabsetup.php`). The site packages (`sevenx_*`, `cjw_multisite`,
`plain_site`) need nothing of their own: the kernel step runs for every package.

- **Whose crontab**: the crontab of the system user that owns the installation directory. When the installer runs as
  that user (the web server user in the wizard, `runuser -u <owner> -- php bin/php/console exp:install --apply` on a shell),
  it writes its own crontab; when it runs as root it writes `crontab -u <owner>`. It never writes root's crontab for a
  site, and never the crontab of another user: an installation owned by root, or an installer running as a third user,
  gets the lines printed instead.
- **Which parts**: `cronjob.ini [CrontabSettings] Parts[]`, each only when the installation defines it with at least
  one script: the kernel's `global`, `frequent` and `infrequent` always, `expqueue`, `sqliimport` and `expai` when
  their extension is active (`ActiveExtensions[]`, or `ActiveAccessExtensions[]` of the admin siteaccess). An
  extension can add a part of its own with `Parts[]` in its `settings/cronjob.ini.append.php`.
- **With which siteaccess**: the parts of `AdminParts[]` (`expqueue`, `sqliimport`, `expai`) run with the admin
  siteaccess (`-s admin`), where their extensions and job types are known; the others with the default siteaccess.
- **When**: `cronjob.ini [AdminSettings] CrontabSchedule_<part>`, the schedule Setup > Cronjobs proposes as well:
  `frequent` every five minutes, `global` every quarter of an hour, `infrequent` hourly at :17, `expqueue` and
  `sqliimport` every minute, `expai` every five minutes.
- **With which PHP**: `[AdminSettings] PhpCliPath`, else the PHP binary the installer runs with (on the command
  line), else the one Setup > Cronjobs finds.

A default installation (`sevenx_multisite`) in `/var/www/example` gets:

```
# BEGIN Exponential cronjobs: /var/www/example
# The cronjob parts of this installation; php bin/php/installcrontab.php --show, --add or --remove (in /var/www/example).
*/15 * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q >> var/log/cronjob-global.log 2>&1
*/5 * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q frequent >> var/log/cronjob-frequent.log 2>&1
17 * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q infrequent >> var/log/cronjob-infrequent.log 2>&1
* * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q -s admin expqueue >> var/log/cronjob-expqueue.log 2>&1
* * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q -s admin sqliimport >> var/log/cronjob-sqliimport.log 2>&1
*/5 * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q -s admin expai >> var/log/cronjob-expai.log 2>&1
# END Exponential cronjobs: /var/www/example
```

and the installer says so:

```
Cron job: added to the crontab of example (6 lines; php bin/php/installcrontab.php --show)
```

### Marked, idempotent, other lines untouched

- The lines stand between `# BEGIN Exponential cronjobs: <root>` and `# END Exponential cronjobs: <root>`, so one
  user can run several installations, each with its own block.
- A second run replaces the block in its place, or writes nothing when it is the same (`already in the crontab`).
- Every other line of the crontab (a `MAILTO=`, other jobs, other installations' blocks) is kept as it is, in its
  place.
- A part that a line outside the block runs already for this installation (written by hand, from `exponential.cron`)
  is left out of the block and named, so it never runs twice:
  `Cron job: part frequent left out, an existing line runs it: 0,15,30,45 * * * * cd /var/www/example && php runcronjobs.php -q frequent 2>&1`.
- After writing, the crontab is read back and compared.

### When there is no crontab

In a container, without a `crontab` command, without permission (`/etc/cron.allow`), or when the user cannot be
told, nothing fails: the installation finishes and the installer prints the exact lines to add.

```
Cron job: not written: there is no crontab command on this system (or it cannot be run). Add these lines to the crontab of example:
  # BEGIN Exponential cronjobs: /var/www/example
  ...
```

The setup wizard shows the same on its last page, under **Cronjobs**. The lines are also in `var/log/setup.log`.

### Switching it off

- For every installation that reads the setting: `settings/override/cronjob.ini.append.php`

  ```ini
  [CrontabSettings]
  Install=disabled
  ```

- For one `exp:install` run: `--no-crontab` (or the environment variable `EXP_INSTALL_CRONTAB=0` for the kickstarter).

Switched off, the installer prints the lines instead of writing them.

## On an installation that exists: bin/php/installcrontab.php

```bash
php bin/php/installcrontab.php --show            # the planned lines, the block in the crontab, the last runs
php bin/php/installcrontab.php --add --dry-run   # what --add would change
php bin/php/installcrontab.php --add --apply             # write or update the block
php bin/php/installcrontab.php --remove --dry-run
php bin/php/installcrontab.php --remove --apply          # take the block out, nothing else
```

Run it as the owner of the installation, or as root with `--allow-root-user` (it then writes `crontab -u <owner>`).
`--admin-siteaccess=<name>` names the admin siteaccess when it is not `admin` (or set
`[CrontabSettings] AdminSiteAccess` in `settings/override`). `--add` and `--remove` print `PASS` or `FAIL`; when the
crontab cannot be written `--add` prints the lines to add by hand and ends with `FAIL` (exit 1). `--dry-run` lists the
lines that would go (`-`) and come (`+`).

An installation with hand-written lines for some parts keeps them: `--add` adds only the parts no line runs yet. To
move such an installation to the marked block, remove the hand-written lines first, then run `--add`.

## When the parts last ran

`runcronjobs.php` records each part it starts and finishes in `var/log/cronjob-lastrun.json` (part, siteaccess, start,
end; a single script run with `--script` is not recorded). With `-q` the lines write no log while all is well, so this
record is how a run is seen.

- **The dashboard** has the block **Cronjobs** (`dashboard.ini [DashboardBlock_cronjobs]`, for users with
  `setup/managecronjobs`): every part the cron job runs, its schedule, when it last started and whether that is on
  time. It warns when no part has run yet, and when a part has not started for twice the interval of its schedule
  plus five minutes (at least an hour). Template `design/standard/templates/dashboard/cronjobs.tpl`, fetch
  `fetch( 'setup', 'cronjob_status' )`.
- **`installcrontab.php --show`** prints the same table and a `WARN` line.
- **`exp:syscard`** and its cronjob fact read the record too.

## Settings

`settings/cronjob.ini`:

| Setting | Default | Meaning |
| --- | --- | --- |
| `[CrontabSettings] Install` | `enabled` | `disabled`: the installers print the lines instead of writing them |
| `[CrontabSettings] Parts[]` | `global`, `frequent`, `infrequent`, `expqueue`, `sqliimport`, `expai` | the parts the cron job runs, each when the installation defines it |
| `[CrontabSettings] AdminParts[]` | `expqueue`, `sqliimport`, `expai` | the parts that run with the admin siteaccess |
| `[CrontabSettings] AdminSiteAccess` | empty (`admin` when it exists) | the admin siteaccess, set in `settings/override` |
| `[CrontabSettings] CrontabCommand` | `crontab` | the crontab command, a name on the `PATH` or a full path |
| `[AdminSettings] CrontabSchedule_<part>` | see above | the schedule of a part |
| `[AdminSettings] PhpCliPath` | empty | the PHP binary of the lines |

## What to do when you upgrade

Nothing changes on an installation that exists until you run the command. Check what it would do, then add it:

```bash
php bin/php/installcrontab.php --show
php bin/php/installcrontab.php --add --dry-run
php bin/php/installcrontab.php --add --apply
```

If your crontab runs the parts already, `--add` leaves those parts out (or writes nothing). Then open the dashboard: the
block **Cronjobs** shows the parts as *never ran* until each has started once.

Code: `kernel/classes/expcrontabsetup.php`, command `kernel/private/classes/commands/installcrontab.php`, step
`kernel/setup/steps/ezstep_final.php`. Test without a database or a crontab:
`tests/tests/kernel/classes/expCrontabSetupTest.php`. See also [Cronjobs](../../guides/cronjobs.md).
