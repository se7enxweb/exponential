<?php
/**
 * INSERT ... SELECT on MongoDB (expMongoDB::insertSelectPlan()). eZContentObjectState::store() gives a new state to
 * every object with "INSERT INTO ezcobj_state_link (contentobject_id, contentobject_state_id) SELECT id, 3 FROM
 * ezcontentobject", which the driver logged as unhandled: on MongoDB no object got the state of a new state group
 * (the 6.0.15-review-hold upgrade step). The plan says what each column copies; the query is proved against a
 * MongoDB database in the database job.
 *
 * Database: none.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expMongoDBInsertSelectPlanTest extends PHPUnit\Framework\TestCase
{
    public function testColumnsAndLiterals()
    {
        $plan = expMongoDB::insertSelectPlan( array( 'contentobject_id', 'contentobject_state_id' ), expMongoDB::splitSqlList( 'id, 3' ) );
        $this->assertSame( array( 'contentobject_id' => array( 'field' => 'id', 'literal' => null ),
                                  'contentobject_state_id' => array( 'field' => null, 'literal' => '3' ) ), $plan );

        $plan = expMongoDB::insertSelectPlan( array( 'a', 'b', 'c' ), expMongoDB::splitSqlList( "ezcontentobject.id AS oid, 'x, y', -2.5" ) );
        $this->assertSame( array( 'field' => 'id', 'literal' => null ), $plan['a'] );
        $this->assertSame( array( 'field' => null, 'literal' => "'x, y'" ), $plan['b'] );
        $this->assertSame( array( 'field' => null, 'literal' => '-2.5' ), $plan['c'] );
    }

    public function testWhatItCannotCopyIsRefused()
    {
        $this->assertFalse( expMongoDB::insertSelectPlan( array( 'a', 'b' ), expMongoDB::splitSqlList( 'id' ) ), 'count differs' );
        $this->assertFalse( expMongoDB::insertSelectPlan( array( 'a' ), expMongoDB::splitSqlList( 'max( id )' ) ), 'an expression' );
        $this->assertFalse( expMongoDB::insertSelectPlan( array( 'a' ), expMongoDB::splitSqlList( 'id + 1' ) ) );
        $this->assertFalse( expMongoDB::insertSelectPlan( array( 'a' ), expMongoDB::splitSqlList( 'NULL' ) ) );
        $this->assertFalse( expMongoDB::insertSelectPlan( array( 'a' ), expMongoDB::splitSqlList( '*' ) ) );
        $this->assertFalse( expMongoDB::insertSelectPlan( array(), array() ) );
    }
}
