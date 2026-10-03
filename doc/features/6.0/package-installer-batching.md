# A package installer that survives big packages

Installing a site package (the content, classes and design of a demo site) is the
longest thing the setup wizard and **Setup > Packages** do. Since June 2026
(release 6.0.15 development, commits `277ec0ae12` and `86911400dd`) the
installer is hardened in three ways:

1. **Each package item installs in its own transaction.** If one item fails, its
   writes are rolled back instead of leaving half an item in the database, and
   the installation stops with a clear error naming the item.
2. **The browser installs in batches.** The page installs a few items, then
   redirects to itself and continues, so no single request runs long enough to
   meet the web server's or PHP's time limit.
3. **The batch size is a setting.** Slow hardware can use smaller batches, fast
   hardware larger ones.

## Settings

File `settings/package.ini`, block `[InstallerSettings]`:

| Key | Default | Meaning |
|---|---|---|
| `InstallBatchMaxItems` | `12` | Most non-interactive install items processed in one request. Values below 1 are treated as 1. |
| `InstallBatchTimeBudgetSeconds` | `6` | Wall-clock seconds after which the request stops taking new items. A value of 0 or less is treated as 1. |

A batch ends when either limit is reached. If the code finds neither setting it
uses 8 items and 3 seconds. Example override for a slow shared host:

```ini
# settings/override/package.ini.append.php
[InstallerSettings]
InstallBatchMaxItems=4
InstallBatchTimeBudgetSeconds=3
```

Clear the INI cache afterwards (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`).

## What the installer now checks

`eZPackage::installItem()` (`kernel/classes/ezpackage.php`) refuses an item that:

- has no `type`;
- has no installation handler for its type;
- should have XML content but whose file cannot be read or is empty.

It begins a database transaction, runs the handler, and rolls back if the handler
returns false, if the transaction became invalid, or if the handler throws. An
exception no longer escapes to the page; it is logged and shown as an install
error. `eZPackage::install()` (all items) stops at the first failing item unless
the caller sets the parameter `continue-on-error`, and it marks the package as
installed only when every item succeeded.

The install view also lifts PHP's `max_execution_time` for the request, so a
single large item does not hit the limit either.

## If an installation stops

The error page names the item type and name. Fix the cause (a missing extension,
a database error shown in the debug output), then start the installation step
again. The view keeps the position of the current item (`currentItem`) in the
install step's persistent data between requests (`kernel/private/classes/views/package/install.php`);
no button named "Retry" exists in the templates, so reload the install step
rather than looking for one.

## Related

[Chronicle: June 2026, second half](../../history/2026/2026-06b.md),
[MongoDB database support](mongodb-database-support.md) (the wizard changes of
the same weeks),
[Changelog 6.0.15](../../changelogs/6.0/6.0.15.md).
- [Kickstarter: install a whole site from one file](kickstarter-cli.md)
- [Installing Exponential in one command](install-in-one-command.md)
- [Look inside a package, compare it with your site, import single items](package-compare-and-import.md)

## Related pages

- [Installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md)
