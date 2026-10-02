<?php
/**
 * The code of cronjobs/session_gc.php, moved into a class (#207 stage 1). The file cronjobs/session_gc.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
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
