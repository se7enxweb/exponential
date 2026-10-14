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
 *  QC-15 — Oracle: sequences, the clock, the SCN and the catalogue are never cached; PL/SQL blocks write
 *  QC-16 — Oracle: an ALTER SESSION SET of NLS_*, CURSOR_SHARING, TIME_ZONE writes nothing; any other makes everything stale
 *  QC-17 — The debug report: accumulators, one timing point, the bottom report as text and HTML
 *  QC-18 — No debug report without the debug output or with Report=disabled
 *  QC-19 — Oracle: connections with the same session settings share results, also with NLS_COMP/NLS_SORT
 *  QC-20 — Oracle: a result is never answered to a session with other settings
 *  QC-21 — Literals are masked under SQL-standard and MySQL backslash quoting
 *  QC-22 — The debug report starts again after eZDebug::reset() and per request
 *  QC-23 — debugSnapshot(): the report's figures for the Exp Debug bar, null without the debug output
 *  QC-24 — inventory(): the APCu entries by table and the largest ones, without statement text
 *  QC-25 — storage(): Storage=file, auto (APCu, else files; none on the command line), apcu
 *  QC-26 — The file storage: one file per result, a hit in the next request, stale after a write
 *  QC-27 — A result file never makes objects; a broken file is a miss
 *  QC-28 — cleanupFiles(), status() and inventory() with the files; a clear removes them
 *  QC-29 — A result file is only ever named by a key (hex digits): no path leaves the directory
 *  QC-30 — The requests sweep the result files at most once per interval, one at a time, within a time budget
 *  QC-31 — Storage=auto uses the files in every web process, a Velocity worker (cli SAPI) included
 *  QC-32 — apcuRoom(): room only with more than ApcuReserve percent of APCu free
 *  QC-33 — Below the reserve a result stays in the request and is counted; files have no reserve
 *  QC-34 — purgeExpired() frees the memory of the expired entries, once a minute for the pool
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
        eZDBQueryCache::setApcuFree( null );
        $this->removeDir( $this->dir );
    }

    private function removeDir( $dir )
    {
        foreach ( array_merge( glob( $dir . '/*' ), glob( $dir . '/.[!.]*' ) ) as $f )
            is_dir( $f ) ? $this->removeDir( $f ) : unlink( $f );
        rmdir( $dir );
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

    /** QC-16 */
    public function testOracleAlterSessionOfTheDriverWritesNothing()
    {
        // ezoracle sets these with one ALTER SESSION on every connect
        $sql = 'SELECT * FROM ezcontentobject';
        $this->select( $sql );
        $generation = eZDBQueryCache::freshState()['generation'];
        foreach ( array( "ALTER SESSION SET NLS_NUMERIC_CHARACTERS='. ' NLS_LENGTH_SEMANTICS=CHAR CURSOR_SHARING=FORCE",
                         "  alter session set cursor_sharing = EXACT;",
                         "ALTER SESSION\n\tSET NLS_LENGTH_SEMANTICS=CHAR\tNLS_NUMERIC_CHARACTERS = '.'''",
                         "ALTER SESSION SET NLS_NUMERIC_CHARACTERS='. ' NLS_COMP=LINGUISTIC NLS_SORT=BINARY_CI",
                         "ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'",
                         "ALTER SESSION SET TIME_ZONE='+02:00'",
                         "ALTER SESSION SET CURSOR_SHARING=FORCE NLS_SORT=GERMAN",
                         "ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.''' NLS_SORT='x'" ) as $alter )
        {
            $this->assertTrue( eZDBQueryCache::isNeutralAlterSession( $alter ), $alter );
            $this->write( $alter );
        }
        $this->assertSame( $generation, eZDBQueryCache::freshState()['generation'], 'session settings are no write' );

        // settings that can change results in other ways stay unreadable writes
        foreach ( array( 'ALTER SESSION SET CURRENT_SCHEMA = other',
                         'ALTER SESSION SET CONTAINER = pdb2',
                         'ALTER SESSION SET',
                         'ALTER SESSION ENABLE PARALLEL DML',
                         "ALTER SESSION SET CURSOR_SHARING=FORCE; DROP TABLE ezcontentobject",
                         "/* x */ ALTER SESSION SET CURSOR_SHARING=FORCE",
                         'ALTER SESSION SET CURSOR_SHARING=',
                         "ALTER SESSION SET NLS_SORT=GERMAN CURRENT_SCHEMA=other",
                         "ALTER SESSION SET EVENTS '10046 trace name context forever'" ) as $alter )
        {
            $this->assertFalse( eZDBQueryCache::isNeutralAlterSession( $alter ), $alter );
            $this->select( $sql );
            $this->write( $alter );
            $this->assertNull( $this->select( $sql ), 'stale after: ' . $alter );
        }

        $this->select( $sql );
        $this->write( 'ALTER TABLE ezcontentobject ADD foo INTEGER' );
        $this->assertNull( $this->select( $sql ), 'ALTER TABLE still makes its table stale' );
    }

    /** A new connection of ezoracle: a new driver object and its ALTER SESSION, as initializeSession() runs it. */
    private function oracleConnection( $caseInsensitive = false, $sort = 'BINARY_CI' )
    {
        $db = $this->driver();
        $alter = "ALTER SESSION SET NLS_NUMERIC_CHARACTERS='. ' NLS_LENGTH_SEMANTICS=CHAR CURSOR_SHARING=EXACT";
        if ( $caseInsensitive )
            $alter .= ' NLS_COMP=LINGUISTIC NLS_SORT=' . $sort;
        eZDBQueryCache::noteWrite( $db, $alter );
        return $db;
    }

    /** QC-19 */
    public function testOracleConnectionsHitAcrossRequestsAlsoCaseInsensitive()
    {
        $sql = 'SELECT * FROM ezcontentobject';
        foreach ( array( false, true ) as $caseInsensitive )
        {
            $this->newRequest();
            $this->select( $sql, array( array( 'id' => 1 ) ), array(), $this->oracleConnection( $caseInsensitive ) );
            $generation = eZDBQueryCache::freshState()['generation'];
            // the next connections (the next requests) with the same settings
            for ( $connection = 0; $connection < 2; $connection++ )
            {
                $db = $this->oracleConnection( $caseInsensitive );
                $this->assertSame( $generation, eZDBQueryCache::freshState()['generation'], 'connecting writes nothing' );
                $this->assertSame( array( array( 'id' => 1 ) ), $this->select( $sql, array(), array(), $db ),
                                   'answered to another connection with the same settings' . ( $caseInsensitive ? ' (OracleCaseInsensitive)' : '' ) );
            }
        }
        // a connection that set nothing keys as before
        $this->assertSame( '', eZDBQueryCache::sessionKey( $this->driver() ) );
        $this->assertSame( "CURSOR_SHARING=EXACT NLS_COMP=LINGUISTIC NLS_LENGTH_SEMANTICS=CHAR NLS_NUMERIC_CHARACTERS='. ' NLS_SORT=BINARY_CI ",
                           eZDBQueryCache::sessionKey( $this->oracleConnection( true ) ) );
    }

    /** QC-20 */
    public function testAResultIsAnsweredOnlyToASessionWithTheSameSettings()
    {
        $sql = "SELECT name FROM ezcontentobject ORDER BY name";
        $binary = $this->oracleConnection();
        $german = $this->oracleConnection( true, 'GERMAN' );
        $this->select( $sql, array( array( 'name' => 'B' ), array( 'name' => 'a' ) ), array(), $binary );
        $this->assertNull( $this->select( $sql, array( array( 'name' => 'a' ), array( 'name' => 'B' ) ), array(), $german ),
                           'a sort of another session is not answered' );
        $this->assertSame( array( array( 'name' => 'B' ), array( 'name' => 'a' ) ), $this->select( $sql, array(), array(), $binary ) );
        $this->assertSame( array( array( 'name' => 'a' ), array( 'name' => 'B' ) ), $this->select( $sql, array(), array(), $german ) );
        $this->assertSame( array( array( 'name' => 'a' ), array( 'name' => 'B' ) ), $this->select( $sql, array(), array(), $this->oracleConnection( true, 'german' ) ),
                           'names and unquoted values are compared in upper case' );

        // a later ALTER SESSION on the same connection: from then on its own key, the last value of a parameter wins
        eZDBQueryCache::noteWrite( $binary, 'ALTER SESSION SET NLS_SORT=GERMAN NLS_COMP=LINGUISTIC' );
        $this->assertSame( eZDBQueryCache::sessionKey( $german ), eZDBQueryCache::sessionKey( $binary ) );
        $this->assertSame( array( array( 'name' => 'a' ), array( 'name' => 'B' ) ), $this->select( $sql, array(), array(), $binary ) );
        eZDBQueryCache::noteWrite( $binary, 'ALTER SESSION SET NLS_SORT=BINARY_CI' );
        $this->assertNull( $this->select( $sql, array( array( 'name' => 'x' ) ), array(), $binary ) );

        // with the cache off nothing is noted
        eZDBQueryCache::setSettings( array( 'mode' => 'off' ) + eZDBQueryCache::settings() );
        $off = $this->driver();
        eZDBQueryCache::noteWrite( $off, 'ALTER SESSION SET NLS_SORT=GERMAN' );
        $this->assertSame( '', eZDBQueryCache::sessionKey( $off ) );
    }

    /** Runs $test with a fresh eZDebug (debug output on or off, logs in the test directory), and puts the old one back. */
    private function withDebug( $on, callable $test )
    {
        if ( !class_exists( 'eZDebug' ) )
            $this->markTestSkipped( 'needs eZDebug' );
        $keys = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly' );
        $saved = array();
        foreach ( $keys as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $debug = new eZDebug();
        foreach ( $debug->LogFiles as $level => $file )
            $debug->LogFiles[$level] = array( $this->dir . '/', $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $debug;
        $GLOBALS['eZDebugEnabled'] = $on;
        $GLOBALS['eZDebugLogOnly'] = false;
        try
        {
            $test( $debug );
        }
        finally
        {
            foreach ( $saved as $key => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$key] );
                else
                    $GLOBALS[$key] = $value[0];
            }
        }
    }

    /** QC-17 */
    public function testDebugReport()
    {
        $this->withDebug( true, function ( $debug )
        {
            $this->newRequest();
            $sql = 'SELECT * FROM ezcontentobject';
            $this->select( $sql );
            $this->assertNotNull( $this->select( $sql ), 'hit' );
            $this->select( 'SELECT NOW() FROM ezsection' );
            $this->write( 'CALL some_procedure( \'secret\' ) /* <b> */' );

            foreach ( array( 'querycache_hit' => 1, 'querycache_miss' => 1, 'querycache_store' => 1, 'querycache_skip' => 1 ) as $key => $count )
            {
                $this->assertSame( $count, $debug->TimeAccumulatorList[$key]['count'], $key );
                $this->assertSame( 'querycache_total', $debug->TimeAccumulatorList[$key]['in_group'], $key );
            }
            $this->assertSame( array( 'eZDBQueryCache', 'debugReport' ), $debug->bottomReportsList['Query cache'] );
            $points = array_column( $debug->TimePoints, 'Description' );
            $this->assertCount( 1, preg_grep( '/^Query cache: state read \(generation \d+\)$/', $points ), 'one timing point per request' );

            $text = eZDBQueryCache::debugReport( false );
            $this->assertMatchesRegularExpression( '/^Hits +1$/m', $text );
            $this->assertMatchesRegularExpression( '/^Misses +1$/m', $text );
            $this->assertMatchesRegularExpression( '/^Hit rate +50 %$/m', $text );
            $this->assertMatchesRegularExpression( '/^Not cached: statement \(volatile or tables not readable\) +1$/m', $text );
            $this->assertMatchesRegularExpression( '/^Database time saved \(estimated\) +[0-9.]+ ms$/m', $text );
            $this->assertStringContainsString( "Made everything stale: CALL some_procedure( '?' ) /* <b> */", $text, 'literals masked' );
            $this->assertStringNotContainsString( 'secret', $text );

            $html = eZDBQueryCache::debugReport( true );
            $this->assertStringContainsString( '<table id="querycache"', $html );
            $this->assertStringContainsString( 'CALL some_procedure( &#039;?&#039; ) /* &lt;b&gt; */', $html );
            $this->assertStringNotContainsString( '<b>', $html );
        } );
    }

    /** QC-18 */
    public function testDebugReportStaysOffWithoutDebugOutputOrWhenDisabled()
    {
        $this->withDebug( false, function ( $debug )
        {
            $this->newRequest();
            $this->select( 'SELECT * FROM ezcontentobject' );
            $this->select( 'SELECT * FROM ezcontentobject' );
            $this->assertSame( array(), $debug->TimeAccumulatorList );
            $this->assertSame( array(), $debug->bottomReportsList );
            $this->assertSame( '', eZDBQueryCache::debugReport( true ) );
        } );
        $this->withDebug( true, function ( $debug )
        {
            // eZDebug::reset() between two jobs of a worker: the report comes back with the next statement
            $this->newRequest();
            $this->select( 'SELECT * FROM ezcontentobject' );
            $debug->bottomReportsList = array();
            $debug->TimeAccumulatorList = array();
            $this->select( 'SELECT * FROM ezcontentobject' );
            $this->assertArrayHasKey( 'Query cache', $debug->bottomReportsList );
            $this->assertSame( 1, $debug->TimeAccumulatorList['querycache_hit']['count'] );
        } );
        $this->withDebug( true, function ( $debug )
        {
            $this->newRequest( array( 'report' => false ) );
            $this->select( 'SELECT * FROM ezcontentobject' );
            $this->assertSame( array(), $debug->TimeAccumulatorList, 'querycache.ini [DebugSettings] Report=disabled' );
            $this->assertSame( '', eZDBQueryCache::debugReport( true ) );
        } );
    }

    /** QC-21 */
    public function testLiteralsAreMaskedUnderBothQuotingRules()
    {
        foreach ( array(
            "SELECT 1" => "SELECT 1",
            'CALL p( "ident" )' => 'CALL p( "ident" )',
            "UPDATE t SET a = 'x''y' WHERE b = 'z'" => "UPDATE t SET a = '?' WHERE b = '?'",
            "CALL p( 'it\\'s secret' )" => "CALL p( '?'", // the standard reading ends the literal at \', the rest is masked to the end
            "CALL p( 'a\\\\' , 'b' )" => "CALL p( '?' , '?' )",
            "CALL p( 'C:\\', 'hidden' )" => "CALL p( '?'",
            "CALL p( 'open" => "CALL p( '?'",
            "CALL p( 'ends in a backslash\\" => "CALL p( '?'",
            "CALL p( 'two\nlines' ), q( 'x' )" => "CALL p( '?' ), q( '?' )",
        ) as $sql => $want )
            $this->assertSame( $want, eZDBQueryCache::maskLiterals( $sql ), $sql );

        $this->withDebug( true, function ( $debug )
        {
            $this->newRequest();
            $this->select( 'SELECT * FROM ezcontentobject' );
            // as MySQL escapes (\' and \\), and as Oracle, PostgreSQL and SQLite quote ('' and a plain \)
            $this->write( "CALL some_procedure( 'it\\'s secret', 'C:\\\\', 'hidden' )" );
            $this->write( "CALL ora_procedure( 'it''s secret', 'C:\\', 'hidden' )" );
            $this->write( 'CALL other( \'' . str_repeat( 'x', 5000 ) . 'tail\' )' );
            $text = eZDBQueryCache::debugReport( false );
            foreach ( array( 'secret', 's secret', 'hidden', 'tail', 'xxx' ) as $part )
                $this->assertStringNotContainsString( $part, $text, 'no part of a literal: ' . $part );
            $this->assertStringContainsString( "Made everything stale: CALL some_procedure( '?'", $text );
            $this->assertStringContainsString( "Made everything stale: CALL ora_procedure( '?', '?'\n", $text, 'from C:\\ on both readings disagree: masked to the end' );
            $this->assertStringContainsString( "Made everything stale: CALL other( '?'", $text );
        } );
    }

    /** QC-22 */
    public function testDebugReportStartsAgainAfterAResetAndPerRequest()
    {
        $this->withDebug( true, function ( $debug )
        {
            $points = function () use ( $debug )
            {
                return count( preg_grep( '/^Query cache: state read \(generation \d+\)$/', array_column( $debug->TimePoints, 'Description' ) ) );
            };
            $sql = 'SELECT * FROM ezcontentobject';
            $this->newRequest();
            $this->select( $sql );
            $this->assertNotNull( $this->select( $sql ) );
            $this->write( 'UPDATE ezsection SET name = 1' );
            $text = eZDBQueryCache::debugReport( false );
            $this->assertMatchesRegularExpression( '/^Hits +1$/m', $text );
            $this->assertMatchesRegularExpression( '/^Writes +1$/m', $text );
            $this->assertStringNotContainsString( 'Made everything stale', $text, 'a write of known tables is only counted' );
            $this->assertSame( 1, $points() );

            // eZDBQueryCache::flushStats() mid-request (Setup > System information) leaves the report as it is
            eZDBQueryCache::flushStats();
            $this->assertMatchesRegularExpression( '/^Hits +1$/m', eZDBQueryCache::debugReport( false ) );

            // eZDebug::reset() in the same process (a worker between two jobs), the state still current
            $debug->reset();
            $this->assertNotNull( $this->select( $sql ) );
            $this->assertArrayHasKey( 'Query cache', $debug->bottomReportsList );
            $this->assertSame( 2, $points(), 'the timing point comes again after a reset' );
            $text = eZDBQueryCache::debugReport( false );
            $this->assertMatchesRegularExpression( '/^Hits +1$/m', $text, 'the job counts its own statements' );
            $this->assertMatchesRegularExpression( '/^Misses +0$/m', $text );
            $this->assertMatchesRegularExpression( '/^Writes +0$/m', $text );
            $this->assertSame( 1, $debug->TimeAccumulatorList['querycache_hit']['count'] );

            // a reset after a write that made everything stale: the next statement is a miss that reads no state,
            // and the timing point still comes, with the generation this process holds
            $this->write( 'DECLARE n NUMBER; BEGIN n := 1; END;' );
            $debug->reset();
            $this->assertNull( $this->select( $sql ) );
            $this->assertSame( 3, $points() );

            // the next request of a persistent worker: nothing carried over; the report reads no state itself
            $this->newRequest();
            $this->select( $sql );
            $text = eZDBQueryCache::debugReport( false );
            $this->assertMatchesRegularExpression( '/^Hits +0$/m', $text );
            $this->assertMatchesRegularExpression( '/^Misses +1$/m', $text );
            $this->assertMatchesRegularExpression( '/^Generation +not read$/m', $text );
            $this->assertSame( 3, $points(), 'no state read yet, no timing point, also not from the report' );
            $this->assertNotNull( $this->select( $sql ) );
            $this->assertSame( 4, $points(), 'the first look at the state' );

            // the debug output switched off, then a reset: nothing registered, no accumulator half made
            $GLOBALS['eZDebugEnabled'] = false;
            $debug->reset();
            $this->select( $sql );
            $this->write( 'DECLARE n NUMBER; BEGIN n := 1; END;' );
            $this->assertSame( array(), $debug->TimeAccumulatorList );
            $this->assertSame( array(), $debug->bottomReportsList );
        } );
    }

    /** QC-23 */
    public function testDebugSnapshot()
    {
        $this->withDebug( true, function ( $debug )
        {
            $this->newRequest();
            $this->assertNull( eZDBQueryCache::debugSnapshot(), 'nothing measured yet' );
            $sql = 'SELECT * FROM ezcontentobject';
            $this->select( $sql );
            $this->select( $sql );
            $this->select( 'SELECT NOW() FROM ezsection' );
            $this->write( 'CALL some_procedure()' );
            $snap = eZDBQueryCache::debugSnapshot();
            $this->assertSame( 'request', $snap['mode'] );
            $this->assertSame( 1, $snap['hits'] );
            $this->assertSame( 1, $snap['misses'] );
            $this->assertSame( 1, $snap['stores'] );
            $this->assertSame( 1, $snap['not_cached'] );
            $this->assertSame( 1, $snap['writes'] );
            $this->assertSame( 50.0, $snap['hit_rate'] );
            $this->assertSame( array( 'CALL some_procedure()' ), $snap['stale'] );
            $this->assertIsFloat( $snap['saved_ms'] );
            $this->assertIsInt( $snap['generation'] );
            // the report's own counters: Setup > System information flushing the request's counters changes nothing
            eZDBQueryCache::flushStats();
            $this->assertSame( $snap, eZDBQueryCache::debugSnapshot() );
        } );
        $this->withDebug( false, function ( $debug )
        {
            $this->newRequest();
            $this->select( 'SELECT * FROM ezcontentobject' );
            $this->assertNull( eZDBQueryCache::debugSnapshot(), 'without the debug output' );
        } );
    }

    /** QC-24 */
    public function testInventoryGroupsTheApcuEntriesByTable()
    {
        if ( !function_exists( 'apcu_enabled' ) || !apcu_enabled() || !class_exists( 'APCUIterator' ) )
            $this->markTestSkipped( 'needs APCu (apc.enable_cli=1)' );
        apcu_delete( new APCUIterator( '/^ezqc/' ) );
        try
        {
            $this->newRequest( array( 'mode' => 'shared', 'storage' => 'apcu' ) );
            $this->select( 'SELECT * FROM ezcontentobject a, ezcontentobject_tree t WHERE a.id = t.contentobject_id',
                           array( array( 'id' => 1 ), array( 'id' => 2 ), array( 'id' => 3 ) ) );
            $this->select( 'SELECT * FROM ezcontentobject WHERE id = 1' );
            $this->newRequest( array( 'mode' => 'shared', 'storage' => 'apcu' ) );
            $this->assertNotNull( $this->select( 'SELECT * FROM ezcontentobject WHERE id = 1' ), 'a hit from APCu' );

            $inv = eZDBQueryCache::inventory( 10 );
            $this->assertTrue( $inv['apcu'] );
            $this->assertSame( 2, $inv['entries_read'] );
            $this->assertFalse( $inv['truncated'] );
            $byTable = array_column( $inv['tables'], null, 'table' );
            $this->assertSame( 2, $byTable['ezcontentobject']['entries'], 'an entry that read two tables counts for each' );
            $this->assertSame( 4, $byTable['ezcontentobject']['rows'] );
            $this->assertSame( 1, $byTable['ezcontentobject_tree']['entries'] );
            $this->assertSame( 1, $byTable['ezcontentobject']['hits'] );
            $this->assertCount( 2, $inv['largest'] );
            $this->assertGreaterThanOrEqual( $inv['largest'][1]['bytes'], $inv['largest'][0]['bytes'], 'largest first' );
            $this->assertSame( 12, strlen( $inv['largest'][0]['key'] ) );
            $this->assertArrayNotHasKey( 'sql', $inv['largest'][0], 'no statement text' );
            $this->assertNotNull( $inv['sma'] );
            $this->assertArrayNotHasKey( 'state_file', $inv, 'the state file path does not reach the browser' );
        }
        finally
        {
            apcu_delete( new APCUIterator( '/^ezqc/' ) );
        }
    }

    /** QC-25 */
    public function testStorageFollowsTheSettingAndApcu()
    {
        $apcu = function_exists( 'apcu_enabled' ) && apcu_enabled();
        $this->newRequest( array( 'mode' => 'request' ) );
        $this->assertNull( eZDBQueryCache::storage(), 'request mode keeps nothing across requests' );
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $this->assertSame( 'file', eZDBQueryCache::storage(), 'Storage=file even with APCu' );
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'auto' ) );
        $this->assertSame( $apcu ? 'apcu' : null, eZDBQueryCache::storage(), 'auto on the command line: no files without APCu' );
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'apcu' ) );
        $this->assertSame( $apcu ? 'apcu' : null, eZDBQueryCache::storage() );
        $this->newRequest( array( 'mode' => 'shared' ) );
        $this->assertSame( $apcu ? 'apcu' : null, eZDBQueryCache::storage(), 'without the setting: auto' );
    }

    /** QC-26 */
    public function testFileStorageAnswersTheNextRequestUntilAWrite()
    {
        $sql = 'SELECT * FROM ezcontentobject WHERE id = 1';
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $this->assertNull( $this->select( $sql, array( array( 'id' => 1, 'name' => 'a' ) ) ) );
        $files = glob( eZDBQueryCache::entriesDir() . '/*/*' );
        $this->assertCount( 1, $files, 'one file per result' );
        $this->assertSame( substr( basename( $files[0] ), 0, 2 ), basename( dirname( $files[0] ) ) );

        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $this->assertSame( array( array( 'id' => 1, 'name' => 'a' ) ), $this->select( $sql ), 'a hit from the file' );

        $this->write( 'UPDATE ezcontentobject SET name = \'b\' WHERE id = 1' );
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $this->assertNull( $this->select( $sql, array( array( 'id' => 1, 'name' => 'b' ) ) ), 'stale after the write' );
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $this->assertSame( array( array( 'id' => 1, 'name' => 'b' ) ), $this->select( $sql ), 'the new rows' );
    }

    /** QC-27 */
    public function testFileStorageNeverMakesObjectsAndSkipsBrokenFiles()
    {
        $sql = 'SELECT * FROM ezsection';
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $this->select( $sql );
        $file = glob( eZDBQueryCache::entriesDir() . '/*/*' )[0];

        $entry = unserialize( file_get_contents( $file ) );
        $entry['rows'] = array( new ArrayObject( array( 1 ) ) );
        file_put_contents( $file, serialize( $entry ) );
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $hit = $this->select( $sql );
        $this->assertNotNull( $hit );
        $this->assertInstanceOf( '__PHP_Incomplete_Class', $hit[0], 'no object of a real class' );

        file_put_contents( $file, 'not serialized' );
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $this->assertNull( $this->select( $sql ), 'a broken file is a miss' );
    }

    /** QC-28 */
    public function testCleanupFilesRemovesExpiredClearedAndLeftOverFiles()
    {
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file', 'maxAge' => 300 ) );
        $this->select( 'SELECT * FROM ezsection' );
        $this->select( 'SELECT * FROM ezcontentobject' );
        $files = glob( eZDBQueryCache::entriesDir() . '/*/*' );
        $this->assertCount( 2, $files );
        touch( $files[0], time() - 400 );
        $tmp = dirname( $files[1] ) . '/' . basename( $files[1] ) . '.1.2.tmp';
        file_put_contents( $tmp, 'x' );
        touch( $tmp, time() - 120 );

        $out = eZDBQueryCache::cleanupFiles();
        $this->assertSame( 1, $out['removed'], 'older than MaxAge' );
        $this->assertSame( 1, $out['tmp'], 'left over by an aborted write' );
        $this->assertSame( 1, $out['kept'] );
        $this->assertFileDoesNotExist( $files[0] );
        $this->assertFileExists( $files[1] );

        $status = eZDBQueryCache::status();
        $this->assertSame( 'file', $status['storage'] );
        $this->assertSame( 1, $status['entries'] );
        $this->assertGreaterThan( 0, $status['memory'] );

        $inv = eZDBQueryCache::inventory( 10 );
        $this->assertArrayNotHasKey( 'entries_dir', $inv, 'the path of the result files does not reach the browser' );
        $this->assertArrayNotHasKey( 'state_file', $inv );
        $this->assertSame( 1, $inv['entries_read'] );
        $this->assertNull( $inv['largest'][0]['hits'], 'files count no hits' );
        $this->assertSame( 12, strlen( $inv['largest'][0]['key'] ) );

        sleep( 1 );
        eZDBQueryCache::clearAll();
        $this->assertSame( array(), glob( eZDBQueryCache::entriesDir() . '/*/*' ), 'clearing removes the files' );
    }

    /** QC-29 */
    public function testAnEntryFileIsOnlyEverNamedByAKey()
    {
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        $store = new ReflectionMethod( 'eZDBQueryCache', 'storeShared' );
        $fetch = new ReflectionMethod( 'eZDBQueryCache', 'fetchShared' );
        $file = new ReflectionMethod( 'eZDBQueryCache', 'entryFile' );
        if ( PHP_VERSION_ID < 80100 )
        {
            $store->setAccessible( true );
            $fetch->setAccessible( true );
            $file->setAccessible( true );
        }
        $entry = array( 'created' => microtime( true ), 'tables' => array( 'ezsection' ), 'rows' => array( array( 'id' => 1 ) ) );
        foreach ( array( '../../../escaped', '../x', 'ab/../../cd', '', 'ABCDEF0123456789', "0123456789abcdef\0" ) as $bad )
        {
            $this->assertNull( $file->invoke( null, $bad ), 'no file for ' . json_encode( $bad ) );
            $store->invoke( null, 'file', $bad, $entry );
            $this->assertNull( $fetch->invoke( null, 'file', $bad ) );
        }
        $this->assertSame( array(), glob( $this->dir . '/*/*/*' ), 'nothing written' );
        $this->assertFileDoesNotExist( dirname( $this->dir ) . '/escaped' );
        $key = eZDBQueryCache::key( $this->db, 'SELECT * FROM ezsection', array() );
        $this->assertSame( eZDBQueryCache::entriesDir() . '/' . substr( $key, 0, 2 ) . '/' . $key, $file->invoke( null, $key ) );
    }

    /** QC-30 */
    public function testRequestsSweepTheFilesWithoutTheCronjobAtMostOncePerInterval()
    {
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file', 'maxAge' => 300 ) );
        $this->select( 'SELECT * FROM ezsection' );
        $this->select( 'SELECT * FROM ezcontentobject' );
        $files = glob( eZDBQueryCache::entriesDir() . '/*/*' );
        $this->assertCount( 2, $files );
        touch( $files[0], time() - 400 );
        $out = eZDBQueryCache::sweepFiles();
        $this->assertSame( 1, $out['removed'], 'the expired file' );
        $this->assertSame( 1, $out['kept'] );
        $this->assertFalse( $out['truncated'] );
        $this->assertFileExists( eZDBQueryCache::entriesDir() . '/.swept' );
        touch( $files[1], time() - 400 );
        $this->assertNull( eZDBQueryCache::sweepFiles(), 'not again within the interval' );
        $this->assertFileExists( $files[1] );
        touch( eZDBQueryCache::entriesDir() . '/.swept', time() - 3600 );
        $this->assertSame( 1, eZDBQueryCache::sweepFiles()['removed'], 'due again after the interval' );

        // one sweeper at a time
        touch( eZDBQueryCache::entriesDir() . '/.swept', time() - 3600 );
        $lock = fopen( eZDBQueryCache::entriesDir() . '/.sweep.lock', 'c' );
        flock( $lock, LOCK_EX );
        $this->assertNull( eZDBQueryCache::sweepFiles(), 'another process sweeps' );
        flock( $lock, LOCK_UN );
        fclose( $lock );

        // a time budget stops it early
        $this->select( 'SELECT * FROM ezuser' );
        $this->assertTrue( eZDBQueryCache::cleanupFiles( null, -1.0 )['truncated'] );
    }

    /** QC-31 */
    public function testAutoUsesFilesInEveryWebProcessVelocityIncluded()
    {
        $apcu = function_exists( 'apcu_enabled' ) && apcu_enabled();
        try
        {
            $this->newRequest( array( 'mode' => 'shared', 'storage' => 'auto' ) );
            eZDBQueryCache::setWebProcess( true );
            $this->assertSame( $apcu ? 'apcu' : 'file', eZDBQueryCache::storage(), 'a web process (FPM, a Velocity worker with the cli SAPI)' );
            eZDBQueryCache::setWebProcess( false );
            $this->assertSame( $apcu ? 'apcu' : null, eZDBQueryCache::storage(), 'a script' );
            eZDBQueryCache::setWebProcess( null );
            $this->assertSame( PHP_SAPI !== 'cli' || class_exists( 'Q_WebServer', false ), eZDBQueryCache::webProcess() );
        }
        finally
        {
            eZDBQueryCache::setWebProcess( null );
        }
    }

    /** QC-32 */
    public function testApcuRoomFollowsTheReserve()
    {
        $this->newRequest( array( 'mode' => 'shared', 'apcuReserve' => 25 ) );
        eZDBQueryCache::setApcuFree( 10 );
        $this->assertFalse( eZDBQueryCache::apcuRoom(), '10 % free, 25 % reserve' );
        eZDBQueryCache::setApcuFree( 25 );
        $this->assertFalse( eZDBQueryCache::apcuRoom(), 'exactly the reserve is not more' );
        eZDBQueryCache::setApcuFree( 30 );
        $this->assertTrue( eZDBQueryCache::apcuRoom() );
        eZDBQueryCache::setApcuFree( 1 );
        $this->newRequest( array( 'mode' => 'shared', 'apcuReserve' => 0 ) );
        $this->assertTrue( eZDBQueryCache::apcuRoom(), 'ApcuReserve=0 never stops' );
        $this->newRequest( array( 'mode' => 'shared' ) );
        $this->assertFalse( eZDBQueryCache::apcuRoom(), 'without the setting: 25 %' );
    }

    /** QC-33 */
    public function testBelowTheReserveResultsStayInTheRequest()
    {
        $sql = 'SELECT * FROM ezsection WHERE id = 1';
        // the file storage has no reserve
        $this->newRequest( array( 'mode' => 'shared', 'storage' => 'file' ) );
        eZDBQueryCache::setApcuFree( 1 );
        $this->select( $sql );
        $this->assertCount( 1, glob( eZDBQueryCache::entriesDir() . '/*/*' ) );

        if ( !function_exists( 'apcu_enabled' ) || !apcu_enabled() || !class_exists( 'APCUIterator' ) )
            $this->markTestSkipped( 'the rest needs APCu (apc.enable_cli=1)' );
        apcu_delete( new APCUIterator( '/^ezqc:/' ) );
        try
        {
            $this->newRequest( array( 'mode' => 'shared', 'storage' => 'apcu', 'apcuReserve' => 25 ) );
            eZDBQueryCache::setApcuFree( 10 );
            $this->assertNull( $this->select( $sql ) );
            $this->assertSame( 1, eZDBQueryCache::$stats['reserve'] );
            $this->assertSame( 0, eZDBQueryCache::$stats['stores'] );
            $this->assertNotNull( $this->select( $sql ), 'the request still has it' );
            $this->assertSame( 0, iterator_count( new APCUIterator( '/^ezqc:/' ) ), 'nothing in APCu' );

            $this->newRequest( array( 'mode' => 'shared', 'storage' => 'apcu', 'apcuReserve' => 25 ) );
            $this->assertNull( $this->select( $sql ), 'the next request misses' );
            eZDBQueryCache::setApcuFree( 60 );
            $this->newRequest( array( 'mode' => 'shared', 'storage' => 'apcu', 'apcuReserve' => 25 ) );
            $this->select( $sql );
            $this->newRequest( array( 'mode' => 'shared', 'storage' => 'apcu', 'apcuReserve' => 25 ) );
            $this->assertNotNull( $this->select( $sql ), 'with room again: a hit from APCu' );
        }
        finally
        {
            apcu_delete( new APCUIterator( '/^ezqc:/' ) );
        }
    }

    /** QC-34 */
    public function testPurgeExpiredFreesTheMemoryOfExpiredEntries()
    {
        if ( !function_exists( 'apcu_enabled' ) || !apcu_enabled() || !class_exists( 'APCUIterator' ) )
            $this->markTestSkipped( 'needs APCu (apc.enable_cli=1)' );
        // the pool's purge lock (ezqcpurge) is matched too: no earlier test or run holds it
        apcu_delete( new APCUIterator( '/^ezqc/' ) );
        try
        {
            $free = function () { return apcu_sma_info( true )['avail_mem']; };
            $before = $free();
            for ( $i = 0; $i < 20; $i++ )
                apcu_store( 'ezqc:expired' . $i, str_repeat( 'x', 20000 ), 1 );
            apcu_store( 'ezqc:live', 'x', 300 );
            apcu_store( 'other:expired', 'x', 1 );
            sleep( 2 );
            $this->assertLessThan( $before - 300000, $free(), 'expired entries still take their memory' );

            $this->assertSame( 20, eZDBQueryCache::purgeExpired() );
            $this->assertGreaterThan( $before - 100000, $free(), 'their memory is free again' );
            $this->assertTrue( apcu_exists( 'ezqc:live' ), 'a live entry stays' );
            $this->assertNull( eZDBQueryCache::purgeExpired(), 'once a minute for the pool' );

            // below the reserve apcuRoom() starts it
            apcu_delete( 'ezqcpurge' );
            apcu_store( 'ezqc:expired', 'x', 1 );
            sleep( 2 );
            $this->newRequest( array( 'mode' => 'shared', 'apcuReserve' => 25 ) );
            eZDBQueryCache::setApcuFree( 10 );
            $this->assertFalse( eZDBQueryCache::apcuRoom() );
            $this->assertTrue( apcu_exists( 'ezqcpurge' ), 'the purge ran (at once on the command line)' );
            $this->assertFalse( apcu_exists( 'ezqc:expired' ) );
        }
        finally
        {
            apcu_delete( new APCUIterator( '/^ezqc/' ) );
            apcu_delete( 'other:expired' );
        }
    }
}
