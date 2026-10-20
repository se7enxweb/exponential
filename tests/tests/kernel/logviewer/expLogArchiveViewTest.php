<?php
/**
 * The archive viewer of the log viewer (doc/eng-us/features/6.0/log-viewer.md, "Archives"): the rotations .N and .N.gz of
 * a log opened read only in the same viewer, at /setup/logs/view/<key>/(archive)/N. No database.
 *
 *  LV-110  (archive)/N resolves to rotation N: the plain .N before a .N.gz of the same number, a .N.gz alone, a bare
 *          .gz as -1; an archive that does not exist resolves to nothing
 *  LV-111  an archive is shown, never followed: tail() of a plain or compressed archive says live = false, and a
 *          compressed one gives nothing for a poll (only its end, once)
 *  LV-112  actionsFor(): an archive is downloaded or removed only; a current file is never removed; a log configured
 *          by key (the web server's) is downloaded only
 *  LV-113  an old link to an archive (?file=var/log/error.log.2.gz) maps to its permalink .../(archive)/2, and that
 *          permalink opens the same file again
 *  LV-114  a search with the rotations reads the archives too, compressed ones included
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogArchiveViewTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvarch' );
        $log = $this->root . '/var/log/';
        $t = strtotime( '2026-10-17 01:00:00' );
        file_put_contents( $log . 'error.log', expLogViewerTestFixtures::debugLines( 3, $t + 300 ) );
        file_put_contents( $log . 'error.log.1', str_replace( 'Error number', 'Plain one', expLogViewerTestFixtures::debugLines( 3, $t + 200 ) ) );
        file_put_contents( $log . 'error.log.2.gz', gzencode( str_replace( 'Error number', 'Packed two', expLogViewerTestFixtures::debugLines( 3, $t + 100 ) ) ) );
        file_put_contents( $log . 'error.log.3', str_replace( 'Error number', 'Plain three', expLogViewerTestFixtures::debugLines( 2, $t + 50 ) ) );
        file_put_contents( $log . 'error.log.3.gz', gzencode( str_replace( 'Error number', 'Packed three', expLogViewerTestFixtures::debugLines( 2, $t + 50 ) ) ) );
        file_put_contents( $log . 'debug.log.gz', gzencode( expLogViewerTestFixtures::debugLines( 1, $t ) ) );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    protected function locator()
    {
        return new expLogFileLocator( expLogViewerTestFixtures::config( $this->root ), 'var/log' );
    }

    /** LV-110 */
    public function testArchiveResolves()
    {
        $loc = $this->locator();
        $one = $loc->resolveKey( 'error', 1 );
        $this->assertSame( 'error.log.1', $one['name'] );
        $this->assertTrue( $one['archive'] );
        $this->assertFalse( $one['gz'] );
        $two = $loc->resolveKey( 'error', 2 );
        $this->assertSame( 'error.log.2.gz', $two['name'] );
        $this->assertTrue( $two['gz'] );
        $this->assertSame( 'error.log.3', $loc->resolveKey( 'error', 3 )['name'], 'the plain one before the compressed one' );
        $this->assertSame( 'debug.log.gz', $loc->resolveKey( 'debug', -1 )['name'] );
        $this->assertNull( $loc->resolveKey( 'error', 9 ) );
        $this->assertFalse( $loc->resolveKey( 'error', 0 )['archive'] );
    }

    /** LV-111 */
    public function testArchivesAreNotFollowed()
    {
        $loc = $this->locator();
        $viewer = new expLogViewer( expLogViewerTestFixtures::config( $this->root ), $loc, false, 'salt' );
        $plain = $viewer->tail( 'var/log/error.log.1', -1 );
        $this->assertFalse( $plain['live'] );
        $this->assertCount( 3, $plain['entries'] );
        $packed = $viewer->tail( 'var/log/error.log.2.gz', -1 );
        $this->assertFalse( $packed['live'] );
        $this->assertCount( 3, $packed['entries'] );
        $this->assertStringContainsString( 'Packed two', json_encode( $packed['entries'] ) );
        $poll = $viewer->tail( 'var/log/error.log.2.gz', $packed['next'], $packed['ino'] );
        $this->assertSame( array(), $poll['entries'], 'a compressed archive is never polled' );
        $this->assertFalse( $poll['live'] );
        $this->assertTrue( $viewer->tail( 'var/log/error.log', -1 )['live'], 'the current file is followed' );
    }

    /** LV-112 */
    public function testActionsOfAnArchive()
    {
        $all = array( 'download' => true, 'rotate' => true, 'truncate' => true, 'delete' => true );
        $loc = $this->locator();
        $this->assertSame( array( 'download' => true, 'rotate' => false, 'truncate' => false, 'delete' => true ),
                           expLogViewer::actionsFor( $loc->resolveKey( 'error', 2 ), $all ) );
        $this->assertSame( array( 'download' => true, 'rotate' => true, 'truncate' => true, 'delete' => false ),
                           expLogViewer::actionsFor( $loc->resolveKey( 'error', 0 ), $all ) );
        $this->assertSame( array( 'download' => true, 'rotate' => false, 'truncate' => false, 'delete' => false ),
                           expLogViewer::actionsFor( array( 'archive' => false, 'configured' => true ), $all ) );
        $this->assertSame( array( 'download' => true, 'rotate' => false, 'truncate' => false, 'delete' => false ),
                           expLogViewer::actionsFor( array( 'archive' => true, 'configured' => true ), $all ) );
        $none = array( 'download' => false, 'rotate' => false, 'truncate' => false, 'delete' => false );
        $this->assertSame( $none, expLogViewer::actionsFor( $loc->resolveKey( 'error', 2 ), $none ), 'switched off stays off' );
    }

    /** LV-113 */
    public function testOldLinkToAnArchive()
    {
        $loc = $this->locator();
        $file = $loc->resolve( 'var/log/error.log.2.gz' );
        $state = expLogPermalink::state( array( 'q' => 'Packed' ), false );
        $state['rotation'] = $file['rotation'];
        $url = expLogPermalink::build( $file['key'], $state );
        $this->assertSame( 'setup/logs/view/error/(archive)/2/(search)/Packed', $url );
        // the permalink, as the view reads it, opens the same file
        $parsed = expLogPermalink::state( array( 'archive' => '2', 'rotation' => false, 'search' => 'Packed' ) );
        $this->assertSame( 'var/log/error.log.2.gz', $loc->resolveKey( 'error', $parsed['rotation'] )['id'] );
        $this->assertSame( 'Packed', $parsed['q'] );
    }

    /** LV-114 */
    public function testSearchReadsArchives()
    {
        $loc = $this->locator();
        $viewer = new expLogViewer( expLogViewerTestFixtures::config( $this->root ), $loc, false, 'salt' );
        $files = $viewer->searchFiles( 'var/log/error.log', 'file', true );
        $names = array_map( function ( $f ) { return $f['name']; }, $files );
        $this->assertContains( 'error.log.2.gz', $names );
        $this->assertContains( 'error.log.3.gz', $names );
        $found = $viewer->search( $files, expLogSearch::criteria( array( 'q' => 'Packed' ) ) );
        $this->assertCount( 5, $found['entries'] );
        $this->assertSame( '', $found['error'] );
    }
}
