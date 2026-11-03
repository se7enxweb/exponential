# Upgrade steps for extensions

This guide shows an extension developer how to make an extension's own upgrade work, such as a new table, a new
column, a default row or a policy, part of `./console exp:upgrade`. Once the step is registered, an administrator
who upgrades sees it in `exp:upgrade:status`, gets it with `exp:upgrade --apply` and has it recorded, the same way as
the kernel's steps.

[Guides](README.md) · Related: [Upgrading with the console](upgrading-with-the-console.md),
[Extensions](extensions.md), [Migrations](migrations.md), [Migrations: the hub](../help/migrations/README.md),
[Upgrade step or migration?](../help/migrations/recipes/upgrade-step-or-migration.md),
[Migrations in an extension or a package](../help/migrations/recipes/migrations-in-an-extension-or-package.md)

An upgrade step or a migration? An **upgrade step** is for what every installation of the extension needs when it
upgrades: it can look at the installation (`detect()`) and is run by `exp:upgrade` with the kernel's steps. A
**migration** in `extension/<name>/migrations/` is a change written once and applied in order with a site's own
migrations (`exp:migration:migrate`), with a caption before it and a record of the run; use it for changes a site
builds on, which its own migrations can name in `requires:`. Both are recorded in the same table, `exp_migration`,
and both run under the same lock ([PHP migrations](migrations-php.md#migrations-in-an-extension)).

## The step

A step is a class that implements `expUpgradeStep` (`kernel/classes/upgrade/expupgradestep.php`). Three base
classes do most of the work:

| Base class | For | You write |
|---|---|---|
| `expUpgradeSchemaStep` | tables, columns and indexes defined in a `.dba` schema | `tables()`, `fields()`, `indexes()` |
| `expUpgradeScriptStep` | anything else: rows, policies, settings, files | `changes()` (what is missing, one line each) and `write()` |
| `expUpgradeBaseStep` | the defaults (every engine, no dependency, version 6.0.15) | everything else |

Every step has:

- `id()`: stable for good, `<version>-<name>`, for example `1.4.0-myext-widget-table`. It is what the record keeps
  and what administrators pass to `--only` and `--skip`. Never rename it.
- `stage()`: `database` or `scripts` (`finish` is the kernel's).
- `version()`: the release of the extension that brought it. Steps run by stage, then by version, then in the order
  listed.
- `engines()`: `array( '*' )`, or the engines it is for (`mysql`, `postgresql`, `sqlite`, `oracle`, `mongodb`).
- `after()`: step ids that must be in place first.
- `detect( $context )`: what the installation holds now: `PRESENT`, `MISSING`, `NOT_APPLICABLE` or `UNKNOWN`, with a
  detail. Look at the installation itself (the database catalog, the role, the settings), never only at the
  record: an administrator may have done the step by hand.
- `plan( $context )`: what `apply()` would change, one line each, for the dry run. It must change nothing.
- `apply( $context )`: makes the change and returns what it did; it throws on failure. It must be idempotent and
  complete a half-applied state: `exp:upgrade` may run it again after a failure.

Use `$context->db()` for the database, never `eZDB::instance()` of your own: tests run steps on other connections.

## A table and a column

A new table needs no step of its own: the kernel's step `6.0.15-active-extension-tables` creates the missing tables
of the `share/db_schema.dba` of every active extension, and a new installation does the same after its site package
([where the tables come from](../features/6.0/extensions-of-a-new-installation.md#where-the-tables-come-from)). Write
a step for what that does not do: a column or an index of a table that exists already, rows, policies, settings.

The extension keeps its tables in `share/db_schema.dba`, as the kernel does. The schema step reads the definitions
from the file you name, creates the missing tables through the engine's schema handler and adds missing columns and
indexes:

```php
<?php
class myextUpgradeStepWidgetTable extends expUpgradeSchemaStep
{
    public function id()          { return '1.4.0-myext-widget-table'; }
    public function version()     { return '1.4.0'; }
    public function description() { return 'myext: the widget table and its owner column'; }
    public function tables()      { return array( 'myext_widget' ); }
    public function fields()      { return array( 'myext_item' => array( 'owner_id' ) ); }
    public function indexes()     { return array( 'myext_item' => array( 'myext_item_owner' ) ); }
}
```

The context reads the kernel schema by default. For an extension's own `.dba`, give the step the extension's
schema by overriding `missing()`, `statements()` and `apply()` through a context made with that file:

```php
    public function detect( expUpgradeContext $context ) { return parent::detect( $this->own( $context ) ); }
    public function plan( expUpgradeContext $context )   { return parent::plan( $this->own( $context ) ); }
    public function apply( expUpgradeContext $context )  { return parent::apply( $this->own( $context ) ); }

    private function own( expUpgradeContext $context )
    {
        return new expUpgradeContext( $context->db(), $context->isApply(), array(), 'extension/myext/share/db_schema.dba' );
    }
```

## Rows, policies and settings

```php
<?php
class myextUpgradeStepDefaultCategory extends expUpgradeScriptStep
{
    public function id()          { return '1.4.0-myext-default-category'; }
    public function version()     { return '1.4.0'; }
    public function description() { return 'myext: the category "General" exists'; }
    public function after()       { return array( '1.4.0-myext-widget-table' ); }

    protected function changes( expUpgradeContext $context )
    {
        $rows = $context->db()->arrayQuery( "SELECT id FROM myext_category WHERE identifier = 'general'" );
        return $rows ? array() : array( 'create the category "General"' );
    }

    protected function write( expUpgradeContext $context )
    {
        $context->db()->query( "INSERT INTO myext_category ( identifier, name ) VALUES ( 'general', 'General' )" );
        return array( 'created the category "General"' );
    }
}
```

`expUpgradeScriptStep` turns `changes()` into the state (an empty list is in place), uses it as the dry run, and
after `write()` asks `changes()` again: anything still listed fails the step. Set `$this->notApplicable = true` with
a `$this->reason` when the step does not apply (the feature is switched off), or return `null` with a reason when it
cannot be told.

When the extension already has a setup command, call the class that command uses, with the command's default
options, as the kernel's policy steps do (`kernel/classes/upgrade/steps/expupgradestepeditorpolicies.php`). Do not
start the command's script in a step.

## Registering the steps

In the extension's `settings/upgrade.ini.append.php`:

```ini
[UpgradeSettings]
ExtensionStepList[]=myextUpgradeStepWidgetTable
ExtensionStepList[]=myextUpgradeStepDefaultCategory
```

Generate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and check:

```bash
./console exp:upgrade:steps --stage=database
./console exp:upgrade:status
```

A class that is not in the autoload array, or that does not implement `expUpgradeStep`, is listed as a warning
and left out; two steps with the same id, an `after()` that names no step and a cycle are warnings too.

Ship the steps in the extension's repository and in its `-ezpackage` repository alike: both are installed, and both
are upgraded.

The steps of an extension are migrations of the source `kernel` as well, like the kernel's own
([The steps among the migrations](upgrading-with-the-console.md#the-steps-among-the-migrations)):
`./console exp:migration:status` and Setup > Migrations list them, and `./console exp:migration:migrate --apply`
applies the pending ones of the stages `database` and `scripts` before the migrations. A migration in
`extension/<name>/migrations/` that needs the step's table names it in its header (`requires: myext-1.2-widget-table`).
Choose an upgrade step for what every installation of the extension needs on upgrade, and a migration for a change
that one site makes once.

## Testing a step

Test each step four ways on a throwaway database (SQLite always; MySQL and PostgreSQL where you can):

1. a new installation: `not-needed`;
2. the previous release's schema and data: `pending`, then `exp:upgrade --apply`, then `done`;
3. a second `--apply`: nothing changes;
4. half applied (one of two tables, one of two columns): the run completes it.

`expUpgrade::run( array( 'database', 'scripts' ), array( 'db' => $db, 'apply' => true ) )` runs the stages on a
connection of your test. The kernel's own tests are in `tests/tests/kernel/classes/upgrade/`.
