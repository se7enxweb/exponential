# Velocity engine upgrade notes (0.0.4.27 to 0.0.4.42)

*Behaviour changes of the Velocity web server engine that can affect an installation, in the order you meet them when you upgrade from an older release. Choosing and running an engine from Exponential is in [Velocity engines](velocity-engines.md); the on-disk tree in [Velocity on-disk layout](velocity-ondisk-layout.md); the engine archive in [phar](phar.md); what Exponential itself changed in [behaviour changes 2026-10](behaviour-changes-2026-10.md). Release notes: [changelog](../../changelogs/extensions/exponential-velocity.md). History: [Velocity chronicle](../../history/velocity/README.md).*

## Before you upgrade

1. Know where you are: `php sbin/qbixserver.php --version` (or `php qbixserver.php --version` before 0.0.4.41) prints the release and build. A checkout reports the release read from git.
2. Read the sections below from your release upwards. Each says who is affected, how to check, and how to fix.
3. After installing the new engine, test the configuration before restarting: `php sbin/qbixserver.php -t --config=<your site file>`; list what would be loaded with `--layout`.
4. Restart (`qbixctl restart` brings a server back with the options it was started with from 0.0.4.40 on; from Exponential use `./bin/php/console exp:velocity restart --allow-root-user`). Then check `qbixctl panel:check` (the panel can be locked, see 0.0.4.28) and `qbixctl ext:check`.
5. A tag is permanent: if a release is bad, the fix is the next release. Versions `v0.0.4.21`, `v0.0.4.22` and `v0.0.4.26` have no release page; move to the next version above them.

## To 0.0.4.28

Affected: everyone who runs the control panel, reads the access log, or uses the OS packages.

- **The panel's credentials and sessions moved and are trusted only when nobody else can change them.** With a configuration tree they live in `<conf>/acl/panel.json` and `<state>/sessions/`; without one, in `local/` beside the application. An existing password is carried over on the first start (the old file is kept, renamed `panel.json.migrated-<time>`, with a copy `panel.json.pre-migration-<time>`). Every directory from the store up to `/` must belong to root or the server's user and not be writable by group or others; otherwise **the panel is locked** (sign-in, default key and every session refused). Check: `qbixctl panel:check --root=...`. Usual fix after an upgrade (an application directory left `0775` by a umask of `002`): `chmod g-w,o-w <dir>`, the directory the refusal names. Or set `Q.panel.aclDir` and `Q.panel.sessionsDir` (or start with `--conf-dir`) to keep the files elsewhere.
- **The panel starts with a default key that must be changed at first sign-in, and passwords are rule-checked and stored with bcrypt.** An existing password keeps working. Set one at once on any server reachable from outside: `qbixctl panel:password --root=... --generate`. See [control panel](../../features/6.0/velocity-control-panel.md).
- **The access log's default format is `vhost`**: the `qbix` format plus the host in quotes at the end of each line. A log reader anchored at the end of the line (fail2ban, a GoAccess custom format) needs updating, or keep the old lines: `{"Q":{"webserver":{"log":{"format":"qbix"}}}}`.
- **Listed scripts, front controllers and static paths are opt-in.** Nothing changes until you set `Q.webserver.scripts`, `Q.webserver.frontControllers` or `Q.web.static.paths`. When you do, only listed scripts run by name and only matching files are sent as they are; the response cache starts a new generation. Anchor the static path patterns and name scripts exactly.
- **The `iopoll` event loop is opt-in.** `auto` chooses Revolt when installed, else `stream_select`, as before.
- **The OS package was renamed `exponential-velocity`** (deb and rpm; unit `exponential-velocity.service`; `/usr/share/exponential-velocity`, `/var/lib/exponential-velocity`, `/etc/default/exponential-velocity`). Installing it over `qbix-webserver` upgrades in place (Replaces/Obsoletes, settings and state carried, service re-enabled and restarted, `/usr/share/qbix-webserver` left as a symlink). Where apt removed the old service before unpacking, run `sudo systemctl enable --now exponential-velocity`. The Docker image is `ghcr.io/se7enxweb/exponential-velocity`; the old image name is deprecated and gets no new tags. See [packages](../../features/6.0/velocity-packages-and-binaries.md).
- **The default branch is `main`** (was `maintain`); the Composer package is `se7enxweb/exponential-velocity`.
- A forgotten panel password: `qbixctl panel:password --root=...`; a locked panel: see above.

