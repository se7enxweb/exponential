<?php
/**
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * DFS/SQLite cluster gateway: index_cluster.php serves the cluster's binary files with it when config.cluster.php
 * says define( 'CLUSTER_STORAGE_BACKEND', 'dfssqlite' ). CLUSTER_STORAGE_DB is the database file: an absolute path, or
 * a name in var/storage/sqlite3 of the installation (as file.ini [eZDFSClusteringSettings] DBName). Host, port, user
 * and password are not used.
 *
 * The same structure as the PostgreSQL gateway (kernel/clustering/dfspostgresql.php): PDO with a prepared statement,
 * and the file streamed in pieces. The database is opened without SQLite's CREATE flag, and a read waits up to five
 * seconds for a writer that is checkpointing.
 */
class expDfsSQLiteClusterGateway extends ezpClusterGateway
{
    /** The directory a database named without a path lives in, relative to the installation root */
    const STORAGE_DIRECTORY = 'var/storage/sqlite3';

    /**
     * The database file of CLUSTER_STORAGE_DB (pure): an absolute path as it is, a name in var/storage/sqlite3 of
     * the installation $root otherwise; '' for no name or :memory:.
     *
     * @param string $name
     * @param string $root the installation root
     * @return string
     */
    public static function databaseFile( $name, $root )
    {
        $name = trim( (string)$name );
        if ( $name === '' || $name === ':memory:' || strpos( $name, "\0" ) !== false )
            return '';
        // the gateway runs without the autoloader: eZDir::isAbsolutePath() written out
        if ( $name[0] === '/' || ( DIRECTORY_SEPARATOR === '\\' && ( $name[0] === '\\' || preg_match( '#^[A-Za-z]:[\\\\/]#', $name ) ) ) )
            return $name;
        return rtrim( (string)$root, '/' ) . '/' . self::STORAGE_DIRECTORY . '/' . $name;
    }

    public function connect()
    {
        $file = self::databaseFile( $this->name, dirname( __DIR__, 2 ) );
        if ( $file === '' )
            throw new RuntimeException( "CLUSTER_STORAGE_DB names no SQLite database file" );
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_STRINGIFY_FETCHES => true,
            PDO::ATTR_TIMEOUT => 5,
        );
        // PHP 8.4 moved the driver's constants to Pdo\Sqlite, and 8.5 deprecates the old names
        if ( defined( 'Pdo\Sqlite::ATTR_OPEN_FLAGS' ) )
            $options[constant( 'Pdo\Sqlite::ATTR_OPEN_FLAGS' )] = constant( 'Pdo\Sqlite::OPEN_READWRITE' );
        elseif ( defined( 'PDO::SQLITE_ATTR_OPEN_FLAGS' ) )
            $options[constant( 'PDO::SQLITE_ATTR_OPEN_FLAGS' )] = constant( 'PDO::SQLITE_OPEN_READWRITE' );
        try
        {
            $this->db = new PDO( 'sqlite:' . $file, null, null, $options );
        }
        catch ( PDOException $e )
        {
            $this->db = null;
            throw new RuntimeException( "Failed connecting to the SQLite cluster database: " . $e->getMessage() );
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
            $stmt->closeCursor();
        }
        catch ( PDOException $e )
        {
            throw new RuntimeException( "Failed to fetch file metadata for '$filepath': " . $e->getMessage() );
        }
        return is_array( $metadata ) ? $metadata : false;
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

ezpClusterGateway::setGatewayClass( 'expDfsSQLiteClusterGateway' );
