<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = array( "name" => "Settings management",
                 "variable_params" => true );

$ViewList = array();
$ViewList["view"] = array(
    "script" => "view.php",
    "default_navigation_part" => "ezsetupnavigationpart",
    "params" => array( 'SiteAccess' , 'INIFile' ),
    // The search options are view parameters only ((q), (scope)/all, (changed)/1, (compare)/<siteaccess>); the search
    // form is posted and answered with a 303 to that address
    "unordered_params" => array( 'q' => 'Query', 'scope' => 'Scope', 'changed' => 'Changed', 'compare' => 'Compare' ) );
$ViewList["edit"] = array(
    "script" => "edit.php",
    'ui_context' => 'edit',
    "default_navigation_part" => "ezsetupnavigationpart",
    "params" => array( 'SiteAccess', 'INIFile', 'Block', 'Setting', 'Placement' ) );

?>
