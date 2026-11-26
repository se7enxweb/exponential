<?php
/**
 * File containing the eZMySQLiDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 3d18f47551), Copyright (C) Mugo Web, GPL v2
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZMySQLiDB eZMySQLiDB.php
  \ingroup eZDB
  \brief The eZMySQLiDB class provides MySQL implementation of the database interface.

  eZMySQLiDB is the MySQL implementation of eZDB.
  \sa eZDB
*/

class eZMySQLiDB extends eZDBInterface
{
    const RELATION_FOREIGN_KEY = 5;
    const RELATION_FOREIGN_KEY_BIT = 32;

    /**
     * The range of an INT column (int(11) in the kernel schema): signed 32-bit (eZDBInterface::clampInteger()).
     *
     * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 3774a54ead), Copyright (C) Mugo Web, GPL v2
     *
     * @var int
     */
    const MAX_INT = 2147483647;

    /** @var int see MAX_INT */
    const MIN_INT = -2147483648;

    /**
     * Create a new eZMySQLiDB object and connects to the database backend.
     *
     * @param array $parameters
     */
    public function __construct( $parameters )
    {
        parent::__construct( $parameters );

        if ( !extension_loaded( 'mysqli' ) )
        {
            if ( function_exists( 'eZAppendWarningItem' ) )
            {
                eZAppendWarningItem( array( 'error' => array( 'type' => 'ezdb',
                                                              'number' => eZDBInterface::ERROR_MISSING_EXTENSION ),
                                            'text' => 'MySQLi extension was not found, the DB handler will not be initialized.' ) );
                $this->IsConnected = false;
            }
            eZDebug::writeWarning( 'MySQLi extension was not found, the DB handler will not be initialized.', 'eZMySQLiDB' );
            return;
        }

        eZDebug::createAccumulatorGroup( 'mysqli_total', 'Mysql Total' );

        /// Connect to master server
        if ( !$this->DBWriteConnection )
        {
            $connection = $this->connect( $this->Server, $this->DB, $this->User, $this->Password, $this->SocketPath, $this->Charset, $this->Port );
            if ( $this->IsConnected )
            {
                $this->DBWriteConnection = $connection;
            }
        }

        // DBConnection is the primary as well. A read replica ([DatabaseSettings] UseSlaveServer) has a connection of
        // its own, opened on the first statement that may run there (replicaConnectionFor()): a replica that is down
        // no longer stops the site, and every statement that must not read a replica never sees one.
        if ( !$this->DBConnection && $this->DBWriteConnection )
        {
            $this->DBConnection = $this->DBWriteConnection;
            $this->IsConnected = true;
        }

        // Initialize TempTableList
        $this->TempTableList = array();
    }

    /*!
     \private
     Opens a new connection to a MySQL database and returns the connection
    */
    function connect( $server, $db, $user, $password, $socketPath, $charset = null, $port = false )
    {
        $connection = false;

        if ( $socketPath !== false )
        {
            ini_set( "mysqli.default_socket", $socketPath );
        }

        if ( $this->UsePersistentConnection == true )
        {
            // Only supported on PHP 5.3 (mysqlnd)
            if ( version_compare( PHP_VERSION, '5.3' ) > 0 )
                $server = 'p:' . $server;
            else
                eZDebug::writeWarning( 'mysqli only supports persistent connections when using php 5.3 and higher', __METHOD__ );
        }

        $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        eZDebug::accumulatorStart( 'mysqli_connection', 'mysqli_total', 'Database connection' );
        try {
            $connection = mysqli_connect( $server, $user, $password, null, (int)$port, $socketPath );
        } catch( ErrorException | mysqli_sql_exception $e ) { $connection = false; }
        eZDebug::accumulatorStop( 'mysqli_connection' );
        eZDebug::setHandleType( $oldHandling );

        $maxAttempts = $this->connectRetryCount();
        $waitTime = $this->connectRetryWaitTime();
        $numAttempts = 1;
        while ( !$connection && $numAttempts <= $maxAttempts )
        {
            sleep( $waitTime );

            $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
            eZDebug::accumulatorStart( 'mysqli_connection', 'mysqli_total', 'Database connection' );
            try {
                $connection = mysqli_connect( $server, $user, $password, null, (int)$port, $socketPath );
            } catch( ErrorException | mysqli_sql_exception $e ) { $connection = false; }
            eZDebug::accumulatorStop( 'mysqli_connection' );
            eZDebug::setHandleType( $oldHandling );

            $numAttempts++;
        }
        $this->setError();

        $this->IsConnected = true;

        if ( !$connection )
        {
            eZDebug::writeError( "Connection error: Couldn't connect to database server. Please try again later or inform the system administrator.\n{$this->ErrorMessage}", __CLASS__ );
            eZLog::write( "eZMySQLiDB connection failed to server '{$server}' error #{$this->ErrorNumber}: {$this->ErrorMessage}", 'error.log' );
            $this->IsConnected = false;
            throw new eZDBNoConnectionException( $server, $this->ErrorMessage, $this->ErrorNumber );
        }

        if ( $this->IsConnected && $db != null )
        {
            $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
            try
            {
                $ret = mysqli_select_db( $connection, $db );
            }
            catch( ErrorException | mysqli_sql_exception $e )
            {
                $ret = false;
            }
            eZDebug::setHandleType( $oldHandling );
            if ( !$ret )
            {
                $this->setError( $connection );
                eZDebug::writeError( "Connection error: Couldn't select the database. Please try again later or inform the system administrator.\n{$this->ErrorMessage}", __CLASS__ );
                eZLog::write( "eZMySQLiDB couldn't select database '{$db}' error #{$this->ErrorNumber}: {$this->ErrorMessage}", 'error.log' );
                $this->IsConnected = false;
            }
        }

        if ( $charset !== null )
        {
            $originalCharset = $charset;
            $charset = eZCharsetInfo::realCharsetCode( $charset );
        }

        if ( $this->IsConnected and $charset !== null )
        {
            // Resolve the name BEFORE switching to exception handling.
            //
            // This used to read mysqli_set_charset( $connection,
            // eZMySQLCharset::mapTo( $charset ) ), which puts the class's
            // autoload inside a window where any notice or warning becomes an
            // ErrorException and is caught below as "the charset could not be
            // set". Autoloading probes paths, a probe that misses can raise,
            // and then the connection silently keeps the server's default --
            // latin1 here. Nothing is logged and nothing fails: rows just come
            // back transliterated, so a non-breaking space arrives as "?", XML
            // fields stop parsing, and pages quietly lose their content.
            //
            // It cost a long afternoon to find. Nothing but the call itself
            // belongs between setHandleType() and the catch.
            $mappedCharset = eZMySQLCharset::mapTo( $charset );

            $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
            try
            {
                $status = mysqli_set_charset( $connection, $mappedCharset );
            }
            catch( ErrorException | mysqli_sql_exception $e )
            {
                $status = false;
            }
            eZDebug::setHandleType( $oldHandling );

            // Trust what the connection reports, not what the call returned.
            // The return value can be false for reasons that have nothing to
            // do with the connection, and running on the wrong charset
            // corrupts data silently, so it is worth one more round trip to
            // be sure -- and worth trying again before giving up.
            $activeCharset = @mysqli_character_set_name( $connection );
            if ( $activeCharset !== $mappedCharset )
            {
                @mysqli_set_charset( $connection, $mappedCharset );
                $activeCharset = @mysqli_character_set_name( $connection );
            }

            if ( $activeCharset !== $mappedCharset )
            {
                $this->setError();
                eZDebug::writeWarning( "Connection warning: could not set the connection charset to"
                    . " '$mappedCharset'; it is '$activeCharset'. Text will be transliterated to that"
                    . " character set on its way out of the database. "
                    . mysqli_errno( $connection ) . ": " . mysqli_error( $connection ), "eZMySQLiDB" );
            }
            else if ( !$status )
            {
                eZDebug::writeNotice( "mysqli_set_charset() reported failure but the connection is"
                    . " '$activeCharset' as requested; continuing.", "eZMySQLiDB" );
            }
        }

        return $connection;
    }

