<?php
/**
 * The root query search of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Search"): the arguments reach the
 * repository as the search engine needs them, a page with cursors, the count, the engine and facets, the refusal of a
 * search without text or filter, and the facets of an engine that has them (ezfind's SearchExtras). No database: a
 * repository that searches the names of fixture content.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expGraphQLSchemaTest.php';

class expGraphQLTestSearchRepository extends expGraphQLTestRepository
{
    /** @var array[] the searches asked for */
    public $searches = array();

    public function search( array $params )
    {
        $this->searches[] = $params;
        $found = array();
        foreach ( $this->byNode as $content )
        {
            if ( stripos( $content->name(), (string)$params['text'] ) !== false && $this->isExposed( $content->classIdentifier() )
                 && ( empty( $params['classes'] ) || in_array( $content->classIdentifier(), $params['classes'], true ) ) )
                $found[] = $content;
        }
        return array( 'items' => array_slice( $found, $params['offset'], $params['limit'] ), 'count' => count( $found ), 'engine' => 'eZSearchEngine',
                      'facets' => $params['facets'] ? array( array( 'field' => 'class', 'values' => array( array( 'value' => '2', 'name' => 'Article', 'count' => count( $found ) ) ) ) ) : array() );
    }
}

/** The SearchExtras of an engine with facets (ezfind's ezfSearchResultInfo): read with attribute(). */
class expGraphQLTestSearchExtras
{
    /** @var array */
    public $values;

    public function __construct( array $values ) { $this->values = $values; }
    public function attribute( $name ) { return isset( $this->values[$name] ) ? $this->values[$name] : null; }
}

class expGraphQLSearchTest extends PHPUnit\Framework\TestCase
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

    /**
     * @param string $query
     * @param expGraphQLTestSearchRepository|null $repository
     * @return array
     */
    protected static function query( $query, &$repository = null )
    {
        $attribute = array( 'id' => 1, 'identifier' => 'title', 'datatype' => 'ezstring', 'name' => 'Title', 'description' => '', 'required' => false, 'options' => array() );
        $catalogue = expGraphQLClassCatalogue::filtered( array( array( 'id' => 2, 'identifier' => 'article', 'name' => 'Article', 'description' => '', 'attributes' => array( $attribute ) ),
                                                                array( 'id' => 4, 'identifier' => 'user', 'name' => 'User', 'description' => '', 'attributes' => array() ) ),
                                                         array( 'ExcludedClassList' => array( 'user' ) ) );
        $builder = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ) );
        $repository = new expGraphQLTestSearchRepository( $catalogue, array( 'ExcludedClassList' => array( 'user' ) ) );
        for ( $i = 1; $i <= 5; $i++ )
            $repository->byNode[10 + $i] = new expGraphQLTestContent( array( 'id' => 100 + $i, 'class' => 'article', 'name' => "Pasta $i",
                'location' => new expGraphQLTestLocation( array( 'id' => 10 + $i, 'parentId' => 2, 'contentId' => 100 + $i, 'depth' => 2, 'pathString' => '/1/2/' . ( 10 + $i ) . '/',
                                                                 'urlAlias' => "pasta-$i" ) ),
                'fields' => array() ) );
        $repository->byNode[30] = new expGraphQLTestContent( array( 'id' => 300, 'class' => 'user', 'name' => 'Pasta lover',
            'location' => new expGraphQLTestLocation( array( 'id' => 30, 'parentId' => 2, 'contentId' => 300, 'depth' => 2, 'pathString' => '/1/2/30/', 'urlAlias' => 'u' ) ),
            'fields' => array() ) );
        $context = new expGraphQLContext( new eZUser( array( 'contentobject_id' => 14, 'login' => 'admin' ) ), expGraphQLContext::AUTH_TOKEN, $repository );
        return ( new expGraphQLServer( $builder ) )->execute( $query, null, null, $context );
    }

    public function testASearchAnswersAPageWithItsCountAndEngine()
    {
        $answer = self::query( '{ search(text: "pasta", classes: ["article"], subtree: [2], sections: [1], publishedAfter: "2026-01-01", first: 2, facets: ["class"]) {
                                    totalCount engine nodes { name } pageInfo { hasNextPage endCursor } facets { field values { value name count } } } }', $repository );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $search = $answer['data']['search'];
        $this->assertSame( 5, $search['totalCount'], 'the user class is not in the schema' );
        $this->assertSame( 'eZSearchEngine', $search['engine'] );
        $this->assertSame( array( array( 'name' => 'Pasta 1' ), array( 'name' => 'Pasta 2' ) ), $search['nodes'] );
        $this->assertTrue( $search['pageInfo']['hasNextPage'] );
        $this->assertSame( array( array( 'field' => 'class', 'values' => array( array( 'value' => '2', 'name' => 'Article', 'count' => 5 ) ) ) ), $search['facets'] );
        $this->assertCount( 1, $repository->searches, 'one search for every field' );
        $asked = $repository->searches[0];
        $this->assertSame( array( 'pasta', array( 'article' ), array( 2 ), array( 1 ), '2026-01-01', array( 'class' ), 3, 0 ),
                           array( $asked['text'], $asked['classes'], $asked['subtree'], $asked['sections'], $asked['publishedAfter'], $asked['facets'],
                                  $asked['limit'], $asked['offset'] ), 'one more than the page, to know of a next page' );

        $next = self::query( '{ search(text: "pasta", first: 2, after: "' . $search['pageInfo']['endCursor'] . '") { nodes { name } pageInfo { hasPreviousPage } } }' );
        $this->assertSame( array( array( 'name' => 'Pasta 3' ), array( 'name' => 'Pasta 4' ) ), $next['data']['search']['nodes'] );
        $this->assertTrue( $next['data']['search']['pageInfo']['hasPreviousPage'] );
    }

    public function testASearchNeedsATextOrAFilter()
    {
        $answer = self::query( '{ search { totalCount } }' );
        $this->assertSame( 'BAD_USER_INPUT', $answer['errors'][0]['extensions']['code'] );
        $this->assertArrayNotHasKey( 'errors', self::query( '{ search(classes: ["article"]) { totalCount } }' ), 'a filter alone is a search (Solr finds by filters)' );
    }

    public function testTheFacetsOfAnEngineThatHasThem()
    {
        $extras = new expGraphQLTestSearchExtras( array( 'facet_fields' => array(
            array( 'field' => 'meta_class_identifier_ms', 'nameList' => array( 'article' => 'Article', 'folder' => 'Folder' ), 'countList' => array( 'article' => 7, 'folder' => 2 ) ),
            array( 'countList' => array( '1' => 9 ) ) ) ) );
        $this->assertSame( array( array( 'field' => 'class', 'values' => array( array( 'value' => 'article', 'name' => 'Article', 'count' => 7 ),
                                                                                array( 'value' => 'folder', 'name' => 'Folder', 'count' => 2 ) ) ),
                                  array( 'field' => 'section', 'values' => array( array( 'value' => '1', 'name' => '1', 'count' => 9 ) ) ) ),
                           expGraphQLRepository::searchFacets( $extras, array( 'class', 'section' ) ) );
        $this->assertSame( array(), expGraphQLRepository::searchFacets( null, array( 'class' ) ), 'the kernel engine has none' );
    }
}
