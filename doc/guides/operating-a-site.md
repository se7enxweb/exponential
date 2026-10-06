# Operating a site: caches, cronjobs, backups and repairs

This guide is for the person who keeps an Exponential site running, day to day. It takes you through the routine work of keeping it healthy: looking at the caches and clearing the right one, running cronjobs, switching on the static cache, warming the site, taking a backup, knowing which database you run on, reading the logs, and repairing a site that will not start. Follow it top to bottom. Each part takes a few minutes and ends in something you can check.

Every command is run from the installation root, the directory that contains `index.php`. When you are logged in as `root`, add `--allow-root-user` to the PHP scripts; the scripts refuse to run as root otherwise. In the examples the flag is written out so you can copy them as they are.

Where a topic needs more than a few minutes, the part ends with links to the feature, specification and upgrade pages.

## 1. Look before you clear: what caches exist

Exponential has about twenty-seven caches: INI, templates, content, translations, image aliases, the HTTP cache, the SQL query cache, and, if you run the Velocity server, its response cache. One command lists them and says how each is cleared.

```bash
php bin/php/cache.php list --sizes --allow-root-user
```

Expected output: one block per cache, for example

```
  content              on  content                    expiry timestamp content-view-cache set, directory moved aside
                          var/site/cache/content 3 files, 31.5 KB
  template             on  template                   directory removed
                          var/site/cache/template/compiled 227 files, 5.4 MB
```

The word after the cache name is its **tag**; one tag clears every cache that carries it. For a one-screen summary of the caches that sit in front of PHP (static, HTTP, query, Velocity, OPcache, APCu):

```bash
php bin/php/cache.php status --allow-root-user
```

Expected output ends with `PASS  status read` and has one line each for `static`, `httpcache`, `querycache`, `velocity`, `precompress` and `php`.

In the administration interface the same caches are under **Setup > Cache**.

## 2. Clear the right cache

Clear the smallest thing that fixes the problem. Each of these first, with `--dry-run`, shows what it would do and changes nothing:

```bash
php bin/php/cache.php clear --all --dry-run --allow-root-user
```

Then clear for real, choosing by what you changed:

| You changed | Command |
|---|---|
| A setting (`.ini` file) | `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` |
| A template (`.tpl`) | `php bin/php/ezcache.php --clear-tag=template --allow-root-user` |
| Content looks stale | `php bin/php/ezcache.php --clear-tag=content --allow-root-user` |
| A new template or extension was added | `php bin/php/ezcache.php --clear-id=template-override --allow-root-user` and `--clear-id=design_base` |
| Everything, when unsure | `php bin/php/ezcache.php --clear-all --allow-root-user` |

`ezcache.php --list-tags` and `--list-ids` print the valid names. `--purge` physically removes entries instead of only marking them expired, which saves disk space; `--expiry="-2 days"` with `--purge` removes only old entries.

The same actions exist per group in `exp:cache`, which also reports `PASS` or `FAIL` and has a `--json` output for monitoring:

```bash
php bin/php/cache.php clear --tag=ini --allow-root-user
php bin/php/cache.php imagealias clear --allow-root-user
php bin/php/cache.php opcache status --allow-root-user
```

Large caches are moved aside and deleted afterwards, so a clear does not make requests wait; see [Cache clears that move directories aside](../features/6.0/cache-clear-rename-aside.md).

### The caches in front of PHP

Content is cached again after a clear as soon as the next request renders it. Three caches sit in front of the page rendering and have their own commands:

```bash
php bin/php/cache.php httpcache status --allow-root-user     # the HTTP cache: entries, size, hits and misses
php bin/php/cache.php httpcache clear --allow-root-user      # every stored page becomes stale
php bin/php/cache.php velocity status --allow-root-user      # Velocity's response cache
php bin/php/cache.php velocity clear --allow-root-user
```

Expected output of the status commands ends in `PASS` and a line such as `the HTTP cache is on: 4651 entries, generation 22850`. Clear the HTTP and response caches **after** PHP-FPM or Velocity runs the new code, otherwise a page rendered by the old code in between is stored again. If you run Velocity, one command does all steps in the right order:

