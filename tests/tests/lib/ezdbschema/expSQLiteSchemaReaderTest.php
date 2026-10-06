<?php
/**
 * The SQLite schema reader (eZSQLiteSchema) on a throwaway SQLite file under var/tmp, and what the database
 * consistency check compares on SQLite. No site database is opened or written.
 *
 *  SR-01 - An INTEGER PRIMARY KEY is auto_increment: bare, and "integer NOT NULL PRIMARY KEY AUTOINCREMENT"
 *  SR-02 - What is not auto_increment stays as it was: integer NOT NULL PRIMARY KEY without AUTOINCREMENT,
 *          a composite key, text and longtext as declared
 *  SR-03 - The comparison takes out what SQLite cannot tell apart (text = longtext, int(11) of an auto_increment,
 *          no default written as false or left out), so a table as the hand-written SQLite files create it
 *          matches its schema file
 *  SR-04 - Real differences still show: a missing field, another type, another varchar length, NOT NULL, another
 *          default, DEFAULT NULL against DEFAULT 0, an index
 *  SR-05 - The SQL of a difference is written from the shipped definition, not from the normalized one
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$root = dirname( __DIR__, 4 );
foreach ( array( 'lib/ezdbschema/classes/ezdbschemainterface.php', 'lib/ezdbschema/classes/ezsqliteschema.php',
                 'lib/ezdbschema/classes/ezdbschemachecker.php', 'kernel/private/classes/expschemaconsistencyreport.php' ) as $file )
    require_once $root . '/' . $file;

/** Enough of eZDBInterface for the reader: arrayQuery() over an SQLite3 file */
class expSQLiteSchemaReaderTestDB
{
    public $db;

    public function __construct( $file )
    {
        $this->db = new SQLite3( $file );
    }

    public function arrayQuery( $sql )
    {
        $rows = array();
        $result = $this->db->query( $sql );
        while ( $result && ( $row = $result->fetchArray( SQLITE3_ASSOC ) ) )
            $rows[] = $row;
        return $rows;
    }
}

/** The reader without the INI-driven transformation, which needs settings */
class expSQLiteSchemaReaderTestSchema extends eZSQLiteSchema
{
    function transformSchema( &$schema, $toLocal )
    {
        return true;
    }
}

class expSQLiteSchemaReaderTest extends PHPUnit\Framework\TestCase
{
    private $file;
    private $db;

    protected function setUp(): void
    {
        $this->file = dirname( __DIR__, 4 ) . '/var/tmp/phpunit-sqlite-schema-' . getmypid() . '-' . mt_rand() . '.sqlite';
        $this->db = new expSQLiteSchemaReaderTestDB( $this->file );
        $this->db->db->exec( "CREATE TABLE expmail_category (
              description text,
              id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              identifier varchar(100) NOT NULL DEFAULT '',
              priority integer NOT NULL DEFAULT 0
            )" );
        $this->db->db->exec( "CREATE TABLE ezbare ( id INTEGER PRIMARY KEY AUTOINCREMENT, body longtext )" );
        $this->db->db->exec( "CREATE TABLE ezplainkey ( id integer NOT NULL PRIMARY KEY, name varchar(50) DEFAULT NULL )" );
        $this->db->db->exec( "CREATE TABLE ezversioned ( id INTEGER, version INTEGER(11) NOT NULL DEFAULT '0', PRIMARY KEY ( id, version ) )" );
        $this->db->db->exec( "CREATE INDEX expmail_category__identifier ON expmail_category ( identifier )" );
    }

    protected function tearDown(): void
    {
        $this->db->db->close();
        if ( is_file( $this->file ) )
            unlink( $this->file );
    }

    private function read()
    {
        $reader = new expSQLiteSchemaReaderTestSchema( array( 'instance' => $this->db ) );
        return $reader->schema( array( 'format' => 'local' ) );
    }

