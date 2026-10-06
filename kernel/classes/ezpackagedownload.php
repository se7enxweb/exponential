<?php
/**
 * File containing the eZPackageDownload class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Sends a file of the package module (an exported archive, a file out of a package) to the browser in pieces.
 *
 * What it does on top of a plain readfile():
 * - every output buffer above the one it found at the start is closed first, so nothing of the page so far is
 *   sent with the file, and the file is written in 256 KiB pieces with a flush after each, never read whole into
 *   memory (under Exponential Velocity the engine still collects a persistent worker's response before it sends
 *   it; that buffer is the engine's own and stays);
 * - a "Range: bytes=a-b", "bytes=a-" or "bytes=-n" header is answered with 206 and that part, an unsatisfiable one
 *   with 416, anything else with the whole file;
 * - the file name is sent quoted, with its UTF-8 form, and never with a path or a quote in it.
 *
 * The caller ends the request with eZExecution::cleanExit() after send(), outside any try/catch: under Velocity
 * cleanExit() ends a request by throwing, and a catch would render the page on after the file.
 */
class eZPackageDownload
{
    const CHUNK = 262144;

    /**
     * The part of a file of $size bytes a Range header asks for.
     *
     * @param string|null $header the Range header, or null when there is none
     * @param int $size
     * @return array|null|false array( start, end ) inclusive; null for the whole file (no header, another unit,
     *                          several ranges or a malformed one: all answered with the whole file); false for an
     *                          unsatisfiable range (416)
     */
    static function range( $header, $size )
    {
        $size = (int)$size;
        if ( $header === null || $header === '' )
            return null;
        if ( !preg_match( '/^\s*bytes\s*=\s*(\d*)\s*-\s*(\d*)\s*$/i', (string)$header, $m ) )
            return null;
        if ( $m[1] === '' && $m[2] === '' )
            return null;
        if ( $size <= 0 )
            return false;
        if ( $m[1] === '' )
        {
            // the last n bytes
            $length = (int)$m[2];
            if ( $length <= 0 )
                return false;
            return array( max( 0, $size - $length ), $size - 1 );
        }
        $start = (int)$m[1];
        $end = $m[2] === '' ? $size - 1 : min( (int)$m[2], $size - 1 );
        if ( $start >= $size || $start > $end )
            return false;
        return array( $start, $end );
    }

    /**
     * The status line and headers send() writes, without writing them.
     *
     * @param int $size
     * @param string $fileName
     * @param string $mimeType
     * @param array|null|false $range from range()
     * @param bool $inline true for Content-Disposition inline (an image shown in the page)
     * @return array( string|null $status, array $headers name => value )
     */
    static function headers( $size, $fileName, $mimeType, $range, $inline = false )
    {
        $headers = array(
            'Content-Type' => $mimeType,
            'Content-Disposition' => eZPackageRequestGuard::contentDisposition( $fileName, 'package.ezpkg', $inline ? 'inline' : 'attachment' ),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'Accept-Ranges' => 'bytes',
        );
        $status = null;
        if ( $range === false )
        {
            $status = '416 Range Not Satisfiable';
            $headers['Content-Range'] = 'bytes */' . (int)$size;
            $headers['Content-Length'] = '0';
        }
        else if ( is_array( $range ) )
        {
            $status = '206 Partial Content';
            $headers['Content-Range'] = 'bytes ' . $range[0] . '-' . $range[1] . '/' . (int)$size;
            $headers['Content-Length'] = (string)( $range[1] - $range[0] + 1 );
        }
        else
        {
            $headers['Content-Length'] = (string)(int)$size;
        }
        return array( $status, $headers );
    }

    /**
     * Writes $path's bytes from $start to $end (inclusive) to $out in CHUNK pieces.
     *
     * @param string $path
     * @param resource $out a writable stream (php://output in send())
     * @param int $start
     * @param int|null $end null for the end of the file
     * @param bool $flush flush PHP's and the server's output after each piece
     * @return int|false the number of bytes written, false when the file cannot be read
     */
    static function copy( $path, $out, $start = 0, $end = null, $flush = false )
    {
        $in = @fopen( $path, 'rb' );
        if ( !$in )
            return false;
        if ( $start > 0 && fseek( $in, $start ) !== 0 )
        {
            fclose( $in );
            return false;
        }
        $left = $end === null ? PHP_INT_MAX : $end - $start + 1;
        $written = 0;
        while ( $left > 0 && !feof( $in ) )
        {
            $piece = fread( $in, (int)min( self::CHUNK, $left ) );
            if ( $piece === false || $piece === '' )
                break;
            fwrite( $out, $piece );
            $written += strlen( $piece );
            $left -= strlen( $piece );
            if ( $flush )
            {
                fflush( $out );
                flush();
            }
        }
        fclose( $in );
        return $written;
    }

    /**
     * Sends $path as the whole response (headers and body). The caller ends the request after it.
     *
     * @param string $path
     * @param string $fileName the name the browser saves it under
     * @param string $mimeType
     * @param bool $inline
     * @return bool false when the file is missing (nothing has been sent then)
     */
    static function send( $path, $fileName, $mimeType = 'application/octet-stream', $inline = false )
    {
        clearstatcache( true, $path );
        if ( !is_file( $path ) || !is_readable( $path ) )
            return false;
        $size = (int)filesize( $path );
        $range = self::range( isset( $_SERVER['HTTP_RANGE'] ) ? $_SERVER['HTTP_RANGE'] : null, $size );
        list( $status, $headers ) = self::headers( $size, $fileName, $mimeType, $range, $inline );

        // What the page wrote so far must not go out with the file
        while ( ob_get_level() > 0 && @ob_end_clean() );

        if ( $status !== null )
            header( 'HTTP/1.1 ' . $status );
        foreach ( $headers as $name => $value )
            header( $name . ': ' . $value );
        if ( $range === false )
            return true;

        $out = fopen( 'php://output', 'wb' );
        if ( is_array( $range ) )
            self::copy( $path, $out, $range[0], $range[1], true );
        else
            self::copy( $path, $out, 0, null, true );
        fclose( $out );
        return true;
    }
}

?>
