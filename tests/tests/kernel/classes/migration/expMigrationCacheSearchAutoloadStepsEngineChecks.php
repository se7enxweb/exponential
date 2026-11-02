<?php
/**
 * The checks of the step types cache and search and of what a run does after its migrations (expMigrationAfterRun)
 * that run on every engine the same way: SQLite in process (expMigrationCacheSearchAutoloadStepsSqliteTest),
 * MySQL/MariaDB and PostgreSQL in the database job (expMigrationCacheSearchAutoloadStepsDatabaseTest), MongoDB in the
 * live MongoDB suite (expMigrationCacheSearchAutoloadStepsMongoDBTest). Each gives a connection with the kernel schema
 * and the clean data. The step type autoload runs a process of its own and is checked without a database.
 *
 *  CE-1 a run that creates a folder and clears caches: the report lists the caches of its steps (content from the
 *       content step, template-block and tag:ini from the cache step), the deploy that clears them, and the folder is
 *       queued for the search index once (a search step queued it already); a reindex of named objects at once
 *  CE-2 a full reindex of the folders is queued; the objects of a class that does not exist fail the step
 *  CE-3 [RunSettings] Reindex as an option: disabled queues nothing; a dry run clears and queues nothing
 *  CE-4 a rollback of a migration with a cache step lists the caches again
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationContentStepsEngineChecks.php';

trait expMigrationCacheSearchAutoloadStepsEngineChecks
{
    use expMigrationContentStepsEngineChecks;

    /**
     * A context of the runner with more options.
     *
     * @param eZDBInterface $db
     * @param array $options
     * @param bool $apply
     * @return expMigrationContext
     */
    protected function wp10dContext( $db, array $options = array(), $apply = true )
    {
        return new expMigrationContext( $db, $apply, $options + array( 'lock-file' => $this->runnerDir() . '/upgrade.lock', 'repository' => $this->runnerDir() . '/migrations',
                                                                       'environment' => 'testing', 'root' => self::runnerRoot(), 'user' => eZUser::fetchByName( 'admin' ) ) );
    }

    /**
     * @param eZDBInterface $db
     * @param int $objectId
     * @return int how many index_object actions wait for the object
     */
    protected static function wp10dQueued( $db, $objectId )
    {
        $rows = eZPersistentObject::fetchObjectList( eZPendingActions::definition(), array( 'id' ), array( 'action' => 'index_object', 'param' => (string)(int)$objectId ),
                                                     null, null, false );
        return is_array( $rows ) ? count( $rows ) : 0;
    }

    /**
     * @param eZDBInterface $db
     * @param string[] $remoteIds
     */
    protected function wp10dClean( $db, array $remoteIds )
    {
        $this->contentClean( $db, $remoteIds );
        foreach ( (array)eZPersistentObject::fetchObjectList( eZPendingActions::definition(), null, array( 'action' => 'index_object' ), null, null, true ) as $action )
            $action->remove();
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261105%wp10d%'" );
    }

    /**
     * CE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkCachesAndReindex( $db )
    {
        $this->wp10dClean( $db, array( 'wp10d-folder' ) );
        $migrations = $this->contentMigrations( 'caches', array(
            '20261105100000_wp10d_caches.yml' => <<<'YAML'
# description: A folder, caches cleared, the search index
steps:
    - type: content
      mode: create
      class: folder
      parent: 2
      remote_id: wp10d-folder
      fields: { name: WP10d Folder }
      references: { folder: object_id }
    - type: cache
      caches: [ template-block ]
      tags: [ ini ]
    - type: cache
      objects: [ "remote:wp10d-folder" ]
      when: now
    - type: search
      references: { queued: count }
    - type: search
      objects: [ "remote:wp10d-folder" ]
      queue: false
      references: { indexed: count }
YAML
        ) );
        $report = ( new expMigrationRunner( $this->wp10dContext( $db ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $messages = $report['migrations'][0]['messages'];
        $object = self::contentObject( 'wp10d-folder' );
        $this->assertNotNull( $object );
        $id = (int)$object->attribute( 'id' );
        $this->assertContains( 'the caches template-block, tag:ini are cleared after the run', $messages );
        $this->assertContains( "cleared the view caches of the object $id", $messages );
        $this->assertContains( 'queued 1 for the search index: 1 objects the run changed', $messages );
        $this->assertContains( "indexed 1: the objects $id", $messages );
        foreach ( array( 'content', 'tag:ini', 'template-block' ) as $cache )
            $this->assertContains( $cache, $report['caches'] );
        $this->assertStringStartsWith( './console exp:velocity deploy --caches=', $report['deploy'] );
        $this->assertStringContainsString( 'tag:ini', $report['deploy'] );
        $this->assertSame( array( 'mode' => 'queue', 'objects' => 1, 'count' => 0 ), $report['reindex'], 'queued once: the search step did it already' );
        $this->assertSame( 1, self::wp10dQueued( $db, $id ) );
        $this->wp10dClean( $db, array( 'wp10d-folder' ) );
    }

    /**
     * CE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkFullReindex( $db )
    {
        $this->wp10dClean( $db, array() );
        $migrations = $this->contentMigrations( 'full', array(
            '20261105110000_wp10d_full.yml' => "# description: Every folder queued\n- type: search\n  mode: full\n  classes: [ folder ]\n  references: { queued: count }\n",
        ) );
        $report = ( new expMigrationRunner( $this->wp10dContext( $db ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $folders = eZPersistentObject::fetchObjectList( eZContentObject::definition(), array( 'id' ),
                                                        array( 'contentclass_id' => (int)eZContentClass::classIDByIdentifier( 'folder' ), 'status' => eZContentObject::STATUS_PUBLISHED ),
                                                        null, null, false );
        $this->assertNotEmpty( $folders );
        $this->assertContains( 'queued ' . count( $folders ) . ' for the search index: every published object of the classes folder', $report['migrations'][0]['messages'],
                               json_encode( $report['migrations'][0]['messages'] ) );
        foreach ( $folders as $row )
            $this->assertSame( 1, self::wp10dQueued( $db, $row['id'] ) );

        $migrations = $this->contentMigrations( 'full-missing', array(
            '20261105110100_wp10d_full_missing.yml' => "# description: A class that is not there\n- type: search\n  mode: full\n  classes: [ wp10d_no_class ]\n",
        ) );
        $report = ( new expMigrationRunner( $this->wp10dContext( $db ) ) )->run( $migrations );
        $this->assertNotSame( 0, $report['exit'] );
        $this->assertStringContainsString( 'the class wp10d_no_class does not exist', json_encode( $report['migrations'][0]['messages'] ) );
        $this->wp10dClean( $db, array() );
    }

    /**
     * CE-3
     *
     * @param eZDBInterface $db
     */
    protected function checkReindexSettings( $db )
    {
        $this->wp10dClean( $db, array( 'wp10d-quiet', 'wp10d-dry' ) );
        $migrations = $this->contentMigrations( 'quiet', array(
            '20261105120000_wp10d_quiet.yml' => "# description: A folder not reindexed\n- type: content\n  mode: create\n  class: folder\n  parent: 2\n"
                                              . "  remote_id: wp10d-quiet\n  fields: { name: WP10d Quiet }\n",
        ) );
        $report = ( new expMigrationRunner( $this->wp10dContext( $db, array( 'reindex' => 'disabled', 'no-cache-clear' => true ) ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'mode' => 'disabled', 'objects' => 1, 'count' => 0 ), $report['reindex'] );
        $this->assertSame( 0, self::wp10dQueued( $db, self::contentObject( 'wp10d-quiet' )->attribute( 'id' ) ) );

        $migrations = $this->contentMigrations( 'dry', array(
            '20261105120100_wp10d_dry.yml' => "# description: A dry run\n- type: content\n  mode: create\n  class: folder\n  parent: 2\n"
                                            . "  remote_id: wp10d-dry\n  fields: { name: WP10d Dry }\n- type: cache\n  caches: [ template-block ]\n",
        ) );
        $report = ( new expMigrationRunner( $this->wp10dContext( $db, array(), false ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertArrayNotHasKey( 'caches', $report, 'a dry run has no cache list' );
        $this->assertNull( self::contentObject( 'wp10d-dry' ) );
        $this->wp10dClean( $db, array( 'wp10d-quiet', 'wp10d-dry' ) );
    }

    /**
     * CE-4
     *
     * @param eZDBInterface $db
     */
    protected function checkRollbackCaches( $db )
    {
        $this->wp10dClean( $db, array() );
        $migrations = $this->contentMigrations( 'undo', array(
            '20261105130000_wp10d_undo.yml' => "# description: Caches only\n- type: cache\n  caches: [ template-block ]\n- type: search\n",
        ) );
        $report = ( new expMigrationRunner( $this->wp10dContext( $db ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'template-block' ), $report['caches'] );
        $report = ( new expMigrationRunner( $this->wp10dContext( $db ) ) )->rollback( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 'undone', $report['migrations'][0]['action'] );
        $this->assertContains( 'the caches template-block are cleared after the rollback', $report['migrations'][0]['messages'] );
        $this->assertContains( 'search: nothing to undo', $report['migrations'][0]['messages'] );
        $this->assertSame( array( 'template-block' ), $report['caches'] );
        $this->wp10dClean( $db, array() );
    }
}
