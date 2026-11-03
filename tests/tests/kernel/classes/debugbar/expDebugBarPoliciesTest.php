<?php
/**
 * The policies of the debug bar's User tab (kernel/classes/debugbar/expdebugbarpolicies.php), loaded on demand by
 * expdebugbar::policies:
 *   - UP-01 the policy rows and their limitation values as one list, sorted by module, function and role
 *   - UP-02 the selection: a role, a search in module/function, role name, limitation, values and their names; a page
 *   - UP-03 a row as the tab shows it: readable values with the id, the rest as they are
 *   - UP-04 the tab offers the section only with policies allowed, with the address of role/view for the links, and its
 *           words are in the script's strings
 *   - UP-06 a search "module/function" also finds the policies that grant it with a wildcard, in any case
 *   - UP-07 the queries: published policies, limitations and values of hundreds of roles in chunks of QUERY_CHUNK ids,
 *           none without roles
 *   - UP-08 hundreds of roles: the selection and its pages
 *   (UP-05, UP-09 and UP-10 are in expDebugBarUserPoliciesTest: who sees the tab, the path arguments, the script.)
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
        $this->assertCount( 3, expDebugBarPolicies::select( $p, $this->roleNames, null, 'content/read', $r ), 'module/function, and the policy of every module (UP-06)' );
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
        $this->assertMatchesRegularExpression( '#<details class="exp-debug-user-policies-section" data-exp-debug-policies data-role-url="[^"]*role/view/?"><summary>Policies</summary>#', $html );
        foreach ( array( 'Load more', 'Module/function', 'All roles (%count policies)', '%shown of %matched policies', 'No policy matches.' ) as $word )
            $this->assertContains( $word, expDebugBarReport::scriptStrings() );
    }

    /** UP-06 */
    public function testASearchForAModuleAndFunctionFindsTheWildcards()
    {
        $p = expDebugBarPolicies::combine( array(
            array( 'id' => 1, 'role_id' => 1, 'module_name' => 'content', 'function_name' => 'read' ),
            array( 'id' => 2, 'role_id' => 1, 'module_name' => 'content', 'function_name' => '*' ),
            array( 'id' => 3, 'role_id' => 1, 'module_name' => '*', 'function_name' => '*' ),
            array( 'id' => 4, 'role_id' => 1, 'module_name' => 'content', 'function_name' => 'edit' ),
            array( 'id' => 5, 'role_id' => 1, 'module_name' => 'user', 'function_name' => 'login' ),
        ), array() );
        $r = $this->resolver();
        $ids = function ( $query ) use ( $p, $r ) {
            $ids = array_column( expDebugBarPolicies::select( $p, array( 1 => 'Editor' ), null, $query, $r ), 'id' );
            sort( $ids );
            return $ids;
        };
        $this->assertSame( array( 1, 2, 3 ), $ids( 'content/read' ), 'its own, every function of content, every module' );
        $this->assertSame( array( 1, 2, 3 ), $ids( 'Content/Read' ), 'any case' );
        $this->assertSame( array( 1, 2, 3, 4 ), $ids( 'content/*' ), 'every function of content' );
        $this->assertSame( array( 3, 5 ), $ids( 'user/login' ) );
        $this->assertSame( array( 1, 2, 3 ), $ids( '*/read' ), 'a function in any module, and what grants every function' );
        $this->assertSame( array( 1, 2, 4 ), $ids( 'content' ), 'a word: as found in the text' );
    }

    /** UP-07 */
    public function testTheQueriesInChunks()
    {
        $asked = array();
        $fetch = function ( $definition, array $conditions ) use ( &$asked ) {
            $table = $definition['name'];
            $column = key( $conditions );
            $ids = $conditions[$column][0];
            $asked[] = array( $table, count( $ids ), isset( $conditions['original_id'] ) ? $conditions['original_id'] : null );
            $rows = array();
            foreach ( $ids as $id )
            {
                if ( $table === 'ezpolicy' )
                    $rows[] = array( 'id' => $id * 10, 'role_id' => $id, 'module_name' => 'content', 'function_name' => 'read' );
                else if ( $table === 'ezpolicy_limitation' )
                    $rows[] = array( 'id' => $id + 1, 'policy_id' => $id, 'identifier' => 'Section' );
                else
                    $rows[] = array( 'id' => $id, 'limitation_id' => $id, 'value' => '1' );
            }
            return $rows;
        };
        $p = expDebugBarPolicies::fetchPoliciesWith( range( 1, 1200 ), $fetch );
        $this->assertCount( 1200, $p );
        $this->assertSame( array( 'Section' => array( '1' ) ), $p[0]['limitations'], 'the values reach their policy' );
        $this->assertSame( array( array( 'ezpolicy', 500, 0 ), array( 'ezpolicy', 500, 0 ), array( 'ezpolicy', 200, 0 ) ),
                           array_slice( $asked, 0, 3 ), 'published policies, QUERY_CHUNK role ids a query' );
        $this->assertCount( 9, $asked, 'three tables, three chunks each' );

        $asked = array();
        $this->assertSame( array(), expDebugBarPolicies::fetchPoliciesWith( array(), $fetch ) );
        $this->assertSame( array(), $asked, 'no roles: no query' );
    }

    /** UP-08 */
    public function testHundredsOfRoles()
    {
        $rows = array();
        $names = array();
        for ( $role = 1; $role <= 300; $role++ )
        {
            $names[$role] = 'Role ' . $role;
            $rows[] = array( 'id' => $role * 10 + 1, 'role_id' => $role, 'module_name' => 'content', 'function_name' => 'read' );
            $rows[] = array( 'id' => $role * 10 + 2, 'role_id' => $role, 'module_name' => 'content', 'function_name' => 'edit' );
        }
        $p = expDebugBarPolicies::combine( $rows, array() );
        $r = $this->resolver();
        $all = expDebugBarPolicies::select( $p, $names, null, '', $r );
        $this->assertCount( 600, $all );
        $this->assertCount( expDebugBarPolicies::PAGE, array_slice( $all, 0, expDebugBarPolicies::PAGE ), 'a page' );
        $this->assertCount( 200, array_slice( $all, 400, expDebugBarPolicies::PAGE ), 'the last page' );
        $this->assertCount( 2, expDebugBarPolicies::select( $p, $names, 250, '', $r ), 'one role of 300' );
        $this->assertCount( 300, expDebugBarPolicies::select( $p, $names, null, 'content/read', $r ) );
        $this->assertCount( 2, expDebugBarPolicies::select( $p, $names, null, 'role 300', $r ), 'a role by its name' );
    }
}
