# Specification: the audit event model

Technical summary of the audit introduced on 2026-10-02. The authoritative, much longer text is
[doc/bc/6.0/audit.md](../../bc/6.0/audit.md); the user-level introduction is
[the audit trail](../features/6.0/audit-trail.md) (path `doc/features/6.0/audit-trail.md`).

## Data model

| Element | Definition |
|---|---|
| Event name | `domain.subject.action[.detail]`, from a taxonomy registry of 135 names (`expAuditTaxonomy`); switchable at any rank (`content.*`, `content.node.remove.*`) |
| Record | JSON object: actor, verb, object, target, result, before/after values, parent and child event ids, request id, session, job and run ids; canonical JSON; carries the hash of the previous record of its channel |
| Channel | `content`, `access`, `system`, `commerce` (also holds `data.*`), `read`; routed by `[AuditChannelSettings] Route[]` (`content.*` to `content`, `content.node.view` to `read`, `data.*` to `commerce`, default channel `system`) |
| File | one JSON-lines file per channel and UTC day under `var/<site>/log/audit/`, a new part past `MaxFileSize`; the first write of a new day appends `system.audit.file.close` to the previous file |
| Checkpoint | signed head of each channel's chain (daily maintenance, `exp:audit checkpoint`) |
| Archive | compressed day with a manifest signed by HMAC (key id, link to the manifest before it); formats gzip, bzip2, xz, zstd, zip |
| Index | tables `expaudit_event`, `expaudit_cursor`, `expaudit_file` in the site database; `expaudit_event` holds one row per record (id, channel, seq, name, severity 0-7, time_ms, request_id, user, object, target, result, parent_id, depth, job_id, run_id, imported, pseudonymised, record, search_text) |

Keys (installation id, signing key, pseudonym key) are generated on first use into `settings/override`.
Privacy defaults: addresses truncated, sessions hashed, user agents shortened, query values removed; passwords,
tokens and secrets are never recorded.

## Classes (kernel/classes/audit/)

`expAudit` (record an event), `expAuditBuffer` and `expAuditWriter` (one append per channel at the end of the
request, per-channel lock so PHP-FPM, Velocity and commands never interleave), `expAuditVerifier`,
`expAuditKeys`, `expAuditConfig`, `expAuditPrivacy`, `expAuditReader`, `expAuditGuard`, `expAuditHook` (the one
guarded way kernel code records an event; builds the data only when the name is switched on),
`expAuditBridge` (records existing `ezpEvent`s mapped by `[AuditBridgeSettings] Bridge[]`), and the
subdirectories `sinks/`, `alerts/`, `archive/`, `format/`, `index/`, `console/`.

## Settings (settings/audit.ini)

| Block | Key | Default | Scope |
|---|---|---|---|
| `AuditSettings` | `Audit` | `enabled` | installation; `disabled` raises an alert |
| `AuditSettings` | `LogDir` | `log/audit` | installation |
| `AuditSettings` | `OnWriteFailure` | `continue` | installation |
| `AuditSettings` | `RefuseExemptViews[]` | `user/login`, `user/logout` | installation |
| `AuditEventSettings` | `Enabled[]` / `Disabled[]` | `access.*`, `system.*`, selected `content.*`, `commerce.*`, `data.*` on; `access.session.regenerate` and `access.session.expire` off | installation |
| `AuditChannelSettings` | `Channels[]`, `Route[]`, `DefaultChannel` | five channels, `system` | installation |
| `AuditChannel_<name>` | `LiveDays`, `ArchiveDays`, `MaxFileSize`, `ArchiveFormat`, `Sinks[]` | 90, 730, 64M, gzip (`read`: 30, 90, 256M, zstd); `access` and `system` use the `syslog` sink | per channel |

Alert rule blocks are `[AlertRule_<name>]`; sink blocks `[AuditSink_<name>]` (`syslog`, webhook, `mail`);
recipient groups `[AlertRecipients_<name>]`.

## Extension points

- Taxonomy branches: `[AuditEventSettings] Branches[<name>]=<class implementing expAuditTaxonomyBranch>` (an extension registers its own event names, counted by the RAD survey as `auditbranches`).
- Sink classes (`expAuditSink`), alert rule classes (`expAuditAlertRule`: threshold, match, schedule),
  archive format handlers (`[AuditArchiveSettings] FormatHandlers[]`, each reports what it needs and the
  registry falls back to gzip).
- The RAD survey (`setup/rad`) counts the four audit registries.

## Interfaces

- Console: `./console exp:audit <action>` (see the feature page for the list); exit codes 0 intact, 1 broken
  chain, 2 refused or error.
- Admin views: `audit/dashboard`, `audit/console`, `audit/event`, charts, alerts, export, archives, settings,
  `audit/recent`. Policies `audit/read` (limitation `Channel`) and `audit/manage`.
- Template fetch functions `events`, `event`, `count`, `chain_status`, `can_read` (empty without `audit/read`)
  and the operator `audit_label`.
- Every web response carries the header `X-Exp-Request-Id`; the same id is stored in each record the request wrote.
- Cronjob part `cronjobs/audit.php` (class `Exponential\Cronjob\Kernel\Audit`), in its own group and in `frequent`.
- Remote services: the read-only `expaudit` domain of [expservices](../features/6.0/remote-services-expservices.md).

Created on an existing installation with `php update/common/scripts/6.0/createaudittables.php` (every engine,
Oracle and MongoDB included; leaves existing tables alone and indexes the files written so far).
