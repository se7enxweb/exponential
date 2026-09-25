<?php
/**
 * File containing the expVelocityFrankenPHP class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Velocity's second engine: FrankenPHP, the Caddy web server with PHP built in.
 *
 * Chosen with [ServerSettings] Engine=frankenphp. The verbs are the same as for
 * the bundled Qbix server -- start, stop, graceful, restart, kill, status,
 * command -- and so are the settings they read wherever the two servers mean
 * the same thing: port, host, document root, worker count, TLS, logs, the
 * interpreter options. What only one of them has is reported rather than
 * silently dropped: status and start list every setting this engine ignores.
 *
 * FrankenPHP is a single self-contained binary (PHP, its extensions and the
 * web server in one file), downloaded on demand by `install`
 * (expVelocityFrankenPHPInstaller) or supplied as [FrankenPHPSettings]
 * BinaryPath. It runs in classic mode: a pool of PHP threads in one process,
 * each request starting from a clean state, as under php-fpm. Worker mode is
 * not offered, because the kernel keeps request state in globals.
 *
 * The server is configured by a Caddyfile generated from velocity.ini on every
 * start, graceful and restart, and controlled through Caddy's admin API on a
 * unix socket -- no ps, no ss, no signals needed where the API answers.
 *
 * @package kernel
 */
class expVelocityFrankenPHP extends expVelocity
{
    /**
     * The longest unix socket path the admin API can listen on. The limit is
     * 108 bytes with the terminating NUL (PHP reports 107); a little is kept
     * in hand, and a longer path falls back to a TCP port on localhost.
     */
    const MAX_SOCKET_PATH = 104;

    /** @var array|null `frankenphp version`, per binary, for status */
    protected $versionCache = array();

    // ── Settings ─────────────────────────────────────────────────────────

    /**
     * A [FrankenPHPSettings] value, for this class and its installer.
     *
     * @param string $variable
     * @param mixed $default
     * @return mixed
     */
    public function frankenSetting( $variable, $default = null )
    {
        return parent::setting( 'FrankenPHPSettings', $variable, $default );
    }

    /**
     * Port, HTTPSPort, Host, Workers, SpareWorkers and DocumentRoot from
     * [FrankenPHPSettings] when it sets them, so this engine can run beside
     * the others; [ServerSettings] otherwise.
     */
    protected function setting( $block, $variable, $default = null )
    {
        return $this->engineSetting( 'FrankenPHPSettings', $block, $variable, $default );
    }

    /**
     * @return string
     */
    public function role()
    {
        return 'production';
    }

    /**
     * @param bool|null $token unused: this engine has no dashboard
     * @param bool|null $remote unused
     * @param bool $panelPassword unused
     * @return array see expVelocity::views()
     */
    public function views( $token = null, $remote = null, $panelPassword = false )
    {
        $socket = $this->adminSocket();
        return array(
            array( '/Q/health', 'text', 'public', 'health check (answered by Caddy, empty 200)' ),
            array( $socket !== null ? $socket : $this->adminAddress(), 'admin API', $socket !== null
                ? 'not a web page: a unix socket readable only by the user running the server'
                : 'not a web page: ' . $this->adminAddress() . ', reachable from this machine',
                'Caddy\'s admin API, which stop and graceful use' ),
        );
    }

    /**
     * @return string
     */
    public function engineName()
    {
        return 'frankenphp';
    }

    /**
     * No /etc/vc tree and no certificate commands: those belong to the Qbix
     * server. The generated Caddyfile is the whole configuration here.
     *
     * @param string $feature
     * @return bool
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
        return $this->absolute( $this->frankenSetting( 'PidFile', 'var/tmp/velocity-frankenphp.pid' ) );
    }

    /**
     * @return string
     */
    public function logFile()
    {
        return $this->absolute( $this->frankenSetting( 'LogFile', 'var/tmp/velocity-frankenphp.log' ) );
    }

    /**
     * Where the generated Caddyfile is written.
     *
     * @return string
     */
    public function caddyfile()
    {
        return $this->absolute( $this->frankenSetting( 'ConfigFile', 'var/tmp/velocity-frankenphp.Caddyfile' ) );
    }

    /**
     * @return expVelocityFrankenPHPInstaller
     */
    public function installer()
    {
        return new expVelocityFrankenPHPInstaller( $this );
    }

    /**
     * The binary this installation runs: BinaryPath when one is set (an own
     * build, a distribution package), otherwise the one `install` puts in
     * BinaryDir for the pinned version.
     *
     * @return string
     */
    public function binary()
    {
        $path = trim( (string)$this->frankenSetting( 'BinaryPath', '' ) );
        if ( $path !== '' )
            return $this->absolute( $path );

        return $this->installer()->targetPath();
    }

    /**
     * The process that is the server, for the parent's status: the binary.
     *
     * @return string
     */
    public function scriptPath()
    {
        return $this->binary();
    }

