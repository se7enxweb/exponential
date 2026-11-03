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
 *   - US-07 values that look like secrets are masked whatever their key is called (a JWT, bearer credentials, a
 *           private key, API key prefixes, hex, random tokens), keys that look like tokens too; words, paths, slugs
 *           and UUIDs are not; SessionValueMask[] adds patterns
 *   - US-08 "Copy as text": the masked session as text, no secret in it, the session id shortened
 *   - US-09 the warnings: larger than SessionWarnBytes, PHP's locking file handler; the overview's badge
 *   - US-10 only the requesting session: POST, not from another site, and nothing in the code can read another
 *           session (no session id, no save path, no session table)
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
        $this->assertStringContainsString( "call('session', {}, true)", $js, 'POST with the form token' );
        $this->assertStringContainsString( "querySelectorAll('[data-exp-debug-session]')", $js );
    }

    /**
     * Secrets made up at run time, so no scanner takes the test for a leaked key.
     *
     * @return string[] name => value
     */
    private function secrets()
    {
        $b64 = function ( $text ) { return rtrim( strtr( base64_encode( $text ), '+/', '-_' ), '=' ); };
        return array(
            'jwt'    => $b64( '{"alg":"HS256","typ":"JWT"}' ) . '.' . $b64( '{"sub":"1234567890","iat":1516239022}' ) . '.' . $b64( random_bytes( 32 ) ),
            'bearer' => 'Authorization: Bearer ' . 'abc' . '123DEF456ghi789JKL',
            'pem'    => "-----BEGIN RSA PRIVATE" . " KEY-----\nMIIEowIBAAKCAQEA\n-----END RSA PRIVATE KEY-----",
            'github' => 'gh' . 'p_' . str_repeat( 'Ab1Cd2Ef3', 4 ),
            'stripe' => 's' . 'k_' . 'live_' . str_repeat( 'x7Y', 8 ),
            'hex'    => bin2hex( random_bytes( 20 ) ),
            'random' => 'Zk3Qp9Lm2X' . 'v7RtB4nW8sY1' . 'cH6dJ0fG5aEuK',
        );
    }

    /** US-07 */
    public function testValuesThatLookLikeSecretsAreMaskedUnderInnocentKeys()
    {
        $secrets = $this->secrets();
        foreach ( expDebugBarSession::SECRET_VALUE_PATTERNS as $name => $pattern )
            $this->assertNotFalse( @preg_match( $pattern, '' ), 'the built-in pattern ' . $name . ' is valid' );
        $session = array( 'Data' => array(), 'Plain' => array() );
        foreach ( $secrets as $name => $value )
            $session['Data']['item' . count( $session['Data'] )] = $value;
        $plain = array( 'word' => 'Hello world, this is a perfectly ordinary sentence.',
                        'path' => '/Fit-Healthy/Recipes/Spaghetti-with-tomato-sauce-2024',
                        'slug' => 'the-quick-brown-fox-jumps-over-the-lazy-dog-2024',
                        'camel' => 'CanInstantiateClassListForUser2',
                        'uuid' => '123e4567-e89b-12d3-a456-426614174000',
                        'url' => 'https://alpha.example.com/content/view/full/2' );
        $session['Plain'] = $plain;
        // the key "user" is innocent and so are its children's names: the values give them away
        $answer = expDebugBarSession::forSession( $session, array(), array(), array() );
        $data = $this->find( $answer, 'Data' );
        $this->assertFalse( $data['masked'] );
        foreach ( $data['children'] as $child )
        {
            $this->assertTrue( $child['masked'], $child['key'] . ' is masked by its value' );
            $this->assertSame( 'value', $child['mask'] );
            $this->assertNull( $child['value'] );
        }
        foreach ( $this->find( $answer, 'Plain' )['children'] as $child )
        {
            $this->assertFalse( $child['masked'], $child['key'] . ' is not a secret' );
            $this->assertSame( $plain[$child['key']], $child['value'] );
        }

        // not one secret reaches the answer, the text included
        $json = json_encode( $answer );
        foreach ( $secrets as $name => $value )
        {
            $this->assertStringNotContainsString( $value, $json, $name );
            $this->assertStringNotContainsString( substr( $value, 8, 16 ), $json, $name . ' not even in part' );
        }

        // a token as a key: shortened, masked with everything below
        $token = $secrets['hex'];
        $answer = expDebugBarSession::forSession( array( 'Forms' => array( $token => array( 'created' => 1 ) ) ), array(), array(), array() );
        $child = $this->find( $answer, 'Forms' )['children'][0];
        $this->assertSame( substr( $token, 0, 4 ) . '…', $child['key'] );
        $this->assertTrue( $child['masked'] );
        $this->assertNull( $child['children'] );
        $this->assertStringNotContainsString( $token, json_encode( $answer ) );

        // the reasons: the key's name, a masked key above
        $answer = expDebugBarSession::forSession( array( 'api_token' => 'x', 'auth' => array( 'inner' => 'y' ) ), array(), array(), array() );
        $this->assertSame( 'key', $this->find( $answer, 'api_token' )['mask'] );
        $this->assertNull( $this->find( $answer, 'auth' )['children'] );
        $budget = 10;
        $this->assertSame( 'parent', expDebugBarSession::node( 'inner', 'y', 1, $budget, array(), true )['mask'] );
        $this->assertNull( expDebugBarSession::node( 'Name', 'Jane', 0, $budget, array(), false )['mask'] );

        // SessionValueMask[] adds patterns, case-sensitive; an invalid one is text; the built-in ones stay
        $configured = expDebugBarSession::valueMasks( array( '^tok_[a-z0-9]{8,}$', 'a(b', '' ) );
        $this->assertCount( count( expDebugBarSession::SECRET_VALUE_PATTERNS ) + 2, $configured );
        $this->assertTrue( expDebugBarSession::looksSecret( 'tok_abcdef123456', $configured ) );
        $this->assertFalse( expDebugBarSession::looksSecret( 'TOK_ABCDEF123456', $configured ), 'case-sensitive' );
        $this->assertTrue( expDebugBarSession::looksSecret( 'xxxxa(bxxxxxxx', $configured ), 'an invalid expression is text' );
        $answer = expDebugBarSession::forSession( array( 'Code' => 'tok_abcdef123456' ), array(), array(), array(),
                                                  array( 'value_masks' => array( '^tok_[a-z0-9]{8,}$' ) ) );
        $this->assertTrue( $this->find( $answer, 'Code' )['masked'] );
        $this->assertFalse( expDebugBarSession::looksSecret( 'short', $configured ) );
        $this->assertTrue( expDebugBarSession::isRandomToken( $secrets['random'] ) );
        $this->assertFalse( expDebugBarSession::isRandomToken( 'ThisIsALongCamelCaseIdentifierName2' ), 'words are not random' );

        // the settings: the patterns, the threshold
        $ini = file_get_contents( __DIR__ . '/../../../../../settings/debugbar.ini' );
        $this->assertStringContainsString( "\nSessionValueMask[]\n", $ini );
        $this->assertStringContainsString( "\nSessionWarnBytes=65536\n", $ini );
    }

    /** US-08 */
    public function testTheSessionAsText()
    {
        $secrets = $this->secrets();
        $answer = expDebugBarSession::forSession(
            array( 'eZUserLoggedInID' => 14, 'Cart' => array( 'items' => array( 3, 4 ), 'note' => 'gift' ), 'csrf' => 'abc',
                   'Profile' => array( 'id_token' => 'value', 'remote' => $secrets['jwt'] ), 'Empty' => array(), 'Flag' => true ),
            array( 'id' => 'abcdef0123456789' ), array(), array() );
        $text = $answer['text'];
        $this->assertSame( expDebugBarSession::asText( array_diff_key( $answer, array( 'text' => 1 ) ) ), $text );
        $this->assertStringStartsWith( '# session: 6 keys, ', $text );
        $this->assertStringContainsString( 'id abcd…', $text );
        $this->assertStringNotContainsString( 'abcdef0123456789', $text, 'the session id only shortened' );
        $this->assertStringContainsString( "[kernel]\n", $text );
        $this->assertStringContainsString( "eZUserLoggedInID = 14 (int)\n", $text );
        $this->assertStringContainsString( "Cart.items.0 = 3 (int)\n", $text );
        $this->assertStringContainsString( "Cart.note = \"gift\"\n", $text );
        $this->assertStringContainsString( "csrf = *** (string, 3)\n", $text );
        $this->assertStringContainsString( "Profile.id_token = *** (string, 5)\n", $text );
        $this->assertStringContainsString( "Profile.remote = *** (string, ", $text );
        $this->assertStringContainsString( "Empty = (array, empty)\n", $text );
        $this->assertStringContainsString( "Flag = true (bool)\n", $text );
        $this->assertStringNotContainsString( 'abc' . "\n", substr( $text, strpos( $text, 'csrf' ) ), 'no masked value' );
        $this->assertStringNotContainsString( $secrets['jwt'], $text );

        $js = file_get_contents( __DIR__ . '/../../../../../design/standard/javascript/expdebugbar.js' );
        $this->assertStringContainsString( 'copyText(a.text)', $js, 'the button copies the server\'s masked text' );
        $this->assertContains( 'Copy as text', expDebugBarReport::scriptStrings() );

        // every word of the session section and the sub-tabs is in the script's strings, so it can be translated
        $start = strpos( $js, 'User tab: the user\'s own session' );
        $end = strpos( $js, 'IP helpers (client side validation)' );
        $this->assertNotFalse( $start );
        $this->assertNotFalse( $end );
        preg_match_all( "~\\bt\\('((?:[^'\\\\]|\\\\.)*)'~", substr( $js, $start, $end - $start ), $m );
        $strings = expDebugBarReport::scriptStrings();
        $words = array_merge( $m[1], array( 'The key looks like a secret (debugbar.ini SessionMask[]).', 'The value looks like a token.', 'Below a masked key.' ) );
        foreach ( array_unique( $words ) as $word )
            $this->assertContains( str_replace( "\\'", "'", $word ), $strings, $word );
    }

    /** US-09 */
    public function testTheWarnings()
    {
        $session = array( 'Big' => str_repeat( 'x', 2000 ) );
        $answer = expDebugBarSession::forSession( $session, array( 'save_handler' => 'user' ), array(), array(), array( 'warn_bytes' => 1000 ) );
        $this->assertTrue( $answer['large'] );
        $this->assertSame( array( 'large' ), $answer['warnings'] );
        $this->assertSame( 1000, $answer['warn_bytes'] );
        $answer = expDebugBarSession::forSession( $session, array( 'save_handler' => 'files' ), array(), array() );
        $this->assertFalse( $answer['large'], 'LARGE_BYTES when no threshold is given' );
        $this->assertSame( expDebugBarSession::LARGE_BYTES, $answer['warn_bytes'] );
        $this->assertSame( array( 'file_lock' ), $answer['warnings'], 'PHP\'s file handler locks the session' );
        $this->assertSame( array(), expDebugBarSession::forSession( array(), array( 'save_handler' => 'files' ) )['warnings'], 'no session, no lock to warn of' );

        $user = array( 'id' => 10, 'login' => 'anonymous', 'logged_in' => false, 'siteaccess' => 'site', 'session' => true,
                       'session_keys' => 1, 'session_bytes' => 2000, 'session_warn_bytes' => 1024, 'session_large' => true,
                       'profile' => 'abcd1234', 'view_profile' => null, 'node_id' => 0, 'per_user' => false, 'tweak' => '',
                       'keys' => array(), 'removed' => array(), 'changed' => array(), 'view_keys' => array(), 'from_listeners' => array(),
                       'preferences' => array(), 'assignments' => array() );
        $html = expDebugBarReport::userHtml( $user, false );
        $this->assertMatchesRegularExpression( '~data-level="warn" title="[^"]*SessionWarnBytes[^"]*">large</span>~', $html );
        $user['session_large'] = false;
        $this->assertStringNotContainsString( 'SessionWarnBytes', expDebugBarReport::userHtml( $user, false ) );
        $this->assertStringContainsString( 'role="tabpanel" tabindex="0"', $html, 'a sub-tab\'s panel takes the focus' );
    }

    /** US-10 */
    public function testOnlyTheRequestingSessionAndOnlyFromThisSite()
    {
        $refused = function ( $loggedIn, array $server ) {
            try
            {
                expDebugBarServerFunctions::requireSessionRequest( $loggedIn, $server );
                return false;
            }
            catch ( InvalidArgumentException $e )
            {
                return true;
            }
        };
        $saved = expDebugBarServerFunctions::$trustRequest;
        expDebugBarServerFunctions::$trustRequest = null;
        try
        {
            $this->assertTrue( $refused( false, array( 'REQUEST_METHOD' => 'GET' ) ), 'GET is refused' );
            $this->assertTrue( $refused( false, array() ), 'no method is refused' );
            $this->assertTrue( $refused( false, array( 'REQUEST_METHOD' => 'POST', 'HTTP_SEC_FETCH_SITE' => 'cross-site' ) ), 'another site' );
            $this->assertTrue( $refused( false, array( 'REQUEST_METHOD' => 'POST', 'HTTP_SEC_FETCH_SITE' => 'same-site' ) ), 'another host of the site' );
            $this->assertFalse( $refused( false, array( 'REQUEST_METHOD' => 'POST', 'HTTP_SEC_FETCH_SITE' => 'same-origin' ) ) );
            $this->assertFalse( $refused( false, array( 'REQUEST_METHOD' => 'POST' ) ), 'an older browser sends no Sec-Fetch-Site' );
        }
        finally
        {
            expDebugBarServerFunctions::$trustRequest = $saved;
        }

        // nothing can name another session: no argument reaches the session, and the code never touches a session
        // id, the save path, a session file or the session table
        $method = new ReflectionMethod( 'expDebugBarSession', 'current' );
        $this->assertSame( array( 'privileged' ), array_map( function ( $p ) { return $p->getName(); }, $method->getParameters() ) );
        $root = __DIR__ . '/../../../../..';
        $class = file_get_contents( $root . '/kernel/classes/debugbar/expdebugbarsession.php' );
        foreach ( array( '~session_id\(\s*[^)\s]~', '~session_decode|session_start|session_regenerate_id|session_write_close|session_set_save_handler~',
                         '~file_get_contents|fopen|readfile|scandir|glob\(~', '~\bezsession\b~', '~eZSession::(?!hasStarted|getHandlerInstance)~',
                         '~\$_(GET|POST|REQUEST|COOKIE)~' ) as $pattern )
            $this->assertDoesNotMatchRegularExpression( $pattern, $class, $pattern );
        $functions = file_get_contents( $root . '/kernel/classes/debugbar/expdebugbarserverfunctions.php' );
        $this->assertMatchesRegularExpression( '~public static function session\( \$args \)\s*\{(?:(?!\$args).)*?return expDebugBarSession::current\( self::canReadSetup\(\) \);~s',
                                               $functions, 'the arguments are never used' );
        $this->assertStringContainsString( "header( 'Cache-Control: private, no-store, max-age=0' );", $functions );
    }
}

class expDebugBarSessionTestObject
{
    public $visible = 'v';
    protected $inner = 'i';
    private $hidden = 'h';
}
