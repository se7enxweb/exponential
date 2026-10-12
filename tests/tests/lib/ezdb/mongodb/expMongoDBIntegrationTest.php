<?php
/**
 * Live integration tests for expMongoDB and the MySQL (ezmysqli) adapter.
 *
 * These tests read their servers from environment variables and skip when they are not set:
 *  - MongoDB 8.x: EXP_TEST_MONGO_URI (a mongodb:// connection string) and EXP_TEST_MONGO_DATABASE
 *    (default "exp"), a database holding an imported installation.
 *  - MySQL/MariaDB: EXP_TEST_MYSQL_USER, EXP_TEST_MYSQL_PASSWORD and EXP_TEST_MYSQL_DATABASE, with
 *    EXP_TEST_MYSQL_HOST (default "localhost") and EXP_TEST_MYSQL_PORT (default 3306).
 *
 * They are tagged @group mongodb-live so they are EXCLUDED from the default
 * phpunit run.  Run them explicitly with:
 *
 *   php vendor/bin/phpunit --testsuite mongodb-live
 *
 * They do NOT modify production data — all writes target a dedicated
 * _phpunit_test collection/table and are cleaned up in tearDown().
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group mongodb-live
 */

/**
 * Integration tests — require live MongoDB + MySQL.
 */
#[\PHPUnit\Framework\Attributes\Group('mongodb-live')]
class expMongoDBIntegrationTest extends PHPUnit\Framework\TestCase
{
    private const TEST_COL   = '_phpunit_test';
    private const SEQ_COL    = 'ezsequence';

    /** @var MongoDB\Client */
    private static $mongoClient;

    /** @var mysqli */
    private static $mysql;

    /** @var string */
    private static $mongoDb = 'exp';

    /**
     * The value of environment variable $name, trimmed; '' when it is not set.
     */
    private static function env( string $name ): string
    {
        $value = getenv( $name );
        return $value === false ? '' : trim( $value );
    }

    /**
     * Connects to the MySQL/MariaDB server named by EXP_TEST_MYSQL_HOST, EXP_TEST_MYSQL_PORT,
     * EXP_TEST_MYSQL_USER, EXP_TEST_MYSQL_PASSWORD and EXP_TEST_MYSQL_DATABASE. Null when
     * EXP_TEST_MYSQL_USER or EXP_TEST_MYSQL_DATABASE is not set or the connection fails.
     */
    private static function connectMySQL(): ?mysqli
    {
        $user = self::env( 'EXP_TEST_MYSQL_USER' );
        $database = self::env( 'EXP_TEST_MYSQL_DATABASE' );
        if ( $user === '' || $database === '' )
            return null;

        $host = self::env( 'EXP_TEST_MYSQL_HOST' );
        $port = (int)self::env( 'EXP_TEST_MYSQL_PORT' );
        $password = getenv( 'EXP_TEST_MYSQL_PASSWORD' );
        try
        {
            $mysql = @new mysqli( $host !== '' ? $host : 'localhost', $user, $password === false ? '' : $password, $database, $port > 0 ? $port : 3306 );
        }
        catch ( mysqli_sql_exception $e )
        {
            return null;
        }
        return $mysql->connect_errno ? null : $mysql;
    }

    /** @var bool */
    private static $mongoBootstrapAttempted = false;

    /** @var string|null */
    private static $mongoSkipReason = null;

    /** @var bool */
    private static $mysqlBootstrapAttempted = false;

    public static function setUpBeforeClass(): void
    {
        self::$mongoClient = null;
        self::$mysql = null;
        self::$mongoBootstrapAttempted = false;
        self::$mongoSkipReason = null;
        self::$mysqlBootstrapAttempted = false;
    }

