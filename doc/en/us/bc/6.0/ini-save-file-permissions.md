# Settings files saved through the admin are no longer world-writable

Read this page if settings are changed from the administration (settings editor, debug bar, Setup > Extensions, the
setup wizard), with `exp:ini`, or by a script that calls `eZINI::save()`, and especially if Velocity and PHP-FPM run as
different users. Since 5 October 2026 (6.0.15) a save keeps the mode, owner and group of the file it replaces and
gives a new file `0640`. Before, every saved file was made `0666`.

## In short

| | |
|---|---|
| What changed | `eZINI::save()` no longer runs `chmod( $file, 0666 )`. An existing file keeps its mode, owner and group; a new one gets `0640` and the owner and group of its directory. New settings directories are `0755`, not `0777`. INI cache files are `0644`, not `0666`. |
| Who is affected | Everyone. Every settings file the admin ever saved is `0666` on disk until it is fixed once (below). |
| How to check | `find settings extension/*/settings -name '*.ini*' -perm -o+w` lists the world-writable ones; it must print nothing. |
| How to fix | Once: `chmod 0640` the files the command lists (secrets in `settings/override`), or `0644` where other users must read them. Nothing to change in the code. |

## What was wrong

`eZINI` had one mode for two different kinds of file: the settings files `save()` writes and the INI cache files in
`var/cache/ini`. Both were set with `chmod()` to `EZP_INI_FILE_PERMISSION`, and when that constant is not defined in
`config.php` (the shipped case) to `0666`. So a single save of `settings/override/site.ini.append.php`, which holds
the database password, made it readable **and writable** by every user of the machine. A local user, or any other
site on a shared host, could read the password, or add a setting (`[DebugSettings]`, an extension, a database
host) that the next request obeyed.

The cache files were `0666` too. They are PHP files that `eZINI` includes, and they hold every merged setting,
passwords included: any local user could change one and have code run in the site.

Settings directories that `save()` had to create were made `0777` (with the umask switched off).

## What happens now

### A settings file that already exists

It keeps its own mode, owner and group: a file you made `0600` stays `0600`, a `0644` file stays `0644`. Only the bit
that lets other users write it is removed (`0666` becomes `0664`), as are setuid, setgid and sticky bits; a file that
an earlier save left world-writable is corrected by the next save.

The owner is set back to the old file's owner only when the save runs as root (Velocity's workers on a host where
they run as root): root writes the new file and hands it to the site's user, so PHP-FPM can still read and save it.
A save by another user sets the group when that user may (it belongs to the group), and otherwise the file ends up
with that user, as before.

The backup the save keeps (`<file>~`) is the previous file moved aside, so it keeps the previous mode.

### A new settings file

- Mode `0640` (owner read/write, group read), or `EZP_INI_SAVE_FILE_PERMISSION` when `config.php` defines it. Write
  for other users is removed from that value as well, so `0666` there gives `0664`.
- Owner and group of the directory it is created in. When that directory belongs to root (made by a root process,
  such as `settings/siteaccess/<name>` created by Velocity), the owner and group of the nearest directory above it
  inside the installation that does not belong to root, normally the site user's `settings/`. Without that, a file
  root wrote with `0640` would be unreadable for PHP-FPM and its settings would silently be missing there.
- The temporary file the save writes first is `0600` until it gets its final mode, so the settings are never
  readable by others in between.

Directories `save()` creates are `0755`, with the same owner and group rule.

### The same for expIniEditor

`expIniEditor` (behind `exp:ini`, the debug bar's settings and the mail preferences) already kept mode, owner and
group of an existing file. A new file it creates now gets the same `0640` (or `EZP_INI_SAVE_FILE_PERMISSION`) and the
same owner rule as `eZINI::save()`, instead of `0644`, and its temporary file is `0600` until it is moved into place.

### INI cache files

`var/cache/ini/*.php` keep using `EZP_INI_FILE_PERMISSION`, which now applies to them (and to generated autoload
files) only. Its default is `0644` instead of `0666`. A cache file is replaced by writing a new file and renaming it,
so a second user (Velocity as root, PHP-FPM as the site user) needs write access to the directory, not to the file;
`0644` keeps every user able to read it. Set `EZP_INI_FILE_PERMISSION` in `config.php` if your users need something
else, for example `0640` when all of them share the file's group.

## Settings

| Constant (`config.php`) | Applies to | Default |
|---|---|---|
| `EZP_INI_SAVE_FILE_PERMISSION` | new settings files written by `eZINI::save()` and `expIniEditor` | `0640` |
| `EZP_INI_FILE_PERMISSION` | INI cache files, generated autoload files | `0644` (was `0666`) |

Both are commented out in `config.php-RECOMMENDED`, with these defaults. If your `config.php` defines
`EZP_INI_FILE_PERMISSION` as `0666`, change it to `0644`: it no longer affects settings files, but it still makes the
cache files world-writable.

## Fixing an installation once

```bash
# list world-writable settings files and directories
find settings extension/*/settings -perm -o+w \( -type f -o -type d \)
# secrets: owner and group only
chmod 0640 settings/override/*.ini.append.php
# cache files: regenerated with the new mode
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Check that PHP-FPM's user can still read every file you changed (`sudo -u <site user> cat <file> > /dev/null`), then
reload PHP-FPM and restart Velocity so both use the new code.

## Code

- `lib/ezutils/classes/ezini.php`: `eZINI::SAVE_FILE_PERMISSION`, `eZINI::SAVE_DIRECTORY_PERMISSION`,
  `eZINI::newSaveFileMode()`, `eZINI::saveFileMode()`, `eZINI::saveFileOwnership()`; `save()` uses them.
- `kernel/classes/ini/expinieditor.php`: `writeFile()` uses them for new files.
- Tests: `eZINISaveFilePermissionsTest` (suite `lib`): a new file is `0640`; `0600`, `0640`, `0644` and `0664` files
  keep their mode, `0666` becomes `0664`; owner and group kept as root; a new file in a root-owned directory gets the
  owner of the directory above; new directories `0755`; `EZP_INI_FILE_PERMISSION` no longer reaches a save; a configured
  `EZP_INI_SAVE_FILE_PERMISSION` is honoured but never world-writable. `expIniEditorTest` INI-06 checks the mode of a
  new file.

## Related pages

- [Installation guide, 13.11 File permissions and ownership](../../install/13-security-hardening.md#1311-file-permissions-and-ownership)
- [eZINI keeps comments when it saves](eZINI_PRESERVES_COMMENTS.md)
- [exp:ini](console-exp-ini.md)
