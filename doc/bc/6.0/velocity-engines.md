# Velocity engines — Qbix, FrankenPHP, PHP's built-in server

Read this page if you run Exponential under Velocity (`exp:velocity`, `vc`), choose between its engines, or deploy
PHP changes to a site that Velocity serves. Velocity drives one of three web servers with the same verbs (`start`,
`stop`, `graceful`, `restart`, `kill`, `status`, `deploy`, `command`, `config`, `install`) and the same
`velocity.ini`.

## In short

| | |
|---|---|
| What changed | `velocity.ini [ServerSettings] Engine` chooses the default engine: `php` (shipped), `qbix` (recommended) or `frankenphp`. `exp:velocity deploy` runs every step a PHP change needs, in order, with PASS, FAIL or SKIP. Several Qbix servers can share one port (`Instances`). |
| Who is affected | Every Velocity user. A changed PHP class reaches Velocity's workers only after a restart; `deploy` does it in the right order. The PHP-FPM that serves the site must be named in `[DeploySettings] PhpFpmService` when `auto` does not find it. |
| How to check | `php bin/php/console exp:velocity status` and `php bin/php/console exp:velocity deploy --dry-run --allow-root-user` |
| How to fix | Set `Engine=qbix` for any real site; deploy PHP changes with `exp:velocity deploy` (add `--kernel` when a kernel class was added or renamed). |

```ini
[ServerSettings]
Engine=php          # shipped default: development, always works
Engine=qbix         # recommended: every stage, development to production
Engine=frankenphp   # production-ready alternative
```

