<?php
/**
 * Tests of eZPackageFileBrowser, the package contents browser of package/view/full: the files of a package
 * directory (hidden bookkeeping files left out), their kinds (by directory, and for an XML item by its root
 * element), the path check that keeps a requested file inside the package (no "..", no absolute path, no symlink
 * out), filtering and paging, pretty printing and the summary of a content object item.
 *
 * The package directory is made under var/tmp and removed in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageFileBrowserTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $package;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = 'var/tmp/phpunit-packagebrowser-' . getmypid() . '-' . mt_rand();
        $files = array(
            'package.xml' => '<?xml version="1.0"?><package version="1"><name>k1_browse</name></package>',
            'ezcontentclass/article.xml' => "\xEF\xBB\xBF<content-class><identifier>article</identifier></content-class>",
            'ezcontentobject/objects.xml' => '<object-list><object name="One"/></object-list>',
            'ezcontentobject/one.xml' => '<ezremote:object xmlns:ezremote="http://ez.no/ezobject" name="Single"/>',
            'ezcontentobject/notes.xml' => '<notes/>',
            'images/logo.png' => 'png',
            'simplefiles/abc.txt' => 'simple',
            'documents/README' => 'readme',
            'other/data.bin' => 'bin',
            '.cache/package.php' => '<?php',
            'ezfile/.hidden/x.txt' => 'hidden',
        );
        foreach ( $files as $path => $content )
        {
            $full = "$this->dir/k1_browse/$path";
            if ( !is_dir( dirname( $full ) ) )
                mkdir( dirname( $full ), 0775, true );
            file_put_contents( $full, $content );
        }
        file_put_contents( "$this->dir/outside.txt", 'secret' );
        $this->package = eZPackage::create( 'k1_browse', array(), $this->dir );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    public function testAllFilesLeavesOutHiddenFiles()
    {
        $paths = array_column( eZPackageFileBrowser::allFiles( $this->package ), 'path' );
        $this->assertSame( array( 'documents/README', 'ezcontentclass/article.xml', 'ezcontentobject/notes.xml', 'ezcontentobject/objects.xml',
                                  'ezcontentobject/one.xml', 'images/logo.png', 'other/data.bin', 'package.xml', 'simplefiles/abc.txt' ), $paths );
        $this->assertSame( array(), eZPackageFileBrowser::allFiles( eZPackage::create( 'k1_missing', array(), $this->dir ) ) );
    }

    public static function kindProvider()
    {
        return array(
            array( 'images/a/b.png', 'image' ), array( 'simplefiles/x.jpg', 'simplefile' ), array( 'documents/LICENCE', 'document' ),
            array( 'data/file.bin', 'other' ), array( 'README', 'other' ), array( 'x/item.XML', 'unknown-xml' ), array( 'package.xml', 'unknown-xml' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('kindProvider')]
    public function testFileKind( $path, $kind )
    {
        $this->assertSame( $kind, eZPackageFileBrowser::fileKind( $path ) );
    }

    public function testXmlKindsAreReadFromTheRootElement()
    {
        $base = "$this->dir/k1_browse";
        $this->assertSame( 'class', eZPackageFileBrowser::peekXMLKind( "$base/ezcontentclass/article.xml" ), 'a byte order mark is skipped' );
        $this->assertSame( 'object', eZPackageFileBrowser::peekXMLKind( "$base/ezcontentobject/objects.xml" ) );
        $this->assertSame( 'object', eZPackageFileBrowser::peekXMLKind( "$base/ezcontentobject/one.xml" ), 'a namespace prefix' );
        $this->assertSame( 'package', eZPackageFileBrowser::peekXMLKind( "$base/package.xml" ) );
        $this->assertSame( 'other', eZPackageFileBrowser::peekXMLKind( "$base/ezcontentobject/notes.xml" ) );
        $this->assertSame( 'other', @eZPackageFileBrowser::peekXMLKind( "$base/missing.xml" ) );
    }

    public function testFilePathStaysInsideThePackage()
    {
        $base = realpath( "$this->dir/k1_browse" );
        $this->assertSame( "$base/images/logo.png", eZPackageFileBrowser::filePath( $this->package, 'images/logo.png' ) );
        $this->assertSame( "$base/images/logo.png", eZPackageFileBrowser::filePath( $this->package, '/images/logo.png' ), 'a leading slash is relative to the package' );
        foreach ( array( '', '../outside.txt', 'images/../../outside.txt', '..', "images/logo.png\0x", 'images', 'missing.txt' ) as $path )
            $this->assertFalse( eZPackageFileBrowser::filePath( $this->package, $path ), $path );
        $this->assertFalse( eZPackageFileBrowser::filePath( eZPackage::create( 'k1_missing', array(), $this->dir ), 'package.xml' ) );
    }

    public function testASymlinkOutOfThePackageIsRefused()
    {
        if ( !@symlink( realpath( "$this->dir/outside.txt" ), "$this->dir/k1_browse/link.txt" ) )
            $this->markTestSkipped( 'no symlinks here' );
        $this->assertFalse( eZPackageFileBrowser::filePath( $this->package, 'link.txt' ) );
    }

    public function testFilteredPage()
    {
        $page = eZPackageFileBrowser::filteredPage( $this->package, array( 'limit' => 4 ) );
        $this->assertSame( 9, $page['total_all'] );
        $this->assertSame( 3, $page['pages'] );
        $this->assertCount( 4, $page['files'] );
        $this->assertSame( 'document', $page['files'][0]['kind'] );
        $this->assertSame( 'class', $page['files'][1]['kind'], 'XML kinds are resolved' );

        $objects = eZPackageFileBrowser::filteredPage( $this->package, array( 'type' => 'object' ) );
        $this->assertSame( array( 'ezcontentobject/objects.xml', 'ezcontentobject/one.xml' ), array_column( $objects['files'], 'path' ) );
        $this->assertSame( array( 3, 4 ), array_column( $objects['files'], 'index' ) );

        $search = eZPackageFileBrowser::filteredPage( $this->package, array( 'search' => 'LOGO' ) );
        $this->assertSame( array( 'images/logo.png' ), array_column( $search['files'], 'path' ) );
    }

    public function testFilteredPageOffsets()
    {
        $last = eZPackageFileBrowser::filteredPage( $this->package, array( 'limit' => 4, 'offset' => 'last' ) );
        $this->assertSame( 8, $last['offset'] );
        $this->assertSame( 3, $last['page'] );
        $this->assertSame( 8, eZPackageFileBrowser::filteredPage( $this->package, array( 'limit' => 4, 'offset' => 100 ) )['offset'] );
        $all = eZPackageFileBrowser::filteredPage( $this->package, array( 'limit' => 'all', 'offset' => 5 ) );
        $this->assertCount( 9, $all['files'] );
        $this->assertSame( 0, $all['offset'] );
        $this->assertSame( 1, $all['pages'] );
        $none = eZPackageFileBrowser::filteredPage( $this->package, array( 'type' => 'nothing', 'offset' => 'last' ) );
        $this->assertSame( array( 0, 1, 1 ), array( $none['offset'], $none['page'], $none['pages'] ) );
    }

    public function testPrettyPrintXML()
    {
        $this->assertSame( "<?xml version=\"1.0\"?>\n<a>\n  <b>1</b>\n</a>\n", eZPackageFileBrowser::prettyPrintXML( '<a><b>1</b></a>' ) );
        $this->assertSame( 'not <xml', eZPackageFileBrowser::prettyPrintXML( 'not <xml' ) );
    }

    public function testObjectItemSummary()
    {
        $long = str_repeat( 'x', 450 );
        $xml = '<object-list xmlns:ezobject="http://ez.no/ezobject" xmlns:ezremote="http://ez.no/ezobject">'
             . '<ezremote:object name="First" remote_id="r1" ezobject:class_identifier="article" ezobject:modified="123">'
             . '<ezremote:version-list><version><object-translation language="eng-GB">'
             . '<attribute xmlns="http://ez.no/object/" type="ezstring" ezobject:identifier="title">  Hello  </attribute>'
             . '<attribute xmlns="http://ez.no/object/" type="eztext" ezobject:identifier="body">' . $long . '</attribute>'
             . '<attribute xmlns="http://ez.no/object/" type="eztext">no identifier</attribute>'
             . '</object-translation></version></ezremote:version-list></ezremote:object>'
             . '<object name="Second"/></object-list>';
        $summary = eZPackageFileBrowser::objectItemSummary( $xml );
        $this->assertSame( 'First', $summary['name'] );
        $this->assertSame( 'r1', $summary['remote_id'] );
        $this->assertSame( 'article', $summary['class_identifier'] );
        $this->assertSame( '123', $summary['modified'] );
        $this->assertSame( 1, $summary['more_objects'] );
        $this->assertSame( array( 'title', 'body' ), array_keys( $summary['translations']['eng-GB'] ) );
        $this->assertSame( array( 'type' => 'ezstring', 'text' => 'Hello' ), $summary['translations']['eng-GB']['title'] );
        $this->assertSame( 401, mb_strlen( $summary['translations']['eng-GB']['body']['text'] ), 'long texts are cut with an ellipsis' );
    }

    public function testObjectItemSummaryOfOtherFiles()
    {
        $this->assertNull( eZPackageFileBrowser::objectItemSummary( '<content-class/>' ) );
        $this->assertNull( eZPackageFileBrowser::objectItemSummary( 'no xml' ) );
        $this->assertNull( eZPackageFileBrowser::objectItemSummary( '<object-list/>' ) );
        $summary = eZPackageFileBrowser::objectItemSummary( '<object name="Bare"/>' );
        $this->assertSame( 'Bare', $summary['name'] );
        $this->assertSame( array(), $summary['translations'] );
        $this->assertSame( 0, $summary['more_objects'] );
    }

    public function testLocalNameHelpers()
    {
        $dom = new DOMDocument();
        $dom->loadXML( '<r xmlns:p="urn:x"><p:a/><b><a/></b></r>' );
        $this->assertSame( 2, eZPackageFileBrowser::countByLocalName( $dom->documentElement, 'a' ) );
        $this->assertSame( 'p:a', eZPackageFileBrowser::firstByLocalName( $dom->documentElement, 'a' )->tagName );
        $this->assertNull( eZPackageFileBrowser::firstByLocalName( $dom->documentElement, 'z' ) );
    }
}
