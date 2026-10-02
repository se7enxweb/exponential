<?php
/**
 * The code of bin/php/install.php, moved into a class (#207 stage 1). The file bin/php/install.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Command\Kernel
{

class Install extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'exitCode', 'flags', 'forward', 'rootDir', 'runner' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $forward = array( '--force' );
        if ( $flags['dry-run'] )
            $forward = array( '--dry-run' );
        if ( $flags['allow-root-user'] )
            $forward[] = '--allow-root-user';

        $runner = new \expKickstarter( $rootDir, $forward );
        $exitCode = $runner->run();

        exit( $exitCode );
    }
}

}
