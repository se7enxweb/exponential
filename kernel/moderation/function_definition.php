<?php
/**
 * The fetch functions of the moderation module: summary, the objects held for review and the new objects of today, for
 * the dashboard block "moderation" (expModerationAdmin::fetchSummary()). Empty for a user without moderation/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();

$FunctionList['summary'] = array( 'name' => 'summary',
    'call_method' => array( 'class' => 'expModerationAdmin', 'method' => 'fetchSummary' ),
    'parameter_type' => 'standard',
    'parameters' => array() );

?>
