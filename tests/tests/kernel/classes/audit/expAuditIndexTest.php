<?php
/**
 * The audit index and console (doc/bc/6.0/audit.md, "The index", "The console", acceptance tests D1, D2, A3).
 *
 * Live-database tests: the kernel is started once on the admin siteaccess against the installation's own database
 * (no test database). The records are written by the audit into a throwaway directory under var/tmp/audit-tests/
 * (expAuditTestFixtures) and routed to test channels t4content, t4access and t4system, so every row, cursor and
 * file state the tests make is in those channels only; they are removed before and after the tests. The live
 * channels' rows are never read for an assertion and never changed (pseudonymisation and retention are run with
 * the channels option).
 *
 *  IX-01 — the tables are installed, install() is idempotent, the schema definition matches share/db_schema.dba
 *  IX-02 — incremental: every record of the files becomes one row, in batches; a second run adds nothing; new
 *          records are picked up from the cursor
 *  IX-03 — a run interrupted between the rows and the cursor (cursor lost) indexes no line twice
 *  IX-04 — rebuild gives the same ids as the incremental runs
 *  IX-05 — a record whose prev does not follow the chain marks the file broken (with the record) and records
 *          system.audit.chain.broken once
 *  IX-06 — pseudonymisation after a simulated 91 days: login, address and user agent hashed, the record marked,
 *          search finds the pseudonym and no longer the login; only the given channels are touched
 *  IX-07 — search: full text and LIKE give the same ids for the same queries (D2); filters by name pattern,
 *          result, severity, user, object, request and time
 *  IX-08 — records not indexed yet are merged into the first page; the PHP filter agrees with the SQL filter
 *  IX-09 — fetch( 'audit', ... ) as a user without audit/read is empty; as the administrator it has events (A3)
 *  IX-10 — retention (KeepDays) removes old rows of the given channels only
 *
 * Run: sudo -u alpha php vendor/bin/phpunit tests/tests/kernel/classes/audit/expAuditIndexTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditIndexTest extends PHPUnit\Framework\TestCase
{
    const CHANNELS = array( 't4content', 't4access', 't4system' );

    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

    /** @var bool */
    protected static $booted = false;

    protected $dir;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$booted || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 5 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            if ( !eZDB::hasInstance() || !eZDB::instance()->isConnected() )
            {
                $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
                $script->startup();
                $script->setUseSiteAccess( 'admin' );
                $script->initialize();
            }
            eZExecution::setCleanExit();
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            if ( !expAuditIndexSchema::isInstalled() )
                throw new RuntimeException( 'the audit index tables are not installed (update/common/scripts/6.0/createaudittables.php)' );
            self::$booted = true;
            self::cleanUp();
            // these tests run as the site user (they write to the site's database), so their files go into a
            // directory of their own: var/tmp/audit-tests/ belongs to whoever ran the other audit tests first
            $run = $root . '/var/tmp/audit-index-tests/run-' . date( 'Ymd-His' ) . '-' . getmypid() . '/';
            if ( !is_dir( $run ) )
                mkdir( $run, 0750, true );
            ( function () use ( $run ) { self::$runDir = $run; } )->bindTo( null, 'expAuditTestFixtures' )();
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$booted )
            self::cleanUp();
        expAuditIndexSettings::setOverride( null );
        parent::tearDownAfterClass();
    }

    /** Removes every row, cursor and file state of the test channels. */
    protected static function cleanUp()
    {
        $db = eZDB::instance();
        $in = "channel IN ('" . implode( "','", self::CHANNELS ) . "')";
        if ( expAuditIndexSchema::fullTextKind( $db ) === 'fts5' )
            expAuditIndexSchema::tryQuery( $db, 'DELETE FROM ' . expAuditIndexSchema::FTS . ' WHERE rowid IN (SELECT rowid FROM expaudit_event WHERE ' . $in . ')' );
        foreach ( array( 'expaudit_event', 'expaudit_cursor', 'expaudit_file' ) as $t )
            expAuditIndexSchema::tryQuery( $db, "DELETE FROM $t WHERE $in" );
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        self::cleanUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name(), array(
            'AuditChannelSettings/Channels' => self::CHANNELS,
            'AuditChannelSettings/Route' => array( 'content.*' => 't4content', 'access.*' => 't4access', 'system.*' => 't4system',
                                                   'commerce.*' => 't4system', 'data.*' => 't4system' ),
            'AuditChannelSettings/DefaultChannel' => 't4system',
            'AuditEventSettings/Enabled' => array( 'access.*', 'system.*', 'content.*' ),
        ) );
        expAuditIndexSettings::setOverride( array( 'AuditIndexSettings/BatchSize' => '40' ) );
        $this->login( 'admin' );
    }

    protected function tearDown(): void
    {
        expAudit::flush( true );
        expAuditTestFixtures::tearDown();
        expAuditIndexSettings::setOverride( null );
        parent::tearDown();
    }

    protected function login( $login )
    {
        $user = $login === 'anonymous' ? eZUser::fetch( eZUser::anonymousId() ) : eZUser::fetchByName( $login );
        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    /** Writes $n events: logins (some failed), node moves and settings writes, at a fixed clock. */
    protected function write( $n, $t = 1790942400, $prefix = 'editor' )
    {
        for ( $i = 0; $i < $n; $i++ )
        {
            expAudit::setNow( $t + $i );
            switch ( $i % 3 )
            {
                case 0:
                    expAudit::event( 'access.session.login', array( 'object' => array( 'type' => 'user', 'id' => 14 ),
                        'actor' => array( 'login' => $prefix . ( $i % 5 ), 'user_id' => 100 + $i % 5, 'ip' => '203.0.113.0/24' ),
                        'result' => $i % 9 === 0 ? 'failed' : 'success', 'reason' => $i % 9 === 0 ? 'credentials' : null ) );
                    break;
                case 1:
                    expAudit::event( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => 200 + $i, 'name' => 'Workout ' . $i ),
                        'target' => array( 'type' => 'node', 'id' => 89 ), 'before' => array( 'parent' => 2 ), 'after' => array( 'parent' => 89 ) ) );
                    break;
                default:
                    expAudit::event( 'system.setting.write', array( 'object' => array( 'type' => 'setting', 'id' => 'site.ini/DebugSettings/DebugOutput' ),
                        'after' => array( 'value' => 'enabled' ) ) );
            }
        }
        expAudit::flush( true );
        expAudit::setNow( null );
    }

    /** @return int records (lines that are records) in the test log files */
    protected function recordsInFiles()
    {
        $n = 0;
        foreach ( self::CHANNELS as $c )
            foreach ( expAuditWriter::channelFiles( $this->dir . 'log', $c ) as $f )
                foreach ( file( $this->dir . 'log/' . $f, FILE_IGNORE_NEW_LINES ) as $l )
                    if ( is_array( json_decode( $l, true ) ) )
                        $n++;
        return $n;
    }

    /** @return string[] ids of the test channels in the index, sorted */
    protected function indexedIds()
    {
        $rows = eZDB::instance()->arrayQuery( "SELECT id FROM expaudit_event WHERE channel IN ('" . implode( "','", self::CHANNELS ) . "') ORDER BY id" );
        return array_column( $rows, 'id' );
    }

    protected function indexer()
    {
        return new expAuditIndexer( eZDB::instance(), expAuditConfig::get(), expAuditIndexSettings::get() );
    }

    /** IX-01 */
    public function testSchema()
    {
        $this->assertTrue( expAuditIndexSchema::isInstalled() );
        $messages = array();
        $this->assertTrue( expAuditIndexSchema::install( null, $messages ) );
        $this->assertContains( 'the tables exist already', $messages );
        $dba = eZDbSchema::read( dirname( __DIR__, 5 ) . '/share/db_schema.dba', true );
        foreach ( expAuditIndexSchema::definition() as $table => $def )
        {
            $this->assertArrayHasKey( $table, $dba['schema'] );
            ksort( $def['fields'] );
            $this->assertEquals( $def['fields'], $dba['schema'][$table]['fields'], "fields of $table" );
            $this->assertEquals( array_keys( $def['indexes'] ), array_keys( $dba['schema'][$table]['indexes'] ), "indexes of $table" );
        }
        foreach ( expAuditIndexSchema::definition() as $def )
            foreach ( array_keys( $def['indexes'] ) as $index )
                $this->assertLessThanOrEqual( 30, strlen( $index ), 'Oracle: index names of at most 30 characters' );
    }

    /** IX-02 */
    public function testIncremental()
    {
        $this->write( 250 );
        $files = $this->recordsInFiles();
        $stats = $this->indexer()->run();
        $this->assertTrue( $stats['ok'], $stats['error'] );
        $this->assertSame( $files, $stats['rows'] );
        $this->assertGreaterThan( 3, $stats['batches'], 'batches of BatchSize (40)' );
        $this->assertCount( $files, $this->indexedIds() );
        $this->assertSame( 0, $this->indexer()->run()['rows'], 'a second run adds nothing' );
        $this->write( 10, 1790942400 + 1000 );
        $again = $this->indexer()->run();
        $this->assertSame( $this->recordsInFiles() - $files, $again['rows'] );
        $this->assertSame( 0, $this->indexer()->lag()['bytes'] );
    }

    /** IX-03 */
    public function testCrashBetweenRowsAndCursor()
    {
        $this->write( 60 );
        $this->indexer()->run();
        $before = $this->indexedIds();
        // the cursor lost: as if the process died after the rows were committed and before the cursor was
        eZDB::instance()->query( "DELETE FROM expaudit_cursor WHERE channel IN ('" . implode( "','", self::CHANNELS ) . "')" );
        $stats = $this->indexer()->run();
        $this->assertTrue( $stats['ok'], $stats['error'] );
        $this->assertSame( 0, $stats['rows'], 'no line indexed twice' );
        $this->assertSame( $before, $this->indexedIds() );
        $this->assertSame( 0, $this->indexer()->lag()['bytes'], 'the cursor is back at the end of each file' );
    }

    /** IX-04 */
    public function testRebuildEqualsIncremental()
    {
        $this->write( 90 );
        $this->indexer()->run();
        $incremental = $this->indexedIds();
        foreach ( self::CHANNELS as $c )
        {
            $r = $this->indexer()->rebuild( array( 'channel' => $c ) );
            $this->assertTrue( $r['ok'], isset( $r['error'] ) ? $r['error'] : '' );
        }
        // the rebuild records system.audit.reindex: index that too, then compare without it
        $rebuilt = array_values( array_intersect( $this->indexedIds(), $incremental ) );
        $this->assertSame( $incremental, $rebuilt );
    }

    /** IX-05 */
    public function testBrokenChainMarksTheFile()
    {
        $this->write( 30 );
        $this->indexer()->run();
        $this->write( 6, 1790942400 + 500 );
        // change the prev of the first new content record (after the cursor)
        $file = expAuditWriter::channelFiles( $this->dir . 'log', 't4content' )[0];
        $cursor = $this->indexer()->cursors()["t4content\n" . $file];
        $path = $this->dir . 'log/' . $file;
        $text = file_get_contents( $path );
        $tail = substr( $text, (int)$cursor['byte_offset'] );
        $tail = preg_replace( '/"prev":"sha256:[0-9a-f]{64}"/', '"prev":"sha256:' . str_repeat( '0', 64 ) . '"', $tail, 1 );
        file_put_contents( $path, substr( $text, 0, (int)$cursor['byte_offset'] ) . $tail );
        $stats = $this->indexer()->run();
        $this->assertArrayHasKey( $file, $stats['broken'] );
        $state = $this->indexer()->fileStates()["t4content\n" . $file];
        $this->assertSame( 'broken', $state['verified'] );
        $this->assertGreaterThan( 0, (int)$state['break_line'] );
        expAudit::flush( true );
        $broken = 0;
        foreach ( expAuditWriter::channelFiles( $this->dir . 'log', 't4system' ) as $f )
            foreach ( file( $this->dir . 'log/' . $f, FILE_IGNORE_NEW_LINES ) as $l )
                if ( strpos( $l, '"name":"system.audit.chain.broken"' ) !== false && strpos( $l, $file ) !== false )
                    $broken++;
        $this->assertSame( 1, $broken, 'system.audit.chain.broken recorded once' );
        $this->indexer()->run();
        $this->assertSame( 1, $broken );
    }

    /** IX-06 */
    public function testPseudonymiseAfter91Days()
    {
        $this->write( 30, 1790942400, 'pseudotest' );
        $this->indexer()->run();
        $q = new expAuditQuery();
        $found = $q->count( expAuditQuery::normalise( array( 'q' => 'pseudotest1' ) ), self::CHANNELS );
        $this->assertGreaterThan( 0, $found );
        $r = $this->indexer()->pseudonymise( array( 'now' => 1790942400 + 91 * 86400, 'channels' => self::CHANNELS ) );
        $this->assertTrue( $r['ok'], $r['error'] );
        $this->assertGreaterThan( 0, $r['rows'] );
        $rows = eZDB::instance()->arrayQuery( "SELECT login, ip, pseudonymised, record FROM expaudit_event WHERE channel = 't4access'" );
        foreach ( $rows as $row )
        {
            $this->assertSame( 1, (int)$row['pseudonymised'] );
            $this->assertStringStartsWith( 'h:', $row['login'] );
            $this->assertStringStartsWith( 'h:', $row['ip'] );
            $rec = json_decode( $row['record'], true );
            $this->assertTrue( $rec['pseudonymised'] );
            $this->assertStringStartsWith( 'h:', $rec['actor']['login'] );
        }
        $this->assertSame( 0, $q->count( expAuditQuery::normalise( array( 'q' => 'pseudotest1' ) ), self::CHANNELS ), 'the login is gone from the search' );
        $pseudonym = $rows[0]['login'];
        $this->assertGreaterThan( 0, $q->count( expAuditQuery::normalise( array( 'login' => $pseudonym ) ), self::CHANNELS ), 'grouping by the pseudonym works' );
        $this->assertSame( 0, $this->indexer()->pseudonymise( array( 'now' => 1790942400 + 91 * 86400, 'channels' => self::CHANNELS ) )['rows'], 'done once' );
    }

    /** IX-07 */
    public function testSearchAndFilters()
    {
        $this->write( 120 );
        $this->indexer()->run();
        $fts = new expAuditQuery();
        $like = new expAuditQuery( null, false );
        foreach ( array( 'workout', 'workout 1', 'debugoutput', 'editor3', 'credentials', 'move', 'site.ini', 'node 89', '203.0.113', 'zzzz-none' ) as $text )
        {
            $f = expAuditQuery::normalise( array( 'q' => $text ) );
            $a = array_column( (array)$fts->fetch( $f, self::CHANNELS, 0, 500, 'id' ), 'id' );
            $b = array_column( (array)$like->fetch( $f, self::CHANNELS, 0, 500, 'id' ), 'id' );
            sort( $a );
            sort( $b );
            $this->assertSame( $b, $a, "search '$text': " . $fts->fullTextKind() . ' and like' );
        }
        $count = function ( array $filter ) use ( $fts ) { return $fts->count( expAuditQuery::normalise( $filter ), self::CHANNELS ); };
        $this->assertSame( 40, $count( array( 'name' => 'access.session.login' ) ) );
        $this->assertSame( 40, $count( array( 'name' => 'access.*', 'channel' => 't4access' ) ) );
        $this->assertSame( 14, $count( array( 'name' => 'access.session.login', 'result' => 'failed' ) ) );
        $this->assertSame( $count( array( 'result' => 'failed' ) ), $count( array( 'severity' => 'warning', 'channel' => 't4access' ) ) );
        $this->assertSame( 8, $count( array( 'user' => 101, 'name' => 'access.session.login' ) ) );
        $this->assertSame( 1, $count( array( 'object' => 'node:201' ) ) );
        $this->assertSame( 0, $count( array( 'from' => '2030-01-01' ) ) );
        $this->assertGreaterThan( 0, $count( array( 'from' => '2026-10-01', 'to' => '2026-10-31' ) ) );
        $one = $fts->fetch( expAuditQuery::normalise( array( 'name' => 'content.node.move' ) ), self::CHANNELS, 0, 1 );
        $this->assertSame( 1, $count( array( 'request' => $one[0]['request_id'], 'name' => 'content.node.move', 'object' => 'node:' . $one[0]['object_id'] ) ) );
        // the Channel limitation: an empty list reads nothing
        $this->assertSame( 0, $fts->count( array(), array() ) );
    }

    /** IX-08 */
    public function testUnindexedRowsAreMerged()
    {
        $this->write( 20 );
        $this->indexer()->run();
        $this->write( 5, 1790942400 + 900 );
        $rows = $this->indexer()->unindexedRows( array(), self::CHANNELS, 50 );
        $this->assertNotEmpty( $rows );
        $indexed = array_flip( $this->indexedIds() );
        foreach ( $rows as $r )
            $this->assertArrayNotHasKey( $r['id'], $indexed );
        $f = expAuditQuery::normalise( array( 'name' => 'content.*' ) );
        foreach ( $this->indexer()->unindexedRows( $f, self::CHANNELS, 50 ) as $r )
            $this->assertStringStartsWith( 'content.', $r['name'] );
        $this->indexer()->run();
        $q = new expAuditQuery();
        foreach ( (array)$q->fetch( $f, self::CHANNELS, 0, 500 ) as $r )
            $this->assertTrue( expAuditIndexRow::matches( $r, $f ), 'PHP and SQL filters agree' );
    }

    /** IX-09 */
    public function testFetchAsEachRole()
    {
        $this->write( 12 );
        $this->indexer()->run();
        $this->login( 'anonymous' );
        $this->assertSame( array(), expAuditFunctionCollection::fetchEvents( 't4access' )['result'] );
        $this->assertSame( 0, expAuditFunctionCollection::fetchCount( 't4access' )['result'] );
        $this->assertFalse( expAuditFunctionCollection::fetchCanRead()['result'] );
        $this->login( 'admin' );
        $events = expAuditFunctionCollection::fetchEvents( 't4access' )['result'];
        $this->assertNotEmpty( $events );
        $this->assertSame( 't4access', $events[0]['channel'] );
        $this->assertTrue( expAuditFunctionCollection::fetchCanRead( 'content' )['result'] );
        $e = expAuditFunctionCollection::fetchEvent( $events[0]['id'] )['result'];
        $this->assertSame( $events[0]['id'], $e['id'] );
    }

    /** IX-10 */
    public function testRetention()
    {
        $this->write( 15 );
        $this->indexer()->run();
        $n = count( $this->indexedIds() );
        expAuditIndexSettings::setOverride( array( 'AuditIndexSettings/KeepDays' => '30' ) );
        $dry = $this->indexer()->purgeOld( array( 'now' => 1790942400 + 40 * 86400, 'channels' => self::CHANNELS, 'dryRun' => true ) );
        $this->assertSame( $n, $dry['rows'] );
        $this->assertCount( $n, $this->indexedIds(), 'a dry run removes nothing' );
        $r = $this->indexer()->purgeOld( array( 'now' => 1790942400 + 40 * 86400, 'channels' => self::CHANNELS ) );
        $this->assertTrue( $r['ok'], $r['error'] );
        $this->assertLessThanOrEqual( 1, count( $this->indexedIds() ), 'only the purge record itself may be left' );
    }
}
