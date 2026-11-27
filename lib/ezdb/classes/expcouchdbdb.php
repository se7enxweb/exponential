<?php
/**
 * File containing the expCouchDBDB class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * The Apache CouchDB driver (DatabaseImplementation=couchdb, a data table connection's engine couchdb): tables as
 * documents of one CouchDB database, through CouchDB's HTTP API (expCouchDBClient).
 *
 * CouchDB is a document store without SQL, joins or transactions; its query language Mango selects documents by
 * their fields. The driver therefore answers SQL in two steps:
 *
 * 1. The documents a statement needs are read with Mango: every table it names, and where the statement reads one
 *    table and compares columns of it with = to literals, only the documents that match (an index on exp_table and
 *    the definition's indexes serve that). More than ScanLimit documents are refused, never answered from a part.
 * 2. The statement runs on an in-memory SQLite copy of those documents, so its answer is SQLite's, exactly: joins,
 *    GROUP BY, sub-selects, functions. A write is captured by triggers on the copy (the rows inserted, changed,
 *    deleted) and written back with _bulk_docs, each change with the revision it was read at: a document changed
 *    by someone else in between is a conflict, not a lost update.
 *
 * The SQL is SQLite's (sqlHelperDialect() is 'sqlite'): the data table layer writes its upserts, limits and
 * GROUP_CONCAT for it. A row is a document { "exp_table": table, column: value ... } whose _id is the table and the
 * primary key ("table:key"; a key of several columns as a JSON array; a table without a key gets a UUID). The
 * definition of each table (.dba form) is the document exp_couchdb_schema:<table>; counters of new ids are documents
 * exp_couchdb_sequence:<table>, moved on with their revision (compare and set).
 *
 * Transactions: CouchDB has none. Writes reach the server at once; rollback() undoes this connection's writes of the
 * transaction by compensating writes (up to JournalLimit), as the Cassandra driver does. Others see the writes in
 * between.
 *
 * Not a site database: see the CouchDB driver page for the measurements.
 *
 * @since 6.0.15
 * @package lib
 */
class expCouchDBDB extends eZDBInterface
{
    /** The exp_table of the tables' definitions */
    const SCHEMA_TABLE = 'exp_couchdb_schema';

    /** The exp_table of the counters of new ids */
    const SEQUENCE_TABLE = 'exp_couchdb_sequence';

    /** The design document of the driver's Mango indexes */
    const DESIGN = 'exp-couchdb';

    /** The error number of a server that does not answer (MySQL's "can't connect") */
    const ERROR_CONNECTION_FAILED = 2003;

    /** The defaults of [CouchDBSettings] */
    const DEFAULT_SETTINGS = array(
        'TLS' => 'disabled',
        'TLSCAFile' => '',
        'TLSVerifyPeer' => 'enabled',
        'ConnectTimeout' => 5,
        'RequestTimeout' => 60,
        'AutoCreateDatabase' => 'disabled',
        'ScanLimit' => 100000,
        'BatchSize' => 1000,
        'JournalLimit' => 10000,
    );

    /** @var array the effective [CouchDBSettings] */
    protected $Settings = array();

    /** @var expCouchDBClient */
    protected $Client;

    /** @var array|null table => .dba definition */
    protected $Definitions = null;

    /** @var array table => the id last given */
    protected $LastIds = array();

    /** @var bool whether a transaction is open (the journal is kept) */
    protected $InTransaction = false;

    /** @var array the journal: _id => the document before the transaction's first write of it (null: it was new) */
    protected $Journal = array();

    /** @var string */
    protected $ServerVersion = '';

    /** @var int documents read from the server, for the debug output and the measurements */
    public $DocumentsRead = 0;

    /** @var int statements run */
    public $NumQueries = 0;

