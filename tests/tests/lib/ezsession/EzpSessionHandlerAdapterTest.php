<?php
/**
 * ezpSessionHandlerAdapter gives an ezpSessionHandler to PHP as an object implementing
 * SessionHandlerInterface, instead of the six separate callbacks that are deprecated since PHP 8.4.
 *
 * No kernel, database or session store is needed: a stand-in handler records the calls.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezsession/EzpSessionHandlerAdapterTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ezsession
 */

/**
 * A session handler that records every call and answers with fixed values.
 */
class ezpSessionHandlerAdapterTestHandler extends ezpSessionHandler
{
    /** @var array list of array( method, arguments ) */
    public $calls = array();

    public function open( $savePath, $sessionName ) { $this->calls[] = array( 'open', array( $savePath, $sessionName ) ); return true; }
    public function close() { $this->calls[] = array( 'close', array() ); return true; }
    public function read( $sessionId ) { $this->calls[] = array( 'read', array( $sessionId ) ); return 'data-of-' . $sessionId; }
    public function write( $sessionId, $sessionData ) { $this->calls[] = array( 'write', array( $sessionId, $sessionData ) ); return true; }
    public function destroy( $sessionId ) { $this->calls[] = array( 'destroy', array( $sessionId ) ); return true; }
    public function regenerate( $updateBackendData = true ) { return true; }
    public function gc( $maxLifeTime ) { $this->calls[] = array( 'gc', array( $maxLifeTime ) ); return true; }
    public function cleanup() { return true; }
    public function deleteByUserIDs( array $userIDArray ) { }
}

class EzpSessionHandlerAdapterTest extends \PHPUnit\Framework\TestCase
{
    public function testTheAdapterIsASessionHandlerInterface()
    {
        $adapter = new ezpSessionHandlerAdapter( new ezpSessionHandlerAdapterTestHandler() );
        $this->assertInstanceOf( 'SessionHandlerInterface', $adapter );
    }

    /**
     * Every method passes its arguments to the handler and returns the handler's answer unchanged.
     */
    public function testEveryCallIsPassedOnUnchanged()
    {
        $handler = new ezpSessionHandlerAdapterTestHandler();
        $adapter = new ezpSessionHandlerAdapter( $handler );

        $this->assertTrue( $adapter->open( '/var/sessions', 'eZSESSID' ) );
        $this->assertSame( 'data-of-abc', $adapter->read( 'abc' ) );
        $this->assertTrue( $adapter->write( 'abc', 'payload' ) );
        $this->assertTrue( $adapter->destroy( 'abc' ) );
        $this->assertTrue( $adapter->gc( 1440 ) );
        $this->assertTrue( $adapter->close() );
        $this->assertSame( $handler, $adapter->handler() );

        $this->assertSame( array(
            array( 'open', array( '/var/sessions', 'eZSESSID' ) ),
            array( 'read', array( 'abc' ) ),
            array( 'write', array( 'abc', 'payload' ) ),
            array( 'destroy', array( 'abc' ) ),
            array( 'gc', array( 1440 ) ),
            array( 'close', array() ),
        ), $handler->calls );
    }

    /**
     * setSaveHandler() registers the handler without a deprecation (six callbacks are deprecated since
     * PHP 8.4) and without session_module_name( 'user' ) (a ValueError since PHP 8.0).
     */
    public function testSetSaveHandlerRaisesNoDeprecation()
    {
        if ( session_status() === PHP_SESSION_ACTIVE )
        {
            $this->markTestSkipped( 'a session is active; the save handler cannot be changed' );
        }
        $notices = array();
        set_error_handler( function ( $errno, $errstr ) use ( &$notices ) {
            $notices[] = $errstr;
            return true;
        }, E_DEPRECATED | E_USER_DEPRECATED | E_WARNING );
        try
        {
            $result = ( new ezpSessionHandlerAdapterTestHandler() )->setSaveHandler();
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertTrue( $result );
        $this->assertSame( array(), $notices );
        $this->assertSame( 'user', session_module_name() );
    }
}
