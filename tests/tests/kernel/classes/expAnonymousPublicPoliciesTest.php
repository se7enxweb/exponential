<?php
/**
 * The public policies of the Anonymous role (expAnonymousPublicPolicies): what the upgrade script
 * bin/php/addanonymouspublicpolicies.php adds, and the setup step's user/register guarantee.
 *
 * No database: the role's policies are plain arrays as policiesOf() makes them.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expAnonymousPublicPoliciesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expAnonymousPublicPoliciesTest extends PHPUnit\Framework\TestCase
{
    private static function policies( array $names )
    {
        $list = array();
        foreach ( $names as $name )
        {
            list( $module, $function ) = explode( '/', $name );
            $list[] = array( 'module' => $module, 'function' => $function );
        }
        return $list;
    }

    private static function granted( array $plan )
    {
        $names = array();
        foreach ( $plan as $entry )
            if ( $entry['action'] === 'grant' )
                $names[] = $entry['module'] . '/' . $entry['function'];
        return $names;
    }

    /** The base data before 6.0.15: content/read, content/pdf, user/login; a kernel without eztags and cjw_newsletter. */
    public function testOldBaseDataGetsRegisterAndFeed()
    {
        $plan = expAnonymousPublicPolicies::plan( self::policies( array( 'content/read', 'content/pdf', 'user/login' ) ), array( 'content', 'user', 'rss' ) );
        $this->assertSame( array( 'user/register', 'rss/feed' ), self::granted( $plan ) );
        $this->assertCount( count( expAnonymousPublicPolicies::definitions() ), $plan );
    }

    public function testExtensionPoliciesOnlyWhereTheModuleExists()
    {
        $plan = expAnonymousPublicPolicies::plan( self::policies( array( 'user/register', 'rss/feed' ) ), array( 'tags', 'newsletter' ) );
        $this->assertSame( array( 'tags/view', 'newsletter/subscribe', 'newsletter/configure', 'newsletter/unsubscribe', 'newsletter/archive' ),
                           self::granted( $plan ) );
        $plan = expAnonymousPublicPolicies::plan( self::policies( array( 'user/register', 'rss/feed' ) ), array() );
        $this->assertSame( array(), self::granted( $plan ) );
    }

    /** A policy in any form counts: module/*, * / *, a limited one; a second run adds nothing. */
    public function testExistingPoliciesAreLeftAlone()
    {
        $this->assertTrue( expAnonymousPublicPolicies::covers( self::policies( array( 'user/*' ) ), 'user', 'register' ) );
        $this->assertTrue( expAnonymousPublicPolicies::covers( self::policies( array( '*/*' ) ), 'newsletter', 'archive' ) );
        $this->assertFalse( expAnonymousPublicPolicies::covers( self::policies( array( 'user/login' ) ), 'user', 'register' ) );
        $all = self::policies( array( 'user/register', 'rss/feed', 'tags/view', 'newsletter/*' ) );
        $this->assertSame( array(), self::granted( expAnonymousPublicPolicies::plan( $all, array( 'tags', 'newsletter' ) ) ) );
    }

    public function testOnlyAndSkip()
    {
        $plan = expAnonymousPublicPolicies::plan( array(), array( 'tags', 'newsletter' ), array( 'user/register' ) );
        $this->assertSame( array( 'user/register' ), self::granted( $plan ) );
        $plan = expAnonymousPublicPolicies::plan( array(), array(), array(), array( 'user/register' ) );
        $this->assertSame( array( 'rss/feed' ), self::granted( $plan ) );
    }

    /** Every planned grant is a module/function the role can hold without limitations. */
    public function testDefinitionsAreUniqueAndExplained()
    {
        $seen = array();
        foreach ( expAnonymousPublicPolicies::definitions() as $definition )
        {
            $name = $definition['module'] . '/' . $definition['function'];
            $this->assertArrayNotHasKey( $name, $seen );
            $seen[$name] = true;
            $this->assertNotSame( '', $definition['reason'] );
        }
        $this->assertArrayHasKey( 'user/register', $seen );
        $this->assertArrayNotHasKey( 'ezinfo/about', $seen );
    }
}