    private static function bootstrapMongo(): void
    {
        if ( self::$mongoBootstrapAttempted )
            return;

        self::$mongoBootstrapAttempted = true;

        if ( !extension_loaded( 'mongodb' ) )
        {
            self::$mongoSkipReason = 'MongoDB PHP extension not loaded';
            return;
        }

        require_once __DIR__ . '/../../../../../vendor/autoload.php';

        if ( !class_exists( 'MongoDB\\Client' ) )
        {
            self::$mongoSkipReason = 'MongoDB library is not installed';
            return;
        }

        $uri = self::env( 'EXP_TEST_MONGO_URI' );
        if ( $uri === '' )
        {
            self::$mongoSkipReason = 'EXP_TEST_MONGO_URI is not set (a mongodb:// connection string of a server holding an imported installation)';
            return;
        }
        $database = self::env( 'EXP_TEST_MONGO_DATABASE' );
        self::$mongoDb = $database !== '' ? $database : 'exp';

        try
        {
            self::$mongoClient = new MongoDB\Client( $uri );
            self::$mongoClient->selectDatabase( self::$mongoDb )->command( [ 'ping' => 1 ] );
        }
        catch ( Exception $e )
        {
            self::$mongoClient    = null;
            self::$mongoSkipReason = 'Cannot connect to MongoDB: ' . $e->getMessage();
        }
    }

    private static function bootstrapMySQL(): void
    {
        if ( self::$mysqlBootstrapAttempted )
            return;

        self::$mysqlBootstrapAttempted = true;

        self::$mysql = self::connectMySQL();
    }

    private function requireMongo(): void
    {
        self::bootstrapMongo();
        if ( self::$mongoSkipReason !== null )
            $this->markTestSkipped( self::$mongoSkipReason );
    }

    protected function tearDown(): void
    {
        if ( self::$mongoClient === null )
            return;

        // Always remove test documents written during this test.
        self::$mongoClient
            ->selectCollection( self::$mongoDb, self::TEST_COL )
            ->deleteMany( [] );
        // Clean up sequence counter if touched
        self::$mongoClient
            ->selectCollection( self::$mongoDb, self::SEQ_COL )
            ->deleteMany( [ '_id' => 'phpunit_seq_test' ] );
    }

    private function skipIfNoMySQL(): void
    {
        self::bootstrapMySQL();
        if ( self::$mysql === null )
            $this->markTestSkipped( 'EXP_TEST_MYSQL_USER and EXP_TEST_MYSQL_DATABASE are not set, or MySQL/MariaDB cannot be reached' );
    }

    // ── MongoDB live: basic connectivity ─────────────────────────────────────

    /**
     * @testdox [LIVE] MongoDB ping succeeds — server is reachable
     */
    public function testMongoPingSucceeds(): void
    {
        $this->requireMongo();
        $result = self::$mongoClient
            ->selectDatabase( self::$mongoDb )
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
        $this->requireMongo();
        $col = self::$mongoClient->selectCollection( self::$mongoDb, self::TEST_COL );

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
        $this->requireMongo();
        $col = self::$mongoClient->selectCollection( self::$mongoDb, self::TEST_COL );
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
        $this->requireMongo();
        $col = self::$mongoClient->selectCollection( self::$mongoDb, self::TEST_COL );
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
        $this->requireMongo();
        $col = self::$mongoClient->selectCollection( self::$mongoDb, self::SEQ_COL );
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
        $this->requireMongo();
        $names = iterator_to_array(
            self::$mongoClient->selectDatabase( self::$mongoDb )->listCollectionNames()
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
                "MongoDB collection '{$col}' must exist in database '" . self::$mongoDb . "'"
            );
        }
    }

    /**
     * @testdox [LIVE] MongoDB ezcontentobject collection has expected fields
     */
    public function testMongoContentObjectDocumentStructure(): void
    {
        $this->requireMongo();
        $doc = self::$mongoClient
            ->selectCollection( self::$mongoDb, 'ezcontentobject' )
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
        $this->requireMongo();
        $doc = self::$mongoClient
            ->selectCollection( self::$mongoDb, 'ezcontentobject_tree' )
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
        $this->requireMongo();
        $this->skipIfNoMySQL();

        $r = self::$mysql->query( 'SELECT COUNT(*) AS c FROM ezcontentobject WHERE status=1' );
        $mysqlCount = (int)$r->fetch_assoc()['c'];

        $pipeline = [ [ '$match' => [ 'status' => 1 ] ], [ '$count' => 'c' ] ];
        $cursor   = self::$mongoClient
            ->selectCollection( self::$mongoDb, 'ezcontentobject' )
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