    /**
     * @param array $parameters the eZDB parameters; 'couchdb' => array( <[CouchDBSettings] name> => value ) over
     *                          site.ini; 'couchdb_transport' => a callable( $method, $path, $body ) (tests)
     * @throws eZDBNoConnectionException
     */
    public function __construct( $parameters )
    {
        $parameters = array_merge( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => '',
                                          'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false,
                                          'slave_user' => '', 'slave_password' => '', 'slave_database' => '', 'socket' => false,
                                          'charset' => 'utf-8', 'is_internal_charset' => false, 'builtin_encoding' => false,
                                          'connect_retries' => 1, 'use_persistent_connection' => false, 'show_errors' => false ),
                                   (array)$parameters );
        parent::__construct( $parameters );
        $this->UseSlaveServer = false;
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        $this->Settings = self::settings( isset( $parameters['couchdb'] ) && is_array( $parameters['couchdb'] ) ? $parameters['couchdb'] : array() );
        if ( !class_exists( 'SQLite3' ) )
            $this->fail( 'PHP has no sqlite3 extension: the CouchDB driver answers SQL on an in-memory SQLite copy', eZDBInterface::ERROR_MISSING_EXTENSION );
        $transport = isset( $parameters['couchdb_transport'] ) && is_callable( $parameters['couchdb_transport'] ) ? $parameters['couchdb_transport'] : null;
        if ( $transport === null && !expCouchDBClient::available() )
            $this->fail( 'PHP has no curl extension: the CouchDB driver reaches CouchDB through it', eZDBInterface::ERROR_MISSING_EXTENSION );
        $this->Client = new expCouchDBClient( $this->Server, $this->Port, $this->User, $this->Password, $this->Settings + ( $transport ? array( 'transport' => $transport ) : array() ) );
        if ( (string)$this->DB === '' )
        {
            $this->IsConnected = false;
            return;
        }
        if ( !expCouchDBClient::validDatabaseName( $this->DB ) )
            $this->fail( "'{$this->DB}' is no CouchDB database name (lower case letters, digits and _$()+-/, starting with a letter)", self::ERROR_CONNECTION_FAILED );
        list( $status, $welcome ) = $this->Client->request( 'GET', '/' );
        if ( $status !== 200 || !isset( $welcome['couchdb'] ) )
            $this->fail( 'CouchDB does not answer at ' . $this->Client->base() . ': ' . self::reason( $status, $welcome ), self::ERROR_CONNECTION_FAILED );
        $this->ServerVersion = isset( $welcome['version'] ) ? (string)$welcome['version'] : '';
        list( $status, $info ) = $this->Client->request( 'GET', expCouchDBClient::path( $this->DB ) );
        if ( $status === 404 )
        {
            if ( !self::flag( $this->Settings['AutoCreateDatabase'] ) || !$this->createDatabase( $this->DB ) )
                $this->fail( "CouchDB has no database {$this->DB}", self::ERROR_CONNECTION_FAILED );
        }
        elseif ( $status !== 200 )
            $this->fail( "The database {$this->DB} cannot be read: " . self::reason( $status, $info ), self::ERROR_CONNECTION_FAILED );
        $this->IsConnected = true;
    }

    /**
     * Records a connection error and throws.
     *
     * @throws eZDBNoConnectionException
     */
    protected function fail( $message, $number )
    {
        $this->ErrorMessage = $message;
        $this->ErrorNumber = $number;
        $this->IsConnected = false;
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeError( $message, __CLASS__ );
        throw new eZDBNoConnectionException( (string)$this->Server, $message, $number );
    }

    /**
     * The effective [CouchDBSettings]: the defaults, site.ini, then $overrides.
     */
    public static function settings( array $overrides = array() )
    {
        $settings = self::DEFAULT_SETTINGS;
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance();
            if ( $ini->hasGroup( 'CouchDBSettings' ) )
                foreach ( $ini->group( 'CouchDBSettings' ) as $name => $value )
                    if ( array_key_exists( $name, $settings ) && $value !== '' && $value !== null )
                        $settings[$name] = $value;
        }
        foreach ( $overrides as $name => $value )
            $settings[$name] = $value;
        return $settings;
    }

    /** CouchDB's error of a response as text (pure) */
    public static function reason( $status, $body )
    {
        if ( is_array( $body ) && isset( $body['error'] ) )
            return $body['error'] . ( isset( $body['reason'] ) ? ': ' . ( is_string( $body['reason'] ) ? $body['reason'] : json_encode( $body['reason'] ) ) : '' ) . " (HTTP $status)";
        return "HTTP $status";
    }

    /** @return expCouchDBClient */
    public function client()
    {
        return $this->Client;
    }

    function databaseName()
    {
        return 'couchdb';
    }

    /**
     * The SQL the driver answers is SQLite's (it runs on an in-memory SQLite copy).
     */
    public function sqlHelperDialect()
    {
        return 'sqlite';
    }

    function bindingType()
    {
        return eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return $value;
    }

    // ------------------------------------------------------------------ server

    /**
     * A request to the database; the decoded body, or false (the error recorded).
     *
     * @param string $method
     * @param string $path below the database (/_find ...), or a server path when $server
     * @param mixed $body
     * @param int[] $accept statuses that are no error
     * @return mixed|false
     */
    protected function call( $method, $path, $body = null, array $accept = array( 200, 201, 202 ) )
    {
        $started = microtime( true );
        list( $status, $decoded ) = $this->Client->request( $method, expCouchDBClient::path( $this->DB ) . $path, $body );
        if ( class_exists( 'eZDBInterface' ) && method_exists( 'eZDBInterface', 'profileSQL' ) )
            eZDBInterface::profileSQL( "$method $path", microtime( true ) - $started );
        if ( !in_array( $status, $accept, true ) )
        {
            $this->ErrorMessage = self::reason( $status, $decoded );
            $this->ErrorNumber = $status === 409 ? 1062 : ( $status ? $status : self::ERROR_CONNECTION_FAILED );
            return false;
        }
        return $decoded === null ? true : $decoded;
    }

    /**
     * Documents by a Mango selector, page by page (bookmarks); at most $limit (ScanLimit), else an error.
     *
     * @param array $selector
     * @param int|null $limit
     * @return array[]|false
     */
    public function find( array $selector, $limit = null )
    {
        $limit = $limit === null ? (int)$this->Settings['ScanLimit'] : (int)$limit;
        $batch = max( 1, (int)$this->Settings['BatchSize'] );
        $docs = array();
        $bookmark = null;
        do
        {
            $request = array( 'selector' => (object)$selector, 'limit' => $batch, 'execution_stats' => false );
            if ( $bookmark !== null )
                $request['bookmark'] = $bookmark;
            $response = $this->call( 'POST', '/_find', $request );
            if ( $response === false )
                return false;
            $page = isset( $response['docs'] ) ? $response['docs'] : array();
            foreach ( $page as $doc )
                $docs[] = $doc;
            $this->DocumentsRead += count( $page );
            if ( count( $docs ) > $limit )
            {
                $this->ErrorMessage = 'The statement needs more than ' . $limit . ' documents ([CouchDBSettings] ScanLimit): it is refused, not answered from a part';
                $this->ErrorNumber = 1;
                return false;
            }
            $bookmark = isset( $response['bookmark'] ) ? $response['bookmark'] : null;
        }
        while ( count( $page ) === $batch && $bookmark !== null );
        return $docs;
    }

    /**
     * Writes documents (_bulk_docs); the server's answer per document.
     *
     * @param array[] $docs
     * @return array[]|false
     */
    public function bulk( array $docs )
    {
        $results = array();
        foreach ( array_chunk( $docs, max( 1, (int)$this->Settings['BatchSize'] ) ) as $chunk )
        {
            $response = $this->call( 'POST', '/_bulk_docs', array( 'docs' => $chunk ) );
            if ( $response === false )
                return false;
            foreach ( $response as $r )
                $results[] = $r;
        }
        return $results;
    }

    /**
     * Makes the driver's Mango index on exp_table (once).
     */
    protected function ensureTableIndex()
    {
        static $done = array();
        $key = $this->Client->base() . '/' . $this->DB;
        if ( isset( $done[$key] ) )
            return true;
        $ok = $this->call( 'POST', '/_index', array( 'index' => array( 'fields' => array( 'exp_table' ) ), 'ddoc' => self::DESIGN, 'name' => 'exp_table', 'type' => 'json' ) ) !== false;
        if ( $ok )
            $done[$key] = true;
        return $ok;
    }

    // ------------------------------------------------------------------ catalogue

    /**
     * The tables' definitions (.dba form), read once per connection.
     *
     * @param bool $reload
     * @return array table => definition
     */
    public function definitions( $reload = false )
    {
        if ( $this->Definitions !== null && !$reload )
            return $this->Definitions;
        $this->Definitions = array();
        if ( !$this->IsConnected )
            return $this->Definitions;
        $this->ensureTableIndex();
        $docs = $this->find( array( 'exp_table' => self::SCHEMA_TABLE ) );
        foreach ( is_array( $docs ) ? $docs : array() as $doc )
            if ( isset( $doc['name'], $doc['definition'] ) )
                $this->Definitions[(string)$doc['name']] = $doc['definition'];
        ksort( $this->Definitions );
        return $this->Definitions;
    }

    /**
     * The kind of each column of a table (int, float, text), its key and its counted-up column.
     *
     * @param string $table
     * @return array|null
     */
    public function tableEntry( $table )
    {
        $definitions = $this->definitions();
        return isset( $definitions[$table] ) ? expCouchbaseDB::catalogueEntry( $definitions[$table] ) : null;
    }

    /** The _id of a row's document (pure). */
    public static function documentId( $table, array $entry, array $row )
    {
        $parts = array();
        foreach ( $entry['primary'] as $column )
        {
            if ( !isset( $row[$column] ) )
                return $table . ':' . expCouchbaseDB::uuid();
            $parts[] = (string)expCouchbaseDialect::coerce( $row[$column], $entry['fields'][$column] === 'text' ? 'text' : $entry['fields'][$column] );
        }
        if ( !$parts )
            return $table . ':' . expCouchbaseDB::uuid();
        return $table . ':' . ( count( $parts ) === 1 ? $parts[0] : json_encode( $parts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
    }

    /**
     * The known tables a statement names (any of its names that is a table).
     *
     * @param array[] $tokens
     * @return string[]
     */
    protected function tablesOf( array $tokens )
    {
        $definitions = $this->definitions();
        $tables = array();
        foreach ( $tokens as $t )
            if ( $t[0] === 'id' && isset( $definitions[strtolower( $t[2] )] ) )
                $tables[strtolower( $t[2] )] = true;
        return array_keys( $tables );
    }

    /**
     * The table a write is to (INSERT INTO t, UPDATE t, DELETE FROM t, REPLACE INTO t).
     */
    protected static function targetOf( array $tokens )
    {
        foreach ( $tokens as $i => $t )
        {
            if ( $t[0] === 'kw' && in_array( $t[2], array( 'INTO', 'UPDATE', 'FROM' ), true ) )
            {
                $j = $i + 1;
                while ( isset( $tokens[$j] ) && $tokens[$j][0] === 'kw' && in_array( $tokens[$j][2], array( 'OR', 'REPLACE', 'IGNORE' ), true ) )
                    $j++;
                if ( isset( $tokens[$j] ) && $tokens[$j][0] === 'id' )
                    return strtolower( $tokens[$j][2] );
            }
        }
        return null;
    }

    /**
     * The Mango selector of = comparisons of a one-table statement's WHERE with literals (the rest is SQLite's).
     *
     * @param array[] $tokens the whole statement
     * @param string $table
     * @param array $entry
     * @return array
     */
    public static function pushdown( array $tokens, $table, array $entry )
    {
        $selector = array( 'exp_table' => $table );
        $where = expCouchSQLLexer::findTopLevel( $tokens, array( 'WHERE' ) );
        if ( $where < 0 )
            return $selector;
        $end = expCouchSQLLexer::findTopLevel( $tokens, array( 'GROUP', 'ORDER', 'LIMIT', 'HAVING', 'UNION' ), $where );
        $condition = array_slice( $tokens, $where + 1, $end >= 0 ? $end - $where - 1 : null );
        if ( expCouchSQLLexer::findTopLevel( $condition, array( 'OR', 'NOT' ) ) >= 0 )
            return $selector;
        $conjuncts = array();
        $current = array();
        $depth = 0;
        $between = false;
        foreach ( $condition as $t )
        {
            if ( expCouchSQLLexer::isChar( $t, '(' ) )
                $depth++;
            elseif ( expCouchSQLLexer::isChar( $t, ')' ) )
                $depth--;
            if ( $depth === 0 && expCouchSQLLexer::isKeyword( $t, 'BETWEEN' ) )
                $between = true;
            elseif ( $depth === 0 && expCouchSQLLexer::isKeyword( $t, 'AND' ) && $between )
                $between = false;
            elseif ( $depth === 0 && expCouchSQLLexer::isKeyword( $t, 'AND' ) )
            {
                $conjuncts[] = $current;
                $current = array();
                continue;
            }
            $current[] = $t;
        }
        $conjuncts[] = $current;
        foreach ( $conjuncts as $c )
        {
            if ( count( $c ) === 5 && $c[0][0] === 'id' && expCouchSQLLexer::isChar( $c[1], '.' ) )
                $c = array_slice( $c, 2 );
            if ( count( $c ) !== 3 || $c[0][0] !== 'id' || !expCouchSQLLexer::isChar( $c[1], '=' ) || !in_array( $c[2][0], array( 'str', 'num' ), true ) )
                continue;
            $column = strtolower( $c[0][2] );
            if ( !isset( $entry['fields'][$column] ) || $column === 'exp_table' )
                continue;
            $value = expCouchbaseDialect::coerce( $c[2][2], $entry['fields'][$column] );
            // SQLite would compare a text column with a number as text, a number column with text as a number:
            // a value that would not survive that unchanged is left to SQLite
            if ( $entry['fields'][$column] !== 'text' && !is_numeric( $c[2][2] ) )
                continue;
            if ( isset( $selector[$column] ) && $selector[$column] !== $value )
                return array( 'exp_table' => $table, '$and' => array( array( $column => $selector[$column] ), array( $column => $value ) ) );
            $selector[$column] = $value;
        }
        return $selector;
    }

    // ------------------------------------------------------------------ the in-memory copy

    /**
     * An in-memory SQLite database with the tables a statement needs, filled from the documents.
     *
     * @param array $load table => Mango selector (null: the table empty)
     * @return SQLite3|false
     */
    protected function memory( array $load )
    {
        $memory = new SQLite3( ':memory:' );
        $memory->enableExceptions( true );
        $memory->exec( 'PRAGMA recursive_triggers = ON' );
        foreach ( $load as $table => $selector )
        {
            $entry = $this->tableEntry( $table );
            try
            {
                $memory->exec( self::memoryTableSQL( $table, $entry, $this->Definitions[$table] ) );
            }
            catch ( Exception $e )
            {
                $this->ErrorMessage = $e->getMessage();
                $this->ErrorNumber = 1;
                return false;
            }
            if ( $selector === null )
                continue;
            $docs = $this->fetch( $table, $entry, $selector );
            if ( $docs === false )
                return false;
            if ( !$docs )
                continue;
            $columns = array_merge( array( '_exp_id', '_exp_rev' ), $entry['order'] );
            $quoted = array_map( array( 'expCouchDBDB', 'quote' ), $columns );
            $memory->exec( 'BEGIN' );
            $insert = $memory->prepare( 'INSERT INTO ' . self::quote( $table ) . ' (' . implode( ', ', $quoted ) . ') VALUES (' . implode( ', ', array_fill( 0, count( $columns ), '?' ) ) . ')' );
            foreach ( $docs as $doc )
            {
                $insert->reset();
                $insert->bindValue( 1, $doc['_id'], SQLITE3_TEXT );
                $insert->bindValue( 2, $doc['_rev'], SQLITE3_TEXT );
                $k = 3;
                foreach ( $entry['order'] as $column )
                {
                    $value = array_key_exists( $column, $doc ) ? $doc[$column] : null;
                    if ( is_array( $value ) )
                        $value = json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                    elseif ( is_bool( $value ) )
                        $value = $value ? 1 : 0;
                    $insert->bindValue( $k++, $value, $value === null ? SQLITE3_NULL : ( is_int( $value ) ? SQLITE3_INTEGER : ( is_float( $value ) ? SQLITE3_FLOAT : SQLITE3_TEXT ) ) );
                }
                $insert->execute();
            }
            $memory->exec( 'COMMIT' );
        }
        return $memory;
    }

    /**
     * The documents of a table a selector names: by their id when it names the whole primary key (one request to
     * _all_docs, no index), else with Mango.
     *
     * @param string $table
     * @param array $entry
     * @param array $selector
     * @return array[]|false
     */
    protected function fetch( $table, array $entry, array $selector )
    {
        $row = array();
        foreach ( $entry['primary'] as $column )
        {
            if ( !array_key_exists( $column, $selector ) || is_array( $selector[$column] ) )
                return $this->indexed( $table, $selector ) ? $this->find( $selector ) : $this->range( $table, $selector );
            $row[$column] = $selector[$column];
        }
        if ( !$row )
            return $this->indexed( $table, $selector ) ? $this->find( $selector ) : $this->range( $table, $selector );
        $response = $this->call( 'POST', '/_all_docs?include_docs=true', array( 'keys' => array( self::documentId( $table, $entry, $row ) ) ) );
        if ( $response === false )
            return false;
        $docs = array();
        foreach ( isset( $response['rows'] ) ? $response['rows'] : array() as $r )
            if ( isset( $r['doc'] ) && is_array( $r['doc'] ) && expCouchDBDB::matches( $r['doc'], $selector ) )
                $docs[] = $r['doc'];
        $this->DocumentsRead += count( $docs );
        return $docs;
    }

    /**
     * Whether a selector names the first column of an index of the table's definition (a Mango index serves it).
     */
    protected function indexed( $table, array $selector )
    {
        $definition = isset( $this->Definitions[$table] ) ? $this->Definitions[$table] : array();
        foreach ( isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array() as $index )
        {
            if ( isset( $index['type'] ) && $index['type'] === 'primary' || empty( $index['fields'] ) )
                continue;
            $first = reset( $index['fields'] );
            if ( array_key_exists( strtolower( is_array( $first ) ? $first['name'] : $first ), $selector ) )
                return true;
        }
        return false;
    }

    /**
     * The documents of a table by the range of their ids ("table:" ...), a page at a time, filtered by the selector
     * here: CouchDB reads them without evaluating a selector, several times faster than Mango without an index.
     *
     * @param string $table
     * @param array $selector
     * @return array[]|false
     */
    protected function range( $table, array $selector )
    {
        $limit = (int)$this->Settings['ScanLimit'];
        $batch = max( 1, (int)$this->Settings['BatchSize'] );
        $docs = array();
        $read = 0;
        $start = $table . ':';
        $end = $table . ":\u{fff0}";
        $skip = 0;
        do
        {
            $query = '?include_docs=true&limit=' . $batch . '&skip=' . $skip . '&startkey=' . rawurlencode( json_encode( $start ) ) . '&endkey=' . rawurlencode( json_encode( $end ) );
            $response = $this->call( 'GET', '/_all_docs' . $query );
            if ( $response === false )
                return false;
            $rows = isset( $response['rows'] ) ? $response['rows'] : array();
            foreach ( $rows as $r )
            {
                if ( isset( $r['doc'] ) && is_array( $r['doc'] ) && self::matches( $r['doc'], $selector ) )
                    $docs[] = $r['doc'];
                $start = $r['id'];
            }
            $read += count( $rows );
            $this->DocumentsRead += count( $rows );
            if ( $read > $limit )
            {
                $this->ErrorMessage = 'The statement needs more than ' . $limit . ' documents ([CouchDBSettings] ScanLimit): it is refused, not answered from a part';
                $this->ErrorNumber = 1;
                return false;
            }
            // the next page starts after the last id
            $skip = 1;
        }
        while ( count( $rows ) === $batch );
        return $docs;
    }

    /**
     * Whether a document has the values of a selector of = conditions (pure).
     */
    public static function matches( array $doc, array $selector )
    {
        foreach ( $selector as $field => $value )
        {
            if ( $field === '$and' )
            {
                foreach ( $value as $part )
                    if ( !self::matches( $doc, $part ) )
                        return false;
                continue;
            }
            if ( !array_key_exists( $field, $doc ) || $doc[$field] !== $value )
                return false;
        }
        return true;
    }

    /** A name quoted for SQLite */
    public static function quote( $name )
    {
        return '"' . str_replace( '"', '""', (string)$name ) . '"';
    }

    /**
     * The CREATE TABLE of a table's in-memory copy (pure): the columns with SQLite's affinity and the definition's
     * defaults, the primary key and the unique indexes (so SQLite's upserts and conflicts see them), plus _exp_id and
     * _exp_rev (the document's; CouchDB allows no column named _...).
     */
    public static function memoryTableSQL( $table, array $entry, array $definition )
    {
        $columns = array( '"_exp_id" TEXT', '"_exp_rev" TEXT' );
        foreach ( $entry['order'] as $column )
        {
            $type = $entry['fields'][$column] === 'int' ? 'INT' : ( $entry['fields'][$column] === 'float' ? 'REAL' : 'TEXT' );
            $sql = self::quote( $column ) . ' ' . $type;
            if ( array_key_exists( $column, $entry['defaults'] ) )
            {
                $d = $entry['defaults'][$column];
                $sql .= ' DEFAULT ' . ( is_int( $d ) || is_float( $d ) ? $d : "'" . str_replace( "'", "''", (string)$d ) . "'" );
            }
            $columns[] = $sql;
        }
        // the counted-up column is given its id by the driver: a key that SQLite would fill itself (INTEGER PRIMARY KEY) is avoided
        if ( $entry['primary'] )
            $columns[] = 'PRIMARY KEY (' . implode( ', ', array_map( array( 'expCouchDBDB', 'quote' ), $entry['primary'] ) ) . ')';
        $sql = 'CREATE TABLE ' . self::quote( $table ) . ' (' . implode( ', ', $columns ) . ');';
        foreach ( isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array() as $name => $index )
            if ( isset( $index['type'] ) && $index['type'] === 'unique' )
            {
                $fields = array();
                foreach ( $index['fields'] as $f )
                    $fields[] = self::quote( strtolower( is_array( $f ) ? $f['name'] : $f ) );
                $sql .= ' CREATE UNIQUE INDEX ' . self::quote( $table . '__' . $name ) . ' ON ' . self::quote( $table ) . ' (' . implode( ', ', $fields ) . ');';
            }
        return $sql;
    }

    // ------------------------------------------------------------------ statements

    function query( $sql, $server = false )
    {
        $result = $this->run( $sql, false );
        return $result === false ? false : true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $limit = false;
        $offset = 0;
        $column = false;
        if ( is_array( $params ) )
        {
            if ( isset( $params['limit'] ) && is_numeric( $params['limit'] ) )
                $limit = (int)$params['limit'];
            if ( isset( $params['offset'] ) && is_numeric( $params['offset'] ) )
                $offset = (int)$params['offset'];
            if ( isset( $params['column'] ) && ( is_numeric( $params['column'] ) || is_string( $params['column'] ) ) )
                $column = $params['column'];
        }
        if ( $limit !== false && $limit >= 0 || $offset > 0 )
        {
            $limited = expDBSQLHelpers::limit( 'sqlite', $sql, $limit !== false && $limit >= 0 ? $limit : false, $offset );
            if ( $limited === null )
                return array();
            $sql = $limited;
        }
        $result = $this->run( $sql, true );
        if ( !is_array( $result ) )
            return $result === false ? false : array();
        $rows = array();
        foreach ( $result as $i => $row )
        {
            if ( $column === false )
                $rows[$i + $offset] = $row;
            elseif ( is_int( $column ) || ctype_digit( (string)$column ) )
            {
                $values = array_values( $row );
                $rows[$i + $offset] = isset( $values[(int)$column] ) ? $values[(int)$column] : null;
            }
            else
                $rows[$i + $offset] = array_key_exists( $column, $row ) ? $row[$column] : null;
        }
        return $rows;
    }

    /**
     * Runs one statement.
     *
     * @param string $sql
     * @param bool $wantRows
     * @return array|bool
     */
    protected function run( $sql, $wantRows )
    {
        if ( !$this->IsConnected )
            return false;
        $this->NumQueries++;
        $kind = expCouchbaseDialect::kind( $sql );
        try
        {
            switch ( $kind )
            {
                case 'select':
                    return $this->runSelect( $sql );
                case 'insert':
                case 'update':
                case 'delete':
                    return $this->runWrite( $sql, $kind );
                case 'ddl':
                    return $this->runDDL( $sql );
                case 'transaction':
                    $word = strtoupper( strtok( ltrim( $sql ), " \t\r\n;" ) );
                    return $word === 'COMMIT' ? $this->commitQuery() : ( $word === 'ROLLBACK' ? $this->rollbackQuery() : $this->beginQuery() );
                case 'native':
                    throw new expDBUnsupportedException( 'couchdb', 'SQL++', 'CouchDB has no query language of that kind' );
                default:
                    return $wantRows ? array() : true;
            }
        }
        catch ( expDBUnsupportedException $e )
        {
            return $this->failure( $e->getMessage(), 1235, $sql );
        }
    }

    /**
     * Records and reports a failure; false.
     */
    protected function failure( $message, $number, $sql )
    {
        $this->ErrorMessage = (string)$message;
        $this->ErrorNumber = (int)$number;
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeError( self::failedQueryMessage( $this->ErrorMessage, $sql ), __CLASS__ );
        if ( $this->errorHandling == eZDB::ERROR_HANDLING_EXCEPTIONS )
            throw new eZDBException( $this->ErrorMessage, $this->ErrorNumber );
        $this->reportError();
        return false;
    }

    /**
     * The Mango selector of each table a statement reads.
     */
    protected function loadPlan( array $tokens, array $tables )
    {
        $load = array();
        foreach ( $tables as $table )
            $load[$table] = count( $tables ) === 1 ? self::pushdown( $tokens, $table, $this->tableEntry( $table ) ) : array( 'exp_table' => $table );
        return $load;
    }

    protected function runSelect( $sql )
    {
        $tokens = expCouchSQLLexer::tokenize( $sql, false );
        $tables = $this->tablesOf( $tokens );
        $memory = $this->memory( $this->loadPlan( $tokens, $tables ) );
        if ( $memory === false )
            return $this->failure( $this->ErrorMessage, $this->ErrorNumber, $sql );
        $rows = array();
        try
        {
            $result = $memory->query( $sql );
            while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
            {
                // the copy's own columns (a SELECT * would list them) are not the table's
                unset( $row['_exp_id'], $row['_exp_rev'] );
                $rows[] = $row;
            }
        }
        catch ( Exception $e )
        {
            $memory->close();
            return $this->failure( $e->getMessage(), 1, $sql );
        }
        $memory->close();
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        return $rows;
    }

    /**
     * An INSERT, UPDATE, DELETE or REPLACE: run on the copy, its effect captured by triggers and written back.
     *
     * @param string $sql
     * @param string $kind insert, update, delete
     * @return bool
     */
    protected function runWrite( $sql, $kind )
    {
        $tokens = expCouchSQLLexer::tokenize( $sql, false );
        $target = self::targetOf( $tokens );
        $definitions = $this->definitions();
        if ( $target === null || !isset( $definitions[$target] ) )
            return $this->failure( "The table " . ( $target ?: '?' ) . " does not exist (CouchDB database {$this->DB})", 1146, $sql );
        $entry = $this->tableEntry( $target );
        $tables = $this->tablesOf( $tokens );
        $ignore = false;
        $replace = false;
        foreach ( array_slice( $tokens, 0, 4 ) as $t )
        {
            if ( expCouchSQLLexer::isKeyword( $t, 'IGNORE' ) )
                $ignore = true;
            if ( expCouchSQLLexer::isKeyword( $t, 'REPLACE' ) )
                $replace = true;
        }
        // a plain INSERT needs the target empty: its conflicts are CouchDB's (the key is the _id); one that may
        // replace or update existing rows (REPLACE, ON CONFLICT) needs the rows it may meet, so the whole table
        $upsert = $replace || expCouchSQLLexer::findTopLevel( $tokens, array( 'ON' ) ) >= 0 && $kind === 'insert';
        $load = array();
        foreach ( $tables as $table )
        {
            if ( $table === $target && $kind === 'insert' && !$upsert )
                $load[$table] = null;
            elseif ( $table === $target && $kind !== 'insert' )
                $load[$table] = self::pushdown( $tokens, $table, $entry );
            else
                $load[$table] = array( 'exp_table' => $table );
        }
        $memory = $this->memory( $load );
        if ( $memory === false )
            return $this->failure( $this->ErrorMessage, $this->ErrorNumber, $sql );
        $q = self::quote( $target );
        $old = array();
        foreach ( $entry['order'] as $column )
            $old[] = "'" . str_replace( "'", "''", $column ) . "', OLD." . self::quote( $column );
        $memory->exec( 'CREATE TEMP TABLE exp_changes (op TEXT, rid INTEGER, exp_id TEXT, exp_rev TEXT, old TEXT)' );
        $memory->exec( "CREATE TEMP TRIGGER exp_i AFTER INSERT ON $q BEGIN INSERT INTO exp_changes VALUES ('i', NEW.rowid, NULL, NULL, NULL); END" );
        $memory->exec( "CREATE TEMP TRIGGER exp_u AFTER UPDATE ON $q BEGIN INSERT INTO exp_changes VALUES ('u', NEW.rowid, OLD._exp_id, OLD._exp_rev, json_object(" . implode( ', ', $old ) . ')); END' );
        $memory->exec( "CREATE TEMP TRIGGER exp_d AFTER DELETE ON $q BEGIN INSERT INTO exp_changes VALUES ('d', NULL, OLD._exp_id, OLD._exp_rev, json_object(" . implode( ', ', $old ) . ')); END' );
        try
        {
            $memory->exec( $sql );
        }
        catch ( Exception $e )
        {
            $message = $e->getMessage();
            $memory->close();
            return $this->failure( $message, stripos( $message, 'UNIQUE constraint' ) !== false ? 1062 : 1, $sql );
        }
        // the changes in order; a row inserted and changed later in the same statement is written once
        $changes = array();
        $result = $memory->query( 'SELECT op, rid, exp_id, exp_rev, old FROM exp_changes ORDER BY rowid' );
        while ( $c = $result->fetchArray( SQLITE3_ASSOC ) )
            $changes[] = $c;
        $deleted = array();
        $written = array();
        $columns = implode( ', ', array_map( array( 'expCouchDBDB', 'quote' ), array_merge( array( '_exp_id', '_exp_rev' ), $entry['order'] ) ) );
        foreach ( $changes as $c )
        {
            if ( $c['op'] === 'd' )
            {
                if ( $c['exp_id'] !== null )
                    $deleted[$c['exp_id']] = array( 'rev' => $c['exp_rev'], 'old' => json_decode( $c['old'], true ) );
                continue;
            }
            $row = $memory->querySingle( "SELECT $columns FROM $q WHERE rowid = " . (int)$c['rid'], true );
            if ( !$row )
                continue;
            $written[(int)$c['rid']] = array( 'row' => $row, 'op' => $c['op'], 'old' => $c['old'] !== null ? json_decode( $c['old'], true ) : null,
                                              'from_id' => $c['exp_id'], 'from_rev' => $c['exp_rev'] );
        }
        $memory->close();

        // new ids for the counted-up column
        $auto = $entry['auto'];
        if ( $auto !== null )
        {
            $need = 0;
            $max = null;
            foreach ( $written as $w )
                if ( $w['row'][$auto] === null || $w['row'][$auto] === 0 || $w['row'][$auto] === '' )
                    $need++;
                elseif ( $w['op'] === 'i' )
                    $max = $max === null ? (int)$w['row'][$auto] : max( $max, (int)$w['row'][$auto] );
            if ( $need )
            {
                $first = $this->nextIds( $target, $need, $max );
                if ( $first === false )
                    return $this->failure( $this->ErrorMessage, $this->ErrorNumber, $sql );
                foreach ( $written as $rid => $w )
                    if ( $w['row'][$auto] === null || $w['row'][$auto] === 0 || $w['row'][$auto] === '' )
                    {
                        $written[$rid]['row'][$auto] = $first++;
                        $this->LastIds[$target] = $first - 1;
                    }
            }
            elseif ( $max !== null )
            {
                if ( $this->nextIds( $target, 0, $max ) === false )
                    return $this->failure( $this->ErrorMessage, $this->ErrorNumber, $sql );
                $this->LastIds[$target] = $max;
            }
        }

        // the documents to write
        $docs = array();
        $before = array();
        foreach ( $written as $w )
        {
            $row = $w['row'];
            $doc = array( 'exp_table' => $target );
            foreach ( $entry['order'] as $column )
                $doc[$column] = expCouchbaseDialect::coerce( $row[$column], $row[$column] === null ? null : $entry['fields'][$column] );
            $id = self::documentId( $target, $entry, $row );
            $doc['_id'] = $id;
            if ( $w['from_id'] !== null && $w['from_id'] === $id )
            {
                $doc['_rev'] = $w['from_rev'];
                $before[$id] = $w['old'];
            }
            elseif ( isset( $deleted[$id] ) )
            {
                // REPLACE: the row it replaced had the same key
                $doc['_rev'] = $deleted[$id]['rev'];
                $before[$id] = $deleted[$id]['old'];
                unset( $deleted[$id] );
            }
            else
            {
                if ( $w['from_id'] !== null )
                {
                    // the key changed: the old document goes
                    $deleted[$w['from_id']] = array( 'rev' => $w['from_rev'], 'old' => $w['old'] );
                }
                $before[$id] = null;
            }
            $docs[] = $doc;
        }
        foreach ( $deleted as $id => $d )
        {
            $docs[] = array( '_id' => $id, '_rev' => $d['rev'], '_deleted' => true );
            $before[$id] = $d['old'];
        }
        if ( !$docs )
        {
            $this->ErrorMessage = '';
            $this->ErrorNumber = 0;
            return true;
        }
        $results = $this->bulk( $docs );
        if ( $results === false )
            return $this->failure( $this->ErrorMessage, $this->ErrorNumber, $sql );
        $errors = array();
        foreach ( $results as $k => $r )
        {
            $id = isset( $r['id'] ) ? $r['id'] : $docs[$k]['_id'];
            if ( isset( $r['error'] ) )
            {
                // a new document whose key exists: a duplicate key (or nothing, for INSERT IGNORE)
                if ( $r['error'] === 'conflict' && !isset( $docs[$k]['_rev'] ) && $ignore )
                    continue;
                $errors[] = $id . ': ' . $r['error'] . ( isset( $r['reason'] ) ? ' (' . $r['reason'] . ')' : '' );
                continue;
            }
            if ( $this->InTransaction && !array_key_exists( $id, $this->Journal ) && count( $this->Journal ) < (int)$this->Settings['JournalLimit'] )
                $this->Journal[$id] = $before[$id] === null ? null : array( 'exp_table' => $target ) + (array)$before[$id];
        }
        if ( $errors )
        {
            $duplicate = true;
            foreach ( $results as $k => $r )
                if ( isset( $r['error'] ) && ( $r['error'] !== 'conflict' || isset( $docs[$k]['_rev'] ) ) )
                    $duplicate = false;
            return $this->failure( ( $duplicate ? 'Duplicate key: ' : 'Not written: ' ) . implode( '; ', array_slice( $errors, 0, 5 ) ), $duplicate ? 1062 : 1, $sql );
        }
        $this->ErrorMessage = '';
        $this->ErrorNumber = 0;
        return true;
    }

    /**
     * Takes $count new ids of a table (and moves the counter past $atLeast); the first of them.
     *
     * @param string $table
     * @param int $count
     * @param int|null $atLeast
     * @return int|false
     */
    public function nextIds( $table, $count, $atLeast = null )
    {
        $id = self::SEQUENCE_TABLE . ':' . $table;
        for ( $attempt = 0; $attempt < 20; $attempt++ )
        {
            list( $status, $doc ) = $this->Client->request( 'GET', expCouchDBClient::path( $this->DB ) . '/' . rawurlencode( $id ) );
            if ( $status === 404 )
            {
                $entry = $this->tableEntry( $table );
                $start = 0;
                $docs = $this->find( array( 'exp_table' => $table ) );
                foreach ( is_array( $docs ) ? $docs : array() as $d )
                    if ( isset( $d[$entry['auto']] ) && is_numeric( $d[$entry['auto']] ) )
                        $start = max( $start, (int)$d[$entry['auto']] );
                $doc = array( '_id' => $id, 'exp_table' => self::SEQUENCE_TABLE, 'name' => $table, 'v' => $start );
            }
            elseif ( $status !== 200 )
            {
                $this->ErrorMessage = 'The counter of ' . $table . ' cannot be read: ' . self::reason( $status, $doc );
                $this->ErrorNumber = 1;
                return false;
            }
            $value = (int)$doc['v'];
            if ( $atLeast !== null )
                $value = max( $value, (int)$atLeast );
            $doc['v'] = $value + $count;
            list( $status, $answer ) = $this->Client->request( 'PUT', expCouchDBClient::path( $this->DB ) . '/' . rawurlencode( $id ), $doc );
            if ( $status === 201 || $status === 202 )
                return $value + 1;
            if ( $status !== 409 )
            {
                $this->ErrorMessage = 'The counter of ' . $table . ' cannot be written: ' . self::reason( $status, $answer );
                $this->ErrorNumber = 1;
                return false;
            }
            // another writer moved it: read it again
            usleep( mt_rand( 1000, 20000 ) );
        }
        $this->ErrorMessage = 'The counter of ' . $table . ' stays busy (20 conflicts)';
        $this->ErrorNumber = 1;
        return false;
    }

    function lastSerialID( $table = false, $column = false )
    {
        $table = strtolower( (string)$table );
        return isset( $this->LastIds[$table] ) ? $this->LastIds[$table] : false;
    }

    // ------------------------------------------------------------------ DDL

    protected function runDDL( $sql )
    {
        if ( preg_match( '/^\s*TRUNCATE\s+(?:TABLE\s+)?["`]?(\w+)/i', $sql, $m ) )
            return $this->runWrite( 'DELETE FROM ' . $m[1], 'delete' );
        $parser = new expCouchbaseDialect();
        $ddl = $parser->ddl( $sql, false );
        $definitions = $this->definitions();
        switch ( $ddl['action'] )
        {
            case 'create_table':
                if ( isset( $definitions[$ddl['table']] ) )
                {
                    if ( preg_match( '/\bIF\s+NOT\s+EXISTS\b/i', $sql ) )
                        return true;
                    return $this->failure( "The table {$ddl['table']} exists", 1050, $sql );
                }
                foreach ( array_keys( $ddl['definition']['fields'] ) as $column )
                    if ( $column[0] === '_' || $column === 'exp_table' )
                        return $this->failure( "CouchDB keeps no field named $column (a leading _ is CouchDB's own)", 1235, $sql );
                return $this->storeDefinition( $ddl['table'], $ddl['definition'] ) && $this->createIndexes( $ddl['table'], $ddl['definition'] );
            case 'drop_table':
                foreach ( $ddl['tables'] as $table )
                {
                    if ( !isset( $definitions[$table] ) )
                        continue;
                    $docs = $this->find( array( 'exp_table' => $table ) );
                    if ( $docs === false )
                        return $this->failure( $this->ErrorMessage, $this->ErrorNumber, $sql );
                    $gone = array();
                    foreach ( $docs as $d )
                        $gone[] = array( '_id' => $d['_id'], '_rev' => $d['_rev'], '_deleted' => true );
                    foreach ( array( self::SCHEMA_TABLE . ':' . $table, self::SEQUENCE_TABLE . ':' . $table ) as $id )
                    {
                        list( $status, $doc ) = $this->Client->request( 'GET', expCouchDBClient::path( $this->DB ) . '/' . rawurlencode( $id ) );
                        if ( $status === 200 )
                            $gone[] = array( '_id' => $id, '_rev' => $doc['_rev'], '_deleted' => true );
                    }
                    if ( $gone && $this->bulk( $gone ) === false )
                        return $this->failure( $this->ErrorMessage, $this->ErrorNumber, $sql );
                    $this->dropIndexes( $table );
                    unset( $this->Definitions[$table], $this->LastIds[$table] );
                }
                return true;
            case 'create_index':
                if ( !isset( $definitions[$ddl['table']] ) )
                    return $this->failure( "The table {$ddl['table']} does not exist", 1146, $sql );
                $definition = $definitions[$ddl['table']];
                $definition['indexes'][$ddl['name']] = array( 'type' => $ddl['unique'] ? 'unique' : 'non-unique', 'fields' => $ddl['fields'] );
                return $this->storeDefinition( $ddl['table'], $definition ) && $this->createIndexes( $ddl['table'], $definition );
            case 'drop_index':
                foreach ( $definitions as $table => $definition )
                    if ( ( $ddl['table'] === null || $ddl['table'] === $table ) && isset( $definition['indexes'][$ddl['name']] ) )
                    {
                        unset( $definition['indexes'][$ddl['name']] );
                        $this->call( 'DELETE', '/_index/_design/' . rawurlencode( self::DESIGN ) . '/json/' . rawurlencode( self::indexName( $table, $ddl['name'] ) ), null, array( 200, 404 ) );
                        return $this->storeDefinition( $table, $definition );
                    }
                return true;
            case 'add_column':
            case 'modify_column':
            case 'drop_column':
                $table = $ddl['table'];
                if ( !isset( $definitions[$table] ) )
                    return $this->failure( "The table $table does not exist", 1146, $sql );
                $definition = $definitions[$table];
                if ( $ddl['action'] === 'drop_column' )
                    unset( $definition['fields'][$ddl['column']] );
                else
                    $definition['fields'][$ddl['column']] = $ddl['field'];
                if ( !$this->storeDefinition( $table, $definition ) )
                    return false;
                if ( $ddl['action'] === 'modify_column' )
                    return true;
                // the documents get the new field with its default, or lose the dropped one
                $entry = expCouchbaseDB::catalogueEntry( $definition );
                $docs = $this->find( array( 'exp_table' => $table ) );
                if ( $docs === false )
                    return $this->failure( $this->ErrorMessage, $this->ErrorNumber, $sql );
                $changed = array();
                foreach ( $docs as $d )
                {
                    if ( $ddl['action'] === 'drop_column' )
                    {
                        if ( !array_key_exists( $ddl['column'], $d ) )
                            continue;
                        unset( $d[$ddl['column']] );
                    }
                    elseif ( !array_key_exists( $ddl['column'], $d ) )
                        $d[$ddl['column']] = array_key_exists( $ddl['column'], $entry['defaults'] ) ? $entry['defaults'][$ddl['column']] : null;
                    else
                        continue;
                    $changed[] = $d;
                }
                return !$changed || $this->bulk( $changed ) !== false;
        }
        throw new expDBUnsupportedException( 'couchdb', 'DDL ' . $ddl['action'] );
    }

    /** The name of a Mango index of a table (pure) */
    public static function indexName( $table, $index )
    {
        return $table . '__' . preg_replace( '/[^A-Za-z0-9_]+/', '_', (string)$index );
    }

    /**
     * Mango indexes of a table: exp_table and the columns of each index of its definition.
     */
    protected function createIndexes( $table, array $definition )
    {
        $this->ensureTableIndex();
        foreach ( isset( $definition['indexes'] ) ? (array)$definition['indexes'] : array() as $name => $index )
        {
            $fields = array( 'exp_table' );
            foreach ( $index['fields'] as $f )
                $fields[] = strtolower( is_array( $f ) ? $f['name'] : $f );
            if ( $this->call( 'POST', '/_index', array( 'index' => array( 'fields' => $fields ), 'ddoc' => self::DESIGN, 'name' => self::indexName( $table, $name ), 'type' => 'json' ) ) === false )
                return $this->failure( $this->ErrorMessage, $this->ErrorNumber, "CREATE INDEX $name ON $table" );
        }
        return true;
    }

    protected function dropIndexes( $table )
    {
        $indexes = $this->call( 'GET', '/_index' );
        foreach ( isset( $indexes['indexes'] ) ? $indexes['indexes'] : array() as $index )
            if ( isset( $index['name'] ) && strpos( $index['name'], $table . '__' ) === 0 )
                $this->call( 'DELETE', '/_index/' . rawurlencode( $index['ddoc'] ) . '/json/' . rawurlencode( $index['name'] ), null, array( 200, 404 ) );
    }

    /**
     * Keeps a table's definition (the document exp_couchdb_schema:<table>).
     */
    public function storeDefinition( $table, array $definition )
    {
        $this->ensureTableIndex();
        $id = self::SCHEMA_TABLE . ':' . $table;
        $definition['name'] = $table;
        $doc = array( '_id' => $id, 'exp_table' => self::SCHEMA_TABLE, 'name' => $table, 'definition' => $definition );
        list( $status, $current ) = $this->Client->request( 'GET', expCouchDBClient::path( $this->DB ) . '/' . rawurlencode( $id ) );
        if ( $status === 200 )
            $doc['_rev'] = $current['_rev'];
        if ( $this->call( 'PUT', '/' . rawurlencode( $id ), $doc ) === false )
            return $this->failure( $this->ErrorMessage, $this->ErrorNumber, "the definition of $table" );
        if ( $this->Definitions !== null )
        {
            $this->Definitions[$table] = $definition;
            ksort( $this->Definitions );
        }
        return true;
    }

    // ------------------------------------------------------------------ transactions

    function beginQuery()
    {
        $this->InTransaction = true;
        $this->Journal = array();
        return true;
    }

    function commitQuery()
    {
        $this->InTransaction = false;
        $this->Journal = array();
        return true;
    }

    /**
     * Undoes this connection's writes of the transaction: an inserted document is deleted, a changed or deleted one
     * written back as it was.
     */
    function rollbackQuery()
    {
        $journal = $this->Journal;
        $this->InTransaction = false;
        $this->Journal = array();
        if ( !$journal || !$this->IsConnected )
            return true;
        $current = $this->call( 'POST', '/_all_docs', array( 'keys' => array_map( 'strval', array_keys( $journal ) ) ) );
        if ( $current === false )
            return false;
        $revs = array();
        foreach ( isset( $current['rows'] ) ? $current['rows'] : array() as $row )
            if ( isset( $row['id'], $row['value']['rev'] ) )
                $revs[$row['id']] = array( 'rev' => $row['value']['rev'], 'deleted' => !empty( $row['value']['deleted'] ) );
        $docs = array();
        foreach ( $journal as $id => $before )
        {
            $rev = isset( $revs[$id] ) ? $revs[$id] : null;
            if ( $before === null )
            {
                if ( $rev && !$rev['deleted'] )
                    $docs[] = array( '_id' => (string)$id, '_rev' => $rev['rev'], '_deleted' => true );
                continue;
            }
            $doc = array( '_id' => (string)$id ) + $before;
            // a deleted document is made again without a revision
            if ( $rev && !$rev['deleted'] )
                $doc['_rev'] = $rev['rev'];
            $docs[] = $doc;
        }
        if ( !$docs )
            return true;
        $results = $this->bulk( $docs );
        if ( $results === false )
            return false;
        foreach ( $results as $r )
            if ( isset( $r['error'] ) )
            {
                $this->ErrorMessage = 'The rollback could not restore ' . $r['id'] . ': ' . $r['error'];
                $this->ErrorNumber = 1;
                if ( class_exists( 'eZDebug' ) )
                    eZDebug::writeError( $this->ErrorMessage, __CLASS__ );
                return false;
            }
        return true;
    }

    /** CouchDB locks nothing: a no-op (a write conflict is reported instead) */
    function lock( $table )
    {
        return true;
    }

    function unlock()
    {
        return true;
    }

    // ------------------------------------------------------------------ helpers of the kernel

    function escapeString( $str )
    {
        return str_replace( "'", "''", (string)$str );
    }

    function subString( $string, $from, $len = null )
    {
        return $len === null || $len === false ? " substr( $string, $from ) " : " substr( $string, $from, $len ) ";
    }

    function concatString( $strings = array() )
    {
        return ' ' . implode( ' || ', $strings ) . ' ';
    }

    function md5( $str )
    {
        throw new expDBUnsupportedException( 'couchdb', 'MD5', 'SQLite has no MD5: compute it in PHP' );
    }

    function supportedRelationTypeMask()
    {
        return eZDBInterface::RELATION_TABLE_BIT;
    }

    function supportedRelationTypes()
    {
        return array( eZDBInterface::RELATION_TABLE );
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        if ( $relationType !== eZDBInterface::RELATION_TABLE )
            return array();
        return array_keys( $this->definitions() );
    }

    function relationCount( $relationType = eZDBInterface::RELATION_TABLE )
    {
        return count( $this->relationList( $relationType ) );
    }

    function relationCounts( $relationMask )
    {
        return $relationMask & eZDBInterface::RELATION_TABLE_BIT ? $this->relationCount() : 0;
    }

    function eZTableList( $server = eZDBInterface::SERVER_MASTER )
    {
        $tables = array();
        foreach ( $this->relationList() as $name )
            if ( preg_match( '/^(ez|exp)/', $name ) )
                $tables[$name] = eZDBInterface::RELATION_TABLE;
        return $tables;
    }

    function relationMatchRegexp( $relationType )
    {
        return '#^(ez|exp)#';
    }

    function removeRelation( $relationName, $relationType )
    {
        return $relationType === eZDBInterface::RELATION_TABLE && preg_match( '/^\w+$/', (string)$relationName ) ? $this->query( 'DROP TABLE ' . $relationName ) : false;
    }

    /** A table's definition as kept (or null) */
    public function tableDefinition( $table )
    {
        $definitions = $this->definitions();
        return isset( $definitions[strtolower( $table )] ) ? $definitions[strtolower( $table )] : null;
    }

    function databaseServerVersion()
    {
        return array( 'string' => $this->ServerVersion, 'values' => array_map( 'intval', explode( '.', $this->ServerVersion ) ), 'name' => 'Apache CouchDB' );
    }

    function databaseClientVersion()
    {
        $curl = function_exists( 'curl_version' ) ? curl_version() : array( 'version' => '' );
        return array( 'string' => 'HTTP (curl ' . $curl['version'] . ')', 'values' => array() );
    }

    function isCharsetSupported( $charset )
    {
        return in_array( strtolower( (string)$charset ), array( 'utf-8', 'utf8', 'utf8mb4' ), true );
    }

    function availableDatabases()
    {
        list( $status, $list ) = $this->Client->request( 'GET', '/_all_dbs' );
        return $status === 200 && is_array( $list ) ? array_values( array_filter( $list, function ( $n ) { return $n[0] !== '_'; } ) ) : false;
    }

    function createDatabase( $dbName )
    {
        list( $status, $answer ) = $this->Client->request( 'PUT', expCouchDBClient::path( $dbName ) );
        if ( $status === 201 || $status === 202 || $status === 412 )
            return true;
        $this->ErrorMessage = self::reason( $status, $answer );
        return false;
    }

    function removeDatabase( $dbName )
    {
        list( $status, ) = $this->Client->request( 'DELETE', expCouchDBClient::path( $dbName ) );
        return $status === 200 || $status === 202 || $status === 404;
    }

    function supportsDefaultValuesInsertion()
    {
        return true;
    }

    function close()
    {
        if ( $this->InTransaction )
            $this->rollbackQuery();
        $this->Client->close();
        $this->IsConnected = false;
    }

    function setError()
    {
    }

    /** Whether a setting is on */
    public static function flag( $value )
    {
        return in_array( strtolower( trim( (string)$value ) ), array( '1', 'yes', 'true', 'enabled', 'on' ), true );
    }
}
?>
