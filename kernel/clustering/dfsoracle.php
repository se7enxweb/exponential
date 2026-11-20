<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * DFS/Oracle cluster gateway: index_cluster.php serves the cluster's binary files with it when config.cluster.php
 * says define( 'CLUSTER_STORAGE_BACKEND', 'dfsoracle' ). CLUSTER_STORAGE_DB is the connect string (Easy Connect
 * host:port/service, a TNS alias or a full descriptor), CLUSTER_STORAGE_USER and CLUSTER_STORAGE_PASS the account;
 * host and port are not used, as file.ini [eZDFSClusteringSettings] says for the backend.
 *
 * The same structure as the SQLite and PostgreSQL gateways: oci8 with a bound variable, the size read from the
 * column filesize (SIZE is a reserved word in Oracle), and the file streamed from the DFS mount in pieces.
 */
class expDfsOracleClusterGateway extends ezpClusterGateway
{
    public function connect()
    {
        if ( !function_exists( 'oci_new_connect' ) )
            throw new RuntimeException( "The Oracle cluster gateway needs the PHP extension oci8" );
        $db = @oci_new_connect( (string)$this->user, (string)$this->password, (string)$this->name, 'AL32UTF8' );
        if ( !$db )
        {
            $e = oci_error();
            $this->db = null;
            throw new RuntimeException( "Failed connecting to the Oracle cluster database: " . ( is_array( $e ) ? $e['message'] : 'oci_new_connect() failed' ) );
        }
        $this->db = $db;
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
        $stmt = @oci_parse( $this->db, "SELECT datatype, filesize, mtime FROM " . $this->dbTable( $filepath ) . " WHERE name_hash = :hash" );
        $hash = md5( $filepath );
        if ( !$stmt || !oci_bind_by_name( $stmt, ':hash', $hash ) || !@oci_execute( $stmt, OCI_DEFAULT ) )
        {
            $e = oci_error( $stmt ? $stmt : $this->db );
            throw new RuntimeException( "Failed to fetch file metadata for '$filepath': " . ( is_array( $e ) ? $e['message'] : 'oci8 error' ) );
        }
        $row = oci_fetch_array( $stmt, OCI_NUM | OCI_RETURN_NULLS );
        oci_free_statement( $stmt );
        if ( $row === false )
            return false;
        // as the SQL gateways return them: strings, Oracle's NULL (the empty string) as ''
        return array( 'datatype' => (string)$row[0], 'size' => (string)$row[1], 'mtime' => (string)$row[2] );
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
            @oci_close( $this->db );
        $this->db = null;
    }
}

ezpClusterGateway::setGatewayClass( 'expDfsOracleClusterGateway' );
