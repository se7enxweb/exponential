<?php
/**
 * The import from a Cassandra or ScyllaDB keyspace (expCassandraReader with the import engine of expAccessImport, the
 * command exp:cassandra:import) against the live servers of expCassandraTestKit (skipped without them).
 *
 *  CI-01 - A table made outside Exponential (a composite key, a clustering order, uuid, timestamp, boolean, decimal,
 *          varint, blob, a list, a map, an index) is listed and described in the import's vocabulary
 *  CI-02 - Imported into SQLite: the table made with the mapped types and the key, every row written, the values
 *          (text, numbers, dates in UTC, booleans as 1/0, collections as JSON, a blob as base64)
 *  CI-03 - The modes: create refuses a table with rows, append adds, replace empties first
 *  CI-04 - 2,000 rows read page by page (a page size of 500) arrive complete
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 2 ) . '/datatable/expDataTableTestLoader.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdb/cassandra/expCassandraTestKit.php';
foreach ( array( 'expaccessreader', 'expaccessimport' ) as $file )
    require_once expDataTableTestLoader::root() . '/kernel/classes/msaccess/' . $file . '.php';
require_once expDataTableTestLoader::root() . '/kernel/classes/cassandra/expcassandrareader.php';

#[\PHPUnit\Framework\Attributes\Group('kernel')]
#[\PHPUnit\Framework\Attributes\Group('cassandra')]
#[\PHPUnit\Framework\Attributes\Group('cassandra-live')]
class expCassandraImportLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    /** @var string[] */
    private $files = array();

    /** @var expCassandraDB[] */
    private $open = array();

    public static function products()
    {
        return array( 'cassandra' => array( 'cassandra' ), 'scylladb' => array( 'scylladb' ) );
    }

    protected function setUp(): void
    {
        $this->saved = expCassandraTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        foreach ( $this->open as $db )
            $db->close();
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
        expKernelTableSchema::reset();
        expCassandraTestKit::restoreDebug( $this->saved );
    }

    private function source( $product, array $settings = array() )
    {
        $db = expCassandraTestKit::liveDriver( $product, $settings );
        if ( !$db instanceof expCassandraDB )
            $this->markTestSkipped( $db );
        $this->open[] = $db;
        return $db;
    }

    private function target()
    {
        $file = sys_get_temp_dir() . '/exp-cassandra-import-' . getmypid() . '-' . count( $this->files ) . '.sqlite';
        $this->files[] = $file;
        return new expPDODB( array( 'pdo_dsn' => 'sqlite:' . $file ) );
    }

    private function makeEvents( expCassandraDB $db )
    {
        foreach ( array( 'ci_events', 'ci_bulk' ) as $table )
            $this->assertTrue( $db->query( "DROP TABLE IF EXISTS $table" ) );
        $this->assertTrue( $db->query( 'CREATE TABLE ci_events (day date, at timeuuid, kind text, ok boolean, amount decimal, big varint, data blob, '
                                     . 'tags list<text>, attrs map<text, int>, note text, PRIMARY KEY ((day), at)) WITH CLUSTERING ORDER BY (at DESC)' ), $db->errorMessage() );
        $this->assertTrue( $db->query( 'CREATE INDEX IF NOT EXISTS ci_events_kind ON ci_events (kind)' ), $db->errorMessage() );
        $this->assertNotFalse( $db->cql( "INSERT INTO ci_events (day, at, kind, ok, amount, big, data, tags, attrs, note) VALUES ('2026-11-26', "
                                       . "50554d6e-29bb-11e5-b345-feff819cdc9f, 'login', true, 12.50, 123456789012345678901, 0xcafe, ['a', 'b'], {'x': 1}, 'Grüße')" ), $db->errorMessage() );
        $this->assertNotFalse( $db->cql( "INSERT INTO ci_events (day, at, kind, ok) VALUES ('2026-11-25', 50554d6e-29bb-11e5-b345-feff819cdc9e, 'logout', false)" ) );
        $db->forgetTables();
    }

    /** CI-01, CI-02 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testImport( $product )
    {
        $db = $this->source( $product );
        $this->makeEvents( $db );
        $reader = new expCassandraReader( $db );
        $this->assertContains( 'ci_events', $reader->tables() );
        $this->assertNotContains( expCassandraDB::SEQUENCE_TABLE, $reader->tables() );
        $description = $reader->table( 'ci_events' );
        $this->assertSame( array( 'day', 'at' ), $description['primary'] );
        $this->assertSame( array( 'datetime', 'guid', 'text', 'boolean', 'decimal', 'decimal', 'binary', 'memo', 'memo', 'text' ),
                           array_column( array_map( function ( $f ) { return $f; }, array( $description['fields']['day'], $description['fields']['at'], $description['fields']['kind'],
                                                                                              $description['fields']['ok'], $description['fields']['amount'], $description['fields']['big'],
                                                                                              $description['fields']['data'], $description['fields']['tags'], $description['fields']['attrs'],
                                                                                              $description['fields']['note'] ) ), 'type' ) );
        $this->assertSame( array( 'ci_events_kind' => array( 'unique' => false, 'fields' => array( 'kind' ) ) ), $description['indexes'] );

        $target = $this->target();
        $report = expAccessImport::run( $reader, $target, array( 'tables' => array( 'ci_events' ), 'prefix' => 'cs_' ) );
        $this->assertTrue( $report['ok'], json_encode( $report ) );
        $this->assertSame( array( 2, 2 ), array( $report['tables']['ci_events']['read'], $report['tables']['ci_events']['written'] ) );
        $row = $target->arrayQuery( "SELECT * FROM cs_ci_events WHERE kind = 'login'" )[0];
        $this->assertSame( '2026-11-26 00:00:00', $row['day'] );
        $this->assertSame( '50554d6e-29bb-11e5-b345-feff819cdc9f', $row['at'] );
        $this->assertEquals( 1, $row['ok'] );
        $this->assertEqualsWithDelta( 12.5, (float)$row['amount'], 0.0001 );
        $this->assertSame( base64_encode( "\xca\xfe" ), $row['data'] );
        $this->assertSame( '["a","b"]', $row['tags'] );
        $this->assertSame( '{"x":1}', $row['attrs'] );
        $this->assertSame( 'Grüße', $row['note'] );
        $this->assertEquals( 0, $target->arrayQuery( "SELECT ok FROM cs_ci_events WHERE kind = 'logout'" )[0]['ok'] );
    }

    /** CI-03 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testModes( $product )
    {
        $db = $this->source( $product );
        $this->makeEvents( $db );
        $reader = new expCassandraReader( $db );
        $target = $this->target();
        $options = array( 'tables' => array( 'ci_events' ) );
        $this->assertTrue( expAccessImport::run( $reader, $target, $options )['ok'] );
        expKernelTableSchema::reset();
        $again = expAccessImport::run( $reader, $target, $options );
        $this->assertFalse( $again['ok'] );
        $this->assertStringContainsString( 'use the mode append or replace', implode( ' ', $again['tables']['ci_events']['errors'] ) );
        expKernelTableSchema::reset();
        $this->assertTrue( expAccessImport::run( $reader, $target, $options + array( 'mode' => 'replace' ) )['ok'] );
        $this->assertEquals( 2, $target->arrayQuery( 'SELECT COUNT(*) AS n FROM ci_events' )[0]['n'] );
    }

    /** CI-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testManyRows( $product )
    {
        $db = $this->source( $product, array( 'PageSize' => 500 ) );
        $this->assertTrue( $db->query( 'DROP TABLE IF EXISTS ci_bulk' ) );
        $this->assertTrue( $db->query( 'CREATE TABLE ci_bulk (id int PRIMARY KEY, name text)' ) );
        $db->forgetTables();
        $started = microtime( true );
        for ( $i = 1; $i <= 2000; $i++ )
            $this->assertNotFalse( $db->cql( 'INSERT INTO ci_bulk (id, name) VALUES (?, ?)', array( $i, "row $i" ) ) );
        $written = microtime( true ) - $started;
        $reader = new expCassandraReader( $db );
        $target = $this->target();
        $started = microtime( true );
        $report = expAccessImport::run( $reader, $target, array( 'tables' => array( 'ci_bulk' ) ) );
        $imported = microtime( true ) - $started;
        $this->assertTrue( $report['ok'] );
        $this->assertSame( 2000, $report['tables']['ci_bulk']['written'] );
        $this->assertEquals( 2001000, $target->arrayQuery( 'SELECT SUM(id) AS s FROM ci_bulk' )[0]['s'] );
        fwrite( STDERR, sprintf( "\n%s: 2000 rows written by CQL in %.2f s, imported into SQLite in %.2f s\n", $product, $written, $imported ) );
    }
}
?>
