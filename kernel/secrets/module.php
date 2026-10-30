<?php
/**
 * The secrets module (doc/eng-us/features/6.0/secret-store.md): the kernel's secret store, Setup > Secrets. The names
 * of the secrets every tool uses (data table connections, import sources, remote servers), where the key is, setting
 * a value (write-only: a value is never shown), removing one, and rotating the key.
 *
 * Policy: secrets/manage. Only Administrator has it in a new installation.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Secrets',
                 'variable_params' => false );

$ViewList = array();

$ViewList['list'] = array(
    'default_navigation_part' => 'ezsetupnavigationpart',
    'script' => 'list.php',
    'functions' => array( 'manage' ),
    'params' => array(),
    'unordered_params' => array( 'scope' => 'Scope' ) );

$FunctionList = array();
// See the names of the secrets, set and remove values, rotate the key
$FunctionList['manage'] = array();

?>
