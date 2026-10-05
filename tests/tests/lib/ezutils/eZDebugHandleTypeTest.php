<?php
/**
 * Tests of eZDebug::setHandleType(): eZDebug installs and removes only its own PHP error handler.
 *
 * Every debug message is written with the handle type switched to HANDLE_TO_PHP and back. HANDLE_TO_PHP and
 * HANDLE_NONE used to call restore_error_handler() although they install nothing, which took off the handler below
 * eZDebug's: the application's, a persistent worker's or the test runner's, once per message.
 *
 * No kernel bootstrap, no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdebug
 */

class eZDebugHandleTypeTest extends PHPUnit\Framework\TestCase
{
    private $oldType;

    private $sentinel;

    protected function setUp(): void
    {
        $this->oldType = eZDebug::setHandleType( eZDebug::HANDLE_NONE );
        $this->sentinel = function ( $errno, $errstr )
        {
            return false;
        };
        set_error_handler( $this->sentinel );
    }

    protected function tearDown(): void
    {
        eZDebug::setHandleType( eZDebug::HANDLE_NONE );
        // take off the sentinel only when it is still on top; the test has failed otherwise
        if ( self::currentHandler() === $this->sentinel )
            restore_error_handler();
        eZDebug::setHandleType( $this->oldType );
    }

    private static function currentHandler()
    {
        $handler = set_error_handler( 'var_dump' );
        restore_error_handler();
        return $handler;
    }

    private static function isDebugHandler( $handler )
    {
        return is_array( $handler ) && $handler[0] instanceof eZDebug;
    }

    public static function switchProvider()
    {
        return array(
            'none to php'          => array( array( eZDebug::HANDLE_TO_PHP ) ),
            'to php and back'      => array( array( eZDebug::HANDLE_TO_PHP, eZDebug::HANDLE_NONE ) ),
            'from php and back'    => array( array( eZDebug::HANDLE_FROM_PHP, eZDebug::HANDLE_NONE ) ),
            'from php to php'      => array( array( eZDebug::HANDLE_FROM_PHP, eZDebug::HANDLE_TO_PHP, eZDebug::HANDLE_NONE ) ),
            'exception and back'   => array( array( eZDebug::HANDLE_EXCEPTION, eZDebug::HANDLE_NONE ) ),
            'exception to from'    => array( array( eZDebug::HANDLE_EXCEPTION, eZDebug::HANDLE_FROM_PHP, eZDebug::HANDLE_NONE ) ),
            'many round trips'     => array( array( eZDebug::HANDLE_TO_PHP, eZDebug::HANDLE_NONE, eZDebug::HANDLE_TO_PHP,
                                                    eZDebug::HANDLE_FROM_PHP, eZDebug::HANDLE_TO_PHP, eZDebug::HANDLE_NONE ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('switchProvider')]
    public function testTheHandlerBelowSurvivesAnySequenceEndingInNone( $types )
    {
        foreach ( $types as $type )
            eZDebug::setHandleType( $type );
        if ( extension_loaded( 'xdebug' ) && in_array( eZDebug::HANDLE_FROM_PHP, $types ) )
            $this->assertTrue( true ); // with Xdebug, FROM_PHP is NONE: nothing else to see
        $this->assertSame( $this->sentinel, self::currentHandler() );
    }

    public function testFromPhpPutsTheDebugHandlerOnTop()
    {
        if ( extension_loaded( 'xdebug' ) )
            $this->markTestSkipped( 'eZDebug does not take PHP errors while Xdebug is loaded' );
        eZDebug::setHandleType( eZDebug::HANDLE_FROM_PHP );
        $this->assertTrue( self::isDebugHandler( self::currentHandler() ) );
        eZDebug::setHandleType( eZDebug::HANDLE_TO_PHP );
        $this->assertSame( $this->sentinel, self::currentHandler() );
        eZDebug::setHandleType( eZDebug::HANDLE_FROM_PHP );
        $this->assertTrue( self::isDebugHandler( self::currentHandler() ) );
    }

    public function testExceptionPutsTheDebugHandlerOnTop()
    {
        eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        $this->assertTrue( self::isDebugHandler( self::currentHandler() ) );
        eZDebug::setHandleType( eZDebug::HANDLE_NONE );
        $this->assertSame( $this->sentinel, self::currentHandler() );
    }

    public function testSettingTheSameTypeTwiceChangesNothing()
    {
        eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION );
        eZDebug::setHandleType( eZDebug::HANDLE_NONE );
        $this->assertSame( $this->sentinel, self::currentHandler() );
    }

    public function testReturnsThePreviousType()
    {
        $this->assertSame( eZDebug::HANDLE_NONE, eZDebug::setHandleType( eZDebug::HANDLE_EXCEPTION ) );
        $this->assertSame( eZDebug::HANDLE_EXCEPTION, eZDebug::setHandleType( eZDebug::HANDLE_TO_PHP ) );
        $this->assertSame( eZDebug::HANDLE_TO_PHP, eZDebug::setHandleType( eZDebug::HANDLE_NONE ) );
    }

    public function testWritingDebugMessagesLeavesTheHandlerBelow()
    {
        eZDebug::writeNotice( 'eZDebugHandleTypeTest notice', __METHOD__ );
        eZDebug::writeWarning( 'eZDebugHandleTypeTest warning', __METHOD__ );
        eZDebug::writeError( 'eZDebugHandleTypeTest error', __METHOD__ );
        eZDebug::writeDebug( 'eZDebugHandleTypeTest debug', __METHOD__ );
        $this->assertSame( $this->sentinel, self::currentHandler() );
    }
}
