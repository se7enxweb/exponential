<?php
/**
 * The step types tag, url_alias, trash, file and image on SQLite in process: a SQLite file under var/tmp with the
 * kernel schema of
 * kernel/sql/sqlite/schema.sql and the clean data, removed afterwards. No server and no installation's database.
 * Skipped without symfony/yaml.
 *
 *  ST-1 TE-1 URL aliases and wildcards, rolled back
 *  ST-2 TE-2 the trash: move, restore, purge, empty, a move rolled back
 *  ST-3 TE-3 files and images, with the focal point and crop rectangle
 *  ST-4 TE-4 tags with eztags (or the step failing cleanly without it)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationTagAliasTrashFileStepsEngineChecks.php';

class expMigrationTagAliasTrashFileStepsSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationTagAliasTrashFileStepsEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->contentBegin();
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->runnerBegin( $this->db );
        // the shipped SQLite schema: it keys classes and object attributes on (id, version) as the .dba does
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
        $this->contentEnd();
    }

    /** ST-1 */
    public function testUrlAliases()
    {
        $this->checkUrlAliases( $this->db );
    }

    /** ST-2 */
    public function testTrash()
    {
        $this->checkTrash( $this->db );
    }

    /** ST-3 */
    public function testFilesAndImages()
    {
        $this->checkFilesAndImages( $this->db );
    }

    /** ST-4 */
    public function testTags()
    {
        $this->checkTags( $this->db );
    }
}
