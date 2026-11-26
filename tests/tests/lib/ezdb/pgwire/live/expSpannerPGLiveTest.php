<?php
/**
 * Google Cloud Spanner, PostgreSQL dialect, through PGAdapter (EXP_TEST_PGWIRE_SPANNER_HOST ...; CI and the tests
 * of the driver use PGAdapter with the Spanner emulator): the checks of expPostgreSQLWireLiveTestCase, and
 *
 *  SL-01 - new ids come from a bit-reversed sequence, 64-bit, read with RETURNING (there is no currval())
 *  SL-02 - what Spanner refuses: smallint, temporary tables, LOCK TABLE, advisory locks, md5()
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expPostgreSQLWireLiveTestCase.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('pgwire-live')]
class expSpannerPGLiveTest extends expPostgreSQLWireLiveTestCase
{
    const PROFILE = 'spanner';
    const DRIVER = 'expSpannerPGDB';

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
        return $refused;
    }

    public function testBitReversedIds()
    {
        $this->createTable();
        $this->db->query( "INSERT INTO {$this->table} ( name ) VALUES ( 'x' )" );
        $this->db->query( "INSERT INTO {$this->table} ( name ) VALUES ( 'y' )" );
        $id = $this->db->lastSerialID( $this->table, 'id' );
        $this->assertGreaterThan( 2147483647, $id, 'a bit-reversed id is past 32 bits' );
        $rows = $this->db->arrayQuery( "SELECT name FROM {$this->table} WHERE id = $id" );
        $this->assertSame( 'y', $rows[0]['name'] );
    }

    public function testRefused()
    {
        $this->assertTrue( $this->refused( "CREATE TABLE {$this->table}_s ( id smallint NOT NULL, PRIMARY KEY ( id ) )" ) );
        $this->assertTrue( $this->refused( 'SELECT pg_advisory_lock(1)' ) );
        $this->assertTrue( $this->refused( "SELECT md5('x')" ) );
        $this->createTable();
        $this->assertTrue( $this->refused( "LOCK TABLE {$this->table}" ) );
    }
}
