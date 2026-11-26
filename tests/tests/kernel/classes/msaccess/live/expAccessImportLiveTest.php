<?php
/**
 * The Access import with real Access files (skipped without EXP_TEST_ACCESS_FIXTURES, a directory of .mdb and .accdb
 * files; CI uses MDBTools' test data: nwind.mdb (JET3), DateTestDatabase.mdb (JET4), ASampleDatabase.accdb (ACE)).
 * The reader is the one this system has (MDBTools on Linux, the Access ODBC driver on Windows).
 *
 *  AL-I1 - Every file's tables are listed and described
 *  AL-I2 - Every file imports into SQLite (PDO): every row read is written, the names are the target's
 *  AL-I3 - nwind.mdb: known values (an employee, a product with its price, umlauts in a table name, a photo as base64)
 *  AL-I4 - Every file imports into each ODBC target of expODBCTestKit (SQL Server, PostgreSQL, MariaDB, SQLite)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 2 ) . '/datatable/expDataTableTestLoader.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdb/odbc/expODBCTestKit.php';
foreach ( array( 'expaccessreader', 'expaccessmdbtoolsreader', 'expaccessodbcreader', 'expaccessimport' ) as $file )
    require_once expDataTableTestLoader::root() . '/kernel/classes/msaccess/' . $file . '.php';

class expAccessImportLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    /** @var string[] files to remove */
    private $files = array();

    protected function setUp(): void
    {
        $this->saved = expODBCTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
        expKernelTableSchema::reset();
        expODBCTestKit::restoreDebug( $this->saved );
    }

    public static function files()
    {
        $dir = (string)getenv( 'EXP_TEST_ACCESS_FIXTURES' );
        $files = $dir !== '' && is_dir( $dir ) ? array_merge( glob( "$dir/*.mdb" ) ?: array(), glob( "$dir/*.accdb" ) ?: array() ) : array();
        if ( !$files )
            return array( 'none' => array( null ) );
        $data = array();
        foreach ( $files as $file )
            $data[basename( $file )] = array( $file );
        return $data;
    }

    private function reader( $file )
    {
        if ( $file === null )
            $this->markTestSkipped( 'No Access files: set EXP_TEST_ACCESS_FIXTURES' );
        try
        {
            return expAccessReader::create( $file, (string)getenv( 'EXP_TEST_ACCESS_READER' ) ?: 'auto' );
        }
        catch ( RuntimeException $e )
        {
            $this->markTestSkipped( $e->getMessage() );
        }
    }

    /**
     * Imports into a target and checks every row arrived.
     *
     * @return array the report
     */
    private function importInto( expAccessReader $reader, $db, $label )
    {
        foreach ( expAccessImport::schema( $reader, array(), 'acc_' )['schema'] as $table => $definition )
        {
            if ( expKernelTableSchema::exists( $table, $db, true ) )
                $db->query( 'DROP TABLE ' . $db->sqlQuoteIdentifier( $table ) );
        }
        expKernelTableSchema::reset();
        $report = expAccessImport::run( $reader, $db, array( 'prefix' => 'acc_' ) );
        foreach ( $report['tables'] as $access => $outcome )
        {
            $this->assertSame( array(), $outcome['errors'], "$label: $access" );
            $this->assertSame( $outcome['read'], $outcome['written'], "$label: $access" );
            $count = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ' . $db->sqlQuoteIdentifier( $outcome['table'] ) );
            $this->assertEquals( $outcome['read'], array_values( $count[0] )[0], "$label: rows in {$outcome['table']}" );
        }
        $this->assertTrue( $report['ok'], $label );
        return $report;
    }

    /** AL-I1, AL-I2, AL-I3 */
    #[\PHPUnit\Framework\Attributes\DataProvider('files')]
    public function testImportIntoSqlite( $file )
    {
        $reader = $this->reader( $file );
        $tables = $reader->tables();
        $this->assertNotEmpty( $tables );
        foreach ( $tables as $table )
            $this->assertNotEmpty( $reader->table( $table )['fields'], $table );
        $target = expODBCTestKit::tempFile( 'access-import', 'sqlite' );
        $this->files[] = $target;
        $db = new expPDODB( array( 'pdo_dsn' => 'sqlite:' . $target ) );
        $report = $this->importInto( $reader, $db, 'sqlite' );
        fwrite( STDERR, sprintf( "\n%s (%s, %s): %d tables, %d rows\n", basename( $file ), $reader->format(), $reader->name(), count( $report['tables'] ),
                                 array_sum( array_column( $report['tables'], 'written' ) ) ) );
        if ( basename( $file ) === 'nwind.mdb' )
        {
            $this->assertArrayHasKey( 'Umsätze', $report['tables'] );
            $this->assertSame( 'acc_umsaetze', $report['tables']['Umsätze']['table'] );
            $employee = $db->arrayQuery( 'SELECT lastname, firstname, birthdate, photo FROM acc_employees WHERE employeeid = 1' )[0];
            $this->assertSame( array( 'Davolio', 'Nancy' ), array( $employee['lastname'], $employee['firstname'] ) );
            $this->assertSame( '1948-12-08 00:00:00', $employee['birthdate'] );
            $this->assertNotFalse( base64_decode( (string)$employee['photo'], true ) );
            $this->assertGreaterThan( 1000, strlen( (string)$employee['photo'] ) );
            $chai = $db->arrayQuery( "SELECT unitprice, discontinued FROM acc_products WHERE productname = 'Chai'" )[0];
            $this->assertEqualsWithDelta( 18.0, (float)$chai['unitprice'], 0.0001 );
            $this->assertEquals( 0, $chai['discontinued'] );
        }
    }

    /** AL-I4 */
    #[\PHPUnit\Framework\Attributes\DataProvider('files')]
    public function testImportIntoOdbcTargets( $file )
    {
        $reader = $this->reader( $file );
        $targets = expODBCTestKit::targets();
        if ( !$targets )
            $this->markTestSkipped( 'No ODBC target' );
        $done = array();
        foreach ( $targets as $name => $target )
        {
            // one layer per database is enough here
            if ( isset( $done[$target[0]] ) )
                continue;
            $done[$target[0]] = true;
            $db = expODBCTestKit::connect( $target );
            $this->importInto( $reader, $db, $name );
            foreach ( expAccessImport::schema( $reader, array(), 'acc_' )['schema'] as $table => $definition )
                $db->query( 'DROP TABLE ' . $db->sqlQuoteIdentifier( $table ) );
            $db->close();
        }
    }
}
?>
