<?php
/**
 * The code of bin/php/solr.php, moved into a class (#207 stage 1). The file bin/php/solr.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/solr.php:
 *
 *
 * File containing the Solr search server control script.
 *
 * @description Control the Solr search server, once it is installed and configured
 * @alias search
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
           // a remote Solr cannot be started or stopped from here

/**
 * A [SolrSettings] value with a fallback, without throwing when it is absent.
 */
function solrSetting( $variable, $default = '' )
{
    $ini = eZINI::instance( 'solr.ini' );
    if ( !$ini->hasVariable( 'SolrSettings', $variable ) )
        return $default;
    return $ini->variable( 'SolrSettings', $variable );
}

/**
 * The signals that Solr is meant to be used on this installation, so the
 * "not configured" message can say what is missing rather than just "no".
 *
 * @return array enabled, hasUrl, hasBinary, hasEzfind, configured, mode
 */
function solrDetect()
{
    $enabled = strtolower( trim( (string)solrSetting( 'Enabled', 'false' ) ) );
    $enabled = in_array( $enabled, array( 'true', 'enabled', '1', 'yes' ), true );
    $url = trim( (string)solrSetting( 'Url', '' ) );
    $binary = trim( (string)solrSetting( 'BinaryPath', '' ) );
    $hasEzfind = is_dir( eZSys::rootDir() . '/extension/ezfind' );
    $mode = $url !== '' ? 'remote' : ( $binary !== '' ? 'local' : 'none' );
    return array(
        'enabled'    => $enabled,
        'hasUrl'     => $url !== '',
        'url'        => $url,
        'hasBinary'  => $binary !== '',
        'binary'     => $binary,
        'hasEzfind'  => $hasEzfind,
        'configured' => $enabled && $mode !== 'none',
        'mode'       => $mode,
    );
}

/**
 * The base URL to reach Solr at: the configured Url, or one built from Host and
 * Port for a local instance.
 */
function solrBaseUrl( array $d )
{
    if ( $d['hasUrl'] )
        return rtrim( $d['url'], '/' );
    $host = trim( (string)solrSetting( 'Host', '127.0.0.1' ) );
    $port = (int)solrSetting( 'Port', 8983 );
    return 'http://' . ( $host === '' ? '127.0.0.1' : $host ) . ':' . ( $port ?: 8983 );
}

/**
 * Whether Solr answers its system-info endpoint. Returns array reachable, note.
 */
function solrPing( array $d )
{
    $base = solrBaseUrl( $d );
    $endpoint = $base . '/solr/admin/info/system?wt=json';
    $ctx = stream_context_create( array( 'http' => array( 'timeout' => 5, 'ignore_errors' => true ),
                                         'ssl'  => array( 'verify_peer' => false, 'verify_peer_name' => false ) ) );
    $body = @file_get_contents( $endpoint, false, $ctx );
    if ( $body === false )
        return array( 'reachable' => false, 'url' => $base, 'note' => 'no response from ' . $endpoint );
    $ok = false;
    if ( isset( $http_response_header ) )
        foreach ( $http_response_header as $h )
            if ( preg_match( '#^HTTP/\S+\s+(\d+)#', $h, $m ) && (int)$m[1] < 500 )
                $ok = true;
    return array( 'reachable' => $ok, 'url' => $base,
                  'note' => $ok ? 'answered at ' . $endpoint : 'error response from ' . $endpoint );
}

/**
 * Whether the local Solr port is open.
 */
function solrPortOpen()
{
    $host = trim( (string)solrSetting( 'Host', '127.0.0.1' ) );
    $port = (int)solrSetting( 'Port', 8983 );
    $sock = @fsockopen( $host === '' ? '127.0.0.1' : $host, $port ?: 8983, $errno, $errstr, 2 );
    if ( $sock === false )
        return false;
    fclose( $sock );
    return true;
}

/**
 * Make sure the run and log directories exist, so a local Solr has somewhere to
 * write. Returns the log directory.
 */
function solrEnsureDirs()
{
    $root = rtrim( eZSys::rootDir(), '/' );
    foreach ( array( 'RunDir' => 'var/vc/solr/run', 'LogDir' => 'var/vc/solr/log' ) as $key => $default )
    {
        $dir = trim( (string)solrSetting( $key, $default ) );
        if ( $dir === '' ) $dir = $default;
        $abs = ( $dir[0] === '/' ) ? $dir : $root . '/' . $dir;
        if ( !is_dir( $abs ) ) @mkdir( $abs, 0755, true );
    }
    $logDir = trim( (string)solrSetting( 'LogDir', 'var/vc/solr/log' ) );
    if ( $logDir === '' ) $logDir = 'var/vc/solr/log';
    return ( $logDir[0] === '/' ) ? $logDir : $root . '/' . $logDir;
}

/**
 * The command line to run Solr's own control for a local instance.
 * $action is start, stop, restart or status.
 */
