---
title: "Migrations in the administration"
audience: admin, developer
category: migrations
order: 10
summary: Where the migrations of an installation show in the administration - Setup > Migrations, the dashboard block and the website sidebar - which page explains what, and which policy each needs.
keywords: migrations, migration, Setup > Migrations, migration module, dashboard block, website sidebar, policies, migration/read, migration/run, migration/rollback, migration/edit, verify it's you, background run, administration
type: help
---
# Migrations in the administration

Migrations are changes to an installation written once as files and applied to every installation of the site in
the same order ([Migrations](README.md)). The administration shows them and runs them in **Setup > Migrations** (the
module `migration`), and shows their state in a dashboard block and in the website sidebar.

## Contents

1. [The pages](#the-pages)
2. [Who may do what](#who-may-do-what)
3. [Runs started here](#runs-started-here)
4. [What is done on the console](#what-is-done-on-the-console)
5. [Related pages](#related-pages)

## The pages

| Page | Address | What it helps you do | Help |
|---|---|---|---|
| Status | `/migration/status` | see every migration and its state, the lock, the latest runs | [The status and the history](status-and-history.md) |
| History | `/migration/history` | every run with its captions, commit, tag and push; resume and roll back | [The history](status-and-history.md#the-history) |
| A run's page | `/migration/log/<run-id>` | its record, its migrations, its run log, and its progress while it goes on | [The page of a run](status-and-history.md#the-page-of-a-run) |
| Dry run | `/migration/dryrun` | what a run would change; **Simulate** runs it in a transaction that is rolled back | [Dry run](run-dry-run-and-rollback.md#dry-run) |
| Run | `/migration/run` | run the pending migrations, chosen one by one | [Run](run-dry-run-and-rollback.md#run) |
| Rollback | `/migration/rollback/<target>` | take a run, one migration or an earlier caption back | [Roll back](run-dry-run-and-rollback.md#roll-back) |
| Recording | `/migration/recording`, `/migration/recording/<id>` | record changes made by hand, a site-wide window, Generate migration | [Recording changes](recording.md) |
| Editor | `/migration/editor`, `/migration/editor/<migration-id>` | write a YAML migration with forms, check, preview, save | [The migration editor](the-migration-editor.md) |
| Remotes | `/migration/remotes`, `/migration/remote/<id>` | the installations this one deploys its migrations to: the wizard, Test connection | [Add a remote step by step](add-a-remote-step-by-step.md) |
| Deploys | `/migration/deploy`, `/migration/deployment/<deploy-id>` | deploy to remotes all at once or stage by stage, follow every remote live, the history, a gate, a rollback | [Deploy step by step](deploy-step-by-step.md) |
| Dashboard block | the dashboard | pending, failed, orphans, the run going on and the last run | [The dashboard block](status-and-history.md#the-dashboard-block) |
| Website sidebar | the site's own pages | the same state, read-only | [Migrations in the website sidebar](expwt-panel.md) |

The status also lists the kernel's upgrade steps first, as migrations of the source `kernel`: a run applies the
pending ones of the stages `database` and `scripts` before the site's migrations
([The steps among the migrations](../../guides/upgrading-with-the-console.md#the-steps-among-the-migrations)).

## Who may do what

| Policy | Allows |
|---|---|
| `migration/read` | the status, the history, the page of a run, the dry run, the dashboard block |
| `migration/run` | run, resume, and simulate in the dry run |
| `migration/rollback` | take a run, one migration or an earlier caption back |
| `migration/edit` | the Recording page and Generate migration, and the migration editor |
| `migration/remotes` | add, change and remove the remotes deploys go to, and test their connection |
| `migration/deploy` | deploy to remotes, approve the gate of a staged deploy, roll a deploy back |

Only the Administrator role has them in a new installation. Run, resume, simulate and rollback ask
["Verify it's you"](../../guides/verify-its-you.md) first. The policies in detail:
[Policies of the migration module](../../examples/policies/migration.md); every view, policy and event in one place:
[Policies, views and events](policies-views-and-events.md).

## Runs started here

A run, a resume or a rollback started in the administration runs in the background: the page starts the console
command (`exp:migration:migrate --apply`, `exp:migration:resume --apply` or `exp:migration:rollback --apply`) as a
process of its own, as the signed-in user, and opens the run's page, which follows it live. The run goes on when the
page is closed. Its files are in `var/<site>/migration/jobs/`. It needs `proc_open` and a PHP command line binary
(`migration.ini [AdminSettings] PhpCliPath`); without them the page says so, and the console runs it. One run at a
time: while one goes on, or `exp:upgrade` or another run holds the installation lock, the page refuses a new one.

## What is done on the console

Migrations are files in `migrations/` (or `extension/<name>/migrations/`). The administration writes YAML migrations
(the editor, a recording); PHP, SQL and INI migrations, the generators from content, classes, roles, sections,
settings and differences, and skeletons are made on the console with `./console exp:migration:generate`
([Generating migrations](generating-migrations.md)). The settings are in `settings/migration.ini`
([Commands](commands.md), [Settings](settings.md)).

A tour of every page with screenshots: [A tour of Setup > Migrations](a-tour-of-setup-migrations.md).

## Related pages

- [Migrations: the hub](README.md), [All recipes](recipes/README.md), [Frequently asked questions](faq.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [Commands](commands.md), [Settings](settings.md), [Policies, views and events](policies-views-and-events.md)
- [The file format](../../specifications/6.0/migration-file-format.md),
  [The feature](../../features/6.0/migrations.md)
- [When a migration fails](when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](migrations-versus-migrating-a-site.md)
- [Migrations: the hub](README.md), [Migrations for administrators](migrations-for-administrators.md)
- [A tour of the administration](../admin-tour.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
