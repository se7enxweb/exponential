<?php
/**
 * The libSQL driver (lib/ezdb/classes/explibsqldb.php, exptursodb.php) on a stand-in libSQL server, without one:
 *
 *  LD-01 - Connect: the server's SQLite version, databaseName() sqlite, the SQLite helpers' dialect, the URL built
 *  LD-02 - A server that cannot be reached, a refused token, Turso without a token: eZDBNoConnectionException
 *  LD-03 - query(), arrayQuery() with limit, offset and column, table-prefixed column names, errors and exceptions
 *  LD-04 - Transactions: BEGIN IMMEDIATE, commit, rollback, a failed statement inside one, nested eZDB transactions
 *  LD-05 - lastSerialID(): the server's rowid, MAX + 1 for a two-column key
 *  LD-06 - rewriteFunctions(): BIT_OR() and BIT_AND() as MAX()/MIN() per bit, with parentheses inside; run on SQLite
 *  LD-07 - md5(): a literal in PHP, a column as the server's md5() in hex; escapeString() without a connection
 *  LD-08 - Temporary tables as ordinary tables, dropped by dropTempTable() or close()
 *  LD-09 - No writer gate file; removeDatabase() empties the server's database; versions; statistics
 *  LD-10 - Data table connections: the engines libsql and turso, the options url and tls, a url refused
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLibSQLTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('libsql')]
class expLibSQLDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The stand-in libSQL server needs PHP\'s sqlite3 extension' );
        $this->saved = expLibSQLTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        if ( $this->saved !== null )
            expLibSQLTestKit::restoreDebug( $this->saved );
    }

    /** LD-01 */
    public function testConnect()
    {
        $server = new expLibSQLFakeConnection();
        $db = expLibSQLTestKit::driver( $server );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( 'sqlite', $db->databaseName() );
        $this->assertSame( 'sqlite', $db->sqlHelperDialect() );
        $this->assertSame( SQLite3::version()['versionString'], $db->databaseServerVersion()['string'] );
        $this->assertSame( 'Hrana 3 over HTTP', $db->databaseClientVersion()['string'] );
        $this->assertSame( 'sqld 0.24.33 (fake)', $db->serverProduct() );
        $this->assertSame( 'http://127.0.0.1:8080', $db->Url );
        $this->assertSame( 'libsql', $db->Profile['name'] );
        $this->assertSame( array( 'exponential' ), $db->availableDatabases() );
        $this->assertFalse( $db->UseSlaveServer );
        $sent = $server->Sent[0][2]['requests'][0]['stmt']['sql'];
        $this->assertSame( 'SELECT sqlite_version()', $sent );
        // no PRAGMAs: the server's settings are its own
        foreach ( $server->Sent as $request )
            foreach ( isset( $request[2]['requests'] ) ? (array)$request[2]['requests'] : array() as $r )
                $this->assertStringNotContainsStringIgnoringCase( 'PRAGMA', isset( $r['stmt']['sql'] ) ? $r['stmt']['sql'] : '' );
        $db->close();
        $this->assertFalse( $db->isConnected() );
    }

    /** LD-02 */
    public function testConnectFails()
    {
        $server = new expLibSQLFakeConnection();
        $server->DropNext = 10;
        try
        {
            expLibSQLTestKit::driver( $server, array( 'libsql' => array( 'ConnectAttempts' => 2, 'ConnectWait' => 0 ) ) );
            $this->fail( 'connected to a server that cannot be reached' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( 'could not be reached', $e->getMessage() );
        }
        $server = new expLibSQLFakeConnection( 'http://127.0.0.1:8080', 'secret-token' );
        $server->RequiredToken = 'another';
        try
        {
            expLibSQLTestKit::driver( $server, array( 'password' => 'secret-token' ) );
            $this->fail( 'connected with a refused token' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( 'HTTP 401', $e->getMessage() );
            $this->assertStringNotContainsString( 'secret-token', $e->getMessage() );
        }
        try
        {
            new expTursoDB( expLibSQLTestKit::parameters( array( 'server' => 'libsql://exp-acme.turso.io', 'libsql_connection' => new expLibSQLFakeConnection() ) ) );
            $this->fail( 'Turso without a token' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( 'needs an auth token', $e->getMessage() );
        }
        $turso = new expTursoDB( expLibSQLTestKit::parameters( array( 'server' => 'libsql://exp-acme.turso.io', 'password' => 't',
                                                                      'libsql_connection' => new expLibSQLFakeConnection() ) ) );
        $this->assertSame( 'https://exp-acme.turso.io', $turso->Url );
        $this->assertSame( 'turso', $turso->Profile['name'] );
    }

    /** LD-03 */
    public function testQueries()
    {
        $db = expLibSQLTestKit::driver();
        $this->assertTrue( $db->query( 'CREATE TABLE ezt ( id INTEGER PRIMARY KEY, name TEXT )' ) );
        for ( $i = 1; $i <= 6; $i++ )
            $db->query( "INSERT INTO ezt ( name ) VALUES ( '" . $db->escapeString( "n'$i" ) . "' )" );
        $this->assertSame( array( 2 => array( 'id' => 3, 'name' => "n'3" ), 3 => array( 'id' => 4, 'name' => "n'4" ) ),
                           $db->arrayQuery( 'SELECT id, name FROM ezt ORDER BY id', array( 'limit' => 2, 'offset' => 2 ) ) );
        $this->assertSame( array( "n'1", "n'2" ), $db->arrayQuery( 'SELECT name FROM ezt ORDER BY id', array( 'limit' => 2, 'column' => 'name' ) ) );
        $this->assertSame( array( array( 'name' => "n'6" ) ), $db->arrayQuery( 'SELECT ezt.name FROM ezt WHERE id = 6' ) );
        // a MySQL session switch the installers send: nothing to do
        $this->assertTrue( $db->query( 'SET NAMES utf8' ) );
        $this->assertFalse( $db->query( 'INSERT INTO ezt ( id, name ) VALUES ( 1, \'x\' )' ) );
        $this->assertSame( 19, $db->errorNumber() );
        $this->assertStringContainsString( 'UNIQUE constraint failed', $db->errorMessage() );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM missing' ) );
        $this->assertSame( 'no such table: missing', $db->errorMessage() );
        // a joined UPDATE is rewritten for SQLite, as on a local file
        $db->query( 'CREATE TABLE ezu ( id INTEGER PRIMARY KEY, other TEXT )' );
        $db->query( "INSERT INTO ezu ( id, other ) VALUES ( 2, 'joined' )" );
        $this->assertTrue( $db->query( 'UPDATE ezt t INNER JOIN ( SELECT id, other FROM ezu ) u ON t.id = u.id SET t.name = u.other' ) );
        $this->assertSame( array( 'joined' ), $db->arrayQuery( 'SELECT name FROM ezt WHERE id = 2', array( 'column' => 'name' ) ) );
    }

    /** LD-04 */
    public function testTransactions()
    {
        $server = new expLibSQLFakeConnection();
        $db = expLibSQLTestKit::driver( $server );
        $db->query( 'CREATE TABLE ezt ( id INTEGER PRIMARY KEY, name TEXT )' );
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'a' )" );
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'b' )" );
        $db->commit();
        $this->assertTrue( $server->inTransaction() );
        $db->commit();
        $this->assertFalse( $server->inTransaction() );
        $this->assertContains( 'BEGIN IMMEDIATE', array_map( function ( $r ) { return $r[2]['requests'][0]['stmt']['sql'] ?? ''; }, $server->Sent ) );
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'c' )" );
        $db->rollback();
        $this->assertSame( array( 2 ), $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezt', array( 'column' => 'c' ) ) );
        // a failed statement inside a transaction: rolled back, an exception in exception mode
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'd' )" );
        try
        {
            $db->query( "INSERT INTO ezt ( id, name ) VALUES ( 1, 'dup' )" );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 19, $e->getCode() );
        }
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertFalse( $server->inTransaction() );
        $this->assertSame( array( 2 ), $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezt', array( 'column' => 'c' ) ) );
        // a transaction the server ended (its timeout, another writer waiting): the statement fails with the reason,
        // the transaction is rolled back (nothing of it written) and the connection works on
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'e' )" );
        $server->otherClient( 'BEGIN IMMEDIATE' );
        $server->otherClient( 'ROLLBACK' );
        try
        {
            $db->query( "INSERT INTO ezt ( name ) VALUES ( 'f' )" );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 4, $e->getCode() );
            $this->assertStringContainsString( 'the libSQL server ended the transaction', $e->getMessage() );
        }
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertFalse( $server->inTransaction() );
        $this->assertSame( array( 2 ), $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezt', array( 'column' => 'c' ) ) );
    }

    /** LD-05 */
    public function testSerialIds()
    {
        $db = expLibSQLTestKit::driver();
        $db->query( 'CREATE TABLE ezt ( id INTEGER PRIMARY KEY, name TEXT )' );
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'a' )" );
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'b' )" );
        $this->assertSame( 2, (int)$db->lastSerialID( 'ezt', 'id' ) );
        // a two-column key: SQLite numbers the rowid, the driver gives MAX + 1
        $db->query( 'CREATE TABLE ezv ( id INTEGER, version INTEGER NOT NULL, PRIMARY KEY ( id, version ) )' );
        $db->query( 'INSERT INTO ezv ( id, version ) VALUES ( 40, 1 )' );
        $db->query( 'INSERT INTO ezv ( version ) VALUES ( 1 )' );
        $this->assertSame( 41, (int)$db->lastSerialID( 'ezv', 'id' ) );
        $this->assertTrue( $db->serialIDNeedsValue( 'ezv', 'version' ) );
        $this->assertFalse( $db->serialIDNeedsValue( 'ezt', 'id' ) );
    }

    /** LD-06 */
    public function testBitAggregates()
    {
        $this->assertSame( 'SELECT 1', expLibSQLDB::rewriteFunctions( 'SELECT 1' ) );
        $sql = expLibSQLDB::rewriteFunctions( 'SELECT BIT_OR( mask & ( 1 | 2 ) ) AS o FROM t' );
        $this->assertStringStartsWith( 'SELECT COALESCE((MAX(( mask & ( 1 | 2 ) )&1)|MAX(( mask & ( 1 | 2 ) )&2)|', $sql );
        $this->assertStringEndsWith( '),0) AS o FROM t', $sql );
        $this->assertStringNotContainsString( 'BIT_OR', $sql );
        $db = expLibSQLTestKit::driver();
        $db->query( 'CREATE TABLE ezm ( g INT, mask INT )' );
        foreach ( array( array( 1, 5 ), array( 1, 6 ), array( 2, 7 ), array( 2, 3 ), array( 3, 1 << 40 ) ) as $row )
            $db->query( "INSERT INTO ezm VALUES ( {$row[0]}, {$row[1]} )" );
        $rows = $db->arrayQuery( 'SELECT g, BIT_OR( mask ) AS o, bit_and( mask ) AS a FROM ezm GROUP BY g ORDER BY g' );
        $this->assertSame( array( array( 'g' => 1, 'o' => 7, 'a' => 4 ), array( 'g' => 2, 'o' => 7, 'a' => 3 ),
                                  array( 'g' => 3, 'o' => 1 << 40, 'a' => 1 << 40 ) ), $rows );
        $this->assertSame( array( array( 'o' => 0, 'a' => 0 ) ), $db->arrayQuery( 'SELECT BIT_OR( mask ) AS o, BIT_AND( mask ) AS a FROM ezm WHERE g = 9' ) );
    }

    /** LD-07 */
    public function testMd5AndEscape()
    {
        $db = expLibSQLTestKit::driver();
        $this->assertSame( " '" . md5( "it's" ) . "' ", $db->md5( "'it''s'" ) );
        $this->assertSame( " lower( hex( md5( name ) ) ) ", $db->md5( 'name' ) );
        $this->assertSame( "it''s", $db->escapeString( "it's" ) );
        $this->assertSame( '', $db->escapeString( null ) );
        $db->close();
        $this->assertSame( "it''s", $db->escapeString( "it's" ) );
    }

    /** LD-08 */
    public function testTempTables()
    {
        $server = new expLibSQLFakeConnection();
        $db = expLibSQLTestKit::driver( $server );
        $this->assertTrue( $db->createTempTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, published int )' ) );
        $this->assertSame( array( 'ezsearch_tmp_0' ), $db->tempTables() );
        $db->query( 'INSERT INTO ezsearch_tmp_0 VALUES ( 1, 2 )' );
        $this->assertSame( array( 1 ), $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezsearch_tmp_0', array( 'column' => 'c' ) ) );
        $db->dropTempTable( 'DROP TABLE ezsearch_tmp_0' );
        $this->assertSame( array(), $db->tempTables() );
        $this->assertSame( 0, $server->querySingle( "SELECT COUNT(*) FROM sqlite_master WHERE name = 'ezsearch_tmp_0'" ) );
        $db->createTempTable( 'CREATE TEMPORARY TABLE eznode_count_1 ( count int )' );
        $db->createTempTable( 'CREATE TEMPORARY TABLE eznode_count_2 ( count int )' );
        $db->dropTempTableList( array( 'eznode_count_2' ) );
        $this->assertSame( array( 'eznode_count_1' ), $db->tempTables() );
        $db->close();
        $this->assertSame( 0, $server->querySingle( "SELECT COUNT(*) FROM sqlite_master WHERE name LIKE 'eznode_count_%'" ) );
        // anything else goes through as it is
        $db = expLibSQLTestKit::driver();
        $this->assertTrue( $db->createTempTable( 'CREATE TABLE plain ( a int )' ) );
        $this->assertSame( array(), $db->tempTables() );
    }

    /** LD-09 */
    public function testServerDatabase()
    {
        $server = new expLibSQLFakeConnection();
        $db = expLibSQLTestKit::driver( $server );
        $db->query( 'CREATE TABLE ezt ( id INTEGER PRIMARY KEY, name TEXT UNIQUE )' );
        $db->query( 'CREATE INDEX ezt_name ON ezt ( name )' );
        $db->query( 'CREATE VIEW ezv AS SELECT * FROM ezt' );
        $this->assertSame( array( 'ezt' => eZDBInterface::RELATION_TABLE ), $db->eZTableList() );
        // a write takes no writer gate: no lock file anywhere
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'a' )" );
        $db->commit();
        $this->assertFileDoesNotExist( eZSQLite3DB::filePath( 'exponential' ) . '.writer-lock' );
        $this->assertTrue( $db->removeDatabase( 'exponential' ) );
        $this->assertSame( 0, $server->querySingle( "SELECT COUNT(*) FROM sqlite_master WHERE name NOT LIKE 'sqlite_%'" ) );
        $stats = $db->transportStatistics();
        $this->assertGreaterThan( 5, $stats['statements'] );
        $this->assertSame( 0, $stats['retries'] );
        $db->createDatabase( 'anything' );
    }

    /** LD-10 */
    public function testDataConnections()
    {
        $types = expDataConnections::engineTypes();
        $this->assertSame( 'libsql', $types['libsql']['ez'] );
        $this->assertSame( 8080, $types['libsql']['port'] );
        $this->assertSame( 'turso', $types['turso']['ez'] );
        $this->assertFalse( $types['libsql']['file'] );
        $engines = expDataConnections::engines();
        $this->assertTrue( $engines['turso']['untested'] );
        $this->assertFalse( $engines['libsql']['untested'] );
        $c = array( 'identifier' => 'edge', 'name' => 'Edge', 'engine' => 'libsql', 'server' => '10.0.0.5', 'port' => 8080,
                    'database' => 'exponential', 'user' => '', 'secret' => '', 'options' => array( 'url' => 'http://10.0.0.5:8080', 'tls' => 'disabled' ) );
        $normal = expDataConnections::normalize( $c );
        $this->assertSame( array( 'url' => 'http://10.0.0.5:8080', 'tls' => 'disabled' ), $normal['options'] );
        $this->assertSame( array(), expDataConnections::problems( $normal ) );
        foreach ( array( 'ftp://10.0.0.5' => 'The url must be', 'not a url' => 'The url must be' ) as $url => $problem )
        {
            $problems = expDataConnections::problems( expDataConnections::normalize( array( 'options' => array( 'url' => $url ) ) + $c ) );
            $this->assertStringStartsWith( $problem, implode( ' ', $problems ), $url );
        }
        $problems = expDataConnections::problems( expDataConnections::normalize( array( 'engine' => 'mysql' ) + $c ) );
        $this->assertContains( 'The option url is for libsql and turso.', $problems );
        if ( $engines['libsql']['available'] )
        {
            $plan = expDataConnections::plan( $normal, 'token' );
            $this->assertSame( '', $plan['error'] );
            $this->assertSame( 'ez', $plan['via'] );
            $this->assertSame( 'libsql', $plan['implementation'] );
            $this->assertSame( array( 'URL' => 'http://10.0.0.5:8080', 'TLS' => 'disabled' ), $plan['parameters']['libsql'] );
            $this->assertSame( 'token', $plan['parameters']['password'] );
        }
    }
}
