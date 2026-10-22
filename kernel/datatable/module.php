<?php
/**
 * The datatable module (doc/eng-us/features/6.0/data-tables.md): the data tables of the installation (every
 * persistent class and its table, described from its definition and from the database), the named database
 * connections, and the kernel tables they need. Setup > Data tables.
 *
 * Policies: datatable/read (the catalogue and the description of a table), datatable/manage (create the kernel
 * tables, rebuild the catalogue), datatable/connections (make, change, test and remove connections),
 * datatable/sync (the synchronisation between tables and content). Only Administrator has them in a new installation.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Data tables',
                 'variable_params' => false );

$ViewList = array();

$ViewList['dashboard'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'dashboard.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'origin' => 'Origin', 'q' => 'Query' ) );

$ViewList['table'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'table.php',
    'functions' => array( 'read' ),
    'params' => array( 'ClassName' ) );

$ViewList['rows'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'rows.php',
    'functions' => array( 'read' ),
    'params' => array( 'ClassName' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

$ViewList['schema'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'schema.php',
    'functions' => array( 'manage' ),
    'params' => array(),
    'unordered_params' => array( 'class' => 'ClassName', 'extension' => 'Extension', 'connection' => 'Connection' ) );

$ViewList['syncs'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'syncs.php',
    'functions' => array( 'sync' ),
    'params' => array() );

$ViewList['sync'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'sync.php',
    'functions' => array( 'sync' ),
    'params' => array( 'SyncID' ),
    'unordered_params' => array( 'class' => 'ClassName', 'content_class' => 'ContentClass' ) );

$ViewList['conflicts'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'conflicts.php',
    'functions' => array( 'sync' ),
    'params' => array(),
    'unordered_params' => array( 'sync' => 'SyncID', 'offset' => 'Offset' ) );

$ViewList['connections'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'connections.php',
    'functions' => array( 'connections' ),
    'params' => array() );

$ViewList['connection'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'connection.php',
    'functions' => array( 'connections' ),
    'params' => array( 'Identifier' ) );

$FunctionList = array();
// The catalogue of persistent classes and their tables, read only
$FunctionList['read'] = array();
// Create the kernel tables of the data tables, rebuild the catalogue
$FunctionList['manage'] = array();
// Make, change, test and remove named database connections
$FunctionList['connections'] = array();
// The synchronisation between data tables and content
$FunctionList['sync'] = array();

?>
