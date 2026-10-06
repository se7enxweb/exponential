# System information: what the site runs on and what needs attention

This guide explains the administration page **Setup > System information** (`/setup/info`) and the command
`./console exp:system:info`: which server answered, how it runs PHP, the health checks and what to do about each,
the overview cards, the detailed parts below them, the report for a support request, and what is never shown.

It is written for administrators and for whoever supports a site. Every check, field and path below was checked
against the code (`kernel/classes/expsystemreport.php`, `kernel/classes/expsystemreportmask.php`,
`kernel/private/classes/views/setup/info.php`, `kernel/private/classes/commands/systeminfo.php`). The figures quoted
are from the demonstration server (alpha.se7enx.com) on 6 October 2026, read-only.

[Guides](README.md) · Related guides: [Deploying](deploying.md), [Operating a site](operating-a-site.md),
[Cronjobs](cronjobs.md), [Security and audit](security-and-audit.md)

## In short

- **Setup > System information** needs the `setup/system_info` policy. It reads; it changes nothing. (The buttons of
  the HTTP cache, the query cache and the SQL profile, folded further down, post to it and need `setup/managecache`.)
- The page first says **which server answered**: Apache with PHP-FPM (and its pool), Exponential Velocity (with its
  worker model and release), FrankenPHP, PHP's built-in server. Each has its own PHP, OPcache and settings, so the
  same page shows different figures on `https://example.org/admin` and on Velocity's port. Open it on both.
- **Health checks** come first, failures and warnings before notes, each with what to do. The checks in order are
  folded under "N checks in order".
- **Overview** cards: Exponential, Server, PHP, OPcache and APCu, Database, Storage, Caches, Cronjobs, Mail, Locale
  and time, Machine.
- **Download report (.txt)**, **Download as JSON** and **Copy report as text** give the same report for a support
  request. Passwords, keys, tokens, session ids, credentials in addresses and full server paths are never in it.
- **Measure sizes** (`/setup/info/sizes`) adds the size of `var/`, of each cache directory and of a database on a
  server; it reads every file, within three seconds.
- `./console exp:system:info [--json] [--sizes] --allow-root-user` prints the report of the command-line PHP and
  exits 1 when a check fails.

## 1. Opening the page

Setup > System information, or `/setup/info` in the administration siteaccess. The left menu of Setup lists it.

| Address | What it gives |
|---|---|
| `/setup/info` | The page |
| `/setup/info/sizes` | The page, with directory and database sizes measured |
| `/setup/info/report` | The report as a text file (`exponential-system-information-<date>.txt`) |
| `/setup/info/json` | The report as JSON |
| `/setup/info/php` | `phpinfo()` of the server process, without its request and environment |

All five need `setup/system_info`. Give it only to administrators: even masked, the page says a lot about the
installation.

## 2. Which server answered

The blue banner (orange under Velocity) names the server that answered this request and how it runs PHP:

| Server | What the page says | What it means for the figures |
|---|---|---|
| Apache + PHP-FPM | "a pool of PHP processes, a clean state for every request", the pool name, and its busy, idle and total processes | OPcache and APCu are those of the pool; a reload of PHP-FPM empties them |
| Exponential Velocity | "persistent workers: one process answers many requests and keeps its classes and caches between them", the release (`v0.0.4.42+...`), workers and spare workers from `velocity.ini`, whether the engine runs from the archive | OPcache and APCu belong to the Velocity parent and its workers; `max_execution_time` is "no limit" because Velocity runs PHP's command-line SAPI |
| FrankenPHP | worker mode or a pool of threads | as above for its own process |
| PHP built-in web server | "a development server, not for production" | |

The badge on the right repeats the PHP version and SAPI (`fpm-fcgi`, `cli`, `frankenphp`, `cli-server`).

On alpha on 6 October 2026, Apache answered with PHP 8.5.11 `fpm-fcgi`, `memory_limit` 4999M, OPcache on with its
statistics hidden; Velocity answered with PHP 8.5.11 `cli`, `memory_limit` 4828M, OPcache on with a hit rate, and
`opcache.file_update_protection` 0.

## 3. Health checks

Each check has a state: **Failure** (red), **Warning** (amber), **Note** (blue) or **OK** (green), a title, and for
the first three what it means and what to do (after the arrow).