```bash
./console exp:velocity deploy --dry-run --allow-root-user
```

Depth: [Velocity response cache](../features/6.0/velocity-response-cache.md), [HTTP cache](../bc/6.0/http-caching.md), [response cache and navigation](../bc/6.0/response-cache-and-navigation.md), [cache console](../bc/6.0/cache-console.md), [Velocity engines: deploying a PHP change](../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).

## 3. Run cronjobs

Cronjobs do the work nobody waits for: publishing scheduled content, importing feeds, cleaning baskets, sending newsletters, building sitemaps.

See what exists and which script belongs to which part:

```bash
php runcronjobs.php --list --allow-root-user
```

Expected output: blocks named `CronjobSettings:` and `CronjobPart-frequent:`, `CronjobPart-infrequent:` and so on, each followed by its script files.

Run one part, or one script of a part:

```bash
php runcronjobs.php --siteaccess=site frequent --allow-root-user
php runcronjobs.php --siteaccess=site --script=notification.php --allow-root-user
```

Use the name of your own public siteaccess for `--siteaccess`. The same list is in the administration at **Setup > Cronjobs** (`/setup/cronjobs`), where each part has a card with its schedule, its next and last run, a *Run part* button and its shell command to copy; the output follows while the job runs, and the page proposes the crontab lines. Show what is installed now:

```bash
./console crontab:list
```

To install the schedule open the crontab for editing (`./console crontab:edit`) and add a line per part. A common starting point is the frequent part every few minutes and the infrequent part once an hour or a day:

```
*/5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent --allow-root-user >> var/log/cron-frequent.log 2>&1
17 * * * *  cd /path/to/installation && php runcronjobs.php --siteaccess=site infrequent --allow-root-user >> var/log/cron-infrequent.log 2>&1
```

Replace `/path/to/installation` with the root directory. Every part also has a console alias, for example `./console cron:frequent`; `./console list cron` shows them all. If you run Velocity, its own [scheduler](../features/6.0/velocity-scheduler.md) can start these tasks without the system cron.

The notification cronjob part has its own guide: [Notifications: running them and fixing problems](notifications-administrator.md).

The part `mailpreferences` (also in `infrequent`) keeps the e-mail consent log and confirmations within their retention time: [E-mail preferences: setting them up and running them](mail-preferences-administrator.md).

Depth: [Cronjobs console](../features/6.0/cronjobs-console.md), [commands, cronjob parts and views as classes](../specifications/6.0/runnable-commands-cronjobs-views.md), [Content jobs](../features/6.0/content-jobs.md) (large removals and copies run in batches and are resumed by `cron:contentjobs`).

## 4. Turn on the static cache

The static cache stores public pages as plain files that the web server can send without starting Exponential. First see what it would do:

```bash
php bin/php/cache.php static status --allow-root-user
```

Expected output lists the sites that can be generated and ends with `PASS` and a sentence such as `the static cache is not enabled in site.ini [ContentSettings] StaticCache; 3 sites can be generated`.

Generate the pages of one site, limited to 500 pages so you can see it work quickly:

```bash
php bin/php/makestaticcache.php --site=site --max-pages=500 --max-depth=6 --allow-root-user
```

Omit `--site` to generate every public siteaccess. Only siteaccesses that need no login are offered, because their pages are the same for everybody. The same is possible in the browser at **Setup > Cache**, section *Static content cache*, with a *Stop* button. Check the result:

```bash
php bin/php/cache.php static status --allow-root-user
```

