<?php
/**
 * The policies of a role (role/view, role/edit) and the role cards of role/list are striped like the data tables
 * (--a4-zebra), so a long list reads row by row; a hidden entry does not break the stripes. A one-line select
 * without a class in an .a4-field (the "Subtree" select beside "Assign" in role/view) leaves room at its end for the
 * chevron admin4.css draws there. Both rules are in the admin4 and the admin copy of the stylesheet. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminRoleListZebraTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminRoleListZebraTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    public function testPoliciesAndRoleCardsAreStriped()
    {
        $rule = '.a4-role-policies > .a4-role-policy:where(:nth-child(even of :not([hidden]))),' . "\n" .
                '#role-list > .a4-card:where(:nth-child(even of :not([hidden]))) { background-color: var(--a4-zebra); }';
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $css = AdminFiles::read( self::root() . "/design/$design/stylesheets/modules/people.css" );
            $this->assertStringContainsString( $rule, $css, $design );
            // the stripe comes before the tints of a moved or selected policy, which win
            $this->assertLessThan( strpos( $css, '.a4-role-policy.is-selected' ), strpos( $css, $rule ), $design );
            $this->assertMatchesRegularExpression( '/--a4-zebra:\s*#/', AdminFiles::read( self::root() . "/design/$design/stylesheets/components.css" ) );
        }
        $this->assertStringContainsString( 'id="role-list"', AdminFiles::read( self::root() . '/design/admin4/templates/role/list.tpl' ) );
        foreach ( array( 'view', 'edit' ) as $view )
        {
            $tpl = AdminFiles::read( self::root() . "/design/admin4/templates/role/$view.tpl" );
            $this->assertStringContainsString( 'a4-role-policies', $tpl, $view );
            $this->assertStringContainsString( 'class="a4-role-policy', $tpl, $view );
        }
    }

    public function testAOneLineSelectWithoutAClassHasRoomForTheChevron()
    {
        $rule = '.a4-field select:not([class]):where(:not([multiple]):not([size]), [size="1"]) { padding-inline-end: var(--a4-space-6); }';
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $css = AdminFiles::read( self::root() . "/design/$design/stylesheets/components.css" );
            $this->assertStringContainsString( $rule, $css, $design );
            // after the field padding it overrides
            $this->assertGreaterThan( strpos( $css, '.a4-field select[multiple], select.a4-input[multiple]' ), strpos( $css, $rule ), $design );
        }
        // the chevron is drawn for exactly these selects, at the inline end (and at the start in right-to-left)
        $admin4 = AdminFiles::read( self::root() . '/design/admin4/stylesheets/admin4.css' );
        $this->assertStringContainsString( ':where(:not([class])):where(:not([multiple]):not([size]), [size="1"])', $admin4 );
    }
}
