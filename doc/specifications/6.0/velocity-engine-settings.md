# Velocity engine settings and programs

*Reference. Applies to: Exponential Velocity 0.0.4.42. The settings of larger subsystems are in their own pages: [response cache](../../features/6.0/velocity-response-cache.md), [HTTPS](../../features/6.0/velocity-https-certificates.md), [control panel](../../features/6.0/velocity-control-panel.md), [Q shell](../../features/6.0/velocity-q-shell.md), [worker pool](velocity-worker-pool.md), [headers and limits](velocity-http2-and-security.md). From Exponential the engine is configured through `settings/velocity.ini`; see [Velocity engines](../../bc/6.0/velocity-engines.md).*

## Where settings come from

Settings are JSON under the `Q` key. They are read, later wins, from: the engine's built-in defaults; the configuration tree (`qbix.conf`, `ports.conf`, `conf-enabled/`, `mods-enabled/`, `sites-enabled/`, in `/etc/qbix`, with overlays such as `/etc/vc` stacked on top); the file given by `--config`; and the control panel's store (only for the settings the panel offers). Print what would be loaded without starting:

```bash
php sbin/qbixserver.php --layout
php sbin/qbixserver.php -t --config=/etc/qbix/sites-enabled/default.conf
```

| Option | Meaning |
|---|---|
| `--conf-dir=DIR` | A configuration directory laid out like `/etc/apache2` (`auto` is `/etc/qbix` with overlays) |
| `--config=FILE` | A JSON site file, usually `DIR/sites-enabled/SITE.conf` |
| `--distribution=NAME` | Load `Q_WebServer_Distribution_NAME`'s additions (default: `QBIX_DISTRIBUTION`, then the `DISTRIBUTION` file). Velocity's distribution is `vc`, which stacks `/etc/vc` on `/etc/qbix` |
| `--layout` | Print the configuration files that would be loaded, and exit |

The tree: `qbix.conf` (base, like `apache2.conf`), `ports.conf` (listen ports), `envvars` (environment of the server process, including `QBIX_RUN_USER` and `QBIX_RUN_GROUP`), `conf-available/` and `conf-enabled/`, `mods-available/` and `mods-enabled/`, `sites-available/` and `sites-enabled/` (enabled by symlink), `designs/`, `ssl/`, `acl/`. State lives in `/var/lib/qbix` (`/var/lib/vc`), logs in `/var/log/qbix` (`/var/log/vc`). Details and the migration of an existing installation: [Velocity on-disk layout](../../bc/6.0/velocity-ondisk-layout.md).

## Programs

| Program | What |
|---|---|
| `sbin/qbixserver.php` (phar: `sbin/qbixserver.phar`) | The server |
| `sbin/qbixctl.php` | `start`, `stop`, `restart`, `graceful`, `status`, `-t`, `-S`, `ensite`, `dissite`, `enconf`, `disconf`, `enmod`, `dismod`, `panel:password`, `panel:check`, `panel:2fa`, `ext:*`, `ssl:*` |
| `sbin/qbixconsole.php` | Every console command; `qbixconsole list`, `qbixconsole help <command>` |
| `bin/qshell.php` (`vc-qshell` when installed from a package) | The [Q shell](../../features/6.0/velocity-q-shell.md) at a terminal |
| `sbin/uwebserver` | The small C [static file server](../../features/6.0/velocity-uwebserver.md) |

Since 0.0.4.41 the daemon and its administration commands are in `sbin/` and the user commands in `bin/`. Every former path (`qbixserver.php`, `qbixctl.php`, `qbixconsole.php`, `qshell.php` at the top of the tree, `bin/qbixserver.phar`, `bin/uwebserver`) is a forwarder that runs the new file in the same process (same command line, process id and title, standard streams and exit status). A forwarder prints a note on standard error only when it is a terminal; `QBIX_MOVED_QUIET=1` silences it. Composer's `bin` lists the four `sbin/` and `bin/` PHP programs.

All programs accept `--name=value`, `--name value`, `-name=value`, `-name value`, bundled one-letter flags and `--no-flag`, and `--version`, `-version`, `-v`, `-V`, `--about`, `--copyright` (release, build, where it runs from, PHP, system and extensions, licence).

### `qbixctl restart` and logs

