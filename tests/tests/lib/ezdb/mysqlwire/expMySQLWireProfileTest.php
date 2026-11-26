<?php
/**
 * The profiles of the databases that speak the MySQL protocol (expMySQLWireProfile): their facts, how a server is
 * recognised, and every statement rewritten for TiDB, Vitess, SingleStore, Xpand and Aurora. No server is needed.
 *
 *  MW-01 - Every profile states every fact; each driver class exists and has its profile
 *  MW-02 - Names people write (planetscale, memsql, aurora-mysql) lead to the profile; an unknown name is an error
 *  MW-03 - A server is recognised from what it says (the strings measured on TiDB, Vitess and SingleStore)
 *  MW-04 - Data statements and statements of MySQL itself go as they are
 *  MW-05 - TiDB: BEGIN, FOR UPDATE for shared locks, a case-insensitive collation and AUTO_ID_CACHE for new tables,
 *          ANALYZE for OPTIMIZE; foreign keys stay
 *  MW-06 - Vitess: foreign keys are left out of CREATE TABLE and ALTER TABLE; text in quotes is not touched
 *  MW-07 - SingleStore: reference tables, no FULLTEXT, RENAME TABLE as ALTER TABLE, LOCK IN SHARE MODE
 *  MW-08 - Xpand: no FULLTEXT; Aurora: MySQL as it is
 *  MW-09 - The kernel schema and the cluster schema rewritten for each profile: every table is created, nothing is
 *          lost but what the server refuses
 *  MW-10 - Isolation levels, timestamps past 2038, lock names, the errors after a failover
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 * @group mysqlwire
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('mysqlwire')]
class expMySQLWireProfileTest extends PHPUnit\Framework\TestCase
{
    private static function rewrite( $profile, $sql, array $options = array() )
    {
        return expMySQLWireProfile::rewriteStatement( $profile, $sql, $options );
    }

    private static function one( $profile, $sql, array $options = array() )
    {
        $statements = self::rewrite( $profile, $sql, $options );
        return count( $statements ) === 1 ? $statements[0] : $statements;
    }

    /** MW-01 */
    public function testEveryProfileStatesEveryFact()
    {
        $definitions = expMySQLWireProfile::definitions();
        $keys = array_keys( $definitions['mysql'] );
        sort( $keys );
        foreach ( $definitions as $name => $facts )
        {
            $have = array_keys( $facts );
            sort( $have );
            $this->assertSame( $keys, $have, $name );
            $this->assertContains( $facts['level'], array( 'site', 'datatable', 'import' ), $name );
            $this->assertContains( $facts['foreign_keys'], array( 'enforced', 'refused', 'ignored' ), $name );
            $this->assertContains( $facts['table_locks'], array( 'native', 'named', 'none' ), $name );
            $this->assertContains( $facts['share_lock'], array( 'native', 'for_update', 'lock_in_share_mode' ), $name );
            $this->assertContains( $facts['fulltext'], array( 'native', 'ignored', 'refused' ), $name );
            $this->assertContains( $facts['auto_increment'], array( 'consecutive', 'cached', 'unique' ), $name );
            $this->assertContains( $facts['default_isolation'], $facts['isolation_levels'], $name );
            $this->assertTrue( class_exists( $facts['driver'] ), $facts['driver'] );
        }
        $this->assertSame( array( 'mysql', 'mariadb', 'tidb', 'vitess', 'singlestore', 'xpand', 'aurora' ), expMySQLWireProfile::names() );
    }

    /** MW-01 */
    public function testEachDriverClassHasItsProfile()
    {
        $classes = array( 'tidb' => 'expTiDBDB', 'vitess' => 'expVitessDB', 'singlestore' => 'expSingleStoreDB',
                          'xpand' => 'expXpandDB', 'aurora' => 'expAuroraMySQLDB' );
        foreach ( $classes as $profile => $class )
        {
            $this->assertTrue( is_subclass_of( $class, 'expMySQLWireDB' ), $class );
            $this->assertTrue( is_subclass_of( $class, 'eZMySQLiDB' ), $class );
            $this->assertSame( $profile, $class::PROFILE, $class );
            $facts = expMySQLWireProfile::get( $profile );
            $this->assertSame( $class, $facts['driver'] );
            $this->assertSame( $profile, $facts['implementation'] );
        }
    }

    /** MW-01: site.ini names a driver for every implementation */
    public function testSiteIniNamesTheDrivers()
    {
        $ini = (string)file_get_contents( 'settings/site.ini' );
        $aliases = array( 'tidb' => 'expTiDBDB', 'vitess' => 'expVitessDB', 'planetscale' => 'expVitessDB',
                          'singlestore' => 'expSingleStoreDB', 'memsql' => 'expSingleStoreDB', 'xpand' => 'expXpandDB',
                          'aurora' => 'expAuroraMySQLDB', 'auroramysql' => 'expAuroraMySQLDB' );
        foreach ( $aliases as $alias => $class )
            $this->assertStringContainsString( "\nImplementationAlias[$alias]=$class\n", $ini );
        $this->assertStringContainsString( "\n[MySQLWireSettings]\n", $ini );
        foreach ( array_keys( expMySQLWireDB::DEFAULT_SETTINGS ) as $name )
            $this->assertMatchesRegularExpression( '/^' . $name . '=/m', $ini, $name );
    }

    /** MW-02 */
    public function testNamesPeopleWrite()
    {
        $this->assertSame( 'vitess', expMySQLWireProfile::normalizeName( 'PlanetScale' ) );
        $this->assertSame( 'singlestore', expMySQLWireProfile::normalizeName( 'memsql' ) );
        $this->assertSame( 'aurora', expMySQLWireProfile::normalizeName( 'aurora-mysql' ) );
        $this->assertSame( 'xpand', expMySQLWireProfile::normalizeName( 'clustrix' ) );
        $this->assertSame( 'mysql', expMySQLWireProfile::normalizeName( 'ezmysqli' ) );
        $this->assertSame( 'tidb', expMySQLWireProfile::normalizeName( ' TiDB ' ) );
        $facts = expMySQLWireProfile::get( 'planetscale' );
        $this->assertSame( 'vitess', $facts['name'] );
        $this->expectException( InvalidArgumentException::class );
        expMySQLWireProfile::get( 'cockroach' );
    }

    /** MW-03: the answers measured on the servers, and the documented ones */
    public function testServerIsRecognised()
    {
        $this->assertSame( 'tidb', expMySQLWireProfile::detect( '8.0.11-TiDB-v8.5.1', 'TiDB Server (Apache License 2.0) Community Edition, MySQL 8.0 compatible' ) );
        $this->assertSame( 'vitess', expMySQLWireProfile::detect( '8.4.6-Vitess', "Version: 25.0.0-SNAPSHOT (Git revision 84d5f2df80) built on 2026-11-24" ) );
        $this->assertSame( 'singlestore', expMySQLWireProfile::detect( '5.7.32', 'SingleStoreDB source distribution (compatible; MySQL Enterprise & MySQL Commercial)', array( 'memsql_version' => '10.10.0' ) ) );
        $this->assertSame( 'singlestore', expMySQLWireProfile::detect( '5.7.32', '', array( 'memsql_version' => '8.5.1' ) ) );
        $this->assertSame( 'xpand', expMySQLWireProfile::detect( '5.0.45-Xpand-6.1.1', 'Xpand' ) );
        $this->assertSame( 'aurora', expMySQLWireProfile::detect( '8.0.32', 'Source distribution', array( 'aurora_version' => '3.05.2', 'memsql_version' => null ) ) );
        $this->assertSame( 'mariadb', expMySQLWireProfile::detect( '10.11.6-MariaDB', 'MariaDB Server' ) );
        $this->assertSame( 'mysql', expMySQLWireProfile::detect( '8.4.3', 'MySQL Community Server - GPL', array( 'aurora_version' => null ) ) );
    }

    /** MW-04 */
    public function testDataStatementsGoAsTheyAre()
    {
        $statements = array(
            "INSERT INTO ezurl (url) VALUES ('CREATE TABLE x (a int, FOREIGN KEY (a) REFERENCES y(b))')",
            "UPDATE ezcontentobject SET name = 'BEGIN WORK' WHERE id = 1",
            "DELETE FROM ezsession WHERE expiration_time < 5",
            "SELECT * FROM ezcontentobject WHERE id = 1",
            "SELECT * FROM ezcontentobject WHERE name = 'LOCK IN SHARE MODE'",
            "  select 1",
        );
        foreach ( expMySQLWireProfile::names() as $profile )
            foreach ( $statements as $sql )
                $this->assertSame( array( $sql ), self::rewrite( $profile, $sql ), "$profile: $sql" );
        // MySQL and MariaDB: nothing at all, not even a CREATE TABLE
        $create = "CREATE TABLE t (\n  id int NOT NULL auto_increment,\n  PRIMARY KEY (id),\n  FULLTEXT KEY t_ft (name)\n) ENGINE=InnoDB";
        $this->assertSame( $create, self::one( 'mysql', $create, array( 'auto_id_cache' => 1 ) ) );
        $this->assertSame( $create, self::one( 'mariadb', $create ) );
        $this->assertSame( 'BEGIN', self::one( 'mysql', 'BEGIN WORK' ) );
    }

    /** MW-05 */
    public function testTiDB()
    {
        $this->assertSame( 'BEGIN', self::one( 'tidb', 'BEGIN WORK' ) );
        $this->assertSame( 'SELECT * FROM ezurlalias_ml WHERE parent = 1 FOR UPDATE',
                           self::one( 'tidb', 'SELECT * FROM ezurlalias_ml WHERE parent = 1 LOCK IN SHARE MODE' ) );
        $this->assertSame( 'SELECT id FROM t WHERE id = 1 FOR UPDATE', self::one( 'tidb', 'SELECT id FROM t WHERE id = 1 FOR SHARE;' ) );
        $this->assertSame( 'ANALYZE TABLE ezsearch_word', self::one( 'tidb', 'OPTIMIZE TABLE ezsearch_word' ) );
        // CHECKSUM TABLE is refused by TiDB, Vitess and SingleStore: the migration caption takes no marker there
        foreach ( array( 'tidb' => false, 'vitess' => false, 'singlestore' => false, 'aurora' => true, 'mysql' => true ) as $profile => $works )
            $this->assertSame( $works, expMySQLWireProfile::get( $profile )['checksum_table'], $profile );

        $sql = "CREATE TABLE ezcontentclass (\n  id int(11) NOT NULL auto_increment,\n  identifier varchar(50) NOT NULL default '',\n  PRIMARY KEY (id)\n) ENGINE=InnoDB";
        $this->assertSame( $sql . ' COLLATE=utf8mb4_general_ci AUTO_ID_CACHE=1', self::one( 'tidb', $sql, array( 'auto_id_cache' => 1 ) ) );
        $this->assertSame( $sql . ' COLLATE=utf8mb4_general_ci', self::one( 'tidb', $sql, array( 'auto_id_cache' => 0 ) ) );
        $this->assertSame( $sql . ' COLLATE=utf8mb4_general_ci', self::one( 'tidb', $sql . ';' ) );
        // a collation of its own, another character set, no auto_increment column
        $this->assertSame( $sql . ' COLLATE utf8mb4_bin AUTO_ID_CACHE=1', self::one( 'tidb', $sql . ' COLLATE utf8mb4_bin', array( 'auto_id_cache' => 1 ) ) );
        $this->assertSame( $sql . ' DEFAULT CHARACTER SET latin1', self::one( 'tidb', $sql . ' DEFAULT CHARACTER SET latin1' ) );
        $this->assertSame( $sql . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci', self::one( 'tidb', $sql . ' DEFAULT CHARSET=utf8' ) );
        $plain = "CREATE TABLE ezsession (\n  session_key varchar(32) NOT NULL default '',\n  PRIMARY KEY (session_key)\n)";
        $this->assertSame( $plain . ' COLLATE=utf8mb4_general_ci', self::one( 'tidb', $plain, array( 'auto_id_cache' => 1 ) ) );
        // foreign keys and FULLTEXT stay: TiDB enforces the first and takes the second
        $fk = "CREATE TABLE c ( id int NOT NULL, pid int, PRIMARY KEY (id), CONSTRAINT fk FOREIGN KEY (pid) REFERENCES p (id) ON DELETE CASCADE )";
        $this->assertSame( $fk . ' COLLATE=utf8mb4_general_ci', self::one( 'tidb', $fk ) );
        $this->assertSame( array( 'ALTER TABLE expaudit_event ADD FULLTEXT INDEX expaudit_event_fts (search_text)' ),
                           self::rewrite( 'tidb', 'ALTER TABLE expaudit_event ADD FULLTEXT INDEX expaudit_event_fts (search_text)' ) );
        // CREATE TABLE ... LIKE and temporary tables are not changed
        $this->assertSame( 'CREATE TABLE a LIKE b', self::one( 'tidb', 'CREATE TABLE a LIKE b' ) );
        $this->assertSame( 'CREATE TEMPORARY TABLE t ( id int )', self::one( 'tidb', 'CREATE TEMPORARY TABLE t ( id int )' ) );
    }

    /** MW-06 */
    public function testVitessLeavesOutForeignKeys()
    {
        $sql = "CREATE TABLE c (\n  id int NOT NULL,\n  pid int,\n  note varchar(50) DEFAULT 'FOREIGN KEY, (not one)',\n  PRIMARY KEY (id),\n"
             . "  CONSTRAINT c_fk FOREIGN KEY (pid) REFERENCES p (id) ON DELETE CASCADE ON UPDATE NO ACTION,\n"
             . "  FOREIGN KEY (pid) REFERENCES p (id)\n) ENGINE=InnoDB";
        $expected = "CREATE TABLE c (\n  id int NOT NULL,\n  pid int,\n  note varchar(50) DEFAULT 'FOREIGN KEY, (not one)',\n  PRIMARY KEY (id)\n) ENGINE=InnoDB";
        $this->assertSame( $expected, self::one( 'vitess', $sql, array( 'auto_id_cache' => 1 ) ) );
        $this->assertSame( array(), self::rewrite( 'vitess', 'ALTER TABLE c ADD CONSTRAINT c_fk FOREIGN KEY (pid) REFERENCES p (id)' ) );
        $this->assertSame( array(), self::rewrite( 'vitess', 'ALTER TABLE c ADD FOREIGN KEY (pid) REFERENCES p (id)' ) );
        $this->assertSame( array( 'ALTER TABLE c ADD INDEX c_pid (pid)' ), self::rewrite( 'vitess', 'ALTER TABLE c ADD INDEX c_pid (pid)' ) );
        $this->assertSame( 'BEGIN', self::one( 'vitess', 'begin work' ) );
        // Vitess runs MySQL underneath: FULLTEXT, shared locks, RENAME TABLE and OPTIMIZE stay
        $this->assertSame( 'SELECT 1 FROM t LOCK IN SHARE MODE', self::one( 'vitess', 'SELECT 1 FROM t LOCK IN SHARE MODE' ) );
        $this->assertSame( 'RENAME TABLE a TO b', self::one( 'vitess', 'RENAME TABLE a TO b' ) );
        $this->assertSame( 'OPTIMIZE TABLE a', self::one( 'vitess', 'OPTIMIZE TABLE a' ) );
    }

    /** MW-07 */
    public function testSingleStore()
    {
        $sql = "CREATE TABLE ezcontentobject (\n  id int(11) NOT NULL auto_increment,\n  name varchar(255),\n  remote_id varchar(100) NOT NULL default '',\n"
             . "  PRIMARY KEY (id),\n  UNIQUE KEY ezcontentobject_remote_id (remote_id),\n  FULLTEXT KEY ezcontentobject_ft (name)\n) ENGINE=InnoDB";
        $expected = "CREATE ROWSTORE REFERENCE TABLE ezcontentobject (\n  id int(11) NOT NULL auto_increment,\n  name varchar(255),\n  remote_id varchar(100) NOT NULL default '',\n"
             . "  PRIMARY KEY (id),\n  UNIQUE KEY ezcontentobject_remote_id (remote_id)\n) ENGINE=InnoDB COLLATE=utf8mb4_general_ci";
        $this->assertSame( $expected, self::one( 'singlestore', $sql, array( 'auto_id_cache' => 1 ) ) );
        // a table without a primary key cannot be a reference table
        $this->assertSame( 'CREATE ROWSTORE TABLE IF NOT EXISTS log ( line text ) COLLATE=utf8mb4_general_ci',
                           self::one( 'singlestore', 'CREATE TABLE IF NOT EXISTS log ( line text )' ) );
        $this->assertSame( 'CREATE TEMPORARY TABLE t ( id int )', self::one( 'singlestore', 'CREATE TEMPORARY TABLE t ( id int )' ) );
        $this->assertSame( array(), self::rewrite( 'singlestore', 'ALTER TABLE expaudit_event ADD FULLTEXT INDEX expaudit_event_fts (search_text)' ) );
        $this->assertSame( array(), self::rewrite( 'singlestore', 'CREATE FULLTEXT INDEX f ON t (a)' ) );
        $this->assertSame( array(), self::rewrite( 'singlestore', 'ALTER TABLE c ADD CONSTRAINT c_fk FOREIGN KEY (pid) REFERENCES p (id)' ) );
        $this->assertSame( array( 'ALTER TABLE a RENAME TO b', 'ALTER TABLE c RENAME TO d' ), self::rewrite( 'singlestore', 'RENAME TABLE a TO b, c TO d' ) );
        $this->assertSame( 'SELECT id FROM t FOR UPDATE', self::one( 'singlestore', 'SELECT id FROM t FOR UPDATE' ) );
        $this->assertSame( 'SELECT id FROM t LOCK IN SHARE MODE', self::one( 'singlestore', 'SELECT id FROM t FOR SHARE' ) );
        $this->assertSame( 'SELECT id FROM t LOCK IN SHARE MODE', self::one( 'singlestore', 'SELECT id FROM t LOCK IN SHARE MODE' ) );
        $this->assertSame( 'OPTIMIZE TABLE t', self::one( 'singlestore', 'OPTIMIZE TABLE t' ) );
    }

    /** MW-07: CREATE TABLE ... LIKE copies the kind of the table it copies; AS SELECT has no primary key */
    public function testSingleStoreCreateLikeAndSelect()
    {
        $this->assertSame( 'CREATE TABLE a LIKE b', self::one( 'singlestore', 'CREATE TABLE a LIKE b' ) );
        $this->assertSame( 'CREATE TABLE a (LIKE b)', self::one( 'singlestore', 'CREATE TABLE a (LIKE b)' ) );
        $this->assertSame( 'CREATE ROWSTORE TABLE a AS SELECT * FROM b', self::one( 'singlestore', 'CREATE TABLE a AS SELECT * FROM b' ) );
    }

    /** MW-08 */
    public function testXpandAndAurora()
    {
        $this->assertSame( array(), self::rewrite( 'xpand', 'ALTER TABLE expaudit_event ADD FULLTEXT INDEX expaudit_event_fts (search_text)' ) );
        $fk = "CREATE TABLE c ( id int NOT NULL, pid int, PRIMARY KEY (id), FULLTEXT KEY f (pid), FOREIGN KEY (pid) REFERENCES p (id) )";
        $this->assertSame( 'CREATE TABLE c ( id int NOT NULL, pid int, PRIMARY KEY (id), FOREIGN KEY (pid) REFERENCES p (id) )', self::one( 'xpand', $fk ) );
        $this->assertSame( $fk, self::one( 'aurora', $fk ) );
        $this->assertSame( 'SELECT 1 FROM t LOCK IN SHARE MODE', self::one( 'aurora', 'SELECT 1 FROM t LOCK IN SHARE MODE' ) );
    }

    public static function profilesWithSchema()
    {
        return array( 'tidb' => array( 'tidb' ), 'vitess' => array( 'vitess' ), 'singlestore' => array( 'singlestore' ),
                      'xpand' => array( 'xpand' ), 'aurora' => array( 'aurora' ) );
    }

    /**
     * MW-09
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('profilesWithSchema')]
    public function testKernelSchemaForEachProfile( $profile )
    {
        $facts = expMySQLWireProfile::get( $profile );
        foreach ( array( 'kernel/sql/mysql/kernel_schema.sql', 'kernel/sql/mysql/cluster_dfs_schema.sql' ) as $file )
        {
            $text = (string)file_get_contents( $file );
            $text = preg_replace( '/^\s*--.*$/m', '', $text );
            $created = 0;
            $tables = preg_match_all( '/^CREATE TABLE /m', $text );
            foreach ( preg_split( '/;\s*$/m', $text ) as $statement )
            {
                if ( trim( $statement ) === '' )
                    continue;
                foreach ( expMySQLWireProfile::rewriteStatement( $profile, $statement, array( 'auto_id_cache' => 1 ) ) as $out )
                {
                    $this->assertStringNotContainsString( 'BEGIN WORK', $out );
                    if ( preg_match( '/^CREATE (ROWSTORE (REFERENCE )?)?TABLE /', trim( $out ) ) )
                    {
                        ++$created;
                        if ( $facts['reference_tables'] )
                            $this->assertMatchesRegularExpression( '/^CREATE ROWSTORE (REFERENCE )?TABLE /', trim( $out ) );
                        if ( $facts['binary_default_collation'] )
                            $this->assertStringContainsString( 'COLLATE=utf8mb4_general_ci', $out );
                        // the column list stays balanced
                        $this->assertSame( substr_count( $out, '(' ), substr_count( $out, ')' ), $out );
                    }
                    if ( $facts['fulltext'] === 'refused' )
                        $this->assertDoesNotMatchRegularExpression( '/\bFULLTEXT\b/', $out );
                    if ( $facts['foreign_keys'] !== 'enforced' )
                        $this->assertDoesNotMatchRegularExpression( '/\bFOREIGN\s+KEY\b/', $out );
                }
            }
            $this->assertGreaterThan( 0, $tables, $file );
            $this->assertSame( $tables, $created, "$profile: every table of $file is created" );
        }
    }

    /** MW-10 */
    public function testIsolationLevels()
    {
        $this->assertSame( 'READ COMMITTED', expMySQLWireProfile::isolationLevel( 'tidb', 'read-committed' ) );
        $this->assertSame( 'REPEATABLE READ', expMySQLWireProfile::isolationLevel( 'tidb', 'REPEATABLE_READ' ) );
        $this->assertNull( expMySQLWireProfile::isolationLevel( 'tidb', 'SERIALIZABLE' ) );
        $this->assertNull( expMySQLWireProfile::isolationLevel( 'tidb', 'READ UNCOMMITTED' ) );
        $this->assertNull( expMySQLWireProfile::isolationLevel( 'singlestore', 'REPEATABLE READ' ) );
        $this->assertSame( 'SERIALIZABLE', expMySQLWireProfile::isolationLevel( 'vitess', 'serializable' ) );
        $this->assertNull( expMySQLWireProfile::isolationLevel( 'vitess', '' ) );
        $this->assertNull( expMySQLWireProfile::isolationLevel( 'vitess', 'SNAPSHOT; DROP TABLE x' ) );
    }

    /** MW-10 */
    public function testTimestampsPast2038()
    {
        $this->assertNull( expMySQLWireProfile::unixTimestamp( 'tidb' ) );
        $this->assertNull( expMySQLWireProfile::fromUnixTimestamp( 'vitess', 'x' ) );
        $this->assertSame( "TIMESTAMPDIFF(SECOND, '1970-01-01 00:00:00', UTC_TIMESTAMP())", expMySQLWireProfile::unixTimestamp( 'singlestore' ) );
        $this->assertSame( "TIMESTAMPDIFF(SECOND, '1970-01-01 00:00:00', created)", expMySQLWireProfile::unixTimestamp( 'singlestore', 'created' ) );
        $this->assertSame( "TIMESTAMPADD(SECOND, 2208988800, '1970-01-01 00:00:00')", expMySQLWireProfile::fromUnixTimestamp( 'singlestore', '2208988800' ) );
    }

    /** MW-10 */
    public function testLockNamesAndFailoverErrors()
    {
        $this->assertSame( 'exp:site.ezcontent_language', expMySQLWireProfile::namedLockName( 'site', 'ezcontent_language' ) );
        $long = expMySQLWireProfile::namedLockName( str_repeat( 'd', 40 ), str_repeat( 't', 40 ) );
        $this->assertLessThanOrEqual( 64, strlen( $long ) );
        $this->assertSame( $long, expMySQLWireProfile::namedLockName( str_repeat( 'd', 40 ), str_repeat( 't', 40 ) ) );
        $this->assertTrue( expMySQLWireProfile::isReconnectError( 'aurora', 1290 ) );
        $this->assertTrue( expMySQLWireProfile::isReconnectError( 'aurora', 1836 ) );
        $this->assertTrue( expMySQLWireProfile::isReconnectError( 'aurora', 2013 ) );
        $this->assertFalse( expMySQLWireProfile::isReconnectError( 'aurora', 1062 ) );
        $this->assertFalse( expMySQLWireProfile::isReconnectError( 'tidb', 1290 ) );
    }

    /** The splitting the rewrites rest on */
    public function testSplittingRespectsQuotesAndParentheses()
    {
        $this->assertSame( array( "a 'x, y'", ' b(1, 2)', ' `c,d`', ' "e\\", f"' ),
                           expMySQLWireProfile::splitTopLevel( "a 'x, y', b(1, 2), `c,d`, \"e\\\", f\"", ',' ) );
        $this->assertSame( array( "a 'it''s, ok'", ' b' ), expMySQLWireProfile::splitTopLevel( "a 'it''s, ok', b", ',' ) );
        $sql = "CREATE TABLE t ( a varchar(10) DEFAULT ')', b int )";
        $this->assertSame( strlen( $sql ) - 1, expMySQLWireProfile::matchingParenthesis( $sql, strpos( $sql, '(' ) ) );
        $this->assertFalse( expMySQLWireProfile::matchingParenthesis( 'CREATE TABLE t ( a int', 15 ) );
    }
}
