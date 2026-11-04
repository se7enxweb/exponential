<?php
/**
 * A transaction an exception leaves open between begin() and commit() is rolled back, never committed later.
 *
 *  TX-01 - eZDB::transaction() commits what its callable did, and rolls back (and throws on) when the callable throws:
 *          a later begin()/commit() pair commits, the counter is 0.
 *  TX-02 - nested in an outer transaction, a throwing eZDB::transaction() rolls the whole transaction back: the outer
 *          commit() commits nothing.
 *  TX-03 - eZDB::rollbackOpenTransaction() rolls back what an exception left open (two levels), and does nothing
 *          without an open transaction or without a database.
 *  TX-04 - two requests through expKernelServiceScope::resetRequest(), as a persistent worker serves them: the first
 *          throws inside a transaction, the second starts with no open transaction and its commit really commits.
 *  TX-05 - the web kernel rolls back the open transaction of a module that threw, before its cleanup handlers run
 *          (source check of ezpKernelWeb::run()).
 *
 * Before the fix the counter stayed raised: every later commit() of the request (or, in a persistent worker, of
 * the next one) only lowered it, and eZDB::checkTransactionCounter() committed the half done work at the end.
 *
 * No database server: a driver that records its transaction queries is set as the eZDB instance.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
class eZDBTransactionExceptionTestDriver extends eZDBInterface
{
    public $TransactionQueries = array();

    public function __construct()
    {
        $params = array_fill_keys(
            array( 'server', 'port', 'user', 'password', 'database', 'use_slave_server',
                   'slave_server', 'slave_port', 'slave_user', 'slave_password', 'slave_database',
                   'socket', 'is_internal_charset', 'builtin_encoding', 'connect_retries', 'use_persistent_connection' ),
            false );
        $params['charset'] = 'utf-8';
        $params['show_errors'] = false;
        parent::__construct( $params );
        $this->IsConnected = true;
    }

    function databaseName()
    {
        return 'recorder';
    }

    function query( $sql, $server = false )
    {
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
}

class eZDBTransactionExceptionTest extends PHPUnit\Framework\TestCase
{
    private $savedDB = null;
    private $hadDB = false;
    private $savedWorker = null;
    private $hadWorker = false;

    protected function setUp(): void
    {
        if ( eZINI::instance()->variable( 'DatabaseSettings', 'Transactions' ) !== 'enabled' )
            $this->markTestSkipped( 'transactions are disabled in site.ini' );
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->savedDB = $this->hadDB ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->hadWorker = array_key_exists( expKernelServiceScope::WORKER_GLOBAL, $GLOBALS );
        $this->savedWorker = $this->hadWorker ? $GLOBALS[expKernelServiceScope::WORKER_GLOBAL] : null;
    }

    protected function tearDown(): void
    {
        if ( $this->hadDB )
            $GLOBALS['eZDBGlobalInstance'] = $this->savedDB;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
        if ( $this->hadWorker )
            $GLOBALS[expKernelServiceScope::WORKER_GLOBAL] = $this->savedWorker;
        else
            unset( $GLOBALS[expKernelServiceScope::WORKER_GLOBAL] );
    }

    private function driver()
    {
        $db = new eZDBTransactionExceptionTestDriver();
        eZDB::setInstance( $db );
        return $db;
    }

    /** TX-01 */
    public function testTransactionCommitsAndRollsBackOnException()
    {
        $db = $this->driver();
        $this->assertSame( 42, eZDB::transaction( function ( $d ) use ( $db ) { return $d === $db ? 42 : 0; } ) );
        $this->assertSame( array( 'begin', 'commit' ), $db->TransactionQueries );

        $db->TransactionQueries = array();
        try
        {
            eZDB::transaction( function () { throw new RuntimeException( 'half done' ); } );
            $this->fail( 'the exception is thrown on' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertSame( 'half done', $e->getMessage() );
        }
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertSame( array( 'begin', 'rollback' ), $db->TransactionQueries );

        $db->TransactionQueries = array();
        $db->begin();
        $db->commit();
        $this->assertSame( array( 'begin', 'commit' ), $db->TransactionQueries, 'the next transaction commits' );
    }

    /** TX-02 */
    public function testNestedTransactionThatThrowsRollsBackTheOuterOne()
    {
        $db = $this->driver();
        $db->begin();
        try
        {
            eZDB::transaction( function () { throw new LogicException( 'inner' ); } );
        }
        catch ( LogicException $e )
        {
        }
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertFalse( @$db->commit(), 'nothing left to commit' );
        $this->assertSame( array( 'begin', 'rollback' ), $db->TransactionQueries );
    }

    /** TX-03 */
    public function testRollbackOpenTransaction()
    {
        unset( $GLOBALS['eZDBGlobalInstance'] );
        $this->assertSame( 0, eZDB::rollbackOpenTransaction(), 'no database' );

        $db = $this->driver();
        $this->assertSame( 0, eZDB::rollbackOpenTransaction(), 'no transaction' );
        $this->assertSame( array(), $db->TransactionQueries );

        $db->begin();
        $db->begin();
        $this->assertSame( 2, @eZDB::rollbackOpenTransaction( 'test' ) );
        $this->assertSame( 0, $db->transactionCounter() );
        $this->assertSame( array( 'begin', 'rollback' ), $db->TransactionQueries );
    }

    /** TX-04 */
    public function testPersistentWorkerStartsTheNextRequestWithoutTheOpenTransaction()
    {
        unset( $GLOBALS[expKernelServiceScope::WORKER_GLOBAL] );
        $db = $this->driver();

        // request 1 of the worker (the worker store is made during it): an exception between begin() and commit()
        @expKernelServiceScope::resetRequest();
        expKernelServiceScope::worker();
        $db->begin();
        $db->begin();
        $this->assertSame( 2, $db->transactionCounter(), 'request 1 left its transaction open' );

        // request 2, in the same worker and on the same connection
        @expKernelServiceScope::resetRequest();
        $this->assertSame( 0, $db->transactionCounter(), 'request 2 starts without it' );
        $db->TransactionQueries = array();
        $db->begin();
        $db->commit();
        $this->assertSame( array( 'begin', 'commit' ), $db->TransactionQueries, 'request 2 commits' );
    }

    /** TX-05 */
    public function testWebKernelRollsBackWhenTheModuleThrows()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/ezpkernelweb.php' );
        $at = strpos( $source, '$moduleResult = $this->dispatchLoop();' );
        $this->assertNotFalse( $at );
        $catch = substr( $source, $at, 900 );
        $rollback = strpos( $catch, 'eZDB::rollbackOpenTransaction(' );
        $this->assertNotFalse( $rollback, 'the catch of dispatchLoop() rolls back' );
        $this->assertLessThan( strpos( $catch, '$this->shutdown();' ), $rollback, 'before the cleanup handlers' );
    }
}
