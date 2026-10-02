<?php
/**
 * The code of cronjobs/basket_cleanup.php, moved into a class (#207 stage 1). The file cronjobs/basket_cleanup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
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
