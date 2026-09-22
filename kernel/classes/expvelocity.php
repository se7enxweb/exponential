<?php
/**
 * File containing the expVelocity class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Drives the bundled application server as a service.
 *
 * The server is an ordinary PHP process that serves this installation from
 * persistent workers instead of one process per request. Running it means
 * assembling a long command line, backgrounding it so it survives the shell
 * that started it, and then knowing which processes belong to it when the
 * time comes to stop them. Getting any of that subtly wrong is easy, and the
 * failure modes are unpleasant: a pkill that takes the calling shell with it,
 * an orphaned pool still holding the port, a second server started because
 * the first was not noticed.
 *
 * So the knowledge lives here rather than in a shell history, and the command
 * line is built from settings rather than typed. Everything a caller needs is
 * a verb -- start, stop, graceful, restart, kill, status -- and the class
 * answers with a result array rather than printing, so a module view can use
 * it as readily as the console can.
 *
 * Settings come from velocity.ini, which ships with defaults and can be
 * overridden per installation in the usual way.
 *
 *   $velocity = new expVelocity();
 *   $result = $velocity->start();
 *   if ( !$result['ok'] ) echo $result['message'];
 *
 * @package kernel
 */
class expVelocity
{
    /**
     * Seconds to wait between polls when watching for a state change.
     */
    const POLL_INTERVAL = 0.25;

    /**
     * @var eZINI
     */
    protected $ini;

    /**
     * @var string Absolute path to the installation root.
     */
    protected $rootDir;

    public function __construct( $iniName = 'velocity.ini' )
    {
        $this->ini = eZINI::instance( $iniName );
        $this->rootDir = rtrim( eZSys::rootDir(), '/' );
        if ( $this->rootDir === '' )
            $this->rootDir = rtrim( getcwd(), '/' );
    }

    // ── Settings ─────────────────────────────────────────────────────────

    /**
     * A setting, with a fallback when the block or variable is absent.
     *
     * eZINI throws when asked for something that is not there, and a control
     * script that dies because an optional setting was never written is not
     * much use.
     *
     * @param string $block
     * @param string $variable
     * @param mixed $default
     * @return mixed
     */
    protected function setting( $block, $variable, $default = null )
    {
        if ( !$this->ini->hasVariable( $block, $variable ) )
            return $default;

        return $this->ini->variable( $block, $variable );
    }

    /**
     * Absolute path, resolving a relative setting against the root.
     *
     * @param string $path
     * @return string
     */
    protected function absolute( $path )
    {
        $path = (string)$path;
        if ( $path === '' )
            return $this->rootDir;

        return $path[0] === '/' ? $path : $this->rootDir . '/' . $path;
    }

    /**
     * Where the pid file lives.
     *
     * @return string
     */
    public function pidFile()
    {
        return $this->absolute( $this->setting( 'ServerSettings', 'PidFile', 'var/tmp/velocity.pid' ) );
    }

    /**
     * Where the server's own output goes.
     *
     * @return string
     */
    public function logFile()
    {
        return $this->absolute( $this->setting( 'ServerSettings', 'LogFile', 'var/tmp/velocity.log' ) );
    }

    /**
     * The server script this installation drives.
     *
     * @return string
     */
    public function scriptPath()
    {
        return $this->absolute( $this->setting( 'ServerSettings', 'ScriptPath',
            'vendor/se7enxweb/qbix-webserver/qbixserver.php' ) );
    }

    /**
     * Whether TLS is configured and the certificate actually exists.
     *
     * Reporting TLS as enabled when the files are missing produces a server
     * that starts and then serves plain HTTP on the port a browser will
     * address as https, which is a confusing way to spend an afternoon.
     *
     * @return bool
     */
    public function httpsEnabled()
    {
        $enabled = $this->setting( 'HTTPSSettings', 'Enabled', 'false' );
        if ( $enabled !== 'true' && $enabled !== 'enabled' && $enabled !== true )
            return false;

        $cert = $this->absolute( $this->setting( 'HTTPSSettings', 'Certificate', '' ) );
        $key  = $this->absolute( $this->setting( 'HTTPSSettings', 'Key', '' ) );

        return $cert !== $this->rootDir && $key !== $this->rootDir
            && is_file( $cert ) && is_file( $key );
    }

    // ── Command line ─────────────────────────────────────────────────────

