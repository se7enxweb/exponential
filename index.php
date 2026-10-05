<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// Set a default time zone if none is given to avoid "It is not safe to rely
// on the system's timezone settings" warnings. The time zone can be overriden
// in config.php or php.ini.
if ( !ini_get( "date.timezone" ) )
{
    date_default_timezone_set( "UTC" );
}

ignore_user_abort( true );
error_reporting ( E_ALL );

// Maintenance mode (var/maintenance.json): answered here, before the settings
// and the database, so a site being installed or taken offline never serves
// a half-built page. See kernel/classes/expmaintenance.php.
if ( is_file( __DIR__ . '/var/maintenance.json' ) )
{
    require_once __DIR__ . '/kernel/classes/expmaintenance.php';
    if ( expMaintenance::check( __DIR__ ) )
        return;
}

// The repair of the missing-libraries page: its start (only while the libraries
// are missing, before the kernel fails and prints its error ahead of the answer),
// and its status and "update the lock file" run (only with that run's token), so
// the page keeps showing the steps and the log after the libraries are back.
// Anything else goes on to the kernel.
if ( isset( $_REQUEST['exp_repair'] ) )
{
    require_once __DIR__ . '/lib/ezutils/classes/ezprepairqueue.php';
    if ( ezpRepairQueue::handleRunRequest() )
        return;
}

require 'autoload.php';

// On a development or test machine (EXP_ENV dev or test in config.env.php) PHP
// errors are shown in the browser. Any other environment, or none, keeps the
// display_errors setting of php.ini.
if ( in_array( eZINI::environment(), array( 'dev', 'test' ), true ) )
{
    ini_set( 'display_errors', '1' );
}

$kernel = new ezpKernel( new ezpKernelWeb() );
echo $kernel->run()->getContent();