The files are counted per site in `var/site/static`. To make the web server answer from them, set `StaticCache=enabled` in `settings/override/site.ini.append.php` block `[ContentSettings]`, clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`), and check that the web server rules of your installation are active. Remove the generated files with `php bin/php/cache.php static clear --allow-root-user`.

Depth: [Static cache generator](../features/6.0/static-cache-generator.md), [static cache defaults](../bc/6.0/static-cache-defaults.md).

## 5. Warm the site (preload)

After a clear or a deploy the first visitor pays for the render. Warm the pages yourself:

```bash
php bin/php/preload.php --siteaccess=site --allow-root-user
```

It requests the section pages first, then follows the links of the site, with PHP's curl extension (no `wget` needed); one preload runs at a time, and every run is listed on Setup > Preload. To warm every published page, which suits a cron entry every few minutes:

```bash
php bin/php/warm.php --allow-root-user >> var/log/cache-warm.log 2>&1
```

In the browser use the *Preload Sites* view from the administration: choose the site, a page limit (default 250) and a link depth (default 3), and watch the pages being fetched. Depth: [Preloading caches](preloading-caches.md), [Preload Sites view](../features/6.0/preload-sites-view.md), [Site cache preloader](../bc/6.0/preload.md), [HTTP/2 and cache warming](../bc/6.0/http2-and-cache-warming.md).

## 6. Back up and restore

Exponential keeps state in two places: the **database** and the **files** (`var/`, `settings/override/` and `settings/siteaccess/`, and your own extensions and designs). A backup is both, taken at the same time.

Maintenance mode makes the pair consistent. It answers every visitor with a 503 page while you work:

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
php bin/php/maintenance.php status --allow-root-user     # prints: Maintenance is on...
```

Take the files:

```bash
tar czf backup-files.tar.gz var/storage var/log settings/override settings/siteaccess
```

Take the database with the tool of your database system. Replace `USER` and `DATABASE` with your values. `-p` makes the client ask for the password; never write the password itself on the command line, where it lands in the shell history.

| Database | Backup | Restore |
|---|---|---|
| MySQL or MariaDB | `mysqldump --single-transaction -u USER -p DATABASE > backup.sql` | `mysql -u USER -p DATABASE < backup.sql` |
| PostgreSQL | `pg_dump -U USER DATABASE > backup.sql` | `psql -U USER DATABASE < backup.sql` |
| SQLite | `sqlite3 var/storage/sqlite3/exponential.db ".backup backup.db"` | copy `backup.db` back with the site in maintenance mode |

`exponential.db` is the file name `exp:install` uses by default; use the name of your own database file (`ls var/storage/sqlite3/`).
For SQLite never copy the file while the site is busy; `.backup` is safe. The schema alone, as a portable file, is also available through `php bin/php/ezsqldumpschema.php --type=mysql --user=USER DATABASE schema.sql --allow-root-user`.

Then switch the site back on and clear the caches:

```bash
php bin/php/maintenance.php off --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
```

Test a restore on another server at least once; a backup you have never restored is a hope. Depth: [Maintenance mode](../features/6.0/maintenance-mode.md).

## 7. Know your database driver

Which database runs the site is one setting. Read it:

```bash
grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php settings/site.ini
```

Expected output: a line such as `DatabaseImplementation=ezmysqli`. The values are `ezmysqli` (MySQL, MariaDB), `pgsql` or `ezpostgresql` (PostgreSQL), `sqlite3` (one file under `var/storage/sqlite3/`) and `mongodb`; Oracle comes with its own extension. The aliases are listed in `settings/site.ini` block `[DatabaseSettings]`, key `ImplementationAlias[]`.

What to remember:

