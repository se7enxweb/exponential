# Repair a broken installation from the browser

This page is for administrators whose site does not start because the Composer libraries (`vendor/`) are missing.
Until 6.0.15 a visitor then saw PHP's raw output. Now Exponential shows a page of its own that explains what is wrong
and lets an administrator repair it without a shell session. Added 2026-10-02. Full guide:
[Repair](../../bc/6.0/repair.md).

## Repair in three minutes

1. On the server, as the web server user, create a repair key:

   ```bash
   sudo -u <web server user> php bin/php/exprepair.php --create-key
   ```

   The key is printed once and works once. Only its hash is stored. The command runs without the libraries, so it
   works while the site is down.
2. Open any page of the site. The cursor is already in the key field: paste the key and press Enter.
3. Watch the status bar. It shows three steps, each waiting, running, done or failed, with a progress bar and the end
   of the log, refreshed every two seconds. When all are done, the page reopens after five seconds.

| Step | What runs |
|---|---|
| Install the Composer libraries | `composer install --no-dev --no-interaction --no-plugins --no-scripts --no-progress` |
| Regenerate the autoload arrays | `php bin/php/ezpgenerateautoloads.php -e`, then the kernel autoloads (`ezpgenerateautoloads.php -k` with an `--exclude` for the installation's worktree directory) |
| Clear the caches | `php bin/php/ezcache.php --clear-all` |

If `composer.lock` does not match `composer.json`, the page explains it and offers **Update the lock file and install**,
authorised by the failed run's token for 15 minutes.

The repair runs `composer install` for you; you do not need to run Composer yourself.

## Check and switch off

```bash
php bin/php/exprepair.php --status    # state of the last repair and the end of its log; safe at any time
php bin/php/exprepair.php --disable   # writes Enabled=false and an empty KeyHash
```

## Why it is safe

- The queue runs as the web server user, never as root.
- The key is stored only as a hash, in `settings/override/exprepair.ini.append.php` (mode 0640). Never commit that
  file.
- The worker is detached, so the page that starts a repair answers at once.
- `index.php` answers the run's status before the kernel starts, because the kernel cannot start without its
  libraries.
- Repair settings are recorded in the [audit trail](audit-trail.md): whether a key is set, never the key or its hash.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Enabled` | `true` once a key exists | installation |
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `KeyHash` | written by `--create-key` | installation |
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Composer` | `composer` on the PATH; set it when the binary is elsewhere | installation |
| `settings/error.ini` | `ErrorSettings` | `StaticErrorPage[dependencies]` | commented out; the page it would name is `design/standard/errors/dependencies.html` | installation |

## Related pages

- [Maintenance mode](maintenance-mode.md)
- [File consistency check](file-consistency-check.md)
- [Velocity and the opcode cache](velocity-opcode-cache-and-profile.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)
