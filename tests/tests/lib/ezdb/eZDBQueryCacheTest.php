<?php
/**
 * The SQL query cache (lib/ezdb/classes/ezdbquerycache.php).
 *
 *  QC-01 — The tables a SELECT reads; not cacheable: not a plain SELECT, volatile, subquery in FROM
 *  QC-02 — The tables a write names, for every verb; unreadable writes are null
 *  QC-03 — A miss, a store, then a hit with the same rows
 *  QC-04 — A write to a table the result read makes it stale; a write elsewhere does not
 *  QC-05 — An unreadable write and a clear make everything stale
 *  QC-06 — In a transaction: reads are not cached, writes count at COMMIT and are dropped on ROLLBACK
 *  QC-07 — Temporary tables are never cached and never reach the state
 *  QC-08 — ExcludeTables, MaxRows and MaxAge
 *  QC-09 — Keys differ by statement, parameters, database and server
 *  QC-10 — A stored result is a copy: changing what a caller got changes nothing
 *  QC-11 — Mode=off stores and invalidates nothing
 *  QC-12 — A write by another process, seen through the state file, makes a result stale
 *  QC-13 — A stored result is answered without reading its tables again, but not once a table it read is excluded
 *  QC-14 — The database's own catalogue (sqlite_master, sqlite_stat1, pg_*, information_schema) is never cached
 *
 * No database: the driver is a stand-in object with the properties the cache
 * reads, and the state lives in a directory of its own under var/tmp.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group querycache
 */

require_once __DIR__ . '/../../../../lib/ezdb/classes/ezdbquerycache.php';

class eZDBQueryCacheTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $db;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 4 ) . '/var/tmp/querycache-test-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0770, true );
        eZDBQueryCache::setStateDir( $this->dir );
        $this->newRequest();
        $this->db = $this->driver();
    }

    protected function tearDown(): void
    {
        eZDBQueryCache::resetRequest();
        eZDBQueryCache::setStateDir( null );
        eZDBQueryCache::setSettings( null );
        foreach ( glob( $this->dir . '/*' ) as $f )
            unlink( $f );
        rmdir( $this->dir );
    }

    private function newRequest( array $settings = array() )
    {
        eZDBQueryCache::resetRequest();
        eZDBQueryCache::setSettings( $settings + array( 'mode' => 'request', 'maxAge' => 300, 'maxRows' => 5000, 'exclude' => array() ) );
    }

    private function driver( $database = 'site', $server = 'localhost' )
    {
        $db = new stdClass();
        $db->TransactionCounter = 0;
        $db->DB = $database;
        $db->Server = $server;
        return $db;
    }

    /** What arrayQuery() does: the cached rows, or null after storing $rows as the database's answer. */
    private function select( $sql, array $rows = array( array( 'id' => 1 ) ), $params = array(), $db = null )
    {
        $ticket = eZDBQueryCache::lookup( $db ?: $this->db, $sql, $params, $hit );
        if ( $hit !== null )
            return $hit;
        eZDBQueryCache::store( $ticket, $rows );
        return null;
    }

    private function write( $sql )
    {
        // Entries and writes carry microtime(): keep them from sharing one.
        usleep( 5 );
        eZDBQueryCache::noteWrite( $this->db, $sql );
        usleep( 5 );
    }

    /** QC-01 */
    public function testReadTables()
    {
        $cases = array(
            'SELECT * FROM ezcontentobject WHERE id = 1' => array( 'ezcontentobject' ),
            'SELECT a.id FROM ezcontentobject a, ezcontentobject_tree t WHERE a.id = t.contentobject_id' => array( 'ezcontentobject', 'ezcontentobject_tree' ),
            'SELECT * FROM `ezuser` u LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id' => array( 'ezuser', 'ezuser_setting' ),
            'SELECT count(*) FROM db.ezcontentclass AS c' => array( 'ezcontentclass' ),
            "SELECT id FROM ezurlalias_ml WHERE text = 'from ezfake where'" => array( 'ezurlalias_ml' ),
        );
        foreach ( $cases as $sql => $want )
        {
            $got = eZDBQueryCache::readTables( $sql );
            $this->assertIsArray( $got, $sql );
            sort( $got );
            sort( $want );
            $this->assertSame( $want, $got, $sql );
        }
        foreach ( array(
            'UPDATE ezcontentobject SET name = 1',
            'SELECT * FROM ezsession WHERE expiration_time > UNIX_TIMESTAMP()',
            'SELECT NOW() FROM ezcontentobject',
            'SELECT * FROM ezcontentobject ORDER BY RAND()',
            'SELECT * FROM ezcontentobject WHERE id = 1 FOR UPDATE',
            'SELECT SQL_CALC_FOUND_ROWS * FROM ezcontentobject',
            'SELECT * FROM ( SELECT id FROM ezcontentobject ) x',
            'SELECT @@version',
        ) as $sql )
            $this->assertNull( eZDBQueryCache::readTables( $sql ), $sql );
    }

    /** QC-02 */
    public function testWrittenTables()
    {
        $cases = array(
            'INSERT INTO ezcontentobject (id) VALUES (1)' => array( 'ezcontentobject' ),
            'INSERT IGNORE INTO `ezurl` (id) VALUES (1)' => array( 'ezurl' ),
            'REPLACE INTO ezpreferences (a) VALUES (1)' => array( 'ezpreferences' ),
            'UPDATE ezcontentobject_tree SET is_hidden = 1 WHERE node_id = 2' => array( 'ezcontentobject_tree' ),
            'UPDATE a, b SET a.x = b.x WHERE a.id = b.id' => array( 'a', 'b' ),
            'DELETE FROM ezcontentobject_link WHERE id = 1' => array( 'ezcontentobject_link' ),
            'DELETE a FROM a JOIN b ON a.x = b.x WHERE 1' => array( 'a', 'b' ),
            'TRUNCATE TABLE ezsearch_word' => array( 'ezsearch_word' ),
            'CREATE TABLE foo (id int)' => array( 'foo' ),
            'ALTER TABLE ezcontentobject ADD x int' => array( 'ezcontentobject' ),
            'DROP TABLE IF EXISTS foo' => array( 'foo' ),
            'CREATE INDEX i ON ezcontentobject (x)' => array( 'ezcontentobject' ),
            'RENAME TABLE a TO b' => array( 'a', 'b' ),
        );
        foreach ( $cases as $sql => $want )
        {
            $got = eZDBQueryCache::writtenTables( $sql );
            $this->assertIsArray( $got, $sql );
            sort( $got );
            sort( $want );
            $this->assertSame( $want, $got, $sql );
        }
        $this->assertNull( eZDBQueryCache::writtenTables( 'CALL some_procedure()' ) );
    }

    /** QC-03 */
    public function testMissStoreHit()
    {
        $rows = array( array( 'id' => 1, 'name' => 'Home' ), array( 'id' => 2, 'name' => 'Media' ) );
        $this->assertNull( $this->select( 'SELECT id, name FROM ezcontentobject', $rows ) );
        $this->assertSame( $rows, $this->select( 'SELECT id, name FROM ezcontentobject' ) );
        $this->assertSame( 1, eZDBQueryCache::$stats['hits'] );
        $this->assertSame( 1, eZDBQueryCache::$stats['misses'] );
        $this->assertSame( 1, eZDBQueryCache::$stats['stores'] );
    }

    /** QC-04 */
    public function testWriteToAReadTableMakesItStale()
    {
        $sql = 'SELECT * FROM ezcontentobject o, ezcontentobject_name n WHERE o.id = n.contentobject_id';
        $this->select( $sql );
        $this->write( 'UPDATE ezsection SET name = 1' );
        $this->assertNotNull( $this->select( $sql ), 'a write to another table leaves it current' );
        $this->write( "UPDATE ezcontentobject_name SET name = 'x'" );
        $this->assertNull( $this->select( $sql ), 'a write to a table it read makes it stale' );
        $this->assertNotNull( $this->select( $sql ), 'stored again after the write, and current' );
        $this->assertLessThanOrEqual( microtime( true ), eZDBQueryCache::freshState()['tables']['ezcontentobject_name'] );
    }

    /** QC-05 */
    public function testUnreadableWriteAndClearMakeEverythingStale()
    {
        $this->select( 'SELECT * FROM ezcontentobject' );
        $this->write( 'CALL some_procedure()' );
        $this->assertNull( $this->select( 'SELECT * FROM ezcontentobject' ), 'unreadable write' );

        $generation = eZDBQueryCache::freshState()['generation'];
        usleep( 5 );
        eZDBQueryCache::clearAll();
        usleep( 5 );
        $this->assertNull( $this->select( 'SELECT * FROM ezcontentobject' ), 'clear' );
        $this->assertGreaterThan( $generation, eZDBQueryCache::freshState()['generation'] );
    }

    /** QC-06 */
    public function testTransactions()
    {
        $sql = 'SELECT * FROM ezcontentobject';
        $this->select( $sql );

        $this->db->TransactionCounter = 1;
        $ticket = eZDBQueryCache::lookup( $this->db, $sql, array(), $hit );
        $this->assertNull( $hit, 'no answer inside a transaction' );
        $this->assertNull( $ticket, 'nothing stored inside a transaction' );
        $this->write( 'UPDATE ezcontentobject SET name = 1' );
        $this->db->TransactionCounter = 0;
        $this->assertNotNull( $this->select( $sql ), 'the write does not count before COMMIT' );
        eZDBQueryCache::afterCommit();
        usleep( 5 );
        $this->assertNull( $this->select( $sql ), 'it counts at COMMIT' );

        $this->select( $sql );
        $this->db->TransactionCounter = 1;
        $this->write( 'UPDATE ezcontentobject SET name = 2' );
        $this->db->TransactionCounter = 0;
        eZDBQueryCache::afterRollback();
        eZDBQueryCache::afterCommit();
        $this->assertNotNull( $this->select( $sql ), 'a rolled back write is forgotten' );
    }

    /** QC-07 */
    public function testTemporaryTables()
    {
        $this->assertTrue( eZDBQueryCache::isTemporary( 'ezproductcoll_tmp_972' ) );
        $this->assertTrue( eZDBQueryCache::isTemporary( 'ezsearch_tmp_0' ) );
        $this->assertTrue( eZDBQueryCache::isTemporary( 'eznode_count_12' ) );
        $this->assertFalse( eZDBQueryCache::isTemporary( 'ezcontentobject' ) );

        $this->write( 'CREATE TEMPORARY TABLE scratch_list ( id int )' );
        $this->assertTrue( eZDBQueryCache::isTemporary( 'scratch_list' ) );
        $this->write( 'INSERT INTO scratch_list VALUES (1)' );
        $this->write( 'INSERT INTO ezsearch_tmp_3 VALUES (1)' );
        $tables = eZDBQueryCache::freshState()['tables'];
        $this->assertArrayNotHasKey( 'scratch_list', $tables );
        $this->assertArrayNotHasKey( 'ezsearch_tmp_3', $tables );

        foreach ( array( 'SELECT id FROM scratch_list', 'SELECT i.x FROM ezproductcollection_item i, ezproductcoll_tmp_5 t WHERE i.id = t.id' ) as $sql )
        {
            $this->assertNull( $this->select( $sql ) );
            $this->assertNull( $this->select( $sql ), 'not cached: ' . $sql );
        }
        $this->assertSame( 0, eZDBQueryCache::$stats['hits'] );
        $this->assertSame( 4, eZDBQueryCache::$stats['uncacheable'] );
    }

    /** QC-08 */
    public function testExcludeTablesMaxRowsMaxAge()
    {
        $this->newRequest( array( 'exclude' => array( 'ezsession' => true ), 'maxRows' => 2 ) );
        $this->select( 'SELECT * FROM ezsession' );
        $this->assertNull( $this->select( 'SELECT * FROM ezsession' ), 'excluded table' );

        $three = array( array( 'id' => 1 ), array( 'id' => 2 ), array( 'id' => 3 ) );
        $this->select( 'SELECT * FROM ezcontentobject', $three );
        $this->assertNull( $this->select( 'SELECT * FROM ezcontentobject' ), 'more rows than MaxRows' );

        $this->newRequest( array( 'maxAge' => 1 ) );
        $this->select( 'SELECT * FROM ezsection' );
        $this->assertNotNull( $this->select( 'SELECT * FROM ezsection' ) );
        usleep( 1100000 );
        $this->assertNull( $this->select( 'SELECT * FROM ezsection' ), 'older than MaxAge' );
    }

    /** QC-09 */
    public function testKeys()
    {
        $sql = 'SELECT * FROM ezcontentobject';
        $this->select( $sql, array( array( 'id' => 1 ) ) );
        $this->assertNull( $this->select( $sql . ' WHERE id = 2' ), 'another statement' );
        $this->assertNull( $this->select( $sql, array(), array( 'limit' => 10 ) ), 'other parameters' );
        $this->assertNull( $this->select( $sql, array(), array(), $this->driver( 'other' ) ), 'another database' );
        $this->assertNull( $this->select( $sql, array(), array(), $this->driver( 'site', 'replica' ) ), 'another server' );
        $this->assertSame( array( array( 'id' => 1 ) ), $this->select( $sql ) );
    }

    /** QC-10 */
    public function testStoredResultIsACopy()
    {
        $rows = array( array( 'id' => 1 ) );
        $this->select( 'SELECT * FROM ezcontentobject', $rows );
        $rows[0]['id'] = 99;
        $got = $this->select( 'SELECT * FROM ezcontentobject' );
        $got[0]['id'] = 42;
        $this->assertSame( array( array( 'id' => 1 ) ), $this->select( 'SELECT * FROM ezcontentobject' ) );
    }

    /** QC-11 */
    public function testModeOff()
    {
        $this->newRequest( array( 'mode' => 'off' ) );
        $this->assertFalse( eZDBQueryCache::enabled() );
        $this->select( 'SELECT * FROM ezcontentobject' );
        $this->assertNull( $this->select( 'SELECT * FROM ezcontentobject' ) );
        $this->write( 'UPDATE ezcontentobject SET x = 1' );
        $this->assertFileDoesNotExist( $this->dir . '/state.ser' );
        $this->assertSame( 0, array_sum( eZDBQueryCache::$stats ) );
    }

    /** QC-12 */
    public function testWriteByAnotherProcess()
    {
        $read = 'SELECT * FROM ezcontentobject';
        $control = 'SELECT * FROM ezsection';
        $this->select( $read );
        $this->select( $control );
        usleep( 5 );
        // What another process's updateState() leaves on disk. This process
        // keeps its copy of the state for up to a second, then reads it again;
        // the result is still in this request's memo, so only the state can
        // make it stale.
        $state = eZDBQueryCache::freshState();
        $state['tables']['ezcontentobject'] = microtime( true );
        file_put_contents( $this->dir . '/state.ser', serialize( $state ) );
        usleep( 1100000 );
        $this->assertNotNull( $this->select( $control ), 'a table the other process did not write is still current' );
        $this->assertNull( $this->select( $read ), 'the table it wrote is stale' );
    }

    /** QC-14 */
    public function testCatalogueTablesAreNeverCached()
    {
        foreach ( array( "SELECT name FROM sqlite_master WHERE type='table'", 'SELECT * FROM sqlite_stat1', 'SELECT relname FROM pg_class' ) as $sql )
        {
            $this->select( $sql );
            $this->assertNull( $this->select( $sql ), 'not cached: ' . $sql );
        }
        $this->assertTrue( eZDBQueryCache::isSystemTable( 'information_schema.tables' ) || eZDBQueryCache::isSystemTable( 'information_schema' ) );
        $this->assertFalse( eZDBQueryCache::isSystemTable( 'ezcontentobject' ) );
        // Keys differ by parameters and stay stable.
        $this->assertSame( eZDBQueryCache::key( $this->db, 'SELECT 1 FROM a', array( 'limit' => 5 ) ), eZDBQueryCache::key( $this->db, 'SELECT 1 FROM a', array( 'limit' => 5 ) ) );
        $this->assertNotSame( eZDBQueryCache::key( $this->db, 'SELECT 1 FROM a', array( 'limit' => 5 ) ), eZDBQueryCache::key( $this->db, 'SELECT 1 FROM a', array( 'limit' => 6 ) ) );
    }

    /** QC-13 */
    public function testHitWithoutParsingButNotForAnExcludedTable()
    {
        $sql = 'SELECT * FROM ezcontentobject o, ezsection s WHERE o.section_id = s.id';
        $this->select( $sql );
        $this->assertNotNull( $this->select( $sql ), 'answered from the entry' );
        // Excluding a table after the result was stored: the entry must not answer.
        eZDBQueryCache::setSettings( array( 'mode' => 'request', 'maxAge' => 300, 'maxRows' => 5000, 'exclude' => array( 'ezsection' => true ) ) );
        $this->assertNull( $this->select( $sql ), 'a table it read is now excluded' );
        $this->assertNull( $this->select( $sql ), 'and it is not stored again' );
    }

    /** QC-15 */
    public function testOracleFormsAreNeverCachedAndPlsqlBlocksWrite()
    {
        // The Oracle driver reads every new row's id with "SELECT <sequence>.currval FROM DUAL":
        // an answer from the cache would hand out an id twice.
        foreach ( array( 'SELECT s_contentobject.currval from DUAL', 'SELECT S_NODE.NEXTVAL FROM dual',
                         'SELECT SYSDATE FROM dual', 'SELECT id FROM ezorder WHERE created > SYSTIMESTAMP',
                         'SELECT SYS_GUID() FROM dual', "SELECT SYS_CONTEXT( 'USERENV', 'SID' ) FROM dual",
                         'SELECT DBMS_RANDOM.VALUE FROM dual', 'SELECT table_name FROM user_tables',
                         'SELECT column_name FROM ALL_TAB_COLUMNS WHERE table_name = 1', 'SELECT sid FROM v$session' ) as $sql )
        {
            $this->select( $sql );
            $this->assertNull( $this->select( $sql ), 'not cached: ' . $sql );
        }
        $this->assertTrue( eZDBQueryCache::isSystemTable( 'user_sequences' ) );
        $this->assertTrue( eZDBQueryCache::isSystemTable( 'gv$instance' ) );
        $this->assertFalse( eZDBQueryCache::isSystemTable( 'ezuser' ) );

        $sql = 'SELECT * FROM ezcontentobject';
        $this->select( $sql );
        $this->write( 'BEGIN' );
        $this->assertNotNull( $this->select( $sql ), 'a bare BEGIN (a transaction) writes nothing' );
        $this->write( 'BEGIN UPDATE ezcontentobject SET status = 1; END;' );
        $this->assertNull( $this->select( $sql ), 'a PL/SQL block makes everything stale' );
        $this->select( 'SELECT * FROM ezsection' );
        $this->write( 'DECLARE n NUMBER; BEGIN n := 1; END;' );
        $this->assertNull( $this->select( 'SELECT * FROM ezsection' ), 'so does a DECLARE block' );
    }
}
