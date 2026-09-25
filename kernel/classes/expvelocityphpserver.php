<?php
/**
 * File containing the expVelocityPHPServer class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Velocity's third engine: PHP's own built-in web server (php -S).
 *
 * Chosen with [ServerSettings] Engine=php. Nothing to download and nothing to
 * install -- it is the PHP that runs this script, with the machine's php.ini
 * and extensions -- so it is the quickest way to see an installation in a
 * browser. It is a development server, and the PHP manual says so: no TLS, no
 * HTTP/2, no reload, a request log on the console only. Workers sets
 * PHP_CLI_SERVER_WORKERS, so that many requests are served at once (Linux);
 * each worker process serves request after request, with a clean state for
 * each, as php-fpm does.
 *
 * Requests are routed by bin/php/velocity-router.php, which does what
 * .htaccess_root does: the built-in server reads no .htaccess, and left to
 * itself would serve every file under the root and run every .php file.
 *
 * @package kernel
 */
class expVelocityPHPServer extends expVelocity
{
    /** @var string|null the PHP version of the binary, for status */
    protected $phpVersion = null;

    /**
     * A [PHPServerSettings] value.
     *
     * @param string $variable
     * @param mixed $default
     * @return mixed
     */
    protected function serverSetting( $variable, $default = null )
    {
        return parent::setting( 'PHPServerSettings', $variable, $default );
    }

    /**
     * Port, HTTPSPort, Host, Workers, SpareWorkers and DocumentRoot from
     * [PHPServerSettings] when it sets them, so this engine can run beside
     * the others; [ServerSettings] otherwise.
     */
    protected function setting( $block, $variable, $default = null )
    {
        return $this->engineSetting( 'PHPServerSettings', $block, $variable, $default );
    }

    /**
     * @return string
     */
    public function role()
    {
        return 'development';
    }

    /**
     * @param bool|null $token unused: this engine has no dashboard
     * @param bool|null $remote unused
     * @param bool $panelPassword unused
     * @return array see expVelocity::views()
     */
    public function views( $token = null, $remote = null, $panelPassword = false )
    {
        return array(
            array( '/Q/health', 'text', 'public', 'health check (answered by the router, empty 200)' ),
        );
    }

    /**
     * @return string
     */
    public function engineName()
    {
        return 'php';
    }

    /**
     * @param string $feature
     * @return bool no /etc/vc tree, no certificates
     */
    public function supports( $feature )
    {
        return !in_array( $feature, array( 'layout', 'ssl' ), true );
    }

    /**
     * @return string
     */
    public function pidFile()
    {
        return $this->absolute( $this->serverSetting( 'PidFile', 'var/tmp/velocity-php.pid' ) );
    }

    /**
     * The server's console: its request log and PHP's errors.
     *
     * @return string
     */
    public function logFile()
    {
        return $this->absolute( $this->serverSetting( 'LogFile', 'var/tmp/velocity-php.log' ) );
    }

    /**
     * The PHP binary that runs the server: the one running this script,
     * unless [PHPServerSettings] PHPBinary names another.
     *
     * @return string
     */
    public function binary()
    {
        $binary = trim( (string)$this->serverSetting( 'PHPBinary', '' ) );
        return $binary !== '' ? $this->absolute( $binary ) : PHP_BINARY;
    }

    /**
     * @return string
     */
    public function router()
    {
        return $this->absolute( 'bin/php/velocity-router.php' );
    }

    /**
     * @return string what status reports as the server script
     */
    public function scriptPath()
    {
        return $this->router();
    }

    /**
     * host:port, as php -S takes it; an IPv6 address in brackets.
     *
     * @return string
     */
    protected function listenAddress()
    {
        $host = trim( (string)$this->setting( 'ServerSettings', 'Host', '127.0.0.1' ) );
        if ( $host === '' )
            $host = '127.0.0.1';
        if ( strpos( $host, ':' ) !== false && $host[0] !== '[' )
            $host = '[' . $host . ']';
        return $host . ':' . (int)$this->setting( 'ServerSettings', 'Port', 8088 );
    }

