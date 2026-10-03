# The audit trail: who did what, and proof that nobody changed the record

Exponential 6.0.15 records security-relevant events in a tamper-evident log: sign-ins and refusals, content
changes, settings writes, cache clears, deploys, orders and VAT changes. The log is on by default, needs no
setup, and can be read from the admin interface or the command line.

Added 2026-10-02. The complete reference (event catalogue, record format, every setting) is
[doc/bc/6.0/audit.md](../../bc/6.0/audit.md); this page is the short version for a person who wants results in
minutes.

## Why you want it

- **Answer "who removed this?"** A subtree removal is one parent event with one child per node below it; the
  trash purge is a child of `content.trash.empty`; a content job is the parent of everything its batches did.
- **Notice trouble early.** Alert rules watch for brute-force sign-ins, an administrator role being granted,
  settings written out of hours, mass deletes, the audit being switched off and a broken chain.
- **Prove the record is intact.** Every record carries the hash of the record before it (a hash chain). Daily
  files are closed, archived and listed in signed manifests. `verify` tells you if anything was edited or cut.
- **Keep privacy rules.** Addresses are truncated, sessions hashed, user agents shortened, query values removed.
  Passwords, tokens, repair keys and secrets are never recorded. A failed sign-in for an unknown account is
  recorded hashed only.

## Try it in two minutes

```bash
./console exp:audit status --allow-root-user
./console exp:audit tail --channel=access --name='access.session.*' --lines=3 --allow-root-user
./console exp:audit verify --allow-root-user        # exit 1 when a chain is broken
```

Then sign in to the admin interface as an administrator, open the **Audit** tab (`/audit/dashboard`), sign out
and in again, and open **Console**: your sign-in is the newest `access.session.login`.

Three things to do once on a new installation:

1. Make sure the `frequent` cronjob group runs (`php runcronjobs.php frequent` every few minutes); it contains the
   `audit` cronjob part that indexes, delivers sink spools, evaluates alert rules and, once a day, rotates,
   verifies and archives.
2. Check who gets alert mail: `./console exp:audit alerts recipients` (default: the site's `AdminEmail`).
3. Back up `settings/override/audit.ini.append.php`: it holds the signing and pseudonym keys.

## What is recorded

| Channel | Examples | File location |
|---|---|---|
| `content` | publish, move, copy, remove, hide, sections, states, trash, versions, URL aliases, classes, content jobs | `var/<site>/log/audit/` |
| `access` | sign-in, failed sign-in, refused view, account lock, role assignment, password reset, form-token refusal | same |
| `system` | settings writes, cache clears, cronjob runs, commands, package installs, maintenance, deploys, repair settings | same |
| `commerce` | orders, payments (never card data), VAT, currencies, discounts, CSV/package exports and imports | same |
| `read` | sampled node views, searches and downloads, only when reads are switched on | same |

Defaults of `settings/audit.ini` (`[AuditSettings]`, `[AuditChannelSettings]`):

| Block | Key | Default | Scope |
|---|---|---|---|
| `AuditSettings` | `Audit` | `enabled` | installation |
| `AuditSettings` | `LogDir` | `log/audit` | installation |
| `AuditSettings` | `OnWriteFailure` | `continue` | installation |
| `AuditChannel_content`, `_access`, `_system`, `_commerce` | `LiveDays` / `ArchiveDays` / `MaxFileSize` / `ArchiveFormat` | 90 / 730 / 64M / gzip | per channel |
| `AuditChannel_read` | `LiveDays` / `ArchiveDays` / `MaxFileSize` / `ArchiveFormat` | 30 / 90 / 256M / zstd | per channel |

Override in `settings/override/audit.ini.append.php` or an extension's `settings/audit.ini.append.php`.

## Who may look

Two policies in the `audit` module: `audit/read` (the tab, dashboard, console, event, charts, alerts, export;
optionally limited by `Channel`) and `audit/manage` (archives, settings, "Verify now"). Only Administrator holds
them in a new installation. A user without `audit/read` sees no tab, no link and no block, and a typed URL is
refused and itself recorded as `access.permission.refused`.

## From the command line

`exp:audit` has the actions `status`, `channels`, `tail`, `show <event id>`, `verify`, `checkpoint`, `search`,
`rotate`, `archive`, `restore`, `purge`, `reindex`, `pseudonymise`, `export`, `import`, `key`, `sinks`, `alerts`
and `cron`. Full-text search takes `--query=<text>` (a plain `-q` is every script's quiet option). Name patterns
are `*`, a prefix ending in `.*` or a whole name; anything else is refused with exit code 2. `--from` and `--to`
accept `Z` or `+HH:MM` offsets; without one they are UTC on the command line and site time in the console.

## Sinks, alerts and archives

- **Sinks:** syslog (RFC 5424; with `Transport=local` journald's native socket, so `journalctl -t exponential`
  finds the records), a signed webhook (HMAC-SHA-256 over timestamp and body, spooled with retries) and e-mail on
  critical events.
- **Alert recipients:** a rule's `Recipients[]`, else `[AuditAlertSettings] Recipients[]`, else
  `[AuditSink_mail] Receivers[]`, else the site's `AdminEmail`; named groups and users by id or login work too.
- **Archives:** days older than `LiveDays` are compressed (gzip, bzip2, xz, zstd or zip), read back and compared
  before the live file goes, and listed in a manifest signed with an HMAC. Restore and import of the 4.x text logs
  are supported.
- **Index:** three tables (`expaudit_event`, `expaudit_cursor`, `expaudit_file`) hold a searchable copy on every
  engine. On an existing installation create them with
  `php update/common/scripts/6.0/createaudittables.php`.

## Limits and good to know

- The 4.x `eZAudit::writeAudit()` calls keep working; ten 4.x names map to the new event names.
- Node views, searches and downloads are only recorded when reads are switched on.
- `ReauthForManage` (off by default) makes "Verify now" ask for the password again.
- The dashboard keeps its 7-day figures for a minute, so it opens in about 25 ms instead of 170 ms.

Related: [audit specification](../../specifications/6.0/audit-event-model.md),
[remote audit services](remote-services-expservices.md), [content jobs](content-jobs.md),
[October 2026 chronicle](../../history/2026/2026-10.md).
