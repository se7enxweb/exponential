<?php
/**
 * File containing the ezpFormTokenRefusal class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Turns an ezpFormTokenException -- a POST whose form token was missing or
 * wrong -- into a refusal: HTTP 403, Cache-Control: no-store, the security
 * headers, a page or a JSON body, and one warning line. No redirect, and
 * neither the session nor the posted data is touched.
 *
 * The web kernel (ezpKernelWeb) answers a refusal from the request/input check
 * with the error module, kernel error eZError::KERNEL_FORM_TOKEN_REFUSED, so
 * the page is design:error/kernel/6.tpl inside the pagelayout, with
 * templateParameters() as $parameters. Until a design has that template, the
 * error view uses fallbackContent(). An XHR or JSON request gets jsonBody()
 * instead. A refusal that escapes anywhere else (the REST and tree menu
 * kernels, a module that runs the check itself, the uncaught exception
 * handler) gets respond(): the same headers, JSON or a plain built-in page.
 *
 * @package kernel
 */
class ezpFormTokenRefusal
{
    /** The kernel error number the refusal page is rendered with */
    const ERROR_NUMBER = eZError::KERNEL_FORM_TOKEN_REFUSED;

    /** The translation context of the built-in wording */
    const I18N_CONTEXT = 'kernel/error/formtoken';

    /** The log file the warning line is written to (in eZDebug's log directory, var/log by default) */
    const LOG_FILE = 'warning.log';

    /** Seconds in which repeats of the same refusal are counted, not written */
    const DEFAULT_COLLAPSE_SECONDS = 60;

    /** The most distinct refusals the collapse state remembers */
    const STATE_MAX_ENTRIES = 500;

    /** @var array|null what the last log() call did, for tests: written, suppressed, line */
    public static $lastLog = null;

