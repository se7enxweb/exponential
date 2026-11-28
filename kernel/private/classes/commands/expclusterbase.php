<?php
/**
 * File containing the base of the commands that run on the hosts of settings/cluster.ini over ssh: exp:host:run,
 * exp:cluster:run, exp:cluster:cache, exp:cluster:deploy, exp:cluster:status and exp:cluster:hosts
 * (doc/eng-us/features/6.0/cluster-host-commands.md).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

abstract class Expclusterbase extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_FAILED = 1;
    const EXIT_USAGE = 2;

    /** The machine output (--json). */
    const SCHEMA = 'exp-cluster-run/1';

    /**
     * The command's work, in each command: it reads its options, builds the steps for the hosts and hands them to
     * execute().
     */
    abstract public function run();

    /** The options every command that runs on hosts has. */
    const RUN_OPTIONS = '[dry-run][json][parallel][sequential][timeout:]';

    /** Their help texts. */
    const RUN_HELP = array(
        'dry-run' => 'Print the command each host would run, and run nothing',
        'json' => 'Report as one JSON object (' . self::SCHEMA . ')',
        'parallel' => 'Run on every host at once (default: [ClusterSettings] Mode)',
        'sequential' => 'Run on one host after the other',
        'timeout' => 'Seconds one host may take (default: [ClusterSettings] Timeout)',
    );

    /** The options that pick hosts. */
    const HOST_OPTIONS = '[hosts:][nodes:][all]';

    const HOST_HELP = array(
        'hosts' => 'Only these hosts of [ClusterSettings] HostList[] (comma list)',
        'nodes' => 'The same as --hosts',
        'all' => 'Every host (the default)',
    );

    /** @var string[] the words after --, for the command on the hosts */
    protected $remoteWords = array();

    /** @var bool whether the command line had a -- */
    protected $hasSeparator = false;

    /**
     * Splits a command line at the first --: the words before it are this command's, the words after it are the
     * command for the hosts.
     *
     * @param string[] $argv without the program name
     * @return array( own words, remote words, whether there was a -- )
     */
    public static function splitArguments( array $argv )
    {
        $at = array_search( '--', $argv, true );
        if ( $at === false )
            return array( array_values( $argv ), array(), false );
        return array( array_values( array_slice( $argv, 0, $at ) ), array_values( array_slice( $argv, $at + 1 ) ), true );
    }

    /**
     * startup() for the words before --; the words after it are kept in $remoteWords.
     *
     * @param string $config
     * @param string $argumentConfig
     * @param array $help
     * @return array the options
     */
    protected function startupSplit( $config, $argumentConfig, array $help )
    {
        $argv = isset( $GLOBALS['argv'] ) && is_array( $GLOBALS['argv'] ) ? array_slice( $GLOBALS['argv'], 1 ) : array();
        list( $own, $this->remoteWords, $this->hasSeparator ) = self::splitArguments( $argv );
        return $this->startup( $config, $argumentConfig, $help, $own );
    }

    /**
     * The hosts of cluster.ini; settings with errors end the command (exit 2).
     *
     * @return \expClusterHosts
     */
    protected function loadHosts()
    {
        $hosts = \expClusterHosts::fromINI();
        if ( $hosts->errors() )
        {
            foreach ( $hosts->errors() as $error )
                $this->cli()->error( "FAIL cluster.ini: $error" );
            $this->shutdown( self::EXIT_USAGE );
        }
        return $hosts;
    }

    /**
     * The hosts picked by --hosts / --nodes; every host without them. No host, or an unknown name, ends the command.
     *
     * @param \expClusterHosts $hosts
     * @param array $options
     * @return array name => host
     */
    protected function selectHosts( \expClusterHosts $hosts, array $options )
    {
        $names = !empty( $options['hosts'] ) ? (string)$options['hosts'] : ( !empty( $options['nodes'] ) ? (string)$options['nodes'] : '' );
        $selected = $hosts->select( $names, $unknown );
        if ( $unknown )
            $this->fail( 'Not a host of [ClusterSettings] HostList[]: ' . implode( ', ', $unknown ) . ' (exp:cluster:hosts lists them)', self::EXIT_USAGE );
        if ( !$selected )
            $this->fail( 'No hosts: list them in settings/override/cluster.ini.append.php, [ClusterSettings] HostList[] and a [Host_<name>] block each', self::EXIT_USAGE );
        return $selected;
    }

    /**
     * Runs the steps on the hosts (or prints them with --dry-run), reports, logs, and ends the command with 0 when
     * every host passed, 1 otherwise.
     *
     * @param \expClusterHosts $hosts
     * @param array $selected name => host
     * @param array $steps one list for every host, or name => list
     * @param array $options this command's options
     * @param string $what the command line that is run, for the report and the log
     */
    protected function execute( \expClusterHosts $hosts, array $selected, array $steps, array $options, $what )
    {
        $settings = $hosts->settings();
        if ( !empty( $options['timeout'] ) )
        {
            if ( !ctype_digit( (string)$options['timeout'] ) || (int)$options['timeout'] < 1 )
                $this->fail( '--timeout is a number of seconds', self::EXIT_USAGE );
            $settings['timeout'] = (int)$options['timeout'];
        }
        if ( !empty( $options['parallel'] ) && !empty( $options['sequential'] ) )
            $this->fail( '--parallel and --sequential exclude each other', self::EXIT_USAGE );
        $parallel = !empty( $options['parallel'] ) || ( empty( $options['sequential'] ) && $settings['mode'] === 'parallel' );
        $remote = new \expClusterRemote( $settings );
        $json = !empty( $options['json'] );
        $out = $this->cli();

        if ( !empty( $options['dry-run'] ) )
        {
            $plan = $remote->plan( $selected, $steps );
            if ( $json )
            {
                $out->output( self::json( array( 'schema' => self::SCHEMA, 'mode' => 'plan', 'command' => $what, 'parallel' => $parallel, 'hosts' => $plan ) ) );
                $this->shutdown( self::EXIT_OK );
                return;
            }
            $out->output( 'Dry run of ' . $what . ' on ' . count( $plan ) . ' host(s), ' . ( $parallel ? 'all at once' : 'one after the other' ) . ':' );
            foreach ( $plan as $name => $lines )
            {
                $out->output( "== $name" );
                foreach ( $lines as $line )
                    $out->output( '   ' . $line['label'] . ":\n     " . $line['command'] );
            }
            $out->output( 'Nothing was run.' );
            $this->shutdown( self::EXIT_OK );
            return;
        }

        $quiet = $this->isQuiet() || $json;
        if ( !$quiet )
            $out->output( 'Running ' . $what . ' on ' . implode( ', ', array_keys( $selected ) ) . ( $parallel ? ' (all at once)' : '' ) );
        $results = $remote->run( $selected, $steps, $parallel, function ( $name, array $result ) use ( $quiet ) {
            if ( !$quiet )
                $this->printHost( $name, $result );
        } );
        $summary = \expClusterRemote::summary( $results );
        $this->record( $what, $results, $settings['log_file'] );
        if ( $json )
            $out->output( self::json( array( 'schema' => self::SCHEMA, 'mode' => 'run', 'command' => $what, 'parallel' => $parallel,
                                             'ok' => $summary['exit'] === 0, 'passed' => $summary['passed'], 'failed' => $summary['failed'],
                                             'hosts' => $results ) ) );
        else
        {
            if ( !$this->isQuiet() )
                $out->output( '' );
            foreach ( $summary['lines'] as $line )
                $out->output( $line );
        }
        $this->shutdown( $summary['exit'] );
    }

    /**
     * One host's output, as soon as it is done.
     *
     * @param string $name
     * @param array $result
     */
    protected function printHost( $name, array $result )
    {
        $out = $this->cli();
        $out->output( '' );
        $out->output( '== ' . $name . ': ' . ( $result['ok'] ? 'PASS' : 'FAIL' ) . sprintf( ' (%.1f s)', $result['ms'] / 1000 ) );
        foreach ( $result['steps'] as $step )
        {
            $out->output( '-- ' . $step['label'] . ': ' . ( $step['exit'] === 0 ? 'PASS' : 'FAIL, exit ' . $step['exit'] ) . sprintf( ' (%.1f s)', $step['ms'] / 1000 ) );
            foreach ( array( 'stdout', 'stderr' ) as $stream )
            {
                $text = rtrim( $step[$stream] );
                if ( $text !== '' )
                    $out->output( preg_replace( '/^/m', '   ', $text ) );
            }
        }
    }

    /**
     * Writes the run to the log (one line per host) and records the audit event system.cluster.run.
     *
     * @param string $what
     * @param array $results
     * @param string $logFile
     */
    protected function record( $what, array $results, $logFile )
    {
        $user = function_exists( 'posix_getpwuid' ) && function_exists( 'posix_geteuid' ) ? posix_getpwuid( posix_geteuid() ) : false;
        $who = is_array( $user ) && isset( $user['name'] ) ? $user['name'] : (string)get_current_user();
        $hosts = array();
        foreach ( $results as $name => $result )
        {
            $hosts[$name] = $result['exit'];
            if ( $logFile !== '' && basename( $logFile ) === $logFile )
                $this->log( sprintf( '%s host=%s exit=%d ms=%d by=%s', $what, $name, $result['exit'], $result['ms'], $who ), $logFile );
        }
        $failed = count( array_filter( $results, function ( $r ) { return !$r['ok']; } ) );
        if ( class_exists( 'expAudit' ) )
            \expAudit::event( 'system.cluster.run', array( 'object' => array( 'type' => 'cluster', 'id' => $what ),
                                                           'after' => array( 'command' => $what, 'hosts' => $hosts, 'by' => $who ),
                                                           'result' => $failed ? 'failed' : 'success' ) );
    }

    /**
     * @param string $message
     * @param int $code
     */
    protected function fail( $message, $code )
    {
        $this->cli()->error( "FAIL $message" );
        $this->shutdown( $code );
    }

    /** @return string pretty JSON */
    protected static function json( array $data )
    {
        return (string)json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
    }
}

}
?>
