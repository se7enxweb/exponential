<?php
/**
 * The checks of the recording of changes that run on every engine the same way: SQLite in process
 * (expMigrationRecordingSqliteTest), MySQL/MariaDB and PostgreSQL in the database job (expMigrationRecordingDatabaseTest),
 * MongoDB in the live MongoDB suite (expMigrationRecordingMongoDBTest). Each gives a connection with the kernel schema
 * and the clean data, which it may write to; the recording tables are made by their upgrade step.
 *
 *  RC-1 the upgrade step 6.0.15-migration-recording-tables is missing, makes both tables, then is present; again: nothing
 *  RC-2 a session recording of the administrator: a class created (recorded as class/edit records it), a content
 *       object of it published below node 2, its children's sort changed, a role assigned to a user group, the
 *       location hidden; each is recorded with remote ids and identifiers, the step it stands for, and the values to
 *       repeat it. A change by another user is not in it; a site-wide window records it. Stop: nothing more is
 *       recorded. The plan has one step per thing changed; the generator reports the step types missing, and with
 *       the installation's step types writes a migration that passes the check (a dry run writes no file). Discard
 *       removes everything.
 *  RC-3 a user published as content: the account and the personal fields are left out of the recording, and its
 *       migration names the account by secrets
 *  RC-4 a migration of what is there (a class, a role, a section, content to a depth) passes the check; unknown
 *       things are named
 *  RC-5 a site-wide window of time: its end kept while it runs, nothing matched after it, stopped by closeEnded()
 *       with the end as its stop, a new window may start; an end in the past is refused
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

trait expMigrationRecordingEngineChecks
{
    use expMigrationRunnerEngineChecks;

    /** The administrator and the anonymous user of the clean data. */
    protected static $admin = 14;
    protected static $anonymous = 10;

    /** Starts the checks: the test's connection, the file of running recordings in the test's var directory. */
    protected function recordingBegin( $db )
    {
        $this->runnerBegin( $db );
        expMigrationRecorder::setUp( null, self::$admin );
    }

    /** Ends the checks. */
    protected function recordingEnd()
    {
        expMigrationRecorder::setUp( null, null );
        $this->runnerEnd();
    }

    /** RC-1 */
    protected function checkRecordingTables( $db )
    {
        foreach ( array( expMigrationRecording::EVENT_TABLE, expMigrationRecording::TABLE ) as $table )
            if ( expKernelTableSchema::exists( $table, $db, true ) === true )
                $db->query( 'DROP TABLE ' . $table );
        expKernelTableSchema::reset();
        $context = new expUpgradeContext( $db, true, array(), self::runnerRoot() . '/share/db_schema.dba' );
        $step = new expUpgradeStepMigrationRecordingTables();
        $this->assertSame( '6.0.15-migration-recording-tables', $step->id() );
        $this->assertSame( array( '6.0.15-migration-tables' ), $step->after() );
        $this->assertSame( expUpgradeStep::MISSING, $step->detect( $context )['effect'] );
        $step->apply( $context );
        expKernelTableSchema::reset();
        $this->assertSame( expUpgradeStep::PRESENT, $step->detect( $context )['effect'] );
        $this->assertTrue( expMigrationRecording::tablesExist( $db ) );
        $step->apply( $context );
        $this->assertSame( array(), expMigrationRecording::all( $db ) );
    }

    /** RC-2 */
    protected function checkRecordSession( $db )
    {
        $this->checkRecordingTables( $db );
        $errors = array();
        $rec = expMigrationRecording::start( $db, expMigrationRecording::MODE_SESSION, self::$admin, 'engine test', $errors );
        $this->assertSame( array(), $errors );
        $this->assertNotNull( $rec );
        $this->assertNull( expMigrationRecording::start( $db, expMigrationRecording::MODE_SESSION, self::$admin, '', $errors ), 'one per user' );
        $this->assertNotEmpty( $errors );
        $this->assertSame( array( $rec['recording_id'] ), array_column( expMigrationRecording::active(), 'recording_id' ), 'the file lists it' );
        $this->assertTrue( expMigrationRecorder::wants( 'content.object.publish' ) );
        $this->assertFalse( expMigrationRecorder::wants( 'content.node.view' ), 'a read is no change' );

        // a class, stored as class/edit stores it, and recorded as class/edit records it
        $suffix = substr( bin2hex( random_bytes( 3 ) ), 0, 6 );
        $identifier = 'rec_note_' . $suffix;
        $class = $this->createClass( $identifier );
        expAuditHook::emit( 'content.class.create', function () use ( $class ) {
            $after = expAuditHook::contentClass( eZContentClass::fetch( $class->attribute( 'id' ) ), true );
            $object = $after;
            unset( $object['attributes'] );
            return array( 'object' => $object, 'after' => array( 'identifier' => $after['identifier'], 'attributes' => $after['attributes'] ) );
        } );

        // content of it below the root, published, and its children sorted by name
        $root = eZContentObjectTreeNode::fetch( 2 );
        $language = $root->attribute( 'object' )->attribute( 'initial_language_code' );
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => 2, 'language' => $language, 'class_identifier' => $identifier,
                                                                    'creator_id' => self::$admin, 'remote_id' => 'rec-note-' . $suffix,
                                                                    'attributes' => array( 'title' => 'First title' ) ), false );
        $this->assertInstanceOf( 'eZContentObject', $object );
        eZContentOperationCollection::changeSortOrder( (int)$object->attribute( 'main_node_id' ), eZContentObjectTreeNode::SORT_FIELD_NAME, 0 );

        // a role assigned to a group (the members group of the clean data, object 12)
        $role = eZRole::fetchByName( 'Editor' ) ?: eZRole::fetchByName( 'Anonymous' );
        $this->assertInstanceOf( 'eZRole', $role );
        $role->assignToUser( 12 );

        // the location hidden
        $node = eZContentObjectTreeNode::fetch( (int)$object->attribute( 'main_node_id' ) );
        eZContentObjectTreeNode::hideSubTree( $node );

        // a change of another user is not in this user's recording
        expMigrationRecorder::setUp( null, self::$anonymous );
        $this->assertFalse( expMigrationRecorder::wants( 'content.node.reveal' ) );
        expMigrationRecorder::setUp( null, self::$admin );

        $events = expMigrationRecording::events( $db, $rec['recording_id'] );
        $byName = array();
        foreach ( $events as $e )
            $byName[$e['audit_name']][] = $e['event'];
        foreach ( array( 'content.class.create', 'content.object.create', 'content.node.sort', 'access.role.assign', 'content.node.hide' ) as $name )
            $this->assertArrayHasKey( $name, $byName, "$name recorded: " . implode( ', ', array_keys( $byName ) ) );

        $classStep = $byName['content.class.create'][0]['step'];
        $this->assertSame( array( 'class', 'create', $identifier ), array( $classStep['type'], $classStep['mode'], $classStep['identifier'] ) );
        $this->assertSame( 'ezstring', $classStep['attributes']['title']['datatype'] );
        $this->assertSame( 'class ' . $identifier, $byName['content.class.create'][0]['subject'] );

        $create = $byName['content.object.create'][0]['step'];
        $this->assertSame( array( 'content', 'create', $identifier, 'rec-note-' . $suffix ), array( $create['type'], $create['mode'], $create['class'], $create['remote_id'] ) );
        $this->assertSame( 'remote:' . $root->attribute( 'remote_id' ), $create['parent'], 'the parent by its remote id' );
        $this->assertSame( 'First title', $create['fields'][$language]['title'] );
        $sort = $byName['content.node.sort'][0]['step'];
        $this->assertSame( array( 'location', 'update', 'name', 'desc' ), array( $sort['type'], $sort['mode'], $sort['sort_field'], $sort['sort_order'] ) );

        $assign = $byName['access.role.assign'][0]['step'];
        $members = eZContentObject::fetch( 12 );
        $this->assertSame( array( 'role', 'assign', $role->attribute( 'name' ), 'remote:' . $members->attribute( 'remote_id' ) ),
                           array( $assign['type'], $assign['mode'], $assign['match'], $assign['group'] ) );
        $hide = $byName['content.node.hide'][0]['step'];
        $this->assertSame( array( 'location', 'hide', 'remote:' . $node->attribute( 'remote_id' ) ), array( $hide['type'], $hide['mode'], $hide['match'] ) );
        foreach ( $events as $e )
        {
            $this->assertSame( self::$admin, $e['actor_id'] );
            $this->assertStringNotContainsString( '"actor"', json_encode( $e['event'] ), 'the payload names nobody' );
        }

        // the plan: one create of the content, the location's sort and hide after it
        $plan = expMigrationRecordingGenerator::plan( array_column( $events, 'event' ) );
        $content = array_values( array_filter( $plan, function ( $p ) { return $p['step']['type'] === 'content'; } ) );
        $this->assertCount( 1, $content );
        $this->assertSame( 'create', $content[0]['step']['mode'] );
        $this->assertSame( 'First title', $content[0]['step']['fields'][$language]['title'] );
        $this->assertSame( array( 'class', 'content', 'location', 'role', 'location' ), array_map( function ( $p ) { return $p['step']['type']; }, $plan ) );
        $report = expMigrationRecordingGenerator::fromRecording( $db, $rec['recording_id'], array( 'registry' => new expMigrationStepRegistry() ) );
        $this->assertSame( expUpgrade::EXIT_FAILED, $report['exit'] );
        $this->assertContains( 'content', $report['missing_step_types'] );
        $this->assertStringContainsString( 'needs the YAML step types', $report['errors'][0] );
        // with the step types of the installation: the migration, checked as exp:migration:check checks a file
        $report = expMigrationRecordingGenerator::fromRecording( $db, $rec['recording_id'], array( 'name' => 'recorded', 'time' => 1798761600 ) );
        $this->assertSame( expUpgrade::EXIT_OK, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'content_class', 'content', 'location', 'role', 'location' ),
                           array_column( expMigrationRecordingSteps::fromPlan( $report['steps'] )['steps'], 'type' ) );
        $this->assertStringContainsString( "identifier: $identifier", $report['generated']['content'] );
        $this->assertStringContainsString( 'remote:' . $node->attribute( 'remote_id' ), $report['generated']['content'] );
        $this->assertSame( date( 'YmdHis', 1798761600 ) . '_recorded', $report['generated']['id'] );
        $this->assertFalse( $report['written'], 'a dry run writes nothing' );
        if ( expMigrationYamlParser::available() )
        {
            $parseErrors = array();
            $warnings = array();
            $this->assertNotNull( expMigrationYaml::fromText( $report['generated']['content'], expMigrationStepRegistry::fromSettings(), $parseErrors, $warnings ),
                                  implode( '; ', $parseErrors ) );
        }

        // a site-wide window records another user's change too
        $site = expMigrationRecording::start( $db, expMigrationRecording::MODE_SITE, self::$admin, '', $errors );
        $this->assertNotNull( $site, implode( '; ', $errors ) );
        expMigrationRecorder::setUp( null, self::$anonymous );
        eZContentObjectTreeNode::unhideSubTree( eZContentObjectTreeNode::fetch( (int)$node->attribute( 'node_id' ) ) );
        expMigrationRecorder::setUp( null, self::$admin );
        $siteEvents = expMigrationRecording::events( $db, $site['recording_id'] );
        $this->assertSame( array( 'content.node.reveal' ), array_column( $siteEvents, 'audit_name' ) );
        $this->assertSame( self::$anonymous, $siteEvents[0]['actor_id'] );
        $this->assertNotContains( 'content.node.reveal', array_column( expMigrationRecording::events( $db, $rec['recording_id'] ), 'audit_name' ) );

        // stopped: nothing more
        $count = count( expMigrationRecording::events( $db, $rec['recording_id'] ) );
        $stopped = expMigrationRecording::stop( $db, $rec['recording_id'], $errors );
        $this->assertSame( expMigrationRecording::STOPPED, $stopped['state'] );
        $this->assertSame( $count, $stopped['event_count'] );
        expMigrationRecording::stop( $db, $site['recording_id'], $errors );
        $this->assertSame( array(), expMigrationRecording::active(), 'the file is gone with the last recording' );
        $this->assertFalse( expMigrationRecorder::wants( 'content.node.hide' ) );
        eZContentObjectTreeNode::hideSubTree( eZContentObjectTreeNode::fetch( (int)$node->attribute( 'node_id' ) ) );
        $this->assertCount( $count, expMigrationRecording::events( $db, $rec['recording_id'] ) );

        // discarded: nothing left
        $this->assertTrue( expMigrationRecording::discard( $db, $rec['recording_id'], $errors ) );
        $this->assertTrue( expMigrationRecording::discard( $db, $site['recording_id'], $errors ) );
        $this->assertSame( array(), expMigrationRecording::events( $db, $rec['recording_id'] ) );
        $this->assertSame( array(), expMigrationRecording::all( $db ) );
    }

    /** RC-5 */
    protected function checkRecordingWindow( $db )
    {
        $this->checkRecordingTables( $db );
        $errors = array();
        $until = time() + 600;
        $site = expMigrationRecording::start( $db, expMigrationRecording::MODE_SITE, self::$admin, 'window', $errors, $until );
        $this->assertNotNull( $site, implode( '; ', $errors ) );
        $this->assertSame( $until, expMigrationRecording::get( $db, $site['recording_id'] )['stopped_at'], 'a running window keeps its end in stopped_at' );
        $this->assertSame( expMigrationRecording::RECORDING, expMigrationRecording::get( $db, $site['recording_id'] )['state'] );
        $active = expMigrationRecording::active();
        $this->assertSame( $until, $active[0]['until'] );
        $this->assertCount( 1, expMigrationRecording::matching( $active, self::$anonymous ) );
        $this->assertSame( array(), expMigrationRecording::matching( $active, self::$anonymous, $until ), 'nothing is kept once the window ends' );
        $this->assertSame( array(), expMigrationRecording::closeEnded( $db, $until - 1 ), 'not yet' );

        $closed = expMigrationRecording::closeEnded( $db, $until );
        $this->assertSame( array( $site['recording_id'] ), array_column( $closed, 'recording_id' ) );
        $row = expMigrationRecording::get( $db, $site['recording_id'] );
        $this->assertSame( expMigrationRecording::STOPPED, $row['state'] );
        $this->assertSame( $until, $row['stopped_at'], 'the end of the window stays its stop' );
        $this->assertSame( array(), expMigrationRecording::active() );
        $this->assertSame( array(), expMigrationRecording::closeEnded( $db, $until + 1 ) );

        // a window that has ended does not block a new one
        $again = expMigrationRecording::start( $db, expMigrationRecording::MODE_SITE, self::$admin, '', $errors, time() + 60 );
        $this->assertNotNull( $again, implode( '; ', $errors ) );
        $this->assertNull( expMigrationRecording::start( $db, expMigrationRecording::MODE_SITE, self::$admin, '', $errors ) );
        $this->assertNull( expMigrationRecording::start( $db, expMigrationRecording::MODE_SITE, self::$admin, '', $errors, time() - 1 ) );
        $this->assertSame( array( 'the end of the window is not in the future' ), $errors );
        expMigrationRecording::stop( $db, $again['recording_id'], $errors );
        $this->assertGreaterThan( 0, expMigrationRecording::get( $db, $again['recording_id'] )['stopped_at'] );
        $this->assertTrue( expMigrationRecording::discard( $db, $site['recording_id'], $errors ) );
        $this->assertTrue( expMigrationRecording::discard( $db, $again['recording_id'], $errors ) );
    }

    /** RC-3 */
    protected function checkUserLeftOut( $db )
    {
        $this->checkRecordingTables( $db );
        $errors = array();
        $rec = expMigrationRecording::start( $db, expMigrationRecording::MODE_SESSION, self::$admin, '', $errors );
        $this->assertNotNull( $rec, implode( '; ', $errors ) );
        $suffix = bin2hex( random_bytes( 3 ) );
        $root = eZContentObjectTreeNode::fetch( 2 );
        $language = $root->attribute( 'object' )->attribute( 'initial_language_code' );
        $members = eZContentObject::fetch( 12 );
        $user = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => (int)$members->attribute( 'main_node_id' ), 'language' => $language,
                                                                  'class_identifier' => 'user', 'creator_id' => self::$admin, 'remote_id' => 'rec-user-' . $suffix,
                                                                  'attributes' => array( 'first_name' => 'Private', 'last_name' => 'Person',
                                                                                         'user_account' => "rec$suffix|rec$suffix@example.org|secretpw|md5_password|1" ) ), false );
        $this->assertInstanceOf( 'eZContentObject', $user );
        $events = expMigrationRecording::events( $db, $rec['recording_id'] );
        $this->assertNotEmpty( $events );
        $json = json_encode( array_column( $events, 'event' ) );
        foreach ( array( 'Private', 'Person', "rec$suffix@example.org", "rec$suffix|", 'secretpw' ) as $personal )
            $this->assertStringNotContainsString( $personal, $json, "$personal is not recorded" );
        $userEvent = null;
        foreach ( $events as $e )
            if ( $e['step_type'] === 'user' )
                $userEvent = $e['event'];
        $this->assertNotNull( $userEvent, 'a user step: ' . implode( ', ', array_column( $events, 'step_type' ) ) );
        $this->assertContains( 'email', $userEvent['omitted'] );
        $this->assertContains( 'first_name', $userEvent['omitted'] );
        $this->assertSame( array( 'remote:' . $members->attribute( 'remote_id' ) ), $userEvent['step']['groups'] );
        // the migration of it names the account by secrets
        $report = expMigrationRecordingGenerator::fromRecording( $db, $rec['recording_id'], array( 'name' => 'user' ) );
        $this->assertSame( expUpgrade::EXIT_OK, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertStringContainsString( "secrets:\n          login: user_rec", $report['generated']['content'] );
        foreach ( array( 'Private', 'Person', "rec$suffix@example.org", 'secretpw' ) as $personal )
            $this->assertStringNotContainsString( $personal, $report['generated']['content'], "$personal is not in the migration" );
        expMigrationRecording::discard( $db, $rec['recording_id'], $errors );
    }

    /** RC-4 */
    protected function checkGenerateFromExisting( $db )
    {
        $report = expMigrationRecordingGenerator::fromExisting( array( 'classes' => array( 'folder' ), 'roles' => array( 'Anonymous' ), 'sections' => array( 'standard' ),
                                                                       'content' => array( '2' ), 'depth' => 1, 'limit' => 5 ),
                                                                array( 'name' => 'existing' ) );
        $this->assertSame( expUpgrade::EXIT_OK, $report['exit'], implode( '; ', $report['errors'] ) );
        $content = $report['generated']['content'];
        foreach ( array( "type: content_class\n      mode: ensure\n      identifier: folder\n", "type: role\n      mode: ensure\n      name: Anonymous\n",
                         "type: section\n      mode: ensure\n      identifier: standard\n", "type: content\n      mode: ensure\n" ) as $text )
            $this->assertStringContainsString( $text, $content );
        $this->assertFalse( $report['written'] );
        $missing = expMigrationRecordingGenerator::fromExisting( array( 'classes' => array( 'no_such_class' ), 'content' => array( 'remote:no-such-node' ) ) );
        $this->assertSame( expUpgrade::EXIT_USAGE, $missing['exit'] );
        $this->assertSame( array( 'the class no_such_class: not found', 'the node remote:no-such-node: not found' ), $missing['errors'] );
    }

    /**
     * A class with one text attribute "title", stored as the class editor stores it.
     *
     * @param string $identifier
     * @return eZContentClass
     */
    protected function createClass( $identifier )
    {
        $language = eZContentLanguage::topPriorityLanguage()->attribute( 'locale' );
        $class = eZContentClass::create( self::$admin, array(), $language );
        $class->setName( 'Recorded note', $language );
        $class->setAttribute( 'contentobject_name', '<title>' );
        $class->setAttribute( 'identifier', $identifier );
        $class->store();
        $class->setAlwaysAvailableLanguageID( eZContentLanguage::idByLocale( $language ) );
        $group = eZContentClassClassGroup::create( $class->attribute( 'id' ), $class->attribute( 'version' ), 1, 'Content' );
        $group->store();
        $attribute = eZContentClassAttribute::create( $class->attribute( 'id' ), 'ezstring', array(), $language );
        $attribute->setName( 'Title', $language );
        $attribute->dataType()->initializeClassAttribute( $attribute );
        $attribute->setAttribute( 'identifier', 'title' );
        $attribute->store();
        $class->storeDefined( $class->fetchAttributes() );
        return eZContentClass::fetch( $class->attribute( 'id' ) );
    }
}