    /**
     * Caddy's admin API address, as the Caddyfile and `frankenphp stop
     * --address` write it: unix/<path> or host:port.
     *
     * A unix socket by default. It cannot clash with a second installation
     * on the same machine, it takes no port, and Caddy creates it readable
     * and writable by its owner only. A socket path longer than the kernel
     * allows (an installation deep in the file system) falls back to a port
     * on localhost, 10000 above the HTTP port.
     *
     * @return string
     */
    public function adminAddress()
    {
        $address = trim( (string)$this->frankenSetting( 'AdminAddress', 'auto' ) );
        if ( $address !== '' && $address !== 'auto' )
            return $address;

        $socket = $this->absolute( 'var/tmp/vfp-admin.sock' );
        if ( strlen( $socket ) <= self::MAX_SOCKET_PATH )
            return 'unix/' . $socket;

        return 'localhost:' . ( (int)$this->setting( 'ServerSettings', 'Port', 8088 ) + 10000 );
    }

    /**
     * The admin socket file, when the admin API listens on one.
     *
     * @return string|null
     */
    protected function adminSocket()
    {
        $address = $this->adminAddress();
        return strncmp( $address, 'unix/', 5 ) === 0 ? substr( $address, 5 ) : null;
    }

    // ── Command line and configuration ───────────────────────────────────

    /**
     * The command line the server is started with.
     *
     * [ControlSettings] ExtraOptions holds options for the Qbix server and
     * is not passed here; [FrankenPHPSettings] ExtraOptions[] is.
     *
     * @return array
     */
    public function command()
    {
        $arguments = array( $this->binary(), 'run', '--config', $this->caddyfile(),
                            '--adapter', 'caddyfile', '--pidfile', $this->pidFile() );

        foreach ( (array)$this->frankenSetting( 'ExtraOptions', array() ) as $option )
            if ( trim( (string)$option ) !== '' )
                $arguments[] = (string)$option;

        return $arguments;
    }

    /**
     * A value for the Caddyfile, quoted.
     *
     * Every path and value goes through here, so a space, a brace or a quote
     * in an installation path cannot end a directive early.
     *
     * @param string $value
     * @return string
     */
    public static function caddyQuote( $value )
    {
        return '"' . str_replace( array( '\\', '"' ), array( '\\\\', '\\"' ), (string)$value ) . '"';
    }

    /**
     * The php_ini lines: [PHPSettings] IniOptions[] (shared with the Qbix
     * server), then [FrankenPHPSettings] IniOptions[]; a later key wins.
     *
     * The binary reads no php.ini of the machine it runs on, so without these
     * PHP runs on its compiled-in defaults -- display_errors on, a 128M
     * memory limit. velocity.ini ships sensible values in the second list.
     *
     * @return array key => value
     */
    public function phpIni()
    {
        $ini = array();
        foreach ( array( (array)$this->setting( 'PHPSettings', 'IniOptions', array() ),
                         (array)$this->frankenSetting( 'IniOptions', array() ) ) as $list )
        {
            foreach ( $list as $option )
            {
                $option = trim( (string)$option );
                $eq = strpos( $option, '=' );
                if ( $option === '' || $eq === false || $eq === 0 )
                    continue;
                $ini[trim( substr( $option, 0, $eq ) )] = trim( substr( $option, $eq + 1 ) );
            }
        }
        return $ini;
    }

    /**
     * The access and error log files. Named for this engine rather than taken
     * from [LogSettings] AccessName/ErrorName, because Caddy writes JSON and
     * the Qbix server does not: switching engines must not leave one file in
     * two formats.
     *
     * @return array access, error (null when logging is off)
     */
    public function logFiles()
    {
        $enabled = $this->setting( 'LogSettings', 'Enabled', 'enabled' );
        if ( $enabled !== 'enabled' && $enabled !== 'true' )
            return array( 'access' => null, 'error' => null );

        $dir = $this->absolute( $this->setting( 'LogSettings', 'Dir', 'var/log/qbix' ) );
        $access = trim( (string)$this->frankenSetting( 'AccessLog', '' ) );
        $error  = trim( (string)$this->frankenSetting( 'ErrorLog', '' ) );
        return array(
            'access' => $access !== '' ? $this->absolute( $access ) : $dir . '/frankenphp-access.log',
            'error'  => $error  !== '' ? $this->absolute( $error )  : $dir . '/frankenphp-error.log',
        );
    }

