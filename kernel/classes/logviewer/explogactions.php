<?php
/**
 * The actions of the log viewer besides reading (doc/features/6.0/log-viewer.md, "Actions"): download a range,
 * rotate now, empty a file, remove an archived rotation. Each is switched on in [ActionSettings]; the view checks the
 * policy (setup/logs for download, setup/managelogs for the others), the POST, the form token and the confirmation,
 * and records the audit. Nothing here locks a file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogActions
{
    /**
     * The byte range of a download, inside the file and at most MaxDownloadBytes long (the end of the file when
     * the range asked for is longer). Pure.
     *
     * @param int $size the file's size (unpacked size for .gz: unknown, pass PHP_INT_MAX)
     * @param mixed $from '' or a byte offset
     * @param mixed $to '' or a byte offset (exclusive)
     * @param int $max
     * @return array from, to
     */
    public static function range( $size, $from, $to, $max )
    {
        $size = max( 0, (int)$size );
        $to = ( $to === '' || $to === null || !is_numeric( $to ) ) ? $size : max( 0, min( $size, (int)$to ) );
        $from = ( $from === '' || $from === null || !is_numeric( $from ) ) ? max( 0, $to - $max ) : max( 0, min( $to, (int)$from ) );
        if ( $to - $from > $max )
            $from = $to - $max;
        return array( 'from' => $from, 'to' => $to );
    }

    /**
     * Sends a range of a file, masked line by line, compressed with gzip, as an attachment. Ends the request.
     *
     * @param array $file resolved
     * @param int $from
     * @param int $to
     * @param expLogRedactor $redactor
     * @return int the bytes of the file read
     */
    public static function download( array $file, $from, $to, expLogRedactor $redactor )
    {
        $name = preg_replace( '/[^A-Za-z0-9._\-]+/', '_', preg_replace( '/\.gz$/', '', $file['name'] ) ) . '.' . (int)$from . '-' . (int)$to . '.gz';
        if ( method_exists( 'eZExecution', 'discardOutputBuffers' ) )
            eZExecution::discardOutputBuffers();
        header( 'Content-Type: application/gzip' );
        header( 'Content-Disposition: attachment; filename="' . $name . '"' );
        header( 'Cache-Control: no-store' );
        header( 'X-Content-Type-Options: nosniff' );
        $out = fopen( 'php://output', 'wb' );
        $filter = stream_filter_append( $out, 'zlib.deflate', STREAM_FILTER_WRITE, array( 'window' => 31, 'level' => 6 ) );
        $read = 0;
        $gz = !empty( $file['gz'] );
        $handle = $gz ? @gzopen( $file['path'], 'rb' ) : @fopen( $file['path'], 'rb' );
        if ( $handle !== false )
        {
            $pos = 0;
            if ( $from > 0 )
            {
                if ( $gz )
                {
                    while ( $pos < $from && !gzeof( $handle ) )
                    {
                        $skip = gzread( $handle, (int)min( 1048576, $from - $pos ) );
                        if ( $skip === false || $skip === '' )
                            break;
                        $pos += strlen( $skip );
                    }
                }
                else if ( fseek( $handle, $from ) === 0 )
                    $pos = $from;
            }
            while ( $pos < $to )
            {
                $line = $gz ? gzgets( $handle, 1048576 ) : fgets( $handle, 1048576 );
                if ( $line === false )
                    break;
                if ( $pos + strlen( $line ) > $to )
                    $line = substr( $line, 0, $to - $pos );
                $pos += strlen( $line );
                $read += strlen( $line );
                $nl = substr( $line, -1 ) === "\n";
                fwrite( $out, $redactor->text( rtrim( $line, "\n" ) ) . ( $nl ? "\n" : '' ) );
            }
            $gz ? gzclose( $handle ) : fclose( $handle );
        }
        stream_filter_remove( $filter );
        fclose( $out );
        return $read;
    }

    /**
     * Rotates a current log file now, as eZLog does when a file is full: .N-1 becomes .N (the oldest beyond
     * site.ini [FileSettings] LogFileRotateCount, at least 1, is removed), the file becomes .1. The next line
     * written starts a new file.
     *
     * @param array $file resolved, not an archive
     * @param int|null $count null: eZLog::maxLogrotateFiles(), at least 1
     * @return array ok, error, renamed (list of "a -> b")
     */
    public static function rotate( array $file, $count = null )
    {
        if ( !empty( $file['archive'] ) )
            return array( 'ok' => false, 'error' => 'archive', 'renamed' => array() );
        if ( $count === null )
            $count = class_exists( 'eZLog' ) ? (int)eZLog::maxLogrotateFiles() : 3;
        $count = max( 1, min( 99, (int)$count ) );
        $path = $file['path'];
        $renamed = array();
        for ( $i = $count; $i > 0; --$i )
        {
            $name = $path . '.' . $i;
            if ( !file_exists( $name ) && !is_link( $name ) )
                continue;
            if ( is_link( $name ) )
                return array( 'ok' => false, 'error' => 'link', 'renamed' => $renamed );
            if ( $i === $count )
            {
                if ( !@unlink( $name ) )
                    return array( 'ok' => false, 'error' => 'unlink', 'renamed' => $renamed );
                $renamed[] = basename( $name ) . ' -> -';
            }
            else
            {
                if ( !@rename( $name, $path . '.' . ( $i + 1 ) ) )
                    return array( 'ok' => false, 'error' => 'rename', 'renamed' => $renamed );
                $renamed[] = basename( $name ) . ' -> ' . basename( $path ) . '.' . ( $i + 1 );
            }
        }
        if ( !@rename( $path, $path . '.1' ) )
            return array( 'ok' => false, 'error' => 'rename', 'renamed' => $renamed );
        $renamed[] = basename( $path ) . ' -> ' . basename( $path ) . '.1';
        return array( 'ok' => true, 'error' => '', 'renamed' => $renamed );
    }

    /**
     * Empties a current log file (its size becomes 0; writers that append go on at the start). No lock.
     *
     * @param array $file resolved, not an archive
     * @return array ok, error, bytes (the size it had)
     */
    public static function truncate( array $file )
    {
        if ( !empty( $file['archive'] ) )
            return array( 'ok' => false, 'error' => 'archive', 'bytes' => 0 );
        $handle = @fopen( $file['path'], 'r+' );
        if ( $handle === false )
            return array( 'ok' => false, 'error' => 'open', 'bytes' => 0 );
        $size = (int)$file['size'];
        $ok = ftruncate( $handle, 0 );
        fclose( $handle );
        clearstatcache( true, $file['path'] );
        return array( 'ok' => $ok, 'error' => $ok ? '' : 'truncate', 'bytes' => $size );
    }

    /**
     * Removes an archived rotation (.1 .. .N, .gz). A current file is never removed.
     *
     * @param array $file resolved, an archive
     * @return array ok, error, bytes
     */
    public static function deleteArchive( array $file )
    {
        if ( empty( $file['archive'] ) )
            return array( 'ok' => false, 'error' => 'not_archive', 'bytes' => 0 );
        if ( is_link( $file['path'] ) )
            return array( 'ok' => false, 'error' => 'link', 'bytes' => 0 );
        $ok = @unlink( $file['path'] );
        return array( 'ok' => $ok, 'error' => $ok ? '' : 'unlink', 'bytes' => (int)$file['size'] );
    }
}
