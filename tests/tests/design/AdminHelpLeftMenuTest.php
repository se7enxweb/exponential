<?php
/**
 * The help views (exphelp/*) draw the help's own left menu (menu.ini [Leftmenu_help], design:exphelp/menu.tpl) while
 * the Setup tab, whose navigation part they keep, stays marked; and the header's "?" is marked on those pages.
 * Before, a help page drew the Setup menu, in which the help is a single link. Checked on the sources (no database);
 * rendered in admin4 when the change was made: the help pages show "Help", setup/* and content pages their own menu
 * and no mark on the "?".
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminHelpLeftMenuTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminHelpLeftMenuTest extends PHPUnit\Framework\TestCase
{
    const LEFT_MENU = "\$Result['left_menu'] = 'design:exphelp/menu.tpl';";

    private static function read( $path )
    {
        return AdminFiles::read( dirname( __DIR__, 3 ) . '/' . $path );
    }

    /** The views that render a page; asset, image, pdf, context, state and feedback answer with a file, JSON or a redirect */
    public static function pageViews()
    {
        $views = array();
        foreach ( array( 'audience', 'contents', 'dashboard', 'extensions', 'manual', 'report', 'search', 'topic', 'viewer' ) as $view )
        {
            $views[$view] = array( $view );
        }
        return $views;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'pageViews' )]
    public function testThePageViewsNameTheHelpMenu( $view )
    {
        $php = self::read( "kernel/private/classes/views/exphelp/$view.php" );
        $this->assertStringContainsString( self::LEFT_MENU, $php, $view );
        $this->assertSame( 1, substr_count( $php, '$Result = array();' ), $view );
    }

    public function testTheViewsKeepTheSetupNavigationPart()
    {
        $module = self::read( 'kernel/exphelp/module.php' );
        $this->assertGreaterThan( 0, substr_count( $module, "'default_navigation_part' => 'ezsetupnavigationpart'" ) );
        $this->assertStringNotContainsString( 'exphelpnavigationpart', $module );
    }

    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testTheMenuTemplateDrawsLeftmenuHelp( $design )
    {
        $tpl = self::read( "design/$design/templates/exphelp/menu.tpl" );
        $this->assertStringContainsString( "{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_help'", $tpl );
        foreach ( array( 'help', 'dashboard', 'contents', 'search', 'extensions', 'report' ) as $key )
        {
            $this->assertMatchesRegularExpression( "/'$key',\s+'[^']+'\|i18n\( 'kernel\/exphelp' \)/", $tpl, $key );
        }
    }

    public function testEveryHelpMenuLinkNeedsTheReadPolicy()
    {
        $menu = self::read( 'settings/menu.ini' );
        $this->assertSame( 1, preg_match( '/\n\[Leftmenu_help\]\n((?:(?!\n\[)[\s\S])*)/', $menu, $match ) );
        $section = $match[1];
        $this->assertMatchesRegularExpression( '/^Name=help$/m', $section );
        $this->assertSame( 5, preg_match_all( '/^Links\[(\w+)\]=exphelp\/\w+$/m', $section, $links ) );
        foreach ( $links[1] as $key )
        {
            $this->assertStringContainsString( "\nPolicyList_{$key}[]=exphelp/read\n", $section, $key );
        }
    }

    public function testTheHeaderHelpIsMarkedOnTheHelpPages()
    {
        $this->assertStringContainsString(
            "<div id=\"header-help\" class=\"header-help{if eq( first_set( \$a4_help_params.module_name, '' ), 'exphelp' )} is-current{/if}\">",
            self::read( 'design/admin4/templates/parts/a4_header_help.tpl' ) );
        // the header's cache block is keyed by the module, so the mark does not stay on other pages
        $this->assertMatchesRegularExpression( '/\{cache-block keys=array\( \$ui_context, \$ui_component,[^}]*\}\s*\{\* HEADER/',
            self::read( 'design/admin4/templates/pagelayout.tpl' ) );
        $this->assertStringContainsString( '.header-help.is-current .a4-header-btn {', self::read( 'design/admin4/stylesheets/admin4.css' ) );
    }
}
