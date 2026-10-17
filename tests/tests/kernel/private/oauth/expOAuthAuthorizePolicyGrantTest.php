<?php
/**
 * The plan of the upgrade script bin/php/addoauthauthorizepolicy.php (expOAuthAuthorizePolicyGrant::plan(), #548):
 * every role that lets users sign in gets oauth/authorize for the same siteaccesses, roles that have it already or
 * belong only to the anonymous user are skipped, and --role restricts the grant to the roles named.
 *
 * No database: the roles are plain arrays as roleData() makes them.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/oauth/expOAuthAuthorizePolicyGrantTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group oauth
 */

class expOAuthAuthorizePolicyGrantTest extends PHPUnit\Framework\TestCase
{
    private static function role( $id, $name, array $policies, $anonymousOnly = false )
    {
        $list = array();
        foreach ( $policies as $policy )
        {
            list( $module, $function ) = explode( '/', $policy[0] );
            $list[] = array( 'module' => $module, 'function' => $function, 'limitations' => isset( $policy[1] ) ? $policy[1] : array() );
        }
        return array( 'id' => $id, 'name' => $name, 'anonymous_only' => $anonymousOnly, 'policies' => $list );
    }

    private static function roles()
    {
        return array(
            self::role( 1, 'Anonymous', array( array( 'content/read' ), array( 'user/login', array( 'SiteAccess' => array( '111' ) ) ) ), true ),
            self::role( 2, 'Administrator', array( array( '*/*' ) ) ),
            self::role( 3, 'Editor', array( array( 'content/*' ), array( 'user/login', array( 'SiteAccess' => array( '222' ) ) ), array( 'user/login', array( 'SiteAccess' => array( '333', '222' ) ) ) ) ),
            self::role( 4, 'Member', array( array( 'user/login' ), array( 'content/read' ) ) ),
            self::role( 5, 'Readers', array( array( 'content/read' ) ) ),
            self::role( 6, 'API', array( array( 'user/login' ), array( 'oauth/authorize' ) ) ),
            self::role( 7, 'OAuth all', array( array( 'user/login' ), array( 'oauth/*' ) ) ),
            self::role( 8, 'User all', array( array( 'user/*' ) ) ),
        );
    }

    private static function byName( array $plan )
    {
        $result = array();
        foreach ( $plan as $entry )
            $result[$entry['name']] = $entry;
        return $result;
    }

    public function testEveryRoleThatLetsUsersSignInGetsThePolicyForTheSameSiteaccesses()
    {
        $plan = self::byName( expOAuthAuthorizePolicyGrant::plan( self::roles() ) );

        $this->assertSame( 'skip', $plan['Anonymous']['action'] );
        $this->assertSame( 'assigned only to the anonymous user', $plan['Anonymous']['reason'] );
        $this->assertSame( 'skip', $plan['Administrator']['action'] );
        $this->assertSame( 'already has oauth/authorize', $plan['Administrator']['reason'] );

        $this->assertSame( 'grant', $plan['Editor']['action'] );
        $this->assertSame( array( 'SiteAccess' => array( '222', '333' ) ), $plan['Editor']['limitations'] );

        $this->assertSame( 'grant', $plan['Member']['action'] );
        $this->assertSame( array(), $plan['Member']['limitations'] );

        $this->assertSame( 'skip', $plan['Readers']['action'] );
        $this->assertSame( 'no user/login', $plan['Readers']['reason'] );
        $this->assertSame( 'skip', $plan['API']['action'] );
        $this->assertSame( 'skip', $plan['OAuth all']['action'] );

        $this->assertSame( 'grant', $plan['User all']['action'] );
        $this->assertSame( array(), $plan['User all']['limitations'] );
        $this->assertCount( 8, $plan );
    }

    public function testListedRolesOnly()
    {
        $plan = self::byName( expOAuthAuthorizePolicyGrant::plan( self::roles(), array( 'Readers', '1', '6' ) ) );

        $this->assertSame( 'not listed', $plan['Editor']['reason'] );
        $this->assertSame( 'not listed', $plan['Member']['reason'] );
        // listed: granted without user/login, and for the anonymous role too (it was asked for)
        $this->assertSame( 'grant', $plan['Readers']['action'] );
        $this->assertSame( array(), $plan['Readers']['limitations'] );
        $this->assertSame( 'grant', $plan['Anonymous']['action'] );
        $this->assertSame( array( 'SiteAccess' => array( '111' ) ), $plan['Anonymous']['limitations'] );
        // listed, but has it already
        $this->assertSame( 'already has oauth/authorize', $plan['API']['reason'] );
    }

    public function testASecondRunGrantsNothing()
    {
        $roles = self::roles();
        $plan = expOAuthAuthorizePolicyGrant::plan( $roles );
        foreach ( $plan as $i => $entry )
        {
            if ( $entry['action'] === 'grant' )
                $roles[$i]['policies'][] = array( 'module' => 'oauth', 'function' => 'authorize', 'limitations' => $entry['limitations'] );
        }
        foreach ( expOAuthAuthorizePolicyGrant::plan( $roles ) as $entry )
            $this->assertSame( 'skip', $entry['action'], $entry['name'] );
    }

    public function testLoginSiteAccesses()
    {
        $this->assertFalse( expOAuthAuthorizePolicyGrant::loginSiteAccesses( array() ) );
        $this->assertNull( expOAuthAuthorizePolicyGrant::loginSiteAccesses( array(
            array( 'module' => 'user', 'function' => 'login', 'limitations' => array( 'SiteAccess' => array( '1' ) ) ),
            array( 'module' => 'user', 'function' => 'login', 'limitations' => array() ) ) ) );
    }

    public function testApplyOnlyGrants()
    {
        $this->assertFalse( expOAuthAuthorizePolicyGrant::apply( null, array( 'action' => 'skip' ) ) );
        $role = new class {
            public $calls = array();
            public function appendPolicy( $module, $function, $limitations = array() ) { $this->calls[] = func_get_args(); return 'policy'; }
        };
        $this->assertSame( 'policy', expOAuthAuthorizePolicyGrant::apply( $role, array( 'action' => 'grant', 'limitations' => array( 'SiteAccess' => array( '9' ) ) ) ) );
        $this->assertSame( array( array( 'oauth', 'authorize', array( 'SiteAccess' => array( '9' ) ) ) ), $role->calls );
    }
}
