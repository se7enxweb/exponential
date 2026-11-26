<?php
/**
 * CockroachDB live (EXP_TEST_PGWIRE_COCKROACHDB_HOST ...): the checks of expPostgreSQLWireLiveTestCase, and
 *
 *  CL-01 - the session runs READ COMMITTED and may make temporary tables (the profile's session statements)
 *  CL-02 - retryTransaction() runs a transaction again that CockroachDB restarted (40001: a SERIALIZABLE
 *          transaction whose read row another connection changed before it wrote it)
 *  CL-03 - LOCK TABLE does not exist and pg_try_advisory_lock() locks nothing: why lock() takes row locks
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expPostgreSQLWireLiveTestCase.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('pgwire-live')]
class expCockroachDBLiveTest extends expPostgreSQLWireLiveTestCase
{
    const PROFILE = 'cockroachdb';
    const DRIVER = 'expCockroachDB';

    public function testSession()
    {
        $rows = $this->db->arrayQuery( 'SHOW default_transaction_isolation' );
        $this->assertSame( 'read committed', strtolower( (string)reset( $rows[0] ) ) );
        $rows = $this->db->arrayQuery( 'SHOW experimental_enable_temp_tables' );
        $this->assertSame( 'on', strtolower( (string)reset( $rows[0] ) ) );
    }

    public function testRestartedTransactionRunsAgain()
    {
        $this->createTable();
        $t = $this->table;
        $other = $this->connect();
        $attempts = 0;
        $value = $this->db->retryTransaction( function ( $db ) use ( $t, $other, &$attempts ) {
            $attempts++;
            // SERIALIZABLE, as CockroachDB runs by default: the row read here is changed by another connection
            // before this transaction writes it, so the server restarts the transaction (40001)
            $db->query( 'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE' );
            $db->arrayQuery( "SELECT flag FROM $t WHERE id = 2" );
            if ( $attempts === 1 )
                $other->query( "UPDATE $t SET flag = flag + 1 WHERE id = 2" );
            $db->query( "UPDATE $t SET flag = 99, name = 'retried' WHERE id = 2" );
            return $attempts;
        } );
        $other->close();
        $this->assertGreaterThanOrEqual( 2, $value, 'the transaction ran again' );
        $this->assertGreaterThanOrEqual( 1, $this->db->Retries );
        $rows = $this->db->arrayQuery( "SELECT flag, name FROM $t WHERE id = 2" );
        $this->assertSame( array( '99', 'retried' ), array( (string)$rows[0]['flag'], $rows[0]['name'] ) );
    }

    public function testWhyRowLocks()
    {
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $refused = false;
        try
        {
            $this->db->query( 'LOCK TABLE exp_wire_lock' );
        }
        catch ( eZDBException $e )
        {
            $refused = true;
        }
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertTrue( $refused, 'CockroachDB has no LOCK TABLE' );
        $other = $this->connect();
        $a = $this->db->arrayQuery( 'SELECT pg_try_advisory_lock(4242) AS l' );
        $b = $other->arrayQuery( 'SELECT pg_try_advisory_lock(4242) AS l' );
        $this->assertSame( array( 't', 't' ), array( (string)$a[0]['l'], (string)$b[0]['l'] ), 'both sessions "got" the same advisory lock' );
        $other->close();
    }
}
