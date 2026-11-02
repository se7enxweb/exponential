<?php
/**
 * The step types layout, layout_block, layout_rule, http, mail and command on a real database server:
 * MySQL/MariaDB and PostgreSQL in the database job (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL
 * database of ezpThrowawayDatabase. The database has the kernel schema and the clean data. Skipped without one, and
 * without symfony/yaml.
 *
 *  LD-1 LE-1 layouts, zones linked to a shared layout, blocks, rules, a create rolled back (or the step failing
 *            cleanly without Exponential Layouts)
 *  LD-2 LE-2 http, mail and command, and a dry run that sends, mails and runs nothing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationLayoutHttpMailCommandStepsEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationLayoutHttpMailCommandStepsDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationLayoutHttpMailCommandStepsEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql', 'oracle' ), true ) )
            $this->markTestSkipped( "not a MySQL, PostgreSQL or Oracle database: $engine" );
        $this->contentBegin();
        $this->runnerBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->runnerEnd();
        $this->contentEnd();
        parent::tearDown();
    }

    /** LD-1 */
    public function testLayouts()
    {
        $this->checkLayouts( eZDB::instance() );
    }

    /** LD-2 */
    public function testHttpMailCommand()
    {
        $this->checkHttpMailCommand( eZDB::instance() );
    }
}
