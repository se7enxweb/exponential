<?php
/**
 * File containing the expDFSFileHandlerOracleBackend class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The DFS cluster metadata backend for Oracle Database 12.1 and later, in the kernel: the database half of
 * eZDFSFileHandler on one or several web servers, with only the PHP extension oci8.
 *
 * Written after the structure of the kernel's PostgreSQL backend (expDFSFileHandlerPostgresqlBackend, itself after
 * the ezpostgresqlcluster extension by eZ Systems and 7x's fork of it) and method for method against the MySQLi and
 * SQLite backends; the Oracle specifics come from the ezoracle extension's eZDFSFileHandlerOracleBackend (eZ Systems,
 * GPL-2.0), which this class replaces for the kernel. The parity checks of
 * tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSBackendParityChecks.php pass on it. What differs
 * (doc/eng-us/features/6.0/oracle-dfs-cluster.md lists it all):
 *
 *  - oci8 with every value a bound variable; a new connection per backend (oci_new_connect), or a persistent one
 *    with DBPersistentConnection=enabled; the client character set AL32UTF8;
 *  - bounded lock waits: a statement that changes rows first locks them with SELECT ... FOR UPDATE WAIT n
 *    ([eZDFSClusteringSettings] DBLockTimeout, seconds, default 10), since an Oracle DML statement waits for a row
 *    lock without limit; a wait that ran out (ORA-30006), a deadlock (ORA-00060), a busy resource (ORA-00054), a
 *    serialization failure (ORA-08177) and the duplicate key of two concurrent upserts (ORA-00001) are tried again
 *    by _protect(), up to DBExecuteRetries tries, with a short bounded pause;
 *  - MERGE for the upserts; a plain INSERT, whose duplicate key (ORA-00001) means "being generated", for the
 *    one-winner cache generation;
 *  - SIZE is a reserved word: the column is filesize (as in the ezoracle extension's table), returned as size;
 *  - Oracle stores the empty string as NULL: datatype and scope may be NULL and are read back as '';
 *  - LIKE with ESCAPE '\' (binary, case sensitive, using the name index for a prefix); a shell wildcard with {a,b}
 *    alternatives becomes several LIKE patterns; OFFSET ... FETCH NEXT for the limits (hence 12.1);
 *  - NUMBER(19) sizes and times (kernel/sql/oracle/cluster_dfs_schema.sql), no 2038 limit.
 *
 * Usable on PHP 7.4, clean on PHP 8.0 to 8.5.
 *
 * Settings: file.ini [eZDFSClusteringSettings] DBBackend=expDFSFileHandlerOracleBackend, DBName (the connect string:
 * Easy Connect host:port/service, a TNS alias or a full descriptor), DBUser, DBPassword, DBConnectRetries,
 * DBExecuteRetries, optional DBLockTimeout and DBPersistentConnection (enabled|disabled). DBHost, DBPort and DBSocket
 * are not used.
 *
 * @package kernel
 */
class expDFSFileHandlerOracleBackend implements eZClusterEventNotifier, expDFSFileHandlerDBBackendInterface
{
    /**
     * Wait for n microseconds until retry if copy fails, to avoid DFS overload.
     */
    const TIME_UNTIL_RETRY = 100;

    /** Seconds a statement waits for a row lock when DBLockTimeout is not set */
    const DEFAULT_LOCK_TIMEOUT = 10;

    /** Longest pause between two tries of a transaction, in microseconds */
    const MAX_RETRY_PAUSE = 200000;

    /** Rows deleted per statement by _purgeByLike() (Oracle allows 1000 expressions in an IN list) */
    const DELETE_CHUNK = 500;

    /** Most LIKE patterns a wildcard with {a,b} alternatives may expand to */
    const MAX_WILDCARD_PATTERNS = 256;

    /** The Oracle errors this backend acts on */
    const ORA_UNIQUE_CONSTRAINT = 1;
    const ORA_RESOURCE_BUSY = 54;
    const ORA_DEADLOCK = 60;
    const ORA_TABLE_MISSING = 942;
    const ORA_SERIALIZATION = 8177;
    const ORA_WAIT_TIMEOUT = 30006;

    /** The columns of a file's row, in their order; size is the column filesize */
    const COLUMNS = 'name, name_trunk, name_hash, datatype, scope, filesize, mtime, expired';

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

