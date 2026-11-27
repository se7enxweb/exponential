<?php
/**
 * The ClickHouse driver (expClickHouseDB) on a stand-in for the HTTP transport: connecting, statements and settings
 * sent, an UPDATE sent again as a mutation, transactions that send nothing, new ids, values read back, errors.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar')]
class expClickHouseDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        $this->saved = expColumnarTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expColumnarTestKit::restoreDebug( $this->saved );
    }

    public function testConnect()
    {
        $http = new expClickHouseFakeHttp();
        $db = expColumnarTestKit::clickhouse( $http );
        $this->assertTrue( $db->isConnected() );
        $this->assertSame( array( 'SELECT 1' ), $http->statements(), 'a first statement tells whether the server answers' );
        $this->assertSame( 'clickhouse', $db->databaseName() );
        $options = $db->transportOptions();
        $this->assertSame( array( 'ch.example', 8123, false, 'u', 'p', 'analytics' ),
                           array( $options['host'], $options['port'], $options['secure'], $options['user'], $options['password'], $options['database'] ) );

        $failing = new expClickHouseFakeHttp();
        $failing->Answers = array( '/^SELECT 1$/' => array( 'error' => 'default: Authentication failed', 'code' => 516 ) );
        try
        {
            expColumnarTestKit::clickhouse( $failing );
            $this->fail( 'no exception' );
        }
        catch ( eZDBNoConnectionException $e )
        {
            $this->assertStringContainsString( 'Authentication failed', $e->getMessage() );
        }
    }

    public function testSettingsOfAConnection()
    {
        $db = expColumnarTestKit::clickhouse( new expClickHouseFakeHttp(), array( 'Secure' => 'enabled', 'VerifyTLS' => 'disabled',
                                                                                    'Settings' => array( 'max_threads' => 2, 'bad name' => 1 ) ) );
        $options = $db->transportOptions();
        $this->assertTrue( $options['secure'] );
        $this->assertFalse( $options['verify_tls'] );
        $this->assertSame( array( 'max_threads' => 2 ), $options['settings'], 'only names a setting can have' );
    }

    public function testUpdateFallsBackToAMutation()
    {
        $http = new expClickHouseFakeHttp();
        $db = expColumnarTestKit::clickhouse( $http );
        $http->Answers = array( '/^UPDATE/' => array( 'error' => 'Lightweight updates are not supported.', 'code' => 48 ) );
        $http->Sent = array();
        $this->assertTrue( $db->query( 'UPDATE t SET n = 1 WHERE id = 2' ) );
        $this->assertSame( array( array( 'UPDATE t SET n = 1 WHERE id = 2', array( 'allow_experimental_lightweight_update' => 1 ) ),
                                  array( 'ALTER TABLE t UPDATE n = 1 WHERE id = 2', array( 'mutations_sync' => 2 ) ) ), $http->Sent );
    }

    public function testTransactionsSendNothing()
    {
        $http = new expClickHouseFakeHttp();
        $db = expColumnarTestKit::clickhouse( $http );
        $http->Sent = array();
        $db->begin();
        $db->query( 'INSERT INTO t VALUES (1)' );
        $db->rollback();
        $db->begin();
        $db->commit();
        $this->assertSame( array( 'INSERT INTO t VALUES (1)' ), $http->statements() );
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertTrue( $db->query( 'LOCK TABLE t WRITE' ) );
        $this->assertSame( array( 'INSERT INTO t VALUES (1)' ), $http->statements(), 'LOCK TABLE is not sent' );
    }

    public function testSerialIds()
    {
        $http = new expClickHouseFakeHttp();
        $db = expColumnarTestKit::clickhouse( $http );
        $http->Answers = array( '/max\(`id`\)/' => array( 'columns' => array( 'm' ), 'types' => array( 'Int32' ), 'rows' => array( array( '9' ) ) ) );
        $this->assertTrue( $db->serialIDNeedsValue( 't', 'id' ) );
        $this->assertSame( 10, $db->nextSerialID( 't', 'id' ) );
        $this->assertSame( 11, $db->nextSerialID( 't', 'id' ), 'never the same id twice in a connection' );
        $this->assertSame( 11, $db->lastSerialID( 't', 'id' ) );
        $this->assertFalse( $db->lastSerialID( 'other', 'id' ) );
        $this->assertFalse( $db->nextSerialID( 't; DROP', 'id' ) );
    }

    public function testValuesAndPaging()
    {
        $http = new expClickHouseFakeHttp();
        $db = expColumnarTestKit::clickhouse( $http );
        $http->Answers = array( '/^SELECT \* FROM t/' => array( 'columns' => array( 'id', 'ok', 'ts', 'n' ),
                                                                 'types' => array( 'UInt64', 'Bool', 'DateTime64(6)', 'Nullable(Bool)' ),
                                                                 'rows' => array( array( '1', 'true', '2026-01-02 03:04:05.250000', null ),
                                                                                  array( '2', 'false', '2026-01-02 03:04:05.000000', 'true' ) ) ) );
        $rows = $db->arrayQuery( 'SELECT * FROM t' );
        $this->assertSame( array( 'id' => '1', 'ok' => '1', 'ts' => '2026-01-02 03:04:05', 'n' => null ), $rows[0] );
        $this->assertSame( '1', $rows[1]['n'] );
        $this->assertSame( array( 10 => '1', 11 => '2' ), $db->arrayQuery( 'SELECT * FROM t', array( 'offset' => 10, 'limit' => 5, 'column' => 'id' ) ) );
        $this->assertSame( "SELECT * FROM t\nLIMIT 5 OFFSET 10", end( $http->Sent )[0] );
    }

    public function testErrors()
    {
        $http = new expClickHouseFakeHttp();
        $db = expColumnarTestKit::clickhouse( $http );
        $http->Answers = array( '/missing/' => array( 'error' => "Unknown table expression identifier 'missing'", 'code' => 60 ),
                                '/^SELECT 2/' => array( 'error' => 'The ClickHouse server did not answer', 'code' => -1 ) );
        $this->assertFalse( $db->arrayQuery( 'SELECT * FROM missing' ) );
        $this->assertSame( '42P01', $db->errorNumber() );
        $this->assertFalse( $db->arrayQuery( 'SELECT 2' ) );
        $this->assertSame( '08006', $db->errorNumber() );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $this->expectException( eZDBException::class );
        $db->query( 'SELECT * FROM missing' );
    }

    public function testStrings()
    {
        $db = expColumnarTestKit::clickhouse( new expClickHouseFakeHttp() );
        $this->assertSame( "it\\'s", $db->escapeString( "it's" ) );
        $this->assertSame( ' substringUTF8( name, 2, 3 ) ', $db->subString( 'name', 2, 3 ) );
        $this->assertSame( ' concat( a, b ) ', $db->concatString( array( 'a', 'b' ) ) );
        $this->assertSame( ' bitAnd( a, 4 ) ', $db->bitAnd( 'a', 4 ) );
        $this->assertFalse( $db->supportsDefaultValuesInsertion() );
        $this->assertSame( 'clickhouse', $db->sqlHelperDialect() );
    }
}
