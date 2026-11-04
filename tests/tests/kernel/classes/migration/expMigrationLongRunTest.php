<?php
/**
 * Long runs, runs at installation and in a deploy, without a database (the runner's in-memory context of
 * expMigrationRunnerFakes, processes replaced through expMigrationProcess::setRunner()).
 *
 *  LR-01 --max-migrations: the first n migrations applied, the rest postponed (pending, a warning, exit 0), the next run goes on
 *  LR-02 --max-time: no further migration starts once the time is up (the first always runs); a failure still stops
 *  LR-03 --throttle: a pause between two migrations, never before the first; settings and options, nice levels
 *  LR-04 expMigrationCheckpoint: set, get, clear, written whole, a dry run writes nothing, ids that are no file name
 *  LR-05 expMigrationPhp::batches(): a transaction per batch, the checkpoint after each, a failed run goes on from the
 *        last committed batch, a transactional migration is refused, the runner clears the checkpoint once applied
 *  LR-06 expMigrationInstall: the package's migrations directory, the command line, the report read back, a failure
 *        and a disabled setting, the kickstarter's run
 *  LR-07 exp:velocity deploy --migrate: the setting and the options decide; the step runs before PHP-FPM and Velocity
 *  LR-08 background through expqueue: Background=process never queues; without expqueue a queued job has ended;
 *        runQueued() refuses a job it cannot run; the long-run options pass from the administration to the command
 *  LR-09 settings: migration.ini [RunSettings] Limit, MaxTime, Nice, [InstallSettings], [AdminSettings] Background;
 *        velocity.ini [DeploySettings] Migrate=disabled
 *  LR-10 a run at installation makes the installer read the default object states again (a state group the run added
 *        reaches the objects the installer creates afterwards)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerFakes.php';

/** A PHP migration that works through $items in batches. */
class expMigrationLongRunBatchFake extends expMigrationPhp
{
    public $items = array();
    public $handled = array();
    public $failAt = null;
    public $transactional = false;
    public $size = 2;

    public function transactional()
    {
        return $this->transactional;
    }

    public function up( $context )
    {
        $self = $this;
        $count = $this->batches( $context,
            function ( $offset, $limit ) use ( $self ) {
                return array_slice( $self->items, $offset, $limit );
            },
            function ( $item ) use ( $self ) {
                if ( $self->failAt !== null && $item === $self->failAt )
                    throw new RuntimeException( "item $item failed" );
                $self->handled[] = $item;
            }, $this->size );
        return array( "$count handled" );
    }
}

/** migration.ini (or velocity.ini) as a list of values. */
class expMigrationLongRunIni
{
    private $values;

    public function __construct( array $values )
    {
        $this->values = $values;
    }

    public function hasVariable( $group, $name )
    {
        return isset( $this->values["$group/$name"] );
    }

    public function variable( $group, $name )
    {
        return $this->values["$group/$name"];
    }
}

class expMigrationLongRunTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var string */
    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        $this->dir = self::$root . '/var/tmp/migration-long-run-test-' . getmypid();
        if ( !is_dir( $this->dir ) )
            mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        expMigrationProcess::setRunner( null );
        expMigrationLock::installation( $this->dir . '/upgrade.lock' )->release();
        $this->remove( $this->dir );
    }

    private function remove( $dir )
    {
        foreach ( (array)glob( $dir . '/{,.}*', GLOB_BRACE ) as $path )
        {
            if ( in_array( basename( $path ), array( '.', '..' ), true ) )
                continue;
            is_dir( $path ) ? $this->remove( $path ) : unlink( $path );
        }
        if ( is_dir( $dir ) )
            rmdir( $dir );
    }

    private function context( $apply = true, array $options = array() )
    {
        return new expMigrationRunnerTestContext( $apply, $options + array( 'lock-file' => $this->dir . '/upgrade.lock',
                                                                            'checkpoint-directory' => $this->dir . '/checkpoints' ) );
    }

    private static function actions( array $report )
    {
        $out = array();
        foreach ( $report['migrations'] as $entry )
            $out[$entry['id']] = $entry['action'];
        return $out;
    }

    private function fakes( array $ids, array &$ran )
    {
        $out = array();
        foreach ( $ids as $id )
            $out[] = new expMigrationRunnerFake( $id, array( 'up' => function () use ( $id, &$ran ) { $ran[] = $id; return array(); } ) );
        return $out;
    }

    private function batchFake( array $items )
    {
        $migration = new expMigrationLongRunBatchFake();
        $migration->setDefinition( new expMigrationDefinition( '20261101100000_batch', 'site', 'php' ) );
        $migration->items = $items;
        return $migration;
    }

    /** LR-01 */
    public function testLimitPostponesTheRest()
    {
        $ran = array();
        $context = $this->context();
        $report = ( new expMigrationRunner( $context, array( 'max-migrations' => 2 ) ) )->run( $this->fakes( array( 'm1', 'm2', 'm3', 'm4' ), $ran ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'm1', 'm2' ), $ran );
        $this->assertSame( array( 'm1' => 'applied', 'm2' => 'applied', 'm3' => 'postponed', 'm4' => 'postponed' ), self::actions( $report ) );
        $this->assertSame( 2, $report['summary']['postponed'] );
        $this->assertSame( 2, $report['summary']['pending'], 'postponed migrations stay pending' );
        $this->assertStringContainsString( '2 migrations postponed by the limit of 2 migrations per run', implode( "\n", $report['warnings'] ) );
        $this->assertSame( array( 'running', 'done' ), array_column( $context->runs, 'state' ) );

        // the next run goes on with them
        $next = $this->context();
        $next->records = $context->records;
        $report = ( new expMigrationRunner( $next, array( 'max-migrations' => 2 ) ) )->run( $this->fakes( array( 'm1', 'm2', 'm3', 'm4' ), $ran ) );
        $this->assertSame( array( 'm1', 'm2', 'm3', 'm4' ), $ran );
        $this->assertSame( 0, $report['summary']['postponed'] );
        $this->assertSame( array(), $report['warnings'] );
    }

    /** LR-02 */
    public function testMaxTime()
    {
        $now = 1000.0;
        $ran = array();
        $migrations = array();
        foreach ( array( 'm1', 'm2', 'm3' ) as $id )
            $migrations[] = new expMigrationRunnerFake( $id, array( 'up' => function () use ( $id, &$ran, &$now ) { $ran[] = $id; $now += 40; return array(); } ) );
        $clock = function () use ( &$now ) { return $now; };
        $report = ( new expMigrationRunner( $this->context(), array( 'max-time' => 60, 'clock' => $clock ) ) )->run( $migrations );
        $this->assertSame( array( 'm1', 'm2' ), $ran, 'm2 starts at 40 s, m3 would start at 80 s' );
        $this->assertSame( 'postponed', self::actions( $report )['m3'] );
        $this->assertStringContainsString( 'the time limit of 60 seconds', implode( "\n", $report['warnings'] ) );

        // the first migration always runs, however long the time limit has been up
        $now = 1000.0;
        $ran = array();
        $report = ( new expMigrationRunner( $this->context(), array( 'max-time' => 1, 'clock' => $clock ) ) )->run( $migrations );
        $this->assertSame( array( 'm1' ), $ran );

        // a failure still stops the run: not-run, not postponed
        $ran = array();
        $failing = array( new expMigrationRunnerFake( 'f1', array( 'up' => function () { throw new RuntimeException( 'no' ); } ) ),
                          new expMigrationRunnerFake( 'f2' ) );
        $report = ( new expMigrationRunner( $this->context(), array( 'max-migrations' => 5 ) ) )->run( $failing );
        $this->assertSame( array( 'f1' => 'failed', 'f2' => 'not-run' ), self::actions( $report ) );
        $this->assertSame( 1, $report['exit'] );
    }

    /** LR-03 */
    public function testThrottleAndSettings()
    {
        $pauses = array();
        $ran = array();
        $sleep = function ( $seconds ) use ( &$pauses ) { $pauses[] = $seconds; };
        ( new expMigrationRunner( $this->context(), array( 'throttle' => 0.25, 'sleep' => $sleep ) ) )->run( $this->fakes( array( 'm1', 'm2', 'm3' ), $ran ) );
        $this->assertSame( array( 0.25, 0.25 ), $pauses, 'between two migrations, not before the first' );

        $this->assertSame( 0.5, expMigrationRunner::throttleSeconds( '0.5' ) );
        $this->assertSame( 60.0, expMigrationRunner::throttleSeconds( 600 ) );
        $this->assertSame( 0.0, expMigrationRunner::throttleSeconds( '' ) );
        $this->assertSame( 0.0, expMigrationRunner::throttleSeconds( 'fast' ) );
        $this->assertSame( 0.0, expMigrationRunner::throttleSeconds( -3 ) );
        $this->assertSame( 10, expMigrationRunner::niceLevel( '10' ) );
        $this->assertSame( 19, expMigrationRunner::niceLevel( 40 ) );
        $this->assertSame( 0, expMigrationRunner::niceLevel( '' ) );
        $this->assertSame( 0, expMigrationRunner::niceLevel( '2.5' ) );

        $ini = new expMigrationLongRunIni( array( 'RunSettings/StopOnFailure' => 'enabled', 'RunSettings/Throttle' => '1.5', 'RunSettings/MaxMigrations' => '20',
                                                  'RunSettings/MaxTime' => '', 'RunSettings/Nice' => '10' ) );
        $options = expMigrationRunner::settingsOptions( array(), $ini );
        $this->assertSame( array( 'stop-on-failure' => true, 'max-migrations' => 20, 'max-time' => 0, 'throttle' => 1.5, 'nice' => 10 ), $options );
        $options = expMigrationRunner::settingsOptions( array( 'max-migrations' => 3, 'throttle' => 0, 'nice' => 5 ), $ini );
        $this->assertSame( 3, $options['max-migrations'], 'an option wins over the setting' );
        $this->assertSame( 0.0, $options['throttle'] );
        $this->assertSame( 5, $options['nice'] );
        $this->assertFalse( expMigrationRunner::applyNice( 0 ), 'level 0 leaves the priority alone' );
    }

    /** LR-04 */
    public function testCheckpoint()
    {
        $checkpoint = new expMigrationCheckpoint( '20261101100000_batch', $this->dir . '/checkpoints' );
        $this->assertFalse( $checkpoint->exists() );
        $this->assertSame( 7, $checkpoint->get( 'offset', 7 ) );
        $this->assertTrue( $checkpoint->set( array( 'offset' => 40, 'last' => 'x' ) ) );
        $this->assertTrue( $checkpoint->set( array( 'offset' => 60 ) ) );
        $again = new expMigrationCheckpoint( '20261101100000_batch', $this->dir . '/checkpoints' );
        $this->assertSame( array( 'offset' => 60, 'last' => 'x' ), $again->values() );
        $this->assertSame( array(), glob( $this->dir . '/checkpoints/*.tmp' ), 'written whole: no temporary file left' );
        $again->clear();
        $this->assertFileDoesNotExist( $this->dir . '/checkpoints/20261101100000_batch.json' );

        $dry = new expMigrationCheckpoint( '20261101100000_batch', $this->dir . '/checkpoints', false );
        $this->assertTrue( $dry->set( array( 'offset' => 5 ) ) );
        $this->assertSame( 5, $dry->get( 'offset' ) );
        $this->assertFileDoesNotExist( $this->dir . '/checkpoints/20261101100000_batch.json', 'a dry run writes nothing' );

        $context = $this->context( false );
        $this->assertSame( $this->dir . '/checkpoints/m1.json', expMigrationCheckpoint::of( 'm1', $context )->file() );
        foreach ( array( '../etc', '', '.hidden', 'a/b' ) as $bad )
        {
            try
            {
                new expMigrationCheckpoint( $bad, $this->dir );
                $this->fail( "$bad is no migration id" );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'not a migration id', $e->getMessage() );
            }
        }
    }

    /** LR-05 */
    public function testBatchesGoOnFromTheCheckpoint()
    {
        $file = $this->dir . '/checkpoints/20261101100000_batch.json';
        $migration = $this->batchFake( array( 'a', 'b', 'c', 'd', 'e' ) );
        $migration->failAt = 'd';
        $context = $this->context( true, array( 'throttle' => 0.1, 'sleep' => function () {} ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( $migration ) );
        $this->assertSame( 'failed', self::actions( $report )['20261101100000_batch'] );
        $this->assertSame( array( 'a', 'b', 'c' ), $migration->handled );
        $this->assertSame( 2, ( new expMigrationCheckpoint( '20261101100000_batch', $this->dir . '/checkpoints' ) )->get( 'offset' ),
                           'the batch a, b was committed; c, d was rolled back' );
        $this->assertSame( array( 'begin', 'commit', 'begin', 'rollback' ),
                           array_values( array_intersect( $context->calls, array( 'begin', 'commit', 'rollback' ) ) ),
                           'a transaction per batch, none around the migration' );

        // the next run goes on with c
        $migration->failAt = null;
        $migration->handled = array();
        $next = $this->context( true, array( 'sleep' => function () {} ) );
        $next->records = $context->records;
        $report = ( new expMigrationRunner( $next ) )->run( array( $migration ) );
        $this->assertSame( 'applied', self::actions( $report )['20261101100000_batch'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'c', 'd', 'e' ), $migration->handled );
        $this->assertContains( '3 handled', $report['migrations'][0]['messages'] );
        $this->assertFileDoesNotExist( $file, 'applied: the checkpoint is removed' );

        // a dry run handles nothing
        $dry = $this->batchFake( array( 'a' ) );
        $this->assertSame( array( '0 handled' ), $dry->up( $this->context( false ) ) );
        $this->assertSame( array(), $dry->handled );

        // batches() commits on its own: a transactional migration may not use it
        $transactional = $this->batchFake( array( 'a' ) );
        $transactional->transactional = true;
        $report = ( new expMigrationRunner( $this->context() ) )->run( array( $transactional ) );
        $this->assertSame( 'failed', self::actions( $report )['20261101100000_batch'] );
        $this->assertStringContainsString( 'transactional() must return false', implode( "\n", $report['migrations'][0]['messages'] ) );
    }

    /** LR-06 */
    public function testInstall()
    {
        $package = $this->dir . '/packages/news';
        mkdir( $package . '/migrations', 0775, true );
        $ini = new expMigrationLongRunIni( array( 'MigrationSettings/ExtensionDirectory' => 'migrations' ) );
        file_put_contents( $package . '/migrations/README.md', 'notes' );
        $this->assertSame( '', expMigrationInstall::packageDirectory( $package, $ini ), 'no migration file' );
        file_put_contents( $package . '/migrations/20261101120000_news_class.yml', "- type: reference\n" );
        $this->assertSame( $package . '/migrations', expMigrationInstall::packageDirectory( $package, $ini ) );
        $this->assertSame( '', expMigrationInstall::packageDirectory( $this->dir . '/packages/none', $ini ) );

        $this->assertSame( array( '--apply', '--format=json', '--path=var/p/news/migrations', '--include-uncommitted', '--no-push', '--login=editor',
                                  '--siteaccess=admin', '--allow-root-user' ),
                           expMigrationInstall::arguments( array( 'var/p/news/migrations' ), array( 'include-uncommitted', 'no-push', 'rm -rf' ), 'editor', 'admin', true ) );
        $this->assertSame( array( '--apply', '--format=json' ), expMigrationInstall::arguments( array(), array() ) );

        $settings = expMigrationInstall::settings( new expMigrationLongRunIni( array( 'InstallSettings/RunOnPackageInstall' => 'disabled',
            'InstallSettings/RunOptions' => array( 'no-push', 'no-push', 'unknown' ), 'InstallSettings/Timeout' => '0' ) ) );
        $this->assertSame( array( 'package' => false, 'kickstart' => true, 'options' => array( 'no-push' ), 'timeout' => 3600 ), $settings );

        $calls = array();
        $report = array( 'schema' => 'exp-migration/1', 'run_id' => '20261101-120000-abcdef', 'summary' => array( 'applied' => 1, 'failed' => 0 ),
                         'errors' => array(), 'exit' => 0 );
        expMigrationProcess::setRunner( function ( $command, $cwd, $timeout ) use ( &$calls, &$report ) {
            $calls[] = array( $command, $cwd, $timeout );
            return array( (int)$report['exit'], "a line first {}\n" . json_encode( $report, JSON_PRETTY_PRINT ) );
        } );
        $base = array( 'package' => true, 'kickstart' => true, 'options' => array( 'no-push' ), 'timeout' => 60, 'root' => $this->dir, 'login' => 'editor',
                       'siteaccess' => '' );
        $parameters = array();
        $this->assertTrue( expMigrationInstall::afterPackageInstall( $package, 'news', $parameters, $base ) );
        $this->assertCount( 1, $calls );
        $this->assertSame( expMigrationInstall::SCRIPT, $calls[0][0][1] );
        $this->assertContains( '--path=' . $package . '/migrations', $calls[0][0] );
        $this->assertContains( '--login=editor', $calls[0][0] );
        $this->assertSame( array( $this->dir, 60 ), array( $calls[0][1], $calls[0][2] ) );
        $this->assertSame( '20261101-120000-abcdef', $parameters['migration-report']['run_id'] );

        // a failed run: the install fails
        $report = array( 'schema' => 'exp-migration/1', 'run_id' => '20261101-120001-abcdef', 'summary' => array( 'applied' => 0, 'failed' => 1 ),
                         'errors' => array( '20261101120000_news_class failed: no class' ), 'exit' => 1 );
        $this->assertFalse( expMigrationInstall::afterPackageInstall( $package, 'news', $parameters, $base ) );
        $result = expMigrationInstall::run( array(), $base );
        $this->assertSame( '0 migrations applied, 1 failed (run 20261101-120001-abcdef): 20261101120000_news_class failed: no class', $result['message'] );

        // nothing to run: no process
        $calls = array();
        $this->assertTrue( expMigrationInstall::afterPackageInstall( $this->dir . '/packages/none', 'none', $parameters, $base ) );
        $this->assertTrue( expMigrationInstall::afterPackageInstall( $package, 'news', $parameters, array( 'package' => false ) + $base ) );
        $this->assertNull( expMigrationInstall::afterKickstart( array( 'kickstart' => false ) + $base ) );
        $this->assertSame( array(), $calls );

        // the kickstarter: every pending migration, no --path
        $report['exit'] = 0;
        $report['summary'] = array( 'applied' => 3, 'failed' => 0 );
        $report['errors'] = array();
        $result = expMigrationInstall::afterKickstart( array( 'login' => '' ) + $base );
        $this->assertTrue( $result['ok'] );
        $this->assertSame( array(), preg_grep( '/^--path=/', $calls[0][0] ) );
        $this->assertSame( '3 migrations applied (run 20261101-120001-abcdef)', $result['message'] );

        // a run that printed no report
        $this->assertStringStartsWith( 'the migration run printed no report (exit 255): ', expMigrationInstall::message( 255, null, "PHP Fatal error: x" ) );
    }

    /** LR-07 */
    /** LR-10 */
    public function testInstallRunForgetsTheDefaultObjectStatesOfThisProcess()
    {
        $defaults = new ReflectionProperty( 'eZContentObjectState', 'Defaults' );
        if ( PHP_VERSION_ID < 80100 )
            $defaults->setAccessible( true );
        // what an installer read before the run: the states of before (the run adds a state group in its own process)
        $defaults->setValue( null, array( 'the default states of before' ) );
        $report = array( 'schema' => 'exp-migration/1', 'run_id' => '20261104-044106-b2f79b', 'summary' => array( 'applied' => 1, 'failed' => 0 ),
                         'errors' => array(), 'exit' => 0 );
        expMigrationProcess::setRunner( function () use ( $report ) {
            return array( 0, json_encode( $report ) );
        } );
        $result = expMigrationInstall::run( array(), array( 'package' => true, 'kickstart' => true, 'options' => array(), 'timeout' => 60,
                                                            'root' => $this->dir ) );
        $this->assertTrue( $result['ok'] );
        $this->assertNull( $defaults->getValue(), 'the next object of the installer reads the default states again' );
    }

    public function testDeployMigrate()
    {
        $off = new expMigrationLongRunIni( array( 'DeploySettings/Migrate' => 'disabled' ) );
        $on = new expMigrationLongRunIni( array( 'DeploySettings/Migrate' => 'enabled' ) );
        $this->assertFalse( expVelocityDeploy::migrateEnabled( null, $off ) );
        $this->assertFalse( expVelocityDeploy::migrateEnabled( null, new expMigrationLongRunIni( array() ) ) );
        $this->assertTrue( expVelocityDeploy::migrateEnabled( null, $on ) );
        $this->assertTrue( expVelocityDeploy::migrateEnabled( true, $off ), '--migrate wins' );
        $this->assertFalse( expVelocityDeploy::migrateEnabled( false, $on ), '--no-migrate wins' );

        $source = file_get_contents( self::$root . '/kernel/classes/expvelocitydeploy.php' );
        $steps = substr( $source, strpos( $source, 'public function steps()' ) );
        $this->assertLessThan( strpos( $steps, '$this->archiveStep()' ), strpos( $steps, '$this->migrateStep()' ),
                               'the migrations run before the engine archive, PHP-FPM and Velocity' );
        $this->assertGreaterThan( strpos( $steps, 'extraCacheSteps()' ), strpos( $steps, '$this->migrateStep()' ),
                                  'and after the caches of settings and templates' );
        $command = file_get_contents( self::$root . '/kernel/private/classes/commands/velocity.php' );
        $this->assertStringContainsString( '[migrate][no-migrate]', $command );
    }

    /** LR-08 */
    public function testBackground()
    {
        $this->assertSame( '', expMigrationJob::queueType( new expMigrationLongRunIni( array( 'AdminSettings/Background' => 'process' ) ) ) );
        if ( !class_exists( 'expQueue' ) )
        {
            $this->assertSame( 'expqueue is not active', expMigrationJob::queueProblem( 'migration.run' ) );
            $this->assertSame( '', expMigrationJob::queueType( new expMigrationLongRunIni( array( 'AdminSettings/Background' => 'queue' ) ) ),
                               'queue asked, but expqueue cannot take it: a process' );
            $this->assertFalse( expMigrationJob::queueWaiting( 'f0f0' ) );
        }

        // a job handed to the queue that never started (or a queue that is gone) has ended, without a report
        $jobs = $this->dir . '/jobs';
        mkdir( $jobs, 0775, true );
        $id = '20261101-130000-abcdef';
        file_put_contents( "$jobs/$id.json", json_encode( array( 'id' => $id, 'kind' => 'migrate', 'pid' => 0, 'queue' => 'f0f0',
                                                                 'arguments' => array( 'bin/php/expmigrationmigrate.php', '--apply' ) ) ) );
        $job = expMigrationJob::get( $id, $jobs );
        if ( !class_exists( 'expQueue' ) )
        {
            $this->assertFalse( $job['queued'] );
            $this->assertTrue( $job['ended'] );
        }

        // runQueued() runs only the commands of a job
        $this->assertSame( expUpgrade::EXIT_USAGE, expMigrationJob::runQueued( 'nonsense', null, array( 'directory' => $jobs ) ) );
        $this->assertSame( expUpgrade::EXIT_USAGE, expMigrationJob::runQueued( '20261101-130001-abcdef', null, array( 'directory' => $jobs ) ), 'no such job' );
        file_put_contents( "$jobs/20261101-130002-abcdef.json", json_encode( array( 'id' => '20261101-130002-abcdef', 'arguments' => array( '/bin/sh', '-c', 'id' ) ) ) );
        $this->assertSame( expUpgrade::EXIT_USAGE, expMigrationJob::runQueued( '20261101-130002-abcdef', null, array( 'directory' => $jobs ) ) );

        // the administration passes the long-run options on
        $arguments = expMigrationJob::arguments( 'migrate', $id, array( 'max-migrations' => 10, 'max-time' => 600, 'throttle' => '0.5' ), 'admin', '', '/p' );
        $this->assertContains( '--max-migrations=10', $arguments );
        $this->assertContains( '--max-time=600', $arguments );
        $this->assertContains( '--throttle=0.5', $arguments );
    }

    /** LR-09 */
    public function testSettings()
    {
        $ini = file_get_contents( self::$root . '/settings/migration.ini' );
        foreach ( array( 'MaxMigrations=', 'MaxTime=', 'Nice=', 'Throttle=', '[InstallSettings]', 'RunOnPackageInstall=enabled', 'RunOnKickstart=enabled',
                         'RunOptions[]=include-uncommitted', 'RunOptions[]=no-push', 'Timeout=3600', 'Background=process', 'QueueJobType=migration.run' ) as $line )
            $this->assertStringContainsString( "\n$line", $ini );
        $this->assertStringContainsString( "\nMigrate=disabled\n", file_get_contents( self::$root . '/settings/velocity.ini' ) );
        $package = file_get_contents( self::$root . '/kernel/classes/ezpackage.php' );
        $this->assertStringContainsString( 'expMigrationInstall::afterPackageInstall(', $package );
        $this->assertStringContainsString( 'expMigrationInstall::afterKickstart(', file_get_contents( self::$root . '/kernel/classes/expkickstarter.php' ) );
    }
}
