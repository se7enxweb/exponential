<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * DFS/MongoDB cluster gateway: index_cluster.php serves the cluster's binary files with it when config.cluster.php
 * says define( 'CLUSTER_STORAGE_BACKEND', 'dfsmongodb' ). CLUSTER_STORAGE_HOST is a host name (or a whole mongodb:// or
 * mongodb+srv:// connection string, for a replica set), CLUSTER_STORAGE_PORT its port (default 27017),
 * CLUSTER_STORAGE_DB the database, CLUSTER_STORAGE_USER and CLUSTER_STORAGE_PASS the account (authenticated against
 * the database, as file.ini [eZDFSClusteringSettings] says for the backend).
 *
 * The same structure as the SQLite and PostgreSQL gateways: one document read by its _id (md5 of the path), the file
 * streamed from the DFS mount in pieces. Only the PHP extension mongodb is needed, no library.
 */
class expDfsMongoDBClusterGateway extends ezpClusterGateway
{
    /** @var string the database of the connection */
    protected $database = '';

    public function connect()
    {
        if ( !extension_loaded( 'mongodb' ) )
            throw new RuntimeException( "The MongoDB cluster gateway needs the PHP extension mongodb" );
        $this->database = (string)$this->name;
        if ( !preg_match( '/^[^\/\\\\. "$\0]{1,63}$/', $this->database ) )
            throw new RuntimeException( "CLUSTER_STORAGE_DB names no MongoDB database" );

        list( $uri, $options ) = self::connectionUri( (string)$this->host, $this->port, (string)$this->user, (string)$this->password, $this->database );
        try
        {
            $this->db = new MongoDB\Driver\Manager( $uri, $options );
        }
        catch ( MongoDB\Driver\Exception\Exception $e )
        {
            $this->db = null;
            throw new RuntimeException( "Failed connecting to the MongoDB cluster database: " . $e->getMessage() );
        }
    }

    /**
     * The connection string and its options (pure), as the backend builds them: a whole mongodb:// or
     * mongodb+srv:// string in the host is taken as it is; the account goes in the options, never in the string.
     *
     * @return array( uri, options )
     */
    public static function connectionUri( $host, $port, $user, $password, $database )
    {
        $host = trim( (string)$host );
        if ( preg_match( '#^mongodb(\+srv)?://#i', $host ) )
            $uri = $host;
        elseif ( $host !== '' && $host[0] === '/' )
            $uri = 'mongodb://' . rawurlencode( $host );
        else
            $uri = 'mongodb://' . ( $host !== '' ? $host : 'localhost' ) . ':' . ( (int)$port > 0 ? (int)$port : 27017 );
        $options = array( 'serverSelectionTimeoutMS' => 5000, 'connectTimeoutMS' => 5000, 'appname' => 'Exponential DFS cluster gateway' );
        if ( (string)$user !== '' && strpos( $uri, '@' ) === false )
        {
            $options['username'] = (string)$user;
            if ( (string)$password !== '' )
                $options['password'] = (string)$password;
            if ( stripos( $uri, 'authSource=' ) === false )
                $options['authSource'] = (string)$database;
        }
        return array( $uri, $options );
    }

    /**
     * Returns the collection to use for the specified file: the cache collection for cache files, else the
     * generic one.
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
        try
        {
            $cursor = $this->db->executeQuery( $this->database . '.' . $this->dbTable( $filepath ),
                new MongoDB\Driver\Query( array( '_id' => md5( $filepath ) ),
                                          array( 'limit' => 1, 'projection' => array( '_id' => 0, 'datatype' => 1, 'size' => 1, 'mtime' => 1 ) ) ) );
            $cursor->setTypeMap( array( 'root' => 'array', 'document' => 'array', 'array' => 'array' ) );
            $rows = $cursor->toArray();
        }
        catch ( MongoDB\Driver\Exception\Exception $e )
        {
            throw new RuntimeException( "Failed to fetch file metadata for '$filepath': " . $e->getMessage() );
        }
        if ( !$rows )
            return false;
        // as the SQL gateways return them: strings
        return array( 'datatype' => (string)$rows[0]['datatype'], 'size' => (string)$rows[0]['size'], 'mtime' => (string)$rows[0]['mtime'] );
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
        $this->db = null;
    }
}

ezpClusterGateway::setGatewayClass( 'expDfsMongoDBClusterGateway' );
