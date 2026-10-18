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
 *   exphelp/search/(q)/<words>/(category)/<id>/...  search, or browse without (q); format=json: the client's JSON
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

// View parameters (q)/(category)/(audience)/(type)/(offset)/(limit)/(format), canonical order: expHelp::searchUrl().
// Without (q) it browses the pages the filters allow. A GET ?q=... is redirected to the canonical address; JSON
// (format=json or Accept: application/json) takes both forms and is never redirected.
$ViewList['search'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'search.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'q' => 'Query',
                                 'category' => 'Category',
                                 'audience' => 'Audience',
                                 'type' => 'Type',
                                 'offset' => 'Offset',
                                 'limit' => 'Limit',
                                 'format' => 'Format' ) );

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

// Help maintained as content (doc/features/6.0/help-content.md): a topic by its help_id, rendered
// (exphelp/topic/content.publish-article); what the user may see is decided by content/read and the state
$ViewList['topic'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'topic.php',
    'functions' => array( 'read' ),
    'params' => array() );

// A manual of help maintained as content by its remote_id: its table of contents (exphelp/manual/help-manual-editors)
$ViewList['manual'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'manual.php',
    'functions' => array( 'read' ),
    'params' => array() );

// Context help as JSON for other clients: exphelp/context/<help_id> and exphelp/context/manual/<remote_id>, with
// an ETag. Its own function, so a site can give it to readers without giving them the help of the administration
$ViewList['context'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'context.php',
    'functions' => array( 'context' ),
    'params' => array() );

$FunctionList = array();
// Reading the help: the dashboard, the pages, the search, the images and the error report guide
$FunctionList['read'] = array();
// Context help as JSON (exphelp/context): only topics the user may read (content/read) and that are Released
$FunctionList['context'] = array();

?>
