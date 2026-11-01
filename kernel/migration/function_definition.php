<?php
/**
 * The fetch functions of the migration module: summary, the counts of the migrations' status and the latest run, for
 * the dashboard block "migrations" (expMigrationAdmin::fetchSummary()). Empty for a user without migration/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();

$FunctionList['summary'] = array( 'name' => 'summary',
    'call_method' => array( 'class' => 'expMigrationAdmin', 'method' => 'fetchSummary' ),
    'parameter_type' => 'standard',
    'parameters' => array() );

?>
