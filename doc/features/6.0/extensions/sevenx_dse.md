# sevenx_dse: the Database Source Editor

`sevenx_dse` ("7x Database Source Editor") embeds the **AdminNeo** database tool (a downstream fork of Adminer) as a native module of the Exponential admin, at
`/dse/dashboard`. Use it to look at and, with care, change the live database without leaving the admin: raw SQL, tables, schema, import and export. It is the
only sanctioned database editor extension of Exponential (`sevenx_dse`).

## What it gives you

* **Raw SQL execution** with syntax highlighting.
* **Table browsing** with filter, sort and pagination; **schema inspection and editing** (tables, columns, indexes, foreign keys).
* **Import** (SQL, CSV, TSV) and **export** (SQL dump, CSV, TSV) as direct file downloads that bypass the template wrapping.
* **Multi-driver**: MySQL 8.0+ and MariaDB 10.3+, PostgreSQL 14+, SQLite 3.35+.
* **Safety gate**: a mandatory disclaimer and **backup acknowledgement** screen on every bare access.
* **CSRF-protected database switching** (the "Change database" link carries a per-session token `dse_switch`), "return to last database" without storing URLs,
  AdminNeo's server password sessions kept across the gate.
* **Role policy**: access is the policy `dse/dashboard`; assign it only to the roles that must have it.
* **CSS isolation**: AdminNeo's global styles cannot damage the admin's own chrome.

Requires PHP 8.3 or later (8.3 and 8.5 recommended), Exponential 6 and the admin3 design.

## Set it up

```bash
composer require se7enxweb/sevenx_dse
# settings/override/site.ini.append.php
#   [ExtensionSettings]
#   ActiveExtensions[]=sevenx_dse
php bin/php/ezpgenerateautoloads.php
php bin/php/ezcache.php --clear-all
```

Assign `dse/dashboard` to the roles that may use it, and take a backup first (for example with [git_manager](git_manager.md)).

## Settings (`dse.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| DSESettings | `InsecureUse` | `disabled` | When `enabled`, pre-fills the database password in the login form. Use only in trusted, closed environments |

## What changed

* 1.0.0 (23 April 2026): the first stable release; README with screenshots.
* 1.1.0 (22 September): module views safe on a persistent worker (Velocity).
* 1.1.1 (24 September): two defects on a persistent worker. AdminNeo's content security policy **nonce stayed the same for every request** a worker served
  (`get_nonce()` kept it in a function static), so the policy lost its point; it now lives in `$GLOBALS`, fresh per request. A **second dashboard request in the same
  worker died** because AdminNeo declares constants and driver functions per request; the dashboard now asks the worker to be replaced after it answers, so the next
  request gets a fresh one (a guarded call that does nothing under any other server).
* 1.1.2 to 1.1.4 (27 to 30 September): `ezinfo.php` and `extension.xml` report the release version and name the license in full; the **navigation part has its own
  identifier** (`dsenavigationpart`: `menu.ini` used `ezupdatenavigationpart`, the identifier of ezupdate, and so renamed that extension's part to "DSE" wherever both
  were active) and the menu texts are translated (English and German); funding metadata; the description names Exponential.

## Related

* [AdminNeo](../../../history/ecosystem.md): the upstream tool
* [Chronicle](../../../history/extensions/sevenx_dse.md) and [release notes](../../../changelogs/extensions/sevenx_dse.md)
