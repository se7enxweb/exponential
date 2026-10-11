<?php
/**
 * Two text comparisons the SQL engines all understand, which the MongoDB driver's WHERE translation refused (the
 * statement then returned no rows):
 *
 *  - LIKE ... ESCAPE '!': the content finder's file prefixes ("filepath LIKE 'var/a!_b/%' ESCAPE '!'") found
 *    nothing on MongoDB. The character after the escape character is literal; a trailing one is refused.
 *  - LOWER( column ) = '...' (and UPPER): eZUser::fetchByName() and fetchByEmail() compare so, and found no user
 *    at all on MongoDB. It becomes $expr with $toLower / $toUpper, compared as text.
 *
 * No server needed: the filters are checked as the driver builds them.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mongodb
 */

require_once __DIR__ . '/stubs.php';

class expMongoDBWhereTextTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'MongoDB\\BSON\\Regex' ) )
            $this->markTestSkipped( 'the mongodb extension is not loaded' );
    }

    private function where( $sql, $table = null )
    {
        $method = new ReflectionMethod( 'expMongoDB', 'parseWhereClause' );
        if ( PHP_VERSION_ID < 80100 )
            $method->setAccessible( true );
        return $method->invoke( new expMongoDBTestable(), $sql, $table );
    }

    /** @return string the pattern of a LIKE filter on the column */
    private function pattern( $filter, $column )
    {
        $this->assertIsArray( $filter );
        $this->assertInstanceOf( 'MongoDB\\BSON\\Regex', $filter[$column] );
        return $filter[$column]->getPattern();
    }

    public function testLikeWithEscape()
    {
        $pattern = $this->pattern( $this->where( "filepath LIKE 'var/a!_b!%c!!d/%' ESCAPE '!'" ), 'filepath' );
        $this->assertSame( 1, preg_match( '#' . $pattern . '#', 'var/a_b%c!d/x.jpg' ) );
        $this->assertSame( 0, preg_match( '#' . $pattern . '#', 'var/aXb%c!d/x.jpg' ) );
        $this->assertSame( 0, preg_match( '#' . $pattern . '#', 'var/a_bXc!d/x.jpg' ) );
        // with other conditions after it
        $filter = $this->where( "mime_type LIKE 'image/%' ESCAPE '!' AND version = 2" );
        $this->assertIsArray( $filter );
        $this->assertStringContainsString( 'image', json_encode( $filter ) );
        // without ESCAPE as before; an escape that is not one character, or ends the pattern, is refused
        $this->assertSame( '^a.*$', $this->pattern( $this->where( "x LIKE 'a%'" ), 'x' ) );
        $this->assertFalse( $this->where( "x LIKE 'a%' ESCAPE '!!'" ) );
        $this->assertFalse( $this->where( "x LIKE 'a!' ESCAPE '!'" ) );
    }

    public function testLowerAndUpperOfAColumn()
    {
        $this->assertSame( array( '$expr' => array( '$eq' => array( array( '$toLower' => '$login' ), 'admin' ) ) ),
                           $this->where( "LOWER( login ) = 'admin'" ) );
        $this->assertSame( array( '$expr' => array( '$eq' => array( array( '$toUpper' => '$email' ), 'A@EXAMPLE.INVALID' ) ) ),
                           $this->where( "upper(email) = 'A@EXAMPLE.INVALID'" ) );
        // compared as text, not as a number
        $this->assertSame( array( '$expr' => array( '$eq' => array( array( '$toLower' => '$login' ), '42' ) ) ),
                           $this->where( "LOWER( login ) = '42'" ) );
        // another function stays refused
        $this->assertFalse( $this->where( "LENGTH( login ) = 5" ) );
        // LOWER( column ) LIKE: the column's regex without case (the content finder's part of an alias)
        $filter = $this->where( "LOWER( text ) LIKE '%test_pro!%duct%' ESCAPE '!' AND is_original = 1" );
        $this->assertIsArray( $filter );
        $regex = null;
        array_walk_recursive( $filter, function ( $value ) use ( &$regex ) { if ( $value instanceof MongoDB\BSON\Regex ) $regex = $value; } );
        $this->assertInstanceOf( 'MongoDB\\BSON\\Regex', $regex );
        $this->assertSame( 'i', $regex->getFlags() );
        $this->assertSame( 1, preg_match( '#' . $regex->getPattern() . '#i', 'my-Test-Pro%duct-x' ) );
        $this->assertSame( 0, preg_match( '#' . $regex->getPattern() . '#i', 'my-test-product' ) );
    }
}
