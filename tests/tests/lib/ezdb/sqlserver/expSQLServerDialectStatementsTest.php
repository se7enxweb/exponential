<?php
/**
 * expSQLServerDialect statements (no server needed):
 *
 *  ST-01 - statementKind(): the first keyword, the statement after WITH's CTEs, "(SELECT" as SELECT
 *  ST-02 - isReplicaReadable(): only one plain SELECT; never SELECT INTO, locking hints, #temp tables, sequences,
 *          session values or several statements. The old extension sent every statement with "select" anywhere in
 *          it to the read connection (bug 6): an UPDATE with a subquery, an INSERT ... SELECT
 *  ST-03 - parseInsert(): table, columns, rows of values (functions, nested parentheses, literals with commas)
 *  ST-04 - rewriteIdentityInsert(): NULL or 0 for the identity column leaves it out (one row or many; DEFAULT VALUES
 *          when it is the only column); a real value asks for IDENTITY_INSERT (the old extension switched it on
 *          around every INSERT, bug 4)
 *  ST-05 - temporaryTable(): CREATE TEMPORARY TABLE x becomes CREATE TABLE #x (the old extension made a permanent
 *          table, bug 8); dropTemporaryTable() never fails on a table already gone
 *  ST-06 - isSchemaChange(), isSingleStatement()
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerDialectStatementsTest extends PHPUnit\Framework\TestCase
{
    public static function kinds()
    {
        return array(
            array( 'SELECT 1', 'SELECT' ), array( '  select 1', 'SELECT' ), array( "\n\tInsert into t values (1)", 'INSERT' ),
            array( 'UPDATE t SET a = 1', 'UPDATE' ), array( 'DELETE FROM t', 'DELETE' ), array( 'MERGE t USING s ON 1=1 WHEN MATCHED THEN DELETE;', 'MERGE' ),
            array( 'CREATE TABLE t (a INT)', 'CREATE' ), array( 'ALTER TABLE t ADD b INT', 'ALTER' ), array( 'DROP TABLE t', 'DROP' ),
            array( 'TRUNCATE TABLE t', 'TRUNCATE' ), array( 'EXEC sp_who', 'EXEC' ), array( 'EXECUTE sp_who', 'EXEC' ),
            array( 'BEGIN TRANSACTION', 'BEGIN' ), array( 'COMMIT', 'COMMIT' ), array( 'SET NOCOUNT ON', 'SET' ),
            array( 'DECLARE @a INT', 'DECLARE' ), array( 'WITH c AS (SELECT 1 AS a) SELECT a FROM c', 'SELECT' ),
            array( 'WITH c AS (SELECT 1 AS a) DELETE FROM t WHERE a IN (SELECT a FROM c)', 'DELETE' ),
            array( 'WITH c AS (SELECT 1 AS a), d AS (SELECT 2 AS b) UPDATE t SET a = 1', 'UPDATE' ),
            array( 'WITH c (a) AS (SELECT 1) INSERT INTO t SELECT a FROM c', 'INSERT' ),
            array( '(SELECT 1)', 'SELECT' ), array( '((SELECT 1))', 'SELECT' ), array( '/* c */ SELECT 1', 'SELECT' ),
            array( "-- c\nUPDATE t SET a = 1", 'UPDATE' ), array( '', '' ), array( '   ', '' ), array( ';', '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('kinds')]
    public function testStatementKind( $sql, $kind )
    {
        $this->assertSame( $kind, expSQLServerDialect::statementKind( $sql ) );
    }

    public static function replicaReadable()
    {
        return array(
            'plain select' => array( 'SELECT * FROM ezcontentobject WHERE id = 1', true ),
            'join' => array( 'SELECT a.id FROM a JOIN b ON a.id = b.id', true ),
            'cte select' => array( 'WITH c AS (SELECT 1 AS a) SELECT a FROM c', true ),
            'nolock is fine' => array( 'SELECT * FROM t WITH (NOLOCK)', true ),
            'literal with into' => array( "SELECT 'INSERT INTO t' AS s", true ),
            'comment with update' => array( "SELECT 1 -- UPDATE t\n", true ),
            'trailing semicolon' => array( 'SELECT 1;', true ),
            'count' => array( 'SELECT COUNT(*) FROM t', true ),
            'select into' => array( 'SELECT * INTO u FROM t', false ),
            'select into temp' => array( 'SELECT * INTO #u FROM t', false ),
            'temp table read' => array( 'SELECT * FROM #t', false ),
            'bracketed temp table' => array( 'SELECT * FROM [#t]', false ),
            'global temp table' => array( 'SELECT * FROM ##g', false ),
            'updlock' => array( 'SELECT * FROM t WITH (UPDLOCK)', false ),
            'holdlock' => array( 'SELECT * FROM t WITH (HOLDLOCK, ROWLOCK)', false ),
            'xlock' => array( 'SELECT * FROM t WITH (XLOCK)', false ),
            'tablockx' => array( 'SELECT TOP (0) 1 FROM t WITH (TABLOCKX, HOLDLOCK)', false ),
            'serializable hint' => array( 'SELECT * FROM t WITH (SERIALIZABLE)', false ),
            'sequence' => array( 'SELECT NEXT VALUE FOR s', false ),
            'scope identity' => array( 'SELECT SCOPE_IDENTITY()', false ),
            'identity global' => array( 'SELECT @@IDENTITY', false ),
            'spid' => array( 'SELECT @@SPID', false ),
            'trancount' => array( 'SELECT @@TRANCOUNT', false ),
            'two statements' => array( 'SELECT 1; SELECT 2', false ),
            'update with subselect (bug 6)' => array( 'UPDATE t SET a = (SELECT MAX(a) FROM u)', false ),
            'insert select (bug 6)' => array( 'INSERT INTO t SELECT * FROM u', false ),
            'delete with select (bug 6)' => array( 'DELETE FROM t WHERE id IN (SELECT id FROM u)', false ),
            'cte delete' => array( 'WITH c AS (SELECT 1 AS a) DELETE FROM t', false ),
            'exec' => array( 'EXEC sp_who', false ),
            'applock' => array( "SELECT 1; EXEC sp_getapplock 'x'", false ),
            'set' => array( 'SET NOCOUNT ON', false ),
            'begin' => array( 'BEGIN TRANSACTION', false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('replicaReadable')]
    public function testIsReplicaReadable( $sql, $readable )
    {
        $this->assertSame( $readable, expSQLServerDialect::isReplicaReadable( $sql ) );
        $this->assertSame( $readable, expSQLServerDB::isReplicaReadable( $sql ) );
    }

    public function testParseInsertWithColumnsAndRows()
    {
        $parsed = expSQLServerDialect::parseInsert( "INSERT INTO [dbo].[ezuser] (id, login, email) VALUES (NULL, 'a,b', LOWER('X')), (0, 'c', 'd')" );
        $this->assertSame( 'ezuser', $parsed['table'] );
        $this->assertSame( '[dbo].[ezuser]', $parsed['tableText'] );
        $this->assertSame( array( 'id', 'login', 'email' ), $parsed['columns'] );
        $this->assertCount( 2, $parsed['rows'] );
        $values = array();
        foreach ( $parsed['rows'][0] as $range )
            $values[] = expSQLServerLexer::join( array_slice( $parsed['tokens'], $range[0], $range[1] - $range[0] + 1 ) );
        $this->assertSame( array( 'NULL', "'a,b'", "LOWER('X')" ), $values );
    }

    public function testParseInsertWithoutInto()
    {
        $parsed = expSQLServerDialect::parseInsert( 'INSERT t (a) VALUES (1)' );
        $this->assertSame( 't', $parsed['table'] );
    }

    public function testParseInsertSelectHasNoRows()
    {
        $parsed = expSQLServerDialect::parseInsert( 'INSERT INTO t (a) SELECT a FROM u' );
        $this->assertSame( array( 'a' ), $parsed['columns'] );
        $this->assertSame( array(), $parsed['rows'] );
    }

    public function testParseInsertOfNoInsert()
    {
        $this->assertNull( expSQLServerDialect::parseInsert( 'SELECT 1' ) );
        $this->assertNull( expSQLServerDialect::insertTable( 'UPDATE t SET a = 1' ) );
    }

    public function testParseInsertNestedParentheses()
    {
        $parsed = expSQLServerDialect::parseInsert( 'INSERT INTO t (a, b) VALUES (COALESCE((1), 2), (3))' );
        $this->assertCount( 2, $parsed['rows'][0] );
    }

    public static function identityRewrites()
    {
        return array(
            'null first' => array( "INSERT INTO t (id, name) VALUES (NULL, 'a')", "INSERT INTO t (name) VALUES ('a')", false ),
            'zero first' => array( "INSERT INTO t (id, name) VALUES (0, 'a')", "INSERT INTO t (name) VALUES ('a')", false ),
            'quoted zero' => array( "INSERT INTO t (id, name) VALUES ('0', 'a')", "INSERT INTO t (name) VALUES ('a')", false ),
            'empty string' => array( "INSERT INTO t (id, name) VALUES ('', 'a')", "INSERT INTO t (name) VALUES ('a')", false ),
            'null last' => array( "INSERT INTO t (name, id) VALUES ('a', NULL)", "INSERT INTO t (name) VALUES ('a')", false ),
            'null middle' => array( "INSERT INTO t (a, id, b) VALUES (1, NULL, 2)", 'INSERT INTO t (a, b) VALUES (1, 2)', false ),
            'lower case null' => array( "INSERT INTO t (id, a) VALUES (null, 1)", 'INSERT INTO t (a) VALUES (1)', false ),
            'case of column' => array( "INSERT INTO t (ID, a) VALUES (NULL, 1)", 'INSERT INTO t (a) VALUES (1)', false ),
            'bracketed column' => array( "INSERT INTO t ([id], a) VALUES (NULL, 1)", 'INSERT INTO t (a) VALUES (1)', false ),
            'several rows' => array( "INSERT INTO t (id, a) VALUES (NULL, 1), (0, 2)", 'INSERT INTO t (a) VALUES (1), (2)', false ),
            'only column' => array( 'INSERT INTO t (id) VALUES (NULL)', 'INSERT INTO t DEFAULT VALUES', false ),
            'explicit id' => array( "INSERT INTO t (id, a) VALUES (42, 1)", "INSERT INTO t (id, a) VALUES (42, 1)", true ),
            'mixed rows' => array( "INSERT INTO t (id, a) VALUES (NULL, 1), (5, 2)", "INSERT INTO t (id, a) VALUES (NULL, 1), (5, 2)", true ),
            'expression' => array( "INSERT INTO t (id, a) VALUES (1+1, 1)", "INSERT INTO t (id, a) VALUES (1+1, 1)", true ),
            'insert select' => array( 'INSERT INTO t (id, a) SELECT id, a FROM u', 'INSERT INTO t (id, a) SELECT id, a FROM u', true ),
            'no identity column named' => array( "INSERT INTO t (a, b) VALUES (1, 2)", 'INSERT INTO t (a, b) VALUES (1, 2)', false ),
            'no column list' => array( "INSERT INTO t VALUES (NULL, 1)", 'INSERT INTO t VALUES (NULL, 1)', false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('identityRewrites')]
    public function testRewriteIdentityInsert( $sql, $expected, $identityInsert )
    {
        $result = expSQLServerDialect::rewriteIdentityInsert( $sql, 'id' );
        $this->assertSame( $expected, $result['sql'] );
        $this->assertSame( $identityInsert, $result['identity_insert'] );
    }

    public static function temporaryTables()
    {
        return array(
            array( 'CREATE TEMPORARY TABLE ezsearch_tmp_0 ( contentobject_id int )', 'CREATE TABLE #ezsearch_tmp_0 ( contentobject_id int )', '#ezsearch_tmp_0' ),
            array( 'create temporary table x (a int)', 'create table #x (a int)', '#x' ),
            array( 'CREATE TEMPORARY TABLE #x (a int)', 'CREATE TABLE #x (a int)', '#x' ),
            array( 'CREATE TABLE #x (a int)', 'CREATE TABLE #x (a int)', '#x' ),
            array( 'CREATE TEMPORARY TABLE IF NOT EXISTS x (a int)', 'CREATE TABLE #x (a int)', '#x' ),
            array( 'CREATE TEMPORARY TABLE [x] (a int)', 'CREATE TABLE #x (a int)', '#x' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('temporaryTables')]
    public function testTemporaryTable( $sql, $expected, $name )
    {
        $this->assertSame( array( $expected, $name ), expSQLServerDialect::temporaryTable( $sql ) );
    }

    public function testPermanentTableIsNoTemporaryTable()
    {
        $this->assertNull( expSQLServerDialect::temporaryTable( 'CREATE TABLE x (a int)' ) );
        $this->assertNull( expSQLServerDialect::temporaryTable( 'SELECT 1' ) );
        $this->assertNull( expSQLServerDialect::temporaryTable( 'CREATE INDEX i ON t (a)' ) );
    }

    public function testTemporaryTableNameTooLong()
    {
        $this->expectException( InvalidArgumentException::class );
        expSQLServerDialect::temporaryTable( 'CREATE TEMPORARY TABLE ' . str_repeat( 'x', 120 ) . ' (a int)' );
    }

    public function testDropTemporaryTable()
    {
        $this->assertSame( "IF OBJECT_ID(N'tempdb..#x') IS NOT NULL DROP TABLE #x", expSQLServerDialect::dropTemporaryTable( 'x' ) );
        $this->assertSame( "IF OBJECT_ID(N'tempdb..#x') IS NOT NULL DROP TABLE #x", expSQLServerDialect::dropTemporaryTable( '#x' ) );
    }

    public static function schemaChanges()
    {
        return array( array( 'CREATE TABLE t (a INT)', true ), array( 'ALTER TABLE t ADD b INT', true ), array( 'DROP TABLE t', true ),
                      array( 'TRUNCATE TABLE t', true ), array( "EXEC sp_rename 't', 'u'", true ), array( 'SELECT 1', false ),
                      array( 'INSERT INTO t VALUES (1)', false ), array( 'EXEC sp_who', false ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('schemaChanges')]
    public function testIsSchemaChange( $sql, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::isSchemaChange( $sql ) );
    }

    public static function singleStatements()
    {
        return array( array( 'SELECT 1', true ), array( 'SELECT 1;', true ), array( 'SELECT 1;;', true ), array( 'SELECT 1; SELECT 2', false ),
                      array( "SELECT ';'", true ), array( 'SELECT 1 -- ; SELECT 2', true ),
                      array( 'MERGE t USING s ON (1=1) WHEN MATCHED THEN DELETE;', true ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('singleStatements')]
    public function testIsSingleStatement( $sql, $expected )
    {
        $this->assertSame( $expected, expSQLServerDialect::isSingleStatement( $sql ) );
    }
}
