<?php
/**
 * The details of a signed-in session (expuser_session_info, expUserSessionInfo), the local GeoIP lookup
 * (expUserGeoIP), how addresses are kept ([UserSessionSettings] RecordIP) and the per-request state of
 * expUserSessionTracker. No installation: the rules are plain functions; the tables run on a SQLite database in memory
 * built from the kernel schema.
 *
 *  SI-01 - An address is kept in full, as its network or not at all; "audit" follows the audit's actor.ip rule
 *  SI-02 - A GeoIP record gives the country and "City, Region, Country"; private addresses are never looked up; no
 *          database configured means no lookup
 *  SI-03 - A new country is a "new location" only when earlier sign-ins had a country and none had this one
 *  SI-04 - A session kept by Remember me is not timed out before its remember_until
 *  SI-05 - A method hint is used once and only in the request that set it (persistent workers); the second step is
 *          kept with it
 *  SI-06 - The table is in share/db_schema.dba, the persistent object, the kernel schemas and the update files
 *  SI-07 - On a database: details recorded and read with the list, countries of a user, removed by the retention and
 *          with an erased user; the export leaves out the token's hash
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../datatypes/eZDatatypeTestFixtures.php';

class expUserSessionInfoTest extends eZDatatypeTestCase
{
    const COLUMNS = array( 'auth_detail', 'country', 'host', 'location', 'session_id', 'user_id' );

    /** @var array|null */
    private $server = null;

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $file )
    {
        return file_get_contents( self::root() . '/' . $file );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->server = $_SERVER;
        expUserSessionTracker::reset();
        expUserGeoIP::setLookup( null );
        if ( class_exists( 'expKernelTableSchema' ) )
            expKernelTableSchema::reset();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        expUserSessionTracker::reset();
        expUserGeoIP::setLookup( null );
        if ( class_exists( 'expKernelTableSchema' ) )
            expKernelTableSchema::reset();
        parent::tearDown();
    }

    /** SI-01 */
    public function testAddressModes()
    {
        $this->assertSame( '203.0.113.57', expUserSessionTracker::storedIP( '203.0.113.57', 'full' ) );
        $this->assertSame( '203.0.113.x', expUserSessionTracker::storedIP( '203.0.113.57', 'truncated' ) );
        $this->assertSame( '2001:db8:85a3::/48', expUserSessionTracker::storedIP( '2001:db8:85a3:8d3:1319:8a2e:370:7348', 'truncated' ) );
        $this->assertSame( '', expUserSessionTracker::storedIP( '203.0.113.57', 'disabled' ) );
        $this->assertSame( '', expUserSessionTracker::storedIP( '', 'full' ) );
        $this->assertSame( 'disabled', expUserSessionTracker::modeFromAuditField( 'off' ) );
        $this->assertSame( 'truncated', expUserSessionTracker::modeFromAuditField( 'truncate' ) );
        $this->assertSame( 'truncated', expUserSessionTracker::modeFromAuditField( 'hash' ) );
        $this->assertSame( 'full', expUserSessionTracker::modeFromAuditField( 'full' ) );
        // the shipped settings: "audit", and the audit truncates addresses
        $this->assertSame( 'audit', eZINI::instance()->variable( 'UserSessionSettings', 'RecordIP' ) );
        $this->assertContains( expUserSessionTracker::recordIPMode(), array( 'full', 'truncated', 'disabled' ) );
    }

    /** SI-02 */
    public function testGeoIPRecord()
    {
        $city = array( 'city' => array( 'names' => array( 'en' => 'Berlin', 'de' => 'Berlin' ) ),
                       'subdivisions' => array( array( 'names' => array( 'en' => 'Land Berlin' ) ) ),
                       'country' => array( 'iso_code' => 'de', 'names' => array( 'en' => 'Germany', 'de' => 'Deutschland' ) ) );
        $this->assertSame( array( 'country' => 'DE', 'location' => 'Berlin, Land Berlin, Germany' ), expUserGeoIP::fromRecord( $city ) );
        $this->assertSame( array( 'country' => 'DE', 'location' => 'Berlin, Land Berlin, Deutschland' ), expUserGeoIP::fromRecord( $city, 'de' ) );
        $this->assertSame( array( 'country' => 'FR', 'location' => 'FR' ), expUserGeoIP::fromRecord( array( 'registered_country' => array( 'iso_code' => 'FR' ) ) ) );
        $this->assertNull( expUserGeoIP::fromRecord( array( 'continent' => array( 'code' => 'EU' ) ) ) );

        $this->assertTrue( expUserGeoIP::isPublic( '8.8.8.8' ) );
        $this->assertFalse( expUserGeoIP::isPublic( '10.0.0.1' ) );
        $this->assertFalse( expUserGeoIP::isPublic( '127.0.0.1' ) );
        $this->assertFalse( expUserGeoIP::isPublic( 'fd00::1' ) );
        $this->assertFalse( expUserGeoIP::isPublic( 'unknown' ) );

        $asked = array();
        expUserGeoIP::setLookup( function ( $ip ) use ( &$asked, $city ) {
            $asked[] = $ip;
            return $city;
        } );
        $this->assertTrue( expUserGeoIP::available() );
        $this->assertSame( 'DE', expUserGeoIP::lookup( '::ffff:8.8.4.4' )['country'] );
        $this->assertNull( expUserGeoIP::lookup( '192.168.1.10' ), 'a private address is not looked up' );
        $this->assertSame( array( '8.8.4.4' ), $asked );
        expUserGeoIP::setLookup( function () { throw new RuntimeException( 'broken database' ); } );
        $this->assertNull( expUserGeoIP::lookup( '8.8.8.8' ), 'a failing reader gives no place' );

        expUserGeoIP::setLookup( null );
        $this->assertSame( '', (string)eZINI::instance()->variable( 'UserSessionSettings', 'GeoIPDatabase' ), 'off by default' );
        $this->assertFalse( expUserGeoIP::available() );
        $this->assertNull( expUserGeoIP::lookup( '8.8.8.8' ) );
    }

    /** SI-03 */
    public function testNewLocationRule()
    {
        $this->assertFalse( expUserSessionTracker::isNewCountryAmong( 'DE', array() ), 'nothing to compare with' );
        $this->assertFalse( expUserSessionTracker::isNewCountryAmong( '', array( 'DE' ) ), 'no country known' );
        $this->assertFalse( expUserSessionTracker::isNewCountryAmong( 'de', array( 'DE', 'FR' ) ) );
        $this->assertTrue( expUserSessionTracker::isNewCountryAmong( 'BR', array( 'DE', 'FR' ) ) );
    }

    /** SI-04 */
    public function testRememberMeKeepsTheSession()
    {
        $now = 1800000000;
        $day = 86400;
        $this->assertFalse( expUserSessionTracker::hasTimedOut( $now - 10, 0, 3600, $now ) );
        $this->assertTrue( expUserSessionTracker::hasTimedOut( $now - 7200, 0, 3600, $now ) );
        $this->assertFalse( expUserSessionTracker::hasTimedOut( $now - 7200, $now + 300 * $day, 3600, $now ), 'kept until remember_until' );
        $this->assertTrue( expUserSessionTracker::hasTimedOut( $now - 7200, $now - 1, 3600, $now ) );
        $this->assertFalse( expUserSessionTracker::hasTimedOut( $now - 9999999, 0, 0, $now ), 'no timeout' );
    }

    /** SI-05 */
    public function testMethodHintStaysInItsRequest()
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = 1800000000.125;
        expUserSessionTracker::setMethodHint( 'two_factor', 'totp' );
        $this->assertSame( 'two_factor', expUserSessionTracker::detectMethod() );
        $this->assertSame( 'unknown', expUserSessionTracker::detectMethod(), 'used once' );

        expUserSessionTracker::setMethodHint( 'two_factor', 'recovery' );
        $_SERVER['REQUEST_TIME_FLOAT'] = 1800000001.5;
        $this->assertNotSame( 'two_factor', expUserSessionTracker::detectMethod(), 'the next request of a persistent worker does not get it' );

        expUserSessionTracker::setMethodHint( 'no_such_method', 'totp' );
        $this->assertNotSame( 'no_such_method', expUserSessionTracker::detectMethod() );
        $this->assertSame( array( 'host' => 'www.example.com', 'country' => 'DE', 'location' => 'Berlin', 'auth_detail' => '' ),
                           expUserSessionInfo::clean( array( 'host' => "WWW.Example.com\n", 'country' => 'de', 'location' => 'Berlin', 'auth_detail' => 'sms' ) ) );
        $this->assertSame( 'totp', expUserSessionInfo::clean( array( 'auth_detail' => 'totp' ) )['auth_detail'] );
    }

    /** SI-06 */
    public function testSchemaEverywhere()
    {
        $schema = null;
        include self::root() . '/share/db_schema.dba';
        $this->assertArrayHasKey( 'expuser_session_info', $schema );
        $this->assertSame( self::COLUMNS, array_keys( $schema['expuser_session_info']['fields'] ) );
        $this->assertSame( array( 'session_id' ), $schema['expuser_session_info']['indexes']['PRIMARY']['fields'] );
        $definition = expUserSessionInfo::definition();
        $columns = array_keys( $definition['fields'] );
        sort( $columns );
        $this->assertSame( self::COLUMNS, $columns );
        $this->assertSame( array( 'session_id' ), $definition['keys'] );
        $pattern = '/CREATE TABLE (?:IF NOT EXISTS )?`?expuser_session_info`? \((.*?)\n\s*\)/s';
        foreach ( array( 'kernel/sql/mysql/kernel_schema.sql', 'kernel/sql/postgresql/kernel_schema.sql', 'kernel/sql/sqlite/schema.sql',
                         'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql', 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql',
                         'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' ) as $file )
        {
            $sql = self::read( $file );
            $this->assertMatchesRegularExpression( $pattern, $sql, $file );
            preg_match( $pattern, $sql, $m );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|varchar|character)/m', $m[1], $cols );
            $this->assertSame( self::COLUMNS, $cols[1], $file );
            $this->assertStringContainsString( 'expuser_session_info_user', $sql, $file );
        }
        $this->assertStringContainsString( 'expUserSessionInfo::TABLE', self::read( 'update/common/scripts/6.0/createusersessiontable.php' ) );
        foreach ( array_merge( self::COLUMNS, array( 'expuser_session_info', 'expuser_session_info_user', 'expuser_session_info_pkey' ) ) as $name )
            $this->assertLessThanOrEqual( 30, strlen( $name ), "Oracle: $name" );
    }

    private function sqlite()
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        $db = new eZSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => ':memory:',
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
        $schema = self::read( 'kernel/sql/sqlite/schema.sql' );
        foreach ( array( 'expuser_session', 'expuser_session_info' ) as $table )
        {
            preg_match( '/CREATE TABLE `' . $table . '` \(.*?\n\);/s', $schema, $m );
            $db->query( $m[0] );
        }
        preg_match_all( '/^CREATE (?:UNIQUE )?INDEX expuser_session_[a-z_]+ ON expuser_session[a-z_]* .*;$/m', $schema, $ix );
        foreach ( $ix[0] as $statement )
            $db->query( rtrim( $statement, ';' ) );
        $db->query( "CREATE TABLE ezuser ( contentobject_id INTEGER NOT NULL DEFAULT 0, email varchar(150) NOT NULL DEFAULT '',
                     login varchar(150) NOT NULL DEFAULT '', password_hash varchar(255) DEFAULT NULL,
                     password_hash_type INTEGER NOT NULL DEFAULT 1, PRIMARY KEY ( contentobject_id ) )" );
        foreach ( array( 14 => 'anna', 15 => 'bob' ) as $id => $login )
            $db->query( "INSERT INTO ezuser ( contentobject_id, email, login, password_hash, password_hash_type ) VALUES ( $id, '$login@example.invalid', '$login', 'x', 7 )" );
        eZDB::setInstance( $db );
        expKernelTableSchema::reset();
        expUserSessionTracker::reset();
        return $db;
    }

    private function row( $userID, $browser, $created, array $more = array() )
    {
        $row = new expUserSession( $more + array( 'user_id' => $userID, 'token_hash' => hash( 'sha256', uniqid( '', true ) . $browser . $created ),
            'created' => $created, 'last_active' => $created, 'first_ip' => '203.0.113.x', 'last_ip' => '203.0.113.x',
            'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 ' . $browser . '/131.0', 'browser' => $browser,
            'os' => 'Linux', 'device_type' => 'desktop', 'method' => 'password', 'remember_until' => 0, 'siteaccess' => 'site',
            'ended' => 0, 'ended_by' => 0, 'end_reason' => '' ) );
        $row->store();
        return $row;
    }

    /** SI-07 */
    public function testDetailsOnADatabase()
    {
        $this->sqlite();
        $this->assertTrue( expUserSessionInfo::available() );
        $now = time();
        $a = $this->row( 14, 'Firefox', $now - 60 );
        $b = $this->row( 14, 'Chrome', $now - 30, array( 'remember_until' => $now + 86400 ) );
        expUserSessionInfo::record( $a->attribute( 'id' ), 14, array( 'host' => 'www.example.com', 'country' => 'DE', 'location' => 'Berlin, Germany', 'auth_detail' => 'totp' ) );
        expUserSessionInfo::record( $b->attribute( 'id' ), 14, array( 'host' => 'admin.example.com', 'country' => 'FR', 'location' => 'Paris, France' ) );
        $this->assertNull( expUserSessionInfo::record( 0, 14, array( 'host' => 'x' ) ) );
        $this->assertNull( expUserSessionInfo::record( 99, 14, array() ), 'nothing to store' );

        $this->assertEqualsCanonicalizing( array( 'DE', 'FR' ), expUserSessionInfo::countriesOfUser( 14 ) );
        $this->assertSame( array( 'FR' ), expUserSessionInfo::countriesOfUser( 14, (int)$a->attribute( 'id' ) ) );

        $list = expUserSessionTracker::listForUser( 14 );
        $byID = array_column( $list['active'], null, 'id' );
        $this->assertSame( array( 'www.example.com', 'DE', 'Berlin, Germany', 'totp', false ),
                           array( $byID[(int)$a->attribute( 'id' )]['host'], $byID[(int)$a->attribute( 'id' )]['country'], $byID[(int)$a->attribute( 'id' )]['location'],
                                  $byID[(int)$a->attribute( 'id' )]['auth_detail'], $byID[(int)$a->attribute( 'id' )]['remember'] ) );
        $this->assertTrue( $byID[(int)$b->attribute( 'id' )]['remember'] );

        $export = expUserSessionTracker::exportForUser( 14 );
        $this->assertCount( 2, $export );
        $this->assertArrayNotHasKey( 'token_hash', $export[0] );
        $this->assertSame( 'Berlin, Germany', $export[0]['location'], 'oldest first' );

        // the retention removes the details with their session
        $old = $this->row( 14, 'Safari', $now - 400 * 86400, array( 'ended' => $now - 399 * 86400, 'end_reason' => 'signout' ) );
        expUserSessionInfo::record( $old->attribute( 'id' ), 14, array( 'country' => 'BR' ) );
        expUserSessionTracker::cleanup( $now );
        $this->assertSame( array(), expUserSessionInfo::fetchForSessions( array( (int)$old->attribute( 'id' ) ) ) );
        $this->assertCount( 2, expUserSessionInfo::fetchForSessions( array( (int)$a->attribute( 'id' ), (int)$b->attribute( 'id' ) ) ) );

        // erasure: every row and detail of the user, nothing of another
        $other = $this->row( 15, 'Firefox', $now );
        expUserSessionInfo::record( $other->attribute( 'id' ), 15, array( 'country' => 'DE' ) );
        $this->assertSame( 2, expUserSessionTracker::eraseForUser( 14 ) );
        $this->assertSame( array(), expUserSession::fetchForUser( 14, false ) );
        $this->assertSame( array(), expUserSessionInfo::countriesOfUser( 14 ) );
        $this->assertSame( array( 'DE' ), expUserSessionInfo::countriesOfUser( 15 ) );
    }
}
