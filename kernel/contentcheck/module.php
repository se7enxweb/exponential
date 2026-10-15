<?php
/**
 * The content checks (doc/features/6.0/content-checks.md): the objects below a node that nothing refers to, with
 * a move to the trash for those the user may remove, and the visible content that links to or embeds something
 * deleted, in the trash, hidden or never published.
 *
 * Policies: contentcheck/unreferenced opens the list of unreferenced objects; contentcheck/trash is needed as
 * well to move them to the trash, and then content/remove on each object decides. contentcheck/brokenrelations
 * opens the read-only list of broken relations. Every list shows only what content/read lets the user see. No role
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

// The objects below a node that nothing refers to (GET: ClassID[], Sort, Order, Containers; (offset) in the address)
$ViewList['unreferenced'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'unreferenced.php',
    'functions' => array( 'unreferenced' ),
    'params' => array( 'NodeID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

// Moving unreferenced objects to the trash: POST only; first the confirmation page, then the move
$ViewList['trash'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'trash.php',
    'functions' => array( 'unreferenced', 'trash' ),
    'params' => array() );

// Visible content that refers to deleted, trashed, hidden or unpublished content (GET: Exclude, Kind[],
// Relation[], After)
$ViewList['brokenrelations'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'brokenrelations.php',
    'functions' => array( 'brokenrelations' ),
    'params' => array( 'NodeID' ) );

$FunctionList = array();
// The list of unreferenced objects; it shows only what content/read lets the user see
$FunctionList['unreferenced'] = array();
// Moving unreferenced objects to the trash; each object also needs content/remove
$FunctionList['trash'] = array();
// The list of broken relations (read only); it shows only what content/read lets the user see
$FunctionList['brokenrelations'] = array();

?>
