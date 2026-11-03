#!/usr/bin/env php
<?php
/**
 * File containing the expviewcountupdate.php script.
 *
 * Counts the page views of the web server's access logs into the view counter (ezview_counter), for one siteaccess
 * or several: the cronjob updateviewcount.php with a dry run, a JSON report and the upkeep of its resume state
 * (doc/eng-us/guides/view-count-statistics.md).
 *
 * @alias exp:viewcount:update
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expviewcountupdate.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Command\\Kernel\\Expviewcountupdate' ) )
{
    fwrite( STDERR, "The view count command class is not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k\n" );
    exit( 2 );
}
\Exponential\Command\Kernel\Expviewcountupdate::main( __FILE__ );
