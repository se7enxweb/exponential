<?php
/**
 * The generic PDO driver (lib/ezdb/classes/exppdodb.php), the eZDB instance of engines without a native driver:
 *
 *  PD-01 - dialect(): every PDO driver name to its SQL dialect; a pdo_dialect parameter overrides it
 *  PD-02 - limitClause(): LIMIT/OFFSET per dialect (SQL Server and Oracle OFFSET ... FETCH NEXT, SQL Server adds an
 *          ORDER BY when the statement has none), nothing added without limit and offset
 *  PD-03 - escapeFor(): quotes doubled for SQL Server, Oracle, SQLite and generic, backslashes for MySQL, NUL dropped
 *  PD-04 - A failed connection throws eZDBNoConnectionException without the password, also one in the DSN
 *  PD-05 - query() and arrayQuery() on SQLite through PDO: rows, column, limit, offset (keys from the offset)
 *  PD-06 - Transactions: a rollback leaves nothing, a commit keeps the row; lastSerialID() gives the new id
 *  PD-07 - eZTableList() lists every table; databaseName() is pdo_<driver>; versions are read
 *  PD-08 - A failed statement returns false with the standard error handling, throws eZDBException with the
 *          exceptions error handling, and its message holds the reason
 *  PD-09 - The SQL expressions per dialect: bitAnd, bitOr, concatString, md5, subString
 *
 * No installation database: SQLite through pdo_sqlite in a file of var/tmp, removed afterwards.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezdb
 */

require_once dirname( __DIR__, 4 ) . '/lib/ezdb/classes/exppdodb.php';

class expPDODBTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $file;

    /** @var array the debug globals before the test */
    private $saved = array();

    protected function setUp(): void
    {
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
        $dir = dirname( __DIR__, 4 ) . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $this->file = $dir . '/t691-pdodb-' . getmypid() . '.db';
    }

    protected function tearDown(): void
    {
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
    }

    private function db( array $extra = array() )
    {
        if ( !expPDODB::available( 'sqlite' ) )
            $this->markTestSkipped( 'This PHP has no pdo_sqlite' );
        $db = new expPDODB( $extra + array( 'pdo_dsn' => 'sqlite:' . $this->file, 'pdo_driver' => 'sqlite' ) );
        $this->assertTrue( $db->isConnected() );
        return $db;
    }

    private function filled()
    {
        $db = $this->db();
        $this->assertTrue( $db->query( 'CREATE TABLE item ( id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL )' ) );
        foreach ( array( 'a', 'b', 'c', 'd', 'e' ) as $name )
            $this->assertTrue( $db->query( "INSERT INTO item ( name ) VALUES ( '$name' )" ) );
        return $db;
    }

    /** PD-01 */
    public function testDialects()
    {
        $expected = array( 'sqlsrv' => 'sqlserver', 'dblib' => 'sqlserver', 'mssql' => 'sqlserver', 'oci' => 'oracle',
                           'mysql' => 'mysql', 'pgsql' => 'postgresql', 'sqlite' => 'sqlite', 'odbc' => 'generic',
                           'firebird' => 'generic', '' => 'generic', 'SQLSRV' => 'sqlserver' );
        foreach ( $expected as $driver => $dialect )
            $this->assertSame( $dialect, expPDODB::dialect( $driver ), "driver '$driver'" );

        $db = $this->db( array( 'pdo_dialect' => 'generic' ) );
        $this->assertSame( 'generic', $db->sqlDialect(), 'pdo_dialect overrides the driver' );
        $db = $this->db( array( 'pdo_driver' => '' ) );
        $this->assertSame( 'sqlite', $db->pdoDriver(), 'the driver from the DSN prefix' );
        $this->assertSame( 'sqlite', $db->sqlDialect() );
    }

    /** PD-02 */
    public function testLimitClause()
    {
        $sql = 'SELECT id FROM item';
        $this->assertSame( $sql, expPDODB::limitClause( 'sqlserver', $sql, false, 0 ) );
        $this->assertSame( "SELECT id FROM item\nORDER BY (SELECT NULL)\nOFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY",
                           expPDODB::limitClause( 'sqlserver', $sql, 10, 20 ) );
        $this->assertSame( "SELECT id FROM item ORDER BY id\nOFFSET 0 ROWS FETCH NEXT 5 ROWS ONLY",
                           expPDODB::limitClause( 'sqlserver', 'SELECT id FROM item ORDER BY id;', 5, 0 ) );
        $this->assertSame( "SELECT id FROM item WHERE id IN ( SELECT id FROM x ORDER BY id )\nORDER BY (SELECT NULL)\nOFFSET 3 ROWS",
                           expPDODB::limitClause( 'sqlserver', 'SELECT id FROM item WHERE id IN ( SELECT id FROM x ORDER BY id )', false, 3 ),
                           'an ORDER BY inside a subquery is not the statement\'s' );
        $this->assertSame( "SELECT id FROM item\nOFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY", expPDODB::limitClause( 'oracle', $sql, 10, 20 ) );
        $this->assertSame( "SELECT id FROM item\nOFFSET 7 ROWS", expPDODB::limitClause( 'oracle', $sql, false, 7 ) );
        $this->assertSame( "SELECT id FROM item\nLIMIT 10 OFFSET 20", expPDODB::limitClause( 'mysql', $sql, 10, 20 ) );
        $this->assertSame( "SELECT id FROM item\nLIMIT 18446744073709551615 OFFSET 4", expPDODB::limitClause( 'mysql', $sql, false, 4 ) );
        $this->assertSame( "SELECT id FROM item\nLIMIT -1 OFFSET 4", expPDODB::limitClause( 'sqlite', $sql, false, 4 ) );
        $this->assertSame( "SELECT id FROM item\nLIMIT 3 OFFSET 0", expPDODB::limitClause( 'postgresql', $sql, 3, 0 ) );
        $this->assertSame( "SELECT id FROM item\n OFFSET 4", expPDODB::limitClause( 'postgresql', $sql, false, 4 ) );
    }

    /** PD-03 */
    public function testEscaping()
    {
        foreach ( array( 'sqlserver', 'oracle', 'sqlite', 'generic' ) as $dialect )
            $this->assertSame( "O''Brien \\ x", expPDODB::escapeFor( $dialect, "O'Brien \\ x\0" ), $dialect );
        $this->assertSame( "O\\'Brien \\\\ \\n", expPDODB::escapeFor( 'mysql', "O'Brien \\ \n" ) );
        $db = $this->db();
        $this->assertSame( "it''s", $db->escapeString( "it's" ) );
        $this->assertSame( '', $db->escapeString( null ) );
    }

    /** PD-04 */
    public function testConnectionErrorHidesThePassword()
    {
        if ( !expPDODB::available( 'sqlite' ) )
            $this->markTestSkipped( 'This PHP has no pdo_sqlite' );
        $password = 'Sup3rS3cret!x';
        try
        {
            new expPDODB( array( 'pdo_dsn' => 'sqlite:/nonexistent-t691-dir/sub/x.db;password=' . $password, 'pdo_driver' => 'sqlite',
                                 'user' => 'reader', 'password' => $password ) );
            $this->fail( 'a connection to a file in a missing directory' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringNotContainsString( $password, $e->getMessage() );
            $this->assertStringContainsString( 'password=***', $e->getMessage() );
        }

        $this->assertSame( 'odbc:Driver={x};Server=h;UID=u;PWD=***;Database=d',
                           expPDODB::maskSecret( 'odbc:Driver={x};Server=h;UID=u;PWD={p;w};Database=d', '' ) );
        $this->assertSame( 'login failed for *** user', expPDODB::maskSecret( 'login failed for abc123 user', 'abc123' ) );

        try
        {
            new expPDODB( array( 'pdo_dsn' => 'nosuchdriver:host=x', 'pdo_driver' => 'nosuchdriver', 'password' => $password ) );
            $this->fail( 'a driver this PHP does not have' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( 'nosuchdriver', $e->getMessage() );
            $this->assertStringNotContainsString( $password, $e->getMessage() );
        }

        try
        {
            new expPDODB( array( 'pdo_driver' => 'sqlite' ) );
            $this->fail( 'no data source name' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertTrue( true );
        }
    }

    /** PD-05 */
    public function testQueries()
    {
        $db = $this->filled();
        $rows = $db->arrayQuery( 'SELECT id, name FROM item ORDER BY id' );
        $this->assertCount( 5, $rows );
        $this->assertSame( array( 'id', 'name' ), array_keys( $rows[0] ) );
        $this->assertSame( 'a', $rows[0]['name'] );

        $this->assertSame( array( 'a', 'b', 'c', 'd', 'e' ), $db->arrayQuery( 'SELECT name FROM item ORDER BY id', array( 'column' => 'name' ) ) );
        $this->assertSame( array( 'a', 'b' ), $db->arrayQuery( 'SELECT id, name FROM item ORDER BY id', array( 'column' => 1, 'limit' => 2 ) ) );

        $page = $db->arrayQuery( 'SELECT name FROM item ORDER BY id', array( 'limit' => 2, 'offset' => 2 ) );
        $this->assertSame( array( 2 => array( 'name' => 'c' ), 3 => array( 'name' => 'd' ) ), $page, 'keys count from the offset, as in the other drivers' );
        $this->assertSame( array( 4 => 'e' ), $db->arrayQuery( 'SELECT name FROM item ORDER BY id', array( 'offset' => 4, 'column' => 'name' ) ) );

        $joined = $db->arrayQuery( 'SELECT item.name FROM item WHERE item.id = 1' );
        $this->assertSame( array( array( 'name' => 'a' ) ), $joined );

        $this->assertTrue( $db->query( 'SET NAMES utf8' ), 'MySQL session switches are no error elsewhere' );
    }

    /** PD-06 */
    public function testTransactionsAndSerialIds()
    {
        $db = $this->filled();
        $db->begin();
        $db->query( "INSERT INTO item ( name ) VALUES ( 'gone' )" );
        $db->rollback();
        $this->assertSame( array(), $db->arrayQuery( "SELECT id FROM item WHERE name = 'gone'" ) );

        $db->begin();
        $db->query( "INSERT INTO item ( name ) VALUES ( 'kept' )" );
        $id = $db->lastSerialID( 'item', 'id' );
        $db->commit();
        $this->assertSame( 6, $id );
        $this->assertSame( array( 'kept' ), $db->arrayQuery( 'SELECT name FROM item WHERE id = 6', array( 'column' => 'name' ) ) );
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertSame( 'se_item', expPDODB::oracleSequence( 'item' ) );
        $this->assertSame( 's_contentobject', expPDODB::oracleSequence( 'ezcontentobject' ) );
    }

    /** PD-07 */
    public function testCatalogueAndVersions()
    {
        $db = $this->filled();
        $db->query( 'CREATE TABLE other ( x INTEGER )' );
        $this->assertSame( array( 'item' => eZDBInterface::RELATION_TABLE, 'other' => eZDBInterface::RELATION_TABLE ), $db->eZTableList() );
        $this->assertSame( array( 'item', 'other' ), $db->relationList() );
        $this->assertSame( 'pdo_sqlite', $db->databaseName() );
        $this->assertInstanceOf( PDO::class, $db->pdo() );
        $version = $db->databaseServerVersion();
        $this->assertNotSame( '', $version['string'] );
        $this->assertTrue( $db->removeRelation( 'other', eZDBInterface::RELATION_TABLE ) );
        $this->assertSame( array( 'item' ), $db->relationList() );
        $this->assertFalse( $db->removeRelation( 'x; DROP TABLE item', eZDBInterface::RELATION_TABLE ) );
        $db->close();
        $this->assertFalse( $db->isConnected() );
        $this->assertNull( $db->pdo() );
    }

    /** PD-08 */
    public function testFailedStatements()
    {
        $db = $this->filled();
        $this->assertFalse( $db->query( 'INSERT INTO missing_table ( x ) VALUES ( 1 )' ) );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM missing_table' ) );
        $this->assertStringContainsString( 'missing_table', (string)$db->errorMessage() );

        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $db->arrayQuery( 'SELECT * FROM missing_table' );
            $this->fail( 'an exception with the exceptions error handling' );
        }
        catch ( eZDBException $e )
        {
            $this->assertStringContainsString( 'missing_table', $e->getMessage() );
        }

        $db->begin();
        $db->query( "INSERT INTO item ( name ) VALUES ( 'in a failed transaction' )" );
        try
        {
            $db->query( 'INSERT INTO missing_table ( x ) VALUES ( 1 )' );
            $this->fail( 'an exception inside the transaction' );
        }
        catch ( eZDBException $e )
        {
            $this->assertTrue( true );
        }
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertSame( array(), $db->arrayQuery( "SELECT id FROM item WHERE name = 'in a failed transaction'" ), 'rolled back' );
    }

    /** PD-09 */
    public function testExpressionsPerDialect()
    {
        $db = $this->db( array( 'pdo_dialect' => 'sqlserver' ) );
        $this->assertSame( ' ( a & b ) ', $db->bitAnd( 'a', 'b' ) );
        $this->assertSame( ' ( a | b ) ', $db->bitOr( 'a', 'b' ) );
        $this->assertSame( ' ( a + b ) ', $db->concatString( array( 'a', 'b' ) ) );
        $this->assertSame( " LOWER( CONVERT( VARCHAR(32), HASHBYTES( 'MD5', a ), 2 ) ) ", $db->md5( 'a' ) );
        $this->assertSame( ' SUBSTRING( a, 2, 3 ) ', $db->subString( 'a', 2, 3 ) );

        $db = $this->db( array( 'pdo_dialect' => 'oracle' ) );
        $this->assertSame( ' BITAND( a, b ) ', $db->bitAnd( 'a', 'b' ) );
        $this->assertSame( ' ( a + b - BITAND( a, b ) ) ', $db->bitOr( 'a', 'b' ) );
        $this->assertSame( ' ( a || b ) ', $db->concatString( array( 'a', 'b' ) ) );
        $this->assertSame( " LOWER( STANDARD_HASH( a, 'MD5' ) ) ", $db->md5( 'a' ) );
        $this->assertSame( ' SUBSTR( a, 2 ) ', $db->subString( 'a', 2 ) );

        $db = $this->filled();
        $this->assertSame( array( 'ab' ), $db->arrayQuery( 'SELECT ' . $db->concatString( array( "'a'", "'b'" ) ) . ' AS v', array( 'column' => 'v' ) ) );
        $this->assertSame( array( 2 ), array_map( 'intval', $db->arrayQuery( 'SELECT ' . $db->bitAnd( 6, 3 ) . ' AS v', array( 'column' => 'v' ) ) ) );
    }
}
