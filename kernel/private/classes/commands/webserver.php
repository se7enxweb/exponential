<?php
/**
 * The code of bin/php/webserver.php, moved into a class (#207 stage 1). The file bin/php/webserver.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/webserver.php:
 *
 *
 * File containing the web server control script.
 *
 * @description Control the Exponential web server (php, frankenphp or qbix engines)
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{


/**
 * The velocity.ini block an engine keeps its own ServerSettings-style keys in,
 * so the three engines can run side by side. The qbix engine reads
 * [ServerSettings] directly; the others have a block of their own.
 *
 * @param string $engine
 * @return string
 */
function webserverEngineBlock( $engine )
{
    switch ( $engine )
    {
        case 'php':        return 'PHPServerSettings';
        case 'frankenphp': return 'FrankenPHPSettings';
        default:           return 'ServerSettings';
    }
}

/**
 * The port the given engine would use right now: its own block if it sets one,
 * then [ServerSettings], then the packaged default for that engine.
 *
 * @param string $engine
 * @return string
 */
function webserverEffectivePort( $engine )
{
    $ini = eZINI::instance( 'velocity.ini' );
    $block = webserverEngineBlock( $engine );
    if ( $block !== 'ServerSettings' && $ini->hasVariable( $block, 'Port' ) )
    {
        $v = trim( (string)$ini->variable( $block, 'Port' ) );
        if ( $v !== '' ) return $v;
    }
    if ( $ini->hasVariable( 'ServerSettings', 'Port' ) )
    {
        $v = trim( (string)$ini->variable( 'ServerSettings', 'Port' ) );
        if ( $v !== '' ) return $v;
    }
    $defaults = array( 'php' => '8087', 'frankenphp' => '8089', 'qbix' => '8088' );
    return isset( $defaults[$engine] ) ? $defaults[$engine] : '8088';
}

/**
 * Whether this installation has already been configured, i.e. its override
 * names the engine. First start configures; every later start runs directly.
 *
 * @return bool
 */
function webserverIsConfigured()
{
    $cfg = new expVelocityConfig();
    $g = $cfg->get( 'ServerSettings', 'Engine' );
    return !empty( $g['ok'] ) && !empty( $g['overridden'] );
}

/**
 * Resolve the effective engine, port, document root and HTTPS port and persist
 * them to the installation override, one surgical key at a time through
 * expVelocityConfig (the same writer exp:velocity config uses). Unrelated keys,
 * including [HTTPSSettings] certificates and [PHPSettings] IniOptions, are never
 * touched. With $dryRun the plan is returned but nothing is written.
 *
 * @param array $options parsed CLI options
 * @param bool $dryRun
 * @return array ok, message, data (planned/applied writes)
 */
function webserverConfigure( array $options, $dryRun )
{
    $engine = !empty( $options['engine'] )
            ? strtolower( trim( (string)$options['engine'] ) )
            : expVelocity::defaultEngine();
    if ( !in_array( $engine, expVelocity::engines(), true ) )
        return array( 'ok' => false,
            'message' => "unknown engine '$engine'; use " . implode( ', ', expVelocity::engines() ), 'data' => array() );

    $block = webserverEngineBlock( $engine );
    $port  = isset( $options['port'] ) && trim( (string)$options['port'] ) !== ''
           ? trim( (string)$options['port'] ) : webserverEffectivePort( $engine );

    // The keys to write: engine, then the engine's own port. Document root and
    // HTTPS port only when the operator gave one, so an empty value is never
    // forced over a packaged default.
    $plan = array();
    $plan[] = array( 'ServerSettings', 'Engine', $engine );
    $plan[] = array( $block, 'Port', $port );
    if ( isset( $options['docroot'] ) )
        $plan[] = array( $block, 'DocumentRoot', (string)$options['docroot'] );
    if ( isset( $options['https-port'] ) && trim( (string)$options['https-port'] ) !== '' )
    {
        if ( $engine === 'php' )
            $plan[] = array( '', '', 'note: the php engine has no TLS; --https-port ignored' );
        else
            $plan[] = array( $block, 'HTTPSPort', trim( (string)$options['https-port'] ) );
    }

    $actions = array();
    $ok = true;
    foreach ( $plan as $row )
    {
        list( $b, $v, $value ) = $row;
        if ( $b === '' )
        {
            $actions[] = $value; // a note, not a write
            continue;
        }
        if ( $dryRun )
        {
            $actions[] = "would set [$b] $v = '$value'";
            continue;
        }
        $cfg = new expVelocityConfig();
        $res = $cfg->set( $b, $v, $value );
        $ok = $ok && !empty( $res['ok'] );
        $actions[] = ( !empty( $res['ok'] ) ? 'set ' : 'FAILED ' ) . "[$b] $v = '$value'"
                   . ( empty( $res['ok'] ) ? ' -- ' . $res['message'] : '' );
    }

    $summary = ( $dryRun ? 'configure (dry run): ' : 'configured ' )
             . "engine=$engine port=$port"
             . ( isset( $options['docroot'] ) ? " docroot='" . $options['docroot'] . "'" : '' );
    return array( 'ok' => $ok, 'message' => $summary, 'data' => array( 'engine' => $engine, 'actions' => $actions ) );
}

