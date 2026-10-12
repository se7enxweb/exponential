<?php
/**
 * The streamed export of the audit console and its background job (doc/bc/6.0/audit.md, "Export", #393).
 *
 * Live-database tests like expAuditIndexTest: the kernel is started on the admin siteaccess against the installation's
 * own database; the records are written by the audit into a throwaway directory under var/tmp/ and routed to the
 * test channels t393content, t393access and t393system, whose rows are removed before and after the tests. The
 * content jobs go to a throwaway job directory. The live channels are never read for an assertion nor written.
 *
 *  EX-01 — the stream writes the same bytes as the export's loop before #393 (OFFSET pages of 500 through
 *          expAuditConsole::page()), for CSV, JSON lines and JSON, over several pages and cut by a limit
 *  EX-02 — run a batch at a time through state()/fromState() (via JSON, as a job's checkpoint is kept): the same bytes
 *  EX-03 — records of the live files not indexed yet are merged into the first page exactly as before
 *  EX-04 — orderedIds() after a row gives the order of OFFSET pages; byIds() keeps the order of the ids it is given
 *  EX-05 — the query cache is switched off while the stream reads, and its settings are back afterwards
 *  EX-06 — the background job 'auditexport' writes the same file in batches; a batch cut short by a crash before
 *          its checkpoint is written again, not doubled; finish() records the result and its sha256
 *  EX-07 — only the user who asked for a background export may download it
 *
 * Run: sudo -u alpha php vendor/bin/phpunit tests/tests/kernel/classes/audit/expAuditExportStreamTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditExportStreamTest extends PHPUnit\Framework\TestCase
{
    const CHANNELS = array( 't393content', 't393access', 't393system' );

    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

    /** @var bool */
    protected static $booted = false;

    /** @var string|null this run's directory */
    protected static $run = null;

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
            if ( !class_exists( 'expAuditExportStream' ) )
                throw new RuntimeException( 'expAuditExportStream is not in the autoload array (php bin/php/ezpgenerateautoloads.php -k)' );
            if ( !expAuditIndexSchema::isInstalled() )
                throw new RuntimeException( 'the audit index tables are not installed' );
            self::$booted = true;
            self::cleanUp();
            self::$run = $root . '/var/tmp/audit-export-tests/run-' . date( 'Ymd-His' ) . '-' . getmypid() . '/';
            if ( !is_dir( self::$run ) )
                mkdir( self::$run, 0750, true );
            $run = self::$run;
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
            'AuditChannelSettings/Route' => array( 'content.*' => 't393content', 'access.*' => 't393access', 'system.*' => 't393system',
                                                   'commerce.*' => 't393system', 'data.*' => 't393system' ),
            'AuditChannelSettings/DefaultChannel' => 't393system',
            'AuditEventSettings/Enabled' => array( 'access.*', 'system.*', 'content.*' ),
        ) );
        expAuditIndexSettings::setOverride( array( 'AuditIndexSettings/BatchSize' => '2000', 'AuditConsoleSettings/MaxBackgroundExportRecords' => '100000' ) );
        $this->login( 'admin' );
    }

    protected function tearDown(): void
    {
        expAudit::flush( true );
        expAuditTestFixtures::tearDown();
        expAuditIndexSettings::setOverride( null );
        expContentJobStore::setDirectory( null );
        expContentJob::$settings = null;
        parent::tearDown();
    }

    protected function login( $login )
    {
        $user = $login === 'anonymous' ? eZUser::fetch( eZUser::anonymousId() ) : eZUser::fetchByName( $login );
        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    /** Writes $n events, a third each of logins, node moves (some with the same millisecond) and settings writes. */
    protected function write( $n, $t = 1790942400, $prefix = 'editor' )
    {
        for ( $i = 0; $i < $n; $i++ )
        {
            // pairs share a second: the order inside it is the id's
            expAudit::setNow( $t + intdiv( $i, 2 ) );
            switch ( $i % 3 )
            {
                case 0:
                    expAudit::event( 'access.session.login', array( 'object' => array( 'type' => 'user', 'id' => 14 ),
                        'actor' => array( 'login' => $prefix . ( $i % 5 ), 'user_id' => 100 + $i % 5, 'ip' => '203.0.113.0/24' ),
                        'result' => $i % 9 === 0 ? 'failed' : 'success', 'reason' => $i % 9 === 0 ? 'credentials' : null ) );
                    break;
                case 1:
                    expAudit::event( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => 200 + $i, 'name' => '=Workout "' . $i . '", 2' ),
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

    protected function index()
    {
        $stats = ( new expAuditIndexer( eZDB::instance(), expAuditConfig::get(), expAuditIndexSettings::get() ) )->run();
        $this->assertTrue( $stats['ok'], isset( $stats['error'] ) ? $stats['error'] : '' );
    }

    /** The export's loop before #393, as it was in kernel/private/classes/views/audit/export.php */
    protected function oldExport( array $raw, $format, $limit )
    {
        $filters = expAuditQuery::normalise( $raw );
        $allowed = self::CHANNELS;
        $cols = expAuditExportStream::CSV_COLUMNS;
        $extra = $format === 'csv' && isset( $raw['channel'] ) ? expAuditSourceRegistry::exportColumns( $raw['channel'], $cols ) : array();
        $out = '';
        if ( $format === 'csv' )
            $out .= "\xEF\xBB\xBF" . expAuditExportStream::csvLine( array_merge( $cols, array_keys( $extra ) ) );
        elseif ( $format === 'json' )
            $out .= "[\n";
        $count = 0;
        for ( $offset = 0; $offset < $limit; $offset += 500 )
        {
            $page = expAuditConsole::page( $filters, $allowed, $offset, min( 500, $limit - $offset ) );
            if ( !$page['rows'] )
                break;
            foreach ( $page['rows'] as $row )
            {
                if ( $format === 'csv' )
                {
                    $v = expAuditConsole::view( $row );
                    $line = array();
                    foreach ( $cols as $c )
                        $line[] = isset( $v[$c] ) ? $v[$c] : '';
                    $out .= expAuditExportStream::csvLine( $line );
                }
                elseif ( $format === 'jsonl' )
                    $out .= rtrim( (string)$row['record'], "\n" ) . "\n";
                else
                    $out .= ( $count ? ",\n" : '' ) . rtrim( (string)$row['record'], "\n" );
                $count++;
            }
        }
        if ( $format === 'json' )
            $out .= "\n]\n";
        return $out;
    }

    protected function newExport( array $raw, $format, $limit, $batch = 0 )
    {
        $out = '';
        $emit = function ( $s ) use ( &$out ) { $out .= $s; };
        $stream = new expAuditExportStream( $raw, self::CHANNELS, $format, $limit );
        if ( !$batch )
        {
            $this->assertTrue( $stream->run( $emit ) );
            return $out;
        }
        $runs = 0;
        while ( !$stream->run( $emit, $batch ) )
        {
            $stream = expAuditExportStream::fromState( json_decode( json_encode( $stream->state() ), true ) );
            $this->assertLessThan( 100, ++$runs );
        }
        return $out;
    }

    /** EX-01 */
    public function testSameBytesAsTheOldLoop()
    {
        $this->write( 1300 );
        $this->index();
        $this->assertTrue( expAuditExportStream::seekable(), 'the index is read by ordered ids here' );
        foreach ( array( array(), array( 'channel' => 't393content' ), array( 'name' => 'access.*' ) ) as $raw )
        {
            $total = expAuditExportStream::total( $raw, self::CHANNELS );
            foreach ( array( 'csv', 'jsonl', 'json' ) as $format )
                foreach ( array( $total, 777, 500, 3 ) as $limit )
                {
                    $old = $this->oldExport( $raw, $format, min( $total, $limit ) );
                    $this->assertSame( $old, $this->newExport( $raw, $format, min( $total, $limit ) ),
                                       json_encode( $raw ) . " $format limit $limit" );
                }
        }
        // the 1300 events and the audit's own records about them (genesis, index runs)
        $this->assertGreaterThanOrEqual( 1300, substr_count( $this->newExport( array(), 'jsonl', 5000 ), "\n" ) );
        $empty = $this->newExport( array( 'login' => 'nobody-at-all' ), 'json', 0 );
        $this->assertSame( "[\n\n]\n", $empty );
    }

    /** EX-02 */
    public function testBatchesGiveTheSameBytes()
    {
        $this->write( 1200 );
        $this->index();
        $total = expAuditExportStream::total( array(), self::CHANNELS );
        foreach ( array( 'csv', 'json' ) as $format )
        {
            $whole = $this->newExport( array(), $format, $total );
            foreach ( array( 300, 500, 1000 ) as $batch )
                $this->assertSame( $whole, $this->newExport( array(), $format, $total, $batch ), "$format batch $batch" );
        }
    }

    /** EX-03 */
    public function testUnindexedRecordsOnTop()
    {
        $this->write( 900 );
        $this->index();
        $this->write( 7, 1790942400 + 5000 );
        $total = expAuditExportStream::total( array(), self::CHANNELS );
        $this->assertGreaterThan( 900, $total, 'the records not indexed yet are counted' );
        foreach ( array( 'csv', 'jsonl' ) as $format )
            $this->assertSame( $this->oldExport( array(), $format, $total ), $this->newExport( array(), $format, $total ), $format );
    }

    /** EX-04 */
    public function testOrderedIdsAndByIds()
    {
        $this->write( 120 );
        $this->index();
        $q = new expAuditQuery();
        $f = expAuditQuery::normalise( array() );
        $n = (int)$q->count( $f, self::CHANNELS );
        $this->assertLessThanOrEqual( 500, $n );
        $paged = array_column( $q->fetch( $f, self::CHANNELS, 0, $n ), 'id' );
        $first = $q->orderedIds( $f, self::CHANNELS, 0, 50 );
        $last = end( $first );
        $rest = $q->orderedIds( $f, self::CHANNELS, 0, 500, array( $last['time_ms'], $last['id'] ) );
        $this->assertSame( $paged, array_merge( array_column( $first, 'id' ), array_column( $rest, 'id' ) ) );
        $this->assertSame( array_slice( $paged, 30, 20 ), array_column( $q->orderedIds( $f, self::CHANNELS, 30, 20 ), 'id' ) );
        $ids = array( $paged[5], $paged[1], 'ZZZZZZZZZZZZZZZZZZZZZZZZZZ', $paged[3] );
        $this->assertSame( array( $paged[5], $paged[1], $paged[3] ), array_column( $q->byIds( $ids ), 'id' ), 'order kept, a missing id left out' );
        $this->assertSame( array(), $q->byIds( array() ) );
    }

    /** EX-05 */
    public function testQueryCacheIsOffWhileReading()
    {
        $this->write( 30 );
        $this->index();
        $before = eZDBQueryCache::settings();
        eZDBQueryCache::setSettings( array( 'mode' => 'request' ) + $before );
        try
        {
            $modes = array();
            $stream = new expAuditExportStream( array(), self::CHANNELS, 'jsonl', 30 );
            $stream->run( function ( $s ) use ( &$modes ) { $modes[] = eZDBQueryCache::settings()['mode']; } );
            $this->assertSame( array( 'off' ), array_values( array_unique( $modes ) ) );
            $this->assertSame( 'request', eZDBQueryCache::settings()['mode'], 'the settings are back' );
        }
        finally
        {
            eZDBQueryCache::setSettings( $before );
        }
    }

    /** EX-06 */
    public function testBackgroundJobWritesTheSameFile()
    {
        $this->write( 1600 );
        $this->index();
        expContentJobStore::setDirectory( $this->dir . 'jobs' );
        expContentJob::$settings = array();
        $this->assertTrue( expContentJobAuditExport::available() );
        $raw = array( 'name' => 'access.*' );
        $job = expContentJob::create( 'auditexport', array( 'filters' => $raw + array( 'offset' => '20', 'bogus' => 'x' ), 'format' => 'csv', 'mode' => 'job' ),
                                      eZUser::currentUser() );
        expAudit::flush( true );
        $this->assertSame( $raw, $job->params()['filters'], 'only filters are kept' );
        $this->assertNull( $job->params()['channels'], 'the administrator reads every channel' );
        $this->assertSame( 'csv', $job->params()['format'] );
        // the job would read every channel: for the comparison, the test channels only
        $total = expAuditExportStream::total( $raw, self::CHANNELS );
        $this->assertGreaterThan( 500, $total, 'more than one batch' );
        $expected = $this->newExport( $raw, 'csv', $total );
        $params = $job->params();
        $params['channels'] = self::CHANNELS;
        $params['total'] = $params['limit'] = $total;
        ( function () use ( $params ) { $this->data['params'] = $params; } )->call( $job );

        $type = new expContentJobAuditExport();
        $type->prepare( $job );
        // BatchSize 3: one page of 500 records per batch
        $file = expContentJobAuditExport::file( $job );
        $r = $type->runBatch( $job, 3 );
        $this->assertFalse( $r['finished'] );
        $this->assertSame( 500, $r['done_total'] );
        $this->assertFileExists( $file );
        $cut = filesize( $file );
        // a crash after the next batch wrote, before its checkpoint was saved: the file has more than the checkpoint
        file_put_contents( $file, "garbage that must go\r\n", FILE_APPEND );
        $this->assertGreaterThan( $cut, filesize( $file ) );
        $type->prepare( $job );
        $batches = 1;
        while ( !$r['finished'] )
        {
            $r = $type->runBatch( $job, 3 );
            $this->assertLessThan( 50, ++$batches );
        }
        $this->assertSame( $total, $r['done_total'] );
        $type->finish( $job );
        $this->assertSame( $expected, file_get_contents( $file ) );
        $result = $job->result();
        $this->assertSame( hash( 'sha256', $expected ), $result['sha256'] );
        $this->assertSame( $total, $result['count'] );
        $this->assertSame( strlen( $expected ), $result['bytes'] );
        $this->assertFalse( $result['cut'] );
        $this->assertStringEndsWith( '.csv', $result['name'] );
        // the file goes with the job
        expContentJobStore::remove( $job->id() );
        $this->assertFileDoesNotExist( $file );
    }

    /** EX-07 */
    public function testOnlyTheOwnerDownloads()
    {
        $this->write( 12 );
        $this->index();
        expContentJobStore::setDirectory( $this->dir . 'jobs' );
        expContentJob::$settings = array();
        $job = expContentJob::create( 'auditexport', array( 'filters' => array(), 'format' => 'jsonl', 'mode' => 'job' ), eZUser::currentUser() );
        $this->assertTrue( expContentJobAuditExport::mayDownload( $job, eZUser::currentUser() ) );
        $this->assertFalse( expContentJobAuditExport::mayDownload( $job, eZUser::fetch( eZUser::anonymousId() ) ) );
        $this->assertSame( array( $job->id() ), array_map( function ( $j ) { return $j->id(); }, expContentJobAuditExport::jobsOf( eZUser::currentUser() ) ) );
        $this->login( 'anonymous' );
        $this->expectException( expContentJobException::class );
        expContentJob::create( 'auditexport', array( 'filters' => array(), 'format' => 'csv' ), eZUser::currentUser() );
    }
}
