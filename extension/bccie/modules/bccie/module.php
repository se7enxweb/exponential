<?php
/**
 * File containing the bccie module definition.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */
//
// Creation Date : 12 October 2006
// Author : Mathias VITALIS
// www.silverhand.fr
//
// This module allows for easy export collected informations
// from contentobjects to csv or sylk files
//
// Inspired from the csv export module from Gabriel Ambuehl.

// Define module name
$Module = array( 'name' => 'Export Collected Information Objects for eZ Publish' );

// Define module view and parameters
$ViewList = array();

// Define overview, export and doexport module view parameters
$ViewList['overview'] = array(
    'script' => 'overview.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezbccienavigationpart',
    'ui_context' => 'view', // 'ui_context' => 'administration',
    'unordered_params' => array( 'offset' => 'Offset' ),
    'single_post_actions' => array(
        'RemoveObjectCollectionButton' => 'RemoveObjectCollection',
        'ConfirmRemoveButton' => 'ConfirmRemoval',
        'CancelRemoveButton' => 'CancelRemoval'
    )
);

$ViewList['export'] = array(
    'script' => 'export.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezbccienavigationpart',
    'ui_context' => 'view', // 'ui_context' => 'administration',
    'params' => array( 'ObjectID' ),
    'unordered_params' => array()
);

$ViewList['doexport'] = array(
    'script' => 'doexport.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezbccienavigationpart',
    'ui_context' => 'view',
    'params' => array( 'ObjectID' ),
    'unordered_params' => array()
);

$FunctionList['read'] = array();

?>