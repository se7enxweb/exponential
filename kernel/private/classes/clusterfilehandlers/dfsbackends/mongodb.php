<?php
/**
 * File containing the expDFSFileHandlerMongoDBBackend class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The DFS cluster metadata backend for MongoDB 5.0 and later: the database half of eZDFSFileHandler on a site that
 * runs on MongoDB, one node or several web servers.
 *
 * Written method for method against the kernel's SQLite and PostgreSQL backends (the PostgreSQL backend itself after
 * the ezpostgresqlcluster extension by eZ Systems and 7x's fork of it), with the same behavior: the parity checks of
 * tests/tests/kernel/classes/clusterfilehandlers/dfsbackends/expDFSBackendParityChecks.php pass on it. What differs
 * (doc/eng-us/features/6.0/mongodb-dfs-cluster.md lists it all):
 *
 *  - only the PHP extension mongodb (MongoDB\Driver), no library: one document per file in the collections
 *    ezdfsfile and ezdfsfile_cache (kernel/sql/mongodb/cluster_dfs_schema.json), _id is name_hash;
 *  - no multi-document transactions, so the backend runs on a single server as well as on a replica set: every
 *    change of one file is one atomic write of one document. The unique _id is the lock of a cache generation
 *    (an insert that only one process can win), and a takeover or a timeout check is one update conditioned on
 *    the mtime it read (compare and set), as the SQL backends do it;
 *  - the writes of several documents (a rename, the end of a cache generation, a purge) are ordered so that a
 *    process stopped half way leaves a state the next one recovers from: the file is written on the DFS before
 *    its document, the document of a new name before the old one is removed;
 *  - LIKE patterns and shell wildcards become anchored regular expressions: case sensitive, as MySQL's binary LIKE
 *    on name is, and a literal prefix uses the name index;
 *  - a network error, a stepped-down primary or a write the server labels retryable is tried again, up to
 *    DBExecuteRetries tries, with a short bounded pause; the server selection waits DBLockTimeout seconds at most;
 *  - 64 bit integers for sizes and times, no 2038 limit.
 *
 * Usable on PHP 7.4 with ext-mongodb 1.11 or later, clean on PHP 8.0 to 8.5.
 *
 * Settings: file.ini [eZDFSClusteringSettings] DBBackend=expDFSFileHandlerMongoDBBackend, DBHost (a host name, or a
 * whole mongodb:// or mongodb+srv:// connection string for a replica set), DBPort (default 27017), DBSocket (a Unix
 * socket path, instead of host and port), DBName (the database; users are authenticated against it, as
 * expMongoDB does), DBUser, DBPassword, DBConnectRetries, DBExecuteRetries, optional DBLockTimeout.
 *
 * @package kernel
 */
class expDFSFileHandlerMongoDBBackend implements eZClusterEventNotifier, expDFSFileHandlerDBBackendInterface
{
    /**
     * Wait for n microseconds until retry if copy fails, to avoid DFS overload.
     */
    const TIME_UNTIL_RETRY = 100;

    /** Seconds the selection of a server may take when DBLockTimeout is not set */
    const DEFAULT_LOCK_TIMEOUT = 10;

    /** Longest pause between two tries of a write, in microseconds */
    const MAX_RETRY_PAUSE = 200000;

    /** Documents removed per command by _purgeByLike() and deleteCacheFiles() */
    const DELETE_CHUNK = 1000;

    /** Longest wildcard pattern _deleteByWildcard() takes, in characters */
    const MAX_WILDCARD_LENGTH = 4096;

    /** The server's error code of a duplicate key */
    const DUPLICATE_KEY = 11000;

    /** The schema file, relative to the installation root */
    const SCHEMA_FILE = 'kernel/sql/mongodb/cluster_dfs_schema.json';

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
     * The anchored regular expression of a LIKE pattern the callers built (with % wildcards): % is any text, an
     * underscore is a literal (as in the MySQLi backend), a backslash makes the next character a literal; every
     * other character is a literal.
     *
     * @param string $like
     * @return string
     */
    public static function likeToRegex( $like )
    {
        $like = (string)$like;
        $regex = '';
        $literal = '';
        $length = strlen( $like );
        for ( $i = 0; $i < $length; ++$i )
        {
            $char = $like[$i];
            if ( $char === '\\' && $i + 1 < $length )
            {
                $literal .= $like[++$i];
            }
            elseif ( $char === '%' )
            {
                $regex .= self::quote( $literal ) . '.*';
                $literal = '';
            }
            else
            {
                $literal .= $char;
            }
        }
        $regex .= self::quote( $literal );
        // a trailing .* adds nothing to an unanchored end: left out, the prefix search stays a plain index range
        if ( substr( $regex, -2 ) === '.*' )
            return '^' . substr( $regex, 0, -2 );
        return '^' . $regex . '$';
    }

    /**
     * The regular expression of every name that starts with $prefix.
     *
     * @param string $prefix
     * @return string
     */
    public static function prefixRegex( $prefix )
    {
        return '^' . self::quote( (string)$prefix );
    }

    /**
     * The anchored regular expression of a shell wildcard: ? one character, * any text, {a,b} one of the
     * alternatives (nested braces too); every other character is a literal. A comma outside braces stays a comma;
     * an unclosed brace is closed at the end.
     *
     * @param string $wildcard
     * @return string
     * @throws InvalidArgumentException for a wildcard longer than MAX_WILDCARD_LENGTH
     */
    public static function wildcardToRegex( $wildcard )
    {
        $wildcard = (string)$wildcard;
        if ( strlen( $wildcard ) > self::MAX_WILDCARD_LENGTH )
            throw new InvalidArgumentException( "The wildcard is longer than " . self::MAX_WILDCARD_LENGTH . " characters" );
        $pos = 0;
        return '^' . self::wildcardPart( $wildcard, $pos, false ) . '$';
    }

