# Specification: expservices (remote services over ezjscore)

Extension `extension/expservices`, version 0.1.0, introduced 2026-10-02. User-level introduction:
[remote services](../../features/6.0/remote-services-expservices.md); full per-domain tables:
[doc/bc/6.0/backend_ezjscore_services.md](../../bc/6.0/backend_ezjscore_services.md).

## Architecture

| Element | Definition |
|---|---|
| Transport | ezjscore: `<root>/ezjscore/call/<block>::<method>[::<arg>...]`; the router (`ezjscServerRouter`) refuses a class without an `[ezjscServer_<name>]` block and hands every class extending `expServiceBase` to `expServiceBase::invoke()` |
| Domain | one class `exp<Domain>Services` extending `expServiceBase`, registered in `extension/expservices/settings/ezjscore.ini.append.php` as `[ezjscServer_exp<domain>]` with `Class=` |
| Service | a public static method plus an entry in the class's `public static $services` (`summary`, `access`, `write`, `args`, `returns`) |
| Access | `public`, `user`, or `array( module, function )`; 401 without login, 403 without policy |
| Envelope | `{ok, data, meta}` or `{ok:false, error:{code,message}}`; codes 400, 401, 403, 404, 409, 422, 500 |
| Writes | POST, form token (`ezxform_token` or `X-CSRF-Token`) unless an API token signs the request, the policy, audit event `service.<domain>.<method>` |
| Tokens | table `expservices_token` (SQL in `sql/{mysql,postgresql,sqlite}/schema.sql`); SHA-256 hash only; prefix `expt_`; 1 to 365 days |
| Audit | taxonomy branch `expServicesAuditBranch` registered in `settings/audit.ini.append.php` of the extension |

## Classes

Core classes sit in `extension/expservices/classes/`: `expServiceBase`, `expServicesCatalog`, `expSessionServices`,
`expSystemServices`, `expIniServices`, `expCacheServices`, `expCronjobServices`, `expExtensionServices`,
`expPackageServices`, `expWorkflowServices`, `expVelocityServices`, `expRadServices`, `expDebugServices`,
`expEditorServices`, `expServiceToken`. The other domains are in the subdirectories `content/` (fifteen
classes over a shared base with the access checks, the exporters and the now-or-job routing), `users/`,
`commerce/`, `community/`, `media/` and `misc/`. Helpers of the base: `arg()`, `post()`, `paging()`, `pageOf()`,
`ok()`, `page()`, `guard()`, `audit()`, `node()`, `can()`.

## Discovery services (public)

`expservices::catalog[::<domain>]`, `schema::<domain>`, `service::<domain>::<method>`, `domains`, `version`.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `extension/expservices/settings/expservices.ini` | `Services` | `Enabled` | `enabled` | installation |
| same | `Paging` | `DefaultLimit`, `MaxLimit` | 25, 200 | installation |
| same | `Writes` | `RequireToken`, `Audit` | `enabled`, `enabled` | installation |
| same | `Community` | class identifiers (see the feature page) | `forum`, `forum_topic`, ... | installation |
| `extension/expservices/settings/expportal.ini` | `Portal` | `SiteTitle`, `NewsNode`, `ShopNode`, `ForumsNode`, `MediaNode`, `PageSize` | Exponential Portal, 2, 2, 2, 43, 12 | installation |

## Extension points

- A new domain: a class extending `expServiceBase`, a block in an extension's `ezjscore.ini.append.php`, then
  `php bin/php/ezpgenerateautoloads.php -e`.
- The RAD survey (`setup/rad`) reads the static `$services` of every such class and lists each declared
  service as an entry of the registry "Remote services (expservices)", named `<function>::<service>`; a declared
  service whose method is missing is shown as broken. The survey total rose from 2,799 to 3,921 points
  (1,122 services) on the reference installation.

## Notes

- `expvat::removeRule` and `removeType` remove a rule themselves, because `eZVatRule::removeVatRule()` called an
  instance method statically, which PHP 8 refuses; the kernel method was fixed the same day (a VAT rule can be
  removed again in the admin).
- Tests: the unit tests of the base, the catalogue and the core services, and live-database tests per domain
  (test content under the Media root, removed in `tearDown`; baskets on a test session only; no order or payment
  is created).
- Command-line clients: `extension/expservices/bin/expservices-client.sh` and `expservices_client.py` (both send
  the token headers).
