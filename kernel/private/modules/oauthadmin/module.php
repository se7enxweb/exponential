<?php
/**
 * File containing the oauthadmin module definition.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

include_once 'kernel/private/rest/classes/lazy.php';
// Again for each request of a persistent worker, which resets ezcBaseInit's callbacks.
ezpRestDbConfig::registerCallbacks();

$Module = array( 'name' => 'Rest client admin',
                 'variable_params' => true );

$ViewList = array();

$ViewList['list'] = array(
    'script' => 'list.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
);

$ViewList['edit'] = array(
    'script' => 'edit.php',
    'params' => array( 'ApplicationID' ),
    'single_post_actions' => array( 'StoreButton' => 'Store',
                                    'DiscardButton' => 'Discard' ),
    'post_action_parameters' => array( 'Store' => array( 'Name' => 'Name',
                                                         'EndPointURI' => 'EndPointURI',
                                                         'Description' => 'Description' ) ),
    'default_navigation_part' => 'ezsetupnavigationpart',
);

$ViewList['action'] = array(
    'script' => 'action.php',
    'single_post_actions' => array( 'NewApplicationButton' => 'NewApplication',
                                    'DeleteApplicationListButton' => 'DeleteApplicationList' ),
    'post_action_parameters' => array( 'DeleteApplicationList' => array( 'ApplicationIDList' => 'DeleteIDArray',
                                                                         'ConfirmDelete' => 'ConfirmDelete' ) ),
    'default_navigation_part' => 'ezsetupnavigationpart',
);

$ViewList['view'] = array(
    'script' => 'view.php',
    'params' => array( 'ApplicationID' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
);

// The personal API keys of every user (doc/guides/api-keys.md): figures, search and filters, revoke.
// oauthadmin/keys/(user)/<id> is one user's keys, linked from the user's settings page.
$ViewList['keys'] = array(
    'script' => 'keys.php',
    'unordered_params' => array( 'status' => 'Status', 'user' => 'UserID', 'offset' => 'Offset' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
);

// Revoking keys: the selected keys, a confirmation page, then the revocation.
$ViewList['keyaction'] = array(
    'script' => 'keyaction.php',
    'single_post_actions' => array( 'RevokeKeyListButton' => 'RevokeKeyList',
                                    'RevokeOneKeyButton' => 'RevokeOneKey' ),
    'post_action_parameters' => array( 'RevokeKeyList' => array( 'KeyIDList' => 'RevokeKeyIDArray',
                                                                 'ConfirmRevoke' => 'ConfirmRevoke',
                                                                 'RedirectURI' => 'RedirectURI' ),
                                       'RevokeOneKey' => array( 'KeyID' => 'RevokeOneKeyButton',
                                                                'RedirectURI' => 'RedirectURI' ) ),
    'default_navigation_part' => 'ezsetupnavigationpart',
);

$FunctionList = array( );
?>
