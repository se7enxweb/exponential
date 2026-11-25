<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * DFS/SQL Server cluster gateway: index_cluster.php serves the cluster's binary files with it when config.cluster.php
 * says define( 'CLUSTER_STORAGE_BACKEND', 'dfssqlserver' ). CLUSTER_STORAGE_HOST and CLUSTER_STORAGE_PORT (1433 by
 * default; a named instance as host\instance, no port), CLUSTER_STORAGE_DB the database, CLUSTER_STORAGE_USER and
 * CLUSTER_STORAGE_PASS the login, as file.ini [eZDFSClusteringSettings] says for the backend.
 *
 * The connection goes through the SQL Server driver's own layer (expSQLServerTransport: sqlsrv, pdo_sqlsrv, PDO_ODBC
 * or pdo_dblib), loaded here by hand as index_cluster.php has no autoloader. As site.ini [SQLServerSettings] does for
 * the site, these constants choose it (exp:cluster:enable writes them from the site's settings):
 *   CLUSTER_SQLSERVER_LAYER                    auto (default), sqlsrv, pdo_sqlsrv, pdo_odbc or pdo_dblib
 *   CLUSTER_SQLSERVER_ODBC_DRIVER              the ODBC driver PDO_ODBC uses (ODBC Driver 18 for SQL Server)
 *   CLUSTER_SQLSERVER_ENCRYPT                  yes (default), no or strict
 *   CLUSTER_SQLSERVER_TRUST_SERVER_CERTIFICATE false (default); true only for a self-signed certificate you trust
 *
 * The same structure as the other gateways: one statement with a bound value, the size read from the column filesize
 * (as in the backend's table), and the file streamed from the DFS mount in pieces.
 */
class expDfsSQLServerClusterGateway extends ezpClusterGateway
{
    protected $port = 1433;

    /** The driver files the gateway needs, in the order they load (relative to the installation root) */
    const DRIVER_FILES = array(
        'expSQLServerLexer' => 'lib/ezdb/classes/expsqlserverlexer.php',
        'expSQLServerDialect' => 'lib/ezdb/classes/expsqlserverdialect.php',
        'expSQLServerErrorMap' => 'lib/ezdb/classes/expsqlservererrormap.php',
        'expSQLServerTransport' => 'lib/ezdb/classes/expsqlservertransport.php',
        'expSQLServerSqlsrvTransport' => 'lib/ezdb/classes/expsqlserversqlsrvtransport.php',
        'expSQLServerPDOTransport' => 'lib/ezdb/classes/expsqlserverpdotransport.php',
    );

    /**
     * The transport settings of the gateway's connection (pure): the CLUSTER_* values and the CLUSTER_SQLSERVER_*
     * options, with the defaults of site.ini [SQLServerSettings].
     *
     * @param array $storage host, port, name, user, password
     * @param array $options layer, odbc_driver, encrypt, trust_server_certificate (each optional)
     * @return array layer and config (for expSQLServerTransport::open())
     */
    public static function connectionSettings( array $storage, array $options )
    {
        $options += array( 'layer' => 'auto', 'odbc_driver' => 'ODBC Driver 18 for SQL Server', 'encrypt' => 'yes', 'trust_server_certificate' => false );
        $host = isset( $storage['host'] ) && (string)$storage['host'] !== '' ? (string)$storage['host'] : 'localhost';
        $port = isset( $storage['port'] ) && (int)$storage['port'] > 0 ? (int)$storage['port'] : 1433;
        return array(
            'layer' => (string)$options['layer'],
            'config' => array(
                'host' => $host, 'port' => strpos( $host, '\\' ) === false ? $port : null,
                'database' => isset( $storage['name'] ) ? (string)$storage['name'] : '',
                'user' => isset( $storage['user'] ) ? (string)$storage['user'] : '',
                'password' => isset( $storage['password'] ) ? (string)$storage['password'] : '',
                'encrypt' => (string)$options['encrypt'],
                'trust_server_certificate' => $options['trust_server_certificate'],
                'login_timeout' => 10, 'connect_retry_count' => 0,
                'app_name' => 'Exponential cluster gateway',
                'odbc_driver' => (string)$options['odbc_driver'],
            ),
        );
    }

    /** Loads the driver's transport classes (index_cluster.php has no autoloader) */
    protected static function loadDriver()
    {
        $root = dirname( __DIR__, 2 );
        foreach ( self::DRIVER_FILES as $class => $file )
            if ( !class_exists( $class, false ) )
                require_once $root . '/' . $file;
    }

    public function connect()
    {
        self::loadDriver();
        $options = array();
        foreach ( array( 'layer' => 'CLUSTER_SQLSERVER_LAYER', 'odbc_driver' => 'CLUSTER_SQLSERVER_ODBC_DRIVER', 'encrypt' => 'CLUSTER_SQLSERVER_ENCRYPT',
                         'trust_server_certificate' => 'CLUSTER_SQLSERVER_TRUST_SERVER_CERTIFICATE' ) as $key => $constant )
            if ( defined( $constant ) )
                $options[$key] = constant( $constant );
        $settings = self::connectionSettings( array( 'host' => $this->host, 'port' => $this->port, 'name' => $this->name,
                                                     'user' => $this->user, 'password' => $this->password ), $options );
        $layer = expSQLServerTransport::chooseLayer( $settings['layer'] );
        if ( $layer === null )
            throw new RuntimeException( "The SQL Server cluster gateway needs one of the PHP extensions sqlsrv, pdo_sqlsrv, PDO_ODBC or pdo_dblib" );
        $transport = expSQLServerTransport::create( $layer );
        if ( !$transport->open( $settings['config'] ) )
        {
            $e = $transport->error();
            $message = is_array( $e ) ? (string)$e['message'] : 'the connection failed';
            if ( (string)$this->password !== '' )
                $message = str_replace( (string)$this->password, '***', $message );
            $this->db = null;
            throw new RuntimeException( "Failed connecting to the SQL Server cluster database: " . $message );
        }
        $this->db = $transport;
    }

    /**
     * Returns the database table name to use for the specified file: the cache table for cache files, else the
     * generic table.
     *
     * @param string $filePath
     * @return string
     */
    protected function dbTable( $filePath )
    {
        $cacheDir = defined( 'CLUSTER_METADATA_CACHE_PATH' ) ? CLUSTER_METADATA_CACHE_PATH : "/cache/";
        $storageDir = defined( 'CLUSTER_METADATA_STORAGE_PATH' ) ? CLUSTER_METADATA_STORAGE_PATH : "/storage/";

        if ( strpos( $filePath, $cacheDir ) !== false && strpos( $filePath, $storageDir ) === false )
        {
            return defined( 'CLUSTER_METADATA_TABLE_CACHE' ) ? CLUSTER_METADATA_TABLE_CACHE : 'ezdfsfile_cache';
        }

        return 'ezdfsfile';
    }

    public function fetchFileMetadata( $filepath )
    {
        $result = $this->db->run( "SELECT datatype, filesize, mtime FROM " . $this->dbTable( $filepath ) . " WHERE name_hash = ?", array( md5( $filepath ) ) );
        if ( $result === false )
        {
            $e = $this->db->error();
            throw new RuntimeException( "Failed to fetch file metadata for '$filepath': " . ( is_array( $e ) ? $e['message'] : 'SQL Server error' ) );
        }
        if ( empty( $result['sets'][0] ) )
            return false;
        $row = $result['sets'][0][0];
        // as the other gateways return them: strings, NULL as ''
        return array( 'datatype' => (string)$row['datatype'], 'size' => (string)$row['filesize'], 'mtime' => (string)$row['mtime'] );
    }

    public function passthrough( $filepath, $filesize, $offset = false, $length = false )
    {
        // config.cluster.php defines it; without it there is no file to serve, which is an error, not a notice
        if ( !defined( 'CLUSTER_MOUNT_POINT_PATH' ) )
            throw new RuntimeException( "CLUSTER_MOUNT_POINT_PATH is not defined in config.cluster.php" );
        $dfsFilePath = constant( 'CLUSTER_MOUNT_POINT_PATH' ) . '/' . $filepath;

        if ( !file_exists( $dfsFilePath ) )
            throw new RuntimeException( "Unable to open DFS file '$dfsFilePath'" );

        $fp = fopen( $dfsFilePath, 'rb' );
        if ( $fp === false )
            throw new RuntimeException( "Unable to open DFS file '$dfsFilePath'" );
        if ( $offset !== false && @fseek( $fp, (int)$offset ) === -1 )
            throw new RuntimeException( "Failed to seek offset $offset on file '$filepath'" );
        if ( $length === false )
        {
            fpassthru( $fp );
        }
        else
        {
            // one fread() may return less than asked for: read until the requested length or the end of the file
            $left = (int)$length;
            while ( $left > 0 && !feof( $fp ) )
            {
                $data = fread( $fp, min( $left, 65536 ) );
                if ( $data === false || $data === '' )
                    break;
                echo $data;
                $left -= strlen( $data );
            }
        }

        fclose( $fp );
    }

    public function close()
    {
        if ( $this->db )
            $this->db->close();
        $this->db = null;
    }
}

ezpClusterGateway::setGatewayClass( 'expDfsSQLServerClusterGateway' );
