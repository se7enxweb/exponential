# Log rotation and pruning

Exponential writes its logs to `var/log` (or each site's `var/<site>/log`): `error.log`, `warning.log`,
`notice.log`, `debug.log`, `deprecated.log`, `storage.log`, the cronjob logs and the logs of extensions. This page
describes how they are rotated, how to rotate them on demand and how to remove old archives, on the command line
and in **Setup > Log files** ([the log viewer](log-viewer.md)).

## In short

- **One rotation for the whole kernel.** When a log outgrows `site.ini [FileSettings] LogFileMaxSize` (2 MB), the
  next write rotates it: `<file>.N-1` becomes `<file>.N`, ..., `<file>` becomes `<file>.1`, and the archives beyond
  `LogFileRotateCount` (3) are removed. eZDebug (the debug logs) and eZLog (`storage.log`, cronjob and extension
  logs) both use it (`expLogRotator`), as do the command line and the log viewer.
- **Safe with two writers.** On a host where Velocity runs as root and PHP-FPM as the site's user, both write the same
  logs. A rotation takes an exclusive lock on the file it rotates, so two processes never rotate it at once; a
  rotation because of the size checks the size again under the lock, so two requests that both found the log too big
  rotate it once. The new, empty log is created at once with the owner, group and mode of the old one (the owner when
  the rotation runs as root), so neither user ends up with a log the other cannot write.
- **On demand**: `./console exp:logs --rotate=error,debug`, `--rotate-all`, and `--prune` for the archives beyond the
  limits; each with `--dry-run`.
- **Optional compression** of the archives from `.2` on (`LogFileCompress=enabled`, off by default). `.1` is never
  compressed: a request that opened the log just before the rotation may still be writing to it.

## Settings

All in `site.ini [FileSettings]`; override them in `settings/override/site.ini.append.php`.

| Setting | Default | Meaning |
|---|---|---|
| `LogFileMaxSize` | `2097152` | the size in bytes at which a log is rotated by the next write. The constants `EZPUBLISH_LOG_MAX_FILE_SIZE` and `CUSTOM_LOG_MAX_FILE_SIZE` in `config.php` win. |
| `LogFileRotateCount` | `3` | how many archives (`.1` .. `.N`) each log keeps. `0` switches rotation by size off (the log grows). `EZPUBLISH_LOG_ROTATE_FILES` and `CUSTOM_LOG_ROTATE_FILES` in `config.php` win. |
| `LogFileCompress` | `disabled` | `enabled`: archives from `.2` on are compressed to `.2.gz`, `.3.gz` ... The log viewer reads and searches them. |
| `LogFileMaxAge` | `0` | days after which a prune removes an archive, whatever its number. `0`: no age limit. A rotation never removes by age. |
| `LogFilePermissions` | `0666` | the mode of a log file created where none existed (a rotated log keeps the mode of the old file). |

Which files can be rotated and pruned is the log viewer's allow-list: the directories of `logviewer.ini
[LogViewerSettings] Directories[]` (`var/log`, the log directory eZDebug writes to, and every `var/*/log`), real paths
inside the installation. The logs configured by key there (`LogList[]`, such as the web server's) are never rotated
or pruned: the program that writes them owns their rotation.

### Per log: logviewer.ini [RotationSettings]

A log can have limits of its own, by its key (the key of the log viewer's permalinks):

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
MaxSize[debug]=10485760
Keep[debug]=1
Compress[error]=enabled
MaxAgeDays[error]=30
```

They apply to rotation, pruning and compression on demand, the cronjob part and the log viewer's actions. A log
without an entry takes the `site.ini [FileSettings]` values above. The size-triggered rotation while a log is being
written always uses `site.ini`.

## On the command line

```bash
./console exp:logs --archives                    # each log with its archives, their sizes, and the limits
./console exp:logs --rotate=error,debug          # rotate these now, whatever their size
./console exp:logs --rotate=var/site/log/storage.log
./console exp:logs --rotate-all                  # every log that is not empty
./console exp:logs --rotate-all --dry-run        # what would be rotated
./console exp:logs --prune --dry-run             # archives beyond LogFileRotateCount and LogFileMaxAge, and bare .gz files
./console exp:logs --prune                       # remove them (all logs)
./console exp:logs --prune=error,debug           # only these logs
./console exp:logs --compress                    # compress the plain archives from .2 on
./console exp:logs --truncate=debug              # empty a log by rotating it (its content stays in debug.log.1)
./console exp:logs --truncate=debug --in-place   # empty it in place, keeping no copy
./console exp:logs --rotate-all --force          # also the logs under their MaxSize
./console exp:logs --prune --days=7              # an age limit for this run instead of MaxAgeDays
./console exp:logs --archives --json             # the same as JSON
```

`--rotate-all` rotates the logs over their `MaxSize` (with `--force`: every log that is not empty); `--rotate=<keys>`
rotates the named ones whatever their size. Every operation is first a plan (`--dry-run` prints it) and is then carried
out step by step, each step checked again against the disk under the lock.

A log is named by its **key**, the same as in the log viewer's permalinks (`setup/logs/view/<key>`): `error` for
`var/log/error.log`, `<site>-<name>` for `var/<site>/log/<name>.log` (`site-storage`), or by its **id**, the path
relative to the installation root. A name that is not a log of the allow-list (a path outside the log directories,
`..`, an archive such as `error.log.1`, a symbolic link out of the directories, a web server log) is reported as
`FAIL: not a log that can be rotated` and nothing is touched.

Exit codes: `0` done, `1` nothing to do (the named logs were empty, nothing to prune), `2` an unknown name or a
failure. Run it as the user who owns the logs, or as root (the new logs then keep their owner).

Rotations are recorded in the audit trail as `system.log.rotate`, prunes as `system.log.delete` (channel system),
the same names as the log viewer's actions.

### As a cronjob part

The size-based rotation needs no job. The cronjob part `logs` (`cronjobs/explogrotate.php`, in no default part, so
it runs only when asked) rotates every log over its `MaxSize`, prunes the archives beyond `Keep` and `MaxAgeDays`, and
compresses the archives of the logs with `Compress` enabled; each change is recorded in the audit trail:

```bash
php runcronjobs.php logs
# or every night, as the site's user (or root, the new logs keep their owner):
15 3 * * * www-data cd /path/to/exponential && php runcronjobs.php -q logs
```

### With logrotate If the system's `logrotate` manages `var/log` instead, set `LogFileRotateCount=0` so
the kernel does not rotate as well, and use `copytruncate` or `create <mode> <owner> <group>` there.

## In the log viewer

**Setup > Log files** offers **Rotate now** for the open log (`setup/managelogs`, switched on with
`logviewer.ini [ActionSettings] Rotate=enabled`), which uses the same rotation. See [the log viewer](log-viewer.md),
"Actions".

## For developers

- `expLogRotator` (`lib/ezfile/classes/explogrotator.php`): `rotate( $path, $keep, $options )` (`compress`,
  `minSize`, `wait`), `archives( $path )`, `prune( $path, $keep, $maxAge, $dryRun )`, `recreate()`, `gzip()`. Pure
  file system, no settings: it runs on the earliest error paths.
- `eZDebug::rotateLog( $file, $minSize = 0 )` and `eZLog::rotateLog( $file, $minSize = 0 )` call it with
  `LogFileRotateCount`; a process that loaded its classes before `expLogRotator` existed (a long running worker)
  rotates the old way until it restarts.
- `expLogManager` (`kernel/classes/logviewer/explogmanager.php`), the API of the command line, the cronjob part and the
  log viewer's actions: `logs()` (each log with its archives, sizes, `growth` in bytes an hour since the last rotation,
  `limits` with their sources, `writable`, `owner`), `effectiveSettings( $key )`, `select( $names )`,
  `plan( $keys|'all', 'rotate'|'prune'|'compress'|'truncate', $opts )` (the dry run: steps `{key, op, from, to,
  bytes}`), `apply( $plan )` (each step resolved through the allow-list again; a step naming any file but the log or
  one of its archives is refused), and the shortcuts `rotate( $key )`, `prune( $key, $days )`, `compress( $key )`,
  `truncate( $key, $archiveFirst = true )`.
- Tests (no database): `tests/tests/lib/ezfile/expLogRotatorTest.php`,
  `tests/tests/kernel/logviewer/expLogManagerTest.php`.