`Engine` is the **default** engine — the one `start`, `stop` and `status` use
without `--engine`. It ships as `php`, because PHP's built-in server needs
nothing an installation might lack; any site, from development to production,
sets `qbix` (Velocity's own server), the recommended and fastest engine. What
an engine is *for* (its role) does not change with that.

| | `qbix` | `frankenphp` | `php` |
|---|---|---|---|
| Role | **recommended**: every stage, development to production | production-ready alternative | development (shipped default) |
| Port (shipped) | 8088 (`[ServerSettings]`) | 8089, HTTPS 8444 | 8087 |
| What | the bundled Qbix server, a PHP process | FrankenPHP: Caddy with PHP built in, one binary | `php -S`, PHP's own server |
| Where it comes from | Composer (`se7enxweb/exponential-velocity`, formerly `se7enxweb/qbix-webserver`) | downloaded by `exp:velocity install`, SHA-256 checked | the PHP that runs `exp:velocity` |
| PHP | the machine's | the binary's own (official 1.12.x: PHP 8.5) | the machine's |
| Requests | persistent or per-request workers | classic mode: a thread pool, clean state per request | `Workers` processes, clean state per request |
| Response cache | yes (`[CacheSettings]`) | not yet | no |
| TLS / HTTP/2 | yes | yes | no |
| graceful | re-exec, socket kept | configuration reload, no dropped connection | restart |
| Config tree `/etc/vc` | yes | no — a generated Caddyfile | no — a router script |

Switch one command to another engine with `--engine`, several with
`--engine=php,qbix`, all with `--all`.

## Deploying a PHP change (`exp:velocity deploy`)

One command does everything a changed PHP class, operator or INI file needs
before it is live, in the one order that works, and says PASS, FAIL or SKIP
for each step with its time:

```bash
php bin/php/console exp:velocity deploy --allow-root-user            # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user   # a kernel class was added or renamed
php bin/php/console exp:velocity deploy --dry-run --allow-root-user  # what it would do, and nothing else
```

| # | Step | By hand |
|---|---|---|
| 1 | extension autoloads | `php bin/php/ezpgenerateautoloads.php -e` |
| 2 | kernel autoloads, only with `--kernel` | `php bin/php/ezpgenerateautoloads.php -k`, restricted to `kernel/` and `lib/` |
| 3 | INI caches | `php bin/php/ezcache.php --clear-tag=ini` |
| 4 | template, template-override, translation and design_base caches | `--clear-id=template`, `--clear-id=template-override`, `--clear-id=translation`, `--clear-id=design_base`, one id per call |
| 5 | engine archive, when Velocity runs from one | `php bin/php/phar.php build`: rebuilt only when a file in `kernel/`, `lib/` or `autoload/` changed, every file parsed first |
| 6 | reload PHP-FPM | `systemctl reload <[DeploySettings] PhpFpmService>` |
| 7 | restart Velocity, when it is running | `exp:velocity restart` |
| 8 | content, exphttpcache, ezjscore-packer (only with `--packer`) and template-block caches | `--clear-id=content`, `--clear-id=exphttpcache`, `--clear-id=ezjscore-packer`, `--clear-id=template-block` |
| 9 | Velocity's response cache | `exp:velocity cache clear` |

**The order is the point.** The caches that hold rendered output (8 and 9)
are cleared only after PHP-FPM and Velocity run the new code. Cleared before,
every page requested in between is rendered by the old code and cached again,
and the change looks as if it had not worked.

**The first step that fails stops the run.** A kernel file that does not parse
fails step 5, before PHP-FPM or Velocity has been touched; a restart that does
not come up leaves every cache as it was. The exit status is 1, and the steps
not run are listed as `NOT RUN`.

**design_base is always cleared.** It holds the list of design directories as
it was when the extensions were last read, so after an extension is activated
its templates are not found until it is cleared: the page answers 200 with an
empty content area.

**The packed scripts and styles go with the template blocks.** The admin's page
head is kept in a template block, and names the packed files. Clear
`ezjscore-packer` on its own and those heads point to files that no longer
exist: the admin loses its scripts and styles until `template-block` is cleared
as well. `--packer` therefore clears it in step 8, immediately before
`template-block`, and never anywhere else. It is needed only when what a packer
server function returns has changed (an `ezjscServer_*` class, or the settings
it reads); an edited `.js` or `.css` file gets a new packed file by itself,
since the packed file's name carries the source files' time.

Options: `--kernel`, `--no-autoload`, `--no-fpm`, `--no-velocity`,
`--rebuild-phar` (rebuild the engine archive even when it is current),
`--packer` (clear the packed scripts and styles, together with the template
blocks), `--engine=<name>` (the engine to restart), `--dry-run`, `--json`.

The archive build checks files 200 to a `php -n -l` call instead of one process per file. With 1,049 files that took nine minutes per build before; it now takes about two seconds for the checks and about 26 for the whole build. A build also writes `dist/engine.phar.index.json`, so `exp:phar build` and every start, restart and graceful rebuild only when something in the archive changed.

```
velocity deploy: /var/www/vhosts/example.com/doc/example.com
  [ 1/14] PASS      1.4s  extension autoloads: var/autoload/ezp_extension.php written
  [ 2/14] PASS      0.3s  INI caches: Clearing ini: Query cache (SQL results), Global INI cache, INI cache, ...
  [ 3/14] PASS      0.4s  template cache: Clearing template: Template cache
  [ 4/14] PASS      0.3s  template-override cache: Clearing template-override: Template override cache
  [ 5/14] PASS      0.6s  translation cache: Clearing translation: TS Translation cache
  [ 6/14] PASS      1.3s  design_base cache: Clearing design_base: Design base cache
  [ 7/14] PASS     31.7s  engine archive: engine.phar rebuilt: 1 changed (kernel/classes/expvelocitydeploy.php)
  [ 8/14] PASS      0.2s  reload PHP-FPM: reloaded plesk-php85-fpm (auto: pool /opt/plesk/php/8.5/etc/php-fpm.d/example.com.conf)
  [ 9/14] PASS      7.5s  restart Velocity: 4 instances started (engine.phar is current, not rebuilt)
  [10/14] PASS      0.3s  content cache: Clearing content: Content view cache
  [11/14] PASS      0.4s  exphttpcache cache: Clearing exphttpcache: HTTP cache (role-aware pages)
  [12/14] PASS      0.4s  ezjscore-packer cache: Clearing ezjscore-packer: eZJSCore Public Packer cache
  [13/14] PASS      0.4s  template-block cache: Clearing template-block: Template block cache
  [14/14] PASS      0.0s  Velocity response cache: response cache cleared (...)
velocity deploy: PASS, 14 steps in 45.2s
```

This run had `--packer` (step 12); without it there are 13 steps.

With no file in the engine archive changed, step 6 says `engine.phar is
current, not rebuilt` and the whole run takes about 7 seconds.

### Which PHP-FPM

```ini
[DeploySettings]
PhpFpmService=auto        # the default
PhpFpmService=disabled    # served by Velocity alone
PhpFpmService=plesk-php85-fpm
```

`auto` finds the pool configuration named after the installation's hosting
domain (`/var/www/vhosts/<domain>/...`, as Plesk names pools) or one that names
the installation's directory, in `/opt/plesk/php/*/etc/php-fpm.d` (service
`plesk-php<XY>-fpm`), `/etc/php-fpm.d` (`php-fpm`), `/etc/php/*/fpm/pool.d`
(`php<X.Y>-fpm`) and `/etc/opt/remi/php*/php-fpm.d` (`php<XY>-php-fpm`). The
service is the pool's master, not the machine's `php-fpm` if that is another
one: on a Plesk server `systemctl restart php-fpm` restarts a PHP no site of
that kind runs on. `--dry-run` shows what was found and why.