`qbixctl restart` restarts with the options the server was started with: a server started with a pid file writes how it was started beside it (`<pid file>.json`: the command line, the interpreter's `-d` settings, the start directory, the `QBIX_*`, `*_CONF_DIR`, `*_STATE_DIR`, `*_DISTRIBUTION`, `*_RUN_USER` and `*_RUN_GROUP` variables, and the output file). Options given to `restart` replace the recorded ones (`qbixctl restart --workers=16`); anything after `--` is added. Without `--log`, the server's output goes to `Q.webserver.log.dir`, else `var/log`, `files/log` or `logs` of the document root or the directory above it, else the configuration tree's log directory (`/var/log/qbix`, `/var/log/vc` for Velocity, or `QBIX_LOG_DIR`), else `var/log` beside the document root; never the temporary directory. `qbixctl status`, `stop` and `graceful` find a running server without its pid file.

## General server settings (`Q.webserver` and `Q`)

| Key | Default | What it does |
|---|---|---|
| `webserver.keepAlive.max` | `100` | Max requests per keep-alive connection; the last one and any `5xx` are answered `Connection: close` |
| `webserver.keepAlive.timeout` | `15` | Seconds before an idle connection is closed |
| `webserver.maxConnections` | `1024` | Max simultaneous connections; beyond it `503` |
| `webserver.fileCache.maxSize` | `64MB` | Memory for cached file responses; the least recently used file makes room when full |
| `webserver.fileCache.maxFile` | `1MB` | Largest file kept in memory |
| `webserver.fileCache.checkInterval` | `1` | Seconds between file modification checks |
| `webserver.rateLimit.enabled`, `.requests`, `.window` | `false`, `100`, `60` | Per-IP rate limiting |
| `webserver.requestTimeout` | `30` | Seconds a request may run before `504`; `0` no limit |
| `webserver.workerMemoryCeiling` | `256` MB or 3/4 of `memory_limit` | Heap past which a worker is replaced |
| `webserver.warmup` | none | Script run once in the parent after the transform, before forking |
| `webserver.eventLoop` | `auto` | `auto`, `iopoll`, `revolt`, `select`; `QBIX_EVENT_LOOP` overrides |
| `webserver.reusePort` | `false` | `SO_REUSEPORT` so several servers share a port |
| `webserver.debug` | `false` | Show an uncaught error's message, class, file, line and trace in the response; off, the application's exception handler runs, else the designed `500` page |
| `webserver.followSymlinks` | `false` | Follow links that leave the document root |
| `webserver.hosts.<host>.root` | the default root | Per-host document root (selected by `Host`) |
| `webserver.hosts.<host>.log` | none | `true` for `<host>-access.log` and `<host>-error.log` in the server's log directory, or `{dir, accessName, errorName, format, fileMode, dirMode}` |
| `webserver.headers`, `headersOnScripts`, `hsts` | see [security](velocity-http2-and-security.md) | Headers on every response |
| `webserver.scripts`, `frontControllers`; `web.static.paths` | all | Which scripts run by name, which file is the front controller, which files are sent as they are (opt-in lists) |
| `webserver.fallback` | none | Catch-all: `"index.html"`, `{"handler":"app/notfound"}` or `{"file":"404.html"}` |
| `webserver.hotReload` | `false` | Watch `classes/`, `handlers/`, `config/`; restart on change (`--hotreload`) |
| `webserver.cgi.patterns`, `webserver.cgi.binary` | `[]`, auto | Scripts that need `php-cgi`, and its path |
| `webserver.user`, `group`, `allowRootWorkers` | see [worker pool](velocity-worker-pool.md) | Who workers run as |
| `webserver.brand`, `brandUrl`, `maintainer`, `maintainerUrl` | `Qbix Server`, empty, empty, empty | The product name and optional links shown on the server's own views; a URL that is not http or https is dropped |
| `dashboard` | enabled | `false` disables `/Q/dashboard`, `/Q/health`, `/Q/ws` |
| `dashboard.token` | none | Require `?token=VALUE` |
| `dashboard.hidePanelRequests` | `false` | Leave `/Q/` requests out of the dashboard |
| `autoload.psr-4`, `autoload.psr-0` | `{}` | Namespace maps, for example `{"App\\": "src/"}` |
| `socket.io`, `socket.js` | `/socket.io`, `/Q/socket.js` | Socket.IO endpoint and the minimal WebSocket client path; `false` disables |
| `app` | empty | App name that prefixes handler function names |
| `compat.statTtl` | `0` | See [worker pool](velocity-worker-pool.md) |
| `web.log.format` | `vhost` | Access log line: `vhost` is the `qbix` format plus the host in quotes at the end; `qbix` is the older line; or a custom format string |

### The access log

Since 0.0.4.28 the default format is `vhost`: the older `qbix` line plus the host name, in quotes, at the end. A log reader anchored at the end of the line (fail2ban, a GoAccess custom format) needs updating, or set `{"Q":{"web":{"log":{"format":"qbix"}}}}` to keep the old lines. Pooled responses are logged when they go out, not when dispatched (0.0.3.1), and the line no longer claims HTTP/1.1 for other protocols. Each host's files rotate, archive and prune on the same terms as the server's own; the dashboard's log viewer takes `?host=`.

### Presets

`--preset=NAME` with `laravel`, `symfony`, `wordpress`, `drupal`, `exponential`. A preset carries a `_webserver` block that merges under `Q.webserver` (that is how the preset reaches `keepGlobals`). The `exponential` preset keeps the source transform on, preserves the type registries across requests, and brings Exponential's own script, front controller and static path lists when the configuration names none; lists that are configured are never widened.

### Environment

| Variable | Meaning |
|---|---|
| `QBIX_RUN_USER`, `QBIX_RUN_GROUP`, `QBIX_RUN_ALLOW_ROOT` (`VC_RUN_*` first under Velocity) | Who workers run as |
| `QBIX_DISTRIBUTION` | Distribution to load |
| `QBIX_CONF_DIR`, `QBIX_STATE_DIR`, `QBIX_LOG_DIR` | Directories |
| `QBIX_EVENT_LOOP` | Event loop backend |
| `QBIX_SHIP_VERSION` | At phar build time: the version the archive shows (set to the release being cut) |
| `QBIX_MOVED_QUIET` | Silence the forwarder note |

## Binding and files

Without `--user` and a configuration, a server started as root keeps root workers only when the document root belongs to root (the start-up says so). `--socket=PATH` listens on a Unix socket (mode `--socket-mode`, default `0660`). Per-host and server log and cache files take `fileMode` and `dirMode` (defaults: files `0666` less the umask, directories `0755`); for sites that each run as their own user, the host writing the file should own it.

## Scheduler

The server can run CLI PHP scripts on a schedule, like cron, from its own configuration (introduced 21 July 2026). Scheduled tasks give up root like workers do. See the engine's `docs/configuration.md`, section "Scheduler".
