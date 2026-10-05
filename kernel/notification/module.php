<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = array( "name" => "eZNotification",
                 "variable_params" => true );


$ViewList = array();
$ViewList["settings"] = array(
    "functions" => array( 'use' ),
    "script" => "settings.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezmynavigationpart',
    "params" => array( ),
    'unordered_params' => array( 'offset' => 'Offset', 'q' => 'Query', 'class' => 'ClassFilter' ) );

$ViewList["runfilter"] = array(
    "functions" => array( 'administrate' ),
    "script" => "runfilter.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

// The administrator's page: pending events, the digest items, the last runs of the notification cronjob, problems
$ViewList["status"] = array(
    "functions" => array( 'administrate' ),
    "script" => "status.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

// The status page's "Run now": starts exp:notification:run in the background and answers its progress as JSON
$ViewList["job"] = array(
    "functions" => array( 'administrate' ),
    "script" => "job.php",
    'ui_context' => 'ajax',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( 'JobID', 'Offset' ) );

$ViewList["addtonotification"] = array(
    "functions" => array( 'use' ),
    "script" => "addtonotification.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezcontentnavigationpart',
    "params" => array( 'ContentNodeID' ) );

$FunctionList['use'] = array( );
$FunctionList['administrate'] = array( );


?>