    /**
     * Whether the client wants the refusal as JSON: an XHR, a JSON body, an
     * Accept header asking for JSON rather than HTML, or the REST kernel.
     *
     * @param bool $rest the request came in through the REST kernel
     * @return bool
     */
    public static function wantsJson( $rest = false )
    {
        if ( $rest )
            return true;
        $requestedWith = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ? strtolower( trim( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) : '';
        if ( $requestedWith === 'xmlhttprequest' )
            return true;
        $contentType = isset( $_SERVER['CONTENT_TYPE'] ) ? strtolower( $_SERVER['CONTENT_TYPE'] )
            : ( isset( $_SERVER['HTTP_CONTENT_TYPE'] ) ? strtolower( $_SERVER['HTTP_CONTENT_TYPE'] ) : '' );
        if ( preg_match( '#^\s*application/([a-z0-9.+-]*\+)?json\b#', $contentType ) )
            return true;
        $accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? strtolower( $_SERVER['HTTP_ACCEPT'] ) : '';
        if ( strpos( $accept, 'json' ) !== false
            && strpos( $accept, 'text/html' ) === false
            && strpos( $accept, 'application/xhtml+xml' ) === false )
            return true;
        return false;
    }

    /**
     * A URL on this site, as a path and query ('/a/b?c=d'), or '' when $url is
     * empty, on another host, not http(s), or not a plain path. Still to be
     * escaped wherever it is output (|wash in templates).
     *
     * @param string $url absolute or root-relative
     * @return string
     */
    public static function sameSiteURL( $url )
    {
        $url = (string)$url;
        if ( $url === '' || strlen( $url ) > 2048 || preg_match( '/[\x00-\x1f\x7f\\\\]/', $url ) )
            return '';
        $parts = @parse_url( $url );
        if ( !is_array( $parts ) )
            return '';
        if ( isset( $parts['scheme'] ) || isset( $parts['host'] ) )
        {
            $scheme = isset( $parts['scheme'] ) ? strtolower( $parts['scheme'] ) : '';
            if ( $scheme !== 'http' && $scheme !== 'https' )
                return '';
            if ( !isset( $parts['host'] ) || isset( $parts['user'] ) || isset( $parts['pass'] ) )
                return '';
            $ownHost = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( preg_replace( '/:\d+$/', '', $_SERVER['HTTP_HOST'] ) ) : '';
            if ( $ownHost === '' || strtolower( $parts['host'] ) !== $ownHost )
                return '';
        }
        $path = isset( $parts['path'] ) ? $parts['path'] : '';
        if ( $path === '' || $path[0] !== '/' || strncmp( $path, '//', 2 ) === 0 )
            return '';
        return $path . ( isset( $parts['query'] ) && $parts['query'] !== '' ? '?' . $parts['query'] : '' );
    }

    /**
     * The page the refused form was on, when the browser said so and it is on
     * this site; '' otherwise.
     *
     * @return string
     */
    public static function referrer()
    {
        return self::sameSiteURL( isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '' );
    }

    /**
     * Where "reload the form and try again" goes: the referring page, or else
     * the URL the form was posted to, which for most forms shows it again.
     *
     * @return string
     */
    public static function retryURL()
    {
        $referrer = self::referrer();
        if ( $referrer !== '' )
            return $referrer;
        $requested = self::sameSiteURL( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );
        return $requested !== '' ? $requested : '/';
    }

    /**
     * The built-in explanation for the visitor, translated when a translation
     * for self::I18N_CONTEXT exists.
     *
     * The wording is the one of the refusal page (design:error/kernel/6.tpl),
     * the same for a missing and a wrong token: to the visitor both mean the
     * form is no longer valid.
     *
     * @param string $reason ezpFormTokenException::MISSING or ::WRONG
     * @return array title, message, action (the link text)
     */
    public static function texts( $reason )
    {
        try
        {
            // Literal calls, so the translation tools find the strings
            return array(
                'title' => ezpI18n::tr( 'kernel/error/formtoken', 'This form has expired' ),
                'message' => ezpI18n::tr( 'kernel/error/formtoken', 'The page with this form was open for a long time, or the form was sent from another page. To keep your information safe, nothing was saved.' ),
                'action' => ezpI18n::tr( 'kernel/error/formtoken', 'Reload the form and send it again.' ),
            );
        }
        catch ( Throwable $e )
        {
            // No translation system (yet): the English wording
            return array(
                'title' => 'This form has expired',
                'message' => 'The page with this form was open for a long time, or the form was sent from another page. To keep your information safe, nothing was saved.',
                'action' => 'Reload the form and send it again.',
            );
        }
    }

    /**
     * The readable name of the refusal, for the path (breadcrumb) and title.
     *
     * @return string
     */
    public static function pathName()
    {
        try
        {
            return ezpI18n::tr( 'kernel/error/formtoken', 'Form expired' );
        }
        catch ( Throwable $e )
        {
            return 'Form expired';
        }
    }

    /**
     * The template variables of the refusal page, as the error view passes
     * them in $parameters to design:error/kernel/6.tpl.
     *
     * @param ezpFormTokenException $e
     * @return array
     */
    public static function templateParameters( ezpFormTokenException $e )
    {
        $texts = self::texts( $e->getReason() );
        return array(
            'reason' => $e->getReason(),
            'reason_code' => $e->getReasonCode(),
            'referrer' => self::referrer(),
            'retry_url' => self::retryURL(),
            'is_ajax' => self::wantsJson(),
            'title' => $texts['title'],
            'message' => $texts['message'],
            'action' => $texts['action'],
        );
    }

    /**
     * The body an XHR, JSON or REST request is refused with.
     *
     * @param ezpFormTokenException $e
     * @return string
     */
    public static function jsonBody( ezpFormTokenException $e )
    {
        $texts = self::texts( $e->getReason() );
        return json_encode( array(
            'error' => array(
                'code' => 403,
                'reason' => $e->getReasonCode(),
                'message' => $texts['message'],
                'retry_url' => self::retryURL(),
            ),
        ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }

    /**
     * Status 403, never stored anywhere, plus the security headers when they
     * have not been sent with the page headers already.
     *
     * @param string|null $contentType null keeps the one already set
     * @param bool $withSecurityHeaders
     * @return void
     */
    public static function sendHeaders( $contentType = null, $withSecurityHeaders = false )
    {
        if ( headers_sent() )
            return;
        $protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) && preg_match( '#^HTTP/\d(\.\d)?$#', $_SERVER['SERVER_PROTOCOL'] )
            ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        // http_response_code() first: after a header( 'HTTP/...' ) line PHP
        // ignores it and warns ("has no effect"), once per refused form.
        http_response_code( 403 );
        header( $protocol . ' 403 Forbidden' );
        header( 'Status: 403 Forbidden' );
        header( 'Cache-Control: no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        if ( $contentType !== null )
            header( 'Content-Type: ' . $contentType );
        if ( $withSecurityHeaders && class_exists( 'ezpKernelWeb' ) )
        {
            try
            {
                foreach ( ezpKernelWeb::securityHeaders() as $name => $value )
                    header( $name . ': ' . $value );
            }
            catch ( Throwable $ignored )
            {
            }
        }
    }

    /**
     * $parameters completed with what templateParameters() would give, for a
     * caller that raised the error without them.
     *
     * @param array $parameters
     * @return array
     */
    protected static function withDefaults( array $parameters )
    {
        $reason = isset( $parameters['reason'] ) && $parameters['reason'] === ezpFormTokenException::WRONG
            ? ezpFormTokenException::WRONG : ezpFormTokenException::MISSING;
        return $parameters + self::texts( $reason ) + array( 'retry_url' => self::retryURL() );
    }

    /**
     * The explanation the error view shows inside the pagelayout while the
     * design has no design:error/kernel/6.tpl.
     *
     * @param array $parameters templateParameters()
     * @return string HTML
     */
    public static function fallbackContent( array $parameters )
    {
        $parameters = self::withDefaults( $parameters );
        $esc = function ( $v ) { return htmlspecialchars( (string)$v, ENT_QUOTES, 'UTF-8' ); };
        return '<div class="message-warning form-token-refused">'
            . '<h2>' . $esc( $parameters['title'] ) . '</h2>'
            . '<p>' . $esc( $parameters['message'] ) . '</p>'
            . '<p><a href="' . $esc( $parameters['retry_url'] ) . '">' . $esc( $parameters['action'] ) . '</a></p>'
            . '</div>';
    }

    /**
     * A whole page, for a refusal answered outside the pagelayout.
     *
     * @param array $parameters templateParameters()
     * @return string HTML
     */
    public static function standalonePage( array $parameters )
    {
        $parameters = self::withDefaults( $parameters );
        $esc = function ( $v ) { return htmlspecialchars( (string)$v, ENT_QUOTES, 'UTF-8' ); };
        return '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<meta name="robots" content="noindex"><title>' . $esc( $parameters['title'] ) . '</title>'
            . '<style>body{font-family:system-ui,sans-serif;max-width:40rem;margin:10vh auto;padding:0 1rem;text-align:center;color:#111}'
            . 'h1{font-size:2rem}a{display:inline-block;margin-top:1rem;padding:.7rem 1.4rem;background:#FED82F;color:#000;font-weight:700;text-decoration:none}</style>'
            . '</head><body><p style="font-size:4rem;font-weight:700;margin:0">403</p>'
            . '<h1>' . $esc( $parameters['title'] ) . '</h1><p>' . $esc( $parameters['message'] ) . '</p>'
            . '<a href="' . $esc( $parameters['retry_url'] ) . '">' . $esc( $parameters['action'] ) . '</a></body></html>';
    }

    /**
     * Answers a refusal on its own, for every place that is not the web
     * kernel's request/input check: logs it, sends the headers and returns the
     * body (JSON or the built-in page).
     *
     * @param ezpFormTokenException $e
     * @param bool $rest the REST kernel: always JSON
     * @return string the body, to be output by the caller
     */
    public static function respond( ezpFormTokenException $e, $rest = false )
    {
        self::log( $e );
        if ( self::wantsJson( $rest ) )
        {
            self::sendHeaders( 'application/json; charset=utf-8', true );
            return self::jsonBody( $e );
        }
        self::sendHeaders( 'text/html; charset=utf-8', true );
        return self::standalonePage( self::templateParameters( $e ) );
    }

    /**
     * The seconds within which the same refusal is counted rather than
     * written again: site.ini [HTMLForms] RefusalLogCollapseSeconds, 0 writes
     * every one.
     *
     * @return int
     */
    public static function collapseSeconds()
    {
        try
        {
            $ini = eZINI::instance();
            if ( $ini->hasVariable( 'HTMLForms', 'RefusalLogCollapseSeconds' ) )
                return max( 0, (int)$ini->variable( 'HTMLForms', 'RefusalLogCollapseSeconds' ) );
        }
        catch ( Throwable $ignored )
        {
        }
        return self::DEFAULT_COLLAPSE_SECONDS;
    }

    /**
     * Writes the one warning line of a refusal: which check failed, the
     * method, the path (without the query) and the siteaccess, never a token.
     * The same refusal again (same check, siteaccess, path and client) within
     * collapseSeconds() is only counted; the next line written for it says how
     * many were left out.
     *
     * @param ezpFormTokenException $e
     * @return bool whether a line was written
     */
    public static function log( ezpFormTokenException $e )
    {
        $siteaccess = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
        $method = isset( $_SERVER['REQUEST_METHOD'] ) && preg_match( '/^[A-Z]{1,10}$/', $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'POST';
        $path = isset( $_SERVER['REQUEST_URI'] ) ? (string)parse_url( (string)$_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
        $path = preg_replace( '/[\x00-\x20\x7f]/', '', $path );
        if ( strlen( $path ) > 200 )
            $path = substr( $path, 0, 200 ) . '...';
        $ip = class_exists( 'eZSys' ) ? (string)eZSys::clientIP() : '';

        $key = sha1( $e->getReason() . "\n" . $siteaccess . "\n" . $path . "\n" . $ip );
        $seconds = self::collapseSeconds();
        $previous = $seconds > 0 ? self::collapse( $key, time(), $seconds ) : array( 'count' => 0, 'since' => 0 );
        if ( $previous === null )
        {
            self::$lastLog = array( 'written' => false, 'suppressed' => true, 'line' => '' );
            return false;
        }

        $line = 'Form token refused (' . $e->getReason() . ' token): ' . $method . ' ' . ( $path !== '' ? $path : '/' )
            . ', siteaccess ' . ( $siteaccess !== '' ? $siteaccess : '-' ) . ', answered 403';
        if ( $previous['count'] > 0 )
            $line .= sprintf( '; %d more like it since %s were not logged', $previous['count'], date( 'H:i:s', $previous['since'] ) );

        self::writeLine( $line, $ip );
        self::$lastLog = array( 'written' => true, 'suppressed' => false, 'line' => $line );
        return true;
    }

    /**
     * The file the collapse state is kept in, shared by every process.
     *
     * @return string
     */
    public static function stateFile()
    {
        return eZSys::cacheDirectory() . '/formtoken/refusals.json';
    }

    /**
     * Records one refusal in the shared state. Returns null when the same one
     * was written less than $seconds ago (count it, write nothing), otherwise
     * what was counted for it since its last line.
     *
     * @param string $key
     * @param int $now
     * @param int $seconds
     * @return array|null count, since
     */
    protected static function collapse( $key, $now, $seconds )
    {
        $file = self::stateFile();
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
            @eZDir::mkdir( $dir, false, true );
        $existed = is_file( $file );
        $fp = @fopen( $file, 'c+' );
        if ( !$fp )
            return array( 'count' => 0, 'since' => 0 );
        if ( !$existed )
            self::chmodLike( $file );
        if ( !@flock( $fp, LOCK_EX ) )
        {
            fclose( $fp );
            return array( 'count' => 0, 'since' => 0 );
        }
        $state = json_decode( (string)stream_get_contents( $fp ), true );
        if ( !is_array( $state ) )
            $state = array();

        // What is older than the window is only kept while it has a count to report
        foreach ( $state as $k => $entry )
        {
            if ( !is_array( $entry ) || !isset( $entry['at'], $entry['n'] ) )
                unset( $state[$k] );
            else if ( $entry['at'] < $now - 86400 || ( $entry['n'] == 0 && $entry['at'] < $now - $seconds ) )
                unset( $state[$k] );
        }

        $result = null;
        if ( isset( $state[$key] ) && $now - (int)$state[$key]['at'] < $seconds )
        {
            $state[$key]['n'] = (int)$state[$key]['n'] + 1;
        }
        else
        {
            $result = array(
                'count' => isset( $state[$key] ) ? (int)$state[$key]['n'] : 0,
                'since' => isset( $state[$key] ) ? (int)$state[$key]['at'] : 0,
            );
            $state[$key] = array( 'at' => $now, 'n' => 0 );
        }

        if ( count( $state ) > self::STATE_MAX_ENTRIES )
        {
            uasort( $state, function ( $a, $b ) { return $b['at'] - $a['at']; } );
            $state = array_slice( $state, 0, self::STATE_MAX_ENTRIES, true );
        }

        ftruncate( $fp, 0 );
        rewind( $fp );
        fwrite( $fp, json_encode( $state ) );
        fflush( $fp );
        flock( $fp, LOCK_UN );
        fclose( $fp );
        return $result;
    }

    /**
     * Appends one line to the warning.log of eZDebug (var/log/warning.log by default), in eZDebug's format.
     *
     * @param string $line
     * @param string $ip
     * @return void
     */
    protected static function writeLine( $line, $ip )
    {
        // Where eZDebug writes its warning.log, beside error.log: var/log, or the log directory of the site with
        // site.ini [FileSettings] UseGlobalLogDir=disabled
        $dir = class_exists( 'eZDebug' ) ? rtrim( eZDebug::instance()->logDirectory(), '/' ) : 'var/log';
        $file = $dir . '/' . self::LOG_FILE;
        if ( !is_dir( $dir ) )
            @eZDir::mkdir( $dir, false, true );
        clearstatcache( true, $file );
        $existed = is_file( $file );
        if ( $existed && class_exists( 'eZDebug' ) && filesize( $file ) > eZDebug::maxLogSize() )
        {
            if ( eZDebug::rotateLog( $file ) )
                $existed = false;
        }
        if ( $ip === '' )
            $ip = (string)eZSys::serverVariable( 'HOSTNAME', true );
        $written = @file_put_contents( $file, '[ ' . date( 'M d Y H:i:s' ) . ' ] [' . $ip . '] ' . $line . "\n", FILE_APPEND | LOCK_EX );
        if ( $written !== false && !$existed )
            self::chmodLike( $file );
    }

    /**
     * Gives a new file the permissions of a log file, so the web server and a
     * persistent worker running as another user can both write it.
     *
     * @param string $file
     * @return void
     */
    protected static function chmodLike( $file )
    {
        try
        {
            $permissions = octdec( eZINI::instance()->variable( 'FileSettings', 'LogFilePermissions' ) );
            if ( $permissions )
                @chmod( $file, eZFile::fileMode( $permissions ) );
        }
        catch ( Throwable $ignored )
        {
        }
    }
}
