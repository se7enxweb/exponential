<?php
/**
 * Three admin pages that read another siteaccess or a value that may be missing:
 *
 *  - eZTemplateDesignResource::designExtensions( $siteAccess ) reads the design.ini of that siteaccess, so
 *    allDesignBases( $siteAccess ) (visual/menuconfig, the classic menu inspector) finds the designs of the design
 *    extensions only that siteaccess activates, not those of the siteaccess that shows the page
 *  - setup/info sets every php_ini key (open_basedir is '' when not restricted), and its OPcache/APCu box does not
 *    read $cache.enabled of a cache that is not installed (the if function evaluates every elseif)
 *  - class/view falls back to the name and description of the class's prioritized languages when a class or an
 *    attribute has none in the language shown
 *  - notification/status has a template in design/admin too (it had one in admin4 only: "No template could be
 *    loaded"), with no javascript
 *
 * No database. Runs in its own process, because it changes the global site.ini.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/SiteAccessDesignAndAdminViewWarningsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class SiteAccessDesignAndAdminViewWarningsTest extends PHPUnit\Framework\TestCase
{
    const SITEACCESS = 'zz_designext_test';

    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'settings/siteaccess/' . self::SITEACCESS;
        $this->tearDown(); // a run that was stopped may have left it
    }

    protected function tearDown(): void
    {
        if ( is_file( $this->dir . '/design.ini.append.php' ) )
            unlink( $this->dir . '/design.ini.append.php' );
        if ( is_dir( $this->dir ) )
            rmdir( $this->dir );
    }

    private static function file( $path )
    {
        $file = dirname( __DIR__, 4 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    public function testDesignExtensionsOfAnotherSiteAccessComeFromItsDesignIni()
    {
        mkdir( $this->dir );
        file_put_contents( $this->dir . '/design.ini.append.php',
                           "<?php /* #?ini charset=\"utf-8\"?\n\n[ExtensionSettings]\nDesignExtensions[]\nDesignExtensions[]=zz_only_here\nDesignExtensions[]=zz_last\n*/ ?>\n" );

        $ini = eZINI::instance();
        $siteAccessList = $ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
        try
        {
            $ini->setVariable( 'SiteAccessSettings', 'AvailableSiteAccessList', array_merge( $siteAccessList, array( self::SITEACCESS ) ) );

            // reversed, as for the current siteaccess: the last one named is searched first. Extensions whose own
            // settings add design extensions are read after the siteaccess, so the list may hold more.
            $extensions = eZTemplateDesignResource::designExtensions( self::SITEACCESS );
            $this->assertContains( 'zz_only_here', $extensions );
            $this->assertLessThan( array_search( 'zz_only_here', $extensions ), array_search( 'zz_last', $extensions ) );
            $this->assertNotContains( 'zz_only_here', eZTemplateDesignResource::designExtensions() );
            $this->assertSame( eZTemplateDesignResource::designExtensions(), eZTemplateDesignResource::designExtensions( false ) );
        }
        finally
        {
            $ini->setVariable( 'SiteAccessSettings', 'AvailableSiteAccessList', $siteAccessList );
        }
    }

    public function testFindDesignBaseAsksForTheDesignExtensionsOfItsSiteAccess()
    {
        $source = self::file( 'kernel/common/eztemplatedesignresource.php' );
        $this->assertStringContainsString( 'eZTemplateDesignResource::designExtensions( $siteAccess );', $source );
    }

    public function testSetupInfoSetsEveryPhpIniKey()
    {
        $source = self::file( 'kernel/private/classes/views/setup/info.php' );
        $this->assertStringContainsString( "\$phpINI[\$iniName] = (string)ini_get( \$iniName );", $source );
        $this->assertStringNotContainsString( "if ( \$value !== '' )\n                \$phpINI[\$iniName]", $source );
    }

    public function testSetupInfoReadsCacheEnabledOnlyForAnInstalledCache()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = self::file( "design/$design/templates/setup/info.tpl" );
            $this->assertStringNotContainsString( '{elseif $cache.enabled}', $tpl, "design/$design setup/info.tpl" );
            $this->assertMatchesRegularExpression( '/\{if \$cache\|not\}.*?\{else\}.*?\{if \$cache\.enabled\}/s', $tpl, "design/$design setup/info.tpl" );
        }
    }

    public function testClassViewFallsBackForNamesAndDescriptions()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = self::file( "design/$design/templates/class/view.tpl" );
            // every lookup by $language_code is inside first_set(), which falls back without a template error
            preg_match_all( '/(first_set\(\s*)?\$(?:class|attribute)\.(?:nameList|descriptionList)\[\$language_code\]/', $tpl, $m );
            $this->assertNotEmpty( $m[0], "design/$design class/view.tpl" );
            foreach ( $m[1] as $before )
                $this->assertNotSame( '', $before, "design/$design class/view.tpl: a lookup by language outside first_set()" );
            $this->assertStringContainsString( 'first_set( $class.nameList[$language_code], $class.name )', $tpl );
            $this->assertStringContainsString( 'first_set( $attribute.nameList[$language_code], $attribute.name )', $tpl );
        }
    }

    public function testNotificationStatusHasAnAdminTemplateWithoutJavascript()
    {
        $tpl = self::file( 'design/admin/templates/notification/status.tpl' );
        $this->assertStringContainsString( "'Notification status'|i18n( 'design/admin/notification/status' )", $tpl );
        $this->assertDoesNotMatchRegularExpression( '/<script|ezscript\(|\son[a-z]+=|data-a4-/i', $tpl );
        // every variable the view sets for admin4 that this page reads is one the view sets
        $view = self::file( 'kernel/private/classes/views/notification/status.php' );
        foreach ( array( 'status', 'notice', 'problems', 'missing_rules', 'recent_subscriptions', 'recent_total', 'events' ) as $name )
            $this->assertStringContainsString( "setVariable( '$name'", $view );
    }
}
