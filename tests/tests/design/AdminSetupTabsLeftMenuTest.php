<?php
/**
 * A page of a tab the Setup menu stands in for draws the Setup menu, also when its view names a left menu of its own.
 * The audit views return $module_result.left_menu (design:audit/menu.tpl, expAuditConsole::result()); admin4's
 * page_leftmenu.tpl and admin4l's left menu block drew it before asking parts/a4_navigation_part.tpl, so with Audit in
 * SetupTabs[] or HiddenTabs[] an audit page marked the Setup tab but drew the audit menu instead of the Setup menu with
 * the Audit group open (doc/eng-us/features/6.0/admin4-design.md, "Moving a tab into Setup"). While the tab is in the top
 * menu, the view's own menu stays. Checked on the templates (no database); rendered with and without the tab in
 * Setup when the change was made.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSetupTabsLeftMenuTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminSetupTabsLeftMenuTest extends PHPUnit\Framework\TestCase
{
    const CONDITION = "or( \$navigation_part_name|ne( 'ezsetupnavigationpart' ), \$a4_own_part|eq( 'ezsetupnavigationpart' ) )";

    private static function read( $path )
    {
        return AdminFiles::read( dirname( __DIR__, 3 ) . '/' . $path );
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

    #[PHPUnit\Framework\Attributes\DataProvider( 'leftMenus' )]
    public function testGroupTabsLeftMenuTabDrawsTheTabsOwnMenu( $path )
    {
        $tpl = self::read( $path );
        $switch = strpos( $tpl, "ezini( 'Leftmenu_setup', 'GroupTabsLeftMenu', 'menu.ini' )|eq( 'tab' )" );
        $this->assertNotFalse( $switch, $path );
        // the switch hands the page's own part back after the navigation part is asked and before either menu is drawn
        $this->assertGreaterThan( strpos( $tpl, "{include uri='design:parts/a4_navigation_part.tpl'}{/set-block}" ), $switch, $path );
        $this->assertLessThan( strpos( $tpl, '{include uri=$module_result.left_menu}' ), $switch, $path );
        $this->assertStringContainsString( '{set $navigation_part_name = $a4_own_part}', $tpl, $path );
    }

    public function testGroupTabsLeftMenuDefaultsToTheSetupMenu()
    {
        $this->assertMatchesRegularExpression( '/\n\[Leftmenu_setup\]\n(?:(?!\n\[)[\s\S])*\nGroupTabsLeftMenu=setup\n/', self::read( 'settings/menu.ini' ) );
    }

    /**
     * The menu each left menu template draws, rendered with the real template engine: <resolved part> is what
     * parts/a4_navigation_part.tpl answers (ezsetupnavigationpart while the tab is moved into Setup), <own part> the
     * page's, <left menu> the view's $module_result.left_menu or ''.
     */
    public static function drawnMenus()
    {
        $cases = array(
            // an audit page (a view with its own left menu) while Audit is moved into Setup
            'audit moved, setup' => array( 'setup', 'ezsetupnavigationpart', 'expauditnavigationpart', 'design:audit/menu.tpl', 'part:setup' ),
            'audit moved, tab'   => array( 'tab',   'ezsetupnavigationpart', 'expauditnavigationpart', 'design:audit/menu.tpl', 'view:design:audit/menu.tpl' ),
            // a design page (the tab's parts/design/menu.tpl) while Design is moved into Setup
            'design moved, setup' => array( 'setup', 'ezsetupnavigationpart', 'ezdesignnavigationpart', '', 'part:setup' ),
            'design moved, tab'   => array( 'tab',   'ezsetupnavigationpart', 'ezdesignnavigationpart', '', 'part:design' ),
            // the tab is in the top menu: its own menu whatever the setting
            'audit shown, setup' => array( 'setup', 'expauditnavigationpart', 'expauditnavigationpart', 'design:audit/menu.tpl', 'view:design:audit/menu.tpl' ),
            'design shown, tab'  => array( 'tab',   'ezdesignnavigationpart', 'ezdesignnavigationpart', '', 'part:design' ),
            // a Setup page itself
            'setup page, tab' => array( 'tab', 'ezsetupnavigationpart', 'ezsetupnavigationpart', '', 'part:setup' ),
        );
        $data = array();
        foreach ( self::leftMenus() as $design => $args )
            foreach ( $cases as $name => $case )
                $data["$design: $name"] = array_merge( $args, $case );
        return $data;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'drawnMenus' )]
    public function testTheDrawnMenuFollowsGroupTabsLeftMenu( $path, $setting, $resolved, $own, $leftMenu, $expected )
    {
        $this->assertSame( $expected, $this->render( $path, $setting, $resolved, $own, $leftMenu ) );
    }

    /**
     * Renders a copy of a left menu template in var/tmp whose three includes print what they would include: the
     * navigation part answers $resolved, the view's menu prints view:<template>, a part's menu part:<name>.
     */
    private function render( $path, $setting, $resolved, $own, $leftMenu )
    {
        $root = dirname( __DIR__, 3 );
        $source = self::read( $path );
        $includes = array(
            "{include uri='design:parts/a4_navigation_part.tpl'}" => '{$test_resolved_part}',
            '{include uri=$module_result.left_menu}' => '[view:{$module_result.left_menu}]',
            "{include uri=concat( 'design:parts/', \$part_name, '/menu.tpl' )}" => '[part:{$part_name}]',
        );
        foreach ( $includes as $include => $replacement )
            $this->assertSame( 1, substr_count( $source, $include ), "$path: $include" );
        $dir = $root . '/var/tmp/phpunit-setup-tabs-left-menu-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $dir, 0777, true );
        $copy = $dir . '/menu.tpl';
        file_put_contents( $copy, strtr( $source, $includes ) );
        $injected = ( new ReflectionProperty( 'eZINI', 'injectedSettings' ) )->getValue();
        $basics = array_key_exists( 'eZSiteBasics', $GLOBALS ) ? array( $GLOBALS['eZSiteBasics'] ) : null;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        eZINI::injectSettings( array( 'menu.ini' => array( 'Leftmenu_setup' => array( 'GroupTabsLeftMenu' => $setting ) ) ) );
        eZINI::resetInstance( 'menu.ini' );
        try
        {
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'test_resolved_part', $resolved );
            $tpl->setVariable( 'navigation_part', array( 'identifier' => $own ) );
            $module = array( 'navigation_part' => $own );
            if ( $leftMenu !== '' )
                $module['left_menu'] = $leftMenu;
            $tpl->setVariable( 'module_result', $module );
            $tpl->setVariable( 'block', array( 'values' => array( 'part' => '' ) ) );
            $html = $tpl->fetch( $copy );
            $this->assertSame( array(), $tpl->errorLog() );
            $this->assertSame( 1, preg_match_all( '/\[((?:view|part):[^\]]*)\]/', $html, $drawn ), "$path draws exactly one menu" );
            return $drawn[1][0];
        }
        finally
        {
            eZTemplate::resetInstance();
            eZINI::injectSettings( $injected );
            eZINI::resetInstance( 'menu.ini' );
            if ( $basics === null )
                unset( $GLOBALS['eZSiteBasics'] );
            else
                $GLOBALS['eZSiteBasics'] = $basics[0];
            unlink( $copy );
            rmdir( $dir );
        }
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
