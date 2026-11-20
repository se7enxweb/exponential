<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';
/**
 * cjw_newsletter 4.3.0, area N8 Welcome series: sending the steps through the queue-create run.
 *
 * Live style (cjwNewsletterTestCase): the installation's own database, throwaway subscribers nltest-*@example.invalid,
 * the file transport. The series, steps and members a test made are removed in tearDown.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class cjwNewsletterSeriesSendTest extends cjwNewsletterTestCase
{
    /** @var int[] */
    protected $seriesIds = array();
    /** @var array|null list row id and its tracking mode before the test, restored in tearDown */
    protected $savedTracking = null;

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'CjwNewsletterSeriesRunner' ) || !class_exists( 'CjwNewsletterSeries' ) )
            $this->markTestSkipped( 'the series classes are not loaded' );
        $this->useFileTransport();
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $db = eZDB::instance();
            if ( $this->savedTracking !== null )
                $db->query( 'UPDATE cjwnl_list SET tracking_mode = ' . (int)$this->savedTracking[1] . ' WHERE ' . $this->savedTracking[0] );
            $this->savedTracking = null;
            foreach ( $this->seriesIds as $id )
            {
                $db->query( 'DELETE FROM cjwnl_series_member WHERE series_id = ' . (int)$id );
                $db->query( 'DELETE FROM cjwnl_series_step WHERE series_id = ' . (int)$id );
                $db->query( 'DELETE FROM cjwnl_series WHERE id = ' . (int)$id );
            }
            $this->seriesIds = array();
        }
        parent::tearDown();
    }

    /** An active series of the list with three steps (0, 3 and 14 days), each with an edition of its own. */
    protected function newSeries()
    {
        $series = CjwNewsletterSeries::create( array( 'list_contentobject_id' => self::listObjectId(), 'status' => 0,
            'created' => time(), 'modified' => time() ) );
        $series->store();
        $this->seriesIds[] = (int)$series->attribute( 'id' );
        foreach ( array( 0, 3, 14 ) as $position => $days )
        {
            CjwNewsletterSeriesStep::create( array( 'series_id' => (int)$series->attribute( 'id' ), 'position' => $position + 1,
                'edition_contentobject_id' => (int)$this->newEdition()->attribute( 'id' ), 'delay_days' => $days,
                'created' => time(), 'modified' => time() ) )->store();
        }
        return $series;
    }

    /** A subscriber who is approved after the series exists, so the approval enters the series. */
    protected function enteredSubscription()
    {
        $user = $this->newSubscriber( 'ser', null, CjwNewsletterSubscription::STATUS_PENDING );
        $sub = $this->subscriptionOf( $user );
        $sub->setAttribute( 'status', CjwNewsletterSubscription::STATUS_APPROVED );
        $sub->store();
        return $sub;
    }

    protected function member( CjwNewsletterSeries $series, CjwNewsletterSubscription $sub )
    {
        return CjwNewsletterSeriesMember::fetchBySeriesAndSubscription( (int)$series->attribute( 'id' ), (int)$sub->attribute( 'id' ) );
    }

    /** One queue-create run (it runs the series first); waits while another run holds the lock. */
    protected function queueCreate()
    {
        for ( $i = 0; $i < 60; $i++ )
        {
            $totals = CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
            if ( empty( $totals['locked'] ) )
                return;
            usleep( 500000 );
        }
        $this->fail( "the runner's queueCreate stayed locked" );
    }

    /** @return array the sends of the steps of the series: step position => send rows */
    protected function stepSends( CjwNewsletterSeries $series )
    {
        $out = array();
        foreach ( $series->steps() as $step )
        {
            $out[(int)$step->attribute( 'position' )] = (array)eZDB::instance()->arrayQuery(
                'SELECT id, tracking_mode FROM cjwnl_edition_send WHERE series_step_id = ' . (int)$step->attribute( 'id' ) );
        }
        return $out;
    }

    /** @return int the items of the subscription in the sends of the series */
    protected function itemCount( CjwNewsletterSeries $series, CjwNewsletterSubscription $sub )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM cjwnl_edition_send_item i'
            . ' JOIN cjwnl_edition_send s ON s.id = i.edition_send_id JOIN cjwnl_series_step t ON t.id = s.series_step_id'
            . ' WHERE t.series_id = ' . (int)$series->attribute( 'id' ) . ' AND i.subscription_id = ' . (int)$sub->attribute( 'id' ) );
        return (int)$rows[0]['c'];
    }

    /** Moves the member's last send into the past, so that the next step is due in the next run. */
    protected function ageMember( CjwNewsletterSeriesMember $member, $days )
    {
        $member->setAttribute( 'last_sent', time() - $days * 86400 );
        $member->store();
    }

    public function testListApprovesAutomaticallyAndTheWelcomeIsQueuedInTheFirstRun()
    {
        $series = $this->newSeries();
        $sub = $this->enteredSubscription();
        $this->assertNotNull( $this->member( $series, $sub ), 'the approval entered the series' );

        $this->queueCreate();

        $member = $this->member( $series, $sub );
        $this->assertSame( 1, (int)$member->attribute( 'last_position' ), 'the welcome step was sent' );
        $this->assertSame( 0, (int)$member->attribute( 'pending_send_id' ), 'the marker is cleared after queuing' );
        $this->assertSame( 1, $this->itemCount( $series, $sub ) );
    }

    public function testOneStepPerRunAndEachStepOnce()
    {
        $series = $this->newSeries();
        $sub = $this->enteredSubscription();

        $this->queueCreate();
        $this->queueCreate();
        $this->assertSame( 1, (int)$this->member( $series, $sub )->attribute( 'last_position' ), 'the second run waits for the delay' );
        $this->assertSame( 1, $this->itemCount( $series, $sub ) );

        $this->ageMember( $this->member( $series, $sub ), 4 );
        $this->queueCreate();
        $this->assertSame( 2, (int)$this->member( $series, $sub )->attribute( 'last_position' ) );
        $this->assertSame( 2, $this->itemCount( $series, $sub ), 'one step per run, none twice' );
    }

    public function testUnsubscribedAfterStepOneGetsNothingMore()
    {
        $series = $this->newSeries();
        $sub = $this->enteredSubscription();
        $this->queueCreate();
        $this->assertSame( 1, $this->itemCount( $series, $sub ) );

        $this->assertTrue( $sub->unsubscribe() );
        $this->ageMember( $this->member( $series, $sub ), 4 );
        $this->queueCreate();

        $this->assertSame( 1, $this->itemCount( $series, $sub ), 'no item for the second step' );
        $this->assertSame( 1, (int)$this->member( $series, $sub )->attribute( 'last_position' ), 'the progress is kept' );
    }

    public function testTheStepSendHasTheTrackingModeOfTheList()
    {
        $list = CjwNewsletterList::fetchByListObjectVersion( self::listObjectId(),
            (int)eZContentObject::fetch( self::listObjectId() )->attribute( 'current_version' ) );
        if ( !is_object( $list ) )
            $this->markTestSkipped( 'the list is not available' );
        $this->savedTracking = array( 'contentobject_attribute_id = ' . (int)$list->attribute( 'contentobject_attribute_id' )
            . ' AND contentobject_attribute_version = ' . (int)$list->attribute( 'contentobject_attribute_version' ), (int)$list->attribute( 'tracking_mode' ) );
        $mode = CjwNewsletterTracking::MODE_ANONYMOUS;
        eZDB::instance()->query( 'UPDATE cjwnl_list SET tracking_mode = ' . $mode . ' WHERE ' . $this->savedTracking[0] );

        $series = $this->newSeries();
        $this->enteredSubscription();
        $this->queueCreate();

        $sends = $this->stepSends( $series );
        $this->assertCount( 1, $sends[1], 'the welcome send was made' );
        $this->assertSame( $mode, (int)$sends[1][0]['tracking_mode'] );
        $this->assertSame( array(), $sends[2], 'no send for a step that is not due' );
    }
}
