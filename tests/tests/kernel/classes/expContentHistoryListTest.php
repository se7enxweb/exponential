<?php
/**
 * The list of the versions page (content/history) without the database: expContentHistoryList and
 * History::comparedWith(), and the shape of the view.
 *
 *  HL-01 - The filters of the address: known values are taken, anything else is left out
 *  HL-02 - The address part keeps the filters and leaves out the defaults; the links set one filter each
 *  HL-03 - The rows match the filters, in the order asked for
 *  HL-04 - The figures: versions by status in a fixed order, translations, the user's own drafts
 *  HL-05 - The choices of the filters: creators and translations by name
 *  HL-06 - The actions: view, edit, copy (and its languages), compare and remove, and why one is not offered
 *  HL-07 - The version a version's Compare button compares it with
 *  HL-08 - run() stays short, its steps are methods, and every POST name of the page is still read
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Content\History;

class expContentHistoryListTest extends PHPUnit\Framework\TestCase
{
    const ME = 14;
    const OTHER = 990501;

    private function rows()
    {
        $rows = array();
        foreach ( array( array( 1, 3, 'eng-GB', 'English (United Kingdom)', self::OTHER, 'Zoe', 100, 150 ),
                         array( 2, 1, 'eng-GB', 'English (United Kingdom)', self::ME, 'Anna', 200, 500 ),
                         array( 3, 4, 'ger-DE', 'German', self::OTHER, 'Zoe', 300, 350 ),
                         array( 4, 0, 'ger-DE', 'German', self::ME, 'Anna', 400, 450 ),
                         array( 5, 5, 'eng-GB', 'English (United Kingdom)', self::OTHER, 'Zoe', 410, 410 ),
                         array( 6, 2, 'eng-GB', 'English (United Kingdom)', self::OTHER, 'Zoe', 420, 420 ) ) as $v )
        {
            $rows[] = expContentHistoryList::row( array( 'id' => 100 + $v[0], 'version' => $v[0], 'status' => $v[1], 'language' => $v[2],
                                                         'language_name' => $v[3], 'creator_id' => $v[4], 'creator_name' => $v[5],
                                                         'created' => $v[6], 'modified' => $v[7] ) );
        }
        return $rows;
    }

    private function numbers( array $rows )
    {
        return array_map( function ( $r ) { return $r['version']; }, $rows );
    }

    /** HL-01 */
    public function testFilters()
    {
        $this->assertSame( array( 'status' => '', 'language' => '', 'creator' => 0, 'sort' => 'newest' ), expContentHistoryList::filters( array() ) );
        $this->assertSame( array( 'status' => 'archived', 'language' => 'ger-DE', 'creator' => 14, 'sort' => 'oldest' ),
                           expContentHistoryList::filters( array( 'status' => 'archived', 'language' => 'ger-DE', 'creator' => '14', 'sort' => 'oldest' ) ) );
        $bad = expContentHistoryList::filters( array( 'status' => 'gone', 'language' => "eng-GB'<x>", 'creator' => '14 or 1', 'sort' => 'random' ) );
        $this->assertSame( array( 'status' => '', 'language' => '', 'creator' => 0, 'sort' => 'newest' ), $bad );
        $this->assertSame( 0, expContentHistoryList::filters( array( 'creator' => array( 1 ) ) )['creator'] );
    }

    /** HL-02 */
    public function testSuffixAndLinks()
    {
        $filters = expContentHistoryList::filters( array( 'status' => 'draft', 'creator' => '14', 'sort' => 'oldest' ) );
        $this->assertSame( '/(status)/draft/(creator)/14/(sort)/oldest', expContentHistoryList::suffix( $filters ) );
        $this->assertSame( '', expContentHistoryList::suffix( expContentHistoryList::filters( array() ) ) );
        $this->assertSame( '/(creator)/14/(sort)/oldest', expContentHistoryList::suffix( $filters, array( 'status' => '' ) ) );

        $rows = $this->rows();
        $links = expContentHistoryList::links( $filters, expContentHistoryList::overview( $rows, self::ME )['statuses'], expContentHistoryList::choices( $rows ) );
        $this->assertSame( '/(creator)/14/(sort)/oldest', $links['status']['all'] );
        $this->assertSame( '/(status)/archived/(creator)/14/(sort)/oldest', $links['status']['archived'] );
        $this->assertSame( '/(status)/draft/(language)/ger-DE/(creator)/14/(sort)/oldest', $links['language']['ger-DE'] );
        $this->assertSame( '/(status)/draft/(sort)/oldest', $links['creator']['all'] );
        $this->assertSame( '/(status)/draft/(creator)/14', $links['sort']['newest'] );
        $this->assertArrayHasKey( self::OTHER, $links['creator'] );
    }

    /** HL-03 */
    public function testSelect()
    {
        $rows = $this->rows();
        $this->assertSame( array( 6, 5, 4, 3, 2, 1 ), $this->numbers( expContentHistoryList::select( $rows, expContentHistoryList::filters( array() ) ) ) );
        $this->assertSame( array( 1, 2, 3, 4, 5, 6 ), $this->numbers( expContentHistoryList::select( $rows, array( 'sort' => 'oldest' ) ) ) );
        $this->assertSame( array( 2, 4, 6, 5, 3, 1 ), $this->numbers( expContentHistoryList::select( $rows, array( 'sort' => 'modified' ) ) ) );
        $this->assertSame( array( 4, 3 ), $this->numbers( expContentHistoryList::select( $rows, array( 'language' => 'ger-DE' ) ) ) );
        $this->assertSame( array( 4, 2 ), $this->numbers( expContentHistoryList::select( $rows, array( 'creator' => self::ME ) ) ) );
        $this->assertSame( array( 1 ), $this->numbers( expContentHistoryList::select( $rows, array( 'status' => 'archived' ) ) ) );
        $this->assertSame( array(), expContentHistoryList::select( $rows, array( 'status' => 'archived', 'language' => 'ger-DE' ) ) );
    }

    /** HL-04 */
    public function testOverview()
    {
        $overview = expContentHistoryList::overview( $this->rows(), self::ME );
        $this->assertSame( 6, $overview['total'] );
        $this->assertSame( array( 'draft' => 1, 'published' => 1, 'pending' => 1, 'archived' => 1, 'rejected' => 1, 'untouched' => 1 ), $overview['statuses'] );
        $this->assertSame( 2, $overview['translations'] );
        $this->assertSame( 1, $overview['own_drafts'], 'version 4; the untouched draft 5 is someone else\'s' );
        $this->assertSame( array( 'total' => 0, 'statuses' => array(), 'translations' => 0, 'own_drafts' => 0 ), expContentHistoryList::overview( array(), self::ME ) );
    }

    /** HL-05 */
    public function testChoices()
    {
        $choices = expContentHistoryList::choices( $this->rows() );
        $this->assertSame( array( self::ME => 'Anna', self::OTHER => 'Zoe' ), $choices['creators'] );
        $this->assertSame( array( 'eng-GB' => 'English (United Kingdom)', 'ger-DE' => 'German' ), $choices['languages'] );
    }

    private function actions( $number, array $context = array(), array $extra = array() )
    {
        foreach ( $this->rows() as $row )
        {
            if ( $row['version'] === $number )
                return expContentHistoryList::actions( array_merge( $row, array( 'can_versionread' => true, 'can_remove' => true,
                                                                               'languages' => array( $row['language'] => $row['language_name'] ) ), $extra ),
                                                       array_merge( array( 'can_edit' => true, 'content_versions' => array( 1, 2, 3, 4 ),
                                                                           'edit_languages' => array( 'eng-GB', 'ger-DE' ), 'user_id' => self::ME ), $context ) );
        }
        $this->fail( "no version $number" );
    }

    private function allowed( array $actions )
    {
        $out = array();
        foreach ( array( 'view', 'edit', 'copy', 'compare', 'remove' ) as $name )
            $out[$name] = $actions[$name]['allowed'] ? 'yes' : $actions[$name]['reason'];
        return $out;
    }

    /** HL-06 */
    public function testActions()
    {
        $this->assertSame( array( 'view' => 'yes', 'edit' => 'not_draft', 'copy' => 'yes', 'compare' => 'yes', 'remove' => 'yes' ), $this->allowed( $this->actions( 1 ) ) );
        $this->assertSame( array( 'view' => 'yes', 'edit' => 'not_draft', 'copy' => 'yes', 'compare' => 'yes', 'remove' => 'published' ), $this->allowed( $this->actions( 2 ) ) );
        $this->assertSame( array( 'view' => 'yes', 'edit' => 'yes', 'copy' => 'yes', 'compare' => 'yes', 'remove' => 'yes' ), $this->allowed( $this->actions( 4 ) ) );
        $this->assertSame( array( 'view' => 'yes', 'edit' => 'not_own', 'copy' => 'untouched', 'compare' => 'not_readable', 'remove' => 'yes' ), $this->allowed( $this->actions( 5 ) ) );
        $this->assertSame( array( 'view' => 'yes', 'edit' => 'not_draft', 'copy' => 'not_readable', 'compare' => 'not_readable', 'remove' => 'workflow' ), $this->allowed( $this->actions( 6 ) ) );
        // who may not edit the object
        $this->assertSame( array( 'view' => 'yes', 'edit' => 'no_edit', 'copy' => 'no_edit', 'compare' => 'yes', 'remove' => 'no_remove' ),
                           $this->allowed( $this->actions( 4, array( 'can_edit' => false ) ) ) );
        // without versionread and versionremove
        $this->assertSame( array( 'view' => 'versionread', 'edit' => 'not_draft', 'copy' => 'yes', 'compare' => 'yes', 'remove' => 'no_remove' ),
                           $this->allowed( $this->actions( 1, array(), array( 'can_versionread' => false, 'can_remove' => false ) ) ) );
        // the copy starts only in a translation the user may edit
        $copy = $this->actions( 3, array( 'edit_languages' => array( 'eng-GB' ) ), array( 'languages' => array( 'ger-DE' => 'German', 'eng-GB' => 'English' ) ) );
        $this->assertSame( array( 'eng-GB' => 'English' ), $copy['copy_languages'] );
        $this->assertSame( 'no_language', $this->actions( 3, array( 'edit_languages' => array( 'eng-GB' ) ) )['copy']['reason'] );
    }

    /** HL-07 */
    public function testComparedWith()
    {
        $this->assertSame( 2, History::comparedWith( 1, 2, array( 1, 2, 3 ) ), 'the current version' );
        $this->assertSame( 3, History::comparedWith( 2, 2, array( 1, 2, 3 ) ), 'the current one itself: the newest other' );
        $this->assertSame( 3, History::comparedWith( 1, 4, array( 1, 2, 3 ) ), 'a current version not seen: the newest other' );
        $this->assertSame( 1, History::comparedWith( 1, 1, array( 1 ) ), 'no other' );
    }

    /** HL-08 */
    public function testTheViewStaysThin()
    {
        $code = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/views/content/history.php' );
        $this->assertSame( 1, preg_match( '/public function run\( array \$scope \)\n    \{\n(.*?)\n    \}\n/s', $code, $m ) );
        $this->assertLessThanOrEqual( 80, substr_count( $m[1], "\n" ) + 1, 'run() of content/history' );
        foreach ( array( 'compare', 'versionLimitWarning', 'removeVersions', 'editAction', 'copyAction' ) as $step )
            $this->assertStringContainsString( "protected function $step()", $code );
        foreach ( array( 'BackButton', 'RedirectURI', 'DiffButton', 'FromVersion', 'ToVersion', 'Language', 'ExtraOptions', 'CompareButton',
                         'RemoveButton', 'DeleteIDArray', 'DoNotEditAfterCopy' ) as $name )
            $this->assertStringContainsString( "'$name'", $code, "the view reads $name" );
        foreach ( array( 'history_rows', 'history_overview', 'content_versions', 'can_read', 'refused', 'newerDraftVersionList', 'selectOldVersion' ) as $name )
            $this->assertStringContainsString( "'$name'", $code, "the view sets $name" );
    }
}
