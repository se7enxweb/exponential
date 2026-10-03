# Maintenance mode

This page is for administrators who need to take a site offline for a while: an upgrade, a data import, a
reinstall. Maintenance mode takes the whole public site offline, shows visitors a friendly page, and brings it back
with one command or one button. The Kickstarter and the web setup wizard use the same mechanism, so a site that is
being installed never serves a half-built page.

Quick start:

```bash
php bin/php/maintenance.php on --message="Back at 06:00" --until=2h --allow-root-user
curl -sI https://www.example.com/ | head -1      # HTTP/1.1 503 Service Unavailable
php bin/php/maintenance.php off --allow-root-user
```

## What a visitor sees

While maintenance is on, every request to `index.php`, `index_rest.php` and
`index_treemenu.php` is answered with the maintenance page before the settings
or the database are touched:

- HTTP status `503 Service Unavailable` with a `Retry-After` header
- `Cache-Control: no-store` and `noindex`, so no cache or search engine keeps it
- images, styles and scripts are still served, so the page keeps its look

The page is the active theme's `errors/maintenance.html` when an active
extension has one, otherwise `share/maintenance.html`. It is a self-contained
HTML file with `{placeholders}`. A default installation now has a
`favicon.ico` at the document root and the default page links it.

## Switch it on and off

### From the command line

```bash
php bin/php/maintenance.php on --message="Back at 06:00" --until=2h
php bin/php/maintenance.php status
php bin/php/maintenance.php off
./console exp:maintenance on|off|status     # the same, through the console
```

Run as `root`, add `--allow-root-user` to `php bin/php/maintenance.php` (the
script refuses otherwise). `php bin/php/maintenance.php --help` lists the options.

| Option | Meaning |
|---|---|
| `--message="..."` | What the page says, instead of the default text. |
| `--until=30m` / `2h` / `"2026-09-28 06:00"` | Expected end, shown to visitors and sent as `Retry-After`. |
| `--allow-ip=1.2.3.4,5.6.7.8` | Addresses that still see the site. |
| `--allow-admin` | Keep the administration (`/admin`) reachable. |

Switching on also empties the caches that answer ahead of `index.php`.

### From the administration

**Setup > Maintenance** (`/setup/maintenance`) does the same: a message for
visitors, the expected duration, and addresses that still see the site. The
page shows who opened the window, since when, until when and whether it is an
installation running. It needs the `setup / administrate` right and the form
carries the administration's form token. The administration stays reachable
while the site is offline, otherwise the page that switches it off could not be
opened. The address you switched it on from is let through only when **Let my
own address still see the site** is ticked (before 27 September it was always
let through, so to the administrator it looked as if nothing had happened).

## How it works

State is one file, `var/maintenance.json`. While it exists, the front
controllers include `kernel/classes/expmaintenance.php` first (plain PHP, no
autoloader, no database) and answer with the page. A marker that cannot be read
still means maintenance: a maintenance page is better than a half-changed site.

| Marker field | Meaning |
|---|---|
| `reason` | `unknown`, an installation, `setup` (web wizard) or a manual window. |
| `message`, `until` | Shown on the page. `until` is a Unix time; `Retry-After` is the seconds left, or 120 when no end is set. |
| `allow_ips`, `allow_paths` | Addresses and paths still served (`--allow-admin` adds `/admin`). |
| `lease` | Web wizard only: the marker expires 1,800 seconds after the wizard's last request. |
| `allow_token` | Web wizard only: SHA-256 hash of the cookie that lets the wizard's own browser through. |

## Installations use it automatically

- **Kickstarter**: switches maintenance on when a run begins and off when the
  site is installed and checked. After a failed step it stays on and says how
  to end it (`php bin/php/maintenance.php off`).
- **Web setup wizard**: its first request switches maintenance on with reason
  `setup` and gives its browser a random cookie (`exp_setup_wizard`); every
  other visitor gets the maintenance page instead of a second wizard that
  would abandon or overwrite the first run. Each wizard request renews the
  30-minute lease, so a wizard that is left does not keep the site offline. The
  last page switches maintenance off and clears the cookie.

Before this, requests that arrived during an installation met half-built tables
and logged errors that were not the installation's ("no such table:
ezurlalias_ml"). A reinstall verified with one request per second showed 93
maintenance answers during the run and an `error.log` that held only the run's
own begin and end entries.

## Velocity and other caches

The generated Velocity site configuration names the marker as its cache pause
file, so under [Velocity](velocity-persistent-worker-server.md) the response
caches pause during maintenance too (engine release 0.0.4.34 and later).

## Related pages

- [Installing with one command](install-in-one-command.md), [Kickstarter](kickstarter-cli.md), [setup wizard](setup-wizard-and-editor-siteaccess.md)
- [Velocity: running Exponential in a persistent-worker web server](velocity-persistent-worker-server.md)
- [Installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
