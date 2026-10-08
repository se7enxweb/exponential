# Limits for the modes of new files and directories (6.0.15)

Read this page if you run Exponential on a server where other accounts must not read or change what the site writes
(a shared host, a hardened live server), or if you write code that creates files or directories.

## In short

- Two constants in `config.php` set the widest mode any file and any directory the installation creates may get:
  `define( 'EZP_DIR_MODE_MAX', 0750 );` and `define( 'EZP_FILE_MODE_MAX', 0640 );`.
- A mode the code asks for is only narrowed by them, never widened: 0777 becomes 0750, a deliberate 0600 stays 0600.
- They apply to logs, caches (INI cache, PHP caches, autoload arrays, static cache), storage (uploads, image
  variations, the file handlers of the cluster) and everything written without a mode of its own: the umask of the
  process is set from them at start-up. Every place in the kernel, the libraries, the scripts and the extensions of the
  repository that gives a file or directory a mode goes through them; a security test keeps it so.
- Without the constants nothing changes. Nobody has to act; live servers should set them.

## Why

The defaults in `site.ini [FileSettings]` are open: `StorageDirPermissions=0777`, `StorageFilePermissions=0666`,
`LogFilePermissions=0666`, `TemporaryPermissions=0777`. The INI cache directory was created 0777 whatever they say,
and several classes set `umask( 0 )` before they write, so the umask of the server did not narrow anything either.
The INI cache and the autoload arrays are PHP that is included on every request: in a directory every account may
write, another account can replace them, which is running code as the site. Lowering the four settings did not cover
the places that use a fixed mode or none.

## The constants

```php
// config.php
define( 'EZP_DIR_MODE_MAX', 0750 );   // directories: owner rwx, group r-x, others nothing
define( 'EZP_FILE_MODE_MAX', 0640 );  // files: owner rw, group r, others nothing
```

- Write them as octal numbers with the leading 0 (`0750`) or as strings (`'0750'`, `'750'`). An integer above 0777
  written without the 0 (`750`) is read as 0750 too, but a smaller one cannot be told from an octal number: `440` is
  0670. A value that is no mode (`'rwxr-x---'`, 0x1ff0) limits to the owner (0700 for directories, 0600 for files),
  so the site keeps working and nothing is opened, and is reported once to the PHP error log.
- One constant is enough: `EZP_FILE_MODE_MAX=0640` alone gives directories 0750 (the search bit wherever reading is
  allowed), `EZP_DIR_MODE_MAX=0750` alone gives files 0640.
- Set them as a pair whose file limit is the directory limit without the search bits (0750/0640, 0770/0660,
  0700/0600). With a narrower file limit (0750/0600) the files that get their mode set keep to 0600, but files written
  without a mode of their own get what the umask lets through for both, 0640.
- They are constants, not settings, because they have to hold before the INI files are read (the INI cache, the
  autoload arrays, early log lines), and a security limit belongs to the configuration of the server, not to a
  setting an administrator can change in the admin interface.
- Where the web server and the command line scripts (cronjobs, `bin/php/*`) run as different users, they need a
  common group with write access: use `0770` / `0660` and put both users in the group. With one user for both, `0700`
  / `0600` is the tightest choice. Some files are written by both, whoever comes first creates them: the lock and the
  run log of the notification service, the SQL profile of the cache manager, the maintenance state, the content job
  store, the query cache. They used to be made writable for everybody (0666) for that reason; with `0750` / `0640` and
  two users, the second one can no longer write them (a notification run, for example, finds the lock busy). Run both
  as one user, or as one group with `0770` / `0660`.
- The `site.ini [FileSettings]` permissions still apply below the limits: `StorageFilePermissions=0666` with
  `EZP_FILE_MODE_MAX=0640` gives 0640.

Files and directories that exist keep their mode. After setting the constants, clear the caches (they are written
again) and narrow what remains once:

```bash
find var -type d -exec chmod 0750 {} +
find var -type f -exec chmod 0640 {} +
```

