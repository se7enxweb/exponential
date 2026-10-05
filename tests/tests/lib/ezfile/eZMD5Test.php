<?php
/**
 * eZMD5::checkMD5Sums() (lib/ezfile), the check behind the upgrade check's "Check file consistency": a list of
 * "<md5>  <path>" lines against files on disk, with an optional sub directory prefix (extensions keep their own
 * list). Changed and missing files are reported, short and empty lines are ignored, an empty list reports nothing.
 * Also checks that share/filelist.md5 itself has the format the check reads.
 *
 * Plain PHP, no kernel bootstrap. Files go to a private directory under var/tmp that tearDown() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezfile
 */

class eZMD5Test extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        $root = dirname( __DIR__, 4 );
        require_once $root . '/lib/ezfile/classes/ezmd5.php';
        $this->dir = $root . '/var/tmp/phpunit-ezmd5-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->dir . 'sub', 0777, true );
        file_put_contents( $this->dir . 'sub/a.txt', 'alpha' );
        file_put_contents( $this->dir . 'sub/b.txt', 'beta' );
    }

    protected function tearDown(): void
    {
        foreach ( array( 'sub/a.txt', 'sub/b.txt', 'list.md5' ) as $file )
        {
            if ( file_exists( $this->dir . $file ) )
                unlink( $this->dir . $file );
        }
        rmdir( $this->dir . 'sub' );
        rmdir( $this->dir );
    }

    private function writeList( $lines )
    {
        file_put_contents( $this->dir . 'list.md5', implode( "\n", $lines ) . "\n" );
        return $this->dir . 'list.md5';
    }

    public function testUnchangedFilesReportNothing()
    {
        $list = $this->writeList( array( md5( 'alpha' ) . '  sub/a.txt', md5( 'beta' ) . '  sub/b.txt' ) );
        $this->assertSame( array(), eZMD5::checkMD5Sums( $list, $this->dir ) );
    }

    public function testChangedAndMissingFilesAreReported()
    {
        $list = $this->writeList( array(
            md5( 'alpha' ) . '  sub/a.txt',
            md5( 'old beta' ) . '  sub/b.txt',
            md5( 'gone' ) . '  sub/missing.txt',
        ) );
        $this->assertSame( array( $this->dir . 'sub/b.txt', $this->dir . 'sub/missing.txt' ), eZMD5::checkMD5Sums( $list, $this->dir ) );
    }

    public function testShortAndEmptyLinesAreIgnored()
    {
        $list = $this->writeList( array( '', 'version: 1.2.3', md5( 'alpha' ) . '  sub/a.txt', md5( 'x' ) ) );
        $this->assertSame( array(), eZMD5::checkMD5Sums( $list, $this->dir ) );
    }

    public function testEmptyListReportsNothing()
    {
        file_put_contents( $this->dir . 'list.md5', '' );
        $this->assertSame( array(), eZMD5::checkMD5Sums( $this->dir . 'list.md5', $this->dir ) );
    }

    public function testShippedListHasTheFormatTheCheckReads()
    {
        $file = dirname( __DIR__, 4 ) . '/' . eZMD5::CHECK_SUM_LIST_FILE;
        $this->assertFileExists( $file );
        $lines = file( $file, FILE_IGNORE_NEW_LINES );
        $this->assertNotEmpty( $lines );
        foreach ( array_slice( $lines, 0, 2000 ) as $n => $line )
            $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}  \S/', $line, 'line ' . ( $n + 1 ) );
    }
}
