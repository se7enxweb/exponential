<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * cjw_newsletter 4.2.0, area editorial (N2): recurring sends, the article pool and its finder, the picks of an
 * edition, the approval through the collaboration inbox, the hooks, the admin views and ext:cjw_newsletter:schedule.
 *
 * Live style (cjwNewsletterTestCase): the installation's own database, throwaway data on example.invalid, the file
 * transport only (the test refuses to run otherwise). Every schedule, pool, edition, pick, approval and inbox item a
 * test makes is removed in tearDown; the content of the site is only read (the auto-fill copies its titles and
 * intros into newsletter articles of the test editions). The settings of the list the tests use are put back.
 * Skipped where there is no installation (CI).
 *
 *  ED-01..06  recurrence: weekdays, weekly, monthly with short months, time zones and the change of the clocks
 *  ED-07..10  the pool: forList() fallbacks, the finder's filters, casting, the anonymous view
 *  ED-11..13  the edition builder: latest unsent edition, copy of a template, picks in and out
 *  ED-14..21  the runner: latest, copy with auto-fill, nothing new, conditions, failures, dry run, lock, the hook
 *  ED-22..27  the approval: request, inbox item, the send held and refused, decide, self approval, withdraw
 *  ED-28..32  hooks, dashboard, views, fetch functions, command
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class cjwNewsletterEditorialTest extends cjwNewsletterTestCase
{
    protected $scheduleIds = array();
    protected $poolIds = array();
    protected $savedList = null;

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        foreach ( array( 'TransportMethodCronjob', 'TransportMethodPreview', 'TransportMethodDirectly' ) as $name )
            if ( eZINI::instance( 'cjw_newsletter.ini' )->variable( 'NewsletterMailSettings', $name ) !== 'file' )
                $this->fail( 'The newsletter transport is not the file transport: the test refuses to run.' );
        if ( !class_exists( 'CjwNewsletterScheduleRunner' ) )
            $this->markTestSkipped( 'the editorial classes are not loaded' );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'Notification', 'disabled' );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'ApproverUserIds', array( $this->adminId() ) );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'ApproverGroupIds', array() );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'AllowSelfApproval', 'disabled' );
        $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
        $this->savedList = array( 'approval_required' => (int)$list->attribute( 'approval_required' ), 'article_pool_id' => (int)$list->attribute( 'article_pool_id' ) );
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null && class_exists( 'CjwNewsletterScheduleRunner' ) )
        {
            $this->loginAdmin();
            $db = eZDB::instance();
            // the approvals and their inbox items, the picks of the test editions
            foreach ( $this->createdObjectIds as $id )
            {
                foreach ( CjwNewsletterApproval::fetchList( array( 'edition_contentobject_id' => (int)$id ) ) as $approval )
                {
                    CjwNewsletterApprovalFlow::removeCollaborationItem( $approval->attribute( 'collaboration_item_id' ) );
                    $approval->remove();
                }
                eZPersistentObject::removeObject( CjwNewsletterEditionArticle::definition(), array( 'edition_contentobject_id' => (int)$id ) );
            }
            foreach ( $this->scheduleIds as $id )
            {
                $schedule = CjwNewsletterSchedule::fetch( $id );
                if ( $schedule )
                {
                    // the editions the schedule made (copies) go too
                    foreach ( $schedule->editionObjectIds() as $editionId )
                        if ( !in_array( $editionId, $this->createdObjectIds ) )
                            $this->createdObjectIds[] = $editionId;
                    $schedule->removeSchedule();
                }
            }
            foreach ( $this->createdObjectIds as $id )
            {
                foreach ( CjwNewsletterApproval::fetchList( array( 'edition_contentobject_id' => (int)$id ) ) as $approval )
                {
                    CjwNewsletterApprovalFlow::removeCollaborationItem( $approval->attribute( 'collaboration_item_id' ) );
                    $approval->remove();
                }
                eZPersistentObject::removeObject( CjwNewsletterEditionArticle::definition(), array( 'edition_contentobject_id' => (int)$id ) );
            }
            $this->scheduleIds = array();
            foreach ( $this->poolIds as $id )
            {
                $pool = CjwNewsletterArticlePool::fetch( $id );
                if ( $pool )
                    $pool->removePool();
            }
            $this->poolIds = array();
            if ( $this->savedList !== null )
            {
                $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
                foreach ( $this->savedList as $column => $value )
                    $list->setAttribute( $column, $value );
                $list->store();
                $this->savedList = null;
            }
        }
        parent::tearDown();
    }

    // ------------------------------------------------------------------ helpers

    protected function adminId()
    {
        return (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
    }

    /** @return CjwNewsletterSchedule stored, removed in tearDown */
    protected function newSchedule( $row = array() )
    {
        $schedule = CjwNewsletterSchedule::create( array_merge( array( 'list_contentobject_id' => self::LIST_OBJECT_ID, 'mode' => 'latest',
            'recurrence_type' => 'w', 'recurrence_value' => '1', 'send_time' => 8 * 3600, 'timezone' => 'Europe/Berlin',
            'creator_contentobject_id' => $this->adminId(), 'created' => time() ), $row ) );
        $schedule->store();
        $this->scheduleIds[] = (int)$schedule->attribute( 'id' );
        return $schedule;
    }

    /** @return CjwNewsletterArticlePool stored, removed in tearDown */
    protected function newPool( $row = array() )
    {
        $pool = CjwNewsletterArticlePool::create( array_merge( array( 'name' => 'NLTEST pool ' . getmypid(), 'max_items' => 10 ), $row ) );
        $pool->store();
        $this->poolIds[] = (int)$pool->attribute( 'id' );
        return $pool;
    }

    protected function setList( $column, $value )
    {
        $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
        $list->setAttribute( $column, $value );
        $list->store();
    }

    /** A Unix time from a local time in a zone. */
    protected function at( $text, $zone = 'Europe/Berlin' )
    {
        $d = new DateTime( $text, new DateTimeZone( $zone ) );
        return $d->getTimestamp();
    }

    protected function local( $time, $zone = 'Europe/Berlin' )
    {
        $d = new DateTime( '@' . $time );
        $d->setTimezone( new DateTimeZone( $zone ) );
        return $d->format( 'Y-m-d H:i D' );
    }

    /** Marks an edition as sent (a finished send), so that it is not "unsent" any more. */
    protected function markSent( eZContentObject $edition )
    {
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() );
        $send->setAttribute( 'status', CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED );
        $send->store();
        return $send;
    }

    /** Every unsent edition of the test list that is not one of this test's: they would be taken by mode latest. */
    protected function foreignUnsentEdition()
    {
        $found = CjwNewsletterEditionBuilder::latestUnsentEdition( self::LIST_OBJECT_ID );
        return $found && !in_array( (int)$found->attribute( 'id' ), $this->createdObjectIds ) ? $found : null;
    }

    // ------------------------------------------------------------------ ED-01..06 recurrence

    public function testED01ChosenWeekdaysRunOnTheNextChosenDayAtTheTime()
    {
        $s = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'd', 'recurrence_value' => '1,3,5', 'send_time' => 8 * 3600 + 30 * 60, 'timezone' => 'Europe/Berlin' ) );
        // Monday 2026-10-05 09:00 -> Wednesday 08:30
        $this->assertSame( '2026-10-07 08:30 Wed', $this->local( $s->nextRunAfter( $this->at( '2026-10-05 09:00' ) ) ) );
        // Monday 07:00 -> the same Monday 08:30
        $this->assertSame( '2026-10-05 08:30 Mon', $this->local( $s->nextRunAfter( $this->at( '2026-10-05 07:00' ) ) ) );
        // exactly at the time: strictly after, so the next chosen day
        $this->assertSame( '2026-10-07 08:30 Wed', $this->local( $s->nextRunAfter( $this->at( '2026-10-05 08:30' ) ) ) );
        // Friday after the time -> Monday
        $this->assertSame( '2026-10-12 08:30 Mon', $this->local( $s->nextRunAfter( $this->at( '2026-10-09 12:00' ) ) ) );
        $this->assertSame( array( 1, 3, 5 ), $s->weekdays() );
        $this->assertSame( '08:30', $s->sendTimeText() );
    }

    public function testED02WeeklyAndEveryDay()
    {
        $s = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'w', 'recurrence_value' => '7', 'send_time' => 18 * 3600, 'timezone' => 'Europe/Berlin' ) );
        $this->assertSame( '2026-10-11 18:00 Sun', $this->local( $s->nextRunAfter( $this->at( '2026-10-05 09:00' ) ) ) );
        $this->assertSame( '2026-10-18 18:00 Sun', $this->local( $s->nextRunAfter( $this->at( '2026-10-11 18:00' ) ) ) );
        // weekly takes one day only, even if more are stored
        $s->setAttribute( 'recurrence_value', '3,5' );
        $this->assertSame( array( 3 ), $s->weekdays() );
        $all = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'd', 'recurrence_value' => '1,2,3,4,5,6,7', 'send_time' => 0, 'timezone' => 'UTC' ) );
        $this->assertSame( '2026-10-06 00:00 Tue', $this->local( $all->nextRunAfter( $this->at( '2026-10-05 00:00', 'UTC' ) ), 'UTC' ) );
    }

    public function testED03MonthlyTakesTheLastDayOfShortMonths()
    {
        $s = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'm', 'recurrence_value' => '31', 'send_time' => 6 * 3600, 'timezone' => 'Europe/Berlin' ) );
        $this->assertSame( '2026-10-31 06:00 Sat', $this->local( $s->nextRunAfter( $this->at( '2026-10-05 09:00' ) ) ) );
        $this->assertSame( '2026-11-30 06:00 Mon', $this->local( $s->nextRunAfter( $this->at( '2026-10-31 06:00' ) ) ) );
        $this->assertSame( '2027-02-28 06:00 Sun', $this->local( $s->nextRunAfter( $this->at( '2027-01-31 07:00' ) ) ) );
        $this->assertSame( '2028-02-29 06:00 Tue', $this->local( $s->nextRunAfter( $this->at( '2028-02-01 00:00' ) ) ) );
        // across the year
        $s->setAttribute( 'recurrence_value', '15' );
        $this->assertSame( '2027-01-15 06:00 Fri', $this->local( $s->nextRunAfter( $this->at( '2026-12-15 06:00' ) ) ) );
        // nonsense falls back to the 1st
        $s->setAttribute( 'recurrence_value', '99' );
        $this->assertSame( 1, $s->monthDay() );
    }

    public function testED04TimeZonesAndTheChangeOfTheClocks()
    {
        $berlin = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'd', 'recurrence_value' => '7', 'send_time' => 2 * 3600 + 30 * 60, 'timezone' => 'Europe/Berlin' ) );
        // 2027-03-28: the clocks go from 02:00 to 03:00 in Berlin, 02:30 does not exist and moves forward
        $run = $berlin->nextRunAfter( $this->at( '2027-03-27 12:00' ) );
        $this->assertSame( '2027-03-28 03:30 Sun', $this->local( $run ) );
        // in October 02:30 exists twice: one run, the next one a week later
        $run = $berlin->nextRunAfter( $this->at( '2026-10-24 12:00' ) );
        $this->assertSame( '2026-10-25', substr( $this->local( $run ), 0, 10 ) );
        $this->assertSame( '2026-11-01 02:30 Sun', $this->local( $berlin->nextRunAfter( $run ) ) );
        // the same wall time in two zones is two different moments
        $ny = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'w', 'recurrence_value' => '1', 'send_time' => 9 * 3600, 'timezone' => 'America/New_York' ) );
        $b = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'w', 'recurrence_value' => '1', 'send_time' => 9 * 3600, 'timezone' => 'Europe/Berlin' ) );
        $from = $this->at( '2026-10-06 00:00', 'UTC' );
        $this->assertSame( 6 * 3600, $ny->nextRunAfter( $from ) - $b->nextRunAfter( $from ) );
        // an unknown zone falls back to the default
        $bad = CjwNewsletterSchedule::create( array( 'timezone' => 'Mars/Olympus' ) );
        $this->assertTrue( CjwNewsletterSchedule::isTimezone( $bad->timezoneName() ) );
        $this->assertFalse( CjwNewsletterSchedule::isTimezone( 'Mars/Olympus' ) );
    }

    public function testED05NoDayMeansNoRunAndPausedSchedulesAreNotDue()
    {
        $s = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'd', 'recurrence_value' => '', 'send_time' => 0 ) );
        $this->assertSame( 0, $s->nextRunAfter( time() ) );
        $this->assertSame( array(), $s->weekdays() );
        $p = $this->newSchedule( array( 'status' => CjwNewsletterSchedule::STATUS_PAUSED ) );
        $p->updateNextRun();
        $this->assertSame( 0, (int)$p->attribute( 'next_run' ) );
        $p->store();
        $active = $this->newSchedule( array( 'next_run' => time() - 60 ) );
        $due = array_map( function ( $x ) { return (int)$x->attribute( 'id' ); }, CjwNewsletterSchedule::fetchDue() );
        $this->assertContains( (int)$active->attribute( 'id' ), $due );
        $this->assertNotContains( (int)$p->attribute( 'id' ), $due );
        $this->assertTrue( $active->isDue() );
        $this->assertFalse( $active->isDue( time() - 3600 ) );
    }

    public function testED06TheTextsOfARecurrence()
    {
        $s = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'm', 'recurrence_value' => '15', 'send_time' => 8 * 3600 ) );
        $this->assertStringContainsString( '15', $s->recurrenceText() );
        $this->assertStringContainsString( '08:00', $s->recurrenceText() );
        $d = CjwNewsletterSchedule::create( array( 'recurrence_type' => 'd', 'recurrence_value' => '1,2,3,4,5,6,7', 'send_time' => 0 ) );
        $this->assertNotSame( '', $d->recurrenceText() );
        $this->assertCount( 7, CjwNewsletterSchedule::weekdayNames() );
        $this->assertNotSame( '', $s->statusName() );
    }

    // ------------------------------------------------------------------ ED-07..10 the pool

    public function testED07ForListFallsBackFromTheListToTheDefaultToTheSettings()
    {
        $default = CjwNewsletterArticlePool::fetchDefault();
        $fallback = CjwNewsletterArticlePool::forList( self::LIST_OBJECT_ID );
        if ( !$default && !CjwNewsletterArticlePool::fetchListByListContentobjectId( self::LIST_OBJECT_ID ) && !$this->savedList['article_pool_id'] )
        {
            $this->assertFalse( $fallback->isStored(), 'without any pool the settings make one' );
            $this->assertSame( array( 'cjw_newsletter_article' ), $fallback->classIdentifiers() );
            $this->assertNotEmpty( $fallback->parentNodeIds() );
        }
        $own = $this->newPool( array( 'list_contentobject_id' => self::LIST_OBJECT_ID, 'parent_node_id_array_string' => ';2;' ) );
        $this->setList( 'article_pool_id', 0 );
        $this->assertSame( (int)$own->attribute( 'id' ), (int)CjwNewsletterArticlePool::forList( self::LIST_OBJECT_ID )->attribute( 'id' ), 'the pool made for the list' );
        $named = $this->newPool( array( 'parent_node_id_array_string' => ';2;' ) );
        $this->setList( 'article_pool_id', (int)$named->attribute( 'id' ) );
        $this->assertSame( (int)$named->attribute( 'id' ), (int)CjwNewsletterArticlePool::forList( self::LIST_OBJECT_ID )->attribute( 'id' ), 'the pool the list names wins' );
        // the named pool is removed: the list falls back and no longer names it
        $named->removePool();
        $this->assertSame( 0, (int)CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 )->attribute( 'article_pool_id' ) );
        $this->assertSame( (int)$own->attribute( 'id' ), (int)CjwNewsletterArticlePool::forList( self::LIST_OBJECT_ID )->attribute( 'id' ) );
        // a list that does not exist gets the default (stored or from the settings)
        $this->assertInstanceOf( 'CjwNewsletterArticlePool', CjwNewsletterArticlePool::forList( 999999999 ) );
        // makeDefault() leaves one default
        $a = $this->newPool( array( 'is_default' => 1 ) );
        $b = $this->newPool();
        $b->makeDefault();
        $b->store();
        $this->assertSame( (int)$b->attribute( 'id' ), (int)CjwNewsletterArticlePool::fetchDefault()->attribute( 'id' ) );
        $this->assertSame( 0, (int)CjwNewsletterArticlePool::fetch( $a->attribute( 'id' ) )->attribute( 'is_default' ) );
        if ( $default )
        {
            $default->makeDefault();
            $default->store();
        }
    }

    public function testED08TheArrayStringsAreCast()
    {
        $this->assertSame( array( 2, 45 ), CjwNewsletterArticlePool::fromArrayString( ';2;45;x;-3;2;0;' ) );
        $this->assertSame( array( 'article', 'ng_article' ), CjwNewsletterArticlePool::fromArrayString( ';article;ng_article;bad name;a\'b;', false ) );
        $this->assertSame( ';1;3;', CjwNewsletterArticlePool::toArrayString( array( 1, '3', '', '1', 'x y' ) ) );
        $this->assertSame( '', CjwNewsletterArticlePool::toArrayString( array() ) );
        $pool = CjwNewsletterArticlePool::create( array( 'filter_data' => '{"exclude_class_identifiers":["folder","x;y"],"only_main_language":1,"sql":"DROP"}' ) );
        $this->assertSame( array( 'exclude_class_identifiers' => array( 'folder' ), 'only_main_language' => true ), $pool->filterArray() );
        $this->assertSame( array( 'exclude_class_identifiers' => array(), 'only_main_language' => false ), CjwNewsletterArticlePool::create( array( 'filter_data' => 'not json' ) )->filterArray() );
    }

    public function testED09TheFinderFiltersByTheFetchFilters()
    {
        $source = $this->newEdition( 'NLTEST pool source', false );
        $a1 = $this->newArticle( $source, 'NLTEST pool one' );
        $a2 = $this->newArticle( $source, 'NLTEST pool two' );
        $pool = $this->newPool( array( 'parent_node_id_array_string' => ';' . $source->attribute( 'main_node_id' ) . ';',
                                       'class_identifier_array_string' => ';cjw_newsletter_article;' ) );
        $ids = function ( $nodes ) { return array_map( function ( $n ) { return (int)$n->attribute( 'contentobject_id' ); }, $nodes ); };
        $found = $ids( CjwNewsletterArticlePoolFinder::find( $pool ) );
        sort( $found );
        $this->assertSame( array( (int)$a1->attribute( 'id' ), (int)$a2->attribute( 'id' ) ), $found );
        $this->assertSame( 2, CjwNewsletterArticlePoolFinder::count( $pool ) );
        $this->assertCount( 1, CjwNewsletterArticlePoolFinder::find( $pool, array( 'limit' => 1 ) ) );
        $this->assertSame( array( (int)$a2->attribute( 'id' ) ), $ids( CjwNewsletterArticlePoolFinder::find( $pool, array( 'exclude_object_ids' => array( $a1->attribute( 'id' ), 'x' ) ) ) ) );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( $pool, array( 'since' => time() + 60 ) ), 'nothing published later' );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( $pool, array( 'until' => time() - 86400 ) ), 'nothing published before yesterday' );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( $pool, array( 'class_identifiers' => array( 'folder' ) ) ), 'a class outside the pool' );
        $this->assertCount( 2, CjwNewsletterArticlePoolFinder::find( $pool, array( 'class_identifiers' => array( 'cjw_newsletter_article', "x' OR 1=1" ) ) ) );
        $section = (int)$a1->attribute( 'section_id' );
        $this->assertCount( 2, CjwNewsletterArticlePoolFinder::find( $pool, array( 'section_ids' => array( $section ) ) ) );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( $pool, array( 'section_ids' => array( 999999 ) ) ) );
        $sectionPool = $this->newPool( array( 'parent_node_id_array_string' => ';' . $source->attribute( 'main_node_id' ) . ';', 'section_id_array_string' => ';' . $section . ';' ) );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( $sectionPool, array( 'section_ids' => array( 999999 ) ) ), 'the option only narrows the pool' );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( $pool, array( 'state_ids' => array( 999999 ) ) ) );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( $pool, array( 'tag_ids' => array( 999999999 ) ) ), 'an interest nobody tagged' );
        $byName = CjwNewsletterArticlePoolFinder::find( $pool, array( 'sort_by' => 'name' ) );
        $this->assertCount( 2, $byName );
        $this->assertCount( 2, CjwNewsletterArticlePoolFinder::find( $pool, array( 'sort_by' => 'DROP TABLE', 'language' => "x'; --", 'offset' => -5 ) ), 'bad values are ignored' );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( CjwNewsletterArticlePool::create( array() ) ), 'a pool without nodes finds nothing' );
        $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( null ) );
        $this->assertSame( 0, CjwNewsletterArticlePoolFinder::count( null ) );
    }

    public function testED10TheAnonymousViewIsAtMostWhatTheEditorSees()
    {
        $limitation = CjwNewsletterArticlePoolFinder::anonymousLimitation();
        $this->assertTrue( $limitation === false || is_array( $limitation ) );
        $pool = $this->newPool( array( 'parent_node_id_array_string' => ';2;', 'class_identifier_array_string' => ';ng_article;article;', 'max_items' => 200 ) );
        $editor = count( CjwNewsletterArticlePoolFinder::find( $pool ) );
        $anonymous = count( CjwNewsletterArticlePoolFinder::find( $pool, array( 'anonymous' => true ) ) );
        $this->assertLessThanOrEqual( $editor, $anonymous );
        // the newsletter section is not public: the test articles under an edition are not in the anonymous view
        $source = $this->newEdition( 'NLTEST anonymous source', false );
        $this->newArticle( $source, 'NLTEST anonymous' );
        $hidden = $this->newPool( array( 'parent_node_id_array_string' => ';' . $source->attribute( 'main_node_id' ) . ';' ) );
        $this->assertCount( 1, CjwNewsletterArticlePoolFinder::find( $hidden ) );
        $anon = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        if ( $anon && $anon->hasAccessTo( 'content', 'read' )['accessWord'] !== 'yes' && !$source->checkAccess( 'read', false, false, false, $anon ) )
            $this->assertSame( array(), CjwNewsletterArticlePoolFinder::find( $hidden, array( 'anonymous' => true ) ) );
    }

    // ------------------------------------------------------------------ ED-11..13 the edition builder

    public function testED11TheLatestUnsentEdition()
    {
        $old = $this->newEdition( 'NLTEST old edition' );
        sleep( 1 );
        $new = $this->newEdition( 'NLTEST new edition' );
        $found = CjwNewsletterEditionBuilder::latestUnsentEdition( self::LIST_OBJECT_ID );
        if ( $this->foreignUnsentEdition() )
            $this->markTestSkipped( 'another unsent edition of the list (made in parallel) is newer' );
        $this->assertSame( (int)$new->attribute( 'id' ), (int)$found->attribute( 'id' ) );
        $this->markSent( $new );
        $found = CjwNewsletterEditionBuilder::latestUnsentEdition( self::LIST_OBJECT_ID );
        $this->assertNotSame( (int)$new->attribute( 'id' ), (int)$found->attribute( 'id' ), 'a sent edition is skipped' );
        if ( !$this->foreignUnsentEdition() )
            $this->assertSame( (int)$old->attribute( 'id' ), (int)$found->attribute( 'id' ), 'the next newest unsent one' );
        $this->assertNull( CjwNewsletterEditionBuilder::latestUnsentEdition( 999999999 ) );
        $this->assertNull( CjwNewsletterEditionBuilder::editionContent( null ) );
    }

    public function testED12ACopyOfATemplateEditionWithItsArticles()
    {
        $template = $this->newEdition( 'NLTEST template' );
        $copy = CjwNewsletterEditionBuilder::copyEdition( $template, self::LIST_OBJECT_ID, 'NLTEST template copy' );
        $this->assertInstanceOf( 'eZContentObject', $copy );
        $this->createdObjectIds[] = (int)$copy->attribute( 'id' );
        $this->assertNotSame( (int)$template->attribute( 'id' ), (int)$copy->attribute( 'id' ) );
        $this->assertSame( 'NLTEST template copy', $copy->attribute( 'name' ) );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)$copy->attribute( 'status' ) );
        $this->assertSame( self::LIST_NODE_ID, (int)$copy->attribute( 'main_node' )->attribute( 'parent_node_id' ) );
        $content = CjwNewsletterEditionBuilder::editionContent( $copy );
        $this->assertInstanceOf( 'CjwNewsletterEdition', $content, 'the copy has its newsletter data' );
        $this->assertTrue( $content->isDraft() );
        $children = $copy->attribute( 'main_node' )->subTree( array( 'Depth' => 1, 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'cjw_newsletter_article' ) ) );
        $this->assertCount( 1, $children, 'the article of the template comes along' );
        $this->assertFalse( CjwNewsletterEditionBuilder::copyEdition( $this->newArticle( $template, 'x' ), self::LIST_OBJECT_ID ), 'only editions are copied' );
        $this->assertFalse( CjwNewsletterEditionBuilder::copyEdition( $template, 999999999 ), 'only into a list that exists' );
        $out = CjwNewsletterEdition::getOutput( $copy->attribute( 'id' ), $copy->attribute( 'current_version' ), 0, 'site', 'default' );
        $this->assertArrayNotHasKey( 'error', $out );
        $this->assertStringContainsString( 'NLTEST article body line', $out['body']['html'] );
    }

    public function testED13PicksGoIntoTheEditionAndOutAgain()
    {
        $edition = $this->newEdition( 'NLTEST picks', false );
        $source = $this->newEdition( 'NLTEST picks source', false );
        $article = $this->newArticle( $source, 'NLTEST picked intro' );
        $node = $article->attribute( 'main_node' );
        $row = CjwNewsletterEditionBuilder::addArticle( $edition, $node, CjwNewsletterEditionArticle::ADDED_BY_EDITOR, 0 );
        $this->assertInstanceOf( 'CjwNewsletterEditionArticle', $row );
        $this->assertSame( (int)$article->attribute( 'id' ), (int)$row->attribute( 'contentobject_id' ) );
        $this->assertSame( 1, (int)$row->attribute( 'position' ) );
        $again = CjwNewsletterEditionBuilder::addArticle( $edition, $node );
        $this->assertSame( (int)$row->attribute( 'id' ), (int)$again->attribute( 'id' ), 'taken once' );
        $copy = $row->copyNode();
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $copy );
        $this->assertSame( (int)$edition->attribute( 'main_node_id' ), (int)$copy->attribute( 'parent_node_id' ) );
        $this->assertSame( 'cjw_newsletter_article', $copy->attribute( 'class_identifier' ) );
        $this->assertSame( $article->attribute( 'name' ), $copy->attribute( 'name' ) );
        $xml = $copy->attribute( 'data_map' )['short_description']->attribute( 'data_text' );
        $this->assertStringContainsString( 'NLTEST picked intro', $xml );
        $this->assertStringContainsString( 'node_id="' . (int)$node->attribute( 'node_id' ) . '"', $xml );
        $this->assertSame( array( (int)$article->attribute( 'id' ) ), CjwNewsletterEditionBuilder::pickedObjectIds( $edition->attribute( 'id' ) ) );
        $this->assertNotSame( '', $row->addedByName() );
        $this->assertSame( (int)$article->attribute( 'id' ), (int)$row->articleObject()->attribute( 'id' ) );
        // the skin shows the pick
        $out = CjwNewsletterEdition::getOutput( $edition->attribute( 'id' ), $edition->attribute( 'current_version' ), 0, 'site', 'default' );
        $this->assertStringContainsString( 'NLTEST picked intro', $out['body']['html'] );
        $this->assertFalse( CjwNewsletterEditionBuilder::addArticle( $edition, $edition->attribute( 'main_node' ) ), 'not the edition into itself' );
        $this->assertTrue( CjwNewsletterEditionBuilder::removeArticle( $edition->attribute( 'id' ), $article->attribute( 'id' ) ) );
        $this->assertSame( array(), CjwNewsletterEditionBuilder::pickedObjectIds( $edition->attribute( 'id' ) ) );
        $this->assertNull( eZContentObject::fetchByRemoteID( CjwNewsletterEditionArticle::copyRemoteId( $edition->attribute( 'id' ), $article->attribute( 'id' ) ) ) );
        $this->assertFalse( CjwNewsletterEditionBuilder::removeArticle( $edition->attribute( 'id' ), $article->attribute( 'id' ) ) );
        // ezstring intros are taken as text
        $this->assertStringContainsString( '<link node_id=', CjwNewsletterEditionBuilder::introXml( $node ) );
    }

    // ------------------------------------------------------------------ ED-14..21 the runner

    public function testED14ModeLatestSendsTheLatestUnsentEdition()
    {
        $edition = $this->newEdition( 'NLTEST latest run' );
        if ( $this->foreignUnsentEdition() )
            $this->markTestSkipped( 'another unsent edition of the list (made in parallel) is newer' );
        $now = time();
        $s = $this->newSchedule( array( 'next_run' => $now - 10 ) );
        $totals = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, $now, (int)$s->attribute( 'id' ) );
        $this->assertTrue( $totals['ok'] );
        $this->assertSame( 1, $totals['due'] );
        $this->assertSame( 1, $totals['sent'], json_encode( $totals['runs'] ) );
        $run = $totals['runs'][0];
        $this->assertSame( (int)$edition->attribute( 'id' ), $run['edition_id'] );
        $send = CjwNewsletterEditionSend::fetch( $run['send_id'] );
        $this->assertSame( (int)$s->attribute( 'id' ), (int)$send->attribute( 'schedule_id' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_WAIT_FOR_SCHEDULE, (int)$send->attribute( 'status' ) );
        $this->assertLessThanOrEqual( $now, (int)$send->attribute( 'mailqueue_process_scheduled' ) );
        $fresh = CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) );
        $this->assertSame( 'sent', $fresh->attribute( 'last_result' ) );
        $this->assertSame( $now, (int)$fresh->attribute( 'last_run' ) );
        $this->assertSame( (int)$run['send_id'], (int)$fresh->attribute( 'last_edition_send_id' ) );
        $this->assertGreaterThan( $now, (int)$fresh->attribute( 'next_run' ) );
        $log = $fresh->recentLog();
        $this->assertCount( 1, $log );
        $this->assertSame( 'sent', $log[0]->attribute( 'result' ) );
        $this->assertSame( $now, $fresh->lastSentTime() );
        $this->assertNotFalse( CjwNewsletterRunner::lastRun( CjwNewsletterScheduleRunner::LAST_RUN ) );
        // the edition is in process now: not taken again
        $this->assertTrue( $this->editionContent( $edition )->isProcess() );
    }

    public function testED15ModeCopyFillsTheCopyFromThePoolAndSkipsWhenNothingIsNew()
    {
        $template = $this->newEdition( 'NLTEST copy template' );
        $pool = $this->newPool( array( 'parent_node_id_array_string' => ';2;', 'class_identifier_array_string' => ';ng_article;article;', 'max_items' => 2 ) );
        $public = CjwNewsletterArticlePoolFinder::count( $pool, array( 'anonymous' => true ) );
        if ( $public < 3 )
            $this->markTestSkipped( 'the site has fewer than three public articles for the auto-fill' );
        $now = time();
        $s = $this->newSchedule( array( 'mode' => 'copy', 'template_edition_contentobject_id' => $template->attribute( 'id' ), 'auto_fill' => 1,
                                        'article_pool_id' => $pool->attribute( 'id' ), 'skip_if_empty' => 1, 'next_run' => $now - 1 ) );
        $totals = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, $now, (int)$s->attribute( 'id' ) );
        $run = $totals['runs'][0];
        $this->assertSame( 'sent', $run['result'], $run['message'] );
        $this->assertSame( 2, $run['article_count'] );
        $copy = eZContentObject::fetch( $run['edition_id'] );
        $this->createdObjectIds[] = (int)$copy->attribute( 'id' );
        $this->assertNotSame( (int)$template->attribute( 'id' ), (int)$copy->attribute( 'id' ) );
        $this->assertStringStartsWith( 'NLTEST copy template ', $copy->attribute( 'name' ) );
        $picks = CjwNewsletterEditionArticle::fetchByEdition( $copy->attribute( 'id' ) );
        $this->assertCount( 2, $picks );
        foreach ( $picks as $pick )
        {
            $this->assertSame( CjwNewsletterEditionArticle::ADDED_BY_AUTO_FILL, (int)$pick->attribute( 'added_by' ) );
            $this->assertSame( (int)$pool->attribute( 'id' ), (int)$pick->attribute( 'article_pool_id' ) );
        }
        $this->assertSame( (int)$s->attribute( 'id' ), (int)CjwNewsletterEditionSend::fetch( $run['send_id'] )->attribute( 'schedule_id' ) );
        $first = CjwNewsletterEditionBuilder::pickedObjectIds( $copy->attribute( 'id' ) );

        // the next run: the articles the first copy carried are left out, the next two come
        $later = $now + 7 * 86400;
        $totals = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, $later, (int)$s->attribute( 'id' ), true );
        $run2 = $totals['runs'][0];
        // published since the last send: the site's articles are older, so nothing is new
        $this->assertSame( 'skipped_empty', $run2['result'], 'content published before the last send does not count as new' );
        $this->assertSame( 0, $run2['edition_id'], 'no copy is made when nothing is new' );
        $this->assertSame( 'skipped_empty', CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->attribute( 'last_result' ) );
        $this->assertCount( 2, CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->recentLog() );

        // without skip_if_empty the copy goes out empty
        $s = CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) );
        $s->setAttribute( 'skip_if_empty', 0 );
        $s->store();
        $run3 = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, $later + 60, (int)$s->attribute( 'id' ), true )['runs'][0];
        $this->assertSame( 'sent', $run3['result'], $run3['message'] );
        $this->createdObjectIds[] = (int)$run3['edition_id'];
        $this->assertSame( array(), array_intersect( $first, CjwNewsletterEditionBuilder::pickedObjectIds( $run3['edition_id'] ) ) );
    }

    public function testED16ConditionsSkipOrFail()
    {
        require_once __DIR__ . '/fixtures/editorial/condition_never.php';
        $this->newEdition( 'NLTEST condition' );
        $this->setIni( 'cjw_newsletter.ini', 'ScheduleSettings', 'ConditionHandlers', array( 'cjwNewsletterEditorialTestConditionNo', 'NoSuchClassPhpunit', 'stdClass' ) );
        $this->assertSame( array( 'cjwNewsletterEditorialTestConditionNo' ), array_keys( CjwNewsletterScheduleRunner::conditionHandlers() ), 'only existing classes of the interface' );
        $s = $this->newSchedule( array( 'condition_handler' => 'cjwNewsletterEditorialTestConditionNo', 'next_run' => time() - 1 ) );
        $run = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, null, (int)$s->attribute( 'id' ) )['runs'][0];
        $this->assertSame( 'skipped_condition', $run['result'] );
        $this->assertSame( 0, $run['send_id'] );
        $s = $this->newSchedule( array( 'condition_handler' => 'stdClass', 'next_run' => time() - 1 ) );
        $run = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, null, (int)$s->attribute( 'id' ) )['runs'][0];
        $this->assertSame( 'failed', $run['result'], 'a class that is not a condition is never called' );
        $this->assertGreaterThan( time(), (int)CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->attribute( 'next_run' ), 'a failed run moves on' );
        // the shipped condition: new articles in the pool
        $pool = $this->newPool( array( 'parent_node_id_array_string' => ';2;', 'class_identifier_array_string' => ';no_such_class_phpunit;' ) );
        $c = CjwNewsletterSchedule::create( array( 'article_pool_id' => $pool->attribute( 'id' ) ) );
        $this->assertFalse( CjwNewsletterScheduleConditionNewArticles::isMet( $c, time() ) );
        $this->assertNotSame( '', CjwNewsletterScheduleConditionNewArticles::name() );
    }

    public function testED17FailuresAreLoggedAndTheScheduleMovesOn()
    {
        $s = $this->newSchedule( array( 'list_contentobject_id' => 999999999, 'next_run' => time() - 1 ) );
        $run = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, null, (int)$s->attribute( 'id' ) )['runs'][0];
        $this->assertSame( 'failed', $run['result'] );
        $this->assertStringContainsString( '999999999', $run['message'] );
        $s = $this->newSchedule( array( 'mode' => 'copy', 'template_edition_contentobject_id' => 999999999, 'next_run' => time() - 1 ) );
        $run = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, null, (int)$s->attribute( 'id' ) )['runs'][0];
        $this->assertSame( 'failed', $run['result'] );
        $log = CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->recentLog();
        $this->assertSame( 'failed', $log[0]->attribute( 'result' ) );
        $this->assertNotSame( '', (string)$log[0]->attribute( 'message' ) );
    }

    public function testED18ADryRunWritesNothing()
    {
        $edition = $this->newEdition( 'NLTEST dry run' );
        $s = $this->newSchedule( array( 'next_run' => time() - 1 ) );
        $before = CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) );
        $lastRun = CjwNewsletterRunner::lastRun( CjwNewsletterScheduleRunner::LAST_RUN );
        $totals = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', true, null, (int)$s->attribute( 'id' ) );
        $this->assertSame( 'skipped_dry_run', $totals['runs'][0]['result'] );
        $after = CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) );
        $this->assertSame( $before->attribute( 'next_run' ), $after->attribute( 'next_run' ) );
        $this->assertSame( '', (string)$after->attribute( 'last_result' ) );
        $this->assertSame( array(), $after->recentLog() );
        $this->assertSame( 0, count( (array)CjwNewsletterEditionSend::fetchByEditionContentObjectId( $edition->attribute( 'id' ) ) ) );
        $this->assertSame( $lastRun, CjwNewsletterRunner::lastRun( CjwNewsletterScheduleRunner::LAST_RUN ), 'no run is recorded' );
    }

    public function testED19PausedAndNotDueSchedulesWaitUnlessForced()
    {
        $this->newEdition( 'NLTEST paused' );
        $paused = $this->newSchedule( array( 'status' => CjwNewsletterSchedule::STATUS_PAUSED, 'next_run' => 0 ) );
        $this->assertSame( 0, CjwNewsletterScheduleRunner::runDue( false, 'phpunit', true, null, (int)$paused->attribute( 'id' ) )['due'] );
        $future = $this->newSchedule( array( 'next_run' => time() + 86400 ) );
        $this->assertSame( 0, CjwNewsletterScheduleRunner::runDue( false, 'phpunit', true, null, (int)$future->attribute( 'id' ) )['due'] );
        $this->assertSame( 1, CjwNewsletterScheduleRunner::runDue( false, 'phpunit', true, null, (int)$future->attribute( 'id' ), true )['due'] );
        $this->assertSame( 1, CjwNewsletterScheduleRunner::runDue( false, 'phpunit', true, time() + 2 * 86400, (int)$future->attribute( 'id' ) )['due'], '--at moves the time' );
    }

    public function testED20OneRunAtATime()
    {
        $lock = CjwNewsletterRunner::lock( 'schedule' );
        $this->assertNotFalse( $lock );
        try
        {
            $totals = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false );
            $this->assertTrue( $totals['locked'] );
            $this->assertFalse( $totals['ok'] );
        }
        finally
        {
            CjwNewsletterRunner::unlock( $lock );
        }
    }

    public function testED21TheQueueCreateHookRunsTheDueSchedulesWhenEnabled()
    {
        $edition = $this->newEdition( 'NLTEST hook' );
        if ( $this->foreignUnsentEdition() )
            $this->markTestSkipped( 'another unsent edition of the list (made in parallel) is newer' );
        $s = $this->newSchedule( array( 'next_run' => time() - 1 ) );
        $cli = new CjwNewsletterJobOutput( false );
        $this->setIni( 'cjw_newsletter.ini', 'ScheduleSettings', 'Schedules', 'disabled' );
        CjwNewsletterEditorialHooks::queueCreateBefore( $cli );
        $this->assertSame( '', (string)CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->attribute( 'last_result' ), 'disabled: not run' );
        $this->setIni( 'cjw_newsletter.ini', 'ScheduleSettings', 'Schedules', 'enabled' );
        // only this test's schedule may be due
        foreach ( CjwNewsletterSchedule::fetchDue() as $due )
            if ( !in_array( (int)$due->attribute( 'id' ), $this->scheduleIds ) )
                $this->markTestSkipped( 'another schedule of the site is due' );
        CjwNewsletterEditorialHooks::queueCreateBefore( $cli );
        $this->assertSame( 'sent', CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->attribute( 'last_result' ) );
        $this->assertCount( 1, (array)CjwNewsletterEditionSend::fetchByEditionContentObjectId( $edition->attribute( 'id' ) ) );
        $this->assertContains( 'CjwNewsletterEditorialHooks', CjwNewsletterExtensionPoints::handlers() );
    }

    // ------------------------------------------------------------------ ED-22..27 the approval

    public function testED22ARequestMakesAnInboxItemForTheApprovers()
    {
        $edition = $this->newEdition( 'NLTEST approval' );
        $this->setList( 'approval_required', 1 );
        $version = (int)$edition->attribute( 'current_version' );
        $this->assertTrue( CjwNewsletterApprovalFlow::isRequired( self::LIST_OBJECT_ID ) );
        $this->assertSame( self::LIST_OBJECT_ID, CjwNewsletterApprovalFlow::listOfEdition( $edition->attribute( 'id' ) ) );
        $this->assertSame( 'none', CjwNewsletterApprovalFlow::state( $edition->attribute( 'id' ), $version ) );
        $this->assertFalse( CjwNewsletterApprovalFlow::maySend( $edition->attribute( 'id' ), $version ) );
        $anonymousId = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        $approval = CjwNewsletterApprovalFlow::request( $edition, $version, $anonymousId, 'NLTEST please' );
        $this->assertInstanceOf( 'CjwNewsletterApproval', $approval );
        $this->assertSame( 'pending', CjwNewsletterApprovalFlow::state( $edition->attribute( 'id' ), $version ) );
        $this->assertSame( self::LIST_OBJECT_ID, (int)$approval->attribute( 'list_contentobject_id' ) );
        $item = eZCollaborationItem::fetch( $approval->attribute( 'collaboration_item_id' ) );
        $this->assertInstanceOf( 'eZCollaborationItem', $item );
        $this->assertSame( 'cjwnewsletterapproval', $item->attribute( 'type_identifier' ) );
        $this->assertSame( (int)$edition->attribute( 'id' ), (int)$item->attribute( 'data_int1' ) );
        $this->assertSame( (string)$approval->attribute( 'id' ), (string)$item->attribute( 'data_text1' ) );
        $roles = array();
        foreach ( eZCollaborationItemParticipantLink::fetchParticipantList( array( 'item_id' => $item->attribute( 'id' ) ) ) as $link )
            $roles[(int)$link->attribute( 'participant_id' )] = (int)$link->attribute( 'participant_role' );
        $this->assertSame( eZCollaborationItemParticipantLink::ROLE_APPROVER, $roles[$this->adminId()] );
        $this->assertSame( eZCollaborationItemParticipantLink::ROLE_AUTHOR, $roles[$anonymousId] );
        $this->assertSame( 1, (int)eZCollaborationItemMessageLink::fetchItemCount( array( 'item_id' => $item->attribute( 'id' ) ) ), 'the message of the request' );
        $handler = $item->attribute( 'handler' );
        $this->assertInstanceOf( 'CjwNewsletterApprovalCollaborationHandler', $handler );
        $this->assertStringContainsString( 'NLTEST approval', $handler->title( $item ) );
        $this->assertSame( (int)$approval->attribute( 'id' ), $handler->content( $item )['approval_id'] );
        // the same request again is the same row
        $this->assertSame( (int)$approval->attribute( 'id' ), (int)CjwNewsletterApprovalFlow::request( $edition, $version, $anonymousId )->attribute( 'id' ) );
        $this->assertFalse( CjwNewsletterApprovalFlow::request( $this->newArticle( $edition, 'x' ), 1, $anonymousId ), 'only editions' );
        $pending = array_map( function ( $a ) { return (int)$a->attribute( 'id' ); }, CjwNewsletterApprovalFlow::pending() );
        $this->assertContains( (int)$approval->attribute( 'id' ), $pending );
    }

    public function testED23TheSendIsRefusedAndHeldUntilApproved()
    {
        $edition = $this->newEdition( 'NLTEST held' );
        $this->setList( 'approval_required', 1 );
        $version = $edition->attribute( 'current' );
        $errors = CjwNewsletterEditorialHooks::sendFormValidate( eZHTTPTool::instance(), $version );
        $this->assertCount( 1, $errors );
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() );
        $this->assertFalse( CjwNewsletterEditorialHooks::sendProcessAllowed( $send ) );
        $this->assertFalse( CjwNewsletterExtensionPoints::allows( 'sendProcessAllowed', array( $send ) ) );
        $approval = CjwNewsletterApprovalFlow::request( $edition, $version->attribute( 'version' ), (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        $this->assertStringContainsString( 'waits', implode( ' ', CjwNewsletterEditorialHooks::sendFormValidate( eZHTTPTool::instance(), $version ) ) );
        $this->assertTrue( CjwNewsletterApprovalFlow::decide( $approval, true, $this->adminId(), 'NLTEST fine' ) );
        $this->assertSame( array(), CjwNewsletterEditorialHooks::sendFormValidate( eZHTTPTool::instance(), $version ) );
        $this->assertTrue( CjwNewsletterEditorialHooks::sendProcessAllowed( $send ) );
        // without approval_required everything may be sent
        $this->setList( 'approval_required', 0 );
        $other = $this->newEdition( 'NLTEST free' );
        $this->assertSame( array(), CjwNewsletterEditorialHooks::sendFormValidate( eZHTTPTool::instance(), $other->attribute( 'current' ) ) );
        $this->assertSame( array(), CjwNewsletterEditorialHooks::sendFormValidate( eZHTTPTool::instance(), null ) );
    }

    public function testED24ADecisionClosesTheInboxItem()
    {
        $edition = $this->newEdition( 'NLTEST decide' );
        $this->setList( 'approval_required', 1 );
        $anonymousId = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        $approval = CjwNewsletterApprovalFlow::request( $edition, 0, $anonymousId );
        $this->assertFalse( CjwNewsletterApprovalFlow::decide( $approval, true, $anonymousId ), 'the anonymous user may not approve' );
        $this->assertTrue( CjwNewsletterApprovalFlow::decide( $approval, false, $this->adminId(), 'NLTEST no' ) );
        $approval = CjwNewsletterApproval::fetch( $approval->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterApproval::STATUS_REJECTED, (int)$approval->attribute( 'status' ) );
        $this->assertSame( $this->adminId(), (int)$approval->attribute( 'decided_by' ) );
        $this->assertSame( 'NLTEST no', $approval->attribute( 'comment' ) );
        $item = eZCollaborationItem::fetch( $approval->attribute( 'collaboration_item_id' ) );
        $this->assertSame( 2, (int)$item->attribute( 'data_int3' ) );
        $this->assertSame( eZCollaborationItem::STATUS_INACTIVE, (int)$item->attribute( 'status' ) );
        $this->assertFalse( CjwNewsletterApprovalFlow::decide( $approval, true, $this->adminId() ), 'decided already' );
        $this->assertFalse( CjwNewsletterApprovalFlow::maySend( $edition->attribute( 'id' ), $edition->attribute( 'current_version' ) ) );
        // after a rejection a new request can be made
        $again = CjwNewsletterApprovalFlow::request( $edition, 0, $anonymousId );
        $this->assertNotSame( (int)$approval->attribute( 'id' ), (int)$again->attribute( 'id' ) );
        $this->assertSame( 'pending', CjwNewsletterApprovalFlow::state( $edition->attribute( 'id' ), $edition->attribute( 'current_version' ) ) );
        $this->assertNotSame( '', $again->statusName() );
        $this->assertNotSame( '', $again->editionName() );
    }

    public function testED25NobodyApprovesHisOwnRequestUnlessAllowed()
    {
        $edition = $this->newEdition( 'NLTEST self' );
        $this->setList( 'approval_required', 1 );
        $approval = CjwNewsletterApprovalFlow::request( $edition, 0, $this->adminId() );
        $this->assertSame( 0, (int)$approval->attribute( 'collaboration_item_id' ), 'the only approver asked: nobody else gets it' );
        $this->assertFalse( CjwNewsletterApprovalFlow::canDecide( $approval ) );
        $this->assertFalse( CjwNewsletterApprovalFlow::decide( $approval, true, $this->adminId() ) );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'AllowSelfApproval', 'enabled' );
        $this->assertTrue( CjwNewsletterApprovalFlow::decide( $approval, true, $this->adminId() ) );
        $this->assertTrue( CjwNewsletterApprovalFlow::isApproved( $edition->attribute( 'id' ), $edition->attribute( 'current_version' ) ) );
    }

    public function testED26ApproversComeFromUsersAndGroups()
    {
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'ApproverUserIds', array( $this->adminId(), 999999999, 'x' ) );
        $this->assertSame( array( $this->adminId() ), CjwNewsletterApprovalFlow::approverUserIds() );
        $admin = eZUser::fetch( $this->adminId() );
        $groups = $admin->attribute( 'groups' );
        if ( $groups )
        {
            $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'ApproverUserIds', array() );
            $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'ApproverGroupIds', array( $groups[0] ) );
            $this->assertContains( $this->adminId(), CjwNewsletterApprovalFlow::approverUserIds(), 'a member of the group' );
        }
    }

    public function testED27AChangeOfThePicksReplacesTheApproval()
    {
        $edition = $this->newEdition( 'NLTEST withdraw' );
        $this->setList( 'approval_required', 1 );
        $approval = CjwNewsletterApprovalFlow::request( $edition, 0, (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        CjwNewsletterApprovalFlow::decide( $approval, true, $this->adminId() );
        $this->assertSame( 1, CjwNewsletterApprovalFlow::withdraw( $edition->attribute( 'id' ) ) );
        $this->assertSame( 'none', CjwNewsletterApprovalFlow::state( $edition->attribute( 'id' ), $edition->attribute( 'current_version' ) ) );
        $this->assertSame( CjwNewsletterApproval::STATUS_WITHDRAWN, (int)CjwNewsletterApproval::fetch( $approval->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertSame( 3, (int)eZCollaborationItem::fetch( $approval->attribute( 'collaboration_item_id' ) )->attribute( 'data_int3' ) );
        $this->assertCount( 1, CjwNewsletterApprovalFlow::history( $edition->attribute( 'id' ) ) );
        // a scheduled send of a list with approval asks for it and waits
        if ( $this->foreignUnsentEdition() )
            $this->markTestSkipped( 'another unsent edition of the list (made in parallel) is newer' );
        $s = $this->newSchedule( array( 'next_run' => time() - 1 ) );
        $run = CjwNewsletterScheduleRunner::runDue( false, 'phpunit', false, null, (int)$s->attribute( 'id' ) )['runs'][0];
        $this->assertSame( 'sent', $run['result'], $run['message'] );
        $this->assertSame( 'pending', CjwNewsletterApprovalFlow::state( $edition->attribute( 'id' ), $edition->attribute( 'current_version' ) ) );
        $this->assertFalse( CjwNewsletterEditorialHooks::sendProcessAllowed( CjwNewsletterEditionSend::fetch( $run['send_id'] ) ) );
    }

    // ------------------------------------------------------------------ ED-28..32 hooks, dashboard, views, fetches, command

    public function testED28TheListAttributeTakesTheApprovalAndThePool()
    {
        $pool = $this->newPool();
        $attribute = $this->listAttribute();
        $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
        $base = 'ContentObjectAttribute_CjwNewsletterList_';
        $id = '_' . $attribute->attribute( 'id' );
        $_POST = array();
        $this->assertSame( array(), CjwNewsletterEditorialHooks::listAttributeInput( $list, eZHTTPTool::instance(), 'ContentObjectAttribute', '', $attribute ), 'no part posted: nothing changes' );
        $_POST = array( $base . 'EditorialPart' . $id => 1, $base . 'ApprovalRequired' . $id => '1', $base . 'ArticlePoolId' . $id => (string)$pool->attribute( 'id' ) );
        $this->assertSame( array(), CjwNewsletterEditorialHooks::listAttributeInput( $list, eZHTTPTool::instance(), 'ContentObjectAttribute', '', $attribute ) );
        $this->assertSame( 1, (int)$list->attribute( 'approval_required' ) );
        $this->assertSame( (int)$pool->attribute( 'id' ), (int)$list->attribute( 'article_pool_id' ) );
        $_POST = array( $base . 'EditorialPart' . $id => 1, $base . 'ArticlePoolId' . $id => '999999999' );
        $this->assertCount( 1, CjwNewsletterEditorialHooks::listAttributeInput( $list, eZHTTPTool::instance(), 'ContentObjectAttribute', '', $attribute ) );
        $this->assertSame( 0, (int)$list->attribute( 'approval_required' ) );
        $_POST = array();
    }

    protected function listAttribute()
    {
        foreach ( eZContentObject::fetch( self::LIST_OBJECT_ID )->dataMap() as $attribute )
            if ( $attribute->attribute( 'data_type_string' ) === 'cjwnewsletterlist' )
                return $attribute;
        $this->fail( 'the list has no list attribute' );
    }

    public function testED29TheDashboardShowsSchedulesAndApprovals()
    {
        $edition = $this->newEdition( 'NLTEST dashboard' );
        $this->setList( 'approval_required', 1 );
        $s = $this->newSchedule( array( 'next_run' => time() + 3600 ) );
        CjwNewsletterApprovalFlow::request( $edition, 0, (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        $summary = CjwNewsletterDashboard::summary();
        $this->assertArrayHasKey( 'CjwNewsletterEditorialHooks', $summary['areas'] );
        $e = $summary['areas']['CjwNewsletterEditorialHooks'];
        foreach ( array( 'enabled', 'schedule_count', 'active_count', 'next', 'pending', 'pending_count', 'pool_count', 'last_run', 'problems' ) as $key )
            $this->assertArrayHasKey( $key, $e );
        $this->assertGreaterThanOrEqual( 1, $e['active_count'] );
        $this->assertGreaterThanOrEqual( 1, $e['pending_count'] );
        $this->setIni( 'cjw_newsletter.ini', 'ScheduleSettings', 'Schedules', 'disabled' );
        $codes = array_map( function ( $p ) { return $p['code']; }, CjwNewsletterEditorialHooks::dashboardSummary( array() )['problems'] );
        $this->assertContains( 'schedules_disabled', $codes );
        $this->setIni( 'cjw_newsletter.ini', 'ScheduleSettings', 'Schedules', 'enabled' );
        $r = $this->runView( 'index' );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'nl-area-editorial', $r['content'] );
        $this->assertStringContainsString( 'NLTEST dashboard', $r['content'] );
    }

    public function testED30TheScheduleAndPoolViews()
    {
        $template = $this->newEdition( 'NLTEST view template' );
        $r = $this->runView( 'schedule_edit', array( 0 ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'name="RecurrenceType"', $r['content'] );
        // a bad form is shown again with the errors
        $r = $this->runView( 'schedule_edit', array( 0 ), array( 'StoreButton' => 1, 'ListId' => 0, 'Mode' => 'copy', 'SendTime' => '25:99', 'RecurrenceType' => 'd' ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'message-error', $r['content'] );
        $before = CjwNewsletterSchedule::fetchListCount();
        $r = $this->runView( 'schedule_edit', array( 0 ), array( 'StoreButton' => 1, 'ListId' => self::LIST_OBJECT_ID, 'Mode' => 'copy',
            'TemplateEditionId' => $template->attribute( 'id' ), 'RecurrenceType' => 'd', 'Weekdays' => array( '2', '4', '9', 'x' ),
            'SendTime' => '07:15', 'Timezone' => 'Europe/Vienna', 'AutoFill' => 1, 'SkipIfEmpty' => 1, 'Status' => 0 ) );
        $this->assertNotEmpty( $r['redirect'], 'stored and redirected' );
        $this->assertSame( $before + 1, CjwNewsletterSchedule::fetchListCount() );
        $rows = CjwNewsletterSchedule::fetchList( null, 1 );
        $s = $rows[0];
        $this->scheduleIds[] = (int)$s->attribute( 'id' );
        $this->assertSame( 'copy', $s->attribute( 'mode' ) );
        $this->assertSame( '2,4', $s->attribute( 'recurrence_value' ) );
        $this->assertSame( 7 * 3600 + 15 * 60, (int)$s->attribute( 'send_time' ) );
        $this->assertSame( 'Europe/Vienna', $s->attribute( 'timezone' ) );
        $this->assertSame( 1, (int)$s->attribute( 'auto_fill' ) );
        $this->assertGreaterThan( time(), (int)$s->attribute( 'next_run' ) );
        $this->assertSame( $this->adminId(), (int)$s->attribute( 'creator_contentobject_id' ) );
        $r = $this->runView( 'schedule_list' );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'NLTEST view template', $r['content'] );
        $r = $this->runView( 'schedule_list', array(), array( 'PauseButton' => array( $s->attribute( 'id' ) => 1 ) ) );
        $this->assertSame( 0, (int)CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->attribute( 'next_run' ) );
        $r = $this->runView( 'schedule_list', array(), array( 'ResumeButton' => array( $s->attribute( 'id' ) => 1 ) ) );
        $this->assertGreaterThan( 0, (int)CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->attribute( 'next_run' ) );
        $r = $this->runView( 'schedule_list', array(), array( 'DryRunButton' => array( $s->attribute( 'id' ) => 1 ) ) );
        $this->assertSame( array(), CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->recentLog(), 'the try writes nothing' );
        $r = $this->runView( 'schedule_list', array(), array( 'RemoveButton' => array( $s->attribute( 'id' ) => 1 ) ) );
        $this->assertStringContainsString( 'ConfirmRemoveButton', $r['content'] );
        $this->runView( 'schedule_list', array(), array( 'ConfirmRemoveButton' => array( $s->attribute( 'id' ) => 1 ) ) );
        $this->assertNull( CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) ) );
        $this->assertSame( eZError::KERNEL_NOT_AVAILABLE, $this->runView( 'schedule_edit', array( 999999999 ) )['module']->errorCode() );

        // the pools
        $r = $this->runView( 'article_pool_edit', array( 0 ), array( 'StoreButton' => 1, 'Name' => '', 'ParentNodeIds' => '999999999' ) );
        $this->assertStringContainsString( 'message-error', $r['content'] );
        $r = $this->runView( 'article_pool_edit', array( 0 ), array( 'PreviewButton' => 1, 'Name' => 'NLTEST view pool', 'ParentNodeIds' => (string)$template->attribute( 'main_node_id' ),
            'ClassIdentifiers' => array( 'cjw_newsletter_article', 'nosuchclass' ), 'MaxItems' => '5', 'SortBy' => 'name' ) );
        $this->assertStringContainsString( 'NLTEST article title', $r['content'], 'the preview lists what the pool finds' );
        $count = CjwNewsletterArticlePool::fetchListCount();
        $r = $this->runView( 'article_pool_edit', array( 0 ), array( 'StoreButton' => 1, 'Name' => 'NLTEST view pool', 'ParentNodeIds' => (string)$template->attribute( 'main_node_id' ),
            'ClassIdentifiers' => array( 'cjw_newsletter_article', 'nosuchclass' ), 'SectionIds' => array( '1', '999999' ), 'MaxItems' => '5', 'MaxAgeDays' => '30', 'SortBy' => 'name' ) );
        $this->assertNotEmpty( $r['redirect'] );
        $this->assertSame( $count + 1, CjwNewsletterArticlePool::fetchListCount() );
        $pool = CjwNewsletterArticlePool::fetchList( null, 1 )[0];
        $this->poolIds[] = (int)$pool->attribute( 'id' );
        $this->assertSame( ';cjw_newsletter_article;', $pool->attribute( 'class_identifier_array_string' ) );
        $this->assertSame( ';1;', $pool->attribute( 'section_id_array_string' ) );
        $this->assertSame( 'name', $pool->attribute( 'sort_by' ) );
        $r = $this->runView( 'article_pool_list' );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'NLTEST view pool', $r['content'] );
        $this->runView( 'article_pool_list', array(), array( 'ConfirmRemoveButton' => array( $pool->attribute( 'id' ) => 1 ) ) );
        $this->assertNull( CjwNewsletterArticlePool::fetch( $pool->attribute( 'id' ) ) );
    }

    public function testED31ThePickerAndTheApprovalViews()
    {
        $edition = $this->newEdition( 'NLTEST picker', false );
        $source = $this->newEdition( 'NLTEST picker source', false );
        $article = $this->newArticle( $source, 'NLTEST picker article' );
        $pool = $this->newPool( array( 'parent_node_id_array_string' => ';' . $source->attribute( 'main_node_id' ) . ';' ) );
        $this->setList( 'article_pool_id', (int)$pool->attribute( 'id' ) );
        $nodeId = (int)$edition->attribute( 'main_node_id' );
        $r = $this->runView( 'article_pool', array( $nodeId ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'NLTEST article title', $r['content'] );
        // a filter is a redirect to the view parameters, cast
        $r = $this->runView( 'article_pool', array( $nodeId ), array( 'FilterButton' => 1, 'FilterClass' => "x'", 'FilterFrom' => '2020-02-30', 'FilterTo' => date( 'Y-m-d' ), 'FilterTag' => 'abc' ) );
        $this->assertStringContainsString( '(to)/' . date( 'Y-m-d' ), (string)$r['redirect'] );
        $this->assertStringNotContainsString( '(class)', (string)$r['redirect'] );
        $this->assertStringNotContainsString( '(from)', (string)$r['redirect'] );
        // something outside the pool cannot be taken
        $this->runView( 'article_pool', array( $nodeId ), array( 'AddButton' => 1, 'AddNodeIds' => array( 2 ) ) );
        $this->assertSame( array(), CjwNewsletterEditionBuilder::pickedObjectIds( $edition->attribute( 'id' ) ) );
        $this->runView( 'article_pool', array( $nodeId ), array( 'AddButton' => 1, 'AddNodeIds' => array( $article->attribute( 'main_node_id' ) ) ) );
        $this->assertSame( array( (int)$article->attribute( 'id' ) ), CjwNewsletterEditionBuilder::pickedObjectIds( $edition->attribute( 'id' ) ) );
        $r = $this->runView( 'article_pool', array( $nodeId ) );
        $this->assertStringContainsString( 'RemoveButton[' . $article->attribute( 'id' ) . ']', $r['content'] );
        $this->runView( 'article_pool', array( $nodeId ), array( 'RemoveButton' => array( $article->attribute( 'id' ) => 1 ) ) );
        $this->assertSame( array(), CjwNewsletterEditionBuilder::pickedObjectIds( $edition->attribute( 'id' ) ) );
        $this->assertSame( eZError::KERNEL_NOT_AVAILABLE, $this->runView( 'article_pool', array( self::LIST_NODE_ID ) )['module']->errorCode(), 'only for an edition' );

        // the approval view: ask (as admin, the only approver: nobody else gets it), allow self approval, approve
        $this->setList( 'approval_required', 1 );
        $version = (int)$edition->attribute( 'current_version' );
        $r = $this->runView( 'approval', array( $edition->attribute( 'id' ), $version ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'RequestButton', $r['content'] );
        $this->runView( 'approval', array( $edition->attribute( 'id' ), $version ), array( 'RequestButton' => 1, 'Comment' => 'NLTEST view request' ) );
        $this->assertSame( 'pending', CjwNewsletterApprovalFlow::state( $edition->attribute( 'id' ), $version ) );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'AllowSelfApproval', 'enabled' );
        $r = $this->runView( 'approval', array( $edition->attribute( 'id' ), $version ) );
        $this->assertStringContainsString( 'ApproveButton', $r['content'] );
        $this->runView( 'approval', array( $edition->attribute( 'id' ), $version ), array( 'ApproveButton' => 1, 'Comment' => 'NLTEST ok' ) );
        $this->assertSame( 'approved', CjwNewsletterApprovalFlow::state( $edition->attribute( 'id' ), $version ) );
        $this->assertSame( eZError::KERNEL_NOT_AVAILABLE, $this->runView( 'approval', array( $edition->attribute( 'id' ), 999 ) )['module']->errorCode() );
        $this->assertSame( eZError::KERNEL_NOT_AVAILABLE, $this->runView( 'approval', array( self::LIST_OBJECT_ID ) )['module']->errorCode() );
    }

    public function testED32FetchFunctionsCheckAccess()
    {
        $edition = $this->newEdition( 'NLTEST fetch', false );
        $source = $this->newEdition( 'NLTEST fetch source', false );
        $article = $this->newArticle( $source, 'NLTEST fetch article' );
        CjwNewsletterEditionBuilder::addArticle( $edition, $article->attribute( 'main_node' ) );
        $state = CjwNewsletterEditorialFetch::fetchApprovalState( $edition->attribute( 'id' ) );
        $this->assertIsArray( $state['result'] );
        $this->assertSame( 'none', $state['result']['state'] );
        $this->assertCount( 1, CjwNewsletterEditorialFetch::fetchEditionArticles( $edition->attribute( 'id' ) )['result'] );
        $this->assertFalse( CjwNewsletterEditorialFetch::fetchApprovalState( 999999999 )['result'] );
        $this->assertInstanceOf( 'CjwNewsletterArticlePool', CjwNewsletterEditorialFetch::fetchListArticlePool( self::LIST_OBJECT_ID )['result'] );
        $this->assertIsArray( CjwNewsletterEditorialFetch::fetchArticlePoolList()['result'] );
        $this->assertIsArray( CjwNewsletterEditorialFetch::fetchScheduleList( self::LIST_OBJECT_ID )['result'] );
        $this->loginAnonymous();
        if ( !$edition->canRead() )
        {
            $this->assertFalse( CjwNewsletterEditorialFetch::fetchApprovalState( $edition->attribute( 'id' ) )['result'], 'not for who may not read the edition' );
            $this->assertSame( array(), CjwNewsletterEditorialFetch::fetchEditionArticles( $edition->attribute( 'id' ) )['result'] );
        }
        $this->loginAdmin();
    }

    public function testED34PickCopiesAndTheEditionsOwnArticlesAreNeverOffered()
    {
        $edition = $this->newEdition( 'NLTEST own' );
        $source = $this->newEdition( 'NLTEST own source', false );
        $article = $this->newArticle( $source, 'NLTEST own article' );
        // a pool over the whole list: it holds the editions' own articles and the copies the picks make
        $pool = $this->newPool( array( 'parent_node_id_array_string' => ';' . self::LIST_NODE_ID . ';', 'class_identifier_array_string' => ';cjw_newsletter_article;', 'max_items' => 200 ) );
        CjwNewsletterEditionBuilder::addArticle( $edition, $article->attribute( 'main_node' ) );
        $copy = eZContentObject::fetchByRemoteID( CjwNewsletterEditionArticle::copyRemoteId( $edition->attribute( 'id' ), $article->attribute( 'id' ) ) );
        $this->assertInstanceOf( 'eZContentObject', $copy );
        $found = array_map( function ( $n ) { return (int)$n->attribute( 'contentobject_id' ); }, CjwNewsletterArticlePoolFinder::find( $pool ) );
        $this->assertContains( (int)$article->attribute( 'id' ), $found );
        $this->assertNotContains( (int)$copy->attribute( 'id' ), $found, 'a pick copy is never a source' );
        $own = CjwNewsletterEditionBuilder::childObjectIds( $edition );
        $this->assertCount( 2, $own, 'the template article and the pick copy' );
        $this->assertContains( (int)$copy->attribute( 'id' ), $own );
        $this->assertSame( array(), CjwNewsletterEditionBuilder::childObjectIds( null ) );
        // the picker does not offer the edition's own article, and refuses it when it is posted
        $this->setList( 'article_pool_id', (int)$pool->attribute( 'id' ) );
        $nodeId = (int)$edition->attribute( 'main_node_id' );
        $ownArticle = array_values( array_diff( $own, array( (int)$copy->attribute( 'id' ) ) ) )[0];
        $r = $this->runView( 'article_pool', array( $nodeId ) );
        $this->assertStringNotContainsString( 'value="' . eZContentObject::fetch( $ownArticle )->attribute( 'main_node_id' ) . '"', $r['content'] );
        $this->runView( 'article_pool', array( $nodeId ), array( 'AddButton' => 1, 'AddNodeIds' => array( eZContentObject::fetch( $ownArticle )->attribute( 'main_node_id' ) ) ) );
        $this->assertSame( array( (int)$article->attribute( 'id' ) ), CjwNewsletterEditionBuilder::pickedObjectIds( $edition->attribute( 'id' ) ) );
    }

    public function testED33TheCommand()
    {
        $stub = 'extension/cjw_newsletter/bin/php/schedule.php';
        $this->assertTrue( is_executable( $stub ) );
        $code = file_get_contents( $stub );
        $this->assertStringContainsString( '@alias nl-schedule', $code );
        $this->assertStringContainsString( '\\Exponential\\Command\\Extension\\CjwNewsletter\\Schedule::main( __FILE__ )', $code );
        $this->newEdition( 'NLTEST command' );
        $s = $this->newSchedule( array( 'next_run' => time() + 86400 ) );
        list( $exit, $out ) = $this->command( array( 'list' ) );
        $this->assertSame( 0, $exit, $out );
        $this->assertStringContainsString( '#' . $s->attribute( 'id' ), $out );
        list( $exit, $out ) = $this->command( array( 'run', '--dry-run', '--id=' . $s->attribute( 'id' ), '--at=' . date( 'Y-m-d H:i', time() + 2 * 86400 ) ) );
        $this->assertSame( 0, $exit, $out );
        $this->assertStringContainsString( 'skipped_dry_run', $out );
        $this->assertStringContainsString( 'Dry run: 1 due', $out );
        $this->assertSame( array(), CjwNewsletterSchedule::fetch( $s->attribute( 'id' ) )->recentLog(), 'the dry run wrote nothing' );
        list( $exit, $out ) = $this->command( array( 'nonsense' ) );
        $this->assertSame( 2, $exit );
        list( $exit, $out ) = $this->command( array( 'run', '--dry-run', '--at=not a time' ) );
        $this->assertSame( 2, $exit );
        list( $exit, $out ) = $this->command( array( '--help' ) );
        $this->assertStringContainsString( '--dry-run', $out );
    }

    /** @return array( exit code, output ) of the command in a process of its own */
    private function command( array $arguments )
    {
        $line = array_merge( array( PHP_BINARY, 'extension/cjw_newsletter/bin/php/schedule.php', '-s', 'admin', '--allow-root-user' ), $arguments );
        $process = proc_open( $line, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, eZSys::rootDir(),
                              array( 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin' ) );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $process ), $out );
    }
}

