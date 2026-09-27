# FrankenPHP as the Exponential web engine

FrankenPHP is Exponential Velocity's **production** engine: the Caddy web
server with PHP and its extensions compiled into a single self-contained
binary. This document is the complete operator + developer reference for
installing, running, debugging, optimising and extending FrankenPHP with
Exponential 6.x, driven by the `exp:velocity` multi-engine
tooling. It is written to be self-sufficient — no paid support needed.

It is a companion to [`velocity-engines.md`](velocity-engines.md), which covers
all three engines (`qbix`, `frankenphp`, `php`) and the settings they share.
Read that for the cross-engine picture; this document goes deep on FrankenPHP
alone. Where the two overlap, the shared doc is the source of truth and this
one links to it rather than repeating it.

Everything here was verified against the **working install on alpha** on
2026-09-26: FrankenPHP v1.12.7 (PHP 8.5.11, Caddy v2.11.4) serving
`https://alpha.se7enx.com:8070/site/` (HTTP 200, Let's Encrypt certificate)
side by side with the qbix engine on 8080.

> Command examples use `./console`, the symlink to `bin/php/console` at the
> installation root. `./bin/php/console`, `php bin/php/velocity.php` and the
> `exp:vc` shorthand are equivalent. All examples add `--allow-root-user`
> because this box runs the tooling as root; drop it if you run as the site
> user.

---

## 1. What FrankenPHP is here, and when to choose it

FrankenPHP is one file per platform (150–180 MB) that bundles the Caddy web
server, the PHP interpreter and PHP's extensions. `exp:velocity install`
downloads it from the FrankenPHP project's GitHub releases for a pinned version
and verifies its SHA-256 before it is ever run.

On this engine PHP runs in **classic mode**: a pool of PHP threads inside one
process, each request starting from a clean state, exactly as under php-fpm.
Worker mode (persistent PHP scripts across requests) is **deliberately not
offered**, because the Exponential kernel keeps request state in globals and
would leak one request into the next. This is a real limitation to be aware of:
you get php-fpm-style isolation and its per-request bootstrap cost, not a
long-lived worker's warm state.

The server is configured by a **Caddyfile generated from `velocity.ini` on
every `start`, `graceful` and `restart`**, and controlled through Caddy's admin
API over a unix socket.

### Choosing an engine

See the full comparison table in
[`velocity-engines.md`](velocity-engines.md). In short:

| Engine | Role | Pick it when |
|---|---|---|
| `qbix` | **recommended**: every stage, development to production | you want the fastest engine: persistent workers, the response cache and the role-aware HTTP cache answered in the server, HTTP/2, the `/Q/` panel and the `/etc/vc` config tree |
| `frankenphp` | production-ready alternative | you want one verified binary, TLS + HTTP/2, php-fpm-style isolation, no machine PHP/php-fpm to maintain |
| `php` | development (shipped default) | you want zero install; plain HTTP only, no reload |

FrankenPHP has **no response cache yet** (`[CacheSettings]` is ignored — the
engine says so under `status`). If you rely on Velocity's in-server response
cache, that is qbix territory today. FrankenPHP does serve static assets
directly and speaks HTTP/2.

The engine class is `expVelocityFrankenPHP`
(`kernel/classes/expvelocityfrankenphp.php`); the installer is
`expVelocityFrankenPHPInstaller`
(`kernel/classes/expvelocityfrankenphpinstaller.php`).

---

## 2. Installation — step by step, exactly as tested on alpha

This is the sequence that produced the working
`https://alpha.se7enx.com:8070/site/` install. Copy-pasteable.

### 2.1 Select the engine (optional — you can also just pass `--engine=frankenphp`)

`Engine` in `[ServerSettings]` is the *default* engine used when no `--engine`
is given. On alpha the default stays `qbix` (it serves the live site); FrankenPHP
runs beside it and is reached explicitly with `--engine=frankenphp`. To make
FrankenPHP the default instead:

```bash
./console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user
```

`config set` writes to `settings/override/velocity.ini.append.php`. You can also
edit that file by hand (see §3).

### 2.2 Configure the engine (the alpha worked example)

All of the following goes in `settings/override/velocity.ini.append.php` under
`[FrankenPHPSettings]` (plus `[HTTPSSettings]` and `[PHPSettings]`). This is the
exact tested block, annotated:

```ini
[FrankenPHPSettings]
# Own address/ports so frankenphp never clashes with the qbix engine on 8080.
Port=8089                 # plain HTTP (packaged default, kept)
HTTPSPort=8070            # public HTTPS
HTTPS=enabled
Host=66.94.126.4          # bind the public interface (same one qbix binds)
DocumentRoot=             # empty = installation root; /site/ resolves the 'site' siteaccess
# Corrected checksum for the v1.12.7 gnu asset because FrankenPHP re-cut the
# release and the packaged pin went stale (see §2.4). This value is GitHub's
# own published release digest, verified against the downloaded asset.
Sha256[frankenphp-linux-x86_64-gnu]=1897afb7b80d0f108af27815a449f6b6e57fa3d7847eb8fe842f0928c443e3e2

[HTTPSSettings]
# The real Let's Encrypt certificate Plesk holds for this host — the same file
# the qbix engine serves. Browser-trusted, read-only. Exported outside the
# document root; re-export after each renewal (see §4).
Enabled=true
Certificate=/usr/local/psa/var/certificates/scfqvfhatugf33seS8EFBS
Key=/usr/local/psa/var/certificates/scfqvfhatugf33seS8EFBS

[PHPSettings]
# FrankenPHP's embedded PHP has no machine php.ini, so no default MySQL socket.
# Point mysqli/pdo_mysql at the real socket or DB connections fail (see §6).
IniOptions[]=mysqli.default_socket=/var/lib/mysql/mysql.sock
IniOptions[]=pdo_mysql.default_socket=/var/lib/mysql/mysql.sock
```

> Note: `Host`, `DocumentRoot`, and the two `IniOptions[]` are the specific
> settings that made the install work on this box. `Sha256[…]` is only needed
> because of the re-cut release (§2.4); a fresh version with an intact pin needs
> nothing here.

### 2.3 Install the binary

```bash
./console exp:velocity install --engine=frankenphp --allow-root-user
```

This downloads the release asset for this machine
(`frankenphp-linux-x86_64-gnu` for `Variant=gnu` on x86_64/glibc) from
`https://github.com/php/frankenphp/releases/download/v1.12.7/…`, verifies its
SHA-256 against the pin, runs `frankenphp version` to confirm it executes, and
lands it at:

```
var/vc/frankenphp/bin/frankenphp-1.12.7-linux-x86_64-gnu   (git-ignored)
var/vc/frankenphp/bin/frankenphp-1.12.7-linux-x86_64-gnu.sha256
```

Install options (from the `install` verb):

| Option | Effect |
|---|---|
| *(none)* | download the pinned version if not already installed |
| `--force` | download again even if present |
| `--from=<file>` | install a binary you copied here by hand (machine with no GitHub access) — **still verified against the pinned Sha256** |
| `--check` | re-hash the installed binary and report whether it matches |
| `--trust-github-digest` | for a `Version` with **no** `Sha256[]` pin: accept the digest GitHub publishes for the release (weaker — same source as the file) |

`start` also installs on its own when the binary is missing and
`AutoInstall=enabled` (the default). Set `AutoInstall=disabled` to require an
explicit `install`.

### 2.4 If the pin is stale — the re-cut-release procedure (the gotcha we hit)

FrankenPHP **re-published v1.12.7** after our pin was set. The re-cut archive
has a different SHA-256, so `install` refused it:

```
SHA-256 mismatch for frankenphp-linux-x86_64-gnu: expected <old>, got <new> -- nothing installed
```

The **wrong** fix is to disable the check. The **correct, safe** fix is to
verify the new download against GitHub's *authoritative* release digest and, if
it matches, update the pin. GitHub computes the digest server-side from the
uploaded asset, so it is a stronger authority than a locally recomputed hash.

```bash
# 1. Ask GitHub for the authoritative digest of the asset for this version:
gh api repos/php/frankenphp/releases/tags/v1.12.7 \
  --jq '.assets[]|select(.name=="frankenphp-linux-x86_64-gnu")|.digest'
# -> sha256:1897afb7b80d0f108af27815a449f6b6e57fa3d7847eb8fe842f0928c443e3e2
```

If that value matches what the download actually is, set it as the pin in
`settings/override/velocity.ini.append.php` under `[FrankenPHPSettings]` (strip
the `sha256:` prefix):

```ini
Sha256[frankenphp-linux-x86_64-gnu]=1897afb7b80d0f108af27815a449f6b6e57fa3d7847eb8fe842f0928c443e3e2
```

Then re-run install:

```bash
./console exp:velocity install --engine=frankenphp --force --allow-root-user
```

**Why this is the standard procedure when a pin goes stale:** never move a pin
to whatever you happened to download — that defeats the check. Move it only to
the digest GitHub itself publishes for the release, which is the same source of
authority as the asset. For a version that ships with *no* `Sha256[]` pin at
all, `install --trust-github-digest` does the equivalent automatically (fetches
the digest from `ApiUrl` and uses it) — convenient, but weaker than a
human-reviewed pin, because it trusts the release page rather than a value you
committed.

The engine's official `install` verb was **not** hard-coded to trust GitHub;
you set the pin explicitly, which is why the change is a one-line, reviewable
edit in the override file.

### 2.5 Open the firewall for the HTTPS port

FrankenPHP binds `Host` and listens on `Port` / `HTTPSPort`; the OS firewall
must allow the HTTPS port. On alpha the firewall is **firewalld**:

```bash
firewall-cmd --permanent --zone=public --add-port=8070/tcp
firewall-cmd --reload
firewall-cmd --list-ports          # confirm 8070/tcp (and 8080/tcp for qbix) are present
```

Which firewall is in force varies by box:

- **firewalld** (this box): `firewall-cmd --state` says `running`; manage with
  `firewall-cmd`. Verified: `8070/tcp` and `8080/tcp` are open here.
- **Plesk-managed**: some Plesk installs drive firewalld or nftables through the
  Plesk Firewall extension. If a rule you add with `firewall-cmd` disappears on
  the next Plesk apply, add it in Plesk (Tools & Settings → Firewall) instead.
- **iptables/nftables directly**: check with `iptables -L -n` /
  `nft list ruleset`; add a rule for the port there.

Rule of thumb: add the port with whatever tool *owns* the ruleset on the box, or
the change will not stick.

### 2.6 Start and verify (tested)

```bash
./console exp:velocity start --engine=frankenphp --allow-root-user
```

Verify — these are the exact checks run on alpha, all passing:

```bash
# Site answers 200 (‑k because a self-signed cert would otherwise fail; here the
# cert is real, but ‑k keeps the check independent of the client trust store):
curl -k -o /dev/null -w '%{http_code}\n' https://66.94.126.4:8070/site/     # -> 200

# Certificate issuer (should be Let's Encrypt for the shared Plesk cert):
echo | openssl s_client -servername alpha.se7enx.com -connect 66.94.126.4:8070 \
  2>/dev/null | openssl x509 -noout -issuer
# -> issuer=C=US, O=Let's Encrypt, CN=YR1

# qbix on 8080 is undisturbed:
curl -k -o /dev/null -w '%{http_code}\n' https://66.94.126.4:8080/          # -> 200
```

`/site/` is the path that resolves the Exponential `site` siteaccess through
`.htaccess_root` routing when `DocumentRoot` is the installation root. The bare
`/` resolves the default siteaccess; `status` prints the full URL list.

FrankenPHP runs entirely **beside** qbix: separate `Port`/`HTTPSPort`, separate
pid file, separate logs, separate admin socket. Nothing in the frankenphp start
touches the running qbix process — do not confuse the two.

---

## 3. Full configuration reference

FrankenPHP reads three blocks in `velocity.ini` (defaults) /
`settings/override/velocity.ini.append.php` (your overrides):
`[FrankenPHPSettings]`, `[PHPSettings]` and `[HTTPSSettings]`. It also reads a
handful of shared `[ServerSettings]`, `[LogSettings]` and `[ControlSettings]`
keys. Settings that belong only to another engine are **reported, not silently
dropped** — `status` and `start` list every one an installation set that this
engine ignores.

Precedence for `Port`, `HTTPSPort`, `Host`, `DocumentRoot`:
`[FrankenPHPSettings]` value if set, otherwise the `[ServerSettings]` value.
That is what lets the engine run beside the others. `Workers` and
`SpareWorkers` are the exception: empty, they do **not** fall back to
`[ServerSettings]` (the Qbix server's process pool) but default to twice the
CPU cores and none extra -- see §8.

### 3.1 `[FrankenPHPSettings]`

| Key | Meaning | Default (velocity.ini) | Example |
|---|---|---|---|
| `Port` | plain-HTTP listen port | `8089` | `8089` |
| `HTTPSPort` | HTTPS listen port (when TLS on) | `8444` | `8070` |
| `HTTPS` | `enabled`/`true` = serve TLS on `HTTPSPort` beside HTTP; `disabled` = off for good | `enabled` | `enabled` |
| `Host` | bind address; empty = `[ServerSettings] Host` | *(empty → 127.0.0.1)* | `66.94.126.4` |
| `Workers` | number of PHP threads (classic mode) = `num_threads`; empty = twice the CPU cores (never `[ServerSettings] Workers`) | *(empty → 2 × cores)* | `24` |
| `SpareWorkers` | extra threads started under load; `max_threads = Workers + SpareWorkers`; empty = 0 (never `[ServerSettings] SpareWorkers`) | *(empty → 0)* | `0` |
| `DocumentRoot` | web root; empty = installation root (where `index.php` lives) | *(empty)* | *(empty)* |
| `Version` | FrankenPHP release to download (no leading `v`) | `1.12.7` | `1.12.7` |
| `Variant` | Linux build: `gnu`, `musl`, `mimalloc` | `gnu` | `gnu` |
| `Sha256[<asset>]` | SHA-256 pin per release asset; a download that mismatches is deleted, never run | 7 pins shipped | `Sha256[frankenphp-linux-x86_64-gnu]=…` |
| `BinaryPath` | use an own binary instead of the download; nothing is fetched | *(empty)* | `/opt/frankenphp` |
| `BinarySha256` | optional pin checked for `BinaryPath` | *(empty)* | `<hex>` |
| `BinaryDir` | where `install` puts the download | `var/vc/frankenphp/bin` | |
| `AutoInstall` | install on first start when binary missing | `enabled` | |
| `DownloadUrl` | asset URL template; `{version}`, `{asset}` filled in | `https://github.com/php/frankenphp/releases/download/v{version}/{asset}` | |
| `ApiUrl` | release-metadata URL (read only by `--trust-github-digest`) | `https://api.github.com/repos/php/frankenphp/releases/tags/v{version}` | |
| `AdminAddress` | Caddy admin API address; `auto` = unix socket, else `unix/<path>` or `host:port` | `auto` | `auto` |
| `ConfigFile` | where the generated Caddyfile is written | `var/vc/frankenphp/run/Caddyfile` | |
| `PidFile` | pid file | `var/vc/frankenphp/run/server.pid` | |
| `LogFile` | server's console output | `var/vc/frankenphp/run/console.log` | |
| `LogDir` | access/error log directory; empty = `var/vc/frankenphp/log` | *(empty)* | |
| `AccessLog` | override access-log path | *(empty)* | |
| `ErrorLog` | override error-log path | *(empty)* | |
| `Compression` | codecs offered to clients that ask, in order of preference, from `zstd`, `br`, `gzip`; or `disabled` (see §8 Compression) | `zstd br gzip` | `br zstd gzip` |
| `SiteInclude` | Caddyfile fragment imported into every site block (your own directives) | *(empty)* | `settings/caddy/extra.caddy` |
| `PHPIniFile` | a php.ini for the embedded PHP (sets `PHPRC`) | *(empty)* | |
| `IniOptions[]` | interpreter settings, applied *after* `[PHPSettings] IniOptions[]` | `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`, `post_max_size=64M`, `upload_max_filesize=32M` | see §6 |
| `ExtraOptions[]` | extra args appended to `frankenphp run` verbatim | *(empty)* | |

Assets recognised for `Sha256[]` / `Variant` (from the installer):
`frankenphp-linux-x86_64` (musl), `-x86_64-gnu` (glibc), `-x86_64-mimalloc`,
`-aarch64`, `-aarch64-gnu`, `frankenphp-mac-arm64`, `frankenphp-mac-x86_64`.
Windows is not downloaded — use `BinaryPath`.

### 3.2 `[PHPSettings]`

Shared with the qbix engine. `IniOptions[]` are applied **first**, then
`[FrankenPHPSettings] IniOptions[]` (a later key wins). Shipped defaults enable
OPcache for CLI/embedded PHP and size interned strings; see §6 and §8.

### 3.3 `[HTTPSSettings]`

| Key | Meaning | Default |
|---|---|---|
| `Enabled` | `true` + a certificate switches TLS on (in addition to `[FrankenPHPSettings] HTTPS`) | `false` |
| `Certificate` | PEM certificate path (relative resolves against root) | *(empty)* |
| `Key` | PEM private-key path | *(empty)* |

Both `Certificate` and `Key` must name files that exist, or the start is
refused. Leave both empty for a self-signed certificate (§4).

### 3.4 Shared keys this engine honours

- `[ServerSettings]`: `Port`, `HTTPSPort`, `Host`, `DocumentRoot`
  (fallbacks -- not `Workers`/`SpareWorkers`, see §8), `HTTP2` (`enabled` → advertise `h2` in the TLS
  handshake, emitted as `protocols h1 h2`), `StaticMaxAge` (seconds; `>0` sends
  `Cache-Control: public, max-age=<n>` on static assets).
- `[LogSettings]`: `Enabled` (off = no access/error log at all), `FileMode`
  (octal mode on the log files). **`Format` is ignored** — Caddy writes JSON.
- `[ControlSettings]`: `StopTimeout` (seconds; also becomes Caddy's
  `grace_period`).
- `EnginePhar` (`[ServerSettings]`): run the kernel from `dist/engine.phar`
  when set to `enabled`/a path; passed as `EXP_ENGINE_PHAR`. Build it first with
  `bin/php/phar.php build` or start is refused.

### 3.5 Settings this engine ignores (and says so)

`status`/`start` list only those an installation actually set. On alpha the
current list is:

- `CacheSettings` — no response cache on the frankenphp engine yet.
- `ForkPerRequest` — not needed; every request starts clean in classic mode.
- `KeepGlobals` — nothing is kept between requests.
- `FollowSymlinks=disabled` — Caddy's file server always follows links out of
  the document root; there is no switch for it. (Security note in §9.)
- `PreloadWarmup` — Qbix server only.
- `LogSettings Format=<x>` — Caddy writes JSON.
- `Brand*` / `HTTP2ErrorImage` — the Qbix server's own pages only.
- `DashboardSettings` — the Qbix server's dashboard only.
- `ControlSettings ExtraOptions` — Qbix options; use
  `[FrankenPHPSettings] ExtraOptions[]`.

---

## 4. HTTPS & certificates

FrankenPHP serves HTTPS on `HTTPSPort` beside plain HTTP on `Port`, **on by
default** (`HTTPS=enabled`), so a development machine has `https://` from the
first start. TLS uses one of three certificate sources, in this order:

1. **Named certificate** — `[HTTPSSettings] Certificate` **and** `Key` both set.
   Both must name existing files or the start is refused (with `--https` or a
   named cert, a missing file is a hard failure; without, it falls back to a
   self-signed one and says why). This is what alpha uses.
2. **Self-signed** — both empty. The engine makes a certificate for this machine
   (`localhost`, `127.0.0.1`, `::1`, the host name, and a specific non-loopback
   bind address), SHA-256, RSA 2048, valid 365 days, in
   `var/vc/frankenphp/tls/` (key `0600`, cert `0644`). It is renewed
   automatically when fewer than 30 days remain. Browsers warn about it — fine
   for development and behind a reverse proxy, **not for visitors**. Needs the
   `openssl` extension; without it the server starts with plain HTTP and the
   start message says why.
3. **Let's Encrypt / ACME** — FrankenPHP/Caddy can obtain certificates via ACME,
   but this engine sets `auto_https off` in the generated Caddyfile, so Velocity
   does **not** drive ACME for you. Two supported paths:
   - **Share an existing cert** (the alpha approach): point `[HTTPSSettings]
     Certificate`/`Key` at a certificate another system already manages — here
     the Let's Encrypt cert **Plesk** holds for the host, exported outside the
     document root. This is the same file the qbix engine serves. Browser-trusted,
     no ACME work in Velocity.
   - **Your own ACME/Caddy config** via `SiteInclude` (§5/§10): add Caddy TLS
     directives (e.g. an `acme` issuer) in the imported fragment. Advanced;
     you own the renewal loop then.

### Renewal (shared Plesk cert)

Plesk renews the Let's Encrypt cert on its own schedule, writing a **new file
under a new name**; the exported copy then goes stale. The export — copying the
certificate and key Plesk currently assigns to the domain (under
`/usr/local/psa/var/certificates/`) to the paths named in `[HTTPSSettings]
Certificate`/`Key` — must be **repeated after each renewal**, followed by
reloading the engine so it picks up the new file:

```bash
# after copying the renewed certificate and key into place:
./console exp:velocity graceful --engine=frankenphp --allow-root-user
```

> A `graceful` reloads the config (and thus re-reads the cert files) without
> dropping connections. Because the cert path in `velocity.ini` is fixed, if the
> *path* changes you must update `[HTTPSSettings] Certificate`/`Key` too.

### Per-start TLS overrides

```bash
./console exp:velocity start --engine=frankenphp --no-https   # plain HTTP, this start only
./console exp:velocity start --engine=frankenphp --https      # insist on HTTPS, this start only
```

`--https`/`--no-https` are the frankenphp engine's (the built-in `php` server
has no TLS; the qbix engine takes `[HTTPSSettings]`). A server started with
`--https` serves TLS even though the settings don't say so — `status` reads the
running Caddyfile, not just the settings, so it reports the truth.

---

## 5. The generated Caddyfile

`var/vc/frankenphp/run/Caddyfile` is regenerated from `velocity.ini` on every
`start`, `graceful` and `restart`, written atomically, and **validated with
`frankenphp validate` before it replaces the previous one** — a broken config
never reaches the running server (a broken `SiteInclude` leaves the running file
untouched). The header says: *"Do not edit: it is rewritten on every start,
graceful and restart."*

### What it contains (verified output)

- **Global block**: `auto_https off` (Velocity manages TLS, not ACME);
  `admin <AdminAddress>`; `persist_config off`; `grace_period <StopTimeout>s`;
  `servers { protocols h1 [h2] }`; an error `log` block (JSON, level INFO); and
  a `frankenphp { … }` block with `num_threads <Workers>`,
  `max_threads <Workers+SpareWorkers>`, and one `php_ini "<key>" "<value>"` line
  per resolved ini option (§6).
- **`(exp)` snippet** — imported into every site block: `root * <DocumentRoot>`;
  an access `log` block (`format json`); `encode <codecs>` from `Compression`
  (e.g. `encode zstd br gzip`; left out when `Compression=disabled`); and a
  `route { … }` implementing `.htaccess_root`:
  - `respond /Q/health 200` — health check.
  - `@static` matcher = `expVelocity::STATIC_PATHS` **and not**
    `expVelocity::NEVER_STATIC`; `header @static Cache-Control` (when
    `StaticMaxAge>0`); `file_server @static`.
  - `@rest` `^/(api/|index_rest\.php)` → `rewrite /index_rest.php`.
  - `@treemenu` `^/([^/]+/)?content/treemenu` → `rewrite /index_treemenu.php`.
  - `@front` (everything else) → `rewrite /index.php`.
  - `php { env EXP_VELOCITY_ENGINE "<version>"; [env EXP_ENGINE_PHAR "<phar>"] }`.
- **Site blocks**: `http://:<Port>` and, when TLS is on,
  `https://:<HTTPSPort> { tls "<cert>" "<key>" }`. Each has `bind <Host>`,
  `import exp`, and `import "<SiteInclude>"` when set.

`STATIC_PATHS` (the exact regexp) matches design/extension
stylesheets/images/javascript/fonts, `share/icons/`, `var/*/storage/images`,
storage originals for images, public/texttoimage caches, package styles,
`var/storage/packages/`, `favicon.ico`, `robots.txt`, `sw.js`, `w3c/p3p.xml`.
`NEVER_STATIC` = `(?i)(\.(php\d?|phtml|phar)$|/\.)` — no PHP source and no
dotfile is ever served as a file, even below an asset directory; both fall
through to `index.php`. This is why the engine does **not** use Caddy's
`php_server` (whose default would serve `settings/*.ini`, the SQLite DB, kernel
sources).

### Inspecting and driving the binary — `exp:velocity ctl …`

`ctl` runs the binary's own subcommands with this installation's Caddyfile
filled in:

| Command | Does |
|---|---|
| `ctl caddyfile` | print the Caddyfile these settings generate (does not touch the file on disk) |
| `ctl validate` | write the Caddyfile, then `frankenphp validate` it |
| `ctl adapt` | write and adapt (show the JSON config Caddy would run) |
| `ctl version` | `frankenphp version` (FrankenPHP, PHP and Caddy versions) |
| `ctl list-modules` | list the Caddy modules the binary was built with |
| `ctl build-info` | Go build info for the binary |
| `ctl environ` | the environment the binary sees |

```bash
./console exp:velocity ctl caddyfile --engine=frankenphp --allow-root-user
./console exp:velocity ctl version   --engine=frankenphp --allow-root-user
./console exp:velocity ctl list-modules --engine=frankenphp --allow-root-user | grep cache
```

### Custom Caddy config — `SiteInclude`

Put your own directives (headers, redirects, extra routes, a custom TLS issuer)
in a file and name it with `[FrankenPHPSettings] SiteInclude`. It is imported
into **every** site block (both the HTTP and HTTPS ones). The generated file is
rewritten on every start, so never edit it directly. A `SiteInclude` that Caddy
rejects fails validation and the running config is left untouched.

Example `settings/caddy/extra.caddy`:

```caddy
header {
    Strict-Transport-Security "max-age=31536000"
    X-Content-Type-Options "nosniff"
}
```

```ini
[FrankenPHPSettings]
SiteInclude=settings/caddy/extra.caddy
```

---

## 6. PHP configuration for the embedded interpreter

This is the single most important thing to understand about FrankenPHP here:

**The binary reads no php.ini of the machine it runs on.** Its PHP is compiled
in, and without configuration it runs on PHP's compiled-in defaults —
`display_errors` on, a 128 MB memory limit, and **no MySQL socket path**. So
everything the machine's php.ini would normally provide has to be supplied here,
as `php_ini` lines in the Caddyfile.

Two sources feed those lines, in order (later wins):

1. `[PHPSettings] IniOptions[]` (shared with qbix)
2. `[FrankenPHPSettings] IniOptions[]`

Each is `key=value`, exactly as after `php -d`. The engine emits them as
`php_ini "<key>" "<value>"` inside the `frankenphp { … }` global block.

Alternatively, `[FrankenPHPSettings] PHPIniFile=<path>` points the embedded PHP
at a full php.ini of your own via `PHPRC`.

### 6.1 The MySQL socket fix (the key gotcha)

With `[DatabaseSettings] Server=localhost`, mysqli/pdo_mysql connect through a
**unix socket** whose path comes from php.ini. The embedded PHP has no php.ini,
so it has no default socket, and the connection fails:

```
No such file or directory in .../kernel/classes/dbdrivers/ezmysqli/ezmysqlidb.php
```

Exponential then shows the generic **"An unexpected error has occurred."** page.

**Fix** — tell the embedded PHP where the socket is:

```ini
[PHPSettings]
IniOptions[]=mysqli.default_socket=/var/lib/mysql/mysql.sock
IniOptions[]=pdo_mysql.default_socket=/var/lib/mysql/mysql.sock
```

Find the real socket path on your box:

```bash
php -i | grep -i "mysqli.default_socket\|pdo_mysql.default_socket"
# alpha: mysqli.default_socket => /var/lib/mysql/mysql.sock => /var/lib/mysql/mysql.sock
```

**Alternative** — use TCP instead of a socket: set
`[DatabaseSettings] Server=127.0.0.1` in `site.ini`. Then mysqli connects over
TCP and no socket path is needed — **but MySQL must be listening on TCP** (not
`skip-networking`, and bound so the loopback is reachable). The socket route is
usually faster and needs no MySQL config change, which is why it is the alpha
choice.

**Why this generalises:** any behaviour the machine's php.ini normally provides
— a default socket, a timezone, an extension's config, a `sendmail_path`, an
`open_basedir`, a `session.save_path` — is **absent** under FrankenPHP unless
you add it as an `IniOptions[]` line (or via `PHPIniFile`). When something works
under Apache/php-fpm but not under FrankenPHP, suspect a php.ini value the
embedded PHP never saw, and add it here.

### 6.2 Extensions the binary ships vs the app needs

FrankenPHP's official builds ship a broad set of PHP extensions, but **not
every** extension your Exponential install might load under the machine PHP. The
`gnu` variant can `dlopen` external `.so` extensions (e.g. `oci8`); the `musl`
variant is fully static and cannot. To see what the binary actually has:

```bash
# The binary is PHP too; -m lists modules:
var/vc/frankenphp/bin/frankenphp-1.12.7-linux-x86_64-gnu -r 'print_r(get_loaded_extensions());'
# or via ctl:
./console exp:velocity ctl list-modules --engine=frankenphp --allow-root-user   # Caddy modules
```

If the app needs an extension the binary lacks and you're on `gnu`, load the
matching `.so` with an `IniOptions[]=extension=/path/to/ext.so` line. If it's
not available for the binary's exact PHP version/ABI, you need an **own build**
(§10) — this is a real limitation of the single-binary model.

### 6.3 Shipped ini defaults

`[FrankenPHPSettings] IniOptions[]` ships (stand-ins for the missing php.ini):
`display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`,
`post_max_size=64M`, `upload_max_filesize=32M`.
`[PHPSettings] IniOptions[]` ships the OPcache tuning (see §8). Note
`opcache.revalidate_freq=0` from `[PHPSettings]` is harmless here (each request
has its own request time), so edited/regenerated PHP files are always picked up.

---

## 7. Operations

### Verbs

All standard Velocity verbs work; reach the engine with `--engine=frankenphp`
(or make it the default). Add `--all` / `--engine=a,b` for multiple engines.

```bash
./console exp:velocity start    --engine=frankenphp --allow-root-user
./console exp:velocity stop     --engine=frankenphp --allow-root-user
./console exp:velocity restart  --engine=frankenphp --allow-root-user
./console exp:velocity graceful --engine=frankenphp --allow-root-user   # reload, no dropped connections
./console exp:velocity kill     --engine=frankenphp --allow-root-user   # SIGKILL, no grace
./console exp:velocity status   --engine=frankenphp --allow-root-user
```

- **`start`** checks everything that can refuse (binary present or auto-install,
  certificate, Caddyfile validation, engine phar) *before* doing anything, then
  refuses if the port is already in use (naming the culprit), then launches and
  polls `/Q/health` for up to 20 s. It reports the version and, if HTTPS was
  turned off against the settings, why.
- **`graceful`** regenerates and validates the Caddyfile, then reloads via the
  admin API (`frankenphp reload`). Unlike qbix's reload this **cannot leave a
  half-reloaded server**: a config Caddy rejects is never applied and the old
  one keeps serving; a rejected reload is *reported*, not answered with a
  restart. If the admin API doesn't answer at all, it falls back to a full
  restart.
- **`stop`** asks the admin API to `/stop` (letting in-flight requests finish
  within `grace_period`), then falls back to SIGTERM, then reports if still
  running after `StopTimeout`.
- **`restart`** validates readiness while the old server is still up, then stop +
  start.

### The admin socket

`stop` and `graceful` talk to Caddy's admin API. By default that is a **unix
socket** at `var/vc/frankenphp/run/admin.sock`, readable/writable by the owner
only — no TCP port to clash with another installation. If the installation path
is too long for a socket (> 104 bytes), it falls back to
`localhost:<Port + 10000>`. Override with `AdminAddress`. `status` shows the
address and whether the API is reachable. The engine talks HTTP over the socket
directly (no Origin header, which the API would reject).

### Where everything lives

```
var/vc/frankenphp/
├── bin/    frankenphp-<version>-<asset>  (+ .sha256)   the binary (git-ignored)
├── caddy/  config/ + data/               Caddy's XDG state (certs, autosave)
├── tls/    selfsigned.crt, selfsigned.key             self-signed cert (if used)
├── log/    access.log, error.log         JSON; [FrankenPHPSettings] LogDir
└── run/    server.pid, console.log, Caddyfile, admin.sock
```

`status` names each file. `status` also reports process/threads, version +
binary source (`download` vs `BinaryPath`), HTTPS state and certificate, and the
ignored-settings notes.

### Running beside qbix

Verified on alpha: frankenphp (8089/8070) runs concurrently with qbix
(8088/8080), each with its own port, pid, logs, admin socket and config.
`exp:velocity status --all` shows both. Starting/stopping/reloading frankenphp
never touches the qbix process — the cert is shared read-only, and qbix reads
its cert from the generated `/etc/vc` config, so changing the frankenphp cert
setting does not restart qbix.

### Upgrading the binary

Change `Version` **and every `Sha256[]` line together** (digests are on the
release's GitHub API page = `ApiUrl`), then `install`. The old binary stays in
`var/vc/frankenphp/bin`, so rolling back is only the setting. See §2.4 for the
stale-pin procedure.

```bash
# after editing Version + Sha256[…] in the override:
./console exp:velocity install --engine=frankenphp --allow-root-user
./console exp:velocity restart --engine=frankenphp --allow-root-user
```

### Pinning / own builds

`BinaryPath` points at a binary you built (another PHP version, extra Caddy
modules such as the response-cache `cache-handler`); nothing is downloaded, and
`BinarySha256` (if set) is checked. Own builds are made with FrankenPHP's
`static-builder-gnu.Dockerfile` / `static-builder-musl.Dockerfile` — see §10.

---

## 8. Performance & optimisation

### Threads (classic mode)

`Workers` = number of PHP threads (base pool), `SpareWorkers` = extra threads
started under load; the Caddyfile gets `num_threads Workers` and
`max_threads Workers+SpareWorkers`. Each PHP thread bootstraps the kernel per
request (classic mode), so CPU and memory scale with thread count. There is no
fork-per-request cost as with a process pool, but also no warm worker reuse.

**Default: twice the CPU cores, no extra.** Left empty, `Workers` is
`2 × cores` (24 on a 12-core machine) and `SpareWorkers` is 0 -- FrankenPHP's
own default. Neither falls back to `[ServerSettings]`: those size the Qbix
server's process pool, where most processes sit idle, while FrankenPHP threads
all compete for the CPU at once. Until 2026-09-26 they did fall back, and alpha
ran `num_threads 590`/`max_threads 638`.

**Why not more.** Measured on alpha (12 cores, rendered `/site/` pages, two
passes each, the box otherwise busy):

| threads | 16 concurrent | 64 concurrent | CPU per page at 64 | p95 at 64 |
|---:|---:|---:|---:|---:|
| 12 | 16.4 req/s | 16.0 req/s | 233 ms | 4.4 s |
| 24 | 15.6 req/s | 16.0 req/s | 261 ms | 4.5 s |
| 48 | 17.2 req/s | 16.5 req/s | 270 ms | 4.5 s |
| 96 | 16.7 req/s | 14.7 req/s | 294 ms | 5.1 s |
| 590 | 17.5 req/s | 14.6 req/s | 290 ms | 4.9 s |

At 16 concurrent the count makes no difference. At 64, 12–48 threads do about
10% more work than 96 or 590 with up to 24% less CPU per page: past the core
count, extra threads only contend with each other and with the database. The
ceiling is the machine -- about 0.25 s of PHP and 0.25 s of database CPU per
rendered page -- not FrankenPHP; there is no limit at 64. Twice the cores is
chosen over one per core to keep the CPU busy while threads wait on the
database, which is half of each page. Set `Workers` explicitly only after
measuring your own pages.

### Worker mode

Not available. Exponential's kernel keeps request state in globals, so classic
mode (clean state per request) is the only safe choice. Do not try to enable
FrankenPHP worker mode by hand — it will leak state between requests.

### OPcache

The most impactful tuning. The embedded PHP honours `opcache.*` via
`IniOptions[]`. Shipped: `opcache.enable=1` and (from `[PHPSettings]`)
`opcache.enable_cli=1`, `opcache.memory_consumption=256`,
`opcache.max_accelerated_files=20000`, `opcache.interned_strings_buffer=32`,
`opcache.revalidate_freq=0`. Measured on this codebase, OPcache alone gave the
bulk of the gain (~+26 %); tracing JIT was *slower* on this workload and is
deliberately absent. Keep `revalidate_freq=0` so edited/regenerated PHP files
are noticed.

### Go runtime knobs

FrankenPHP is a Go program; the Go runtime env vars apply and can be passed via
the process environment (e.g. in the systemd/launcher wrapper, or a `SiteInclude`
that sets them is *not* the place — these are process env, not Caddy config):

- `GOMEMLIMIT` — soft memory ceiling for the Go heap; useful to keep the process
  within a cgroup limit.
- `GOMAXPROCS` — max OS threads executing Go code simultaneously; pin it to the
  cores you want Caddy to use.

Set them where the process is launched. Velocity launches the binary directly;
export them in the shell/service that runs `exp:velocity start`, or wrap via
`ExtraOptions[]` is *not* for env — use the environment of the launching
process.

### Static file serving

Static assets are served by Caddy's `file_server` directly (no PHP), with
`Cache-Control: public, max-age=<StaticMaxAge>` when `StaticMaxAge>0` (shipped
default 31536000 = one year). `HTTP2=enabled` lets a browser multiplex the
dozens of assets an Exponential page references over one connection.

### Compression

Caddy compresses nothing unless told to, so until September 2026 every page
left this engine uncompressed. `[FrankenPHPSettings] Compression` now puts an
`encode` directive in the generated Caddyfile (§5):

```ini
[FrankenPHPSettings]
# Codecs offered to clients that ask, in order of preference; or disabled.
Compression=zstd br gzip
```

- The codecs are named in order of preference, from `zstd`, `br` and `gzip`
  (all three are built into the official binary; check with
  `exp:velocity ctl list-modules --engine=frankenphp`, look for
  `http.encoders.*`). Unknown names are dropped, a name given twice counts once,
  and `disabled`, `off`, `none`, `0` or an empty value leave `encode` out.
- A client gets the first codec in the list that it accepts. A browser sends
  `Accept-Encoding: gzip, deflate, br, zstd`, so with the default it gets zstd,
  the cheapest of the three to compress; put `br` first for the smallest pages.
- Caddy skips small bodies and types that do not shrink (images, archives), and
  adds `Vary: Accept-Encoding` so caches keep one copy per coding.

Measured on alpha's front page (`/site/`, 95,724 bytes uncompressed):

| `Accept-Encoding` | Bytes on the wire |
|---|---:|
| `identity` (none) | 95,724 |
| `gzip` | 12,121 |
| `zstd` | 11,862 |
| `br` | 9,982 |
| a browser (`gzip, deflate, br, zstd`) | 11,862 (zstd) |

On a loopback benchmark compression was worth up to a third more requests per
second at moderate load and little at saturation, where rendering is the limit;
for real visitors it is the 88% fewer bytes that counts. The setting is applied
on the next `start`, `restart` or `graceful`; confirm it with
`grep encode var/vc/frankenphp/run/Caddyfile` and
`curl -s -o /dev/null -D - -H 'Accept-Encoding: gzip' https://<host>:<HTTPSPort>/ | grep -i content-encoding`.

### Response cache

**None yet** on this engine — `[CacheSettings]` is ignored. If you need
in-server response caching today, that is the qbix engine, or a reverse proxy /
Caddy `cache-handler` module in front (the latter requires an own build, §10).

### Benchmarking pointers

Round-robin requests across engines so drift hits all equally; measure a real
content page and the anonymous front page; watch p99, not just mean. `curl -w`
for latency, `ab`/`wrk`/`hey` for throughput. Compare against qbix on the same
box to see the cache's effect.

---

## 9. Debugging & troubleshooting

### Where every log is

| Log | Path | Format |
|---|---|---|
| Server console output | `var/vc/frankenphp/run/console.log` (`LogFile`) | plain + Caddy JSON lines |
| Access log | `var/vc/frankenphp/log/access.log` (`LogDir`/`AccessLog`) | **JSON** |
| Error log | `var/vc/frankenphp/log/error.log` (`LogDir`/`ErrorLog`) | **JSON**, level INFO |
| Exponential app errors | `var/log/error.log` (and per-siteaccess under `var/<sa>/log/`) | eZ format |

`status` prints all of the engine's paths (relative to the installation).
`[LogSettings] Enabled=disabled` turns the access/error logs off entirely.

### Reading Caddy JSON

Each access/error line is one JSON object. Pretty-print and filter with `jq`:

```bash
tail -n 50 var/vc/frankenphp/log/error.log  | jq .
tail -n 50 var/vc/frankenphp/log/access.log | jq '{ts, status, uri: .request.uri, dur: .duration}'
tail -f  var/vc/frankenphp/log/access.log | jq 'select(.status >= 500)'
```

Startup failures print to `console.log`; the engine's own `start` message tails
the last error lines when a start fails.

### Troubleshooting table

| Symptom | Cause | Fix |
|---|---|---|
| `SHA-256 mismatch … expected … got …` on install | pinned `Sha256[]` is stale (release re-cut) or wrong asset/variant | verify against GitHub's digest (`gh api … .digest`), set the pin, re-run `install --force`. §2.4 |
| `velocity.ini pins no SHA-256 for <asset>` | new `Version`, no pin | add `Sha256[<asset>]=<hex>`, or `install --trust-github-digest`. §2.3 |
| `No such file or directory in …ezmysqlidb.php` / DB connect fails | embedded PHP has no default MySQL socket | add `mysqli.default_socket` + `pdo_mysql.default_socket` `IniOptions[]`, or use `Server=127.0.0.1`. §6.1 |
| "An unexpected error has occurred." page | usually the DB socket issue above; or an app-level fatal | check `var/log/error.log` and `var/vc/frankenphp/log/error.log`; set `display_errors=On` temporarily via `IniOptions[]` to see it |
| Port not reachable from outside | OS firewall closed, or `Host` bound to loopback | open `<HTTPSPort>/tcp` (§2.5); set `Host` to the public interface |
| Browser cert warning | self-signed certificate in use | name a real cert in `[HTTPSSettings]`, or terminate TLS in a proxy. §4 |
| Start refused: "port … already in use" | another engine/installation on that port | change `[FrankenPHPSettings] Port`/`HTTPSPort`, or stop the other. `status --all` |
| Start refused: "HTTPS: … Certificate and Key must both name files that exist" | named cert path wrong/missing | fix the paths, or empty both for self-signed. §4 |
| 502 / blank on an AJAX or REST path | app fatal in `index_rest.php`/`index.php` path | read `error.log` JSON; check the DB/socket and memory_limit |
| 500 on every page | Caddyfile served but app boot fails (DB, permissions, missing ext) | `error.log`; verify DB socket, `var/` writable, needed PHP extension present (§6.2) |
| Siteaccess not resolving (wrong/blank site) | `DocumentRoot` or the `/site/` path; siteaccess matching | keep `DocumentRoot` empty (= root) and use the path siteaccess (`/site/`), or match by host in `site.ini`; `status` lists the URLs |
| Permission denied on log/pid/socket | `var/vc/frankenphp/` not writable by the run user, or `FileMode`/dir mode | ensure the run user owns `var/vc/frankenphp/`; check `[LogSettings] FileMode` |
| A feature works under Apache but 500s here | a php.ini value the embedded PHP never saw (timezone, extension config, socket, open_basedir) | add it as `IniOptions[]`, or point `PHPIniFile` at a full php.ini. §6.1 |
| Missing PHP extension | the binary doesn't ship it | on `gnu`: `IniOptions[]=extension=/path/ext.so`; else an own build. §6.2/§10 |
| `graceful` says "reload rejected" | the new Caddyfile (usually `SiteInclude`) is invalid | fix the `SiteInclude`; the running config was kept; `ctl validate` to see the error |
| Admin API "did not answer" | socket removed, or `AdminAddress` changed since start | `restart` (graceful falls back to it automatically) |

---

## 10. Extending & development

### Adding Caddy modules / an own FrankenPHP build

The downloaded binary is the stock FrankenPHP with its built-in module set
(`ctl list-modules` to enumerate). To add Caddy modules (e.g. the response-cache
`cache-handler`, a rate limiter, an ACME issuer plugin) or a different PHP
version/extension set, build your own binary with FrankenPHP's static builder
(`static-builder-gnu.Dockerfile` / `static-builder-musl.Dockerfile`), then point
Velocity at it:

```ini
[FrankenPHPSettings]
BinaryPath=/opt/frankenphp-custom
BinarySha256=<hex>     # optional; install verifies it when set
```

Nothing is downloaded when `BinaryPath` is set. Everything else (Caddyfile
generation, verbs, logs) works identically.

### Custom SiteInclude

Add your own Caddy directives (headers, redirects, extra routes, a TLS issuer)
via `[FrankenPHPSettings] SiteInclude` — imported into every site block, checked
by validation, never overwritten. §5.

### Adding PHP ini / extensions

Use `IniOptions[]` (per-key) or `PHPIniFile` (a full php.ini). §6. Remember the
embedded PHP starts from *nothing*, so anything the machine php.ini would give
must be added.

### Integrating app changes

Because classic mode reloads PHP per request and OPcache uses
`revalidate_freq=0`, edited templates, regenerated caches and changed PHP files
are picked up **without a restart** — the same cadence as php-fpm. A restart is
only needed for changes to the *engine configuration* (`velocity.ini` /
`[FrankenPHPSettings]`), which regenerate the Caddyfile:

```bash
./console exp:velocity graceful --engine=frankenphp --allow-root-user   # config change, no dropped conns
```

For a class/INI change that the app itself caches, follow the project's normal
cache cadence (`bin/php/ezpgenerateautoloads.php -e`,
`bin/php/ezcache.php --clear-all`) — that is app-level, independent of the
engine. See `AGENTS.md`.

### How the engine regenerates config

`start`/`graceful`/`restart` call `caddyfileText()` → `writeCaddyfile(true)`
(atomic temp-file write + `frankenphp validate` + rename). A validation failure
leaves the previous file intact. `ctl caddyfile` prints the text without writing;
`ctl validate`/`ctl adapt` write then run the binary. There is no state to
migrate and no `/etc/vc` tree — the Caddyfile is the whole configuration
(`layout`/`migrate`/`site|conf|mod` verbs belong to qbix and refuse here).

---

## 11. Verification checklist & quick reference

### Post-install / post-change checklist

1. `./console exp:velocity ctl caddyfile --engine=frankenphp --allow-root-user`
   — the Caddyfile is what you expect (ports, host, cert, php_ini lines).
2. `./console exp:velocity ctl validate --engine=frankenphp --allow-root-user`
   — it validates.
3. `./console exp:velocity install --engine=frankenphp --check --allow-root-user`
   — the binary matches its SHA-256.
4. `./console exp:velocity start --engine=frankenphp --allow-root-user`
   — it starts and answers health.
5. `curl -k -o /dev/null -w '%{http_code}\n' https://<host>:<HTTPSPort>/site/`
   — 200.
6. `echo | openssl s_client -servername <host> -connect <ip>:<HTTPSPort> | openssl x509 -noout -issuer`
   — the expected issuer.
7. Load a real content page and an admin page; confirm the DB works (no
   "unexpected error"), CSS/JS load, and a REST/AJAX path answers.
8. `./console exp:velocity status --all --allow-root-user` — frankenphp running,
   other engines (qbix) undisturbed.
9. Firewall: `firewall-cmd --list-ports` includes `<HTTPSPort>/tcp`.

### Quick-reference commands

```bash
# lifecycle
./console exp:velocity start|stop|restart|graceful|kill|status --engine=frankenphp --allow-root-user
./console exp:velocity status --all --allow-root-user

# install / upgrade
./console exp:velocity install --engine=frankenphp [--force|--check|--from=<file>|--trust-github-digest] --allow-root-user

# config
./console exp:velocity config get  FrankenPHPSettings Port --allow-root-user
./console exp:velocity config set  ServerSettings Engine frankenphp --allow-root-user
./console exp:velocity config paths --allow-root-user

# inspect the binary / caddyfile
./console exp:velocity ctl caddyfile|validate|adapt|version|list-modules|build-info|environ --engine=frankenphp --allow-root-user

# stale-pin fix
gh api repos/php/frankenphp/releases/tags/v<VERSION> \
  --jq '.assets[]|select(.name=="frankenphp-linux-x86_64-gnu")|.digest'
# -> set Sha256[frankenphp-linux-x86_64-gnu]=<hex> in the override, then install --force

# find the mysql socket for IniOptions[]
php -i | grep -i "mysqli.default_socket\|pdo_mysql.default_socket"

# firewall (firewalld)
firewall-cmd --permanent --zone=public --add-port=<HTTPSPort>/tcp && firewall-cmd --reload

# logs (Caddy JSON)
tail -f var/vc/frankenphp/log/access.log | jq 'select(.status>=500)'
tail -n 50 var/vc/frankenphp/log/error.log | jq .
```

### Key file locations

- Engine class: `kernel/classes/expvelocityfrankenphp.php`
- Installer: `kernel/classes/expvelocityfrankenphpinstaller.php`
- Settings (defaults): `settings/velocity.ini` — `[FrankenPHPSettings]`,
  `[PHPSettings]`, `[HTTPSSettings]`
- Overrides (this install): `settings/override/velocity.ini.append.php`
- Generated Caddyfile: `var/vc/frankenphp/run/Caddyfile` (do not edit)
- Binary: `var/vc/frankenphp/bin/frankenphp-<version>-<asset>`
- Cross-engine reference: [`doc/bc/6.0/velocity-engines.md`](velocity-engines.md)
