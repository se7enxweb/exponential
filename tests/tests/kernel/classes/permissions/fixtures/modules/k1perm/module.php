<?php
/**
 * A module definition for the permission tests (eZUserAccessEvaluationTest): one view per kind of policy function
 * expression a module.php can give.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

$Module = array( 'name' => 'k1perm' );

$ViewList = array();
$ViewList['read'] = array( 'script' => 'read.php', 'functions' => array( 'read' ) );
$ViewList['both'] = array( 'script' => 'both.php', 'functions' => array( 'read', 'edit' ) );
$ViewList['either'] = array( 'script' => 'either.php', 'functions' => array( 'edit or create' ) );
$ViewList['symbols'] = array( 'script' => 'symbols.php', 'functions' => array( '(read && edit) || create' ) );
$ViewList['string'] = array( 'script' => 'string.php', 'functions' => 'read or edit' );
$ViewList['open'] = array( 'script' => 'open.php' );
$ViewList['emptyentry'] = array( 'script' => 'emptyentry.php', 'functions' => array( 'read', '' ) );
$ViewList['unknown'] = array( 'script' => 'unknown.php', 'functions' => array( 'read or nosuchfunction' ) );
$ViewList['code'] = array( 'script' => 'code.php', 'functions' => array( 'read or phpinfo()' ) );
$ViewList['prefix'] = array( 'script' => 'prefix.php', 'functions' => array( 'readall' ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['readall'] = array();
$FunctionList['edit'] = array();
$FunctionList['create'] = array();
