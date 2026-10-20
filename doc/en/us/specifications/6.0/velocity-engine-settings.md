# Specification: Velocity engine settings and programs

This page is the reference for the Velocity engine's own settings (JSON under the `Q` key), where they are read
from, the programs the engine ships, logging, branding, the event loop, TLS session reuse and the start-up
pre-warm cache. Read it when you configure Velocity outside Exponential's `settings/velocity.ini`, read its logs,
or need the default of an engine key. It applies to Exponential Velocity 0.0.4.42.

From Exponential, the engine is normally configured through `settings/velocity.ini`; see
[Velocity engines](../../bc/6.0/velocity-engines.md). The settings of larger subsystems have their own pages:
[response cache](../../features/6.0/velocity-response-cache.md), [HTTPS](../../features/6.0/velocity-https-certificates.md),
[control panel](../../features/6.0/velocity-control-panel.md), [Q shell](../../features/6.0/velocity-q-shell.md),
[worker pool](velocity-worker-pool.md), [headers and limits](velocity-http2-and-security.md).

In the tables below, a key without the `Q.` prefix is relative to `Q` (`webserver.keepAlive.max` is
`Q.webserver.keepAlive.max`).

## Where settings come from

Settings are JSON under the `Q` key. They are read in this order, and a later source wins: the engine's built-in defaults; the configuration tree (`qbix.conf`, `ports.conf`, `conf-enabled/`, `mods-enabled/`, `sites-enabled/`, in `/etc/qbix`, with overlays such as `/etc/vc` stacked on top); the file given by `--config`; and the control panel's store (only for the settings the panel offers).

To print what would be loaded, without starting the server:

```bash
php sbin/qbixserver.php --root=web --layout
php sbin/qbixserver.php --root=web -t --config=/etc/qbix/sites-enabled/default.conf
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
| `webserver.hosts.<host>.log` | none | `true` for `<host>-access.log` and `<host>-error.log` in the server's log directory, or an object that overrides `dir`, `accessName`, `errorName`, `fileMode`, `dirMode` |
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
| `log.*` | no log without it | The access and error log, see "Logging" below. The logs are written only when `Q.webserver.log` is present |

### Logging

Settings under `Q.webserver.log` (checked against `Q_WebServer_Log` in the engine; **nothing is logged until the block exists**, even an empty-looking one such as `{"dir": "logs"}`). From Exponential, `[LogSettings]` in `settings/velocity.ini` writes this block for you (its `Format` default is `combined`, not the engine's `vhost`).

| Key | Default | Meaning |
|---|---|---|
| `dir` | `<app>/logs`, else `logs` | Directory of the log files |
| `access`, `error` | `true`, `true` | Switch one of the two logs off with `false` |
| `accessName`, `errorName` | `access.log`, `error.log` | File names: a bare name, no directory part (anything else falls back to the default, so a name cannot write outside `dir`) |
| `format` | `vhost` | `vhost`, `qbix`, `combined`, `common` or a format string with Apache tokens (`%h %t %r %>s %b %D %T %{ms}T %{Name}i` and others) |
| `bufferSize` | `65536` | Bytes buffered before a write; `0` writes every line |
| `flushInterval` | `1` | Seconds between timed flushes |
| `maxSize` | `52428800` | Size at which a file is rotated |
| `archiveAfterDays` | `2` | Older logs are compressed to `.gz` |
| `deleteAfterDays` | `30` | Older logs are deleted |
| `fileMode`, `dirMode` | `0666` less umask, `0755` | Permissions; `0640` and `0750` keep other accounts from reading who visited |

Rotation is daily and inserts the date before `.log` (`site.log` becomes `site.2000-10-10.log`). A virtual host logs to its own files by adding `log` to its `webserver.hosts.<host>` entry (`true`, or an object that overrides `dir`, `accessName`, `errorName`, `fileMode`, `dirMode`); a request for a host without one goes to the server's log. Named formats: `common` is `%h %l %u %t "%r" %>s %b`; `combined` adds the referrer and user agent; `qbix` adds the time in milliseconds; `vhost` adds the host name in quotes.

### The access log

Since 0.0.4.28 the default format is `vhost`: the older `qbix` line plus the host name, in quotes, at the end. A log reader anchored at the end of the line (fail2ban, a GoAccess custom format) needs updating, or set `{"Q":{"webserver":{"log":{"format":"qbix"}}}}` to keep the old lines. Pooled responses are logged when they go out, not when dispatched (0.0.3.1), and the line no longer claims HTTP/1.1 for other protocols. Each host's files rotate, archive and prune on the same terms as the server's own; the dashboard's log viewer takes `?host=`.

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

## Brand and the server's own pages

The names and marks shown on the server's own views (`/Q/dashboard`, `/Q/panel`, error pages, directory listings) come from `Q.webserver` (checked against `Q_WebServer_Brand` in the engine):

| Key | Default | Meaning |
|---|---|---|
| `brand` | `Qbix Server` | Product name; shown in headers, titles and the control panel header |
| `brandUrl`, `maintainer`, `maintainerUrl` | empty | Links for the brand and a maintainer credit in the footer (only `http` and `https` URLs are kept) |
| `brandDescription` | built in | Text for link previews |
| `brandIcon` | none | An `.svg`, `.png`, `.jpg` or `.ico` file used as the icon of the pages |
| `brandMark` | first letter of the brand | The letter drawn on the generated icon |
| `brandColor`, `brandAccent`, `brandBackground` | `#7c8aff`, `#22d3ee`, `#0f1117` | Colours of the generated icon |
| `brandFont` | search of common system fonts | Font file for the generated raster icon |

