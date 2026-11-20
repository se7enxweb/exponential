<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';
/**
 * cjw_newsletter 4.3.0, area N8 Welcome series: entering the series when a subscription is approved.
 *
 * Live style (cjwNewsletterTestCase): the installation's own database, throwaway subscribers nltest-*@example.invalid.
 * The series a test made is removed in tearDown.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class cjwNewsletterSeriesEnterLiveTest extends cjwNewsletterTestCase
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
        if ( !class_exists( 'CjwNewsletterSeriesHooks' ) || !class_exists( 'CjwNewsletterSeries' ) )
            $this->markTestSkipped( 'the series classes are not loaded' );
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $db = eZDB::instance();
            foreach ( $this->seriesIds as $id )
            {
                $db->query( 'DELETE FROM cjwnl_series_member WHERE series_id = ' . (int)$id );
                $db->query( 'DELETE FROM cjwnl_series WHERE id = ' . (int)$id );
            }
            $this->seriesIds = array();
        }
        parent::tearDown();
    }

    protected function newSeries()
    {
        $series = CjwNewsletterSeries::create( array( 'list_contentobject_id' => self::listObjectId(), 'status' => 0,
            'created' => time(), 'modified' => time() ) );
        $series->store();
        $this->seriesIds[] = (int)$series->attribute( 'id' );
        return $series;
    }

    protected function members( CjwNewsletterSeries $series )
    {
        return CjwNewsletterSeriesMember::fetchBySeries( (int)$series->attribute( 'id' ) );
    }

    protected function pendingSubscription()
    {
        $user = $this->newSubscriber( 'series', null, CjwNewsletterSubscription::STATUS_PENDING );
        return $this->subscriptionOf( $user );
    }

    public function testApprovalMakesOneMemberAndRepeatChangesNothing()
    {
        $series = $this->newSeries();
        $sub = $this->pendingSubscription();
        $sub->setAttribute( 'status', CjwNewsletterSubscription::STATUS_APPROVED );
        $sub->store();
        $this->assertCount( 1, $this->members( $series ) );
        $sub->store();
        CjwNewsletterSeries::enterApproved( $sub );
        $this->assertCount( 1, $this->members( $series ) );
    }

    public function testRemovalAndApprovalKeepProgress()
    {
        $series = $this->newSeries();
        $sub = $this->pendingSubscription();
        $sub->setAttribute( 'status', CjwNewsletterSubscription::STATUS_APPROVED );
        $sub->store();
        $member = CjwNewsletterSeriesMember::fetchBySeriesAndSubscription( $series->attribute( 'id' ), $sub->attribute( 'id' ) );
        $member->setAttribute( 'last_position', 2 );
        $member->store();
        $sub->setAttribute( 'status', CjwNewsletterSubscription::STATUS_REMOVED_ADMIN );
        $sub->store();
        $sub->setAttribute( 'status', CjwNewsletterSubscription::STATUS_APPROVED );
        $sub->store();
        $members = $this->members( $series );
        $this->assertCount( 1, $members );
        $this->assertSame( 2, (int)$members[0]->attribute( 'last_position' ) );
    }

    public function testImportMakesNoMember()
    {
        $series = $this->newSeries();
        $sub = $this->pendingSubscription();
        CjwNewsletterExtensionPoints::beginBulk();
        try
        {
            $sub->setAttribute( 'status', CjwNewsletterSubscription::STATUS_APPROVED );
            $sub->store();
        }
        finally
        {
            CjwNewsletterExtensionPoints::endBulk();
        }
        $this->assertCount( 0, $this->members( $series ) );
    }
}
