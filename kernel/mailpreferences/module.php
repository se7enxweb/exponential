<?php
/**
 * The mailpreferences module: every person's e-mail preferences and the admin's tools for them
 * (kernel/classes/mailpreferences).
 *
 * The person's views need no policy: settings and export check the login themselves, manage, unsubscribe and confirm
 * work with a signed link (expMailToken) and request ("send me a link") is open to everyone. They are in
 * site.ini [RoleSettings] PolicyOmitList[]. The admin view needs mailpreferences/administrate; its CSV exports of the
 * consent log and the suppression list need mailpreferences/export too.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'eZMailPreferences',
                 'variable_params' => true );

$ViewList = array();

// The preference page of the logged in user (public site, admin and editor)
$ViewList['settings'] = array(
    'script' => 'settings.php',
    'default_navigation_part' => 'ezmynavigationpart',
    'single_post_actions' => array( 'StoreButton' => 'Store', 'AllOffButton' => 'AllOff', 'AllOnButton' => 'AllOn' ),
    'post_action_parameters' => array( 'Store' => array( 'Category' => 'Category', 'Frequency' => 'Frequency', 'Master' => 'Master' ) ),
    'params' => array() );

// The same page without login, from the "Manage preferences" link of a mail or of "send me a link"
$ViewList['manage'] = array(
    'script' => 'manage.php',
    'default_navigation_part' => 'ezmynavigationpart',
    'params' => array( 'Token' ) );

// One-click unsubscribe: GET shows a page with one button, POST (and the RFC 8058 POST of a mail program,
// body List-Unsubscribe=One-Click, no cookie, no form token) unsubscribes at once
$ViewList['unsubscribe'] = array(
    'script' => 'unsubscribe.php',
    'default_navigation_part' => 'ezmynavigationpart',
    'params' => array( 'Token' ) );

// The link of a double opt-in mail (a newsletter or marketing category, a new e-mail address)
$ViewList['confirm'] = array(
    'script' => 'confirm.php',
    'default_navigation_part' => 'ezmynavigationpart',
    'params' => array( 'Token' ) );

// "Send me a link": an address in, a link to the preference page out (the same answer for every address)
$ViewList['request'] = array(
    'script' => 'request.php',
    'default_navigation_part' => 'ezmynavigationpart',
    'params' => array() );

// "Download my e-mail data": JSON or CSV of the logged in user (or of the person of a manage link)
$ViewList['export'] = array(
    'script' => 'export.php',
    'default_navigation_part' => 'ezmynavigationpart',
    'params' => array( 'Format' ),
    'unordered_params' => array( 'token' => 'Token' ) );

// The admin: admin/categories, admin/user/<id>, admin/suppression, admin/consent, admin/status
$ViewList['admin'] = array(
    'functions' => array( 'administrate' ),
    'script' => 'admin.php',
    'ui_context' => 'administration',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'Page', 'ID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

$FunctionList = array();
$FunctionList['administrate'] = array();
$FunctionList['export'] = array();

?>
