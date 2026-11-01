<?php
/**
 * File containing the eZLog class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit e39dde30ff), Copyright (C) Mugo Web, GPL v2
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*! \defgroup eZUtils Utility classes */

/*!
  \class eZLog ezlog.php
  \ingroup eZUtils
*/


class eZLog
{
    const MAX_LOGROTATE_FILES = 3;
    /// Without site.ini [FileSettings] LogFileMaxSize (and CUSTOM_LOG_MAX_FILE_SIZE): 2 MB, was 200 KB
    const MAX_LOGFILE_SIZE = 2097152; // 2*1024*1024

    /*!
     \static
     \public
     Writes a message $message to a given file name $name and directory $dir for logging
    */
    static function write( $message, $logName = 'common.log', $dir = 'var/log' )
    {
        // The default directory is the one eZDebug writes to: the log directory of the site when site.ini
        // [FileSettings] UseGlobalLogDir is disabled (multi-site hosting), var/log otherwise.
        if ( $dir === 'var/log' || $dir === 'var/log/' )
        {
            $dir = rtrim( eZDebug::instance()->logDirectory(), '/' );
        }
        $fileName = $dir . '/' . $logName;
        $oldumask = @umask( eZFile::creationUmask() );

        $fileExisted = file_exists( $fileName );
        if ( $fileExisted and
             filesize( $fileName ) > eZLog::maxLogSize() )
        {
            if ( eZLog::rotateLog( $fileName, eZLog::maxLogSize() ) )
                $fileExisted = file_exists( $fileName );
        }
        else if ( !$fileExisted and !file_exists( $dir ) )
        {
            eZDir::mkdir( $dir, false, true );
        }

        $logFile = @fopen( $fileName, "a" );
        if ( $logFile )
        {
            $ini = eZINI::instance();
            $time = date( "M d Y H:i:s", strtotime( "now" ) );

            $logMessage = "[ " . $time . " ]";
            $logMessage .= "[ " . self::siteAccessName() . " ]";
            $logMessage .= '[ ' . self::requestContext() . ' ]';
            // One entry, one line: a line break in the message (a user name, a URL) would forge further entries
            $logMessage .= ' ' . str_replace( array( "\r\n", "\r", "\n" ), ' ', (string)$message ) . "\n";
            @fwrite( $logFile, $logMessage );
            @fclose( $logFile );
            if ( !$fileExisted )
            {
                $permissions = octdec( $ini->variable( 'FileSettings', 'LogFilePermissions' ) );
                @chmod( $fileName, eZFile::fileMode( $permissions ) );
            }
            @umask( $oldumask );
        }
        else
        {
            eZDebug::writeError( 'Couldn\'t create the log file "' . $fileName . '"', __METHOD__ );
        }
    }

    /**
     * The siteaccess the request runs in, for a log line: the name the request was matched to
     * (site, admin, bold_ger ...), or '-' before one is chosen (early errors, command line scripts that
     * never load one). Reads only the global the siteaccess code sets, so it is safe on any error path.
     *
     * @return string
     */
    public static function siteAccessName()
    {
        $name = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
        $name = preg_replace( '/[\x00-\x1F\x7F]+/', '', $name );
        return $name !== '' ? $name : '-';
    }

    /**
     * What a log line says the message came from: the request's full address for a web request
     * (scheme, host with its port when the client used one, path and query), e.g.
     * https://www.example.com:8080/admin/package/compare/slash_quotes, or the command line of a
     * command line script.
     *
     * A web request is recognised by its REQUEST_URI, not by the PHP SAPI: Velocity's workers are
     * command line processes serving web requests, and a SAPI test logged every one of them as the
     * server's own command line. Nothing here reads the configuration or runs a program (the former
     * "ps -p" per line), so it is safe from any error path, and control characters are removed so a
     * crafted address cannot start a line of its own.
     *
     * @return string
     */
    public static function requestContext()
    {
        $clean = function ( $value ) { return preg_replace( '/[\x00-\x1F\x7F]+/', '', (string)$value ); };
        if ( !empty( $_SERVER['REQUEST_URI'] ) && ( PHP_SAPI !== 'cli' || !empty( $_SERVER['HTTP_HOST'] ) || !empty( $_SERVER['SERVER_NAME'] ) ) )
        {
            $https = ( !empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' )
                  || ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && strtolower( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) === 'https' );
            if ( !empty( $_SERVER['HTTP_HOST'] ) )
            {
                // As the client asked for it, so a non-default port (:8080) is part of it
                $host = $_SERVER['HTTP_HOST'];
            }
            else
            {
                $host = isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : 'localhost';
                $port = isset( $_SERVER['SERVER_PORT'] ) ? (int)$_SERVER['SERVER_PORT'] : 0;
                if ( $port && $port !== ( $https ? 443 : 80 ) )
                    $host .= ':' . $port;
            }
            return ( $https ? 'https' : 'http' ) . '://' . $clean( $host ) . $clean( $_SERVER['REQUEST_URI'] );
        }
        $argv = isset( $_SERVER['argv'] ) && is_array( $_SERVER['argv'] ) ? $_SERVER['argv'] : array();
        return $argv ? $clean( implode( ' ', $argv ) ) : $clean( PHP_SAPI );
    }