    /**
     * The Caddyfile for this installation, generated from velocity.ini.
     *
     * The routing is exponential's .htaccess_root: the paths in
     * STATIC_PATHS are files, api/ goes to index_rest.php, the tree menu to
     * index_treemenu.php, and everything else to index.php. It is spelled out
     * rather than left to php_server, whose default is to serve any file that
     * exists -- settings/site.ini, the SQLite database, the kernel's sources.
     *
     * @return string
     */
    public function caddyfileText()
    {
        $q = array( __CLASS__, 'caddyQuote' );
        $port    = (int)$this->setting( 'ServerSettings', 'Port', 8088 );
        $host    = trim( (string)$this->setting( 'ServerSettings', 'Host', '127.0.0.1' ) );
        $root    = $this->absolute( $this->setting( 'ServerSettings', 'DocumentRoot', '' ) );
        $workers = (int)$this->setting( 'ServerSettings', 'Workers', 4 );
        $workers = $workers > 0 ? $workers : 4;
        $spare   = max( 0, (int)$this->setting( 'ServerSettings', 'SpareWorkers', 0 ) );
        $timeout = (int)$this->setting( 'ControlSettings', 'StopTimeout', 15 );
        $http2   = $this->setting( 'ServerSettings', 'HTTP2', 'enabled' );
        $maxAge  = (int)$this->setting( 'ServerSettings', 'StaticMaxAge', 0 );
        $mode    = trim( (string)$this->setting( 'LogSettings', 'FileMode', '' ) );
        $logs    = $this->logFiles();
        $phar    = $this->enginePhar();
        $include = trim( (string)$this->frankenSetting( 'SiteInclude', '' ) );

        $fileOutput = function ( $file ) use ( $q, $mode )
        {
            return "\t\toutput file " . call_user_func( $q, $file )
                 . ( preg_match( '/^0?[0-7]{3,4}$/', $mode ) ? " {\n\t\t\tmode $mode\n\t\t}" : '' ) . "\n";
        };

        $lines = array();
        $lines[] = '# Generated by exp:velocity (engine frankenphp) from velocity.ini on ' . date( 'c' ) . '.';
        $lines[] = '# Do not edit: it is rewritten on every start, graceful and restart. Own';
        $lines[] = '# directives go in a file named by [FrankenPHPSettings] SiteInclude.';
        $lines[] = '{';
        $lines[] = "\tauto_https off";
        $lines[] = "\tadmin " . call_user_func( $q, $this->adminAddress() );
        $lines[] = "\tpersist_config off";
        $lines[] = "\tgrace_period " . ( $timeout > 0 ? $timeout : 15 ) . 's';
        $lines[] = "\tservers {";
        $lines[] = "\t\tprotocols h1" . ( ( $http2 === 'enabled' || $http2 === 'true' ) ? ' h2' : '' );
        $lines[] = "\t}";
        if ( $logs['error'] !== null )
        {
            $lines[] = "\tlog {";
            $lines[] = rtrim( $fileOutput( $logs['error'] ), "\n" );
            $lines[] = "\t\tlevel INFO";
            $lines[] = "\t}";
        }
        $lines[] = "\tfrankenphp {";
        $lines[] = "\t\tnum_threads $workers";
        $lines[] = "\t\tmax_threads " . ( $workers + $spare );
        foreach ( $this->phpIni() as $key => $value )
            $lines[] = "\t\tphp_ini " . call_user_func( $q, $key ) . ' ' . call_user_func( $q, $value );
        $lines[] = "\t}";
        $lines[] = '}';
        $lines[] = '';

        $lines[] = '(exp) {';
        $lines[] = "\troot * " . call_user_func( $q, $root );
        if ( $logs['access'] !== null )
        {
            $lines[] = "\tlog {";
            $lines[] = rtrim( $fileOutput( $logs['access'] ), "\n" );
            $lines[] = "\t\tformat json";
            $lines[] = "\t}";
        }
        $lines[] = "\troute {";
        $lines[] = "\t\trespond /Q/health 200";
        // Never a script or a dot path, even below a listed directory: the
        // file server would send a .php file's source (var/storage/packages
        // holds package settings as PHP), and .htaccess, .git or .cache are
        // not the site's to serve. Both fall through to index.php.
        $lines[] = "\t\t@static {";
        $lines[] = "\t\t\tpath_regexp static " . self::STATIC_PATHS;
        $lines[] = "\t\t\tnot path_regexp " . self::NEVER_STATIC;
        $lines[] = "\t\t}";
        if ( $maxAge > 0 )
            $lines[] = "\t\theader @static Cache-Control " . call_user_func( $q, 'public, max-age=' . $maxAge );
        $lines[] = "\t\tfile_server @static";
        $lines[] = "\t\t@rest path_regexp rest ^/(api/|index_rest\\.php)";
        $lines[] = "\t\trewrite @rest /index_rest.php";
        $lines[] = "\t\t@treemenu path_regexp treemenu ^/([^/]+/)?content/treemenu";
        $lines[] = "\t\trewrite @treemenu /index_treemenu.php";
        $lines[] = "\t\t@front not path /index_rest.php /index_treemenu.php";
        $lines[] = "\t\trewrite @front /index.php";
        $lines[] = "\t\tphp {";
        // Who serves the page, for Setup > System information: FrankenPHP says
        // only "FrankenPHP" in SERVER_SOFTWARE, and PHP has no call that tells.
        $lines[] = "\t\t\tenv EXP_VELOCITY_ENGINE " . call_user_func( $q, $this->binaryVersion() !== '' ? $this->binaryVersion() : 'FrankenPHP' );
        if ( $phar !== '' )
            $lines[] = "\t\t\tenv EXP_ENGINE_PHAR " . call_user_func( $q, $phar );
        $lines[] = "\t\t}";
        $lines[] = "\t}";
        $lines[] = '}';
        $lines[] = '';

        $bind = ( $host !== '' ) ? "\tbind " . $host : null;
        $sites = array( array( 'http://:' . $port, null ) );
        if ( $this->httpsEnabled() )
            $sites[] = array( 'https://:' . (int)$this->setting( 'ServerSettings', 'HTTPSPort', 8080 ),
                              "\ttls " . call_user_func( $q, $this->absolute( $this->setting( 'HTTPSSettings', 'Certificate', '' ) ) )
                              . ' ' . call_user_func( $q, $this->absolute( $this->setting( 'HTTPSSettings', 'Key', '' ) ) ) );
        foreach ( $sites as $site )
        {
            $lines[] = $site[0] . ' {';
            if ( $bind !== null )
                $lines[] = $bind;
            if ( $site[1] !== null )
                $lines[] = $site[1];
            $lines[] = "\timport exp";
            if ( $include !== '' )
                $lines[] = "\timport " . call_user_func( $q, $this->absolute( $include ) );
            $lines[] = '}';
        }

        return implode( "\n", $lines ) . "\n";
    }

