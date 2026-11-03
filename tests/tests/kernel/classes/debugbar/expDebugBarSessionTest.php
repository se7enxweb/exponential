<?php
/**
 * The section "Session" of the debug bar's User tab (kernel/classes/debugbar/expdebugbarsession.php), loaded on demand
 * by expdebugbar::session:
 *   - US-01 the keys grouped by the active extension whose name prefixes them (the longest), the kernel and the rest
 *           first, the extensions by name, keys sorted
 *   - US-02 values of keys that look like secrets are masked, everything below them too; the masks of debugbar.ini,
 *           or the defaults when none are set; an invalid expression is taken as text
 *   - US-03 a node: type, size as stored, value; long strings cut, binary as hex, objects with their properties
 *   - US-04 nested too deep, too many children, too many nodes: summarized, never all; the keys after the budget counted
 *   - US-03 also: keys and texts that are not UTF-8 as hex, a value that cannot be serialized has no size
 *   - US-05 the frame: the session id only shortened
 *   - US-06 the tab: an overview and the sub-tabs cache profile, roles and policies, session; the session to everybody
 *           who sees the tab, loaded when its sub-tab is shown, only its size in the page; the words in the script's
 *           strings
 *
 * No session and no kernel: the session comes as an array.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDebugBarSessionTest extends PHPUnit\Framework\TestCase
{
    private function keysOf( array $group )
    {
        return array_map( function ( $n ) { return $n['key']; }, $group['entries'] );
    }

    private function find( array $answer, $key )
    {
        foreach ( $answer['groups'] as $group )
        {
            foreach ( $group['entries'] as $node )
            {
                if ( $node['key'] === $key )
                    return $node;
            }
        }
        return null;
    }

    /** US-01 */
    public function testTheKeysAreGroupedByExtension()
    {
        $answer = expDebugBarSession::forSession(
            array( 'fi_superadmin_grep_csrf' => 'x', 'eZUserLoggedInID' => 14, 'ezfindrag_stream_hits' => array( 1, 2 ),
                   'fi_message' => 'a', 'RedirectAfterLogin' => '/', 'ezfind_x' => 1 ),
            array( 'id' => '' ),
            array( 'ezfind', 'ezfindrag', 'fi_superadmin', 'fi' ) );

        $this->assertSame( array( null, 'ezfind', 'ezfindrag', 'fi', 'fi_superadmin' ),
                           array_map( function ( $g ) { return $g['extension']; }, $answer['groups'] ) );
        $this->assertSame( array( 'eZUserLoggedInID', 'RedirectAfterLogin' ), $this->keysOf( $answer['groups'][0] ), 'kernel first, sorted' );
        $this->assertSame( array( 'ezfindrag_stream_hits' ), $this->keysOf( $answer['groups'][2] ), 'the longest prefix wins' );
        $this->assertSame( array( 'fi_superadmin_grep_csrf' ), $this->keysOf( $answer['groups'][4] ) );
        $this->assertSame( 6, $answer['count'] );
        $this->assertSame( array_sum( array_map( function ( $g ) { return $g['bytes']; }, $answer['groups'] ) ), $answer['bytes'] );
        $this->assertFalse( $answer['large'] );
        $this->assertNull( expDebugBarSession::extensionOf( 'fiadfs', array( 'fi' ) ), 'a prefix needs the underscore' );
    }

    /** US-02 */
    public function testSecretsAreMasked()
    {
        $answer = expDebugBarSession::forSession(
            array( 'ezxFormToken' => 'abcdef', 'fi_superadmin_grep_csrf' => 'secret-value', 'eZUserLoggedInHash' => 'h',
                   'GeneratedPassword' => 'pw', 'expUserSessionsFormKey' => 'k', 'OAuthState' => array( 'nested' => 'n' ),
                   'eZUserLoggedInID' => 14 ),
            array( 'id' => '' ), array(), array() );

        foreach ( array( 'ezxFormToken', 'fi_superadmin_grep_csrf', 'eZUserLoggedInHash', 'GeneratedPassword', 'expUserSessionsFormKey' ) as $key )
        {
            $node = $this->find( $answer, $key );
            $this->assertTrue( $node['masked'], $key );
            $this->assertNull( $node['value'], $key . ' shows no value' );
        }
        $this->assertSame( 12, $this->find( $answer, 'fi_superadmin_grep_csrf' )['length'], 'the length is given' );
        $oauth = $this->find( $answer, 'OAuthState' );
        $this->assertTrue( $oauth['masked'] );
        $this->assertNull( $oauth['children'], 'nothing below a masked key' );
        $this->assertFalse( $this->find( $answer, 'eZUserLoggedInID' )['masked'] );
        $this->assertSame( '14', $this->find( $answer, 'eZUserLoggedInID' )['value'] );

        $nested = expDebugBarSession::forSession( array( 'Data' => array( 'api_token' => 't', 'name' => 'n' ) ), array(), array(), array() );
        $children = $this->find( $nested, 'Data' )['children'];
        $this->assertTrue( $children[0]['masked'], 'a secret below a normal key' );
        $this->assertFalse( $children[1]['masked'] );

        $this->assertSame( expDebugBarSession::DEFAULT_MASKS, expDebugBarSession::masks( array( '', ' ' ) ) );
        $this->assertSame( array( 'only' ), expDebugBarSession::masks( array( 'only' ) ) );
        $this->assertSame( array( preg_quote( 'a(b', '~' ) ), expDebugBarSession::masks( array( 'a(b' ) ), 'an invalid expression is text' );
        $this->assertTrue( expDebugBarSession::isMasked( 'xa(by', expDebugBarSession::masks( array( 'a(b' ) ) ) );
        $this->assertFalse( expDebugBarSession::isMasked( 'eZUserLoggedInID', expDebugBarSession::masks( array() ) ) );
    }

    /** US-03 */
    public function testANode()
    {
        $budget = 100;
        $long = str_repeat( 'ä', expDebugBarSession::VALUE_MAX + 5 );
        $node = expDebugBarSession::node( 'Text', $long, 0, $budget, array(), false );
        $this->assertSame( array( 'string', expDebugBarSession::VALUE_MAX + 5, true ), array( $node['type'], $node['length'], $node['cut'] ) );
        $this->assertSame( expDebugBarSession::VALUE_MAX, mb_strlen( $node['value'], 'UTF-8' ) );
        $this->assertSame( strlen( serialize( $long ) ), $node['bytes'] );

        $this->assertSame( '0xff41', expDebugBarSession::node( "\xffA", 1, 0, $budget, array(), false )['key'], 'a key that is not UTF-8 as hex' );
        $this->assertSame( 'ä', expDebugBarSession::text( 'ä' ) );
        $this->assertSame( 0, expDebugBarSession::bytes( function () {} ), 'a closure cannot be serialized' );

        $binary = expDebugBarSession::node( 'Bin', "\xff\x00\x01", 0, $budget, array(), false );
        $this->assertSame( array( 'binary', '0xff0001' ), array( $binary['type'], $binary['value'] ) );

        $this->assertSame( array( 'bool', 'true' ), array_values( array_intersect_key( expDebugBarSession::node( 'B', true, 0, $budget, array(), false ), array( 'type' => 1, 'value' => 1 ) ) ) );
        $this->assertSame( 'null', expDebugBarSession::node( 'N', null, 0, $budget, array(), false )['value'] );
        $this->assertSame( array( 'float', '1.5' ), array( expDebugBarSession::node( 'F', 1.5, 0, $budget, array(), false )['type'], expDebugBarSession::node( 'F', 1.5, 0, $budget, array(), false )['value'] ) );

        $object = new expDebugBarSessionTestObject();
        $node = expDebugBarSession::node( 'Obj', $object, 0, $budget, array(), false );
        $this->assertSame( 'object expDebugBarSessionTestObject', $node['type'] );
        $this->assertSame( array( 'visible', 'inner', 'hidden' ), array_map( function ( $c ) { return $c['key']; }, $node['children'] ), 'every property, without the markers of the cast' );
    }

    /** US-04 */
    public function testLimits()
    {
        $deep = 'leaf';
        for ( $i = 0; $i < expDebugBarSession::MAX_DEPTH + 2; $i++ )
            $deep = array( 'level' => $deep );
        $budget = 1000;
        $node = expDebugBarSession::node( 'Deep', $deep, 0, $budget, array(), false );
        for ( $i = 0; $i < expDebugBarSession::MAX_DEPTH - 1; $i++ )
            $node = $node['children'][0];
        $this->assertTrue( $node['depth_cut'] );
        $this->assertNull( $node['children'] );

        $budget = 1000;
        $wide = expDebugBarSession::node( 'Wide', range( 1, expDebugBarSession::MAX_CHILDREN + 7 ), 0, $budget, array(), false );
        $this->assertCount( expDebugBarSession::MAX_CHILDREN, $wide['children'] );
        $this->assertSame( 7, $wide['more'] );

        $many = array();
        for ( $i = 0; $i < 40; $i++ )
            $many['k' . $i] = range( 1, 150 );
        $answer = expDebugBarSession::forSession( $many, array(), array(), array() );
        $this->assertTrue( $answer['truncated'], 'more than MAX_NODES nodes' );
        $this->assertSame( 40, $answer['count'], 'every key is counted' );
        $shown = array_sum( array_map( function ( $g ) { return $g['keys']; }, $answer['groups'] ) );
        $this->assertSame( 40, $shown + $answer['left'], 'the keys after the budget are counted as left' );
        $this->assertGreaterThan( 0, $answer['left'] );

        $large = expDebugBarSession::forSession( array( 'Big' => str_repeat( 'x', expDebugBarSession::LARGE_BYTES ) ), array(), array(), array() );
        $this->assertTrue( $large['large'] );
    }

    /** US-05 */
    public function testTheSessionIdIsShortened()
    {
        $answer = expDebugBarSession::forSession( array(), array( 'id' => 'abcdef0123456789', 'started' => true ), array(), array() );
        $this->assertSame( 'abcd…', $answer['frame']['id'] );
        $this->assertSame( '', expDebugBarSession::shortId( '' ) );
        $this->assertSame( 0, $answer['count'] );
        $this->assertSame( array(), $answer['groups'] );
    }

    /** US-06 */
    public function testTheTabOffersTheSection()
    {
        $user = array( 'id' => 10, 'login' => 'anonymous', 'logged_in' => false, 'siteaccess' => 'site', 'session' => true,
                       'profile' => 'abcd1234', 'view_profile' => null, 'node_id' => 0, 'per_user' => false, 'tweak' => '',
                       'keys' => array(), 'removed' => array(), 'changed' => array(), 'view_keys' => array(), 'from_listeners' => array(),
                       'preferences' => array(), 'assignments' => array() );
        foreach ( array( false, true ) as $policies )
        {
            $html = expDebugBarReport::userHtml( $user, $policies );
            $this->assertStringContainsString( 'data-exp-debug-session', $html, 'with and without setup/setup' );
            $this->assertStringContainsString( 'exp-debug-session-body', $html );
            foreach ( array( 'profile', 'preferences', 'roles', 'session' ) as $sub )
            {
                $this->assertStringContainsString( 'data-subtab="' . $sub . '"', $html, 'sub-tab ' . $sub );
                $this->assertStringContainsString( 'id="exp-debug-user-' . $sub . '"', $html, 'its panel' );
            }
            $this->assertSame( $policies, strpos( $html, 'data-exp-debug-policies' ) !== false, 'the policies only with setup/setup' );
        }
        $user['session_keys'] = 12;
        $user['session_bytes'] = 4200;
        $this->assertStringContainsString( '4.1 KB', expDebugBarReport::userHtml( $user, false ), 'the overview names the size, not the content' );
        $strings = expDebugBarReport::scriptStrings();
        foreach ( array( 'masked', 'Kernel and others', 'Extension %name', '%count keys, %size', 'Frame', 'Session ID' ) as $word )
            $this->assertContains( $word, $strings, $word );

        $js = file_get_contents( __DIR__ . '/../../../../../design/standard/javascript/expdebugbar.js' );
        $this->assertStringContainsString( "call('session', {})", $js );
        $this->assertStringContainsString( "querySelectorAll('[data-exp-debug-session]')", $js );
    }
}

class expDebugBarSessionTestObject
{
    public $visible = 'v';
    protected $inner = 'i';
    private $hidden = 'h';
}
