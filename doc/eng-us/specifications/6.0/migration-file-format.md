# Specification: the migration file format

This page is the normative description of a migration file of Exponential 6.0.15: the file name, the header, the
PHP class, the YAML document with its steps, placeholders, conditions and loops, the id map, the checksum, and the
formats of the record files a run writes (the status file and the run log), and the SQL and INI migration files. It
covers what the 6.0.15 kernel reads and writes. The guide is
[Migrations](../../guides/migrations.md).

Version of this specification: 1 (exp-migration/1, exp-migration-status/1, exp-migration-idmap/1).

## The file name

```text
YYYYMMDDHHMMSS_<name>.<type>
YYYYMMDDHHMMSS_<name>.<engine>.sql
YYYYMMDDHHMMSS_<name>.mongodb.json
```

| Part | Rule |
|---|---|
| `YYYYMMDDHHMMSS` | a valid date and time (checked as a calendar date) |
| `<name>` | letters, digits, `_` and `-` |
| `<type>` | `php`, `yml`, `sql` or `ini` |
| `<engine>` | only before `.sql`: `mysql`, `postgresql` (or `pgsql`), `sqlite` or `oracle`; MongoDB's file is `.mongodb.json` |

- The **id** is the file name without `.<type>` (and without `.<engine>`): `20261031120000_add_news_class`.
- Files of one id that differ only in the engine (`..._index.mysql.sql`, `..._index.pgsql.sql`,
  `..._index.mongodb.json` and a portable `..._index.sql`) are **one** migration of the type `sql` with a file per
  engine; the engine's own file wins, else the portable one. A portable file is for the SQL engines: without a
  `.mongodb.json` (or `engines` in the header) the migration does not run on MongoDB.
- One id has one type: a `.php` and a `.yml` with the same id are an error.
- A file whose name does not start with 14 digits and `_` is not a migration and is skipped (a README, a `.gitkeep`);
  one that does but breaks a rule above is an error.
