<?php
/**
 * // ###exp_feature_g44_ez2014.11### separate var_log and var_cache project folders
 *
 * File containing the eZLog class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
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
    const MAX_LOGFILE_SIZE = 204800; // 200*1024

    /*!
     \static
     \public
     Writes a message $message to a given file name $name and directory $dir for logging
    */
    static function write( $message, $logName = 'common.log', $dir = 'var/log' )
    {

        // ###exp_feature_g44_ez2014.11### separate var_log and var_cache project folders
        if ( defined( 'exp_feature_USE_EXTRA_FOLDER_VAR_LOG' ) && exp_feature_USE_EXTRA_FOLDER_VAR_LOG === true )
        {
            $dir = str_replace( 'var/', 'var_log/', $dir );
        }

        $fileName = $dir . '/' . $logName;
        $oldumask = @umask( 0 );

        $fileExisted = file_exists( $fileName );
        if ( $fileExisted and
             filesize( $fileName ) > eZLog::maxLogSize() )
        {
            if ( eZLog::rotateLog( $fileName ) )
                $fileExisted = false;
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
            $logMessage .= "[ {$GLOBALS['eZCurrentAccess']['name']} ]";
            if (php_sapi_name() === 'cli') {
                $pid = getmypid();
                $command = exec("ps -p $pid -o args=");
                $logMessage .= '[ ' . $command . ' ]';
            } else {
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $uri = $_SERVER['REQUEST_URI'];
                $url = "$scheme://$host$uri";
                $logMessage .= '[ ' . $url . ' ]';
            }
            $logMessage .= " $message\n";
            @fwrite( $logFile, $logMessage );
            @fclose( $logFile );
            if ( !$fileExisted )
            {
                $permissions = octdec( $ini->variable( 'FileSettings', 'LogFilePermissions' ) );
                @chmod( $fileName, $permissions );
            }
            @umask( $oldumask );
        }
        else
        {
            eZDebug::writeError( 'Couldn\'t create the log file "' . $fileName . '"', __METHOD__ );
        }
    }

    /*!
     \private
     Writes file name $name and storage directory $dir to storage log
    */
    static function writeStorageLog( $name, $dir = false )
    {
        $ini = eZINI::instance();
        $varDir = $ini->variable( 'FileSettings', 'VarDir' );

        // ###exp_feature_g44_ez2014.11### separate var_log and var_cache project folders
        if ( defined( 'exp_feature_USE_EXTRA_FOLDER_VAR_LOG' ) && exp_feature_USE_EXTRA_FOLDER_VAR_LOG === true )
        {
            $varDir = str_replace( 'var/', 'var_log/', $varDir );
        }

        $logDir = $ini->variable( 'FileSettings', 'LogDir' );
        $logName = 'storage.log';
        $fileName = $varDir . '/' . $logDir . '/' . $logName;
        $oldumask = @umask( 0 );

        clearstatcache( true, $fileName );
        $fileExisted = file_exists( $fileName );
        if ( $fileExisted and
             filesize( $fileName ) > eZLog::maxLogSize() )
        {
            if ( eZLog::rotateLog( $fileName ) )
                $fileExisted = false;
        }
        else if ( !$fileExisted and !file_exists( $varDir . '/' . $logDir ) )
        {
            eZDir::mkdir( $varDir . '/' . $logDir, false, true );
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
                @chmod( $fileName, $permissions );
            }
            @umask( $oldumask );
        }
        else
        {
            eZDebug::writeError( 'Couldn\'t create the log file "' . $fileName . '"', __METHOD__ );
        }
    }

    /*!
     \static
     \return the maximum size for a log file in bytes.
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
        return self::MAX_LOGFILE_SIZE;
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
     \return the maximum number of logrotate files to keep.
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
        return self::MAX_LOGROTATE_FILES;
    }

    /*!
     \static
     Rotates logfiles so the current logfile is backed up,
     old rotate logfiles are rotated once more and those that
     exceed maxLogrotateFiles() will be removed.
     Rotated files will get the extension .1, .2 etc.
    */
    static function rotateLog( $fileName )
    {
        $maxLogrotateFiles = eZLog::maxLogrotateFiles();
        if ( $maxLogrotateFiles == 0 )
        {
            return;
        }
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
                    eZFile::rename( $logRotateName, $newLogRotateName );
                }
            }
        }
        if ( file_exists( $fileName ) )
        {
            $newLogRotateName = $fileName . '.' . 1;
            eZFile::rename( $fileName, $newLogRotateName );
            return true;
        }
        return false;
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
