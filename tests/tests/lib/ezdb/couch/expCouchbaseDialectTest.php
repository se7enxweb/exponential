<?php
/**
 * The lexer both document drivers share (expCouchSQLLexer) and the Couchbase dialect (expCouchbaseDialect), without a
 * server:
 *
 *  CB-01 - Tokens: strings with MySQL's escapes or SQLite's, comments dropped, quoted names, numbers, keywords
 *  CB-02 - Helpers: closing(), splitTopLevel(), findTopLevel(), literal(), text()
 *  CB-03 - kind(): select, insert, update, delete, ddl, transaction, noop, native
 *  CB-04 - SELECT: names quoted and in lower case, strings as JSON, SELECT * as t.*, names of expressions as written
 *  CB-05 - Types: a string compared with a numeric column becomes a number, a number with a text column a string,
 *          also in IN lists, BETWEEN and literal = column; MySQL's conversion of text (coerce())
 *  CB-06 - IS NULL / IS NOT NULL as IS NOT VALUED / IS VALUED; & and | as BITAND / BITOR; MD5 of a literal
 *  CB-07 - Joins: comma joins, JOIN ... ON, LEFT JOIN, USING; a column without its table gets the table that has it
 *  CB-08 - Sub-selects: SELECT RAW after IN, a value [0] elsewhere, EXISTS as it is; UNION
 *  CB-09 - LIMIT o, n and LIMIT n OFFSET o; GROUP BY, HAVING and ORDER BY by a name of the select list
 *  CB-10 - Primary key lookups: USE KEYS for = and IN on the whole key (one column, two columns), none with OR
 *  CB-11 - COUNT over a whole collection gets a filter on the key (the collection's statistics lag behind)
 *  CB-12 - Functions: CONCAT, SUBSTRING, IFNULL, COALESCE, CAST, IF, LENGTH, UNIX_TIMESTAMP, GROUP_CONCAT
 *  CB-13 - Refused by name: RIGHT JOIN, MD5 of a column, ORDER BY a position, DIV, a parameter, an unknown function
 *  CB-14 - UPDATE and DELETE: SQL++ with USE KEYS; a changed key column is reported; several tables refused
 *  CB-15 - INSERT parsed: rows of literals and expressions, DEFAULT, REPLACE, IGNORE, INSERT OR REPLACE, ON DUPLICATE
 *          KEY UPDATE with VALUES(), INSERT ... SELECT
 *  CB-16 - DDL parsed: CREATE TABLE (keys, defaults, AUTO_INCREMENT), CREATE INDEX, DROP, ALTER TABLE ADD/DROP/MODIFY
 *  CB-17 - documentKey(), kindOf(), errorNumber(), the portable helpers of the couchbase dialect
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCouchTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('couch')]
class expCouchbaseDialectTest extends PHPUnit\Framework\TestCase
{
    private function dialect()
    {
        return new expCouchbaseDialect( expCouchTestKit::catalogue() );
    }

    private function sql( $sql )
    {
        return $this->dialect()->select( $sql )['sql'];
    }

    /** CB-01 */
    public function testTokens()
    {
        $t = expCouchSQLLexer::tokenize( "SELECT `a b`, \"c\", 'it\\'s', 'x''y', 3.5, .5, 1e3, 12 -- note\n FROM t /* x */ WHERE a <> 1;" );
        $this->assertSame( array( 'kw', 'SELECT', 'SELECT' ), $t[0] );
        $this->assertSame( array( 'id', '`a b`', 'a b' ), $t[1] );
        $this->assertSame( 'c', $t[3][2] );
        $this->assertSame( "it's", $t[5][2] );
        $this->assertSame( "x'y", $t[7][2] );
        $this->assertSame( array( 3.5, 0.5, 1000.0, 12 ), array( $t[9][2], $t[11][2], $t[13][2], $t[15][2] ) );
        $this->assertSame( '<>', $t[count( $t ) - 2][2] );
        $this->assertSame( 'num', $t[count( $t ) - 1][0], 'the trailing ; is dropped' );
        // SQLite's strings: a backslash is a backslash
        $this->assertSame( 'a\\b', expCouchSQLLexer::tokenize( "'a\\b'", false )[0][2] );
        $this->assertSame( 'a' . "\n" . 'b', expCouchSQLLexer::tokenize( "'a\\nb'" )[0][2] );
        $this->assertSame( array( 'id', '2fa_x', '2fa_x' ), expCouchSQLLexer::tokenize( '2fa_x' )[0] );
        try
        {
            expCouchSQLLexer::tokenize( "SELECT 'open" );
            $this->fail( 'an unterminated string is refused' );
        }
        catch ( expDBUnsupportedException $e )
        {
            $this->assertStringContainsString( 'unterminated string', $e->getMessage() );
        }
    }

    /** CB-02 */
    public function testHelpers()
    {
        $t = expCouchSQLLexer::tokenize( 'f(a, (b, c)), d' );
        $this->assertSame( 9, expCouchSQLLexer::closing( $t, 1 ) );
        $parts = expCouchSQLLexer::splitTopLevel( $t );
        $this->assertCount( 2, $parts );
        $this->assertSame( 'd', $parts[1][0][2] );
        $this->assertSame( 8, expCouchSQLLexer::findTopLevel( expCouchSQLLexer::tokenize( 'a IN (SELECT x FROM y) AND b' ), array( 'AND' ) ) );
        $this->assertSame( -1, expCouchSQLLexer::findTopLevel( expCouchSQLLexer::tokenize( 'a IN (SELECT x FROM y WHERE c AND d)' ), array( 'AND' ) ) );
        $is = false;
        $this->assertSame( -5, expCouchSQLLexer::literal( expCouchSQLLexer::tokenize( '-5' ), $is ) );
        $this->assertTrue( $is );
        $this->assertNull( expCouchSQLLexer::literal( expCouchSQLLexer::tokenize( 'NULL' ), $is ) );
        $this->assertTrue( $is );
        expCouchSQLLexer::literal( expCouchSQLLexer::tokenize( 'a + 1' ), $is );
        $this->assertFalse( $is );
        $this->assertSame( 'COUNT(*) AS n', expCouchSQLLexer::text( expCouchSQLLexer::tokenize( 'COUNT( * ) AS n' ) ) );
    }

    /** CB-03 */
    public function testKind()
    {
        foreach ( array( 'select' => ' (SELECT 1)', 'insert' => 'REPLACE INTO t VALUES (1)', 'update' => 'update t set a=1', 'delete' => 'DELETE FROM t',
                         'ddl' => 'CREATE TABLE t (a int)', 'transaction' => 'BEGIN WORK', 'noop' => 'LOCK TABLES t WRITE', 'native' => '/*sqlpp*/ UPSERT INTO x' ) as $kind => $sql )
            $this->assertSame( $kind, expCouchbaseDialect::kind( $sql ), $sql );
        $this->assertSame( 'native', expCouchbaseDialect::kind( 'BUILD INDEX ON t(a)' ) );
        $this->assertSame( 'ddl', expCouchbaseDialect::kind( 'TRUNCATE TABLE t' ) );
    }

    /** CB-04 */
    public function testNamesStringsAndStar()
    {
        $this->assertSame( 'SELECT `ezpreferences`.* FROM `ezpreferences` AS `ezpreferences` WHERE `ezpreferences`.`name` = "a\"b"',
                           $this->sql( "SELECT * FROM ezpreferences WHERE Name = 'a\"b'" ) );
        $out = $this->dialect()->select( 'SELECT p.*, COUNT( * ), MAX(p.id) AS top, p.Value FROM ezpreferences p GROUP BY p.user_id' );
        $this->assertSame( 'SELECT `p`.*, COUNT(*) AS `COUNT(*)`, MAX(`p`.`id`) AS `top`, `p`.`value` AS `value` FROM `ezpreferences` AS `p` GROUP BY `p`.`user_id`', $out['sql'] );
        $this->assertSame( array( 'id', 'user_id', 'name', 'value', 'COUNT(*)', 'top', 'value' ), $out['columns'] );
        // a table the catalogue does not know: its columns are not known either
        $this->assertNull( $this->dialect()->select( 'SELECT * FROM unknown_table' )['columns'] );
    }

    /** CB-05 */
    public function testTypes()
    {
        // a column of a one-table statement is written with its table (CB-08: a sub-select would read it from the enclosing query)
        $this->assertSame( 'SELECT `ezcontentobject`.`id` AS `id` FROM `ezcontentobject` AS `ezcontentobject` WHERE `ezcontentobject`.`section_id` = 3 AND `ezcontentobject`.`remote_id` = "17"',
                           $this->sql( "SELECT id FROM ezcontentobject WHERE section_id = '3' AND remote_id = 17" ) );
        $this->assertStringContainsString( 'WHERE `ezcontentobject`.`section_id` IN [1, 2, 0]', $this->sql( "SELECT id FROM ezcontentobject WHERE section_id IN ('1', 2, 'x')" ) );
        $this->assertStringContainsString( 'WHERE `ezcontentobject`.`section_id` NOT IN [1]', $this->sql( "SELECT id FROM ezcontentobject WHERE section_id NOT IN ('1')" ) );
        $this->assertStringContainsString( '`ezcontentobject`.`section_id` BETWEEN 1 AND 5', $this->sql( "SELECT id FROM ezcontentobject WHERE section_id BETWEEN '1' AND '5'" ) );
        $this->assertStringContainsString( 'WHERE 4 = `ezcontentobject`.`section_id`', $this->sql( "SELECT id FROM ezcontentobject WHERE '4' = section_id" ) );
        $this->assertStringContainsString( 'WHERE `ezcontentobject`.`name` LIKE "Fo%"', $this->sql( "SELECT id FROM ezcontentobject WHERE name LIKE 'Fo%'" ) );
        $this->assertSame( 12, expCouchbaseDialect::coerce( '12abc', 'int' ) );
        $this->assertSame( 0, expCouchbaseDialect::coerce( 'abc', 'int' ) );
        $this->assertSame( 2.5, expCouchbaseDialect::coerce( ' 2.5', 'float' ) );
        $this->assertSame( 3, expCouchbaseDialect::coerce( 3.0, 'int' ) );
        $this->assertSame( '7', expCouchbaseDialect::coerce( 7, 'text' ) );
        $this->assertSame( '0.5', expCouchbaseDialect::coerce( 0.5, 'text' ) );
        $this->assertNull( expCouchbaseDialect::coerce( null, 'int' ) );
        $this->assertSame( 1, expCouchbaseDialect::coerce( true, 'int' ) );
    }

    /** CB-06 */
    public function testNullBitsAndMd5()
    {
        $this->assertStringContainsString( 'WHERE `ezcontentobject`.`name` IS NOT VALUED AND `ezcontentobject`.`remote_id` IS VALUED',
                                           $this->sql( 'SELECT id FROM ezcontentobject WHERE name IS NULL AND remote_id IS NOT NULL' ) );
        $this->assertStringContainsString( 'WHERE BITAND(`o`.`language_mask`, 3) > 0 AND BITOR(`o`.`section_id`, 1) = 1',
                                           $this->sql( 'SELECT id FROM ezcontentobject o WHERE language_mask & 3 > 0 AND section_id | 1 = 1' ) );
        $this->assertStringContainsString( 'WHERE (BITAND(`e0`.`lang_mask`, 3) > 0)', $this->sql( 'SELECT e0.id FROM ezurlalias_ml e0 WHERE (e0.lang_mask & 3 > 0)' ) );
        $this->assertStringContainsString( '`e`.`text_md5` = "' . md5( 'fit-healthy' ) . '"',
                                           $this->sql( "SELECT id FROM ezurlalias_ml e WHERE text_md5 = MD5( 'fit-healthy' )" ) );
        $this->assertStringContainsString( 'BITAND(`o`.`language_mask`, 2)', $this->sql( 'SELECT id FROM ezcontentobject o WHERE BITAND( language_mask, 2 ) > 0' ) );
    }

    /** CB-07 */
    public function testJoins()
    {
        $sql = $this->sql( 'SELECT o.id, t.node_id, depth FROM ezcontentobject o, ezcontentobject_tree t WHERE t.contentobject_id = o.id AND path_string LIKE \'/1/2/%\'' );
        $this->assertSame( 'SELECT `o`.`id` AS `id`, `t`.`node_id` AS `node_id`, `t`.`depth` AS `depth` FROM `ezcontentobject` AS `o`, `ezcontentobject_tree` AS `t` '
                           . 'WHERE `t`.`contentobject_id` = `o`.`id` AND `t`.`path_string` LIKE "/1/2/%"', $sql );
        $this->assertStringContainsString( 'FROM `ezcontentobject` AS `o` LEFT JOIN `ezcontentobject_tree` AS `t` ON `t`.`contentobject_id` = `o`.`id` AND `t`.`depth` = 2',
                                           $this->sql( "SELECT o.id FROM ezcontentobject o LEFT OUTER JOIN ezcontentobject_tree t ON t.contentobject_id = o.id AND t.depth = '2'" ) );
        $this->assertStringContainsString( 'INNER' === 'x' ? '' : 'JOIN `ezcontentobject_tree` AS `t` ON `o`.`id` = `t`.`id`',
                                           $this->sql( 'SELECT o.id FROM ezcontentobject o INNER JOIN ezcontentobject_tree t USING (id)' ) );
        // SQLite's INDEXED BY and MySQL's index hints are left out
        $this->assertSame( 'SELECT `o`.`id` AS `id` FROM `ezcontentobject` AS `o`', $this->sql( 'SELECT id FROM ezcontentobject o INDEXED BY x' ) );
        $this->assertSame( 'SELECT `o`.`id` AS `id` FROM `ezcontentobject` AS `o`', $this->sql( 'SELECT id FROM ezcontentobject AS o USE INDEX (a, b)' ) );
    }

    /** CB-08 */
    public function testSubSelectsAndUnion()
    {
        $this->assertStringContainsString( '`o`.`id` IN (SELECT RAW `t`.`contentobject_id` FROM `ezcontentobject_tree` AS `t` WHERE `t`.`depth` = 2)',
                                           $this->sql( "SELECT id FROM ezcontentobject o WHERE id IN (SELECT contentobject_id FROM ezcontentobject_tree t WHERE depth = '2')" ) );
        $this->assertStringContainsString( '(SELECT RAW COUNT(*) FROM `ezcontentobject_tree` AS `t` WHERE `t`.`contentobject_id` = `o`.`id`)[0] AS `n`',
                                           $this->sql( 'SELECT o.id, (SELECT COUNT(*) FROM ezcontentobject_tree t WHERE t.contentobject_id = o.id) AS n FROM ezcontentobject o' ) );
        $this->assertStringContainsString( 'EXISTS (SELECT 1 AS `1` FROM',
                                           $this->sql( 'SELECT o.id FROM ezcontentobject o WHERE EXISTS (SELECT 1 FROM ezcontentobject_tree t WHERE t.contentobject_id = o.id)' ) );
        $this->assertSame( 'SELECT `o`.`id` AS `id` FROM `ezcontentobject` AS `o` UNION ALL SELECT `t`.`node_id` AS `node_id` FROM `ezcontentobject_tree` AS `t`',
                           $this->sql( 'SELECT id FROM ezcontentobject o UNION ALL SELECT node_id FROM ezcontentobject_tree t' ) );
        $derived = $this->sql( 'SELECT x.n FROM (SELECT COUNT(*) AS n FROM ezcontentobject) x' );
        $this->assertStringContainsString( 'FROM (SELECT COUNT(*) AS `n` FROM `ezcontentobject` AS `ezcontentobject` WHERE META(`ezcontentobject`).id IS NOT MISSING) AS `x`', $derived );
        // a sub-select that repeats a name of the enclosing query gets its own (SQL++ refuses a repeated name)
        // (a column without its table is the inner table's, as in SQL)
        $this->assertSame( 'SELECT `ezcontentobject`.`name` AS `name` FROM `ezcontentobject` AS `ezcontentobject` WHERE `ezcontentobject`.`id` = (SELECT RAW MAX(`ezcontentobject_2`.`id`) '
                           . 'FROM `ezcontentobject` AS `ezcontentobject_2` WHERE `ezcontentobject_2`.`section_id` = `ezcontentobject_2`.`section_id`)[0]',
                           $this->sql( 'SELECT name FROM ezcontentobject WHERE id = (SELECT MAX(ezcontentobject.id) FROM ezcontentobject WHERE ezcontentobject.section_id = section_id)' ) );
        // and a statement refused halfway leaves nothing for the next one
        try
        {
            $this->sql( 'SELECT id FROM ezcontentobject WHERE id IN (SELECT MD5(name) FROM ezcontentobject)' );
        }
        catch ( expDBUnsupportedException $e )
        {
        }
        $this->assertSame( 'SELECT `ezcontentobject`.`id` AS `id` FROM `ezcontentobject` AS `ezcontentobject`', $this->sql( 'SELECT id FROM ezcontentobject' ) );
        // the sub-select's own column, not the enclosing row's (SQL++ would take qty of the outer row)
        $this->assertStringContainsString( '(SELECT RAW MAX(`ezcontentobject_2`.`section_id`) FROM `ezcontentobject` AS `ezcontentobject_2`',
                                           $this->sql( 'SELECT name FROM ezcontentobject WHERE section_id = (SELECT MAX(section_id) FROM ezcontentobject)' ) );
        // with the connection's bucket and scope, that sub-select names its collection by the full path: SQL++ would
        // read the bare name as the enclosing query's row (alias ezcontentobject), a sub-select over one document
        $scoped = new expCouchbaseDialect( expCouchTestKit::catalogue(), 'exponential', 'site' );
        $this->assertStringContainsString( 'FROM `exponential`.`site`.`ezcontentobject` AS `ezcontentobject_2`',
                                           $scoped->select( 'SELECT name FROM ezcontentobject WHERE section_id = (SELECT MAX(section_id) FROM ezcontentobject)' )['sql'] );
        $this->assertStringStartsWith( 'SELECT `ezcontentobject`.`name` AS `name` FROM `ezcontentobject` AS `ezcontentobject` WHERE',
                                       $scoped->select( 'SELECT name FROM ezcontentobject WHERE section_id = (SELECT MAX(section_id) FROM ezcontentobject)' )['sql'] );
    }

    /** CB-09 */
    public function testLimitGroupOrder()
    {
        $this->assertStringEndsWith( 'ORDER BY `o`.`id` DESC LIMIT 10 OFFSET 20', $this->sql( 'SELECT id FROM ezcontentobject o ORDER BY id DESC LIMIT 20, 10' ) );
        $this->assertStringEndsWith( 'LIMIT 5 OFFSET 2', $this->sql( 'SELECT id FROM ezcontentobject LIMIT 5 OFFSET 2' ) );
        $this->assertStringEndsWith( 'GROUP BY `o`.`section_id` HAVING COUNT(*) > 1 ORDER BY `Total` DESC',
                                     $this->sql( 'SELECT section_id, COUNT(*) AS Total FROM ezcontentobject o GROUP BY section_id HAVING COUNT(*) > 1 ORDER BY total DESC' ) );
        // FOR UPDATE is left out
        $this->assertStringEndsWith( 'WHERE `o`.`section_id` = 1', $this->sql( 'SELECT id FROM ezcontentobject o WHERE section_id = 1 FOR UPDATE' ) );
    }

    /** CB-10 */
    public function testKeyLookups()
    {
        $this->assertStringContainsString( 'AS `ezcontentobject` USE KEYS ["5"] WHERE `ezcontentobject`.`id` = 5', $this->sql( "SELECT name FROM ezcontentobject WHERE id = '5'" ) );
        $this->assertStringContainsString( 'USE KEYS ["1","2"]', $this->sql( 'SELECT name FROM ezcontentobject WHERE id IN (1, 2, 2) AND section_id = 1' ) );
        $md5 = md5( 'x' );
        $this->assertStringContainsString( 'USE KEYS ["[\"0\",\"' . $md5 . '\"]"]',
                                           $this->sql( "SELECT id FROM ezurlalias_ml e WHERE e.parent = 0 AND e.text_md5 = '$md5' AND (lang_mask & 1 > 0)" ) );
        $this->assertStringNotContainsString( 'USE KEYS', $this->sql( 'SELECT name FROM ezcontentobject WHERE id = 5 OR id = 6' ) );
        $this->assertStringNotContainsString( 'USE KEYS', $this->sql( 'SELECT id FROM ezurlalias_ml WHERE parent = 0' ) );
        $this->assertStringNotContainsString( 'USE KEYS', $this->sql( 'SELECT o.id FROM ezcontentobject o, ezcontentobject_tree t WHERE o.id = 5' ) );
        $this->assertSame( array( '5' ), $this->dialect()->keysFromWhere( 'ezcontentobject', 'ezcontentobject', expCouchSQLLexer::tokenize( "( id = '5' )" ) ) );
    }

    /** CB-11 */
    public function testCountGetsAFilter()
    {
        $this->assertSame( 'SELECT COUNT(*) AS `n` FROM `ezcontentobject` AS `ezcontentobject` WHERE META(`ezcontentobject`).id IS NOT MISSING',
                           $this->sql( 'SELECT COUNT(*) AS n FROM ezcontentobject' ) );
        $this->assertStringNotContainsString( 'META(', $this->sql( 'SELECT COUNT(*) AS n FROM ezcontentobject WHERE section_id = 1' ) );
    }

    /** CB-12 */
    public function testFunctions()
    {
        $sql = $this->sql( "SELECT CONCAT(name, '-', id) AS a, SUBSTRING(name, 2, 3) AS b, IFNULL(name, 'x') AS c, COALESCE(name, remote_id, '') AS d, "
                         . "CAST(remote_id AS UNSIGNED) AS e, IF(id > 1, 'y', 'n') AS f, LENGTH(name) AS g, UNIX_TIMESTAMP() AS h FROM ezcontentobject o" );
        foreach ( array( 'CONCAT(TOSTRING(`o`.`name`), "-", TOSTRING(`o`.`id`)) AS `a`', 'SUBSTR1(TOSTRING(`o`.`name`), 2, 3) AS `b`', 'IFMISSINGORNULL(`o`.`name`, "x") AS `c`',
                         'IFMISSINGORNULL(`o`.`name`, `o`.`remote_id`, "") AS `d`', 'TRUNC(TONUMBER(`o`.`remote_id`)) AS `e`', 'CASE WHEN `o`.`id` > 1 THEN "y" ELSE "n" END AS `f`',
                         'LENGTH(TOSTRING(`o`.`name`)) AS `g`', 'TRUNC(NOW_MILLIS() / 1000) AS `h`' ) as $part )
            $this->assertStringContainsString( $part, $sql );
        $this->assertStringContainsString( 'CONCAT2(";", ARRAY_AGG(TOSTRING(`o`.`name`)))', $this->sql( "SELECT GROUP_CONCAT(name SEPARATOR ';') AS n FROM ezcontentobject o" ) );
    }

    /** CB-13 */
    public function testRefusedByName()
    {
        foreach ( array( 'SELECT a.id FROM ezcontentobject a RIGHT JOIN ezcontentobject_tree t ON t.node_id = a.id' => 'RIGHT JOIN',
                         'SELECT MD5(name) FROM ezcontentobject' => 'MD5 of a column',
                         'SELECT id FROM ezcontentobject ORDER BY 1' => 'by position',
                         'SELECT id DIV 2 FROM ezcontentobject' => 'DIV',
                         'SELECT id FROM ezcontentobject WHERE id = ?' => 'bound parameter',
                         'SELECT NO_SUCH_FN(id) FROM ezcontentobject' => 'function NO_SUCH_FN' ) as $sql => $what )
        {
            try
            {
                $this->sql( $sql );
                $this->fail( "refused: $sql" );
            }
            catch ( expDBUnsupportedException $e )
            {
                $this->assertStringContainsString( $what, $e->getMessage(), $sql );
                $this->assertSame( 'couchbase', $e->dialect() );
            }
        }
    }

    /** CB-14 */
    public function testUpdateAndDelete()
    {
        $w = $this->dialect()->write( "UPDATE ezcontentobject SET name = 'n', section_id = '2', current_version = current_version + 1 WHERE id = 7" );
        $this->assertSame( 'UPDATE `ezcontentobject` AS `ezcontentobject` USE KEYS ["7"] SET `ezcontentobject`.`name` = "n", `ezcontentobject`.`section_id` = 2, '
                           . '`ezcontentobject`.`current_version` = `ezcontentobject`.`current_version` + 1 WHERE `ezcontentobject`.`id` = 7', $w['sql'] );
        $this->assertFalse( $w['keys_changed'] );
        $this->assertTrue( $this->dialect()->write( 'UPDATE ezcontentobject SET id = 9 WHERE id = 7' )['keys_changed'] );
        $this->assertSame( 'DELETE FROM `ezpreferences` AS `ezpreferences` WHERE `ezpreferences`.`user_id` = 14 LIMIT 1',
                           $this->dialect()->write( "DELETE FROM ezpreferences WHERE user_id = '14' LIMIT 1" )['sql'] );
        $this->assertSame( 'DELETE FROM `ezpreferences`', $this->dialect()->write( 'TRUNCATE TABLE ezpreferences' )['sql'] );
        foreach ( array( 'UPDATE a, b SET a.x = 1', 'DELETE a FROM a JOIN b ON a.id = b.id' ) as $sql )
        {
            try
            {
                $this->dialect()->write( $sql );
                $this->fail( $sql );
            }
            catch ( expDBUnsupportedException $e )
            {
                $this->assertStringContainsString( 'several tables', $e->getMessage() );
            }
        }
    }

    /** CB-15 */
    public function testInsertParsed()
    {
        $p = $this->dialect()->insert( "INSERT INTO ezpreferences (user_id, name, value) VALUES (14, 'a', 'x'), ('15', 'b', DEFAULT), (16, CONCAT('c', 'd'), NULL)" );
        $this->assertSame( array( 'ezpreferences', array( 'user_id', 'name', 'value' ), 'insert' ), array( $p['table'], $p['columns'], $p['mode'] ) );
        $this->assertCount( 3, $p['rows'] );
        $this->assertSame( array( 'literal' => true, 'value' => '15' ), $p['rows'][1][0] );
        $this->assertTrue( $p['rows'][1][2]['default'] );
        $this->assertFalse( $p['rows'][2][1]['literal'] );
        $this->assertSame( 'CONCAT("c", "d")', $p['rows'][2][1]['value'] );
        $this->assertSame( 'replace', $this->dialect()->insert( "REPLACE INTO ezpreferences (id) VALUES (1)" )['mode'] );
        $this->assertSame( 'ignore', $this->dialect()->insert( "INSERT IGNORE INTO ezpreferences (id) VALUES (1)" )['mode'] );
        $this->assertSame( 'replace', $this->dialect()->insert( "INSERT OR REPLACE INTO ezpreferences (id) VALUES (1)" )['mode'] );
        $p = $this->dialect()->insert( "INSERT INTO `ezpreferences` (`id`, `value`) VALUES (1, 'v') ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), user_id = user_id + 1" );
        $this->assertSame( array( 'value' => array( 'values' => 'value' ), 'user_id' => array( 'sql' => '`ezpreferences`.`user_id` + 1' ) ), $p['update'] );
        $p = $this->dialect()->insert( 'INSERT INTO ezpreferences (user_id, name) SELECT id, name FROM ezcontentobject o WHERE section_id = 1' );
        $this->assertSame( 'SELECT `o`.`id` AS `id`, `o`.`name` AS `name` FROM `ezcontentobject` AS `o` WHERE `o`.`section_id` = 1', $p['select'] );
        // the catalogue's columns when the statement names none
        $this->assertSame( array( 'id', 'user_id', 'name', 'value' ), $this->dialect()->insert( "INSERT INTO ezpreferences VALUES (1, 2, 'n', 'v')" )['columns'] );
    }

    /** CB-16 */
    public function testDDLParsed()
    {
        $d = $this->dialect()->ddl( "CREATE TABLE IF NOT EXISTS t1 (id int(11) NOT NULL AUTO_INCREMENT, name varchar(50) NOT NULL DEFAULT 'x''y', n int DEFAULT -1, "
                                  . 'PRIMARY KEY (id), KEY t1_name (name), UNIQUE KEY t1_n (n))' );
        $this->assertSame( 'create_table', $d['action'] );
        $this->assertFalse( $d['temporary'] );
        $this->assertSame( array( 'type' => 'auto_increment', 'not_null' => '1', 'default' => false ), $d['definition']['fields']['id'] );
        $this->assertSame( "x'y", $d['definition']['fields']['name']['default'] );
        $this->assertSame( 50, $d['definition']['fields']['name']['length'] );
        $this->assertSame( -1, $d['definition']['fields']['n']['default'] );
        $this->assertSame( array( 'type' => 'primary', 'fields' => array( 'id' ) ), $d['definition']['indexes']['PRIMARY'] );
        $this->assertSame( 'unique', $d['definition']['indexes']['t1_n']['type'] );
        $d = $this->dialect()->ddl( 'CREATE TEMPORARY TABLE tmp (id INTEGER PRIMARY KEY AUTOINCREMENT, v text)' );
        $this->assertTrue( $d['temporary'] );
        $this->assertSame( 'auto_increment', $d['definition']['fields']['id']['type'] );
        $this->assertSame( array( 'id' ), $d['definition']['indexes']['PRIMARY']['fields'] );
        $this->assertSame( array( 'action' => 'create_index', 'table' => 't1', 'name' => 'i1', 'fields' => array( 'a', 'b' ), 'unique' => true ),
                           $this->dialect()->ddl( 'CREATE UNIQUE INDEX i1 ON t1 (a, b(10))' ) );
        $this->assertSame( array( 'action' => 'drop_table', 'tables' => array( 't1', 't2' ) ), $this->dialect()->ddl( 'DROP TABLE IF EXISTS t1, t2' ) );
        $this->assertSame( array( 'action' => 'drop_index', 'name' => 'i1', 'table' => 't1' ), $this->dialect()->ddl( 'DROP INDEX i1 ON t1' ) );
        $d = $this->dialect()->ddl( "ALTER TABLE t1 ADD COLUMN note varchar(20) DEFAULT 'n'" );
        $this->assertSame( array( 'add_column', 'note', 'n' ), array( $d['action'], $d['column'], $d['field']['default'] ) );
        $this->assertSame( array( 'action' => 'drop_column', 'table' => 't1', 'column' => 'note' ), $this->dialect()->ddl( 'ALTER TABLE t1 DROP COLUMN note' ) );
        $this->assertSame( 'modify_column', $this->dialect()->ddl( 'ALTER TABLE t1 MODIFY note text' )['action'] );
        $this->assertSame( 'create_index', $this->dialect()->ddl( 'ALTER TABLE t1 ADD INDEX t1_x (x)' )['action'] );
    }

    /** CB-17 */
    public function testKeysKindsErrorsAndHelpers()
    {
        $d = $this->dialect();
        $this->assertSame( '5', $d->documentKey( 'ezcontentobject', array( 'id' => '5' ) ) );
        $this->assertSame( '["0","abc"]', $d->documentKey( 'ezurlalias_ml', array( 'parent' => 0, 'text_md5' => 'abc' ) ) );
        $this->assertNull( $d->documentKey( 'ezcontentobject', array( 'id' => null ) ) );
        $this->assertNull( $d->documentKey( 'unknown', array( 'id' => 1 ) ) );
        $this->assertSame( array( 'int', 'int', 'float', 'text', 'text' ), array_map( function ( $t ) { return expCouchbaseDialect::kindOf( array( 'type' => $t ) ); },
                                                                                           array( 'auto_increment', 'bigint', 'double', 'varchar', 'longtext' ) ) );
        $this->assertSame( 1062, expCouchbaseDialect::errorNumber( 12009, 'DML Error ... Duplicate Key: 1' ) );
        $this->assertSame( 1062, expCouchbaseDialect::errorNumber( 17012, 'x' ) );
        $this->assertSame( 1146, expCouchbaseDialect::errorNumber( 12003, 'Keyspace not found' ) );
        $this->assertSame( 4000, expCouchbaseDialect::errorNumber( 4000, 'x' ) );
        $this->assertSame( '`a`.`b`', expDBSQLHelpers::quoteIdentifier( 'couchbase', 'a.b' ) );
        $this->assertSame( "SELECT 1\nOFFSET 5", expDBSQLHelpers::limit( 'couchbase', 'SELECT 1', false, 5 ) );
        $this->assertSame( "SELECT 1\nLIMIT 3 OFFSET 5", expDBSQLHelpers::limit( 'couchbase', 'SELECT 1', 3, 5 ) );
        $literal = function ( $v ) { return is_int( $v ) ? (string)$v : "'" . $v . "'"; };
        $this->assertSame( "INSERT INTO `t` (`id`, `v`) VALUES (1, 'x') ON DUPLICATE KEY UPDATE `v` = VALUES(`v`)",
                           expDBSQLHelpers::upsert( 'couchbase', $literal, 't', array( 'id' => 1, 'v' => 'x' ), array( 'id' ) ) );
        $this->assertSame( "GROUP_CONCAT(name SEPARATOR ',')", expDBSQLHelpers::groupConcat( 'couchbase', $literal, 'name' ) );
        // and the driver understands what the helpers write
        $this->assertStringContainsString( 'CONCAT2(",", ARRAY_AGG(TOSTRING(`o`.`name`)))',
                                           $this->sql( 'SELECT ' . expDBSQLHelpers::groupConcat( 'couchbase', $literal, 'name' ) . ' AS n FROM ezcontentobject o' ) );
    }
}
?>
