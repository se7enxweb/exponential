<?php
/**
 * The step type graphql through the runner on SQLite in process, with the real GraphQL server: a SQLite file under
 * var/tmp with the kernel schema and the clean data, removed afterwards; the persisted queries, the caches and the
 * event journal in a folder of the test. No server and no installation's database.
 *
 *  GS-1 a YAML migration runs a persisted query (its answer kept as a reference) and a mutation with a variable from
 *       that reference, as the run's user; the folder is there; the journal has the publish and the mutation, each
 *       tagged with the migration; the status API lists the migration done and its run
 *  GS-2 events: none leaves the journal empty; a failing graphql step rolls its migration back with its events
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

class expMigrationGraphqlSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    /** @var expGraphQLEvents|null */
    private $journal;

    public static function setUpBeforeClass(): void
    {
        if ( !expGraphQL::loadLibrary() )
        {
            if ( getenv( 'CI' ) )
                self::fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
            self::markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        }
    }

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->runnerBegin( $this->db );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
        expMigrationStepRegistry::forget();
        expGraphQLEventListener::reset();
        expGraphQLEventListener::$forceActive = true;
        $this->journal = new expGraphQLEvents( $this->runnerDir() . '/events' );
        expGraphQLEventListener::$journal = $this->journal;
    }

    protected function tearDown(): void
    {
        expGraphQLEventListener::reset();
        expGraphQLEventListener::$forceActive = null;
        expGraphQLEventListener::$journal = null;
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
    }

    /**
     * @param array $files name => text
     * @return expMigration[]
     */
    private function migrations( array $files )
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
    private static function why( array $report )
    {
        return implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) );
    }

    /** @return array[] the events written to the journal of the test */
    private function written()
    {
        expGraphQLEventListener::flush();
        return $this->journal->read( '0.0', 1000 )['events'];
    }

    /** GS-1 */
    public function testPersistedQueryAndMutationFromAMigration()
    {
        expGraphQLPersistedQueries::fromSettings()->store( 'query Root($id: Int!) { node(id: $id) { name } }', 'wp12-root' );
        $migrations = $this->migrations( array(
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
        $report = ( new expMigrationRunner( $this->runnerContext( $this->db ) ) )->run( $migrations );
        $this->assertSame( array(), $report['errors'], self::why( $report ) );
        $this->assertSame( 'applied', $report['migrations'][0]['action'], self::why( $report ) );
        $rows = $this->db->arrayQuery( "SELECT id FROM ezcontentobject WHERE contentclass_id = 1 AND name LIKE 'Below %'" );
        $this->assertCount( 1, $rows, 'the folder is there: ' . self::why( $report ) );
        $objectId = (int)$rows[0]['id'];

        $events = $this->written();
        $mine = array_values( array_filter( $events, function ( $e ) use ( $objectId ) { return isset( $e['objectId'] ) && (int)$e['objectId'] === $objectId; } ) );
        $this->assertNotEmpty( $mine, 'the change is in the journal: ' . json_encode( $events ) );
        foreach ( $mine as $event )
            $this->assertSame( '20261108100000_wp12_graphql', isset( $event['migration'] ) ? $event['migration'] : null, $event['event'] . ' is tagged' );
        $this->assertContains( 'mutation', array_column( $mine, 'event' ) );
        $this->assertContains( 'publish', array_column( $mine, 'event' ) );

        $record = expMigrationRecord::get( $this->db, 'site', '20261108100000_wp12_graphql' );
        $this->assertSame( 'done', $record['state'] );
        $run = expMigrationRecord::run( $this->db, $record['run_id'] );
        $this->assertNotNull( $run );
        $this->assertSame( array( '20261108100000_wp12_graphql' ), array_column( ( new expMigrationService() )->run( $record['run_id'] )['migrations'], 'id' ) );
    }

    /** GS-2 */
    public function testNoEventsAndARolledBackMigration()
    {
        $migrations = $this->migrations( array(
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
        $report = ( new expMigrationRunner( $this->runnerContext( $this->db ) ) )->run( $migrations );
        $this->assertSame( 'applied', $report['migrations'][0]['action'], self::why( $report ) );
        $this->assertSame( 'failed', $report['migrations'][1]['action'], self::why( $report ) );
        $this->assertStringContainsString( 'nameThatIsNot', self::why( $report ) );
        $this->assertCount( 1, $this->db->arrayQuery( "SELECT id FROM ezcontentobject WHERE name = 'Quiet'" ) );
        $this->assertCount( 0, $this->db->arrayQuery( "SELECT id FROM ezcontentobject WHERE name = 'Broken'" ), 'rolled back' );
        $this->assertSame( array(), $this->written(), 'none: no event; the rolled back migration: none either' );
    }
}