- Only the files directly in a migrations folder are read; subfolders are not.
- Every type runs: `sql` and `ini` files run as the YAML migration of their steps ([the SQL migration](#the-sql-migration),
  [the INI migration](#the-ini-migration)).

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
| `transactional` | `yes` or `no`: `no` runs the migration without a transaction | `yes` |
| `events` | what its content changes put into the event journal of the GraphQL API (webhooks, subscriptions): `each` (every change, tagged with the migration), `summary` (one event at the end), `none` ([the event journal](../../features/6.0/graphql.md#the-event-journal)) | `each` |

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

## The YAML migration

A `.yml` file is read with symfony/yaml (`^5.4 || ^6.4 || ^7.0`; composer.json requires it, and
`migration.ini [YamlSettings] LibraryPath` names a copy outside the Composer `vendor/`). Without it every YAML
migration is refused with the install hint. Only plain YAML is read: a duplicate key is an error, `!php/...` tags
are refused, and a date without quotes is not a text (write `'2026-11-01'`). The header stays in the `#` comment
lines at the top; header keys written as YAML keys are an error.

### The document

The document is either the list of steps alone, or a map:

| Key | Value | Default |
|---|---|---|
| `steps` | the list of steps, run in order (required in the map form) | |
| `down` | the list of steps that undo the migration (`exp:migration:rollback <id>`) | none |
| `transactional` | `false`: no transaction around the migration | `true` |

Any other key is an error. A document without steps is valid and does nothing (a warning in `exp:migration:check`).

```yaml
# description: The languages of the news, checked before the import
steps:
    - type: reference
      set: { news_languages: [ eng-US, ger-DE ] }
    - foreach: "${ref.news_languages}"
      as: language
      steps:
          - type: assert
            that: { value: "${loop.language}", matches: "/^[a-z]{3}-[A-Z]{2}$/" }
down:
    - type: reference
      set: { news_languages: [] }
```

### A step

A step is a map. The keys the engine reads:

| Key | Value |
|---|---|
| `type` | the step type, as `migration.ini [StepSettings] StepTypeList[<name>]` registers it (required) |
| `mode` | the mode, one of those the step type's schema names (`create`, `update`, `ensure`, `delete` ...); the type's default when not given; a type without modes takes no `mode` |
| `if` | a condition (below): the step is skipped when it is false |
| `environments` | a list (or a comma-separated text) of environments; `!<name>` leaves one out (as in the header) |
| `engines` | a list of `mysql`, `postgresql`, `sqlite`, `oracle`, `mongodb`; `!<name>` leaves one out |
| `references` | a map `<reference name>: <value name>`: after the step, each reference is set to that value of the step's result |
| `comment` | a text, ignored |

Every other key belongs to the step type and is checked against its schema: unknown keys, missing required keys,
the type of each value (`string`, `int`, `number`, `bool`, `scalar`, `list`, `map`, `any`, `identifier`,
`condition`), the allowed values, and keys that belong to another mode are errors. A value with a placeholder is
checked when the step runs, after the placeholder is replaced; a step type adds its own checks
(`expMigrationStepType::validate()`). The type's defaults are filled in before it runs.

A reference name is letters, digits and `_`, not starting with a digit. `references:` may only name the values the
step type's schema lists.

### Loops

A step with `foreach` or `while` is a loop; it has no `type`.

| Key | Value | Default |
|---|---|---|
| `foreach` | a list, a map, or one placeholder that stands for one (`"${ref.languages}"`) | |
| `as` | `foreach` only: the name of the current item | `item` |
| `key` | `foreach` only: the name of the current key (the position in a list) | `key` |
| `while` | a condition, evaluated before each turn | |
| `max` | `while` only: the most turns; one more fails the migration | `[StepSettings] MaxLoopIterations` (1000) |
| `steps` | the loop's steps (required, not empty) | |
| `if`, `comment` | as for a step | |

Inside a loop `${loop.<as>}`, `${loop.<key>}` and `${loop.index}` (the turn, from 0) are set; a nested loop sees the
variables of the loops around it. A `${loop.<name>}` that no loop around it sets is an error. Loops nest 8 deep at most.

### Placeholders

`${<scope>.<name>[.<key>...]}` in a value is replaced right before the step runs:

| Scope | Stands for |
|---|---|
| `ref` | a reference: set by `references:` of an earlier step, by a `reference` step, or by an earlier migration of the same run (`$context->setReference()` in PHP) |
| `loop` | a variable of a loop around the step |
| `idmap` | `${idmap.<kind>.<name>}`: the id map of the run's environment (below) |
| `run` | `${run.id}`, `${run.environment}`, `${run.engine}` |

- A value that is one placeholder and nothing else keeps the type of what it stands for (a number, a list); inside a
  longer text the value is written into it (`true`/`false` for a boolean, nothing for `null`), and a list or map
  there is an error.
- `.<key>` goes into a list or map (`${ref.node.id}`, `${ref.list.0}`).
- `$${` is a literal `${`. Keys of maps are never replaced, only values.
- A placeholder that names nothing fails the step. In the dry run it stays as written and the plan says what is
  decided when the migration runs.

### Conditions

`if:`, `while:` and the `that:` of an `assert` step take a condition:

| Form | True when |
|---|---|
| `true`, `false` | as written |
| `"${ref.flag}"` | the value is not `null`, `false`, `0`, `""`, `"0"`, `"false"`, `"no"` or an empty list |
| `{ all: [ <condition>, ... ] }` | every one is true (an empty list is true) |
| `{ any: [ <condition>, ... ] }` | one is true (an empty list is false) |
| `{ not: <condition> }` | the condition is false |
| `{ ref: <name>, set: true }` | the reference is set (`set: false`: it is not) |
| `{ environment: <name or list> }` | the run's environment is one of them; `!<name>` as in the header |
| `{ engine: <name or list> }` | the database engine is one of them |
| `{ value: <value>, <operator>: <operand> }` | the comparison holds |

Operators: `equals`, `not_equals` (numbers and numeric texts compare as numbers), `in`, `not_in` (a list), `gt`,
`gte`, `lt`, `lte` (numbers), `matches` (a PCRE pattern with its delimiters), `empty` (`true` or `false`). Exactly
one operator per `value`.

### Identifiers and the id map

A key of type `identifier` names a node or an object: a number (the id), `remote:<remote id>`,
`object-remote:<remote id>` (a node: the main node of that object) or `path:<url path>` (`path:media/images`).
Ids differ between installations; a site keeps the names of its ids per environment in
`<repository>/idmap/<environment>.json` (`default.json` without an environment):

```json
{
    "schema": "exp-migration-idmap/1",
    "ids": {
        "node": { "news": 1234, "media": "remote:media-root" }
    }
}
```

Kinds and names are letters, digits, `_` and `-`; a value is a number or an identifier text. A missing file is an
empty map; a file that is not one fails every YAML migration of the run.

### Step types

The kernel registers `reference` (`set:` a map of references), `assert` (`that:` a condition, `message:` the text
of the failure), `content`, `location`, `content_version`, `content_class`, `content_class_group`, `user`,
`user_group`, `role`, `section`, `language`, `object_state_group`, `tag`, `url_alias`, `trash`, `file`, `image`,
`cache`, `search`, `autoload`, `layout`, `layout_block`, `layout_rule`, `http`, `mail` and `command`;
every key of each is on its generated page ([the step types](../../examples/migration-steps/README.md)). An
identifier key names its kind: `node`, `object`, `class`, `section`, `language`, `user`, `role`, `state`,
`class_group`, `state_group`, `tag` or `url_alias`; all but `node`, `object` and `tag` also take a name (a class or
section identifier, a locale, a login, a role name, `<group>/<state>`, the name of a class group, the identifier of a
state group, the address of a URL alias from the root). A tag is named by its id, `remote:<remote id>` or
`path:<keyword path>`, and only where the extension eztags is installed. The steps `file` and `image` read their
`file:` below the folder of the migration file. The caches every step declares are cleared once after the run, and
the objects the steps changed are reindexed ([caches and the search index](../../guides/migrations-yaml-steps.md#caches-the-search-index-and-autoloads)).
The layout steps work only where Exponential Layouts is installed; `http`, `mail` and `command` send and run nothing in
a dry run, and `command` runs only the allowlisted `exp:*` commands
([HTTP requests, mails and console commands](../../guides/migrations-yaml-steps.md#http-requests-mails-and-console-commands)).
An extension registers its own step types in its
`migration.ini.append.php`:

```ini
[StepSettings]
StepTypeList[news_import]=expNewsImportStep
```

The class implements `expMigrationStepType` (`name()`, `schema()`, `validate()`, `caches()`, `plan()`, `apply()`;
`expMigrationStepTypeBase` has the defaults). `apply()` returns `lines` (what it did) and `values` (what
`references:` can take). A class that does not exist, answers another name or has a broken schema is not registered
and is an error of every YAML migration that is checked.

### Running and undoing

- `up()` runs the steps in order; the first that fails stops the migration with its place
  (`steps[1].steps[0] (assert): ...`), and the migration's transaction is rolled back.
- `plan()` (the dry run) lists each step's plan; values set by a `reference` step are used for the steps after it,
  everything else that is only known when the migration runs is marked so.
- `down()` runs the `down:` steps; without `down:` a migration can be undone only when every step's type implements
  `expMigrationRevertibleStepType` and none is a loop: each step's `revert()` runs, the last first, with the
  references of the run that rolls back. `reference` and `assert` revert to nothing.

## The SQL migration

The files of one id (`.sql`, `.<engine>.sql`, `.mongodb.json`) are one migration. On an engine, its own file runs,
else the portable `.sql` (on the SQL engines only). An SQL file is statements that end with `;` at the end of a line;
lines that start with `--` are comments; a line `-- down` (also `-- down:`, any case) ends the statements that apply
the migration and starts those that undo it. A `.mongodb.json` file is a JSON list of commands, or an object with
`up` and `down` lists (and `description`); a command is an object with one of `insert` (`documents`), `update`
(`filter`, `set`, `unset`), `delete` (`filter`), `create_collection`, `drop_collection`, `create_index` (`keys`,
`name`, `unique`) and `drop_index` (`name`), whose value is the collection. The migration is the YAML migration
`steps: [ { type: sql, file: <id>.sql } ]`, with `down: [ { type: sql, file: <id>.sql, part: down } ]` when a file
has a down part, and `transactional` from the header. The checksum is that of the engine's file.

## The INI migration

An INI file whose blocks name their file: `[<file>.ini/<Block>]` for `settings/override/<file>.ini.append.php`,
`[siteaccess:<name> <file>.ini/<Block>]` for `settings/siteaccess/<name>/<file>.ini.append.php` (`override` or
`global` before the file name is the same as none). In a block: `Var=value` sets, `Var[]` followed by `Var[]=value`
lines makes the array these values, `Var[]=value` without `Var[]` before it adds a value, `Var[key]=value` sets a
key, and `-Var`, `-Var[key]`, `-Var[]=value` remove. Lines starting with `#` or `;` are comments. The migration is
the YAML migration of one `settings` step per file, in the order the files first appear; it has no `down:`.

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

## Recorded events (exp-migration-recording-event/1)

A recording of changes ([the guide](../../guides/migrations-generators-and-recording.md)) keeps each change as one
event, the payload of a row of `exp_migration_recording_event`: a JSON object in the shape of the YAML step it stands
for, so that a generator writes the step without reading the installation again.

| Key | Meaning |
|---|---|
| `v` | `1` |
| `audit` | the audit event's name, from the kernel's taxonomy (`content.object.publish` ...) |
| `subject` | what it is about, in stable names: `object remote:<id>`, `location remote:<id>`, `class <identifier>`, `role <name>`, `section <identifier>`, `state_group <identifier>`, `user remote:<id>`, `url_alias <path>`, `setting <file> [<block>] <variable>`, `workflow_trigger <keys>` |
| `step` | the step: `type`, `mode`, and the keys of that step (below), every id in a stable form: nodes and objects as `remote:<remote id>` (see [Identifiers and the id map](#identifiers-and-the-id-map)), classes, sections and object states by identifier, roles by name, languages by locale; the values are the state after the change |
| `omitted` | what was left out on purpose: the account and personal fields of users, the value of a secret setting |
| `notes` | what a generator must know: a value kept as the kernel stores it, the languages a translation added |

The event never names who made the change; the row keeps the user id (`actor_id`) for the installation only. The
rows of a recording sort by `event_id` (the millisecond, a sequence of the process, five random hex digits) in the
order the changes were made.

The audit events, the step each becomes and its keys:

| Audit event | `type` / `mode` | Keys |
|---|---|---|
| `content.object.create` | `content` / `create` | `class`, `parent`, `remote_id`, `section`, `always_available`, `initial_language`, `states`, `fields`, `locations` |
| `content.object.publish`, `.translate`, `.translation.remove`, `.always_available`, `.initial_language`, `.state` | `content` / `update` | `match`, `section`, `always_available`, `initial_language`, `states`, `fields`, `locations`; `changed` (publish), `remove_translations` (translation removed) |
| `content.object.remove`, `content.object.purge` | `content` / `delete` | `match` |
| `content.object.restore` | `trash` / `restore` | `object`, `parent` |
| `content.node.add` | `location` / `create` | `object`, `parent`, `remote_id` |
| `content.node.move` | `location` / `move` | `match`, `parent` |
| `content.node.copy` | `content` / `copy` | `node`, `parent`, `remote_id`, `location_remote_id`, `subtree` |
| `content.node.hide`, `content.node.reveal` | `location` / `hide`, `reveal` | `match` |
| `content.node.sort`, `.priority`, `.main` | `location` / `update` | `match`; `sort_field`, `sort_order` / `child_priorities` / `main` |
| `content.node.swap` | `location` / `swap` | `match`, `with` |
| `content.node.remove` | `location` / `delete` | `match` |
| `content.node.remove.trash` | `trash` / `move` | `match` |
| `content.node.section` | `section` / `assign` | `match`, `subtree` |
| `content.class.create`, `content.class.copy` | `class` / `create` | `identifier`, `names`, `descriptions`, `contentobject_name`, `url_alias_name`, `is_container`, `always_available`, `sort_field`, `sort_order`, `groups`, `remote_id`, `attributes` |
| `content.class.change` | `class` / `update` | `match` and the keys of create but `identifier` |
| `content.class.remove` | `class` / `delete` | `match` |
| `content.section.change` | `section` / `ensure` | `match`, `identifier`, `name`, `navigation_part` |
| `content.section.remove` | `section` / `delete` | `match` |
| `content.state.change` | `object_state_group` / `ensure` | `match`, `identifier`, `names`, `states`, `default_language` |
| `content.state.remove` | `object_state_group` / `delete` | `match` |
| `content.urlalias.change` | `url_alias` / `ensure` (added), `delete` (removed) | `path`, `node` / `match`, `which` |
| `access.role.create`, `access.role.copy` | `role` / `create` | `name`, `policies` |
| `access.role.change`, `access.policy.add`, `access.policy.remove` | `role` / `update` | `match`, `policies` |
| `access.role.remove` | `role` / `delete` | `match` |
| `access.role.assign`, `access.role.unassign` | `role` / `assign`, `unassign` | `match`, `user` or `group`, `limitation` |
| `access.user.create` | `user` / `create` | `remote_id`, `class`, `groups`, `enabled`, `fields` (only `[RecordingSettings] UserKeepAttributeList[]`) |
| `access.user.change`, `.move`, `.activate`, `.lock`, `.unlock` | `user` / `update` | `match`, `class`, `groups`, `enabled`, `fields` |
| `access.user.remove` | `user` / `delete` | `match` |
| `system.setting.write` | `ini` / `set`, `remove` | `file`, `scope`, `block`, `variable`, `value` or `secret` |
| `system.workflow.trigger.change` | `workflow_trigger` / `ensure` | `triggers` |

A content object of a user class is recorded as a `user` step. `[RecordingSettings] EventList[]` may narrow the list;
it never widens it. Events and keys may be added within `exp-migration-recording-event/1`; none is renamed or removed.

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
