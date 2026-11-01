<?php
/**
 * The checks of the migration record that run on every engine the same way: SQLite in process
 * (expMigrationRecordTest), MySQL/MariaDB and PostgreSQL in the database job (expMigrationRecordDatabaseTest), MongoDB
 * in the live MongoDB suite (expMigrationRecordMongoDBTest). Each is given a connection without exp_migration and
 * exp_migration_run, which it may write to.
 *
 *  MR-E1 create: the upgrade step 6.0.15-migration-tables is missing and makes both tables (it copies nothing); a
 *        database of a pre-release of 6.0.15 with both tables: the step 6.0.15-upgrade-record-table is missing, its
 *        dry run lists each row it copies and the drop, it copies the rows exp_migration does not have (mapping of
 *        expMigrationRecord; a newer row of exp_migration is kept), drops expupgrade_step and is present afterwards
 *  MR-E2 re-run idempotent: a second apply of both steps does nothing and changes no row
 *  MR-E3 write and read: a migration row and a run row, replaced by a second write, listed, removed
 *  MR-E4 exp:upgrade's record: written to exp_migration as source "kernel" and read from it only
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationLegacyRecordFixture.php';

trait expMigrationRecordEngineChecks
{
    /** @return string the root of the checkout */
    protected static function migrationRoot()
    {
        return dirname( __DIR__, 5 );
    }

    /** @return array the kernel schema */
    protected static function migrationSchema()
    {
        return expKernelTableSchema::read( self::migrationRoot() . '/share/db_schema.dba' );
    }

    /**
     * Makes expupgrade_step (when missing) with two step rows and a caption row, and leaves exp_migration and
     * exp_migration_run out.
     *
     * @param eZDBInterface $db
     */
    protected function prepareLegacy( $db )
    {
        expKernelTableSchema::reset();
        foreach ( array( expMigrationRecord::TABLE, expMigrationRecord::RUN_TABLE ) as $table )
            if ( expKernelTableSchema::exists( $table, $db, true ) === true )
                $this->dropTable( $db, $table );
        expKernelTableSchema::reset();
        $this->assertFalse( expMigrationRecord::exists( $db ), 'exp_migration is gone before the test' );
        $this->assertArrayNotHasKey( expMigrationRecord::LEGACY_TABLE, self::migrationSchema(), 'a new installation has no expupgrade_step' );
        $messages = expMigrationLegacyRecordFixture::install( $db, array(
            array( '6.0.15-upgrade-record-table', 'database', '6.0.15', 'done', 1761900000, 1500, 'cli root', 'sqlite', str_repeat( 'a', 64 ), '' ),
            array( '6.0.15-trash-columns', 'database', '6.0.15', 'failed', 1761900100, 250, 'cli alpha', 'sqlite', str_repeat( 'b', 64 ), 'column exists' ),
            array( 'caption:20261031-120000-pre', 'caption', '', 'pre', 1761899990, 0, 'cli root', 'sqlite', '', 'before the upgrade (git_manager)' ),
        ) );
        $this->assertSame( array(), $messages, implode( '; ', $messages ) );
        $this->assertTrue( expKernelTableSchema::exists( expMigrationRecord::LEGACY_TABLE, $db, true ) );
        // exp:upgrade never reads expupgrade_step: without exp_migration there is no record, and nothing is written
        $this->assertNull( expUpgradeRecord::table( $db ) );
        $this->assertFalse( expUpgradeRecord::exists( $db ) );
        $this->assertSame( array(), expUpgradeRecord::all( $db ), 'expupgrade_step is not read' );
        $this->assertFalse( expUpgradeRecord::writeCaption( $db, 'none', 'pre', 'before', 'kernel' ), 'nothing is written without exp_migration' );
    }

    /** Drops a table of the test connection (MongoDB: the collection). */
    protected function dropTable( $db, $table )
    {
        $db->query( 'DROP TABLE ' . $table );
    }

    /** MR-E1, MR-E2 */
    protected function checkCreateAndCopy( $db )
    {
        $this->prepareLegacy( $db );
        $context = new expUpgradeContext( $db, true, array(), self::migrationRoot() . '/share/db_schema.dba' );
        $tables = new expUpgradeStepMigrationTables();
        $drop = new expUpgradeStepRecordTable();
        $this->assertSame( array(), $tables->after(), 'step 0' );
        $this->assertSame( array( '6.0.15-migration-tables' ), $drop->after() );

        // the drop step refuses to drop while exp_migration is missing: nothing is lost
        $effect = $drop->detect( $context );
        $this->assertSame( expUpgradeStep::MISSING, $effect['effect'], $effect['detail'] );
        try
        {
            $drop->apply( $context );
            $this->fail( 'the drop step ran without exp_migration' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'expupgrade_step is kept', $e->getMessage() );
        }
        expKernelTableSchema::reset();
        $this->assertTrue( expKernelTableSchema::exists( expMigrationRecord::LEGACY_TABLE, $db, true ) );

        // step 0 makes the tables and copies nothing
        $effect = $tables->detect( $context );
        $this->assertSame( expUpgradeStep::MISSING, $effect['effect'], $effect['detail'] );
        $done = $tables->apply( $context );
        $this->assertSame( array( 'created table exp_migration', 'created table exp_migration_run' ), $done );
        $effect = $tables->detect( $context );
        $this->assertSame( expUpgradeStep::PRESENT, $effect['effect'], $effect['detail'] );
        $this->assertSame( array(), expMigrationRecord::all( $db ) );

        // a newer result of one step is in exp_migration already (the live state: both tables)
        expMigrationRecord::write( $db, array( 'source' => 'kernel', 'migration_id' => '6.0.15-trash-columns', 'stage' => 'database',
                                               'version' => '6.0.15', 'state' => 'done', 'finished_at' => 1761990000 ) );

        $effect = $drop->detect( $context );
        $this->assertSame( expUpgradeStep::MISSING, $effect['effect'], $effect['detail'] );
        $this->assertStringContainsString( '2 row(s) not in exp_migration yet', $effect['detail'] );
        $this->assertSame( array( '6.0.15-upgrade-record-table', 'caption:20261031-120000-pre' ), expUpgradeStepRecordTable::pendingIds( $db ) );
        $plan = $drop->plan( $context );
        $this->assertSame( array( 'copy the row 6.0.15-upgrade-record-table of expupgrade_step into exp_migration',
                                  'copy the row caption:20261031-120000-pre of expupgrade_step into exp_migration',
                                  'check that every row of expupgrade_step is in exp_migration' ), array_slice( $plan, 0, 3 ) );
        $this->assertMatchesRegularExpression( '/^drop table expupgrade_step: DROP TABLE expupgrade_step( PURGE)?;$/', $plan[3] );
        $this->assertCount( 4, $plan );
        expKernelTableSchema::reset();
        $this->assertTrue( expKernelTableSchema::exists( expMigrationRecord::LEGACY_TABLE, $db, true ), 'the dry run dropped nothing' );
        $this->assertSame( 2, expMigrationRecord::legacyPending( $db ), 'the dry run copied nothing' );

        $done = $drop->apply( $context );
        $this->assertSame( array( 'copied 2 row(s) of expupgrade_step into exp_migration', 'every row of expupgrade_step is in exp_migration',
                                  'dropped table expupgrade_step' ), $done );
        expKernelTableSchema::reset();
        $this->assertFalse( expKernelTableSchema::exists( expMigrationRecord::LEGACY_TABLE, $db, true ), 'expupgrade_step is gone' );
        $effect = $drop->detect( $context );
        $this->assertSame( expUpgradeStep::PRESENT, $effect['effect'], $effect['detail'] );

        $rows = array();
        foreach ( expMigrationRecord::all( $db ) as $row )
            $rows[$row['migration_id']] = $row;
        $this->assertSame( array( '6.0.15-trash-columns', '6.0.15-upgrade-record-table', 'caption:20261031-120000-pre' ), array_keys( $rows ) );
        $first = $rows['6.0.15-upgrade-record-table'];
        $this->assertSame( 'kernel', $first['source'] );
        $this->assertSame( 'database', $first['stage'] );
        $this->assertSame( '6.0.15', $first['version'] );
        $this->assertSame( 'done', $first['state'] );
        $this->assertSame( 1761900000, $first['finished_at'] );
        $this->assertSame( 1761899998, $first['started_at'], 'finished minus 1.5 s, rounded' );
        $this->assertSame( 1500, $first['duration_ms'] );
        $this->assertSame( 'cli root', $first['run_by'] );
        $this->assertSame( str_repeat( 'a', 64 ), $first['checksum'] );
        $this->assertSame( 'done', $rows['6.0.15-trash-columns']['state'], 'the newer row of exp_migration is kept' );
        $this->assertSame( 1761990000, $rows['6.0.15-trash-columns']['finished_at'] );
        $this->assertSame( 'caption', $rows['caption:20261031-120000-pre']['stage'] );
        $this->assertSame( '20261031-120000-pre', $rows['caption:20261031-120000-pre']['caption_id'] );

        // MR-E2: both steps again: nothing to do, nothing changed
        $before = expMigrationRecord::all( $db );
        $this->assertSame( array(), $tables->apply( $context ) );
        $this->assertSame( array(), $drop->plan( $context ) );
        $this->assertSame( array(), $drop->apply( $context ), 'nothing to copy or drop the second time' );
        $this->assertSame( 0, expMigrationRecord::legacyPending( $db ) );
        $this->assertSame( $before, expMigrationRecord::all( $db ) );
    }

    /** MR-E3 */
    protected function checkWriteAndRead( $db )
    {
        $row = array( 'source' => 'site', 'migration_id' => '20261031120000_add_news_class', 'stage' => 'migration',
                      'file_path' => 'migrations/20261031120000_add_news_class.php', 'checksum' => str_repeat( 'c', 64 ),
                      'state' => 'done', 'environment' => 'staging', 'run_id' => '20261031-120000-ab12', 'started_at' => 1761912000,
                      'finished_at' => 1761912001, 'duration_ms' => 812, 'run_by' => 'cli alpha', 'caption_id' => 'cap-1',
                      'revision' => str_repeat( 'd', 40 ), 'engine' => expKernelTableSchema::type( $db ), 'message' => "two\nlines",
                      'unknown' => 'ignored' );
        $this->assertTrue( expMigrationRecord::write( $db, $row ) );
        $read = expMigrationRecord::get( $db, 'site', '20261031120000_add_news_class' );
        unset( $row['unknown'] );
        foreach ( $row as $name => $value )
            $this->assertSame( $value, $read[$name], "column $name" );
        $this->assertSame( array_keys( expMigrationRecord::COLUMNS ), array_keys( $read ) );

        $this->assertTrue( expMigrationRecord::write( $db, array( 'state' => 'failed' ) + $row ) );
        $this->assertSame( 'failed', expMigrationRecord::get( $db, 'site', '20261031120000_add_news_class' )['state'], 'replaced' );
        $this->assertTrue( expMigrationRecord::write( $db, array( 'source' => 'extension:expwt' ) + $row ) );
        $this->assertCount( 1, expMigrationRecord::all( $db, 'site' ), 'one row per source and id' );
        $this->assertCount( 1, expMigrationRecord::all( $db, 'extension:expwt' ) );
        $this->assertTrue( expMigrationRecord::remove( $db, 'extension:expwt', '20261031120000_add_news_class' ) );
        $this->assertNull( expMigrationRecord::get( $db, 'extension:expwt', '20261031120000_add_news_class' ) );

        foreach ( array( array( 'r-1', 1761912000, 'done' ), array( 'r-2', 1761913000, 'failed' ), array( 'r-0', 1761911000, 'done' ) ) as $r )
            $this->assertTrue( expMigrationRecord::writeRun( $db, array( 'run_id' => $r[0], 'started_at' => $r[1], 'finished_at' => $r[1] + 5,
                                                                         'state' => $r[2], 'caption_pre' => 'cap-' . $r[0], 'commit_id' => 'abc',
                                                                         'tag_name' => 'migration-' . $r[0], 'push_state' => 'pushed',
                                                                         'log_file' => 'runs/' . $r[0] . '.log', 'environment' => 'staging' ) ) );
        $this->assertTrue( expMigrationRecord::writeRun( $db, array( 'run_id' => 'r-2', 'started_at' => 1761913000, 'state' => 'rolled-back' ) ) );
        $this->assertSame( 'rolled-back', expMigrationRecord::run( $db, 'r-2' )['state'] );
        $this->assertSame( '', expMigrationRecord::run( $db, 'r-2' )['commit_id'], 'a run row is replaced whole' );
        $this->assertSame( array( 'r-2', 'r-1', 'r-0' ), array_column( expMigrationRecord::runs( $db ), 'run_id' ), 'newest first' );
        $this->assertSame( array( 'r-2' ), array_column( expMigrationRecord::runs( $db, 1 ), 'run_id' ) );
        $this->assertNull( expMigrationRecord::run( $db, 'r-9' ) );
    }

    /** MR-E4 */
    protected function checkUpgradeRecord( $db )
    {
        $this->assertSame( expMigrationRecord::TABLE, expUpgradeRecord::table( $db ) );
        $this->assertTrue( expUpgradeRecord::exists( $db ) );
        $step = new class() extends expUpgradeBaseStep {
            public function id() { return '6.0.15-wp2-record-check'; }
            public function stage() { return 'database'; }
            public function description() { return 'test'; }
            public function detect( expUpgradeContext $context ) { return self::effect( self::PRESENT ); }
            public function plan( expUpgradeContext $context ) { return array(); }
            public function apply( expUpgradeContext $context ) { return array(); }
        };
        $this->assertTrue( expUpgradeRecord::write( $db, $step, 'done', 2100, 'applied' ) );
        $this->assertTrue( expUpgradeRecord::writeCaption( $db, 'cap-wp2', 'post', 'after', 'kernel' ) );
        $row = expMigrationRecord::get( $db, 'kernel', '6.0.15-wp2-record-check' );
        $this->assertSame( 'done', $row['state'] );
        $this->assertSame( 2100, $row['duration_ms'] );
        $this->assertSame( $row['finished_at'] - 2, $row['started_at'] );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{64}$/', $row['checksum'] );
        $this->assertStringEndsWith( 'expMigrationRecordEngineChecks.php', $row['file_path'] );
        $this->assertSame( 'cap-wp2', expMigrationRecord::get( $db, 'kernel', 'caption:cap-wp2' )['caption_id'] );
        expKernelTableSchema::reset();
        $this->assertFalse( expKernelTableSchema::exists( expMigrationRecord::LEGACY_TABLE, $db, true ), 'writing never makes expupgrade_step' );

        $all = expUpgradeRecord::all( $db );
        $this->assertSame( 'done', $all['6.0.15-wp2-record-check']['state'] );
        $this->assertSame( 2100, $all['6.0.15-wp2-record-check']['duration_ms'] );
        $this->assertSame( 'done', $all['6.0.15-trash-columns']['state'] );
        $this->assertSame( 'done', $all['6.0.15-upgrade-record-table']['state'], 'the copied rows are read from exp_migration' );
        $this->assertSame( array( 'step_id', 'stage', 'version', 'state', 'applied_at', 'duration_ms', 'applied_by', 'engine', 'checksum', 'message' ),
                           array_keys( $all['6.0.15-wp2-record-check'] ) );
    }
}
