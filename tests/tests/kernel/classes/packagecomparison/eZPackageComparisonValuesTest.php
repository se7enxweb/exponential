<?php
/**
 * Tests of the parts of eZPackageComparison that need no database: how an attribute value of a package object
 * (or of the site's object, serialized the same way) is reduced to what is compared and shown, for each kind of
 * datatype; equality of values and of files; the XML helpers (formatting white space, empty elements, the text of
 * an ezxmltext, canonical and indented XML); serialized name lists; reading a class definition; comparing two
 * objects (values per language, translations on one side only, placement, class) and two classes; and filtering,
 * sorting and paging the index.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageComparisonValuesTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    private static function element( $xml )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->loadXML( $xml );
        return $dom->documentElement;
    }

    private static function value( $datatype, $xml, $fileInfo = null )
    {
        return eZPackageComparison::normalizeValue( $datatype, self::element( $xml ), $fileInfo ?: function ( $key ) { return null; } );
    }

    // ------------------------------------------------------------- values

    public function testPlainValueIsComparedByItsText()
    {
        $value = self::value( 'ezstring', "<attribute>\n  <text>Hello</text>\n</attribute>" );
        $this->assertSame( 'text', $value['kind'] );
        $this->assertSame( 'Hello', $value['text'] );
        $this->assertSame( 'text=Hello', $value['compare'] );
    }

    public function testSeveralPlainElementsAreLines()
    {
        $value = self::value( 'ezprice', '<attribute><price>10</price><vat>2</vat></attribute>' );
        $this->assertSame( "price: 10\nvat: 2", $value['text'] );
        $this->assertSame( $value['text'], $value['compare'] );
    }

    public function testNoChildrenIsEmpty()
    {
        $this->assertSame( 'empty', self::value( 'ezinteger', "<attribute>\n</attribute>" )['kind'] );
    }

    public function testOtherValuesAreComparedAsCanonicalXMLWithoutEmptyElements()
    {
        $one = self::value( 'ezmatrix', '<attribute><ezmatrix><columns number="1"/><new-field/></ezmatrix></attribute>' );
        $two = self::value( 'ezmatrix', "<attribute>\n  <ezmatrix>\n    <columns number=\"1\" />\n  </ezmatrix>\n</attribute>" );
        $this->assertSame( 'xml', $one['kind'] );
        $this->assertSame( 'serialized', $one['note'] );
        $this->assertSame( $two['compare'], $one['compare'], 'formatting and a new empty element are no difference' );
        $this->assertTrue( eZPackageComparison::valuesEqual( $one, $two ) );
        $this->assertStringContainsString( '<columns number="1"/>', $one['text'] );
    }

    public function testXmlText()
    {
        $xml = '<attribute><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/"><paragraph>First <strong>bold</strong> text.</paragraph>'
             . '<paragraph><embed object_remote_id="abc"/></paragraph><table><tr><td>a</td><td>b</td></tr></table></section></attribute>';
        $value = self::value( 'ezxmltext', $xml );
        $this->assertSame( 'xmltext', $value['kind'] );
        $this->assertSame( "First bold text.\n\n[embed abc]\n\na\tb", $value['text'] );
        $this->assertStringContainsString( '<strong>bold</strong>', $value['compare'] );
        $this->assertStringContainsString( '<paragraph>', $value['raw'] );
    }

    public function testEmptyXmlTextIsNoText()
    {
        foreach ( array( '<attribute/>', '<attribute><section/></attribute>', "<attribute><section>\n </section></attribute>" ) as $xml )
        {
            $value = self::value( 'ezxmltext', $xml );
            $this->assertSame( '', $value['compare'], $xml );
            $this->assertSame( 'xmltext', $value['kind'] );
        }
    }

    public function testUrl()
    {
        $value = self::value( 'ezurl', '<attribute><url>https%3A%2F%2Fexample.invalid%2Fa%20b</url><text>Example</text></attribute>' );
        $this->assertSame( "https://example.invalid/a b\nExample", $value['compare'] );
        $this->assertSame( "https://example.invalid/a b\nExample", $value['text'] );
        $this->assertSame( 'https://example.invalid', eZPackageComparison::urlValue( 'https://example.invalid', '' )['text'] );
    }

    public function testKeywordsAreComparedInAnyOrder()
    {
        $one = eZPackageComparison::keywordValue( 'beta, alpha, , gamma' );
        $two = self::value( 'ezkeyword', '<attribute><keyword-string>gamma,alpha,beta,alpha</keyword-string></attribute>' );
        $this->assertSame( 'beta, alpha, gamma', $one['text'] );
        $this->assertSame( $one['compare'], $two['compare'] );
    }

    public function testRelationListIsOrderedByPriority()
    {
        $xml = '<attribute><related-objects><relation-list>'
             . '<relation-item priority="2" contentobject-remote-id="second"/>'
             . '<relation-item priority="1" contentobject-remote-id="first"/>'
             . '<relation-item priority="2" contentobject-remote-id="third"/>'
             . '<relation-item priority="0" contentobject-remote-id=""/>'
             . '</relation-list></related-objects></attribute>';
        $value = self::value( 'ezobjectrelationlist', $xml );
        $this->assertSame( 'relation', $value['kind'] );
        $this->assertSame( array( 'first', 'second', 'third' ), $value['ids'] );
        $this->assertSame( "first\nsecond\nthird", $value['compare'] );
    }

    public function testSingleRelation()
    {
        $value = self::value( 'ezobjectrelation', '<attribute><related-object-remote-id> target </related-object-remote-id></attribute>' );
        $this->assertSame( array( 'target' ), $value['ids'] );
        $this->assertSame( array(), self::value( 'ezobjectrelation', '<attribute><related-object-remote-id/></attribute>' )['ids'] );
    }

    public function testImageUsesTheFileOfItsKey()
    {
        $info = function ( $key ) { return $key === 'k1' ? array( 'name' => 'photo.jpg', 'size' => 1234, 'md5' => 'abc', 'missing' => false ) : null; };
        $value = self::value( 'ezimage', '<attribute image-file-key="k1" alternative-text="A photo"/>', $info );
        $this->assertSame( 'file', $value['kind'] );
        $this->assertSame( 'photo.jpg', $value['file']['name'] );
        $this->assertStringContainsString( 'MD5: abc', $value['text'] );
        $this->assertSame( 'alternative_text=A photo', $value['compare'] );
        $this->assertSame( 'empty', self::value( 'ezimage', '<attribute/>' )['kind'] );
    }

    public function testBinaryFileWithoutTheFileAtHandIsComparedByNameAndSize()
    {
        $value = self::value( 'ezbinaryfile', '<attribute><binary-file filekey="gone" original-filename="doc.pdf" mime-type="application/pdf" filesize="99"/></attribute>' );
        $this->assertSame( array( 'name' => 'doc.pdf', 'size' => 99, 'md5' => null, 'missing' => true ), $value['file'] );
        $this->assertSame( 'file_missing', $value['note'] );
        $this->assertSame( 'mime_type=application/pdf', $value['compare'] );
    }

    public function testMediaKeepsItsPlayerSettings()
    {
        $value = self::value( 'ezmedia', '<attribute><media-file filekey="x" original-filename="clip.mp4" mime-type="video/mp4" width="640" height="480" is-loop="1"/></attribute>' );
        $this->assertStringContainsString( 'width=640', $value['compare'] );
        $this->assertStringContainsString( 'is-loop=1', $value['compare'] );
        $this->assertSame( 'empty', self::value( 'ezmedia', '<attribute/>' )['kind'] );
    }

    public function testUserAccountNeverShowsThePasswordHash()
    {
        $value = self::value( 'ezuser', '<attribute><account login="ada" email="ada@k1.example.invalid" is_enabled="1" password_hash="SECRET-HASH" password_hash_type="7"/></attribute>' );
        $this->assertStringContainsString( 'ada@k1.example.invalid', $value['text'] );
        $this->assertStringNotContainsString( 'SECRET-HASH', $value['text'] );
        $this->assertStringNotContainsString( 'SECRET-HASH', $value['compare'] );
        $other = self::value( 'ezuser', '<attribute><account login="ada" email="ada@k1.example.invalid" is_enabled="1" password_hash="OTHER" password_hash_type="7"/></attribute>' );
        $this->assertNotSame( $value['compare'], $other['compare'], 'a different hash is still a difference' );
    }

    public function testValuesEqual()
    {
        $text = function ( $c ) { return array( 'kind' => 'text', 'compare' => $c ); };
        $file = function ( $md5, $name = 'a', $size = 1 ) { return array( 'kind' => 'file', 'compare' => '', 'file' => array( 'md5' => $md5, 'name' => $name, 'size' => $size ) ); };
        $this->assertTrue( eZPackageComparison::valuesEqual( $text( 'x' ), $text( 'x' ) ) );
        $this->assertFalse( eZPackageComparison::valuesEqual( $text( 'x' ), $text( 'y' ) ) );
        $this->assertFalse( eZPackageComparison::valuesEqual( $text( '' ), $file( 'm' ) ), 'a file is never equal to a text' );
        $this->assertTrue( eZPackageComparison::valuesEqual( $file( 'm' ), $file( 'm', 'other', 5 ) ), 'checksums decide' );
        $this->assertFalse( eZPackageComparison::valuesEqual( $file( 'm' ), $file( 'n' ) ) );
        $this->assertTrue( eZPackageComparison::valuesEqual( $file( null ), $file( 'n' ) ), 'without a checksum: name and size' );
        $this->assertFalse( eZPackageComparison::valuesEqual( $file( null ), $file( 'n', 'a', 2 ) ) );
        $noFile = array( 'kind' => 'file', 'compare' => '', 'file' => null );
        $this->assertTrue( eZPackageComparison::valuesEqual( $noFile, $noFile ) );
        $this->assertFalse( eZPackageComparison::valuesEqual( $noFile, $file( 'm' ) ) );
    }

    // ------------------------------------------------------------- XML helpers

    public function testUnformatRemovesOnlyFormattingWhiteSpace()
    {
        $root = self::element( "<a>\n  <b>keep  this</b>\n  <c><i>x</i> <i>y</i></c>\n</a>" );
        eZPackageComparison::unformat( $root );
        $this->assertSame( '<a><b>keep  this</b><c><i>x</i> <i>y</i></c></a>', $root->ownerDocument->saveXML( $root ) );
    }

    public function testPruneEmptyElements()
    {
        $root = self::element( '<a><b/><c x="1"/><d><e/></d><f>t</f></a>' );
        eZPackageComparison::pruneEmptyElements( $root );
        $this->assertSame( '<a><c x="1"/><f>t</f></a>', $root->ownerDocument->saveXML( $root ) );
    }

    public function testCanonicalXMLIsTheSameForEquivalentDocuments()
    {
        $one = eZPackageComparison::canonicalXML( self::element( '<a y="2" x="1"><b></b></a>' ) );
        $two = eZPackageComparison::canonicalXML( self::element( "<a x='1' y='2'><b/></a>" ) );
        $this->assertSame( $one, $two );
        $detached = self::element( '<r><a>1</a></r>' )->firstChild->cloneNode( true );
        $this->assertSame( '<a>1</a>', eZPackageComparison::canonicalXML( $detached ), 'a node outside its tree is not empty' );
    }

    public function testPrettyXMLIndents()
    {
        $this->assertSame( "<a>\n  <b>1</b>\n</a>", eZPackageComparison::prettyXML( self::element( '<a><b>1</b></a>' ) ) );
    }

    public function testChildrenByLocalNameIgnoresThePrefix()
    {
        $root = self::element( '<r xmlns:ez="http://ez.no/object/"><ez:item/><item/><other/>text</r>' );
        $this->assertCount( 2, eZPackageComparison::childrenByLocalName( $root, 'item' ) );
        $this->assertCount( 0, eZPackageComparison::childrenByLocalName( $root, 'missing' ) );
    }

    public function testNameList()
    {
        $this->assertSame( array( 'eng-GB' => 'Article', 'ger-DE' => 'Artikel' ),
                           eZPackageComparison::nameList( serialize( array( 'ger-DE' => 'Artikel', 'eng-GB' => 'Article', 'always-available' => 'eng-GB', 'nor-NO' => '' ) ) ) );
        $this->assertSame( array(), eZPackageComparison::nameList( 'not serialized' ) );
        $this->assertSame( array(), eZPackageComparison::nameList( serialize( new ArrayObject() ) ), 'objects are not unserialized' );
    }

    // ------------------------------------------------------------- classes

    private static function classXML( $nameSuffix = '', $extraAttribute = '' )
    {
        $names = htmlspecialchars( serialize( array( 'eng-GB' => 'Article' . $nameSuffix, 'always-available' => 'eng-GB' ) ) );
        $titleNames = htmlspecialchars( serialize( array( 'eng-GB' => 'Title' ) ) );
        return <<<XML
<content-class is-container="true" always-available="false" sort-field="1" sort-order="1">
  <serialized-name-list>$names</serialized-name-list>
  <identifier>article</identifier>
  <remote-id>rid</remote-id>
  <object-name-pattern>&lt;title&gt;</object-name-pattern>
  <url-alias-pattern/>
  <attributes>
    <attribute datatype="ezstring" required="true" searchable="true" information-collector="false" translatable="true">
      <serialized-name-list>$titleNames</serialized-name-list>
      <identifier>title</identifier>
      <placement>1</placement>
      <datatype-parameters><max-length>255</max-length></datatype-parameters>
    </attribute>
    $extraAttribute
  </attributes>
</content-class>
XML;
    }

    public function testClassData()
    {
        $data = eZPackageComparison::classData( self::element( self::classXML() ) );
        $this->assertSame( 'article', $data['identifier'] );
        $this->assertSame( 'Article', $data['name'] );
        $this->assertSame( 'eng-GB: Article', $data['properties']['names'] );
        $this->assertSame( '<title>', $data['properties']['object_name_pattern'] );
        $this->assertSame( 'true', $data['properties']['is_container'] );
        $this->assertSame( '1 1', $data['properties']['sort'] );
        $this->assertSame( array( 'title' ), array_keys( $data['attributes'] ) );
        $title = $data['attributes']['title'];
        $this->assertSame( 'ezstring', $title['datatype'] );
        $this->assertSame( 'Title', $title['name'] );
        $this->assertSame( '<max-length>255</max-length>', $title['parameters'] );
        $this->assertSame( 'true', $title['required'] );
    }

    public function testCompareClassData()
    {
        $package = eZPackageComparison::classData( self::element( self::classXML( '', '<attribute datatype="eztext"><identifier>body</identifier></attribute>' ) ) );
        $site = eZPackageComparison::classData( self::element( self::classXML( ' (site)' ) ) );

        $same = eZPackageComparison::compareClassData( $package, $package, true );
        $this->assertSame( 'identical', $same['status'] );
        $this->assertSame( 0, $same['fields'] );

        $result = eZPackageComparison::compareClassData( $package, $site, true );
        $this->assertSame( 'changed', $result['status'] );
        $this->assertSame( 2, $result['fields'], 'the names and the added attribute' );
        $this->assertSame( 1, $result['attributes']['added'] );
        $this->assertSame( array( 'body' ), $result['addable'] );
        $rows = array_column( $result['attribute_rows'], 'state', 'identifier' );
        $this->assertSame( array( 'title' => 'identical', 'body' => 'package_only' ), $rows );

        $new = eZPackageComparison::compareClassData( $package, null, false );
        $this->assertSame( 'new', $new['status'] );
        $this->assertSame( 0, $new['fields'] );
    }

    public function testAttributeWithADatatypeTheSiteLacksCannotBeAdded()
    {
        $package = eZPackageComparison::classData( self::element( self::classXML( '', '<attribute datatype="k1nosuchdatatype"><identifier>odd</identifier></attribute>' ) ) );
        $site = eZPackageComparison::classData( self::element( self::classXML() ) );
        $this->assertSame( array( 'odd' ), eZPackageComparison::compareClassData( $package, $site, false )['unaddable'] );
    }

    // ------------------------------------------------------------- objects

    private static function object( array $languages, array $placement = array(), $class = 'article' )
    {
        return array( 'languages' => $languages, 'placement' => $placement, 'class_identifier' => $class, 'names' => array() );
    }

    private static function text( $text )
    {
        return array( 'kind' => 'text', 'compare' => $text, 'text' => $text );
    }

    public function testCompareObjectData()
    {
        $place = array( array( 'node_remote_id' => 'n1', 'parent_remote_id' => 'p1', 'main' => true, 'top' => false ) );
        $package = self::object( array( 'eng-GB' => array( 'title' => self::text( 'A' ), 'body' => self::text( 'B' ) ),
                                        'ger-DE' => array( 'title' => self::text( 'X' ) ) ), $place );
        $site = self::object( array( 'eng-GB' => array( 'title' => self::text( 'A' ), 'body' => self::text( 'changed' ) ),
                                     'nor-NO' => array( 'title' => self::text( 'Y' ) ) ), $place );

        $this->assertSame( 'identical', eZPackageComparison::compareObjectData( $package, $package, false )['status'] );

        $result = eZPackageComparison::compareObjectData( $package, $site, true );
        $this->assertSame( 'changed', $result['status'] );
        $this->assertSame( 1, $result['fields'] );
        $this->assertSame( 1, $result['lang_package'] );
        $this->assertSame( 1, $result['lang_site'] );
        $this->assertFalse( $result['placement'] );
        $this->assertSame( array( 'title' => 'identical', 'body' => 'changed' ), array_column( $result['languages']['eng-GB']['rows'], 'state', 'identifier' ) );
        $this->assertSame( 'package', $result['languages']['ger-DE']['state'] );
        $this->assertSame( 'site', $result['languages']['nor-NO']['state'] );

        $this->assertSame( 'new', eZPackageComparison::compareObjectData( $package, null, false )['status'] );
        $this->assertSame( 'removed', eZPackageComparison::compareObjectData( null, $site, false )['status'] );
        $this->assertSame( 'identical', eZPackageComparison::compareObjectData( null, null, false )['status'] );
    }

    public function testAttributesTheSiteClassLacksAreNamed()
    {
        $package = self::object( array( 'eng-GB' => array( 'title' => self::text( 'A' ), 'subtitle' => self::text( 'S' ) ) ) );
        $site = self::object( array( 'eng-GB' => array( 'title' => self::text( 'A' ) ) ) );
        $result = eZPackageComparison::compareObjectData( $package, $site, false );
        $this->assertSame( array( 'subtitle' ), $result['missing_attributes'] );
        $this->assertSame( 1, $result['missing_fields'] );
    }

    public function testPlacementAndClassChanges()
    {
        $package = self::object( array(), array( array( 'node_remote_id' => 'n1', 'parent_remote_id' => 'p1', 'main' => true, 'top' => false ) ) );
        $moved = self::object( array(), array( array( 'node_remote_id' => 'n1', 'parent_remote_id' => 'p2', 'main' => true, 'top' => false ) ) );
        $this->assertTrue( eZPackageComparison::compareObjectData( $package, $moved, false )['placement'] );
        $this->assertTrue( eZPackageComparison::compareObjectData( $package, self::object( array(), $package['placement'], 'folder' ), false )['class_changed'] );
        $this->assertFalse( eZPackageComparison::compareObjectData( self::object( array(), $package['placement'], '' ), self::object( array(), $package['placement'], 'folder' ), false )['class_changed'] );
    }

    public function testTopNodesAreComparedWithoutTheirParent()
    {
        $package = array( array( 'node_remote_id' => 'top', 'parent_remote_id' => 'anywhere', 'main' => true, 'top' => true ) );
        $site = array( array( 'node_remote_id' => 'top', 'parent_remote_id' => 'elsewhere', 'main' => 1, 'top' => false ) );
        $this->assertSame( eZPackageComparison::placementLines( $package, array() ), eZPackageComparison::placementLines( $site, $package ) );
        $lines = eZPackageComparison::placementLines( array( array( 'node_remote_id' => 'b', 'parent_remote_id' => 'x', 'main' => 0, 'top' => false ),
                                                             array( 'node_remote_id' => 'a', 'parent_remote_id' => 'y', 'main' => 1, 'top' => false ) ), array() );
        $this->assertSame( array( 'a', 'b' ), array_column( $lines, 'node_remote_id' ) );
        $this->assertSame( array( true, false ), array_column( $lines, 'main' ) );
    }

    // ------------------------------------------------------------- paging

    private static function index()
    {
        $items = array();
        $data = array( array( 'changed', 'article', 'Zeta', 3 ), array( 'new', 'folder', 'alpha', 0 ), array( 'identical', 'article', 'Beta', 0 ),
                       array( 'changed', 'folder', 'gamma', 1 ), array( 'removed', '', 'Delta', 0 ) );
        foreach ( $data as $i => $row )
            $items[] = array( 'index' => $i, 'status' => $row[0], 'class_identifier' => $row[1], 'name' => $row[2], 'remote_id' => "rid$i",
                              'fields' => $row[3], 'lang_package' => 0, 'lang_site' => 0, 'placement' => false, 'class_changed' => false );
        return array( 'items' => $items );
    }

    public function testFilteredPage()
    {
        $page = eZPackageComparison::filteredPage( self::index(), array( 'limit' => 2 ) );
        $this->assertSame( 5, $page['total_all'] );
        $this->assertSame( 5, $page['total_filtered'] );
        $this->assertSame( 3, $page['pages'] );
        $this->assertSame( array( 0, 1 ), array_column( $page['items'], 'index' ) );
        $this->assertSame( array( 'new' => 1, 'changed' => 2, 'removed' => 1, 'identical' => 1, 'class_missing' => 0 ), $page['counts'] );
        $this->assertSame( array( 'article' => 2, 'folder' => 2 ), $page['classes'] );
    }

    public function testFilteredPageFilters()
    {
        $this->assertSame( array( 0, 3 ), eZPackageComparison::filteredPage( self::index(), array( 'status' => 'changed' ) )['filtered_indices'] );
        $this->assertSame( array( 1, 3 ), eZPackageComparison::filteredPage( self::index(), array( 'class' => 'folder' ) )['filtered_indices'] );
        $this->assertSame( array( 2 ), eZPackageComparison::filteredPage( self::index(), array( 'search' => 'BET' ) )['filtered_indices'] );
        $this->assertSame( array( 4 ), eZPackageComparison::filteredPage( self::index(), array( 'search' => 'rid4' ) )['filtered_indices'] );
        $counts = eZPackageComparison::filteredPage( self::index(), array( 'class' => 'article', 'status' => 'new' ) )['counts'];
        $this->assertSame( 1, $counts['changed'], 'the status counts ignore the status filter' );
    }

    public function testFilteredPageSorts()
    {
        $this->assertSame( array( 1, 2, 4, 3, 0 ), eZPackageComparison::filteredPage( self::index(), array( 'sort' => 'name' ) )['filtered_indices'] );
        $this->assertSame( array( 0, 3, 1, 2, 4 ), eZPackageComparison::filteredPage( self::index(), array( 'sort' => 'differences', 'dir' => 'desc' ) )['filtered_indices'] );
        $this->assertSame( array( 1, 0, 3, 4, 2 ), eZPackageComparison::filteredPage( self::index(), array( 'sort' => 'status' ) )['filtered_indices'] );
        $this->assertSame( array( 0, 1, 2, 3, 4 ), eZPackageComparison::filteredPage( self::index(), array( 'sort' => 'nonsense' ) )['filtered_indices'] );
    }

    public function testFilteredPageOffsets()
    {
        $last = eZPackageComparison::filteredPage( self::index(), array( 'limit' => 2, 'offset' => 'last' ) );
        $this->assertSame( 4, $last['offset'] );
        $this->assertSame( 3, $last['page'] );
        $this->assertSame( 4, eZPackageComparison::filteredPage( self::index(), array( 'limit' => 2, 'offset' => 99 ) )['offset'] );
        $empty = eZPackageComparison::filteredPage( self::index(), array( 'status' => 'class_missing', 'offset' => 'last' ) );
        $this->assertSame( 0, $empty['offset'] );
        $this->assertSame( 1, $empty['pages'] );
        $this->assertSame( 1, eZPackageComparison::filteredPage( self::index(), array( 'limit' => 0 ) )['limit'] );
    }

    public function testDifferenceCount()
    {
        $item = array( 'status' => 'changed', 'fields' => 2, 'lang_package' => 1, 'lang_site' => 0, 'placement' => true, 'class_changed' => true );
        $this->assertSame( 5, eZPackageComparison::differenceCount( $item ) );
        $item['status'] = 'new';
        $this->assertSame( 0, eZPackageComparison::differenceCount( $item ) );
    }

    public function testStatusesAndNotes()
    {
        $this->assertSame( array( 'new', 'changed', 'removed', 'identical', 'class_missing' ), eZPackageComparison::statuses() );
        $this->assertSame( array( 'status', 'name', 'class', 'differences' ), eZPackageComparison::sortFields() );
        $this->assertNotSame( 'file_missing', eZPackageComparison::noteText( 'file_missing' ) );
        $this->assertSame( 'k1unknown', eZPackageComparison::noteText( 'k1unknown' ) );
        $this->assertStringEndsWith( '/packagecompare/a_b-123.cache', eZPackageComparison::cacheFilePath( 'a/b', '123' ) );
    }
}
