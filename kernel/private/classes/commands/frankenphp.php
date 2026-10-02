<?php
/**
 * The code of bin/php/frankenphp.php, moved into a class (#207 stage 1). The file bin/php/frankenphp.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/frankenphp.php:
 *
 *
 * File containing the FrankenPHP control script.
 *
 * @description Control the FrankenPHP engine of the Exponential web server
 * @alias fp
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Frankenphp extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'a', 'asJson', 'cli', 'exitCode', 'flag', 'forward', 'fpArgs', 'fpTail', 'options', 'parts', 'pipes', 'proc', 'script', 'velocityScript' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array( 'description' => (
            "Exponential web server, FrankenPHP engine - production\n\n" .
            "The same controller as exp:velocity and exp:webserver, pinned to the\n" .
            "frankenphp engine (Caddy with PHP built in). Every verb behaves exactly\n" .
            "as exp:velocity <verb> --engine=frankenphp; this command just fixes the\n" .
            "engine so you do not have to name it.\n\n" .
            "Commands:\n" .
            "  status      what the FrankenPHP engine is doing (the default)\n" .
            "  start       start it\n" .
            "  stop        ask it to stop, and wait\n" .
            "  restart     stop, then start\n" .
            "  graceful    re-exec without dropping the listening socket\n" .
            "  kill        stop without asking, for a wedged worker\n" .
            "  command     print the command line it would run, and exit\n" .
            "  config      read and write the settings it runs on\n" .
            "  cache       cache clear: re-render every cached page on its next request\n" .
            "  ctl         FrankenPHP's own control: ctl caddyfile | validate | adapt |\n" .
            "              version | list-modules\n" .
            "  install     download the pinned FrankenPHP release and verify its SHA-256\n" .
            "              (--force, --from=<file>, --check, --trust-github-digest)\n\n" .
            "FrankenPHP address and ports come from [FrankenPHPSettings] in velocity.ini\n" .
            "(Port for plain HTTP, HTTPSPort for TLS), so it runs beside the other\n" .
            "engines without clashing. HTTPS is on by default with a self-signed\n" .
            "certificate unless [HTTPSSettings] names one; --https / --no-https force it\n" .
            "for a single start.\n\n" .
            "This command targets the frankenphp engine. --engine=<other> is honoured if\n" .
            "you pass it (it then behaves as exp:velocity for that engine), and --all\n" .
            "reaches every engine; with neither, frankenphp is used.\n\n" .
            "Through the console:\n" .
            "  ./bin/php/console exp:frankenphp status --allow-root-user\n" .
            "  ./bin/php/console exp:frankenphp install --allow-root-user\n" .
            "  ./bin/php/console exp:fp status --allow-root-user   (exp:fp is shorthand)\n\n" .
            "Settings come from velocity.ini; override per installation in\n" .
            "settings/override/velocity.ini.append.php." ),
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true ) );

        $script->startup();

        // GNU and BSD spellings alike, exactly as velocity.php normalizes them.
        list( $fpArgs, $fpTail ) = \expVelocity::normalizeCliArguments(
            array_slice( $_SERVER['argv'], 1 ),
            array( 'engine', 'from', 'keep-global', 'siteaccess', 'login', 'password' ),
            array( 'json', 'help', 'quiet', 'verbose', 'colors', 'no-colors', 'allow-root-user', 'debug',
                   'all', 'force', 'check', 'trust-github-digest', 'https', 'no-https' ) );

        $options = $this->options(
            '[json][engine:][all][force][check][trust-github-digest][from:][keep-global:][https][no-https]',
            '[command]',
            array( 'json' => 'Report as JSON, for a caller that is not a person',
                   'engine' => 'override the frankenphp default for this command only (php, qbix, or several)',
                   'all' => 'start, stop, restart, graceful, kill or status every engine, one after the other',
                   'from' => 'install: take the binary from this file instead of downloading it',
                   'force' => 'install: download again even if the binary is already there',
                   'check' => 'install: re-hash the installed binary against its SHA-256',
                   'trust-github-digest' => 'install: accept the digest GitHub publishes when no Sha256 is pinned' ),
            $fpArgs );
        $options['arguments'] = array_merge( $options['arguments'], $fpTail );
        $script->initialize();

        $asJson = !empty( $options['json'] );

        // ── delegate to the velocity controller, engine pinned to frankenphp ───────
        // velocity.php owns every engine verb; reusing it as a child process keeps this
        // script free of engine logic. The frankenphp engine is injected only when the
        // operator named neither an engine nor --all, so an explicit --engine=<other>
        // still works and --all still reaches every engine.
        $forward = $options['arguments']; // verb + any sub-arguments (ctl caddyfile, config get X Y, ...)

        if ( !empty( $options['engine'] ) )
            $forward[] = '--engine=' . $options['engine'];
        elseif ( empty( $options['all'] ) )
            $forward[] = '--engine=frankenphp';

        if ( !empty( $options['all'] ) )
            $forward[] = '--all';
        if ( $asJson )
            $forward[] = '--json';
        foreach ( array( 'force', 'check', 'trust-github-digest', 'https', 'no-https' ) as $flag )
            if ( !empty( $options[$flag] ) )
                $forward[] = '--' . $flag;
        if ( !empty( $options['from'] ) )
            $forward[] = '--from=' . $options['from'];
        if ( !empty( $options['keep-global'] ) )
            $forward[] = '--keep-global=' . $options['keep-global'];
        if ( !empty( $options['siteaccess'] ) )
            $forward[] = '--siteaccess=' . $options['siteaccess'];
        foreach ( array_slice( $_SERVER['argv'], 1 ) as $a )
            if ( in_array( $a, array( '--allow-root-user', '--debug', '--quiet', '--verbose', '--colors', '--no-colors' ), true ) )
                $forward[] = $a;

        $velocityScript = $this->scriptDir() . '/velocity.php';

        $parts = array( escapeshellarg( PHP_BINARY ), escapeshellarg( $velocityScript ) );
        foreach ( $forward as $a )
            $parts[] = escapeshellarg( $a );

        $proc = proc_open( implode( ' ', $parts ), array( 0 => STDIN, 1 => STDOUT, 2 => STDERR ), $pipes );
        if ( $proc === false )
        {
            $cli->error( 'frankenphp: could not run the velocity controller' );
            $script->shutdown( 1 );
        }
        $exitCode = proc_close( $proc );
        $script->shutdown( (int)$exitCode );
    }
}

}
