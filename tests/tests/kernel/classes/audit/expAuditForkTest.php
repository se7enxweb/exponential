<?php
/**
 * A forked process and its parent's buffer; repeated record ids (doc/bc/6.0/audit.md, "Repeated records").
 *
 *  AF-01 — a child forked with buffered records writes only its own; the parent writes its records once; every
 *          record id is in the channel once and the child's records carry a request id of their own
 *  AF-02 — a child's final flush neither ends its parent's open parent events nor retries the parent's failed
 *          records: the parent ends its open parent once
 *  AF-03 — a child that only exits (shutdown flush, no event of its own) writes nothing
 *  AF-04 — a file that repeats a record id (written before the fix) verifies intact, with the repeats counted and a
 *          repeated_ids notice; the export from the files gives each id once
 *
 * The records go to a throwaway directory under var/tmp/audit-tests/ (expAuditTestFixtures), never to the live
 * log. Needs pcntl (the command line PHP has it); skipped without.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditForkTest extends PHPUnit\Framework\TestCase
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

    /**
     * Runs $code in a forked child and waits for it. The child leaves with SIGKILL (no PHPUnit shutdown) unless
     * $exitNormally, which runs the shutdown functions the parent registered.
     */
    protected function inChild( $code, $exitNormally = false )
    {
        if ( !function_exists( 'pcntl_fork' ) )
            $this->markTestSkipped( 'pcntl is not available' );
        $pid = pcntl_fork();
        $this->assertNotSame( -1, $pid, 'fork' );
        if ( $pid === 0 )
        {
            try
            {
                call_user_func( $code );
            }
            catch ( Throwable $e )
            {
                fwrite( STDERR, 'child: ' . $e->getMessage() . "\n" );
            }
            if ( $exitNormally )
            {
                expAudit::shutdown();
                exit( 0 );
            }
            posix_kill( getmypid(), SIGKILL );
        }
        pcntl_waitpid( $pid, $status );
    }

    /** @return array id => how often it is in the channel */
    protected function idCounts( $channel = 'content' )
    {
        return array_count_values( array_map( function ( $r ) { return $r['id']; }, expAuditTestFixtures::events( $this->dir, $channel ) ) );
    }

    /** AF-01 */
    public function testChildWritesOnlyItsOwnRecords()
    {
        $a = $this->move( 1 );
        $b = $this->move( 2 );
        $parentRequest = expAudit::requestId();
        $this->assertSame( 2, expAudit::state()['buffered'] );
        $childIds = array();
        foreach ( array( 10, 20, 30 ) as $node )
        {
            $this->inChild( function () use ( $node ) {
                $this->move( $node );
                expAudit::flush();
            } );
            $childIds[] = $node;
        }
        $this->assertSame( 2, expAudit::state()['buffered'], 'the parent still holds its own records' );
        expAudit::flush();

        $events = expAuditTestFixtures::events( $this->dir, 'content' );
        $this->assertCount( 5, $events, 'two of the parent, one of each child' );
        foreach ( $this->idCounts() as $id => $n )
            $this->assertSame( 1, $n, "record $id once" );
        foreach ( $events as $r )
        {
            if ( in_array( $r['id'], array( $a, $b ), true ) )
                $this->assertSame( $parentRequest, $r['request']['id'] );
            else
                $this->assertNotSame( $parentRequest, $r['request']['id'], 'a child has a request id of its own' );
        }
        $this->assertSame( 'intact', expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' )['result'] );
    }

    /** AF-02 */
    public function testChildLeavesOpenParentsToTheParent()
    {
        $parent = expAudit::begin( 'content.node.remove', array( 'object' => array( 'type' => 'node', 'id' => 5 ) ) );
        $this->assertNotNull( $parent );
        $this->assertSame( 1, expAudit::state()['open'] );
        $seen = $this->dir . 'child-state.json';
        $this->inChild( function () use ( $seen, $parent ) {
            $id = expAudit::withParent( $parent, function () { return $this->move( 6 ); } );
            file_put_contents( $seen, json_encode( expAudit::state() + array( 'event' => $id ) ) );
            expAudit::flush( true );
        } );
        $child = json_decode( (string)file_get_contents( $seen ), true );
        $this->assertSame( 0, $child['open'], 'the child holds none of the parent\'s open parents' );
        $this->assertSame( 1, $child['buffered'], 'only its own record' );
        expAudit::end( $parent, array( 'after' => array( 'done' => true ) ) );
        expAudit::flush( true );

        $events = expAuditTestFixtures::events( $this->dir, 'content' );
        $removes = array_values( array_filter( $events, function ( $r ) { return $r['name'] === 'content.node.remove'; } ) );
        $this->assertCount( 1, $removes, 'the open parent is written once, by the parent' );
        $this->assertSame( 'success', $removes[0]['result'] );
        $moves = array_values( array_filter( $events, function ( $r ) { return $r['name'] === 'content.node.move'; } ) );
        $this->assertCount( 1, $moves );
        $this->assertSame( $parent, $moves[0]['parent'], 'the child\'s record still names the parent event it worked for' );
    }

    /** AF-03 */
    public function testChildThatOnlyExitsWritesNothing()
    {
        $this->move( 1 );
        $this->move( 2 );
        $this->inChild( function () {}, true );
        $this->assertSame( array(), expAuditTestFixtures::events( $this->dir, 'content' ), 'the child wrote nothing' );
        expAudit::flush();
        $this->assertCount( 2, expAuditTestFixtures::events( $this->dir, 'content' ) );
    }

    /** AF-04 */
    public function testRepeatedIdsVerifyIntactAndExportOnce()
    {
        // what a child wrote before the fix: the same records once more, chained like any other lines
        $record = function ( $id, $node ) {
            return array( 'v' => 1, 'id' => $id, 'time' => gmdate( 'Y-m-d\TH:i:s' ) . '.000Z', 'name' => 'content.node.move',
                          'channel' => 'content', 'severity' => 'info', 'depth' => 0, 'result' => 'success',
                          'object' => array( 'type' => 'node', 'id' => $node ) );
        };
        $a = $record( '01M4T396AAAAAAAAAAAAAAAAAA', 7 );
        $b = $record( '01M4T396BBBBBBBBBBBBBBBBBB', 8 );
        $writer = expAudit::writerFor( expAuditConfig::get() );
        $writer->append( 'content', array( $a ) );
        $writer->append( 'content', array( $a, $b ) );
        $writer->append( 'content', array( $a, $b ) );

        $res = expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'content' );
        $this->assertSame( 'intact', $res['result'] );
        $this->assertSame( 6, $res['records'], 'the file open and five lines' );
        $this->assertSame( 3, $res['repeated'] );
        $this->assertContains( 'repeated_ids', array_map( function ( $n ) { return $n['kind']; }, $res['notices'] ) );

        $found = ( new expAuditExporter( expAuditConfig::get() ) )->search( array( 'channel' => 'content', 'name' => 'content.node.*' ), 0 );
        $ids = array_map( function ( $r ) { return $r['id']; }, $found );
        sort( $ids );
        $this->assertSame( array( $a['id'], $b['id'] ), $ids, 'each id once' );
    }
}
