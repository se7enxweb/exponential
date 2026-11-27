<?php
/**
 * Couchbase and CouchDB as data table connections (expDataConnections, engines couchbase and couchdb), against the
 * live servers of expCouchTestKit (each skips without its server). The site's database is SQLite in memory.
 *
 *  CT-01 - The connection: opened through expDataConnections with its secret, tested (version, tables)
 *  CT-02 - expDataTableSchema: plan and apply create the table of a persistent class; a field added later is an
 *          upgrade (ALTER TABLE ... ADD); the describer reads the columns from the kept definition
 *  CT-03 - A persistent class on the connection: store() of new objects (ids 1, 2, 3), fetchObject(),
 *          fetchObjectList() with conditions, sorts and a limit, count(), store() of a change, removeObject()
 *  CT-04 - expDataTableWriter: upsert by a key that is no primary key, unchanged rows left alone
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 2 ) . '/datatable/expDataTableTestLoader.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdb/couch/expCouchTestKit.php';

/** A row of cl_note on the connection of the test */
class expCouchTestNote extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'title' => array( 'name' => 'Title', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'body' => array( 'name' => 'Body', 'datatype' => 'text', 'default' => '', 'required' => false ),
                'remote_id' => array( 'name' => 'RemoteID', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'contentobject_id' => array( 'name' => 'ContentObjectID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'sort' => array( 'id' => 'asc' ),
            'class_name' => 'expCouchTestNote',
            'name' => 'cl_note',
            'connection' => 'docs',
        );
    }
}

#[\PHPUnit\Framework\Attributes\Group('kernel')]
#[\PHPUnit\Framework\Attributes\Group('datatable')]
#[\PHPUnit\Framework\Attributes\Group('couch')]
#[\PHPUnit\Framework\Attributes\Group('couch-live')]
class expCouchDataTableLiveTest extends PHPUnit\Framework\TestCase
{
    private $saved;

    private $savedGlobal;

    public static function products()
    {
        return array( 'couchbase' => array( 'couchbase' ), 'couchdb' => array( 'couchdb' ) );
    }

    protected function setUp(): void
    {
        $this->saved = expCouchTestKit::quietDebug();
        $this->savedGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $site = new eZSQLite3DB( expCouchTestKit::parameters( array( 'server' => '', 'user' => '', 'password' => '', 'database' => ':memory:' ) ) );
        eZDB::setInstance( $site );
    }

