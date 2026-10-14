<?php
/**
 * Setup > Extensions hands an extension out as a package archive only through a button of its form (POST, with
 * the form token), and not when the extension's settings hold a secret value
 * (\Exponential\View\Kernel\Setup\Extensions::download() and secretSettings()). No database.
 *
 *  ED-01 - secretSettings(): a secret name with a value, or a value with a password in it, is found; empty values,
 *          numbers, switches and comments are not
 *  ED-02 - download() refuses an extension with a secret setting before anything is packed
 *  ED-03 - download() refuses an unknown extension or format
 *  ED-04 - the view no longer packs from the address (/setup/extensions/<name>/<format>), and both designs post a
 *          button instead of linking
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Setup\Extensions;

class ExtensionDownloadTest extends PHPUnit\Framework\TestCase
{
    /** @var string the working directory before the test */
    private $cwd;

    /** @var string an extension made for the test, below extension/ */
    private $name;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->name = 'phpunitextdownload' . getmypid();
        $settings = eZExtension::baseDirectory() . '/' . $this->name . '/settings';
        mkdir( $settings . '/siteaccess/site', 0777, true );
        file_put_contents( $settings . '/plain.ini', "<?php /* #?ini charset=\"utf-8\"?\n[Limits]\nMaxTokens=4096\nRequireToken=enabled\nPassword=\n# Password=commented\n*/ ?>\n" );
    }

    protected function tearDown(): void
    {
        eZDir::recursiveDelete( eZExtension::baseDirectory() . '/' . $this->name );
        chdir( $this->cwd );
    }

    private function dir()
    {
        return eZExtension::baseDirectory() . '/' . $this->name;
    }

    private function download( $choice, array $available )
    {
        $view = ( new ReflectionClass( Extensions::class ) )->newInstanceWithoutConstructor();
        $method = new ReflectionMethod( Extensions::class, 'download' );
        return $method->invoke( $view, $choice, $available );
    }

    /** ED-01 */
    public function testSecretSettingsAreFound()
    {
        $this->assertSame( array(), Extensions::secretSettings( $this->dir() ) );
        file_put_contents( $this->dir() . '/settings/siteaccess/site/site.ini.append.php',
                           "<?php /* #?ini charset=\"utf-8\"?\n[DatabaseSettings]\nUser=db\nPassword=hunter2\n[Feed]\nSource=https://reader:pw1@feeds.example.invalid/x\nApiKey[default]=abc\n*/ ?>\n" );
        $this->assertSame( array( 'siteaccess/site/site.ini.append.php: ApiKey', 'siteaccess/site/site.ini.append.php: Password',
                                  'siteaccess/site/site.ini.append.php: Source' ), Extensions::secretSettings( $this->dir() ) );
        $this->assertSame( array(), Extensions::secretSettings( $this->dir() . '/missing' ) );
    }

    /** ED-02 */
    public function testAnExtensionWithASecretIsNotPacked()
    {
        file_put_contents( $this->dir() . '/settings/site.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n[DatabaseSettings]\nPassword=hunter2\n*/ ?>\n" );
        $error = $this->download( $this->name . '/zip', array( $this->name ) );
        $this->assertStringContainsString( 'site.ini.append.php: Password', $error );
        $this->assertStringNotContainsString( 'hunter2', $error );
    }

    /** ED-03 */
    public function testUnknownExtensionsAndFormatsAreRefused()
    {
        $this->assertSame( 'The extension or the archive format is not known.', $this->download( $this->name . '/exe', array( $this->name ) ) );
        $this->assertSame( 'The extension or the archive format is not known.', $this->download( $this->name . '/zip', array() ) );
        $this->assertSame( 'The extension or the archive format is not known.', $this->download( '../' . $this->name . '/zip', array( '../' . $this->name ) ) );
    }

    /** ED-04 */
    public function testOnlyThePostedButtonDownloads()
    {
        $root = dirname( __DIR__, 5 );
        $source = file_get_contents( $root . '/kernel/private/classes/views/setup/extensions.php' );
        $this->assertStringNotContainsString( "\$Params['ExtensionFormat']", $source );
        $this->assertStringContainsString( "\$http->hasPostVariable( 'ExtensionDownload' )", $source );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = file_get_contents( $root . "/design/$design/templates/setup/extensions.tpl" );
            $this->assertStringNotContainsString( "'/setup/extensions/', \$row.name", $tpl, $design );
            $this->assertStringContainsString( 'name="ExtensionDownload"', $tpl, $design );
        }
    }
}