## To 0.0.4.29

- **Pool workers are forked from the zygote by default** (`Q.webserver.zygote`, from Exponential `[ServerSettings] Zygote=enabled`). Set it to `false` to fork from the server as before. Without `ext-sockets` the pool falls back on its own. **On PHP 8.2 and 8.3, release 0.0.4.29 itself answers only the first request of each worker forked after start:** go to 0.0.4.30 or later (or set `zygote` to `false`).
- **An uncaught exception's message is no longer sent to the client.** Without `Q.webserver.debug` the response is the application's own exception handler (`set_exception_handler()`), else the designed `500` page. The message, class, file, line and trace go to the error log, and to the response only with `Q.webserver.debug` or `--debug`. Anything that parsed the message out of a `500` body needs debug on.
- **APCu is used only when it can hold entries**: under the CLI that means `apc.enable_cli=1`. With it off, the cache says so at startup instead of running from disk while its settings said memory.
- **Cached pages are filed under the coding they are stored in**: brotli and gzip clients share one entry, so the first request for each page after the upgrade renders again.

## To 0.0.4.31

- Sessions are kept where `session.save_path` says, in PHP's `N;MODE;/path` form, created with its mode (before: the temporary directory, mode 0644, when set with `-d`). If you relied on sessions in `/tmp`, set `session.save_path` explicitly.
- HTTP/2 requests now set `HTTPS` (an application's absolute URLs and secure-request test were wrong before; a page cache keyed by scheme stored HTTP/2 pages under `http://`: clear such a cache once).

## To 0.0.4.33

- A PHP file read with `file_get_contents()`, `md5_file()` or `fopen()` returns the file's own bytes. Under 0.0.4.32 and earlier, Exponential's upgrade check (Setup, System Upgrade, File consistency) listed about 330 kernel and library files as modified when served by Velocity. Re-run the check after upgrading ([file consistency check](../../features/6.0/file-consistency-check.md)).

## To 0.0.4.34

- **Workers no longer run as root.** Started as root, the master keeps root; workers, the zygote, fork-per-request children and scheduled tasks drop to the user and group of the document root (or `--user`, `--group`, `Q.webserver.user`/`group`, `VC_RUN_USER`/`VC_RUN_GROUP`). The cache directories (`Q.web.cache.dir`, `Q.web.appCache.dir`, `Q.webserver.precompress.dir`, `Q.webserver.writable`) are handed to that user at start. Check: the start-up line `Workers as: user:group (...)`. If files under your site are root-owned from earlier runs (image variations, caches), change their owner once: `chown -R <user>:<group> <dir>`. A user or group that does not exist stops the start; root is refused unless `--allow-root-workers`.
- `Q.web.cache.pauseFile` pauses both response caches while a file exists (maintenance windows).

## To 0.0.4.35

- Nothing changes by itself. New, opt-in: `Q.webserver.headers`, `headersOnScripts`, `hsts` ([security headers](../../specifications/6.0/velocity-http2-and-security.md#headers-on-every-response)).
- The `exponential` preset now brings its own script, front controller and static path lists when none are configured: with it, only entry points run and only listed files are served. Narrower lists you set are never widened; if you served other files through the preset before, list them.
- `header('WWW-Authenticate: ...')` answers `401`, as in PHP.

## To 0.0.4.38

- Requests over `post_max_size` get a clean `413` over HTTP/1.1 and HTTP/2 (HTTP/2 used to answer `502` from 8 MB up). Check that `post_max_size` and `upload_max_filesize` fit your largest upload.
- `post_max_size = 0` now means no limit, as in PHP (it used to refuse every request with a body).

## To 0.0.4.39

- **The response cache is off unless a setting turns it on.** Before, the built-in default was on, although the documentation said off. An installation that stated `enabled: true` anywhere (a cache module, the site file) caches exactly as before. **An installation that relied on the old default stops caching:** add `{"Q":{"web":{"cache":{"enabled":true}}}}` to the site file. A module that sets only `dir` or `defaultTtl` no longer turns the cache on by itself. The site file wins over a module; a setting saved in the panel wins over both. `qbixctl dismod cache` now really turns the cache off.
- Check: `curl -sI https://your-host/ | grep -i x-cache` after two requests: no `X-Cache: HIT` means off.

## To 0.0.4.40

- `qbixctl restart` restarts with the options the server was started with (a record `<pid file>.json` beside the pid file). A server started by hand with no pid file is restarted from its process table entry.
- The default server log is no longer `/tmp/qbixserver.log`. It goes to `Q.webserver.log.dir`, else the document root's `var/log`, `files/log` or `logs`, else the configuration tree's log directory (`/var/log/qbix`, `/var/log/vc` for Velocity). A `/tmp/qbixserver.log` left by an earlier version can be removed by hand.
- Server tests no longer hide the default cache setting; nothing changes for an installation.

## To 0.0.4.41

Nothing has to change. The daemon and administration commands are in `sbin/`, the shell in `bin/`; every former path is a forwarder that runs the new file in the same process, with the same command line, process id and title, so `ps`, `pkill -f 'qbixserver.php.*--port=N'` and monitoring keep matching.

| | 0.0.4.40 and earlier | 0.0.4.41 and later |
|---|---|---|
| server | `qbixserver.php` | `sbin/qbixserver.php` (the old path forwards) |
| control | `qbixctl.php` | `sbin/qbixctl.php` |
| console | `qbixconsole.php` | `sbin/qbixconsole.php` |
| shell | `qshell.php` | `bin/qshell.php` |
| archive | `bin/qbixserver.phar` | `sbin/qbixserver.phar` (`bin/qbixserver.phar` is the same file) |
| uwebserver | `bin/uwebserver` | `sbin/uwebserver` (C sources in `native/uwebserver/`) |
| packages | programs in `/usr/bin` | programs in `/usr/sbin`, links in `/usr/bin` |

- A forwarder prints a note on standard error only when standard error is a terminal; `QBIX_MOVED_QUIET=1` silences it.
- Exponential's `[ServerSettings] ScriptPath` keeps its shipped value (the forwarder); `.../sbin/qbixserver.php` also works. `exp:velocity` decides by which files exist ([Velocity engines](velocity-engines.md)).
- A service started before the package upgrade is restarted under the new unit by the upgrade.

## To 0.0.4.42

- Nothing in a site starts `uwebserver`; the engine, packages, image, release workflows and the Exponential installation never run it. If **you** run it:
  - it listens on `127.0.0.1:8000` by default (was every interface at 8080); keep the old address with `--listen='*:8080'`;
  - as root it needs `--user` or `--allow-root`;
  - an unknown option starts nothing and exits 2; `--cert` without `--key` is an error;
  - the `Server` header is `uwebserver`; the start-up line goes to standard error.
- The packages install the shell as `/usr/bin/vc-qshell` (0.0.4.41 did not install it as a command).
- `REQUEST_TIME` and `REQUEST_TIME_FLOAT` are per request in persistent workers. An application that cached on them (Exponential does) no longer serves one request's data to the next. Per-request kernel caches need this release.

## Installer templates (HTML5)

Not part of the engine, but in the same window: the `ezwebin` site package templates (repository `ezwebin-ezpackage`, July 2026) no longer emit XHTML self-closing slashes or obsolete `type` attributes (`<input ... />` becomes `<input ...>`, `<script type="text/javascript">` becomes `<script>`). Browsers are unaffected. Check customised sites for stylesheets, scripts or tests that match the old markup exactly. The legacy kernel installer (`exponential-legacy-installer` 2.2.3) fixes `composer update` failures with Composer 2.10 when the legacy root is `.`; upgrade it if the copy step fails. History: [installers and packages](../../history/velocity/installers-and-packages.md).

## If you need the old behaviour

| Behaviour | How to get it back |
|---|---|
| Workers fork from the server | `Q.webserver.zygote` `false` |
| Old access log lines | `{"Q":{"webserver":{"log":{"format":"qbix"}}}}` |
| Follow symlinks out of the document root | `Q.webserver.followSymlinks` `true` (not recommended) |
| Cache on without stating it | Set `enabled: true` explicitly |
| Workers as root | `--allow-root-workers` (not recommended) |
| uwebserver on every interface | `--listen='*:8080'` |

## Verify after upgrading

```bash
php sbin/qbixserver.php --version
php sbin/qbixserver.php -t --config=/etc/qbix/sites-enabled/default.conf
qbixctl status
qbixctl panel:check --root=/path/to/web
qbixctl ext:check
curl -s https://your-host/Q/health
```
