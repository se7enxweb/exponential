<?php
/**
 * File containing the eZExpiryHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/**
 * Keeps track of expiry keys and their timestamps
 * @class eZExpiryHandler ezexpiryhandler.php
 */
class eZExpiryHandler
{
    public function __construct()
    {
        $this->Timestamps = array();
        $this->IsModified = false;

        $this->CacheFile = eZClusterFileHandler::instance( self::filePath() );
        $this->restore();
    }

    /**
     * The expiry.php of the current site: in its cache directory, or in site.ini [FileSettings] ExpiryDir when that
     * is set, inside VarDir or as an absolute path.
     *
     * The timestamps in it decide whether the image aliases and caches kept elsewhere are still valid. A cache
     * directory on a file system that is emptied at a restart (memory) lost them with it; ExpiryDir keeps them
     * where the storage is.
     *
     * @return string
     */
    static function filePath()
    {
        $ini = eZINI::instance();
        $expiryDir = $ini->hasVariable( 'FileSettings', 'ExpiryDir' ) ? trim( (string)$ini->variable( 'FileSettings', 'ExpiryDir' ) ) : '';
        if ( $expiryDir === '' )
        {
            return eZSys::cacheDirectory() . '/' . 'expiry.php';
        }
        if ( $expiryDir[0] == '/' )
        {
            return eZDir::path( array( $expiryDir, 'expiry.php' ) );
        }
        return eZDir::path( array( eZSys::varDirectory(), $expiryDir, 'expiry.php' ) );
    }

    /**
     * Load the expiry timestamps from cache
     *
     * @return void
     */
    function restore()
    {
        $Timestamps = $this->CacheFile->processFile( array( $this, 'fetchData' ) );
        if ( $Timestamps === false )
        {
            $errMsg = 'Fatal error - could not restore expiry.php file.';
            eZDebug::writeError( $errMsg, __METHOD__ );
            trigger_error( $errMsg, E_USER_ERROR );
        }

        $this->Timestamps = $Timestamps;
        $this->IsModified = false;
    }

    /**
     * Includes the expiry file and extracts the $Timestamps variable from it.
     * @param string $path
     */
    static function fetchData( $path )
    {
        include( $path );
        return $Timestamps;
    }

    /**
     * Stores the current timestamps values to cache
     */
    function store()
    {
        if ( !$this->IsModified )
        {
            return;
        }

        // EZP-23908: Restore timestamps before saving, to reduce chance of race condition issues
        $modifiedTimestamps = $this->Timestamps;
        $this->restore();

        // Apply timestamps that have been added or modified in this process
        foreach ( $modifiedTimestamps as $name => $value )
        {
            if ( $value > self::getTimestamp( $name, 0 ) )
            {
                $this->setTimestamp( $name, $value );
            }
        }

        if ( $this->IsModified )
        {
            $this->CacheFile->storeContents( "<?php\n\$Timestamps = " . var_export( $this->Timestamps, true ) . ";\n?>", 'expirycache', false, true );
            $this->IsModified = false;
        }
    }

    /**
     * Sets the expiry timestamp for a key
     *
     * @param string $name Expiry key
     * @param int    $value Expiry timestamp value
     */
    function setTimestamp( $name, $value )
    {
        $this->Timestamps[$name] = $value;
        $this->IsModified = true;
    }

    /**
     * Checks if an expiry timestamp exist
     *
     * @param string $name Expiry key name
     *
     * @return bool true if the timestamp exists, false otherwise
     */
    function hasTimestamp( $name )
    {
        return isset( $this->Timestamps[$name] );
    }

    /**
     * Returns the expiry timestamp for a key
     *
     * @param string $name Expiry key
     *
     * @return int|false The timestamp if it exists, false otherwise
     */
    function timestamp( $name )
    {
        if ( !isset( $this->Timestamps[$name] ) )
        {
            eZDebug::writeError( "Unknown expiry timestamp called '$name'", __METHOD__ );
            return false;
        }
        return $this->Timestamps[$name];
    }

    /**
     * Returns the expiry timestamp for a key, or a default value if it isn't set
     *
     * @param string $name Expiry key name
     * @param int $default Default value that will be returned if the key isn't set
     *
     * @return mixed The expiry timestamp, or $default
     */
    static function getTimestamp( $name, $default = false )
    {
        $handler = eZExpiryHandler::instance();
        if ( !isset( $handler->Timestamps[$name] ) )
        {
            return $default;
        }
        return $handler->Timestamps[$name];
    }

    /**
     * Returns a shared instance of the eZExpiryHandler class
     *
     * @return eZExpiryHandler
     */
    static function instance()
    {
        if ( !isset( $GLOBALS['eZExpiryHandlerInstance'] ) ||
             !( $GLOBALS['eZExpiryHandlerInstance'] instanceof eZExpiryHandler ) )
        {
            $GLOBALS['eZExpiryHandlerInstance'] = new eZExpiryHandler();
        }

        return $GLOBALS['eZExpiryHandlerInstance'];
    }

    /**
     * Checks if a shared instance of eZExpiryHandler exists
     *
     * @return bool true if an instance exists, false otherwise
     */
    static function hasInstance()
    {
        return isset( $GLOBALS['eZExpiryHandlerInstance'] ) && $GLOBALS['eZExpiryHandlerInstance'] instanceof eZExpiryHandler;
    }

    /**
     * Stores and drops the shared instance when it reads another expiry file than the one of the current site
     * (filePath(): its cache directory or ExpiryDir), so that the next instance() reads the right one.
     *
     * The instance can be created before the siteaccess is known, with the expiry.php of the default VarDir. A
     * siteaccess with a VarDir of its own (multi-site hosting) has its own expiry.php; without the reset, clearing a
     * cache of one site wrote the shared file and expired the caches of every site. Timestamps set before the reset
     * are stored into the file they were set for.
     *
     * @return bool true if the instance was dropped
     */
    static function resetForCurrentCacheDirectory()
    {
        if ( !self::hasInstance() )
        {
            return false;
        }
        $instance = $GLOBALS['eZExpiryHandlerInstance'];
        if ( $instance->CacheFile->name() === self::filePath() )
        {
            return false;
        }
        $instance->store();
        unset( $GLOBALS['eZExpiryHandlerInstance'] );
        return true;
    }

    /**
     * Called at the end of execution and will store the data if it is modified.
     */
    static function shutdown()
    {
        if ( eZExpiryHandler::hasInstance() )
        {
            eZExpiryHandler::instance()->store();
        }
    }

    /**
     * Registers the shutdown function.
     * @see eZExpiryHandler::shutdown()
     * @deprecated See EZP-22749
     */
    public static function registerShutdownFunction(){
        eZDebug::writeStrict( __METHOD__ . " is deprecated. See EZP-22749.", __METHOD__ . " is deprecated" );
    }

    /**
     * Holds the expiry timestamps array
     * @var array
     */
    public $Timestamps;

    /**
     * Wether data has been modified or not
     * @var bool
     */
    public $IsModified;

    public $CacheFile;
    
}

?>
