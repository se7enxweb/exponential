# exponential-velocity (Exponential Velocity engine): release notes

Read this page before you install or update `exponential-velocity`, or to find out which release brought a change.

Releases of the Velocity web server engine, newest first. The engine is the Composer package `se7enxweb/exponential-velocity` (formerly `se7enxweb/qbix-webserver`; the two repository ledgers are the same history). Each release lists what changed for a user, grouped as Added, Updated (fixes included), Removed and Renamed, and links its feature or upgrade page. Versions count in the last position: `0.0.4.9` is followed by `0.0.4.10`, never `0.0.5.0`. A tag is permanent: a bad release is corrected by the next one, never re-cut.

Three tags have no release page: `v0.0.4.21`, `v0.0.4.22` and `v0.0.4.26` (their builds did not produce a usable artifact; they are still installable versions on Packagist, so move to the next version above them). Releases are published only for a tag that has a section in the engine's changelog.

## v0.0.4.42 (2026-09-30)

Persistent workers report each request's own start time and read sessions without a warning; `uwebserver` becomes a small static file server with a GNU command line, safe defaults and four security reviews; the packages install the shell as `vc-qshell`.

**Added**

- `uwebserver --root=DIR` serves files (ranges, `ETag`, directory listing, `--gzip-static`, hidden-file and symlink policy), with options for listening, TLS, limits, logging and privileges, a configuration file, a manual page, bash completion and a Makefile (`make sanitize`, `make fuzz`).
- The packages install the shell as `/usr/bin/vc-qshell` (container image: `/usr/local/bin/vc-qshell`) ([Q shell](../../features/6.0/velocity-q-shell.md)).

**Updated**

- A pool worker reports each request's own `REQUEST_TIME_FLOAT` and `REQUEST_TIME` (they were inherited from the forking process, so a per-request cache keyed on them, as Exponential has, kept one request's data for the worker's life).
- The compat session handler decodes each value from its own bytes (PHP 8.3 and later reported `unserialize(): Extra data` for every key but the last; values after a float or an object with `__serialize` were misread).
- `uwebserver` never starts on an argument it does not know; its defaults are safe (`127.0.0.1:8000`, no `SO_REUSEPORT`, root needs `--user` or `--allow-root`); hardening against oversize heads, slow clients, descriptor exhaustion, path escape and weak TLS ([uwebserver](../../features/6.0/velocity-uwebserver.md)).

**Removed**

- The generated record types and U runtime compiled into `uwebserver`, and the unused headers `u_runtime.h` and `u_merkle_cache.h`.

