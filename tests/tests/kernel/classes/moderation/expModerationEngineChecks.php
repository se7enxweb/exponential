<?php
/**
 * The checks of Dashboard > Recent content (expModerationList) on a database with the kernel schema and the clean data,
 * shared by the engine tests: SQLite in the process (expModerationSqliteTest), MySQL/MariaDB and PostgreSQL
 * (expModerationDatabaseTest) and MongoDB (expModerationMongoDBTest).
 *
 * The data: ROWS objects above the clean data, of two classes, in two sections, by two creators, a third of them
 * modified on one day, every fifth hidden, every seventh held (every fourteenth by the auto-hold, with its kept data),
 * every eleventh a draft that was never published, every thirteenth with a pending version scheduled by a workflow
 * process, audit events (create and auto-hold) for the first hundred with an address, a browser and a siteaccess.
 *
 *  ME-1  the default page: the newest first, the total, and one query per table (no query per row)
 *  ME-2  every filter: class, status (published, draft, pending), held yes/no, hidden yes/no, section, creator, the date
 *        range on either date, the siteaccess of the submission
 *  ME-3  the sorts: name from A, the creation date, ascending and descending, a stable order by id
 *  ME-4  a row: status, pending, scheduled, hidden, held by the auto-hold with its reason, the creator and the
 *        submission's address and browser (the earliest event), "not recorded" without one
 *  ME-5  1000 rows a page: under two seconds, a fixed number of queries; the second page and the last
 *  ME-6  the dashboard's counts
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// a checkout whose class map was built before the classes existed
foreach ( array( 'expModerationList' => 'kernel/classes/moderation/expmoderationlist.php', 'expModerationAction' => 'kernel/classes/moderation/expmoderationaction.php',
                 'expModerationAdmin' => 'kernel/classes/moderation/expmoderationadmin.php', 'expReviewHoldAuto' => 'kernel/classes/expreviewholdauto.php' ) as $moderationClass => $moderationFile )
    if ( !class_exists( $moderationClass ) )
        require_once dirname( __DIR__, 5 ) . '/' . $moderationFile;
unset( $moderationClass, $moderationFile );

trait expModerationEngineChecks
{
    /** @var int objects made above the clean data */
    protected static $rows = 1500;

    /** @var int the first object id made */
    protected static $first = 200000;

    /** @var array|null the database instance and the settings before the test */
    private $moderationSaved = null;

    /** @var int */
    private $heldStateID = 0;

    /** @var int the day the third of the objects was modified on (its midnight) */
    private $day = 0;

    protected function moderationBegin( $db )
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        $this->moderationSaved = array( 'db' => array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null,
                                        'handler' => $handler );
        $GLOBALS['eZDBGlobalInstance'] = $db;
        expModerationList::$settings = array( 'PageSizes' => array( 25, 100, 300, 700, 1000 ), 'JobThreshold' => 100,
                                              'SubmissionEvents' => array( 'content.object.hold.auto', 'content.object.publish', 'content.object.create' ) );
        eZContentObjectState::cleanDefaultsCache();
    }

    protected function moderationEnd()
    {
        expModerationList::$settings = null;
        if ( $this->moderationSaved !== null )
        {
            if ( $this->moderationSaved['db'] === null )
                unset( $GLOBALS['eZDBGlobalInstance'] );
            else
                $GLOBALS['eZDBGlobalInstance'] = $this->moderationSaved['db'][0];
            // a lookup of the current user (the persistent objects' store) may install the kernel's exception handler
            for ( $i = 0; $i < 5; $i++ )
            {
                $now = set_exception_handler( null );
                restore_exception_handler();
                if ( $now === $this->moderationSaved['handler'] )
                    break;
                restore_exception_handler();
            }
        }
        eZContentObject::clearCache();
        eZContentObjectState::cleanDefaultsCache();
        $this->moderationSaved = null;
    }

    /** The state group review_hold with its states, as the upgrade step makes them (without touching the roles) */
    private function makeHoldStates( $db )
    {
        $language = null;
        foreach ( (array)eZContentLanguage::fetchList( true ) as $l )
        {
            $language = $l;
            break;
        }
        $this->assertNotNull( $language, 'the clean data has a language' );
        $locale = (string)$language->attribute( 'locale' );
        $group = new eZContentObjectStateGroup();
        $group->setAttribute( 'identifier', expReviewHold::GROUP );
        $translation = $group->translationByLocale( $locale );
        $group->setAttribute( 'default_language_id', (int)$translation->realLanguageID() );
        $translation->setAttribute( 'name', 'Review hold' );
        $translation->setAttribute( 'description', 'A held object is not edited or published until a reviewer releases it' );
        $group->store();
        $group = expReviewHold::group();
        $this->assertInstanceOf( 'eZContentObjectStateGroup', $group, 'the group is found by its identifier' );
        $this->assertSame( expReviewHold::GROUP, (string)$group->attribute( 'identifier' ), 'and it is that group, not the first one' );
        foreach ( expReviewHold::states() as $identifier => $texts )
        {
            $state = $group->newState( $identifier );
            $t = $state->translationByLocale( $locale );
            $state->setAttribute( 'default_language_id', (int)$t->realLanguageID() );
            $t->setAttribute( 'name', $texts[0] );
            $t->setAttribute( 'description', $texts[1] );
            $state->store();
            $group = expReviewHold::group();
        }
        eZContentObjectState::cleanDefaultsCache();
        $held = expReviewHold::state( expReviewHold::STATE_HELD );
        $this->assertInstanceOf( 'eZContentObjectState', $held, 'the held state is found' );
        $this->assertSame( 'held', (string)$held->attribute( 'identifier' ) );
        $this->heldStateID = (int)$held->attribute( 'id' );
    }

    /** The objects, their locations, versions, holds, workflow processes and audit events */
    private function makeObjects( $db )
    {
        $this->day = mktime( 0, 0, 0, 10, 15, 2026 );
        $base = mktime( 12, 0, 0, 9, 1, 2026 );
        $q = function ( $s ) use ( $db ) { return "'" . $db->escapeString( (string)$s ) . "'"; };
        $mongo = $db->databaseName() === 'mongo';
        $nodeBase = self::$first;
        $db->begin();
        for ( $i = 0; $i < self::$rows; $i++ )
        {
            $id = self::$first + $i;
            $draft = $i % 11 === 10;
            $class = $i % 2 ? 1 : 4;                       // folder, user
            $section = $i % 3 ? 1 : 2;
            $owner = $i % 4 ? 14 : 10;                     // admin, anonymous
            $modified = $i % 3 === 0 ? $this->day + $i : $base + $i * 60;
            $published = $draft ? 0 : $modified - 30;
            $db->query( 'INSERT INTO ezcontentobject ( id, contentclass_id, current_version, initial_language_id, language_mask, modified, name, owner_id, published, remote_id, section_id, status ) VALUES ( '
                        . "$id, $class, 1, 2, 3, $modified, " . $q( sprintf( 'Item %05d', self::$rows - $i ) ) . ", $owner, $published, " . $q( 'moderation-test-' . $id )
                        . ", $section, " . ( $draft ? 0 : 1 ) . ' )' );
            $db->query( 'INSERT INTO ezcontentobject_version ( ' . ( $mongo ? 'id, ' : '' ) . 'contentobject_id, version, status, creator_id, created, modified, initial_language_id, language_mask, user_id, workflow_event_pos ) VALUES ( '
                        . ( $mongo ? ( 900000 + $i ) . ', ' : '' ) . "$id, 1, " . ( $draft ? 0 : 1 ) . ", $owner, $modified, $modified, 2, 3, 0, 0 )" );
            if ( $draft )
                continue;
            $node = $nodeBase + $i;
            $hidden = $i % 5 === 0 ? 1 : 0;
            $db->query( 'INSERT INTO ezcontentobject_tree ( node_id, contentobject_id, contentobject_version, contentobject_is_published, depth, is_hidden, is_invisible, main_node_id, modified_subnode, parent_node_id, path_identification_string, path_string, priority, remote_id, sort_field, sort_order ) VALUES ( '
                        . "$node, $id, 1, 1, 2, $hidden, $hidden, $node, $modified, 2, " . $q( 'item_' . $id ) . ', ' . $q( "/1/2/$node/" ) . ', 0, ' . $q( 'moderation-node-' . $node ) . ', 1, 1 )' );
            if ( $i % 13 === 12 )
            {
                $db->query( 'INSERT INTO ezcontentobject_version ( ' . ( $mongo ? 'id, ' : '' ) . 'contentobject_id, version, status, creator_id, created, modified, initial_language_id, language_mask, user_id, workflow_event_pos ) VALUES ( '
                            . ( $mongo ? ( 950000 + $i ) . ', ' : '' ) . "$id, 2, 2, $owner, $modified, $modified, 2, 3, 0, 0 )" );
                $db->query( 'INSERT INTO ezworkflow_process ( ' . ( $mongo ? 'id, ' : '' ) . 'activation_date, content_id, content_version, created, modified, node_id, process_key, user_id, workflow_id, event_id, event_position, last_event_id, last_event_position, last_event_status, event_status, session_key ) VALUES ( '
                            . ( $mongo ? ( 700000 + $i ) . ', ' : '' ) . ( $this->day + 86400 * 30 ) . ", $id, 2, $modified, $modified, $node, " . $q( md5( 'p' . $id ) ) . ", $owner, 1, 0, 0, 0, 0, 0, 0, '0' )" );
            }
            if ( $i % 7 === 0 )
            {
                $db->query( "INSERT INTO ezcobj_state_link ( contentobject_id, contentobject_state_id ) VALUES ( $id, {$this->heldStateID} )" );
                if ( $i % 14 === 0 )
                    $db->query( 'INSERT INTO ezsite_data ( name, value ) VALUES ( ' . $q( expReviewHoldAuto::META_PREFIX . $id ) . ', '
                                . $q( json_encode( array( 'auto' => true, 'reason' => '', 'hidden' => array( $node ), 'since' => $modified ) ) ) . ' )' );
                else
                    $db->query( 'INSERT INTO ezsite_data ( name, value ) VALUES ( ' . $q( expReviewHoldAuto::META_PREFIX . $id ) . ', '
                                . $q( json_encode( array( 'auto' => false, 'reason' => 'Check the link', 'name' => 'Reviewer', 'hidden' => array() ) ) ) . ' )' );
            }
        }
        // the audit: the first hundred objects were submitted on the siteaccess "site" (create, then the auto-hold
        // a second later from another address), one on "other"
        for ( $i = 0; $i < 100; $i++ )
        {
            $id = self::$first + $i;
            foreach ( array( array( 'content.object.create', 1000, '198.51.100.0/24', 'Mozilla/5.0 (first)' ), array( 'content.object.hold.auto', 2000, '203.0.113.0/24', 'Later' ) ) as $k => $e )
                $db->query( 'INSERT INTO expaudit_event ( id, channel, file_name, seq, name, domain_name, object_type, object_id, ip, ua, siteaccess, time_ms, severity, depth, imported, pseudonymised ) VALUES ( '
                            . $q( sprintf( 'MOD%023d', $i * 2 + $k ) ) . ", 'content', 'moderation-test.jsonl', " . ( $i * 2 + $k ) . ', ' . $q( $e[0] ) . ", 'content', 'object', "
                            . $q( (string)$id ) . ', ' . $q( $e[2] ) . ', ' . $q( $e[3] ) . ', ' . $q( $i === 99 ? 'other' : 'site' ) . ', ' . ( 1790000000000 + $i * 10000 + $e[1] ) . ', 6, 0, 0, 0 )' );
        }
        $db->commit();
    }

    /** @return int[] the ids of the made objects matching a test */
    private function made( $test )
    {
        $ids = array();
        for ( $i = 0; $i < self::$rows; $i++ )
            if ( $test( $i ) )
                $ids[] = self::$first + $i;
        return $ids;
    }

    private static function filters( array $in = array() )
    {
        return expModerationList::filters( $in + array( 'limit' => '1000' ), array( 25, 100, 300, 700, 1000 ), 25 );
    }

    /** @return int[] every id the list gives for a filter, page by page */
    private function allIds( expModerationList $list, array $in )
    {
        $ids = array();
        $offset = 0;
        do
        {
            $page = $list->page( self::filters( $in + array( 'offset' => (string)$offset ) ) );
            foreach ( $page['rows'] as $row )
                $ids[] = $row['id'];
            $offset += 1000;
        }
        while ( $offset < $page['total'] );
        return $ids;
    }

    /** ME-1 to ME-6 */
    protected function checkModerationList( $db )
    {
        $this->makeHoldStates( $db );
        $this->makeObjects( $db );
        $list = new expModerationList( $db, true );
        $made = array_flip( $this->made( function () { return true; } ) );
        $countRows = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' );
        $all = (int)$countRows[0]['c'];

        // ME-1
        $page = $list->page( expModerationList::filters( array(), array( 25, 100, 300, 700, 1000 ), 25 ) );
        $this->assertSame( $all, $page['total'] );
        $this->assertCount( 25, $page['rows'] );
        $newest = $db->arrayQuery( 'SELECT id FROM ezcontentobject ORDER BY modified DESC, id DESC', array( 'limit' => 25 ) );
        $this->assertSame( array_map( 'intval', array_column( $newest, 'id' ) ), array_column( $page['rows'], 'id' ), 'newest first' );
        $this->assertLessThanOrEqual( 12, $page['queries'], 'one query per table, not per row' );

        // ME-2
        $this->assertEqualsCanonicalizing( $this->made( function ( $i ) { return $i % 2 === 1; } ),
            array_values( array_filter( $this->allIds( $list, array( 'class' => 'folder' ) ), function ( $id ) use ( $made ) { return isset( $made[$id] ); } ) ), 'class' );
        $this->assertSame( array(), $this->allIds( $list, array( 'class' => 'nosuchclass' ) ) );
        $this->assertEqualsCanonicalizing( $this->made( function ( $i ) { return $i % 11 === 10; } ),
            array_values( array_filter( $this->allIds( $list, array( 'status' => 'draft' ) ), function ( $id ) use ( $made ) { return isset( $made[$id] ); } ) ), 'drafts' );
        $this->assertEqualsCanonicalizing( $this->made( function ( $i ) { return $i % 13 === 12 && $i % 11 !== 10; } ), $this->allIds( $list, array( 'status' => 'pending' ) ), 'pending' );
        $heldIds = $this->made( function ( $i ) { return $i % 7 === 0 && $i % 11 !== 10; } );
        $this->assertEqualsCanonicalizing( $heldIds, $this->allIds( $list, array( 'held' => 'yes' ) ), 'held' );
        $notHeld = $this->allIds( $list, array( 'held' => 'no' ) );
        $this->assertCount( $all - count( $heldIds ), $notHeld, 'not held: all the others' );
        $this->assertSame( array(), array_intersect( $heldIds, $notHeld ) );
        $hiddenIds = $this->made( function ( $i ) { return $i % 5 === 0 && $i % 11 !== 10; } );
        $this->assertEqualsCanonicalizing( $hiddenIds, array_values( array_filter( $this->allIds( $list, array( 'hidden' => 'yes' ) ), function ( $id ) use ( $made ) { return isset( $made[$id] ); } ) ), 'hidden' );
        $this->assertEqualsCanonicalizing( $this->made( function ( $i ) { return $i % 7 === 0 && $i % 5 === 0 && $i % 11 !== 10; } ),
                                           $this->allIds( $list, array( 'held' => 'yes', 'hidden' => 'yes' ) ), 'held and hidden' );
        $this->assertEqualsCanonicalizing( $this->made( function ( $i ) { return $i % 3 === 0 && $i % 4 === 0; } ),
            $this->allIds( $list, array( 'section' => '2', 'creator' => '10', 'from' => date( 'Y-m-d', $this->day ), 'to' => date( 'Y-m-d', $this->day ) ) ), 'section, creator and day' );
        // created 30 seconds before the modification: the first ten of the day were created the day before
        $this->assertEqualsCanonicalizing( $this->made( function ( $i ) { return $i % 3 === 0 && $i >= 30 && $i % 11 !== 10; } ),
            array_values( array_filter( $this->allIds( $list, array( 'date' => 'published', 'from' => date( 'Y-m-d', $this->day ), 'to' => date( 'Y-m-d', $this->day ) ) ),
                                        function ( $id ) use ( $made ) { return isset( $made[$id] ); } ) ), 'created on the day' );
        $this->assertEqualsCanonicalizing( $this->made( function ( $i ) { return $i < 99; } ), $this->allIds( $list, array( 'siteaccess' => 'site' ) ), 'siteaccess' );
        $this->assertSame( array( self::$first + 99 ), $this->allIds( $list, array( 'siteaccess' => 'other' ) ) );

        // ME-3
        $byName = $list->page( self::filters( array( 'sort' => 'name', 'order' => 'asc', 'creator' => '14', 'section' => '1' ) ) );
        $names = array_column( $byName['rows'], 'name' );
        $sorted = $names;
        sort( $sorted, SORT_STRING );
        $this->assertSame( $sorted, $names, 'by name from A' );
        $byCreated = $list->page( self::filters( array( 'date' => 'published', 'order' => 'asc' ) ) )['rows'];
        $previous = -1;
        foreach ( $byCreated as $row )
        {
            $this->assertGreaterThanOrEqual( $previous, $row['published'] );
            $previous = $row['published'];
        }

        // ME-4
        $rows = array();
        foreach ( $list->page( self::filters( array( 'held' => 'yes' ) ), true )['rows'] as $row )
            $rows[$row['id']] = $row;
        $auto = $rows[self::$first];                                  // i = 0: held automatically, hidden, recorded
        foreach ( array( 'held', 'auto', 'hidden', 'recorded' ) as $flag )
            $this->assertTrue( $auto[$flag], "i = 0 is $flag: " . json_encode( $auto ) );
        $this->assertSame( 'published', $auto['status'] );
        $this->assertSame( '198.51.100.0/24', $auto['ip'], 'the earliest event' );
        $this->assertSame( 'Mozilla/5.0 (first)', $auto['ua'] );
        $this->assertSame( 'site', $auto['siteaccess'] );
        $this->assertSame( 'Anonymous User', $auto['owner_name'], 'i = 0: owner 10' );
        $this->assertSame( 'user', $auto['class_identifier'], 'i = 0: class 4' );
        $placed = $rows[self::$first + 7];                            // i = 7: held by a reviewer
        $this->assertFalse( $placed['auto'] );
        $this->assertSame( 'Check the link', $placed['hold_reason'] );
        $this->assertSame( 'Reviewer', $placed['hold_by'] );
        $late = $rows[self::$first + 7 * 20];                         // i = 140: held, no audit event
        $this->assertFalse( $late['recorded'] );
        $this->assertSame( '', $late['ip'] );
        $scheduled = $list->page( self::filters( array( 'status' => 'pending' ) ) )['rows'][0];
        $this->assertTrue( $scheduled['pending'] );
        $this->assertSame( 'pending', $scheduled['version_status'] );
        $this->assertSame( 2, $scheduled['version'] );
        $this->assertSame( $this->day + 86400 * 30, $scheduled['scheduled'] );
        $draft = $list->page( self::filters( array( 'status' => 'draft', 'creator' => '10' ) ) )['rows'][0];
        $this->assertSame( 'draft', $draft['status'] );
        $this->assertSame( 0, $draft['node_id'] );
        $noPersonal = $list->page( self::filters( array( 'held' => 'yes' ) ), false )['rows'];
        foreach ( $noPersonal as $row )
            $this->assertSame( '', $row['ip'], 'no address without moderation/personaldata' );

        // ME-5
        $before = $list->queries;
        $started = microtime( true );
        $big = $list->page( self::filters( array( 'limit' => '1000' ) ), true );
        $seconds = microtime( true ) - $started;
        $this->assertCount( 1000, $big['rows'] );
        $this->assertLessThan( 2.0, $seconds, 'a page of 1000 rows' );
        $this->assertLessThanOrEqual( 12, $list->queries - $before, 'the same queries for 1000 rows as for 25' );
        $second = $list->page( self::filters( array( 'limit' => '1000', 'offset' => '1000' ) ) );
        $this->assertCount( $all - 1000, $second['rows'], 'the last page' );
        $this->assertSame( array(), array_intersect( array_column( $big['rows'], 'id' ), array_column( $second['rows'], 'id' ) ), 'no row twice' );
        $beyond = $list->page( self::filters( array( 'limit' => '1000', 'offset' => '50000' ) ) );
        $this->assertSame( 0, $beyond['offset'], 'a page past the end: the first' );

        // ME-6
        $counts = $list->counts();
        $this->assertSame( count( $heldIds ), $counts['held'] );
        $this->assertGreaterThanOrEqual( 0, $counts['new_today'] );
    }
}
