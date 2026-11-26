<?php
/**
 * The driver of the databases of the PostgreSQL wire protocol, without a server (the parts that need none):
 *
 *  PD-01 - databaseName() is postgresql, the SQL helper dialect too; databaseVariant() names the database
 *  PD-02 - md5() is the built-in md5(), never pgcrypto's digest()
 *  PD-03 - Spanner: no LIMIT ALL, no FOR UPDATE; CockroachDB: as PostgreSQL
 *  PD-04 - retries: a 40001 outside a transaction is sent again up to SerializationRetries times; inside a
 *          transaction never; an error that is no serialization failure never
 *  PD-05 - a lost connection outside a transaction: connected again and sent again once; ReconnectOnLoss=disabled
 *          leaves the error
 *  PD-06 - lastSerialID() answers from RETURNING where currval() cannot (Spanner, a transaction pooler)
 *  PD-07 - Aurora's replica check asks pg_is_in_recovery() alone; PostgreSQL's wants a streaming WAL receiver
 *  PD-08 - a temporary table on Spanner is an error, not a statement
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** The driver with the server replaced: sendAgain() answers from a script, connect() counts */
class expPostgreSQLWireDBTestDouble extends expPostgreSQLWireDB
{
    /** @var array of results (false: fails with the SQLSTATE in $states) */
    public $script = array();
    public $states = array();
    public $sent = 0;
    public $connects = 0;

    public static function make( $profile, array $settings = array() )
    {
        $db = ( new ReflectionClass( __CLASS__ ) )->newInstanceWithoutConstructor();
        $db->Profile = expPostgreSQLWireProfile::apply( expPostgreSQLWireProfile::get( $profile ), $settings + array( 'RetryWait' => 0 ) );
        $db->Pooler = expPostgreSQLWireProfile::pooler( $db->Profile, 'localhost', 5432 );
        $db->TransactionCounter = 0;
        $db->IsConnected = true;
        $db->DBConnection = false;
        return $db;
    }

    protected function sendAgain( $sql )
    {
        $this->sent++;
        $result = array_shift( $this->script );
        if ( !$result )
        {
            $this->ErrorNumber = array_shift( $this->states );
            $this->ErrorMessage = 'failed';
        }
        return $result;
    }

    protected function connect()
    {
        $this->connects++;
        return 'connection';
    }

    protected function startSession()
    {
    }

    protected function connectionAlive()
    {
        return true;
    }

    protected function setErrorVerbosity()
    {
    }

    public function retry( $sql, $state, $message = 'failed' )
    {
        $this->ErrorNumber = $state;
        $this->ErrorMessage = $message;
        return $this->retryFailedQuery( $sql, false );
    }

    public function setLastInsertId( $table, $id )
    {
        $this->LastInsertIds[$table] = $id;
    }

    public function replicaStatus()
    {
        return $this->replicaStatusQuery();
    }

    public function returning()
    {
        return $this->needsReturning();
    }
}

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('pgwire')]
class expPostgreSQLWireDBTest extends PHPUnit\Framework\TestCase
{
    public function testNames()
    {
        $db = expPostgreSQLWireDBTestDouble::make( 'cockroachdb' );
        $this->assertSame( 'postgresql', $db->databaseName() );
        $this->assertSame( 'postgresql', $db->sqlHelperDialect() );
        $this->assertSame( 'cockroachdb', $db->databaseVariant() );
        $this->assertSame( 'CockroachDB', $db->wireProfile()['label'] );
        $this->assertTrue( $db instanceof eZPostgreSQLDB, 'the kernel sees a PostgreSQL driver' );
    }

    public function testMd5()
    {
        foreach ( array( 'cockroachdb', 'yugabytedb', 'neon', 'supabase', 'aurorapostgresql' ) as $profile )
        {
            $md5 = expPostgreSQLWireDBTestDouble::make( $profile )->md5( 'name' );
            $this->assertSame( ' md5( name ) ', $md5, $profile );
        }
    }

    public function testHelpers()
    {
        $spanner = expPostgreSQLWireDBTestDouble::make( 'spanner' );
        $this->assertSame( "SELECT * FROM t\nLIMIT 9223372036854775807 OFFSET 5", $spanner->sqlLimit( 'SELECT * FROM t', false, 5 ) );
        $this->assertSame( "SELECT * FROM t\nLIMIT 10 OFFSET 5", $spanner->sqlLimit( 'SELECT * FROM t', 10, 5 ) );
        $this->assertNull( $spanner->sqlLimit( 'SELECT * FROM t', 0 ) );
        $this->assertSame( 'SELECT * FROM t WHERE id = 1', $spanner->sqlForUpdate( 'SELECT * FROM t WHERE id = 1;' ) );
        $crdb = expPostgreSQLWireDBTestDouble::make( 'cockroachdb' );
        $this->assertSame( "SELECT * FROM t\nLIMIT ALL OFFSET 5", $crdb->sqlLimit( 'SELECT * FROM t', false, 5 ) );
        $this->assertSame( 'SELECT * FROM t WHERE id = 1 FOR UPDATE', $crdb->sqlForUpdate( 'SELECT * FROM t WHERE id = 1' ) );
        $this->assertStringContainsString( 'ON CONFLICT', $crdb->sqlUpsert( 't', array( 'id' => 1, 'v' => 2 ), array( 'id' ) ) );
    }

