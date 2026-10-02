<?php
/**
 * The code of cronjobs/trashpurge.php, moved into a class (#207 stage 1). The file cronjobs/trashpurge.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Cronjob\Kernel
{

class Trashpurge extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $purgeHandler = new \eZScriptTrashPurge( \eZCLI::instance() );
        $purgeHandler->run();
    }
}

}
