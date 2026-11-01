---
title: "Deploying with migrations: CI/CD and GitHub Actions"
audience: developer, software-engineer, admin, hosting
category: migrations
order: 16
summary: Run migrations as part of every deploy - the order of the steps, a complete GitHub Actions workflow that checks pull requests and deploys over SSH, the exit codes a pipeline acts on, the installation lock, and runs started in Setup > Migrations.
keywords: migration, migrations, deploy, deployment, CI, CD, continuous delivery, GitHub Actions, pipeline, workflow, SSH, exit code, lock, upgrade.lock, background run, schema change, database change, exp:migration:migrate, exp:migration:status, exp:migration:check, exp:velocity deploy
featured: true
type: help
---
# Deploying with migrations: CI/CD and GitHub Actions

A deploy brings new code to an installation. When the code comes with migrations, the deploy also runs them, in the
right place among the other steps, and stops when one fails. This recipe gives the order of the steps, a complete
GitHub Actions workflow, and the rules a pipeline must know: exit codes, the lock, and runs started from the
administration.

## What you'll have at the end

- a `check` job that lints the migrations of every pull request;
- a `deploy` job that updates staging (or production) over SSH and runs the pending migrations;
- a pipeline that stops on a failed migration, with the installation restored from its caption.

## Before you start

- The target is a git checkout of the site, owned by the user the web server runs as; a deploy user may run
  commands as that user over SSH.
- `site.ini [AdminEnvironment] Level` is set on the target (`staging`, `production`), so the run commits
  `status/<environment>.json` and tags `migrations/<environment>/<run-id>`.
- git_manager is active and the target can push to its remote, or `[RepositorySettings] Push=disabled` is set there.

## Steps

### 1. The order of a deploy

| Step | Command | Why here |
|---|---|---|
| 1 | `git pull --ff-only` | the new code and the new migration files; refuses a diverged checkout |
| 2 | `php bin/php/ezpgenerateautoloads.php -e` | migrations may use classes the new code brings |
| 3 | `./console exp:migration:status` | the log shows what is about to run |
| 4 | `./console exp:migration:migrate --apply --format=json` | caption, kernel steps of the stages `database` and `scripts`, the migrations, record, commit, tag, push |
| 5 | `./console exp:velocity deploy` | caches, PHP-FPM, Velocity: the running site sees the new code and data |

Step 4 refuses to start (exit 2) when `settings/`, `extension/` or `design/` have changes that are not committed:
a caption keeps the commit of the code, not uncommitted files. A clean `git pull` never leaves any. When the run
applied kernel upgrade steps, also run `./console exp:upgrade:finish --apply`.

### 2. The workflow file

`.github/workflows/deploy.yml` in the site's repository:

```yaml
name: Migrations and deploy

on:
  pull_request:
  push:
    branches: [main]

jobs:
  check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: sqlite3, intl, mbstring, xsl
      - run: composer install --no-interaction --no-progress
      - run: php bin/php/ezpgenerateautoloads.php -e
      - name: Lint the migrations (no database)
        run: ./console exp:migration:check

  deploy:
    if: github.event_name == 'push'
    needs: check
    runs-on: ubuntu-latest
    environment: staging
    concurrency: deploy-staging
    steps:
      - name: Deploy over SSH and run the migrations
        env:
          SSH_KEY: ${{ secrets.DEPLOY_SSH_KEY }}
          HOST: ${{ secrets.DEPLOY_HOST }}
        run: |
          mkdir -p ~/.ssh
          printf '%s\n' "$SSH_KEY" > ~/.ssh/id_deploy
          chmod 600 ~/.ssh/id_deploy
          ssh -i ~/.ssh/id_deploy -o StrictHostKeyChecking=accept-new "deploy@$HOST" '
            set -e
            cd /var/www/site
            git pull --ff-only
            php bin/php/ezpgenerateautoloads.php -e
            ./console exp:migration:status
            ./console exp:migration:migrate --apply --format=json > var/log/migration-last-run.json
            ./console exp:velocity deploy
          '
```

