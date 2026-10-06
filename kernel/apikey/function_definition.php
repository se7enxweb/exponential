<?php
/**
 * The fetch functions of the apikey module (doc/guides/api-keys.md):
 *
 * - fetch( 'apikey', 'can_create' ): whether the current user may make API keys (for the link on the profile);
 * - fetch( 'apikey', 'counts', hash( 'user_id', <id> ) ): active, expired, revoked, total, never_used and
 *   expiring_soon of a user's keys. Only for the current user's own id, or for a user with access to oauthadmin;
 *   anyone else gets false.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();

$FunctionList['can_create'] = array( 'name' => 'can_create',
    'call_method' => array( 'class' => 'expApiKeyFunctionCollection', 'method' => 'fetchCanCreate' ),
    'parameter_type' => 'standard',
    'parameters' => array() );

$FunctionList['counts'] = array( 'name' => 'counts',
    'call_method' => array( 'class' => 'expApiKeyFunctionCollection', 'method' => 'fetchCounts' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'default' => 0 ) ) );

?>
