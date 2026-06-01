<?php
/**
 * Stubs for expMongoDB adapter unit tests.
 *
 * Provides minimal versions of eZDBInterface (and helpers) that allow
 * expMongoDB to be loaded and tested without bootstrapping the full eZ
 * Publish kernel or requiring a live MongoDB/MySQL server.
 *
 * The key class here is expMongoDBTestable — a subclass of expMongoDB
 * that replaces the real MongoDB\Client with an in-process stub backed by
 * PHP arrays, giving full control over what aggregate/find/insert/etc return.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group mongodb
 */

// ── eZDebug stub ──────────────────────────────────────────────────────────────

if ( !class_exists( 'eZDebug', false ) )
{
    class eZDebug
    {
        public static $lastWarning = null;
        public static $lastError   = null;
        public static function writeWarning( $msg, $ctx = '' ) { self::$lastWarning = $msg; }
        public static function writeError( $msg, $ctx = '' )   { self::$lastError   = $msg; }
        public static function accumulatorStart( $key, $group = '', $label = '' ) {}
        public static function accumulatorStop( $key ) {}
        public static function reset() { self::$lastWarning = self::$lastError = null; }
    }
}

// ── eZDBInterface stub ────────────────────────────────────────────────────────
// Provides the fields and begin/commit/rollback that expMongoDB inherits.

if ( !class_exists( 'eZDBInterface', false ) )
{
    abstract class eZDBInterface
    {
        const SERVER_MASTER = 1;
        const SERVER_SLAVE  = 2;
        const RELATION_TABLE = 1;

        public $TransactionCounter = 0;
        public $IsConnected        = false;
        public $OutputSQL          = false;
        public $ErrorMessage       = '';
        public $ErrorNumber        = 0;
        public $RecordError        = true;
        public $UsePersistentConnection = false;
        protected $TransactionIsValid   = true;

        public function __construct( $parameters = [] ) {}

        /** Real begin/commit plumbing — mirrors eZDBInterface exactly. */
        public function begin()
        {
            if ( $this->TransactionCounter === 0 )
                $this->beginQuery();
            ++$this->TransactionCounter;
        }

        public function commit()
        {
            if ( $this->TransactionCounter <= 0 ) return;
            --$this->TransactionCounter;
            if ( $this->TransactionCounter === 0 )
                $this->commitQuery();
        }

        public function rollback()
        {
            $this->TransactionCounter = 0;
            $this->rollbackQuery();
        }

        // Required abstract-like methods — expMongoDB provides all of these.
        abstract public function databaseName();
        abstract public function query( $sql, $server = false );
        abstract public function arrayQuery( $sql, $params = [], $server = false );
        abstract public function escapeString( $str );
        abstract public function beginQuery();
        abstract public function commitQuery();
        abstract public function rollbackQuery();

        // Timer helpers used in aggregate() debug path — no-ops in tests.
        protected function startTimer() {}
        protected function endTimer() {}
        protected function timeTaken() { return 0.0; }
        protected function reportQuery( $class, $label, $count, $time ) {}

        // generateSQLINStatement — needed by some kernel callers.
        public function generateSQLINStatement( $elements, $col, $not = false, $unique = false, $type = 'int' )
        {
            if ( $unique ) $elements = array_unique( $elements );
            $cast = array_map( function( $v ) use ( $type ) {
                return $type === 'int' ? (int)$v : (string)$v;
            }, $elements );
            $list = implode( ', ', $cast );
            return $not ? "$col NOT IN ( $list )" : "$col IN ( $list )";
        }
    }
}

// ── Minimal MongoDB\BSON\Document stub ────────────────────────────────────────
// Used by the stub collection's cursor to provide getArrayCopy().

if ( !class_exists( 'StubMongoDocument', false ) )
{
    class StubMongoDocument
    {
        private array $data;
        public function __construct( array $data ) { $this->data = $data; }
        public function getArrayCopy(): array { return $this->data; }
    }
}

// ── Stub in-process collection ────────────────────────────────────────────────

