<?php
/**
 * File containing abstract session handler
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/** Abstract session handler class to extend
 *
 * CALLBACKS:
 * destroy, gc, regenerate & cleanup functions MUST implement callbacks.
 * Definition is documented in functions on this class, examples can be
 * found in the handlers and examples of use can be sessin in ezsession.php
 * class doc section.
 *
 * @since 4.4
 * @abstract
 * @package lib
 * @subpackage ezsession
 */
abstract class ezpSessionHandler
{
    /**
     * __construct
     *
     * @param bool $userHasCookie
     */
    public function __construct( $userHasCookie = false )
    {
        $this->userHasCookie = $userHasCookie;
    }

    /**
     * Checks if session handler is connected with backend.
     *
     * @return bool
     */
    public function isConnected()
    {
        return true;
    }

    /**
     * Set it self as save handler
     *
     * @return bool
     */
    public function setSaveHandler()
    {
        // No session_module_name( 'user' ): PHP 8 throws a ValueError for it, and
        // session_set_save_handler() selects the user module by itself.
        // The handler is given as an object (ezpSessionHandlerAdapter): six separate callbacks are deprecated
        // since PHP 8.4. No shutdown function is registered (false), as with the callbacks before: eZSession
        // and the persistent workers of Exponential Velocity write and close the session themselves.
        session_set_save_handler( new ezpSessionHandlerAdapter( $this ), false );
        return true;
    }

    /**
     * Session open handler
     *
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open( $savePath, $sessionName )
    {
        return true;
    }

    /**
     * Session close handler
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * Session read handler
     *
     * @param string $sessionId
     * @return string|false Binary session data
     */
    abstract public function read( $sessionId );

    /**
     * Session write handler
     *
     * @param string $sessionId
     * @param string $sessionData Binary session data
     * @return bool
     */
    abstract public function write( $sessionId, $sessionData );

    /**
     * Session destroy handler
     * Callback: "destroy_[pre|post]"
     *           eZDB $db
     *           string $sessionId
     *           string $escKey
     *
     * @param string $sessionId
     * @return bool
     */
    abstract public function destroy( $sessionId );

    /**
     * Regenerate session id
     * Callback (when $updateBackendData is true): "regenerate_[pre|post]"
     *           eZDB $db
     *           string $escNewKey
     *           string $escOldKey
     *           int $escUserID
     *
     * @param bool $updateBackendData True if we want to keep session data with the new session id
     * @return bool
     */
    abstract public function regenerate( $updateBackendData = true );

   /**
     * Session gc (garbageCollector) handler
     * Callback: "gc_[pre|post]"
     *           eZDB $db
     *           int $maxLifeTime
     *
     * @param int $maxLifeTime In seconds
     * @return bool
     */
    abstract public function gc( $maxLifeTime );

    /**
     * Remove all session data
     * Callback: "cleanup_[pre|post]"
     *           eZDB $db
     *
     * @return bool
     */
    abstract public function cleanup();

    /**
     * Remove all session data for a specific user
     *
     * @param array(int) $userIDArray
     */
    abstract public function deleteByUserIDs( array $userIDArray );

    /**
     * Ends every session of a user except one (after a password change: the user stays signed in where the change
     * was made and is signed out everywhere else).
     *
     * A handler that cannot find the sessions of a user returns null, which is what this default does; handlers
     * with a backend override it (ezpSessionHandlerDB).
     *
     * @param int $userID
     * @param string $keepSessionKey the session key to keep ('' keeps none)
     * @return int|null the number of sessions ended, null when the handler cannot
     */
    public function deleteOtherSessionsOfUser( $userID, $keepSessionKey )
    {
        return null;
    }

    /**
     * Counts the number of session and returns it.
     *
     * @return int Returns -1 if handler does not support this.
     */
    static public function count()
    {
        return -1;
    }

    /**
     * Signals that handler has direct access to backend, thus is cable of supporting features
     * like gc, cleanup, delete & count.
     *
     * @return bool
     */
    static public function hasBackendAccess()
    {
        return true;
    }

    /**
     * Signals that handler requires db instance
     *
     * @return bool
     */
    static public function dbRequired()
    {
        return true;
    }

    /**
     * Starts the session.
     * Override this method if you need to delegate session start to an external system (e.g. Symfony stack in eZ Publish 5)
     *
     * @since 5.0
     * @return bool
     */
    public function sessionStart()
    {
        return session_start();
    }

    public $userHasCookie;
}
?>
