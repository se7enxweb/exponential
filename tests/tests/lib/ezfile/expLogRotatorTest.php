<?php
/**
 * expLogRotator, the one log rotation of the kernel, in a private directory under var/tmp (no database, the
 * site's logs are never touched):
 *   - archives shift .1 -> .2 ..., the current file becomes .1 and a new empty file is created at once
 *   - the number kept is a limit: the oldest is removed, numbers left above it from a larger setting too
 *   - keep 0 is "switched off", a missing file, a directory and a symbolic link are refused
 *   - minSize: a file that is no longer too big under the lock is not rotated again (two processes that both saw
 *     it too big rotate it once)
 *   - a file held by another rotation is reported as locked; a file renamed under the lock is skipped
 *   - the new file keeps the old one's mode
 *   - compression (optional) packs .2 and later, never .1
 *   - prune: by number, age and bare .gz, and --dry-run removes nothing
 *   - eZLog::rotateLog() and eZDebug::rotateLog() go through it
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezfile
 */

class expLogRotatorTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = getcwd() . '/var/tmp/phpunit-explogrotator-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0777, true );
    }

    protected function tearDown(): void
    {
        foreach ( (array)glob( $this->dir . '/{,.}*', GLOB_BRACE ) as $file )
        {
            if ( is_file( $file ) || is_link( $file ) )
                unlink( $file );
        }
        if ( is_dir( $this->dir . '/sub' ) )
            rmdir( $this->dir . '/sub' );
        rmdir( $this->dir );
        foreach ( array( 'eZMaxLogrotateFiles', 'eZDebugMaxLogrotateFiles' ) as $key )
            unset( $GLOBALS[$key] );
    }

    private function put( $name, $content, $mtime = null )
    {
        file_put_contents( $this->dir . '/' . $name, $content );
        if ( $mtime !== null )
            touch( $this->dir . '/' . $name, $mtime );
        return $this->dir . '/' . $name;
    }

    private function content( $name )
    {
        return file_exists( $this->dir . '/' . $name ) ? file_get_contents( $this->dir . '/' . $name ) : null;
    }

    public function testRotationShiftsTheArchivesAndCreatesAnEmptyFile()
    {
        $log = $this->put( 'error.log', 'now' );
        $this->put( 'error.log.1', 'one' );
        $this->put( 'error.log.2', 'two' );

        $result = expLogRotator::rotate( $log, 3 );

        $this->assertTrue( $result['ok'] );
        $this->assertSame( '', $result['error'] );
        $this->assertSame( '', $this->content( 'error.log' ), 'the new file exists and is empty' );
        $this->assertSame( 'now', $this->content( 'error.log.1' ) );
        $this->assertSame( 'one', $this->content( 'error.log.2' ) );
        $this->assertSame( 'two', $this->content( 'error.log.3' ) );
        $this->assertContains( 'error.log -> error.log.1', $result['renamed'] );
    }

    public function testTheOldestArchiveGoesAtTheLimitAndNumbersAboveItToo()
    {
        $log = $this->put( 'error.log', 'now' );
        $this->put( 'error.log.1', 'one' );
        $this->put( 'error.log.2', 'two' );
        $this->put( 'error.log.5', 'left from a larger setting' );
        $this->put( 'error.log.7.gz', 'left too' );

        $result = expLogRotator::rotate( $log, 2 );

        $this->assertTrue( $result['ok'] );
        $this->assertSame( 'now', $this->content( 'error.log.1' ) );
        $this->assertSame( 'one', $this->content( 'error.log.2' ) );
        $this->assertNull( $this->content( 'error.log.3' ) );
        $this->assertNull( $this->content( 'error.log.5' ) );
        $this->assertNull( $this->content( 'error.log.7.gz' ) );
        $this->assertContains( 'error.log.2 -> -', $result['renamed'] );
    }

    public function testKeepZeroMissingFilesDirectoriesAndLinksAreRefused()
    {
        $log = $this->put( 'error.log', 'now' );
        $this->assertSame( 'disabled', expLogRotator::rotate( $log, 0 )['error'] );
        $this->assertSame( 'now', $this->content( 'error.log' ) );

        $this->assertSame( 'missing', expLogRotator::rotate( $this->dir . '/none.log', 3 )['error'] );
        $this->assertSame( 'missing', expLogRotator::rotate( "bad\0name", 3 )['error'] );

        mkdir( $this->dir . '/sub' );
        $this->assertSame( 'not_a_file', expLogRotator::rotate( $this->dir . '/sub', 3 )['error'] );

        symlink( $log, $this->dir . '/link.log' );
        $this->assertSame( 'link', expLogRotator::rotate( $this->dir . '/link.log', 3 )['error'] );
        $this->assertSame( 'now', $this->content( 'error.log' ) );
    }

    public function testMinSizeLeavesAFileThatIsNoLongerTooBig()
    {
        $log = $this->put( 'storage.log', 'small' );
        $result = expLogRotator::rotate( $log, 3, array( 'minSize' => 100 ) );
        $this->assertFalse( $result['ok'] );
        $this->assertSame( 'skipped', $result['error'] );
        $this->assertNull( $this->content( 'storage.log.1' ) );

        $this->put( 'storage.log', str_repeat( 'x', 101 ) );
        $this->assertTrue( expLogRotator::rotate( $log, 3, array( 'minSize' => 100 ) )['ok'] );
        $this->assertSame( 101, strlen( $this->content( 'storage.log.1' ) ) );
    }

    public function testAFileLockedByAnotherRotationIsNotRotated()
    {
        $log = $this->put( 'error.log', 'now' );
        $other = fopen( $log, 'r' );
        $this->assertTrue( flock( $other, LOCK_EX ) );
        // a lock of another open file description of the same file blocks this one, as another process's would
        $result = expLogRotator::rotate( $log, 3, array( 'wait' => 50000 ) );
        flock( $other, LOCK_UN );
        fclose( $other );

        $this->assertFalse( $result['ok'] );
        $this->assertSame( 'locked', $result['error'] );
        $this->assertSame( 'now', $this->content( 'error.log' ) );
        $this->assertTrue( expLogRotator::rotate( $log, 3 )['ok'] );
    }

    public function testTheNewFileKeepsTheModeOfTheOldOne()
    {
        $log = $this->put( 'warning.log', 'now' );
        chmod( $log, 0640 );
        $old = umask( 0077 );
        try
        {
            $this->assertTrue( expLogRotator::rotate( $log, 3 )['ok'] );
        }
        finally
        {
            umask( $old );
        }
        clearstatcache();
        $this->assertSame( 0640, fileperms( $log ) & 0777 );
        $this->assertSame( fileowner( $this->dir . '/warning.log.1' ), fileowner( $log ) );
    }

    public function testCompressionPacksTheArchivesFromTheSecondOn()
    {
        $log = $this->put( 'debug.log', 'now' );
        $this->put( 'debug.log.1', 'one' );
        $this->put( 'debug.log.2.gz', gzencode( 'two' ) );

        $result = expLogRotator::rotate( $log, 4, array( 'compress' => true ) );

        $this->assertTrue( $result['ok'] );
        $this->assertSame( 'now', $this->content( 'debug.log.1' ), '.1 is never compressed' );
        $this->assertNull( $this->content( 'debug.log.2' ) );
        $this->assertSame( 'one', gzdecode( $this->content( 'debug.log.2.gz' ) ) );
        $this->assertSame( 'two', gzdecode( $this->content( 'debug.log.3.gz' ) ) );
        $this->assertContains( 'debug.log.2 -> debug.log.2.gz', $result['renamed'] );
    }

    public function testArchivesListsTheRotationsByNumber()
    {
        $log = $this->dir . '/error.log';
        $this->put( 'error.log', 'now' );
        $this->put( 'error.log.2', '22' );
        $this->put( 'error.log.1', '1' );
        $this->put( 'error.log.3.gz', '333' );
        $this->put( 'error.log.gz', 'bare' );
        $this->put( 'error.logx', 'other' );
        $this->put( 'error.log.old', 'other' );

        $names = array_column( expLogRotator::archives( $log ), 'name' );
        $this->assertSame( array( 'error.log.1', 'error.log.2', 'error.log.3.gz', 'error.log.gz' ), $names );
        $first = expLogRotator::archives( $log )[0];
        $this->assertSame( 1, $first['rotation'] );
        $this->assertSame( 1, $first['size'] );
        $this->assertFalse( $first['gz'] );
    }

    public function testPruneRemovesByNumberAgeAndBareArchivesAndDryRunRemovesNothing()
    {
        $now = 2000000000;
        $log = $this->put( 'error.log', 'now', $now );
        $this->put( 'error.log.1', 'new', $now - 3600 );
        $this->put( 'error.log.2', 'old', $now - 10 * 86400 );
        $this->put( 'error.log.4', 'number', $now );
        $this->put( 'error.log.gz', 'bare', $now );

        $dry = expLogRotator::prune( $log, 3, 7 * 86400, true, $now );
        $this->assertSame( array( 'error.log.2' => 'age', 'error.log.4' => 'number', 'error.log.gz' => 'bare' ),
                           array_column( $dry, 'reason', 'name' ) );
        $this->assertSame( array( false, false, false ), array_column( $dry, 'removed' ) );
        $this->assertSame( 'old', $this->content( 'error.log.2' ) );

        $done = expLogRotator::prune( $log, 3, 7 * 86400, false, $now );
        $this->assertSame( array( true, true, true ), array_column( $done, 'removed' ) );
        $this->assertNull( $this->content( 'error.log.2' ) );
        $this->assertSame( 'new', $this->content( 'error.log.1' ) );
        $this->assertSame( 'now', $this->content( 'error.log' ), 'the current file is never pruned' );

        $this->assertSame( array(), expLogRotator::prune( $log, 3, 0, false, $now ), 'no age limit: .1 stays' );
    }

    public function testEZLogAndEZDebugRotateThroughIt()
    {
        eZLog::setLogrotateFiles( 2 );
        $log = $this->put( 'common.log', str_repeat( 'x', 50 ) );
        $this->assertTrue( eZLog::rotateLog( $log ) );
        $this->assertSame( '', $this->content( 'common.log' ) );
        $this->assertFalse( eZLog::rotateLog( $log, 10 ), 'not too big any more: left as it is' );
        $this->assertNull( $this->content( 'common.log.2' ) );

        eZDebug::setLogrotateFiles( 1 );
        $debugLog = $this->put( 'error.log', 'e' );
        $this->put( 'error.log.1', 'old' );
        $this->assertTrue( eZDebug::rotateLog( $debugLog ) );
        $this->assertSame( 'e', $this->content( 'error.log.1' ) );
        $this->assertNull( $this->content( 'error.log.2' ) );

        eZLog::setLogrotateFiles( 0 );
        $this->assertNull( eZLog::rotateLog( $log ), 'switched off' );
    }
}
