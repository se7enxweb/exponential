<?php
/**
 * The code of cronjobs/session_gc.php, moved into a class (#207 stage 1). The file cronjobs/session_gc.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/session_gc.php:
 *
 *
 * @description Garbage-collect expired user sessions from the session store
 *
 * File containing the session_gc.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
function eZSessionBasketGarbageCollector( $db, $time )
{
    eZBasket::cleanupExpired( $time );
}
}

namespace Exponential\Cronjob\Kernel
{

class SessionGc extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        // Fill in hooks
        \eZSession::addCallback( 'gc_pre', 'eZSessionBasketGarbageCollector');

        \eZSession::garbageCollector();
    }
}

}
