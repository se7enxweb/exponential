<?php
/**
 * File containing the eZFileDownloadShortReadTest class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/**
 * A download must deliver every byte even when the stream returns less than fread() asked for.
 *
 * A userland stream wrapper hands back one buffer per call, and network and cluster mounts return
 * what has arrived. eZFile::downloadContent() counted the bytes it requested instead of the bytes
 * it got, so on such a stream it stopped after the first short read and the client received a
 * truncated file with status 200. The DFS/MySQLi cluster gateway sent a range with one fread() and
 * had the same result.
 */
class eZFileDownloadShortReadTest extends ezpTestCase
{
    private const MAX_READ = 1000;

    private string $content;

    public function setUp(): void
    {
        parent::setUp();
        if ( !in_array( eZFileDownloadShortReadStreamWrapper::SCHEME, stream_get_wrappers(), true ) )
        {
            stream_wrapper_register( eZFileDownloadShortReadStreamWrapper::SCHEME, 'eZFileDownloadShortReadStreamWrapper' );
        }
        // Larger than eZFile::READ_PACKET_SIZE, so the transfer needs several packets as well.
        $this->content = '';
        for ( $i = 0; strlen( $this->content ) < 50000; ++$i )
        {
            $this->content .= sprintf( "%06d:%s\n", $i, md5( (string)$i ) );
        }
        eZFileDownloadShortReadStreamWrapper::$files = array( 'dfs/file.bin' => $this->content );
        eZFileDownloadShortReadStreamWrapper::$maxRead = self::MAX_READ;
    }

    public function tearDown(): void
    {
        if ( in_array( eZFileDownloadShortReadStreamWrapper::SCHEME, stream_get_wrappers(), true ) )
        {
            stream_wrapper_unregister( eZFileDownloadShortReadStreamWrapper::SCHEME );
        }
        eZFileDownloadShortReadStreamWrapper::$files = array();
        parent::tearDown();
    }

    private function url(): string
    {
        return eZFileDownloadShortReadStreamWrapper::SCHEME . '://dfs/file.bin';
    }

    public function testWrapperReallyReturnsShortReads()
    {
        $fp = fopen( $this->url(), 'rb' );
        $this->assertSame( self::MAX_READ, strlen( fread( $fp, 16384 ) ) );
        fclose( $fp );
    }

    public function testDownloadWholeFile()
    {
        ob_start();
        $this->assertTrue( eZFile::downloadContent( $this->url() ) );
        $this->assertSame( $this->content, ob_get_clean() );
    }

    public function testDownloadRange()
    {
        ob_start();
        $this->assertTrue( eZFile::downloadContent( $this->url(), 1234, 30000 ) );
        $this->assertSame( substr( $this->content, 1234, 30000 ), ob_get_clean() );
    }

    public function testDownloadOffsetToEnd()
    {
        ob_start();
        $this->assertTrue( eZFile::downloadContent( $this->url(), 777 ) );
        $this->assertSame( substr( $this->content, 777 ), ob_get_clean() );
    }

    public function testDownloadRangePastEnd()
    {
        ob_start();
        $this->assertTrue( eZFile::downloadContent( $this->url(), 49000, 100000 ) );
        $this->assertSame( substr( $this->content, 49000 ), ob_get_clean() );
    }

    public function testDownloadRangeSmallerThanOneRead()
    {
        ob_start();
        $this->assertTrue( eZFile::downloadContent( $this->url(), 10, 50 ) );
        $this->assertSame( substr( $this->content, 10, 50 ), ob_get_clean() );
    }

    public function testDfsMySQLiGatewayPassthroughRange()
    {
        $gateway = $this->dfsGateway();
        ob_start();
        $gateway->passthrough( 'file.bin', strlen( $this->content ), 1234, 30000 );
        $this->assertSame( substr( $this->content, 1234, 30000 ), ob_get_clean() );
    }

    public function testDfsMySQLiGatewayPassthroughWholeFile()
    {
        $gateway = $this->dfsGateway();
        ob_start();
        $gateway->passthrough( 'file.bin', strlen( $this->content ), 0, strlen( $this->content ) );
        $this->assertSame( $this->content, ob_get_clean() );
    }

    private function dfsGateway()
    {
        if ( !defined( 'CLUSTER_MOUNT_POINT_PATH' ) )
        {
            define( 'CLUSTER_MOUNT_POINT_PATH', eZFileDownloadShortReadStreamWrapper::SCHEME . '://dfs' );
        }
        if ( CLUSTER_MOUNT_POINT_PATH !== eZFileDownloadShortReadStreamWrapper::SCHEME . '://dfs' )
        {
            $this->markTestSkipped( 'CLUSTER_MOUNT_POINT_PATH is already defined by another test' );
        }
        $root = dirname( __FILE__, 5 );
        require_once $root . '/kernel/clustering/gateway.php';
        require_once $root . '/kernel/clustering/dfsmysqli.php';
        $class = new ReflectionClass( 'ezpDfsMySQLiClusterGateway' );
        return $class->newInstanceWithoutConstructor();
    }
}

/**
 * Read-only in-memory stream that returns at most $maxRead bytes per read call.
 */
class eZFileDownloadShortReadStreamWrapper
{
    const SCHEME = 'ezpshortreadtest';

    /** @var array<string,string> */
    public static $files = array();

    public static $maxRead = 1000;

    /** @var resource|null */
    public $context;

    private string $data = '';

    private int $position = 0;

    private static function key( $url )
    {
        return ltrim( (string)substr( $url, strlen( self::SCHEME ) + 3 ), '/' );
    }

    public function stream_open( $path, $mode, $options, &$openedPath )
    {
        $key = self::key( $path );
        if ( !isset( self::$files[$key] ) )
        {
            return false;
        }
        $this->data = self::$files[$key];
        $this->position = 0;
        return true;
    }

    public function stream_read( $count )
    {
        $chunk = (string)substr( $this->data, $this->position, min( $count, self::$maxRead ) );
        $this->position += strlen( $chunk );
        return $chunk;
    }

    public function stream_eof()
    {
        return $this->position >= strlen( $this->data );
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function stream_seek( $offset, $whence )
    {
        $size = strlen( $this->data );
        switch ( $whence )
        {
            case SEEK_SET: $target = $offset; break;
            case SEEK_CUR: $target = $this->position + $offset; break;
            case SEEK_END: $target = $size + $offset; break;
            default: return false;
        }
        if ( $target < 0 )
        {
            return false;
        }
        $this->position = $target;
        return true;
    }

    public function stream_stat()
    {
        return array( 'size' => strlen( $this->data ), 'mode' => 0100644 );
    }

    public function url_stat( $path, $flags )
    {
        $key = self::key( $path );
        if ( !isset( self::$files[$key] ) )
        {
            return false;
        }
        return array( 'size' => strlen( self::$files[$key] ), 'mode' => 0100644 );
    }

    public function stream_close()
    {
    }
}
