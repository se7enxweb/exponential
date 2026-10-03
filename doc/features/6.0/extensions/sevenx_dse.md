# sevenx_dse: the Database Source Editor

This page is for administrators who need to look at, and with care change, the live database without leaving the
admin. `sevenx_dse` ("7x Database Source Editor") embeds the **AdminNeo** database tool (a downstream fork of Adminer)
as a native module of the Exponential admin, at `/dse/dashboard`. It is the only sanctioned database editor extension
of Exponential.

## What it gives you

- **Raw SQL execution** with syntax highlighting.
- **Table browsing** with filter, sort and pagination; **schema inspection and editing** (tables, columns, indexes,
  foreign keys).
- **Import** (SQL, CSV, TSV) and **export** (SQL dump, CSV, TSV) as direct file downloads that bypass the template
  wrapping.
- **Several database drivers**: MySQL 8.0+ and MariaDB 10.3+, PostgreSQL 14+, SQLite 3.35+.
- **Safety gate**: a mandatory disclaimer and **backup acknowledgement** screen on every bare access.
- **CSRF-protected database switching**: the "Change database" link carries a per-session token `dse_switch`; "return
  to last database" works without storing URLs; AdminNeo's server password sessions are kept across the gate.
- **Role policy**: access is the policy `dse/dashboard`.
- **CSS isolation**: AdminNeo's global styles cannot damage the admin's own layout.

Requires PHP 8.3 or later (8.3 and 8.5 recommended), Exponential 6 and the admin3 design.

## Set it up

1. Install the package:

```bash
composer require se7enxweb/sevenx_dse
```

2. Activate it in `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=sevenx_dse
```

3. Regenerate autoloads and clear the caches:

```bash
php bin/php/ezpgenerateautoloads.php
php bin/php/ezcache.php --clear-all
```

4. Assign the policy `dse/dashboard` only to the roles that must have it.
5. Take a backup first (for example with [git_manager](git_manager.md)), then open `/dse/dashboard` and confirm the
   backup acknowledgement.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `dse.ini` | `DSESettings` | `InsecureUse` | `disabled` | When `enabled`, pre-fills the database password in the login form. Use only in trusted, closed environments |

## What changed

| Version | Date | Change |
|---|---|---|
| 1.0.0 | 23 April 2026 | First stable release; README with screenshots. |
| 1.1.0 | 22 September 2026 | Module views safe on a persistent worker (Velocity). |
| 1.1.1 | 24 September 2026 | Two defects on a persistent worker, see below. |
| 1.1.2 to 1.1.4 | 27 to 30 September 2026 | `ezinfo.php` and `extension.xml` report the release version and name the license in full; the **navigation part has its own identifier** (`dsenavigationpart`: `menu.ini` used `ezupdatenavigationpart`, the identifier of ezupdate, and so renamed that extension's part to "DSE" wherever both were active); menu texts translated (English and German); funding metadata; the description names Exponential. |

About 1.1.1:

- AdminNeo's content security policy **nonce stayed the same for every request** a worker served (`get_nonce()` kept
  it in a function static), so the policy lost its point. It now lives in `$GLOBALS`, fresh per request.
- A **second dashboard request in the same worker died**, because AdminNeo declares constants and driver functions per
  request. The dashboard now asks the worker to be replaced after it answers, so the next request gets a fresh one (a
  guarded call that does nothing under any other server).

## Related pages

- [AdminNeo database manager](../adminneo-database-manager.md)
- [Ecosystem overview](../../../history/ecosystem.md): the upstream tool
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/sevenx_dse.md) and [release notes](../../../changelogs/extensions/sevenx_dse.md)
- [Change ledger](../../../history/ledger/sevenx_dse.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-04](../../../history/extensions/months/2026-04.md), [2026-09](../../../history/extensions/months/2026-09.md) (all extensions)