    /*!
     \private
     Writes file name $name and storage directory $dir to storage log
    */
    static function writeStorageLog( $name, $dir = false )
    {
        $ini = eZINI::instance();
        // The log directory of the site; site.ini [FileSettings] LogDir may be an absolute path
        $logDirectory = eZSys::logDirectory();
        $logName = 'storage.log';
        $fileName = $logDirectory . '/' . $logName;
        $oldumask = @umask( eZFile::creationUmask() );

        clearstatcache( true, $fileName );
        $fileExisted = file_exists( $fileName );
        if ( $fileExisted and
             filesize( $fileName ) > eZLog::maxLogSize() )
        {
            if ( eZLog::rotateLog( $fileName, eZLog::maxLogSize() ) )
                $fileExisted = file_exists( $fileName );
        }
        else if ( !$fileExisted and !file_exists( $logDirectory ) )
        {
            eZDir::mkdir( $logDirectory, false, true );
        }

        if ( $dir !== false )
        {
            $dir = preg_replace( "#/$#", "", $dir );
            $dir .= "/";
        }
        else
        {
            $dir = "";
        }

        $logFile = @fopen( $fileName, "a" );
        if ( $logFile )
        {
            $time = date( "M d Y H:i:s", strtotime( "now" ) );
            $logMessage = "[ " . $time . " ] [" . $dir . $name . "]\n";
            @fwrite( $logFile, $logMessage );
            @fclose( $logFile );
            if ( !$fileExisted )
            {
                $permissions = octdec( $ini->variable( 'FileSettings', 'LogFilePermissions' ) );
                @chmod( $fileName, eZFile::fileMode( $permissions ) );
            }
            @umask( $oldumask );
        }
        else
        {
            eZDebug::writeError( 'Couldn\'t create the log file "' . $fileName . '"', __METHOD__ );
        }
    }

    /**
     * A whole number from site.ini [FileSettings], for the size of the log files (LogFileMaxSize, bytes) and
     * how many rotated files are kept (LogFileRotateCount): used by maxLogSize() and maxLogrotateFiles() here
     * and in eZDebug, after setMaxLogSize()/setLogrotateFiles() and the constants in config.php, which win.
     *
     * Read on every call, not kept: a siteaccess with settings of its own, or a persistent worker serving the
     * next request, gets its own value. null while site.ini is not loaded yet (an early error), when the
     * setting is missing, empty, not a whole number or below $min.
     *
     * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit e39dde30ff), Copyright (C) Mugo Web, GPL v2.
     *
     * @param string $variable LogFileMaxSize or LogFileRotateCount
     * @param int $min the smallest value accepted
     * @return int|null
     */
    public static function fileSetting( $variable, $min = 0 )
    {
        if ( !class_exists( 'eZINI', false ) || !eZINI::isLoaded() )
            return null;
        try
        {
            $ini = eZINI::instance();
            if ( !$ini->hasVariable( 'FileSettings', $variable ) )
                return null;
            $value = trim( (string)$ini->variable( 'FileSettings', $variable ) );
        }
        catch ( Throwable $e )
        {
            return null;
        }
        if ( $value === '' || !ctype_digit( $value ) || (int)$value < $min )
            return null;
        return (int)$value;
    }

    /*!
     \static
     \return the maximum size for a log file in bytes: setMaxLogSize(), else CUSTOM_LOG_MAX_FILE_SIZE,
     else site.ini [FileSettings] LogFileMaxSize, else MAX_LOGFILE_SIZE.
    */
    static function maxLogSize()
    {
        $maxLogSize =& $GLOBALS['eZMaxLogSize'];
        if ( isset( $maxLogSize ) )
        {
            return $maxLogSize;
        }
        else if ( defined( 'CUSTOM_LOG_MAX_FILE_SIZE' ) )
        {
            self::setMaxLogSize( (int)CUSTOM_LOG_MAX_FILE_SIZE );
            return (int)CUSTOM_LOG_MAX_FILE_SIZE;
        }
        $fromINI = self::fileSetting( 'LogFileMaxSize', 1 );
        return $fromINI !== null ? $fromINI : self::MAX_LOGFILE_SIZE;
    }

