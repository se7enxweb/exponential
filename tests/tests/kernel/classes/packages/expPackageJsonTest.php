<?php
/**
 * Tests of the JSON package format (expPackageJson, expJsonSchemaValidator, eZPackage's definition in package.json),
 * without the database: the JsonML mapping of every kind of node (namespaces, mixed content, comments, CDATA,
 * processing instructions, rich text kept as an XML string), formatting white space, refused documents, the JSON
 * Schema, a package directory converted XML -> JSON -> XML (documents canonically equal, payload byte for byte,
 * the JSON a fixed point), checksums, and package metadata.
 *
 * Files are written under var/tmp and removed in tearDown(). Reference: doc/bc/6.0/package-json-format.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expPackageJsonTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    const OBJECT_XML = '<?xml version="1.0" encoding="UTF-8"?>
<ezremote:object xmlns:ezremote="http://ez.no/ezobject" xmlns:ezobject="http://ez.no/object/" ezremote:id="1" name="A &amp; B" remote_id="r1" class_remote_id="c1" ezremote:class_identifier="article" initial_language="eng-US">
  <ezobject:version-list active_version="1">
    <ezobject:version ezremote:version="1" ezremote:status="1">
      <ezobject:object-translation language="eng-US" object_name="A &amp; B">
        <ezobject:attribute ezremote:identifier="title" type="ezstring"><text>Tom &amp; "Jerry" &lt;3</text></ezobject:attribute>
        <ezobject:attribute ezremote:identifier="space" type="ezstring"><text> </text></ezobject:attribute>
        <ezobject:attribute ezremote:identifier="body" type="ezxmltext">
          <section xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>One <strong>two</strong> <emphasize>three</emphasize></paragraph></section>
        </ezobject:attribute>
        <ezobject:attribute ezremote:identifier="data" type="ezother"><!-- a comment --><![CDATA[raw <data> & more]]><?target some data?></ezobject:attribute>
      </ezobject:object-translation>
    </ezobject:version>
  </ezobject:version-list>
</ezremote:object>
';

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        // the export directory names the current user, which registers the kernel's handlers once: before the tests
        eZPackage::temporaryExportPath();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        expPackageJson::resetCache();
        $this->dir = 'var/tmp/phpunit-package-json-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    private function roundTrip( $xml )
    {
        $dom = expPackageJson::loadXML( $xml );
        $this->assertInstanceOf( DOMDocument::class, $dom );
        $json = expPackageJson::encode( expPackageJson::envelope( $dom, 'ezcontentobject/object-r1.xml' ) );
        $back = expPackageJson::toDOM( $json, $error );
        $this->assertInstanceOf( DOMDocument::class, $back, (string)$error );
        return array( $dom, $json, $back );
    }

    public function testEveryNodeKindComesBack()
    {
        list( $dom, $json, $back ) = $this->roundTrip( self::OBJECT_XML );
        $this->assertSame( expPackageJson::canonical( $dom ), expPackageJson::canonical( $back ) );
        $this->assertSame( 'A & B', $back->documentElement->getAttribute( 'name' ) );
        $this->assertSame( 'http://ez.no/ezobject', $back->documentElement->namespaceURI );
        $this->assertSame( '1', $back->documentElement->getAttributeNS( 'http://ez.no/ezobject', 'id' ) );
        $data = json_decode( $json, true );
        $this->assertSame( 'ezremote:object', $data['root'][0] );
        $this->assertSame( 'http://ez.no/ezobject', $data['root'][1]['xmlns:ezremote'] );
        // the JSON is a fixed point
        $this->assertSame( $json, expPackageJson::encode( expPackageJson::envelope( $back, 'ezcontentobject/object-r1.xml' ) ) );
    }

    public function testRichTextIsKeptAsItsXMLString()
    {
        list( , $json ) = $this->roundTrip( self::OBJECT_XML );
        $this->assertStringContainsString( '["#xml","<section xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\"><paragraph>One <strong>two</strong> <emphasize>three</emphasize></paragraph></section>"]', $json );
    }

    public function testCommentsCDATAAndProcessingInstructions()
    {
        list( , $json, $back ) = $this->roundTrip( self::OBJECT_XML );
        $this->assertStringContainsString( '["#comment"," a comment "]', $json );
        $this->assertStringContainsString( '["#cdata","raw <data> & more"]', $json );
        $this->assertStringContainsString( '["?target","some data"]', $json );
        $this->assertStringContainsString( '<![CDATA[raw <data> & more]]>', $back->saveXML() );
    }

    public function testFormattingWhiteSpaceIsDroppedOtherWhiteSpaceKept()
    {
        list( , $json ) = $this->roundTrip( self::OBJECT_XML );
        $this->assertStringContainsString( '["text"," "]', $json );
        $this->assertStringNotContainsString( '"\n', $json );
    }

    public function testRefusedDocuments()
    {
        $envelope = array( 'format' => 'exponential-package', 'format-version' => '1.0', 'kind' => 'document' );
        $cases = array(
            'wrong format' => array( 'format' => 'other' ) + $envelope + array( 'root' => array( 'a' ) ),
            'newer major version' => array( 'format-version' => '2.0' ) + $envelope + array( 'root' => array( 'a' ) ),
            'invalid element name' => $envelope + array( 'root' => array( 'a b' ) ),
            'invalid attribute name' => $envelope + array( 'root' => array( 'a', array( 'x y' => '1' ) ) ),
            'number as text' => $envelope + array( 'root' => array( 'a', 5 ) ),
            'DOCTYPE in rich text' => $envelope + array( 'root' => array( 'a', array( '#xml', '<!DOCTYPE x [<!ENTITY e SYSTEM "file:///etc/passwd">]><x>&e;</x>' ) ) ),
            'not well-formed rich text' => $envelope + array( 'root' => array( 'a', array( '#xml', '<p>' ) ) ),
            'comment with --' => $envelope + array( 'root' => array( 'a', array( '#comment', 'x -- y' ) ) ),
        );
        foreach ( $cases as $label => $data )
            $this->assertFalse( expPackageJson::toDOM( json_encode( $data ) ), $label );
        $this->assertFalse( expPackageJson::toDOM( '{not json' ) );
    }

    public function testSchema()
    {
        $schema = expPackageJson::schema();
        $this->assertNotEmpty( $schema );
        $dom = expPackageJson::loadXML( self::OBJECT_XML );
        $data = expPackageJson::envelope( $dom, 'ezcontentobject/object-r1.xml' );
        $this->assertSame( array(), expJsonSchemaValidator::validate( $data, $schema ) );
        $bad = $data;
        $bad['unknown'] = 1;
        $this->assertNotEmpty( expJsonSchemaValidator::validate( $bad, $schema ) );
        $bad = $data;
        $bad['root'][] = array( 'a' => 'attributes after children' );
        $this->assertNotEmpty( expJsonSchemaValidator::validate( $bad, $schema ) );
        $bad = $data;
        $bad['source'] = '../outside.xml';
        $this->assertNotEmpty( expJsonSchemaValidator::validate( $bad, $schema ) );
        $bad = $data;
        $bad['checksums'] = array( 'a.png' => 'not-a-hash' );
        $this->assertNotEmpty( expJsonSchemaValidator::validate( $bad, $schema ) );
    }

    private function packageDirectory()
    {
        $source = $this->dir . '/source';
        $package = eZPackage::create( 'j1_test_package', array( 'summary' => 'JSON test', 'vendor' => 'Example Vendor' ), $this->dir . '/repo' );
        $package->setRelease( '1.0', '1', 1700000000, 'GPL-2.0-or-later', 'stable' );
        $package->appendInstall( 'ezcontentobject', false, false, true, 'contentobjects', 'ezcontentobject', array() );
        $package->setMetadata( 'expai.ai-generated', 'true' );
        $package->setMetadata( 'expai.models', 'model-a, model-b' );
        $package->storePackageFile( $source, false );
        mkdir( "$source/ezcontentobject", 0775, true );
        file_put_contents( "$source/ezcontentobject/contentobjects.xml", '<?xml version="1.0"?><content-object><object-files-list><object-file filename="object-r1.xml"/></object-files-list></content-object>' );
        file_put_contents( "$source/ezcontentobject/object-r1.xml", self::OBJECT_XML );
        mkdir( "$source/simplefiles", 0775, true );
        file_put_contents( "$source/simplefiles/a1b2.png", "\x89PNG binary payload" );
        file_put_contents( "$source/notes.xml", '<payload>an XML file that is not a document of the package</payload>' );
        return $source;
    }

    public function testPackageDirectoryRoundTrip()
    {
        $source = $this->packageDirectory();
        $json = $this->dir . '/json';
        $xml = $this->dir . '/xml';
        $json2 = $this->dir . '/json2';
        $this->assertSame( array( 'package.xml', 'ezcontentobject/contentobjects.xml', 'ezcontentobject/object-r1.xml' ), expPackageJson::modelDocuments( $source ) );
        $report = expPackageJson::convertDirectory( $source, $json, 'json' );
        $this->assertTrue( $report['ok'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 'json', expPackageJson::directoryFormat( $json ) );
        $this->assertFileExists( "$json/ezcontentobject/object-r1.json" );
        $this->assertFileDoesNotExist( "$json/ezcontentobject/object-r1.xml" );
        $this->assertFileEquals( "$source/notes.xml", "$json/notes.xml", 'a payload XML file is not converted' );
        $validation = expPackageJson::validateDirectory( $json );
        $this->assertTrue( $validation['valid'], implode( '; ', $validation['errors'] ) );
        $this->assertSame( 'j1_test_package', $validation['name'] );

        $this->assertTrue( expPackageJson::convertDirectory( $json, $xml, 'xml' )['ok'] );
        $this->assertTrue( expPackageJson::convertDirectory( $xml, $json2, 'json' )['ok'] );
        foreach ( expPackageJson::modelDocuments( $source ) as $doc )
        {
            $this->assertSame( expPackageJson::canonical( expPackageJson::documentDOM( $source, $doc ) ),
                               expPackageJson::canonical( expPackageJson::documentDOM( $xml, $doc ) ), $doc );
            $this->assertFileEquals( expPackageJson::jsonName( "$json/$doc" ), expPackageJson::jsonName( "$json2/$doc" ), "$doc: JSON is a fixed point" );
        }
        $this->assertFileEquals( "$source/simplefiles/a1b2.png", "$xml/simplefiles/a1b2.png" );

        // the handlers read the JSON documents by their XML names
        $dom = eZPackage::fetchDOMFromFile( "$json/ezcontentobject/object-r1.xml" );
        $this->assertInstanceOf( DOMDocument::class, $dom );
        $this->assertSame( 'r1', $dom->documentElement->getAttribute( 'remote_id' ) );
    }

    public function testChecksums()
    {
        $json = $this->dir . '/json';
        expPackageJson::convertDirectory( $this->packageDirectory(), $json, 'json' );
        $data = json_decode( file_get_contents( "$json/package.json" ), true );
        $this->assertSame( array( 'ezcontentobject/contentobjects.json', 'ezcontentobject/object-r1.json', 'notes.xml', 'simplefiles/a1b2.png' ), array_keys( $data['checksums'] ) );
        file_put_contents( "$json/simplefiles/a1b2.png", 'changed' );
        $validation = expPackageJson::validateDirectory( $json );
        $this->assertFalse( $validation['valid'] );
        $this->assertContains( 'checksums: simplefiles/a1b2.png does not match', $validation['errors'] );
    }

    public function testDefinitionInJSONAndMetadata()
    {
        $json = $this->dir . '/json';
        expPackageJson::convertDirectory( $this->packageDirectory(), $json, 'json' );
        $package = eZPackage::fetchFromFile( "$json/package.json" );
        $this->assertInstanceOf( eZPackage::class, $package );
        $this->assertSame( 'json', $package->attribute( 'format' ) );
        $this->assertSame( 'j1_test_package', $package->attribute( 'name' ) );
        $this->assertSame( array( 'expai.ai-generated' => 'true', 'expai.models' => 'model-a, model-b' ), $package->attribute( 'metadata' ) );
        $this->assertFalse( $package->setMetadata( 'bad key', 'x' ) );
        // stored again in its own format
        $package->storePackageFile( $this->dir . '/again', false );
        $this->assertFileExists( $this->dir . '/again/package.json' );
        $this->assertFileDoesNotExist( $this->dir . '/again/package.xml' );
        $this->assertSame( 'j1_test_package-1.0-1.json.ezpkg', $package->exportName() );
        $this->assertSame( 'j1_test_package-1.0-1.ezpkg', $package->exportName( 'xml' ) );
        // a package without metadata is written as before: no <metadata>
        $plain = eZPackage::create( 'j1_plain', array(), $this->dir . '/repo' );
        $this->assertStringNotContainsString( '<metadata', $plain->toString() );
    }

    public function testArchiveConversion()
    {
        $source = $this->packageDirectory();
        $archive = $this->dir . '/classic.ezpkg';
        $this->assertSame( $archive, eZPackage::archiveDirectory( $source, $archive, 'tar.gz' ) );
        $report = expPackageJson::convert( $archive, $this->dir . '/converted.ezpkg' );
        $this->assertTrue( $report['ok'], implode( '; ', $report['errors'] ) );
        $this->assertSame( 'json', $report['to'] );
        $this->assertTrue( expPackageJson::extractArchive( $this->dir . '/converted.ezpkg', $this->dir . '/x', $error ), (string)$error );
        $this->assertSame( 'json', expPackageJson::directoryFormat( $this->dir . '/x' ) );
        $this->assertTrue( expPackageJson::validateDirectory( $this->dir . '/x' )['valid'] );
    }
}
