<?php
/**
 * The eztags schema extension of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Tags"): tag and tags, a
 * tag's place in the tree, its synonyms and main tag through one batch, the content tagged with it, the eztags fields
 * of content, and the policy tags/read. No database, no eztags: a source of fixture tags.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expGraphQLSchemaTest.php';

class expGraphQLTestTagsSource extends expGraphQLTagsSource
{
    /** @var array id => tag */
    public $tags = array();

    /** @var bool */
    public $mayRead = true;

    /** @var int how often byIds() was asked */
    public $batches = 0;

    public function __construct()
    {
        $tag = function ( $id, $parentId, $keyword, $pathString, $mainTagId = 0 ) {
            return array( 'id' => $id, 'parentId' => $parentId, 'mainTagId' => $mainTagId, 'keyword' => $keyword, 'remoteId' => "tag$id",
                          'depth' => substr_count( $pathString, '/' ) - 1, 'pathString' => $pathString, 'modified' => 1767225600,
                          'url' => 'tags/view/' . $keyword, 'language' => false, 'languages' => array( 'eng-GB' ) );
        };
        $this->tags = array( 1 => $tag( 1, 0, 'Food', '/1/' ), 2 => $tag( 2, 1, 'Pasta', '/1/2/' ), 3 => $tag( 3, 1, 'Soup', '/1/3/' ),
                             4 => $tag( 4, 1, 'Noodles', '/1/4/', 2 ), 5 => $tag( 5, 2, 'Spaghetti', '/1/2/5/' ) );
    }

    public function isAvailable() { return true; }
    public function byId( $id, $language = false ) { return isset( $this->tags[$id] ) ? $this->tags[$id] : null; }
    public function byIds( array $ids, $language = false )
    {
        $this->batches++;
        $answer = array();
        foreach ( $ids as $id )
            $answer[$id] = $this->byId( $id );
        return $answer;
    }
    public function byRemoteId( $remoteId, $language = false ) { return $this->byId( (int)substr( $remoteId, 3 ) ); }
    public function byPath( $path, $language = false )
    {
        foreach ( $this->tags as $tag )
            if ( $tag['keyword'] === basename( $path ) )
                return $tag;
        return null;
    }
    public function byKeyword( $keyword, $language = false ) { $t = $this->byPath( $keyword ); return $t ? array( $t ) : array(); }
    public function children( $parentId, $limit, $offset, $language = false )
    {
        $children = array();
        foreach ( $this->tags as $tag )
            if ( $tag['parentId'] === (int)$parentId && $tag['mainTagId'] === 0 )
                $children[] = $tag;
        return array_slice( $children, $offset, $limit );
    }
    public function childrenCount( $parentId ) { return count( $this->children( $parentId, 100, 0 ) ); }
    public function synonyms( $mainTagId, $language = false )
    {
        $synonyms = array();
        foreach ( $this->tags as $tag )
            if ( $tag['mainTagId'] === (int)$mainTagId )
                $synonyms[] = $tag;
        return $synonyms;
    }
    public function tagsOfField( eZContentObjectAttribute $field ) { return array( $this->tags[2], $this->tags[3] ); }
    public function mayRead( $user ) { return $this->mayRead; }
}

class expGraphQLTestTaggedRepository extends expGraphQLTestRepository
{
    /** @var array[] the params of children() */
    public $asked = array();

    public function children( expGraphQLLocation $parent, array $params )
    {
        $this->asked[] = $params;
        return parent::children( $parent, $params );
    }
}

