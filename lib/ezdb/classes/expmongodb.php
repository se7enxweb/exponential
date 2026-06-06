<?php
/**
 * File containing the expMongoDB class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package sevenx_mongodb
 */

// NOTE: This is the ACTIVE file loaded by var/autoload/ezp_override.php
// Do NOT edit sevenx_mongodb/classes/expmongodb.php (root copy - never loaded)

 use Exception as Exception;
 use MongoDB\Client;

class expMongoDB extends eZDBInterface
{
    var $databaseName = "";

    /** @var string Last error message (cleared on each successful operation) */
    public $ErrorMessage = '';

    /** @var int Last error number (0 = no error) */
    public $ErrorNumber = 0;

    /** @var bool Whether to record errors into ErrorMessage/ErrorNumber */
    public $RecordError = true;

    /** @var bool Persistent connections flag (no-op for MongoDB) */
    public $UsePersistentConnection = false;

    public function __construct( $parameters = array() )
    {
        parent::__construct( $parameters );
        try {
            // Test connection by pinging the server
            $this->getClient()->selectDatabase( $this->DB )->command(['ping' => 1]);
            $this->IsConnected = true;
        } catch ( Exception $e ) {
            error_log('expMongoDB::__construct connection failed: ' . $e->getMessage());
            $this->IsConnected = false;
        }
    }

    function databaseName()
    {
        return 'mongo';
    }

    public function getClient()
    {
        static $client = null;
        if ( $client === null ) {
            $server = $this->Server ?: 'localhost';
            $port   = $this->Port   ?: 27017;
            $user   = rawurlencode( $this->User );
            $pass   = rawurlencode( $this->Password );
            $dbName = $this->DB;
            $uri = 'mongodb://' . $user . ':' . $pass . '@' . $server . ':' . $port . '/' . $dbName;
            $client = new MongoDB\Client($uri);
        }
        return $client;
    }

    function findOne( $table, $condition, $server = false )
    {
        $dbName = $this->DB;
        $results = false;
        try {
            $filter = $this->translateConditions( is_array($condition) ? $condition : [] );
            $doc = $this->getClient()->selectCollection( $dbName, $table )->findOne($filter);
            $results = $doc === null ? [] : $doc->getArrayCopy();
        } catch (Exception $e) {
            error_log('expMongoDB::findOne ' . $e->getMessage());
        }
        return $results;
    }

    /**
     * Parse a SQL WHERE clause (ANDs only, no ORs) into a MongoDB filter array.
     * Handles: field=N, field='str', field!=N, field<>N, field LIKE 'pfx%', field IN (...)
     */
    private function parseWhereClause( $whereSql )
    {
        $filter = [];
        // Split on AND boundaries (not inside quotes or parens)
        $terms = preg_split( '/\bAND\b/i', $whereSql );
        foreach ( $terms as $term )
        {
            $term = trim( $term );
            // field IN (v1, v2, ...)
            if ( preg_match( '/^([\w.]+)\s+IN\s*\(([^)]+)\)/i', $term, $m ) )
            {
                $col  = trim( $m[1] );
                $vals = array_map( 'trim', explode( ',', $m[2] ) );
                $typedVals = [];
                foreach ( $vals as $v )
                {
                    if ( preg_match( "/^'(.*)'$/s", $v, $sv ) )
                        $typedVals[] = $sv[1];
                    else
                        $typedVals[] = (int) $v;
                }
                $filter[$col] = [ '$in' => $typedVals ];
            }            // field NOT LIKE 'prefix%' (trailing wildcard only)
            elseif ( preg_match( "/^([\\w.]+)\\s+NOT\\s+LIKE\\s+'([^%']*)(%)'/i", $term, $m ) )
            {
                $filter[trim($m[1])] = [ '$not' => new MongoDB\BSON\Regex( '^' . preg_quote( $m[2], '/' ) ) ];
            }            // field LIKE 'prefix%' (trailing wildcard only)
            elseif ( preg_match( "/^([\w.]+)\s+LIKE\s+'([^%']*)(%)'/i", $term, $m ) )
            {
                $filter[trim($m[1])] = [ '$regex' => '^' . preg_quote( $m[2], '/' ) ];
            }
            // field != N  or  field <> N
            elseif ( preg_match( '/^([\w.]+)\s*(?:!=|<>)\s*(-?\d+)$/i', $term, $m ) )
            {
                $filter[trim($m[1])] = [ '$ne' => (int) $m[2] ];
            }
            // field = N (integer)
            elseif ( preg_match( '/^([\w.]+)\s*=\s*(-?\d+)$/i', $term, $m ) )
            {
                $filter[trim($m[1])] = (int) $m[2];
            }
            // field = 'string'
            elseif ( preg_match( "/^([\w.]+)\s*=\s*'(.*)'$/s", $term, $m ) )
            {
                $val = stripslashes( $m[2] );
                // If the string value is purely numeric, cast to int for MongoDB int fields
                $filter[trim($m[1])] = ctype_digit( $val ) ? (int) $val : $val;
            }
        }
        return $filter;
    }

