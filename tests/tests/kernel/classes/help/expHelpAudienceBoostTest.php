<?php
/**
 * The audience factor of the help search, [SearchSettings] AudienceBoost[] (doc/eng-us/features/6.0/help-system.md). No
 * database.
 *
 *  AB-01  a trainer lesson ranks below an editor page with the same words in a general search, and first when the
 *         search is filtered to trainers; browse mode orders the same way
 *  AB-02  audienceFactor(): no factor without a setting, the lowest of a page's audiences, none while one of them is
 *         selected
 *  AB-03  settings/exphelp.ini sets AudienceBoost[trainer] below 1
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpAudienceBoostTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

if ( !class_exists( 'expHelpSearch' ) )
    require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/exphelpsearch.php';

class expHelpAudienceBoostTest extends PHPUnit\Framework\TestCase
{
    private static function search( array $boost )
    {
        return expHelpSearch::fromDocuments( array(
            'help/trainers/editors/lesson-publish.md' => array(
                'title' => 'Lesson: publish an article', 'audience' => array( 'editor', 'trainer' ), 'category' => 'training', 'type' => 'help',
                'text' => "# Lesson: publish an article\n\nPublish an article step by step: publish, publish, publish.\n" ),
            'help/publishing-and-versions.md' => array(
                'title' => 'Publish an article', 'audience' => array( 'editor' ), 'category' => 'publishing', 'type' => 'help',
                'text' => "# Publish an article\n\nHow to publish an article.\n" ),
        ), array( 'audienceBoost' => $boost ) );
    }

    /** AB-01 */
    public function testTrainerLessonsRankBelowUnlessFiltered()
    {
        $plain = self::search( array() )->search( 'publish article' );
        $plainScores = array_column( $plain['results'], 'score', 'id' );

        $search = self::search( array( 'trainer' => 0.3 ) );
        $general = $search->search( 'publish article' );
        $scores = array_column( $general['results'], 'score', 'id' );
        $this->assertEqualsWithDelta( $plainScores['help/trainers/editors/lesson-publish.md'] * 0.3, $scores['help/trainers/editors/lesson-publish.md'], 0.01 );
        $this->assertEqualsWithDelta( $plainScores['help/publishing-and-versions.md'], $scores['help/publishing-and-versions.md'], 0.01 );
        $this->assertSame( array( 'help/publishing-and-versions.md', 'help/trainers/editors/lesson-publish.md' ), array_column( $general['results'], 'id' ) );

        $trainers = $search->search( 'publish article', array( 'audience' => 'trainer' ) );
        $this->assertSame( array( 'help/trainers/editors/lesson-publish.md' ), array_column( $trainers['results'], 'id' ) );
        $this->assertGreaterThan( $general['results'][1]['score'], $trainers['results'][0]['score'], 'no factor under the trainer filter' );

        $editors = $search->search( 'publish article', array( 'audience' => 'editor' ) );
        $this->assertSame( 'help/publishing-and-versions.md', $editors['results'][0]['id'], 'the factor stays when another of its audiences is selected' );
    }

    /** AB-02 */
    public function testFactor()
    {
        $search = self::search( array( 'trainer' => 0.6, 'press' => 0.8 ) );
        $this->assertSame( 1.0, self::search( array() )->audienceFactor( array( 'x', 't', array( 'trainer' ) ), array() ) );
        $this->assertSame( 1.0, $search->audienceFactor( array( 'x', 't', array( 'editor' ) ), array() ) );
        $this->assertSame( 0.6, $search->audienceFactor( array( 'x', 't', array( 'editor', 'trainer' ) ), array() ) );
        $this->assertSame( 0.6, $search->audienceFactor( array( 'x', 't', array( 'press', 'trainer' ) ), array( 'audience' => array() ) ) );
        $this->assertSame( 1.0, $search->audienceFactor( array( 'x', 't', array( 'press', 'trainer' ) ), array( 'audience' => array( 'press' ) ) ) );
        $this->assertSame( 1.0, $search->audienceFactor( array( 'x', 't', array() ), array() ) );
    }

    /** AB-03 */
    public function testSetting()
    {
        $ini = (string)file_get_contents( dirname( __DIR__, 5 ) . '/settings/exphelp.ini' );
        $this->assertMatchesRegularExpression( '/^AudienceBoost\[trainer\]=0?\.\d+\s*$/m', $ini );
    }
}
