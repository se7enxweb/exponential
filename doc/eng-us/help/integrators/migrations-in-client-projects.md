---
title: "Migrations in client projects"
audience: integrator, partner, developer
category: migrations
order: 550
summary: For agencies and integrators who build and maintain many client sites - shipping changes as migrations in a site or in your own extension, environments per client, a handover the client can read in git, estimating the work, imports keyed by remote id, a project checklist and go-live.
keywords: migration, migrations, schema change, database change, deploy, exp:migration:migrate, agency, integrator, partner, client project, extension, extension:<name>, handover, go-live, estimate, remote id, status file, tags
type: help
---
# Migrations in client projects

This page is for agencies and integrators who build Exponential sites for clients and keep them running. Since
6.0.15 the changes you make to a client's database, content and settings can travel as migrations: written once,
tested on your development copy, applied in the same order on the client's staging and production, and recorded in
git where the client can see them.

## What changes for your projects

| Before 6.0.15 | With migrations |
|---|---|
| a list of steps in a ticket, clicked through on each environment | a file per change, in the project's repository |
| "did we run that on production?" | `exp:migration:status` on each environment, and `migrations/status/<environment>.json` in git |
| an update script that someone ran twice | the record: a migration runs once per installation |
| a backup taken by hand, maybe | a caption before every run, restored after a failure |
| changes repeated for every client by hand | migrations in your own extension, applied on every client site that activates it |

## Two places for a migration

| Where | Source | Namespace from `--skeleton` | Use for |
|---|---|---|---|
| the site's `migrations/` | `site` | `Site\Migrations` | changes for this one client |
| `extension/<name>/migrations/` of your extension | `extension:<name>` | `Extension\<Name>\Migrations` | changes every client of that extension gets |

```bash
./console exp:migration:generate --skeleton news_archive_folder --apply
./console exp:migration:generate --skeleton widget_table --source=extension:acmewidgets --apply
```

An extension's migrations are found only while the extension is active. A site migration that builds on one names it
in its header: `requires: 20261101090000_widget_table` or `requires: extension:acmewidgets`. When the change is one
every installation of the extension needs on upgrade, whether or not a site chose it, ship an upgrade step instead
([Upgrade step or migration?](../migrations/recipes/upgrade-step-or-migration.md)).

## Environments per client

Each installation names its environment in `site.ini [AdminEnvironment] Level` (`development`, `test`, `staging`,
`production`, or `auto` with `EXP_ENV`). A migration header can limit a migration:

```php
// environments: staging, production
// environments: !development
```

Seed data for your development copies, test users, demo content: give those migrations `environments: development`.
[Per-environment migrations](../migrations/recipes/per-environment-migrations.md) has the details.

## Imports keyed by remote id

Client sites differ in their object and node ids. Find everything by remote id or identifier, and give everything a
migration creates a remote id of your own (`acme-news-folder`). The same rule makes imports repeatable:
[Idempotent imports keyed by remote id](idempotent-imports-keyed-by-remote-id.md). A migration written this way runs
on every client's installation and can meet a half-done state after a failure.

## Handover

After every run the status file and the run log are committed, tagged `migrations/<environment>/<run-id>` and pushed.
At handover the client gets, in the repository they own:

- `migrations/`: every change, with its description and author in the header;
- `migrations/status/<environment>.json`: what each environment has applied;
- `migrations/runs/`: the log of every run;
- the tags: one per run, never moved.

Set `[RepositorySettings] Remote` to the client's repository, and give the system user of each installation the push
credentials (a deploy key). Hand over the policies: who has `migration/run` and `migration/rollback` in the client's
administration ([Policies, views and events](../migrations/policies-views-and-events.md)).

## Estimating

| Work | What to count |
|---|---|
| writing | one migration per change; schema and content apart; `plan()`, `up()`, `detect()`, `down()` |
| testing | `exp:migration:check` in CI; a simulation on SQLite or PostgreSQL; a test of `up()`, `down()`, `up()` |
| applying | a dry run and a run per environment; on MySQL/MariaDB the time of a caption (a database dump) |
| large changes | batches and resume for many objects ([Large data migrations](../migrations/recipes/large-data-migrations.md)) |

## Client-project checklist

- [ ] The record tables exist on every environment (`6.0.15-migration-tables` is done in `exp:upgrade:status`).
- [ ] Each installation names its environment; `RunAsUser` is an existing administrator login.
- [ ] git_manager is active and the remote takes pushes from each installation; `migrationpush` is scheduled.
- [ ] CI runs `./console exp:migration:check` on every pull request.
- [ ] Every migration finds things by remote id or identifier, and says how it is taken back.
- [ ] Policies: only the people who should run migrations on production have `migration/run`.
- [ ] Disk space for captions is agreed with the host.

## Go-live

1. Freeze the migrations that go live; `exp:migration:check` passes.
2. On staging, restored from a copy of production: `exp:migration:migrate`, then `--apply`; check the site.
3. On production: deploy the code, `./console exp:migration:migrate --apply`, deploy the caches
   ([Deploying with migrations](../migrations/recipes/deploying-with-migrations.md)).
4. `./console exp:migration:status --fail-on-pending` exits 0; the push went through.
5. Add the run's tag to the release notes for the client.

Moving a client's whole site from another system or another host is not a migration in this sense:
[Migrations versus migrating a site](../migrations/migrations-versus-migrating-a-site.md).

## Where to go next

- [Migrations in an extension or package](../migrations/recipes/migrations-in-an-extension-or-package.md)
- [Team workflow with git](../migrations/recipes/team-workflow-with-git.md)
- [Pushing tags](../migrations/recipes/pushing-tags.md)
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
- [Integrators: start here](integrators-start-here.md)
- [Building an integration as an extension](building-an-integration-as-an-extension.md),
  [Handover of an integration](handover-of-an-integration.md),
  [Estimating integration work](estimating-integration-work.md),
  [Integration go-live checklist](integration-go-live-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