    function databaseName()
    {
        return 'mysql';
    }

    function bindingType( )
    {
        return eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    /*!
      Checks if the requested character set matches the one used in the database.

      \return \c true if it matches or \c false if it differs.
      \param[out] $currentCharset The charset that the database uses.
                                  will only be set if the match fails.
                                  Note: This will be specific to the database.
    */
    function checkCharset( $charset, &$currentCharset )
    {
        // If we don't have a database yet we shouldn't check it
        if ( !$this->DB )
            return true;

        $versionInfo = $this->databaseServerVersion();

        if ( is_array( $charset ) )
        {
            $realCharset = array();
            foreach ( $charset as $charsetItem )
                $realCharset[] = eZCharsetInfo::realCharsetCode( $charsetItem );
        }
        else
            $realCharset = eZCharsetInfo::realCharsetCode( $charset );

        return $this->checkCharsetPriv( $realCharset, $currentCharset );
    }

    /*!
     \private
    */
    function checkCharsetPriv( $charset, &$currentCharset )
    {
        $query = "SHOW CREATE DATABASE `{$this->DB}`";
        $status = mysqli_query( $this->DBConnection, $query );
        $this->reportQuery( __CLASS__, $query, false, false );
        if ( !$status )
        {
            $this->setError();
            eZDebug::writeWarning( "Connection warning: " . mysqli_errno( $this->DBConnection ) . ": " . mysqli_error( $this->DBConnection ), "eZMySQLiDB" );
            return false;
        }

        $numRows = mysqli_num_rows( $status );
        if ( $numRows == 0 )
            return false;

        for ( $i = 0; $i < $numRows; ++$i )
        {
            $tmpRow = mysqli_fetch_array( $status, MYSQLI_ASSOC );
            if ( $tmpRow['Database'] == $this->DB )
            {
                $createText = $tmpRow['Create Database'];
                if ( preg_match( '#DEFAULT CHARACTER SET ([a-zA-Z0-9_-]+)#', $createText, $matches ) )
                {
                    $currentCharset = $matches[1];
                    $currentCharset = eZCharsetInfo::realCharsetCode( $currentCharset );
                    // Convert charset names into something MySQL will understand
                    $unmappedCurrentCharset = eZMySQLCharset::mapFrom( $currentCharset );

                    if ( is_array( $charset ) )
                    {
                        if ( in_array( $unmappedCurrentCharset, $charset ) )
                        {
                            return $unmappedCurrentCharset;
                        }
                    }
                    else if ( $unmappedCurrentCharset == $charset )
                    {
                        return true;
                    }
                    return false;
                }
                break;
            }
        }
        return true;
    }

    function query( $sql, $server = false )
    {
        if ( $this->IsConnected )
        {
            eZDebug::accumulatorStart( 'mysqli_query', 'mysqli_total', 'Mysqli_queries' );
            $orig_sql = $sql;
            // The converted sql should not be output
            if ( $this->InputTextCodec )
            {
                eZDebug::accumulatorStart( 'mysqli_conversion', 'mysqli_total', 'String conversion in mysqli' );
                $sql = $this->InputTextCodec->convertString( $sql );
                eZDebug::accumulatorStop( 'mysqli_conversion' );
            }

            if ( $this->OutputSQL )
            {
                $this->startTimer();
            }

            $sql = trim( $sql );

            // A read replica ([DatabaseSettings] UseSlaveServer): a plain SELECT of an anonymous GET/HEAD request that
            // has written nothing yet may run there, while the replica is current; anything else, or a failure there,
            // runs on the primary. SERVER_SLAVE is a wish, not an order: the rule decides (SERVER_MASTER always wins).
            $replicaResult = false;
            $replica = $this->replicaConnectionFor( $sql, $server );
            if ( !$replica )
                $this->notePrimaryStatement( $sql );
            $server = eZDBInterface::SERVER_MASTER;
            $connection = $this->DBWriteConnection;

            $analysisText = false;
            // If query analysis is enable we need to run the query
            // with an EXPLAIN in front of it
            // Then we build a human-readable table out of the result
            if ( $this->QueryAnalysisOutput )
            {
                $analysisResult = mysqli_query( $connection, 'EXPLAIN ' . $sql );
                if ( $analysisResult )
                {
                    $numRows = mysqli_num_rows( $analysisResult );
                    $rows = array();
                    if ( $numRows > 0 )
                    {
                        for ( $i = 0; $i < $numRows; ++$i )
                        {
                            if ( $this->InputTextCodec )
                            {
                                $tmpRow = mysqli_fetch_array( $analysisResult, MYSQLI_ASSOC );
                                $convRow = array();
                                foreach( $tmpRow as $key => $row )
                                {
                                    $convRow[$key] = $this->OutputTextCodec->convertString( $row );
                                }
                                $rows[$i] = $convRow;
                            }
                            else
                                $rows[$i] = mysqli_fetch_array( $analysisResult, MYSQLI_ASSOC );
                        }
                    }

                    // Figure out all columns and their maximum display size
                    $columns = array();
                    foreach ( $rows as $row )
                    {
                        foreach ( $row as $col => $data )
                        {
                            if ( !isset( $columns[$col] ) )
                                $columns[$col] = array( 'name' => $col,
                                                        'size' => strlen( $col ) );
                            $columns[$col]['size'] = max( $columns[$col]['size'], strlen( $data ) );
                        }
                    }

                    $delimiterLine = array();
                    $colLine = array();
                    // Generate the column line and the vertical delimiter
                    // The look of the table is taken from the MySQL CLI client
                    // It looks like this:
                    // +-------+-------+
                    // | col_a | col_b |
                    // +-------+-------+
                    // | txt   |    42 |
                    // +-------+-------+
                    foreach ( $columns as $col )
                    {
                        $delimiterLine[] = str_repeat( '-', $col['size'] + 2 );
                        $colLine[] = ' ' . str_pad( $col['name'], $col['size'], ' ', STR_PAD_RIGHT ) . ' ';
                    }
                    $delimiterLine = '+' . join( '+', $delimiterLine ) . "+\n";
                    $analysisText = $delimiterLine;
                    $analysisText .= '|' . join( '|', $colLine ) . "|\n";
                    $analysisText .= $delimiterLine;

                    // Go trough all data and pad them to create the table correctly
                    foreach ( $rows as $row )
                    {
                        $rowLine = array();
                        foreach ( $columns as $col )
                        {
                            $name = $col['name'];
                            $size = $col['size'];
                            $data = isset( $row[$name] ) ? $row[$name] : '';
                            // Align numerical values to the right (ie. pad left)
                            $rowLine[] = ' ' . str_pad( $data, $size, ' ',
                                                        is_numeric( $data ) ? STR_PAD_LEFT : STR_PAD_RIGHT ) . ' ';
                        }
                        $analysisText .= '|' . join( '|', $rowLine ) . "|\n";
                        $analysisText .= $delimiterLine;
                    }

                    // Reduce memory usage
                    unset( $rows, $delimiterLine, $colLine, $columns );
                }
            }

            $profileStart = eZDBInterface::$SQLProfileOn === false ? 0 : microtime( true );
            if ( $replica )
                $replicaResult = $this->replicaQuery( $replica, $sql );
            if ( $replicaResult !== false )
            {
                $result = $replicaResult;
                $connection = $replica;
                $server = eZDBInterface::SERVER_SLAVE;
            }
            else if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
            {
                // With mysqli reporting errors as exceptions (PHP 8.1's default) a failed statement throws
                // mysqli_sql_exception past this driver. Code that asked for the database's exceptions gets an
                // eZDBException from the failure handling below instead, its code the MySQL error number, after
                // the transaction is rolled back. The standard error handling is left as it was.
                try
                {
                    $result = mysqli_query( $connection, $sql );
                }
                catch ( mysqli_sql_exception $e )
                {
                    $result = false;
                }
            }
            else
                $result = mysqli_query( $connection, $sql );
            if ( eZDBInterface::$SQLProfileOn !== false )
                eZDBInterface::profileSQL( $sql, microtime( true ) - $profileStart );
            // A write makes the query cache's results for its tables stale.
            if ( $result )
                eZDBQueryCache::noteWrite( $this, $sql );

            if ( $this->RecordError and !$result )
                $this->setError( $connection );

            if ( $this->OutputSQL )
            {
                $this->endTimer();

                if ($this->timeTaken() > $this->SlowSQLTimeout)
                {
                    $num_rows = mysqli_affected_rows( $connection );
                    $text = $sql;

                    // If we have some analysis text we append this to the SQL output
                    if ( $analysisText !== false )
                        $text = "EXPLAIN\n" . $text . "\n\nANALYSIS:\n" . $analysisText;

                    $this->reportQuery( __CLASS__ . '[' . $connection->host_info . ( $server == eZDBInterface::SERVER_MASTER ? ', on master' : '' ) . ']', $text, $num_rows, $this->timeTaken() );
                }
            }
            eZDebug::accumulatorStop( 'mysqli_query' );
            if ( $result )
            {
                return $result;
            }
            else
            {
                // The error of the connection the statement ran on: with a separate write server the slave
                // connection setError() reads by default knows nothing of it, and the number was 0.
                $this->setError( $connection );
                $errorMessage = self::failedQueryMessage( '(' . $this->ErrorNumber . ') ' . $this->ErrorMessage, $sql );
                eZDebug::writeError( $errorMessage, __CLASS__  );
                // eZDebug writes errors to error.log itself (site.ini [DebugSettings]
                // AlwaysLog[]=error); written here as well, each failed query was there twice
                if ( !eZDebug::alwaysLogMessage( eZDebug::LEVEL_ERROR ) )
                    eZLog::write( $errorMessage, 'error.log' );
                $oldRecordError = $this->RecordError;
                // Turn off error handling while we unlock
                $this->RecordError = false;
                mysqli_query( $connection, 'UNLOCK TABLES' );
                $this->RecordError = $oldRecordError;

                $this->reportError();

                // This is to behave the same way as other RDBMS PHP API as PostgreSQL
                // functions which throws an error with a failing request.
                if ( $this->errorHandling == eZDB::ERROR_HANDLING_STANDARD )
                {
                    eZFatalUserError::raise( "mysqli_query(): $errorMessage" );
                }
                else
                {
                    throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
                }

                return false;
            }
        }
        else
        {
            eZDebug::writeError( "Trying to do a query without being connected to a database!", __CLASS__ );
        }


    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
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
            $result = $this->query( $sql, $server );

            if ( $result == false )
            {
                $this->reportQuery( __CLASS__, $sql, false, false );
                return false;
            }

            $numRows = mysqli_num_rows( $result );
            if ( $numRows > 0 )
            {
                if ( !is_string( $column ) )
                {
                    eZDebug::accumulatorStart( 'mysqli_loop', 'mysqli_total', 'Looping result' );
                    for ( $i=0; $i < $numRows; $i++ )
                    {
                        if ( $this->InputTextCodec )
                        {
                            $tmpRow = mysqli_fetch_array( $result, MYSQLI_ASSOC );
                            $convRow = array();
                            foreach( $tmpRow as $key => $row )
                            {
                                eZDebug::accumulatorStart( 'mysqli_conversion', 'mysqli_total', 'String conversion in mysqli' );
                                $convRow[$key] = $this->OutputTextCodec->convertString( $row );
                                eZDebug::accumulatorStop( 'mysqli_conversion' );
                            }
                            $retArray[$i + $offset] = $convRow;
                        }
                        else
                            $retArray[$i + $offset] = mysqli_fetch_array( $result, MYSQLI_ASSOC );
                    }
                    eZDebug::accumulatorStop( 'mysqli_loop' );

                }
                else
                {
                    eZDebug::accumulatorStart( 'mysqli_loop', 'mysqli_total', 'Looping result' );
                    for ( $i=0; $i < $numRows; $i++ )
                    {
                        $tmp_row = mysqli_fetch_array( $result, MYSQLI_ASSOC );
                        if ( $this->InputTextCodec )
                        {
                            eZDebug::accumulatorStart( 'mysqli_conversion', 'mysqli_total', 'String conversion in mysqli' );
                            $retArray[$i + $offset] = $this->OutputTextCodec->convertString( $tmp_row[$column] );
                            eZDebug::accumulatorStop( 'mysqli_conversion' );
                        }
                        else
                            $retArray[$i + $offset] =& $tmp_row[$column];
                    }
                    eZDebug::accumulatorStop( 'mysqli_loop' );
                }
            }
            eZDBQueryCache::store( $cacheTicket, $retArray );
        }
        return $retArray;
    }

