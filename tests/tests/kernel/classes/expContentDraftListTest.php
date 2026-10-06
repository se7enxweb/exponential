<?php
/**
 * The drafts page (content/draft) and the pending page (content/pendinglist) without the database:
 * expContentDraftList, expContentPendingList and the shape of the two views.
 *
 *  DL-01 - The filters of the drafts page: known values are taken, anything else is left out; the search is cleaned
 *  DL-02 - The address part and the filter links keep the other filters
 *  DL-03 - Age, search, translation, class and the orders select the drafts
 *  DL-04 - The figures and the choices
 *  DL-05 - The drafts older than a number of days; only the ages offered are taken
 *  DL-06 - Only the user's own draft or untouched draft is removable, never the anonymous user's, never another status
 *  DL-07 - The pending filters, address part and links
 *  DL-08 - A version waiting for the user's approval is listed only when the user may read it; the figures count only
 *          what is listed; scope, class and order select
 *  DL-09 - The views stay thin, keep the POST names of the pages, and remove only through removable()
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentDraftListTest extends PHPUnit\Framework\TestCase
{
    const NOW = 1790000000;
    const DAY = 86400;

    private function drafts()
    {
        $rows = array();
        foreach ( array( array( 1, 'Spring news', 'article', 'Article', 'eng-GB', 2, false, 'News' ),
                         array( 2, 'About us', 'folder', 'Folder', 'eng-GB', 40, true, 'Company' ),
                         array( 3, 'Über uns', 'folder', 'Folder', 'ger-DE', 100, false, 'Firma' ),
                         array( 4, 'Old note', 'article', 'Article', 'ger-DE', 400, true, '' ) ) as $d )
        {
            $rows[] = expContentDraftList::row( array( 'id' => 100 + $d[0], 'version' => $d[0], 'object_id' => 50 + $d[0], 'name' => $d[1],
                                                       'class_identifier' => $d[2], 'class_name' => $d[3], 'language' => $d[4],
                                                       'language_name' => $d[4] === 'eng-GB' ? 'English' : 'German',
                                                       'created' => self::NOW - $d[5] * self::DAY - 60, 'modified' => self::NOW - $d[5] * self::DAY,
                                                       'status' => 0, 'is_new' => $d[6], 'node_id' => $d[6] ? 0 : 200 + $d[0], 'location' => $d[7] ), self::NOW );
        }
        return $rows;
    }

    private function ids( array $rows )
    {
        return array_map( function ( $r ) { return $r['id']; }, $rows );
    }

    /** DL-01 */
    public function testDraftFilters()
    {
        $this->assertSame( array( 'language' => '', 'class' => '', 'age' => 0, 'sort' => 'modified', 'search' => '' ), expContentDraftList::filters( array() ) );
        $this->assertSame( array( 'language' => 'ger-DE', 'class' => 'folder', 'age' => 90, 'sort' => 'name', 'search' => 'uns' ),
                           expContentDraftList::filters( array( 'language' => 'ger-DE', 'class' => 'folder', 'age' => '90', 'sort' => 'name' ), '  uns ' ) );
        $this->assertSame( array( 'language' => '', 'class' => '', 'age' => 0, 'sort' => 'modified', 'search' => 'a b' ),
                           expContentDraftList::filters( array( 'language' => 'x', 'class' => 'a/b', 'age' => '12', 'sort' => 'random' ), "a\r\nb" ) );
        $this->assertSame( '', expContentDraftList::filters( array(), array( 'x' ) )['search'] );
        $this->assertSame( 100, mb_strlen( expContentDraftList::searchText( str_repeat( 'ä', 300 ) ) ) );
    }

    /** DL-02 */
    public function testDraftSuffixAndLinks()
    {
        $filters = expContentDraftList::filters( array( 'class' => 'folder', 'age' => '30', 'sort' => 'oldest' ), 'x' );
        $this->assertSame( '/(class)/folder/(age)/30/(sort)/oldest', expContentDraftList::suffix( $filters ) );
        $links = expContentDraftList::links( $filters, expContentDraftList::choices( $this->drafts() ) );
        $this->assertSame( '/(age)/30/(sort)/oldest', $links['class']['all'] );
        $this->assertSame( '/(language)/ger-DE/(class)/folder/(age)/30/(sort)/oldest', $links['language']['ger-DE'] );
        $this->assertSame( '/(class)/folder/(age)/7/(sort)/oldest', $links['age'][7] );
        $this->assertSame( '/(class)/folder/(age)/30', $links['sort']['modified'] );
    }

    /** DL-03 */
    public function testDraftSelect()
    {
        $rows = $this->drafts();
        $this->assertSame( array( 2, 40, 100, 400 ), array_map( function ( $r ) { return $r['age_days']; }, $rows ) );
        $this->assertSame( array( 101, 102, 103, 104 ), $this->ids( expContentDraftList::select( $rows, expContentDraftList::filters( array() ) ) ) );
        $this->assertSame( array( 104, 103, 102, 101 ), $this->ids( expContentDraftList::select( $rows, array( 'sort' => 'oldest' ) ) ) );
        $this->assertSame( array( 102, 104, 101, 103 ), $this->ids( expContentDraftList::select( $rows, array( 'sort' => 'name' ) ) ) );
        $this->assertSame( array( 104, 101, 102, 103 ), $this->ids( expContentDraftList::select( $rows, array( 'sort' => 'class' ) ) ) );
        $this->assertSame( array( 103, 104 ), $this->ids( expContentDraftList::select( $rows, array( 'age' => 90 ) ) ) );
        $this->assertSame( array( 103, 104 ), $this->ids( expContentDraftList::select( $rows, array( 'language' => 'ger-DE' ) ) ) );
        $this->assertSame( array( 102, 103 ), $this->ids( expContentDraftList::select( $rows, array( 'class' => 'folder' ) ) ) );
        $this->assertSame( array( 103 ), $this->ids( expContentDraftList::select( $rows, array( 'search' => 'ÜBER' ) ) ), 'case does not matter' );
        $this->assertSame( array( 102 ), $this->ids( expContentDraftList::select( $rows, array( 'search' => 'company' ) ) ), 'the location is searched' );
    }

    /** DL-04 */
    public function testDraftOverviewAndChoices()
    {
        $overview = expContentDraftList::overview( $this->drafts(), 30 );
        $this->assertSame( 4, $overview['total'] );
        $this->assertSame( 2, $overview['new_objects'] );
        $this->assertSame( 3, $overview['old'] );
        $this->assertSame( array( 'eng-GB' => 2, 'ger-DE' => 2 ), $overview['languages'] );
        $this->assertSame( 2, $overview['classes']['folder'] );
        $choices = expContentDraftList::choices( $this->drafts() );
        $this->assertSame( array( 'article' => 'Article', 'folder' => 'Folder' ), $choices['classes'] );
        $this->assertSame( array( 'eng-GB' => 'English', 'ger-DE' => 'German' ), $choices['languages'] );
    }

    /** DL-05 */
    public function testOlderThan()
    {
        $rows = $this->drafts();
        $this->assertSame( array( 102, 103, 104 ), expContentDraftList::olderThan( $rows, 30 ) );
        $this->assertSame( array( 104 ), expContentDraftList::olderThan( $rows, 365 ) );
        $this->assertSame( array(), expContentDraftList::olderThan( $rows, 0 ) );
        $this->assertSame( 90, expContentDraftList::age( '90' ) );
        $this->assertSame( 0, expContentDraftList::age( '1' ), 'only the ages offered' );
        $this->assertSame( 0, expContentDraftList::age( '30 or 1' ) );
        $this->assertSame( 0, expContentDraftList::ageDays( self::NOW + 5000, self::NOW ) );
    }

    /** DL-06 */
    public function testRemovable()
    {
        $this->assertTrue( expContentDraftList::removable( array( 'creator_id' => 14, 'status' => 0 ), 14, 10 ) );
        $this->assertTrue( expContentDraftList::removable( array( 'creator_id' => '14', 'status' => '5' ), 14, 10 ) );
        $this->assertFalse( expContentDraftList::removable( array( 'creator_id' => 15, 'status' => 0 ), 14, 10 ), 'someone else\'s draft' );
        foreach ( array( 1, 2, 3, 4, 6, 7 ) as $status )
            $this->assertFalse( expContentDraftList::removable( array( 'creator_id' => 14, 'status' => $status ), 14, 10 ), "status $status" );
        $this->assertFalse( expContentDraftList::removable( array( 'creator_id' => 10, 'status' => 0 ), 10, 10 ), 'the anonymous user' );
        $this->assertFalse( expContentDraftList::removable( array( 'creator_id' => 0, 'status' => 0 ), 0, 10 ) );
        $this->assertFalse( expContentDraftList::removable( array(), 14, 10 ) );
    }

    private function pending()
    {
        $rows = array();
        foreach ( array( array( 1, 'Mine waiting', true, false, false, 300, 7, 0 ),
                         array( 2, 'To approve readable', false, true, true, 200, 8, 0 ),
                         array( 3, 'To approve hidden', false, true, false, 100, 9, 0 ),
                         array( 4, 'Mine no approval', true, false, false, 50, 0, 0 ),
                         array( 5, 'Mine and approve', true, true, false, 400, 10, 3 ) ) as $p )
        {
            $rows[] = expContentPendingList::row( array( 'id' => 300 + $p[0], 'version' => 2, 'object_id' => 60 + $p[0], 'name' => $p[1],
                                                         'class_identifier' => $p[0] % 2 ? 'article' : 'folder', 'class_name' => $p[0] % 2 ? 'Article' : 'Folder',
                                                         'mine' => $p[2], 'approver' => $p[3], 'can_versionread' => $p[4], 'sent' => $p[5],
                                                         'approval_id' => $p[6], 'approval_state' => $p[7], 'approvers' => array( 'Ann' ),
                                                         'workflows' => array( 'Approve' ) ) );
        }
        return $rows;
    }

    /** DL-07 */
    public function testPendingFiltersAndLinks()
    {
        $this->assertSame( array( 'scope' => 'all', 'class' => '', 'sort' => 'newest' ), expContentPendingList::filters( array( 'scope' => 'x', 'sort' => 'y' ) ) );
        $filters = expContentPendingList::filters( array( 'scope' => 'approve', 'class' => 'folder', 'sort' => 'oldest' ) );
        $this->assertSame( '/(scope)/approve/(class)/folder/(sort)/oldest', expContentPendingList::suffix( $filters ) );
        $links = expContentPendingList::links( $filters, array( 'article' => 'Article' ) );
        $this->assertSame( '/(class)/folder/(sort)/oldest', $links['scope']['all'] );
        $this->assertSame( '/(scope)/approve/(class)/article/(sort)/oldest', $links['class']['article'] );
        $this->assertSame( '/(scope)/approve/(class)/folder', $links['sort']['newest'] );
    }

    /** DL-08 */
    public function testPendingVisibilityAndSelect()
    {
        $rows = $this->pending();
        $all = expContentPendingList::filters( array() );
        $this->assertSame( array( 305, 301, 302, 304 ), $this->ids( expContentPendingList::select( $rows, $all ) ), 'the hidden one is left out' );
        $this->assertSame( array( 304, 302, 301, 305 ), $this->ids( expContentPendingList::select( $rows, array_merge( $all, array( 'sort' => 'oldest' ) ) ) ) );
        $this->assertSame( array( 305, 302 ), $this->ids( expContentPendingList::select( $rows, array_merge( $all, array( 'scope' => 'approve' ) ) ) ) );
        $this->assertSame( array( 305, 301, 304 ), $this->ids( expContentPendingList::select( $rows, array_merge( $all, array( 'scope' => 'mine' ) ) ) ) );
        $this->assertSame( array( 305, 301 ), $this->ids( expContentPendingList::select( $rows, array_merge( $all, array( 'class' => 'article' ) ) ) ) );
        $overview = expContentPendingList::overview( $rows );
        $this->assertSame( 3, $overview['mine'] );
        $this->assertSame( 2, $overview['approve'], 'the one the user may not read is not counted' );
        $this->assertSame( 3, $overview['held_by_approval'] );
        $this->assertSame( 'deferred', $rows[4]['approval_state_name'] );
        $this->assertSame( '', $rows[3]['approval_state_name'], 'no approval' );
        $this->assertFalse( expContentPendingList::visible( $rows[2] ) );
    }

    /** DL-09 */
    public function testTheViewsStayThin()
    {
        $root = dirname( __DIR__, 4 ) . '/kernel/private/classes/views/content/';
        foreach ( array( 'draft.php' => 50, 'pendinglist.php' => 50 ) as $file => $limit )
        {
            $code = file_get_contents( $root . $file );
            $this->assertSame( 1, preg_match( '/public function run\( array \$scope \)\n    \{\n(.*?)\n    \}\n/s', $code, $m ), $file );
            $this->assertLessThanOrEqual( $limit, substr_count( $m[1], "\n" ) + 1, "run() of $file" );
        }
        $draft = file_get_contents( $root . 'draft.php' );
        foreach ( array( 'RemoveButton', 'DeleteIDArray', 'EmptyButton', 'RemoveDraftButton', 'RemoveOldButton', 'OldDraftDays' ) as $name )
            $this->assertStringContainsString( "'$name'", $draft );
        $this->assertSame( 1, substr_count( $draft, '->removeThis()' ), 'one place removes' );
        $this->assertSame( 1, preg_match( '/removable\(.*?\)\s*\)\s*\{\s*\$db->commit\(\);\s*\$refused\+\+;\s*continue;\s*\}\s*\$version->removeThis\(\);/s', $draft ),
                           'removeThis() only after removable() on the version as fetched' );
        $this->assertStringContainsString( "'view_parameters'", $draft );
        $this->assertStringContainsString( "'view_parameters'", file_get_contents( $root . 'pendinglist.php' ) );
    }
}
