<?php
/**
 * The fetch functions of the datatable module (doc/eng-us/features/6.0/data-tables.md, "In templates"): list, count,
 * row, pointing_at and can. See expDataTableFunctionCollection.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();

$FunctionList['list'] = array( 'name' => 'list',
    'call_method' => array( 'class' => 'expDataTableFunctionCollection', 'method' => 'fetchList' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'class', 'type' => 'string', 'required' => true ),
        array( 'name' => 'conditions', 'type' => 'array', 'required' => false, 'default' => false ),
        array( 'name' => 'sort', 'type' => 'array', 'required' => false, 'default' => false ),
        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ),
        array( 'name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => 50 ) ) );

$FunctionList['count'] = array( 'name' => 'count',
    'call_method' => array( 'class' => 'expDataTableFunctionCollection', 'method' => 'fetchCount' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'class', 'type' => 'string', 'required' => true ),
        array( 'name' => 'conditions', 'type' => 'array', 'required' => false, 'default' => false ) ) );

$FunctionList['row'] = array( 'name' => 'row',
    'call_method' => array( 'class' => 'expDataTableFunctionCollection', 'method' => 'fetchRow' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'class', 'type' => 'string', 'required' => true ),
        array( 'name' => 'key', 'type' => 'array', 'required' => true ) ) );

$FunctionList['pointing_at'] = array( 'name' => 'pointing_at',
    'call_method' => array( 'class' => 'expDataTableFunctionCollection', 'method' => 'fetchPointingAt' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'object_id', 'type' => 'integer', 'required' => true ),
        array( 'name' => 'node_id', 'type' => 'integer', 'required' => false, 'default' => 0 ) ) );

$FunctionList['can'] = array( 'name' => 'can',
    'call_method' => array( 'class' => 'expDataTableFunctionCollection', 'method' => 'fetchCan' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'function', 'type' => 'string', 'required' => true ) ) );

?>
