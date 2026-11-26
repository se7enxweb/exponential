<?php
/**
 * Property and fuzz tests of the SQL Server SQL builder (no server needed): random statements made of random
 * literals, names, comments and keywords, each seed one case.
 *
 *  PR-01 - escapeString() then parameterize() gives back every value (without NUL), whatever bytes it holds
 *  PR-02 - unicodeLiterals() keeps every value: its literal expression, evaluated, is the original value, and no
 *          line continuation is left inside a literal
 *  PR-03 - unicodeLiterals() changes nothing outside literals (comments, names, keywords stay byte for byte)
 *  PR-04 - applyLimit() of a random SELECT has exactly one outer OFFSET and one outer ORDER BY, starts with the
 *          statement unchanged, and keeps its literal values
 *  PR-05 - quoteIdentifier() then the lexer gives back the name, for random names with ] [ " ' and spaces
 *  PR-06 - inList(): every value appears once, in chunks no larger than the chunk size
 *  PR-07 - isReplicaReadable() never says yes to a statement with a write outside literals and comments
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerBuilderPropertyTest extends PHPUnit\Framework\TestCase
{
    const PIECES = array( "'", "''", 'a', 'Z', ' ', "\n", "\r\n", '\\', "\\\n", '?', '--', '/*', '*/', '[', ']', '"', '`', 'N', "N'",
                          'ä', '😀', '漢', "\xff", "\0", ';', ',', '(', ')', '%', '_', 'ORDER BY', 'SELECT', 'GROUP BY', 'x' );

    public static function seeds()
    {
        $list = array();
        for ( $i = 1; $i <= 30; $i++ )
            $list["seed $i"] = array( $i * 7919 );
        return $list;
    }

    private static function randomValue( $maxPieces = 12 )
    {
        $value = '';
        $n = mt_rand( 0, $maxPieces );
        for ( $i = 0; $i < $n; $i++ )
            $value .= self::PIECES[mt_rand( 0, count( self::PIECES ) - 1 )];
        return $value;
    }

    private static function randomStatement( &$values )
    {
        $values = array();
        $parts = array( 'SELECT' );
        $columns = mt_rand( 1, 4 );
        for ( $i = 0; $i < $columns; $i++ )
        {
            $value = self::randomValue();
            $values[] = str_replace( "\0", '', $value );
            $parts[] = ( $i ? ', ' : '' ) . "'" . expSQLServerDialect::escapeString( $value ) . "' AS " . expSQLServerDialect::quoteIdentifier( 'c' . $i . self::randomName() );
        }
        $parts[] = 'FROM t';
        if ( mt_rand( 0, 1 ) )
            $parts[] = '/* ' . str_replace( array( '*/', '/*' ), '', self::randomValue() ) . ' */';
        if ( mt_rand( 0, 1 ) )
        {
            $value = self::randomValue();
            $values[] = str_replace( "\0", '', $value );
            $parts[] = "WHERE name = '" . expSQLServerDialect::escapeString( $value ) . "'";
        }
        if ( mt_rand( 0, 1 ) )
            $parts[] = 'ORDER BY ' . ( mt_rand( 0, 1 ) ? 'LOWER(name)' : 'id DESC' );
        return implode( ' ', $parts );
    }

    private static function randomName()
    {
        $chars = array( 'a', 'b', ' ', ']', '[', '"', "'", 'ä', '-', '1' );
        $name = '';
        $n = mt_rand( 0, 8 );
        for ( $i = 0; $i < $n; $i++ )
            $name .= $chars[mt_rand( 0, count( $chars ) - 1 )];
        return $name;
    }

    private static function literalValues( $sql )
    {
        $values = array();
        foreach ( expSQLServerLexer::tokenize( $sql ) as $token )
            if ( $token['type'] === expSQLServerLexer::STRING || $token['type'] === expSQLServerLexer::NSTRING )
                $values[] = expSQLServerLexer::literalValue( $token );
        return $values;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('seeds')]
    public function testParameterizeGivesBackEveryValue( $seed )
    {
        mt_srand( $seed );
        $sql = self::randomStatement( $values );
        $result = expSQLServerDialect::parameterize( $sql );
        $this->assertNotNull( $result, $sql );
        $this->assertSame( $values, $result[1] );
        $params = 0;
        foreach ( expSQLServerLexer::tokenize( $result[0] ) as $token )
            if ( $token['type'] === expSQLServerLexer::PARAM )
                $params++;
        $this->assertSame( count( $values ), $params );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('seeds')]
    public function testUnicodeLiteralsKeepEveryValue( $seed )
    {
        mt_srand( $seed );
        for ( $i = 0; $i < 20; $i++ )
        {
            $value = self::randomValue();
            $sql = expSQLServerDialect::unicodeLiterals( "SELECT '" . expSQLServerDialect::escapeString( $value ) . "'" );
            // evaluate the literal expression: N'...' pieces, NCHAR(n), +, parentheses
            $read = '';
            $tokens = expSQLServerLexer::tokenize( $sql );
            foreach ( $tokens as $k => $token )
            {
                if ( $token['type'] === expSQLServerLexer::NSTRING )
                {
                    $this->assertFalse( $token['open'], $sql );
                    $read .= expSQLServerLexer::literalValue( $token );
                }
                else if ( $token['type'] === expSQLServerLexer::NUMBER )
                    $read .= chr( (int)$token['text'] );
                else if ( $token['type'] === expSQLServerLexer::STRING )
                    $this->fail( "A literal without N: $sql" );
            }
            $this->assertSame( str_replace( "\0", '', $value ), $read, $sql );
            // and no line continuation is left inside a literal
            foreach ( $tokens as $token )
                if ( $token['type'] === expSQLServerLexer::NSTRING )
                    $this->assertDoesNotMatchRegularExpression( '/\\\\(\r|\n)/', $token['text'] );
        }
    }
    #[\PHPUnit\Framework\Attributes\DataProvider('seeds')]
    public function testUnicodeLiteralsChangeNothingOutsideLiterals( $seed )
    {
        mt_srand( $seed );
        $sql = self::randomStatement( $values );
        $strip = function ( $sql )
        {
            $out = '';
            foreach ( expSQLServerLexer::tokenize( $sql ) as $token )
                $out .= ( $token['type'] === expSQLServerLexer::STRING || $token['type'] === expSQLServerLexer::NSTRING ) ? '<lit>' : $token['text'];
            return preg_replace( '/\(<lit>(?: \+ (?:NCHAR\(1[03]\)|<lit>))*\)/', '<lit>', $out );
        };
        $this->assertSame( $strip( $sql ), $strip( expSQLServerDialect::unicodeLiterals( $sql ) ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('seeds')]
    public function testApplyLimitHasOneOuterOffsetAndKeepsTheStatement( $seed )
    {
        mt_srand( $seed );
        $sql = self::randomStatement( $values );
        $paged = expSQLServerDialect::applyLimit( $sql, mt_rand( 1, 50 ), mt_rand( 0, 1000 ) );
        $tokens = expSQLServerLexer::tokenize( $paged );
        $offsets = 0;
        $orders = 0;
        $significant = expSQLServerLexer::significant( $tokens );
        foreach ( $significant as $n => $entry )
        {
            if ( $entry[1] !== 0 )
                continue;
            $word = expSQLServerLexer::keyword( $tokens[$entry[0]] );
            if ( $word === 'OFFSET' )
                $offsets++;
            if ( $word === 'ORDER' && isset( $significant[$n + 1] ) && expSQLServerLexer::keyword( $tokens[$significant[$n + 1][0]] ) === 'BY' )
                $orders++;
        }
        $this->assertSame( 1, $offsets, $paged );
        $this->assertSame( 1, $orders, $paged );
        $this->assertStringStartsWith( rtrim( $sql ), $paged );
        $this->assertSame( $values, expSQLServerDialect::parameterize( $paged )[1] );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('seeds')]
    public function testQuotedNamesComeBack( $seed )
    {
        mt_srand( $seed );
        for ( $i = 0; $i < 20; $i++ )
        {
            $name = 'n' . self::randomName();
            $tokens = expSQLServerLexer::tokenize( expSQLServerDialect::quoteIdentifier( $name ) );
            $this->assertCount( 1, $tokens );
            $this->assertSame( $name, expSQLServerLexer::identifierValue( $tokens[0] ) );
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('seeds')]
    public function testInListHasEveryValueOnce( $seed )
    {
        mt_srand( $seed );
        $values = array();
        $count = mt_rand( 0, 3000 );
        for ( $i = 0; $i < $count; $i++ )
            $values[] = mt_rand( 1, 1000000 );
        $values = array_values( array_unique( $values ) );
        $chunk = mt_rand( 1, 1200 );
        $sql = expSQLServerDialect::inList( $values, 'id', false, 'int', $chunk );
        preg_match_all( '/IN \( ([^)]*) \)/', $sql, $m );
        $seen = array();
        foreach ( $m[1] as $list )
        {
            $items = explode( ', ', $list );
            $this->assertLessThanOrEqual( $chunk, count( $items ) );
            foreach ( $items as $item )
                $seen[] = (int)$item;
        }
        $this->assertSame( $values, $seen );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('seeds')]
    public function testReplicaNeverGetsAWrite( $seed )
    {
        mt_srand( $seed );
        $writes = array( 'INSERT INTO t VALUES (1)', 'UPDATE t SET a = 1', 'DELETE FROM t', 'SELECT * INTO u FROM t', 'MERGE t USING s ON 1=1 WHEN MATCHED THEN DELETE;',
                         'EXEC p', 'SELECT * FROM t WITH (UPDLOCK)', 'SELECT 1; DELETE FROM t', 'SELECT NEXT VALUE FOR s', 'SELECT * FROM #tmp' );
        $sql = $writes[mt_rand( 0, count( $writes ) - 1 )];
        // random harmless noise: comments and literals before and after
        $noise = '/* ' . str_replace( array( '*/', '/*' ), '', self::randomValue( 4 ) ) . " */ ";
        $this->assertFalse( expSQLServerDialect::isReplicaReadable( $noise . $sql ), $noise . $sql );
        $safe = "SELECT '" . expSQLServerDialect::escapeString( 'INSERT ' . self::randomValue( 4 ) . ' UPDATE' ) . "' AS x FROM t";
        $this->assertTrue( expSQLServerDialect::isReplicaReadable( $safe ), $safe );
    }
}
