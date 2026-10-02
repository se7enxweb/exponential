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
*/

class eZExecution
{
    /*!
     Sets the clean exit flag to on,
     this notifies the exit handler that everything finished properly.
    */
    static function setCleanExit( $hasCleanExit = true )
    {
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
        self::$cleanupHandlers[] = $handler;
    }

    /*!
     \return An array with cleanup handlers.
    */
    static function cleanupHandlers()
    {
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
        self::$fatalErrorHandlers[] = $handler;
    }

    /*!
     \return An array with fatal error handlers.
    */
    static function fatalErrorHandlers()
    {
        return self::$fatalErrorHandlers;
    }

    /*!
     \return true if the request finished properly.
    */
    static function isCleanExit()
    {
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
        if ( self::$eZDocumentRoot !== null )
        {
            chdir( self::$eZDocumentRoot );
        }

        if ( eZExecution::isCleanExit() )
        {
            chdir( $previousCwd );
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

        chdir( $previousCwd );
    }

    /*!
     Register ::uncleanShutdownHandler as shutdown function
    */
    static public function registerShutdownHandler( $documentRoot = false )
    {
        if ( !self::$shutdownHandle )
        {
            register_shutdown_function( array('eZExecution', 'uncleanShutdownHandler') );
            /*
                see:
                - http://www.php.net/manual/en/function.session-set-save-handler.php
                - http://bugs.php.net/bug.php?id=33635
                - http://bugs.php.net/bug.php?id=33772
            */
            register_shutdown_function( array('eZSession', 'stop') );
            set_exception_handler( array('eZExecution', 'defaultExceptionHandler') );
            self::$shutdownHandle = true;
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
            return in_array( (int) $e->getCode(), array(
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
            ), true );
        }
        return false;
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
    static private $hasCleanExit = false;
    static private $shutdownHandle = false;
    static private $fatalErrorHandlers = array();
    static private $cleanupHandlers = array();
}


?>
