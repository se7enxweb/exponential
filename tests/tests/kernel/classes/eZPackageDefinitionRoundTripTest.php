<?php
/**
 * A package read from its package.xml and written again (eZPackage::domStructure(), which store() and
 * exportToArchive() use) keeps what a site package needs:
 *   - <settings><settings-file filename=""/></settings>, the install scripts CreateSites runs
 *   - every attribute of a dependency (min-version, max-version), not only type, name and value
 * exportToArchive() also takes the side files at the top of a package (eZPackage::extraTopLevelEntries()),
 * such as cjw_multisite_democontent's cjw-*.json, which an export dropped before 6.0.15.
 * And the setup's install order of a site package's requirements, down the whole chain
 * (eZStepCreateSites::requirementsInInstallOrder()). No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZPackageDefinitionRoundTripTest extends PHPUnit\Framework\TestCase
{
    private $tempDir;

    protected function tearDown(): void
    {
        if ( $this->tempDir && is_dir( $this->tempDir ) )
            eZDir::recursiveDelete( $this->tempDir );
    }

    private static function sitePackageXML()
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<package version="6.0.15-0" development="false" is_active="true" install_type="import">
  <name>example_site</name>
  <summary>Example site</summary>
  <description>A site package.</description>
  <vendor>7x</vendor>
  <type value="site" />
  <version>
    <number>6.0</number>
    <release>15-stable</release>
  </version>
  <licence>GPL</licence>
  <state>stable</state>
  <dependencies>
    <provides />
    <requires>
      <require type="ezpackage" name="example_classes" min-version="5.1" />
      <require type="ezpackage" name="example_democontent" min-version="6.0" max-version="7.0" />
    </requires>
    <obsoletes />
    <conflicts />
  </dependencies>
  <settings>
    <settings-file filename="ini-site.php"/>
    <settings-file filename="example-installer.php"/>
  </settings>
  <install />
  <uninstall />
</package>
XML;
    }

    private static function parse( $xml )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->loadXML( $xml );
        $package = new eZPackage( array(), '/nonexistent-repository' );
        $package->parseDOMTree( $dom );
        return $package;
    }

    public function testSettingsFilesAndMinVersionSurviveARoundTrip()
    {
        $package = self::parse( self::sitePackageXML() );
        $this->assertSame( array( 'ini-site.php', 'example-installer.php' ), $package->attribute( 'settings-files' ) );

        $dom = $package->domStructure();
        $xpath = new DOMXPath( $dom );
        $this->assertSame( 2, $xpath->query( '/package/settings/settings-file' )->length );
        $this->assertSame( 'example-installer.php', $xpath->query( '/package/settings/settings-file' )->item( 1 )->getAttribute( 'filename' ) );
        $this->assertSame( '5.1', $xpath->query( '//requires/require[@name="example_classes"]' )->item( 0 )->getAttribute( 'min-version' ) );
        $this->assertSame( '6.0', $xpath->query( '//requires/require[@name="example_democontent"]' )->item( 0 )->getAttribute( 'min-version' ) );
        $this->assertSame( '7.0', $xpath->query( '//requires/require[@name="example_democontent"]' )->item( 0 )->getAttribute( 'max-version' ) );

        // and read again, the same
        $again = self::parse( $dom->saveXML() );
        $this->assertSame( $package->attribute( 'settings-files' ), $again->attribute( 'settings-files' ) );
        $dependencies = $again->attribute( 'dependencies' );
        $this->assertSame( '5.1', $dependencies['requires'][0]['min-version'] );
        $this->assertSame( '6.0', $dependencies['requires'][1]['min-version'] );
    }

    public function testPackageWithoutSettingsWritesNone()
    {
        $xml = preg_replace( '#<settings>.*</settings>#s', '', self::sitePackageXML() );
        $dom = self::parse( $xml )->domStructure();
        $this->assertSame( 0, $dom->getElementsByTagName( 'settings' )->length );
    }

    public function testExtraTopLevelEntriesAreTheSideFiles()
    {
        $this->tempDir = dirname( __DIR__, 4 ) . '/var/tmp/package-roundtrip-test-' . getmypid();
        $dir = $this->tempDir . '/example_democontent';
        foreach ( array( 'documents', 'settings', 'ezcontentobject', 'extra-dir', '.git' ) as $sub )
            mkdir( $dir . '/' . $sub, 0777, true );
        foreach ( array( 'package.xml', 'package.json', 'cjw-paths.json', 'cjw-class-additions.xml', '.hidden' ) as $file )
            file_put_contents( $dir . '/' . $file, 'x' );
        $copied = array( 'documents', 'files', 'simplefiles', 'settings', 'ezcontentobject' );
        $this->assertSame( array( 'cjw-class-additions.xml', 'cjw-paths.json', 'extra-dir' ),
                           eZPackage::extraTopLevelEntries( $dir, $copied ) );
        $this->assertSame( array(), eZPackage::extraTopLevelEntries( $this->tempDir . '/missing', $copied ) );
    }

    public function testExportCopiesTheExtraEntries()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/classes/ezpackage.php' );
        $this->assertStringContainsString( 'self::extraTopLevelEntries( $path, $directoryList )', $source );
    }

    /**
     * site -> classes, democontent; democontent -> classes, media; media -> classes; a cycle back to the site.
     */
    public function testRequirementsInInstallOrderFollowTheWholeChain()
    {
        $packages = array(
            'site' => array( array( 'type' => 'ezpackage', 'name' => 'democontent', 'min-version' => '1.0' ),
                             array( 'type' => 'ezpackage', 'name' => 'classes' ),
                             array( 'type' => 'ezextension', 'name' => 'not-a-package' ) ),
            'democontent' => array( array( 'type' => 'ezpackage', 'name' => 'classes' ),
                                    array( 'type' => 'ezpackage', 'name' => 'media' ) ),
            'media' => array( array( 'type' => 'ezpackage', 'name' => 'classes' ),
                              array( 'type' => 'ezpackage', 'name' => 'site' ) ),
            'classes' => array(),
        );
        $make = function( $name ) use ( &$packages )
        {
            if ( !isset( $packages[$name] ) )
                return false;
            return new class( $name, $packages[$name] ) {
                private $name;
                private $requires;
                public function __construct( $name, $requires ) { $this->name = $name; $this->requires = $requires; }
                public function attribute( $key )
                {
                    return $key === 'name' ? $this->name : array( 'requires' => $this->requires );
                }
            };
        };
        $order = eZStepCreateSites::requirementsInInstallOrder( $make( 'site' ), $make );
        $this->assertSame( array( 'classes', 'media', 'democontent' ), array_column( $order, 'name' ) );
        $this->assertSame( '1.0', $order[2]['min-version'] );

        // a requirement that is not there stays in the list, so the caller can report it
        $packages['site'][] = array( 'type' => 'ezpackage', 'name' => 'missing' );
        $order = eZStepCreateSites::requirementsInInstallOrder( $make( 'site' ), $make );
        $this->assertSame( 'missing', end( $order )['name'] );
    }
}
