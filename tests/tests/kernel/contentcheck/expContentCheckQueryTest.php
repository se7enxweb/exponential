<?php
/**
 * The parts of the content checks that need no database (doc/features/6.0/content-checks.md): reading the request,
 * the SQL they build, what counts as a reference, the links found in rich text, and how a target is judged.
 *
 *  CQ-01  id lists: whole numbers only, unique, bounded; anything else (SQL, negative, huge, arrays in arrays) is left out
 *  CQ-02  the parameters of the unreferenced list: node, classes, order, direction, sub-items, offset, limit
 *  CQ-03  the SQL of the unreferenced list: the subtree, the main location, published, the class filter, no
 *         sub-items, the reference condition, the permission condition, the order with the id as tie-breaker; a
 *         pattern that is not a subtree path is refused
 *  CQ-04  what counts as a reference: other objects, published or draft objects (not the trash), from published,
 *         draft, pending, internal draft and queued versions (not archived, rejected or repeat)
 *  CQ-05  links and embeds in rich text: object_id and node_id of link, embed and embed-inline, each once; other
 *         attributes (url_id, custom:object_id) and other tags are not references
 *  CQ-06  how a target is judged: deleted, in the trash, never published, without location, hidden, fine
 *  CQ-07  relation types (a bit mask) and the kinds asked for
 *  CQ-08  the parameters and the source query of the broken relations: subtree, exclusions, visible and
 *         published, keyset paging after an object id
 *  CQ-09  the query strings of the pages keep the filters; nothing from the request is copied into them unchecked
 *  CQ-10  the image path of an image attribute, only inside the installation
 *  CQ-11  the databases the checks run on
 *  CQ-12  the trash view: another method than POST is refused before anything else
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Contentcheck\Brokenrelations;
use Exponential\View\Kernel\Contentcheck\Trash;
use Exponential\View\Kernel\Contentcheck\Unreferenced;

class expContentCheckQueryTest extends PHPUnit\Framework\TestCase
{
    /** CQ-01 */
    public function testIdListsTakeWholeNumbersOnly()
    {
        $this->assertSame( array( 3, 7, 12 ), expContentCheck::idList( '3, 7;12 7' ) );
        $this->assertSame( array( 5, 9 ), expContentCheck::idList( array( '5', 9, '9', '-1', '0', 'x', '1 OR 1=1', array( 4 ), '2147483648', ' 9 ' ) ) );
        $this->assertSame( array( 1 ), expContentCheck::idList( '1; DROP TABLE ezcontentobject' ) );
        $this->assertCount( 3, expContentCheck::idList( range( 1, 10 ), 3 ) );
        $this->assertSame( array(), expContentCheck::idList( null ) );
        $this->assertSame( 42, expContentCheck::id( '42', 2 ) );
        $this->assertSame( 2, expContentCheck::id( array( 42 ), 2 ) );
        $this->assertSame( 2, expContentCheck::id( 'abc', 2 ) );
        $this->assertSame( '1, 2, 3', expContentCheck::inList( array( '1', 2, 3.7 ) ) );
        $this->assertSame( '0', expContentCheck::inList( array() ) );
    }

    /** CQ-02 */
    public function testUnreferencedParameters()
    {
        $p = expContentCheckUnreferenced::parameters( array(), 2 );
        $this->assertSame( array( 'node_id' => 2, 'class_ids' => array(), 'sort' => 'name', 'order' => 'asc', 'containers' => false,
                                  'offset' => 0, 'limit' => 25 ), $p );
        $p = expContentCheckUnreferenced::parameters( array( 'NodeID' => '58', 'ClassID' => array( '5', 'x', '12' ), 'Sort' => 'modified',
                                                             'Containers' => '1', 'Offset' => '50', 'Limit' => 10 ), 2 );
        $this->assertSame( 58, $p['node_id'] );
        $this->assertSame( array( 5, 12 ), $p['class_ids'] );
        $this->assertSame( 'modified', $p['sort'] );
        $this->assertSame( 'desc', $p['order'], 'the newest first when sorted by a date' );
        $this->assertTrue( $p['containers'] );
        $this->assertSame( 50, $p['offset'] );
        $this->assertSame( 10, $p['limit'] );
        $p = expContentCheckUnreferenced::parameters( array( 'Sort' => 'name; DROP', 'Order' => 'sideways', 'Containers' => 'yes',
                                                             'Offset' => '-5', 'Limit' => 100000 ), 2 );
        $this->assertSame( 'name', $p['sort'] );
        $this->assertSame( 'asc', $p['order'] );
        $this->assertFalse( $p['containers'] );
        $this->assertSame( 0, $p['offset'] );
        $this->assertSame( 200, $p['limit'] );
    }

    /** CQ-03 */
    public function testUnreferencedSQL()
    {
        $params = expContentCheckUnreferenced::parameters( array( 'NodeID' => 58, 'ClassID' => array( 5, 12 ), 'Sort' => 'class', 'Order' => 'desc' ), 2 );
        $q = expContentCheckUnreferenced::queries( $params, '/1/2/58/%', array( 'from' => ', ezcontentobject_state_link perm', 'where' => 'AND perm.x = 1' ) );
        $this->assertStringStartsWith( 'SELECT COUNT(*) AS row_count FROM ezcontentobject, ezcontentobject_tree, ezcontentclass, ezcontentobject_state_link perm WHERE ', $q['count'] );
        foreach ( array( "ezcontentobject_tree.path_string LIKE '/1/2/58/%'", 'ezcontentobject_tree.node_id <> 58',
                         'ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id', 'ezcontentobject.status = 1',
                         'ezcontentobject.contentclass_id IN ( 5, 12 )',
                         'NOT EXISTS ( SELECT 1 FROM ezcontentobject_tree cc_child WHERE cc_child.parent_node_id = ezcontentobject_tree.node_id )',
                         'NOT EXISTS ( SELECT 1 FROM ezcontentobject_link cc_link', 'AND perm.x = 1' ) as $part )
        {
            $this->assertStringContainsString( $part, $q['count'] );
            $this->assertStringContainsString( $part, $q['list'] );
        }
        $this->assertStringEndsWith( 'ORDER BY ezcontentclass.identifier DESC, ezcontentobject.id DESC', $q['list'] );
        $this->assertStringNotContainsString( 'LIMIT', $q['list'], 'the database layer adds the limit, per engine' );

        $params['containers'] = true;
        $params['class_ids'] = array();
        $params['sort'] = 'id';
        $params['order'] = 'asc';
        $q = expContentCheckUnreferenced::queries( $params, '/1/2/58/%', array() );
        $this->assertStringNotContainsString( 'cc_child', $q['list'] );
        $this->assertStringNotContainsString( 'contentclass_id IN', $q['list'] );
        $this->assertStringEndsWith( 'ORDER BY ezcontentobject.id ASC', $q['list'] );

        foreach ( array( "/1/2/' OR 1=1 --/%", '/1/2/58/', '1/2/%', '' ) as $bad )
        {
            try
            {
                expContentCheckUnreferenced::conditions( $params, $bad, array() );
                $this->fail( "accepted $bad" );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertSame( 'not a subtree pattern', $e->getMessage() );
            }
        }
        $this->assertSame( '/1/2/58/%', expContentCheck::subtreePattern( '/1/2/58/' ) );
        $this->assertNull( expContentCheck::subtreePattern( "/1/2/58'/" ) );
        $this->assertNull( expContentCheck::subtreePattern( '/1/2/58' ) );
    }

    /** CQ-04 */
    public function testWhatCountsAsAReference()
    {
        $sql = expContentCheck::referenceExistsSQL( 'ezcontentobject.id' );
        $this->assertStringContainsString( 'cc_link.to_contentobject_id = ezcontentobject.id', $sql );
        $this->assertStringContainsString( 'cc_link.from_contentobject_id <> ezcontentobject.id', $sql, 'an object that refers to itself is not referenced' );
        $this->assertStringContainsString( 'cc_src.status IN ( 0, 1 )', $sql, 'not from an object in the trash (2)' );
        $this->assertStringContainsString( 'cc_ver.version = cc_link.from_contentobject_version', $sql );
        $this->assertStringContainsString( 'cc_ver.status IN ( 0, 1, 2, 5, 7 )', $sql, 'not from archived (3), rejected (4) or repeat (6) versions' );
        $this->assertSame( array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_PUBLISHED, eZContentObjectVersion::STATUS_PENDING,
                                  eZContentObjectVersion::STATUS_INTERNAL_DRAFT, eZContentObjectVersion::STATUS_QUEUED ),
                           expContentCheck::REFERENCE_VERSION_STATUSES );
        $this->assertSame( array( eZContentObject::STATUS_DRAFT, eZContentObject::STATUS_PUBLISHED ), expContentCheck::REFERENCE_OBJECT_STATUSES );
        $this->assertStringNotContainsString( 'relation_type', $sql, 'every relation type counts' );
        $this->assertStringContainsString( 'ezcontentobject.id', expContentCheck::referenceExistsSQL( "ezcontentobject.id'; --" ) );
        $this->assertStringNotContainsString( "'", expContentCheck::referenceExistsSQL( "ezcontentobject.id'; --" ) );
    }

    /** CQ-05 */
    public function testLinksAndEmbedsInRichText()
    {
        $xml = '<?xml version="1.0"?><section><paragraph>See <link object_id="93">form</link>, <link node_id="67" target="_blank">page</link>'
             . ' and <link url_id="5">site</link>.<embed view="embed" size="medium" custom:offset="0" object_id="167"/>'
             . '<embed-inline object_id="12"/><embed node_id="44"/><link object_id="93">again</link><custom name="x" custom:object_id="99"/></paragraph></section>';
        $refs = expContentCheckBrokenRelations::xmlReferences( $xml );
        $this->assertSame( array( array( 'type' => 'object', 'id' => 93, 'tag' => 'link' ),
                                  array( 'type' => 'node', 'id' => 67, 'tag' => 'link' ),
                                  array( 'type' => 'object', 'id' => 167, 'tag' => 'embed' ),
                                  array( 'type' => 'object', 'id' => 12, 'tag' => 'embed-inline' ),
                                  array( 'type' => 'node', 'id' => 44, 'tag' => 'embed' ) ), $refs );
        $this->assertSame( array(), expContentCheckBrokenRelations::xmlReferences( '<section><paragraph>object_id="5" as text</paragraph></section>' ) );
        $this->assertSame( array(), expContentCheckBrokenRelations::xmlReferences( '' ) );
        $this->assertSame( array(), expContentCheckBrokenRelations::xmlReferences( '<link object_id="0"/>' ) );
    }

    /** CQ-06 */
    public function testHowATargetIsJudged()
    {
        $this->assertSame( 'deleted', expContentCheckBrokenRelations::objectState( null, null ) );
        $this->assertSame( 'trash', expContentCheckBrokenRelations::objectState( array( 'status' => 2 ), array( 'locations' => 0, 'visible' => 0 ) ) );
        $this->assertSame( 'unpublished', expContentCheckBrokenRelations::objectState( array( 'status' => 0 ), null ) );
        $this->assertSame( 'no_location', expContentCheckBrokenRelations::objectState( array( 'status' => 1 ), null ) );
        $this->assertSame( 'hidden', expContentCheckBrokenRelations::objectState( array( 'status' => 1 ), array( 'locations' => 2, 'visible' => 0 ) ) );
        $this->assertNull( expContentCheckBrokenRelations::objectState( array( 'status' => 1 ), array( 'locations' => 2, 'visible' => 1 ) ) );
        $this->assertSame( 'deleted', expContentCheckBrokenRelations::nodeState( null, false ) );
        $this->assertSame( 'trash', expContentCheckBrokenRelations::nodeState( null, true ) );
        $this->assertSame( 'hidden', expContentCheckBrokenRelations::nodeState( array( 'is_hidden' => 0, 'is_invisible' => 1 ), false ) );
        $this->assertSame( 'hidden', expContentCheckBrokenRelations::nodeState( array( 'is_hidden' => 1, 'is_invisible' => 1 ), false ) );
        $this->assertNull( expContentCheckBrokenRelations::nodeState( array( 'is_hidden' => 0, 'is_invisible' => 0 ), false ) );
    }

    /** CQ-07 */
    public function testRelationTypes()
    {
        $both = array( 'xml', 'object' );
        foreach ( array( 0, 1, 2, 4, 6, 8, 9, 15 ) as $type )
            $this->assertTrue( expContentCheckBrokenRelations::relationMatches( $type, $both ), "type $type" );
        $this->assertTrue( expContentCheckBrokenRelations::relationMatches( 2, array( 'xml' ) ) );
        $this->assertTrue( expContentCheckBrokenRelations::relationMatches( 4, array( 'xml' ) ) );
        $this->assertFalse( expContentCheckBrokenRelations::relationMatches( 8, array( 'xml' ) ) );
        $this->assertFalse( expContentCheckBrokenRelations::relationMatches( 0, array( 'xml' ) ) );
        $this->assertTrue( expContentCheckBrokenRelations::relationMatches( 0, array( 'object' ) ), 'a row from before the types is an object relation' );
        $this->assertFalse( expContentCheckBrokenRelations::relationMatches( 6, array( 'object' ) ) );
        $this->assertTrue( expContentCheckBrokenRelations::relationMatches( 9, array( 'object' ) ) );
    }

    /** CQ-08 */
    public function testBrokenRelationParametersAndSourceQuery()
    {
        $p = expContentCheckBrokenRelations::parameters( array() );
        $this->assertSame( 1, $p['node_id'] );
        $this->assertSame( 2, expContentCheckBrokenRelations::parameters( array(), 2 )['node_id'], 'the view passes the top node of the content tree' );
        $this->assertSame( expContentCheckBrokenRelations::KINDS, $p['kinds'] );
        $this->assertSame( expContentCheckBrokenRelations::RELATIONS, $p['relations'] );
        $this->assertSame( 0, $p['after'] );
        $p = expContentCheckBrokenRelations::parameters( array( 'NodeID' => '58', 'Exclude' => '43, 5,x', 'Kind' => array( 'trash', 'nope' ),
                                                                'Relation' => array( 'xml' ), 'After' => '120', 'Limit' => '0' ) );
        $this->assertSame( 58, $p['node_id'] );
        $this->assertSame( array( 43, 5 ), $p['exclude'] );
        $this->assertSame( array( 'trash' ), $p['kinds'] );
        $this->assertSame( array( 'xml' ), $p['relations'] );
        $this->assertSame( 120, $p['after'] );
        $this->assertSame( 1, $p['limit'] );
        $this->assertSame( 0, expContentCheckBrokenRelations::parameters( array( 'After' => '1 OR 1=1' ) )['after'] );
        $this->assertSame( expContentCheckBrokenRelations::KINDS, expContentCheckBrokenRelations::parameters( array( 'Kind' => 'trash' ) )['kinds'] );
        $this->assertCount( expContentCheckBrokenRelations::MAX_EXCLUDED, expContentCheckBrokenRelations::parameters( array( 'Exclude' => range( 1, 50 ) ) )['exclude'] );

        $sql = expContentCheckBrokenRelations::sourceQuery( '/1/2/%', array( '/1/2/43/%', '/1/5/%' ), array( 'from' => '', 'where' => 'AND 1 = 1' ), 120 );
        foreach ( array( "ezcontentobject_tree.path_string LIKE '/1/2/%'", "ezcontentobject_tree.path_string NOT LIKE '/1/2/43/%'",
                         "ezcontentobject_tree.path_string NOT LIKE '/1/5/%'", 'ezcontentobject_tree.is_invisible = 0', 'ezcontentobject.status = 1',
                         'ezcontentobject.id > 120', 'ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id', 'AND 1 = 1' ) as $part )
            $this->assertStringContainsString( $part, $sql );
        $this->assertStringEndsWith( 'ORDER BY ezcontentobject.id ASC', $sql );
        $this->expectException( InvalidArgumentException::class );
        expContentCheckBrokenRelations::sourceQuery( '/1/2/%', array( "/1/' OR '1'='1/%" ), array(), 0 );
    }

    /** CQ-09 */
    public function testQueryStringsOfThePages()
    {
        $p = expContentCheckUnreferenced::parameters( array( 'ClassID' => array( 5, '12' ), 'Sort' => 'modified', 'Containers' => '1' ), 2 );
        $this->assertSame( '?ClassID%5B%5D=5&ClassID%5B%5D=12&Sort=modified&Order=desc&Containers=1', Unreferenced::querySuffix( $p ) );
        $p = expContentCheckBrokenRelations::parameters( array( 'Exclude' => '43,5', 'Kind' => array( 'deleted', '"><script>' ), 'Relation' => array( 'xml', 'object' ) ) );
        $this->assertSame( '?Exclude=43%2C5&Kind%5B%5D=deleted', Brokenrelations::querySuffix( $p ) );
        $this->assertSame( '?', Brokenrelations::querySuffix( expContentCheckBrokenRelations::parameters( array() ) ) );
    }

    /** CQ-10 */
    public function testImagePath()
    {
        $this->assertSame( 'var/site/storage/images/a/b/c-1-ger-DE/c.jpg',
                           expContentCheckUnreferenced::imagePath( '<?xml version="1.0"?><ezimage serial_number="1" is_valid="1" filename="c.jpg" url="var/site/storage/images/a/b/c-1-ger-DE/c.jpg" original_filename="c.jpg"><original attribute_id="1"/></ezimage>' ) );
        $this->assertSame( 'var/a&b.jpg', expContentCheckUnreferenced::imagePath( '<ezimage url="var/a&amp;b.jpg"/>' ) );
        foreach ( array( '<ezimage url="../etc/passwd"/>', '<ezimage url="/etc/passwd"/>', '<ezimage url="http://x/y.jpg"/>', '<ezimage url=""/>', 'nothing' ) as $xml )
            $this->assertSame( '', expContentCheckUnreferenced::imagePath( $xml ), $xml );
    }

    /** CQ-11 */
    public function testDatabases()
    {
        foreach ( array( 'mysql', 'postgresql', 'sqlite', 'oracle' ) as $name )
            $this->assertTrue( expContentCheck::isSupportedDatabase( $name ), $name );
        $this->assertFalse( expContentCheck::isSupportedDatabase( 'mongo' ) );
        $this->assertFalse( expContentCheck::isSupportedDatabase( 'null' ) );
    }

    /** CQ-12 */
    public function testTrashViewRefusesOtherMethods()
    {
        foreach ( array( 'GET', 'HEAD', 'PUT', '' ) as $method )
        {
            $d = Trash::decide( $method, array( 'ObjectIDList' => array( 1 ), 'ConfirmButton' => 1, 'NodeID' => 58 ) );
            $this->assertSame( 'refused', $d['step'], $method );
            $this->assertSame( 'method', $d['reason'], $method );
            $this->assertSame( array(), $d['object_ids'], $method );
            $this->assertSame( 58, $d['node_id'] );
        }
        $this->assertFalse( expContentCheck::matches( null, 'abc' ) );
        $this->assertFalse( expContentCheck::matches( 'abc', '' ) );
        $this->assertFalse( expContentCheck::matches( 'abd', 'abc' ) );
        $this->assertTrue( expContentCheck::matches( 'abc', 'abc' ) );
    }
}
