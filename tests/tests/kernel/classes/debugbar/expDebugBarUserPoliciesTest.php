<?php
/**
 * The role policies of the debug bar's User tab (expDebugBarUser::policies(), policyPage(), mayView(),
 * expDebugBarServerFunctions::policies(), expDebugBarReport::userPoliciesHtml() and the script):
 *   - UP-01 who may see them: setup/setup, or an anonymous visitor with debug output; never a logged-in user without
 *           setup/setup, even with debug output
 *   - UP-02 a page: sorted by role, module, function; role names; limitations with their values; the module list for
 *           the filter
 *   - UP-03 the filter by module and function keeps the policies of every module or function (*)
 *   - UP-04 hundreds of roles: pages of POLICY_PAGE, next_offset, the last page, the limits held
 *   - UP-05 the tab draws a closed section with the number of roles and no policy: they are loaded when it is opened
 *   - UP-06 the filter in the path: "-" or nothing for any, anything but a name refused
 *   - UP-07 the script asks expdebugbar::policies with the filter in the path, and its words are in the strings
 *   - UP-08 no roles: no query, an empty page
 *
 * No database: the policies are given as policyRows() returns them.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarUserPoliciesTest extends PHPUnit\Framework\TestCase
{
    private function policy( $id, $roleID, $module, $function, array $limitations = array() )
    {
        return array( 'id' => $id, 'role_id' => $roleID, 'module' => $module, 'function' => $function, 'limitations' => $limitations );
    }

    private function sample()
    {
        return array(
            $this->policy( 10, 7, 'content', 'read', array( 'Subtree' => array( '/1/2/42/' ), 'Class' => array( '1', '16' ) ) ),
            $this->policy( 11, 7, 'content', 'edit', array( 'Owner' => array( '1' ) ) ),
            $this->policy( 3, 1, 'user', 'login', array( 'SiteAccess' => array( '1766001124' ) ) ),
            $this->policy( 4, 1, 'content', 'read' ),
            $this->policy( 20, 2, '*', '*' ),
            $this->policy( 21, 7, 'content', '*' ),
        );
    }

    /** UP-01 */
    public function testWhoMaySeeThePolicies()
    {
        $this->assertTrue( expDebugBarUser::mayView( true, true, false ), 'setup/setup, with or without debug output' );
        $this->assertTrue( expDebugBarUser::mayView( true, true, true ) );
        $this->assertTrue( expDebugBarUser::mayView( false, false, true ), 'an anonymous visitor with debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, false, false ), 'an anonymous visitor without debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, true, true ), 'a logged-in user without setup/setup, even with debug output' );
        $this->assertFalse( expDebugBarUser::mayView( false, true, false ) );
    }

    /** UP-02 */
    public function testAPageOfPolicies()
    {
        $page = expDebugBarUser::policyPage( $this->sample(), array( 1 => 'Anonymous', 2 => 'Administrator', 7 => 'Editor' ) );
        $this->assertSame( 3, $page['roles'] );
        $this->assertSame( 6, $page['total'] );
        $this->assertSame( 6, $page['matching'] );
        $this->assertNull( $page['next_offset'] );
        $this->assertNull( $page['module'] );
        $this->assertSame( array( 4, 3, 20, 21, 11, 10 ), array_column( $page['policies'], 'id' ), 'by role, module, function, id' );
        $this->assertSame( 'Editor', $page['policies'][5]['role'] );
        $this->assertSame( array( array( 'identifier' => 'Class', 'values' => array( '1', '16' ) ),
                                  array( 'identifier' => 'Subtree', 'values' => array( '/1/2/42/' ) ) ),
                           $page['policies'][5]['limitations'], 'limitations by identifier, values as stored' );
        $this->assertSame( array(), $page['policies'][0]['limitations'] );
        $this->assertSame( array( array( 'module' => '*', 'functions' => array( '*' ) ),
                                  array( 'module' => 'content', 'functions' => array( '*', 'edit', 'read' ) ),
                                  array( 'module' => 'user', 'functions' => array( 'login' ) ) ),
                           $page['modules'], 'every module and function of the roles, for the filter' );
        $this->assertSame( '', expDebugBarUser::policyPage( array( $this->policy( 1, 99, 'a', 'b' ) ), array() )['policies'][0]['role'], 'a role without a name' );
    }

    /** UP-03 */
    public function testTheFilter()
    {
        $roles = array( 1 => 'Anonymous', 2 => 'Administrator', 7 => 'Editor' );
        $content = expDebugBarUser::policyPage( $this->sample(), $roles, 'content' );
        $this->assertSame( array( 4, 20, 21, 11, 10 ), array_column( $content['policies'], 'id' ), 'content, and every module (*)' );
        $this->assertSame( 5, $content['matching'] );
        $this->assertSame( 6, $content['total'] );
        $this->assertSame( 'content', $content['module'] );

        $read = expDebugBarUser::policyPage( $this->sample(), $roles, 'content', 'read' );
        $this->assertSame( array( 4, 20, 21, 10 ), array_column( $read['policies'], 'id' ), 'content/read, content/* and */*' );

        $edit = expDebugBarUser::policyPage( $this->sample(), $roles, null, 'edit' );
        $this->assertSame( array( 20, 21, 11 ), array_column( $edit['policies'], 'id' ), 'a function in every module' );

        $none = expDebugBarUser::policyPage( array( $this->policy( 1, 1, 'user', 'login' ) ), $roles, 'content' );
        $this->assertSame( 0, $none['matching'] );
        $this->assertSame( array(), $none['policies'] );
        $this->assertSame( 1, $none['total'] );
        $this->assertCount( 1, $none['modules'], 'the filter keeps every module to choose from' );

        $this->assertSame( 6, expDebugBarUser::policyPage( $this->sample(), $roles, '', '' )['matching'], 'empty: no filter' );
    }

    /** UP-04 */
    public function testHundredsOfRoles()
    {
        $policies = array();
        $roles = array();
        for ( $r = 1; $r <= 300; $r++ )
        {
            $roles[$r] = 'Role ' . $r;
            $policies[] = $this->policy( $r * 10 + 1, $r, 'content', 'read', array( 'Subtree' => array( '/1/2/' . $r . '/' ) ) );
            $policies[] = $this->policy( $r * 10 + 2, $r, 'content', 'edit' );
        }
        $first = expDebugBarUser::policyPage( $policies, $roles );
        $this->assertSame( 300, $first['roles'] );
        $this->assertSame( 600, $first['matching'] );
        $this->assertCount( expDebugBarUser::POLICY_PAGE, $first['policies'] );
        $this->assertSame( expDebugBarUser::POLICY_PAGE, $first['next_offset'] );

        $last = expDebugBarUser::policyPage( $policies, $roles, null, null, 500 );
        $this->assertCount( 100, $last['policies'] );
        $this->assertNull( $last['next_offset'], 'the last page' );
        $this->assertSame( 251, $last['policies'][0]['role_id'] );

        $read = expDebugBarUser::policyPage( $policies, $roles, 'content', 'read', 200 );
        $this->assertSame( 300, $read['matching'] );
        $this->assertCount( 100, $read['policies'] );
        $this->assertNull( $read['next_offset'] );

        $this->assertSame( 500, expDebugBarUser::policyPage( $policies, $roles, null, null, 0, 100000 )['limit'], 'at most 500 a page' );
        $this->assertSame( 1, expDebugBarUser::policyPage( $policies, $roles, null, null, 0, 0 )['limit'] );
        $this->assertSame( 0, expDebugBarUser::policyPage( $policies, $roles, null, null, -5 )['offset'] );
        $this->assertSame( array(), expDebugBarUser::policyPage( $policies, $roles, null, null, 9999 )['policies'], 'past the end: nothing' );
    }

    /** UP-05 */
    public function testTheTabLoadsThePoliciesWhenOpened()
    {
        $html = expDebugBarReport::userPoliciesHtml( array(
            array( 'role_id' => 1, 'role' => 'Anonymous' ), array( 'role_id' => 7, 'role' => 'Editor' ), array( 'role_id' => 7, 'role' => 'Editor' ) ) );
        $this->assertStringContainsString( '<details class="exp-debug-user-policies" data-exp-debug-policies="2">', $html, 'closed, the roles counted once' );
        $this->assertStringContainsString( 'Show the policies of the roles (2)', $html );
        $this->assertStringNotContainsString( '<table', $html, 'no policy in the report' );
        $this->assertDoesNotMatchRegularExpression( '#<details[^>]*\sopen#', $html );
        $this->assertStringNotContainsString( 'style=', $html );

        $tab = expDebugBarReport::userHtml( array(
            'id' => 14, 'login' => 'editor', 'logged_in' => true, 'siteaccess' => 'site', 'session' => true, 'node_id' => 2, 'tweak' => '',
            'per_user' => false, 'keys' => array( 'userroles' => '7' ), 'profile' => 'abcd0123', 'removed' => array(), 'changed' => array(),
            'view_keys' => array(), 'from_listeners' => array(), 'view_profile' => null,
            'assignments' => array( array( 'role_id' => 7, 'role' => 'Editor', 'limit_identifier' => '', 'limit_value' => '', 'from_id' => 14, 'from_name' => '', 'direct' => true ) ) ) );
        $this->assertStringContainsString( 'exp-debug-user-policies', $tab );
        $this->assertGreaterThan( strpos( $tab, 'exp-debug-user-roles' ), strpos( $tab, 'exp-debug-user-policies' ), 'after the roles' );

        $noRoles = expDebugBarReport::userHtml( array(
            'id' => 10, 'login' => 'anonymous', 'logged_in' => false, 'siteaccess' => 'site', 'session' => false, 'node_id' => null, 'tweak' => '',
            'per_user' => false, 'keys' => array(), 'profile' => 'abcd0123', 'removed' => array(), 'changed' => array(),
            'view_keys' => array(), 'from_listeners' => array(), 'view_profile' => null, 'assignments' => array() ) );
        $this->assertStringNotContainsString( 'exp-debug-user-policies', $noRoles, 'no roles: nothing to load' );
    }

    /** UP-06 */
    public function testTheFilterInThePath()
    {
        $arg = new ReflectionMethod( 'expDebugBarServerFunctions', 'policyFilterArg' );
        if ( PHP_VERSION_ID < 80100 )
            $arg->setAccessible( true );
        $this->assertNull( $arg->invoke( null, array(), 0 ) );
        $this->assertNull( $arg->invoke( null, array( '-' ), 0 ) );
        $this->assertNull( $arg->invoke( null, array( '' ), 0 ) );
        $this->assertSame( 'content', $arg->invoke( null, array( 'content', 'read' ), 0 ) );
        $this->assertSame( 'read', $arg->invoke( null, array( 'content', 'read' ), 1 ) );
        $this->assertSame( '*', $arg->invoke( null, array( '*' ), 0 ) );
        foreach ( array( '<script>', 'content/read', "a'b", str_repeat( 'a', 65 ) ) as $bad )
        {
            try
            {
                $arg->invoke( null, array( $bad ), 0 );
                $this->fail( 'refused: ' . $bad );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'letters, digits', $e->getMessage() );
            }
        }
    }

    /** UP-07 */
    public function testTheScript()
    {
        $js = file_get_contents( __DIR__ . '/../../../../../design/standard/javascript/expdebugbar.js' );
        $this->assertStringContainsString( "call('policies::' + pathArg(state.module) + '::' + pathArg(state.fn) + '::' + state.offset, {})", $js,
                                           'the filter and the page in the path, no parameters' );
        $this->assertStringContainsString( "details.addEventListener('toggle'", $js, 'asked when the section is opened' );
        foreach ( array( 'Module', 'Function', 'All modules', 'All functions', 'Role', 'Limitations', 'none', 'Show more',
                         'These roles have no policies.', 'No policy matches the filter.', '%shown of %matching policies (%total in all)' ) as $word )
        {
            $this->assertContains( $word, expDebugBarReport::scriptStrings() );
            $this->assertStringContainsString( "t('" . $word . "'", $js );
        }
    }

    /** UP-08 */
    public function testNoRoles()
    {
        $this->assertSame( array(), expDebugBarUser::policyRows( array() ) );
        $page = expDebugBarUser::policyPage( array(), array() );
        $this->assertSame( array( 0, 0, 0, null, array(), array() ),
                           array( $page['roles'], $page['total'], $page['matching'], $page['next_offset'], $page['modules'], $page['policies'] ) );
    }
}
