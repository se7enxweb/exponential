<?php
/**
 * The live checks every database of the PostgreSQL wire protocol passes, against a server named by
 * EXP_TEST_PGWIRE_<PROFILE>_HOST, _PORT, _USER, _PASSWORD and _DATABASE (PROFILE: COCKROACHDB, YUGABYTEDB, SPANNER);
 * a test class per database extends it and names its profile. Without a server the tests skip.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A row of the test table on a data-table connection of the database under test */
class expPostgreSQLWireLiveNote extends eZPersistentObject
{
    /** @var string the table of the running test */
    public static $table = '';

    public static function definition()
    {
        return array(
            'fields' => array(
                'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'name' => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'flag' => array( 'name' => 'Flag', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'body' => array( 'name' => 'Body', 'datatype' => 'text', 'default' => '', 'required' => false ),
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'class_name' => 'expPostgreSQLWireLiveNote',
            'name' => self::$table,
            'connection' => 'pgwiretest',
        );
    }
}

abstract class expPostgreSQLWireLiveTestCase extends PHPUnit\Framework\TestCase
{
    /** the profile name (expPostgreSQLWireProfile) */
    const PROFILE = '';

    /** the driver class */
    const DRIVER = '';

    /** @var expPostgreSQLWireDB|null */
    protected $db = null;

    /** @var string the test table of this run */
    protected $table = '';

    /**
     * The connection parameters of the profile's server, or null when none is named.
     *
     * @return array|null
     */
    protected static function parameters()
    {
        $prefix = 'EXP_TEST_PGWIRE_' . strtoupper( static::PROFILE ) . '_';
        $host = getenv( $prefix . 'HOST' );
        if ( $host === false || $host === '' || !extension_loaded( 'pgsql' ) )
            return null;
        $profile = expPostgreSQLWireProfile::get( static::PROFILE );
        $value = function ( $key, $default ) use ( $prefix ) {
            $v = getenv( $prefix . $key );
            return ( $v === false || $v === '' ) ? $default : $v;
        };
        return array( 'server' => $host, 'port' => $value( 'PORT', $profile['port'] ), 'user' => $value( 'USER', $profile['user'] ),
                      'password' => $value( 'PASSWORD', '' ), 'database' => $value( 'DATABASE', 'exp_pgwire_test' ),
                      'use_slave_server' => false, 'slave_server' => null, 'slave_port' => null, 'slave_user' => null,
                      'slave_password' => null, 'slave_database' => null, 'socket' => false, 'charset' => 'utf-8',
                      'is_internal_charset' => true, 'builtin_encoding' => false, 'connect_retries' => 0,
                      'use_persistent_connection' => 'disabled', 'show_errors' => false,
                      // a statement that hangs fails the test instead of the job
                      'postgresql_wire' => array( 'ApplicationName' => 'Exponential tests', 'SessionStatements' => array( "SET statement_timeout = '120s'" ) ) );
    }

    /**
     * A new connection of the driver.
     *
     * @return expPostgreSQLWireDB
     */
    protected function connect()
    {
        $class = static::DRIVER;
        return new $class( static::parameters() );
    }

    protected function setUp(): void
    {
        if ( static::parameters() === null )
            $this->markTestSkipped( 'No ' . static::PROFILE . ' server: set EXP_TEST_PGWIRE_' . strtoupper( static::PROFILE ) . '_HOST (and _PORT, _USER, _PASSWORD, _DATABASE)' );
        $this->db = $this->connect();
        $this->assertTrue( $this->db->isConnected(), 'connected' );
        $this->table = 'exp_pgw_' . substr( md5( uniqid( '', true ) ), 0, 10 );
    }

