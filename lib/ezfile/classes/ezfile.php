<?php
/**
 * File containing the eZFile class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
 \class eZFile ezfile.php
 \ingroup eZUtils
 \brief Tool class which has convencience functions for files and directories

*/
class eZFile
{
    /**
     * Number of bytes read per fread() operation.
     *
     * @see downloadContent()
     */
    const READ_PACKET_SIZE = 16384;

    /**
     * Flags for file manipulation
     *
     * @see rename()
     */
    const CLEAN_ON_FAILURE = 1,
          APPEND_DEBUG_ON_FAILURE = 2;

    /**
     * The upper limit for the mode of the files the installation creates: the constant EZP_FILE_MODE_MAX (config.php);
     * with only EZP_DIR_MODE_MAX set, that limit without the search bits (0750 gives 0640); null without either (no
     * limit, the modes asked for are used as before). See doc/bc/6.0/file-modes.md.
     *
     * @return int|null
     */
    static function fileModeLimit()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) )
        {
            return self::limitFromSettingFor( 'EZP_FILE_MODE_MAX', EZP_FILE_MODE_MAX, 0600 );
        }
        if ( defined( 'EZP_DIR_MODE_MAX' ) )
        {
            return self::limitFromSettingFor( 'EZP_DIR_MODE_MAX', EZP_DIR_MODE_MAX, 0700 ) & 0666;
        }
        return null;
    }

    /**
     * The mode $mode limited to EZP_FILE_MODE_MAX: never wider than the limit, a narrower mode stays as it is.
     *
     * @param int $mode
     * @return int
     */
    static function fileMode( $mode )
    {
        $limit = self::fileModeLimit();
        return $limit === null ? (int)$mode : ( (int)$mode & $limit );
    }

    /**
     * The mode $mode of a file that has to stay executable (a downloaded binary, a script), limited to
     * EZP_DIR_MODE_MAX: that limit keeps the execute bits where it allows them (0755 with 0750 gives 0750), where the
     * file limit would take them away.
     *
     * @param int $mode
     * @return int
     */
    static function executableMode( $mode )
    {
        return eZDir::dirMode( $mode );
    }

    /**
     * The umask to create files and directories with in place of umask( 0 ): 0 without limits (the mode asked for is
     * the mode the file gets, as before); with them, the bits neither limit allows, so a file fopen() makes (0666) or
     * a directory mkdir() makes stays inside the limits. A file mode limit narrower than the directory one without its
     * search bits (0600 with 0750) holds for the files that get their mode set (fileMode()), while files written
     * without a mode of their own get what the directory limit allows for files (0640).
     *
     * @param int $keep A umask whose bits stay set (the umask of the process, see applyCreationUmask())
     * @return int
     */
    static function creationUmask( $keep = 0 )
    {
        $fileLimit = self::fileModeLimit();
        $dirLimit = eZDir::dirModeLimit();
        if ( $fileLimit === null && $dirLimit === null )
        {
            return (int)$keep & 0777;
        }
        return ( (int)$keep | ~( $fileLimit | $dirLimit ) ) & 0777;
    }

    /**
     * Sets the umask of the process to creationUmask() when a limit is set, keeping what the server umask already
     * forbids, so files written without a mode of their own (fopen(), file_put_contents(), touch()) stay inside the
     * limits too. Called by autoload.php after config.php; without the limits the umask is left alone.
     *
     * @return void
     */
    static function applyCreationUmask()
    {
        if ( self::fileModeLimit() !== null )
        {
            umask( self::creationUmask( umask() ) );
        }
    }

    /**
     * A mode from a constant or setting: an integer (0640) or a string of octal digits ("0640", "640"). An integer
     * above 0777 written without the leading 0 (750) is read as its octal digits; a smaller one cannot be told from
     * an octal number (440 is 0670), so write the 0 or use a string. Anything else gives null.
     *
     * @param int|string $value
     * @return int|null
     */
    static function modeFromSetting( $value )
    {
        if ( is_int( $value ) )
        {
            if ( $value >= 0 && $value <= 0777 )
            {
                return $value;
            }
            return preg_match( '/^[0-7]{3}$/', (string)$value ) ? octdec( (string)$value ) : null;
        }
        $value = trim( (string)$value );
        return preg_match( '/^0*([0-7]{1,3})$/', $value, $match ) ? octdec( $match[1] ) : null;
    }

    /**
     * The limit the constant $name with $value sets; a value that is no mode gives $ownerOnly (rights for the owner
     * only, so the site keeps working and nothing is opened) and is reported once to the PHP error log.
     *
     * @param string $name
     * @param int|string $value
     * @param int $ownerOnly
     * @return int
     */
    static function limitFromSettingFor( $name, $value, $ownerOnly )
    {
        $mode = self::modeFromSetting( $value );
        if ( $mode !== null )
        {
            return $mode;
        }
        // error_log(), not eZDebug: eZDebug writes its log through these limits
        if ( !isset( $GLOBALS['eZFileModeLimitReported'][$name] ) )
        {
            $GLOBALS['eZFileModeLimitReported'][$name] = true;
            error_log( "Exponential: $name in config.php is no file mode (" . var_export( $value, true ) . '); ' .
                       sprintf( '%04o', $ownerOnly ) . ' is used. Write it as an octal number such as 0750.' );
        }
        return $ownerOnly;
    }

    /*!
     Creates a file called \a $filename.
     If \a $directory is specified the file is placed there, the directory will also be created if missing.
     if \a $data is specified the file will created with the content of this variable.

     \param $atomic If true the file contents will be written to a temporary file and renamed to the correct file.
    */
    static function create( $filename, $directory = false, $data = false, $atomic = false )
    {
        $filepath = $filename;
        if ( $directory )
        {
            if ( !file_exists( $directory ) )
            {
                eZDir::mkdir( $directory, false, true );
//                 eZDebugSetting::writeNotice( 'ezfile-create', "Created directory $directory", 'eZFile::create' );
            }
            $filepath = $directory . '/' . $filename;
        }
        // If atomic creation is needed we will use a temporary
        // file when writing the data, then rename it to the correct path.
        if ( $atomic )
        {
            $realpath = $filepath;
            $dirname  = dirname( $filepath );
            if ( strlen( $dirname ) != 0 )
                $dirname .= "/";
            $filepath = $dirname . "ezfile-tmp." . md5( $filepath . getmypid() . mt_rand() );
        }

        $file = fopen( $filepath, 'wb' );
        if ( $file )
        {
//             eZDebugSetting::writeNotice( 'ezfile-create', "Created file $filepath", 'eZFile::create' );
            if ( $data )
            {
                if ( is_resource( $data ) )
                {
                    // block-copy source $data to new $file in 1MB chunks
                    while ( !feof( $data ) )
                    {
                        fwrite( $file, fread( $data, 1048576 ) );
                    }
                    fclose( $data );
                }
                else
                    fwrite( $file, $data );
            }
            fclose( $file );

            if ( $atomic )
            {
                // If the renaming process fails, delete the temporary file
                eZFile::rename( $filepath, $realpath, false, eZFile::CLEAN_ON_FAILURE );
            }
            return true;
        }
//         eZDebugSetting::writeNotice( 'ezfile-create', "Failed creating file $filepath", 'eZFile::create' );
        return false;
    }

    /*!
     \static
     Get suffix from filename

     \param filename
     \return suffix, extends: file/to/readme.txt return txt
    */
    static function suffix( $filename )
    {
        $parts = explode( '.', $filename);
        return array_pop( $parts );
    }

    /*!
    \static
    Check if a given file is writeable

    \return TRUE/FALSE
    */
    static function isWriteable( $filename )
    {
        if ( eZSys::osType() != 'win32' )
            return is_writable( $filename );

        /* PHP function is_writable() doesn't work correctly on Windows NT descendants.
         * So we have to use the following hack on those OSes.
         */
        if ( !( $fd = @fopen( $filename, 'a' ) ) )
            return FALSE;

        fclose( $fd );

        return TRUE;
    }

    /**
     * Renames $srcFile to $destFile atomically on Unix, and provides a workaround for Windows.
     *
     * Usage example:
     * <code>
     * $srcFile = '/path/to/src/file';
     * $destFile = '/path/to/dest/file';
     * eZFile::rename( $srcFile, $destFile );
     *
     * // Using flags
     * // In following example, if rename operation fails, $srcFile will be deleted and a message will be appended in eZDebug
     * eZFile::rename( $srcFile, $destFile, false, eZFile::APPEND_DEBUG_ON_FAILURE | eZFile::CLEAN_ON_FAILURE );
     * </code>
     *
     * @param string $srcFile Source file path
     * @param string $destFile Destination file path
     * @param bool $mkdir Make directory for destination file if needed
     * @param int $flags Supported flags are :
     *                     - APPEND_DEBUG_ON_FAILURE (will append a message to the debug if operation fails
     *                     - CLEAN_ON_FAILURE (Will remove $srcFile if operation fails)
     * @return bool rename() status (true if successful, false if not)
     */
    static function rename( $srcFile, $destFile, $mkdir = false, $flags = 0 )
    {
        /* On windows we need to unlink the destination file first */
        if ( strtolower( substr( PHP_OS, 0, 3 ) ) == 'win' )
        {
            @unlink( $destFile );
        }
        if( $mkdir )
        {
            eZDir::mkdir( dirname( $destFile ), false, true );
        }

        $status = rename( $srcFile, $destFile );
        // Rename operation failed, check $flags to know what to do then
        if ( $status === false )
        {
            if ( $flags & self::APPEND_DEBUG_ON_FAILURE )
        	    eZDebug::writeWarning( "$srcFile could not be renamed to $destFile", __METHOD__ );

            if ( $flags & self::CLEAN_ON_FAILURE )
        	    unlink( $srcFile );
        }

        return $status;
    }

    /**
     * Prepares a file for Download and terminates the execution.
     * This method will:
     * - empty the output buffer
     * - stop buffering
     * - stop the active session (in order to allow concurrent browsing while downloading)
     *
     * @param string $file Path to the local file
     * @param bool $isAttachedDownload Determines weather to download the file as an attachment ( download popup box ) or not.
     * @param string $overrideFilename
     * @param int $startOffset Offset to start transfer from, in bytes
     * @param int $length Data size to transfer
     *
     * @return bool false if error
     */
    static function download( $file, $isAttachedDownload = true, $overrideFilename = false, $startOffset = 0, $length = false )
    {
        if ( !file_exists( $file ) )
        {
            return false;
        }

        ob_end_clean();
        eZSession::stop();
        self::downloadHeaders( $file, $isAttachedDownload, $overrideFilename, $startOffset, $length );
        self::downloadContent( $file, $startOffset, $length );

        eZExecution::cleanExit();
    }

    /**
     * Handles the header part of a file transfer to the client
     *
     * @see download()
     *
     * @param string $file Path to the local file
     * @param bool $isAttachedDownload Determines weather to download the file as an attachment ( download popup box ) or not.
     * @param string $overrideFilename Filename to send in headers instead of the actual file's name
     * @param int $startOffset Offset to start transfer from, in bytes
     * @param int $length Data size to transfer
     * @param string $fileSize The file's size. If not given, actual filesize will be queried. Required to work with clusterized files...
     */
    public static function downloadHeaders( $file, $isAttachedDownload = true, $overrideFilename = false, $startOffset = 0, $length = false, $fileSize = false )
    {
        if ( $fileSize === false )
        {
            if ( !file_exists( $file ) )
            {
                eZDebug::writeError( "\$fileSize not given, and file not found", __METHOD__ );
                return false;
            }

            $fileSize = filesize( $file );
        }

        header( 'X-Powered-By: Exponential' );
        $mimeinfo = eZMimeType::findByURL( $file );
        header( "Content-Type: {$mimeinfo['name']}" );

        // Fixes problems with IE when opening a file directly
        header( "Pragma: " );
        header( "Cache-Control: " );
        // Last-Modified header cannot be set, otherwise browser like FF will fail while resuming a paused download
        // because it compares the value of Last-Modified headers between requests.
        header( "Last-Modified: " );
        /* Set cache time out to 10 minutes, this should be good enough to work
           around an IE bug */
        header( "Expires: ". gmdate( 'D, d M Y H:i:s', time() + 600 ) . ' GMT' );
        header( self::contentDispositionHeader( $isAttachedDownload ? 'attachment' : 'inline', $overrideFilename ) );

        // partial download (HTTP 'Range' header)
        if ( $startOffset !== 0 )
        {
            $endOffset = ( $length !== false ) ? ( $length + $startOffset - 1 ) : $fileSize - 1;
            header( "Content-Length: " . ( $endOffset - $startOffset + 1 ) );
            header( "Content-Range: bytes {$startOffset}-{$endOffset}/{$fileSize}" );
            header( "HTTP/1.1 206 Partial Content" );
        }
        else
        {
            header( "Content-Length: $fileSize" );
        }
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Accept-Ranges: bytes' );
    }

    /**
     * Builds a Content-Disposition header line that is safe for any file name.
     *
     * The file name used to be appended unquoted, so a name containing a
     * semicolon, a quote or a space changed or broke the header parameters,
     * and a line break made PHP refuse the header altogether (the download
     * then went out without one). Now:
     * - control characters are removed from the name;
     * - filename= always carries a quoted ASCII fallback in which quotes,
     *   backslashes, and every byte outside printable ASCII become '_';
     * - when that fallback differs from the real name (non-ASCII, quotes...),
     *   filename*= (RFC 5987/6266) carries the exact UTF-8 name, which every
     *   current browser prefers over filename=.
     * A plain ASCII name produces just the quoted filename= parameter.
     *
     * @param string $type 'attachment' or 'inline' (anything else is sent as 'attachment')
     * @param string|false|null $fileName Name to offer, false/null/'' for none
     * @return string The complete header line, ready for header()
     */
    public static function contentDispositionHeader( $type, $fileName = false )
    {
        $type = ( $type === 'inline' ) ? 'inline' : 'attachment';
        $header = "Content-Disposition: $type";
        if ( $fileName === false || $fileName === null || !is_scalar( $fileName ) )
            return $header;

        $fileName = preg_replace( '/[\x00-\x1F\x7F]/', '', (string)$fileName );
        if ( $fileName === '' )
            return $header;

        $isUtf8 = preg_match( '//u', $fileName ) === 1;
        $fallback = preg_replace( $isUtf8 ? '/[^\x20-\x7E]|["\\\\]/u' : '/[^\x20-\x7E]|["\\\\]/', '_', $fileName );
        $header .= '; filename="' . $fallback . '"';
        if ( $fallback !== $fileName && $isUtf8 )
            $header .= "; filename*=UTF-8''" . rawurlencode( $fileName );
        return $header;
    }

    /**
     * Handles the data part of a file transfer to the client
     *
     * @see download()
     *
     * @param string $file Path to the local file
     * @param int $startOffset Offset to start transfer from, in bytes
     * @param int $length Data size to transfer
     */
    public static function downloadContent( $file, $startOffset = 0, $length = false )
    {
        if ( !file_exists( $file ) )
        {
            eZDebug::writeError( "'$file' does not exist", __METHOD__ );
            return false;
        }
        if ( ( $fp = fopen( $file, 'rb' ) ) === false )
        {
            eZDebug::writeError( "An error occured opening '$file' for reading", __METHOD__ );
            return false;
        }

        $fileSize = filesize( $file );

        // an offset has been given: move the pointer to that offset if it seems valid
        if ( $startOffset !== false && $startOffset <= $fileSize && fseek( $fp, $startOffset ) === -1 )
        {
            eZDebug::writeError( "Error while setting offset on '{$file}'", __METHOD__ );
            return false;
        }

        $transferred = $startOffset;
        $packetSize = self::READ_PACKET_SIZE;
        $endOffset = ( $length === false ) ? $fileSize - 1 : $length + $startOffset - 1;

        // Count the bytes fread() actually returned, not the bytes asked for: a stream may return
        // less than requested (a userland stream wrapper returns one 8 KB buffer per call, network
        // and cluster streams return what has arrived), and counting the request ended the
        // transfer early with a truncated body.
        while ( !feof( $fp ) && $transferred < $endOffset + 1 )
        {
            if ( $transferred + $packetSize > $endOffset + 1 )
            {
                $packetSize = $endOffset + 1 - $transferred;
            }
            $data = fread( $fp, $packetSize );
            if ( $data === false || $data === '' )
            {
                break;
            }
            echo $data;
            $transferred += strlen( $data );
        }
        fclose( $fp );

        return true;
    }
}

?>
