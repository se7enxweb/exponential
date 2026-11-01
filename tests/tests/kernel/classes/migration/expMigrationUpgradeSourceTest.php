<?php
/**
 * The exp:upgrade bridge without a database: upgrade steps (fakes and the kernel's registry) as migrations of the
 * source "kernel", run by the migration runner with the in-memory context of expMigrationRunnerTest.
 *
 *  UB-01 every step of the registry is a migration of the source kernel, in exp:upgrade's order: id, description,
 *        requires = after(), engines, stage, version, the class file and its checksum; not reversible
 *  UB-02 the state is exp:upgrade's (expUpgrade::stateOf()): present without a record not-needed, with a record done;
 *        missing pending, failed after a failed attempt; a detection that throws unknown; the detail is detect()'s
 *  UB-03 a run applies the pending steps of the stages database and scripts first, then the site's migrations; the
 *        record has the step's stage and version; the step is checked with detect() afterwards
 *  UB-04 a step of the stages code, libraries, finish (and an optional step) is listed but deferred, with the
 *        exp:upgrade command in a warning; it is detected in the mode status (no fetch); a migration requiring it waits
 *  UB-05 no checksum guard and no transaction for a step; a step still missing after apply() failed
 *  UB-06 merge(): a site migration with the id of a step is left out with an error; the graph accepts a step id in
 *        requires when it knows the kernel's ids
 *  UB-07 the kernel's own registry (upgrade.ini) as migrations: every StepList[] class, none missing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerFakes.php';
require_once __DIR__ . '/expMigrationUpgradeSourceFakes.php';

class expMigrationUpgradeSourceTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var string|null */
    private $lockFile;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        $dir = self::$root . '/var/tmp/migration-upgrade-source-test-' . getmypid();
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        $this->lockFile = $dir . '/upgrade.lock';
    }

    protected function tearDown(): void
    {
        expUpgradeLock::release();
        expMigrationLock::installation( $this->lockFile )->release();
        $dir = dirname( $this->lockFile );
        foreach ( glob( $dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $dir );
    }

    /** @return expMigrationRunnerTestContext */
    private function context( $apply = true, array $options = array() )
    {
        return new expMigrationRunnerTestContext( $apply, $options + array( 'lock-file' => $this->lockFile, 'root' => self::$root ) );
    }

    /**
     * @param expMigrationUpgradeFakeStep[] $steps
     * @return expMigrationUpgradeSource
     */
    private static function source( array $steps )
    {
        return new expMigrationUpgradeSource( new expUpgradeRegistry( $steps ) );
    }

    /** @return array id => entry of a report */
    private static function byId( array $report )
    {
        $out = array();
        foreach ( $report['migrations'] as $entry )
            $out[$entry['id']] = $entry;
        return $out;
    }

    /** UB-01 */
    public function testStepsAreKernelMigrationsInUpgradeOrder()
    {
        $scripts = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-script', 'stage' => 'scripts', 'after' => array( '6.0.15-fake-table' ) ) );
        $table = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-table', 'engines' => array( 'mysql', 'sqlite' ), 'version' => '6.0.14' ) );
        $code = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-code', 'stage' => 'code' ) );
        $source = self::source( array( $scripts, $table, $code ) );

        $this->assertSame( array( '6.0.15-fake-code', '6.0.15-fake-table', '6.0.15-fake-script' ), $source->ids(), 'stage first, as exp:upgrade orders them' );
        $this->assertSame( array(), $source->errors() );
        $migrations = $source->migrations();
        $this->assertCount( 3, $migrations );
        foreach ( $migrations as $migration )
        {
            $this->assertInstanceOf( 'expMigration', $migration );
            $this->assertSame( 'kernel', $migration->source() );
            $this->assertSame( array(), $migration->environments() );
            $this->assertFalse( $migration->maintenance() );
            $this->assertFalse( $migration->reversible() );
        }
        $this->assertSame( '6.0.15-fake-script', $migrations[2]->id() );
        $this->assertSame( 'fake step 6.0.15-fake-script', $migrations[2]->description() );
        $this->assertSame( array( '6.0.15-fake-table' ), $migrations[2]->requires() );
        $this->assertSame( array( 'mysql', 'sqlite' ), $migrations[1]->engines() );
        $this->assertSame( 'database', $migrations[1]->stage() );
        $this->assertSame( '6.0.14', $migrations[1]->version() );
        $this->assertSame( $table, $migrations[1]->step() );
        $this->assertSame( realpath( __DIR__ . '/expMigrationUpgradeSourceFakes.php' ), realpath( $migrations[1]->file() ) );
        $this->assertSame( hash_file( 'sha256', __DIR__ . '/expMigrationUpgradeSourceFakes.php' ), $migrations[1]->checksum() );
        try
        {
            $migrations[1]->down( $this->context() );
            $this->fail( 'down() of a step throws' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'restoring a caption', $e->getMessage() );
        }
    }

    /** UB-02 */
    public function testStateIsUpgradeState()
    {
        $context = $this->context( false );
        $step = function ( $effect ) { return new expMigrationUpgradeStep( new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-state', 'effect' => $effect ) ) ); };

        $present = $step( expUpgradeStep::PRESENT );
        $this->assertSame( expMigration::NOT_NEEDED, $present->state( $context ), 'in place without a record' );
        $this->assertSame( 'fake: present', $present->detail() );
        $this->assertSame( expMigration::NOT_NEEDED, $step( expUpgradeStep::NOT_APPLICABLE )->state( $context ) );
        $this->assertSame( expMigration::PENDING, $step( expUpgradeStep::MISSING )->state( $context ) );
        $this->assertSame( expMigration::UNKNOWN, $step( expUpgradeStep::UNKNOWN )->state( $context ) );
        $throws = $step( function () { throw new RuntimeException( 'catalog unreadable' ); } );
        $this->assertSame( expMigration::UNKNOWN, $throws->state( $context ) );
        $this->assertSame( 'detection failed: catalog unreadable', $throws->detail() );

        $context->records["kernel\n6.0.15-fake-state"] = expMigrationRecord::normalize( array( 'source' => 'kernel', 'migration_id' => '6.0.15-fake-state', 'state' => 'done' ), expMigrationRecord::COLUMNS );
        $this->assertSame( expMigration::DONE, $step( expUpgradeStep::PRESENT )->state( $context ), 'in place and recorded' );
        $this->assertSame( expMigration::PENDING, $step( expUpgradeStep::MISSING )->state( $context ), 'recorded done, missing again (drift): pending' );
        $context->records["kernel\n6.0.15-fake-state"]['state'] = 'failed';
        $this->assertSame( expMigration::FAILED, $step( expUpgradeStep::MISSING )->state( $context ) );
    }

    /** UB-03 */
    public function testRunAppliesKernelStepsBeforeSiteMigrations()
    {
        $order = array();
        $table = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-table', 'apply' => null ) );
        $script = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-script', 'stage' => 'scripts', 'after' => array( '6.0.15-fake-table' ) ) );
        $done = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-done', 'effect' => expUpgradeStep::PRESENT ) );
        $site = new expMigrationRunnerFake( '20261101090000_site_note', array( 'requires' => array( '6.0.15-fake-script' ),
                                                                              'up' => function () use ( &$order, $table, $script ) {
                                                                                  $order[] = 'site';
                                                                                  $order[] = in_array( 'apply', $table->calls, true ) && in_array( 'apply', $script->calls, true ) ? 'after-kernel' : 'before-kernel';
                                                                                  return array( 'site done' );
                                                                              } ) );
        $source = self::source( array( $script, $done, $table ) );
        $errors = array();
        $migrations = expMigrationUpgradeSource::merge( $source->migrations(), array( $site ), $errors );
        $this->assertSame( array(), $errors );

        $dry = ( new expMigrationRunner( $this->context( false ) ) )->run( $migrations );
        $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
        $entries = self::byId( $dry );
        $this->assertSame( array( '6.0.15-fake-done', '6.0.15-fake-table', '6.0.15-fake-script', '20261101090000_site_note' ), array_keys( $entries ), 'by stage, then as declared' );
        $this->assertSame( 'would-apply', $entries['6.0.15-fake-table']['action'] );
        $this->assertSame( array( 'would do 6.0.15-fake-table' ), $entries['6.0.15-fake-table']['plan'] );
        $this->assertSame( 'not-needed', $entries['6.0.15-fake-done']['state'] );
        $this->assertSame( 'database', $entries['6.0.15-fake-table']['stage'] );
        $this->assertSame( 'migration', $entries['20261101090000_site_note']['stage'] );
        $this->assertNotContains( 'apply', $table->calls, 'the dry run applied nothing' );

        $context = $this->context();
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 3, $report['summary']['applied'] );
        $this->assertSame( array( 'site', 'after-kernel' ), $order );
        $this->assertSame( array( 'detect:dry-run', 'plan', 'detect:apply', 'apply', 'detect:apply' ), $table->calls, 'detected again after apply()' );
        $row = $context->records["kernel\n6.0.15-fake-script"];
        $this->assertSame( 'done', $row['state'] );
        $this->assertSame( 'scripts', $row['stage'] );
        $this->assertSame( '6.0.15', $row['version'] );
        $this->assertSame( $context->runId(), $row['run_id'] );
        $this->assertSame( hash_file( 'sha256', __DIR__ . '/expMigrationUpgradeSourceFakes.php' ), $row['checksum'] );
        $this->assertSame( 'migration', $context->records["site\n20261101090000_site_note"]['stage'] );
        $this->assertContains( 'kernel upgrade steps were applied: ./console exp:upgrade:finish --apply makes them live (deploy, caches, smoke check)', $report['warnings'] );

        $again = ( new expMigrationRunner( $this->context( true, array() ) ) )->run( $migrations );
        $this->assertSame( 0, $again['exit'] );
    }

    /** UB-04 */
    public function testCodeLibrariesFinishAndOptionalStepsAreDeferred()
    {
        $code = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-code', 'stage' => 'code' ) );
        $libraries = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-libraries', 'stage' => 'libraries' ) );
        $optional = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-optional', 'optional' => true ) );
        $finish = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-finish', 'stage' => 'finish' ) );
        $source = self::source( array( $code, $libraries, $optional, $finish ) );
        $waits = new expMigrationRunnerFake( '20261101090000_needs_libraries', array( 'requires' => array( '6.0.15-fake-libraries' ) ) );
        $free = new expMigrationRunnerFake( '20261101090100_free' );
        $migrations = expMigrationUpgradeSource::merge( $source->migrations(), array( $waits, $free ) );

        $context = $this->context();
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $entries = self::byId( $report );
        foreach ( array( '6.0.15-fake-code', '6.0.15-fake-libraries', '6.0.15-fake-optional', '6.0.15-fake-finish' ) as $id )
        {
            $this->assertSame( 'pending', $entries[$id]['state'], $id );
            $this->assertSame( 'deferred', $entries[$id]['action'], $id );
        }
        $this->assertSame( 4, $report['summary']['deferred'] );
        $this->assertContains( '6.0.15-fake-code: pending, not run by a migration run: an upgrade step of the stage code: ./console exp:upgrade:code --apply', $report['warnings'] );
        $this->assertContains( '6.0.15-fake-optional: pending, not run by a migration run: an optional upgrade step: ./console exp:upgrade --apply --only=6.0.15-fake-optional', $report['warnings'] );
        $this->assertSame( array( 'detect:status' ), $code->calls, 'detected in the mode status: the code stage does not fetch' );
        $this->assertSame( 'blocked', $entries['20261101090000_needs_libraries']['action'] );
        $this->assertSame( array( 'waits for 6.0.15-fake-libraries' ), $entries['20261101090000_needs_libraries']['messages'] );
        $this->assertSame( 'applied', $entries['20261101090100_free']['action'] );
        $this->assertSame( 0, $report['exit'], 'a deferred step is not a failure' );
        $this->assertArrayNotHasKey( "kernel\n6.0.15-fake-code", $context->records );

        try
        {
            ( new expMigrationUpgradeStep( $code ) )->up( $context );
            $this->fail( 'up() of a code step throws' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'exp:upgrade:code', $e->getMessage() );
        }
    }

    /** UB-05 */
    public function testNoGuardNoTransactionAndCheckedAfterApply()
    {
        $step = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-guard', 'effect' => expUpgradeStep::PRESENT ) );
        $context = $this->context( true, array( 'engine' => 'postgresql' ) );
        $context->records["kernel\n6.0.15-fake-guard"] = expMigrationRecord::normalize( array( 'source' => 'kernel', 'migration_id' => '6.0.15-fake-guard',
                                                                                              'state' => 'done', 'checksum' => str_repeat( 'f', 64 ) ), expMigrationRecord::COLUMNS );
        $report = ( new expMigrationRunner( $context ) )->run( self::source( array( $step ) )->migrations() );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertFalse( $report['migrations'][0]['changed'], 'another checksum of the class file is no change of a migration' );
        $this->assertSame( 'done', $report['migrations'][0]['state'] );

        $stuck = new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-stuck', 'apply' => function () { return array( 'tried' ); } ) );
        $context = $this->context( true, array( 'engine' => 'postgresql' ) );
        $report = ( new expMigrationRunner( $context ) )->run( self::source( array( $stuck ) )->migrations() );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( 'failed', $report['migrations'][0]['state'] );
        $this->assertSame( 'none', $report['migrations'][0]['transaction'], 'no transaction around a step' );
        $this->assertNotContains( 'begin', $context->calls );
        $this->assertStringContainsString( 'applied, but detection still says missing', end( $report['migrations'][0]['messages'] ) );
        $this->assertSame( 'failed', $context->records["kernel\n6.0.15-fake-stuck"]['state'] );
    }

    /** UB-06 */
    public function testMergeAndGraphRequirements()
    {
        $source = self::source( array( new expMigrationUpgradeFakeStep( array( 'id' => '6.0.15-fake-table' ) ) ) );
        $clash = new expMigrationRunnerFake( '6.0.15-fake-table' );
        $errors = array();
        $merged = expMigrationUpgradeSource::merge( $source->migrations(), array( $clash ), $errors );
        $this->assertCount( 1, $merged );
        $this->assertSame( 'kernel', $merged[0]->source() );
        $this->assertSame( array( 'the migration 6.0.15-fake-table (site) has the id of a kernel upgrade step and is left out' ), $errors );

        $file = dirname( $this->lockFile ) . '/20261101090000_after_kernel.sql';
        file_put_contents( $file, "-- requires: 6.0.15-migration-tables\n" );
        $definition = new expMigrationDefinition( '20261101090000_after_kernel', 'site', 'sql' );
        $definition->addFile( $file );
        $this->assertSame( array( '6.0.15-migration-tables' ), $definition->requiredMigrations() );
        $unknown = new expMigrationGraph( array( $definition ) );
        $this->assertSame( array( 'the migration 20261101090000_after_kernel requires 6.0.15-migration-tables, which does not exist' ), $unknown->errors() );
        $graph = new expMigrationGraph( array( $definition ), array( 'kernel' => array( '6.0.15-migration-tables' ), 'environment' => 'testing' ) );
        $this->assertSame( array(), $graph->errors(), 'a step id is met: the steps run first' );
        $this->assertSame( array( '20261101090000_after_kernel' ), $graph->ids() );
    }

    /** UB-07 */
    public function testKernelRegistryAsMigrations()
    {
        $source = expMigrationUpgradeSource::fromSettings();
        $this->assertSame( array(), $source->errors() );
        $ini = eZINI::instance( 'upgrade.ini' );
        $classes = array_values( array_filter( (array)$ini->variable( 'UpgradeSettings', 'StepList' ), 'strlen' ) );
        $this->assertCount( count( $classes ), $source->migrations() );
        $ids = $source->ids();
        $this->assertSame( '6.0.15-upgrade-record-table', $ids[array_search( '6.0.15-upgrade-record-table', $ids, true )] );
        $this->assertLessThan( array_search( '6.0.15-upgrade-record-table', $ids, true ), array_search( '6.0.15-migration-tables', $ids, true ), 'the drop of expupgrade_step after the tables it copies into' );
        $runnable = 0;
        foreach ( $source->migrations() as $migration )
        {
            $this->assertSame( 'kernel', $migration->source() );
            $this->assertNotSame( '', $migration->file(), $migration->id() . ': the class file' );
            if ( $migration->runnable() )
                $runnable++;
            else
                $this->assertContains( $migration->stage(), array( 'code', 'libraries', 'finish' ), $migration->id() );
        }
        $this->assertGreaterThan( 30, $runnable, 'the database and scripts steps' );
    }
}
