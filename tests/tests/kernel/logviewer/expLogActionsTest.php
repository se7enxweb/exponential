<?php
/**
 * The log viewer's actions besides reading, the session limit and the settings (doc/eng-us/features/6.0/log-viewer.md,
 * "Actions", "Cost", "Settings"), on a temporary root.
 *
 *  LV-50  range(): inside the file, at most the maximum, the end by default
 *  LV-51  rotate(): .1 -> .2 ..., the oldest beyond the count removed, the file becomes .1; archives refused
 *  LV-52  truncate(): size 0, archives refused; deleteArchive(): rotations only, never a current file or a link
 *  LV-53  download(): the range, masked line by line, compressed with gzip (run in its own PHP process, because it
 *         empties every output buffer as a download must)
 *  LV-54  the session limit: counting, Retry-After, a new window, kept between requests; the read audit interval
 *  LV-55  settings: bounds, switches, lists, the browser's values
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogActionsTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvact' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    protected function resolve( $id )
    {
        $l = new expLogFileLocator( expLogViewerTestFixtures::config( $this->root ), 'var/log' );
        return $l->resolve( $id );
    }

    /** LV-50 */
    public function testRange()
    {
        $this->assertSame( array( 'from' => 900, 'to' => 1000 ), expLogActions::range( 1000, '', '', 100 ) );
        $this->assertSame( array( 'from' => 10, 'to' => 20 ), expLogActions::range( 1000, 10, 20, 100 ) );
        $this->assertSame( array( 'from' => 900, 'to' => 1000 ), expLogActions::range( 1000, 0, '', 100 ) );
        $this->assertSame( array( 'from' => 0, 'to' => 1000 ), expLogActions::range( 1000, -5, 5000, 10000 ) );
        $this->assertSame( array( 'from' => 900, 'to' => 1000 ), expLogActions::range( 1000, 'abc', 'x', 100 ) );
        $this->assertSame( array( 'from' => 20, 'to' => 20 ), expLogActions::range( 1000, 50, 20, 100 ) );
        $this->assertSame( array( 'from' => 0, 'to' => 0 ), expLogActions::range( 0, '', '', 100 ) );
    }

    /** LV-51 */
    public function testRotate()
    {
        $log = $this->root . '/var/log/error.log';
        file_put_contents( $log, "current\n" );
        file_put_contents( $log . '.1', "one\n" );
        file_put_contents( $log . '.2', "two\n" );
        $r = expLogActions::rotate( $this->resolve( 'var/log/error.log' ), 2 );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( '', file_get_contents( $log ), 'the new log is created at once, empty, with the mode of the old one' );
        $this->assertSame( "current\n", file_get_contents( $log . '.1' ) );
        $this->assertSame( "one\n", file_get_contents( $log . '.2' ) );
        $this->assertFileDoesNotExist( $log . '.3' );
        $this->assertSame( array( 'error.log.2 -> -', 'error.log.1 -> error.log.2', 'error.log -> error.log.1' ), $r['renamed'] );

        $archive = $this->resolve( 'var/log/error.log.1' );
        $this->assertSame( 'archive', expLogActions::rotate( $archive, 2 )['error'] );
        $this->assertFileExists( $log . '.1' );
    }

    /** LV-52 */
    public function testTruncateAndDelete()
    {
        $log = $this->root . '/var/log/error.log';
        file_put_contents( $log, str_repeat( "line\n", 100 ) );
        file_put_contents( $log . '.1', "old\n" );
        $r = expLogActions::truncate( $this->resolve( 'var/log/error.log' ) );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( 500, $r['bytes'] );
        clearstatcache();
        $this->assertSame( 0, filesize( $log ) );
        $this->assertSame( 'archive', expLogActions::truncate( $this->resolve( 'var/log/error.log.1' ) )['error'] );
        $this->assertSame( 4, filesize( $log . '.1' ) );

        $this->assertSame( 'not_archive', expLogActions::deleteArchive( $this->resolve( 'var/log/error.log' ) )['error'] );
        $this->assertFileExists( $log );
        symlink( $log, $log . '.2' );
        $link = $this->resolve( 'var/log/error.log.2' );
        $this->assertNotNull( $link );
        $this->assertSame( 'link', expLogActions::deleteArchive( $link )['error'] );
        $this->assertTrue( is_link( $log . '.2' ) );
        $r = expLogActions::deleteArchive( $this->resolve( 'var/log/error.log.1' ) );
        $this->assertTrue( $r['ok'] );
        $this->assertFileDoesNotExist( $log . '.1' );
    }

    /** LV-53 */
    public function testDownload()
    {
        $log = $this->root . '/var/log/error.log';
        $content = "first line\nmail jane@example.com from 192.0.2.7\nthird line\n";
        file_put_contents( $log, $content );
        $from = strlen( "first line\n" );
        $to = strlen( $content ) - strlen( "third line\n" );
        $vendor = dirname( __DIR__, 4 ) . '/vendor/autoload.php';
        $script = $this->root . '/download.php';
        file_put_contents( $script, '<?php
require ' . var_export( $vendor, true ) . ';
$l = new expLogFileLocator( expLogViewerConfig::fromArray( array(), ' . var_export( $this->root, true ) . ' ), "var/log" );
expLogActions::download( $l->resolve( "var/log/error.log" ), ' . $from . ', ' . $to . ', new expLogRedactor() );
' );
        $out = shell_exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $script ) . ' 2>/dev/null' );
        $this->assertIsString( $out );
        $plain = gzdecode( $out );
        $this->assertSame( "mail j***@***.com from 192.0.x.x\n", $plain );
    }

    /** LV-54 */
    public function testRateLimit()
    {
        $dir = $this->root . '/rate';
        $l = new expLogRateLimit( $dir, 'session-a', 3, 60 );
        $this->assertSame( 0, $l->hit( 1000 ) );
        $this->assertSame( 0, $l->hit( 1001 ) );
        $this->assertSame( 0, $l->hit( 1002 ) );
        $this->assertSame( 58, $l->hit( 1002 ) );
        $again = new expLogRateLimit( $dir, 'session-a', 3, 60 );
        $this->assertSame( 30, $again->hit( 1030 ), 'kept between requests' );
        $this->assertSame( 0, $again->hit( 1060 ), 'a new window' );
        $other = new expLogRateLimit( $dir, 'session-b', 3, 60 );
        $this->assertSame( 0, $other->hit( 1002 ), 'each session has its own' );
        foreach ( scandir( $dir ) as $name )
            $this->assertStringNotContainsString( 'session', $name, 'the file is named by a hash, never the session id' );

        $this->assertTrue( $l->shouldRecordRead( 'var/log/error.log', 600, 2000 ) );
        $this->assertFalse( $l->shouldRecordRead( 'var/log/error.log', 600, 2100 ) );
        $this->assertTrue( $l->shouldRecordRead( 'var/log/warning.log', 600, 2100 ) );
        $this->assertTrue( ( new expLogRateLimit( $dir, 'session-a', 3, 60 ) )->shouldRecordRead( 'var/log/error.log', 600, 2601 ) );
    }

    /** LV-55 */
    public function testConfig()
    {
        $c = expLogViewerConfig::fromArray( array(
            'TailSettings' => array( 'InitialBytes' => 10, 'MaxBytesPerPoll' => 'abc', 'PollInterval' => 5000, 'MaxPollInterval' => 1000 ),
            'SearchSettings' => array( 'TimeLimit' => 999999 ),
            'ActionSettings' => array( 'Rotate' => 'enabled', 'Truncate' => 'true', 'DeleteArchives' => true, 'Download' => 'off' ),
            'LogViewerSettings' => array( 'ExcludePatterns' => array( '', ' a.log ', array( 'x' ) ), 'DefaultFile' => 'single' ),
        ), $this->root . '/' );
        $this->assertSame( $this->root, $c->root() );
        $this->assertSame( 1024, $c->int( 'TailSettings', 'InitialBytes' ) );
        $this->assertSame( 262144, $c->int( 'TailSettings', 'MaxBytesPerPoll' ) );
        $this->assertSame( 30000, $c->int( 'SearchSettings', 'TimeLimit' ) );
        $this->assertTrue( $c->enabled( 'ActionSettings', 'Rotate' ) );
        $this->assertTrue( $c->enabled( 'ActionSettings', 'Truncate' ) );
        $this->assertTrue( $c->enabled( 'ActionSettings', 'DeleteArchives' ) );
        $this->assertFalse( $c->enabled( 'ActionSettings', 'Download' ) );
        $this->assertSame( array( 'a.log' ), $c->lst( 'LogViewerSettings', 'ExcludePatterns' ) );
        $this->assertSame( array( 'single' ), $c->lst( 'LogViewerSettings', 'DefaultFile' ) );
        $this->assertSame( array(), $c->lst( 'LogViewerSettings', 'Nothing' ) );
        $this->assertNull( $c->get( 'LogViewerSettings', 'Nothing' ) );
        $this->assertSame( array( 'poll_interval' => 5000, 'max_poll_interval' => 5000, 'max_lines' => 5000 ), $c->clientSettings() );
        $d = expLogViewerConfig::fromArray( array(), $this->root );
        $this->assertFalse( $d->enabled( 'ActionSettings', 'Rotate' ), 'rotate is off by default' );
        $this->assertFalse( $d->enabled( 'ActionSettings', 'Truncate' ) );
        $this->assertFalse( $d->enabled( 'ActionSettings', 'DeleteArchives' ) );
        $this->assertTrue( $d->enabled( 'RedactionSettings', 'MaskEmails' ) );
        $this->assertTrue( $d->enabled( 'RedactionSettings', 'MaskIPs' ) );
    }
}
