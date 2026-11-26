<?php
/**
 * The profiles of the databases of the PostgreSQL wire protocol (no server needed):
 *
 *  PW-01 - every profile is there, found by its name and its aliases, with the keys the driver reads
 *  PW-02 - apply(): an empty setting keeps the profile's value, a set one replaces it; an unknown pooler is auto
 *  PW-03 - pooler(): Neon's -pooler host and Supabase's port 6543 are transaction mode, Supabase's pooler host on
 *          5432 session mode, an Aurora RDS Proxy session mode; a set Pooler wins
 *  PW-04 - connectString(): sslmode, connect_timeout, application_name, values quoted for libpq (a password with a
 *          quote or a backslash), options left out behind a transaction pooler except Neon's endpoint
 *  PW-05 - sessionStatements(): CockroachDB's READ COMMITTED and temporary tables, an Isolation setting instead,
 *          SessionStatements appended, none behind a transaction pooler
 *  PW-06 - sqlState(), isRetryable() (40001, 40P01 when the profile retries), isConnectionLost()
 *  PW-07 - parseVersion() and detect() on the version() texts of CockroachDB, YugabyteDB, PGAdapter and PostgreSQL
 *  PW-08 - the row locks, insertTable(), mapType()
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('pgwire')]
class expPostgreSQLWireProfileTest extends PHPUnit\Framework\TestCase
{
    public function testEveryProfileIsThere()
    {
        $all = expPostgreSQLWireProfile::all();
        $this->assertSame( array( 'postgresql', 'cockroachdb', 'yugabytedb', 'spanner', 'neon', 'supabase', 'aurora' ), array_keys( $all ) );
        foreach ( $all as $name => $profile )
        {
            $this->assertSame( $name, $profile['name'] );
            $this->assertSame( 'postgresql', $profile['kernel_name'], "$name is PostgreSQL to the kernel" );
            foreach ( array( 'label', 'alias', 'port', 'lock', 'retries', 'session', 'sequences', 'level', 'live_tested', 'notes' ) as $key )
                $this->assertArrayHasKey( $key, $profile, "$name: $key" );
            $this->assertContains( $profile['level'], array( 'site', 'datatable', 'import' ) );
        }
        $this->assertTrue( $all['cockroachdb']['live_tested'] );
        $this->assertTrue( $all['yugabytedb']['live_tested'] );
        $this->assertTrue( $all['spanner']['live_tested'] );
        $this->assertFalse( $all['neon']['live_tested'] );
        $this->assertFalse( $all['supabase']['live_tested'] );
        $this->assertFalse( $all['aurora']['live_tested'] );
        $this->assertSame( 'datatable', $all['spanner']['level'] );
    }

    public static function aliases()
    {
        return array(
            array( 'cockroachdb', 'cockroachdb' ), array( 'CockroachDB', 'cockroachdb' ), array( 'crdb', 'cockroachdb' ),
            array( 'cockroach', 'cockroachdb' ), array( 'yugabyte', 'yugabytedb' ), array( 'ysql', 'yugabytedb' ),
            array( 'spannerpg', 'spanner' ), array( 'pgadapter', 'spanner' ), array( ' neon ', 'neon' ), array( 'neondb', 'neon' ),
            array( 'supabase', 'supabase' ), array( 'aurorapostgresql', 'aurora' ), array( 'aurora-postgresql', 'aurora' ),
            array( 'postgresql', 'postgresql' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('aliases')]
    public function testGetByNameAndAlias( $name, $expected )
    {
        $profile = expPostgreSQLWireProfile::get( $name );
        $this->assertNotNull( $profile );
        $this->assertSame( $expected, $profile['name'] );
    }

    public function testUnknownProfile()
    {
        $this->assertNull( expPostgreSQLWireProfile::get( 'oracle' ) );
        $this->assertNull( expPostgreSQLWireProfile::get( '' ) );
        $names = expPostgreSQLWireProfile::names();
        $this->assertSame( array( 'cockroachdb', 'cockroach', 'crdb' ), $names['cockroachdb'] );
    }

    public function testEveryAliasIsInSiteIni()
    {
        $ini = file_get_contents( __DIR__ . '/../../../../../settings/site.ini' );
        $classes = array( 'cockroachdb' => 'expCockroachDB', 'yugabytedb' => 'expYugabyteDB', 'spanner' => 'expSpannerPGDB',
                          'neon' => 'expNeonDB', 'supabase' => 'expSupabaseDB', 'aurora' => 'expAuroraPostgreSQLDB' );
        foreach ( expPostgreSQLWireProfile::names() as $name => $aliases )
        {
            if ( $name === 'postgresql' )
                continue;
            $class = $classes[$name];
            $this->assertSame( $name, constant( "$class::PROFILE" ) );
            $this->assertTrue( is_subclass_of( $class, 'expPostgreSQLWireDB' ) );
            foreach ( $aliases as $alias )
                $this->assertStringContainsString( "ImplementationAlias[$alias]=$class\n", $ini, "site.ini: $alias" );
        }
    }

    public function testApplyKeepsAndReplaces()
    {
        $p = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'neon' ), array( 'SSLMode' => '', 'ConnectAttempts' => '9', 'Pooler' => 'nonsense',
                                                                                         'SessionStatements' => array( ' SET a = 1 ', '' ) ) );
        $s = $p['settings'];
        $this->assertSame( 'require', $s['SSLMode'] );
        $this->assertSame( 9, $s['ConnectAttempts'] );
        $this->assertSame( 500, $s['ConnectWait'] );
        $this->assertSame( 15, $s['ConnectTimeout'] );
        $this->assertSame( 'auto', $s['Pooler'] );
        $this->assertSame( array( 'SET a = 1' ), $s['SessionStatements'] );
        $this->assertTrue( $s['ReconnectOnLoss'] );
        $p = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'cockroachdb' ), array( 'SerializationRetries' => '0', 'ReconnectOnLoss' => 'disabled' ) );
        $this->assertSame( 0, $p['settings']['SerializationRetries'] );
        $this->assertFalse( $p['settings']['ReconnectOnLoss'] );
        $this->assertSame( '', $p['settings']['SSLMode'] );
    }

    public static function poolers()
    {
        return array(
            'neon direct' => array( 'neon', 'ep-cool-1.us-east-2.aws.neon.tech', 5432, array(), 'none' ),
            'neon pooler' => array( 'neon', 'ep-cool-1-pooler.us-east-2.aws.neon.tech', 5432, array(), 'transaction' ),
            'supabase direct' => array( 'supabase', 'db.abcd.supabase.co', 5432, array(), 'none' ),
            'supabase transaction' => array( 'supabase', 'aws-0-eu-central-1.pooler.supabase.com', 6543, array(), 'transaction' ),
            'supabase session' => array( 'supabase', 'aws-0-eu-central-1.pooler.supabase.com', 5432, array(), 'session' ),
            'aurora cluster' => array( 'aurora', 'c.cluster-abc.eu-west-1.rds.amazonaws.com', 5432, array(), 'none' ),
            'aurora proxy' => array( 'aurora', 'p.proxy-abc.eu-west-1.rds.amazonaws.com', 5432, array(), 'session' ),
            'cockroach' => array( 'cockroachdb', 'crdb1', 26257, array(), 'none' ),
            'setting wins' => array( 'cockroachdb', 'pgbouncer1', 6432, array( 'Pooler' => 'transaction' ), 'transaction' ),
            'setting none wins' => array( 'neon', 'ep-1-pooler.x.neon.tech', 5432, array( 'Pooler' => 'none' ), 'none' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('poolers')]
    public function testPooler( $name, $host, $port, $settings, $expected )
    {
        $p = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( $name ), $settings );
        $this->assertSame( $expected, expPostgreSQLWireProfile::pooler( $p, $host, $port ) );
    }

    public function testConnectString()
    {
        $p = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'neon' ), array( 'Endpoint' => 'ep-cool-1', 'Options' => '-c statement_timeout=5000' ) );
        $direct = expPostgreSQLWireProfile::connectString( $p, 'ep-cool-1.aws.neon.tech', 5432, 'neondb', 'u', "pa'ss\\w" );
        $this->assertSame( "host='ep-cool-1.aws.neon.tech' port='5432' dbname='neondb' user='u' password='pa\\'ss\\\\w' sslmode='require'"
                         . " connect_timeout='15' application_name='Exponential' options='endpoint=ep-cool-1 -c statement_timeout=5000'", $direct );
        $pooled = expPostgreSQLWireProfile::connectString( $p, 'ep-cool-1-pooler.aws.neon.tech', 5432, 'neondb', 'u', 'p' );
        $this->assertStringEndsWith( "application_name='Exponential' options='endpoint=ep-cool-1'", $pooled, 'only the endpoint behind the pooler' );

        $c = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'cockroachdb' ), array( 'ApplicationName' => str_repeat( 'x', 80 ) ) );
        $s = expPostgreSQLWireProfile::connectString( $c, 'crdb1', 26257, 'exponential', 'root', '' );
        $this->assertSame( "host='crdb1' port='26257' dbname='exponential' user='root' connect_timeout='10' application_name='" . str_repeat( 'x', 63 ) . "'", $s );
        $this->assertStringNotContainsString( 'sslmode', $s );
        $this->assertStringNotContainsString( 'password', $s, 'an empty password is left out' );

        $a = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'aurora' ), array( 'SSLMode' => 'verify-full', 'SSLRootCert' => '/etc/ssl/rds.pem' ) );
        $s = expPostgreSQLWireProfile::connectString( $a, 'c.cluster-x.rds.amazonaws.com', false, 'exponential', 'postgres', 'x' );
        $this->assertStringContainsString( "sslmode='verify-full' sslrootcert='/etc/ssl/rds.pem'", $s );
        $this->assertStringNotContainsString( 'port=', $s );
    }

    public function testSessionStatements()
    {
        $c = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'cockroachdb' ), array() );
        $this->assertSame( array( "SET default_transaction_isolation = 'read committed'", "SET experimental_enable_temp_tables = 'on'" ),
                           expPostgreSQLWireProfile::sessionStatements( $c, 'none' ) );
        $this->assertSame( array(), expPostgreSQLWireProfile::sessionStatements( $c, 'transaction' ) );
        $c = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'cockroachdb' ), array( 'Isolation' => 'serializable', 'SessionStatements' => array( 'SET x = 1' ) ) );
        $this->assertSame( array( "SET default_transaction_isolation = 'serializable'", "SET experimental_enable_temp_tables = 'on'", 'SET x = 1' ),
                           expPostgreSQLWireProfile::sessionStatements( $c, 'session' ) );
        $y = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'yugabytedb' ), array( 'Isolation' => 'nonsense' ) );
        $this->assertSame( array(), expPostgreSQLWireProfile::sessionStatements( $y, 'none' ) );
    }

    public function testErrorsAndRetries()
    {
        $this->assertSame( '40001', expPostgreSQLWireProfile::sqlState( "ERROR:  40001: restart transaction: TransactionRetryWithProtoRefreshError" ) );
        $this->assertSame( '57P01', expPostgreSQLWireProfile::sqlState( 'FATAL:  57P01: terminating connection due to administrator command' ) );
        $this->assertSame( '23505', expPostgreSQLWireProfile::sqlState( 'duplicate key, SQLSTATE 23505' ) );
        $this->assertSame( '', expPostgreSQLWireProfile::sqlState( 'server closed the connection unexpectedly' ) );

        $c = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'cockroachdb' ), array() );
        $this->assertTrue( expPostgreSQLWireProfile::isRetryable( $c, '40001' ) );
        $this->assertTrue( expPostgreSQLWireProfile::isRetryable( $c, '40P01' ) );
        $this->assertFalse( expPostgreSQLWireProfile::isRetryable( $c, '23505' ) );
        $n = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'neon' ), array() );
        $this->assertFalse( expPostgreSQLWireProfile::isRetryable( $n, '40001' ), 'Neon is PostgreSQL: no retries by default' );
        $n = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( 'neon' ), array( 'SerializationRetries' => 2 ) );
        $this->assertTrue( expPostgreSQLWireProfile::isRetryable( $n, '40001' ) );

        $this->assertTrue( expPostgreSQLWireProfile::isConnectionLost( '57P01' ) );
        $this->assertTrue( expPostgreSQLWireProfile::isConnectionLost( '08006' ) );
        $this->assertTrue( expPostgreSQLWireProfile::isConnectionLost( 1, "server closed the connection unexpectedly\n\tThis probably means" ) );
        $this->assertTrue( expPostgreSQLWireProfile::isConnectionLost( 1, 'SSL SYSCALL error: EOF detected' ) );
        $this->assertFalse( expPostgreSQLWireProfile::isConnectionLost( '42P01', 'relation "x" does not exist' ) );
    }

    public static function versions()
    {
        return array(
            'cockroachdb' => array( 'cockroachdb', 'CockroachDB CCL v25.2.4 (x86_64-pc-linux-gnu, built 2025/07/31 21:20:50, go1.23.7)', '25.2.4', array( '25', '2', '4' ) ),
            'yugabytedb' => array( 'yugabytedb', 'PostgreSQL 15.12-YB-2.25.2.0-b0 on x86_64-pc-linux-gnu, compiled by clang version 19.1.0, 64-bit', '15.12-YB-2.25.2.0', array( '15', '12' ) ),
            'pgadapter' => array( 'postgresql', 'PostgreSQL 14.1', '14.1', array( '14', '1' ) ),
            'postgresql' => array( 'postgresql', 'PostgreSQL 17.2 (Debian 17.2-1.pgdg120+1) on x86_64-pc-linux-gnu', '17.2', array( '17', '2' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('versions')]
    public function testVersions( $detected, $text, $string, $values )
    {
        $this->assertSame( $detected, expPostgreSQLWireProfile::detect( $text ) );
        $profile = expPostgreSQLWireProfile::get( $detected );
        $this->assertSame( array( 'string' => $string, 'values' => $values ), expPostgreSQLWireProfile::parseVersion( $profile, $text ) );
    }

    public function testVersionWithoutNumber()
    {
        $this->assertFalse( expPostgreSQLWireProfile::parseVersion( expPostgreSQLWireProfile::get( 'cockroachdb' ), 'nothing here' ) );
    }

    public function testLocksAndInserts()
    {
        $this->assertSame( expPostgreSQLWireProfile::LOCK_ROW, expPostgreSQLWireProfile::lockMethod( expPostgreSQLWireProfile::get( 'cockroachdb' ) ) );
        $this->assertSame( expPostgreSQLWireProfile::LOCK_ROW, expPostgreSQLWireProfile::lockMethod( expPostgreSQLWireProfile::get( 'yugabytedb' ) ) );
        $this->assertSame( expPostgreSQLWireProfile::LOCK_NONE, expPostgreSQLWireProfile::lockMethod( expPostgreSQLWireProfile::get( 'spanner' ) ) );
        $this->assertSame( expPostgreSQLWireProfile::LOCK_TABLE, expPostgreSQLWireProfile::lockMethod( expPostgreSQLWireProfile::get( 'aurora' ) ) );
        $this->assertSame( 'CREATE TABLE IF NOT EXISTS exp_wire_lock ( name VARCHAR(255) NOT NULL PRIMARY KEY )', expPostgreSQLWireProfile::lockTableDDL() );
        $literal = function ( $v ) { return "'" . str_replace( "'", "''", $v ) . "'"; };
        $this->assertSame( array( "INSERT INTO exp_wire_lock ( name ) VALUES ( 'ezurlalias_ml' ) ON CONFLICT ( name ) DO NOTHING",
                                  "SELECT name FROM exp_wire_lock WHERE name = 'ezurlalias_ml' FOR UPDATE" ),
                           expPostgreSQLWireProfile::rowLockStatements( 'eZURLAlias_ML', $literal ) );

        $this->assertSame( 'ezbasket', expPostgreSQLWireProfile::insertTable( "INSERT INTO ezbasket ( session_id ) VALUES ( 'x' )" ) );
        $this->assertSame( 'ezbasket', expPostgreSQLWireProfile::insertTable( "  insert into \"ezbasket\"(id) select 1" ) );
        $this->assertSame( '', expPostgreSQLWireProfile::insertTable( 'UPDATE ezbasket SET id = 1' ) );
        $this->assertSame( '', expPostgreSQLWireProfile::insertTable( 'SELECT 1' ) );

        $spanner = expPostgreSQLWireProfile::get( 'spanner' );
        $this->assertSame( 'bigint', expPostgreSQLWireProfile::mapType( $spanner, 'integer' ) );
        $this->assertSame( 'bigint', expPostgreSQLWireProfile::mapType( $spanner, 'smallint' ) );
        $this->assertSame( 'double precision', expPostgreSQLWireProfile::mapType( $spanner, 'real' ) );
        $this->assertSame( 'varchar', expPostgreSQLWireProfile::mapType( $spanner, 'varchar' ) );
        $this->assertSame( 'int4', expPostgreSQLWireProfile::mapType( expPostgreSQLWireProfile::get( 'cockroachdb' ), 'integer' ), 'INTEGER is INT8 on CockroachDB' );
        $this->assertSame( 'integer', expPostgreSQLWireProfile::mapType( expPostgreSQLWireProfile::get( 'yugabytedb' ), 'integer' ) );
    }
}
