#!/usr/bin/env php
<?php
/**
 * File containing the explogs.php script.
 *
 * The log viewer on the command line, read only (doc/en/us/features/6.0/log-viewer.md):
 *
 *   ./console exp:logs --list                                the log files, by group, with size, time and line rate
 *   ./console exp:logs --tail=var/log/error.log --follow     follow a file (Ctrl-C ends)
 *   ./console exp:logs --search=timeout --rotations          search a file and its rotations
 *   ./console exp:logs --reference=ERR-0123456789            where an error page's reference is, and its request
 *
 * @alias exp:logs
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/explogs.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Explogs' ) )
{
    fwrite( STDERR, "The log viewer command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Explogs::main( __FILE__ );
