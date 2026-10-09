<?php
/**
 * eZPersistentObject on a database that uses short column names (Oracle, eZDBInterface::useShortNames()), without
 * the database: a field with a short name is written with it in every query eZPersistentObject builds, and on a
 * database without short names every query stays exactly as it was.
 *
 *  PS-01 - The UPDATE of an existing row names a key field with a short name by it in its WHERE condition
 *  PS-02 - The INSERT of a new row and the SELECT that looks for the row use the short name too
 *  PS-03 - On a database without short names nothing changes
 *  PS-04 - A bound (long) text field with a short name is written as its bind variable in the UPDATE
 *  PS-05 - removeObject() names the condition by the short name
 *  PS-06 - fetchObjectList() uses the short name in the field list, the condition, ORDER BY and GROUP BY, and a
 *          row comes back under the long name, also when the column is NULL
 *  PS-07 - updateObjectList() uses the short name in SET and WHERE
 *  PS-08 - newObjectOrder() uses the short name for the order column and the condition
 *  PS-09 - reorderObject() swaps two rows, and moves a single row, by the short names of the keys and the order column
 *  PS-10 - A definition whose fields are plain strings (old style) gives no error on a database with short names
 *  PS-11 - Without short names every query of PS-04 to PS-09 is exactly the query of the previous release
 *  PS-12 - The ezenum to ezselection conversion removes the enum values through the definition (short name)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * A database handler that runs nothing: it records the queries and answers each SELECT from a list of answers, the
 * first one whose pattern matches; storeObject()'s lookup gets one row when the row is to exist.
 */
class eZPersistentObjectShortNameTestDB extends eZNullDB
{
    public $queries = array();
    public $rowExists = true;
    public $shortNames = true;
    public $binding = false;
    /** @var array pattern => rows */
    public $answers = array();

    public function __construct()
    {
    }

    function databaseName()
    {
        return $this->shortNames ? 'oracle' : 'mysql';
    }

    function useShortNames()
    {
        return $this->shortNames;
    }

    function bindingType()
    {
        return $this->binding ? eZDBInterface::BINDING_NAME : eZDBInterface::BINDING_NO;
    }

    function bindVariable( $value, $fieldDef = false )
    {
        return ':' . $fieldDef['name'];
    }

    function query( $sql, $server = false )
    {
        $this->queries[] = $sql;
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->queries[] = $sql;
        foreach ( $this->answers as $pattern => $rows )
        {
            if ( preg_match( $pattern, $sql ) )
            {
                return $rows;
            }
        }
        return $this->rowExists ? array( array( 'id' => 1 ) ) : array();
    }

    function escapeString( $str )
    {
        return addslashes( (string)$str );
    }

    function begin()
    {
        return true;
    }

    function commit()
    {
        return true;
    }
}

/**
 * A persistent object whose second key has a short name, as eZEnumObjectValue's contentobject_attribute_version has.
 */
class eZPersistentObjectShortNameTestObject extends eZPersistentObject
{
    public static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'contentobject_attribute_version' => array( 'name' => 'Version', 'datatype' => 'integer',
                                                                                     'default' => 0, 'required' => true,
                                                                                     'short_name' => 'contentobject_attr_version' ),
                                         'title' => array( 'name' => 'Title', 'datatype' => 'string', 'default' => '', 'required' => true ) ),
                      'keys' => array( 'id', 'contentobject_attribute_version' ),
                      'class_name' => 'eZPersistentObjectShortNameTestObject',
                      'name' => 'x1_short_name_test' );
    }
}

/**
 * A persistent object with a short-named text field (bound when long) and a short-named order column.
 */
class eZPersistentObjectShortNameTestPlacedObject extends eZPersistentObject
{
    public static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'contentobject_attribute_version' => array( 'name' => 'Version', 'datatype' => 'integer',
                                                                                     'default' => 0, 'required' => true,
                                                                                     'short_name' => 'contentobject_attr_version' ),
                                         'parent_id' => array( 'name' => 'ParentID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'placement_in_parent_list' => array( 'name' => 'Placement', 'datatype' => 'integer',
                                                                              'default' => 0, 'required' => true,
                                                                              'short_name' => 'placement_in_parent' ),
                                         'long_description_text' => array( 'name' => 'LongDescription', 'datatype' => 'text',
                                                                           'default' => '', 'required' => true,
                                                                           'short_name' => 'long_desc_text' ) ),
                      'keys' => array( 'id', 'contentobject_attribute_version' ),
                      'sort' => array( 'placement_in_parent_list' => 'asc' ),
                      'class_name' => 'eZPersistentObjectShortNameTestPlacedObject',
                      'name' => 'x1_short_name_placed' );
    }
}

/**
 * An old-style definition: the fields are plain strings.
 */
