<?php
/**
 * The portable SQL helpers every driver offers (eZDBInterface::sqlLimit(), sqlUpsert(), sqlGroupConcat(),
 * sqlUnixTimestamp(), sqlFromUnixTimestamp(), sqlQuoteIdentifier(), sqlLiteral(); expDBSQLHelpers):
 *
 *  HL-01 - Each dialect's words (mysql, postgresql, sqlite, oracle, sqlserver, generic) for every helper
 *  HL-02 - Each driver names its dialect: eZMySQLiDB and expMongoDB mysql, eZPostgreSQLDB postgresql, eZSQLite3DB
 *          sqlite, expSQLServerDB sqlserver, expPDODB its PDO driver's
 *  HL-03 - limit 0 is null (send nothing); no limit and no offset leaves the statement; offset alone
 *  HL-04 - upsert: update columns by default, explicit, none (insert only); keys checked; values escaped
 *  HL-05 - Literals: NULL, booleans, integers, floats without exponent, NaN as NULL, text escaped by the driver
 *  HL-06 - Live on SQLite (pdo_sqlite in var/tmp): limit, upsert insert/update/insert-only, group concat, unix
 *          timestamps round trip
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/sqlserver/expSQLServerTestKit.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdb/classes/expdbsqlhelpers.php';
require_once dirname( __DIR__, 4 ) . '/lib/ezdb/classes/exppdodb.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
class expDBSQLHelpersTest extends PHPUnit\Framework\TestCase
{
    public static function lit( $value )
    {
        return expDBSQLHelpers::literal( function ( $s ) { return str_replace( "'", "''", $s ); }, $value );
    }

    public static function limits()
    {
        return array(
            array( 'mysql', 10, 20, "SELECT a FROM t\nLIMIT 10 OFFSET 20" ),
            array( 'mysql', 10, 0, "SELECT a FROM t\nLIMIT 10" ),
            array( 'mysql', false, 5, "SELECT a FROM t\nLIMIT 18446744073709551615 OFFSET 5" ),
            array( 'postgresql', 10, 20, "SELECT a FROM t\nLIMIT 10 OFFSET 20" ),
            array( 'postgresql', null, 5, "SELECT a FROM t\nLIMIT ALL OFFSET 5" ),
            array( 'sqlite', 10, 20, "SELECT a FROM t\nLIMIT 10 OFFSET 20" ),
            array( 'sqlite', false, 5, "SELECT a FROM t\nLIMIT -1 OFFSET 5" ),
            array( 'oracle', 10, 20, "SELECT a FROM t\nOFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY" ),
            array( 'generic', false, 3, "SELECT a FROM t\nOFFSET 3 ROWS" ),
            array( 'sqlserver', 10, 20, "SELECT a FROM t\nORDER BY (SELECT NULL)\nOFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY" ),
            array( 'mysql', '7', '-3', "SELECT a FROM t\nLIMIT 7" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('limits')]
    public function testLimit( $dialect, $limit, $offset, $expected )
    {
        $this->assertSame( $expected, expDBSQLHelpers::limit( $dialect, 'SELECT a FROM t;', $limit, $offset ) );
    }

    public static function dialects()
    {
        $list = array();
        foreach ( expDBSQLHelpers::DIALECTS as $d )
            $list[$d] = array( $d );
        return $list;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dialects')]
    public function testLimitZeroIsNullAndNoLimitIsTheStatement( $dialect )
    {
        $this->assertNull( expDBSQLHelpers::limit( $dialect, 'SELECT a FROM t', 0, 5 ) );
        $this->assertSame( 'SELECT a FROM t', expDBSQLHelpers::limit( $dialect, 'SELECT a FROM t', false, 0 ) );
        $this->assertSame( 'SELECT a FROM t', expDBSQLHelpers::limit( $dialect, 'SELECT a FROM t', 'x', 0 ) );
    }

    public static function quotes()
    {
        return array(
            array( 'mysql', 'a.b', '`a`.`b`' ), array( 'mysql', 'x`y', '`x``y`' ),
            array( 'postgresql', 'order', '"order"' ), array( 'postgresql', 'a"b', '"a""b"' ),
            array( 'sqlite', 's.t', '"s"."t"' ), array( 'oracle', 't', '"t"' ), array( 'sqlserver', 'dbo.t', '[dbo].[t]' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('quotes')]
    public function testQuoteIdentifier( $dialect, $name, $expected )
    {
        $this->assertSame( $expected, expDBSQLHelpers::quoteIdentifier( $dialect, $name ) );
    }

    public function testEmptyNamePartIsRefused()
    {
        $this->expectException( InvalidArgumentException::class );
        expDBSQLHelpers::quoteIdentifier( 'mysql', 'a..b' );
    }

    public static function upserts()
    {
        $row = array( 'name' => 'k', 'value' => "it's" );
        return array(
            'mysql update' => array( 'mysql', $row, null, "INSERT INTO `t` (`name`, `value`) VALUES ('k', 'it''s') ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)" ),
            'mysql insert only' => array( 'mysql', $row, array(), "INSERT IGNORE INTO `t` (`name`, `value`) VALUES ('k', 'it''s')" ),
            'postgresql update' => array( 'postgresql', $row, null, "INSERT INTO \"t\" (\"name\", \"value\") VALUES ('k', 'it''s') ON CONFLICT (\"name\") DO UPDATE SET \"value\" = excluded.\"value\"" ),
            'postgresql insert only' => array( 'postgresql', $row, array(), "INSERT INTO \"t\" (\"name\", \"value\") VALUES ('k', 'it''s') ON CONFLICT (\"name\") DO NOTHING" ),
            'sqlite update' => array( 'sqlite', $row, array( 'value' ), "INSERT INTO \"t\" (\"name\", \"value\") VALUES ('k', 'it''s') ON CONFLICT (\"name\") DO UPDATE SET \"value\" = excluded.\"value\"" ),
            'oracle merge' => array( 'oracle', $row, null, "MERGE INTO \"t\" t USING (SELECT 'k' AS \"name\", 'it''s' AS \"value\" FROM dual) s ON (t.\"name\" = s.\"name\") WHEN MATCHED THEN UPDATE SET t.\"value\" = s.\"value\" WHEN NOT MATCHED THEN INSERT (\"name\", \"value\") VALUES (s.\"name\", s.\"value\")" ),
            'sqlserver merge' => array( 'sqlserver', $row, null, "MERGE INTO [t] WITH (HOLDLOCK) AS t USING (SELECT N'k' AS [name], N'it''s' AS [value]) AS s ON (t.[name] = s.[name]) WHEN MATCHED THEN UPDATE SET t.[value] = s.[value] WHEN NOT MATCHED THEN INSERT ([name], [value]) VALUES (s.[name], s.[value]);" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('upserts')]
    public function testUpsert( $dialect, array $row, $update, $expected )
    {
        $this->assertSame( $expected, expDBSQLHelpers::upsert( $dialect, array( __CLASS__, 'lit' ), 't', $row, array( 'name' ), $update ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dialects')]
    public function testUpsertNeedsKeyValues( $dialect )
    {
        $this->expectException( InvalidArgumentException::class );
        expDBSQLHelpers::upsert( $dialect, array( __CLASS__, 'lit' ), 't', array( 'a' => 1 ), array( 'b' ) );
    }

    public static function groupConcats()
    {
        return array(
            array( 'mysql', 'name', ', ', 'name', "GROUP_CONCAT(name ORDER BY name SEPARATOR ', ')" ),
            array( 'mysql', 'name', ',', null, "GROUP_CONCAT(name SEPARATOR ',')" ),
            array( 'postgresql', 'id', ',', 'id DESC', "STRING_AGG(CAST(id AS TEXT), ',' ORDER BY id DESC)" ),
            array( 'sqlite', 'name', '|', 'name', "GROUP_CONCAT(name, '|')" ),
            array( 'oracle', 'name', ',', null, "LISTAGG(name, ',') WITHIN GROUP (ORDER BY NULL)" ),
            array( 'sqlserver', 'name', ',', 'name', "STRING_AGG(CAST(name AS NVARCHAR(MAX)), N',') WITHIN GROUP (ORDER BY name)" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('groupConcats')]
    public function testGroupConcat( $dialect, $expression, $separator, $order, $expected )
    {
        $this->assertSame( $expected, expDBSQLHelpers::groupConcat( $dialect, array( __CLASS__, 'lit' ), $expression, $separator, $order ) );
    }

    public static function timestamps()
    {
        return array(
            array( 'mysql', null, 'UNIX_TIMESTAMP()', 'FROM_UNIXTIME(x)' ),
            array( 'mysql', 'd', 'UNIX_TIMESTAMP(d)', null ),
            array( 'postgresql', null, 'CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS BIGINT)', "(TIMESTAMP '1970-01-01 00:00:00' + (x) * INTERVAL '1 second')" ),
            array( 'sqlite', null, "CAST(strftime('%s', 'now') AS INTEGER)", "datetime(x, 'unixepoch')" ),
            array( 'sqlite', 'd', "CAST(strftime('%s', d) AS INTEGER)", null ),
            array( 'oracle', null, "ROUND((CAST(SYS_EXTRACT_UTC(SYSTIMESTAMP) AS DATE) - DATE '1970-01-01') * 86400)", "(TIMESTAMP '1970-01-01 00:00:00' + NUMTODSINTERVAL(x, 'SECOND'))" ),
            array( 'sqlserver', null, 'DATEDIFF_BIG(SECOND, DATETIME2FROMPARTS(1970, 1, 1, 0, 0, 0, 0, 0), SYSUTCDATETIME())', null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('timestamps')]
    public function testUnixTimestamps( $dialect, $expression, $expected, $from )
    {
        $this->assertSame( $expected, expDBSQLHelpers::unixTimestamp( $dialect, $expression ) );
        if ( $from !== null )
            $this->assertSame( $from, expDBSQLHelpers::fromUnixTimestamp( $dialect, 'x' ) );
    }

    public static function forUpdates()
    {
        return array( array( 'mysql', 'SELECT * FROM t WHERE id = 1 FOR UPDATE' ), array( 'postgresql', 'SELECT * FROM t WHERE id = 1 FOR UPDATE' ),
                      array( 'oracle', 'SELECT * FROM t WHERE id = 1 FOR UPDATE' ), array( 'generic', 'SELECT * FROM t WHERE id = 1 FOR UPDATE' ),
                      array( 'sqlite', 'SELECT * FROM t WHERE id = 1' ), array( 'sqlserver', 'SELECT * FROM t WITH (UPDLOCK, ROWLOCK) WHERE id = 1' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('forUpdates')]
    public function testForUpdate( $dialect, $expected )
    {
        $this->assertSame( $expected, expDBSQLHelpers::forUpdate( $dialect, 'SELECT * FROM t WHERE id = 1;' ) );
    }
    public static function literals()
    {
        return array( array( null, 'NULL' ), array( true, '1' ), array( false, '0' ), array( 7, '7' ), array( -2.5, '-2.5' ),
                      array( 1.0e15, '1000000000000000' ), array( NAN, 'NULL' ), array( INF, 'NULL' ), array( -0.0, '0' ),
                      array( "a'b", "'a''b'" ), array( '5', "'5'" ), array( '', "''" ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('literals')]
    public function testLiteral( $value, $expected )
    {
        $this->assertSame( $expected, self::lit( $value ) );
    }

    public static function drivers()
    {
        return array(
            'mysqli' => array( 'eZMySQLiDB', 'mysql' ),
            'postgresql' => array( 'eZPostgreSQLDB', 'postgresql' ),
            'sqlite' => array( 'eZSQLite3DB', 'sqlite' ),
            'sqlserver' => array( 'expSQLServerDB', 'sqlserver' ),
            'null' => array( 'eZNullDB', 'mysql' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('drivers')]
    public function testDriverNamesItsDialect( $class, $dialect )
    {
        $db = ( new ReflectionClass( $class ) )->newInstanceWithoutConstructor();
        $this->assertSame( $dialect, $db->sqlHelperDialect() );
        $this->assertSame( expDBSQLHelpers::limit( $dialect, 'SELECT a FROM t', 3, 1 ), $db->sqlLimit( 'SELECT a FROM t', 3, 1 ) );
        $this->assertSame( expDBSQLHelpers::quoteIdentifier( $dialect, 'x' ), $db->sqlQuoteIdentifier( 'x' ) );
        $this->assertSame( expDBSQLHelpers::unixTimestamp( $dialect ), $db->sqlUnixTimestamp() );
        $this->assertSame( expDBSQLHelpers::fromUnixTimestamp( $dialect, 'x' ), $db->sqlFromUnixTimestamp( 'x' ) );
        $this->assertSame( expDBSQLHelpers::forUpdate( $dialect, 'SELECT * FROM t' ), $db->sqlForUpdate( 'SELECT * FROM t' ) );
        $this->assertStringContainsString( '7', $db->sqlUpsert( 't', array( 'a' => 7, 'b' => null ), array( 'a' ) ) );
    }

    public function testMongoDriverSpeaksTheBaseDialect()
    {
        if ( !class_exists( 'expMongoDB' ) )
            $this->markTestSkipped( 'expMongoDB is not loadable here' );
        $db = ( new ReflectionClass( 'expMongoDB' ) )->newInstanceWithoutConstructor();
        $this->assertSame( 'mysql', $db->sqlHelperDialect() );
    }

    public static function pdoDialects()
    {
        return array( array( 'sqlserver', 'sqlserver' ), array( 'oracle', 'oracle' ), array( 'mysql', 'mysql' ), array( 'postgresql', 'postgresql' ),
                      array( 'sqlite', 'sqlite' ), array( 'generic', 'generic' ), array( 'unknown', 'generic' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('pdoDialects')]
    public function testPdoDriverNamesItsDialect( $dialect, $expected )
    {
        $db = ( new ReflectionClass( 'expPDODB' ) )->newInstanceWithoutConstructor();
        $p = new ReflectionProperty( 'expPDODB', 'Dialect' );
        $p->setValue( $db, $dialect );
        $this->assertSame( $expected, $db->sqlHelperDialect() );
    }

    public function testSqlServerLiteralsAreUnicode()
    {
        $db = ( new ReflectionClass( 'expSQLServerDB' ) )->newInstanceWithoutConstructor();
        $this->assertSame( "N'ä'", $db->sqlLiteral( 'ä' ) );
    }

    // ---------------------------------------------------------------- HL-06 live on SQLite

    public function testLiveSqliteHelpers()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) || version_compare( SQLite3::version()['versionString'] ?? '0', '3.24.0', '<' ) )
            $this->markTestSkipped( 'pdo_sqlite 3.24 or later is needed' );
        $saved = expSQLServerTestKit::quietDebug();
        try
        {
            $db = new expPDODB( array( 'pdo_dsn' => 'sqlite::memory:', 'pdo_driver' => 'sqlite' ) );
            $this->assertSame( 'sqlite', $db->sqlHelperDialect() );
            $db->query( 'CREATE TABLE kv (name TEXT PRIMARY KEY, value TEXT, n INTEGER)' );
            $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'a', 'value' => 'one', 'n' => 1 ), array( 'name' ) ) ) );
            $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'a', 'value' => "it's", 'n' => 2 ), array( 'name' ) ) ) );
            $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'a', 'value' => 'ignored', 'n' => 3 ), array( 'name' ), array() ) ) );
            $this->assertTrue( $db->query( $db->sqlUpsert( 'kv', array( 'name' => 'b', 'value' => 'two', 'n' => 5 ), array( 'name' ) ) ) );
            $this->assertSame( array( array( 'name' => 'a', 'value' => "it's", 'n' => '2' ), array( 'name' => 'b', 'value' => 'two', 'n' => '5' ) ),
                               array_map( function ( $r ) { return array_map( 'strval', $r ); }, array_values( $db->arrayQuery( 'SELECT name, value, n FROM kv ORDER BY name' ) ) ) );
            $rows = $db->arrayQuery( $db->sqlLimit( 'SELECT name FROM kv ORDER BY name', 1, 1 ) );
            $this->assertSame( 'b', reset( $rows )['name'] );
            $joined = $db->arrayQuery( 'SELECT ' . $db->sqlGroupConcat( 'name', '|' ) . ' AS j FROM (SELECT name FROM kv ORDER BY name)' );
            $this->assertSame( 'a|b', reset( $joined )['j'] );
            $ts = $db->arrayQuery( 'SELECT ' . $db->sqlUnixTimestamp( $db->sqlFromUnixTimestamp( '2147483748' ) ) . ' AS t, ' . $db->sqlUnixTimestamp() . ' AS now' );
            $this->assertSame( '2147483748', (string)reset( $ts )['t'] );
            $this->assertEqualsWithDelta( time(), (int)reset( $ts )['now'], 5 );
        }
        finally
        {
            expSQLServerTestKit::restoreDebug( $saved );
        }
    }
}