    /**
     * Write the Caddyfile, atomically -- and, when asked, only if the binary
     * accepts it, so a broken SiteInclude never replaces the file a running
     * server was started from.
     *
     * @param bool $validate
     * @return string|false its path, or false (see $this->writeError)
     */
    public function writeCaddyfile( $validate = false )
    {
        $this->writeError = '';
        $file = $this->caddyfile();
        if ( !is_dir( dirname( $file ) ) )
            eZDir::mkdir( dirname( $file ), false, true );

        $tmp = $file . '.tmp.' . getmypid();
        if ( @file_put_contents( $tmp, $this->caddyfileText() ) === false )
        {
            $this->writeError = 'could not write ' . $tmp;
            return false;
        }
        @chmod( $tmp, 0600 );
        if ( $validate )
        {
            $valid = $this->validate( $tmp );
            if ( $valid !== true )
            {
                @unlink( $tmp );
                $this->writeError = 'the generated Caddyfile is invalid, ' . $file . ' was left as it is: ' . $valid;
                return false;
            }
        }
        if ( !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            $this->writeError = 'could not write ' . $file;
            return false;
        }
        return $file;
    }

    /** @var string why the last writeCaddyfile() returned false */
    protected $writeError = '';

    /**
     * The Qbix server's JSON configuration has no meaning here.
     */
    protected function writeServerConfig()
    {
        throw new LogicException( 'the frankenphp engine is configured by its Caddyfile, not velocity-server.json' );
    }

    /**
     * Nor have the Qbix server's --stop and --reload options.
     *
     * @param string $option
     */
    protected function control( $option )
    {
        throw new LogicException( 'the frankenphp engine is controlled through its admin API' );
    }

    /**
     * Check a Caddyfile with the binary itself.
     *
     * @param string|null $file the generated one when null
     * @return true|string true, or what the binary objected to
     */
    public function validate( $file = null )
    {
        $output = array();
        $code = 0;
        @exec( implode( ' ', array_map( 'escapeshellarg', array(
                   $this->binary(), 'validate', '--config', $file !== null ? $file : $this->caddyfile(),
                   '--adapter', 'caddyfile' ) ) )
               . ' 2>&1', $output, $code );
        if ( $code === 0 )
            return true;

        // Caddy logs JSON lines; the error is the last "Error:" line.
        $errors = preg_grep( '/^Error:/', $output );
        return trim( $errors ? end( $errors ) : implode( ' ', array_slice( $output, -2 ) ) );
    }

    /**
     * Settings velocity.ini has that this engine does not act on, and why --
     * only those an installation actually set, so the list stays short.
     *
     * @return array of strings
     */
    public function ignoredSettings()
    {
        $on = function ( $value ) { return $value === 'enabled' || $value === 'true' || $value === true; };
        $notes = array();

        if ( $on( $this->cacheSetting( 'Enabled', null, 'enabled' ) ) )
            $notes[] = 'CacheSettings: no response cache on the frankenphp engine yet';
        if ( $on( $this->setting( 'ServerSettings', 'ForkPerRequest', 'disabled' ) ) )
            $notes[] = 'ForkPerRequest: not needed, every request starts from a clean state in classic mode';
        if ( $this->setting( 'ApplicationSettings', 'KeepGlobals', array() ) )
            $notes[] = 'KeepGlobals: not needed, nothing is kept between requests';
        if ( !$this->followsSymlinks() )
            $notes[] = 'FollowSymlinks=disabled: Caddy\'s file server always follows links out of the document root';
        if ( $on( $this->setting( 'ServerSettings', 'PreloadWarmup', 'disabled' ) ) )
            $notes[] = 'PreloadWarmup: Qbix server only';
        $format = strtolower( trim( (string)$this->setting( 'LogSettings', 'Format', '' ) ) );
        if ( $format !== '' && $format !== 'json' )
            $notes[] = "LogSettings Format=$format: Caddy writes JSON";
        if ( trim( (string)$this->setting( 'ServerSettings', 'Brand', '' ) ) !== ''
             || trim( (string)$this->setting( 'ServerSettings', 'HTTP2ErrorImage', '' ) ) !== '' )
            $notes[] = 'Brand*/HTTP2ErrorImage: the Qbix server\'s own pages only';
        if ( trim( (string)$this->setting( 'DashboardSettings', 'Token', '' ) ) !== '' )
            $notes[] = 'DashboardSettings: the Qbix server\'s dashboard only';
        if ( array_filter( (array)$this->setting( 'ControlSettings', 'ExtraOptions', array() ) ) )
            $notes[] = 'ControlSettings ExtraOptions: Qbix server options; use FrankenPHPSettings ExtraOptions[]';

        return $notes;
    }

