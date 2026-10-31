<?php
/**
 * The GraphQL API without a database (doc/eng-us/features/6.0/graphql.md): the names of the schema, the class
 * catalogue's filters, the schema generated from fixture classes (a type per class, a field per attribute with the
 * type of its datatype), queries run against fixture content (fields, lists with offsets and cursors, inline
 * fragments), the limits (depth, complexity), introspection for anonymous and signed-in requests, the error codes,
 * and the reading of an HTTP request.
 *
 * webonyx/graphql-php comes from the Composer autoload (composer.json requires it), else from the environment
 * variable GRAPHQL_LIBRARY_PATH; without it the test is skipped (and fails with CI=1).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLTestLocation extends expGraphQLLocation
{
    /** @var array */
    public $data;

    public function __construct( array $data, $language = false )
    {
        parent::__construct( null, $language );
        $this->data = $data + array( 'priority' => 0, 'hidden' => false, 'invisible' => false, 'remoteId' => 'loc' . $data['id'],
                                     'sortArray' => array( array( 'priority', true ) ) );
    }

    public function id() { return $this->data['id']; }
    public function parentId() { return $this->data['parentId']; }
    public function contentId() { return $this->data['contentId']; }
    public function depth() { return $this->data['depth']; }
    public function pathString() { return $this->data['pathString']; }
    public function priority() { return $this->data['priority']; }
    public function hidden() { return $this->data['hidden']; }
    public function invisible() { return $this->data['invisible']; }
    public function urlAlias() { return $this->data['urlAlias']; }
    public function remoteId() { return $this->data['remoteId']; }
    public function sortArray() { return $this->data['sortArray']; }
}

class expGraphQLTestContent extends expGraphQLContent
{
    /** @var array */
    public $data;

    public function __construct( array $data )
    {
        parent::__construct( null, null, false );
        $this->data = $data;
    }

    public function id() { return $this->data['id']; }
    public function remoteId() { return 'remote' . $this->data['id']; }
    public function classIdentifier() { return $this->data['class']; }
    public function className() { return ucfirst( $this->data['class'] ); }
    public function language() { return 'eng-GB'; }
    public function languages() { return array( 'eng-GB' ); }
    public function mainLanguage() { return 'eng-GB'; }
    public function name() { return $this->data['name']; }
    public function published() { return 1767225600; }
    public function modified() { return 1767225600; }
    public function ownerId() { return 14; }
    public function ownerName() { return 'Administrator User'; }
    public function sectionId() { return 1; }
    public function dataMap() { return $this->data['fields']; }
    public function location() { return $this->data['location']; }
    public function mainLocation() { return $this->data['location']; }
    public function locations() { return array( $this->data['location'] ); }
}

class expGraphQLTestRepository extends expGraphQLRepository
{
    /** @var expGraphQLTestContent[] node id => content */
    public $byNode = array();

    /** @var int how often children() was asked */
    public $childrenCalls = 0;

    public function contentByNodeId( $nodeId, $language = false )
    {
        return isset( $this->byNode[$nodeId] ) && $this->isExposed( $this->byNode[$nodeId]->classIdentifier() ) ? $this->byNode[$nodeId] : null;
    }

    public function locationById( $nodeId, $language = false )
    {
        return isset( $this->byNode[$nodeId] ) ? $this->byNode[$nodeId]->location() : null;
    }

    public function contentAtLocation( expGraphQLLocation $location )
    {
        return $this->contentByNodeId( $location->id() );
    }

    protected function listed( expGraphQLLocation $parent, array $params )
    {
        $items = array();
        foreach ( $this->byNode as $content )
        {
            if ( $content->location()->parentId() !== $parent->id() || !$this->isExposed( $content->classIdentifier() ) )
                continue;
            if ( !empty( $params['classes'] ) && !in_array( $content->classIdentifier(), $params['classes'], true ) )
                continue;
            $items[] = $content;
        }
        return $items;
    }

    public function children( expGraphQLLocation $parent, array $params )
    {
        $this->childrenCalls++;
        return array_slice( $this->listed( $parent, $params ), (int)$params['offset'], (int)$params['limit'] );
    }