    /**
     * The command line the server is started with.
     *
     * Built rather than typed, so the parts that must not be forgotten --
     * an explicit worker count, the globals the application keeps -- cannot
     * be left off by accident.
     *
     * @return array
     */
    /**
     * The engine archive the server should run from, or '' for the files on disk.
     *
     * The bootstrap only loads the engine out of an archive when the
     * EXP_ENGINE_PHAR environment variable names one that exists. Nothing set
     * it, so the archive could be built and inspected but never actually run,
     * and the Setup > System information page could only ever report
     * "Individual files on disk". Testing the archive meant exporting the
     * variable by hand into whatever started the server.
     *
     * [ServerSettings]EnginePhar takes:
     *
     *   disabled   run from the files on disk. The default.
     *   enabled    run from dist/engine.phar, wherever expPhar puts it.
     *   <path>     run from that archive, relative to the installation root
     *              or absolute.
     *
     * A path that does not exist is refused rather than ignored: starting from
     * the files on disk after being asked for an archive looks identical to
     * success, and the difference only shows up much later.
     *
     * @return string absolute path, or ''
     */
    public function enginePhar()
    {
        $setting = trim( (string)$this->setting( 'ServerSettings', 'EnginePhar', 'disabled' ) );

        if ( $setting === '' || $setting === 'disabled' || $setting === 'false' )
            return '';

        if ( $setting === 'enabled' || $setting === 'true' )
        {
            if ( !class_exists( 'expPhar' ) )
                @include_once( $this->absolute( 'kernel/classes/expphar.php' ) );
            return class_exists( 'expPhar' ) ? expPhar::enginePath() : '';
        }

        return $this->absolute( $setting );
    }

    public function command()
    {
        $documentRoot = $this->absolute( $this->setting( 'ServerSettings', 'DocumentRoot', '' ) );
        $host    = $this->setting( 'ServerSettings', 'Host', '127.0.0.1' );
        $port    = (int)$this->setting( 'ServerSettings', 'Port', 8088 );
        $workers = (int)$this->setting( 'ServerSettings', 'Workers', 4 );

        $arguments = array( PHP_BINARY );

        // Interpreter settings, applied to this process only.
        //
        // The opcode cache is the single largest win available to this
        // installation: measured on a content page, four workers, requests
        // round-robined between servers so drift hits them equally, it is
        // about 26% off the time to render. Nothing else came close -- an
        // engine archive was within noise, and tracing JIT was *slower* than
        // the plain cache.
        //
        // It has to be asked for here because /etc/php.d/99-no-opcache-cli.ini
        // turns it off for every command-line PHP on this machine. That is a
        // system-wide decision affecting every site on the box, so it is left
        // alone and the setting is made per process instead.
        foreach ( (array)$this->setting( 'PHPSettings', 'IniOptions', array() ) as $iniOption )
        {
            $iniOption = trim( (string)$iniOption );
            if ( $iniOption !== '' )
            {
                $arguments[] = '-d';
                $arguments[] = $iniOption;
            }
        }

        $arguments[] = $this->scriptPath();
        $arguments = array_merge( $arguments, array(
            '--root=' . $documentRoot,
            '--host=' . $host,
            '--port=' . $port,
            '--workers=' . ( $workers > 0 ? $workers : 4 ),
            '--pid=' . $this->pidFile(),
        ) );

        if ( $this->httpsEnabled() )
            $arguments[] = '--https-port=' . (int)$this->setting( 'ServerSettings', 'HTTPSPort', 8080 );

        $keepGlobals = $this->setting( 'ApplicationSettings', 'KeepGlobals', array() );
        if ( is_array( $keepGlobals ) && $keepGlobals )
            $arguments[] = '--keep-globals=' . implode( ',', $keepGlobals );

        $extra = $this->setting( 'ControlSettings', 'ExtraOptions', array() );
        if ( is_array( $extra ) )
            foreach ( $extra as $option )
                if ( trim( (string)$option ) !== '' )
                    $arguments[] = (string)$option;

        return $arguments;
    }

