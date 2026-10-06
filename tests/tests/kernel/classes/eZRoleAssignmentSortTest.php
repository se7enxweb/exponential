<?php
/**
 * The parts of the role assignment paging of role/view that need no database: the sorting and filtering MongoDB does
 * in php (eZRole::sortAssignmentRows()), the name filter as it goes into the address and into SQL, the node of a
 * subtree limitation, and the address parts of the page (\Exponential\View\Kernel\Role\View).
 *
 *  RS-01 - Sorted with the gone ones first, then by name without regard to case, then by assignment id
 *  RS-02 - A filter matches part of a name without regard to case, never a gone one, and % and _ only as themselves
 *  RS-03 - The filter loses '/', '(', ')' and control characters, keeps one space between words and 100 characters
 *  RS-04 - The LIKE pattern escapes %, _ and ! with !
 *  RS-05 - The node of a subtree limitation is the last element of its path; other limitations have none
 *  RS-06 - A row is the same on every engine: empty limitations are '', a gone object has no object_id
 *  RS-07 - The address parts of the two lists hold only what differs from the default, the filter encoded
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Role\View;

class eZRoleAssignmentSortTest extends PHPUnit\Framework\TestCase
{
    private function row( $id, $name, $userID = null )
    {
        return eZRole::assignmentRow( $userID === null ? 100 + $id : $userID, $id, '', '', $name );
    }

    private function ids( array $rows )
    {
        return array_map( function ( $row ) { return $row['id']; }, $rows );
    }

    /** RS-01 */
    public function testSortedGoneFirstThenByNameWithoutCaseThenById()
    {
        $rows = array( $this->row( 1, 'beta' ), $this->row( 2, 'Alpha' ), $this->row( 3, null ), $this->row( 4, 'alpha' ),
                       $this->row( 5, 'Beta' ), $this->row( 6, '' ), $this->row( 7, null ), $this->row( 8, 'ALPHA' ) );
        $this->assertSame( array( 3, 7, 6, 2, 4, 8, 1, 5 ), $this->ids( eZRole::sortAssignmentRows( $rows ) ) );
        // Lowered by mb_strtolower, so an umlaut sorts with its own lower case
        $rows = array( $this->row( 1, 'Ölbaum' ), $this->row( 2, 'öde' ), $this->row( 3, 'Zeder' ) );
        $this->assertSame( array( 3, 2, 1 ), $this->ids( eZRole::sortAssignmentRows( $rows ) ) );
        $this->assertSame( array(), eZRole::sortAssignmentRows( array() ) );
    }

    /** RS-02 */
    public function testTheFilterMatchesPartOfANameWithoutCase()
    {
        $rows = array( $this->row( 1, 'Anna Admin' ), $this->row( 2, 'Editors' ), $this->row( 3, null ),
                       $this->row( 4, 'members 100%' ), $this->row( 5, 'member_x' ), $this->row( 6, 'Ölmühle' ) );
        $this->assertSame( array( 1 ), $this->ids( eZRole::sortAssignmentRows( $rows, 'ADMIN' ) ) );
        // member_x before members: '_' sorts before 's' by its byte, as in SQLite
        $this->assertSame( array( 2, 5, 4, 6 ), $this->ids( eZRole::sortAssignmentRows( $rows, 'e' ) ) );
        $this->assertSame( array( 4 ), $this->ids( eZRole::sortAssignmentRows( $rows, '%' ) ) );
        $this->assertSame( array( 5 ), $this->ids( eZRole::sortAssignmentRows( $rows, '_' ) ) );
        $this->assertSame( array( 6 ), $this->ids( eZRole::sortAssignmentRows( $rows, 'ÖLM' ) ) );
        $this->assertSame( array(), eZRole::sortAssignmentRows( $rows, 'nobody' ) );
        // No filter, or one that normalises to nothing, keeps all of them
        $this->assertCount( 6, eZRole::sortAssignmentRows( $rows, ' / ' ) );
    }

    /** RS-03 */
    public function testTheFilterIsNormalisedForTheAddress()
    {
        $this->assertSame( '', eZRole::normaliseAssignmentFilter( '' ) );
        $this->assertSame( '', eZRole::normaliseAssignmentFilter( null ) );
        $this->assertSame( '', eZRole::normaliseAssignmentFilter( array( 'x' ) ) );
        $this->assertSame( 'a b', eZRole::normaliseAssignmentFilter( "  a/(b)\t" ) );
        $this->assertSame( 'Group x', eZRole::normaliseAssignmentFilter( "Group\n\nx" ) );
        $this->assertSame( "it's 100%", eZRole::normaliseAssignmentFilter( "it's 100%" ) );
        $this->assertSame( 100, mb_strlen( eZRole::normaliseAssignmentFilter( str_repeat( 'ä', 150 ) ) ) );
        $this->assertSame( '42', eZRole::normaliseAssignmentFilter( 42 ) );
    }

    /** RS-04 */
    public function testTheLikePatternEscapesItsWildcards()
    {
        $this->assertSame( '%anna%', eZRole::assignmentFilterLikePattern( 'anna' ) );
        $this->assertSame( '%100!%%', eZRole::assignmentFilterLikePattern( '100%' ) );
        $this->assertSame( '%a!_b%', eZRole::assignmentFilterLikePattern( 'a_b' ) );
        $this->assertSame( '%hey!!%', eZRole::assignmentFilterLikePattern( 'hey!' ) );
        $this->assertSame( "%o'brien%", eZRole::assignmentFilterLikePattern( "o'brien" ) );
    }

    /** RS-05 */
    public function testTheNodeOfASubtreeLimitation()
    {
        $this->assertSame( 42, eZRole::subtreeLimitationNodeID( 'Subtree', '/1/2/42/' ) );
        $this->assertSame( 42, eZRole::subtreeLimitationNodeID( 'subtree', '/1/2/42' ) );
        $this->assertSame( 0, eZRole::subtreeLimitationNodeID( 'Section', '3' ) );
        $this->assertSame( 0, eZRole::subtreeLimitationNodeID( '', '' ) );
        $this->assertSame( 0, eZRole::subtreeLimitationNodeID( 'Subtree', '' ) );
    }

    /** RS-06 */
    public function testARowIsTheSameOnEveryEngine()
    {
        $this->assertSame( array( 'user_id' => 14, 'id' => 7, 'limit_identifier' => '', 'limit_value' => '',
                                  'name' => 'Admin', 'object_id' => 14 ),
                           eZRole::assignmentRow( '14', '7', null, null, 'Admin' ) );
        $gone = eZRole::assignmentRow( 99, 8, 'Section', 3, null );
        $this->assertNull( $gone['name'] );
        $this->assertNull( $gone['object_id'] );
        $this->assertSame( '3', $gone['limit_value'] );
        $this->assertSame( array(), eZRole::assignmentPageFromRows( array() ) );
    }

    /** RS-07 */
    public function testTheAddressPartsHoldOnlyWhatDiffersFromTheDefault()
    {
        $this->assertSame( '', View::policyUriSuffix( array( 'policy_offset' => 0, 'policy_sort' => 'id', 'policy_dir' => 'asc' ) ) );
        $this->assertSame( '/(policy_offset)/25/(policy_sort)/module/(policy_dir)/desc',
                           View::policyUriSuffix( array( 'policy_offset' => '25', 'policy_sort' => 'module', 'policy_dir' => 'DESC' ) ) );
        $this->assertSame( '', View::policyUriSuffix( array( 'policy_offset' => -5, 'policy_sort' => 'nonsense' ) ) );
        $this->assertSame( '', View::assignmentUriSuffix( 0, '' ) );
        $this->assertSame( '/(assignment_offset)/50', View::assignmentUriSuffix( '50', '' ) );
        $this->assertSame( '/(assignment_offset)/50/(assignment_filter)/M%C3%BCller%20Gruppe',
                           View::assignmentUriSuffix( 50, 'Müller Gruppe' ) );
        $this->assertSame( '/(assignment_filter)/a%20b', View::assignmentUriSuffix( -1, 'a/b' ) );
    }
}
