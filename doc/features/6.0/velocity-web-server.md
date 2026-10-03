# Velocity web server

*Applies to: Exponential 6.0.x with Exponential Velocity 0.0.4.x. History: [Velocity chronicle](../../history/velocity/README.md).*

## What it is

Velocity is a web server written in PHP that runs your Exponential installation (or any PHP application) from a pool of long-lived PHP processes. It answers HTTP/1.1, HTTP/2 over TLS and WebSockets itself, serves static files, caches pages in its own process, and keeps the application loaded between requests. You do not need Apache, nginx or PHP-FPM in front of it, though it works behind them.

The engine comes from the Qbix web server and is shipped as the Composer package `se7enxweb/exponential-velocity` (earlier `se7enxweb/qbix-webserver`). Exponential drives it with `exp:velocity`; the engine's own programs are `qbixserver`, `qbixctl` and `qbixconsole`.

## Why you would use it

- **Speed without a stack.** One PHP process tree answers everything. Cached public pages are answered in the server process before any worker is chosen, so a hit costs about 0.2 to 0.4 ms of CPU. Measured on one 12-core host with an Exponential front page over TLS and gzip: about 2,950 cached pages a second from one server, about 12,000 from four servers on one port ([Velocity engines](../../bc/6.0/velocity-engines.md)).
- **Persistent workers.** The application is loaded once in a parent process; workers are forked from it, share its memory, and are put back into their forked state between requests. See [Worker pool](../../specifications/6.0/velocity-worker-pool.md).
- **Everything in one place.** HTTPS ([certificates](velocity-https-certificates.md)), a [control panel and dashboard](velocity-control-panel.md), a [shell](velocity-q-shell.md), a [response cache](velocity-response-cache.md), logs and metrics.
- **Small footprint.** A worker of a full CMS holds about 10 MB of private memory (measured); the dynamic pool retires idle workers.

## Start it in two minutes

From an Exponential installation, use the console command (see [Velocity engines](../../bc/6.0/velocity-engines.md) for all verbs and `velocity.ini`):

```bash
./bin/php/console exp:velocity status --allow-root-user
./bin/php/console exp:velocity start --engine=qbix --allow-root-user
```

Without Exponential, from the engine's own tree:

```bash
mkdir -p web && echo '<?php echo "Hello!";' > web/index.php
php sbin/qbixserver.php --root=web --port=8080
```

Open `http://localhost:8080/` and `http://localhost:8080/Q/dashboard`. Stop it with `php sbin/qbixserver.php --stop` (uses the pid file) or `qbixctl stop`.

### The options you use most

| Option | Meaning |
|---|---|
| `--root=DIR` | Document root (default `./web`) |
| `--app=DIR` | A Qbix application directory |
| `--host=IP`, `--port=PORT`, `--https-port=PORT` | Where to listen (defaults `0.0.0.0`, `80`, `443`) |
| `--socket=PATH`, `--socket-mode=MODE` | Listen on a Unix socket (mode default `0660`) |
| `--workers=N` | Pool size; default is what fits in RAM, at most 8 per core and 64, never fewer than 4 |
| `--config=FILE`, `--conf-dir=DIR` | A JSON site file; a configuration directory laid out like `/etc/apache2` |
| `--preset=NAME` | Framework preset: `laravel`, `symfony`, `wordpress`, `drupal`, `exponential` |
| `--user=NAME`, `--group=NAME` | Who workers run as when the server starts as root |
| `--hotreload` | Watch files and restart on change |
| `-t` | Test the configuration and exit |
| `--stop`, `--reload` | Graceful stop, re-exec (through the pid file) |
| `--version`, `--about`, `--copyright` | Release, build, licence |

Values may be written `--name=V`, `--name V`, `-name=V` or `-name V`; flags may be turned off with `--no-name`.

## The `exponential` preset

`--preset=exponential` runs an Exponential installation under the server with one setting. It keeps the source transform on (the legacy kernel calls `header()` and `setcookie()` in the way that depends on the SAPI) and preserves the type registries across requests; both are settings a modern framework does not need. Without configured lists, the preset also supplies the lists of scripts, front controllers and static paths that Exponential ships, so only entry points run and only listed files are served as they are. See [Engine settings](../../specifications/6.0/velocity-engine-settings.md).

