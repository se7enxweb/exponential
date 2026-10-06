<?php
/**
 * expAuditReader::linesBackwards() through a stream wrapper whose reads return less than asked for, as PHP's
 * userland wrappers do (8 KB at most; Velocity runs every file through one). No database.
 *
 *  RC-01 - Every line of a file larger than one chunk is returned, newest first, when each read returns 100 bytes
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expAuditReaderChunkTestShortReadWrapper
{
    public $context;
    private $handle;

    public function stream_open( $path, $mode, $options, &$opened )
    {
        $this->handle = fopen( substr( $path, strlen( 'shortread://' ) ), $mode );
        return (bool)$this->handle;
    }

    public function stream_read( $count )
    {
        return fread( $this->handle, min( 100, $count ) );
    }

    public function stream_seek( $offset, $whence = SEEK_SET )
    {
        return fseek( $this->handle, $offset, $whence ) === 0;
    }

    public function stream_tell()
    {
        return ftell( $this->handle );
    }

    public function stream_eof()
    {
        return feof( $this->handle );
    }

    public function stream_stat()
    {
        return fstat( $this->handle );
    }

    public function stream_close()
    {
        fclose( $this->handle );
    }
}

class expAuditReaderChunkTest extends PHPUnit\Framework\TestCase
{
    /** RC-01 */
    public function testShortReadsLoseNoLine()
    {
        $file = tempnam( dirname( __DIR__, 5 ) . '/var/tmp', 'auditreader' );
        $lines = array();
        for ( $i = 1; $i <= 3000; $i++ )
            $lines[] = '{"n":' . $i . ',"pad":"' . str_repeat( 'x', 40 ) . '"}';
        file_put_contents( $file, implode( "\n", $lines ) . "\n" );
        stream_wrapper_register( 'shortread', 'expAuditReaderChunkTestShortReadWrapper' );
        try
        {
            $reader = new expAuditReader( dirname( $file ) );
            $read = iterator_to_array( $reader->linesBackwards( 'shortread://' . $file ), false );
        }
        finally
        {
            stream_wrapper_unregister( 'shortread' );
            @unlink( $file );
        }
        $this->assertCount( 3000, $read );
        $this->assertSame( $lines[2999], $read[0] );
        $this->assertSame( $lines[0], $read[2999] );
        $this->assertSame( array_reverse( $lines ), $read );
    }
}
