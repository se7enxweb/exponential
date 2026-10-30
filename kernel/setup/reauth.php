<?php
/**
 * Setup > "Verify it's you": setup/reauth[/<siteaccess>] (doc/eng-us/guides/verify-its-you.md).
 *
 * Shows whether "Verify it's you" is on, the minutes a confirmation counts and each group of actions (account, users,
 * system, export, delete, content) with its switch, the number of its rules and, for content, the user groups it asks
 * (none ticked: every user). Without a siteaccess the page edits the values of every siteaccess, written to the global
 * override settings/override/site.ini.append.php; with one (chosen in the page, [SiteAccessSettings]
 * AvailableSiteAccessList[]) it edits settings/siteaccess/<name>/site.ini.append.php, so content can ask on the
 * editors' siteaccess only. Saving (expReauthSettings) answers with a redirect. Content (removing, publishing, storing
 * a draft) is off by default and switched on here. The save itself is a system setting, so it asks first.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/** @var array $Params */

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$ini = eZINI::instance();
$siteAccesses = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' )
    ? array_values( array_filter( array_map( 'strval', (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ), 'strlen' ) ) : array();
$scope = expReauthSettings::scope( isset( $Params['SiteAccess'] ) ? $Params['SiteAccess'] : '', $siteAccesses );
$self = '/setup/reauth' . ( $scope !== '' ? '/' . $scope : '' );

// the scope chooser is a GET form: it opens the page of the scope chosen
if ( $_SERVER['REQUEST_METHOD'] === 'GET' && isset( $_GET['ReauthScope'] ) )
{
    $chosen = expReauthSettings::scope( $_GET['ReauthScope'], $siteAccesses );
    return $module->redirectTo( '/setup/reauth' . ( $chosen !== '' ? '/' . $chosen : '' ) );
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && $http->hasPostVariable( 'SaveReauthButton' ) )
{
    $current = expReauthSettings::current( $scope );
    $values = expReauthSettings::fromForm( array_keys( $current['groups'] ), $_POST );
    $error = expReauthSettings::write( $values, $scope );
    $http->setSessionVariable( 'ExpReauthSettingsFlash', $error === null
        ? array( 'kind' => 'ok', 'text' => ezpI18n::tr( 'design/admin/setup/reauth', 'The settings are saved.' ) )
        : array( 'kind' => 'bad', 'text' => ezpI18n::tr( 'design/admin/setup/reauth', 'The settings could not be saved: %reason', null, array( '%reason' => $error ) ) ) );
    return $module->redirectTo( $self );
}

$message = null;
if ( $http->hasSessionVariable( 'ExpReauthSettingsFlash' ) )
{
    $message = $http->sessionVariable( 'ExpReauthSettingsFlash' );
    $http->removeSessionVariable( 'ExpReauthSettingsFlash' );
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'settings', expReauthSettings::current( $scope ) );
$tpl->setVariable( 'scope', $scope );
$tpl->setVariable( 'siteaccesses', $siteAccesses );
$tpl->setVariable( 'action_uri', $self );
$tpl->setVariable( 'user_groups', expReauthSettings::userGroupChoices() );
// the groups that show a user group picker
$tpl->setVariable( 'user_group_pickers', array( 'content' ) );
$tpl->setVariable( 'message', $message );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/reauth.tpl' );
$Result['path'] = array( array( 'url' => 'setup/menu', 'text' => ezpI18n::tr( 'design/admin/setup/reauth', 'Setup' ) ),
                         array( 'url' => false, 'text' => ezpI18n::tr( 'design/admin/setup/reauth', "Verify it's you" ) ) );
return $Result;
