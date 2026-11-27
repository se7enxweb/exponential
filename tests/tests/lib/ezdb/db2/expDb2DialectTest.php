<?php
/**
 * expDb2Dialect and expInformixDialect (no server needed): the Db2 and Informix words of the kernel's statements.
 *
 *  DD-01 - Names: Db2 quotes in upper case (as it stores an unquoted name), MySQL backticks as double quotes, the
 *          special register USER quoted where it is a column; Informix never quotes and folds to lower case;
 *          empty, NUL and over-long names refused
 *  DD-02 - Functions: LENGTH and SUBSTRING count characters, CONCAT of more than two as ||, MD5 of a literal
 *          computed in PHP (HASH() otherwise on Db2), UNIX_TIMESTAMP, CURDATE; nothing inside a literal or a comment
 *  DD-03 - The bit operators of the language masks as BITAND/BITOR/BITNOT; || stays the concatenation
 *  DD-04 - LIKE with a backslash gets ESCAPE '\'; one with its own ESCAPE or without a backslash is left alone
 *  DD-05 - Db2: LIMIT stays, FOR UPDATE becomes the isolation clause that keeps update locks, a SELECT without
 *          FROM reads SYSIBM.SYSDUMMY1, TRUNCATE becomes DELETE; Informix: LIMIT as SKIP/FIRST after SELECT,
 *          FOR UPDATE kept, CREATE TEMPORARY TABLE as CREATE TEMP TABLE ... WITH NO LOG
 *  DD-06 - applyLimit(): Db2's OFFSET/FETCH before a lock clause, a paged statement wrapped, limit 0 is null;
 *          Informix's SKIP/FIRST, DISTINCT after them, a UNION wrapped
 *  DD-07 - Helpers: upsert as MERGE, LISTAGG (Informix: refused), timestamps, IN lists in chunks, temporary tables
 *  DD-08 - Errors by category; DSN; versions; long literals as parameters; the table of an INSERT and a DROP
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDb2TestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('db2')]
class expDb2DialectTest extends PHPUnit\Framework\TestCase
{
    public function testNames()
    {
        $this->assertSame( '"EZUSER"', expDb2Dialect::quoteIdentifier( 'ezuser' ) );
        $this->assertSame( '"A""B"', expDb2Dialect::quoteIdentifier( 'a"b' ) );
        $this->assertSame( '"T"."USER"', expDb2Dialect::quoteName( 't.user' ) );
        $this->assertSame( 'ezuser', expDb2Dialect::quoteIdentifierIfNeeded( 'ezuser' ) );
        $this->assertSame( 'value', expDb2Dialect::quoteIdentifierIfNeeded( 'value' ) );
        $this->assertSame( '"USER"', expDb2Dialect::quoteIdentifierIfNeeded( 'user' ) );
        $this->assertSame( '"MY-TABLE"', expDb2Dialect::quoteIdentifierIfNeeded( 'my-table' ) );
        $this->assertSame( 'SELECT "NAME" FROM "EZUSER"', expDb2Dialect::backticksToQuotes( 'SELECT `name` FROM `ezuser`' ) );
        $this->assertSame( 'SELECT name FROM ezuser', expInformixDialect::backticksToQuotes( 'SELECT `name` FROM `EZUSER`' ) );
        $this->assertSame( 'ezuser', expInformixDialect::quoteIdentifier( 'EZUser' ) );
        foreach ( array( '', "a\0b", str_repeat( 'x', 129 ) ) as $bad )
        {
            try
            {
                expDb2Dialect::quoteIdentifier( $bad );
                $this->fail( 'refused: ' . strlen( $bad ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
        try
        {
            expInformixDialect::quoteIdentifier( 'my-table' );
            $this->fail( 'Informix cannot quote a name' );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertStringContainsString( 'DELIMIDENT', $e->getMessage() );
        }
    }

    public static function db2Cases()
    {
        return array(
            'user column' => array( 'SELECT t.user, user FROM t', 'SELECT t."USER", "USER" FROM t' ),
            'current user kept' => array( 'SELECT CURRENT USER FROM t', 'SELECT CURRENT USER FROM t' ),
            'kernel names stay' => array( 'UPDATE ezview_counter SET count = count + 1 WHERE node_id = 2', 'UPDATE ezview_counter SET count = count + 1 WHERE node_id = 2' ),
            'length' => array( 'SELECT LENGTH(name) FROM t', 'SELECT CHARACTER_LENGTH(name, CODEUNITS32) FROM t' ),
            'substring' => array( 'SELECT SUBSTRING(name, 2, 3), SUBSTR(name, 2) FROM t', 'SELECT SUBSTRING(name, 2, 3, CODEUNITS32), SUBSTRING(name, 2, CODEUNITS32) FROM t' ),
            'substring with units kept' => array( 'SELECT SUBSTRING(name, 2, 3, OCTETS) FROM t', 'SELECT SUBSTRING(name, 2, 3, OCTETS) FROM t' ),
            'concat' => array( "SELECT CONCAT(a, '/', b) FROM t", "SELECT (a || '/' || b) FROM t" ),
            'md5 literal' => array( "SELECT id FROM ezurlalias_ml WHERE text_md5 = MD5('abc')", "SELECT id FROM ezurlalias_ml WHERE text_md5 = '900150983cd24fb0d6963f7d28e17f72'" ),
            'md5 column' => array( 'SELECT MD5(name) FROM t', 'SELECT LOWER(HEX(HASH(name, 0))) FROM t' ),
            'curdate' => array( 'SELECT CURDATE() FROM t', 'SELECT CURRENT DATE FROM t' ),
            'ifnull and now stay' => array( 'SELECT IFNULL(a, 0), NOW() FROM t', 'SELECT IFNULL(a, 0), NOW() FROM t' ),
            'in literal and comment' => array( "SELECT 'LENGTH(x) user' /* user */ FROM t", "SELECT 'LENGTH(x) user' /* user */ FROM t" ),
            'bit and' => array( 'SELECT id FROM ezcontentobject WHERE ezcontentobject.language_mask & 6 > 0', 'SELECT id FROM ezcontentobject WHERE BITAND(ezcontentobject.language_mask, 6) > 0' ),
            'bit or and not' => array( 'SELECT a | 1, ~b FROM t', 'SELECT BITOR(a, 1), BITNOT(b) FROM t' ),
            'concatenation kept' => array( "SELECT a || 'x' FROM t", "SELECT a || 'x' FROM t" ),
            'like escape' => array( "SELECT id FROM t WHERE name LIKE 'a\\_%'", "SELECT id FROM t WHERE name LIKE 'a\\_%' ESCAPE '\\'" ),
            'like own escape' => array( "SELECT id FROM t WHERE name LIKE 'a!_%' ESCAPE '!'", "SELECT id FROM t WHERE name LIKE 'a!_%' ESCAPE '!'" ),
            'like no backslash' => array( "SELECT id FROM t WHERE name LIKE 'a%'", "SELECT id FROM t WHERE name LIKE 'a%'" ),
            'limit stays' => array( 'SELECT id FROM t ORDER BY id LIMIT 10 OFFSET 20', 'SELECT id FROM t ORDER BY id LIMIT 10 OFFSET 20' ),
            'for update' => array( 'SELECT id FROM t WHERE id = 1 ORDER BY id FOR UPDATE', 'SELECT id FROM t WHERE id = 1 ORDER BY id WITH RS USE AND KEEP UPDATE LOCKS' ),
            'no from' => array( 'SELECT 1 AS one', 'SELECT 1 AS one FROM SYSIBM.SYSDUMMY1' ),
            'truncate' => array( 'TRUNCATE TABLE ezsession', 'DELETE FROM ezsession' ),
            'truncate immediate' => array( 'TRUNCATE TABLE ezsession IMMEDIATE', 'DELETE FROM ezsession' ),
            'ddl backticks only' => array( 'CREATE TABLE `t` (`user` INTEGER)', 'CREATE TABLE "T" ("USER" INTEGER)' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('db2Cases')]
    public function testDb2Rewrite( $sql, $expected )
    {
        $this->assertSame( $expected, expDb2Dialect::rewrite( $sql ) );
        // idempotent
        $this->assertSame( $expected, expDb2Dialect::rewrite( $expected ) );
    }

    public static function informixCases()
    {
        return array(
            'names stay' => array( 'SELECT t.value, count, user FROM t', 'SELECT t.value, count, user FROM t' ),
            'length' => array( 'SELECT LENGTH(name) FROM t', 'SELECT CHAR_LENGTH(name) FROM t' ),
            'substring' => array( 'SELECT SUBSTRING(name, 2, 3) FROM t', 'SELECT SUBSTRING(name FROM 2 FOR 3) FROM t' ),
            'ifnull now curdate' => array( 'SELECT IFNULL(a, 0), NOW(), CURDATE() FROM t', 'SELECT NVL(a, 0), CURRENT YEAR TO SECOND, TODAY FROM t' ),
            'md5 literal' => array( "SELECT id FROM t WHERE m = MD5('abc')", "SELECT id FROM t WHERE m = '900150983cd24fb0d6963f7d28e17f72'" ),
            'bit and' => array( 'SELECT id FROM t WHERE t.mask & 2 > 0', 'SELECT id FROM t WHERE BITAND(t.mask, 2) > 0' ),
            'limit' => array( 'SELECT id FROM t ORDER BY id LIMIT 10', 'SELECT FIRST 10 id FROM t ORDER BY id' ),
            'limit offset' => array( 'SELECT DISTINCT id FROM t ORDER BY id LIMIT 10 OFFSET 20', 'SELECT SKIP 20 FIRST 10 DISTINCT id FROM t ORDER BY id' ),
            'limit comma' => array( 'SELECT id FROM t LIMIT 20, 10', 'SELECT SKIP 20 FIRST 10 id FROM t' ),
            'for update kept' => array( 'SELECT id FROM t WHERE id = 1 FOR UPDATE', 'SELECT id FROM t WHERE id = 1 FOR UPDATE' ),
            'no from kept' => array( 'SELECT 1 AS one', 'SELECT 1 AS one' ),
            'temporary table' => array( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null )',
                                        'CREATE TEMP TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL PRIMARY KEY ) WITH NO LOG' ),
            'truncate' => array( 'TRUNCATE TABLE ezsession', 'DELETE FROM ezsession' ),
            'counter table' => array( 'INSERT INTO ezurlalias_ml_incr(id) VALUES(NULL)', 'INSERT INTO ezurlalias_ml_incr (id) VALUES (0)' ),
            'like on a clob' => array( "SELECT id FROM ezcontentobject_attribute a WHERE a.data_text LIKE '%x%' AND data_text NOT LIKE 'y%' AND name LIKE 'z%'",
                                       "SELECT id FROM ezcontentobject_attribute a WHERE CAST(a.data_text AS LVARCHAR(32739)) LIKE '%x%' AND CAST(data_text AS LVARCHAR(32739)) NOT LIKE 'y%' AND name LIKE 'z%'" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('informixCases')]
    public function testInformixRewrite( $sql, $expected )
    {
        $this->assertSame( $expected, expInformixDialect::rewrite( $sql ) );
    }

    public function testApplyLimit()
    {
        $this->assertSame( "SELECT id FROM t ORDER BY id\nOFFSET 20 ROWS FETCH FIRST 10 ROWS ONLY", expDb2Dialect::applyLimit( 'SELECT id FROM t ORDER BY id', 10, 20 ) );
        $this->assertSame( "SELECT id FROM t\nOFFSET 5 ROWS", expDb2Dialect::applyLimit( 'SELECT id FROM t', false, 5 ) );
        $this->assertSame( "SELECT id FROM t\nOFFSET 0 ROWS FETCH FIRST 1 ROWS ONLY WITH RS USE AND KEEP UPDATE LOCKS",
                           expDb2Dialect::applyLimit( 'SELECT id FROM t WITH RS USE AND KEEP UPDATE LOCKS', 1 ) );
        $this->assertSame( "SELECT * FROM (\nSELECT id FROM t LIMIT 3\n) exp_page\nOFFSET 1 ROWS FETCH FIRST 2 ROWS ONLY", expDb2Dialect::applyLimit( 'SELECT id FROM t LIMIT 3', 2, 1 ) );
        $this->assertNull( expDb2Dialect::applyLimit( 'SELECT id FROM t', 0 ) );
        $this->assertSame( 'SELECT id FROM t', expDb2Dialect::applyLimit( 'SELECT id FROM t;', false, 0 ) );
        $this->assertSame( 'DELETE FROM t', expDb2Dialect::applyLimit( 'DELETE FROM t', 5 ) );

        $this->assertSame( 'SELECT SKIP 20 FIRST 10 id FROM t ORDER BY id', expInformixDialect::applyLimit( 'SELECT id FROM t ORDER BY id', 10, 20 ) );
        $this->assertSame( 'SELECT FIRST 1 id FROM t FOR UPDATE', expInformixDialect::applyLimit( 'SELECT id FROM t FOR UPDATE', 1 ) );
        $this->assertSame( "SELECT FIRST 2 * FROM (\nSELECT a FROM t UNION SELECT b FROM u\n) exp_page", expInformixDialect::applyLimit( 'SELECT a FROM t UNION SELECT b FROM u', 2 ) );
        $this->assertNull( expInformixDialect::applyLimit( 'SELECT id FROM t', 0 ) );
    }

    public function testHelpers()
    {
        $this->assertSame( "MERGE INTO \"KV\" t USING (VALUES ('a', 'it''s')) s (\"NAME\", \"VALUE\") ON t.\"NAME\" = s.\"NAME\""
                         . " WHEN MATCHED THEN UPDATE SET \"VALUE\" = s.\"VALUE\" WHEN NOT MATCHED THEN INSERT (\"NAME\", \"VALUE\") VALUES (s.\"NAME\", s.\"VALUE\")",
                           expDb2Dialect::upsert( 'kv', array( 'name' => 'a', 'value' => "it's" ), array( 'name' ) ) );
        $this->assertStringContainsString( 'USING (SELECT 1 AS n FROM systables WHERE tabid = 1) s ON (t.n = s.n)',
                                           expInformixDialect::upsert( 'kv', array( 'n' => 1 ), array( 'n' ), array() ) );
        $this->assertSame( "LISTAGG(CAST(name AS VARCHAR(32000)), '|') WITHIN GROUP (ORDER BY name)", expDb2Dialect::groupConcat( 'name', '|', 'name' ) );
        try
        {
            expInformixDialect::groupConcat( 'name' );
            $this->fail( 'Informix has no LISTAGG' );
        }
        catch ( expDBUnsupportedException $e )
        {
            $this->assertStringContainsString( 'GROUP_CONCAT', $e->getMessage() );
        }
        $this->assertSame( 'BIGINT(EXTRACT(EPOCH FROM CURRENT TIMESTAMP - CURRENT TIMEZONE))', expDb2Dialect::unixTimestamp() );
        $this->assertSame( "(TIMESTAMP('1970-01-01-00.00.00') + (5) SECONDS)", expDb2Dialect::fromUnixTimestamp( '5' ) );
        $this->assertSame( "DBINFO('utc_current')", expInformixDialect::unixTimestamp() );
        $this->assertSame( "DBINFO('utc_to_datetime', 5)", expInformixDialect::fromUnixTimestamp( '5' ) );
        $this->assertSame( 'SELECT id FROM t WITH RS USE AND KEEP UPDATE LOCKS', expDb2Dialect::forUpdate( 'SELECT id FROM t;' ) );
        $this->assertSame( 'SELECT id FROM t FOR UPDATE', expInformixDialect::forUpdate( 'SELECT id FROM t' ) );
        $list = expDb2Dialect::inList( range( 1, 2500 ), 'id', false, 'int', 1000 );
        $this->assertSame( 3, substr_count( $list, ' IN ( ' ) );
        $this->assertStringStartsWith( '( id IN ( 1, 2', $list );
        $this->assertSame( '1 = 0', expDb2Dialect::inList( array(), 'id' ) );
        $this->assertSame( "name NOT IN ( 'a''b' )", expDb2Dialect::inList( array( "a'b" ), 'name', true, 'string' ) );
        try
        {
            expInformixDialect::md5( 'name' );
            $this->fail( 'Informix has no MD5 of a column' );
        }
        catch ( expDBUnsupportedException $e )
        {
            $this->assertStringContainsString( 'MD5', $e->getMessage() );
        }
        $this->assertSame( "'" . md5( "it's" ) . "'", expInformixDialect::md5( "'it''s'" ) );
    }

    public function testTemporaryTables()
    {
        $t = expDb2Dialect::temporaryTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, user int, t longtext )' );
        $this->assertSame( 'ezsearch_tmp_0', $t['name'] );
        $this->assertSame( array( 'contentobject_id', 'user', 't' ), $t['columns'] );
        $this->assertSame( 'CREATE GLOBAL TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id INTEGER NOT NULL, "USER" INTEGER, t VARCHAR(8000) ) ON COMMIT PRESERVE ROWS NOT LOGGED', $t['sql'] );
        $this->assertNull( expDb2Dialect::temporaryTable( 'CREATE TABLE t (id int)' ) );
        $i = expInformixDialect::temporaryTable( 'CREATE TEMPORARY TABLE x (t text)' );
        $this->assertSame( 'CREATE TEMP TABLE x ( t LVARCHAR(8000) ) WITH NO LOG', $i['sql'] );
    }

    public function testErrorsDsnVersions()
    {
        $this->assertSame( 'deadlock', expDb2Dialect::errorCategory( -911, '40001', 'SQL0911N deadlock or timeout' ) );
        $this->assertSame( 'lock', expDb2Dialect::errorCategory( -913, '57033' ) );
        $this->assertSame( 'duplicate', expDb2Dialect::errorCategory( -803, '23505' ) );
        $this->assertSame( 'connection', expDb2Dialect::errorCategory( -30081, '08001', 'A communication error' ) );
        $this->assertSame( 'unknown_object', expDb2Dialect::errorCategory( -204, '42704' ) );
        $this->assertSame( 'other', expDb2Dialect::errorCategory( -1, 'HY000' ) );
        $this->assertSame( 'deadlock', expInformixDialect::errorCategory( -143 ) );
        $this->assertSame( 'lock', expInformixDialect::errorCategory( -244 ) );
        $this->assertSame( 'duplicate', expInformixDialect::errorCategory( -268 ) );
        $this->assertSame( 'syntax', expInformixDialect::errorCategory( -201 ) );

        $this->assertSame( 'ibm:DATABASE=EXP;HOSTNAME=db.example;PORT=50001;PROTOCOL=TCPIP;SECURITY=SSL;CURRENTSCHEMA=SITE;',
                           expDb2Dialect::pdoDSN( array( 'host' => 'db.example', 'port' => 50001, 'database' => 'EXP', 'security' => 'SSL', 'extra' => 'CURRENTSCHEMA=SITE' ) ) );
        $this->assertSame( 'ibm:DATABASE=EXP;', expDb2Dialect::pdoDSN( array( 'database' => 'EXP' ) ) );
        $this->assertSame( 'ibm:DATABASE=EXP;StmtConcentrator=WITHLITERALS;', expDb2Dialect::pdoDSN( array( 'database' => 'EXP', 'concentrator' => true ) ) );
        $this->assertSame( 'odbc:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=EXP;HOSTNAME=h;PORT=50000;PROTOCOL=TCPIP;', expDb2Dialect::pdoDSN( array( 'layer' => 'pdo_odbc', 'host' => 'h', 'database' => 'EXP' ) ) );
        $this->assertSame( 'ibm:DATABASE=exp;HOSTNAME=h;PORT=9089;PROTOCOL=TCPIP;', expInformixDialect::pdoDSN( array( 'host' => 'h', 'database' => 'exp' ) ) );
        try
        {
            expDb2Dialect::pdoDSN( array( 'database' => 'a;b' ) );
            $this->fail( 'refused' );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertStringContainsString( ';', $e->getMessage() );
        }

        $v = expDb2Dialect::describeVersion( '12.01.0600' );
        $this->assertSame( array( 12, 1, true, 'Db2 12.1' ), array( $v['major'], $v['minor'], $v['supported'], $v['name'] ) );
        $this->assertTrue( expDb2Dialect::describeVersion( 'DB2 v11.5.9.0' )['supported'] );
        $this->assertFalse( expDb2Dialect::describeVersion( 'SQL11014' )['supported'] );
        $i = expInformixDialect::describeVersion( 'IBM Informix Dynamic Server Version 15.0.1.0.3DE' );
        $this->assertSame( array( 15, 0, true, 'Informix 15.0' ), array( $i['major'], $i['minor'], $i['supported'], $i['name'] ) );
        $this->assertFalse( expInformixDialect::describeVersion( 'IBM Informix Dynamic Server Version 12.10.FC15' )['supported'] );
    }

    public function testStatementsAndLongLiterals()
    {
        $this->assertSame( 'ezcontentobject', expDb2Dialect::insertTable( 'INSERT INTO ezcontentobject (name) VALUES (1)' ) );
        $this->assertSame( 'ezuser', expDb2Dialect::insertTable( 'INSERT INTO "EZUSER" (id) VALUES (1)' ) );
        $this->assertNull( expDb2Dialect::insertTable( 'UPDATE t SET a = 1' ) );
        $this->assertSame( 'ezuser', expDb2Dialect::droppedTable( 'DROP TABLE ezuser' ) );
        $this->assertSame( 'ezuser', expDb2Dialect::droppedTable( 'DROP TABLE IF EXISTS ezuser' ) );
        $this->assertNull( expDb2Dialect::droppedTable( 'DROP INDEX ezuser' ) );
        $this->assertTrue( expDb2Dialect::isSchemaChange( 'ALTER TABLE t ADD c INTEGER' ) );
        $long = str_repeat( 'x', 20000 );
        $bound = expDb2Dialect::parameterizeLongLiterals( "INSERT INTO t (a, b) VALUES ('short', '$long')" );
        $this->assertSame( array( "INSERT INTO t (a, b) VALUES ('short', ?)", array( $long ) ), $bound );
        $this->assertNull( expDb2Dialect::parameterizeLongLiterals( "INSERT INTO t (a) VALUES ('short')" ) );
        $this->assertNull( expDb2Dialect::parameterizeLongLiterals( "INSERT INTO t (a, b) VALUES (?, '$long')" ) );
        $this->assertSame( "SELECT 1 AS found FROM SYSCAT.SEQUENCES WHERE SEQSCHEMA = CURRENT SCHEMA AND SEQNAME = 'S_EZUSER'", expDb2Dialect::existsQuery( 'SEQUENCE', 's_ezuser' ) );
        $this->assertSame( 4294967295 & 1, expDb2PDOTransport::rowCount( -4294967295 ) );
        $this->assertSame( -1, expDb2PDOTransport::rowCount( -1 ) );
    }
}