class eZPersistentObjectShortNameTestPlainObject extends eZPersistentObject
{
    public static function definition()
    {
        return array( 'fields' => array( 'id' => 'ID', 'name' => 'Name' ),
                      'keys' => array( 'id' ),
                      'sort' => array( 'name' => 'asc' ),
                      'class_name' => 'eZPersistentObjectShortNameTestPlainObject',
                      'name' => 'x1_short_name_plain' );
    }
}

class eZPersistentObjectShortNameTest extends PHPUnit\Framework\TestCase
{
    private $hadDB;
    private $previousDB;
    /** @var eZPersistentObjectShortNameTestDB */
    private $db;

    protected function setUp(): void
    {
        // the instance as it is, without creating one (eZDB::instance() would connect)
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->previousDB = $this->hadDB ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->db = new eZPersistentObjectShortNameTestDB();
        eZDB::setInstance( $this->db );
    }

    protected function tearDown(): void
    {
        if ( $this->hadDB )
            eZDB::setInstance( $this->previousDB );
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
    }

    private function store( $hasDirtyData = true )
    {
        $object = new eZPersistentObjectShortNameTestObject( array( 'id' => 7, 'contentobject_attribute_version' => 3, 'title' => 'x1' ) );
        $object->setHasDirtyData( $hasDirtyData );
        $object->store();
        return $this->db->queries;
    }

    private function queryStartingWith( array $queries, $verb )
    {
        foreach ( $queries as $sql )
        {
            if ( stripos( ltrim( $sql ), $verb ) === 0 )
            {
                return $sql;
            }
        }
        $this->fail( "no $verb query among: " . implode( ' | ', $queries ) );
    }

    private static function oneLine( $sql )
    {
        return trim( preg_replace( '/\s+/', ' ', $sql ) );
    }

    /**
     * Runs the scenarios of PS-04 to PS-09 and returns every query, one line each.
     */
    private function runScenarios()
    {
        $def = eZPersistentObjectShortNameTestPlacedObject::definition();
        $long = $this->db->shortNames ? 'placement_in_parent' : 'placement_in_parent_list';
        $version = $this->db->shortNames ? 'contentobject_attr_version' : 'contentobject_attribute_version';
        $this->db->binding = true;
        $this->db->rowExists = true;

        // PS-04
        $object = new eZPersistentObjectShortNameTestPlacedObject( array( 'id' => 7, 'contentobject_attribute_version' => 3, 'parent_id' => 2,
                                                                          'placement_in_parent_list' => 4,
                                                                          'long_description_text' => str_repeat( 'x', 2100 ) ) );
        $object->store();
        // PS-05
        eZPersistentObject::removeObject( $def, array( 'id' => 7, 'contentobject_attribute_version' => 3 ) );
        // PS-06
        $this->db->answers = array( '/^SELECT .*GROUP BY/s' => array( array( 'id' => 7, $version => null, 'parent_id' => 2 ) ) );
        eZPersistentObject::fetchObjectList( $def, array( 'id', 'contentobject_attribute_version', 'parent_id' ),
                                             array( 'contentobject_attribute_version' => array( '>', 1 ), 'parent_id' => 2 ),
                                             array( 'contentobject_attribute_version' => 'desc', 'id' => 'asc' ), null, false,
                                             array( 'id', 'contentobject_attribute_version', 'parent_id' ) );
        // PS-07
        eZPersistentObject::updateObjectList( array( 'definition' => $def,
                                                     'update_fields' => array( 'placement_in_parent_list' => 5, 'parent_id' => 3 ),
                                                     'conditions' => array( 'contentobject_attribute_version' => 3, 'id' => array( 7, 8 ) ) ) );
        // PS-08
        $this->db->answers = array( '/^SELECT MAX/' => array( array( $long => 9 ) ) );
        $next = eZPersistentObject::newObjectOrder( $def, 'placement_in_parent_list', array( 'contentobject_attribute_version' => 3 ) );
        // PS-09: two rows to swap, then a single row moved
        $this->db->answers = array( '/^SELECT /' => array( array( 'id' => 7, $version => 3, $long => 4 ),
                                                           array( 'id' => 8, $version => 3, $long => 5 ) ) );
        eZPersistentObject::reorderObject( $def, array( 'placement_in_parent_list' => 4 ), array( 'parent_id' => 2 ), true );
        $this->db->answers = array( '/^SELECT /' => array( array( 'id' => 8, $version => 3, $long => 5 ) ) );
        eZPersistentObject::reorderObject( $def, array( 'placement_in_parent_list' => 5 ), array( 'parent_id' => 2 ), true );

        return array( array_map( array( __CLASS__, 'oneLine' ), $this->db->queries ), $next );
    }

