# Velocity engines — Qbix, FrankenPHP, PHP's built-in server

Exponential Velocity (`exp:velocity`, `vc`) drives one of three web servers. The
verbs are the same for all of them — `start`, `stop`, `graceful`, `restart`,
`kill`, `status`, `command`, `config`, `install` — and so is `velocity.ini`.

```ini
[ServerSettings]
Engine=php          # shipped default: development, always works
Engine=frankenphp   # production
Engine=qbix         # experimental, for tests
```

`Engine` is the **default** engine — the one `start`, `stop` and `status` use
without `--engine`. It ships as `php`, because PHP's built-in server needs
nothing an installation might lack; a production site sets `frankenphp`. What
an engine is *for* (its role) does not change with that.

| | `qbix` | `frankenphp` | `php` |
|---|---|---|---|
| Role | experimental, for tests | **production** | development (shipped default) |
| Port (shipped) | 8088 (`[ServerSettings]`) | 8089, HTTPS 8444 | 8087 |
| What | the bundled Qbix server, a PHP process | FrankenPHP: Caddy with PHP built in, one binary | `php -S`, PHP's own server |
| Where it comes from | Composer (`se7enxweb/qbix-webserver`) | downloaded by `exp:velocity install`, SHA-256 checked | the PHP that runs `exp:velocity` |
| PHP | the machine's | the binary's own (official 1.12.x: PHP 8.5) | the machine's |
| Requests | persistent or per-request workers | classic mode: a thread pool, clean state per request | `Workers` processes, clean state per request |
| Response cache | yes (`[CacheSettings]`) | not yet | no |
| TLS / HTTP/2 | yes | yes | no |
| graceful | re-exec, socket kept | configuration reload, no dropped connection | restart |
| Config tree `/etc/vc` | yes | no — a generated Caddyfile | no — a router script |

Switch one command to another engine with `--engine`, several with
`--engine=php,qbix`, all with `--all`.

## Running them side by side

For tests the three can run at the same time: each has its own port, pid
file, logs and configuration, so nothing collides.

```bash
./bin/php/console exp:velocity start  --all --allow-root-user   # php :8087, frankenphp :8089, qbix :8088
./bin/php/console exp:velocity status --all --allow-root-user   # role, default, running, port for each
./bin/php/console exp:velocity stop   --engine=qbix --allow-root-user
./bin/php/console exp:velocity stop   --all --allow-root-user
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
`status --engine=<name>` shows that one only. `start --all` ends with the
overview. The exit code and `--json` still describe the default engine.

`[FrankenPHPSettings]` and `[PHPServerSettings]` may set `Port`, `HTTPSPort`,
`Host`, `Workers`, `SpareWorkers` and `DocumentRoot` for their engine. An empty
value falls back to `[ServerSettings]`, which is also the Qbix engine's own.

A site runs one engine. Setup > System information therefore describes, in
detail, only the engine serving the page, and names any other engine of the
installation that is running in one line, as a test setup.

**Upgrading:** until this change the default was `qbix`. An installation whose
deploy scripts run `exp:velocity restart` to mean the Qbix server sets
`Engine=qbix` in `settings/override/velocity.ini.append.php`.

## FrankenPHP

```bash
./bin/php/console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user
./bin/php/console exp:velocity install --allow-root-user     # optional: start installs it
./bin/php/console exp:velocity start --allow-root-user
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
./bin/php/console exp:velocity start --engine=frankenphp              # http :8089 + https :8444
./bin/php/console exp:velocity start --engine=frankenphp --no-https   # plain HTTP, this start only
./bin/php/console exp:velocity start --engine=frankenphp --https      # insist on HTTPS, this start only
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
./bin/php/console exp:velocity start --engine=php --allow-root-user
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
