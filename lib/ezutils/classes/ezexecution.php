<?php
/**
 * File containing the eZExecution class.
 *
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

    static private $eZDocumentRoot = null;
    static private $hasCleanExit = false;
    static private $shutdownHandle = false;
    static private $fatalErrorHandlers = array();
    static private $cleanupHandlers = array();
}


?>
