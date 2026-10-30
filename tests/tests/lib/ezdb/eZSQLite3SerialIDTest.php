<?php
/**
 * The SQLite driver numbers a serial id that is part of a two column key.
 *
 * A database made by Exponential Platform (Doctrine) declares the id of tables such as
 * ezcontentobject_attribute NOT NULL inside a primary key of two columns (id, version). SQLite
 * numbers only a single INTEGER PRIMARY KEY, so an insert without an id failed with
 * "NOT NULL constraint failed" and creating content in the legacy admin stopped. The driver now
 * tells which tables need the id given (serialIDNeedsValue) and gives the maximum + 1
 * (nextSerialID); eZPersistentObject::storeObject() uses both.
 *
 * Database: SQLite in memory.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */
class eZSQLite3SerialIDTest extends PHPUnit\Framework\TestCase
{
    /** @var array the debug globals before the test */
    private $savedDebug = array();

    /** @var eZDBInterface|null the eZDB instance before the test */
    private $savedInstance = null;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        if ( !method_exists( 'eZDebug', 'createAccumulatorGroup' ) )
            $this->markTestSkipped( 'eZDebug is a stand-in declared by another test of this run' );
        $this->savedInstance = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        // A real debug instance: another test may have left a stand-in without the accumulator methods
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $this->savedDebug[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
    }

    protected function tearDown(): void
    {
        eZDB::setInstance( $this->savedInstance );
        foreach ( $this->savedDebug as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }

    private function db()
    {
        $db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => ':memory:',
                                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false,
                                      'slave_user' => '', 'slave_password' => '', 'slave_database' => '', 'socket' => false,
                                      'charset' => 'utf-8', 'is_internal_charset' => false, 'builtin_encoding' => false,
                                      'connect_retries' => 1, 'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->assertTrue( $db->isConnected(), 'an SQLite database in memory' );
        // As Exponential Platform creates it: the id NOT NULL in a key of two columns
        $db->query( 'CREATE TABLE platform_attr ( id INTEGER NOT NULL, version INTEGER DEFAULT 0 NOT NULL, data_text TEXT, PRIMARY KEY ( id, version ) )' );
        // As the legacy kernel's own schema has them
        $db->query( 'CREATE TABLE legacy_rowid ( id INTEGER PRIMARY KEY AUTOINCREMENT, data_text TEXT )' );
        $db->query( 'CREATE TABLE legacy_nullable ( id INTEGER, version INTEGER NOT NULL DEFAULT 0, data_text TEXT, PRIMARY KEY ( id, version ) )' );
        return $db;
    }

    /**
     * @testdox serialIDNeedsValue() is true only for a NOT NULL id in a key of two columns
     */
    public function testSerialIDNeedsValue(): void
    {
        $db = $this->db();
        $this->assertTrue( $db->serialIDNeedsValue( 'platform_attr', 'id' ) );
        $this->assertFalse( $db->serialIDNeedsValue( 'legacy_rowid', 'id' ), 'SQLite numbers a rowid alias itself' );
        $this->assertFalse( $db->serialIDNeedsValue( 'legacy_nullable', 'id' ), 'lastSerialID() numbers a nullable id after the insert' );
        $this->assertFalse( $db->serialIDNeedsValue( 'no_such_table', 'id' ) );
    }

    /**
     * @testdox nextSerialID() is the maximum + 1, and 1 for an empty table
     */
    public function testNextSerialID(): void
    {
        $db = $this->db();
        $this->assertSame( 1, $db->nextSerialID( 'platform_attr', 'id' ) );
        $db->query( "INSERT INTO platform_attr ( id, version, data_text ) VALUES ( 41, 1, 'a' )" );
        $db->query( "INSERT INTO platform_attr ( id, version, data_text ) VALUES ( 41, 2, 'b' )" );
        $this->assertSame( 42, $db->nextSerialID( 'platform_attr', 'id' ) );
    }

    /**
     * @testdox eZPersistentObject stores a new row with a NOT NULL id in a key of two columns, and numbers the next one after it
     */
    public function testStoreObjectNumbersTheId(): void
    {
        $db = $this->db();
        eZDB::setInstance( $db );
        $db->query( "INSERT INTO platform_attr ( id, version, data_text ) VALUES ( 7, 1, 'existing' )" );

        $first = new eZSQLite3SerialIDTestObject( array( 'id' => null, 'version' => 1, 'data_text' => 'new' ) );
        $first->store();
        $this->assertSame( 8, (int)$first->attribute( 'id' ) );

        $second = new eZSQLite3SerialIDTestObject( array( 'id' => null, 'version' => 1, 'data_text' => 'newer' ) );
        $second->store();
        $this->assertSame( 9, (int)$second->attribute( 'id' ) );

        $rows = $db->arrayQuery( 'SELECT id, version, data_text FROM platform_attr ORDER BY id' );
        $this->assertSame( array( '7:existing', '8:new', '9:newer' ),
            array_map( function ( $r ) { return $r['id'] . ':' . $r['data_text']; }, $rows ) );
        $this->assertSame( 0, $db->transactionCounter(), 'no transaction left open' );
    }
}

/**
 * A persistent object on the table platform_attr of eZSQLite3SerialIDTest.
 */
class eZSQLite3SerialIDTestObject extends eZPersistentObject
{
    public static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'version' => array( 'name' => 'Version', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'data_text' => array( 'name' => 'DataText', 'datatype' => 'text', 'default' => '', 'required' => false ) ),
                      'keys' => array( 'id', 'version' ),
                      'increment_key' => 'id',
                      'class_name' => 'eZSQLite3SerialIDTestObject',
                      'name' => 'platform_attr' );
    }
}
