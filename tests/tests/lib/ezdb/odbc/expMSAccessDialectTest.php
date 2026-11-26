<?php
/**
 * The SQL of MS Access (lib/ezdb/classes/expmsaccessdialect.php), without Access:
 *
 *  MA-01 - tokenize() is lossless and tells strings, [names], `names`, #dates#, comments and words apart
 *  MA-02 - quoteName(): brackets, dotted names, refused characters and names past 64 characters
 *  MA-03 - escapeString(), literal(), dateLiteral()
 *  MA-04 - translate(): comments out, backticks to brackets, trailing ; dropped; nothing inside literals changes
 *  MA-05 - split(): one statement per call; ; inside strings, names and comments does not split
 *  MA-06 - top(): TOP after SELECT [DISTINCT], UNION and an existing TOP wrapped, 0 rows sends nothing
 *  MA-07 - applyLimit(): TOP; an offset throws expDBUnsupportedException
 *  MA-08 - Functions: &, Mid(), Len(), IIf(), DateDiff/DateAdd for unix time stamps
 *  MA-09 - bitAnd()/bitOr(): BAND/BOR in ANSI-92; for a constant mask the integer emulation equals PHP's & and |
 *  MA-10 - columnType() and createTable(): COUNTER, LONG, TEXT(n), LONGTEXT, DECIMAL or CURRENCY, DEFAULT only in
 *          ANSI-92, primary key constraint, unique and plain indexes, index names within 64 characters
 *  MA-11 - expDBSQLHelpers with the msaccess dialect: names, limit, upsert and GROUP_CONCAT refused, FOR UPDATE
 *          left out, time stamps
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expODBCTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('odbc')]
class expMSAccessDialectTest extends PHPUnit\Framework\TestCase
{
    /** MA-01 */
    public function testTokenizeIsLosslessAndTellsTokensApart()
    {
        $sql = "SELECT [Order ID], `x`, 'it''s; [no]' & \"two\" FROM t -- c;\n WHERE d > #2026-01-02 03:04:05# /* ; */ AND n = 1.5e3;";
        $tokens = expMSAccessDialect::tokenize( $sql );
        $this->assertSame( $sql, implode( '', array_column( $tokens, 'text' ) ) );
        $types = array();
        foreach ( $tokens as $token )
            $types[$token['text']] = $token['type'];
        $this->assertSame( expMSAccessDialect::IDENT, $types['[Order ID]'] );
        $this->assertSame( expMSAccessDialect::IDENT, $types['`x`'] );
        $this->assertSame( expMSAccessDialect::STRING, $types["'it''s; [no]'"] );
        $this->assertSame( expMSAccessDialect::STRING, $types['"two"'] );
        $this->assertSame( expMSAccessDialect::DATE, $types['#2026-01-02 03:04:05#'] );
        $this->assertSame( expMSAccessDialect::COMMENT, $types['/* ; */'] );
        $this->assertSame( expMSAccessDialect::NUMBER, $types['1.5e3'] );
        // random bytes never lose a byte
        for ( $i = 0; $i < 200; $i++ )
        {
            $random = random_bytes( random_int( 1, 60 ) );
            $this->assertSame( $random, implode( '', array_column( expMSAccessDialect::tokenize( $random ), 'text' ) ) );
        }
    }

    /** MA-02 */
    public function testQuoteName()
    {
        $this->assertSame( '[Order Details]', expMSAccessDialect::quoteName( 'Order Details' ) );
        $this->assertSame( '[s].[t]', expMSAccessDialect::quoteName( 's.t' ) );
        $this->assertSame( '[Größe]', expMSAccessDialect::quoteName( 'Größe' ) );
        foreach ( array( 'a]b', "a\0b", '', 'a[b', 'a`b', 'a!b', str_repeat( 'x', 65 ) ) as $bad )
        {
            try
            {
                expMSAccessDialect::quoteName( $bad );
                $this->fail( "'$bad' was quoted" );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->addToAssertionCount( 1 );
            }
        }
        $this->assertSame( '[' . str_repeat( 'ä', 64 ) . ']', expMSAccessDialect::quoteName( str_repeat( 'ä', 64 ) ) );
    }

    /** MA-03 */
    public function testLiterals()
    {
        $this->assertSame( "it''s \\ fine", expMSAccessDialect::escapeString( "it's \\ fi\0ne" ) );
        $this->assertSame( 'NULL', expMSAccessDialect::literal( null ) );
        $this->assertSame( '1', expMSAccessDialect::literal( true ) );
        $this->assertSame( '42', expMSAccessDialect::literal( 42 ) );
        $this->assertSame( '0.5', expMSAccessDialect::literal( 0.5 ) );
        $this->assertSame( "'O''Neil'", expMSAccessDialect::literal( "O'Neil" ) );
        $this->assertSame( '#2026-11-25 10:11:12#', expMSAccessDialect::dateLiteral( '2026-11-25 10:11:12' ) );
        $this->assertSame( '#' . date( 'Y-m-d H:i:s', 86400 ) . '#', expMSAccessDialect::dateLiteral( 86400 ) );
        $this->expectException( InvalidArgumentException::class );
        expMSAccessDialect::dateLiteral( 'not a date at all' );
    }

    /** MA-04 */
    public function testTranslate()
    {
        $this->assertSame( "SELECT [a], [b c] FROM [t]  \n WHERE x = '`no` -- kept'", expMSAccessDialect::translate( "SELECT `a`, [b c] FROM `t` -- gone\n WHERE x = '`no` -- kept';" ) );
        $this->assertSame( 'SELECT 1   FROM t', expMSAccessDialect::translate( 'SELECT 1 /* x */ FROM t' ) );
    }

    /** MA-05 */
    public function testSplit()
    {
        $this->assertSame( array( "INSERT INTO t VALUES ('a;b')", 'SELECT [x;y] FROM t', 'DELETE FROM t' ),
                           expMSAccessDialect::split( "INSERT INTO t VALUES ('a;b');\nSELECT [x;y] FROM t; -- c;d\n;DELETE FROM t;" ) );
        $this->assertSame( array(), expMSAccessDialect::split( " ; ;\n" ) );
    }

    /** MA-06 */
    public function testTop()
    {
        $this->assertSame( 'SELECT TOP 5 a FROM t ORDER BY a', expMSAccessDialect::top( 'SELECT a FROM t ORDER BY a', 5 ) );
        $this->assertSame( 'select distinct TOP 3 a from t', expMSAccessDialect::top( 'select distinct a from t', 3 ) );
        $this->assertSame( 'SELECT TOP 2 * FROM ( SELECT a FROM t UNION SELECT b FROM u ) AS [page]', expMSAccessDialect::top( 'SELECT a FROM t UNION SELECT b FROM u', 2 ) );
        $this->assertSame( 'SELECT TOP 2 * FROM ( SELECT TOP 10 a FROM t ) AS [page]', expMSAccessDialect::top( 'SELECT TOP 10 a FROM t', 2 ) );
        $this->assertSame( 'SELECT TOP 1 a FROM ( SELECT b FROM u UNION SELECT c FROM v ) AS x', expMSAccessDialect::top( 'SELECT a FROM ( SELECT b FROM u UNION SELECT c FROM v ) AS x', 1 ) );
        $this->assertNull( expMSAccessDialect::top( 'SELECT a FROM t', 0 ) );
        $this->assertSame( 'SELECT a FROM t', expMSAccessDialect::top( 'SELECT a FROM t;', false ) );
    }

    /** MA-07 */
    public function testApplyLimit()
    {
        $this->assertSame( 'SELECT TOP 10 * FROM t', expMSAccessDialect::applyLimit( 'SELECT * FROM t', 10 ) );
        $this->assertSame( 'SELECT * FROM t', expMSAccessDialect::applyLimit( 'SELECT * FROM t', false ) );
        $this->expectException( expDBUnsupportedException::class );
        expMSAccessDialect::applyLimit( 'SELECT * FROM t', 10, 20 );
    }

    /** MA-08 */
    public function testFunctions()
    {
        $this->assertSame( ' ( a & \'-\' & b ) ', expMSAccessDialect::concatString( array( 'a', "'-'", 'b' ) ) );
        $this->assertSame( ' Mid( name, 2, 3 ) ', expMSAccessDialect::subString( 'name', 2, 3 ) );
        $this->assertSame( ' Mid( name, 2 ) ', expMSAccessDialect::subString( 'name', 2 ) );
        $this->assertSame( 'Len( name )', expMSAccessDialect::charLength( 'name' ) );
        $this->assertSame( 'IIf( a > 1, 1, 0 )', expMSAccessDialect::iif( 'a > 1', '1', '0' ) );
        $this->assertSame( "DateDiff( 's', #1970-01-01 00:00:00#, Now() )", expMSAccessDialect::unixTimestamp() );
        $this->assertSame( "DateDiff( 's', #1970-01-01 00:00:00#, created )", expMSAccessDialect::unixTimestamp( 'created' ) );
        $this->assertSame( "DateAdd( 's', ts, #1970-01-01 00:00:00# )", expMSAccessDialect::fromUnixTimestamp( 'ts' ) );
    }

    /**
     * Evaluates the integer emulation as Access would: \ is integer division, MOD the remainder.
     */
    private static function evaluate( $expression, $a )
    {
        $php = str_replace( array( 'x', '\\', 'MOD' ), array( (string)$a, 'intdiv', '%' ), $expression );
        // ( ( ( n ) intdiv 4 ) % 2 ) -> intdiv( n, 4 ) % 2
        $php = preg_replace( '/\(\s*(\d+)\s*\)\s*intdiv\s*(\d+)/', 'intdiv( $1, $2 )', $php );
        return eval( "return $php;" );
    }

    /** MA-09 */
    public function testBitOperations()
    {
        $this->assertSame( ' ( a BAND b ) ', expMSAccessDialect::bitAnd( 'a', 'b', true ) );
        $this->assertSame( ' ( a BOR b ) ', expMSAccessDialect::bitOr( 'a', 'b', true ) );
        $this->assertSame( ' 0 ', expMSAccessDialect::bitAnd( 'x', '0' ) );
        foreach ( array( 1, 2, 3, 6, 255, 1024, 0x40000001, 0x7fffffff ) as $mask )
        {
            $and = expMSAccessDialect::bitAnd( 'x', (string)$mask );
            $andSwapped = expMSAccessDialect::bitAnd( (string)$mask, 'x' );
            $or = expMSAccessDialect::bitOr( 'x', (string)$mask );
            foreach ( array( 0, 1, 2, 5, 7, 254, 1023, 0x40000001, 0x7ffffffe, 123456789 ) as $a )
            {
                $this->assertSame( $a & $mask, self::evaluate( $and, $a ), "$a & $mask" );
                $this->assertSame( $a & $mask, self::evaluate( $andSwapped, $a ), "$mask & $a" );
                $this->assertSame( $a | $mask, self::evaluate( $or, $a ), "$a | $mask" );
            }
        }
        $this->expectException( expDBUnsupportedException::class );
        expMSAccessDialect::bitAnd( 'a', 'b', false );
    }

    /** MA-10 */
    public function testColumnTypesAndCreateTable()
    {
        $this->assertSame( 'COUNTER NOT NULL', expMSAccessDialect::columnType( array( 'type' => 'auto_increment' ) ) );
        $this->assertSame( 'LONG DEFAULT 0 NOT NULL', expMSAccessDialect::columnType( array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ) ) );
        $this->assertSame( 'LONG NOT NULL', expMSAccessDialect::columnType( array( 'type' => 'int', 'not_null' => '1', 'default' => 0 ), false ) );
        $this->assertSame( "TEXT(50) DEFAULT 'it''s'", expMSAccessDialect::columnType( array( 'type' => 'varchar', 'length' => 50, 'default' => "it's" ) ) );
        $this->assertSame( 'LONGTEXT', expMSAccessDialect::columnType( array( 'type' => 'varchar', 'length' => 1000 ) ) );
        $this->assertSame( 'LONGTEXT', expMSAccessDialect::columnType( array( 'type' => 'longtext', 'default' => 'x', 'not_null' => '1' ) ) );
        $this->assertSame( 'DECIMAL(19,0)', expMSAccessDialect::columnType( array( 'type' => 'bigint' ) ) );
        $this->assertSame( 'DOUBLE', expMSAccessDialect::columnType( array( 'type' => 'bigint' ), false ) );
        $this->assertSame( 'DECIMAL(10,2)', expMSAccessDialect::columnType( array( 'type' => 'decimal', 'length' => 10 ) ) );
        $this->assertSame( 'CURRENCY', expMSAccessDialect::columnType( array( 'type' => 'decimal' ), false ) );
        $this->assertSame( 'DOUBLE', expMSAccessDialect::columnType( array( 'type' => 'float' ) ) );
        $this->assertSame( 'DATETIME', expMSAccessDialect::columnType( array( 'type' => 'datetime' ) ) );
        $statements = expMSAccessDialect::createTable( 'ezurl', array(
            'fields' => array( 'id' => array( 'type' => 'auto_increment' ), 'url' => array( 'type' => 'longtext' ), 'md5' => array( 'type' => 'char', 'length' => 32 ),
                               'is_valid' => array( 'type' => 'int', 'not_null' => '1', 'default' => 1 ) ),
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'id' ) ),
                                'ezurl_md5' => array( 'type' => 'non-unique', 'fields' => array( array( 'name' => 'md5', 'mysql:length' => 10 ) ) ),
                                'ezurl_u' => array( 'type' => 'unique', 'fields' => array( 'md5', 'is_valid' ) ) ) ) );
        $this->assertSame( array(
            'CREATE TABLE [ezurl] ([id] COUNTER NOT NULL, [url] LONGTEXT, [md5] TEXT(32), [is_valid] LONG DEFAULT 1 NOT NULL, CONSTRAINT [ezurl_pk] PRIMARY KEY ([id]))',
            'CREATE INDEX [ezurl_ezurl_md5] ON [ezurl] ([md5])',
            'CREATE UNIQUE INDEX [ezurl_ezurl_u] ON [ezurl] ([md5], [is_valid])' ), $statements );
        $long = expMSAccessDialect::indexName( str_repeat( 't', 40 ), str_repeat( 'i', 40 ) );
        $this->assertSame( 64, strlen( $long ) );
    }

    /** MA-11 */
    public function testSqlHelpers()
    {
        $literal = array( 'expMSAccessDialect', 'literal' );
        $this->assertSame( '[a b].[c]', expDBSQLHelpers::quoteIdentifier( 'msaccess', 'a b.c' ) );
        $this->assertSame( 'SELECT TOP 3 * FROM t', expDBSQLHelpers::limit( 'msaccess', 'SELECT * FROM t', 3 ) );
        $this->assertNull( expDBSQLHelpers::limit( 'msaccess', 'SELECT * FROM t', 0 ) );
        $this->assertSame( 'SELECT * FROM t WHERE id = 1', expDBSQLHelpers::forUpdate( 'msaccess', 'SELECT * FROM t WHERE id = 1' ) );
        $this->assertSame( "DateDiff( 's', #1970-01-01 00:00:00#, Now() )", expDBSQLHelpers::unixTimestamp( 'msaccess' ) );
        $this->assertSame( "DateAdd( 's', 5, #1970-01-01 00:00:00# )", expDBSQLHelpers::fromUnixTimestamp( 'msaccess', '5' ) );
        foreach ( array( function () use ( $literal ) { expDBSQLHelpers::upsert( 'msaccess', $literal, 't', array( 'id' => 1, 'v' => 'x' ), array( 'id' ) ); },
                         function () use ( $literal ) { expDBSQLHelpers::groupConcat( 'msaccess', $literal, 'name' ); },
                         function () { expDBSQLHelpers::limit( 'msaccess', 'SELECT * FROM t', 10, 5 ); } ) as $refused )
        {
            try
            {
                $refused();
                $this->fail( 'not refused' );
            }
            catch ( expDBUnsupportedException $e )
            {
                $this->assertSame( 'msaccess', $e->dialect() );
                $this->assertStringContainsString( 'msaccess', $e->getMessage() );
            }
        }
    }
}
?>