    /**
     * The command line the server is started with: [PHPSettings] IniOptions[]
     * as -d, as for the Qbix server, then -S, the document root and the router.
     *
     * @return array
     */
    public function command()
    {
        $arguments = array( $this->binary() );
        foreach ( (array)$this->setting( 'PHPSettings', 'IniOptions', array() ) as $option )
        {
            $option = trim( (string)$option );
            if ( $option !== '' )
            {
                $arguments[] = '-d';
                $arguments[] = $option;
            }
        }
        $arguments[] = '-S';
        $arguments[] = $this->listenAddress();
        $arguments[] = '-t';
        $arguments[] = $this->absolute( $this->setting( 'ServerSettings', 'DocumentRoot', '' ) );
        $arguments[] = $this->router();

        foreach ( (array)$this->serverSetting( 'ExtraOptions', array() ) as $option )
            if ( trim( (string)$option ) !== '' )
                $arguments[] = (string)$option;

        return $arguments;
    }

    /**
     * The Qbix server's configuration and control options have no meaning here.
     */
    protected function writeServerConfig()
    {
        throw new LogicException( 'the php engine has no configuration file' );
    }

    /**
     * @param string $option
     */
    protected function control( $option )
    {
        throw new LogicException( 'the php engine is controlled by signals' );
    }

    /**
     * The PHP version of the server's binary.
     *
     * @return string
     */
    public function phpVersion()
    {
        if ( $this->phpVersion === null )
        {
            $output = array();
            $code = 1;
            @exec( escapeshellarg( $this->binary() ) . ' -r ' . escapeshellarg( 'echo PHP_VERSION;' ) . ' 2>/dev/null', $output, $code );
            $this->phpVersion = $code === 0 ? trim( (string)( $output[0] ?? '' ) ) : '';
        }
        return $this->phpVersion;
    }

    /**
     * Settings this engine does not act on -- those an installation set.
     *
     * @return array
     */
    public function ignoredSettings()
    {
        $on = function ( $value ) { return $value === 'enabled' || $value === 'true' || $value === true; };
        $notes = array( 'a development server (PHP manual): for production use --engine=frankenphp,'
                      . ' or [ServerSettings] Engine=frankenphp as the default' );

        $host = trim( (string)$this->setting( 'ServerSettings', 'Host', '127.0.0.1' ) );
        if ( !in_array( $host, array( '127.0.0.1', 'localhost', '::1', '[::1]', '' ), true ) )
            $notes[] = "Host=$host: reachable from other machines, which a development server should not be";
        if ( $on( $this->setting( 'HTTPSSettings', 'Enabled', 'false' ) ) )
            $notes[] = 'HTTPSSettings: no TLS on the built-in server, plain HTTP only';
        if ( $on( $this->cacheSetting( 'Enabled', null, 'enabled' ) ) )
            $notes[] = 'CacheSettings: no response cache';
        if ( $on( $this->setting( 'ServerSettings', 'ForkPerRequest', 'disabled' ) ) )
            $notes[] = 'ForkPerRequest: not needed, every request starts from a clean state';
        if ( $this->setting( 'ApplicationSettings', 'KeepGlobals', array() ) )
            $notes[] = 'KeepGlobals: not needed, nothing is kept between requests';
        if ( (int)$this->setting( 'ServerSettings', 'StaticMaxAge', 0 ) > 0 )
            $notes[] = 'StaticMaxAge: the built-in server sends files without a cache lifetime';
        $logs = $this->setting( 'LogSettings', 'Enabled', 'enabled' );
        if ( $on( $logs ) )
            $notes[] = 'LogSettings: the request log goes to ' . $this->logFile();
        if ( PHP_OS_FAMILY !== 'Linux' && (int)$this->setting( 'ServerSettings', 'Workers', 4 ) > 1 )
            $notes[] = 'Workers: PHP_CLI_SERVER_WORKERS works on Linux only; one request at a time here';

        return $notes;
    }