    public function childrenCount( expGraphQLLocation $parent, array $params )
    {
        return count( $this->listed( $parent, $params ) );
    }

    public function related( $objectId, $language = false )
    {
        foreach ( $this->byNode as $content )
        {
            if ( $content->id() === (int)$objectId )
                return $content;
        }
        return null;
    }

    /** @var string[] the batches asked for: kind:number of keys */
    public $batches = array();

    public function relatedList( array $objectIds, $language = false )
    {
        $this->batches[] = 'content:' . count( $objectIds );
        $answer = array();
        foreach ( $objectIds as $id )
            $answer[$id] = $this->related( $id, $language );
        return $answer;
    }

    public function contentByNodeIdList( array $nodeIds, $language = false )
    {
        $this->batches[] = 'node:' . count( $nodeIds );
        $answer = array();
        foreach ( $nodeIds as $id )
            $answer[$id] = $this->contentByNodeId( $id, $language );
        return $answer;
    }

    public function objectNames( array $objectIds )
    {
        $this->batches[] = 'names:' . count( $objectIds );
        return array_fill_keys( $objectIds, 'Administrator User' );
    }

    public function primeUrlAliases( array $items )
    {
        $this->batches[] = 'urls:' . count( $items );
    }

    public function languages()
    {
        return array( array( 'code' => 'eng-GB', 'name' => 'English (United Kingdom)' ) );
    }

    public function siteAccess()
    {
        return array( 'name' => 'site', 'languages' => array( 'eng-GB' ), 'rootNodeId' => 2 );
    }
}

class expGraphQLSchemaTest extends PHPUnit\Framework\TestCase
{
    /** @var array settings changed by a test: name => old value */
    protected $changed = array();

    public static function setUpBeforeClass(): void
    {
        if ( !expGraphQL::loadLibrary() )
        {
            if ( getenv( 'CI' ) )
                self::fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
            self::markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        }
    }

    protected function tearDown(): void
    {
        foreach ( $this->changed as $name => $value )
            expGraphQL::ini()->setVariable( 'GraphQLSettings', $name, $value );
        $this->changed = array();
    }

    protected function setting( $name, $value )
    {
        if ( !array_key_exists( $name, $this->changed ) )
            $this->changed[$name] = expGraphQL::setting( $name );
        expGraphQL::ini()->setVariable( 'GraphQLSettings', $name, $value );
    }

    /** @return array the fixture classes */
    protected static function classes()
    {
        $attribute = function ( $identifier, $datatype, array $options = array() ) {
            return array( 'id' => crc32( $identifier ), 'identifier' => $identifier, 'datatype' => $datatype, 'name' => ucfirst( $identifier ),
                          'description' => '', 'required' => false, 'options' => $options );
        };
        return array(
            array( 'id' => 1, 'identifier' => 'folder', 'name' => 'Folder', 'description' => '',
                   'attributes' => array( $attribute( 'name', 'ezstring' ), $attribute( 'short_name', 'ezstring' ), $attribute( 'description', 'ezxmltext' ) ) ),
            array( 'id' => 2, 'identifier' => 'article', 'name' => 'Article', 'description' => 'A news article',
                   'attributes' => array( $attribute( 'title', 'ezstring' ), $attribute( 'intro', 'ezxmltext' ), $attribute( 'image', 'ezimage' ),
                                          $attribute( 'rating', 'ezinteger' ), $attribute( 'score', 'ezfloat' ), $attribute( 'featured', 'ezboolean' ),
                                          $attribute( 'publish_date', 'ezdate' ), $attribute( 'tags', 'ezkeyword' ),
                                          $attribute( 'kind', 'ezselection', array( 1 => 'News', 2 => 'Opinion' ) ),
                                          $attribute( 'related', 'ezobjectrelationlist' ), $attribute( 'location', 'ezgmaplocation' ),
                                          $attribute( 'author_account', 'ezuser' ) ) ),
            array( 'id' => 3, 'identifier' => 'image', 'name' => 'Image', 'description' => '',
                   'attributes' => array( $attribute( 'name', 'ezstring' ), $attribute( 'image', 'ezimage' ) ) ),
            array( 'id' => 4, 'identifier' => 'user', 'name' => 'User', 'description' => '',
                   'attributes' => array( $attribute( 'first_name', 'ezstring' ), $attribute( 'user_account', 'ezuser' ) ) ),
            array( 'id' => 5, 'identifier' => 'news-item', 'name' => 'News item', 'description' => '',
                   'attributes' => array( $attribute( '2nd-title', 'ezstring' ) ) ),
        );
    }