    public function testSerializationFailureIsSentAgain()
    {
        $db = expPostgreSQLWireDBTestDouble::make( 'cockroachdb' );
        $db->script = array( false, false, 'result' );
        $db->states = array( '40001', '40001' );
        $this->assertSame( 'result', $db->retry( 'UPDATE t SET a = 1', '40001' ) );
        $this->assertSame( 3, $db->sent );
        $this->assertSame( 3, $db->Retries );
    }

    public function testRetriesEnd()
    {
        $db = expPostgreSQLWireDBTestDouble::make( 'cockroachdb', array( 'SerializationRetries' => 2 ) );
        $db->script = array( false, false, 'never' );
        $db->states = array( '40001', '40001' );
        $this->assertFalse( $db->retry( 'UPDATE t SET a = 1', '40001' ) );
        $this->assertSame( 2, $db->sent );
    }

    public function testOtherErrorsAreNotRepeated()
    {
        $db = expPostgreSQLWireDBTestDouble::make( 'cockroachdb' );
        $db->script = array( 'result' );
        $this->assertFalse( $db->retry( 'INSERT INTO t VALUES (1)', '23505' ) );
        $this->assertSame( 0, $db->sent );
        $db->script = array( false, 'result' );
        $db->states = array( '23505' );
        $this->assertFalse( $db->retry( 'INSERT INTO t VALUES (1)', '40001' ), 'a duplicate key after the retry stops it' );
        $this->assertSame( 1, $db->sent );
    }

    public function testNothingIsRepeatedInsideATransaction()
    {
        $db = expPostgreSQLWireDBTestDouble::make( 'cockroachdb' );
        $db->TransactionCounter = 1;
        $db->script = array( 'result' );
        $this->assertFalse( $db->retry( 'UPDATE t SET a = 1', '40001' ) );
        $this->assertFalse( $db->retry( 'UPDATE t SET a = 1', '57P01' ) );
        $this->assertSame( 0, $db->sent );
        $this->assertSame( 0, $db->connects );
    }

    public function testPostgreSQLProfilesDoNotRetryByDefault()
    {
        $db = expPostgreSQLWireDBTestDouble::make( 'supabase' );
        $db->script = array( 'result' );
        $this->assertFalse( $db->retry( 'UPDATE t SET a = 1', '40001' ) );
        $this->assertSame( 0, $db->sent );
    }

    public function testLostConnectionIsOpenedAgain()
    {
        $db = expPostgreSQLWireDBTestDouble::make( 'aurorapostgresql' );
        $db->script = array( 'result' );
        $this->assertSame( 'result', $db->retry( 'SELECT 1', '57P01', 'FATAL:  57P01: terminating connection due to administrator command' ) );
        $this->assertSame( 1, $db->connects );
        $this->assertSame( 'connection', $db->DBConnection );
        $this->assertTrue( $db->isConnected() );

        $off = expPostgreSQLWireDBTestDouble::make( 'aurorapostgresql', array( 'ReconnectOnLoss' => 'disabled' ) );
        $off->script = array( 'result' );
        $this->assertFalse( $off->retry( 'SELECT 1', '08006' ) );
        $this->assertSame( 0, $off->connects );
    }

    public function testLastSerialIDFromReturning()
    {
        $spanner = expPostgreSQLWireDBTestDouble::make( 'spanner' );
        $this->assertTrue( $spanner->returning(), 'Spanner has no currval()' );
        $this->assertFalse( $spanner->lastSerialID( 'exp_t', 'id' ), 'nothing inserted yet' );
        $spanner->setLastInsertId( 'exp_t', 4611686018427387904 );
        $this->assertSame( 4611686018427387904, $spanner->lastSerialID( 'EXP_T', 'id' ) );

        $this->assertFalse( expPostgreSQLWireDBTestDouble::make( 'cockroachdb' )->returning() );
        $pooled = expPostgreSQLWireDBTestDouble::make( 'supabase', array( 'Pooler' => 'transaction' ) );
        $this->assertTrue( $pooled->returning(), 'behind a transaction pooler currval() may ask another session' );
    }

    public function testReplicaStatus()
    {
        $this->assertSame( "SELECT CASE WHEN pg_is_in_recovery() THEN 'streaming' ELSE 'primary' END",
                           expPostgreSQLWireDBTestDouble::make( 'aurorapostgresql' )->replicaStatus() );
        $this->assertStringContainsString( 'pg_stat_wal_receiver', expPostgreSQLWireDBTestDouble::make( 'neon' )->replicaStatus() );
    }

    public function testNoTemporaryTablesOnSpanner()
    {
        $db = expPostgreSQLWireDBTestDouble::make( 'spanner' );
        $this->assertFalse( $db->createTempTable( 'CREATE TEMPORARY TABLE t ( id bigint )' ) );
        $this->assertSame( '0A000', $db->errorNumber() );
    }
}
