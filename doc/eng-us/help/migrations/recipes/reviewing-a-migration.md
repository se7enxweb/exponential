---
title: "Reviewing a migration in code review"
audience: developer, software-engineer, php-developer, integrator
category: migrations
order: 14
summary: A checklist for reviewing a migration in a pull request - the file and header, the order, plan(), up() and down(), engines and transactions, ids that differ between installations, personal data, and what the reviewer runs before approving.
keywords: migration, migrations, code review, review, pull request, checklist, reviewer, header, requires, down, plan, schema change, database change, deploy, exp:migration:check, exp:migration:dry-run
type: help
---
# Reviewing a migration in code review

A migration is code that runs once on production and then can never change. That makes the review the last cheap
moment to catch a mistake. This page is the checklist a reviewer works through on a pull request that adds a file
to `migrations/` or `extension/<name>/migrations/`.

## What you'll have at the end

- a review that covers the file, its order, what it changes, how it is undone and where it runs;
- the commands the reviewer runs on a development installation before approving.

## Before you start

Check out the branch of the pull request on a development installation with a recent copy of the site's data.

## Steps

### 1. The file and the header

- [ ] The name is `YYYYMMDDHHMMSS_name.php`, the name says what it does (`news_note_priority`, not `fix2`).
- [ ] The file is **new**. A pull request that edits or renames a migration file that has run anywhere is
      refused: the checksum guard would stop every run there. The change belongs in a new migration.
- [ ] `description:` is one clear line; it appears in the status, the administration and the commit message of the
      run.
- [ ] `requires:` names every migration (and kernel step, and `extension:<name>`) whose result this one uses.
- [ ] `environments:` and `engines:` are set only when the change really is limited. Sample data never reaches
      production by accident: look for `environments: development, test` on it.
- [ ] `maintenance: yes` where visitors must not see the change half done.
- [ ] One namespaced class extending `\expMigrationPhp`, nothing outside the class.

### 2. The order

- [ ] The timestamp is later than every migration already on the main branch. A branch that waited weeks may carry
      an old timestamp: it then sorts before migrations that ran already, which is fine only when nothing in it
      depends on them. Otherwise ask for `requires:` (see [Team workflow with git](team-workflow-with-git.md)).

### 3. plan(), up() and down()

- [ ] `plan()` writes nothing and is cheap: it runs on every dry run and status page.
- [ ] `up()` uses `$context->db()` and the run's user, never a connection or a user of its own.
- [ ] `up()` throws on failure instead of returning quietly; return values of kernel calls are checked
      (`createAndPublishObject()`, `updateAndPublishObject()` return `false` or `null` on failure).
- [ ] What `up()` creates gets a remote id or an identifier, so `down()` and later migrations find it on every
      installation. No object, node or class ids written into the file.
- [ ] `down()` undoes exactly `up()`, is safe to run twice, or is absent on purpose with a reason in the
      description ([Writing a reversible down()](writing-a-reversible-down.md)).
- [ ] The returned messages say what was done and contain no secret, password or e-mail address.

### 4. Engines, transactions and size

- [ ] Raw SQL works on every engine the header allows, or `engines:` limits it ([Engine notes](engine-notes.md)).
- [ ] On MySQL/MariaDB and Oracle a schema change commits at once: is the order of statements the least harmful if
      it fails halfway?
- [ ] A migration over thousands of rows uses batches and `transactional()` returning `false`, and is idempotent
      ([Large data migrations](large-data-migrations.md)).

### 5. Data and privacy

- [ ] No personal data of real people in the file: the file is committed and pushed. Accounts are created with
      placeholder addresses or read their values from settings that stay out of git.
- [ ] No password, token or key in the file.

## Check it

The reviewer runs, on the branch:

```bash
./console exp:migration:check
./console exp:migration:migrate
./console exp:migration:dry-run --simulate --diff      # SQLite or PostgreSQL
./console exp:migration:migrate --apply --no-commit
./console exp:migration:rollback 20261101110000_news_note_priority --apply
./console exp:migration:migrate --apply --no-commit
```

and reads the diff of the simulation: every changed table must be one the description promises.

## Roll it back

A reviewer's local runs are taken back with `exp:migration:rollback <migration-id> --apply`, or, for a migration
without `down()`, with the run's caption: `./console exp:migration:rollback <run-id> --apply`.

## Next step

[Team workflow with git](team-workflow-with-git.md): branches, conflicts and the order of migrations from several
developers.

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Testing migrations](testing-migrations.md), [Develop with git](../../develop-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
