# Specification: the audit event model

This page is the technical summary of the audit trail introduced on 2026-10-02: event names, the record format,
channels, files, checkpoints, archives, the database index, the classes, the settings and the extension points.
Read it if you operate the audit trail, connect it to a log system, or record events from your own extension.
The authoritative, much longer text is [Audit](../../bc/6.0/audit.md); the user-level introduction is
[the audit trail](../../features/6.0/audit-trail.md).

## In short

- Every event has a dotted name, `domain.subject.action[.detail]`, and lands in one of five channels.
- Each channel is a hash chain of JSON lines, one file per channel and UTC day, under `var/<site>/log/audit/`.
- `./console exp:audit verify` proves a chain is intact; exit code 0 means intact.
- Passwords, tokens and secrets are never recorded.

## Example: follow and verify the log

Run in the installation root:

```bash
./console exp:audit status                       # the channels and their state
./console exp:audit tail --channel=access --lines=20
./console exp:audit verify --channel=content     # exit code 0 intact, 1 broken chain, 2 refused or error
```

The same commands run as `php bin/php/audit.php <action>`. Other actions: `channels`, `show <event id>`,
`checkpoint`; `tail` also takes `--name=access.*`, `--follow` and `--json`, and `verify` takes `--date=YYYY-MM-DD`.

## Data model

| Element | Definition |
|---|---|
| Event name | `domain.subject.action[.detail]`, from a taxonomy registry of 135 names (`expAuditTaxonomy`). Can be switched on or off at any level (`content.*`, `content.node.remove.*`) |
| Record | A JSON object: actor, verb, object, target, result, before and after values, parent and child event ids, request id, session, job and run ids. Canonical JSON; carries the hash of the previous record of its channel |
| Channel | `content`, `access`, `system`, `commerce` (also holds `data.*`), `read`. Routed by `[AuditChannelSettings] Route[]`: `content.*` to `content`, `content.node.view` to `read`, `data.*` to `commerce`; default channel `system` |
| File | One JSON-lines file per channel and UTC day, `var/<site>/log/audit/<channel>-<date>.jsonl`; a new part past `MaxFileSize`. The first write of a new day appends `system.audit.file.close` to the previous file |
| Checkpoint | Signed head of each channel's chain (daily maintenance, `exp:audit checkpoint`) |
| Archive | A compressed day with a manifest signed by HMAC (key id, link to the manifest before it). Formats: gzip, bzip2, xz, zstd, zip |
| Index | Tables `expaudit_event`, `expaudit_cursor`, `expaudit_file` in the site database |

`expaudit_event` holds one row per record, with the columns `id`, `channel`, `seq`, `name`, `severity` (0 to 7),
`time_ms`, `request_id`, `user`, `object`, `target`, `result`, `parent_id`, `depth`, `job_id`, `run_id`,
`imported`, `pseudonymised`, `record`, `search_text`.

Keys (installation id, signing key, pseudonym key) are generated on first use into `settings/override`.

Privacy defaults: addresses are truncated, sessions hashed, user agents shortened and query values removed.
Passwords, tokens and secrets are never recorded.

## Classes (`kernel/classes/audit/`)

| Class | Role |
|---|---|
| `expAudit` | Record an event |
| `expAuditBuffer`, `expAuditWriter` | One append per channel at the end of the request, with a lock per channel so PHP-FPM, Velocity and commands never interleave |
| `expAuditHook` | The one guarded way kernel code records an event; builds the data only when the name is switched on |
| `expAuditBridge` | Records existing `ezpEvent` events, mapped by `[AuditBridgeSettings] Bridge[]` |
| `expAuditVerifier` | Checks the hash chains |
| `expAuditKeys`, `expAuditConfig`, `expAuditPrivacy`, `expAuditReader`, `expAuditGuard` | Keys, settings, privacy rules, reading, access checks |

Subdirectories: `hook/`, `bridge/`, `sinks/`, `alerts/`, `archive/`, `format/`, `index/`, `console/`.

