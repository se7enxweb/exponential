<?php
/**
 * File containing the expClusterRemote class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Runs steps on the hosts of expClusterHosts over the system's ssh (or with sh -c for a host whose Ssh is "local").
 *
 * A step is an array:
 *   - label:   what it does, for the report
 *   - console: the arguments of a console command (bin/php/console <arguments>), e.g. array( 'exp:ezcache', '--clear-all' )
 *   - shell:   instead of console, a script for sh -c in the installation's root; it gets the PHP binary as $1 and
 *              --allow-root-user (or an empty string) as $2
 *   - as:      'user' (the default: the host's User=, else the ssh login), 'deploy' (DeployUser=, else the ssh login)
 *              or 'login' (always the ssh login)
 *
 * The steps of one host run one after the other; the first that fails ends that host's list. Hosts run one after the
 * other, or all at once (parallel), each with a time limit. Nothing is interactive: ssh runs with BatchMode=yes and
 * no standard input (-n), sudo with -n. No shell runs on this side: proc_open() gets an argument array; on the host
 * the command line is quoted for POSIX sh by quote().
 *
 * A test replaces the transport with setRunner( callable( array $argv, array $host, array $step, $timeout ) ),
 * which returns array( exit, stdout, stderr ).
 *
 * @since 6.0.15
 * @package kernel
 */
class expClusterRemote
{
    /** @var callable|null */
    protected static $runner = null;

    /** @var array [ClusterSettings] of expClusterHosts::settings() */
    protected $settings;

    /**
     * @param array $settings expClusterHosts::settings()
     */
    public function __construct( array $settings )
    {
        $this->settings = $settings + array( 'connect_timeout' => 20, 'timeout' => 1800, 'ssh_options' => array() );
    }

    /**
     * Replaces the transport (tests: a fake ssh). null puts proc_open() back.
     *
     * @param callable|null $runner
     */
    public static function setRunner( $runner )
    {
        self::$runner = $runner;
    }

    /**
     * Quotes a word for POSIX sh: words of safe characters stay as they are, every other is put in single quotes.
     *
     * @param string $word
     * @return string
     */
    public static function quote( $word )
    {
        $word = (string)$word;
        if ( $word !== '' && preg_match( '#^[A-Za-z0-9_@%+=:,./-]+$#', $word ) )
            return $word;
        return "'" . str_replace( "'", "'\\''", $word ) . "'";
    }

    /**
     * @param string[] $words
     * @return string the words quoted and joined with spaces
     */
    public static function quoteAll( array $words )
    {
        return implode( ' ', array_map( array( __CLASS__, 'quote' ), $words ) );
    }

    /**
     * The user a step runs as on the host: '' for the ssh login.
     *
     * @param array $host
     * @param array $step
     * @return string
     */
    public static function runAs( array $host, array $step )
    {
        $as = isset( $step['as'] ) ? $step['as'] : 'user';
        if ( $as === 'login' )
            return '';
        if ( $as === 'deploy' )
            return $host['deploy_user'];
        return $host['user'];
    }

    /**
     * The command line for sh on the host: change into the root, become the user, run the step.
     *
     * @param array $host
     * @param array $step
     * @return string
     */
    public static function remoteLine( array $host, array $step )
    {
        $prefix = array();
        $user = self::runAs( $host, $step );
        if ( $user !== '' )
            $prefix = array( 'sudo', '-n', '-H', '-u', $user, '--' );
        $allowRoot = !empty( $host['allow_root_user'] ) ? '--allow-root-user' : '';
        if ( isset( $step['shell'] ) )
            $words = array_merge( $prefix, array( 'sh', '-c', (string)$step['shell'], 'sh', $host['php'], $allowRoot ) );
        else
        {
            $words = array_merge( $prefix, array( $host['php'], 'bin/php/console' ), array_map( 'strval', $step['console'] ) );
            if ( $allowRoot !== '' && !in_array( '--allow-root-user', $words, true ) )
                $words[] = $allowRoot;
        }
        return 'cd ' . self::quote( $host['root'] ) . ' && exec ' . self::quoteAll( $words );
    }

