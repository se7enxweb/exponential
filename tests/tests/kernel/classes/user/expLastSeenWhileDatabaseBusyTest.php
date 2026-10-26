<?php
/**
 * The last-seen writes of a page view while another write holds a SQLite database (WAL, one writer at a time): the
 * visit stamp (eZUser::updateLastVisit(), ezuservisit) and the session's last use (expUserSessionTracker, the
 * column last_active of expuser_session). They waited busy_timeout (5 s) behind a publish or a content job and
 * then failed with "database is locked"; now they wait briefly and are left out, and the next request writes them.
 * The writes that matter keep the full wait. A SQLite file in var/tmp, locked by a second connection of this
 * process; no site database.
 *
 *  LSB-01  eZSQLite3DB::queryUnlessBusy() leaves a write out within a fraction of a second when the lock is held,
 *          without an error, and writes it when the lock is free; query() keeps busy_timeout
 *  LSB-02  eZUser::updateLastVisit() does not stand still or fail behind a held lock, and writes when it is free
 *  LSB-03  the session tracker's touch does not stand still or fail behind a held lock, and writes when it is free
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLastSeenWhileDatabaseBusyTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $file;

    /** @var eZSQLite3DB */
    private $db;

    /** @var SQLite3 the other writer */
    private $other;

    /** @var eZDBInterface|null */
    private $previous;

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        $dir = self::root() . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $this->file = $dir . '/lastseen-busy-' . getmypid() . '-' . mt_rand() . '.db';
        $this->db = new eZSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => $this->file,
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
        $this->assertTrue( $this->db->isConnected(), 'the SQLite file could not be opened' );
        $schema = file_get_contents( self::root() . '/kernel/sql/sqlite/schema.sql' );
        foreach ( array( 'expuser_session', 'ezuservisit' ) as $table )
        {
            $this->assertSame( 1, preg_match( '/CREATE TABLE `' . $table . '` \(.*?\n\);/s', $schema, $m ) );
            $this->db->query( $m[0] );
        }
        $this->previous = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        eZDB::setInstance( $this->db );
        if ( class_exists( 'expKernelTableSchema' ) )
            expKernelTableSchema::reset();
        expUserSessionTracker::reset();
        unset( $GLOBALS['eZUserUpdatedLastVisit'] );

        $this->other = new SQLite3( $this->file );
        $this->other->busyTimeout( 0 );
    }

    protected function tearDown(): void
    {
        unset( $GLOBALS['eZUserUpdatedLastVisit'] );
        if ( $this->other )
        {
            @$this->other->exec( 'ROLLBACK' );
            $this->other->close();
        }
        $this->other = null;
        if ( $this->db )
            $this->db->close();
        $this->db = null;
        if ( $this->previous )
            eZDB::setInstance( $this->previous );
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
        expUserSessionTracker::reset();
        foreach ( array( '', '-wal', '-shm', '.writer-lock' ) as $suffix )
        {
            if ( $this->file && file_exists( $this->file . $suffix ) )
                unlink( $this->file . $suffix );
        }
    }

    /** The other connection takes the write lock, as a publish does */
    private function lock()
    {
        $this->assertTrue( $this->other->exec( 'BEGIN IMMEDIATE' ) );
    }

    private function unlock()
    {
        $this->assertTrue( $this->other->exec( 'COMMIT' ) );
    }

    /** @return array( result, seconds ) */
    private function timed( callable $call )
    {
        $start = microtime( true );
        $result = $call();
        return array( $result, microtime( true ) - $start );
    }

    /** LSB-01 */
    public function testQueryUnlessBusyLeavesTheWriteOutWhileLocked()
    {
        $this->db->query( 'INSERT INTO ezuservisit ( user_id, current_visit_timestamp ) VALUES ( 14, 1 )' );
        $this->lock();
        list( $written, $seconds ) = $this->timed( function () {
            return $this->db->queryUnlessBusy( 'UPDATE ezuservisit SET current_visit_timestamp=2 WHERE user_id=14' );
        } );
        $this->assertFalse( $written );
        $this->assertLessThan( 1.0, $seconds, 'it waited for the lock' );
        // the statements' wait is back
        $this->assertSame( 5000, (int)$this->db->arrayQuery( 'PRAGMA busy_timeout' )[0]['timeout'] );
        $this->unlock();

        $this->assertTrue( $this->db->queryUnlessBusy( 'UPDATE ezuservisit SET current_visit_timestamp=3 WHERE user_id=14' ) );
        $this->assertSame( 3, (int)$this->db->arrayQuery( 'SELECT current_visit_timestamp FROM ezuservisit WHERE user_id=14' )[0]['current_visit_timestamp'] );
    }

    /** LSB-02 */
    public function testTheVisitStampDoesNotWaitForAHeldLock()
    {
        $this->db->query( 'INSERT INTO ezuservisit ( user_id, current_visit_timestamp, last_visit_timestamp ) VALUES ( 14, 100, 100 )' );
        $this->lock();
        list( , $seconds ) = $this->timed( function () { eZUser::updateLastVisit( 14 ); } );
        $this->assertLessThan( 1.0, $seconds, 'the page view stood still behind another write' );
        $this->unlock();
        $this->assertSame( 100, (int)$this->db->arrayQuery( 'SELECT current_visit_timestamp FROM ezuservisit WHERE user_id=14' )[0]['current_visit_timestamp'] );

        unset( $GLOBALS['eZUserUpdatedLastVisit'] );
        $before = time();
        eZUser::updateLastVisit( 14 );
        $row = $this->db->arrayQuery( 'SELECT current_visit_timestamp, last_visit_timestamp FROM ezuservisit WHERE user_id=14' )[0];
        $this->assertGreaterThanOrEqual( $before, (int)$row['current_visit_timestamp'] );
        $this->assertSame( 100, (int)$row['last_visit_timestamp'] );

        // a first visit is recorded too
        unset( $GLOBALS['eZUserUpdatedLastVisit'] );
        eZUser::updateLastVisit( 15 );
        $this->assertCount( 1, $this->db->arrayQuery( 'SELECT 1 FROM ezuservisit WHERE user_id=15' ) );
    }

    private function sessionRow( $lastActive )
    {
        $row = new expUserSession( array( 'user_id' => 14, 'token_hash' => hash( 'sha256', uniqid( '', true ) ),
            'created' => $lastActive, 'last_active' => $lastActive, 'first_ip' => '203.0.113.9', 'last_ip' => '203.0.113.9',
            'user_agent' => 'Mozilla/5.0', 'browser' => 'Firefox', 'os' => 'Linux', 'device_type' => 'desktop', 'method' => 'password',
            'remember_until' => 0, 'siteaccess' => 'site', 'ended' => 0, 'ended_by' => 0, 'end_reason' => '' ) );
        $row->store();
        return $row;
    }

    private function touch( expUserSession $row )
    {
        ( new ReflectionMethod( 'expUserSessionTracker', 'touch' ) )->invoke( null, $row );
    }

    private function lastActive( $id )
    {
        return (int)$this->db->arrayQuery( 'SELECT last_active FROM expuser_session WHERE id=' . (int)$id )[0]['last_active'];
    }

    /** LSB-03 */
    public function testTheSessionsLastUseDoesNotWaitForAHeldLock()
    {
        $row = $this->sessionRow( 1000 );
        $id = (int)$row->attribute( 'id' );
        $this->assertGreaterThan( 0, $id );

        $this->lock();
        list( , $seconds ) = $this->timed( function () use ( $row ) { $this->touch( $row ); } );
        $this->assertLessThan( 1.0, $seconds, 'the page view stood still behind another write' );
        $this->unlock();
        $this->assertSame( 1000, $this->lastActive( $id ) );
        $this->assertSame( 1000, (int)$row->attribute( 'last_active' ), 'the row claims a write that was left out' );

        $before = time();
        $this->touch( $row );
        $this->assertGreaterThanOrEqual( $before, $this->lastActive( $id ) );
        $this->assertSame( $this->lastActive( $id ), (int)$row->attribute( 'last_active' ) );
    }
}
?>
