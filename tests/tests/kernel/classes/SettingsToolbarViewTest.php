<?php
/**
 * setup/settingstoolbar, the view behind the administration's Quick settings:
 *  - opened by its address (no form posted) it shows the quick settings on a page of their own instead of running its
 *    save on nothing (a fatal error on PHP 8: foreach over null);
 *  - after a save it rebuilds the INI cache, so the setting takes effect on the next request also where
 *    EZP_INI_FILEMTIME_CHECK is false (alpha, the demo sites), where it seemed to be forgotten;
 *  - the form of every design can come back to the page (RedirectURI).
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/SettingsToolbarViewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SettingsToolbarViewTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        return file_get_contents( dirname( __DIR__, 4 ) . '/' . $path );
    }

    public function testWithoutAFormTheViewShowsAPage()
    {
        $view = self::read( 'kernel/private/classes/views/setup/settingstoolbar.php' );
        $guard = strpos( $view, "\$allSettingsList = \$module->isCurrentAction( 'Set' ) ? \$module->actionParameter( 'AllSettingsList' ) : null;" );
        $this->assertNotFalse( $guard, 'the list is read only for the Set action' );
        $page = strpos( $view, 'if ( !is_array( $allSettingsList ) )', $guard );
        $this->assertNotFalse( $page );
        $this->assertLessThan( strpos( $view, 'foreach( $allSettingsList' ), $page, 'the page is returned before the list is walked' );
        $this->assertStringContainsString( "\$tpl->fetch( 'design:setup/settingstoolbar.tpl' )", $view );
        $tpl = self::read( 'design/standard/templates/setup/settingstoolbar.tpl' );
        $this->assertStringContainsString( "{include uri='design:setup/quick_settings.tpl'", $tpl );
    }

    public function testASaveRebuildsTheIniCacheBeforeRedirecting()
    {
        $view = self::read( 'kernel/private/classes/views/setup/settingstoolbar.php' );
        $clear = strpos( $view, "\\eZCache::clearByTag( 'ini' );" );
        $this->assertNotFalse( $clear, 'the INI cache is rebuilt after a save' );
        $this->assertGreaterThan( strrpos( $view, '$ini->save()' ), $clear, 'after the files are saved' );
        $this->assertLessThan( strpos( $view, '$module->redirectTo( $uri );' ), $clear, 'before the redirect' );
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
