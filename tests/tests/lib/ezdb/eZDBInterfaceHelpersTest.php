<?php
/**
 * The parts of eZDBInterface, eZNullDB and eZDBTool that need no database server, through a recording driver:
 *   - the attribute map (attributes/hasAttribute/attribute), the connection parameters it exposes
 *   - SQL helpers: bitAnd/bitOr, relationName for every relation type, implodeWithTypeCast (values that end in the
 *     glue keep it), generateSQLINStatement with a single value, failedQueryMessage (whitespace, length cap, no
 *     reason), truncateString/countStringSize on multi-byte text, generateUniqueTempTableName against taken names,
 *     setErrorHandling with a mode it does not know
 *   - insertFile()/prepareSqlQuery(): comments dropped, statements split on ";\n", a statement split over the 4 KB
 *     read buffer, a missing file, the path with and without the driver's type directory
 *   - transactions: nested begin/commit counting, rollback resets the counter, an invalidated transaction is
 *     rolled back by the outermost commit, commit/rollback without a transaction
 *   - eZNullDB: every query answers false, escapeString passes the text through
 *   - eZDBTool::isEmpty() and cleanup() with the driver's relation lists and match regexps
 *
 * No database server: the driver records the statements it is given. Temporary SQL files go to a private directory
 * under var/tmp that tearDown() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 */

/**
 * A driver that is "connected" and records every statement and transaction query instead of running it.
 */
class eZDBInterfaceHelpersTestDriver extends eZDBInterface
{
    public $Statements = array();
    public $TransactionQueries = array();
    public $Relations = array();
    public $Removed = array();
    public $MatchRegexp = null;
    public $FailRemove = false;

    public function __construct( $connected = true )
    {
        parent::__construct( eZDBInterfaceHelpersTest::parameters() );
        $this->IsConnected = $connected;
    }

    function databaseName()
    {
        return 'recorder';
    }

    function query( $sql, $server = false )
    {
        $this->Statements[] = $sql;
        return true;
    }

    function beginQuery()
    {
        $this->TransactionQueries[] = 'begin';
        return true;
    }

    function commitQuery()
    {
        $this->TransactionQueries[] = 'commit';
        return true;
    }

    function rollbackQuery()
    {
        $this->TransactionQueries[] = 'rollback';
        return true;
    }

    function eZTableList( $server = self::SERVER_MASTER )
    {
        return array_flip( $this->Relations[eZDBInterface::RELATION_TABLE] ?? array() );
    }

    function supportedRelationTypes()
    {
        return array_keys( $this->Relations );
    }

    function supportedRelationTypeMask()
    {
        $mask = 0;
        foreach ( array_keys( $this->Relations ) as $type )
            $mask |= ( 1 << $type );
        return $mask;
    }

    function relationCounts( $relationMask )
    {
        $count = 0;
        foreach ( $this->Relations as $type => $names )
        {
            if ( $relationMask & ( 1 << $type ) )
                $count += count( $names );
        }
        return $count;
    }

    function relationList( $relationType = eZDBInterface::RELATION_TABLE )
    {
        return $this->Relations[$relationType] ?? array();
    }

    function removeRelation( $relationName, $relationType )
    {
        if ( $this->FailRemove )
            return false;
        $this->Removed[] = array( $relationType, $relationName );
        return true;
    }

    function relationMatchRegexp( $relationType )
    {
        return $this->MatchRegexp;
    }
}

class eZDBInterfaceHelpersTest extends PHPUnit\Framework\TestCase
{
    private static $root;
    private $dir;

