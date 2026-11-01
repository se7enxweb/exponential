---
title: "Team workflow with git: branches, conflicts and order"
audience: developer, software-engineer, php-developer, integrator
category: migrations
order: 15
summary: How several developers write migrations on branches without stepping on each other - timestamps and order, requires for real dependencies, two migrations with the same timestamp, rebasing, renaming only before a merge, and the status files after a merge.
keywords: migration, migrations, git, branch, branches, merge, rebase, conflict, team, order, timestamp, requires, pull request, status file, schema change, database change, deploy, exp:migration:check, exp:migration:status
featured: true
type: help
---
# Team workflow with git: branches, conflicts and order

Migrations are files in git, so a team works on them the way it works on code: on branches, through pull requests.
Two things are different. Their **order** comes from their names, and a migration that has **run anywhere** must
never change again. This recipe shows the workflow that respects both.

## What you'll have at the end

- a branch per change, with its migration named at the time it is written;
- `requires:` where one migration really depends on another;
- clear rules for renaming, rebasing and merge conflicts.

## Before you start

- Every developer has a development installation of the site and runs migrations there with
  `--no-commit` or with `[RepositorySettings] Push=disabled`, so no record leaves the laptop.
- The main branch is protected and CI runs `./console exp:migration:check` on every pull request
  ([Testing migrations](testing-migrations.md)).

## Steps

### 1. One branch, one change, its own migration

```bash
git switch -c feature/news-priority
./console exp:migration:generate --skeleton news_note_priority --description="News notes get a priority (default 0)" --apply
```

The id starts with the time of now, to the second: `20261101110000_news_note_priority`. Two developers who generate
on the same day get different ids, and both files merge without a conflict, because they are different files.

### 2. Order: timestamps first, requires for dependencies

Migrations run in id order: timestamp, then name. When two branches are merged, their migrations interleave by
time. That is right as long as they are independent. When one uses what the other creates, say so:

```php
// requires: 20261101090000_news_note_table
```

`requires:` wins over the timestamps: the migration runs after the one it names, wherever their names put them.
A requirement that names no migration, and a cycle, fail `exp:migration:check` in CI before the merge.

### 3. Two migrations with the same timestamp

It happens when a developer copies a file instead of generating it. The ids still differ by name
(`20261101110000_news_note_priority` and `20261101110000_news_tags`) and sort by name; when they must run in a given
order, add `requires:`. Two files with the **same id** in different sources (the site and an extension) are an
error: rename the one that has run nowhere yet.

### 4. Rebasing a long-lived branch

A branch that waited weeks carries a migration with an old timestamp. After `git rebase main` it sorts before
migrations other developers merged and production already ran. The runner applies it anyway (a pending migration
runs whatever its position), but on a fresh installation it would run before them. If it depends on them, add
`requires:`. If you want the name to tell the truth, rename it **now**, before the merge:

```bash
git mv migrations/20261015090000_news_tags.php migrations/20261101120500_news_tags.php
```

Rename only a migration that has run on no shared installation. Your own development installation then lists the
old id as an orphan (`recorded as done, but its file is not found any more`); roll it back before renaming, or leave
the harmless row.

### 5. After the merge: never edit, always add

Once a migration is on the main branch, staging or production may have run it. A bug fix is a **new** migration:

```text
FAIL: 20261101110000_news_note_priority changed after it was applied (checksum 3c1f9a0b2d41 -> 8e77d0c45a19); a run with --force --reason=<text> accepts the change
FAIL (exit 1)
```

`--force --reason=<text>` is only for changes that do not change what the migration does, such as a comment.

### 6. Conflicts in the status files

The record of each environment is written to `migrations/status/<environment>.json` and `migrations/runs/` by the
runs themselves, never by hand. Each environment writes only its own file, so two environments never conflict. If a
merge does conflict in a status file, take the version of the branch that the environment's runs commit to and let
the next run write it again; the database, not the file, is the record.

## Check it

On the merge result:

```bash
./console exp:migration:check
./console exp:migration:status
```

```text
Exponential migrations: status
Run 20261101-121530-0a8c3e, environment development, database sqlite (transactions: full)

  done        20261101090000_news_note_table               site               A table for news notes, with a first note
  pending     20261101110000_news_note_priority            site               News notes get a priority (default 0)
  pending     20261101120500_news_tags                     site               Tags for news notes

Summary: 3 migration(s): 1 done, 2 pending, 0 not needed, 0 failed, 0 unknown; 2 would-apply
PASS
```

## Roll it back

A branch that is abandoned after its migration ran on development: `./console exp:migration:rollback <id> --apply`
before you switch back to main, otherwise the record keeps an orphan.

## Next step

Ship the merged result: [Deploying with migrations](deploying-with-migrations.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Reviewing a migration](reviewing-a-migration.md), [The migrations repository](../../../guides/migrations-repositories.md),
  [Develop with git](../../develop-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
