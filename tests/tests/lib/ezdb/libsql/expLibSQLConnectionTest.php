<?php
/**
 * The libSQL transport (lib/ezdb/classes/explibsqlconnection.php, explibsqlresult.php, explibsqlprofile.php), without a
 * libSQL server: the pure functions, and the requests on a stand-in server (expLibSQLFakeConnection):
 *
 *  LC-01 - normalizeUrl(): libsql:// and wss:// are https://, ws:// is http://, no scheme is http://, path dropped
 *  LC-02 - decodeValue(): integer (as int, beyond PHP's range as string), float, text, blob (base64 without padding), null
 *  LC-03 - sqliteCode() and message(): Hrana's codes as SQLite's result codes, "SQLite error: " left out
 *  LC-04 - opensTransaction() and transactionAfter(): BEGIN and SAVEPOINT keep a stream, COMMIT/ROLLBACK end it
 *  LC-05 - exec(), query(), querySingle(), changes(), lastInsertRowID(), a statement list sent as a sequence
 *  LC-06 - One stream for all statements (KeepStream); keepStreamOpen( false ) closes it outside a transaction
 *  LC-07 - An expired stream or a lost connection: sent again once outside a transaction; inside it the error stays
 *  LC-08 - A server of Hrana 2 only: /v2/pipeline, the transaction known from the statements
 *  LC-09 - A refused token: AUTH, code 23, nothing sent again; close() closes the stream
 *  LC-10 - expLibSQLResult: fetchArray() in the three modes, numColumns(), columnName(), reset(), finalize()
 *  LC-11 - expLibSQLProfile: the profiles, apply(), url() from Server/Port/TLS/URL, token() from the settings
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLibSQLTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('libsql')]
class expLibSQLConnectionTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The stand-in libSQL server needs PHP\'s sqlite3 extension' );
    }

    /** LC-01 */
    public function testNormalizeUrl()
    {
        $this->assertSame( 'https://exp-acme.turso.io', expLibSQLConnection::normalizeUrl( 'libsql://exp-acme.turso.io' ) );
        $this->assertSame( 'https://exp-acme.turso.io', expLibSQLConnection::normalizeUrl( 'wss://exp-acme.turso.io/' ) );
        $this->assertSame( 'http://10.0.0.5:8080', expLibSQLConnection::normalizeUrl( 'ws://10.0.0.5:8080' ) );
        $this->assertSame( 'http://10.0.0.5:8080', expLibSQLConnection::normalizeUrl( '10.0.0.5:8080' ) );
        $this->assertSame( 'https://h.example:8443', expLibSQLConnection::normalizeUrl( 'HTTPS://h.example:8443/v2/pipeline' ) );
        $this->assertSame( 'http://[::1]:8080', expLibSQLConnection::normalizeUrl( 'http://[::1]:8080' ) );
    }

    /** LC-02 */
    public function testDecodeValue()
    {
        $this->assertSame( 42, expLibSQLConnection::decodeValue( array( 'type' => 'integer', 'value' => '42' ) ) );
        $this->assertSame( -9223372036854775807 - 1, expLibSQLConnection::decodeValue( array( 'type' => 'integer', 'value' => '-9223372036854775808' ) ) );
        $this->assertSame( '99999999999999999999', expLibSQLConnection::decodeValue( array( 'type' => 'integer', 'value' => '99999999999999999999' ) ) );
        $this->assertSame( 1.5, expLibSQLConnection::decodeValue( array( 'type' => 'float', 'value' => 1.5 ) ) );
        $this->assertSame( 1.0, expLibSQLConnection::decodeValue( array( 'type' => 'float', 'value' => 1 ) ) );
        $this->assertSame( "it's ünïcode", expLibSQLConnection::decodeValue( array( 'type' => 'text', 'value' => "it's ünïcode" ) ) );
        $this->assertSame( "\x00\xff", expLibSQLConnection::decodeValue( array( 'type' => 'blob', 'base64' => 'AP8' ) ) );
        $this->assertSame( md5( '1', true ), expLibSQLConnection::decodeValue( array( 'type' => 'blob', 'base64' => 'xMpCOKC5I4INzFCab3WEmw' ) ) );
        $this->assertNull( expLibSQLConnection::decodeValue( array( 'type' => 'null' ) ) );
    }

    /** LC-03 */
    public function testCodesAndMessages()
    {
        $this->assertSame( 19, expLibSQLConnection::sqliteCode( 'SQLITE_CONSTRAINT' ) );
        $this->assertSame( 19, expLibSQLConnection::sqliteCode( 'SQLITE_CONSTRAINT_UNIQUE' ) );
        $this->assertSame( 5, expLibSQLConnection::sqliteCode( 'SQLITE_BUSY' ) );
        $this->assertSame( 8, expLibSQLConnection::sqliteCode( 'SQLITE_READONLY' ) );
        $this->assertSame( 1, expLibSQLConnection::sqliteCode( 'SQLITE_UNKNOWN' ) );
        $this->assertSame( 1, expLibSQLConnection::sqliteCode( 'SQL_PARSE_ERROR' ) );
        $this->assertSame( 4, expLibSQLConnection::sqliteCode( 'TRANSACTION_TIMEOUT' ) );
        $this->assertSame( expLibSQLConnection::TRANSPORT_ERROR, expLibSQLConnection::sqliteCode( 'STREAM_EXPIRED' ) );
        $this->assertSame( 'UNIQUE constraint failed: t.id', expLibSQLConnection::message( array( 'message' => 'SQLite error: UNIQUE constraint failed: t.id' ), 'SQLITE_CONSTRAINT' ) );
        $this->assertStringContainsString( 'transaction timeout', expLibSQLConnection::message( array( 'message' => 'Transaction timed out' ), 'TRANSACTION_TIMEOUT' ) );
        $this->assertSame( 'SQL_PARSE_ERROR', expLibSQLConnection::message( array(), 'SQL_PARSE_ERROR' ) );
        $this->assertSame( "it''s", expLibSQLConnection::escapeString( "it's" ) );
    }

    /** LC-04 */
    public function testTransactionStatements()
    {
        foreach ( array( 'BEGIN', 'begin immediate', "  -- note\nBEGIN TRANSACTION", '/* x */ SAVEPOINT a' ) as $sql )
            $this->assertTrue( expLibSQLConnection::opensTransaction( $sql ), $sql );
        foreach ( array( 'SELECT 1', 'COMMIT', 'INSERT INTO beginnings VALUES ( 1 )' ) as $sql )
            $this->assertFalse( expLibSQLConnection::opensTransaction( $sql ), $sql );
        $this->assertTrue( expLibSQLConnection::transactionAfter( 'UPDATE t SET a = 1', true ) );
        $this->assertFalse( expLibSQLConnection::transactionAfter( 'COMMIT', true ) );
        $this->assertFalse( expLibSQLConnection::transactionAfter( 'ROLLBACK', true ) );
        $this->assertTrue( expLibSQLConnection::transactionAfter( 'ROLLBACK TO a', true ) );
        $this->assertFalse( expLibSQLConnection::transactionAfter( 'SELECT 1', false ) );
    }

    /** LC-05 */
    public function testStatements()
    {
        $c = new expLibSQLFakeConnection();
        $this->assertTrue( $c->exec( 'CREATE TABLE t ( id INTEGER PRIMARY KEY, name TEXT, data BLOB )' ) );
        $this->assertTrue( $c->exec( "INSERT INTO t ( name ) VALUES ( 'a' )" ) );
        $this->assertSame( 1, $c->lastInsertRowID() );
        $this->assertTrue( $c->exec( "INSERT INTO t ( name, data ) VALUES ( 'b', x'00ff' )" ) );
        $this->assertSame( 2, $c->lastInsertRowID() );
        $this->assertTrue( $c->exec( "UPDATE t SET name = name || 'x'" ) );
        $this->assertSame( 2, $c->changes() );
        // a statement that inserts nothing keeps the last id, as SQLite's connection does
        $this->assertTrue( $c->exec( 'DELETE FROM t WHERE id = 99' ) );
        $this->assertSame( 2, $c->lastInsertRowID() );
        $result = $c->query( 'SELECT id, name, data FROM t ORDER BY id' );
        $this->assertInstanceOf( 'expLibSQLResult', $result );
        $this->assertSame( array( 'id' => 1, 'name' => 'ax', 'data' => null ), $result->fetchArray( SQLITE3_ASSOC ) );
        $this->assertSame( array( 'id' => 2, 'name' => 'bx', 'data' => "\x00\xff" ), $result->fetchArray( SQLITE3_ASSOC ) );
        $this->assertFalse( $result->fetchArray( SQLITE3_ASSOC ) );
        $this->assertSame( 2, $c->querySingle( 'SELECT COUNT(*) FROM t' ) );
        $this->assertSame( array( 'n' => 'ax' ), $c->querySingle( 'SELECT name AS n FROM t WHERE id = 1', true ) );
        $this->assertNull( $c->querySingle( 'SELECT name FROM t WHERE id = 99' ) );
        // several statements: the server refuses them in one execute, the connection sends them as a sequence
        $this->assertTrue( $c->exec( "INSERT INTO t ( name ) VALUES ( 'c' ); INSERT INTO t ( name ) VALUES ( 'd' );" ) );
        $this->assertSame( 4, $c->querySingle( 'SELECT COUNT(*) FROM t' ) );
        $this->assertSame( 0, $c->lastErrorCode() );
        // an error: SQLite's code and message
        $this->assertFalse( $c->exec( "INSERT INTO t ( id, name ) VALUES ( 1, 'dup' )" ) );
        $this->assertSame( 19, $c->lastErrorCode() );
        $this->assertStringStartsWith( 'UNIQUE constraint failed', $c->lastErrorMsg() );
        $this->assertSame( 'SQLITE_CONSTRAINT', $c->hranaCode() );
        $this->assertFalse( $c->query( 'SELECT * FROM missing' ) );
        $this->assertSame( 1, $c->lastErrorCode() );
        $this->assertSame( 'no such table: missing', $c->lastErrorMsg() );
        $this->assertSame( 'sqld 0.24.33 (fake)', $c->serverVersion() );
        $this->assertTrue( $c->ping() );
    }

    /** LC-06 */
    public function testOneStream()
    {
        $c = new expLibSQLFakeConnection();
        for ( $i = 0; $i < 5; $i++ )
            $c->querySingle( 'SELECT 1' );
        $this->assertSame( 1, $c->Opened );
        $this->assertSame( 0, $c->Closed );
        // every request asks whether the stream is in a transaction (Hrana 3)
        $last = end( $c->Sent );
        $this->assertSame( array( 'execute', 'get_autocommit' ), array_column( $last[2]['requests'], 'type' ) );
        $this->assertNotNull( $last[2]['baton'] );
        $c->exec( 'BEGIN' );
        $this->assertTrue( $c->inTransaction() );
        $c->exec( 'COMMIT' );
        $this->assertFalse( $c->inTransaction() );
        $c->close();
        $this->assertSame( 1, $c->Closed );

        // a stream per statement outside a transaction
        $c = new expLibSQLFakeConnection();
        $c->keepStreamOpen( false );
        $c->querySingle( 'SELECT 1' );
        $c->querySingle( 'SELECT 1' );
        $this->assertSame( 2, $c->Opened );
        $this->assertSame( 2, $c->Closed );
        $c->exec( 'CREATE TABLE t ( a )' );
        $c->exec( 'BEGIN' );
        $c->exec( 'INSERT INTO t VALUES ( 1 )' );
        $c->exec( 'COMMIT' );
        // BEGIN opened one, kept for the transaction, closed by the next statement after it
        $c->querySingle( 'SELECT COUNT(*) FROM t' );
        $this->assertSame( $c->Opened, $c->Closed );
        $this->assertSame( 1, $c->querySingle( 'SELECT COUNT(*) FROM t' ) );
    }

    /** LC-07 */
    public function testRetries()
    {
        $c = new expLibSQLFakeConnection();
        $c->querySingle( 'SELECT 1' );
        $c->ExpireNext = true;
        $this->assertSame( 2, $c->querySingle( 'SELECT 2' ) );
        $this->assertSame( 1, $c->Retries );
        $c->DropNext = 1;
        $this->assertSame( 3, $c->querySingle( 'SELECT 3' ) );
        $this->assertSame( 2, $c->Retries );
        // twice lost: the error
        $c->DropNext = 2;
        $this->assertFalse( $c->querySingle( 'SELECT 4' ) );
        $this->assertSame( expLibSQLConnection::TRANSPORT_ERROR, $c->lastErrorCode() );
        $this->assertStringContainsString( 'could not be reached: Connection refused', $c->lastErrorMsg() );
        // inside a transaction: never sent again, the transaction is lost
        $c->exec( 'CREATE TABLE t ( a )' );
        $c->exec( 'BEGIN' );
        $c->ExpireNext = true;
        $retries = $c->Retries;
        $this->assertFalse( $c->exec( 'INSERT INTO t VALUES ( 1 )' ) );
        $this->assertSame( $retries, $c->Retries );
        $this->assertStringContainsString( 'the transaction on the libSQL server is lost', $c->lastErrorMsg() );
        $this->assertFalse( $c->inTransaction() );
        // a transaction the server took away (its timeout, another writer waiting)
        $c->exec( 'BEGIN' );
        $c->exec( 'INSERT INTO t VALUES ( 2 )' );
        $c->otherClient( 'BEGIN IMMEDIATE' );
        $c->otherClient( 'ROLLBACK' );
        $this->assertFalse( $c->exec( 'INSERT INTO t VALUES ( 3 )' ) );
        $this->assertSame( 4, $c->lastErrorCode() );
        $this->assertSame( 'TRANSACTION_TIMEOUT', $c->hranaCode() );
        $this->assertFalse( $c->inTransaction() );
    }

    /** LC-08 */
    public function testHrana2()
    {
        $c = new expLibSQLFakeConnection();
        $c->Hrana2Only = true;
        $this->assertSame( 1, $c->querySingle( 'SELECT 1' ) );
        $this->assertSame( 2, $c->hranaVersion() );
        $c->exec( 'CREATE TABLE t ( a )' );
        $c->exec( 'BEGIN' );
        $this->assertTrue( $c->inTransaction() );
        $c->exec( 'INSERT INTO t VALUES ( 1 )' );
        $this->assertTrue( $c->inTransaction() );
        $c->exec( 'COMMIT' );
        $this->assertFalse( $c->inTransaction() );
        $this->assertSame( 1, $c->querySingle( 'SELECT COUNT(*) FROM t' ) );
        $last = end( $c->Sent );
        $this->assertStringEndsWith( '/v2/pipeline', $last[1] );
        $this->assertNotContains( 'get_autocommit', array_column( $last[2]['requests'], 'type' ) );
    }

    /** LC-09 */
    public function testTokenAndClose()
    {
        $c = new expLibSQLFakeConnection( 'http://127.0.0.1:8080', 'wrong' );
        $c->RequiredToken = 'right';
        $this->assertFalse( $c->querySingle( 'SELECT 1' ) );
        $this->assertSame( 23, $c->lastErrorCode() );
        $this->assertSame( 'AUTH', $c->hranaCode() );
        $this->assertSame( 0, $c->Retries );
        $c = new expLibSQLFakeConnection( 'http://127.0.0.1:8080', 'right' );
        $c->RequiredToken = 'right';
        $this->assertSame( 1, $c->querySingle( 'SELECT 1' ) );
        $c->exec( 'CREATE TABLE t ( a )' );
        $c->exec( 'BEGIN' );
        $c->exec( 'INSERT INTO t VALUES ( 1 )' );
        // closing a stream with a transaction rolls it back
        $c->close();
        $this->assertFalse( $c->inTransaction() );
        $this->assertSame( 0, $c->querySingle( 'SELECT COUNT(*) FROM t' ) );
    }

    /** LC-10 */
    public function testResult()
    {
        $r = new expLibSQLResult( array( 'a', 'b', 'a' ), array( array( 1, 2, 3 ), array( 4, 5, 6 ) ) );
        $this->assertSame( 3, $r->numColumns() );
        $this->assertSame( 2, $r->numRows() );
        $this->assertSame( 'b', $r->columnName( 1 ) );
        $this->assertFalse( $r->columnName( 7 ) );
        $this->assertSame( array( 'a' => 3, 'b' => 2 ), $r->fetchArray( SQLITE3_ASSOC ) );
        $this->assertSame( array( 4, 5, 6 ), $r->fetchArray( SQLITE3_NUM ) );
        $this->assertFalse( $r->fetchArray() );
        $this->assertTrue( $r->reset() );
        $this->assertSame( array( 0 => 1, 'a' => 3, 1 => 2, 'b' => 2, 2 => 3 ), $r->fetchArray() );
        $this->assertTrue( $r->finalize() );
        $this->assertFalse( $r->fetchArray() );
    }

    /** LC-11 */
    public function testProfiles()
    {
        $this->assertSame( array( 'libsql', 'turso' ), array_keys( expLibSQLProfile::all() ) );
        $this->assertNull( expLibSQLProfile::get( 'mysql' ) );
        $libsql = expLibSQLProfile::apply( expLibSQLProfile::get( 'libsql' ), array() );
        $turso = expLibSQLProfile::apply( expLibSQLProfile::get( 'TURSO' ), array() );
        $this->assertSame( 'http://10.0.0.5:8080', expLibSQLProfile::url( $libsql, '10.0.0.5', '' ) );
        $this->assertSame( 'http://10.0.0.5:9000', expLibSQLProfile::url( $libsql, '10.0.0.5', '9000' ) );
        $this->assertSame( 'https://db.example', expLibSQLProfile::url( $libsql, 'db.example', '443' ) );
        $this->assertSame( 'http://127.0.0.1:8080', expLibSQLProfile::url( $libsql, '', '' ) );
        $this->assertSame( 'http://[::1]:8080', expLibSQLProfile::url( $libsql, '::1', '8080' ) );
        $tls = expLibSQLProfile::apply( expLibSQLProfile::get( 'libsql' ), array( 'TLS' => 'enabled' ) );
        $this->assertSame( 'https://db.example:8080', expLibSQLProfile::url( $tls, 'db.example', '8080' ) );
        $this->assertSame( 'https://exp-acme.turso.io', expLibSQLProfile::url( $turso, 'exp-acme.turso.io', '' ) );
        $this->assertSame( 'https://exp-acme.turso.io', expLibSQLProfile::url( $turso, 'libsql://exp-acme.turso.io', '' ) );
        $url = expLibSQLProfile::apply( expLibSQLProfile::get( 'libsql' ), array( 'URL' => 'libsql://x.example', 'Unknown' => 'y' ) );
        $this->assertSame( 'https://x.example', expLibSQLProfile::url( $url, 'ignored', '1' ) );
        $this->assertArrayNotHasKey( 'Unknown', $url['settings'] );
        $this->assertSame( 'pw', expLibSQLProfile::token( $libsql, 'pw' ) );
        $token = expLibSQLProfile::apply( expLibSQLProfile::get( 'libsql' ), array( 'AuthToken' => 'tok' ) );
        $this->assertSame( 'tok', expLibSQLProfile::token( $token, 'pw' ) );
        putenv( 'TURSO_AUTH_TOKEN=from-env' );
        $this->assertSame( 'from-env', expLibSQLProfile::token( $turso, '' ) );
        putenv( 'TURSO_AUTH_TOKEN' );
        $this->assertTrue( $turso['token_required'] );
        $this->assertFalse( $libsql['token_required'] );
    }
}
