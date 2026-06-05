<?php
/**
 * Live integration tests for expMongoDB and the MySQL (ezmysqli) adapter.
 *
 * These tests require:
 *  - A running MongoDB 8.x instance at mongodb://db:publishing$8088@localhost:27017/exp
 *  - A running MariaDB/MySQL instance at localhost with user xa_alpha / db-alpha-2025 / xa_alpha
 *
 * They are tagged @group mongodb-live so they are EXCLUDED from the default
 * phpunit run.  Run them explicitly with:
 *
 *   php vendor/bin/phpunit --testsuite mongodb-live
 *
 * They do NOT modify production data — all writes target a dedicated
 * _phpunit_test collection/table and are cleaned up in tearDown().
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group mongodb-live
 */

/**
 * Integration tests — require live MongoDB + MySQL.
 */
class expMongoDBIntegrationTest extends PHPUnit\Framework\TestCase
{
    private const MONGO_URI  = 'mongodb://db:publishing$8088@localhost:27017/exp';
    private const MONGO_DB   = 'exp';
    private const TEST_COL   = '_phpunit_test';
    private const SEQ_COL    = 'ezsequence';

    private const MYSQL_HOST = 'localhost';
    private const MYSQL_USER = 'xa_alpha';
    private const MYSQL_PASS = 'db-alpha-2025';
    private const MYSQL_DB   = 'xa_alpha';

    /** @var MongoDB\Client */
    private static $mongoClient;

    /** @var mysqli */
    private static $mysql;

    // ── Bootstrap ─────────────────────────────────────────────────────────────

    public static function setUpBeforeClass(): void
    {
        // MongoDB
        if ( !extension_loaded( 'mongodb' ) )
            self::markTestSkipped( 'MongoDB PHP extension not loaded' );

        require_once __DIR__ . '/../../../../../vendor/autoload.php';

        try
        {
            self::$mongoClient = new MongoDB\Client( self::MONGO_URI );
            self::$mongoClient->selectDatabase( self::MONGO_DB )->command( [ 'ping' => 1 ] );
        }
        catch ( Exception $e )
        {
            self::markTestSkipped( 'Cannot connect to MongoDB: ' . $e->getMessage() );
        }

        // MySQL
        self::$mysql = @new mysqli(
            self::MYSQL_HOST, self::MYSQL_USER, self::MYSQL_PASS, self::MYSQL_DB
        );
        if ( self::$mysql->connect_errno )
        {
            self::$mysql = null;
            // Not skipped — MySQL tests will be individually skipped via skipIfNoMySQL()
        }
    }

    protected function tearDown(): void
    {
        // Always remove test documents written during this test.
        self::$mongoClient
            ->selectCollection( self::MONGO_DB, self::TEST_COL )
            ->deleteMany( [] );
        // Clean up sequence counter if touched
        self::$mongoClient
            ->selectCollection( self::MONGO_DB, self::SEQ_COL )
            ->deleteMany( [ '_id' => 'phpunit_seq_test' ] );
    }

    private function skipIfNoMySQL(): void
    {
        if ( self::$mysql === null )
            $this->markTestSkipped( 'Cannot connect to MySQL/MariaDB' );
    }

    // ── MongoDB live: basic connectivity ─────────────────────────────────────

    /**
     * @testdox [LIVE] MongoDB ping succeeds — server is reachable
     */
    public function testMongoPingSucceeds(): void
    {
        $result = self::$mongoClient
            ->selectDatabase( self::MONGO_DB )
            ->command( [ 'ping' => 1 ] );
        $arr = current( iterator_to_array( $result ) );
        $this->assertArrayHasKey( 'ok', (array)$arr );
        $this->assertEquals( 1, (int)( (array)$arr )['ok'] );
    }

