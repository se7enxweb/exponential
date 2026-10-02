<?php
/**
 * The code of bin/php/phar.php, moved into a class (#207 stage 1). The file bin/php/phar.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/phar.php:
 *
 *
 * File containing the engine phar command.
 *
 * Discovered by the console as exp:phar.
 *
 *   php -d phar.readonly=0 bin/php/console exp:phar build
 *   bin/php/console exp:phar build --force   (rebuild even when current)
 *   bin/php/console exp:phar check
 *   bin/php/console exp:phar info
 *   bin/php/console exp:phar clean
 *
 * Building needs phar.readonly off, which is an ini setting and not something
 * this script can change for itself; the build verb says so rather than
 * failing obscurely.
 *
 * To run the installation from the archive, set EXP_ENGINE_PHAR in the
 * environment of whatever serves it. The autoloader reads kernel and library
 * classes from the archive when it is set and from disk when it is not, and
 * nothing else about the installation changes either way.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 * @package   kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Phar extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'check', 'cli', 'command', 'current', 'exit', 'force', 'json', 'k', 'options', 'outputPath', 'result', 'script', 'v', 'verb' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array(
            'description' => "Exponential engine phar\n\nBuild and inspect the engine archive.",
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => false,
        ) );
        $script->startup();

        $options = $script->getOptions(
            '[json][output:][force]',
            '[verb]',
            array(
                'json'   => 'Print the result as JSON.',
                'output' => 'Write the archive somewhere other than dist/engine.phar.',
                'force'  => 'build: rebuild even when the archive already carries exactly the files on disk.',
                'verb'   => "build, check, info or clean (default: info)\n"
                          . "build  write the archive, unless it is current (--force: anyway)\n"
                          . "check  whether it is current, and if not which files differ; exit 1 when not",
            ) );
        $script->initialize();

        require_once 'kernel/classes/expphar.php';

        $verb = isset( $options['arguments'][0] ) ? $options['arguments'][0] : 'info';
        $json = !empty( $options['json'] );
        $force = !empty( $options['force'] );
        $outputPath = isset( $options['output'] ) ? (string)$options['output'] : '';

        // Building needs phar.readonly off, and the console dispatches each command to
        // a fresh PHP, so an ini flag typed on the console line never reaches here.
        // Re-exec once with the setting rather than telling the caller to work that
        // out. The guard variable stops a loop if the setting somehow does not take.
        // An archive that is current needs no writing, so that is found out first.
        $current = null;
        if ( $verb === 'build' && !$force && ini_get( 'phar.readonly' ) )
        {
            $current = \expPhar::build( array( 'output' => $outputPath ) );
            if ( !$current['ok'] || !empty( $current['data']['rebuilt'] ) || !empty( $current['data']['unparsable'] ) )
                $current = null;
        }
        if ( $verb === 'build' && $current === null && ini_get( 'phar.readonly' ) && getenv( 'EXP_PHAR_REEXEC' ) !== '1' )
        {
            $command = escapeshellarg( PHP_BINARY ) . ' -d phar.readonly=0 '
                     . escapeshellarg( $this->scriptFile() ) . ' build --allow-root-user';
            if ( $outputPath !== '' )
                $command .= ' --output=' . escapeshellarg( $outputPath );
            if ( $force )
                $command .= ' --force';
            if ( $json )
                $command .= ' --json';

            putenv( 'EXP_PHAR_REEXEC=1' );
            passthru( $command, $exit );
            $script->shutdown( $exit );
            return;
        }

        switch ( $verb )
        {
            case 'build':
                $result = $current !== null ? $current
                        : \expPhar::build( array( 'output' => $outputPath, 'force' => $force ) );
                break;
            case 'check':
                $check = \expPhar::check( $outputPath !== '' ? $outputPath : null );
                $result = array( 'ok' => $check['current'],
                                 'message' => ( $check['current'] ? 'engine.phar is current: ' : 'engine.phar is not current: ' ) . $check['reason'],
                                 'data' => array( 'files' => $check['files'], 'touched' => $check['touched'] )
                                         + array_filter( array_map( function ( $list ) { return array_slice( $list, 0, 20 ); },
                                               array_intersect_key( $check, array_flip( array( 'changed', 'added', 'removed' ) ) ) ) ) );
                break;
            case 'clean':
                $result = \expPhar::clean();
                break;
            case 'info':
                $result = \expPhar::info();
                break;
            default:
                $result = array( 'ok' => false, 'message' => "unknown verb '$verb'; use build, check, info or clean", 'data' => array() );
        }

        if ( $json )
        {
            $cli->output( json_encode( $result ) );
        }
        else
        {
            $cli->output( ( $result['ok'] ? '  ' : '  ERROR: ' ) . $result['message'] );
            foreach ( $result['data'] as $k => $v )
            {
                if ( is_array( $v ) )
                    $v = implode( ', ', $v );
                elseif ( is_bool( $v ) )
                    $v = $v ? 'yes' : 'no';
                $cli->output( sprintf( '    %-10s %s', $k, $v ) );
            }
        }

        $script->shutdown( $result['ok'] ? 0 : 1 );
    }
}

}