    protected function tearDown(): void
    {
        if ( $this->db && $this->db->isConnected() )
        {
            $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
            while ( (int)$this->db->transactionCounter() > 0 )
                $this->db->rollback();
            foreach ( (array)$this->db->relationList() as $relation )
                if ( strpos( (string)$relation, $this->table ) === 0 )
                    @$this->db->query( 'DROP TABLE ' . $relation );
            foreach ( (array)$this->db->relationList( eZDBInterface::RELATION_SEQUENCE ) as $relation )
                if ( strpos( (string)$relation, $this->table ) === 0 )
                    @$this->db->query( 'DROP SEQUENCE ' . $relation );
            $this->db->close();
        }
        $this->db = null;
    }

    /** a table with a serial key, a unique and a plain index */
    protected function tableDefinition()
    {
        return array(
            'name' => $this->table,
            'fields' => array(
                'id' => array( 'type' => 'auto_increment', 'default' => false ),
                'name' => array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                'flag' => array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
                'body' => array( 'type' => 'longtext', 'default' => false ),
            ),
            'indexes' => array(
                'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                $this->table . '_name' => array( 'type' => 'unique', 'fields' => array( 'name' ) ),
                $this->table . '_flag' => array( 'type' => 'non-unique', 'fields' => array( 'flag' ) ),
            ),
        );
    }

    /** Creates the test table through the schema handler, with two rows */
    protected function createTable()
    {
        $schema = eZDbSchema::instance( array( 'type' => 'postgresql', 'instance' => $this->db,
                                               'schema' => array( $this->table => $this->tableDefinition() ),
                                               'data' => array( $this->table => array( 'fields' => array( 'id', 'name', 'flag', 'body' ),
                                                                                       'rows' => array( array( 1, 'one', 1, 'a' ), array( 2, "it's", 0, 'b\\c' ) ) ) ) ) );
        $this->assertInstanceOf( 'eZPgsqlSchema', $schema );
        $this->assertTrue( $schema->insertSchema( array( 'schema' => true, 'data' => true ) ), 'schema and data inserted: ' . $this->db->errorMessage() );
    }

    public function testKnowsItself()
    {
        $this->assertSame( 'postgresql', $this->db->databaseName() );
        $this->assertSame( static::PROFILE, $this->db->databaseVariant() );
        $version = $this->db->databaseServerVersion();
        $this->assertIsArray( $version );
        $this->assertNotEmpty( $version['values'][0] );
        // PGAdapter answers version() only as the whole statement, without an alias
        $rows = $this->db->arrayQuery( 'SELECT version()' );
        $this->assertNotEmpty( $rows );
        $detected = expPostgreSQLWireProfile::detect( reset( $rows[0] ) );
        $this->assertSame( static::PROFILE === 'spanner' ? 'postgresql' : static::PROFILE, $detected );
    }

    public function testSchemaDataAndNewIds()
    {
        $this->createTable();
        $rows = $this->db->arrayQuery( "SELECT id, name, body FROM {$this->table} ORDER BY name" );
        $this->assertCount( 2, $rows );
        $this->assertSame( "it's", $rows[0]['name'] );
        $this->assertSame( 'b\\c', $rows[0]['body'], 'a backslash is a character' );

        $this->assertNotFalse( $this->db->query( "INSERT INTO {$this->table} ( name, flag ) VALUES ( 'three', 3 )" ) );
        $id = $this->db->lastSerialID( $this->table, 'id' );
        $this->assertIsInt( $id );
        $this->assertGreaterThan( 0, $id );
        $row = $this->db->arrayQuery( "SELECT name FROM {$this->table} WHERE id = $id" );
        $this->assertSame( 'three', $row[0]['name'], 'lastSerialID() is the id of the row just inserted' );
        if ( static::PROFILE !== 'spanner' )
            $this->assertGreaterThan( 2, $id, 'the sequence was moved past the rows of the data (setval)' );

        $schema = eZDbSchema::instance( array( 'type' => 'postgresql', 'instance' => $this->db ) );
        $read = $schema->schema( array( 'table_include' => array( $this->table ) ) );
        $this->assertArrayHasKey( $this->table, $read );
        $this->assertSame( 'auto_increment', $read[$this->table]['fields']['id']['type'], 'the serial column is read back as one' );
    }