    /** The table as share/db_schema.dba describes it */
    private function shippedCategory()
    {
        return array(
            'name' => 'expmail_category',
            'fields' => array(
                'description' => array( 'type' => 'longtext' ),
                'id' => array( 'type' => 'auto_increment', 'length' => 11, 'default' => false ),
                'identifier' => array( 'length' => 100, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'priority' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
            ),
            'indexes' => array(
                'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                'expmail_category__identifier' => array( 'type' => 'non-unique', 'fields' => array( 'identifier' ) ),
            ),
        );
    }

    private function diff( array $shipped )
    {
        $current = $this->read();
        return eZDbSchemaChecker::diff( eZSQLiteSchema::normalizeForComparison( array( 'expmail_category' => $current['expmail_category'] ) ),
                                        eZSQLiteSchema::normalizeForComparison( array( 'expmail_category' => $shipped ) ) );
    }

    /** SR-01 */
    public function testIntegerPrimaryKeysAreAutoIncrement()
    {
        $schema = $this->read();
        $this->assertSame( array( 'type' => 'auto_increment', 'default' => false ), $schema['expmail_category']['fields']['id'] );
        $this->assertSame( array( 'type' => 'auto_increment', 'default' => false ), $schema['ezbare']['fields']['id'] );
        $this->assertSame( array( 'type' => 'primary', 'fields' => array( 'id' ) ), $schema['expmail_category']['indexes']['PRIMARY'] );
    }

    /** SR-02 */
    public function testOtherColumnsAsBefore()
    {
        $schema = $this->read();
        // no AUTOINCREMENT: SQLite does not number it, so it is not reported as if it did
        $this->assertSame( 'int', $schema['ezplainkey']['fields']['id']['type'] );
        $this->assertSame( '1', $schema['ezplainkey']['fields']['id']['not_null'] );
        // a composite key keeps its first member auto_increment, as before
        $this->assertSame( 'auto_increment', $schema['ezversioned']['fields']['id']['type'] );
        $this->assertSame( 'int', $schema['ezversioned']['fields']['version']['type'] );
        // the reader reports the declared type
        $this->assertSame( 'text', $schema['expmail_category']['fields']['description']['type'] );
        $this->assertSame( 'longtext', $schema['ezbare']['fields']['body']['type'] );
        $this->assertSame( 100, $schema['expmail_category']['fields']['identifier']['length'] );
    }

    /** SR-03 */
    public function testComparisonMatchesTheShippedTable()
    {
        $this->assertSame( array(), $this->diff( $this->shippedCategory() ) );

        $normalized = eZSQLiteSchema::normalizeForComparison( array( '_info' => array( 'format' => 'local' ),
            't' => array( 'fields' => array( 'a' => array( 'type' => 'mediumtext', 'length' => 5, 'default' => null ),
                                             'b' => array( 'type' => 'int', 'default' => false ),
                                             'c' => array( 'type' => 'int', 'default' => null ) ), 'indexes' => array() ) ) );
        $this->assertSame( array( 'type' => 'text' ), $normalized['t']['fields']['a'] );
        $this->assertSame( array( 'type' => 'int' ), $normalized['t']['fields']['b'] );
        $this->assertSame( array( 'type' => 'int', 'default' => null ), $normalized['t']['fields']['c'], 'DEFAULT NULL stays' );
        $this->assertSame( array( 'format' => 'local' ), $normalized['_info'] );
    }

    /** SR-04 */
    public function testRealDifferencesStillShow()
    {
        $cases = array(
            'missing field' => function ( &$t ) { $t['fields']['extra'] = array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ); },
            'another type' => function ( &$t ) { $t['fields']['priority']['type'] = 'varchar'; },
            'another varchar length' => function ( &$t ) { $t['fields']['identifier']['length'] = 255; },
            'NOT NULL' => function ( &$t ) { unset( $t['fields']['identifier']['not_null'] ); },
            'another default' => function ( &$t ) { $t['fields']['priority']['default'] = 5; },
            'DEFAULT NULL against 0' => function ( &$t ) { unset( $t['fields']['priority']['not_null'] ); $t['fields']['priority']['default'] = null; },
            'an index' => function ( &$t ) { $t['indexes']['expmail_category__priority'] = array( 'type' => 'non-unique', 'fields' => array( 'priority' ) ); },
            'text against varchar' => function ( &$t ) { $t['fields']['description'] = array( 'length' => 255, 'type' => 'varchar', 'default' => null ); },
        );
        foreach ( $cases as $what => $change )
        {
            $shipped = $this->shippedCategory();
            $change( $shipped );
            $diff = $this->diff( $shipped );
            $this->assertNotEmpty( $diff, $what . ' is a difference' );
            $this->assertArrayHasKey( 'expmail_category', $diff['table_changes'], $what );
        }
    }

    /** SR-05 */
    public function testSqlIsWrittenFromTheShippedDefinition()
    {
        $shipped = array( 'expmail_category' => $this->shippedCategory(),
                          'eznew' => array( 'name' => 'eznew', 'fields' => array( 'body' => array( 'type' => 'longtext' ) ), 'indexes' => array() ) );
        $shipped['expmail_category']['fields']['notes'] = array( 'type' => 'longtext' );
        $current = $this->read();
        $diff = eZDbSchemaChecker::diff( eZSQLiteSchema::normalizeForComparison( array( 'expmail_category' => $current['expmail_category'] ) ),
                                         eZSQLiteSchema::normalizeForComparison( $shipped ) );
        $this->assertSame( array( 'type' => 'text' ), $diff['table_changes']['expmail_category']['added_fields']['notes'] );
        $restored = expSchemaConsistencyReport::restoreDefinitions( $diff, $shipped );
        $this->assertSame( array( 'type' => 'longtext' ), $restored['table_changes']['expmail_category']['added_fields']['notes'] );
        $this->assertSame( $shipped['eznew'], $restored['new_tables']['eznew'] );
        $this->assertFalse( expSchemaConsistencyReport::restoreDefinitions( false, $shipped ) );
    }
}
