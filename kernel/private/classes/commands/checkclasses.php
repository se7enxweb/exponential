<?php
/**
 * The code of bin/php/checkclasses.php, moved into a class (#207 stage 1). The file bin/php/checkclasses.php is one call to it.
 * @description Load every declared class and report the ones PHP refuses to load
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Command\Kernel
{

class Checkclasses extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'argv', 'class', 'classes', 'cli', 'found', 'handle', 'line', 'name', 'options', 'reason', 'script', 'why' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        require_once 'kernel/setup/expclassloadcheck.php';

        // ── The child ───────────────────────────────────────────────────────────────
        //
        // Invoked as: php bin/php/checkclasses.php --load < list-of-names
        //
        // Deliberately without eZScript. The kernel installs a fatal handler that
        // prints "An unexpected error has occurred" over whatever php was going to say,
        // and what php was going to say is the entire value of this script.
        if ( in_array( '--load', $argv, true ) )
        {
            $handle = fopen( 'php://stdin', 'r' );

            while ( ( $line = fgets( $handle ) ) !== false )
            {
                $name = trim( $line );

                if ( $name === '' )
                    continue;

                echo "TRY:$name\n";
                flush();

                @class_exists( $name );

                echo "OK:$name\n";
                flush();
            }

            echo "DONE\n";
            exit( 0 );
        }

        // ── The reason one class cannot be loaded ───────────────────────────────────
        //
        // Invoked as: php bin/php/checkclasses.php --why=<class>
        if ( ( $why = \eZCheckClasses::optionValue( $argv, '--why' ) ) !== false )
        {
            @class_exists( $why );
            echo "LOADED\n";
            exit( 0 );
        }

        $cli = $this->cli();
        $script = $this->script( array(
            'description' => "Exponential class loader check\n" .
                             "Loads every class this installation declares and reports the ones php refuses.\n" .
                             "\n" .
                             "./bin/php/checkclasses.php",
            'use-session'    => false,
            'use-modules'    => false,
            'use-extensions' => true ) );

        $options = $this->startup(
            "[kernel][tests][quiet-ok]", "",
            array( 'kernel'   => 'Check the kernel classes as well as the extension ones. Slower, and they are the ones least likely to be wrong.',
                   'tests'    => 'Include classes that live under a tests directory. Left out by default: they are usually written against whichever version of phpunit was current, and a test class that will not load breaks nothing anybody is looking at.',
                   'quiet-ok' => 'Print nothing when everything loads.' ) );

        $classes = \eZCheckClasses::classNames( !empty( $options['kernel'] ), !empty( $options['tests'] ) );

        if ( count( $classes ) === 0 )
        {
            $cli->error( 'No autoload map to read. Run bin/php/ezpgenerateautoloads.php first.' );
            $script->shutdown( 1 );
        }

        $cli->output( 'Loading ' . count( $classes ) . ' classes.' );

        $found = \eZCheckClasses::check( $classes );

        if ( count( $found['bad'] ) === 0 )
        {
            if ( empty( $options['quiet-ok'] ) )
                $cli->output( $cli->stylize( 'green', 'Every one of them loads.' ) );

            $script->shutdown( 0 );
        }

        $cli->output( '' );
        $cli->warning( count( $found['bad'] ) . ' of them cannot be loaded:' );
        $cli->output( '' );

        foreach ( $found['bad'] as $class )
        {
            $reason = \eZCheckClasses::reason( $class );

            $cli->output( '  ' . $cli->stylize( 'emphasize', $class ) );
            $cli->output( '    ' . $reason['kind'] );
            $cli->output( '    ' . $reason['message'] );
            $cli->output( '' );
        }

        $cli->output( 'Each of these ends the request that touches it. A class whose parent is' );
        $cli->output( 'missing usually means an extension that needs another one; an incompatible' );
        $cli->output( 'declaration means a class that has to be changed.' );

        $script->shutdown( 1 );
    }
}

}