- **SQLite** is the quickest way to start, and its transactions queue for the single write lock (`SQLiteTransactionWait`, default 25 seconds, kept below Velocity's 30-second request timeout `Q.webserver.requestTimeout` so the driver can report "database is busy" before the request is cut off; raise both together). For many editors publishing at once use MySQL or PostgreSQL.
- **MySQL** and **PostgreSQL** are the usual production choices.
- **Oracle** and **MongoDB** are supported; check the pages below before choosing them.

Depth: [SQLite database support](../features/6.0/sqlite-database.md), [SQLite 3 driver](../specifications/6.0/sqlite3-database-driver.md), [database drivers 16 to 30 September 2026](../specifications/6.0/database-drivers-2026-09.md), [SQLite and Oracle driver behaviour](../specifications/6.0/database-drivers-sqlite-oracle.md), [MongoDB support](../features/6.0/mongodb-database-support.md).

## 8. Read the logs

Where to look first:

| What | Where | How |
|---|---|---|
| PHP and application errors | `var/log/error.log`, `var/log/debug.log` | `tail -n 50 var/log/error.log` |
| Cronjob output | `var/log/` and, for jobs started from the browser, `cronjobs/output.log` and `cronjobs/error.log` in the siteaccess var directory | **Setup > Cronjobs**, section *Output* |
| Installer | `var/log/kickstart.log` (passwords masked) | `tail -n 50 var/log/kickstart.log` |
| Who did what (logins, content changes, permission changes) | the audit trail | `php bin/php/audit.php status --allow-root-user` |

The audit trail is a signed, tamper-evident log. Check that it is intact and read the newest events:

```bash
php bin/php/audit.php status --allow-root-user
php bin/php/audit.php tail --channel=access --lines=20 --allow-root-user
php bin/php/audit.php verify --allow-root-user
```

Expected output of `status`: `Audit: enabled` and one line per channel (`content`, `access`, `system`, ...) ending in `intact`. To see more in the page itself while you investigate, set `DebugOutput=enabled` in `settings/override/site.ini.append.php` block `[DebugSettings]`, reload the page, and switch it off again; never leave it on in production. Depth: [Audit trail](../features/6.0/audit-trail.md), [audit event model](../specifications/6.0/audit-event-model.md), [debug output improvements](../features/6.0/debug-output-improvements.md).

Remove expired sessions and old versions regularly if you do not run the matching cron parts:

```bash
php bin/php/ezsessiongc.php --allow-root-user
php bin/php/cleanupversions.php --help --allow-root-user
```

## 9. Repair a site that will not start

The most common cause is a missing `vendor/` directory: the site shows a page that explains it. Repair it without a shell session, or with one command:

```bash
sudo -u WEB_SERVER_USER php bin/php/exprepair.php --create-key
```

The key is printed once and works once. Open any page of the site, paste the key into the field and press Enter; three steps run (install the libraries, regenerate the autoloads, clear the caches) and the page reopens when they are done. Check the state at any time, and switch the feature off when you do not want it:

```bash
php bin/php/exprepair.php --status
php bin/php/exprepair.php --disable
```

For a site that starts but misbehaves, the usual order is: clear the INI and template caches (part 2), check `var/log/error.log` (part 8), run `php bin/php/checkclasses.php --allow-root-user` to find a class PHP refuses to load, and regenerate the autoloads after adding or renaming a class:

```bash
php bin/php/ezpgenerateautoloads.php -e --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
```

Depth: [Repair from the browser](../features/6.0/repair-from-the-browser.md), [repair (upgrade page)](../bc/6.0/repair.md).

## 10. A weekly checklist

1. `php bin/php/cache.php status --allow-root-user` ends in `PASS`.
2. `php bin/php/audit.php status --allow-root-user` shows every chain `intact`.
3. `tail -n 50 var/log/error.log` shows nothing new that you do not understand.
4. `./console crontab:list` still shows your cron entries.
5. A backup from the last seven days exists and has been restored at least once on another machine.

## Related pages

- Running on the Velocity server: [Velocity persistent-worker server](../features/6.0/velocity-persistent-worker-server.md) and [Velocity engines](../bc/6.0/velocity-engines.md).
- Installing a new site: [Installing in one command](../features/6.0/install-in-one-command.md) and [Kickstarter](../features/6.0/kickstarter-cli.md).
- What changed, month by month: the [Velocity chronicle](../history/velocity/README.md).
- Other guides: [Deploying](deploying.md) (servers, HTTPS, shipping a change), [Security and audit](security-and-audit.md) (hardening, roles, the audit trail), [Upgrading](upgrading.md).
- Words used here: [Glossary](../glossary.md).
