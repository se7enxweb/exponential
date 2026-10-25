<?php
/**
 * Setup > "Verify it's you": setup/reauth (doc/eng-us/guides/verify-its-you.md).
 *
 * Shows whether "Verify it's you" is on, the minutes a confirmation counts and each group of actions (account, users,
 * system, export, content) with its switch and the number of its rules; saving writes the switches to the global
 * override settings/override/site.ini.append.php (expReauthSettings) and answers with a redirect. Content (removing
 * and publishing) is off by default and switched on here. The save itself is a system setting, so it asks first.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/** @var array $Params */

$module = $Params['Module'];
$http = eZHTTPTool::instance();

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && $http->hasPostVariable( 'SaveReauthButton' ) )
{
    $current = expReauthSettings::current();
    $values = expReauthSettings::fromForm( array_keys( $current['groups'] ), $_POST );
    $error = expReauthSettings::write( $values );
    $http->setSessionVariable( 'ExpReauthSettingsFlash', $error === null
        ? array( 'kind' => 'ok', 'text' => ezpI18n::tr( 'design/admin/setup/reauth', 'The settings are saved.' ) )
        : array( 'kind' => 'bad', 'text' => ezpI18n::tr( 'design/admin/setup/reauth', 'The settings could not be saved: %reason', null, array( '%reason' => $error ) ) ) );
    return $module->redirectTo( '/setup/reauth' );
}

$message = null;
if ( $http->hasSessionVariable( 'ExpReauthSettingsFlash' ) )
{
    $message = $http->sessionVariable( 'ExpReauthSettingsFlash' );
    $http->removeSessionVariable( 'ExpReauthSettingsFlash' );
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'settings', expReauthSettings::current() );
$tpl->setVariable( 'message', $message );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/reauth.tpl' );
$Result['path'] = array( array( 'url' => 'setup/menu', 'text' => ezpI18n::tr( 'design/admin/setup/reauth', 'Setup' ) ),
                         array( 'url' => false, 'text' => ezpI18n::tr( 'design/admin/setup/reauth', "Verify it's you" ) ) );
return $Result;
