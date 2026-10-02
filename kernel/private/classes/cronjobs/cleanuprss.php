<?php
/**
 * The code of cronjobs/cleanuprss.php, moved into a class (#207 stage 1). The file cronjobs/cleanuprss.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Cronjob\Kernel
{

class Cleanuprss extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $cleanup = new \expCleanupRSS();

        // Said once, rather than by every run of a cronjob that has nothing to do.
        if ( !$cleanup->isEnabled() )
        {
            \eZDebug::writeNotice( 'RSS import cleanup is not running: ' . $cleanup->reason(), $this->scriptFile() );
            return;
        }

        $cli->output( 'Cleaning up imported RSS content...' );

        $cleanup->cleanup();

        $counts = $cleanup->counts();

        $cli->output( sprintf( 'Done. %d item(s) removed across %d feed(s).',
                               $counts['removed'], $counts['feeds'] ) );
    }
}

}
