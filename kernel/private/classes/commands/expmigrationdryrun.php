<?php
/**
 * @description Dry run of the pending migrations: the plan, and with --simulate a run in a transaction that is rolled back (--diff shows what changed)
 * @alias exp:migration:dry-run
 *
 * File containing the exp:migration:dry-run command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationdryrun extends ExpmigrationBase
{
    protected function name()
    {
        return 'exp:migration:dry-run';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Dry run of the pending migrations\n" .
                             "exp:migration:migrate without --apply: every pending migration with what it would change, the\n" .
                             "checksum guard and the check of the code trees, nothing written. --simulate runs the migrations with\n" .
                             "up() in one transaction that is rolled back at the end, under the installation lock, where the\n" .
                             "database undoes schema changes too (PostgreSQL, SQLite; elsewhere the plan is listed with a warning).\n" .
                             "--diff adds what each migration changed: tables and columns added or dropped, rows per table. Files,\n" .
                             "caches and the search index a migration writes are not undone by the rollback.\n" .
                             "\n" .
                             "./console exp:migration:dry-run\n" .
                             "./console exp:migration:dry-run --simulate --diff\n" .
                             "./console exp:migration:dry-run --only=20261031120000_add_news_class --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[only:*][path:*][environment:][format:][login:][diff][simulate]', '', array(
            'only' => 'Only these migrations (migration ids, a comma list or repeated)',
            'path' => 'Migration files or folders, relative to the root or absolute (a comma list or repeated): migrate and dry-run take only the migrations in them, check checks them instead of the migration directories',
            'environment' => 'The environment (default the installation\'s: [AdminEnvironment] Level, or EXP_ENV with Level=auto; check: every environment); only the migrations for it count',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'login' => 'Run as this user (default: [RunSettings] RunAsUser of migration.ini)',
            'diff' => 'With --simulate: what each migration changed in the database (tables, columns, rows)',
            'simulate' => 'Run the migrations in a transaction that is rolled back (PostgreSQL and SQLite; elsewhere the plan is listed)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $selection = $this->selection( $options );
        if ( !empty( $options['diff'] ) && empty( $options['simulate'] ) )
            $this->usage( '--diff goes with --simulate: the changes are those of the simulated run' );
        $db = $this->database( $format );
        if ( empty( $options['simulate'] ) )
        {
            $this->finish( \expMigrationRunner::migrate( $selection + array( 'apply' => false, 'db' => $db ) ), $format );
            return;
        }
        $context = new \expMigrationContext( $db, true, $selection + array( 'no-mirror' => true ) );
        $errors = array();
        $migrations = \expMigrationRunner::migrations( $context->environment(), $errors );
        if ( isset( $selection['only'] ) || isset( $selection['path'] ) )
            $migrations = \expMigrationRunner::select( $migrations, isset( $selection['only'] ) ? $selection['only'] : array(),
                                                       isset( $selection['path'] ) ? $selection['path'] : array(), $context->root(), $errors );
        if ( $errors )
        {
            $this->finish( array( 'mode' => 'simulate', 'migrations' => array(), 'errors' => $errors, 'warnings' => array(),
                                  'exit' => \expUpgrade::EXIT_FAILED ), $format );
            return;
        }
        $simulation = new \expMigrationSimulation( $context, array( 'diff' => !empty( $options['diff'] ),
                                                                    'ignore-tables' => \expMigrationCaption::settingsFrom()['ignore-tables'] ) );
        $this->finish( $simulation->run( $migrations ), $format );
    }
}

}