    public function testHelpers()
    {
        $this->createTable();
        $t = $this->table;
        $this->assertNotFalse( $this->db->query( $this->db->sqlUpsert( $t, array( 'id' => 1, 'name' => 'uno', 'flag' => 5 ), array( 'id' ) ) ) );
        $this->assertNotFalse( $this->db->query( $this->db->sqlUpsert( $t, array( 'id' => 7, 'name' => 'seven', 'flag' => 7 ), array( 'id' ) ) ) );
        $rows = $this->db->arrayQuery( "SELECT id, name, flag FROM $t ORDER BY id" );
        $this->assertSame( array( 'uno', "it's", 'seven' ), array_column( $rows, 'name' ) );

        $limited = $this->db->arrayQuery( $this->db->sqlLimit( "SELECT name FROM $t ORDER BY id", false, 1 ) );
        $this->assertSame( array( "it's", 'seven' ), array_column( $limited, 'name' ) );
        $limited = $this->db->arrayQuery( "SELECT name FROM $t ORDER BY id", array( 'limit' => 1, 'offset' => 2 ) );
        $this->assertSame( array( 2 => 'seven' ), array_map( function ( $row ) { return $row['name']; }, $limited ), 'the rows keep their offset' );

        $joined = $this->db->arrayQuery( 'SELECT ' . $this->db->sqlGroupConcat( 'name', '|', 'name' ) . " AS names FROM $t" );
        $this->assertSame( "it's|seven|uno", $joined[0]['names'] );
        $now = $this->db->arrayQuery( 'SELECT ' . $this->db->sqlUnixTimestamp() . ' AS t' );
        $this->assertEqualsWithDelta( time(), (int)$now[0]['t'], 120 );

        if ( static::PROFILE !== 'spanner' )
        {
            $md5 = $this->db->arrayQuery( 'SELECT ' . $this->db->md5( "'x'" ) . ' AS h' );
            $this->assertSame( md5( 'x' ), $md5[0]['h'] );
        }
    }

    public function testTransactions()
    {
        $this->createTable();
        $t = $this->table;
        $this->db->begin();
        $this->db->query( "UPDATE $t SET flag = 42 WHERE id = 1" );
        $this->db->rollback();
        $rows = $this->db->arrayQuery( "SELECT flag FROM $t WHERE id = 1" );
        $this->assertSame( '1', (string)$rows[0]['flag'] );

        $value = $this->db->retryTransaction( function ( $db ) use ( $t ) {
            $db->query( "UPDATE $t SET flag = 43 WHERE id = 1" );
            return 'done';
        } );
        $this->assertSame( 'done', $value );
        $rows = $this->db->arrayQuery( "SELECT flag FROM $t WHERE id = 1" );
        $this->assertSame( '43', (string)$rows[0]['flag'] );
    }

    public function testLock()
    {
        $this->createTable();
        $profile = $this->db->wireProfile();
        $this->db->lock( $this->table );
        $this->assertSame( 1, (int)$this->db->transactionCounter(), 'lock() opens a transaction' );
        if ( $profile['lock'] === expPostgreSQLWireProfile::LOCK_ROW )
        {
            // a second connection cannot take the same lock while the first holds it
            $other = $this->connect();
            $other->query( "SET statement_timeout = '1500ms'" );
            $other->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            $blocked = false;
            try
            {
                $other->begin();
                $other->query( "SELECT name FROM exp_wire_lock WHERE name = '" . strtolower( $this->table ) . "' FOR UPDATE" );
            }
            catch ( eZDBException $e )
            {
                $blocked = true;
            }
            $other->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
            while ( (int)$other->transactionCounter() > 0 )
                $other->rollback();
            $this->assertTrue( $blocked, 'the second locker waited and gave up' );
            $this->db->unlock();
            $other->lock( $this->table );
            $this->assertSame( 1, (int)$other->transactionCounter(), 'after unlock() the second locker gets it' );
            $other->unlock();
            $other->close();
        }
        else
            $this->db->unlock();
        $this->assertSame( 0, (int)$this->db->transactionCounter() );
    }

