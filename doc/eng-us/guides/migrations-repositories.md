# Migrations: the migrations repository, commit, tag and push

Every migration run leaves a record in git: a status file that says what an environment has applied, and the log of
the run. After every run, the failed ones too, both are committed, the commit is tagged and pushed. This page
explains where that repository is, how it is set up, what a commit and a tag look like, and what happens when a push
fails.

[Guides](README.md) · [Migrations](migrations.md) · Related: [Rollback and captions](migrations-rollback-and-captions.md),
[Troubleshooting](migrations-troubleshooting.md), [migration.ini \[RepositorySettings\]](../examples/ini-settings/migration/RepositorySettings.md)

## In short

- The record goes to the folder `migrations/` of the installation's own git checkout (`Mode=site`, the default), or
  to a git repository of its own (`Mode=separate`).
- One run is one revision: one commit and one tag, `migrations/<environment>/<run-id>`.
- A push that fails stays local; the cronjob part `migrationpush` and `./console exp:migration:push --apply` push it
  again.
- Without git_manager nothing is committed, and the report says so.

## What is written

| File | Content |
|---|---|
| `migrations/status/<environment>.json` | the rows of `exp_migration` and the latest runs of that environment (schema `exp-migration-status/1`) |
| `migrations/runs/<run-id>.log` | one line per event of the run: `<UTC time> <level> <text>` |

Neither file holds a secret or personal data: the status leaves out who ran a migration and every message; a log
line is cleaned of secret values, credentials in URLs, `password=...` pairs, bearer tokens and e-mail addresses before
it is written. The status file is written whole, sorted and with the times of the record only, so writing it again
without a change gives the same bytes and no diff. An installation without an environment writes `default.json`.

A status file of the [tutorial](migrations-tutorial.md), shortened:

```json
{
    "schema": "exp-migration-status/1",
    "environment": "development",
    "engine": "sqlite",
    "updated_at": "2026-11-01T02:30:20Z",
    "summary": { "done": 2, "total": 2 },
    "migrations": [
        {
            "source": "site",
            "id": "20261031120000_news_note_table",
            "state": "done",
            "file": "migrations/20261031120000_news_note_table.php",
            "checksum": "5b5c92d15691663414cea9eca82de62e44f0fc1e890d0d2c8939e7728615060d",
            "stage": "migration",
            "version": "",
            "run_id": "20261031-193020-1356d6",
            "started_at": "2026-11-01T02:30:20Z",
            "finished_at": "2026-11-01T02:30:20Z",
            "duration_ms": 3,
            "caption_id": "",
            "revision": ""
        }
    ],
    "runs": [
        {
            "run_id": "20261031-193020-1356d6",
            "state": "done",
            "caption_pre": "",
            "caption_post": "",
            "commit": "",
            "tag": "",
            "push_state": "",
            "log": "runs/20261031-193020-1356d6.log"
        }
    ]
}
```

The JSON schema of the file is `share/migration/schema/exp-migration-status-1.json`.

## The two modes

```ini
# settings/override/migration.ini.append.php
[RepositorySettings]
Mode=site
Path=migrations
Remote=origin
Branch=
```

| Mode | `Path` is | Commits go to |
|---|---|---|
| `site` (default) | a folder of the installation's own checkout | the installation's repository; only the run's two files are committed (`git commit -- <files>`), whatever else is staged stays as it is |
| `separate` | a git repository of its own (relative to the root, or absolute) | that repository; the site's history stays free of run records |

With `Mode=site` the migration files and the record of their runs live side by side in `migrations/`; the locator
only reads the files directly in the folder, never `status/` or `runs/`.

`Branch` is the branch a run commits on; empty means the branch the repository is on. A repository on another branch,
or on no branch, is not committed to, and the report says why. A checkout that belongs to another system user than
the process is left alone, since the files git would write would belong to the wrong user.

## The commit and the tag

After the run, the two files are added and committed with a message such as:

```text
Updated: Migration run 20261031-193020-1356d6 in development: 2 applied

- applied site:20261031120000_news_note_table
- applied site:20261031120100_news_folder
```

A failed run says `... in development failed` and lists the restore. When the repository has no `user.email`, the
commit is made as `Exponential migrations`. The commit is then tagged (annotated) with `[RepositorySettings]
TagPattern`, by default `migrations/<environment>/<run-id>`; the placeholders are `<environment>` (`default` when
the installation names none), `<run-id>` and `<date>` (`YYYYMMDD`). **A tag that exists is never moved or replaced**:
the commit then stays untagged and the report says so. The run's row records `commit_id`, `tag_name` and
`push_state`, and every migration it applied gets the commit as its `revision`.

The output of a run ends with one line about it:

```text
Repository: commit 1a2b3c4d5e6f, tag migrations/production/20261031-120000-a1b2c3, push pushed
```

## The push

The branch and the tag are pushed to `[RepositorySettings] Remote` (`origin` by default): any git remote, on GitHub,
GitLab or a server of your own. Credentials are those of the remote as git_manager and the system user see them
(an SSH key, a credential helper); nothing is stored by the migrations. Every git command goes through git_manager's
runner: no shell, a time limit.

| `push_state` | Means |
|---|---|
| `pushed` | the commit and tag are on the remote |
| `pending` | the push failed; commit and tag are local and wait for the next try |
| `disabled` | `Commit=disabled`, `Push=disabled` or `--no-push` / `--no-commit` |
| `no-remote` | `Remote` is empty or not a remote of the repository |
| `none` | nothing was committed |

### A push that failed

A network failure, an expired key, a remote that refuses: the run is not failed for it. The push state is `pending`,
`exp:migration:status` lists the run under the pushes that wait and warns
`1 run(s) wait for their push: ./console exp:migration:push --apply`.

```bash
./console exp:migration:push              # the runs that wait
./console exp:migration:push --apply      # push them, oldest first (--limit, default 20)
php runcronjobs.php migrationpush         # the same, from cron
```

The cronjob part `migrationpush` is in no default part: add it to one the site runs, or run it on its own
([the cronjob part](../examples/cronjobs/migrationpush.md)). It does nothing while no run waits.

## Switching it off

| For one run | For the installation |
|---|---|
| `--no-commit`: no commit, no tag, no push | `[RepositorySettings] Commit=disabled` |
| `--no-tag`: commit and push, no tag | `Tag=disabled` |
| `--no-push`: commit and tag, no push | `Push=disabled` |

A development installation often runs with `Push=disabled`, production with all three on.

## Reading the record of other environments

Because every environment pushes its own `status/<environment>.json`, a clone of the repository tells what each
environment has applied without access to its database:

```bash
git fetch --tags
git tag -l 'migrations/production/*' --sort=version:refname | tail -5
git show origin/main:migrations/status/production.json | head -20
```

## Read on

- [Rollback and captions](migrations-rollback-and-captions.md)
- [Troubleshooting migrations](migrations-troubleshooting.md)
- [`exp:migration:push`](../examples/commands/expmigrationpush.md), [migration.ini \[RepositorySettings\]](../examples/ini-settings/migration/RepositorySettings.md)
