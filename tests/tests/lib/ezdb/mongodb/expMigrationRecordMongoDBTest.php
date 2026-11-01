<?php
/**
 * The migration record on MongoDB through the kernel's driver (expMongoDB): the upgrade step makes the collections
 * exp_migration and exp_migration_run with expKernelTableSchema::install(), the next step copies a pre-release expupgrade_step and drops it, and the record
 * is written and read with the same SQL as on the other engines.
 *
 * Needs a MongoDB server: EXP_TEST_MONGO_URI (a mongodb:// connection string); skipped without it. Everything is
 * written to a database of its own, exp_test_migration_<pid>, which is dropped afterwards; no database of an
 * installation is opened.
 *
 *  MR-G1 create, copy and drop, re-run idempotent, write and read, exp:upgrade's record (expMigrationRecordEngineChecks)
 *
 * Run: php vendor/bin/phpunit --testsuite mongodb-live
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mongodb-live
 */

require_once dirname( __DIR__, 4 ) . '/tests/kernel/classes/migration/expMigrationRecordEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('mongodb-live')]
class expMigrationRecordMongoDBTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRecordEngineChecks;

    /** @var expMongoDB|null */
    private $db = null;

    /** @var array|null */
    private $savedGlobal = null;

    /** @var bool whether the global database was replaced */
    private $replacedGlobal = false;

    /** @var callable|null the exception handler before the test */
    private $exceptionHandler = null;

    /** @return callable|null the exception handler in place */
    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    protected function setUp(): void
    {
        $this->exceptionHandler = self::exceptionHandler();
        if ( !extension_loaded( 'mongodb' ) )
            $this->markTestSkipped( 'MongoDB PHP extension not loaded' );
        if ( !class_exists( 'MongoDB\\Client' ) )
            $this->markTestSkipped( 'MongoDB library is not installed' );
        $uri = trim( (string)getenv( 'EXP_TEST_MONGO_URI' ) );
        if ( $uri === '' )
            $this->markTestSkipped( 'EXP_TEST_MONGO_URI is not set (a mongodb:// connection string)' );
        $parts = parse_url( $uri );
        if ( !is_array( $parts ) || !isset( $parts['host'] ) )
            $this->markTestSkipped( 'EXP_TEST_MONGO_URI is not a mongodb:// connection string' );
        $this->savedGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        $this->replacedGlobal = true;
        $this->db = new expMongoDB( array( 'server' => $parts['host'], 'port' => isset( $parts['port'] ) ? $parts['port'] : 27017,
                                           'user' => isset( $parts['user'] ) ? rawurldecode( $parts['user'] ) : '',
                                           'password' => isset( $parts['pass'] ) ? rawurldecode( $parts['pass'] ) : '',
                                           'database' => 'exp_test_migration_' . getmypid(),
                                           'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                           'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                           'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                           'use_persistent_connection' => false, 'show_errors' => false ) );
        if ( !$this->db->IsConnected )
            $this->markTestSkipped( 'Cannot connect to MongoDB' );
        eZDB::setInstance( $this->db );
        expKernelTableSchema::reset();
    }

    protected function tearDown(): void
    {
        if ( $this->db && $this->db->IsConnected && strpos( $this->db->DB, 'exp_test_migration_' ) === 0 )
            $this->db->getClient()->dropDatabase( $this->db->DB );
        if ( $this->replacedGlobal )
        {
            if ( $this->savedGlobal === null )
                unset( $GLOBALS['eZDBGlobalInstance'] );
            else
                $GLOBALS['eZDBGlobalInstance'] = $this->savedGlobal[0];
        }
        expKernelTableSchema::reset();
        // the first lookup of the current user in a process may install the kernel's exception handler
        for ( $i = 0; $i < 5 && self::exceptionHandler() !== $this->exceptionHandler; $i++ )
            restore_exception_handler();
    }

    /** Drops a collection of the test database. */
    protected function dropTable( $db, $table )
    {
        $db->getClient()->selectCollection( $db->DB, $table )->drop();
    }

    /** MR-G1 */
    public function testOnMongoDB()
    {
        $this->assertSame( 'mongodb', expKernelTableSchema::type( $this->db ) );
        $this->checkCreateAndCopy( $this->db );
        $this->checkWriteAndRead( $this->db );
        $this->checkUpgradeRecord( $this->db );
    }
}