function solrLocalCommand( array $d, $action )
{
    $binary = $d['binary'];
    $port = (int)solrSetting( 'Port', 8983 );
    $host = trim( (string)solrSetting( 'Host', '127.0.0.1' ) );
    $dataDir = trim( (string)solrSetting( 'DataDir', '' ) );

    $args = array( escapeshellarg( $binary ), $action );
    if ( in_array( $action, array( 'start', 'restart' ), true ) )
    {
        $args[] = '-p'; $args[] = (int)( $port ?: 8983 );
        if ( $host !== '' && $host !== '127.0.0.1' && $host !== 'localhost' )
        {
            $args[] = '-h'; $args[] = escapeshellarg( $host );
        }
        if ( $dataDir !== '' )
        {
            $args[] = '-s'; $args[] = escapeshellarg( $dataDir );
        }
    }
    elseif ( $action === 'stop' )
    {
        $args[] = '-p'; $args[] = (int)( $port ?: 8983 );
    }
    return implode( ' ', $args );
}

/**
 * Report a result for a person or as JSON, and shut down with $exit.
 */
function solrFinish( eZCLI $cli, eZScript $script, $ok, $message, array $data, $asJson, $exit )
{
    if ( $asJson )
        $cli->output( json_encode( array( 'ok' => (bool)$ok, 'message' => $message, 'data' => $data ) ) );
    elseif ( $ok )
        $cli->output( $cli->stylize( 'emphasize', 'solr: ' . $message ) );
    else
        $cli->error( 'solr: ' . $message );
    $script->shutdown( $exit );
}
}

