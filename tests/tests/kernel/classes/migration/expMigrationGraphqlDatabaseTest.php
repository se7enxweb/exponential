<?php
/**
 * The step type graphql through the runner on a real database server, with the real GraphQL server in process:
 * MySQL/MariaDB and PostgreSQL in the database job (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL
 * database of ezpThrowawayDatabase. The database has the kernel schema and the clean data. Skipped without one. The
 * checks: expMigrationGraphqlEngineChecks.
 *
 *  GD-1 GE-1 a persisted query and a mutation from a migration, tagged events, the status API
 *  GD-2 GE-2 events: none; a failing graphql step rolls its migration back with its events
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationGraphqlEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationGraphqlDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationGraphqlEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql', 'oracle' ), true ) )
            $this->markTestSkipped( "not a MySQL, PostgreSQL or Oracle database: $engine" );
        self::graphqlLibrary();
        $this->runnerBegin( eZDB::instance() );
        $this->graphqlBegin();
    }

    protected function tearDown(): void
    {
        $this->graphqlEnd();
        $this->runnerEnd();
        parent::tearDown();
    }

    /** GD-1 */
    public function testPersistedQueryAndMutationFromAMigration()
    {
        $this->checkGraphqlQueryAndMutation( eZDB::instance() );
    }

    /** GD-2 */
    public function testNoEventsAndAFailedMigration()
    {
        $this->checkGraphqlNoEventsAndAFailedMigration( eZDB::instance() );
    }
}