With the default brand and nothing set, the server's own logo is used. The pages also carry icons, a web app manifest and link previews (24 September 2026); every markup file is a design on disk ([designs](../../features/6.0/velocity-control-panel.md#restyle-the-servers-pages-designs)).

## Event loop and TLS

`webserver.eventLoop` chooses the loop that waits for sockets: `auto` takes Revolt when `revolt/event-loop` is installed, else `stream_select`; `iopoll` (PHP's native `Io\Poll`) is opt-in because it has not been run against the real extension; `QBIX_EVENT_LOOP` overrides the setting. Revolt gains speed only with the `ev`, `event` or `uv` extension and measured no faster than `select` up to 2,000 connections; the ceiling below about 1,000 workers is that of `stream_select` ([worker pool](velocity-worker-pool.md)).

A TLS session can be resumed (22 September 2026): the HTTPS listener holds one context with the session cache and ticket key, and every accepted connection inherits it, so a returning visitor, and each new connection within a visit, skips the full handshake. Check against a Velocity HTTPS listener (not a front end such as Apache) with `openssl s_client -connect host:443 -reconnect < /dev/null | grep -c Reused`; more than 0 means sessions are resumed.

## Start-up pre-warm cache

At start the compatibility layer walks the document root and tokenises every PHP file so workers inherit the results. Since 23 September 2026 the result is kept between starts (an Exponential tree of 5,000 files: 4.7 s down to 0.6 s). An entry is reused only when its size and modification time both still match; the file is dropped when the transformer, the PHP version or the root differs. The start-up line reports how many entries were reused: none reused on a second start means the cache could not be written.

| Key | Default | Meaning |
|---|---|---|
| `Q.webserver.compat.persistPrewarm` | `true` | Keep the result between starts |
| `Q.webserver.compat.dir` | `qbixserver-compat` in the system temporary directory | Where it is kept (created `0700`, the file `0600`: it holds transformed copies of your source) |
| `Q.webserver.compat.dirMode` | `0700` | Directory mode |
| `Q.compat.skipSourceCodeTransform` | `false` | Turn the source transform off (see the [worker pool](velocity-worker-pool.md), "The compatibility layer") |

## Binding and files

Without `--user` and a configuration, a server started as root keeps root workers only when the document root belongs to root (the start-up says so). `--socket=PATH` listens on a Unix socket (mode `--socket-mode`, default `0660`). Per-host and server log and cache files take `fileMode` and `dirMode` (defaults: files `0666` less the umask, directories `0755`); for sites that each run as their own user, the host writing the file should own it.

## Scheduler

The server runs handlers on a schedule, like cron, from `Q.scheduler` (introduced 21 July 2026): each task names a handler dispatched with `Q::event()` in a forked child, with `every` seconds, or `times` (`HH:MM`) narrowed by `weekdays` and `monthdays`. It is not a way to run a command-line PHP script. Scheduled tasks give up root like workers do. Fields, examples and limits: [Velocity scheduler](../../features/6.0/velocity-scheduler.md); the engine's own `docs/configuration.md`, section "Scheduler".

## Related pages

- Features: [Velocity web server](../../features/6.0/velocity-web-server.md), [persistent worker server](../../features/6.0/velocity-persistent-worker-server.md), [Scheduler](../../features/6.0/velocity-scheduler.md), [Static files and images](../../features/6.0/velocity-static-files-and-images.md), [WebSockets and events](../../features/6.0/velocity-websockets-and-events.md), [Discovery, federation and deploy](../../features/6.0/velocity-discovery-federation-and-deploy.md), [Packages and binaries](../../features/6.0/velocity-packages-and-binaries.md)
- Specifications: [Worker pool](velocity-worker-pool.md), [HTTP/2 and security](velocity-http2-and-security.md)
- Upgrade notes: [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md), [Velocity engines](../../bc/6.0/velocity-engines.md), [Velocity on-disk layout](../../bc/6.0/velocity-ondisk-layout.md)
- History: [Velocity chronicle](../../history/velocity/README.md), [Velocity changelog](../../changelogs/extensions/exponential-velocity.md)
