<?php
/**
 * A page of a tab the Setup menu stands in for draws the Setup menu, also when its view names a left menu of its own.
 * The audit views return $module_result.left_menu (design:audit/menu.tpl, expAuditConsole::result()); admin4's
 * page_leftmenu.tpl and admin4l's left menu block drew it before asking parts/a4_navigation_part.tpl, so with Audit in
 * SetupTabs[] or HiddenTabs[] an audit page marked the Setup tab but drew the audit menu instead of the Setup menu with
 * the Audit group open (doc/features/6.0/admin4-design.md, "Moving a tab into Setup"). While the tab is in the top
 * menu, the view's own menu stays. Checked on the templates (no database); rendered with and without the tab in
 * Setup when the change was made.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSetupTabsLeftMenuTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSetupTabsLeftMenuTest extends PHPUnit\Framework\TestCase
{
    const CONDITION = "or( \$navigation_part_name|ne( 'ezsetupnavigationpart' ), \$a4_own_part|eq( 'ezsetupnavigationpart' ) )";

    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public static function leftMenus()
    {
        return array(
            'admin4'  => array( 'design/admin4/templates/page_leftmenu.tpl' ),
            'admin4l' => array( 'design/admin4l/templates/explayouts/block/admin_left_menu.tpl' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'leftMenus' )]
    public function testTheViewsOwnLeftMenuGivesWayToTheSetupMenu( $path )
    {
        $tpl = self::read( $path );
        $navigationPart = strpos( $tpl, "{include uri='design:parts/a4_navigation_part.tpl'}{/set-block}" );
        $ownMenu = strpos( $tpl, '{include uri=$module_result.left_menu}' );
        $this->assertNotFalse( $navigationPart, $path );
        $this->assertNotFalse( $ownMenu, $path );
        $this->assertLessThan( $ownMenu, $navigationPart, "$path asks the navigation part before it draws the view's own menu" );
        // the view's own menu only while the page is not moved into Setup: the navigation part is not Setup's, or the
        // page's own part is Setup's already
        $this->assertStringContainsString( self::CONDITION, $tpl, $path );
        $this->assertStringContainsString( "\$a4_own_part = first_set( \$navigation_part.identifier, '' )", $tpl, $path );
        $this->assertStringContainsString( "{if \$a4_own_part|eq( '' )}{set \$a4_own_part = first_set( \$module_result.navigation_part, '' )}{/if}", $tpl, $path );
        $this->assertStringContainsString( '{undef $navigation_part_name $a4_own_part}', $tpl, $path );
    }

    public function testTheAuditViewsNameTheirOwnMenuAndTheirNavigationPartIsMoved()
    {
        $this->assertStringContainsString( "'left_menu' => 'design:audit/menu.tpl'", self::read( 'kernel/classes/audit/console/expauditconsole.php' ) );
        $this->assertStringContainsString( "'default_navigation_part' => 'expauditnavigationpart'", self::read( 'kernel/audit/module.php' ) );
        $menu = self::read( 'settings/menu.ini' );
        $this->assertStringContainsString( 'GroupTabs[audit]=audit', $menu );
        $this->assertMatchesRegularExpression( '/\[Topmenu_audit\][^\[]*NavigationPartIdentifier=expauditnavigationpart/s', $menu );
    }
}
