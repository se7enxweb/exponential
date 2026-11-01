---
title: "Migrations for software engineers"
audience: software-engineer, developer
category: migrations
order: 1000
summary: The migration model of Exponential 6.0.15 for engineers - ordered, recorded, checksum-guarded, captioned and committed changes - its architecture next to the .dba schema files and the upgrade steps, the design rules for migrations that run the same on every installation, and how to test and review them.
keywords: migration, migrations, schema change, database change, deploy, exp:migration:migrate, software engineer, architecture, expMigrationPhp, exp_migration, idempotent, detect, remote id, down, CI, code review, upgrade step, db_schema.dba
featured: true
type: help
---
# Migrations for software engineers

This page is for engineers who own the way a site's database, content and settings change over time: you design
the changes, set the rules for your team, wire them into CI and review them. It explains the model behind the
migrations of Exponential 6.0.15, where they sit in the architecture, and the rules that make a migration run the
same on development, staging and production.

## The model in five words

A migration is a change written once in a file `YYYYMMDDHHMMSS_name.php` and applied to every installation of a
site. Five properties make it safe:

| Property | What guarantees it |
|---|---|
| **Ordered** | migrations run by id (timestamp, then name), and after every migration their header names in `requires:` |
| **Recorded** | each installation records what ran in `exp_migration` (one row per migration) and `exp_migration_run` (one row per run); a done migration never runs there again |
| **Checksum-guarded** | the record keeps a checksum of the file; a done migration whose file changed stops the run unless `--force --reason=<text>` accepts it |
| **Captioned** | a caption (database dump, `var/` storage, `settings/override`, code commits) is taken before a run and restored after a failure |
| **Committed** | the status file and the run log are committed, tagged `migrations/<environment>/<run-id>` and pushed after every run |

The result: any clone of the repository tells which environment applied what, and any installation can be brought
to the same state by running the pending migrations in order.

## The architecture

| Layer | Pieces |
|---|---|
| Files | `migrations/` of the site, `extension/<name>/migrations/` of active extensions, `migrations/` of installed packages (sources `site`, `extension:<name>`, `package:<name>`) |
| Kernel classes (`kernel/classes/migration/`) | `expMigrationLocator` finds files, `expMigrationGraph` orders them, `expMigrationPhp` is the base class, `expMigrationContext` is what a migration works on, `expMigrationRunner` runs them, `expMigrationRunFlow` wraps captions, restore, commit, tag and push |
| Record | tables `exp_migration` and `exp_migration_run`, made by the upgrade step `6.0.15-migration-tables`; `migrations/status/<environment>.json` and `migrations/runs/<run-id>.log` in git |
| Lock | `var/<site>/upgrade.lock`, shared with `exp:upgrade`, with a holder file that names who holds it |
| Commands | `exp:migration:status`, `:migrate`, `:dry-run`, `:rollback`, `:resume`, `:caption`, `:push`, `:check`, `:generate` |
| Administration | Setup > Migrations (module `migration`), the dashboard block, the website sidebar panel |

The kernel's own upgrade steps are migrations too, of the source `kernel`. `exp:migration:status` lists them first
and a run applies the pending ones of the stages `database` and `scripts` before the site's migrations; the stages
`code`, `libraries` and `finish` stay with `exp:upgrade` and are listed as `deferred`. A site migration that needs a
step's table names the step: `requires: 6.0.15-migration-tables`.

## Where migrations fit

| Change | Tool |
|---|---|
| the schema a **new** installation is created from | `share/db_schema.dba` of the kernel or the extension |
| what **every** installation of the kernel or an extension needs on upgrade | an upgrade step, run by `exp:upgrade` ([Upgrade steps for extensions](../../guides/upgrade-steps-for-extensions.md)) |
| what **one site** changes once: its tables, classes, content, roles, settings | a migration |
| moving or upgrading a whole installation | not a migration: [Migrations versus migrating a site](../migrations/migrations-versus-migrating-a-site.md) |

[Upgrade step or migration?](../migrations/recipes/upgrade-step-or-migration.md) walks through the choice, and
[Schema changes and migrations](schema-changes-and-migrations.md) covers the `.dba` side.

## Design rules for reliable migrations

