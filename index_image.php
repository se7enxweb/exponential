<?php
/**
 * The entry script for an image the web server did not find in the storage (var/<site>/storage/images...), and for
 * content/image where the web server routes it here (Velocity, FrankenPHP, .htaccess_root): an
 * image alias that is not made yet is made and served, anything else gets the placeholder with 404
 * (expImageAliasServer::handleStoragePath()). Light: no session, no module, no template. The rewrite rule that sends
 * missing storage images here is in .htaccess_root.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

if ( !ini_get( "date.timezone" ) )
{
    date_default_timezone_set( "UTC" );
}

ignore_user_abort( true );
error_reporting( E_ALL );
// Maintenance mode (var/maintenance.json), as every front controller
if ( !class_exists( 'expMaintenance', false ) )
    require_once __DIR__ . '/kernel/classes/expmaintenance.php';
if ( expMaintenance::check( __DIR__ ) )
    return;

require 'autoload.php';

// The request filter config.php names (EXP_REQUEST_PREPEND_FILE), as index.php runs it: in a persistent worker
// (Velocity), where config.php ran only once, this is where the request shield sees the request
if ( defined( 'EXP_REQUEST_PREPEND_FILE' ) && is_file( EXP_REQUEST_PREPEND_FILE ) )
{
    require EXP_REQUEST_PREPEND_FILE;
}

expImageAliasServer::runEntryScript();