    // ── Process state ────────────────────────────────────────────────────

    /**
     * The server and its workers: every process running the router on this
     * installation's address. With PHP_CLI_SERVER_WORKERS the parent forks the
     * workers, which carry the same command line.
     *
     * @return array
     */
    public function processIDs()
    {
        $needle = '-S ' . $this->listenAddress();
        $router = $this->router();
        $pids = array();
        foreach ( $this->processTable() as $proc )
            if ( strpos( $proc['args'], $needle ) !== false && strpos( $proc['args'], $router ) !== false )
                $pids[] = $proc['pid'];

        return $pids;
    }

    /**
     * @param int $parent
     * @return array
     */
    public function childIDs( $parent )
    {
        $parent = (int)$parent;
        $pids = array();
        foreach ( $this->processTable() as $proc )
            if ( $proc['ppid'] === $parent && in_array( $proc['pid'], $this->processIDs(), true ) )
                $pids[] = $proc['pid'];
        sort( $pids );
        return $pids;
    }

    /**
     * @return bool whether the site answers its health check
     */
    protected function healthy()
    {
        $host = trim( (string)$this->setting( 'ServerSettings', 'Host', '127.0.0.1' ) );
        if ( $host === '' || $host === '0.0.0.0' || $host === '::' )
            $host = '127.0.0.1';
        $socket = @fsockopen( $host, (int)$this->setting( 'ServerSettings', 'Port', 8088 ), $errno, $errstr, 2 );
        if ( !$socket )
            return false;
        stream_set_timeout( $socket, 5 );
        fwrite( $socket, "GET /Q/health HTTP/1.1\r\nHost: $host\r\nConnection: close\r\n\r\n" );
        $status = fgets( $socket );
        fclose( $socket );
        return (bool)preg_match( '#^HTTP/\d(?:\.\d)?\s+200#', (string)$status );
    }

    // ── Verbs ────────────────────────────────────────────────────────────

