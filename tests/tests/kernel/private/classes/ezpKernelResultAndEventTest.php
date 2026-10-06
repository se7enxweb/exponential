<?php
/**
 * ezpKernelResult (content and attributes), ezpKernelRedirect (target and status code from the status line) and
 * ezpEvent (attach, detach, notify, filter, uncallable listeners skipped, the listeners of site.ini [Event] attached
 * once however often registerEventListeners() runs, entries without <event>@<callback> or with a callback that is
 * no function or Class::method name skipped, blanks dropped, duplicates attached once, mistakes logged once).
 *
 * No database. site.ini [Event] Listeners is injected and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezpKernelResultAndEventTestListener
{
    public static $calls = array();

    public static function record()
    {
        self::$calls[] = func_get_args();
    }

    public static function upper( $value )
    {
        return strtoupper( $value );
    }

    public static function suffix( $value, $suffix = '' )
    {
        return $value . $suffix;
    }
}

class ezpKernelResultAndEventTest extends PHPUnit\Framework\TestCase
{
    private $injected;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        ezpKernelResultAndEventTestListener::$calls = array();
        $property = new ReflectionProperty( 'eZINI', 'injectedSettings' );
        $this->injected = $property->getValue();
    }

    protected function tearDown(): void
    {
        eZINI::injectSettings( $this->injected );
    }

    // ---------------------------------------------------------------- ezpKernelResult

    public function testResultContentAndAttributes()
    {
        $result = new ezpKernelResult( 'body', array( 'a' => 1, 'b' => 2 ) );
        $this->assertSame( 'body', $result->getContent() );
        $result->setContent( 'other' );
        $this->assertSame( 'other', $result->getContent() );
        $this->assertSame( 1, $result->getAttribute( 'a' ) );
        $this->assertSame( 'dflt', $result->getAttribute( 'missing', 'dflt' ) );
        $this->assertNull( $result->getAttribute( 'missing' ) );
        $this->assertTrue( $result->hasAttribute( 'b' ) );
        $this->assertFalse( $result->hasAttribute( 'c' ) );
        $result->setAttribute( 'c', 3 );
        $this->assertSame( array( 'a' => 1, 'b' => 2, 'c' => 3 ), $result->getAttributes() );
    }

    public function testSetAttributesAddsAndOverridesReplaceAttributesReplaces()
    {
        $result = new ezpKernelResult( null, array( 'a' => 1, 'b' => 2 ) );
        $result->setAttributes( array( 'b' => 20, 'c' => 30 ) );
        $this->assertSame( array( 'b' => 20, 'c' => 30, 'a' => 1 ), $result->getAttributes() );
        $result->replaceAttributes( array( 'z' => 0 ) );
        $this->assertSame( array( 'z' => 0 ), $result->getAttributes() );
        $this->assertFalse( $result->hasAttribute( 'a' ) );
    }

    public function testNullAttributeCountsAsMissing()
    {
        $result = new ezpKernelResult();
        $result->setAttribute( 'n', null );
        $this->assertFalse( $result->hasAttribute( 'n' ) );
        $this->assertSame( 'd', $result->getAttribute( 'n', 'd' ) );
        $this->assertNull( $result->getContent() );
    }

    // ---------------------------------------------------------------- ezpKernelRedirect

    public static function redirectProvider()
    {
        return array(
            'default' => array( '/a', null, '/a', 302 ),
            'status line' => array( '/b', '301 Moved Permanently', '/b', 301 ),
            'number' => array( 'https://x.example.invalid/', 307, 'https://x.example.invalid/', 307 ),
            'empty url goes home' => array( '', '303 See Other', '/', 303 ),
            'null url goes home' => array( null, null, '/', 302 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('redirectProvider')]
    public function testRedirect( $url, $status, $target, $code )
    {
        $redirect = new ezpKernelRedirect( $url, $status, 'c' );
        $this->assertInstanceOf( 'ezpKernelResult', $redirect );
        $this->assertSame( $target, $redirect->getTargetUrl() );
        $this->assertSame( $code, $redirect->getStatusCode() );
        $this->assertSame( $status, $redirect->getAttribute( 'status' ) );
        $this->assertSame( 'c', $redirect->getContent() );
    }

    // ---------------------------------------------------------------- ezpEvent

    public function testNotifyWithoutListenersIsFalse()
    {
        $event = new ezpEvent( false );
        $this->assertFalse( $event->notify( 'k1/none', array( 1 ) ) );
        $this->assertSame( 'v', $event->filter( 'k1/none', 'v' ) );
    }

    public function testNotifyCallsEveryListenerInOrder()
    {
        $event = new ezpEvent( false );
        $seen = array();
        $event->attach( 'k1/a', function ( $x ) use ( &$seen ) { $seen[] = "first $x"; } );
        $event->attach( 'k1/a', function ( $x ) use ( &$seen ) { $seen[] = "second $x"; } );
        $event->attach( 'k1/b', function () use ( &$seen ) { $seen[] = 'other'; } );
        $this->assertTrue( $event->notify( 'k1/a', array( 7 ) ) );
        $this->assertSame( array( 'first 7', 'second 7' ), $seen );
    }

    public function testStaticStringListenerIsSplit()
    {
        $event = new ezpEvent( false );
        $event->attach( 'k1/s', 'ezpKernelResultAndEventTestListener::record' );
        $event->notify( 'k1/s', array( 'x', 'y' ) );
        $this->assertSame( array( array( 'x', 'y' ) ), ezpKernelResultAndEventTestListener::$calls );
    }

    public function testDetach()
    {
        $event = new ezpEvent( false );
        $id = $event->attach( 'k1/d', 'ezpKernelResultAndEventTestListener::record' );
        $other = $event->attach( 'k1/d', 'ezpKernelResultAndEventTestListener::record' );
        $this->assertNotSame( $id, $other );
        $this->assertFalse( $event->detach( 'k1/other', $id ) );
        $this->assertTrue( $event->detach( 'k1/d', $id ) );
        $this->assertFalse( $event->detach( 'k1/d', $id ) );
        $event->notify( 'k1/d' );
        $this->assertCount( 1, ezpKernelResultAndEventTestListener::$calls );
        $event->detach( 'k1/d', $other );
        $this->assertFalse( $event->notify( 'k1/d' ) );
    }

    public function testFilterChainsTheFirstValueAndPassesTheRest()
    {
        $event = new ezpEvent( false );
        $event->attach( 'k1/f', 'ezpKernelResultAndEventTestListener::upper' );
        $event->attach( 'k1/f', array( 'ezpKernelResultAndEventTestListener', 'suffix' ) );
        $this->assertSame( 'ABC!', $event->filter( 'k1/f', 'abc', '!' ) );
    }

    public function testUncallableListenersAreSkipped()
    {
        $event = new ezpEvent( false );
        $event->attach( 'k1/u', 'k1NoSuchClass::method' );
        $event->attach( 'k1/u', array( new stdClass(), 'nothing' ) );
        $event->attach( 'k1/u', 42 );
        $event->attach( 'k1/u', 'ezpKernelResultAndEventTestListener::upper' );
        $this->assertSame( 'OK', $event->filter( 'k1/u', 'ok' ) );
        $this->assertTrue( $event->notify( 'k1/u', array( 'x' ) ) );
    }

    public function testNoGlobalEventsWhenNotAsked()
    {
        $event = new ezpEvent( false );
        $event->registerEventListeners();
        $this->assertFalse( $event->notify( 'k1/global' ) );
    }

    public function testGlobalListenersAreAttachedOnceAcrossRegistrations()
    {
        $settings = $this->injected;
        $settings['site.ini']['Event']['Listeners'] = array( 'k1/global@ezpKernelResultAndEventTestListener::record', '' );
        eZINI::injectSettings( $settings );
        eZINI::instance()->loadPlacement();
        eZINI::instance()->load();
        $event = new ezpEvent( true );
        $event->registerEventListeners();
        $event->registerEventListeners();
        $runtime = $event->attach( 'k1/global', 'ezpKernelResultAndEventTestListener::record' );
        $event->registerEventListeners();
        $event->notify( 'k1/global', array( 'n' ) );
        $this->assertSame( array( array( 'n' ), array( 'n' ) ), ezpKernelResultAndEventTestListener::$calls, 'one from site.ini, one attached at run time' );
        $this->assertTrue( $event->detach( 'k1/global', $runtime ) );
        eZINI::injectSettings( $this->injected );
        eZINI::instance()->load();
    }

    /**
     * An entry of site.ini [Event] Listeners[] without the form <event>@<callback> is logged and skipped; it raised
     * an "Undefined array key" warning and attached a listener without a callback (or one to an event named '').
     */
    public function testMalformedGlobalListenersAreSkipped()
    {
        $settings = $this->injected;
        $settings['site.ini']['Event']['Listeners'] = array(
            'k1/nocallback',
            '@ezpKernelResultAndEventTestListener::record',
            'k1/malformed@',
            'k1/malformed@ezpKernelResultAndEventTestListener::record',
        );
        eZINI::injectSettings( $settings );
        eZINI::instance()->loadPlacement();
        eZINI::instance()->load();
        $event = new ezpEvent( true );
        $event->registerEventListeners();

        $listeners = new ReflectionProperty( 'ezpEvent', 'listeners' );
        $attached = $listeners->getValue( $event );
        $this->assertArrayNotHasKey( '', $attached );
        $this->assertArrayNotHasKey( 'k1/nocallback', $attached );
        $this->assertCount( 1, $attached['k1/malformed'] );
        $event->notify( 'k1/malformed', array( 'n' ) );
        $this->assertSame( array( array( 'n' ) ), ezpKernelResultAndEventTestListener::$calls );
        eZINI::injectSettings( $this->injected );
        eZINI::instance()->load();
    }

    /** Injects $listeners as site.ini [Event] Listeners[] and returns a fresh ezpEvent that registered them */
    private function eventWithGlobalListeners( array $listeners )
    {
        $settings = $this->injected;
        $settings['site.ini']['Event']['Listeners'] = $listeners;
        eZINI::injectSettings( $settings );
        eZINI::instance()->loadPlacement();
        eZINI::instance()->load();
        $event = new ezpEvent( true );
        $event->registerEventListeners();
        return $event;
    }

    private function attachedListeners( ezpEvent $event )
    {
        $listeners = new ReflectionProperty( 'ezpEvent', 'listeners' );
        return $listeners->getValue( $event );
    }

    private function loggedOnce()
    {
        $logged = new ReflectionProperty( 'ezpEvent', 'loggedOnce' );
        return $logged->getValue();
    }

    /** Blanks around the event and the callback are dropped; the entry is attached under the trimmed event name */
    public function testBlanksAroundAGlobalListenerAreDropped()
    {
        $event = $this->eventWithGlobalListeners( array( "  k1/blank @ ezpKernelResultAndEventTestListener::record \t" ) );
        $attached = $this->attachedListeners( $event );
        $this->assertArrayHasKey( 'k1/blank', $attached );
        $this->assertSame( array( array( 'ezpKernelResultAndEventTestListener', 'record' ) ), array_values( $attached['k1/blank'] ) );
        $event->notify( 'k1/blank', array( 'b' ) );
        $this->assertSame( array( array( 'b' ) ), ezpKernelResultAndEventTestListener::$calls );
        eZINI::injectSettings( $this->injected );
        eZINI::instance()->load();
    }

    /** The same listener listed twice (also with other blanks) is attached once and runs once per event */
    public function testADuplicateGlobalListenerIsAttachedOnce()
    {
        $event = $this->eventWithGlobalListeners( array( 'k1/dup@ezpKernelResultAndEventTestListener::record',
                                                         ' k1/dup@ezpKernelResultAndEventTestListener::record',
                                                         'k1/dup@ezpKernelResultAndEventTestListener::upper' ) );
        $this->assertCount( 2, $this->attachedListeners( $event )['k1/dup'] );
        $event->notify( 'k1/dup', array( 'x' ) );
        $this->assertSame( array( array( 'x' ) ), ezpKernelResultAndEventTestListener::$calls );
        $this->assertArrayHasKey( 'site.ini [Event] Listeners[]=k1/dup@ezpKernelResultAndEventTestListener::record is listed more than once; attached once', $this->loggedOnce() );
        eZINI::injectSettings( $this->injected );
        eZINI::instance()->load();
    }

    /** A callback that is not written as a function or Class::method name is skipped without being looked up */
    public function testACallbackThatIsNoNameIsSkipped()
    {
        $event = $this->eventWithGlobalListeners( array( 'k1/name@ezpKernelResultAndEventTestListener::record; drop',
                                                         'k1/name@k1 Class::method',
                                                         'k1/name@ezpKernelResultAndEventTestListener:::record',
                                                         'k1/name@\\k1\\Name\\Space\\k1Class::method',
                                                         'k1/name@ezpKernelResultAndEventTestListener::record' ) );
        $attached = array_values( $this->attachedListeners( $event )['k1/name'] );
        $this->assertSame( array( array( '\\k1\\Name\\Space\\k1Class', 'method' ),
                                  array( 'ezpKernelResultAndEventTestListener', 'record' ) ), $attached );
        eZINI::injectSettings( $this->injected );
        eZINI::instance()->load();
    }

    /**
     * A mistake in site.ini is logged once in the life of the process, not once per request: a persistent worker
     * registers the listeners again for every request. A listener whose class is missing is logged once as well.
     */
    public function testMistakesAreLoggedOncePerProcess()
    {
        $event = $this->eventWithGlobalListeners( array( 'k1/once-without-callback', 'k1/once@k1NoSuchClassOnce::method' ) );
        $before = $this->loggedOnce();
        $this->assertArrayHasKey( 'site.ini [Event] Listeners[]=k1/once-without-callback is not of the form <event>@<callback>; skipped', $before );
        $event->notify( 'k1/once' );
        $afterFirst = $this->loggedOnce();
        $this->assertArrayHasKey( 'Listener k1NoSuchClassOnce::method for event k1/once cannot be called (no such class, method or function); skipped', $afterFirst );

        // the next "request"
        $event->registerEventListeners();
        $event->notify( 'k1/once' );
        $event->notify( 'k1/once' );
        $this->assertSame( $afterFirst, $this->loggedOnce() );
        $this->assertCount( 1, $this->attachedListeners( $event )['k1/once'] );
        eZINI::injectSettings( $this->injected );
        eZINI::instance()->load();
    }

    public function testInstanceIsSharedUntilReset()
    {
        $property = new ReflectionProperty( 'ezpEvent', 'instance' );
        $original = $property->getValue();
        $one = ezpEvent::getInstance();
        $this->assertSame( $one, ezpEvent::getInstance() );
        ezpEvent::resetInstance();
        $two = ezpEvent::getInstance();
        $this->assertNotSame( $one, $two );
        $this->assertInstanceOf( 'ezpEvent', $two );
        $property->setValue( null, $original );
    }
}
