<?php
/**
 * The personal API keys end to end, over HTTP, against the installation's own web servers (doc/guides/api-keys.md).
 *
 * A throwaway user (...@apikey.example.invalid) with a throwaway role (apikey/create, content/read, user/login, content/create below its own folder)
 * signs in on the public site, makes a key on apikey/list, uses it against the REST interface, has it refused where
 * it must be, and revokes it; a second key is revoked by the administrator on oauthadmin/keys. Every step is checked
 * on each server of APIKEY_LIVE_BASES (default https://alpha.se7enx.com and https://alpha.se7enx.com:8080, Apache and
 * Velocity), and the audit records it wrote are read back. Everything is removed again: keys, role, user.
 *
 * Not part of the normal run (group network-live): it needs a live installation reachable over HTTP. Run it with
 *   php vendor/bin/phpunit --group network-live tests/tests/kernel/classes/apikey/ApiKeyLiveTest.php
 * Env: APIKEY_LIVE_BASES (comma separated), APIKEY_LIVE_SITEACCESS (site), APIKEY_LIVE_ADMIN_SITEACCESS (admin),
 * APIKEY_LIVE_ADMIN_PASSWORD, APIKEY_LIVE_REST_PATH (default from expApiKey::examplePath()).
 *
 *  AL-01 the page asks a signed out visitor to sign in or register, and makes no key
 *  AL-02 a key is made on the page: shown once (no-store), stored as a hash, recorded as access.apikey.create
 *  AL-03 the key reads through REST as its owner on every server; the first use is recorded and noted (throttled)
 *  AL-04 refused: no key, a wrong secret, a key in the query string, a key whose scopes do not cover the route (403)
 *  AL-05 revoked on the page: refused on every server from then on, recorded as access.apikey.revoke
 *  AL-06 revoked by the administrator on oauthadmin/keys after the confirmation
 *  AL-07 basic authentication checks the password of a modern hash, and counts a wrong one as a failed login
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group network-live
 */

require_once dirname( __DIR__ ) . '/contentmodel/expContentModelLiveTestCase.php';

#[\PHPUnit\Framework\Attributes\Group('network-live')]
class ApiKeyLiveTest extends expContentModelLiveTestCase
{
    protected static $user;
    protected static $login;
    protected static $password;
    protected static $roleID;
    protected static $bases = array();
    protected static $siteAccess;
    protected static $restPath;
    /** @var array base => cookie file */
    protected static $cookies = array();
    protected static $token;
    protected static $startedAt;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( !function_exists( 'curl_init' ) )
            self::markTestSkipped( 'needs curl' );
        if ( !class_exists( 'expApiKeySchema' ) || expApiKeySchema::exists() !== true )
            self::markTestSkipped( 'needs the expapikey table (update/common/scripts/6.0/createapikeytable.php)' );
        static::$bases = array_filter( array_map( 'trim', explode( ',', getenv( 'APIKEY_LIVE_BASES' ) ?: 'https://alpha.se7enx.com,https://alpha.se7enx.com:8080' ) ) );
        static::$siteAccess = getenv( 'APIKEY_LIVE_SITEACCESS' ) ?: 'site';
        static::$restPath = getenv( 'APIKEY_LIVE_REST_PATH' ) ?: expApiKey::examplePath();
        static::$startedAt = time();

        $suffix = substr( md5( uniqid( '', true ) ), 0, 10 );
        static::$login = 'aklive' . $suffix;
        static::$password = 'Ak-live-' . $suffix . '-Pw9!';
        $email = 'aklive-' . $suffix . '@apikey.example.invalid';
        $group = static::createObject( 'user_group', static::$root['node'], array( 'name' => 'API key live test ' . $suffix ) );
        $hash = eZUser::createHash( static::$login, static::$password, eZUser::site(), eZUser::hashType() );
        static::$user = static::createObject( 'user', $group['node'], array(
            'first_name' => 'Api', 'last_name' => 'Keytester',
            'user_account' => static::$login . '|' . $email . '|' . $hash . '|' . eZUser::passwordHashTypeName( eZUser::hashType() ) . '|1' ) );

