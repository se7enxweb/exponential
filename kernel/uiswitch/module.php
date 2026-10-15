<?php
/**
 * The uiswitch module (doc/features/6.0/ui-switch.md): the administration interface's switchable preferences, the
 * display language first. uiswitch/set (POST) changes one, uiswitch/state (GET) answers with JSON what the user may
 * choose.
 *
 * Policies: one function per provider of uiswitch.ini [UISwitchSettings] ProviderList[] (uiswitch/language, and
 * what an extension adds), so a provider gets its policy without a change here. Administrator holds them through
 * its policy on every module; new installations give uiswitch/language to Editor.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'UI switch',
                 'variable_params' => false );

// class_exists: a Velocity worker started before the class was added still answers
$uiSwitchFunctions = class_exists( 'expUISwitch' ) ? expUISwitch::policyFunctions() : array( 'language' );

$ViewList = array();

$ViewList['set'] = array(
    'script' => 'set.php',
    'default_navigation_part' => 'ezmynavigationpart',
    // any of the providers' functions; set.php checks the one provider again
    'functions' => implode( ' or ', $uiSwitchFunctions ),
    'params' => array() );

$ViewList['state'] = array(
    'script' => 'state.php',
    'default_navigation_part' => 'ezmynavigationpart',
    'functions' => implode( ' or ', $uiSwitchFunctions ),
    'params' => array() );

$FunctionList = array();
foreach ( $uiSwitchFunctions as $uiSwitchFunction )
    $FunctionList[$uiSwitchFunction] = array();

unset( $uiSwitchFunctions, $uiSwitchFunction );
