# Specification: the migration file format

This page is the normative description of a migration file of Exponential 6.0.15: the file name, the header, the
PHP class, the checksum, and the formats of the record files a run writes (the status file and the run log). It
covers what the 6.0.15 kernel reads and writes; the steps of YAML, SQL and INI migrations will be added to it when
they exist. The guide is [Migrations](../../guides/migrations.md).

Version of this specification: 1 (exp-migration/1, exp-migration-status/1).

## The file name

```text
YYYYMMDDHHMMSS_<name>.<type>
YYYYMMDDHHMMSS_<name>.<engine>.sql
```

| Part | Rule |
|---|---|
| `YYYYMMDDHHMMSS` | a valid date and time (checked as a calendar date) |
| `<name>` | letters, digits, `_` and `-` |
| `<type>` | `php`, `yml`, `sql` or `ini` |
| `<engine>` | only before `.sql`: `mysql`, `postgresql`, `sqlite`, `oracle` or `mongodb` |

- The **id** is the file name without `.<type>` (and without `.<engine>`): `20261031120000_add_news_class`.
- Files of one id that differ only in the engine (`..._index.mysql.sql`, `..._index.postgresql.sql` and a portable
  `..._index.sql`) are **one** migration with a file per engine; the engine's own file wins, else the portable one.
- One id has one type: a `.php` and a `.yml` with the same id are an error.
- A file whose name does not start with 14 digits and `_` is not a migration and is skipped (a README, a `.gitkeep`);
  one that does but breaks a rule above is an error.
- Only the files directly in a migrations folder are read; subfolders are not.
- In 6.0.15 only `php` runs; the other types are found, ordered and checked, and refused by the runner.

## The header

The header is the comment block at the top of the file, one `key: value` per line, in the comment syntax of the
file. At most the first 16 KiB of a file are read for it.

| Type | Comment syntax | May come before the header |
|---|---|---|
| `php` | `//`, `#`, or the lines of a `/** ... */` block | `<?php` |
| `yml`, `ini` | `#` | a `---` line (YAML), a `#?ini charset="utf-8"?` line (INI) |
| `sql` | `--` | |

The header ends at the first line that is not a comment. Within it, a line that is not a lowercase `key:` followed
by a value is prose and ignored.

| Key | Value | Default |
|---|---|---|
| `id` | the id; when given it must equal the file name's id | the file name's id |
| `description` | one line of text | empty (a warning in `exp:migration:check`) |
| `requires` | a list: migration ids, and `extension:<name>` for an extension that must be active | none |
| `environments` | a list of environment names; `!<name>` leaves one out | every environment |
| `engines` | a list of `mysql`, `postgresql`, `sqlite`, `oracle`, `mongodb` | every engine |
| `maintenance` | `yes` or `no` | `no` |
| `author` | one line of text | empty |

- Lists are comma-separated; a list key may repeat and the values add up.
- A key that is not in the table is an error (so a misspelled key does not pass silently); a key other than a list
  given twice is an error.
- `environments`: with names only, the migration runs in those; with `!<name>` only, everywhere except those; the
  environment of an installation is `site.ini [AdminEnvironment] Level`, with `Level=auto` the variable `EXP_ENV`,
  or the command's `--environment`.

Example (PHP):

```php
<?php
// description: An index for the news list
// requires: 20261030090000_news_class, extension:expnews
// environments: staging, production
// engines: mysql, postgresql
// maintenance: no
// author: Site team
namespace MySite\Migrations;
```

## Order

1. By id: the timestamp, then the name.
2. A migration comes after every migration it names in `requires`, wherever their timestamps put them.
3. With an environment, only the migrations that run in it are kept.

Errors: an id defined twice (the first found counts; the sources are searched site, extensions, packages), a
requirement that names no migration, a requirement that does not run in the environment, an `extension:<name>`
requirement whose extension is not active, a migration that requires itself, and a cycle. A migration in a cycle, or
waiting for one, is kept at the end of the order so a listing still shows it. No migration runs while there is an
error.

## The PHP migration

- The file declares exactly one non-abstract class that extends `\expMigrationPhp`. No other file of the
  installation's migrations may declare a class of the same name.
