<?php
/**
 * The policies of the debug bar's User tab (kernel/classes/debugbar/expdebugbarpolicies.php), loaded on demand by
 * expdebugbar::policies:
 *   - UP-01 the policy rows and their limitation values as one list, sorted by module, function and role
 *   - UP-02 the selection: a role, a search in module/function, role name, limitation, values and their names; a page
 *   - UP-03 a row as the tab shows it: readable values with the id, the rest as they are
 *   - UP-04 the tab offers the section only with policies allowed, with the address of role/view for the links, and its
 *           words are in the script's strings
 *
 * No database: the rows come as the queries return them, the names from a stand-in resolver.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarPoliciesTest extends PHPUnit\Framework\TestCase
{
    private function resolver()
    {
        return new class extends expDebugBarPolicyNames {
            public function cheapName( $identifier, $value )
            {
                $names = array( 'Class' => array( '2' => 'Article', '16' => 'Folder' ), 'Section' => array( '1' => 'Standard', '3' => 'Media' ) );
                return isset( $names[$identifier][$value] ) ? $names[$identifier][$value] : '';
            }
            public function name( $identifier, $value )
            {
                return $identifier === 'Subtree' && $value === '/1/2/42/' ? 'News' : $this->cheapName( $identifier, $value );
            }
        };
    }

    private function policies()
    {
        return expDebugBarPolicies::combine(
            array(
                array( 'id' => 30, 'role_id' => 7, 'module_name' => 'content', 'function_name' => 'read' ),
                array( 'id' => 31, 'role_id' => 7, 'module_name' => 'content', 'function_name' => 'edit' ),
                array( 'id' => 10, 'role_id' => 2, 'module_name' => '*', 'function_name' => '*' ),
                array( 'id' => 20, 'role_id' => 1, 'module_name' => 'content', 'function_name' => 'read' ),
                array( 'id' => 21, 'role_id' => 1, 'module_name' => 'user', 'function_name' => 'login' ),
            ),
            array(
                array( 'policy_id' => 30, 'identifier' => 'Subtree', 'value' => '/1/2/42/' ),
                array( 'policy_id' => 20, 'identifier' => 'Class', 'value' => '2' ),
                array( 'policy_id' => 20, 'identifier' => 'Class', 'value' => '16' ),
                array( 'policy_id' => 20, 'identifier' => 'Section', 'value' => '1' ),
                array( 'policy_id' => 21, 'identifier' => 'SiteAccess', 'value' => '1766001124' ),
            ) );
    }

    private $roleNames = array( 1 => 'Anonymous', 2 => 'Administrator', 7 => 'Editor' );

    /** UP-01 */
    public function testTheRowsAsOneSortedList()
    {
        $p = $this->policies();
        $order = array_map( function ( $x ) { return $x['module'] . '/' . $x['function'] . '#' . $x['role_id']; }, $p );
        $this->assertSame( array( '*/*#2', 'content/edit#7', 'content/read#1', 'content/read#7', 'user/login#1' ), $order );
        $this->assertSame( array( 'Class' => array( '2', '16' ), 'Section' => array( '1' ) ), $p[2]['limitations'] );
        $this->assertSame( array(), $p[0]['limitations'] );
    }

    /** UP-02 */
    public function testTheSelection()
    {
        $p = $this->policies();
        $r = $this->resolver();
        $this->assertCount( 5, expDebugBarPolicies::select( $p, $this->roleNames, null, '', $r ) );
        $this->assertCount( 2, expDebugBarPolicies::select( $p, $this->roleNames, 7, '', $r ), 'one role' );
        $this->assertCount( 0, expDebugBarPolicies::select( $p, $this->roleNames, 99, '', $r ), 'not a role of the user' );
        $this->assertCount( 2, expDebugBarPolicies::select( $p, $this->roleNames, null, 'content/read', $r ), 'module/function' );
        $this->assertCount( 2, expDebugBarPolicies::select( $p, $this->roleNames, null, 'EDITOR', $r ), 'role name, any case' );
        $this->assertCount( 1, expDebugBarPolicies::select( $p, $this->roleNames, null, 'article', $r ), 'a class by its name' );
        $this->assertCount( 1, expDebugBarPolicies::select( $p, $this->roleNames, null, 'siteaccess', $r ), 'a limitation by its identifier' );
        $this->assertCount( 1, expDebugBarPolicies::select( $p, $this->roleNames, null, '/1/2/42/', $r ), 'a value as stored' );
        $this->assertCount( 1, expDebugBarPolicies::select( $p, $this->roleNames, 1, 'login', $r ), 'role and search together' );
    }

    /** UP-03 */
    public function testARowAsTheTabShowsIt()
    {
        $p = $this->policies();
        $r = $this->resolver();
        $row = expDebugBarPolicies::row( $p[2], $this->roleNames, $r );
        $this->assertSame( array( 'module' => 'content', 'function' => 'read',
                                  'limitations' => array( array( 'identifier' => 'Class', 'values' => array( 'Article (2)', 'Folder (16)' ) ),
                                                          array( 'identifier' => 'Section', 'values' => array( 'Standard (1)' ) ) ),
                                  'role_id' => 1, 'role' => 'Anonymous' ), $row );
        $this->assertSame( array( 'News (/1/2/42/)' ), expDebugBarPolicies::row( $p[3], $this->roleNames, $r )['limitations'][0]['values'] );
        $this->assertSame( array( '1766001124' ), expDebugBarPolicies::row( $p[4], $this->roleNames, $r )['limitations'][0]['values'], 'no name: the value' );
        $this->assertSame( '', expDebugBarPolicies::row( $p[0], array(), $r )['role'], 'a role without a name' );
    }

    /** UP-04 */
    public function testTheSectionAndItsWords()
    {
        $u = array( 'id' => 14, 'login' => 'editor', 'logged_in' => true, 'siteaccess' => 'site', 'session' => true, 'node_id' => null,
                    'tweak' => '', 'per_user' => false, 'keys' => array(), 'removed' => array(), 'changed' => array(), 'profile' => 'abcd1234',
                    'view_keys' => array(), 'from_listeners' => array(), 'view_profile' => null, 'assignments' => array() );
        $this->assertStringNotContainsString( 'data-exp-debug-policies', expDebugBarReport::userHtml( $u ) );
        $html = expDebugBarReport::userHtml( $u, true );
        $this->assertMatchesRegularExpression( '#<details class="exp-debug-user-policies-section" data-exp-debug-policies data-role-url="[^"]*role/view/"><summary>Policies</summary>#', $html );
        foreach ( array( 'Load more', 'Module/function', 'All roles (%count policies)', '%shown of %matched policies', 'No policy matches.' ) as $word )
            $this->assertContains( $word, expDebugBarReport::scriptStrings() );
    }
}
