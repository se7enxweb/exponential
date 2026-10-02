#!/usr/bin/env php
<?php
/**
 * File containing the createaudittables.php upgrade script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */

/**
 * Creates the audit index tables (expaudit_event, expaudit_cursor, expaudit_file) on an installation made before
 * Exponential 6.0.15, on whatever engine it runs: MySQL/MariaDB, PostgreSQL, SQLite, Oracle (ezoracle) and MongoDB,
 * each through its schema handler, with the engine's full-text search where it has one. Tables that exist are left
 * alone, so the script can be run again. Then it indexes the audit files written so far (--no-index skips that).
 *
 * The SQL upgrade files of MySQL, PostgreSQL and SQLite (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql)
 * contain the same statements. Guide: doc/bc/6.0/audit.md, "The index".
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Creates the audit index tables and indexes the audit files.\n"
                                                    . "Tables that exist already are left alone.",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[dry-run][no-index]', '', array( 'dry-run' => 'Show which tables are missing, change nothing',
                                                                  'no-index' => 'Create the tables only' ) );
$script->initialize();

if ( !class_exists( 'expAuditIndexSchema' ) )
{
    $cli->error( 'The audit index classes are not in the autoload array: run bin/php/ezpgenerateautoloads.php -k first.' );
    $script->shutdown( 1 );
}

$db = eZDB::instance();
$missing = expAuditIndexSchema::missingTables( $db );
$cli->output( 'Database: ' . expAuditIndexSchema::type( $db ) . ', missing tables: ' . ( $missing === null ? 'unknown' : ( $missing ? implode( ', ', $missing ) : 'none' ) ) );
if ( $options['dry-run'] )
    $script->shutdown( 0 );

$messages = array();
$ok = expAuditIndexSchema::install( $db, $messages );
foreach ( $messages as $m )
    $cli->output( $m );
$cli->output( 'Full-text search: ' . expAuditIndexSchema::fullTextKind( $db ) );
if ( !$ok )
{
    $cli->error( 'The audit index tables could not be created.' );
    $script->shutdown( 1 );
}

if ( !$options['no-index'] )
{
    $indexer = new expAuditIndexer( $db );
    $stats = $indexer->run( array( 'wait' => true ) );
    $cli->output( sprintf( 'Indexed %d records from %d files in %d ms%s', $stats['rows'], $stats['files'], $stats['ms'],
                           $stats['broken'] ? '; broken: ' . json_encode( $stats['broken'] ) : '' ) );
    if ( !$stats['ok'] )
        $cli->error( $stats['error'] );
}
$script->shutdown( 0 );
