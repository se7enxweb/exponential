<?php
/**
 * Relations between data tables and content (expDataTableRelation; doc/eng-us/features/6.0/data-tables.md,
 * "Relations") and the template fetch functions over them (expDataTableFunctionCollection, "In templates"), on the
 * SQLite connection of the fixture (expDataTableSqliteFixture) with a catalogue set for the test. The policy checks of
 * the fetch functions ask the current user; the test user is anonymous, so what needs datatable/read is checked as
 * refused.
 *
 *  DR-01 - isReference(): @object:, @node:, @path: with a value; anything else is not
 *  DR-02 - url() of each kind: node, class, section and a table need no database; an empty value has none
 *  DR-03 - pointingAt(): a declared relation of a test class finds its rows (count, first rows, more), by node and by
 *          object; a relation inferred from a name is skipped unless ContentTabInferred=enabled (default off)
 *  DR-04 - pointingAt() skips the kernel's tables and an extension's class over a kernel table
 *  DR-05 - fetch list/count/row: conditions only on fields of the class, sort by a field, the limit bounded
 *  DR-06 - a class the catalogue does not have, a malformed name, an empty key: nothing; can() for an unknown
 *          function is false
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableSqliteFixture.php';

/** A class over a table of the site's database (the kernel's tables are skipped) */
class expDTKernelTableRow extends eZPersistentObject
{
    public static function definition()
    {
        return array( 'fields' => array( 'node_id' => array( 'name' => 'NodeID', 'datatype' => 'integer', 'default' => 0, 'required' => true ) ),
                      'keys' => array( 'node_id' ), 'class_name' => 'expDTKernelTableRow', 'name' => 't691_kernel_like',
                      'relations' => array( 'node_id' => array( 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id' ) ) );
    }
}

/** An extension's class over the same table */
class expDTKernelTableRowAlias extends expDTKernelTableRow
{
}

class expDataTableRelationTest extends PHPUnit\Framework\TestCase
{
    use expDataTableSqliteFixture;

    protected function setUp(): void
    {
        $kernel = expDataTableCatalogue::entryFromDefinition( 'expDTKernelTableRow', expDTKernelTableRow::definition(), '', 'kernel' );
        // an extension's class over the same table
        $overKernel = expDataTableCatalogue::entryFromDefinition( 'expDTKernelTableRow', expDTKernelTableRow::definition(), '', 'someextension' );
        $overKernel['class'] = 'expDTKernelTableRowAlias';
        $kernel['class'] = 'expDTKernelTableRow';
        $this->setUpFixture( true, array( $kernel, $overKernel ) );
        $this->fixtureSite->query( 'CREATE TABLE t691_kernel_like ( node_id INTEGER NOT NULL DEFAULT 0 )' );
        $this->fixtureSite->query( 'INSERT INTO t691_kernel_like ( node_id ) VALUES ( 5 )' );
        $writer = new expDataTableWriter( 'expDTFixtureRow', array( 'keys' => array( 'remote_id' ) ) );
        $rows = array();
        for ( $i = 1; $i <= 12; $i++ )
            $rows[] = array( 'remote_id' => "r$i", 'title' => "T$i", 'node_id' => $i <= 11 ? 5 : 6, 'contentobject_id' => $i <= 2 ? 50 : 0, 'price' => $i );
        $result = $writer->write( $rows );
        $this->assertSame( 12, $result['inserted'], json_encode( $result['errors'] ) );
    }

    protected function tearDown(): void
    {
        $this->tearDownFixture();
    }

    /** DR-01 */
    public function testIsReference()
    {
        foreach ( array( '@object:abc', '@node:1', '@path:a/b' ) as $value )
            $this->assertTrue( expDataTableRelation::isReference( $value ), $value );
        foreach ( array( '@object:', 'object:abc', '@user:1', 42, null, '' ) as $value )
            $this->assertFalse( expDataTableRelation::isReference( $value ), var_export( $value, true ) );
        $this->assertNull( expDataTableRelation::resolveReference( '@unknown:x', 'object' ) );
    }

    /** DR-02 */
    public function testUrl()
    {
        $relation = function ( $kind, $class = '' ) { return array( 'column' => 'c', 'kind' => $kind, 'class' => $class, 'field' => 'id', 'source' => 'declared' ); };
        $this->assertSame( 'content/view/full/7', expDataTableRelation::url( $relation( 'node' ), 7 ) );
        $this->assertSame( 'class/view/3', expDataTableRelation::url( $relation( 'class' ), '3' ) );
        $this->assertSame( 'section/view/1', expDataTableRelation::url( $relation( 'section' ), 1 ) );
        $this->assertSame( 'datatable/rows/expDTFixtureRow', expDataTableRelation::url( $relation( 'table', 'expDTFixtureRow' ), 9 ) );
        $this->assertSame( '', expDataTableRelation::url( $relation( 'node' ), '' ) );
        $this->assertSame( '', expDataTableRelation::url( $relation( 'node' ), 0 ) );
        $this->assertSame( '', expDataTableRelation::url( $relation( 'node' ), 'abc' ) );
        $this->assertNull( expDataTableRelation::target( $relation( 'node' ), 0 ) );
    }

    /** DR-03 */
    public function testPointingAtFindsDeclaredRelationsOnly()
    {
        $this->assertFalse( expDataTableRelation::includeInferred(), 'ContentTabInferred is disabled by default' );
        $found = expDataTableRelation::pointingAt( 50, 5 );
        $this->assertCount( 1, $found, 'node_id is declared; contentobject_id is only inferred from its name' );
        $this->assertSame( 'expDTFixtureRow', $found[0]['class'] );
        $this->assertSame( 'node_id', $found[0]['column'] );
        $this->assertSame( 'node', $found[0]['kind'] );
        $this->assertSame( 11, $found[0]['count'] );
        $this->assertCount( expDataTableRelation::POINTING_LIMIT, $found[0]['rows'] );
        $this->assertTrue( $found[0]['more'] );
        $this->assertContains( 'title', $found[0]['fields'] );
        $this->assertCount( 3, expDataTableRelation::pointingAt( 50, 5, 3 )[0]['rows'] );
        $this->assertSame( 1, expDataTableRelation::pointingAt( 0, 6 )[0]['count'] );
        $this->assertSame( array(), expDataTableRelation::pointingAt( 50, 0 ), 'by the object alone: the inferred column is not asked' );
        // the same relation declared: found by the object
        $definition = expDTFixtureRow::definition();
        $definition['relations']['contentobject_id'] = array( 'class' => 'eZContentObject', 'field' => 'id' );
        expDataTableCatalogue::setInstance( new expDataTableCatalogue( array( 'expDTFixtureRow' => expDataTableCatalogue::entryFromDefinition( 'expDTFixtureRow', $definition, '', 'tests' ) ) ) );
        $byObject = expDataTableRelation::pointingAt( 50, 0 );
        $this->assertCount( 1, $byObject );
        $this->assertSame( 'contentobject_id', $byObject[0]['column'] );
        $this->assertSame( 2, $byObject[0]['count'] );
        $this->assertFalse( $byObject[0]['more'] );
    }

    /** DR-04 */
    public function testKernelTablesAreSkipped()
    {
        $classes = array_column( expDataTableRelation::pointingAt( 0, 5 ), 'class' );
        $this->assertNotContains( 'expDTKernelTableRow', $classes );
        $this->assertNotContains( 'expDTKernelTableRowAlias', $classes );
        $this->assertSame( array( 'expDTFixtureRow' ), $classes );
    }

    /** DR-05 */
    public function testFetchFunctions()
    {
        $list = expDataTableFunctionCollection::fetchList( 'expDTFixtureRow', array( 'node_id' => 5, 'no_such_field' => 1 ), array( 'price', false ), 0, 3 );
        $this->assertSame( array( 'T11', 'T10', 'T9' ), array_map( function ( $o ) { return $o->attribute( 'title' ); }, $list['result'] ) );
        $this->assertSame( 12, expDataTableFunctionCollection::fetchCount( 'expDTFixtureRow' )['result'] );
        $this->assertSame( 11, expDataTableFunctionCollection::fetchCount( 'expDTFixtureRow', array( 'node_id' => 5 ) )['result'] );
        $this->assertSame( 12, expDataTableFunctionCollection::fetchCount( 'expDTFixtureRow', array( 'bogus' => 5 ) )['result'], 'unknown fields are no condition' );
        $this->assertCount( 12, expDataTableFunctionCollection::fetchList( 'expDTFixtureRow', false, false, 0, 100000 )['result'] );
        $this->assertCount( 1, expDataTableFunctionCollection::fetchList( 'expDTFixtureRow', false, false, -5, 0 )['result'], 'limit at least 1, offset at least 0' );
        $row = expDataTableFunctionCollection::fetchRow( 'expDTFixtureRow', array( 'remote_id' => 'r3' ) );
        $this->assertSame( 'T3', $row['result']->attribute( 'title' ) );
        $this->assertFalse( expDataTableFunctionCollection::fetchRow( 'expDTFixtureRow', array( 'remote_id' => 'r3', 'bogus' => 1 ) )['result'] );
        $this->assertFalse( expDataTableFunctionCollection::fetchRow( 'expDTFixtureRow', array( 'remote_id' => 'none' ) )['result'] );
    }

    /** DR-06 */
    public function testFetchFunctionsRefuse()
    {
        $this->assertSame( array(), expDataTableFunctionCollection::fetchList( 'NoSuchClass' )['result'] );
        $this->assertSame( array(), expDataTableFunctionCollection::fetchList( 'bad name;' )['result'] );
        $this->assertSame( 0, expDataTableFunctionCollection::fetchCount( 'NoSuchClass' )['result'] );
        $this->assertFalse( expDataTableFunctionCollection::fetchRow( 'expDTFixtureRow', array() )['result'] );
        $this->assertFalse( expDataTableFunctionCollection::fetchRow( 'expDTFixtureRow', 'r3' )['result'] );
        $this->assertFalse( expDataTableFunctionCollection::fetchCan( 'everything' )['result'] );
    }
}