    function subString( $string, $from, $len = null )
    {
        if ( $len == null )
        {
            return " substring( $string from $from ) ";
        }else
        {
            return " substring( $string from $from for $len ) ";
        }
    }

    function concatString( $strings = array() )
    {
        $str = implode( "," , $strings );
        return " concat( $str  ) ";
    }

    function md5( $str )
    {
        return " MD5( $str ) ";
    }

    function bitAnd( $arg1, $arg2 )
    {
        return 'cast(' . $arg1 . ' & ' . $arg2 . ' AS SIGNED ) ';
    }

    function bitOr( $arg1, $arg2 )
    {
        return 'cast( ' . $arg1 . ' | ' . $arg2 . ' AS SIGNED ) ';
    }

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT | self::RELATION_FOREIGN_KEY_BIT;
    }

    function supportedRelationTypes()
    {
        return array( self::RELATION_FOREIGN_KEY, eZDBInterface::RELATION_TABLE );
    }

    function relationCounts( $relationMask )
    {
        if ( $relationMask & eZDBInterface::RELATION_TABLE_BIT )
            return $this->relationCount();
        else
            return 0;
    }

    function relationCount( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( !in_array( $relationType, $this->supportedRelationTypes() ) )
        {
            eZDebug::writeError( "Unsupported relation type '$relationType'", __METHOD__ );
            return false;
        }
        $count = false;
        if ( $this->IsConnected )
        {
            switch ( $relationType )
            {
                case eZDBInterface::RELATION_TABLE:
                {
                    $query = 'SHOW TABLES from `' . $this->DB .'`';
                    $result = mysqli_query( $this->DBConnection, $query );
                    $this->reportQuery( __CLASS__, $query, false, false );
                    $count = mysqli_num_rows( $result );
                    mysqli_free_result( $result );
                } break;

                case self::RELATION_FOREIGN_KEY:
                {
                    $count = count( $this->relationList( self::RELATION_FOREIGN_KEY ) );
                } break;
            }
        }
        return $count;
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( !in_array( $relationType, $this->supportedRelationTypes() ) )
        {
            eZDebug::writeError( "Unsupported relation type '$relationType'", __METHOD__ );
            return false;
        }
        if ( $this->IsConnected )
        {
            switch ( $relationType )
            {
                case eZDBInterface::RELATION_TABLE:
                {
                    $tables = array();
                    $query = 'SHOW TABLES from `' . $this->DB .'`';
                    $result = mysqli_query( $this->DBConnection, $query );
                    $this->reportQuery( __CLASS__, $query, false, false );
                    while( $row = mysqli_fetch_row( $result ) )
                    {
                        $tables[] = $row[0];
                    }
                    mysqli_free_result( $result );
                    return $tables;
                } break;

                case self::RELATION_FOREIGN_KEY:
                {
                    /**
                     * Ideally, we would have queried information_schema.KEY_COLUMN_USAGE
                     * However, a known bug causes queries on this table to potentially be VERY slow (http://bugs.mysql.com/bug.php?id=19588)
                     *
                     * The query would look like this:
                     * SELECT table_name AS from_table, column_name AS from_column, referenced_table_name AS to_table,
                     *        referenced_column_name AS to_column
                     * FROM information_schema.KEY_COLUMN_USAGE
                     * WHERE REFERENCED_TABLE_SCHEMA = '{$this->DB}'
                     *   AND REFERENCED_TABLE_NAME is not null;
                     *
                     * Result as of MySQL 5.1.48 / August 2010:
                     *
                     * +---------------+-------------+----------+-----------+
                     * | from_table    | from_column | to_table | to_column |
                     * +---------------+-------------+----------+-----------+
                     * | ezdbfile_data | name_hash   | ezdbfile | name_hash |
                     * +---------------+-------------+----------+-----------+
                     * 1 row in set (12.56 sec)
                     *
                     * The only way out right now is to parse SHOW CREATE TABLE for each table and extract CONSTRAINT lines
                     */

                    $foreignKeys = array();
                    foreach( $this->relationList( eZDBInterface::RELATION_TABLE ) as $table )
                    {
                        $query = "SHOW CREATE TABLE $table";
                        $result = mysqli_query( $this->DBConnection, $query );
                        $this->reportQuery( __CLASS__, $query, false, false );
                        if ( mysqli_num_rows( $result ) === 1 )
                        {
                            $row = mysqli_fetch_row( $result );
                            if ( strpos( $row[1], "CONSTRAINT" ) !== false )
                            {
                                if ( preg_match_all( '#CONSTRAINT [`"]([^`"]+)[`"] FOREIGN KEY \([`"].*[`"]\) REFERENCES [`"]([^`"]+)[`"] \([`"].*[`"]\)#', $row[1], $matches, PREG_PATTERN_ORDER ) )
                                {
                                    // $foreignKeys[] = array( 'table' => $table, 'keys' => $matches[1] );
                                    foreach( $matches[1] as $fkMatch )
                                    {
                                        $foreignKeys[] = array( 'table' => $table, 'fk' => $fkMatch );
                                    }
                                }
                            }
                        }
                    }
                    return $foreignKeys;
                }
            }
        }
    }

    function eZTableList( $server = eZDBInterface::SERVER_MASTER )
    {
        $tables = array();
        if ( $this->IsConnected )
        {
            // always the primary: the tables a replica has may lag behind (6.0.15)
            $connection = $this->DBWriteConnection;
            $db = $this->DB;

            // Do not attempt to list tables if no database has been selected.
            // This can happen during the setup wizard before a database name is chosen.
            if ( !is_string( $db ) || trim( $db ) === '' )
                return $tables;

            $query = 'SHOW TABLES from `' . $db .'`';
            $result = mysqli_query( $connection, $query );
            $this->reportQuery( __CLASS__, $query, false, false );
            if ( !$result )
                return $tables;
            while( $row = mysqli_fetch_row( $result ) )
            {
                $tableName = $row[0];
                if ( substr( $tableName, 0, 2 ) == 'ez' )
                {
                    $tables[$tableName] = eZDBInterface::RELATION_TABLE;
                }
            }
            mysqli_free_result( $result );
        }
        return $tables;
    }

    function relationMatchRegexp( $relationType )
    {
        return "#^ez#";
    }

    function removeRelation( $relationName, $relationType )
    {
        $relationTypeName = $this->relationName( $relationType );
        if ( !$relationTypeName )
        {
            eZDebug::writeError( "Unknown relation type '$relationType'", __METHOD__ );
            return false;
        }

        if ( $this->IsConnected )
        {
            switch ( $relationType )
            {
                case self::RELATION_FOREIGN_KEY:
                {
                    $sql = "ALTER TABLE {$relationName['table']} DROP FOREIGN KEY {$relationName['fk']}";
                    $this->query( $sql );
                    return true;
                } break;

                default:
                {
                    $sql = "DROP $relationTypeName $relationName";
                    return $this->query( $sql );
                }
            }
        }
        return false;
    }

    /**
     * Local eZDBInterface::relationName() override to support the foreign keys type relation
     * @param $relationType
     * @return string|false
     */
    public function relationName( $relationType )
    {
        if ( $relationType == self::RELATION_FOREIGN_KEY )
            return 'FOREIGN_KEY';
        else
            return parent::relationName( $relationType );
    }

    function lock( $table )
    {
        if ( $this->IsConnected )
        {
            if ( is_array( $table ) )
            {
                $lockQuery = "LOCK TABLES";
                $first = true;
                foreach( array_keys( $table ) as $tableKey )
                {
                    if ( $first == true )
                        $first = false;
                    else
                        $lockQuery .= ",";
                    $lockQuery .= " " . $table[$tableKey]['table'] . " WRITE";
                }
                $this->query( $lockQuery );
            }
            else
            {
                $this->query( "LOCK TABLES $table WRITE" );
            }
        }
    }

    function unlock()
    {
        if ( $this->IsConnected )
        {
            $this->query( "UNLOCK TABLES" );
        }
    }

    /*!
     The query to start the transaction.
    */
    function beginQuery()
    {
        return $this->query("BEGIN WORK");
    }

    /*!
     The query to commit the transaction.
    */
    function commitQuery()
    {
        return $this->query( "COMMIT" );
    }

    /*!
     The query to cancel the transaction.
    */
    function rollbackQuery()
    {
        return mysqli_query( $this->DBWriteConnection, "ROLLBACK" );
    }

    /**
     * Returns the last serial ID generated with an auto increment field.
     *
     * @param string|bool $table
     * @param string|bool $column
     * @return int|bool The most recent value for the sequence
     */
    function lastSerialID( $table = false, $column = false )
    {
        if ( $this->IsConnected )
        {
            $id = mysqli_insert_id( $this->DBWriteConnection );
            return $id;
        }

        return false;
    }

    function escapeString( $str )
    {
        if ( $str === null )
	{
	    return '';
	}

        if ( $this->IsConnected )
        {
            return mysqli_real_escape_string( $this->DBConnection, $str );
        }
        else
        {
            eZDebug::writeDebug( 'escapeString called before connection is made', __METHOD__ );
            // mysqli_real_escape_string() needs a link (one argument threw ArgumentCountError on PHP 8). Use the
            // write connection when there is one, else escape the characters MySQL escapes itself, as the SQLite3
            // driver escapes without a connection.
            if ( $this->DBWriteConnection instanceof mysqli )
            {
                return mysqli_real_escape_string( $this->DBWriteConnection, (string)$str );
            }
            return self::escapeStringWithoutConnection( (string)$str );
        }
    }

    /**
     * Escapes $str as mysqli_real_escape_string() does for an ASCII compatible character set (utf8, utf8mb4, latin1),
     * for the case that no connection is open yet: NUL, \n, \r, backslash, both quotes and Ctrl-Z get a backslash.
     *
     * @param string $str
     * @return string
     */
    public static function escapeStringWithoutConnection( $str )
    {
        return strtr( (string)$str, array( "\\" => "\\\\", "\0" => "\\0", "\n" => "\\n", "\r" => "\\r",
                                           "'" => "\\'", '"' => '\\"', "\x1a" => "\\Z" ) );
    }

    function close()
    {
        if ( $this->ReplicaConnection instanceof mysqli )
        {
            try { @mysqli_close( $this->ReplicaConnection ); } catch ( Throwable $e ) {}
        }
        $this->ReplicaConnection = false;
        $this->ReplicaState = null;
        $this->ReplicaPinned = false;
        if ( $this->IsConnected )
        {
            mysqli_close( $this->DBWriteConnection );
            // closed: a second close() (the Velocity warm-up closes the parent's connection
            // before its workers fork) must not call mysqli_close() on a closed link, which throws
            $this->IsConnected = false;
        }
    }

    function createDatabase( $dbName )
    {
        if ( $this->IsConnected )
        {
            $this->query( "CREATE DATABASE $dbName" );
            $this->setError();
        }
    }

    function removeDatabase( $dbName )
    {
        if ( $this->IsConnected )
        {
            $this->query( "DROP DATABASE $dbName" );
            $this->setError();
        }
    }

    /**
     * Sets the internal error messages & number
     * @param MySQLi $connection database connection handle, overrides the current one if given
     */
    function setError( $connection = false )
    {
        if ( $this->IsConnected )
        {
            if ( $connection === false )
                $connection = $this->DBConnection;

            if ( $connection instanceof MySQLi )
            {
                $this->ErrorMessage = mysqli_error( $connection );
                $this->ErrorNumber = mysqli_errno( $connection );
            }
        }
        else
        {
            $this->ErrorMessage = mysqli_connect_error();
            $this->ErrorNumber = mysqli_connect_errno();
        }
    }

    function availableDatabases()
    {
        $databaseArray = mysqli_query( $this->DBConnection, 'SHOW DATABASES' );

        if ( !$databaseArray or $this->errorNumber() != 0 )
        {
            return null;
        }

        $databases = array();

        $numRows = mysqli_num_rows( $databaseArray );
        if ( $numRows == 0 )
        {
            return false;
        }

        while ( $row = mysqli_fetch_row( $databaseArray ) )
        {
            // we don't allow "mysql" or "information_schema" database to be shown anywhere
            $curDB = $row[0];
            if ( strcasecmp( $curDB, 'mysql' ) != 0 && strcasecmp( $curDB, 'information_schema' ) != 0 )
            {
                $databases[] = $curDB;
            }
        }
        return $databases;
    }

    function databaseServerVersion()
    {
        if ( $this->IsConnected )
        {
            $versionInfo = mysqli_get_server_info( $this->DBConnection );

            $versionArray = explode( '.', $versionInfo );

            return array( 'string' => $versionInfo,
                          'values' => $versionArray );
        }

        return false;
    }

    function databaseClientVersion()
    {
        $versionInfo = mysqli_get_client_info();

        $versionArray = explode( '.', $versionInfo );

        return array( 'string' => $versionInfo,
                      'values' => $versionArray );
    }

    function isCharsetSupported( $charset )
    {
        return true;
    }

    function supportsDefaultValuesInsertion()
    {
        return false;
    }

    /**
     * Whether a statement may run on a read replica: a plain SELECT that changes nothing and takes no lock. FOR
     * UPDATE/SHARE and LOCK IN SHARE MODE lock on the primary only, SELECT ... INTO writes a file or variables,
     * named locks, LAST_INSERT_ID() and the sequence functions belong to the primary's session. The test is on the
     * text, so a literal that only contains such a word also goes to the primary: a round trip, never correctness.
     *
     * @param string $sql
     * @return bool
     * @since 6.0.15
     */
    public static function isReplicaReadable( $sql )
    {
        $sql = ltrim( (string)$sql, " \t\r\n(" );
        if ( strncasecmp( $sql, 'select', 6 ) !== 0 || !preg_match( '/^select\b/i', $sql ) )
            return false;
        return !preg_match( '/\b(?:get_lock|release_lock|release_all_locks|is_free_lock|is_used_lock|last_insert_id'
                          . '|nextval|lastval|setval|master_pos_wait|source_pos_wait'
                          . '|master_gtid_wait|wait_for_executed_gtid_set|wait_until_sql_thread_after_gtids'
                          . '|load_file)\s*\(|\b(?:next|previous)\s+value\s+for\b|\bfor\s+(?:update|share)\b'
                          . '|\block\s+in\s+share\s+mode\b|\binto\b/i', $sql );
    }

    /**
     * What a replica's SHOW REPLICA STATUS row says: whether it may be read. A replica is read while both of its
     * threads run and it is at most $maxLag seconds behind (Seconds_Behind_Master, or _Source since MySQL 8.0.22; NULL
     * means it does not know, so no). No row: the server is no replica (a primary, or a node whose "replica" is the
     * primary itself), which is read as it is. False: the status could not be read (no REPLICATION CLIENT or REPLICA
     * MONITOR privilege) and is trusted only with SlaveServerStatusCheck=disabled.
     *
     * @param array|false $row
     * @param int $maxLag seconds
     * @return array( bool $usable, string $reason, bool $isReplica )
     * @since 6.0.15
     */
    public static function replicaStatusVerdict( $row, $maxLag )
    {
        if ( $row === false )
            return array( false, 'the replica status could not be read (grant REPLICATION CLIENT, on MariaDB REPLICA MONITOR, or set SlaveServerStatusCheck=disabled)', false );
        if ( !$row )
            return array( true, 'not a replica', false );
        $value = function( array $names ) use ( $row )
        {
            foreach ( $names as $name )
                if ( array_key_exists( $name, $row ) )
                    return $row[$name];
            return null;
        };
        $io = $value( array( 'Replica_IO_Running', 'Slave_IO_Running' ) );
        $sqlThread = $value( array( 'Replica_SQL_Running', 'Slave_SQL_Running' ) );
        $lag = $value( array( 'Seconds_Behind_Source', 'Seconds_Behind_Master' ) );
        if ( strcasecmp( (string)$io, 'Yes' ) !== 0 || strcasecmp( (string)$sqlThread, 'Yes' ) !== 0 )
            return array( false, "the replication threads do not run (IO {$io}, SQL {$sqlThread})", true );
        if ( $lag === null || $lag === '' || !is_numeric( $lag ) )
            return array( false, 'the replica does not know how far behind it is', true );
        if ( (int)$lag > (int)$maxLag )
            return array( false, "the replica is {$lag} s behind (SlaveServerMaxLag {$maxLag})", true );
        return array( true, "replica, {$lag} s behind", true );
    }

    /**
     * The statement that asks the replica whether it has applied the primary's GTID position (read at the start of
     * the request, before the replica): MariaDB waits up to $waitMs for it (MASTER_GTID_WAIT() answers 0 once it is
     * there, -1 after the wait), MySQL compares the sets at once (GTID_SUBSET() answers 1). Null without a position
     * or for one that is not a GTID position.
     *
     * @param bool $isMariaDB
     * @param string $position @@gtid_binlog_pos (MariaDB) or @@gtid_executed (MySQL) of the primary
     * @param int $waitMs
     * @return string|null
     * @since 6.0.15
     */
    public static function catchUpStatement( $isMariaDB, $position, $waitMs )
    {
        $position = trim( preg_replace( '/\s+/', '', (string)$position ) );
        if ( $position === '' || !preg_match( '/^[0-9A-Fa-f:,\-]+$/', $position ) )
            return null;
        if ( $isMariaDB )
            return "SELECT MASTER_GTID_WAIT('{$position}', " . sprintf( '%.3F', max( 0, (int)$waitMs ) / 1000 ) . ")";
        return "SELECT GTID_SUBSET('{$position}', @@GLOBAL.gtid_executed)";
    }

    /**
     * Whether the answer of catchUpStatement() says the replica has caught up.
     *
     * @param bool $isMariaDB
     * @param mixed $value
     * @return bool
     * @since 6.0.15
     */
    public static function catchUpVerdict( $isMariaDB, $value )
    {
        return $value !== null && $value !== false && (string)$value === ( $isMariaDB ? '0' : '1' );
    }

    /**
     * The replica connection for this statement, or false for the primary. The first statement of a request that
     * may use the replica decides for the whole request: the request must be allowed to (requestMayReadReplica()),
     * the replica must answer, both replication threads must run and it must be at most SlaveServerMaxLag seconds
     * behind (SlaveServerStatusCheck), and it must have applied what the primary had committed when the request
     * started (SlaveServerCatchUp, by GTID), so a visitor right after an edit sees it, on any node. A node without
     * a replica (nothing answers on the configured address) reads the primary, so one set of settings serves every
     * node of a cluster.
     *
     * @param string $sql
     * @param int|bool $server eZDBInterface::SERVER_MASTER forces the primary
     * @return mysqli|false
     * @since 6.0.15
     */
    protected function replicaConnectionFor( $sql, $server )
    {
        if ( !$this->UseSlaveServer || $this->ReplicaPinned || $this->ReplicaState === 'off'
             || $this->TransactionCounter > 0 || $server === eZDBInterface::SERVER_MASTER
             || !static::isReplicaReadable( $sql ) )
            return false;
        if ( $this->ReplicaState === null )
        {
            $this->ReplicaState = 'off';
            if ( !$this->SlaveServer || !self::requestMayReadReplica( $_SERVER, $_COOKIE, self::isReplicaWebProcess() ) )
                return false;
            // the primary's position first: whatever it had committed before this request, the replica must have
            $catchUp = $this->replicaSetting( 'SlaveServerCatchUp', 'enabled' ) !== 'disabled';
            $position = $catchUp ? $this->primaryGtidPosition() : '';
            $kept = (bool)$this->ReplicaConnection;
            if ( !$kept )
                $this->ReplicaConnection = $this->connectReplica();
            if ( !$this->ReplicaConnection )
                return false;
            $current = $this->replicaIsCurrent( $position, $failed );
            if ( !$current && $failed && $kept )
            {
                // a connection kept from an earlier request (persistent, a Velocity worker) that broke meanwhile, e.g.
                // the replica was restarted: once more on a new one
                $this->dropReplica( 'the kept replica connection failed' );
                $this->ReplicaConnection = $this->connectReplica();
                if ( !$this->ReplicaConnection )
                    return false;
                $current = $this->replicaIsCurrent( $position, $failed );
            }
            if ( !$current )
            {
                if ( $failed )
                    $this->dropReplica( 'a check failed on the replica' );
                $this->ReplicaState = 'off';
                return false;
            }
            $this->ReplicaState = 'on';
        }
        return $this->ReplicaConnection;
    }

    /**
     * Whether the open replica may be read now: its status (SlaveServerStatusCheck, SlaveServerMaxLag) and, for a
     * replica, the primary's GTID position $position (SlaveServerCatchUp).
     *
     * @param string $position
     * @param bool $failed set to whether a statement failed on the replica (rather than its answer saying no)
     * @return bool
     */
    protected function replicaIsCurrent( $position, &$failed = false )
    {
        $failed = false;
        $isReplica = true;
        if ( $this->replicaSetting( 'SlaveServerStatusCheck', 'enabled' ) !== 'disabled' )
        {
            $row = $this->replicaRawRow( 'SHOW REPLICA STATUS', $errno );
            if ( $row === false && $errno == 1064 )
                $row = $this->replicaRawRow( 'SHOW SLAVE STATUS', $errno );   // MySQL before 8.0.22, MariaDB before 10.5.1
            // no privilege (1227) is an answer; anything else that failed is the connection's
            if ( $row === false && $errno != 1227 && $errno != 1045 && $errno != 1142 )
                $failed = true;
            list( $usable, $reason, $isReplica ) = self::replicaStatusVerdict( $row, (int)$this->replicaSetting( 'SlaveServerMaxLag', 0 ) );
            if ( !$usable )
            {
                eZDebug::writeWarning( "The read replica {$this->SlaveServer}:{$this->SlavePort} is not read: {$reason}; reading from the primary", __METHOD__ );
                return false;
            }
        }
        if ( $isReplica && $position !== '' )
        {
            $isMariaDB = $this->isMariaDB();
            $statement = self::catchUpStatement( $isMariaDB, $position, (int)$this->replicaSetting( 'SlaveServerCatchUpWait', 100 ) );
            if ( $statement !== null )
            {
                $row = $this->replicaRawRow( $statement, $errno, MYSQLI_NUM );
                if ( $row === false )
                    $failed = true;
                if ( !$row || !self::catchUpVerdict( $isMariaDB, $row[0] ) )
                {
                    eZDebugSetting::writeDebug( 'kernel-db-mysql', "The read replica {$this->SlaveServer}:{$this->SlavePort} has not applied the primary's position yet, reading from the primary", __METHOD__ );
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * The primary's GTID position: @@gtid_binlog_pos on MariaDB, @@gtid_executed on MySQL; '' without one (no
     * binary log, gtid_mode OFF), then only the replica's lag decides.
     *
     * @return string
     */
    protected function primaryGtidPosition()
    {
        $variable = $this->isMariaDB() ? '@@GLOBAL.gtid_binlog_pos' : '@@GLOBAL.gtid_executed';
        $position = '';
        $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        try {
            $result = mysqli_query( $this->DBWriteConnection, "SELECT {$variable}" );
            if ( $result )
            {
                $row = mysqli_fetch_row( $result );
                mysqli_free_result( $result );
                $position = $row && $row[0] !== null ? (string)$row[0] : '';
            }
        } catch ( Throwable $e ) {
            $position = '';
        }
        eZDebug::setHandleType( $oldHandling );
        return $position;
    }

    /**
     * Whether the primary is MariaDB (its GTID functions differ from MySQL's).
     *
     * @return bool
     */
    protected function isMariaDB()
    {
        if ( $this->ServerIsMariaDB === null )
            $this->ServerIsMariaDB = $this->DBWriteConnection instanceof mysqli
                                     && stripos( (string)@mysqli_get_server_info( $this->DBWriteConnection ), 'mariadb' ) !== false;
        return $this->ServerIsMariaDB;
    }

    /**
     * Opens the replica connection: once, no retries, a short timeout, the primary's character set (a replica on
     * another one would hand out transliterated text, so it is not used then).
     *
     * @return mysqli|false
     */
    protected function connectReplica()
    {
        $mappedCharset = null;
        if ( $this->Charset !== null )
            $mappedCharset = eZMySQLCharset::mapTo( eZCharsetInfo::realCharsetCode( $this->Charset ) );
        $server = ( $this->UsePersistentConnection ? 'p:' : '' ) . $this->SlaveServer;
        $database = ( is_string( $this->SlaveDB ) && $this->SlaveDB !== '' ) ? $this->SlaveDB : $this->DB;
        $connection = false;
        $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        try {
            $link = mysqli_init();
            mysqli_options( $link, MYSQLI_OPT_CONNECT_TIMEOUT, 2 );
            if ( @mysqli_real_connect( $link, $server, $this->SlaveUser, $this->SlavePassword, $database,
                                       (int)$this->SlavePort, $this->SocketPath !== false ? $this->SocketPath : null ) )
            {
                $connection = $link;
                if ( $mappedCharset !== null )
                {
                    @mysqli_set_charset( $connection, $mappedCharset );
                    if ( @mysqli_character_set_name( $connection ) !== $mappedCharset )
                    {
                        @mysqli_close( $connection );
                        $connection = false;
                        eZDebug::writeWarning( "The read replica {$this->SlaveServer}:{$this->SlavePort} cannot use the character set {$mappedCharset}, reading from the primary", __METHOD__ );
                    }
                }
            }
        } catch ( Throwable $e ) {
            $connection = false;
        }
        eZDebug::setHandleType( $oldHandling );
        if ( !$connection )
            eZDebugSetting::writeDebug( 'kernel-db-mysql', "No read replica at {$this->SlaveServer}:{$this->SlavePort}, reading from the primary", __METHOD__ );
        return $connection;
    }

    /**
     * Runs a SELECT on the replica. On any failure the replica is not used again in this request (its connection is
     * dropped when the failure was the connection's) and false is returned, so the caller runs it on the primary.
     *
     * @param mysqli $replica
     * @param string $sql
     * @return mysqli_result|bool
     */
    protected function replicaQuery( $replica, $sql )
    {
        $errno = 0;
        $error = '';
        $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        try {
            $result = mysqli_query( $replica, $sql );
            if ( $result === false )
            {
                $errno = mysqli_errno( $replica );
                $error = mysqli_error( $replica );
            }
        } catch ( Throwable $e ) {
            $result = false;
            $errno = (int)$e->getCode();
            $error = $e->getMessage();
        }
        eZDebug::setHandleType( $oldHandling );
        if ( $result === false )
        {
            $this->ReplicaState = 'off';
            eZDebug::writeWarning( "A statement failed on the read replica and runs on the primary: ({$errno}) {$error}", __METHOD__ );
            // whatever failed, a new connection next request: a server that went away does not always say so
            // with a client error (MariaDB answers a shutdown with 1053, a killed connection with 1927)
            $this->dropReplica( 'a statement failed on the replica' );
        }
        return $result;
    }

    /**
     * The first row of a statement on the replica, without the fallback of replicaQuery(): an empty array for no
     * row, false on an error ($errno says which).
     *
     * @param string $sql
     * @param int $errno
     * @param int $mode MYSQLI_ASSOC or MYSQLI_NUM
     * @return array|false
     */
    protected function replicaRawRow( $sql, &$errno, $mode = MYSQLI_ASSOC )
    {
        $errno = 0;
        $row = false;
        $oldHandling = eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        try {
            $result = mysqli_query( $this->ReplicaConnection, $sql );
            if ( $result instanceof mysqli_result )
            {
                $row = mysqli_fetch_array( $result, $mode );
                $row = is_array( $row ) ? $row : array();
                mysqli_free_result( $result );
            }
            else
                $errno = mysqli_errno( $this->ReplicaConnection );
        } catch ( Throwable $e ) {
            $row = false;
            $errno = (int)$e->getCode();
        }
        eZDebug::setHandleType( $oldHandling );
        return $row;
    }

    /**
     * Closes the replica connection for the rest of this request.
     *
     * @param string $reason
     * @return bool false
     */
    protected function dropReplica( $reason )
    {
        if ( $this->ReplicaConnection instanceof mysqli )
        {
            try { @mysqli_close( $this->ReplicaConnection ); } catch ( Throwable $e ) {}
        }
        $this->ReplicaConnection = false;
        $this->ReplicaState = 'off';
        eZDebugSetting::writeDebug( 'kernel-db-mysql', "Read replica {$this->SlaveServer}:{$this->SlavePort}: {$reason}, reading from the primary", __METHOD__ );
        return false;
    }

    /**
     * The reset hook of the read replica: eZDBInterface::resetReplicaRequest() (expKernelServiceScope 'dbreplica').
     *
     * @since 6.0.15
     */
    public static function resetRequest()
    {
        eZDBInterface::resetReplicaRequest();
    }

    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        $dropTableQuery = str_ireplace( 'DROP TABLE', 'DROP TEMPORARY TABLE', $dropTableQuery );
        parent::dropTempTable( $dropTableQuery, $server );
    }

    protected $TempTableList;

    /** @var bool|null whether the primary is MariaDB (isMariaDB()), null until asked */
    public $ServerIsMariaDB = null;

    /// \privatesection
}

?>
