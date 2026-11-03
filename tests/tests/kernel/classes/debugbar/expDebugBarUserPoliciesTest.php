<?php
/**
 * Who sees the debug bar's User tab, who reads what (debugbar.ini ReadAccess) and how the policies are asked for,
 * continuing expDebugBarPoliciesTest (UP-01 to UP-04, UP-06 to UP-08):
 *   - UP-05 the tab: setup/setup always; with debugbar.ini ReadAccess=debug (the default) whoever gets the debug
 *           output, logged in or not; with ReadAccess=privileged an anonymous visitor with debug output, not a
 *           logged-in user without setup/setup (expDebugBarUser::mayView())
 *   - UP-11 ReadAccess: debug unless it says privileged; readsAll() only with debug output; reading the cache list
 *           is not clearing, the form for it only with setup/managecache
 *   - UP-12 a reader by the debug output (ReadAccess=debug, no setup/setup) gets nobody else's data: the change log
 *           without user id, login and address, the IP and the user list masked, the IP test without the list lines
 *   - UP-09 expdebugbar::policies takes its arguments from the path: role id or "-", offset, the search as base64url
 *           ("-" for none); anything else refused, the search held to 100 characters
 *   - UP-10 the script asks with everything in the path, never the query string, and only when the section is opened
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarUserPoliciesTest extends PHPUnit\Framework\TestCase
{
    /** UP-05 */
    public function testWhoMaySeeTheTab()
    {
        foreach ( array( 'debug', 'privileged' ) as $mode )
        {
            $this->assertTrue( expDebugBarUser::mayView( true, true, false, $mode ), "$mode: setup/setup, with or without debug output" );
            $this->assertTrue( expDebugBarUser::mayView( true, true, true, $mode ), $mode );
            $this->assertTrue( expDebugBarUser::mayView( false, false, true, $mode ), "$mode: an anonymous visitor with debug output" );
            $this->assertFalse( expDebugBarUser::mayView( false, false, false, $mode ), "$mode: an anonymous visitor without debug output" );
            $this->assertFalse( expDebugBarUser::mayView( false, true, false, $mode ), "$mode: a logged-in user without setup/setup and without debug output" );
        }
        $this->assertTrue( expDebugBarUser::mayView( false, true, true, 'debug' ), 'debug: a logged-in user without setup/setup who gets the debug output, their own data' );
        $this->assertFalse( expDebugBarUser::mayView( false, true, true, 'privileged' ), 'privileged: not a logged-in user without setup/setup' );
    }

    private function barIni( $readAccess )
    {
        $ini = $this->createStub( eZINI::class );
        $ini->method( 'hasVariable' )->willReturnCallback( function ( $block, $name ) use ( $readAccess ) {
            return $block === 'DebugBarSettings' && $name === 'ReadAccess' && $readAccess !== null;
        } );
        $ini->method( 'variable' )->willReturn( (string)$readAccess );
        return $ini;
    }

    /** UP-11 */
    public function testReadAccess()
    {
        $this->assertSame( 'debug', expDebugBarRegistry::readAccess( $this->barIni( null ) ), 'not set: debug, everything' );
        $this->assertSame( 'debug', expDebugBarRegistry::readAccess( $this->barIni( 'debug' ) ) );
        $this->assertSame( 'privileged', expDebugBarRegistry::readAccess( $this->barIni( ' Privileged ' ) ) );
        $this->assertSame( 'debug', expDebugBarRegistry::readAccess( $this->barIni( 'bogus' ) ), 'an unknown word: the default' );
        $this->assertTrue( expDebugBarRegistry::readsAll( true, $this->barIni( null ) ) );
        $this->assertFalse( expDebugBarRegistry::readsAll( false, $this->barIni( null ) ), 'never without debug output' );
        $this->assertFalse( expDebugBarRegistry::readsAll( true, $this->barIni( 'privileged' ) ) );

        $cacheHtml = new ReflectionMethod( 'expDebugBarReport', 'cacheHtml' );
        $page = array( 'node_id' => 2, 'object_id' => 1, 'uri' => '/' );
        $reader = $cacheHtml->invoke( null, $page, array( 'can_cache' => false, 'read_cache' => true ) );
        $this->assertStringContainsString( 'class="exp-debug-cache"', $reader, 'the list for a reader' );
        $this->assertStringNotContainsString( 'setup/cachetoolbar', $reader, 'no form that clears for a reader' );
        $manager = $cacheHtml->invoke( null, $page, array( 'can_cache' => true, 'read_cache' => true ) );
        $this->assertStringContainsString( 'setup/cachetoolbar', $manager );
        $nobody = $cacheHtml->invoke( null, $page, array( 'can_cache' => false, 'read_cache' => false ) );
        $this->assertStringNotContainsString( 'class="exp-debug-cache"', $nobody );
    }

    private static function b64url( $text )
    {
        return rtrim( strtr( base64_encode( $text ), '+/', '-_' ), '=' );
    }

    /** UP-09 */
    public function testTheArgumentsInThePath()
    {
        $this->assertSame( array( null, 0, '' ), expDebugBarServerFunctions::policyArgs( array() ), 'nothing: all, the first page' );
        $this->assertSame( array( null, 0, '' ), expDebugBarServerFunctions::policyArgs( array( '-', '0', '-' ) ) );
        $this->assertSame( array( 7, 200, '' ), expDebugBarServerFunctions::policyArgs( array( '7', '200' ) ) );
        $this->assertSame( array( null, 0, 'content/read' ), expDebugBarServerFunctions::policyArgs( array( '-', '0', self::b64url( 'content/read' ) ) ), 'a "/" in the search' );
        $this->assertSame( array( null, 0, 'Médias::x' ), expDebugBarServerFunctions::policyArgs( array( '-', '0', self::b64url( 'Médias::x' ) ) ), 'UTF-8 and "::"' );
        $long = expDebugBarServerFunctions::policyArgs( array( '-', '0', self::b64url( str_repeat( 'ä', 150 ) ) ) );
        $this->assertSame( 100, mb_strlen( $long[2], 'UTF-8' ), 'at most 100 characters' );

        foreach ( array( array( 'x' ), array( '1;2' ), array( '-', '-1' ), array( '-', 'a' ), array( '-', '0', 'a+b/' ),
                         array( '-', '0', 'x' ), array( '-', '0', self::b64url( "\xff\xfe" ) ), array( '<script>' ) ) as $bad )
        {
            try
            {
                expDebugBarServerFunctions::policyArgs( $bad );
                $this->fail( 'refused: ' . implode( '::', $bad ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
    }

    /** UP-10 */
    public function testTheScriptAsksWithThePath()
    {
        $js = file_get_contents( __DIR__ . '/../../../../../design/standard/javascript/expdebugbar.js' );
        $this->assertStringContainsString( "call('policies::' + (state.role || '-') + '::' + (append ? state.offset : 0) + '::' + (state.q ? b64url(state.q) : '-'), {})", $js,
                                           'role, page and search in the path, no parameters' );
        $this->assertStringNotContainsString( "call('policies', {", $js );
        $this->assertStringContainsString( "details.addEventListener('toggle'", $js, 'asked when the section is opened' );
    }
    /** UP-12 */
    public function testAReaderByTheDebugOutputGetsNobodyElsesData()
    {
        $log = expDebugBarServerFunctions::forReader( array(
            array( 'id' => 'a', 'time' => 1, 'user_id' => 14, 'user' => 'admin', 'ip' => '192.0.2.7', 'setting' => 'debug_output', 'old' => 'disabled', 'new' => 'enabled' ),
        ) );
        $this->assertNull( $log[0]['user_id'] );
        $this->assertNull( $log[0]['user'] );
        $this->assertNull( $log[0]['ip'] );
        $this->assertSame( 'enabled', $log[0]['new'], 'what changed stays' );

        $settings = expDebugBarServerFunctions::listsMasked( array(
            array( 'id' => 'debug_ip_list', 'type' => 'iplist', 'secret' => false, 'effective' => array( '192.0.2.7', '198.51.100.0/24' ),
                   'default' => array(), 'files' => array( array( 'scope' => 'global', 'path' => 'settings/override/site.ini.append.php', 'value' => array( '192.0.2.7' ) ) ),
                   'scopes' => array( 'global' => array( '192.0.2.7' ) ), 'entries' => array( array( 'line' => '192.0.2.7' ) ) ),
            array( 'id' => 'debug_user_list', 'type' => 'userlist', 'secret' => false, 'effective' => array( '14' ) ),
            array( 'id' => 'debug_output', 'type' => 'bool', 'secret' => false, 'effective' => 'enabled' ),
        ) );
        $json = json_encode( $settings );
        $this->assertStringNotContainsString( '192.0.2.7', $json );
        $this->assertStringNotContainsString( '198.51.100', $json );
        $this->assertTrue( $settings[0]['secret'] );
        $this->assertArrayNotHasKey( 'entries', $settings[0] );
        $this->assertSame( array( expIniEditor::MASK ), $settings[1]['effective'] );
        $this->assertSame( 'enabled', $settings[2]['effective'], 'other settings unchanged' );

        $ip = expDebugBarServerFunctions::ownIpAnswer( array(
            'address' => '203.0.113.5', 'client_ip' => '203.0.113.5', 'allowed' => true,
            'matched' => array( 'index' => 1, 'line' => '203.0.113.0/24 # office' ), 'request_match' => array( 'line' => '203.0.113.0/24 # office' ),
            'entries' => array( array( 'line' => '192.0.2.7' ) ),
            'warnings' => array( array( 'code' => 'open', 'message' => "'0.0.0.0/0' opens debug" ), array( 'code' => 'expired', 'message' => '1 entry has expired' ) ),
        ) );
        $json = json_encode( $ip );
        $this->assertStringNotContainsString( 'office', $json );
        $this->assertStringNotContainsString( '192.0.2.7', $json );
        $this->assertStringNotContainsString( '0.0.0.0', $json );
        $this->assertTrue( $ip['allowed'], 'whether the own address is allowed stays' );
        $this->assertSame( '203.0.113.5', $ip['address'] );
    }
}
