<?php
/**
 * The schema extension of the migrations (expMigrationGraphQLExtension; doc/eng-us/features/6.0/graphql.md,
 * "Migrations"): the read-only root queries migrationStatus, migrations, migrationRuns and migrationRun, the policy
 * migration/read, one status report per request, no mutation, and the types it adds, compared with a snapshot
 * (fixtures/migration-schema.graphql). No database: a service of fixture data.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expMigrationTestService extends expMigrationService
{
    /** @var bool */
    public $mayRead = true;

    /** @var int how often status() was asked */
    public $reports = 0;

    /** @var array[] the arguments of runs() */
    public $asked = array();

    public function mayRead( $user = null ) { return $this->mayRead; }

    public function status()
    {
        $this->reports++;
        return expMigrationService::statusOf( array(
            'environment' => 'staging', 'engine' => 'postgresql', 'table' => true, 'lock' => null, 'warnings' => array(), 'errors' => array(),
            'migrations' => array(
                array( 'id' => '6.0.15-migration-tables', 'source' => 'kernel', 'description' => 'The tables', 'stage' => 'database', 'file' => 'kernel/x.php',
                       'state' => 'done', 'detail' => '', 'changed' => false, 'record' => array( 'state' => 'done', 'run_id' => 'r1', 'finished_at' => '2026-11-01T10:00:00+00:00' ) ),
                array( 'id' => '20261108100000_news', 'source' => 'site', 'description' => 'News', 'stage' => 'migration', 'file' => 'migrations/20261108100000_news.yml',
                       'state' => 'pending', 'detail' => '', 'changed' => false, 'record' => null ),
            ),
            'orphans' => array(), 'runs' => array(),
        ) );
    }

    public function runs( $limit = 20, $offset = 0 )
    {
        $this->asked[] = array( $limit, $offset );
        return array( self::fixtureRun() );
    }

    public function run( $runId )
    {
        return $runId === 'r1' ? self::fixtureRun() + array( 'migrations' => array( expMigrationService::record( expMigrationRecord::normalize(
            array( 'source' => 'kernel', 'migration_id' => '6.0.15-migration-tables', 'state' => 'done', 'duration_ms' => 12, 'engine' => 'postgresql' ), expMigrationRecord::COLUMNS ) ) ) )
            : null;
    }

    public static function fixtureRun()
    {
        return array( 'run_id' => 'r1', 'state' => 'done', 'started_at' => '2026-11-01T09:59:00+00:00', 'finished_at' => '2026-11-01T10:00:00+00:00',
                      'run_by' => 'cli www-data', 'environment' => 'staging', 'caption_pre' => 'c1', 'caption_post' => '', 'commit_id' => '', 'tag_name' => '',
                      'push_state' => '', 'message' => '' );
    }
}

