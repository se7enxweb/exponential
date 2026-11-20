<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';
/**
 * cjw_newsletter 4.3.0, area N7 Segments: the pages newsletter/segment_list and newsletter/segment_edit.
 *
 * Live style (cjwNewsletterTestCase): the installation's own database, the views called in-process as the admin.
 * The segments a test made are removed in tearDown.
 *
 *  SV-01  The list and the edit view render
 *  SV-02  Store saves a segment with two conditions (interested organisations)
 *  SV-03  ShowSize evaluates the conditions and saves nothing
 *  SV-04  Delete is refused while a waiting send uses the segment, and works once it does not
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class cjwNewsletterSegmentViewsTest extends cjwNewsletterTestCase
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
        if ( !class_exists( 'CjwNewsletterSegment' ) )
            $this->markTestSkipped( 'the segment classes are not loaded' );
        $this->loginAdmin();
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $db = eZDB::instance();
            foreach ( $this->segmentIds as $id )
                $db->query( 'DELETE FROM cjwnl_segment WHERE id = ' . (int)$id );
            $db->query( "DELETE FROM cjwnl_segment WHERE name LIKE 'nltest views%'" );
            $this->segmentIds = array();
        }
        parent::tearDown();
    }

    protected function newSegment( $name = 'nltest views segment' )
    {
        $segment = CjwNewsletterSegment::create( array(
            'list_contentobject_id' => self::LIST_OBJECT_ID, 'name' => $name, 'match_mode' => 'all',
            'conditions' => json_encode( array( array( 'type' => 'subscribed_within', 'field' => 'created', 'days' => 1 ) ) ),
            'created' => time(), 'modified' => time() ) );
        $segment->store();
        $this->segmentIds[] = (int)$segment->attribute( 'id' );
        return $segment;
    }

    /** The posted form of the Scenario "Interested organisations": an organisation is set and the language is one of two. */
    protected function formPost( $name, array $buttons )
    {
        return array_merge( array(
            'Name' => $name, 'MatchMode' => 'all',
            'Condition' => array(
                0 => array( 'type' => 'profile', 'field' => 'organisation', 'op' => 'set' ),
                1 => array( 'type' => 'language', 'values' => 'de, en' ) ) ), $buttons );
    }

    protected function countNamed( $name )
    {
        $rows = eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS c FROM cjwnl_segment WHERE name = '" . eZDB::instance()->escapeString( $name ) . "'" );
        return (int)$rows[0]['c'];
    }

    public function testListAndEditViewsRender()
    {
        $segment = $this->newSegment();
        $list = $this->runView( 'segment_list', array( self::LIST_OBJECT_ID ) );
        $this->assertSame( eZModule::STATUS_OK, $list['exit'] );
        $this->assertStringContainsString( 'nltest views segment', $list['content'] );

        $new = $this->runView( 'segment_edit', array( 0, self::LIST_OBJECT_ID ) );
        $this->assertSame( eZModule::STATUS_OK, $new['exit'] );
        $this->assertNotEmpty( $new['content'] );

        $edit = $this->runView( 'segment_edit', array( (int)$segment->attribute( 'id' ), self::LIST_OBJECT_ID ) );
        $this->assertSame( eZModule::STATUS_OK, $edit['exit'] );
        $this->assertStringContainsString( 'nltest views segment', $edit['content'] );

        $foreign = $this->runView( 'segment_edit', array( (int)$segment->attribute( 'id' ), self::LIST_OBJECT_ID + 100000 ) );
        $this->assertNotSame( eZModule::STATUS_OK, $foreign['exit'], 'an unknown list is not served' );
    }

    public function testStoreSavesASegmentWithTwoConditions()
    {
        $name = 'nltest views stored';
        $r = $this->runView( 'segment_edit', array( 0, self::LIST_OBJECT_ID ), $this->formPost( $name, array( 'StoreButton' => 'Store' ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertStringContainsString( 'segment_list/' . self::LIST_OBJECT_ID, (string)$r['redirect'] );
        $rows = eZDB::instance()->arrayQuery( "SELECT * FROM cjwnl_segment WHERE name = '" . $name . "'" );
        $this->assertCount( 1, $rows );
        $this->segmentIds[] = (int)$rows[0]['id'];
        $this->assertSame( self::LIST_OBJECT_ID, (int)$rows[0]['list_contentobject_id'] );
        $this->assertSame( 'all', $rows[0]['match_mode'] );
        $conditions = json_decode( $rows[0]['conditions'], true );
        $this->assertCount( 2, $conditions );
        $this->assertSame( array( 'profile', 'language' ), array( $conditions[0]['type'], $conditions[1]['type'] ) );
    }

    public function testStoreWithoutNameOrConditionsSavesNothing()
    {
        $r = $this->runView( 'segment_edit', array( 0, self::LIST_OBJECT_ID ), $this->formPost( '', array( 'StoreButton' => 'Store' ) ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'], 'the form is shown again' );
        $r = $this->runView( 'segment_edit', array( 0, self::LIST_OBJECT_ID ),
            array( 'Name' => 'nltest views empty', 'MatchMode' => 'all', 'StoreButton' => 'Store' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $this->assertSame( 0, $this->countNamed( 'nltest views empty' ) );
    }

    public function testShowSizeDoesNotSave()
    {
        $name = 'nltest views size';
        $r = $this->runView( 'segment_edit', array( 0, self::LIST_OBJECT_ID ), $this->formPost( $name, array( 'ShowSizeButton' => 'Show size' ) ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $this->assertNotEmpty( $r['content'] );
        $this->assertSame( 0, $this->countNamed( $name ) );
    }

    public function testDeleteIsRefusedWhileAWaitingSendUsesTheSegment()
    {
        $segment = $this->newSegment();
        $id = (int)$segment->attribute( 'id' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() + 86400 );
        $send->setAttribute( 'segment_id', $id );
        $send->setAttribute( 'status', CjwNewsletterEditionSend::STATUS_WAIT_FOR_SCHEDULE );
        $send->store();

        $r = $this->runView( 'segment_edit', array( $id, self::LIST_OBJECT_ID ), array( 'DeleteButton' => 'Remove' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'], 'refused: the form is shown again' );
        $this->assertNotNull( CjwNewsletterSegment::fetch( $id ) );

        $send->setAttribute( 'status', CjwNewsletterEditionSend::STATUS_ABORT );
        $send->store();
        $r = $this->runView( 'segment_edit', array( $id, self::LIST_OBJECT_ID ), array( 'DeleteButton' => 'Remove' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertNull( CjwNewsletterSegment::fetch( $id ) );
    }
}
