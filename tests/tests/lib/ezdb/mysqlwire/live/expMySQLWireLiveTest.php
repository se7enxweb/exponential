<?php
/**
 * The MySQL-wire drivers against running servers: TiDB, Vitess (vttestserver), SingleStore (the dev container),
 * and Xpand and Aurora MySQL where one is given. Each server is named by EXP_TEST_<PROFILE>_HOST (and _PORT, _USER,
 * _PASSWORD or _PASSWORD_FILE, _DATABASE); a profile without one is skipped. The tables of the test start with
 * expmw_ and are dropped at the end.
 *
 *  MWL-01 - The driver reaches the server of its profile, and recognises it
 *  MWL-02 - MySQL's DDL as the kernel writes it (foreign keys, FULLTEXT, prefix indexes, unique keys) creates the table
 *  MWL-03 - Text: case-insensitive comparison of columns, UTF-8 with emoji
 *  MWL-04 - Ids: an explicit id, then the next one after it; lastSerialID()
 *  MWL-05 - Transactions: rollback, commit, nesting; a shared lock in a transaction; table locks
 *  MWL-06 - The portable helpers: upsert, GROUP_CONCAT, timestamps past 2038
 *  MWL-07 - RENAME TABLE, ALTER TABLE ADD/MODIFY, the schema read back by the MySQL schema handler
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 * @group mysqlwire
 * @group mysqlwire-live
 */

