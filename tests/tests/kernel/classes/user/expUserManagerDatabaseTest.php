<?php
/**
 * expUserManager (the exp:user commands) on the database of the database job (MySQL/MariaDB, PostgreSQL), with the
 * kernel schema and the clean data; skipped elsewhere. No mail leaves the process.
 *
 *  UD-1 UE-1 create        UD-2 UE-2 edit           UD-3 UE-3 disable and enable   UD-4 UE-4 reset the password
 *  UD-5 UE-5 registration  UD-6 UE-6 delete, find   UD-7 UE-7 list and show
 *  UD-8 to UD-14: UE-8 to UE-14 of expUserGroupsBulkEngineChecks (groups, roles, unlock, check-login, import and
 *  export, bulk, the read-only tools)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/migration/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expUserManagerEngineChecks.php';
require_once __DIR__ . '/expUserGroupsBulkEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expUserManagerDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationRunnerEngineChecks;
    use expUserManagerEngineChecks;
    use expUserGroupsBulkEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql' ), true ) )
            $this->markTestSkipped( "not a MySQL or PostgreSQL database: $engine" );
        $this->runnerBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->userEnd();
        $this->runnerEnd();
        parent::tearDown();
    }

    /** UD-1 */
    public function testCreate()
    {
        $this->checkCreate();
    }

    /** UD-2 */
    public function testEdit()
    {
        $this->checkEdit();
    }

    /** UD-3 */
    public function testDisableEnable()
    {
        $this->checkDisableEnable();
    }

    /** UD-4 */
    public function testResetPassword()
    {
        $this->checkResetPassword();
    }

    /** UD-5 */
    public function testRegistration()
    {
        $this->checkRegistration();
    }

    /** UD-6 */
    public function testDelete()
    {
        $this->checkDelete();
    }

    /** UD-7 */
    public function testList()
    {
        $this->checkList();
    }

    /** UD-8 UE-8 */
    public function testGroups()
    {
        $this->checkGroups();
    }

    /** UD-9 UE-9 */
    public function testRoles()
    {
        $this->checkRoles();
    }

    /** UD-10 UE-10 */
    public function testUnlockAndMark()
    {
        $this->checkUnlockAndMark();
    }

    /** UD-11 UE-11 */
    public function testCheckLogin()
    {
        $this->checkCheckLogin();
    }

    /** UD-12 UE-12 */
    public function testImportExport()
    {
        $this->checkImportExport();
    }

    /** UD-13 UE-13 */
    public function testBulk()
    {
        $this->checkBulk();
    }

    /** UD-14 UE-14 */
    public function testReadOnlyTools()
    {
        $this->checkReadOnlyTools();
    }
}
