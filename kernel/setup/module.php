<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = array( "name" => "eZSetup",
                 "variable_params" => true,
                 'ui_component_match' => 'view',
                 "function" => array(
                     "script" => "setup.php",
                     "params" => array( ) ) );

$ViewList = array();
$ViewList["init"] = array(
    'functions' => array( 'install' ),
    "script" => "ezsetup.php",
    'single_post_actions' => array( 'ChangeStepAction' => 'ChangeStep' ),
    'post_value_action_parameters' => array( 'ChangeStep' => array( 'Step' => 'StepButton' ) ),
    "params" => array() );

$ViewList["cache"] = array(
    "script" => "cache.php",
    'functions' => array( 'managecache' ),
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'ClearCacheButton' => 'ClearCache',
                                    'ClearAllCacheButton' => 'ClearAllCache',
                                    'ClearContentCacheButton' => 'ClearContentCache',
                                    'ClearINICacheButton' => 'ClearINICache',
                                    'ClearTemplateCacheButton' => 'ClearTemplateCache',
                                    'RegenerateStaticCacheButton' => 'RegenerateStaticCache' ),
    'post_action_parameters' => array( 'ClearCache' => array( 'CacheList' => 'CacheList' ),
                                       // Which site to generate, chosen on the
                                       // page before the button is pressed.
                                       'RegenerateStaticCache' => array( 'StaticCacheSiteAccess' => 'StaticCacheSiteAccess' ) ),
    "params" => array() );

$ViewList['cachetoolbar'] = array(
    'script' => 'cachetoolbar.php',
    'functions' => array( 'managecache' ),
    'single_post_actions' => array( 'ClearCacheButton' => 'ClearCache' ),
    'post_action_parameters' => array( 'ClearCache' => array( 'CacheType' => 'CacheTypeValue',
                                                              'NodeID' => 'NodeID',
                                                              'ObjectID' => 'ObjectID' ) ),
    'params' => array() );

$ViewList['settingstoolbar'] = array(
    'functions' => array( 'setup' ),
    'script' => 'settingstoolbar.php',
    'single_post_actions' => array( 'SetButton' => 'Set' ),
    'post_action_parameters' => array( 'Set' => array( 'SiteAccess' => 'SiteAccess',
                                                       'AllSettingsList' => 'AllSettingsList',
                                                       'SelectedList' => 'SelectedList' ) ),
    'params' => array() );

$ViewList['session'] = array(
    'functions' => array( 'administrate' ),
    'script'                  => 'session.php',
    'ui_context'              => 'administration',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions'     => array( 'RemoveAllSessionsButton' => 'RemoveAllSessions',
                                        'ShowAllUsersButton' => 'ShowAllUsers',
                                        'ChangeFilterButton' => 'ChangeFilter',
                                        'RemoveTimedOutSessionsButton' => 'RemoveTimedOutSessions',
                                        'RemoveSelectedSessionsButton' => 'RemoveSelectedSessions' ),
    'post_action_parameters' => array( 'ChangeFilter' => array( 'FilterType' => 'FilterType',
                                                                'ExpirationFilterType' => 'ExpirationFilterType',
                                                                'InactiveUsersCheck' => 'InactiveUsersCheck',
                                                                'InactiveUsersCheckExists' => 'InactiveUsersCheckExists' ) ),
    'params' => array( 'UserID' ) );

$ViewList["info"] = array(
    'functions' => array( 'system_info' ),
    "script" => "info.php",
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( 'Mode' ) );

$ViewList["rad"] = array(
    'functions' => array( 'setup' ),
    "script" => "rad.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    // Which of the extension points to list. In the address so a filtered list
    // can be linked to and come back the same.
    'unordered_params' => array( 'show' => 'Show' ),
    "params" => array( ) );

