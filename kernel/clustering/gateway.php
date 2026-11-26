<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Basis for a cluster gateway.
 *
 * Inherited by cluster index gateways
 */
abstract class ezpClusterGateway
{
    /**
     * The active gateway class
     * @var string
     */
    private static $gatewayClass;

    /**
     * Database instance, optional
     *
     * @var mixed
     */
    protected $db;

    /**
     * Database hostname
     *
     * @var string
     */
    protected $host;

    /**
     * Database port
     *
     * @var int
     */
    protected $port;

    /**
     * Database user
     *
     * @var string
     */
    protected $user;

    /**
     * Database password
     *
     * @var string
     */
    protected $password;

    /**
     * Database name
     *
     * @var string
     */
    protected $name;

    /**
     * Database charset
     *
     * @var string
     */
    protected $charset;

    /**
     * Instantiate a gateway with the database parameters.
     *
     * @param array $params Database parameters.
     */
    public function __construct( array $params = array() )
    {
        if ( isset( $params["host"] ) )
            $this->host = $params["host"];

        if ( isset( $params["port"] ) )
            $this->port = $params["port"];

        if ( isset( $params["user"] ) )
            $this->user = $params["user"];

        if ( isset( $params["password"] ) )
            $this->password = $params["password"];

        if ( isset( $params["name"] ) )
            $this->name = $params["name"];

        if ( isset( $params["charset"] ) )
            $this->charset = $params["charset"];
    }

    /**
     * Creates the necessary database connection
     *
     * The database connexion must be usable as is after return, meaning
     * that database connection, charset choice must be set
     *
     * @throws RuntimeException if connection failed
     */
    abstract public function connect();

    /**
     * Fetches file metadata for $filepath
     *
     * @param string $filepath
     *
     * @return array|false
     */
    abstract public function fetchFileMetadata( $filepath );

    /**
     * Passes the $filepath data through
     * @param mixed $db
     * @param string $filepath
     * @param int $filesize
     * @param int $offset
     * @param int $length
     * @return void
     */
    abstract public function passthrough( $filepath, $filesize, $offset = false, $length = false );

    /**
     * Closes any connection that should be closed
     */
    abstract public function close();

    /**
     * @TODO: evaluate whether it is the right place for those methods or if it
     *        should belong to other dedicated classes.
     * @TODO: get rid of constants
     */
    public function retrieve( $filename )
    {
        // connection
        $tries = 0;
        $maxTries = 3;
        while ( true )
        {
            try
            {
                $this->connect();
                break;
            }
            catch ( RuntimeException $e )
            {
                if ( ++$tries === $maxTries )
                {
                    $this->interrupt( $e->getMessage() );
                }
            }
        }

        // Fetch file metadata.
        try
        {
            $metaData = $this->fetchFileMetadata( $filename );
        }
        catch ( RuntimeException $e )
        {
            $this->interrupt( $e->getMessage() );
        }
        if ( $metaData === false || $metaData['mtime'] < 0 )
        {
            // An image of the storage the cluster does not have (an image alias not made yet): made by
            // index_image.php instead of the 404
            if ( $this->handOverMissingImage( $filename ) )
                return;
            $this->interrupt( '', 404, $filename );
        }

        // Output HTTP headers.
        $filesize = $metaData['size'];
        $mtime = $metaData['mtime'];
        $datatype = $metaData['datatype'];

        header( "Content-Type: {$datatype}" );
        // An SVG image opened directly runs nothing and loads nothing
        if ( $datatype === 'image/svg+xml' || preg_match( '/\.svgz?$/i', $filename ) )
        {
            header( "Content-Security-Policy: sandbox; default-src 'none'; style-src 'unsafe-inline'" );
            header( 'X-Content-Type-Options: nosniff' );
        }
        header( "Connection: close" );
        header( 'Served-by: ' . $_SERVER["SERVER_NAME"] );

        if ( CLUSTER_HEADER_X_POWERED_BY !== false )
            header( "X-Powered-By: " . CLUSTER_HEADER_X_POWERED_BY );

        // Request headers: eTag + IF-MODIFIED-SINCE
        if ( CLUSTER_ENABLE_HTTP_CACHE )
        {
            header( "Last-Modified: " . gmdate( 'D, d M Y H:i:s', $mtime ) . ' GMT' );

            if ( CLUSTER_EXPIRY_TIMEOUT !== false )
                header( "Expires: " . gmdate( 'D, d M Y H:i:s', time() + CLUSTER_EXPIRY_TIMEOUT ) . ' GMT' );

            header( "ETag: \"$mtime-$filesize\"" );
            $serverVariables = array_change_key_case( $_SERVER, CASE_UPPER );
            if ( isset( $serverVariables['HTTP_IF_NONE_MATCH'] ) && trim( $serverVariables['HTTP_IF_NONE_MATCH'] ) != "$mtime-$filesize" )
            {
                $this->notModified();
            }

            if ( isset( $serverVariables['HTTP_IF_MODIFIED_SINCE'] ) )
            {
                $value = $serverVariables['HTTP_IF_MODIFIED_SINCE'];

                // strip the garbage prepended by a semi-colon used by some browsers
                if ( ( $pos = strpos( $value , ';' ) ) !== false )
                    $value = substr( $value, 0, $pos );
                if ( strtotime( $value ) <= $mtime )
                {
                    $this->notModified();
                }
            }
        }

        // Request headers:  HTTP Range
        $contentLength = $filesize;
        $startOffset = false;
        if ( CLUSTER_ENABLE_HTTP_RANGE )
        {
            // let the client know we do accept range by bytes
            header( 'Accept-Ranges: bytes' );

            if ( isset( $_SERVER['HTTP_RANGE'] ) && strpos( $_SERVER['HTTP_RANGE'], 'bytes=' ) === 0 && strpos( $_SERVER['HTTP_RANGE'], ',' ) === false )
            {
                $matches = explode( '-', substr( $_SERVER['HTTP_RANGE'], 6 ) );
                $startOffset = $matches[0];
                $endOffset = !empty( $matches[1] ) ? $matches[1] : false;
                if ( $endOffset !== false && empty( $startOffset ) && $startOffset !== "0" )
                {
                    $startOffset = $filesize - $endOffset;
                    $endOffset = $filesize - 1;
                }
                elseif ( $endOffset === false || $endOffset > ( $filesize - 1 ) )
                {
                    $endOffset = $filesize - 1;
                }
                $contentLength = $endOffset - $startOffset + 1;
                if ( $startOffset >= $endOffset )
                {
                    header( "Content-Range: bytes */$filesize" );
                    $this->interrupt( '', 416 );
                }
                else
                {
                    header( "Content-Range: bytes $startOffset-$endOffset/$filesize" );
                    header( "HTTP/1.1 206 Partial Content" );
                }
            }
        }
        else
        {
            header( 'Accept-Ranges: none' );
        }

        header( "Content-Length: $contentLength" );

        // Output file data
        try
        {
            $this->passthrough( $filename, $filesize, $startOffset, $contentLength );
        }
        catch ( Exception $e )
        {
            $this->interrupt( $e->getMessage() );
        }

        $this->close();
    }

