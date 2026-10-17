<?php
/**
 * File containing the oauth module definition.
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

$Module = array( 'name' => 'Rest client authorization',
                 'variable_params' => true );

$ViewList = array();

$ViewList['authorize'] = array(
    'script' => 'authorize.php',
);

// oauth/authorize: a signed in user may authorize an OAuth client to act on the user's behalf. The view checks it
// itself (site.ini [RoleSettings] PolicyOmitList has oauth/authorize, so the login form can keep the request's GET
// parameters), with expOAuthAuthorizePolicy. Limited by siteaccess like user/login.
$FunctionList = array();
$FunctionList['authorize'] = array(
    'SiteAccess' => array(
        'name' => 'SiteAccess',
        'values' => array(),
        'class' => 'eZSiteAccess',
        'function' => 'siteAccessList',
        'parameter' => array()
    )
);
?>
