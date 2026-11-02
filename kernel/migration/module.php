<?php
/**
 * The migration module: Setup > Migrations. The migrations of the installation (site, extensions, packages) with their
 * state, the history of the runs, a dry run, a run and a rollback started in the background with their progress shown
 * live, and the log of each run. The work is in kernel/classes/migration (the exp:migration commands run the same
 * code); a run or rollback started here runs as the signed-in user in a console process of its own (expMigrationJob).
 *
 * Policies: migration/read (see), migration/run (run, resume, simulate), migration/rollback (take back),
 * migration/edit (recordings of changes, the generators and the editor of YAML migrations). Only Administrator has them in a new installation. Runs and
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

$ViewList['editor'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'editor.php',
    'functions' => array( 'edit' ),
    'single_post_actions' => array( 'ChooseTypeButton' => 'ChooseType', 'AddStepButton' => 'AddStep', 'EditStepButton' => 'EditStep',
                                    'RemoveStepButton' => 'RemoveStep', 'MoveUpButton' => 'MoveUp', 'MoveDownButton' => 'MoveDown',
                                    'CheckButton' => 'Check', 'SaveButton' => 'Save' ),
    'params' => array( 'MigrationID' ),
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

?>
