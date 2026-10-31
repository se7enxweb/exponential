#!/usr/bin/env php
<?php
/**
 * File containing the createbookmarkviewtable.php upgrade script
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */

/**
 * Creates the table of the bookmarks of system pages (expbookmark_view) on an installation made before Exponential
 * 6.0.15, on whatever engine it runs: MySQL/MariaDB, PostgreSQL, SQLite, Oracle (ezoracle) and MongoDB, each through
 * its schema handler, from the table's definition in share/db_schema.dba. A table that exists is left alone, so the
 * script can be run again. Until the table exists the administration offers no "Bookmark this page" and node
 * bookmarks work as before.
 *
 * A table made by an early 6.0.15 build has the address (module_uri) only: the script adds the parsed columns
 * (module_name, view_name, view_parameters) and their index when they are missing, and fills them from the address of
 * every row (the upgrade steps 6.0.15-bookmark-view-columns and 6.0.15-bookmark-view-fill of ./console exp:upgrade).
 *
 * With --rename=<old>=<new> it moves the bookmarks of a module, or of one view, to its new name after a module or a
 * view was renamed: --rename=oldmodule=newmodule, --rename=oldmodule/oldview=newmodule/newview. The parameters, names,
 * folders and order of the bookmarks stay.
 *
 * The SQL upgrade files of MySQL, PostgreSQL and SQLite (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql)
 * create the same table. Guide: doc/eng-us/features/6.0/bookmarks-of-system-pages.md.
 *
 *   php update/common/scripts/6.0/createbookmarkviewtable.php [--dry-run] [--rename=<old>=<new>] [-s <admin siteaccess>]
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Creates the table of the bookmarks of system pages (expbookmark_view), adds and fills\n"
                                                    . "its parsed columns, and moves bookmarks to a renamed module or view (--rename).\n"
                                                    . "What exists already is left alone.",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[dry-run][rename:]', '',
                                array( 'dry-run' => 'Say what is missing or would change, change nothing',
                                       'rename' => 'Move bookmarks to a renamed module or view: <old>=<new>, each "module" or "module/view"' ) );
$script->initialize();

if ( !class_exists( 'expBookmarkView' ) || !class_exists( 'expUpgradeStepBookmarkViewColumns' ) )
{
    $cli->error( 'The classes of the bookmarks of system pages are not in the autoload array: run bin/php/ezpgenerateautoloads.php -k first.' );
    $script->shutdown( 1 );
}

$dryRun = (bool)$options['dry-run'];
$db = eZDB::instance();
$exists = expBookmarkView::exists( $db, true );
$cli->output( 'Database: ' . expAuditIndexSchema::type( $db ) . ', table expbookmark_view: '
              . ( $exists === null ? 'unknown' : ( $exists ? 'exists' : 'missing' ) ) );

if ( !$dryRun )
{
    $messages = array();
    $ok = expBookmarkView::install( $db, $messages );
    foreach ( $messages as $m )
        $cli->output( $m );
    if ( !$ok )
    {
        $cli->error( 'The table expbookmark_view could not be created.' );
        $script->shutdown( 1 );
    }
}
else if ( $exists !== true )
{
    $script->shutdown( 0 );
}

// the parsed columns and their index (a table of an early 6.0.15 build), then the rows filled from their address
$context = new expUpgradeContext( $db, !$dryRun );
foreach ( array( new expUpgradeStepBookmarkViewColumns(), new expUpgradeStepBookmarkViewFill() ) as $step )
{
    $state = $step->detect( $context );
    $cli->output( $step->id() . ': ' . $state['effect'] . ( $state['detail'] !== '' ? ' (' . $state['detail'] . ')' : '' ) );
    if ( $state['effect'] !== expUpgradeStep::MISSING )
        continue;
    try
    {
        foreach ( $dryRun ? $step->plan( $context ) : $step->apply( $context ) as $line )
            $cli->output( '  ' . ( $dryRun ? 'would: ' : '' ) . $line );
    }
    catch ( Exception $e )
    {
        $cli->error( $step->id() . ': ' . $e->getMessage() );
        $script->shutdown( 1 );
    }
}

if ( $options['rename'] )
{
    $pair = explode( '=', (string)$options['rename'], 2 );
    $changes = count( $pair ) === 2 ? expBookmarkView::renameModuleView( trim( $pair[0] ), trim( $pair[1] ), !$dryRun ) : false;
    if ( $changes === false )
    {
        $cli->error( '--rename takes <old>=<new>, each "module" or "module/view"; a module alone cannot become one view.' );
        $script->shutdown( 1 );
    }
    foreach ( $changes as $bookmarkID => $change )
        $cli->output( ( $dryRun ? 'would move' : 'moved' ) . " bookmark $bookmarkID: {$change['from']} -> {$change['to']}" );
    $cli->output( count( $changes ) . ' bookmark(s) ' . ( $dryRun ? 'would be moved' : 'moved' ) );
}
$script->shutdown( 0 );
