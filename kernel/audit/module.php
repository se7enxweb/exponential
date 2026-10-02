<?php
/**
 * The audit module (doc/bc/6.0/audit.md, "The console: module audit"). Stage 2 has one read-only view,
 * audit/recent: the latest events of every channel and the state of each channel's hash chain. The console,
 * event, charts, alerts, export, archives and settings views follow in stage 4.
 *
 * Policies: audit/read (no role but Administrator, which holds every policy, has it in a new installation).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Audit',
                 'variable_params' => false );

$ViewList = array();

$ViewList['recent'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'recent.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'channel' => 'Channel' ) );

$FunctionList = array();
// View the audit records (stage 4 adds the Channel limitation)
$FunctionList['read'] = array();

?>
