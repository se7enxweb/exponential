<?php
/**
 * The exphelp module: the help system of the administration (doc/eng-us/features/6.0/help-system.md).
 *
 * It reads the git-tracked text and Markdown files under doc/ and the documentation of the active extensions
 * (exphelp.ini [IndexSettings] ActiveExtensionDocs, ExtensionDocDirs[]) and shows them rendered, searchable and
 * filtered by audience.
 *
 *   exphelp/dashboard              the help start page: search, audiences, featured pages, recent changes, trackers
 *   exphelp/audience/<audience>    the landing page of an audience (expHelpLanding)
 *   exphelp/viewer/<page id>       one page; <page id> is its path below doc/ (features/6.0/help-system.md);
 *                                  (lang)/<key> shows it in another language (the English original)
 *   exphelp/contents               every help page as one tree (all folders open), with a filter box
 *   exphelp/extensions[/<name>]    the documentation of the active extensions: the list, or one extension's pages
 *   exphelp/search/(q)/<words>/(category)/<id>/...  search, or browse without (q); (format)/json: the client's JSON
 *   exphelp/asset/<path>           an image below doc/, with its content type (only git-tracked files)
 *   exphelp/image/(id)/<sha256>    the local copy of an external image a page shows, stored by exp:help index
 *   exphelp/pdf/<page id>          the page as a PDF file to download (expHelpPdf)
 *   exphelp/report                 how to report an error: which tracker, a prefilled issue, safe diagnostics
 *
 * Help maintained as content (doc/eng-us/features/6.0/help-content.md):
 *
 *   exphelp/topic/<help_id>        a topic; exphelp/manual/<remote_id> a manual; exphelp/context/... their JSON
 *   exphelp/state/<help_id>        POST: a step of the review workflow (Draft, Review, Released)
 *   exphelp/feedback/<help_id>     POST: an answer to "Was this helpful?"
 *
 * Every option is a view parameter (/(name)/value), never a GET query parameter.
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

// The landing page of an audience (exphelp/audience/developer): blocks from exphelp.ini [LandingSettings]; (offset)
// pages the developers' commit log, (merges)/1 shows merge commits too
$ViewList['audience'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'audience.php',
    'functions' => array( 'read' ),
    'params' => array( 'Audience' ),
    'unordered_params' => array( 'offset' => 'Offset',
                                 'merges' => 'Merges' ) );

// The page is the rest of the address (exphelp/viewer/features/6.0/help-system.md); (lang)/<key> shows it in another
// language for this request (exphelp/viewer/features/6.0/help-system.md/(lang)/eng-us)
$ViewList['viewer'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'viewer.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'lang' => 'Language' ) );

// Every help page as one tree, all folders open, with a filter box
$ViewList['contents'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'contents.php',
    'functions' => array( 'read' ),
    'params' => array() );

// View parameters (q)/(category)/(audience)/(type)/(extension)/(offset)/(limit)/(format), canonical order:
// expHelp::searchUrl(). (extension)/<name> limits it to the documentation of one extension.
// Without (q) it browses the pages the filters allow. The search form without scripts is a POST, redirected to the
// canonical address; JSON ((format)/json or Accept: application/json) is never redirected. No GET query parameters.
$ViewList['search'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'search.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'q' => 'Query',
                                 'category' => 'Category',
                                 'audience' => 'Audience',
                                 'type' => 'Type',
                                 'extension' => 'Extension',
                                 'offset' => 'Offset',
                                 'limit' => 'Limit',
                                 'format' => 'Format' ) );

// The documentation of the active extensions (ext/<name>/...): the list (exphelp/extensions), and one extension's
// pages with a search limited to them (exphelp/extensions/<name>)
$ViewList['extensions'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'extensions.php',
    'functions' => array( 'read' ),
    'params' => array( 'Extension' ) );

// The file is the rest of the address (exphelp/asset/images/foo.png)
$ViewList['asset'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'asset.php',
    'functions' => array( 'read' ),
    'params' => array() );

// The local copy of an external image a page shows, stored by exp:help index: the view parameter
// (id)/<sha256 of its address>, never GET (expHelpRemoteImages)
$ViewList['image'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'image.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'id' => 'Id' ) );

// The page as a PDF file to download: the rest of the address (exphelp/pdf/help/getting-started.md)
$ViewList['pdf'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'pdf.php',
    'functions' => array( 'read' ),
    'params' => array() );

// (tracker)/<software|velocity|community|extension key>: the tracker chosen (the posted form may choose it too)
$ViewList['report'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'report.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'tracker' => 'Tracker' ) );

// Help maintained as content (doc/eng-us/features/6.0/help-content.md): a topic by its help_id, rendered
// (exphelp/topic/content.publish-article); what the user may see is decided by content/read and the state
$ViewList['topic'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'topic.php',
    'functions' => array( 'read' ),
    'params' => array(),
    // (step)/<state>: the result of a step of the review workflow; (helpful)/thanks: after "Was this helpful?"
    'unordered_params' => array( 'step' => 'Step',
                                 'helpful' => 'Helpful' ) );

// One step of the review workflow (Draft, Review, Released) of a topic or manual: POST HelpState
// (exphelp/state/<help_id>, exphelp/state/manual/<remote_id>); state/assign decides, the author does not release
$ViewList['state'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'state.php',
    'functions' => array( 'read' ),
    'params' => array() );

// "Was this helpful?" of a Released topic: POST Helpful, Reason, RedirectURI (exphelp/feedback/<help_id>); for the
// readers of the help in the administration (read) and of a site's public help (feedback)
$ViewList['feedback'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'feedback.php',
    'functions' => 'read or feedback',
    'params' => array() );

// A manual of help maintained as content by its remote_id: its table of contents (exphelp/manual/help-manual-editors);
// (format)/<epub|pdf|dita> downloads it (exphelp/manual/<remote_id>/(format)/pdf, every manual: exphelp/manual/(format)/dita)
$ViewList['manual'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'manual.php',
    'functions' => array( 'read' ),
    'params' => array(),
    'unordered_params' => array( 'format' => 'Format' ) );

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
// "Was this helpful?" (exphelp/feedback) without the help of the administration: for the readers of a public help
$FunctionList['feedback'] = array();

?>
