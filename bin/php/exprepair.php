#!/usr/bin/env php
<?php
/**
 * The repair queue of the missing-libraries error page (lib/ezutils/classes/ezprepairqueue.php).
 * Plain PHP: works while the Composer libraries are missing. Run it as the web server user.
 *
 *   php bin/php/exprepair.php --create-key   a new one-time key for the error page (printed once)
 *   php bin/php/exprepair.php --disable      turn the page's repair off
 *   php bin/php/exprepair.php --status       the state of the last repair and the end of its log
 *   php bin/php/exprepair.php --run          run the queued repair (started by the page; as root it
 *                                            switches to the installation's owner first)
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */
require_once dirname( __DIR__, 2 ) . '/lib/ezutils/classes/ezprepairqueue.php';
$arg = isset( $argv[1] ) ? $argv[1] : '--help';
switch ( $arg )
{
    case '--create-key':
        $key = ezpRepairQueue::createKey();
        echo "Repair key (shown once, used once): $key\n"
           . "Open any page of the site while the libraries are missing, enter the key and start the repair.\n";
        exit( 0 );
    case '--disable':
        $s = ezpRepairQueue::settings();
        $s['Enabled'] = false;
        $s['KeyHash'] = '';
        ezpRepairQueue::writeSettings( $s );
        echo "The error page's repair is off.\n";
        exit( 0 );
    case '--status':
        $s = ezpRepairQueue::status();
        unset( $s['token'] );
        echo json_encode( $s, JSON_PRETTY_PRINT ), "\n", ezpRepairQueue::logTail(), "\n";
        exit( 0 );
    case '--run':
        exit( ezpRepairQueue::runWorker() );
    default:
        echo "Usage: php bin/php/exprepair.php --create-key | --disable | --status | --run\n";
        exit( $arg === '--help' ? 0 : 1 );
}
