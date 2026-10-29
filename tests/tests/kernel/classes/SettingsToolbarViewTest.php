<?php
/**
 * setup/settingstoolbar, the view behind the administration's Quick settings. Every request ends in a page or a
 * redirect, never in an error, whatever it sends:
 *  - opened by its address (no form) it shows the quick settings on a page of their own;
 *  - a form may switch only the settings of site.ini [DebugSettings] QuickSettingsList[], and only in settings/override
 *    or the settings of a siteaccess the installation has: no other setting, no other file, no path in the siteaccess;
 *  - values of the wrong type (a string for a list, a list for a string, entries without ";") are left out;
 *  - Set goes back only to a path inside the site;
 *  - after a save the INI cache is rebuilt, so the setting takes effect where EZP_INI_FILEMTIME_CHECK is false.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/SettingsToolbarViewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/kernel/private/classes/runnable/moduleview.php';
require_once dirname( __DIR__, 4 ) . '/kernel/private/classes/views/setup/settingstoolbar.php';

use Exponential\View\Kernel\Setup\Settingstoolbar;

class SettingsToolbarViewTest extends PHPUnit\Framework\TestCase
{
    const QUICK = array( '', 'DebugSettings;DebugOutput;site.ini;Debug output', 'TemplateSettings;Debug;site.ini;Template debug',
                         'DatabaseSettings;SQLOutput;site.ini;SQL debug output', 'broken', 'X;Y;../../etc/passwd.ini;Z' );

    private static function read( $path )
    {
        return file_get_contents( dirname( __DIR__, 4 ) . '/' . $path );
    }

    private static function parse( $all, $selected, $siteAccess )
    {
        return Settingstoolbar::parseRequest( $all, $selected, $siteAccess, Settingstoolbar::allowedSettings( self::QUICK ), array( 'admin', 'site' ) );
    }

    public function testOnlyWellFormedQuickSettingsAreAllowed()
    {
        $this->assertSame( array( 'DebugSettings;DebugOutput;site.ini', 'TemplateSettings;Debug;site.ini', 'DatabaseSettings;SQLOutput;site.ini' ),
                           array_keys( Settingstoolbar::allowedSettings( self::QUICK ) ) );
        $this->assertSame( array(), Settingstoolbar::allowedSettings( 'not a list' ) );
    }

    public function testTheSidebarFormIsTakenAsItIs()
    {
        $r = self::parse( array( 'DebugSettings;DebugOutput;site.ini', 'TemplateSettings;Debug;site.ini' ), array( '0' ), 'global_override' );
        $this->assertSame( array(), $r['problems'] );
        $this->assertSame( 'global_override', $r['siteaccess'] );
        $this->assertSame( array( 'site' => array( array( 'DebugSettings', 'DebugOutput', true ), array( 'TemplateSettings', 'Debug', false ) ) ), $r['settings'] );
        $r = self::parse( array( 'DebugSettings;DebugOutput;site.ini' ), array(), 'admin' );
        $this->assertSame( 'admin', $r['siteaccess'] );
        $this->assertSame( array( array( 'DebugSettings', 'DebugOutput', false ) ), $r['settings']['site'] );
    }

    public static function badForms()
    {
        return array(
            'no list'                 => array( null, array(), '', array( 'no-form' ) ),
            'a string for the list'   => array( 'DebugSettings;DebugOutput;site.ini', array(), '', array( 'no-form' ) ),
            'an empty list'           => array( array(), array(), '', array( 'no-form' ) ),
            'a path as siteaccess'    => array( array( 'DebugSettings;DebugOutput;site.ini' ), array( '0' ), '../../var', array( 'siteaccess' ) ),
            'an unknown siteaccess'   => array( array( 'DebugSettings;DebugOutput;site.ini' ), array( '0' ), 'nosuchsite', array( 'siteaccess' ) ),
            'a list as siteaccess'    => array( array( 'DebugSettings;DebugOutput;site.ini' ), array( '0' ), array( 'admin' ), array() ),
            'another setting'         => array( array( 'DatabaseSettings;Password;site.ini' ), array( '0' ), '', array( 'setting' ) ),
            'another file'            => array( array( 'DebugSettings;DebugOutput;../../config.ini' ), array(), '', array( 'setting' ) ),
            'an entry without ;'      => array( array( 'garbage' ), array(), '', array( 'setting' ) ),
            'a list as an entry'      => array( array( array( 'x' ) ), array(), '', array( 'setting' ) ),
        );
    }

    /**
     * @dataProvider badForms
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'badForms' )]
    public function testABadFormChangesNothingAndNeverFails( $all, $selected, $siteAccess, array $problems )
    {
        $r = self::parse( $all, $selected, $siteAccess );
        $this->assertSame( $problems, $r['problems'] );
        if ( $problems )
            $this->assertSame( array(), $r['settings'], 'nothing is written' );
        $this->assertContains( $r['siteaccess'], array( 'global_override', 'admin', 'site' ) );
    }

    public function testAMixedFormKeepsOnlyTheQuickSettings()
    {
        $r = self::parse( array( 'DatabaseSettings;Password;site.ini', 'DebugSettings;DebugOutput;site.ini' ), array( '1', 'x', array() ), '' );
        $this->assertSame( array( 'setting' ), $r['problems'] );
        $this->assertSame( array( 'site' => array( array( 'DebugSettings', 'DebugOutput', true ) ) ), $r['settings'] );
    }

    public function testSetGoesBackOnlyInsideTheSite()
    {
        $this->assertSame( '/setup/settingstoolbar', Settingstoolbar::redirectTarget( '/setup/settingstoolbar', '/x' ) );
        $this->assertSame( '/content/dashboard', Settingstoolbar::redirectTarget( 'https://evil.example/', '/content/dashboard' ) );
        $this->assertSame( '/content/dashboard', Settingstoolbar::redirectTarget( '//evil.example/', '/content/dashboard' ) );
        $this->assertSame( '/', Settingstoolbar::redirectTarget( array( 'x' ), "/a\r\nLocation: x" ) );
    }

    public function testTheViewRoutesEveryRequestToAPageOrASave()
    {
        $view = self::read( 'kernel/private/classes/views/setup/settingstoolbar.php' );
        $this->assertStringContainsString( "if ( !\$module->isCurrentAction( 'Set' ) )\n            return \$this->viewResult( \$this->page(), null );", $view );
        $this->assertStringContainsString( "if ( !\$request['settings'] )\n            return \$this->viewResult( \$this->page( \$request['problems'] ), null );", $view );
        $clear = strpos( $view, "\\eZCache::clearByTag( 'ini' );" );
        $this->assertNotFalse( $clear, 'the INI cache is rebuilt after a save' );
        $this->assertGreaterThan( strrpos( $view, '$ini->save()' ), $clear );
        $this->assertStringContainsString( "{include uri='design:setup/quick_settings.tpl'", self::read( 'design/standard/templates/setup/settingstoolbar.tpl' ) );
    }

    public function testThePageBelongsToTheSetupTab()
    {
        $module = self::read( 'kernel/setup/module.php' );
        $start = strpos( $module, "\$ViewList['settingstoolbar']" );
        $view = substr( $module, $start, strpos( $module, "\$ViewList[", $start + 10 ) - $start );
        $this->assertStringContainsString( "'default_navigation_part' => 'ezsetupnavigationpart'", $view );
    }

    public function testEveryFormCanComeBackToThePage()
    {
        foreach ( array( 'admin', 'admin4', 'standard' ) as $design )
        {
            $form = self::read( "design/$design/templates/setup/quick_settings.tpl" );
            $this->assertMatchesRegularExpression( '/\{if is_set\( \$redirect_uri \)\}<input type="hidden" name="RedirectURI" value="\{\$redirect_uri\|wash\}" ?\/?>\{\/if\}/', $form, $design );
            $this->assertStringContainsString( 'name="SiteAccess" value="{$siteaccess|wash}"', $form, "$design: the siteaccess field is intact" );
        }
    }
}
