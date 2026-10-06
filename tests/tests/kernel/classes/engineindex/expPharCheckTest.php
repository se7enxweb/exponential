<?php
/**
 * Whether the engine archive still carries exactly the engine files on disk (expPhar::check()), decided from the
 * file index written beside it: every reason an archive is not current (none, no index, an older index format, an
 * archive replaced after its index, another PHP release, files changed, added or removed), and files touched but
 * unchanged, which are written back into the index. The archive and its index are made under var/tmp; the
 * installation's own engine archive is not read or touched.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once dirname( __DIR__ ) . '/radwizards/expRadWizardTestHelper.php';

class expPharCheckTest extends PHPUnit\Framework\TestCase
{
    private $scratch;
    private $archive;
    private static $files;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
        self::$files = expPhar::collect();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        $this->scratch = expRadWizardTestHelper::scratch( 'phar' );
        $this->archive = $this->scratch . '/engine.phar';
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::removeTree( $this->scratch );
    }

    /**
     * An archive and the index build() would have written for the files on disk now.
     */
    private function archiveWithIndex( $callback = null )
    {
        file_put_contents( $this->archive, 'not really a phar' );
        $root = expPhar::root();
        $files = array();
        foreach ( self::$files as $rel )
            $files[$rel] = array( filemtime( $root . '/' . $rel ), filesize( $root . '/' . $rel ), '' );
        $index = array( 'format' => expPhar::INDEX_FORMAT, 'php' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
                        'archive' => array( 'bytes' => filesize( $this->archive ), 'mtime' => filemtime( $this->archive ) ), 'files' => $files );
        if ( $callback )
            $index = $callback( $index );
        file_put_contents( expPhar::indexPath( $this->archive ), json_encode( $index ) );
        return $index;
    }

    public function testWhatGoesIntoTheArchive()
    {
        $this->assertSame( realpath( dirname( __DIR__, 5 ) ), expPhar::root() );
        $this->assertSame( expPhar::root() . '/dist/engine.phar', expPhar::enginePath() );
        $this->assertSame( '/x/engine.phar.index.json', expPhar::indexPath( '/x/engine.phar' ) );
        $this->assertContains( 'kernel/classes/expphar.php', self::$files );
        $this->assertContains( 'lib/ezutils/classes/ezini.php', self::$files );
        $this->assertContains( 'autoload/ezp_kernel.php', self::$files );
        $sorted = self::$files;
        sort( $sorted );
        $this->assertSame( $sorted, self::$files );
        foreach ( self::$files as $rel )
            $this->assertMatchesRegularExpression( '#^(kernel|lib|autoload)/#', $rel );
    }

    public function testNoArchiveOrNoIndex()
    {
        $check = expPhar::check( $this->archive );
        $this->assertFalse( $check['current'] );
        $this->assertSame( 'there is no archive yet', $check['reason'] );

        file_put_contents( $this->archive, 'x' );
        $this->assertStringContainsString( 'has no file index', expPhar::check( $this->archive )['reason'] );
        file_put_contents( expPhar::indexPath( $this->archive ), '{"format":1}' );
        $this->assertStringContainsString( 'has no file index', expPhar::check( $this->archive )['reason'] );
        file_put_contents( expPhar::indexPath( $this->archive ), 'not json' );
        $this->assertStringContainsString( 'has no file index', expPhar::check( $this->archive )['reason'] );
    }

    public function testAnIndexThatDoesNotFit()
    {
        $this->archiveWithIndex( function ( $index ) { $index['format'] = 0; return $index; } );
        $this->assertStringContainsString( 'older builder (index format 0, now ' . expPhar::INDEX_FORMAT . ')', expPhar::check( $this->archive )['reason'] );

        $this->archiveWithIndex( function ( $index ) { $index['archive']['bytes']++; return $index; } );
        $this->assertSame( 'the archive was replaced after its index was written', expPhar::check( $this->archive )['reason'] );

        $this->archiveWithIndex( function ( $index ) { $index['php'] = '5.6'; return $index; } );
        $this->assertStringContainsString( 'checked under PHP 5.6 and this is PHP ' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION, expPhar::check( $this->archive )['reason'] );
    }

    public function testCurrentWhenEveryFileIsAsPackaged()
    {
        $this->archiveWithIndex();
        $check = expPhar::check( $this->archive );
        $this->assertTrue( $check['current'], $check['reason'] );
        $this->assertSame( count( self::$files ), $check['files'] );
        $this->assertSame( 'every one of ' . count( self::$files ) . ' files is as packaged', $check['reason'] );
        $this->assertSame( 0, $check['touched'] );
    }

    public function testChangedAddedAndRemovedFiles()
    {
        $changed = 'kernel/classes/expphar.php';
        $added = 'lib/ezutils/classes/ezini.php';
        $this->archiveWithIndex( function ( $index ) use ( $changed, $added ) {
            $index['files'][$changed][1]++;
            unset( $index['files'][$added] );
            $index['files']['kernel/classes/k1e_gone.php'] = array( 1, 2, 'x' );
            return $index;
        } );
        $check = expPhar::check( $this->archive );
        $this->assertFalse( $check['current'] );
        $this->assertSame( array( $changed ), $check['changed'] );
        $this->assertSame( array( $added ), $check['added'] );
        $this->assertSame( array( 'kernel/classes/k1e_gone.php' ), $check['removed'] );
        $this->assertSame( "1 changed, 1 added, 1 removed ($changed, $added, kernel/classes/k1e_gone.php)", $check['reason'] );
    }

    public function testATouchedFileWithTheSameContentsIsCurrentAndRemembered()
    {
        $touched = 'kernel/classes/expphar.php';
        $path = expPhar::root() . '/' . $touched;
        $this->archiveWithIndex( function ( $index ) use ( $touched, $path ) {
            $index['files'][$touched] = array( filemtime( $path ) - 100, filesize( $path ), sha1_file( $path ) );
            return $index;
        } );
        $check = expPhar::check( $this->archive, false );
        $this->assertTrue( $check['current'] );
        $this->assertSame( 1, $check['touched'] );
        $this->assertStringContainsString( '(1 touched, same contents)', $check['reason'] );
        $index = json_decode( file_get_contents( expPhar::indexPath( $this->archive ) ), true );
        $this->assertSame( filemtime( $path ) - 100, $index['files'][$touched][0], 'not written back when asked not to' );

        expPhar::check( $this->archive );
        $index = json_decode( file_get_contents( expPhar::indexPath( $this->archive ) ), true );
        $this->assertSame( filemtime( $path ), $index['files'][$touched][0], 'written back' );
        $this->assertSame( array( 'engine.phar', 'engine.phar.index.json' ), array_values( array_diff( scandir( $this->scratch ), array( '.', '..' ) ) ), 'no temporary file left' );
        $this->assertSame( 0, expPhar::check( $this->archive )['touched'] );

        // same size, different contents: changed
        $this->archiveWithIndex( function ( $index ) use ( $touched, $path ) {
            $index['files'][$touched] = array( filemtime( $path ) - 100, filesize( $path ), sha1( 'other' ) );
            return $index;
        } );
        $this->assertSame( array( $touched ), expPhar::check( $this->archive )['changed'] );
    }
}
