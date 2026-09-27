<?php
/**
 * File containing the rest bootstrap
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// Maintenance mode (var/maintenance.json): answered here, before the settings
// and the database, so a site being installed or taken offline never serves
// a half-built page. See kernel/classes/expmaintenance.php.
if ( is_file( __DIR__ . '/var/maintenance.json' ) )
{
    require_once __DIR__ . '/kernel/classes/expmaintenance.php';
    if ( expMaintenance::check( __DIR__ ) )
        return;
}

require __DIR__ . '/autoload.php';

ignore_user_abort( true );
error_reporting ( E_ALL | E_STRICT & E_DEPRECATED);
require 'autoload.php';

$kernel = new ezpKernel( new ezpKernelRest() );

// mvc_tools will directly output the headers and content
$kernel->run();
