<?php
/**
 * File containing the expPDFExportFile class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The file of a PDF export that is generated once: its name, where it is kept, what is known about it, sending it
 * and removing it.
 *
 * A stored export is written to <storage directory>/pdf/<file name> (var/<site>/storage/pdf/handbook.pdf), through
 * the cluster file handler, so it is also served from that path by the web server. The name comes from a form
 * field, so it is checked before it becomes part of a path: only letters, digits, ".", "_" and "-", no leading dot,
 * ending in ".pdf". Nothing here reads the database. Guide: doc/guides/pdf-exports.md
 */
class expPDFExportFile
{
    /** The directory below the storage directory */
    const DIRECTORY = 'pdf';

    /** The longest file name accepted, extension included */
    const MAX_LENGTH = 100;

    /**
     * The file name a form asked for, made into one that is safe as the last part of a path, or false when it
     * cannot be: empty, a path, a hidden file, a character outside letters, digits, ".", "_" and "-".
     * ".pdf" is added when the name has another or no extension; a name is never shortened.
     *
     * @param mixed $name
     * @return string|false
     */
    public static function normalizeName( $name )
    {
        if ( !is_string( $name ) )
            return false;
        $name = trim( $name );
        if ( $name === '' || strlen( $name ) > self::MAX_LENGTH )
            return false;
        if ( strtolower( substr( $name, -4 ) ) !== '.pdf' )
            $name .= '.pdf';
        return self::isSafeName( $name ) ? $name : false;
    }

    /**
     * Whether $name can be used as it is: what normalizeName() accepts, extension included.
     *
     * @param mixed $name
     * @return bool
     */
    public static function isSafeName( $name )
    {
        return is_string( $name )
            && strlen( $name ) <= self::MAX_LENGTH
            && preg_match( '/^[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$/i', $name ) === 1
            && strpos( $name, '..' ) === false;
    }

    /**
     * Why a name is refused, as a key the form turns into words: 'empty', 'too_long', 'path', 'characters', or
     * false when it is accepted.
     *
     * @param mixed $name
     * @return string|false
     */
    public static function nameProblem( $name )
    {
        if ( !is_string( $name ) || trim( $name ) === '' )
            return 'empty';
        if ( self::normalizeName( $name ) !== false )
            return false;
        $name = trim( $name );
        if ( strlen( $name ) > self::MAX_LENGTH )
            return 'too_long';
        if ( strpbrk( $name, "/\\" ) !== false || strpos( $name, '..' ) !== false || $name[0] === '.' )
            return 'path';
        return 'characters';
    }

    /**
     * The directory stored exports are written to.
     *
     * @param string|null $storageDirectory the storage directory, eZSys::storageDirectory() when null
     * @return string
     */
    public static function directory( $storageDirectory = null )
    {
        if ( $storageDirectory === null )
            $storageDirectory = eZSys::storageDirectory();
        return rtrim( (string)$storageDirectory, '/' ) . '/' . self::DIRECTORY;
    }

    /**
     * The path of the stored file named $name, or false when the name is not safe.
     *
     * @param string $name
     * @param string|null $storageDirectory
     * @return string|false
     */
    public static function path( $name, $storageDirectory = null )
    {
        return self::isSafeName( $name ) ? self::directory( $storageDirectory ) . '/' . $name : false;
    }

    /**
     * The name a download is offered under: the file name for a stored export; for one generated on the fly its
     * file name when it has a usable one, else one made of the title ("Product sheet" -> "Product-sheet.pdf").
     *
     * @param string $fileName
     * @param string $title
     * @return string
     */
    public static function downloadName( $fileName, $title = '' )
    {
        if ( self::isSafeName( $fileName ) && strtolower( $fileName ) !== 'file.pdf' )
            return $fileName;
        $base = preg_replace( '/[^A-Za-z0-9._-]+/', '-', (string)$title );
        $base = trim( preg_replace( '/-{2,}/', '-', $base ), '-._' );
        if ( $base === '' )
            return self::isSafeName( $fileName ) ? $fileName : 'export.pdf';
        return substr( $base, 0, self::MAX_LENGTH - 4 ) . '.pdf';
    }

    /**
     * What is known about the stored file: whether it is there, its size and when it was written.
     *
     * @param string $name
     * @param string|null $storageDirectory
     * @return array exists (bool), size (int), mtime (int), path (string, '' when the name is not safe), safe (bool)
     */
    public static function facts( $name, $storageDirectory = null )
    {
        $path = self::path( $name, $storageDirectory );
        $facts = array( 'exists' => false, 'size' => 0, 'mtime' => 0, 'path' => $path === false ? '' : $path,
                        'safe' => $path !== false );
        if ( $path === false )
            return $facts;
        $file = eZClusterFileHandler::instance( $path );
        if ( $file->exists() )
        {
            $facts['exists'] = true;
            $facts['size'] = (int)$file->size();
            $facts['mtime'] = (int)$file->mtime();
        }
        return $facts;
    }

    /**
     * Removes the stored file named $name, when there is one.
     *
     * @param string $name
     * @return bool whether a file was removed
     */
    public static function remove( $name )
    {
        $path = self::path( $name );
        if ( $path === false )
            return false;
        $file = eZClusterFileHandler::instance( $path );
        if ( !$file->exists() )
            return false;
        $file->delete();
        return true;
    }

    /**
     * The value of a Content-Disposition header that offers $name for download, with the plain and the RFC 5987
     * form of the name.
     *
     * @param string $name
     * @param bool $attachment false to show it in the browser
     * @return string
     */
    public static function dispositionHeader( $name, $attachment = true )
    {
        $plain = preg_replace( '/[^A-Za-z0-9._-]/', '_', (string)$name );
        return ( $attachment ? 'attachment' : 'inline' ) . '; filename="' . $plain . '"; filename*=UTF-8\'\'' . rawurlencode( (string)$name );
    }

    /**
     * Sends the file at $path as the whole answer: everything printed so far is dropped, the session is closed so
     * other pages of the same user do not wait, the file goes out in chunks (never read into a string). The caller
     * ends the request with eZExecution::cleanExit(), outside any catch block (under Velocity cleanExit() throws).
     *
     * @param string $path
     * @param string $downloadName
     * @return bool false when there is no such file; nothing has been sent then
     */
    public static function send( $path, $downloadName )
    {
        $file = eZClusterFileHandler::instance( $path );
        if ( !$file->exists() )
            return false;
        $size = (int)$file->size();
        eZSession::stop();
        eZExecution::discardOutputBuffers();
        header( 'Content-Type: application/pdf' );
        header( 'Content-Length: ' . $size );
        header( 'Content-Disposition: ' . self::dispositionHeader( $downloadName ) );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', (int)$file->mtime() ) . ' GMT' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'X-Content-Type-Options: nosniff' );
        $file->passthrough();
        return true;
    }
}

?>
