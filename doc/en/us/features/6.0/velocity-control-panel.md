# Velocity control panel and dashboard

This page is for administrators who run a Velocity server and want to watch and manage it from a browser. Every
Velocity server has two built-in web views of itself:

- the **dashboard** at `/Q/dashboard`: live requests, workers, memory, status codes and top paths, over a WebSocket
  (no refresh);
- the **control panel** at `/Q/panel`: a password-protected admin page that manages apps, domains, certificates, the
  response cache, logs, workers and settings.

They answer to the server, not to your application, so they keep working when the application is broken. Both carry
one toolbar (Dashboard, Control Panel, Documentation, PHP Info, Health, Metrics, Shell), so each view is a click from
the others. Applies to Exponential Velocity 0.0.4.x.

With them you can see what the server is doing without logging in to the machine (hit rate, workers busy and idle,
swap, slowest requests); change a domain's status, renew a certificate, clear the cache or read filtered logs from a
browser; and restyle the pages without touching the engine.

## First sign-in

1. Open `https://your-host/Q/panel`.
2. On a new installation the panel accepts a documented default key, once, only to let you choose a real password (the
   engine's panel guide names it). Until you do, every other panel action is refused.
3. Choose a password that passes the rules below. The default stops working everywhere.

**On a server reachable from the internet, lock the default down before going live**, or set the password at once
from the command line:

```bash
qbixctl panel:password --root=/path/to/web              # asks twice, without echo
qbixctl panel:password --root=/path/to/web --generate   # makes a strong one, prints it once
```

Pass the same `--root` (or `--app`, `--conf-dir`, `--config`) the server runs with. A running server uses the new
password on its next request; changing it ends every session.

### Password rules

- At least 16 characters (at most 72 bytes: bcrypt reads no more).
- An uppercase and a lowercase letter, a digit and a symbol; at least 10 different characters.
- No letter three times in a row, no run of four in order, none of the product or host names, not a common password.
- At least 80 bits of estimated strength, and different from the current one.

Passwords are stored with bcrypt (cost 12 by default, rehashed when the cost changes). Five failed sign-ins from one
address lock it out for 60 seconds, doubling each time up to an hour (a locked sign-in gets `429`).

## If the panel is locked: the trust rule

Before anything is read or written, every directory from the credential store up to `/` must belong to root or to the
server's user and not be writable by group or others. The store itself must be `0700` / `0600`, and no symbolic link
may be on the way. Otherwise **the panel is locked**: sign-in, the default key and every session are refused, and the
start-up log says why. Check and fix:

```bash
qbixctl panel:check --root=/path/to/web          # prints each path, owner, mode, verdict and the fix
chmod g-w,o-w /srv/www/app                       # the usual cause after an upgrade: a 0775 directory
```

Or keep the files elsewhere by setting `Q.panel.aclDir` and `Q.panel.sessionsDir`.

| Situation | Credentials | Sessions |
|---|---|---|
| Explicit | `Q.panel.aclDir` | `Q.panel.sessionsDir` |
| With a configuration tree | `<tree>/acl` (`/etc/qbix/acl`) | `<state dir>/sessions` (`/var/lib/qbix/sessions`) |
| Without one | `local/` above the document root | `local/sessions/` |

A `local/panel.json` from before 0.0.4.28 is moved once (a copy is kept as `panel.json.pre-migration-<time>`).

## Two-factor authentication (optional)

Off by default. Enrol a device first, then switch enforcement on, so nobody is locked out:

```bash
qbixctl panel:2fa status  --root=/path/to/web
qbixctl panel:2fa enroll  --root=/path/to/web      # prints the secret and an otpauth:// URI
qbixctl panel:2fa confirm --root=/path/to/web --code=123456   # enable; prints ten recovery codes once
```

Then set `{"Q":{"panel":{"twofactor":true}}}` in the site file and restart. Sign-in becomes the password, then a
six-digit code (RFC 6238, 30-second step) or a recovery code (each usable once). A wrong or replayed code feeds the
same lockout as a wrong password. If you lose the authenticator, `qbixctl panel:2fa disable --root=...` on the box is
the recovery path. You can also enrol in the panel's **Security** tab.

## Who may open what

For `/Q/panel` and `/Q/api/...`:

- from this machine, always;
- from elsewhere, only when a password is set (or the default is still in force and the page shows its sign-in form),
  when the request carries the dashboard token or a panel session, or when `Q.panel.remote` or `Q.dashboard.remote` is
  `true`.

Otherwise the server's own 403 page says how to set a password. The dashboard, `/Q/stats`, `/Q/metrics` and
`/Q/phpinfo` are for an admin by the same rule. `/Q/health` answers anyone with `{"status":"ok"}` and gives figures to
an admin only.

These are the engine's own rules. An Exponential installation configures them through `settings/velocity.ini` and may
be stricter (for example the panel from this machine only); see "Views and who may open them" in
[Velocity engines](../../bc/6.0/velocity-engines.md).

A browser that is not signed in is redirected to the panel login for the admin views, and returned to the page it
asked for after signing in (release 0.0.4.28).

## Settings

All under `Q.panel` unless noted.

| Configuration key | Default | Meaning |
|---|---|---|
| `bcryptCost` | `12` | bcrypt cost, held between 10 and 15 |
| `passwordMinLength` | `16` | May be raised, never lowered |
| `passwordMinDistinct` | `10` | May be raised, never lowered |
| `passwordMinBits` | `80` | May be raised, never lowered |
| `defaultPassword` | the documented default | `null` or `""` switches the default off; the first password is then set from this machine, with the dashboard token or on the command line |
| `defaultLocalOnly` | `false` | Accept the default only from this machine or with the dashboard token |
| `remote` (and `Q.dashboard.remote`) | `false` | Let anyone who passes the other checks reach the panel from elsewhere |
| `aclDir`, `sessionsDir` | see the trust rule above | Where the credentials and sessions live |
| `twofactor` | `false` | Require a TOTP code after the password |
| `twofactorWindow` | `1` | 30-second steps of clock skew accepted on each side |
| `Q.dashboard` | enabled | `false` disables `/Q/dashboard`, `/Q/health`, `/Q/ws` |
| `Q.dashboard.token` | none | Requires `?token=VALUE` for the dashboard |
| `Q.dashboard.hidePanelRequests` | `false` | Leave `/Q/` requests out of the dashboard's counts and log |

## The tabs

| Tab | What you do there |
|---|---|
| **Apps**, **Frameworks** | See every PHP application the server recognises (name, release, root), switch the document root, run each framework's own tools (clear caches, migrations, route lists). Commands marked `...` change the running installation and ask first. |
| **Domains** | See the domains in use (certificate names, records, Host headers seen); set a status per domain (`active`; `suspended` answers `503` with `Retry-After`; `disabled` answers `404`); aliases, subdomains and a document root per domain; redirects (HTTP to HTTPS, preferred `www` or bare host, forwarding); HSTS; custom error documents; the covering certificate with issue or renew as a background job; per-domain traffic |
| **SSL** | The served certificate, every certificate by expiry, safe settings, renew and reload, a bounded certificate history ([HTTPS and certificates](velocity-https-certificates.md)) |
| **Cache** | Run the [response cache](velocity-response-cache.md) from the browser |
| **Logs** | Filter the access log by text, method, status and host; tabs are bookmarkable |
| **Scripts**, **Plugins**, **Playground**, **System** | Run scripts, see plugins, try PHP in a sandbox (32 MB, 5 s, no network), see PHP and system information |

The server's own `/Q/` and `/.well-known/` paths are never gated by a domain status, so the panel and certificate
renewals keep working on a suspended host.

## The dashboard

Cards: total requests, requests per second (5-second window), average and slowest response, memory and peak, workers
(count, idle, busy, PHP and static requests served), WebSocket connections, data transferred, status codes, top paths
with hit count and average time, a 60-point throughput sparkline, and a live request log with column headings and a
status filter. Two cards are worth knowing:

- **Worker Memory** shows PSS (proportional set size) summed over the parent and a bounded sample of workers, not
  summed RSS (which counts every shared copy-on-write page once per worker and overstates a pool many times).
- **System RAM** shows used (total minus available) and, when swap exists, swap used / total coloured by how fast
  pages come back from swap: dim when idle, amber at 1 MB/s read back or 90 percent full, red at 10 MB/s.

| Endpoint | Format |
|---|---|
| `/Q/dashboard` | HTML |
| `/Q/health` | JSON, for monitors |
| `/Q/stats` | JSON, the full payload for Grafana or similar |
| `/Q/metrics` | Prometheus text for scrapers; a browser sees a page |
| `/Q/phpinfo` | PHP's own report as an HTML page with a filter |

## Restyle the server's pages (designs)

The dashboard, panel, documentation, directory listing and error pages read their markup from a design on disk
(`designs/` in the configuration tree, `/etc/vc/designs` for Velocity), so you can restyle them without editing the
engine. Error pages are designed pages in plain words for every error the server answers itself, over HTTP/1.1 and
HTTP/2. An uncaught exception's message is never sent to the client unless `Q.webserver.debug` is on.

## Limits

- The dashboard updates over a WebSocket. Behind a proxy, the shell needs its public origin in
  `Q.shell.allowedOrigins`.
- A panel store that fails the trust rule locks the panel by design; there is no looser fallback.
- Settings saved in the Cache tab win over the site file.

## Related pages

- [Q shell](velocity-q-shell.md), [HTTPS and certificates](velocity-https-certificates.md), [response cache](velocity-response-cache.md), [Velocity web server](velocity-web-server.md)
- Specifications: [HTTP/2 and security](../../specifications/6.0/velocity-http2-and-security.md) (the admin surface), [engine settings](../../specifications/6.0/velocity-engine-settings.md) (`Q.dashboard`, brand, logging)
- Upgrade: [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md) (credential store, trust rule), [Velocity engines](../../bc/6.0/velocity-engines.md) (views and who may open them from Exponential)
- [Changelog: Exponential Velocity engine](../../changelogs/extensions/exponential-velocity.md)
- History: [July 2026](../../history/velocity/2026-07.md) (first panel), [24 September](../../history/velocity/2026-09d.md) (passwords, domains), [25 to 30 September](../../history/velocity/2026-09e.md) (SSL, cache, two-factor)
