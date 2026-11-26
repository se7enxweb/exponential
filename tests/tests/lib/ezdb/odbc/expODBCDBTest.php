<?php
/**
 * The generic ODBC driver (lib/ezdb/classes/expodbcdb.php) and the MS Access driver (expmsaccessdb.php), without a
 * database:
 *
 *  OD-01 - chooseLayer(): auto takes pdo_odbc first (odbc first for MS Access); a layer PHP lacks is ''
 *  OD-02 - defaultDriver(): the names the drivers' packages register, on Linux and on Windows
 *  OD-03 - connectionString(): per dialect (SQL Server tcp:host,port; DBQ for Access; Database for SQLite; Server,
 *          Port, Database and CHARSET for MySQL), DSN instead of Driver, values with ; { } in braces, extra keywords
 *  OD-04 - expMSAccessDB::connectionString(): ExtendedAnsiSQL, ReadOnly and Exclusive for ACE, none for MDBTools
 *  OD-05 - engineName() and databaseName(): the kernel's engine names; sqlHelperDialect()
 *  OD-06 - escapeString(): backslashes doubled for MySQL only, quotes doubled, NUL dropped
 *  OD-07 - pageStatement(): the dialect's paging; TOP offset+limit for MS Access; nothing for limit 0
 *  OD-08 - The SQL expressions of MS Access: & concat, Mid, BAND with ExtendedAnsiSQL, emulation without it, no MD5
 *  OD-09 - A failed connection throws eZDBNoConnectionException without the password
 *  OD-10 - settings(): the defaults, then the parameters; MS Access reads [MSAccessSettings]
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expODBCTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('odbc')]
class expODBCDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expODBCTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expODBCTestKit::restoreDebug( $this->saved );
    }

    /** OD-01 */
    public function testChooseLayer()
    {
        $both = array( 'pdo_odbc', 'odbc' );
        $this->assertSame( 'pdo_odbc', expODBCDB::chooseLayer( 'auto', 'postgresql', $both ) );
        $this->assertSame( 'odbc', expODBCDB::chooseLayer( 'auto', 'msaccess', $both ) );
        $this->assertSame( 'odbc', expODBCDB::chooseLayer( 'auto', 'postgresql', array( 'odbc' ) ) );
        $this->assertSame( 'pdo_odbc', expODBCDB::chooseLayer( 'auto', 'msaccess', array( 'pdo_odbc' ) ) );
        $this->assertSame( 'odbc', expODBCDB::chooseLayer( 'ODBC', 'mysql', $both ) );
        $this->assertSame( '', expODBCDB::chooseLayer( 'pdo_odbc', 'mysql', array( 'odbc' ) ) );
        $this->assertSame( '', expODBCDB::chooseLayer( 'auto', 'mysql', array() ) );
    }

    /** OD-02 */
    public function testDefaultDriver()
    {
        $this->assertSame( 'ODBC Driver 18 for SQL Server', expODBCDB::defaultDriver( 'sqlserver', false ) );
        $this->assertSame( 'PostgreSQL Unicode', expODBCDB::defaultDriver( 'postgresql', false ) );
        $this->assertSame( 'PostgreSQL Unicode(x64)', expODBCDB::defaultDriver( 'postgresql', true ) );
        $this->assertSame( 'MariaDB Unicode', expODBCDB::defaultDriver( 'mysql', false ) );
        $this->assertSame( 'SQLite3', expODBCDB::defaultDriver( 'sqlite', false ) );
        $this->assertSame( 'Microsoft Access Driver (*.mdb, *.accdb)', expODBCDB::defaultDriver( 'msaccess', true ) );
        $this->assertSame( 'MDBTools', expODBCDB::defaultDriver( 'msaccess', false ) );
        $this->assertSame( '', expODBCDB::defaultDriver( 'generic', false ) );
    }

    /** OD-03 */
    public function testConnectionString()
    {
        $this->assertSame( 'Driver={ODBC Driver 18 for SQL Server};Server=tcp:db.example.com,1433;Database=crm;Encrypt=yes',
                           expODBCDB::connectionString( 'sqlserver', array( 'Driver' => 'ODBC Driver 18 for SQL Server', 'ConnectionString' => 'Encrypt=yes;' ), 'db.example.com', 1433, 'crm' ) );
        $this->assertSame( 'Driver={PostgreSQL Unicode};Server=pg;Port=5433;Database=site',
                           expODBCDB::connectionString( 'postgresql', array( 'Driver' => 'PostgreSQL Unicode' ), 'pg', 5433, 'site' ) );
        $this->assertSame( 'Driver={MariaDB Unicode};Server=127.0.0.1;Database=site;CHARSET=utf8mb4',
                           expODBCDB::connectionString( 'mysql', array( 'Driver' => 'MariaDB Unicode' ), '127.0.0.1', false, 'site' ) );
        $this->assertSame( 'Driver={SQLite3};Database=/var/data/a.db', expODBCDB::connectionString( 'sqlite', array( 'Driver' => 'SQLite3' ), '', 0, '/var/data/a.db' ) );
        $this->assertSame( 'Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ={C:\data\shop;old.accdb}',
                           expODBCDB::connectionString( 'msaccess', array( 'Driver' => 'Microsoft Access Driver (*.mdb, *.accdb)' ), '', 0, 'C:\data\shop;old.accdb' ) );
        $this->assertSame( 'DSN={my;dsn};Database=x', expODBCDB::connectionString( 'generic', array( 'DataSourceName' => 'my;dsn' ), '', 0, 'x' ) );
        $this->assertSame( 'Driver={odd}}name};Server=h', expODBCDB::connectionString( 'generic', array( 'Driver' => 'odd}name' ), 'h', 0, '' ) );
        $this->assertSame( 'Driver={Oracle 23 ODBC driver};DBQ=//ora:1521/FREEPDB1', expODBCDB::connectionString( 'oracle', array( 'Driver' => 'Oracle 23 ODBC driver' ), 'ora', 1521, 'FREEPDB1' ) );
    }

    /** OD-04 */
    public function testAccessConnectionString()
    {
        $ace = array( 'Driver' => 'Microsoft Access Driver (*.mdb, *.accdb)', 'ExtendedAnsiSQL' => 'enabled', 'ReadOnly' => 'enabled', 'Exclusive' => 'disabled', 'ConnectionString' => 'PageTimeout=5' );
        $this->assertSame( 'Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=C:\a.accdb;ExtendedAnsiSQL=1;ReadOnly=1;PageTimeout=5',
                           expMSAccessDB::connectionString( 'msaccess', $ace, '', 0, 'C:\a.accdb' ) );
        $this->assertSame( 'Driver={MDBTools};DBQ=/data/a.mdb', expMSAccessDB::connectionString( 'msaccess', array( 'Driver' => 'MDBTools', 'ExtendedAnsiSQL' => 'enabled', 'ReadOnly' => 'enabled' ), '', 0, '/data/a.mdb' ) );
        $this->assertSame( 'Driver={MDBTools};DBQ=/data/a.mdb;PageTimeout=5', expMSAccessDB::connectionString( 'msaccess', array( 'Driver' => 'MDBTools' ) + $ace, '', 0, '/data/a.mdb' ) );
        $this->assertTrue( expODBCTestKit::unconnected( 'msaccess', 'expMSAccessDB', 'Driver={x};DBQ=a;ExtendedAnsiSQL=1' )->ansi92() );
        $this->assertFalse( expODBCTestKit::unconnected( 'msaccess', 'expMSAccessDB', 'Driver={x};DBQ=a' )->ansi92() );
    }

    /** OD-05 */
    public function testEngineNames()
    {
        $expected = array( 'sqlserver' => 'SQLServer', 'msaccess' => 'MSAccess', 'postgresql' => 'postgresql', 'mysql' => 'mysql',
                           'sqlite' => 'sqlite', 'oracle' => 'oracle', 'generic' => 'odbc' );
        foreach ( $expected as $dialect => $name )
        {
            $db = expODBCTestKit::unconnected( $dialect );
            $this->assertSame( $name, $db->databaseName() );
            $this->assertSame( $dialect, $db->sqlHelperDialect() );
            $this->assertSame( $dialect, $db->sqlDialect() );
        }
    }

    /** OD-06 */
    public function testEscapeString()
    {
        $text = "a'b\\c\0d";
        $this->assertSame( "a''b\\\\cd", expODBCTestKit::unconnected( 'mysql' )->escapeString( $text ) );
        foreach ( array( 'postgresql', 'sqlserver', 'sqlite', 'msaccess', 'oracle', 'generic' ) as $dialect )
            $this->assertSame( "a''b\\cd", expODBCTestKit::unconnected( $dialect )->escapeString( $text ), $dialect );
        $this->assertSame( '', expODBCTestKit::unconnected( 'mysql' )->escapeString( null ) );
    }

    /** OD-07 */
    public function testPageStatement()
    {
        $sql = 'SELECT id FROM t ORDER BY id';
        $this->assertSame( "$sql\nLIMIT 5 OFFSET 10", expODBCTestKit::unconnected( 'postgresql' )->pageStatement( $sql, 5, 10 ) );
        $this->assertSame( "$sql\nLIMIT 5 OFFSET 10", expODBCTestKit::unconnected( 'mysql' )->pageStatement( $sql, 5, 10 ) );
        $this->assertSame( "$sql\nLIMIT -1 OFFSET 3", expODBCTestKit::unconnected( 'sqlite' )->pageStatement( $sql, false, 3 ) );
        $this->assertStringContainsString( 'OFFSET 10 ROWS FETCH NEXT 5 ROWS ONLY', expODBCTestKit::unconnected( 'sqlserver' )->pageStatement( $sql, 5, 10 ) );
        $this->assertSame( 'SELECT TOP 15 id FROM t ORDER BY id', expODBCTestKit::unconnected( 'msaccess' )->pageStatement( $sql, 5, 10 ) );
        $this->assertSame( $sql, expODBCTestKit::unconnected( 'msaccess' )->pageStatement( $sql, false, 10 ) );
        $this->assertNull( expODBCTestKit::unconnected( 'postgresql' )->pageStatement( $sql, 0, 0 ) );
        $this->assertNull( expODBCTestKit::unconnected( 'msaccess' )->pageStatement( $sql, 0, 0 ) );
        $this->assertSame( $sql, expODBCTestKit::unconnected( 'mysql' )->pageStatement( $sql, false, 0 ) );
    }

    /** OD-08 */
    public function testAccessExpressions()
    {
        $ansi = expODBCTestKit::unconnected( 'msaccess', 'expMSAccessDB', 'Driver={x};DBQ=a;ExtendedAnsiSQL=1' );
        $plain = expODBCTestKit::unconnected( 'msaccess', 'expMSAccessDB', 'Driver={x};DBQ=a' );
        $this->assertSame( ' ( a & b ) ', $ansi->concatString( array( 'a', 'b' ) ) );
        $this->assertSame( ' Mid( s, 1, 2 ) ', $ansi->subString( 's', 1, 2 ) );
        $this->assertSame( ' ( mask BAND 4 ) ', $ansi->bitAnd( 'mask', '4' ) );
        $this->assertStringContainsString( 'MOD 2', $plain->bitAnd( 'mask', '4' ) );
        $this->assertSame( ' ( a || b ) ', expODBCTestKit::unconnected( 'postgresql' )->concatString( array( 'a', 'b' ) ) );
        $this->assertSame( ' CONCAT( a, b ) ', expODBCTestKit::unconnected( 'mysql' )->concatString( array( 'a', 'b' ) ) );
        $this->expectException( expDBUnsupportedException::class );
        $ansi->md5( 'x' );
    }

    /** OD-09 */
    public function testFailedConnectionHidesThePassword()
    {
        if ( !expODBCTestKit::layers() )
            $this->markTestSkipped( 'This PHP has neither PDO_ODBC nor the odbc extension' );
        try
        {
            new expODBCDB( array( 'user' => 'u', 'password' => 'S3cret-pw', 'connect_retries' => 0,
                                  'odbc' => array( 'Dialect' => 'generic', 'Driver' => 'No Such Driver 7x', 'ConnectionString' => 'PWD=S3cret-pw;Server=nowhere' ) ) );
            $this->fail( 'connected to nothing' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringNotContainsString( 'S3cret-pw', $e->getMessage() );
        }
    }

    /** OD-10 */
    public function testSettings()
    {
        $settings = expODBCDB::settings( array( 'Dialect' => 'mysql', 'Layer' => 'odbc' ) );
        $this->assertSame( 'mysql', $settings['Dialect'] );
        $this->assertSame( 'odbc', $settings['Layer'] );
        $this->assertArrayHasKey( 'ConnectionString', $settings );
        $access = expMSAccessDB::settings( array() );
        $this->assertSame( 'msaccess', $access['Dialect'] );
        $this->assertArrayHasKey( 'ExtendedAnsiSQL', $access );
        $this->assertSame( 'MSAccessSettings', expMSAccessDB::settingsGroup() );
        $this->assertSame( 'ODBCSettings', expODBCDB::settingsGroup() );
    }
}
?>