    public function testDataTableConnection()
    {
        $p = static::parameters();
        expDataConnections::setConnections( array( 'pgwiretest' => array( 'identifier' => 'pgwiretest', 'engine' => static::PROFILE, 'server' => $p['server'],
                                                                         'port' => $p['port'], 'database' => $p['database'], 'user' => $p['user'] ) ) );
        // the site's database of the test: the connection of the test itself (a persistent class asks which it is)
        $saved = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        eZDB::setInstance( $this->db );
        try
        {
            $engines = expDataConnections::engines();
            $this->assertTrue( $engines[static::PROFILE]['available'], $engines[static::PROFILE]['why'] );
            $this->assertSame( 'ez', $engines[static::PROFILE]['via'] );
            $db = expDataConnections::db( 'pgwiretest' );
            $this->assertInstanceOf( static::DRIVER, $db );

            // the table made through the data tables' schema, rows written and read by a persistent class
            expPostgreSQLWireLiveNote::$table = $this->table;
            $result = expDataTableSchema::apply( array( $this->table => $this->tableDefinition() ), $db );
            $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
            $note = new expPostgreSQLWireLiveNote( array( 'name' => "it's one", 'flag' => 1, 'body' => 'a\\b' ) );
            $note->store();
            $this->assertGreaterThan( 0, (int)$note->attribute( 'id' ), 'store() set the new id' );
            $second = new expPostgreSQLWireLiveNote( array( 'name' => 'two', 'flag' => 2, 'body' => '' ) );
            $second->store();
            $rows = eZPersistentObject::fetchObjectList( expPostgreSQLWireLiveNote::definition(), null, null, array( 'flag' => 'asc' ), null, false );
            $this->assertSame( array( "it's one", 'two' ), array_column( $rows, 'name' ) );
            $this->assertSame( 'a\\b', $rows[0]['body'] );
            $secondId = (int)$second->attribute( 'id' );
            $found = eZPersistentObject::fetchObject( expPostgreSQLWireLiveNote::definition(), null, array( 'id' => $secondId ) );
            $this->assertSame( 'two', $found->attribute( 'name' ) );
            $found->setAttribute( 'flag', 7 );
            $found->store();
            $this->assertSame( 7, (int)eZPersistentObject::fetchObject( expPostgreSQLWireLiveNote::definition(), null, array( 'id' => $secondId ) )->attribute( 'flag' ) );
            $this->assertSame( 2, (int)eZPersistentObject::count( expPostgreSQLWireLiveNote::definition() ) );
            eZPersistentObject::removeObject( expPostgreSQLWireLiveNote::definition(), array( 'id' => (int)$note->attribute( 'id' ) ) );
            $this->assertSame( 1, (int)eZPersistentObject::count( expPostgreSQLWireLiveNote::definition() ) );
        }
        finally
        {
            expDataConnections::closeAll();
            expDataConnections::setConnections( null );
            if ( $saved === null )
                unset( $GLOBALS['eZDBGlobalInstance'] );
            else
                $GLOBALS['eZDBGlobalInstance'] = $saved[0];
        }
    }

    public function testTemporaryTables()
    {
        $profile = $this->db->wireProfile();
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $made = true;
        try
        {
            $made = (bool)$this->db->createTempTable( "CREATE TEMPORARY TABLE {$this->table}_tmp ( id integer )" );
        }
        catch ( eZDBException $e )
        {
            $made = false;
        }
        $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertSame( $profile['temp_tables'] !== 'none', $made );
        if ( $made )
            $this->db->dropTempTable( "DROP TABLE {$this->table}_tmp" );
    }
}
