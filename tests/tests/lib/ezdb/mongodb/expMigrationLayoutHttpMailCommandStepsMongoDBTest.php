<?php
/**
 * The step types layout, layout_block, layout_rule, http, mail and command on MongoDB through the kernel's driver
 * (expMongoDB): a database of its own for the class, exp_test_migration_wp10e_<pid>_<random>, with every collection of
 * the kernel schema (expKernelTableSchema::install()) and the clean data, dropped after the last test. No database of
 * an installation is opened.
 *
 *  LG-1 LE-1 layouts, zones linked to a shared layout, blocks, rules, a create rolled back (or the step failing
 *            cleanly without Exponential Layouts)
 *  LG-2 LE-2 http, mail and command, and a dry run that sends, mails and runs nothing
 *
 * Needs a MongoDB server: EXP_TEST_MONGO_URI (a mongodb:// connection string), and symfony/yaml; skipped without.
 * Run: php vendor/bin/phpunit --testsuite mongodb-live
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mongodb-live
 */
require_once dirname( __DIR__, 4 ) . '/tests/kernel/classes/migration/expMigrationLayoutHttpMailCommandStepsEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('mongodb-live')]
class expMigrationLayoutHttpMailCommandStepsMongoDBTest extends PHPUnit\Framework\TestCase
{
    use expMigrationLayoutHttpMailCommandStepsEngineChecks;

    /** @var expMongoDB|null the database of the class */
    private static $mongo = null;

    /** @var string|null why the class is skipped */
    private static $skip = null;

    /** @var expMongoDB|null */
    private $db = null;

    public static function tearDownAfterClass(): void
    {
        if ( self::$mongo && self::$mongo->IsConnected && strpos( self::$mongo->DB, 'exp_test_migration_wp10e_' ) === 0 )
            self::$mongo->getClient()->dropDatabase( self::$mongo->DB );
        self::$mongo = null;
        self::$skip = null;
        parent::tearDownAfterClass();
    }

    /** @return expMongoDB|null the database of the class, made by the first test; null with self::$skip set */
    private function mongo()
    {
        if ( self::$mongo || self::$skip !== null )
            return self::$mongo;
        if ( !extension_loaded( 'mongodb' ) )
            return $this->skipClass( 'MongoDB PHP extension not loaded' );
        if ( !class_exists( 'MongoDB\\Client' ) )
            return $this->skipClass( 'MongoDB library is not installed' );
        $uri = trim( (string)getenv( 'EXP_TEST_MONGO_URI' ) );
        if ( $uri === '' )
            return $this->skipClass( 'EXP_TEST_MONGO_URI is not set (a mongodb:// connection string)' );
        $parts = parse_url( $uri );
        if ( !is_array( $parts ) || !isset( $parts['host'] ) )
            return $this->skipClass( 'EXP_TEST_MONGO_URI is not a mongodb:// connection string' );
        $db = new expMongoDB( array( 'server' => $parts['host'], 'port' => isset( $parts['port'] ) ? $parts['port'] : 27017,
                                     'user' => isset( $parts['user'] ) ? rawurldecode( $parts['user'] ) : '',
                                     'password' => isset( $parts['pass'] ) ? rawurldecode( $parts['pass'] ) : '',
                                     'database' => 'exp_test_migration_wp10e_' . getmypid() . '_' . bin2hex( random_bytes( 3 ) ),
                                     'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                     'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                     'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                     'use_persistent_connection' => false, 'show_errors' => false ) );
        if ( !$db->IsConnected )
            return $this->skipClass( 'Cannot connect to MongoDB' );
        self::$mongo = $db;
        $this->runnerBegin( $db );
        $messages = array();
        $installed = expKernelTableSchema::install( array(), $db, self::runnerRoot() . '/share/db_schema.dba', $messages );
        $loaded = $installed && $db->insertFile( self::runnerRoot() . '/kernel/sql/common', 'cleandata.sql', false ) !== false;
        $this->runnerEnd();
        $this->assertTrue( $installed, 'the kernel schema: ' . implode( '; ', array_filter( $messages, function ( $m ) { return strpos( $m, 'created ' ) !== 0; } ) ) );
        $this->assertTrue( $loaded, 'the clean data loads' );
        return $db;
    }

    private function skipClass( $reason )
    {
        self::$skip = $reason;
        return null;
    }

    protected function setUp(): void
    {
        $this->db = $this->mongo();
        if ( !$this->db )
            $this->markTestSkipped( self::$skip );
        $this->contentBegin();
        $this->runnerBegin( $this->db );
    }

    protected function tearDown(): void
    {
        $this->runnerEnd();
        $this->contentEnd();
    }

    /** LG-1 */
    public function testLayouts()
    {
        $this->checkLayouts( $this->db );
    }

    /** LG-2 */
    public function testHttpMailCommand()
    {
        $this->checkHttpMailCommand( $this->db );
    }
}
