# Audit: tracking what happens in Exponential

Status: **design, agreed with the owner on 2026-10-02 — not implemented yet.** This document is the specification the
work is built against, stage by stage (see "Delivery"). It also records the dashboard permission defect found the same
day, which is stage 1.

## What exists today

`kernel/classes/ezaudit.php` (`eZAudit`, 134 lines) and `settings/audit.ini`:

- **Off** by default (`[AuditSettings] Audit=disabled`); no installation file enables it on alpha.
- `eZAudit::writeAudit( $name, $attributes )` writes a plain text block through `eZLog::write()` to
  `var/<site>/log/audit/<file>.log`, one file per event name (`AuditFileNames[<name>]=<file>`):
  ```
  [ Oct 02 2026 13:30:01 ] [203.0.113.7] [admin:14]
  Node ID: 275
  Object ID: 273
  ...
  ```
- Ten event names are configured: user-login, user-failed-login, content-delete, content-move, content-hide,
  role-change, role-assign, section-assign, state-assign, order-delete. They are written from about a dozen places
  (eZUser, eZContentObject, eZContentObjectTreeNode, eZRole, the role edit view, the content operations, eZOrder; since
  today also the content jobs' hide and section types).
- The ezmbpaex extension writes `user-forgotpassword*` and `user-password-change*` events that have **no file
  configured**, so they are dropped.
- Missing: an event id, request/session correlation, siteaccess, URL, server engine, duration, result, before/after
  values, a structured format, retention, compression, archives, integrity, a viewer, access control, alerts.
- Not audited at all: publish/edit/translate, locations, swap, trash/restore, classes, policies, user lifecycle,
  settings writes (setup views, `exp:ini`, the debug bar), cache clears, cronjobs, commands, logout/sessions,
  permission and form-token refusals, packages/installs, the repair queue, content jobs as a whole, commerce beyond
  order delete, exports/imports.

## Owner decisions (27 questions, 2026-10-02)

| # | Question | Decision |
|---|---|---|
| Q1 | Storage | **Both**: JSON lines files are the record; an index in the site's database serves the console (F4) |
| Q2 | Fields | **Request context** (event id, request id, siteaccess, URL/method, module/view, engine Apache/Velocity/CLI, host, pid, duration, HTTP status), **actor** (user id, login, roles at the time, session hash, IP v4/v6, user agent, impersonation, CLI user + command), **before/after values** (configurable, never passwords/tokens), **result + reason** (success/refused/failed, policy/token/lock reason, error) |
| Q3 | Privacy | **Configurable, safe default**: per field full / truncated IP (/24, /48) / hashed / off; user agent on/off; never passwords or tokens; personal fields pseudonymised after the retention period |
| Q4 | Integrity | **Hash chain + signed archives**: each event carries the previous event's hash per file; archives get a checksum manifest and an HMAC; the console shows whether a chain is intact or where it breaks |
| Q5 | Families | **Content lifecycle, users + access, system + config, commerce + data** — and the classification must be generic and reusable ("track almost everything, zoology style"): see the taxonomy below |
| Q6 | Files | **Channels by family, daily files** (`content-2026-10-02.jsonl`, `access-…`, `system-…`, `commerce-…`); INI maps events to channels; the old per-event file names keep working as aliases |
| Q7 | Sinks | **syslog/journald, webhook/HTTP, e-mail on critical events, a sink registry** for extensions |
| Q8 | Rotation | **By day and size; compressed archives** (gzip, bzip2, xz, zstd, zip through format handlers in a registry) to a configurable archive path; **retention per channel; scheduled by a cronjob part** (also runnable from the console and the command) |
| Q9 | Access | New policies **audit/read** (view, search, export) and **audit/manage** (rotation, archives, retention, settings); every console access is itself audited; optional password re-entry before manage actions |
| Q10 | Console | **Timeline + filters + search, event detail + links, charts + alerts view, export** |
| F1 | Taxonomy | **Both**: hierarchical dotted names for configuration and routing, and every record also carries actor / verb / object / target / result (ActivityStreams-like) |
| F2 | Correlation | **Request id** (also a response header), **session and job ids** (content jobs, cronjob runs), **parent/child events** (a subtree remove is a parent with child events, depth configurable) |
| F3 | Speed | **Buffered**, flushed in one append at request end (also on fatal errors through the shutdown handler); security events written at once; per-request state reset for Velocity's persistent workers |
| F4 | Index | **The site's main database** (schema on every engine: Z1) |
| F5 | Alerts | **Built-in rules** (brute force, admin role granted, settings written out of hours, mass delete, audit disabled or chain broken), **INI rules** (`[AlertRule_x]` Event, Threshold, Window, GroupBy, Severity, Sinks[]), **rule classes** in a registry |
| F6 | Retention | **90 days live, 2 years archived**, per channel; personal fields pseudonymised in the index after 90 days |
| F7 | Dashboard | **Only what the user's policies allow**: every dashboard block and sidebar link checks access to its module/view (and limitations) |
| Z1 | Engines | **SQLite, MySQL/MariaDB, PostgreSQL, Oracle, MongoDB** — schema and tests on each one reachable here |
| Z2 | Placement | **A new `audit` module with its own top tab** (console, event, archives, settings), **dashboard sidebar link + block** (recent security events, alerts), **links from content/job and content/jobs** (the job's audit trail), **the Setup menu** |
| Z3 | API | **`expAudit::event()`** (the old `eZAudit::writeAudit()` keeps working and maps to it), **template operator/fetch** (policy checked), **ezpEvent bridge** (audit existing kernel events by INI mapping), **command `exp:audit`** (tail, search, verify, rotate, archive, reindex, export, import) |
| Z4 | Old logs | **Imported** into the new format (marked imported, outside the chain); originals archived |
| Z5 | Default | **On by default in every installation** (owner, 2026-10-02: "enabled in a default installation by default conventions, vs ezp4 where it was off"): the shipped `settings/audit.ini` has `Audit=enabled` with access, security, system/config and destructive content actions on; read tracking off. See "Default installation" |
| Z6 | Reads | **Optional, sampled**: node views and searches per section/class with a sample rate; views of sensitive admin modules (setup, role, user, audit) always |
| Z7 | Key | **Generated on first use, stored in settings/override** (never committed), shown as a fingerprint; key rotation with key ids in archives |
| Z8 | Delivery | **Stages with sign-off** (below) |
| Z9 | Proof | **Coverage matrix, tamper test, performance, permission matrix** |
| Z10 | Docs | **Operator guide, developer guide, event reference, security notes** |

## The event model

### Taxonomy (F1)

A dotted name with ranks like a biological classification — **domain.subject.action[.detail]** — so configuration
and routing can work at any level, and new branches can be added without touching existing ones:

```
content.node.publish        content.node.move         content.node.remove.trash    content.object.translate
content.job.start           content.job.finish        content.job.cancel
access.session.login        access.session.login.failed   access.session.logout    access.permission.refused
access.token.refused        access.user.create        access.role.assign           access.policy.change
system.setting.write        system.cache.clear        system.cronjob.run           system.command.run
system.package.install      system.velocity.deploy    system.audit.chain.broken
commerce.order.delete       commerce.basket.checkout  data.export.csv              data.import
```

INI switches on or off at any rank (`content.*`, `content.node.*`, `content.node.remove.*`), maps branches to
channels and sinks, and extensions register whole branches (`[AuditEventSettings] Branches[myext]=…`). Every record
also carries the activity fields **actor, verb, object, target, result**, so it can be fed to activity-stream
consumers unchanged.

### One record (JSON line)

```json
{"v":1,"id":"01J9Z…","name":"content.node.move","time":"2026-10-02T13:30:01.123Z",
 "request":{"id":"r-7f3c…","siteaccess":"admin","method":"POST","url":"/content/action","module":"content/action",
            "engine":"velocity","host":"alpha","pid":991876,"ms":184,"status":302},
 "actor":{"user_id":14,"login":"admin","roles":[2],"session":"h:5d2e…","ip":"203.0.113.0/24","ua":"Firefox 131"},
 "verb":"move","object":{"type":"node","id":275,"object_id":273,"name":"Workout"},
 "target":{"type":"node","id":89},"before":{"parent":2},"after":{"parent":89},
 "result":"success","reason":null,"parent":null,"job":null,
 "prev":"sha256:9b1c…","hash":"sha256:41aa…"}
```

## Default installation

Audit is a convention of the product, not an option one remembers to switch on. Unlike the 4.x releases, where
`audit.ini` shipped with `Audit=disabled`:

- `settings/audit.ini` ships with `Audit=enabled`; the families access, security, system/config and the
  destructive content actions (remove, trash, move, hide, section, state, role and policy changes, content jobs)
  are on; read tracking (node views, searches) is off; the channels, rotation, 90 days live / 2 years archived,
  privacy defaults (truncated IPs, user agent on, no passwords or tokens) and the hash chain are on.
- Every way an installation is made gets it with no extra step: the setup wizard, `exp:install`, kickstarter, the
  multisite package installer and an upgrade of an existing installation (whose `settings/override` may still say
  `Audit=disabled` from the 4.x releases: the upgrade reports that override and asks before keeping it).
- The archive signing key is generated on the first event, so a fresh installation never runs unsigned.
- The log directory is created with the site user's ownership, also when the first event comes from a command run
  as root.
- Turning it off is an explicit, audited decision: `Audit=disabled` written anywhere is itself recorded as
  `system.audit.disable` before it takes effect, and the dashboard shows an "audit is off" warning to users with
  audit/manage.
- The performance budget (stage 6) is measured with these defaults on, since that is what every site runs.

## Delivery (Z8) — each stage committed and shown before the next

1. **Dashboard permission defect (F7)**: every dashboard block and sidebar link checks the user's access to its
   module/view and limitations; a permission matrix test (Anonymous, Editor, Member, Partner, Administrator).
2. **Event core**: `expAudit::event()`, taxonomy registry, buffered writer with request/session/job correlation,
   JSON lines channels, hash chain, privacy rules, `audit.ini` (every setting documented), the `eZAudit::writeAudit()`
   compatibility mapping, per-request reset for Velocity.
3. **Instrumentation**: every family of Q5 in the kernel (and the ezpEvent bridge), with the coverage matrix test on
   Apache, Velocity and CLI.
4. **Index + console**: the database index on all five engines (Z1), the `audit` module and tab, timeline, filters,
   search, event detail with links, charts and alerts view, export; dashboard block and sidebar link; links from
   content/job and content/jobs; Setup menu entry; policies audit/read and audit/manage.
5. **Sinks, alerts, rotation**: syslog/journald, webhook, e-mail, sink registry; built-in, INI and class alert rules;
   rotation by day and size, compressed archives through format handlers, retention, HMAC-signed manifests, the
   cronjob part, `exp:audit` (tail, search, verify, rotate, archive, reindex, export, import of the old logs).
6. **Docs and proof**: this guide becomes the operator guide, developer guide (worked, tested examples), generated
   event reference and security notes; the tamper test, the performance measurement (target: under 2 ms per request
   with buffered writes, content jobs within +5% time) and the RAD survey counting the new registries.

## Dashboard defect (recorded 2026-10-02)

The admin dashboard shows setup features (and other module views editors may not use) to every user who can open
the dashboard. It must show each block and link only when the current user has access to the module/view behind it,
checked the way the kernel checks it (`eZUser::hasAccessTo()` with limitations), not by role name. Fixed in stage 1.
