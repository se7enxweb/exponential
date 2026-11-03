<?php
/**
 * The checks of captions and the restore after a failure that run on every engine the same way, with real captions
 * (expUpgradeCaption::take(): exp:upgrade's own dump, git_manager is not used here) in a folder under var/tmp and the
 * real rollback (expUpgradeRollback::run()): SQLite in process (expMigrationCaptionSqliteTest), MySQL/MariaDB and
 * PostgreSQL in the database job (expMigrationCaptionDatabaseTest, mysqldump/mysql and pg_dump/psql), MongoDB in the
 * live MongoDB suite (expMigrationCaptionMongoDBTest, mongodump/mongorestore). Each gives a connection with the kernel
 * schema and the clean data. Uses expMigrationRunnerEngineChecks for the global state and the folder of the test.
 *
 *  CE-1 a run whose second migration fails: the caption migration-pre before it, the caption migration-failure of
 *       the failed state, then the pre caption restored: the table the first migration made is gone, the row it
 *       wrote into ezsite_data too; exp_migration says failed for the failed one and has no row for the first; the
 *       run row says failed with caption_pre; both captions are in the record and in the caption folder
 *  CE-2 a run that succeeds takes migration-post (caption_post). With its changes undone by hand the state is the one
 *       before the run, and the run's pre caption is reused; a changed row takes a new caption, which is reused while
 *       nothing changes. Only where the engine has an exact marker (SQLite, MySQL/MariaDB, PostgreSQL); MongoDB
 *       always takes a new one.
 *  CE-3 Oracle (expMigrationCaptionDatabaseTest), which has no built-in dump or restore: CE-1's run goes on with the
 *       warning that no caption could be taken (the message names expdp), nothing is restored after the failure and
 *       the report gives the steps by hand (impdp with the account and the connect string of the connection); the
 *       first migration's table and record stay, the failed one is recorded failed, the run row has no caption.
 *       CE-2's run succeeds without captions.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

trait expMigrationCaptionEngineChecks
{
    use expMigrationRunnerEngineChecks;

    /**
     * Skips the test when a tool the engine's dump or restore needs is not on PATH.
     *
     * @param string[] $tools
     */
    protected function requireTools( array $tools )
    {
        foreach ( $tools as $tool )
        {
            $found = false;
            foreach ( explode( PATH_SEPARATOR, (string)getenv( 'PATH' ) ) as $dir )
                if ( $dir !== '' && is_executable( rtrim( $dir, '/' ) . '/' . $tool ) )
                    $found = true;
            if ( !$found )
                $this->markTestSkipped( "$tool is not installed" );
        }
    }

    /**
     * A context that writes, with the captions in a folder of the test.
     *
     * @param eZDBInterface $db
     * @param string $runId
     * @return expMigrationContext
     */
    protected function captionContext( $db, $runId )
    {
        return new expMigrationContext( $db, true, array( 'lock-file' => $this->runnerDir() . '/upgrade.lock', 'repository' => $this->runnerDir() . '/migrations',
                                                          'environment' => 'testing', 'root' => self::runnerRoot(), 'run-id' => $runId,
                                                          // the test's connection dumped, never the installation's
                                                          // database through git_manager
                                                          'backup-dir' => $this->runnerDir() . '/captions', 'storage-dir' => '', 'settings-dir' => '', 'own-dump' => true,
                                                          'user' => eZUser::fetchByName( 'admin' ) ) );
    }

    /**
     * A runner with the caption flow: the database part restored, no code trees, no repository.
     *
     * @param expMigrationContext $context
     * @return expMigrationRunner
     */
    protected function captionRunner( expMigrationContext $context )
    {
        $caption = new expMigrationCaption( $context, array( 'restore-parts' => array( 'database' ), 'trees' => array() ) );
        return new expMigrationRunner( $context, array( 'flow' => new expMigrationRunFlow( $context, $caption, null ) ) );
    }

    /** @return expMigration[] the migration that makes a table and writes ezsite_data, and the one that fails */
    protected function captionMigrations( $withFailure = true )
    {
        $first = new expMigrationCaptionEngineNote();
        $first->setDefinition( new expMigrationDefinition( '20261031140000_wp4_note', 'site', 'php' ) );
        if ( !$withFailure )
            return array( $first );
        $second = new expMigrationCaptionEngineFailing();
        $second->setDefinition( new expMigrationDefinition( '20261031140100_wp4_fail', 'site', 'php' ) );
        return array( $first, $second );
    }

    /** Removes what the checks leave in the database. */
    protected function clearCaptionChecks( $db )
    {
        $this->dropRunnerTable( $db, 'exp_test_wp4_note' );
        $db->query( "DELETE FROM ezsite_data WHERE name LIKE 'exp_test_wp4%'" );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261031%wp4%' OR migration_id LIKE 'caption:%'" );
        $db->query( 'DELETE FROM ' . expMigrationRecord::RUN_TABLE );
        expKernelTableSchema::reset();
    }

    /**
     * CE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkRestoreAfterFailure( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        if ( $engine === 'oracle' )
            return $this->checkRestoreByHandAfterFailure( $db );
        $this->clearCaptionChecks( $db );
        $context = $this->captionContext( $db, '20261031-140000-c0ffee' );
        $report = $this->captionRunner( $context )->run( $this->captionMigrations() );

        $this->assertSame( 1, $report['exit'] );
        $this->assertTrue( $report['captions']['pre']['ok'], "$engine: the pre caption: " . implode( '; ', $report['captions']['pre']['messages'] ) );
        $this->assertSame( 'exp:upgrade', $report['captions']['pre']['source'], 'a dump of the test\'s connection' );
        $this->assertSame( 'restored', $report['restore']['state'], "$engine: " . implode( '; ', $report['errors'] ) . ' ' . json_encode( $report['restore'] ) );
        $this->assertTrue( $report['captions']['failure']['ok'], "$engine: the failure caption" );
        $this->assertSame( array( 'restored', 'failed' ), array_column( $report['migrations'], 'action' ) );

        expKernelTableSchema::reset();
        $this->assertNotTrue( expKernelTableSchema::exists( 'exp_test_wp4_note', $db, true ), "$engine: the table made after the caption is gone" );
        $this->assertSame( 0, self::runnerCount( $db, 'ezsite_data', "name LIKE 'exp_test_wp4%'" ), "$engine: the rows written after the caption are gone" );
        $this->assertNull( expMigrationRecord::get( $db, 'site', '20261031140000_wp4_note' ), "$engine: the first migration is not recorded any more" );
        $failed = expMigrationRecord::get( $db, 'site', '20261031140100_wp4_fail' );
        $this->assertNotNull( $failed, "$engine: the failure is recorded after the restore" );
        $this->assertSame( 'failed', $failed['state'] );
        $this->assertSame( $report['captions']['pre']['id'], $failed['caption_id'] );
        $run = expMigrationRecord::run( $db, '20261031-140000-c0ffee' );
        $this->assertNotNull( $run, "$engine: the run row" );
        $this->assertSame( 'failed', $run['state'] );
        $this->assertSame( $report['captions']['pre']['id'], $run['caption_pre'] );
        foreach ( array( $report['captions']['pre'], $report['captions']['failure'] ) as $caption )
        {
            $this->assertNotNull( expMigrationRecord::get( $db, expMigrationRecord::SOURCE_UPGRADE, 'caption:' . $caption['id'] ), "$engine: caption {$caption['id']} recorded" );
            $this->assertDirectoryExists( $this->runnerDir() . '/captions/' . $caption['id'] );
        }
        $meta = json_decode( file_get_contents( $this->runnerDir() . '/captions/' . $report['captions']['failure']['id'] . '/upgrade/caption.json' ), true );
        $this->assertSame( 'migration-failure', $meta['kind'] );
        $this->assertFalse( expMigrationLock::installation( $this->runnerDir() . '/upgrade.lock' )->isLocked(), 'the lock is free' );
        $this->clearCaptionChecks( $db );
    }

    /**
     * CE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkPostCaptionAndReuse( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        if ( $engine === 'oracle' )
            return $this->checkRunWithoutCaptions( $db );
        $this->clearCaptionChecks( $db );
        $context = $this->captionContext( $db, '20261031-140200-c0ffee' );
        $report = $this->captionRunner( $context )->run( $this->captionMigrations( false ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertTrue( $report['captions']['post']['ok'], "$engine: the post caption" );
        $run = expMigrationRecord::run( $db, '20261031-140200-c0ffee' );
        $this->assertSame( $report['captions']['pre']['id'], $run['caption_pre'] );
        $this->assertSame( $report['captions']['post']['id'], $run['caption_post'] );
        $this->assertNotSame( $run['caption_pre'], $run['caption_post'] );
        $this->assertTrue( expKernelTableSchema::exists( 'exp_test_wp4_note', $db, true ) );

        // the next run: the post caption reused while nothing changed (exact markers only)
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id = '20261031140000_wp4_note'" );
        $this->dropRunnerTable( $db, 'exp_test_wp4_note' );
        $db->query( "DELETE FROM ezsite_data WHERE name LIKE 'exp_test_wp4%'" );
        $caption = new expMigrationCaption( $this->captionContext( $db, '20261031-140300-c0ffee' ), array( 'trees' => array() ) );
        $back = $caption->take( expMigrationCaption::PRE, array() );
        $this->assertTrue( $back['ok'] );
        if ( $engine === 'mongodb' )
        {
            $this->assertFalse( $back['reused'], 'MongoDB has no exact marker: always a new caption' );
            $this->assertFalse( $caption->take( expMigrationCaption::PRE, array() )['reused'] );
        }
        else
        {
            // the run undone by hand: the state is the one before the run, so its pre caption is reused
            $this->assertTrue( $back['reused'], "$engine: the state before the run again" );
            $this->assertSame( $run['caption_pre'], $back['id'] );
            $db->query( "INSERT INTO ezsite_data ( name, value ) VALUES ( 'exp_test_wp4_change', '1' )" );
            $new = $caption->take( expMigrationCaption::PRE, array() );
            $this->assertFalse( $new['reused'], "$engine: a changed row takes a new caption" );
            $again = $caption->take( expMigrationCaption::PRE, array() );
            $this->assertTrue( $again['reused'], "$engine: nothing changed since, the caption is reused" );
            $this->assertSame( $new['id'], $again['id'] );
        }
        $this->clearCaptionChecks( $db );
    }

    /**
     * CE-3, the failure: no caption, nothing restored, the steps by hand.
     *
     * @param eZDBInterface $db
     */
    protected function checkRestoreByHandAfterFailure( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->clearCaptionChecks( $db );
        $context = $this->captionContext( $db, '20261031-140000-c0ffee' );
        $report = $this->captionRunner( $context )->run( $this->captionMigrations() );

        $this->assertSame( 1, $report['exit'] );
        $this->assertFalse( $report['captions']['pre']['ok'], "$engine: no built-in dump" );
        $this->assertStringContainsString( 'expdp', implode( '; ', $report['captions']['pre']['messages'] ) );
        $this->assertStringContainsString( 'no caption could be taken', implode( '; ', $report['warnings'] ) );
        $this->assertSame( array( 'applied', 'failed' ), array_column( $report['migrations'], 'action' ) );
        $this->assertSame( 'none', $report['restore']['state'], json_encode( $report['restore'] ) );
        $steps = implode( "\n", $report['restore']['steps'] );
        $connect = strpos( (string)$db->DB, '/' ) !== false ? (string)$db->DB : '//' . $db->Server . ':' . ( $db->Port ?: 1521 ) . '/' . $db->DB;
        $this->assertStringContainsString( 'impdp ' . $db->User . '@' . $connect . ' SCHEMAS=' . strtoupper( $db->User ), $steps, 'the connect string of the connection' );

        expKernelTableSchema::reset();
        $this->assertTrue( expKernelTableSchema::exists( 'exp_test_wp4_note', $db, true ), "$engine: nothing restored, the table stays" );
        $this->assertNotNull( expMigrationRecord::get( $db, 'site', '20261031140000_wp4_note' ), "$engine: the first migration stays applied" );
        $failed = expMigrationRecord::get( $db, 'site', '20261031140100_wp4_fail' );
        $this->assertNotNull( $failed, "$engine: the failure is recorded" );
        $this->assertSame( 'failed', $failed['state'] );
        $run = expMigrationRecord::run( $db, '20261031-140000-c0ffee' );
        $this->assertNotNull( $run, "$engine: the run row" );
        $this->assertSame( 'failed', $run['state'] );
        $this->assertEmpty( $run['caption_pre'] );
        $this->assertFalse( expMigrationLock::installation( $this->runnerDir() . '/upgrade.lock' )->isLocked(), 'the lock is free' );
        $this->clearCaptionChecks( $db );
    }

    /**
     * CE-3, a run that succeeds: without captions, with the warning.
     *
     * @param eZDBInterface $db
     */
    protected function checkRunWithoutCaptions( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->clearCaptionChecks( $db );
        $context = $this->captionContext( $db, '20261031-140200-c0ffee' );
        $report = $this->captionRunner( $context )->run( $this->captionMigrations( false ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertFalse( $report['captions']['pre']['ok'], "$engine: no built-in dump" );
        $this->assertStringContainsString( 'no caption could be taken', implode( '; ', $report['warnings'] ) );
        $run = expMigrationRecord::run( $db, '20261031-140200-c0ffee' );
        $this->assertNotNull( $run );
        $this->assertEmpty( $run['caption_pre'] );
        $this->assertEmpty( $run['caption_post'] );
        $this->assertTrue( expKernelTableSchema::exists( 'exp_test_wp4_note', $db, true ) );
        $this->clearCaptionChecks( $db );
    }
}

/** Makes the table exp_test_wp4_note with a row and writes a row into ezsite_data (CE-1, CE-2). */
class expMigrationCaptionEngineNote extends expMigrationPhp
{
    public function up( $context )
    {
        $schema = array( 'exp_test_wp4_note' => array(
            'name' => 'exp_test_wp4_note',
            'fields' => array( 'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
        $messages = array();
        if ( !expKernelTableSchema::install( array( 'exp_test_wp4_note' ), $context->db(), $schema, $messages ) )
            throw new RuntimeException( 'could not create the table: ' . implode( '; ', $messages ) );
        $context->db()->query( 'INSERT INTO exp_test_wp4_note ( id ) VALUES ( 1 )' );
        $context->db()->query( "INSERT INTO ezsite_data ( name, value ) VALUES ( 'exp_test_wp4_note', '1' )" );
        return array( 'made exp_test_wp4_note' );
    }
}

/** Writes a row into ezsite_data and fails (CE-1). */
class expMigrationCaptionEngineFailing extends expMigrationPhp
{
    public function up( $context )
    {
        $context->db()->query( "INSERT INTO ezsite_data ( name, value ) VALUES ( 'exp_test_wp4_fail', '1' )" );
        throw new RuntimeException( 'failed on purpose after a row' );
    }
}
