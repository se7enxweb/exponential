<?php
/**
 * The XML of a package is read without network access: eZPackage::fetchDOMFromFile() and the three
 * readers of eZPackageFileBrowser pass LIBXML_NONET, so a DTD or entity named in an uploaded package is never
 * fetched from another host, like expPackageJson::loadXML() and eZPackageUploadInspector already did.
 *
 *  NONET-01  every load()/loadXML() in ezpackage.php and ezpackagefilebrowser.php passes LIBXML_NONET
 *  NONET-02  a package.xml is still read, also one with a DOCTYPE naming a DTD on another host
 *  NONET-03  the file browser still pretty prints, shows and summarises package XML
 *
 * The files are made under var/tmp and removed in tearDown().
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageXMLNoNetworkTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = 'var/tmp/phpunit-packagenonet-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0777, true );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        @rmdir( $this->dir );
    }

    /** NONET-01 */
    public function testEveryLoadPassesNoNet()
    {
        $root = dirname( __DIR__, 5 );
        foreach ( array( 'kernel/classes/ezpackage.php' => 1, 'kernel/classes/ezpackagefilebrowser.php' => 3 ) as $file => $expected )
        {
            $code = (string)file_get_contents( "$root/$file" );
            preg_match_all( '/->load(?:XML)?\(([^;]*)\)/', $code, $calls );
            $this->assertCount( $expected, $calls[0], $file );
            foreach ( $calls[1] as $arguments )
                $this->assertStringContainsString( 'LIBXML_NONET', $arguments, $file );
        }
    }

    /** NONET-02 */
    public function testAPackageXMLIsStillRead()
    {
        file_put_contents( "$this->dir/package.xml", '<?xml version="1.0"?><package version="1"><name>nonet</name></package>' );
        $dom = eZPackage::fetchDOMFromFile( "$this->dir/package.xml" );
        $this->assertInstanceOf( 'DOMDocument', $dom );
        $this->assertSame( 'nonet', $dom->getElementsByTagName( 'name' )->item( 0 )->textContent );

        // a DOCTYPE naming a DTD elsewhere: read, the DTD not fetched (an unroutable address would fail or hang)
        file_put_contents( "$this->dir/doctype.xml", '<?xml version="1.0"?><!DOCTYPE package SYSTEM "http://192.0.2.1/package.dtd"><package version="1"><name>dtd</name></package>' );
        $dom = eZPackage::fetchDOMFromFile( "$this->dir/doctype.xml" );
        $this->assertInstanceOf( 'DOMDocument', $dom );
        $this->assertSame( 'dtd', $dom->getElementsByTagName( 'name' )->item( 0 )->textContent );
    }

    /** NONET-03 */
    public function testTheFileBrowserStillReadsPackageXML()
    {
        $this->assertStringContainsString( "<b>x</b>", eZPackageFileBrowser::prettyPrintXML( '<a><b>x</b></a>' ) );
        $this->assertSame( 'not xml', eZPackageFileBrowser::prettyPrintXML( 'not xml' ) );
        $text = eZPackageFileBrowser::documentText( '<package><name>n</name></package>', 'package.xml', 'xml' );
        $this->assertSame( '', $text['error'] );
        $this->assertStringContainsString( '<name>n</name>', $text['text'] );
        $summary = eZPackageFileBrowser::objectItemSummary( '<ezremote:object xmlns:ezremote="http://ez.no/ezobject" name="Single"/>' );
        $this->assertSame( 'Single', $summary['name'] );
        $this->assertNull( eZPackageFileBrowser::objectItemSummary( 'not xml' ) );
    }
}
