<?php
/**
 * The staticpublish module: Setup > Static publishing. The targets of static publishing (a front siteaccess published
 * as static files to a git repository, GitHub Pages or any git host) with their state, the wizard that makes and
 * changes them, a build started in the background with its progress shown live, the preview of a build (the diff, the
 * report, the limits, a local preview) with Publish and Discard, the history of the publishes with the rollback, the
 * page of each job or run, and the deploy key. The work is in kernel/classes/staticpublish (the exp:static commands
 * run the same code); a build, publish or rollback started here runs as the signed-in user in a console process of
 * its own (expStaticJob).
 *
 * Policies, each with the limitation Target: staticpublish/read (see), build (build, throw a build away), publish
 * (publish, roll back), setup (make and change targets, the deploy key, switch the feature on or off). Only
 * Administrator has them in a new installation. Publishing, rolling back, saving a target, the deploy key and the
 * switch ask "Verify it's you" (site.ini [ReauthGroup_system]).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Static publishing',
                 'variable_params' => false );

$ViewList = array();

$ViewList['dashboard'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'dashboard.php',
    'functions' => array( 'read' ),
    'single_post_actions' => array( 'SwitchButton' => 'Switch' ),
    'params' => array(),
    'unordered_params' => array() );

$ViewList['target'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'target.php',
    'functions' => array( 'setup' ),
    'single_post_actions' => array( 'ReviewButton' => 'Review', 'SaveButton' => 'Save', 'EditButton' => 'Edit' ),
    'params' => array( 'TargetID' ),
    'unordered_params' => array() );

$ViewList['build'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'build.php',
    'functions' => array( 'build' ),
    'single_post_actions' => array( 'BuildButton' => 'Build' ),
    'params' => array( 'TargetID' ),
    'unordered_params' => array() );

$ViewList['stream'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'stream.php',
    'functions' => array( 'read' ),
    'params' => array( 'JobID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

$ViewList['preview'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'preview.php',
    'functions' => array( 'read' ),
    'single_post_actions' => array( 'PublishButton' => 'Publish', 'DiscardButton' => 'Discard' ),
    'params' => array( 'TargetID' ),
    'unordered_params' => array() );

// The build in a target's folder, served for the local preview: /staticpublish/serve/<target>/<expires>/<signature>/
// <path of the build>. The signature (expStaticAdmin::previewSignature()) binds the link to its user and an hour.
$ViewList['serve'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'serve.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array() );

$ViewList['history'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'history.php',
    'functions' => array( 'read' ),
    'single_post_actions' => array( 'RollbackButton' => 'Rollback' ),
    'params' => array( 'TargetID' ),
    'unordered_params' => array() );

$ViewList['log'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'log.php',
    'functions' => array( 'read' ),
    'params' => array( 'RunID' ),
    'unordered_params' => array() );

$ViewList['keys'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'keys.php',
    'functions' => array( 'setup' ),
    'single_post_actions' => array( 'GenerateButton' => 'Generate' ),
    'params' => array( 'TargetID' ),
    'unordered_params' => array() );

// The form endpoint (expStaticFormEndpoint): the information collectors of a static copy post here, from the
// target's public address, with a token signed by the build. Anyone may post (site.ini PolicyOmitList[]); the view
// checks the origin, the token and the rate itself and hands the post to content/action.
$ViewList['form'] = array(
    'script' => 'form.php',
    'params' => array(),
    'unordered_params' => array() );

$Target = array(
    'name' => 'Target',
    'values' => array(),
    'class' => 'expStaticAdmin',
    'function' => 'limitationList',
    'parameter' => array( false ) );

$FunctionList = array();
// See the targets, their builds, previews, history and logs
$FunctionList['read'] = array( 'Target' => $Target );
// Build a target in the background, throw a build away
$FunctionList['build'] = array( 'Target' => $Target );
// Publish a build, roll back to an earlier publish
$FunctionList['publish'] = array( 'Target' => $Target );
// Make and change targets, their deploy key, switch static publishing on or off
$FunctionList['setup'] = array( 'Target' => $Target );

?>
