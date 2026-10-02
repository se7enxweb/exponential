<?php
/**
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

// The status of a repair started from the missing-libraries page (and its
// follow-up "update the lock file" run): answered here, before the kernel, so
// the page keeps showing the steps and the log after the libraries are back.
// Only requests carrying that run's token are answered; anything else goes on.
if ( isset( $_REQUEST['exp_repair'] ) && is_file( __DIR__ . '/var/repair/status.json' ) )
{
    require_once __DIR__ . '/lib/ezutils/classes/ezprepairqueue.php';
    if ( ezpRepairQueue::handleRunRequest() )
        return;
}

require 'autoload.php';

$kernel = new ezpKernel( new ezpKernelWeb() );
echo $kernel->run()->getContent();
