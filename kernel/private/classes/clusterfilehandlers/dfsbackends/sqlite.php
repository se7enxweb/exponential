<?php
/**
 * File containing the expDFSFileHandlerSQLiteBackend class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The DFS cluster metadata backend for SQLite 3.24 and later: one node, or several processes and PHP servers of one
 * machine, never several machines.
 *
 * SQLite is a file, and its locks are the file system's: a database on a network share is not safe to write from
 * several machines at once (SQLite's own documentation says so). This backend is therefore for a one-node cluster
 * (Velocity and PHP-FPM, or two FPM pools, on one host, with one DFS folder) and for the cluster tests on a laptop
 * or in CI. A site on several web servers clusters on MySQL/MariaDB, PostgreSQL, Oracle or MongoDB.
 *
 * Written after the structure of the kernel's PostgreSQL backend (expDFSFileHandlerPostgresqlBackend, itself after
 * the ezpostgresqlcluster extension by eZ Systems and 7x's fork of it) and method for method against the MySQLi
 * backend. What differs from the PostgreSQL backend (doc/eng-us/features/6.0/sqlite-dfs-cluster.md lists it all):
 *
 *  - PDO's pdo_sqlite, every value a bound parameter; the database file is opened without CREATE, so a wrong
 *    DBName is a connection error, not a new empty database;
 *  - every transaction is BEGIN IMMEDIATE: it takes the database's write lock at its start, so nothing it read can
 *    change before it writes (the part FOR UPDATE plays on the server engines), and two writers never meet in the
 *    middle of a transaction (no deadlock, no SQLITE_BUSY_SNAPSHOT);
 *  - lock waits are bounded by the connection's busy timeout ([eZDFSClusteringSettings] DBLockTimeout, seconds,
 *    default 10); a transaction that did not get the lock in that time (SQLITE_BUSY or SQLITE_LOCKED) is tried
 *    again by _protect(), up to DBExecuteRetries tries, with a short bounded pause;
 *  - WAL journal mode, so readers never wait for the writer;
 *  - LIKE patterns become GLOB patterns: GLOB is case sensitive, as MySQL's binary LIKE on name is, and a prefix
 *    uses the name index; a shell wildcard with {a,b} alternatives becomes several GLOB patterns;
 *  - INSERT ... ON CONFLICT (name_hash) DO UPDATE for stores, as on PostgreSQL;
 *  - 64 bit integers for sizes and times (kernel/sql/sqlite/cluster_dfs_schema.sql), no 2038 limit.
 *
 * Usable on PHP 7.4, clean on PHP 8.0 to 8.5.
 *
 * Settings: file.ini [eZDFSClusteringSettings] DBBackend=expDFSFileHandlerSQLiteBackend, DBName (the database
 * file: an absolute path, or a name in var/storage/sqlite3 as site.ini [DatabaseSettings] Database is for SQLite),
 * DBConnectRetries, DBExecuteRetries, optional DBLockTimeout. DBHost, DBPort, DBSocket, DBUser and DBPassword are
 * not used.
 *
 * @package kernel
 */
class expDFSFileHandlerSQLiteBackend implements eZClusterEventNotifier, expDFSFileHandlerDBBackendInterface
{
    /**
     * Wait for n microseconds until retry if copy fails, to avoid DFS overload.
     */
    const TIME_UNTIL_RETRY = 100;

    /** Seconds a statement waits for the database's write lock when DBLockTimeout is not set */
    const DEFAULT_LOCK_TIMEOUT = 10;

    /** Longest pause between two tries of a transaction, in microseconds */
    const MAX_RETRY_PAUSE = 200000;

    /** Rows deleted per statement by _purgeByLike() (SQLite allows at least 999 bound parameters per statement) */
    const DELETE_CHUNK = 500;

    /** Most GLOB patterns a wildcard with {a,b} alternatives may expand to */
    const MAX_WILDCARD_PATTERNS = 256;

    /** The directory a DBName without a path lives in, relative to the installation root (as eZSQLite3DB's) */
    const STORAGE_DIRECTORY = 'var/storage/sqlite3';

    /** SQLite's primary result codes this backend acts on */
    const SQLITE_ERROR = 1;
    const SQLITE_BUSY = 5;
    const SQLITE_LOCKED = 6;
    const SQLITE_CONSTRAINT = 19;

    /**
     * Max number of times a dfs file is tried to be copied.
     *
     * @var int
     */
    protected $maxCopyTries;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->eventHandler = ezpEvent::getInstance();
        $fileINI = eZINI::instance( 'file.ini' );
        $this->maxCopyTries = (int)$fileINI->variable( 'eZDFSClusteringSettings', 'MaxCopyRetries' );

        if ( defined( 'CLUSTER_METADATA_TABLE_CACHE' ) )
        {
            $this->metaDataTableCache = CLUSTER_METADATA_TABLE_CACHE;
        }
        else if ( $fileINI->hasVariable( 'eZDFSClusteringSettings', 'MetaDataTableNameCache' ) )
        {
            $this->metaDataTableCache = $fileINI->variable( 'eZDFSClusteringSettings', 'MetaDataTableNameCache' );
        }

