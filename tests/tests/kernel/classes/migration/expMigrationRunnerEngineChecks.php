<?php
/**
 * The checks of the migration runner that run on every engine the same way: SQLite in process
 * (expMigrationRunnerSqliteTest), MySQL/MariaDB and PostgreSQL in the database job (expMigrationRunnerDatabaseTest),
 * MongoDB in the live MongoDB suite (expMigrationRunnerMongoDBTest). Each gives a connection with the kernel schema,
 * the tables exp_migration and exp_migration_run, and (where content is checked) the clean data, which it may write to.
 *
 *  RE-1 a PHP migration that creates a table and writes a row, and one that publishes a folder as the run's user (the
 *       fixtures in fixtures/runner): both applied in order, the record, the run row, the run log; a second run has
 *       nothing to do
 *  RE-2 transactions: a migration that creates a table, writes a row and fails. PostgreSQL and SQLite undo all of it;
 *       MySQL/MariaDB keep the table and the row (a schema change commits at once); Oracle keeps the table and
 *       undoes the row (the schema change commits by itself, the transaction goes on); MongoDB keeps both (no
 *       transactions). The record says failed.
 *  RE-3 rollback: down() of both, the last first; the folder and the table are gone, the record is empty again
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

trait expMigrationRunnerEngineChecks
{
    /** @var string|null a directory of this test under var/tmp */
    protected $runnerDir;

    /** @var array|null what runnerBegin() changed: the global database, the module paths, the exception handler */
    protected $runnerSaved;

    /**
     * Makes a connection the global database of the test and the kernel's modules known (the publish operation reads
     * content/operation_definition.php); runnerEnd() puts everything back.
     *
     * @param eZDBInterface $db
     */
    protected function runnerBegin( $db )
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        $this->runnerSaved = array(
            'db' => array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null,
            'modules' => eZModule::globalPathList(),
            'handler' => $handler,
            'access' => array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? array( $GLOBALS['eZCurrentAccess'] ) : null,
        );
        // the search engine of the publish operation is looked up by the siteaccess
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'migration_test', 'type' => 1 );
        // file caches of the kernel (languages, class attributes ...) in a var directory of the test: caches another
        // test process wrote for its database in var/cache are not this database's
        $ini = eZINI::instance();
        $this->runnerSaved['vardir'] = $ini->variable( 'FileSettings', 'VarDir' );
        $ini->setVariable( 'FileSettings', 'VarDir', $this->runnerDir() . '/var' );
        // sections are kept by id and identifier for the process: another test's database may have had other
        // sections under the same ids
        unset( $GLOBALS['eZContentLanguageList'], $GLOBALS['eZContentLanguageMask'], $GLOBALS['eZContentSectionObjectCache'] );
        eZDB::setInstance( $db );
        eZModule::setGlobalPathList( eZModule::activeModuleRepositories( false ) );
        expKernelTableSchema::reset();
        eZContentObject::clearCache();
    }

    /** Undoes runnerBegin() and removes runnerDir(). */
    protected function runnerEnd()
    {
        if ( $this->runnerSaved !== null )
        {
            if ( $this->runnerSaved['db'] === null )
                unset( $GLOBALS['eZDBGlobalInstance'] );
            else
                $GLOBALS['eZDBGlobalInstance'] = $this->runnerSaved['db'][0];
            eZModule::setGlobalPathList( $this->runnerSaved['modules'] );
            eZINI::instance()->setVariable( 'FileSettings', 'VarDir', $this->runnerSaved['vardir'] );
            unset( $GLOBALS['eZContentLanguageList'], $GLOBALS['eZContentLanguageMask'], $GLOBALS['eZContentSectionObjectCache'] );
            if ( $this->runnerSaved['access'] === null )
                unset( $GLOBALS['eZCurrentAccess'], $GLOBALS['eZSearchPlugin_migration_test'] );
            else
                $GLOBALS['eZCurrentAccess'] = $this->runnerSaved['access'][0];
            // the first lookup of the current user in a process may install the kernel's exception handler
            for ( $i = 0; $i < 5; $i++ )
            {
                $now = set_exception_handler( null );
                restore_exception_handler();
                if ( $now === $this->runnerSaved['handler'] )
                    break;
                restore_exception_handler();
            }
            $this->runnerSaved = null;
        }
        expKernelTableSchema::reset();
        eZContentObject::clearCache();
        $this->removeRunnerDir();
    }

    /** @return string the root of the checkout */
    protected static function runnerRoot()
    {
        return dirname( __DIR__, 5 );
    }

    /** @return string a directory under var/tmp for the lock and the run log, made once */
    protected function runnerDir()
    {
        if ( $this->runnerDir === null )
        {
            $this->runnerDir = self::runnerRoot() . '/var/tmp/migration-runner-engine-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
            mkdir( $this->runnerDir . '/migrations', 0775, true );
        }
        return $this->runnerDir;
    }

    /** Removes the directory of runnerDir() with what is in it. */
    protected function removeRunnerDir()
    {
        if ( $this->runnerDir === null || !is_dir( $this->runnerDir ) )
            return;
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->runnerDir, FilesystemIterator::SKIP_DOTS ),
                                                   RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $iterator as $item )
            $item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
        rmdir( $this->runnerDir );
        $this->runnerDir = null;
    }

    /**
     * @param eZDBInterface $db
     * @param bool $apply
     * @return expMigrationContext a context of the admin of the clean data, the lock and the repository in runnerDir()
     */
    protected function runnerContext( $db, $apply = true, $user = true )
    {
        $options = array( 'lock-file' => $this->runnerDir() . '/upgrade.lock', 'repository' => $this->runnerDir() . '/migrations',
                          'environment' => 'testing', 'root' => self::runnerRoot() );
        $options['user'] = $user ? eZUser::fetchByName( 'admin' ) : null;
        return new expMigrationContext( $db, $apply, $options );
    }

    /** @return expMigration[] the fixtures of fixtures/runner, in order */
    protected function runnerFixtures( $withContent = true )
    {
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => __DIR__ . '/fixtures/runner' ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        return $withContent ? $migrations : array( $migrations[0] );
    }

    /**
     * Drops a table or collection when it exists.
     *
     * @param eZDBInterface $db
     * @param string $table
     */
    protected function dropRunnerTable( $db, $table )
    {
        expKernelTableSchema::reset();
        if ( expKernelTableSchema::exists( $table, $db, true ) === true )
        {
            if ( $db instanceof expMongoDB )
                $db->getClient()->selectCollection( $db->DB, $table )->drop();
            else
                $db->query( 'DROP TABLE ' . $table );
        }
        expKernelTableSchema::reset();
    }

    /**
     * Empties the record of the fixtures.
     *
     * @param eZDBInterface $db
     */
    protected function clearRunnerRecord( $db )
    {
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261031%wp3%'" );
        $db->query( 'DELETE FROM ' . expMigrationRecord::RUN_TABLE );
    }

    /** @return int the rows of a table */
    protected static function runnerCount( $db, $table, $where = '' )
    {
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ' . $table . ( $where !== '' ? " WHERE $where" : '' ) );
        return (int)( isset( $rows[0]['n'] ) ? $rows[0]['n'] : ( $rows ? reset( $rows[0] ) : 0 ) );
    }

    /**
     * RE-1
     *
     * @param eZDBInterface $db
     * @param bool $withContent whether the folder is published (the clean data is there)
     */
    protected function checkPhpMigrationRun( $db, $withContent = true )
    {
        $this->dropRunnerTable( $db, 'exp_test_wp3_note' );
        $this->clearRunnerRecord( $db );
        $this->assertTrue( expMigrationRecord::exists( $db ), 'exp_migration exists' );
        $migrations = $this->runnerFixtures( $withContent );

        $dry = ( new expMigrationRunner( $this->runnerContext( $db, false ) ) )->run( $migrations );
        $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
        $this->assertSame( count( $migrations ), $dry['summary']['would-apply'] );
        $this->assertNotTrue( expKernelTableSchema::exists( 'exp_test_wp3_note', $db, true ), 'the dry run created nothing' );

        $context = $this->runnerContext( $db );
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $this->assertSame( count( $migrations ), $report['summary']['applied'] );
        $this->assertSame( 'admin', $report['user'] );
        $this->assertSame( expMigrationContext::transactionModeOf( expKernelTableSchema::type( $db ) ), $report['transaction'] );

        expKernelTableSchema::reset();
        $this->assertTrue( expKernelTableSchema::exists( 'exp_test_wp3_note', $db, true ), 'the table exists' );
        $this->assertSame( $withContent ? 2 : 1, self::runnerCount( $db, 'exp_test_wp3_note' ) );
        if ( $withContent )
        {
            $objectId = $context->reference( 'notes_folder_object_id' );
            $this->assertGreaterThan( 0, $objectId );
            eZContentObject::clearCache();
            $object = eZContentObject::fetchByRemoteID( 'exp-test-wp3-notes-folder' );
            $this->assertInstanceOf( 'eZContentObject', $object );
            $this->assertSame( (int)$objectId, (int)$object->attribute( 'id' ) );
            $dataMap = $object->dataMap();
            $this->assertSame( 'Notes', $object->attribute( 'name' ), 'name attribute: '
                               . ( isset( $dataMap['name'] ) ? var_export( $dataMap['name']->attribute( 'data_text' ), true ) . ' in '
                                   . $dataMap['name']->attribute( 'language_code' ) : 'none' )
                               . ', languages ' . implode( ',', (array)$object->availableLanguages() ) );
            $this->assertSame( 14, (int)$object->attribute( 'owner_id' ), 'owned by the run\'s user (admin)' );
            $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)$object->attribute( 'status' ) );
            $node = $object->attribute( 'main_node' );
            $this->assertInstanceOf( 'eZContentObjectTreeNode', $node );
            $this->assertSame( 2, (int)$node->attribute( 'parent_node_id' ) );
        }

        foreach ( $migrations as $migration )
        {
            $row = expMigrationRecord::get( $db, 'site', $migration->id() );
            $this->assertNotNull( $row, $migration->id() . ' recorded' );
            $this->assertSame( 'done', $row['state'] );
            $this->assertSame( $migration->checksum(), $row['checksum'] );
            $this->assertSame( $context->runId(), $row['run_id'] );
            $this->assertSame( 'cli admin', $row['run_by'] );
            $this->assertSame( 'testing', $row['environment'] );
            $this->assertSame( expKernelTableSchema::type( $db ), $row['engine'] );
            $this->assertStringStartsWith( 'tests/tests/kernel/classes/migration/fixtures/runner/', $row['file_path'] );
        }
        $run = expMigrationRecord::run( $db, $context->runId() );
        $this->assertSame( 'done', $run['state'] );
        $this->assertSame( 'runs/' . $context->runId() . '.log', $run['log_file'] );
        $this->assertFileExists( $this->runnerDir() . '/migrations/' . $run['log_file'] );
        $this->assertFileExists( $this->runnerDir() . '/migrations/status/testing.json' );
        $this->assertFalse( expMigrationLock::installation( $this->runnerDir() . '/upgrade.lock' )->isLocked(), 'the lock is free' );

        $again = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $this->runnerFixtures( $withContent ) );
        $this->assertSame( 0, $again['exit'] );
        $this->assertSame( 0, $again['summary']['applied'] );
        $this->assertSame( count( $migrations ), $again['summary']['done'] );
    }

    /**
     * RE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkTransactions( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->dropRunnerTable( $db, 'exp_test_wp3_fail' );
        $definition = new expMigrationDefinition( '20261031131000_wp3_fail', 'site', 'php' );
        $migration = new expMigrationRunnerFailingMigration();
        $migration->setDefinition( $definition );

        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( array( $migration ) );
        $this->assertSame( 1, $report['exit'] );
        $entry = $report['migrations'][0];
        $this->assertSame( 'failed', $entry['action'] );
        $this->assertContains( 'failed on purpose after the table and its row', $entry['messages'] );
        $this->assertSame( expMigrationContext::transactionModeOf( $engine ), $entry['transaction'] );
        $this->assertSame( 'failed', expMigrationRecord::get( $db, 'site', '20261031131000_wp3_fail' )['state'] );

        expKernelTableSchema::reset();
        $exists = expKernelTableSchema::exists( 'exp_test_wp3_fail', $db, true ) === true;
        switch ( expMigrationContext::transactionModeOf( $engine ) )
        {
            case expMigrationContext::TRANSACTION_FULL:
                $this->assertFalse( $exists, "$engine: the table was rolled back with the rest" );
                break;
            case expMigrationContext::TRANSACTION_DATA:
                $this->assertTrue( $exists, "$engine: CREATE TABLE committed at once and stays" );
                // MySQL/MariaDB is back in autocommit after the implicit commit, so the row written next stays; on
                // Oracle the DDL commits by itself and the session's transaction goes on, so the row is rolled back
                if ( $engine === 'oracle' )
                    $this->assertSame( 0, self::runnerCount( $db, 'exp_test_wp3_fail' ), "$engine: the row written after CREATE TABLE is rolled back" );
                else
                    $this->assertSame( 1, self::runnerCount( $db, 'exp_test_wp3_fail' ), "$engine: the row after the implicit commit stays too" );
                $this->assertContains( "rolled back what could be: $engine keeps schema changes made before the failure", $entry['messages'] );
                break;
            default:
                $this->assertTrue( $exists, "$engine: no transaction, the collection stays" );
                $this->assertSame( 1, self::runnerCount( $db, 'exp_test_wp3_fail' ), "$engine: the row stays" );
        }
        $this->assertSame( 0, $db->transactionCounter(), 'no transaction left open' );
        $this->dropRunnerTable( $db, 'exp_test_wp3_fail' );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id = '20261031131000_wp3_fail'" );
    }

    /**
     * RE-3, after RE-1
     *
     * @param eZDBInterface $db
     * @param bool $withContent
     */
    protected function checkRollback( $db, $withContent = true )
    {
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $this->runnerFixtures( $withContent ) );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( $withContent ? array( 'undone', 'undone' ) : array( 'undone' ), array_column( $report['migrations'], 'action' ) );
        expKernelTableSchema::reset();
        $this->assertNotTrue( expKernelTableSchema::exists( 'exp_test_wp3_note', $db, true ) );
        if ( $withContent )
        {
            eZContentObject::clearCache();
            $this->assertNull( eZContentObject::fetchByRemoteID( 'exp-test-wp3-notes-folder' ) );
        }
        $this->assertNull( expMigrationRecord::get( $db, 'site', '20261031130000_wp3_note_table' ) );
    }
    /**
     * RE-4: batches() commits every batch on its own and keeps its checkpoint; a failed run goes on from the last
     * committed batch, and the checkpoint is gone once the migration is applied.
     *
     * @param eZDBInterface $db
     */
    protected function checkBatches( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->dropRunnerTable( $db, 'exp_test_wp16_batch' );
        $schema = array( 'exp_test_wp16_batch' => array(
            'name' => 'exp_test_wp16_batch',
            'fields' => array( 'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                               'done' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array( 'exp_test_wp16_batch' ), $db, $schema, $messages ), implode( '; ', $messages ) );
        for ( $id = 1; $id <= 5; $id++ )
            $db->query( "INSERT INTO exp_test_wp16_batch ( id, done ) VALUES ( $id, 0 )" );
        $checkpoints = $this->runnerDir() . '/checkpoints';
        $options = array( 'lock-file' => $this->runnerDir() . '/upgrade.lock', 'repository' => $this->runnerDir() . '/migrations',
                          'environment' => 'testing', 'root' => self::runnerRoot(), 'user' => eZUser::fetchByName( 'admin' ),
                          'checkpoint-directory' => $checkpoints, 'throttle' => 0 );
        $migration = new expMigrationRunnerBatchMigration();
        $migration->setDefinition( new expMigrationDefinition( '20261101140000_wp16_batch', 'site', 'php' ) );

        $migration->failAt = 4;
        $report = ( new expMigrationRunner( new expMigrationContext( $db, true, $options ) ) )->run( array( $migration ) );
        $this->assertSame( 'failed', $report['migrations'][0]['action'] );
        $this->assertSame( 2, ( new expMigrationCheckpoint( '20261101140000_wp16_batch', $checkpoints ) )->get( 'offset' ), "$engine: 1 and 2 committed" );
        $this->assertSame( 0, $db->transactionCounter(), 'no transaction left open' );
        $done = self::runnerCount( $db, 'exp_test_wp16_batch', 'done = 1' );
        if ( expMigrationContext::transactionModeOf( $engine ) === expMigrationContext::TRANSACTION_NONE )
            $this->assertSame( 4, $done, "$engine: no transaction, 3 and 4 stay changed" );
        else
            $this->assertSame( 2, $done, "$engine: the batch 3, 4 was rolled back" );

        $migration->failAt = null;
        $report = ( new expMigrationRunner( new expMigrationContext( $db, true, $options ) ) )->run( array( $migration ) );
        $this->assertSame( 'applied', $report['migrations'][0]['action'], implode( '; ', $report['migrations'][0]['messages'] ) );
        $this->assertSame( array( 3, 4, 5 ), $migration->handled, "$engine: the second run goes on with 3" );
        $this->assertSame( 5, self::runnerCount( $db, 'exp_test_wp16_batch', 'done = 1' ) );
        $this->assertFileDoesNotExist( $checkpoints . '/20261101140000_wp16_batch.json' );
        $this->assertSame( 'done', expMigrationRecord::get( $db, 'site', '20261101140000_wp16_batch' )['state'] );

        $this->dropRunnerTable( $db, 'exp_test_wp16_batch' );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id = '20261101140000_wp16_batch'" );
    }
}

/** A migration that marks the rows of exp_test_wp16_batch done two at a time and can fail at one (RE-4). */
class expMigrationRunnerBatchMigration extends expMigrationPhp
{
    public $failAt = null;
    public $handled = array();

    public function transactional()
    {
        return false;
    }

    public function up( $context )
    {
        $db = $context->db();
        $this->handled = array();
        $self = $this;
        $count = $this->batches( $context,
            function ( $offset, $limit ) use ( $db ) {
                $rows = $db->arrayQuery( 'SELECT id FROM exp_test_wp16_batch ORDER BY id', array( 'offset' => $offset, 'limit' => $limit ) );
                return array_map( 'intval', array_column( is_array( $rows ) ? $rows : array(), 'id' ) );
            },
            function ( $id ) use ( $db, $self ) {
                $db->query( "UPDATE exp_test_wp16_batch SET done = 1 WHERE id = $id" );
                if ( $self->failAt === $id )
                    throw new RuntimeException( "failed on purpose at $id" );
                $self->handled[] = $id;
            }, 2 );
        return array( "$count rows" );
    }
}

/** A migration that creates a table, writes a row and fails (RE-2). */
class expMigrationRunnerFailingMigration extends expMigrationPhp
{
    public function up( $context )
    {
        $schema = array( 'exp_test_wp3_fail' => array(
            'name' => 'exp_test_wp3_fail',
            'fields' => array( 'id' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ) ),
        ) );
        $messages = array();
        if ( !expKernelTableSchema::install( array( 'exp_test_wp3_fail' ), $context->db(), $schema, $messages ) )
            throw new RuntimeException( 'could not create the table: ' . implode( '; ', $messages ) );
        $context->db()->query( 'INSERT INTO exp_test_wp3_fail ( id ) VALUES ( 1 )' );
        throw new RuntimeException( 'failed on purpose after the table and its row' );
    }
}
