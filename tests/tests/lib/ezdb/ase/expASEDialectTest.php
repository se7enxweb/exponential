<?php
/**
 * expASEDialect: the SAP ASE Transact-SQL the driver writes, without a server.
 *
 *  AD-01 - Names: brackets, ASE's reserved words, 255 characters; a column named count bracketed where it is a name
 *  AD-02 - Literals without N, functions: CONCAT, LENGTH, SUBSTR, SUBSTRING of two, IFNULL, NOW, MD5, UNIX_TIMESTAMP
 *  AD-03 - LIKE: ESCAPE for a backslash, "[" escaped
 *  AD-04 - Paging: TOP after SELECT [DISTINCT], the offset to skip, a UNION left whole; the kernel's trailing LIMIT
 *  AD-05 - INSERT of several rows split; TRUNCATE as DELETE; temporary tables with ASE's types and NULL written out
 *  AD-06 - Upsert as MERGE, GROUP_CONCAT unsupported, IN lists, version strings, error categories, DSNs
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expASETestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('ase')]
class expASEDialectTest extends PHPUnit\Framework\TestCase
{
    public function testNames()
    {
        $this->assertSame( '[ezsite_data]', expASEDialect::quoteIdentifier( 'ezsite_data' ) );
        $this->assertSame( '[a].[b]', expASEDialect::quoteName( 'a.b' ) );
        $this->assertSame( '[count]', expASEDialect::quoteIdentifierIfNeeded( 'count' ) );
        $this->assertSame( 'value', expASEDialect::quoteIdentifierIfNeeded( 'value' ) );
        $this->assertTrue( expASEDialect::isReservedWord( 'level' ) );
        $this->assertSame( 'SELECT [count] FROM [ezview_counter] WHERE node_id = 2', expASEDialect::rewrite( 'SELECT count FROM `ezview_counter` WHERE node_id = 2' ) );
        $this->assertSame( 'UPDATE ezview_counter SET [count] = [count] + 1', expASEDialect::rewrite( 'UPDATE ezview_counter SET count = count + 1' ) );
        $this->assertSame( 'SELECT COUNT(*) AS [count] FROM t', expASEDialect::rewrite( 'SELECT COUNT(*) AS count FROM t' ) );
        $this->assertSame( '[' . str_repeat( 'a', 255 ) . ']', expASEDialect::quoteIdentifier( str_repeat( 'a', 255 ) ) );
        $this->expectException( InvalidArgumentException::class );
        expASEDialect::quoteIdentifier( str_repeat( 'a', 256 ) );
    }

    public function testLiteralsAndFunctions()
    {
        $this->assertSame( "'it''s'", expASEDialect::stringLiteral( "it's" ) );
        $this->assertSame( "NULL|1|42|1.5|'x'", implode( '|', array_map( array( 'expASEDialect', 'literal' ), array( null, true, 42, 1.5, 'x' ) ) ) );
        $this->assertSame( "SELECT (a + '/' + b) FROM t", expASEDialect::rewrite( "SELECT CONCAT(a, '/', b) FROM t" ) );
        $this->assertSame( 'SELECT char_length(name), substring(name, 1, 3), isnull(x, 0) FROM t',
                           expASEDialect::rewrite( 'SELECT LENGTH(name), SUBSTR(name, 1, 3), IFNULL(x, 0) FROM t' ) );
        $this->assertSame( 'SELECT substring(name, 2, char_length(name)) FROM t', expASEDialect::rewrite( 'SELECT SUBSTRING(name, 2) FROM t' ) );
        $this->assertSame( "SELECT getdate(), lower(hash(name, 'md5')), datediff(ss, '19700101', getutcdate()), dateadd(ss, t, '19700101') FROM t",
                           expASEDialect::rewrite( 'SELECT NOW(), MD5(name), UNIX_TIMESTAMP(), FROM_UNIXTIME(t) FROM t' ) );
        $this->assertSame( "SELECT 'CONCAT(a) LIMIT 1' FROM t", expASEDialect::rewrite( "SELECT 'CONCAT(a) LIMIT 1' FROM t" ) );
    }

    public function testLike()
    {
        $this->assertSame( "SELECT id FROM t WHERE name LIKE 'a\\_b%' ESCAPE '\\'", expASEDialect::rewrite( "SELECT id FROM t WHERE name LIKE 'a\\_b%'" ) );
        $this->assertSame( "SELECT id FROM t WHERE name LIKE '\\[x]%' ESCAPE '\\'", expASEDialect::rewrite( "SELECT id FROM t WHERE name LIKE '[x]%'" ) );
        $this->assertSame( "SELECT id FROM t WHERE name LIKE 'a%'", expASEDialect::rewrite( "SELECT id FROM t WHERE name LIKE 'a%'" ) );
    }

    public function testPaging()
    {
        $this->assertSame( array( 'SELECT TOP 15 id FROM t ORDER BY id', 10, true ), expASEDialect::applyLimit( 'SELECT id FROM t ORDER BY id', 5, 10 ) );
        $this->assertSame( array( 'SELECT DISTINCT TOP 5 id FROM t', 0, true ), expASEDialect::applyLimit( 'SELECT DISTINCT id FROM t;', 5 ) );
        $this->assertSame( array( 'SELECT a FROM t UNION SELECT b FROM u', 2, false ), expASEDialect::applyLimit( 'SELECT a FROM t UNION SELECT b FROM u', 5, 2 ) );
        $this->assertSame( array( 'SELECT id FROM t', 3, true ), expASEDialect::applyLimit( 'SELECT id FROM t', false, 3 ) );
        $this->assertNull( expASEDialect::applyLimit( 'SELECT id FROM t', 0 ) );
        $this->assertSame( array( 'SELECT id FROM t ORDER BY id', 20, 10 ), expASEDialect::trailingLimit( 'SELECT id FROM t ORDER BY id LIMIT 10, 20' ) );
        $this->assertSame( array( 'SELECT id FROM t', 5, 2 ), expASEDialect::trailingLimit( 'SELECT id FROM t LIMIT 5 OFFSET 2' ) );
        $this->assertNull( expASEDialect::trailingLimit( 'SELECT id FROM t WHERE x IN (SELECT y FROM u)' ) );
        $this->assertSame( 'SELECT TOP 3 id FROM t', expASEDialect::limitStatement( 'SELECT id FROM t', 3 ) );
        $this->expectException( expDBUnsupportedException::class );
        expASEDialect::limitStatement( 'SELECT id FROM t', 3, 1 );
    }

    public function testStatements()
    {
        $this->assertSame( array( "INSERT INTO t (a, b) VALUES (1, 'x')", "INSERT INTO t (a, b) VALUES (2, 'y, z')" ),
                           expASEDialect::splitMultiRowInsert( "INSERT INTO t (a, b) VALUES (1, 'x'), (2, 'y, z')" ) );
        $this->assertNull( expASEDialect::splitMultiRowInsert( "INSERT INTO t (a) VALUES (1)" ) );
        $this->assertSame( 'DELETE FROM t', expASEDialect::rewrite( 'TRUNCATE TABLE t' ) );
        $this->assertSame( array( 'CREATE TABLE #ezsearch_tmp_0 ( contentobject_id INT NOT NULL PRIMARY KEY, name UNIVARCHAR(100) NULL, body UNITEXT NULL )', '#ezsearch_tmp_0' ),
                           expASEDialect::temporaryTable( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int primary key not null, name varchar(100), body text )' ) );
        $this->assertSame( "IF OBJECT_ID('tempdb..#x') IS NOT NULL DROP TABLE #x", expASEDialect::dropTemporaryTable( 'x' ) );
        $this->assertSame( 'SELECT a FROM t FOR UPDATE', expASEDialect::forUpdate( 'SELECT a FROM t;' ) );
    }

    public function testHelpersVersionsErrors()
    {
        $this->assertSame( "MERGE INTO [kv] AS t USING (SELECT 'a' AS [name], 1 AS [n]) AS s ON (t.[name] = s.[name]) WHEN MATCHED THEN UPDATE SET [n] = s.[n]"
                         . ' WHEN NOT MATCHED THEN INSERT ([name], [n]) VALUES (s.[name], s.[n])',
                           expASEDialect::upsert( 'kv', array( 'name' => 'a', 'n' => 1 ), array( 'name' ) ) );
        $this->assertSame( "k IN ( 'a', 'b' )", expASEDialect::inList( array( 'a', 'b' ), 'k', false, 'string' ) );
        $this->assertSame( 'id NOT IN ( 1, 2 )', expASEDialect::inList( array( 1, 2 ), 'id', true, 'int' ) );
        $v = expASEDialect::describeVersion( 'Adaptive Server Enterprise/16.0 SP04 PL04/EBF 30399 SMP/P/x86_64' );
        $this->assertSame( array( 16, 0, 4, '16.0 SP04', true ), array( $v['major'], $v['minor'], $v['sp'], $v['string'], $v['supported'] ) );
        $this->assertFalse( expASEDialect::describeVersion( 'Adaptive Server Enterprise/15.5/EBF 18164' )['supported'] );
        $this->assertTrue( expASEDialect::describeVersion( 'Adaptive Server Enterprise/15.7/EBF 21338 SMP SP122' )['supported'] );
        $this->assertSame( 'deadlock', expASEDialect::errorCategory( 1205 ) );
        $this->assertSame( 'duplicate', expASEDialect::errorCategory( 2601, 'Attempt to insert duplicate key row' ) );
        $this->assertSame( 'lock', expASEDialect::errorCategory( 12205 ) );
        $this->assertSame( 'connection', expASEDialect::errorCategory( 20006, 'Write to the server failed' ) );
        $this->assertSame( 'unknown_object', expASEDialect::errorCategory( 208 ) );
        $this->assertSame( 'dblib:host=ase.example:5000;version=5.0;charset=UTF-8;dbname=exp;appname=Exponential',
                           expASEDialect::dblibDSN( array( 'host' => 'ase.example', 'port' => 0, 'database' => 'exp', 'app_name' => 'Exponential' ) ) );
        $this->assertSame( 'odbc:Driver={FreeTDS};Server=h;Port=5001;TDS_Version=5.0;ClientCharset=UTF-8;Database=exp',
                           expASEDialect::odbcDSN( array( 'host' => 'h', 'port' => 5001, 'database' => 'exp' ) ) );
        $this->assertStringContainsString( 'CharSet=utf8', expASEDialect::odbcDSN( array( 'host' => 'h' ), 'Adaptive Server Enterprise' ) );
        $this->assertStringContainsString( 'set quoted_identifier on', expASEDialect::sessionSetup() );
        $this->assertStringContainsString( 'set lock wait 2', expASEDialect::sessionSetup( 1500 ) );
        $this->expectException( expDBUnsupportedException::class );
        expASEDialect::groupConcat( 'name' );
    }
}
