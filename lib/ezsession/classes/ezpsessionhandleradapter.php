<?php
/**
 * File containing the session handler adapter
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * Gives an ezpSessionHandler to PHP as an object implementing SessionHandlerInterface.
 *
 * session_set_save_handler() with six separate callbacks is deprecated since PHP 8.4; the object form
 * works on every PHP version Exponential supports (8.0 and later). Each method passes the call on to
 * the handler unchanged, so handlers (including those of extensions that inherit
 * ezpSessionHandler::setSaveHandler()) keep their open/close/read/write/destroy/gc methods as they are.
 *
 * The methods carry #[\ReturnTypeWillChange] instead of return types: the interface has tentative return
 * types since PHP 8.1, and the handlers return what they always returned (gc() returns a bool).
 *
 * @package lib
 * @subpackage ezsession
 */
class ezpSessionHandlerAdapter implements SessionHandlerInterface
{
    /**
     * @param ezpSessionHandler $handler
     */
    public function __construct( ezpSessionHandler $handler )
    {
        $this->handler = $handler;
    }

    /**
     * @return ezpSessionHandler
     */
    public function handler()
    {
        return $this->handler;
    }

    #[\ReturnTypeWillChange]
    public function open( $savePath, $sessionName )
    {
        return $this->handler->open( $savePath, $sessionName );
    }

    #[\ReturnTypeWillChange]
    public function close()
    {
        return $this->handler->close();
    }

    #[\ReturnTypeWillChange]
    public function read( $sessionId )
    {
        return $this->handler->read( $sessionId );
    }

    #[\ReturnTypeWillChange]
    public function write( $sessionId, $sessionData )
    {
        return $this->handler->write( $sessionId, $sessionData );
    }

    #[\ReturnTypeWillChange]
    public function destroy( $sessionId )
    {
        return $this->handler->destroy( $sessionId );
    }

    #[\ReturnTypeWillChange]
    public function gc( $maxLifeTime )
    {
        return $this->handler->gc( $maxLifeTime );
    }

    /**
     * @var ezpSessionHandler
     */
    private $handler;
}
?>