    /**
     * Whether a file the cluster has no metadata for goes to index_image.php instead of the 404: an image of the
     * storage (var/<site>/storage/images or images-versioned, no dot segment), while the config constant
     * CLUSTER_IMAGE_ALIAS_ON_DEMAND is not false and index_image.php is there. index_image.php decides what it is:
     * an image alias it may make (settings/image.ini [AliasSettings]), or the placeholder with 404.
     *
     * @param string $filename the storage path, without the leading slash
     * @param string|null $script the entry script (index_image.php in the document root when null)
     * @return bool
     */
    public static function handsOverMissingImage( $filename, $script = null )
    {
        if ( defined( 'CLUSTER_IMAGE_ALIAS_ON_DEMAND' ) && !CLUSTER_IMAGE_ALIAS_ON_DEMAND )
            return false;
        $filename = (string)$filename;
        if ( !preg_match( '#^var/([^/]+/)?storage/images(-versioned)?/[^\x00]+$#', $filename )
             || preg_match( '#(^|/)\.\.?(/|$)#', $filename ) )
            return false;
        return is_file( $script === null ? self::imageEntryScript() : $script );
    }

    /**
     * @return string index_image.php in the document root
     */
    protected static function imageEntryScript()
    {
        return dirname( __DIR__, 2 ) . '/index_image.php';
    }

    /**
     * Hands a storage image the cluster does not have to index_image.php (see handsOverMissingImage()): the
     * connection of the gateway is closed first, then the script runs in this request, with the request as it
     * came (REQUEST_URI names the image).
     *
     * @param string $filename
     * @return bool true when index_image.php answered the request
     */
    protected function handOverMissingImage( $filename )
    {
        if ( !self::handsOverMissingImage( $filename ) )
            return false;
        $this->close();
        $this->runImageEntryScript( self::imageEntryScript() );
        return true;
    }

    /**
     * Runs index_image.php (a method of its own, so a test can see the hand-over without running it).
     *
     * @param string $script
     */
    protected function runImageEntryScript( $script )
    {
        // index_image.php loads autoload.php relative to the document root, as index_cluster.php runs there
        require $script;
    }