class expGraphQLTagsExtensionTest extends PHPUnit\Framework\TestCase
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

    protected static function query( $query, expGraphQLTestTagsSource $source, &$repository = null )
    {
        $attribute = array( 'id' => 1, 'identifier' => 'keywords', 'datatype' => 'eztags', 'name' => 'Keywords', 'description' => '', 'required' => false, 'options' => array() );
        $catalogue = expGraphQLClassCatalogue::filtered( array( array( 'id' => 2, 'identifier' => 'article', 'name' => 'Article', 'description' => '',
                                                                       'attributes' => array( $attribute ) ) ), array() );
        $builder = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ), array( new expGraphQLTagsExtension( $source ) ) );
        $repository = new expGraphQLTestTaggedRepository( $catalogue, array() );
        $repository->byNode[2] = new expGraphQLTestContent( array( 'id' => 1, 'class' => 'article', 'name' => 'Home',
            'location' => new expGraphQLTestLocation( array( 'id' => 2, 'parentId' => 1, 'contentId' => 1, 'depth' => 1, 'pathString' => '/1/2/', 'urlAlias' => '' ) ),
            'fields' => array( 'keywords' => new eZContentObjectAttribute( array( 'data_type_string' => 'eztags', 'language_code' => 'eng-GB' ) ) ) ) );
        $repository->byNode[11] = new expGraphQLTestContent( array( 'id' => 11, 'class' => 'article', 'name' => 'Carbonara',
            'location' => new expGraphQLTestLocation( array( 'id' => 11, 'parentId' => 2, 'contentId' => 11, 'depth' => 2, 'pathString' => '/1/2/11/', 'urlAlias' => 'carbonara' ) ),
            'fields' => array() ) );
        $context = new expGraphQLContext( new eZUser( array( 'contentobject_id' => 14, 'login' => 'admin' ) ), expGraphQLContext::AUTH_TOKEN, $repository );
        return ( new expGraphQLServer( $builder ) )->execute( $query, null, null, $context );
    }

    public function testATagHasItsPlaceInTheTreeAndItsSynonyms()
    {
        $source = new expGraphQLTestTagsSource();
        $answer = self::query( '{ tag(id: 5) { id keyword remoteId isSynonym url parent { keyword } path { keyword } } pasta: tag(path: "Food/Pasta") { childrenCount
                                  children { keyword } synonyms { keyword isSynonym mainTag { keyword } } } top: tags { keyword childrenCount } named: tags(keyword: "Soup") { id } }', $source );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $data = $answer['data'];
        $this->assertSame( array( 'id' => 5, 'keyword' => 'Spaghetti', 'remoteId' => 'tag5', 'isSynonym' => false, 'url' => '/tags/view/Spaghetti',
                                  'parent' => array( 'keyword' => 'Pasta' ), 'path' => array( array( 'keyword' => 'Food' ), array( 'keyword' => 'Pasta' ) ) ), $data['tag'] );
        $this->assertSame( array( 'childrenCount' => 1, 'children' => array( array( 'keyword' => 'Spaghetti' ) ),
                                  'synonyms' => array( array( 'keyword' => 'Noodles', 'isSynonym' => true, 'mainTag' => array( 'keyword' => 'Pasta' ) ) ) ), $data['pasta'] );
        $this->assertSame( array( array( 'keyword' => 'Food', 'childrenCount' => 2 ) ), $data['top'] );
        $this->assertSame( array( array( 'id' => 3 ) ), $data['named'] );
    }

    public function testTheTagsOfALevelAreFetchedTogether()
    {
        $source = new expGraphQLTestTagsSource();
        $answer = self::query( '{ tags(parentId: 1) { keyword parent { keyword } } }', $source );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertSame( array( array( 'keyword' => 'Pasta', 'parent' => array( 'keyword' => 'Food' ) ), array( 'keyword' => 'Soup', 'parent' => array( 'keyword' => 'Food' ) ) ),
                           $answer['data']['tags'] );
        $this->assertSame( 1, $source->batches, 'the parents of every tag in one batch' );
    }

    public function testTheContentOfATagAndTheTagsOfAField()
    {
        $source = new expGraphQLTestTagsSource();
        $answer = self::query( '{ noodles: tag(id: 4) { content(limit: 5) { totalCount nodes { name } } } node(id: 2) { ... on article { keywords { id keyword } } } }', $source, $repository );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertSame( array( array( 'id' => 2, 'keyword' => 'Pasta' ), array( 'id' => 3, 'keyword' => 'Soup' ) ), $answer['data']['node']['keywords'] );
        $this->assertSame( array( array( 'name' => 'Carbonara' ) ), $answer['data']['noodles']['content']['nodes'] );
        $this->assertSame( array( 2 ), $repository->asked[0]['tagIds'], 'a synonym lists the content of its main tag' );
        $this->assertSame( 0, $repository->asked[0]['depth'], 'the whole subtree' );
    }

    public function testTagsNeedTheirPolicyAndOneWayToNameATag()
    {
        $source = new expGraphQLTestTagsSource();
        $this->assertSame( 'BAD_USER_INPUT', self::query( '{ tag(id: 1, remoteId: "tag1") { id } }', $source )['errors'][0]['extensions']['code'] );
        $source->mayRead = false;
        $answer = self::query( '{ tag(id: 1) { id } tags { id } node(id: 2) { ... on article { keywords { keyword } } } }', $source );
        $this->assertSame( array( 'tag' => null, 'tags' => array(), 'node' => array( 'keywords' => array( array( 'keyword' => 'Pasta' ), array( 'keyword' => 'Soup' ) ) ) ),
                           $answer['data'], 'without tags/read: no tag queries, but the tags of content the user may read' );
    }
}
