# Specification: expservices (remote services over ezjscore)

This page is the reference for the `expservices` extension (version 0.1.0, introduced 2026-10-02): how a remote
service is declared, called, protected and audited, its classes, settings and extension points. Read it if you
call the services from a script or another system, or add a service domain of your own. The user-level
introduction is [Remote services](../../features/6.0/remote-services-expservices.md); the full per-domain tables
are in [ezjscore backend services](../../bc/6.0/backend_ezjscore_services.md).

## In short

- Every call is an ezjscore call: `<root>/ezjscore/call/<block>::<method>[::<arg>...]`.
- Answers are `{ok, data, meta}`, or `{ok:false, error:{code,message}}` on failure.
- Reads follow the service's `access` rule; writes need POST, the policy and a form token or an API token.
- List what exists: `expservices::catalog`.

## Example: list the service domains

The discovery services are public. Replace the host:

```bash
curl -s 'https://<your-host>/ezjscore/call/expservices::domains'
```

The answer is the `{ok, data, meta}` envelope, with the list of domains in `data`. Continue with
`expservices::catalog::<domain>` for the services of one domain and `expservices::service::<domain>::<method>`
for one service.

## Architecture

| Element | Definition |
|---|---|
| Transport | ezjscore: `<root>/ezjscore/call/<block>::<method>[::<arg>...]`. The router (`ezjscServerRouter`) refuses a class without an `[ezjscServer_<name>]` block and hands every class that extends `expServiceBase` to `expServiceBase::invoke()` |
| Domain | One class `exp<Domain>Services` extending `expServiceBase`, registered in `extension/expservices/settings/ezjscore.ini.append.php` as `[ezjscServer_exp<domain>]` with `Class=` |
| Service | A public static method plus an entry in the class's `public static $services` (`summary`, `access`, `write`, `args`, `returns`) |
| Access | `public`, `user`, or `array( module, function )`. Answers 401 without a login, 403 without the policy |
| Envelope | `{ok, data, meta}` or `{ok:false, error:{code,message}}`; codes 400, 401, 403, 404, 409, 422, 500 |
| Writes | POST; a form token (`ezxform_token` or `X-CSRF-Token`) unless an API token signs the request; the policy; audit event `service.<domain>.<method>` |
| Tokens | Table `expservices_token` (SQL in `sql/{mysql,postgresql,sqlite}/schema.sql`). Only the SHA-256 hash is stored; prefix `expt_`; valid 1 to 365 days |
| Audit | Taxonomy branch `expServicesAuditBranch`, registered in the extension's `settings/audit.ini.append.php` |

## Classes

Core classes in `extension/expservices/classes/`:

`expServiceBase`, `expServicesCatalog`, `expSessionServices`, `expSystemServices`, `expIniServices`,
`expCacheServices`, `expCronjobServices`, `expExtensionServices`, `expPackageServices`, `expWorkflowServices`,
`expVelocityServices`, `expRadServices`, `expDebugServices`, `expEditorServices`, `expServiceToken`.

The other domains are in subdirectories: `content/` (fifteen classes over a shared base with the access checks,
the exporters and the routing between "run now" and "run as a job"), `users/`, `commerce/`, `community/`,
`media/` and `misc/`.

Helpers of the base class: `arg()`, `post()`, `paging()`, `pageOf()`, `ok()`, `page()`, `guard()`, `audit()`,
`node()`, `can()`.

## Discovery services (public)

| Call | Returns |
|---|---|
| `expservices::domains` | The domains, each with its class and service count |
| `expservices::catalog[::<domain>]` | Every service (domain, method, summary, access, write, args, returns), of all domains or of one |
| `expservices::schema::<domain>` | The services of one domain |
| `expservices::service::<domain>::<method>` | One service descriptor |
| `expservices::version` | The version of expservices, the envelope version and the Exponential version |

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `extension/expservices/settings/expservices.ini` | `Services` | `Enabled` | `enabled` | installation |
| same | `Paging` | `DefaultLimit`, `MaxLimit` | `25`, `200` | installation |
| same | `Writes` | `RequireToken`, `Audit` | `enabled`, `enabled` | installation |
| same | `Community` | class identifiers (see the feature page) | `forum`, `forum_topic`, ... | installation |
| `extension/expservices/settings/expportal.ini` | `Portal` | `SiteTitle`, `NewsNode`, `ShopNode`, `ForumsNode`, `MediaNode`, `PageSize` | `Exponential Portal`, `2`, `2`, `2`, `43`, `12` | installation |

## Extension points

- **A new domain**: write a class that extends `expServiceBase`, add a block to an extension's
  `ezjscore.ini.append.php`, then regenerate the autoloads:

  ```bash
  php bin/php/ezpgenerateautoloads.php -e
  ```

- **RAD survey**: Setup > RAD (`setup/rad`) reads the static `$services` of every such class and lists each
  declared service in the registry "Remote services (expservices)", named `<function>::<service>`. A declared
  service whose method is missing is shown as broken. On the reference installation the survey total rose from
  2,799 to 3,921 points (1,122 services).

## Notes

- `expvat::removeRule` and `removeType` remove a rule themselves, because `eZVatRule::removeVatRule()` called an
  instance method statically, which PHP 8 refuses. The kernel method was fixed the same day, so a VAT rule can be
  removed in the admin again.
- Tests: unit tests of the base, the catalogue and the core services, and live-database tests per domain. Test
  content is created under the Media root and removed in `tearDown`; baskets use a test session only; no order
  or payment is created.
- Command-line clients: `extension/expservices/bin/expservices-client.sh` and `expservices_client.py`. Both send
  the token headers.

## Related pages

- [Remote services](../../features/6.0/remote-services-expservices.md), [ezjscore backend services](../../bc/6.0/backend_ezjscore_services.md), [ezjscore](../../features/6.0/extensions/ezjscore.md)
- [Audit event model](audit-event-model.md), [RAD tools](../../features/6.0/rad-tools.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [October 2026 chronicle](../../history/2026/2026-10.md)