    /**
     * A JSON config file for the server, written from velocity.ini.
     *
     * The server takes its TLS paths through a config file rather than the
     * command line, so one is generated instead of being maintained by hand
     * in two places.
     *
     * @return string|false path, or false when nothing needs configuring
     */
    protected function writeServerConfig()
    {
        $web = array();

        if ( $this->httpsEnabled() )
        {
            $web['https'] = array(
                'mode' => 'manual',
                'cert' => $this->absolute( $this->setting( 'HTTPSSettings', 'Certificate', '' ) ),
                'key'  => $this->absolute( $this->setting( 'HTTPSSettings', 'Key', '' ) ),
            );
        }

        // How long a browser may keep a stylesheet, script or image without
        // asking again.
        //
        // The server's own default permits caching and then requires the
        // client to revalidate anyway, so a page carrying dozens of assets
        // costs dozens of conditional requests on every view. Measured here:
        // 41 assets on the front page, every one revalidated every time, while
        // the same site behind the other web server fetched none of them
        // again.
        //
        // Zero leaves the server's default alone. The trade is staleness: a
        // file replaced in place is not noticed until the lifetime expires,
        // which costs nothing for an asset whose URL changes with its content
        // and is why this is a setting rather than an assumption.
        $staticMaxAge = (int)$this->setting( 'ServerSettings', 'StaticMaxAge', 0 );
        if ( $staticMaxAge > 0 )
            $web['static'] = array( 'maxAge' => $staticMaxAge );

        // HTTP/2, which the server negotiates during the TLS handshake.
        //
        // A page here references 41 assets, and over HTTP/1.1 a browser
        // fetches them about six at a time in waves; over HTTP/2 they share
        // one connection. Measured on this installation, asset delivery was
        // 393-418ms against 85-104ms through a front end that spoke it.
        //
        // Off unless asked for, in the server and here both. The protocol is
        // agreed during the handshake and a client does not fall back
        // afterwards, so a fault is a page that never arrives rather than one
        // that arrives slowly -- which is also why the server now answers any
        // stream still open with a page rather than silence.
        if ( $this->setting( 'ServerSettings', 'HTTP2', 'disabled' ) === 'enabled' )
        {
            $web['http2'] = array( 'enabled' => true );

            $errorImage = (string)$this->setting( 'ServerSettings', 'HTTP2ErrorImage', '' );
            if ( $errorImage !== '' )
                $web['http2']['errorPage'] = array( 'image' => $this->absolute( $errorImage ) );
        }

        // Which cookies mean "this response is personal, do not cache it".
        //
        // The server's own default is PHPSESSID and Q_sid, which are PHP's and
        // Qbix's names and are not what signs anybody in here. The effect was
        // that anyone carrying a stale cookie of either name -- a browser that
        // had ever touched a Qbix application on this host, for instance --
        // bypassed the response cache on every request, while a real signed-in
        // session sailed straight through it.
        //
        // Both halves of that were wrong, and the first was expensive: the same
        // front page is 74ms served from cache and about 1300ms rendered,
        // because the session path runs 529 queries instead of 122. The pages
        // were byte for byte identical apart from two packed asset filenames.
        //
        // SessionNamePrefix, plus md5 of the siteaccess name where
        // SessionNamePerSiteAccess is enabled. The public siteaccesses share
        // the bare prefix; the admin has a name of its own, and an admin
        // session is no reason to stop caching the public site.
        $skip = $this->setting( 'ServerSettings', 'CacheSkipCookies', '' );
        if ( !is_array( $skip ) )
            $skip = $skip === '' ? array() : array( $skip );
        if ( !$skip )
        {
            $siteIni = eZINI::instance( 'site.ini' );
            $prefix = $siteIni->hasVariable( 'Session', 'SessionNamePrefix' )
                    ? (string)$siteIni->variable( 'Session', 'SessionNamePrefix' )
                    : 'eZSESSID';
            if ( $prefix !== '' )
                $skip = array( $prefix );
        }
        if ( $skip )
            $web['cache'] = array( 'skip' => array( 'cookies' => array_values( $skip ) ) );

        if ( !$web )
            return false;

        $config = array( 'Q' => array( 'web' => $web ) );

        $path = $this->absolute( 'var/tmp/velocity-server.json' );
        $directory = dirname( $path );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );

        if ( file_put_contents( $path, json_encode( $config, JSON_PRETTY_PRINT ) ) === false )
            return false;