    /** PS-01 */
    public function testTheUpdateConditionUsesTheShortName()
    {
        $update = $this->queryStartingWith( $this->store(), 'UPDATE' );
        $this->assertMatchesRegularExpression( '/\bWHERE\b.*\bcontentobject_attr_version\s*=\s*\'?3\'?/s', $update );
        $this->assertStringNotContainsString( 'contentobject_attribute_version', $update );
        $this->assertMatchesRegularExpression( '/\bid\s*=\s*\'?7\'?/', $update );
    }

    /** PS-02 */
    public function testTheInsertAndTheLookupUseTheShortName()
    {
        $queries = $this->store();
        $this->assertStringContainsString( 'contentobject_attr_version', $this->queryStartingWith( $queries, 'SELECT' ) );

        $this->db->queries = array();
        $this->db->rowExists = false;
        $insert = $this->queryStartingWith( $this->store(), 'INSERT' );
        $this->assertStringContainsString( 'contentobject_attr_version', $insert );
        $this->assertStringNotContainsString( 'contentobject_attribute_version', $insert );
    }

    /** PS-03 */
    public function testWithoutShortNamesTheLongNameStays()
    {
        $this->db->shortNames = false;
        $update = $this->queryStartingWith( $this->store(), 'UPDATE' );
        $this->assertMatchesRegularExpression( '/\bWHERE\b.*\bcontentobject_attribute_version\s*=\s*\'?3\'?/s', $update );
        $this->assertStringNotContainsString( 'contentobject_attr_version', $update );
    }

    /** PS-04 to PS-09 */
    public function testEveryQueryUsesTheShortNames()
    {
        list( $queries, $next ) = $this->runScenarios();
        $expected = array(
            // PS-04: lookup, then the UPDATE with the bind variable of the long text
            "SELECT id, contentobject_attr_version FROM x1_short_name_placed WHERE id='7' AND contentobject_attr_version='3'",
            "UPDATE x1_short_name_placed SET parent_id=2, placement_in_parent=4, long_desc_text=:LongDescription WHERE id='7' AND contentobject_attr_version='3'",
            // PS-05
            "DELETE FROM x1_short_name_placed WHERE id='7' AND contentobject_attr_version='3'",
            // PS-06
            "SELECT id, contentobject_attr_version, parent_id FROM x1_short_name_placed WHERE contentobject_attr_version > '1' AND parent_id='2' GROUP BY id, contentobject_attr_version, parent_id ORDER BY contentobject_attr_version DESC, id ASC",
            // PS-07
            "UPDATE x1_short_name_placed SET placement_in_parent='5', parent_id='3' WHERE contentobject_attr_version='3' AND id IN ('7', '8')",
            // PS-08
            "SELECT MAX(placement_in_parent) AS placement_in_parent FROM x1_short_name_placed WHERE contentobject_attr_version='3'",
            // PS-09: two rows swapped
            "SELECT id, contentobject_attr_version, placement_in_parent FROM x1_short_name_placed WHERE parent_id='2' AND placement_in_parent >= '4' ORDER BY placement_in_parent ASC",
            "UPDATE x1_short_name_placed SET placement_in_parent='5' WHERE id='7' AND contentobject_attr_version='3'",
            "UPDATE x1_short_name_placed SET placement_in_parent='4' WHERE id='8' AND contentobject_attr_version='3'",
            // PS-09: a single row moved
            "SELECT id, contentobject_attr_version, placement_in_parent FROM x1_short_name_placed WHERE parent_id='2' AND placement_in_parent >= '5' ORDER BY placement_in_parent ASC",
            "SELECT id, contentobject_attr_version, placement_in_parent FROM x1_short_name_placed WHERE parent_id='2' ORDER BY placement_in_parent ASC",
            "UPDATE x1_short_name_placed SET placement_in_parent='4' WHERE id='8' AND contentobject_attr_version='3'",
        );
        $this->assertSame( $expected, $queries );
        $this->assertSame( 10, $next );
        foreach ( $queries as $sql )
        {
            $this->assertStringNotContainsString( 'contentobject_attribute_version', $sql );
            $this->assertStringNotContainsString( 'placement_in_parent_list', $sql );
            $this->assertStringNotContainsString( 'long_description_text', $sql );
        }
    }

