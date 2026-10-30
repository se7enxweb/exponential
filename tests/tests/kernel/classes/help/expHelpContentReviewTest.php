<?php
/**
 * Overdue reviews of help maintained as content (doc/eng-us/features/6.0/help-content.md, "Overdue reviews"), without a
 * database: the rule, the settings, and the dashboard's part (shared admin components, no inline CSS, the same file in
 * both designs).
 *
 *  HCR-01  a past review date makes a topic overdue in every state; a future one or none does not
 *  HCR-02  a topic in Review is overdue after ReviewMaxDays without a change, not before; Draft and Released are not
 *  HCR-03  a topic with both reasons is listed once, with the older one; the list is ordered longest overdue first
 *  HCR-04  the settings and their defaults
 *  HCR-05  the dashboard part: shared components, no style, only for who can act, identical in admin and admin4
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentReviewTest extends PHPUnit\Framework\TestCase
{
    const NOW = 1792281600; // 2026-10-18 00:00 UTC
    const DAY = 86400;

    private static function topic( $helpId, $state, $reviewDate, $modified )
    {
        return array( 'object_id' => crc32( $helpId ) % 10000, 'node_id' => 7, 'help_id' => $helpId, 'title' => ucfirst( $helpId ),
                      'type' => 'concept', 'state' => $state, 'review_date' => $reviewDate, 'modified' => $modified );
    }

    private static function ids( array $list )
    {
        return array_column( $list, 'help_id' );
    }

    /** HCR-01 */
    public function testReviewDate()
    {
        $list = expHelpContentReview::overdue( array(
            self::topic( 'a.past', 'released', self::NOW - 10 * self::DAY, self::NOW - 400 * self::DAY ),
            self::topic( 'a.past-draft', 'draft', self::NOW - 1, self::NOW ),
            self::topic( 'a.future', 'released', self::NOW + self::DAY, self::NOW - 400 * self::DAY ),
            self::topic( 'a.none', 'released', 0, self::NOW - 400 * self::DAY ),
        ), self::NOW, 14 );
        $this->assertSame( array( 'a.past', 'a.past-draft' ), self::ids( $list ) );
        $this->assertSame( expHelpContentReview::REASON_DATE, $list[0]['reason'] );
        $this->assertSame( 10, $list[0]['days'] );
        $this->assertSame( self::NOW - 10 * self::DAY, $list[0]['since'] );
        $this->assertSame( 0, $list[1]['days'] );
    }

    /** HCR-02 */
    public function testTooLongInReview()
    {
        $list = expHelpContentReview::overdue( array(
            self::topic( 'b.stale', 'review', 0, self::NOW - 15 * self::DAY ),
            self::topic( 'b.fresh', 'review', 0, self::NOW - 13 * self::DAY ),
            self::topic( 'b.edge', 'review', 0, self::NOW - 14 * self::DAY ),
            self::topic( 'b.draft', 'draft', 0, self::NOW - 90 * self::DAY ),
            self::topic( 'b.released', 'released', 0, self::NOW - 90 * self::DAY ),
        ), self::NOW, 14 );
        $this->assertSame( array( 'b.stale' ), self::ids( $list ) );
        $this->assertSame( expHelpContentReview::REASON_IN_REVIEW, $list[0]['reason'] );
        $this->assertSame( 15, $list[0]['days'] );
        $this->assertSame( array( 'b.stale', 'b.fresh' ), self::ids( expHelpContentReview::overdue( array(
            self::topic( 'b.stale', 'review', 0, self::NOW - 15 * self::DAY ),
            self::topic( 'b.fresh', 'review', 0, self::NOW - 13 * self::DAY ) ), self::NOW, 7 ) ) );
        // at least one day, whatever the setting says
        $this->assertSame( array( 'b.x' ), self::ids( expHelpContentReview::overdue( array(
            self::topic( 'b.x', 'review', 0, self::NOW - 2 * self::DAY ) ), self::NOW, 0 ) ) );
    }

    /** HCR-03 */
    public function testBothReasonsAndOrder()
    {
        $list = expHelpContentReview::overdue( array(
            self::topic( 'c.recent', 'released', self::NOW - 2 * self::DAY, self::NOW - 400 * self::DAY ),
            self::topic( 'c.both', 'review', self::NOW - 3 * self::DAY, self::NOW - 30 * self::DAY ),
            self::topic( 'c.old', 'released', self::NOW - 50 * self::DAY, self::NOW - 400 * self::DAY ),
        ), self::NOW, 14 );
        $this->assertSame( array( 'c.old', 'c.both', 'c.recent' ), self::ids( $list ) );
        $this->assertSame( expHelpContentReview::REASON_IN_REVIEW, $list[1]['reason'], 'the older reason' );
        $this->assertSame( 30, $list[1]['days'] );
    }

    /** HCR-04 */
    public function testSettings()
    {
        $ini = eZINI::instance( 'exphelp.ini' );
        $this->assertSame( '14', $ini->variable( 'ContentSettings', 'ReviewMaxDays' ) );
        $this->assertSame( '10', $ini->variable( 'ContentSettings', 'ReviewListLimit' ) );
        $this->assertSame( 14, expHelpContentReview::maxDays() );
    }

    /** HCR-05 */
    public function testDashboardPart()
    {
        $root = dirname( __DIR__, 5 );
        $part = (string)file_get_contents( $root . '/design/admin4/templates/exphelp/parts/dashboard_review.tpl' );
        $this->assertSame( $part, (string)file_get_contents( $root . '/design/admin/templates/exphelp/parts/dashboard_review.tpl' ) );
        $this->assertDoesNotMatchRegularExpression( '/<style\b|\sstyle\s*=/i', $part );
        foreach ( array( 'a4-card', 'a4-table-wrap', 'a4-table', 'a4-tag', 'a4-btn', 'a4-sr-only', 'a4-help-section-head' ) as $class )
            $this->assertStringContainsString( $class, $part );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $dashboard = (string)file_get_contents( $root . "/design/$design/templates/exphelp/dashboard.tpl" );
            $this->assertStringContainsString( "{if and( is_set( \$help_review ), \$help_review )}\n    {include uri='design:exphelp/parts/dashboard_review.tpl' review=\$help_review}", $dashboard, $design );
        }
        // the view gives the list only to who can act on a topic, and nothing when there is nothing overdue
        $view = (string)file_get_contents( $root . '/kernel/private/classes/views/exphelp/dashboard.php' );
        $this->assertStringContainsString( "if ( !\$review || !\$review['count'] )\n                return false;", $view );
        $class = (string)file_get_contents( $root . '/kernel/classes/help/content/exphelpcontentreview.php' );
        $this->assertStringContainsString( 'if ( !$object->canEdit() && !$assignable )', $class );
    }
}

?>
