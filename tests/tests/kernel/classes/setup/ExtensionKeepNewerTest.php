<?php
/**
 * A site package never puts an older copy of an extension in place of the installed one (eZExtensionPackageHandler,
 * the keep-newer rule). No database.
 *
 *  KN-01 - normalizeVersion(): release numbers pass ("v" removed), "dev-master", "//autogentag//", "" do not
 *  KN-02 - extensionVersionText()/extensionVersion(): extension.xml <metadata><version> first, ezinfo.php
 *          'Version' when extension.xml has no release number, null when neither has one
 *  KN-03 - keepInstalled(): newer package replaces, same or older is kept, unknown never wins over a known number,
 *          an installed copy without a number stays only when Composer or git manages it
 *  KN-04 - extensionManager(): a .git entry is git, an install-path of vendor/composer/installed.json is composer
 *          (format 1 and 2), anything else ''
 *  KN-05 - keepInstalledReason(): the published 2012 packages against the Composer copies (ezwebin, ezstarrating,
 *          ezwt, ezgmaplocation) are kept, with a reason naming both versions; a newer package is not
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ExtensionKeepNewerTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-ext-keep-newer-' . getmypid();
        @mkdir( $this->dir, 0777, true );
    }

    protected function tearDown(): void
    {
        if ( is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    /** An extension directory below the test directory with the given extension.xml version and ezinfo.php version */
    private function extension( $relative, $xmlVersion = null, $infoVersion = null )
    {
        $dir = $this->dir . '/' . $relative;
        @mkdir( $dir, 0777, true );
        if ( $xmlVersion !== null )
            file_put_contents( $dir . '/extension.xml', "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<software>\n    <metadata>\n" .
                "        <name>Test</name>\n        <version>$xmlVersion</version>\n    </metadata>\n</software>\n" );
        if ( $infoVersion !== null )
            file_put_contents( $dir . '/ezinfo.php', "<?php\nclass testInfo\n{\n    static function info()\n    {\n" .
                "        return array( 'Name' => 'Test', 'Version'   => '$infoVersion' );\n    }\n}\n" );
        return $dir;
    }

    public function testNormalizeVersion()
    {
        foreach ( array( '6.0.27' => '6.0.27', 'v6.1.2' => '6.1.2', '5.3' => '5.3', '2.4.25' => '2.4.25',
                         ' 1.2.3 ' => '1.2.3', '6.0.15alpha2' => '6.0.15alpha2', '1.0-beta1' => '1.0-beta1' ) as $in => $out )
            $this->assertSame( $out, eZExtensionPackageHandler::normalizeVersion( $in ), $in );
        foreach ( array( 'dev-master', '//autogentag//', '', 'unknown', 'master', '6.x-dev ', null, array() ) as $in )
            $this->assertNull( eZExtensionPackageHandler::normalizeVersion( $in ), var_export( $in, true ) );
    }

    public function testExtensionVersionReadsExtensionXmlThenEzinfo()
    {
        $both = $this->extension( 'both', '6.0.16', '6.0.15' );
        $this->assertSame( '6.0.16', eZExtensionPackageHandler::extensionVersion( $both ) );

        $infoOnly = $this->extension( 'info', 'dev-master', '6.1.2' );
        $this->assertSame( '6.1.2', eZExtensionPackageHandler::extensionVersion( $infoOnly ) );

        $tag = $this->extension( 'tag', '//autogentag//' );
        $this->assertNull( eZExtensionPackageHandler::extensionVersion( $tag ) );
        $this->assertSame( '//autogentag//', eZExtensionPackageHandler::extensionVersionText( $tag ) );

        $none = $this->extension( 'none' );
        $this->assertNull( eZExtensionPackageHandler::extensionVersion( $none ) );
        $this->assertNull( eZExtensionPackageHandler::extensionVersionText( $none ) );

        // a <version> outside <metadata> (a dependency's) is not the extension's
        @mkdir( $this->dir . '/dep', 0777, true );
        file_put_contents( $this->dir . '/dep/extension.xml', "<software><dependencies><requires><version>9.9</version></requires></dependencies></software>" );
        $this->assertNull( eZExtensionPackageHandler::extensionVersion( $this->dir . '/dep' ) );
    }

    public function testKeepInstalledRule()
    {
        // both known: only a newer package replaces
        $this->assertFalse( eZExtensionPackageHandler::keepInstalled( '6.0.16', '6.0.17' ) );
        $this->assertTrue( eZExtensionPackageHandler::keepInstalled( '6.0.16', '6.0.16' ) );
        $this->assertTrue( eZExtensionPackageHandler::keepInstalled( '6.0.16', '5.3' ) );
        $this->assertTrue( eZExtensionPackageHandler::keepInstalled( '6.0.10', '6.0.9' ) );
        $this->assertFalse( eZExtensionPackageHandler::keepInstalled( '6.0.9', '6.0.10' ) );
        $this->assertTrue( eZExtensionPackageHandler::keepInstalled( '2.4.25', '2.3.0', 'composer' ) );
        // a package without a release number never replaces a numbered copy
        $this->assertTrue( eZExtensionPackageHandler::keepInstalled( '6.0.27', null ) );
        // an installed copy without a number: kept when managed, replaced when a plain copy
        $this->assertTrue( eZExtensionPackageHandler::keepInstalled( null, '5.3', 'git' ) );
        $this->assertTrue( eZExtensionPackageHandler::keepInstalled( null, null, 'composer' ) );
        $this->assertFalse( eZExtensionPackageHandler::keepInstalled( null, '5.3', '' ) );
        $this->assertFalse( eZExtensionPackageHandler::keepInstalled( null, null, '' ) );
    }

    public function testExtensionManager()
    {
        $root = $this->dir . '/root';
        $git = $this->extension( 'root/extension/checkout', '1.0' );
        @mkdir( $git . '/.git' );
        $this->assertSame( 'git', eZExtensionPackageHandler::extensionManager( $git, $root ) );

        $composer = $this->extension( 'root/extension/fromcomposer', '1.0' );
        $plain = $this->extension( 'root/extension/plain', '1.0' );
        @mkdir( $root . '/vendor/composer', 0777, true );
        // format 2 (Composer 2)
        file_put_contents( $root . '/vendor/composer/installed.json', json_encode( array( 'packages' => array(
            array( 'name' => 'x/fromcomposer', 'install-path' => '../../extension/fromcomposer' ),
            array( 'name' => 'x/lib', 'install-path' => '../x/lib' ) ) ) ) );
        $this->assertSame( 'composer', eZExtensionPackageHandler::extensionManager( $composer, $root ) );
        $this->assertSame( '', eZExtensionPackageHandler::extensionManager( $plain, $root ) );
        // format 1 (a list)
        file_put_contents( $root . '/vendor/composer/installed.json', json_encode( array(
            array( 'name' => 'x/fromcomposer', 'install-path' => '../../extension/fromcomposer' ) ) ) );
        $this->assertSame( 'composer', eZExtensionPackageHandler::extensionManager( $composer, $root ) );
        // no installed.json, a broken one
        unlink( $root . '/vendor/composer/installed.json' );
        $this->assertSame( '', eZExtensionPackageHandler::extensionManager( $composer, $root ) );
        file_put_contents( $root . '/vendor/composer/installed.json', '{not json' );
        $this->assertSame( '', eZExtensionPackageHandler::extensionManager( $composer, $root ) );
        $this->assertSame( '', eZExtensionPackageHandler::extensionManager( $this->dir . '/missing', $root ) );
    }

    public function testPublishedLegacyPackagesKeepTheComposerCopies()
    {
        $root = $this->dir . '/site';
        @mkdir( $root . '/vendor/composer', 0777, true );
        file_put_contents( $root . '/vendor/composer/installed.json', json_encode( array( 'packages' => array(
            array( 'name' => 'se7enxweb/ezwebin', 'install-path' => '../../extension/ezwebin' ) ) ) ) );
        // what the published 6.0.15 extension packages carry (2012) and what Composer installed
        $cases = array(
            'ezwebin'        => array( array( '6.0.27', '6.0.27' ), array( '//autogentag//', null ), '6.0.27', '"//autogentag//", not a release number' ),
            'ezstarrating'   => array( array( '6.0.16', '6.0.16' ), array( 'dev-master', null ), '6.0.16', '"dev-master", not a release number' ),
            'ezwt'           => array( array( '6.0.16', '6.0.16' ), array( '5.3', null ), '6.0.16', 'carries 5.3' ),
            'ezgmaplocation' => array( array( '6.1.2', '6.1.2' ), array( 'dev-master', null ), '6.1.2', '"dev-master", not a release number' ),
        );
        foreach ( $cases as $name => $case )
        {
            $installed = $this->extension( "site/extension/$name", $case[0][0], $case[0][1] );
            $packaged = $this->extension( "package/ezextension/$name", $case[1][0], $case[1][1] );
            $reason = eZExtensionPackageHandler::keepInstalledReason( $installed, $packaged, $root );
            $this->assertStringContainsString( 'the installed copy ' . $case[2], $reason, $name );
            $this->assertStringContainsString( $case[3], $reason, $name );
        }
        $this->assertStringContainsString( '(installed by Composer)',
            eZExtensionPackageHandler::keepInstalledReason( $this->dir . '/site/extension/ezwebin', $this->dir . '/package/ezextension/ezwebin', $root ) );

        // a newer package replaces; an unnumbered plain copy is replaced
        $old = $this->extension( 'site/extension/oldcopy', '1.0.0' );
        $new = $this->extension( 'package/ezextension/oldcopy', '1.0.1' );
        $this->assertSame( '', eZExtensionPackageHandler::keepInstalledReason( $old, $new, $root ) );
        $bare = $this->extension( 'site/extension/bare' );
        $this->assertSame( '', eZExtensionPackageHandler::keepInstalledReason( $bare, $this->extension( 'package/ezextension/bare', '5.3' ), $root ) );
    }
}
