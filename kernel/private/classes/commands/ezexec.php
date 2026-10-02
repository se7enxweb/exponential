<?php
/**
 * The code of bin/php/ezexec.php, moved into a class (#207 stage 1). The file bin/php/ezexec.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Command\Kernel
{

class Ezexec extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'cli', 'options', 'retCode', 'script', 'scriptFile' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => ( "Exponential Script Executor\n\n" .
                                                                "Allows execution of simple PHP scripts which use Exponential functionality,\n" .
                                                                "when the script is called all necessary initialization is done\n" .
                                                                "\n" .
                                                                "ezexec.php myscript.php" ),
                                             'use-session' => false,
                                             'use-modules' => true,
                                             'use-extensions' => true ) );

        $script->startup();

        $options = $script->getOptions( "",
                                        "[scriptfile]",
                                        array() );
        $script->initialize();

        if ( count( $options['arguments'] ) < 1 )
        {
            $script->shutdown( 1, "Missing script file" );
        }

        $scriptFile = $options['arguments'][0];

        if ( !file_exists( $scriptFile ) )
            $script->shutdown( 1, "Could not execute the script '$scriptFile', file was not found" );

        $retCode = include( $scriptFile );

        if ( $retCode != 1 )
            $script->setExitCode( 1 );

        $script->shutdown();
    }
}

}