## What it serves

| Request | What happens |
|---|---|
| A static file | Sent directly, with `ETag`, `Last-Modified`, ranges, `Accept-Encoding` honoured (including `q=0`), optional precompression and an in-memory file cache that evicts the least recently used file |
| A PHP script | Run in a pool worker; only scripts listed in `Q.webserver.scripts` run by name when that list is set, everything else goes to the front controller |
| A directory | Directory listings and image galleries; the document root can be listed like any other directory |
| A WebSocket or socket.io connection | Held open in a long-lived process (`/socket.io`, `/Q/socket.js`) |
| A Server-Sent Events stream | Supported (added in August 2026) |
| `/Q/dashboard`, `/Q/panel`, `/Q/health`, `/Q/metrics`, `/Q/phpinfo` | The server's own views; see [Control panel](velocity-control-panel.md) |

The server never serves or runs a file outside the document root, even through a symbolic link ([HTTP/2 and security](../../specifications/6.0/velocity-http2-and-security.md)).

## Running legacy applications

A compatibility layer lets Laravel, Symfony, WordPress, Drupal, Joomla and Exponential run unchanged. It rewrites the few PHP functions that do not work in a long-lived CLI process (`header()`, `setcookie()`, `exit`, `die`, `session_id()`; 44 functions are shimmed in all), wraps the file layer so thousands of `include` and `stat` calls per request are cheap, and resets what a request leaves behind. The details and measurements are in [Worker pool](../../specifications/6.0/velocity-worker-pool.md). The one thing to remember: **do not keep per-request state in your own statics or globals without telling the server.** Globals that must survive are named in `Q.webserver.keepGlobals` (or `--keep-globals=A,B`).

## Moving from Apache, nginx or Caddy

The engine ships a migration page for each; the main differences:

1. Replace `.htaccess` rewrite rules by `Q.webserver.frontControllers` (path pattern to script) and `Q.webserver.scripts` / `Q.web.static.paths` (what may run and what may be served as a file).
2. Put TLS in `Q.web.https` ([HTTPS and certificates](velocity-https-certificates.md)).
3. Set `--user` and `--group` so workers do not run as root.
4. Keep Apache or nginx in front only if you need it. Behind a load balancer that ends TLS, the application's own page cache is handed the request headers and the listener's port (release 0.0.4.35), so it finds pages stored for the public host name.

## Limits

- PHP 8.1 or later; the static binaries and images are built for 8.2, 8.3, 8.4 and 8.5.
- Windows has no `fork()`: no pool of forked workers there; use the binary builds for Windows ([Packages and binaries](velocity-packages-and-binaries.md)).
- The default event loop (`stream_select`) stops a pool below about 1,000 workers. Use more servers on one port (`Q.webserver.reusePort`, or `Instances` in `velocity.ini`) rather than more workers.
- A POST is never retried: if a worker dies mid-request the visitor gets a `502`.

## Related pages

- Features: [Scheduler](velocity-scheduler.md), [Static files and images](velocity-static-files-and-images.md), [WebSockets, events and rooms](velocity-websockets-and-events.md), [Discovery, federation and deploy](velocity-discovery-federation-and-deploy.md), [Packages and binaries](velocity-packages-and-binaries.md), [uwebserver](velocity-uwebserver.md).
- Reference: [Worker pool](../../specifications/6.0/velocity-worker-pool.md), [HTTP/2 and security](../../specifications/6.0/velocity-http2-and-security.md), [Engine settings](../../specifications/6.0/velocity-engine-settings.md) (logging, brand).
- Month pages: [July](../../history/velocity/2026-07.md), [August](../../history/velocity/2026-08.md), [September](../../history/velocity/2026-09a.md).

- [Velocity engines](../../bc/6.0/velocity-engines.md): choose and run an engine from Exponential; deploy a PHP change.
- [Velocity on-disk layout](../../bc/6.0/velocity-ondisk-layout.md): the `/etc/vc` tree.
- [Engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md).
- [Changelog](../../changelogs/extensions/exponential-velocity.md).