        $role = eZRole::create( 'API key live test ' . $suffix );
        $role->store();
        static::$roleID = (int)$role->attribute( 'id' );
        $role->appendPolicy( 'apikey', 'create' );
        $role->appendPolicy( 'content', 'read' );
        $role->appendPolicy( 'user', 'login' );
        // content/create only below the throwaway folder: enough to offer the publish scope, nothing elsewhere
        $role->appendPolicy( 'content', 'create', array( 'Node' => array( static::$root['node'] ) ) );
        $role->store();
        $role->assignToUser( static::$user['object'] );
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( static::$user['object'] );
    }

    public static function tearDownAfterClass(): void
    {
        if ( static::$user )
        {
            $db = eZDB::instance();
            $db->query( 'DELETE FROM expapikey WHERE user_id = ' . (int)static::$user['object'] );
            eZUser::removeSessionData( static::$user['object'] );
            eZUser::purgeUserCacheByUserId( static::$user['object'] );
        }
        if ( static::$roleID && eZRole::fetch( static::$roleID ) )
            eZRole::removeRole( static::$roleID );
        foreach ( static::$cookies as $file )
            @unlink( $file );
        parent::tearDownAfterClass();
        if ( static::$login && eZUser::fetchByName( static::$login ) )
            throw new RuntimeException( 'the API key test user is still there' );
    }

    // ------------------------------------------------------------------------------------------------ helpers

    /**
     * One HTTP request; returns array( status, headers (lower-case name => value), body ).
     */
    protected static function http( $base, $method, $path, $post = null, array $headers = array(), $cookie = true )
    {
        $ch = curl_init( rtrim( $base, '/' ) . $path );
        curl_setopt_array( $ch, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_SSL_VERIFYPEER => false,
                                       CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_TIMEOUT => 90, CURLOPT_FOLLOWLOCATION => false,
                                       CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => $headers,
                                       CURLOPT_USERAGENT => 'Exponential API key live test' ) );
        if ( $cookie )
        {
            // true: the jar of this server; a string: a jar of that name (a second session on the same server)
            $jar = is_string( $cookie ) ? $cookie : $base;
            if ( !isset( static::$cookies[$jar] ) )
                static::$cookies[$jar] = tempnam( dirname( __DIR__, 5 ) . '/var/tmp', 'apikey-cookies-' );
            curl_setopt( $ch, CURLOPT_COOKIEJAR, static::$cookies[$jar] );
            curl_setopt( $ch, CURLOPT_COOKIEFILE, static::$cookies[$jar] );
        }
        if ( $post !== null )
            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
        $raw = curl_exec( $ch );
        $size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
        $status = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
        curl_close( $ch );
        $head = substr( (string)$raw, 0, $size );
        $out = array();
        foreach ( preg_split( '/\r?\n/', $head ) as $line )
            if ( strpos( $line, ':' ) !== false )
            {
                list( $name, $value ) = explode( ':', $line, 2 );
                $out[strtolower( trim( $name ) )] = trim( $value );
            }
        return array( $status, $out, substr( (string)$raw, $size ) );
    }

    protected static function formToken( $body )
    {
        return preg_match( '/name="ezxform_token" value="([^"]+)"/', $body, $m ) ? $m[1] : '';
    }

    protected function signIn( $base )
    {
        $sa = '/' . static::$siteAccess;
        list( , , $page ) = static::http( $base, 'GET', $sa . '/user/login' );
        list( $status, $headers ) = static::http( $base, 'POST', $sa . '/user/login', array(
            'Login' => static::$login, 'Password' => static::$password, 'LoginButton' => 'Login',
            'RedirectURI' => '/apikey/list', 'ezxform_token' => static::formToken( $page ) ) );
        $this->assertContains( $status, array( 200, 302 ), "$base: sign in" );
    }

    protected function page( $base )
    {
        list( $status, $headers, $body ) = static::http( $base, 'GET', '/' . static::$siteAccess . '/apikey/list' );
        $this->assertSame( 200, $status, "$base: apikey/list" );
        return $body;
    }

    protected function makeKey( $base, $name, array $scopes )
    {
        $body = $this->page( $base );
        $this->assertMatchesRegularExpression( '/name="ApiKeyNonce" value="([0-9a-f]{32})"/', $body, "$base: the form is offered" );
        preg_match( '/name="ApiKeyNonce" value="([0-9a-f]{32})"/', $body, $m );
        $post = array( 'ApiKeyName' => $name, 'ApiKeyExpiry' => '30', 'ApiKeyNonce' => $m[1], 'CreateKeyButton' => '1',
                       'ezxform_token' => static::formToken( $body ) );
        foreach ( $scopes as $i => $scope )
            $post['ApiKeyScopes[' . $i . ']'] = $scope;
        list( $status, $headers, $body ) = static::http( $base, 'POST', '/' . static::$siteAccess . '/apikey/list', $post );
        $this->assertSame( 200, $status, "$base: make a key" );
        $this->assertStringContainsString( 'no-store', isset( $headers['cache-control'] ) ? $headers['cache-control'] : '', "$base: the page with the key is not kept" );
        $this->assertMatchesRegularExpression( '/id="ak-token-value">(expk_[a-z0-9]{12}_[A-Za-z0-9]{40})</', $body, "$base: the key is shown" );
        preg_match( '/id="ak-token-value">(expk_[a-z0-9]{12}_[A-Za-z0-9]{40})</', $body, $m );
        // a reload of the same POST makes no second key
        list( , , $again ) = static::http( $base, 'POST', '/' . static::$siteAccess . '/apikey/list', $post );
        $this->assertDoesNotMatchRegularExpression( '/id="ak-token-value">expk_/', $again, "$base: sending the form again makes no second key" );
        return $m[1];
    }

    protected function rest( $base, $token, $path = null, $method = 'GET', $post = null )
    {
        $headers = $token === null ? array() : array( 'Authorization: Bearer ' . $token );
        return static::http( $base, $method, eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' ) . ( $path ?: static::$restPath ), $post, $headers, false );
    }

    protected function keyRow( $token )
    {
        list( $prefix ) = expApiKey::parseToken( $token );
        // the web servers write these rows: a condition of its own each time, so no SQL query cache of this process answers
        return eZDB::instance()->arrayQuery( "SELECT * FROM expapikey WHERE key_prefix = '" . eZDB::instance()->escapeString( $prefix ) . "' AND id > -" . mt_rand( 1, 1000000000 ) );
    }

    /**
     * Audit records of a key since the test started: name => count.
     */
    protected function auditNames( $prefix )
    {
        expAudit::flush();
        $names = array();
        $config = expAuditConfig::get();
        $dir = $config['logDir'];
        foreach ( array_unique( array( gmdate( 'Y-m-d', static::$startedAt ), gmdate( 'Y-m-d' ) ) ) as $day )
            foreach ( glob( $dir . '/access-' . $day . '*.jsonl' ) ?: array() as $file )
                foreach ( file( $file, FILE_IGNORE_NEW_LINES ) as $line )
                {
                    if ( strpos( $line, $prefix ) === false )
                        continue;
                    $r = json_decode( $line, true );
                    if ( isset( $r['name'] ) )
                        $names[$r['name']] = ( isset( $names[$r['name']] ) ? $names[$r['name']] : 0 ) + 1;
                }
        return $names;
    }

    // ------------------------------------------------------------------------------------------------ tests

    /** AL-01 */
    public function testSignedOutVisitorIsAskedToSignInOrRegister()
    {
        foreach ( static::$bases as $base )
        {
            list( $status, , $body ) = static::http( $base, 'GET', '/' . static::$siteAccess . '/apikey/list', null, array(), false );
            $this->assertSame( 200, $status, $base );
            $this->assertStringContainsString( 'name="LoginButton"', $body, "$base: a sign in form" );
            $this->assertStringContainsString( '/user/register', $body, "$base: the way to register" );
            $this->assertStringNotContainsString( 'name="CreateKeyButton"', $body, "$base: no key for a visitor" );
        }
    }

    /** AL-02, AL-03, AL-04, AL-05 */
    public function testMakeUseAndRevokeAKey()
    {
        $first = static::$bases[0];
        $this->signIn( $first );
        $token = $this->makeKey( $first, 'Live test reader', array( 'read' ) );
        list( $prefix, $secret ) = expApiKey::parseToken( $token );

        $rows = $this->keyRow( $token );
        $this->assertCount( 1, $rows, 'one key, even though the form was sent twice' );
        $row = $rows[0];
        $this->assertSame( (int)static::$user['object'], (int)$row['user_id'] );
        $this->assertSame( 'read', $row['scopes'] );
        $this->assertStringNotContainsString( $secret, implode( '|', $row ), 'the secret is not stored' );
        $this->assertSame( hash_hmac( 'sha256', $secret, $row['salt'] ), $row['secret_hash'] );
        $this->assertGreaterThan( time() + 29 * 86400, (int)$row['expires'] );
        $this->assertSame( '0', (string)$row['last_used'] );

        // the page shows the key by its prefix only, never its secret
        $page = $this->page( $first );
        $this->assertStringContainsString( $prefix, $page );
        $this->assertStringNotContainsString( $secret, $page );

        foreach ( static::$bases as $base )
        {
            list( $status, , $body ) = $this->rest( $base, $token );
            $this->assertSame( 200, $status, "$base: the key reads " . static::$restPath . ': ' . substr( $body, 0, 200 ) );
            $this->assertNotNull( json_decode( $body, true ), "$base: JSON" );

            list( $status ) = $this->rest( $base, null );
            $this->assertSame( 401, $status, "$base: no key" );
            list( $status, $headers ) = $this->rest( $base, $prefix . '_' . str_repeat( 'A', 40 ) );
            $this->assertSame( 401, $status, "$base: a wrong secret" );
            $this->assertStringContainsString( 'invalid_token', isset( $headers['www-authenticate'] ) ? $headers['www-authenticate'] : '' );
            list( $status ) = static::http( $base, 'GET', eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' ) . static::$restPath . '?oauth_token=' . $token, null, array(), false );
            $this->assertSame( 401, $status, "$base: a key in the address is refused" );
        }

        // a key without the read scope cannot read: 403 insufficient_scope, even though its owner may read
        $publisher = $this->makeKey( $first, 'Live test publisher', array( 'publish' ) );
        foreach ( static::$bases as $base )
        {
            list( $status, $headers, $body ) = $this->rest( $base, $publisher );
            $this->assertSame( 403, $status, "$base: a publish key cannot read: " . substr( $body, 0, 200 ) );
            $this->assertStringContainsString( 'insufficient_scope', $body );
        }
        list( $publisherPrefix ) = expApiKey::parseToken( $publisher );
        $refusals = $this->auditNames( $publisherPrefix );
        $this->assertSame( count( static::$bases ), isset( $refusals['access.apikey.use.failed'] ) ? $refusals['access.apikey.use.failed'] : 0,
                           'one scope refusal per server' );

        $rows = $this->keyRow( $token );
        $this->assertGreaterThan( 0, (int)$rows[0]['last_used'], 'the use is noted' );
        $this->assertNotSame( '', $rows[0]['last_ip'] );

        $names = $this->auditNames( $prefix );
        $this->assertSame( 1, isset( $names['access.apikey.create'] ) ? $names['access.apikey.create'] : 0, 'made: one record' );
        $this->assertSame( 1, isset( $names['access.apikey.use'] ) ? $names['access.apikey.use'] : 0, 'the first use, once' );
        $this->assertGreaterThanOrEqual( count( static::$bases ), isset( $names['access.apikey.use.failed'] ) ? $names['access.apikey.use.failed'] : 0,
                                         'the wrong secret on every server (a key in the address is refused before it is looked up, so it carries no prefix)' );

        // revoke on the page: first the question, then the revocation
        $body = $this->page( $first );
        list( , , $confirm ) = static::http( $first, 'POST', '/' . static::$siteAccess . '/apikey/list',
                                             array( 'RevokeKeyButton' => $rows[0]['id'], 'ezxform_token' => static::formToken( $body ) ) );
        $this->assertStringContainsString( 'name="ConfirmRevokeKeyButton"', $confirm );
        list( , , $done ) = static::http( $first, 'POST', '/' . static::$siteAccess . '/apikey/list',
                                          array( 'RevokeKeyID' => $rows[0]['id'], 'ConfirmRevokeKeyButton' => '1', 'ezxform_token' => static::formToken( $confirm ) ) );
        $this->assertStringContainsString( 'is-revoked', $done );
        $rows = $this->keyRow( $token );
        $this->assertGreaterThan( 0, (int)$rows[0]['revoked'] );
        $this->assertSame( (int)static::$user['object'], (int)$rows[0]['revoked_by'] );

        foreach ( static::$bases as $base )
        {
            list( $status, $headers ) = $this->rest( $base, $token );
            $this->assertSame( 401, $status, "$base: a revoked key is refused" );
        }
        $names = $this->auditNames( $prefix );
        $this->assertSame( 1, isset( $names['access.apikey.revoke'] ) ? $names['access.apikey.revoke'] : 0 );
    }

    /** AL-06 */
    public function testAdministratorRevokesAKey()
    {
        $first = static::$bases[0];
        $this->signIn( $first );
        $token = $this->makeKey( $first, 'Live test second', array( 'read' ) );
        list( $prefix ) = expApiKey::parseToken( $token );
        $row = $this->keyRow( $token )[0];

        // the administrator, in a session of their own (cookie jar "admin")
        $sa = '/' . ( getenv( 'APIKEY_LIVE_ADMIN_SITEACCESS' ) ?: 'admin' );
        list( , , $login ) = static::http( $first, 'GET', $sa . '/user/login', null, array(), 'admin' );
        list( $status, $headers ) = static::http( $first, 'POST', $sa . '/user/login', array(
            'Login' => 'admin', 'Password' => getenv( 'APIKEY_LIVE_ADMIN_PASSWORD' ) ?: 'publishing$',
            'LoginButton' => 'Login', 'ezxform_token' => static::formToken( $login ) ), array(), 'admin' );
        $this->assertSame( 302, $status, 'the administrator signs in' );
        list( $status, , $list ) = static::http( $first, 'GET', $sa . '/oauthadmin/keys/(user)/' . static::$user['object'], null, array(), 'admin' );
        $this->assertSame( 200, $status );
        $this->assertStringContainsString( $prefix, $list, 'the per-user list shows the key' );
        list( , , $confirm ) = static::http( $first, 'POST', $sa . '/oauthadmin/keyaction', array(
            'RevokeOneKeyButton' => $row['id'], 'RedirectURI' => '/oauthadmin/keys/(user)/' . static::$user['object'],
            'ezxform_token' => static::formToken( $list ) ), array(), 'admin' );
        $this->assertStringContainsString( 'name="ConfirmRevoke"', $confirm, 'asked first' );
        $this->assertSame( 0, (int)$this->keyRow( $token )[0]['revoked'], 'not revoked before the confirmation' );
        list( $status, $headers, $body ) = static::http( $first, 'POST', $sa . '/oauthadmin/keyaction', array(
            'RevokeKeyIDArray' => array( $row['id'] ), 'ConfirmRevoke' => '1', 'RevokeKeyListButton' => '1',
            'RedirectURI' => '/oauthadmin/keys', 'ezxform_token' => static::formToken( $confirm ) ), array(), 'admin' );
        $this->assertSame( 302, $status, substr( $body, 0, 300 ) );
        $this->assertStringContainsString( '/oauthadmin/keys', isset( $headers['location'] ) ? $headers['location'] : '', 'back to the list' );
        $revoked = $this->keyRow( $token )[0];
        $this->assertGreaterThan( 0, (int)$revoked['revoked'] );
        $this->assertSame( (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' ), (int)$revoked['revoked_by'] );
        foreach ( static::$bases as $base )
        {
            list( $status ) = $this->rest( $base, $token );
            $this->assertSame( 401, $status, "$base: revoked by the administrator" );
        }
    }

    /** AL-07 */
    public function testBasicAuthenticationPasswordCheck()
    {
        $user = eZUser::fetch( static::$user['object'] );
        $this->assertInstanceOf( 'eZUser', $user );
        $filter = new expRestPasswordAuthFilter();
        $ok = $filter->run( new ezcAuthenticationPasswordCredentials( static::$login, static::$password ) );
        $this->assertSame( ezcAuthenticationFilter::STATUS_OK, $ok, 'the hash type of the installation: ' . $user->attribute( 'password_hash_type' ) );
        $this->assertSame( (int)static::$user['object'], (int)expRestPasswordAuthFilter::$user->attribute( 'contentobject_id' ) );
        $before = (int)eZUser::failedLoginAttemptsByUserID( static::$user['object'] );
        $bad = $filter->run( new ezcAuthenticationPasswordCredentials( static::$login, static::$password . 'x' ) );
        $this->assertSame( expRestPasswordAuthFilter::STATUS_INVALID_CREDENTIALS, $bad );
        $this->assertNull( expRestPasswordAuthFilter::$user );
        if ( eZUser::maxNumberOfFailedLogin() > 0 )
            $this->assertSame( $before + 1, (int)eZUser::failedLoginAttemptsByUserID( static::$user['object'] ), 'a wrong password is a failed login' );
        eZUser::setFailedLoginAttempts( static::$user['object'], 0, true );
        $this->assertSame( expRestPasswordAuthFilter::STATUS_INVALID_CREDENTIALS,
                           $filter->run( new ezcAuthenticationPasswordCredentials( 'nobody-' . uniqid(), 'x' ) ) );
    }
}
