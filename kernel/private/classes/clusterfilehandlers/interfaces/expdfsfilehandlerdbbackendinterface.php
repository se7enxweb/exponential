<?php
/**
 * File containing the expDFSFileHandlerDBBackendInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The contract of a DFS cluster metadata backend: the database half of eZDFSFileHandler, which keeps one row per
 * clustered file (ezdfsfile, ezdfsfile_cache) while the files themselves sit on the shared mount
 * (eZDFSFileHandlerDFSBackendInterface).
 *
 * eZDFSFileHandler calls exactly these methods ([eZDFSClusteringSettings] DBBackend names the class). The kernel
 * ships these implementations: eZDFSFileHandlerMySQLiBackend (MySQL, MariaDB), expDFSFileHandlerPostgresqlBackend
 * (PostgreSQL 13 and later), expDFSFileHandlerOracleBackend (Oracle 12.1 and later), expDFSFileHandlerSQLiteBackend
 * (SQLite, one machine) and expDFSFileHandlerMongoDBBackend
 * (MongoDB 5.0 and later). A backend for another engine implements this interface and eZClusterEventNotifier, and
 * passes the parity checks of tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSBackendParityChecks.php
 * (doc/eng-us/features/6.0/postgresql-dfs-cluster.md, "More engines").
 *
 * The semantics every implementation keeps:
 *
 *  - name_hash is md5( path ), computed the same way on every engine, so a path has one row whatever the engine;
 *  - a deleted file keeps its row: mtime becomes -ABS(mtime) and expired 1, until _purge()/_purgeByLike() removes
 *    row and file;
 *  - _startCacheGeneration() grants one process per path the right to generate (a "<path>.generating" row), and
 *    returns array( 'result' => 'ok', 'mtime' => int ), array( 'result' => 'ko', 'remaining' => int ) or
 *    array( 'result' => 'error' );
 *  - lock waits are bounded: a lock that is not granted in time, a deadlock or a serialization failure is retried
 *    up to [eZDFSClusteringSettings] DBExecuteRetries times, then reported as a failure, never a hang.
 *
 * The parameter lists are those of the MySQLi backend, which existed first; the parameters stay untyped so that
 * existing backends and their subclasses keep their signatures.
 *
 * @package kernel
 */
interface expDFSFileHandlerDBBackendInterface
{
    /**
     * Connects to the cluster database and sets up the DFS backend.
     *
     * @throws eZClusterHandlerDBNoConnectionException
     */
    public function _connect();

    /**
     * Closes the connection.
     */
    public function _disconnect();

    /**
     * Copies a file, metadata and data.
     *
     * @param string $srcFilePath
     * @param string $dstFilePath
     * @param string|bool $fname
     * @return bool
     */
    public function _copy( $srcFilePath, $dstFilePath, $fname = false );

    /**
     * Removes the row and the file of one path.
     *
     * @param string $filePath
     * @param bool $onlyExpired
     * @param bool|int $expiry
     * @param string|bool $fname
     * @return bool|eZMySQLBackendError
     */
    public function _purge( $filePath, $onlyExpired = false, $expiry = false, $fname = false );

    /**
     * Removes rows and files whose name matches an SQL LIKE pattern.
     *
     * @param string $like
     * @param bool $onlyExpired
     * @param int $limit
     * @param bool|int $expiry
     * @param string|bool $fname
     * @return bool|int false, or the number of rows removed
     */
    public function _purgeByLike( $like, $onlyExpired = false, $limit = 50, $expiry = false, $fname = false );

    /**
     * Marks one path deleted (expired), the file stays until it is purged.
     *
     * @param string $filePath
     * @param bool $insideOfTransaction
     * @param string|bool $fname
     * @return bool
     */
    public function _delete( $filePath, $insideOfTransaction = false, $fname = false );

    /**
     * Marks every path matching an SQL LIKE pattern deleted.
     *
     * @param string $like
     * @param string|bool $fname
     * @return bool
     */
    public function _deleteByLike( $like, $fname = false );