    protected static function settings()
    {
        return array( 'ClassList' => array(), 'ExcludedClassList' => array( 'user', 'user_group' ), 'HiddenFieldList' => array( 'article/score' ),
                      'HiddenDatatypeList' => array( 'ezuser', 'ezpassword' ) );
    }

    protected static function builder( $style = 'identifier' )
    {
        return new expGraphQLSchemaBuilder( expGraphQLClassCatalogue::filtered( self::classes(), self::settings() ), new expGraphQLNaming( $style ) );
    }

    /**
     * @param string $datatype
     * @param array $row
     * @return eZContentObjectAttribute
     */
    protected static function field( $datatype, array $row )
    {
        return new eZContentObjectAttribute( $row + array( 'data_type_string' => $datatype, 'data_text' => '', 'data_int' => 0, 'data_float' => 0,
                                                           'language_code' => 'eng-GB', 'contentobject_id' => 0, 'id' => 0, 'version' => 1 ) );
    }

    protected static function repository( expGraphQLSchemaBuilder $builder )
    {
        $repository = new expGraphQLTestRepository( $builder->catalogue(), self::settings() );
        $root = new expGraphQLTestContent( array( 'id' => 1, 'class' => 'folder', 'name' => 'Home',
            'location' => new expGraphQLTestLocation( array( 'id' => 2, 'parentId' => 1, 'contentId' => 1, 'depth' => 1, 'pathString' => '/1/2/', 'urlAlias' => '' ) ),
            'fields' => array( 'name' => self::field( 'ezstring', array( 'data_text' => 'Home' ) ),
                               'short_name' => self::field( 'ezstring', array( 'data_text' => 'Start' ) ) ) ) );
        $repository->byNode[2] = $root;
        for ( $i = 1; $i <= 5; $i++ )
        {
            $node = 10 + $i;
            $repository->byNode[$node] = new expGraphQLTestContent( array( 'id' => 100 + $i, 'class' => 'article', 'name' => "Article $i",
                'location' => new expGraphQLTestLocation( array( 'id' => $node, 'parentId' => 2, 'contentId' => 100 + $i, 'depth' => 2,
                                                                 'pathString' => "/1/2/$node/", 'urlAlias' => "article-$i" ) ),
                'fields' => array( 'title' => self::field( 'ezstring', array( 'data_text' => "Title $i" ) ),
                                   'rating' => self::field( 'ezinteger', array( 'data_int' => $i ) ),
                                   'featured' => self::field( 'ezboolean', array( 'data_int' => $i % 2 ) ),
                                   'publish_date' => self::field( 'ezdate', array( 'data_int' => 1767225600 ) ),
                                   'kind' => self::field( 'ezselection', array( 'data_text' => '2' ) ),
                                   'related' => self::field( 'ezobjectrelationlist', array( 'data_text' => '<?xml version="1.0" encoding="utf-8"?><related-objects><relation-list>'
                                       . '<relation-item priority="1" contentobject-id="' . ( 100 + ( $i % 5 ) + 1 ) . '" contentobject-version="1" node-id="0" parent-node-id="0" contentclass-id="2" contentclass-identifier="article"/>'
                                       . '<relation-item priority="2" contentobject-id="1" contentobject-version="1" node-id="0" parent-node-id="0" contentclass-id="1" contentclass-identifier="folder"/>'
                                       . '</relation-list></related-objects>' ) ),
                                   'intro' => self::field( 'ezxmltext', array( 'data_text' => '<?xml version="1.0"?><section><paragraph>Intro <strong>' . $i . '</strong></paragraph><paragraph>Second</paragraph></section>' ) ) ) ) );
        }
        $repository->byNode[30] = new expGraphQLTestContent( array( 'id' => 300, 'class' => 'user', 'name' => 'Someone',
            'location' => new expGraphQLTestLocation( array( 'id' => 30, 'parentId' => 2, 'contentId' => 300, 'depth' => 2, 'pathString' => '/1/2/30/', 'urlAlias' => 'someone' ) ),
            'fields' => array() ) );
        return $repository;
    }