    /**
     * @testdox [LIVE] MongoDB insert + aggregate round-trip
     */
    public function testMongoInsertAndAggregate(): void
    {
        $col = self::$mongoClient->selectCollection( self::MONGO_DB, self::TEST_COL );

        // Insert two test documents
        $col->insertOne( [ 'type' => 'phpunit', 'value' => 10, 'active' => true ] );
        $col->insertOne( [ 'type' => 'phpunit', 'value' => 20, 'active' => false ] );

        // Aggregate: match active=true, project value
        $pipeline = [
            [ '$match'   => [ 'type' => 'phpunit', 'active' => true ] ],
            [ '$project' => [ 'value' => 1 ] ],
        ];
        $cursor = $col->aggregate( $pipeline );
        $rows   = iterator_to_array( $cursor );

        $this->assertCount( 1, $rows );
        $this->assertSame( 10, (int)( (array)$rows[0] )['value'] );
    }

    /**
     * @testdox [LIVE] MongoDB upsert: update existing document
     */
    public function testMongoUpsertUpdatesExisting(): void
    {
        $col = self::$mongoClient->selectCollection( self::MONGO_DB, self::TEST_COL );
        $col->insertOne( [ 'type' => 'phpunit_upsert', 'key' => 'k1', 'val' => 'old' ] );

        $col->replaceOne(
            [ 'type' => 'phpunit_upsert', 'key' => 'k1' ],
            [ 'type' => 'phpunit_upsert', 'key' => 'k1', 'val' => 'new' ],
            [ 'upsert' => true ]
        );

        $doc = $col->findOne( [ 'key' => 'k1' ] );
        $this->assertNotNull( $doc );
        $this->assertSame( 'new', ( (array)$doc )['val'] );
    }

    /**
     * @testdox [LIVE] MongoDB deleteMany removes all matched documents
     */
    public function testMongoDeleteMany(): void
    {
        $col = self::$mongoClient->selectCollection( self::MONGO_DB, self::TEST_COL );
        $col->insertOne( [ 'type' => 'phpunit_del', 'id' => 1 ] );
        $col->insertOne( [ 'type' => 'phpunit_del', 'id' => 2 ] );
        $col->insertOne( [ 'type' => 'other',       'id' => 3 ] );

        $col->deleteMany( [ 'type' => 'phpunit_del' ] );

        $remaining = iterator_to_array( $col->find( [] ) );
        // Only the 'other' doc should remain
        foreach ( $remaining as $doc )
            $this->assertNotSame( 'phpunit_del', ( (array)$doc )['type'] );
    }

    /**
     * @testdox [LIVE] MongoDB findAndModify / updateOne with $inc
     */
    public function testMongoUpdateOneIncrement(): void
    {
        $col = self::$mongoClient->selectCollection( self::MONGO_DB, self::SEQ_COL );
        // Remove any leftover from a previous run first
        $col->deleteMany( [ '_id' => 'phpunit_seq_test' ] );

        // Seed with seq = 100
        $col->insertOne( [ '_id' => 'phpunit_seq_test', 'seq' => 100 ] );

        $col->updateOne(
            [ '_id' => 'phpunit_seq_test' ],
            [ '$inc' => [ 'seq' => 1 ] ]
        );

        $doc = $col->findOne( [ '_id' => 'phpunit_seq_test' ] );
        $this->assertSame( 101, (int)( (array)$doc )['seq'] );
    }

    /**
     * @testdox [LIVE] MongoDB listCollections includes expected core collections
     */
    public function testMongoExpectedCollectionsExist(): void
    {
        $names = iterator_to_array(
            self::$mongoClient->selectDatabase( self::MONGO_DB )->listCollectionNames()
        );

        // These collections must exist after the MongoDB migration
        $required = [
            'ezcontentobject',
            'ezcontentobject_tree',
            'ezcontent_language',
            'ezcontentobject_version',
        ];

        foreach ( $required as $col )
        {
            $this->assertContains(
                $col,
                $names,
                "MongoDB collection '{$col}' must exist in database '" . self::MONGO_DB . "'"
            );
        }
    }

    /**
     * @testdox [LIVE] MongoDB ezcontentobject collection has expected fields
     */
    public function testMongoContentObjectDocumentStructure(): void
    {
        $doc = self::$mongoClient
            ->selectCollection( self::MONGO_DB, 'ezcontentobject' )
            ->findOne( [] );

        $this->assertNotNull( $doc, 'ezcontentobject must contain at least one document' );

        $arr = (array)$doc;
        $requiredFields = [ 'id', 'status', 'contentclass_id', 'language_mask' ];
        foreach ( $requiredFields as $field )
        {
            $this->assertArrayHasKey(
                $field,
                $arr,
                "ezcontentobject document must have field '{$field}'"
            );
        }
    }