    /**
     * Marks every path matching a shell wildcard (?, *, {a,b}) deleted.
     *
     * @param string $wildcard
     * @param string|bool $fname
     * @return bool
     */
    public function _deleteByWildcard( $wildcard, $fname = false );

    /**
     * Marks the paths "$commonPath/$dir/$commonSuffix*" of each directory deleted.
     *
     * @param array $dirList
     * @param string $commonPath
     * @param string $commonSuffix
     * @param string|bool $fname
     * @return bool
     */
    public function _deleteByDirList( $dirList, $commonPath, $commonSuffix, $fname = false );

    /**
     * @param string $filePath
     * @param string|bool $fname
     * @param bool $ignoreExpiredFiles
     * @param bool $checkOnDFS
     * @return bool
     */
    public function _exists( $filePath, $fname = false, $ignoreExpiredFiles = true, $checkOnDFS = false );

    /**
     * Copies a clustered file to the local file system.
     *
     * @param string $filePath
     * @param bool|string $uniqueName
     * @return string|bool the local path, or false
     */
    public function _fetch( $filePath, $uniqueName = false );

    /**
     * @param string $filePath
     * @param string|bool $fname
     * @return string|bool
     */
    public function _fetchContents( $filePath, $fname = false );

    /**
     * @param string $filePath
     * @param string|bool $fname
     * @return array|false the row, or false when there is none
     */
    public function _fetchMetadata( $filePath, $fname = false );

    /**
     * @param string $srcPath
     * @param string $dstPath
     * @param string|bool $fname
     * @return bool
     */
    public function _linkCopy( $srcPath, $dstPath, $fname = false );

    /**
     * @param string $filePath
     * @param int $startOffset
     * @param bool|int $length
     * @param string|bool $fname
     * @return bool
     */
    public function _passThrough( $filePath, $startOffset = 0, $length = false, $fname = false );

    /**
     * @param string $srcFilePath
     * @param string $dstFilePath
     * @return bool|null null when both paths are the same
     */
    public function _rename( $srcFilePath, $dstFilePath );

    /**
     * Stores a local file in the cluster.
     *
     * @param string $filePath
     * @param string $datatype
     * @param string $scope
     * @param string|bool $fname
     * @return mixed
     */
    public function _store( $filePath, $datatype, $scope, $fname = false );

    /**
     * Stores $contents under $filePath.
     *
     * @param string $filePath
     * @param string $contents
     * @param string $scope
     * @param string $datatype
     * @param bool|int $mtime
     * @param string|bool $fname
     * @return mixed
     */
    public function _storeContents( $filePath, $contents, $scope, $datatype, $mtime = false, $fname = false );

    /**
     * @param array|bool $scopes
     * @param bool $excludeScopes
     * @param array|bool $limit array( offset, count )
     * @param bool|string $path
     * @return array
     */
    public function _getFileList( $scopes = false, $excludeScopes = false, $limit = false, $path = false );

    /**
     * @param string $filePath
     * @param string $generatingFilePath
     * @return array
     */
    public function _startCacheGeneration( $filePath, $generatingFilePath );

    /**
     * @param string $filePath
     * @param string $generatingFilePath
     * @param bool $rename
     * @return bool
     */
    public function _endCacheGeneration( $filePath, $generatingFilePath, $rename );

    /**
     * @param string $generatingFilePath
     * @param int $generatingFileMtime
     * @return bool
     */
    public function _checkCacheGenerationTimeout( $generatingFilePath, $generatingFileMtime );

    /**
     * @param string $generatingFilePath
     */
    public function _abortCacheGeneration( $generatingFilePath );

    /**
     * @param array $scopes
     * @param array|bool $limit
     * @param bool|int $expiry
     * @return array
     */
    public function expiredFilesList( $scopes, $limit = array( 0, 100 ), $expiry = false );

    /**
     * @param int $limit
     * @return int
     */
    public function deleteCacheFiles( $limit );

    /**
     * @param string $filePath
     * @return string
     */
    public function applyServerUri( $filePath );
}
