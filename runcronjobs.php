#!/usr/bin/env php
<?php
/**
 * Entry point of ./runcronjobs.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/* No more than one instance of a cronjob script can be run at any given time.
   If a script uses more time than the configured MaxScriptExecutionTime (see
   cronjob.ini), the next instance of it will try to gracefully steal the
   cronjob script mutex. If the process has been running for more than two
   times MaxScriptExecutionTime, the original process will be killed.
*/

/* Set a default time zone if none is given. The time zone can be overridden
   in config.php or php.ini.
*/
if ( !ini_get( "date.timezone" ) )
{
    date_default_timezone_set( "UTC" );
}

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/runcronjobs.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Runcronjobs::main( __FILE__ );
