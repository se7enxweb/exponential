---
title: "Migration commands"
audience: developer, admin, software-engineer, php-developer, hosting
category: migrations
order: 20
summary: Every exp:migration command in one table - what it does, when to use it, its most used options and a link to its generated reference page - with the options all of them share, the exit codes, the machine output and the exp:upgrade commands that work with migrations.
keywords: exp:migration, exp:migration:status, exp:migration:migrate, exp:migration:dry-run, exp:migration:rollback, exp:migration:resume, exp:migration:caption, exp:migration:push, exp:migration:check, exp:migration:generate, exp:migration:convert, exp:migration:repo:split, console, command line, CLI, options, exit codes, json, exp-migration/1, --apply, --format, migration commands
type: help
---
# Migration commands

The migration commands run from the installation root as `./console exp:migration:<name>` (the same program as
`php bin/php/expmigration<name>.php`). Run them as the user the web server runs as; as root, add `--allow-root-user`.
Every command that writes is a **dry run** until it is given `--apply`. Each command's full list of options is on
its generated reference page, made from the command's own option definitions.

## Contents

1. [The commands](#the-commands)
2. [By task](#by-task)
3. [Options most commands share](#options-most-commands-share)
4. [Exit codes and the machine output](#exit-codes-and-the-machine-output)
5. [The exp:upgrade commands that work with migrations](#the-expupgrade-commands-that-work-with-migrations)
6. [Related pages](#related-pages)

## The commands

| Command | What it does | Most used options | Reference |
|---|---|---|---|
| `exp:migration:status` | every migration with its state (`done`, `pending`, `not-needed`, `failed`, `unknown`), the orphans, the latest runs, the pushes that wait, the lock | `--fail-on-pending`, `--runs`, `--environment` | [expmigrationstatus](../../examples/commands/expmigrationstatus.md) |
| `exp:migration:migrate` | the dry run, and with `--apply` the run: caption, migrations in order, restore on failure, re-caption, commit, tag, push | `--apply`, `--only`, `--path`, `--environment`, `--login`, `--no-commit`, `--no-push`, `--force --reason` | [expmigrationmigrate](../../examples/commands/expmigrationmigrate.md) |
| `exp:migration:dry-run` | the plan of every pending migration; `--simulate` runs them in a transaction that is rolled back (PostgreSQL, SQLite), `--diff` shows what changed | `--simulate`, `--diff`, `--only`, `--path` | [expmigrationdryrun](../../examples/commands/expmigrationdryrun.md) |
| `exp:migration:rollback` | takes back a run (its pre caption), one migration (its `down()`), or, for experts, any caption | `<run-id>`, `<migration-id>`, `--expert --confirm=<caption-id>`, `--parts` | [expmigrationrollback](../../examples/commands/expmigrationrollback.md) |
| `exp:migration:resume` | a new run of what a failed or interrupted run left pending | `--apply`, `--only` | [expmigrationresume](../../examples/commands/expmigrationresume.md) |
| `exp:migration:caption` | a caption of the installation now, or the list of captions | `--apply`, `--list`, `--label` | [expmigrationcaption](../../examples/commands/expmigrationcaption.md) |
| `exp:migration:push` | pushes the runs whose push failed | `--apply`, `--limit` | [expmigrationpush](../../examples/commands/expmigrationpush.md) |
| `exp:migration:check` | the lint of the migration files, without a database: names, headers, order, PHP syntax, the class | `--path`, `--environment` | [expmigrationcheck](../../examples/commands/expmigrationcheck.md) |
| `exp:migration:generate` | writes a new PHP migration from the skeleton | `--skeleton <name>`, `--description`, `--author`, `--source=extension:<name>`, `--apply` | [expmigrationgenerate](../../examples/commands/expmigrationgenerate.md) |
| `exp:migration:convert` | converts YAML migrations of the reference bundle into YAML migrations of Exponential; what does not convert is listed in the new file | `<file or folder>`, `--source=extension:<name>`, `--apply` | [expmigrationconvert](../../examples/commands/expmigrationconvert.md) |
| `exp:migration:repo:split` | splits the migrations folder into a repository of its own, with its history | `--to`, `--remote`, `--branch`, `--apply` | [expmigrationreposplit](../../examples/commands/expmigrationreposplit.md) |

## By task

| Task | Commands | Recipe |
|---|---|---|
| write a migration | `exp:migration:generate --skeleton <name> --apply`, then `exp:migration:check` | [the tutorial](../../guides/migrations-tutorial.md) |
| see what would change | `exp:migration:migrate`, `exp:migration:dry-run --simulate --diff` | [Testing migrations](recipes/testing-migrations.md) |
| apply the pending migrations | `exp:migration:migrate --apply` | [Deploying with migrations](recipes/deploying-with-migrations.md) |
| gate a pull request in CI | `exp:migration:check`, `exp:migration:status --fail-on-pending` | [Testing migrations](recipes/testing-migrations.md) |
| go back | `exp:migration:rollback <run-id> --apply`, `exp:migration:rollback <migration-id> --apply` | [Captions and rollback in practice](recipes/captions-and-rollback-in-practice.md) |
| finish an interrupted run | `exp:migration:resume --apply` | [Large data migrations](recipes/large-data-migrations.md) |
| push what did not reach the remote | `exp:migration:push --apply` | [Pushing tags](recipes/pushing-tags.md) |
| take a safe point by hand | `exp:migration:caption --apply --label="before the import"` | [Captions and rollback in practice](recipes/captions-and-rollback-in-practice.md) |

## Options most commands share

| Option | Meaning |
|---|---|
| `--apply` | make the changes; without it, a dry run that changes nothing |
| `--format=json` | the machine output `exp-migration/1` instead of text |
| `--environment=<name>` | count the migrations for another environment than the installation's (`site.ini [AdminEnvironment] Level`, or `EXP_ENV` with `Level=auto`) |
| `--login=<login>` | run as this user instead of `migration.ini [RunSettings] RunAsUser` |
| `--only=<ids>` | only these migrations (a comma list or repeated) |
| `--path=<files or folders>` | only the migrations in these files or folders; `exp:migration:check` checks them instead of the migration folders |
| `--run-id=<id>`, `--progress=<file>` | name the run before it starts, and follow it through a file of JSON lines (what the administration uses) |

Every script also takes the standard options of the script framework (`--help`, `--siteaccess`, `--debug`,
`--quiet`, `--allow-root-user`).

## Exit codes and the machine output

| Code | Means |
|---|---|
| 0 | done, or nothing to do |
| 1 | a migration failed, a check found a problem, the lock was held, a changed file was refused, or something is pending with `exp:migration:status --fail-on-pending` |
| 2 | usage: an option is wrong or missing, or uncommitted code refused a run |
| 3 | the database is not reachable |
| 4 | internal error |

`--format=json` writes `exp-migration/1`: the run, the environment, the engine and its transaction mode, every
migration with its state, action and messages, the captions, the repository, a summary, the errors, the warnings and
the exit code ([The machine output](../../specifications/6.0/migration-file-format.md#the-machine-output)).

```bash
./console exp:migration:status --fail-on-pending --format=json > var/log/migration-status.json || echo "pending or failed"
```

## The exp:upgrade commands that work with migrations

The kernel's upgrade steps are migrations of the source `kernel`, and `exp:upgrade` shares the record, the lock and
the captions with the migrations:

| Command | With migrations |
|---|---|
| [`exp:upgrade`](../../examples/commands/upgrade.md) | creates the record tables (step `6.0.15-migration-tables`); runs every stage, also `code`, `libraries` and `finish`, which a migration run leaves to it |
| [`exp:upgrade:status`](../../examples/commands/upgradestatus.md) | the steps and their state; a step applied by a migration run is `done` here too |
| [`exp:upgrade:captions`](../../examples/commands/upgradecaptions.md) | lists the captions of upgrades and of migration runs (kinds `migration-pre`, `migration-post`, `migration-failure`) |
| [`exp:upgrade:rollback`](../../examples/commands/upgraderollback.md) | restores any caption, also one a migration run took |
| [`exp:upgrade:finish`](../../examples/commands/upgradefinish.md) | after a migration run that applied steps, or after a restore: deploy, caches, smoke check |
| [`exp:upgrade:verify`](../../examples/commands/upgradeverify.md) | finds drift: a step recorded done whose effect is missing |

## Related pages

- [Migrations: the hub](README.md), [All recipes](recipes/README.md), [Frequently asked questions](faq.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](in-the-administration.md), [Settings](settings.md),
  [Policies, views and events](policies-views-and-events.md)
- [The file format](../../specifications/6.0/migration-file-format.md),
  [The feature](../../features/6.0/migrations.md)
- [When a migration fails](when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](migrations-versus-migrating-a-site.md)
- [All console commands](../../examples/commands/README.md), [Upgrading with the console](../../guides/upgrading-with-the-console.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
