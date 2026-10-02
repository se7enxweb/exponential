<?php

class eZSQLite3DB extends eZDBInterface
{
    function __construct( $parameters )
    {
        parent::__construct( $parameters );

        if ( !extension_loaded( 'sqlite3' ) )
        {
            if ( function_exists( 'eZAppendWarningItem' ) )
            {
                eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb',
                                                              'number' => eZDBInterface::ERROR_MISSING_EXTENSION ),
                                            'text' => 'SQLite3 extension was not found, the DB handler will not be initialized.' ) );
                $this->IsConnected = false;
            }
            eZDebug::writeError( 'SQLite3 extension was not found, the DB handler will not be initialized.', 'eZSQLite3DB' );
            return;
        }

        if ( $this->DBConnection === false && $this->DB !== null )
        {
            $this->DBConnection = $this->connect( $this->DB );
            // As the MySQL driver does: the callers (the setup wizard, index.php)
            // expect this exception for a database that cannot be reached
            if ( !$this->IsConnected )
                throw new eZDBNoConnectionException( self::filePath( $this->DB ), $this->ErrorMessage, $this->ErrorNumber );
        }


        // The connection's settings first: they include the busy timeout, and
        // until it is set any statement that meets a lock fails at once.
        $this->applyPragmas();
        $this->useWAL();

        // Initialize TempTableList
        $this->TempTableList = array();

        eZDebug::createAccumulatorGroup( 'sqlite3_total', 'SQLite3 Total' );
    }

    /**
     * The connection's settings, from site.ini [DatabaseSettings]
     * SQLitePragmas[] ("name=value"), after these defaults. SQLite's own
     * defaults are made for safety on any hardware, not for a web site:
     *
     * - synchronous=NORMAL: with WAL still crash-safe; FULL synced every commit
     * - cache_size=-65536: 64 MB of page cache (the default was 2 MB)
     * - mmap_size=268435456: read the file through 256 MB of mapped memory,
     *   no read() call and copy per page
     * - temp_store=MEMORY: sorts and temporary tables in memory, not on disk
     * - busy_timeout=5000: wait up to 5 s for a lock instead of failing at
     *   once (0 made a request that met another's write fail)
     *
     * Applied at every connection: most PRAGMAs belong to the connection.
     */
    protected function applyPragmas()
    {
        if ( !$this->DBConnection )
            return;
        $pragmas = array( 'synchronous' => 'NORMAL', 'cache_size' => '-65536', 'mmap_size' => '268435456',
                          'temp_store' => 'MEMORY', 'busy_timeout' => '5000' );
        $ini = eZINI::instance();
        if ( $ini->hasVariable( 'DatabaseSettings', 'SQLitePragmas' ) )
        {
            foreach ( (array)$ini->variable( 'DatabaseSettings', 'SQLitePragmas' ) as $line )
            {
                $line = trim( (string)$line );
                $eq = strpos( $line, '=' );
                if ( $line === '' || $eq === false || $eq === 0 )
                    continue;
                $name = strtolower( trim( substr( $line, 0, $eq ) ) );
                $value = trim( substr( $line, $eq + 1 ) );
                if ( !preg_match( '/^[a-z_]+$/', $name ) || !preg_match( '/^[A-Za-z0-9_-]+$/', $value ) )
                    continue;   // a PRAGMA is not a place for anything else
                $pragmas[$name] = $value;
            }
        }
        // The busy timeout before the rest, so they wait for a lock as well
        $this->StatementWaitMs = (int)$pragmas['busy_timeout'];
        $this->DBConnection->busyTimeout( $this->StatementWaitMs );
        unset( $pragmas['busy_timeout'] );
        foreach ( $pragmas as $name => $value )
        {
            @$this->DBConnection->exec( "PRAGMA $name = $value" );
        }
    }

    /**
     * WAL mode (https://www.sqlite.org/wal.html): readers and one writer at a
     * time without blocking each other. The mode is kept in the database file,
     * so it is switched on once, when the database is not in it yet, instead of
     * by every connection. Asking for it takes a lock; asked for at every
     * connection, before the busy timeout was set, it failed at once whenever
     * another process had the database to itself for a moment (WAL recovery, a
     * checkpoint), and error.log got "PRAGMA journal_mode = wal; -- database is
     * locked" for a request that then worked.
     */
    protected function useWAL()
    {
        if ( !$this->DBConnection )
            return;
        $mode = @$this->DBConnection->querySingle( 'PRAGMA journal_mode' );
        if ( is_string( $mode ) && strtolower( $mode ) === 'wal' )
            return;
        $this->query( 'PRAGMA journal_mode = wal;' );
    }

    /**
     * The directory a database named without a path lives in, relative to
     * the installation root. Below var/storage, which the shipped .htaccess
     * sends to index.php and which Velocity's static allow-list leaves out;
     * .db is not a served extension of either.
     */
    const STORAGE_DIRECTORY = 'var/storage/sqlite3';

    /**
     * The file a DatabaseSettings Database value names: an absolute path as
     * it is (a Doctrine/SQLite URL bridged in), ":memory:", or a name in
     * STORAGE_DIRECTORY.
     *
     * @param string $fileName
     * @return string
     */
    public static function filePath( $fileName )
    {
        $fileName = (string)$fileName;
        if ( $fileName === ':memory:' )
            return $fileName;
        if ( strlen( $fileName ) > 0 && $fileName[0] === '/' )
            return $fileName;
        return eZDir::path( array( self::STORAGE_DIRECTORY, $fileName ) );
    }

    /*!
     \private
     Opens a new connection to a SQLite database and returns the connection
    */
    private function connect( $fileName )
    {
/*
        print( $sql . PHP_EOL . PHP_EOL );

        $backtrace = debug_backtrace();
        $cleanedBackTrace = array();
        foreach ( $backtrace as $call )
        {
            $item = '';
            if ( isset( $call['class'] ) )
            {
                $item .= $call['class'];
            }

            if ( isset( $call['type'] ) )
            {
                $item .= $call['type'];
            }

            $item .= $call['function'] . " in file " . $call['file'] . " line " . $call['line'];

            //$item .= var_export( $call['args'], true );
            $cleanedBacktrace[] = $item;
        }

        print( implode( PHP_EOL, $cleanedBacktrace ) . PHP_EOL . PHP_EOL );
*/
        $connection = false;
        $error = 0;
        $openError = false;

        $maxAttempts = $this->connectRetryCount() + 1;
        $waitTime = $this->connectRetryWaitTime();
        $numAttempts = 1;
        $fullPath = self::filePath( $fileName );
        $directoryPath = dirname( $fullPath );
        while ( ( $connection == false || $error !== 0 ) && $numAttempts <= $maxAttempts )
        {
            // SQLite3 creates the file itself (SQLITE3_OPEN_CREATE); only the
            // directory has to be there. A directory that cannot be created,
            // or a file that cannot be opened, is a failed connection, not a
            // PHP warning followed by an uncaught exception.
            if ( $fullPath !== ':memory:' && !is_dir( $directoryPath ) )
                @mkdir( $directoryPath, 0775, true );
            try
            {
                $connection = new SQLite3( $fullPath );
                $error = $connection->lastErrorCode();
                if ( $error !== 0 && $this->OutputSQL )
                {
                    eZDebug::writeDebug( "SQLite3 error code: $error - " . $connection->lastErrorMsg(), __METHOD__ );
                }
            }
            catch ( Exception $e )
            {
                $connection = false;
                $error = -1;
                $openError = $e->getMessage();
            }
            if ( $this->OutputSQL && $connection )
            {
                eZDebug::writeDebug( "Opened SQLite3 database: $fullPath", __METHOD__ );
            }
            $numAttempts++;
        }

        if ( $error !== 0 )
        {
            $this->ErrorNumber = $error;
            $this->ErrorMessage = $connection ? $connection->lastErrorMsg() : $openError . " ($fullPath)";
            eZDebug::writeError( "Connection error: Couldn't connect to database. Please try again later or inform the system administrator. " . $this->ErrorMessage, "eZSQLite3DB" );
            $this->IsConnected = false;
            $connection = false;
        }
        else
        {
            $connection->createFunction( 'md5', array( $this, 'md5UDF' ) );

            // MySQL's bitwise aggregates. The content engine folds language
            // masks with BIT_OR, and SQLite ships no equivalent, so the
            // statement fails and takes the surrounding transaction with it.
            $connection->createAggregate( 'BIT_OR',
                function ( $context, $rows, $value ) { return (int)$context | (int)$value; },
                function ( $context, $rows ) { return (int)$context; }, 1 );
            $connection->createAggregate( 'BIT_AND',
                function ( $context, $rows, $value ) {
                    // -1 is all bits set, the identity for AND.
                    return ( $rows <= 1 ? (int)$value : (int)$context & (int)$value );
                },
                function ( $context, $rows ) { return $rows > 0 ? (int)$context : 0; }, 1 );

            $this->IsConnected = true;
        }

        return $connection;
    }

    /*!
     \reimp
    */
    function databaseName()
    {
        return 'sqlite';
    }

    /*!
      \reimp
    */
    function bindingType( )
    {
        return eZDBInterface::BINDING_NO;
    }

    /*!
      \reimp
    */
    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    /*
    */
    function checkCharset( $charset, &$currentCharset )
    {
        return true;
    }

    /*!
     \reimp
    */
    /**
     * Rewrite MySQL's "UPDATE t a INNER JOIN ( sub ) x ON c SET ..." into the
     * "UPDATE t AS a SET ... FROM ( sub ) x WHERE c" form SQLite understands.
     *
     * SQLite has supported UPDATE ... FROM since 3.33 and accepts an alias on
     * the target, so the two say the same thing. The content engine rebuilds
     * its language masks with exactly this shape, and without the rewrite the
     * statement fails and rolls back the install.
     *
     * Anything that does not match is handed back untouched.
     */
    function rewriteJoinedUpdate( $sql )
    {
        if ( !preg_match( '/^\s*UPDATE\s+(\w+)\s+(\w+)\s+INNER\s+JOIN\s*\(/is', $sql, $head ) )
            return $sql;

        $table = $head[1];
        $alias = $head[2];

        // Find the subquery's closing bracket by counting, so brackets inside
        // it - BIT_OR( ... ), a nested SELECT - do not end it early.
        $open = strpos( $sql, '(', strlen( $head[0] ) - 1 );
        if ( $open === false )
            return $sql;

        $depth = 0;
        $close = false;
        for ( $i = $open, $length = strlen( $sql ); $i < $length; $i++ )
        {
            if ( $sql[$i] === '(' )
                $depth++;
            elseif ( $sql[$i] === ')' )
            {
                $depth--;
                if ( $depth === 0 )
                {
                    $close = $i;
                    break;
                }
            }
        }
        if ( $close === false )
            return $sql;

        $subQuery = substr( $sql, $open + 1, $close - $open - 1 );
        $rest = substr( $sql, $close + 1 );

        if ( !preg_match( '/^\s*(\w+)\s+ON\s+(.+?)\s+SET\s+(.+?)(?:\s+WHERE\s+(.+))?\s*;?\s*$/is',
            $rest, $tail ) )
        {
            return $sql;
        }

        $subAlias = $tail[1];
        $onClause = trim( $tail[2] );
        $setClause = trim( $tail[3] );
        $whereClause = isset( $tail[4] ) ? trim( $tail[4] ) : '';

        // SQLite wants a bare column on the left of each assignment.
        $setClause = preg_replace( '/(^|,)(\s*)' . preg_quote( $alias, '/' ) . '\./', '$1$2', $setClause );

        $rewritten = 'UPDATE ' . $table . ' AS ' . $alias
            . ' SET ' . $setClause
            . ' FROM ( ' . trim( $subQuery ) . ' ) ' . $subAlias
            . ' WHERE ' . $onClause
            . ( $whereClause !== '' ? ' AND ( ' . $whereClause . ' )' : '' );

        return $rewritten;
    }

    function query( $sql, $server = false )
    {
        if ( $this->IsConnected )
        {
            // MySQL session settings the installers emit around bulk loads.
            // SQLite has no such switches - it enforces foreign keys only when
            // asked to, and speaks UTF-8 - so there is nothing to apply and
            // nothing lost, but failing them aborts the surrounding
            // transaction and takes the whole install with it.
            if ( preg_match( '/^\s*SET\s+(?:SESSION\s+|GLOBAL\s+)?(?:FOREIGN_KEY_CHECKS|NAMES|'
                . 'CHARACTER\s+SET|AUTOCOMMIT|SQL_MODE|UNIQUE_CHECKS)\b/i', $sql ) )
            {
                return true;
            }

            if ( $this->OutputSQL )
            {
                eZDebug::accumulatorStart( 'sqlite3_query', 'sqlite3_total', 'sqlite3_queries' );
                $this->startTimer();
            }

            $sql = $this->rewriteJoinedUpdate( $sql );

            // A failure is reported below with its reason and the statement; the
            // warning PHP adds for it said the same a second time
            $result = @$this->DBConnection->exec( $sql );
            if ( $this->OutputSQL )
            {
                $this->endTimer();

                if ( $this->timeTaken() > $this->SlowSQLTimeout )
                {
                    eZDebug::accumulatorStop( 'sqlite3_query' );
                    $this->reportQuery( 'SQLite3DB', $sql, false, $this->timeTaken() );
                }
            }

            if ( !$result )
            {
                $this->setError();

                eZDebug::writeError( self::failedQueryMessage( $this->DBConnection ? $this->DBConnection->lastErrorMsg() : "", $sql ), "eZSQLite3DB" );
                $this->reportError();
            }
            else
            {
                // A write makes the query cache's results for its tables stale.
                eZDBQueryCache::noteWrite( $this, $sql );
                return true;
            }
        }
        else
        {
            eZDebug::writeError( "Trying to do a query without being connected to a database!", "eZSQLite3DB"  );
        }

        return false;
    }

    /*!
     \reimp
    */
    function arrayQuery( $sql, $params = array(), $server = false )
    {
/*
        print( $sql . PHP_EOL . PHP_EOL );

        $backtrace = debug_backtrace();
        $cleanedBackTrace = array();
        foreach ( $backtrace as $call )
        {
            $item = '';
            if ( isset( $call['class'] ) )
            {
                $item .= $call['class'];
            }

            if ( isset( $call['type'] ) )
            {
                $item .= $call['type'];
            }

            $item .= $call['function'] . " in file " . $call['file'] . " line " . $call['line'];

            //$item .= var_export( $call['args'], true );
            $cleanedBacktrace[] = $item;
        }

        print( implode( PHP_EOL, $cleanedBacktrace ) . PHP_EOL . PHP_EOL );
        /  */

        $retArray = array();
        // The query cache (settings/querycache.ini): the rows, while current.
        $cacheTicket = eZDBQueryCache::lookup( $this, $sql, $params, $cached );
        if ( $cached !== null )
            return $cached;
        if ( $this->IsConnected )
        {
            $limit = false;
            $offset = 0;
            $column = false;
            // check for array parameters
            if ( is_array( $params ) )
            {
                if ( isset( $params["limit"] ) and is_numeric( $params["limit"] ) )
                    $limit = $params["limit"];

                if ( isset( $params["offset"] ) and is_numeric( $params["offset"] ) )
                    $offset = $params["offset"];

                if ( isset( $params["column"] ) and ( is_numeric( $params["column"] ) or is_string( $params["column"] ) ) )
                    $column = $params["column"];
            }

            if ( $limit !== false and is_numeric( $limit ) )
            {
                $sql .= "\nLIMIT $offset, $limit ";
            }
            else if ( $offset !== false and is_numeric( $offset ) and $offset > 0 )
            {
                $sql .= "\nLIMIT $offset, 18446744073709551615"; // 2^64-1
            }

            if ( $this->OutputSQL )
            {
                eZDebug::accumulatorStart( 'sqlite3_query', 'sqlite3_total', 'sqlite3_queries' );
                $this->startTimer();
            }

            $results = @$this->DBConnection->query( $sql );   // reported below when it fails

            if ( $this->OutputSQL )
            {
                $this->endTimer();

                if ( $this->timeTaken() > $this->SlowSQLTimeout )
                {
                    eZDebug::accumulatorStop( 'sqlite3_query' );
                    $this->reportQuery( 'SQLite3DB', $sql, false, $this->timeTaken() );
                }
            }

            if ( $results === false )
            {
                $this->setError();
                eZDebug::writeError( self::failedQueryMessage( $this->DBConnection ? $this->DBConnection->lastErrorMsg() : "", $sql ), "eZSQLite3DB" );
                $this->reportError();

                return false;
            }

            $i = 0;
            while ( $row = $results->fetchArray( SQLITE3_ASSOC ) )
            {
                eZDebug::accumulatorStart( 'sqlite3_loop', 'sqlite3_total', 'Looping result' );

                // SQLite sometimes gives back column names prefixed with the table name
                // we need to transform the row so that there are no table names
                $transformedRow = array();
                foreach ( $row as $identifier => $value )
                {
                    if ( strpos( $identifier, '.' ) !== false )
                    {
                        $parts = explode( '.', $identifier );
                        $newIdentifier = array_pop( $parts );
                    }
                    else
                    {
                        $newIdentifier = $identifier;
                    }

                    $transformedRow[$newIdentifier] = $value;
                }

                $retArray[$i + $offset] = is_string( $column ) ? $transformedRow[$column] : $transformedRow;
                $i++;
                eZDebug::accumulatorStop( 'sqlite3_loop' );
            }

            $results->finalize();
            eZDBQueryCache::store( $cacheTicket, $retArray );
        }
        return $retArray;
    }

    function subString( $string, $from, $len = null )
    {
        if ( $len == null )
        {
            // The rest of the string from $from, as SUBSTRING( s FROM n ) gives it on the other engines.
            // SQLite counts from 1, so substr( s, n, length( s ) - n ) stopped one character short; that
            // dropped the last character of every path below a moved node (eZContentObjectTreeNode::move()).
            return " substr( $string, $from ) ";
        }
        else
        {
            return " substr( $string, $from, $len ) ";
        }
    }

    function concatString( $strings = array() )
    {
        return implode( " || " , $strings );
    }

    function md5( $str )
    {
        return " MD5( $str ) ";
    }

    function md5UDF( $str )
    {
        return md5( $str );
    }

    function bitAnd( $arg1, $arg2 )
    {
        return '(' . $arg1 . ' & ' . $arg2 . ' ) ';
    }

    function bitOr( $arg1, $arg2 )
    {
        return '( ' . $arg1 . ' | ' . $arg2 . ' ) ';
    }

    /*!
     \reimp
     The query to start the transaction.

     BEGIN IMMEDIATE, not a plain (deferred) BEGIN: a deferred transaction
     reads from a snapshot first and asks for the write lock only at its first
     write. When another connection has committed in between, that snapshot
     is stale and SQLite answers SQLITE_BUSY at once, without calling the busy
     handler, because waiting cannot make it current: the write failed with
     "database is locked" however long busy_timeout was, and publishing
     (above all the asynchronous publisher next to web requests) lost its
     transaction. IMMEDIATE takes the write lock at the start, where the busy
     timeout applies, so a transaction waits for another writer and then sees
     its commit. SQLite allows one writer at a time anyway; readers outside a
     transaction are not affected (WAL).

     Writers queue instead of failing. Once a transaction has the write lock
     nothing inside it can lose a lock race, and WAL makes its commit all or
     nothing, so the start is the only place a transaction can fail for a
     lock, and the safe place to wait: nothing has been written yet. The start
     therefore waits [DatabaseSettings] SQLiteTransactionWait seconds (default
     60, within the web server's request timeout) instead of the statements'
     busy_timeout; a publish behind others waits its turn and completes.

     The queue is a lock file next to the database (<database>.writer-lock):
     a transaction takes it before BEGIN IMMEDIATE and gives it back when it
     ends. A try at it costs a quarter of a failed BEGIN IMMEDIATE (which
     opens a read snapshot and reports an error through PHP); the tries come
     every 1-15 ms, thinning out as the wait grows, so the next writer is in
     within a few ms of a commit (SQLite's busy handler sleeps up to 100 ms
     between tries, and a released lock stood idle that long); and a writer
     waiting longer than a second tries more often than the ones after it,
     which serves writers about in the order they came (with the busy handler
     newcomers overtook the longest waiters). The operating system releases
     the lock of a process that ends, so no crash can block the queue. Writers
     that do not use it (single statements, other programs) are still waited
     for by SQLite's busy handler, within what is left of the wait. Measured
     (#199): 192 publishes from 64 processes at once all completed whole,
     where the generic start lost 6; 48 transactions of 300 ms at once were
     done in 14.7 s, the 14.4 s they take one after another.
    */
    function beginQuery()
    {
        $start = microtime( true );
        $deadline = $start + $this->transactionWaitMs() / 1000;
        $gate = $this->writerGate();
        if ( $gate )
        {
            while ( !flock( $gate, LOCK_EX | LOCK_NB ) )
            {
                $waited = microtime( true ) - $start;
                if ( microtime( true ) >= $deadline )
                {
                    $this->ErrorNumber = 5;
                    $this->ErrorMessage = 'database is locked';
                    $this->BeginFailed = true;
                    return false;
                }
                // Every wake-up costs CPU (about 50 us here, whatever is done awake), so the tries thin out
                // as the wait grows, and a writer waiting longer than a second tries more often again than
                // those that came after it, which keeps the queue about in arrival order.
                usleep( $waited < 0.02 ? mt_rand( 1000, 2000 ) : ( $waited < 0.2 ? mt_rand( 3000, 6000 )
                        : ( $waited < 1 ? mt_rand( 8000, 15000 ) : mt_rand( 5000, 10000 ) ) ) );
            }
            $this->HoldsWriterGate = true;
        }
        $left = (int)max( 1, ( $deadline - microtime( true ) ) * 1000 );
        $this->DBConnection->busyTimeout( $left );
        $ok = @$this->DBConnection->exec( "BEGIN IMMEDIATE" );
        $this->DBConnection->busyTimeout( $this->StatementWaitMs );
        if ( $ok )
        {
            $this->InSQLTransaction = true;
            return true;
        }
        $this->setError();
        $this->releaseWriterGate();
        $this->BeginFailed = true;
        return false;
    }

    /**
     * The open lock file that queues this database's transactions, or false
     * (an in-memory database, or a file that cannot be opened: then SQLite's
     * own wait does the queueing). Opened once per connection; read-only is
     * enough for flock(), so a file another user created works as well.
     */
    protected function writerGate()
    {
        // A forked process (a Velocity worker) must not use its parent's handle: flock() belongs to the open
        // file, so processes sharing one would all count as its holder and no longer exclude each other.
        if ( $this->WriterGate !== null && $this->WriterGatePid === getmypid() )
            return $this->WriterGate;
        $this->WriterGate = false;
        $this->HoldsWriterGate = false;
        $this->WriterGatePid = getmypid();
        $path = self::filePath( $this->DB );
        if ( $path === ':memory:' || $path === '' )
            return false;
        $lock = $path . '.writer-lock';
        $fh = @fopen( $lock, 'c' );
        if ( !$fh )
            $fh = @fopen( $lock, 'r' );
        if ( $fh )
            $this->WriterGate = $fh;
        return $this->WriterGate;
    }

    /// Gives the queue's lock back (after COMMIT or ROLLBACK, or a failed start)
    protected function releaseWriterGate()
    {
        if ( $this->HoldsWriterGate && $this->WriterGate )
            flock( $this->WriterGate, LOCK_UN );
        $this->HoldsWriterGate = false;
    }

    /**
     * How long a transaction's start waits for the write lock, in ms:
     * [DatabaseSettings] SQLiteTransactionWait, in seconds (default 60).
     */
    protected function transactionWaitMs()
    {
        $seconds = 60;
        $ini = eZINI::instance();
        if ( $ini->hasVariable( 'DatabaseSettings', 'SQLiteTransactionWait' ) && is_numeric( $ini->variable( 'DatabaseSettings', 'SQLiteTransactionWait' ) ) )
            $seconds = max( 1, (int)$ini->variable( 'DatabaseSettings', 'SQLiteTransactionWait' ) );
        return $seconds * 1000;
    }

    /*!
     \reimp
     A start that did not get the write lock in time must not count as a
     transaction. The generic begin() counts it whatever the start answered,
     so everything after it ran as single statements, each committed on its
     own, and the failure surfaced only at the next write, as "cannot rollback
     - no transaction is active". Here it is reported at once, as the failed
     transaction it is (error page, or eZDBException when asked for), with
     the reason, and nothing has been written.
    */
    function begin()
    {
        $this->BeginFailed = false;
        $result = parent::begin();
        if ( $this->BeginFailed )
        {
            $this->BeginFailed = false;
            if ( $this->ErrorNumber == 5 || $this->ErrorNumber == 6 )   // SQLITE_BUSY, SQLITE_LOCKED: out of time, say so
                $this->ErrorMessage = sprintf( 'database is busy: the transaction could not start within %d s, another write held the lock all that time; nothing was written',
                                               $this->transactionWaitMs() / 1000 );
            else
                $this->ErrorMessage = 'the transaction could not start: ' . $this->ErrorMessage . '; nothing was written';
            eZDebug::writeError( $this->ErrorMessage, 'eZSQLite3DB' );
            $this->reportError();
        }
        return $result;
    }

    /*!
     \reimp
     The query to commit the transaction.
    */
    function commitQuery()
    {
        $ok = $this->query( "COMMIT" );
        if ( $ok )
        {
            $this->InSQLTransaction = false;
            $this->releaseWriterGate();
        }
        return $ok;
    }

    /*!
     \reimp
     The query to cancel the transaction. Nothing to cancel when SQLite has no
     transaction open (its start failed, or SQLite ended it itself after an
     error): answering ROLLBACK there failed with "no transaction is active"
     and hid the error that mattered.
    */
    function rollbackQuery()
    {
        if ( !$this->InSQLTransaction )
        {
            $this->releaseWriterGate();
            return true;
        }
        $this->InSQLTransaction = false;
        $ok = @$this->DBConnection->exec( "ROLLBACK" )
              || stripos( (string)$this->DBConnection->lastErrorMsg(), 'no transaction is active' ) !== false
              || $this->query( "ROLLBACK" );
        $this->releaseWriterGate();
        return $ok;
    }

    /*!
     \reimp
    */
    function lastSerialID( $table = false, $column = false )
    {
        if ( $this->IsConnected )
        {
            $id = $this->DBConnection->lastInsertRowID();

            // if the primary key consists of more than one field
            // then we can not rely on the auto increment functionality of SQLite,
            /// because it only works on a PRIMARY KEY of 1 column
            // so we need to check if the autoincrement field matches the rowid
            // if not, we'll update it
            $result = $this->arrayQuery( "SELECT $column FROM $table WHERE rowid=$id" );
            if ( $result[0][$column] != $id )
            {
                // we use the maximum + 1 instead of the rowid, because some autoincrement fields
                // in the standard data do not follow up each other
                // so for the standard data the autoincrement column might not match the rowid
                // and query errors will appear when we add new data because of unique key violations
                $max = $this->arrayQuery( "SELECT MAX($column) AS maximum FROM $table" );

                $newID = $max['0']['maximum'] + 1;

                $this->query( "UPDATE $table SET $column=$newID WHERE rowid=$id" );

                return $newID;
            }
            else
            {
                return $id;
            }
        }
        else
        {
            return false;
        }
    }

    /*!
     \reimp
    */
    function escapeString( $str )
    {
        // As the MySQL driver does: null is an empty string (PHP 8.1 deprecates
        // passing null to SQLite3::escapeString)
        if ( $str === null )
            return '';
        if ( $this->IsConnected )
        {
            return $this->DBConnection->escapeString( (string)$str );
        }
        else
        {
            // SQLite3::escapeString() needs no connection; returning the value
            // unescaped broke every statement with a quote in it.
            return SQLite3::escapeString( (string)$str );
        }
    }

    /*!
     \reimp
    */
    function close()
    {
        if ( $this->IsConnected )
        {
            $this->DBConnection->close();   // SQLite rolls back a transaction left open
            $this->IsConnected = false;
            $this->InSQLTransaction = false;
        }
        $this->releaseWriterGate();
        if ( $this->WriterGate )
            fclose( $this->WriterGate );
        $this->WriterGate = null;
    }

    function __destruct()
    {
        $this->close();
    }

    /*!
     \reimp
    */
    function createDatabase( $dbName )
    {
        // A SQLite database is a file, and connect() creates it on demand, so
        // there is nothing to do here beyond making sure the directory exists.
        $directory = self::STORAGE_DIRECTORY;
        if ( !file_exists( $directory ) )
            eZDir::mkdir( $directory, false, true );
    }

    /**
     * Empty the database.
     *
     * The installers ask for the database to be removed and recreated before
     * they load a schema. A SQLite database is a file rather than something
     * the server owns, so the inherited no-op left the previous install in
     * place and every CREATE TABLE that followed failed with "table already
     * exists" - which is what stopped a SQLite install part way through.
     *
     * The objects are dropped rather than the file unlinked: the connection is
     * open and other handles may hold the same path, and an emptied database
     * is what "remove then create" is asking for.
     */
    function removeDatabase( $dbName )
    {
        if ( !$this->IsConnected )
            return false;

        $objects = array();
        $result = $this->DBConnection->query(
            "SELECT type, name FROM sqlite_master"
            . " WHERE name NOT LIKE 'sqlite_%'"
            . " AND type IN ( 'table', 'view', 'index', 'trigger' )" );
        if ( $result )
        {
            while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
                $objects[] = $row;
        }

        // Triggers and views first, then indexes, then the tables they sit on.
        $order = array( 'trigger' => 0, 'view' => 1, 'index' => 2, 'table' => 3 );
        usort( $objects, function ( $left, $right ) use ( $order ) {
            return $order[$left['type']] - $order[$right['type']];
        } );

        $this->DBConnection->exec( 'PRAGMA foreign_keys = OFF' );

        $removed = 0;
        foreach ( $objects as $object )
        {
            // An index backing a UNIQUE or PRIMARY KEY constraint cannot be
            // dropped on its own; it goes when its table does.
            if ( $object['type'] === 'index' && strpos( $object['name'], 'sqlite_autoindex' ) === 0 )
                continue;

            if ( @$this->DBConnection->exec(
                'DROP ' . strtoupper( $object['type'] ) . ' IF EXISTS "' . $object['name'] . '"' ) )
            {
                $removed++;
            }
        }

        $this->DBConnection->exec( 'PRAGMA foreign_keys = ON' );
        $this->DBConnection->exec( 'VACUUM' );

        eZDebug::writeNotice( "Emptied SQLite database '$dbName': dropped $removed object(s)",
                              __METHOD__ );

        return true;
    }

    /*!
     \reimp
    */
    function setError()
    {
        if ( $this->DBConnection )
        {
            $this->ErrorNumber = $this->DBConnection->lastErrorCode();
            $this->ErrorMessage = $this->DBConnection->lastErrorMsg();
        }
    }

    /*!
     \reimp
    */
    function availableDatabases()
    {
        return self::availableDatabasesIn( self::STORAGE_DIRECTORY );
    }

    /**
     * The database files in a directory, sorted by name.
     *
     * Databases only: WAL mode keeps <name>-wal and <name>-shm next to each
     * one, and a rollback journal is <name>-journal; offered as a database,
     * any of them could be the setup wizard's first choice.
     *
     * @param string $directory
     * @return string[]
     */
    public static function availableDatabasesIn( $directory )
    {
        $returnFiles = array();
        if ( $handle = @opendir( $directory ) )
        {
            while ( ( $file = readdir( $handle ) ) !== false )
            {
                if ( $file[0] === '.' || preg_match( '/-(wal|shm|journal)$/', $file ) )
                    continue;
                if ( !is_file( $directory . '/' . $file ) )
                    continue;
                // only SQLite databases (or empty files, which SQLite opens as new ones): no SQL dumps or other files
                $size = @filesize( $directory . '/' . $file );
                if ( $size !== 0 && @file_get_contents( $directory . '/' . $file, false, null, 0, 16 ) !== "SQLite format 3\0" )
                    continue;
                $returnFiles[] = $file;
            }
            @closedir( $handle );
        }
        sort( $returnFiles );
        return $returnFiles;
    }

    /*!
     \reimp
    */
    function databaseServerVersion()
    {
        // no server, so returning client version
        //return $this->databaseClientVersion();
        //setup require string instead of array
        $versionInfo = SQLite3::version();
        $versionString = $versionInfo['versionString'];
        return array( 'string' => $versionString, 'values' => $versionString );
    }

    /*!
     \reimp
    */
    function databaseClientVersion()
    {
        $versionInfo = SQLite3::version();
        $versionString = $versionInfo['versionString'];

        $versionArray = explode( '.', $versionString );

        return array( 'string' => $versionInfo,
                      'values' => $versionArray );
    }

    /*!
     \reimp
    */
    function isCharsetSupported( $charset )
    {
        return true;
    }

    function eZTableList( $server = eZDBInterface::SERVER_MASTER )
    {
        $tables = array();
        if ( $this->IsConnected )
        {
            $sql = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
            $results = $this->arrayQuery( $sql );

            foreach ( $results as $entry )
            {
                $tableName = $entry['name'];
                if ( substr( $tableName, 0, 2 ) == 'ez' )
                {
                    $tables[$tableName] = eZDBInterface::RELATION_TABLE;
                }
            }
        }
        return $tables;
    }

    /*!
     \reimp
    */
    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE );
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( $relationType != eZDBInterface::RELATION_TABLE )
        {
            eZDebug::writeError( "Unsupported relation type '$relationType'", 'eZSQLite3DB::relationList' );
            return false;
        }

        $tables = array_keys( $this->eZTableList() );
        return $tables;
    }

    /*!
      \reimp
    */
    function removeRelation( $relationName, $relationType )
    {
        $relationTypeName = $this->relationName( $relationType );
        if ( !$relationTypeName )
        {
            eZDebug::writeError( "Unknown relation type '$relationType'", 'eZSQLite3DB::removeRelation' );
            return false;
        }

        if ( $this->IsConnected )
        {
            $sql = "DROP $relationTypeName $relationName";
            return $this->query( $sql );
        }
        return false;
    }

    public $TempTableList;

    /// The statements' lock wait in ms (busy_timeout), restored after a transaction's longer one
    protected $StatementWaitMs = 5000;
    /// Whether SQLite has a transaction open on this connection (BEGIN IMMEDIATE succeeded, not ended yet)
    protected $InSQLTransaction = false;
    /// Set by beginQuery() when the start did not get the write lock in time
    protected $BeginFailed = false;
    /// The open <database>.writer-lock file (null: not opened yet, false: none), and whether this connection holds it
    protected $WriterGate = null;
    protected $HoldsWriterGate = false;
    protected $WriterGatePid = 0;
}

?>