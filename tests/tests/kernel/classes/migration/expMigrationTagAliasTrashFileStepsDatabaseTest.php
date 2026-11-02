<?php
/**
 * The step types tag, url_alias, trash, file and image on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. The database has
 * the kernel schema and the clean data. Skipped without one, and without symfony/yaml.
 *
 *  SD-1 TE-1 URL aliases and wildcards, rolled back
 *  SD-2 TE-2 the trash: move, restore, purge, empty, a move rolled back
 *  SD-3 TE-3 files and images, with the focal point and crop rectangle
 *  SD-4 TE-4 tags with eztags (or the step failing cleanly without it)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationTagAliasTrashFileStepsEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationTagAliasTrashFileStepsDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationTagAliasTrashFileStepsEngineChecks;

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

    /** SD-1 */
    public function testUrlAliases()
    {
        $this->checkUrlAliases( eZDB::instance() );
    }

    /** SD-2 */
    public function testTrash()
    {
        $this->checkTrash( eZDB::instance() );
    }

    /** SD-3 */
    public function testFilesAndImages()
    {
        $this->checkFilesAndImages( eZDB::instance() );
    }

    /** SD-4 */
    public function testTags()
    {
        $this->checkTags( eZDB::instance() );
    }
}