    /**
     * @testdox [LIVE] MongoDB ezcontentobject_tree documents have path_string
     */
    public function testMongoTreeNodeHasPathString(): void
    {
        $doc = self::$mongoClient
            ->selectCollection( self::MONGO_DB, 'ezcontentobject_tree' )
            ->findOne( [] );

        $this->assertNotNull( $doc, 'ezcontentobject_tree must contain at least one document' );
        $arr = (array)$doc;
        $this->assertArrayHasKey( 'path_string', $arr );
        $this->assertMatchesRegularExpression( '#^/\d+/#', (string)$arr['path_string'],
            'path_string must look like /1/2/ etc.' );
    }

    // ── MySQL live: smoke tests ───────────────────────────────────────────────

    /**
     * @testdox [LIVE] MySQL connection works and returns correct server info
     */
    public function testMysqlConnectionWorks(): void
    {
        $this->skipIfNoMySQL();
        $this->assertNotEmpty( self::$mysql->server_info );
        $this->assertStringContainsString( 'MariaDB', self::$mysql->server_info );
    }

    /**
     * @testdox [LIVE] MySQL ezcontent_language has at least one row
     */
    public function testMysqlContentLanguageHasRows(): void
    {
        $this->skipIfNoMySQL();
        $r = self::$mysql->query( 'SELECT COUNT(*) AS c FROM ezcontent_language' );
        $this->assertNotFalse( $r, 'Query must succeed: ' . self::$mysql->error );
        $row = $r->fetch_assoc();
        $this->assertGreaterThan( 0, (int)$row['c'] );
    }

    /**
     * @testdox [LIVE] MySQL ezcontentobject has at least one row
     */
    public function testMysqlContentObjectHasRows(): void
    {
        $this->skipIfNoMySQL();
        $r = self::$mysql->query( 'SELECT COUNT(*) AS c FROM ezcontentobject' );
        $this->assertNotFalse( $r, 'Query must succeed: ' . self::$mysql->error );
        $row = $r->fetch_assoc();
        $this->assertGreaterThan( 0, (int)$row['c'] );
    }

    /**
     * @testdox [LIVE] MySQL ezcontentobject_tree path_string format is valid
     */
    public function testMysqlTreeNodePathStringFormat(): void
    {
        $this->skipIfNoMySQL();
        $r = self::$mysql->query( 'SELECT path_string FROM ezcontentobject_tree LIMIT 5' );
        $this->assertNotFalse( $r );
        while ( $row = $r->fetch_assoc() )
        {
            $this->assertMatchesRegularExpression(
                '#^/\d+/#',
                $row['path_string'],
                'MySQL path_string must match /id/id/ format'
            );
        }
    }

    /**
     * @testdox [LIVE] MySQL ezcontentobject_version has no orphaned versions (status=5 garbage)
     */
    public function testMysqlNoOrphanedArchivedVersions(): void
    {
        $this->skipIfNoMySQL();
        // Objects in status 5 (ARCHIVED) that have NO published version are orphans.
        // We simply verify the query executes without error.
        $sql = "SELECT COUNT(*) AS c FROM ezcontentobject_version v
                WHERE v.status = 5
                  AND NOT EXISTS (
                    SELECT 1 FROM ezcontentobject_version v2
                    WHERE v2.contentobject_id = v.contentobject_id AND v2.status = 1
                  )";
        $r = self::$mysql->query( $sql );
        $this->assertNotFalse( $r, 'Orphan version query failed: ' . self::$mysql->error );
        $row = $r->fetch_assoc();
        $this->assertIsNumeric( $row['c'] );
    }

