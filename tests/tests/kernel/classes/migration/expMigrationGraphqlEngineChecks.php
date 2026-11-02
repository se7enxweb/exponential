<?php
/**
 * The checks of the step type graphql that run on every engine the same way, with the real GraphQL server in process:
 * SQLite (expMigrationGraphqlSqliteTest), MySQL/MariaDB and PostgreSQL in the database job
 * (expMigrationGraphqlDatabaseTest), MongoDB in the live MongoDB suite (expMigrationGraphqlMongoDBTest). Each gives a
 * connection with the kernel schema and the clean data; the persisted queries, the caches and the event journal are
 * in a folder of the test.
 *
 *  GE-1 a YAML migration runs a persisted query (its answer kept as a reference) and a mutation with a variable from
 *       that reference, as the run's user; the folder is there; the journal has the publish and the mutation, each
 *       tagged with the migration; the status API lists the migration done and its run
 *  GE-2 events: none leaves the journal empty; a failing graphql step fails its migration; where the engine has
 *       transactions the folder it made and its events are undone, on MongoDB both stay (the changes are real)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

trait expMigrationGraphqlEngineChecks
{
    use expMigrationRunnerEngineChecks;

    /** @var expGraphQLEvents|null */
    protected $graphqlJournal;

    /** Fails on CI and skips elsewhere when webonyx/graphql-php is missing. */
    protected static function graphqlLibrary()
    {
        if ( expGraphQL::loadLibrary() )
            return;
        if ( getenv( 'CI' ) )
            self::fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
        self::markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
    }

    /**
     * After runnerBegin(): the step registry, the event listener and a journal of the test.
     */
    protected function graphqlBegin()
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        expMigrationStepRegistry::forget();
        expGraphQLEventListener::reset();
        expGraphQLEventListener::$forceActive = true;
        $this->graphqlJournal = new expGraphQLEvents( $this->runnerDir() . '/events' );
        expGraphQLEventListener::$journal = $this->graphqlJournal;
    }

    /** Before runnerEnd(). */
    protected function graphqlEnd()
    {
        expGraphQLEventListener::reset();
        expGraphQLEventListener::$forceActive = null;
        expGraphQLEventListener::$journal = null;
        $this->graphqlJournal = null;
    }

    /**
     * @param array $files name => text
     * @return expMigration[]
     */
    protected function graphqlMigrations( array $files )
    {
        $dir = $this->runnerDir() . '/site';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0775, true );
        foreach ( $files as $name => $text )
            file_put_contents( "$dir/$name", $text );
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        return $migrations;
    }

    /**
     * @param array $report
     * @return string
     */
    protected static function graphqlWhy( array $report )
    {
        return implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) );
    }

    /** @return array[] the events written to the journal of the test */
    protected function graphqlWritten()
    {
        expGraphQLEventListener::flush();
        return $this->graphqlJournal->read( '0.0', 1000 )['events'];
    }

    /**
     * Removes the records of the checks (each test class has a database of its own, so the folders may stay).
     *
     * @param eZDBInterface $db
     */
    protected function graphqlClean( $db )
    {
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261108%_wp12_%'" );
        eZContentObject::clearCache();
    }

    /**
     * GE-1
     *
     * @param eZDBInterface $db
     */
    protected function checkGraphqlQueryAndMutation( $db )
    {
        $this->graphqlClean( $db );
        expGraphQLPersistedQueries::fromSettings()->store( 'query Root($id: Int!) { node(id: $id) { name } }', 'wp12-root' );
        $migrations = $this->graphqlMigrations( array(
            '20261108100000_wp12_graphql.yml' => <<<'YAML'
# description: A folder made through the GraphQL API
# events: each
steps:
    - type: graphql
      persisted: wp12-root
      variables: { id: 2 }
      references: { root: result }
    - type: graphql
      query: |
        mutation New($parent: Int!, $name: String) {
          createContent(parentId: $parent, classIdentifier: "folder", fields: [ { identifier: "name", value: $name } ]) { contentId nodeId }
        }
      operation: New
      variables: { parent: 2, name: "Below ${ref.root.name}" }
      references: { folder: result }
YAML
        ) );
        $context = $this->runnerContext( $db );
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $engine = expKernelTableSchema::type( $db );
        $this->assertSame( array(), $report['errors'], self::graphqlWhy( $report ) );
        $this->assertSame( 'applied', $report['migrations'][0]['action'], self::graphqlWhy( $report ) );
        $folder = $context->reference( 'folder' );
        $created = is_array( $folder ) && isset( $folder['createContent'] ) ? $folder['createContent'] : $folder;
        $this->assertIsArray( $created, "$engine: the mutation's answer is a reference: " . json_encode( $folder ) );
        $objectId = (int)$created['contentId'];
        $object = eZContentObject::fetch( $objectId );
        $this->assertInstanceOf( 'eZContentObject', $object, "$engine: the folder is there: " . self::graphqlWhy( $report ) );
        $this->assertStringStartsWith( 'Below ', $object->attribute( 'name' ) );
        $this->assertSame( 1, (int)$object->attribute( 'contentclass_id' ), 'a folder' );

        $events = $this->graphqlWritten();
        $mine = array_values( array_filter( $events, function ( $e ) use ( $objectId ) { return isset( $e['objectId'] ) && (int)$e['objectId'] === $objectId; } ) );
        $this->assertNotEmpty( $mine, 'the change is in the journal: ' . json_encode( $events ) );
        foreach ( $mine as $event )
            $this->assertSame( '20261108100000_wp12_graphql', isset( $event['migration'] ) ? $event['migration'] : null, $event['event'] . ' is tagged' );
        $this->assertContains( 'mutation', array_column( $mine, 'event' ) );
        $this->assertContains( 'publish', array_column( $mine, 'event' ) );

        $record = expMigrationRecord::get( $db, 'site', '20261108100000_wp12_graphql' );
        $this->assertSame( 'done', $record['state'] );
        $this->assertNotNull( expMigrationRecord::run( $db, $record['run_id'] ) );
        $this->assertSame( array( '20261108100000_wp12_graphql' ), array_column( ( new expMigrationService() )->run( $record['run_id'] )['migrations'], 'id' ) );
        $this->graphqlClean( $db );
    }

    /**
     * GE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkGraphqlNoEventsAndAFailedMigration( $db )
    {
        $this->graphqlClean( $db );
        $migrations = $this->graphqlMigrations( array(
            '20261108100100_wp12_quiet.yml' => <<<'YAML'
# events: none
- type: graphql
  query: 'mutation { createContent(parentId: 2, classIdentifier: "folder", fields: [ { identifier: "name", value: "Quiet" } ]) { contentId } }'
YAML
            ,
            '20261108100200_wp12_broken.yml' => <<<'YAML'
- type: graphql
  query: 'mutation { createContent(parentId: 2, classIdentifier: "folder", fields: [ { identifier: "name", value: "Broken" } ]) { contentId } }'
- type: graphql
  query: '{ node(id: 2) { nameThatIsNot } }'
YAML
        ) );
        $engine = expKernelTableSchema::type( $db );
        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 'applied', $report['migrations'][0]['action'], self::graphqlWhy( $report ) );
        $this->assertSame( 'failed', $report['migrations'][1]['action'], self::graphqlWhy( $report ) );
        $this->assertStringContainsString( 'nameThatIsNot', self::graphqlWhy( $report ) );
        $this->assertSame( 1, self::runnerCount( $db, 'ezcontentobject', "name = 'Quiet'" ) );
        $kept = expMigrationContext::transactionModeOf( $engine ) === expMigrationContext::TRANSACTION_NONE ? 1 : 0;
        $this->assertSame( $kept, self::runnerCount( $db, 'ezcontentobject', "name = 'Broken'" ),
                           $kept ? "$engine: no transaction, the folder stays" : "$engine: rolled back" );
        $this->assertSame( 'failed', expMigrationRecord::get( $db, 'site', '20261108100200_wp12_broken' )['state'] );
        $events = $this->graphqlWritten();
        $this->assertSame( array(), array_values( array_filter( $events, function ( $e ) { return isset( $e['name'] ) && $e['name'] === 'Quiet'; } ) ),
                           'events: none writes no event' );
        if ( $kept )
        {
            $this->assertNotEmpty( $events, "$engine: the folder stays, so do its events" );
            foreach ( $events as $event )
                $this->assertSame( '20261108100200_wp12_broken', isset( $event['migration'] ) ? $event['migration'] : null, $event['event'] . ' is tagged' );
        }
        else
            $this->assertSame( array(), $events, "$engine: the failed migration was rolled back, and its events with it" );
        $this->graphqlClean( $db );
    }
}