    /** PS-06 */
    public function testARowComesBackUnderTheLongNamesAlsoWhenNull()
    {
        $this->db->answers = array( '/^SELECT /' => array( array( 'id' => 7, 'contentobject_attr_version' => null, 'title' => 'x1' ) ) );
        $rows = eZPersistentObject::fetchObjectList( eZPersistentObjectShortNameTestObject::definition(), null, null, null, null, false );
        $this->assertSame( array( array( 'id' => 7, 'title' => 'x1', 'contentobject_attribute_version' => null ) ), $rows );

        // a NULL short column does not overwrite a value the row already has under the long name
        $this->db->answers = array( '/^SELECT /' => array( array( 'id' => 7, 'contentobject_attribute_version' => 4, 'contentobject_attr_version' => null ) ) );
        $rows = eZPersistentObject::fetchObjectList( eZPersistentObjectShortNameTestObject::definition(), null, null, null, null, false );
        $this->assertSame( 4, $rows[0]['contentobject_attribute_version'] );

        $this->db->answers = array( '/^SELECT /' => array( array( 'id' => 7, 'contentobject_attr_version' => 3, 'title' => 'x1' ) ) );
        $objects = eZPersistentObject::fetchObjectList( eZPersistentObjectShortNameTestObject::definition() );
        $this->assertSame( 3, $objects[0]->attribute( 'contentobject_attribute_version' ) );
    }

    /** PS-10 */
    public function testAPlainStringDefinitionGivesNoError()
    {
        $def = eZPersistentObjectShortNameTestPlainObject::definition();
        $this->db->answers = array( '/^SELECT /' => array( array( 'id' => 1, 'name' => 'a' ) ) );
        $rows = eZPersistentObject::fetchObjectList( $def, array( 'id', 'name' ), array( 'name' => 'a' ), null, null, false, array( 'name' ) );
        $this->assertSame( array( array( 'id' => 1, 'name' => 'a' ) ), $rows );
        eZPersistentObject::updateObjectList( array( 'definition' => $def, 'update_fields' => array( 'name' => 'b' ), 'conditions' => array( 'id' => 1 ) ) );
        $this->assertSame( 'name', eZPersistentObject::getShortAttributeName( $this->db, $def, 'name' ) );
        $this->assertSame( "UPDATE x1_short_name_plain SET name='b' WHERE id='1'", end( $this->db->queries ) );
    }

    /** PS-11 */
    public function testWithoutShortNamesEveryQueryIsAsBefore()
    {
        $this->db->shortNames = false;
        list( $queries, $next ) = $this->runScenarios();
        // the queries of the previous release (origin/main before this change), byte for byte after joining lines
        $expected = array(
            "SELECT id, contentobject_attribute_version FROM x1_short_name_placed WHERE id='7' AND contentobject_attribute_version='3'",
            "UPDATE x1_short_name_placed SET parent_id=2, placement_in_parent_list=4, long_description_text=:LongDescription WHERE id='7' AND contentobject_attribute_version='3'",
            "DELETE FROM x1_short_name_placed WHERE id='7' AND contentobject_attribute_version='3'",
            "SELECT id, contentobject_attribute_version, parent_id FROM x1_short_name_placed WHERE contentobject_attribute_version > '1' AND parent_id='2' GROUP BY id, contentobject_attribute_version, parent_id ORDER BY contentobject_attribute_version DESC, id ASC",
            "UPDATE x1_short_name_placed SET placement_in_parent_list='5', parent_id='3' WHERE contentobject_attribute_version='3' AND id IN ('7', '8')",
            "SELECT MAX(placement_in_parent_list) AS placement_in_parent_list FROM x1_short_name_placed WHERE contentobject_attribute_version='3'",
            "SELECT id, contentobject_attribute_version, placement_in_parent_list FROM x1_short_name_placed WHERE parent_id='2' AND placement_in_parent_list >= '4' ORDER BY placement_in_parent_list ASC",
            "UPDATE x1_short_name_placed SET placement_in_parent_list='5' WHERE id='7' AND contentobject_attribute_version='3'",
            "UPDATE x1_short_name_placed SET placement_in_parent_list='4' WHERE id='8' AND contentobject_attribute_version='3'",
            "SELECT id, contentobject_attribute_version, placement_in_parent_list FROM x1_short_name_placed WHERE parent_id='2' AND placement_in_parent_list >= '5' ORDER BY placement_in_parent_list ASC",
            "SELECT id, contentobject_attribute_version, placement_in_parent_list FROM x1_short_name_placed WHERE parent_id='2' ORDER BY placement_in_parent_list ASC",
            "UPDATE x1_short_name_placed SET placement_in_parent_list='4' WHERE id='8' AND contentobject_attribute_version='3'",
        );
        $this->assertSame( $expected, $queries );
        $this->assertSame( 10, $next );
    }

    /** PS-12 */
    public function testTheEnumConversionRemovesTheValuesThroughTheDefinition()
    {
        $source = file_get_contents( __DIR__ . '/../../../../kernel/private/classes/commands/convertezenumtoezselection.php' );
        $this->assertDoesNotMatchRegularExpression( '/DELETE FROM ezenumobjectvalue/i', $source );
        $this->assertStringContainsString( 'eZPersistentObject::removeObject( \eZEnumObjectValue::definition()', $source );
    }
}
