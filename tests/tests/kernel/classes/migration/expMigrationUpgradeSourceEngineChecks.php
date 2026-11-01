<?php
/**
 * The checks of the exp:upgrade bridge that run on every engine the same way: SQLite in process
 * (expMigrationUpgradeSourceSqliteTest), MySQL/MariaDB and PostgreSQL in the database job
 * (expMigrationUpgradeSourceDatabaseTest), MongoDB in the live MongoDB suite (expMigrationUpgradeSourceMongoDBTest).
 * Used next to expMigrationRunnerEngineChecks (its connection set-up, fixtures and helpers).
 *
 * The steps are the kernel's real 6.0.15-upgrade-record-table and 6.0.15-migration-tables and a fake database step
 * that creates the table exp_test_wp8_kernel; the site migrations are the fixtures of fixtures/runner.
 *
 *  UE-1 the status lists kernel steps and site migrations together, the steps first: a row of expupgrade_step not
 *       copied yet makes 6.0.15-migration-tables pending, the fake step is pending, the record table step is done
 *       (recorded) or not-needed; exp:upgrade's status says the same of the same steps
 *  UE-2 migrate: the pending kernel steps run first (the row copied, the table created), then the site migrations;
 *       the record has the steps under the source kernel with their stage and version; exp:upgrade's status now says
 *       done for them; a second run has nothing to do and copies nothing again (idempotent where both tables exist)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationUpgradeSourceFakes.php';

trait expMigrationUpgradeSourceEngineChecks
{
    /** @return array the definition of the fake step's table in the format of share/db_schema.dba */
    protected static function bridgeTableSchema()
    {
        return array( 'exp_test_wp8_kernel' => array(
            'name' => 'exp_test_wp8_kernel',
            'fields' => array( 'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
    }

    /** @return expMigrationUpgradeSource the two record steps and the fake table step */
    protected function bridgeSource()
    {
        $table = new expMigrationUpgradeFakeStep( array(
            'id' => '6.0.15-wp8-kernel-table',
            'after' => array( '6.0.15-migration-tables' ),
            'effect' => function ( expUpgradeContext $context ) {
                expKernelTableSchema::reset();
                $exists = expKernelTableSchema::exists( 'exp_test_wp8_kernel', $context->db(), true );
                return array( 'effect' => $exists ? expUpgradeStep::PRESENT : expUpgradeStep::MISSING, 'detail' => $exists ? 'table there' : 'missing: table exp_test_wp8_kernel' );
            },
            'apply' => function ( expUpgradeContext $context ) {
                $messages = array();
                if ( !expKernelTableSchema::install( array( 'exp_test_wp8_kernel' ), $context->db(), self::bridgeTableSchema(), $messages ) )
                    throw new RuntimeException( implode( '; ', $messages ) );
                return array( 'created exp_test_wp8_kernel' );
            },
        ) );
        return new expMigrationUpgradeSource( new expUpgradeRegistry( array( new expUpgradeStepRecordTable(), new expUpgradeStepMigrationTables(), $table ) ) );
    }

    /** @return array the options of a status or run of the bridge in runnerDir() */
    protected function bridgeOptions( $db, array $extra = array() )
    {
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => __DIR__ . '/fixtures/runner' ) ) );
        return $extra + array( 'db' => $db, 'lock-file' => $this->runnerDir() . '/upgrade.lock', 'repository' => $this->runnerDir() . '/migrations',
                               'environment' => 'testing', 'root' => self::runnerRoot(), 'user' => eZUser::fetchByName( 'admin' ),
                               'upgrade-source' => $this->bridgeSource(), 'locator' => $locator, 'only' => self::bridgeIds() );
    }

    /** @return string[] the steps and migrations of the checks, in the order they run */
    protected static function bridgeIds()
    {
        return array( '6.0.15-upgrade-record-table', '6.0.15-migration-tables', '6.0.15-wp8-kernel-table', '20261031130000_wp3_note_table' );
    }

    /**
     * expupgrade_step with a row that exp_migration does not have; no fake table, no record of the fixtures.
     *
     * @param eZDBInterface $db
     */
    protected function bridgePrepare( $db )
    {
        $this->dropRunnerTable( $db, 'exp_test_wp3_note' );
        $this->dropRunnerTable( $db, 'exp_test_wp8_kernel' );
        $this->clearRunnerRecord( $db );
        $messages = array();
        $schema = expKernelTableSchema::read( self::runnerRoot() . '/share/db_schema.dba' );
        $this->assertTrue( expKernelTableSchema::install( array( expMigrationRecord::LEGACY_TABLE ), $db, $schema, $messages ), implode( '; ', $messages ) );
        expKernelTableSchema::reset();
        $this->bridgeClearRows( $db );
        $db->query( 'INSERT INTO ' . expMigrationRecord::LEGACY_TABLE . ' ( ' . implode( ', ', expMigrationRecord::LEGACY_COLUMNS ) . " ) VALUES "
                    . "( '6.0.15-wp8-legacy', 'database', '6.0.15', 'done', 1761900000, 1500, 'cli root', 'sqlite', '', 'from expupgrade_step' )" );
        $db->query( 'INSERT INTO ' . expMigrationRecord::LEGACY_TABLE . ' ( ' . implode( ', ', expMigrationRecord::LEGACY_COLUMNS ) . " ) VALUES "
                    . "( '6.0.15-upgrade-record-table', 'database', '6.0.15', 'done', 1761900000, 10, 'cli root', 'sqlite', '', '' )" );
        expKernelTableSchema::reset();
    }

    /** Removes what the checks wrote. */
    protected function bridgeCleanUp( $db )
    {
        $this->dropRunnerTable( $db, 'exp_test_wp3_note' );
        $this->dropRunnerTable( $db, 'exp_test_wp8_kernel' );
        $this->clearRunnerRecord( $db );
        $this->bridgeClearRows( $db );
        expKernelTableSchema::reset();
    }

    /** Removes the rows of the checks' steps from both record tables (one simple statement each: MongoDB too). */
    protected function bridgeClearRows( $db )
    {
        $db->query( 'DELETE FROM ' . expMigrationRecord::LEGACY_TABLE . " WHERE step_id LIKE '6.0.15-wp8-%'" );
        $db->query( 'DELETE FROM ' . expMigrationRecord::LEGACY_TABLE . " WHERE step_id = '6.0.15-upgrade-record-table'" );
        foreach ( array( "LIKE '6.0.15-wp8-%'", "= '6.0.15-migration-tables'", "= '6.0.15-upgrade-record-table'" ) as $where )
            $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE source = 'kernel' AND migration_id $where" );
    }

    /** @return array id => entry */
    protected static function bridgeById( array $report )
    {
        $out = array();
        foreach ( $report['migrations'] as $entry )
            $out[$entry['id']] = $entry;
        return $out;
    }

    /** @return array step id => state in a report of exp:upgrade */
    protected static function upgradeStates( array $report )
    {
        $out = array();
        foreach ( $report['stages'] as $stage )
            foreach ( $stage['steps'] as $step )
                $out[$step['id']] = $step['state'];
        return $out;
    }

    /**
     * UE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkBridgeStatus( $db )
    {
        $this->bridgePrepare( $db );
        $report = expMigrationStatus::report( $this->bridgeOptions( $db ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( self::bridgeIds(), array_column( $report['migrations'], 'id' ), 'the kernel\'s steps first, then the site\'s migrations' );
        $this->assertSame( array( 'kernel', 'kernel', 'kernel', 'site' ), array_column( $report['migrations'], 'source' ) );
        $entries = self::bridgeById( $report );
        $this->assertSame( 'done', $entries['6.0.15-upgrade-record-table']['state'], 'in place and recorded in expupgrade_step (not copied yet)' );
        $this->assertSame( 'pending', $entries['6.0.15-migration-tables']['state'] );
        $this->assertStringContainsString( 'not copied to exp_migration', $entries['6.0.15-migration-tables']['detail'] );
        $this->assertSame( 'pending', $entries['6.0.15-wp8-kernel-table']['state'] );
        $this->assertSame( 'database', $entries['6.0.15-wp8-kernel-table']['stage'] );
        $this->assertSame( 'pending', $entries['20261031130000_wp3_note_table']['state'] );
        $this->assertSame( array(), array_filter( array_column( $report['orphans'], 'source' ), function ( $s ) { return $s === 'kernel'; } ),
                           'a kernel row is never an orphan' );

        $upgrade = self::upgradeStates( expUpgrade::status( array( 'database' ), array( 'db' => $db, 'registry' => $this->bridgeSource()->registry() ) ) );
        foreach ( array( '6.0.15-upgrade-record-table', '6.0.15-migration-tables', '6.0.15-wp8-kernel-table' ) as $id )
            $this->assertSame( $upgrade[$id], $entries[$id]['state'], "$id: the same state in exp:upgrade:status" );
        $this->assertNotTrue( expKernelTableSchema::exists( 'exp_test_wp8_kernel', $db, true ), 'the status changed nothing' );
    }

    /**
     * UE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkBridgeMigrate( $db )
    {
        $this->bridgePrepare( $db );
        $options = $this->bridgeOptions( $db, array( 'apply' => true, 'no-caption' => true, 'no-commit' => true, 'include-uncommitted' => true ) );

        $dry = expMigrationRunner::migrate( array( 'apply' => false ) + $options );
        $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
        $this->assertSame( 3, $dry['summary']['would-apply'] );
        $this->assertSame( 2, expMigrationRecord::legacyPending( $db ), 'the dry run copied nothing' );

        $report = expMigrationRunner::migrate( $options );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $applied = array();
        foreach ( $report['migrations'] as $entry )
            if ( $entry['action'] === 'applied' )
                $applied[] = $entry['id'];
        $this->assertSame( array( '6.0.15-migration-tables', '6.0.15-wp8-kernel-table', '20261031130000_wp3_note_table' ), $applied, 'the kernel\'s steps first' );
        $this->assertContains( 'kernel upgrade steps were applied: ./console exp:upgrade:finish --apply makes them live (deploy, caches, smoke check)', $report['warnings'] );

        expKernelTableSchema::reset();
        $this->assertTrue( expKernelTableSchema::exists( 'exp_test_wp8_kernel', $db, true ), 'the step created its table' );
        $this->assertTrue( expKernelTableSchema::exists( 'exp_test_wp3_note', $db, true ), 'the site migration created its table' );
        $this->assertSame( 0, expMigrationRecord::legacyPending( $db ), 'the rows of expupgrade_step are copied' );
        $legacy = expMigrationRecord::get( $db, 'kernel', '6.0.15-wp8-legacy' );
        $this->assertSame( 'from expupgrade_step', $legacy['message'] );
        $step = expMigrationRecord::get( $db, 'kernel', '6.0.15-wp8-kernel-table' );
        $this->assertSame( 'done', $step['state'] );
        $this->assertSame( 'database', $step['stage'] );
        $this->assertSame( '6.0.15', $step['version'] );
        $this->assertSame( $report['run_id'], $step['run_id'] );
        $this->assertSame( 'cli admin', $step['run_by'] );
        $this->assertSame( 'done', expMigrationRecord::get( $db, 'site', '20261031130000_wp3_note_table' )['state'] );
        $this->assertSame( 'done', expMigrationRecord::run( $db, $report['run_id'] )['state'] );

        $upgrade = self::upgradeStates( expUpgrade::status( array( 'database' ), array( 'db' => $db, 'registry' => $this->bridgeSource()->registry() ) ) );
        $this->assertSame( 'done', $upgrade['6.0.15-migration-tables'], 'exp:upgrade reads the record the migration run wrote' );
        $this->assertSame( 'done', $upgrade['6.0.15-wp8-kernel-table'] );

        $rows = self::runnerCount( $db, expMigrationRecord::TABLE, "source = 'kernel'" );
        $again = expMigrationRunner::migrate( $this->bridgeOptions( $db, array( 'apply' => true, 'no-caption' => true, 'no-commit' => true, 'include-uncommitted' => true ) ) );
        $this->assertSame( 0, $again['exit'], implode( '; ', $again['errors'] ) );
        $this->assertSame( 0, $again['summary']['applied'] );
        $this->assertSame( array( 'done', 'done', 'done', 'done' ), array_column( $again['migrations'], 'state' ) );
        $this->assertSame( 0, expMigrationRecord::copyLegacy( $db ), 'copying again copies nothing' );
        $this->assertSame( $rows, self::runnerCount( $db, expMigrationRecord::TABLE, "source = 'kernel'" ) );
        $this->assertFalse( expMigrationLock::installation( $this->runnerDir() . '/upgrade.lock' )->isLocked(), 'the lock is free' );

        $this->bridgeCleanUp( $db );
    }
}
