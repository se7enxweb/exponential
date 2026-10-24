<?php
/**
 * The everyday policies of the Editor role (expEditorPolicies): what the setup step and the upgrade script
 * bin/php/addeditorpolicies.php add, and the over-broad grants the script reports.
 *
 * No database: the role's policies are plain arrays as policiesOf() makes them.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expEditorPoliciesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expEditorPoliciesTest extends PHPUnit\Framework\TestCase
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

    /** The Editor role of the site packages, with the Member role of the Editors group alongside. */
    private static function packageEditor()
    {
        return self::policies( array( 'user/login', 'ezoe/*', 'content/create', 'websitetoolbar/use', 'content/edit',
                                      'content/read', 'notification/use', 'uiswitch/language', 'uiswitch/design',
                                      'ezjscore/call', 'content/manage_locations', 'ezodf/*', 'content/diff',
                                      'content/versionread', 'content/versionremove', 'content/remove',
                                      'content/translate', 'rss/feed', 'content/bookmark', 'content/pendinglist',
                                      'content/dashboard', 'content/view_embed', 'oauth/authorize', 'exphelp/read' ) );
    }

    private static function packageMember()
    {
        return self::policies( array( 'content/create', 'content/edit', 'user/selfedit', 'notification/use', 'user/password',
                                      'ezjscore/call', 'content/read', 'tags/view', 'info-collection/read' ) );
    }

    public function testPackageEditorGetsHideTrashReverseRelationsAndMultiUpload()
    {
        $plan = expEditorPolicies::plan( self::packageEditor(), array( 'content', 'user', 'ezmultiupload' ), array(), array(), self::packageMember() );
        $this->assertSame( array( 'content/hide', 'content/restore', 'content/reverserelatedlist', 'ezmultiupload/*' ), self::granted( $plan ) );
        $this->assertCount( count( expEditorPolicies::definitions() ), $plan );
    }

    /** Without the Member role alongside (plain_site), the own account policies are added to the Editor role too. */
    public function testOwnAccountPoliciesWhenNoOtherRoleHasThem()
    {
        $plan = expEditorPolicies::plan( self::packageEditor(), array(), array(), array(), array() );
        $this->assertSame( array( 'content/hide', 'content/restore', 'content/reverserelatedlist', 'user/password', 'user/selfedit' ),
                           self::granted( $plan ) );
    }

    /** The base data's content/* covers every content function; ezmultiupload only where the module exists. */
    public function testBaseDataContentStarAndMissingModule()
    {
        $base = self::policies( array( 'content/*', 'ezoe/*', 'user/login', 'uiswitch/language', 'uiswitch/design', 'oauth/authorize', 'exphelp/read' ) );
        $this->assertSame( array( 'user/password', 'user/selfedit', 'notification/use' ),
                           self::granted( expEditorPolicies::plan( $base, array( 'content', 'user' ) ) ) );
        $this->assertSame( array( 'ezmultiupload/*', 'user/password', 'user/selfedit', 'notification/use' ),
                           self::granted( expEditorPolicies::plan( $base, array( 'ezmultiupload' ) ) ) );
    }

    /** A policy in any form counts; a second run adds nothing. */
    public function testExistingPoliciesAreLeftAlone()
    {
        $this->assertTrue( expEditorPolicies::covers( self::policies( array( 'content/*' ) ), 'content', 'hide' ) );
        $this->assertTrue( expEditorPolicies::covers( self::policies( array( '*/*' ) ), 'ezmultiupload', '*' ) );
        $this->assertFalse( expEditorPolicies::covers( self::policies( array( 'content/edit' ) ), 'content', 'hide' ) );
        $first = expEditorPolicies::plan( self::packageEditor(), array( 'ezmultiupload' ), array(), array(), self::packageMember() );
        $after = array_merge( self::packageEditor(), self::policies( self::granted( $first ) ) );
        $this->assertSame( array(), self::granted( expEditorPolicies::plan( $after, array( 'ezmultiupload' ), array(), array(), self::packageMember() ) ) );
    }

    public function testOnlyAndSkip()
    {
        $plan = expEditorPolicies::plan( self::packageEditor(), array( 'ezmultiupload' ), array( 'content/restore' ), array(), self::packageMember() );
        $this->assertSame( array( 'content/restore' ), self::granted( $plan ) );
        $plan = expEditorPolicies::plan( self::packageEditor(), array( 'ezmultiupload' ), array(), array( 'ezmultiupload/*', 'content/hide' ), self::packageMember() );
        $this->assertSame( array( 'content/restore', 'content/reverserelatedlist' ), self::granted( $plan ) );
    }

    /** content/* holds the installation-wide content functions; the package role holds none of them. */
    public function testOverGrants()
    {
        $base = expEditorPolicies::overGrants( self::policies( array( 'content/*', 'ezoe/*', 'user/login' ) ) );
        $names = array_map( function ( $o ) { return $o['module'] . '/' . $o['function'] . ' by ' . $o['by']; }, $base );
        $this->assertSame( array( 'content/cleantrash by content/*', 'content/translations by content/*',
                                  'content/urltranslator by content/*', 'content/jobs by content/*' ), $names );
        $this->assertSame( array(), expEditorPolicies::overGrants( self::packageEditor() ) );
        $admin = expEditorPolicies::overGrants( self::policies( array( '*/*' ) ) );
        $this->assertCount( count( expEditorPolicies::overGrantDefinitions() ), $admin );
        $setup = expEditorPolicies::overGrants( self::policies( array( 'setup/cache', 'user/*' ) ) );
        $this->assertSame( array( 'setup/* by setup/cache', 'user/register by user/*' ),
                           array_map( function ( $o ) { return $o['module'] . '/' . $o['function'] . ' by ' . $o['by']; }, $setup ) );
    }

    public function testDefinitionsAreUniqueAndExplained()
    {
        $seen = array();
        foreach ( expEditorPolicies::definitions() as $definition )
        {
            $name = $definition['module'] . '/' . $definition['function'];
            $this->assertArrayNotHasKey( $name, $seen );
            $seen[$name] = true;
            $this->assertNotSame( '', $definition['reason'] );
            foreach ( expEditorPolicies::overGrantDefinitions() as $over )
                $this->assertNotSame( $over['module'] . '/' . $over['function'], $name, 'a policy is both needed and an over-grant' );
        }
    }
}
