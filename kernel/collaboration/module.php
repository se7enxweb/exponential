<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = array( 'name' => 'eZCollaboration' );

$ViewList = array();
$ViewList['action'] = array(
    'script' => 'action.php',
    "default_navigation_part" => 'ezmynavigationpart',
    'default_action' => array( array( 'name' => 'Custom',
                                      'type' => 'post',
                                      'parameters' => array( 'CollaborationActionCustom',
                                                             'CollaborationTypeIdentifier',
                                                             'CollaborationItemID' ) ) ),
    'post_action_parameters' => array( 'Custom' => array( 'TypeIdentifer' => 'CollaborationTypeIdentifier',
                                                          'ItemID' => 'CollaborationItemID' ) ),
    'params' => array() );
$ViewList['view'] = array(
    'script' => 'view.php',
    "default_navigation_part" => 'ezmynavigationpart',
    'params' => array( 'ViewMode' ),
    "unordered_params" => array( "language" => "Language",
                                 "offset" => "Offset",
                                 "status" => "Status",
                                 "role" => "Role",
                                 "type" => "Type",
                                 "group" => "Group" ) );
$ViewList['item'] = array(
    'script' => 'item.php',
    "default_navigation_part" => 'ezmynavigationpart',
    'params' => array( 'ViewMode', 'ItemID' ),
    "unordered_params" => array( "language" => "Language",
                                 "offset" => "Offset" ) );
$ViewList['group'] = array(
    'script' => 'group.php',
    "default_navigation_part" => 'ezmynavigationpart',
    'params' => array( 'ViewMode', 'GroupID' ),
    "unordered_params" => array( "language" => "Language",
                                 "offset" => "Offset",
                                 "status" => "Status",
                                 "role" => "Role",
                                 "type" => "Type" ) );

?>
