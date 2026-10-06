<?php
/**
 * Tests of eZPackageDownload: the Range header (a-b, a-, -n, unsatisfiable, malformed, several), the headers of
 * a whole and a partial answer, and the copy in pieces (exact bytes, a part, a file bigger than one piece). The
 * file is made under var/tmp and removed in tearDown(). No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageDownloadTest extends PHPUnit\Framework\TestCase
{
    private $file;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->file = 'var/tmp/phpunit-packagedownload-' . getmypid() . '-' . mt_rand() . '.bin';
        $bytes = '';
        for ( $i = 0; $i < eZPackageDownload::CHUNK * 2 + 1000; $i++ )
            $bytes .= chr( $i % 251 );
        file_put_contents( $this->file, $bytes );
    }

    protected function tearDown(): void
    {
        if ( $this->file && is_file( $this->file ) )
            unlink( $this->file );
    }

    public function testRange()
    {
        $this->assertNull( eZPackageDownload::range( null, 100 ) );
        $this->assertNull( eZPackageDownload::range( '', 100 ) );
        $this->assertSame( array( 10, 99 ), eZPackageDownload::range( 'bytes=10-', 100 ) );
        $this->assertSame( array( 10, 19 ), eZPackageDownload::range( 'bytes=10-19', 100 ) );
        $this->assertSame( array( 10, 99 ), eZPackageDownload::range( 'bytes=10-500', 100 ) );
        $this->assertSame( array( 90, 99 ), eZPackageDownload::range( 'bytes=-10', 100 ) );
        $this->assertSame( array( 0, 99 ), eZPackageDownload::range( 'bytes=-500', 100 ) );
        $this->assertFalse( eZPackageDownload::range( 'bytes=100-', 100 ) );
        $this->assertFalse( eZPackageDownload::range( 'bytes=20-10', 100 ) );
        $this->assertFalse( eZPackageDownload::range( 'bytes=-0', 100 ) );
        // another unit, several ranges or nonsense: the whole file
        $this->assertNull( eZPackageDownload::range( 'items=1-2', 100 ) );
        $this->assertNull( eZPackageDownload::range( 'bytes=0-1,5-6', 100 ) );
        $this->assertNull( eZPackageDownload::range( 'bytes=-', 100 ) );
        $this->assertNull( eZPackageDownload::range( "bytes=1-2\r\nX: y", 100 ) );
    }

    public function testHeaders()
    {
        list( $status, $headers ) = eZPackageDownload::headers( 100, 'k1-1.0.ezpkg', 'application/octet-stream', null );
        $this->assertNull( $status );
        $this->assertSame( '100', $headers['Content-Length'] );
        $this->assertSame( 'attachment; filename="k1-1.0.ezpkg"', $headers['Content-Disposition'] );
        $this->assertSame( 'nosniff', $headers['X-Content-Type-Options'] );
        $this->assertArrayNotHasKey( 'Content-Range', $headers );

        list( $status, $headers ) = eZPackageDownload::headers( 100, 'x', 'image/png', array( 10, 19 ), true );
        $this->assertSame( '206 Partial Content', $status );
        $this->assertSame( 'bytes 10-19/100', $headers['Content-Range'] );
        $this->assertSame( '10', $headers['Content-Length'] );
        $this->assertStringStartsWith( 'inline;', $headers['Content-Disposition'] );

        list( $status, $headers ) = eZPackageDownload::headers( 100, 'x', 'a/b', false );
        $this->assertSame( '416 Range Not Satisfiable', $status );
        $this->assertSame( 'bytes */100', $headers['Content-Range'] );
    }

    public function testCopyInPieces()
    {
        $expected = file_get_contents( $this->file );
        $out = fopen( 'php://memory', 'w+b' );
        $this->assertSame( strlen( $expected ), eZPackageDownload::copy( $this->file, $out ) );
        rewind( $out );
        $this->assertSame( sha1( $expected ), sha1( stream_get_contents( $out ) ) );
        fclose( $out );

        $start = eZPackageDownload::CHUNK - 5;
        $end = eZPackageDownload::CHUNK * 2 + 10;
        $out = fopen( 'php://memory', 'w+b' );
        $this->assertSame( $end - $start + 1, eZPackageDownload::copy( $this->file, $out, $start, $end ) );
        rewind( $out );
        $this->assertSame( substr( $expected, $start, $end - $start + 1 ), stream_get_contents( $out ) );
        fclose( $out );

        $this->assertFalse( eZPackageDownload::copy( $this->file . '.missing', fopen( 'php://memory', 'wb' ) ) );
    }

    public function testSendRefusesAMissingFile()
    {
        $this->assertFalse( eZPackageDownload::send( $this->file . '.missing', 'x' ) );
    }
}