1. **One change per migration.** Small migrations fail small, are easy to review and easy to take back. Split a
   schema change and the data that fills it into two migrations on MySQL/MariaDB, where DDL commits at once.
2. **Find things by remote id or identifier, never by numeric id.** Object, node and class ids differ between
   installations. Create objects with a `remote_id` you choose; look up classes by identifier and roles by name.
3. **Make `up()` safe to meet a half-done state.** Check before you create (`expKernelTableSchema::exists()`,
   `eZContentObject::fetchByRemoteID()`), so a second run after a partial failure completes the change.
4. **Give `detect()` when the installation can tell.** A migration that can see its change (a table, a class) returns
   `\expMigration::DONE` from `detect()`; installations that got the change another way do not run it.
5. **Write `down()` when the change can be undone by itself**, and leave it out when it cannot (a removal of
   content). Without `down()` the caption is the way back.
6. **Keep `plan()` cheap and pure.** It runs on every dry run and status page; it must not write.
7. **No secrets, no personal data.** The output goes to the run log, the record and the audit log; the log is
   committed and pushed. Read credentials from settings, never write them into a migration.
8. **Use the context.** `$context->db()` is the run's connection inside its transaction; a connection of your own is
   outside it.
9. **Never edit a migration that ran.** Write a new one. The checksum guard enforces it.

## Testing and CI

| Check | Command | Needs a database |
|---|---|---|
| the lint: names, headers, order, PHP syntax, one class | `./console exp:migration:check` | no |
| what would run | `./console exp:migration:dry-run` | yes |
| a real run, rolled back (PostgreSQL, SQLite) | `./console exp:migration:dry-run --simulate --diff` | yes |
| nothing pending after a deploy | `./console exp:migration:status --fail-on-pending` | yes |
| `up()`, `down()` and `up()` again | a PHPUnit test on a throwaway SQLite database | SQLite |

Every command takes `--format=json` (`exp-migration/1`) and exits 0, 1 (failed or pending), 2 (usage), 3 (database
unreachable) or 4 (internal). [Testing migrations](../migrations/recipes/testing-migrations.md) has the CI job and the
PHPUnit test.

## Review

A reviewer reads a migration for order (`requires`), scope (one change), ids (remote ids, identifiers), the way back
(`down()` or the caption), engines (what MySQL/MariaDB commits at once, what MongoDB never rolls back) and output
(no secrets). [Reviewing a migration in code review](../migrations/recipes/reviewing-a-migration.md) is the
checklist.

## A reading path through the recipes

1. [Add a database table](../migrations/recipes/add-a-database-table.md) and
   [add a column or an index](../migrations/recipes/add-a-column-or-an-index.md)
2. [Writing a reversible down()](../migrations/recipes/writing-a-reversible-down.md)
3. [Team workflow with git](../migrations/recipes/team-workflow-with-git.md)
4. [Testing migrations](../migrations/recipes/testing-migrations.md) and
   [deploying with migrations](../migrations/recipes/deploying-with-migrations.md)
5. [Large data migrations](../migrations/recipes/large-data-migrations.md) and
   [engine notes](../migrations/recipes/engine-notes.md)
6. [Captions and rollback in practice](../migrations/recipes/captions-and-rollback-in-practice.md)

YAML migrations will have their own pages when the YAML engine lands.

## Where to go next

- [All recipes](../migrations/recipes/README.md)
- [PHP migrations](../../guides/migrations-php.md): every method and context call
- [The migration file format](../../specifications/6.0/migration-file-format.md): the normative rules
- Start now: [the tutorial](../../guides/migrations-tutorial.md)

## Related pages

- [Migrations: the hub](../migrations/README.md)
- [Migrations: the guide](../../guides/migrations.md)
- [Migrations: the tutorial](../../guides/migrations-tutorial.md)
- [PHP migrations](../../guides/migrations-php.md)
- [Migrations in the administration](../migrations/in-the-administration.md)
- [The migration commands](../migrations/commands.md)
- [The migration settings](../migrations/settings.md)
- [The migration file format](../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../migrations/when-a-migration-fails.md)
- [Troubleshooting migrations](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations/migrations-versus-migrating-a-site.md)
- [Software engineers: start here](software-engineers-start-here.md)
- [Schema changes and migrations](schema-changes-and-migrations.md)
- [Persistence and the database layer](persistence-and-the-database-layer.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
