<?php
/**
 * expHANADialect: the SAP HANA SQL the driver writes, without a server.
 *
 *  HD-01 - Names: quoting in upper case, reserved words, the length limit; backticks become double quotes
 *  HD-02 - Functions: CONCAT of more than two, CHAR_LENGTH, SUBSTR, MD5, CURDATE, UNIX_TIMESTAMP, FROM_UNIXTIME
 *  HD-03 - Bit operators as BITAND/BITOR/BITNOT, || left as the concatenation
 *  HD-04 - LIKE with a backslash gets ESCAPE; a keyword inside a literal is never rewritten
 *  HD-05 - LIMIT m, n; SELECT without FROM; applyLimit() wraps a statement with paging of its own, keeps FOR UPDATE
 *  HD-06 - INSERT of several rows split per row; TRUNCATE as DELETE; IF [NOT] EXISTS read from the catalogue
 *  HD-07 - NCLOB columns read as TO_NVARCHAR where compared or sorted, not where set, selected or matched with LIKE
 *  HD-11 - Literal values as parameters where HANA can type them
 *  HD-12 - Literals with more than ASCII as the bytes of CESU-8 (BINTOSTR), not in DDL
 *  HD-08 - Upsert as MERGE, GROUP_CONCAT as STRING_AGG, IN lists in chunks, long literals as parameters
 *  HD-09 - Temporary tables as global temporary tables with HANA's types
 *  HD-10 - Errors by category, the connection string, versions, the SQL helpers' dialect hana
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expHANATestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('hana')]
class expHANADialectTest extends PHPUnit\Framework\TestCase
{
    public function testNames()
    {
        $this->assertSame( '"EZSITE_DATA"', expHANADialect::quoteIdentifier( 'ezsite_data' ) );
        $this->assertSame( '"A"."B"', expHANADialect::quoteName( 'a.b' ) );
        $this->assertSame( 'value', expHANADialect::quoteIdentifierIfNeeded( 'value' ) );
        $this->assertSame( '"ORDER"', expHANADialect::quoteIdentifierIfNeeded( 'order' ) );
        $this->assertSame( '"A-B"', expHANADialect::quoteIdentifierIfNeeded( 'a-b' ) );
        $this->assertTrue( expHANADialect::isReservedWord( 'limit' ) );
        $this->assertFalse( expHANADialect::isReservedWord( 'count' ) );
        $this->assertSame( 'SELECT "NAME" FROM "EZSITE_DATA"', expHANADialect::rewrite( 'SELECT `name` FROM `ezsite_data`' ) );
        $this->expectException( InvalidArgumentException::class );
        expHANADialect::quoteIdentifier( str_repeat( 'a', 128 ) );
    }

    public function testFunctions()
    {
        $this->assertSame( "SELECT (a || '/' || b) AS p FROM t", expHANADialect::rewrite( "SELECT CONCAT(a, '/', b) AS p FROM t" ) );
        $this->assertSame( 'SELECT LENGTH(name) FROM t', expHANADialect::rewrite( 'SELECT CHAR_LENGTH(name) FROM t' ) );
        $this->assertSame( 'SELECT SUBSTRING(name, 1, 3) FROM t', expHANADialect::rewrite( 'SELECT SUBSTR(name, 1, 3) FROM t' ) );
        $this->assertSame( 'SELECT LOWER(BINTOHEX(HASH_MD5(TO_VARBINARY(TO_NVARCHAR(name))))) AS h FROM t',
                           expHANADialect::rewrite( 'SELECT MD5(name) AS h FROM t' ) );
        $this->assertSame( 'SELECT CURRENT_DATE FROM DUMMY', expHANADialect::rewrite( 'SELECT CURDATE()' ) );
        $this->assertSame( "SELECT SECONDS_BETWEEN(TO_TIMESTAMP('1970-01-01 00:00:00'), CURRENT_UTCTIMESTAMP) AS n FROM DUMMY",
                           expHANADialect::rewrite( 'SELECT UNIX_TIMESTAMP() AS n' ) );
        $this->assertSame( "SELECT ADD_SECONDS(TO_TIMESTAMP('1970-01-01 00:00:00'), created) FROM t",
                           expHANADialect::rewrite( 'SELECT FROM_UNIXTIME(created) FROM t' ) );
        // a method of an object, not the function
        $this->assertSame( 'SELECT x.CONCAT(a) FROM t', expHANADialect::rewrite( 'SELECT x.CONCAT(a) FROM t' ) );
    }

    public function testBitOperators()
    {
        $this->assertSame( 'SELECT id FROM ezcontentobject WHERE BITAND(ezcontentobject.language_mask, 6) > 0',
                           expHANADialect::rewrite( 'SELECT id FROM ezcontentobject WHERE ezcontentobject.language_mask & 6 > 0' ) );
        $this->assertSame( 'UPDATE t SET mask = BITOR(mask, 1)', expHANADialect::rewrite( 'UPDATE t SET mask = mask | 1' ) );
        $this->assertSame( 'SELECT BITAND(a, BITNOT(b)) FROM t', expHANADialect::rewrite( 'SELECT a & ~b FROM t' ) );
        $this->assertSame( "SELECT a || 'x' FROM t", expHANADialect::rewrite( "SELECT a || 'x' FROM t" ) );
        $this->assertSame( 'SELECT BITAND((a + 1), (b - 1)) FROM t', expHANADialect::rewrite( 'SELECT (a + 1) & (b - 1) FROM t' ) );
    }

    public function testLikeAndLiterals()
    {
        $this->assertSame( "SELECT id FROM t WHERE name LIKE 'a\\_b%' ESCAPE '\\'", expHANADialect::rewrite( "SELECT id FROM t WHERE name LIKE 'a\\_b%'" ) );
        $this->assertSame( "SELECT id FROM t WHERE name LIKE 'a#_b' ESCAPE '#'", expHANADialect::rewrite( "SELECT id FROM t WHERE name LIKE 'a#_b' ESCAPE '#'" ) );
        $this->assertSame( "SELECT 'CONCAT(a, b) LIMIT 1, 2 & 3' AS t FROM DUMMY", expHANADialect::rewrite( "SELECT 'CONCAT(a, b) LIMIT 1, 2 & 3' AS t" ) );
        $this->assertSame( "it''s", expHANADialect::escapeString( "it's" ) );
        $this->assertSame( 'a\\b', expHANADialect::escapeString( 'a\\b' ) );
        $this->assertSame( "NULL|1|42|1.5|'x'", implode( '|', array_map( array( 'expHANADialect', 'literal' ), array( null, true, 42, 1.5, 'x' ) ) ) );
    }

    public function testPaging()
    {
        $this->assertSame( "SELECT id FROM t ORDER BY id\nLIMIT 20 OFFSET 10", expHANADialect::rewrite( 'SELECT id FROM t ORDER BY id LIMIT 10, 20' ) );
        $this->assertSame( 'SELECT id FROM t ORDER BY id LIMIT 20 OFFSET 10', expHANADialect::rewrite( 'SELECT id FROM t ORDER BY id LIMIT 20 OFFSET 10' ) );
        $this->assertSame( 'SELECT 1 AS ok FROM DUMMY', expHANADialect::rewrite( 'SELECT 1 AS ok' ) );
        $this->assertSame( 'SELECT 1 AS ok FROM DUMMY WHERE 1 = 1', expHANADialect::rewrite( 'SELECT 1 AS ok WHERE 1 = 1' ) );
        $this->assertSame( 'SELECT a FROM t UNION SELECT b FROM u', expHANADialect::rewrite( 'SELECT a FROM t UNION SELECT b FROM u' ) );
        $this->assertSame( "SELECT id FROM t\nLIMIT 5 OFFSET 10", expHANADialect::applyLimit( 'SELECT id FROM t', 5, 10 ) );
        $this->assertSame( "SELECT id FROM t\nLIMIT 2147483647 OFFSET 3", expHANADialect::applyLimit( 'SELECT id FROM t', false, 3 ) );
        $this->assertSame( "SELECT id FROM t\nLIMIT 5 FOR UPDATE", expHANADialect::applyLimit( 'SELECT id FROM t FOR UPDATE', 5 ) );
        $this->assertSame( "SELECT * FROM (\nSELECT id FROM t LIMIT 50\n) exp_page\nLIMIT 5", expHANADialect::applyLimit( 'SELECT id FROM t LIMIT 50', 5 ) );
        $this->assertNull( expHANADialect::applyLimit( 'SELECT id FROM t', 0 ) );
        $this->assertSame( 'SELECT id FROM t', expHANADialect::applyLimit( 'SELECT id FROM t;', false ) );
        $this->assertSame( 'DELETE FROM t', expHANADialect::applyLimit( 'DELETE FROM t', 5 ) );
        $this->assertSame( 'SELECT id FROM t FOR UPDATE', expHANADialect::forUpdate( 'SELECT id FROM t;' ) );
    }

    public function testStatementShapes()
    {
        $this->assertSame( array( "INSERT INTO t (a, b) VALUES (1, 'x')", "INSERT INTO t (a, b) VALUES (2, 'y, z')" ),
                           expHANADialect::splitMultiRowInsert( "INSERT INTO t (a, b) VALUES (1, 'x'), (2, 'y, z')" ) );
        $this->assertNull( expHANADialect::splitMultiRowInsert( "INSERT INTO t (a) VALUES (1)" ) );
        $this->assertNull( expHANADialect::splitMultiRowInsert( "INSERT INTO t (a) SELECT a FROM u" ) );
        $this->assertSame( 'DELETE FROM ezsearch_tmp_0', expHANADialect::rewrite( 'TRUNCATE TABLE ezsearch_tmp_0' ) );
        $this->assertSame( array( 'exists' => false, 'type' => 'TABLE', 'name' => 'EZX', 'sql' => 'DROP TABLE ezx' ),
                           expHANADialect::conditionalDDL( 'DROP TABLE IF EXISTS ezx' ) );
        $this->assertSame( array( 'exists' => true, 'type' => 'INDEX', 'name' => 'Ix', 'sql' => 'CREATE UNIQUE INDEX "Ix" ON t (a)' ),
                           expHANADialect::conditionalDDL( 'CREATE UNIQUE INDEX IF NOT EXISTS "Ix" ON t (a)' ) );
        $this->assertNull( expHANADialect::conditionalDDL( 'DROP TABLE ezx' ) );
        $this->assertSame( 'ezx', expHANADialect::droppedTable( 'DROP TABLE ezx' ) );
        $this->assertNull( expHANADialect::droppedTable( 'DROP INDEX ezx' ) );
        $this->assertSame( 'ezsite_data', expHANADialect::insertTable( 'INSERT INTO ezsite_data (name) VALUES (1)' ) );
        $this->assertSame( 'ezsite_data', expHANADialect::insertTable( 'INSERT INTO "EXP"."EZSITE_DATA" (name) VALUES (1)' ) );
        $this->assertTrue( expHANADialect::isSchemaChange( 'create table x (a int)' ) );
        $this->assertFalse( expHANADialect::isSchemaChange( 'SELECT 1' ) );
        $this->assertStringContainsString( 'SYS.SEQUENCES', expHANADialect::existsQuery( 'SEQUENCE', 'S_X' ) );
    }

    public function testLobColumns()
    {
        $lob = array( 'data_text', 'param' );
        $this->assertSame( "SELECT id FROM ezpending_actions WHERE action = 'a' AND TO_NVARCHAR(param) = '5'",
                           expHANADialect::rewrite( "SELECT id FROM ezpending_actions WHERE action = 'a' AND param = '5'", $lob ) );
        $this->assertSame( "DELETE FROM ezpending_actions WHERE TO_NVARCHAR(ezpending_actions.param) IN ('1', '2')",
                           expHANADialect::rewrite( "DELETE FROM ezpending_actions WHERE ezpending_actions.param IN ('1', '2')", $lob ) );
        $this->assertSame( "UPDATE t SET data_text = 'x', b = 1 WHERE TO_NVARCHAR(data_text) <> 'y'",
                           expHANADialect::rewrite( "UPDATE t SET data_text = 'x', b = 1 WHERE data_text <> 'y'", $lob ) );
        $this->assertSame( 'SELECT data_text FROM t ORDER BY TO_NVARCHAR(t.data_text) DESC, id',
                           expHANADialect::rewrite( 'SELECT data_text FROM t ORDER BY t.data_text DESC, id', $lob ) );
        $this->assertSame( "SELECT data_text FROM t WHERE data_text LIKE 'a%' AND data_text IS NOT NULL",
                           expHANADialect::rewrite( "SELECT data_text FROM t WHERE data_text LIKE 'a%' AND data_text IS NOT NULL", $lob ) );
        $this->assertSame( "SELECT id FROM t WHERE 'x' = TO_NVARCHAR(param)", expHANADialect::rewrite( "SELECT id FROM t WHERE 'x' = param", $lob ) );
        $this->assertSame( "SELECT id FROM t WHERE TO_NVARCHAR(param) NOT IN ('x')", expHANADialect::rewrite( "SELECT id FROM t WHERE param NOT IN ('x')", $lob ) );
        $this->assertSame( "SELECT id FROM t WHERE param = 'x'", expHANADialect::rewrite( "SELECT id FROM t WHERE param = 'x'" ) );
        $this->assertSame( 'MERGE INTO kv t USING (SELECT 1 AS param FROM DUMMY) s ON (TO_NVARCHAR(t.param) = TO_NVARCHAR(s.param)) WHEN MATCHED THEN UPDATE SET t.param = s.param',
                           expHANADialect::rewrite( 'MERGE INTO kv t USING (SELECT 1 AS param FROM DUMMY) s ON (t.param = s.param) WHEN MATCHED THEN UPDATE SET t.param = s.param', $lob ) );
    }

    public function testParameterize()
    {
        $this->assertSame( array( "INSERT INTO t (a, b, c, d) VALUES (?, ?, NULL, ?)", array( '1', "it's", '-2.5' ) ),
                           expHANADialect::parameterize( "INSERT INTO t (a, b, c, d) VALUES (1, 'it''s', NULL, -2.5)" ) );
        $this->assertSame( array( "SELECT a FROM t WHERE id = ? AND name <> ? AND x >= ? AND n LIKE ? ESCAPE '\\' AND k IN ( ?, ? )",
                                  array( '5', 'x', '-1', 'a%', '1', '2' ) ),
                           expHANADialect::parameterize( "SELECT a FROM t WHERE id = 5 AND name <> 'x' AND x >= -1 AND n LIKE 'a%' ESCAPE '\\' AND k IN ( 1, 2 )" ) );
        $this->assertSame( array( 'UPDATE t SET a = ?, b = a + 1 WHERE BITAND(m, 6) > ?', array( 'v', '0' ) ),
                           expHANADialect::parameterize( "UPDATE t SET a = 'v', b = a + 1 WHERE BITAND(m, 6) > 0" ) );
        // literals that stay: both sides of 1 = 1, a select list, ORDER BY, a type, a statement with "?" of its own
        $this->assertNull( expHANADialect::parameterize( "SELECT 'x' AS a, CAST(b AS NVARCHAR(10)) FROM t WHERE 1 = 1 ORDER BY 1" ) );
        $this->assertNull( expHANADialect::parameterize( "SELECT a FROM t WHERE id = ? AND b = 'x'" ) );
        $this->assertNull( expHANADialect::parameterize( "CREATE TABLE t (a INTEGER DEFAULT 0)" ) );
        $this->assertSame( array( 'INSERT INTO t (a) SELECT b FROM u WHERE c = ?', array( '3' ) ),
                           expHANADialect::parameterize( 'INSERT INTO t (a) SELECT b FROM u WHERE c = 3' ) );
    }

    public function testUnicodeLiterals()
    {
        $this->assertSame( "SELECT TO_NVARCHAR(BINTOSTR(X'C3A4E282ACEDA0BDEDB880'))AS a, 'ascii' AS b FROM DUMMY",
                           expHANADialect::unicodeLiterals( "SELECT 'ä€😀'AS a, 'ascii' AS b FROM DUMMY" ) );
        $this->assertSame( "CREATE COLUMN TABLE t (a NVARCHAR(5) DEFAULT 'ä')", expHANADialect::unicodeLiterals( "CREATE COLUMN TABLE t (a NVARCHAR(5) DEFAULT 'ä')" ) );
        $this->assertSame( 'eda0bdedb880', bin2hex( expHANADialect::cesu8( '😀' ) ) );
        $this->assertSame( 'ä€', expHANADialect::cesu8( 'ä€' ) );
    }

    public function testHelpers()
    {
        $this->assertSame( 'MERGE INTO "EZSITE_DATA" t USING (SELECT \'a\' AS "NAME", \'1\' AS "VALUE" FROM DUMMY) s ON (t."NAME" = s."NAME")'
                         . ' WHEN MATCHED THEN UPDATE SET t."VALUE" = s."VALUE" WHEN NOT MATCHED THEN INSERT ("NAME", "VALUE") VALUES (s."NAME", s."VALUE")',
                           expHANADialect::upsert( 'ezsite_data', array( 'name' => 'a', 'value' => '1' ), array( 'name' ) ) );
        $this->assertStringNotContainsString( 'WHEN MATCHED', expHANADialect::upsert( 't', array( 'a' => 1 ), array( 'a' ), array() ) );
        $this->assertSame( "STRING_AGG(TO_NVARCHAR(name), ', ' ORDER BY id)", expHANADialect::groupConcat( 'name', ', ', 'id' ) );
        $this->assertSame( 'id IN ( 1, 2 )', expHANADialect::inList( array( '1', 2, 2 ), 'id', false, 'int' ) );
        $this->assertSame( '( id NOT IN ( 1, 2 ) AND id NOT IN ( 3 ) )', expHANADialect::inList( array( 1, 2, 3 ), 'id', true, 'int', 2 ) );
        $this->assertSame( '1 = 0', expHANADialect::inList( array(), 'id' ) );
        $long = str_repeat( 'ä', 3000 );
        $bound = expHANADialect::parameterizeLongLiterals( "INSERT INTO t (a, b) VALUES ('" . $long . "', 'short')" );
        $this->assertSame( "INSERT INTO t (a, b) VALUES (?, 'short')", $bound[0] );
        $this->assertSame( array( $long ), $bound[1] );
        $this->assertNull( expHANADialect::parameterizeLongLiterals( "SELECT 'short' FROM DUMMY" ) );
        $this->assertSame( 'SUBSTRING(a, 2, 3)', expHANADialect::subString( 'a', 2, 3 ) );
        $this->assertSame( '(a || b)', expHANADialect::concatString( array( 'a', 'b' ) ) );
        $this->assertSame( 'BITAND(a, 2)', expHANADialect::bitAnd( 'a', 2 ) );
        $this->assertSame( 'BITOR(a, 2)', expHANADialect::bitOr( 'a', 2 ) );
    }

    public function testTemporaryTables()
    {
        $this->assertSame( array( 'name' => 'ezsearch_tmp_0', 'sql' => 'CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL PRIMARY KEY, name NVARCHAR(100), "LIMIT" BIGINT )' ),
                           expHANADialect::temporaryTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, name varchar(100), limit bigint(20) )' ) );
        $this->assertNull( expHANADialect::temporaryTable( 'CREATE TABLE x (a int)' ) );
        $this->assertSame( 'body NCLOB', expHANADialect::columnDefinition( 'body longtext' ) );
        $this->assertSame( 'nvarchar(10)', expHANADialect::typeName( 'NVARCHAR', 10 ) );
        $this->assertSame( 'decimal(10,2)', expHANADialect::typeName( 'DECIMAL', 10, 2 ) );
        $this->assertSame( 'nclob', expHANADialect::typeName( 'NCLOB', 2147483647 ) );
    }

    public function testErrorsConnectionVersions()
    {
        $this->assertSame( 'deadlock', expHANADialect::errorCategory( 133, 'transaction rolled back by detected deadlock' ) );
        $this->assertSame( 'lock', expHANADialect::errorCategory( 131, 'transaction rolled back by lock wait timeout' ) );
        $this->assertSame( 'duplicate', expHANADialect::errorCategory( 301, 'unique constraint violated' ) );
        $this->assertSame( 'connection', expHANADialect::errorCategory( -10807, 'Connection down' ) );
        $this->assertSame( 'connection', expHANADialect::errorCategory( null, 'x', '08S01' ) );
        $this->assertSame( 'syntax', expHANADialect::errorCategory( 257, 'sql syntax error' ) );
        $this->assertSame( 'unknown_object', expHANADialect::errorCategory( 259, 'invalid table name' ) );
        $this->assertSame( 'other', expHANADialect::errorCategory( 287, 'cannot insert NULL' ) );
        $this->assertSame( 301, expHANATransport::codeInMessage( '[SAP AG][LIBODBCHDB SO][HDBODBC] Integrity constraint violation;301 unique constraint violated' ) );

        $this->assertSame( 'DRIVER=HDBODBC;SERVERNODE=hana.example:30041;CURRENTSCHEMA=EXP;CHAR_AS_UTF8=TRUE',
                           expHANADialect::connectionString( array( 'host' => 'hana.example', 'port' => 30041, 'schema' => 'exp' ) ) );
        $this->assertSame( 'DRIVER={/opt/sap;x/libodbcHDB.so};SERVERNODE=h:443;CHAR_AS_UTF8=TRUE;ENCRYPT=TRUE;sslValidateCertificate=TRUE;PACKETSIZE=1048576',
                           expHANADialect::connectionString( array( 'host' => 'h', 'port' => 443, 'driver' => '/opt/sap;x/libodbcHDB.so', 'encrypt' => true,
                                                                    'validate_certificate' => true, 'extra' => 'PACKETSIZE=1048576;' ) ) );
        $this->assertSame( 'DRIVER=HDBODBC;SERVERNODE=localhost:39041;DATABASENAME=HXE;CHAR_AS_UTF8=TRUE',
                           expHANADialect::connectionString( array( 'tenant' => 'HXE' ) ) );

        $v = expHANADialect::describeVersion( '2.00.088.00.1760424921' );
        $this->assertSame( array( 2, 88, true, 'SAP HANA 2.0 SPS 08' ), array( $v['major'], $v['sps'], $v['supported'], $v['name'] ) );
        $this->assertFalse( expHANADialect::describeVersion( '2.00.048.00' )['supported'] );
        $this->assertSame( 'SAP HANA Cloud', expHANADialect::describeVersion( '4.00.000.00.1730367291' )['name'] );
        $this->assertTrue( expHANADialect::describeVersion( '4.00.000.00.1730367291' )['supported'] );

        $this->assertContains( 'hana', expDBSQLHelpers::DIALECTS );
        $this->assertSame( '"A"."B"', expDBSQLHelpers::quoteIdentifier( 'hana', 'a.b' ) );
        $this->assertSame( "SELECT a FROM t\nLIMIT 3", expDBSQLHelpers::limit( 'hana', 'SELECT a FROM t', 3 ) );
        $this->assertSame( 'SELECT a FROM t FOR UPDATE', expDBSQLHelpers::forUpdate( 'hana', 'SELECT a FROM t' ) );
        $this->assertSame( "ADD_SECONDS(TO_TIMESTAMP('1970-01-01 00:00:00'), x)", expDBSQLHelpers::fromUnixTimestamp( 'hana', 'x' ) );
    }
}
