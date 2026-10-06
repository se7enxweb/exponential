<?php
/**
 * File containing the expApiKeyTest class.
 *
 * The parts of the personal API keys (doc/guides/api-keys.md) that need no database: the key format, hashing and
 * the constant-time check of the secret, status and expiry, names, the scope rules (normalising, limiting to what
 * the owner may give, the Scope limitation, the scope a REST route needs), reading the Authorization header, and
 * the password check of the REST interface's basic authentication for every hash type.
 *
 *  AK-01 a new key is expk_<12 [a-z0-9]>_<40 [A-Za-z0-9]>, parses back into prefix and secret, and keys differ
 *  AK-02 only the hash is kept: HMAC-SHA-256 with the key's own salt; the secret matches, anything else does not
 *  AK-03 status: revoked wins, then expired (at the second), else active
 *  AK-04 lifetimes: allowed days, the maximum, "never" only without a maximum, junk refused
 *  AK-05 names: one line, no control characters, cut to 100 characters
 *  AK-06 scopes: unknown and repeated ones dropped, catalogue order, never more than allowed
 *  AK-07 allowed scopes follow the owner's policies and the Scope limitation
 *  AK-08 a route's scope: listed routes, GET and HEAD default to read, anything else needs a listing
 *  AK-09 the key is read from "Bearer" or "OAuth" in the Authorization header only
 *  AK-10 basic authentication checks every password hash type the way the sign-in does
 *  AK-11 the scope catalogue comes from rest.ini, malformed entries left out
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expApiKeyTest extends ezpTestCase
{
    private $catalogue = array(
        'read' => array( 'id' => 'read', 'policy' => 'content/read', 'name' => 'Read content' ),
        'publish' => array( 'id' => 'publish', 'policy' => 'content/create', 'name' => 'Create and publish content' ),
        'edit' => array( 'id' => 'edit', 'policy' => 'content/edit', 'name' => 'Edit content' ),
        'remove' => array( 'id' => 'remove', 'policy' => 'content/remove', 'name' => 'Remove content' ),
    );

    /** AK-01 */
    public function testNewKeyFormatAndParsing()
    {
        $seen = array();
        for ( $i = 0; $i < 200; $i++ )
        {
            $new = expApiKey::newToken();
            $this->assertMatchesRegularExpression( '/^expk_[a-z0-9]{12}_[A-Za-z0-9]{40}$/', $new['token'] );
            $this->assertSame( $new['prefix'] . '_' . $new['secret'], $new['token'] );
            $this->assertSame( array( $new['prefix'], $new['secret'] ), expApiKey::parseToken( $new['token'] ) );
            $this->assertTrue( expApiKey::looksLikeKey( $new['token'] ) );
            $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}$/', $new['salt'] );
            $this->assertArrayNotHasKey( $new['token'], $seen, 'every key is new' );
            $seen[$new['token']] = true;
        }
        foreach ( array( '', 'expk_', 'expk_abc_def', 'expk_ABCDEFGHIJKL_' . str_repeat( 'a', 40 ),
                         'expk_abcdefghijkl_' . str_repeat( 'a', 39 ), 'expk_abcdefghijkl_' . str_repeat( 'a', 41 ),
                         'expk_abcdefghijkl_' . str_repeat( 'a', 39 ) . '-', ' expk_abcdefghijkl_' . str_repeat( 'a', 40 ),
                         "expk_abcdefghijkl_" . str_repeat( 'a', 40 ) . "\n", 'OAuth 1234', null, array(), 42 ) as $bad )
            $this->assertNull( expApiKey::parseToken( $bad ), var_export( $bad, true ) );
        $this->assertFalse( expApiKey::looksLikeKey( '0123456789abcdef' ), 'an OAuth token is not a key' );
        $this->assertFalse( expApiKey::looksLikeKey( null ) );
    }

    /** AK-01 */
    public function testRandomStringUsesOnlyItsAlphabet()
    {
        $s = expApiKey::randomString( 5000, 'abc' );
        $this->assertSame( 5000, strlen( $s ) );
        $this->assertSame( '', trim( $s, 'abc' ) );
        $counts = count_chars( $s, 1 );
        foreach ( $counts as $n )
            $this->assertGreaterThan( 1400, $n, 'each character is about as likely as the others' );
    }

    /** AK-02 */
    public function testOnlyTheHashIsKeptAndTheSecretMatches()
    {
        $new = expApiKey::newToken();
        $this->assertSame( hash_hmac( 'sha256', $new['secret'], $new['salt'] ), $new['secret_hash'] );
        $this->assertStringNotContainsString( $new['secret'], $new['secret_hash'] );
        $this->assertTrue( expApiKey::secretMatches( $new['secret'], $new['salt'], $new['secret_hash'] ) );
        $this->assertFalse( expApiKey::secretMatches( $new['secret'] . 'x', $new['salt'], $new['secret_hash'] ) );
        $this->assertFalse( expApiKey::secretMatches( substr( $new['secret'], 1 ), $new['salt'], $new['secret_hash'] ) );
        $this->assertFalse( expApiKey::secretMatches( $new['secret'], 'another salt', $new['secret_hash'] ), 'the salt is part of the hash' );
        $this->assertFalse( expApiKey::secretMatches( $new['secret'], $new['salt'], '' ), 'an empty stored hash never matches' );
        $this->assertFalse( expApiKey::secretMatches( $new['secret'], $new['salt'], null ) );
        $this->assertFalse( expApiKey::secretMatches( $new['secret'], $new['salt'], strtoupper( $new['secret_hash'] ) ) );
        $this->assertNotSame( expApiKey::hashSecret( 's', 'a' ), expApiKey::hashSecret( 's', 'b' ) );

        $key = new expApiKey( array( 'salt' => $new['salt'], 'secret_hash' => $new['secret_hash'] ) );
        $this->assertTrue( $key->verifySecret( $new['secret'] ) );
        $this->assertFalse( $key->verifySecret( $new['token'] ), 'the whole key is not the secret' );
    }

    /** AK-03 */
    public function testStatus()
    {
        $now = 1800000000;
        $this->assertSame( 'active', expApiKey::statusFor( 0, 0, $now ) );
        $this->assertSame( 'active', expApiKey::statusFor( 0, $now + 1, $now ) );
        $this->assertSame( 'expired', expApiKey::statusFor( 0, $now, $now ), 'expired at its expiry second' );
        $this->assertSame( 'expired', expApiKey::statusFor( 0, $now - 86400, $now ) );
        $this->assertSame( 'revoked', expApiKey::statusFor( $now - 5, 0, $now ) );
        $this->assertSame( 'revoked', expApiKey::statusFor( $now - 5, $now - 86400, $now ), 'revoked wins over expired' );

        $key = new expApiKey( array( 'revoked' => 0, 'expires' => time() + 3 * 86400, 'last_used' => 0, 'scopes' => 'read publish' ) );
        $this->assertTrue( $key->isActive() );
        $this->assertTrue( $key->isExpiringSoon() );
        $this->assertTrue( $key->isNeverUsed() );
        $this->assertSame( array( 'read', 'publish' ), $key->scopeList() );
        $key->setAttribute( 'expires', time() + 30 * 86400 );
        $this->assertFalse( $key->isExpiringSoon() );
    }

    /** AK-04 */
    public function testExpiry()
    {
        $now = 1800000000;
        $this->assertSame( $now + 30 * 86400, expApiKey::expiryFor( 30, $now, 365 ) );
        $this->assertSame( $now + 365 * 86400, expApiKey::expiryFor( '365', $now, 365 ) );
        $this->assertFalse( expApiKey::expiryFor( 366, $now, 365 ), 'past the maximum' );
        $this->assertFalse( expApiKey::expiryFor( 0, $now, 365 ), 'never is not allowed with a maximum' );
        $this->assertSame( 0, expApiKey::expiryFor( 0, $now, 0 ), 'never, without a maximum' );
        $this->assertSame( $now + 5000 * 86400, expApiKey::expiryFor( 5000, $now, 0 ) );
        foreach ( array( -1, '1.5', 'x', '', null, array(), '30 ' ) as $bad )
            $this->assertFalse( expApiKey::expiryFor( $bad, $now, 365 ), var_export( $bad, true ) );
    }

    /** AK-05 */
    public function testNames()
    {
        $this->assertSame( 'Newsroom import', expApiKey::cleanName( "  Newsroom\n\timport \x00 " ) );
        $this->assertSame( 100, mb_strlen( expApiKey::cleanName( str_repeat( 'ä', 300 ) ), 'UTF-8' ) );
        $this->assertSame( '', expApiKey::cleanName( "\r\n" ) );
        $this->assertSame( '<b>x</b>', expApiKey::cleanName( '<b>x</b>' ), 'kept as text; the templates wash it' );
    }

    /** AK-06 */
    public function testScopesAreNormalisedAndLimited()
    {
        $this->assertSame( array( 'read', 'remove' ), expApiKey::normaliseScopes( 'remove read nope read', $this->catalogue ) );
        $this->assertSame( array( 'read', 'publish' ), expApiKey::normaliseScopes( array( 'publish', 'read', array( 'x' ), 7 ), $this->catalogue ) );
        $this->assertSame( array(), expApiKey::normaliseScopes( '', $this->catalogue ) );
        $this->assertSame( array( 'read' ), expApiKey::limitScopes( array( 'read', 'remove' ), array( 'read', 'publish' ) ) );
        $this->assertSame( array(), expApiKey::limitScopes( array( 'remove' ), array( 'read' ) ), 'nothing is ever added' );
        $this->assertSame( array( 'read', 'publish' ), expApiKey::limitScopes( array( 'publish', 'read', 'read' ), array( 'read', 'publish' ) ) );
    }

    /** AK-07 */
    public function testAllowedScopesFollowThePoliciesAndTheLimitation()
    {
        $member = function ( $module, $function ) { return $module === 'content' && in_array( $function, array( 'read', 'create' ), true ); };
        $this->assertSame( array( 'read', 'publish' ), expApiKey::allowedScopes( $this->catalogue, $member ) );
        $this->assertSame( array( 'read' ), expApiKey::allowedScopes( $this->catalogue, $member, array( 'read', 'remove' ) ),
                           'the limitation narrows, the policies still decide' );
        $this->assertSame( array(), expApiKey::allowedScopes( $this->catalogue, $member, array() ) );
        $nobody = function () { return false; };
        $this->assertSame( array(), expApiKey::allowedScopes( $this->catalogue, $nobody ) );
        $admin = function () { return true; };
        $this->assertSame( array( 'read', 'publish', 'edit', 'remove' ), expApiKey::allowedScopes( $this->catalogue, $admin ) );
    }

    /** AK-08 */
    public function testRouteScope()
    {
        $routes = array( 'ezp7xRestContentController_CreateContentNode' => 'publish',
                         'ezp7xRestContentController_DeleteContentNode' => 'remove' );
        $this->assertSame( 'publish', expApiKey::routeScope( 'ezp7xRestContentController', 'CreateContentNode', 'http-post', $routes, 'read' ) );
        $this->assertSame( 'remove', expApiKey::routeScope( 'ezp7xRestContentController', 'DeleteContentNode', 'http-post', $routes, 'read' ) );
        $this->assertSame( 'read', expApiKey::routeScope( 'ezp7xRestContentController', 'ViewContent', 'http-get', $routes, 'read' ) );
        $this->assertSame( 'read', expApiKey::routeScope( 'ezpRestContentController', 'ViewFields', 'http-head', $routes, 'read' ) );
        $this->assertNull( expApiKey::routeScope( 'myRestController', 'Write', 'http-post', $routes, 'read' ), 'an unlisted write route is refused' );
        $this->assertNull( expApiKey::routeScope( 'myRestController', 'Write', 'http-put', $routes, 'read' ) );
        $this->assertNull( expApiKey::routeScope( 'myRestController', 'Read', 'http-get', $routes, '' ), 'no default read scope: nothing is open' );
    }

    /** AK-09 */
    public function testKeyFromTheAuthorizationHeader()
    {
        $key = 'expk_abcdefghijkl_' . str_repeat( 'Ab1', 13 ) . 'x';
        $this->assertSame( $key, expApiKeyRest::keyFromHeader( 'Bearer ' . $key ) );
        $this->assertSame( $key, expApiKeyRest::keyFromHeader( 'bearer  ' . $key . ' ' ) );
        $this->assertSame( $key, expApiKeyRest::keyFromHeader( 'OAuth ' . $key ) );
        $this->assertNull( expApiKeyRest::keyFromHeader( 'Bearer 0123456789abcdef0123' ), 'an OAuth token stays with OAuth' );
        $this->assertNull( expApiKeyRest::keyFromHeader( 'Basic ' . base64_encode( 'a:b' ) ) );
        $this->assertNull( expApiKeyRest::keyFromHeader( 'Bearer ' . $key . ' extra' ) );
        $this->assertNull( expApiKeyRest::keyFromHeader( '' ) );
        $this->assertNull( expApiKeyRest::keyFromHeader( false ) );
    }

    /** AK-10 */
    public function testBasicAuthChecksEveryHashType()
    {
        $site = eZUser::site();
        $cases = array(
            eZUser::PASSWORD_HASH_PHP_DEFAULT => password_hash( 'Secret-1', PASSWORD_DEFAULT ),
            eZUser::PASSWORD_HASH_BCRYPT => password_hash( 'Secret-1', PASSWORD_BCRYPT ),
            eZUser::PASSWORD_HASH_MD5_USER => md5( "rest-user\nSecret-1" ),
            eZUser::PASSWORD_HASH_MD5_PASSWORD => md5( 'Secret-1' ),
            eZUser::PASSWORD_HASH_MD5_SITE => md5( "rest-user\nSecret-1\n$site" ),
        );
        foreach ( $cases as $type => $hash )
        {
            $user = new eZUser( array( 'login' => 'rest-user', 'password_hash' => $hash, 'password_hash_type' => $type ) );
            $this->assertTrue( expRestPasswordAuthFilter::passwordMatches( $user, 'Secret-1' ), "type $type: the right password" );
            $this->assertFalse( expRestPasswordAuthFilter::passwordMatches( $user, 'Secret-2' ), "type $type: a wrong password" );
            $this->assertFalse( expRestPasswordAuthFilter::passwordMatches( $user, '' ), "type $type: no password" );
        }
        $disabled = new eZUser( array( 'login' => 'rest-user', 'password_hash' => '', 'password_hash_type' => eZUser::PASSWORD_HASH_EMPTY ) );
        $this->assertFalse( expRestPasswordAuthFilter::passwordMatches( $disabled, '' ) );
        $this->assertFalse( expRestPasswordAuthFilter::passwordMatches( $disabled, 'anything' ), 'type 0 never signs in' );
        $bcrypt = new eZUser( array( 'login' => 'rest-user', 'password_hash' => password_hash( 'Secret-1', PASSWORD_DEFAULT ),
                                     'password_hash_type' => eZUser::PASSWORD_HASH_PHP_DEFAULT ) );
        $this->assertFalse( expRestPasswordAuthFilter::passwordMatches( $bcrypt, md5( "rest-user\nSecret-1" ) ),
                            'the md5 the old style sent is not the password' );
    }

    /** AK-11 */
    public function testScopeCatalogueFromTheSettings()
    {
        $ini = eZINI::instance( 'rest.ini' );
        $catalogue = expApiKey::scopeCatalogue( $ini );
        $this->assertSame( 'content/read', $catalogue['read']['policy'] );
        $this->assertSame( 'content/create', $catalogue['publish']['policy'] );
        $this->assertNotSame( '', $catalogue['read']['name'] );
        foreach ( $catalogue as $id => $scope )
            $this->assertMatchesRegularExpression( '#^[a-z0-9_]+/[a-z0-9_*]+$#i', $scope['policy'], $id );
        $this->assertSame( 'read', $ini->variable( 'ApiKeySettings', 'DefaultReadScope' ) );
    }
}
