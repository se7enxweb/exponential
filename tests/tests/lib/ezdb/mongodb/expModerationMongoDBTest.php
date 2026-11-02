<?php
/**
 * Dashboard > Recent content on MongoDB through the kernel's driver (expMongoDB): a database of its own,
 * exp_test_moderation_<pid>_<random>, with every collection of the kernel schema and the clean data, dropped after
 * the test (expModerationEngineChecks, ME-1 to ME-6). Also the object states on MongoDB: the review hold's group is
 * found by its identifier (not the first group) and its states by theirs.
 *
 * Needs a MongoDB server: EXP_TEST_MONGO_URI (a mongodb:// connection string); skipped without.
 * Run: php vendor/bin/phpunit --testsuite mongodb-live
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mongodb-live
 */

require_once dirname( __DIR__, 4 ) . '/tests/kernel/classes/moderation/expModerationEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('mongodb-live')]
class expModerationMongoDBTest extends PHPUnit\Framework\TestCase
{
    use expModerationEngineChecks;

    /** @var expMongoDB|null */
    private $db = null;

    protected function setUp(): void
    {
        if ( !extension_loaded( 'mongodb' ) )
            $this->markTestSkipped( 'MongoDB PHP extension not loaded' );
        if ( !class_exists( 'MongoDB\\Client' ) )
            $this->markTestSkipped( 'MongoDB library is not installed' );
        $uri = trim( (string)getenv( 'EXP_TEST_MONGO_URI' ) );
        $parts = $uri !== '' ? parse_url( $uri ) : false;
        if ( !is_array( $parts ) || !isset( $parts['host'] ) )
            $this->markTestSkipped( 'EXP_TEST_MONGO_URI is not set (a mongodb:// connection string)' );
        $db = new expMongoDB( array( 'server' => $parts['host'], 'port' => isset( $parts['port'] ) ? $parts['port'] : 27017,
                                     'user' => isset( $parts['user'] ) ? rawurldecode( $parts['user'] ) : '',
                                     'password' => isset( $parts['pass'] ) ? rawurldecode( $parts['pass'] ) : '',
                                     'database' => 'exp_test_moderation_' . getmypid() . '_' . bin2hex( random_bytes( 3 ) ),
                                     'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                     'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                     'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                     'use_persistent_connection' => false, 'show_errors' => false ) );
        if ( !$db->IsConnected )
            $this->markTestSkipped( 'Cannot connect to MongoDB' );
        $this->db = $db;
        $this->moderationBegin( $db );
        $root = dirname( __DIR__, 5 );
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array(), $db, $root . '/share/db_schema.dba', $messages ),
                           'the kernel schema: ' . implode( '; ', array_filter( $messages, function ( $m ) { return strpos( $m, 'created ' ) !== 0; } ) ) );
        $this->assertNotFalse( $db->insertFile( $root . '/kernel/sql/common', 'cleandata.sql', false ), 'the clean data loads' );
    }

    protected function tearDown(): void
    {
        $this->moderationEnd();
        if ( $this->db && $this->db->IsConnected && strpos( $this->db->DB, 'exp_test_moderation_' ) === 0 )
            $this->db->getClient()->dropDatabase( $this->db->DB );
        $this->db = null;
        parent::tearDown();
    }

    public function testModerationList()
    {
        // MongoDB has no SQLite-style join or ROWNUM tricks: every query of the list is a single-collection read
        self::$rows = 1100;
        $this->checkModerationList( $this->db );
    }
}
