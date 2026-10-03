# Behaviour changes of 16 to 30 September 2026

Read this page before you take the changes of 16 to 30 September 2026 to an existing Exponential 6.0 installation.
It lists what visitors, editors, installers and operators will notice, and what to do about it. Each row names the
change, who is affected, the setting that controls it and the action; longer explanations are linked. This is the
upgrade companion of the [chronicle for the period](../../history/2026/2026-09b.md). The previous period is in the
[chronicle of 1 to 15 September](../../history/2026/2026-09a.md), the next in
[behaviour changes of October 2026](behaviour-changes-2026-10.md).

## Deploy order

Do these first, in this order, on every site you take the changes to:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
systemctl reload <php-fpm service>      # the PHP-FPM service that serves your site
./console exp:velocity deploy --dry-run # if you run Velocity: shows every step
```

The three changes most sites must act on:

1. **Security headers and the session cookie** (first two rows below): check any host that frames the admin, and any
   script that reads the session cookie.
2. **The admin content tree starts at the top node:** set `RootNodeID=2` if editors want the old tree.
3. **Add the `cache_cleanup` cronjob part** to your crontab (see "Caches and web servers").

## Visible to visitors and editors

| Change | Who is affected | Setting | What to do |
|---|---|---|---|
| Every page is sent with security headers; the admin cannot be framed by other sites. | Anyone framing the admin or the site; proxies that re-add headers. | `site.ini [HTTPHeaderSettings] SecurityHeaders[]` | If an editor host frames the admin, override `Content-Security-Policy` (see [security defaults](../../specifications/6.0/security-defaults-2026-09.md)). |
| The session cookie is `HttpOnly`, `SameSite=Lax` and `Secure` over HTTPS. | Integrations reading the cookie from script or using it cross-site. | `site.ini [Session] CookieHttponly`, `CookieSameSite`, `CookieSecure=auto` | Set explicitly if you need the old behaviour. |
| A POST with a missing or wrong form token answers 403 with the "form expired" page, not 500. | Monitoring that alerts on 500; custom error templates. | `site.ini [HTMLForms] RefusalLogCollapseSeconds`; `design:error/kernel/6.tpl` | See [form expired page](../../features/6.0/form-expired-page.md). Alert on 403 volume instead. |
| Forgot password gives the same answer for unknown and known addresses. | Editors who relied on the "no such user" message. | none | None. |
| Pages print `lang="nb-NO"`, `sv-SE`, `nl-NL` ... instead of `no-bokmaal`, `swe-SE`, `dut-NL`. | CSS or JavaScript selecting on the old `lang` values. | locale files `[HTTP] ContentLanguage` | Update selectors; table in [translations](../../features/6.0/translations-and-languages.md). |
| The admin content tree starts at the top node and the header tabs are named Media, Users and Store. | Editors; documentation with screenshots; scripts clicking tab names. | `contentstructuremenu.ini [TreeMenu] RootNodeID` (now `1`), `MaxDepth` (now honoured, `0` = all) | Set `RootNodeID=2` for the old tree. See [navigation](../../features/6.0/admin-content-tree-top-node.md). |
| The Store tab opens the store dashboard. | Shop staff. | none | The order list is the second entry of the shop menu. |
| A disabled module answers 404 like an unknown module. | Tests that expect the "module is disabled" page. | `site.ini [SiteAccessRules] DisabledModuleResult=disabled` brings the old page back | Set it if you need the old behaviour. |
| A fatal error, uncaught exception or failed database transaction shows the site's static error page with 500, or 503 when the database cannot be reached (refused, unknown host, wrong password, host not allowed, too many connections, gone away). Details only with debug output on. | Anyone parsing the old bare "Fatal error" text. | `error.ini [ErrorSettings] StaticErrorPage[<code>]` (for example `[500]`, `[503]`, `[default]`) | Alert on 503. A reference is written to the error log beside the details. |
| Order e-mails and confirmation links can point to a permanent receipt. | Customers; mail templates. | `shop.ini [OrderViewSettings] OrderLinkView` (default `orderview`); `AnonymousOrderLinkView` was removed | See [order receipts](../../features/6.0/order-receipts.md). |
| Package version starts at `1.0.0`; license is a strict choice. | Package authors. | `package.ini [LicenseSettings]` | See [licenses and versions](../../features/6.0/package-licenses-and-versions.md). |
| `Setup > Extensions` saves only the extensions it shows. | Administrators. | none | Check `ActiveExtensions` once after the change. A copy of the file is kept in `var/<site>/backups/settings-override` before every save. |
| `Design > Templates` writes only `Priority` lines and changed conditions. | Teams that edited `override.ini.append.php` by hand. | none | See [override ordering](../../features/6.0/template-override-ordering.md). |
| Role policies are listed in id order. | Editors used to alphabetical order. | none | Use the new sort headings. |

## Installers and operators

| Change | Setting or command | Action |
|---|---|---|
| The kickstarter and the setup wizard default to SQLite. | `kickstart.ini [database_choice] Type` | Name your engine explicitly. |
| Kickstart no longer ends with a guessable administrator password. | `var/log/initial-admin-password` | Read it once after the install and delete the file. |
| `DatabaseImplementation` is taken from the live `site.ini`, not from `Type`. | `settings/override/site.ini.append.php` | When you install onto another engine set it first (see [install page](../../features/6.0/install-in-one-command.md)). |
| `./console exp:install` installs in one command. | `exp:install --help` | New; nothing to migrate. |
| Maintenance mode wraps installations. | `var/maintenance.json` | If a run failed, end it with `php bin/php/maintenance.php off`. See [maintenance mode](../../features/6.0/maintenance-mode.md). |
| A default installation converts images with GD first. | `image.ini [ImageConverterSettings] ImageConverters[]` (shipped order `GD`, `ImageMagick`) | Put ImageMagick first in `settings/override/image.ini.append.php` if you need its filters. |
| New installations start with the folders Configuration and Archives and class group Configuration; Setup has the alias `x-setup`. | seed data | Existing installations are not touched. See [seed data](../../specifications/6.0/installer-logs-and-seed-data.md). |
| Fourteen order statuses are added. | `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` | Run the 6.0.15 database update; custom statuses keep their rows. |
| The version class is `ExponentialSDK`; the example crontab is `exponential.cron`. Old names still work. | | See [ExponentialSDK and exponential.cron](exponentialsdk-and-exponential-cron.md). |
| Outgoing requests and mail identify themselves as Exponential. | | Change web server rules that matched the static cache generator's former user agent. |
| `ExtensionSettingsSiteAccess` lets a translation siteaccess use another siteaccess's extension settings. | `site.ini [SiteAccessSettings]` | New; see [loading order](../../features/6.0/extension-loading-order.md). |
| Log lines name the siteaccess and the full request address, also under Velocity. | `error.log`, `warning.log` | Update log parsers that expected only the client IP. |
| The console understands `--version`, `--about` and `--copyright` before the command name. | `./console --version` | New. |

## Caches and web servers

| Change | Setting | Action |
|---|---|---|
| Cache clears rename directories aside. | `site.ini [FileSettings] RenameBeforeDelete`, `RenameExpiredCaches` | None; set `disabled` to go back. See [cache clears](../../features/6.0/cache-clear-rename-aside.md). |
| New cronjob part `cache_cleanup`. | `cronjob.ini [CacheCleanupSettings]` | Add `php runcronjobs.php -s <siteaccess> cache_cleanup` to your crontab for each var directory. |
| New role-aware HTTP cache (off by default). | `httpcache.ini [HttpCacheSettings] Enabled` | See [HTTP cache](httpcache.md). Purge tag headers are now off unless `TagHeader` names one. |
| New SQL query cache (off by default). | `querycache.ini [QueryCacheSettings] Mode` | See [SQL query cache](sql-query-cache.md). |
| Shipped Apache rules block `.php` below the root, dot files and most of `var/storage/packages`. | `.htaccess_root`, `.htaccess_root_static` | Merge into your own rules. |
| The site's service worker is `/index.js`; `/sw.js` is a one-line script that loads it. | document root | Keep `/sw.js` in place: browsers that registered it keep running it and check that address for updates. Cache version `exp-nav-v4` drops pages that `v3` stored. It also stays away from Velocity's own `/Q/` views and API. |
| Velocity: `Engine=php` is the shipped default; qbix is recommended; workers persist (`ForkPerRequest=disabled`). | `velocity.ini` | See [Velocity](../../features/6.0/velocity-persistent-worker-server.md). |
| Velocity engine package renamed to `se7enxweb/exponential-velocity`. | `velocity.ini [ServerSettings] ScriptPath` | Default points at `vendor/se7enxweb/exponential-velocity`; the old directory is still found. |

## Fixes that change results

- **Subtree counts** with a language find a start node that has no translation in
  that language and no longer leave that language set; counts that used to come
  back empty now match the fetch.
- **Packages**: content installs completely and under the right parents; old V7
  tar packages import (no hang); old class packages install.
- **MongoDB**: every SELECT COUNT, date-ordered list, `$inc` on a quoted number
  and search reindex now work; see [MongoDB kernel support](MONGODB_KERNEL_SUPPORT_EXPANSION.md).
- **Search indexing** takes the batched path on SQLite and PostgreSQL as well as
  MySQL (about twice as fast on SQLite, same index).
- **Absolute URLs** keep the port an installation is reached on; host matching
  ignores the port, so links no longer carry the siteaccess name when the site is
  served on a non-default port.

## Details behind the cache, archive and engine changes

The tables above say what to do. These are the facts behind them, for the
people who tune a server. Measurements are from a reference installation on the
dates given and were not repeated for this page.

### HTTP cache (`httpcache.ini`)

- **Compressed once.** A hit used to put the visitor's values into the stored
  page and compress the whole page for every request, the largest cost of a hit.
  The parts of the stored body between placeholders are compressed once (each
  ending in a full flush, so each stands alone) and kept in APCu beside the
  entry; a hit compresses only its placeholder values (a form token) and joins
  the pieces into one gzip member with the page's CRC-32. Without APCu it
  compresses the page as before. The bytes served are identical (test HC-11).
  Measured 27 September, 8 concurrent, cached front page on FrankenPHP: 4,048
  pages a second anonymous (about 2,800 before) and 3,413 signed in (3,010).
- **Security headers on cached pages.** The cache answers without the kernel, so
  a cached page used to lack the headers a rendered page carries. The configured
  `[HTTPHeaderSettings] SecurityHeaders[]` are stored with the page, and the cache
  key carries the scheme so an HTTPS-only header never reaches an HTTP answer.
- **Siteaccess matching.** URI and host-plus-URI matched siteaccesses are served,
  and a siteaccess the cache does not serve is reported as `X-Exp-Cache: MISS
  (siteaccess)` and kept out of the miss counters. `RemoveSiteAccessIfDefaultAccess`
  and `PathPrefix` need nothing of their own (a page is kept under the full
  request URI). Behind a TLS-ending load balancer the engine must hand over the
  request headers (engine release 0.0.4.35 and later).
- **Purge tags** are sent only when `[HttpCacheSettings] TagHeader` names a header
  (see [security defaults](../../specifications/6.0/security-defaults-2026-09.md)).
- Setup > Caches clears the query cache and the HTTP cache, and Setup > System
  information shows both.

### SQL query cache (`querycache.ini`)

- **A hit is answered without parsing the statement.** The cache read the tables
  from the statement text before it looked for a stored result, every time; that
  was a third of a rendered page's CPU. A stored entry already carries its tables,
  so the entry is looked up first and the text parsed only on a miss; an entry
  whose tables have since been excluded, or are temporary, is not answered from
  (test QC-13). A single Velocity render went from 211.6 to 141.2 ms of CPU with
  the same bytes.
- **Keys use `xxh128`** of the statement and its parameters (0.84 against 1.55
  microseconds a key; `md5` where PHP lacks `xxh128`).
- **The database's own catalogue is never cached** (`sqlite_master`,
  `sqlite_stat*`, `pg_*`, `information_schema`, and for Oracle `USER_*`, `ALL_*`
  and the like): `ANALYZE` and schema changes are no writes the cache sees, and a
  cached `sqlite_master` read was answered stale within the same request
  (test QC-14).
- **The SQL profile counts each request on its own** under a persistent worker:
  every line of `var/tmp/sql_profile.log` used to repeat the warm-up's figures; a
  request that sends nothing to the database writes no line.
- **APCu size**: the cache and the response cache share APCu; Velocity's PHP gets
  `apc.shm_size=256M` (see [Velocity](../../features/6.0/velocity-persistent-worker-server.md)).
  Several servers on one port (`[ServerSettings] Instances`) are described in
  [Velocity engines](velocity-engines.md#several-qbix-servers-on-one-port-instances).

### Engine archive (`exp:phar`)

- The build writes the archive under a temporary name beside it and renames it
  into place. Before, it deleted `dist/engine.phar` and wrote the new one in
  place, so every build left a window with no archive, and two builds at once (a
  restart rebuilding a stale archive while you built by hand) left none, after
  which the server would not start.
- The syntax check before the build uses the PHP that runs the build (it ran
  `php -l` from the `PATH`, so where there is no `php` on the `PATH` every file
  "failed" and nothing was built) and checks files 200 to a `php -n -l` call
  (1,049 files: nine minutes before, about two seconds for the checks and about
  26 for the whole build). A batch that fails is checked file by file so a broken
  file is still named and nothing is written.
- A stale archive says how to fix itself (`exp:phar check`, `exp:phar build`);
  see [Engine archive](phar.md).

### Engines and the server commands

- The three engines (php, qbix, frankenphp) follow the same asset rules as
  `.htaccess_root`; `[ServerSettings] FollowSymlinks=disabled` refuses a file a
  link inside the document root leads to outside of it (Qbix answers 403, the
  built-in server 404). Caddy always follows links.
- FrankenPHP serves HTTPS by default (`[FrankenPHPSettings] HTTPS=enabled`), offers
  compression (`zstd br gzip`; the front page went out at 95.7 KB uncompressed
  where gzipped it is about 10 KB) and runs by default twice the CPU cores of PHP
  threads (an empty `[FrankenPHPSettings] Workers`), not the bundled server's
  `Workers`, which counts mostly idle processes (measured with 64 concurrent
  pages: 16.0 requests a second with 12 to 48 threads, 14.6 with 96 or 590).
- `exp:webserver`, `exp:frankenphp` and `exp:solr`:
  [Server control commands](../../features/6.0/web-server-and-solr-commands.md).

### Preload

Start preloading on Setup > Preload runs the crawl as a background process, so it
works behind Velocity and behind proxies that buffer streamed answers. Nothing to
configure; see [Preload Sites](../../features/6.0/preload-sites-view.md#it-runs-in-the-background).

## Related pages

- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Security defaults](../../specifications/6.0/security-defaults-2026-09.md)
- [Datatype and input hardening](../../specifications/6.0/datatype-input-hardening.md)
- [Database drivers and installers, September 2026](../../specifications/6.0/database-drivers-2026-09.md)
- [Server control commands](../../features/6.0/web-server-and-solr-commands.md)
- [Maintenance mode](../../features/6.0/maintenance-mode.md), [Order receipts](../../features/6.0/order-receipts.md), [Store dashboard](../../features/6.0/store-dashboard.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
