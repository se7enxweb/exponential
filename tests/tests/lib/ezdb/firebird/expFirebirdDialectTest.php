<?php
/**
 * expFirebirdDialect (no server needed): the Firebird words of the kernel's statements.
 *
 *  FD-01 - Names: quoted in upper case (as Firebird stores an unquoted name), reserved words quoted, MySQL backticks
 *          as double quotes; empty, NUL and over-long names refused
 *  FD-02 - Reserved words that are kernel columns (value, count, time ...) are quoted where they stand as a name,
 *          not as a function, a type, the unit of EXTRACT or before a literal; never inside a literal or a comment
 *  FD-03 - Functions Firebird lacks: LENGTH, IFNULL, NOW(), CONCAT, SUBSTRING(s, f, n), MD5, UNIX_TIMESTAMP,
 *          GREATEST/LEAST, nested calls
 *  FD-04 - LIKE with a backslash gets ESCAPE '\'; one with its own ESCAPE or without a backslash is left alone
 *  FD-05 - Trailing LIMIT (three forms) as OFFSET ... FETCH; FOR UPDATE gets WITH LOCK, after the paging;
 *          a SELECT without FROM reads RDB$DATABASE; TRUNCATE becomes DELETE; rewrite() is idempotent
 *  FD-06 - applyLimit(): paging appended, before FOR UPDATE, a paged statement wrapped, limit 0 is null
 *  FD-07 - Helpers: upsert as MERGE, LIST, timestamps, IN lists in chunks of 1,500, temporary tables
 *  FD-08 - Errors by category; DSN; versions; long literals as parameters; catalogue type names
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expFirebirdTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('firebird')]
class expFirebirdDialectTest extends PHPUnit\Framework\TestCase
{
    public function testNames()
    {
        $this->assertSame( '"EZUSER"', expFirebirdDialect::quoteIdentifier( 'ezuser' ) );
        $this->assertSame( '"A""B"', expFirebirdDialect::quoteIdentifier( 'a"b' ) );
        $this->assertSame( '"T"."VALUE"', expFirebirdDialect::quoteName( 't.value' ) );
        $this->assertSame( 'ezuser', expFirebirdDialect::quoteIdentifierIfNeeded( 'ezuser' ) );
        $this->assertSame( '"VALUE"', expFirebirdDialect::quoteIdentifierIfNeeded( 'value' ) );
        $this->assertSame( '"MY-TABLE"', expFirebirdDialect::quoteIdentifierIfNeeded( 'my-table' ) );
        $this->assertTrue( expFirebirdDialect::isReservedWord( 'position' ) );
        $this->assertFalse( expFirebirdDialect::isReservedWord( 'contentobject_id' ) );
        $this->assertSame( 'SELECT "NAME" FROM "EZUSER"', expFirebirdDialect::backticksToQuotes( 'SELECT `name` FROM `ezuser`' ) );
        foreach ( array( '', "a\0b", str_repeat( 'x', 64 ) ) as $bad )
        {
            try
            {
                expFirebirdDialect::quoteIdentifier( $bad );
                $this->fail( 'refused: ' . strlen( $bad ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
        $this->assertSame( '"' . str_repeat( 'X', 63 ) . '"', expFirebirdDialect::quoteIdentifier( str_repeat( 'x', 63 ) ) );
    }

    public static function nameCases()
    {
        return array(
            'column value' => array( "SELECT value FROM ezsite_data WHERE name = 'x'", "SELECT \"VALUE\" FROM ezsite_data WHERE name = 'x'" ),
            'qualified' => array( 'SELECT t.value, t.count FROM t', 'SELECT t."VALUE", t."COUNT" FROM t' ),
            'set' => array( 'UPDATE ezview_counter SET count = count + 1 WHERE node_id = 2', 'UPDATE ezview_counter SET "COUNT" = "COUNT" + 1 WHERE node_id = 2' ),
            'alias' => array( 'SELECT COUNT(*) AS count FROM t', 'SELECT COUNT(*) AS "COUNT" FROM t' ),
            'function kept' => array( 'SELECT COUNT(id), POSITION(\'a\' IN name) FROM t', 'SELECT COUNT(id), POSITION(\'a\' IN name) FROM t' ),
            'insert columns' => array( 'INSERT INTO ezuser_accountkey (hash_key, time, user_id) VALUES (\'k\', 5, 14)',
                                       'INSERT INTO ezuser_accountkey (hash_key, "TIME", user_id) VALUES (\'k\', 5, 14)' ),
            'cast type' => array( 'SELECT CAST(x AS DATE) FROM t', 'SELECT CAST(x AS DATE) FROM t' ),
            'cast timestamp without time zone' => array( 'SELECT CAST(x AS TIMESTAMP WITHOUT TIME ZONE) FROM t', 'SELECT CAST(x AS TIMESTAMP WITHOUT TIME ZONE) FROM t' ),
            'extract unit' => array( 'SELECT EXTRACT(DAY FROM d) FROM t', 'SELECT EXTRACT(DAY FROM d) FROM t' ),
            'typed literal' => array( "SELECT id FROM t WHERE d > DATE '2026-01-01'", "SELECT id FROM t WHERE d > DATE '2026-01-01'" ),
            'in literal and comment' => array( "SELECT 'value' /* value */ FROM t -- count\n", "SELECT 'value' /* value */ FROM t -- count" ),
            'reserved alias' => array( "SELECT COUNT( * ) AS row_count FROM exp_data_sync WHERE run_mode='live'", "SELECT COUNT( * ) AS \"ROW_COUNT\" FROM exp_data_sync WHERE run_mode='live'" ),
            'cast types kept' => array( 'SELECT CAST(a AS INTEGER), CAST(b AS VARCHAR(20)), CAST(c AS DOUBLE PRECISION) FROM t',
                                        'SELECT CAST(a AS INTEGER), CAST(b AS VARCHAR(20)), CAST(c AS DOUBLE PRECISION) FROM t' ),
            'day column' => array( 'SELECT day, time FROM ezgeneral_digest_user_settings', 'SELECT "DAY", "TIME" FROM ezgeneral_digest_user_settings' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('nameCases')]
    public function testReservedNamesAreQuoted( $sql, $expected )
    {
        $this->assertSame( $expected, expFirebirdDialect::rewrite( $sql ) );
    }

    public static function functionCases()
    {
        return array(
            'length' => array( 'SELECT id FROM t WHERE LENGTH( u.login ) > 0', 'SELECT id FROM t WHERE CHAR_LENGTH( u.login ) > 0' ),
            'ifnull' => array( 'SELECT IFNULL(a, 0) FROM t', 'SELECT COALESCE(a, 0) FROM t' ),
            'now' => array( 'UPDATE t SET d = NOW()', 'UPDATE t SET d = LOCALTIMESTAMP' ),
            'concat' => array( "SELECT CONCAT(a, '-', b) AS c FROM t", "SELECT (a || '-' || b) AS c FROM t" ),
            'concat nested' => array( "SELECT CONCAT(a, CONCAT(b, c)) FROM t", "SELECT (a || (b || c)) FROM t" ),
            'concat with call' => array( "SELECT CONCAT(LOWER(a), 'x') FROM t", "SELECT (LOWER(a) || 'x') FROM t" ),
            'substring comma' => array( 'SELECT SUBSTRING(name, 1, 3) FROM t', 'SELECT SUBSTRING(name FROM 1 FOR 3) FROM t' ),
            'substring two' => array( 'SELECT SUBSTRING(name, 2) FROM t', 'SELECT SUBSTRING(name FROM 2) FROM t' ),
            'substring sql form kept' => array( 'SELECT SUBSTRING(name FROM 2 FOR 1) FROM t', 'SELECT SUBSTRING(name FROM 2 FOR 1) FROM t' ),
            'md5' => array( "SELECT MD5('a') FROM t", "SELECT LOWER(HEX_ENCODE(CRYPT_HASH('a' USING MD5))) FROM t" ),
            'greatest least' => array( 'SELECT GREATEST(a, b), LEAST(a, b) FROM t', 'SELECT MAXVALUE(a, b), MINVALUE(a, b) FROM t' ),
            'method name kept' => array( 'SELECT t.length FROM t', 'SELECT t.length FROM t' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('functionCases')]
    public function testFunctions( $sql, $expected )
    {
        $this->assertSame( $expected, expFirebirdDialect::rewrite( $sql ) );
    }

    public function testUnixTimestampCall()
    {
        $this->assertStringContainsString( "DATEDIFF(SECOND FROM TIMESTAMP '1970-01-01 00:00:00' TO CAST(CURRENT_TIMESTAMP AT TIME ZONE 'UTC'",
                                           expFirebirdDialect::rewrite( 'SELECT UNIX_TIMESTAMP() AS t FROM t' ) );
    }

    public function testLikeEscape()
    {
        $this->assertSame( "SELECT id FROM t WHERE name LIKE 'a\\_%' ESCAPE '\\'", expFirebirdDialect::rewrite( "SELECT id FROM t WHERE name LIKE 'a\\_%'" ) );
        $this->assertSame( "SELECT id FROM t WHERE name LIKE 'a!_%' ESCAPE '!'", expFirebirdDialect::rewrite( "SELECT id FROM t WHERE name LIKE 'a!_%' ESCAPE '!'" ) );
        $this->assertSame( "SELECT id FROM t WHERE name LIKE 'a%'", expFirebirdDialect::rewrite( "SELECT id FROM t WHERE name LIKE 'a%'" ) );
        $this->assertSame( "SELECT id FROM t WHERE name NOT LIKE 'a\\%' ESCAPE '\\'", expFirebirdDialect::rewrite( "SELECT id FROM t WHERE name NOT LIKE 'a\\%'" ) );
    }

    public static function statementCases()
    {
        return array(
            'limit' => array( 'SELECT id FROM t ORDER BY id LIMIT 10', "SELECT id FROM t ORDER BY id\nOFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY" ),
            'limit offset' => array( 'SELECT id FROM t LIMIT 10 OFFSET 20', "SELECT id FROM t\nOFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY" ),
            'limit comma' => array( 'SELECT id FROM t LIMIT 20, 10', "SELECT id FROM t\nOFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY" ),
            'limit in subquery kept' => array( 'SELECT id FROM t WHERE id IN (SELECT id FROM u LIMIT 1)', 'SELECT id FROM t WHERE id IN (SELECT id FROM u LIMIT 1)' ),
            'for update' => array( "SELECT * FROM ezcontentobject WHERE id='1' FOR UPDATE", "SELECT * FROM ezcontentobject WHERE id='1' FOR UPDATE WITH LOCK" ),
            'for update with lock kept' => array( 'SELECT * FROM t FOR UPDATE WITH LOCK', 'SELECT * FROM t FOR UPDATE WITH LOCK' ),
            'limit and for update' => array( 'SELECT * FROM t LIMIT 1 FOR UPDATE', "SELECT * FROM t\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY FOR UPDATE WITH LOCK" ),
            'select without from' => array( 'SELECT 1 AS one', 'SELECT 1 AS one FROM RDB$DATABASE' ),
            'select subquery without outer from' => array( 'SELECT (SELECT MAX(id) FROM t) AS m', 'SELECT (SELECT MAX(id) FROM t) AS m FROM RDB$DATABASE' ),
            'truncate' => array( 'TRUNCATE TABLE ezisbn_group', 'DELETE FROM ezisbn_group' ),
            'truncate short' => array( 'TRUNCATE `ezisbn_group`;', 'DELETE FROM "EZISBN_GROUP"' ),
            'ddl untouched' => array( 'CREATE TABLE t (value INTEGER, `count` INTEGER)', 'CREATE TABLE t (value INTEGER, "COUNT" INTEGER)' ),
            'trailing semicolon' => array( 'DELETE FROM t WHERE id = 1;', 'DELETE FROM t WHERE id = 1' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('statementCases')]
    public function testStatements( $sql, $expected )
    {
        $this->assertSame( $expected, expFirebirdDialect::rewrite( $sql ) );
        // rewriting the result again changes nothing
        $this->assertSame( $expected, expFirebirdDialect::rewrite( $expected ) );
    }

    public function testRewriteIsIdempotentOnFunctions()
    {
        foreach ( self::functionCases() + self::nameCases() as $case )
            $this->assertSame( $case[1], expFirebirdDialect::rewrite( $case[1] ), $case[0] );
    }

    public function testApplyLimit()
    {
        $this->assertSame( "SELECT id FROM t ORDER BY id\nOFFSET 5 ROWS FETCH NEXT 10 ROWS ONLY", expFirebirdDialect::applyLimit( 'SELECT id FROM t ORDER BY id', 10, 5 ) );
        $this->assertSame( "SELECT id FROM t\nOFFSET 5 ROWS", expFirebirdDialect::applyLimit( 'SELECT id FROM t', false, 5 ) );
        $this->assertSame( 'SELECT id FROM t', expFirebirdDialect::applyLimit( 'SELECT id FROM t;', false, 0 ) );
        $this->assertNull( expFirebirdDialect::applyLimit( 'SELECT id FROM t', 0, 0 ) );
        $this->assertSame( "SELECT id FROM t\nOFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY FOR UPDATE WITH LOCK",
                           expFirebirdDialect::applyLimit( 'SELECT id FROM t FOR UPDATE WITH LOCK', 1 ) );
        $this->assertSame( "SELECT * FROM (\nSELECT id FROM t\nOFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY\n) exp_page\nOFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY",
                           expFirebirdDialect::applyLimit( "SELECT id FROM t\nOFFSET 0 ROWS FETCH NEXT 3 ROWS ONLY", 1, 1 ) );
        $this->assertSame( 'DELETE FROM t', expFirebirdDialect::applyLimit( 'DELETE FROM t', 1, 1 ) );
        $this->assertSame( "SELECT id FROM t\nOFFSET 0 ROWS FETCH NEXT 7 ROWS ONLY", expDBSQLHelpers::limit( 'firebird', 'SELECT id FROM t', 7 ) );
    }

    public function testHelpers()
    {
        $this->assertSame( "MERGE INTO \"KV\" t USING (SELECT 'a' AS \"NAME\", 'it''s' AS \"VALUE\" FROM RDB\$DATABASE) s ON (t.\"NAME\" = s.\"NAME\")"
                         . " WHEN MATCHED THEN UPDATE SET \"VALUE\" = s.\"VALUE\" WHEN NOT MATCHED THEN INSERT (\"NAME\", \"VALUE\") VALUES (s.\"NAME\", s.\"VALUE\")",
                           expFirebirdDialect::upsert( 'kv', array( 'name' => 'a', 'value' => "it's" ), array( 'name' ) ) );
        $insertOnly = expFirebirdDialect::upsert( 'kv', array( 'name' => 'a', 'n' => 1 ), array( 'name' ), array() );
        $this->assertStringNotContainsString( 'WHEN MATCHED', $insertOnly );
        $this->assertSame( $insertOnly, expDBSQLHelpers::upsert( 'firebird', 'strval', 'kv', array( 'name' => 'a', 'n' => 1 ), array( 'name' ), array() ) );
        $this->assertSame( "LIST(name, '|')", expFirebirdDialect::groupConcat( 'name', '|', 'name' ) );
        $this->assertSame( "DATEADD(SECOND, 5, TIMESTAMP '1970-01-01 00:00:00')", expDBSQLHelpers::fromUnixTimestamp( 'firebird', '5' ) );
        $this->assertSame( "DATEDIFF(SECOND FROM TIMESTAMP '1970-01-01 00:00:00' TO CAST(d AS TIMESTAMP))", expDBSQLHelpers::unixTimestamp( 'firebird', 'd' ) );
        $this->assertSame( 'SELECT * FROM t WHERE id = 1 FOR UPDATE WITH LOCK', expDBSQLHelpers::forUpdate( 'firebird', 'SELECT * FROM t WHERE id = 1;' ) );
        $this->assertSame( '"EZUSER"."LOGIN"', expDBSQLHelpers::quoteIdentifier( 'firebird', 'ezuser.login' ) );
        $this->assertSame( 'SUBSTRING(s FROM 2 FOR 3)', expFirebirdDialect::subString( 's', 2, 3 ) );
        $this->assertSame( "(a || 'b')", expFirebirdDialect::concatString( array( 'a', "'b'" ) ) );
        $this->assertSame( 'BIN_AND(a, 4)', expFirebirdDialect::bitAnd( 'a', 4 ) );
        $this->assertSame( 'BIN_OR(a, 4)', expFirebirdDialect::bitOr( 'a', 4 ) );
        $this->assertSame( "1", expFirebirdDialect::literal( true ) );
        $this->assertSame( "'a''b\\c'", expFirebirdDialect::literal( "a'b\\c" ) );
    }

    public function testInList()
    {
        $this->assertSame( 'id IN ( 1, 2 )', expFirebirdDialect::inList( array( 1, 2, 2 ), 'id', false, 'int' ) );
        $this->assertSame( "name NOT IN ( 'a', 'b''c' )", expFirebirdDialect::inList( array( 'a', "b'c" ), 'name', true, 'string' ) );
        // without a type the elements are SQL as given: the kernel quotes language codes itself
        $this->assertSame( "language_code IN ( 'eng-GB', 'ger-DE' )", expFirebirdDialect::inList( array( "'eng-GB'", "'ger-DE'" ), 'language_code' ) );
        $this->assertSame( '1 = 0', expFirebirdDialect::inList( array(), 'id' ) );
        $long = expFirebirdDialect::inList( range( 1, 3001 ), 'id', false, 'int' );
        $this->assertSame( 2, substr_count( $long, ' OR ' ) );
        $this->assertSame( 1, substr_count( expFirebirdDialect::inList( range( 1, 1600 ), 'id', true, 'int', 1000 ), ' AND ' ) );
    }

    public function testTemporaryTable()
    {
        $this->assertSame( array( 'name' => 'ezsearch_tmp_0',
                                  'sql' => 'CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL PRIMARY KEY, published INTEGER ) ON COMMIT PRESERVE ROWS' ),
                           expFirebirdDialect::temporaryTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, published int )' ) );
        $this->assertSame( 'CREATE GLOBAL TEMPORARY TABLE eznode_count_0 ( "COUNT" INTEGER ) ON COMMIT PRESERVE ROWS',
                           expFirebirdDialect::temporaryTable( 'CREATE TEMPORARY TABLE eznode_count_0 ( count int )' )['sql'] );
        $this->assertNull( expFirebirdDialect::temporaryTable( 'CREATE TABLE t ( id int )' ) );
    }

    public static function bitCases()
    {
        return array(
            'mask' => array( 'SELECT id FROM o WHERE o.language_mask & 6 > 0', 'SELECT id FROM o WHERE BIN_AND(o.language_mask, 6) > 0' ),
            'two columns' => array( 'SELECT id FROM o, l WHERE l.id & o.language_mask > 0', 'SELECT id FROM o, l WHERE BIN_AND(l.id, o.language_mask) > 0' ),
            'parenthesized' => array( 'SELECT ( a - ( a & b ) ) / 2 FROM t', 'SELECT ( a - ( BIN_AND(a, b) ) ) / 2 FROM t' ),
            'chain' => array( 'SELECT a & b & 4 FROM t', 'SELECT BIN_AND(BIN_AND(a, b), 4) FROM t' ),
            'not' => array( 'UPDATE t SET language_id = language_id & ~1', 'UPDATE t SET language_id = BIN_AND(language_id, BIN_NOT(1))' ),
            'or' => array( 'UPDATE t SET m = m | 4', 'UPDATE t SET m = BIN_OR(m, 4)' ),
            'concat kept' => array( "SELECT a || '&' || b FROM t", "SELECT a || '&' || b FROM t" ),
            'call operand' => array( 'SELECT ABS(a) & 1 FROM t', 'SELECT BIN_AND(ABS(a), 1) FROM t' ),
            'shifts' => array( 'SELECT ( ( a & 4 ) >> 1 ) + ( ( a & 2 ) << 1 ) FROM t', 'SELECT ( BIN_SHR(( BIN_AND(a, 4) ), 1) ) + ( BIN_SHL(( BIN_AND(a, 2) ), 1) ) FROM t' ),
            'comparisons kept' => array( 'SELECT a FROM t WHERE a <> 1 AND b >= 2 AND c <= 3', 'SELECT a FROM t WHERE a <> 1 AND b >= 2 AND c <= 3' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('bitCases')]
    public function testBitOperators( $sql, $expected )
    {
        $this->assertSame( $expected, expFirebirdDialect::bitOperators( $sql ) );
    }

    public function testConditionalDDL()
    {
        $this->assertSame( array( 'exists' => false, 'type' => 'TABLE', 'name' => 'EXPSERVICES_TOKEN', 'sql' => 'DROP TABLE expservices_token' ),
                           expFirebirdDialect::conditionalDDL( 'DROP TABLE IF EXISTS expservices_token' ) );
        $create = expFirebirdDialect::conditionalDDL( 'CREATE UNIQUE INDEX IF NOT EXISTS t_a ON t (a)' );
        $this->assertSame( array( true, 'INDEX', 'T_A', 'CREATE UNIQUE INDEX t_a ON t (a)' ), array_values( $create ) );
        $this->assertSame( 'Quoted', expFirebirdDialect::conditionalDDL( 'DROP TABLE IF EXISTS "Quoted"' )['name'] );
        $this->assertNull( expFirebirdDialect::conditionalDDL( 'DROP TABLE t' ) );
        $this->assertSame( 'ezurl', expFirebirdDialect::droppedTable( 'DROP TABLE EZURL' ) );
        $this->assertSame( 'ezurl', expFirebirdDialect::droppedTable( 'DROP TABLE "EZURL"' ) );
        $this->assertNull( expFirebirdDialect::droppedTable( 'DROP INDEX ezurl_url' ) );
        $this->assertNull( expFirebirdDialect::conditionalDDL( "SELECT 'DROP TABLE IF EXISTS t' FROM t" ) );
        $this->assertSame( "SELECT 1 AS found FROM RDB\$RELATIONS WHERE RDB\$RELATION_NAME = 'T'", expFirebirdDialect::existsQuery( 'TABLE', 'T' ) );
        $this->assertStringContainsString( 'RDB$GENERATORS', expFirebirdDialect::existsQuery( 'SEQUENCE', 'S_T' ) );
    }

    public function testErrorsAndConnection()
    {
        $this->assertSame( 'deadlock', expFirebirdDialect::errorCategory( -913, 'deadlock update conflicts with concurrent update' ) );
        $this->assertSame( 'deadlock', expFirebirdDialect::errorCategory( null, '', 335544336 ) );
        $this->assertSame( 'lock', expFirebirdDialect::errorCategory( -901, 'lock conflict on no wait transaction' ) );
        $this->assertSame( 'duplicate', expFirebirdDialect::errorCategory( -803, 'violation of PRIMARY or UNIQUE KEY constraint "INTEG_4"' ) );
        $this->assertSame( 'connection', expFirebirdDialect::errorCategory( -902, 'connection shutdown' ) );
        $this->assertSame( 'connection', expFirebirdDialect::errorCategory( -902, 'Error reading data from the connection.' ) );
        $this->assertSame( 'unknown_object', expFirebirdDialect::errorCategory( -204, 'Table unknown' ) );
        $this->assertSame( 'syntax', expFirebirdDialect::errorCategory( -104, 'Token unknown' ) );
        $this->assertSame( 'other', expFirebirdDialect::errorCategory( -413, 'conversion error from string' ) );

        $this->assertSame( 'firebird:dbname=db.example.com:exponential;charset=UTF8', expFirebirdDialect::pdoDSN( array( 'host' => 'db.example.com', 'port' => 3050, 'database' => 'exponential' ) ) );
        $this->assertSame( 'firebird:dbname=db.example.com/3051:/data/x.fdb;charset=UTF8;role=EDITOR',
                           expFirebirdDialect::pdoDSN( array( 'host' => 'db.example.com', 'port' => 3051, 'database' => '/data/x.fdb', 'role' => 'EDITOR' ) ) );
        $this->assertSame( 'firebird:dbname=[::1]/3051:x;charset=UTF8', expFirebirdDialect::pdoDSN( array( 'host' => '::1', 'port' => 3051, 'database' => 'x' ) ) );
        $this->assertSame( 'firebird:dbname=/data/x.fdb;charset=UTF8', expFirebirdDialect::pdoDSN( array( 'host' => '', 'database' => '/data/x.fdb' ) ) );
        $this->expectException( InvalidArgumentException::class );
        expFirebirdDialect::pdoDSN( array( 'host' => 'h', 'database' => 'x;role=SYSDBA' ) );
    }

    public function testVersionsLiteralsAndTypes()
    {
        $v = expFirebirdDialect::describeVersion( 'LI-V5.0.4.1812 Firebird 5.0' );
        $this->assertSame( array( 5, 0, '5.0.4.1812', true ), array( $v['major'], $v['minor'], $v['string'], $v['supported'] ) );
        $this->assertTrue( expFirebirdDialect::describeVersion( 'WI-V4.0.5.3140 Firebird 4.0' )['supported'] );
        $this->assertFalse( expFirebirdDialect::describeVersion( 'LI-V3.0.11.33703 Firebird 3.0' )['supported'] );
        $this->assertFalse( expFirebirdDialect::describeVersion( 'WI-V14.4.0.0 InterBase 2020' )['supported'] );

        $big = str_repeat( 'x', 20000 );
        $this->assertNull( expFirebirdDialect::parameterizeLongLiterals( "UPDATE t SET a = 'b'" ) );
        $this->assertSame( array( "UPDATE t SET a = ?, b = 'c' WHERE id = 1", array( $big . "'" ) ),
                           expFirebirdDialect::parameterizeLongLiterals( "UPDATE t SET a = '" . $big . "''', b = 'c' WHERE id = 1" ) );
        $this->assertNull( expFirebirdDialect::parameterizeLongLiterals( "UPDATE t SET a = '" . $big . "', b = ?" ) );

        $this->assertSame( 'integer', expFirebirdDialect::typeName( 8 ) );
        $this->assertSame( 'decimal', expFirebirdDialect::typeName( 8, 2, -2 ) );
        $this->assertSame( 'blob sub_type text', expFirebirdDialect::typeName( 261, 1 ) );
        $this->assertSame( 'varchar', expFirebirdDialect::typeName( 37 ) );
    }
}