        @chmod( $path, 0600 );
        return $path;
    }

    // ── Process state ────────────────────────────────────────────────────

    /**
     * Process ids belonging to this server.
     *
     * The pid file names the parent only. The parent forks a pool, and a stop
     * that leaves the children running leaves the port held, so the whole
     * family is found by matching the script path -- which is specific enough
     * not to catch an unrelated server driven from another checkout.
     *
     * @return array
     */
    public function processIDs()
    {
        $script = $this->scriptPath();
        $output = array();
        @exec( 'ps -eo pid=,args= 2>/dev/null', $output );

        $pids = array();
        foreach ( $output as $line )
        {
            $line = trim( $line );
            if ( $line === '' || strpos( $line, $script ) === false )
                continue;

            $parts = preg_split( '/\s+/', $line, 2 );
            if ( isset( $parts[0] ) && ctype_digit( $parts[0] ) )
                $pids[] = (int)$parts[0];
        }

        return $pids;
    }

    /**
     * The parent process id, from the pid file, when it is still alive.
     *
     * @return int|false
     */
    public function parentID()
    {
        $file = $this->pidFile();
        if ( !is_file( $file ) )
            return false;

        $pid = (int)trim( (string)file_get_contents( $file ) );
        if ( $pid <= 0 )
            return false;

        return $this->isAlive( $pid ) ? $pid : false;
    }

    /**
     * @param int $pid
     * @return bool
     */
    protected function isAlive( $pid )
    {
        if ( function_exists( 'posix_kill' ) )
            return @posix_kill( $pid, 0 );

        return is_dir( '/proc/' . (int)$pid );
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->processIDs() ? true : false;
    }

    /**
     * Ports the server is listening on.
     *
     * @return array
     */
    public function listeningPorts()
    {
        $wanted = array( (int)$this->setting( 'ServerSettings', 'Port', 8088 ) );
        if ( $this->httpsEnabled() )
            $wanted[] = (int)$this->setting( 'ServerSettings', 'HTTPSPort', 8080 );

        $output = array();
        @exec( 'ss -ltn 2>/dev/null', $output );

        $listening = array();
        foreach ( $wanted as $port )
            foreach ( $output as $line )
                if ( preg_match( '/:' . $port . '\s/', $line ) )
                {
                    $listening[] = $port;
                    break;
                }

        return $listening;
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

        $script = $this->scriptPath();
        if ( !is_file( $script ) )
            return $this->result( false, "no server script at $script" );

        $log = $this->logFile();
        $directory = dirname( $log );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );

        $arguments = $this->command();
        $config = $this->writeServerConfig();
        if ( $config !== false )
        {
            // Insert immediately after the server script, wherever that is.
            // This used to splice at a fixed index on the assumption that the
            // script was always the second element. Interpreter settings now
            // come before it, so the fixed index landed between -d and its
            // value and PHP read the value as the file to run: "Could not
            // open input file: opcache.enable_cli=1".
            $scriptIndex = array_search( $this->scriptPath(), $arguments, true );
            if ( $scriptIndex === false )
                $scriptIndex = count( $arguments ) - 1;

            array_splice( $arguments, $scriptIndex + 1, 0, array( '--config=' . $config ) );
        }

        // The engine archive, if one was asked for.
        //
        // It has to be in the environment rather than on the command line
        // because autoload.php reads it before it has parsed anything, which
        // is necessarily before any argument could be looked at.
        $environment = '';
        $enginePhar = $this->enginePhar();
        if ( $enginePhar !== '' )
        {
            if ( !file_exists( $enginePhar ) )
                return $this->result( false,
                    'EnginePhar names an archive that does not exist: ' . $enginePhar
                    . ' -- build it first, or set EnginePhar=disabled',
                    $this->status() );

            $environment = 'EXP_ENGINE_PHAR=' . escapeshellarg( $enginePhar ) . ' ';
        }

        // setsid detaches the server from this process group, so it is not
        // taken down with the shell or the script that started it.
        $command = $environment . 'setsid ' . implode( ' ', array_map( 'escapeshellarg', $arguments ) )
                 . ' > ' . escapeshellarg( $log ) . ' 2>&1 < /dev/null &';

        @exec( $command );

        $deadline = microtime( true ) + 20;
        while ( microtime( true ) < $deadline )
        {
            if ( $this->listeningPorts() )
                return $this->result( true, 'started', $this->status() );

            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }

        if ( $this->isRunning() )
            return $this->result( true, 'started, but no port is listening yet', $this->status() );

        return $this->result( false, 'did not start; see ' . $log, $this->status() );
    }

    /**
     * Stop the server and wait for it to go.
     *
     * Graceful in the sense that matters: the parent is asked to end, the
     * pool follows, and anything still alive after the timeout is reported
     * rather than silently left behind.
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
            return $this->result( true, 'not running' );

        // Ask the server to stop itself first. It knows the order to take its
        // own pool down in; signalling is the fallback for when it cannot.
        $this->control( '--stop' );

        $settle = microtime( true ) + 3;
        while ( microtime( true ) < $settle )
        {
            if ( !$this->isRunning() )
            {
                @unlink( $this->pidFile() );
                return $this->result( true, 'stopped' );
            }
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }

        $pids = $this->processIDs();
        if ( !$pids )
        {
            @unlink( $this->pidFile() );
            return $this->result( true, 'stopped' );
        }

        // The parent first, so it can take its own pool down in order.
        $parent = $this->parentID();
        if ( $parent && in_array( $parent, $pids, true ) )
        {
            $this->signal( $parent, $signal );
            array_unshift( $pids, $parent );
        }

        foreach ( $pids as $pid )
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

        return $this->result( false, 'still running after ' . $timeout . 's; try kill',
                              $this->status() );
    }

    /**
     * Ask a running server to re-exec, keeping the listening socket.
     *
     * Falls back to a restart when the server is not running, so the verb
     * does something sensible either way.
     *
     * @return array
     */
    public function graceful()
    {
        $parent = $this->parentID();
        if ( !$parent )
            return $this->restart();

        $this->control( '--reload' );

        // Re-exec takes a moment and the ports go briefly before they come
        // back, so wait for them rather than judging it immediately.
        $deadline = microtime( true ) + 20;
        while ( microtime( true ) < $deadline )
        {
            if ( $this->listeningPorts() )
                return $this->result( true, 'reloaded', $this->status() );

            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }

        // It did not come back. Leaving a half-reloaded server behind is
        // worse than the reload having failed, so put it back on its feet.
        $this->kill();
        $started = $this->start();
        return $this->result( $started['ok'],
            'reload did not come back; restarted instead', $started['data'] );
    }

    /**
     * Stop, then start.
     *
     * @return array
     */
    public function restart()
    {
        $stopped = $this->stop();
        if ( !$stopped['ok'] && $this->isRunning() )
            return $this->result( false, 'could not stop: ' . $stopped['message'], $this->status() );

        return $this->start();
    }

    /**
     * Stop without asking.
     *
     * For when a worker is wedged and will not answer a polite signal.
     *
     * @return array
     */
    public function kill()
    {
        $pids = $this->processIDs();
        if ( !$pids )
            return $this->result( true, 'not running' );

        $signal = defined( 'SIGKILL' ) ? SIGKILL : 9;
        foreach ( $pids as $pid )
            $this->signal( $pid, $signal );

        usleep( (int)( self::POLL_INTERVAL * 2 * 1000000 ) );
        @unlink( $this->pidFile() );

        $left = $this->processIDs();
        if ( $left )
            return $this->result( false, count( $left ) . ' process(es) survived SIGKILL',
                                  $this->status() );

        return $this->result( true, 'killed ' . count( $pids ) . ' process(es)' );
    }

    /**
     * What the server is doing.
     *
     * @return array
     */
    public function status()
    {
        $pids = $this->processIDs();
        $ports = $this->listeningPorts();

        return array(
            'running'   => $pids ? true : false,
            'parent'    => $this->parentID(),
            'processes' => count( $pids ),
            'pids'      => $pids,
            'listening' => $ports,
            'https'     => $this->httpsEnabled(),
            // Which engine the running server was started with. Worth stating
            // here because it cannot be told apart from the outside: an
            // archive that failed to load and a run from the files on disk
            // look exactly the same until something behaves oddly.
            'engine'    => ( $this->enginePhar() === '' ) ? 'files on disk' : $this->enginePhar(),
            'script'    => $this->scriptPath(),
            'log'       => $this->logFile(),
            'pidFile'   => $this->pidFile(),
        );
    }

    // ── Internals ────────────────────────────────────────────────────────

    /**
     * Run the server script with one of its own control options.
     *
     * The server implements --stop and --reload itself, against the pid file
     * it wrote. Those are the supported ways to ask it to do something, and
     * they do it in the order it expects. Signalling the parent directly is a
     * guess about its internals: SIGHUP, the obvious guess for a reload, took
     * the worker pool down and left the parent alive holding no ports.
     *
     * @param string $option --stop or --reload
     * @return bool whether the command could be run at all
     */
    protected function control( $option )
    {
        $script = $this->scriptPath();
        if ( !is_file( $script ) )
            return false;

        $command = implode( ' ', array_map( 'escapeshellarg', array(
            PHP_BINARY, $script, $option, '--pid=' . $this->pidFile()
        ) ) ) . ' > /dev/null 2>&1';

        @exec( $command, $output, $status );
        return $status === 0;
    }

    /**
     * @param int $pid
     * @param int $signal
     * @return bool
     */
    protected function signal( $pid, $signal )
    {
        if ( function_exists( 'posix_kill' ) )
            return @posix_kill( (int)$pid, (int)$signal );

        @exec( 'kill -' . (int)$signal . ' ' . (int)$pid . ' 2>/dev/null' );
        return true;
    }

    /**
     * @param bool $ok
     * @param string $message
     * @param mixed $data
     * @return array
     */
    protected function result( $ok, $message, $data = null )
    {
        return array( 'ok' => (bool)$ok, 'message' => $message, 'data' => $data );
    }
}
