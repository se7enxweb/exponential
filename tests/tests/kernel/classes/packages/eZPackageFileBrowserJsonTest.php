<?php
/**
 * Tests of the package contents browser for the JSON format and for files that are not text: the kinds of JSON
 * documents (by the element their "root" mirrors), a document shown in either format (XML and JSON are 1:1), what
 * a file is by its content (an image only when its bytes say so; SVG and binaries never inline), the checksum state
 * of a file against package.json, and the in-place conversion of a stored package (the package wizard's format
 * choice).
 *
 * The package directories are made under var/tmp and removed in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZPackageFileBrowserJsonTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = 'var/tmp/phpunit-packagebrowserjson-' . getmypid() . '-' . mt_rand();
        $xml = array(
            'package.xml' => '<?xml version="1.0"?><package version="1"><name>k379_json</name><install><item type="ezcontentobject" name="contentobjects" filename="contentobjects" sub-directory="ezcontentobject"/></install></package>',
            'ezcontentobject/contentobjects.xml' => '<?xml version="1.0"?><content-object><object-list><object name="One" remote_id="r1"/></object-list></content-object>',
        );
        foreach ( $xml as $path => $content )
        {
            $full = "$this->dir/xml/k379_json/$path";
            if ( !is_dir( dirname( $full ) ) )
                mkdir( dirname( $full ), 0775, true );
            file_put_contents( $full, $content );
        }
        $png = "$this->dir/xml/k379_json/simplefiles/a.png";
        mkdir( dirname( $png ), 0775, true );
        $im = imagecreatetruecolor( 4, 3 );
        imagepng( $im, $png );
        file_put_contents( "$this->dir/xml/k379_json/simplefiles/fake.png", "not an image\n" );
        file_put_contents( "$this->dir/xml/k379_json/simplefiles/x.svg", '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>' );
        file_put_contents( "$this->dir/xml/k379_json/simplefiles/data.bin", "\x00\x01\x02\xff binary" );
        file_put_contents( "$this->dir/xml/k379_json/simplefiles/notes.txt", "Ünïcode notes\n" );
        $report = expPackageJson::convertDirectory( "$this->dir/xml/k379_json", "$this->dir/json/k379_json", 'json' );
        $this->assertTrue( $report['ok'], implode( '; ', $report['errors'] ) );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    public function testJsonDocumentsHaveTheKindOfTheElementTheyMirror()
    {
        $package = eZPackage::create( 'k379_json', array(), "$this->dir/json" );
        $kinds = array();
        foreach ( eZPackageFileBrowser::allFiles( $package ) as $row )
            $kinds[$row['path']] = eZPackageFileBrowser::resolvedKind( $package, $row );
        $this->assertSame( 'package', $kinds['package.json'] );
        $this->assertSame( 'object', $kinds['ezcontentobject/contentobjects.json'] );
        $this->assertSame( 'simplefile', $kinds['simplefiles/a.png'] );
        $this->assertNotContains( 'unknown-json', $kinds );
    }

    public function testADocumentIsShownInEitherFormat()
    {
        $json = (string)file_get_contents( "$this->dir/json/k379_json/ezcontentobject/contentobjects.json" );
        $native = eZPackageFileBrowser::documentText( $json, 'ezcontentobject/contentobjects.json', '' );
        $this->assertSame( 'json', $native['format'] );
        $this->assertSame( $json, $native['text'] );
        $asXML = eZPackageFileBrowser::documentText( $json, 'ezcontentobject/contentobjects.json', 'xml' );
        $this->assertSame( 'xml', $asXML['format'] );
        $this->assertStringContainsString( '<object name="One" remote_id="r1"', $asXML['text'] );
        $xml = (string)file_get_contents( "$this->dir/xml/k379_json/ezcontentobject/contentobjects.xml" );
        $asJSON = eZPackageFileBrowser::documentText( $xml, 'ezcontentobject/contentobjects.xml', 'json' );
        $this->assertSame( 'json', $asJSON['format'] );
        $this->assertStringContainsString( '"source": "ezcontentobject/contentobjects.xml"', $asJSON['text'] );
        $broken = eZPackageFileBrowser::documentText( '{not json', 'x.json', 'xml' );
        $this->assertNotSame( '', $broken['error'] );
        $this->assertStringContainsString( '<object', eZPackageFileBrowser::documentXML( $json, 'ezcontentobject/contentobjects.json' ) );
    }

    public function testWhatAFileIsComesFromItsContent()
    {
        $base = "$this->dir/json/k379_json/simplefiles";
        $png = eZPackageFileBrowser::sniff( "$base/a.png" );
        $this->assertSame( array( 'image', 'image/png', true ), array( $png['media'], $png['mime'], $png['inline'] ) );
        $this->assertSame( 'text', eZPackageFileBrowser::sniff( "$base/fake.png" )['media'], 'a .png name alone is no image' );
        $svg = eZPackageFileBrowser::sniff( "$base/x.svg" );
        $this->assertSame( array( 'svg', false ), array( $svg['media'], $svg['inline'] ) );
        $this->assertSame( 'binary', eZPackageFileBrowser::sniff( "$base/data.bin" )['media'] );
        $this->assertSame( 'text', eZPackageFileBrowser::sniff( "$base/notes.txt" )['media'] );
    }

    public function testChecksumState()
    {
        $package = eZPackage::create( 'k379_json', array(), "$this->dir/json" );
        $path = "$this->dir/json/k379_json/simplefiles/notes.txt";
        $this->assertSame( 'match', eZPackageFileBrowser::checksumState( $package, 'simplefiles/notes.txt', hash_file( 'sha256', $path ) ) );
        $this->assertSame( 'mismatch', eZPackageFileBrowser::checksumState( $package, 'simplefiles/notes.txt', hash( 'sha256', 'changed' ) ) );
        $this->assertSame( 'none', eZPackageFileBrowser::checksumState( $package, 'simplefiles/unlisted', 'x' ) );
        $classic = eZPackage::create( 'k379_json', array(), "$this->dir/xml" );
        $this->assertSame( 'none', eZPackageFileBrowser::checksumState( $classic, 'simplefiles/notes.txt', hash_file( 'sha256', $path ) ) );
    }

    public function testConvertInPlace()
    {
        $dir = "$this->dir/xml/k379_json";
        $report = expPackageJson::convertInPlace( $dir, 'json' );
        $this->assertTrue( $report['ok'], implode( '; ', $report['errors'] ) );
        $this->assertFileExists( "$dir/package.json" );
        $this->assertFileDoesNotExist( "$dir/package.xml" );
        $this->assertSame( 'json', expPackageJson::directoryFormat( $dir ) );
        $this->assertTrue( expPackageJson::validateDirectory( $dir )['valid'] );
        $this->assertSame( array(), glob( "$dir.converting-*" ) );
        $this->assertTrue( expPackageJson::convertInPlace( $dir, 'json' )['ok'], 'already in the format: nothing to do' );
    }
}
