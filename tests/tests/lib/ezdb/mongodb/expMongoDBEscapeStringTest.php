<?php
/**
 * expMongoDB::escapeString() escapes a value for a string literal of the SQL the adapter parses, as the SQL engines'
 * drivers do, and the adapter reads the literal back as the value it was. Returned unchanged, a value with a quote
 * ended its literal early: the statement was refused, or an injection-style value became another condition.
 *
 *  ES-01 - a single quote is doubled and a backslash escaped
 *  ES-02 - names and texts with apostrophes (O'Brien, German and French) come back from a WHERE as they were
 *  ES-03 - an injection-style value stays one value of one condition
 *  ES-04 - an INSERT literal is stored as it was (castSqlLiteral)
 *  ES-05 - the SQL fallback (SQLite) reads the escaped literal as the same value
 *
 * No server needed.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mongodb
 */

require_once __DIR__ . '/stubs.php';

#[\PHPUnit\Framework\Attributes\Group('mongodb')]
class expMongoDBEscapeStringTest extends PHPUnit\Framework\TestCase
{
    public static function values()
    {
        return array(
            'irish name'        => array( "O'Brien" ),
            'german'            => array( "Grüß Gott, wie geht's? Heute ist's schön" ),
            'french'            => array( "L'été à l'hôtel d'Aix, aujourd'hui" ),
            'backslash'         => array( 'C:\\Users\\it\'s' ),
            'doubled quotes'    => array( "a''b" ),
            'injection'         => array( "x' OR '1'='1" ),
            'injection comment' => array( "admin'; DROP TABLE ezuser; --" ),
        );
    }

    private function where( $sql )
    {
        $method = new ReflectionMethod( 'expMongoDB', 'parseWhereClause' );
        if ( PHP_VERSION_ID < 80100 )
            $method->setAccessible( true );
        return $method->invoke( new expMongoDBTestable(), $sql, null );
    }

    public function testQuotesAreDoubledAndBackslashesEscaped()
    {
        $db = new expMongoDBTestable();
        $this->assertSame( "O''Brien", $db->escapeString( "O'Brien" ) );
        $this->assertSame( 'a\\\\b', $db->escapeString( 'a\\b' ) );
        $this->assertSame( '', $db->escapeString( null ) );
        $this->assertSame( '42', $db->escapeString( 42 ) );
    }

    /**
     * @dataProvider values
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('values')]
    public function testWhereReadsTheValueBack( $value )
    {
        $db = new expMongoDBTestable();
        $filter = $this->where( "login = '" . $db->escapeString( $value ) . "' AND contentobject_id = 14" );
        $this->assertIsArray( $filter );
        // one condition per column, also when the translation writes them as $and
        $this->assertSame( array( '$and' => array( array( 'login' => $value ), array( 'contentobject_id' => 14 ) ) ), $filter );
    }

    /**
     * @dataProvider values
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('values')]
    public function testInsertLiteralIsStoredAsItWas( $value )
    {
        $db = new expMongoDBTestable();
        $this->assertSame( $value, expMongoDB::castSqlLiteral( "'" . $db->escapeString( $value ) . "'" ) );
    }

    /**
     * @dataProvider values
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('values')]
    public function testSqlFallbackReadsTheSameValue( $value )
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'the sqlite3 extension is not loaded' );
        $db = new expMongoDBTestable();
        $sqlite = expMongoDB::openFallbackDatabase();
        $result = $sqlite->query( expMongoDB::fallbackDialect( "SELECT '" . $db->escapeString( $value ) . "' AS v" ) );
        $row = $result->fetchArray( SQLITE3_ASSOC );
        $this->assertSame( $value, $row['v'] );
    }
}