        $this->cacheDir = eZINI::instance( 'site.ini' )->variable( 'FileSettings', 'CacheDir' );
        $this->storageDir = eZINI::instance( 'site.ini' )->variable( 'FileSettings', 'StorageDir' );
    }

    /**
     * Whether a failure with this SQLite result code is worth another try of the whole transaction: the database
     * was busy (SQLITE_BUSY, 5) or a table locked (SQLITE_LOCKED, 6) for longer than the busy timeout. Extended
     * codes count by their primary code (SQLITE_BUSY_SNAPSHOT is 517, SQLITE_BUSY_RECOVERY 261 ...).
     *
     * @param string|int|null $code
     * @return bool
     */
    public static function isRetryableCode( $code )
    {
        if ( $code === null || !preg_match( '/^[0-9]+$/', (string)$code ) )
            return false;
        $primary = (int)$code & 0xff;
        return $primary === self::SQLITE_BUSY || $primary === self::SQLITE_LOCKED;
    }

    /**
     * The SQLite result code of an exception from PDO (errorInfo[1]; PDO's SQLSTATE is HY000 for nearly every
     * SQLite error and says nothing), '' for anything else.
     *
     * @param Throwable|Exception $e
     * @return string
     */
    public static function errorCodeOf( $e )
    {
        if ( $e instanceof PDOException )
        {
            if ( is_array( $e->errorInfo ) && isset( $e->errorInfo[1] ) && $e->errorInfo[1] !== null && $e->errorInfo[1] !== '' )
                return (string)$e->errorInfo[1];
            // a failed connection has no errorInfo; its message carries the code
            if ( preg_match( '/SQLSTATE\[HY000\] \[([0-9]+)\]/', $e->getMessage(), $m ) )
                return $m[1];
            return '';
        }
        return '';
    }

    /**
     * Whether a failure is SQLite's "no such table" (SQLITE_ERROR with that message).
     *
     * @param string|int|null $code
     * @param string $message
     * @return bool
     */
    public static function isMissingTable( $code, $message )
    {
        return (string)$code === (string)self::SQLITE_ERROR && stripos( (string)$message, 'no such table' ) !== false;
    }

    /**
     * The database file DBName names: an absolute path as it is, a name in STORAGE_DIRECTORY (relative to the
     * installation root, the working directory of every Exponential script) otherwise. '' and :memory: are no
     * database for a cluster, which several processes share.
     *
     * @param string $dbName
     * @return string the file, '' when DBName names none
     */
    public static function filePath( $dbName )
    {
        $dbName = trim( (string)$dbName );
        if ( $dbName === '' || $dbName === ':memory:' || strpos( $dbName, "\0" ) !== false )
            return '';
        if ( $dbName[0] === '/' )
            return $dbName;
        return self::STORAGE_DIRECTORY . '/' . $dbName;
    }

    /**
     * The GLOB pattern of a LIKE pattern the callers built (with % wildcards): % is any text, an underscore is a
     * literal (as in the MySQLi backend), a backslash makes the next character a literal; *, ? and [ of the
     * pattern are literals.
     *
     * @param string $like
     * @return string
     */
    public static function likeToGlob( $like )
    {
        $like = (string)$like;
        $glob = '';
        $length = strlen( $like );
        for ( $i = 0; $i < $length; ++$i )
        {
            $char = $like[$i];
            if ( $char === '\\' && $i + 1 < $length )
                $glob .= self::globLiteral( $like[++$i] );
            elseif ( $char === '%' )
                $glob .= '*';
            else
                $glob .= self::globLiteral( $char );
        }
        return $glob;
    }

    /**
     * A GLOB pattern for every name that starts with $prefix: every character of the prefix is a literal.
     *
     * @param string $prefix
     * @return string
     */
    public static function globPrefix( $prefix )
    {
        $prefix = (string)$prefix;
        $glob = '';
        $length = strlen( $prefix );
        for ( $i = 0; $i < $length; ++$i )
            $glob .= self::globLiteral( $prefix[$i] );
        return $glob . '*';
    }

    /**
     * One character as a GLOB literal: *, ? and [ in brackets, every other character as it is.
     *
     * @param string $char
     * @return string
     */
    private static function globLiteral( $char )
    {
        return ( $char === '*' || $char === '?' || $char === '[' ) ? '[' . $char . ']' : $char;
    }

    /**
     * The GLOB patterns of a shell wildcard: ? one character, * any text, {a,b} one of the alternatives (each
     * alternative becomes a pattern of its own, nested braces too); every other character is a literal. A comma
     * outside braces stays a comma; an unclosed brace is closed at the end.
     *
     * @param string $wildcard
     * @return string[]
     * @throws InvalidArgumentException when the alternatives expand to more than MAX_WILDCARD_PATTERNS patterns
     */
    public static function wildcardToGlobs( $wildcard )
    {
        $wildcard = (string)$wildcard;
        $pos = 0;
        $patterns = self::expandWildcard( $wildcard, $pos, false );
        return array_values( array_unique( $patterns ) );
    }

    /**
     * The patterns of $wildcard from $pos up to the end, or up to the closing brace or the comma of the group it is
     * in ($inGroup). Moves $pos past what it read.
     *
     * @param string $wildcard
     * @param int $pos
     * @param bool $inGroup
     * @return string[]
     */
    private static function expandWildcard( $wildcard, &$pos, $inGroup )
    {
        $patterns = array( '' );
        $length = strlen( $wildcard );
        while ( $pos < $length )
        {
            $char = $wildcard[$pos];
            if ( $inGroup && ( $char === ',' || $char === '}' ) )
                break;
            ++$pos;
            if ( $char === '{' )
            {
                $alternatives = array();
                while ( true )
                {
                    foreach ( self::expandWildcard( $wildcard, $pos, true ) as $alternative )
                        $alternatives[] = $alternative;
                    if ( $pos < $length && $wildcard[$pos] === ',' )
                    {
                        ++$pos;
                        continue;
                    }
                    if ( $pos < $length && $wildcard[$pos] === '}' )
                        ++$pos;
                    break;
                }
                $next = array();
                foreach ( $patterns as $pattern )
                    foreach ( $alternatives as $alternative )
                        $next[] = $pattern . $alternative;
                if ( count( $next ) > self::MAX_WILDCARD_PATTERNS )
                    throw new InvalidArgumentException( "The wildcard '$wildcard' has more than " . self::MAX_WILDCARD_PATTERNS . " alternatives" );
                $patterns = $next;
                continue;
            }
            switch ( $char )
            {
                case '?': $piece = '?'; break;
                case '*': $piece = '*'; break;
                default: $piece = self::globLiteral( $char );
            }
            foreach ( $patterns as $i => $pattern )
                $patterns[$i] = $pattern . $piece;
        }
        return $patterns;
    }

    /**
     * Returns the database table name to use for the specified file.
     *
     * For files detected as cache files the cache table is returned, if not
     * the generic table is returned.
     *
     * @param string $filePath
     * @return string The database table name
     */
    protected function dbTable( $filePath )
    {
        if ( $this->metaDataTableCache == $this->metaDataTable )
            return $this->metaDataTable;

        if ( strpos( $filePath, $this->cacheDir ) !== false && strpos( $filePath, $this->storageDir ) === false )
        {
            return $this->metaDataTableCache;
        }

        return $this->metaDataTable;
    }

    /**
     * Connects to the database.
     *
     * @return void
     * @throws eZClusterHandlerDBNoConnectionException
     */
    public function _connect()
    {
        $siteINI = eZINI::instance( 'site.ini' );

        if ( self::$dbparams === null )
        {
            $fileINI = eZINI::instance( 'file.ini' );

            self::$dbparams = array();
            self::$dbparams['dbname']     = $fileINI->variable( 'eZDFSClusteringSettings', 'DBName' );

            self::$dbparams['max_connect_tries'] = $fileINI->variable( 'eZDFSClusteringSettings', 'DBConnectRetries' );
            self::$dbparams['max_execute_tries'] = $fileINI->variable( 'eZDFSClusteringSettings', 'DBExecuteRetries' );
            self::$dbparams['lock_timeout'] = $fileINI->hasVariable( 'eZDFSClusteringSettings', 'DBLockTimeout' )
                ? $fileINI->variable( 'eZDFSClusteringSettings', 'DBLockTimeout' )
                : self::DEFAULT_LOCK_TIMEOUT;

            self::$dbparams['sql_output'] = $siteINI->variable( "DatabaseSettings", "SQLOutput" ) == "enabled";

            self::$dbparams['cache_generation_timeout'] = $siteINI->variable( "ContentSettings", "CacheGenerationTimeout" );
        }

        $file = self::filePath( self::$dbparams['dbname'] );
        if ( $file === '' )
        {
            throw new eZClusterHandlerDBNoConnectionException( (string)self::$dbparams['dbname'], '', '',
                "[eZDFSClusteringSettings] DBName names no SQLite database file" );
        }

        $lockTimeout = max( 1, (int)self::$dbparams['lock_timeout'] );
        $maxTries = max( 1, (int)self::$dbparams['max_connect_tries'] );
        $tries = 0;
        $error = '';
        $this->db = null;
        eZDebug::accumulatorStart( 'sqlite_cluster_connect', 'SQLite Cluster', 'Cluster database connection' );
        while ( $tries < $maxTries )
        {
            try
            {
                $this->db = new PDO( 'sqlite:' . $file, null, null, self::connectionOptions( $lockTimeout ) );
                break;
            }
            catch ( PDOException $e )
            {
                $error = $e->getMessage();
                ++$tries;
            }
        }
        eZDebug::accumulatorStop( 'sqlite_cluster_connect' );
        if ( !$this->db instanceof PDO )
        {
            throw new eZClusterHandlerDBNoConnectionException( $file, '', '', $error );
        }

        try
        {
            // the busy handler waits this long for the write lock; then SQLITE_BUSY, which _protect() retries
            $this->db->exec( 'PRAGMA busy_timeout = ' . ( $lockTimeout * 1000 ) );
            // readers never wait for the writer; the mode is kept in the file, so it is set once
            $mode = $this->db->query( 'PRAGMA journal_mode' )->fetchColumn();
            if ( strtolower( (string)$mode ) !== 'wal' )
                $this->db->query( 'PRAGMA journal_mode = WAL' )->fetchColumn();
        }
        catch ( PDOException $e )
        {
            $this->recordError( $e );
            $this->_die( "Failed to set up the cluster database connection" );
        }

        $this->transactionCount = 0;
        $this->transactionFailed = false;

        // DFS setup
        $this->dfsbackend = eZDFSFileHandlerBackendFactory::build();
    }

    /**
     * The options of a PDO connection to the cluster database: exceptions, strings (as the MySQLi backend returns
     * them: the handler compares and prints them as such), the busy timeout, and an existing file only (opened
     * without SQLite's CREATE flag, so a wrong DBName fails instead of making an empty database).
     *
     * @param int $lockTimeout seconds
     * @return array
     */
    public static function connectionOptions( $lockTimeout )
    {
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_STRINGIFY_FETCHES => true,
            PDO::ATTR_TIMEOUT => max( 1, (int)$lockTimeout ),
        );
        // PHP 8.4 moved the driver's constants to Pdo\Sqlite, and 8.5 deprecates the old names
        if ( defined( 'Pdo\Sqlite::ATTR_OPEN_FLAGS' ) )
            $options[constant( 'Pdo\Sqlite::ATTR_OPEN_FLAGS' )] = constant( 'Pdo\Sqlite::OPEN_READWRITE' );
        elseif ( defined( 'PDO::SQLITE_ATTR_OPEN_FLAGS' ) )
            $options[constant( 'PDO::SQLITE_ATTR_OPEN_FLAGS' )] = constant( 'PDO::SQLITE_OPEN_READWRITE' );
        return $options;
    }

    /**
     * Disconnects the handler from the database
     */
    public function _disconnect()
    {
        $this->db = null;
        $this->transactionCount = 0;
        $this->transactionFailed = false;
        $this->inSqlTransaction = false;
    }


    /**
     * Creates a copy of a file in DB+DFS
     * @param string $srcFilePath Source file
     * @param string $dstFilePath Destination file
     * @param bool|string $fname
     * @return bool
     *
     * @see _copyInner
     */
    public function _copy( $srcFilePath, $dstFilePath, $fname = false )
    {
        if ( $fname )
            $fname .= "::_copy($srcFilePath, $dstFilePath)";
        else
            $fname = "_copy($srcFilePath, $dstFilePath)";

        $metaData = $this->_fetchMetadata( $srcFilePath, $fname );
        if ( !$metaData )
        {
            return false;
        }
        $result = $this->_protect( array( $this, "_copyInner" ), $fname,
                                   $srcFilePath, $dstFilePath, $fname, $metaData );

        $this->eventHandler->notify( 'cluster/deleteFile', array( $dstFilePath ) );

        return $result;
    }

    /**
     * Inner function used by _copy to perform the operation in a transaction
     *
     * @param string $srcFilePath
     * @param string $dstFilePath
     * @param string $fname
     * @param array  $metaData Source file's metadata
     * @return bool|eZMySQLBackendError
     */
    private function _copyInner( $srcFilePath, $dstFilePath, $fname, $metaData )
    {
        $this->_delete( $dstFilePath, true, $fname );

        $fileMTime = (int)$metaData['mtime'];
        if ( !$this->_insertUpdate( $this->dbTable( $dstFilePath ),
                                    array( 'datatype' => $metaData['datatype'],
                                           'name' => $dstFilePath,
                                           'name_trunk' => self::nameTrunk( $dstFilePath, $metaData['scope'] ),
                                           'name_hash' => md5( $dstFilePath ),
                                           'scope' => $metaData['scope'],
                                           'size' => (int)$metaData['size'],
                                           'mtime' => $fileMTime,
                                           'expired' => ( $fileMTime < 0 ) ? 1 : 0 ),
                                    array( 'datatype', 'name_trunk', 'scope', 'size', 'mtime', 'expired' ),
                                    $fname ) )
        {
            return $this->_fail( $srcFilePath, "Failed to insert file metadata on copying." );
        }

        if ( !$this->dfsbackend->copyFromDFSToDFS( $srcFilePath, $dstFilePath ) )
        {
            return $this->_fail( $srcFilePath, "Failed to copy DFS://$srcFilePath to DFS://$dstFilePath" );
        }
        return true;
    }

    /**
     * Purges meta-data and file-data for a file entry
     *
     * @param string $filePath Path of the file to purge
     * @param bool $onlyExpired Only purges expired files
     * @param bool|int $expiry
     * @param bool|string $fname
     * @return bool|eZMySQLBackendError
     */
    public function _purge( $filePath, $onlyExpired = false, $expiry = false, $fname = false )
    {
        if ( $fname )
            $fname .= "::_purge($filePath)";
        else
            $fname = "_purge($filePath)";
        $sql = "DELETE FROM " . $this->dbTable( $filePath ) . " WHERE name_hash = :hash";
        $params = array( ':hash' => md5( $filePath ) );
        if ( $expiry !== false )
        {
            $sql .= " AND mtime < :expiry";
            $params[':expiry'] = (int)$expiry;
        }
        elseif ( $onlyExpired )
        {
            $sql .= " AND expired = 1";
        }
        $stmt = $this->_query( $sql, $fname, true, $params );
        if ( !$stmt )
        {
            return $this->_fail( "Purging file metadata for $filePath failed" );
        }
        if ( $stmt->rowCount() == 1 )
        {
            $this->dfsbackend->delete( $filePath );
        }

        $this->eventHandler->notify( 'cluster/deleteFile', array( $filePath ) );

        return true;
    }

    /**
     * Purges meta-data and file-data for files matching a pattern using a SQL LIKE syntax.
     *
     * The transaction is BEGIN IMMEDIATE: it holds the database's write lock from the SELECT to the DELETE, so no
     * other process changes the rows in between.
     *
     * @param string $like SQL LIKE string applied to ezdfsfile.name to look for files to purge
     * @param bool $onlyExpired Only purge expired files (ezdfsfile.expired = 1)
     * @param int $limit Maximum number of items to purge in one call
     * @param bool|int $expiry Only files older than this timestamp
     * @param string|bool $fname Optional caller name for debugging
     * @return bool|int false if it fails, number of affected rows otherwise
     */
    public function _purgeByLike( $like, $onlyExpired = false, $limit = 50, $expiry = false, $fname = false )
    {
        if ( $fname )
            $fname .= "::_purgeByLike($like, $onlyExpired)";
        else
            $fname = "_purgeByLike($like, $onlyExpired)";

        $table = $this->dbTable( $like );
        $where = " WHERE name GLOB :glob";
        $params = array( ':glob' => self::likeToGlob( $like ) );
        if ( $expiry !== false )
        {
            $where .= " AND mtime < :expiry";
            $params[':expiry'] = (int)$expiry;
        }
        elseif ( $onlyExpired )
        {
            $where .= " AND expired = 1";
        }
        $sqlLimit = $limit ? " LIMIT " . (int)$limit : "";

        if ( !$this->_begin( $fname ) )
        {
            $this->_rollback( $fname );
            return $this->_fail( "The cluster database stayed busy, purging by like statement $like was not started" );
        }

        $stmt = $this->_query( "SELECT name, name_hash FROM $table{$where}{$sqlLimit}", $fname, true, $params );
        if ( !$stmt )
        {
            $this->_rollback( $fname );
            return $this->_fail( "Selecting file metadata by like statement $like failed" );
        }
        $rows = $stmt->fetchAll( PDO::FETCH_ASSOC );
        if ( count( $rows ) == 0 )
        {
            $this->_rollback( $fname );
            return 0;
        }

        $files = array();
        $hashes = array();
        foreach ( $rows as $row )
        {
            $files[] = $row['name'];
            $hashes[] = $row['name_hash'];
        }

        $deletedDBFiles = 0;
        foreach ( array_chunk( $hashes, self::DELETE_CHUNK ) as $chunk )
        {
            $placeholders = array();
            $deleteParams = array();
            foreach ( $chunk as $i => $hash )
            {
                $placeholders[] = ":h$i";
                $deleteParams[":h$i"] = $hash;
            }
            $stmt = $this->_query( "DELETE FROM $table WHERE name_hash IN (" . implode( ', ', $placeholders ) . ")", $fname, true, $deleteParams );
            if ( !$stmt )
            {
                $this->_rollback( $fname );
                return $this->_fail( "Purging file metadata by like statement $like failed" );
            }
            $deletedDBFiles += $stmt->rowCount();
        }

        $commitResult = $this->_commit( $fname );
        if ( $commitResult === true || $commitResult === null )
        {
            $this->dfsbackend->delete( $files );

            return $deletedDBFiles;
        }

        return false;
    }

    /**
     * Deletes a file from DB (marks it expired); _purge removes it from disk.
     *
     * @param string $filePath Path of the file to delete
     * @param bool $insideOfTransaction Whether or not a transaction is already started
     * @param bool|string $fname Optional caller name for debugging
     * @return bool
     */
    public function _delete( $filePath, $insideOfTransaction = false, $fname = false )
    {
        if ( $fname )
            $fname .= "::_delete($filePath)";
        else
            $fname = "_delete($filePath)";
        if ( $insideOfTransaction )
        {
            $res = $this->_deleteInner( $filePath, $fname );
            if ( !$res || $res instanceof eZMySQLBackendError )
            {
                $this->_handleErrorType( $res );
            }
        }
        else
        {
            $res = $this->_protect( array( $this, '_deleteInner' ), $fname,
                                    $filePath, $fname );
        }

        $this->eventHandler->notify( 'cluster/deleteFile', array( $filePath ) );

        return $res;
    }

    /**
     * Callback method used by by _delete to delete a single file
     *
     * @param string $filePath Path of the file to delete
     * @param string $fname Optional caller name for debugging
     * @return bool|eZMySQLBackendError
     */
    protected function _deleteInner( $filePath, $fname )
    {
        if ( !$this->_query( "UPDATE " . $this->dbTable( $filePath ) . " SET mtime = -ABS(mtime), expired = 1 WHERE name_hash = :hash",
                             $fname, true, array( ':hash' => md5( $filePath ) ) ) )
            return $this->_fail( "Deleting file $filePath failed" );
        return true;
    }

    /**
     * Deletes multiple files using a SQL LIKE statement
     *
     * @param string $like SQL LIKE condition applied to ezdfsfile.name
     * @param bool|string $fname Optional caller name for debugging
     * @return bool
     */
    public function _deleteByLike( $like, $fname = false )
    {
        if ( $fname )
            $fname .= "::_deleteByLike($like)";
        else
            $fname = "_deleteByLike($like)";
        $return = $this->_protect( array( $this, '_deleteByLikeInner' ), $fname,
                                   $like, $fname );

        if ( $return )
            $this->eventHandler->notify( 'cluster/deleteByLike', array( $like ) );

        return $return;
    }

    /**
     * Callback used by _deleteByLike to perform the deletion
     *
     * @param string $like
     * @param string|bool $fname
     * @return bool|eZMySQLBackendError
     */
    private function _deleteByLikeInner( $like, $fname )
    {
        $sql = "UPDATE " . $this->dbTable( $like ) . " SET mtime = -ABS(mtime), expired = 1 WHERE name GLOB :glob";
        if ( !$this->_query( $sql, $fname, true, array( ':glob' => self::likeToGlob( $like ) ) ) )
        {
            return $this->_fail( "Failed to delete files by like: '$like'" );
        }
        return true;
    }

    /**
     * Deletes multiple DB files by wildcard
     *
     * @param string $wildcard
     * @param mixed $fname
     * @return bool
     * @deprecated Has severe performance issues
     */
    public function _deleteByWildcard( $wildcard, $fname = false )
    {
        if ( $fname )
            $fname .= "::_deleteByWildcard($wildcard)";
        else
            $fname = "_deleteByWildcard($wildcard)";
        return $this->_protect( array( $this, '_deleteByWildcardInner' ), $fname,
                                $wildcard, $fname );
    }

    /**
     * Callback used by _deleteByWildcard to perform the deletion
     *
     * @param string $wildcard
     * @param mixed $fname
     * @return bool|eZMySQLBackendError
     * @deprecated Has severe performance issues
     */
    protected function _deleteByWildcardInner( $wildcard, $fname )
    {
        try
        {
            $globs = self::wildcardToGlobs( $wildcard );
        }
        catch ( InvalidArgumentException $e )
        {
            return $this->_fail( $wildcard, $e->getMessage() );
        }
        $conditions = array();
        $params = array();
        foreach ( $globs as $i => $glob )
        {
            $conditions[] = "name GLOB :g$i";
            $params[":g$i"] = $glob;
        }
        $sql = "UPDATE " . $this->dbTable( $wildcard ) . " SET mtime = -ABS(mtime), expired = 1 WHERE " . implode( ' OR ', $conditions );
        if ( !$this->_query( $sql, $fname, true, $params ) )
        {
            return $this->_fail( "Failed to delete files by wildcard: '$wildcard'" );
        }
        return true;
    }

    /**
     * @param array $dirList
     * @param string $commonPath
     * @param string $commonSuffix
     * @param string|bool $fname
     * @return bool|mixed
     */
    public function _deleteByDirList( $dirList, $commonPath, $commonSuffix, $fname = false )
    {
        if ( $fname )
            $fname .= "::_deleteByDirList(" . join( ", ", $dirList ) . ", $commonPath, $commonSuffix)";
        else
            $fname = "_deleteByDirList(" . join( ", ", $dirList ) . ", $commonPath, $commonSuffix)";
        return $this->_protect( array( $this, '_deleteByDirListInner' ), $fname,
                                $dirList, $commonPath, $commonSuffix, $fname );
    }

    /**
     * @param array $dirList
     * @param string $commonPath
     * @param string $commonSuffix
     * @param string|bool $fname
     * @return bool
     */
    protected function _deleteByDirListInner( $dirList, $commonPath, $commonSuffix, $fname )
    {
        $table = $this->dbTable( $commonPath );
        foreach ( $dirList as $dirItem )
        {
            if ( strstr( $commonPath, '/cache/content' ) !== false or strstr( $commonPath, '/cache/template-block' ) !== false )
            {
                $event = 'cluster/deleteByNametrunk';
                $nametrunk = "$commonPath/$dirItem/$commonSuffix";
                $eventParameters = array( $nametrunk );
                $where = "WHERE name_trunk = :value";
                $value = $nametrunk;
            }
            else
            {
                $event = 'cluster/deleteByDirList';
                $eventParameters = array( $commonPath, $dirItem, $commonSuffix );
                $where = "WHERE name GLOB :value";
                $value = self::likeToGlob( "$commonPath/$dirItem/$commonSuffix%" );
            }
            $sql = "UPDATE $table SET mtime = -ABS(mtime), expired = 1 $where";
            if ( !$this->_query( $sql, $fname, true, array( ':value' => $value ) ) )
            {
                eZDebug::writeError( "Failed to delete files in dir: '$commonPath/$dirItem/$commonSuffix%'", __METHOD__ );
                $event = false;
            }

            if ( $event )
            {
                $this->eventHandler->notify( $event, $eventParameters );
            }
        }
        return true;
    }

    /**
     * Checks if a file exists
     *
     * @param string $filePath
     * @param string|bool $fname
     * @param bool $ignoreExpiredFiles
     * @param bool $checkOnDFS
     * @return bool
     */
    public function _exists( $filePath, $fname = false, $ignoreExpiredFiles = true, $checkOnDFS = false )
    {
        if ( $fname )
            $fname .= "::_exists($filePath)";
        else
            $fname = "_exists($filePath)";

        $row = $this->eventHandler->filter( 'cluster/fileExists', $filePath );

        if ( !is_array( $row ) )
        {
            $row = $this->_selectOneRow( "SELECT name, mtime FROM " . $this->dbTable( $filePath ) . " WHERE name_hash = :hash",
                                         $fname, "Failed to check file '$filePath' existance: ", true,
                                         array( ':hash' => md5( $filePath ) ) );
        }
        if ( $row === false )
            return false;

        if ( $ignoreExpiredFiles )
            $rc = $row[1] >= 0;
        else
            $rc = true;

        if ( $checkOnDFS && $rc )
        {
            $rc = $this->dfsbackend->existsOnDFS( $filePath );
        }
        return $rc;
    }

    /**
     * Creates a directory and all parent directories in its path, if necessary
     *
     * @param string $dir
     * @return bool
     */
    private function mkdir_p( $dir )
    {
        $dirElements = explode( '/', $dir );
        if ( count( $dirElements ) == 0 )
            return true;

        $currentDir = $dirElements[0];

        if ( $currentDir != '' && !file_exists( $currentDir ) && !eZDir::mkdir( $currentDir, false ) )
            return false;

        for ( $i = 1; $i < count( $dirElements ); ++$i )
        {
            $dirElement = $dirElements[$i];
            if ( strlen( $dirElement ) == 0 )
                continue;

            $currentDir .= '/' . $dirElement;

            if ( !file_exists( $currentDir ) && !eZDir::mkdir( $currentDir, false ) )
                return false;
        }

        return true;
    }

    /**
     * Fetches the file $filePath from the database to its own name, or $uniqueName if given
     *
     * @param string $filePath
     * @param bool|string $uniqueName Alternative name to save the file to
     * @return string|bool the file physical path, or false if fetch failed
     */
    public function _fetch( $filePath, $uniqueName = false )
    {
        $metaData = $this->_fetchMetadata( $filePath );
        if ( !$metaData )
        {
            eZDebug::writeError( "File '$filePath' does not exist while trying to fetch.", __METHOD__ );
            return false;
        }

        $dfsFileSize = $this->dfsbackend->getDfsFileSize( $filePath );
        $maxTries = max( 1, (int)$this->maxCopyTries );
        $loopCount = 0;
        $localFileSize = 0;

        // The copy is checked under its temporary name, and only a complete copy takes the file's own name: a short
        // or failed copy never replaces (or freshens the mtime of) the local file that other requests read.
        while ( $dfsFileSize !== false && $loopCount < $maxTries )
        {
            $tmpid = getmypid() . '-' . mt_rand() . 'tmp';
            if ( strrpos( $filePath, '.' ) > 0 )
                $tmpFilePath = substr_replace( $filePath, $tmpid, strrpos( $filePath, '.' ), 0 );
            else
                $tmpFilePath = $filePath . '.' . $tmpid;
            $this->mkdir_p( dirname( $tmpFilePath ) );
            eZDebugSetting::writeDebug( 'kernel-clustering', "copying DFS://$filePath to FS://$tmpFilePath on try: $loopCount " );

            if ( !$this->dfsbackend->copyFromDFS( $filePath, $tmpFilePath ) )
            {
                eZDebug::writeError( "Failed copying DFS://$filePath to FS://$tmpFilePath " );
                if ( file_exists( $tmpFilePath ) )
                    unlink( $tmpFilePath );
                usleep( self::TIME_UNTIL_RETRY );
                ++$loopCount;
                continue;
            }

            clearstatcache( true, $tmpFilePath );
            $localFileSize = @filesize( $tmpFilePath );
            if ( $localFileSize === false || (int)$localFileSize !== (int)$dfsFileSize )
            {
                if ( file_exists( $tmpFilePath ) )
                    unlink( $tmpFilePath );
                usleep( self::TIME_UNTIL_RETRY );
                ++$loopCount;
                continue;
            }

            if ( $uniqueName === true )
                return $tmpFilePath;

            if ( !eZFile::rename( $tmpFilePath, $filePath, false, eZFile::CLEAN_ON_FAILURE | eZFile::APPEND_DEBUG_ON_FAILURE ) )
            {
                usleep( self::TIME_UNTIL_RETRY );
                ++$loopCount;
                continue;
            }
            clearstatcache( true, $filePath );
            return $filePath;
        }

        // No complete copy could be made: the local copy under the file's own name is removed, so it is never served
        // afterwards as if it were current (stale-cache mode serves an existing local file whatever its age).
        if ( $uniqueName !== true && file_exists( $filePath ) )
        {
            @unlink( $filePath );
            clearstatcache( true, $filePath );
        }
        eZDebug::writeError( "Size ({$localFileSize}) of written data for file '{$filePath}' does not match expected size " . ( $dfsFileSize === false ? 'unknown (no DFS file)' : $dfsFileSize ), __METHOD__ );
        return false;
    }

    /**
     * @param string $filePath
     * @param string|bool $fname
     * @return string|bool
     */
    public function _fetchContents( $filePath, $fname = false )
    {
        if ( $fname )
            $fname .= "::_fetchContents($filePath)";
        else
            $fname = "_fetchContents($filePath)";
        $metaData = $this->_fetchMetadata( $filePath, $fname );
        if ( !$metaData )
        {
            eZDebug::writeError( "File '$filePath' does not exist while trying to fetch its contents.", __METHOD__ );
            return false;
        }

        if ( !$contents = $this->dfsbackend->getContents( $filePath ) )
        {
            eZDebug::writeError( "An error occured while reading contents of DFS://$filePath", __METHOD__ );
            return false;
        }
        return $contents;
    }

    /**
     * Fetches and returns metadata for $filePath
     *
     * @param string $filePath
     * @param bool|string $fname
     * @return array|false file metadata, or false if the file does not exist in database.
     */
    public function _fetchMetadata( $filePath, $fname = false )
    {
        $metadata = $this->eventHandler->filter( 'cluster/loadMetadata', $filePath );
        if ( is_array( $metadata ) )
            return $metadata;

        if ( $fname )
            $fname .= "::_fetchMetadata($filePath)";
        else
            $fname = "_fetchMetadata($filePath)";
        $metadata = $this->_selectOneAssoc( "SELECT * FROM " . $this->dbTable( $filePath ) . " WHERE name_hash = :hash", $fname,
                                            "Failed to retrieve file metadata: $filePath", true,
                                            array( ':hash' => md5( $filePath ) ) );
        if ( is_array( $metadata ) )
            $this->eventHandler->notify( 'cluster/storeMetadata', array( $metadata ) );

        return $metadata;
    }

    /**
     * @param string $srcPath
     * @param string $dstPath
     * @param string|bool $fname
     * @return bool
     */
    public function _linkCopy( $srcPath, $dstPath, $fname = false )
    {
        if ( $fname )
            $fname .= "::_linkCopy($srcPath,$dstPath)";
        else
            $fname = "_linkCopy($srcPath,$dstPath)";
        return $this->_copy( $srcPath, $dstPath, $fname );
    }

    /**
     * Passes $filePath content through
     *
     * @param string $filePath
     * @param int $startOffset Byte offset to start download from
     * @param bool|int $length Byte length to be sent
     * @param string|bool $fname
     * @return bool
     */
    public function _passThrough( $filePath, $startOffset = 0, $length = false, $fname = false )
    {
        if ( $fname )
            $fname .= "::_passThrough($filePath)";
        else
            $fname = "_passThrough($filePath)";

        $metaData = $this->_fetchMetadata( $filePath, $fname );
        if ( !$metaData )
            return false;

        $this->dfsbackend->passthrough( $filePath, $startOffset, $length );

        return true;
    }

    /**
     * Renames $srcFilePath to $dstFilePath
     *
     * The transaction holds the database's write lock (BEGIN IMMEDIATE, bounded by the busy timeout), the
     * destination row is written with an upsert into the destination's own table, and a failure of the DFS copy
     * rolls the transaction back.
     *
     * @param string $srcFilePath
     * @param string $dstFilePath
     * @return bool|null
     */
    public function _rename( $srcFilePath, $dstFilePath )
    {
        if ( strcmp( $srcFilePath, $dstFilePath ) == 0 )
            return null;

        $metaData = $this->_fetchMetadata( $srcFilePath );
        if ( !$metaData )
            return false;

        $fname = "_rename($srcFilePath, $dstFilePath)";
        $srcTable = $this->dbTable( $srcFilePath );
        $dstTable = $this->dbTable( $dstFilePath );
        $srcHash = md5( $srcFilePath );

        if ( !$this->_begin( __METHOD__ ) )
        {
            eZDebug::writeError( "The cluster database stayed busy, '$srcFilePath' was not renamed", __METHOD__ );
            $this->_rollback( __METHOD__ );
            return false;
        }

        $stmt = $this->_query( "SELECT name_hash FROM $srcTable WHERE name_hash = :hash", $fname, true, array( ':hash' => $srcHash ) );
        if ( !$stmt || $stmt->fetch( PDO::FETCH_NUM ) === false )
        {
            eZDebug::writeError( "Failed locking file '$srcFilePath'", __METHOD__ );
            $this->_rollback( __METHOD__ );
            return false;
        }

        if ( $this->_exists( $dstFilePath, false, false ) )
            $this->_purge( $dstFilePath, false );

        $sql = "INSERT INTO $dstTable (name, name_trunk, name_hash, datatype, scope, size, mtime, expired) " .
               "SELECT :name, :trunk, :dsthash, datatype, scope, size, mtime, expired FROM $srcTable WHERE name_hash = :srchash " .
               "ON CONFLICT (name_hash) DO UPDATE SET name = EXCLUDED.name, name_trunk = EXCLUDED.name_trunk, datatype = EXCLUDED.datatype, " .
               "scope = EXCLUDED.scope, size = EXCLUDED.size, mtime = EXCLUDED.mtime, expired = EXCLUDED.expired";
        if ( !$this->_query( $sql, $fname, true, array( ':name' => $dstFilePath,
                                                        ':trunk' => self::nameTrunk( $dstFilePath, $metaData['scope'] ),
                                                        ':dsthash' => md5( $dstFilePath ),
                                                        ':srchash' => $srcHash ) ) )
        {
            eZDebug::writeError( "Failed making new file entry '$dstFilePath'", __METHOD__ );
            $this->_rollback( __METHOD__ );
            return false;
        }

        if ( !$this->dfsbackend->copyFromDFSToDFS( $srcFilePath, $dstFilePath ) )
        {
            eZDebug::writeError( "Failed to copy DFS://$srcFilePath to DFS://$dstFilePath", __METHOD__ );
            $this->_rollback( __METHOD__ );
            return false;
        }

        if ( !$this->_query( "DELETE FROM $srcTable WHERE name_hash = :hash", $fname, true, array( ':hash' => $srcHash ) ) )
        {
            eZDebug::writeError( "Failed removing old file '$srcFilePath'", __METHOD__ );
            $this->_rollback( __METHOD__ );
            return false;
        }

        if ( $this->_commit( __METHOD__ ) === false )
        {
            // the rows stay as they were; the copy on the DFS is an orphan that dfscleanup.php finds
            eZDebug::writeError( "Failed committing the rename of '$srcFilePath'", __METHOD__ );
            return false;
        }

        $this->dfsbackend->delete( $srcFilePath );

        $this->eventHandler->notify( 'cluster/deleteFile', array( $srcFilePath ) );

        return true;
    }

    /**
     * Stores $filePath to cluster
     *
     * @param string $filePath
     * @param string $datatype
     * @param string $scope
     * @param bool|string $fname
     * @return mixed
     */
    public function _store( $filePath, $datatype, $scope, $fname = false )
    {
        if ( !is_readable( $filePath ) )
        {
            eZDebug::writeError( "Unable to store file '$filePath' since it is not readable.", __METHOD__ );
            return null;
        }
        if ( $fname )
            $fname .= "::_store($filePath, $datatype, $scope)";
        else
            $fname = "_store($filePath, $datatype, $scope)";

        $return = $this->_protect( array( $this, '_storeInner' ), $fname,
                                   $filePath, $datatype, $scope, $fname );

        $this->eventHandler->notify( 'cluster/deleteFile', array( $filePath ) );

        return $return;
    }

    /**
     * Callback function used to perform the actual file store operation
     *
     * @param string $filePath
     * @param string $datatype
     * @param string $scope
     * @param string $fname
     * @return bool|eZMySQLBackendError
     */
    public function _storeInner( $filePath, $datatype, $scope, $fname )
    {
        clearstatcache( true, $filePath );
        $fileMTime = filemtime( $filePath );
        // This check intentionally match 'false' value as well
        if ( $fileMTime == 0 )
            $fileMTime = -1;
        $contentLength = filesize( $filePath );

        if ( !$this->_insertUpdate( $this->dbTable( $filePath ),
            array( 'datatype' => $datatype,
                   'name' => $filePath,
                   'name_trunk' => self::nameTrunk( $filePath, $scope ),
                   'name_hash' => md5( $filePath ),
                   'scope' => $scope,
                   'size' => (int)$contentLength,
                   'mtime' => (int)$fileMTime,
                   'expired' => ( $fileMTime < 0 ) ? 1 : 0 ),
            array( 'datatype', 'scope', 'size', 'mtime', 'expired' ),
            $fname ) )
        {
            return false;
        }

        if ( !$this->dfsbackend->copyToDFS( $filePath ) )
        {
            return $this->_fail( "Failed to copy FS://$filePath to DFS://$filePath" );
        }

        return true;
    }

    /**
     * Stores $contents as the contents of $filePath to the cluster
     *
     * @param string $filePath
     * @param string $contents
     * @param string $scope
     * @param string $datatype
     * @param bool|int $mtime
     * @param bool|string $fname
     * @return mixed
     */
    public function _storeContents( $filePath, $contents, $scope, $datatype, $mtime = false, $fname = false )
    {
        if ( $fname )
            $fname .= "::_storeContents($filePath, ..., $scope, $datatype)";
        else
            $fname = "_storeContents($filePath, ..., $scope, $datatype)";

        return $this->_protect(
            array( $this, '_storeContentsInner' ),
            $fname,
            $filePath,
            $contents,
            $scope,
            $datatype,
            $mtime,
            $fname
        );
    }

    /**
     * @param string $filePath
     * @param string $contents
     * @param string $scope
     * @param string $datatype
     * @param int|bool $curTime
     * @param string|bool $fname
     * @return bool|eZMySQLBackendError
     */
    public function _storeContentsInner( $filePath, $contents, $scope, $datatype, $curTime, $fname )
    {
        if ( $curTime === false )
            $curTime = time();

        if ( !$this->_insertUpdate( $this->dbTable( $filePath ),
            array( 'datatype' => $datatype,
                   'name' => $filePath,
                   'name_trunk' => self::nameTrunk( $filePath, $scope ),
                   'name_hash' => md5( $filePath ),
                   'scope' => $scope,
                   'size' => strlen( $contents ),
                   'mtime' => (int)$curTime,
                   'expired' => ( $curTime < 0 ) ? 1 : 0 ),
            array( 'datatype', 'name_trunk', 'scope', 'size', 'mtime', 'expired' ),
            $fname ) )
        {
            return false;
        }

        if ( !$this->dfsbackend->createFileOnDFS( $filePath, $contents ) )
        {
            return $this->_fail( "Failed to open DFS://$filePath for writing" );
        }

        $this->eventHandler->notify( 'cluster/deleteFile', array( $filePath ) );

        return true;
    }

    /**
     * Returns the list of cluster files, filtered by the optional params
     *
     * @param array|bool $scopes filter by array of scopes to include in the list
     * @param bool $excludeScopes if true, $scopes param acts as an exclude filter
     * @param array|bool $limit limits the search to offset limit[0], limit limit[1]
     * @param bool|string $path filter to include entries only including $path
     * @return array
     * @throws Exception on a database error
     */
    public function _getFileList( $scopes = false, $excludeScopes = false, $limit = false, $path = false )
    {
        $filePathList = array();
        $tables = array_unique( array( $this->metaDataTable, $this->metaDataTableCache ) );

        foreach ( $tables as $table )
        {
            $conditions = array();
            $params = array();
            if ( is_array( $scopes ) && count( $scopes ) > 0 )
            {
                $placeholders = array();
                foreach ( array_values( $scopes ) as $i => $scope )
                {
                    $placeholders[] = ":s$i";
                    $params[":s$i"] = $scope;
                }
                $conditions[] = 'scope ' . ( $excludeScopes ? 'NOT ' : '' ) . 'IN (' . implode( ', ', $placeholders ) . ')';
            }
            if ( $path != false )
            {
                $conditions[] = 'name GLOB :path';
                $params[':path'] = self::globPrefix( $path );
            }
            $query = 'SELECT name FROM ' . $table;
            if ( $conditions )
                $query .= ' WHERE ' . implode( ' AND ', $conditions );
            if ( $limit && array_sum( $limit ) )
            {
                $query .= ' ORDER BY name_hash LIMIT ' . (int)$limit[1] . ' OFFSET ' . (int)$limit[0];
            }
            $stmt = $this->_query( $query, "_getFileList( array( " . implode( ', ', is_array( $scopes ) ? $scopes : array() ) . " ), $excludeScopes )", true, $params );
            if ( !$stmt )
            {
                eZDebug::writeError( 'Unable to get file list', __METHOD__ );
                throw new Exception( "dfs/sqlite DB error: " . $this->lastErrorText() . "\nSQL Query: $query" );
            }

            foreach ( $stmt->fetchAll( PDO::FETCH_NUM ) as $row )
            {
                $filePathList[] = $row[0];
            }
        }

        return $filePathList;
    }

    /**
     * Handles a DB error, displaying it as an eZDebug error
     *
     * @param string $msg Message to display
     * @param string $sql SQL query to display error for
     * @return void
     */
    protected function _die( $msg, $sql = null )
    {
        eZDebug::writeError( $sql, "$msg: " . $this->lastErrorText() );
    }

    /**
     * Performs an insert of the given items in $array.
     *
     * @param string $table
     * @param array $array column => value
     * @param string $fname
     * @return bool
     */
    public function _insert( $table, $array, $fname )
    {
        $params = array();
        foreach ( $array as $column => $value )
            $params[':' . $column] = $value;
        $query = "INSERT INTO $table (" . join( ", ", array_keys( $array ) ) . ") VALUES (" . join( ", ", array_keys( $params ) ) . ")";
        return $this->_query( $query, $fname, true, $params ) !== false;
    }

    /**
     * Inserts a row or, when its name_hash exists, updates the columns $updateColumns of it, in one statement
     * (INSERT ... ON CONFLICT (name_hash) DO UPDATE).
     *
     * @param string $table
     * @param array $array column => value
     * @param array $updateColumns the columns taken from the new values on a conflict
     * @param string $fname
     * @param bool $reportError
     * @return bool
     */
    protected function _insertUpdate( $table, $array, $updateColumns, $fname, $reportError = true )
    {
        $params = array();
        foreach ( $array as $column => $value )
            $params[':' . $column] = $value;
        $set = array();
        foreach ( $updateColumns as $column )
            $set[] = "$column = EXCLUDED.$column";
        $query = "INSERT INTO $table (" . join( ", ", array_keys( $array ) ) . ") VALUES (" . join( ", ", array_keys( $params ) ) . ")" .
                 " ON CONFLICT (name_hash) DO UPDATE SET " . join( ", ", $set );
        return $this->_query( $query, $fname, $reportError, $params ) !== false;
    }

    /**
     * Runs a select query and returns one numeric indexed row from the result, false if there is none.
     *
     * @param string $query
     * @param string $fname
     * @param bool|string $error
     * @param bool $debug
     * @param array $params
     * @return array|false
     */
    protected function _selectOneRow( $query, $fname, $error = false, $debug = false, array $params = array() )
    {
        return $this->_selectOne( $query, $fname, $error, $debug, PDO::FETCH_NUM, $params );
    }

    /**
     * Runs a select query and returns one associative row from the result, false if there is none.
     *
     * @param string $query
     * @param string $fname
     * @param bool|string $error
     * @param bool $debug
     * @param array $params
     * @return array|false
     */
    protected function _selectOneAssoc( $query, $fname, $error = false, $debug = false, array $params = array() )
    {
        return $this->_selectOne( $query, $fname, $error, $debug, PDO::FETCH_ASSOC, $params );
    }

    /**
     * Runs a select query and returns its one row, false if there is none.
     *
     * @param string $query
     * @param string $fname
     * @param bool|string $error
     * @param bool $debug
     * @param int $fetchMode PDO::FETCH_NUM or PDO::FETCH_ASSOC
     * @param array $params
     * @return array|false
     * @throws eZDFSFileHandlerTableNotFoundException
     * @throws Exception on another database error
     */
    protected function _selectOne( $query, $fname, $error, $debug, $fetchMode, array $params = array() )
    {
        $time = microtime( true );
        $stmt = $this->_query( $query, $fname, false, $params );
        if ( !$stmt )
        {
            $this->_error( $query, $fname, $error );
            throw new Exception( "dfs/sqlite DB error: " . $this->lastErrorText() . "\nSQL Query: $query" );
        }

        $rows = $stmt->fetchAll( $fetchMode );
        if ( count( $rows ) > 1 )
        {
            eZDebug::writeError( 'Duplicate entries found', $fname );
        }
        elseif ( count( $rows ) === 0 )
        {
            return false;
        }

        $row = $rows[0];
        if ( $debug && self::$dbparams['sql_output'] )
            $this->_report( "SQL for _selectOne:\n" . $query . "\n\nRESULT:\n" . var_export( $row, true ), $fname, microtime( true ) - $time );
        return $row;
    }

    /**
     * Starts a new transaction with BEGIN IMMEDIATE: the database's write lock is taken at once (waiting up to the
     * busy timeout), so what the transaction reads cannot change before it writes. If a transaction is already
     * started nothing is executed.
     *
     * Every caller counts the transaction, started or not, and ends it with _commit() or _rollback().
     *
     * @param string|bool $fname
     * @return bool whether the transaction runs; false when the database stayed busy (the code is kept, and
     *              _protect() tries again) or the BEGIN failed otherwise
     */
    protected function _begin( $fname = false )
    {
        $this->transactionCount++;
        if ( $this->transactionCount == 1 )
        {
            $this->transactionFailed = false;
            try
            {
                $this->db->exec( 'BEGIN IMMEDIATE' );
                $this->inSqlTransaction = true;
            }
            catch ( PDOException $e )
            {
                $this->recordError( $e );
                if ( !self::isRetryableCode( $this->lastErrorCode() ) )
                    $this->_error( 'BEGIN IMMEDIATE', $fname ? "$fname::_begin" : "_begin" );
                return false;
            }
        }
        return $this->inSqlTransaction;
    }

    /**
     * Commits the outermost transaction. A transaction in which a statement failed is rolled back instead, so a
     * transaction is written whole or not at all, as on the other engines.
     *
     * @param string|bool $fname
     * @return bool|null true when committed, false when it failed or was rolled back, null for a sub-transaction
     */
    protected function _commit( $fname = false )
    {
        $this->transactionCount--;
        if ( $this->transactionCount > 0 )
            return null;
        $this->transactionCount = 0;

        if ( $this->transactionFailed )
        {
            $this->rollbackConnection();
            eZDebug::writeError( "A statement of the transaction failed, it was rolled back: " . $this->lastErrorText(), $fname ? "$fname::_commit" : "_commit" );
            return false;
        }
        if ( !$this->inSqlTransaction )
        {
            // the BEGIN failed: nothing ran in a transaction to commit
            return false;
        }
        try
        {
            $this->db->exec( 'COMMIT' );
            $this->inSqlTransaction = false;
            return true;
        }
        catch ( PDOException $e )
        {
            $this->recordError( $e );
            $this->rollbackConnection();
            $this->_error( 'COMMIT', $fname ? "$fname::_commit" : "_commit" );
            return false;
        }
    }

    /**
     * Rolls the outermost transaction back. A sub-transaction only counts down.
     *
     * @param string|bool $fname
     */
    protected function _rollback( $fname = false )
    {
        $this->transactionCount--;
        if ( $this->transactionCount <= 0 )
        {
            $this->transactionCount = 0;
            $this->rollbackConnection();
        }
    }

    /**
     * ROLLBACK on the connection, whatever state it is in.
     */
    private function rollbackConnection()
    {
        $this->transactionFailed = false;
        if ( !$this->inSqlTransaction )
            return;
        $this->inSqlTransaction = false;
        try
        {
            if ( $this->db )
                $this->db->exec( 'ROLLBACK' );
        }
        catch ( PDOException $e )
        {
            // SQLite has rolled back already after some errors (a full disk, an interrupt): nothing is open
            $lastError = $this->lastError;
            $this->recordError( $e );
            if ( stripos( $e->getMessage(), 'no transaction is active' ) !== false )
                $this->lastError = $lastError;
        }
    }

    /**
     * Protects a callback with a database transaction.
     *
     * The first argument is the callback and the second the name of the function (for query reporting); the
     * remaining arguments go to the callback. false or an eZMySQLBackendError from the callback rolls back and
     * returns false; any other value is committed and returned.
     *
     * A database that stayed busy for the whole busy timeout (SQLITE_BUSY or SQLITE_LOCKED, at the BEGIN
     * IMMEDIATE or at a statement) rolls back and runs the callback again, up to DBExecuteRetries times in all,
     * with a short pause that grows with each try and never exceeds MAX_RETRY_PAUSE. The callback never runs
     * outside a transaction: when the BEGIN fails, it is not called. Inside an outer transaction there is nothing
     * to retry: the failure goes up to the outermost _protect(), which retries.
     *
     * @return bool|mixed
     */
    protected function _protect()
    {
        $args = func_get_args();
        $callback = array_shift( $args );
        $fname    = array_shift( $args );

        $maxTries = max( 1, (int)self::$dbparams['max_execute_tries'] );
        $nested = $this->transactionCount > 0;
        for ( $try = 1; ; ++$try )
        {
            if ( !$nested )
                $this->retryableErrorSeen = false;
            if ( !$this->_begin( $fname ) )
            {
                $this->_rollback( $fname );
                if ( !$nested && $this->retryableErrorSeen && $try < $maxTries )
                {
                    $this->retryPause( $try );
                    continue;
                }
                if ( !$nested )
                    eZDebug::writeError( "Gave up after $try tries: " . $this->lastErrorText(), $fname );
                return false;
            }

            try
            {
                $result = call_user_func_array( $callback, $args );
            }
            catch ( Exception $e )
            {
                $this->_rollback( $fname );
                if ( !$nested && ( $this->retryableErrorSeen || self::isRetryableCode( self::errorCodeOf( $e ) ) ) && $try < $maxTries )
                {
                    $this->retryPause( $try );
                    continue;
                }
                throw $e;
            }

            if ( $this->retryableErrorSeen )
            {
                $this->_rollback( $fname );
                if ( !$nested && $try < $maxTries )
                {
                    $this->retryPause( $try );
                    continue;
                }
                if ( !$nested )
                    eZDebug::writeError( "Gave up after $try tries: " . $this->lastErrorText(), $fname );
                return false;
            }

            if ( $result === false )
            {
                $this->_rollback( $fname );
                return false;
            }
            elseif ( $result instanceof eZMySQLBackendError )
            {
                eZDebug::writeError( $result->errorValue, $result->errorText );
                $this->_rollback( $fname );
                return false;
            }

            if ( $this->_commit( $fname ) === false )
            {
                if ( !$nested && $this->retryableErrorSeen && $try < $maxTries )
                {
                    $this->retryPause( $try );
                    continue;
                }
                return false;
            }
            return $result;
        }
    }

    /**
     * The pause before the next try of a transaction: about 2^try milliseconds plus jitter, at most
     * MAX_RETRY_PAUSE microseconds.
     *
     * @param int $try
     */
    protected function retryPause( $try )
    {
        $pause = min( self::MAX_RETRY_PAUSE, 1000 * ( 1 << min( $try, 8 ) ) + mt_rand( 0, 1000 ) );
        usleep( $pause );
    }

    /**
     * @param eZMySQLBackendError|bool $res
     */
    protected function _handleErrorType( $res )
    {
        if ( $res === false )
        {
            eZDebug::writeError( "SQL failed" );
        }
        elseif ( $res instanceof eZMySQLBackendError )
        {
            eZDebug::writeError( $res->errorValue, $res->errorText );
        }
    }

    /**
     * Checks if $result is a failure type: false or an eZMySQLBackendError.
     *
     * @param mixed $result
     * @return bool
     */
    protected function _isFailure( $result )
    {
        return $result === false || ( $result instanceof eZMySQLBackendError );
    }

    /**
     * Creates an error object which can be read by some backend functions (the kernel's backend error class,
     * which eZDFSFileHandler tests for, whatever the engine).
     *
     * @param mixed $value The value which is sent to the debug system.
     * @param string|bool $text The text/header for the value.
     * @return eZMySQLBackendError
     */
    protected function _fail( $value, $text = false )
    {
        $value .= "\n" . $this->lastErrorText();
        return new eZMySQLBackendError( $value, $text );
    }

    /**
     * Runs a statement, with $params as bound parameters, and returns the statement, or false on failure (the
     * SQLite result code and message are kept, see lastErrorText()). A missing table throws
     * eZDFSFileHandlerTableNotFoundException, as the MySQLi backend does.
     *
     * @param string $query
     * @param string|bool $fname
     * @param bool $reportError
     * @param array $params
     * @return PDOStatement|false
     * @throws eZDFSFileHandlerTableNotFoundException
     */
    protected function _query( $query, $fname = false, $reportError = true, array $params = array() )
    {
        eZDebug::accumulatorStart( 'sqlite_cluster_query', 'SQLite Cluster', 'DB queries' );
        $time = microtime( true );

        $this->lastError = null;
        try
        {
            if ( $params )
            {
                $stmt = $this->db->prepare( $query );
                foreach ( $params as $name => $value )
                    $stmt->bindValue( $name, $value, is_int( $value ) ? PDO::PARAM_INT : ( $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR ) );
                $stmt->execute();
            }
            else
            {
                $stmt = $this->db->query( $query );
            }
        }
        catch ( PDOException $e )
        {
            $stmt = false;
            $this->recordError( $e );
        }

        $time = microtime( true ) - $time;
        eZDebug::accumulatorStop( 'sqlite_cluster_query' );

        if ( !$stmt )
        {
            if ( self::isMissingTable( $this->lastError[0], $this->lastError[1] ) )
                throw new eZDFSFileHandlerTableNotFoundException( $query, $this->lastError[1] );
            if ( $reportError )
                $this->_error( $query, $fname );
            $this->_report( $query, $fname, $time, false );
            return false;
        }

        $this->_report( $query, $fname, $time, $stmt->rowCount() );
        return $stmt;
    }

    /**
     * Keeps the SQLite result code and message of a failed statement, and marks the transaction it ran in as
     * failed (and as worth another try, when the database stayed busy or a table locked).
     *
     * @param PDOException $e
     */
    protected function recordError( PDOException $e )
    {
        $code = self::errorCodeOf( $e );
        $this->lastError = array( $code, $e->getMessage() );
        if ( $this->transactionCount > 0 )
            $this->transactionFailed = true;
        if ( self::isRetryableCode( $code ) )
            $this->retryableErrorSeen = true;
    }

    /**
     * The SQLite result code of the last failed statement (5 when the database stayed busy), null when the last
     * statement succeeded.
     *
     * @return string|null
     */
    public function lastErrorCode()
    {
        return $this->lastError === null ? null : $this->lastError[0];
    }

    /**
     * "code: message" of the last failed statement, '' when it succeeded.
     *
     * @return string
     */
    protected function lastErrorText()
    {
        return $this->lastError === null ? '' : $this->lastError[0] . ': ' . $this->lastError[1];
    }

    /**
     * Prints error message $error to debug system.
     *
     * @param string $query
     * @param string|bool $fname
     * @param string|array|bool $error
     */
    protected function _error( $query, $fname, $error = "Failed to execute SQL for function:" )
    {
        if ( $error === false )
        {
            $error = "Failed to execute SQL for function:";
        }
        else if ( is_array( $error ) )
        {
            $fname = $error[1];
            $error = $error[0];
        }

        eZDebug::writeError( "$error\n" . $this->lastErrorText(), $fname );
    }

    /**
     * Report SQL $query to debug system.
     *
     * @param string $query
     * @param string|bool $fname
     * @param float $timeTaken
     * @param int|bool $numRows
     * @return void
     */
    public function _report( $query, $fname, $timeTaken, $numRows = false )
    {
        if ( !self::$dbparams['sql_output'] )
            return;

        $rowText = '';
        if ( $numRows !== false )
            $rowText = "$numRows rows, ";
        if ( strlen( (string)$fname ) == 0 )
            $fname = "_query";
        $backgroundClass = ( $this->transactionCount > 0 ? "debugtransaction transactionlevel-$this->transactionCount" : "" );
        eZDebug::writeNotice( "$query", "cluster::sqlite::{$fname}[{$rowText}" . number_format( $timeTaken, 3 ) . " ms] query number per page:" . $this->numQueries++, $backgroundClass );
    }

    /**
     * Attempts to begin cache generation by creating a row for "<path>.generating" (INSERT ... ON CONFLICT DO
     * NOTHING). When the row exists the path is being generated: 'ko' with the seconds left, or, once that time
     * is over, the generation is taken over (one UPDATE that only one process can win).
     *
     * @param string $filePath
     * @param string $generatingFilePath
     * @return array 'result' ok (with 'mtime'), ko (with 'remaining') or error
     */
    public function _startCacheGeneration( $filePath, $generatingFilePath )
    {
        $fname = "_startCacheGeneration( {$filePath} )";
        $table = $this->dbTable( $filePath );
        $nameHash = md5( $generatingFilePath );
        $mtime = time();

        for ( $attempt = 0; $attempt < 3; ++$attempt )
        {
            $stmt = $this->_query(
                "INSERT INTO $table (name, name_trunk, name_hash, scope, datatype, mtime, expired) " .
                "VALUES (:name, :trunk, :hash, '', '', :mtime, 0) ON CONFLICT (name_hash) DO NOTHING",
                $fname, false,
                array( ':name' => $generatingFilePath, ':trunk' => $generatingFilePath, ':hash' => $nameHash, ':mtime' => $mtime )
            );
            if ( !$stmt )
            {
                // the database stayed busy: 'ko', so the caller reads the stale cache; 'remaining' is irrelevant
                if ( self::isRetryableCode( $this->lastErrorCode() ) )
                    return array( 'result' => 'ko', 'remaining' => 0 );

                eZDebug::writeError( "Unexpected error " . $this->lastErrorText() . " when trying to start cache generation on $filePath", __METHOD__ );
                return array( 'result' => 'error' );
            }
            if ( $stmt->rowCount() == 1 )
                return array( 'result' => 'ok', 'mtime' => $mtime );

            // the .generating row exists: the file is being generated
            $row = $this->_selectOneRow( "SELECT mtime FROM $table WHERE name_hash = :hash", $fname, false, false, array( ':hash' => $nameHash ) );
            if ( $row === false )
            {
                // the generation ended between the two statements: try to take the next one
                continue;
            }

            $remainingGenerationTime = $this->remainingCacheGenerationTime( $row );
            if ( $remainingGenerationTime >= 0 )
                return array( 'result' => 'ko', 'remaining' => $remainingGenerationTime );

            eZDebugSetting::writeDebug( 'kernel-clustering', "$filePath generation has timedout, taking over", __METHOD__ );
            $stmt = $this->_query( "UPDATE $table SET mtime = :mtime WHERE name_hash = :hash AND mtime = :previous", $fname, true,
                                   array( ':mtime' => $mtime, ':hash' => $nameHash, ':previous' => (int)$row[0] ) );
            if ( $stmt && $stmt->rowCount() == 1 )
                return array( 'result' => 'ok', 'mtime' => $mtime );
            if ( $stmt )
            {
                // another process took the generation over first
                return array( 'result' => 'ko', 'remaining' => (int)self::$dbparams['cache_generation_timeout'] );
            }
            eZDebug::writeError( "An error occured taking over timedout generating cache file $generatingFilePath (" . $this->lastErrorText() . ")", __METHOD__ );
            return array( 'result' => 'error' );
        }

        return array( 'result' => 'ko', 'remaining' => 0 );
    }

    /**
     * Ends the cache generation for the current file: moves the (meta)data for the .generating file to the
     * actual file, and removes the .generating row.
     *
     * @param string $filePath
     * @param string $generatingFilePath
     * @param bool $rename
     * @return bool
     */
    public function _endCacheGeneration( $filePath, $generatingFilePath, $rename )
    {
        $fname = "_endCacheGeneration( $filePath )";
        $table = $this->dbTable( $filePath );
        $generatingHash = md5( $generatingFilePath );

        if ( $rename === false )
        {
            $this->_query( "DELETE FROM $table WHERE name_hash = :hash", $fname, true, array( ':hash' => $generatingHash ) );
            $this->dfsbackend->delete( $generatingFilePath );
            return true;
        }

        if ( !$this->_begin( $fname ) )
        {
            eZDebug::writeError( "The cluster database stayed busy, the cache generation of $filePath was not ended", $fname );
            $this->_rollback( $fname );
            return false;
        }

        $stmt = $this->_query( "SELECT * FROM $table WHERE name_hash = :hash", $fname, true, array( ':hash' => $generatingHash ) );
        $generatingMetaData = $stmt ? $stmt->fetch( PDO::FETCH_ASSOC ) : false;
        if ( empty( $generatingMetaData ) )
        {
            eZDebug::writeError( "An error occured while ending cache generation,  $generatingFilePath", $fname );
            $this->_rollback( $fname );
            return false;
        }

        $stmt = $this->_query( "SELECT name_hash FROM $table WHERE name_hash = :hash", $fname, false, array( ':hash' => md5( $filePath ) ) );
        if ( !$stmt )
        {
            $this->_rollback( $fname );
            return false;
        }

        if ( $stmt->fetch( PDO::FETCH_NUM ) === false )
        {
            // the file does not exist: the generating row becomes its row (an upsert, in case a process inserted it
            // after the lock above found nothing)
            $metaData = $generatingMetaData;
            $metaData['name'] = $filePath;
            $metaData['name_hash'] = md5( $filePath );
            $metaData['name_trunk'] = self::nameTrunk( $filePath, $metaData['scope'] );
            $metaData['expired'] = 0;
            if ( !$this->_insertUpdate( $table, $metaData, array( 'mtime', 'expired', 'size' ), $fname ) )
            {
                eZDebug::writeError( "An error occured creating the metadata entry for $filePath", $fname );
                $this->_rollback( $fname );
                return false;
            }
        }
        else
        {
            if ( !$this->_query( "UPDATE $table SET mtime = :mtime, expired = 0, size = :size WHERE name_hash = :hash", $fname, true,
                                 array( ':mtime' => (int)$generatingMetaData['mtime'], ':size' => (int)$generatingMetaData['size'], ':hash' => md5( $filePath ) ) ) )
            {
                $this->_rollback( $fname );
                return false;
            }
        }

        if ( !$this->dfsbackend->renameOnDFS( $generatingFilePath, $filePath ) )
        {
            eZDebug::writeError( "An error occured renaming DFS://$generatingFilePath to DFS://$filePath", $fname );
            $this->_rollback( $fname );
            return false;
        }

        $this->_query( "DELETE FROM $table WHERE name_hash = :hash", $fname, true, array( ':hash' => $generatingHash ) );

        return $this->_commit( $fname ) !== false;
    }

    /**
     * Checks if generation has timed out by looking for the .generating file and comparing its timestamp to the
     * one assigned when the file was created
     *
     * @param string $generatingFilePath
     * @param int $generatingFileMtime
     * @return bool true if the file didn't timeout, false otherwise
     */
    public function _checkCacheGenerationTimeout( $generatingFilePath, $generatingFileMtime )
    {
        $fname = "_checkCacheGenerationTimeout( $generatingFilePath, $generatingFileMtime )";
        $table = $this->dbTable( $generatingFilePath );
        $nameHash = md5( $generatingFilePath );

        // the update only succeeds if the mtime wasn't changed in between (SQLite counts a row updated with the
        // same values, so 1 row is the answer even within one second)
        $stmt = $this->_query( "UPDATE $table SET mtime = :mtime WHERE name_hash = :hash AND mtime = :previous", $fname, true,
                               array( ':mtime' => time(), ':hash' => $nameHash, ':previous' => (int)$generatingFileMtime ) );
        if ( !$stmt )
            return false;

        if ( $stmt->rowCount() == 1 )
            return true;

        eZDebugSetting::writeDebug( 'kernel-clustering', "No rows affected for '$generatingFilePath', record has been modified", __METHOD__ );
        return false;
    }

    /**
     * Aborts the cache generation process by removing the .generating file
     *
     * @param string $generatingFilePath
     * @return void
     */
    public function _abortCacheGeneration( $generatingFilePath )
    {
        $fname = "_abortCacheGeneration( $generatingFilePath )";

        if ( !$this->_begin( $fname ) )
        {
            // the row stays; the generation times out and the next process takes it over
            $this->_rollback( $fname );
            return;
        }
        $this->_query( "DELETE FROM " . $this->dbTable( $generatingFilePath ) . " WHERE name_hash = :hash", $fname, true,
                       array( ':hash' => md5( $generatingFilePath ) ) );
        $this->dfsbackend->delete( $generatingFilePath );
        $this->_commit( $fname );
    }

    /**
     * Returns the name_trunk for a file path: the part shared by the files of one view cache or template block
     * cache entry, which _deleteByDirList() expires in one statement.
     *
     * @param string $filePath
     * @param string $scope
     * @return string
     */
    public static function nameTrunk( $filePath, $scope )
    {
        switch ( $scope )
        {
            case 'viewcache':
            {
                $fileName = basename( $filePath );
                $nameTrunk = dirname( $filePath ) . '/' . substr( $fileName, 0, strpos( $fileName, '-' ) + 1 );
            } break;

            case 'template-block':
            {
                $templateBlockCacheDir = eZTemplateCacheBlock::templateBlockCacheDir();
                $templateBlockPath = str_replace( $templateBlockCacheDir, '', $filePath );
                if ( strstr( $templateBlockPath, 'subtree/' ) !== false )
                {
                    // 6 = strlen( 'cache/' );
                    $len = strlen( $templateBlockCacheDir ) + strpos( $templateBlockPath, 'cache/' ) + 6;
                    $nameTrunk = substr( $filePath, 0, $len );
                }
                else
                {
                    $nameTrunk = $filePath;
                }
            } break;

            default:
            {
                $nameTrunk = $filePath;
            }
        }
        return $nameTrunk;
    }

    /**
     * Returns the remaining time, in seconds, before the generating file times out
     *
     * @param array $row
     * @return int Remaining generation seconds. A negative value indicates a timeout.
     */
    protected function remainingCacheGenerationTime( $row )
    {
        if ( !isset( $row[0] ) )
            return -1;

        return ( (int)$row[0] + (int)self::$dbparams['cache_generation_timeout'] ) - time();
    }

    /**
     * Returns the list of expired files
     *
     * @param array $scopes Array of scopes to consider. At least one.
     * @param array|bool $limit array( offset, count ), false for unlimited
     * @param bool|int $expiry Number of seconds, only items older than this will be returned.
     * @return array(filepath)
     * @throws ezcBaseValueException
     */
    public function expiredFilesList( $scopes, $limit = array( 0, 100 ), $expiry = false )
    {
        if ( count( $scopes ) == 0 )
            throw new ezcBaseValueException( 'scopes', $scopes, "array of scopes", "parameter" );

        $tables = array_unique( array( $this->metaDataTable, $this->metaDataTableCache ) );
        $filePathList = array();

        foreach ( $tables as $table )
        {
            $placeholders = array();
            $params = array();
            foreach ( array_values( $scopes ) as $i => $scope )
            {
                $placeholders[] = ":s$i";
                $params[":s$i"] = $scope;
            }
            $query = "SELECT name FROM $table WHERE expired = 1 AND scope IN (" . implode( ', ', $placeholders ) . ")";
            if ( $expiry !== false )
            {
                $query .= ' AND mtime < :expiry';
                $params[':expiry'] = time() - (int)$expiry;
            }
            if ( $limit !== false )
            {
                $query .= " LIMIT " . (int)$limit[1] . " OFFSET " . (int)$limit[0];
            }
            $stmt = $this->_query( $query, __METHOD__, true, $params );
            if ( !$stmt )
                continue;

            foreach ( $stmt->fetchAll( PDO::FETCH_NUM ) as $row )
                $filePathList[] = $row[0];
        }

        return $filePathList;
    }

    /**
     * Registers $listener as the cluster event listener.
     *
     * @param eZClusterEventListener $listener
     * @return void
     */
    public function registerListener( eZClusterEventListener $listener )
    {
        $suppliedEvents = array(
            'cluster/storeMetadata',
            'cluster/loadMetadata',
            'cluster/fileExists',
            'cluster/deleteFile',
            'cluster/deleteByLike',
            'cluster/deleteByDirList',
            'cluster/deleteByNametrunk'
        );

        foreach ( $suppliedEvents as $eventName )
        {
            list( , $method ) = explode( '/', $eventName );
            $this->eventHandler->attach( $eventName, array( $listener, $method ) );
        }
    }

    /**
     * Deletes a batch of cache files from the storage table (DELETE ... LIMIT is a compile-time option of SQLite:
     * the rows are picked by a sub-select).
     *
     * @param int $limit
     * @return int The number of deleted rows
     * @throws RuntimeException on a database error
     * @throws InvalidArgumentException if the split table feature is disabled
     */
    public function deleteCacheFiles( $limit )
    {
        if ( $this->metaDataTable === $this->metaDataTableCache )
        {
            throw new InvalidArgumentException( "The split table features is disabled: cache and storage table are identical" );
        }

        $glob = self::globPrefix( eZSys::cacheDirectory() . DIRECTORY_SEPARATOR );
        $table = $this->metaDataTable;
        $query = "DELETE FROM $table WHERE name_hash IN (SELECT name_hash FROM $table WHERE name GLOB :glob LIMIT " . (int)$limit . ")";
        $stmt = $this->_query( $query, __METHOD__, true, array( ':glob' => $glob ) );
        if ( !$stmt )
        {
            throw new RuntimeException( "SQLite error in $query\n" . $this->lastErrorText() );
        }

        return $stmt->rowCount();
    }

    /**
     * Transforms $filePath so that it contains a valid href to the file, wherever it is stored.
     *
     * @param string $filePath
     * @return string
     */
    public function applyServerUri( $filePath )
    {
        return $this->dfsbackend->applyServerUri( $filePath );
    }

    /**
     * DB connection handle
     * @var PDO|null
     */
    public $db = null;

    /**
     * DB connection parameters
     * @var array|null
     */
    protected static $dbparams = null;

    /**
     * Amount of executed queries, for debugging purpose
     * @var int
     */
    protected $numQueries = 0;

    /**
     * Current transaction level.
     * @var int
     */
    protected $transactionCount = 0;

    /**
     * Whether a statement of the running transaction failed (it is rolled back, not committed)
     * @var bool
     */
    protected $transactionFailed = false;

    /**
     * Whether the database stayed busy (or a table locked) in the running try of _protect()
     * @var bool
     */
    protected $retryableErrorSeen = false;

    /**
     * Whether the connection is inside the BEGIN IMMEDIATE of the outermost transaction
     * @var bool
     */
    protected $inSqlTransaction = false;

    /**
     * array( SQLite result code, message ) of the last failed statement, null after a successful one
     * @var array|null
     */
    protected $lastError = null;

    /**
     * Distributed filesystem backend
     * @var eZDFSFileHandlerDFSBackendInterface
     */
    protected $dfsbackend = null;

    /**
     * Event handler
     * @var ezpEvent
     */
    protected $eventHandler;

    /**
     * custom dfs table name support
     * @var string
     */
    protected $metaDataTable = 'ezdfsfile';

    /**
     * Custom DFS table for cache storage.
     * @var string
     */
    protected $metaDataTableCache = 'ezdfsfile_cache';

    /**
     * Cache files directory (site.ini [FileSettings] CacheDir)
     * @var string
     */
    protected $cacheDir;

    /**
     * Storage directory (site.ini [FileSettings] StorageDir)
     * @var string
     */
    protected $storageDir;
}