    protected function tearDown(): void
    {
        expDataConnections::setConnections( null );
        if ( $this->savedGlobal === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        else
            $GLOBALS['eZDBGlobalInstance'] = $this->savedGlobal[0];
        expKernelTableSchema::reset();
        expCouchTestKit::restoreDebug( $this->saved );
    }

    /**
     * The connection 'docs' to a product, cl_note dropped.
     */
    private function connect( $product )
    {
        $live = expCouchTestKit::live( $product );
        $driver = expCouchTestKit::liveDriver( $product, array(), $product === 'couchbase' ? 'exp_live_dt' : 'exp_live_dt' );
        if ( is_string( $driver ) )
            $this->markTestSkipped( $driver );
        $driver->query( 'DROP TABLE IF EXISTS cl_note' );
        $driver->close();
        putenv( 'EXP_TEST_COUCH_SECRET=' . $live['password'] );
        expDataConnections::setConnections( array( 'docs' => array( 'engine' => $product, 'server' => $live['host'], 'port' => $live['port'],
                                                                     'database' => $product === 'couchbase' ? $live['bucket'] . '.exp_live_dt' : 'exp_live_dt',
                                                                     'user' => $live['user'], 'secret' => $live['password'] !== '' ? 'env:EXP_TEST_COUCH_SECRET' : '' ) ) );
        return expDataConnections::db( 'docs' );
    }

    /** CT-01, CT-02, CT-03 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testConnectionSchemaAndClass( $product )
    {
        $db = $this->connect( $product );
        $this->assertSame( $product, $db->databaseName() );
        $test = expDataConnections::test( 'docs' );
        $this->assertTrue( $test['ok'], $test['error'] );
        $this->assertSame( 'ez', $test['via'] );
        $this->assertNotSame( '', $test['version'] );

        $schema = expDataTableSchema::fromDefinition( expCouchTestNote::definition() );
        $plan = expDataTableSchema::plan( $schema, $db );
        $this->assertSame( $product, $plan['engine'] );
        $this->assertSame( 'create', $plan['tables']['cl_note']['action'] );
        $this->assertStringStartsWith( 'CREATE TABLE cl_note (', implode( "\n", $plan['tables']['cl_note']['statements'] ) );
        $result = expDataTableSchema::apply( $schema, $db );
        $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        $this->assertTrue( expKernelTableSchema::exists( 'cl_note', $db, true ) );
        $this->assertSame( 'none', expDataTableSchema::plan( $schema, $db )['tables']['cl_note']['action'] );
        $columns = expDataTableDescriber::columns( $db, 'cl_note' );
        $this->assertSame( 'auto_increment', $columns['id']['type'] );
        $this->assertArrayHasKey( 'title', $columns );

        $schema['cl_note']['fields']['rating'] = array( 'type' => 'int', 'length' => 11, 'not_null' => '1', 'default' => 0 );
        $plan = expDataTableSchema::plan( $schema, $db );
        $this->assertSame( 'upgrade', $plan['tables']['cl_note']['action'] );
        $this->assertStringContainsString( 'ALTER TABLE cl_note ADD rating int(11) NOT NULL DEFAULT 0', implode( "\n", $plan['tables']['cl_note']['statements'] ) );
        $this->assertTrue( expDataTableSchema::apply( $schema, $db )['ok'] );
        $this->assertArrayHasKey( 'rating', expDataTableDescriber::columns( $db, 'cl_note' ) );

        $ids = array();
        foreach ( array( array( 'first', 'r1', 42 ), array( "it's second", 'r2', 42 ), array( 'third', 'r3', 7 ) ) as $values )
        {
            $note = new expCouchTestNote( array( 'id' => null, 'title' => $values[0], 'body' => 'b', 'remote_id' => $values[1], 'contentobject_id' => $values[2] ) );
            $note->store();
            $ids[] = (int)$note->attribute( 'id' );
        }
        $this->assertSame( array( 1, 2, 3 ), $ids );
        $def = expCouchTestNote::definition();
        $one = eZPersistentObject::fetchObject( $def, null, array( 'title' => "it's second" ) );
        $this->assertInstanceOf( 'expCouchTestNote', $one );
        $this->assertSame( 2, (int)$one->attribute( 'id' ) );
        $list = eZPersistentObject::fetchObjectList( $def, null, array( 'contentobject_id' => 42 ), array( 'id' => 'desc' ), array( 'offset' => 0, 'length' => 1 ) );
        $this->assertSame( array( "it's second" ), array_map( function ( $n ) { return $n->attribute( 'title' ); }, $list ) );
        $this->assertSame( 3, (int)eZPersistentObject::count( $def ) );
        $this->assertSame( 2, (int)eZPersistentObject::count( $def, array( 'contentobject_id' => 42 ) ) );
        $this->assertSame( 1, (int)eZPersistentObject::count( $def, array( 'title' => array( 'like', 'th%' ) ) ) );
        $one->setAttribute( 'title', 'changed' );
        $one->store();
        $this->assertSame( 'changed', eZPersistentObject::fetchObject( $def, null, array( 'id' => 2 ) )->attribute( 'title' ) );
        eZPersistentObject::removeObject( $def, array( 'id' => 1 ) );
        $this->assertSame( array( 2, 3 ), array_map( function ( $n ) { return (int)$n->attribute( 'id' ); }, eZPersistentObject::fetchObjectList( $def ) ) );
    }

    /** CT-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('products')]
    public function testWriter( $product )
    {
        $db = $this->connect( $product );
        $this->assertTrue( expDataTableSchema::apply( expDataTableSchema::fromDefinition( expCouchTestNote::definition() ), $db )['ok'] );
        $writer = new expDataTableWriter( 'expCouchTestNote', array( 'keys' => array( 'remote_id' ), 'mode' => 'upsert' ) );
        $result = $writer->write( array( array( 'remote_id' => 'a-1', 'title' => 'First' ), array( 'remote_id' => 'a-2', 'title' => 'Second' ) ) );
        $this->assertSame( 2, $result['inserted'], json_encode( $result['errors'] ) );
        $result = $writer->write( array( array( 'remote_id' => 'a-1', 'title' => 'First' ), array( 'remote_id' => 'a-2', 'title' => 'Second, changed' ) ) );
        $this->assertSame( array( 1, 1 ), array( $result['unchanged'], $result['updated'] ), json_encode( $result ) );
        $rows = $db->arrayQuery( 'SELECT remote_id, title FROM cl_note ORDER BY remote_id' );
        $this->assertSame( array( 'First', 'Second, changed' ), array_column( $rows, 'title' ) );
    }
}
?>
