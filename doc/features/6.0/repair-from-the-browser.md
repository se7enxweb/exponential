# Repair a broken installation from the browser

If the Composer libraries (`vendor/`) are missing, the site cannot start. Until 6.0.15 a visitor saw PHP's raw
output. Now Exponential shows a page of its own that explains what is wrong and lets an administrator repair it
without a shell session.

Added 2026-10-02. Full guide: [doc/bc/6.0/repair.md](../../bc/6.0/repair.md).

## The 3-minute procedure

1. On the server, as the web server user (the key is only ever stored as a hash):

   ```bash
   sudo -u <web server user> php bin/php/exprepair.php --create-key
   ```

   The key is printed once and works once. It runs without the libraries, so it works while the site is down.
2. Open any page of the site. The cursor is already in the key field: paste the key, press Enter.
3. Watch the status bar: three steps, each waiting, running, done or failed, with a progress bar and the end of
   the log refreshed every two seconds. When done, the page reopens after five seconds.

| Step | What runs |
|---|---|
| Install the Composer libraries | `composer install --no-dev --no-interaction --no-plugins --no-scripts --no-progress` |
| Regenerate the autoload arrays | `php bin/php/ezpgenerateautoloads.php -e`, then the kernel autoloads |
| Clear the caches | `php bin/php/ezcache.php --clear-all` |

If `composer.lock` does not match `composer.json`, the page explains it and offers **Update the lock file and
install**, authorised by the failed run's token for 15 minutes.

## Why it is safe

The queue runs as the web server user, never as root; the key is hashed (`settings/override/exprepair.ini.append.php`,
mode 0640, never commit it); the worker is detached, so the page that starts a repair answers at once; index.php
answers the run's status before the kernel starts, because the kernel cannot start without its libraries. Repair
settings are recorded in the [audit trail](audit-trail.md) (whether a key is set, never the key or its hash).

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Enabled` | `true` once a key exists | installation |
| same | `RepairSettings` | `KeyHash` | written by `--create-key` | installation |
| same | `RepairSettings` | `Composer` | `composer` on the PATH | installation |
| `settings/error.ini` | `ErrorSettings` | `StaticErrorPage[dependencies]` | commented out; the page it would name is `design/standard/errors/dependencies.html` | installation |

Disable the feature again with `php bin/php/exprepair.php --disable` (it writes `Enabled=false` and an empty `KeyHash`).
`php bin/php/exprepair.php --status` prints the state of the last repair and the end of its log, and is safe to run at any time.
The second autoload step is the kernel one: `ezpgenerateautoloads.php -k` with an `--exclude` for the installation's worktree directory. The `Composer` key is optional and
names the Composer binary when it is not on the PATH. The repair runs `composer install`; this page documents it, it does
not ask you to run Composer yourself.

Related: [the maintenance mode](maintenance-mode.md) if present in your copy of the documentation, [October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [Velocity and the opcode cache](velocity-opcode-cache-and-profile.md).
