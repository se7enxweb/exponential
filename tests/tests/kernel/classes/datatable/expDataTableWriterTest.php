<?php
/**
 * Writing rows into a data table (expDataTableWriter; doc/eng-us/features/6.0/data-tables.md, "Writing rows"), on the
 * SQLite connection of the fixture (expDataTableSqliteFixture). The site's database is an empty SQLite database in
 * memory, so references (@object:, @node:) can only be checked for what has no target; resolving a real one is
 * checked in the sandbox integration against a copy of a site.
 *
 *  DW-01 - cast(): integer (and a date as a timestamp), float, text, bool, null, an array as JSON
 *  DW-02 - prepare(): an unknown field fails the row (refuse) or is left out (ignore); an empty key fails it
 *  DW-03 - the options: keys default to the class's keys, the counted key is left out of several keys, a key that
 *          is no field and a class that is not a persistent class are refused
 *  DW-04 - write() inserts, then upsert: unchanged and updated, with the ids of the rows
 *  DW-05 - mode insert skips an existing row, mode update skips a new one, mode replace writes it again
 *  DW-06 - a dry run counts and writes nothing
 *  DW-07 - a row that breaks the unique index fails alone; the other rows of its chunk are written
 *  DW-08 - a reference nothing has fails the row; values with a quote and a backslash are kept
 *  DW-09 - the class map route: a class without 'connection' put on the connection by [ClassSettings]
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableSqliteFixture.php';

class expDataTableWriterTest extends PHPUnit\Framework\TestCase
{
    use expDataTableSqliteFixture;

    protected function setUp(): void
    {
        $this->setUpFixture();
    }

    protected function tearDown(): void
    {
        $this->tearDownFixture();
    }

    private static function writer( array $options = array() )
    {
        return new expDataTableWriter( 'expDTFixtureRow', $options + array( 'keys' => array( 'remote_id' ) ) );
    }

    /** DW-01 */
    public function testCast()
    {
        $this->assertSame( 42, expDataTableWriter::cast( 'integer', '42' ) );
        $this->assertSame( 0, expDataTableWriter::cast( 'integer', 'abc' ) );
        $this->assertSame( strtotime( '2026-10-22 10:00:00' ), expDataTableWriter::cast( 'integer', '2026-10-22 10:00:00' ) );
        $this->assertSame( 1, expDataTableWriter::cast( 'integer', true ) );
        $this->assertSame( 9.5, expDataTableWriter::cast( 'float', '9.5' ) );
        $this->assertSame( 0.0, expDataTableWriter::cast( 'float', 'x' ) );
        $this->assertSame( '12', expDataTableWriter::cast( 'string', 12 ) );
        $this->assertNull( expDataTableWriter::cast( 'string', null ) );
        $this->assertSame( '{"a":1}', expDataTableWriter::cast( 'text', array( 'a' => 1 ) ) );
    }

    /** DW-02 */
    public function testPrepare()
    {
        $errors = array();
        $this->assertNull( self::writer()->prepare( array( 'remote_id' => 'a', 'bogus' => 1 ), $errors ) );
        $this->assertSame( array( "unknown field 'bogus'" ), $errors );
        $errors = array();
        $this->assertSame( array( 'remote_id' => 'a', 'price' => 2.0 ),
                           self::writer( array( 'unknown' => 'ignore' ) )->prepare( array( 'remote_id' => 'a', 'bogus' => 1, 'price' => '2' ), $errors ) );
        $errors = array();
        $this->assertNull( self::writer()->prepare( array( 'title' => 'no key' ), $errors ) );
        $this->assertSame( array( "the key field 'remote_id' is empty" ), $errors );
    }

    /** DW-03 */
    public function testOptions()
    {
        $this->assertSame( array( 'id' ), ( new expDataTableWriter( 'expDTFixtureRow' ) )->options()['keys'] );
        $this->assertSame( array( 'remote_id' ), ( new expDataTableWriter( 'expDTFixtureRow', array( 'keys' => array( 'id', 'remote_id' ) ) ) )->options()['keys'] );
        $options = self::writer( array( 'mode' => 'nonsense', 'chunk' => 0 ) )->options();
        $this->assertSame( 'upsert', $options['mode'] );
        $this->assertSame( 1, $options['chunk'] );
        try
        {
            self::writer( array( 'keys' => array( 'nope' ) ) );
            $this->fail( 'a key that is no field' );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertStringContainsString( "'nope'", $e->getMessage() );
        }
        try
        {
            new expDataTableWriter( 'stdClass' );
            $this->fail( 'not a persistent class' );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertStringContainsString( 'stdClass', $e->getMessage() );
        }
    }

    /** DW-04 */
    public function testInsertThenUpsert()
    {
        $result = self::writer()->write( array( array( 'remote_id' => 'a', 'title' => 'A', 'price' => '1.5' ),
                                                array( 'remote_id' => 'b', 'title' => 'B' ) ) );
        $this->assertSame( 2, $result['inserted'] );
        $this->assertSame( 0, $result['failed'], json_encode( $result['errors'] ) );
        $this->assertSame( array( 0 => array( 'id' => 1 ), 1 => array( 'id' => 2 ) ), array_map( function ( $k ) { return array( 'id' => (int)$k['id'] ); }, $result['ids'] ) );
        $rows = self::fixtureRows();
        $this->assertSame( 'A', $rows['a']['title'] );
        $this->assertEquals( 1.5, (float)$rows['a']['price'] );
        $result = self::writer()->write( array( array( 'remote_id' => 'a', 'title' => 'A', 'price' => 1.5 ),
                                                array( 'remote_id' => 'b', 'title' => 'B2' ) ) );
        $this->assertSame( 1, $result['unchanged'] );
        $this->assertSame( 1, $result['updated'] );
        $this->assertSame( 0, $result['inserted'] );
        $this->assertSame( 'B2', self::fixtureRows()['b']['title'] );
        $this->assertCount( 2, self::fixtureRows() );
    }

    /** DW-05 */
    public function testModes()
    {
        self::writer()->write( array( array( 'remote_id' => 'a', 'title' => 'A', 'node_id' => 7 ) ) );
        $result = self::writer( array( 'mode' => 'insert' ) )->write( array( array( 'remote_id' => 'a', 'title' => 'X' ), array( 'remote_id' => 'n', 'title' => 'N' ) ) );
        $this->assertSame( 1, $result['skipped'] );
        $this->assertSame( 1, $result['inserted'] );
        $this->assertSame( 'A', self::fixtureRows()['a']['title'] );
        $result = self::writer( array( 'mode' => 'update' ) )->write( array( array( 'remote_id' => 'a', 'title' => 'U' ), array( 'remote_id' => 'm', 'title' => 'M' ) ) );
        $this->assertSame( 1, $result['skipped'] );
        $this->assertSame( 1, $result['updated'] );
        $this->assertArrayNotHasKey( 'm', self::fixtureRows() );
        $oldID = (int)self::fixtureRows()['a']['id'];
        $result = self::writer( array( 'mode' => 'replace' ) )->write( array( array( 'remote_id' => 'a', 'title' => 'U' ) ) );
        $this->assertSame( 1, $result['updated'] );
        $rows = self::fixtureRows();
        $this->assertNotSame( $oldID, (int)$rows['a']['id'], 'written again: a new id' );
        $this->assertSame( '0', (string)$rows['a']['node_id'], 'written again: what the row does not give is the default' );
    }

    /** DW-06 */
    public function testDryRun()
    {
        self::writer()->write( array( array( 'remote_id' => 'a', 'title' => 'A' ) ) );
        $result = self::writer( array( 'dry_run' => true ) )->write( array( array( 'remote_id' => 'a', 'title' => 'changed' ),
                                                                           array( 'remote_id' => 'z', 'title' => 'Z' ) ) );
        $this->assertSame( 1, $result['updated'] );
        $this->assertSame( 1, $result['inserted'] );
        $rows = self::fixtureRows();
        $this->assertSame( 'A', $rows['a']['title'] );
        $this->assertArrayNotHasKey( 'z', $rows );
    }

    /** DW-07 */
    public function testABadRowFailsAlone()
    {
        // the database refuses the second row (eZPersistentObject writes INSERT OR REPLACE on SQLite, so a unique index
        // would replace the other row instead of refusing: a trigger refuses it whatever the conflict clause)
        self::fixtureDB()->query( "CREATE TRIGGER t691_refuse BEFORE INSERT ON t691_m2_fixture WHEN new.title = 'bad' BEGIN SELECT RAISE( ABORT, 'refused' ); END" );
        $writer = new expDataTableWriter( 'expDTFixtureRow', array( 'keys' => array( 'remote_id' ), 'chunk' => 10 ) );
        $result = $writer->write( array( array( 'remote_id' => 'one', 'title' => 'fine' ),
                                         array( 'remote_id' => 'two', 'title' => 'bad' ),
                                         array( 'remote_id' => 'three', 'title' => 'fine too' ) ) );
        $this->assertSame( 2, $result['inserted'], json_encode( $result ) );
        $this->assertSame( 1, $result['failed'] );
        $this->assertStringStartsWith( 'row 2: ', $result['errors'][0] );
        $this->assertStringContainsString( 'refused', $result['errors'][0] );
        $this->assertSame( array( 'one', 'three' ), array_keys( self::fixtureRows() ) );
        $this->assertSame( 0, self::fixtureDB()->transactionCounter(), 'no transaction left open' );
    }

    /** DW-07 (the kernel's SQLite insert) */
    public function testSqliteInsertRefusesAUniqueConflict()
    {
        // documents eZPersistentObject's insert on SQLite: a row whose unique column another row has is refused, as
        // MySQL and PostgreSQL refuse it (it no longer replaces that row; kernel commit 85332302b6); match on the
        // unique column to update the row instead
        $writer = new expDataTableWriter( 'expDTFixtureRow', array( 'keys' => array( 'title' ) ) );
        $result = $writer->write( array( array( 'remote_id' => 'same', 'title' => 'one' ), array( 'remote_id' => 'same', 'title' => 'two' ) ) );
        $this->assertSame( 1, $result['inserted'], json_encode( $result ) );
        $this->assertSame( 1, $result['failed'], json_encode( $result ) );
        $rows = self::fixtureRows();
        $this->assertCount( 1, $rows );
        $this->assertSame( 'one', $rows['same']['title'] );
    }

    /** DW-08 */
    public function testReferencesAndEscaping()
    {
        $this->fixtureSite->query( 'CREATE TABLE ezcontentobject ( id INTEGER PRIMARY KEY, remote_id TEXT )' );
        $result = self::writer()->write( array( array( 'remote_id' => 'r', 'title' => 'R', 'node_id' => '@object:nobody-has-this' ),
                                                array( 'remote_id' => "q'\\b", 'title' => "O'Brien \\ \"x\"" ) ) );
        $this->assertSame( 1, $result['failed'] );
        $this->assertStringContainsString( 'nothing has the reference @object:nobody-has-this', $result['errors'][0] );
        $this->assertSame( 1, $result['inserted'] );
        $this->assertSame( "O'Brien \\ \"x\"", self::fixtureRows()["q'\\b"]['title'] );
        $this->assertTrue( expDataTableRelation::isReference( '@node:x' ) );
        $this->assertNull( expDataTableRelation::resolveReference( 'node:x', 'node' ) );
    }

    /** DW-09 */
    public function testClassMapRoute()
    {
        expDTFixtureRow::$connection = '';
        $definition = expDTFixtureRow::definition();
        unset( $definition['connection'] );
        $this->assertSame( eZDB::instance(), eZPersistentObject::dbFor( $definition ) );
        expDataConnections::setClassMap( array( 'expDTFixtureRow' => 't691m2' ) );
        $this->assertSame( self::fixtureDB(), eZPersistentObject::dbFor( $definition ) );
        $result = ( new expDataTableWriter( 'expDTFixtureRow', array( 'keys' => array( 'remote_id' ) ) ) )->write( array( array( 'remote_id' => 'cm', 'title' => 'by the map' ) ) );
        $this->assertSame( 1, $result['inserted'], json_encode( $result ) );
        $this->assertSame( 'by the map', self::fixtureRows()['cm']['title'] );
    }
}
