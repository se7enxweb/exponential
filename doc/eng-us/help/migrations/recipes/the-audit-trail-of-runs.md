---
title: "The audit trail of migration runs"
audience: admin, developer, hosting, dba, software-engineer
category: migrations
order: 20
summary: Where a migration run leaves its traces - the tables exp_migration and exp_migration_run, the status file and the run log in git, var/log/migration.log and the audit events system.migration.* - and how to answer "what ran where, when and by whom".
keywords: migration, migrations, schema change, database change, audit, audit trail, audit log, history, exp_migration, exp_migration_run, status file, run log, migration.log, system.migration.run, system.migration.apply, system.migration.fail, system.migration.rollback, system.migration.caption, system.migration.push
type: help
---
# The audit trail of migration runs

A migration that ran leaves a trace in five places. Together they answer the questions an audit, a support case or a
release review asks: what ran, where, when, by whom, with which file, what it changed, and how to get back. This page
shows each place and the query or command that reads it.

## What you'll have at the end

- the commands and files that tell what an installation has applied;
- the audit events of migrations switched on and found in the audit log;
- the record of other environments read from git, without access to their databases.

## Before you start

- The record tables exist: `./console exp:upgrade:status` shows `6.0.15-migration-tables` as `done`.
- For the git part, the migrations repository is committed and pushed
  ([Pushing tags](pushing-tags.md)).

## The five places

| Where | What | Who reads it |
|---|---|---|
| table `exp_migration` | one row per migration (source + id): state, run id, checksum of the file, times, duration, `run_by`, engine, the caption before it, the commit after it, the last message | `exp:migration:status`, Setup > Migrations |
| table `exp_migration_run` | one row per run: state (`running`, `done`, `failed`, `rolled-back`), user, environment, captions before and after, commit, tag, push state | the history, `exp:migration:status` |
| `migrations/status/<environment>.json` | the record mirrored into git, sorted, without users or messages | anyone with a clone |
| `migrations/runs/<run-id>.log` | one line per event of the run | anyone with a clone |
| the audit log | `system.migration.*` events | the audit console, its sinks and webhooks |

Every run also writes its lines to `var/log/migration.log`, with the run id in front.

## Steps

### 1. Read the record on the console

```bash
./console exp:migration:status --runs=5
```

```text
Exponential migrations: status
Run 20261101-111500-91ab2c, environment production, database postgresql (transactions: full)

  done        20261101090000_news_note_priority             site               The column priority of exp_news_note
              recorded done by run 20261101-100500-7c4d11 on 2026-11-01T10:05:00+00:00

The latest runs, newest first:
  20261101-100500-7c4d11   done        2026-11-01T10:05:00+00:00  cli www-data, pushed
Summary: 1 migration(s): 1 done, 0 pending, 0 not needed, 0 failed, 0 unknown
PASS
```

`--format=json` gives the same as the machine output `exp-migration/1`, with `record` per migration and `runs`.

### 2. Read the record in the administration

Setup > Migrations > History lists every run with its captions, commit, tag and push; a run's page shows its
migrations and the end of its run log ([The status and the history](../status-and-history.md)). The policy
`migration/read` is enough.

### 3. Read one run's log

```text
2026-11-01T10:05:00Z info run 20261101-100500-7c4d11 by www-data on postgresql in production: 1 to apply
2026-11-01T10:05:00Z info applied 20261101090000_news_note_priority in 41 ms: added the column exp_news_note.priority (1 statement(s))
2026-11-01T10:05:00Z info run 20261101-100500-7c4d11 done: 1 applied, 0 failed
```

Before a line is written it is cleaned of secret values, credentials in URLs, `password=...` pairs, bearer tokens and
e-mail addresses, so the log can be pushed. Return no secret from `up()` all the same.

### 4. Switch on and read the audit events

The shipped `settings/audit.ini` enables them:

```ini
[AuditEventSettings]
Enabled[]=system.migration.run
Enabled[]=system.migration.apply
Enabled[]=system.migration.fail
Enabled[]=system.migration.rollback
Enabled[]=system.migration.caption
Enabled[]=system.migration.push
```

| Event | Recorded when |
|---|---|
| `system.migration.run` | a run with `--apply` ends |
| `system.migration.apply` | a migration is applied, or a changed file is accepted with `--force --reason` |
| `system.migration.fail` | a migration fails |
| `system.migration.rollback` | a run, a caption or one migration is rolled back (verbs `rollback-run`, `rollback-caption`, `rollback`) |
| `system.migration.caption` | a caption is taken |
| `system.migration.push` | the record of a run is pushed |

The events go through the audit's sinks like every other event, so a webhook or mail sink sends them on.

### 5. Read another environment from git

```bash
git fetch --tags
git tag -l 'migrations/production/*' --sort=version:refname | tail -5
git show origin/main:migrations/status/production.json | head -20
```

The status file says what production has applied, the tag names its runs, `runs/<run-id>.log` what each run did.

## Answering the usual questions

| Question | Where |
|---|---|
| Has production applied migration X? | `migrations/status/production.json`, or `exp:migration:status` there |
| Who ran it? | `exp_migration.run_by` (`cli <system user>` or `web <system user>`), the run's user in `exp_migration_run`, the audit event's actor |
| Which file ran? | `exp_migration.checksum`; a later change is refused by the checksum guard |
| Was a changed file accepted? | `system.migration.apply` with the reason |
| What did it change? | the run log; with `--simulate --diff` before the run, the table diff |
| How do we get back? | the run's `caption_pre`; see [Captions and rollback in practice](captions-and-rollback-in-practice.md) |

## Check it

Run a migration on a development installation and find it in all five places: the status line, the history, the
log, `system.migration.apply` in the audit console, and the commit in git.

## Next step

[Pushing tags](pushing-tags.md): the commit, the tag and the push that carry the record to the remote.

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Policies, views and events](../policies-views-and-events.md), [The status and the history](../status-and-history.md)
- [The audit trail](../../../features/6.0/audit-trail.md), [the audit guide](../../../bc/6.0/audit.md),
  [audit.ini \[AuditEventSettings\]](../../../examples/ini-settings/audit/AuditEventSettings.md)
- [The migrations repository](../../../guides/migrations-repositories.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
