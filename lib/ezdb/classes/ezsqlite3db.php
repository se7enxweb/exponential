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
        }


        // WAL mode has better control over concurrency.
        // Source: https://www.sqlite.org/wal.html
        $this->query('PRAGMA journal_mode = wal;');

        // Initialize TempTableList
        $this->TempTableList = array();

        eZDebug::createAccumulatorGroup( 'sqlite3_total', 'SQLite3 Total' );
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

        $maxAttempts = $this->connectRetryCount() + 1;
        $waitTime = $this->connectRetryWaitTime();
        $numAttempts = 1;
        while ( ( $connection == false || $error !== 0 ) && $numAttempts <= $maxAttempts )
        {
            $directoryPath = 'var/storage/sqlite3';
            // Use absolute path directly (e.g. when bridged from Doctrine/SQLite URL);
            // otherwise prefix with the default storage directory.
            if ( $fileName === ':memory:' )
                $fullPath = $fileName;
            elseif ( strlen( $fileName ) > 0 && $fileName[0] === '/' )
                $fullPath = $fileName;
            else
                $fullPath = eZDir::path( array( $directoryPath, $fileName ) );

            if( !file_exists( $directoryPath ) )
                mkdir( $directoryPath, 0775, true );
            // var_dump( $fullPath ); echo '<hr>'; // die();
            if( !file_exists( $fullPath ) )
                $fh = fopen($fullPath, 'w') or eZDebug::writeError( "Connection error: Couldn't create database file. Please try again later or inform the system administrator.", "eZSQLite3DB" );
            $connection = new SQLite3( $fullPath );
            if ( $this->OutputSQL )
            {
                eZDebug::writeDebug( "Opened SQLite3 database: $fullPath", __METHOD__ );
            }

            if ( $connection )
            {
                $error = $connection->lastErrorCode();
                if ( $error !== 0 && $this->OutputSQL )
                {
                    eZDebug::writeDebug( "SQLite3 error code: $error - " . $connection->lastErrorMsg(), __METHOD__ );
                }
            }
            $numAttempts++;
        }

        if ( $error !== 0 )
        {
            $this->ErrorNumber = $error;
            $this->ErrorMessage = $connection->lastErrorMsg();
            eZDebug::writeError( "Connection error: Couldn't connect to database. Please try again later or inform the system administrator.", "eZSQLite3DB" );
            $this->IsConnected = false;
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

            $result = $this->DBConnection->exec( $sql );
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

                eZDebug::writeError( "Error: error when executing query: $sql", "eZSQLite3DB" );
                $this->reportError();
            }
            else
            {
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

            $results = $this->DBConnection->query( $sql );

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
                eZDebug::writeError( "Error: error executing query: $sql", "eZSQLite3DB" );
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
        }
        return $retArray;
    }

    function subString( $string, $from, $len = null )
    {
        if ( $len == null )
        {
            return " substr( $string, $from, length( $string ) - $from ) ";
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
    */
    function beginQuery()
    {
        return $this->query( "BEGIN" );
    }

    /*!
     \reimp
     The query to commit the transaction.
    */
    function commitQuery()
    {
        return $this->query( "COMMIT" );
    }

    /*!
     \reimp
     The query to cancel the transaction.
    */
    function rollbackQuery()
    {
        return $this->query( "ROLLBACK" );
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
        if ( $this->IsConnected )
        {
            return $this->DBConnection->escapeString( $str );
        }
        else
        {
            return $str;
        }
    }

    /*!
     \reimp
    */
    function close()
    {
        if ( $this->IsConnected )
        {
            $this->DBConnection->close();
            $this->IsConnected = false;
        }
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
        $directory = 'var/storage/sqlite3';
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
        $returnFiles = array();
        if ( $handle = @opendir( 'var/storage/sqlite3' ) )
        {
            while ( ( $file = readdir( $handle ) ) !== false )
            {
                if ( ( $file == "." ) || ( $file == ".." ) )
                {
                    continue;
                }

                if ( is_file( 'var/storage/sqlite3/' . $file ) )
                {
                    $returnFiles[] = $file;
                }
            }
            @closedir( $handle );
        }
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
}

?>