`set -e` stops the remote script at the first command that does not exit 0, so a failed migration never reaches
the deploy step. `concurrency:` keeps GitHub from starting two deploys of the same environment; the installation
lock refuses a second run anyway. For production, copy the job with `environment: production` and a required
reviewer on that GitHub environment.

### 3. The exit codes the pipeline acts on

| Code | Means | The pipeline |
|---|---|---|
| 0 | done, or nothing to do | goes on |
| 1 | a migration failed (the pre caption was restored), a changed migration file was refused, or the lock is held | stops; read `var/log/migration-last-run.json` and the run log |
| 2 | usage: a wrong option, or uncommitted code in the code trees | stops; fix the checkout |
| 3 | the database is not reachable | stops; nothing ran |
| 4 | internal error | stops; read `var/log/migration.log` |

`--format=json` writes the machine output `exp-migration/1`: every migration with its action and messages, the
captions, the restore, the repository state, `errors`, `warnings` and `exit`. Keep it as a build artifact or in the
target's log.

### 4. A run that failed during a deploy

```text
Exponential migrations: run
Run 20261101-140210-b81f4d, environment staging, database mysql (transactions: data), as admin

  applied     20261101110000_news_note_priority            site               News notes get a priority (default 0)
              added exp_news_note.priority
  failed      20261101120500_news_tags                     site               Tags for news notes
              the table exp_news_tag could not be created: the table exp_news_tag exists already

Captions: pre 20261101-140209, failure 20261101-140211-2
Restore: restored (caption 20261101-140209)
Repository: commit 4d2e8a1c7b90, tag migrations/staging/20261101-140210-b81f4d, push pushed
Summary: 2 migration(s): 0 done, 1 pending, 0 not needed, 1 failed, 0 unknown
FAIL (exit 1)
```

The installation is back where it was before the run; the failed run's record is committed and pushed. Fix the
migration in a new commit and deploy again, or resume on the target with `./console exp:migration:resume --apply`.

### 5. The lock, and runs from the administration

A run that writes holds the installation lock `var/<site>/upgrade.lock`, shared with `exp:upgrade`. Next to it the
holder file `upgrade.lock.holder` says who holds it. A second run, from the pipeline, the console or the
administration, stops at once with `another run holds the lock: ...`; `./console exp:migration:status` names the
holder. A process that dies frees the lock; a stale holder file is taken over by the next run.

An administrator can also start a run in **Setup > Migrations**. It runs in the background as a console process of
its own, as the signed-in user, after "Verify it's you"; its files are in `var/<site>/migration/jobs/`. It needs
`proc_open` and `migration.ini [AdminSettings] PhpCliPath`. Agree in the team who runs migrations on which
environment; the lock prevents two at once, not two people expecting different results.

## Check it

- A pull request with a broken header fails the `check` job.
- After a deploy, `./console exp:migration:status` on the target lists nothing pending, and
  `git tag -l 'migrations/staging/*'` in the repository shows the run's tag.

## Roll it back

The deploy restored the caption itself after a failure. To take back a run that passed:
`./console exp:migration:rollback <run-id> --apply` on the target, then `git revert` the code and deploy again.

## Next step

Migrations that touch many objects: [Large data migrations](large-data-migrations.md).

## Related pages

- [Migrations: the hub](../README.md) · [All recipes](README.md)
- [The guide](../../../guides/migrations.md), [the tutorial](../../../guides/migrations-tutorial.md), [PHP migrations](../../../guides/migrations-php.md)
- [In the administration](../in-the-administration.md), [Commands](../commands.md), [Settings](../settings.md)
- [The file format](../../../specifications/6.0/migration-file-format.md)
- [When a migration fails](../when-a-migration-fails.md), [Troubleshooting messages](../../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](../migrations-versus-migrating-a-site.md)
- [Testing migrations](testing-migrations.md), [Per-environment migrations](per-environment-migrations.md),
  [Deploying](../../../guides/deploying.md), [Upgrading with the console](../../../guides/upgrading-with-the-console.md),
  [exp:migration:migrate](../../../examples/commands/expmigrationmigrate.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
