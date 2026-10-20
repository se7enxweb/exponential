#!/usr/bin/env php
<?php
/**
 * File containing the exphelpcontent.php script.
 *
 * Help maintained as content on the command line (doc/eng-us/features/6.0/help-content.md):
 *
 *   ./console exp:help:content install -s <admin siteaccess> [--dry-run]   classes, tree, roles (idempotent)
 *   ./console exp:help:content check -s <admin siteaccess> [--fix]         the validator of the style guide
 *   ./console exp:help:content seed-topics -s <admin siteaccess>           initial topics and manuals
 *
 * @alias exp:help:content
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exphelpcontent.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Exphelpcontent' ) )
{
    fwrite( STDERR, "The help content command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Exphelpcontent::main( __FILE__ );
