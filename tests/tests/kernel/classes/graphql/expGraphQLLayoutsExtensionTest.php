<?php
/**
 * The explayouts schema extension of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Layouts"): layout by
 * id, identifier and URL alias, Location.layout, zones linked to a shared layout, blocks with their parameters and
 * collections in one batch per level, the content of a manual collection as the user may read it, and blocks in a
 * container. No database, no explayouts: a source of a fixture layout.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expGraphQLSchemaTest.php';

class expGraphQLTestLayoutsSource extends expGraphQLLayoutsSource
{
    /** @var string[] the batches asked for */
    public $batches = array();

    /** @var string[] the paths resolved */
    public $paths = array();

    protected static function fixtureBlock( $id, $zoneId, $definition, $parentId = 0 )
    {
        return array( 'id' => $id, 'zoneId' => $zoneId, 'layoutId' => 9, 'parentId' => $parentId, 'position' => $id, 'definition' => $definition,
                      'viewType' => $definition, 'itemViewType' => 'line', 'name' => '', 'placeholder' => '' );
    }

    public function isAvailable() { return true; }
    public function layoutById( $id ) { return (int)$id === 9 ? array( 'id' => 9, 'identifier' => 'home', 'name' => 'Home', 'type' => 'layout_2', 'shared' => false, 'modified' => 1767225600 ) : null; }
    public function layoutByIdentifier( $identifier ) { return $identifier === 'home' ? $this->layoutById( 9 ) : null; }
    public function layoutForPath( $urlAlias ) { $this->paths[] = $urlAlias; return $this->layoutById( 9 ); }
    public function zones( $layoutId )
    {
        return array( array( 'id' => 1, 'identifier' => 'header', 'layoutId' => 9, 'linkedLayoutId' => 2, 'linkedZone' => 'header', 'sourceZoneId' => 50 ),
                      array( 'id' => 2, 'identifier' => 'main', 'layoutId' => 9, 'linkedLayoutId' => null, 'linkedZone' => null, 'sourceZoneId' => 2 ) );
    }
    public function blocks( $zoneId )
    {
        if ( $zoneId === 50 )
            return array( self::fixtureBlock( 500, 50, 'menu' ) );
        return $zoneId === 2 ? array( self::fixtureBlock( 20, 2, 'list' ), self::fixtureBlock( 21, 2, 'title' ), self::fixtureBlock( 22, 2, 'column' ) ) : array();
    }
    public function children( $blockId ) { return $blockId === 22 ? array( self::fixtureBlock( 23, 2, 'html', 22 ) ) : array(); }
    public function parametersOf( array $blockIds )
    {
        $this->batches[] = 'parameters:' . implode( ',', $blockIds );
        $answer = array_fill_keys( $blockIds, array() );
        if ( isset( $answer[21] ) )
            $answer[21] = array( 'title' => 'Latest', 'tag' => 'h2' );
        return $answer;
    }
    public function collectionsOf( array $blockIds )
    {
        $this->batches[] = 'collections:' . implode( ',', $blockIds );
        $answer = array_fill_keys( $blockIds, null );
        if ( array_key_exists( 20, $answer ) )
            $answer[20] = array( 'type' => 'manual', 'offset' => 0, 'limit' => 0, 'nodeIds' => array( 11, 999, 12 ) );
        return $answer;
    }
}

class expGraphQLLayoutsExtensionTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        if ( !expGraphQL::loadLibrary() )
        {
            if ( getenv( 'CI' ) )
                self::fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
            self::markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        }
    }

    protected static function query( $query, expGraphQLTestLayoutsSource $source )
    {
        $catalogue = expGraphQLClassCatalogue::filtered( array( array( 'id' => 2, 'identifier' => 'article', 'name' => 'Article', 'description' => '', 'attributes' => array() ) ), array() );
        $builder = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ), array( new expGraphQLLayoutsExtension( $source ) ) );
        $repository = new expGraphQLTestRepository( $catalogue, array() );
        foreach ( array( 2 => 'Home', 11 => 'One', 12 => 'Two' ) as $node => $name )
            $repository->byNode[$node] = new expGraphQLTestContent( array( 'id' => $node, 'class' => 'article', 'name' => $name,
                'location' => new expGraphQLTestLocation( array( 'id' => $node, 'parentId' => $node === 2 ? 1 : 2, 'contentId' => $node, 'depth' => $node === 2 ? 1 : 2,
                                                                 'pathString' => $node === 2 ? '/1/2/' : "/1/2/$node/", 'urlAlias' => $node === 2 ? '' : strtolower( $name ) ) ),
                'fields' => array() ) );
        $context = new expGraphQLContext( new eZUser( array( 'contentobject_id' => 14, 'login' => 'admin' ) ), expGraphQLContext::AUTH_TOKEN, $repository );
        return ( new expGraphQLServer( $builder ) )->execute( $query, null, null, $context );
    }

    public function testALayoutWithItsZonesBlocksParametersAndItems()
    {
        $source = new expGraphQLTestLayoutsSource();
        $answer = self::query( '{ layout(identifier: "home") { id name type shared zones { identifier linkedLayoutId linkedZone blocks { id definition } }
                                  zone(identifier: "main") { blocks { id definition itemViewType collectionType parameters { name value } title: parameter(name: "title")
                                                                     items { name } children { definition } } } } }', $source );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $layout = $answer['data']['layout'];
        $this->assertSame( array( 9, 'Home', 'layout_2', false ), array( $layout['id'], $layout['name'], $layout['type'], $layout['shared'] ) );
        $this->assertSame( array( 'identifier' => 'header', 'linkedLayoutId' => 2, 'linkedZone' => 'header', 'blocks' => array( array( 'id' => 500, 'definition' => 'menu' ) ) ),
                           $layout['zones'][0], 'a linked zone shows the blocks of the shared zone' );
        $blocks = $layout['zone']['blocks'];
        $this->assertSame( array( 'id' => 20, 'definition' => 'list', 'itemViewType' => 'line', 'collectionType' => 'manual', 'parameters' => array(), 'title' => null,
                                  'items' => array( array( 'name' => 'One' ), array( 'name' => 'Two' ) ), 'children' => array() ), $blocks[0],
                           'the items at their locations, without what the user may not read (999)' );
        $this->assertSame( array( array( 'name' => 'title', 'value' => 'Latest' ), array( 'name' => 'tag', 'value' => 'h2' ) ), $blocks[1]['parameters'] );
        $this->assertSame( 'Latest', $blocks[1]['title'] );
        $this->assertNull( $blocks[1]['collectionType'] );
        $this->assertSame( array( array( 'definition' => 'html' ) ), $blocks[2]['children'] );
        sort( $source->batches );
        $this->assertSame( array( 'collections:20,21,22', 'parameters:20,21,22' ), $source->batches, 'one batch of each for the blocks of a level' );
    }

    public function testTheLayoutOfAPageAndOfALocation()
    {
        $source = new expGraphQLTestLayoutsSource();
        $answer = self::query( '{ byPath: layout(urlAlias: "") { id } byId: layout(id: 9) { identifier } none: layout(id: 5) { id } node(id: 11) { location { layout { identifier } } } }', $source );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertSame( array( 'byPath' => array( 'id' => 9 ), 'byId' => array( 'identifier' => 'home' ), 'none' => null,
                                  'node' => array( 'location' => array( 'layout' => array( 'identifier' => 'home' ) ) ) ), $answer['data'] );
        $this->assertSame( array( '', 'one' ), $source->paths, 'the front page and the URL alias of the location' );
        $this->assertSame( 'BAD_USER_INPUT', self::query( '{ layout { id } }', $source )['errors'][0]['extensions']['code'] );
    }
}