    /**
     * Start the server, unless it is already running.
     *
     * @return array
     */
    public function start()
    {
        if ( $this->isRunning() )
            return $this->result( false, 'already running', $this->status() );

        if ( !is_file( $this->router() ) )
            return $this->result( false, 'no router at ' . $this->router() );
        if ( $this->phpVersion() === '' )
            return $this->result( false, 'cannot run ' . $this->binary() );

        $taken = $this->listeningPorts();
        if ( $taken )
            return $this->result( false, 'port ' . implode( ', ', $taken ) . ' is already in use by another process'
                . ' (another engine? exp:velocity stop --engine=qbix|frankenphp)', $this->status() );

        $enginePhar = $this->enginePhar();
        if ( $enginePhar !== '' )
        {
            if ( !file_exists( $enginePhar ) )
                return $this->result( false, 'EnginePhar names an archive that does not exist: ' . $enginePhar
                    . ' -- build it first, or set EnginePhar=disabled', $this->status() );
            $ready = $this->ensureEngineArchive();
            if ( $ready !== true )
                return $this->result( false, $ready, $this->status() );
        }

        $log = $this->logFile();
        foreach ( array( dirname( $log ), dirname( $this->pidFile() ) ) as $directory )
            if ( !is_dir( $directory ) )
                eZDir::mkdir( $directory, false, true );

        $workers = (int)$this->setting( 'ServerSettings', 'Workers', 4 );
        $environment = array(
            'EXP_VELOCITY_ROOT'         => $this->absolute( $this->setting( 'ServerSettings', 'DocumentRoot', '' ) ),
            'EXP_VELOCITY_STATIC_PATHS' => self::STATIC_PATHS,
            'EXP_VELOCITY_NEVER_STATIC' => self::NEVER_STATIC,
            'EXP_VELOCITY_FOLLOW_SYMLINKS' => $this->followsSymlinks() ? '1' : '0',
            // For Setup > System information, as the frankenphp engine sets it.
            'EXP_VELOCITY_ENGINE'       => 'PHP ' . $this->phpVersion() . ' built-in web server',
        );
        if ( $workers > 1 )
            $environment['PHP_CLI_SERVER_WORKERS'] = (string)$workers;
        if ( $enginePhar !== '' )
            $environment['EXP_ENGINE_PHAR'] = $enginePhar;
        if ( trim( (string)getenv( 'PATH' ) ) === '' )
            $environment['PATH'] = self::DEFAULT_PATH;

        $prefix = '';
        foreach ( $environment as $name => $value )
            $prefix .= $name . '=' . escapeshellarg( $value ) . ' ';

        @unlink( $this->pidFile() );
        $setsid = $this->findExecutable( 'setsid' );
        // The whole subshell redirected and replaced by the server, so it
        // holds none of this process's output and its pid is the server's.
        $command = '( cd ' . escapeshellarg( $this->rootDir ) . ' && ' . $prefix . 'exec '
                 . ( $setsid !== false ? escapeshellarg( $setsid ) . ' ' : '' )
                 . implode( ' ', array_map( 'escapeshellarg', $this->command() ) )
                 . ' ) > ' . escapeshellarg( $log ) . ' 2>&1 < /dev/null & echo $!';
        $output = array();
        @exec( $command, $output );
        $pid = (int)trim( (string)( $output[0] ?? '' ) );
        if ( $pid > 0 )
            @file_put_contents( $this->pidFile(), $pid . "\n" );

        $notes = $this->ignoredSettings();
        $suffix = '; ' . implode( '; ', $notes );

        $started = microtime( true );
        $deadline = $started + 20;
        while ( microtime( true ) < $deadline )
        {
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
            if ( $this->listeningPorts() && $this->healthy() )
                return $this->result( true, 'started (PHP ' . $this->phpVersion() . ' built-in web server, '
                    . ( $workers > 1 ? $workers . ' workers' : 'one request at a time' ) . ')' . $suffix, $this->status() );

            if ( microtime( true ) - $started > 1 && !$this->isRunning() )
            {
                $lines = @file( $log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) ?: array();
                return $this->result( false, 'exited at startup: ' . trim( implode( ' | ', array_slice( $lines, -3 ) ) )
                    . ' -- see ' . $log, $this->status() );
            }
        }

        if ( $this->isRunning() )
            return $this->result( true, 'started, but the site does not answer yet' . $suffix, $this->status() );

        return $this->result( false, 'did not start; see ' . $log, $this->status() );
    }

    /**
     * Stop the server: SIGTERM to the parent, which takes its workers down,
     * then to anything left.
     *
     * @param int $signal
     * @return array
     */
    public function stop( $signal = null )
    {
        if ( $signal === null )
            $signal = defined( 'SIGTERM' ) ? SIGTERM : 15;

        $pids = $this->processIDs();
        if ( !$pids )
        {
            @unlink( $this->pidFile() );
            return $this->result( true, 'not running' );
        }

        $parent = $this->parentID();
        if ( $parent && in_array( $parent, $pids, true ) )
        {
            $this->signal( $parent, $signal );
            usleep( (int)( self::POLL_INTERVAL * 2 * 1000000 ) );
        }
        foreach ( $this->processIDs() as $pid )
            $this->signal( $pid, $signal );

        $timeout = (float)$this->setting( 'ControlSettings', 'StopTimeout', 15 );
        $deadline = microtime( true ) + ( $timeout > 0 ? $timeout : 15 );
        while ( microtime( true ) < $deadline )
        {
            if ( !$this->isRunning() )
            {
                @unlink( $this->pidFile() );
                return $this->result( true, 'stopped' );
            }
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }

        return $this->result( false, 'still running after ' . $timeout . 's; try kill', $this->status() );
    }

