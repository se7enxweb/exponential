#!/usr/bin/env php
<?php
/**
 * File containing the exphelp.php script.
 *
 * The help system on the command line (doc/features/6.0/help-system.md):
 *
 *   ./console exp:help index [--rebuild] [--list]     build the index of doc/ and describe it
 *   ./console exp:help search "clear cache"           search the help pages
 *   ./console exp:help show guides/caches.md          the metadata and headings of one page
 *   ./console exp:help check [--below=guides/]        check the links and images of the pages
 *
 * @alias exp:help
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exphelp.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Exphelp' ) )
{
    fwrite( STDERR, "The help command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Exphelp::main( __FILE__ );
