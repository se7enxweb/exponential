---
title: "Migrations in the administration"
audience: admin, developer
category: administration
order: 1
summary: Where the migrations of an installation show in the administration - Setup > Migrations, the dashboard block and the website sidebar - and which page explains what.
keywords: migrations, Setup > Migrations, migration module, dashboard block, website sidebar, policies, migration/read, migration/run, migration/rollback, migration/edit
type: help
---
# Migrations in the administration

Migrations are changes to an installation written once as files and applied to every installation of the site in
the same order ([the guide](../../guides/migrations.md)). The administration shows them and runs them in
**Setup > Migrations** (the module `migration`), and shows their state in a dashboard block and in the website
sidebar.

| Page | What it helps you do |
|---|---|
| [The status and the history](status-and-history.md) | See every migration and its state, the runs with their captions and commits, follow a run as it goes, read the dashboard block |
| [Run, dry run and rollback](run-dry-run-and-rollback.md) | See what a run would do, simulate it, run the pending migrations, resume a failed run, take a run, a migration or an earlier caption back |
| [Migrations in the website sidebar](expwt-panel.md) | Read the state of the migrations on the site's own pages |
| [Recording changes](recording.md) | Record your own changes, or every change in a site-wide window, as the steps of a migration; see what a recording captured; stop and discard |

## Who may do what

| Policy | Allows |
|---|---|
| `migration/read` | the status, the history, the page of a run, the dry run, the dashboard block |
| `migration/run` | run, resume, and simulate in the dry run |
| `migration/rollback` | take a run, one migration or an earlier caption back |
| `migration/edit` | record changes (Setup > Migrations > Recording); later also writing and generating migrations in the administration |

Only the Administrator role has them in a new installation. Run, resume, simulate, rollback and opening the site-wide
recording window ask
["Verify it's you"](../../guides/verify-its-you.md) first. The policies in detail:
[Policies of the migration module](../../examples/policies/migration.md).

Writing migrations, their editor and generators in the administration, and the settings of the migrations
repository are not part of Setup > Migrations in 6.0.15: migrations are written as files and generated with
`./console exp:migration:generate`, and their settings are in `settings/migration.ini`. What you change can be
recorded as the steps of a migration, though: [Recording changes](recording.md).

## Related pages

- [Migrations: the guide](../../guides/migrations.md) and [the tutorial](../../guides/migrations-tutorial.md)
- [Rollback and captions](../../guides/migrations-rollback-and-captions.md)
- [The migrations feature](../../features/6.0/migrations.md)
- [A tour of the administration](../admin-tour.md)
