#!/usr/bin/env php
<?php
/**
 * File containing the createtransferruntable.php upgrade script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */

/**
 * Creates the run history of the import and export tools (exp_transfer_run) on an installation made before
 * Exponential 6.0.15, on whatever engine it runs: MySQL/MariaDB, PostgreSQL, SQLite, Oracle (ezoracle) and MongoDB,
 * each through its schema handler, from the table's definition in share/db_schema.dba. A table that exists is left
 * alone, so the script can be run again. Until the table exists the tools run as before and write no history.
 *
 * The SQL upgrade files of MySQL, PostgreSQL and SQLite (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql)
 * create the same table. Guide: doc/eng-us/features/6.0/transfer-runs.md.
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Creates the run history of the import and export tools (exp_transfer_run).\n"
                                                    . "A table that exists already is left alone.",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[dry-run]', '', array( 'dry-run' => 'Say whether the table is missing, change nothing' ) );
$script->initialize();

if ( !class_exists( 'expKernelTransferRunSchema' ) )
{
    $cli->error( 'The transfer classes are not in the autoload array: run bin/php/ezpgenerateautoloads.php -k first.' );
    $script->shutdown( 1 );
}

$db = eZDB::instance();
$exists = expKernelTransferRunSchema::exists( $db, true );
$cli->output( 'Database: ' . expAuditIndexSchema::type( $db ) . ', table exp_transfer_run: '
              . ( $exists === null ? 'unknown' : ( $exists ? 'exists' : 'missing' ) ) );
if ( $options['dry-run'] )
    $script->shutdown( 0 );

$messages = array();
$ok = expKernelTransferRunSchema::install( $db, $messages );
foreach ( $messages as $m )
    $cli->output( $m );
if ( !$ok )
{
    $cli->error( 'The table exp_transfer_run could not be created.' );
    $script->shutdown( 1 );
}
$script->shutdown( 0 );