    /**
     * The built-in server cannot reload, so this restarts it -- and says so.
     *
     * @return array
     */
    public function graceful()
    {
        $restarted = $this->restart();
        return $this->result( $restarted['ok'], 'the built-in server cannot reload; restarted: ' . $restarted['message'],
                              $restarted['data'] );
    }

    /**
     * @return array
     */
    public function restart()
    {
        $ready = $this->ensureEngineArchive();
        if ( $ready !== true )
            return $this->result( false, $ready . ' -- the running server was left as it is', $this->status() );

        $stopped = $this->stop();
        if ( !$stopped['ok'] && $this->isRunning() )
            return $this->result( false, 'could not stop: ' . $stopped['message'], $this->status() );

        return $this->start();
    }

    /**
     * @return array
     */
    public function kill()
    {
        $pids = $this->processIDs();
        if ( !$pids )
        {
            @unlink( $this->pidFile() );
            return $this->result( true, 'not running' );
        }

        $signal = defined( 'SIGKILL' ) ? SIGKILL : 9;
        foreach ( $pids as $pid )
            $this->signal( $pid, $signal );
        usleep( (int)( self::POLL_INTERVAL * 2 * 1000000 ) );
        @unlink( $this->pidFile() );

        $left = $this->processIDs();
        if ( $left )
            return $this->result( false, count( $left ) . ' process(es) survived SIGKILL', $this->status() );
        return $this->result( true, 'killed ' . count( $pids ) . ' process(es)' );
    }

    /**
     * @param array $options
     * @return array
     */
    public function install( array $options = array() )
    {
        return $this->result( true, 'the php engine is the PHP that runs this script (' . $this->binary()
            . ', ' . $this->phpVersion() . '); nothing to install' );
    }

    /**
     * @return array
     */
    public function clearCache()
    {
        return $this->result( true, 'nothing to clear: the php engine runs without a response cache' );
    }

    /**
     * @return bool
     */
    public function engineCtl()
    {
        return false;
    }

    /**
     * @throws LogicException
     */
    public function layout()
    {
        throw new LogicException( 'the /etc/vc configuration tree belongs to the qbix engine' );
    }

    /**
     * @return array
     */
    public function migrateLayout()
    {
        return $this->result( false, 'the /etc/vc configuration tree belongs to the qbix engine; the php engine has no configuration file' );
    }

    /**
     * @return array name => path
     */
    public function assets()
    {
        return array(
            'binary'        => $this->binary(),
            'router'        => $this->router(),
            'pidFile'       => $this->pidFile(),
            'serverLog'     => $this->logFile(),
            'engineArchive' => $this->enginePhar() !== '' ? $this->enginePhar() : null,
        );
    }

    /**
     * @param array $args
     * @return int
     */
    public function ctl( array $args )
    {
        fwrite( STDERR, "the php engine has no control program; use start, stop, restart and status\n" );
        return 1;
    }

    /**
     * @return array
     */
    public function status()
    {
        $status = parent::status();
        $workers = (int)$this->setting( 'ServerSettings', 'Workers', 4 );
        return array_merge( $status, array(
            'server'       => 'php',
            'version'      => $this->phpVersion() !== '' ? 'PHP ' . $this->phpVersion() . ' built-in web server' : '',
            'binary'       => $this->binary(),
            'binarySource' => trim( (string)$this->serverSetting( 'PHPBinary', '' ) ) !== '' ? 'PHPBinary' : 'this PHP',
            'threads'      => $workers > 1 ? $workers : 1,
            'caddyfile'    => $this->router(),
            'admin'        => 'none',
            'adminReachable' => true,
            'notes'        => $this->ignoredSettings(),
        ) );
    }
}
