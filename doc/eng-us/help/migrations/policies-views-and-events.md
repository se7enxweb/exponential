---
title: "Migrations: policies, views and events"
audience: developer, admin, software-engineer, php-developer
category: migrations
order: 22
summary: The policies of the module migration and which views check them, the seven views with their addresses, the dashboard block and its fetch function, the ezpEvent events a run sends with their parameters, and the audit events it writes - with a listener example and links to every generated reference page.
keywords: migration/read, migration/run, migration/rollback, migration/edit, policies, policy functions, roles, module migration, views, status, history, log, stream, dryrun, run, rollback, dashboard block, fetch migration summary, ezpEvent, events, migration/run/before, migration/step/before, migration/step/after, migration/run/after, listener, audit events, system.migration.run, system.migration.apply, system.migration.fail, system.migration.rollback, system.migration.caption, system.migration.push, system.migration.http, system.migration.mail, system.migration.command, verify it's you
type: help
---
# Migrations: policies, views and events

This page lists everything an administrator grants and a developer listens to around migrations: the policies of
the module `migration`, its views, the dashboard block, the events a run sends and the audit events it writes.

## Contents

1. [Policies](#policies)
2. [Views](#views)
3. [The dashboard block and the fetch function](#the-dashboard-block-and-the-fetch-function)
4. [Events a run sends](#events-a-run-sends)
5. [Audit events](#audit-events)
6. [Related pages](#related-pages)

## Policies

| Policy | Allows | Checked by |
|---|---|---|
| `migration/read` | the status, the history, the page of a run, the dry run, the dashboard block, the sidebar panel | the views `status`, `history`, `log`, `stream`, `dryrun` |
| `migration/run` | run, resume, and **Simulate** in the dry run | the view `run`, and `dryrun` for the simulation |
| `migration/rollback` | take back a run, one migration or an earlier caption | the view `rollback` |
| `migration/edit` | record changes (Setup > Migrations > Recording, see [Recording changes](recording.md)), generate migrations from them, and write and change YAML migrations in the editor (Setup > Migrations > Editor) | the views `recording` and `editor` |
| `migration/remotes` | add, change and remove remotes, Test connection ([Remotes and deploys](remotes-and-deploys.md)) | the view `remote`, and `remotes` for Test connection |
| `migration/deploy` | a preflight, a deploy, approving a gate, rolling a deploy back | the views `deploy` and `deployment` for their buttons |

None of them has limitations: each is all or nothing. Only the Administrator role has them in a new installation.
Run, resume, simulate and rollback ask "Verify it's you" first (`site.ini [ReauthGroup_system]`,
[Verify it's you](../../guides/verify-its-you.md)). The console checks no policy: whoever can run `./console` on the
server can run migrations, as the user `migration.ini [RunSettings] RunAsUser` or `--login=<login>`.

To give a role read access in a migration of its own, see [Add roles and policies](recipes/add-roles-and-policies.md):

```php
$role = \eZRole::fetchByName( 'Site operators' );
$role->appendPolicy( 'migration', 'read' );
\eZRole::expireCache();
```

Generated reference: [Policies of the migration module](../../examples/policies/migration.md).

## Views

| View | Address | Policy | What it shows | Help |
|---|---|---|---|---|
| [status](../../examples/module-views/migration/status.md) | `/migration/status` | read | every migration and its state, the lock, the latest runs | [The status](status-and-history.md#the-status) |
| [history](../../examples/module-views/migration/history.md) | `/migration/history` | read | every run with its captions, commit, tag and push | [The history](status-and-history.md#the-history) |
| [log](../../examples/module-views/migration/log.md) | `/migration/log/<run-id>` | read | one run: its record, migrations and run log | [The page of a run](status-and-history.md#the-page-of-a-run) |
| [stream](../../examples/module-views/migration/stream.md) | `/migration/stream/<run-id>` | read | the live progress of a run, as the run's page reads it | [The page of a run](status-and-history.md#the-page-of-a-run) |
| [dryrun](../../examples/module-views/migration/dryrun.md) | `/migration/dryrun` | read (run to simulate) | the plan, and the simulation | [Dry run](run-dry-run-and-rollback.md#dry-run) |
| [run](../../examples/module-views/migration/run.md) | `/migration/run` | run | the pending migrations, the switches, starting the run | [Run](run-dry-run-and-rollback.md#run) |
| [rollback](../../examples/module-views/migration/rollback.md) | `/migration/rollback/<target>` | rollback | a run, one migration or a caption to restore | [Roll back](run-dry-run-and-rollback.md#roll-back) |
| [remotes](../../examples/module-views/migration/remotes.md) | `/migration/remotes` | read (remotes to test) | the remotes, Test connection | [Remotes](remotes-and-deploys.md#remotes) |
| [remote](../../examples/module-views/migration/remote.md) | `/migration/remote/<id>` | remotes | the wizard of a remote (new or saved) | [The wizard](remotes-and-deploys.md#the-wizard) |
| [deploy](../../examples/module-views/migration/deploy.md) | `/migration/deploy` | read (deploy to start one) | the deploy form and the history | [Deploys](remotes-and-deploys.md#deploys) |
| [deployment](../../examples/module-views/migration/deployment.md) | `/migration/deployment/<deploy-id>` | read (deploy for a gate or a rollback) | one deploy, live per remote, its report, gate and rollback | [A deploy's page](remotes-and-deploys.md#a-deploys-page) |

The module is `Setup > Migrations` in the menu (`menu.ini`, shown to users with `migration/read`). Generated
reference: [The migration module](../../examples/module-views/migration/README.md). The help button of each view
opens the page in the last column.

## The dashboard block and the fetch function

The dashboard block `migrations` ([its settings](../../examples/ini-settings/dashboard/DashboardBlock_migrations.md))
shows how many migrations are pending or failed, the orphans, the run going on and the last run, for users with
`migration/read`. It reads the fetch function `migration/summary`
([reference](../../examples/fetch-functions/migration/summary.md)), which a template of your own can use too:

```tpl
{def $migrations = fetch( 'migration', 'summary' )}
{if $migrations.failed}
    <p>{$migrations.failed} migration(s) failed: see Setup > Migrations.</p>
{elseif $migrations.pending}
    <p>{$migrations.pending} migration(s) pending.</p>
{/if}
{undef $migrations}
```

The keys are `pending`, `failed`, `orphans`, `total`, `ready` (the record tables exist), `run` (the last run),
`running` (a run started in the administration that goes on), `lock` (who holds the installation lock) and
`problem`. The website sidebar's Migrations panel shows the same ([Migrations in the website sidebar](expwt-panel.md)).

## Events a run sends

The runner notifies `ezpEvent` listeners, registered in `site.ini [Event] Listeners[]=<event>@<callback>`. A
listener that throws does not stop the run. The parameters, by position:

| Event | Parameters | When |
|---|---|---|
| `migration/run/before` | `string $runId`, `string[] $ids`, `expMigrationContext $context` | before the first migration of a run |
| `migration/step/before` | `string $runId`, `expMigration $migration`, `expMigrationContext $context` | before each migration (and before each `down()` of a rollback) |
| `migration/step/after` | `string $runId`, `expMigration $migration`, `array $entry`, `expMigrationContext $context` | after each, with its entry (state, action, messages, duration) |
| `migration/run/after` | `string $runId`, `array $report`, `expMigrationContext $context` | after the run, with its report (`exp-migration/1`) |

A listener in an extension, for example one that posts the end of every run to a chat channel:

```ini
<?php /* #?ini charset="utf-8"?
# extension/mysite/settings/site.ini.append.php
[Event]
Listeners[]=migration/run/after@mysiteMigrationNotifier::runAfter
*/ ?>
```

```php
<?php
class mysiteMigrationNotifier
{
    public static function runAfter( $runId, array $report, $context )
    {
        $summary = isset( $report['summary'] ) ? $report['summary'] : array();
        eZLog::write( sprintf( 'migration run %s in %s: exit %d, %d applied, %d failed', $runId,
                               $context->environment(), (int)$report['exit'],
                               isset( $summary['applied'] ) ? (int)$summary['applied'] : 0,
                               isset( $summary['failed'] ) ? (int)$summary['failed'] : 0 ), 'mysite-migrations.log' );
    }
}
```

## Audit events

Every run writes to the audit trail (channel `system`), enabled in `settings/audit.ini [AuditEventSettings]`
([reference](../../examples/ini-settings/audit/AuditEventSettings.md)):

| Event | Written when |
|---|---|
| `system.migration.run` | a run ends, with its id, environment, counts and exit code |
| `system.migration.apply` | a migration is applied; also when a changed file is accepted with `--force --reason` |
| `system.migration.fail` | a migration fails, with its message |
| `system.migration.rollback` | a run, a migration or a caption is taken back |
| `system.migration.caption` | a caption is taken |
| `system.migration.push` | the record of a run is pushed, or the push fails |
| `system.migration.http` | an `http` step sends a request: method, host and path, status (never the query, a header or the body) |
| `system.migration.mail` | a `mail` step sends a mail: how many recipients (never an address or the text) |
| `system.migration.command` | a `command` step runs a console command: its name and exit code (never its arguments or output) |

`exp:upgrade` writes its own `system.upgrade.*` events. Read them in the audit console, or send them on through the
audit log's sinks (mail, webhook) ([The audit trail of runs](recipes/the-audit-trail-of-runs.md),
[The audit trail](../../features/6.0/audit-trail.md)).

## Related pages

- [Migrations: the hub](README.md), [All recipes](recipes/README.md), [Frequently asked questions](faq.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](in-the-administration.md), [Commands](commands.md), [Settings](settings.md)
- [The file format](../../specifications/6.0/migration-file-format.md),
  [The feature](../../features/6.0/migrations.md)
- [When a migration fails](when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](migrations-versus-migrating-a-site.md)
- [Roles and policies](../../guides/roles-and-policies.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
