<?php
/**
 * The libSQL driver against a libSQL server (sqld, the libsql-server container). Skipped without EXP_TEST_LIBSQL_URL
 * (http://127.0.0.1:8080); EXP_TEST_LIBSQL_TOKEN when the server wants one. The tests empty the server's database.
 *
 *  LL-01 - Connect: the server's SQLite and its own version, Hrana 3, databaseName() sqlite
 *  LL-02 - The kernel's SQLite schema through eZSQLiteSchema: share/db_schema.dba and db_data.dba inserted, read back
 *  LL-03 - Values: integers to 64 bits, floats, UTF-8 text, binary, NULL, a quote, a long text
 *  LL-04 - Transactions: commit, rollback, a failed statement inside one, nested eZDB transactions
 *  LL-05 - Serial ids, a two-column key, errors (UNIQUE 19, no such table), exceptions mode
 *  LL-06 - What a local file has in PHP: MOD() and md5() of the server, BIT_OR() and BIT_AND() rewritten
 *  LL-07 - The portable helpers run: LIMIT/OFFSET, ON CONFLICT upsert, GROUP_CONCAT, UNIX timestamps
 *  LL-08 - Temporary tables as tables of their own; a second connection writing while the first holds a transaction
 *  LL-09 - A stream the server let expire (idle > 10 s) is replaced; a token the server refuses
 *  LL-10 - A data table connection (engine libsql): plan(), test(), open(), a table made, planned again: nothing
 *  LL-11 - Without curl: the same statements over PHP's HTTP stream wrapper (allow_url_fopen), errors and an expired stream
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/expLibSQLTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('libsql')]
#[\PHPUnit\Framework\Attributes\Group('libsql-live')]
class expLibSQLLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;
    private $open = array();

    protected function setUp(): void
    {
        if ( expLibSQLTestKit::liveUrl() === null )
            $this->markTestSkipped( 'No libSQL server to test against: set EXP_TEST_LIBSQL_URL (e.g. http://127.0.0.1:8080)' );
        $this->saved = expLibSQLTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        foreach ( $this->open as $db )
            $db->close();
        $this->open = array();
        if ( $this->saved !== null )
            expLibSQLTestKit::restoreDebug( $this->saved );
    }

    /** A driver on the live server, emptied */
    private function db( array $libsql = array(), $empty = true )
    {
        $db = expLibSQLTestKit::liveDriver( $libsql );
        $this->open[] = $db;
        if ( $empty )
            $db->removeDatabase( 'exponential' );
        return $db;
    }

    private static function root()
    {
        return dirname( __DIR__, 6 );
    }

    /** LL-01 */
    public function testConnect()
    {
        $db = $this->db( array(), false );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( 'sqlite', $db->databaseName() );
        $this->assertMatchesRegularExpression( '/^3\.\d+\.\d+$/', $db->databaseServerVersion()['string'] );
        $this->assertTrue( version_compare( $db->databaseServerVersion()['string'], '3.35', '>=' ) );
        $this->assertStringStartsWith( 'sqld', $db->serverProduct() );
        $this->assertSame( 'Hrana 3 over HTTP', $db->databaseClientVersion()['string'] );
    }

    /** LL-02 */
    public function testKernelSchema()
    {
        $db = $this->db();
        $both = array_merge( eZDbSchema::read( self::root() . '/share/db_schema.dba', true ),
                             eZDbSchema::read( self::root() . '/share/db_data.dba', true ) );
        $handler = new eZSQLiteSchema( $both + array( 'instance' => $db, 'type' => 'sqlite' ) );
        $started = microtime( true );
        $this->assertTrue( $handler->insertSchema( array( 'schema' => true, 'data' => true ) ), (string)$db->errorMessage() );
        fwrite( STDERR, sprintf( "\nLL-02: kernel schema and data in %.1f s, %d statements\n", microtime( true ) - $started, $db->transportStatistics()['statements'] ) );
        $this->assertGreaterThan( 0, (int)$db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject_tree' )[0]['c'] );
        $schema = eZDbSchema::read( self::root() . '/share/db_schema.dba' );
        $back = ( new eZSQLiteSchema( array( 'instance' => $db ) ) )->schema( array( 'format' => 'generic' ) );
        foreach ( $schema as $table => $definition )
        {
            if ( $table === '_info' )
                continue;
            $this->assertArrayHasKey( $table, $back, "table $table" );
            $expected = array_keys( $definition['fields'] );
            $actual = array_keys( $back[$table]['fields'] );
            sort( $expected );
            sort( $actual );
            $this->assertSame( $expected, $actual, "columns of $table" );
        }
        $this->assertSame( count( array_diff( array_keys( $schema ), array( '_info' ) ) ),
                           count( array_intersect( array_keys( $schema ), (array)expKernelTableSchema::tables( $db ) ) ) );
    }

    /** LL-03 */
    public function testValues()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE ezv ( id INTEGER PRIMARY KEY, i INTEGER, f REAL, t TEXT, b BLOB )' );
        $long = str_repeat( 'äöü ', 50000 );
        $db->query( "INSERT INTO ezv ( i, f, t, b ) VALUES ( 9223372036854775807, 1.25, '" . $db->escapeString( "it's ✓ 漢字" ) . "', x'00ff10' )" );
        $db->query( "INSERT INTO ezv ( i, f, t, b ) VALUES ( -9223372036854775808, NULL, '" . $db->escapeString( $long ) . "', NULL )" );
        $rows = $db->arrayQuery( 'SELECT i, f, t, b FROM ezv ORDER BY id' );
        $this->assertSame( PHP_INT_MAX, $rows[0]['i'] );
        $this->assertSame( 1.25, $rows[0]['f'] );
        $this->assertSame( "it's ✓ 漢字", $rows[0]['t'] );
        $this->assertSame( "\x00\xff\x10", $rows[0]['b'] );
        $this->assertSame( PHP_INT_MIN, $rows[1]['i'] );
        $this->assertNull( $rows[1]['f'] );
        $this->assertSame( $long, $rows[1]['t'] );
        $this->assertNull( $rows[1]['b'] );
    }

    /** LL-04 */
    public function testTransactions()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE ezt ( id INTEGER PRIMARY KEY, name TEXT UNIQUE )' );
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'a' )" );
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'b' )" );
        $db->commit();
        $db->commit();
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'c' )" );
        $db->rollback();
        $this->assertSame( array( 'a', 'b' ), $db->arrayQuery( 'SELECT name FROM ezt ORDER BY id', array( 'column' => 'name' ) ) );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->begin();
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'd' )" );
        try
        {
            $db->query( "INSERT INTO ezt ( name ) VALUES ( 'a' )" );
            $this->fail( 'no exception' );
        }
        catch ( eZDBException $e )
        {
            $this->assertSame( 19, $e->getCode() );
        }
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertSame( array( 'a', 'b' ), $db->arrayQuery( 'SELECT name FROM ezt ORDER BY id', array( 'column' => 'name' ) ) );
        // 200 statements in one transaction, one stream
        $started = microtime( true );
        $db->begin();
        for ( $i = 0; $i < 200; $i++ )
            $db->query( "INSERT INTO ezt ( name ) VALUES ( 'n$i' )" );
        $db->commit();
        fwrite( STDERR, sprintf( "\nLL-04: 200 inserts in one transaction: %.2f ms each\n", ( microtime( true ) - $started ) * 5 ) );
        $this->assertSame( array( 202 ), $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezt', array( 'column' => 'c' ) ) );
    }

    /** LL-05 */
    public function testSerialIdsAndErrors()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE ezt ( id INTEGER PRIMARY KEY, name TEXT )' );
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'a' )" );
        $db->query( "INSERT INTO ezt ( name ) VALUES ( 'b' )" );
        $this->assertSame( 2, (int)$db->lastSerialID( 'ezt', 'id' ) );
        $db->query( 'CREATE TABLE ezv ( id INTEGER, version INTEGER NOT NULL, PRIMARY KEY ( id, version ) )' );
        $db->query( 'INSERT INTO ezv ( id, version ) VALUES ( 40, 1 )' );
        $db->query( 'INSERT INTO ezv ( version ) VALUES ( 1 )' );
        $this->assertSame( 41, (int)$db->lastSerialID( 'ezv', 'id' ) );
        $this->assertFalse( $db->query( "INSERT INTO ezt ( id, name ) VALUES ( 1, 'x' )" ) );
        $this->assertSame( 19, $db->errorNumber() );
        $this->assertStringContainsString( 'UNIQUE constraint failed: ezt.id', $db->errorMessage() );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM ez_missing' ) );
        $this->assertSame( 'no such table: ez_missing', $db->errorMessage() );
    }

    /** LL-06 */
    public function testFunctions()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE ezm ( g INT, mask INT, name TEXT )' );
        foreach ( array( array( 1, 5, 'a' ), array( 1, 6, 'b' ), array( 2, 7, 'c' ), array( 2, 3, "it's" ) ) as $row )
            $db->query( "INSERT INTO ezm VALUES ( {$row[0]}, {$row[1]}, '" . $db->escapeString( $row[2] ) . "' )" );
        $this->assertSame( array( array( 'g' => 1, 'o' => 7, 'a' => 4 ), array( 'g' => 2, 'o' => 7, 'a' => 3 ) ),
                           $db->arrayQuery( 'SELECT g, BIT_OR( mask ) AS o, BIT_AND( mask ) AS a FROM ezm GROUP BY g ORDER BY g' ) );
        $this->assertEquals( 1, $db->arrayQuery( 'SELECT MOD( 7, 3 ) AS m' )[0]['m'] );
        $this->assertSame( array( 'b' ), $db->arrayQuery( 'SELECT name FROM ezm WHERE MOD( mask, 2 ) = 0', array( 'column' => 'name' ) ) );
        $this->assertSame( md5( "it's" ), $db->arrayQuery( 'SELECT ' . $db->md5( 'name' ) . " AS h FROM ezm WHERE g = 2 AND mask = 3" )[0]['h'] );
        $this->assertSame( md5( "it's" ), trim( trim( $db->md5( "'it''s'" ) ), "'" ) );
        $this->assertSame( array( 7, 6 ), $db->arrayQuery( 'SELECT ' . $db->bitOr( 'mask', 2 ) . ' AS v FROM ezm WHERE g = 1 ORDER BY mask', array( 'column' => 'v' ) ) );
    }

    /** LL-07 */
    public function testHelpers()
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE ezh ( id INTEGER PRIMARY KEY, name TEXT, n INT )' );
        for ( $i = 1; $i <= 5; $i++ )
            $db->query( "INSERT INTO ezh ( id, name, n ) VALUES ( $i, 'n$i', $i )" );
        $this->assertSame( array( 3, 4 ), $db->arrayQuery( $db->sqlLimit( 'SELECT id FROM ezh ORDER BY id', 2, 2 ), array( 'column' => 'id' ) ) );
        $this->assertTrue( $db->query( $db->sqlUpsert( 'ezh', array( 'id' => 2, 'name' => 'up', 'n' => 9 ), array( 'id' ) ) ) );
        $this->assertSame( array( 'up' ), $db->arrayQuery( 'SELECT name FROM ezh WHERE id = 2', array( 'column' => 'name' ) ) );
        $concat = $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', ',' ) . ' AS c FROM ezh WHERE id < 3' )[0]['c'];
        $this->assertCount( 2, explode( ',', $concat ) );
        $now = (int)$db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp() . ' AS t' )[0]['t'];
        $this->assertEqualsWithDelta( time(), $now, 60 );
    }

    /** LL-08 */
    public function testTempTablesAndTwoConnections()
    {
        $db = $this->db();
        $this->assertTrue( $db->createTempTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, published int )' ) );
        $db->query( 'INSERT INTO ezsearch_tmp_0 VALUES ( 1, 2 )' );
        $this->assertSame( array( 1 ), $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezsearch_tmp_0', array( 'column' => 'c' ) ) );
        $db->close();
        $other = $this->db( array(), false );
        $this->assertSame( array( 0 ), $other->arrayQuery( "SELECT COUNT(*) AS c FROM sqlite_master WHERE name = 'ezsearch_tmp_0'", array( 'column' => 'c' ) ) );

        // two connections: a reader sees only what is committed; a second writer waits for the first
        $other->query( 'CREATE TABLE ezc ( id INTEGER PRIMARY KEY, name TEXT )' );
        $writer = $this->db( array(), false );
        $writer->begin();
        $writer->query( "INSERT INTO ezc ( name ) VALUES ( 'w' )" );
        $this->assertSame( array( 0 ), $other->arrayQuery( 'SELECT COUNT(*) AS c FROM ezc', array( 'column' => 'c' ) ) );
        $writer->commit();
        $this->assertSame( array( 1 ), $other->arrayQuery( 'SELECT COUNT(*) AS c FROM ezc', array( 'column' => 'c' ) ) );
    }

    /** LL-09 */
    public function testExpiredStreamAndToken()
    {
        $db = $this->db( array(), false );
        $this->assertSame( array( 1 ), $db->arrayQuery( 'SELECT 1 AS one', array( 'column' => 'one' ) ) );
        // sqld lets an idle stream go after 10 s
        sleep( 11 );
        $this->assertSame( array( 2 ), $db->arrayQuery( 'SELECT 2 AS two', array( 'column' => 'two' ) ) );
        $this->assertSame( 1, $db->transportStatistics()['retries'] );
        if ( (string)getenv( 'EXP_TEST_LIBSQL_TOKEN' ) === '' )
        {
            // a server without authentication takes any token
            $any = expLibSQLTestKit::liveDriver( array( 'AuthToken' => 'not-a-jwt' ) );
            $this->open[] = $any;
            $this->assertTrue( $any->isConnected() );
        }
        else
        {
            try
            {
                expLibSQLTestKit::liveDriver( array( 'AuthToken' => 'not-a-jwt', 'ConnectAttempts' => 1 ) );
                $this->fail( 'a wrong token connected' );
            }
            catch ( eZDBNoConnectionException $e )
            {
                $this->assertStringNotContainsString( 'not-a-jwt', $e->getMessage() );
            }
        }
    }

    /** LL-10 */
    public function testDataTableConnection()
    {
        $this->db();
        $url = expLibSQLTestKit::liveUrl();
        $parts = parse_url( $url );
        $c = array( 'identifier' => 'libsql_test', 'name' => 'libSQL test', 'engine' => 'libsql', 'server' => $parts['host'],
                    'port' => isset( $parts['port'] ) ? $parts['port'] : 8080, 'database' => 'exponential', 'user' => '', 'secret' => '',
                    'options' => array( 'url' => $url ) );
        $engines = expDataConnections::engines();
        $this->assertTrue( $engines['libsql']['available'], $engines['libsql']['why'] );
        $this->assertSame( 'expLibSQLDB', $engines['libsql']['handler'] );
        $plan = expDataConnections::plan( expDataConnections::normalize( $c ), (string)getenv( 'EXP_TEST_LIBSQL_TOKEN' ) );
        $this->assertSame( '', $plan['error'] );
        $this->assertSame( 'ez', $plan['via'] );
        $this->assertSame( array( 'URL' => $url ), $plan['parameters']['libsql'] );
        $test = expDataConnections::test( $c, (string)getenv( 'EXP_TEST_LIBSQL_TOKEN' ) );
        $this->assertTrue( $test['ok'], $test['error'] );
        $db = expDataConnections::open( expDataConnections::normalize( $c ), (string)getenv( 'EXP_TEST_LIBSQL_TOKEN' ) );
        $this->open[] = $db;
        $this->assertInstanceOf( 'expLibSQLDB', $db );
        $schema = expDataTableSchema::fromDefinition( array( 'name' => 'ezdt_libsql', 'increment_key' => 'id',
            'fields' => array( 'id' => array( 'datatype' => 'integer' ), 'title' => array( 'datatype' => 'string' ), 'score' => array( 'datatype' => 'float' ) ),
            'keys' => array( 'id' ) ) );
        $plan = expDataTableSchema::plan( $schema, $db );
        $this->assertSame( 'create', $plan['tables']['ezdt_libsql']['action'] );
        $result = expDataTableSchema::apply( $schema, $db );
        $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        $db->query( "INSERT INTO ezdt_libsql ( title, score ) VALUES ( 'Täst', 1.5 )" );
        $this->assertSame( 1, (int)$db->lastSerialID( 'ezdt_libsql', 'id' ) );
        $this->assertSame( array( array( 'title' => 'Täst', 'score' => 1.5 ) ), $db->arrayQuery( 'SELECT title, score FROM ezdt_libsql' ) );
        $again = expDataTableSchema::plan( $schema, $db );
        $this->assertSame( 'none', $again['tables']['ezdt_libsql']['action'], json_encode( $again['tables']['ezdt_libsql'] ) );
        $this->assertNotEmpty( expDataTableDescriber::columns( $db, 'ezdt_libsql' ) );
    }

    /** LL-11 */
    public function testStreamWrapper()
    {
        if ( !filter_var( ini_get( 'allow_url_fopen' ), FILTER_VALIDATE_BOOLEAN ) )
            $this->markTestSkipped( 'allow_url_fopen is off' );
        $this->db();
        $c = new expLibSQLConnection( expLibSQLTestKit::liveUrl(), (string)getenv( 'EXP_TEST_LIBSQL_TOKEN' ) );
        $c->UseCurl = false;
        $this->assertStringStartsWith( 'sqld', $c->serverVersion() );
        $this->assertTrue( $c->exec( 'CREATE TABLE ezw ( id INTEGER PRIMARY KEY, name TEXT UNIQUE )' ) );
        $this->assertTrue( $c->exec( 'BEGIN IMMEDIATE' ) );
        $this->assertTrue( $c->exec( "INSERT INTO ezw ( name ) VALUES ( 'ä' )" ) );
        $this->assertSame( 1, $c->lastInsertRowID() );
        $this->assertTrue( $c->exec( 'COMMIT' ) );
        $this->assertSame( 'ä', $c->querySingle( 'SELECT name FROM ezw' ) );
        $this->assertFalse( $c->exec( "INSERT INTO ezw ( name ) VALUES ( 'ä' )" ) );
        $this->assertSame( 19, $c->lastErrorCode() );
        // the server answers a baton it no longer knows with HTTP 400: read from the stream wrapper's headers
        $c->exec( 'SELECT 1' );
        sleep( 11 );
        $this->assertSame( 2, $c->querySingle( 'SELECT 2' ) );
        $this->assertSame( 1, $c->Retries );
        $c->close();
    }
}
