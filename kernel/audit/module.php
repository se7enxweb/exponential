<?php
/**
 * The audit module (doc/bc/6.0/audit.md, "The console: module audit"): the console (timeline, filters, search,
 * paging), one event in full, charts, alerts, export, archives and the effective settings; audit/recent is the
 * short list of stage 2.
 *
 * Policies: audit/read (limitation Channel: content, access, system, commerce, read) and audit/manage. No role but
 * Administrator (which holds every policy) has them in a new installation. Every view records system.audit.read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Audit',
                 'variable_params' => false );

// the console's filters, as ordered parameters /(name)/value (console, charts, export)
$auditFilterParams = array( 'channel' => 'Channel', 'name' => 'Name', 'user' => 'User', 'login' => 'Login',
                            'object' => 'Object', 'target' => 'Target', 'result' => 'Result', 'severity' => 'Severity',
                            'request' => 'Request', 'job' => 'Job', 'run' => 'Run', 'ip' => 'IP', 'from' => 'From',
                            'to' => 'To', 'q' => 'Q', 'legacy_file' => 'LegacyFile', 'parent' => 'Parent',
                            'offset' => 'Offset', 'limit' => 'Limit' );

$ViewList = array();

$ViewList['dashboard'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'dashboard.php',
    'functions' => array( 'read' ),
    'params' => array() );

$ViewList['console'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'console.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => $auditFilterParams );

$ViewList['event'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'event.php',
    'functions' => array( 'read' ),
    'params' => array( 'EventID' ),
    'unordered_params' => array( 'format' => 'Format' ) );

$ViewList['charts'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'charts.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array_merge( $auditFilterParams, array( 'days' => 'Days' ) ) );

$ViewList['alerts'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'alerts.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'offset' => 'Offset', 'state' => 'State' ) );

$ViewList['export'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'export.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array_merge( $auditFilterParams, array( 'format' => 'Format' ) ) );

$ViewList['archives'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'archives.php',
    'functions' => array( 'manage' ),
    'params' => array(),
    'unordered_params' => array( 'channel' => 'Channel' ) );

$ViewList['settings'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'settings.php',
    'functions' => array( 'manage' ),
    'params' => array() );

$ViewList['recent'] = array(
    'default_navigation_part' => 'expauditnavigationpart',
    'script' => 'recent.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'channel' => 'Channel' ) );

$FunctionList = array();
// View, search and export the audit records; the limitation narrows them to some channels
$FunctionList['read'] = array(
    'Channel' => array(
        'name' => 'Channel',
        'values' => array(
            array( 'Name' => 'Content', 'value' => 'content' ),
            array( 'Name' => 'Access', 'value' => 'access' ),
            array( 'Name' => 'System', 'value' => 'system' ),
            array( 'Name' => 'Commerce', 'value' => 'commerce' ),
            array( 'Name' => 'Read', 'value' => 'read' ),
        ),
    ),
);
// Archives, settings, alerts (acknowledge), reindex and retention
$FunctionList['manage'] = array();

?>