    /**
     * The program and its arguments this side runs for a step: ssh with the remote line, or sh -c for a local host.
     *
     * @param array $host
     * @param array $step
     * @return string[]
     */
    public function argv( array $host, array $step )
    {
        $line = self::remoteLine( $host, $step );
        if ( $host['ssh'] === expClusterHosts::LOCAL )
            return array( 'sh', '-c', $line );
        $argv = array( 'ssh', '-n', '-T', '-o', 'BatchMode=yes', '-o', 'ConnectTimeout=' . (int)$this->settings['connect_timeout'] );
        if ( !empty( $host['port'] ) )
        {
            $argv[] = '-p';
            $argv[] = (string)(int)$host['port'];
        }
        foreach ( $this->settings['ssh_options'] as $option )
        {
            $argv[] = '-o';
            $argv[] = $option;
        }
        $argv[] = '--';
        $argv[] = $host['ssh'];
        $argv[] = $line;
        return $argv;
    }

    /**
     * The plan of a run: for each host, each step's label and command line on this side, nothing run.
     *
     * @param array $hosts name => host
     * @param array $steps name => list of steps, or one list for every host
     * @return array name => list of array( label, command )
     */
    public function plan( array $hosts, array $steps )
    {
        $plan = array();
        foreach ( $hosts as $name => $host )
        {
            $plan[$name] = array();
            foreach ( self::stepsFor( $steps, $name ) as $step )
                $plan[$name][] = array( 'label' => $step['label'], 'command' => self::quoteAll( $this->argv( $host, $step ) ) );
        }
        return $plan;
    }

    /**
     * Runs the steps on the hosts.
     *
     * @param array $hosts name => host
     * @param array $steps name => list of steps, or one list for every host
     * @param bool $parallel all hosts at once
     * @param callable|null $onHost called with ( name, result ) as soon as a host is done
     * @return array name => array( host, ok, exit, ms, steps => list of array( label, exit, stdout, stderr, ms ) )
     */
    public function run( array $hosts, array $steps, $parallel = false, $onHost = null )
    {
        $timeout = (int)$this->settings['timeout'];
        $results = array();
        if ( !$parallel || self::$runner !== null || count( $hosts ) < 2 )
        {
            foreach ( $hosts as $name => $host )
            {
                $results[$name] = $this->runHost( $host, self::stepsFor( $steps, $name ), $timeout );
                if ( $onHost )
                    call_user_func( $onHost, $name, $results[$name] );
            }
            return $results;
        }
        return $this->runParallel( $hosts, $steps, $timeout, $onHost );
    }

    /**
     * The result of a run as PASS/FAIL lines and the exit code: 0 when every host passed, 1 otherwise.
     *
     * @param array $results of run()
     * @return array( lines => string[], exit => int, passed => int, failed => int )
     */
    public static function summary( array $results )
    {
        $lines = array();
        $failed = 0;
        foreach ( $results as $name => $result )
        {
            $seconds = sprintf( '%.1f s', $result['ms'] / 1000 );
            if ( $result['ok'] )
            {
                $lines[] = sprintf( 'PASS %-20s %d step(s), %s', $name, count( $result['steps'] ), $seconds );
                continue;
            }
            $failed++;
            $last = $result['steps'] ? end( $result['steps'] ) : array( 'label' => 'start', 'exit' => $result['exit'], 'stderr' => '' );
            $why = $last['exit'] === -1 ? 'timed out or could not start' : 'exit ' . $last['exit'];
            if ( $last['exit'] === 255 )
                $why .= ' (ssh could not connect or log in)';
            $lines[] = sprintf( 'FAIL %-20s %s: %s, %s', $name, $last['label'], $why, $seconds );
        }
        $passed = count( $results ) - $failed;
        $lines[] = ( $failed ? 'FAIL' : 'PASS' ) . " $passed of " . count( $results ) . ' host(s) passed';
        return array( 'lines' => $lines, 'exit' => $failed || !$results ? 1 : 0, 'passed' => $passed, 'failed' => $failed );
    }

    /** The steps of one host: one list for every host, or name => list. */
    protected static function stepsFor( array $steps, $name )
    {
        if ( !$steps || array_keys( $steps ) === range( 0, count( $steps ) - 1 ) )
            return $steps;
        return isset( $steps[$name] ) && is_array( $steps[$name] ) ? array_values( $steps[$name] ) : array();
    }