    /**
     * The regular expression of $wildcard from $pos up to the end, or up to the comma or closing brace of the
     * group it is in ($inGroup). Moves $pos past what it read.
     *
     * @param string $wildcard
     * @param int $pos
     * @param bool $inGroup
     * @return string
     */
    private static function wildcardPart( $wildcard, &$pos, $inGroup )
    {
        $regex = '';
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
                    $alternatives[] = self::wildcardPart( $wildcard, $pos, true );
                    if ( $pos < $length && $wildcard[$pos] === ',' )
                    {
                        ++$pos;
                        continue;
                    }
                    if ( $pos < $length && $wildcard[$pos] === '}' )
                        ++$pos;
                    break;
                }
                $regex .= '(?:' . implode( '|', $alternatives ) . ')';
                continue;
            }
            if ( $char === '?' )
                $regex .= '.';
            elseif ( $char === '*' )
                $regex .= '.*';
            else
                $regex .= self::quote( $char );
        }
        return $regex;
    }

    /**
     * Text as a regular expression literal.
     *
     * @param string $text
     * @return string
     */
    private static function quote( $text )
    {
        return preg_quote( (string)$text );
    }

    /**
     * The connection string and its options for the settings (pure): a whole mongodb:// or mongodb+srv:// string
     * in DBHost is taken as it is (for a replica set), else DBSocket or DBHost and DBPort make one. The account is
     * never written into the string: it goes in the options (username, password, and authSource the database, as
     * expMongoDB authenticates), so no character of a password can split the string.
     *
     * @param array $params host, port, socket, user, password, dbname, lock_timeout
     * @return array( uri, options )
     */
    public static function connectionUri( array $params )
    {
        $params += array( 'host' => '', 'port' => '', 'socket' => '', 'user' => '', 'password' => '', 'dbname' => '', 'lock_timeout' => self::DEFAULT_LOCK_TIMEOUT );
        $host = trim( (string)$params['host'] );
        $socket = trim( (string)$params['socket'] );
        if ( preg_match( '#^mongodb(\+srv)?://#i', $host ) )
        {
            $uri = $host;
        }
        elseif ( $socket !== '' && $socket !== 'disabled' && $socket[0] === '/' )
        {
            $uri = 'mongodb://' . rawurlencode( $socket );
        }
        else
        {
            $port = (int)$params['port'] > 0 ? (int)$params['port'] : 27017;
            $uri = 'mongodb://' . ( $host !== '' ? $host : 'localhost' ) . ':' . $port;
        }

        $timeout = max( 1, (int)$params['lock_timeout'] ) * 1000;
        $options = array( 'serverSelectionTimeoutMS' => $timeout, 'connectTimeoutMS' => $timeout, 'appname' => 'Exponential DFS cluster' );
        if ( (string)$params['user'] !== '' && strpos( $uri, '@' ) === false )
        {
            $options['username'] = (string)$params['user'];
            if ( (string)$params['password'] !== '' )
                $options['password'] = (string)$params['password'];
            if ( (string)$params['dbname'] !== '' && stripos( $uri, 'authSource=' ) === false )
                $options['authSource'] = (string)$params['dbname'];
        }
        return array( $uri, $options );
    }

    /**
     * The collections and indexes of the schema file (pure).
     *
     * @param string $json the contents of kernel/sql/mongodb/cluster_dfs_schema.json
     * @return array collection => list of array( name, key )
     * @throws RuntimeException when the file is not a schema
     */
    public static function schemaFromJson( $json )
    {
        $schema = json_decode( (string)$json, true );
        if ( !is_array( $schema ) || !isset( $schema['collections'] ) || !is_array( $schema['collections'] ) )
            throw new RuntimeException( 'The MongoDB cluster schema is not valid JSON with "collections"' );
        $out = array();
        foreach ( $schema['collections'] as $collection => $definition )
        {
            if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,63}$/', (string)$collection ) )
                throw new RuntimeException( "The MongoDB cluster schema names a collection '$collection' that is not a plain name" );
            $out[$collection] = array();
            foreach ( isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array() as $index )
            {
                if ( !isset( $index['name'], $index['key'] ) || !is_array( $index['key'] ) || !$index['key'] )
                    throw new RuntimeException( "An index of '$collection' in the MongoDB cluster schema has no name or key" );
                $out[$collection][] = array( 'name' => (string)$index['name'], 'key' => $index['key'] );
            }
        }
        return $out;
    }

    /**
     * Creates the collections and indexes of the schema on a database (what exp:cluster:enable runs). Existing
     * collections and indexes stay as they are.
     *
     * @param MongoDB\Driver\Manager $manager
     * @param string $database
     * @param string|null $schemaFile defaults to SCHEMA_FILE of the installation
     * @return string[] what was created
     * @throws RuntimeException on a server error
     */
    public static function createSchema( $manager, $database, $schemaFile = null )
    {
        if ( $schemaFile === null )
            $schemaFile = ( class_exists( 'eZSys' ) ? rtrim( (string)eZSys::rootDir(), '/' ) . '/' : '' ) . self::SCHEMA_FILE;
        $schema = self::schemaFromJson( @file_get_contents( $schemaFile ) );
        $created = array();
        try
        {
            $cursor = $manager->executeCommand( $database, new MongoDB\Driver\Command( array( 'listCollections' => 1, 'nameOnly' => true ) ) );
            $existing = array();
            foreach ( $cursor as $row )
                $existing[] = $row->name;
            foreach ( $schema as $collection => $indexes )
            {
                if ( !in_array( $collection, $existing, true ) )
                {
                    $manager->executeCommand( $database, new MongoDB\Driver\Command( array( 'create' => $collection ) ) );
                    $created[] = $collection;
                }
                if ( $indexes )
                {
                    $manager->executeCommand( $database, new MongoDB\Driver\Command( array( 'createIndexes' => $collection, 'indexes' => $indexes ) ) );
                    foreach ( $indexes as $index )
                        $created[] = $collection . '.' . $index['name'];
                }
            }
        }
        catch ( MongoDB\Driver\Exception\Exception $e )
        {
            throw new RuntimeException( 'Creating the MongoDB cluster collections failed: ' . $e->getMessage(), 0, $e );
        }
        return $created;
    }

    /**
     * Returns the database table name (collection) to use for the specified file.
     *
     * For files detected as cache files the cache collection is returned, if not the generic one.
     *
     * @param string $filePath
     * @return string The collection name
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
     * Connects to the database: the server answers a ping, with the account if one is set.
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
            foreach ( array( 'host' => 'DBHost', 'port' => 'DBPort', 'socket' => 'DBSocket', 'dbname' => 'DBName', 'user' => 'DBUser', 'password' => 'DBPassword' ) as $key => $name )
                self::$dbparams[$key] = $fileINI->hasVariable( 'eZDFSClusteringSettings', $name ) ? (string)$fileINI->variable( 'eZDFSClusteringSettings', $name ) : '';

            self::$dbparams['max_connect_tries'] = $fileINI->variable( 'eZDFSClusteringSettings', 'DBConnectRetries' );
            self::$dbparams['max_execute_tries'] = $fileINI->variable( 'eZDFSClusteringSettings', 'DBExecuteRetries' );
            self::$dbparams['lock_timeout'] = $fileINI->hasVariable( 'eZDFSClusteringSettings', 'DBLockTimeout' )
                ? $fileINI->variable( 'eZDFSClusteringSettings', 'DBLockTimeout' )
                : self::DEFAULT_LOCK_TIMEOUT;

            self::$dbparams['sql_output'] = $siteINI->variable( "DatabaseSettings", "SQLOutput" ) == "enabled";

            self::$dbparams['cache_generation_timeout'] = $siteINI->variable( "ContentSettings", "CacheGenerationTimeout" );
        }

        $dbName = (string)self::$dbparams['dbname'];
        if ( !extension_loaded( 'mongodb' ) )
            throw new eZClusterHandlerDBNoConnectionException( (string)self::$dbparams['host'], (string)self::$dbparams['user'], '',
                "The PHP extension mongodb is not loaded" );
        if ( !preg_match( '/^[^\/\\\\. "$\0]{1,63}$/', $dbName ) )
            throw new eZClusterHandlerDBNoConnectionException( (string)self::$dbparams['host'], (string)self::$dbparams['user'], '',
                "[eZDFSClusteringSettings] DBName names no MongoDB database" );

        list( $uri, $options ) = self::connectionUri( self::$dbparams );
        $maxTries = max( 1, (int)self::$dbparams['max_connect_tries'] );
        $error = '';
        $this->db = null;
        $this->dbName = $dbName;
        eZDebug::accumulatorStart( 'mongodb_cluster_connect', 'MongoDB Cluster', 'Cluster database connection' );
        for ( $tries = 0; $tries < $maxTries; ++$tries )
        {
            try
            {
                $manager = new MongoDB\Driver\Manager( $uri, $options );
                $manager->executeCommand( $dbName, new MongoDB\Driver\Command( array( 'ping' => 1 ) ) );
                $this->db = $manager;
                break;
            }
            catch ( MongoDB\Driver\Exception\Exception $e )
            {
                $error = $e->getMessage();
            }
        }
        eZDebug::accumulatorStop( 'mongodb_cluster_connect' );
        if ( !$this->db )
        {
            throw new eZClusterHandlerDBNoConnectionException( (string)self::$dbparams['host'], (string)self::$dbparams['user'], '', $error );
        }

        $this->lastError = null;

        // DFS setup
        $this->dfsbackend = eZDFSFileHandlerBackendFactory::build();
    }

    /**
     * Disconnects the handler from the database
     */
    public function _disconnect()
    {
        $this->db = null;
    }

    // ------------------------------------------------------------------------------------------ the low level

    /**
     * Whether a failure is worth another try: no connection, a network error, a primary that stepped down, or an
     * error the server labels retryable.
     *
     * @param Throwable|Exception $e
     * @return bool
     */
    public static function isRetryable( $e )
    {
        if ( $e instanceof MongoDB\Driver\Exception\ConnectionException || $e instanceof MongoDB\Driver\Exception\ConnectionTimeoutException )
            return true;
        if ( $e instanceof MongoDB\Driver\Exception\RuntimeException && method_exists( $e, 'hasErrorLabel' ) )
        {
            if ( $e->hasErrorLabel( 'RetryableWriteError' ) || $e->hasErrorLabel( 'TransientTransactionError' ) )
                return true;
        }
        // NotWritablePrimary, NotPrimaryNoSecondaryOk, PrimarySteppedDown, InterruptedDueToReplStateChange, ShutdownInProgress
        return in_array( (int)$e->getCode(), array( 10107, 13435, 189, 11602, 91, 6, 7, 89, 9001 ), true );
    }

    /**
     * Runs one operation on the server, tried again up to DBExecuteRetries times when it failed in a way worth
     * another try. Keeps the code and message of a failure (lastErrorCode()).
     *
     * @param callable $operation receives the manager
     * @param string $fname
     * @param bool $reportError
     * @return mixed what the operation returned, false on failure
     */
    protected function run( $operation, $fname, $reportError = true )
    {
        $maxTries = max( 1, (int)self::$dbparams['max_execute_tries'] );
        eZDebug::accumulatorStart( 'mongodb_cluster_query', 'MongoDB Cluster', 'DB queries' );
        $time = microtime( true );
        $this->lastError = null;
        $result = false;
        if ( !$this->db )
        {
            eZDebug::accumulatorStop( 'mongodb_cluster_query' );
            $this->lastError = array( '', 'Not connected to the cluster database' );
            if ( $reportError )
                $this->_error( $fname, $fname );
            return false;
        }
        for ( $try = 1; ; ++$try )
        {
            try
            {
                $result = call_user_func( $operation, $this->db );
                $this->lastError = null;
                break;
            }
            catch ( MongoDB\Driver\Exception\Exception $e )
            {
                $this->recordError( $e );
                if ( self::isRetryable( $e ) && $try < $maxTries )
                {
                    $this->retryPause( $try );
                    continue;
                }
                $result = false;
                break;
            }
        }
        eZDebug::accumulatorStop( 'mongodb_cluster_query' );
        if ( $this->lastError !== null && $reportError )
            $this->_error( $fname, $fname );
        $this->_report( $fname, $fname, microtime( true ) - $time );
        return $result;
    }

    /**
     * The pause before the next try: about 2^try milliseconds plus jitter, at most MAX_RETRY_PAUSE microseconds.
     *
     * @param int $try
     */
    protected function retryPause( $try )
    {
        usleep( min( self::MAX_RETRY_PAUSE, 1000 * ( 1 << min( $try, 8 ) ) + mt_rand( 0, 1000 ) ) );
    }

    /**
     * The code of a server error: a write error's code (a duplicate key is 11000) before the command's.
     *
     * @param Throwable|Exception $e
     * @return string
     */
    public static function errorCodeOf( $e )
    {
        if ( $e instanceof MongoDB\Driver\Exception\BulkWriteException )
        {
            $result = $e->getWriteResult();
            foreach ( $result->getWriteErrors() as $writeError )
                return (string)$writeError->getCode();
            if ( $result->getWriteConcernError() )
                return (string)$result->getWriteConcernError()->getCode();
        }
        return (string)$e->getCode();
    }

    /**
     * @param Throwable|Exception $e
     */
    protected function recordError( $e )
    {
        $this->lastError = array( self::errorCodeOf( $e ), $e->getMessage() );
    }

    /**
     * The error code of the last failed operation (11000 for a duplicate key), null when it succeeded.
     *
     * @return string|null
     */
    public function lastErrorCode()
    {
        return $this->lastError === null ? null : $this->lastError[0];
    }

    /**
     * "code: message" of the last failed operation, '' when it succeeded.
     *
     * @return string
     */
    protected function lastErrorText()
    {
        return $this->lastError === null ? '' : $this->lastError[0] . ': ' . $this->lastError[1];
    }

    /**
     * @param string $collection
     * @return string the namespace database.collection
     */
    protected function ns( $collection )
    {
        return $this->dbName . '.' . $collection;
    }

    /**
     * The documents of a query, as arrays.
     *
     * @param string $collection
     * @param array $filter
     * @param array $options
     * @param string $fname
     * @return array[]|false
     */
    protected function find( $collection, array $filter, array $options, $fname )
    {
        $ns = $this->ns( $collection );
        return $this->run( function ( $manager ) use ( $ns, $filter, $options )
        {
            $cursor = $manager->executeQuery( $ns, new MongoDB\Driver\Query( $filter, $options ) );
            $cursor->setTypeMap( array( 'root' => 'array', 'document' => 'array', 'array' => 'array' ) );
            return $cursor->toArray();
        }, $fname );
    }

    /**
     * One document, false when there is none or on a failure (lastErrorCode() tells which).
     *
     * @param string $collection
     * @param string $hash
     * @param string $fname
     * @return array|false
     */
    protected function findOne( $collection, $hash, $fname )
    {
        $rows = $this->find( $collection, array( '_id' => (string)$hash ), array( 'limit' => 1 ), $fname );
        return is_array( $rows ) && $rows ? $rows[0] : false;
    }

    /**
     * Runs a bulk write of one kind built by $build.
     *
     * @param string $collection
     * @param callable $build receives the BulkWrite
     * @param string $fname
     * @param bool $reportError
     * @return MongoDB\Driver\WriteResult|false
     */
    protected function write( $collection, $build, $fname, $reportError = true )
    {
        $ns = $this->ns( $collection );
        return $this->run( function ( $manager ) use ( $ns, $build )
        {
            $bulk = new MongoDB\Driver\BulkWrite( array( 'ordered' => true ) );
            call_user_func( $build, $bulk );
            return $manager->executeBulkWrite( $ns, $bulk );
        }, $fname, $reportError );
    }

    /**
     * Inserts a file's document or, when its name_hash exists, sets $updateColumns of it (the rest only on an
     * insert), in one atomic write.
     *
     * @param string $collection
     * @param array $row name, name_trunk, name_hash, datatype, scope, size, mtime, expired
     * @param array $updateColumns
     * @param string $fname
     * @return bool
     */
    protected function upsert( $collection, array $row, array $updateColumns, $fname )
    {
        $set = array();
        $setOnInsert = array();
        foreach ( $row as $column => $value )
        {
            if ( in_array( $column, $updateColumns, true ) )
                $set[$column] = $value;
            else
                $setOnInsert[$column] = $value;
        }
        $update = array();
        if ( $set )
            $update['$set'] = $set;
        if ( $setOnInsert )
            $update['$setOnInsert'] = $setOnInsert;
        $hash = (string)$row['name_hash'];
        return $this->write( $collection, function ( $bulk ) use ( $hash, $update )
        {
            $bulk->update( array( '_id' => $hash ), $update, array( 'upsert' => true ) );
        }, $fname ) !== false;
    }

    /**
     * The update that marks files deleted: mtime becomes -ABS(mtime), expired 1 (an update pipeline, one write).
     *
     * @return array
     */
    protected static function expireUpdate()
    {
        return array( array( '$set' => array( 'mtime' => array( '$multiply' => array( -1, array( '$abs' => '$mtime' ) ) ), 'expired' => 1 ) ) );
    }

    /**
     * Marks every document matching $filter deleted.
     *
     * @param string $collection
     * @param array $filter
     * @param string $fname
     * @return bool
     */
    protected function expire( $collection, array $filter, $fname )
    {
        $update = self::expireUpdate();
        return $this->write( $collection, function ( $bulk ) use ( $filter, $update )
        {
            $bulk->update( $filter, $update, array( 'multi' => true ) );
        }, $fname ) !== false;
    }

    /**
     * A document as the SQL backends return a row: the columns in their order, every value a string.
     *
     * @param array $document
     * @return array
     */
    protected static function row( array $document )
    {
        $row = array();
        foreach ( array( 'name', 'name_trunk', 'name_hash', 'datatype', 'scope', 'size', 'mtime', 'expired' ) as $column )
            $row[$column] = isset( $document[$column] ) ? (string)$document[$column] : ( in_array( $column, array( 'size', 'mtime', 'expired' ), true ) ? '0' : '' );
        return $row;
    }

    /**
     * A new file's document.
     *
     * @return array
     */
    protected static function document( $filePath, $datatype, $scope, $size, $mtime )
    {
        return array( 'name' => (string)$filePath,
                      'name_trunk' => self::nameTrunk( $filePath, $scope ),
                      'name_hash' => md5( $filePath ),
                      'datatype' => (string)$datatype,
                      'scope' => (string)$scope,
                      'size' => (int)$size,
                      'mtime' => (int)$mtime,
                      'expired' => ( (int)$mtime < 0 ) ? 1 : 0 );
    }

    // ------------------------------------------------------------------------------------------ the backend

    /**
     * Creates a copy of a file in DB+DFS: the file on the DFS first, then its document.
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

        $result = true;
        $this->_delete( $dstFilePath, true, $fname );
        if ( !$this->dfsbackend->copyFromDFSToDFS( $srcFilePath, $dstFilePath ) )
        {
            eZDebug::writeError( "Failed to copy DFS://$srcFilePath to DFS://$dstFilePath", __METHOD__ );
            $result = false;
        }
        elseif ( !$this->upsert( $this->dbTable( $dstFilePath ),
                                 self::document( $dstFilePath, $metaData['datatype'], $metaData['scope'], $metaData['size'], $metaData['mtime'] ),
                                 array( 'datatype', 'name_trunk', 'scope', 'size', 'mtime', 'expired' ), $fname ) )
        {
            eZDebug::writeError( "Failed to insert file metadata on copying $srcFilePath", __METHOD__ );
            $result = false;
        }

        $this->eventHandler->notify( 'cluster/deleteFile', array( $dstFilePath ) );

        return $result;
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
        $filter = array( '_id' => md5( $filePath ) );
        if ( $expiry !== false )
            $filter['mtime'] = array( '$lt' => (int)$expiry );
        elseif ( $onlyExpired )
            $filter['expired'] = 1;
        $result = $this->write( $this->dbTable( $filePath ), function ( $bulk ) use ( $filter )
        {
            $bulk->delete( $filter, array( 'limit' => 1 ) );
        }, $fname );
        if ( $result === false )
        {
            return $this->_fail( "Purging file metadata for $filePath failed" );
        }
        if ( $result->getDeletedCount() == 1 )
        {
            $this->dfsbackend->delete( $filePath );
        }

        $this->eventHandler->notify( 'cluster/deleteFile', array( $filePath ) );

        return true;
    }

    /**
     * Purges meta-data and file-data for files matching a pattern using a SQL LIKE syntax: the documents found are
     * removed by _id (a document that changed in between is removed with them, as on MySQL), then their files.
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
        $filter = array( 'name' => new MongoDB\BSON\Regex( self::likeToRegex( $like ), '' ) );
        if ( $expiry !== false )
            $filter['mtime'] = array( '$lt' => (int)$expiry );
        elseif ( $onlyExpired )
            $filter['expired'] = 1;
        $options = array( 'projection' => array( 'name' => 1 ) );
        if ( $limit )
            $options['limit'] = (int)$limit;

        $rows = $this->find( $table, $filter, $options, $fname );
        if ( $rows === false )
            return $this->_fail( "Selecting file metadata by like statement $like failed" );
        if ( count( $rows ) == 0 )
            return 0;

        $files = array();
        $hashes = array();
        foreach ( $rows as $row )
        {
            $files[] = $row['name'];
            $hashes[] = $row['_id'];
        }

        $deletedDBFiles = 0;
        foreach ( array_chunk( $hashes, self::DELETE_CHUNK ) as $chunk )
        {
            $result = $this->write( $table, function ( $bulk ) use ( $chunk )
            {
                $bulk->delete( array( '_id' => array( '$in' => $chunk ) ), array( 'limit' => 0 ) );
            }, $fname );
            if ( $result === false )
                return $this->_fail( "Purging file metadata by like statement $like failed" );
            $deletedDBFiles += $result->getDeletedCount();
        }

        $this->dfsbackend->delete( $files );

        return $deletedDBFiles;
    }

    /**
     * Deletes a file from DB (marks it expired); _purge removes it from disk.
     *
     * @param string $filePath Path of the file to delete
     * @param bool $insideOfTransaction Whether or not a transaction is already started (no meaning here: one write)
     * @param bool|string $fname Optional caller name for debugging
     * @return bool
     */
    public function _delete( $filePath, $insideOfTransaction = false, $fname = false )
    {
        if ( $fname )
            $fname .= "::_delete($filePath)";
        else
            $fname = "_delete($filePath)";
        $res = $this->expire( $this->dbTable( $filePath ), array( '_id' => md5( $filePath ) ), $fname );
        if ( !$res )
            eZDebug::writeError( "Deleting file $filePath failed: " . $this->lastErrorText(), __METHOD__ );

        $this->eventHandler->notify( 'cluster/deleteFile', array( $filePath ) );

        return $res;
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
        $return = $this->expire( $this->dbTable( $like ), array( 'name' => new MongoDB\BSON\Regex( self::likeToRegex( $like ), '' ) ), $fname );
        if ( !$return )
            eZDebug::writeError( "Failed to delete files by like: '$like'", __METHOD__ );

        if ( $return )
            $this->eventHandler->notify( 'cluster/deleteByLike', array( $like ) );

        return $return;
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
        try
        {
            $regex = self::wildcardToRegex( $wildcard );
        }
        catch ( InvalidArgumentException $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return false;
        }
        $return = $this->expire( $this->dbTable( $wildcard ), array( 'name' => new MongoDB\BSON\Regex( $regex, '' ) ), $fname );
        if ( !$return )
            eZDebug::writeError( "Failed to delete files by wildcard: '$wildcard'", __METHOD__ );
        return $return;
    }

    /**
     * @param array $dirList
     * @param string $commonPath
     * @param string $commonSuffix
     * @param string|bool $fname
     * @return bool
     */
    public function _deleteByDirList( $dirList, $commonPath, $commonSuffix, $fname = false )
    {
        if ( $fname )
            $fname .= "::_deleteByDirList(" . join( ", ", $dirList ) . ", $commonPath, $commonSuffix)";
        else
            $fname = "_deleteByDirList(" . join( ", ", $dirList ) . ", $commonPath, $commonSuffix)";

        $table = $this->dbTable( $commonPath );
        foreach ( $dirList as $dirItem )
        {
            if ( strstr( $commonPath, '/cache/content' ) !== false or strstr( $commonPath, '/cache/template-block' ) !== false )
            {
                $event = 'cluster/deleteByNametrunk';
                $nametrunk = "$commonPath/$dirItem/$commonSuffix";
                $eventParameters = array( $nametrunk );
                $filter = array( 'name_trunk' => $nametrunk );
            }
            else
            {
                $event = 'cluster/deleteByDirList';
                $eventParameters = array( $commonPath, $dirItem, $commonSuffix );
                $filter = array( 'name' => new MongoDB\BSON\Regex( self::prefixRegex( "$commonPath/$dirItem/$commonSuffix" ), '' ) );
            }
            if ( !$this->expire( $table, $filter, $fname ) )
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
            $document = $this->findOne( $this->dbTable( $filePath ), md5( $filePath ), $fname );
            if ( $document === false && $this->lastError !== null )
                throw new Exception( "dfs/mongodb DB error: " . $this->lastErrorText() . "\nFailed to check file '$filePath' existance" );
            $row = $document === false ? false : array( $document['name'], (string)$document['mtime'] );
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
        $loopCount = 0;
        $localFileSize = 0;

        do
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
                usleep( self::TIME_UNTIL_RETRY );
                ++$loopCount;
                continue;
            }

            if ( $uniqueName !== true )
            {
                if ( !eZFile::rename( $tmpFilePath, $filePath, false, eZFile::CLEAN_ON_FAILURE | eZFile::APPEND_DEBUG_ON_FAILURE ) )
                {
                    usleep( self::TIME_UNTIL_RETRY );
                    ++$loopCount;
                    continue;
                }
            }
            $filePath = ( $uniqueName ) ? $tmpFilePath : $filePath;

            clearstatcache( true, $filePath );
            $localFileSize = filesize( $filePath );
            if ( $dfsFileSize == $localFileSize )
            {
                return $filePath;
            }
            else if ( file_exists( $tmpFilePath ) )
            {
                unlink( $tmpFilePath );
            }

            usleep( self::TIME_UNTIL_RETRY );
            ++$loopCount;
        }
        while ( $dfsFileSize > $localFileSize && $loopCount < $this->maxCopyTries );

        eZDebug::writeError( "Size ({$localFileSize}) of written data for file '{$filePath}' does not match expected size {$metaData['size']}", __METHOD__ );
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
     * @throws Exception on a database error
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
        $document = $this->findOne( $this->dbTable( $filePath ), md5( $filePath ), $fname );
        if ( $document === false )
        {
            if ( $this->lastError !== null )
                throw new Exception( "dfs/mongodb DB error: " . $this->lastErrorText() . "\nFailed to retrieve file metadata: $filePath" );
            return false;
        }
        $metadata = self::row( $document );
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
     * Renames $srcFilePath to $dstFilePath: a file in the way is purged, the file is copied on the DFS, the new
     * name's document written, and only then the old document and file removed. A process stopped in between
     * leaves both names (the old one still valid), never none.
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

        if ( $this->_exists( $dstFilePath, false, false ) )
            $this->_purge( $dstFilePath, false );

        if ( !$this->dfsbackend->copyFromDFSToDFS( $srcFilePath, $dstFilePath ) )
        {
            eZDebug::writeError( "Failed to copy DFS://$srcFilePath to DFS://$dstFilePath", __METHOD__ );
            return false;
        }

        $document = self::document( $dstFilePath, $metaData['datatype'], $metaData['scope'], $metaData['size'], $metaData['mtime'] );
        $document['expired'] = (int)$metaData['expired'];
        if ( !$this->upsert( $this->dbTable( $dstFilePath ), $document, array( 'name', 'name_trunk', 'datatype', 'scope', 'size', 'mtime', 'expired' ), $fname ) )
        {
            eZDebug::writeError( "Failed making new file entry '$dstFilePath'", __METHOD__ );
            $this->dfsbackend->delete( $dstFilePath );
            return false;
        }

        $srcHash = md5( $srcFilePath );
        if ( $this->write( $this->dbTable( $srcFilePath ), function ( $bulk ) use ( $srcHash )
             {
                 $bulk->delete( array( '_id' => $srcHash ), array( 'limit' => 1 ) );
             }, $fname ) === false )
        {
            eZDebug::writeError( "Failed removing old file '$srcFilePath'", __METHOD__ );
            return false;
        }

        $this->dfsbackend->delete( $srcFilePath );

        $this->eventHandler->notify( 'cluster/deleteFile', array( $srcFilePath ) );

        return true;
    }

    /**
     * Stores $filePath to cluster: the file on the DFS first, then its document.
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

        $return = $this->_storeInner( $filePath, $datatype, $scope, $fname );

        $this->eventHandler->notify( 'cluster/deleteFile', array( $filePath ) );

        return $return;
    }

    /**
     * The actual file store operation
     *
     * @param string $filePath
     * @param string $datatype
     * @param string $scope
     * @param string $fname
     * @return bool
     */
    public function _storeInner( $filePath, $datatype, $scope, $fname )
    {
        clearstatcache( true, $filePath );
        $fileMTime = filemtime( $filePath );
        // This check intentionally match 'false' value as well
        if ( $fileMTime == 0 )
            $fileMTime = -1;
        $contentLength = filesize( $filePath );

        if ( !$this->dfsbackend->copyToDFS( $filePath ) )
        {
            eZDebug::writeError( "Failed to copy FS://$filePath to DFS://$filePath", __METHOD__ );
            return false;
        }

        if ( !$this->upsert( $this->dbTable( $filePath ),
                             self::document( $filePath, $datatype, $scope, $contentLength, $fileMTime ),
                             array( 'datatype', 'scope', 'size', 'mtime', 'expired' ), $fname ) )
        {
            return false;
        }

        return true;
    }

    /**
     * Stores $contents as the contents of $filePath to the cluster: the file on the DFS first, then its document.
     *
     * @param string $filePath
     * @param string $contents
     * @param string $scope
     * @param string $datatype
     * @param bool|int $mtime
     * @param bool|string $fname
     * @return bool
     */
    public function _storeContents( $filePath, $contents, $scope, $datatype, $mtime = false, $fname = false )
    {
        if ( $fname )
            $fname .= "::_storeContents($filePath, ..., $scope, $datatype)";
        else
            $fname = "_storeContents($filePath, ..., $scope, $datatype)";

        return $this->_storeContentsInner( $filePath, $contents, $scope, $datatype, $mtime, $fname );
    }

    /**
     * @param string $filePath
     * @param string $contents
     * @param string $scope
     * @param string $datatype
     * @param int|bool $curTime
     * @param string|bool $fname
     * @return bool
     */
    public function _storeContentsInner( $filePath, $contents, $scope, $datatype, $curTime, $fname )
    {
        if ( $curTime === false )
            $curTime = time();

        if ( !$this->dfsbackend->createFileOnDFS( $filePath, $contents ) )
        {
            eZDebug::writeError( "Failed to open DFS://$filePath for writing", __METHOD__ );
            return false;
        }

        if ( !$this->upsert( $this->dbTable( $filePath ),
                             self::document( $filePath, $datatype, $scope, strlen( $contents ), $curTime ),
                             array( 'datatype', 'name_trunk', 'scope', 'size', 'mtime', 'expired' ), $fname ) )
        {
            return false;
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
            $filter = array();
            if ( is_array( $scopes ) && count( $scopes ) > 0 )
                $filter['scope'] = array( ( $excludeScopes ? '$nin' : '$in' ) => array_values( array_map( 'strval', $scopes ) ) );
            if ( $path != false )
                $filter['name'] = new MongoDB\BSON\Regex( self::prefixRegex( $path ), '' );
            $options = array( 'projection' => array( 'name' => 1 ) );
            if ( $limit && array_sum( $limit ) )
            {
                $options['sort'] = array( '_id' => 1 );
                $options['skip'] = (int)$limit[0];
                $options['limit'] = (int)$limit[1];
            }
            $rows = $this->find( $table, $filter, $options, "_getFileList( array( " . implode( ', ', is_array( $scopes ) ? $scopes : array() ) . " ), $excludeScopes )" );
            if ( $rows === false )
            {
                eZDebug::writeError( 'Unable to get file list', __METHOD__ );
                throw new Exception( "dfs/mongodb DB error: " . $this->lastErrorText() );
            }

            foreach ( $rows as $row )
            {
                $filePathList[] = $row['name'];
            }
        }

        return $filePathList;
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
     * Prints the last error to the debug system.
     *
     * @param string $operation
     * @param string|bool $fname
     * @param string|bool $error
     */
    protected function _error( $operation, $fname, $error = false )
    {
        if ( $error === false )
            $error = "Failed to run the cluster database operation for function:";
        eZDebug::writeError( "$error\n" . $this->lastErrorText(), $fname );
    }

    /**
     * Inserts one document of the given columns (a column => value array, name_hash among them), as the MySQLi
     * backend's _insert() inserts one row.
     *
     * @param string $table the collection
     * @param array $array column => value
     * @param string $fname
     * @return bool
     */
    public function _insert( $table, $array, $fname )
    {
        $document = (array)$array;
        if ( isset( $document['name_hash'] ) )
            $document['_id'] = (string)$document['name_hash'];
        return $this->write( (string)$table, function ( $bulk ) use ( $document )
        {
            $bulk->insert( $document );
        }, $fname ) !== false;
    }

    /**
     * Report an operation to the debug system.
     *
     * @param string $operation
     * @param string|bool $fname
     * @param float $timeTaken
     * @param int|bool $numRows
     * @return void
     */
    public function _report( $operation, $fname, $timeTaken, $numRows = false )
    {
        if ( !self::$dbparams['sql_output'] )
            return;

        $rowText = $numRows !== false ? "$numRows documents, " : '';
        if ( strlen( (string)$fname ) == 0 )
            $fname = "_query";
        eZDebug::writeNotice( "$operation", "cluster::mongodb::{$fname}[{$rowText}" . number_format( $timeTaken, 3 ) . " ms] query number per page:" . $this->numQueries++ );
    }

    /**
     * Attempts to begin cache generation by inserting the document of "<path>.generating": its _id is unique, so
     * one process wins. When it exists the path is being generated: 'ko' with the seconds left, or, once that time
     * is over, the generation is taken over (one update conditioned on the mtime read, which only one process can
     * win).
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
        $document = array( '_id' => $nameHash, 'name' => $generatingFilePath, 'name_trunk' => $generatingFilePath, 'name_hash' => $nameHash,
                           'datatype' => '', 'scope' => '', 'size' => 0, 'mtime' => $mtime, 'expired' => 0 );

        for ( $attempt = 0; $attempt < 3; ++$attempt )
        {
            $result = $this->write( $table, function ( $bulk ) use ( $document )
            {
                $bulk->insert( $document );
            }, $fname, false );
            if ( $result !== false )
                return array( 'result' => 'ok', 'mtime' => $mtime );

            if ( $this->lastErrorCode() !== (string)self::DUPLICATE_KEY )
            {
                eZDebug::writeError( "Unexpected error " . $this->lastErrorText() . " when trying to start cache generation on $filePath", __METHOD__ );
                return array( 'result' => 'error' );
            }

            // the .generating document exists: the file is being generated
            $row = $this->findOne( $table, $nameHash, $fname );
            if ( $row === false )
            {
                if ( $this->lastError !== null )
                    return array( 'result' => 'error' );
                // the generation ended between the two operations: try to take the next one
                continue;
            }

            $remainingGenerationTime = $this->remainingCacheGenerationTime( array( $row['mtime'] ) );
            if ( $remainingGenerationTime >= 0 )
                return array( 'result' => 'ko', 'remaining' => $remainingGenerationTime );

            eZDebugSetting::writeDebug( 'kernel-clustering', "$filePath generation has timedout, taking over", __METHOD__ );
            $previous = $row['mtime'];
            $result = $this->write( $table, function ( $bulk ) use ( $nameHash, $previous, $mtime )
            {
                $bulk->update( array( '_id' => $nameHash, 'mtime' => $previous ), array( '$set' => array( 'mtime' => $mtime ) ) );
            }, $fname );
            if ( $result !== false && $result->getMatchedCount() == 1 )
                return array( 'result' => 'ok', 'mtime' => $mtime );
            if ( $result !== false )
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
     * Ends the cache generation for the current file: the .generating file becomes the file on the DFS, the
     * file's document takes the generated size and time (one upsert), and the .generating document is removed.
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
        $removeGenerating = function () use ( $table, $generatingHash, $fname )
        {
            return $this->write( $table, function ( $bulk ) use ( $generatingHash )
            {
                $bulk->delete( array( '_id' => $generatingHash ), array( 'limit' => 1 ) );
            }, $fname );
        };

        if ( $rename === false )
        {
            $removeGenerating();
            $this->dfsbackend->delete( $generatingFilePath );
            return true;
        }

        $generating = $this->findOne( $table, $generatingHash, $fname );
        if ( $generating === false )
        {
            eZDebug::writeError( "An error occured while ending cache generation,  $generatingFilePath", $fname );
            return false;
        }

        if ( !$this->dfsbackend->renameOnDFS( $generatingFilePath, $filePath ) )
        {
            eZDebug::writeError( "An error occured renaming DFS://$generatingFilePath to DFS://$filePath", $fname );
            return false;
        }

        $scope = isset( $generating['scope'] ) ? (string)$generating['scope'] : '';
        $row = array( 'name' => (string)$filePath, 'name_trunk' => self::nameTrunk( $filePath, $scope ), 'name_hash' => md5( $filePath ),
                      'datatype' => isset( $generating['datatype'] ) ? (string)$generating['datatype'] : '', 'scope' => $scope,
                      'size' => isset( $generating['size'] ) ? (int)$generating['size'] : 0, 'mtime' => (int)$generating['mtime'], 'expired' => 0 );
        if ( !$this->upsert( $table, $row, array( 'mtime', 'expired', 'size' ), $fname ) )
        {
            eZDebug::writeError( "An error occured creating the metadata entry for $filePath", $fname );
            return false;
        }

        $removeGenerating();

        return true;
    }

    /**
     * Checks if generation has timed out by looking for the .generating document and comparing its timestamp to
     * the one assigned when it was created: one update conditioned on that timestamp.
     *
     * @param string $generatingFilePath
     * @param int $generatingFileMtime
     * @return bool true if the file didn't timeout, false otherwise
     */
    public function _checkCacheGenerationTimeout( $generatingFilePath, $generatingFileMtime )
    {
        $fname = "_checkCacheGenerationTimeout( $generatingFilePath, $generatingFileMtime )";
        $nameHash = md5( $generatingFilePath );
        $previous = (int)$generatingFileMtime;
        $now = time();

        // matched, not modified: the same second counts as the generation still being ours
        $result = $this->write( $this->dbTable( $generatingFilePath ), function ( $bulk ) use ( $nameHash, $previous, $now )
        {
            $bulk->update( array( '_id' => $nameHash, 'mtime' => $previous ), array( '$set' => array( 'mtime' => $now ) ) );
        }, $fname );
        if ( $result === false )
            return false;

        if ( $result->getMatchedCount() == 1 )
            return true;

        eZDebugSetting::writeDebug( 'kernel-clustering', "No rows affected for '$generatingFilePath', record has been modified", __METHOD__ );
        return false;
    }

    /**
     * Aborts the cache generation process by removing the .generating document and file
     *
     * @param string $generatingFilePath
     * @return void
     */
    public function _abortCacheGeneration( $generatingFilePath )
    {
        $fname = "_abortCacheGeneration( $generatingFilePath )";
        $hash = md5( $generatingFilePath );
        $this->write( $this->dbTable( $generatingFilePath ), function ( $bulk ) use ( $hash )
        {
            $bulk->delete( array( '_id' => $hash ), array( 'limit' => 1 ) );
        }, $fname );
        $this->dfsbackend->delete( $generatingFilePath );
    }

    /**
     * Returns the name_trunk for a file path: the part shared by the files of one view cache or template block
     * cache entry, which _deleteByDirList() expires in one write.
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
            $filter = array( 'expired' => 1, 'scope' => array( '$in' => array_values( array_map( 'strval', $scopes ) ) ) );
            if ( $expiry !== false )
                $filter['mtime'] = array( '$lt' => time() - (int)$expiry );
            $options = array( 'projection' => array( 'name' => 1 ) );
            if ( $limit !== false )
            {
                $options['skip'] = (int)$limit[0];
                $options['limit'] = (int)$limit[1];
            }
            $rows = $this->find( $table, $filter, $options, __METHOD__ );
            if ( $rows === false )
                continue;

            foreach ( $rows as $row )
                $filePathList[] = $row['name'];
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
     * Deletes a batch of cache files from the storage collection.
     *
     * @param int $limit
     * @return int The number of deleted documents
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
        $rows = $this->find( $table, array( 'name' => new MongoDB\BSON\Regex( self::prefixRegex( eZSys::cacheDirectory() . DIRECTORY_SEPARATOR ), '' ) ),
                             array( 'projection' => array( '_id' => 1 ), 'limit' => max( 0, (int)$limit ) ), __METHOD__ );
        if ( $rows === false )
            throw new RuntimeException( "MongoDB error selecting cache files in $table\n" . $this->lastErrorText() );
        if ( !$rows )
            return 0;
        $hashes = array();
        foreach ( $rows as $row )
            $hashes[] = $row['_id'];
        $result = $this->write( $table, function ( $bulk ) use ( $hashes )
        {
            $bulk->delete( array( '_id' => array( '$in' => $hashes ) ), array( 'limit' => 0 ) );
        }, __METHOD__ );
        if ( $result === false )
            throw new RuntimeException( "MongoDB error deleting cache files in $table\n" . $this->lastErrorText() );

        return $result->getDeletedCount();
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
     * @var MongoDB\Driver\Manager|null
     */
    public $db = null;

    /**
     * The database of the connection
     * @var string
     */
    protected $dbName = '';

    /**
     * DB connection parameters
     * @var array|null
     */
    protected static $dbparams = null;

    /**
     * Amount of executed operations, for debugging purpose
     * @var int
     */
    protected $numQueries = 0;

    /**
     * array( code, message ) of the last failed operation, null after a successful one
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
