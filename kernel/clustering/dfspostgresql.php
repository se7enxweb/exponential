<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * DFS/PostgreSQL cluster gateway: index_cluster.php serves the cluster's binary files with it when config.cluster.php
 * says define( 'CLUSTER_STORAGE_BACKEND', 'dfspostgresql' ). No extension path is needed.
 *
 * After the gateway of the ezpostgresqlcluster extension (eZ Systems, GPL-2.0), rewritten: PDO with a prepared
 * statement, PostgreSQL's default port 5432, a Unix socket directory as the host, and the file streamed in
 * pieces, so a range or a large file never sits in memory whole.
 */
class expDfsPostgresqlClusterGateway extends ezpClusterGateway
{
    protected $port = 5432;

    public function connect()
    {
        $parts = array();
        if ( $this->host !== null && $this->host !== '' )
            $parts[] = 'host=' . str_replace( ';', '', (string)$this->host );
        $parts[] = 'port=' . ( $this->port ? (int)$this->port : 5432 );
        $parts[] = 'dbname=' . str_replace( ';', '', (string)$this->name );
        $parts[] = 'connect_timeout=10';
        try
        {
            $this->db = new PDO( 'pgsql:' . implode( ';', $parts ), (string)$this->user, (string)$this->password, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_STRINGIFY_FETCHES => true,
            ) );
            $charset = trim( (string)$this->charset );
            $encoding = ( $charset === '' || strtolower( str_replace( array( '-', '_' ), '', $charset ) ) === 'utf8' )
                ? 'UTF8'
                : strtoupper( preg_replace( '/[^A-Za-z0-9_]/', '', $charset ) );
            $this->db->exec( "SET client_encoding TO '$encoding'" );
        }
        catch ( PDOException $e )
        {
            $this->db = null;
            throw new RuntimeException( "Failed connecting to the PostgreSQL cluster database: " . $e->getMessage() );
        }
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
        try
        {
            $stmt = $this->db->prepare( "SELECT datatype, size, mtime FROM " . $this->dbTable( $filepath ) . " WHERE name_hash = :hash" );
            $stmt->execute( array( ':hash' => md5( $filepath ) ) );
            $metadata = $stmt->fetch( PDO::FETCH_ASSOC );
        }
        catch ( PDOException $e )
        {
            throw new RuntimeException( "Failed to fetch file metadata for '$filepath': " . $e->getMessage() );
        }
        return is_array( $metadata ) ? $metadata : false;
    }

    public function passthrough( $filepath, $filesize, $offset = false, $length = false )
    {
        $dfsFilePath = CLUSTER_MOUNT_POINT_PATH . '/' . $filepath;

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
            // one fread() may return less than asked for (a network mount returns what it has): read until the
            // requested length or the end of the file
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

ezpClusterGateway::setGatewayClass( 'expDfsPostgresqlClusterGateway' );