/**
 * Report a configure result for a person or as JSON.
 */
function webserverReportConfigure( eZCLI $cli, array $res, $asJson )
{
    if ( $asJson )
    {
        $cli->output( json_encode( $res ) );
        return;
    }
    foreach ( (array)( $res['data']['actions'] ?? array() ) as $line )
        $cli->output( '  ' . $line );
    if ( !empty( $res['ok'] ) )
        $cli->output( $cli->stylize( 'emphasize', 'webserver: ' . $res['message'] ) );
    else
        $cli->error( 'webserver: ' . $res['message'] );
}
}

namespace Exponential\Command\Kernel
{

class Webserver extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'a', 'asJson', 'cli', 'dryRun', 'exitCode', 'flag', 'forward', 'mustConfigure', 'options', 'parts', 'pipes', 'proc', 'res', 'script', 'velocityScript', 'verb', 'verbs', 'wsArgs', 'wsTail' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => (
            "Exponential web server - the engine-agnostic control command\n\n" .
            "One command for whichever engine this installation runs. It reuses the\n" .
            "same controller as exp:velocity, so every verb behaves identically; the\n" .
            "difference is only the name and that it frames the three engines as one\n" .
            "web server. exp:frankenphp is the same command pinned to the frankenphp\n" .
            "engine.\n\n" .
            "Commands:\n" .
            "  status      what each engine is doing (the default)\n" .
            "  start       configure on first use, then run the engine\n" .
            "  stop        ask it to stop, and wait\n" .
            "  restart     stop, then start\n" .
            "  graceful    re-exec without dropping the listening socket\n" .
            "  kill        stop without asking, for a wedged worker\n" .
            "  configure   resolve and persist the engine, port, document root and\n" .
            "              HTTPS port into settings/override/velocity.ini.append.php,\n" .
            "              without starting anything (--dry-run shows the plan only)\n" .
            "  command     print the command line it would run, and exit\n" .
            "  config      read and write the settings it runs on\n" .
            "  cache       cache clear: re-render every cached page on its next request\n" .
            "  layout      the configuration tree and every file the server uses\n" .
            "  ctl         the engine's own control, with this installation's tree\n" .
            "  install     put the engine's binary in place\n\n" .
            "Engines ([ServerSettings] Engine is the configured default; --engine=<name>\n" .
            "or --all reach the others for start, stop, restart, graceful, kill, status):\n" .
            "  php         PHP's built-in web server: development, always works\n" .
            "  frankenphp  FrankenPHP (Caddy with PHP built in): production\n" .
            "  qbix        the bundled Qbix server: experimental, for tests\n" .
            "  Each has its own port, pid file and logs, so they can run side by side.\n\n" .
            "Configure once, then run directly:\n" .
            "  The first start with no saved engine resolves the effective engine, port,\n" .
            "  document root and HTTPS port and writes them to this installation's\n" .
            "  override, then runs. Every later start runs straight from that saved\n" .
            "  configuration. To change it without hand-editing the ini:\n" .
            "  configure [--engine=] [--port=] [--docroot=] [--https-port=]   persist and stop\n" .
            "  start --reconfigure [--engine=] [--port=] ...                  persist, then run\n" .
            "  --dry-run                                                      show, do not write\n" .
            "  Only the engine/port/docroot/HTTPS-port keys are touched; [HTTPSSettings]\n" .
            "  certificates and [PHPSettings] IniOptions the installation set are left\n" .
            "  exactly as they are.\n\n" .
            "Through the console:\n" .
            "  ./bin/php/console exp:webserver status --allow-root-user\n" .
            "  ./bin/php/console exp:webserver start --engine=php --allow-root-user\n" .
            "  ./bin/php/console exp:webserver configure --engine=frankenphp --dry-run --allow-root-user\n\n" .
            "Settings come from velocity.ini; override per installation in\n" .
            "settings/override/velocity.ini.append.php." ),
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true ) );

        $script->startup();

        // GNU and BSD spellings alike, exactly as velocity.php normalizes them, so
        // --engine php and -engine=php both work. This script's own valued names add
        // port, docroot and https-port; its own flags add reconfigure and dry-run.
        list( $wsArgs, $wsTail ) = \expVelocity::normalizeCliArguments(
            array_slice( $_SERVER['argv'], 1 ),
            array( 'engine', 'port', 'docroot', 'https-port', 'from', 'keep-global', 'siteaccess', 'login', 'password' ),
            array( 'json', 'help', 'quiet', 'verbose', 'colors', 'no-colors', 'allow-root-user', 'debug',
                   'all', 'reconfigure', 'dry-run', 'force', 'check', 'trust-github-digest', 'https', 'no-https' ) );

        $options = $script->getOptions(
            '[json][engine:][port:][docroot:][https-port:][reconfigure][dry-run][all][force][check][trust-github-digest][from:][keep-global:][https][no-https]',
            '[command]',
            array( 'json' => 'Report as JSON, for a caller that is not a person',
                   'engine' => 'php, frankenphp or qbix for this command only (or several, comma-separated, or --all), '
                             . 'instead of the configured default',
                   'port' => 'configure/reconfigure: the port to persist for the engine',
                   'docroot' => 'configure/reconfigure: the document root to persist for the engine',
                   'https-port' => 'configure/reconfigure: the HTTPS port to persist (frankenphp or qbix; php has no TLS)',
                   'reconfigure' => 'start: resolve and persist the engine/port/docroot/HTTPS port again before running',
                   'dry-run' => 'configure/reconfigure: show what would be written, without writing it',
                   'all' => 'start, stop, restart, graceful, kill or status every engine, one after the other',
                   'from' => 'install: take the binary from this file instead of downloading it',
                   'force' => 'install: download again even if the binary is already there',
                   'check' => 'install: re-hash the installed binary against its SHA-256',
                   'trust-github-digest' => 'install: accept the digest GitHub publishes when no Sha256 is pinned' ),
            $wsArgs );
        $options['arguments'] = array_merge( $options['arguments'], $wsTail );
        $script->initialize();

        $asJson = !empty( $options['json'] );
        $dryRun = !empty( $options['dry-run'] );

        $verbs = array( 'start', 'stop', 'graceful', 'restart', 'kill', 'status',
                        'command', 'config', 'cache', 'layout', 'site', 'conf', 'mod',
                        'ctl', 'ssl', 'ext', 'install', 'configure' );
        $verb = isset( $options['arguments'][0] ) ? strtolower( trim( $options['arguments'][0] ) ) : 'status';

        if ( !in_array( $verb, $verbs, true ) )
        {
            $cli->error( "webserver: unknown command '$verb'. Try one of: " . implode( ', ', $verbs ) );
            $script->shutdown( 1 );
        }

        // ── configure / reconfigure ───────────────────────────────────────────────
        // A plain `configure` persists and stops. A `start` persists first when the
        // operator asked (--reconfigure) or when the installation has never been
        // configured, then falls through to run.
        $mustConfigure = ( $verb === 'configure' )
                      || ( $verb === 'start' && ( !empty( $options['reconfigure'] ) || !webserverIsConfigured() ) );

        if ( $mustConfigure )
        {
            $res = webserverConfigure( $options, $dryRun );
            webserverReportConfigure( $cli, $res, $asJson );
            if ( $verb === 'configure' || $dryRun || empty( $res['ok'] ) )
                $script->shutdown( empty( $res['ok'] ) ? 1 : 0 );
            // start: configuration written, now run the engine below.
        }

        // ── delegate the actual work to the velocity controller ───────────────────
        // velocity.php owns every engine verb; reusing it as a child process keeps this
        // script free of engine logic and gives correct, independent option parsing.
        // This script's own flags (configure, reconfigure, port, docroot, https-port,
        // dry-run) are dropped here; everything the controller understands is rebuilt
        // from the parsed options so nothing leaks through.
        $forward = $options['arguments']; // verb + any sub-arguments (ctl status, config get X Y, ...)

        if ( !empty( $options['engine'] ) )
            $forward[] = '--engine=' . $options['engine'];
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
        // Standard eZ flags the controller also honours, passed through verbatim.
        foreach ( array_slice( $_SERVER['argv'], 1 ) as $a )
            if ( in_array( $a, array( '--allow-root-user', '--debug', '--quiet', '--verbose', '--colors', '--no-colors' ), true ) )
                $forward[] = $a;

        $velocityScript = dirname( $this->scriptDir() ) . '/php/velocity.php';
        if ( !is_file( $velocityScript ) )
            $velocityScript = $this->scriptDir() . '/velocity.php';

        $parts = array( escapeshellarg( PHP_BINARY ), escapeshellarg( $velocityScript ) );
        foreach ( $forward as $a )
            $parts[] = escapeshellarg( $a );

        $proc = proc_open( implode( ' ', $parts ), array( 0 => STDIN, 1 => STDOUT, 2 => STDERR ), $pipes );
        if ( $proc === false )
        {
            $cli->error( 'webserver: could not run the velocity controller' );
            $script->shutdown( 1 );
        }
        $exitCode = proc_close( $proc );
        $script->shutdown( (int)$exitCode );
    }
}

}