| Check | Failure or warning when | What to do |
|---|---|---|
| PHP version | below `setup.ini [phpversion] MinimumVersion` (failure); a branch whose security support has ended (warning) | upgrade PHP |
| PHP extensions | one the kernel needs is missing: ctype, dom, iconv, json, libxml, mbstring, pcre, session, simplexml, spl, xml, zlib, and the driver of the configured database (failure) | install and enable them, reload PHP |
| Recommended extensions | curl, fileinfo, intl, xsl, OPcache, APCu, or both gd and imagick missing (warning) | install them |
| OPcache | off; full; hit rate below 90 % after 5000 lookups (Apache and FPM only: a young Velocity worker has a low rate by nature) | `opcache.enable=1` (FPM) or `opcache.enable_cli=1` in `velocity.ini [PHPSettings] IniOptions[]` |
| file_update_protection | under Velocity, `opcache.file_update_protection` above 0: a worker never caches a file written less than that many seconds before the request | `IniOptions[]=opcache.file_update_protection=0`, restart Velocity |
| memory_limit | below 128M (failure), below 256M (warning) | raise it |
| max_execution_time | below 30 seconds on a web server | raise it |
| var directory | `var/`, its cache or storage directory not writable (failure) | give the web server's user write access |
| Free disk | under 1 GB or 5 % (failure), under 5 GB or 10 % (warning) | free space |
| Debug output | `[DebugSettings] DebugOutput` on for every visitor (failure); on for chosen addresses or users only (note) | `DebugByIP=enabled` with `DebugIPList[]`, or off |
| Development settings | `[TemplateSettings] Debug`, `ShowUsedTemplates` or `[DatabaseSettings] SQLOutput` on | disable them on production |
| display_errors | PHP shows errors in the page | `display_errors=Off`, `log_errors=On` |
| Caches | `ViewCaching`, `TemplateCompile`, `TemplateCache` or the override cache off | enable them on production |
| SiteURL | empty or a loopback name | set `[SiteSettings] SiteURL` |
| Cronjobs | no run found, or the last one more than two days ago | schedule `runcronjobs.php` (see [Cronjobs](cronjobs.md)) |
| Mail | `Transport=file` (note: nothing is sent); SMTP without a server (failure) | set `[MailSettings]` |
| Database | no connection (failure); a character set other than UTF-8 (warning) | check `[DatabaseSettings]`; convert to UTF-8 |
| Time zone | neither `date.timezone` nor `[TimeZoneSettings] TimeZone` set | set one |

The cronjob check reads the newest of: the run history of Setup > Cronjobs (`var/<site>/cronjobs/history.json`,
read as it is) and the modification time of a non-empty `var/log/cronjob*.log`.

## 4. The overview cards

| Card | What it shows |
|---|---|
| Exponential | version and state, release line, the version the database was installed or upgraded to (`ezsite_data`), the engine archive's build when Velocity runs from it, the siteaccess, the number of extensions |
| Server | as section 2, plus the port and whether it is HTTPS |
| PHP | version (ZTS marked), SAPI, `memory_limit`, `max_execution_time`, upload limits, `max_input_vars`, the operating system |
| OPcache and APCu | on or off; hit rate, memory and scripts when the statistics can be read; otherwise why not; JIT; `validate_timestamps` and how often; `file_update_protection` under Velocity; APCu free memory |
| Database | engine and server version, driver class, the file (SQLite) or server and database name, character set, number of tables, size (SQLite always; MySQL and PostgreSQL with Measure sizes) |
| Storage | the var directory and whether it is writable, its size (on request), free disk |
| Caches | view cache, template compiling, template cache, override cache, static cache, HTTP cache, SQL query cache mode, and Velocity's response cache under Velocity |
| Cronjobs | last run and where it was seen |
| Mail | transport; for SMTP the server, port, encryption and whether it signs in; whether a sender address is set |
| Locale and time | locale, content languages, time zone and where it is set, server time |
| Machine | processor and count, memory, load |

A card with a failure or a warning among its checks has a red or amber edge and badge.

**OPcache "statistics not available".** Plesk can put `opcache_get_status` in `disable_functions` for a domain (on
alpha it is set in the pool configuration). The cache works; only its figures cannot be read from PHP. The page
used to say "not installed" there. Velocity reads them because its PHP has no such setting.

## 5. The detailed parts

Folded below the cards, each opens with a click:

- **Active extensions**, in load order, with the version their `extension.xml`, `ezinfo.php` or `composer.json`
  states (authors, licences and websites are on `ezinfo/about`).
- **PHP extensions and settings**: every loaded extension, `file_uploads`, `open_basedir` (set or not),
  `max_file_uploads`, `display_errors`, the `disable_functions` list, the autoload functions, and the link to
  `phpinfo()`.
- **Site addresses**: the public site, the address this page was served from, the `SiteURL` setting.
- **OPcache and APCu**: memory, scripts, hit rate and interned strings as bars, and their settings.
- **Velocity** (under Velocity only): engine and role, address, process, console log, the views the server answers
  itself and who may open them, other engines running.
