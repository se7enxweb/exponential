<?php
/**
 * The checks of the step types tag, url_alias, trash, file and image that run on every engine the same way: SQLite in
 * process (expMigrationTagAliasTrashFileStepsSqliteTest), MySQL/MariaDB and PostgreSQL in the database job
 * (expMigrationTagAliasTrashFileStepsDatabaseTest), MongoDB in the live MongoDB suite
 * (expMigrationTagAliasTrashFileStepsMongoDBTest). Each gives a connection with the kernel schema and the clean data.
 *
 *  TE-1 URL aliases and wildcards: a folder gets an address, a global address leads to content/search, a wildcard is
 *       ensured; ensured again nothing changes; the address deleted, every custom address of the folder deleted, the
 *       wildcard deleted; a migration that creates an address is rolled back and the address is gone
 *  TE-2 the trash: a folder moved to the trash by object and restored where it was, moved by node, purged; a second
 *       one moved and the whole trash emptied; a move rolled back restores the object
 *  TE-3 files and images: a PDF and an image next to the migration attached to a file and an image object (new
 *       versions), the alternative text, focal point and crop rectangle of the image set in place; a file outside the
 *       folder of the migration fails the migration
 *  TE-4 tags with eztags (its classes from the installation, EXP_TEST_EZTAGS_PATH or extension/eztags, and its tables
 *       made by the test): a tag in two languages, a child with a synonym, ensured again, updated, moved, merged,
 *       deleted, and a create rolled back. Without eztags: the step fails and says so, missing: skip passes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationContentStepsEngineChecks.php';

trait expMigrationTagAliasTrashFileStepsEngineChecks
{
    use expMigrationContentStepsEngineChecks;

    /**
     * Runs migrations and checks they were applied.
     *
     * @param eZDBInterface $db
     * @param expMigration[] $migrations
     * @return array the report
     */
    protected function wp10cRun( $db, array $migrations )
    {
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        return $report;
    }

    /**
     * @param array $report
     * @return string[] the messages of every migration of a report
     */
    protected static function wp10cMessages( array $report )
    {
        $lines = array();
        foreach ( $report['migrations'] as $migration )
            $lines = array_merge( $lines, (array)$migration['messages'] );
        return $lines;
    }

    /**
     * @param eZDBInterface $db
     * @param string[] $remoteIds
     */
    protected function wp10cClean( $db, array $remoteIds )
    {
        $this->contentClean( $db, $remoteIds );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261104%wp10c%'" );
    }

    /**
     * The element an address leads to, or null.
     *
     * @param string $path
     * @return string|null its action
     */
    protected static function wp10cAction( $path )
    {
        foreach ( (array)eZURLAliasML::fetchByPath( $path ) as $element )
            if ( (string)$element->attribute( 'action' ) !== 'nop:' )
                return (string)$element->attribute( 'action' );
        return null;
    }

    /**
     * TE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkUrlAliases( $db )
    {
        $this->wp10cClean( $db, array( 'wp10c-about' ) );
        foreach ( array( 'wp10c-old/*' ) as $source )
        {
            $wildcard = eZURLWildcard::fetchBySourceURL( $source );
            if ( $wildcard )
                $wildcard->remove();
        }
        $migrations = $this->contentMigrations( 'aliases', array(
            '20261104100000_wp10c_aliases.yml' => <<<'YAML'
# description: A folder with addresses, a global address and a wildcard
steps:
    - type: content
      mode: create
      class: folder
      parent: 2
      remote_id: wp10c-about
      fields: { name: WP10c About }
      references: { about: node_id }
    - type: url_alias
      mode: create
      path: wp10c-company/about-us
      node: "${ref.about}"
      references: { about_alias: path }
    - type: url_alias
      mode: ensure
      path: wp10c-short
      node: "object-remote:wp10c-about"
      redirect: false
    - type: url_alias
      mode: ensure
      path: wp10c-short
      node: "object-remote:wp10c-about"
      references: { short_changed: changed }
    - type: url_alias
      mode: ensure
      path: wp10c-find
      destination: content/search
    - type: url_alias
      mode: ensure
      wildcard: "wp10c-old/*"
      destination: "wp10c-new/{1}"
    - type: url_alias
      mode: ensure
      wildcard: "wp10c-old/*"
      destination: "wp10c-new/{1}"
      references: { wildcard_changed: changed }
YAML
        ) );
        $report = $this->wp10cRun( $db, $migrations );
        $about = self::contentObject( 'wp10c-about' );
        $node = (int)$about->attribute( 'main_node_id' );
        $this->assertSame( "eznode:$node", self::wp10cAction( 'wp10c-company/about-us' ) );
        $this->assertSame( "eznode:$node", self::wp10cAction( 'wp10c-short' ) );
        $this->assertSame( 'module:content/search', self::wp10cAction( 'wp10c-find' ) );
        // the whole rows (fetchByPath() reads only some columns): custom aliases, the short one without a redirect
        $row = function ( $parent, $text ) {
            return eZPersistentObject::fetchObject( eZURLAliasML::definition(), null, array( 'parent' => $parent, 'text_md5' => md5( $text ) ), false );
        };
        $short = $row( 0, 'wp10c-short' );
        $this->assertSame( array( 1, 1, 0 ), array( (int)$short['is_alias'], (int)$short['is_original'], (int)$short['alias_redirects'] ), 'redirect: false' );
        $find = $row( 0, 'wp10c-find' );
        $this->assertSame( array( 1, 1 ), array( (int)$find['is_alias'], (int)$find['alias_redirects'] ) );
        $wildcard = eZURLWildcard::fetchBySourceURL( 'wp10c-old/*' );
        $this->assertInstanceOf( 'eZURLWildcard', $wildcard );
        $this->assertSame( 'wp10c-new/{1}', (string)$wildcard->attribute( 'destination_url' ) );
        $this->assertSame( eZURLWildcard::TYPE_FORWARD, (int)$wildcard->attribute( 'type' ) );
        $messages = self::wp10cMessages( $report );
        $this->assertContains( "the address wp10c-short leads to eznode:$node already", $messages );
        $this->assertContains( 'the wildcard wp10c-old/* leads to wp10c-new/{1} already', $messages );
        $this->assertSame( "eznode:$node", (string)eZURLAliasML::fetchByPath( 'wp10c-company/about-us' )[0]->attribute( 'action' ) );

        $deletes = $this->contentMigrations( 'aliases-delete', array(
            '20261104100100_wp10c_aliases_delete.yml' => <<<'YAML'
# description: The addresses and the wildcard deleted again
steps:
    - type: url_alias
      mode: delete
      path: wp10c-find
    - type: url_alias
      mode: delete
      node: "object-remote:wp10c-about"
      which: all
      references: { removed: removed }
    - type: url_alias
      mode: delete
      wildcard: "wp10c-old/*"
    - type: url_alias
      mode: delete
      wildcard: "wp10c-old/*"
      missing: skip
YAML
        ) );
        $report = $this->wp10cRun( $db, $deletes );
        $this->assertNull( self::wp10cAction( 'wp10c-find' ) );
        $this->assertNull( self::wp10cAction( 'wp10c-short' ) );
        $this->assertNull( self::wp10cAction( 'wp10c-company/about-us' ) );
        $this->assertNull( eZURLWildcard::fetchBySourceURL( 'wp10c-old/*' ) );
        $this->assertNotNull( self::wp10cAction( (string)eZContentObjectTreeNode::fetch( $node )->attribute( 'url_alias' ) ),
                              'the address of the name stays' );
        $this->assertContains( 'the wildcard wp10c-old/* is not there: skipped (missing: skip)', self::wp10cMessages( $report ) );

        // a create undoes itself
        $revert = $this->contentMigrations( 'aliases-revert', array(
            '20261104100200_wp10c_alias_revert.yml' => "# description: An address that is rolled back\n- type: url_alias\n  mode: create\n"
                                                     . "  path: wp10c-reverted\n  node: \"object-remote:wp10c-about\"\n" ) );
        $this->assertTrue( $revert[0]->reversible() );
        $this->wp10cRun( $db, $revert );
        $this->assertSame( "eznode:$node", self::wp10cAction( 'wp10c-reverted' ) );
        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $revert );
        $this->assertSame( 0, $rollback['exit'], implode( '; ', $rollback['errors'] ) );
        $this->assertNull( self::wp10cAction( 'wp10c-reverted' ) );
        $this->wp10cClean( $db, array( 'wp10c-about' ) );
    }

    /**
     * TE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkTrash( $db )
    {
        $this->wp10cClean( $db, array( 'wp10c-trashed', 'wp10c-second' ) );
        $migrations = $this->contentMigrations( 'trash', array(
            '20261104110000_wp10c_trash.yml' => <<<'YAML'
# description: A folder to the trash and back, then purged
steps:
    - type: content
      mode: create
      class: folder
      parent: 2
      remote_id: wp10c-trashed
      fields: { name: WP10c Trashed }
    - type: trash
      mode: move
      object: "remote:wp10c-trashed"
    - type: trash
      mode: restore
      object: "remote:wp10c-trashed"
      references: { restored_node: node_id }
    - type: trash
      mode: move
      node: "object-remote:wp10c-trashed"
    - type: trash
      mode: purge
      object: "remote:wp10c-trashed"
      references: { purged: purged }
    - type: trash
      mode: purge
      object: "remote:wp10c-trashed"
      missing: skip
    - type: content
      mode: create
      class: folder
      parent: 2
      remote_id: wp10c-second
      fields: { name: WP10c Second }
    - type: trash
      mode: move
      object: "remote:wp10c-second"
    - type: trash
      mode: purge
      all: true
YAML
        ) );
        $report = $this->wp10cRun( $db, $migrations );
        $messages = self::wp10cMessages( $report );
        $this->assertNull( self::contentObject( 'wp10c-trashed' ), 'purged' );
        $this->assertNull( self::contentObject( 'wp10c-second' ), 'the trash was emptied' );
        $this->assertSame( 0, (int)eZContentObjectTrashNode::trashListCount( array( 'Limitation' => array() ) ) );
        $this->assertContains( 'the object "remote:wp10c-trashed" is not in the trash: skipped (missing: skip)', $messages );
        $restored = array_values( array_filter( $messages, function ( $line ) { return strpos( $line, 'restored the object' ) === 0; } ) );
        $this->assertCount( 1, $restored );
        $this->assertStringContainsString( 'below node 2', $restored[0] );

        // a move undoes itself: the object comes back where it was
        $this->wp10cClean( $db, array( 'wp10c-back' ) );
        $setup = $this->contentMigrations( 'trash-revert', array(
            '20261104110100_wp10c_back.yml' => "# description: A folder\n- type: content\n  mode: create\n  class: folder\n  parent: 2\n  remote_id: wp10c-back\n"
                                             . "  fields: { name: WP10c Back }\n",
            '20261104110200_wp10c_back_trash.yml' => "# description: The folder to the trash\n# requires: 20261104110100_wp10c_back\n"
                                                   . "- type: trash\n  mode: move\n  object: \"remote:wp10c-back\"\n" ) );
        $this->wp10cRun( $db, $setup );
        eZContentObject::clearCache();
        $this->assertSame( eZContentObject::STATUS_ARCHIVED, (int)eZContentObject::fetchByRemoteID( 'wp10c-back', false )['status'] );
        $this->assertTrue( $setup[1]->reversible() );
        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( array( $setup[1] ) );
        $this->assertSame( 0, $rollback['exit'], implode( '; ', $rollback['errors'] ) . ' ' . json_encode( array_column( $rollback['migrations'], 'messages' ) ) );
        $back = self::contentObject( 'wp10c-back' );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)$back->attribute( 'status' ) );
        $this->assertSame( 2, (int)$back->attribute( 'main_node' )->attribute( 'parent_node_id' ) );
        $this->wp10cClean( $db, array( 'wp10c-back' ) );
    }

    /**
     * TE-3
     *
     * @param eZDBInterface $db
     */
    protected function checkFilesAndImages( $db )
    {
        if ( !function_exists( 'imagecreatetruecolor' ) )
            $this->markTestSkipped( 'GD is not loaded: no image to attach' );
        $this->wp10cClean( $db, array( 'wp10c-report', 'wp10c-photo' ) );
        $dir = $this->runnerDir() . '/files';
        mkdir( "$dir/media", 0775, true );
        file_put_contents( "$dir/media/report.pdf", "%PDF-1.4\n1 0 obj << >> endobj\ntrailer << >>\n%%EOF\n" );
        $image = imagecreatetruecolor( 40, 30 );
        imagefilledrectangle( $image, 0, 0, 39, 29, imagecolorallocate( $image, 200, 80, 20 ) );
        imagepng( $image, "$dir/media/photo.png" );
        file_put_contents( $this->runnerDir() . '/outside.pdf', '%PDF-1.4' );
        $migrations = $this->contentMigrations( 'files', array(
            '20261104120000_wp10c_files.yml' => <<<'YAML'
# description: A report and a photo with their files
steps:
    - type: content
      mode: create
      class: file
      parent: 2
      remote_id: wp10c-report
      fields: { name: WP10c Report }
    - type: content
      mode: create
      class: image
      parent: 2
      remote_id: wp10c-photo
      fields: { name: WP10c Photo }
    - type: file
      object: "remote:wp10c-report"
      field: file
      file: media/report.pdf
      name: Annual report.pdf
      references: { report_version: version }
    - type: image
      object: "remote:wp10c-photo"
      field: image
      file: media/photo.png
      alt: An orange square
    - type: image
      node: "object-remote:wp10c-photo"
      field: image
      focal: { x: 0.25, y: 0.5 }
      crop: { x: 0, y: 0, width: 0.5, height: 1 }
      in_place: true
      references: { photo_version: version }
YAML
        ) );
        $this->wp10cRun( $db, $migrations );
        $report = self::contentObject( 'wp10c-report' );
        $this->assertSame( 2, (int)$report->attribute( 'current_version' ), 'the file came as a new version' );
        $binary = $report->dataMap()['file']->content();
        $this->assertInstanceOf( 'eZBinaryFile', $binary );
        $this->assertSame( 'Annual report.pdf', (string)$binary->attribute( 'original_filename' ) );
        $photo = self::contentObject( 'wp10c-photo' );
        $this->assertSame( 2, (int)$photo->attribute( 'current_version' ), 'the focal point was set in place' );
        $handler = $photo->dataMap()['image']->content();
        $this->assertSame( 'An orange square', (string)$handler->attribute( 'alternative_text' ) );
        $original = $handler->attribute( 'original' );
        $this->assertSame( 40, (int)$original['width'] );
        $this->assertSame( array( 'x' => 0.25, 'y' => 0.5 ), $handler->focalPoint() );
        $this->assertEquals( array( 'x' => 0.0, 'y' => 0.0, 'width' => 0.5, 'height' => 1.0 ), $handler->cropRectangle() );

        // a link out of the folder of the migration: refused when the step runs, the migration fails
        mkdir( $this->runnerDir() . '/files-outside', 0775, true );
        symlink( $this->runnerDir() . '/outside.pdf', $this->runnerDir() . '/files-outside/link.pdf' );
        $outside = $this->contentMigrations( 'files-outside', array(
            '20261104120100_wp10c_outside.yml' => "# description: A file from outside the folder\n- type: file\n  object: \"remote:wp10c-report\"\n"
                                                . "  field: file\n  file: link.pdf\n" ) );
        $failed = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $outside );
        $this->assertSame( 1, $failed['exit'] );
        $this->assertStringContainsString( 'leads out of the folder of the migration', implode( '; ', $failed['errors'] ) . json_encode( $failed['migrations'] ) );
        $this->assertSame( 2, (int)self::contentObject( 'wp10c-report' )->attribute( 'current_version' ), 'the report was not changed' );
        $this->wp10cClean( $db, array( 'wp10c-report', 'wp10c-photo' ) );
    }

    /**
     * Makes the classes of eztags loadable and its tables, when the extension can be found: the installation's, the
     * folder of EXP_TEST_EZTAGS_PATH, or extension/eztags of the checkout.
     *
     * @param eZDBInterface $db
     * @return bool whether eztags is ready
     */
    protected function wp10cTags( $db )
    {
        $path = null;
        if ( class_exists( 'eZTagsObject' ) )
        {
            $path = dirname( ( new ReflectionClass( 'eZTagsObject' ) )->getFileName(), 2 );
        }
        else
        {
            foreach ( array( (string)getenv( 'EXP_TEST_EZTAGS_PATH' ), self::runnerRoot() . '/extension/eztags' ) as $candidate )
            {
                if ( $candidate !== '' && is_file( "$candidate/classes/eztagsobject.php" ) )
                {
                    $path = $candidate;
                    break;
                }
            }
            if ( $path === null )
                return false;
            $map = array( 'eZTagsObject' => 'eztagsobject.php', 'eZTagsKeyword' => 'eztagskeyword.php', 'eZTagsAttributeLinkObject' => 'eztagsattributelinkobject.php' );
            spl_autoload_register( function ( $class ) use ( $path, $map ) {
                if ( isset( $map[$class] ) )
                    require_once "$path/classes/" . $map[$class];
            } );
        }
        if ( !is_file( "$path/share/db_schema.dba" ) )
            return false;
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array(), $db, "$path/share/db_schema.dba", $messages ), 'the tables of eztags: ' . implode( '; ', $messages ) );
        foreach ( array( 'eztags_attribute_link', 'eztags_keyword', 'eztags' ) as $table )
            $db->query( "DELETE FROM $table" );
        return expMigrationIdentifier::tagsInstalled();
    }

    /**
     * TE-4
     *
     * @param eZDBInterface $db
     */
    protected function checkTags( $db )
    {
        $this->contentLanguages();
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261104%wp10c%'" );
        if ( !$this->wp10cTags( $db ) )
        {
            $failing = $this->contentMigrations( 'tags-missing', array(
                '20261104130000_wp10c_no_tags.yml' => "# description: A tag without eztags\n- type: tag\n  mode: create\n  keyword: Topics\n" ) );
            $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $failing );
            $this->assertSame( 1, $report['exit'] );
            $this->assertStringContainsString( 'needs the extension eztags, which is not installed here', implode( '; ', $report['errors'] ) . json_encode( $report['migrations'] ) );
            $skipped = $this->contentMigrations( 'tags-skipped', array(
                '20261104130100_wp10c_no_tags_skip.yml' => "# description: A tag skipped without eztags\n- type: tag\n  mode: create\n  keyword: Topics\n  missing: skip\n" ) );
            $report = $this->wp10cRun( $db, $skipped );
            $this->assertContains( 'the extension eztags is not installed: skipped (missing: skip)', self::wp10cMessages( $report ) );
            return;
        }
        $migrations = $this->contentMigrations( 'tags', array(
            '20261104130000_wp10c_tags.yml' => <<<'YAML'
# description: Tags with translations and synonyms, moved, merged and deleted
steps:
    - type: tag
      mode: create
      remote_id: wp10c-topics
      language: eng-US
      keyword: Topics
      keywords: { ger-DE: Themen }
      references: { topics: tag_id }
    - type: tag
      mode: ensure
      parent: "path:Topics"
      remote_id: wp10c-sport
      keyword: Sport
      synonyms: [ Sports ]
      references: { sport: tag_id }
    - type: tag
      mode: ensure
      parent: "remote:wp10c-topics"
      remote_id: wp10c-sport
      keyword: Sport
      synonyms: [ Sports ]
      references: { sport_changed: changed }
    - type: tag
      mode: update
      tag: "path:Topics/Sport"
      keywords: { ger-DE: Sport }
      always_available: true
    - type: tag
      mode: create
      keyword: Athletics
      references: { athletics: tag_id }
    - type: tag
      mode: move
      tag: "${ref.athletics}"
      parent: "remote:wp10c-topics"
    - type: tag
      mode: merge
      tag: "path:Topics/Athletics"
      into: "remote:wp10c-sport"
      references: { merged_into: path }
    - type: tag
      mode: create
      keyword: Old
      remote_id: wp10c-old
    - type: tag
      mode: delete
      tag: "remote:wp10c-old"
YAML
        ) );
        $context = $this->runnerContext( $db );
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $topics = eZTagsObject::fetchByRemoteID( 'wp10c-topics', true );
        $sport = eZTagsObject::fetchByRemoteID( 'wp10c-sport', true );
        $this->assertInstanceOf( 'eZTagsObject', $topics );
        $this->assertSame( 'Themen', (string)eZTagsKeyword::fetch( (int)$topics->attribute( 'id' ), 'ger-DE' )->attribute( 'keyword' ) );
        $this->assertSame( (int)$topics->attribute( 'id' ), (int)$sport->attribute( 'parent_id' ) );
        $this->assertSame( '/' . $topics->attribute( 'id' ) . '/' . $sport->attribute( 'id' ) . '/', (string)$sport->attribute( 'path_string' ) );
        $this->assertSame( 2, (int)$sport->attribute( 'depth' ) );
        $this->assertTrue( $sport->isAlwaysAvailable() );
        $this->assertSame( 'Sport', (string)eZTagsKeyword::fetch( (int)$sport->attribute( 'id' ), 'ger-DE' )->attribute( 'keyword' ) );
        $synonyms = $sport->getSynonyms( true );
        $this->assertCount( 1, $synonyms, 'the synonym once, the ensure did not add it again' );
        $this->assertSame( 'Sports', (string)$synonyms[0]->getKeyword( 'eng-US' ) );
        $this->assertNull( eZTagsObject::fetchByUrl( 'Topics/Athletics', true ), 'merged away' );
        $this->assertNull( eZTagsObject::fetchByRemoteID( 'wp10c-old', true ), 'deleted' );
        $this->assertSame( 'Topics/Sport', $context->reference( 'merged_into' ) );

        $revert = $this->contentMigrations( 'tags-revert', array(
            '20261104130100_wp10c_tag_revert.yml' => "# description: A tag rolled back\n- type: tag\n  mode: create\n  parent: \"remote:wp10c-topics\"\n  keyword: Rolled back\n" ) );
        $this->assertTrue( $revert[0]->reversible() );
        $this->wp10cRun( $db, $revert );
        $this->assertInstanceOf( 'eZTagsObject', eZTagsObject::fetchByUrl( 'Topics/Rolled back', true ) );
        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $revert );
        $this->assertSame( 0, $rollback['exit'], implode( '; ', $rollback['errors'] ) );
        $this->assertNull( eZTagsObject::fetchByUrl( 'Topics/Rolled back', true ) );
        foreach ( array( 'eztags_attribute_link', 'eztags_keyword', 'eztags' ) as $table )
            $db->query( "DELETE FROM $table" );
    }
}
