<?php
/**
 * The request log: at most one line per web request for a page that does not exist (404.log), with the reason the
 * kernel found, and the hand-over to the request classifier (security.log) for a request it names a probe.
 *
 * A request ends in one log at most: security.log when the classifier has a verdict (whatever the status), else
 * 404.log for a 404 or 410, else the line the caller always wrote (error.log for a fault, notice.log for 401/403).
 *
 * Flow of a request:
 *   reason()      where the kernel finds why the page is missing (undefined module or view, a module or view this
 *                 siteaccess switches off ...): recorded, the first one wins; returns false while NotFoundLog is
 *                 disabled, and the caller then writes its old error line.
 *   noteView()    the module, view and named parameters of the request, so the line can name its node or object.
 *   handleError() the error module, where it wrote "Error ocurred using URI": classifies the request (once),
 *                 writes the security line or the 404 line and returns true, or false for the caller's old line.
 *   finish()      once at the end of the web request (ezpKernelWeb): classifies a request not classified yet (a 200
 *                 with an injection in its query) and writes what is still pending.
 *
 * site.ini [RequestLogSettings]: NotFoundLog, NotFoundLogFile, MaskIP, MaxURILength. Line format:
 * doc/features/6.0/request-logs.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expRequestLog
{
    const DEFAULT_FILE = '404.log';
    const DEFAULT_MAX_URI = 300;
    const HIDDEN = '***';

    /** @var array|null the first reason: array( 'kind' => ..., 'detail' => ..., 'node' => int|null, 'legacy' => string|null ) */
    protected static $reason = null;

    /** @var array|null module, view and named parameters of the request */
    protected static $view = null;

    /** @var bool whether this request was classified already */
    protected static $classified = false;

    /** @var bool whether the classifier had a verdict (its security line is written) */
    protected static $verdict = false;

    /** @var bool whether this request's line (404, security, or the old one) is written */
    protected static $done = false;

    /** @var int|null the HTTP status the error module answered */
    protected static $status = null;

    /** @var string|null the line last written to the not-found log, for tests */
    protected static $lastLine = null;

    /** @var callable|null test hook: function( $status ) : bool in place of the classifier (true = a verdict, its line written) */
    protected static $classifier = null;

    /** @var string|null test hook: the log directory */
    protected static $logDirectory = null;

    /** @var array|null test hook: the request in place of $_SERVER (method, uri, siteaccess, referrer, ua, ip) */
    protected static $request = null;

    /** @var array|null test hook: the settings in place of site.ini */
    protected static $settings = null;

    /** @var callable|null test hook: function( string $message ) in place of eZDebug::writeError() for the old line */
    protected static $legacyWriter = null;

    /**
     * Forgets the request: called when a request starts (also in a persistent worker) and by the tests. The test
     * hooks (setClassifier(), setLogDirectory(), setRequest(), setSettings()) are kept.
     */
    public static function reset()
    {
        self::$reason = null;
        self::$view = null;
        self::$classified = false;
        self::$verdict = false;
        self::$done = false;
        self::$status = null;
        self::$lastLine = null;
    }

    /**
     * Test hook: a callable function( int|null $status ) : bool that classifies the request in place of
     * expRequestClassifier (true: it found a verdict and wrote its line), or null for the classifier.
     *
     * @param callable|null $classifier
     */
    public static function setClassifier( $classifier )
    {
        self::$classifier = is_callable( $classifier ) ? $classifier : null;
    }

    /**
     * Test hook: the directory the log is written to, or null for eZDebug's log directory.
     *
     * @param string|null $dir
     */
    public static function setLogDirectory( $dir )
    {
        self::$logDirectory = $dir === null ? null : rtrim( (string)$dir, '/' );
    }

    /**
     * Test hook: the request (keys method, uri, siteaccess, referrer, ua, ip), or null for the real one.
     *
     * @param array|null $request
     */
    public static function setRequest( $request )
    {
        self::$request = is_array( $request ) ? $request : null;
    }

    /**
     * Test hook: the settings (keys NotFoundLog, NotFoundLogFile, MaskIP, MaxURILength), or null for site.ini.
     *
     * @param array|null $settings
     */
    public static function setSettings( $settings )
    {
        self::$settings = is_array( $settings ) ? $settings : null;
    }

    /**
     * Test hook: a callable function( string $message ) that receives the old error line finish() writes, or null
     * for eZDebug::writeError().
     *
     * @param callable|null $writer
     */
    public static function setLegacyWriter( $writer )
    {
        self::$legacyWriter = is_callable( $writer ) ? $writer : null;
    }

    /** @return string|null the line last written to the not-found log (without its prefix) */
    public static function lastLine()
    {
        return self::$lastLine;
    }

    /**
     * Records why the request is a not-found: the first reason wins.
     *
     * @param string $kind module, view, disabled, node, object, urlalias
     * @param string $detail the module name, module/view, the id or the path
     * @param int|null $nodeId the node, when known
     * @param string|null $legacyMessage the error line the caller wrote before, written to error.log at the end of
     *        a request that did not end as a 404 (a redirecting error handler), so nothing is lost
     * @return bool true when the not-found log takes it (the caller writes no error line), false while
     *         NotFoundLog is disabled (the caller writes its error line as before)
     */
    public static function reason( $kind, $detail, $nodeId = null, $legacyMessage = null )
    {
        if ( !self::enabled() )
            return false;
        if ( self::$reason === null )
        {
            self::$reason = array( 'kind' => (string)$kind, 'detail' => (string)$detail,
                                   'node' => is_numeric( $nodeId ) ? (int)$nodeId : null,
                                   'legacy' => $legacyMessage !== null ? (string)$legacyMessage : null );
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeDebug( 'Not found: ' . $kind . ':' . $detail, __METHOD__ );
        }
        return true;
    }

    /**
     * Records the module, view and named parameters of the request (the last call before the error wins), so the
     * line can name the node (NodeID) or object (ObjectID) the request asked for.
     *
     * @param string $module
     * @param string $view
     * @param array $parameters
     * @param bool $overwrite false: only when none is noted yet
     */
    public static function noteView( $module, $view, $parameters = array(), $overwrite = true )
    {
        if ( self::$done || (string)$module === '' || (string)$module === 'error' )
            return;
        if ( !$overwrite && self::$view !== null )
            return;
        self::$view = array( 'module' => (string)$module, 'view' => (string)$view,
                             'parameters' => is_array( $parameters ) ? $parameters : array() );
    }

    /**
     * Called by the error module where it wrote "Error ocurred using URI".
     *
     * @param int|string|null $httpCode the HTTP status of the error page (null when error.ini maps none)
     * @param string $errorType kernel, shop ...
     * @param int|string $errorNumber
     * @return bool true when the request's line is written (the security line, the 404 line, or one written
     *         before in this request), false when the caller writes its old notice or error line
     */
    public static function handleError( $httpCode, $errorType, $errorNumber )
    {
        $code = is_numeric( $httpCode ) ? (int)$httpCode : null;
        if ( self::$status === null && $code !== null )
            self::$status = $code;
        if ( self::classify( $code ) )
        {
            self::$done = true;
            return true;
        }
        $notFound = $code === 404 || $code === 410;
        if ( !$notFound || !self::enabled() )
            return false;
        if ( self::$done )
            return true;
        if ( self::$reason === null )
            self::$reason = self::derivedReason( (string)$errorType, (string)$errorNumber );
        self::writeNotFound( $code );
        return true;
    }

    /**
     * Called once at the end of the web request. Classifies the request when nothing did yet, and writes what is
     * pending: the 404 line of a not-found that did not reach the error module, or the old error line of a reason
     * whose request did not end as a not-found.
     *
     * @param int|null $status the final HTTP status, null to take it from the error module or http_response_code()
     * @return void
     */
    public static function finish( $status = null )
    {
        if ( $status === null )
            $status = self::$status;
        if ( $status === null && function_exists( 'http_response_code' ) )
        {
            $current = @http_response_code();
            if ( is_int( $current ) && $current > 0 )
                $status = $current;
        }
        $status = is_numeric( $status ) ? (int)$status : null;

        if ( self::classify( $status ) )
        {
            self::$done = true;
            return;
        }
        if ( self::$done || self::$reason === null )
        {
            self::$done = true;
            return;
        }
        if ( ( $status === 404 || $status === 410 ) && self::enabled() )
            self::writeNotFound( $status );
        else if ( self::$reason['legacy'] !== null )
        {
            if ( self::$legacyWriter !== null )
                call_user_func( self::$legacyWriter, self::$reason['legacy'] );
            else if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( self::$reason['legacy'], 'index' );
        }
        self::$done = true;
    }

    /**
     * Classifies the request once (memoized). The classifier is subagent code of its own and optional: without it,
     * or while it is disabled, nothing is classified.
     *
     * @param int|null $status
     * @return bool whether there is a verdict (its security line written)
     */
    protected static function classify( $status )
    {
        if ( self::$classified )
            return self::$verdict;
        self::$classified = true;
        try
        {
            if ( self::$classifier !== null )
            {
                self::$verdict = (bool)call_user_func( self::$classifier, $status );
            }
            else if ( class_exists( 'expRequestClassifier' ) && class_exists( 'expRequestContext' ) )
            {
                $context = expRequestContext::fromGlobals( $status );
                $verdict = expRequestClassifier::instance()->classify( $context );
                if ( $verdict )
                {
                    expRequestClassifier::log( $verdict, $context );
                    self::$verdict = true;
                }
            }
        }
        catch ( Throwable $e )
        {
            // a classifier that fails never breaks the page; the request is logged as if it found nothing
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'The request classifier failed: ' . $e->getMessage(), __METHOD__ );
            self::$verdict = false;
        }
        return self::$verdict;
    }

    /**
     * The reason of a not-found nobody recorded one for: the node or object the request asked for (error 2 or 3),
     * else the error.
     *
     * @param string $errorType
     * @param string $errorNumber
     * @return array
     */
    protected static function derivedReason( $errorType, $errorNumber )
    {
        $node = self::knownNode();
        $params = self::$view !== null ? self::$view['parameters'] : array();
        if ( $errorType === 'kernel' && ( $errorNumber === '2' || $errorNumber === '3' ) )
        {
            if ( $node !== null )
                return array( 'kind' => 'node', 'detail' => (string)$node, 'node' => $node, 'legacy' => null );
            if ( isset( $params['ObjectID'] ) && is_numeric( $params['ObjectID'] ) )
                return array( 'kind' => 'object', 'detail' => (string)(int)$params['ObjectID'], 'node' => null, 'legacy' => null );
        }
        return array( 'kind' => 'kernel', 'detail' => ( $errorType !== '' ? $errorType : 'kernel' ) . '/' . $errorNumber,
                      'node' => $node, 'legacy' => null );
    }

    /** @return int|null the node of the request, when known */
    protected static function knownNode()
    {
        if ( self::$reason !== null && self::$reason['node'] !== null )
            return self::$reason['node'];
        if ( self::$view !== null && isset( self::$view['parameters']['NodeID'] ) && is_numeric( self::$view['parameters']['NodeID'] )
             && (int)self::$view['parameters']['NodeID'] > 0 )
            return (int)self::$view['parameters']['NodeID'];
        return null;
    }

    /**
     * Writes the 404 line of the request.
     *
     * @param int $status
     */
    protected static function writeNotFound( $status )
    {
        $request = self::request();
        $node = self::knownNode();
        $line = self::notFoundLine( array(
            'status' => $status,
            'method' => $request['method'],
            'uri' => $request['uri'],
            'siteaccess' => $request['siteaccess'],
            'reason' => self::$reason['kind'] . ':' . self::$reason['detail'],
            'node' => $node,
            'referrer' => $request['referrer'],
            'ua' => $request['ua'],
        ), (int)self::setting( 'MaxURILength' ) );
        self::writeLine( $line, self::formatIp( $request['ip'], self::flag( 'MaskIP' ) ) );
        self::$lastLine = $line;
        self::$done = true;
    }

    /**
     * The 404 line without its prefix:
     * 404 GET /site/no-such-page?q=*** siteaccess=site reason=module:no-such-page node=- referrer=- ua=3f2a9c1b "Mozilla/5.0"
     *
     * @param array $r status, method, uri, siteaccess, reason, node, referrer, ua
     * @param int $maxUriLength
     * @return string
     */
    public static function notFoundLine( array $r, $maxUriLength = self::DEFAULT_MAX_URI )
    {
        $status = isset( $r['status'] ) && is_numeric( $r['status'] ) ? (string)(int)$r['status'] : '-';
        $method = isset( $r['method'] ) && preg_match( '/^[A-Z]{1,10}$/', (string)$r['method'] ) ? (string)$r['method'] : '-';
        $siteaccess = self::token( isset( $r['siteaccess'] ) ? $r['siteaccess'] : '', 64 );
        $reason = self::token( isset( $r['reason'] ) ? $r['reason'] : '', 200 );
        $node = isset( $r['node'] ) && is_numeric( $r['node'] ) ? (string)(int)$r['node'] : '-';
        $referrer = isset( $r['referrer'] ) && (string)$r['referrer'] !== '' ? self::formatUri( $r['referrer'], $maxUriLength ) : '-';
        return $status . ' ' . $method . ' ' . self::formatUri( isset( $r['uri'] ) ? $r['uri'] : '', $maxUriLength )
            . ' siteaccess=' . $siteaccess . ' reason=' . $reason . ' node=' . $node
            . ' referrer=' . $referrer . ' ' . self::formatUserAgent( isset( $r['ua'] ) ? $r['ua'] : '' );
    }

    /**
     * A line with the prefix eZLog writes: "[ Oct 17 2026 09:00:00 ] [203.0.x.x] <line>".
     *
     * @param string $line
     * @param string $ip the address as written (masked already)
     * @param int|null $time
     * @return string
     */
    public static function formatLine( $line, $ip, $time = null )
    {
        $ip = preg_replace( '/[^\w.:\-]/', '', (string)$ip );
        return '[ ' . date( 'M d Y H:i:s', $time === null ? time() : (int)$time ) . ' ] [' . ( $ip !== '' ? $ip : '-' ) . '] ' . $line;
    }

    /**
     * An address as written: control characters removed, spaces encoded, the path kept (secrets and e-mail
     * addresses in it masked), every query value as ***, cut at $max characters.
     *
     * @param string $uri
     * @param int $max
     * @return string
     */
    public static function formatUri( $uri, $max = self::DEFAULT_MAX_URI )
    {
        $uri = preg_replace( '/[\x00-\x1f\x7f]/', '', (string)$uri );
        $uri = str_replace( array( ' ', '"' ), array( '%20', '%22' ), $uri );
        if ( $uri === '' )
            return '/';
        $hash = strpos( $uri, '#' );
        if ( $hash !== false )
            $uri = substr( $uri, 0, $hash );
        $query = null;
        $q = strpos( $uri, '?' );
        if ( $q !== false )
        {
            $query = substr( $uri, $q + 1 );
            $uri = substr( $uri, 0, $q );
        }
        $out = self::redact( $uri );
        if ( $query !== null )
        {
            $pairs = array();
            foreach ( explode( '&', $query ) as $pair )
            {
                if ( $pair === '' )
                    continue;
                $eq = strpos( $pair, '=' );
                if ( $eq === false )
                    $pairs[] = self::redact( $pair );
                else
                    $pairs[] = self::redact( substr( $pair, 0, $eq ) ) . '=' . ( $eq + 1 < strlen( $pair ) ? self::HIDDEN : '' );
            }
            $out .= '?' . implode( '&', $pairs );
        }
        $max = (int)$max > 0 ? (int)$max : self::DEFAULT_MAX_URI;
        if ( strlen( $out ) > $max )
            $out = substr( $out, 0, $max ) . '...';
        return $out;
    }

    /**
     * The user agent as written: ua=<first 8 hex of sha1> "<the agent cut to 60 characters>", or ua=- "" without.
     *
     * @param string $ua
     * @return string
     */
    public static function formatUserAgent( $ua )
    {
        $ua = (string)$ua;
        if ( trim( $ua ) === '' )
            return 'ua=- ""';
        $short = preg_replace( '/[\x00-\x1f\x7f"\\\\]/', '', $ua );
        if ( function_exists( 'mb_substr' ) )
            $short = mb_substr( $short, 0, 60, 'UTF-8' );
        else
            $short = substr( $short, 0, 60 );
        return 'ua=' . substr( sha1( $ua ), 0, 8 ) . ' "' . $short . '"';
    }

    /**
     * A client address as written: with its host part hidden when $mask (203.0.x.x, 2001:db8:x:x), as
     * expLogRedactor::ip() writes it.
     *
     * @param string $ip
     * @param bool $mask
     * @return string
     */
    public static function formatIp( $ip, $mask = true )
    {
        $ip = trim( (string)$ip );
        if ( $ip === '' )
            return '-';
        if ( !$mask )
            return $ip;
        if ( class_exists( 'expLogRedactor' ) )
        {
            $redactor = new expLogRedactor( false, true, false );
            $masked = $redactor->ip( $ip );
        }
        else
        {
            $masked = preg_replace( '/^(\d{1,3}\.\d{1,3})\.\d{1,3}\.\d{1,3}$/', '$1.x.x', $ip );
            if ( strpos( $masked, ':' ) !== false )
                $masked = preg_match( '/^((?:[0-9A-Fa-f]{1,4}:){2})/', $masked, $m ) ? $m[1] . 'x:x' : '::x';
        }
        // never the full address, also for a form neither pattern knows
        if ( $masked === $ip && preg_match( '/\d/', $ip ) )
            return 'x';
        return $masked;
    }

    /** @return bool whether 404s are written to the not-found log (NotFoundLog=enabled, the default) */
    public static function enabled()
    {
        return self::flag( 'NotFoundLog' );
    }

    /**
     * The not-found log file with its directory.
     *
     * @return string
     */
    public static function logFile()
    {
        $file = basename( (string)self::setting( 'NotFoundLogFile' ) );
        if ( $file === '' || $file === '.' || $file === '..' )
            $file = self::DEFAULT_FILE;
        return self::logDirectory() . '/' . $file;
    }

    /** @return string the log directory: var/log, or the site's with [FileSettings] UseGlobalLogDir=disabled */
    protected static function logDirectory()
    {
        if ( self::$logDirectory !== null )
            return self::$logDirectory;
        return class_exists( 'eZDebug' ) ? rtrim( eZDebug::instance()->logDirectory(), '/' ) : 'var/log';
    }

    /**
     * Appends a line to the not-found log, rotating it as eZDebug rotates its logs.
     *
     * @param string $line
     * @param string $ip as written
     */
    protected static function writeLine( $line, $ip )
    {
        $dir = self::logDirectory();
        $file = self::logFile();
        if ( !is_dir( $dir ) )
        {
            if ( class_exists( 'eZDir' ) )
                @eZDir::mkdir( $dir, false, true );
            else
                @mkdir( $dir, 0777, true );
        }
        clearstatcache( true, $file );
        $existed = is_file( $file );
        if ( $existed && class_exists( 'eZDebug' ) && filesize( $file ) > eZDebug::maxLogSize() )
        {
            if ( eZDebug::rotateLog( $file ) )
                $existed = false;
        }
        $written = @file_put_contents( $file, self::formatLine( $line, $ip ) . "\n", FILE_APPEND | LOCK_EX );
        if ( $written !== false && !$existed )
            self::chmodLike( $file );
    }

    /**
     * Gives a new log file the permissions of a log file, so the web server and a persistent worker running as
     * another user can both append to it.
     *
     * @param string $file
     */
    protected static function chmodLike( $file )
    {
        try
        {
            $permissions = class_exists( 'eZINI' ) ? octdec( eZINI::instance()->variable( 'FileSettings', 'LogFilePermissions' ) ) : 0666;
            if ( $permissions )
                @chmod( $file, class_exists( 'eZFile' ) ? eZFile::fileMode( $permissions ) : $permissions );
        }
        catch ( Throwable $ignored )
        {
        }
    }

    /**
     * The request: method, uri, siteaccess, referrer, ua, ip.
     *
     * @return array
     */
    protected static function request()
    {
        $defaults = array( 'method' => '', 'uri' => '', 'siteaccess' => '', 'referrer' => '', 'ua' => '', 'ip' => '' );
        if ( self::$request !== null )
            return self::$request + $defaults;
        $ip = '';
        try
        {
            $ip = class_exists( 'eZSys' ) ? (string)eZSys::clientIP() : (string)( $_SERVER['REMOTE_ADDR'] ?? '' );
        }
        catch ( Throwable $ignored )
        {
            $ip = (string)( $_SERVER['REMOTE_ADDR'] ?? '' );
        }
        return array(
            'method' => (string)( $_SERVER['REQUEST_METHOD'] ?? '' ),
            'uri' => (string)( $_SERVER['REQUEST_URI'] ?? '' ),
            'siteaccess' => isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '',
            'referrer' => (string)( $_SERVER['HTTP_REFERER'] ?? '' ),
            'ua' => (string)( $_SERVER['HTTP_USER_AGENT'] ?? '' ),
            'ip' => $ip,
        );
    }

    /**
     * A setting of site.ini [RequestLogSettings], with its default when the group or the variable is missing.
     *
     * @param string $name
     * @return string
     */
    protected static function setting( $name )
    {
        $defaults = array( 'NotFoundLog' => 'enabled', 'NotFoundLogFile' => self::DEFAULT_FILE,
                           'MaskIP' => 'enabled', 'MaxURILength' => (string)self::DEFAULT_MAX_URI );
        if ( self::$settings !== null )
            return isset( self::$settings[$name] ) ? (string)self::$settings[$name] : $defaults[$name];
        try
        {
            if ( class_exists( 'eZINI' ) )
            {
                $ini = eZINI::instance();
                if ( $ini->hasVariable( 'RequestLogSettings', $name ) )
                    return (string)$ini->variable( 'RequestLogSettings', $name );
            }
        }
        catch ( Throwable $ignored )
        {
        }
        return $defaults[$name];
    }

    /**
     * @param string $name
     * @return bool whether a setting is not "disabled" (the defaults are enabled)
     */
    protected static function flag( $name )
    {
        return strtolower( trim( self::setting( $name ) ) ) !== 'disabled';
    }

    /**
     * A value as one token of the line: control characters removed, spaces encoded, cut.
     *
     * @param string $value
     * @param int $max
     * @return string
     */
    protected static function token( $value, $max )
    {
        $value = preg_replace( '/[\x00-\x1f\x7f"]/', '', (string)$value );
        $value = str_replace( ' ', '%20', $value );
        if ( $value === '' )
            return '-';
        return strlen( $value ) > $max ? substr( $value, 0, $max ) . '...' : $value;
    }

    /**
     * Secrets and e-mail addresses in a part of an address hidden (expLogRedactor, when there).
     *
     * @param string $text
     * @return string
     */
    protected static function redact( $text )
    {
        if ( $text === '' || !class_exists( 'expLogRedactor' ) )
            return preg_replace( '/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,24}/', self::HIDDEN, (string)$text );
        $redactor = new expLogRedactor( true, false, true );
        return $redactor->text( $text );
    }
}
