<?php
/**
 * YugabyteDB live (EXP_TEST_PGWIRE_YUGABYTEDB_HOST ...): the checks of expPostgreSQLWireLiveTestCase, and
 *
 *  YL-01 - LOCK TABLE ... ACCESS EXCLUSIVE is refused (advisory locks too, without their preview flag): why lock()
 *          takes row locks
 *  YL-02 - pgcrypto's digest() is not there by default; the driver's md5() is the built-in one
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expPostgreSQLWireLiveTestCase.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('pgwire-live')]
class expYugabyteDBLiveTest extends expPostgreSQLWireLiveTestCase
{
    const PROFILE = 'yugabytedb';
    const DRIVER = 'expYugabyteDB';

    protected function refused( $sql )
    {
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $refused = false;
        try
        {
            $this->db->query( $sql );
        }
        catch ( eZDBException $e )
        {
            $refused = true;
        }
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        while ( (int)$this->db->transactionCounter() > 0 )
            $this->db->rollback();
        return $refused;
    }

    public function testWhyRowLocks()
    {
        $this->createTable();
        $this->db->begin();
        $this->assertTrue( $this->refused( "LOCK TABLE {$this->table}" ), 'no LOCK TABLE ... ACCESS EXCLUSIVE' );
    }

    public function testMd5WithoutPgcrypto()
    {
        $rows = $this->db->arrayQuery( "SELECT count(*) AS c FROM pg_proc WHERE proname = 'digest'" );
        if ( (int)$rows[0]['c'] === 0 )
            $this->assertTrue( $this->refused( "SELECT encode(digest('x', 'md5'), 'hex')" ) );
        $md5 = $this->db->arrayQuery( 'SELECT ' . $this->db->md5( "'x'" ) . ' AS h' );
        $this->assertSame( md5( 'x' ), $md5[0]['h'] );
    }
}
