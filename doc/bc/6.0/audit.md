# Audit: what happens in Exponential, recorded

Exponential 6.0.15 records what happens in an installation: who signed in and who failed to, who moved, hid or
removed content, who changed a role, a setting or a price, which command or cronjob ran, and what the audit itself
did. Every record is one line of JSON in a file per channel and day. Each line carries the hash of the line before
it, so a changed, removed or inserted line shows. The audit is **on in every installation by default**. It costs
less than a tenth of a millisecond on a page that records nothing.

This guide covers the finished subsystem. It is written for three readers:

- **administrators** who read the audit in the admin interface (sections 2 and 3);
- **operators** who keep it running: cron, rotation, archives, keys, retention, privacy, troubleshooting (sections 4,
  6 and 7);
- **developers** who record their own events or extend the audit with sinks, alert rules or archive formats
  (sections 3.4 and 5).

Every command in this guide was run on a reference installation (Exponential 6.0.15, SQLite, Apache with PHP-FPM, and Velocity) on
2026-10-02/03, and the output shown is the real output. Personal and installation-specific values were replaced by
documentation values: addresses by `203.0.113.0/24`, the host name by `web1`, the installation id, key id and
fingerprint by example values, and e-mail addresses by `@example.com` addresses. Actions that change things
(archiving, purging, key rotation, import) were run on a sandbox copy of the audit with test records, so the live
log stayed as it was. This is said wherever it applies.

## In short