    /**
     * @param string $query
     * @param string $authMode
     * @param array|null $variables
     * @return array
     */
    protected static function query( $query, $authMode = expGraphQLContext::AUTH_TOKEN, $variables = null, $builder = null )
    {
        $builder = $builder ?: self::builder();
        $userId = $authMode === expGraphQLContext::AUTH_ANONYMOUS ? (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) : 14;
        $user = new eZUser( array( 'contentobject_id' => $userId, 'login' => $userId === 14 ? 'admin' : 'anonymous', 'email' => '', 'password_hash' => '',
                                   'password_hash_type' => 2 ) );
        $context = new expGraphQLContext( $user, $authMode, self::repository( $builder ), 'https://example.com' );
        $server = new expGraphQLServer( $builder );
        return $server->execute( $query, $variables, null, $context );
    }

    protected static function codes( array $answer )
    {
        $codes = array();
        foreach ( isset( $answer['errors'] ) ? $answer['errors'] : array() as $error )
            $codes[] = $error['extensions']['code'];
        return $codes;
    }

    public function testNamesFollowTheChosenStyle()
    {
        $identifier = new expGraphQLNaming( 'identifier' );
        $this->assertSame( 'ng_article', $identifier->typeName( 'ng_article' ) );
        $this->assertSame( 'short_title', $identifier->fieldName( 'ng_article', 'short_title' ) );
        $this->assertSame( 'name_field', $identifier->fieldName( 'folder', 'name' ) );
        $this->assertSame( 'children_field', $identifier->fieldName( 'folder', 'children' ) );
        $this->assertSame( 'news_item', $identifier->typeName( 'news-item' ) );
        $this->assertSame( '_2nd_title', $identifier->fieldName( 'news-item', '2nd-title' ) );
        $this->assertSame( '_x', expGraphQLNaming::valid( '__x' ) );

        $pascal = new expGraphQLNaming( 'pascal' );
        $this->assertSame( 'NgArticle', $pascal->typeName( 'ng_article' ) );
        $this->assertSame( 'shortTitle', $pascal->fieldName( 'ng_article', 'short_title' ) );
        $this->assertSame( 'ImageContent', $pascal->typeName( 'image' ), 'a class may not take the name of a type of the schema' );
        $this->assertSame( 'image', $identifier->typeName( 'image' ) );

        $map = new expGraphQLNaming( 'map', array( 'ng_article' => 'Article' ), array( 'ng_article/short_title' => 'teaser' ) );
        $this->assertSame( 'Article', $map->typeName( 'ng_article' ) );
        $this->assertSame( 'teaser', $map->fieldName( 'ng_article', 'short_title' ) );
        $this->assertSame( 'folder', $map->typeName( 'folder' ) );

        $this->assertSame( 'identifier', ( new expGraphQLNaming( 'nonsense' ) )->style() );
    }

    public function testTheCatalogueLeavesOutExcludedClassesAndHiddenFields()
    {
        $catalogue = expGraphQLClassCatalogue::filtered( self::classes(), self::settings() );
        $this->assertSame( array( 'folder', 'article', 'image', 'news-item' ), $catalogue->identifiers() );
        $attributes = array_column( $catalogue->classByIdentifier( 'article' )['attributes'], 'identifier' );
        $this->assertNotContains( 'score', $attributes, 'HiddenFieldList[]' );
        $this->assertNotContains( 'author_account', $attributes, 'HiddenDatatypeList[]' );
        $this->assertContains( 'title', $attributes );

        $narrow = expGraphQLClassCatalogue::filtered( self::classes(), array( 'ClassList' => array( 'article', 'user' ) ) + self::settings() );
        $this->assertSame( array( 'article', 'user' ), $narrow->identifiers(), 'ClassList[] names the classes, even an excluded one' );

        $this->assertSame( $catalogue->hash(), expGraphQLClassCatalogue::filtered( self::classes(), self::settings() )->hash() );
        $this->assertNotSame( $catalogue->hash(), $narrow->hash() );
    }

