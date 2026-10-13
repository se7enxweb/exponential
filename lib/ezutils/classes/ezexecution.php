<?php
/**
 * File containing the eZExecution class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZExecution ezexecution.php
  \brief Handles proper script execution, fatal error detection and handling.

  By registering a fatal error handler it's possible for the PHP script to
  catch fatal errors, such as "Call to a member function on a non-object".

  By registering a cleanup handler it's possible to make sure the script can
  end properly.

  The handlers, the clean exit flag and the shutdown functions belong to one
  request, told apart by REQUEST_TIME_FLOAT (requestKey()): the first call in a
  new request starts from what a fresh process has. Under Apache/PHP-FPM and on
  the command line a process serves one request, so nothing changes there. A
  persistent Exponential Velocity worker serves many, puts this class's statics
  back to what they were after its warm-up between them and runs and forgets the
  request's shutdown functions at its end, so without this every request after a
  worker's first ran with the warm-up's handlers plus its own, and with no
  shutdown function at all: no clean-up and no error page after a fatal error,
  and no eZSession::stop().
*/

class eZExecution
{
    /*!
     Sets the clean exit flag to on,
     this notifies the exit handler that everything finished properly.
    */
    static function setCleanExit( $hasCleanExit = true )
    {
        self::forRequest();
        self::$hasCleanExit = $hasCleanExit;
    }

    /*!
     Calls the cleanup handlers to make sure that the script is ready to exit.
    */
    static function cleanup()
    {
        $handlers = eZExecution::cleanupHandlers();
        foreach ( $handlers as $handler )
        {
            if ( is_callable( $handler ) )
                call_user_func( $handler );
            else
                eZDebug::writeError('Could not call cleanup handler, is it a static public function?', __METHOD__ );
        }
    }

    /*!
     Adds a cleanup handler to the end of the list,
     \a $handler must contain the name of the function to call.
     The function is called at the end of the script execution to
     do some cleanups.
    */
    static function addCleanupHandler( $handler )
    {
        self::registerShutdownHandler();
        // once per request: a handler added again (code that registers on every call, or behind a flag that
        // outlived its request) runs once
        if ( !in_array( $handler, self::$cleanupHandlers, true ) )
            self::$cleanupHandlers[] = $handler;
    }

    /*!
     \return An array with cleanup handlers.
    */
    static function cleanupHandlers()
    {
        self::forRequest();
        return self::$cleanupHandlers;
    }

    /*!
     Adds a fatal error handler to the end of the list,
     \a $handler must contain the name of the function to call.
     The handler will be called whenever a fatal error occurs,
     which usually happens when the script did not finish.
    */
    static function addFatalErrorHandler( $handler )
    {
        self::registerShutdownHandler();
        if ( !in_array( $handler, self::$fatalErrorHandlers, true ) )
            self::$fatalErrorHandlers[] = $handler;
    }

    /*!
     \return An array with fatal error handlers.
    */
    static function fatalErrorHandlers()
    {
        self::forRequest();
        return self::$fatalErrorHandlers;
    }

    /*!
     \return true if the request finished properly.
    */
    static function isCleanExit()
    {
        self::forRequest();
        return self::$hasCleanExit;
    }

    /*!
     Sets the clean exit flag and exits the page.
     Use this if you want premature exits instead of the \c exit function.
    */
    static function cleanExit()
    {
        eZExecution::cleanup();
        eZExecution::setCleanExit();
        exit;
    }

    /**
     * Throws away the output printed so far, buffer by buffer, down to $floor: for a view that answers with its
     * own body (a download, a plain answer, an event stream) instead of the page.
     *
     * A buffer that cannot be removed is emptied instead and left in place. A persistent worker (Velocity) keeps
     * one under the script for the whole process and collects the response in it. A loop on ob_get_level() alone
     * never ends at such a buffer: the request hangs until the server gives up. Call this instead of
     * `while ( ob_get_level() > 0 ) ob_end_clean();`.
     *
     * @param int $floor the buffer level to stop at (0: all of them)
     * @return bool nothing printed before is left (the buffers above $floor are gone or empty)
     */
    static function discardOutputBuffers( $floor = 0 )
    {
        $floor = max( 0, (int)$floor );
        $guard = 0;
        while ( ob_get_level() > $floor && $guard++ < 64 )
        {
            if ( !@ob_end_clean() )
                break;
        }
        if ( ob_get_level() > $floor )
        {
            @ob_clean();
            return (int)ob_get_length() === 0;
        }
        return true;
    }

