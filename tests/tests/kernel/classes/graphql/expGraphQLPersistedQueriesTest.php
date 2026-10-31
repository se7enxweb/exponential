<?php
/**
 * Persisted queries of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Persisted queries"): the store (by hash
 * and by name, shipped directories, removal), the request flow (an id, Apollo's hash with and without the text,
 * automatic persisted queries and who may store them, PersistedQueriesOnly), and the reading of a request's id and
 * extensions. No database: the store is a directory of its own under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLPersistedQueriesTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir;

    /** @var array settings changed by a test: name => old value */
    protected $changed = array();

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/expgraphqlpersisted-' . getmypid() . '-' . mt_rand();
    }

    protected function tearDown(): void
    {
        foreach ( $this->changed as $name => $value )
            expGraphQL::ini()->setVariable( 'GraphQLSettings', $name, $value );
        $this->changed = array();
        self::removeTree( $this->dir );
    }

    protected static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( (array)scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            is_dir( "$dir/$entry" ) ? self::removeTree( "$dir/$entry" ) : unlink( "$dir/$entry" );
        }
        rmdir( $dir );
    }

    protected function setting( $name, $value )
    {
        if ( !array_key_exists( $name, $this->changed ) )
            $this->changed[$name] = expGraphQL::setting( $name );
        expGraphQL::ini()->setVariable( 'GraphQLSettings', $name, $value );
    }

    protected static function context( $authMode )
    {
        $userId = $authMode === expGraphQLContext::AUTH_ANONYMOUS ? (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) : 14;
        $user = new eZUser( array( 'contentobject_id' => $userId, 'login' => 'x', 'email' => '', 'password_hash' => '', 'password_hash_type' => 2 ) );
        return new expGraphQLContext( $user, $authMode, new expGraphQLRepository( new expGraphQLClassCatalogue( array() ), array() ) );
    }

    public function testTheStoreFindsQueriesByHashAndName()
    {
        $store = new expGraphQLPersistedQueries( $this->dir . '/store' );
        $query = '{ siteaccess { name } }';
        $hash = $store->store( $query, 'site-name' );
        $this->assertSame( hash( 'sha256', $query ), $hash );
        $this->assertSame( $query, $store->find( $hash ) );
        $this->assertSame( $query, $store->find( 'site-name' ) );
        $this->assertNull( $store->find( 'other' ) );
        $this->assertNull( $store->find( '../etc/passwd' ), 'a name never leaves the store' );
        $this->assertSame( 1, $store->count() );
        $this->assertSame( $hash, $store->store( $query ), 'the same text is stored once' );
        $this->assertSame( 1, $store->count() );

        mkdir( $this->dir . '/shipped', 0777, true );
        file_put_contents( $this->dir . '/shipped/menu.graphql', '{ translations { code } }' );
        $both = new expGraphQLPersistedQueries( $this->dir . '/store', array( $this->dir . '/shipped' ) );
        $this->assertSame( '{ translations { code } }', $both->find( 'menu' ) );
        $this->assertSame( '{ translations { code } }', $both->find( hash( 'sha256', '{ translations { code } }' ) ) );
        $all = $both->all();
        $this->assertSame( array( 'site-name' ), $all[$hash]['names'] );
        $this->assertSame( 'store', $all[$hash]['source'] );
        $this->assertCount( 2, $all );

        $this->assertTrue( $both->remove( 'site-name' ) );
        $this->assertNull( $both->find( $hash ) );
        $this->assertNull( $both->find( 'site-name' ) );
        $this->assertFalse( $both->remove( 'menu' ), 'a shipped query is not in the store' );

        $this->assertTrue( expGraphQLPersistedQueries::isName( 'blog/latest-10' ) );
        $this->assertFalse( expGraphQLPersistedQueries::isName( 'a..b' ) );
        $this->assertFalse( expGraphQLPersistedQueries::isName( str_repeat( 'a', 64 ) . 'x' . str_repeat( 'b', 50 ) ) );
    }

    public function testARequestRunsAPersistedQueryByIdOrHash()
    {
        $store = new expGraphQLPersistedQueries( $this->dir . '/store' );
        $query = '{ translations { code } }';
        $hash = $store->store( $query, 'languages' );
        $anonymous = self::context( expGraphQLContext::AUTH_ANONYMOUS );

        $this->assertSame( array( 'query' => $query, 'hash' => $hash ), expGraphQLServer::resolvePersisted( array( 'id' => 'languages' ), $anonymous, $store ) );
        $this->assertSame( array( 'query' => $query, 'hash' => $hash ), expGraphQLServer::resolvePersisted( array( 'persistedHash' => $hash ), $anonymous, $store ) );
        $this->assertSame( expGraphQLError::PERSISTED_QUERY_NOT_FOUND, expGraphQLServer::resolvePersisted( array( 'id' => 'nope' ), $anonymous, $store )['error'][1] );
        $missing = expGraphQLServer::resolvePersisted( array( 'persistedHash' => str_repeat( 'a', 64 ) ), $anonymous, $store );
        $this->assertSame( array( 'PersistedQueryNotFound', 'PERSISTED_QUERY_NOT_FOUND' ), $missing['error'], 'the message Apollo clients look for' );
        $this->assertSame( expGraphQLError::BAD_USER_INPUT, expGraphQLServer::resolvePersisted( array( 'persistedHash' => 'xyz' ), $anonymous, $store )['error'][1] );
        $this->assertSame( array( 'query' => '{ a }', 'hash' => null ), expGraphQLServer::resolvePersisted( array( 'query' => '{ a }' ), $anonymous, $store ) );

        $this->setting( 'PersistedQueries', 'disabled' );
        $this->assertSame( expGraphQLError::PERSISTED_QUERY_NOT_SUPPORTED, expGraphQLServer::resolvePersisted( array( 'id' => 'languages' ), $anonymous, $store )['error'][1] );
    }

    public function testAutomaticPersistedQueriesAreStoredForWhoMay()
    {
        $store = new expGraphQLPersistedQueries( $this->dir . '/store' );
        $query = '{ currentUser { id } }';
        $hash = hash( 'sha256', $query );
        $request = array( 'query' => $query, 'persistedHash' => $hash );

        $wrong = expGraphQLServer::resolvePersisted( array( 'query' => $query, 'persistedHash' => str_repeat( 'b', 64 ) ), self::context( expGraphQLContext::AUTH_TOKEN ), $store );
        $this->assertSame( expGraphQLError::BAD_USER_INPUT, $wrong['error'][1], 'a hash that is not the query\'s' );

        // signed-in (the default): anonymous runs it ad hoc, a token stores it
        $this->assertSame( array( 'query' => $query, 'hash' => null ), expGraphQLServer::resolvePersisted( $request, self::context( expGraphQLContext::AUTH_ANONYMOUS ), $store ) );
        $this->assertSame( 0, $store->count() );
        $this->assertSame( array( 'query' => $query, 'hash' => $hash ), expGraphQLServer::resolvePersisted( $request, self::context( expGraphQLContext::AUTH_TOKEN ), $store ) );
        $this->assertSame( $query, $store->find( $hash ) );

        $this->setting( 'AutomaticPersistedQueries', 'disabled' );
        $other = array( 'query' => '{ a }', 'persistedHash' => hash( 'sha256', '{ a }' ) );
        $this->assertNull( expGraphQLServer::resolvePersisted( $other, self::context( expGraphQLContext::AUTH_TOKEN ), $store )['hash'] );
        $this->setting( 'AutomaticPersistedQueries', 'enabled' );
        $this->setting( 'MaxPersistedQueries', '1' );
        $this->assertNull( expGraphQLServer::resolvePersisted( $other, self::context( expGraphQLContext::AUTH_ANONYMOUS ), $store )['hash'], 'the store is full' );
        $this->setting( 'MaxPersistedQueries', '10' );
        $this->assertSame( hash( 'sha256', '{ a }' ), expGraphQLServer::resolvePersisted( $other, self::context( expGraphQLContext::AUTH_ANONYMOUS ), $store )['hash'] );
    }

    public function testPersistedQueriesOnlyRefusesAdHocQueries()
    {
        $store = new expGraphQLPersistedQueries( $this->dir . '/store' );
        $store->store( '{ a }', 'a' );
        $adHoc = array( 'query' => '{ b }' );
        $this->setting( 'PersistedQueriesOnly', 'anonymous' );
        $this->assertSame( expGraphQLError::PERSISTED_QUERY_REQUIRED, expGraphQLServer::resolvePersisted( $adHoc, self::context( expGraphQLContext::AUTH_ANONYMOUS ), $store )['error'][1] );
        $this->assertSame( '{ b }', expGraphQLServer::resolvePersisted( $adHoc, self::context( expGraphQLContext::AUTH_API_KEY ), $store )['query'] );
        $this->assertSame( '{ a }', expGraphQLServer::resolvePersisted( array( 'id' => 'a' ), self::context( expGraphQLContext::AUTH_ANONYMOUS ), $store )['query'] );
        $this->setting( 'PersistedQueriesOnly', 'enabled' );
        $this->assertSame( expGraphQLError::PERSISTED_QUERY_REQUIRED, expGraphQLServer::resolvePersisted( $adHoc, self::context( expGraphQLContext::AUTH_TOKEN ), $store )['error'][1] );
        $this->assertSame( '{ b }', expGraphQLServer::resolvePersisted( $adHoc, self::context( expGraphQLContext::AUTH_SESSION ), $store )['query'], 'the playground' );
        $this->assertSame( '{ b }', expGraphQLServer::resolvePersisted( $adHoc, self::context( expGraphQLContext::AUTH_CONSOLE ), $store )['query'] );
    }

    public function testARequestCarriesItsIdAndExtensions()
    {
        $get = expGraphQLServer::parseHttpRequest( 'GET', '', '', array( 'id' => 'articles', 'variables' => '{"n":5}' ), array() );
        $this->assertSame( 'articles', $get['id'] );
        $this->assertSame( array( 'n' => 5 ), $get['variables'] );
        $hash = str_repeat( 'A', 64 );
        $apollo = expGraphQLServer::parseHttpRequest( 'GET', '', '', array( 'extensions' => '{"persistedQuery":{"version":1,"sha256Hash":"' . $hash . '"}}' ), array() );
        $this->assertSame( strtolower( $hash ), $apollo['persistedHash'] );
        $this->assertSame( '', $apollo['query'] );
        $post = expGraphQLServer::parseHttpRequest( 'POST', 'application/json', json_encode( array( 'query' => '{ a }', 'extensions' => array( 'persistedQuery' => array( 'sha256Hash' => 'abc' ) ) ) ), array(), array() );
        $this->assertSame( 'abc', $post['persistedHash'] );
        $this->assertArrayHasKey( 'error', expGraphQLServer::parseHttpRequest( 'GET', '', '', array( 'extensions' => '{nope' ), array() ) );
    }

    public function testAQueryIsStoredOnlyWithinTheLimitsOfARequest()
    {
        // exp:graphql:persist stored a query of complexity 1051 under MaxComplexity=1000: every call of it was then
        // refused with COMPLEXITY_LIMIT. The check before storing applies the rules a request is checked against.
        if ( !expGraphQL::loadLibrary() )
            $this->markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        $catalogue = expGraphQLClassCatalogue::filtered( array( array( 'id' => 1, 'identifier' => 'folder', 'name' => 'Folder', 'description' => '',
            'attributes' => array( array( 'id' => 1, 'identifier' => 'short_name', 'datatype' => 'ezstring', 'name' => 'Short name', 'description' => '',
                                          'required' => false, 'options' => array() ) ) ) ), array() );
        $schema = ( new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ) ) )->schema();
        $this->setting( 'MaxComplexity', '1000' );
        $this->setting( 'MaxDepth', '12' );
        $small = '{ children(parentId: 2, limit: 3) { nodes { name } } }';
        $this->assertSame( array(), Exponential\Command\Kernel\Expgraphqlpersist::problems( $schema, $small ) );
        $wide = '{ children(parentId: 2, limit: 100) { nodes { name children(limit: 100) { nodes { name url } } } } }';
        $problems = Exponential\Command\Kernel\Expgraphqlpersist::problems( $schema, $wide );
        $this->assertCount( 1, $problems );
        $this->assertStringContainsString( 'Max query complexity should be 1000', $problems[0] );
        $context = self::context( expGraphQLContext::AUTH_CONSOLE );
        $this->assertSame( expGraphQLError::COMPLEXITY_LIMIT, ( new expGraphQLServer( new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ) ) ) )
            ->execute( $wide, null, null, $context )['errors'][0]['extensions']['code'], 'a request refuses what the check refuses' );
        // introspection is decided when the query is called, by who calls it
        $this->assertSame( array(), Exponential\Command\Kernel\Expgraphqlpersist::problems( $schema, '{ __schema { queryType { name } } }' ) );
        $this->setting( 'MaxDepth', '1' );
        $this->assertNotSame( array(), Exponential\Command\Kernel\Expgraphqlpersist::problems( $schema,
            '{ children(parentId: 2, limit: 3) { nodes { children(limit: 2) { nodes { name } } } } }' ), 'deeper than MaxDepth' );
    }

    public function testAQueryWithVariablesIsCheckedForItsWorstCall()
    {
        // The complexity rule reads the values of the variables, and before a request there are none: every query with
        // a required variable failed with 'Variable "$path" of required type "String!" was not provided', the kits'
        // Node and Content_* queries included (exp:graphql:starter, exp:graphql:persist of a kit's graphql/).
        if ( !expGraphQL::loadLibrary() )
            $this->markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        $catalogue = expGraphQLClassCatalogue::filtered( array( array( 'id' => 1, 'identifier' => 'folder', 'name' => 'Folder', 'description' => '',
            'attributes' => array( array( 'id' => 1, 'identifier' => 'short_name', 'datatype' => 'ezstring', 'name' => 'Short name', 'description' => '',
                                          'required' => false, 'options' => array() ) ) ) ), array() );
        $builder = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ) );
        $schema = $builder->schema();
        $this->setting( 'MaxComplexity', '1000' );
        $this->setting( 'MaxDepth', '12' );
        $this->setting( 'MaxListLimit', '100' );
        $problems = function ( $query ) use ( $schema ) {
            return Exponential\Command\Kernel\Expgraphqlpersist::problems( $schema, $query );
        };

        $this->assertSame( array(), $problems( 'query Node($path: String!) { node(urlAlias: $path) { id name urlAlias } }' ), 'a required String' );
        $this->assertSame( array(), $problems( 'query Kids($parent: Int!) { children(parentId: $parent, limit: 3) { nodes { name } } }' ), 'a required Int' );
        $this->assertSame( array(), $problems( 'query Kids($parent: Int!, $n: Int) { children(parentId: $parent, limit: $n) { nodes { name } } }' ),
                           'a limit from a variable, within the limit at MaxListLimit' );

        // a limit from a variable counts as MaxListLimit: one call with $n = 100 would exceed MaxComplexity
        $wide = 'query Kids($parent: Int!, $n: Int) { children(parentId: $parent, limit: $n) { nodes { name children(limit: $n) { nodes { name url } } } } }';
        $refused = $problems( $wide );
        $this->assertCount( 1, $refused );
        $this->assertStringContainsString( 'Max query complexity should be 1000', $refused[0] );
        $this->assertSame( array(), $problems( str_replace( array( ', $n: Int', 'limit: $n' ), array( '', 'limit: 3' ), $wide ) ), 'the same with small literal limits' );

        // a field that a condition may leave out counts: @include runs, @skip does not skip
        $nested = 'nodes { name children(limit: 100) %s { nodes { name url } } }';
        $this->assertNotSame( array(), $problems( 'query S($p: Int!, $hide: Boolean!) { children(parentId: $p, limit: 100) { ' . sprintf( $nested, '@skip(if: $hide)' ) . ' } }' ) );
        $this->assertNotSame( array(), $problems( 'query S($p: Int!, $show: Boolean!) { children(parentId: $p, limit: 100) { ' . sprintf( $nested, '@include(if: $show)' ) . ' } }' ) );

        $this->assertSame( array( 'path' => '1', 'n' => 100, 'hide' => false, 'show' => true, 'list' => array( 100 ) ),
            expGraphQLServer::worstCaseVariables( $schema, GraphQL\Language\Parser::parse(
                'query A($path: String!, $n: Int, $hide: Boolean!, $show: Boolean, $list: [Int!]) { node(urlAlias: $path) { id @skip(if: $hide) name @include(if: $show) } }' ) ) );

        // a request still checks the values it sends
        $context = self::context( expGraphQLContext::AUTH_CONSOLE );
        $server = new expGraphQLServer( $builder );
        $this->assertSame( expGraphQLError::COMPLEXITY_LIMIT, $server->execute( $wide, array( 'parent' => 2, 'n' => 100 ), null, $context )['errors'][0]['extensions']['code'] );
    }
}
