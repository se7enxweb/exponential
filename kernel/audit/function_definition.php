<?php
/**
 * The fetch functions of the audit module (doc/bc/6.0/audit.md, "Template operator and fetch"): events, count,
 * event, chain_status and can_read. Each checks audit/read with its Channel limitation and returns an empty result
 * to a user without it. The filters are the console's. Written by a generator from one list of filters.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();

$FunctionList['events'] = array( 'name' => 'events',
    'call_method' => array( 'class' => 'expAuditFunctionCollection', 'method' => 'fetchEvents' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'channel', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'name', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'user', 'type' => 'integer', 'required' => false, 'default' => false ),
        array( 'name' => 'login', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'object', 'type' => 'mixed', 'required' => false, 'default' => false ),
        array( 'name' => 'target', 'type' => 'mixed', 'required' => false, 'default' => false ),
        array( 'name' => 'result', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'severity', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'request', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'job', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'run', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'from', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'to', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'q', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ),
        array( 'name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => 10 ) ) );

$FunctionList['count'] = array( 'name' => 'count',
    'call_method' => array( 'class' => 'expAuditFunctionCollection', 'method' => 'fetchCount' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'channel', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'name', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'user', 'type' => 'integer', 'required' => false, 'default' => false ),
        array( 'name' => 'login', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'object', 'type' => 'mixed', 'required' => false, 'default' => false ),
        array( 'name' => 'target', 'type' => 'mixed', 'required' => false, 'default' => false ),
        array( 'name' => 'result', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'severity', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'request', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'job', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'run', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'from', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'to', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'q', 'type' => 'string', 'required' => false, 'default' => false ) ) );

$FunctionList['event'] = array( 'name' => 'event',
    'call_method' => array( 'class' => 'expAuditFunctionCollection', 'method' => 'fetchEvent' ),
    'parameter_type' => 'standard',
    'parameters' => array( array( 'name' => 'id', 'type' => 'string', 'required' => true ) ) );

$FunctionList['chain_status'] = array( 'name' => 'chain_status',
    'call_method' => array( 'class' => 'expAuditFunctionCollection', 'method' => 'fetchChainStatus' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'channel', 'type' => 'string', 'required' => false, 'default' => false ),
        array( 'name' => 'verify', 'type' => 'boolean', 'required' => false, 'default' => false ) ) );

$FunctionList['can_read'] = array( 'name' => 'can_read',
    'call_method' => array( 'class' => 'expAuditFunctionCollection', 'method' => 'fetchCanRead' ),
    'parameter_type' => 'standard',
    'parameters' => array( array( 'name' => 'channel', 'type' => 'string', 'required' => false, 'default' => false ) ) );

?>