    /*!
     Exit handler which called after the script is done, if it detects
     that eZ Publish did not exit cleanly it will issue an error message
     and display the debug.
    */
    static function uncleanShutdownHandler()
    {
        // Need to change the current directory, since this information is lost
        // when the callbackfunction is called. eZDocumentRoot is set in ::registerShutdownHandler
        // Getting the previous current working directory as we might need to get back there (i.e. Symfony web/ directory).
        $previousCwd = getcwd();
        $restorePreviousCwd = false;
        if ( self::$eZDocumentRoot !== null && $previousCwd !== self::$eZDocumentRoot )
        {
            chdir( self::$eZDocumentRoot );
            $restorePreviousCwd = is_string( $previousCwd );
        }

        if ( eZExecution::isCleanExit() )
        {
            // Under LiteSpeed (lsapi) shutdown runs in the PHP binary's
            // directory, which open_basedir does not allow: going back there
            // fails and only matters to a caller that set its own directory.
            if ( $restorePreviousCwd )
                @chdir( $previousCwd );
            return;
        }

        eZExecution::cleanup();
        $handlers = eZExecution::fatalErrorHandlers();
        foreach ( $handlers as $handler )
        {
            if ( is_callable( $handler ) )
                call_user_func( $handler );
            else

                eZDebug::writeError('Could not call fatal error handler, is it a static public function?', __METHOD__ );
        }

        if ( $restorePreviousCwd )
            @chdir( $previousCwd );
    }

    /*!
     Register ::uncleanShutdownHandler as shutdown function
    */
    static public function registerShutdownHandler( $documentRoot = false )
    {
        self::forRequest();
        if ( !self::$shutdownHandle )
        {
            // Tied to this request: a persistent worker can still hold the shutdown functions of a request that
            // has ended (its warm-up's, which it never ran), and those must not run in this one.
            $request = self::requestKey();
            register_shutdown_function( array('eZExecution', 'runForRequest'), $request, array('eZExecution', 'uncleanShutdownHandler') );
            /*
                see:
                - http://www.php.net/manual/en/function.session-set-save-handler.php
                - http://bugs.php.net/bug.php?id=33635
                - http://bugs.php.net/bug.php?id=33772
            */
            register_shutdown_function( array('eZExecution', 'runForRequest'), $request, array('eZSession', 'stop') );
            // The process's first registration installs the handler, as it always did. A later request's (a
            // persistent worker's) installs it when none is in place, see below: the worker has taken the last
            // request's handler away by then, and a handler this request's own code installed first is kept.
            if ( !self::$handlerInstalled || self::currentExceptionHandler() === null )
                set_exception_handler( array('eZExecution', 'defaultExceptionHandler') );
            self::$handlerInstalled = true;
            self::$shutdownHandle = true;
        }
        else if ( self::currentExceptionHandler() === null )
        {
            // A persistent worker (Exponential Velocity) takes away the exception handler a request installed once the
            // request has ended, while the flag above lives on in this class's statics: without this every request
            // after the first one of a worker had no handler, and an uncaught exception got the server's own error
            // page and no line in error.log. Installed again only when no handler is in place, so a handler that
            // other code installed after this one stays.
            set_exception_handler( array('eZExecution', 'defaultExceptionHandler') );
        }

        // Needed by the error handler, since the current directory is lost when
        // the callback function eZExecution::uncleanShutdownHandler is called.
        if ( $documentRoot )
        {
            self::$eZDocumentRoot = $documentRoot;
        }
        else if ( self::$eZDocumentRoot === null )
        {
            self::$eZDocumentRoot = getcwd();
        }
    }

    /**
     * The request the registrations belong to: REQUEST_TIME_FLOAT, which a persistent Velocity worker sets anew
     * for every request and which stays the same for the whole of a request under Apache/PHP-FPM and on the
     * command line. '' when it is not set (the registrations then belong to the process, as they always did).
     *
     * @return string
     */
    static public function requestKey()
    {
        return isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
    }

    /**
     * A shutdown function registered by registerShutdownHandler(): calls $callback when the request that
     * registered it is still the current one, and does nothing for an earlier request.
     *
     * @param string $request requestKey() of the request that registered it
     * @param callable $callback
     * @return void
     */
    static public function runForRequest( $request, $callback )
    {
        if ( (string)$request !== self::requestKey() )
            return;
        if ( is_callable( $callback ) )
            call_user_func( $callback );
    }

