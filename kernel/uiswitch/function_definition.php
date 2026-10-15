<?php
/**
 * The fetch functions of the uiswitch module: fetch( 'uiswitch', 'toolbar' ) lists the providers the current user
 * may use on this siteaccess, with their choices (expUISwitch::toolbar()); an empty list elsewhere.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();

$FunctionList['toolbar'] = array( 'name' => 'toolbar',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'expUISwitch', 'method' => 'fetchToolbar' ),
    'parameter_type' => 'standard',
    'parameters' => array() );
