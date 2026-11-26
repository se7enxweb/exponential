<?php
/**
 * expMSAccessDB against a real Access file through Microsoft's Access Database Engine ODBC driver (Windows; skipped
 * without EXP_TEST_MSACCESS_FILE, a writable .accdb or .mdb). Each test makes its own tables and drops them. What
 * Access cannot do is asserted as failing, so a later driver that can is noticed.
 *
 *  AL-01 - Connect: databaseName() MSAccess, the layer, SQLTables lists a new table, tableColumns() describes it
 *  AL-02 - DDL from .dba: COUNTER, LONG, TEXT(n), LONGTEXT, DOUBLE, DECIMAL, DATETIME, DEFAULT (ANSI-92), indexes
 *  AL-03 - Insert and @@IDENTITY: consecutive ids
 *  AL-04 - Paging: TOP with the offset dropped in PHP equals the slice; TOP keeps ties, arrayQuery() does not
 *  AL-05 - Functions: & concat, IIf, Mid, Len, #date# literals, unix time stamps there and back, BAND/BOR in
 *          ANSI-92 and the constant-mask emulation
 *  AL-06 - Transactions: rollback leaves nothing, commit keeps
 *  AL-07 - Text: Unicode (emoji, CJK) round-trips; 1 MB long text; quotes and backslashes
 *  AL-08 - Scripts: two statements in one query() run one after the other; comments are removed
 *  AL-09 - What Access refuses: LIMIT, OFFSET ... FETCH, FOR UPDATE, MERGE, upsert and GROUP_CONCAT helpers, MD5,
 *          a second writer on a row another connection's transaction holds (lock), a TEXT longer than 255
 *  AL-10 - Data table connection: engine msaccess opens the file, expDataTableSchema makes a table on it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expODBCTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('odbc')]
#[\PHPUnit\Framework\Attributes\Group('msaccess-live')]
class expMSAccessLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var expMSAccessDB|null */
    private $db;

    /** @var expMSAccessDB[] */
    private $others = array();

    /** @var string[] */
    private $tables = array();

    private $saved;

    protected function setUp(): void
    {
        $this->saved = expODBCTestKit::quietDebug();
        $file = (string)getenv( 'EXP_TEST_MSACCESS_FILE' );
        if ( $file === '' || !is_file( $file ) )
            $this->markTestSkipped( 'No Access file: set EXP_TEST_MSACCESS_FILE (Windows with the Access Database Engine)' );
        $this->db = self::connect();
    }

    protected function tearDown(): void
    {
        foreach ( $this->others as $other )
            $other->close();
        if ( $this->db )
        {
            $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
            while ( $this->db->isConnected() && $this->db->transactionCounter() > 0 )
                $this->db->rollback();
            foreach ( array_reverse( $this->tables ) as $table )
                $this->db->query( 'DROP TABLE ' . expMSAccessDialect::quoteName( $table ) );
            $this->db->close();
        }
        $this->db = null;
        $this->others = array();
        $this->tables = array();
        if ( class_exists( 'expKernelTableSchema' ) )
            expKernelTableSchema::reset();
        expODBCTestKit::restoreDebug( $this->saved );
    }

    /**
     * @param array $settings [MSAccessSettings]
     * @return expMSAccessDB
     */
    private static function connect( array $settings = array() )
    {
        $driver = (string)getenv( 'EXP_TEST_MSACCESS_DRIVER' );
        if ( $driver !== '' )
            $settings += array( 'Driver' => $driver );
        if ( getenv( 'EXP_TEST_ODBC_LAYER' ) && getenv( 'EXP_TEST_ODBC_LAYER' ) !== 'both' )
            $settings += array( 'Layer' => getenv( 'EXP_TEST_ODBC_LAYER' ) );
        $db = new expMSAccessDB( array( 'database' => getenv( 'EXP_TEST_MSACCESS_FILE' ), 'connect_retries' => 0, 'msaccess' => $settings ) );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        return $db;
    }

    private function table( $prefix = 'exp_acc' )
    {
        $table = $prefix . '_' . substr( md5( uniqid( '', true ) ), 0, 6 );
        $definition = array( 'fields' => array(
                                 'id' => array( 'type' => 'auto_increment' ),
                                 'name' => array( 'type' => 'varchar', 'length' => 200 ),
                                 'body' => array( 'type' => 'longtext' ),
                                 'n' => array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                                 'price' => array( 'type' => 'decimal', 'length' => 12 ),
                                 'ratio' => array( 'type' => 'double' ),
                                 'big' => array( 'type' => 'bigint' ),
                                 'created' => array( 'type' => 'datetime' ) ),
                             'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                                 'n' => array( 'type' => 'non-unique', 'fields' => array( 'n' ) ),
                                                 'name' => array( 'type' => 'unique', 'fields' => array( 'name' ) ) ) );
        foreach ( expMSAccessDialect::createTable( $table, $definition, $this->db->ansi92() ) as $sql )
            $this->assertTrue( $this->db->query( $sql ), $sql );
        $this->tables[] = $table;
        return $table;
    }

    private function insert( $table, $name, $n = 0, $body = null )
    {
        $db = $this->db;
        $this->assertTrue( $db->query( 'INSERT INTO ' . expMSAccessDialect::quoteName( $table ) . ' ( [name], [body], [n] ) VALUES ( '
                                       . $db->sqlLiteral( $name ) . ', ' . $db->sqlLiteral( $body ) . ', ' . (int)$n . ' )' ) );
        return $db->lastSerialID( $table, 'id' );
    }

    /** AL-01 */
    public function testConnect()
    {
        $this->assertSame( 'MSAccess', $this->db->databaseName() );
        $this->assertSame( 'msaccess', $this->db->sqlHelperDialect() );
        $this->assertContains( $this->db->layer(), array( 'odbc', 'pdo_odbc' ) );
        $table = $this->table();
        $this->assertArrayHasKey( $table, $this->db->eZTableList() );
        if ( $this->db->layer() === 'odbc' )
        {
            $columns = $this->db->tableColumns( $table );
            $this->assertSame( array( 'id', 'name', 'body', 'n', 'price', 'ratio', 'big', 'created' ), array_keys( $columns ) );
            $this->assertSame( 'COUNTER', $columns['id']['type'] );
            $this->assertSame( 200, $columns['name']['size'] );
        }
    }

    /** AL-02, AL-03 */
    public function testInsertAndIdentity()
    {
        $table = $this->table();
        $first = $this->insert( $table, 'a' );
        $this->assertIsInt( $first );
        $this->assertSame( $first + 1, $this->insert( $table, 'b' ) );
        $q = expMSAccessDialect::quoteName( $table );
        $this->db->query( "UPDATE $q SET [price] = 1234567.89, [ratio] = 0.125, [big] = 9007199254740993, [created] = #2026-11-25 10:11:12# WHERE [id] = $first" );
        $row = $this->db->arrayQuery( "SELECT [n], [price], [ratio], [big], [created] FROM $q WHERE [id] = $first" )[0];
        $this->assertEquals( 0, $row['n'], 'DEFAULT 0 in ANSI-92 mode' );
        $this->assertEqualsWithDelta( 1234567.89, (float)$row['price'], 0.001 );
        $this->assertEqualsWithDelta( 0.125, (float)$row['ratio'], 0.0000001 );
        $this->assertSame( '9007199254740993', (string)$row['big'], 'DECIMAL(19,0) keeps 2^53 + 1' );
        $this->assertStringStartsWith( '2026-11-25 10:11:12', (string)$row['created'] );
    }

    /** AL-04 */
    public function testPaging()
    {
        $table = $this->table();
        $this->db->begin();
        for ( $i = 1; $i <= 23; $i++ )
            $this->insert( $table, sprintf( 'row %02d', $i ), (int)ceil( $i / 2 ) );
        $this->db->commit();
        $sql = 'SELECT [name] FROM ' . expMSAccessDialect::quoteName( $table ) . ' ORDER BY [name]';
        $all = array_values( $this->db->arrayQuery( $sql, array( 'column' => 'name' ) ) );
        $this->assertCount( 23, $all );
        foreach ( array( array( 5, 0 ), array( 5, 5 ), array( 10, 20 ), array( 7, 30 ), array( false, 21 ) ) as $page )
        {
            list( $limit, $offset ) = $page;
            $rows = $this->db->arrayQuery( $sql, array( 'limit' => $limit, 'offset' => $offset, 'column' => 'name' ) );
            $this->assertSame( array_slice( $all, $offset, $limit === false ? null : $limit ), array_values( $rows ), "limit $limit offset $offset" );
        }
        // TOP 3 over n (1,1,2,2,...) returns the tie at the third row as well: 4 rows; arrayQuery() gives 3
        $ties = $this->db->arrayQuery( 'SELECT TOP 3 [n] FROM ' . expMSAccessDialect::quoteName( $table ) . ' ORDER BY [n]' );
        $this->assertCount( 4, $ties, 'Access TOP keeps ties' );
        $this->assertCount( 3, $this->db->arrayQuery( 'SELECT [n] FROM ' . expMSAccessDialect::quoteName( $table ) . ' ORDER BY [n]', array( 'limit' => 3 ) ) );
    }

    /** AL-05 */
    public function testFunctions()
    {
        $db = $this->db;
        $table = $this->table();
        $id = $this->insert( $table, 'Exponential', 6 );
        $q = expMSAccessDialect::quoteName( $table );
        $row = $db->arrayQuery( 'SELECT ' . $db->concatString( array( '[name]', "'-'", '[n]' ) ) . ' AS c, ' . expMSAccessDialect::iif( '[n] > 5', "'big'", "'small'" ) . ' AS i, '
                                . $db->subString( '[name]', 1, 3 ) . ' AS s, ' . expMSAccessDialect::charLength( '[name]' ) . " AS l FROM $q WHERE [id] = $id" )[0];
        $this->assertSame( 'Exponential-6', $row['c'] );
        $this->assertSame( 'big', $row['i'] );
        $this->assertSame( 'Exp', $row['s'] );
        $this->assertEquals( 11, $row['l'] );
        $now = $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp() . " AS t FROM $q WHERE [id] = $id", array( 'column' => 't' ) );
        // Now() is local time: within a day of UTC
        $this->assertEqualsWithDelta( time(), (int)$now[$id > 0 ? 0 : 0], 86400 );
        $back = $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp( $db->sqlFromUnixTimestamp( '2208988800' ) ) . " AS t FROM $q WHERE [id] = $id", array( 'column' => 't' ) );
        $this->assertSame( 2208988800, (int)$back[0] );
        foreach ( array( 6, 1, 255 ) as $mask )
        {
            $and = $db->arrayQuery( 'SELECT ' . $db->bitAnd( '[n]', (string)$mask ) . ' AS a, ' . expMSAccessDialect::bitAnd( '[n]', (string)$mask, false ) . " AS e FROM $q WHERE [id] = $id" )[0];
            $this->assertEquals( 6 & $mask, $and['a'], "BAND $mask" );
            $this->assertEquals( 6 & $mask, $and['e'], "emulated & $mask" );
        }
        $or = $db->arrayQuery( 'SELECT ' . $db->bitOr( '[n]', '1' ) . " AS o FROM $q WHERE [id] = $id", array( 'column' => 'o' ) );
        $this->assertEquals( 7, $or[0] );
        $dated = $db->arrayQuery( "SELECT COUNT(*) AS c FROM $q WHERE #2026-01-01# < Now()", array( 'column' => 'c' ) );
        $this->assertEquals( 1, $dated[0] );
    }

    /** AL-06 */
    public function testTransactions()
    {
        $table = $this->table();
        $count = 'SELECT COUNT(*) AS c FROM ' . expMSAccessDialect::quoteName( $table );
        $this->db->begin();
        $this->insert( $table, 'gone' );
        $this->db->rollback();
        $this->assertEquals( 0, $this->db->arrayQuery( $count )[0]['c'] );
        $this->db->begin();
        $this->insert( $table, 'kept' );
        $this->db->commit();
        $this->assertEquals( 1, $this->db->arrayQuery( $count )[0]['c'] );
    }

    /** AL-07 */
    public function testText()
    {
        $table = $this->table();
        $texts = array( 'Grüße', 'emoji 😀', '日本語', "O'Neil \\ path" );
        foreach ( $texts as $i => $text )
            $this->insert( $table, $text, $i );
        $read = $this->db->arrayQuery( 'SELECT [name] FROM ' . expMSAccessDialect::quoteName( $table ) . ' ORDER BY [n]', array( 'column' => 'name' ) );
        $this->assertSame( $texts, array_values( $read ) );
        $body = str_repeat( "Zeile ä 😀 0123456789\n", 50000 );
        $id = $this->insert( $table, 'long', 9, $body );
        $back = $this->db->arrayQuery( 'SELECT [body] FROM ' . expMSAccessDialect::quoteName( $table ) . " WHERE [id] = $id", array( 'column' => 'body' ) );
        $this->assertSame( md5( $body ), md5( (string)$back[0] ) );
    }

    /** AL-08 */
    public function testScripts()
    {
        $table = $this->table();
        $q = expMSAccessDialect::quoteName( $table );
        $this->assertTrue( $this->db->query( "INSERT INTO $q ( [name], [n] ) VALUES ( 'one;1', 1 ); -- first\nINSERT INTO $q ( [name], [n] ) /* second */ VALUES ( 'two', 2 );" ) );
        $this->assertSame( array( 'one;1', 'two' ), array_values( $this->db->arrayQuery( "SELECT [name] FROM $q ORDER BY [n]", array( 'column' => 'name' ) ) ) );
    }

    /**
     * @param callable $work
     * @return string the failure's message, or '' when it worked
     */
    private static function failure( callable $work )
    {
        try
        {
            $work();
            return '';
        }
        catch ( Throwable $e )
        {
            return get_class( $e ) . ': ' . $e->getMessage();
        }
    }

    /** AL-09 */
    public function testWhatAccessRefuses()
    {
        $db = $this->db;
        $table = $this->table();
        $id = $this->insert( $table, 'x', 1 );
        $q = expMSAccessDialect::quoteName( $table );
        $refused = array(
            'LIMIT' => function () use ( $db, $q ) { $db->arrayQuery( "SELECT * FROM $q LIMIT 1" ); },
            'OFFSET FETCH' => function () use ( $db, $q ) { $db->arrayQuery( "SELECT * FROM $q ORDER BY [id] OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY" ); },
            'FOR UPDATE' => function () use ( $db, $q ) { $db->arrayQuery( "SELECT * FROM $q FOR UPDATE" ); },
            'MERGE' => function () use ( $db, $q ) { $db->query( "MERGE INTO $q t USING $q s ON (t.[id] = s.[id]) WHEN MATCHED THEN UPDATE SET t.[n] = 2" ); },
            'upsert helper' => function () use ( $db, $table, $id ) { $db->sqlUpsert( $table, array( 'id' => $id, 'n' => 2 ), array( 'id' ) ); },
            'GROUP_CONCAT helper' => function () use ( $db ) { $db->sqlGroupConcat( '[name]' ); },
            'MD5' => function () use ( $db ) { $db->md5( '[name]' ); },
            'TEXT(300)' => function () use ( $db ) { $db->query( 'CREATE TABLE [exp_acc_text300] ( [t] TEXT(300) )' ); },
        );
        $results = array();
        foreach ( $refused as $what => $work )
            $results[$what] = self::failure( $work );
        foreach ( $results as $what => $failure )
            $this->assertNotSame( '', $failure, "$what worked on Access" );
        fwrite( STDERR, "\nAccess refuses:\n" . implode( "\n", array_map( function ( $k, $v ) { return "  $k: " . substr( $v, 0, 160 ); }, array_keys( $results ), $results ) ) . "\n" );

        // a second connection may not change a row the first one's transaction changed
        $other = self::connect( array( 'ConnectionString' => 'PageTimeout=5' ) );
        $this->others[] = $other;
        $db->begin();
        $db->query( "UPDATE $q SET [n] = 5 WHERE [id] = $id" );
        $locked = self::failure( function () use ( $other, $q, $id ) { $other->query( "UPDATE $q SET [n] = 6 WHERE [id] = $id" ); } );
        $db->commit();
        fwrite( STDERR, '  concurrent writer: ' . ( $locked === '' ? 'not blocked' : substr( $locked, 0, 200 ) ) . "\n" );
        $this->assertNotSame( '', $locked, 'a second writer changed a locked row' );
    }

    /** AL-10 */
    public function testDataTableConnection()
    {
        $file = (string)getenv( 'EXP_TEST_MSACCESS_FILE' );
        $descriptor = array( 'identifier' => 'access_live', 'engine' => 'msaccess', 'database' => $file,
                             'options' => getenv( 'EXP_TEST_MSACCESS_DRIVER' ) ? array( 'odbc_driver' => getenv( 'EXP_TEST_MSACCESS_DRIVER' ) ) : array() );
        $plan = expDataConnections::plan( $descriptor );
        if ( $plan['error'] !== '' )
            $this->markTestSkipped( 'The Access file is not in a SQLiteDirectories directory of datatable.ini: ' . $plan['error'] );
        $db = expDataConnections::open( $descriptor );
        $this->assertInstanceOf( 'expMSAccessDB', $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $table = 'exp_acc_dt_' . substr( md5( uniqid( '', true ) ), 0, 6 );
        $schema = expDataTableSchema::fromDefinition( array( 'name' => $table, 'increment_key' => 'id',
            'fields' => array( 'id' => array( 'datatype' => 'integer' ), 'title' => array( 'datatype' => 'string' ), 'score' => array( 'datatype' => 'float' ) ),
            'keys' => array( 'id' ) ) );
        $result = expDataTableSchema::apply( $schema, $db );
        $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        $this->tables[] = $table;
        $db->query( 'INSERT INTO ' . $db->sqlQuoteIdentifier( $table ) . " ( [title], [score] ) VALUES ( 'Täst', 1.5 )" );
        $rows = $db->arrayQuery( 'SELECT [title], [score] FROM ' . $db->sqlQuoteIdentifier( $table ) );
        $this->assertSame( 'Täst', $rows[0]['title'] );
        $db->close();
    }
}
?>
