# 8. Serving the site: Velocity, Apache, nginx and FrankenPHP

An installed Exponential is a directory of PHP files; something has to accept connections, speak HTTP and HTTPS, hand
static files out and pass every other request to a front controller. This chapter shows how. It starts with
**Exponential Velocity**, the application server that ships for Exponential and is the recommended way at every
stage from development to production: with Velocity's `qbix` engine **no separate web server and no separate HTTPS
server is needed**. Velocity listens on the HTTP and HTTPS ports itself, keeps the application loaded in persistent
workers, answers repeat pages from its own response cache and manages its own certificates, including a self-signed
stand-in and Let's Encrypt. The chapter then covers the traditional setups (Apache with PHP-FPM, including on Plesk;
nginx; FrankenPHP), the file permissions every setup needs, and running behind a reverse proxy.

[Previous: 7. Install with one console command](07-console-install.md) ·
[Next: 9. Databases](09-databases.md) ·
[Contents](README.md)

---

## Contents of this chapter

1. [The choice in one table](#81-the-choice-in-one-table)
2. [How a request is served](#82-how-a-request-is-served)
3. [Exponential Velocity, the recommended way](#83-exponential-velocity-the-recommended-way)
   1. [Install the engine package](#831-install-the-engine-package)
   2. [Choose the engine](#832-choose-the-engine)
   3. [Settings: where they live and how to change them](#833-settings-where-they-live-and-how-to-change-them)
   4. [First start](#834-first-start)
   5. [Address, ports, user and group](#835-address-ports-user-and-group)
   6. [Workers: pool size, the dynamic pool and the zygote](#836-workers-pool-size-the-dynamic-pool-and-the-zygote)
   7. [Persistent workers and KeepGlobals](#837-persistent-workers-and-keepglobals)
   8. [The response cache](#838-the-response-cache)
   9. [HTTPS served by Velocity itself](#839-https-served-by-velocity-itself)
   10. [The configuration tree: /etc/vc, Debian style](#8310-the-configuration-tree-etcvc-debian-style)
   11. [Running Velocity as a service](#8311-running-velocity-as-a-service)
   12. [Logs](#8312-logs)
   13. [The dashboard and the control panel](#8313-the-dashboard-and-the-control-panel)
   14. [Deploying a change: exp:velocity deploy](#8314-deploying-a-change-expvelocity-deploy)
   15. [The engine archive: engine.phar](#8315-the-engine-archive-enginephar)
   16. [Command reference](#8316-command-reference)
4. [FrankenPHP](#84-frankenphp)
5. [Apache with PHP-FPM](#85-apache-with-php-fpm)
6. [nginx with PHP-FPM](#86-nginx-with-php-fpm)
7. [File permissions and ownership](#87-file-permissions-and-ownership)
8. [Behind a reverse proxy](#88-behind-a-reverse-proxy)
9. [Checklist](#89-checklist)
10. [References](#810-references)

Every command in this chapter is run from the installation root, the directory that holds `index.php`. The console
is `bin/php/console`; `./console` is the same program where the installation has the usual shortcut. When you run a
command as `root`, add `--allow-root-user`; a script that needs it says so.

---

## 8.1 The choice in one table

| | Exponential Velocity (`qbix`) | FrankenPHP (through Velocity) | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| Role | **recommended** for every stage | production-ready alternative | traditional | traditional |
| Separate web server needed | **no** | no (Caddy is built into the binary) | yes | yes |
| Separate HTTPS / certificate tool needed | **no**: own certificates, self-signed fallback, ACME | no: your certificate or a self-signed one | yes (`mod_ssl` plus certbot or a hosting panel) | yes |
| Where the code comes from | Composer package `se7enxweb/exponential-velocity` | binary downloaded and SHA-256 checked by `exp:velocity install` | the operating system | the operating system |
| Application kept in memory between requests | yes (persistent workers) | no (clean state per request) | no | no |
| Response cache in front of PHP | yes | not yet | no (the kernel's own caches only) | no |
| HTTP/2 | yes | yes | with `mod_http2` | with `http2` |
| Rewrite rules | built in, the same list as `.htaccess_root` | generated Caddyfile, the same list | `.htaccess_root` | written by you (section 8.6) |
| Commands | `exp:velocity start`, `stop`, `graceful`, `deploy`, ... | the same commands | `systemctl`, `apachectl` | `systemctl`, `nginx -t` |
| Suits shared hosting | usually not (needs a long-running process) | usually not | yes | rarely |

PHP's built-in server (`php -S`) is a third Velocity engine. It is the shipped default only because it needs nothing
installed; it is for development, has no TLS and is not for visitors.

Use a traditional server when your hosting does not allow long-running processes, when you already run a tuned Apache
or nginx you want to keep, or when several customers on one machine need operating-system isolation from each other
(run one Velocity per site, each as its own user, or a traditional server with one PHP-FPM pool per site).

## 8.2 How a request is served

Whatever serves the site, the document root is the **installation root** and the same routing applies. It is written
down once in `.htaccess_root`; Velocity's engines and the generated FrankenPHP configuration implement the same list
rule for rule (`expVelocity::STATIC_PATHS`, `FRONT_CONTROLLERS` and `ENTRY_SCRIPTS` in
`kernel/classes/expvelocity.php`).

```
                     request path
                          |
          +---------------+-----------------------------------------------+
          |                                                               |
   a dot file or dot directory (.git, .env, .htaccess)  -----------------> 404
   (.well-known stays reachable)                                          |
          |                                                               |
   an existing .php/.phtml/.phar below the root  ------------------------> 404
          |                                                               |
   /api/...  ------------------------------------------------------------> index_rest.php
   /content/treemenu..., /<siteaccess>/content/treemenu...  -------------> index_treemenu.php
          |                                                               |
   a listed asset path (design and extension stylesheets, images,        |
   scripts and fonts; var/*/storage/images; image originals;              |
   public caches; share/icons; package previews; favicon.ico,             |
   robots.txt, index.js, sw.js, w3c/p3p.xml)  ---------------------------> sent as a file
          |                                                               |
   everything else (URL aliases, modules, /settings/site.ini, ...)  -----> index.php
```

Two consequences matter for security and are covered again in [chapter 13](13-security-hardening.md):

- No PHP file runs because its path was asked for, except the front controllers at the root.
- A file outside the list is never handed out. `settings/site.ini`, the SQLite database or a kernel source requested
  by path reach `index.php` (which answers with a page or a 404), never the file. Uploaded originals other than images
  are only delivered through `content/download`, which checks permissions.

---

## 8.3 Exponential Velocity, the recommended way

Velocity is an application server written for Exponential. A parent process loads the application once and forks
**workers** that answer one request after another; between requests each worker is put back into the state it had
when it was forked. The parent accepts every connection itself, answers static files, response-cache hits and its own
pages without involving a worker, and terminates TLS. That is why a Velocity site needs no Apache, nginx, PHP-FPM or
separate TLS terminator.

```
                    Internet
                       |
         :80 / :443 (or 8088 / 8080 as shipped)
                       |
   +-------------------v--------------------------------------------+
   |  Velocity parent process (root when started as root)           |
   |   - binds the ports, reads the certificate, speaks TLS, HTTP/2  |
   |   - static files, response-cache hits, /Q/ pages               |
   |   - hands PHP requests to an idle worker                       |
   +--------+-------------------+-------------------+---------------+
            |                   |                   |
       worker (site user)  worker (site user)  ...  (forked by the zygote)
       Exponential kept loaded, reset after each request
```

The `exp:velocity` command (also reachable as `exp:vc`, or directly as `php bin/php/velocity.php`) drives three
engines with the same verbs: `qbix` (Velocity's own server, the subject of this section), `frankenphp` (section 8.4)
and `php` (PHP's built-in development server).

### 8.3.1 Install the engine package

The engine is suggested by Exponential's `composer.json` but not installed by default, because it needs PHP 8.1 or
later. Add it to the installation with Composer:

```bash
composer require se7enxweb/exponential-velocity:~0.0.4.42
```

It lands in `vendor/se7enxweb/exponential-velocity` (the former package name `se7enxweb/qbix-webserver` is still
found). The tilde constraint follows the engine's last version position, so `~0.0.4.42` accepts later `0.0.4.x`
releases.

The engine needs PHP's `pcntl` and `posix` extensions to fork and manage workers, `sockets` for the zygote
(section 8.3.6) and `openssl` for HTTPS. To see what this PHP lacks, with the command that installs it on this
operating system:

```bash
php bin/php/console exp:velocity ext check
php bin/php/console exp:velocity ext install-hint <extension>
```

The engine package also exists as an operating-system package (`exponential-velocity`, deb and rpm, with a systemd
unit) and as a Docker image; see [Velocity packages, Docker images and binaries](../features/6.0/velocity-packages-and-binaries.md).
For an Exponential installation the Composer package is what `exp:velocity` drives.

### 8.3.2 Choose the engine

`settings/velocity.ini` ships with `[ServerSettings] Engine=php`, because PHP's built-in server always works. For any
real site set `qbix`:

```bash
php bin/php/console exp:velocity config set ServerSettings Engine qbix
```

| Engine | Role | Shipped ports (HTTP / HTTPS) | Notes |
|---|---|---|---|
| `qbix` | **recommended**: development to production | 8088 / 8080 (`[ServerSettings] Port`, `HTTPSPort`) | persistent workers, response cache, TLS, HTTP/2, `/etc/vc` tree |
| `frankenphp` | production-ready alternative | 8089 / 8444 (`[FrankenPHPSettings]`) | one downloaded binary; clean state per request; HTTPS on by default |
| `php` | development (shipped default) | 8087 / none (`[PHPServerSettings]`) | `php -S` with `bin/php/velocity-router.php`; no TLS, no reload |

`Engine` is only the *default*: `--engine=<name>` reaches another engine for one command, `--engine=php,qbix` several,
`--all` every engine (for `start`, `stop`, `restart`, `graceful`, `kill` and `status`). Each engine has its own port,
pid file and logs, so they can run side by side for tests. A site is served by one.

### 8.3.3 Settings: where they live and how to change them

| File | What it is |
|---|---|
| `settings/velocity.ini` | the shipped defaults, every setting commented; never edit it |
| `settings/override/velocity.ini.append.php` | this installation's values; written by `exp:velocity config set` |
| `/etc/vc/...` (or `var/vc/qbix/etc/` without root) | the engine's configuration tree, generated from the two files above on every start (section 8.3.10) |

```bash
php bin/php/console exp:velocity config list                 # every setting; * marks the ones you set
php bin/php/console exp:velocity config list HTTPSSettings   # one block
php bin/php/console exp:velocity config get ServerSettings Workers
php bin/php/console exp:velocity config set ServerSettings Workers 16
php bin/php/console exp:velocity config unset ServerSettings Workers   # back to the shipped value
php bin/php/console exp:velocity config paths                # which file is which
```

A changed setting reaches the server at the next `restart` (or `graceful`). The workers hold the application in
memory, so the same is true for a changed class or INI file of the application itself (section 8.3.14).

### 8.3.4 First start

```bash
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status
```

`start` writes the configuration, starts the server in the background (detached with `setsid`), waits until the port
answers and then prints the status. `status` lists every address to open as a full URL: each site reached by a path,
the administration login, Setup > System information and Setup > Caches, the HTTPS address beside each plain one when
HTTPS is on, and the server's own dashboard. Below that it shows the process, version, configuration, HTTPS state,
log paths and any setting that the running engine does not act on.

With the shipped values the site answers on `http://127.0.0.1:8088/`, reachable only from the machine itself (or
through an SSH tunnel). Check it:

```bash
curl -sI http://127.0.0.1:8088/ | head -1
```

Expect `HTTP/1.1 200 OK` or a redirect. `status --json` gives the same information for monitoring.

### 8.3.5 Address, ports, user and group

To serve visitors directly, bind to all addresses on the standard ports:

```bash
php bin/php/console exp:velocity config set ServerSettings Host 0.0.0.0 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Port 80 --allow-root-user
php bin/php/console exp:velocity config set ServerSettings HTTPSPort 443 --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

| Setting (`[ServerSettings]`) | Shipped | Meaning |
|---|---|---|
| `Host` | `127.0.0.1` | bind address. The default keeps the server reachable only locally, through a tunnel or a reverse proxy |
| `Port` | `8088` | plain HTTP |
| `HTTPSPort` | `8080` | HTTPS, used when HTTPS is on (section 8.3.9) |
| `DocumentRoot` | empty | empty means the installation root, which is correct |
| `User`, `Group` | empty | who the workers run as when the server is started as root |
| `AllowRootWorkers` | `disabled` | `root` as worker user is refused unless `enabled` |
| `FollowSymlinks` | `disabled` | a file reached through a symbolic link leading out of the document root is refused (403 on `qbix`) |
| `HTTP2` | `enabled` | HTTP/2 for clients that negotiate it during the TLS handshake |
| `StaticMaxAge` | `31536000` | browser cache lifetime of stylesheets, scripts and images, in seconds |

**Ports below 1024 need root.** Started as root, the parent process keeps root (it binds the ports, reads the
certificate and performs reloads), and every worker gives root up right after it is forked and before any application
code runs. Who the workers become is decided in this order, first match wins:

1. `[ServerSettings] User` and `Group` in `velocity.ini` (passed as `--user` and `--group`);
2. `VC_RUN_USER` and `VC_RUN_GROUP` from the environment, or `export VC_RUN_USER=...` lines in `/etc/vc/envvars`
   (the engine also reads `QBIX_RUN_USER` and `QBIX_RUN_GROUP`);
3. the owner and group of the document root.

A user or group that does not exist stops the start. With nothing configured and a document root owned by root, the
workers stay root and the start says so: give the installation its own user (section 8.7) or set `User` and `Group`.
Set them to the user the files belong to:

```bash
php bin/php/console exp:velocity config set ServerSettings User example --allow-root-user
php bin/php/console exp:velocity config set ServerSettings Group example --allow-root-user
```

Started as an ordinary user, the server runs entirely as that user; use ports above 1024 then, and put a reverse proxy
or a firewall redirect in front (section 8.8).

### 8.3.6 Workers: pool size, the dynamic pool and the zygote

| Setting (`[ServerSettings]`) | Shipped | Meaning |
|---|---|---|
| `Workers` | `4` | the size of the pool; with `SpareWorkers` set, the most it grows to. Always set explicitly |
| `SpareWorkers` | `0` | `0`: fixed pool. Above 0: a **dynamic pool** that keeps this many idle workers, forks more when all are busy (up to `Workers`) and retires extra ones |
| `IdleWorkerTimeout` | `60` | seconds an extra worker may stay idle before it is retired |
| `Instances` | `1` | number of servers sharing the same ports with `SO_REUSEPORT` (Linux and the BSDs); `Workers` and `SpareWorkers` are per instance |
| `Zygote` | empty (engine default: on) | fork new workers from a process that never held a client connection |
| `StatTtl` | `0` | seconds a worker may keep file facts (exists, mtime, size) across requests; at most 10 |

**Sizing.** A worker of a full Exponential costs about 10 to 11 MB of its own memory (measured as PSS on 12 cores and
PHP 8.5); the shared part, about 47 MB, is paid once. Judge memory by PSS or private memory, never by RSS, which counts
every shared page once per worker and overstates a pool many times. When every worker is busy a request waits in the
parent; it is not refused until the connection limit (`maxConnections`, 1024) is reached.

A good start for a production site is a dynamic pool:

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
Workers=64
SpareWorkers=8
IdleWorkerTimeout=60
```

**Instances.** One server answers cached pages in its own process and is bounded by one core (measured: about 2,950
cached pages a second over TLS; four instances, about 12,000). Rendered pages are bounded by the workers, not by the
number of instances.

**The zygote.** `fork()` hands a new worker every descriptor the server holds, including open visitor connections. A
worker forked from the server under load therefore kept connections open until it retired. The zygote is a process
forked before the first connection is accepted; later workers are forked from it and inherit no connections. It needs
the `sockets` extension; if it fails, workers are forked from the server as before and the log says so.

Workers are also replaced on their own: after `maxRequests` requests (1000), when the heap passes
`workerMemoryCeiling`, or when a request runs past `requestTimeout` (30 seconds; the client gets 504). These are
engine settings under `Q.webserver`, described in the [worker pool specification](../specifications/6.0/velocity-worker-pool.md).

### 8.3.7 Persistent workers and KeepGlobals

`[ServerSettings] ForkPerRequest` decides how long a worker lives:

| Value | Behaviour |
|---|---|
| `disabled` (shipped) | **persistent workers**: each serves many requests; the request's state is cleared after the response has gone out |
| `enabled` | one request per worker, then a fresh fork: the isolation of PHP-FPM, at the cost of starting every request cold |

Persistent workers keep database connections, stat caches and everything the kernel keeps per process. Measured on a
signed-in administration page: 256 to 284 ms with a fresh worker per request, 73 to 80 ms with persistent workers.
They need an engine that sets `REQUEST_TIME_FLOAT` per request and carries headers and shutdown functions in this mode
(headers and shutdown functions from qbix-webserver 0.0.4.25, the per-request `REQUEST_TIME_FLOAT` from Exponential Velocity 0.0.4.42, the release Exponential suggests).

Between requests the worker restores static class properties, superglobals, custom globals, output buffers, handlers
and response headers to the state it was forked with. A few registries in the kernel are filled with `include_once`
and could never be refilled once cleared, so they must be **kept**: the datatype, workflow event, notification event
and payment gateway registries. Those names are built in (`expVelocity::DEFAULT_KEEP_GLOBALS`) and need no setting.

```ini
[ApplicationSettings]
# Adds to the built-in list. Only for a global filled once by include_once.
KeepGlobals[]
KeepGlobals[]=MyExtensionRegistry
# disabled starts from an empty list (for a kernel without those registries)
KeepGlobalsDefaults=enabled
```

`--keep-global=Name[,Name]` adds names for one `start`, `restart` or `command`; `exp:velocity command` prints the
complete command line, `exp:velocity layout` the resulting list. Keep nothing else: a request-scoped global kept
between requests leaks one visitor's request into the next.

`[ServerSettings] PreloadWarmup` (render a page in the parent before forking) ships **disabled** and should stay so;
the settings file explains the features it broke.

### 8.3.8 The response cache

The response cache keeps rendered anonymous pages and answers repeat requests inside the server process, before a
worker is chosen: no fork, no PHP, no database. Exponential configures it in `[CacheSettings]`:

| Setting | Shipped | Meaning |
|---|---|---|
| `Enabled` | `enabled` | the cache is on (it needs qbix-webserver 0.0.4.26 or later, which recognises the session cookie) |
| `DefaultTtl` | `30` | seconds an anonymous page is kept when it brings no lifetime of its own |
| `Dir` | `var/cache/qbix-reverse` | disk tier for entries too large for shared memory |
| `APCu`, `APCuMaxSize` | `enabled`, `0` | small entries in APCu, shared by the workers (`apc.enable_cli` is set for the server process) |
| `SkipCookies` | empty | cookies that mean "personal, do not cache"; empty derives the session cookie and `is_logged_in` |
| `StaleWhileRevalidate` | `60` | seconds an expired page is still served while one request renders its replacement |
| `NotFoundSeconds` | `60` | seconds a 404 is remembered |
| `SweepEvery`, `SweepMaxAge` | `300`, `86400` | clearing expired entries off the disk |
| `MinifyHtml` | `enabled` | stored pages without template indentation |

Signed-in visitors are never served from the cache, and a page carrying a form token is sent `private`. After a
template or stylesheet change:

```bash
php bin/php/console exp:velocity cache clear     # every cached page is rendered again on its next request
php bin/php/console exp:velocity cache stats     # where it is, how much it holds, when it was cleared
```

Check a hit with two requests: the second carries `X-Cache: HIT` and an `Age` header.

The kernel's own caches (content view cache, template blocks, the role-aware HTTP cache in `settings/httpcache.ini`,
the SQL query cache in `settings/querycache.ini`) work under every server; they are cleared with
`bin/php/ezcache.php` or `exp:cache`. See [the response cache page](../features/6.0/velocity-response-cache.md).

### 8.3.9 HTTPS served by Velocity itself

Velocity terminates TLS in its own process, with HTTP/2. Nothing else is needed: no `mod_ssl`, no stunnel, no reverse
proxy, and for a public certificate not even certbot.

#### With a certificate you have

Any PEM certificate from a CA or a hosting panel (leaf with chain, and the private key; one file may hold both):

```bash
php bin/php/console exp:velocity config set HTTPSSettings Certificate /etc/ssl/example.com/fullchain.pem --allow-root-user
php bin/php/console exp:velocity config set HTTPSSettings Key /etc/ssl/example.com/privkey.pem --allow-root-user
php bin/php/console exp:velocity config set HTTPSSettings Enabled true --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
php bin/php/console exp:velocity ssl show --allow-root-user
curl -sI https://example.com/ | head -1      # expect HTTP/2 200 or a redirect
```

What `velocity.ini` does with these settings (`expVelocity::httpsEnabled()` and `writeServerConfig()`):

- HTTPS is switched on only when `Enabled` is `true` **and** both files exist. A path that does not exist leaves the
  server on plain HTTP; `status` says HTTPS is off and what switches it on.
- The site file then carries `Q.web.https` with `mode: manual` and the two paths, and the server is started with
  `--https-port=<HTTPSPort>`.
- Before use the pair is verified: the certificate parses, has not expired and the key belongs to it. A pair that fails
  is never shown to a visitor; a **self-signed certificate** stands in until the real one is usable again (the
  engine's `fallback`, default `self-signed`; `none` would switch HTTPS off instead).
- The server reads a private copy of the pair (`active-<port>-<fingerprint>-<pid>.pem`), so a file being rewritten
  elsewhere cannot break a handshake. Every `watchInterval` seconds (60) the source files are checked, and a changed,
  valid pair is **swapped in live**, for the next connection, with no restart and no dropped connection. A renewal
  by your own tool is therefore picked up within a minute.
- HTTPS comes up before plain HTTP, so there is never a moment when the server answers HTTP but not HTTPS.

The server process reads the key as root when it was started as root, so the key can stay `0600 root`.

#### Strict-Transport-Security

```ini
[HTTPSSettings]
HSTSMaxAge=300              # seconds; 0 sends no header
HSTSIncludeSubDomains=disabled
HSTSPreload=disabled
```

The header goes out on every HTTPS answer (static files, the server's own pages and the scripts' answers) and never
over plain HTTP. A browser that has seen it refuses plain HTTP to that host for the whole period, even after you lower
the value. Raise it to a year (`31536000`) only when every name of the site is served over HTTPS for good; preload
needs `HSTSIncludeSubDomains` and at least a year.

#### Let's Encrypt and other ACME CAs, archives, PKCS#12

The engine's certificate subsystem can do more than `velocity.ini` exposes: obtain and renew certificates from
Let's Encrypt or any ACME CA (ZeroSSL, Google), read a certificate from a `.zip`, `.tar.gz`, `.p12` or `.pfx`, or make
a self-signed certificate without any file. These sources are configured under `Q.web.https` in the engine's
configuration. In the `/etc/vc` tree (section 8.3.10) everything in `conf-available/` belongs to the administrator and
is merged before the generated site file, so a snippet there is the place for it. Leave `[HTTPSSettings] Enabled`
at `false` in this case, so the generated files do not set `Q.web.https` themselves.

Requirements for the HTTP-01 challenge: DNS of every name points to this machine, and **port 80 of this server is
reachable from the internet** (`[ServerSettings] Port 80`), because the CA fetches
`/.well-known/acme-challenge/...` there.

```bash
cat > /etc/vc/conf-available/https-acme.conf <<'EOF'
{ "Q": { "web": { "https": {
  "port": 443,
  "mode": "letsencrypt",
  "acme": {
    "email": "hostmaster@example.com",
    "domains": ["example.com", "www.example.com"],
    "directory": "letsencrypt-staging"
  }
} } } }
EOF
php bin/php/console exp:velocity conf enable https-acme --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
php bin/php/console exp:velocity ssl show --allow-root-user
```

Annotations:

- The files of the tree are JSON objects under Apache's file names. `/etc/vc` is the tree when `exp:velocity` runs as
  root; `exp:velocity layout` names the directory in use otherwise.
- `directory: letsencrypt-staging` first: its certificates are not trusted by browsers, but its rate limits are
  generous. When `ssl show` lists a staging certificate, remove that line (the default is `letsencrypt`) and restart.
- Until the first certificate arrives, the self-signed one is served; the CA certificate replaces it live.
- Renewal runs in the background when a third of the lifetime is left (`renewAt` 0.33), with growing pauses after a
  failure (5 minutes doubling up to a day), so rate limits are never spent. `exp:velocity ssl renew [host...]` asks
  for it now.
- `keyType` (`ec256` by default), `challenge: dns-01` with a `dnsHook` program (needed for wildcards), `webroot` (when
  another server owns port 80), `eab` (external account binding) and the other keys are listed in
  [Velocity HTTPS and certificates](../features/6.0/velocity-https-certificates.md) and in the engine's
  `docs/https.md`.
- `start` reports that the configuration differs from `velocity.ini` because of the enabled snippet; that is expected.
- **Back up the `ssl/` directory** of the configuration directory the server runs with (the engine's `Q.webserver.confDir`, unless `selfSigned.dir` names another). Everything in it can be made
  again except the ACME account keys (`acme/account-<ca>.pem`).

For a certificate in an archive or bundle use `"mode": "archive"` with `"archive": "<file>"` or `"mode": "pkcs12"`
with `"bundle": "<file>"` and a `password`, `passwordFile` or `passwordEnv`. For development without any certificate:
`{ "Q": { "web": { "https": { "port": 8443, "mode": "self-signed" } } } }`.

#### Inspecting certificates

```bash
php bin/php/console exp:velocity ssl show --allow-root-user          # served certificate, every known one by expiry
php bin/php/console exp:velocity ssl show --json --allow-root-user   # for monitoring: watch daysLeft
php bin/php/console exp:velocity ssl renew example.com --allow-root-user
```

A CA certificate with fewer than 14 days left means renewal has failed for two weeks; `acme.lastError` says why. Keep
NTP running: certificates are checked against the system clock. The control panel's **SSL** tab shows the same and
can renew and reload from a browser (section 8.3.13).

| Symptom | Cause | Fix |
|---|---|---|
| `status` says HTTPS is off | `Enabled` is not `true`, or a named file does not exist | check the paths with `ls -l`; `config get HTTPSSettings Certificate` |
| browser warns about the certificate | the self-signed stand-in is served: the real pair is expired, mismatched or not yet issued | `ssl show`; the start-up log names the reason |
| ACME `Invalid response from http://.../.well-known/acme-challenge/...` | port 80 does not reach this server | open port 80, or set `acme.webroot` for the server that owns it |
| `none of the certificates belongs to the private key` | key and certificate from different requests | use the key the certificate was issued for |

### 8.3.10 The configuration tree: /etc/vc, Debian style

Velocity's configuration on disk mirrors Debian's `/etc/apache2`, so an administrator who knows Apache finds
everything in the expected place:

```
/etc/vc/                      Velocity's overlay on the engine's base tree /etc/qbix
├── vc.conf                   settings shared by every site        (apache2.conf)
├── ports.conf                listen ports                         (ports.conf)
├── envvars                   environment of the server process    (envvars)
├── conf-available/*.conf     shared snippets                      -> conf-enabled/  (symlinks)
├── mods-available/*.conf     engine modules: http2, cache, ...    -> mods-enabled/  (symlinks)
├── sites-available/*.conf    one file per installation            -> sites-enabled/ (symlinks)
├── designs/                  the server's own page designs
└── ssl/                      certificates (self-signed, imported, ACME), in the directory the server runs with

/var/lib/vc/sites/<site>.json  what is known about each installation
```

**Which directory.** `[LayoutSettings] ConfDir=auto` uses `/etc/vc` if it exists or can be created (that is, when
`exp:velocity` runs as root), else an existing `/etc/qbix`, else `var/vc/qbix/etc` inside the installation.
`disabled` uses a single generated file only; a path names a directory. The site's name in `sites-available` is
`[LayoutSettings] SiteName`, by default the host of `site.ini [SiteSettings] SiteURL`.

**The overlay.** `/etc/qbix` is the engine's own base tree (as the operating-system package installs it); `/etc/vc` is
stacked on top of it by the engine's `vc` distribution and loaded after it, so it wins.

**Load order**, later wins (a deep merge, like Apache's includes):

1. engine defaults, then the application's `config/server.json`
2. `vc.conf`
3. `ports.conf`
4. `mods-enabled/*` in name order
5. `conf-enabled/*` in name order
6. the site file, `sites-enabled/<site>.conf`
7. command-line flags (`--port`, `--workers`, ...)

**Who writes what.** `velocity.ini` stays the source. On every `start` and `restart` (and on `layout migrate`)
`exp:velocity` writes `ports.conf`, the module files it manages and `sites-available/<site>.conf` (mode `0600`: it holds
certificate paths and the dashboard token). Generated files carry `"_generated": "exp:velocity"`; remove that marker
from a file and it is never overwritten again, like a dpkg conffile. `vc.conf`, `envvars` and anything else you add are
created once and only ever read. Before the tree is used, the generated pieces are merged and compared with the single
configuration they replace; if that check fails, the server is started from the single file and the log says why.
Differences you made on purpose (a disabled module, an enabled snippet) are honoured and named in the start message.

**Enable and disable**, like `a2ensite`, `a2enconf` and `a2enmod`:

```bash
php bin/php/console exp:velocity layout --allow-root-user                 # every path, what is enabled, the files in use
php bin/php/console exp:velocity layout migrate --allow-root-user         # write the tree now
php bin/php/console exp:velocity site enable <name> --allow-root-user     # sites-enabled/<name>.conf -> ../sites-available/
php bin/php/console exp:velocity site disable <name> --allow-root-user    # removes only the symlink
php bin/php/console exp:velocity conf enable|disable <name> --allow-root-user
php bin/php/console exp:velocity mod enable|disable <name> --allow-root-user
php bin/php/console exp:velocity ctl configtest --allow-root-user         # the engine's own check of the tree
```

The tree belongs to the `qbix` engine. On `frankenphp` the configuration is a generated Caddyfile and on `php` a router
script; there `layout` lists those files and `site|conf|mod` refuse.

### 8.3.11 Running Velocity as a service

`exp:velocity` has no command that installs a boot service. Two routes:

**The engine's own units.** The engine package ships examples:

- `vendor/se7enxweb/exponential-velocity/service/qbixserver.service` (systemd) and `com.qbix.server.plist` (launchd),
  with a `README.md`, for a plain Qbix server started with `--root`, `--port` and `--pid`;
- `vendor/se7enxweb/exponential-velocity/packaging/systemd/exponential-velocity.service`, the unit of the operating-system
  package (`User=qbix`, `AmbientCapabilities=CAP_NET_BIND_SERVICE`, `EnvironmentFile=-/etc/default/exponential-velocity`,
  hardening with `NoNewPrivileges`, `ProtectSystem`, `ProtectHome`, `PrivateTmp`).

Both start the engine directly and therefore bypass what `exp:velocity` adds: the generated `/etc/vc` site file,
`KeepGlobals`, `[PHPSettings] IniOptions[]` (OPcache, APCu), the worker user, the engine archive and the instances.

**A unit that runs exp:velocity (derived).** The following unit is not shipped; it is derived from what
`expVelocity::start()` does (it starts the server detached and returns once the port answers, leaving the pid in
`var/vc/qbix/run/server.pid`) and from the commands above. It keeps every setting in `velocity.ini`:

```ini
# /etc/systemd/system/exponential-velocity-example.service
[Unit]
Description=Exponential Velocity for example.com
After=network-online.target mysqld.service
Wants=network-online.target

[Service]
Type=forking
WorkingDirectory=/var/www/example.com
ExecStart=/usr/bin/php bin/php/console exp:velocity start --allow-root-user
ExecStop=/usr/bin/php bin/php/console exp:velocity stop --allow-root-user
ExecReload=/usr/bin/php bin/php/console exp:velocity graceful --allow-root-user
PIDFile=/var/www/example.com/var/vc/qbix/run/server.pid
TimeoutStartSec=90
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
systemctl daemon-reload
systemctl enable --now exponential-velocity-example
systemctl status exponential-velocity-example
```

Notes: it runs as root so the server can bind ports 80 and 443 and read the key; the workers still drop to
`[ServerSettings] User` and `Group`. Adjust `After=` to your database service. With `Instances` above 1, `PIDFile`
names instance 0 only. Do not combine it with the operating-system package's unit for the same ports.

### 8.3.12 Logs

Everything Velocity writes is below `var/vc/`, one directory per engine:

```
var/vc/
├── qbix/
│   ├── etc/   the configuration tree when /etc/vc is not used
│   ├── lib/   site metadata when /var/lib/vc is not used
│   ├── log/   <siteaccess>-access.log, <siteaccess>-error.log, velocity-layout.log   ([LogSettings] Dir)
│   └── run/   server.pid, console.log                                                 (PidFile, LogFile)
├── frankenphp/  bin/ caddy/ tls/ log/ run/
└── php/         log/server.log, run/server.pid
```

| Setting (`[LogSettings]`) | Shipped | Meaning |
|---|---|---|
| `Enabled` | `enabled` | without it the server records nothing at all |
| `Dir` | `var/vc/qbix/log` | the `qbix` engine's log directory |
| `AccessName`, `ErrorName` | empty | empty names the files after the default siteaccess (`site-access.log`) |
| `Format` | `combined` | Apache's combined format; `qbix` adds the response time; `common`; or a format string |
| `FileMode`, `DirMode` | `0640`, `0750` | an access log names visitors; it is not for every account to read |

Rotation puts the date before the extension (`site-access.2026-09-24.log`). `console.log` holds what the process
prints, including the start-up lines (`Workers as: ...`, `tls: certificate ready ...`). The kernel's own logs stay in
`var/<site>/log/` as under any server.

### 8.3.13 The dashboard and the control panel

The server answers a few pages of its own, below `/Q/`, independent of the application:

| View | What | Who may open it (as `exp:velocity` configures it) |
|---|---|---|
| `/Q/dashboard` | live requests, workers, memory, status codes | this machine; elsewhere with `[DashboardSettings] Token` |
| `/Q/stats`, `/Q/metrics`, full `/Q/health` | figures, Prometheus metrics, health report | this machine; elsewhere with the Token or the panel login |
| `/Q/phpinfo` | `phpinfo()` including the process environment | this machine only, and only with the Token or panel password once one exists |
| `/Q/panel` | control panel: domains, certificates, cache, logs, workers, settings | this machine, then the panel password |
| `/Q/health` (status only), `/Q/docs` | "ok", documentation | everyone |

`[DashboardSettings] Remote=enabled` opens the figures remotely without a token; use it only on a port a firewall
already restricts. To reach the panel of a remote server, use an SSH tunnel to the server's own port.

The first visit of `/Q/panel` sets the panel password (at least 16 characters with the engine's strength rules; bcrypt;
failed sign-ins lock the address out with growing pauses). On a machine reachable from the internet set it at once.
Its storage must not be writable by group or others, or the panel locks itself; the engine's `qbixctl panel:check`
shows why. Details: [Velocity control panel](../features/6.0/velocity-control-panel.md).

### 8.3.14 Deploying a change: exp:velocity deploy

Workers keep classes, INI values and compiled templates in memory, so a changed PHP class, operator or INI file reaches
them only after a restart, and caches that hold rendered output must be cleared **after** the new code runs, or the
old output is cached again. One command does it in the right order:

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do, nothing else
php bin/php/console exp:velocity deploy --allow-root-user             # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

| # | Step | By hand |
|---|---|---|
| 1 | extension autoloads | `php bin/php/ezpgenerateautoloads.php -e` |
| 2 | kernel autoloads, only with `--kernel` (restricted to `kernel/` and `lib/`) | `php bin/php/ezpgenerateautoloads.php -k` |
| 3 | INI caches | `php bin/php/ezcache.php --clear-tag=ini` |
| 4 | template, template-override, translation and design_base caches | `--clear-id=<id>`, one per call |
| 5 | engine archive, when Velocity runs from one (rebuilt only when something in it changed) | `php bin/php/console exp:phar build` |
| 6 | reload PHP-FPM | `systemctl reload <[DeploySettings] PhpFpmService>` |
| 7 | restart Velocity, when it runs | `exp:velocity restart` |
| 8 | content, exphttpcache, (`--packer`: ezjscore-packer,) template-block caches | `--clear-id=<id>` |
| 9 | Velocity's response cache | `exp:velocity cache clear` |

Each step prints `PASS`, `FAIL` or `SKIP` with its time; the first failure stops the run (exit status 1) and the steps
not run are listed as `NOT RUN`. A kernel file that does not parse fails step 5 before any service is touched.

Options: `--kernel`, `--no-autoload`, `--no-fpm`, `--no-velocity`, `--rebuild-phar`, `--packer` (clear the packed
scripts and styles together with the template blocks; needed only when a packer server function or its settings
changed), `--engine=<name>`, `--dry-run`, `--json`.

`[DeploySettings] PhpFpmService` names the PHP-FPM to reload, so an Apache or nginx beside Velocity runs the new code
too: `auto` (the default) finds the pool configuration named after the installation's hosting domain or naming its
directory, in `/opt/plesk/php/*/etc/php-fpm.d`, `/etc/php-fpm.d`, `/etc/php/*/fpm/pool.d` and
`/etc/opt/remi/php*/php-fpm.d`; a unit name (or several, comma-separated) names it; **`disabled` when Velocity serves the
site alone**. The step is a graceful `systemctl reload` and needs root; run as anyone else it is skipped with a note.

For a template or stylesheet change no restart is needed: clear the template caches as usual and run
`exp:velocity cache clear`. `exp:velocity graceful` re-executes the server while keeping the listening socket, so a
restart drops no request.

### 8.3.15 The engine archive: engine.phar

The kernel can run from one archive, `dist/engine.phar`, instead of the files in `kernel/`, `lib/` and `autoload/`. It
is worth having for distribution and for knowing that the engine runs exactly what was built; measured, it is not
faster once OPcache is warm.

```bash
php -d phar.readonly=0 bin/php/console exp:phar build   # build (only when something changed; --force always)
php bin/php/console exp:phar check                      # is the archive current?
php bin/php/console exp:phar info
php bin/php/console exp:velocity config set ServerSettings EnginePhar enabled
php bin/php/console exp:velocity restart
```

`[ServerSettings] EnginePhar` takes `disabled` (the files on disk, the default), `enabled` (`dist/engine.phar`) or a
path. A path that does not exist stops the start instead of silently falling back. With an archive in use, `start`,
`restart` and `graceful` rebuild it first when a file in `kernel/`, `lib/` or `autoload/` was added, removed or
changed (`engine.phar is current, not rebuilt` otherwise); `--rebuild-phar` forces it. See
[Engine archive (phar)](../bc/6.0/phar.md).

### 8.3.16 Command reference

| Command | Does |
|---|---|
| `exp:velocity status` (default verb) | overview of every engine, then details; `--json`, `--all`, `--engine=` |
| `exp:velocity start` / `stop` / `restart` | as named; `start` waits until the port answers |
| `exp:velocity graceful` | re-executes the server, keeping the listening socket; no dropped requests |
| `exp:velocity kill` | stop without asking, for a wedged worker |
| `exp:velocity deploy` | everything a PHP change needs, in order (section 8.3.14) |
| `exp:velocity command` | print the command line it would run, and exit |
| `exp:velocity config list\|get\|set\|unset\|paths` | read and write `velocity.ini` values |
| `exp:velocity cache clear\|stats` | the response cache |
| `exp:velocity layout [migrate]` | the configuration tree and every file the server uses |
| `exp:velocity site\|conf\|mod enable\|disable <name>` | the a2ensite family |
| `exp:velocity ssl show\|renew [host...]` | certificates (`qbix` only) |
| `exp:velocity ctl <args>` | the engine's `qbixctl` with this installation's tree, site and pid file (`ctl status`, `ctl configtest`, ...) |
| `exp:velocity ext check\|list\|plan\|install-hint\|build` | PHP extensions the engine expects |
| `exp:velocity install` | FrankenPHP: download and verify the binary (`--force`, `--from=<file>`, `--check`, `--trust-github-digest`); `qbix`: nothing to do |

Options are accepted in GNU and BSD spellings (`--keep-global V`, `-keep-global=V`, `-json`); everything after `--` is
passed to the engine untouched. `exp:velocity --help` prints the full list.

---

## 8.4 FrankenPHP

FrankenPHP is the Caddy web server with PHP built in, as one binary. Velocity downloads it, verifies it and drives it
with the same commands. It runs in classic mode: a pool of PHP threads, each request starting from a clean state as
under PHP-FPM, so `ForkPerRequest`, `KeepGlobals` and the response cache do not apply.

```bash
php bin/php/console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user
php bin/php/console exp:velocity install --allow-root-user    # optional: start installs it when missing
php bin/php/console exp:velocity start --allow-root-user
```

- **The binary.** `install` fetches the release asset for this machine for `[FrankenPHPSettings] Version` (shipped
  `1.12.7`) and refuses it unless its SHA-256 matches the `Sha256[<asset>]` line pinned beside it. It is stored in
  `var/vc/frankenphp/bin/`. `Variant` chooses the Linux build (`gnu`, the default, loads `.so` extensions; `musl`;
  `mimalloc`). `BinaryPath` names a build of your own. `install --from=<file>` installs a copied file on a machine
  without access to GitHub, checked the same way.
- **PHP settings.** The binary reads no `php.ini` of the machine, so `[FrankenPHPSettings] IniOptions[]` ships
  `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1` and the upload limits; `PHPIniFile`
  names a `php.ini` of your own instead.
- **Threads.** `[FrankenPHPSettings] Workers` is the number of PHP threads; empty means FrankenPHP's default, twice the CPU cores; more threads
  than that measured slower under heavy load. `SpareWorkers` adds threads under load.
- **HTTPS.** On by default (`HTTPS=enabled`) on `HTTPSPort` (8444) beside HTTP on `Port` (8089). It uses
  `[HTTPSSettings] Certificate` and `Key` when both are set; with both empty it makes a self-signed certificate for
  the machine in `var/vc/frankenphp/tls/` and renews it a month before it runs out. A public site names its
  certificate. `start --no-https` and `start --https` decide for one start; `HTTPS=disabled` switches it off.
- **Configuration.** `var/vc/frankenphp/run/Caddyfile` is generated on every `start`, `graceful` and `restart` and
  validated with `frankenphp validate` before it replaces the old one. Its routing is `.htaccess_root`'s; it does not
  use `php_server`, which would serve every existing file. Directives of your own go in a file named by
  `SiteInclude`. `exp:velocity ctl caddyfile` prints the configuration.
- **Control.** `stop` and `graceful` use Caddy's admin API on the owner-only socket
  `var/vc/frankenphp/run/admin.sock`; `graceful` reloads the configuration without dropping a connection.
- **Logs.** `var/vc/frankenphp/log/access.log` and `error.log`, in Caddy's JSON format.

Details: [FrankenPHP](../bc/6.0/frankenphp.md) and [Velocity engines](../bc/6.0/velocity-engines.md).

---

## 8.5 Apache with PHP-FPM

Apache with `mod_rewrite`, `mod_proxy_fcgi` and a PHP-FPM pool is the traditional setup and needs no long-running
process of its own.

### 8.5.1 Step by step

1. **Enable the rewrite rules.** They ship as `.htaccess_root`; copy, do not move, so an update can bring a new
   version:

   ```bash
   cp .htaccess_root .htaccess
   ```

   Use `.htaccess_root_static` instead only when you serve a generated static cache (it is `.htaccess_root` with the
   static cache offload rules added).

2. **Load the modules.** `mod_rewrite`, `mod_proxy` and `mod_proxy_fcgi` (Debian: `a2enmod rewrite proxy_fcgi`);
   for HTTPS `mod_ssl`, for HTTP/2 `mod_http2`.

3. **Create the virtual host.** Adjust the paths and the PHP-FPM socket to your system:

   ```apache
   <VirtualHost *:80>
       ServerName example.com
       ServerAlias www.example.com
       # The installation root, where index.php is
       DocumentRoot /var/www/example.com
       DirectoryIndex index.php

       <Directory /var/www/example.com>
           Options FollowSymLinks
           # Lets Apache read the .htaccess created in step 1
           AllowOverride All
           Require all granted
       </Directory>

       # Every PHP script goes to the PHP-FPM pool of this site
       <FilesMatch \.php$>
           SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
       </FilesMatch>
   </VirtualHost>
   ```

   If you prefer not to use `.htaccess` (`AllowOverride None` saves a file lookup per directory), copy the rules of
   `.htaccess_root` into the `<Directory>` block; inside `<Directory>` they keep their form. In server context
   (outside `<Directory>`) every pattern needs a leading slash, as in `doc/examples/ezpublish.conf`, an older example
   whose rule list predates the current `.htaccess_root`: take the rules from `.htaccess_root`.

4. **HTTPS.** Add a `*:443` virtual host with the same content plus:

   ```apache
       SSLEngine on
       SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
       SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem
       Protocols h2 http/1.1
   ```

   and obtain the certificate with certbot or your hosting panel. Redirect port 80 to HTTPS in the `*:80` host
   (`Redirect permanent / https://example.com/`) once HTTPS works.

5. **Check and reload:**

   ```bash
   apachectl configtest           # Syntax OK
   systemctl reload apache2       # httpd on Red Hat style systems
   curl -sI https://example.com/ | head -1
   ```

   A 404 for every page means `.htaccess` is not read (`AllowOverride`) or `mod_rewrite` is off; a 500 means a look
   at the Apache and PHP-FPM error logs.

6. **Security headers** are sent by the kernel on its pages (`site.ini [HTTPHeaderSettings] SecurityHeaders[]`). For
   static files, add the same with `mod_headers` if you want them there as well; see
   [chapter 13](13-security-hardening.md).

### 8.5.2 PHP-FPM

The pool runs PHP as the site's user. Settings of interest for Exponential (in the pool file or `php.ini`):
`memory_limit` (at least 256M; more for large imports), `max_execution_time`, `upload_max_filesize` and
`post_max_size` (your largest upload), OPcache on. After a PHP class, operator or INI change, **reload the FPM master
that serves the site**:

```bash
systemctl reload php-fpm                       # the distribution's single PHP
php bin/php/console exp:velocity deploy --no-velocity --allow-root-user   # finds the right one and clears caches in order
```

On machines with several PHP versions the master serving the site is often not the one called `php-fpm`. Find it by
its pool file: `[DeploySettings] PhpFpmService=auto` does this (`deploy --dry-run` shows what it found and why).

### 8.5.3 Plesk

Plesk generates the virtual host itself; do not edit its files. In Plesk:

- point the domain's document root at the installation root (the directory with `index.php`);
- choose "FPM application served by Apache" in the PHP settings, and the PHP version;
- keep `.htaccess` (copied from `.htaccess_root`); Plesk's Apache reads it;
- install the certificate in Plesk (its Let's Encrypt extension or your own) and switch on the redirect to HTTPS.

Plesk names each pool after the domain in `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`, served by the systemd
unit `plesk-php<XY>-fpm` (for example `plesk-php85-fpm`). `systemctl restart php-fpm` restarts a different master that
no Plesk site uses. Reload the right one (`systemctl reload plesk-php85-fpm`), or let `exp:velocity deploy` find it:
`auto` recognises Plesk's naming by the hosting domain in the installation's path (`/var/www/vhosts/<domain>/...`).

---

## 8.6 nginx with PHP-FPM

**This configuration is derived, not shipped.** Exponential ships no nginx configuration in its repository. The server
block below is a translation of `.htaccess_root` (the version in this release) into nginx directives, rule for rule,
following nginx's own documentation for `location`, `try_files`, `return` and `fastcgi_pass`. Test it on your site
before you rely on it, and compare it with `.htaccess_root` after every upgrade.

```nginx
server {
    listen 80;
    server_name example.com www.example.com;

    # The installation root, where index.php is
    root /var/www/example.com;
    index index.php;

    # Uploads: nginx's default limit is 1 MB
    client_max_body_size 64m;

    # 1. No dot file or dot directory, anywhere; .well-known stays reachable
    location ~ (^|/)\.(?!well-known(/|$)) {
        return 404;
    }

    # 2. No script below the root runs because its path was asked for
    #    (.htaccess_root answers 404 only when the file exists; this answers 404 always)
    location ~ ^/.+/[^/]+\.(php[0-9]?|phtml|phar)$ {
        return 404;
    }

    # 3. Front controllers
    location ^~ /api/ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location = /index_rest.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location ~ ^/([^/]+/)?content/treemenu {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_treemenu.php;
        fastcgi_param SCRIPT_NAME /index_treemenu.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }

    # 4. Files served as they are (the "- [L]" rules of .htaccess_root);
    #    a listed path that does not exist is a 404, as under Apache
    location ~ ^/var/([^/]+/)?storage/images(-versioned)?/                                    { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$             { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?cache/(texttoimage|public)/                                       { try_files $uri =404; }
    location ~ ^/design/[^/]+/(stylesheets|images|javascript|fonts)/                            { try_files $uri =404; }
    location ~ ^/share/icons/                                                                   { try_files $uri =404; }
    location ~ ^/extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/ { try_files $uri =404; }
    location ~ ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/                      { try_files $uri =404; }
    location ~ ^/packages/styles/.+/thumbnail/                                                  { try_files $uri =404; }
    location ~ ^/var/storage/packages/.+\.(png|jpe?g|gif|webp)$                                 { try_files $uri =404; }
    location = /favicon.ico  { try_files $uri =404; }
    location = /robots.txt   { try_files $uri =404; }
    location = /index.js     { try_files $uri =404; }
    location = /sw.js        { try_files $uri =404; }
    location = /w3c/p3p.xml  { try_files $uri =404; }

    # 5. Everything else goes to index.php, with the original REQUEST_URI
    location / {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_param SCRIPT_NAME /index.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
}
```

How it maps and why it is ordered this way:

- nginx chooses a location by its own precedence, not by file order: exact matches (`=`) first, then the longest
  `^~` prefix, then **regular expressions in the order they appear**, then the longest plain prefix. The dot-file and
  script rules are therefore the first regular expressions, as they are the first rules of `.htaccess_root`; `/` is
  the plain prefix that catches the rest, like the final `RewriteRule .* index.php`.
- `.htaccess_root`'s `/api/` and tree menu rules rewrite to a front controller. Here the front controller is named in
  `SCRIPT_FILENAME` and the request is not rewritten, so `REQUEST_URI` (set by `fastcgi_params` from
  `$request_uri`) still carries the address the visitor asked for, exactly as with Apache.
- A root-level `.php` other than the front controllers (for example `/config.php`) reaches `location /` and is answered
  by `index.php`, never executed: as with `.htaccess_root`.
- The static locations have no `fastcgi_pass`, so no PHP runs there; the script rule before them makes sure a `.php`
  below an asset directory is not sent as text either.
- `fastcgi_params` of the nginx packages passes `HTTPS` when the connection is TLS, which is what Exponential checks
  first.
- Not translated: the commented-out favicon and debug lines of `.htaccess_root`, and `/design/standard/images/favicon.ico`,
  which the design rule already covers.

For HTTPS add `listen 443 ssl;` (and `http2 on;` on nginx 1.25.1 or later), `ssl_certificate` and
`ssl_certificate_key`, and a separate `server` on port 80 that returns `301 https://$host$request_uri`. Check with
`nginx -t` and reload with `systemctl reload nginx`. Security headers for static files: `add_header ... always;`
(chapter 13).

---

## 8.7 File permissions and ownership

### 8.7.1 What must be writable

The user that runs PHP for the site (the PHP-FPM pool user, the Velocity worker user or the FrankenPHP process) must be
able to write these directories, the list the setup wizard checks (`settings/setup.ini [directory_permissions]`):

| Directory | Why |
|---|---|
| `var/` and below: `var/cache`, `var/log`, `var/storage`, `var/autoload`, `var/<site>/...` | caches, compiled templates, logs, uploaded files and image variations, autoload arrays |
| `settings/`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/<admin>` | the setup wizard and the administration write settings |
| `design/`, `extension/` | the setup wizard and package installation write there |

After installation you can take write access to `design/` and `extension/` away again if nothing installs packages
from the administration; `settings/` stays writable when administrators change settings in the browser (and the
setup's `settings_permission` check expects it).

Everything else (kernel, lib, vendor, `index.php`) only needs to be readable. The system check prints commands for
your machine of this form:

```bash
sudo chown -R example:example design extension settings var
sudo chmod -R ug+rwx design extension settings var
```

### 8.7.2 One user, or one group

Problems with permissions almost always come from **two users writing the same files**: the web server's PHP, the
Velocity workers, cronjobs and command-line scripts. A cache file written by `root` from the command line cannot be
replaced by the site user afterwards, and the site breaks in odd places.

```
                 owner: example   group: example
   PHP-FPM pool  ----------------+
   Velocity workers ([ServerSettings] User/Group)
   cronjobs, console scripts ----+---- all write var/ as the same user
```

Recommended, in order:

1. **Run everything as the site's user.** PHP-FPM pool `user = example`; Velocity `[ServerSettings] User=example`,
   `Group=example` (or leave them empty and let the document root's owner decide); cronjobs from that user's crontab
   (`crontab -u example -e`) or with `sudo -u example php runcronjobs.php`. Velocity started as root is fine: only its
   parent process stays root.
2. **If the users must differ** (for example Apache's `apache` and a deploy user), give them a common group, make the
   writable directories group-writable and setgid so new files inherit the group, and use permissions that keep the
   group's write access:

   ```bash
   sudo chgrp -R example design extension settings var
   sudo find var settings -type d -exec chmod 2775 {} +
   ```

   ```ini
   # settings/override/site.ini.append.php
   [FileSettings]
   StorageDirPermissions=0770
   StorageFilePermissions=0660
   TemporaryPermissions=0770
   LogFilePermissions=0660
   ```

   The shipped values are `0777` and `0666` (world-writable); `site.ini` itself recommends `0770` and `0660` with the
   right user and group, which is the hardened choice (chapter 13).

When Apache (PHP-FPM) and Velocity serve the same installation side by side, they share `var/`, the caches and the
sessions: give them the same user, or the setup of point 2. Test with both running and look at the owner of new files
in `var/` after a few requests.

### 8.7.3 Velocity's own files

| Path | Written by | Mode |
|---|---|---|
| `/etc/vc/sites-available/<site>.conf` | `exp:velocity` (root) | `0600` |
| `ssl/` of the configuration directory: private keys | the server | `0600`, written atomically, never logged |
| `var/vc/qbix/log/` | the server | `[LogSettings] FileMode` `0640`, `DirMode` `0750` |
| `var/cache/qbix-reverse/` | the server for the workers | `[CacheSettings] FileMode` `0640`, `DirMode` `0750` |

The cache and precompression directories are handed to the worker user at start. The worker user must be able to read
the engine's source tree in `vendor/`.

---

## 8.8 Behind a reverse proxy

Velocity does not need a proxy in front: it serves HTTPS itself. A proxy is still the right shape when a load balancer
or CDN already terminates TLS, when one public address carries several services, or when the server must run as an
ordinary user on a high port. Velocity's shipped `Host=127.0.0.1` is made for that: the server is reachable only
from the machine, and only through the proxy.

**What Exponential reads from a proxy** (`lib/ezutils/classes/ezsys.php`):

- **HTTPS.** `eZSys::isSSLNow()` checks `$_SERVER['HTTPS']` first, then the port against `site.ini [SiteSettings]
  SSLPort` (443), then `X-Forwarded-Proto: https`, then `X-Forwarded-Port`, then `X-Forwarded-Server` against
  `SSLProxyServerName`. A proxy that terminates TLS must send `X-Forwarded-Proto`; then generated URLs and the
  `Secure` flag of the session cookie (`[Session] CookieSecure=auto`) are right.
- **The visitor's address.** `[HTTPHeaderSettings] ClientIpByCustomHTTPHeader=X-Forwarded-For` makes
  `eZSys::clientIP()` take the **first** address of that header. Set it only when the proxy overwrites the header for
  every request, because a client can send its own (see chapter 13).

**nginx in front of Velocity (derived example):**

```nginx
location / {
    proxy_pass http://127.0.0.1:8088;
    proxy_set_header Host              $host;
    proxy_set_header X-Forwarded-Proto $scheme;
    # overwrite, never append, what the client sent
    proxy_set_header X-Forwarded-For   $remote_addr;
    proxy_http_version 1.1;
}
```

**Apache in front of Velocity (derived example):**

```apache
ProxyPreserveHost On
ProxyPass        / http://127.0.0.1:8088/
ProxyPassReverse / http://127.0.0.1:8088/
RequestHeader set X-Forwarded-Proto "https"
```

(`mod_proxy`, `mod_proxy_http` and `mod_headers`; Apache's `mod_proxy` adds `X-Forwarded-For` itself, appending to a
value the client sent.)

Notes:

- **Let's Encrypt behind a proxy:** the proxy owns port 80, so either let it obtain the certificate, or forward
  `/.well-known/acme-challenge/` to Velocity, or set the engine's `acme.webroot` to a directory the proxy serves.
- **Caching proxies** must not store signed-in pages. Exponential sends `private` on pages that carry a form token;
  a proxy that purges by tags reads the header named in `httpcache.ini [HttpCacheSettings] TagHeader` (`disabled` by
  default).
- **The Velocity response cache** still works behind a proxy; clear it with `exp:velocity cache clear` as usual.

---

## 8.9 Checklist

- [ ] `Engine=qbix` set, `exp:velocity status` shows the server running, the site answers.
- [ ] `Host`, `Port`, `HTTPSPort` as intended; ports 80 and 443 open in the firewall when Velocity faces visitors.
- [ ] Workers run as the site's user (`Workers as: ...` in `var/vc/qbix/run/console.log`), never root.
- [ ] HTTPS: a CA certificate is served (`exp:velocity ssl show`), not the self-signed stand-in; renewal planned.
- [ ] `HSTSMaxAge` raised only after every name is on HTTPS for good.
- [ ] A boot service starts Velocity after a reboot.
- [ ] `[DeploySettings] PhpFpmService=disabled` when Velocity serves the site alone; `exp:velocity deploy --dry-run`
      shows the steps you expect.
- [ ] Apache or nginx: `.htaccess` copied from `.htaccess_root` (or the derived nginx block), front controllers and
      asset paths tested, a `.php` below the root answers 404.
- [ ] `var/` and `settings/` writable by the one user (or group) that runs PHP; no root-owned files in `var/`.
- [ ] Behind a proxy: `X-Forwarded-Proto` sent, `X-Forwarded-For` overwritten, `ClientIpByCustomHTTPHeader` set only then.

---

## 8.10 References

In this repository:

- [Velocity engines](../bc/6.0/velocity-engines.md): engines, `deploy`, `Instances`, FrankenPHP, logs, the files each
  engine serves, the `/Q/` views
- [Velocity: running Exponential in a persistent-worker web server](../features/6.0/velocity-persistent-worker-server.md)
- [Velocity on-disk layout (Debian Apache style)](../bc/6.0/velocity-ondisk-layout.md)
- [Velocity HTTPS and certificates](../features/6.0/velocity-https-certificates.md)
- [Velocity response cache](../features/6.0/velocity-response-cache.md),
  [control panel](../features/6.0/velocity-control-panel.md), [web server](../features/6.0/velocity-web-server.md),
  [packages and binaries](../features/6.0/velocity-packages-and-binaries.md)
- Specifications: [worker pool](../specifications/6.0/velocity-worker-pool.md),
  [engine settings](../specifications/6.0/velocity-engine-settings.md),
  [HTTP/2 and security](../specifications/6.0/velocity-http2-and-security.md),
  [security defaults](../specifications/6.0/security-defaults-2026-09.md)
- [Velocity engine upgrade notes](../bc/6.0/velocity-engine-upgrade-notes.md), [FrankenPHP](../bc/6.0/frankenphp.md),
  [Engine archive (phar)](../bc/6.0/phar.md)
- [Deploying guide](../guides/deploying.md)
- The settings: [`settings/velocity.ini`](../../settings/velocity.ini); the rewrite rules:
  [`.htaccess_root`](../../.htaccess_root), [`.htaccess_root_static`](../../.htaccess_root_static); the PHP router of the
  development engine: [`bin/php/velocity-router.php`](../../bin/php/velocity-router.php)
- The previous single-page guide: [doc/INSTALL.md, section 8](../INSTALL.md#8-serve-the-site-web-server-https-and-permissions)

External:

- Exponential Velocity engine: <https://github.com/se7enxweb/exponential-velocity> (its `docs/https.md`,
  `docs/layout.md`, `docs/workers.md`, `service/` and `packaging/systemd/`)
- Apache HTTP Server: [mod_rewrite](https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html),
  [mod_proxy_fcgi](https://httpd.apache.org/docs/2.4/mod/mod_proxy_fcgi.html),
  [mod_ssl](https://httpd.apache.org/docs/2.4/mod/mod_ssl.html),
  [mod_proxy](https://httpd.apache.org/docs/2.4/mod/mod_proxy.html),
  [AllowOverride](https://httpd.apache.org/docs/2.4/mod/core.html#allowoverride)
- nginx: [location](https://nginx.org/en/docs/http/ngx_http_core_module.html#location),
  [try_files](https://nginx.org/en/docs/http/ngx_http_core_module.html#try_files),
  [fastcgi module](https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html),
  [proxy module](https://nginx.org/en/docs/http/ngx_http_proxy_module.html),
  [ssl module](https://nginx.org/en/docs/http/ngx_http_ssl_module.html),
  [client_max_body_size](https://nginx.org/en/docs/http/ngx_http_core_module.html#client_max_body_size)
- PHP: [FastCGI Process Manager (FPM)](https://www.php.net/manual/en/install.fpm.php),
  [FPM configuration](https://www.php.net/manual/en/install.fpm.configuration.php),
  [built-in web server](https://www.php.net/manual/en/features.commandline.webserver.php),
  [OPcache](https://www.php.net/manual/en/book.opcache.php)
- FrankenPHP: <https://frankenphp.dev/> and [its documentation](https://frankenphp.dev/docs/); Caddy:
  <https://caddyserver.com/docs/>
- Let's Encrypt: [How it works](https://letsencrypt.org/how-it-works/),
  [challenge types](https://letsencrypt.org/docs/challenge-types/),
  [staging environment](https://letsencrypt.org/docs/staging-environment/),
  [rate limits](https://letsencrypt.org/docs/rate-limits/); ACME: [RFC 8555](https://www.rfc-editor.org/rfc/rfc8555)
- systemd: [systemd.service](https://www.freedesktop.org/software/systemd/man/latest/systemd.service.html)
- Debian's Apache layout: [README.Debian of apache2](https://salsa.debian.org/apache-team/apache2/-/blob/master/debian/apache2.README.Debian)
- HSTS: [RFC 6797](https://www.rfc-editor.org/rfc/rfc6797), [hstspreload.org](https://hstspreload.org/)

[Previous: 7. Install with one console command](07-console-install.md) ·
[Next: 9. Databases](09-databases.md) ·
[Contents](README.md)
