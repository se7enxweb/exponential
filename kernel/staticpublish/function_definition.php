<?php
/**
 * The fetch functions of the staticpublish module: summary, the targets with their state, for the dashboard block
 * "staticpublish" (expStaticAdmin::fetchSummary()). Empty for a user without staticpublish/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();

$FunctionList['summary'] = array( 'name' => 'summary',
    'call_method' => array( 'class' => 'expStaticAdmin', 'method' => 'fetchSummary' ),
    'parameter_type' => 'standard',
    'parameters' => array() );

?>