$ViewList["radsurvey"] = array(
    'functions' => array( 'setup' ),
    "script" => "radsurvey.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    // Which part of the survey to show, and how far down it. In the address so
    // a place in a list of several hundred can be linked to.
    'unordered_params' => array( 'show'   => 'Show',
                                 'offset' => 'Offset',
                                 'find'   => 'Find',
                                 // The class loader check takes seconds, so it
                                 // is asked for in the address rather than run
                                 // every time the page is opened.
                                 'check'  => 'Check',
                                 // Which kind of finding, so a number in the
                                 // summary can link to the ones behind it.
                                 'kind'   => 'Kind' ),
    "params" => array( ) );

$ViewList["settingsextension"] = array(
    'functions' => array( 'setup' ),
    "script" => "settingsextension.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

$ViewList["contentextension"] = array(
    'functions' => array( 'setup' ),
    "script" => "contentextension.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

$ViewList["modulewizard"] = array(
    'functions' => array( 'setup' ),
    "script" => "modulewizard.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

$ViewList["kerneloverride"] = array(
    'functions' => array( 'setup' ),
    "script" => "kerneloverride.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

$ViewList["designextension"] = array(
    'functions' => array( 'setup' ),
    "script" => "designextension.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

$ViewList["handlerextension"] = array(
    'functions' => array( 'setup' ),
    "script" => "handlerextension.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    // Which kind of handler, so each is its own address and its own tool.
    "params" => array( 'Kind' ) );

$ViewList["workflowevent"] = array(
    'functions' => array( 'setup' ),
    "script" => "workflowevent.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

$ViewList["moduleextension"] = array(
    'functions' => array( 'setup' ),
    "script" => "moduleextension.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( ) );

$ViewList["datatype"] = array(
    'functions' => array( 'setup' ),
    "script" => "datatype.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'CreateOverrideButton' => 'CreateOverride'
                                    ),
    "params" => array( ) );

$ViewList["templateoperator"] = array(
    'functions' => array( 'setup' ),
    "script" => "templateoperator.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'CreateOverrideButton' => 'CreateOverride'
                                    ),
    "params" => array( ) );

$ViewList["extensions"] = array(
    'functions' => array( 'setup' ),
    "script" => "extensions.php",
    'ui_context' => 'administration',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'ActivateExtensionsButton' => 'ActivateExtensions',
                                    'GenerateAutoloadArraysButton' => 'GenerateAutoloadArrays' ),
    "params" => array( 'ExtensionName', 'ExtensionFormat' ) );

$ViewList['menu'] = array(
    'functions' => array( 'setup' ),
    'script' => 'setupmenu.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( ) );

// The preloader's console, and the stream that drives it. The stream is a view
// rather than a standalone entry point so it goes through the same siteaccess
// and policy checks as the page that opens it.
$ViewList['preload'] = array(
    'functions' => array( 'preload' ),
    'script' => 'preload.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array() );

$ViewList['preloadstream'] = array(
    'functions' => array( 'preload' ),
    'script' => 'preloadstream.php',
    'ui_context' => 'ajax',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array(),
    'unordered_params' => array( 'maxpages' => 'MaxPages',
                                 'maxdepth' => 'MaxDepth' ) );

// The cronjobs console, and the stream that follows a running job's output.
// Launch, stop and clear are actions on the console rather than views of their
// own, so they go through this module's post action handling and one policy.
$ViewList['cronjobs'] = array(
    'functions' => array( 'managecronjobs' ),
    'script' => 'cronjobs.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'LaunchCronjobButton' => 'LaunchCronjob',
                                    'LaunchCronjobScriptButton' => 'LaunchCronjobScript',
                                    'StopCronjobButton' => 'StopCronjob',
                                    'ClearCronjobLogButton' => 'ClearCronjobLog' ),
    // Each part's own submit button carries the part name as its value, so one
    // form serves every part and the page needs no javascript to launch one.
    'post_action_parameters' => array( 'LaunchCronjob' => array( 'CronjobPart' => 'LaunchCronjobButton',
                                                                 'CronjobSiteAccess' => 'CronjobSiteAccess' ),
                                       // The button carries "part|script", so one
                                       // form can run any single script without a
                                       // field per row.
                                       'LaunchCronjobScript' => array( 'CronjobTarget' => 'LaunchCronjobScriptButton',
                                                                       'CronjobSiteAccess' => 'CronjobSiteAccess' ) ),
    'params' => array() );

$ViewList['cronjobsstream'] = array(
    'functions' => array( 'managecronjobs' ),
    'script' => 'cronjobsstream.php',
    'ui_context' => 'ajax',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array(),
    'unordered_params' => array( 'offset' => 'Offset' ) );

// The stream that drives the static cache generator on the cache view. A view
// rather than a standalone entry point so it goes through the same siteaccess
// and policy checks as the page that opens it, and the same managecache policy
// as the rest of setup/cache.
$ViewList['staticcachestream'] = array(
    'functions' => array( 'managecache' ),
    'script' => 'staticcachestream.php',
    'ui_context' => 'ajax',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array(),
    'unordered_params' => array( 'siteaccess' => 'SiteAccess',
                                 'maxpages' => 'MaxPages',
                                 'maxdepth' => 'MaxDepth',
                                 'purge' => 'Purge' ) );

$ViewList['systemupgrade'] = array(
    'functions' => array( 'setup' ),
    'script' => 'systemupgrade.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'MD5CheckButton' => 'MD5Check',
                                    'DBCheckButton' => 'DBCheck' ),
    'params' => array( ) );


/*! Provided for backwards compatibility */
$ViewList["toolbarlist"] = array(
    'functions' => array( 'setup' ),
    "script" => "toolbarlist.php",
    "default_navigation_part" => 'ezsetupnavigationpart',
    "params" => array( 'SiteAccess' ) );

$ViewList["toolbar"] = array(
    'functions' => array( 'setup' ),
    "script" => "toolbar.php",
    'ui_context' => 'edit',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'post_actions' => array( 'BrowseActionName' ),
    "params" => array( 'SiteAccess', 'Position' ) );

$ViewList["menuconfig"] = array(
    'functions' => array( 'setup' ),
    "script" => "menuconfig.php",
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'StoreButton' => 'Store',
                                    'SelectCurrentSiteAccessButton' => 'SelectCurrentSiteAccess' ),
    "params" => array() );

$ViewList["templatelist"] = array(
    'functions' => array( 'setup' ),
    'script' => 'templatelist.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

$ViewList["templateview"] = array(
    'functions' => array( 'setup' ),
    "script" => "templateview.php",
    "default_navigation_part" => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'SelectCurrentSiteAccessButton' => 'SelectCurrentSiteAccess',
                                    'RemoveOverrideButton' => 'RemoveOverride',
                                    'UpdateOverrideButton' => 'UpdateOverride',
                                    'NewOverrideButton' => 'NewOverride' ),
    "params" => array( ) );

$ViewList["templateedit"] = array(
    'functions' => array( 'setup' ),
    "script" => "templateedit.php",
    'ui_context' => 'edit',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'SaveButton' => 'Save',
                                    'DiscardButton' => 'Discard' ),
    "params" => array( ) );

$ViewList["templatecreate"] = array(
    'functions' => array( 'setup' ),
    "script" => "templatecreate.php",
    'ui_context' => 'edit',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'CreateOverrideButton' => 'CreateOverride',
                                    'CancelOverrideButton' => 'CancelOverride' ),
    "params" => array( ) );


$FunctionList = array();
$FunctionList['administrate'] = array();
$FunctionList['install'] = array();
$FunctionList['managecache'] = array();
$FunctionList['managecronjobs'] = array();
$FunctionList['preload'] = array();
$FunctionList['setup'] = array();
$FunctionList['system_info'] = array();

?>
