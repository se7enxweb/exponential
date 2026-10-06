<?php
/**
 * File containing the StateViewHelpersTest class.
 *
 * What the object state pages say without asking the database: the policy limitation a
 * group is named by, which roles use a group and how, what removing states does to their
 * objects, and the figures at the top of the groups page.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\State\Groups;

class StateViewHelpersTest extends ezpTestCase
{
    public function testLimitationKey()
    {
        $this->assertSame( 'StateGroup_review', Groups::limitationKey( 'review' ) );
        $this->assertSame( 'StateGroup_my_workflow_2', Groups::limitationKey( 'my_workflow_2' ) );
        // system groups ("ez...") are not offered to policies
        $this->assertSame( '', Groups::limitationKey( 'ez_lock' ) );
        $this->assertSame( '', Groups::limitationKey( 'ezanything' ) );
        $this->assertSame( '', Groups::limitationKey( '' ) );
        $this->assertSame( '', Groups::limitationKey( null ) );
    }

    private function row( $roleID, $roleName, $module, $function, $identifier, $value )
    {
        return array( 'role_id' => (string)$roleID, 'role_name' => $roleName, 'module_name' => $module,
                      'function_name' => $function, 'identifier' => $identifier, 'value' => (string)$value );
    }

    public function testReferencesByGroup()
    {
        $groupOfState = array( 10 => 'review', 11 => 'review', 12 => 'review', 20 => 'audience' );
        $rows = array(
            $this->row( 3, 'Editor', 'content', 'read', 'StateGroup_review', 12 ),
            $this->row( 3, 'Editor', 'content', 'read', 'StateGroup_review', 11 ),
            $this->row( 3, 'Editor', 'content', 'edit', 'StateGroup_review', 10 ),
            $this->row( 5, 'anonymous', 'content', 'read', 'StateGroup_review', 12 ),
            $this->row( 4, 'Reviewer', 'state', 'assign', 'NewState', 11 ),
            $this->row( 4, 'Reviewer', 'state', 'assign', 'NewState', 12 ),
            $this->row( 4, 'Reviewer', 'state', 'assign', 'NewState', 20 ),
            // limitations that are not about states, and a NewState value of a removed state
            $this->row( 3, 'Editor', 'content', 'read', 'Section', 1 ),
            $this->row( 4, 'Reviewer', 'state', 'assign', 'NewState', 999 ),
        );
        $refs = Groups::referencesByGroup( $rows, $groupOfState );

        $this->assertSame( array( 'review', 'audience' ), array_keys( $refs ) );
        // by role name, case-insensitive
        $this->assertSame( array( 'anonymous', 'Editor', 'Reviewer' ), array_column( $refs['review'], 'role_name' ) );

        $editor = $refs['review'][1];
        $this->assertSame( 3, $editor['role_id'] );
        $this->assertSame( array( 'content/edit', 'content/read' ), $editor['policies'] );
        $this->assertTrue( $editor['condition'] );
        $this->assertFalse( $editor['new_state'] );
        $this->assertSame( array( 12, 11, 10 ), $editor['state_ids'] );

        $reviewer = $refs['review'][2];
        $this->assertSame( array( 'state/assign' ), $reviewer['policies'] );
        $this->assertFalse( $reviewer['condition'] );
        $this->assertTrue( $reviewer['new_state'] );
        $this->assertSame( array( 11, 12 ), $reviewer['state_ids'] );

        $this->assertCount( 1, $refs['audience'] );
        $this->assertSame( array( 20 ), $refs['audience'][0]['state_ids'] );

        $this->assertSame( array(), Groups::referencesByGroup( array(), $groupOfState ) );
    }

    public function testRolesNamingState()
    {
        $refs = Groups::referencesByGroup( array(
            $this->row( 3, 'Editor', 'content', 'read', 'StateGroup_review', 12 ),
            $this->row( 4, 'Reviewer', 'state', 'assign', 'NewState', 11 ),
        ), array( 11 => 'review', 12 => 'review' ) );
        $this->assertSame( array( 'Editor' ), array_column( Groups::rolesNamingState( $refs['review'], 12 ), 'role_name' ) );
        $this->assertSame( array( 'Reviewer' ), array_column( Groups::rolesNamingState( $refs['review'], '11' ), 'role_name' ) );
        $this->assertSame( array(), Groups::rolesNamingState( $refs['review'], 10 ) );
    }

    private function states()
    {
        return array(
            array( 'id' => 10, 'identifier' => 'draft', 'name' => 'Draft', 'object_count' => 40 ),
            array( 'id' => 11, 'identifier' => 'in_review', 'name' => 'In review', 'object_count' => 5 ),
            array( 'id' => 12, 'identifier' => 'approved', 'name' => 'Approved', 'object_count' => 300 ),
        );
    }

    public function testRemovalMovesObjectsToTheFirstStateThatStays()
    {
        $r = Groups::removalConsequence( $this->states(), array( '11', 12 ) );
        $this->assertSame( array( 'in_review', 'approved' ), array_column( $r['removed'], 'identifier' ) );
        $this->assertSame( 305, $r['objects'] );
        $this->assertSame( 'draft', $r['target']['identifier'] );
        $this->assertSame( 1, $r['remaining'] );
        $this->assertFalse( $r['all'] );

        // removing the default: the next state becomes the target (and the new default)
        $r = Groups::removalConsequence( $this->states(), array( 10 ) );
        $this->assertSame( 'in_review', $r['target']['identifier'] );
        $this->assertSame( 40, $r['objects'] );
    }

    public function testRemovalOfEveryState()
    {
        $r = Groups::removalConsequence( $this->states(), array( 10, 11, 12 ) );
        $this->assertTrue( $r['all'] );
        $this->assertFalse( $r['target'] );
        $this->assertSame( 0, $r['remaining'] );
        $this->assertSame( 345, $r['objects'] );
    }

    public function testRemovalOfNothingKnown()
    {
        $r = Groups::removalConsequence( $this->states(), array( 99 ) );
        $this->assertSame( array(), $r['removed'] );
        $this->assertSame( 0, $r['objects'] );
        $this->assertFalse( $r['all'] );
        $this->assertSame( 3, $r['remaining'] );
    }

    public function testOverviewFigures()
    {
        $groups = array(
            array( 'internal' => true, 'states' => array( 1, 2 ), 'roles' => array() ),
            array( 'internal' => false, 'states' => array( 1, 2, 3 ), 'roles' => array( array( 'role_id' => 3 ), array( 'role_id' => 4 ) ) ),
            array( 'internal' => false, 'states' => array(), 'roles' => array( array( 'role_id' => 3 ) ) ),
        );
        $this->assertSame( array( 'groups' => 7, 'custom' => 2, 'system' => 1, 'states' => 5, 'roles' => 2 ),
                           Groups::overviewFigures( $groups, 7 ) );
        $this->assertSame( array( 'groups' => 0, 'custom' => 0, 'system' => 0, 'states' => 0, 'roles' => 0 ),
                           Groups::overviewFigures( array(), 0 ) );
    }
}