The step is a reload, so no request is dropped. It needs root; run as anyone
else, or with no pool found, it is skipped with a note naming what to do.

### Kernel autoloads

`ezpgenerateautoloads.php -k` walks the whole installation. If you keep working copies of the installation inside it
(for example git worktrees in a hidden directory), every kernel class is found twice and the array can end up
pointing into the copy: a plain `-k` run on one installation wrote 1029 entries, every one of them inside such a copy,
and took 17.6 seconds. `deploy --kernel` excludes every top-level directory but `kernel/` and `lib/`, which is where
the kernel's classes are, and takes under a second for the same array. For a run by hand, list such directories in
`.autoloadignore` (one pattern per line, anchored at the installation root) or pass `--exclude=<dir>`.

## Running them side by side

For tests the three can run at the same time: each has its own port, pid
file, logs and configuration, so nothing collides.

```bash
php bin/php/console exp:velocity start  --all --allow-root-user   # php :8087, frankenphp :8089, qbix :8088
php bin/php/console exp:velocity status --all --allow-root-user   # role, default, running, port for each
php bin/php/console exp:velocity stop   --engine=qbix --allow-root-user
php bin/php/console exp:velocity stop   --all --allow-root-user
```

`--all` and engine lists work with start, stop, restart, graceful, kill and
status. Each engine is reported on its own line; one that fails does not stop
the others, and the exit code is non-zero if any did.

`status` shows the addresses to open as full URLs, which a terminal makes
clickable, for each running engine: every site reached by a path (the default
siteaccess at `/`, then the others of `AvailableSiteAccessList`; left out when
siteaccesses are matched by host only), the admin login, Setup > System
information and Setup > Caches, the HTTPS address beside each when the engine
serves TLS, and the Qbix server's dashboard. A stopped engine shows where it
will answer and the command that starts it. Below the addresses the process,
version, configuration, HTTPS (the port, and when it is off what switches it
on) and the logs -- paths relative to the installation -- and the settings the
engine does not act on, one per line. `status` without `--engine` begins
with one line per engine -- role, state, URL -- and the command that stops
each one running, then shows the details of those running (the default's when
none is), so an engine left running from a test is seen. `status --all` shows
the same overview and then the complete status of every engine, stopped ones
included with every address they will answer at: everything, and every URL,
at a glance.
`status --engine=<name>` shows that one only. `start --all` (and
`restart`/`graceful --all`) ends with what `status --all` shows; `start` of one
engine with the overview and that engine's complete status. The exit code and `--json` still describe the default engine.

`[FrankenPHPSettings]` and `[PHPServerSettings]` may set `Port`, `HTTPSPort`,
`Host`, `Workers`, `SpareWorkers` and `DocumentRoot` for their engine. An empty
value falls back to `[ServerSettings]`, which is also the Qbix engine's own.

A site runs one engine. Setup > System information therefore describes, in
detail, only the engine serving the page, and names any other engine of the
installation that is running in one line, as a test setup.

**Upgrading:** until this change the default was `qbix`. An installation whose
deploy scripts run `exp:velocity restart` to mean the Qbix server sets
`Engine=qbix` in `settings/override/velocity.ini.append.php`.

## The Qbix engine's programs: `sbin/` and `bin/`

