<?php
/**
 * Unit tests for the expMongoDB adapter class.
 *
 * These tests exercise the adapter's pure PHP logic — condition translation,
 * string helpers, transaction no-ops, and protocol contracts — without
 * requiring a live MongoDB server.  They use a lightweight in-process stub
 * collection so that aggregate/insert/upsert/deleteWhere can be exercised at
 * the PHP level.
 *
 * Tests that require a live MongoDB or MySQL connection are kept in
 * expMongoDBIntegrationTest.php (@group mongodb-live).
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group mongodb
 */

/**
 * Minimal stubs allowing expMongoDB to be loaded without a full eZ stack.
 */
require_once __DIR__ . '/stubs.php';

/**
 * Unit tests for expMongoDB — no live database required.
 */
class expMongoDBAdapterTest extends PHPUnit\Framework\TestCase
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    /** @var expMongoDBTestable */
    private $db;

    protected function setUp(): void
    {
        $this->db = new expMongoDBTestable();
    }

    // ── databaseName ─────────────────────────────────────────────────────────

    /**
     * @testdox databaseName() always returns the literal string 'mongo'
     */
    public function testDatabaseNameReturnsMongo(): void
    {
        $this->assertSame( 'mongo', $this->db->databaseName() );
    }

    // ── query() is a no-op ────────────────────────────────────────────────────

    /**
     * @testdox query() returns false (SQL no-op — MongoDB cannot execute raw SQL)
     */
    public function testQueryReturnsFalse(): void
    {
        $this->assertFalse( $this->db->query( "SELECT 1" ) );
        $this->assertFalse( $this->db->query( "UPDATE ezcontentobject SET status=1 WHERE id=99" ) );
    }

    // ── escapeString ──────────────────────────────────────────────────────────

    /**
     * @testdox escapeString() returns the input cast to string without modification for clean values
     */
    public function testEscapeStringCleanValue(): void
    {
        $this->assertSame( 'hello world', $this->db->escapeString( 'hello world' ) );
    }

    /**
     * @testdox escapeString() casts to string and does not throw on binary input
     */
    public function testEscapeStringHandlesBinaryInput(): void
    {
        // The MongoDB adapter's escapeString is a simple (string) cast.
        // It does not strip null bytes — that is the responsibility of the caller
        // when constructing queries.  We verify it does not throw.
        $result = $this->db->escapeString( "foo\x00bar" );
        $this->assertIsString( $result );
    }

    /**
     * @testdox escapeString() casts non-string scalars to string
     */
    public function testEscapeStringCastsToString(): void
    {
        $this->assertSame( '42', $this->db->escapeString( 42 ) );
        $this->assertSame( '3.14', $this->db->escapeString( 3.14 ) );
    }

    // ── translateConditions ───────────────────────────────────────────────────

    /**
     * @testdox translateConditions([]) returns an empty filter array
     */
    public function testTranslateConditionsEmpty(): void
    {
        $this->assertSame( [], $this->db->translateConditions( [] ) );
    }

    /**
     * @testdox translateConditions: scalar value → exact-match filter
     */
    public function testTranslateConditionsScalar(): void
    {
        $filter = $this->db->translateConditions( [ 'status' => 1, 'name' => 'Alice' ] );
        $this->assertSame( [ 'status' => 1, 'name' => 'Alice' ], $filter );
    }

    /**
     * @testdox translateConditions: ['>', value] → $gt operator
     */
    public function testTranslateConditionsGt(): void
    {
        $filter = $this->db->translateConditions( [ 'modified' => [ '>', 1000 ] ] );
        $this->assertSame( [ 'modified' => [ '$gt' => 1000 ] ], $filter );
    }

    /**
     * @testdox translateConditions: ['>=', value] → $gte operator
     */
    public function testTranslateConditionsGte(): void
    {
        $filter = $this->db->translateConditions( [ 'id' => [ '>=', 5 ] ] );
        $this->assertSame( [ 'id' => [ '$gte' => 5 ] ], $filter );
    }

    /**
     * @testdox translateConditions: ['<', value] → $lt operator
     */
    public function testTranslateConditionsLt(): void
    {
        $filter = $this->db->translateConditions( [ 'version' => [ '<', 3 ] ] );
        $this->assertSame( [ 'version' => [ '$lt' => 3 ] ], $filter );
    }

    /**
     * @testdox translateConditions: ['<=', value] → $lte operator
     */
    public function testTranslateConditionsLte(): void
    {
        $filter = $this->db->translateConditions( [ 'version' => [ '<=', 3 ] ] );
        $this->assertSame( [ 'version' => [ '$lte' => 3 ] ], $filter );
    }

    /**
     * @testdox translateConditions: ['!=', value] → $ne operator
     */
    public function testTranslateConditionsNe(): void
    {
        $filter = $this->db->translateConditions( [ 'status' => [ '!=', 0 ] ] );
        $this->assertSame( [ 'status' => [ '$ne' => 0 ] ], $filter );
    }

    /**
     * @testdox translateConditions: ['=', value] → exact match (null op)
     */
    public function testTranslateConditionsEq(): void
    {
        $filter = $this->db->translateConditions( [ 'id' => [ '=', 42 ] ] );
        $this->assertSame( [ 'id' => 42 ], $filter );
    }

    /**
     * @testdox translateConditions: ['like', '%pattern%'] → $regex case-insensitive
     */
    public function testTranslateConditionsLike(): void
    {
        $filter = $this->db->translateConditions( [ 'name' => [ 'like', '%Alice%' ] ] );
        $this->assertArrayHasKey( 'name', $filter );
        $this->assertArrayHasKey( '$regex', $filter['name'] );
        $this->assertSame( 'i', $filter['name']['$options'] );
        // Strips leading/trailing % — pattern must contain the word, not the %
        $this->assertStringContainsString( 'Alice', $filter['name']['$regex'] );
    }

    /**
     * @testdox translateConditions: [false, [low, high]] → $gte/$lte range
     */
    public function testTranslateConditionsRange(): void
    {
        $filter = $this->db->translateConditions( [ 'created' => [ false, [ 100, 200 ] ] ] );
        $this->assertSame( [ 'created' => [ '$gte' => 100, '$lte' => 200 ] ], $filter );
    }

    /**
     * @testdox translateConditions: [[v1, v2, v3]] → $in array
     */
    public function testTranslateConditionsIn(): void
    {
        $filter = $this->db->translateConditions( [ 'id' => [ [ 1, 5, 9 ] ] ] );
        $this->assertSame( [ 'id' => [ '$in' => [ 1, 5, 9 ] ] ], $filter );
    }

    // ── transaction no-ops ────────────────────────────────────────────────────

    /**
     * @testdox beginQuery() returns true (MongoDB auto-commits, no real BEGIN)
     */
    public function testBeginQueryReturnsTrue(): void
    {
        $this->assertTrue( $this->db->beginQuery() );
    }

    /**
     * @testdox commitQuery() returns true
     */
    public function testCommitQueryReturnsTrue(): void
    {
        $this->assertTrue( $this->db->commitQuery() );
    }

    /**
     * @testdox rollbackQuery() returns true (no-op)
     */
    public function testRollbackQueryReturnsTrue(): void
    {
        $this->assertTrue( $this->db->rollbackQuery() );
    }

    /**
     * @testdox begin()/commit() cycle increments/decrements transaction counter
     */
    public function testTransactionCounterTracking(): void
    {
        $this->assertSame( 0, $this->db->TransactionCounter );
        $this->db->begin();
        $this->assertSame( 1, $this->db->TransactionCounter );
        $this->db->begin();
        $this->assertSame( 2, $this->db->TransactionCounter );
        $this->db->commit();
        $this->assertSame( 1, $this->db->TransactionCounter );
        $this->db->commit();
        $this->assertSame( 0, $this->db->TransactionCounter );
    }

    // ── string helpers ────────────────────────────────────────────────────────

    /**
     * @testdox subString() returns a PHP substr fragment
     */
    public function testSubString(): void
    {
        $expr = $this->db->subString( "'hello world'", 1, 5 );
        // Implementation returns a SQL-style SUBSTRING expression string
        $this->assertIsString( $expr );
        $this->assertNotEmpty( $expr );
    }

    /**
     * @testdox concatString() joins multiple expressions
     */
    public function testConcatString(): void
    {
        $expr = $this->db->concatString( [ 'a', 'b', 'c' ] );
        $this->assertIsString( $expr );
        $this->assertStringContainsString( 'a', $expr );
        $this->assertStringContainsString( 'c', $expr );
    }

    /**
     * @testdox md5() wraps expression in MD5(...)
     */
    public function testMd5(): void
    {
        $expr = $this->db->md5( "'hello'" );
        $this->assertStringContainsString( 'MD5', strtoupper( $expr ) );
    }

    /**
     * @testdox bitAnd() produces a bitwise-AND expression string
     */
    public function testBitAnd(): void
    {
        $expr = $this->db->bitAnd( 'lang_mask', 2 );
        $this->assertIsString( $expr );
        $this->assertNotEmpty( $expr );
    }

    /**
     * @testdox bitOr() produces a bitwise-OR expression string
     */
    public function testBitOr(): void
    {
        $expr = $this->db->bitOr( 'lang_mask', 4 );
        $this->assertIsString( $expr );
        $this->assertNotEmpty( $expr );
    }

    // ── charset / binding ─────────────────────────────────────────────────────

    /**
     * @testdox checkCharset() always returns true (charset irrelevant for MongoDB)
     */
    public function testCheckCharsetAlwaysTrue(): void
    {
        $current = '';
        $this->assertTrue( $this->db->checkCharset( 'utf-8', $current ) );
        $this->assertTrue( $this->db->checkCharset( 'latin-1', $current ) );
    }

    /**
     * @testdox isCharsetSupported() accepts utf-8 and UTF-8
     */
    public function testIsCharsetSupported(): void
    {
        $this->assertTrue( $this->db->isCharsetSupported( 'utf-8' ) );
        $this->assertTrue( $this->db->isCharsetSupported( 'UTF-8' ) );
    }

    // ── stub-backed collection operations ─────────────────────────────────────

    /**
     * @testdox aggregate() returns array of rows from stub collection
     */
    public function testAggregateReturnsRows(): void
    {
        $this->db->stubCollections['eztestcol'] = [
            [ 'id' => 1, 'status' => 1, 'name' => 'Foo' ],
            [ 'id' => 2, 'status' => 1, 'name' => 'Bar' ],
            [ 'id' => 3, 'status' => 0, 'name' => 'Baz' ],
        ];

        $rows = $this->db->aggregate( 'eztestcol', [
            [ '$match' => [ 'status' => 1 ] ],
        ] );

        $this->assertCount( 2, $rows );
        $this->assertSame( 'Foo', $rows[0]['name'] );
        $this->assertSame( 'Bar', $rows[1]['name'] );
    }

    /**
     * @testdox aggregate() with empty pipeline returns all rows
     */
    public function testAggregateEmptyPipelineReturnsAll(): void
    {
        $this->db->stubCollections['eztestcol2'] = [
            [ 'id' => 10 ],
            [ 'id' => 20 ],
        ];

        $rows = $this->db->aggregate( 'eztestcol2', [] );
        $this->assertCount( 2, $rows );
    }

    /**
     * @testdox aggregate() on missing collection returns empty array (no throw)
     */
    public function testAggregateMissingCollectionReturnsEmpty(): void
    {
        $rows = $this->db->aggregate( 'nosuch_collection', [] );
        $this->assertIsArray( $rows );
        $this->assertCount( 0, $rows );
    }

    /**
     * @testdox insert() appends document and returns true
     */
    public function testInsertAppendsDocument(): void
    {
        $result = $this->db->insert( 'eztestinsert', [ 'id' => 99, 'name' => 'Test' ] );
        $this->assertTrue( $result );
        $this->assertCount( 1, $this->db->stubCollections['eztestinsert'] );
        $this->assertSame( 99, $this->db->stubCollections['eztestinsert'][0]['id'] );
    }

    /**
     * @testdox upsert() updates existing document matched by filter
     */
    public function testUpsertUpdatesExisting(): void
    {
        $this->db->stubCollections['ezupserttable'] = [
            [ 'id' => 5, 'value' => 'old' ],
        ];

        $this->db->upsert( 'ezupserttable', [ 'id' => 5 ], [ 'id' => 5, 'value' => 'new' ] );

        $this->assertCount( 1, $this->db->stubCollections['ezupserttable'] );
        $this->assertSame( 'new', $this->db->stubCollections['ezupserttable'][0]['value'] );
    }

    /**
     * @testdox upsert() inserts new document when filter matches nothing
     */
    public function testUpsertInsertsWhenNotFound(): void
    {
        $this->db->stubCollections['ezupserttable2'] = [];

        $this->db->upsert( 'ezupserttable2', [ 'id' => 7 ], [ 'id' => 7, 'value' => 'inserted' ] );

        $this->assertCount( 1, $this->db->stubCollections['ezupserttable2'] );
        $this->assertSame( 'inserted', $this->db->stubCollections['ezupserttable2'][0]['value'] );
    }

    /**
     * @testdox deleteWhere() removes matched documents
     */
    public function testDeleteWhereRemovesMatched(): void
    {
        $this->db->stubCollections['ezdeltable'] = [
            [ 'id' => 1, 'status' => 1 ],
            [ 'id' => 2, 'status' => 0 ],
            [ 'id' => 3, 'status' => 1 ],
        ];

        $this->db->deleteWhere( 'ezdeltable', [ 'status' => 1 ] );

        $remaining = $this->db->stubCollections['ezdeltable'];
        $this->assertCount( 1, $remaining );
        $this->assertSame( 2, array_values( $remaining )[0]['id'] );
    }

    /**
     * @testdox deleteWhere() with no matching documents leaves collection unchanged
     */
    public function testDeleteWhereNoMatchLeavesCollection(): void
    {
        $this->db->stubCollections['ezdeltable2'] = [
            [ 'id' => 10 ],
        ];

        $this->db->deleteWhere( 'ezdeltable2', [ 'id' => 999 ] );

        $this->assertCount( 1, $this->db->stubCollections['ezdeltable2'] );
    }

    // ── kernel integration contract ───────────────────────────────────────────

    /**
     * @testdox Kernel code that checks databaseName() === 'mongo' will reach MongoDB branches
     */
    public function testDatabaseNameBranchingContract(): void
    {
        // This mirrors the pattern used throughout the patched kernel:
        //   if ( $db->databaseName() === 'mongo' ) { ... MongoDB path ... }
        $db = $this->db;
        $usedMongoPath = false;
        if ( $db->databaseName() === 'mongo' )
        {
            $usedMongoPath = true;
        }
        $this->assertTrue( $usedMongoPath, 'Kernel MongoDB branch must be entered when databaseName() === "mongo"' );
    }

    /**
     * @testdox arrayQuery() returns empty array, does NOT throw, and logs via eZDebug (not error_log)
     */
    public function testArrayQueryReturnsEmptyArray(): void
    {
        eZDebug::reset();
        $result = $this->db->arrayQuery( "SELECT * FROM ezcontentobject WHERE id=1" );
        $this->assertIsArray( $result );
        $this->assertCount( 0, $result );
        // Warning must be routed through eZDebug, not emitted to error_log / stderr
        $this->assertNotNull( eZDebug::$lastWarning,
            'arrayQuery must call eZDebug::writeWarning — not error_log' );
        $this->assertStringContainsString( 'MONGO TODO', eZDebug::$lastWarning );
        $this->assertStringContainsString( 'ezcontentobject', eZDebug::$lastWarning );
    }
}