if ( !class_exists( 'StubMongoCollection', false ) )
{
    /**
     * In-process collection backed by a PHP array.
     * Supports the subset of MongoDB collection operations used by expMongoDB.
     */
    class StubMongoCollection
    {
        /** @var array[] Rows stored in this collection */
        public array $rows;

        public function __construct( array &$rows )
        {
            $this->rows = &$rows;
        }

        /**
         * Very lightweight aggregate: handles $match and $count stages only.
         * Enough to exercise the code paths tested here.
         */
        public function aggregate( array $pipeline, array $options = [] ): array
        {
            $rows = $this->rows;

            foreach ( $pipeline as $stage )
            {
                if ( isset( $stage['$match'] ) )
                {
                    $filter = $stage['$match'];
                    $rows = array_values( array_filter( $rows, function( $row ) use ( $filter ) {
                        foreach ( $filter as $k => $v )
                        {
                            if ( !isset( $row[$k] ) || $row[$k] !== $v ) return false;
                        }
                        return true;
                    } ) );
                }
                elseif ( isset( $stage['$count'] ) )
                {
                    return [ new StubMongoDocument( [ $stage['$count'] => count( $rows ) ] ) ];
                }
                // $group, $sort, $limit etc. are passed through (sufficient for current tests)
            }

            return array_map( fn( $r ) => new StubMongoDocument( $r ), $rows );
        }

        public function findOne( array $filter = [], array $options = [] ): ?StubMongoDocument
        {
            foreach ( $this->rows as $row )
            {
                $match = true;
                foreach ( $filter as $k => $v )
                {
                    if ( !isset( $row[$k] ) || $row[$k] !== $v ) { $match = false; break; }
                }
                if ( $match ) return new StubMongoDocument( $row );
            }
            return null;
        }

        public function find( array $filter = [], array $options = [] ): array
        {
            $result = [];
            foreach ( $this->rows as $row )
            {
                $match = true;
                foreach ( $filter as $k => $v )
                    if ( !isset( $row[$k] ) || $row[$k] !== $v ) { $match = false; break; }
                if ( $match ) $result[] = new StubMongoDocument( $row );
            }
            return $result;
        }

        public function insertOne( array $doc, array $options = [] ): void
        {
            $this->rows[] = $doc;
        }

        public function replaceOne( array $filter, array $replacement, array $options = [] ): void
        {
            foreach ( $this->rows as $i => $row )
            {
                $match = true;
                foreach ( $filter as $k => $v )
                    if ( !isset( $row[$k] ) || $row[$k] !== $v ) { $match = false; break; }
                if ( $match )
                {
                    $this->rows[$i] = $replacement;
                    return;
                }
            }
            // upsert: insert if not found
            if ( $options['upsert'] ?? false )
                $this->rows[] = $replacement;
        }

        public function deleteMany( array $filter, array $options = [] ): void
        {
            $this->rows = array_values( array_filter( $this->rows, function( $row ) use ( $filter ) {
                foreach ( $filter as $k => $v )
                    if ( !isset( $row[$k] ) || $row[$k] !== $v ) return true;
                return false;
            } ) );
        }

        public function deleteOne( array $filter, array $options = [] ): void
        {
            foreach ( $this->rows as $i => $row )
            {
                $match = true;
                foreach ( $filter as $k => $v )
                    if ( !isset( $row[$k] ) || $row[$k] !== $v ) { $match = false; break; }
                if ( $match )
                {
                    array_splice( $this->rows, $i, 1 );
                    return;
                }
            }
        }

        public function updateOne( array $filter, array $update, array $options = [] ): void
        {
            foreach ( $this->rows as $i => $row )
            {
                $match = true;
                foreach ( $filter as $k => $v )
                    if ( !isset( $row[$k] ) || $row[$k] !== $v ) { $match = false; break; }
                if ( $match )
                {
                    if ( isset( $update['$set'] ) )
                        foreach ( $update['$set'] as $k => $v ) $this->rows[$i][$k] = $v;
                    if ( isset( $update['$inc'] ) )
                        foreach ( $update['$inc'] as $k => $v ) $this->rows[$i][$k] = ( $this->rows[$i][$k] ?? 0 ) + $v;
                    return;
                }
            }
            if ( $options['upsert'] ?? false )
            {
                $doc = $filter;
                if ( isset( $update['$set'] ) ) $doc = array_merge( $doc, $update['$set'] );
                if ( isset( $update['$inc'] ) ) foreach ( $update['$inc'] as $k => $v ) $doc[$k] = ( $doc[$k] ?? 0 ) + $v;
                $this->rows[] = $doc;
            }
        }

        public function updateMany( array $filter, array $update, array $options = [] ): void
        {
            foreach ( $this->rows as $i => $row )
            {
                $match = true;
                foreach ( $filter as $k => $v )
                    if ( !isset( $row[$k] ) || $row[$k] !== $v ) { $match = false; break; }
                if ( !$match ) continue;
                if ( isset( $update['$set'] ) )
                    foreach ( $update['$set'] as $k => $v ) $this->rows[$i][$k] = $v;
            }
        }
    }
}

// ── Stub client and database ──────────────────────────────────────────────────

if ( !class_exists( 'StubMongoClient', false ) )
{
    class StubMongoClient
    {
        /** @var array */
        private $collections;

        public function __construct( array &$collections )
        {
            $this->collections = &$collections;
        }

        public function selectCollection( string $dbName, string $collName ): StubMongoCollection
        {
            if ( !isset( $this->collections[$collName] ) )
                $this->collections[$collName] = [];
            return new StubMongoCollection( $this->collections[$collName] );
        }

        /** Stub for ping command used in constructor */
        public function selectDatabase( string $dbName ): self { return $this; }
        public function command( array $cmd ): array { return []; }
        public function listCollectionNames(): array { return array_keys( $this->collections ); }
    }
}

// ── expMongoDBTestable ─────────────────────────────────────────────────────

// Load the real class — it extends eZDBInterface which is now stubbed above.
// Autoload won't find it via PSR-4 since it's an old-style class; load directly.
if ( !class_exists( 'expMongoDB', false ) )
{
    $adapterPath = __DIR__ . '/../../../../../lib/ezdb/classes/expmongodb.php';
    if ( file_exists( $adapterPath ) )
        require_once $adapterPath;
}

/**
 * Testable subclass of expMongoDB that wires getClient() to the stub.
 *
 * Usage:
 *   $db = new expMongoDBTestable();
 *   $db->stubCollections['mytable'] = [ ['id'=>1,'name'=>'x'] ];
 *   $rows = $db->aggregate( 'mytable', [['$match'=>['id'=>1]]] );
 */
class expMongoDBTestable extends expMongoDB
{
    /** @var array[] In-memory collections, keyed by collection name */
    public array $stubCollections = [];

    public function __construct()
    {
        // Skip the parent constructor which tries to connect to a real MongoDB.
        $this->TransactionCounter = 0;
        $this->IsConnected        = true;
        $this->OutputSQL          = false;
        $this->ErrorMessage       = '';
        $this->ErrorNumber        = 0;
        $this->RecordError        = true;
    }

    /** @override — return our in-process stub client */
    protected function getClient(): StubMongoClient
    {
        return new StubMongoClient( $this->stubCollections );
    }
}
