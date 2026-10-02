<?php
/**
 * The code of cronjobs/basket_cleanup.php, moved into a class (#207 stage 1). The file cronjobs/basket_cleanup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/basket_cleanup.php:
 *
 *
 * @description Remove abandoned shopping baskets older than the configured threshold
 *
 * File containing the basket_cleanup.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class BasketCleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $ini = \eZINI::instance();

        // Check if this should be run in a cronjob
        $useCronjob = $ini->variable( 'Session', 'BasketCleanup' ) == 'cronjob';
        if ( !$useCronjob )
            return;

        // Only do basket cleanup once in a while
        $freq = $ini->variable( 'Session', 'BasketCleanupAverageFrequency' );
        if ( mt_rand( 1, max( $freq, 1 ) ) != 1 )
            return;

        $maxTime = $ini->variable( 'Session', 'BasketCleanupTime' );
        $idleTime = $ini->variable( 'Session', 'BasketCleanupIdleTime' );
        $fetchLimit = $ini->variable( 'Session', 'BasketCleanupFetchLimit' );

        $cli->output( "Cleaning up expired baskets" );
        \eZDBGarbageCollector::collectBaskets( $maxTime, $idleTime, $fetchLimit );
    }
}

}
