<?php
/**
 * expODBCDB against real databases through their ODBC drivers (skipped without one): every target of
 * expODBCTestKit::targets() (EXP_TEST_ODBC_SQLSERVER, _POSTGRESQL, _MYSQL, _SQLITE with _USER, _PASSWORD, _DRIVER),
 * on every layer (pdo_odbc, odbc). Every test makes its own tables through the data table layer and drops them.
 *
 *  OL-01 - Connect: databaseName() is the engine's, the server's version, the layer, eZTableList()
 *  OL-02 - Insert and lastSerialID(): consecutive ids, with and without the table and column
 *  OL-03 - Paging: every limit/offset equals the slice of the ordered rows; limit 0 sends nothing; past the end
 *  OL-04 - Transactions: rollback leaves nothing, commit keeps, nested begin/commit
 *  OL-05 - Unicode: umlauts, emoji, CJK, combining marks round-trip and match in a WHERE
 *  OL-06 - Long text: 1 MB round-trips
 *  OL-07 - Escaping: quotes, backslashes, NUL dropped, '' is not NULL
 *  OL-08 - Helpers: sqlUpsert (insert, then update), sqlGroupConcat, unix time stamps there and back, sqlLimit,
 *          sqlQuoteIdentifier of a keyword
 *  OL-09 - Errors: a duplicate key returns false (standard) or throws (exceptions)
 *  OL-10 - Data table connection: expDataConnections opens engine odbc with the dialect in the options, test()
 *          answers, expDataTableSchema makes a table on it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expODBCTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('odbc')]
#[\PHPUnit\Framework\Attributes\Group('odbc-live')]
class expODBCLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var expODBCDB|null */
    private $db;

    /** @var string[] */
    private $tables = array();

    private $saved;

    public static function targets()
    {
        $targets = expODBCTestKit::targets();
        if ( !$targets )
            return array( 'none' => array( null ) );
        $data = array();
        foreach ( $targets as $name => $target )
            $data[$name] = array( $target );
        return $data;
    }

    protected function setUp(): void
    {
        $this->saved = expODBCTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        if ( $this->db )
        {
            $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
            while ( $this->db->isConnected() && $this->db->transactionCounter() > 0 )
                $this->db->rollback();
            foreach ( array_reverse( $this->tables ) as $table )
                $this->db->query( 'DROP TABLE ' . $this->db->sqlQuoteIdentifier( $table ) );
            $this->db->close();
        }
        $this->db = null;
        $this->tables = array();
        if ( class_exists( 'expKernelTableSchema' ) )
            expKernelTableSchema::reset();
        expODBCTestKit::restoreDebug( $this->saved );
    }

    /**
     * Connects to a target and makes a table: id (auto_increment), name varchar(200), body longtext, n int.
     *
     * @param array|null $target
     * @return string the table
     */
    private function open( $target, $table = 'exp_odbc_t' )
    {
        if ( $target === null )
            $this->markTestSkipped( 'No ODBC target: set EXP_TEST_ODBC_SQLSERVER, _POSTGRESQL, _MYSQL or _SQLITE' );
        try
        {
            $this->db = expODBCTestKit::connect( $target );
        }
        catch ( Throwable $e )
        {
            $this->fail( 'Connection failed: ' . $e->getMessage() );
        }
        $table .= '_' . substr( md5( uniqid( '', true ) ), 0, 6 );
        $schema = array( $table => array( 'name' => $table,
            'fields' => array( 'id' => array( 'type' => 'auto_increment', 'default' => false ),
                               'name' => array( 'length' => 200, 'type' => 'varchar', 'not_null' => '0', 'default' => null ),
                               'body' => array( 'type' => 'longtext', 'not_null' => '0', 'default' => null ),
                               'n' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ) ) );
        $result = expDataTableSchema::apply( $schema, $this->db );
        $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        $this->tables[] = $table;
        return $table;
    }

    private function insert( $table, $name, $n = 0, $body = null )
    {
        $db = $this->db;
        $this->assertTrue( $db->query( 'INSERT INTO ' . $db->sqlQuoteIdentifier( $table ) . ' ( name, body, n ) VALUES ( '
                                       . $db->sqlLiteral( $name ) . ', ' . $db->sqlLiteral( $body ) . ', ' . (int)$n . ' )' ) );
        return $db->lastSerialID( $table, 'id' );
    }

    /** OL-01 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testConnect( $target )
    {
        $table = $this->open( $target );
        $expected = array( 'sqlserver' => 'SQLServer', 'postgresql' => 'postgresql', 'mysql' => 'mysql', 'sqlite' => 'sqlite' );
        $this->assertSame( $expected[$target[0]], $this->db->databaseName() );
        $this->assertSame( $target[1], $this->db->layer() );
        $version = $this->db->databaseServerVersion();
        $this->assertNotSame( array(), $version['values'], 'no version' );
        $this->assertContains( $table, array_keys( $this->db->eZTableList() ) );
    }

    /** OL-02 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testLastSerialID( $target )
    {
        $table = $this->open( $target );
        $first = $this->insert( $table, 'a' );
        $this->assertIsInt( $first );
        $this->assertSame( $first + 1, $this->insert( $table, 'b' ) );
        $this->db->query( 'INSERT INTO ' . $this->db->sqlQuoteIdentifier( $table ) . " ( name, n ) VALUES ( 'c', 3 )" );
        $this->assertSame( $first + 2, $this->db->lastSerialID() );
        $rows = $this->db->arrayQuery( 'SELECT id, name FROM ' . $this->db->sqlQuoteIdentifier( $table ) . ' ORDER BY id' );
        $this->assertSame( array( 'a', 'b', 'c' ), array_column( $rows, 'name' ) );
        $this->assertEquals( $first + 2, $rows[2]['id'] );
    }

    /** OL-03 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testPaging( $target )
    {
        $table = $this->open( $target );
        $this->db->begin();
        for ( $i = 1; $i <= 23; $i++ )
            $this->insert( $table, sprintf( 'row %02d', $i ), $i );
        $this->db->commit();
        $sql = 'SELECT n FROM ' . $this->db->sqlQuoteIdentifier( $table ) . ' ORDER BY n';
        $all = array_map( 'intval', $this->db->arrayQuery( $sql, array( 'column' => 'n' ) ) );
        $this->assertSame( range( 1, 23 ), $all );
        foreach ( array( array( 5, 0 ), array( 5, 5 ), array( 10, 20 ), array( 1, 22 ), array( 7, 30 ), array( false, 21 ) ) as $page )
        {
            list( $limit, $offset ) = $page;
            $rows = $this->db->arrayQuery( $sql, array( 'limit' => $limit, 'offset' => $offset, 'column' => 'n' ) );
            $this->assertSame( array_slice( $all, $offset, $limit === false ? null : $limit ), array_map( 'intval', array_values( $rows ) ), "limit $limit offset $offset" );
            if ( $rows )
                $this->assertSame( $offset, array_key_first( $rows ) );
        }
        $this->assertSame( array(), $this->db->arrayQuery( $sql, array( 'limit' => 0 ) ) );
        $limited = $this->db->arrayQuery( $this->db->sqlLimit( $sql, 4, 2 ) );
        $this->assertSame( array( 3, 4, 5, 6 ), array_map( 'intval', array_column( $limited, 'n' ) ) );
    }

    /** OL-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testTransactions( $target )
    {
        $table = $this->open( $target );
        $count = 'SELECT COUNT(*) AS c FROM ' . $this->db->sqlQuoteIdentifier( $table );
        $this->db->begin();
        $this->insert( $table, 'gone' );
        $this->db->rollback();
        $this->assertEquals( 0, $this->db->arrayQuery( $count )[0]['c'] );
        $this->db->begin();
        $this->insert( $table, 'kept' );
        $this->db->begin();
        $this->insert( $table, 'nested' );
        $this->db->commit();
        $this->db->commit();
        $this->assertEquals( 2, $this->db->arrayQuery( $count )[0]['c'] );
    }

    /** OL-05 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testUnicode( $target )
    {
        $table = $this->open( $target );
        $texts = array( 'Grüße, Ærø', 'emoji 😀🚀', '日本語のテキスト', "e\u{0301} combining", 'Ω≈ç√∫' );
        foreach ( $texts as $i => $text )
            $this->insert( $table, $text, $i );
        $read = $this->db->arrayQuery( 'SELECT name FROM ' . $this->db->sqlQuoteIdentifier( $table ) . ' ORDER BY n', array( 'column' => 'name' ) );
        $this->assertSame( $texts, array_values( $read ) );
        $found = $this->db->arrayQuery( 'SELECT n FROM ' . $this->db->sqlQuoteIdentifier( $table ) . ' WHERE name = ' . $this->db->sqlLiteral( 'emoji 😀🚀' ) );
        $this->assertCount( 1, $found );
        $this->assertEquals( 1, $found[0]['n'] );
    }

    /** OL-06 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testLongText( $target )
    {
        $table = $this->open( $target );
        $body = str_repeat( "Line ä 😀 0123456789\n", 50000 );
        $this->insert( $table, 'long', 1, $body );
        if ( $target[0] === 'sqlite' )
        {
            // sqliteodbc describes a TEXT column as 255 characters: PDO_ODBC refuses the longer value, the odbc
            // extension cuts it (use the native SQLite driver for long text)
            if ( $target[1] === 'pdo_odbc' )
            {
                $this->expectException( eZDBException::class );
                $this->expectExceptionMessage( 'truncated' );
            }
            $read = $this->db->arrayQuery( 'SELECT body FROM ' . $this->db->sqlQuoteIdentifier( $table ), array( 'column' => 'body' ) );
            $this->assertLessThan( strlen( $body ), strlen( (string)$read[0] ) );
            return;
        }
        $read = $this->db->arrayQuery( 'SELECT body FROM ' . $this->db->sqlQuoteIdentifier( $table ), array( 'column' => 'body' ) );
        $this->assertSame( strlen( $body ), strlen( (string)$read[0] ) );
        $this->assertSame( md5( $body ), md5( (string)$read[0] ) );
    }

    /** OL-07 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testEscaping( $target )
    {
        $table = $this->open( $target );
        $this->insert( $table, "O'Neil \\ path\\ 'q' -- ; /*", 1 );
        $this->insert( $table, "nul\0dropped", 2 );
        $this->insert( $table, '', 3 );
        $this->db->query( 'INSERT INTO ' . $this->db->sqlQuoteIdentifier( $table ) . ' ( name, n ) VALUES ( NULL, 4 )' );
        $rows = $this->db->arrayQuery( 'SELECT name FROM ' . $this->db->sqlQuoteIdentifier( $table ) . ' ORDER BY n', array( 'column' => 'name' ) );
        $this->assertSame( array( "O'Neil \\ path\\ 'q' -- ; /*", 'nuldropped', '', null ), array_values( $rows ) );
    }

    /** OL-08 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testHelpers( $target )
    {
        $table = $this->open( $target );
        $db = $this->db;
        $q = $db->sqlQuoteIdentifier( $table );
        $id = $this->insert( $table, 'one', 1 );
        $this->insert( $table, 'two', 2 );
        // SQL Server compiles MERGE's INSERT branch with the IDENTITY key: it needs IDENTITY_INSERT (expSQLServerDB does that itself)
        if ( $target[0] === 'sqlserver' )
            $db->query( "SET IDENTITY_INSERT $q ON" );
        $this->assertTrue( $db->query( $db->sqlUpsert( $table, array( 'id' => $id, 'name' => 'one, changed', 'n' => 1 ), array( 'id' ) ) ) );
        if ( $target[0] === 'sqlserver' )
            $db->query( "SET IDENTITY_INSERT $q OFF" );
        $this->assertSame( 'one, changed', $db->arrayQuery( "SELECT name FROM $q WHERE id = $id", array( 'column' => 'name' ) )[0] );
        $joined = $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|', 'name' ) . " AS j FROM $q", array( 'column' => 'j' ) );
        $parts = explode( '|', (string)$joined[0] );
        sort( $parts );
        $this->assertSame( array( 'one, changed', 'two' ), $parts );
        $now = $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp() . ' AS t' . ( $target[0] === 'mysql' || $target[0] === 'sqlite' || $target[0] === 'postgresql' || $target[0] === 'sqlserver' ? '' : ' FROM dual' ), array( 'column' => 't' ) );
        $this->assertEqualsWithDelta( time(), (int)$now[0], 120 );
        // MariaDB before 11.5 and MySQL keep FROM_UNIXTIME() and UNIX_TIMESTAMP() before 2038
        $stamp = $target[0] === 'mysql' ? 2000000000 : 2208988800;
        $back = $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp( $db->sqlFromUnixTimestamp( (string)$stamp ) ) . ' AS t', array( 'column' => 't' ) );
        $this->assertSame( $stamp, (int)$back[0], 'there and back' );
        $keyword = $db->arrayQuery( 'SELECT n AS ' . $db->sqlQuoteIdentifier( 'order' ) . " FROM $q WHERE id = $id" );
        $this->assertEquals( 1, $keyword[0]['order'] );
    }

    /** OL-09 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testDuplicateKey( $target )
    {
        $table = $this->open( $target );
        $id = $this->insert( $table, 'x' );
        $sql = 'INSERT INTO ' . $this->db->sqlQuoteIdentifier( $table ) . " ( id, name, n ) VALUES ( $id, 'dup', 0 )";
        if ( $target[0] === 'sqlserver' )
            $this->db->query( 'SET IDENTITY_INSERT ' . $this->db->sqlQuoteIdentifier( $table ) . ' ON' );
        try
        {
            $this->db->query( $sql );
            $this->fail( 'a duplicate key was written' );
        }
        catch ( eZDBException $e )
        {
            $this->assertNotSame( '', $e->getMessage() );
        }
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertFalse( $this->db->query( $sql ) );
        $this->assertNotSame( '', (string)$this->db->errorMessage() );
    }

    /** OL-10 */
    #[\PHPUnit\Framework\Attributes\DataProvider('targets')]
    public function testDataTableConnection( $target )
    {
        if ( $target === null )
            $this->markTestSkipped( 'No ODBC target' );
        list( $dialect, $layer, $keywords, $user, $password, $driver ) = $target;
        $descriptor = array( 'identifier' => 'odbc_live', 'engine' => 'odbc', 'user' => $user,
                             'options' => array( 'dialect' => $dialect, 'odbc_driver' => $driver, 'connection_string' => $keywords, 'layer' => $layer ) );
        $test = expDataConnections::test( $descriptor, $password );
        $this->assertTrue( $test['ok'], $test['error'] );
        $this->assertSame( 'odbc', $test['via'] );
        $this->db = expDataConnections::open( $descriptor, $password );
        $this->assertInstanceOf( 'expODBCDB', $this->db );
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $table = 'exp_odbc_dt_' . substr( md5( uniqid( '', true ) ), 0, 6 );
        $schema = expDataTableSchema::fromDefinition( array( 'name' => $table, 'increment_key' => 'id',
            'fields' => array( 'id' => array( 'datatype' => 'integer' ), 'title' => array( 'datatype' => 'string' ), 'score' => array( 'datatype' => 'float' ) ),
            'keys' => array( 'id' ) ) );
        $result = expDataTableSchema::apply( $schema, $this->db );
        $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        $this->tables[] = $table;
        $this->db->query( 'INSERT INTO ' . $this->db->sqlQuoteIdentifier( $table ) . " ( title, score ) VALUES ( 'Täst', 1.5 )" );
        $rows = $this->db->arrayQuery( 'SELECT title, score FROM ' . $this->db->sqlQuoteIdentifier( $table ) );
        $this->assertSame( 'Täst', $rows[0]['title'] );
        $this->assertEqualsWithDelta( 1.5, (float)$rows[0]['score'], 0.0001 );
        $again = expDataTableSchema::plan( $schema, $this->db );
        $this->assertSame( 'none', $again['tables'][$table]['action'], json_encode( $again['tables'][$table] ) );
    }
}
?>
