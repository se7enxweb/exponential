<?php
/**
 * The GraphQL integration of the migrations without a database: the step type graphql run against a GraphQL server of
 * the test, the header events: and what it makes of a migration's content events in the journal (and so in the
 * webhooks and subscriptions), and the read-only API (expMigrationService, the REST routes and their scope).
 *
 *  GQ-01 the kernel registers graphql from migration.ini; its schema is valid, its example a valid step; it never
 *        undoes itself and touches no cache
 *  GQ-02 a step names exactly one of persisted, query and file; a file below the migration's folder and .graphql; an
 *        operation name; a persisted query by name or SHA-256
 *  GQ-03 the plan names the query, mutation or persisted query and says a dry run runs nothing
 *  GQ-04 apply hands the query, variables, operation and persisted id to the server and keeps data, result and errors
 *        for references; an error fails the step unless allow_errors and data; a file next to the migration is read
 *  GQ-05 the header events: each (the default), summary, none; another value is an error; the runner reads it
 *  GQ-06 the journal: each tags every event with the migration, summary notes one mutation event "migration" with the
 *        objects and nodes, none notes nothing, a rolled back migration's notes are dropped; the records and the
 *        public fields (webhook payloads) carry migration and objectIds
 *  GQ-07 the service shapes a status report, a record and a page of runs
 *  GQ-08 REST: three GET routes, the scope migration:read for each, no application cache
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationGraphqlIntegrationTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null */
    private $dir;

    /** @var callable|null the exception handler before the test */
    private $handler;

    /**
     * @param string $yaml
     * @return string the errors of the steps, one per line
     */
    private static function errors( $yaml )
    {
        $errors = array();
        expMigrationYaml::fromText( $yaml, expMigrationStepRegistry::fromSettings(), $errors );
        return implode( "\n", $errors );
    }

    /**
     * @param array $step
     * @return array
     */
    private static function step( array $step )
    {
        return expMigrationStepSchema::withDefaults( expMigrationStepRegistry::fromSettings()->get( 'graphql' )->schema(), $step );
    }

    /** @return expMigrationContext */
    private function context()
    {
        return new expMigrationContext( new stdClass(), true, array( 'no-mirror' => true, 'user' => null, 'run-id' => '20261108-100000-abcdef',
                                                                     'root' => $this->dir() ) );
    }

    /** @return string */
    private function dir()
    {
        if ( $this->dir === null )
        {
            $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-graphql-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
            mkdir( $this->dir . '/graphql', 0775, true );
        }
        return $this->dir;
    }

    protected function setUp(): void
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        expMigrationStepRegistry::forget();
        expGraphQLEventListener::reset();
        $this->handler = set_exception_handler( null );
        restore_exception_handler();
    }

    protected function tearDown(): void
    {
        // a webhook's request or an event may look up the current user, which installs the kernel's exception handler
        for ( $i = 0; $i < 5; $i++ )
        {
            $now = set_exception_handler( null );
            restore_exception_handler();
            if ( $now === $this->handler )
                break;
            restore_exception_handler();
        }
        expMigrationStepGraphql::setExecutor( null );
        expGraphQLEventListener::reset();
        expGraphQLEventListener::$forceActive = null;
        expGraphQLEventListener::$journal = null;
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                       RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $iterator as $item )
                $item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
            rmdir( $this->dir );
        }
        $this->dir = null;
    }

    /** GQ-01 */
    public function testRegistered()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array(), $registry->errors() );
        $type = $registry->get( 'graphql' );
        $this->assertInstanceOf( 'expMigrationStepGraphql', $type );
        $schema = $type->schema();
        $this->assertSame( array(), expMigrationStepSchema::problems( $schema ) );
        $this->assertSame( array( 'data', 'result', 'errors' ), array_keys( $schema['values'] ) );
        $this->assertSame( '', self::errors( $schema['example'] ), 'the example is a valid step' );
        $this->assertNotInstanceOf( 'expMigrationRevertibleStepType', $type, 'a query or mutation never undoes itself' );
        $this->assertNotInstanceOf( 'expMigrationPartlyRevertibleStepType', $type );
        $this->assertSame( array(), $type->caches( self::step( array( 'query' => '{ x }' ) ) ) );
    }

    /** GQ-02 */
    public function testChecks()
    {
        $this->assertStringContainsString( 'exactly one of persisted, query and file', self::errors( "- type: graphql\n  variables: { a: 1 }\n" ) );
        $this->assertStringContainsString( 'given: persisted, query', self::errors( "- type: graphql\n  persisted: teasers\n  query: '{ x }'\n" ) );
        $this->assertStringContainsString( 'the text of a query', self::errors( "- type: graphql\n  query: '  '\n" ) );
        $this->assertStringContainsString( 'a relative path below', self::errors( "- type: graphql\n  file: ../secret.graphql\n" ) );
        $this->assertStringContainsString( 'a .graphql or .gql file', self::errors( "- type: graphql\n  file: graphql/teasers.txt\n" ) );
        $this->assertStringContainsString( 'the name of an operation', self::errors( "- type: graphql\n  query: '{ x }'\n  operation: 'a b'\n" ) );
        $this->assertStringContainsString( 'the name of a persisted query', self::errors( "- type: graphql\n  persisted: 'a b'\n" ) );
        $this->assertSame( '', self::errors( "- type: graphql\n  persisted: " . str_repeat( 'ab', 32 ) . "\n" ), 'a SHA-256' );
        $this->assertSame( '', self::errors( "- type: graphql\n  persisted: news/teasers\n  variables: { parent: 2 }\n" ) );
        $this->assertSame( '', self::errors( "- type: graphql\n  file: graphql/teasers.gql\n  operation: Teasers\n  allow_errors: true\n" ) );
        $this->assertSame( '', self::errors( "- type: graphql\n  persisted: '\${ref.query_name}'\n" ), 'a reference is checked when it is known' );
    }

    /** GQ-03 */
    public function testPlans()
    {
        $type = expMigrationStepRegistry::fromSettings()->get( 'graphql' );
        $this->assertSame( array( 'run a mutation with parent of the GraphQL API (not run in a dry run)' ),
                           $type->plan( self::step( array( 'query' => "# make it\nmutation { createContent { contentId } }", 'variables' => array( 'parent' => 2 ) ) ), null ) );
        $this->assertSame( array( 'run a query (Teasers) of the GraphQL API (not run in a dry run)' ),
                           $type->plan( self::step( array( 'query' => 'query Teasers { node(id: 2) { name } }', 'operation' => 'Teasers' ) ), null ) );
        $this->assertSame( array( 'run the persisted query teasers of the GraphQL API (not run in a dry run)' ), $type->plan( self::step( array( 'persisted' => 'teasers' ) ), null ) );
        $this->assertSame( array( 'run the query of graphql/a.graphql of the GraphQL API (not run in a dry run)' ), $type->plan( self::step( array( 'file' => 'graphql/a.graphql' ) ), null ) );
    }

    /** GQ-04 */
    public function testApply()
    {
        $type = expMigrationStepRegistry::fromSettings()->get( 'graphql' );
        $asked = array();
        $answer = array( 'data' => array( 'createContent' => array( 'contentId' => 61, 'nodeId' => 63 ) ) );
        expMigrationStepGraphql::setExecutor( function ( $query, $variables, $operation, $id ) use ( &$asked, &$answer ) {
            $asked[] = array( $query, $variables, $operation, $id );
            return $answer;
        } );
        $result = $type->apply( self::step( array( 'query' => 'mutation New($p: Int!) { createContent(parentId: $p) { contentId nodeId } }', 'variables' => array( 'p' => 2 ),
                                                   'operation' => 'New' ) ), $this->context() );
        $this->assertSame( array( 'mutation New($p: Int!) { createContent(parentId: $p) { contentId nodeId } }', array( 'p' => 2 ), 'New', null ), $asked[0] );
        $this->assertSame( array( 'contentId' => 61, 'nodeId' => 63 ), $result['values']['result'] );
        $this->assertSame( $answer['data'], $result['values']['data'] );
        $this->assertSame( array(), $result['values']['errors'] );
        $this->assertSame( array( 'ran a mutation: createContent' ), $result['lines'] );

        $type->apply( self::step( array( 'persisted' => 'news/teasers' ) ), $this->context() );
        $this->assertSame( array( '', null, null, 'news/teasers' ), $asked[1], 'a persisted query goes by its id, without a text' );

        file_put_contents( $this->dir() . '/graphql/teasers.graphql', '{ node(id: 2) { name } }' );
        $answer = array( 'data' => array( 'node' => array( 'name' => 'Home' ) ) );
        $result = $type->apply( self::step( array( 'file' => 'graphql/teasers.graphql' ) ), $this->context() );
        $this->assertSame( '{ node(id: 2) { name } }', $asked[2][0], 'a file next to the migration (the root without a migration file)' );
        $this->assertSame( array( 'name' => 'Home' ), $result['values']['result'] );

        $answer = array( 'errors' => array( array( 'message' => 'Not allowed' ) ), 'data' => array( 'node' => null ) );
        try
        {
            $type->apply( self::step( array( 'query' => '{ node(id: 2) { name } }' ) ), $this->context() );
            $this->fail( 'an error fails the step' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertSame( 'a query answered: Not allowed', $e->getMessage() );
        }
        $result = $type->apply( self::step( array( 'query' => '{ node(id: 2) { name } }', 'allow_errors' => true ) ), $this->context() );
        $this->assertSame( array( 'Not allowed' ), $result['values']['errors'] );
        $this->assertNull( $result['values']['result'] );
        $answer = array( 'errors' => array( array( 'message' => 'Syntax Error' ) ) );
        $this->expectExceptionMessage( 'a query answered: Syntax Error' );
        $type->apply( self::step( array( 'query' => '{ node(', 'allow_errors' => true ) ), $this->context() );
    }

    /** GQ-05 */
    public function testEventsHeader()
    {
        $errors = array();
        $header = expMigrationDefinition::parseHeader( "# description: x\n# events: summary\n", $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( 'summary', $header['events'] );
        $errors = array();
        expMigrationDefinition::parseHeader( "# events: some\n", $errors );
        $this->assertSame( array( 'events: some is not each, summary, none' ), $errors );

        $dir = $this->dir() . '/migrations';
        mkdir( $dir );
        file_put_contents( "$dir/20261108100000_quiet.yml", "# events: NONE\n- type: reference\n  set: { a: 1 }\n" );
        file_put_contents( "$dir/20261108100100_loud.yml", "- type: reference\n  set: { b: 1 }\n" );
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( 'none', 'each' ), array_map( array( 'expMigrationRunner', 'eventsMode' ), $migrations ) );
    }

    /**
     * @return expGraphQLEvents a journal in the folder of the test
     */
    private function journal()
    {
        return new expGraphQLEvents( $this->dir() . '/events' );
    }

    /** GQ-06 */
    public function testJournal()
    {
        expGraphQLEventListener::$forceActive = true;
        expGraphQLEventListener::$journal = $this->journal();

        expGraphQLEventListener::beginMigration( '20261108100000_news', 'each' );
        expGraphQLEventListener::mutation( 'createContent', array( 'contentId' => 61, 'nodeId' => 63, 'version' => 1 ) );
        expGraphQLEventListener::endMigration( true );
        expGraphQLEventListener::mutation( 'updateContent', array( 'contentId' => 61 ) );
        $pending = expGraphQLEventListener::pending();
        $this->assertCount( 2, $pending );
        $this->assertSame( '20261108100000_news', $pending[0]['migration'], 'each: tagged with the migration' );
        $this->assertArrayNotHasKey( 'migration', $pending[1], 'a change after the migration is not' );
        expGraphQLEventListener::reset();

        expGraphQLEventListener::beginMigration( '20261108100100_bulk', 'summary' );
        expGraphQLEventListener::mutation( 'createContent', array( 'contentId' => 61, 'nodeId' => 63 ) );
        expGraphQLEventListener::mutation( 'createContent', array( 'contentId' => 62, 'nodeId' => 64 ) );
        expGraphQLEventListener::mutation( 'updateContent', array( 'contentId' => 61, 'nodeId' => 63 ) );
        $this->assertSame( array(), expGraphQLEventListener::pending(), 'summary: no event per change' );
        expGraphQLEventListener::endMigration( true );
        $pending = expGraphQLEventListener::pending();
        $this->assertCount( 1, $pending );
        $this->assertSame( array( 'event' => 'mutation', 'mutation' => 'migration', 'migration' => '20261108100100_bulk', 'objectIds' => array( 61, 62 ), 'nodeIds' => array( 63, 64 ) ),
                           array_intersect_key( $pending[0], array_flip( array( 'event', 'mutation', 'migration', 'objectIds', 'nodeIds' ) ) ) );
        expGraphQLEventListener::reset();

        expGraphQLEventListener::beginMigration( '20261108100200_quiet', 'none' );
        expGraphQLEventListener::mutation( 'createContent', array( 'contentId' => 61 ) );
        expGraphQLEventListener::endMigration( true );
        $this->assertSame( array(), expGraphQLEventListener::pending(), 'none: nothing' );

        expGraphQLEventListener::mutation( 'createContent', array( 'contentId' => 60 ) );
        expGraphQLEventListener::beginMigration( '20261108100300_broken', 'each' );
        expGraphQLEventListener::mutation( 'createContent', array( 'contentId' => 61 ) );
        expGraphQLEventListener::endMigration( false );
        $this->assertSame( array( 60 ), array_column( expGraphQLEventListener::pending(), 'objectId' ), 'a rolled back migration leaves no event' );
        expGraphQLEventListener::reset();

        $record = expGraphQLEvents::record( array( 'event' => 'mutation', 'mutation' => 'migration', 'migration' => '20261108100100_bulk', 'objectIds' => array( '61', 62, 62 ) ) );
        $this->assertSame( '20261108100100_bulk', $record['migration'] );
        $this->assertSame( array( 61, 62 ), $record['objectIds'] );
        $public = expGraphQLEvents::publicFields( $record );
        $this->assertSame( '20261108100100_bulk', $public['migration'] );
        $this->assertSame( array( 61, 62 ), $public['objectIds'] );
        $this->assertNull( expGraphQLEvents::publicFields( expGraphQLEvents::record( array( 'event' => 'publish', 'objectId' => 5 ) ) )['migration'] );
        $this->assertSame( array(), expGraphQLEvents::publicFields( expGraphQLEvents::record( array( 'event' => 'publish', 'objectId' => 5 ) ) )['objectIds'] );

        $webhooks = new expGraphQLWebhooks( $this->dir() . '/webhooks', $this->journal(), array(), array() );
        $endpoint = expGraphQLWebhooks::endpoint( 'search', array( 'URL' => 'https://search.example.com/hook', 'Secret' => 'graphql.webhook.search' ) );
        list( $body ) = $webhooks->request( $endpoint, expGraphQLEvents::record( array( 'event' => 'publish', 'objectId' => 61, 'migration' => '20261108100000_news' ) ), 'd1', 1, 's' );
        $payload = json_decode( $body, true );
        $this->assertSame( '20261108100000_news', $payload['data']['contentEvent']['migration'], 'the webhook payload is tagged' );
    }

    /** GQ-07 */
    public function testService()
    {
        $this->assertSame( array( 1, 0 ), expMigrationService::page( 0, -5 ) );
        $this->assertSame( array( 100, 40 ), expMigrationService::page( 500, '40' ) );
        $report = array(
            'environment' => 'production', 'engine' => 'mysql', 'table' => true, 'lock' => null,
            'migrations' => array(
                array( 'id' => '6.0.15-migration-tables', 'source' => 'kernel', 'description' => 'Tables', 'stage' => 'database', 'file' => 'kernel/x.php', 'state' => 'done',
                       'detail' => '', 'changed' => false, 'checksum' => 'abc', 'record' => array( 'state' => 'done', 'run_id' => 'r1', 'finished_at' => '2026-11-01T10:00:00+00:00' ) ),
                array( 'id' => '20261108100000_news', 'source' => 'site', 'description' => 'News', 'stage' => 'migration', 'file' => 'migrations/20261108100000_news.yml',
                       'state' => 'pending', 'detail' => '', 'changed' => false, 'record' => null ),
            ),
            'orphans' => array(), 'runs' => array(), 'warnings' => array( 'w' ), 'errors' => array(),
        );
        $status = expMigrationService::statusOf( $report );
        $this->assertSame( array( 'pending' => 1, 'failed' => 0, 'orphans' => 0, 'total' => 2, 'ready' => true ), $status['summary'] );
        $this->assertSame( array( 'production', 'mysql', '' ), array( $status['environment'], $status['engine'], $status['lock'] ) );
        $this->assertSame( array( 'id', 'source', 'description', 'stage', 'file', 'state', 'detail', 'changed', 'record' ), array_keys( $status['migrations'][0] ) );
        $this->assertArrayNotHasKey( 'checksum', $status['migrations'][0], 'only what the API gives' );
        $this->assertSame( array( 'w' ), $status['warnings'] );
        $row = expMigrationRecord::normalize( array( 'source' => 'site', 'migration_id' => '20261108100000_news', 'state' => 'done', 'started_at' => 1793527200,
                                                     'finished_at' => 1793527201, 'duration_ms' => 900, 'checksum' => 'abc', 'caption_id' => 'c1' ), expMigrationRecord::COLUMNS );
        $record = expMigrationService::record( $row );
        $this->assertSame( array( 'id', 'source', 'state', 'file', 'started_at', 'finished_at', 'duration_ms', 'run_by', 'engine', 'message' ), array_keys( $record ) );
        $this->assertSame( 900, $record['duration_ms'] );
        $this->assertSame( date( 'c', 1793527200 ), $record['started_at'] );
    }

    /** GQ-08 */
    public function testRest()
    {
        $routes = ( new expMigrationRestProvider() )->getRoutes();
        $this->assertSame( array( 'expMigrationStatus', 'expMigrationRuns', 'expMigrationRun', 'expMigrationSnapshot' ), array_keys( $routes ) );
        $this->assertInstanceOf( 'expMigrationRestViewController', ( new expMigrationRestProvider() )->getViewController() );
        $ini = eZINI::instance( 'rest.ini' );
        $this->assertSame( 'expMigrationRestProvider', $ini->variable( 'ApiProvider', 'ProviderClass' )['migration'] );
        $this->assertSame( 'migration/read', $ini->variable( 'ApiKeySettings', 'Scopes' )['migration:read'] );
        $scopes = $ini->variable( 'ApiKeySettings', 'RouteScopes' );
        foreach ( array( 'status', 'runs', 'run' ) as $action )
            $this->assertSame( 'migration:read', $scopes["expMigrationRestController_$action"], $action );
        $this->assertSame( 'disabled', $ini->variable( 'expMigrationRestController_CacheSettings', 'ApplicationCache' ) );
        $this->assertArrayHasKey( 'migration:read', expApiKey::scopeCatalogue( $ini ) );
        $this->assertContains( 'expMigrationGraphQLExtension', eZINI::instance( 'graphql.ini' )->variable( 'GraphQLSettings', 'SchemaExtensionList' ) );
    }
}
