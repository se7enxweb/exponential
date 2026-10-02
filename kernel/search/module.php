<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = array( "name" => "eZSearch",
                 "variable_params" => true );

$ViewList = array();

$ViewList["stats"] = array(
    "script" => "stats.php",
    "default_navigation_part" => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'ResetSearchStatsButton' => 'ResetSearchStats' ),
    "params" => array( ),
    "unordered_params" => array( "offset" => "Offset" ) );

?>
