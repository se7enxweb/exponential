<?php
/**
 * File containing the expDataTypeInventorySqliteTest class.
 *
 * The counts of class/datatype on a real engine: a SQLite database in memory, set as the global instance for the
 * test, with the three tables the counts read. The grouped queries count the class attributes and classes of the
 * defined classes only, the object attributes and objects of the current version of the published objects only, a
 * datatype nothing registers comes out as an orphan, and an Oracle style upper case result is read as well. No
 * installation.
 *
 *     php vendor/bin/phpunit --filter expDataTypeInventorySqliteTest
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDataTypeInventorySqliteTest extends ezpTestCase
{
    /** @var array|null */
    private $savedGlobal;

    /** @var eZSQLite3DB */
    private $db;

    protected function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->savedGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => ':memory:',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        eZDB::setInstance( $this->db );
        foreach ( array( 'CREATE TABLE ezcontentclass_attribute ( id INTEGER, version INTEGER, contentclass_id INTEGER, data_type_string TEXT )',
                         'CREATE TABLE ezcontentobject ( id INTEGER, current_version INTEGER, status INTEGER )',
                         'CREATE TABLE ezcontentobject_attribute ( id INTEGER, version INTEGER, contentobject_id INTEGER, contentclassattribute_id INTEGER, language_code TEXT, data_type_string TEXT )' ) as $sql )
            $this->assertNotFalse( $this->db->query( $sql ) );
        // classes 1 and 2 defined (version 0), class 3 only a temporary draft (version 1)
        foreach ( array( array( 1, 0, 1, 'ezstring' ), array( 2, 0, 1, 'ezstring' ), array( 3, 0, 2, 'ezstring' ), array( 4, 0, 2, 'oldtype' ),
                         array( 5, 1, 3, 'ezimage' ), array( 1, 1, 1, 'ezstring' ) ) as $r )
            $this->db->query( "INSERT INTO ezcontentclass_attribute VALUES ( {$r[0]}, {$r[1]}, {$r[2]}, '{$r[3]}' )" );
        // objects 10 and 11 published at version 2 and 1, 12 a draft (status 0), 13 archived (status 2)
        foreach ( array( array( 10, 2, 1 ), array( 11, 1, 1 ), array( 12, 1, 0 ), array( 13, 1, 2 ) ) as $r )
            $this->db->query( "INSERT INTO ezcontentobject VALUES ( {$r[0]}, {$r[1]}, {$r[2]} )" );
        $id = 100;
        foreach ( array( array( 1, 10, 1, 'eng-GB', 'ezstring' ), array( 2, 10, 1, 'eng-GB', 'ezstring' ), array( 2, 10, 1, 'ger-DE', 'ezstring' ),
                         array( 2, 10, 2, 'eng-GB', 'ezstring' ), array( 2, 10, 4, 'eng-GB', 'oldtype' ),
                         array( 1, 11, 3, 'eng-GB', 'ezstring' ), array( 1, 11, 4, 'eng-GB', 'oldtype' ),
                         array( 1, 12, 1, 'eng-GB', 'ezstring' ), array( 1, 13, 1, 'eng-GB', 'ezstring' ) ) as $r )
            $this->db->query( 'INSERT INTO ezcontentobject_attribute VALUES ( ' . $id++ . ", {$r[0]}, {$r[1]}, {$r[2]}, '{$r[3]}', '{$r[4]}' )" );
    }

    protected function tearDown(): void
    {
        if ( $this->savedGlobal === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        elseif ( isset( $this->savedGlobal ) )
            $GLOBALS['eZDBGlobalInstance'] = $this->savedGlobal[0];
        parent::tearDown();
    }

    public function testClassUsageCountsDefinedClassesOnly()
    {
        $this->assertSame( array( 'ezstring' => array( 'class_attributes' => 3, 'classes' => 2 ),
                                  'oldtype' => array( 'class_attributes' => 1, 'classes' => 1 ) ),
                           expDataTypeInventory::classUsage( $this->db ) );
    }

    public function testObjectUsageCountsCurrentPublishedVersionsOnly()
    {
        // object 10: version 2 has three ezstring rows (two languages of attribute 1, one of 2) and one oldtype;
        // object 11: version 1 has one of each; 12 (draft) and 13 (archived) are not counted, nor version 1 of 10
        $this->assertSame( array( 'ezstring' => array( 'object_attributes' => 4, 'objects' => 2 ),
                                  'oldtype' => array( 'object_attributes' => 2, 'objects' => 2 ) ),
                           expDataTypeInventory::objectUsage( $this->db ) );
    }

    public function testAttributeUsage()
    {
        $usage = expDataTypeInventory::attributeUsage( $this->db );
        $this->assertSame( array( 'object_attributes' => 2, 'objects' => 1 ), $usage[1] );
        $this->assertSame( array( 'object_attributes' => 1, 'objects' => 1 ), $usage[2] );
        $this->assertSame( array( 'object_attributes' => 1, 'objects' => 1 ), $usage[3] );
        $this->assertSame( array( 'object_attributes' => 2, 'objects' => 2 ), $usage[4] );
    }

    public function testADatatypeNothingRegistersIsAnOrphan()
    {
        $registry = array( 'ezstring' => array( 'string' => 'ezstring', 'name' => 'Text line', 'class' => 'eZStringType',
                                                'file' => 'kernel/classes/datatypes/ezstring/ezstringtype.php', 'provider' => 'kernel',
                                                'registered' => true, 'created' => true, 'flags' => expDataTypeInventory::flagsOf( null ) ) );
        $built = expDataTypeInventory::build( $registry, expDataTypeInventory::classUsage( $this->db ), expDataTypeInventory::objectUsage( $this->db ) );
        $this->assertSame( array( 'kernel', 'orphans' ), array_keys( $built['groups'] ) );
        $orphan = $built['groups']['orphans']['rows'][0];
        $this->assertSame( 'oldtype', $orphan['string'] );
        $this->assertSame( 'orphan', $orphan['status'] );
        $this->assertSame( 1, $orphan['class_attributes'] );
        $this->assertSame( 2, $orphan['objects'] );
        $this->assertSame( 1, $built['summary']['orphans'] );
        $this->assertSame( 2, $built['summary']['orphan_object_attributes'] );
    }

    public function testUpperCaseColumnNamesAreRead()
    {
        $db = new expDataTypeInventorySqliteTestUpperCaseDB( $this->db );
        $this->assertSame( array( 'ezstring' => array( 'class_attributes' => 3, 'classes' => 2 ),
                                  'oldtype' => array( 'class_attributes' => 1, 'classes' => 1 ) ), expDataTypeInventory::classUsage( $db ) );
    }
}

/** Answers like Oracle does: the column names in upper case */
class expDataTypeInventorySqliteTestUpperCaseDB
{
    private $db;

    public function __construct( $db ) { $this->db = $db; }

    public function databaseName() { return 'oracle'; }

    public function arrayQuery( $sql )
    {
        return array_map( function ( $row ) { return array_change_key_case( $row, CASE_UPPER ); }, $this->db->arrayQuery( $sql ) );
    }
}
