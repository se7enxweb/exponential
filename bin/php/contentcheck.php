#!/usr/bin/env php
<?php
/**
 * File containing the contentcheck.php script.
 *
 * The content checks on the command line, report only (doc/features/6.0/content-checks.md):
 *
 *   ./console exp:content-check --build-index            build the reference index
 *   ./console exp:content-check --unreferenced --node=2   the unused assets below node 2
 *   ./console exp:content-check --broken --node=2         the broken relations below node 2 and in the layouts
 *   ./console exp:content-check --why=123                 what uses object 123
 *
 * @alias exp:content-check
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/contentcheck.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Contentcheck' ) )
{
    fwrite( STDERR, "The content check command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Contentcheck::main( __FILE__ );
