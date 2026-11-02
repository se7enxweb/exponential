<?php
/**
 * The migration runner without a database: a context that keeps the record, the run rows, the audit events, the
 * transactions and maintenance mode in memory (expMigrationRunnerTestContext), real ezpEvent listeners and a real lock
 * on a file under var/tmp.
 *
 *  RN-01 dry run: the order kept, pending migrations listed with their plan, nothing written, no lock taken
 *  RN-02 apply: up() in the order given, the record and the run rows, references passed on, the run's user
 *  RN-03 stop on failure: the failed migration rolled back and recorded, the rest not run, exit 1; with
 *        StopOnFailure=disabled the others run and those that require the failed one are blocked
 *  RN-04 the checksum guard: a changed applied migration is refused with the whole run; --force without a reason
 *        too; --force --reason accepts it, records the new checksum and audits the override
 *  RN-05 the lock: another holder refuses the run with who holds it; a run holds it with a holder file and frees it;
 *        a lock this process holds already is used and left held
 *  RN-06 expMigrationLock and expUpgradeLock: one lock file, the holder file, stale holders, acquire/release as
 *        exp:upgrade has always had them
 *  RN-07 events and audit: migration/run/before, step/before, step/after, run/after in order with their arguments;
 *        system.migration.run, .apply, .fail; a listener that throws does not stop the run
 *  RN-08 transactions per engine: full (PostgreSQL, SQLite) and data (MySQL, Oracle) wrap each migration, none
 *        (MongoDB) does not; transactional() false opts out; a failure on MySQL says the schema change stays
 *  RN-09 maintenance mode for a migration that asks for it, off again only when the run switched it on
 *  RN-10 refusals: no user to run as, no record table; other engines and environments are not-needed
 *  RN-11 PHP migrations: loaded from their namespaced file, classesIn(), the definition's header, reversible(),
 *        record-based state, detect(); a file without or with two migration classes; load() of other types
 *  RN-12 rollback: down() of the last first, the record removed, system.migration.rollback; a migration without
 *        down() or not applied is refused
 *  RN-13 settings: migration.ini [RunSettings], the six audit events in the catalogue and in audit.ini
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerFakes.php';

class expMigrationRunnerTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var string|null */
    private $lockFile;

    /** @var array name => listener id */
    private $listeners = array();

    /** @var array[] the events the listeners saw */
    private $events = array();

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        $dir = self::$root . '/var/tmp/migration-runner-test-' . getmypid();
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $this->lockFile = $dir . '/upgrade.lock';
    }

    protected function tearDown(): void
    {
        foreach ( $this->listeners as $name => $ids )
            foreach ( $ids as $id )
                ezpEvent::getInstance()->detach( $name, $id );
        $this->listeners = array();
        expUpgradeLock::release();
        expMigrationLock::installation( $this->lockFile )->release();
        $dir = dirname( $this->lockFile );
        foreach ( glob( $dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $dir );
    }

    /**
     * @param array $options
     * @return expMigrationRunnerTestContext
     */
    private function context( $apply = true, array $options = array() )
    {
        return new expMigrationRunnerTestContext( $apply, $options + array( 'lock-file' => $this->lockFile ) );
    }

    private function listen( array $names )
    {
        foreach ( $names as $name )
        {
            $this->listeners[$name][] = ezpEvent::getInstance()->attach( $name, function () use ( $name ) {
                $this->events[] = array( $name, func_get_args() );
            } );
        }
    }

    private static function actions( array $report )
    {
        $out = array();
        foreach ( $report['migrations'] as $entry )
            $out[$entry['id']] = $entry['action'];
        return $out;
    }

    /** A lock held by "another process": a second open file description of the lock file, flock()ed. */
    private function holdElsewhere()
    {
        $handle = fopen( $this->lockFile, 'c' );
        $this->assertTrue( flock( $handle, LOCK_EX | LOCK_NB ), 'the lock is free before the test holds it' );
        return $handle;
    }

    /** RN-01 */
    public function testDryRunListsThePlanAndWritesNothing()
    {
        $context = $this->context( false );
        $context->records['site' . "\n" . 'm2'] = array( 'source' => 'site', 'migration_id' => 'm2', 'state' => 'done', 'checksum' => 'c2', 'run_id' => 'r0', 'finished_at' => 1 );
        $runner = new expMigrationRunner( $context );
        $report = $runner->run( array( new expMigrationRunnerFake( 'm1' ), new expMigrationRunnerFake( 'm2', array( 'checksum' => 'c2' ) ),
                                       new expMigrationRunnerFake( 'm3' ) ) );
        $this->assertSame( expMigrationRunner::REPORT, $report['schema'] );
        $this->assertSame( 'dry-run', $report['mode'] );
        $this->assertSame( array( 'm1' => 'would-apply', 'm2' => 'none', 'm3' => 'would-apply' ), self::actions( $report ) );
        $this->assertSame( array( 'plan of m1' ), $report['migrations'][0]['plan'] );
        $this->assertSame( 0, $report['exit'] );
        $this->assertSame( 2, $report['summary']['would-apply'] );
        $this->assertSame( 1, $report['summary']['done'] );
        $this->assertSame( array(), $context->calls, 'a dry run changes nothing' );
        $this->assertSame( array(), $context->runs );
        $this->assertFalse( expMigrationLock::installation( $this->lockFile )->isLocked(), 'a dry run takes no lock' );
    }

    /** RN-02 */
    public function testApplyRunsInOrderAndRecords()
    {
        $context = $this->context();
        $order = array();
        $m1 = new expMigrationRunnerFake( 'm1', array( 'up' => function ( $c ) use ( &$order ) { $order[] = 'm1'; $c->setReference( 'from_m1', 42 ); return array( 'one' ); } ) );
        $m2 = new expMigrationRunnerFake( 'm2', array( 'requires' => array( 'm1' ), 'up' => function ( $c ) use ( &$order ) {
            $order[] = 'm2:' . $c->reference( 'from_m1' );
            return array( 'two' );
        } ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( $m1, $m2 ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'm1', 'm2:42' ), $order, 'in the order given, the reference of m1 reaches m2' );
        $this->assertSame( array( 'm1' => 'applied', 'm2' => 'applied' ), self::actions( $report ) );
        $this->assertSame( 'tester', $report['user'] );
        $row = $context->records["site\nm1"];
        $this->assertSame( 'done', $row['state'] );
        $this->assertSame( 'checksum-m1', $row['checksum'] );
        $this->assertSame( $context->runId(), $row['run_id'] );
        $this->assertSame( 'cli tester', $row['run_by'] );
        $this->assertSame( 'migration', $row['stage'] );
        $this->assertSame( 'one', $row['message'] );
        $this->assertSame( 'staging', $row['environment'] );
        $this->assertSame( array( 'running', 'done' ), array_column( $context->runs, 'state' ) );
        $this->assertSame( '2 applied', end( $context->runs )['message'] );
        $calls = array_values( array_intersect( $context->calls, array( 'become-user', 'begin', 'commit', 'restore-user', 'write-status' ) ) );
        $this->assertSame( array( 'become-user', 'begin', 'commit', 'begin', 'commit', 'restore-user', 'write-status' ), $calls,
                           'as the run\'s user, each migration in its transaction, the status mirror at the end' );
        $this->assertFalse( expMigrationLock::installation( $this->lockFile )->isLocked(), 'the lock is free after the run' );

        // a second run finds nothing to do
        $again = $this->context();
        $again->records = $context->records;
        $report = ( new expMigrationRunner( $again ) )->run( array( $m1, $m2 ) );
        $this->assertSame( array( 'm1' => 'none', 'm2' => 'none' ), self::actions( $report ) );
        $this->assertSame( array(), $again->runs, 'nothing to do: no run row' );
    }

    /** RN-03 */
    public function testStopsAtTheFirstFailure()
    {
        $context = $this->context();
        $ran = array();
        $fail = new expMigrationRunnerFake( 'm2', array( 'up' => function () use ( &$ran ) { $ran[] = 'm2'; throw new RuntimeException( 'no such class' ); } ) );
        $report = ( new expMigrationRunner( $context ) )->run( array(
            new expMigrationRunnerFake( 'm1', array( 'up' => function () use ( &$ran ) { $ran[] = 'm1'; return array(); } ) ),
            $fail,
            new expMigrationRunnerFake( 'm3', array( 'up' => function () use ( &$ran ) { $ran[] = 'm3'; return array(); } ) ),
        ) );
        $this->assertSame( array( 'm1', 'm2' ), $ran );
        $this->assertSame( array( 'm1' => 'applied', 'm2' => 'failed', 'm3' => 'not-run' ), self::actions( $report ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( 'failed', $context->records["site\nm2"]['state'] );
        $this->assertSame( 'no such class', $context->records["site\nm2"]['message'] );
        $this->assertArrayNotHasKey( "site\nm3", $context->records );
        $this->assertSame( array( 'begin', 'commit', 'begin', 'rollback' ), array_values( array_intersect( $context->calls, array( 'begin', 'commit', 'rollback' ) ) ) );
        $this->assertSame( 'failed', end( $context->runs )['state'] );

        // StopOnFailure=disabled: m3 runs, m4 that requires m2 is blocked
        $context = $this->context();
        $report = ( new expMigrationRunner( $context, array( 'stop-on-failure' => false ) ) )->run( array(
            new expMigrationRunnerFake( 'm2', array( 'up' => function () { throw new RuntimeException( 'broken' ); } ) ),
            new expMigrationRunnerFake( 'm3' ),
            new expMigrationRunnerFake( 'm4', array( 'requires' => array( 'm2', 'extension:expnews' ) ) ),
        ) );
        $this->assertSame( array( 'm2' => 'failed', 'm3' => 'applied', 'm4' => 'blocked' ), self::actions( $report ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( 1, $report['summary']['blocked'] );

        // a failed migration is run again by the next run
        $next = $this->context();
        $next->records = $context->records;
        $report = ( new expMigrationRunner( $next ) )->run( array( new expMigrationRunnerFake( 'm2' ), new expMigrationRunnerFake( 'm3' ) ) );
        $this->assertSame( array( 'm2' => 'applied', 'm3' => 'none' ), self::actions( $report ) );
        $this->assertSame( 0, $report['exit'] );
        $this->assertFalse( expMigrationRunner::settingsOptions( array( 'stop-on-failure' => false ) )['stop-on-failure'], 'an option wins over the setting' );
    }

    /** RN-04 */
    public function testChecksumGuard()
    {
        $records = array( "site\nm1" => array( 'source' => 'site', 'migration_id' => 'm1', 'state' => 'done', 'checksum' => 'old', 'run_id' => 'r0',
                                               'finished_at' => 1, 'message' => '' ) );
        $migrations = function () {
            return array( new expMigrationRunnerFake( 'm1', array( 'checksum' => 'new' ) ), new expMigrationRunnerFake( 'm2' ) );
        };

        $context = $this->context();
        $context->records = $records;
        $report = ( new expMigrationRunner( $context ) )->run( $migrations() );
        $this->assertSame( array( 'm1' => 'refused', 'm2' => 'none' ), self::actions( $report ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertStringContainsString( 'm1 changed after it was applied (checksum old -> new)', $report['errors'][0] );
        $this->assertStringContainsString( '--force --reason=<text>', $report['errors'][0] );
        $this->assertSame( array(), $context->calls, 'nothing runs' );
        $this->assertTrue( $report['migrations'][0]['changed'] );

        $context = $this->context( true );
        $context->records = $records;
        $report = ( new expMigrationRunner( $context, array( 'force' => true ) ) )->run( $migrations() );
        $this->assertSame( 1, $report['exit'] );
        $this->assertStringContainsString( '--force needs a --reason=<text>', $report['errors'][0] );

        // a dry run with --force --reason says what it would accept
        $context = $this->context( false );
        $context->records = $records;
        $report = ( new expMigrationRunner( $context, array( 'force' => true, 'reason' => 'typo fixed' ) ) )->run( $migrations() );
        $this->assertSame( array( 'm1' => 'would-accept', 'm2' => 'would-apply' ), self::actions( $report ) );
        $this->assertSame( 0, $report['exit'] );

        $context = $this->context();
        $context->records = $records;
        $report = ( new expMigrationRunner( $context, array( 'force' => true, 'reason' => 'typo fixed in a comment' ) ) )->run( $migrations() );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'm1' => 'accepted', 'm2' => 'applied' ), self::actions( $report ) );
        $this->assertSame( 'new', $context->records["site\nm1"]['checksum'] );
        $this->assertSame( 'r0', $context->records["site\nm1"]['run_id'], 'the migration is not run again' );
        $this->assertStringContainsString( 'typo fixed in a comment', $context->records["site\nm1"]['message'] );
        $accepted = array_values( array_filter( $context->audits, function ( $a ) { return $a[0] === 'system.migration.apply' && $a[1]['verb'] === 'accept-change'; } ) );
        $this->assertCount( 1, $accepted );
        $this->assertSame( 'typo fixed in a comment', $accepted[0][1]['reason'] );
        $this->assertSame( array( 'checksum_before' => 'old', 'checksum_after' => 'new' ),
                           array_intersect_key( $accepted[0][1]['x'], array( 'checksum_before' => 1, 'checksum_after' => 1 ) ) );
    }

    /** RN-05 */
    public function testTheLock()
    {
        $handle = $this->holdElsewhere();
        file_put_contents( $this->lockFile . '.holder', json_encode( array( 'name' => 'upgrade', 'purpose' => 'exp:upgrade', 'user' => 'system user deploy',
                                                                             'pid' => 4242, 'host' => 'web1', 'started' => 1761912000 ) ) );
        $context = $this->context();
        $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1' ) ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertStringContainsString( 'another run holds the lock: exp:upgrade by system user deploy (pid 4242 on web1) since ', $report['errors'][0] );
        $this->assertSame( array(), $context->runs, 'nothing was started' );
        flock( $handle, LOCK_UN );
        fclose( $handle );

        // the run holds the lock with a holder file while it runs, and frees it
        $seen = null;
        $lockFile = $this->lockFile;
        $context = $this->context();
        $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1', array( 'up' => function () use ( &$seen, $lockFile ) {
            $seen = expMigrationLock::installation( $lockFile )->holder();
            return array();
        } ) ) ) );
        $this->assertSame( 0, $report['exit'] );
        $this->assertSame( 'migration run ' . $context->runId(), $seen['purpose'] );
        $this->assertSame( 'tester', $seen['user'] );
        $this->assertSame( getmypid(), $seen['pid'] );
        $this->assertFalse( $seen['stale'] );
        $this->assertFileDoesNotExist( $this->lockFile . '.holder' );
        $this->assertFalse( expMigrationLock::installation( $this->lockFile )->isLocked() );

        // held already by this process (exp:upgrade running migrations): used, and still held afterwards
        $this->assertTrue( expUpgradeLock::acquire( $this->lockFile ) );
        $report = ( new expMigrationRunner( $this->context() ) )->run( array( new expMigrationRunnerFake( 'm1' ) ) );
        $this->assertSame( 0, $report['exit'] );
        $this->assertTrue( expMigrationLock::installation( $this->lockFile )->isHeld(), 'the lock of exp:upgrade is left to it' );
        expUpgradeLock::release();
        $this->assertFalse( expMigrationLock::installation( $this->lockFile )->isLocked() );
    }

    /** RN-06 */
    public function testLockClasses()
    {
        $lock = expMigrationLock::installation( $this->lockFile );
        $this->assertSame( 'upgrade', $lock->name() );
        $this->assertSame( $this->lockFile . '.holder', $lock->holderFile() );
        $this->assertSame( 'upgrade.lock', basename( expUpgradeLock::file() ), 'exp:upgrade keeps var/<site>/upgrade.lock' );
        $this->assertSame( expMigrationLock::defaultFile( 'upgrade' ), expUpgradeLock::file(), 'one lock file for both' );
        $this->assertNull( $lock->holder() );

        // expUpgradeLock as before: acquire, again true, the pid in the lock file, release, release again
        $this->assertTrue( expUpgradeLock::acquire( $this->lockFile ) );
        $this->assertTrue( expUpgradeLock::acquire( $this->lockFile ) );
        $this->assertSame( (string)getmypid(), trim( file_get_contents( $this->lockFile ) ) );
        $holder = expUpgradeLock::holder( $this->lockFile );
        $this->assertSame( 'exp:upgrade', $holder['purpose'] );
        $this->assertFalse( $holder['stale'] );
        $other = fopen( $this->lockFile, 'c' );
        $this->assertFalse( flock( $other, LOCK_EX | LOCK_NB ), 'held against another process' );
        expUpgradeLock::release();
        $this->assertTrue( flock( $other, LOCK_EX | LOCK_NB ), 'released' );
        expUpgradeLock::release();

        // held by another process: neither class gets it; the holder is not stale
        $this->assertFalse( expUpgradeLock::acquire( $this->lockFile ) );
        $this->assertFalse( $lock->acquire() );
        $this->assertTrue( $lock->isLocked() );
        $this->assertFalse( $lock->isHeld() );
        flock( $other, LOCK_UN );
        fclose( $other );

        // a holder file without a lock is stale and taken over
        file_put_contents( $lock->holderFile(), json_encode( array( 'name' => 'upgrade', 'purpose' => 'migration run x', 'pid' => 999999, 'started' => 1 ) ) );
        $holder = $lock->holder();
        $this->assertTrue( $holder['stale'] );
        $this->assertStringEndsWith( ', stale: its process has ended', expMigrationLock::describe( $holder ) );
        $this->assertTrue( $lock->acquire( array( 'purpose' => 'test', 'user' => 'u' ) ) );
        $holder = $lock->holder();
        $this->assertSame( 'test', $holder['purpose'] );
        $this->assertFalse( $holder['stale'] );
        $lock->release();
        $this->assertFileDoesNotExist( $lock->holderFile() );
        $this->assertSame( 'nobody', expMigrationLock::describe( null ) );

        $this->expectException( InvalidArgumentException::class );
        new expMigrationLock( '../etc/passwd' );
    }

    /** RN-07 */
    public function testEventsAndAudit()
    {
        $this->listen( array( 'migration/run/before', 'migration/step/before', 'migration/step/after', 'migration/run/after' ) );
        $context = $this->context();
        $m1 = new expMigrationRunnerFake( 'm1' );
        $m2 = new expMigrationRunnerFake( 'm2', array( 'up' => function () { throw new RuntimeException( 'nope' ); } ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( $m1, $m2 ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( array( 'migration/run/before', 'migration/step/before', 'migration/step/after', 'migration/step/before',
                                  'migration/step/after', 'migration/run/after' ), array_column( $this->events, 0 ) );
        list( , $before ) = $this->events[0];
        $this->assertSame( array( $context->runId(), array( 'm1', 'm2' ) ), array_slice( $before, 0, 2 ) );
        $this->assertSame( $context, $before[2] );
        $this->assertSame( $m1, $this->events[1][1][1] );
        $this->assertSame( 'applied', $this->events[2][1][2]['action'] );
        $this->assertSame( 'failed', $this->events[4][1][2]['action'] );
        $this->assertSame( 1, $this->events[5][1][1]['exit'] );

        $this->assertSame( array( 'system.migration.apply', 'system.migration.fail', 'system.migration.run' ), array_column( $context->audits, 0 ) );
        $this->assertSame( array( 'type' => 'migration', 'id' => 'm2' ), $context->audits[1][1]['object'] );
        $this->assertSame( 'failure', $context->audits[1][1]['result'] );
        $this->assertSame( 'nope', $context->audits[1][1]['reason'] );
        $this->assertSame( array( 'type' => 'migration_run', 'id' => $context->runId() ), $context->audits[2][1]['object'] );
        $this->assertSame( 'failure', $context->audits[2][1]['result'] );

        // a listener that throws is logged, the run goes on
        $this->listeners['migration/step/before'][] = ezpEvent::getInstance()->attach( 'migration/step/before', function () {
            throw new RuntimeException( 'listener broke' );
        } );
        $context = $this->context();
        $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm9' ) ) );
        $this->assertSame( 0, $report['exit'] );
        $this->assertNotEmpty( array_filter( $context->logs, function ( $l ) { return strpos( $l[1], 'listener broke' ) !== false; } ) );
    }

    /** RN-08 */
    public function testTransactionsPerEngine()
    {
        $this->assertSame( 'full', expMigrationContext::transactionModeOf( 'postgresql' ) );
        $this->assertSame( 'full', expMigrationContext::transactionModeOf( 'sqlite' ) );
        $this->assertSame( 'data', expMigrationContext::transactionModeOf( 'mysql' ) );
        $this->assertSame( 'data', expMigrationContext::transactionModeOf( 'oracle' ) );
        $this->assertSame( 'none', expMigrationContext::transactionModeOf( 'mongodb' ) );

        foreach ( array( 'sqlite' => 'full', 'mysql' => 'data', 'mongodb' => 'none' ) as $engine => $mode )
        {
            $context = $this->context( true, array( 'engine' => $engine ) );
            $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1' ),
                                                                         new expMigrationRunnerFake( 'm2', array( 'transactional' => false ) ) ) );
            $this->assertSame( $mode, $report['transaction'] );
            $this->assertSame( $mode, $report['migrations'][0]['transaction'], $engine );
            $this->assertSame( 'none', $report['migrations'][1]['transaction'], "$engine: transactional() false" );
            $this->assertSame( $mode === 'none' ? array() : array( 'begin', 'commit' ),
                               array_values( array_intersect( $context->calls, array( 'begin', 'commit', 'rollback' ) ) ), $engine );
        }

        $context = $this->context( true, array( 'engine' => 'mysql' ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1', array( 'up' => function () { throw new RuntimeException( 'x' ); } ) ) ) );
        $this->assertContains( 'rolled back what could be: mysql keeps schema changes made before the failure', $report['migrations'][0]['messages'] );
        $context = $this->context( true, array( 'engine' => 'mongodb' ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1', array( 'up' => function () { throw new RuntimeException( 'x' ); } ) ) ) );
        $this->assertNotContains( 'rollback', $context->calls, 'MongoDB: nothing to roll back' );
    }

    /** RN-09 */
    public function testMaintenance()
    {
        $context = $this->context();
        ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1', array( 'maintenance' => true ) ), new expMigrationRunnerFake( 'm2' ) ) );
        $this->assertSame( array( 'maintenance-begin', 'begin', 'commit', 'maintenance-end', 'begin', 'commit' ),
                           array_values( array_intersect( $context->calls, array( 'maintenance-begin', 'maintenance-end', 'begin', 'commit' ) ) ) );

        // maintenance was on already: it stays on
        $context = $this->context( true, array( 'maintenance' => 'existing' ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1', array( 'maintenance' => true ) ) ) );
        $this->assertSame( 'existing', $report['migrations'][0]['maintenance'] );
        $this->assertNotContains( 'maintenance-end', $context->calls );
    }

    /** RN-10 */
    public function testRefusalsAndFilters()
    {
        $context = $this->context( true, array( 'no-user' => true ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1' ) ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertStringContainsString( 'the user to run as does not exist', $report['errors'][0] );
        $this->assertSame( array(), $context->runs );

        $context = $this->context( true, array( 'no-table' => true ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( new expMigrationRunnerFake( 'm1' ) ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertStringContainsString( 'the table exp_migration does not exist yet', $report['errors'][0] );

        $context = $this->context();
        $report = ( new expMigrationRunner( $context ) )->run( array(
            new expMigrationRunnerFake( 'm1', array( 'engines' => array( 'postgresql' ) ) ),
            new expMigrationRunnerFake( 'm2', array( 'environments' => array( '!staging' ) ) ),
            new expMigrationRunnerFake( 'm3', array( 'environments' => array( 'production', 'staging' ), 'engines' => array( 'sqlite' ) ) ),
            new expMigrationRunnerFake( 'm4', array( 'state' => function () { throw new RuntimeException( 'cannot read' ); } ) ),
        ) );
        $this->assertSame( array( 'm1' => 'none', 'm2' => 'none', 'm3' => 'applied', 'm4' => 'none' ), self::actions( $report ) );
        $this->assertSame( 'not for sqlite', $report['migrations'][0]['detail'] );
        $this->assertSame( 'not for the environment staging', $report['migrations'][1]['detail'] );
        $this->assertSame( 'unknown', $report['migrations'][3]['state'] );
        $this->assertStringContainsString( 'm4: skipped, its state cannot be told', $report['warnings'][0] );
        $this->assertTrue( expMigrationRunner::appliesTo( array(), 'x' ) );
        $this->assertFalse( expMigrationRunner::appliesTo( array( 'production' ), '' ) );
    }

    /** RN-11 */
    public function testPhpMigrations()
    {
        $dir = __DIR__ . '/fixtures/runner';
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $graph = new expMigrationGraph( $locator->find() );
        $this->assertSame( array(), $locator->errors() );
        $errors = array();
        $migrations = expMigrationRunner::load( $graph->migrations(), $errors );
        $this->assertSame( array(), $errors );
        $this->assertCount( 2, $migrations );
        $this->assertInstanceOf( 'ExpTests\\Migrations\\Runner\\NoteTable', $migrations[0] );
        $this->assertSame( '20261031130000_wp3_note_table', $migrations[0]->id() );
        $this->assertSame( 'site', $migrations[0]->source() );
        $this->assertSame( 'A table for notes, with a first note', $migrations[0]->description() );
        $this->assertSame( array( '20261031130000_wp3_note_table' ), $migrations[1]->requires() );
        $this->assertSame( expMigrationDefinition::checksum( "$dir/20261031130000_wp3_note_table.php" ), $migrations[0]->checksum() );
        $this->assertTrue( $migrations[0]->reversible() );
        $this->assertTrue( $migrations[0]->transactional() );
        $this->assertFalse( $migrations[0]->maintenance() );
        $this->assertSame( array( '*' ), $migrations[0]->engines() );

        // loaded twice: the same class, no redeclaration
        $again = expMigrationRunner::load( $graph->migrations(), $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( get_class( $migrations[0] ), get_class( $again[0] ) );

        // the state goes by the record
        $context = $this->context( false );
        $this->assertSame( 'pending', $migrations[0]->state( $context ) );
        $context->records["site\n20261031130000_wp3_note_table"] = array( 'state' => 'done' );
        $this->assertSame( 'done', $migrations[0]->state( $context ) );
        $context->records["site\n20261031130000_wp3_note_table"] = array( 'state' => 'failed' );
        $this->assertSame( 'failed', $migrations[0]->state( $context ) );
        $this->assertSame( array( 'create the table exp_test_wp3_note', 'write the first note' ), $migrations[0]->plan( $context ) );

        $this->assertSame( array( 'A\\B\\One', 'A\\B\\Two' ),
                           expMigrationPhp::classesIn( "<?php\nnamespace A\\B;\nfinal class One extends \\expMigrationPhp {}\n\$x = One::class; \$y = new class {};\nclass Two {}\n" ) );
        $this->assertSame( array( 'Plain' ), expMigrationPhp::classesIn( "<?php\nabstract class Plain {}" ) );

        // a file without a migration class, with two, and a type without a runner
        $broken = new expMigrationLocator( array( array( 'source' => 'site', 'path' => __DIR__ . '/fixtures/runner-broken' ),
                                                  array( 'source' => 'site', 'path' => __DIR__ . '/fixtures/site' ) ) );
        $errors = array();
        $loaded = expMigrationRunner::load( ( new expMigrationGraph( $broken->find() ) )->migrations(), $errors );
        $text = implode( "\n", $errors );
        $this->assertStringContainsString( '20261031140000_no_class: ', $text );
        $this->assertStringContainsString( 'no class extends expMigrationPhp', $text );
        $this->assertStringContainsString( 'more than one class extends expMigrationPhp (ExpTests\\Migrations\\Broken\\FirstOfTwo, ExpTests\\Migrations\\Broken\\SecondOfTwo)', $text );
        // SQL and INI migrations load as the YAML migration of their steps
        $byId = array();
        foreach ( $loaded as $migration )
            $byId[$migration->id()] = $migration;
        $this->assertInstanceOf( 'expMigrationYaml', $byId['20261003100000_news_settings'] );
        $this->assertStringNotContainsString( 'cannot be run yet', $text );
        foreach ( $loaded as $migration )
            $this->assertTrue( $migration instanceof expMigrationPhp || $migration instanceof expMigrationYaml );
    }

    /** RN-12 */
    public function testRollback()
    {
        $context = $this->context();
        $undone = array();
        $make = function ( $id, $reversible ) use ( &$undone ) {
            return new expMigrationRunnerFake( $id, array( 'reversible' => $reversible, 'down' => function () use ( $id, &$undone ) { $undone[] = $id; return array( "undid $id" ); } ) );
        };
        foreach ( array( 'm1', 'm2' ) as $id )
            $context->records["site\n$id"] = array( 'source' => 'site', 'migration_id' => $id, 'state' => 'done', 'checksum' => "checksum-$id", 'run_id' => 'r', 'finished_at' => 1 );
        $report = ( new expMigrationRunner( $context ) )->rollback( array( $make( 'm1', true ), $make( 'm2', true ) ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'm2', 'm1' ), $undone, 'the last first' );
        $this->assertSame( array( 'm2' => 'undone', 'm1' => 'undone' ), self::actions( $report ) );
        $this->assertSame( array(), $context->records );
        $this->assertSame( array( 'system.migration.rollback', 'system.migration.rollback' ), array_column( $context->audits, 0 ) );

        $context = $this->context();
        $context->records["site\nm1"] = array( 'source' => 'site', 'migration_id' => 'm1', 'state' => 'done', 'checksum' => 'checksum-m1', 'run_id' => 'r', 'finished_at' => 1 );
        $report = ( new expMigrationRunner( $context ) )->rollback( array( $make( 'm1', false ), $make( 'm5', true ) ) );
        $this->assertSame( 1, $report['exit'] );
        $this->assertStringContainsString( 'm5 cannot be undone: it is pending', $report['errors'][0] );
        $this->assertStringContainsString( 'm1 cannot be undone by itself (no down())', $report['errors'][1] );
        $this->assertSame( array(), $context->calls );
    }

    /** RN-13 */
    public function testSettings()
    {
        $text = file_get_contents( self::$root . '/settings/migration.ini' );
        $this->assertSame( 1, preg_match( '/^\[RunSettings\]\n(.*?)(?:^\[|\z)/ms', $text, $m ) );
        $settings = array();
        foreach ( explode( "\n", $m[1] ) as $line )
            if ( preg_match( '/^([A-Za-z]+)=(.*)$/', $line, $kv ) )
                $settings[$kv[1]] = $kv[2];
        $this->assertSame( array( 'RunAsUser' => 'admin', 'StopOnFailure' => 'enabled', 'Throttle' => '', 'MaxMigrations' => '', 'MaxTime' => '', 'Nice' => '', 'ClearCaches' => 'enabled', 'Reindex' => 'queue' ), $settings );
        $catalogue = expAuditTaxonomy::catalogue();
        $auditIni = file_get_contents( self::$root . '/settings/audit.ini' );
        foreach ( array( 'run', 'apply', 'fail', 'rollback', 'caption', 'push' ) as $event )
        {
            $this->assertArrayHasKey( "system.migration.$event", $catalogue );
            $this->assertSame( 'system', $catalogue["system.migration.$event"]['channel'] );
            $this->assertSame( 'on', $catalogue["system.migration.$event"]['default'] );
            $this->assertStringContainsString( "\nEnabled[]=system.migration.$event\n", $auditIni );
        }
    }
}
