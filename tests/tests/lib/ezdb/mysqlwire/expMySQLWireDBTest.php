<?php
/**
 * The MySQL-wire driver (expMySQLWireDB) without a server: what it sends for each profile, its settings, its locks,
 * its databases, the failover check, and how the installer, the setup wizard and the cluster setup know the drivers.
 *
 *  MWD-01 - Settings: the defaults, site.ini and the 'mysqlwire' parameter; an unknown profile is refused
 *  MWD-02 - Statements are rewritten in their order; one the server has no use for sends nothing
 *  MWD-03 - BEGIN, never BEGIN WORK
 *  MWD-04 - Table locks: GET_LOCK() in a fixed order where LOCK TABLES does nothing, LOCK TABLES elsewhere, none
 *  MWD-05 - Foreign keys as relations only where the server keeps them; timestamps past 2038 on SingleStore
 *  MWD-06 - Databases: a case-insensitive default collation; a Vitess keyspace is used, never created or dropped
 *  MWD-07 - The session: the isolation level the profile keeps; Aurora's read-only writer is connected to again
 *  MWD-08 - The installer, the setup wizard and the cluster setup know every driver
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 * @group mysqlwire
 */

require_once __DIR__ . '/expMySQLWireTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('mysqlwire')]
class expMySQLWireDBTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    protected function setUp(): void
    {
        if ( !extension_loaded( 'mysqli' ) )
            $this->markTestSkipped( 'The mysqli extension is not loaded' );
        $this->saved = expMySQLWireTestKit::quietDebug();
    }

    protected function tearDown(): void
    {
        expMySQLWireTestKit::restoreDebug( $this->saved );
    }

    private static function driver( $profile, array $settings = array() )
    {
        return new expMySQLWireRecordingDB( array( 'Profile' => $profile ) + $settings );
    }

    /** MWD-01 */
    public function testSettings()
    {
        $settings = expMySQLWireDB::settings();
        $this->assertSame( 'auto', $settings['TableLocks'] );
        $this->assertSame( '1', $settings['TiDBAutoIdCache'] );
        $this->assertSame( 'disabled', $settings['SSL'] );
        $settings = expMySQLWireDB::settings( array( 'SSL' => 'verify', 'NamedLockTimeout' => 5, 'Nested' => array( 1 ) ) );
        $this->assertSame( 'verify', $settings['SSL'] );
        $this->assertSame( '5', $settings['NamedLockTimeout'] );
        $this->assertArrayNotHasKey( 'Nested', $settings );

        $db = self::driver( 'planetscale' );
        $this->assertSame( 'vitess', $db->mysqlWireProfile() );
        $this->assertSame( 'Vitess / PlanetScale', $db->mysqlWireFacts()['label'] );
        $this->assertSame( 'mysql', $db->databaseName() );
        $this->assertSame( 'mysql', $db->sqlHelperDialect() );

        $this->expectException( InvalidArgumentException::class );
        self::driver( 'nosuchdb' );
    }

    /** MWD-02 */
    public function testStatementsAreRewrittenInOrder()
    {
        $db = self::driver( 'singlestore' );
        $this->assertTrue( $db->query( 'RENAME TABLE a TO b, c TO d' ) );
        $this->assertSame( array( 'ALTER TABLE a RENAME TO b', 'ALTER TABLE c RENAME TO d' ), $db->sent );

        $db = self::driver( 'singlestore' );
        $this->assertTrue( $db->query( 'ALTER TABLE expaudit_event ADD FULLTEXT INDEX expaudit_event_fts (search_text)' ) );
        $this->assertSame( array(), $db->sent );

        $db = self::driver( 'tidb' );
        $db->query( "INSERT INTO t (a) VALUES ('x')" );
        $db->query( 'SELECT a FROM t WHERE id = 1 LOCK IN SHARE MODE' );
        $this->assertSame( array( "INSERT INTO t (a) VALUES ('x')", 'SELECT a FROM t WHERE id = 1 FOR UPDATE' ), $db->sent );
    }

    /** MWD-03 */
    public function testBegin()
    {
        foreach ( array( 'tidb', 'vitess', 'singlestore', 'xpand', 'aurora' ) as $profile )
        {
            $db = self::driver( $profile );
            $db->beginQuery();
            $this->assertSame( array( 'BEGIN' ), $db->sent, $profile );
        }
    }

    /** MWD-04 */
    public function testNamedTableLocks()
    {
        $db = self::driver( 'tidb', array( 'NamedLockTimeout' => '7' ) );
        $db->answers = array( '/GET_LOCK/' => array( array( 'l' => '1' ) ) );
        $this->assertSame( 'named', $db->tableLockMethod() );
        $db->lock( array( array( 'table' => 'ezorder_status' ), array( 'table' => 'ezcontent_language' ), array( 'table' => 'ezorder_status' ) ) );
        $this->assertSame( array( "SELECT GET_LOCK('exp:site.ezcontent_language', 7) AS l", "SELECT GET_LOCK('exp:site.ezorder_status', 7) AS l" ), $db->sent );
        $db->sent = array();
        $db->unlock();
        $this->assertSame( array( "SELECT RELEASE_LOCK('exp:site.ezorder_status')", "SELECT RELEASE_LOCK('exp:site.ezcontent_language')" ), $db->sent );
        $db->sent = array();
        $db->unlock();
        $this->assertSame( array(), $db->sent, 'a second unlock releases nothing' );

        // a lock not granted in time is not released later
        $db = self::driver( 'tidb' );
        $db->answers = array( '/GET_LOCK/' => array( array( 'l' => '0' ) ) );
        $db->lock( 'ezsearch_search_phrase' );
        $db->sent = array();
        $db->unlock();
        $this->assertSame( array(), $db->sent );
    }

    /** MWD-04 */
    public function testNativeAndNoTableLocks()
    {
        $db = self::driver( 'vitess' );
        $this->assertSame( 'native', $db->tableLockMethod() );
        $db->lock( array( array( 'table' => 'a' ), array( 'table' => 'b' ) ) );
        $db->unlock();
        $this->assertSame( array( 'LOCK TABLES a WRITE, b WRITE', 'UNLOCK TABLES' ), $db->sent );

        // SingleStore has no GET_LOCK(): named falls back to LOCK TABLES
        $this->assertSame( 'native', self::driver( 'singlestore' )->tableLockMethod() );
        $this->assertSame( 'native', self::driver( 'singlestore', array( 'TableLocks' => 'auto' ) )->tableLockMethod() );
        $this->assertSame( 'named', self::driver( 'aurora', array( 'TableLocks' => 'named' ) )->tableLockMethod() );

        $db = self::driver( 'tidb', array( 'TableLocks' => 'none' ) );
        $db->lock( 'a' );
        $db->unlock();
        $this->assertSame( array(), $db->sent );
    }

    /** MWD-05 */
    public function testRelationsAndTimestamps()
    {
        $this->assertSame( array( eZMySQLiDB::RELATION_FOREIGN_KEY, eZDBInterface::RELATION_TABLE ), self::driver( 'tidb' )->supportedRelationTypes() );
        $this->assertSame( array( eZDBInterface::RELATION_TABLE ), self::driver( 'vitess' )->supportedRelationTypes() );
        $this->assertSame( eZDBInterface::RELATION_TABLE_BIT, self::driver( 'singlestore' )->supportedRelationTypeMask() );
        $this->assertSame( 'UNIX_TIMESTAMP()', self::driver( 'tidb' )->sqlUnixTimestamp() );
        $this->assertSame( 'FROM_UNIXTIME(x)', self::driver( 'vitess' )->sqlFromUnixTimestamp( 'x' ) );
        $this->assertSame( "TIMESTAMPDIFF(SECOND, '1970-01-01 00:00:00', d)", self::driver( 'singlestore' )->sqlUnixTimestamp( 'd' ) );
        $this->assertSame( "TIMESTAMPADD(SECOND, n, '1970-01-01 00:00:00')", self::driver( 'singlestore' )->sqlFromUnixTimestamp( 'n' ) );
        // the other helpers are MySQL's
        $this->assertSame( "INSERT INTO `t` (`a`, `b`) VALUES (1, 'x') ON DUPLICATE KEY UPDATE `b` = VALUES(`b`)",
                           self::driver( 'singlestore' )->sqlUpsert( 't', array( 'a' => 1, 'b' => 'x' ), array( 'a' ) ) );
    }

    /** MWD-06 */
    public function testDatabases()
    {
        $db = self::driver( 'tidb' );
        $db->createDatabase( 'site' );
        $this->assertSame( array( 'CREATE DATABASE site CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci' ), $db->sent );
        $db = self::driver( 'aurora' );
        $db->createDatabase( 'site' );
        $this->assertSame( array( 'CREATE DATABASE site' ), $db->sent );
    }

    /** MWD-06 */
    public function testVitessKeyspaceIsUsedNotCreated()
    {
        $db = new class( array( 'Profile' => 'vitess' ) ) extends expMySQLWireRecordingDB
        {
            public $databases = array( 'site' );
            function availableDatabases() { return $this->databases; }
        };
        $db->createDatabase( 'site' );
        $this->assertSame( array(), $db->sent );
        $db->createDatabase( 'other' );
        $this->assertSame( array(), $db->sent );
        $this->assertSame( 1049, $db->ErrorNumber );

        $db->answers = array( '/^SHOW FULL TABLES/' => array( array( 'Tables_in_site' => 'ezuser', 'Table_type' => 'BASE TABLE' ),
                                                              array( 'Tables_in_site' => 'v1', 'Table_type' => 'VIEW' ) ) );
        $db->removeDatabase( 'site' );
        $this->assertSame( array( 'SHOW FULL TABLES FROM `site`', 'DROP TABLE IF EXISTS `site`.`ezuser`', 'DROP VIEW IF EXISTS `site`.`v1`' ), $db->sent );
    }

    /** MWD-06 */
    public function testSystemDatabasesAreHidden()
    {
        // as the servers listed them: SingleStore, TiDB, Vitess
        $this->assertSame( array( 'site' ), expMySQLWireDB::withoutSystemDatabases( array( 'cluster', 'information_schema', 'memsql', 'site' ) ) );
        $this->assertSame( array( 'test' ), expMySQLWireDB::withoutSystemDatabases( array( 'INFORMATION_SCHEMA', 'METRICS_SCHEMA', 'PERFORMANCE_SCHEMA', 'mysql', 'sys', 'test' ) ) );
        $this->assertSame( array( 'exponential' ), expMySQLWireDB::withoutSystemDatabases( array( 'exponential', 'information_schema', 'mysql', 'sys', 'performance_schema', '_vt' ) ) );
    }

    /** MWD-06: the database's character set without SHOW CREATE DATABASE, which SingleStore refuses */
    public function testCharsetVerdict()
    {
        // as the MySQL driver answers: utf8mb4 is not utf-8 by name, and the installer takes it as UTF-8 by $current
        $this->assertFalse( expMySQLWireDB::charsetVerdict( 'utf-8', 'utf8mb4', $current ) );
        $this->assertSame( 'utf8mb4', $current );
        $this->assertTrue( expMySQLWireDB::charsetVerdict( 'utf-8', 'utf8', $current ) );
        $this->assertSame( 'utf-8', $current );
        $this->assertFalse( expMySQLWireDB::charsetVerdict( 'utf-8', 'latin1', $current ) );
        $this->assertSame( 'iso-8859-1', $current );
        $this->assertSame( 'iso-8859-1', expMySQLWireDB::charsetVerdict( array( 'utf-8', 'iso-8859-1' ), 'latin1' ) );
        $this->assertFalse( expMySQLWireDB::charsetVerdict( array( 'utf-8' ), 'latin1' ) );

        $db = self::driver( 'singlestore' );
        $db->answers = array( '/information_schema\.SCHEMATA/' => array( 'utf8' ) );
        $this->assertTrue( $db->checkCharsetPriv( 'utf-8', $current ) );
        $this->assertSame( array( "SELECT DEFAULT_CHARACTER_SET_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'site'" ), $db->raw );
    }

    /** MWD-07 */
    public function testIsolationLevel()
    {
        $db = self::driver( 'tidb', array( 'IsolationLevel' => 'read committed' ) );
        $db->setUp();
        $this->assertSame( array( 'SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED' ), $db->raw );
        $db = self::driver( 'tidb', array( 'IsolationLevel' => 'SERIALIZABLE' ) );
        $db->setUp();
        $this->assertSame( array(), $db->raw, 'TiDB refuses SERIALIZABLE: nothing is sent' );
        $db = self::driver( 'vitess' );
        $db->setUp();
        $this->assertSame( array(), $db->raw, 'no level set: the server default' );
    }

    /** MWD-07 */
    public function testAuroraConnectsAgainToAReadOnlyWriter()
    {
        $db = self::driver( 'aurora', array( 'FailoverRetries' => '3' ) );
        $db->answers = array( '/innodb_read_only/' => array( 'sequence' => array( array( '1' ), array( '1' ), array( '0' ) ) ) );
        $db->setUp();
        $this->assertSame( 2, $db->reopened );
        $this->assertSame( array( 'SELECT @@innodb_read_only', 'SELECT @@innodb_read_only', 'SELECT @@innodb_read_only' ), $db->raw );

        // it gives up after FailoverRetries
        $db = self::driver( 'aurora', array( 'FailoverRetries' => '2' ) );
        $db->answers = array( '/innodb_read_only/' => array( 'sequence' => array( array( '1' ), array( '1' ), array( '1' ), array( '1' ) ) ) );
        $db->setUp();
        $this->assertSame( 2, $db->reopened );

        // other profiles do not ask, unless told to
        $db = self::driver( 'tidb' );
        $db->setUp();
        $this->assertSame( array(), $db->raw );
        $db = self::driver( 'vitess', array( 'FailoverCheck' => 'enabled' ) );
        $db->setUp();
        $this->assertSame( array( 'SELECT @@innodb_read_only' ), $db->raw );
        $db = self::driver( 'aurora', array( 'FailoverCheck' => 'disabled' ) );
        $db->setUp();
        $this->assertSame( array(), $db->raw );
    }

    /** MWD-08 */
    public function testSetupWizardKnowsTheDrivers()
    {
        require_once 'kernel/setup/ezsetupcommon.php';
        $map = eZSetupDatabaseMap();
        foreach ( array( 'tidb', 'vitess', 'singlestore', 'xpand', 'aurora' ) as $type )
        {
            $this->assertArrayHasKey( $type, $map );
            $this->assertSame( $type, $map[$type]['type'] );
            $this->assertSame( $type, $map[$type]['driver'] );
            $this->assertTrue( eZSetupIsMySQLFamily( $type ), $type );
            $this->assertSame( 'mysqli', expClusterSetup::engineOf( $type ), $type );
        }
        $this->assertTrue( eZSetupIsMySQLFamily( 'mysqli' ) );
        $this->assertFalse( eZSetupIsMySQLFamily( 'pgsql' ) );
        $this->assertFalse( eZSetupIsMySQLFamily( 'sqlserver' ) );
        $this->assertSame( 'mysqli', expClusterSetup::engineOf( 'planetscale' ) );
    }

    /** MWD-08: a data table connection names the database as its engine, or engine mysql and the option profile */
    public function testDataTableConnections()
    {
        $c = expDataConnections::normalize( array( 'identifier' => 'ps', 'engine' => 'PlanetScale', 'options' => array( 'ssl' => 'verify' ) ) );
        $this->assertSame( 'mysql', $c['engine'] );
        $this->assertSame( array( 'ssl' => 'verify', 'profile' => 'vitess' ), $c['options'] );
        $c = expDataConnections::normalize( array( 'engine' => 'mysql', 'options' => array( 'profile' => 'memsql' ) ) );
        $this->assertSame( 'singlestore', $c['options']['profile'] );
        $c = expDataConnections::normalize( array( 'engine' => 'mysql', 'options' => array( 'profile' => 'nosuch' ) ) );
        $this->assertArrayNotHasKey( 'profile', $c['options'] );

        $plan = expDataConnections::plan( array( 'identifier' => 'x', 'engine' => 'tidb', 'server' => 'tidb.example.com', 'port' => 4000,
                                                 'database' => 'd', 'options' => array( 'ssl' => 'enabled' ) ) );
        $this->assertSame( '', $plan['error'] );
        $this->assertSame( 'ez', $plan['via'] );
        $this->assertSame( 'tidb', $plan['implementation'] );
        $this->assertSame( array( 'Profile' => 'tidb', 'SSL' => 'enabled' ), $plan['parameters']['mysqlwire'] );
        $plan = expDataConnections::plan( array( 'identifier' => 'x', 'engine' => 'mysql', 'server' => 'h', 'database' => 'd' ) );
        $this->assertSame( 'ezmysqli', $plan['implementation'] );
        $this->assertArrayNotHasKey( 'mysqlwire', $plan['parameters'] );
    }

    /** MWD-08 */
    public function testInstallerKnowsTheDrivers()
    {
        $source = (string)file_get_contents( 'bin/php/install.php' );
        foreach ( array( 'tidb', 'vitess', 'singlestore', 'xpand', 'aurora' ) as $type )
            $this->assertMatchesRegularExpression( "/'$type'\s*=> array\( 'type' => '$type'/", $source );
        foreach ( array( "'planetscale' => 'vitess'", "'memsql' => 'singlestore'", "'aurora-mysql' => 'aurora'", "'db-ssl'", "'db-ssl-ca'" ) as $needle )
            $this->assertStringContainsString( $needle, $source );
    }
}
