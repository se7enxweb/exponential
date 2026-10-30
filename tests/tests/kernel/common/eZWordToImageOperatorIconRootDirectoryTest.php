<?php
/**
 * The icon operators look up share/icons (and extension/<name>/icons) under eZSys::siteDir() when that is a directory,
 * and under the current directory, the legacy root, when it is not. Under a Symfony front controller (the Legacy
 * Bridge's web/app.php, public/index.php) SCRIPT_FILENAME does not end in the legacy index.php and siteDir() is
 * "<web dir>/app.php/": every theme was skipped, each icon was logged as "Missing icon file" and its <img> pointed at
 * share/icons/crystal/<size name>/mimetypes/empty.png, a 404 in the legacy admin of every Platform Legacy project.
 *
 * No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/common/eZWordToImageOperatorIconRootDirectoryTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group templateoperators
 */

class eZWordToImageOperatorIconRootDirectoryTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugLogFileEnabled' );
    private $dir;
    private $siteDir;
    private $saved = array();
    private $savedINI = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/icon_root_directory_' . uniqid( '', true );
        mkdir( $this->dir, 0700, true );
        $this->siteDir = eZSys::instance()->SiteDir;

        foreach ( self::GLOBAL_KEYS as $key )
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $debug = new eZDebug();
        $debug->ShowTypes = eZDebug::SHOW_ALL;
        $GLOBALS['eZDebugGlobalInstance'] = $debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
        eZDebug::setLogFileEnabled( false );

        // The classic admin's icons: crystal-admin, then crystal
        $icon = eZINI::instance( 'icon.ini' );
        $site = eZINI::instance();
        foreach ( array( array( $icon, 'IconSettings', 'Theme' ), array( $icon, 'IconSettings', 'StandardTheme' ),
                         array( $site, 'DesignSettings', 'SiteDesign' ) ) as $setting )
            $this->savedINI[] = array( $setting, $setting[0]->hasVariable( $setting[1], $setting[2] ) ? array( $setting[0]->variable( $setting[1], $setting[2] ) ) : null );
        $icon->setVariable( 'IconSettings', 'Theme', 'crystal-admin' );
        $icon->setVariable( 'IconSettings', 'StandardTheme', 'crystal' );
        $site->setVariable( 'DesignSettings', 'SiteDesign', 'admin3' );
    }

    protected function tearDown(): void
    {
        eZSys::instance()->SiteDir = $this->siteDir;
        foreach ( $this->savedINI as $saved )
        {
            list( $setting, $value ) = $saved;
            if ( $value === null )
            {
                if ( $setting[0]->hasVariable( $setting[1], $setting[2] ) )
                    $setting[0]->removeSetting( $setting[1], $setting[2] );
            }
            else
                $setting[0]->setVariable( $setting[1], $setting[2], $value[0] );
        }
        foreach ( $this->saved as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        eZTemplate::resetInstance();
        foreach ( glob( $this->dir . '/*' ) ?: array() as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    public function testASiteDirectoryThatIsADirectoryIsUsed()
    {
        eZSys::instance()->SiteDir = getcwd() . '/';
        $this->assertSame( getcwd() . '/', eZWordToImageOperator::iconRootDirectory() );
    }

    public function testASymfonyFrontControllerPathFallsBackToTheLegacyRoot()
    {
        eZSys::instance()->SiteDir = getcwd() . '/web/app.php/';
        $this->assertSame( '', eZWordToImageOperator::iconRootDirectory() );
    }

    public function testTheIconsResolveUnderASymfonyFrontController()
    {
        eZSys::instance()->SiteDir = getcwd() . '/web/app.php/';
        $file = $this->dir . '/icons.tpl';
        file_put_contents( $file, "{'folder'|class_icon( 'small', '', true() )}|{'folder'|class_icon( 'normal', '', true() )}|{'text/plain'|mimetype_icon( 'small', '', true() )}" );
        eZTemplate::resetInstance();
        $out = eZTemplate::factory()->fetch( $file );

        $this->assertSame( '/share/icons/crystal-admin/16x16_indexed/filesystems/folder.png|/share/icons/crystal-admin/32x32/filesystems/folder.png|/share/icons/crystal-admin/16x16_indexed/mimetypes/ascii.png',
                           $out );
        $this->assertStringNotContainsString( 'empty.png', $out );
        $this->assertSame( array(), $this->errors(), 'no "Missing icon file" error' );
    }

    private function errors()
    {
        $out = array();
        foreach ( eZDebug::instance()->DebugStrings as $entry )
            if ( $entry['Level'] === eZDebug::LEVEL_ERROR )
                $out[] = $entry['String'];
        return $out;
    }
}