    public static function parameters()
    {
        $params = array_fill_keys(
            array( 'server', 'port', 'user', 'password', 'database', 'use_slave_server',
                   'slave_server', 'slave_port', 'slave_user', 'slave_password', 'slave_database',
                   'socket', 'is_internal_charset', 'builtin_encoding', 'connect_retries', 'use_persistent_connection' ),
            false
        );
        $params['server'] = 'db.example.invalid';
        $params['port'] = 3307;
        $params['database'] = 'exampledb';
        $params['user'] = 'exampleuser';
        $params['charset'] = 'utf-8';
        $params['is_internal_charset'] = true;
        return $params;
    }

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
    }

    protected function setUp(): void
    {
        chdir( self::$root );
        $this->dir = 'var/tmp/phpunit-ezdb-helpers-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/recorder', 0777, true );
    }

    protected function tearDown(): void
    {
        chdir( self::$root );
        foreach ( array( $this->dir . '/recorder', $this->dir ) as $dir )
        {
            foreach ( glob( $dir . '/*.sql' ) as $file )
                unlink( $file );
            rmdir( $dir );
        }
    }

    public function testAttributesExposeTheConnectionParameters()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertContains( 'database_name', $db->attributes() );
        $this->assertTrue( $db->hasAttribute( 'database_server' ) );
        $this->assertFalse( $db->hasAttribute( 'password' ), 'the password is not an attribute' );
        $this->assertSame( 'exampledb', $db->attribute( 'database_name' ) );
        $this->assertSame( 'db.example.invalid', $db->attribute( 'database_server' ) );
        $this->assertSame( 3307, $db->attribute( 'database_port' ) );
        $this->assertSame( 'exampleuser', $db->attribute( 'database_user' ) );
        $this->assertTrue( $db->isConnected() );
        $this->assertFalse( ( new eZDBInterfaceHelpersTestDriver( false ) )->isConnected() );
    }

    public function testBitExpressions()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertSame( '(a & 4 ) ', $db->bitAnd( 'a', 4 ) );
        $this->assertSame( '( a | 4 ) ', $db->bitOr( 'a', 4 ) );
    }

    public function testRelationNames()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertSame( 'TABLE', $db->relationName( eZDBInterface::RELATION_TABLE ) );
        $this->assertSame( 'SEQUENCE', $db->relationName( eZDBInterface::RELATION_SEQUENCE ) );
        $this->assertSame( 'TRIGGER', $db->relationName( eZDBInterface::RELATION_TRIGGER ) );
        $this->assertSame( 'VIEW', $db->relationName( eZDBInterface::RELATION_VIEW ) );
        $this->assertSame( 'INDEX', $db->relationName( eZDBInterface::RELATION_INDEX ) );
        $this->assertFalse( $db->relationName( 99 ) );
    }

    public static function implodeProvider()
    {
        return array(
            'ints'                      => array( ',', array( '1', '2x', 3.7 ), 'int', '1,2,3' ),
            'strings'                   => array( ', ', array( 'a', 'b' ), 'string', 'a, b' ),
            'single'                    => array( ',', array( 5 ), 'int', '5' ),
            'empty'                     => array( ',', array(), 'int', '' ),
            'last value ends in glue'   => array( ',', array( 'x,', 'y,' ), 'string', 'x,,y,' ),
            'last value is empty'       => array( '-', array( 'a', '' ), 'string', 'a-' ),
            'value ending in a space'   => array( ', ', array( 'a', 'b ' ), 'string', 'a, b ' ),
        );
    }

    /**
     * The values are joined by the glue and nothing else is removed. The trailing glue used to be cut off with
     * rtrim(), which takes a list of characters, so the last value also lost its own trailing glue characters.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('implodeProvider')]
    public function testImplodeWithTypeCast( $glue, $pieces, $type, $expected )
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertSame( $expected, $db->implodeWithTypeCast( $glue, $pieces, $type ) );
    }

    public function testGenerateSqlInStatementOfASingleValue()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertSame( 'id IN ( 7 )', $db->generateSQLINStatement( 7, 'id' ) );
        $this->assertSame( 'id NOT IN ( 7, 8 )', $db->generateSQLINStatement( array( '7', '8abc' ), 'id', true, true, 'int' ) );
        $this->assertSame( ' IN ( a )', $db->generateSQLINStatement( 'a' ) );
    }

    public function testFailedQueryMessage()
    {
        $this->assertSame( "Query failed: Duplicate key\nQuery: INSERT INTO t VALUES ( 1 )",
                           eZDBInterface::failedQueryMessage( " Duplicate key\n", "INSERT INTO t\n   VALUES ( 1 )\n" ) );
        $this->assertSame( "Query failed: no reason given by the database\nQuery: SELECT 1",
                           eZDBInterface::failedQueryMessage( '', 'SELECT 1' ) );
        $this->assertSame( "Query failed: x\nQuery: SELECT ... (12 characters in all)",
                           eZDBInterface::failedQueryMessage( 'x', 'SELECT 1 + 1', 6 ) );
    }

    public function testStringSizes()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertSame( 4, $db->countStringSize( 'äöüß' ) );
        $this->assertSame( 0, $db->countStringSize( 12 ) );
        $this->assertSame( 'äöüß', $db->truncateString( 'äöüß', 4, 'f' ) );
        $this->assertSame( 'äö', $db->truncateString( 'äöüß', 2, 'f' ) );
        $this->assertSame( 'ä', $db->truncateString( 'äöüß', 3, 'f', '..' ), 'room is left for the suffix' );
    }

    public function testUniqueTempTableNameSkipsTakenNames()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $db->Relations[eZDBInterface::RELATION_TABLE] = array( 'tmp_5', 'tmp_6' );
        $this->assertSame( 'tmp_7', $db->generateUniqueTempTableName( 'tmp_%', 5 ) );
        $this->assertSame( 'tmp_4', $db->generateUniqueTempTableName( 'tmp_%', 4 ) );
        $this->assertMatchesRegularExpression( '/^tmp_\d+$/', $db->generateUniqueTempTableName( 'tmp_%' ) );
    }

    public function testUnknownErrorHandlingModeIsRefused()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->expectException( RuntimeException::class );
        $db->setErrorHandling( 12345 );
    }

    public function testInsertFileRunsEveryStatementWithoutComments()
    {
        file_put_contents( $this->dir . '/recorder/schema.sql',
            "# a comment\n" .
            "CREATE TABLE a (\n  id int\n);\n" .
            "-- another comment\n" .
            "/* block */\n" .
            "INSERT INTO a VALUES (1);\r\n" .
            "INSERT INTO a VALUES (2);\n" );
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertTrue( $db->insertFile( $this->dir, 'schema.sql' ) );
        $this->assertSame( array( 'CREATE TABLE a (   id int )', 'INSERT INTO a VALUES (1)', 'INSERT INTO a VALUES (2)' ), $db->Statements );
    }

    public function testInsertFileWithoutTheTypeDirectory()
    {
        file_put_contents( $this->dir . '/plain.sql', "SELECT 1;\n" );
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertTrue( $db->insertFile( $this->dir, 'plain.sql', false ) );
        $this->assertSame( array( 'SELECT 1' ), $db->Statements );
    }

    public function testInsertFileJoinsAStatementSplitByTheReadBuffer()
    {
        $long = 'INSERT INTO a VALUES (\'' . str_repeat( 'x', 5000 ) . '\')';
        file_put_contents( $this->dir . '/recorder/long.sql', "SELECT 1;\n" . $long . ";\nSELECT 2;\n" );
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertTrue( $db->insertFile( $this->dir, 'long.sql' ) );
        $this->assertSame( array( 'SELECT 1', $long, 'SELECT 2' ), $db->Statements );
    }

    public function testInsertFileOfAMissingFile()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertFalse( $db->insertFile( $this->dir, 'no-such-file.sql' ) );
        $this->assertSame( array(), $db->Statements );
    }

    private function requireTransactions()
    {
        if ( eZINI::instance()->variable( 'DatabaseSettings', 'Transactions' ) !== 'enabled' )
            $this->markTestSkipped( 'transactions are disabled in site.ini' );
    }

    public function testNestedTransactionsCommitOnce()
    {
        $this->requireTransactions();
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertTrue( $db->begin() );
        $this->assertFalse( $db->begin(), 'a nested begin does not start a transaction' );
        $this->assertSame( 2, $db->transactionCounter() );
        $this->assertTrue( $db->commit() );
        $this->assertSame( array( 'begin' ), $db->TransactionQueries, 'the inner commit does not commit' );
        $this->assertTrue( $db->commit() );
        $this->assertSame( array( 'begin', 'commit' ), $db->TransactionQueries );
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertTrue( $db->isTransactionValid() );
    }

    public function testInvalidatedTransactionIsRolledBackByTheOutermostCommit()
    {
        $this->requireTransactions();
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertFalse( $db->invalidateTransaction(), 'nothing to invalidate outside a transaction' );
        $db->begin();
        $db->begin();
        $this->assertTrue( $db->invalidateTransaction() );
        $this->assertFalse( $db->isTransactionValid() );
        $db->commit();
        $this->assertFalse( $db->commit() );
        $this->assertSame( array( 'begin', 'rollback' ), $db->TransactionQueries );
    }

    public function testRollbackResetsTheCounter()
    {
        $this->requireTransactions();
        $db = new eZDBInterfaceHelpersTestDriver();
        $db->begin();
        $db->begin();
        $this->assertTrue( $db->rollback() );
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertSame( array( 'begin', 'rollback' ), $db->TransactionQueries );
    }

    public function testCommitAndRollbackWithoutATransaction()
    {
        $this->requireTransactions();
        $db = new eZDBInterfaceHelpersTestDriver();
        $this->assertFalse( @$db->commit() );
        $this->assertFalse( @$db->rollback() );
        $this->assertSame( array(), $db->TransactionQueries );
    }

    public function testNullDatabaseDoesNothing()
    {
        $db = new eZNullDB( self::parameters() );
        $this->assertSame( 'null', $db->databaseName() );
        $this->assertFalse( $db->query( 'SELECT 1' ) );
        $this->assertFalse( $db->arrayQuery( 'SELECT 1' ) );
        $this->assertFalse( $db->lastSerialID( 't', 'id' ) );
        $this->assertSame( "it's", $db->escapeString( "it's" ) );
        $this->assertNull( $db->begin() );
        $this->assertNull( $db->commit() );
        $this->assertNull( $db->rollback() );
        $this->assertNull( $db->lock( 't' ) );
        $this->assertNull( $db->unlock() );
        $this->assertNull( $db->close() );
    }

    public function testDbToolIsEmpty()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $db->Relations = array( eZDBInterface::RELATION_TABLE => array(), eZDBInterface::RELATION_SEQUENCE => array() );
        $this->assertTrue( eZDBTool::isEmpty( $db ) );
        $db->Relations[eZDBInterface::RELATION_SEQUENCE] = array( 'ezcontentobject_s' );
        $this->assertFalse( eZDBTool::isEmpty( $db ) );
    }

    public function testDbToolCleanupRemovesOnlyMatchingRelations()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $db->Relations = array(
            eZDBInterface::RELATION_TABLE => array( 'ezcontentobject', 'mytable', 'tmp_notification_rule_s' ),
            eZDBInterface::RELATION_SEQUENCE => array( 'ezuser_s', 'other_s' ),
        );
        $this->assertTrue( eZDBTool::cleanup( $db ) );
        $this->assertSame( array(
            array( eZDBInterface::RELATION_TABLE, 'ezcontentobject' ),
            array( eZDBInterface::RELATION_TABLE, 'tmp_notification_rule_s' ),
            array( eZDBInterface::RELATION_SEQUENCE, 'ezuser_s' ),
        ), $db->Removed );
    }

    public function testDbToolCleanupWithTheDriversRegexp()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $db->Relations = array( eZDBInterface::RELATION_TABLE => array( 'ezcontentobject', 'mytable' ) );
        $db->MatchRegexp = false; // every relation
        $this->assertTrue( eZDBTool::cleanup( $db ) );
        $this->assertCount( 2, $db->Removed );

        $db->Removed = array();
        $db->MatchRegexp = '#^my#';
        eZDBTool::cleanup( $db );
        $this->assertSame( array( array( eZDBInterface::RELATION_TABLE, 'mytable' ) ), $db->Removed );
    }

    public function testDbToolCleanupStopsAtTheFirstFailure()
    {
        $db = new eZDBInterfaceHelpersTestDriver();
        $db->Relations = array( eZDBInterface::RELATION_TABLE => array( 'eza', 'ezb' ) );
        $db->FailRemove = true;
        $this->assertFalse( eZDBTool::cleanup( $db ) );
    }
}
