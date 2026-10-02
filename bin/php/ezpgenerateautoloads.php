#!/usr/bin/env php
<?php
/**
 * File containing the ezpgenerateautoloads.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

if ( file_exists( "config.php" ) )
{
    require_once "config.php";
}

// Setup, includes
//{

$platformVendorDir = getcwd() . "/../vendor";
$legacyVendorDir = getcwd() . "/vendor";
if ( class_exists( 'Composer\Autoload\ClassLoader', false ) )
{
    // Do nothing, composer autoload already loaded
}
// Composer if in eZ Platform context
else if ( file_exists( "{$platformVendorDir}/autoload.php" ) )
{
    require_once "{$platformVendorDir}/autoload.php";
}
// Composer if in Exponential legacy context
else if ( file_exists( "{$legacyVendorDir}/autoload.php" ) )
{
    require_once "{$legacyVendorDir}/autoload.php";
}

// The code is in kernel/private/classes/commands/ezpgenerateautoloads.php (#207); this file is the entry point.
require_once __DIR__ . '/../../kernel/private/classes/runnable/runnable.php';
require_once __DIR__ . '/../../kernel/private/classes/runnable/command.php';
require_once __DIR__ . '/../../kernel/private/classes/commands/ezpgenerateautoloads.php';
\Exponential\Command\Kernel\Ezpgenerateautoloads::main( __FILE__ );
