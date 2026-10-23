<?php
/**
 * File containing the expimageeditor module definition
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expimageeditor
 */

$Module = array( 'name' => 'expimageeditor' );

$ViewList = array();

// Stores the image the editor posts as the image of the draft's attribute; answers JSON
$ViewList['save'] = array( 'script' => 'save.php',
                           'functions' => array( 'edit' ),
                           'params' => array( 'attribute_id', 'version' ) );

$FunctionList = array();
$FunctionList['edit'] = array();