    /**
     * Error termination
     *
     * @param string $data
     * @param int $errorCode HTTP error code
     * @param string $filename
     *
     * @return
     */
    private function interrupt( $message, $errorCode = false, $filename = false )
    {
        header( 'Content-Type: text/html' );
        switch ( $errorCode )
        {
            case 404:
                $filename = htmlspecialchars( $filename, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );
                header( $_SERVER['SERVER_PROTOCOL'] . " 404 Not Found" );
                echo <<<EOF
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<H1>Not Found</H1>
<p>The requested URL {$filename} was not found on this server.<p>
</body></html>
EOF;
            exit( 1 );

            case 416:
                header( $_SERVER['SERVER_PROTOCOL'] . " 416 Requested Range Not Satisfiable" );
                echo <<<EOF
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>416 Requested Range Not Satisfiable</title>
</head><body>
<h1>416 Requested Range Not Satisfiable</h1>
</body></html>
EOF;
            exit( 1 );

            case 500:
            default:
                if ( !CLUSTER_ENABLE_DEBUG )
                    $message = "An error has occured";
                header( $_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error" );
                echo <<<EOF
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>500 Internal Server Error</title>
</head><body>
<h1>$message</h1>
</body></html>
EOF;
                // stops the script as a fatal error does; index_cluster.php has no autoloader
                if ( !class_exists( 'eZFatalUserError', false ) )
                    require __DIR__ . '/../../lib/ezutils/classes/ezfatalusererror.php';
                eZFatalUserError::raise( $message );
        }
    }

    private function notModified()
    {
        header( "HTTP/1.1 304 Not Modified" );
        if ( !defined( 'CLUSTER_PERSISTENT_CONNECTION' ) || CLUSTER_PERSISTENT_CONNECTION == false )
        {
            $this->close();
        }
        exit;
    }

    /**
     * Sets the gateway class to $gatewayClass
     *
     * @param string $gatewayClass
     */
    public static function setGatewayClass( $gatewayClass )
    {
        self::$gatewayClass = $gatewayClass;
    }

    /**
     * The gateway class that kernel/clustering/<backend>.php registers, by CLUSTER_STORAGE_BACKEND
     *
     * @param string $backend
     * @return string|null null for a backend the kernel does not ship
     */
    public static function gatewayClassForBackend( $backend )
    {
        $backendClasses = array(
            'dfsmysqli' => 'ezpDfsMySQLiClusterGateway',
            'dfspostgresql' => 'expDfsPostgresqlClusterGateway',
            'dfspostgres' => 'expDfsPostgresqlClusterGateway',
            'dfsoracle' => 'expDfsOracleClusterGateway',
            'dfssqlite' => 'expDfsSQLiteClusterGateway',
            'dfsmongodb' => 'expDfsMongoDBClusterGateway',
            'dfssqlserver' => 'expDfsSQLServerClusterGateway',
        );
        return isset( $backendClasses[$backend] ) ? $backendClasses[$backend] : null;
    }

    /**
     * Returns an instance of the gateway class depending on {@link setGatewayClass()}
     *
     * @return ezpClusterGateway
     */
    public static function getGateway()
    {
        $gatewayClass = self::$gatewayClass;

        // A persistent worker (Velocity) serves many requests in one process: the backend file is
        // loaded once (require_once), so its setGatewayClass() call does not run again on a later
        // request whose statics were reset. Find the class from the configured backend instead.
        if ( !$gatewayClass && defined( 'CLUSTER_STORAGE_BACKEND' ) )
        {
            $backendClass = self::gatewayClassForBackend( CLUSTER_STORAGE_BACKEND );
            if ( $backendClass !== null && class_exists( $backendClass, false ) )
            {
                $gatewayClass = $backendClass;
                self::$gatewayClass = $gatewayClass;
            }
        }

        if ( !$gatewayClass || !class_exists( $gatewayClass, false ) )
        {
            header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1' ) . " 500 Internal Server Error" );
            echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n<html><head>\n<title>500 Internal Server Error</title>\n</head><body>\n<h1>An error has occured</h1>\n</body></html>";
            error_log( 'ezpClusterGateway: no gateway class is set for backend ' . ( defined( 'CLUSTER_STORAGE_BACKEND' ) ? CLUSTER_STORAGE_BACKEND : '(none)' ) );
            exit;
        }

        return new $gatewayClass(
            array(
                // some databases don't need a hostname (oracle for instance)
                "host" => defined( "CLUSTER_STORAGE_HOST" ) ? CLUSTER_STORAGE_HOST : null,
                "port" => defined( "CLUSTER_STORAGE_PORT" ) ? CLUSTER_STORAGE_PORT : null,
                "user" => CLUSTER_STORAGE_USER,
                "password" => CLUSTER_STORAGE_PASS,
                "name" => CLUSTER_STORAGE_DB,
                "charset" => CLUSTER_STORAGE_CHARSET,
            )
        );
    }
}
