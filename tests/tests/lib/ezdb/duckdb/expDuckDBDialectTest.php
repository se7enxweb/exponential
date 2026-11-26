<?php
/**
 * The DuckDB dialect (lib/ezdb/classes/expduckdbdialect.php) and the portable SQL helpers of the duckdb dialect, without
 * a database:
 *
 *  DD-01 - firstWord(): after comments and parentheses, upper case
 *  DD-02 - isWrite(): reads (SELECT, SHOW, PRAGMA, VALUES, FROM ...), settings and transaction control are no writes;
 *          INSERT, UPDATE, DELETE, CREATE (TEMP too), DROP, ALTER, COPY are; WITH only with a data change inside
 *  DD-03 - rewrite(): FOR UPDATE / FOR SHARE / LOCK IN SHARE MODE left out, LOCK TABLE not sent
 *  DD-04 - rewrite(): INSERT IGNORE, REPLACE INTO, `names`, LIMIT offset, count; literals never changed
 *  DD-05 - blankLiterals(): same length, quotes kept, '' inside a literal, null for one that does not end
 *  DD-06 - escape(): quotes doubled, NUL dropped, backslash kept, null as ''
 *  DD-07 - errorCode(): the SQLSTATE PostgreSQL gives the same error
 *  DD-08 - isLockError() and isReadOnlyError()
 *  DD-09 - expDBSQLHelpers for duckdb: LIMIT/OFFSET (no LIMIT ALL), FOR UPDATE left out, ON CONFLICT, STRING_AGG,
 *          EXTRACT(EPOCH), quoting
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDuckDBTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('duckdb')]
class expDuckDBDialectTest extends PHPUnit\Framework\TestCase
{
    /** DD-01 */
    public function testFirstWord()
    {
        $this->assertSame( 'SELECT', expDuckDBDialect::firstWord( "  -- a comment\n /* block */ (select 1)" ) );
        $this->assertSame( 'INSERT', expDuckDBDialect::firstWord( "insert into t values (1)" ) );
        $this->assertSame( '', expDuckDBDialect::firstWord( '   ' ) );
        $this->assertSame( '', expDuckDBDialect::firstWord( '/* never ends' ) );
    }

    /** DD-02 */
    public function testIsWrite()
    {
        foreach ( array( 'SELECT 1', 'show tables', 'PRAGMA table_info(t)', 'VALUES (1)', 'FROM t', 'DESCRIBE t', 'EXPLAIN SELECT 1',
                         'SET integer_division = true', 'RESET memory_limit', 'BEGIN TRANSACTION', 'COMMIT', 'ROLLBACK',
                         "WITH x AS (SELECT 'insert' AS w) SELECT * FROM x" ) as $sql )
            $this->assertFalse( expDuckDBDialect::isWrite( $sql ), $sql );
        foreach ( array( 'INSERT INTO t VALUES (1)', 'update t set a = 1', 'DELETE FROM t', 'CREATE TABLE t (a int)',
                         'CREATE TEMPORARY TABLE t (a int)', 'DROP TABLE t', 'ALTER TABLE t ADD COLUMN b int', "COPY t FROM 'x.csv'",
                         'CHECKPOINT', 'WITH x AS (SELECT 1) INSERT INTO t SELECT * FROM x' ) as $sql )
            $this->assertTrue( expDuckDBDialect::isWrite( $sql ), $sql );
        $this->assertTrue( expDuckDBDialect::isSessionStatement( ' SET threads = 2' ) );
        $this->assertFalse( expDuckDBDialect::isSessionStatement( 'SELECT 1' ) );
    }

    /** DD-03 */
    public function testLocksAreLeftOut()
    {
        $this->assertSame( 'SELECT * FROM t WHERE id = 1', expDuckDBDialect::rewrite( 'SELECT * FROM t WHERE id = 1 FOR UPDATE' ) );
        $this->assertSame( 'SELECT * FROM t', expDuckDBDialect::rewrite( "SELECT * FROM t FOR UPDATE NOWAIT;" ) );
        $this->assertSame( 'SELECT * FROM t', expDuckDBDialect::rewrite( 'SELECT * FROM t FOR SHARE SKIP LOCKED' ) );
        $this->assertSame( 'SELECT * FROM t', expDuckDBDialect::rewrite( 'SELECT * FROM t LOCK IN SHARE MODE' ) );
        $this->assertSame( "SELECT 'x FOR UPDATE' AS a", expDuckDBDialect::rewrite( "SELECT 'x FOR UPDATE' AS a" ) );
        $this->assertNull( expDuckDBDialect::rewrite( 'LOCK TABLE ezcontentobject' ) );
        $this->assertNull( expDuckDBDialect::rewrite( 'UNLOCK TABLES' ) );
    }

    /** DD-04 */
    public function testMySQLWords()
    {
        $this->assertSame( 'INSERT OR IGNORE INTO t (a) VALUES (1)', expDuckDBDialect::rewrite( 'INSERT IGNORE INTO t (a) VALUES (1)' ) );
        $this->assertSame( 'INSERT OR REPLACE INTO t (a) VALUES (1)', expDuckDBDialect::rewrite( 'REPLACE INTO t (a) VALUES (1)' ) );
        $this->assertSame( 'SELECT "a" FROM "t" WHERE b = \'`x`\'', expDuckDBDialect::rewrite( 'SELECT `a` FROM `t` WHERE b = \'`x`\'' ) );
        $this->assertSame( 'SELECT a FROM t ORDER BY a LIMIT 10 OFFSET 20', expDuckDBDialect::rewrite( 'SELECT a FROM t ORDER BY a LIMIT 20, 10' ) );
        $this->assertSame( "SELECT 'LIMIT 1, 2' AS a", expDuckDBDialect::rewrite( "SELECT 'LIMIT 1, 2' AS a" ) );
        $this->assertSame( 'UPDATE t SET a = 1', expDuckDBDialect::rewrite( 'UPDATE t SET a = 1' ) );
        $this->assertSame( "SELECT 'it''s' AS a", expDuckDBDialect::rewrite( "SELECT 'it''s' AS a" ) );
    }

    /** DD-05 */
    public function testBlankLiterals()
    {
        $sql = "SELECT 'a''b', \"c\"\"d\" FROM t";
        $blank = expDuckDBDialect::blankLiterals( $sql );
        $this->assertSame( strlen( $sql ), strlen( $blank ) );
        $this->assertSame( "SELECT '    ', \"    \" FROM t", $blank );
        $this->assertNull( expDuckDBDialect::blankLiterals( "SELECT 'open" ) );
    }

    /** DD-06 */
    public function testEscape()
    {
        $this->assertSame( "it''s", expDuckDBDialect::escape( "it's" ) );
        $this->assertSame( 'ab', expDuckDBDialect::escape( "a\0b" ) );
        $this->assertSame( 'C:\\path\\', expDuckDBDialect::escape( 'C:\\path\\' ) );
        $this->assertSame( '', expDuckDBDialect::escape( null ) );
        $this->assertSame( '12', expDuckDBDialect::escape( 12 ) );
    }

    /** DD-07 */
    public function testErrorCode()
    {
        $this->assertSame( '23505', expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_CONSTRAINT, 'Constraint Error: Duplicate key "id: 1" violates primary key constraint.' ) );
        $this->assertSame( '23502', expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_CONSTRAINT, 'Constraint Error: NOT NULL constraint failed: t.a' ) );
        $this->assertSame( '42P01', expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_CATALOG, 'Catalog Error: Table with name nope does not exist!' ) );
        $this->assertSame( '42P07', expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_CATALOG, 'Catalog Error: Table with name t already exists!' ) );
        $this->assertSame( '42703', expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_BINDER, 'Binder Error: Referenced column "x" not found in FROM clause!' ) );
        $this->assertSame( '42601', expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_PARSER, 'Parser Error: syntax error at or near "FROM"' ) );
        $this->assertSame( '22P02', expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_CONVERSION, "Conversion Error: Could not convert string 'a' to INT32" ) );
        $this->assertSame( '55P03', expDuckDBDialect::errorCode( expDuckDBFFI::ERROR_IO, 'IO Error: Could not set lock on file "x": Conflicting lock is held' ) );
        $this->assertSame( 'XX000', expDuckDBDialect::errorCode( 99, 'something else' ) );
    }

    /** DD-08 */
    public function testLockAndReadOnlyErrors()
    {
        $this->assertTrue( expDuckDBDialect::isLockError( 'IO Error: Could not set lock on file "/x/site.duckdb": Conflicting lock is held in php (PID 12)' ) );
        $this->assertFalse( expDuckDBDialect::isLockError( 'IO Error: Cannot open file "/x": Permission denied' ) );
        $this->assertTrue( expDuckDBDialect::isReadOnlyError( 'Invalid Input Error: Cannot execute statement of type "INSERT" on database "site" which is attached in read-only mode!' ) );
        $this->assertFalse( expDuckDBDialect::isReadOnlyError( 'Binder Error: x' ) );
    }

    /** DD-09 */
    public function testSQLHelpers()
    {
        $literal = function ( $value ) { return expDBSQLHelpers::literal( array( 'expDuckDBDialect', 'escape' ), $value ); };
        $this->assertContains( 'duckdb', expDBSQLHelpers::DIALECTS );
        $this->assertSame( "SELECT a FROM t\nLIMIT 10 OFFSET 20", expDBSQLHelpers::limit( 'duckdb', 'SELECT a FROM t', 10, 20 ) );
        $this->assertSame( "SELECT a FROM t\nLIMIT 10", expDBSQLHelpers::limit( 'duckdb', 'SELECT a FROM t;', 10 ) );
        $this->assertSame( "SELECT a FROM t\nOFFSET 5", expDBSQLHelpers::limit( 'duckdb', 'SELECT a FROM t', false, 5 ) );
        $this->assertSame( 'SELECT a FROM t', expDBSQLHelpers::limit( 'duckdb', 'SELECT a FROM t', null ) );
        $this->assertNull( expDBSQLHelpers::limit( 'duckdb', 'SELECT a FROM t', 0 ) );
        $this->assertSame( 'SELECT a FROM t WHERE id = 1', expDBSQLHelpers::forUpdate( 'duckdb', 'SELECT a FROM t WHERE id = 1;' ) );
        $this->assertSame( 'INSERT INTO "t" ("id", "name") VALUES (1, \'it\'\'s\') ON CONFLICT ("id") DO UPDATE SET "name" = excluded."name"',
                           expDBSQLHelpers::upsert( 'duckdb', $literal, 't', array( 'id' => 1, 'name' => "it's" ), array( 'id' ) ) );
        $this->assertSame( 'INSERT INTO "t" ("id") VALUES (1) ON CONFLICT ("id") DO NOTHING',
                           expDBSQLHelpers::upsert( 'duckdb', $literal, 't', array( 'id' => 1 ), array( 'id' ), array() ) );
        $this->assertSame( "STRING_AGG(CAST(name AS TEXT), ', ' ORDER BY name)", expDBSQLHelpers::groupConcat( 'duckdb', $literal, 'name', ', ', 'name' ) );
        $this->assertSame( 'CAST(EXTRACT(EPOCH FROM CURRENT_TIMESTAMP) AS BIGINT)', expDBSQLHelpers::unixTimestamp( 'duckdb' ) );
        $this->assertSame( "(TIMESTAMP '1970-01-01 00:00:00' + (created) * INTERVAL '1 second')", expDBSQLHelpers::fromUnixTimestamp( 'duckdb', 'created' ) );
        $this->assertSame( '"main"."a""b"', expDBSQLHelpers::quoteIdentifier( 'duckdb', 'main.a"b' ) );
    }
}
