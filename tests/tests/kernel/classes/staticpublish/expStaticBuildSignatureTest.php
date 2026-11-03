<?php
/**
 * The signed request header of a static publishing build (expStaticBuildSignature) and the debug decision it changes
 * (eZDebug::updateSettings()). DB-less.
 *
 *  SG-01 a signature made now is valid; one for another key, changed, malformed or from another version is not
 *  SG-02 expired after LIFETIME seconds; issued more than SKEW in the future is not valid
 *  SG-03 a nonce serves MAX_USES requests (the request and the redirects curl follows), then it is used up
 *  SG-04 only the server's own addresses count: loopback, SERVER_ADDR, the interfaces; a forwarded header never
 *  SG-05 the debug decision: a listed address gets debug output; with a valid signature from the server it gets none
 *        (DebugByUser and the template debug switches too); a forged, expired, replayed or outside request changes
 *        nothing; the header never turns debug output on
 *  SG-06 the template debug switches are given back at the next request (a persistent worker)
 *  SG-07 the build sends a fresh signature with every request; debugOutput() finds what the server should not send
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expStaticBuildSignatureTest extends PHPUnit\Framework\TestCase
{
    const KEY = 'static-build-signature-test-key';

    const GLOBALS = array( 'eZDebugEnabled', 'eZDebugAllowed', 'eZDebugAllowedByIP', 'eZDebugCollect', 'eZDebugUserIDList', 'eZDebugIPMatch',
                           'eZTemplateDebugInternalsEnabled', 'eZTemplateShowTemplatePathComments', 'eZTemplateDebugXHTMLCodeEnabled' );

    private $dir;
    private $server = array();
    private $globals = array();

    protected function setUp(): void
    {
        $this->dir = getcwd() . '/var/tmp/static-build-signature-test-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        foreach ( array( 'REMOTE_ADDR', 'SERVER_ADDR', 'HTTP_X_FORWARDED_FOR', expStaticBuildSignature::SERVER_KEY, 'HTTP_X_EXP_DEBUG' ) as $key )
            $this->server[$key] = isset( $_SERVER[$key] ) ? array( $_SERVER[$key] ) : null;
        foreach ( self::GLOBALS as $name )
            $this->globals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        unset( $_SERVER['HTTP_X_EXP_DEBUG'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER[expStaticBuildSignature::SERVER_KEY] );
        expStaticBuildSignature::resetRequest();
        expStaticBuildSignature::setForTest( self::KEY, $this->dir, array( '192.0.2.10', '2001:db8::10' ) );
    }

    protected function tearDown(): void
    {
        expStaticBuildSignature::resetRequest();
        expStaticBuildSignature::setForTest();
        foreach ( $this->server as $key => $value )
        {
            if ( $value === null )
                unset( $_SERVER[$key] );
            else
                $_SERVER[$key] = $value[0];
        }
        foreach ( $this->globals as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value[0];
        }
        if ( is_dir( $this->dir ) )
        {
            foreach ( (array)glob( $this->dir . '/*' ) as $file )
                unlink( $file );
            rmdir( $this->dir );
        }
    }

    /** SG-01 */
    public function testSignatureIsValidOnlyForItsKey()
    {
        $token = expStaticBuildSignature::sign();
        $this->assertMatchesRegularExpression( '/^v1\.[0-9]+\.[0-9a-f]{32}\.[A-Za-z0-9_-]{43}$/', $token );
        $this->assertSame( '', expStaticBuildSignature::problem( $token ) );
        $this->assertSame( 'the signature does not match', expStaticBuildSignature::problem( $token, null, 'another key' ), 'forged' );
        $parts = explode( '.', $token );
        $parts[2] = str_repeat( 'a', 32 );
        $this->assertSame( 'the signature does not match', expStaticBuildSignature::problem( implode( '.', $parts ) ), 'another nonce' );
        $parts = explode( '.', $token );
        $parts[1] = (string)( (int)$parts[1] - 1 );
        $this->assertSame( 'the signature does not match', expStaticBuildSignature::problem( implode( '.', $parts ) ), 'another time' );
        foreach ( array( '', 'off', 'v2' . substr( $token, 2 ), $token . 'x', str_replace( 'v1.', 'v1..', $token ) ) as $bad )
            $this->assertSame( 'not a static build signature', expStaticBuildSignature::problem( $bad ), $bad );
        $this->assertNotSame( expStaticBuildSignature::sign(), expStaticBuildSignature::sign(), 'a fresh nonce each time' );
    }

    /** SG-02 */
    public function testExpiryAndFuture()
    {
        $now = 1800000000;
        $token = expStaticBuildSignature::sign( $now );
        $this->assertSame( '', expStaticBuildSignature::problem( $token, $now + expStaticBuildSignature::LIFETIME ) );
        $this->assertSame( 'expired', expStaticBuildSignature::problem( $token, $now + expStaticBuildSignature::LIFETIME + 1 ) );
        $this->assertSame( '', expStaticBuildSignature::problem( $token, $now - expStaticBuildSignature::SKEW ) );
        $this->assertSame( 'issued in the future', expStaticBuildSignature::problem( $token, $now - expStaticBuildSignature::SKEW - 1 ) );
    }

    /** SG-03 */
    public function testNonceIsUsedUp()
    {
        // one issue time for every claim: the nonce file is named after it, so a second boundary crossed inside the
        // loop would start a fresh file and let the replay through
        $issued = time();
        $nonce = str_repeat( 'b', 32 );
        for ( $i = 1; $i <= expStaticBuildSignature::MAX_USES; $i++ )
            $this->assertTrue( expStaticBuildSignature::claim( $nonce, $issued ), "use $i" );
        $this->assertFalse( expStaticBuildSignature::claim( $nonce, $issued ), 'replayed once too often' );
        $this->assertTrue( expStaticBuildSignature::claim( str_repeat( 'c', 32 ), $issued ), 'another nonce' );
        $this->assertFalse( expStaticBuildSignature::claim( '../../etc/passwd', time() ) );

        // the files of expired signatures go when a new one comes
        $old = time() - expStaticBuildSignature::LIFETIME - expStaticBuildSignature::SKEW - 10;
        touch( $this->dir . '/' . $old . '-' . str_repeat( 'd', 32 ) . '.1' );
        $this->assertTrue( expStaticBuildSignature::claim( str_repeat( 'e', 32 ), time() ) );
        $this->assertFileDoesNotExist( $this->dir . '/' . $old . '-' . str_repeat( 'd', 32 ) . '.1' );
        $this->assertFileExists( $this->dir . '/' . time() . '-' . str_repeat( 'e', 32 ) . '.1' );
    }

    /** SG-04 */
    public function testLocalAddresses()
    {
        foreach ( array( '127.0.0.1', '127.1.2.3', '::1', '::ffff:127.0.0.1', '192.0.2.10', '2001:DB8::10', '2001:db8:0::10' ) as $address )
            $this->assertTrue( expStaticBuildSignature::isLocalAddress( $address ), $address );
        foreach ( array( '', '203.0.113.9', '192.0.2.11', '::2', 'localhost', '127.0.0.1, 203.0.113.9', '10.0.0.1/8' ) as $address )
            $this->assertFalse( expStaticBuildSignature::isLocalAddress( $address ), $address );
        $this->assertTrue( expStaticBuildSignature::isLocalAddress( '198.51.100.7', array( '198.51.100.7' ) ) );

        // the address the connection came from, never a forwarded one
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1';
        $_SERVER[expStaticBuildSignature::SERVER_KEY] = expStaticBuildSignature::sign();
        $this->assertFalse( expStaticBuildSignature::noDebugRequested() );
        $this->assertSame( 'not from this server', expStaticBuildSignature::reason() );
    }

    private function updateDebug( $enabled = true, array $users = array() )
    {
        expStaticBuildSignature::resetRequest();
        unset( $GLOBALS['eZDebugUserIDList'] );
        eZDebug::updateSettings( array( 'debug-enabled' => $enabled, 'debug-by-ip' => true,
                                        'debug-ip-list' => array( '127.0.0.1', '::1', '192.0.2.10', '203.0.113.9' ),
                                        'debug-by-user' => (bool)$users, 'debug-user-list' => $users ) );
        return eZDebug::isDebugEnabled();
    }

    /** SG-05 */
    public function testDebugDecision()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->assertTrue( $this->updateDebug(), 'the server\'s own address is on the list' );

        $_SERVER[expStaticBuildSignature::SERVER_KEY] = expStaticBuildSignature::sign();
        $this->assertFalse( $this->updateDebug( true, array( 14 ) ), 'a valid signature from the server: no debug output' );
        $this->assertSame( '', expStaticBuildSignature::reason() );
        $this->assertFalse( $GLOBALS['eZDebugAllowed'] );
        $this->assertArrayNotHasKey( 'eZDebugUserIDList', $GLOBALS, 'no DebugByUser left to turn it back on' );
        $this->assertFalse( $GLOBALS['eZTemplateDebugInternalsEnabled'] );
        $this->assertFalse( $GLOBALS['eZTemplateShowTemplatePathComments'] );
        $this->assertFalse( $GLOBALS['eZTemplateDebugXHTMLCodeEnabled'] );
        $this->assertFalse( eZDebug::checkDebugByUser() || eZDebug::isDebugEnabled() );

        // called again in the same request (a siteaccess change): the same verdict, the nonce counted once
        eZDebug::updateSettings( array( 'debug-enabled' => true, 'debug-by-ip' => true, 'debug-ip-list' => array( '127.0.0.1' ) ) );
        $this->assertFalse( eZDebug::isDebugEnabled() );
        $this->assertCount( 1, glob( $this->dir . '/*' ) );

        $_SERVER['REMOTE_ADDR'] = '192.0.2.10';
        $this->assertFalse( $this->updateDebug(), 'from an address of one of the server\'s interfaces' );

        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $this->assertTrue( $this->updateDebug(), 'from outside: ignored' );
        $this->assertSame( 'not from this server', expStaticBuildSignature::reason() );

        $_SERVER['REMOTE_ADDR'] = '::1';
        $_SERVER[expStaticBuildSignature::SERVER_KEY] = expStaticBuildSignature::sign( null, null, 'forged' );
        $this->assertTrue( $this->updateDebug(), 'forged' );
        $this->assertSame( 'the signature does not match', expStaticBuildSignature::reason() );

        $_SERVER[expStaticBuildSignature::SERVER_KEY] = expStaticBuildSignature::sign( time() - expStaticBuildSignature::LIFETIME - 1 );
        $this->assertTrue( $this->updateDebug(), 'expired' );
        $this->assertSame( 'expired', expStaticBuildSignature::reason() );

        $_SERVER[expStaticBuildSignature::SERVER_KEY] = expStaticBuildSignature::sign();
        for ( $i = 1; $i <= expStaticBuildSignature::MAX_USES; $i++ )
            $this->assertFalse( $this->updateDebug(), "use $i" );
        $this->assertTrue( $this->updateDebug(), 'replayed' );
        $this->assertSame( 'used up', expStaticBuildSignature::reason() );

        // never on: debug output disabled in site.ini, or the address not on the list
        $_SERVER[expStaticBuildSignature::SERVER_KEY] = expStaticBuildSignature::sign();
        $this->assertFalse( $this->updateDebug( false ) );
        $_SERVER[expStaticBuildSignature::SERVER_KEY] = 'on';
        $this->assertFalse( $this->updateDebug( false ) );

        // without a secret in the installation nothing is checked (and none is made)
        expStaticBuildSignature::setForTest( null, $this->dir, array() );
        $_SERVER[expStaticBuildSignature::SERVER_KEY] = 'v1.' . time() . '.' . str_repeat( 'f', 32 ) . '.' . str_repeat( 'A', 43 );
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        expMailSecret::setForTest( $this->dir . '/no-secret-here' );
        try
        {
            $this->assertTrue( $this->updateDebug() );
            $this->assertSame( 'the installation has no secret', expStaticBuildSignature::reason() );
            $this->assertDirectoryDoesNotExist( $this->dir . '/no-secret-here' );
        }
        finally
        {
            expMailSecret::setForTest();
            expMailSecret::reset();
        }
    }

    /** SG-06 */
    public function testTemplateSwitchesComeBack()
    {
        $GLOBALS['eZTemplateDebugInternalsEnabled'] = true;
        unset( $GLOBALS['eZTemplateShowTemplatePathComments'] );
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER[expStaticBuildSignature::SERVER_KEY] = expStaticBuildSignature::sign();
        $this->assertFalse( $this->updateDebug() );
        $this->assertFalse( $GLOBALS['eZTemplateDebugInternalsEnabled'] );
        expStaticBuildSignature::resetRequest();
        $this->assertTrue( $GLOBALS['eZTemplateDebugInternalsEnabled'] );
        $this->assertArrayNotHasKey( 'eZTemplateShowTemplatePathComments', $GLOBALS );
    }

    /** SG-07 */
    public function testBuildSignsEveryRequestAndFindsDebugOutput()
    {
        $line = expStaticBuildSignature::headerLine();
        $this->assertStringStartsWith( 'X-Exp-Static-Build: v1.', $line );
        $this->assertSame( '', expStaticBuildSignature::problem( substr( $line, strlen( 'X-Exp-Static-Build: ' ) ) ) );

        $build = new expStaticBuild( new expStaticTarget( 'sig', array( 'SiteAccess' => 'site' ) ), array( 'fetcher' => function () { return array(); }, 'sign_requests' => true ) );
        $first = $build->requestHeaders();
        $second = $build->requestHeaders();
        $this->assertCount( 1, $first );
        $this->assertNotSame( $first, $second, 'signed afresh for each request' );
        $unsigned = new expStaticBuild( new expStaticTarget( 'sig', array( 'SiteAccess' => 'site' ) ), array( 'fetcher' => function () { return array(); } ) );
        $this->assertSame( array(), $unsigned->requestHeaders(), 'a test fetcher is not signed for unless asked' );

        $this->assertSame( array(), expStaticPostProcessor::debugOutput( '<html><body><p>debug</p><div id="main"></div></body></html>', array( 'content-type' ) ) );
        $this->assertSame( array( '<div id="debug"' ), expStaticPostProcessor::debugOutput( '<body><div id="debug"><h2>x</h2></div>' ) );
        $this->assertSame( array( '<!--exp-debug-bar-->' ), expStaticPostProcessor::debugOutput( '<body><!--exp-debug-bar--></body>' ) );
        $this->assertSame( array( '<!-- START: including template' ), expStaticPostProcessor::debugOutput( "\n<!-- START: including template: a.tpl (a.tpl) -->\n" ) );
        $this->assertSame( array( 'the header x-debug-token' ), expStaticPostProcessor::debugOutput( '<p>ok</p>', array( 'x-debug-token' ) ) );
    }
}
