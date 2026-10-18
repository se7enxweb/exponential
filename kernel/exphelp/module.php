<?php
/**
 * The exphelp module: the help system of the administration (doc/features/6.0/help-system.md).
 *
 * It reads the git-tracked text and Markdown files under doc/ (and, opt-in, extension doc directories named in
 * exphelp.ini [HelpSettings] ExtensionDocDirs[]) and shows them rendered, searchable and filtered by audience.
 *
 *   exphelp/dashboard              the help start page: search, audiences, featured pages, recent changes, trackers
 *   exphelp/viewer/<page id>       one page; <page id> is its path below doc/ (features/6.0/help-system.md); ?p= too
 *   exphelp/contents               every help page as one tree (all folders open), with a filter box
 *   exphelp/search?q=...           the results page; with format=json the JSON the client's instant search reads
 *   exphelp/asset/<path>           an image below doc/, with its content type (only git-tracked files)
 *   exphelp/report                 how to report an error: which tracker, a prefilled issue, safe diagnostics
 *
 * Policy function read; Anonymous is not given it. An unknown or refused path is a 404.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Help',
                 'variable_params' => false );

$ViewList = array();

$ViewList['dashboard'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'dashboard.php',
    'functions' => array( 'read' ),
    'params' => array() );

// The page is the rest of the address (exphelp/viewer/features/6.0/help-system.md) or GET p
$ViewList['viewer'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'viewer.php',
    'functions' => array( 'read' ),
    'params' => array() );

// Every help page as one tree, all folders open, with a filter box
$ViewList['contents'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'contents.php',
    'functions' => array( 'read' ),
    'params' => array() );

// GET q, audience, category, type, offset, limit, format (html|json)
$ViewList['search'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'search.php',
    'functions' => array( 'read' ),
    'params' => array() );

// The file is the rest of the address (exphelp/asset/images/foo.png) or GET p
$ViewList['asset'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'asset.php',
    'functions' => array( 'read' ),
    'params' => array() );

// GET tracker (software|project|community)
$ViewList['report'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'report.php',
    'functions' => array( 'read' ),
    'params' => array() );

$FunctionList = array();
// Reading the help: the dashboard, the pages, the search, the images and the error report guide
$FunctionList['read'] = array();

?>
