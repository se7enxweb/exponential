<?php
/**
 * The GraphQL API without its library, without a database (doc/eng-us/features/6.0/graphql.md, "Installing the
 * library"): composer.json only suggests webonyx/graphql-php, so a kernel installed by a platform whose GraphQL bundle
 * brings another version (ibexa/graphql-php 0.13, graphql-php 14), or by nobody, still installs and boots. The API
 * then says why it cannot run (missing, or another major version), the status degrades, and /api/graphql answers 503
 * with a GraphQL error instead of a fatal error. The library's absence is simulated with expGraphQL::useLibraryClasses(),
 * so this runs alike with and without the library; tests/bin/bootsmoke.php checks the real absence on PHP 7.4 and 8.0.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLLibraryMissingTest extends PHPUnit\Framework\TestCase
{
    /** @var array */
    protected $settings = array();

    /** @var array */
    protected $server;

    protected function setUp(): void
    {
        parent::setUp();
        $ini = eZINI::instance( 'graphql.ini' );
        foreach ( array( 'Enabled', 'SiteAccessList', 'LibraryPath' ) as $name )
            $this->settings[$name] = $ini->hasVariable( 'GraphQLSettings', $name ) ? $ini->variable( 'GraphQLSettings', $name ) : null;
        $ini->setVariable( 'GraphQLSettings', 'LibraryPath', '' );
        $this->server = $_SERVER;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'graphql.ini' );
        foreach ( $this->settings as $name => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'GraphQLSettings', $name );
            else
                $ini->setVariable( 'GraphQLSettings', $name, $value );
        }
        $_SERVER = $this->server;
        expGraphQL::useLibraryClasses();
        parent::tearDown();
    }

    public function testComposerOnlySuggestsTheLibrary()
    {
        $composer = json_decode( (string)file_get_contents( dirname( __DIR__, 5 ) . '/composer.json' ), true );
        $this->assertArrayNotHasKey( 'webonyx/graphql-php', $composer['require'],
                                     'a hard requirement breaks every platform whose GraphQL bundle needs another version' );
        $this->assertArrayHasKey( 'webonyx/graphql-php', $composer['suggest'] );
        $this->assertSame( expGraphQL::LIBRARY_CONSTRAINT, $composer['require-dev']['webonyx/graphql-php'],
                           'the kernel\'s own development install (and CI) keeps the library, so the GraphQL tests run' );
    }

    public function testAMissingLibraryIsReportedNotFatal()
    {
        expGraphQL::useLibraryClasses( '\ExpGraphQLLibraryMissingTest\GraphQL' );
        $this->assertFalse( expGraphQL::loadLibrary() );
        $this->assertSame( expGraphQL::LIBRARY_MISSING, expGraphQL::libraryState() );
        $this->assertStringContainsString( 'composer require webonyx/graphql-php:^15.32.3', expGraphQL::libraryProblem() );
        $this->assertNull( expGraphQL::libraryVersion() );
    }

    public function testAnotherMajorVersionIsIncompatible()
    {
        // GraphQL\ classes are there (a platform's 0.13 or 14), but not the class only version 15 has
        expGraphQL::useLibraryClasses( 'expGraphQL', '\ExpGraphQLLibraryMissingTest\QueryValidationContext' );
        $this->assertFalse( expGraphQL::loadLibrary() );
        $this->assertSame( expGraphQL::LIBRARY_INCOMPATIBLE, expGraphQL::libraryState() );
        $this->assertStringContainsString( 'are not webonyx/graphql-php 15', expGraphQL::libraryProblem() );
    }

    public function testTheRealLibraryIsRecognised()
    {
        if ( !class_exists( '\GraphQL\GraphQL' ) && !getenv( 'GRAPHQL_LIBRARY_PATH' ) )
            $this->markTestSkipped( 'webonyx/graphql-php is not installed here' );
        expGraphQL::useLibraryClasses();
        if ( !expGraphQL::loadLibrary() )
            $this->markTestSkipped( 'the GraphQL library here is not version 15: ' . expGraphQL::libraryProblem() );
        $this->assertSame( expGraphQL::LIBRARY_AVAILABLE, expGraphQL::libraryState() );
        $this->assertNull( expGraphQL::libraryProblem() );
    }

    public function testTheStatusDegrades()
    {
        expGraphQL::useLibraryClasses( '\ExpGraphQLLibraryMissingTest\GraphQL' );
        $status = expGraphQLSetup::status();
        $this->assertFalse( $status['library'] );
        $this->assertNull( $status['libraryVersion'] );
        $this->assertSame( expGraphQL::LIBRARY_MISSING, $status['libraryState'] );
        $this->assertNotEmpty( $status['libraryProblem'] );
        $this->assertNull( $status['schemaHash'], 'no schema is built without the library' );
        $this->assertSame( '/api/graphql', $status['endpoint'] );
    }

    public function testTheEndPointAnswersAnError()
    {
        if ( !class_exists( 'ezcMvcRequest' ) )
            $this->markTestSkipped( 'the MVC component of the Zeta Components is not installed' );
        $ini = eZINI::instance( 'graphql.ini' );
        $ini->setVariable( 'GraphQLSettings', 'Enabled', 'enabled' );
        $ini->setVariable( 'GraphQLSettings', 'SiteAccessList', array() );
        unset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
        expGraphQL::useLibraryClasses( '\ExpGraphQLLibraryMissingTest\GraphQL' );

        foreach ( array( 'doQuery', 'doSubscribe' ) as $action )
        {
            $request = new ezpRestRequest();
            $request->get = array( 'query' => '{ __typename }' );
            $request->post = array();
            $controller = new expGraphQLRestController( $action === 'doQuery' ? 'query' : 'subscribe', $request );
            $result = $controller->$action();
            $this->assertInstanceOf( 'expGraphQLRestResponse', $result->status, $action );
            $this->assertSame( 503, $result->status->code, $action );
            $this->assertSame( expGraphQLError::INTERNAL, $result->status->message['errors'][0]['extensions']['code'], $action );
            $this->assertStringContainsString( 'GraphQL', $result->status->message['errors'][0]['message'], $action );
        }
    }
}

?>
