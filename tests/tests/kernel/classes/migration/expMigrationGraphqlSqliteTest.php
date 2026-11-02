<?php
/**
 * The step type graphql through the runner on SQLite in process, with the real GraphQL server: a SQLite file under
 * var/tmp with the kernel schema and the clean data, removed afterwards; the persisted queries, the caches and the
 * event journal in a folder of the test. No server and no installation's database. The checks:
 * expMigrationGraphqlEngineChecks.
 *
 *  GS-1 GE-1 a persisted query and a mutation from a migration, tagged events, the status API
 *  GS-2 GE-2 events: none; a failing graphql step rolls its migration back with its events
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationGraphqlEngineChecks.php';

class expMigrationGraphqlSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationGraphqlEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    public static function setUpBeforeClass(): void
    {
        self::graphqlLibrary();
    }

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->runnerBegin( $this->db );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
        $this->graphqlBegin();
    }

    protected function tearDown(): void
    {
        $this->graphqlEnd();
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
    }

    /** GS-1 */
    public function testPersistedQueryAndMutationFromAMigration()
    {
        $this->checkGraphqlQueryAndMutation( $this->db );
    }

    /** GS-2 */
    public function testNoEventsAndARolledBackMigration()
    {
        $this->checkGraphqlNoEventsAndAFailedMigration( $this->db );
    }
}
