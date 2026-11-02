<?php
/**
 * The migration module: Setup > Migrations. The migrations of the installation (site, extensions, packages) with their
 * state, the history of the runs, a dry run, a run and a rollback started in the background with their progress shown
 * live, and the log of each run. The work is in kernel/classes/migration (the exp:migration commands run the same
 * code); a run or rollback started here runs as the signed-in user in a console process of its own (expMigrationJob).
 *
 * Policies: migration/read (see), migration/run (run, resume, simulate), migration/rollback (take back),
 * migration/edit (recordings of changes, the generators and the editor of YAML migrations), migration/remotes (the remotes
 * deploys go to) and migration/deploy (deploys to remotes, their gates and rollbacks). Only Administrator has them in a new
 * installation. Runs and
 * rollbacks ask "Verify it's you" (site.ini [ReauthGroup_system]).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Migrations',
                 'variable_params' => false );

$ViewList = array();

$ViewList['status'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'status.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array() );

$ViewList['history'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'history.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'state' => 'State' ) );

$ViewList['log'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'log.php',
    'functions' => array( 'read' ),
    'params' => array( 'RunID' ),
    'unordered_params' => array() );

$ViewList['stream'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'stream.php',
    'functions' => array( 'read' ),
    'params' => array( 'RunID' ),
    'unordered_params' => array( 'offset' => 'Offset', 'log' => 'LogOffset' ) );

$ViewList['dryrun'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'dryrun.php',
    'functions' => array( 'read' ),
    'single_post_actions' => array( 'SimulateButton' => 'Simulate' ),
    'params' => array(),
    'unordered_params' => array() );

$ViewList['run'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'run.php',
    'functions' => array( 'run' ),
    'single_post_actions' => array( 'RunButton' => 'Run', 'ResumeButton' => 'Resume' ),
    'params' => array(),
    'unordered_params' => array() );

$ViewList['rollback'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'rollback.php',
    'functions' => array( 'rollback' ),
    'single_post_actions' => array( 'RollbackButton' => 'Rollback' ),
    'params' => array( 'Target' ),
    'unordered_params' => array() );

$ViewList['recording'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'recording.php',
    'functions' => array( 'edit' ),
    'single_post_actions' => array( 'StartButton' => 'Start', 'StartSiteButton' => 'StartSite', 'StopButton' => 'Stop', 'DiscardButton' => 'Discard' ),
    'params' => array( 'RecordingID' ),
    'unordered_params' => array() );

// Remotes: the other installations this one deploys its migrations to (migration.ini [Remote_<id>]); Test connection
// reads one remote's status
$ViewList['remotes'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'remotes.php',
    'functions' => array( 'read' ),
    'single_post_actions' => array( 'TestButton' => 'Test' ),
    'params' => array(),
    'unordered_params' => array() );

// The wizard of one remote (new or <id>): saved through expIniEditor to settings/override/migration.ini.append.php
$ViewList['remote'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'remote.php',
    'functions' => array( 'remotes' ),
    'single_post_actions' => array( 'ReviewButton' => 'Review', 'SaveButton' => 'Save', 'EditButton' => 'Edit', 'TestButton' => 'Test', 'RemoveButton' => 'Remove' ),
    'params' => array( 'RemoteID' ),
    'unordered_params' => array() );

// Deploys: start one (a preflight, a deploy, staged or not, transfer only) and the history from the reports
$ViewList['deploy'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'deploy.php',
    'functions' => array( 'read' ),
    'single_post_actions' => array( 'PreflightButton' => 'Preflight', 'DeployButton' => 'Deploy' ),
    'params' => array(),
    'unordered_params' => array() );

// One deploy: live per-remote progress (migration/stream of its job), its report, the gate of a staged deploy,
// the rollback
$ViewList['deployment'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'deployment.php',
    'functions' => array( 'read' ),
    'single_post_actions' => array( 'ApproveButton' => 'Approve', 'RollbackButton' => 'Rollback' ),
    'params' => array( 'DeployID' ),
    'unordered_params' => array() );

// The editor of a YAML migration
$ViewList['editor'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'editor.php',
    'functions' => array( 'edit' ),
    'single_post_actions' => array( 'ChooseTypeButton' => 'ChooseType', 'AddStepButton' => 'AddStep', 'EditStepButton' => 'EditStep',
                                    'RemoveStepButton' => 'RemoveStep', 'MoveUpButton' => 'MoveUp', 'MoveDownButton' => 'MoveDown',
                                    'CheckButton' => 'Check', 'SaveButton' => 'Save' ),
    'params' => array( 'MigrationID' ),
    'unordered_params' => array() );

// The signed HTTP trigger a deploy over FTP runs the migrations through (expMigrationTrigger, migration.ini
// [TriggerSettings]): no session, no policy (site.ini AnonymousAccessList[], PolicyOmitList[]); the signature, the
// source address and the nonce are the permission
$ViewList['trigger'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'trigger.php',
    'params' => array(),
    'unordered_params' => array() );

$FunctionList = array();
// See the migrations, their state, the runs and their logs, and the dry run
$FunctionList['read'] = array();
// Run the pending migrations, resume a failed run, simulate a run
$FunctionList['run'] = array();
// Take back a run, one migration or (expert) restore an earlier caption
$FunctionList['rollback'] = array();
// Record changes as migrations (recordings of changes; the site-wide window asks "Verify it's you"), and write
// and generate migrations in the administration (the editor and the generators)
$FunctionList['edit'] = array();
// Add, change and remove the remotes migrations are deployed to, and test their connection
$FunctionList['remotes'] = array();
// Deploy migrations to remotes, approve the next stage of a staged deploy, and roll a deploy back
$FunctionList['deploy'] = array();

?>
