<?php
/**
 * The conditions of eZPersistentObject::fetchObjectList() as MongoDB filters, without a database: an array condition
 * means on MongoDB what conditionText() writes for SQL. Before, the MongoDB branch passed it on as it was, a filter
 * that compares the field with an array and matches nothing (a full reindex of a class queued 0 objects).
 *
 *  PM-01 array( array( 1, 3 ) ) is $in, each value cast like a scalar of the field's datatype
 *  PM-02 array( false, array( 1, 9 ) ) is $gte/$lte
 *  PM-03 the operators >=, <=, <, >, =, <>, != and like
 *  PM-04 a filter written for MongoDB and an unknown operator are kept as they are; scalars are cast as before
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** Opens the two protected helpers of the MongoDB branch to the test. */
class eZPersistentObjectMongoConditionTestObject extends eZPersistentObject
{
    public static function condition( array $value, $dtype )
    {
        return self::mongoCondition( $value, $dtype );
    }

    public static function value( $value, $dtype )
    {
        return self::mongoValue( $value, $dtype );
    }
}

class eZPersistentObjectMongoConditionTest extends PHPUnit\Framework\TestCase
{
    /** PM-01 */
    public function testInList()
    {
        $this->assertSame( array( '$in' => array( 1, 3 ) ), eZPersistentObjectMongoConditionTestObject::condition( array( array( '1', '3' ) ), 'integer' ) );
        $this->assertSame( array( '$in' => array( '1', 'a' ) ), eZPersistentObjectMongoConditionTestObject::condition( array( array( '1', 'a' ) ), 'string' ) );
        $this->assertSame( array( '$in' => array() ), eZPersistentObjectMongoConditionTestObject::condition( array( array() ), 'integer' ) );
    }

    /** PM-02 */
    public function testRange()
    {
        $this->assertSame( array( '$gte' => 1, '$lte' => 9 ), eZPersistentObjectMongoConditionTestObject::condition( array( false, array( '1', '9' ) ), 'integer' ) );
    }

    /** PM-03 */
    public function testOperators()
    {
        $expected = array( '>=' => array( '$gte' => 5 ), '<=' => array( '$lte' => 5 ), '<' => array( '$lt' => 5 ), '>' => array( '$gt' => 5 ),
                           '=' => 5, '<>' => array( '$ne' => 5 ), '!=' => array( '$ne' => 5 ) );
        foreach ( $expected as $operator => $filter )
            $this->assertSame( $filter, eZPersistentObjectMongoConditionTestObject::condition( array( $operator, '5' ), 'integer' ), $operator );
        $like = eZPersistentObjectMongoConditionTestObject::condition( array( 'like', 'Below %_x.' ), 'string' );
        $this->assertSame( array( '$regex' => '^Below .*.x\\.$', '$options' => 'i' ), $like );
        $this->assertSame( 1, preg_match( '/' . $like['$regex'] . '/i', 'below the root: x.' ) );
    }

    /** PM-04 */
    public function testKeptAndScalars()
    {
        $this->assertSame( array( '$in' => array( 2 ) ), eZPersistentObjectMongoConditionTestObject::condition( array( '$in' => array( 2 ) ), 'integer' ) );
        $this->assertSame( array( 'between', 3 ), eZPersistentObjectMongoConditionTestObject::condition( array( 'between', 3 ), 'integer' ) );
        $this->assertSame( 7, eZPersistentObjectMongoConditionTestObject::value( '7', 'int' ) );
        $this->assertSame( 1.5, eZPersistentObjectMongoConditionTestObject::value( '1.5', 'float' ) );
        $this->assertSame( '7', eZPersistentObjectMongoConditionTestObject::value( '7', 'string' ) );
        $this->assertNull( eZPersistentObjectMongoConditionTestObject::value( null, 'integer' ) );
    }
}