    /*!
     \static
     Sets the maximum size for a log file to \a $size.
    */
    static function setMaxLogSize( $size )
    {
        $GLOBALS['eZMaxLogSize'] = $size;
    }

    /*!
     \static
     \return the maximum number of logrotate files to keep: setLogrotateFiles(), else CUSTOM_LOG_ROTATE_FILES,
     else site.ini [FileSettings] LogFileRotateCount, else MAX_LOGROTATE_FILES.
    */
    static function maxLogrotateFiles()
    {
        $maxLogrotateFiles =& $GLOBALS['eZMaxLogrotateFiles'];
        if ( isset( $maxLogrotateFiles ) )
        {
            return $maxLogrotateFiles;
        }
        else if ( defined( 'CUSTOM_LOG_ROTATE_FILES' ) )
        {
            self::setLogrotateFiles( (int)CUSTOM_LOG_ROTATE_FILES );
            return (int)CUSTOM_LOG_ROTATE_FILES;
        }
        $fromINI = self::fileSetting( 'LogFileRotateCount', 0 );
        return $fromINI !== null ? $fromINI : self::MAX_LOGROTATE_FILES;
    }

    /**
     * Rotates a log file: <file> becomes <file>.1, older archives move one up, and what is beyond
     * maxLogrotateFiles() is removed (expLogRotator: one rotation for the whole kernel, locked against a second
     * rotation of the same file, the new file created with the old one's owner and mode).
     *
     * @param string $fileName
     * @param int $minSize rotate only when the file is still larger than this under the lock (0: always), so two
     *                     processes that both found the file too big rotate it once, not twice
     * @return bool|null true when the file was rotated (or another process did meanwhile), false when not (also when no longer bigger than $minSize), null when
     *                   rotation is switched off (LogFileRotateCount=0)
     */
    static function rotateLog( $fileName, $minSize = 0 )
    {
        $maxLogrotateFiles = eZLog::maxLogrotateFiles();
        if ( $maxLogrotateFiles == 0 )
        {
            return null;
        }
        if ( class_exists( 'expLogRotator' ) )
        {
            $result = expLogRotator::rotate( $fileName, $maxLogrotateFiles,
                                            array( 'compress' => self::compressArchives(), 'minSize' => (int)$minSize ) );
            return $result['ok'] || $result['error'] === 'moved';
        }
        // A process that loaded its classes before expLogRotator existed (a long running worker)
        for ( $i = $maxLogrotateFiles; $i > 0; --$i )
        {
            $logRotateName = $fileName . '.' . $i;
            if ( file_exists( $logRotateName ) )
            {
                if ( $i == $maxLogrotateFiles )
                {
                    @unlink( $logRotateName );
                }
                else
                {
                    $newLogRotateName = $fileName . '.' . ($i + 1);
                    @rename( $logRotateName, $newLogRotateName );
                }
            }
        }
        if ( file_exists( $fileName ) )
        {
            return @rename( $fileName, $fileName . '.1' );
        }
        return false;
    }

    /**
     * Whether the archives of a log are compressed from .2 on (site.ini [FileSettings] LogFileCompress=enabled;
     * disabled by default, and while site.ini is not loaded).
     *
     * @return bool
     */
    public static function compressArchives()
    {
        if ( !class_exists( 'eZINI', false ) || !eZINI::isLoaded() )
            return false;
        try
        {
            $ini = eZINI::instance();
            return $ini->hasVariable( 'FileSettings', 'LogFileCompress' ) && $ini->variable( 'FileSettings', 'LogFileCompress' ) === 'enabled';
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * How old an archive may become before a prune removes it, in seconds (site.ini [FileSettings] LogFileMaxAge,
     * in days; 0 or missing: no age limit, only the number of archives counts).
     *
     * @return int
     */
    public static function maxArchiveAge()
    {
        $days = self::fileSetting( 'LogFileMaxAge', 0 );
        return $days !== null ? $days * 86400 : 0;
    }

    /*!
     \static
     Sets the maximum number of logrotate files to keep to \a $files.
    */
    static function setLogrotateFiles( $files )
    {
        $GLOBALS['eZMaxLogrotateFiles'] = $files;
    }

}

?>