    public function testTheSchemaHasATypePerClassAndAFieldPerAttribute()
    {
        $sdl = self::builder()->sdl();
        $this->assertStringContainsString( 'interface Content {', $sdl );
        $this->assertStringContainsString( 'type folder implements Content {', $sdl );
        $this->assertStringContainsString( 'type article implements Content {', $sdl );
        $this->assertStringContainsString( 'type news_item implements Content {', $sdl );
        $this->assertStringNotContainsString( 'type user implements', $sdl );
        $this->assertMatchesRegularExpression( '/name_field: String\b/', $sdl );
        $this->assertMatchesRegularExpression( '/short_name: String\b/', $sdl );
        foreach ( array( 'title: String', 'intro: RichText', 'image: Image', 'rating: Int', 'featured: Boolean', 'publish_date: String',
                         'tags: [String!]!', 'kind: [SelectionOption!]!', 'related: [Content!]!', 'location_field: GeoLocation' ) as $field )
            $this->assertStringContainsString( $field, $sdl );
        $this->assertStringNotContainsString( 'score:', $sdl );
        $flat = preg_replace( '/\s*"[^"\n]*"\s*|\s+/', ' ', $sdl );
        $this->assertStringContainsString( 'children( classes: [String!] sortField: ContentSortField sortOrder: SortOrder depth: Int = 1 limit: Int offset: Int first: Int after: String ): ContentConnection!', $flat );
        $this->assertStringContainsString( 'node( id: Int remoteId: String path: String urlAlias: String language: String ): Content', $flat );

        $pascal = self::builder( 'pascal' )->sdl();
        $this->assertStringContainsString( 'type ImageContent implements Content {', $pascal );
        $this->assertStringContainsString( 'publishDate: String', $pascal );

        $summary = self::builder()->summary();
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{12}$/', $summary['schemaHash'] );
        $fields = array_column( $summary['classes'][1]['fields'], 'mapped', 'attribute' );
        $this->assertTrue( $fields['title'] );
        $this->assertTrue( $fields['location'], 'ezgmaplocation has a type of its own (GeoLocation)' );
        $this->assertNotSame( self::builder()->schemaHash(), self::builder( 'pascal' )->schemaHash() );
    }

    public function testANodeIsReadWithItsFieldsAndChildren()
    {
        $answer = self::query( '{ node(id: 2) { id name classIdentifier url location { id depth pathString }
                                  ... on folder { short_name name_field }
                                  children(limit: 2) { totalCount nodes { name ... on article { title rating featured publish_date kind { id name }
                                                                                                    intro { text excerpt(length: 8) } } }
                                                       pageInfo { hasNextPage hasPreviousPage endCursor } } } }' );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $node = $answer['data']['node'];
        $this->assertSame( 1, $node['id'] );
        $this->assertSame( 'Home', $node['name'] );
        $this->assertSame( 'folder', $node['classIdentifier'] );
        $this->assertSame( 'Start', $node['short_name'] );
        $this->assertSame( 'Home', $node['name_field'] );
        $this->assertSame( array( 'id' => 2, 'depth' => 1, 'pathString' => '/1/2/' ), $node['location'] );
        $this->assertStringStartsWith( 'https://example.com/', $node['url'] );
        $children = $node['children'];
        $this->assertSame( 5, $children['totalCount'], 'the user class is not in the schema, so not in the list' );
        $this->assertCount( 2, $children['nodes'] );
        $this->assertSame( array( 'name' => 'Article 1', 'title' => 'Title 1', 'rating' => 1, 'featured' => true, 'publish_date' => '2026-01-01',
                                  'kind' => array( array( 'id' => 2, 'name' => 'Opinion' ) ),
                                  'intro' => array( 'text' => "Intro 1\nSecond", 'excerpt' => 'Intro 1…' ) ), $children['nodes'][0] );
        $this->assertTrue( $children['pageInfo']['hasNextPage'] );
        $this->assertFalse( $children['pageInfo']['hasPreviousPage'] );

