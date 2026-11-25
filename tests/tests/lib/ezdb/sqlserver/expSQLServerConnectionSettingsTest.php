<?php
/**
 * Connection settings of the SQL Server driver (no server needed):
 *
 *  CN-01 - serverAddress(): tcp:host,port with 1433 by default; named instances, LocalDB, IPv6, "host,port"
 *  CN-02 - connectionValue(): a value with ; { } = or edge spaces is braced, } doubled (no keyword injection
 *          through a database name)
 *  CN-03 - connectionKeywords(): Encrypt (yes/no/strict; ODBC Driver 18 encrypts by default),
 *          TrustServerCertificate, MultiSubnetFailover, ApplicationIntent=ReadOnly, LoginTimeout, APP
 *  CN-04 - DSNs: pdo_sqlsrv takes 1/0 (W0: pdo_sqlsrv 5.11 read "yes" as false and failed on the self-signed
 *          certificate), PDO_ODBC takes yes/no and Driver={...}, pdo_dblib TDS 7.4 UTF-8; sqlsrv options
 *  CN-05 - sessionSetup(): NOCOUNT, XACT_ABORT, ANSI options, DATEFORMAT ymd, LOCK_TIMEOUT, READ COMMITTED
 *  CN-06 - describeVersion(): 2012-2025 by major version, Azure SQL Database / Managed Instance supported, Azure
 *          SQL Edge (engine edition 9) retired 30 September 2025, below 2017 unsupported
 *  CN-07 - replicaUsable(): a readable secondary (READ_ONLY) or a primary (READ_WRITE); not offline/unknown; lag
 *  CN-08 - expSQLServerTransport: chooseLayer() order and aliases, normalizeRow() as MySQL gives values
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerConnectionSettingsTest extends PHPUnit\Framework\TestCase
{
    public static function addresses()
    {
        return array(
            'host default port' => array( 'db1', null, 'tcp:db1,1433' ),
            'host and port' => array( 'db1', 14330, 'tcp:db1,14330' ),
            'port as string' => array( 'db1', '1500', 'tcp:db1,1500' ),
            'port zero is default' => array( 'db1', 0, 'tcp:db1,1433' ),
            'empty host' => array( '', null, 'tcp:localhost,1433' ),
            'ip' => array( '10.77.0.2', 1433, 'tcp:10.77.0.2,1433' ),
            'ipv6' => array( '2605:a141::1', 1433, 'tcp:[2605:a141::1],1433' ),
            'ipv6 bracketed' => array( '[::1]', 1433, 'tcp:[::1],1433' ),
            'named instance' => array( 'db1\\SQLEXPRESS', null, 'db1\\SQLEXPRESS' ),
            'named instance with port' => array( 'db1\\SQLEXPRESS', 1500, 'tcp:db1\\SQLEXPRESS,1500' ),
            'localdb' => array( '(localdb)\\MSSQLLocalDB', 1433, '(localdb)\\MSSQLLocalDB' ),
            'host,port written in' => array( 'db1,1500', 1433, 'tcp:db1,1500' ),
            'tcp prefix kept once' => array( 'tcp:db1', 1500, 'tcp:db1,1500' ),
            'azure' => array( 'x.database.windows.net', null, 'tcp:x.database.windows.net,1433' ),
            'listener' => array( 'ag-listener.corp', 1433, 'tcp:ag-listener.corp,1433' ),
            'named pipe' => array( 'np:\\\\.\\pipe\\sql\\query', null, 'np:\\\\.\\pipe\\sql\\query' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('addresses')]
    public function testServerAddress( $host, $port, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::serverAddress( $host, $port ) );
    }

    public static function values()
    {
        return array(
            array( 'abc', 'abc' ), array( 'a;b', '{a;b}' ), array( 'a}b', '{a}}b}' ), array( 'a{b', '{a{b}' ), array( 'a=b', '{a=b}' ),
            array( ' a', '{ a}' ), array( 'a ', '{a }' ), array( '', '{}' ), array( 'x;Encrypt=no', '{x;Encrypt=no}' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('values')]
    public function testConnectionValue( $value, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::connectionValue( $value ) );
    }

    public function testDatabaseNameCannotInjectAKeyword()
    {
        $dsn = expSQLServerDialect::odbcDSN( array( 'host' => 'h', 'database' => 'x;Encrypt=no', 'encrypt' => 'yes' ) );
        $this->assertStringContainsString( 'Database={x;Encrypt=no};Encrypt=yes', $dsn );
    }

    public static function encryptValues()
    {
        return array( array( 'yes', 'yes' ), array( 'true', 'yes' ), array( 'mandatory', 'yes' ), array( 'no', 'no' ), array( 'false', 'no' ),
                      array( 'optional', 'no' ), array( 'disabled', 'no' ), array( 'strict', 'strict' ), array( 'STRICT', 'strict' ), array( '', 'yes' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('encryptValues')]
    public function testEncrypt( $value, $expected )
    {
        $keywords = expSQLServerDialect::connectionKeywords( array( 'encrypt' => $value ) );
        $this->assertSame( $expected, $keywords['Encrypt'] );
    }

    public function testEncryptIsOnByDefault()
    {
        $keywords = expSQLServerDialect::connectionKeywords( array() );
        $this->assertSame( 'yes', $keywords['Encrypt'] );
        $this->assertSame( 'no', $keywords['TrustServerCertificate'] );
        $this->assertArrayNotHasKey( 'MultiSubnetFailover', $keywords );
        $this->assertArrayNotHasKey( 'ApplicationIntent', $keywords );
    }

    public function testAllKeywords()
    {
        $keywords = expSQLServerDialect::connectionKeywords( array( 'database' => 'site', 'encrypt' => 'yes', 'trust_server_certificate' => 'enabled',
            'multi_subnet_failover' => true, 'application_intent' => 'ReadOnly', 'login_timeout' => 5, 'app_name' => 'Exponential' ) );
        $this->assertSame( array( 'Database' => 'site', 'Encrypt' => 'yes', 'TrustServerCertificate' => 'yes', 'MultiSubnetFailover' => 'yes',
                                  'ApplicationIntent' => 'ReadOnly', 'LoginTimeout' => '5', 'APP' => 'Exponential' ), $keywords );
    }

    public function testConnectRetryCount()
    {
        $this->assertSame( '0', expSQLServerDialect::connectionKeywords( array( 'connect_retry_count' => 0 ) )['ConnectRetryCount'] );
        $this->assertSame( '255', expSQLServerDialect::connectionKeywords( array( 'connect_retry_count' => 999 ) )['ConnectRetryCount'] );
        $this->assertArrayNotHasKey( 'ConnectRetryCount', expSQLServerDialect::connectionKeywords( array() ) );
        $this->assertSame( 0, expSQLServerDialect::sqlsrvOptions( array( 'connect_retry_count' => 0 ) )['ConnectRetryCount'] );
        $this->assertStringContainsString( ';ConnectRetryCount=0', expSQLServerDialect::odbcDSN( array( 'host' => 'h', 'connect_retry_count' => 0 ) ) );
    }

    public function testReadWriteIntentIsNotWritten()
    {
        $this->assertArrayNotHasKey( 'ApplicationIntent', expSQLServerDialect::connectionKeywords( array( 'application_intent' => 'ReadWrite' ) ) );
    }

    public function testPdoSqlsrvDsnUsesOneAndZero()
    {
        $dsn = expSQLServerDialect::pdoSqlsrvDSN( array( 'host' => 'db', 'port' => 1433, 'database' => 'site', 'trust_server_certificate' => true,
                                                         'multi_subnet_failover' => true ) );
        $this->assertSame( 'sqlsrv:Server=tcp:db,1433;Database=site;Encrypt=1;TrustServerCertificate=1;MultiSubnetFailover=1;MultipleActiveResultSets=0;ConnectionPooling=0', $dsn );
        $this->assertStringNotContainsString( '=yes', $dsn );
    }

    public function testPdoSqlsrvDsnStrict()
    {
        $this->assertStringContainsString( 'Encrypt=strict', expSQLServerDialect::pdoSqlsrvDSN( array( 'host' => 'db', 'encrypt' => 'strict' ) ) );
    }

    public function testOdbcDsn()
    {
        $dsn = expSQLServerDialect::odbcDSN( array( 'host' => 'db', 'port' => 1433, 'database' => 'site', 'application_intent' => 'readonly' ) );
        $this->assertSame( 'odbc:Driver={ODBC Driver 18 for SQL Server};Server=tcp:db,1433;Database=site;Encrypt=yes;TrustServerCertificate=no;ApplicationIntent=ReadOnly', $dsn );
    }

    public function testOdbcDsnOtherDriver()
    {
        $this->assertStringStartsWith( 'odbc:Driver={ODBC Driver 17 for SQL Server};', expSQLServerDialect::odbcDSN( array( 'host' => 'db' ), 'ODBC Driver 17 for SQL Server' ) );
    }

    public function testDblibDsn()
    {
        $this->assertSame( 'dblib:host=db:1433;version=7.4;charset=UTF-8;dbname=site;appname=Exponential',
                           expSQLServerDialect::dblibDSN( array( 'host' => 'db', 'database' => 'site', 'app_name' => 'Exponential' ) ) );
        $this->assertSame( 'dblib:host=db\\SQLEXPRESS;version=7.4;charset=UTF-8', expSQLServerDialect::dblibDSN( array( 'host' => 'db\\SQLEXPRESS' ) ) );
    }

    public function testSqlsrvOptions()
    {
        $options = expSQLServerDialect::sqlsrvOptions( array( 'database' => 'site', 'user' => 'u', 'password' => 'p', 'trust_server_certificate' => true,
                                                              'login_timeout' => 7, 'multi_subnet_failover' => true ) );
        $this->assertSame( 'UTF-8', $options['CharacterSet'] );
        $this->assertTrue( $options['ReturnDatesAsStrings'] );
        $this->assertSame( 1, $options['Encrypt'] );
        $this->assertSame( 1, $options['TrustServerCertificate'] );
        $this->assertSame( 1, $options['MultiSubnetFailover'] );
        $this->assertSame( 7, $options['LoginTimeout'] );
        $this->assertSame( 'site', $options['Database'] );
        $this->assertSame( 'u', $options['UID'] );
        $this->assertSame( 'p', $options['PWD'] );
    }

    /**
     * Pooling is off unless asked for: a process that forks after closing a pooled connection would give that one
     * socket to all its children (seen live: TCP Provider errors 0x20 and 0x2746 in the forked cluster tests).
     */
    public function testConnectionPoolingIsOffUnlessEnabled()
    {
        $this->assertFalse( expSQLServerDialect::sqlsrvOptions( array() )['ConnectionPooling'] );
        $this->assertTrue( expSQLServerDialect::sqlsrvOptions( array( 'connection_pooling' => 'enabled' ) )['ConnectionPooling'] );
        $this->assertStringEndsWith( ';ConnectionPooling=0', expSQLServerDialect::pdoSqlsrvDSN( array( 'host' => 'db' ) ) );
        $this->assertStringEndsWith( ';ConnectionPooling=1', expSQLServerDialect::pdoSqlsrvDSN( array( 'host' => 'db', 'connection_pooling' => true ) ) );
        $this->assertStringNotContainsString( 'ConnectionPooling', expSQLServerDialect::odbcDSN( array( 'host' => 'db', 'connection_pooling' => true ) ) );
    }

    public function testSqlsrvOptionsWithoutUserUseIntegratedAuthentication()
    {
        $options = expSQLServerDialect::sqlsrvOptions( array() );
        $this->assertArrayNotHasKey( 'UID', $options );
        $this->assertArrayNotHasKey( 'PWD', $options );
    }

    public static function sessionParts()
    {
        return array( array( 'SET NOCOUNT ON' ), array( 'SET XACT_ABORT ON' ), array( 'SET ANSI_NULLS ON' ), array( 'SET QUOTED_IDENTIFIER ON' ),
                      array( 'SET CONCAT_NULL_YIELDS_NULL ON' ), array( 'SET ANSI_WARNINGS ON' ), array( 'SET ARITHABORT ON' ),
                      array( 'SET DATEFORMAT ymd' ), array( 'SET IMPLICIT_TRANSACTIONS OFF' ), array( 'SET TRANSACTION ISOLATION LEVEL READ COMMITTED' ),
                      array( 'SET TEXTSIZE 2147483647' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sessionParts')]
    public function testSessionSetupHas( $part )
    {
        $this->assertStringContainsString( $part, expSQLServerDialect::sessionSetup() );
    }

    public function testSessionLockTimeout()
    {
        $this->assertStringContainsString( 'SET LOCK_TIMEOUT -1', expSQLServerDialect::sessionSetup() );
        $this->assertStringContainsString( 'SET LOCK_TIMEOUT 5000', expSQLServerDialect::sessionSetup( 5000 ) );
        $this->assertStringContainsString( 'SET LOCK_TIMEOUT -1', expSQLServerDialect::sessionSetup( -20 ) );
        $this->assertStringContainsString( 'SET LOCK_TIMEOUT 0', expSQLServerDialect::sessionSetup( '0' ) );
    }

    public static function versions()
    {
        return array(
            '2012' => array( '11.0.7001.0', null, 'SQL Server 2012', false ),
            '2014' => array( '12.0.6024.0', null, 'SQL Server 2014', false ),
            '2016' => array( '13.0.6300.2', null, 'SQL Server 2016', false ),
            '2017' => array( '14.0.3456.2', null, 'SQL Server 2017', true ),
            '2019' => array( '15.0.4355.3', 3, 'SQL Server 2019', true ),
            '2022' => array( '16.0.4325.4', 3, 'SQL Server 2022', true ),
            '2025' => array( '17.0.1000.7', 3, 'SQL Server 2025', true ),
            'future' => array( '18.0.1.1', 3, 'SQL Server (version 18)', true ),
            'azure sql database' => array( '12.0.2000.8', 5, 'Azure SQL Database', true ),
            'azure managed instance' => array( '16.0.5000.1', 8, 'Azure SQL Managed Instance', true ),
            'azure sql edge retired' => array( '15.0.2000.1', 9, 'Azure SQL Edge', false ),
            'express edition' => array( '16.0.1000.6', 4, 'SQL Server 2022', true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('versions')]
    public function testDescribeVersion( $version, $edition, $name, $supported )
    {
        $info = expSQLServerDialect::describeVersion( $version, $edition );
        $this->assertSame( $name, $info['name'] );
        $this->assertSame( $supported, $info['supported'] );
        $this->assertSame( (int)$version, $info['major'] );
        $this->assertSame( $supported, $info['note'] === '' );
    }

    public function testAzureSqlEdgeNoteSaysRetired()
    {
        $this->assertStringContainsString( 'retired on 30 September 2025', expSQLServerDialect::describeVersion( '15.0.2000.1', 9 )['note'] );
    }

    public static function replicaStates()
    {
        return array(
            'secondary' => array( 'READ_ONLY', null, null, true ),
            'primary through listener' => array( 'READ_WRITE', null, null, true ),
            'lower case' => array( 'read_only', null, null, true ),
            'unknown' => array( null, null, null, false ),
            'empty' => array( '', null, null, false ),
            'offline answer' => array( 'OFFLINE', null, null, false ),
            'lag within' => array( 'READ_ONLY', 3, 10, true ),
            'lag beyond' => array( 'READ_ONLY', 30, 10, false ),
            'lag unknown with limit' => array( 'READ_ONLY', null, 10, true ),
            'lag without limit' => array( 'READ_ONLY', 300, 0, true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('replicaStates')]
    public function testReplicaUsable( $updateability, $lag, $maxLag, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::replicaUsable( $updateability, $lag, $maxLag ) );
    }

    public static function flags()
    {
        return array( array( true, true ), array( false, false ), array( 'enabled', true ), array( 'disabled', false ), array( 'yes', true ),
                      array( 'no', false ), array( '1', true ), array( '0', false ), array( 'true', true ), array( 'TRUE', true ), array( '', false ), array( 'on', true ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('flags')]
    public function testFlag( $value, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::flag( $value ) );
    }

    public static function layers()
    {
        $all = array( 'sqlsrv', 'pdo_sqlsrv', 'pdo_odbc', 'pdo_dblib' );
        return array(
            'auto takes sqlsrv first' => array( 'auto', $all, 'sqlsrv' ),
            'auto takes pdo_sqlsrv next' => array( 'auto', array( 'pdo_sqlsrv', 'pdo_odbc' ), 'pdo_sqlsrv' ),
            'auto takes odbc' => array( 'auto', array( 'pdo_odbc', 'pdo_dblib' ), 'pdo_odbc' ),
            'auto takes dblib last' => array( 'auto', array( 'pdo_dblib' ), 'pdo_dblib' ),
            'auto with nothing' => array( 'auto', array(), null ),
            'empty is auto' => array( '', $all, 'sqlsrv' ),
            'explicit' => array( 'pdo_odbc', $all, 'pdo_odbc' ),
            'alias odbc' => array( 'odbc', $all, 'pdo_odbc' ),
            'alias freetds' => array( 'FreeTDS', $all, 'pdo_dblib' ),
            'explicit missing' => array( 'sqlsrv', array( 'pdo_odbc' ), null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('layers')]
    public function testChooseLayer( $layer, array $available, $expected )
    {
        $this->assertSame( $expected, expSQLServerTransport::chooseLayer( $layer, $available ) );
    }

    public function testNormalizeRow()
    {
        $this->assertSame( array( 'i' => '5', 'f' => '1.5', 'b' => '1', 'n' => null, 's' => 'x', 'big' => '100000000000000000000' ),
                           expSQLServerTransport::normalizeRow( array( 'i' => 5, 'f' => 1.5, 'b' => true, 'n' => null, 's' => 'x', 'big' => 1.0e20 ) ) );
    }

    public function testNormalizeRowDate()
    {
        $row = expSQLServerTransport::normalizeRow( array( 'd' => new DateTime( '2026-11-24 10:11:12' ) ) );
        $this->assertSame( '2026-11-24 10:11:12', $row['d'] );
    }

    public function testNormalizeRowStream()
    {
        $stream = fopen( 'php://memory', 'r+' );
        fwrite( $stream, "\x00\x01" );
        rewind( $stream );
        $this->assertSame( "\x00\x01", expSQLServerTransport::normalizeRow( array( 'b' => $stream ) )['b'] );
    }

    public function testPdoTransportRefusesUnknownLayer()
    {
        $this->expectException( InvalidArgumentException::class );
        new expSQLServerPDOTransport( 'pdo_mysql' );
    }

    public function testPdoTransportDsnPerLayer()
    {
        $config = array( 'host' => 'db', 'port' => 1433, 'database' => 'site' );
        $this->assertStringStartsWith( 'sqlsrv:', ( new expSQLServerPDOTransport( 'pdo_sqlsrv' ) )->dsn( $config ) );
        $this->assertStringStartsWith( 'odbc:Driver={ODBC Driver 18', ( new expSQLServerPDOTransport( 'pdo_odbc' ) )->dsn( $config ) );
        $this->assertStringStartsWith( 'dblib:', ( new expSQLServerPDOTransport( 'pdo_dblib' ) )->dsn( $config ) );
    }
}
