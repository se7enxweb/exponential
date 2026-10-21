<?php
/**
 * The content checks (doc/eng-us/features/6.0/content-checks.md): the objects below a node that nothing refers to, with
 * a move to the trash for those the user may remove, and the visible content that links to or embeds something
 * deleted, in the trash, hidden or never published.
 *
 * Policies: contentcheck/unreferenced opens the list of unreferenced objects; contentcheck/trash is needed as
 * well to move them to the trash, and then content/remove on each object decides. contentcheck/brokenrelations
 * opens the read-only list of broken relations. contentcheck/storage opens the storage files (orphans and file
 * problems) and runs their scan; contentcheck/quarantine moves orphans to the quarantine, restores and deletes them
 * there; contentcheck/permissions sets the expected owner and mode of used files. Every list shows only what content/read lets the user see. No role
 * but Administrator (which holds every policy) has them in a new installation. The checks answer on administration
 * siteaccesses only (contentcheck.ini [ContentCheckSettings] SiteAccessList[]).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'ContentCheck',
                 'variable_params' => false );

$ViewList = array();

// The objects below a node that nothing refers to. The options are view parameters only: (class) (comma list of
// class ids), (sort), (order), (containers)/1, (selectall)/1, (offset). The filter form is posted and answered with a
// 303 to that address.
$ViewList['unreferenced'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'unreferenced.php',
    'functions' => array( 'unreferenced' ),
    'params' => array( 'NodeID' ),
    'unordered_params' => array( 'offset' => 'Offset', 'class' => 'Class', 'sort' => 'Sort', 'order' => 'Order',
                                 'containers' => 'Containers', 'selectall' => 'SelectAll' ) );

// Moving unreferenced objects to the trash: POST only; first the confirmation page, then the move
$ViewList['trash'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'trash.php',
    'functions' => array( 'unreferenced', 'trash' ),
    'params' => array() );

// Visible content that refers to deleted, trashed, hidden or unpublished content. The options are view parameters
// only: (exclude), (kind), (relation) (comma lists, "none" for none ticked), (after), and of the missing files
// (problem), (version), (selectall)/1, (file_offset). The filter form is posted and answered with a 303 to that address.
$ViewList['brokenrelations'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'brokenrelations.php',
    'functions' => array( 'brokenrelations' ),
    'params' => array( 'NodeID' ),
    'unordered_params' => array( 'exclude' => 'Exclude', 'kind' => 'Kind', 'relation' => 'Relation', 'after' => 'After',
                                 'problem' => 'Problem', 'version' => 'Version', 'selectall' => 'SelectAll',
                                 'file_offset' => 'FileOffset' ) );

// Resolving the missing files of the broken relations: POST only; first the preview, then the batches
$ViewList['fixfiles'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'fixfiles.php',
    'functions' => array( 'brokenrelations', 'fixfiles' ),
    'params' => array() );

// The start location of a check, chosen with the node picker: GET contentcheck/scope/<view>/<node id> opens it, its
// POST comes back here and opens contentcheck/<view>/<chosen node id> (expContentCheckScope)
$ViewList['scope'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'scope.php',
    'params' => array( 'View', 'NodeID', 'StartNodeID' ) );

// The storage files: orphans (no content uses them), file-system problems of the used ones below the node, and the
// quarantine. The options are view parameters only: (show), (sort), (order), (kind) (comma list, or "none"),
// (selectall)/1, (offset). The filter forms are posted and answered with a 303 to that address.
$ViewList['storage'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'storage.php',
    'functions' => array( 'storage' ),
    'params' => array( 'NodeID' ),
    'unordered_params' => array( 'offset' => 'Offset', 'show' => 'Show', 'sort' => 'Sort', 'order' => 'Order',
                                 'kind' => 'Kind', 'selectall' => 'SelectAll' ) );

// The scan and the actions on storage files: POST only; every action shows its plan (the dry run) and is confirmed
$ViewList['storageaction'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'storageaction.php',
    'functions' => array( 'storage' ),
    'params' => array() );

$FunctionList = array();
// The list of unreferenced objects; it shows only what content/read lets the user see
$FunctionList['unreferenced'] = array();
// Moving unreferenced objects to the trash; each object also needs content/remove
$FunctionList['trash'] = array();
// The list of broken relations (read only); it shows only what content/read lets the user see
$FunctionList['brokenrelations'] = array();
// With brokenrelations: resolving missing files (regenerate the aliases, restore, re-upload, remove the reference,
// mark for review); each object also needs content/edit (marking for review: content/read)
$FunctionList['fixfiles'] = array();
// The storage files: scanning the storage and the lists of orphan files and file problems
$FunctionList['storage'] = array();
// With storage: moving orphan files to the quarantine, restoring them, and deleting them from the quarantine
$FunctionList['quarantine'] = array();
// With storage: setting the expected owner and mode of used files
$FunctionList['permissions'] = array();

?>
