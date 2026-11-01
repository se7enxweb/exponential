<?php
/**
 * The progress of a migration run for Setup > Migrations, without a database: the runner's progress callback
 * (expMigrationRunner option progress) and the progress file (expMigrationProgress) the administration reads.
 *
 *  PG-01 a run that writes tells run, phase, step and step-done of each migration in order and end; a dry run tells
 *        nothing; a callback that throws does not stop the run
 *  PG-02 a failure: step-done failed, the rest not-run, end with exit 1; StopOnFailure=disabled: blocked ones too
 *  PG-03 a rollback with down(): run (mode rollback), the last first, end
 *  PG-04 the file: one JSON object a line, appended under a lock; read() returns whole lines only and the offset to go
 *        on from; a half-written line waits; an unknown event is not written
 *  PG-05 fold(): total, done, the migration running now, the phase, failed ones, the end, the percentage; the meter
 *        steps (.a4-pct-0 ... -100)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerFakes.php';

class expMigrationProgressTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-progress-test-' . getmypid();
        if ( !is_dir( $this->dir ) )
            mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        expMigrationLock::installation( $this->dir . '/upgrade.lock' )->release();
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private function context( $apply = true )
    {
        return new expMigrationRunnerTestContext( $apply, array( 'lock-file' => $this->dir . '/upgrade.lock' ) );
    }

    /** @return array the callback and the list it fills */
    private static function recorder( array &$events )
    {
        return function ( $event, array $data ) use ( &$events ) {
            $events[] = array( $event, $data );
        };
    }

    /** PG-01 */
    public function testARunTellsItsProgress()
    {
        $events = array();
        $runner = new expMigrationRunner( $this->context(), array( 'progress' => self::recorder( $events ) ) );
        $report = $runner->run( array( new expMigrationRunnerFake( 'm1' ), new expMigrationRunnerFake( 'm2' ) ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'run', 'step', 'step-done', 'step', 'step-done', 'end' ), array_column( $events, 0 ) );
        $this->assertSame( array( 'run_id' => $report['run_id'], 'mode' => 'apply', 'total' => 2, 'ids' => array( 'm1', 'm2' ) ), $events[0][1] );
        $this->assertSame( array( 'id' => 'm2', 'index' => 2, 'total' => 2 ), $events[3][1] );
        $this->assertSame( 'm2', $events[4][1]['id'] );
        $this->assertSame( 'done', $events[4][1]['state'] );
        $this->assertSame( 'applied', $events[4][1]['action'] );
        $this->assertSame( 0, $events[5][1]['exit'] );
        $this->assertSame( 2, $events[5][1]['summary']['applied'] );

        $events = array();
        ( new expMigrationRunner( $this->context( false ), array( 'progress' => self::recorder( $events ) ) ) )->run( array( new expMigrationRunnerFake( 'm1' ) ) );
        $this->assertSame( array(), $events, 'a dry run tells nothing' );

        $report = ( new expMigrationRunner( $this->context(), array( 'progress' => function () { throw new RuntimeException( 'page gone' ); } ) ) )
            ->run( array( new expMigrationRunnerFake( 'm1' ) ) );
        $this->assertSame( 0, $report['exit'], 'a callback that throws does not stop the run' );
        $this->assertSame( 'applied', $report['migrations'][0]['action'] );
    }

    /** PG-02 */
    public function testAFailure()
    {
        $events = array();
        $report = ( new expMigrationRunner( $this->context(), array( 'progress' => self::recorder( $events ) ) ) )->run( array(
            new expMigrationRunnerFake( 'm1', array( 'up' => function () { throw new RuntimeException( 'broken' ); } ) ),
            new expMigrationRunnerFake( 'm2' ),
        ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( array( 'run', 'step', 'step-done', 'step-done', 'end' ), array_column( $events, 0 ) );
        $this->assertSame( array( 'failed', 'failed' ), array( $events[2][1]['state'], $events[2][1]['action'] ) );
        $this->assertSame( array( 'm2', 'not-run', 2 ), array( $events[3][1]['id'], $events[3][1]['action'], $events[3][1]['index'] ) );
        $this->assertSame( 1, $events[4][1]['exit'] );

        $events = array();
        ( new expMigrationRunner( $this->context(), array( 'progress' => self::recorder( $events ), 'stop-on-failure' => false ) ) )->run( array(
            new expMigrationRunnerFake( 'm1', array( 'up' => function () { throw new RuntimeException( 'broken' ); } ) ),
            new expMigrationRunnerFake( 'm2', array( 'requires' => array( 'm1' ) ) ),
        ) );
        $this->assertSame( 'blocked', $events[3][1]['action'] );
    }

    /** PG-03 */
    public function testARollbackTellsItsProgress()
    {
        $context = $this->context();
        foreach ( array( 'm1', 'm2' ) as $id )
            $context->records["site\n$id"] = array( 'source' => 'site', 'migration_id' => $id, 'state' => 'done', 'checksum' => "checksum-$id", 'run_id' => 'r', 'finished_at' => 1 );
        $events = array();
        $make = function ( $id ) {
            return new expMigrationRunnerFake( $id, array( 'reversible' => true, 'down' => function () { return array(); } ) );
        };
        $report = ( new expMigrationRunner( $context, array( 'progress' => self::recorder( $events ) ) ) )->rollback( array( $make( 'm1' ), $make( 'm2' ) ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'run', 'step', 'step-done', 'step', 'step-done', 'end' ), array_column( $events, 0 ) );
        $this->assertSame( 'rollback', $events[0][1]['mode'] );
        $this->assertSame( array( 'm2', 'm1' ), $events[0][1]['ids'], 'the last first' );
        $this->assertSame( 'undone', $events[2][1]['action'] );
    }

    /** PG-04 */
    public function testTheFile()
    {
        $file = $this->dir . '/run.progress';
        $write = expMigrationProgress::writer( $file, 1700000000 );
        $write( 'run', array( 'mode' => 'apply', 'total' => 2, 'ids' => array( 'a', 'b' ) ) );
        $write( 'nonsense', array( 'x' => 1 ) );
        $write( 'step', array( 'id' => 'a', 'index' => 1, 'total' => 2, 'time' => 5 ) );
        $lines = file( $file, FILE_IGNORE_NEW_LINES );
        $this->assertCount( 2, $lines, 'an unknown event is not written' );
        $this->assertSame( array( 'time' => 1700000000, 'event' => 'run', 'mode' => 'apply', 'total' => 2, 'ids' => array( 'a', 'b' ) ), json_decode( $lines[0], true ) );
        $this->assertSame( 1700000000, json_decode( $lines[1], true )['time'], 'the data cannot replace the time' );

        $read = expMigrationProgress::read( $file, 0 );
        $this->assertSame( array( 'run', 'step' ), array_column( $read['events'], 'event' ) );
        $this->assertSame( filesize( $file ), $read['offset'] );

        // a line still being written waits for its newline
        file_put_contents( $file, '{"time":1,"event":"step-done","id":"a"', FILE_APPEND );
        $more = expMigrationProgress::read( $file, $read['offset'] );
        $this->assertSame( array(), $more['events'] );
        $this->assertSame( $read['offset'], $more['offset'] );
        file_put_contents( $file, ",\"index\":1}\nnot json\n", FILE_APPEND );
        $more = expMigrationProgress::read( $file, $read['offset'] );
        $this->assertSame( array( 'step-done' ), array_column( $more['events'], 'event' ), 'a line that is no event is skipped' );
        $this->assertSame( filesize( $file ), $more['offset'] );

        $this->assertSame( array( 'events' => array(), 'offset' => 7 ), expMigrationProgress::read( $this->dir . '/missing', 7 ) );
        $again = expMigrationProgress::read( $file, 1 << 30 );
        $this->assertSame( array( 'run', 'step', 'step-done' ), array_column( $again['events'], 'event' ), 'an offset past the end (a new file) starts again' );
        $this->assertSame( filesize( $file ), $again['offset'] );
        $this->assertSame( '', expMigrationProgress::line( 'other', array(), 1 ) );
    }

    /** PG-05 */
    public function testFold()
    {
        $state = expMigrationProgress::fold( array() );
        $this->assertSame( array( 0, 0, '', false, null, 0 ), array( $state['total'], $state['done'], $state['current'], $state['ended'], $state['exit'], $state['percent'] ) );

        $state = expMigrationProgress::fold( array(
            array( 'event' => 'run', 'mode' => 'apply', 'total' => 3 ),
            array( 'event' => 'phase', 'name' => 'caption-pre' ),
        ) );
        $this->assertSame( array( 'apply', 3, 'caption-pre', 0 ), array( $state['mode'], $state['total'], $state['phase'], $state['percent'] ) );
        $state = expMigrationProgress::fold( array(
            array( 'event' => 'step', 'id' => 'a', 'index' => 1, 'total' => 3 ),
            array( 'event' => 'step-done', 'id' => 'a', 'index' => 1, 'total' => 3, 'action' => 'applied' ),
            array( 'event' => 'step', 'id' => 'b', 'index' => 2, 'total' => 3 ),
        ), $state );
        $this->assertSame( array( 1, 'b', 'migrations', 33 ), array( $state['done'], $state['current'], $state['phase'], $state['percent'] ) );
        $state = expMigrationProgress::fold( array(
            array( 'event' => 'step-done', 'id' => 'b', 'index' => 2, 'action' => 'failed' ),
            array( 'event' => 'step-done', 'id' => 'c', 'index' => 3, 'action' => 'not-run' ),
            array( 'event' => 'phase', 'name' => 'after-migrations' ),
            array( 'event' => 'end', 'exit' => 1 ),
        ), $state );
        $this->assertSame( array( 3, '', array( 'b' ), true, 1, 100 ), array( $state['done'], $state['current'], $state['failed'], $state['ended'], $state['exit'], $state['percent'] ) );

        $this->assertSame( 0, expMigrationProgress::meterStep( -5 ) );
        $this->assertSame( 32, expMigrationProgress::meterStep( 33 ) );
        $this->assertSame( 100, expMigrationProgress::meterStep( 100 ) );
        $this->assertSame( 100, expMigrationProgress::meterStep( 250 ) );
    }
}