    // ------------------------------------------------------------------------------------------ pure helpers

    /**
     * Whether a failure with this Oracle error number is worth another try of the whole transaction: a lock wait
     * that ran out (30006), a busy resource (54), a deadlock (60), a serialization failure (8177), or the duplicate
     * key of two upserts that both inserted (1).
     *
     * @param string|int|null $code
     * @return bool
     */
    public static function isRetryableCode( $code )
    {
        if ( $code === null || !preg_match( '/^[0-9]+$/', (string)$code ) )
            return false;
        return in_array( (int)$code, array( self::ORA_WAIT_TIMEOUT, self::ORA_RESOURCE_BUSY, self::ORA_DEADLOCK, self::ORA_SERIALIZATION, self::ORA_UNIQUE_CONSTRAINT ), true );
    }

    /**
     * One character as a literal of a LIKE pattern with ESCAPE '\'.
     *
     * @param string $char
     * @return string
     */
    private static function likeLiteral( $char )
    {
        return ( $char === '%' || $char === '_' || $char === '\\' ) ? '\\' . $char : $char;
    }

    /**
     * The LIKE pattern (ESCAPE '\') of a LIKE pattern the callers built: % is any text, an underscore is a literal
     * (as in the MySQLi backend), a backslash makes the next character a literal.
     *
     * @param string $like
     * @return string
     */
    public static function likePattern( $like )
    {
        $like = (string)$like;
        $out = '';
        $length = strlen( $like );
        for ( $i = 0; $i < $length; ++$i )
        {
            $char = $like[$i];
            if ( $char === '\\' && $i + 1 < $length )
                $out .= self::likeLiteral( $like[++$i] );
            elseif ( $char === '%' )
                $out .= '%';
            else
                $out .= self::likeLiteral( $char );
        }
        return $out;
    }

    /**
     * The LIKE pattern (ESCAPE '\') of every name that starts with $prefix.
     *
     * @param string $prefix
     * @return string
     */
    public static function likePrefix( $prefix )
    {
        $prefix = (string)$prefix;
        $out = '';
        $length = strlen( $prefix );
        for ( $i = 0; $i < $length; ++$i )
            $out .= self::likeLiteral( $prefix[$i] );
        return $out . '%';
    }