namespace Exponential\Command\Kernel
{

class Solr extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'EXIT_FAIL', 'EXIT_NOT_CONFIGURED', 'EXIT_OK', 'EXIT_REMOTE', 'action', 'asJson', 'binary', 'cli', 'command', 'd', 'deadline', 'full', 'l', 'lines', 'logDir', 'logFile', 'ok', 'options', 'out', 'p', 'portOpen', 'ret', 'running', 'script', 'timeout', 'verb', 'verbs', 'why' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => (
            "Exponential Solr - control the Solr search server\n\n" .
            "Solr is optional. This installation uses the built-in eZ search index by\n" .
            "default (bin/php/updatesearchindex.php); Solr takes over only once it is\n" .
            "installed and turned on. Until then every verb below says so plainly and\n" .
            "start exits non-zero, so nothing silently pretends a search server is\n" .
            "running when none is.\n\n" .
            "Commands (same shape as exp:webserver):\n" .
            "  status      whether Solr is configured, and if so whether it answers\n" .
            "  start       start the local Solr instance (BinaryPath), or report that a\n" .
            "              remote Solr (Url) is managed externally\n" .
            "  stop        stop the local Solr instance\n" .
            "  restart     stop, then start\n" .
            "  ping        ask Solr whether it is alive\n\n" .
            "Enable Solr in settings/solr.ini (override in\n" .
            "settings/override/solr.ini.append.php):\n" .
            "  [SolrSettings]\n" .
            "  Enabled=true\n" .
            "  Url=http://127.0.0.1:8983          a running Solr to talk to, or\n" .
            "  BinaryPath=/opt/solr/bin/solr      the Solr control binary on this box\n" .
            "  Host=127.0.0.1  Port=8983  DataDir=/var/solr/data\n\n" .
            "With Url set, Solr is treated as remote/managed elsewhere: status and ping\n" .
            "reach it, start and stop do not. With BinaryPath set (and Url empty), this\n" .
            "command runs Solr's own start/stop/status for a local instance, keeping its\n" .
            "logs under var/vc/solr/ to match the Velocity layout.\n\n" .
            "Through the console:\n" .
            "  ./bin/php/console exp:solr status --allow-root-user\n" .
            "  ./bin/php/console exp:solr start --allow-root-user" ),
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true ) );

        $script->startup();

        $options = $script->getOptions( '[json]', '[command]',
            array( 'json' => 'Report as JSON, for a caller that is not a person' ) );
        $script->initialize();

        $asJson = !empty( $options['json'] );

        $verbs = array( 'status', 'start', 'stop', 'restart', 'ping' );
        $verb = isset( $options['arguments'][0] ) ? strtolower( trim( $options['arguments'][0] ) ) : 'status';
        if ( !in_array( $verb, $verbs, true ) )
        {
            $cli->error( "solr: unknown command '$verb'. Try one of: " . implode( ', ', $verbs ) );
            $script->shutdown( 1 );
        }

        // Exit codes: a caller can tell the states apart.
        $EXIT_OK = 0;               // did what was asked / running
        $EXIT_FAIL = 1;             // configured, but the action failed / not answering
        $EXIT_NOT_CONFIGURED = 3;   // Solr is not installed or not enabled here
        $EXIT_REMOTE = 4;

        $d = solrDetect();

        // ── not configured ────────────────────────────────────────────────────────
        if ( !$d['configured'] )
        {
            $why = !$d['enabled']
                ? '[SolrSettings] Enabled is not true'
                : 'neither [SolrSettings] Url nor BinaryPath is set';
            $lines = array(
                'Solr is not installed or configured on this installation.',
                '  reason      : ' . $why,
                '  search now  : the built-in eZ index (bin/php/updatesearchindex.php)',
                '  ezfind ext  : ' . ( $d['hasEzfind'] ? 'present' : 'not installed' ),
                '  to enable   : set [SolrSettings] Enabled=true in settings/solr.ini and',
                '                either Url=<running Solr base URL> or BinaryPath=<solr binary>',
                '                (with Host, Port, DataDir for a local instance).',
            );
            if ( $asJson )
                solrFinish( $cli, $script, false, 'not configured',
                    array( 'configured' => false, 'detect' => $d ), true,
                    $verb === 'start' ? $EXIT_NOT_CONFIGURED : ( $verb === 'status' ? $EXIT_OK : $EXIT_NOT_CONFIGURED ) );
            $cli->output( '' );
            foreach ( $lines as $l ) $cli->output( '  ' . $l );
            $cli->output( '' );
            // status reports cleanly (0); the acting verbs make the missing server an error.
            $script->shutdown( $verb === 'status' ? $EXIT_OK : $EXIT_NOT_CONFIGURED );
        }

        // ── configured: remote (Url) ────────────────────────────────────────────────
        if ( $d['mode'] === 'remote' )
        {
            if ( $verb === 'status' || $verb === 'ping' )
            {
                $p = solrPing( $d );
                solrFinish( $cli, $script, $p['reachable'],
                    ( $p['reachable'] ? 'remote Solr is answering at ' : 'remote Solr is NOT answering at ' ) . $p['url']
                      . ' (' . $p['note'] . ')',
                    array( 'mode' => 'remote', 'ping' => $p ), $asJson,
                    $p['reachable'] ? $EXIT_OK : $EXIT_FAIL );
            }
            // start / stop / restart of a remote instance is not this command's to do.
            solrFinish( $cli, $script, false,
                "remote Solr at " . solrBaseUrl( $d ) . " is managed externally; cannot $verb it from here"
                  . ' (use status/ping to check it)',
                array( 'mode' => 'remote' ), $asJson, $EXIT_REMOTE );
        }

        // ── configured: local (BinaryPath) ──────────────────────────────────────────
        $binary = $d['binary'];
        if ( !is_file( $binary ) || !is_executable( $binary ) )
        {
            solrFinish( $cli, $script, false,
                "the Solr binary [SolrSettings] BinaryPath=$binary is missing or not executable",
                array( 'mode' => 'local', 'binary' => $binary ), $asJson, $EXIT_FAIL );
        }

        $logDir = solrEnsureDirs();

        if ( $verb === 'ping' )
        {
            $p = solrPing( $d );
            solrFinish( $cli, $script, $p['reachable'],
                ( $p['reachable'] ? 'Solr is answering at ' : 'Solr is NOT answering at ' ) . $p['url'] . ' (' . $p['note'] . ')',
                array( 'mode' => 'local', 'ping' => $p ), $asJson, $p['reachable'] ? $EXIT_OK : $EXIT_FAIL );
        }

        if ( $verb === 'status' )
        {
            $portOpen = solrPortOpen();
            $p = $portOpen ? solrPing( $d ) : array( 'reachable' => false, 'url' => solrBaseUrl( $d ), 'note' => 'port closed' );
            $running = $portOpen && $p['reachable'];
            solrFinish( $cli, $script, $running,
                $running ? 'local Solr is running at ' . $p['url'] : 'local Solr is stopped (' . $p['note'] . ')',
                array( 'mode' => 'local', 'running' => $running, 'binary' => $binary, 'port' => (int)solrSetting( 'Port', 8983 ),
                       'logDir' => $logDir, 'ping' => $p ), $asJson, $running ? $EXIT_OK : $EXIT_FAIL );
        }

        // start / stop / restart — drive Solr's own control binary.
        $action = $verb; // start|stop|restart map 1:1 to Solr's subcommands
        $command = solrLocalCommand( $d, $action );
        $logFile = $logDir . '/solr-' . $action . '.log';

        if ( !$asJson )
            $cli->output( $cli->stylize( 'emphasize', "solr: $action -> $command" ) );

        $full = $command . ' > ' . escapeshellarg( $logFile ) . ' 2>&1';
        $ret = 0;
        $out = array();
        exec( $full, $out, $ret );

        $ok = ( $ret === 0 );
        if ( $ok && in_array( $action, array( 'start', 'restart' ), true ) )
        {
            // Wait for the port to answer, up to StartTimeout seconds.
            $timeout = (int)solrSetting( 'StartTimeout', 60 );
            $deadline = time() + max( 5, $timeout );
            $ok = false;
            while ( time() < $deadline )
            {
                if ( solrPortOpen() ) { $ok = true; break; }
                usleep( 500000 );
            }
        }

        solrFinish( $cli, $script, $ok,
            $ok ? "$action ok (log: " . $logFile . ')'
                : "$action failed (exit $ret; see " . $logFile . ')',
            array( 'mode' => 'local', 'action' => $action, 'exit' => $ret, 'log' => $logFile ), $asJson,
            $ok ? $EXIT_OK : $EXIT_FAIL );
    }
}

}
