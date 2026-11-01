<?php
/**
 * The checks of the content step types (content, location, content_version) that run on every engine the same way:
 * SQLite in process (expMigrationContentStepsSqliteTest), MySQL/MariaDB and PostgreSQL in the database job
 * (expMigrationContentStepsDatabaseTest), MongoDB in the live MongoDB suite (expMigrationContentStepsMongoDBTest).
 * Each gives a connection with the kernel schema and the clean data (one language, eng-US; ger-DE is added).
 *
 *  CE-1 one migration through the runner: a folder created, a second one ensured with a German translation, ensured
 *       again without a change, updated (a new version), the first changed in place (no new version); a second
 *       location with a priority, hidden; sorting of the children; a copy, moved, then deleted; an earlier version
 *       restored, the old versions cleaned up; the second location removed; a delete of a missing object skipped.
 *       The migration cannot undo itself (it updates), so a rollback without down: is refused
 *  CE-2 a migration whose steps can undo themselves (a create with a remote id, a new location, a hide) runs and is
 *       rolled back by their revert(): the article and its location are gone, the folder is shown again; a third
 *       migration deletes the folder with its subtree
 *  CE-3 transactions: a migration that creates an object and then fails an assert. PostgreSQL, SQLite and
 *       MySQL/MariaDB roll the object back; MongoDB keeps it (no transactions)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

trait expMigrationContentStepsEngineChecks
{
    use expMigrationRunnerEngineChecks;

    /** Skips without symfony/yaml; makes the step types of the settings be read again. */
    protected function contentBegin()
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        expMigrationStepRegistry::forget();
        expMigrationIdMap::forget();
    }

    protected function contentEnd()
    {
        expMigrationStepRegistry::forget();
        expMigrationIdMap::forget();
    }

    /**
     * Writes migrations into a folder of the test and loads them.
     *
     * @param string $folder
     * @param array $files file name => YAML text
     * @return expMigration[]
     */
    protected function contentMigrations( $folder, array $files )
    {
        $dir = $this->runnerDir() . '/' . $folder;
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        foreach ( $files as $name => $text )
            file_put_contents( "$dir/$name", $text );
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        return $migrations;
    }

    /** ger-DE as a second language of content. */
    protected function contentLanguages()
    {
        if ( !eZContentLanguage::fetchByLocale( 'ger-DE' ) )
            eZContentLanguage::addLanguage( 'ger-DE', 'Deutsch' );
        unset( $GLOBALS['eZContentLanguageList'], $GLOBALS['eZContentLanguageMask'] );
        eZContentLanguage::expireCache();
    }

    /**
     * @param string $remoteId
     * @return eZContentObject|null fresh from the database
     */
    protected static function contentObject( $remoteId )
    {
        $found = eZContentObject::fetchByRemoteID( $remoteId, false );
        if ( !is_array( $found ) )
            return null;
        eZContentObject::clearCache( array( (int)$found['id'] ) );
        $object = eZContentObject::fetch( (int)$found['id'] );
        return $object instanceof eZContentObject ? $object : null;
    }

    /**
     * Removes what an earlier run of the checks left (MongoDB keeps its database for the class).
     *
     * @param eZDBInterface $db
     * @param string[] $remoteIds
     */
    protected function contentClean( $db, array $remoteIds )
    {
        foreach ( $remoteIds as $remoteId )
        {
            $object = self::contentObject( $remoteId );
            if ( $object )
            {
                $db->begin();
                $object->purge();
                $db->commit();
            }
        }
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261102%wp10a%'" );
    }

    /**
     * CE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkContentAndLocations( $db )
    {
        $this->contentClean( $db, array( 'wp10a-news', 'wp10a-first' ) );
        $this->contentLanguages();
        $migrations = $this->contentMigrations( 'content', array( '20261102100000_wp10a_content.yml' => <<<'YAML'
# description: A news folder with a first folder in two languages, its locations and versions
steps:
    - type: content
      mode: create
      class: folder
      parent: 2
      remote_id: wp10a-news
      fields: { name: WP10 News, short_name: news }
      references: { news: node_id, news_object: object_id }
    - type: content
      mode: ensure
      class: folder
      parent: "${ref.news}"
      remote_id: wp10a-first
      language: eng-US
      fields: { name: First }
      translations:
          ger-DE: { name: Erster }
      references: { first: node_id, first_created: created }
    - type: content
      mode: ensure
      class: folder
      parent: "${ref.news}"
      remote_id: wp10a-first
      language: eng-US
      fields: { name: First }
      translations:
          ger-DE: { name: Erster }
      references: { first_changed_again: changed }
    - type: content
      mode: update
      object: remote:wp10a-first
      fields: { name: First updated }
      references: { first_version: version }
    - type: content
      mode: update
      node: "${ref.news}"
      in_place: true
      fields: { short_name: corrected }
    - type: location
      mode: create
      object: remote:wp10a-first
      parent: 2
      priority: 7
      references: { second: node_id }
    - type: location
      mode: hide
      node: "${ref.second}"
    - type: location
      mode: update
      node: "${ref.news}"
      sort_field: name
      sort_order: asc
    - type: location
      mode: copy
      node: "${ref.first}"
      parent: 2
      references: { copy: node_id, copy_object: object_id }
    - type: location
      mode: move
      node: "${ref.copy}"
      parent: "${ref.news}"
    - type: content_version
      mode: restore
      object: remote:wp10a-first
      version: 1
    - type: content_version
      mode: cleanup
      object: remote:wp10a-first
      keep: 0
      references: { removed: removed }
    - type: location
      mode: delete
      node: "${ref.second}"
    - type: content
      mode: delete
      object: "${ref.copy_object}"
    - type: content
      mode: delete
      object: remote:wp10a-no-such-object
      missing: skip
YAML
        ) );
        $this->assertFalse( $migrations[0]->reversible(), 'ensure and update cannot undo themselves' );
        $this->assertSame( array( 'content' ), array_values( array_diff( $migrations[0]->caches(), array( 'template-block' ) ) ) );

        $dry = ( new expMigrationRunner( $this->runnerContext( $db, false ) ) )->run( $migrations );
        $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
        $this->assertContains( 'create a "folder" below node 2 with the remote id wp10a-news (the default language: name, short_name)', $dry['migrations'][0]['plan'] );
        $this->assertNull( self::contentObject( 'wp10a-news' ), 'the dry run created nothing' );

        $context = $this->runnerContext( $db );
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $messages = $report['migrations'][0]['messages'];
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( $messages ) );
        $this->assertSame( 'applied', $report['migrations'][0]['action'] );

        $news = self::contentObject( 'wp10a-news' );
        $first = self::contentObject( 'wp10a-first' );
        $this->assertNotNull( $news );
        $this->assertNotNull( $first );
        $newsNode = (int)$news->attribute( 'main_node_id' );
        $this->assertSame( $newsNode, $context->reference( 'news' ) );
        $this->assertSame( 2, (int)$news->attribute( 'main_node' )->attribute( 'parent_node_id' ) );
        // in place: the published version changed, no new one
        $this->assertSame( 1, (int)$news->attribute( 'current_version' ) );
        $this->assertSame( 'corrected', $news->fetchDataMap( false, 'eng-US' )['short_name']->toString() );
        $this->assertSame( 'corrected', $news->attribute( 'name' ), 'the name follows the change in place (a folder is named by short_name)' );
        // ensured, ensured again without a change, updated, restored, cleaned up
        $this->assertTrue( $context->reference( 'first_created' ) );
        $this->assertFalse( $context->reference( 'first_changed_again' ), json_encode( $messages ) );
        $this->assertSame( 3, $context->reference( 'first_version' ) );
        $this->assertSame( 4, (int)$first->attribute( 'current_version' ) );
        $this->assertSame( 'First', $first->fetchDataMap( false, 'eng-US' )['name']->toString() );
        $this->assertSame( 'Erster', $first->fetchDataMap( false, 'ger-DE' )['name']->toString() );
        $this->assertEqualsCanonicalizing( array( 'eng-US', 'ger-DE' ), array_keys( $first->allLanguages() ) );
        $this->assertSame( 3, $context->reference( 'removed' ) );
        $this->assertCount( 1, $first->versions() );
        // the second location was added with its priority, hidden, then removed again; the copy moved and deleted
        $this->assertContains( 'hid node ' . $context->reference( 'second' ), $messages );
        $this->assertContains( 'set the priority of node ' . $context->reference( 'second' ) . ' to 7', $messages );
        $this->assertContains( 'moved node ' . $context->reference( 'copy' ) . " below node $newsNode", $messages );
        $this->assertCount( 1, eZContentObjectTreeNode::fetchByContentObjectID( (int)$first->attribute( 'id' ) ) );
        $this->assertNull( eZContentObject::fetch( (int)$context->reference( 'copy_object' ) ) );
        $this->assertSame( 1, (int)eZContentObjectTreeNode::subTreeCountByNodeID( array( 'Depth' => 1, 'DepthOperator' => 'eq' ), $newsNode ) );
        $newsNodeObject = eZContentObjectTreeNode::fetch( $newsNode );
        $this->assertSame( eZContentObjectTreeNode::sortFieldID( 'name' ), (int)$newsNodeObject->attribute( 'sort_field' ) );
        $this->assertSame( 1, (int)$newsNodeObject->attribute( 'sort_order' ) );
        $this->assertContains( 'the object "remote:wp10a-no-such-object" is not there: skipped (missing: skip)', $messages );

        $again = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 0, $again['summary']['applied'] );

        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $migrations );
        $this->assertNotSame( 0, $rollback['exit'], 'a migration that cannot undo itself is not rolled back' );
        $this->assertNotNull( self::contentObject( 'wp10a-first' ) );
    }

    /**
     * CE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkContentRevert( $db )
    {
        $this->contentClean( $db, array( 'wp10a-folder', 'wp10a-article' ) );
        $migrations = $this->contentMigrations( 'revert', array(
            '20261102110000_wp10a_folder.yml' => "# description: A folder for the article\n- type: content\n  mode: create\n  class: folder\n  parent: 2\n"
                                               . "  remote_id: wp10a-folder\n  fields: { name: WP10 Folder }\n",
            '20261102110100_wp10a_article.yml' => <<<'YAML'
# description: An article in two places, its folder hidden
# requires: 20261102110000_wp10a_folder
- type: content
  mode: create
  class: article
  parent: object-remote:wp10a-folder
  remote_id: wp10a-article
  fields: { title: The article }
- type: location
  mode: create
  object: remote:wp10a-article
  parent: 2
- type: location
  mode: hide
  node: object-remote:wp10a-folder
YAML
        ) );
        $this->assertTrue( $migrations[1]->reversible() );
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $article = self::contentObject( 'wp10a-article' );
        $folder = self::contentObject( 'wp10a-folder' );
        $this->assertSame( 'The article', $article->attribute( 'name' ) );
        $this->assertCount( 2, eZContentObjectTreeNode::fetchByContentObjectID( (int)$article->attribute( 'id' ) ) );
        $this->assertTrue( (bool)eZContentObjectTreeNode::fetch( (int)$folder->attribute( 'main_node_id' ) )->attribute( 'is_hidden' ) );

        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( array( $migrations[1] ) );
        $this->assertSame( 0, $rollback['exit'], implode( '; ', $rollback['errors'] ) . ' ' . json_encode( array_column( $rollback['migrations'], 'messages' ) ) );
        $this->assertSame( array( 'undone' ), array_column( $rollback['migrations'], 'action' ) );
        $this->assertNull( self::contentObject( 'wp10a-article' ), 'the article is removed again' );
        $this->assertFalse( (bool)eZContentObjectTreeNode::fetch( (int)$folder->attribute( 'main_node_id' ) )->attribute( 'is_hidden' ), 'the folder is shown again' );

        $cleanup = $this->contentMigrations( 'revert-cleanup', array(
            '20261102110200_wp10a_cleanup.yml' => "# description: The folder deleted\n- type: content\n  mode: delete\n  node: object-remote:wp10a-folder\n  trash: false\n" ) );
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $cleanup );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertNull( self::contentObject( 'wp10a-folder' ) );
        $this->contentClean( $db, array( 'wp10a-news', 'wp10a-first' ) );
    }

    /**
     * CE-3
     *
     * @param eZDBInterface $db
     */
    protected function checkContentTransactions( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->contentClean( $db, array( 'wp10a-fail' ) );
        $migrations = $this->contentMigrations( 'fail', array(
            '20261102120000_wp10a_fail.yml' => "# description: Creates a folder and fails\n- type: content\n  mode: create\n  class: folder\n  parent: 2\n"
                                             . "  remote_id: wp10a-fail\n  fields: { name: Kept only without transactions }\n"
                                             . "- type: assert\n  that: false\n  message: failed on purpose after the folder\n" ) );
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 1, $report['exit'] );
        $this->assertSame( 'failed', $report['migrations'][0]['action'] );
        eZContentObject::clearCache();
        if ( expMigrationContext::transactionModeOf( $engine ) === expMigrationContext::TRANSACTION_NONE )
            $this->assertNotNull( self::contentObject( 'wp10a-fail' ), "$engine: no transaction, the folder stays" );
        else
            $this->assertNull( self::contentObject( 'wp10a-fail' ), "$engine: the folder was rolled back" );
        $this->assertSame( 0, $db->transactionCounter(), 'no transaction left open' );
        $this->contentClean( $db, array( 'wp10a-fail' ) );
    }
}