require_once dirname( __DIR__ ) . '/expMySQLWireTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('mysqlwire')]
#[\PHPUnit\Framework\Attributes\Group('mysqlwire-live')]
class expMySQLWireLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var expMySQLWireDB|null */
    private $db;

    private $saved;

    private $tables = array( 'expmw_child', 'expmw_parent', 'expmw_t', 'expmw_t2', 'expmw_up' );

    public static function profiles()
    {
        $profiles = array();
        foreach ( array_keys( expMySQLWireTestKit::LIVE ) as $profile )
            $profiles[$profile] = array( $profile );
        return $profiles;
    }

    protected function tearDown(): void
    {
        if ( $this->db )
        {
            while ( $this->db->transactionCounter() > 0 )
                $this->db->rollback();
            $this->db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            foreach ( $this->tables as $table )
            {
                try { $this->db->query( "DROP TABLE IF EXISTS $table" ); } catch ( Exception $e ) {}
            }
            $this->db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
            $this->db->close();
            $this->db = null;
        }
        if ( $this->saved !== null )
            expMySQLWireTestKit::restoreDebug( $this->saved );
        $this->saved = null;
    }

    private function connect( $profile )
    {
        $this->saved = expMySQLWireTestKit::quietDebug();
        $db = expMySQLWireTestKit::liveDriver( $profile );
        if ( !$db instanceof expMySQLWireDB )
            $this->markTestSkipped( $db );
        $this->db = $db;
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        foreach ( $this->tables as $table )
            $db->query( "DROP TABLE IF EXISTS $table" );
        $db->query( "CREATE TABLE expmw_t (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  code varchar(50) NOT NULL default '',
  body longtext,
  created int(11) NOT NULL default 0,
  PRIMARY KEY (id),
  KEY expmw_t_name (name(191)),
  UNIQUE KEY expmw_t_code (code)
) ENGINE=InnoDB" );
        return $db;
    }

    private function scalar( $sql )
    {
        $rows = $this->db->arrayQuery( $sql );
        $row = $rows ? reset( $rows ) : null;
        return is_array( $row ) ? reset( $row ) : null;
    }

    private function tableNames()
    {
        $names = array();
        foreach ( $this->db->arrayQuery( 'SHOW TABLES' ) as $row )
            $names[(string)reset( $row )] = true;
        return $names;
    }

    /** MWL-01 */
    #[\PHPUnit\Framework\Attributes\DataProvider('profiles')]
    public function testReachesAndRecognisesItsServer( $profile )
    {
        $db = $this->connect( $profile );
        $this->assertSame( $profile, $db->mysqlWireProfile() );
        $this->assertSame( $profile, $db->detectServerProfile() );
        $this->assertSame( 'mysql', $db->databaseName() );
        $this->assertNotSame( '', (string)$db->databaseServerVersion()['string'] );
    }

    /** MWL-02 */
    #[\PHPUnit\Framework\Attributes\DataProvider('profiles')]
    public function testKernelStyleDDL( $profile )
    {
        $db = $this->connect( $profile );
        $db->query( 'CREATE TABLE expmw_parent ( id int NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB' );
        $db->query( 'CREATE TABLE expmw_child ( id int NOT NULL, pid int NOT NULL, note text, PRIMARY KEY (id), KEY expmw_child_pid (pid),
  CONSTRAINT expmw_child_fk FOREIGN KEY (pid) REFERENCES expmw_parent (id) ON DELETE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4' );
        $db->query( 'ALTER TABLE expmw_t ADD FULLTEXT INDEX expmw_t_fts (body)' );
        $tables = $this->tableNames();
        foreach ( array( 'expmw_parent', 'expmw_child', 'expmw_t' ) as $table )
            $this->assertArrayHasKey( $table, $tables, $table );

        $facts = $db->mysqlWireFacts();
        if ( $facts['foreign_keys'] === 'enforced' )
        {
            try
            {
                $db->query( 'INSERT INTO expmw_child (id, pid) VALUES (1, 99)' );
                $this->fail( 'the foreign key was not enforced' );
            }
            catch ( eZDBException $e )
            {
                $this->assertSame( 1452, (int)$e->getCode() );
            }
        }
        else
        {
            $db->query( 'INSERT INTO expmw_child (id, pid) VALUES (1, 99)' );
            $this->assertSame( '1', (string)$this->scalar( 'SELECT COUNT(*) FROM expmw_child' ) );
        }
    }

    /** MWL-03 */
    #[\PHPUnit\Framework\Attributes\DataProvider('profiles')]
    public function testText( $profile )
    {
        $db = $this->connect( $profile );
        $text = "Grüße \u{1F600} 中文 'quoted' \\ back";
        $db->query( "INSERT INTO expmw_t (name, code, body) VALUES ('Admin', 'a1', '" . $db->escapeString( $text ) . "')" );
        $this->assertSame( $text, $this->scalar( "SELECT body FROM expmw_t WHERE code = 'a1'" ) );
        $this->assertSame( '1', (string)$this->scalar( "SELECT COUNT(*) FROM expmw_t WHERE name = 'admin'" ), 'columns compare without case, as on MySQL' );
        $this->assertSame( '1', (string)$this->scalar( "SELECT COUNT(*) FROM expmw_t WHERE code = 'A1'" ) );
    }

    /** MWL-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('profiles')]
    public function testIds( $profile )
    {
        $db = $this->connect( $profile );
        $db->query( "INSERT INTO expmw_t (name, code) VALUES ('a', 'a')" );
        $first = (int)$db->lastSerialID( 'expmw_t', 'id' );
        $this->assertGreaterThan( 0, $first );
        $db->query( "INSERT INTO expmw_t (id, name, code) VALUES (1000, 'b', 'b')" );
        $db->query( "INSERT INTO expmw_t (name, code) VALUES ('c', 'c')" );
        $next = (int)$db->lastSerialID( 'expmw_t', 'id' );
        $this->assertGreaterThan( 1000, $next, 'an id after an explicit one is above it' );
        $this->assertSame( (string)$next, (string)$this->scalar( "SELECT id FROM expmw_t WHERE code = 'c'" ) );
        $facts = $db->mysqlWireFacts();
        if ( $facts['auto_increment'] === 'consecutive' || $facts['name'] === 'tidb' )
            $this->assertSame( 1001, $next, 'MySQL order of ids (TiDB: AUTO_ID_CACHE=1)' );
    }

    /** MWL-05 */
    #[\PHPUnit\Framework\Attributes\DataProvider('profiles')]
    public function testTransactionsAndLocks( $profile )
    {
        $db = $this->connect( $profile );
        $db->begin();
        $db->query( "INSERT INTO expmw_t (name, code) VALUES ('r', 'r')" );
        $db->rollback();
        $this->assertSame( '0', (string)$this->scalar( "SELECT COUNT(*) FROM expmw_t WHERE code = 'r'" ) );

        $db->begin();
        $db->begin();
        $db->query( "INSERT INTO expmw_t (name, code) VALUES ('c', 'c')" );
        $db->commit();
        $this->assertSame( 1, $db->transactionCounter() );
        $rows = $db->arrayQuery( "SELECT id FROM expmw_t WHERE code = 'c' LOCK IN SHARE MODE" );
        $this->assertCount( 1, $rows );
        $rows = $db->arrayQuery( $db->sqlForUpdate( "SELECT id FROM expmw_t WHERE code = 'c'" ) );
        $this->assertCount( 1, $rows );
        $db->commit();
        $this->assertSame( '1', (string)$this->scalar( "SELECT COUNT(*) FROM expmw_t WHERE code = 'c'" ) );

        $db->lock( array( array( 'table' => 'expmw_t' ) ) );
        $db->query( "INSERT INTO expmw_t (name, code) VALUES ('l', 'l')" );
        $db->unlock();
        $this->assertSame( '1', (string)$this->scalar( "SELECT COUNT(*) FROM expmw_t WHERE code = 'l'" ) );
    }

    /** MWL-06 */
    #[\PHPUnit\Framework\Attributes\DataProvider('profiles')]
    public function testHelpers( $profile )
    {
        $db = $this->connect( $profile );
        $db->query( 'CREATE TABLE expmw_up ( k varchar(20) NOT NULL, v varchar(20) NOT NULL default \'\', PRIMARY KEY (k) )' );
        $db->query( $db->sqlUpsert( 'expmw_up', array( 'k' => 'a', 'v' => 'one' ), array( 'k' ) ) );
        $db->query( $db->sqlUpsert( 'expmw_up', array( 'k' => 'a', 'v' => 'two' ), array( 'k' ) ) );
        $db->query( $db->sqlUpsert( 'expmw_up', array( 'k' => 'b', 'v' => 'x' ), array( 'k' ), array() ) );
        $db->query( $db->sqlUpsert( 'expmw_up', array( 'k' => 'b', 'v' => 'y' ), array( 'k' ), array() ) );
        $this->assertSame( array( 'a' => 'two', 'b' => 'x' ), array_column( $db->arrayQuery( 'SELECT k, v FROM expmw_up ORDER BY k' ), 'v', 'k' ) );

        $this->assertSame( 'b|a', $this->scalar( 'SELECT ' . $db->sqlGroupConcat( 'k', '|', 'k DESC' ) . ' FROM expmw_up' ) );

        $this->assertSame( '2208988800', (string)$this->scalar( 'SELECT ' . $db->sqlUnixTimestamp( "'2040-01-01 00:00:00'" ) ) );
        $this->assertStringStartsWith( '2040-01-01 00:00:00', (string)$this->scalar( 'SELECT ' . $db->sqlFromUnixTimestamp( '2208988800' ) ) );
        $now = (int)$this->scalar( 'SELECT ' . $db->sqlUnixTimestamp() );
        $this->assertLessThan( 86400, abs( $now - time() ), 'now, as seconds since 1970' );
        $this->assertSame( array( array( 'k' => 'b' ) ), $db->arrayQuery( $db->sqlLimit( 'SELECT k FROM expmw_up ORDER BY k', 1, 1 ) ) );
    }

    /** MWL-07 */
    #[\PHPUnit\Framework\Attributes\DataProvider('profiles')]
    public function testSchemaChanges( $profile )
    {
        $db = $this->connect( $profile );
        $db->query( 'ALTER TABLE expmw_t ADD COLUMN extra int NOT NULL default 0' );
        $db->query( 'ALTER TABLE expmw_t MODIFY extra bigint NOT NULL default 0' );
        $db->query( 'ALTER TABLE expmw_t ADD INDEX expmw_t_extra (extra)' );
        $db->query( 'RENAME TABLE expmw_t TO expmw_t2' );
        $this->assertArrayHasKey( 'expmw_t2', $this->tableNames() );

        $schema = eZDbSchema::instance( $db );
        $this->assertInstanceOf( 'eZMysqlSchema', $schema );
        $tables = $schema->schema( array( 'format' => 'generic' ) );
        $this->assertArrayHasKey( 'expmw_t2', $tables );
        $table = $tables['expmw_t2'];
        $this->assertSame( 'auto_increment', $table['fields']['id']['type'] );
        $this->assertArrayHasKey( 'extra', $table['fields'] );
        $this->assertSame( 'unique', $table['indexes']['expmw_t_code']['type'] );
        $this->assertSame( 'primary', $table['indexes']['PRIMARY']['type'] );
        $this->assertArrayHasKey( 'expmw_t_extra', $table['indexes'] );
    }
}
