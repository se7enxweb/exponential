<?php
/**
 * A persistent class on a named connection (eZPersistentObject::dbFor(), onConnection(); expDataConnections;
 * doc/eng-us/features/6.0/data-tables.md, "Persistent classes on a connection"). No installation: the site's database
 * is a SQLite database in memory set as the global instance for the test, the connection a SQLite file under
 * var/datatable/, removed afterwards.
 *
 *  PC-01 - store() of a new object writes to the connection and sets the increment key; the site's database has no
 *          such table
 *  PC-02 - fetchObject(), fetchObjectList() and count() read from the connection
 *  PC-03 - a quote, a backslash and both survive store, a condition and an update: the values are escaped for the
 *          connection's engine, not the site's
 *  PC-04 - store() of a changed object updates its row; updateObjectList() and removeObject() work on the connection
 *  PC-05 - after every call the connection in use is the site's again ($connectionDB is null), also after a failure
 *  PC-06 - the class map of the settings ([ClassSettings] Connection[<class>]) puts a class without 'connection' on
 *          the connection
 *  PC-07 - a class of the site's database is read on the site's database, also from inside a call on a connection
 *  PC-08 - dbFor(): the site's instance for a definition without connection, the connection's for one with
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableTestLoader.php';

/** A row of the table t691_note on the connection t691test */
class expPOConnectionTestNote extends eZPersistentObject
{
    public static $connection = 't691test';

    public static function definition()
    {
        $definition = array(
            'fields' => array(
                'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'title' => array( 'name' => 'Title', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'body' => array( 'name' => 'Body', 'datatype' => 'text', 'default' => '', 'required' => false ),
                'contentobject_id' => array( 'name' => 'ContentObjectID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'sort' => array( 'id' => 'asc' ),
            'class_name' => 'expPOConnectionTestNote',
            'name' => 't691_note',
        );
        if ( self::$connection !== null )
            $definition['connection'] = self::$connection;
        return $definition;
    }
}

/** A row of the table t691_site on the site's database */
class expPOConnectionTestSiteRow extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'name' => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'class_name' => 'expPOConnectionTestSiteRow',
            'name' => 't691_site',
        );
    }

    /** reads the site's table: called from a function attribute of a connection's object, say */
    public static function names()
    {
        $rows = self::fetchObjectList( self::definition(), null, null, null, null, false );
        return array_column( (array)$rows, 'name' );
    }
}

class expPersistentObjectOnConnectionTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $file;

    /** @var mixed the global instance before the test */
    private $savedGlobal;

    /** @var eZSQLite3DB the site's database of the test */
    private $site;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        if ( !method_exists( 'eZPersistentObject', 'dbFor' ) )
            $this->markTestSkipped( 'eZPersistentObject of another checkout was loaded first (no dbFor())' );
        $this->savedGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        $this->site = new eZSQLite3DB( self::parameters( ':memory:' ) );
        $this->assertTrue( $this->site->isConnected() );
        $this->site->query( 'CREATE TABLE t691_site ( id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL DEFAULT \'\' )' );
        $this->site->query( "INSERT INTO t691_site ( name ) VALUES ( 'on the site' )" );
        eZDB::setInstance( $this->site );

        $relative = 'var/datatable/t691-po-' . getmypid() . '.sqlite';
        $this->file = eZSys::rootDir() . '/' . $relative;
        expPOConnectionTestNote::$connection = 't691test';
        expDataConnections::setConnections( array( 't691test' => array( 'engine' => 'sqlite', 'database' => $relative ) ) );
        $db = expDataConnections::db( 't691test' );
        $db->query( 'CREATE TABLE t691_note ( id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL DEFAULT \'\', body TEXT, contentobject_id INTEGER NOT NULL DEFAULT 0 )' );
    }

    protected function tearDown(): void
    {
        expDataConnections::setConnections( null );
        expDataConnections::setClassMap( null );
        expPOConnectionTestNote::$connection = 't691test';
        if ( $this->savedGlobal === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        elseif ( isset( $this->savedGlobal ) )
            $GLOBALS['eZDBGlobalInstance'] = $this->savedGlobal[0];
        foreach ( array( '', '-wal', '-shm', '-journal' ) as $suffix )
            if ( $this->file && is_file( $this->file . $suffix ) )
                unlink( $this->file . $suffix );
    }

    private static function parameters( $database )
    {
        return array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $database,
                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                      'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                      'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                      'use_persistent_connection' => false, 'show_errors' => false );
    }

    private static function note( $title, $body = '', $objectID = 0 )
    {
        return new expPOConnectionTestNote( array( 'id' => null, 'title' => $title, 'body' => $body, 'contentobject_id' => $objectID ) );
    }

    private static function connectionInUse()
    {
        $property = new ReflectionProperty( 'eZPersistentObject', 'connectionDB' );
        return $property->getValue();
    }

    /** PC-01 */
    public function testStoreWritesToTheConnection()
    {
        $note = self::note( 'first', 'body', 42 );
        $note->store();
        $this->assertSame( 1, (int)$note->attribute( 'id' ) );
        $rows = expDataConnections::db( 't691test' )->arrayQuery( 'SELECT * FROM t691_note' );
        $this->assertCount( 1, $rows );
        $this->assertSame( 'first', $rows[0]['title'] );
        $this->assertTrue( expKernelTableSchema::exists( 't691_note', expDataConnections::db( 't691test' ), true ) );
        $this->assertFalse( expKernelTableSchema::exists( 't691_note', $this->site, true ), 'the site\'s database has no t691_note' );
        $this->assertNull( self::connectionInUse() );
    }

    /** PC-02 */
    public function testReadsFromTheConnection()
    {
        self::note( 'a', '', 1 )->store();
        self::note( 'b', '', 2 )->store();
        self::note( 'c', '', 2 )->store();
        $def = expPOConnectionTestNote::definition();
        $one = eZPersistentObject::fetchObject( $def, null, array( 'title' => 'b' ) );
        $this->assertInstanceOf( 'expPOConnectionTestNote', $one );
        $this->assertSame( 2, (int)$one->attribute( 'contentobject_id' ) );
        $list = eZPersistentObject::fetchObjectList( $def, null, array( 'contentobject_id' => 2 ), array( 'id' => 'asc' ) );
        $this->assertSame( array( 'b', 'c' ), array_map( function ( $n ) { return $n->attribute( 'title' ); }, $list ) );
        $this->assertSame( 3, (int)eZPersistentObject::count( $def ) );
        $rows = eZPersistentObject::fetchObjectList( $def, null, null, null, array( 'offset' => 1, 'length' => 1 ), false );
        $this->assertCount( 1, $rows );
        $this->assertSame( 'b', reset( $rows )['title'] );
        $this->assertNull( self::connectionInUse() );
    }

    /** PC-03 */
    public function testEscapingIsTheConnections()
    {
        $values = array( "it's", 'back\\slash', "both \\' here", "two '' quotes" );
        foreach ( $values as $value )
            self::note( $value, $value )->store();
        $def = expPOConnectionTestNote::definition();
        foreach ( $values as $value )
        {
            $found = eZPersistentObject::fetchObject( $def, null, array( 'title' => $value ) );
            $this->assertInstanceOf( 'expPOConnectionTestNote', $found, "condition on: $value" );
            $this->assertSame( $value, $found->attribute( 'body' ), "stored: $value" );
            $found->setAttribute( 'body', $value . '!' );
            $found->store();
            $again = eZPersistentObject::fetchObject( $def, null, array( 'id' => $found->attribute( 'id' ) ) );
            $this->assertSame( $value . '!', $again->attribute( 'body' ), "updated: $value" );
        }
        $this->assertSame( count( $values ), (int)eZPersistentObject::count( $def ) );
    }

    /** PC-04 */
    public function testUpdateAndRemove()
    {
        $note = self::note( 'old', '', 5 );
        $note->store();
        $note->setAttribute( 'title', 'new' );
        $note->store();
        $def = expPOConnectionTestNote::definition();
        $this->assertSame( 1, (int)eZPersistentObject::count( $def ) );
        $this->assertSame( 'new', eZPersistentObject::fetchObject( $def, null, array( 'id' => $note->attribute( 'id' ) ) )->attribute( 'title' ) );

        self::note( 'other', '', 5 )->store();
        eZPersistentObject::updateObjectList( array( 'definition' => $def, 'update_fields' => array( 'contentobject_id' => 9 ),
                                                     'conditions' => array( 'contentobject_id' => 5 ) ) );
        $this->assertSame( 2, (int)eZPersistentObject::count( $def, array( 'contentobject_id' => 9 ) ) );

        eZPersistentObject::removeObject( $def, array( 'title' => 'other' ) );
        $this->assertSame( 1, (int)eZPersistentObject::count( $def ) );
        $note->remove();
        $this->assertSame( 0, (int)eZPersistentObject::count( $def ) );
        $this->assertNull( self::connectionInUse() );
    }

    /** PC-05 */
    public function testTheConnectionInUseIsResetAfterAFailure()
    {
        expDataConnections::setConnections( array( 't691test' => array( 'engine' => 'sqlite', 'database' => 'var/datatable/t691-po-' . getmypid() . '.sqlite' ),
                                                    'broken' => array( 'engine' => 'nosuch' ) ) );
        expPOConnectionTestNote::$connection = 'broken';
        try
        {
            eZPersistentObject::fetchObjectList( expPOConnectionTestNote::definition() );
            $this->fail( 'a connection that cannot be opened throws' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'nosuch', $e->getMessage() );
        }
        $this->assertNull( self::connectionInUse() );

        // an exception thrown inside a call on the connection leaves it reset too
        expPOConnectionTestNote::$connection = 't691test';
        $db = expDataConnections::db( 't691test' );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            eZPersistentObject::fetchObjectList( array( 'name' => 't691_nosuchtable' ) + expPOConnectionTestNote::definition() );
        }
        catch ( Throwable $e )
        {
        }
        $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
        $this->assertNull( self::connectionInUse() );
    }

    /** PC-06 */
    public function testTheClassMapOfTheSettings()
    {
        expPOConnectionTestNote::$connection = null;
        expDataConnections::setClassMap( array( 'expPOConnectionTestNote' => 't691test' ) );
        $note = self::note( 'mapped' );
        $note->store();
        $rows = expDataConnections::db( 't691test' )->arrayQuery( "SELECT title FROM t691_note" );
        $this->assertSame( 'mapped', $rows[0]['title'] );
        $this->assertSame( 1, (int)eZPersistentObject::count( expPOConnectionTestNote::definition() ) );
    }

    /** PC-07 */
    public function testASiteClassInsideACallOnAConnection()
    {
        $this->assertSame( array( 'on the site' ), expPOConnectionTestSiteRow::names() );
        $inside = null;
        $method = new ReflectionMethod( 'eZPersistentObject', 'onConnection' );
        $result = $method->invoke( null, expDataConnections::db( 't691test' ), function () use ( &$inside ) {
            $inside = expPOConnectionTestSiteRow::names();
            return 'done';
        } );
        $this->assertSame( 'done', $result );
        $this->assertSame( array( 'on the site' ), $inside, 'the site\'s class read the site\'s database' );
        $this->assertNull( self::connectionInUse() );
    }

    /** PC-08 */
    public function testDbFor()
    {
        $this->assertSame( $this->site, eZPersistentObject::dbFor( expPOConnectionTestSiteRow::definition() ) );
        $this->assertSame( expDataConnections::db( 't691test' ), eZPersistentObject::dbFor( expPOConnectionTestNote::definition() ) );
        $this->assertSame( $this->site, eZPersistentObject::dbFor( array( 'connection' => 'site', 'class_name' => 'X' ) ) );
        $this->assertSame( $this->site, eZPersistentObject::dbFor( null ) );
    }
}