- `up( expMigrationContext $context ): string[]` is required; `plan()`, `down()`, `detect()` and `transactional()`
  are optional ([PHP migrations](../../guides/migrations-php.md#the-methods)).
- `up()` signals a failure by throwing; it returns the lines of what it did.
- `detect()` returns `done`, `pending`, `not-needed` or `null`; `null` means the record decides.

## The checksum

The checksum of a migration is the SHA-256 of its file's bytes with the line endings made `\n` (so a checkout with
CRLF line endings gives the same checksum), of the file that runs on the engine for a migration with a file per
engine, as a lowercase hex string of 64 characters. It is recorded in `exp_migration.checksum` when
the migration is applied. A migration recorded as `done` whose checksum differs is refused, unless the run is given
`--force --reason=<text>`.

## States and actions

| State | Means |
|---|---|
| `done` | recorded as applied (or `detect()` says so) |
| `pending` | not applied yet |
| `not-needed` | not for this environment or engine, or `detect()` says the change is there |
| `failed` | the last run of it failed |
| `unknown` | the state could not be told (`detect()` threw) |

Actions in a report: `applied`, `would-apply`, `simulated`, `failed`, `not-run`, `blocked`, `refused`, `accepted`,
`would-accept`, `undone`, `would-undo`, `restored`, `none`.

## The record tables

`exp_migration` (key: `source` + `migration_id`): `source`, `migration_id`, `stage`, `version`, `state`,
`started_at`, `finished_at`, `duration_ms`, `run_by`, `engine`, `checksum`, `message` (at most 1000 characters),
`file_path`, `environment`, `run_id`, `caption_id`, `revision`. Sources: `site`, `extension:<name>`,
`package:<name>`, and `kernel` for the steps of `exp:upgrade` (stage `code`, `libraries`, `database`, `scripts`,
`finish`) and their captions (stage `caption`, `migration_id` `caption:<id>`).

`exp_migration_run` (key: `run_id`): one row per run with its state (`running`, `done`, `failed`, `rolled-back`),
times, user, environment, `caption_pre`, `caption_post`, `commit_id`, `tag_name` and `push_state` (`pushed`,
`pending`, `disabled`, `no-remote`, `none`).

`run_by` is `cli <system user>` or `web <system user>`, never a password. A run id is
`YYYYMMDD-HHMMSS-<6 hex>`; any id of letters, digits, `.`, `_` and `-` (at most 64, not starting with a dot) is
accepted.

## The status file

`<repository>/status/<environment>.json`, schema `exp-migration-status/1`
(`share/migration/schema/exp-migration-status-1.json`):

| Key | Value |
|---|---|
| `schema` | `exp-migration-status/1` |
| `environment` | the environment, `default` when the installation names none |
| `engine` | the database engine |
| `updated_at` | the newest time of the record (UTC, ISO 8601), never "now" |
| `summary` | counts per state and `total` |
| `migrations[]` | `source`, `id`, `state`, `file`, `checksum`, `stage`, `version`, `run_id`, `started_at`, `finished_at`, `duration_ms`, `caption_id`, `revision` |
| `runs[]` | `run_id`, `state`, `started_at`, `finished_at`, `caption_pre`, `caption_post`, `commit`, `tag`, `push_state`, `log` |

The file is written whole and atomically, sorted, with the times of the record only, so the same record gives the
same bytes. It never contains `run_by` or a message.

## The run log

`<repository>/runs/<run-id>.log`, one line per event:

```text
<UTC time, ISO 8601> <level> <text>
2026-11-01T02:30:20Z info applied 20261031120000_news_note_table in 3 ms: created exp_news_note | wrote the note 1
```

Levels: `debug`, `info`, `notice`, `warning`, `error`. Before a line is written it is cleaned of secret values,
credentials in URLs, `password=...` pairs, bearer tokens and e-mail addresses. The same lines go to
`var/log/migration.log` with the run id in front.

## The machine output

Every `exp:migration:*` command with `--format=json` prints one JSON object with `schema` = `exp-migration/1`,
`command`, `mode` (`status`, `dry-run`, `simulate`, `apply`, `rollback`, `rollback-dry-run`, `check`, `generate`,
`caption`, ...), `errors`, `warnings` and `exit`, plus what the mode reports: `run_id`, `environment`, `engine`,
`transaction`, `user`, `migrations[]` (`id`, `source`, `state`, `action`, `detail`, `description`, `plan`,
`messages`, `duration_ms`, `record`, `checksum`, `changed`, ...), `summary`, `captions`, `restore`, `repository`,
`runs`, `orphans`, `pushes`, `lock`. Keys may be added in later versions; none is renamed or removed within
`exp-migration/1`.

## Related pages

- [Migrations: the hub](../../help/migrations/README.md), [All recipes](../../help/migrations/recipes/README.md),
  [Frequently asked questions](../../help/migrations/faq.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](../../help/migrations/in-the-administration.md),
  [Commands](../../help/migrations/commands.md), [Settings](../../help/migrations/settings.md),
  [Policies, views and events](../../help/migrations/policies-views-and-events.md)
- [The feature](../../features/6.0/migrations.md)
- [When a migration fails](../../help/migrations/when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../../help/migrations/migrations-versus-migrating-a-site.md)
- [migration.ini](../../examples/ini-settings/migration/README.md)
- [Audit event model](audit-event-model.md)