    function query( $sql, $server = false )
    {
        $dbName = $this->DB;
        $sql = trim( $sql );

        // --- UPDATE table SET col=val [, col=val ...] WHERE conditions ---
        if ( preg_match( '/^\s*UPDATE\s+([\w]+)\s+SET\s+(.+?)\s+WHERE\s+(.+)$/is', $sql, $m ) )
        {
            $table    = trim( $m[1] );
            $setClause = trim( $m[2] );
            $whereSql  = trim( $m[3] );

            // Parse SET clause into individual assignments (handles simple col=val and col=col+expr)
            $setFields = [];
            // Split on commas that are not inside parentheses (depth-tracking to handle nested parens)
            $parts = [];
            $depth = 0;
            $current = '';
            for ( $i = 0; $i < strlen( $setClause ); $i++ )
            {
                $c = $setClause[$i];
                if ( $c === '(' ) $depth++;
                elseif ( $c === ')' ) $depth--;
                if ( $c === ',' && $depth === 0 )
                {
                    $parts[] = $current;
                    $current = '';
                }
                else
                {
                    $current .= $c;
                }
            }
            if ( $current !== '' ) $parts[] = $current;
            foreach ( $parts as $part )
            {
                $part = trim( $part );
                if ( preg_match( '/^([\w]+)\s*=\s*(.+)$/s', $part, $fm ) )
                {
                    $col = trim( $fm[1] );
                    $val = trim( $fm[2] );
                    // Integer literal
                    if ( preg_match( '/^-?\d+$/', $val ) )
                    {
                        $setFields[$col] = (int) $val;
                    }
                    // depth = depth +/- N [+/- M ...] — evaluate the arithmetic delta
                    elseif ( preg_match( '/^' . $col . '\s*([\+\-\d\s]+)$/i', $val, $dm ) )
                    {
                        // Extract and evaluate: e.g. " + 3 - 2 + 1" => 2
                        $expr = $dm[1];
                        preg_match_all( '/([\+\-])\s*(\d+)/', $expr, $terms, PREG_SET_ORDER );
                        $delta = 0;
                        foreach ( $terms as $term )
                            $delta += ( $term[1] === '+' ? 1 : -1 ) * (int) $term[2];
                        $setFields[$col] = [ '$inc' => $delta ];
                    }
                    // String literal 'value'
                    elseif ( preg_match( "/^'(.*)'$/s", $val, $sm ) )
                    {
                        $setFields[$col] = stripslashes( $sm[1] );
                    }
                    // CONCAT( expr, expr ) — for path_string / path_identification_string rebuild
                    elseif ( preg_match( '/^CONCAT\s*\((.+)\)$/is', $val, $cm ) )
                    {
                        // Store as a special marker; handled per-document below
                        $setFields[$col] = [ '__concat__' => $cm[1] ];
                    }
                    else
                    {
                        $setFields[$col] = $val; // fallback: raw string
                    }
                }
            }

            // Parse WHERE clause using general-purpose parser
            $filter = $this->parseWhereClause( $whereSql );

            $collection = $this->getClient()->selectCollection( $dbName, $table );

            // Check for __concat__ fields — need per-document update
            $hasConcatField = false;
            foreach ( $setFields as $col => $val )
            {
                if ( is_array( $val ) && isset( $val['__concat__'] ) )
                {
                    $hasConcatField = true;
                    break;
                }
            }

            try
            {
                if ( $hasConcatField )
                {
                    // Per-document update needed for CONCAT expressions
                    $docs = $collection->find( $filter );
                    foreach ( $docs as $doc )
                    {
                        $docUpdate = [];
                        foreach ( $setFields as $col => $fieldVal )
                        {
                            if ( is_array( $fieldVal ) && isset( $fieldVal['__concat__'] ) )
                            {
                                // Evaluate CONCAT: replace old path prefix with new path prefix
                                // Pattern: CONCAT('newPrefix', SUBSTRING(field, offset))
                                $concatExpr = $fieldVal['__concat__'];
                                // Handle both SUBSTRING(col, N) and substring( col from N ) syntax
                                if ( preg_match( "/^'([^']*)'\s*,\s*(?:SUBSTRING|SUBSTR)\s*\(\s*[\w]+\s*(?:,|FROM)\s*(\d+)\s*\)/i", $concatExpr, $concatM )
                                     || preg_match( "/^'([^']*)'\s*,\s*substring\s*\(\s*[\w]+\s+from\s+(\d+)\s*(?:for\s+\d+\s*)?\)/i", $concatExpr, $concatM ) )
                                {
                                    $newPrefix = $concatM[1];
                                    $offset    = (int) $concatM[2] - 1; // SQL SUBSTRING is 1-based
                                    $oldVal    = isset( $doc[$col] ) ? (string) $doc[$col] : '';
                                    $docUpdate[$col] = $newPrefix . substr( $oldVal, $offset );
                                }
                            }
                            elseif ( is_array( $fieldVal ) && isset( $fieldVal['$inc'] ) )
                            {
                                $docUpdate[$col] = ( (int) $doc[$col] ) + $fieldVal['$inc'];
                            }
                            elseif ( is_array( $fieldVal ) && isset( $fieldVal['$dec'] ) )
                            {
                                $docUpdate[$col] = ( (int) $doc[$col] ) - $fieldVal['$dec'];
                            }
                            else
                            {
                                $docUpdate[$col] = $fieldVal;
                            }
                        }
                        if ( $docUpdate )
                            $collection->updateOne( [ '_id' => $doc['_id'] ], [ '$set' => $docUpdate ] );
                    }
                }
                else
                {
                    // Build standard $set / $inc updateMany
                    $setOp = [];
                    $incOp = [];
                    foreach ( $setFields as $col => $fieldVal )
                    {
                        if ( is_array( $fieldVal ) && isset( $fieldVal['$inc'] ) )
                            $incOp[$col] = $fieldVal['$inc'];
                        elseif ( is_array( $fieldVal ) && isset( $fieldVal['$dec'] ) )
                            $incOp[$col] = -$fieldVal['$dec'];
                        else
                            $setOp[$col] = $fieldVal;
                    }
                    $updateDoc = [];
                    if ( $setOp ) $updateDoc['$set'] = $setOp;
                    if ( $incOp ) $updateDoc['$inc'] = $incOp;
                    if ( $updateDoc )
                        $collection->updateMany( $filter, $updateDoc );
                }
            }
            catch ( Exception $e )
            {
                error_log( 'expMongoDB::query UPDATE failed: ' . $e->getMessage() . ' SQL: ' . substr( $sql, 0, 200 ) );
                return false;
            }
            return true;
        }

        // --- DELETE FROM table WHERE conditions ---
        if ( preg_match( '/^\s*DELETE\s+FROM\s+([\w]+)\s+WHERE\s+(.+)$/is', $sql, $m ) )
        {
            $table    = trim( $m[1] );
            $whereSql = trim( $m[2] );
            $filter = $this->parseWhereClause( $whereSql );
            if ( empty( $filter ) )
            {
                error_log( 'expMongoDB::query DELETE with unparseable WHERE: ' . substr( $sql, 0, 200 ) );
                return false;
            }
            try
            {
                $this->getClient()->selectCollection( $dbName, $table )->deleteMany( $filter );
            }
            catch ( Exception $e )
            {
                error_log( 'expMongoDB::query DELETE failed: ' . $e->getMessage() );
                return false;
            }
            return true;
        }

        error_log( 'expMongoDB::query unhandled SQL: ' . substr( $sql, 0, 300 ) );
        return false;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $dbName = $this->DB;
        $sql = trim( $sql );

        // --- SELECT col[, col...] FROM single_table WHERE conditions [ORDER BY ...] [LIMIT n] ---
        // Only handle single-table, no JOINs, no sub-queries
        if ( preg_match( '/^\s*SELECT\s+(.+?)\s+FROM\s+([\w]+)\s*(?:WHERE\s+(.+?))?(?:\s+ORDER\s+BY\s+.+?)?(?:\s+LIMIT\s+\d+)?\s*$/is', $sql, $m )
             && strpos( $m[2], ',' ) === false )   // single table only
        {
            $selectClause = trim( $m[1] );
            $table        = trim( $m[2] );
            $whereSql     = isset( $m[3] ) ? trim( $m[3] ) : '';

            // Build projection from SELECT list (skip * and COUNT(*))
            $projection = [ '_id' => 0 ];
            if ( $selectClause !== '*' && stripos( $selectClause, 'COUNT(' ) === false )
            {
                foreach ( preg_split( '/\s*,\s*/', $selectClause ) as $col )
                {
                    $col = trim( $col );
                    // strip table.col prefix if present
                    if ( strpos( $col, '.' ) !== false )
                        $col = substr( $col, strrpos( $col, '.' ) + 1 );
                    if ( $col !== '' )
                        $projection[$col] = 1;
                }
            }
            else
            {
                $projection = []; // no projection = all fields
            }

            $filter = $whereSql !== '' ? $this->parseWhereClause( $whereSql ) : [];

            // Pagination from $params
            $options = [];
            if ( !empty( $projection ) )
                $options['projection'] = $projection;
            if ( isset( $params['limit'] ) && $params['limit'] > 0 )
                $options['limit'] = (int) $params['limit'];
            if ( isset( $params['offset'] ) && $params['offset'] > 0 )
                $options['skip'] = (int) $params['offset'];

            $result = [];
            try {
                $cursor = $this->getClient()->selectCollection( $dbName, $table )->find( $filter, $options );
                foreach ( $cursor as $doc )
                    $result[] = $doc->getArrayCopy();
            } catch ( Exception $e ) {
                error_log( 'expMongoDB::arrayQuery SELECT failed: ' . $e->getMessage() . ' SQL: ' . substr( $sql, 0, 200 ) );
            }
            return $result;
        }

        // Cannot translate arbitrary multi-table SQL to MongoDB. Log caller info.
        $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 5 );
        // Find the first frame outside this file
        $caller = '';
        foreach ( $trace as $frame )
        {
            $file = isset( $frame['file'] ) ? $frame['file'] : '';
            if ( strpos( $file, 'expmongodb.php' ) !== false ) continue;
            $file  = str_replace( '/web/vh/alpha.se7enx.com/doc/mongodb.alpha.se7enx.com/', '', $file );
            $line  = isset( $frame['line'] ) ? $frame['line'] : '?';
            $func  = ( isset( $frame['class'] ) ? $frame['class'] . '::' : '' ) . ( isset( $frame['function'] ) ? $frame['function'] : '' );
            $caller = "$file:$line ($func)";
            break;
        }
        // Extract tables referenced in the SQL for a concise label
        $tables = '';
        if ( preg_match( '/\bFROM\s+([\w, ]+?)(?:\s+WHERE|\s+ORDER|\s+GROUP|\s+LIMIT|$)/is', $sql, $m ) )
            $tables = trim( preg_replace( '/\s+/', ' ', $m[1] ) );
        $label = "MONGO TODO arrayQuery: tables=[$tables] caller=$caller";
        eZDebug::writeWarning( $label, 'expMongoDB' );
        if ( $this->OutputSQL )
        {
            eZDebug::accumulatorStart( 'mongodb_query', 'MongoDB Total', 'MongoDB queries' );
            $this->startTimer();
        }
        $result = [];
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            $this->reportQuery( __CLASS__, $label, 0, $this->timeTaken() );
            eZDebug::accumulatorStop( 'mongodb_query' );
        }
        return $result;
    }

    function aggregate( $table, $pipeline = [] )
    {
        $dbName = $this->DB;
        $results = [];
        if ( $this->OutputSQL )
        {
            eZDebug::accumulatorStart( 'mongodb_query', 'MongoDB Total', 'MongoDB queries' );
            $this->startTimer();
        }
        try {
            $cursor = $this->getClient()->selectCollection( $dbName, $table )->aggregate($pipeline);
            foreach ( $cursor as $doc ) {
                $results[] = $doc->getArrayCopy();
            }
        } catch (Exception $e) {
            error_log('expMongoDB::aggregate ' . $e->getMessage());
        }
        if ( $this->OutputSQL )
        {
            $this->endTimer();
            $matchStage = isset( $pipeline[0]['$match'] ) ? json_encode( $pipeline[0]['$match'] ) : '...';
            $this->reportQuery( __CLASS__, "aggregate($table) " . $matchStage, count( $results ), $this->timeTaken() );
            eZDebug::accumulatorStop( 'mongodb_query' );
        }
        return $results;
    }

    function find( $table, $conds, $projection = [] )
    {
        $dbName = $this->DB;
        $results = false;
        $options = [];
        if ( !empty( $projection ) )
            $options['projection'] = $projection;
        try {
            $filter = $this->translateConditions( $conds );
            $cursor = $this->getClient()->selectCollection( $dbName, $table )->find( $filter, $options );
            $results = [];
            foreach ( $cursor as $doc ) {
                $results[] = $doc->getArrayCopy();
            }
        } catch (Exception $e) {
            error_log('expMongoDB::find ' . $e->getMessage());
        }
        return $results;
    }

    /**
     * Translates eZPersistentObject condition array to a MongoDB filter array.
     *   'field' => scalar                  -> exact match
     *   'field' => ['>', value]            -> $gt / $gte / $lt / $lte / $ne
     *   'field' => ['like', '%val%']       -> $regex
     *   'field' => [false, [start, end]]   -> $gte/$lte range
     *   'field' => [[1,5,7]]               -> $in
     */
    public function translateConditions( $conds )
    {
        if ( empty( $conds ) )
            return [];

        $opMap = [
            '='  => null,
            '!=' => '$ne',
            '>'  => '$gt',
            '>=' => '$gte',
            '<'  => '$lt',
            '<=' => '$lte',
        ];

        $filter = [];
        foreach ( $conds as $field => $value )
        {
            if ( !is_array( $value ) )
            {
                $filter[$field] = $value;
            }
            elseif ( isset( $value[0] ) && $value[0] === false && isset( $value[1] ) && is_array( $value[1] ) )
            {
                $filter[$field] = [ '$gte' => $value[1][0], '$lte' => $value[1][1] ];
            }
            elseif ( isset( $value[0] ) && is_array( $value[0] ) )
            {
                $filter[$field] = [ '$in' => $value[0] ];
            }
            elseif ( isset( $value[0] ) && is_string( $value[0] ) && array_key_exists( $value[0], $opMap ) )
            {
                $op = $opMap[$value[0]];
                $filter[$field] = $op === null ? $value[1] : [ $op => $value[1] ];
            }
            elseif ( isset( $value[0] ) && strtolower( $value[0] ) === 'like' )
            {
                $pattern = preg_quote( trim( $value[1], '%' ), '/' );
                $filter[$field] = [ '$regex' => $pattern, '$options' => 'i' ];
            }
            else
            {
                $filter[$field] = $value;
            }
        }
        return $filter;
    }

    /**
     * Insert a document into $table. If $doc does not contain the increment_key
     * field (or it is null/0), a new sequential ID is generated via nextSeqID().
     * Returns the inserted ID on success, false on failure.
     */
    function insert( $table, $doc )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->insertOne( $doc );
            return true;
        } catch ( Exception $e ) {
            error_log( 'expMongoDB::insert ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Upsert: update matching $filter with $doc fields; insert if no match.
     */
    function upsert( $table, $filter, $doc )
    {
        $dbName = $this->DB;
        try {
            // Remove key fields from the $set payload to avoid immutable field errors
            $setDoc = array_diff_key( $doc, $filter );
            $this->getClient()->selectCollection( $dbName, $table )->updateOne(
                $filter,
                [ '$set' => $setDoc ],
                [ 'upsert' => true ]
            );
            return true;
        } catch ( Exception $e ) {
            error_log( 'expMongoDB::upsert ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Return the next available sequential integer for $column in $table.
     * Uses a MongoDB $max aggregate to find the current maximum, then adds 1.
     */
    function nextSeqID( $table, $column )
    {
        $dbName = $this->DB;
        try {
            $cursor = $this->getClient()->selectCollection( $dbName, $table )->aggregate( [
                [ '$group' => [ '_id' => null, 'maxVal' => [ '$max' => '$' . $column ] ] ],
            ] );
            $rows = iterator_to_array( $cursor );
            $max  = ( !empty( $rows ) && isset( $rows[0]['maxVal'] ) ) ? (int) $rows[0]['maxVal'] : 0;
            return $max + 1;
        } catch ( Exception $e ) {
            error_log( 'expMongoDB::nextSeqID ' . $table . '.' . $column . ' ' . $e->getMessage() );
            return 1;
        }
    }

    function lock( $table ) {}
    function unlock() {}

    /**
     * No-op: MongoDB does not require an explicit BEGIN statement.
     * The base class eZDBInterface::begin() manages the transaction counter
     * and calls this method when it actually needs to start a transaction.
     */
    function beginQuery()
    {
        return true;
    }

    /**
     * No-op: MongoDB auto-commits single operations.
     */
    function commitQuery()
    {
        return true;
    }

    /**
     * No-op: nothing to roll back in auto-commit mode.
     */
    function rollbackQuery()
    {
        return true;
    }

    function deleteWhere( $table, $filter )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->deleteMany( $filter );
            return true;
        } catch ( Exception $e ) {
            error_log( 'expMongoDB::deleteWhere ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    function eZTableList( $server = eZDBInterface::SERVER_MASTER )
    {
        $tables = array();
        foreach ( $this->listCollectionNames() as $name )
        {
            if ( strncmp( $name, 'ez', 2 ) === 0 )
                $tables[$name] = eZDBInterface::RELATION_TABLE;
        }
        return $tables;
    }

    /**
     * Returns a plain array of collection names present in the MongoDB database.
     * Used by setup/systemupgrade.php to validate expected collections exist.
     */
    function listCollectionNames()
    {
        $dbName = $this->DB;
        $names  = [];
        try {
            foreach ( $this->getClient()->selectDatabase( $dbName )->listCollectionNames() as $name )
                $names[] = $name;
        } catch ( Exception $e ) {
            error_log( 'expMongoDB::listCollectionNames ' . $e->getMessage() );
        }
        return $names;
    }

    function lastSerialID( $table = false, $column = false )
    {
        return $this->_lastInsertedID;
    }

    private $_lastInsertedID = false;

    /**
     * Atomically increment and return a sequential integer counter for $counterName.
     * Uses a dedicated 'ezsequence' collection with {_id: $counterName, seq: N}.
     * Seeds from the current max of $seedTable.$seedColumn on first use.
     */
    function nextAtomicID( $counterName, $seedTable = null, $seedColumn = 'id' )
    {
        $dbName = $this->DB;
        try {
            $col = $this->getClient()->selectCollection( $dbName, 'ezsequence' );
            // If counter does not exist yet, seed it from the current max in the real collection
            $existing = $col->findOne( [ '_id' => $counterName ] );
            if ( $existing === null && $seedTable !== null ) {
                $cursor = $this->getClient()->selectCollection( $dbName, $seedTable )->aggregate( [
                    [ '$group' => [ '_id' => null, 'maxVal' => [ '$max' => '$' . $seedColumn ] ] ],
                ] );
                $rows   = iterator_to_array( $cursor );
                $seed   = ( !empty( $rows ) && isset( $rows[0]['maxVal'] ) ) ? (int)$rows[0]['maxVal'] : 0;
                $col->updateOne(
                    [ '_id' => $counterName ],
                    [ '$setOnInsert' => [ 'seq' => $seed ] ],
                    [ 'upsert' => true ]
                );
            }
            $result = $col->findOneAndUpdate(
                [ '_id' => $counterName ],
                [ '$inc' => [ 'seq' => 1 ] ],
                [ 'upsert' => true, 'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER ]
            );
            return (int)( $result['seq'] ?? 1 );
        } catch ( \Exception $e ) {
            error_log( 'expMongoDB::nextAtomicID ' . $counterName . ' ' . $e->getMessage() );
            // Fallback: read max from actual table
            return $this->nextSeqID( $seedTable ?? $counterName, $seedColumn );
        }
    }

    function escapeString( $str )
    {
        // MongoDB queries use BSON — no SQL injection vector, but sanitise nulls
        return $str === null ? '' : (string)$str;
    }

    /**
     * Clears the error state. Called by the base class before every operation.
     */
    function setError( $connection = false )
    {
        $this->ErrorMessage = '';
        $this->ErrorNumber  = 0;
    }

    // -------------------------------------------------------------------------
    // Binding — MongoDB uses native PHP types, no binding needed
    // -------------------------------------------------------------------------

    function bindingType()
    {
        return eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    // -------------------------------------------------------------------------
    // Charset — MongoDB stores UTF-8 natively
    // -------------------------------------------------------------------------

    function checkCharset( $charset, &$currentCharset )
    {
        return true;
    }

    function isCharsetSupported( $charset )
    {
        return true;
    }

    // -------------------------------------------------------------------------
    // SQL expression helpers — these return SQL-style expressions.
    // In practice they are never invoked via the MongoDB code path, but the
    // kernel may call them unconditionally, so we mirror the MySQL behaviour.
    // -------------------------------------------------------------------------

    function subString( $string, $from, $len = null )
    {
        return $len === null
            ? " substring( $string from $from ) "
            : " substring( $string from $from for $len ) ";
    }

    function concatString( $strings = array() )
    {
        return ' concat( ' . implode( ', ', $strings ) . ' ) ';
    }

    function md5( $str )
    {
        return " MD5( $str ) ";
    }

    function bitAnd( $arg1, $arg2 )
    {
        return 'cast( ' . $arg1 . ' & ' . $arg2 . ' AS SIGNED ) ';
    }

    function bitOr( $arg1, $arg2 )
    {
        return 'cast( ' . $arg1 . ' | ' . $arg2 . ' AS SIGNED ) ';
    }

    /**
     * Update a single document in $table matching $filter with raw MongoDB $update operators.
     */
    function mongoUpdateOne( $table, $filter, $update )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->updateOne( $filter, $update );
            return true;
        } catch ( \Exception $e ) {
            error_log( 'expMongoDB::mongoUpdateOne ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Update all documents in $table matching $filter with raw MongoDB $update operators.
     */
    function mongoUpdateMany( $table, $filter, $update )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->updateMany( $filter, $update );
            return true;
        } catch ( \Exception $e ) {
            error_log( 'expMongoDB::mongoUpdateMany ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    /**
     * Delete a single document from $table matching $filter.
     */
    function mongoDeleteOne( $table, $filter )
    {
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $table )->deleteOne( $filter );
            return true;
        } catch ( \Exception $e ) {
            error_log( 'expMongoDB::mongoDeleteOne ' . $table . ' ' . $e->getMessage() );
            return false;
        }
    }

    // =========================================================================
    // Relation / schema introspection
    // =========================================================================

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE );
    }

    function relationCounts( $relationMask )
    {
        if ( $relationMask & eZDBInterface::RELATION_TABLE_BIT )
            return $this->relationCount( eZDBInterface::RELATION_TABLE );
        return 0;
    }

    function relationCount( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( $relationType !== eZDBInterface::RELATION_TABLE )
            return 0;
        return count( $this->listCollectionNames() );
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( $relationType !== eZDBInterface::RELATION_TABLE )
            return array();
        return $this->listCollectionNames();
    }

    function removeRelation( $relationName, $relationType )
    {
        if ( $relationType !== eZDBInterface::RELATION_TABLE )
            return false;
        $dbName = $this->DB;
        try {
            $this->getClient()->selectCollection( $dbName, $relationName )->drop();
            return true;
        } catch ( \Exception $e ) {
            error_log( 'expMongoDB::removeRelation ' . $relationName . ' ' . $e->getMessage() );
            return false;
        }
    }

    function relationMatchRegexp( $relationType )
    {
        return '#^ez#';
    }

    // =========================================================================
    // Version and server information
    // =========================================================================

    function databaseServerVersion()
    {
        try {
            $result = $this->getClient()->selectDatabase( $this->DB )->command( array( 'buildInfo' => 1 ) );
            $info   = current( iterator_to_array( $result ) );
            $versionString = isset( $info['version'] ) ? (string)$info['version'] : '0.0.0';
            return array( 'string' => $versionString,
                          'values' => explode( '.', $versionString ) );
        } catch ( \Exception $e ) {
            return array( 'string' => '0.0.0', 'values' => array( '0', '0', '0' ) );
        }
    }

    function databaseClientVersion()
    {
        // MongoDB PHP library version via Composer metadata
        $composerJson = dirname( __FILE__ ) . '/../../../../vendor/mongodb/mongodb/composer.json';
        $versionString = '0.0.0';
        if ( file_exists( $composerJson ) )
        {
            $json = json_decode( file_get_contents( $composerJson ), true );
            if ( isset( $json['version'] ) )
                $versionString = $json['version'];
        }
        return array( 'string' => $versionString,
                      'values' => explode( '.', $versionString ) );
    }

    function version()
    {
        $info = $this->databaseServerVersion();
        return $info['string'];
    }

    function availableDatabases()
    {
        $names = array();
        try {
            foreach ( $this->getClient()->listDatabaseNames() as $name )
                $names[] = $name;
        } catch ( \Exception $e ) {
            error_log( 'expMongoDB::availableDatabases ' . $e->getMessage() );
            return null;
        }
        return $names ?: false;
    }

    // =========================================================================
    // Database lifecycle
    // =========================================================================

    /**
     * MongoDB creates databases on first write — this is a no-op.
     */
    function createDatabase( $dbName )
    {
        // No explicit CREATE DATABASE in MongoDB; the DB is created on first insert.
    }

    /**
     * Drops the named MongoDB database.
     */
    function removeDatabase( $dbName )
    {
        try {
            $this->getClient()->selectDatabase( $dbName )->drop();
        } catch ( \Exception $e ) {
            error_log( 'expMongoDB::removeDatabase ' . $dbName . ' ' . $e->getMessage() );
        }
    }

    /**
     * MongoDB has no concept of temporary tables — silently ignore.
     */
    function createTempTable( $createTableQuery = '', $server = self::SERVER_SLAVE )
    {
        // no-op
    }

    /**
     * MongoDB has no concept of temporary tables — silently ignore.
     */
    function dropTempTable( $dropTableQuery = '', $server = self::SERVER_SLAVE )
    {
        // no-op
    }

    function close() {}
}