    /**
     * The steps of one host, one after the other, the first failure ends them.
     *
     * @param array $host
     * @param array $steps
     * @param int $timeout seconds for all of them
     * @return array
     */
    protected function runHost( array $host, array $steps, $timeout )
    {
        $started = microtime( true );
        $result = array( 'host' => $host['name'], 'ok' => true, 'exit' => 0, 'ms' => 0, 'steps' => array() );
        foreach ( $steps as $step )
        {
            $left = max( 1, (int)ceil( $timeout - ( microtime( true ) - $started ) ) );
            $stepStarted = microtime( true );
            $argv = $this->argv( $host, $step );
            if ( self::$runner !== null )
                $out = call_user_func( self::$runner, $argv, $host, $step, $left );
            else
                $out = self::execute( $argv, $left );
            $done = array( 'label' => $step['label'], 'exit' => (int)$out[0], 'stdout' => (string)$out[1], 'stderr' => (string)$out[2],
                           'ms' => (int)round( ( microtime( true ) - $stepStarted ) * 1000 ) );
            $result['steps'][] = $done;
            if ( $done['exit'] !== 0 )
            {
                $result['ok'] = false;
                $result['exit'] = $done['exit'];
                break;
            }
        }
        $result['ms'] = (int)round( ( microtime( true ) - $started ) * 1000 );
        return $result;
    }

    /**
     * Every host at once: one running process per host, polled; each host's next step starts when its last one ended.
     */
    protected function runParallel( array $hosts, array $steps, $timeout, $onHost )
    {
        $state = array();
        $results = array();
        foreach ( $hosts as $name => $host )
        {
            $state[$name] = array( 'host' => $host, 'steps' => array_values( self::stepsFor( $steps, $name ) ), 'index' => -1, 'proc' => null, 'pipes' => array(),
                                   'started' => microtime( true ), 'step_started' => 0.0 );
            $results[$name] = array( 'host' => $name, 'ok' => true, 'exit' => 0, 'ms' => 0, 'steps' => array() );
            if ( !$this->startNext( $name, $state, $results ) )
                $this->finish( $name, $state, $results, $onHost );
        }
        while ( $state )
        {
            $read = array();
            foreach ( $state as $s )
                foreach ( $s['pipes'] as $pipe )
                    if ( is_resource( $pipe ) && !feof( $pipe ) )
                        $read[] = $pipe;
            if ( $read )
            {
                $write = $except = null;
                @stream_select( $read, $write, $except, 0, 200000 );
            }
            else
                usleep( 50000 );
            foreach ( array_keys( $state ) as $name )
            {
                $s =& $state[$name];
                $index = count( $results[$name]['steps'] ) - 1;
                foreach ( array( 1 => 'stdout', 2 => 'stderr' ) as $n => $key )
                    if ( isset( $s['pipes'][$n] ) && is_resource( $s['pipes'][$n] ) )
                        $results[$name]['steps'][$index][$key] .= (string)stream_get_contents( $s['pipes'][$n] );
                $status = proc_get_status( $s['proc'] );
                $over = microtime( true ) - $s['started'] > $timeout;
                if ( $status['running'] && !$over )
                {
                    unset( $s );
                    continue;
                }
                if ( $status['running'] )
                {
                    proc_terminate( $s['proc'], 9 );
                    $results[$name]['steps'][$index]['stderr'] .= "\ntimed out after {$timeout} s";
                    $exit = -1;
                }
                else
                    $exit = (int)$status['exitcode'];
                foreach ( array( 1 => 'stdout', 2 => 'stderr' ) as $n => $key )
                    if ( isset( $s['pipes'][$n] ) && is_resource( $s['pipes'][$n] ) )
                        $results[$name]['steps'][$index][$key] .= (string)stream_get_contents( $s['pipes'][$n] );
                self::close( $s );
                $results[$name]['steps'][$index]['exit'] = $exit;
                $results[$name]['steps'][$index]['ms'] = (int)round( ( microtime( true ) - $s['step_started'] ) * 1000 );
                unset( $s );
                if ( $exit !== 0 )
                {
                    $results[$name]['ok'] = false;
                    $results[$name]['exit'] = $exit;
                    $this->finish( $name, $state, $results, $onHost );
                }
                else if ( !$this->startNext( $name, $state, $results ) )
                    $this->finish( $name, $state, $results, $onHost );
            }
        }
        return $results;
    }