class expMigrationGraphQLExtensionTest extends PHPUnit\Framework\TestCase
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
     * @param expMigrationTestService $service
     * @return expGraphQLSchemaBuilder
     */
    protected static function builder( expMigrationTestService $service )
    {
        $catalogue = expGraphQLClassCatalogue::filtered( array(), array() );
        return new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ), array( new expMigrationGraphQLExtension( $service ) ) );
    }

    protected static function query( $query, expMigrationTestService $service )
    {
        $builder = self::builder( $service );
        $context = new expGraphQLContext( new eZUser( array( 'contentobject_id' => 14, 'login' => 'admin' ) ), expGraphQLContext::AUTH_TOKEN,
                                          new expGraphQLRepository( $builder->catalogue() ) );
        return ( new expGraphQLServer( $builder ) )->execute( $query, null, null, $context );
    }

    public function testTheStatusAndTheMigrations()
    {
        $service = new expMigrationTestService();
        $answer = self::query( '{ migrationStatus { pending failed total ready environment engine lock warnings } all: migrations { id source state runId finishedAt }
                                  pending: migrations(state: "pending") { id file description } kernel: migrations(source: "kernel") { id } }', $service );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $data = $answer['data'];
        $this->assertSame( array( 'pending' => 1, 'failed' => 0, 'total' => 2, 'ready' => true, 'environment' => 'staging', 'engine' => 'postgresql', 'lock' => '',
                                  'warnings' => array() ), $data['migrationStatus'] );
        $this->assertSame( array( 'id' => '6.0.15-migration-tables', 'source' => 'kernel', 'state' => 'done', 'runId' => 'r1', 'finishedAt' => '2026-11-01T10:00:00+00:00' ),
                           $data['all'][0] );
        $this->assertNull( $data['all'][1]['runId'] );
        $this->assertSame( array( array( 'id' => '20261108100000_news', 'file' => 'migrations/20261108100000_news.yml', 'description' => 'News' ) ), $data['pending'] );
        $this->assertSame( array( array( 'id' => '6.0.15-migration-tables' ) ), $data['kernel'] );
        $this->assertSame( 1, $service->reports, 'one status report per request' );
    }

    public function testTheRuns()
    {
        $service = new expMigrationTestService();
        $answer = self::query( '{ migrationRuns(limit: 5, offset: 10) { id state runBy captionBefore migrations { id } }
                                  migrationRun(id: "r1") { id migrations { id source state durationMs engine } } none: migrationRun(id: "r9") { id } }', $service );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertSame( array( array( 'id' => 'r1', 'state' => 'done', 'runBy' => 'cli www-data', 'captionBefore' => 'c1', 'migrations' => array() ) ),
                           $answer['data']['migrationRuns'] );
        $this->assertSame( array( array( 5, 10 ) ), $service->asked );
        $this->assertSame( array( array( 'id' => '6.0.15-migration-tables', 'source' => 'kernel', 'state' => 'done', 'durationMs' => 12, 'engine' => 'postgresql' ) ),
                           $answer['data']['migrationRun']['migrations'] );
        $this->assertNull( $answer['data']['none'] );
    }

    public function testThePolicyAndNoMutation()
    {
        $service = new expMigrationTestService();
        $service->mayRead = false;
        $answer = self::query( '{ migrationStatus { total } migrations { id } migrationRuns { id } migrationRun(id: "r1") { id } }', $service );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertSame( array( 'migrationStatus' => null, 'migrations' => array(), 'migrationRuns' => array(), 'migrationRun' => null ), $answer['data'],
                           'without migration/read: nothing' );
        $this->assertSame( 0, $service->reports );
        $schema = self::builder( $service )->schema();
        $mutation = $schema->getMutationType();
        $this->assertTrue( $mutation === null || !$mutation->hasField( 'runMigration' ), 'no mutation runs a migration' );
    }

    public function testTheTypesMatchTheSnapshot()
    {
        $schema = self::builder( new expMigrationTestService() )->schema();
        $printed = array();
        foreach ( array( 'MigrationStatus', 'Migration', 'MigrationRun', 'MigrationRunEntry' ) as $name )
            $printed[] = \GraphQL\Utils\SchemaPrinter::printType( $schema->getType( $name ) );
        $query = $schema->getQueryType();
        $fields = array();
        foreach ( array( 'migrationStatus', 'migrations', 'migrationRuns', 'migrationRun' ) as $name )
        {
            $field = $query->getField( $name );
            $args = array();
            foreach ( $field->args as $arg )
                $args[] = $arg->name . ': ' . $arg->getType()->toString() . ( $arg->defaultValueExists() ? ' = ' . json_encode( $arg->defaultValue ) : '' );
            $fields[] = '  ' . $name . ( $args ? '(' . implode( ', ', $args ) . ')' : '' ) . ': ' . $field->getType()->toString();
        }
        $printed[] = "extend type Query {\n" . implode( "\n", $fields ) . "\n}";
        $this->assertSame( file_get_contents( __DIR__ . '/fixtures/migration-schema.graphql' ), implode( "\n\n", $printed ) . "\n",
                           'the types of the migrations changed: check the change is meant, then update the snapshot' );
        $this->assertContains( 'Migration', expGraphQLNaming::RESERVED_TYPES, 'no content class takes the name of a type of the extension' );
        $this->assertContains( 'MigrationRunEntry', expGraphQLNaming::RESERVED_TYPES );
    }
}