Upgrade: [uwebserver defaults](../../bc/6.0/velocity-engine-upgrade-notes.md#to-00442).

## v0.0.4.41 (2026-09-30)

The programs move to `sbin/` and `bin/`, and every former path keeps working.

**Updated**

- The packages install `qbixserver`, `qbixctl`, `qbixconsole` in `/usr/sbin` with links in `/usr/bin`; the systemd unit starts `/usr/sbin/qbixserver`.
- `build-phar.php` writes both phar paths; the workflows, tests and documentation use the new paths.

**Renamed**

- `qbixserver.php`, `qbixctl.php`, `qbixconsole.php` become `sbin/...`; `qshell.php` becomes `bin/qshell.php`; the committed phar is `sbin/qbixserver.phar` (`bin/qbixserver.phar` is the same file); `uwebserver` is `sbin/uwebserver` with C sources in `native/uwebserver/`. Every former path is a forwarder that runs the new file in the same process.

Upgrade: [programs moved](../../bc/6.0/velocity-engine-upgrade-notes.md#to-00441).

## v0.0.4.40 (2026-09-30)

`qbixctl restart` brings a server back as it was started, and its default log is the server's own.

**Updated**

- `qbixctl restart` restarts with the options the server was started with (it used to come back with the default root and ports and time out). A record is written beside the pid file (`<pid file>.json`).
- The default server log is never the temporary directory (`/tmp/qbixserver.log`, one file for all servers, overwritten at each start): it goes to `Q.webserver.log.dir`, else the document root's `var/log`, `files/log` or `logs`, else the configuration tree's log directory.
- `qbixctl start` passes on every server option it is given (`--app`, `--socket`, `--preset`, `--keep-globals`, `--user`, `--group`, `--debug`, `--hotreload`, `--watchdog`, `--allow-root-workers` and the rest).

## v0.0.4.39 (2026-09-30)

The response cache is off unless a setting turns it on.

**Updated**

- The built-in default of `Q.web.cache.enabled` was `true` although the documentation said `false`; it is now `false`. Enabling the cache is an explicit `enabled: true`. An installation that relied on the old default stops caching: add `{"Q":{"web":{"cache":{"enabled":true}}}}`. Disabling the cache module (`qbixctl dismod cache`) now really turns the cache off ([response cache](../../features/6.0/velocity-response-cache.md); [upgrade](../../bc/6.0/velocity-engine-upgrade-notes.md#to-00439)).

## v0.0.4.38 (2026-09-29)

Large uploads get a clean `413` or arrive whole, and compressed files open through the file layer.

**Updated**

- A body over `post_max_size` gets a `413` the client can read (`Connection: close`, the rest discarded: `Q.webserver.timeout.linger` 5 s, `lingerTotal` 30 s); HTTP/2 applies `post_max_size` too (it used to give `502` from 8 MB up); chunked bodies are parsed chunk by chunk; binary uploads just under the limit no longer give `502`; slow uploads are not cut off; framing is read from the head only; `post_max_size = 0` means no limit.
- `compress.zlib://`, `gzopen()` and copies into and out of `.gz` work through the file layer (Exponential packages are `.tar.gz`); stream options work on files.

## v0.0.4.37 (2026-09-29)

**Updated**

- Multipart form fields with nested names (`Attributes[0][id]`) reach `$_POST` as PHP builds them (every `fetch()` with a `FormData` body lost them before); `$_FILES` has PHP's layout.

## v0.0.4.36 (2026-09-28)

**Updated**

- A warm-up that throws halfway leaves nothing of its request in the workers (before it, half of the signed-in admin requests of an installation came back as the anonymous front page, marked publicly cacheable). `Q_WebServer_WarmupGuard` restores the parent's state before the snapshot.
- No deprecation for implicitly nullable parameters on PHP 8.4 and later.

## v0.0.4.35 (2026-09-28)

Security headers on every response, pages found behind a TLS-ending load balancer, and 401 challenges answered as 401.

**Added**

- `Q.webserver.headers`, `headersOnScripts` and `hsts` ([headers](../../specifications/6.0/velocity-http2-and-security.md#headers-on-every-response)); embedded OpenType fonts are served with their media type.

**Updated**

- The application's page cache gets the request headers and the listener's port, so it finds pages stored behind a load balancer that ends TLS.
- `header('WWW-Authenticate: ...')` answers `401`; `getallheaders()` works in pool workers.
- A static file is served whole to a client that takes no gzip when precompression is on (a `200` with `Content-Length: 0` before).
- The `exponential` preset brings its own script, front controller and static path lists when the configuration names none.
- The tests rebuild the phar first and run against it.

## v0.0.4.34 (2026-09-27)

Workers run as the site's user, not as root, and the response caches pause for a maintenance window.

**Added**

- Workers, the zygote, fork-per-request children and scheduled tasks give up root: `--user`, `--group`, `Q.webserver.user`, `Q.webserver.group`, `QBIX_RUN_USER`, `QBIX_RUN_GROUP` (`VC_RUN_*` under Velocity); default: the owner of the document root ([worker pool](../../specifications/6.0/velocity-worker-pool.md#the-user-workers-run-as)).
- `Q.web.cache.pauseFile`: while the file exists neither cache answers.

## v0.0.4.33 (2026-09-27)

**Updated**

- An application reading its own PHP files gets the files, not the transformed source. Exponential's file-consistency check listed about 330 kernel and library files as modified under Velocity.

## v0.0.4.32 (2026-09-27)

Several servers on one port, browsers answered from the response cache, purges seen at once, and the zygote on PHP 8.2 and 8.3.

**Added**

- `Q.webserver.reusePort` (several servers on one port: about 3,500 cached pages a second for one, 6,600 to 6,900 for two, 12,200 to 13,100 for four, on 12 cores with TLS and gzip).

**Updated**

- The zygote runs on PHP 8.2 to 8.5 (the connection is handed over as a stream).
- A gzip answer from the application's cache is kept in the response cache, so browsers are answered from it (724 to 2,637 pages a second at 64 concurrent: 741 to 2,965).
- A purge reaches the response cache within the second; every server holds the same copy of a page.
- PHP 8.5 full binaries build (`memcache` marked unavailable for 8.5).
- Requests without a session cookie are answered from the response cache before the application's cache; the Revolt event loop is documented (it measured 2 to 12 percent slower than `stream_select` up to 2,000 connections).

## v0.0.4.31 (2026-09-27)

**Added**

- An application's own page cache is asked before a worker (`Q.web.appCache`); `--version`, `-v`, `-V`, `--about`, `--copyright` in every program.

**Updated**

- HTTP/2 pages are known as secure (`HTTPS` set); `session.save_path` in PHP's `N;MODE;/path` form is honoured (sessions went to the temporary directory with mode 0644); a checkout reports its own release.

## v0.0.4.30 (2026-09-27)

**Updated**

- v0.0.4.29's worker pool on PHP 8.2 and 8.3: it answered only the first request of each worker forked after start (the rest `502`). The zygote is used only where PHP passes sockets intact.

## v0.0.4.29 (2026-09-26)

A response cache that is faster and says what it is doing, a control panel to run it, and error pages a visitor can use.

**Added**

- Optional TOTP two-factor authentication for the panel, off by default (`Q.panel.twofactor`, `qbixctl panel:2fa`).
- A Cache tab in the panel (`/Q/api/cache`); an optional in-process memory layer in front of APCu; `/Q/health` reports where hits come from; `Q.dashboard.hidePanelRequests`; `Q.webserver.zygote`; `Q.compat.statTtl`; per-domain SNI certificate selection; client IP and user agent on `5xx` metrics.

**Updated**

- Keep-alive: the last response before `keepAlive.max`, and any `5xx`, now say `Connection: close`.
- Every error the server answers itself is the designed page in plain words, over HTTP/2 too; an open dashboard no longer doubles the cost of every request; the static file cache evicts the least recently used file; `q=0` in `Accept-Encoding` is honoured.
- Pool workers are forked from the zygote by default; an uncaught exception's message is no longer sent to the client (the application's exception handler, else the designed `500`; the message goes to the log, and to the response only with `Q.webserver.debug`); APCu is used only when it can hold entries; cached pages are filed under the coding they are stored in (the first request for each page re-renders).
- Worker memory figures corrected from measurements: 1.3 to 1.9 MB private for the bare server, about 10 MB for a full CMS.

Upgrade: [from v0.0.4.28](../../bc/6.0/velocity-engine-upgrade-notes.md#to-00429).

## v0.0.4.28 (2026-09-25, tagged 2026-09-26)

Domains, certificates and a shell in the control panel, and scripts that run only when listed.

**Added**

- Domains tab (status, aliases, subdomains, document roots, redirects, HSTS, error documents, certificates, traffic), SSL tab, bookmarkable tabs, Logs tab filter, `/Q/metrics` for browsers ([control panel](../../features/6.0/velocity-control-panel.md)).
- The Q shell ([Q shell](../../features/6.0/velocity-q-shell.md)); one registry of recognised PHP applications; a PHP extension baseline with `ext:*` commands; release builds for every platform, PHP version and variant; deb and rpm packages; Docker images ([packages and binaries](../../features/6.0/velocity-packages-and-binaries.md)).
- `Q.webserver.scripts`, `Q.webserver.frontControllers`, `Q.web.static.paths`: only listed scripts run by name, only listed files are served as they are (opt-in).
- `qbixctl panel:password`; `qbixctl status`, `stop` and `graceful` find a server without its pid file.

**Updated**

- A second server on the same certificate directory and port no longer takes HTTPS away from the first; a stopping server no longer removes another server's pid file; the shell works from the phar, packages, image and static binaries; the `full` static binaries build again.

**Renamed**

- The package and repository are `se7enxweb/exponential-velocity`; the OS packages are `exponential-velocity` (they replace `qbix-webserver` in place); the default branch is `main`.

Upgrade: [from v0.0.4.27](../../bc/6.0/velocity-engine-upgrade-notes.md#to-00428).

## v0.0.4.27 (2026-09-24)

HTTPS that looks after itself, a worker pool that sizes itself, and workers that stay the size they started. (Includes everything of the unreleased v0.0.4.26.)

**Added**

- Self-managing HTTPS: a self-signed certificate through a chain of providers, renewed and swapped live; certificates from files, archives, PKCS#12; built-in ACME ([HTTPS](../../features/6.0/velocity-https-certificates.md)).
- A dynamic worker pool; a configuration directory like `/etc/apache2`; `qbixconsole` and `qbixctl`; GNU and BSD option spellings; designs on disk for the server's own pages; a toolbar; the response cache generation marker; `Q_WebServer_Pool::retireAfterResponse()`; `Q.webserver.workerMemoryCeiling` and a per-request health check; `Q.webserver.warmup`; the `exponential` preset; `Q.webserver.brand`.

**Updated**

- Files rewritten after start no longer keep running old code; under PHP 8.2 and 8.3's function JIT the source transform wrote a script twice; the admin surface and cluster join were open to anyone who could reach the port; HTTP/2 rapid-reset false positives; a newly forked worker ended every open TLS connection; bodies of 0 to 47 and 58 to 255 bytes were refused.
- Workers grew without limit (an output buffer, an error handler and a file-wrapper registration left behind by each request; cyclic garbage); the worker-memory card summed RSS (it now reports PSS); `$db or die()` ended the worker.

## v0.0.4.26 (2026-09-23, no release page)

**Updated**

- A signed-in visitor's pages were cached and served to everybody else (the session-cookie match missed Exponential's cookie, which carries a digest after the name).

## v0.0.4.25 (2026-09-23)

Three security fixes, and macOS ships for the first time.

**Added**

- The macOS binary (it was never broken); `docs/security.md` in the engine.

**Updated**

- A symbolic link inside the document root served, and executed, files outside it; contradictory request framing was resolved rather than refused (request smuggling); six HTTP/2 frames the RFC says must be refused were accepted; HTTP/2 served files HTTP/1.1 refused (a site configuration file, `.git`); header values could write headers of their own.
- `php-cgi` mode answered "Class Q_WebServer not found"; the server died on FreeBSD after its banner; riscv64 segfaulted under emulation.

## v0.0.4.24 and v0.0.4.23 (2026-09-23)

**Added**

- A pre-warm cache that survives a restart (startup from 4.7 s to 0.6 s on 5,000 files); a platform matrix for musl, DragonFly, illumos, RISC-V and ARMv5. The phar is published as a release asset for the first time (v0.0.4.24).

**Updated**

- The server ignored `SIGTERM` (so `stop` and `restart` timed out); `--stop` and `--reload` exited 0 without sending the signal; WebSocket frames and worker packets were written without checking they went out whole; a host name ending in a newline or a hyphen passed validation; every release carried one frozen name.

## v0.0.4.22, v0.0.4.21 (no release pages) and v0.0.4.20 (2026-09-23)

**Updated**

- The request that fills the response cache is served what was stored (it used to get different bytes from every request after it).

## v0.0.4.16 to v0.0.4.19 (2026-09-23)

**Updated**

- A reused response now says how old it is (`Age`), so a downstream cache does not treat an almost-expired page as fresh (v0.0.4.16); a forked worker no longer holds every visitor's connection open by inheriting the parent's descriptors; the committed archive's staleness check joins the suite a developer runs before a commit.

## v0.0.4.15 (2026-09-23)

**Added**

- A virtual host can keep its own access and error log; log and cache take configurable file and directory modes; the pre-compressed cache no longer defaults to a world-readable directory ([engine settings](../../specifications/6.0/velocity-engine-settings.md)).

## v0.0.4.14 (2026-09-22)

**Added**

- Conditional requests (`304`) for the response cache; stale-while-revalidate and negative caching; optional whitespace collapsing of cached HTML; shared-dictionary compression of stored entries (off by default); a cached response is recognised before routing, the listen backlog is a queue, every response carries a `Date`; the README gains a configuration reference ([response cache](../../features/6.0/velocity-response-cache.md)).

## v0.0.4.9 to v0.0.4.13 (2026-09-22)

**Updated**

- Bound every resource an HTTP/2 peer can make the server hold; refuse a header block that lies about its own lengths (v0.0.4.9); a nullsafe method call is left alone by the source transform (v0.0.4.10); Nagle is disabled on the TLS listener (v0.0.4.11); a cached body is no longer stored inside the JSON that describes it (v0.0.4.12); the compressed body is stored instead of compressing on every hit (v0.0.4.13).

## v0.0.4.0 to v0.0.4.8 (2026-09-22)

**Added**

- HTTP/2 over TLS in five waves: HPACK, frames and connection state, negotiation on the real listener, a page for URLs the server does not serve, body compression with flow control (v0.0.4.0 and v0.0.4.1).

**Updated**

- HTTP/2 responses were never compressed (v0.0.4.2); HTTP/2 cost 800 ms a request (v0.0.4.3); HTTP/2 rendered every page from scratch (v0.0.4.4); the TLS handshake stalled on a timer and sessions could not be resumed (v0.0.4.5); responses were truncated under small windows, and a request can ask the cache to renew an entry with a header (v0.0.4.6); cookies a script set were not sent over HTTP/2, and the port leaked into `SERVER_NAME` (v0.0.4.7); code included from an archive was not transformed (v0.0.4.8).

## v0.0.2.1 to v0.0.3.1 (2026-09-22)

**Updated**

- A raw `Set-Cookie` survives a script's `setcookie()`; HTTPS and `REQUEST_SCHEME` are set over TLS so redirects stop pointing at `http` (v0.0.2.1, v0.0.2.2).
- File uploads work (binary bodies carried to the worker; `is_uploaded_file()` accepts them); the worker count is sized from measured cost; binaries carry `gd`, XML, iconv, intl, mysqli, curl, bcmath and exif (v0.0.2.5).
- The reverse proxy cache is actually switched on (v0.0.2.6), sweeps expired entries (v0.0.2.7); the phar is rebuilt when its sources change and files that need no transform are remembered (v0.0.2.8, v0.0.2.9).
- A static file can say how long it may be kept (v0.0.3.0); the access line is configurable and pooled responses are logged when they go out (v0.0.3.1).

## v0.0.1 (2026-09-21)

The first tag: the engine as adopted from upstream plus the compatibility layer for existing PHP applications, the contributor fixes of 21 September (UPX removal that had corrupted every Linux release, `phpinfo()` as HTML, directory listing of the document root, scripts run from their own directory, `session_id()` shim), and the package name fix.

## Before v0.0.1 (July to 21 September 2026)

Not tagged. The engine as it stood after the first release of a pure PHP web server on 20 July 2026: WebSockets, socket.io, a scheduler, virtual hosts, a control panel, octane (persistent) workers, logging, Server-Sent Events, GitHub Actions builds of binaries, example applications, and the compatibility layer. See the [July](../../history/velocity/2026-07.md), [August](../../history/velocity/2026-08.md) and [September](../../history/velocity/2026-09a.md) chronicles.

## Related pages

- [Velocity history](../../history/velocity/README.md)
- [upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md)
- [feature: web server](../../features/6.0/velocity-web-server.md)
- the engine's own `CHANGELOG.md` in the package.
