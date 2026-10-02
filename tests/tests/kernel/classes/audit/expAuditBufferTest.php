<?php
/**
 * Buffering, flushing and the per-request reset of a persistent worker (doc/bc/6.0/audit.md, "Buffering and
 * flushing (F3)", acceptance test B4).
 *
 *  AB-01 — buffered events reach the file only at the flush, in one append; immediate events at once
 *  AB-02 — MaxEvents flushes early and records system.audit.overflow once per request
 *  AB-03 — the flush fills request.ms of the request's buffered records
 *  AB-04 — 1 000 Velocity-style requests in one process: each starts empty (resetRequest(), or a new
 *          REQUEST_TIME_FLOAT), no record carries another request's id, and what a request left unflushed is
 *          written by the next reset with its own request id
 *  AB-05 — a request that ends without the cleanup handler: the shutdown function flushes; flushOnFatal() records
 *          system.error.fatal and flushes
 *  AB-06 — a write that fails (the log directory cannot be created) never throws to the caller; the records are
 *          kept for one more try and then written to the error log, never lost silently
 *  AB-07 — Buffering=disabled writes every event at once
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditBufferTest extends PHPUnit\Framework\TestCase
{
    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
    }

    protected function tearDown(): void
    {
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    protected function move( $id )
    {
        return expAudit::event( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => $id ) ) );
    }

    /** AB-01 */
    public function testBufferedAndImmediate()
    {
        $this->move( 1 );
        $this->move( 2 );
        $this->assertSame( 2, expAudit::state()['buffered'] );
        $this->assertSame( array(), glob( $this->dir . 'log/content-*.jsonl' ) );
        expAudit::event( 'access.session.login' );
        $this->assertCount( 1, glob( $this->dir . 'log/access-*.jsonl' ), 'access.* is written at once' );
        expAudit::flush();
        $this->assertSame( 0, expAudit::state()['buffered'] );
        $this->assertCount( 2, expAuditTestFixtures::events( $this->dir, 'content' ) );
    }

    /** AB-02 */
    public function testEarlyFlush()
    {
        expAuditTestFixtures::configure( $this->dir, array( 'AuditBufferSettings/MaxEvents' => '10' ) );
        for ( $i = 0; $i < 25; $i++ )
            $this->move( $i );
        $this->assertCount( 20, expAuditTestFixtures::events( $this->dir, 'content' ) );
        $this->assertSame( 5, expAudit::state()['buffered'] );
        $overflow = array_values( array_filter( expAuditTestFixtures::events( $this->dir, 'system' ), function ( $r ) { return $r['name'] === 'system.audit.overflow'; } ) );
        $this->assertCount( 1, $overflow );
        $this->assertSame( 10, $overflow[0]['after']['events'] );
    }

    /** AB-03 */
    public function testFlushFillsDuration()
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime( true ) - 0.25;
        expAudit::resetRequest();
        $this->move( 1 );
        expAudit::flushFinal();
        $r = expAuditTestFixtures::events( $this->dir, 'content' )[0];
        $this->assertGreaterThanOrEqual( 250, $r['request']['ms'] );
    }

    /** AB-04 */
    public function testThousandVelocityRequests()
    {
        $seen = array();
        $base = microtime( true );
        for ( $n = 0; $n < 1000; $n++ )
        {
            $_SERVER['REQUEST_TIME_FLOAT'] = $base + $n;
            if ( $n % 2 === 0 )
                expAudit::resetRequest(); // ezpKernelWeb::__construct()
            // odd requests: no explicit reset, the new REQUEST_TIME_FLOAT does it at the first call
            $rid = expAudit::requestId();
            $state = expAudit::state();
            $this->assertSame( 0, $state['open'], "request $n starts without open parents" );
            $this->assertSame( 0, $state['buffered'], "request $n starts with an empty buffer" );
            $this->assertArrayNotHasKey( $rid, $seen );
            $seen[$rid] = $n;
            $this->move( $n );
            if ( $n % 10 === 0 )
                expAudit::begin( 'content.node.remove', array( 'object' => array( 'type' => 'node', 'id' => $n ) ) ); // left open
            if ( $n % 3 === 0 )
                expAudit::flushFinal(); // the cleanup handler; other requests leave their buffer to the next reset
        }
        expAudit::resetRequest();
        $events = expAuditTestFixtures::events( $this->dir, 'content' );
        $moves = array_values( array_filter( $events, function ( $r ) { return $r['name'] === 'content.node.move'; } ) );
        $this->assertCount( 1000, $moves );
        foreach ( $moves as $r )
            $this->assertSame( $r['object']['id'], $seen[$r['request']['id']], 'the record carries its own request id' );
        $removes = array_values( array_filter( $events, function ( $r ) { return $r['name'] === 'content.node.remove'; } ) );
        $this->assertCount( 100, $removes );
        foreach ( $removes as $r )
        {
            $this->assertSame( 'failed', $r['result'] );
            $this->assertSame( $r['object']['id'], $seen[$r['request']['id']] );
        }
        $this->assertSame( 'intact', expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' )['result'] );
    }

    /** AB-05 */
    public function testShutdownAndFatal()
    {
        $this->move( 1 );
        expAudit::shutdown();
        $this->assertCount( 1, expAuditTestFixtures::events( $this->dir, 'content' ) );
        $this->move( 2 );
        expAudit::flushOnFatal();
        $this->assertCount( 2, expAuditTestFixtures::events( $this->dir, 'content' ) );
    }

    /** AB-06 */
    public function testWriteFailureNeverThrows()
    {
        $blocker = $this->dir . 'blocker';
        file_put_contents( $blocker, 'a file where the log directory should be' );
        expAuditTestFixtures::configure( $this->dir, array( 'logDir' => $blocker . '/log' ) );
        $this->assertNotNull( $this->move( 1 ), 'the event is accepted' );
        $this->assertNotNull( expAudit::event( 'access.session.login' ), 'an immediate write that fails returns its id too' );
        expAudit::flush();
        $this->assertGreaterThanOrEqual( 1, expAudit::state()['failed'] );
        expAudit::flushFinal();
        $this->assertSame( 0, expAudit::state()['failed'], 'given up after the final try (written to the error log)' );
    }

    /** AB-07 */
    public function testBufferingDisabled()
    {
        expAuditTestFixtures::configure( $this->dir, array( 'AuditBufferSettings/Buffering' => 'disabled' ) );
        $this->move( 1 );
        $this->assertSame( 0, expAudit::state()['buffered'] );
        $this->assertCount( 1, expAuditTestFixtures::events( $this->dir, 'content' ) );
    }
}