| | |
|---|---|
| What changed | New audit subsystem, **on by default** (4.x had it off): records go to `var/<site>/log/audit/<channel>-<YYYY-MM-DD>.jsonl`, with an index in the site's database, the console in the admin, the command `exp:audit`, alert rules, sinks and archives. New policies `audit/read` and `audit/manage` (only the Administrator role holds them). |
| Who is affected | Every installation that upgrades. Existing installations need the index tables once. Old `eZAudit::writeAudit()` calls keep working. The audit's daily work runs from the `frequent` cronjob group. |
| How to check | `php bin/php/console exp:audit verify` (each channel **intact** or **broken**) |
| How to fix | Create the index tables: `php update/common/scripts/6.0/createaudittables.php`; make sure the `frequent` cronjob group runs; give `audit/read` to the roles that should read the audit. See [Quick start](#2-quick-start-two-minutes) and [upgrades](#4-maintenance-guide). |

**Contents**

1. [What the audit offers](#1-what-the-audit-offers)
2. [Quick start (two minutes)](#2-quick-start-two-minutes)
3. [Usage guide](#3-usage-guide): the admin interface · the command `exp:audit` · templates · the developer API
4. [Maintenance guide](#4-maintenance-guide): cron · rotation and archives · retention · verify and restore · keys ·
   the index · importing 4.x logs · upgrades · changing settings · troubleshooting · performance tuning · privacy
   and GDPR tasks · when the audit cannot write
5. [Internals](#5-internals): event flow · the record · the hash chain · taxonomy and registries · buffering · the
   index · sinks · alerts · Velocity · failure modes · security
6. [Reference configurations](#6-reference-configurations): small site · busy site · cluster · strict privacy ·
   SIEM forwarding · long retention for compliance
7. [Settings reference](#7-settings-reference)
8. [Event reference](#8-event-reference) (generated from the taxonomy registry)
9. [Proof](#9-proof): tests, tamper test, performance, permissions
- [Appendix A: design decisions](#appendix-a-design-decisions-27-questions-2026-10-02)
- [Appendix B: how it was built, stage by stage](#appendix-b-how-it-was-built-stage-by-stage)
- [Appendix C: known issues](#appendix-c-known-issues-2026-10-03)

---

## 1. What the audit offers

| You want to know | The audit gives you |
|---|---|
| Who signed in, who failed to, from which network | `access.session.login`, `access.session.login.failed` (with the reason), `access.session.logout`, account locks; the address truncated to its /24 network by default |
| Who removed, moved, hid or restored content | `content.node.*` and `content.object.*`. A subtree removal is one parent event with one child per node. A content job is the parent of everything its batches do |
| Who changed roles, policies, users, sections, states, classes | `access.role.*`, `access.policy.*`, `access.user.*`, `content.section.*`, `content.state.*`, `content.class.*`, with the values before and after |
| Who changed a setting, cleared a cache, ran a command or a cronjob | `system.setting.write` (with the diff; secrets masked), `system.cache.clear`, `system.command.run`, `system.cronjob.run` |
| Who changed prices, VAT, currencies, discounts or removed an order | `commerce.*`; exports and imports of data are `data.*` |
| Whether anyone tampered with the log | `exp:audit verify` and the console show, per channel, **intact** or **broken** with the file, the line and the kind of change |
| To be told when something serious happens | Alert rules for brute force, admin roles granted, settings written out of hours, mass deletes, the audit switched off, a broken chain. Alerts go to syslog/journald and to mail recipients you choose |
| To keep evidence for years | Daily files and compressed archives (gzip, bzip2, xz, zstd, zip). Each archived day has a signed manifest. Retention is set per channel |
| To get the records out | Export as CSV, JSON lines or a signed bundle. Copies go to syslog/journald (local, UDP, TCP, TLS) and to a signed webhook, or to sinks of your own |
| To answer a data-subject access request | `exp:audit export --subject-user=<id>` returns every record by or about that user, pseudonymised ones included |

What it is made of:

- **Channels and files.** Five channels: `content`, `access`, `system`, `commerce` (with `data`), and `read` for
  sampled reads, which is off by default. Each channel writes one file per UTC day,
  `var/<site>/log/audit/<channel>-<YYYY-MM-DD>.jsonl`, and starts a new part past `MaxFileSize`.
- **135 event names** in a taxonomy (`domain.subject.action[.detail]`), switchable at any rank (`content.*`,
  `content.node.remove.*`). Extensions add their own branches. See the [event reference](#8-event-reference).
- **A hash chain per channel**, daily signed checkpoints and signed archive manifests (HMAC-SHA-256 with a key that is
  generated on first use and never leaves `settings/override`).
- **An index** in the site's own database (SQLite, MySQL/MariaDB, PostgreSQL, Oracle, MongoDB) for the console's
  search, filters and charts. The index can be rebuilt from the files at any time.
- **The Audit tab** in the admin: a dashboard, a timeline with filters and full-text search, one event in full,
  charts, alerts, export, archives and settings. There is also a dashboard block, a sidebar link, an Audit tab on
  each node, and audit links on content jobs. Each of them is shown only to users who hold the policy.
- **The command `exp:audit`** with 19 actions, and a **cronjob part** that does the daily work.
- **Privacy by default.** Addresses are truncated, sessions hashed and user agents shortened. Content values,
  passwords, tokens and secrets are never recorded. Reads are off. The index pseudonymises personal fields after
  90 days.

---

## 2. Quick start (two minutes)

The audit is already on. Check it, read it and verify it:

```
$ ./console exp:audit status --allow-root-user
Audit:        enabled
Directory:    var/site/log/audit
Installation: 6f1c3e0a-2b7d-4c55-9a01-3d2e4f5a6b7c
Signing key:  k1-20261002-3fa94c1b  fingerprint 3FA9 4C1B 77D0 E215
Channel     Files      Bytes    Today  Chain
content         2    1495660      600  intact (1506 records)
access          2    1233762       35  intact (1575 records)
system          2   13957482       64  intact (12641 records)
commerce        1      76442        0  intact (93 records)
read            1     105484        0  intact (133 records)
(exit 0)
```

```
$ ./console exp:audit tail --channel=access --name='access.session.*' --lines=3 --allow-root-user
2026-10-03 00:00:44.254  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH09NJPKY38DJD7G9T7GQZ  01M3ZH0A6YPVK60J8W374CDQG4
2026-10-03 00:00:49.997  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH0F9K0MNZG6DQ0KWQ572T  01M3ZH0FTDYQ1D8BW0G3G8EC8T
2026-10-03 00:02:54.653  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH48ZGGSYK3N7NR3Z2F3JX  01M3ZH49HXBMS8TY7HJCA7NWE0
```

```
$ ./console exp:audit verify --allow-root-user
access     INTACT    1575 records in 2 files, 2026-10-02T22:36:00.716Z to 2026-10-03T00:02:59.302Z, 1 checkpoint(s) matched
commerce   INTACT    93 records in 1 file, 2026-10-02T23:29:45.805Z to 2026-10-02T23:53:55.862Z, 1 checkpoint(s) matched
content    INTACT    1506 records in 2 files, 2026-10-02T23:28:16.227Z to 2026-10-03T00:02:34.006Z, 1 checkpoint(s) matched
read       INTACT    133 records in 1 file, 2026-10-02T23:41:59.913Z to 2026-10-02T23:48:57.088Z, 1 checkpoint(s) matched
system     INTACT    12648 records in 2 files, 2026-10-02T22:35:09.898Z to 2026-10-03T00:04:20.408Z, 3 checkpoint(s) matched
(exit 0)
```

Then, in the admin interface as an administrator, open the **Audit** tab (`/audit/dashboard`). To see a record
appear, sign out and in again, then open **Console**: the sign-in is the newest `access.session.login`.

Three things to do on a new installation:

1. **Make sure the cronjob part runs.** It ships in the `frequent` group, so `php runcronjobs.php frequent` every
   few minutes is enough (see [4.1](#41-cron)). The dashboard warns when it has not run for an hour.
2. **Decide who gets alert mail.** The default is the site's `AdminEmail`. Check it with
   `./console exp:audit alerts recipients` (see [4.5](#45-alerts-and-their-recipients)).
3. **Back up `settings/override/audit.ini.append.php`.** It holds the signing and pseudonym keys. Treat the backup
   as a secret. Without the file, archives signed with its keys can no longer be verified.

---

## 3. Usage guide

### 3.1 Who may see what: the policies

| Policy | Grants | Limitation |
|---|---|---|
| `audit/read` | the Audit tab, dashboard, console, event, charts, alerts, export, recent; the dashboard block, sidebar link, node tab and job links; the fetch functions | `Channel` (content, access, system, commerce, read): for example, a shop auditor who sees only `commerce` |
| `audit/manage` | archives and settings views, "Verify now" | none |

No role except Administrator (which holds `*/*`) has these policies in a new installation. To give someone read
access, create a role with `audit/read` (optionally limited by Channel) in Roles and policies and assign it.

A user without the policy sees **nothing** of the audit: no tab, no sidebar link, no block, no node tab, no job
links, and an empty fetch. A typed URL is refused and recorded as `access.permission.refused`. Every allowed view is
itself recorded as `system.audit.read`.

**Password re-entry before manage actions** (Q9; `[AuditConsoleSettings] ReauthForManage`, off by default). With
`ReauthForManage=enabled`, "Verify now" (dashboard, console, `audit/recent`) first shows a small form asking for the
signed-in user's password. The form posts to the same view with the same button and the form token, so a correct
password runs the action at once. The password is checked against the stored hash the way the sign-in checks it
(`eZUser::authenticateHash()`), without signing in again and without changing the session's user. A correct password
holds for `ReauthMinutes` (10) in that session and for that user only. Each attempt is recorded:
`access.session.reauth` (after: action, minutes) or `access.session.reauth.failed` (result failed, reason
credentials; never the password). Cancel returns to the page and does nothing. The command line never asks: whoever
runs `exp:audit` already holds the server. The archives and settings views have no manage buttons (Appendix C), so
"Verify now" is the action this guards today; `expAuditReauth::gate()` is the one call a new manage action makes:

```php
$form = expAuditReauth::gate( $Module, 'AuditVerifyNowButton', 'audit/dashboard', ezpI18n::tr( 'design/admin/audit', 'Verify now' ) );
if ( $form !== null )
    return $form;   // the password form, or the redirect of Cancel
```

Checked on the reference installation over HTTP (Apache), with `ReauthForManage=enabled` set for the test and the override file put back
byte for byte afterwards:

```
PASS Verify now asks for the password: 200
PASS the password form carries the form token
PASS wrong password: the form again (200)
PASS right password: the action runs (302)
PASS within ReauthMinutes: not asked again (302)
PASS both attempts are in the console
```

### 3.2 The admin interface

The **Audit** tab (after Design in the top menu) opens the dashboard. The left menu has Dashboard, Console, Recent
events, Charts, Alerts and Export, plus Archives and Settings for `audit/manage`.

| View | URL | What you see |
|---|---|---|
| **Dashboard** | `audit/dashboard` | Cards that link into the detailed views. *Health*: audit on/off, each channel's chain as last verified, **Verify now** (manage; with the password again when `ReauthForManage` is on), the signing key's age, with a hint after one year. *Today and 7 days*: per channel and family, events per day. *Security*: failed logins by address and by login (hashed for unknown accounts), the latest role grants, refused views. *Alerts* and their mail recipients. *Activity*: top actors and objects today, the latest warnings. *Operations*: the cronjob part (a warning when it has not run for an hour), the index (rows, lag, last reindex), archives, sinks. Quick links. It never reads the files, so it opens in about 25 ms (the 7-day figures are cached for a minute) |
| **Console** | `audit/console` | The timeline, newest first, with a filter form, full-text search and paging, and the chain state of each channel at the top. Records not indexed yet are merged into the first page, so the newest event is always there |
| **Event** | `audit/event/<id>` | One record in full: when (local and UTC), who, the request, object and target with links into the admin (node, object, user, role, job), before and after side by side, the parent, the children, the other events of the same request and job, and the chain position (prev, hash, whether the hash matches the record now). `/(format)/json` returns the line as it is in the file |
| **Charts** | `audit/charts` | Events per day per channel, refusals and failures per day, logins against failed logins, top actors, top event names; the last 14 days (`/(days)/<n>`, up to 366), under the console's filters. Drawn in HTML/CSS, each chart with its numbers in a table. Needs the index |
| **Alerts** | `audit/alerts` | The alerts that fired (`system.audit.alert`: rule, group, count, window, first and last matching events) and the rules in use with any problem. Read-only |
| **Export** | `audit/export` | The console's current filter as CSV, JSON lines (the lines as written, so hashes can be checked) or JSON, at most `MaxExportRecords` records (a larger filter is cut and says so; `exp:audit export` has no limit). Recorded as `system.audit.export` with the sha256 of what was sent |
| **Archives** (manage) | `audit/archives` | Per channel: live files (count, size, oldest), archives and manifests, each live file's verification state (intact, repaired, broken at line n, unchecked), the signing key id and fingerprint. Read-only: archiving and restoring are done by the cronjob part and `exp:audit` |
| **Settings** (manage) | `audit/settings` | The effective audit.ini with the file each value comes from, secrets masked, keys as ids and fingerprints, the sinks and format handlers with what keeps them from working, the index state (tables, full-text kind, rows, lag). Read-only: write settings with `exp:ini` (see [4.9](#49-changing-settings)) |
| **Recent** | `audit/recent` | The latest 100 events, all channels or `/(channel)/<name>`, with a chain line per channel |

**Console filters** are URL parameters, so every filtered view can be bookmarked and shared:

| Parameter | Example | Means |
|---|---|---|
| `(channel)` | `(channel)/access` | one channel |
| `(name)` | `(name)/access.session.*` | `*`, a prefix ending in `.*` (it matches the prefix itself too) or a whole name; anything else, such as `access.session.login*`, is refused: the page says why and lists nothing |
| `(user)`, `(login)` | `(user)/14`, `(login)/editor1` | by the actor |
| `(object)`, `(target)` | `(object)/node:275` | `<type>:<id>`: node, object, user, role, job, setting… |
| `(result)` | `(result)/refused` | success, refused, failed |
| `(severity)` | `(severity)/warning` | this severity or worse |
| `(request)`, `(job)`, `(run)`, `(parent)` | `(job)/20261003-000230-29a7dcc0` | correlation: one request, one content job, one cronjob run, one parent's children |
| `(ip)` | `(ip)/203.0.113.0/24` | as recorded (after privacy) |
| `(from)`, `(to)` | `(from)/2026-10-01/(to)/2026-10-02T12:00` | `YYYY-MM-DD`, `…THH:MM` or `…THH:MM:SS`; without an offset in the site's time zone (the console prints site times), with `Z` or `+02:00` that instant; `(to)` includes the day, minute or second given |
| `(q)` | `(q)/workout` | full-text search over names, object names and before/after values |
| `(legacy_file)` | `(legacy_file)/login.log` | the events a 4.x audit file name stood for |
| `(offset)`, `(limit)` | `(limit)/200` | paging, at most 500 per page |

Example: every failed login of the last day from one network:
`/audit/console/(name)/access.session.login.failed/(ip)/203.0.113.0/24/(from)/2026-10-02`.

**Elsewhere in the admin** (each only with `audit/read`, and only for channels the user may read):

- **Dashboard block** "Audit: security events" on the admin dashboard: the latest notice-or-worse events of the
  access and system channels, open alerts, and the "audit is off" or "chain broken" warnings (manage).
- **Sidebar link** "Audit trail" in the dashboard's left menu.
- **Node tab** "Audit" in every node view: the last ten events about that node and a link to all of them
  (`audit/console/(object)/node:<id>`).
- **Content jobs**: "Audit trail" on `content/job/<id>` and a link per job on `content/jobs`
  (`audit/console/(job)/<id>`): everything that job did, node by node.
- **Setup menu** entry "Audit" (manage).

Every admin response carries the header `X-Exp-Request-Id`. The same id is in `request.id` of every record that
request wrote, so `audit/console/(request)/<id>` shows exactly what one click did.

### 3.3 The command `exp:audit`

`./console exp:audit <action> [options] --allow-root-user` (or `php bin/php/audit.php`). The default action is
`status`. Run as root, it writes files owned by the site user. Reading is recorded as `system.audit.read` and every
change as its own event. `--json` prints machine-readable results for every action that reads.

| Action | Does | Options |
|---|---|---|
| `status` | audit on/off, directory, installation, signing key, files, bytes, today's records and chain per channel | `--json` |
| `channels` | per channel: routes, files, newest file, chain head | `--json` |
| `tail` | the newest records; `--follow` keeps printing | `--channel= --name= --lines=20 --follow --json` |
| `show <id>` | one record in full, its hash re-checked | `--json` |
| `search` | the index (or the files with `--files`), newest first | filters (below), `--files --limit=50 --json` |
| `verify` | the hash chains; exit 0 intact, 1 broken, 2 error | `--channel= --date=YYYY-MM-DD --archives --json` |
| `checkpoint` | a signed checkpoint of every channel's head now | |
| `rotate` | closes the newest file of channels idle since an earlier day; runs retention | `--channel= --dry-run` |
| `archive` | compresses days older than LiveDays (or `--before=`) into signed archives | `--channel= --before= --format= --dry-run` |
| `restore` | decompresses an archived day for reading | `--channel= --date= [--to=<dir>]` |
| `purge` | removes archives older than ArchiveDays (and old index rows) | `--channel= --dry-run` |
| `reindex` | rebuilds the index, or catches up | `--incremental --archives --channel=` |
| `pseudonymise` | the index's pseudonymisation now | `--dry-run` |
| `export` | records to a file | filters, `--subject-user=<id> --format=jsonl\|csv\|bundle --out=` |
| `import` | the 4.x text logs into the new format | `--dir= --file= --dry-run --keep-originals` |
| `key` | `list`, `fingerprint`, `rotate`, `rotate --pseudonym` | |
| `sinks` | `list`, `test <name>`, `flush [<name>]` | |
| `alerts` | `list`, `test <rule> --replay=<date>`, `recipients [--rule=]` | |
| `cron` | one run of the cronjob part | `--daily` (the daily tasks now) |

**Filters** of `search` and `export`: `--channel= --name=<pattern> --user=<id> --login= --object=<type:id>
--target=<type:id> --result=success|refused|failed --severity=<min> --request= --job= --run= --ip=<network>
--from=<time> --to=<time> --query=<text> --legacy-file=<4.x file name>`.

- `--query=<text>` is the full-text search, the console's `(q)`. It cannot be `--q`: `-q`/`--quiet` is every
  script's standard quiet option, and the option parser keeps that name for it.
- `--name` (also for `tail`) takes `*`, a prefix of one to five ranks ending in `.*` (`access.*`,
  `access.session.*`; the prefix itself matches too), or a whole name of 3 to 6 ranks. The first rank is a domain
  (content, access, system, commerce, data). Anything else, such as `access.session.login*`, `access.*.failed` or a
  bare `access.session`, is refused with the reason and exit code 2, and nothing is searched.
- **Times** (one rule for the command): `--from`/`--to` take `YYYY-MM-DD`, `YYYY-MM-DDTHH:MM` or
  `YYYY-MM-DDTHH:MM:SS` (a space for the `T` works too). Without an offset they are **UTC**, the zone the command
  prints. With `Z`, `+HH:MM`, `-HH:MM`, `+HHMM` or `+HH` they are that instant. `--to` includes the whole day, minute
  or second given. A malformed time is refused (exit 2). The console reads `(from)`/`(to)` the same way, but there a
  bare time is in the site's time zone, because the console prints site times.

```
$ ./console exp:audit search --query=logout --name='access.session.*' --limit=2 --allow-root-user
2026-10-03 00:44:39.636  access   access.session.logout              admin(14)              user 14                            success  r-01M3ZKGQTA32V9XK0NQ4WN57R8  01M3ZKGQTM7DC5263YNBNDGTWF
2026-10-03 00:44:29.179  access   access.session.logout              admin(14)              user 14                            success  r-01M3ZKGDKG2PB5W55ZDAEZRWXA  01M3ZKGDKV0B6SA799A0KDFEBT
2 record(s) from the index (limit 2: --limit=)

$ ./console exp:audit search --name='access.session.login*' --allow-root-user; echo "exit $?"
--name: 'access.session.login*': a * may only be the whole pattern or the last rank after a dot (did you mean 'access.session.login.*'?); allowed: *, a prefix of ranks ending in .* (access.*, access.session.*) or a whole name (access.session.login)
exit 2

$ ./console exp:audit search --q=logout --allow-root-user
bin/php/audit.php: invalid option `--q'

# the same ten minutes, once in UTC (as printed) and once with the offset of Central European Summer Time
$ ./console exp:audit search --name=access.session.login --from=2026-10-03T00:30 --to=2026-10-03T00:40 --limit=3 --allow-root-user
$ ./console exp:audit search --name=access.session.login --from=2026-10-03T02:30+02:00 --to=2026-10-03T02:40+02:00 --limit=3 --allow-root-user
2026-10-03 00:33:56.882  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZJX3M2G4BG5EFY2CMR9Z3X  01M3ZJX44JDSQV7624W6SA9B0S
2026-10-03 00:33:40.684  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZJWKTDYFT34YRJSXWG6118  01M3ZJWMAC35VNXCDRSXF37ER2
2026-10-03 00:33:24.440  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZJW3XRXMD69TEGFYNYDKKX  01M3ZJW4ER0H05HBARG2SF7GPJ
3 record(s) from the index (limit 3: --limit=)            (both commands print these three records)

$ ./console exp:audit search --to=2026-10-02T24:00 --allow-root-user
--to: '2026-10-02T24:00' is not a time (YYYY-MM-DD, YYYY-MM-DDTHH:MM[:SS], optionally with Z or +HH:MM)
```

#### Reading

```
$ ./console exp:audit channels --allow-root-user
content
  routes:  content.*
  files:   2 (1495660 bytes), newest content-2026-10-03.jsonl
  head:    seq 600 sha256:9861883047ff7fe7…
access
  routes:  access.*
  files:   2 (1233762 bytes), newest access-2026-10-03.jsonl
  head:    seq 35 sha256:086dd84595769212…
system
  routes:  system.*, (default)
  files:   2 (13959023 bytes), newest system-2026-10-03.jsonl
  head:    seq 66 sha256:7ebd8aa793b8e02d…
commerce
  routes:  commerce.*, data.*
  files:   1 (76442 bytes), newest commerce-2026-10-02.jsonl
  head:    seq 93 sha256:90805b7bb47a2979…
read
  routes:  content.node.view, content.search.*, content.object.download
  files:   1 (105484 bytes), newest read-2026-10-02.jsonl
  head:    seq 133 sha256:1c389fc0ecf5597d…
```

`tail` columns: time (UTC), channel, name, actor (`login(user id)`), object, result, request id, event id.

```
$ ./console exp:audit tail --lines=5 --allow-root-user
2026-10-03 00:04:02.789  system   system.cronjob.run                 anonymous(10)          cronjob audit                      success  r-01M3ZH6C0HHAVXBWZZPPSM6MHS  01M3ZH6C352JHDB4QF2NQB7QP8
2026-10-03 00:04:02.797  system   system.command.run                 anonymous(10)          command runcronjobs.php            success  r-01M3ZH6C0HHAVXBWZZPPSM6MHS  01M3ZH6C3DD30VS3A3FFQQN7P5
2026-10-03 00:04:18.285  system   system.audit.read                  anonymous(10)          audit status                       success  r-01M3ZH6V7EZCJMTDRY3FE67SQN  01M3ZH6V7DV9VTS7JW2VFEQ0XG
2026-10-03 00:04:18.307  system   system.command.run                 anonymous(10)          command bin/php/audit.php          success  r-01M3ZH6V7EZCJMTDRY3FE67SQN  01M3ZH6V83RTAYEB5Y4BBBRSBK
2026-10-03 00:04:19.019  system   system.command.run                 anonymous(10)          command bin/php/audit.php          success  r-01M3ZH6VYBCDG293S16NS72R8C  01M3ZH6VYBJARPGHC93VDZKXYX
```

`tail --follow` prints the newest `--lines` and then every new record as it is written (Ctrl-C ends):

```
$ ./console exp:audit tail --channel=system --lines=2 --follow --allow-root-user
2026-10-03 00:14:41.422  system   system.audit.export                anonymous(10)          audit export                       success  r-01M3ZHSVREF60990SHTRJ8M3K5  01M3ZHSVREK9D7G6M59MQG6QQE
2026-10-03 00:14:41.450  system   system.command.run                 anonymous(10)          command bin/php/audit.php          success  r-01M3ZHSVREF60990SHTRJ8M3K5  01M3ZHSVSAMKDX7PKRDFJYYYY1
```

`show` prints one record, says where it is, and recomputes its hash:

```
$ ./console exp:audit show 01M3ZH49HXBMS8TY7HJCA7NWE0 --allow-root-user
Event 01M3ZH49HXBMS8TY7HJCA7NWE0   access.session.login   success
  where      access-2026-10-03.jsonl line 26, seq 26
  time       2026-10-03T00:02:54.653Z
  channel    access
  severity   info
  verb       login
  depth      0
  request    {"engine":"velocity","host":"web1","id":"r-01M3ZH48ZGGSYK3N7NR3Z2F3JX","method":"POST","module":"user/login","ms":630,"pid":1322013,"siteaccess":"admin","status":200,"url":"/admin/user/login"}
  actor      {"ip":"203.0.113.0/24","login":"admin","roles":[2],"session":"h:7617a963afc3ca9c","ua":"curl 7","user_id":14}
  object     {"id":14,"login":"admin","type":"user"}
  after      {"handler":"standard"}
  prev       sha256:4c3e5dc8417119194c764488576c7c66afe914cea1eb74119465a824edb376bf
  hash       sha256:949b036fdd6f0c59e0214ca1032d56a7a514a4f6b43f8189214b3774b8cc72ba
  check      the hash matches the record
```

(The host and address above were replaced for this guide, so this printed record no longer matches its hash. On
the reference installation it does.) `show <id> --json` prints the line exactly as it is in the file.

`search` reads the index, or the files with `--files`:

```
$ ./console exp:audit search --name='access.session.login.*' --limit=3 --allow-root-user
2026-10-03 00:02:54.653  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH48ZGGSYK3N7NR3Z2F3JX  01M3ZH49HXBMS8TY7HJCA7NWE0
2026-10-03 00:00:49.997  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH0F9K0MNZG6DQ0KWQ572T  01M3ZH0FTDYQ1D8BW0G3G8EC8T
2026-10-03 00:00:44.254  access   access.session.login               admin(14)              user 14 admin                      success  r-01M3ZH09NJPKY38DJD7G9T7GQZ  01M3ZH0A6YPVK60J8W374CDQG4
3 record(s) from the index (limit 3: --limit=)
```

```
$ ./console exp:audit search --result=refused --limit=5 --allow-root-user
2026-10-02 23:50:36.333  access   access.permission.refused          anonymous(10)          view visual/templatecreate         refused  r-01M3ZGDRHCS5D6G93K0DC7FSEX  01M3ZGDRHD4C1TCD02FBFGJE1Y
2026-10-02 23:50:29.393  access   access.permission.refused          anonymous(10)          view visual/templatecreate         refused  r-01M3ZGDHRGP2PEGW0TGDE652MP  01M3ZGDHRHRB0P70G8QQPQGRXN
2026-10-02 23:50:28.537  access   access.permission.refused          anonymous(10)          view visual/templatecreate         refused  r-01M3ZGDGXRFDQ0XVBP8KPNP99W  01M3ZGDGXSH26FHEQMX3ERQ0NS
2026-10-02 23:49:05.745  access   access.permission.refused          a4-test-shopauditor(13734) view audit/settings                refused  r-01M3ZGB02AVXHGJTC09J8YNYJY  01M3ZGB02HEHN5M90ANG13V99P
2026-10-02 23:49:05.602  access   access.permission.refused          a4-test-shopauditor(13734) view audit/archives                refused  r-01M3ZGAZXS74X4W4FFQPRCTD91  01M3ZGAZY2KE8MAQ36K8JSXD40
5 record(s) from the index (limit 5: --limit=)
```

The trail of a content job, from the files:

```
$ ./console exp:audit search --files --channel=content --name='content.job.*' --limit=4 --allow-root-user
2026-10-03 00:02:34.006  content  content.job.finish                 admin(14)              job 20261003-000230-29a7dcc0       success  r-01M3ZH3JR5MDZ2MX1N4YH1P70E  01M3ZH3NCP3RC9KMSPFNRV3T7J
2026-10-03 00:02:31.308  content  content.job.start                  admin(14)              job 20261003-000230-29a7dcc0       success  r-01M3ZH3JR5MDZ2MX1N4YH1P70E  01M3ZH3JRCYH3PWDPP095T1JFF
2026-10-03 00:02:31.301  content  content.job.create                 admin(14)              job 20261003-000230-29a7dcc0       success  r-01M3ZH3JR5MDZ2MX1N4YH1P70E  01M3ZH3JR5QQ59WJWEWP6ZPM8A
2026-10-03 00:02:20.739  content  content.job.finish                 admin(14)              job 20261003-000218-0611fb7d       success  r-01M3ZH31289HVC0HTG3W5QYKTK  01M3ZH38E3BYY485E46S7ZP4P8
4 record(s) from the files (limit 4: --limit=)
```

#### Verifying

```
$ ./console exp:audit verify --channel=access --date=2026-10-02 --allow-root-user
access     INTACT    1540 records in 1 file, 2026-10-02T22:36:00.716Z to 2026-10-03T00:00:34.640Z, 1 checkpoint(s) matched
```

```
$ ./console exp:audit verify --channel=commerce --json --allow-root-user
{
    "commerce": {
        "channel": "commerce",
        "result": "intact",
        "records": 93,
        "files": 1,
        "first_time": "2026-10-02T23:29:45.805Z",
        "last_time": "2026-10-02T23:53:55.862Z",
        "breaks": [],
        "repairs": [],
        "notices": [],
        "checkpoints": 1,
        "last_seq": 93,
        "last_hash": "sha256:90805b7bb47a2979b4791f5ad7288fcf2783ae3e01730e0ac3859aefa42bc675"
    }
}
```

`verify --archives` also reads every archive through its format handler and checks the manifests' HMACs, their
chain (`previous_manifest`) and that the oldest live file continues from the newest archived one. On a channel with
archives (sandbox, after `archive` below):

```
$ ./console exp:audit verify --archives            (sandbox)
content    INTACT    archives: 3 manifests, 3 files, 21 records, 2026-06-01T09:04:00.000Z to 2026-10-03T00:04:03.819Z
access     INTACT    archives: 2 manifests, 2 files, 6 records, 2026-06-01T09:04:00.000Z to 2026-06-03T09:04:00.000Z
system     INTACT    archives: 3 manifests, 3 files, 10 records, 2026-06-01T09:04:00.000Z to 2026-10-03T00:04:03.822Z
access     INTACT    2 records in 1 file, 2026-06-03T09:04:00.000Z to 2026-06-03T09:04:00.000Z, 1 checkpoint(s) matched
content    INTACT    2 records in 1 file, 2026-10-03T00:04:03.820Z to 2026-10-03T00:04:03.819Z, 1 checkpoint(s) matched
system     INTACT    13 records in 1 file, 2026-10-03T00:04:03.822Z to 2026-10-03T00:05:22.619Z
```

A broken chain prints `BROKEN` with the first break (file, line, kind) and exits 1. The kinds are explained in
[5.3](#53-the-hash-chain-and-what-verification-finds). `checkpoint` writes a signed anchor now (the cronjob part
writes one a day):

```
$ ./console exp:audit checkpoint --allow-root-user
Checkpoint written: 01M3ZGYDH0BNG4E5NASHP69XSX (system channel)
```

#### Rotating, archiving, restoring, purging

Dry runs on the reference installation (the oldest live file is from the day before, so nothing is due yet):

```
$ ./console exp:audit rotate --dry-run --allow-root-user
content    nothing to close (today's file is the newest, or the newest is closed)
access     nothing to close (today's file is the newest, or the newest is closed)
system     nothing to close (today's file is the newest, or the newest is closed)
commerce   would close commerce-2026-10-02.jsonl (93 records)
read       would close read-2026-10-02.jsonl (133 records)

$ ./console exp:audit archive --dry-run --allow-root-user
content    nothing due
access     nothing due
system     nothing due
commerce   nothing due
read       nothing due

$ ./console exp:audit purge --dry-run --allow-root-user
content    would remove 0 archived day(s) before 2024-10-03
access     would remove 0 archived day(s) before 2024-10-03
system     would remove 0 archived day(s) before 2024-10-03
commerce   would remove 0 archived day(s) before 2024-10-03
read       would remove 0 archived day(s) before 2026-07-05
```

In the sandbox, which has three days from June (older than LiveDays=90):

```
$ ./console exp:audit archive --dry-run            (sandbox)
content    2026-06-01 would be archived with gzip: content-2026-06-01.jsonl
content    2026-06-02 would be archived with gzip: content-2026-06-02.jsonl
content    2026-06-03 would be archived with gzip: content-2026-06-03.jsonl
access     2026-06-01 would be archived with gzip: access-2026-06-01.jsonl
access     2026-06-02 would be archived with gzip: access-2026-06-02.jsonl
system     2026-06-01 would be archived with gzip: system-2026-06-01.jsonl
system     2026-06-02 would be archived with gzip: system-2026-06-02.jsonl
system     2026-06-03 would be archived with gzip: system-2026-06-03.jsonl
commerce   nothing due
read       nothing due

$ ./console exp:audit archive                      (sandbox)
content    2026-06-01 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/content/2026/content-2026-06-01.manifest.json
content    2026-06-02 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/content/2026/content-2026-06-02.manifest.json
content    2026-06-03 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/content/2026/content-2026-06-03.manifest.json
access     2026-06-01 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/access/2026/access-2026-06-01.manifest.json
access     2026-06-02 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/access/2026/access-2026-06-02.manifest.json
system     2026-06-01 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/system/2026/system-2026-06-01.manifest.json
system     2026-06-02 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/system/2026/system-2026-06-02.manifest.json
system     2026-06-03 archived with gzip (k1-20261003-0a43315c, chain intact): …/log/archive/system/2026/system-2026-06-03.manifest.json
commerce   nothing due
read       nothing due
```

`access-2026-06-03.jsonl` was not archived: it was still the newest file of its channel. A channel's newest file is
never archived until it is closed, either by the first write of a later day or by `rotate`:

```
$ ./console exp:audit rotate --dry-run             (sandbox)
content    nothing to close (today's file is the newest, or the newest is closed)
access     would close access-2026-06-03.jsonl (2 records)
…
```

```
$ ./console exp:audit restore --channel=content --date=2026-06-02      (sandbox)
var/tmp/audit-stage6/sandbox/log/restored/content-2026-06-02.jsonl  (identical to the archived live file)
```

Restored files go to `<LogDir>/restored/` for reading, verifying and searching (`search --files`). They never go
back into the live chain.

```
$ ./console exp:audit purge --dry-run              (sandbox, ArchiveDays=100)
content    would remove 3 archived day(s) before 2026-06-25: content-2026-06-01.manifest.json, content-2026-06-02.manifest.json, content-2026-06-03.manifest.json
access     would remove 2 archived day(s) before 2026-06-25: access-2026-06-01.manifest.json, access-2026-06-02.manifest.json
system     would remove 3 archived day(s) before 2026-06-25: system-2026-06-01.manifest.json, system-2026-06-02.manifest.json, system-2026-06-03.manifest.json
commerce   would remove 0 archived day(s) before 2024-10-03
read       would remove 0 archived day(s) before 2026-07-05

$ ./console exp:audit purge                        (sandbox, ArchiveDays=100)
content    removed 3 archived day(s) before 2026-06-25: content-2026-06-01.manifest.json, content-2026-06-02.manifest.json, content-2026-06-03.manifest.json
…
```

Each removal is written to `<ArchiveDir>/<channel>/purged.jsonl` and recorded as `system.audit.purge` with the
file names and their sha256, so the record of what existed outlives the data. Live files that continued from a
purged day still verify INTACT.

#### The index

```
$ ./console exp:audit reindex --incremental --allow-root-user
Index: 43 rows in 64 ms

$ ./console exp:audit pseudonymise --dry-run --allow-root-user
Pseudonymised: {"ok":true,"rows":0,"error":"","cutoff":"2026-07-05T00:04:33Z"}
```

`reindex` without `--incremental` empties the index tables (one channel with `--channel=`) and rebuilds them from
the files. On the reference installation that took about 6 seconds for 13 000 records.

#### Exporting

```
$ ./console exp:audit export --name='access.*' --from=2026-10-02 --format=csv --out=var/tmp/audit-stage6/access.csv --allow-root-user
1573 record(s) exported as csv to var/tmp/audit-stage6/access.csv (sha256 86b22d7342bd43acf46a6668ba40518c7408d1cf5ca663304916b3d9916c34e7)

$ ./console exp:audit export --channel=content --format=jsonl --limit=50 --out=var/tmp/audit-stage6/content.jsonl --allow-root-user
50 record(s) exported as jsonl to var/tmp/audit-stage6/content.jsonl (sha256 53b2145e194588e610470375ed8173642149f5847cd19b999dd814aaa8dfb369)

$ ./console exp:audit export --name='access.*' --format=bundle --out=var/tmp/audit-stage6/bundle --allow-root-user
1573 record(s) exported as bundle to var/tmp/audit-stage6/bundle/records.jsonl (sha256 a87a91ae085f6130d349ada65eb7c32f40779b0c071b77be784b79f576aad2e1), signed manifest var/tmp/audit-stage6/bundle/manifest.json
```

A **bundle** is `records.jsonl` (the lines as written) and `manifest.json`, which holds the filter, the count, the
sha256 and an HMAC with the signing key. It is what you hand to an auditor: the hashes of the lines can be checked
against each other, and the manifest against the key's fingerprint.

#### Importing the 4.x logs

```
$ ./console exp:audit import --dry-run --allow-root-user
var/site/log/audit/login.log             skipped: imported before (same sha256)
```

On a sandbox directory with two 4.x files:

```
$ ./console exp:audit import --dir=…/old --dry-run          (sandbox)
…/old/login.log 1 entries, 1 record(s) would be imported
…/old/failed_login.log 2 entries, 2 record(s) would be imported

$ ./console exp:audit import --dir=…/old --keep-originals   (sandbox)
…/old/login.log 1 entries, 1 record(s) imported
…/old/failed_login.log 2 entries, 2 record(s) imported
Imported into var/tmp/audit-stage6/sandbox/log/imported (outside the chain, marked imported); originals archived: var/tmp/audit-stage6/sandbox/log/archive/legacy/legacy-20261003-000544-gescfc.manifest.json

$ ./console exp:audit import --dir=…/old --keep-originals   (sandbox, again)
…/old/login.log skipped: imported before (same sha256)
…/old/failed_login.log skipped: imported before (same sha256)
```

An imported record (one line of `imported/access-2026-09-30.jsonl`):

```json
{"actor":{"ip":"203.0.113.0/24","login":"editor1","user_id":14},"after":{"legacy":{"User ID":"14"}},"channel":"access","id":"01M3SE0M1G2JED4MV2DQSF32BA","imported":true,"name":"access.session.login","object":{"login":"editor1","type":"user"},"request":{"siteaccess":"admin","url":"/user/login"},"result":"success","severity":"info","source":{"file":"login.log","line":1,"sha256":"7e7a8b9cf24e21b2e1a8658ff2943f9e63aeabb11914eca9224be9643939e622"},"time":"2026-09-30T15:13:02.000Z","v":1,"verb":"login","x":{"legacy":{"name":"user-login"}}}
```

The 4.x time stamps carry no time zone and are read in the server's time zone (`08:13:02` there became
`15:13:02Z`). The privacy rules apply (the address truncated), and an attempted login of a failed login is hashed.

#### Keys

```
$ ./console exp:audit key list --allow-root-user
k1-20261002-3fa94c1b  3FA9 4C1B 77D0 E215  (active)
```

Rotation (sandbox):

```
$ ./console exp:audit key rotate                   (sandbox)
Signing key rotated: k1-20261003-0a43315c -> k2-20261003-f12e3c8d (fingerprint F12E 3C8D 8CC8 8C12); the old key stays for its archives

$ ./console exp:audit key list                     (sandbox)
k1-20261003-0a43315c  0A43 315C DD87 355B
k2-20261003-f12e3c8d  F12E 3C8D 8CC8 8C12  (active)
```

The archives signed with k1 still verified INTACT after the rotation (`verify --archives`, sandbox).

#### Sinks and alerts

```
$ ./console exp:audit sinks list --allow-root-user
syslog     expAuditSyslogSink     ready; spooled 0; channels access, system
webhook    expAuditWebhookSink    not ready: no URL is set ([AuditSink_webhook] URL); spooled 0; channels -
mail       expAuditMailSink       ready; spooled 0; channels -; last delivery 2026-10-02T23:45:02Z

$ ./console exp:audit sinks test syslog --allow-root-user
Test record 01M3ZH9RJ2B3K5B6RSW394HYJK delivered through syslog
(exit 0)

$ ./console exp:audit sinks test webhook --allow-root-user
Not delivered through webhook: no URL is set ([AuditSink_webhook] URL)
(exit 1)

$ ./console exp:audit sinks flush --allow-root-user
Nothing spooled
```

What the syslog sink writes, as journald shows it:

```
$ journalctl -t exponential -n 1 -o short-iso
2026-10-02T17:05:53-0700 web1 exponential[1325003]: 1 2026-10-03T00:05:53.861Z web1 exponential 1325003 system [exp@32473 id="01M3ZH9RJ5912SQKP1B2FAQHAT" name="system.command.run" seq="124" channel="system" user="anonymous" ip="" result="success" request="r-01M3ZH9RJ25FV748WK0Z3SK7N4" hash="sha256:da6e4…"] {"v":1,…}
```

```
$ ./console exp:audit alerts list --allow-root-user
Alerts: enabled, evaluated in flush, cronjob
brute_force              threshold access.session.login.failed        20 in 300 s by actor.ip, critical -> syslog,mail
brute_force_user         threshold access.session.login.failed        10 in 900 s by object.id, alert -> syslog,mail
admin_role_granted       match     access.role.assign                 critical -> syslog,mail
settings_out_of_hours    schedule  system.setting.write               warning -> syslog
mass_delete              threshold content.node.remove.*              500 in 600 s by actor.user_id, critical -> syslog,mail
audit_disabled           match     system.audit.disable               emergency -> syslog,mail
chain_broken             match     system.audit.chain.broken          alert -> syslog,mail

$ ./console exp:audit alerts test brute_force --replay=2026-10-02 --allow-root-user
brute_force over 15986 record(s) since 2026-10-02: 0 alert(s) would fire (nothing recorded)

$ ./console exp:audit alerts recipients --rule=brute_force --allow-root-user
brute_force
  from:     default -> admin
  mail to:  webmaster@example.com  (admin)
```

`alerts test` runs a rule over past records and only reports. Use it to tune a threshold before you change it.

#### The cronjob part, by hand

```
$ ./console exp:audit cron --allow-root-user
Audit index: 2 rows
Audit cronjob run done
```

`cron --daily` runs the daily tasks now (rotation, verification, archiving, retention, pseudonymisation,
checkpoint) instead of waiting for `RotateAfter`.

### 3.4 Templates: fetch functions and the operator

The `audit` module has five fetch functions. Each checks `audit/read` and its Channel limitation for the current
user and returns an empty result, never an error, when the user may not read. A template therefore cannot leak
events. Use is recorded as `system.audit.read` once per request and fetch.

| Fetch | Parameters | Returns |
|---|---|---|
| `events` | `channel name user login object target result severity request job run from to q offset limit` (limit 10) | rows: `id time time_utc name label channel actor result reason object …` |
| `count` | the same filters | an integer |
| `event` | `id` | one record |
| `chain_status` | `channel`, `verify` (false) | per channel: result, files, first break, verified at |
| `can_read` | `channel` | whether the current user may read that channel (or any) |

The node view's Audit tab (`design/admin/templates/tabs/audit/node.tpl`) is a complete example:

```smarty
{def $audit_tab_module = 'audit'
     $audit_tab_events = fetch( $audit_tab_module, 'events', hash( 'object', hash( 'type', 'node', 'id', $node.node_id ), 'limit', 10 ) )}
{if fetch( $audit_tab_module, 'can_read', hash( 'channel', 'content' ) )}
{foreach $audit_tab_events as $e}
    {$e.time|wash} <a href={concat( 'audit/event/', $e.id )|ezurl}>{$e.label|wash}</a> {$e.actor|wash} {$e.result|wash}
{/foreach}
<a href={concat( 'audit/console/(object)/node:', $node.node_id )|ezurl}>All audit events of this node</a>
{/if}
{undef $audit_tab_module $audit_tab_events}
```

The module name is passed through a variable on purpose. The fetch is then not compiled into a direct class call, so
a server process started before the audit classes existed shows nothing instead of failing.

The operator `audit_label` turns a name into its label, translated in the context `kernel/audit`:
`{$e.name|audit_label|wash}` gives "Node move" for `content.node.move`.

### 3.5 The developer API

All classes are in `kernel/classes/audit/` and in the kernel autoload array. **Guard every call with
`class_exists()`**, so code also runs where the audit is missing or a Velocity worker predates it. **The audit never
throws to its caller**: a failure to record goes to `error.log` and your code carries on.

#### Recording an event

```php
// kernel/classes/ezcontentobjecttreenode.php, eZContentObjectTreeNode::move()
if ( class_exists( 'expAuditHook' ) )
    expAuditHook::emit( 'content.node.move', function () use ( $node, $oldParentNodeID, $newParentNodeID, $oldPath, $nodeID ) {
        $newParent = eZContentObjectTreeNode::fetch( $newParentNodeID );
        return array( 'object' => expAuditHook::node( $node ),
                      'target' => expAuditHook::node( $newParent ) ?: array( 'type' => 'node', 'id' => $newParentNodeID ),
                      'before' => array( 'parent' => (int)$oldParentNodeID, 'path' => (string)$oldPath ),
                      'after' => array( 'parent' => (int)$newParentNodeID,
                                        'path' => $newParent ? $newParent->attribute( 'path_string' ) . $nodeID . '/' : null ) );
    } );
```

- `expAuditHook::emit( $name, $data )` is the kernel's hook point. `$data` can be an array or a closure. The closure
  only runs when the name is on, so describing a node costs nothing when the event is off. Nothing a hook point does
  can throw into the request. Helpers describe things the same way everywhere: `node()`, `object()`, `user()`,
  `role()`, `policy()`, `section()`, `contentClass()`, `states()`, `languages()`, `ids()`.
- `expAudit::event( $name, array $data = array() )` is the core call, and returns the event id (a 26-character ULID)
  or `null` when nothing was recorded. `$data` keys:

| Key | Meaning |
|---|---|
| `object`, `target` | arrays: `type`, `id` and identifying fields (`name`, `object_id`, `class`, `file`, `block`, `variable` …) |
| `before`, `after` | the values that changed. They are cut at `MaxValueLength`. Secrets are masked. Never put content attribute values here |
| `result`, `reason`, `error` | `success` (default), `refused` or `failed`; a reason code (`policy`, `token`, `credentials`, `not_found`, `expired`, …); for failed, `array( 'ref' => …, 'message' => … )`. Refused raises the severity to at least notice, failed to at least warning |
| `severity` | an RFC 5424 name, overriding the registry's |
| `parent` | a parent event id (see below) |
| `x` | your extension's own fields, under its name: `array( 'myext' => array( … ) )` |
| `actor` | only for an actor other than the current user (a command acting for someone) |
| `verb` | default: the catalogue's verb, else the name's third rank |

- `expAudit::isOn( $name )`: ask before building expensive values when you do not use a closure.
- A refusal is the same name with a result:
  `expAudit::event( 'access.permission.refused', array( 'object' => array( 'type' => 'view', 'id' => 'setup/cache' ), 'result' => 'refused', 'reason' => 'policy' ) );`

#### Parents and children

```php
$parent = expAudit::begin( 'content.node.remove', array( 'object' => array( 'type' => 'node', 'id' => 89, 'name' => 'Archive 2019' ) ) );
foreach ( $subtree as $node )
    expAudit::event( 'content.node.remove', array( 'parent' => $parent, 'object' => array( 'type' => 'node', 'id' => $node->attribute( 'node_id' ) ) ) );
expAudit::end( $parent, array( 'after' => array( 'removed' => count( $subtree ) ) ) );
```

`begin()` returns the id at once and writes the parent at `end()`, after its children, so the parent can hold totals.
Past `ChildDepth` levels or `MaxChildren` children, children are counted in the parent's `after.children_omitted`
instead of being written. `expAudit::withParent( $id, function () { … } )` makes `$id` the parent of every event
inside, for code that does not pass ids. The content job worker wraps each batch this way. A parent still open when
the request ends is written with `result: failed`, `reason: error`. `expAudit::setJob( $jobID )` and
`expAudit::setRun( $runID )` stamp `job` and `run` on every following event of the process.
`expAuditHook::muted( array( 'content.object.purge' ), function () { … } )` keeps inner hook points quiet while an
outer one records the action as a whole. For example, the purge inside a removal.

#### Your own event names: a taxonomy branch

```php
// extension/myext/classes/myextauditbranch.php
class myExtAuditBranch implements expAuditTaxonomyBranch
{
    public function events()
    {
        return array(
            'content.myext_poll.vote'  => array( 'label' => 'Poll vote',   'severity' => 'info',   'default' => 'off' ),
            'content.myext_poll.close' => array( 'label' => 'Poll closed', 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        );
    }
}
```

```ini
# extension/myext/settings/audit.ini.append.php
[AuditEventSettings]
Branches[myext]=myExtAuditBranch
```

```php
// extension/myext/modules/poll/close.php
if ( class_exists( 'expAudit' ) )
    expAudit::event( 'content.myext_poll.close', array( 'object' => array( 'type' => 'poll', 'id' => $pollID ) ) );
```

Names are 3 to 6 ranks of `[a-z][a-z0-9_]*` in one of the five domains (`content`, `access`, `system`, `commerce`,
`data`). Start your subjects with your extension's name (`content.myext_*`). A name claimed twice, a class that is
missing or does not implement the interface, or a malformed name is left out and reported by the RAD survey
(registry `auditbranches`) and in `audit/settings`. Regenerate the autoloads after adding the class
(`php bin/php/ezpgenerateautoloads.php -e`).

#### Recording an existing ezpEvent without code: the bridge

```ini
[AuditBridgeSettings]
Bridge[content/state/assign]=content.object.state
Bridge[myext/vote]=content.myext_poll.vote
```

One listener per entry is attached when `ezpEvent::registerEventListeners()` runs (web) or once per ezpEvent
instance (commands). It is attached only once in a Velocity worker. The listener records `after.args`, the event's
arguments made scalar (objects as `class#id`; arguments of `session/*` events are session ids and are only recorded
hashed). A filter event's value passes through unchanged. Shipped: `session/regenerate` →
`access.session.regenerate`, and `session/destroy` and `session/gc` → `access.session.expire`. Both are off by
default.

#### A sink, an alert rule class, an archive format

Each is a class named in audit.ini, counted by the RAD survey (registries `auditsinks`, `auditalertrules`,
`auditformats`):

```php
class myExtAuditSink extends expAuditSinkBase          // implements expAuditSink
{
    public function name()    { return 'myext'; }
    public function problem() { return trim( (string)$this->setting( 'Endpoint' ) ) === '' ? 'no Endpoint is set' : ''; }
    /** @param array[] $records already through the privacy rules; @return int how many were delivered */
    public function deliver( array $records ) { /* send them */ return count( $records ); }
}
// audit.ini: [AuditSinkSettings] SinkClasses[myext]=myExtAuditSink ; [AuditSink_myext] Endpoint=… ; [AuditChannel_access] Sinks[]=myext
```

- **Alert rules** implement `expAuditAlertRule::evaluate( array $config, array $records, $state )` and return the
  alerts (group, count, events, message). They are registered as `[AuditAlertSettings] RuleClasses[<key>]=<class>`
  and used by `[AlertRule_<name>] Class=<key>`. Extend `expAuditAlertRuleBase` for the window state.
- **Format handlers** implement `expAuditFormatHandler` (`name`, `problem`, `extension`, `compress( $source,
  $target, $level )`, `open( $archive )` returning a stream of the plain lines). Extend `expAuditFormatBase` and
  register `[AuditArchiveSettings] FormatHandlers[<name>]=<class>`.

`problem()` returns `''` when the class can work here, otherwise the reason. The settings view and `sinks list` show
it.

#### Settings writes and the 4.x API

- Every write through `expIniEditor` (exp:ini, the debug bar, the settings views) is recorded as
  `system.setting.write`, one record per changed variable, with the diff. Values of variables that
  `expIniEditor::isSecret()` recognises become `[secret]`. A write to audit.ini is also
  `system.audit.setting.write`, and setting `Audit` to anything but `enabled` is `system.audit.disable`, recorded
  before it takes effect.
- `eZAudit::writeAudit( $name, $attributes )` keeps working for extensions. The old names are mapped through
  `[AuditCompatSettings] Map[]`, and the attributes go under `after.legacy` (`Comment`, `NeverRecord[]` keys and
  secrets dropped). Unmapped names become `system.legacy.<name>`. The kernel no longer calls it: its call sites
  raise native events.

---

## 4. Maintenance guide

### 4.1 Cron

The cronjob part `audit` (`cronjobs/audit.php`, class `Exponential\Cronjob\Kernel\Audit`) does all the scheduled
work:

- **every run**: the incremental index (at most 120 s), delivery of the sink spools (webhook, network syslog, mail),
  alert evaluation over records since its cursor, the check that Audit was not switched off by hand;
- **once a day**, on the first run after `[AuditRotationSettings] RotateAfter` (00:15, the site's time zone):
  close idle day files, verify every channel, archive what is older than `LiveDays`, purge what is older than
  `ArchiveDays`, pseudonymise index rows older than `PseudonymiseAfterDays`, remove index rows older than
  `KeepDays`, and write the signed checkpoint.

It ships in the `frequent` group of `settings/cronjob.ini`, so the usual crontab line is enough:

```
*/5 * * * * cd /path/to/exponential && php runcronjobs.php -q frequent
```

On its own: `php runcronjobs.php audit`. A run takes `<LogDir>/.cron.lock`, so two runs never overlap. The daily
marker is `<LogDir>/.cron-daily`. On the reference installation the part runs every minute in the `publishing` group
(`settings/override/cronjob.ini.append.php`). Each run is recorded as `system.cronjob.run` (part `audit`). The
dashboard's Operations card warns when the part has not run for an hour.

### 4.2 Rotation and archives

- **Daily files.** Files are named by the UTC date at the moment of writing. The first write of a new UTC day closes
  the previous day's file with `system.audit.file.close`. `exp:audit rotate` closes a channel that nobody wrote to
  since.
- **Size.** Past `MaxFileSize` (64M by default, 256M for read), the day continues in
  `<channel>-<date>.2.jsonl`, `.3.jsonl` …, linked in the same chain.
- **Archiving** (daily task, or `exp:audit archive`): each closed day older than `LiveDays` (90; read: 30) is
  verified (`VerifyBeforeArchive`), compressed with the channel's `ArchiveFormat` into
  `<ArchiveDir>/<channel>/<YYYY>/<file>.jsonl.<ext>`, read back and compared by sha256 (`VerifyAfterArchive`), and
  given a signed manifest `<channel>-<date>.manifest.json`. Only then is the live file removed. A broken day is
  archived all the same (removing evidence would be worse); its manifest says broken and
  `system.audit.chain.broken` is recorded.
- **Formats**: gzip (zlib, always there), bzip2 (ext-bz2), xz (the `xz` binary), zstd (ext-zstd or the binary), zip
  (ext-zip). A handler that cannot work here reports why in `audit/settings`, and archiving falls back to gzip. The
  manifest names the handler actually used. On the reference installation all five work.
- **Where archives go.** `ArchiveDir` (default `log/audit/archive` under the var directory) is best on another file
  system, or on a mount the web server user can add files to but not change (see [5.11](#511-security)). Modes:
  `FileMode=0440`, `DirMode=0750`.

### 4.3 Retention

Per channel in `[AuditChannel_<channel>]`: `LiveDays` (uncompressed in LogDir) and `ArchiveDays` (kept as
archives). Shipped: 90 and 730 for content, access, system and commerce, and 30 and 90 for read. **The shipped
channel blocks set both values**, so a default in `[AuditRotationSettings]` reaches a channel only when you clear the
channel block's own value. Set them per channel (see the
[small-site configuration](#61-small-site)). The index keeps its rows for `[AuditIndexSettings] KeepDays` (730).

Retention never removes a signing key from the settings: `exp:audit purge` names the keys that retained archives still
need. Remove a key yourself only when no archive signed with it remains.

### 4.4 Verify and restore

- **Verify** every day (the cronjob part does), after any incident, and before handing records to anyone:
  `exp:audit verify` (live), `exp:audit verify --archives` (everything), `--channel=`, `--date=`. Exit code 0 =
  intact, 1 = broken, 2 = error, so it fits monitoring: `./console exp:audit verify -q --allow-root-user || alert`.
  Verifying the 14 MB system channel took 2.5 s on the reference installation.
- **Verify now** in the console and on the dashboard (manage) verifies every channel the user may read and stores
  the result. Plain page views show the stored state and never walk the files.
- **What to do with BROKEN**: do not edit or remove anything. Note the file, line and kind, copy the directory
  somewhere safe, and compare with what left the server (syslog or journal copies, webhook receiver, checkpoints
  sent by the sinks, archives). [5.3](#53-the-hash-chain-and-what-verification-finds) explains each kind. A broken
  file stays a record: the console still shows it and marks it.
- **Restore** an archived day for reading: `exp:audit restore --channel=<c> --date=<YYYY-MM-DD> [--to=<dir>]`, then
  `search --files` or read the file. The day is decompressed into `<LogDir>/restored/` and checked against the
  manifest's sha256.

### 4.5 Alerts and their recipients

Alert rules are `[AlertRule_<name>]` blocks listed in `[AuditAlertSettings] Rules[]`. Each firing is a
`system.audit.alert` record sent to the rule's `Sinks[]`. Mail goes to:

1. the rule's own `[AlertRule_<rule>] Recipients[]`, else
2. `[AuditAlertSettings] Recipients[]`, else
3. `[AuditSink_mail] Receivers[]` (the older name), else
4. `admin` = site.ini `[MailSettings] AdminEmail`.

| Recipient | Means |
|---|---|
| `admin` | site.ini `[MailSettings] AdminEmail` |
| `address:ops@example.com` (or a bare address) | that address |
| `group:security` | the named list `[AlertRecipients_security]`: `Addresses[]` and `Recipients[]` (any kind, other groups too; loops are cut) |
| `user:14`, `login:editor1` | that user, with the e-mail address it has when the mail is sent |
| `usergroup:12`, `usergroup:<remote id>` | every enabled user below that user group, sub-groups included |
| `role:Administrator`, `role:<id>` | every enabled user the role is assigned to, directly or through a group |

Addresses are deduplicated without regard to case. Disabled users and invalid addresses are left out. Nothing in
the event itself ever becomes a recipient. Mail is sent by the cronjob part only (a request never waits on SMTP),
one mail per recipient, at most one per rule and recipient within `[AuditSink_mail] Throttle` (900 s). Check with
`exp:audit alerts recipients [--rule=<rule>]`, which lists every address with the entries that produced it and any
problem (an unknown group, a disabled user, a missing role).

To tune a rule, replay it first: `exp:audit alerts test mass_delete --replay=2026-09-01`. Rule classes:
`threshold` (Threshold events in Window seconds per GroupBy; fires once per window, again when the count doubles;
`CountChildren=enabled` counts children), `match` (every matching record; `Policies[]` for role grants), `schedule`
(outside `BusinessDays`/`BusinessHours` in the site's time zone, once per group and window).

### 4.6 Keys

- **What exists**: an installation id, signing keys (`SigningKey[<key id>]`, one active) and a pseudonym key, all in
  `settings/override/audit.ini.append.php` (mode 0640, never committed). They are generated on the first event,
  which records `system.audit.key.create`. exp:ini, the debug bar and the settings view mask them. Only ids and
  fingerprints are shown.
- **Back them up** with the rest of `settings/override`, and treat the backup as a secret. Without the signing keys,
  archives and checkpoints signed with them can no longer be verified (`unknown_key`). Without the pseudonym key,
  new pseudonyms no longer match old ones.
- **Rotate the signing key** once a year (the dashboard reminds you after 365 days), and at once if the key may have
  been read: `exp:audit key rotate`. The new key becomes active, the old ones stay for their archives, and
  `system.audit.key.rotate` is recorded. The command clears the INI cache so that other processes read the new active key; if a
  Velocity worker keeps signing with the old one, restart Velocity.
- **The pseudonym key** is replaced only on purpose: `exp:audit key rotate --pseudonym`, then
  `exp:audit pseudonymise`. Old and new pseudonyms of the same person then differ.
- **Keys from a secrets store**: set `[AuditKeySettings] GenerateKeys=disabled` and provide `InstallationID`,
  `ActiveSigningKey`, `SigningKey[<id>]` (base64, 32 bytes) and `PseudonymKey` in settings/override yourself.
- **Several servers** must share one key file (see the [cluster configuration](#63-cluster)).

### 4.7 The index

The index (`expaudit_event`, `expaudit_cursor`, `expaudit_file` in the site's database) is a copy of the files for
the console. Nothing writes to it during a page request. It is filled:

- by the cronjob part (every run, incremental, at most `BatchSize` rows per batch, rows and cursor in one
  transaction, so a crash never indexes a line twice or skips one);
- briefly when a console view opens (a bounded catch-up), and by `exp:audit reindex --incremental`;
- from scratch by `exp:audit reindex` (all channels, or `--channel=`).

The indexer checks each record's `prev` against the cursor. A mismatch marks the file broken in `expaudit_file`
and records `system.audit.chain.broken` once. Full-text search is FTS5 with the trigram tokenizer on SQLite,
`FULLTEXT` on MySQL/MariaDB, a generated `tsvector` with GIN on PostgreSQL, Oracle Text when available, and `LIKE`
everywhere else (and for terms under three characters). Imported records (`<LogDir>/imported/`) are indexed
without a chain check. Reads are indexed only with `IndexReads=enabled`.

Size on the reference installation: about 0.8 to 1.1 KB per record in the files. Plan for about 1.2 KB per row in the index.

| Events per day | Index rows after 2 years | Index | Live files (90 days) | Archives (2 years, gzip about 1:8) |
|---|---|---|---|---|
| 1 000 (small site) | 0.73 M | about 0.9 GB | about 80 MB | about 80 MB |
| 10 000 (editorial site) | 7.3 M | about 9 GB | about 0.8 GB | about 0.8 GB |
| 100 000 (reads sampled on) | 73 M | keep reads out of the index (`IndexReads=disabled`) | 8 GB | 8 GB |

### 4.8 Upgrades

An installation made before 6.0.15:

1. **Index tables**: `php update/common/scripts/6.0/createaudittables.php` (every engine: MySQL/MariaDB,
   PostgreSQL, SQLite, Oracle, MongoDB). Tables that exist are left alone, and the script then indexes the files
   (`--no-index` skips that, `--dry-run` only reports). On the reference installation, where the tables exist:

   ```
   $ sudo -u <site user> php update/common/scripts/6.0/createaudittables.php --no-index
   Database: sqlite, missing tables: none
   the tables exist already
   Full-text search: fts5
   ```

   The SQL upgrade files `update/database/{mysql,postgresql,sqlite}/6.0/dbupdate-6.0.0-6.0.15.sql` contain the same
   statements.
2. **A 4.x override that switches the audit off.** 4.x shipped `Audit=disabled`, and many sites kept that in
   settings/override. Check where the value comes from:

   ```
   $ ./console exp:ini where audit.ini/AuditSettings/Audit --allow-root-user
   audit.ini/AuditSettings/Audit (load order of siteaccess site)
    1. settings/audit.ini                                           scope default
         Audit=enabled
   In effect:
         Audit=enabled
   ```

   Remove an old `Audit=disabled` line unless you mean it: `./console exp:ini rem audit.ini/AuditSettings/Audit global`.
3. **The old logs**: `exp:audit import --dry-run`, then `exp:audit import` (add `--keep-originals` to leave the
   files in place). See [3.3](#33-the-command-expaudit).
4. **Code**: `php bin/php/ezpgenerateautoloads.php -k`, clear caches, reload PHP-FPM, and restart Velocity (its
   workers record nothing from classes they have not loaded).
5. **Policies**: give `audit/read` (and `audit/manage`) to whoever should read the audit. Only Administrator has
   them.

### 4.9 Changing settings

Never edit `settings/audit.ini`. Put changes in `settings/override/audit.ini.append.php` (or a siteaccess's or an
extension's `audit.ini.append.php`), preferably with exp:ini, which records the write and keeps a backup. Preview
with `--dry-run`:

```
$ ./console exp:ini set audit.ini/AuditChannel_content/ArchiveDays 365 global --dry-run --allow-root-user
--- a/settings/override/audit.ini.append.php
+++ b/settings/override/audit.ini.append.php
@@ -6,4 +6,7 @@
 ActiveSigningKey=********
 SigningKey[k1-20261002-3fa94c1b]=********

+[AuditChannel_content]
+ArchiveDays=365
+
 */ ?>
Dry run: set audit.ini/AuditChannel_content/ArchiveDays in global, nothing written

$ ./console exp:ini get audit.ini/AuditChannel_content/ArchiveDays --allow-root-user
730
```

After a change: exp:ini clears the INI cache itself. After a hand edit, run
`php bin/php/ezcache.php --clear-tag=ini`. If a Velocity worker still shows the old value, restart Velocity. A new
class (a sink, a branch) always needs a Velocity restart. Every write to audit.ini is recorded
(`system.audit.setting.write`), and switching the audit off fires the `audit_disabled` alert.

### 4.10 Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| No Audit tab, no dashboard block | the user lacks `audit/read`; or Velocity's workers started before the audit classes existed | assign a role with `audit/read`; restart Velocity |
| Nothing is recorded at all | `Audit=disabled` somewhere (the dashboard says "Audit is switched off"; `system.audit.disable` was recorded) | `exp:ini where audit.ini/AuditSettings/Audit`, remove the override |
| Nothing recorded, `error.log` says "writing the audit channel …" or `AUDIT-UNWRITTEN` lines | LogDir not writable (owner, mode, full disk) | fix ownership (`chown -R <site user>: var/<site>/log/audit`, dirs 0770, files 0640) or free space. The `AUDIT-UNWRITTEN` lines hold the records that could not be written |
| Records from Apache but not from Velocity (:8080) | the workers predate the audit classes (every hook is guarded by `class_exists`) | restart Velocity |
| The console misses the newest events, or charts are empty | the index is behind (cronjob part not running) or its tables are missing | `exp:audit reindex --incremental`; `createaudittables.php`; check cron |
| Dashboard: "The audit cronjob part has not run for over an hour" | no crontab line, or the group that holds `audit.php` is not run | add `runcronjobs.php frequent` (or `audit`) to the crontab |
| `verify` says BROKEN | a file was changed after it was written (see the kind) | do not repair; follow [4.4](#44-verify-and-restore) |
| `verify` says `no_origin` for the oldest live file | its archived predecessor is not where the verifier looks (ArchiveDir changed, archives moved) | put the archives back under the configured ArchiveDir, or verify with `--archives` |
| `unknown_key` | a manifest or checkpoint is signed with a key id no longer in the settings | restore that `SigningKey[<id>]` from the settings backup |
| `repaired` | a process died in the middle of a write; the next write appended `system.audit.chain.repair` | nothing to do: the torn bytes stay in the file for inspection |
| An old day file is not archived | it is still its channel's newest file (nothing written to that channel since) | `exp:audit rotate`, or wait for the next write |
| `journalctl -t exponential` finds nothing | `Transport=devlog` on a journald that does not parse RFC 5424 headers on /dev/log | use `Transport=local` (journald's native socket) |
| The webhook spool grows | the receiver is down or refuses (`system.audit.sink.failed`, once per sink and hour) | `exp:audit sinks list` shows the last error; fix the receiver; `exp:audit sinks flush` |
| No alert mail | no recipient, mail transport in debug mode (`DebugSending`), throttled, or the cronjob part does not run | `exp:audit alerts recipients`; site.ini `[MailSettings]`; check cron |
| `search --name=…login*` exits 2: "a * may only be the whole pattern …" | a pattern is `*`, a prefix ending in `.*` or a whole name | `--name='access.session.login.*'` |
| `search --q=…` says "invalid option `--q'" | `-q` is the standard quiet option | `--query=…` |
| `--from` seems to miss records | a bare `--from`/`--to` is UTC, as printed (the console's bare times are site time) | give UTC, or add the offset: `--from=2026-10-03T01:30+02:00` |
| An admin form answers 503 "Not done: the audit cannot record it"; `AUDIT-REFUSED` in error.log | `OnWriteFailure=refuse` and LogDir cannot be written | fix LogDir as above; see [4.13](#413-when-the-audit-cannot-write) |
| "Verify now" asks for the password | `ReauthForManage=enabled` | enter it; it holds `ReauthMinutes` in that session |
| A settings change has no effect | INI cache | `php bin/php/ezcache.php --clear-tag=ini` (exp:ini does this itself) |
| The log directory grows fast | reads on, or a chatty extension name on | `exp:audit status` (today's counts per channel), `search --channel=… --limit=…`; switch the name off in `Disabled[]`; lower `SampleRate` |

### 4.11 Performance tuning

Measured cost on the reference installation (2 000 requests each, in the kernel; [9.3](#93-performance)): a page that records nothing
pays **0.08 ms** more with the audit on. A request that records one event pays about **0.6–0.8 ms** (one append).
Content jobs show no measurable difference. To go further:

- Keep reads off, or sample them low (`SampleRate=0.001`) and limit them to `Sections[]`/`Classes[]`.
- Switch off names you do not need: `[AuditEventSettings] Disabled[]=content.node.sort`. A name that is off costs
  one array lookup (0.001 ms).
- Long commands and workers: raise `MaxEvents`/`MaxBytes`/`FlushInterval` for fewer, larger appends.
- Keep `ImmediateEvents[]` to security events. Each one is its own append.
- Network sinks (webhook, udp/tcp/tls syslog) never slow a request: they are spooled and sent by the cronjob part.
  `Transport=local` writes to journald at flush (included in the measured 0.6 ms).
- Big indexes: `KeepDays` shorter than `ArchiveDays`, `IndexReads=disabled`, `BatchSize` up for faster catch-up.
- Put LogDir on local disk, and ArchiveDir elsewhere.

### 4.12 Privacy and GDPR tasks

(This describes what the audit does. It is not legal advice. Document it in your own record of processing.)

- **Lawful basis**: legitimate interest in the security and accountability of the system (Art. 6(1)(f)), and a legal
  obligation where a law requires access logging (Art. 6(1)(c)). Art. 32 names logging as a security measure.
- **Minimisation by default**: addresses truncated to /24 (IPv6 /48), sessions hashed (the session id is never
  recorded), user agents reduced to browser and OS family, e-mail addresses and attempted logins of unknown users
  hashed, no content values, never passwords, hashes, tokens, reset keys or secrets, no POST bodies, reads off.
- **Stricter**: per field `full | truncate | hash | off` in `[AuditPrivacySettings] Field[]`, `BeforeAfter=keys`, and
  shorter retention. See the [strict-privacy configuration](#64-strict-privacy). A hash is a keyed HMAC, so the same
  person still groups together (alerts by address keep working) but cannot be read back.
- **Retention**: 90 days live and 2 years archived, per channel, removed automatically, and each removal recorded.
- **Pseudonymisation**: the index replaces login, address, user agent and personal fields by their hashed form after
  `PseudonymiseAfterDays` (daily, recorded as `system.audit.pseudonymise`; preview with `exp:audit pseudonymise
  --dry-run`). The files are not rewritten (that would break the chain). The full values remain only in the
  archives, readable with `audit/manage`. A site that must not keep them even there uses `hash` from the start.
- **Access request (Art. 15)**: every record by or about a user, pseudonymised ones included:

  ```
  $ ./console exp:audit export --subject-user=14 --format=jsonl --out=var/tmp/audit-stage6/subject-14.jsonl --allow-root-user
  3625 record(s) exported as jsonl to var/tmp/audit-stage6/subject-14.jsonl (sha256 b82831ef24eeb594b8da40b21498fdefc0ba7f3368c3668997ec042cfa7ad283)
  ```

  Review the file before you hand it out: it holds records of other people's actions on that user too.
- **Erasure (Art. 17)**: audit records are kept under Art. 17(3)(b) and (e) for their retention period. Deleting one
  would break the chain. Answer with the retention period and the pseudonymisation already applied.

### 4.13 When the audit cannot write

`[AuditSettings] OnWriteFailure` decides what happens to an action whose record cannot be written (full disk,
permissions, a missing mount):

- **`continue`** (the shipped default): the action goes on. error.log gets the failure and, at the end of the
  request, one `AUDIT-UNWRITTEN <channel> <record>` line per record, so nothing is lost silently.
- **`refuse`**: the actions of the kind that is written at once (`[AuditBufferSettings] ImmediateEvents[]`:
  `access.*`, `system.audit.*`, `system.setting.write`) are refused while their channel cannot be written, so nothing
  security-relevant happens unrecorded. Each refusal is an `AUDIT-REFUSED` line in error.log, with the reason.

What `refuse` guards, and only this (`expAuditGuard`):

| Action | Event checked | Refused with |
|---|---|---|
| a POST by a signed-in user to a view of `[AuditReadSettings] AlwaysModules[]` (setup, role, user, audit, settings): role and policy changes, user administration, setup forms, the audit's "Verify now" | the access channel (`access.view.sensitive`), and for setup/settings the system channel (`system.setting.write`), for audit `system.audit.read` | HTTP 503 with the page `audit/refused.tpl` (it names the channel, never a path); the view does not run |
| an INI write through `expIniEditor` (`exp:ini`, the debug bar, the settings forms) | `system.setting.write` | `expIniException` "Refused: …", nothing written |
| `exp:audit rotate, archive, restore, purge, reindex, pseudonymise, import, export, checkpoint, key rotate` | its own `system.audit.*` name | the message and exit code 2; `--dry-run` is never refused |

Never refused, so the site stays usable: GET requests, anything an anonymous visitor does, pages, content editing,
the views in `[AuditSettings] RefuseExemptViews[]` (`user/login`, `user/logout`: people can still sign in and out;
their records go to error.log), and reading and diagnosing the audit (`exp:audit status`, `channels`, `tail`,
`search`, `verify`, the console's GET views).

"Cannot be written" is decided before the action by a probe of the event's channel that writes nothing (the
directory exists or can be created and is writable, the channel lock opens, the newest file can be appended to,
more than 1 MB is free), and by the request's own last write of that channel. With the audit switched off nothing is
refused (there is nothing to record). To leave `refuse` while the audit cannot write, fix LogDir, or edit
`settings/override/audit.ini.append.php` by hand: an `exp:ini` write would itself be refused as unrecorded.

Shown without touching the live settings, with the audit pointed at a log directory below a plain file
(`expAuditConfig::setOverride()` in one process):

```
OnWriteFailure=continue
  system.setting.write   allowed
  access.role.assign     allowed
  system.audit.archive   allowed
  content.node.move      allowed
OnWriteFailure=refuse
  system.setting.write   REFUSED (system: the directory var/tmp/audit-refuse-demo/blocker/log does not exist and cannot be created)
  access.role.assign     REFUSED (access: the directory var/tmp/audit-refuse-demo/blocker/log does not exist and cannot be created)
  system.audit.archive   REFUSED (system: the directory var/tmp/audit-refuse-demo/blocker/log does not exist and cannot be created)
  content.node.move      allowed
```

and in error.log:

```
AUDIT-REFUSED system.audit.archive (OnWriteFailure=refuse): the audit channel system cannot be written: the directory var/tmp/audit-refuse-demo/blocker/log does not exist and cannot be created
```

`content.node.move` is buffered, not written at once, so it is never refused. The tests (`expAuditFilterGuardReauthTest`
AG-06 to AG-08) prove the guard, the refused settings write and the refused admin POST the same way.

---

## 5. Internals

### 5.1 Event flow

```
 call site (kernel hook, extension, eZAudit::writeAudit, ezpEvent bridge, settings write)
   │  expAuditHook::emit( name, closure )  ──► off?  return (one array lookup, the closure never runs)
   ▼
 expAudit::event()
   │  expAuditConfig::get()          settings snapshot per request (keyed by REQUEST_TIME_FLOAT)
   │  expAuditTaxonomy::decide()     on? sampled? channel, severity, immediate? (compiled once per settings hash)
   │  parent / depth / child limits
   │  build(): request and actor context, ULID id, time ──► expAuditPrivacy::apply()   (privacy before anything is kept)
   ▼
 immediate?  ── yes ──► write now
   │ no
   ▼
 expAuditBuffer (per channel) ── full (MaxEvents/MaxBytes) or FlushInterval (CLI) ──► flush early (+ system.audit.overflow)
   │  end of request: eZExecution cleanup handler → expAudit::flushFinal()
   │  fatal error: flushOnFatal() (records system.error.fatal); last resort: shutdown function
   ▼
 expAuditWriter::append( channel, records )
   │  flock <LogDir>/.<channel>.lock, read the head from the file's last line (never from memory),
   │  torn last line? → system.audit.chain.repair; new UTC day? → file.close + new file with file.open;
   │  seq, prev, hash per record (canonical JSON), one fwrite, fflush, unlock
   ▼
 after the records are in the file:
   ├─ expAuditSinkRegistry::dispatch()   syslog local at once; webhook / network syslog / mail → spool
   ├─ expAuditAlertEvaluator::afterWrite()   only rules whose Event matches a written record touch their state
   ├─ key creation events, daily checkpoint (first write of a UTC day)
   ▼
 cronjob part (every run): index ◄── files, spools → sinks, alert windows; daily: rotate, verify, archive, purge,
                           pseudonymise, checkpoint
```

Writing never depends on the database, the network or a sink. The file is always written first, and every copy
follows it.

### 5.2 The record

One record is one line of canonical JSON. Fields whose value is null are left out:

| Field | Meaning |
|---|---|
| `v` | format version (1) |
| `id` | event id: a ULID, 26 characters, sortable by time |
| `seq` | position in the file: 1, 2, 3 … without gaps (line number = seq) |
| `name`, `channel`, `severity`, `verb` | taxonomy name, channel written to, RFC 5424 severity, the action |
| `time` | RFC 3339 UTC with milliseconds, when it happened |
| `request` | `id` (`r-<ULID>`, also the header `X-Exp-Request-Id`), `siteaccess`, `method` (`CLI` on the command line), `url` (truncated: query values and secret path parameters cut), `module`, `engine` (apache, velocity, frankenphp, cli), `host`, `pid`, `ms`, `status` (the exit code for a command) |
| `actor` | `user_id`, `login`, `roles`, `session` (`h:` + 16 hex, never the id), `ip` (a network), `ua` (family), `impersonator` (the process user when a content job acts as its owner), `cli` (OS user, command line with secrets masked) |
| `object`, `target` | `type`, `id` and identifying fields |
| `before`, `after` | the values that changed |
| `result`, `reason`, `error` | success, refused or failed; why |
| `parent`, `depth`, `job`, `run` | correlation |
| `x` | an extension's fields under its name |
| `imported`, `source` | imported 4.x records only (no `prev`/`hash`) |
| `prev`, `hash` | the chain |

**Canonical JSON** (what the hash covers): the record without `hash`, object keys sorted by their UTF-8 bytes at
every depth, no whitespace, `/` and non-ASCII characters not escaped, integers only (durations are integer ms,
money is a string). An empty object is `{}`. The line on disk *is* that form with `"hash"` in its sorted place, so a
line whose bytes are not canonical is itself a sign of editing (`noncanonical`).

A complete record from the reference installation (wrapped here; one line on disk; address and host replaced):

```json
{"actor":{"ip":"203.0.113.0/24","login":"admin","roles":[2],"session":"h:7617a963afc3ca9c","ua":"curl 7","user_id":14},
 "after":{"handler":"standard"},"channel":"access","depth":0,
 "hash":"sha256:949b036fdd6f0c59e0214ca1032d56a7a514a4f6b43f8189214b3774b8cc72ba","id":"01M3ZH49HXBMS8TY7HJCA7NWE0",
 "name":"access.session.login","object":{"id":14,"login":"admin","type":"user"},
 "prev":"sha256:4c3e5dc8417119194c764488576c7c66afe914cea1eb74119465a824edb376bf",
 "request":{"engine":"velocity","host":"web1","id":"r-01M3ZH48ZGGSYK3N7NR3Z2F3JX","method":"POST","module":"user/login",
            "ms":630,"pid":1322013,"siteaccess":"admin","status":200,"url":"/admin/user/login"},
 "result":"success","seq":26,"severity":"info","time":"2026-10-03T00:02:54.653Z","v":1,"verb":"login"}
```

### 5.3 The hash chain and what verification finds

- `hash = "sha256:" + hex( SHA-256( canonical( record without hash ) ) )`. `prev` is the previous record's hash in the
  same channel, across all its files.
- The first file of a channel starts from the **genesis** value
  `sha256( "exponential-audit:" + installation id + ":" + channel )`, so a chain cannot be passed off as another
  installation's or channel's.
- Each file starts with `system.audit.file.open` (seq 1, naming the previous file, its last seq and hash) and a
  closed file ends with `system.audit.file.close`.
- **Checkpoints**: once a day, and on `exp:audit checkpoint`, `system.audit.checkpoint` in the system channel holds
  every channel's current file, seq and hash, signed with HMAC-SHA-256 and the active key, and goes to the sinks. A
  chain recomputed after the fact no longer matches a checkpoint signed before.
- **Manifests**: each archived day has a manifest (files, counts, first and last seq and hash, sha256 of the plain
  and the compressed file, the verification result, `previous_manifest`, `key_id`, `hmac`). Manifests of a channel
  are chained, so removing a whole archived day shows too.

What `verify` reports:

| Kind | Means |
|---|---|
| `altered` | the line's content does not give its hash: the record was changed |
| `noncanonical` | the line is not in canonical form: edited by hand or by a tool |
| `link` | `prev` is not the previous record's hash: a line was removed or inserted before it |
| `gap` / `reordered` | seq jumps forward / goes back: lines removed / swapped |
| `unparseable` | not JSON |
| `missing_file` | a file between two others is gone |
| `truncated` | the next file's open record names a different end than the previous file has: lines were cut off, or the previous file was rewritten |
| `no_origin` | the first file starts neither from genesis nor from an archived file |
| `rewritten` | a signed checkpoint recorded another hash at that seq: the chain was recomputed |
| `checkpoint_invalid`, `unknown_key` | a checkpoint whose HMAC does not verify, or whose key is not in the settings |
| archives: `archive_sha256`, `hmac_invalid`, `previous_manifest` | an archive changed, a manifest edited, an archived day removed |
| `repaired` (not a break) | a torn last line was followed by `system.audit.chain.repair` |

The verifier keeps going after a break (using the record's own `prev`), so a report lists every damaged stretch.
[9.2](#92-tamper-test) shows each kind produced on a copy of the reference installation's real files.

### 5.4 Taxonomy and registries

- A name is `domain.subject.action[.detail[.detail]]`. Settings use patterns: a whole name, or a prefix ending in
  `.*` that also matches the prefix itself (`content.node.remove.*` matches `content.node.remove` and
  `content.node.remove.trash`).
- `decide()`: an `always` name is always on. A `sampled` name is on only with `Reads=enabled` and then sampled at
  `SampleRate`. Otherwise the most specific of `Enabled[]`/`Disabled[]` wins, and on a tie `Disabled[]` wins. With
  no match the registry default applies. `MinSeverity` drops lower severities. The answers are compiled once per
  settings hash and kept for the life of the process.
- Channel: an exact `Route[]` entry, then the branch's own channel, then the most specific `Route[]` pattern, then
  `DefaultChannel`. Only channels listed in `Channels[]` are used.
- The four registries, which the RAD survey (Setup › RAD) counts and checks:

| Registry | Setting | On the reference installation |
|---|---|---|
| `auditbranches` | `[AuditEventSettings] Branches[]` | 0 (none registered) |
| `auditsinks` | `[AuditSinkSettings] SinkClasses[]` | syslog, webhook, mail; 0 broken |
| `auditalertrules` | `[AuditAlertSettings] RuleClasses[]` | threshold, match, schedule; 0 broken |
| `auditformats` | `[AuditArchiveSettings] FormatHandlers[]` | gzip, bzip2, xz, zstd, zip; 0 broken |

### 5.5 Buffering and flushing

Events are collected per channel and written in one append per channel at the end of the request, through the
`eZExecution` cleanup handler that `ezpKernelWeb::shutdown()` and `eZScript::shutdown()` run. A fatal error is caught
by `flushOnFatal()`, which also records `system.error.fatal`, and a shutdown function is the last resort.
`ImmediateEvents[]` (access.*, system.audit.*, system.setting.write) are written at once, so a security event is in
the file even if the request then dies. Past `MaxEvents`/`MaxBytes` the buffer flushes early and records
`system.audit.overflow` once. Commands, cronjobs and content job workers also flush every `FlushInterval` seconds.
A failed append keeps the records for one more try at the end. If that fails too, each record becomes an
`AUDIT-UNWRITTEN <channel> <json>` line in error.log, so no record is lost silently.

### 5.6 The index tables

`expaudit_event`: one row per record (`id`, `channel`, `seq`, `file_name`, `name`, `domain_name`, `severity`
0–7, `time_ms`, `request_id`, `siteaccess`, `engine`, `module_view`, `user_id`, `login`, `ip`, `session_h`, `ua`,
`verb`, `object_type/id/name`, `target_type/id`, `result`, `reason`, `parent_id`, `depth`, `job_id`, `run_id`,
`imported`, `pseudonymised`, `record`, `search_text`), with indexes on time, name, user, object, request, job,
parent, address, result and domain/severity. `expaudit_cursor`: per channel and file, the byte offset, last seq and
hash already indexed. `expaudit_file`: each file's state and verification. The schema is in
`share/db_schema.dba` and `kernel/sql/<engine>/`. Oracle and MongoDB get it from the same definition through
`expAuditIndexSchema::install()`. Table names start with `exp`, and the drivers' `relationList()` only lists `ez*`,
so `missingTables()` asks each engine directly.

### 5.7 Sinks

The file is always written. Sinks are copies, delivered after the record is in the file, and fed only records that
passed the privacy rules.

- **syslog** (`expAuditSyslogSink`): RFC 5424, one message per record, `MSGID` = channel, structured data
  `exp@32473` (the documentation enterprise number of RFC 5612) with id, name, seq, channel, user, ip, result,
  request and hash, then the JSON record. `Transport=local` writes to journald's native socket where journald runs
  (identifier `AppName`, fields `EXP_AUDIT_ID/NAME/CHANNEL/SEQ/HASH/RESULT/SEVERITY/REQUEST`), else to `/dev/log`.
  `devlog` sends the RFC 5424 line to `/dev/log`. `udp` sends header and structured data only (RFC 5426). `tcp` and
  `tls` send the whole record with octet counting (RFC 6587/5425). Local delivery happens at flush; network
  transports go through the spool.
- **webhook** (`expAuditWebhookSink`): `POST` JSON `{"v":1,"installation":…,"site":…,"batch":…,"events":[…]}` in
  batches of `BatchSize` or `BatchSeconds`, with headers `X-Exponential-Timestamp`, `X-Exponential-Batch` and
  `X-Exponential-Signature: sha256=<hex HMAC-SHA-256( SigningSecret, timestamp + "." + body )>`. Receivers check the
  signature, refuse timestamps more than 300 s away (`expAuditWebhookSink::verify()` does both), and de-duplicate by
  event id (delivery is at least once). Retries: `Retries` times with `RetryBackoff` doubled each time. After the
  last one the batch stays spooled and `system.audit.sink.failed` is recorded.
- **mail** (`expAuditMailSink`): from the cronjob part only, through the kernel's mail transport (or
  `[AuditSink_mail] Transport=<class>`), to the recipients of [4.5](#45-alerts-and-their-recipients).
- **Spools**: `<LogDir>/spool/<sink>.jsonl` under `flock()`, delivered by the cronjob part and by
  `exp:audit sinks flush`. Critical records are also tried right after the response where PHP-FPM allows it.

### 5.8 Alert evaluation

At flush, after the write, only rules whose `Event` pattern matches a written record are evaluated, so a request
without failed logins or role changes pays nothing. `match` and `schedule` rules decide at once. `threshold` rules
keep their window in `<LogDir>/alerts/<rule>.json` (per group the event ids and times inside the window) under
`flock()`. The cronjob part evaluates records since its cursor, closes windows, and checks the `.state` file
against the settings: a change made by editing a file by hand still produces `system.audit.disable` and the
`audit_disabled` alert. An alert for (rule, group) fires once per window and again when the count doubles. Each
firing is `system.audit.alert` (rule, group, count, window, first and last event, sinks, message) with the rule's
severity.

### 5.9 Velocity

Velocity's workers are persistent, so several things work differently from PHP-FPM:

- `ezpKernelWeb::__construct()` calls `expAudit::resetRequest()`: anything a previous request left is flushed, then
  the buffer, open parents, request id, job, run and cached actors are cleared. A changed `REQUEST_TIME_FLOAT` also
  resets the per-request settings snapshot. The cleanup handler, not the shutdown function, flushes each request.
- The 4.x `$GLOBALS` caches of `eZAudit` are gone, so a changed audit.ini is seen on the next request.
- Every hook point checks `class_exists( 'expAuditHook' )`, and templates fetch through a variable module name. A
  worker started before the audit classes existed records nothing and shows nothing until `exp:velocity restart`.
- The bridge's listeners are attached once per worker (a second attach replaces the first), so an event is never
  recorded twice.
- 1 000 requests in one worker were tested: no record carried another request's id.

### 5.10 Failure modes

| What fails | What happens |
|---|---|
| LogDir cannot be written | `OnWriteFailure=continue` (default): the request carries on; error.log gets the failure and, at the end, one `AUDIT-UNWRITTEN` line per record. `refuse`: the guarded actions are refused first ([4.13](#413-when-the-audit-cannot-write)) |
| A process dies mid-write | the next append repairs the torn line (`system.audit.chain.repair`); verify says `repaired` |
| The database is down | nothing changes at request time (the index is not written then); the cronjob part's index run fails and catches up later |
| A sink is down | records wait in the spool; `system.audit.sink.failed` once per sink and hour; delivered when it is back |
| The cronjob part stops | files keep being written; index, spools, archives, checkpoints wait; the dashboard warns after an hour |
| The key file is lost | new keys are generated on the next event (`system.audit.key.create`); archives and checkpoints of the old keys report `unknown_key` until the old `SigningKey[]` is restored |
| An extension's branch class is missing | its names are left out (not recorded), the RAD survey and the settings view say why |
| The audit classes are missing (old worker) | nothing is recorded by that process; nothing breaks |
| Audit is switched off | `system.audit.disable` is recorded first; the `audit_disabled` alert (emergency) goes to syslog and mail |

### 5.11 Security

| Who | Can | The answer |
|---|---|---|
| An editor without audit policies | see nothing | policies on every view, link, block and fetch; refusals recorded |
| Someone using a signed-in session left open | run audit manage actions | `ReauthForManage=enabled` asks for the password first; the attempts are recorded |
| A user with `audit/read` | read the channels the limitation allows, export them | reads and exports are recorded; personal fields truncated or hashed by default |
| An administrator with `audit/manage` | change settings, switch the audit off, rotate keys | each is recorded before it takes effect; `audit_disabled` alert to syslog and mail; the record leaves the server through the sinks |
| Code running as the web server user | stop recording, read the key, rewrite live files and recompute the chain | it cannot rewrite what already left the server (syslog, webhook, mail, checkpoints); archives on a path it cannot change; checkpoints signed before the compromise expose the rewrite (`rewritten`) |
| root or the hosting provider | anything on the server | out of scope: only copies held elsewhere (sinks) can show what happened |
| Someone on the network | — | syslog over `tls`, webhook over HTTPS with signatures |

**What the chain proves**: the records of a channel, from its genesis or the last verified checkpoint or manifest
up to the point checked, are the ones that were written, in that order, with none removed, inserted or changed,
**unless** someone who could write the files recomputed the chain after the last signed checkpoint. It does not
prove that every event was recorded (code that never calls the audit leaves no break), that a record's content is
true, or who changed a file. The HMAC adds that only a key holder could have signed a checkpoint or manifest. A key
read by an attacker weakens what is signed after that moment, not what was signed and sent away before. Send
checkpoints off the server (syslog or webhook) to make that hold. See the
[SIEM](#65-siem-forwarding) and [compliance](#66-compliance-long-retention) configurations.

---

## 6. Reference configurations

Each block below is a complete `settings/override/audit.ini.append.php` body (the file starts with
`<?php /* #?ini charset="utf-8"?` and ends with `*/ ?>`). Each was validated on the reference installation by
a script that reads them out of this guide. For each one
it builds a temporary INI root with the shipped `settings/audit.ini` and the block as its append file, lets eZINI
merge them, and gives the result to the audit in a throwaway directory. It then asks every part whether it can work:
the taxonomy, the event decisions, each channel's days, size and format handler, the sinks the channels use, the
alert rules, the privacy rules (on a sample record) and the index settings. The live settings were not changed. The
validator's output for each configuration is shown under it.

### 6.1 Small site

```ini
# Reference configuration: small-site
# One server, a handful of editors. The shipped defaults stay; only who is mailed and
# how long archives are kept change. File: settings/override/audit.ini.append.php
[AuditAlertSettings]
Recipients[]
Recipients[]=admin
Recipients[]=role:Administrator

# One year of archives instead of two. The shipped channel blocks set ArchiveDays
# themselves, so it is set per channel ([AuditRotationSettings] would not be read).
[AuditChannel_content]
ArchiveDays=365

[AuditChannel_access]
ArchiveDays=365

[AuditChannel_system]
ArchiveDays=365

[AuditChannel_commerce]
ArchiveDays=365
```

```
== small-site
  audit enabled; LogDir log/audit; ArchiveDir log/audit/archive
  events recorded: 109 of 135 (as shipped)
  channel content   live   90 d, archived  365 d, gzip, max 64M, sinks -
  channel access    live   90 d, archived  365 d, gzip, max 64M, sinks syslog
  channel system    live   90 d, archived  365 d, gzip, max 64M, sinks syslog
  channel commerce  live   90 d, archived  365 d, gzip, max 64M, sinks -
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels access, system)
  sink webhook  not ready: no URL is set ([AuditSink_webhook] URL)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 730 d, pseudonymise after 90 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

With it: a crontab line for `runcronjobs.php frequent`, and a backup of settings/override.

### 6.2 Busy site

```ini
# Reference configuration: busy-site
# An editorial site with many publishes per hour on one or two servers.
# File: settings/override/audit.ini.append.php
[AuditEventSettings]
# "Who published this?" is the question asked most; the shipped default leaves it off.
Enabled[]=content.object.create
Enabled[]=content.object.publish

[AuditBufferSettings]
# Long requests and commands write fewer, larger appends.
MaxEvents=2000
MaxBytes=4M
FlushInterval=10

[AuditChannel_content]
MaxFileSize=256M
ArchiveFormat=zstd

[AuditChannel_system]
MaxFileSize=256M
ArchiveFormat=zstd

[AuditArchiveSettings]
# Another file system: create it first, owned by the site user, mode 0750.
ArchiveDir=/srv/audit-archive/example

[AuditIndexSettings]
BatchSize=10000
# The console searches one year; older years are in the archives.
KeepDays=365

[AlertRule_mass_delete]
# Editors clean up large sections here; 500 nodes in 10 minutes is normal work.
Threshold=2000
```

```
== busy-site
  audit enabled; LogDir log/audit; ArchiveDir /srv/audit-archive/example
  events recorded: 111 of 135 (against the shipped defaults: +content.object.create +content.object.publish)
  channel content   live   90 d, archived  730 d, zstd, max 256M, sinks -
  channel access    live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel system    live   90 d, archived  730 d, zstd, max 256M, sinks syslog
  channel commerce  live   90 d, archived  730 d, gzip, max 64M, sinks -
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels access, system)
  sink webhook  not ready: no URL is set ([AuditSink_webhook] URL)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 365 d, pseudonymise after 90 d; buffer 2000 events / 4194304 bytes, flush every 10 s
  PASS loads and every part can work with it
```

Before switching it on: `mkdir -p /srv/audit-archive/example && chown <site user>: /srv/audit-archive/example && chmod
0750 /srv/audit-archive/example`. Run `exp:audit alerts test mass_delete --replay=<a month ago>` to check that the
new threshold fits.

### 6.3 Cluster

```ini
# Reference configuration: cluster
# Several web servers behind a load balancer, one database. Every server writes into
# one shared directory, so each channel stays one chain. The file system must support
# POSIX locks across servers (NFSv4, CephFS, GlusterFS); the writer takes flock() on
# every append. settings/override is shared or copied identically to every server.
[AuditSettings]
LogDir=/mnt/exponential-shared/audit

[AuditArchiveSettings]
ArchiveDir=/mnt/exponential-archive/audit

[AuditPrivacySettings]
# The server's host name tells which server answered (request.host).
Field[request.host]=full

[AuditSink_syslog]
# Each server forwards its own records to the central collector.
Transport=tcp
Host=logs.example.com
Port=514

[AuditChannel_content]
Sinks[]=syslog

[AuditChannel_commerce]
Sinks[]=syslog
```

```
== cluster
  audit enabled; LogDir /mnt/exponential-shared/audit; ArchiveDir /mnt/exponential-archive/audit
  events recorded: 109 of 135 (as shipped)
  channel content   live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel access    live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel system    live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel commerce  live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels content, access, system, commerce)
  sink webhook  not ready: no URL is set ([AuditSink_webhook] URL)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 730 d, pseudonymise after 90 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

How it works across servers: every lock file (`.<channel>.lock`, `.keys.lock`, `.cron.lock`, `.index.lock`) and the
daily marker live in the shared LogDir. The chain head is read from the file under the lock at every append, never
from memory. So any number of servers write one chain per channel, and the cronjob part may run on all of them: one
run at a time does the work, and the daily tasks run once a day for the cluster. **Generate the keys once before the
other servers record** (`./console exp:audit checkpoint` on one server), then share or copy
`settings/override/audit.ini.append.php` to every server. Two servers with different keys would write different
genesis values and fail verification. What was checked on the reference installation: the configuration loads and every part accepts it,
and ten concurrent writers to one channel keep the chain intact (test B2). A multi-server cluster itself was not
available to test.

### 6.4 Strict privacy

```ini
# Reference configuration: strict-privacy
# A site that must keep as little personal data as possible, also in its archives.
# File: settings/override/audit.ini.append.php
[AuditPrivacySettings]
Field[actor.login]=hash
Field[actor.ip]=hash
Field[actor.ua]=off
Field[actor.cli.os_user]=hash
Field[request.url]=truncate
Field[request.host]=hash
Field[object.name]=truncate
PseudonymiseAfterDays=30

[AuditRecordSettings]
# Which fields changed, not their values.
BeforeAfter=keys

[AuditChannel_content]
LiveDays=30
ArchiveDays=180

[AuditChannel_access]
LiveDays=30
ArchiveDays=180

[AuditChannel_system]
LiveDays=30
ArchiveDays=180

[AuditChannel_commerce]
LiveDays=30
ArchiveDays=180

[AuditIndexSettings]
KeepDays=180
```

```
== strict-privacy
  audit enabled; LogDir log/audit; ArchiveDir log/audit/archive
  events recorded: 109 of 135 (as shipped)
  channel content   live   30 d, archived  180 d, gzip, max 64M, sinks -
  channel access    live   30 d, archived  180 d, gzip, max 64M, sinks syslog
  channel system    live   30 d, archived  180 d, gzip, max 64M, sinks syslog
  channel commerce  live   30 d, archived  180 d, gzip, max 64M, sinks -
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels access, system)
  sink webhook  not ready: no URL is set ([AuditSink_webhook] URL)
  sink mail     ready
  alert rules: 7, all usable
  sample record: login h:a7b9fa3a5c1c2d01, ip h:b1c47ab3fda84e2d, ua (off), url /user/login?redirect=…, host h:50f7904f8d21e9e6, object.name "A very long article title that goes on and on well past sixty-fo…", before {"parent":"sha256:d4735e3a265e16eee03f59718b9b5d03019c07d8b6c51f90da3a666eec13ab35"}
  index on, keep 180 d, pseudonymise after 30 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

The sample record line shows what is written: logins, addresses and hosts as keyed hashes, no user agent, names cut
at 64 characters, and before/after values as their sha256. Grouping still works: the brute-force rules group by the
hashed address. With the shipped defaults the same sample reads `login editor1, ip 203.0.113.0/24, ua Firefox 131 /
Linux`.

### 6.5 SIEM forwarding

```ini
# Reference configuration: siem-forwarding
# Every record leaves the server at once to a SIEM: syslog over TLS for the stream,
# a signed webhook for the security events. File: settings/override/audit.ini.append.php
[AuditSink_syslog]
Transport=tls
Host=siem.example.com
Port=6514
Facility=local4
MinSeverity=info

[AuditSink_webhook]
URL=https://siem.example.com/api/ingest/exponential
# A long random secret (openssl rand -hex 32); the receiver checks X-Exponential-Signature.
SigningSecret=3f9a0c12b47d5e6f8a1b2c3d4e5f60718293a4b5c6d7e8f90a1b2c3d4e5f6071
BatchSize=200
MinSeverity=notice
Events[]
Events[]=access.*
Events[]=system.audit.*
Events[]=system.setting.*

[AuditChannel_content]
Sinks[]=syslog

[AuditChannel_access]
Sinks[]=webhook

[AuditChannel_system]
Sinks[]=webhook

[AuditChannel_commerce]
Sinks[]=syslog
```

```
== siem-forwarding
  audit enabled; LogDir log/audit; ArchiveDir log/audit/archive
  events recorded: 109 of 135 (as shipped)
  channel content   live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel access    live   90 d, archived  730 d, gzip, max 64M, sinks syslog, webhook
  channel system    live   90 d, archived  730 d, gzip, max 64M, sinks syslog, webhook
  channel commerce  live   90 d, archived  730 d, gzip, max 64M, sinks syslog
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels content, access, system, commerce)
  sink webhook  ready (channels access, system)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 730 d, pseudonymise after 90 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

`Sinks[]=…` lines append to the shipped lists, so access and system keep their shipped `syslog` entry. Replace the
example secret with your own (exp:ini masks it, and settings/override is never committed). Then
`exp:audit sinks test syslog` and `exp:audit sinks test webhook` send one test record each, and the receiver must
answer 2xx. With TLS, PHP verifies the collector's certificate against the system CA store. The receiver's check in
PHP is `expAuditWebhookSink::verify( $body, $headers, $secret )`.

### 6.6 Compliance: long retention

```ini
# Reference configuration: compliance-long-retention
# Ten years of evidence, kept compressed on write-once storage, with the daily signed
# checkpoints sent off the server. File: settings/override/audit.ini.append.php
[AuditEventSettings]
Enabled[]=content.object.create
Enabled[]=content.object.publish
Enabled[]=commerce.order.create
Enabled[]=commerce.order.status
Enabled[]=commerce.payment.approve
Enabled[]=data.export.pdf
Enabled[]=data.infocollection.view

[AuditChannel_content]
ArchiveDays=3650
ArchiveFormat=xz

[AuditChannel_access]
ArchiveDays=3650
ArchiveFormat=xz

[AuditChannel_system]
ArchiveDays=3650
ArchiveFormat=xz
Sinks[]=webhook

[AuditChannel_commerce]
ArchiveDays=3650
ArchiveFormat=xz

[AuditArchiveSettings]
# A mount the web server can add files to but not change (WORM, or a backup target).
ArchiveDir=/srv/worm/exponential-audit

[AuditIndexSettings]
# Two years searchable in the console; earlier years: exp:audit restore, then read.
KeepDays=730

[AuditSink_webhook]
URL=https://evidence.example.com/exponential/checkpoints
SigningSecret=9b1c0f7e2d4a6c8e0f1a3b5c7d9e1f2a4b6c8d0e2f4a6b8c0d2e4f6a8b0c2d4e
MinSeverity=info
Events[]
Events[]=system.audit.checkpoint
Events[]=system.audit.chain.broken
Events[]=system.audit.purge
Events[]=system.audit.key.rotate
```

```
== compliance-long-retention
  audit enabled; LogDir log/audit; ArchiveDir /srv/worm/exponential-audit
  events recorded: 116 of 135 (against the shipped defaults: +content.object.create +content.object.publish +commerce.order.create +commerce.order.status +commerce.payment.approve +data.export.pdf +data.infocollection.view)
  channel content   live   90 d, archived 3650 d, xz, max 64M, sinks -
  channel access    live   90 d, archived 3650 d, xz, max 64M, sinks syslog
  channel system    live   90 d, archived 3650 d, xz, max 64M, sinks syslog, webhook
  channel commerce  live   90 d, archived 3650 d, xz, max 64M, sinks -
  channel read      live   30 d, archived   90 d, zstd, max 256M, sinks -
  sink syslog   ready (channels access, system)
  sink webhook  ready (channels system)
  sink mail     ready
  alert rules: 7, all usable
  index on, keep 730 d, pseudonymise after 90 d; buffer 500 events / 1048576 bytes, flush every 5 s
  PASS loads and every part can work with it
```

Keep every `SigningKey[]` for as long as the archives it signed (rotation adds keys and never removes them). Store the
settings backup with the same retention. Run `exp:audit verify --archives` monthly and keep its output.

---

## 7. Settings reference

`settings/audit.ini` is the complete, commented reference: every variable with its default and allowed values. This
section summarises it. Change values in an override, never in that file ([4.9](#49-changing-settings)).

| Block | Variable (default) | Meaning |
|---|---|---|
| `[AuditSettings]` | `Audit` (enabled) | on or off; switching it off is recorded first |
| | `LogDir` (log/audit) | live files, relative to the var directory, or absolute |
| | `AuditFileNames[]` | the 4.x file names, as aliases for `(legacy_file)` filters and the import |
| | `OnWriteFailure` (continue) | `continue`: a failed write never stops the request; `refuse`: the actions of ImmediateEvents[] are refused while their channel cannot be written ([4.13](#413-when-the-audit-cannot-write)) |
| | `RefuseExemptViews[]` (user/login, user/logout) | POSTs `refuse` never refuses |
| `[AuditEventSettings]` | `Enabled[]`, `Disabled[]` | patterns; the most specific wins, `Disabled[]` on a tie |
| | `Branches[<ext>]` | extension taxonomy branches |
| | `MinSeverity` (info) | lower severities are not recorded |
| `[AuditChannelSettings]` | `Channels[]`, `Route[<pattern>]`, `DefaultChannel` (system) | channels and routing |
| `[AuditChannel_<c>]` | `LiveDays`, `ArchiveDays`, `MaxFileSize`, `ArchiveFormat`, `Sinks[]` | per channel; see the shipped values in [4.3](#43-retention) |
| `[AuditRecordSettings]` | `BeforeAfter` (enabled \| keys \| disabled), `MaxValueLength` (512), `ChildDepth` (3), `MaxChildren` (10000), `RequestContext`, `RequestIdHeader` (X-Exp-Request-Id), `TrustedRequestIdHeader` (empty; trusted from loopback only), `MaxLineBytes` (262144) | what a record holds |
| `[AuditPrivacySettings]` | `Field[<field>]` (full \| truncate \| hash \| off), `IPv4Prefix` (24), `IPv6Prefix` (48), `SecretPathViews[]`, `NeverRecord[]`, `PseudonymiseAfterDays` (90) | privacy; `actor.session` takes only hash or off |
| `[AuditBufferSettings]` | `Buffering`, `ImmediateEvents[]`, `MaxEvents` (500), `MaxBytes` (1M), `FlushInterval` (5) | [5.5](#55-buffering-and-flushing) |
| `[AuditChainSettings]` | `Algorithm` (sha256), `Checkpoints` (enabled) | the chain is always on |
| `[AuditKeySettings]` | `GenerateKeys` (enabled); generated: `InstallationID`, `ActiveSigningKey`, `SigningKey[]`, `PseudonymKey` | [4.6](#46-keys) |
| `[AuditReadSettings]` | `Reads` (disabled), `SampleRate` (0.01), `Sections[]`, `Classes[]`, `AlwaysModules[]` (setup, role, user, audit, settings) | sampled reads; views of `AlwaysModules[]` are always `access.view.sensitive` for signed-in users |
| `[AuditSinkSettings]` | `SinkClasses[]`, `SpoolDir` (log/audit/spool) | [5.7](#57-sinks) |
| `[AuditSink_syslog]` | `Transport` (local), `Host`, `Port` (514), `Facility` (authpriv), `AppName` (exponential), `Events[]`, `MinSeverity` (info) | |
| `[AuditSink_webhook]` | `URL`, `SigningSecret`, `BatchSize` (100), `BatchSeconds` (10), `Timeout` (5), `Retries` (5), `RetryBackoff` (30), `Events[]`, `MinSeverity` (notice) | |
| `[AuditSink_mail]` | `Receivers[]` (older name of the default recipients), `MinSeverity` (critical), `Events[]`, `Throttle` (900), `Transport` | |
| `[AuditAlertSettings]` | `Alerts`, `EvaluateIn[]` (flush, cronjob), `Rules[]`, `RuleClasses[]`, `BusinessDays` (1-5), `BusinessHours` (7-19), `Recipients[]` | [4.5](#45-alerts-and-their-recipients) |
| `[AlertRecipients_<name>]` | `Addresses[]`, `Recipients[]` | named recipient groups |
| `[AlertRule_<name>]` | `Class`, `Event`, `Threshold`, `Window`, `GroupBy`, `Severity`, `Sinks[]`, `Recipients[]`, `CountChildren`, `Policies[]`, `OutOfHours` | the seven shipped rules |
| `[AuditRotationSettings]` | defaults for the channel blocks; `VerifyBeforeArchive`, `VerifyAfterArchive`, `RotateAfter` (00:15) | |
| `[AuditArchiveSettings]` | `ArchiveDir`, `FormatHandlers[]`, `Level[]`, `FileMode` (0440), `DirMode` (0750) | |
| `[AuditIndexSettings]` | `Index`, `BatchSize` (2000), `IndexReads` (disabled), `FullText`, `KeepDays` (730) | |
| `[AuditConsoleSettings]` | `PageSize` (50), `MaxExportRecords` (100000), `ReauthForManage` (disabled), `ReauthMinutes` (10) | the password again before manage actions ([3.1](#31-who-may-see-what-the-policies)) |
| `[AuditCompatSettings]` | `Map[]`, `UnmappedAsLegacy`, `LegacyFiles` (disabled: also write the 4.x text files) | |
| `[AuditBridgeSettings]` | `Bridge[<ezpEvent>]=<name>` | [3.5](#35-the-developer-api) |

Related settings elsewhere: `cronjob.ini [CronjobPart-audit]` and the `frequent` group; `menu.ini [Topmenu_audit]`;
`dashboard.ini [DashboardBlock_audit]`; `admininterface.ini [AdditionalTab_audit]`; `module.ini ModuleList[]=audit`.

---

## 8. Event reference

Generated from `expAuditTaxonomy::registry()` and the settings in effect on the reference installation by
a generator script. **Raised in** comes from the code: the call sites
that raise the name. **On here** is what `decide()` answers with the reference installation's settings. **Written** says whether the event
is appended at once or with the request's buffer. The two descriptive columns are kept with their names by the
generator. Columns: Sev. = severity; Default = shipped (`always` cannot be switched off; `sampled` = a read, recorded
only with `Reads=enabled`).

<!-- event-reference:start (generated from expAuditTaxonomy::registry() and the settings in effect on 2026-10-03; do not edit by hand) -->
135 names (42 content, 32 access, 40 system, 12 commerce, 9 data); shipped default: 86 on, 23 off, 23 always, 3 sampled.

| Event | Fires when | Records (before → after; never) | Raised in | Verb | Sev. | Default | On here | Ch. | Written |
|---|---|---|---|---|---|---|---|---|---|
| `content.object.create` | the first version of a new object is published | – → class, languages, section, owner (never attribute values) | `content/ezcontentoperationcollection.php:auditPublished`, `views/content/restore.php:run` | publish | info | off | no | content | buffered |
| `content.object.publish` | a later version is published | version, languages, modified → version, languages, modified, changed attribute identifiers (never attribute values) | `content/ezcontentoperationcollection.php:auditPublished`, `views/content/restore.php:run` | publish | info | off | no | content | buffered |
| `content.object.translate` | a published version adds a language the previous one did not have | languages → languages | `content/ezcontentoperationcollection.php:auditPublished`, `views/content/restore.php:run` | translate | info | off | no | content | buffered |
| `content.object.translation.remove` | a translation is removed from an object | languages → languages | `content/ezcontentoperationcollection.php:removeTranslation` | remove | notice | on | yes | content | buffered |
| `content.version.remove` | archived or draft versions are removed by an editor | status, language → – | `views/content/history.php:run`, `views/content/removeeditversion.php:run` | remove | notice | on | yes | content | buffered |
| `content.node.move` | a location gets a new parent `[content-move]` | parent, path → parent, path | `ezcontentobjecttreenode.php:move` | move | info | on | yes | content | buffered |
| `content.node.copy` | a node or subtree is copied | – → new node id, new object id | `contentjob/expcontentjobcopysubtree.php:copyNode`, `ezcontentobjecttreenodeoperations.php:auditSubtreeCopy`, `views/content/copy.php:copyObject` | copy | info | off | no | content | buffered |
| `content.node.add` | a location is added to an object | locations → locations | `content/ezcontentoperationcollection.php:addAssignment` | add | info | off | no | content | buffered |
| `content.node.remove` | a location is removed, the object stays | parent, path, object id, name → – | `ezcontentobjecttreenode.php:removeSubtrees`, `ezcontentobjecttreenode.php:removeNodeFromTree`, `content/ezcontentoperationcollection.php:removeNodes` | remove | notice | on | yes | content | buffered |
| `content.node.remove.trash` | an object goes to the trash `[content-delete]` | parent, path, object id, name, class → – | `ezcontentobject.php:removeThis`, `ezcontentobjecttreenode.php:removeSubtrees`, `ezcontentobjecttreenode.php:removeNodeFromTree`, `ezcontentobjecttreenode.php:auditRemovalData` | remove | notice | on | yes | content | buffered |
| `content.object.remove` | an object is removed for good, not through the trash `[content-delete]` | name, class, owner, locations → – | `ezcontentobjecttreenode.php:removeSubtrees`, `ezcontentobjecttreenode.php:removeNodeFromTree`, `ezcontentobjecttreenode.php:auditRemovalData` | remove | notice | on | yes | content | buffered |
| `content.object.purge` | an object is purged (trash emptied, version purge) `[content-delete]` | name, class → – | `ezcontentobject.php:purge`, `ezcontentobjecttreenode.php:removeNodeFromTree` | purge | notice | on | yes | content | buffered |
| `content.object.restore` | an object is restored from the trash | – → node, parent | `views/content/restore.php:run` | restore | info | on | yes | content | buffered |
| `content.trash.empty` | the trash is emptied, or a selection purged | count → count | `services/trash.php:audited` | purge | notice | on | yes | content | buffered |
| `content.node.hide` | a subtree is hidden `[content-hide]` | visibility → visibility | `contentjob/expcontentjobhidesubtree.php:mainStep`, `ezcontentobjecttreenode.php:hideSubTree` | hide | info | on | yes | content | buffered |
| `content.node.reveal` | a subtree is revealed `[content-hide]` | visibility → visibility | `contentjob/expcontentjobhidesubtree.php:mainStep`, `ezcontentobjecttreenode.php:unhideSubTree` | reveal | info | on | yes | content | buffered |
| `content.node.swap` | two locations swap their objects | object ids → object ids | `content/ezcontentoperationcollection.php:swapNode` | swap | info | on | yes | content | buffered |
| `content.node.section` | a section is assigned to a subtree `[section-assign]` | section → section | `contentjob/expcontentjobsectionsubtree.php:prepare`, `ezcontentobjecttreenode.php:assignSectionToSubTree`, `ezcontentobjecttreenode.php:removeNodeFromTree` | assign | info | on | yes | content | buffered |
| `content.object.state` | object states are assigned `[state-assign]` | states → states | `content/ezcontentoperationcollection.php:updateObjectState` | assign | info | on | yes | content | buffered |
| `content.node.main` | the main location changes | main node → main node | `content/ezcontentoperationcollection.php:updateMainAssignment` | assign | info | off | no | content | buffered |
| `content.node.sort` | a node's sort order changes | field, order → field, order | `content/ezcontentoperationcollection.php:changeSortOrder` | sort | info | off | no | content | buffered |
| `content.node.priority` | priorities of children change | priorities → priorities | `content/ezcontentoperationcollection.php:updatePriority` | sort | info | off | no | content | buffered |
| `content.object.always_available` | the always-available flag changes | flag → flag | `content/ezcontentoperationcollection.php:updateAlwaysAvailable` | change | info | off | no | content | buffered |
| `content.object.initial_language` | the initial language changes | language → language | `content/ezcontentoperationcollection.php:updateInitialLanguage` | change | info | off | no | content | buffered |
| `content.urlalias.change` | URL aliases or wildcards are added or removed | alias → alias | `views/content/urlalias.php:run`, `views/content/urlalias_global.php:run`, `views/content/urlalias_wildcard.php:run` | change | info | off | no | content | buffered |
| `content.class.create` | a new content class is stored | – → identifier, attribute identifiers | `views/class/edit.php:storeClass` | create | info | on | yes | content | buffered |
| `content.class.change` | a class definition is stored | identifier, attributes (identifier, datatype, required, searchable) → the same | `views/class/edit.php:storeClass` | change | info | on | yes | content | buffered |
| `content.class.remove` | classes are removed | identifier, object count → – | `views/class/removeclass.php:run`, `views/class/removegroup.php:run` | remove | notice | on | yes | content | buffered |
| `content.class.copy` | a class is copied | – → identifier | `views/class/copy.php:run` | copy | info | off | no | content | buffered |
| `content.section.change` | a section is created or edited | name, identifier, navigation part → the same | `views/section/edit.php:run` | change | info | on | yes | content | buffered |
| `content.section.remove` | a section is removed | name, identifier → – | `views/section/list.php:run` | remove | notice | on | yes | content | buffered |
| `content.state.change` | a state or state group is created or edited | identifier, translations → the same | `views/state/edit.php:run`, `views/state/group_edit.php:run` | change | info | on | yes | content | buffered |
| `content.state.remove` | state groups or states are removed | identifier → – | `views/state/groups.php:run`, `views/state/group.php:run` | remove | notice | on | yes | content | buffered |
| `content.job.create` | a content job is created | – → type, params (node ids only), mode | `contentjob/expcontentjob.php:create` | create | info | on | yes | content | buffered |
| `content.job.start` | a worker starts or resumes a job | state → state, attempts | `contentjob/expcontentjobworker.php:auditStart` | start | info | on | yes | content | buffered |
| `content.job.finish` | a job ends `done` | – → nodes done, ms | `contentjob/expcontentjobworker.php:runLocked` | finish | info | on | yes | content | buffered |
| `content.job.fail` | a job ends `failed` | – → error, node id | `contentjob/expcontentjobworker.php:fail` | fail | warning | on | yes | content | buffered |
| `content.job.cancel` | a cancel is requested or takes effect | state → state | `contentjob/expcontentjob.php:cancel`, `contentjob/expcontentjobworker.php:cancelled`, `contentjob/expcontentjobworker.php:auditEnd` | cancel | info | on | yes | content | buffered |
| `content.job.resume` | a stopped job is resumed | state → state | `contentjob/expcontentjob.php:resume` | resume | info | on | yes | content | buffered |
| `content.node.view` | a node is viewed (Z6) | – (never the rendered page) | `views/content/view.php:run` | read | info | sampled | no | read | buffered |
| `content.search.query` | a search is run (Z6) | – → phrase (truncated to 64 characters), hit count | `views/content/advancedsearch.php:run`, `views/content/search.php:run` | read | info | sampled | no | read | buffered |
| `content.object.download` | a file attribute is downloaded | – | `views/content/download.php:run` | read | info | sampled | no | read | buffered |
| `access.session.login` | a user logs in `[user-login]` | – → session (hashed), handler (standard, ldap, …) (never the password) | `datatypes/ezuser/ezuser.php:loginSucceeded` | login | info | on | yes | access | at once |
| `access.session.login.failed` | a login attempt fails `[user-failed-login]` | – → attempts, reason (never the password; the attempted login of an **unknown** user is always hashed, as it is often a password typed in the wrong field) | `datatypes/ezuser/ezuser.php:loginFailed` | login | notice | on | yes | access | at once |
| `access.session.logout` | a user logs out | session → – | `datatypes/ezuser/ezuser.php:logoutCurrent` | logout | info | on | yes | access | at once |
| `access.session.regenerate` | the session id is renewed | old hash → new hash | `ezpEvent session/regenerate (bridge)` | regenerate | info | off | no | access | at once |
| `access.session.expire` | a session is destroyed or collected | – → count | `ezpEvent session/destroy, session/gc (bridge)` | expire | info | off | no | access | at once |
| `access.session.reauth` | a user re-enters the password before an audit/manage action (Q9) | – | `audit/console/expauditreauth.php:confirm` | reauth | info | on | yes | access | at once |
| `access.session.reauth.failed` | that re-entry fails | – (never the password) | `audit/console/expauditreauth.php:confirm` | reauth | notice | on | yes | access | at once |
| `access.session.revoke` | a user's other sessions end after a password change (`[PasswordSettings] EndOtherSessions`): deleted at once by the database session handler, or one session signed out on its next request because its password stamp is out of date | – → count; reason password_change or password_changed | `classes/exppasswordpolicy.php:endOtherSessions`, `classes/exppasswordpolicy.php:endStaleSession` | revoke | notice | on | yes | access | at once |
| `access.user.lock` | failed logins reach `[UserSettings] MaxNumberOfFailedLogin` | attempts → attempts, enabled | `datatypes/ezuser/ezuser.php:setFailedLoginAttempts` | lock | warning | on | yes | access | at once |
| `access.user.unlock` | the failed-login counter is reset by an administrator | attempts → 0 | `datatypes/ezuser/ezuser.php:setFailedLoginAttempts` | unlock | notice | on | yes | access | at once |
| `access.permission.refused` | a module view is refused by policy | – → the policy asked (module/function), limitation that failed | `lib/ezutils/classes/ezmodule.php:handleError` | access | notice | on | yes | access | at once |
| `access.token.refused` | a POST is refused for a missing or wrong form token | post · module/view | `ezpkernelweb.php:formTokenRefusalResult` | post | notice | on | yes | access | at once |
| `access.view.sensitive` | a view of setup, role, user or audit is opened (Z6 "always") | – (never POST bodies) | `ezpkernelweb.php:auditSensitiveView` | read | info | always | yes | access | at once |
| `access.user.create` | a user account is created | – → login, email (privacy rules), groups | `datatypes/ezuser/ezuser.php:auditStore` | create | notice | on | yes | access | at once |
| `access.user.activate` | an account is activated by its link | enabled → enabled (never the activation hash) | `user/ezuseroperationcollection.php:activation` | activate | notice | on | yes | access | at once |
| `access.user.enable` | an administrator enables or disables an account | enabled, max_login → the same | `user/ezuseroperationcollection.php:setSettings` | enable | notice | on | yes | access | at once |
| `access.user.disable` | an administrator enables or disables an account | enabled, max_login → the same | `user/ezuseroperationcollection.php:setSettings` | disable | notice | on | yes | access | at once |
| `access.user.remove` | a user is removed | login, email (privacy rules) → – | `datatypes/ezuser/ezuser.php:removeUser` | remove | notice | on | yes | access | at once |
| `access.user.email.change` | the e-mail address of an account changes | email → email (privacy rules: hashed by default) | `datatypes/ezuser/ezuser.php:auditStore` | change | notice | on | yes | access | at once |
| `access.user.login.change` | the login name changes | login → login | `datatypes/ezuser/ezuser.php:auditStore` | change | notice | on | yes | access | at once |
| `access.user.password.change` | a password is changed `[user-password-change*]` | – (never the password, the hash or its type) | `datatypes/ezuser/ezuser.php:auditStore` | change | notice | on | yes | access | at once |
| `access.user.password.change.failed` | a change is refused (wrong old password, rules) `[user-password-change-self-fail]` | – → reason | `views/user/password.php:run` | change | notice | on | yes | access | at once |
| `access.user.password.reset.request` | a reset mail is requested `[user-forgotpassword]` | – → mail sent yes/no (never the hash key) | `views/user/forgotpassword.php:run` | request | notice | on | yes | access | at once |
| `access.user.password.reset` | a reset completes `[user-forgotpassword]` | – (never the hash key, never the password) | `views/user/forgotpassword.php:run` | reset | notice | on | yes | access | at once |
| `access.user.password.reset.failed` | a reset link is unknown or expired, or the address unknown `[user-forgotpassword-fail]` | – → reason `unknown_key`/`expired`/`unknown_email` (never the key; an unknown e-mail address is hashed) | `views/user/forgotpassword.php:run` | reset | notice | on | yes | access | at once |
| `access.role.create` | a role is created | – → name | `views/role/edit.php:auditRoleStored`, `views/role/edit.php:applyRole` | create | notice | on | yes | access | at once |
| `access.role.change` | a role is stored after editing `[role-change]` | name, policies → name, policies | `views/role/edit.php:auditRoleStored`, `views/role/edit.php:applyRole` | change | notice | on | yes | access | at once |
| `access.role.remove` | roles are removed | name, policies, assignments → – | `ezrole.php:removeThis` | remove | notice | on | yes | access | at once |
| `access.role.copy` | a role is copied | – → name | `ezrole.php:copy` | copy | notice | on | yes | access | at once |
| `access.role.assign` | a role is assigned to a user or group `[role-assign]` | – → limitation (subtree, section) | `ezrole.php:assignToUser`, `ezrole.php:auditAssignment` | assign | notice | on | yes | access | at once |
| `access.role.unassign` | an assignment is removed | limitation → – | `ezrole.php:removeUserAssignment`, `ezrole.php:removeUserAssignmentByID` | unassign | notice | on | yes | access | at once |
| `access.policy.add` | a policy is added to a role | – → module, function, limitations | `ezrole.php:appendPolicy`, `views/role/edit.php:auditRoleStored` | add | notice | on | yes | access | at once |
| `access.policy.remove` | a policy is removed | module, function, limitations → – | `ezpolicy.php:removeThis`, `views/role/edit.php:auditRoleStored` | remove | notice | on | yes | access | at once |
| `system.setting.write` | an INI file is written | value in that file and value in effect → the same; for `expIniEditor` also the unified diff from `diff()` (never a value whose variable `expIniEditor::isSecret()` recognises: it is written as `[secret]`) | `audit/expaudit.php:settingWrite` | write | notice | on | yes | system | at once |
| `system.setting.undo` | a debug bar write is undone | as `system.setting.write` | `debugbar/expdebugbarsettings.php:undo` | undo | notice | on | yes | system | buffered |
| `system.extension.change` | ActiveExtensions or its order is written | list → list | `ezpactiveextensions.php:write` | change | notice | on | yes | system | buffered |
| `system.cache.clear` | caches are cleared on request | – → ids, ms | `ezcache.php:auditCleared` | clear | info | on | yes | system | buffered |
| `system.cronjob.run` | a cronjob part runs (parent: the runcronjobs invocation) | – → ms, result | `ezruncronjobs.php:auditPart` | run | info | on | yes | system | buffered |
| `system.cronjob.fail` | a part throws or exits non-zero | – → error | `ezruncronjobs.php:auditPart` | run | warning | on | yes | system | buffered |
| `system.command.run` | a command runs | – → exit code, ms | `ezscript.php:shutdown` | run | info | on | yes | system | buffered |
| `system.package.install` | a package is installed | – → name, version, items | `ezpackage.php:install` | install | notice | on | yes | system | buffered |
| `system.package.uninstall` | a package is uninstalled | name, version → – | `ezpackage.php:uninstall` | uninstall | notice | on | yes | system | buffered |
| `system.package.import` | a package archive is imported | – → name, version, sha256 of the archive | `ezpackage.php:import` | import | notice | on | yes | system | buffered |
| `system.install.run` | an installation is made | – → siteaccesses, packages, database engine | `setup/steps/ezstep_create_sites.php:init` | install | notice | on | yes | system | buffered |
| `system.upgrade.run` | an upgrade check or upgrade script runs | – → result | `views/setup/systemupgrade.php:run` | run | notice | on | yes | system | buffered |
| `system.velocity.deploy` | `exp:velocity deploy` or `restart` runs | – → steps, ms, result | `expvelocity.php:restart`, `expvelocitydeploy.php:run` | deploy | notice | on | yes | system | buffered |
| `system.repair.queue` | the repair queue is written or a repair key created | entries → entries (never the key) | `lib/ezutils/classes/ezprepairqueue.php:writeSettings` | change | notice | on | yes | system | buffered |
| `system.maintenance.change` | maintenance mode is switched | mode → mode | `expmaintenance.php:auditChange` | change | notice | on | yes | system | buffered |
| `system.template.change` | a template is created or edited in the admin | sha256 → sha256 (never the template text) | `views/visual/templateedit.php:run`, `views/visual/templatecreate.php:run` | change | notice | on | yes | system | buffered |
| `system.workflow.trigger.change` | workflow triggers are changed | workflow → workflow | `views/trigger/list.php:run` | change | notice | on | yes | system | buffered |
| `system.error.fatal` | a request ends in a fatal error | – → error reference (`eZExecution::errorReference()`), file:line (never the message's arguments) | `audit/expaudit.php:flushOnFatal` | fatal | error | on | yes | system | buffered |
| `system.audit.enable` | `Audit=enabled` takes effect where it was disabled | state → state | `audit/alerts/expauditalertevaluator.php:cronjob` | enable | notice | always | yes | system | at once |
| `system.audit.disable` | `Audit=disabled` is written or found (Default installation) | state → state | `audit/expaudit.php:settingWrite`, `audit/alerts/expauditalertevaluator.php:cronjob` | disable | warning | always | yes | system | at once |
| `system.audit.setting.write` | any audit.ini variable is written (child of `system.setting.write`) | as `system.setting.write` | `audit/expaudit.php:settingWrite` | setting | notice | always | yes | system | at once |
| `system.audit.read` | an audit console view, the fetch or `exp:audit tail/search` is used (Q9) | – → filters, result count | `audit/archive/expauditarchiver.php:restore`, `audit/console/expauditconsole.php:recordRead`, `commands/audit.php:status`, `commands/audit.php:tail`, `commands/audit.php:show`, `commands/audit.php:search` | read | info | always | yes | system | at once |
| `system.audit.export` | records are exported | – → filters, format, count, sha256 of the file | `commands/audit.php:export` | export | info | always | yes | system | at once |
| `system.audit.rotate` | a live file is closed and a new one started | – → file, records, last hash | `audit/archive/expauditmaintenance.php:rotate` | rotate | info | always | yes | system | at once |
| `system.audit.archive` | files are compressed into the archive | – → files, manifest, key id | `audit/archive/expauditarchiver.php:archiveDay` | archive | info | always | yes | system | at once |
| `system.audit.purge` | retention removes live files, archives or index rows | – → files, rows, oldest kept | `audit/archive/expauditarchiver.php:purge`, `audit/index/expauditindexer.php:purgeOld` | purge | info | always | yes | system | at once |
| `system.audit.pseudonymise` | index rows past `PseudonymiseAfterDays` are pseudonymised | – → rows | `audit/index/expauditindexer.php:pseudonymise` | pseudonymise | info | always | yes | system | at once |
| `system.audit.verify` | a chain or an archive is verified | – → intact/broken, records | `audit/archive/expauditmaintenance.php:verify`, `commands/audit.php:verify` | verify | info | always | yes | system | at once |
| `system.audit.chain.broken` | verification finds a break | – → file, line, kind (see Verification) | `audit/archive/expauditarchiver.php:archiveDay`, `audit/archive/expauditmaintenance.php:verify`, `audit/index/expauditindexer.php:recordBreak`, `commands/audit.php:verify` | chain | error | always | yes | system | at once |
| `system.audit.chain.repair` | the writer finds a torn last line and continues after it | – → bytes skipped, last good hash | `audit/expauditwriter.php` | chain | notice | always | yes | system | at once |
| `system.audit.checkpoint` | the daily signed anchor of every channel's last hash | – → per channel: file, seq, hash, HMAC | `audit/expaudit.php:checkpoint` | checkpoint | info | always | yes | system | at once |
| `system.audit.reindex` | the index is rebuilt | – → rows, ms | `audit/index/expauditindexer.php:rebuild`, `commands/audit.php:reindex` | reindex | info | always | yes | system | at once |
| `system.audit.import` | old text logs are imported (Z4) | – → file, sha256, records | `audit/archive/expauditimporter.php:import` | import | info | always | yes | system | at once |
| `system.audit.key.create` | a signing or pseudonym key is generated (Z7) | – → key id, fingerprint (never the key) | `audit/expaudit.php:write` | key | notice | always | yes | system | at once |
| `system.audit.key.rotate` | a new signing key becomes active | key id → key id | `commands/audit.php:key` | key | notice | always | yes | system | at once |
| `system.audit.sink.failed` | a sink cannot deliver (after its retries) | – → sink, error, spooled count | `audit/sinks/expauditsinkregistry.php:deliverSpool` | sink | warning | always | yes | system | at once |
| `system.audit.alert` | an alert rule fires | – → rule, count, window, group | `audit/alerts/expauditalertevaluator.php` | alert | warning | always | yes | system | at once |
| `system.audit.overflow` | the buffer exceeded its limit and flushed early, or could not be written | – → events, bytes | `audit/expaudit.php:store` | overflow | warning | always | yes | system | at once |
| `system.audit.file.open` | the first record of every channel file (links to the previous file) | – → previous file, its last seq and hash | `audit/expauditwriter.php` | file | info | always | yes | system | at once |
| `system.audit.file.close` | the last record of a rotated file | – → records | `audit/expauditwriter.php` | file | info | always | yes | system | at once |
| `commerce.order.delete` | an order is removed `[order-delete]` | order number, status, total → – (never customer address fields) | `ezorder.php:cleanupOrder` | remove | notice | on | yes | commerce | buffered |
| `commerce.order.purge` | all orders are removed `[order-delete]` | count → – | `ezorder.php:cleanup` | purge | notice | on | yes | commerce | buffered |
| `commerce.order.item.remove` | an order item is removed | product, count → – | `ezorder.php:removeItem` | remove | notice | on | yes | commerce | buffered |
| `commerce.order.create` | an order is activated (checkout confirmed) | – → order number, total, currency (never the customer's address) | `ezorder.php:activate` | create | info | off | no | commerce | buffered |
| `commerce.order.status` | the order status changes | status → status | `ezorder.php:modifyStatus` | change | info | off | no | commerce | buffered |
| `commerce.order.archive` | an order is archived or brought back | flag → flag | `ezorder.php:archiveOrder` | archive | info | off | no | commerce | buffered |
| `commerce.order.unarchive` | an order is archived or brought back | flag → flag | `ezorder.php:unArchiveOrder` | unarchive | info | off | no | commerce | buffered |
| `commerce.basket.checkout` | a basket goes to checkout | – → items, total | `views/shop/checkout.php:run` | checkout | info | off | no | commerce | buffered |
| `commerce.payment.approve` | a payment is approved | status → status (never card or account data) | `shop/classes/ezpaymentobject.php:approve` | approve | info | off | no | commerce | buffered |
| `commerce.vat.change` | VAT types or rules change | rates → rates | `ezvatrule.php:removeVatRule`, `ezvatrule.php:store`, `ezvattype.php:store`, `ezvattype.php:removeThis` | change | info | on | yes | commerce | buffered |
| `commerce.currency.change` | currencies are created, changed or removed | code, rate → code, rate | `shop/classes/ezcurrencydata.php:removeCurrencyList`, `shop/classes/ezcurrencydata.php:store` | change | info | on | yes | commerce | buffered |
| `commerce.discount.change` | discount groups or rules change | rule → rule | `ezdiscountrule.php:store`, `ezdiscountrule.php:removeByID`, `ezdiscountsubrule.php:store`, `ezdiscountsubrule.php:remove` | change | info | on | yes | commerce | buffered |
| `data.export.csv` | content is exported as CSV | – → node, rows, columns | `commands/ezcsvexport.php:run`, `views/content/subitemsexport.php:run` | export | info | on | yes | commerce | buffered |
| `data.export.package` | a package is exported | – → name, sha256 | `views/package/export.php:run` | export | info | on | yes | commerce | buffered |
| `data.export.pdf` | a PDF export is generated | – | `views/content/pdf.php:contentPDFPassthrough` | export | info | off | no | commerce | buffered |
| `data.import.csv` | CSV is imported | – → rows, created | `commands/ezcsvimport.php:run` | import | info | on | yes | commerce | buffered |
| `data.import.dba` | a .dba file is imported | – → tables, rows | `commands/ezimportdbafile.php:run` | import | info | on | yes | commerce | buffered |
| `data.import.rss` | an RSS import runs | – → created | `cronjobs/rssimport.php:rssImportAudit` | import | info | off | no | commerce | buffered |
| `data.infocollection.remove` | collected information is removed | count → – (never the collected values) | `views/infocollector/collectionlist.php:run`, `views/infocollector/overview.php:run` | remove | notice | on | yes | commerce | buffered |
| `data.infocollection.view` | collected information is opened | – | `views/infocollector/view.php:run` | read | info | off | no | commerce | buffered |
| `data.index.rebuild` | the search index is rebuilt | – → objects, ms | `commands/updatesearchindex.php:run` | rebuild | info | on | yes | commerce | buffered |
<!-- event-reference:end -->

`access.session.reauth` and `access.session.reauth.failed` are raised by the password re-entry before manage
actions (`ReauthForManage`, [3.1](#31-who-may-see-what-the-policies)); with the shipped default (disabled) nothing
raises them.

---

## 9. Proof

The acceptance tests of the design (Z9), run at the end of the work on 2026-10-02/03.

### 9.1 Tests

`php vendor/bin/phpunit tests/tests/kernel/classes/audit/`: **95 tests, 5 768 assertions, OK** (one marked risky:
`testWriteFailureNeverThrows` deliberately replaces the error handler to provoke a write failure).

| Test class | Tests | Covers |
|---|---|---|
| `expAuditRecordTest` | 12 | record format and canonical JSON (B1), names, patterns, routing, branches, parents |
| `expAuditChainTest` | 9 | genesis, linked day files, size rotation, concurrent appends (B2), tamper cases T0–T8 |
| `expAuditPrivacyTest` | 7 | every field × full/truncate/hash/off, /24 and /48, secret paths, unknown logins hashed (B5) |
| `expAuditBufferTest` | 7 | exception, exit, cleanExit, fatal error, 1 000 Velocity requests in one worker (B4) |
| `expAuditCompatTest` | 7 | the 15 4.x names, HashKey never kept, keys, ownership, settings writes (B3) |
| `expAuditHookTest` | 5 | every catalogue name has a hook point; off names build nothing; verbs; the bridge (live database) |
| `expAuditIndexTest` | 10 | schema, incremental index, crash between batch and cursor, rebuild = incremental, search, pseudonymisation, fetch functions (live database, test channels) |
| `expAuditSinksTest` | 5 | RFC 5424 and journald, a webhook receiver verifying every signature, outage and retry, mail throttling (E1) |
| `expAuditAlertsTest` | 9 | every built-in rule reached, not reached, repeated; INI rules; replay (E2) |
| `expAuditArchiveTest` | 6 | each format handler, restore, retention, T9–T12, key rotation, the daily run (E3) |
| `expAuditImportTest` | 1 | both 4.x header forms, re-import skipped, the legacy manifest (E4) |
| `expAuditFilterGuardReauthTest` | 9 | name patterns, malformed filters find nothing, the time rule, `--query`, `OnWriteFailure=refuse` (the guard, a settings write, an admin POST), `ReauthForManage` (AG-01–AG-09; live database, throwaway log directories) |
| `expAuditMailRecipientsTest` | 8 | every recipient kind against the live database (temporary users and groups, removed afterwards) |

Also `tests/tests/kernel/classes/expViewAccessTest.php` (the dashboard permission fix): 14 tests, 68 assertions, OK
(6 skipped).

### 9.2 Tamper test

The tests prove T0–T12 on generated records. Stage 6 repeated the file cases on **copies of the reference installation's real access
channel** (1 565 records over two day files), verified with the live keys. The live files were hashed before and
checked afterwards to still begin with exactly the bytes copied:

```
Channel access: access-2026-10-02.jsonl, access-2026-10-03.jsonl (copies in var/tmp/audit-stage6/tamper-20261002-170104/)
Edited file: access-2026-10-02.jsonl
PASS T0  no change                                                  INTACT   1565 records (expected intact)
PASS T1  one byte of line 100 changed                               BROKEN   first break access-2026-10-02.jsonl line 100: altered (kinds: altered; expected altered at line 100)
PASS T2  line 50 removed                                            BROKEN   first break access-2026-10-02.jsonl line 50: link (kinds: link, gap; expected link + gap at line 50)
PASS T3  lines 61 and 62 swapped                                    BROKEN   first break access-2026-10-02.jsonl line 61: link (kinds: link, gap, reordered; expected reordered)
PASS T4  a forged line with a correct hash after line 70            BROKEN   first break access-2026-10-02.jsonl line 72: link (kinds: link, reordered; expected link at line 72)
PASS T5  20 records before checkpoint seq 1539 rewritten, chain recomputed BROKEN   first break access-2026-10-02.jsonl line 1539: rewritten (kinds: rewritten, truncated; expected rewritten at line 1539)
PASS T6  the oldest day file access-2026-10-02.jsonl removed        BROKEN   first break access-2026-10-03.jsonl line 1: no_origin (kinds: no_origin; expected no_origin or missing_file)
PASS T7  the last 10 lines of access-2026-10-02.jsonl cut off       BROKEN   first break access-2026-10-03.jsonl line 1: truncated (kinds: truncated; expected truncated)
PASS the live access files were not changed by the test (every live file still begins with the bytes copied)
PASS all cases
```

T5 is the attacker who recomputes the chain without the key. The signed checkpoint written before shows the rewrite
at its seq, and the next day's `file.open` record still names the old last hash (`truncated`). T8 (a torn line,
repaired) and T9–T12 (archives) are proven by `expAuditChainTest` and `expAuditArchiveTest`. `exp:audit verify` on
the live log afterwards: every channel INTACT (output in [section 2](#2-quick-start-two-minutes)).

### 9.3 Performance

**Per request** (P1, P2), measured in the kernel as in stages 2 and 3: 2 000 requests per case, alternating in
blocks of 100 between Audit on (the live settings, every stage 3 hook point) and `Audit=disabled`:

| Case | On p50 / p95 | Off p50 / p95 | Added (p50) | Target |
|---|---|---|---|---|
| R: a request without events (reset, request id header, bridge attached, sensitive-view check, an off hook point, a sampled read with reads off, final flush) | 0.118 / 0.156 ms | 0.040 / 0.061 ms | **+0.078 ms** | < 2 ms |
| W: one buffered event (a node described from the database) and its append | 0.796 / 1.087 ms | 0.040 / 0.063 ms | **+0.756 ms** | < 2 ms |
| I: one immediate access event and the syslog sink at flush (journald, 200 requests under a test identifier) | 0.615 / 0.768 ms | 0.040 / 0.058 ms | **+0.575 ms** | < 2 ms |

A front page or admin page that records nothing therefore pays less than a tenth of a millisecond, and a request
that records something pays well under the 2 ms budget, the journald copy included.

**Content jobs** (P3): a content job copy and a content job remove (no trash) of a 144-node test subtree under Media,
three rounds, alternating between the audit on and off:

```
source 14757: 144 nodes; 3 rounds, copy job + remove job, audit on (live settings) and off (Audit=disabled)
round 1 off copy done 144 nodes   5.15 s | remove done   2.91 s | total   8.06 s
round 1 on  copy done 144 nodes   5.21 s | remove done   2.68 s | total   7.89 s
round 2 on  copy done 144 nodes   4.95 s | remove done   2.76 s | total   7.71 s
round 2 off copy done 144 nodes   6.00 s | remove done   2.74 s | total   8.74 s
round 3 off copy done 144 nodes   5.62 s | remove done   2.54 s | total   8.17 s
round 3 on  copy done 144 nodes   4.95 s | remove done   2.63 s | total   7.58 s
copy: on 15.11 s, off 16.77 s | remove: on 8.07 s, off 8.19 s | total on 23.18 s, off 24.97 s, ratio 0.928
PASS content jobs with the audit within +5% of the time without (ratio 0.928)
```

The audit's share is below the run-to-run noise (the target was at most 1.05). The on rounds wrote 6 job creates,
6 starts, 6 finishes and 432 `content.object.remove` children. The off rounds wrote nothing. The test folder was
removed afterwards by a content job:

```
removed the test folder 14756 (146 nodes) by content job 20261003-000230-29a7dcc0: done
PASS no test content left: 0 objects named 'Audit stage 6 test%' or 'A6 %', 0 nodes below 14756
```

P4 (ten concurrent writers to one channel, the chain intact, no torn lines) is `expAuditChainTest::testConcurrentAppends`.

### 9.4 Permissions

- **A1** (stage 1, the dashboard shows only what a user may open): 0 failures in 336 checks per siteaccess for
  Anonymous, Member, Partner, Editor, a subtree-limited Editor and Administrator (Appendix B, stage 1).
- **A2/A3** (stage 4, the audit console): admin, Editor, Auditor (`audit/read`), Shop auditor (`audit/read` limited
  to Channel commerce), Audit manager and Administrator, on admin4 and admin, Apache and Velocity. Tab, sidebar link,
  dashboard block, job links and every view were shown or refused as specified, the shop auditor saw commerce only,
  and every refusal was recorded as `access.permission.refused`. All PASS (Appendix B, stage 4).

### 9.5 Registries and configurations

```
auditbranches    Audit taxonomy branches    0 entries, 0 broken:
auditsinks       Audit sinks                3 entries, 0 broken: syslog=expAuditSyslogSink, webhook=expAuditWebhookSink, mail=expAuditMailSink
auditalertrules  Audit alert rule classes   3 entries, 0 broken: threshold=expAuditThresholdRule, match=expAuditMatchRule, schedule=expAuditScheduleRule
auditformats     Audit archive formats      5 entries, 0 broken: gzip=expAuditGzipFormat, bzip2=expAuditBzip2Format, xz=expAuditXzFormat, zstd=expAuditZstdFormat, zip=expAuditZipFormat
PASS the RAD survey counts the audit registries
```

The six reference configurations: `PASS 6 reference configurations` ([section 6](#6-reference-configurations)). The
event reference: `PASS 135 names, every one described`.

---

## Appendix A: design decisions (27 questions, 2026-10-02)

| # | Question | Decision |
|---|---|---|
| Q1 | Storage | **Both**: JSON lines files are the record; an index in the site's database serves the console (F4) |
| Q2 | Fields | **Request context** (event id, request id, siteaccess, URL/method, module/view, engine Apache/Velocity/CLI, host, pid, duration, HTTP status), **actor** (user id, login, roles at the time, session hash, IP v4/v6, user agent, impersonation, CLI user + command), **before/after values** (configurable, never passwords/tokens), **result + reason** (success/refused/failed, policy/token/lock reason, error) |
| Q3 | Privacy | **Configurable, safe default**: per field full / truncated IP (/24, /48) / hashed / off; user agent on/off; never passwords or tokens; personal fields pseudonymised after the retention period |
| Q4 | Integrity | **Hash chain + signed archives**: each event carries the previous event's hash per file; archives get a checksum manifest and an HMAC; the console shows whether a chain is intact or where it breaks |
| Q5 | Families | **Content lifecycle, users + access, system + config, commerce + data**, and the classification must be generic and reusable ("track almost everything, zoology style") |
| Q6 | Files | **Channels by family, daily files**; INI maps events to channels; the old per-event file names keep working as aliases |
| Q7 | Sinks | **syslog/journald, webhook/HTTP, e-mail on critical events, a sink registry** for extensions |
| Q8 | Rotation | **By day and size; compressed archives** (gzip, bzip2, xz, zstd, zip through format handlers in a registry) to a configurable archive path; **retention per channel; scheduled by a cronjob part** (also runnable from the console and the command) |
| Q9 | Access | New policies **audit/read** and **audit/manage**; every console access is itself audited; optional password re-entry before manage actions |
| Q10 | Console | **Timeline + filters + search, event detail + links, charts + alerts view, export** |
| F1 | Taxonomy | **Both**: hierarchical dotted names for configuration and routing, and every record also carries actor / verb / object / target / result (ActivityStreams-like) |
| F2 | Correlation | **Request id** (also a response header), **session and job ids**, **parent/child events** (depth configurable) |
| F3 | Speed | **Buffered**, flushed in one append at request end (also on fatal errors); security events written at once; per-request state reset for Velocity's persistent workers |
| F4 | Index | **The site's main database** (schema on every engine: Z1) |
| F5 | Alerts | **Built-in rules** (brute force, admin role granted, settings written out of hours, mass delete, audit disabled or chain broken), **INI rules**, **rule classes** in a registry |
| F6 | Retention | **90 days live, 2 years archived**, per channel; personal fields pseudonymised in the index after 90 days |
| F7 | Dashboard | **Only what the user's policies allow**: every dashboard block and sidebar link checks access to its module/view (and limitations) |
| Z1 | Engines | **SQLite, MySQL/MariaDB, PostgreSQL, Oracle, MongoDB**: schema and tests on each one reachable here |
| Z2 | Placement | **A new `audit` module with its own top tab**, **dashboard sidebar link + block**, **links from content/job and content/jobs**, **the Setup menu** |
| Z3 | API | **`expAudit::event()`** (the old `eZAudit::writeAudit()` keeps working and maps to it), **template operator/fetch** (policy checked), **ezpEvent bridge**, **command `exp:audit`** |
| Z4 | Old logs | **Imported** into the new format (marked imported, outside the chain); originals archived |
| Z5 | Default | **On by default in every installation** (owner: "enabled in a default installation by default conventions, vs ezp4 where it was off") |
| Z6 | Reads | **Optional, sampled**: node views and searches per section/class with a sample rate; views of sensitive admin modules (setup, role, user, audit) always |
| Z7 | Key | **Generated on first use, stored in settings/override** (never committed), shown as a fingerprint; key rotation with key ids in archives |
| Z8 | Delivery | **Stages with sign-off** |
| Z9 | Proof | **Coverage matrix, tamper test, performance, permission matrix** |
| Z10 | Docs | **Operator guide, developer guide, event reference, security notes** |

Every point the design marked "Proposed" was accepted by the owner and built as described in this guide, apart from
the items listed in Appendix C.

## Appendix B: how it was built, stage by stage

Before 6.0.15, `eZAudit` (off by default) wrote plain text blocks per event name through `eZLog`. Rotation kept
about 800 KB per file and silently deleted older history. Its `$GLOBALS` caches made it unsafe under Velocity, a
password-reset key and mistyped passwords could end up in the log, and the ezmbpaex password events had no file at
all and were dropped. Ten event names were written from 36 call sites. The audit described here replaces it, and
`eZAudit::writeAudit()` remains as a compatibility path.

**Stage 1: the dashboard permission defect (F7).** The admin dashboard showed setup features to every user who could
open it. `expViewAccess` (kernel/classes/expviewaccess.php) now decides whether the current user can open an address
the way `ezpKernelWeb` decides the request: URL aliases, module and view, `[SiteAccessRules]`, `RequireUserLogin`,
`PolicyOmitList`, the login siteaccess limitation, `hasAccessToView()` with limitations, and the node or object of
content/view and content/edit. Templates ask `fetch( 'user', 'can_open', hash( 'uri', … ) )`. Top tabs, every left
menu, the dashboard blocks (`ViewList[]`), the admin4 dashboard's links and the context menus use it. New settings:
`menu.ini [MenuAccessSettings] CheckViewAccess`, `NoAccessLinks`; `dashboard.ini ViewList[]`. Matrix A1: 0 failures in
336 checks per siteaccess (admin and admin4).

| User | Visible links | open | Hidden (administrator sees) | refused |
|---|---|---|---|---|
| Anonymous, Member, Partner | sign-in page | – | 44 | 44 |
| Editor | 26 | 26 | 21 | 21 |
| Editor, subtree-limited | 27 | 27 | 20 | 20 |
| Administrator (test) / admin | 48 / 61 | all | 0 | 0 |

**Stage 2: the event core.** `expAudit`, the taxonomy registry (135 names), the buffer and writer (channel locks,
the chain head read from the file, open and close records, size rotation, torn-line repair, ownership), verifier,
reader, keys, privacy, canonical JSON, the compatibility path, the Velocity reset and the `X-Exp-Request-Id` header,
`exp:audit` status/channels/tail/show/verify/checkpoint, the view `audit/recent`. Measured: a request without events
0.090 ms on against 0.029 ms off. Deviations from the original text, now part of the design: the header is
`X-Exp-Request-Id`; `TrustedRequestIdHeader` is trusted from loopback only; a failed login of a known account has
`reason: credentials`, an unknown one `reason: not_found` with the login hashed.

**Stage 3: instrumentation.** Every hook point goes through `expAuditHook` (guarded, never throwing, data built only
when on). The kernel's `writeAudit()` calls became native events with full fields, subtrees and content jobs became
parents of their parts, inner hook points are muted while an outer one records, and the ezpEvent bridge was added.
Coverage matrix C1, run through real HTTP requests and CLI calls on test fixtures:

| Server | PASS | FAIL | n/a |
|---|---|---|---|
| Apache (PHP-FPM) | 59 | 0 | 5 |
| Velocity (:8080) | 59 | 0 | 5 |
| CLI | 78 | 0 | 45 |

Names no server raised in the test were the audit's own (`system.audit.*`, proven by the stage 2, 4 and 5 tests),
and actions not run against live data (package install/import, installation, deploy, order purge, search index
rebuild, dba import, RSS import), each listed with its reason in the stage 3 commit of this document. The fixtures
were removed afterwards with proof. Overhead: a request without events 0.127 ms, +0.04 ms over stage 2. Found on the
way and fixed separately: content/pdf answered 500 (`eZContentObject::cacheInfo()` called statically).

**Stage 4: index and console.** The three index tables on every engine (with full text per engine), the incremental
indexer, search (`expAuditQuery`), the module `audit` with dashboard, console, event, charts, alerts, export,
archives and settings, the policies with the Channel limitation, the navigation part, the fetch functions and the
operator, the top tab, sidebar link, dashboard block, Setup entry, job links and node tab, and translations
(eng-US, ger-DE, 240 messages). Proof: schema on SQLite (21 checks) and generated SQL for MySQL, PostgreSQL, Oracle and
MongoDB (15 checks); 13 097 records rebuilt in 6.0 s; FTS5 and LIKE gave the same ids; `expAuditIndexTest` 10/10;
permission matrix A2/A3 PASS for six users on admin4 and admin, Apache and Velocity; Playwright at 960 px, scale 2,
light and dark. The dashboard opens in about 25 ms with its 7-day figures cached. Deviations: SQLite FTS5 uses the
trigram tokenizer; filter forms post and redirect to the bookmarkable URL; charts are HTML/CSS.

**Stage 5: sinks, alerts, rotation.** The sink registry with spools and retries, syslog/journald (journald's native
protocol for `Transport=local`, because journald 252 does not parse RFC 5424 headers on /dev/log), the signed
webhook, mail with configurable recipients (an owner decision on 2026-10-02), the alert evaluator with the three
rule classes and seven shipped rules, five format handlers, archives with signed and chained manifests, retention
with a purge ledger, the import of the 4.x logs (the reference installation's own `login.log` imported, originals archived), the
cronjob part, and `exp:audit` complete. Tests E1–E4 plus the recipients test: 81 tests in all at that point.
Deviations: `match` rules fire once per record, `schedule` rules once per group and window; retention never removes a
key; restored and imported files have their own directories.

**Stage 6: proof and this guide.** The full test run, the tamper test on copies of real files, the performance
measurement against the targets, the content job timing, the RAD survey count, the validation of the reference
configurations, and the generated event reference ([section 9](#9-proof)).

## Appendix C: known issues (2026-10-03)

Fixed since this list was first written: `exp:audit search --query` ([3.3](#33-the-command-expaudit)), malformed name
patterns refused, one time rule for `--from`/`--to`, the password re-entry `ReauthForManage`
([3.1](#31-who-may-see-what-the-policies)) and `OnWriteFailure=refuse` ([4.13](#413-when-the-audit-cannot-write)).

Found while writing this guide. Each was checked on the reference installation:

| Issue | Effect | Until it is fixed |
|---|---|---|
| The console's export cuts at `MaxExportRecords` instead of running larger exports in the background | large exports from the browser are incomplete (it says so) | `exp:audit export` has no limit |
| The archives and settings views and the alerts view are read-only (no "Archive now", no acknowledge, no settings form) | these actions are done on the command line | `exp:audit archive/restore/key`, `exp:ini set audit.ini/…` |

## Related pages

- [Audit trail (feature)](../../features/6.0/audit-trail.md)
- [Behaviour changes of 1 and 2 October 2026](behaviour-changes-2026-10.md)
- [exp:ini](console-exp-ini.md), used to change audit settings
- [Security and audit guide](../../guides/security-and-audit.md)
