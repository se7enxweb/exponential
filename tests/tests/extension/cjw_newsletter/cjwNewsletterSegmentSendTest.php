<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';
/**
 * cjw_newsletter 4.3.0, area N7 Segments: a send to a segment (send form part, queue).
 *
 * Live style (cjwNewsletterTestCase): the installation's own database, throwaway subscribers nltest-*@example.invalid,
 * the file transport. The segments a test made are removed in tearDown.
 *
 *  SG-01  Send to a segment: members who unsubscribe after the preview get no item
 *  SG-02  A segment changed after queuing: the queued send keeps its recipients
 *  SG-03  The send form part: validation refuses a foreign segment, a part not posted leaves the send alone
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class cjwNewsletterSegmentSendTest extends cjwNewsletterTestCase
{
    /** @var int[] */
    protected $segmentIds = array();

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'CjwNewsletterSegmentHooks' ) || !class_exists( 'CjwNewsletterSegment' ) )
            $this->markTestSkipped( 'the segment classes are not loaded' );
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $db = eZDB::instance();
            foreach ( $this->segmentIds as $id )
                $db->query( 'DELETE FROM cjwnl_segment WHERE id = ' . (int)$id );
            $this->segmentIds = array();
        }
        parent::tearDown();
    }

    /** A segment of everybody who subscribed within the last day: all throwaway subscribers. */
    protected function newSegment( $listId = null )
    {
        $segment = CjwNewsletterSegment::create( array(
            'list_contentobject_id' => $listId === null ? self::LIST_OBJECT_ID : $listId,
            'name' => 'nltest segment',
            'match_mode' => 'all',
            'conditions' => json_encode( array( array( 'type' => 'subscribed_within', 'field' => 'created', 'days' => 1 ) ) ),
            'created' => time(), 'modified' => time() ) );
        $segment->store();
        $this->segmentIds[] = (int)$segment->attribute( 'id' );
        return $segment;
    }

    /** @return int the items of the send that belong to the throwaway subscribers of this test */
    protected function ownItemCount( $sendId )
    {
        $db = eZDB::instance();
        $in = array();
        foreach ( array_unique( $this->extraEmails ) as $email )
            $in[] = "'" . $db->escapeString( $email ) . "'";
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM cjwnl_edition_send_item i JOIN cjwnl_user u ON u.id = i.newsletter_user_id'
            . ' WHERE i.edition_send_id = ' . (int)$sendId . ' AND u.email IN ( ' . implode( ', ', $in ) . ' )' );
        return (int)$rows[0]['c'];
    }

    protected function queued( CjwNewsletterSegment $segment )
    {
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() + 86400 );
        $send->setAttribute( 'segment_id', (int)$segment->attribute( 'id' ) );
        $send->setAttribute( 'mailqueue_process_scheduled', time() - 5 );
        $send->store();
        for ( $i = 0; $i < 60; $i++ )
        {
            $totals = CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
            if ( empty( $totals['locked'] ) )
                return CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
            usleep( 500000 );
        }
        $this->fail( "the runner's queueCreate stayed locked" );
    }

    public function testSendToASegmentLeavesOutThoseWhoUnsubscribedAfterThePreview()
    {
        $users = array();
        for ( $n = 0; $n < 120; $n++ )
            $users[] = $this->newSubscriber( 'sg' . $n );
        $segment = $this->newSegment();
        $options = CjwNewsletterSegmentHooks::sendFormOptions( self::LIST_OBJECT_ID, true );
        $counts = array();
        foreach ( $options as $option )
            $counts[$option['id']] = $option['count'];
        $this->assertGreaterThanOrEqual( 120, $counts[(int)$segment->attribute( 'id' )], 'the form shows the size before the send' );
        foreach ( array_slice( $users, 0, 3 ) as $user )
            $this->assertTrue( $this->subscriptionOf( $user )->unsubscribe() );

        $send = $this->queued( $segment );
        $this->assertSame( 117, $this->ownItemCount( $send->attribute( 'id' ) ) );
    }

    public function testASegmentChangedAfterQueuingKeepsTheQueuedRecipients()
    {
        for ( $n = 0; $n < 5; $n++ )
            $this->newSubscriber( 'ch' . $n );
        $segment = $this->newSegment();
        $send = $this->queued( $segment );
        $this->assertSame( 5, $this->ownItemCount( $send->attribute( 'id' ) ) );

        $segment->setAttribute( 'conditions', json_encode( array( array( 'type' => 'subscribed_within', 'field' => 'created', 'days' => 1, 'x' => 1 ), array( 'type' => 'language', 'values' => array( 'xx' ) ) ) ) );
        $segment->setAttribute( 'match_mode', 'all' );
        $segment->store();
        $this->assertSame( 5, $this->ownItemCount( $send->attribute( 'id' ) ), 'the items were written once, at queuing' );
    }

    public function testSendFormPartValidatesTheSegmentAndLeavesASendWithoutThePartAlone()
    {
        $http = eZHTTPTool::instance();
        $object = $this->newEdition();
        $send = $this->editionContent( $object )->createNewsletterSendObject( time() + 86400 );
        // the send view hands the hooks the edition's content object version, not the datatype content
        $version = eZContentObject::fetch( $object->attribute( 'id' ) )->attribute( 'current' );
        $own = $this->newSegment();
        $foreign = $this->newSegment( self::LIST_OBJECT_ID + 100000 );

        $_POST = array( 'CjwNewsletterSegmentId' => (string)$foreign->attribute( 'id' ) );
        $this->assertNotEmpty( CjwNewsletterSegmentHooks::sendFormValidate( $http, $version ) );
        $_POST = array( 'CjwNewsletterSegmentId' => '999999999' );
        $this->assertNotEmpty( CjwNewsletterSegmentHooks::sendFormValidate( $http, $version ) );
        $_POST = array( 'CjwNewsletterSegmentId' => '0' );
        $this->assertSame( array(), CjwNewsletterSegmentHooks::sendFormValidate( $http, $version ) );
        $_POST = array( 'CjwNewsletterSegmentId' => (string)$own->attribute( 'id' ) );
        $this->assertSame( array(), CjwNewsletterSegmentHooks::sendFormValidate( $http, $version ) );

        $send->setAttribute( 'segment_id', (int)$own->attribute( 'id' ) );
        $send->store();
        $_POST = array();
        CjwNewsletterSegmentHooks::sendFormStored( $send, $http, null );
        $this->assertSame( (int)$own->attribute( 'id' ), (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'segment_id' ) );
        $_POST = array( 'CjwNewsletterSegmentId' => '' );
        CjwNewsletterSegmentHooks::sendFormStored( $send, $http, null );
        $this->assertSame( 0, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'segment_id' ) );
        $_POST = array();
    }
}