## Settings (`settings/audit.ini`)

| Block | Key | Default | Scope |
|---|---|---|---|
| `AuditSettings` | `Audit` | `enabled` | installation; `disabled` raises an alert |
| `AuditSettings` | `LogDir` | `log/audit` | installation |
| `AuditSettings` | `OnWriteFailure` | `continue` | installation |
| `AuditSettings` | `RefuseExemptViews[]` | `user/login`, `user/logout` | installation |
| `AuditEventSettings` | `Enabled[]` / `Disabled[]` | on: `access.*`, `system.*`, selected `content.*`, `commerce.*`, `data.*`; off: `access.session.regenerate`, `access.session.expire` | installation |
| `AuditChannelSettings` | `Channels[]`, `Route[]`, `DefaultChannel` | the five channels; `system` | installation |
| `AuditChannel_<name>` | `LiveDays`, `ArchiveDays`, `MaxFileSize`, `ArchiveFormat` | `90`, `730`, `64M`, `gzip` (`read`: `30`, `90`, `256M`, `zstd`) | per channel |
| `AuditChannel_<name>` | `Sinks[]` | `access` and `system` use the `syslog` sink | per channel |

Other blocks of the same file: `[AuditRecordSettings]`, `[AuditPrivacySettings]`, `[AuditBufferSettings]`,
`[AuditChainSettings]`, `[AuditKeySettings]`, `[AuditReadSettings]`, `[AuditSinkSettings]`,
`[AuditAlertSettings]`; sink blocks `[AuditSink_<name>]` (`syslog`, `webhook`, `mail`); alert rule blocks
`[AlertRule_<name>]` (for example `[AlertRule_brute_force]`); recipient groups `[AlertRecipients_<name>]`.

Read a value with:

```bash
./console exp:ini get audit.ini/AuditChannel_read/LiveDays --allow-root-user
```

Expected output: `30`, unless an override changes it.

## Extension points

| Point | How |
|---|---|
| Taxonomy branch | `[AuditEventSettings] Branches[<name>]=<class implementing expAuditTaxonomyBranch>`. An extension registers its own event names; the RAD survey counts them as `auditbranches` |
| Sink | A class implementing `expAuditSink` |
| Alert rule | A class implementing `expAuditAlertRule` (threshold, match, schedule) |
| Archive format | `[AuditArchiveSettings] FormatHandlers[]`. Each handler reports what it needs; the registry falls back to gzip |

The RAD survey (Setup > RAD, `setup/rad`) counts the four audit registries.

## Interfaces

| Interface | Details |
|---|---|
| Console | `./console exp:audit <action>`; exit codes 0 intact, 1 broken chain, 2 refused or error |
| Admin views | `audit/dashboard`, `audit/console`, `audit/event`, charts, alerts, export, archives, settings, `audit/recent` |
| Policies | `audit/read` (limitation `Channel`), `audit/manage` |
| Template fetch functions | `events`, `event`, `count`, `chain_status`, `can_read` (empty without `audit/read`) |
| Template operator | `audit_label` |
| Response header | Every web response carries `X-Exp-Request-Id`; each record the request wrote stores the same id |
| Cronjob part | `cronjobs/audit.php` (class `Exponential\Cronjob\Kernel\Audit`), in its own group and in `frequent` |
| Remote services | The read-only `expaudit` domain of [expservices](expservices.md) |

## Create the tables on an existing installation

```bash
php update/common/scripts/6.0/createaudittables.php
```

It works on every engine, Oracle and MongoDB included. It leaves existing tables alone and indexes the files
written so far.

## Related pages

- [Audit](../../bc/6.0/audit.md), [the audit trail](../../features/6.0/audit-trail.md), [remote services](../../features/6.0/remote-services-expservices.md)
- [Commands, cronjob parts and module views as classes](runnable-commands-cronjobs-views.md), [expservices specification](expservices.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [October 2026 chronicle](../../history/2026/2026-10.md)