    // ── Process state ────────────────────────────────────────────────────

    /**
     * The server process: the binary, run with this installation's Caddyfile.
     * FrankenPHP serves from threads, so there is one process, not a family.
     *
     * @return array
     */
    public function processIDs()
    {
        $binary = $this->binary();
        $config = '--config ' . $this->caddyfile();
        $pids = array();
        foreach ( $this->processTable() as $proc )
            if ( strpos( $proc['args'], $binary ) !== false && strpos( $proc['args'], $config ) !== false )
                $pids[] = $proc['pid'];

        return $pids;
    }

    /**
     * @param int $parent
     * @return array always empty: there are no worker processes
     */
    public function childIDs( $parent )
    {
        return array();
    }

    /**
     * The binary's own report: FrankenPHP, PHP and Caddy versions.
     *
     * @return string '' when it cannot be run
     */
    public function binaryVersion()
    {
        $binary = $this->binary();
        if ( !isset( $this->versionCache[$binary] ) )
        {
            $output = array();
            $code = 1;
            if ( is_file( $binary ) && is_executable( $binary ) )
                @exec( escapeshellarg( $binary ) . ' version 2>/dev/null', $output, $code );
            $this->versionCache[$binary] = $code === 0
                ? expVelocityFrankenPHPInstaller::cleanVersion( (string)( $output[0] ?? '' ) ) : '';
        }
        return $this->versionCache[$binary];
    }

    /**
     * Send a request to Caddy's admin API.
     *
     * Plain HTTP over the socket rather than an HTTP client: it needs nothing
     * beyond the stream functions, works on a unix socket, and sends no Origin
     * header (the API rejects requests that carry an unexpected one).
     *
     * @param string $method
     * @param string $path
     * @return int HTTP status, 0 when the API could not be reached
     */
    public function adminRequest( $method, $path )
    {
        $address = $this->adminAddress();
        if ( strncmp( $address, 'unix/', 5 ) === 0 )
        {
            $target = 'unix://' . substr( $address, 5 );
            $hostHeader = 'localhost';
        }
        else
        {
            $address = preg_replace( '#^tcp/#', '', $address );
            $target = 'tcp://' . $address;
            $hostHeader = $address;
        }

        $socket = @stream_socket_client( $target, $errno, $errstr, 5 );
        if ( !$socket )
            return 0;
        stream_set_timeout( $socket, 10 );
        fwrite( $socket, "$method $path HTTP/1.1\r\nHost: $hostHeader\r\nContent-Length: 0\r\nConnection: close\r\n\r\n" );
        $status = fgets( $socket );
        fclose( $socket );

        return preg_match( '#^HTTP/\d(?:\.\d)?\s+(\d{3})#', (string)$status, $m ) ? (int)$m[1] : 0;
    }

    /**
     * Whether the site answers its health check.
     *
     * @return bool
     */
    protected function healthy()
    {
        $host = trim( (string)$this->setting( 'ServerSettings', 'Host', '127.0.0.1' ) );
        if ( $host === '' || $host === '0.0.0.0' || $host === '::' )
            $host = '127.0.0.1';
        $port = (int)$this->setting( 'ServerSettings', 'Port', 8088 );

        $socket = @fsockopen( $host, $port, $errno, $errstr, 2 );
        if ( !$socket )
            return false;
        stream_set_timeout( $socket, 5 );
        fwrite( $socket, "GET /Q/health HTTP/1.1\r\nHost: $host\r\nConnection: close\r\n\r\n" );
        $status = fgets( $socket );
        fclose( $socket );

        return (bool)preg_match( '#^HTTP/\d(?:\.\d)?\s+200#', (string)$status );
    }