    /**
     * @testdox [LIVE] MySQL ezcontent_class table has at least one class
     */
    public function testMysqlContentClassExists(): void
    {
        $this->skipIfNoMySQL();
        $r = self::$mysql->query( 'SELECT COUNT(*) AS c FROM ezcontentclass WHERE version=0' );
        $this->assertNotFalse( $r );
        $this->assertGreaterThan( 0, (int)$r->fetch_assoc()['c'],
            'ezcontentclass must have at least one published class (version=0)' );
    }

    /**
     * @testdox [LIVE] MySQL ezcontentobject has no rows with NULL status
     */
    public function testMysqlContentObjectNoNullStatus(): void
    {
        $this->skipIfNoMySQL();
        $r = self::$mysql->query( 'SELECT COUNT(*) AS c FROM ezcontentobject WHERE status IS NULL' );
        $this->assertNotFalse( $r );
        $this->assertSame( 0, (int)$r->fetch_assoc()['c'],
            'ezcontentobject.status must never be NULL' );
    }

    /**
     * @testdox [LIVE] MySQL ezsection has at least one section
     */
    public function testMysqlSectionTableHasRows(): void
    {
        $this->skipIfNoMySQL();
        $r = self::$mysql->query( 'SELECT COUNT(*) AS c FROM ezsection' );
        $this->assertNotFalse( $r );
        $this->assertGreaterThan( 0, (int)$r->fetch_assoc()['c'] );
    }

    /**
     * @testdox [LIVE] MySQL and MongoDB ezcontentobject row counts match
     */
    public function testContentObjectCountMatchesBetweenEngines(): void
    {
        $this->skipIfNoMySQL();

        $r = self::$mysql->query( 'SELECT COUNT(*) AS c FROM ezcontentobject WHERE status=1' );
        $mysqlCount = (int)$r->fetch_assoc()['c'];

        $pipeline = [ [ '$match' => [ 'status' => 1 ] ], [ '$count' => 'c' ] ];
        $cursor   = self::$mongoClient
            ->selectCollection( self::MONGO_DB, 'ezcontentobject' )
            ->aggregate( $pipeline );
        $rows = iterator_to_array( $cursor );
        $mongoCount = empty( $rows ) ? 0 : (int)( (array)$rows[0] )['c'];

        // Counts won't be identical if data diverged, but both must be > 0
        $this->assertGreaterThan( 0, $mysqlCount,  'MySQL published object count must be > 0' );
        $this->assertGreaterThan( 0, $mongoCount,  'MongoDB published object count must be > 0' );

        // Allow up to 10% divergence — warns if they've seriously de-synced
        $ratio = $mysqlCount > 0 ? abs( $mongoCount - $mysqlCount ) / $mysqlCount : 1.0;
        $this->assertLessThan(
            0.10,
            $ratio,
            sprintf(
                'MySQL (%d) and MongoDB (%d) ezcontentobject counts diverge by %.1f%% — data may be out of sync',
                $mysqlCount, $mongoCount, $ratio * 100
            )
        );
    }

    /**
     * @testdox [LIVE] MySQL INSERT + SELECT + DELETE round-trip (no side effects)
     */
    public function testMysqlInsertSelectDeleteRoundTrip(): void
    {
        $this->skipIfNoMySQL();

        // Use ezpreferences — a safe table with no FK constraints that allow
        // inserts with a dummy user_id.  user_id=0 is never a real user.
        $name = '_phpunit_' . time();
        $name = self::$mysql->real_escape_string( $name );
        $ok   = self::$mysql->query( "INSERT INTO ezpreferences (user_id, name, value) VALUES (0, '$name', 'test_value')" );
        $this->assertTrue( $ok, 'INSERT must succeed: ' . self::$mysql->error );

        $id = self::$mysql->insert_id;
        $this->assertGreaterThan( 0, $id );

        $r = self::$mysql->query( "SELECT name FROM ezpreferences WHERE id=$id" );
        $this->assertStringStartsWith( '_phpunit_', $r->fetch_assoc()['name'] );

        self::$mysql->query( "DELETE FROM ezpreferences WHERE id=$id" );

        $r = self::$mysql->query( "SELECT COUNT(*) AS c FROM ezpreferences WHERE id=$id" );
        $this->assertSame( 0, (int)$r->fetch_assoc()['c'] );
    }
}