    /**
     * The LIKE patterns (ESCAPE '\') of a shell wildcard: ? one character, * any text, {a,b} one of the alternatives
     * (each alternative becomes a pattern of its own, nested braces too); every other character is a literal. A
     * comma outside braces stays a comma; an unclosed brace is closed at the end.
     *
     * @param string $wildcard
     * @return string[]
     * @throws InvalidArgumentException when the alternatives expand to more than MAX_WILDCARD_PATTERNS patterns
     */
    public static function wildcardToLikes( $wildcard )
    {
        $wildcard = (string)$wildcard;
        $pos = 0;
        return array_values( array_unique( self::expandWildcard( $wildcard, $pos, false ) ) );
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
                case '?': $piece = '_'; break;
                case '*': $piece = '%'; break;
                default: $piece = self::likeLiteral( $char );
            }
            foreach ( $patterns as $i => $pattern )
                $patterns[$i] = $pattern . $piece;
        }
        return $patterns;
    }

    /**
     * A row as the other backends return it (pure): lower case keys, filesize as size, NULL as ''.
     *
     * @param array $row a row of oci_fetch_array( OCI_ASSOC | OCI_RETURN_NULLS )
     * @return array
     */
    public static function normalizeRow( array $row )
    {
        $out = array();
        foreach ( $row as $key => $value )
        {
            $key = strtolower( (string)$key );
            if ( $key === 'filesize' )
                $key = 'size';
            $out[$key] = $value === null ? '' : (string)$value;
        }
        return $out;
    }

    /**
     * Returns the database table name to use for the specified file.
     *
     * For files detected as cache files the cache table is returned, if not the generic table is returned.
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

    // ------------------------------------------------------------------------------------------ connection

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
            self::$dbparams['dbname'] = $fileINI->variable( 'eZDFSClusteringSettings', 'DBName' );
            self::$dbparams['user']   = $fileINI->variable( 'eZDFSClusteringSettings', 'DBUser' );
            self::$dbparams['pass']   = $fileINI->variable( 'eZDFSClusteringSettings', 'DBPassword' );

            self::$dbparams['max_connect_tries'] = $fileINI->variable( 'eZDFSClusteringSettings', 'DBConnectRetries' );
            self::$dbparams['max_execute_tries'] = $fileINI->variable( 'eZDFSClusteringSettings', 'DBExecuteRetries' );
            self::$dbparams['lock_timeout'] = $fileINI->hasVariable( 'eZDFSClusteringSettings', 'DBLockTimeout' )
                ? $fileINI->variable( 'eZDFSClusteringSettings', 'DBLockTimeout' )
                : self::DEFAULT_LOCK_TIMEOUT;
            self::$dbparams['persistent_connection'] = $fileINI->hasVariable( 'eZDFSClusteringSettings', 'DBPersistentConnection' )
                && $fileINI->variable( 'eZDFSClusteringSettings', 'DBPersistentConnection' ) == 'enabled';

            self::$dbparams['sql_output'] = $siteINI->variable( "DatabaseSettings", "SQLOutput" ) == "enabled";

            self::$dbparams['cache_generation_timeout'] = $siteINI->variable( "ContentSettings", "CacheGenerationTimeout" );
        }

        if ( !function_exists( 'oci_new_connect' ) )
            throw new eZClusterHandlerDBNoConnectionException( (string)self::$dbparams['dbname'], (string)self::$dbparams['user'], '',
                "The PHP extension oci8 is not loaded" );

        $this->lockTimeout = max( 1, (int)self::$dbparams['lock_timeout'] );
        $maxTries = max( 1, (int)self::$dbparams['max_connect_tries'] );
        $error = '';
        $this->db = null;
        eZDebug::accumulatorStart( 'oracle_cluster_connect', 'Oracle Cluster', 'Cluster database connection' );
        for ( $tries = 0; $tries < $maxTries; ++$tries )
        {
            $connect = self::$dbparams['persistent_connection'] ? 'oci_pconnect' : 'oci_new_connect';
            $db = @$connect( (string)self::$dbparams['user'], (string)self::$dbparams['pass'], (string)self::$dbparams['dbname'], 'AL32UTF8' );
            if ( $db )
            {
                $this->db = $db;
                break;
            }
            $e = oci_error();
            $error = is_array( $e ) ? 'ORA-' . sprintf( '%05d', $e['code'] ) . ': ' . $e['message'] : 'oci_connect() failed';
        }
        eZDebug::accumulatorStop( 'oracle_cluster_connect' );
        if ( !$this->db )
        {
            throw new eZClusterHandlerDBNoConnectionException( (string)self::$dbparams['dbname'], (string)self::$dbparams['user'], '', $error );
        }

        $this->transactionCount = 0;
        $this->transactionFailed = false;
        $this->lastError = null;

        // DFS setup
        $this->dfsbackend = eZDFSFileHandlerBackendFactory::build();
    }

    /**
     * Disconnects the handler from the database: an open transaction is rolled back.
     */
    public function _disconnect()
    {
        if ( $this->db )
        {
            if ( $this->transactionCount > 0 )
                @oci_rollback( $this->db );
            if ( !self::$dbparams['persistent_connection'] )
                @oci_close( $this->db );
        }
        $this->db = null;
        $this->transactionCount = 0;
        $this->transactionFailed = false;
    }

    // ------------------------------------------------------------------------------------------ the backend

    /**
     * Creates a copy of a file in DB+DFS
     *
     * @param string $srcFilePath Source file
     * @param string $dstFilePath Destination file
     * @param bool|string $fname
     * @return bool
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
        if ( oci_num_rows( $stmt ) == 1 )
        {
            $this->dfsbackend->delete( $filePath );
        }

        $this->eventHandler->notify( 'cluster/deleteFile', array( $filePath ) );

        return true;
    }

    /**
     * Purges meta-data and file-data for files matching a pattern using a SQL LIKE syntax.
     *
     * The rows are locked by SELECT ... FOR UPDATE SKIP LOCKED: rows another process holds are left for a later
     * run, so two purges never wait for each other.
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
        $where = " WHERE name LIKE :pattern ESCAPE '\\'";
        $params = array( ':pattern' => self::likePattern( $like ) );
        if ( $expiry !== false )
        {
            $where .= " AND mtime < :expiry";
            $params[':expiry'] = (int)$expiry;
        }
        elseif ( $onlyExpired )
        {
            $where .= " AND expired = 1";
        }
        if ( $limit )
            $where .= " AND ROWNUM <= " . (int)$limit;

        $this->_begin( $fname );

        $stmt = $this->_query( "SELECT name, name_hash FROM $table{$where} FOR UPDATE SKIP LOCKED", $fname, true, $params );
        if ( !$stmt )
        {
            $this->_rollback( $fname );
            return $this->_fail( "Selecting file metadata by like statement $like failed" );
        }
        $files = array();
        $hashes = array();
        while ( ( $row = oci_fetch_array( $stmt, OCI_NUM | OCI_RETURN_NULLS ) ) !== false )
        {
            $files[] = $row[0];
            $hashes[] = $row[1];
        }
        if ( count( $hashes ) == 0 )
        {
            $this->_rollback( $fname );
            return 0;
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
            $deletedDBFiles += oci_num_rows( $stmt );
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
     * Callback method used by by _delete to delete a single file: the row is locked (a bounded wait), then expired.
     *
     * @param string $filePath Path of the file to delete
     * @param string $fname Optional caller name for debugging
     * @return bool|eZMySQLBackendError
     */
    protected function _deleteInner( $filePath, $fname )
    {
        $table = $this->dbTable( $filePath );
        $params = array( ':hash' => md5( $filePath ) );
        if ( !$this->lockRows( $table, "name_hash = :hash", $params, $fname ) )
            return $this->_fail( "Locking file $filePath failed" );
        if ( !$this->_query( "UPDATE $table SET mtime = -ABS(mtime), expired = 1 WHERE name_hash = :hash", $fname, true, $params ) )
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
        $table = $this->dbTable( $like );
        $params = array( ':pattern' => self::likePattern( $like ) );
        $where = "name LIKE :pattern ESCAPE '\\'";
        if ( !$this->lockRows( $table, $where, $params, $fname )
             || !$this->_query( "UPDATE $table SET mtime = -ABS(mtime), expired = 1 WHERE $where", $fname, true, $params ) )
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
            $likes = self::wildcardToLikes( $wildcard );
        }
        catch ( InvalidArgumentException $e )
        {
            return $this->_fail( $wildcard, $e->getMessage() );
        }
        $conditions = array();
        $params = array();
        foreach ( $likes as $i => $like )
        {
            $conditions[] = "name LIKE :w$i ESCAPE '\\'";
            $params[":w$i"] = $like;
        }
        $table = $this->dbTable( $wildcard );
        $where = '(' . implode( ' OR ', $conditions ) . ')';
        if ( !$this->lockRows( $table, $where, $params, $fname )
             || !$this->_query( "UPDATE $table SET mtime = -ABS(mtime), expired = 1 WHERE $where", $fname, true, $params ) )
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
                $where = "name_trunk = :value";
                $value = $nametrunk;
            }
            else
            {
                $event = 'cluster/deleteByDirList';
                $eventParameters = array( $commonPath, $dirItem, $commonSuffix );
                $where = "name LIKE :value ESCAPE '\\'";
                $value = self::likePrefix( "$commonPath/$dirItem/$commonSuffix" );
            }
            $params = array( ':value' => $value );
            if ( !$this->lockRows( $table, $where, $params, $fname )
                 || !$this->_query( "UPDATE $table SET mtime = -ABS(mtime), expired = 1 WHERE $where", $fname, true, $params ) )
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
        $metadata = $this->_selectOneAssoc( "SELECT " . self::COLUMNS . " FROM " . $this->dbTable( $filePath ) . " WHERE name_hash = :hash", $fname,
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
     * The source row is locked (a bounded wait), the destination row is written with a MERGE into the destination's
     * own table, and a failure of the DFS copy rolls the transaction back.
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
        $srcHash = md5( $srcFilePath );

        $this->_begin( __METHOD__ );

        $stmt = $this->_query( "SELECT name_hash FROM $srcTable WHERE name_hash = :hash FOR UPDATE WAIT " . $this->lockTimeout, $fname, true, array( ':hash' => $srcHash ) );
        if ( !$stmt || oci_fetch_array( $stmt, OCI_NUM ) === false )
        {
            eZDebug::writeError( "Failed locking file '$srcFilePath'", __METHOD__ );
            $this->_rollback( __METHOD__ );
            return false;
        }

        if ( $this->_exists( $dstFilePath, false, false ) )
            $this->_purge( $dstFilePath, false );

        $mtime = (int)$metaData['mtime'];
        if ( !$this->_insertUpdate( $this->dbTable( $dstFilePath ),
                                    array( 'datatype' => $metaData['datatype'],
                                           'name' => $dstFilePath,
                                           'name_trunk' => self::nameTrunk( $dstFilePath, $metaData['scope'] ),
                                           'name_hash' => md5( $dstFilePath ),
                                           'scope' => $metaData['scope'],
                                           'size' => (int)$metaData['size'],
                                           'mtime' => $mtime,
                                           'expired' => (int)$metaData['expired'] ),
                                    array( 'name', 'name_trunk', 'datatype', 'scope', 'size', 'mtime', 'expired' ), $fname ) )
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
                // a NULL scope (Oracle's empty string) is in no list: NOT IN keeps it, as the other engines keep ''
                $conditions[] = $excludeScopes
                    ? '(scope IS NULL OR scope NOT IN (' . implode( ', ', $placeholders ) . '))'
                    : 'scope IN (' . implode( ', ', $placeholders ) . ')';
            }
            if ( $path != false )
            {
                $conditions[] = "name LIKE :path ESCAPE '\\'";
                $params[':path'] = self::likePrefix( $path );
            }
            $query = 'SELECT name FROM ' . $table;
            if ( $conditions )
                $query .= ' WHERE ' . implode( ' AND ', $conditions );
            if ( $limit && array_sum( $limit ) )
            {
                $query .= ' ORDER BY name_hash OFFSET ' . (int)$limit[0] . ' ROWS FETCH NEXT ' . (int)$limit[1] . ' ROWS ONLY';
            }
            $stmt = $this->_query( $query, "_getFileList( array( " . implode( ', ', is_array( $scopes ) ? $scopes : array() ) . " ), $excludeScopes )", true, $params );
            if ( !$stmt )
            {
                eZDebug::writeError( 'Unable to get file list', __METHOD__ );
                throw new Exception( "dfs/oracle DB error: " . $this->lastErrorText() . "\nSQL Query: $query" );
            }

            while ( ( $row = oci_fetch_array( $stmt, OCI_NUM | OCI_RETURN_NULLS ) ) !== false )
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
     * @param array $array column => value (size is written to the column filesize)
     * @param string $fname
     * @return bool
     */
    public function _insert( $table, $array, $fname )
    {
        $params = array();
        $columns = array();
        foreach ( $array as $column => $value )
        {
            $columns[] = $column === 'size' ? 'filesize' : $column;
            $params[':v_' . $column] = $value; // a bind name is never the column: SIZE is reserved
        }
        $query = "INSERT INTO $table (" . join( ", ", $columns ) . ") VALUES (" . join( ", ", array_keys( $params ) ) . ")";
        return $this->_query( $query, $fname, true, $params ) !== false;
    }

    /**
     * Inserts a row or, when its name_hash exists, updates the columns $updateColumns of it, in one statement
     * (MERGE). The row is locked first (a bounded wait); two processes inserting the same new row at once end in
     * ORA-00001 for one of them, which _protect() tries again (it then updates).
     *
     * @param string $table
     * @param array $array column => value (size is written to the column filesize)
     * @param array $updateColumns the columns taken from the new values when the row exists
     * @param string $fname
     * @param bool $reportError
     * @return bool
     */
    protected function _insertUpdate( $table, $array, $updateColumns, $fname, $reportError = true )
    {
        $params = array();
        $columns = array();
        $values = array();
        $set = array();
        foreach ( $array as $column => $value )
        {
            $dbColumn = $column === 'size' ? 'filesize' : $column;
            $params[':v_' . $column] = $value; // a bind name is never the column: SIZE is reserved
            if ( $column === 'name_hash' )
                continue;
            $columns[] = $dbColumn;
            $values[] = ':v_' . $column;
            if ( in_array( $column, $updateColumns, true ) )
                $set[] = "d.$dbColumn = :v_$column";
        }
        if ( !$this->lockRows( $table, "name_hash = :v_name_hash", array( ':v_name_hash' => $params[':v_name_hash'] ), $fname ) )
            return false;
        $query = "MERGE INTO $table d USING (SELECT :v_name_hash AS name_hash FROM dual) s ON (d.name_hash = s.name_hash)" .
                 ( $set ? " WHEN MATCHED THEN UPDATE SET " . join( ", ", $set ) : "" ) .
                 " WHEN NOT MATCHED THEN INSERT (name_hash, " . join( ", ", $columns ) . ") VALUES (s.name_hash, " . join( ", ", $values ) . ")";
        return $this->_query( $query, $fname, $reportError, $params ) !== false;
    }

    /**
     * Locks the rows of $table that match $where with SELECT ... FOR UPDATE WAIT DBLockTimeout: an Oracle DML
     * statement waits for a locked row without limit, so every change takes its locks this way first. A wait that
     * ran out is ORA-30006, which _protect() tries again.
     *
     * @param string $table
     * @param string $where
     * @param array $params
     * @param string $fname
     * @return bool
     */
    protected function lockRows( $table, $where, array $params, $fname )
    {
        return $this->_query( "SELECT name_hash FROM $table WHERE $where FOR UPDATE WAIT " . $this->lockTimeout, $fname, false, $params ) !== false;
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
        return $this->_selectOne( $query, $fname, $error, $debug, OCI_NUM, $params );
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
        return $this->_selectOne( $query, $fname, $error, $debug, OCI_ASSOC, $params );
    }

    /**
     * Runs a select query and returns its one row, false if there is none.
     *
     * @param string $query
     * @param string $fname
     * @param bool|string $error
     * @param bool $debug
     * @param int $fetchMode OCI_NUM or OCI_ASSOC
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
            throw new Exception( "dfs/oracle DB error: " . $this->lastErrorText() . "\nSQL Query: $query" );
        }

        $rows = array();
        while ( ( $row = oci_fetch_array( $stmt, $fetchMode | OCI_RETURN_NULLS ) ) !== false )
            $rows[] = $row;
        if ( count( $rows ) > 1 )
        {
            eZDebug::writeError( 'Duplicate entries found', $fname );
        }
        elseif ( count( $rows ) === 0 )
        {
            return false;
        }

        $row = $fetchMode === OCI_ASSOC ? self::normalizeRow( $rows[0] ) : array_map( function ( $v ) { return $v === null ? '' : (string)$v; }, $rows[0] );
        if ( $debug && self::$dbparams['sql_output'] )
            $this->_report( "SQL for _selectOne:\n" . $query . "\n\nRESULT:\n" . var_export( $row, true ), $fname, microtime( true ) - $time );
        return $row;
    }

    /**
     * Starts a new transaction (Oracle opens it with the first change; the statements of the transaction run
     * without auto commit). If a transaction is already started nothing is executed.
     *
     * Every caller counts the transaction and ends it with _commit() or _rollback().
     *
     * @param string|bool $fname
     * @return bool
     */
    protected function _begin( $fname = false )
    {
        $this->transactionCount++;
        if ( $this->transactionCount == 1 )
            $this->transactionFailed = false;
        return true;
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
        if ( !@oci_commit( $this->db ) )
        {
            $this->recordError( oci_error( $this->db ) );
            $this->rollbackConnection();
            $this->_error( 'COMMIT', $fname ? "$fname::_commit" : "_commit" );
            return false;
        }
        return true;
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
     * ROLLBACK on the connection.
     */
    private function rollbackConnection()
    {
        $this->transactionFailed = false;
        if ( $this->db )
            @oci_rollback( $this->db );
    }

    /**
     * Protects a callback with a database transaction.
     *
     * The first argument is the callback and the second the name of the function (for query reporting); the
     * remaining arguments go to the callback. false or an eZMySQLBackendError from the callback rolls back and
     * returns false; any other value is committed and returned.
     *
     * A lock wait that ran out (ORA-30006), a deadlock (ORA-00060), a busy resource (ORA-00054), a serialization
     * failure (ORA-08177) or the duplicate key of two concurrent upserts (ORA-00001) rolls back and runs the callback
     * again, up to DBExecuteRetries times in all, with a short pause that grows with each try and never exceeds
     * MAX_RETRY_PAUSE. Inside an outer transaction there is nothing to retry: the failure goes up to the outermost
     * _protect(), which retries.
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
            $this->_begin( $fname );

            try
            {
                $result = call_user_func_array( $callback, $args );
            }
            catch ( Exception $e )
            {
                $this->_rollback( $fname );
                if ( !$nested && $this->retryableErrorSeen && $try < $maxTries )
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
     * Runs a statement, with $params as bound variables, and returns the statement, or false on failure (the
     * Oracle error number and message are kept, see lastErrorText()). Inside a transaction it runs without auto
     * commit, else it commits on success. A missing table throws eZDFSFileHandlerTableNotFoundException, as the
     * MySQLi backend does.
     *
     * @param string $query
     * @param string|bool $fname
     * @param bool $reportError
     * @param array $params
     * @return resource|false
     * @throws eZDFSFileHandlerTableNotFoundException
     */
    protected function _query( $query, $fname = false, $reportError = true, array $params = array() )
    {
        eZDebug::accumulatorStart( 'oracle_cluster_query', 'Oracle Cluster', 'DB queries' );
        $time = microtime( true );

        $this->lastError = null;
        $stmt = $this->db ? @oci_parse( $this->db, $query ) : false;
        $ok = false;
        if ( $stmt )
        {
            // bound by reference: the values must outlive the execution
            $values = array();
            foreach ( $params as $name => $value )
            {
                $values[$name] = $value === null ? null : (string)$value;
                oci_bind_by_name( $stmt, $name, $values[$name], -1 );
            }
            $ok = @oci_execute( $stmt, $this->transactionCount > 0 ? OCI_NO_AUTO_COMMIT : OCI_COMMIT_ON_SUCCESS );
        }
        if ( !$ok )
            $this->recordError( $stmt ? oci_error( $stmt ) : ( $this->db ? oci_error( $this->db ) : false ) );

        $time = microtime( true ) - $time;
        eZDebug::accumulatorStop( 'oracle_cluster_query' );

        if ( !$ok )
        {
            if ( $this->lastError !== null && (int)$this->lastError[0] === self::ORA_TABLE_MISSING )
                throw new eZDFSFileHandlerTableNotFoundException( $query, $this->lastError[1] );
            if ( $reportError )
                $this->_error( $query, $fname );
            $this->_report( $query, $fname, $time, false );
            return false;
        }

        $this->_report( $query, $fname, $time, oci_num_rows( $stmt ) );
        return $stmt;
    }

    /**
     * Keeps the Oracle error number and message of a failed statement, and marks the transaction it ran in as
     * failed (and as worth another try, for the errors isRetryableCode() names).
     *
     * @param array|false $error what oci_error() returned
     */
    protected function recordError( $error )
    {
        $code = is_array( $error ) && isset( $error['code'] ) ? (string)$error['code'] : '';
        $message = is_array( $error ) && isset( $error['message'] ) ? (string)$error['message'] : 'no connection to the cluster database';
        $this->lastError = array( $code, $message );
        if ( $this->transactionCount > 0 )
            $this->transactionFailed = true;
        if ( self::isRetryableCode( $code ) )
            $this->retryableErrorSeen = true;
    }

    /**
     * The Oracle error number of the last failed statement (30006 when a lock wait ran out), null when the last
     * statement succeeded.
     *
     * @return string|null
     */
    public function lastErrorCode()
    {
        return $this->lastError === null ? null : $this->lastError[0];
    }

    /**
     * "ORA-nnnnn: message" of the last failed statement, '' when it succeeded.
     *
     * @return string
     */
    protected function lastErrorText()
    {
        return $this->lastError === null ? '' : $this->lastError[1];
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
        eZDebug::writeNotice( "$query", "cluster::oracle::{$fname}[{$rowText}" . number_format( $timeTaken, 3 ) . " ms] query number per page:" . $this->numQueries++, $backgroundClass );
    }

    /**
     * Attempts to begin cache generation by inserting the row of "<path>.generating": its name_hash is the primary
     * key, so one process wins and the others get ORA-00001. When the row exists the path is being generated: 'ko'
     * with the seconds left, or, once that time is over, the generation is taken over (one UPDATE conditioned on
     * the mtime read, which only one process can win).
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
                "INSERT INTO $table (name, name_trunk, name_hash, scope, datatype, filesize, mtime, expired) " .
                "VALUES (:name, :trunk, :hash, NULL, NULL, 0, :mtime, 0)",
                $fname, false,
                array( ':name' => $generatingFilePath, ':trunk' => $generatingFilePath, ':hash' => $nameHash, ':mtime' => $mtime )
            );
            if ( $stmt )
                return array( 'result' => 'ok', 'mtime' => $mtime );

            if ( (int)$this->lastErrorCode() !== self::ORA_UNIQUE_CONSTRAINT )
            {
                // a lock the row is held by: 'ko', so the caller reads the stale cache
                if ( self::isRetryableCode( $this->lastErrorCode() ) )
                    return array( 'result' => 'ko', 'remaining' => 0 );
                eZDebug::writeError( "Unexpected error " . $this->lastErrorText() . " when trying to start cache generation on $filePath", __METHOD__ );
                return array( 'result' => 'error' );
            }

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
            if ( $stmt && oci_num_rows( $stmt ) == 1 )
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
     * actual file, and removes the .generating row, in one transaction with both rows locked (bounded waits).
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

        $this->_begin( $fname );

        $stmt = $this->_query( "SELECT " . self::COLUMNS . " FROM $table WHERE name_hash = :hash FOR UPDATE WAIT " . $this->lockTimeout, $fname, true,
                               array( ':hash' => $generatingHash ) );
        $generatingMetaData = $stmt ? oci_fetch_array( $stmt, OCI_ASSOC | OCI_RETURN_NULLS ) : false;
        if ( empty( $generatingMetaData ) )
        {
            eZDebug::writeError( "An error occured while ending cache generation,  $generatingFilePath", $fname );
            $this->_rollback( $fname );
            return false;
        }
        $generatingMetaData = self::normalizeRow( $generatingMetaData );

        $stmt = $this->_query( "SELECT name_hash FROM $table WHERE name_hash = :hash FOR UPDATE WAIT " . $this->lockTimeout, $fname, false,
                               array( ':hash' => md5( $filePath ) ) );
        if ( !$stmt )
        {
            $this->_rollback( $fname );
            return false;
        }

        if ( oci_fetch_array( $stmt, OCI_NUM ) === false )
        {
            // the file does not exist: the generating row becomes its row (a MERGE, in case a process inserted it
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
            if ( !$this->_query( "UPDATE $table SET mtime = :mtime, expired = 0, filesize = :filesize WHERE name_hash = :hash", $fname, true,
                                 array( ':mtime' => (int)$generatingMetaData['mtime'], ':filesize' => (int)$generatingMetaData['size'], ':hash' => md5( $filePath ) ) ) )
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

        // the update only succeeds if the mtime wasn't changed in between (Oracle counts a row updated with the same
        // values, so 1 row is the answer even within one second)
        $stmt = $this->_query( "UPDATE $table SET mtime = :mtime WHERE name_hash = :hash AND mtime = :previous", $fname, true,
                               array( ':mtime' => time(), ':hash' => $nameHash, ':previous' => (int)$generatingFileMtime ) );
        if ( !$stmt )
            return false;

        if ( oci_num_rows( $stmt ) == 1 )
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

        $this->_begin( $fname );
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
                $query .= " ORDER BY name_hash OFFSET " . (int)$limit[0] . " ROWS FETCH NEXT " . (int)$limit[1] . " ROWS ONLY";
            }
            $stmt = $this->_query( $query, __METHOD__, true, $params );
            if ( !$stmt )
                continue;

            while ( ( $row = oci_fetch_array( $stmt, OCI_NUM | OCI_RETURN_NULLS ) ) !== false )
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
     * Deletes a batch of cache files from the storage table.
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

        $table = $this->metaDataTable;
        $query = "DELETE FROM $table WHERE name LIKE :pattern ESCAPE '\\' AND ROWNUM <= " . (int)$limit;
        $stmt = $this->_query( $query, __METHOD__, true, array( ':pattern' => self::likePrefix( eZSys::cacheDirectory() . DIRECTORY_SEPARATOR ) ) );
        if ( !$stmt )
        {
            throw new RuntimeException( "Oracle error in $query\n" . $this->lastErrorText() );
        }

        return oci_num_rows( $stmt );
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
     * @var resource|object|null
     */
    public $db = null;

    /**
     * DB connection parameters
     * @var array|null
     */
    protected static $dbparams = null;

    /**
     * Seconds a statement waits for a row lock (DBLockTimeout)
     * @var int
     */
    protected $lockTimeout = self::DEFAULT_LOCK_TIMEOUT;

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
     * Whether a lock wait, deadlock or concurrent insert was seen in the running try of _protect()
     * @var bool
     */
    protected $retryableErrorSeen = false;

    /**
     * array( Oracle error number, message ) of the last failed statement, null after a successful one
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
