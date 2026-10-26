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
error_reporting( E_ALL ); // E_STRICT is part of E_ALL since PHP 5.4 and deprecated in 8.4
// Maintenance mode (var/maintenance.json): answered here, before the settings,
// the database and every cache, so a site being installed or taken offline never
// serves a half-built page. Asked for every request: a persistent worker
// (Velocity, FrankenPHP) remembers what it learnt about files, so a plain
// is_file() here kept serving the site to the workers that had looked before
// the installation began. See kernel/classes/expmaintenance.php.
if ( !class_exists( 'expMaintenance', false ) )
    require_once __DIR__ . '/kernel/classes/expmaintenance.php';
if ( expMaintenance::check( __DIR__ ) )
    return;

require 'autoload.php';

$kernel = new ezpKernel( new ezpKernelTreeMenu() );
echo $kernel->run()->getContent();
