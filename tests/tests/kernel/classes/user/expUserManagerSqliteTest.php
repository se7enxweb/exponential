<?php
/**
 * expUserManager (the exp:user commands) on SQLite in process: a SQLite file under var/tmp with the kernel schema and
 * the clean data, removed afterwards. No server and no installation's database; no mail leaves the process.
 *
 *  US-1 UE-1 create        US-2 UE-2 edit           US-3 UE-3 disable and enable   US-4 UE-4 reset the password
 *  US-5 UE-5 registration  US-6 UE-6 delete, find   US-7 UE-7 list and show
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/migration/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expUserManagerEngineChecks.php';

class expUserManagerSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;
    use expUserManagerEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

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
    }

    protected function tearDown(): void
    {
        $this->userEnd();
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( 10 ) ?: new eZUser( array() ), 10, eZUser::NO_SESSION_REGENERATE );
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
    }

    /** US-1 */
    public function testCreate()
    {
        $this->checkCreate();
    }

    /** US-2 */
    public function testEdit()
    {
        $this->checkEdit();
    }

    /** US-3 */
    public function testDisableEnable()
    {
        $this->checkDisableEnable();
    }

    /** US-4 */
    public function testResetPassword()
    {
        $this->checkResetPassword();
    }

    /** US-5 */
    public function testRegistration()
    {
        $this->checkRegistration();
    }

    /** US-6 */
    public function testDelete()
    {
        $this->checkDelete();
    }

    /** US-7 */
    public function testList()
    {
        $this->checkList();
    }
}
