#!/usr/bin/env php
<?php
/**
 * File containing the exphelpexamples.php script.
 *
 * The generated reference examples below doc/examples/ (doc/examples/README.md, "How the generated pages are made"):
 *
 *   ./console exp:help:examples              write the pages (only changed files are written)
 *   ./console exp:help:examples --check      exit 1 when the pages on disk are not what the sources give
 *
 * @alias exp:help:examples
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exphelpexamples.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Exphelpexamples' ) )
{
    fwrite( STDERR, "The help examples command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Exphelpexamples::main( __FILE__ );
