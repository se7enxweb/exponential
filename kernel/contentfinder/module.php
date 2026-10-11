<?php
/**
 * The content finder (doc/features/6.0/content-finder.md): find content by an object, node, attribute or version
 * id, a remote id, a URL alias or the path of a file, see every version, location and trash entry of what was
 * found, and act on it: move to the trash, delete permanently, purge from the trash, remove a version.
 *
 * Policies: contentfinder/search opens the finder; contentfinder/delete is needed as well for its actions. What
 * each result shows and allows is then decided by the content policies of the object (content/read,
 * content/versionread, content/remove, content/restore, content/cleantrash, content/versionremove). No role but
 * Administrator (which holds every policy) has them in a new installation. The finder answers on administration
 * siteaccesses only (contentfinder.ini [ContentFinderSettings] SiteAccessList[]).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'ContentFinder',
                 'variable_params' => false );

$ViewList = array();

// The search form and the results (GET: SearchText, SearchType)
$ViewList['search'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'search.php',
    'functions' => array( 'search' ),
    'params' => array() );

// An action on a result: POST only; first the confirmation page, then the action
$ViewList['action'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'action.php',
    'functions' => array( 'search', 'delete' ),
    'params' => array() );

$FunctionList = array();
// Open the finder and search; each result shows only what the content policies let the user see
$FunctionList['search'] = array();
// The finder's actions (trash, delete permanently, purge, remove a version); each also needs its content policies
$FunctionList['delete'] = array();

?>