    /**
     * Starts a new request's registrations when the request is not the one they belong to: no handlers, no
     * clean exit, no shutdown functions, as in a fresh process. The first call in a process only notes the
     * request; everything still has its declared value then.
     */
    static private function forRequest()
    {
        $request = self::requestKey();
        if ( self::$requestKey === $request )
            return;
        if ( self::$requestKey !== null )
        {
            self::$hasCleanExit = false;
            self::$shutdownHandle = false;
            self::$fatalErrorHandlers = array();
            self::$cleanupHandlers = array();
        }
        self::$requestKey = $request;
    }

    /**
     * The exception handler in place, read without changing it (a handler is pushed and popped again, so PHP's
     * stack of handlers is as it was).
     *
     * @return callable|null null when none is installed
     */
    static public function currentExceptionHandler()
    {
        $current = set_exception_handler( static function ( $e ) {} );
        restore_exception_handler();
        return $current;
    }

    /**
     * Installs the default Exception handler
     *
     * @params Exception|Throwable the exception
     * @return void
     */
    static public function defaultExceptionHandler( $e )
    {
        // A refused form token is the visitor's 403, not our fault: no
        // "Unexpected error" in error.log, one warning line instead
        if ( $e instanceof ezpFormTokenException && self::isWebRequest() )
        {
            echo ezpFormTokenRefusal::respond( $e );
            eZExecution::cleanup();
            eZExecution::setCleanExit();
            exit( 1 );
        }

        if( self::isWebRequest() )
        {
            // A database that cannot be reached is temporary and says so;
            // anything else is ours.
            $status = self::isDatabaseUnavailable( $e ) ? 503 : 500;
            $reference = self::errorReference();
            self::renderErrorPage( $status, $reference,
                eZDebug::isDebugEnabled() ? $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() : '' );
            eZLog::write( 'Unexpected error ' . $reference . ', the message was : ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine(), 'error.log' );
            eZExecution::cleanup();
            eZExecution::setCleanExit();
            exit( 1 );
        }
        else
        {
            $cli = eZCLI::instance();
            $cli->error( "An unexpected error has occurred. Please contact the webmaster.");

            if( eZDebug::isDebugEnabled() )
            {
                $cli->error( $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() );
            }
        }

        eZLog::write( 'Unexpected error, the message was : ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine(), 'error.log' );

        eZExecution::cleanup();
        eZExecution::setCleanExit();
        exit( 1 );
    }

    /**
     * Whether this is a request from a browser rather than a shell script.
     *
     * php_sapi_name() alone cannot say: a PHP web server running in the CLI
     * SAPI (the built-in server, Velocity) answers browsers as 'cli', and the
     * error handlers that trusted it wrote their message to the server's
     * stderr and sent the visitor an empty page. A request method and a Host
     * header are what a web request has and a shell script does not.
     *
     * @return bool
     */
    static public function isWebRequest()
    {
        if ( PHP_SAPI !== 'cli' && strncmp( PHP_SAPI, 'cgi', 3 ) !== 0 )
            return true;
        return !empty( $_SERVER['REQUEST_METHOD'] ) && !empty( $_SERVER['HTTP_HOST'] );
    }

    /**
     * Whether an exception means the database could not be reached or used
     * at all -- refused, unknown host, wrong password, host not allowed, too
     * many connections, gone away -- rather than a fault in the page.
     *
     * With mysqli reporting errors as exceptions (PHP 8.1's default) a failed
     * connect throws mysqli_sql_exception before eZDBNoConnectionException can
     * be made, so the MySQL/MariaDB connection error codes are checked too.
     *
     * @param Throwable $e
     * @return bool
     */
    static public function isDatabaseUnavailable( $e )
    {
        if ( $e instanceof eZDBNoConnectionException )
            return true;
        if ( class_exists( 'mysqli_sql_exception', false ) && $e instanceof mysqli_sql_exception )
        {
            return in_array( (int) $e->getCode(), self::mysqlUnavailableCodes(), true );
        }
        // A statement that failed because the connection went away: the drivers' eZDBException carries the
        // driver's error number (MySQL with the exception error handling no longer lets mysqli_sql_exception through)
        if ( class_exists( 'eZDBException', false ) && $e instanceof eZDBException )
        {
            $message = isset( $e->originalMessage ) ? (string)$e->originalMessage : (string)$e->getMessage();
            return self::isDatabaseUnavailableCode( $e->getCode(), $message );
        }
        return false;
    }

    /**
     * Whether a driver's error number means the database could not be reached or used at all:
     *   - PostgreSQL (the SQLSTATE, a string): class 08 (connection exception), 57P01 to 57P03 (the server shuts
     *     down or is starting), 53300 (too many connections)
     *   - Oracle (the ORA number; told apart by the "ORA-" message): 1033, 1034, 1089, 3113, 3114, 3135, 12170,
     *     12514, 12528, 12537, 12541, 12543
     *   - MySQL/MariaDB: the numbers of mysqlUnavailableCodes()
     *   - SQLite: 14 (cannot open the file), 26 (not a database)
     * The ranges of MySQL (1000 and above) and SQLite (below 100) do not overlap; Oracle's numbers do overlap
     * MySQL's, so they count only with an Oracle message.
     *
     * @param int|string $code
     * @param string $message the driver's message
     * @return bool
     */
    static public function isDatabaseUnavailableCode( $code, $message = '' )
    {
        // PostgreSQL's SQLSTATE is a string of five characters; the other drivers' numbers are integers
        if ( is_string( $code ) && preg_match( '/^[0-9A-Z]{5}$/', $code ) )
        {
            return strncmp( $code, '08', 2 ) === 0 || in_array( $code, array( '57P01', '57P02', '57P03', '53300' ), true );
        }
        if ( !is_int( $code ) )
            return false;
        if ( strncmp( ltrim( (string)$message ), 'ORA-', 4 ) === 0 )
        {
            return in_array( $code, array( 1033, 1034, 1089, 3113, 3114, 3135, 12170, 12514, 12528, 12537, 12541, 12543 ), true );
        }
        return in_array( $code, self::mysqlUnavailableCodes(), true ) || in_array( $code, array( 14, 26 ), true );
    }

    /**
     * The MySQL/MariaDB error numbers of a database that cannot be reached or used.
     *
     * @return int[]
     */
    static public function mysqlUnavailableCodes()
    {
        return array(
            1040, // too many connections
            1044, // access denied to the database
            1045, // access denied (user or password)
            1049, // unknown database
            1129, // host blocked after too many errors
            1130, // host not allowed to connect
            2002, // cannot connect (socket, refused)
            2003, // cannot connect (TCP)
            2005, // unknown host
            2006, // server has gone away
            2013, // lost connection during query
        );
    }

    /**
     * A short reference for one failure, shown to the visitor and written to
     * the log beside the details, so a report can be matched to its log line.
     *
     * @return string
     */
    static public function errorReference()
    {
        return 'ERR-' . strtoupper( substr( md5( uniqid( '', true ) . mt_rand() ), 0, 10 ) );
    }

    /**
     * Send the site's error page for a failure that happened outside the
     * template engine: an uncaught exception, a fatal error, a failed database
     * transaction. It must work with no database and no templates, so it reads
     * a static page named in error.ini [ErrorSettings] StaticErrorPage[<status>]
     * (placeholders {status}, {title}, {message}, {reference}, {home}, {detail})
     * and falls back to a plain built-in page.
     *
     * @param int $status 500 or 503
     * @param string $reference shown to the visitor, e.g. from errorReference()
     * @param string $detail technical detail, only passed when debug output is on
     * @return void
     */
    static public function renderErrorPage( $status, $reference = '', $detail = '' )
    {
        $case = self::errorCase();
        if ( $case !== '' )
            return self::renderErrorCasePage( $case, $reference, $detail );

        $status = (int) $status === 503 ? 503 : 500;
        $texts = array(
            500 => array( 'Internal Server Error', 'Something went wrong on our side',
                          'The page could not be finished because of a problem on the server. It has been logged. Please try again in a moment; if it keeps happening, let us know and quote the reference below.' ),
            503 => array( 'Service Unavailable', "We'll be right back",
                          "The site can't reach its content right now. This is usually over within a minute or two, so please try again shortly." ),
        );
        list( $reason, $title, $message ) = $texts[$status];

        if ( !headers_sent() )
        {
            header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1' ) . " $status $reason" );
            header( "Status: $status $reason" );
            header( 'Content-Type: text/html; charset=utf-8' );
            // Never kept by a browser, a proxy or the response cache: the
            // next request is exactly when it may work again.
            header( 'Cache-Control: no-store, max-age=0' );
            if ( $status === 503 )
                header( 'Retry-After: 30' );
        }

        $template = '';
        try
        {
            $ini = eZINI::instance( 'error.ini' );
            $pages = $ini->hasVariable( 'ErrorSettings', 'StaticErrorPage' ) ? $ini->variable( 'ErrorSettings', 'StaticErrorPage' ) : array();
            $file = isset( $pages[$status] ) ? $pages[$status] : ( isset( $pages['default'] ) ? $pages['default'] : '' );
            if ( $file !== '' )
            {
                $root = self::$eZDocumentRoot !== null ? self::$eZDocumentRoot : getcwd();
                $path = ( $file[0] === '/' ) ? $file : $root . '/' . $file;
                if ( is_file( $path ) && is_readable( $path ) )
                    $template = (string) file_get_contents( $path );
            }
        }
        catch ( Throwable $ignored )
        {
            $template = '';
        }
        if ( $template === '' )
        {
            $template = '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
                . '<title>{title}</title><style>body{font-family:system-ui,sans-serif;max-width:40rem;margin:10vh auto;padding:0 1rem;text-align:center;color:#111}'
                . 'h1{font-size:2rem}a{display:inline-block;margin-top:1rem;padding:.7rem 1.4rem;background:#FED82F;color:#000;font-weight:700;text-decoration:none}'
                . 'small{display:block;margin-top:2rem;color:#666}</style></head><body><p style="font-size:4rem;font-weight:700;margin:0">{status}</p>'
                . '<h1>{title}</h1><p>{message}</p><a href="{home}">Go to the home page</a><small>{reference}</small>{detail}</body></html>';
        }

        $esc = function ( $v ) { return htmlspecialchars( (string) $v, ENT_QUOTES, 'UTF-8' ); };
        echo strtr( $template, array(
            '{status}' => $status,
            '{title}' => $esc( $title ),
            '{message}' => $esc( $message ),
            '{reference}' => $reference !== '' ? $esc( 'Reference: ' . $reference ) : '',
            '{home}' => '/',
            '{detail}' => $detail !== '' ? '<pre style="text-align:left;white-space:pre-wrap">' . $esc( $detail ) . '</pre>' : '',
        ) );
    }

    /**
     * The known cause of a failure, when the installation itself is in a state
     * that explains it, so the visitor and the administrator get a page that
     * says what to do instead of the general error page:
     *
     *  'dependencies'  the Composer libraries are not loaded and vendor/autoload.php
     *                  is not there (vendor/ missing, moved, or never installed):
     *                  every class from a library (ezcBaseOptions, ...) is "not found".
     *
     * @return string the case, or '' for none
     */
    static public function errorCase()
    {
        if ( !class_exists( 'Composer\Autoload\ClassLoader', false ) )
        {
            $root = self::$eZDocumentRoot !== null ? self::$eZDocumentRoot : getcwd();
            if ( !is_file( $root . '/vendor/autoload.php' ) && !is_file( dirname( $root ) . '/vendor/autoload.php' ) )
                return 'dependencies';
        }
        return '';
    }

    /**
     * The page for a known error case (errorCase()), a page of its own beside
     * the general one: error.ini [ErrorSettings] StaticErrorPage[<case>] if set
     * (placeholders {status}, {title}, {message}, {steps}, {reference}, {home},
     * {detail}), else a built-in page. Sent with HTTP 503: the site is not broken,
     * it is not installed completely, and it works again once that is done.
     *
     * @param string $case from errorCase()
     * @param string $reference
     * @param string $detail technical detail, only passed when debug output is on
     * @return void
     */
    static public function renderErrorCasePage( $case, $reference = '', $detail = '' )
    {
        $cases = array(
            'dependencies' => array(
                'The site is missing the software libraries it needs',
                'Exponential could not load the libraries it is installed with (the vendor directory of the installation is missing, was moved or renamed, or was never installed). Nothing is wrong with the content; the site works again as soon as the libraries are back.',
                array(
                    'If the vendor directory was moved or renamed, put it back in the installation directory.',
                    'Otherwise install the libraries: in the installation directory on the server, run composer install (add --no-dev on a production server).',
                    'Then clear the caches (php bin/php/ezcache.php --clear-all) and, under Exponential Velocity, restart it.',
                ),
            ),
        );
        if ( !isset( $cases[$case] ) )
            return self::renderErrorPage( 500, $reference, $detail );

        // the page's repair queue answers its own requests (start, status) here
        if ( $case === 'dependencies' )
        {
            require_once __DIR__ . '/ezprepairqueue.php';
            if ( ezpRepairQueue::handleWebRequest() )
                return;
        }
        list( $title, $message, $steps ) = $cases[$case];

        if ( !headers_sent() )
        {
            header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1' ) . ' 503 Service Unavailable' );
            header( 'Status: 503 Service Unavailable' );
            header( 'Content-Type: text/html; charset=utf-8' );
            header( 'Cache-Control: no-store, max-age=0' );
            header( 'Retry-After: 60' );
        }

        $template = '';
        try
        {
            // no eZINI: without the libraries it may not load either; read the setting from the file
            $root = self::$eZDocumentRoot !== null ? self::$eZDocumentRoot : getcwd();
            foreach ( array( 'settings/override/error.ini.append.php', 'settings/error.ini' ) as $ini )
            {
                if ( is_file( "$root/$ini" ) && preg_match( '/^StaticErrorPage\[' . preg_quote( $case, '/' ) . '\]=(.+)$/m', (string) file_get_contents( "$root/$ini" ), $m ) )
                {
                    $file = trim( $m[1] );
                    $path = $file[0] === '/' ? $file : "$root/$file";
                    if ( is_file( $path ) && is_readable( $path ) )
                        $template = (string) file_get_contents( $path );
                    break;
                }
            }
        }
        catch ( Throwable $ignored )
        {
            $template = '';
        }
        if ( $template === '' )
        {
            $template = '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
                . '<title>{title}</title><style>body{font-family:system-ui,sans-serif;max-width:44rem;margin:10vh auto;padding:0 1rem;color:#111}'
                . 'h1{font-size:1.8rem}ol{text-align:left;line-height:1.5}code{background:#f2f2f2;padding:.1rem .3rem}'
                . 'small{display:block;margin-top:2rem;color:#666}.logo{margin:0 0 1.5rem}'
                . 'footer{margin-top:2.5rem;padding-top:1rem;border-top:1px solid #eee;color:#666;font-size:.85rem;text-align:center}footer a{color:#0b4e7a}'
                . '</style></head><body>'
                . '<p class="logo"><img src="/design/admin4/images/admin4/logo-light.png" alt="Exponential" width="200" height="50"></p>'
                . '<p style="font-size:3rem;font-weight:700;margin:0">{status}</p>'
                . '<h1>{title}</h1><p>{message}</p><p><strong>For the administrator:</strong></p>{steps}{repair}<small>{reference}</small>{detail}'
                . '<footer>Powered by <strong>Exponential</strong>. Copyright &copy; 1998-' . date( 'Y' ) . ' <a href="https://se7enx.com">7x</a> and others.</footer>'
                . '</body></html>';
        }

        $esc = function ( $v ) { return htmlspecialchars( (string) $v, ENT_QUOTES, 'UTF-8' ); };
        $list = '<ol>';
        foreach ( $steps as $step )
            $list .= '<li>' . preg_replace( '/(composer install(?: \(add --no-dev[^)]*\))?|php bin\/php\/ezcache\.php --clear-all)/', '<code>$1</code>', $esc( $step ) ) . '</li>';
        $list .= '</ol>';
        echo strtr( $template, array(
            '{status}' => 503,
            '{title}' => $esc( $title ),
            '{message}' => $esc( $message ),
            '{steps}' => $list,
            '{repair}' => $case === 'dependencies' ? ezpRepairQueue::panelHtml() : '',
            '{reference}' => $reference !== '' ? $esc( 'Reference: ' . $reference ) : '',
            '{home}' => '/',
            '{detail}' => $detail !== '' ? '<pre style="white-space:pre-wrap">' . $esc( $detail ) . '</pre>' : '',
        ) );
    }

    static private $eZDocumentRoot = null;
    /** @var string|null requestKey() of the request the four below belong to, null before the first call */
    static private $requestKey = null;
    static private $hasCleanExit = false;
    static private $shutdownHandle = false;
    /** @var bool the exception handler was installed in this process (never reset per request) */
    static private $handlerInstalled = false;
    static private $fatalErrorHandlers = array();
    static private $cleanupHandlers = array();
}


?>
