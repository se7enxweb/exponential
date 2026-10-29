#!/usr/bin/env php
<?php
/**
 * File containing the createuseraudittable.php upgrade script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */

/**
 * Creates the table of the user audit log (expuser_audit) on an installation made before Exponential 6.0.15, on
 * whatever engine it runs: MySQL/MariaDB, PostgreSQL, SQLite and Oracle (ezoracle), each through its schema handler,
 * from the table's definition in share/db_schema.dba. A table that exists is left alone, so the script can be run
 * again. Until the table exists nothing is stored and user/audit says so; the audit files keep every event anyway.
 *
 * The SQL upgrade files of MySQL, PostgreSQL and SQLite (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql)
 * create the same table. Guide: doc/eng-us/features/6.0/user-audit.md.
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Creates the table of the user audit log (expuser_audit).\n"
                                                    . "A table that exists already is left alone.",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[dry-run]', '', array( 'dry-run' => 'Say whether the table is missing, change nothing' ) );
$script->initialize();

if ( !class_exists( 'expUserAudit' ) )
{
    $cli->error( 'The user audit class is not in the autoload array: run bin/php/ezpgenerateautoloads.php -k first.' );
    $script->shutdown( 1 );
}

$db = eZDB::instance();
if ( $db->databaseName() === 'mongo' )
{
    $cli->output( 'MongoDB: the user audit log is not kept in this database (the audit files keep every event).' );
    $script->shutdown( 0 );
}
$messages = array();
$ok = expUserAudit::install( $db, $messages, (bool)$options['dry-run'] );
$cli->output( 'Database: ' . expAuditIndexSchema::type( $db ) );
foreach ( $messages as $m )
    $cli->output( $m );
if ( !$ok && !$options['dry-run'] )
{
    $cli->error( 'FAIL: the table expuser_audit could not be created.' );
    $script->shutdown( 1 );
}
$cli->output( $options['dry-run'] ? 'PASS (dry run, nothing changed)' : 'PASS: the table expuser_audit exists' );
$script->shutdown( 0 );
