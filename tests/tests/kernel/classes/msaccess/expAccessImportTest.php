<?php
/**
 * The Access import (kernel/classes/msaccess/), without Access: a reader in memory and a SQLite target.
 *
 *  AI-01 - name(): ASCII lower case, umlauts spelled out, prefix, keywords with a trailing _, a leading digit, length
 *  AI-02 - dbaField(): every Access type to its .dba field
 *  AI-03 - schema(): target names, the AutoNumber as the key (the Access key kept as a unique index), indexes on
 *          long text left out, a second AutoNumber a plain integer, binary noted, unknown tables noted
 *  AI-04 - parseSchema(): mdb-schema's MySQL output (spaces in names, auto_increment, NOT NULL, keys and indexes)
 *  AI-05 - literal(): numbers bare, text quoted, binary as base64, NULL
 *  AI-06 - run() into SQLite: tables made, rows copied with their ids, NULL kept, a bad row reported while the others
 *          arrive, the mode create refuses a table with rows, append adds, replace replaces
 *  AI-07 - expAccessReader::date() and the reader factory's refusal without a reader
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/datatable/expDataTableTestLoader.php';
foreach ( array( 'expaccessreader', 'expaccessmdbtoolsreader', 'expaccessodbcreader', 'expaccessimport' ) as $file )
    require_once expDataTableTestLoader::root() . '/kernel/classes/msaccess/' . $file . '.php';

/** A reader over arrays */
class expAccessFakeReader extends expAccessReader
{
    public $tables = array();
    public $rows = array();

    public function __construct( array $tables, array $rows )
    {
        $this->file = 'memory.accdb';
        $this->tables = $tables;
        $this->rows = $rows;
    }

    public function name()
    {
        return 'fake';
    }

    public function tables()
    {
        return array_keys( $this->tables );
    }

    public function table( $table )
    {
        return $this->tables[$table];
    }

    public function rows( $table, callable $each )
    {
        foreach ( isset( $this->rows[$table] ) ? $this->rows[$table] : array() as $row )
            $each( $row );
        return isset( $this->rows[$table] ) ? count( $this->rows[$table] ) : 0;
    }
}

class expAccessImportTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $file;

    private $saved = array();

    protected function setUp(): void
    {
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
    }

    protected function tearDown(): void
    {
        if ( $this->file )
            foreach ( array( '', '-wal', '-shm', '-journal' ) as $suffix )
                if ( is_file( $this->file . $suffix ) )
                    unlink( $this->file . $suffix );
        foreach ( $this->saved as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
        expKernelTableSchema::reset();
    }

    private static function field( $type, $length = 0, $notNull = false )
    {
        return array( 'type' => $type, 'length' => $length, 'scale' => 0, 'not_null' => $notNull || $type === 'counter', 'auto' => $type === 'counter' );
    }

    private static function reader()
    {
        return new expAccessFakeReader( array(
            'Order Details' => array( 'fields' => array( 'OrderID' => self::field( 'integer' ), 'ProductID' => self::field( 'integer', 0, true ),
                                                         'UnitPrice' => self::field( 'currency', 0, true ), 'Quantity' => self::field( 'smallint' ) ),
                                      'primary' => array( 'OrderID', 'ProductID' ),
                                      'indexes' => array( 'ProductID' => array( 'unique' => false, 'fields' => array( 'ProductID' ) ) ) ),
            'Umsätze' => array( 'fields' => array( 'Nr' => self::field( 'counter' ), 'Größe' => self::field( 'text', 50 ), 'Order' => self::field( 'double' ),
                                                   'Notiz' => self::field( 'memo' ), 'Bild' => self::field( 'binary' ), 'Datum' => self::field( 'datetime' ),
                                                   'Aktiv' => self::field( 'boolean', 0, true ), 'Zweit' => self::field( 'counter' ) ),
                                'primary' => array( 'Größe' ),
                                'indexes' => array( 'Notiz' => array( 'unique' => false, 'fields' => array( 'Notiz' ) ),
                                                    'Datum' => array( 'unique' => true, 'fields' => array( 'Datum', 'Aktiv' ) ) ) ),
        ), array(
            'Order Details' => array( array( 'OrderID' => 10248, 'ProductID' => 11, 'UnitPrice' => '14.0000', 'Quantity' => 12 ),
                                      array( 'OrderID' => 10248, 'ProductID' => 42, 'UnitPrice' => '9.8000', 'Quantity' => null ),
                                      array( 'OrderID' => 10248, 'ProductID' => 11, 'UnitPrice' => '1', 'Quantity' => 1 ),
                                      array( 'OrderID' => 10249, 'ProductID' => 14, 'UnitPrice' => '18.6', 'Quantity' => 9 ) ),
            'Umsätze' => array( array( 'Nr' => 7, 'Größe' => "O'Neil ä 😀", 'Order' => 1.5, 'Notiz' => str_repeat( 'x', 5000 ), 'Bild' => new expAccessBinary( "\x00\xff\x10" ),
                                       'Datum' => '2026-11-25 10:11:12', 'Aktiv' => 1, 'Zweit' => 3 ),
                                array( 'Nr' => 9, 'Größe' => 'b', 'Order' => null, 'Notiz' => null, 'Bild' => null, 'Datum' => null, 'Aktiv' => 0, 'Zweit' => 4 ) ),
        ) );
    }

    /** AI-01 */
    public function testName()
    {
        $this->assertSame( 'order_details', expAccessImport::name( 'Order Details' ) );
        $this->assertSame( 'acc_umsaetze', expAccessImport::name( 'Umsätze', 'acc_' ) );
        $this->assertSame( 'groesse', expAccessImport::name( 'Größe' ) );
        $this->assertSame( 'order_', expAccessImport::name( 'Order' ) );
        $this->assertSame( 'user_', expAccessImport::name( 'USER' ) );
        $this->assertSame( 't_2024_sales', expAccessImport::name( '2024 Sales' ) );
        $this->assertMatchesRegularExpression( '/^c[0-9a-f]{8}$/', expAccessImport::name( '%%%' ) );
        $long = expAccessImport::name( str_repeat( 'Long Name ', 20 ) );
        $this->assertLessThanOrEqual( 60, strlen( $long ) );
        $this->assertMatchesRegularExpression( '/^[a-z_][a-z0-9_]*$/', $long );
    }

    /** AI-02 */
    public function testDbaField()
    {
        $this->assertSame( array( 'type' => 'auto_increment', 'default' => false ), expAccessImport::dbaField( self::field( 'counter' ) ) );
        $this->assertSame( 'int', expAccessImport::dbaField( self::field( 'boolean', 0, true ) )['type'] );
        $this->assertSame( 0, expAccessImport::dbaField( self::field( 'integer', 0, true ) )['default'] );
        $this->assertSame( 'bigint', expAccessImport::dbaField( self::field( 'bigint' ) )['type'] );
        $this->assertSame( 'double', expAccessImport::dbaField( self::field( 'single' ) )['type'] );
        $this->assertSame( array( 19, 'decimal' ), array_values( array_intersect_key( expAccessImport::dbaField( self::field( 'currency' ) ), array( 'length' => 1, 'type' => 1 ) ) ) );
        $this->assertSame( array( 'length' => 19, 'type' => 'varchar', 'not_null' => '0', 'default' => null ), expAccessImport::dbaField( self::field( 'datetime' ) ) );
        $this->assertSame( 'varchar', expAccessImport::dbaField( self::field( 'text', 50 ) )['type'] );
        $this->assertSame( 'longtext', expAccessImport::dbaField( self::field( 'text', 0 ) )['type'] );
        $this->assertSame( 'longtext', expAccessImport::dbaField( self::field( 'memo' ) )['type'] );
        $this->assertSame( 'longtext', expAccessImport::dbaField( self::field( 'binary' ) )['type'] );
        $this->assertSame( 'char', expAccessImport::dbaField( self::field( 'guid' ) )['type'] );
    }

    /** AI-03 */
    public function testSchema()
    {
        $result = expAccessImport::schema( self::reader(), array( 'Order Details', 'Umsätze', 'Nope' ), 'acc_' );
        $this->assertSame( array( 'acc_order_details', 'acc_umsaetze' ), array_keys( $result['schema'] ) );
        $details = $result['schema']['acc_order_details'];
        $this->assertSame( array( 'type' => 'primary', 'fields' => array( 'orderid', 'productid' ) ), $details['indexes']['PRIMARY'] );
        $umsatz = $result['schema']['acc_umsaetze'];
        $this->assertSame( array( 'nr' ), $umsatz['indexes']['PRIMARY']['fields'], 'the AutoNumber is the key' );
        $this->assertContains( array( 'type' => 'unique', 'fields' => array( 'groesse' ) ), $umsatz['indexes'], 'the Access key stays unique' );
        $this->assertSame( 'int', $umsatz['fields']['zweit']['type'] );
        $this->assertArrayHasKey( 'order_', $umsatz['fields'] );
        $this->assertSame( array( 'Nr' => 'nr', 'Größe' => 'groesse', 'Order' => 'order_', 'Notiz' => 'notiz', 'Bild' => 'bild', 'Datum' => 'datum', 'Aktiv' => 'aktiv', 'Zweit' => 'zweit' ),
                           $result['map']['Umsätze']['columns'] );
        $notes = implode( "\n", $result['notes'] );
        $this->assertStringContainsString( "no table 'Nope'", $notes );
        $this->assertStringContainsString( 'Bild is binary', $notes );
        $this->assertStringContainsString( 'Notiz is on long text', $notes );
        $this->assertStringContainsString( 'second AutoNumber', $notes );
        $all = expAccessImport::schema( self::reader() );
        $this->assertSame( array( 'order_details', 'umsaetze' ), array_keys( $all['schema'] ) );
    }

    /** AI-04 */
    public function testParseSchema()
    {
        $ddl = "-- MDB Tools\n\nCREATE TABLE `Order Details`\n (\n\t`OrderID`\t\t\tint, \n\t`ProductID`\t\t\tint NOT NULL, \n\t`UnitPrice`\t\t\tfloat NOT NULL, \n"
             . "\t`Note`\t\t\ttext, \n\t`Code`\t\t\tvarchar (5), \n\t`Amount`\t\t\tnumeric (18, 4), \n\t`Pic`\t\t\tblob\n);\n\n"
             . "ALTER TABLE `Order Details` ADD INDEX `OrderID` (`OrderID`);\nALTER TABLE `Order Details` ADD PRIMARY KEY (`OrderID`, `ProductID`);\n"
             . "ALTER TABLE `Order Details` ADD UNIQUE INDEX `Code` (`Code`);\n"
             . "CREATE TABLE `Employees`\n (\n\t`EmployeeID`\t\t\tint not null auto_increment unique, \n\t`Done`\t\t\tboolean NOT NULL\n);\n";
        $details = expAccessMdbToolsReader::parseSchema( $ddl, 'Order Details' );
        $this->assertSame( array( 'OrderID', 'ProductID', 'UnitPrice', 'Note', 'Code', 'Amount', 'Pic' ), array_keys( $details['fields'] ) );
        $this->assertSame( 'integer', $details['fields']['OrderID']['type'] );
        $this->assertTrue( $details['fields']['ProductID']['not_null'] );
        $this->assertSame( 'double', $details['fields']['UnitPrice']['type'] );
        $this->assertSame( 'memo', $details['fields']['Note']['type'] );
        $this->assertSame( array( 'text', 5 ), array( $details['fields']['Code']['type'], $details['fields']['Code']['length'] ) );
        $this->assertSame( array( 'decimal', 18, 4 ), array( $details['fields']['Amount']['type'], $details['fields']['Amount']['length'], $details['fields']['Amount']['scale'] ) );
        $this->assertSame( 'binary', $details['fields']['Pic']['type'] );
        $this->assertSame( array( 'OrderID', 'ProductID' ), $details['primary'] );
        $this->assertSame( array( 'OrderID' => array( 'unique' => false, 'fields' => array( 'OrderID' ) ), 'Code' => array( 'unique' => true, 'fields' => array( 'Code' ) ) ), $details['indexes'] );
        $employees = expAccessMdbToolsReader::parseSchema( $ddl, 'Employees' );
        $this->assertSame( 'counter', $employees['fields']['EmployeeID']['type'] );
        $this->assertTrue( $employees['fields']['EmployeeID']['auto'] );
        $this->assertSame( 'boolean', $employees['fields']['Done']['type'] );
        $this->assertSame( array( 'fields' => array(), 'primary' => array(), 'indexes' => array() ), expAccessMdbToolsReader::parseSchema( $ddl, 'Missing' ) );
    }

    /** AI-05 */
    public function testLiteral()
    {
        $db = new expPDODB( array( 'pdo_dsn' => 'sqlite::memory:' ) );
        $int = array( 'type' => 'int' );
        $text = array( 'type' => 'varchar' );
        $this->assertSame( 'NULL', expAccessImport::literal( $db, null, $int ) );
        $this->assertSame( '14.0000', expAccessImport::literal( $db, '14.0000', array( 'type' => 'decimal' ) ) );
        $this->assertSame( '-3', expAccessImport::literal( $db, -3, $int ) );
        $this->assertSame( '1', expAccessImport::literal( $db, true, $int ) );
        $this->assertSame( 'NULL', expAccessImport::literal( $db, '', $int ) );
        $this->assertSame( "'12a'", expAccessImport::literal( $db, '12a', $int ) );
        $this->assertSame( "'O''Neil'", expAccessImport::literal( $db, "O'Neil", $text ) );
        $this->assertSame( "'AP8Q'", expAccessImport::literal( $db, new expAccessBinary( "\x00\xff\x10" ), array( 'type' => 'longtext' ) ) );
        $this->assertSame( "'5'", expAccessImport::literal( $db, 5, $text ) );
    }

    /** AI-06 */
    public function testRunIntoSqlite()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'pdo_sqlite is not loaded' );
        $this->file = expDataTableTestLoader::root() . '/var/tmp/access-import-' . getmypid() . '.sqlite';
        $db = new expPDODB( array( 'pdo_dsn' => 'sqlite:' . $this->file ) );
        $reader = self::reader();
        $report = expAccessImport::run( $reader, $db, array( 'prefix' => 'acc_', 'batch' => 2 ) );
        $this->assertSame( 'sqlite', $report['engine'] );
        $this->assertSame( 'create', $report['tables']['Umsätze']['action'] );
        $this->assertSame( 2, $report['tables']['Umsätze']['written'] );
        $this->assertSame( array(), $report['tables']['Umsätze']['errors'] );
        // the third row of Order Details repeats the key of the first: reported, the other three arrive
        $this->assertSame( 4, $report['tables']['Order Details']['read'] );
        $this->assertSame( 3, $report['tables']['Order Details']['written'] );
        $this->assertCount( 1, $report['tables']['Order Details']['errors'] );
        $this->assertStringStartsWith( 'row 3:', $report['tables']['Order Details']['errors'][0] );
        $this->assertFalse( $report['ok'] );
        $rows = $db->arrayQuery( 'SELECT * FROM acc_umsaetze ORDER BY nr' );
        $this->assertSame( array( 7, 9 ), array_map( 'intval', array_column( $rows, 'nr' ) ) );
        $this->assertSame( "O'Neil ä 😀", $rows[0]['groesse'] );
        $this->assertSame( 'AP8Q', $rows[0]['bild'] );
        $this->assertSame( '2026-11-25 10:11:12', $rows[0]['datum'] );
        $this->assertSame( 5000, strlen( $rows[0]['notiz'] ) );
        $this->assertNull( $rows[1]['order_'] );
        $this->assertNull( $rows[1]['datum'] );
        $again = expAccessImport::run( $reader, $db, array( 'prefix' => 'acc_', 'tables' => array( 'Umsätze' ) ) );
        $this->assertFalse( $again['ok'] );
        $this->assertStringContainsString( 'has 2 rows already', $again['tables']['Umsätze']['errors'][0] );
        $replaced = expAccessImport::run( $reader, $db, array( 'prefix' => 'acc_', 'tables' => array( 'Umsätze' ), 'mode' => 'replace' ) );
        $this->assertTrue( $replaced['ok'], json_encode( $replaced ) );
        $this->assertEquals( 2, $db->arrayQuery( 'SELECT COUNT(*) AS c FROM acc_umsaetze' )[0]['c'] );
        $reader->rows['Umsätze'][0]['Nr'] = 70;
        $reader->rows['Umsätze'][1]['Nr'] = 90;
        $reader->rows['Umsätze'][0]['Größe'] = 'other';
        $reader->rows['Umsätze'][1]['Größe'] = 'other 2';
        $reader->rows['Umsätze'][0]['Datum'] = '2026-11-26 00:00:00';
        $appended = expAccessImport::run( $reader, $db, array( 'prefix' => 'acc_', 'tables' => array( 'Umsätze' ), 'mode' => 'append' ) );
        $this->assertTrue( $appended['ok'], json_encode( $appended ) );
        $this->assertEquals( 4, $db->arrayQuery( 'SELECT COUNT(*) AS c FROM acc_umsaetze' )[0]['c'] );
        $this->expectException( InvalidArgumentException::class );
        expAccessImport::run( $reader, $db, array( 'mode' => 'merge' ) );
    }

    /** AI-07 */
    public function testDatesAndReaderFactory()
    {
        $this->assertSame( '2026-11-25 10:11:12', expAccessReader::date( '2026-11-25 10:11:12' ) );
        $this->assertSame( '2026-11-25 00:00:00', expAccessReader::date( '2026-11-25' ) );
        $this->assertSame( '2026-11-25 10:11:00', expAccessReader::date( '2026-11-25T10:11' ) );
        $this->assertNull( expAccessReader::date( '' ) );
        $this->assertSame( '1899-12-30 00:00:00', expAccessReader::date( '1899-12-30 00:00:00' ) );
        try
        {
            expAccessReader::create( __FILE__ . '.missing.accdb' );
            $this->fail( 'a missing file was read' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'missing.accdb', $e->getMessage() );
        }
        try
        {
            new expAccessMdbToolsReader( __FILE__, array( 'Bin' => '/nonexistent-7x' ) );
            $this->fail( 'MDBTools found in a directory that does not exist' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'MDBTools', $e->getMessage() );
        }
    }
}
?>
