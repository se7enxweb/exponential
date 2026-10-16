<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = array( 'name' => 'User management',
                 'variable_params' => true );

$ViewList = array();
$ViewList['logout'] = array(
    'functions' => array( 'login' ),
    'script' => 'logout.php',
    'ui_context' => 'authentication',
    'params' => array( ) );

$ViewList['login'] = array(
    'functions' => array( 'login' ),
    'script' => 'login.php',
    'ui_context' => 'authentication',
    'default_action' => array( array( 'name' => 'Login',
                                      'type' => 'post',
                                      'parameters' => array( 'Login',
                                                             'Password' ) ) ),
    'single_post_actions' => array( 'LoginButton' => 'Login' ),
    'post_action_parameters' => array( 'Login' => array( 'UserLogin' => 'Login',
                                                         'UserPassword' => 'Password',
                                                         'UserRedirectURI' => 'RedirectURI' ) ),
    'params' => array( ) );

$ViewList['setting'] = array(
    'functions' => array( 'preferences' ),
    'default_navigation_part' => 'ezusernavigationpart',
    'script' => 'setting.php',
    'params' => array( 'UserID' ) );

$ViewList['preferences'] = array(
    'functions' => array( 'login' ),
    'script' => 'preferences.php',
    'params' => array( 'Function', 'Key', 'Value' ) );

// admin4's mega menu (menu.ini [TopAdminMenu] MegaMenuTabs[]): the groups and links of a top tab's menu as an HTML
// fragment without the page layout, asked for once by design/admin4/javascript/a4_mega_menu.js when the panel is first
// opened (user/megamenu/<tab>). The policy of any user of the siteaccess, as user/preferences, since the tabs belong to
// different modules: the view itself answers only for a tab in the user's top menu, and checks each link as the left
// menu does. ajax, so the address is not kept as the last page seen.
$ViewList['megamenu'] = array(
    'functions' => array( 'login' ),
    'script' => 'megamenu.php',
    'ui_context' => 'ajax',
    'params' => array( 'Tab' ) );

$ViewList['password'] = array(
    'functions' => array( 'password' ),
    'script' => 'password.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezmynavigationpart',
    'params' => array( 'UserID' ) );

$ViewList['forgotpassword'] = array(
    'functions' => array( 'password' ),
    'script' => 'forgotpassword.php',
    'params' => array( ),
    'ui_context' => 'administration',
    'single_post_actions' => array( 'GenerateButton' => 'Generate' ),
    'post_action_parameters' => array( 'Generate' => array( 'Login' => 'UserLogin',
                                                            'Email' => 'UserEmail' ) ),
    'params' => array( 'HashKey' ) );

/// \deprecated Use normal content edit view instead
$ViewList['edit'] = array(
    'functions' => array( 'login' ),
    'script' => 'edit.php',
    'ui_context' => 'edit',
    'single_post_actions' => array( 'ChangePasswordButton' => 'ChangePassword',
                                    'ChangeSettingButton' => 'ChangeSetting',
                                    'CancelButton' => 'Cancel',
                                    'EditButton' => 'Edit' ),
    'params' => array( 'UserID' ) );

$ViewList['register'] = array(
    'functions' => array( 'register' ),
    'script' => 'register.php',
    'params' => array( 'redirect_number' ),
    'ui_context' => 'edit',
    'default_navigation_part' => 'ezmynavigationpart',
    'single_post_actions' => array( 'PublishButton' => 'Publish',
                                    'CancelButton' => 'Cancel',
                                    'CustomActionButton' => 'CustomAction' ) );

$ViewList['activate'] = array(
    'functions' => array( 'login' ),
    'script' => 'activate.php',
    'ui_context' => 'authentication',
    'default_navigation_part' => 'ezmynavigationpart',
    'params' => array( 'Hash', 'MainNodeID' ) );

$ViewList['success'] = array(
    'functions' => array( 'register' ),
    'script' => 'success.php',
    'ui_context' => 'authentication',
    'default_navigation_part' => 'ezmynavigationpart',
    'params' => array( ) );

$ViewList['unactivated'] = array(
    'functions' => array( 'activation' ),
    'script' => 'unactivated.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezusernavigationpart',
    'unordered_params' => array( 'offset' => 'Offset' ),
    'single_post_actions' => array(
        'ActivateButton' => 'ActivateUsers',
        'RemoveButton' => 'RemoveUsers',
        // Sends the activation mail again, with a new link
        'ResendButton' => 'ResendActivation',
        // Removes every unactivated user, or every one matching the search
        'RemoveAllButton' => 'RemoveAllUsers'
    ),
    'post_action_parameters' => array(
        'ActivateUsers' => array( 'UserIDs' => 'DeleteIDArray' ),
        'RemoveUsers' => array( 'UserIDs' => 'DeleteIDArray' ),
        'ResendActivation' => array( 'UserIDs' => 'DeleteIDArray' ),
    ),
    'params' => array( 'SortField', 'SortOrder' ),
);

// Check user accounts: every account, or those below a user group, by filter (never signed in, not seen for N days,
// disabled, locked, orphaned, a shared e-mail address); disable, enable or unlock the ticked ones (after a
// confirmation, each needing content/edit on the account) and the CSV of the list (doc/features/6.0/user-hygiene.md)
$ViewList['hygiene'] = array(
    'functions' => array( 'hygiene' ),
    'script' => 'hygiene.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezusernavigationpart',
    'unordered_params' => array( 'offset' => 'Offset' ),
    'params' => array(),
);

// Deactivate and move / Reactivate and move back (doc/features/6.0/user-hygiene.md): disables an account and
// brings its locations together in the group for deactivated accounts, remembering where it was, or puts it back.
// Off unless site.ini [UserSettings] DeactivateAndMove=enabled; POST only, after a confirmation, each account
// needing content/edit on it and the rights to move it (see kernel/private/classes/views/user/deactivate.php)
$ViewList['deactivate'] = array(
    'functions' => array( 'deactivate' ),
    'script' => 'deactivate.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezusernavigationpart',
    'params' => array(),
);

// The page of the signed links in the "New user registered" mail of the administrator (expRegistrationAdminAction):
// user/registration/<token> shows the account (view) or asks to approve or reject it, and acts only on a POST of the
// confirmation (ConfirmButton). Opens with user/login; the view checks the right to act itself (user/activation, an
// approver of the waiting approval item, or content/edit on the account to view it).
// See kernel/private/classes/views/user/registration.php
$ViewList['registration'] = array(
    'functions' => array( 'login' ),
    'script' => 'registration.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezusernavigationpart',
    'single_post_actions' => array(
        'ConfirmButton' => 'Confirm',
        'CancelButton' => 'Cancel',
    ),
    'params' => array( 'Token' ),
);

$SiteAccess = array(
    'name'=> 'SiteAccess',
    'values'=> array(),
    'class' => 'eZSiteAccess',
    'function' => 'siteAccessList',
    'parameter' => array()
    );

$FunctionList = array();
$FunctionList['login'] = array( 'SiteAccess' => $SiteAccess );
$FunctionList['password'] = array();
$FunctionList['preferences'] = array();
$FunctionList['register'] = array();
$FunctionList['selfedit'] = array();
$FunctionList['activation'] = array();
$FunctionList['hygiene'] = array();
$FunctionList['deactivate'] = array();

?>
