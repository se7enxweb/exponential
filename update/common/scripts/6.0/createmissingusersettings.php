#!/usr/bin/env php
<?php
/**
 * File containing the createmissingusersettings.php upgrade script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */

/**
 * Creates the ezuser_setting rows that accounts of an older installation lack (expUserSettingRepair). Since 6.0.15
 * an account without its row is not enabled anywhere: its session, API key or OAuth token stops working, as its
 * sign-in already did. Run with --dry-run first: it lists the ids and what each would get. Rows that exist are never
 * changed, so the script can be run again.
 *
 *   --dry-run    list, change nothing
 *   --disabled   create every row disabled (the accounts stay locked out, administrators enable them one by one)
 *
 * Guide: doc/eng-us/specifications/6.0/security-hardening-2026-08.md (F-06)
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Creates the missing ezuser_setting rows of user accounts.\n"
                                                    . "Enabled, as before 6.0.15; disabled with --disabled or for an account waiting for activation.",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[dry-run][disabled]', '', array( 'dry-run' => 'List the accounts, change nothing',
                                                                   'disabled' => 'Create every row disabled' ) );
$script->initialize();

if ( !class_exists( 'expUserSettingRepair' ) )
{
    $cli->error( 'expUserSettingRepair is not in the autoload array: run bin/php/ezpgenerateautoloads.php -k first.' );
    $script->shutdown( 1 );
}

$plan = expUserSettingRepair::plan( expUserSettingRepair::missing(), !$options['disabled'] );
if ( !$plan )
{
    $cli->output( 'Every account has its ezuser_setting row, nothing to do.' );
    $script->shutdown( 0 );
}
$words = array( 1 => 'enabled', 0 => 'disabled' );
foreach ( $plan as $id => $isEnabled )
{
    $cli->output( sprintf( 'user %d: %s', $id, $isEnabled === null ? 'left alone (content object not published)'
                                                                    : $words[$isEnabled] . ' row' ) );
}
if ( $options['dry-run'] )
{
    $cli->output( count( $plan ) . ' account(s) without a row; --dry-run, nothing changed.' );
    $script->shutdown( 0 );
}
$created = expUserSettingRepair::apply( $plan );
$cli->output( count( $created ) . ' row(s) created.' );
$script->shutdown( 0 );