## How it works

| Helper | Does |
|---|---|
| `eZFile::fileMode( $mode )` | `$mode` limited to `EZP_FILE_MODE_MAX` |
| `eZDir::dirMode( $mode )` | `$mode` limited to `EZP_DIR_MODE_MAX` |
| `eZFile::executableMode( $mode )` | `$mode` of a file that has to stay executable (a downloaded binary), limited by `EZP_DIR_MODE_MAX`, which keeps the execute bits it allows (0755 with 0750 gives 0750) |
| `eZFile::creationUmask()` | The umask to write with in place of `umask( 0 )`: 0 without limits (as before), else the bits neither limit allows (0027 for 0750/0640) |
| `eZFile::applyCreationUmask()` | Called by `autoload.php` after `config.php` when a limit is set: adds those bits to the umask of the process, so `fopen()`, `file_put_contents()` and `touch()` stay inside the limits too. Under Velocity this holds for the whole worker |

`eZDir::mkdir()` (and the parents of a recursive one), `eZDir::directoryPermission()`, `eZFile::create()`, `eZLog`,
`eZDebug`, `eZINI` (cache directory, cache files, saved settings files), `eZPHPCreator`, `eZFileHandler` and the
autoload generator use them.

Uploads (`eZHTTPFile`), image variations (`eZImageHandler`, `image.ini ImagePermissions`), the file system and DFS
cluster handlers and the static cache set the modes of `site.ini [FileSettings]` and `image.ini` through
`eZFile::fileMode()` as well.

The autoload generator created `var/autoload` with `EZP_INI_FILE_PERMISSION`, a file mode (0644), which left the
directory without the search bit, and its files 0777. The directory is now created with a directory mode under the
umask of the server, as before, and within `EZP_DIR_MODE_MAX`; the files with `EZP_INI_FILE_PERMISSION` or 0666,
never executable.

The umask belongs to the process. A server that runs requests in threads of one process (FrankenPHP in its threaded
mode) shares it between them; the places that change it for a moment put it back, and what they set never allows
more than the limits.

## For extension and kernel code

Give a file or directory its mode through the helpers:

```php
eZDir::mkdir( $dir, 0770, true );                       // limited inside eZDir
chmod( $file, eZFile::fileMode( 0660 ) );
mkdir( $dir, eZDir::dirMode( 0770 ), true );
$old = umask( eZFile::creationUmask() );                 // not umask( 0 )
...
umask( $old );
```

The security test `expFileModeLimitsTest` checks this for the kernel, the libraries, the scripts and the extensions
of the repository: a native `chmod()`, `mkdir()` or `umask()` (also written `\chmod()`) counts only when its mode
argument is a call of a helper and nothing else (`eZFile::fileMode( 0640 ) | 0777` is not), and `umask( $old... )`
only when it puts back a umask saved before. A call through a variable function name or `call_user_func()` is not
seen; do not write one.

All other places use them too: the audit trail (`expAuditWriter::ownLikeParent()` limits a directory as a directory
and a file as a file), content jobs, Velocity and FrankenPHP (the binary through `executableMode()`), the INI editor
and mover, mail preferences, the setup wizard and the installer, the HTTP cache, the debug bar, maintenance and
notification state, packages, templates written in the admin, shop receipts, the query cache and the SQLite
database directory. Narrow umasks they set on purpose (`umask( 0077 )` around a secret) stay at least that narrow:
`umask( eZFile::creationUmask( 0077 ) )`.

## Tests

`expFileModeLimitsTest` (security suite, no database): reading a limit; without limits nothing changes; with
0750/0640 every directory, file, log, INI cache file and PHP cache file it creates stays inside them; one constant
gives the other; a mistyped one limits to the owner and is reported; no code bypasses the helpers.

## Related pages

- [Security and audit guide](../../guides/security-and-audit.md)
- [Settings files saved through the admin](ini-save-file-permissions.md)
