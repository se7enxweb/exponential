---
title: "Migrations: frequently asked questions"
audience: developer, admin, software-engineer, php-developer, hosting, dba, integrator, dxp-developer, project-manager
category: migrations
order: 31
summary: Short answers to the questions people ask about migrations in Exponential 6.0.15 - what they are for, how they differ from upgrades and site moves, how runs, captions, rollback, environments, teams, CI, engines, extensions and the administration work - each with a link to the page that explains it.
keywords: migrations FAQ, frequently asked questions, migration, migrations, schema change, database change, deploy, rollback, caption, environment, CI, git, Doctrine, Kaliop, YAML, MongoDB, Oracle, MySQL, PostgreSQL, SQLite, extension, package, administration, questions, answers
featured: true
type: help
---
# Migrations: frequently asked questions

## Contents

1. [The idea](#the-idea)
2. [Writing migrations](#writing-migrations)
3. [Running them](#running-them)
4. [Safety and going back](#safety-and-going-back)
5. [Teams, environments and delivery](#teams-environments-and-delivery)
6. [In the administration](#in-the-administration)
7. [Databases](#databases)
8. [Coming from other tools](#coming-from-other-tools)
9. [Related pages](#related-pages)

## The idea

**What is a migration?**
A change to an installation (a table, a column, a content class, content, a role, a setting) in a PHP file under
version control, applied once to every installation of a site in the same order, with a record of what ran where.
[Migrations](README.md).

**Is a migration the same as migrating my site to Exponential or to another server?**
No. Migrations change one site a step at a time; migrating a site moves or upgrades a whole installation.
[Migrations versus migrating a site](migrations-versus-migrating-a-site.md).

**Why not just run SQL by hand on each server?**
Because nothing would record which statements ran where, a mistake on production would have no automatic way back,
and staging and production would drift apart. A migration runs once per installation, in order, after a caption, and
its record is committed to git. [Coming from SQL update files](recipes/coming-from-sql-update-files.md).

**When do I use an upgrade step instead of a migration?**
For what every installation of an extension needs when it upgrades, use an upgrade step of `exp:upgrade`; for a
change one site makes once, a migration. [Upgrade step or migration?](recipes/upgrade-step-or-migration.md).

**Do I need a migration for a change of a template or a setting file?**
No: templates and settings files are code and travel with git. A migration is for what lives in the database (and for
`settings/override`, which is not in git): [Move or rename INI settings](recipes/move-or-rename-ini-settings.md).

## Writing migrations

**How do I start a new one?**
`./console exp:migration:generate --skeleton <name> --apply` writes `migrations/<timestamp>_<name>.php` with the
header, `plan()`, `up()` and `down()`. [The tutorial](../../guides/migrations-tutorial.md).

**Can I write migrations in YAML or SQL?**
Yes: `.yml` files of steps, `.sql` files (one portable file, or a file per engine and a `.mongodb.json`), and `.ini`
files for settings. [Migrations: SQL and settings](../../guides/migrations-sql-and-settings.md).

**Can I use the kernel's PHP API in a migration?**
Yes: `eZContentFunctions`, `eZContentClass`, `eZRole`, `eZINI`, `expKernelTableSchema` and the rest, always with
`$context->db()` for the database. [Writing migrations in PHP](../php-developers/writing-migrations-in-php.md).

**May I edit a migration after it ran?**
No. A run refuses a migration whose file changed after it was applied. Write a new migration for the new change;
accept a harmless edit (a comment) with `--force --reason=<text>`. [Reviewing a migration](recipes/reviewing-a-migration.md).

**How do two migrations share a value?**
`$context->setReference( 'name', $value )` in one, `$context->reference( 'name' )` in a later one of the same run.
Across runs, find the value again in the installation (by remote id). [PHP migrations](../../guides/migrations-php.md).

**Must every migration have a down()?**
No. Leave it out when a change cannot be undone by itself (removed content); the caption taken before the run is
the way back then. [Writing a reversible down()](recipes/writing-a-reversible-down.md).

## Running them

**What does `exp:migration:migrate --apply` do, step by step?**
Takes the lock, checks the code is committed, takes or reuses a caption, runs the pending migrations in order (each in
a transaction where the database has one), stops and restores at the first failure, takes a caption after a run that
passed, then commits, tags and pushes the record. [The guide](../../guides/migrations.md#the-life-cycle).

**Can I see what a run would do first?**
Yes: `./console exp:migration:migrate` without `--apply` is the dry run, and `./console exp:migration:dry-run
--simulate --diff` runs the migrations for real in a transaction that is rolled back, on PostgreSQL and SQLite.
[Testing migrations](recipes/testing-migrations.md).

**Can I run migrations from the administration?**
Yes, in Setup > Migrations, with the policy `migration/run`. The run goes on in the background when you close the page.
[In the administration](in-the-administration.md), [Migrations for administrators](migrations-for-administrators.md).

**Which user does a migration run as?**
The signed-in user in the administration; on the console `migration.ini [RunSettings] RunAsUser` (`admin`) or
`--login=<login>`. Content it creates belongs to that user. [Settings](settings.md).

**What happens with the kernel's upgrade steps?**
They are listed first, as migrations of the source `kernel`, and a run applies the pending ones of the stages
`database` and `scripts` before the site's migrations; the other stages stay with `exp:upgrade`.
[Upgrading with the console](../../guides/upgrading-with-the-console.md#the-steps-among-the-migrations).

**A migration takes an hour. Will it time out?**
Not on the console, which has no time limit. Write it in batches that are safe to run again, and resume an
interrupted run with `exp:migration:resume --apply`. [Large data migrations](recipes/large-data-migrations.md).

## Safety and going back

**What is a caption?**
A safe point of the installation (the database, the `var/` storage, `settings/override`, the commits of the code),
taken before every run and restored automatically after a failure. "Sicherungspunkt" in German.
[Captions and rollback in practice](recipes/captions-and-rollback-in-practice.md).

**How do I undo a run that passed?**
`./console exp:migration:rollback <run-id> --apply` restores its pre caption; `./console exp:migration:rollback
<migration-id> --apply` runs one migration's `down()`. [Rollback and captions](../../guides/migrations-rollback-and-captions.md).

**Does a rollback lose the content editors wrote after the run?**
Restoring a caption puts the database back to the time of the caption, so yes, for the database. Prefer a migration's
`down()`, or a new migration, on a site with editors at work. [Captions and rollback in practice](recipes/captions-and-rollback-in-practice.md).

**Who can see what ran, and when?**
Setup > Migrations > History, the tables `exp_migration` and `exp_migration_run`, the status file and run log in git,
and the audit events `system.migration.*`. [The audit trail of runs](recipes/the-audit-trail-of-runs.md).

## Teams, environments and delivery

**Two developers created migrations at the same time. What happens?**
Both run, in the order of their timestamps; when one needs the other, `requires:` says so. A duplicate id or a cycle
stops every run until it is fixed. [Team workflow with git](recipes/team-workflow-with-git.md).

**Can a migration run only on staging, or never on production?**
Yes: the header line `environments: staging` or `environments: !production`. [Per-environment migrations](recipes/per-environment-migrations.md).

**How do I run migrations in my deploy pipeline?**
After the code is in place: `./console exp:migration:migrate --apply`, then `./console exp:velocity deploy`; gate pull
requests with `./console exp:migration:check`. [Deploying with migrations](recipes/deploying-with-migrations.md).

**Where does the record go in git, and who pushes it?**
To `migrations/status/<environment>.json` and `migrations/runs/<run-id>.log` in the site's repository (or a separate
one), committed, tagged `migrations/<environment>/<run-id>` and pushed by the run itself.
[Pushing tags](recipes/pushing-tags.md).

**Can an extension ship migrations?**
Yes, in `extension/<name>/migrations/`; they run while the extension is active. Packages carry them in their own
`migrations/` folder. [Migrations in an extension or a package](recipes/migrations-in-an-extension-or-package.md).

## In the administration

**Where do I start in the administration?**
**Setup > Migrations**: its tabs are Status, History, Dry run, Run, Rollback, Recording, Remotes, Deploys and Editor.
[A tour of Setup > Migrations](a-tour-of-setup-migrations.md).

**Can I make a migration without a shell?**
Yes: record your changes and press **Generate migration** ([Recording changes](recording.md)), or build it in the
**Editor** ([The migration editor](the-migration-editor.md)).

**The editor will not save my change to an old migration. Why?**
The migration has run; every installation that applied it keeps its checksum, and a changed file would stop their
next run. Write the change as a new migration. [A migration that has run](the-migration-editor.md#a-migration-that-has-run).

**How long does a site-wide recording stay open?**
Until someone closes it, or for the minutes typed in **Close by itself after (minutes, optional)** (at most 31 days).
[The site-wide window](recording.md#the-site-wide-window).

**How do I add staging or production as a target?**
**Setup > Migrations > Remotes > New remote**: five steps, the credential as the name of a secret.
[Add a remote step by step](add-a-remote-step-by-step.md).

**Is a password stored in the remote's settings?**
Never. The settings hold the name of a secret of Setup > Secrets (or an environment variable); a password typed into
the wizard is not written. [Step 3: the credential](add-a-remote-step-by-step.md#step-3-the-credential).

**Can I try a deploy without changing anything?**
Yes: **Preflight only** on the Deploys page checks every remote and plans the files, and sends nothing.
[Preflight only](deploy-step-by-step.md#preflight-only).

**My deploy waits at a gate. What now?**
It is a staged deploy: check the stage that was deployed, then **Approve** on the deploy's page.
[Staged deploys and the gate](deploy-step-by-step.md#staged-deploys-and-the-gate).

**What does "out of sync" mean?**
A remote failed and not every remote could be taken back, so the remotes now differ.
[In sync and out of sync](deploy-step-by-step.md#in-sync-and-out-of-sync).

**Our host only offers FTP. Can we deploy there?**
With **Transfer only** the files are sent and verified; to run them there too, set up the signed HTTP trigger.
[FTP-only hosts and the trigger](add-a-remote-step-by-step.md#ftp-only-hosts-and-the-trigger).

## Databases

**Do migrations work on every database?**
Yes, with differences: PostgreSQL and SQLite undo a failed migration completely, MySQL/MariaDB and Oracle undo its
data but not its schema changes, MongoDB has no transactions; the caption covers the rest (Oracle by hand).
[Engine notes](recipes/engine-notes.md).

**How do I add a column on every engine?**
Through the engine's schema handler, as the kernel's own upgrade steps do. [Add a column or an index](recipes/add-a-column-or-an-index.md).

## Coming from other tools

**I know Doctrine Migrations. What is different?**
Migrations change content as well as schema, and every run has captions, a record in git and an administration page.
[Coming from Doctrine Migrations](recipes/coming-from-doctrine-migrations.md),
[Exponential migrations for Doctrine users](../dxp-developers/exponential-migrations-for-doctrine-users.md).

**We used the Kaliop migration bundle on eZ Platform. Can we convert our YAML?**
Yes: `./console exp:migration:convert <file or folder>` reads the YAML migrations of that bundle and writes a YAML
migration of Exponential for each file; check each one before it runs. [Coming from the Kaliop migration bundle](recipes/coming-from-kaliop-ezmigrationbundle.md),
[Converting migrations of the reference bundle](../../guides/migrations-generators-and-recording.md#converting-migrations-of-the-reference-bundle).

## Related pages

- [Migrations: the hub](README.md), [All recipes](recipes/README.md)
- [The guide](../../guides/migrations.md), [the tutorial](../../guides/migrations-tutorial.md),
  [PHP migrations](../../guides/migrations-php.md),
  [Rollback and captions](../../guides/migrations-rollback-and-captions.md),
  [The migrations repository](../../guides/migrations-repositories.md)
- [In the administration](in-the-administration.md), [Commands](commands.md), [Settings](settings.md),
  [Policies, views and events](policies-views-and-events.md)
- [The file format](../../specifications/6.0/migration-file-format.md),
  [The feature](../../features/6.0/migrations.md)
- [When a migration fails](when-a-migration-fails.md),
  [Troubleshooting messages](../../guides/migrations-troubleshooting.md)
- [Migrations versus migrating a site](migrations-versus-migrating-a-site.md)
- [Glossary](../../glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
