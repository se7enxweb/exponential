<?php
/**
 * The checks of the classes behind the exp:migration commands that run on every engine the same way: SQLite in
 * process (expMigrationCommandSqliteTest), MySQL/MariaDB and PostgreSQL in the database job
 * (expMigrationCommandDatabaseTest), MongoDB in the live MongoDB suite (expMigrationCommandMongoDBTest). Used next to
 * expMigrationRunnerEngineChecks (its connection set-up, fixtures and helpers).
 *
 *  CE-1 the status: both fixtures pending, the tables there, a record whose file is gone listed as an orphan, the
 *       runs newest first, a run whose push waits; nothing written
 *  CE-2 the simulation with diff: on PostgreSQL and SQLite both fixtures run in a transaction that is rolled back
 *       (the diff shows the new table and the content rows; afterwards there is no table, no folder and no record);
 *       on MySQL/MariaDB, Oracle and MongoDB nothing runs and a warning says why
 *  CE-3 resume: an interrupted run (its row says running, the lock is free) is closed as failed and the pending
 *       migrations run in a new run; afterwards nothing is left to resume
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

trait expMigrationCommandEngineChecks
{
    /** @return array the options of a run of the fixtures in runnerDir() */
    protected function commandOptions( $db, array $extra = array() )
    {
        return $extra + array( 'db' => $db, 'lock-file' => $this->runnerDir() . '/upgrade.lock', 'repository' => $this->runnerDir() . '/migrations',
                               'environment' => 'testing', 'root' => self::runnerRoot(), 'user' => eZUser::fetchByName( 'admin' ),
                               'migrations' => $this->runnerFixtures() );
    }

    /**
     * CE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkStatus( $db )
    {
        $this->dropRunnerTable( $db, 'exp_test_wp3_note' );
        $this->clearRunnerRecord( $db );
        $migrations = $this->runnerFixtures();
        expMigrationRecord::write( $db, array( 'source' => 'site', 'migration_id' => '20261031125900_wp5_gone', 'stage' => 'migration', 'state' => 'done',
                                               'file_path' => 'migrations/20261031125900_wp5_gone.php', 'run_id' => '20261031-125900-aaaaaa',
                                               'finished_at' => 1793451540 ) );
        expMigrationRecord::writeRun( $db, array( 'run_id' => '20261031-125900-aaaaaa', 'state' => 'done', 'started_at' => 1793451500,
                                                  'finished_at' => 1793451540, 'push_state' => 'pending' ) );
        expMigrationRecord::writeRun( $db, array( 'run_id' => '20261031-130000-bbbbbb', 'state' => 'failed', 'started_at' => 1793451600,
                                                  'finished_at' => 1793451660, 'push_state' => 'pushed' ) );

        $report = expMigrationStatus::report( $this->commandOptions( $db, array( 'migrations' => $migrations ) ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 'status', $report['mode'] );
        $this->assertTrue( $report['table'] );
        $this->assertSame( array( 'pending', 'pending' ), array_column( $report['migrations'], 'state' ) );
        $this->assertSame( count( $migrations ), $report['summary']['pending'] );
        $this->assertSame( array(), array_filter( array_column( $report['migrations'], 'plan' ) ), 'the status lists no plan' );
        $this->assertSame( array( '20261031125900_wp5_gone' ), array_column( $report['orphans'], 'id' ) );
        $this->assertSame( array( '20261031-130000-bbbbbb', '20261031-125900-aaaaaa' ), array_column( $report['runs'], 'run_id' ) );
        $this->assertSame( array( '20261031-125900-aaaaaa' ), $report['pushes'] );
        $this->assertNull( $report['lock'] );
        $this->assertNotTrue( expKernelTableSchema::exists( 'exp_test_wp3_note', $db, true ), 'the status changed nothing' );

        $only = expMigrationStatus::report( $this->commandOptions( $db, array( 'migrations' => $migrations, 'only' => array( '20261031130000_wp3_note_table' ), 'runs' => 0 ) ) );
        $this->assertSame( array( '20261031130000_wp3_note_table' ), array_column( $only['migrations'], 'id' ) );
        $this->assertSame( array(), $only['runs'] );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id = '20261031125900_wp5_gone'" );
        // one statement per row: the rows of this check go on every engine (MongoDB included)
        foreach ( array( '20261031-125900-aaaaaa', '20261031-130000-bbbbbb' ) as $runId )
            $db->query( 'DELETE FROM ' . expMigrationRecord::RUN_TABLE . " WHERE run_id = '$runId'" );
        $this->clearRunnerRecord( $db );
    }

    /**
     * CE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkSimulation( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->dropRunnerTable( $db, 'exp_test_wp3_note' );
        $this->clearRunnerRecord( $db );
        $withContent = true;
        $context = $this->runnerContext( $db );
        $context->setOption( 'no-mirror', true );
        $simulation = new expMigrationSimulation( $context, array( 'diff' => true ) );
        $report = $simulation->run( $this->runnerFixtures( $withContent ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'simulation' ) ) );
        $this->assertSame( 'simulate', $report['mode'] );
        if ( !expMigrationSimulation::supports( $engine ) )
        {
            $this->assertFalse( $report['simulated'], "$engine does not simulate" );
            $this->assertStringContainsString( "no simulation on $engine", implode( "\n", $report['warnings'] ) );
            $this->assertSame( array_fill( 0, count( $report['migrations'] ), 'would-apply' ), array_column( $report['migrations'], 'action' ) );
        }
        else
        {
            $this->assertTrue( $report['simulated'] );
            $first = $report['migrations'][0]['simulation'];
            $this->assertSame( 'passed', $first['result'] );
            $this->assertContains( '+ table exp_test_wp3_note (2 columns, 1 row)', $first['diff'] );
            if ( $withContent )
            {
                $second = $report['migrations'][1]['simulation'];
                $this->assertSame( 'passed', $second['result'], implode( '; ', $second['messages'] ) );
                $this->assertContains( '~ table exp_test_wp3_note: rows 1 -> 2', $second['diff'] );
                $this->assertNotEmpty( preg_grep( '/^~ table ezcontentobject: rows \d+ -> \d+$/', $second['diff'] ), implode( "\n", $second['diff'] ) );
            }
        }
        expKernelTableSchema::reset();
        $this->assertNotTrue( expKernelTableSchema::exists( 'exp_test_wp3_note', $db, true ), 'the table was rolled back' );
        eZContentObject::clearCache();
        $this->assertNull( eZContentObject::fetchByRemoteID( 'exp-test-wp3-notes-folder' ), 'the folder was rolled back' );
        $this->assertNull( expMigrationRecord::get( $db, 'site', '20261031130000_wp3_note_table' ), 'nothing recorded' );
        $this->assertSame( array(), expMigrationRecord::runs( $db ), 'no run recorded' );
        $this->assertSame( 0, $db->transactionCounter(), 'no transaction left open' );
        $this->assertFalse( expMigrationLock::installation( $this->runnerDir() . '/upgrade.lock' )->isLocked(), 'the lock is free' );
    }

    /**
     * CE-3
     *
     * @param eZDBInterface $db
     */
    protected function checkResume( $db )
    {
        $this->dropRunnerTable( $db, 'exp_test_wp3_note' );
        $this->clearRunnerRecord( $db );
        expMigrationRecord::writeRun( $db, array( 'run_id' => '20261031-140000-cccccc', 'state' => 'running', 'started_at' => 1793455200,
                                                  'environment' => 'testing' ) );
        $options = $this->commandOptions( $db, array( 'apply' => true, 'no-caption' => true, 'no-commit' => true, 'include-uncommitted' => true ) );

        $dry = expMigrationResume::run( null, array( 'apply' => false ) + $options );
        $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
        $this->assertSame( '20261031-140000-cccccc', $dry['resumed_from']['run_id'] );
        $this->assertSame( 'running', expMigrationRecord::run( $db, '20261031-140000-cccccc' )['state'], 'the dry run changed nothing' );

        $report = expMigrationResume::run( null, $options );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( count( $options['migrations'] ), $report['summary']['applied'] );
        $old = expMigrationRecord::run( $db, '20261031-140000-cccccc' );
        $this->assertSame( 'failed', $old['state'] );
        $this->assertStringStartsWith( 'interrupted; resumed on ', $old['message'] );
        $this->assertSame( 'done', expMigrationRecord::run( $db, $report['run_id'] )['state'] );

        $again = expMigrationResume::run( null, $options );
        $this->assertSame( 1, $again['exit'] );
        $this->assertSame( array( 'the newest run ' . $report['run_id'] . ' is done, there is nothing to resume' ), $again['errors'] );

        $this->checkRollback( $db );
        $this->clearRunnerRecord( $db );
    }
}
