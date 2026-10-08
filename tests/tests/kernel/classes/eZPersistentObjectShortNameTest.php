<?php
/**
 * eZPersistentObject on a database that uses short column names (Oracle, eZDBInterface::useShortNames()), without
 * the database: a field with a short name is written with it in every part of the queries of storeObject().
 *
 *  PS-01 - The UPDATE of an existing row names a key field with a short name by it in its WHERE condition
 *  PS-02 - The INSERT of a new row and the SELECT that looks for the row use the short name too
 *  PS-03 - On a database without short names nothing changes
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * A database handler that runs nothing: it records the queries and answers the lookup of storeObject() with one row
 * when the row is to exist.
 */
class eZPersistentObjectShortNameTestDB extends eZNullDB
{
    public $queries = array();
    public $rowExists = true;
    public $shortNames = true;

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

    function query( $sql, $server = false )
    {
        $this->queries[] = $sql;
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->queries[] = $sql;
        return $this->rowExists ? array( array( 'id' => 1 ) ) : array();
    }

    function escapeString( $str )
    {
        return addslashes( $str );
    }
}

/**
 * A persistent object whose second key has a short name, as eZURLObjectLink's contentobject_attribute_version has.
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

class eZPersistentObjectShortNameTest extends PHPUnit\Framework\TestCase
{
    private $previousDB;
    private $db;

    protected function setUp(): void
    {
        $this->previousDB = eZDB::instance();
        $this->db = new eZPersistentObjectShortNameTestDB();
        eZDB::setInstance( $this->db );
    }

    protected function tearDown(): void
    {
        eZDB::setInstance( $this->previousDB );
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
}