        $after = $children['pageInfo']['endCursor'];
        $next = self::query( 'query($after: String) { children(parentId: 2, first: 10, after: $after) { edges { cursor node { name } } pageInfo { hasNextPage hasPreviousPage } } }',
                           expGraphQLContext::AUTH_TOKEN, array( 'after' => $after ) );
        $this->assertArrayNotHasKey( 'errors', $next, json_encode( $next ) );
        $this->assertSame( array( 'Article 3', 'Article 4', 'Article 5' ), array_column( array_column( $next['data']['children']['edges'], 'node' ), 'name' ) );
        $this->assertFalse( $next['data']['children']['pageInfo']['hasNextPage'] );
        $this->assertTrue( $next['data']['children']['pageInfo']['hasPreviousPage'] );

        $offset = self::query( '{ children(parentId: 2, limit: 1, offset: 4, classes: ["article"]) { nodes { name } } }' );
        $this->assertSame( array( array( 'name' => 'Article 5' ) ), $offset['data']['children']['nodes'] );
    }

    public function testWhatIsNotThereIsNull()
    {
        $answer = self::query( '{ a: node(id: 30) { id } b: node(id: 999) { id } }' );
        $this->assertArrayNotHasKey( 'errors', $answer );
        $this->assertSame( array( 'a' => null, 'b' => null ), $answer['data'], 'a class outside the schema reads as not there' );
    }

    public function testTheRootQueriesAnswer()
    {
        $answer = self::query( '{ currentUser { id anonymous signedInBy } siteaccess { name rootNodeId schemaHash } translations { code } }',
                             expGraphQLContext::AUTH_ANONYMOUS );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertTrue( $answer['data']['currentUser']['anonymous'] );
        $this->assertSame( 'anonymous', $answer['data']['currentUser']['signedInBy'] );
        $this->assertSame( self::builder()->schemaHash(), $answer['data']['siteaccess']['schemaHash'] );
        $this->assertSame( array( array( 'code' => 'eng-GB' ) ), $answer['data']['translations'] );
        $this->assertSame( self::builder()->schemaHash(), $answer['extensions']['schemaHash'] );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{16}$/', $answer['extensions']['requestId'] );
    }

    public function testErrorsCarryTheirCode()
    {
        $this->assertSame( array( 'VALIDATION' ), self::codes( self::query( '{ node(id: 2) { nosuchfield } }' ) ) );
        $this->assertSame( array( 'PARSE' ), self::codes( self::query( '{ node(id: 2) { id }' ) ) );
        $this->assertSame( array( 'BAD_USER_INPUT' ), self::codes( self::query( '{ node { id } }' ) ) );
        $this->assertSame( array( 'BAD_USER_INPUT' ), self::codes( self::query( '{ children(parentId: 2, after: "nonsense") { totalCount } }' ) ) );
        $this->assertSame( array( 'BAD_USER_INPUT' ), self::codes( self::query( '' ) ) );
        // mutations switched off (graphql.ini MutationsEnabled, the default): refused before validation
        $this->setting( 'MutationsEnabled', 'disabled' );
        $this->assertSame( array( 'DISABLED' ), self::codes( self::query( 'mutation { x }' ) ) );
        $answer = self::query( '{ node(id: 2) { nosuchfield } }' );
        $this->assertSame( $answer['extensions']['requestId'], $answer['errors'][0]['extensions']['requestId'] );
        $this->assertArrayNotHasKey( 'trace', $answer['errors'][0]['extensions'] );
    }

    public function testTheLimitsRefuseAQueryBeforeItRuns()
    {
        $this->setting( 'MaxDepth', '3' );
        $deep = self::query( '{ node(id: 2) { children { nodes { children { nodes { id } } } } } }' );
        $this->assertSame( array( 'DEPTH_LIMIT' ), self::codes( $deep ) );
        $this->assertArrayNotHasKey( 'data', $deep );
        $this->setting( 'MaxDepth', '12' );

        $this->setting( 'MaxComplexity', '50' );
        $repository = null;
        $complex = self::query( '{ node(id: 2) { children(limit: 100) { nodes { id name } } } }' );
        $this->assertSame( array( 'COMPLEXITY_LIMIT' ), self::codes( $complex ) );
        $this->assertSame( array(), self::codes( self::query( '{ node(id: 2) { children(limit: 5) { nodes { id name } } } }' ) ) );
    }

    public function testIntrospectionIsForSignedInRequests()
    {
        $query = '{ __schema { queryType { name } } }';
        $this->assertSame( array( 'INTROSPECTION_DISABLED' ), self::codes( self::query( $query, expGraphQLContext::AUTH_ANONYMOUS ) ) );
        $this->assertSame( 'Query', self::query( $query, expGraphQLContext::AUTH_API_KEY )['data']['__schema']['queryType']['name'] );
        $this->assertSame( 'Query', self::query( $query, expGraphQLContext::AUTH_SESSION )['data']['__schema']['queryType']['name'] );
        $this->setting( 'Introspection', 'enabled' );
        $this->assertSame( array(), self::codes( self::query( $query, expGraphQLContext::AUTH_ANONYMOUS ) ) );
        $this->setting( 'Introspection', 'disabled' );
        $this->assertSame( array( 'INTROSPECTION_DISABLED' ), self::codes( self::query( $query, expGraphQLContext::AUTH_TOKEN ) ) );
        $this->assertSame( array(), self::codes( self::query( $query, expGraphQLContext::AUTH_CONSOLE ) ) );
    }

    public function testAListIsFetchedOnceForItsFields()
    {
        $builder = self::builder();
        $repository = self::repository( $builder );
        $user = new eZUser( array( 'contentobject_id' => 14, 'login' => 'admin' ) );
        $context = new expGraphQLContext( $user, expGraphQLContext::AUTH_TOKEN, $repository );
        $answer = ( new expGraphQLServer( $builder ) )->execute( '{ node(id: 2) { children { nodes { id } edges { cursor } pageInfo { hasNextPage } } } }', null, null, $context );
        $this->assertArrayNotHasKey( 'errors', $answer );
        $this->assertSame( 1, $repository->childrenCalls );
    }

    public function testALevelOfAListIsFetchedInOneBatchPerKind()
    {
        $builder = self::builder();
        $repository = self::repository( $builder );
        $user = new eZUser( array( 'contentobject_id' => 14, 'login' => 'admin' ) );
        $context = new expGraphQLContext( $user, expGraphQLContext::AUTH_TOKEN, $repository );
        $answer = ( new expGraphQLServer( $builder ) )->execute(
            '{ node(id: 2) { children { nodes { name ownerName url urlAlias parent { name } ... on article { related { name ownerName } } } } } }', null, null, $context );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $nodes = $answer['data']['node']['children']['nodes'];
        $this->assertCount( 5, $nodes );
        $this->assertSame( 'Administrator User', $nodes[0]['ownerName'] );
        $this->assertSame( 'Home', $nodes[0]['parent']['name'] );
        $this->assertSame( array( array( 'name' => 'Article 2', 'ownerName' => 'Administrator User' ), array( 'name' => 'Home', 'ownerName' => 'Administrator User' ) ),
                           $nodes[0]['related'] );
        $this->assertSame( 'Article 1', $nodes[4]['related'][0]['name'] );
        // 5 items: one batch of owners, one of parents, one of relations, one of URL aliases, whatever the page size
        sort( $repository->batches );
        $this->assertSame( array( 'content:6', 'names:1', 'node:1', 'urls:5' ), $repository->batches );
        $stats = $context->loaderStats();
        $this->assertSame( 1, $stats['names|']['batches'] );
        $this->assertSame( 1, $stats['content|eng-GB']['batches'] );
    }

    public function testABatchLoaderFetchesPendingKeysOnceAndRemembers()
    {
        $calls = array();
        $loader = new expGraphQLBatchLoader( function ( array $keys ) use ( &$calls ) {
            $calls[] = $keys;
            $values = array();
            foreach ( $keys as $key )
                if ( $key !== 3 )
                    $values[$key] = "v$key";
            return $values;
        } );
        $loader->want( 1 );
        $loader->want( 2 );
        $loader->want( 3 );
        $this->assertSame( 'v2', $loader->value( 2 ) );
        $this->assertNull( $loader->value( 3 ), 'a key the batch leaves out is null' );
        $this->assertSame( 'v1', $loader->value( 1 ) );
        $loader->prime( 4, 'primed' );
        $this->assertSame( 'primed', $loader->value( 4 ) );
        $this->assertSame( array( array( 1, 2, 3 ) ), $calls );
        $this->assertSame( 1, $loader->batches );
        $this->assertSame( 3, $loader->keys );
    }

    public function testAnHttpRequestIsRead()
    {
        $json = expGraphQLServer::parseHttpRequest( 'POST', 'application/json; charset=utf-8', '{"query":"{ a }","variables":{"x":1},"operationName":"Q"}', array(), array() );
        $this->assertSame( array( 'query' => '{ a }', 'variables' => array( 'x' => 1 ), 'operationName' => 'Q', 'id' => null, 'persistedHash' => null, 'method' => 'POST' ), $json );
        $get = expGraphQLServer::parseHttpRequest( 'GET', '', '', array( 'query' => '{ b }', 'variables' => '{"y":2}' ), array() );
        $this->assertSame( array( 'query' => '{ b }', 'variables' => array( 'y' => 2 ), 'operationName' => null, 'id' => null, 'persistedHash' => null, 'method' => 'GET' ), $get );
        $graphql = expGraphQLServer::parseHttpRequest( 'POST', 'application/graphql', '{ c }', array(), array() );
        $this->assertSame( '{ c }', $graphql['query'] );
        $this->assertArrayHasKey( 'error', expGraphQLServer::parseHttpRequest( 'POST', 'application/json', '[1', array(), array() ) );
        $this->assertArrayHasKey( 'error', expGraphQLServer::parseHttpRequest( 'PUT', '', '', array(), array() ) );
        $this->assertArrayHasKey( 'error', expGraphQLServer::parseHttpRequest( 'GET', '', '', array( 'query' => '{ b }', 'variables' => 'x' ), array() ) );
    }

    public function testPagesAndCursors()
    {
        $this->assertSame( array( 25, 0 ), expGraphQLConnection::page( array() ) );
        $this->assertSame( array( 100, 0 ), expGraphQLConnection::page( array( 'limit' => 5000 ) ), 'MaxListLimit' );
        $this->assertSame( array( 3, 8 ), expGraphQLConnection::page( array( 'first' => 3, 'limit' => 9, 'after' => expGraphQLConnection::encodeCursor( 7 ), 'offset' => 1 ) ) );
        $this->assertNull( expGraphQLConnection::decodeCursor( 'bm9wZQ==' ) );
        $this->assertSame( 12, expGraphQLConnection::decodeCursor( expGraphQLConnection::encodeCursor( 12 ) ) );
    }

    public function testRichTextAsPlainText()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?><section><header>Title</header><paragraph>One &amp; <emphasize>two</emphasize></paragraph><ul><li><paragraph>a</paragraph></li></ul></section>';
        $this->assertSame( "Title\nOne & two\na", expGraphQLRichText::plainText( $xml ) );
        $text = new expGraphQLRichText( '<section><paragraph>' . str_repeat( 'word ', 30 ) . '</paragraph></section>' );
        $this->assertSame( 'word word…', $text->excerpt( 12 ) );
        $this->assertNull( $text->html(), 'no field, no HTML' );
    }
}