    /** Starts the next step of a host in parallel mode; false when none is left or it could not start. */
    protected function startNext( $name, array &$state, array &$results )
    {
        $s =& $state[$name];
        if ( ++$s['index'] >= count( $s['steps'] ) )
            return false;
        $step = $s['steps'][$s['index']];
        $s['step_started'] = microtime( true );
        $results[$name]['steps'][] = array( 'label' => $step['label'], 'exit' => 0, 'stdout' => '', 'stderr' => '', 'ms' => 0 );
        $proc = self::open( $this->argv( $s['host'], $step ), $pipes );
        if ( !is_resource( $proc ) )
        {
            $index = count( $results[$name]['steps'] ) - 1;
            $results[$name]['steps'][$index]['exit'] = -1;
            $results[$name]['steps'][$index]['stderr'] = 'could not start ' . $this->argv( $s['host'], $step )[0];
            $results[$name]['ok'] = false;
            $results[$name]['exit'] = -1;
            return false;
        }
        $s['proc'] = $proc;
        $s['pipes'] = $pipes;
        return true;
    }

    /** A host is done: its time, the callback, out of the state. */
    protected function finish( $name, array &$state, array &$results, $onHost )
    {
        $results[$name]['ms'] = (int)round( ( microtime( true ) - $state[$name]['started'] ) * 1000 );
        unset( $state[$name] );
        if ( $onHost )
            call_user_func( $onHost, $name, $results[$name] );
    }

    /**
     * Starts a process without a shell; its standard input is closed at once.
     *
     * @param string[] $argv
     * @param array $pipes gets 1 => stdout, 2 => stderr, non-blocking
     * @return resource|false
     */
    protected static function open( array $argv, &$pipes )
    {
        $spec = array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) );
        $proc = @proc_open( $argv, $spec, $all );
        if ( !is_resource( $proc ) )
            return false;
        fclose( $all[0] );
        stream_set_blocking( $all[1], false );
        stream_set_blocking( $all[2], false );
        $pipes = array( 1 => $all[1], 2 => $all[2] );
        return $proc;
    }

    /** Closes the pipes and the process of a host's running step. */
    protected static function close( array &$s )
    {
        foreach ( $s['pipes'] as $pipe )
            if ( is_resource( $pipe ) )
                fclose( $pipe );
        $s['pipes'] = array();
        if ( is_resource( $s['proc'] ) )
            proc_close( $s['proc'] );
        $s['proc'] = null;
    }

    /**
     * Runs one process to its end or the time limit.
     *
     * @param string[] $argv
     * @param int $timeout seconds
     * @return array( exit, stdout, stderr ); exit -1 when it timed out or did not start
     */
    protected static function execute( array $argv, $timeout )
    {
        $proc = self::open( $argv, $pipes );
        if ( !is_resource( $proc ) )
            return array( -1, '', 'could not start ' . $argv[0] );
        $s = array( 'proc' => $proc, 'pipes' => $pipes );
        $out = array( 1 => '', 2 => '' );
        $started = microtime( true );
        while ( true )
        {
            $read = array();
            foreach ( $pipes as $pipe )
                if ( is_resource( $pipe ) && !feof( $pipe ) )
                    $read[] = $pipe;
            if ( $read )
            {
                $write = $except = null;
                @stream_select( $read, $write, $except, 0, 200000 );
            }
            else
                usleep( 50000 );
            foreach ( $pipes as $n => $pipe )
                $out[$n] .= (string)stream_get_contents( $pipe );
            $status = proc_get_status( $proc );
            if ( !$status['running'] )
            {
                foreach ( $pipes as $n => $pipe )
                    $out[$n] .= (string)stream_get_contents( $pipe );
                self::close( $s );
                return array( (int)$status['exitcode'], $out[1], $out[2] );
            }
            if ( microtime( true ) - $started > $timeout )
            {
                proc_terminate( $proc, 9 );
                self::close( $s );
                return array( -1, $out[1], $out[2] . "\ntimed out after {$timeout} s" );
            }
        }
    }
}
