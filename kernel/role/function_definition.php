<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$FunctionList = array();
$FunctionList['role'] = array( 'name' => 'role',
                               'operation_types' => array( 'read' ),
                               'call_method' => array( 'class' => 'eZRoleFunctionCollection',
                                                       'method' => 'fetchRole' ),
                               'parameter_type' => 'standard',
                               'parameters' => array( array( 'name' => 'role_id',
                                                             'type' => 'integer',
                                                             'required' => true ) ) );

$FunctionList['policies'] = array( 'name' => 'policies',
                                   'operation_types' => array( 'read' ),
                                   'call_method' => array( 'class' => 'eZRoleFunctionCollection',
                                                           'method' => 'fetchRolePolicies' ),
                                   'parameter_type' => 'standard',
                                   'parameters' => array( array( 'name' => 'role_id',
                                                                 'type' => 'integer',
                                                                 'required' => true ),
                                                          array( 'name' => 'offset',
                                                                 'type' => 'integer',
                                                                 'required' => false,
                                                                 'default' => 0 ),
                                                          array( 'name' => 'limit',
                                                                 'type' => 'integer',
                                                                 'required' => false,
                                                                 'default' => false ) ) );

$FunctionList['policy_count'] = array( 'name' => 'policy_count',
                                       'operation_types' => array( 'read' ),
                                       'call_method' => array( 'class' => 'eZRoleFunctionCollection',
                                                               'method' => 'fetchRolePolicyCount' ),
                                       'parameter_type' => 'standard',
                                       'parameters' => array( array( 'name' => 'role_id',
                                                                     'type' => 'integer',
                                                                     'required' => true ) ) );

?>
