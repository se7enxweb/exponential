<?php
/**
 * The fetch functions of the uiswitch module: fetch( 'uiswitch', 'toolbar' ) lists the providers the current user
 * may use on this siteaccess, with their choices (expUISwitch::toolbar()); an empty list elsewhere.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();

$FunctionList['toolbar'] = array( 'name' => 'toolbar',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'expUISwitch', 'method' => 'fetchToolbar' ),
    'parameter_type' => 'standard',
    'parameters' => array() );

// fetch( 'uiswitch', 'front_designs' ): the designs a front siteaccess lets every visitor switch between
// (site.ini [DesignSettings] SwitchableDesigns[]), with the current one; empty where the siteaccess does not switch
$FunctionList['front_designs'] = array( 'name' => 'front_designs',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'expFrontDesignSwitch', 'method' => 'fetchSwitch' ),
    'parameter_type' => 'standard',
    'parameters' => array() );
