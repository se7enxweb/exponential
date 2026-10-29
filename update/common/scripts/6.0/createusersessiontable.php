#!/usr/bin/env php
<?php
/**
 * File containing the createusersessiontable.php upgrade script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */

/**
 * Creates the tables of the signed-in sessions, "Your devices": expuser_session (one row per sign-in) and
 * expuser_session_info (the host, the place a local GeoIP database names and the second step of each sign-in), on an
 * installation made before Exponential 6.0.15, on whatever engine it runs: MySQL/MariaDB, PostgreSQL, SQLite, Oracle
 * (ezoracle) and MongoDB, each through its schema handler, from the tables' definitions in share/db_schema.dba. A
 * table that exists is left alone, so the script can be run again. Until expuser_session is there, sessions are not
 * tracked and every page works as before; until expuser_session_info is there, they are tracked without those
 * details.
 *
 * The SQL upgrade files of MySQL, PostgreSQL and SQLite (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql)
 * create the same tables. Guide: doc/eng-us/features/6.0/user-sessions.md
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Creates the tables of the signed-in sessions (expuser_session, expuser_session_info).\n"
                                                    . "A table that exists already is left alone.",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[dry-run]', '', array( 'dry-run' => 'Say whether the tables are missing, change nothing' ) );
$script->initialize();

if ( !class_exists( 'expUserSession' ) || !class_exists( 'expUserSessionInfo' ) || !class_exists( 'expKernelTableSchema' ) )
{
    $cli->error( 'The session classes are not in the autoload array: run bin/php/ezpgenerateautoloads.php -k first.' );
    $script->shutdown( 1 );
}

$db = eZDB::instance();
$tables = array( expUserSession::TABLE, expUserSessionInfo::TABLE );
foreach ( $tables as $table )
{
    $exists = expKernelTableSchema::exists( $table, $db, true );
    $cli->output( 'Database: ' . expKernelTableSchema::type( $db ) . ', table ' . $table . ': '
                  . ( $exists === null ? 'unknown' : ( $exists ? 'exists' : 'missing' ) ) );
}
if ( $options['dry-run'] )
    $script->shutdown( 0 );

$messages = array();
$ok = expKernelTableSchema::install( $tables, $db, null, $messages );
foreach ( $messages as $m )
    $cli->output( $m );
if ( !$ok )
{
    $cli->error( 'The tables ' . implode( ', ', $tables ) . ' could not all be created.' );
    $script->shutdown( 1 );
}
$script->shutdown( 0 );