From Exponential Velocity 0.0.4.41 the engine package keeps its programs the way
UNIX systems do: the server and its administration commands in `sbin/`, the
shell in `bin/` (the engine's `docs/layout.md`, "Programs"). Every former path
still runs the same program in the same process, so nothing in an installation
has to change, and `exp:velocity` works with the engine before and after that
release, deciding by which files are there:

| | Engine 0.0.4.40 and earlier | Engine 0.0.4.41 and later |
|---|---|---|
| the server | `qbixserver.php` | `sbin/qbixserver.php`; `qbixserver.php` forwards to it |
| control | `qbixctl.php` | `sbin/qbixctl.php`; `qbixctl.php` forwards to it |
| console | `qbixconsole.php` | `sbin/qbixconsole.php`; `qbixconsole.php` forwards to it |
| shell | `qshell.php` | `bin/qshell.php`; `qshell.php` forwards to it |
| archive | `bin/qbixserver.phar` | `sbin/qbixserver.phar`; `bin/qbixserver.phar` is the same file |

- `[ServerSettings] ScriptPath` keeps its shipped value,
  `vendor/se7enxweb/exponential-velocity/qbixserver.php`: with the new engine it
  is the forwarder, and a server started by it shows the same command line as
  before, so `ps`, `pkill -f` patterns and monitoring that look for it keep
  matching. Setting it to `.../sbin/qbixserver.php` works too; if the file it
  names is not there (the vendor copy is still the older engine), the other
  path is used.
- `start`, `stop`, `graceful`, `restart` and `status` count a server of this
  installation whichever of the two paths it runs from: one started by
  `exp:velocity start` runs `ScriptPath`, one started by the engine's own
  `qbixctl start` runs `sbin/qbixserver.php` (`expVelocity::scriptPaths()`).
- `exp:velocity ctl`, `ssl` and `ext` run the engine's `sbin/qbixctl.php`, or
  `qbixctl.php` with an older engine (`expVelocity::engineFile()`), and
  `cache clear` loads the engine's control class from its `src/`, which did not
  move (`expVelocity::engineDir()`).

## Several Qbix servers on one port (`Instances`)

A Qbix server answers cached pages in its own process, before any worker is
involved: about 0.3 ms of CPU a page, so one server is bounded by one core.
`[ServerSettings] Instances=N` runs N servers on the same HTTP and HTTPS ports
instead. The generated configuration sets the engine's `Q.webserver.reusePort`,
so every instance opens its listeners with `SO_REUSEPORT` and the kernel spreads
new connections across them.

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
Instances=4
Workers=148        # per instance: the old 590 divided by four
SpareWorkers=12    # per instance: the old 48 divided by four
```

- Each instance has its own worker pool, pid file and log, beside the first
  one's: `server.pid`, `server.1.pid`, ... and `console.log`,
  `console.1.log`, .... Instance 0 keeps the configured names, so
  `Instances=1` (the default) is exactly the single server as before.
- `start`, `stop`, `kill` and `status` reach every instance; `status` lists the
  parents. `graceful` reloads them one at a time, so the port keeps answering.
- The response cache's disk tier is shared (the same cache directory); each
  instance's memory and APCu tiers are its own and warm up separately.
- Only the Qbix engine: FrankenPHP and PHP's server run one process tree each.
- Linux and the BSDs (`SO_REUSEPORT`).

Measured on a test installation (12 cores, 2026-09-27), cached front page over TLS with gzip:

| | one instance | four instances | FrankenPHP |
|---|---:|---:|---:|
| anonymous, 64 concurrent | ~2,950/s | **11,985/s**, p95 10 ms | 4,153/s |
| signed in (the HTTP cache), 8 concurrent | 1,435/s | **3,991/s** | 1,937-3,413/s |

Full renders are not faster with more instances; they are bounded by the
workers and by Exponential itself.

## FrankenPHP

```bash
php bin/php/console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user
php bin/php/console exp:velocity install --allow-root-user     # optional: start installs it
php bin/php/console exp:velocity start --allow-root-user
```

### The binary

`install` fetches the release asset for this machine from GitHub, for the
version pinned in `[FrankenPHPSettings] Version`, and refuses it unless its
SHA-256 matches the value pinned beside it (`Sha256[<asset>]`). It lands in
`var/vc/frankenphp/bin/frankenphp-<version>-<asset>` (git-ignored), next to a
`.sha256` file. `start` installs it on its own when it is missing
(`AutoInstall=enabled`).

| Option | Effect |
|---|---|
| `install --check` | re-hash the installed binary |
| `install --force` | download again |
| `install --from=<file>` | install a file copied here by hand — for a machine without access to GitHub; checked the same way |
| `install --trust-github-digest` | for a `Version` with no `Sha256[]` lines: accept the digest GitHub publishes for the release (weaker: the same source as the file) |

`Variant` picks the build on Linux:

| Variant | Asset | When |
|---|---|---|
| `gnu` (default) | `frankenphp-linux-<arch>-gnu` | any distribution with glibc 2.17+; faster allocator than musl, loads `.so` extensions (oci8 …) |
| `musl` | `frankenphp-linux-<arch>` | Alpine, or where nothing may be taken from the system |
| `mimalloc` | `frankenphp-linux-x86_64-mimalloc` | musl with the mimalloc allocator, x86_64 only |

macOS gets `frankenphp-mac-arm64` or `-x86_64`; Windows is not downloaded.

**A new release** means changing `Version` and every `Sha256[]` line together
(the digests are on the release's GitHub API page, `ApiUrl`). The old binary
stays in `var/vc/frankenphp/bin`, so going back is only the setting.

**An own build** — another PHP version, extra Caddy modules (the response cache
`cache-handler`, for instance) — is named by `BinaryPath`. Nothing is downloaded
then; `BinarySha256`, when set, is checked by `install`. Such builds are made
with FrankenPHP's `static-builder-gnu.Dockerfile` / `static-builder-musl.Dockerfile`.

### PHP settings

The binary reads **no php.ini of the machine**. Without settings PHP would run
on its compiled-in defaults — `display_errors` on, a 128 MB memory limit. So
`[FrankenPHPSettings] IniOptions[]` ships `display_errors=Off`, `log_errors=On`,
`memory_limit=512M`, `opcache.enable=1` and the upload limits; `[PHPSettings]
IniOptions[]` is applied first, a later key wins. `PHPIniFile=` points PHP at a
php.ini of your own instead (`PHPRC`).

OPcache needs no `opcache.revalidate_freq=0` here: every request has its own
request time, so edited and regenerated PHP files are picked up.

Each value reaches PHP as ini text, where a `;` starts a comment, so a value
holding one (`session.save_path=0;0660;<dir>`) is written quoted into the
Caddyfile. Before that was done, the session path arrived as `0`, no session
was ever found and nobody could stay signed in on this engine.

`[PHPSettings] IniOptions[]` also sets `apc.shm_size=256M` for both engines:
the command-line default is 32 MB, in which the response cache and the SQL
query cache evicted each other (a Velocity render cost 727 ms of CPU, 303 ms
with 256 MB).

### The generated Caddyfile

`var/vc/frankenphp/run/Caddyfile` is written from `velocity.ini` on every
`start`, `graceful` and `restart`, and checked with `frankenphp validate` before
it replaces the previous one — a broken configuration never reaches the running
server. `exp:velocity ctl caddyfile` prints it; `ctl validate`, `ctl adapt`,
`ctl version`, `ctl list-modules`, `ctl build-info` run the binary's own
commands with it.

The routing is `.htaccess_root`'s: the listed design, extension, image and
cache paths are files, `api/` goes to `index_rest.php`, the tree menu to
`index_treemenu.php`, everything else to `index.php`. It deliberately does
**not** use `php_server`, whose default is to serve every file that exists —
`settings/*.ini`, the SQLite database, the kernel sources.

Directives of your own (headers, redirects, extra routes) go in a file named by
`[FrankenPHPSettings] SiteInclude`, imported into every site block. Do not edit
the generated file.

### HTTPS

FrankenPHP serves HTTPS on `HTTPSPort` (8444) beside plain HTTP on `Port`,
**by default** (`[FrankenPHPSettings] HTTPS=enabled`), so a development machine
has `https://` from the first start:

```bash
php bin/php/console exp:velocity start --engine=frankenphp              # http :8089 + https :8444
php bin/php/console exp:velocity start --engine=frankenphp --no-https   # plain HTTP, this start only
php bin/php/console exp:velocity start --engine=frankenphp --https      # insist on HTTPS, this start only
```

`HTTPS=disabled` switches it off for good. It uses
`[HTTPSSettings] Certificate` and `Key` when both are set (both must exist).
With both empty it makes a **self-signed certificate** for this machine --
`localhost`, `127.0.0.1`, `::1` and the host name, SHA-256, valid a year --
in `var/vc/frankenphp/tls/` (key 0600) and renews it a month before it runs out.
A browser warns about it once; it is meant for development and for a reverse
proxy in front, not for visitors -- a public site names its certificate or
terminates TLS in front. When the self-signed certificate cannot be made (no
openssl extension, an unwritable directory) the server starts with plain HTTP
and the start message says why; with `--https`, or a named certificate that
does not exist, the start is refused instead. `[HTTPSSettings] Enabled=true` with a
certificate switches it on as before. `status` says which certificate is in
use and lists the HTTPS address beside the HTTP one, also for a server started
with `--https`. `--https` is the frankenphp engine's: the built-in server has
no TLS, and the Qbix server takes `[HTTPSSettings]`.

### Control

`stop` and `graceful` use Caddy's admin API, by default on a unix socket at
`var/vc/frankenphp/run/admin.sock` (owner only; no port that could clash with another
installation). A path longer than a socket allows falls back to
`localhost:<Port + 10000>`; `AdminAddress` sets it explicitly. `stop` falls back
to SIGTERM when the API does not answer; `graceful` falls back to a restart.

`graceful` loads the new configuration without dropping a connection; the PHP
threads restart with the new interpreter settings. Measured: a thread count
change under a request loop, 66 of 66 requests answered, the process kept.

Caddy keeps its state under `var/vc/frankenphp/caddy/` (`XDG_DATA_HOME`,
`XDG_CONFIG_HOME`), not in the home directory of the service user.

### Logs

Everything Velocity (`vc`) writes is below `var/vc/`, **one directory per
server**, so what belongs together is kept together:

```
var/vc/
├── qbix/
│   ├── etc/    the configuration tree            (/etc/vc with root)
│   ├── lib/    what is known about each site     (/var/lib/vc with root)
│   ├── log/    site-access.log, site-error.log, velocity-layout.log   [LogSettings] Dir
│   └── run/    server.pid, console.log
├── frankenphp/
│   ├── bin/    the binary (exp:velocity install)
│   ├── caddy/  Caddy's state
│   ├── tls/    the self-signed certificate
│   ├── log/    access.log, error.log              [FrankenPHPSettings] LogDir
│   └── run/    server.pid, console.log, Caddyfile, admin.sock
└── php/
    ├── log/    server.log                         [PHPServerSettings] LogFile
    └── run/    server.pid
```

`status` names each file. Caddy writes JSON, so FrankenPHP's `access.log` and
`error.log` (or `AccessLog`/`ErrorLog`) are its own — switching engines never
leaves one file in two formats. `LogSettings Enabled` and `FileMode` apply;
`Format` does not. Caches stay where they were (`[CacheSettings] Dir`,
`var/tmp/precompress`), and so does `var/tmp/velocity-server.json`, the Qbix
server's configuration before the vc layout, which older setups read.

**Upgrading:** until 2026-09 the logs were in `var/log/qbix/` (Qbix server,
and FrankenPHP's `frankenphp-access.log`/`frankenphp-error.log`) and
`var/tmp/velocity-php.log`, the pid files, console logs, the Caddyfile and the
admin socket in `var/tmp/`, the Qbix configuration tree in `var/vc/etc` and
`var/vc/lib`. That tree is moved to `var/vc/qbix/` on the next start, with
whatever was edited in it, when nothing is there yet; the other old files
stay where they are. A server started
before the upgrade is still found and stopped (FrankenPHP by its old
Caddyfile path, since its admin socket moved). An installation that sets
`[LogSettings] Dir`, `PidFile` or `LogFile` itself keeps what it set; a
script that reads `var/tmp/velocity.pid` reads
`var/vc/qbix/run/server.pid` now.

## PHP's built-in web server

```bash
php bin/php/console exp:velocity start --engine=php --allow-root-user
```

`php -S Host:Port -t DocumentRoot bin/php/velocity-router.php`, with the PHP
that runs `exp:velocity` (or `[PHPServerSettings] PHPBinary`), its php.ini and
`[PHPSettings] IniOptions[]`. Nothing to install. `Workers` becomes
`PHP_CLI_SERVER_WORKERS` (Linux); the request log is the server's console,
`var/vc/php/log/server.log` — requests and PHP's errors in one file,
since `php -S` does not separate them.

It is a development server, as the PHP manual says: plain HTTP, no reload
(`graceful` restarts), static files without a cache lifetime. Keep `Host` at
127.0.0.1.

`bin/php/velocity-router.php` does what `.htaccess_root` does. It judges a
static path **as it resolves**: `/design/standard/stylesheets/../../../settings/site.ini`
starts like a stylesheet, and the built-in server would resolve the dots and
send the file. A resolved path outside the root, or outside the list, or a
script, is a 404.

## Settings each engine ignores

`start` and `status` name every setting an installation set that the engine in
use does not act on — `CacheSettings` on FrankenPHP, `ForkPerRequest` and
`KeepGlobals` (nothing is kept between requests there), `LogSettings Format`,
the Qbix server's `Brand*` and dashboard settings, `HTTPSSettings` on the
built-in server. Shared settings: `Host`, `Port`, `HTTPSPort`, `DocumentRoot`,
`Workers` (FrankenPHP: threads; `SpareWorkers` more under load), `StaticMaxAge`,
`HTTP2`, `HTTPSSettings`, `LogSettings`, `PHPSettings`, `EnginePhar`,
`ControlSettings StopTimeout`.

## What is served as a file

All three engines follow `.htaccess_root`: the asset directories it lists
(`expVelocity::STATIC_PATHS` — design and extension stylesheets, images and
scripts, `var/*/storage/images`, the public caches, icons, package previews,
`favicon.ico`, `robots.txt`) are sent as files; everything else goes to a
front controller — `/api/` to `index_rest.php`, the admin tree menu to
`index_treemenu.php` (`expVelocity::FRONT_CONTROLLERS`), the rest to
`index.php`. No other `.php` file runs because its path was asked for
(`expVelocity::ENTRY_SCRIPTS`: `index.php`, `index_rest.php`), and a file
outside the list is not handed out: the originals in `var/*/storage/original`
reach a visitor only through `content/download`, which checks who is asking.

| | frankenphp | php | qbix |
|---|---|---|---|
| assets | `file_server @static` | router returns the file | `Q.web.static.paths` |
| scripts and dot paths below an asset directory | `not path_regexp` `NEVER_STATIC` → `index.php` | same list → `index.php` | not static (`.php` never is, dot paths are blocked) |
| other `.php` by name | `rewrite @front /index.php` | router → `index.php` | `Q.webserver.scripts` |
| `/api/`, tree menu | `rewrite @rest`, `@treemenu` | router | `Q.webserver.frontControllers` |

The three Qbix keys need a qbix-webserver that has them; an older one ignores
them, runs any `.php` file requested by name and sends every file with a
served extension — `.htaccess` is not read on its pooled path.

## Symbolic links out of the document root

`[ServerSettings] FollowSymlinks=disabled` (the default) refuses a file that a
link inside the document root leads to outside of it: the qbix engine answers
403 (the server's own check, `Q.webserver.followSymlinks`), the php engine 404
(the router compares the resolved path with the root). A link placed by an
upload, an installer or a shared asset tree would otherwise hand out whatever
it points at.

`enabled` is for installations that link on purpose — extensions linked in from
a shared checkout, `var/storage` on another disk. Velocity then writes
`followSymlinks: true` into the Qbix config and passes
`EXP_VELOCITY_FOLLOW_SYMLINKS=1` to the router. A path with `..` segments is
refused either way, and neither engine serves a `.php` file as a file. Caddy's
file server always follows links; on FrankenPHP `status` names
`FollowSymlinks=disabled` as a setting it does not act on.

Verbs that belong to the Qbix server: `ssl`, `layout migrate`,
`site|conf|mod enable|disable` refuse on the other engines; `layout` lists the
files the engine uses instead; `cache clear` succeeds with nothing to clear.

## Setup > System information

- **"Webserver"** names the software (FrankenPHP with its and Caddy's version
  and the mode; PHP's built-in server with its workers).
- **"Velocity: <engine> (<role>)"** appears when a Velocity engine serves the
  page: its role, whether it is the default and how the others are started,
  its address and who can reach it, version, process, logs, the notes about
  settings it does not use, and the **views the server answers itself**, each
  linked, with who may open it (below). Other engines of the installation that
  are running are named in one line. A server that answers on another port
  than velocity.ini gives the engine (started by hand) is marked as such.
- **"Phar App Engine"** (the kernel archive, `EnginePhar`) is shown for the
  qbix engine only. The archive also runs on the other engines and servers
  (`EXP_ENGINE_PHAR`), but is not described there.
- **"OPcache and APCu"**, for every server — Velocity or not, Apache, nginx
  with php-fpm: enabled or why not, memory, entries, hit rate, restarts, and
  the settings that decide whether an edited PHP file is picked up
  (`validate_timestamps`, `revalidate_freq`). `apc.enable_cli` is listed only
  for command-line servers (qbix, php), which depend on it. The figures are
  the answering server process's own. Shown as bars (memory, fill level, hit
  rate; green, amber from 75 %, red from 90 % — for the hit rate the other way
  round), with a pending reset named.
- **Setup > Caches** empties both, in a section "PHP caches of this server
  process" below "Clear all caches" (which does not include them), for users
  with `setup/managecache`, as a POST checked by the form token:
  - *Reset OPcache*: `opcache_reset()` under php-fpm, FrankenPHP and `php -S`
    — the next request compiles from scratch. Under a command-line server
    (qbix) OPcache never sees the process idle, so a reset would stay pending
    until the server restarts; there every cached script is invalidated
    instead (`opcache_invalidate()`), which takes effect at once. Only a
    restart gives that memory back.
  - *Empty APCu*: `apcu_clear_cache()`; under qbix that includes the memory
    tier of its response cache.
- Without Velocity, "Webserver" names the server from `SERVER_SOFTWARE` (for
  example nginx 1.14.1, SAPI fpm-fcgi) and there is no Velocity box.

Tokens are never put into links; the page itself is for administrators only.

## Views and who may open them

| Engine | View | What | Who may open it |
|---|---|---|---|
| qbix | `/Q/dashboard` | live dashboard | this machine; elsewhere with `[DashboardSettings] Token`. **With a panel password set, only the panel login opens it, from this machine too — the Token does not** (Qbix `Dashboard::handle()`) |
| qbix | `/Q/stats`, `/Q/metrics`, full `/Q/health` | dashboard figures, Prometheus, health report | this machine; elsewhere with the Token or the panel login; or everyone with `Remote=enabled` and no token |
| qbix | `/Q/phpinfo` | phpinfo() including the process environment | this machine only; once a Token or panel password exists, only with it, from this machine too |
| qbix | `/Q/panel` | control panel | this machine only, then the panel password (the first visit sets it) |
| qbix | `/Q/docs`, `/Q/cluster/status`, `/Q/health` status | documentation, cluster state, "ok" | everyone |
| frankenphp | `/Q/health` | empty 200 | everyone |
| frankenphp | admin API | `var/vc/frankenphp/run/admin.sock` | not a web page: the user running the server only |
| php | `/Q/health` | empty 200 | everyone |

The Qbix rules are the server's own (`Q_WebServer::adminAllowed()`, v0.0.4.27);
the page reads the running server's token, remote setting and panel password
rather than assuming velocity.ini's.

## The engine itself: releases, upgrade notes and feature pages

This page is about driving the engine from Exponential. What the engine is, how it came to be and what each of its releases changed is documented separately:

| You want | Read |
|---|---|
| What changed in a release | [Changelog of the Velocity engine](../../changelogs/extensions/exponential-velocity.md) |
| What to change when you upgrade the engine (0.0.4.27 to 0.0.4.42) | [Velocity engine upgrade notes](velocity-engine-upgrade-notes.md) |
| The story month by month | [Velocity chronicle](../../history/velocity/README.md) |
| The web server, control panel, cache, HTTPS, shell, packages | [web server](../../features/6.0/velocity-web-server.md), [control panel](../../features/6.0/velocity-control-panel.md), [response cache](../../features/6.0/velocity-response-cache.md), [HTTPS](../../features/6.0/velocity-https-certificates.md), [Q shell](../../features/6.0/velocity-q-shell.md), [packages and binaries](../../features/6.0/velocity-packages-and-binaries.md) |
| Every setting of the engine | [Engine settings](../../specifications/6.0/velocity-engine-settings.md), [worker pool](../../specifications/6.0/velocity-worker-pool.md), [HTTP/2 and security](../../specifications/6.0/velocity-http2-and-security.md) |

The access rules in "Views and who may open them" above are the ones `exp:velocity` writes into the engine's configuration; they can be stricter than the engine's own defaults described in the control panel page.

## Related pages

- [Velocity: running Exponential in a persistent-worker web server](../../features/6.0/velocity-persistent-worker-server.md)
- [Velocity engine upgrade notes](velocity-engine-upgrade-notes.md)
- [Velocity on-disk layout](velocity-ondisk-layout.md)
- [HTTP/2 and cache warming](http2-and-cache-warming.md)
- [FrankenPHP](frankenphp.md)
- [Engine archive (phar)](phar.md)
- [Deploying guide](../../guides/deploying.md)
