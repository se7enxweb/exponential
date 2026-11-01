<?php
/**
 * The step types layout, layout_block, layout_rule, http, mail and command on SQLite in process: a SQLite file
 * under var/tmp with the kernel schema of kernel/sql/sqlite/schema.sql and the clean data, removed afterwards. No
 * server and no installation's database. Skipped without symfony/yaml.
 *
 *  LS-1 LE-1 layouts, zones linked to a shared layout, blocks, rules, a create rolled back (or the step failing
 *            cleanly without Exponential Layouts)
 *  LS-2 LE-2 http, mail and command, and a dry run that sends, mails and runs nothing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationLayoutHttpMailCommandStepsEngineChecks.php';

class expMigrationLayoutHttpMailCommandStepsSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationLayoutHttpMailCommandStepsEngineChecks;

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

    /** LS-1 */
    public function testLayouts()
    {
        $this->checkLayouts( $this->db );
    }

    /** LS-2 */
    public function testHttpMailCommand()
    {
        $this->checkHttpMailCommand( $this->db );
    }
}
