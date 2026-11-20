<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';
/**
 * cjw_newsletter 4.3.0, area N8 Welcome series: the page newsletter/series_edit.
 *
 * Live style (cjwNewsletterTestCase): the installation's own database, the view called in-process as the admin, the
 * file transport. The series, steps and members a test made are removed in tearDown. A test skips when the list
 * already has a series: that one belongs to somebody else and is never touched.
 *
 *  SW-01  The page renders, for a list without and with a series
 *  SW-02  Store of a new series leaves it paused
 *  SW-03  Resume activates the series
 *  SW-04  Step tested: SendTest writes exactly one mail to the editor and changes no member row
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class cjwNewsletterSeriesViewsTest extends cjwNewsletterTestCase
{
    /** @var int[] */
    protected $seriesIds = array();

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'CjwNewsletterSeries' ) )
            $this->markTestSkipped( 'the series classes are not loaded' );
        if ( CjwNewsletterSeries::fetchByList( self::LIST_OBJECT_ID ) !== null )
            $this->markTestSkipped( 'the list already has a series, which a test must not touch' );
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $db = eZDB::instance();
            foreach ( array_unique( $this->seriesIds ) as $id )
            {
                $db->query( 'DELETE FROM cjwnl_series_member WHERE series_id = ' . (int)$id );
                $db->query( 'DELETE FROM cjwnl_series_step WHERE series_id = ' . (int)$id );
                $db->query( 'DELETE FROM cjwnl_series WHERE id = ' . (int)$id );
            }
            $this->seriesIds = array();
        }
        parent::tearDown();
    }

    /** Stores a series with one step through the view, as an editor does. */
    protected function storedSeries()
    {
        $edition = $this->newEdition();
        $r = $this->runView( 'series_edit', array( self::LIST_OBJECT_ID ),
            array( 'AddStepButton' => 'Add', 'Audience' => 0, 'NewEdition' => (int)$edition->attribute( 'id' ), 'NewDelay' => 0 ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $series = CjwNewsletterSeries::fetchByList( self::LIST_OBJECT_ID );
        $this->assertNotNull( $series );
        $this->seriesIds[] = (int)$series->attribute( 'id' );
        return $series;
    }

    public function testPageRenders()
    {
        $new = $this->runView( 'series_edit', array( self::LIST_OBJECT_ID ) );
        $this->assertSame( eZModule::STATUS_OK, $new['exit'] );
        $this->assertNotEmpty( $new['content'] );

        $this->storedSeries();
        $with = $this->runView( 'series_edit', array( self::LIST_OBJECT_ID ) );
        $this->assertSame( eZModule::STATUS_OK, $with['exit'] );
        $this->assertNotEmpty( $with['content'] );

        $foreign = $this->runView( 'series_edit', array( self::LIST_OBJECT_ID + 100000 ) );
        $this->assertSame( eZModule::STATUS_FAILED, $foreign['exit'], 'an unknown list is not served' );
        $this->assertStringNotContainsString( 'newsletter-series_edit', (string)$foreign['content'], 'only the error page, not the series page' );
    }

    public function testStoreOfANewSeriesLeavesItPausedAndResumeActivatesIt()
    {
        $series = $this->storedSeries();
        $this->assertTrue( $series->isPaused(), 'a new series starts paused' );

        $r = $this->runView( 'series_edit', array( self::LIST_OBJECT_ID ), array( 'ResumeButton' => array( 0 => 'Resume' ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertFalse( CjwNewsletterSeries::fetchByList( self::LIST_OBJECT_ID )->isPaused() );

        $r = $this->runView( 'series_edit', array( self::LIST_OBJECT_ID ), array( 'PauseButton' => array( 0 => 'Pause' ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertTrue( CjwNewsletterSeries::fetchByList( self::LIST_OBJECT_ID )->isPaused() );
    }

    public function testStepTestedMailsOnlyTheEditorAndChangesNoMember()
    {
        $email = (string)eZUser::currentUser()->attribute( 'email' );
        if ( $email === '' )
            $this->markTestSkipped( 'the admin account has no e-mail address' );
        $series = $this->storedSeries();
        $steps = $series->steps();
        $stepId = (int)$steps[0]->attribute( 'id' );
        $memberRows = eZDB::instance()->arrayQuery( 'SELECT * FROM cjwnl_series_member WHERE series_id = ' . (int)$series->attribute( 'id' ) );
        $before = $this->outbox();

        $r = $this->runView( 'series_edit', array( self::LIST_OBJECT_ID ), array( 'SendTestButton' => array( $stepId => 'Send to me' ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );

        // a test mail goes out once per output format of the list (as on the send page), every one to the editor
        $edition = eZContentObject::fetch( (int)$steps[0]->attribute( 'edition_contentobject_id' ) );
        $formats = count( $this->editionContent( $edition )->attribute( 'list_attribute_content' )->attribute( 'output_format_array' ) );
        $this->assertGreaterThan( 0, $formats );
        $files = $this->outbox();
        $new = array_diff( $files, $before );
        $this->assertCount( $formats, $new, 'one mail per output format' );
        foreach ( $new as $file )
            $this->assertStringContainsString( $email, $this->mailText( $file ) );
        $this->assertSame( $memberRows, eZDB::instance()->arrayQuery( 'SELECT * FROM cjwnl_series_member WHERE series_id = ' . (int)$series->attribute( 'id' ) ) );
    }
}