- **Phar App Engine** (under Velocity's own server): whether the engine runs from the archive and whether it is current.
- **HTTP cache**, **Database queries** (query cache and SQL profile) and **Response cache**, with their buttons for
  users with `setup/managecache`: purge all pages, remove dead entries and reset the counters of the HTTP cache;
  clear the query cache and reset its counters; switch the SQL profile on and off. These are the only things on the
  page that post, and each post carries the form token. **Setup > Caches** (`/setup/cache`) has the same buttons
  in its block "HTTP cache, query cache and SQL profile": both pages post the same fields
  (`HttpCacheAction`, `QueryCacheAction`, `SQLProfileAction`) to the same code, `expCacheManager::sharedActionFromPost()`,
  which checks `setup/managecache` itself. A post without the form token is refused (403).
- **Database connection**: type, server, socket, database name, character set, read replica.
- **Report for support**: the text of the report in a read-only field.

## 6. The report for a support request

**Download report (.txt)** gives a text file like this (shortened):

```
Exponential system information
==============================
Generated: 2026-10-06T04:31:14-07:00
Answered by: Exponential Velocity (persistent workers: one process answers many requests ...)
Health: 9 ok, 0 warnings, 0 failures, 1 notes

[Server]
  Answered by              Exponential Velocity v0.0.4.42+da360b4-dirty
  Web server               QbixServer/1.5.0
  Workers                  148 configured, 12 spare
  Port                     8080 (HTTPS)

[Database]
  Engine                   sqlite 3.34.1
  File                     var/storage/sqlite3/sqlite.db
  Tables                   136
  Size                     389 MB
...
```

**Download as JSON** adds every fact the cards are made from (`facts`) and how many milliseconds each part took to
read (`facts.ms`; about 10 ms in all on alpha). **Copy report as text** copies the same text to the clipboard; it is
shown only with JavaScript, and selects the text for copying by hand where the clipboard is not available.

## 7. What is never shown

What is a secret is decided by one rule, `expSecretRule`, shared by exp:ini and `expIniEditor`, the audit log, the
settings pages (`expSettingsSecretRule`) and this page (`expSystemReportMask`), so none of them shows what another
hides. Each keeps its own mask text and may add names, never remove them. Tests: `expSecretRuleTest`,
`expSystemReportMaskTest`, `expSettingsPageTest`.

- a value whose name is a secret (it contains password, passwd, passphrase, secret, token, salt, credential,
  privatekey or apikey, ends in pwd or dsn, is "key" or ends in Key, _key or -key; SortKey, KeyField and the like are
  not) shows only "(set, hidden)" or "(not set)"; the report adds a session id, a cookie, an Authorization and a
  Signature value; the database user is always hidden;
- the password inside an address is cut: `mysql://user:pw@db/site` becomes `mysql://user:***@db/site`;
- `password=...`, `token: ...` and the like in free text lose their value;
- a run of 26 or more letters and digits with a digit in it (a session id, a token, a key) becomes `***`;
- paths inside the installation are relative to it (`var/site/cache`), paths under a web or home directory outside
  it keep only their last two parts (`…/other/site`); system paths such as `/etc/vc/vc.conf` stay.

`/setup/info/php` calls `phpinfo()` without its variables and environment parts, which carry the HTTP sign-in, the
session cookie and whatever a server puts in the environment. Under Velocity it is plain text, as PHP's
command-line SAPI writes it.

## 8. From the command line

```
./console exp:system:info --allow-root-user            the text report
./console exp:system:info --json --allow-root-user     JSON
./console exp:system:info --sizes --allow-root-user    with directory and database sizes
```

`bin/php/systeminfo.php` is the same command. It reports the **command-line** PHP, which is neither PHP-FPM nor
Velocity: OPcache is usually off there and is not a warning, and `max_execution_time` is "no limit". It exits 1 when
a check fails, so it can guard a deploy script.

## 9. Usual questions

**The figures differ between Apache and Velocity.** They should: two servers, two PHP processes, two OPcaches. Check
the banner before comparing.

**The OPcache hit rate under Velocity is low.** A worker that started minutes ago has compiled every file once;
the rate rises as it serves. The check ignores it under Velocity.

**Sizes say "measured on request".** Open Measure sizes. A size marked "≥" was cut short by the three-second budget.

**The cronjob check warns though cron runs.** Cron runs that write neither to `var/log/cronjob*.log` nor through
Setup > Cronjobs are not seen. Send their output to `var/log/cronjob-<part>.log`.
