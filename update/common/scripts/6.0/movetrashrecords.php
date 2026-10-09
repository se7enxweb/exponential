#!/usr/bin/env php
<?php
/**
 * File containing the movetrashrecords.php upgrade script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */

/**
 * Copies who moved what to the trash from <VarDir>/trash/trashed.json into the columns trashed_by and trashed_via
 * of ezcontentobject_trash, which the kernel writes since Exponential 6.0.15. Run it once per siteaccess with its
 * own var directory (php update/common/scripts/6.0/movetrashrecords.php -s <siteaccess>), after the database update
 * has added the columns. Rows that have a trashed_by keep it, so the script can be run again. --remove-file deletes
 * the file afterwards, when every entry in it has a trash row. On a cluster of web servers each server has its own
 * file: run the script on each of them.
 *
 * Exit status: 0 done, 1 an error (the columns are missing, the file holds no JSON object, the file cannot be
 * removed), 2 the file was kept because some of its entries have no trash row.
 *
 * Guide: doc/bc/6.0/trash.md
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Copies who trashed what from <VarDir>/trash/trashed.json into\n"
                                                    . "ezcontentobject_trash.trashed_by and trashed_via.",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[dry-run][remove-file]', '', array( 'dry-run' => 'Count only, change nothing',
                                                                     'remove-file' => 'Delete the file when every entry has a trash row' ) );
$script->initialize();

$file = \Exponential\Service\TrashRecord::file();
if ( !is_file( $file ) )
{
    $cli->output( "No $file: nothing to copy." );
    $script->shutdown( 0 );
}

if ( \Exponential\Service\TrashRecord::isUnreadable( $file ) )
{
    $cli->error( "$file holds no JSON object (cut short or edited by hand): nothing is copied and the file is kept. "
               . "Repair it from a backup, or remove it by hand." );
    $script->shutdown( 1 );
}

$db = eZDB::instance();
if ( !\Exponential\Service\TrashRecord::columnsExist( $db ) )
{
    $cli->error( 'ezcontentobject_trash has no columns trashed_by and trashed_via: run the database update first '
               . '(update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql).' );
    $script->shutdown( 1 );
}
$stats = \Exponential\Service\TrashRecord::moveToColumns( $db, (bool)$options['dry-run'] );
$cli->output( sprintf( '%s: %d entries, %d rows %s, %d rows kept their trashed_by, %d entries without a trash row, '
                       . '%d rows whose entry names no user',
                       $file, $stats['entries'], $stats['moved'], $options['dry-run'] ? 'to give a trashed_by' : 'given a trashed_by',
                       $stats['kept'], $stats['orphans'], $stats['unknown'] ) );

if ( $options['remove-file'] && !$options['dry-run'] )
{
    if ( $stats['orphans'] > 0 )
    {
        $cli->warning( 'The file is kept: some of its entries have no trash row (their objects are purged or restored, '
                     . 'or the trash row was replaced). Remove it by hand when the numbers above look right.' );
        $script->shutdown( 2 );
    }
    if ( !\Exponential\Service\TrashRecord::removeFile() )
    {
        $cli->error( "Cannot remove $file." );
        $script->shutdown( 1 );
    }
    $cli->output( "Removed $file." );
}
$script->shutdown( 0 );