    /**
     * The last lines of the server's own output, for a start that failed.
     *
     * @param int $count
     * @return string
     */
    protected function logTail( $count = 5 )
    {
        $lines = @file( $this->logFile(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) ?: array();
        $errors = preg_grep( '/^Error:|"level":"(error|fatal)"/', $lines );
        return trim( implode( ' | ', array_slice( $errors ?: $lines, -$count ) ) );
    }

    /**
     * Remove what a stopped server leaves behind.
     */
    protected function cleanUp()
    {
        @unlink( $this->pidFile() );
        // The configured socket, and the default one: a server started with
        // AdminAddress=auto and stopped after it was changed leaves the latter.
        foreach ( array_unique( array_filter( array( $this->adminSocket(), $this->absolute( 'var/tmp/vfp-admin.sock' ) ) ) ) as $socket )
            if ( file_exists( $socket ) && filetype( $socket ) === 'socket' )
                @unlink( $socket );
    }

    // ── Verbs ────────────────────────────────────────────────────────────

    /**
     * The binary, installed first when it is missing and AutoInstall allows.
     *
     * @return true|string true, or why there is none
     */
    protected function ensureBinary()
    {
        $binary = $this->binary();
        if ( is_file( $binary ) && is_executable( $binary ) )
            return true;

        if ( trim( (string)$this->frankenSetting( 'BinaryPath', '' ) ) !== '' )
            return "BinaryPath names no executable file: $binary";

        $auto = $this->frankenSetting( 'AutoInstall', 'enabled' );
        if ( $auto !== 'enabled' && $auto !== 'true' )
            return "no FrankenPHP binary at $binary -- run exp:velocity install (AutoInstall is disabled)";

        $installed = $this->install();
        return $installed['ok'] ? true : $installed['message'];
    }

    /**
     * Everything that can refuse a start, checked before anything is
     * stopped: binary, Caddyfile, the engine archive.
     *
     * @return true|string
     */
    protected function prepare()
    {
        $ready = $this->ensureBinary();
        if ( $ready !== true )
            return $ready;

        foreach ( array( dirname( $this->logFile() ), dirname( $this->pidFile() ),
                         $this->absolute( 'var/velocity/caddy/config' ), $this->absolute( 'var/velocity/caddy/data' ) )
                  + array_filter( array_map( function ( $f ) { return $f === null ? null : dirname( $f ); }, $this->logFiles() ) )
                  as $directory )
            if ( !is_dir( $directory ) )
                eZDir::mkdir( $directory, false, true );

        if ( $this->writeCaddyfile( true ) === false )
            return $this->writeError;

        $enginePhar = $this->enginePhar();
        if ( $enginePhar !== '' )
        {
            if ( !file_exists( $enginePhar ) )
                return 'EnginePhar names an archive that does not exist: ' . $enginePhar
                     . ' -- build it first, or set EnginePhar=disabled';
            $archive = $this->ensureEngineArchive();
            if ( $archive !== true )
                return $archive;
        }
        return true;
    }

    /**
     * Start the server, unless it is already running.
     *
     * @return array
     */
    public function start()
    {
        if ( $this->isRunning() )
            return $this->result( false, 'already running', $this->status() );

        $ready = $this->prepare();
        if ( $ready !== true )
            return $this->result( false, $ready, $this->status() );

        // Another server on the port -- the other engine, typically -- would
        // make this one fail with an address in use, after the Caddyfile
        // looked fine. Say what is going on instead.
        $taken = $this->listeningPorts();
        if ( $taken )
            return $this->result( false, 'port ' . implode( ', ', $taken ) . ' is already in use by another process'
                . ' (the qbix engine? exp:velocity stop --engine=qbix)', $this->status() );

        // Caddy keeps its state (certificates, autosave) under XDG_DATA_HOME
        // and XDG_CONFIG_HOME, else in the home directory of whoever runs it --
        // which a service user may not have or may not be able to write.
        $environment = array(
            'XDG_CONFIG_HOME' => $this->absolute( 'var/velocity/caddy/config' ),
            'XDG_DATA_HOME'   => $this->absolute( 'var/velocity/caddy/data' ),
        );
        if ( trim( (string)getenv( 'PATH' ) ) === '' )
            $environment['PATH'] = self::DEFAULT_PATH;
        if ( $this->enginePhar() !== '' )
            $environment['EXP_ENGINE_PHAR'] = $this->enginePhar();
        $phpIniFile = trim( (string)$this->frankenSetting( 'PHPIniFile', '' ) );
        if ( $phpIniFile !== '' )
            $environment['PHPRC'] = $this->absolute( $phpIniFile );

        $prefix = '';
        foreach ( $environment as $name => $value )
            $prefix .= $name . '=' . escapeshellarg( $value ) . ' ';

        $this->cleanUp();
        $log = $this->logFile();
        $setsid = $this->findExecutable( 'setsid' );
        // The kernel resolves some paths against the working directory, so
        // the server starts in the root. The whole subshell is redirected and
        // replaced by the server (exec): with "cd ... && server > log &" the
        // backgrounded shell kept this process's output pipe open, and exec()
        // here waited for as long as the server ran.
        $command = '( cd ' . escapeshellarg( $this->rootDir ) . ' && ' . $prefix . 'exec '
                 . ( $setsid !== false ? escapeshellarg( $setsid ) . ' ' : '' )
                 . implode( ' ', array_map( 'escapeshellarg', $this->command() ) )
                 . ' ) > ' . escapeshellarg( $log ) . ' 2>&1 < /dev/null &';
        @exec( $command );

        $notes = $this->ignoredSettings();
        $suffix = $notes ? '; not used by this engine: ' . implode( '; ', $notes ) : '';

        $started = microtime( true );
        $deadline = $started + 20;
        while ( microtime( true ) < $deadline )
        {
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
            if ( $this->listeningPorts() && $this->healthy() )
                return $this->result( true, 'started (' . $this->binaryVersion() . ')' . $suffix, $this->status() );

            // A server that is already gone will not come up: say why now
            // rather than after twenty seconds.
            if ( microtime( true ) - $started > 1 && !$this->isRunning() )
                return $this->result( false, 'exited at startup: ' . $this->logTail() . ' -- see ' . $log,
                                      $this->status() );
        }

        if ( $this->isRunning() )
            return $this->result( true, 'started, but the site does not answer yet' . $suffix, $this->status() );

        return $this->result( false, 'did not start; see ' . $log, $this->status() );
    }

    /**
     * Stop the server and wait for it to go: through the admin API first,
     * which lets requests in flight finish (grace_period), then by signal.
     *
     * @param int $signal
     * @return array
     */
    public function stop( $signal = null )
    {
        if ( $signal === null )
            $signal = defined( 'SIGTERM' ) ? SIGTERM : 15;

        if ( !$this->processIDs() )
        {
            $this->cleanUp();
            return $this->result( true, 'not running' );
        }

        $timeout = (float)$this->setting( 'ControlSettings', 'StopTimeout', 15 );
        $timeout = $timeout > 0 ? $timeout : 15;

        $asked = $this->adminRequest( 'POST', '/stop' ) === 200;
        if ( $asked && $this->waitUntilStopped( $timeout ) )
        {
            $this->cleanUp();
            return $this->result( true, 'stopped' );
        }

        foreach ( $this->processIDs() as $pid )
            $this->signal( $pid, $signal );

        if ( $this->waitUntilStopped( $timeout ) )
        {
            $this->cleanUp();
            return $this->result( true, $asked ? 'stopped' : 'stopped (the admin API did not answer; signalled)' );
        }

        return $this->result( false, 'still running after ' . $timeout . 's; try kill', $this->status() );
    }

    /**
     * @param float $seconds
     * @return bool whether the server went within that time
     */
    protected function waitUntilStopped( $seconds )
    {
        $deadline = microtime( true ) + $seconds;
        while ( microtime( true ) < $deadline )
        {
            if ( !$this->isRunning() )
                return true;
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }
        return !$this->isRunning();
    }

    /**
     * Load a new configuration into the running server, without dropping a
     * connection: Caddy swaps it in and restarts the PHP threads with the new
     * interpreter settings.
     *
     * Unlike the Qbix server's reload this cannot leave a half-reloaded
     * server: a configuration Caddy rejects is never applied, and the old one
     * keeps serving. So a rejected reload is reported, not answered with a
     * restart.
     *
     * @return array
     */
    public function graceful()
    {
        if ( !$this->isRunning() )
            return $this->restart();

        $ready = $this->prepare();
        if ( $ready !== true )
            return $this->result( false, $ready . ' -- the running configuration is unchanged', $this->status() );

        // A reload goes through the admin API. When that does not answer --
        // its socket removed, AdminAddress changed since the start -- there is
        // no way to hand the server a new configuration, so restart it, as
        // the Qbix engine does when its reload does not come back.
        if ( $this->adminRequest( 'GET', '/config/' ) !== 200 )
        {
            $restarted = $this->restart();
            return $this->result( $restarted['ok'], 'the admin API (' . $this->adminAddress()
                . ') did not answer, so restarted instead: ' . $restarted['message'], $restarted['data'] );
        }

        $output = array();
        $code = 0;
        @exec( implode( ' ', array_map( 'escapeshellarg', array(
                   $this->binary(), 'reload', '--config', $this->caddyfile(), '--adapter', 'caddyfile',
                   '--address', $this->adminAddress(), '--force' ) ) ) . ' 2>&1', $output, $code );
        if ( $code !== 0 )
        {
            $errors = preg_grep( '/^Error:/', $output );
            return $this->result( false, 'reload rejected, the running configuration is unchanged: '
                . trim( $errors ? end( $errors ) : implode( ' ', array_slice( $output, -2 ) ) ), $this->status() );
        }

        $deadline = microtime( true ) + 20;
        while ( microtime( true ) < $deadline )
        {
            if ( $this->listeningPorts() && $this->healthy() )
                return $this->result( true, 'reloaded', $this->status() );
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }
        return $this->result( false, 'reloaded, but the site does not answer', $this->status() );
    }

    /**
     * Stop, then start -- with everything that can refuse the start checked
     * while the running server is still up.
     *
     * @return array
     */
    public function restart()
    {
        $ready = $this->prepare();
        if ( $ready !== true )
            return $this->result( false, $ready . ' -- the running server was left as it is', $this->status() );

        $stopped = $this->stop();
        if ( !$stopped['ok'] && $this->isRunning() )
            return $this->result( false, 'could not stop: ' . $stopped['message'], $this->status() );

        return $this->start();
    }

    /**
     * Stop without asking.
     *
     * @return array
     */
    public function kill()
    {
        $pids = $this->processIDs();
        if ( !$pids )
        {
            $this->cleanUp();
            return $this->result( true, 'not running' );
        }

        $signal = defined( 'SIGKILL' ) ? SIGKILL : 9;
        foreach ( $pids as $pid )
            $this->signal( $pid, $signal );

        usleep( (int)( self::POLL_INTERVAL * 2 * 1000000 ) );
        $this->cleanUp();

        $left = $this->processIDs();
        if ( $left )
            return $this->result( false, count( $left ) . ' process(es) survived SIGKILL', $this->status() );

        return $this->result( true, 'killed ' . count( $pids ) . ' process(es)' );
    }

    /**
     * Download and verify the binary: see expVelocityFrankenPHPInstaller.
     *
     * @param array $options force, from, check, trustGithubDigest, progress
     * @return array result
     */
    public function install( array $options = array() )
    {
        return $this->installer()->install( $options );
    }

    /**
     * There is no response cache on this engine yet, so there is nothing to
     * clear. Succeeds, so a deploy script that clears it after every release
     * does not fail on this engine.
     *
     * @return array
     */
    public function clearCache()
    {
        return $this->result( true, 'nothing to clear: the frankenphp engine runs without a response cache' );
    }

    /**
     * @return bool the Qbix server's control class is no use here
     */
    public function engineCtl()
    {
        return false;
    }

    /**
     * The /etc/vc tree is the Qbix server's.
     *
     * @throws LogicException
     */
    public function layout()
    {
        throw new LogicException( 'the /etc/vc configuration tree belongs to the qbix engine; the frankenphp engine uses '
            . $this->caddyfile() );
    }

    /**
     * @return array result
     */
    public function migrateLayout()
    {
        return $this->result( false, 'the /etc/vc configuration tree belongs to the qbix engine; the frankenphp engine uses '
            . $this->caddyfile() . ' (own directives: [FrankenPHPSettings] SiteInclude)' );
    }

    /**
     * Every file and directory this engine uses, for `exp:velocity layout`.
     *
     * @return array name => path (null where not in use)
     */
    public function assets()
    {
        $logs = $this->logFiles();
        $socket = $this->adminSocket();
        return array(
            'binary'         => $this->binary(),
            'caddyfile'      => $this->caddyfile(),
            'siteInclude'    => trim( (string)$this->frankenSetting( 'SiteInclude', '' ) ) !== ''
                                ? $this->absolute( $this->frankenSetting( 'SiteInclude', '' ) ) : null,
            'pidFile'        => $this->pidFile(),
            'serverLog'      => $this->logFile(),
            'accessLog'      => $logs['access'],
            'errorLog'       => $logs['error'],
            'adminSocket'    => $socket,
            'caddyData'      => $this->absolute( 'var/velocity/caddy' ),
            'certificate'    => $this->httpsEnabled() ? $this->absolute( $this->setting( 'HTTPSSettings', 'Certificate', '' ) ) : null,
            'certificateKey' => $this->httpsEnabled() ? $this->absolute( $this->setting( 'HTTPSSettings', 'Key', '' ) ) : null,
            'engineArchive'  => $this->enginePhar() !== '' ? $this->enginePhar() : null,
        );
    }

    /**
     * The binary's own commands, with this installation's Caddyfile filled in
     * where they take one: `ctl version`, `ctl list-modules`, `ctl build-info`,
     * `ctl validate`, `ctl adapt`; `ctl caddyfile` prints the file this
     * installation's settings generate.
     *
     * @param array $args
     * @return int exit code
     */
    public function ctl( array $args )
    {
        $sub = isset( $args[0] ) ? (string)$args[0] : '';
        if ( $sub === 'caddyfile' )
        {
            echo $this->caddyfileText();
            return 0;
        }
        if ( !in_array( $sub, array( 'version', 'list-modules', 'build-info', 'validate', 'adapt', 'environ' ), true ) )
        {
            fwrite( STDERR, "Usage: ctl caddyfile|validate|adapt|version|list-modules|build-info|environ\n" );
            return 1;
        }
        $binary = $this->binary();
        if ( !is_file( $binary ) )
        {
            fwrite( STDERR, "no FrankenPHP binary at $binary -- run exp:velocity install\n" );
            return 1;
        }
        if ( $sub === 'validate' || $sub === 'adapt' )
        {
            if ( $this->writeCaddyfile() === false )
            {
                fwrite( STDERR, 'could not write ' . $this->caddyfile() . "\n" );
                return 1;
            }
            $args = array_merge( $args, array( '--config', $this->caddyfile(), '--adapter', 'caddyfile' ) );
        }
        passthru( implode( ' ', array_map( 'escapeshellarg', array_merge( array( $binary ), $args ) ) ), $code );
        return (int)$code;
    }

    /**
     * What the server is doing: the keys every engine reports, plus what only
     * this one knows.
     *
     * @return array
     */
    public function status()
    {
        $status = parent::status();
        $pids = $status['pids'];
        $threads = 0;
        foreach ( $pids as $pid )
            $threads += count( glob( '/proc/' . (int)$pid . '/task/*', GLOB_ONLYDIR ) ?: array() );

        $path = trim( (string)$this->frankenSetting( 'BinaryPath', '' ) );
        return array_merge( $status, array(
            'server'         => 'frankenphp',
            'version'        => $this->binaryVersion(),
            'binary'         => $this->binary(),
            'binarySource'   => $path !== '' ? 'BinaryPath' : 'download',
            'threads'        => $threads,
            'caddyfile'      => $this->caddyfile(),
            'admin'          => $this->adminAddress(),
            'adminReachable' => $pids ? $this->adminRequest( 'GET', '/config/' ) === 200 : false,
            'notes'          => $this->ignoredSettings(),
        ) );
    }
}
