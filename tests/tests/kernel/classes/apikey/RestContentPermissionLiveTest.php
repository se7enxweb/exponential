<?php
/**
 * The rights of the REST interface, end to end over HTTP against the installation's own web servers
 * (doc/guides/api-keys.md, "Permissions of the REST interface"; the ezprestapi routes of the ezp provider).
 *
 * A throwaway user (...@restperm.example.invalid) with a narrow throwaway role: user/login, content/read of the test's
 * own folder, content/create of folders directly below its "allowed" subfolder, content/remove inside that subfolder.
 * It calls the REST interface with an OAuth token (a throwaway REST application, authorized by the user) and with
 * personal API keys. Every step runs on each server of APIKEY_LIVE_BASES (default https://alpha.se7enx.com and
 * https://alpha.se7enx.com:8080, Apache and Velocity). Everything is removed again: the content (below the test's
 * folder), the token, the application, the keys, the role and the user. No content outside the test's folder is
 * written; node 2 is only read (and refused).
 *
 * Not part of the normal run (group network-live): it needs a live installation reachable over HTTP. Run it with
 *   php vendor/bin/phpunit --group network-live tests/tests/kernel/classes/apikey/RestContentPermissionLiveTest.php
 *
 *  RP-01 reads: v1 and v2 answer 200 inside the user's read rights, 403 outside them (content/read)
 *  RP-02 v1 lists the children with links that stay in v1 (the API the mobile app calls)
 *  RP-03 a token creates where content/create allows it (201) and removes it again with DELETE and with POST (200)
 *  RP-04 a token is refused (403) a create and a removal outside its user's rights; nothing changes
 *  RP-05 without authentication a create and a delete answer 401 (not 405)
 *  RP-06 a key without the publish scope is refused a create with 403 insufficient_scope, recorded once per server
 *  RP-07 a key with the publish scope creates within its owner's rights only (201 inside, 403 outside)
 *  RP-08 v1 is read only: a create there answers 405; a bad create answers 400
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group network-live
 */

require_once dirname( __DIR__ ) . '/contentmodel/expContentModelLiveTestCase.php';

#[\PHPUnit\Framework\Attributes\Group('network-live')]
class RestContentPermissionLiveTest extends expContentModelLiveTestCase
{
    protected static $user;
    protected static $login;
    protected static $roleID;
    protected static $bases = array();
    protected static $allowed;
    protected static $outside;
    protected static $clientID;
    protected static $clientRowID;
    protected static $token;
    protected static $startedAt;
    protected static $language;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( !function_exists( 'curl_init' ) )
            self::markTestSkipped( 'needs curl' );
        if ( !class_exists( 'expRestContentPermission' ) )
            self::markTestSkipped( 'needs expRestContentPermission (Exponential 6.0.15)' );
        static::$bases = array_filter( array_map( 'trim', explode( ',', getenv( 'APIKEY_LIVE_BASES' ) ?: 'https://alpha.se7enx.com,https://alpha.se7enx.com:8080' ) ) );
        static::$startedAt = time();
        static::$language = eZContentLanguage::topPriorityLanguage()->attribute( 'locale' );

        $suffix = substr( md5( uniqid( '', true ) ), 0, 10 );
        static::$allowed = static::folder( static::$root['node'], 'allowed ' . $suffix );
        static::$outside = static::folder( static::$root['node'], 'outside ' . $suffix );
        $allowedPath = eZContentObjectTreeNode::fetch( static::$allowed['node'] )->attribute( 'path_string' );
        $rootPath = eZContentObjectTreeNode::fetch( static::$root['node'] )->attribute( 'path_string' );

        static::$login = 'rplive' . $suffix;
        $password = 'Rp-live-' . $suffix . '-Pw9!';
        $email = 'rplive-' . $suffix . '@restperm.example.invalid';
        $group = static::createObject( 'user_group', static::$root['node'], array( 'name' => 'REST permission live test ' . $suffix ) );
        $hash = eZUser::createHash( static::$login, $password, eZUser::site(), eZUser::hashType() );
        static::$user = static::createObject( 'user', $group['node'], array(
            'first_name' => 'Rest', 'last_name' => 'Permtester',
            'user_account' => static::$login . '|' . $email . '|' . $hash . '|' . eZUser::passwordHashTypeName( eZUser::hashType() ) . '|1' ) );

        $role = eZRole::create( 'REST permission live test ' . $suffix );
        $role->store();
        static::$roleID = (int)$role->attribute( 'id' );
        $folderClassID = (int)eZContentClass::fetchByIdentifier( 'folder' )->attribute( 'id' );
        $role->appendPolicy( 'user', 'login' );
        $role->appendPolicy( 'apikey', 'create' );
        $role->appendPolicy( 'content', 'read', array( 'Subtree' => array( $rootPath ) ) );
        $role->appendPolicy( 'content', 'create', array( 'Class' => array( $folderClassID ), 'Node' => array( static::$allowed['node'] ) ) );
        $role->appendPolicy( 'content', 'remove', array( 'Subtree' => array( $allowedPath ) ) );
        $role->store();
        $role->assignToUser( static::$user['object'] );
        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( static::$user['object'] );

        // a throwaway REST application, authorized by the user, and an access token of it
        ezpRestDbConfig::registerCallbacks();
        $session = ezcPersistentSessionInstance::get();
        $client = new ezpRestClient();
        $client->name = 'REST permission live test ' . $suffix;
        $client->description = 'throwaway, removed by the test';
        $client->client_id = 'rplive' . $suffix;
        $client->client_secret = md5( uniqid( '', true ) );
        $client->endpoint_uri = '';
        $client->owner_id = (int)eZUser::currentUserID();
        $client->created = time();
        $client->updated = 0;
        $client->version = ezpRestClient::STATUS_PUBLISHED;
        $session->save( $client );
        static::$clientID = $client->client_id;
        static::$clientRowID = (int)$client->id;
        $client->authorizeFor( eZUser::fetch( static::$user['object'] ) );
        $token = new ezpRestToken();
        $token->id = ezpRestToken::generateToken( '' );
        $token->refresh_token = ezpRestToken::generateToken( '' );
        $token->client_id = static::$clientID;
        $token->user_id = (int)static::$user['object'];
        $token->expirytime = time() + 3600;
        $token->scope = '';
        $session->save( $token );
        static::$token = $token->id;
    }

    public static function tearDownAfterClass(): void
    {
        $db = eZDB::instance();
        if ( static::$clientID )
        {
            $db->query( "DELETE FROM ezprest_token WHERE client_id = '" . $db->escapeString( static::$clientID ) . "'" );
            $db->query( 'DELETE FROM ezprest_authorized_clients WHERE rest_client_id = ' . (int)static::$clientRowID );
            $db->query( "DELETE FROM ezprest_clients WHERE client_id = '" . $db->escapeString( static::$clientID ) . "'" );
        }
        if ( static::$user )
        {
            $db->query( 'DELETE FROM expapikey WHERE user_id = ' . (int)static::$user['object'] );
            eZUser::removeSessionData( static::$user['object'] );
            eZUser::purgeUserCacheByUserId( static::$user['object'] );
        }
        if ( static::$roleID && eZRole::fetch( static::$roleID ) )
            eZRole::removeRole( static::$roleID );
        parent::tearDownAfterClass();
        if ( static::$login && eZUser::fetchByName( static::$login ) )
            throw new RuntimeException( 'the REST permission test user is still there' );
    }

    // ------------------------------------------------------------------------------------------------ helpers

    /**
     * One REST request; returns array( status, headers (lower-case name => value), body, decoded JSON ).
     *
     * @param string|null $auth an OAuth token, a key ("expk_..."), or null for none
     */
    protected static function rest( $base, $method, $path, $auth, $post = null )
    {
        $url = rtrim( $base, '/' ) . eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' ) . $path;
        $headers = $auth === null ? array() : array( ( strpos( $auth, 'expk_' ) === 0 ? 'Authorization: Bearer ' : 'Authorization: OAuth ' ) . $auth );
        $ch = curl_init( $url );
        curl_setopt_array( $ch, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_SSL_VERIFYPEER => false,
                                       CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_TIMEOUT => 90, CURLOPT_FOLLOWLOCATION => false,
                                       CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => $headers,
                                       CURLOPT_USERAGENT => 'Exponential REST permission live test' ) );
        if ( $post !== null )
            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
        $raw = curl_exec( $ch );
        $size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
        $status = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
        curl_close( $ch );
        $out = array();
        foreach ( preg_split( '/\r?\n/', substr( (string)$raw, 0, $size ) ) as $line )
            if ( strpos( $line, ':' ) !== false )
            {
                list( $name, $value ) = explode( ':', $line, 2 );
                $out[strtolower( trim( $name ) )] = trim( $value );
            }
        $body = substr( (string)$raw, $size );
        return array( $status, $out, $body, json_decode( $body, true ) );
    }

    protected function create( $base, $auth, $parentNodeID, $name, $version = 2 )
    {
        return static::rest( $base, 'POST', "/ezp/v$version/content/node/create", $auth, array(
            'parentNodeID' => $parentNodeID, 'classIdentifier' => 'folder', 'languageLocale' => static::$language, 'name' => $name ) );
    }

    protected static function childNames( $nodeID )
    {
        // the web servers write these rows: a condition of its own each time, so no SQL query cache of this process answers
        $rows = eZDB::instance()->arrayQuery( 'SELECT o.name FROM ezcontentobject_tree t, ezcontentobject o WHERE o.id = t.contentobject_id'
                                              . ' AND t.parent_node_id = ' . (int)$nodeID . ' AND t.node_id > -' . mt_rand( 1, 1000000000 ) );
        return array_column( $rows, 'name' );
    }

    protected static function makeKey( array $scopes )
    {
        $made = expApiKey::create( static::$user['object'], 'REST permission live test ' . implode( ',', $scopes ), $scopes, time() + 3600, static::$user['object'] );
        return $made['token'];
    }

    protected function auditFailures( $token )
    {
        list( $prefix ) = expApiKey::parseToken( $token );
        expAudit::flush();
        $config = expAuditConfig::get();
        $count = 0;
        foreach ( array_unique( array( gmdate( 'Y-m-d', static::$startedAt ), gmdate( 'Y-m-d' ) ) ) as $day )
            foreach ( glob( $config['logDir'] . '/access-' . $day . '*.jsonl' ) ?: array() as $file )
                foreach ( file( $file, FILE_IGNORE_NEW_LINES ) as $line )
                    if ( strpos( $line, $prefix ) !== false && strpos( $line, 'access.apikey.use.failed' ) !== false )
                        $count++;
        return $count;
    }

    // ------------------------------------------------------------------------------------------------ tests

    /** RP-01, RP-02 */
    public function testReadsFollowContentRead()
    {
        foreach ( static::$bases as $base )
        {
            foreach ( array( 1, 2 ) as $v )
            {
                list( $status, , $body, $json ) = static::rest( $base, 'GET', "/ezp/v$v/content/node/" . static::$root['node'], static::$token );
                $this->assertSame( 200, $status, "$base v$v: the user reads its folder: " . substr( $body, 0, 200 ) );
                $this->assertSame( static::$root['node'], (int)$json['metadata']['nodeId'] );
                list( $status, , $body ) = static::rest( $base, 'GET', "/ezp/v$v/content/node/2", static::$token );
                $this->assertSame( 403, $status, "$base v$v: node 2 is outside its read rights: " . substr( $body, 0, 200 ) );
                list( $status ) = static::rest( $base, 'GET', "/ezp/v$v/content/node/2/list", static::$token );
                $this->assertSame( 403, $status, "$base v$v: neither is the list of node 2" );
            }
            list( $status, , $body, $json ) = static::rest( $base, 'GET', '/ezp/v1/content/node/' . static::$root['node'] . '/list/offset/0/limit/20?ResponseGroups=Fields', static::$token );
            $this->assertSame( 200, $status, "$base: the v1 list of the mobile app: " . substr( $body, 0, 200 ) );
            $this->assertNotEmpty( $json['childrenNodes'] );
            foreach ( $json['childrenNodes'] as $child )
                $this->assertStringContainsString( '/api/ezp/v1/content/node/' . $child['nodeId'], $child['link'], "$base: links stay in v1" );
            list( $status ) = static::rest( $base, 'GET', '/ezp/v2/content/node/' . static::$root['node'], null );
            $this->assertSame( 401, $status, "$base: no authentication" );
        }
    }

    /** RP-03, RP-04, RP-05, RP-08 */
    public function testWritesWithAToken()
    {
        foreach ( static::$bases as $i => $base )
        {
            foreach ( array( 'DELETE', 'POST' ) as $removeWith )
            {
                $name = "rp created $i $removeWith";
                list( $status, , $body, $json ) = $this->create( $base, static::$token, static::$allowed['node'], $name );
                $this->assertSame( 201, $status, "$base: create inside the rights: " . substr( $body, 0, 300 ) );
                $this->assertGreaterThan( 0, (int)$json['nodeId'] );
                $this->assertContains( $name, static::childNames( static::$allowed['node'] ) );
                list( $status, , $body, $json2 ) = static::rest( $base, $removeWith, '/ezp/v2/content/node/delete/' . (int)$json['nodeId'], static::$token );
                $this->assertSame( 200, $status, "$base: remove with $removeWith: " . substr( $body, 0, 300 ) );
                $this->assertSame( (int)$json['objectId'], (int)$json2['objectId'] );
                $this->assertNotContains( $name, static::childNames( static::$allowed['node'] ) );
            }

            list( $status, , $body, $json ) = $this->create( $base, static::$token, static::$outside['node'], "rp refused $i" );
            $this->assertSame( 403, $status, "$base: create outside the rights: " . substr( $body, 0, 300 ) );
            $this->assertSame( 'access_denied', $json['error'] );
            $this->assertStringContainsString( 'content/create', $json['error_message'] );
            $this->assertNotContains( "rp refused $i", static::childNames( static::$outside['node'] ) );
            foreach ( array( 'DELETE', 'POST' ) as $method )
            {
                list( $status, , $body ) = static::rest( $base, $method, '/ezp/v2/content/node/delete/' . static::$outside['node'], static::$token );
                $this->assertSame( 403, $status, "$base: remove outside the rights with $method: " . substr( $body, 0, 300 ) );
            }
            $this->assertInstanceOf( 'eZContentObjectTreeNode', eZContentObjectTreeNode::fetch( static::$outside['node'] ), "$base: still there" );

            list( $status ) = $this->create( $base, null, static::$allowed['node'], 'rp anonymous' );
            $this->assertSame( 401, $status, "$base: create without authentication" );
            list( $status ) = static::rest( $base, 'DELETE', '/ezp/v2/content/node/delete/' . static::$outside['node'], null );
            $this->assertSame( 401, $status, "$base: delete without authentication" );

            list( $status ) = $this->create( $base, static::$token, static::$allowed['node'], 'rp v1', 1 );
            $this->assertSame( 405, $status, "$base: v1 is read only" );
            list( $status, , $body ) = static::rest( $base, 'POST', '/ezp/v2/content/node/create', static::$token, array( 'parentNodeID' => static::$allowed['node'], 'languageLocale' => static::$language ) );
            $this->assertSame( 400, $status, "$base: no classIdentifier: " . substr( $body, 0, 200 ) );
        }
    }

    /** RP-06, RP-07 */
    public function testWritesWithKeys()
    {
        $reader = static::makeKey( array( 'read' ) );
        $publisher = static::makeKey( array( 'read', 'publish', 'remove' ) );
        foreach ( static::$bases as $i => $base )
        {
            list( $status, , $body ) = $this->create( $base, $reader, static::$allowed['node'], "rp key reader $i" );
            $this->assertSame( 403, $status, "$base: a read key cannot create: " . substr( $body, 0, 200 ) );
            $this->assertStringContainsString( 'insufficient_scope', $body );

            list( $status, , $body, $json ) = $this->create( $base, $publisher, static::$allowed['node'], "rp key $i" );
            $this->assertSame( 201, $status, "$base: a publish key creates within its owner's rights: " . substr( $body, 0, 300 ) );
            list( $status ) = static::rest( $base, 'DELETE', '/ezp/v2/content/node/delete/' . (int)$json['nodeId'], $publisher );
            $this->assertSame( 200, $status, "$base: and removes it" );

            list( $status, , $body ) = $this->create( $base, $publisher, static::$outside['node'], "rp key outside $i" );
            $this->assertSame( 403, $status, "$base: not outside them: " . substr( $body, 0, 200 ) );
        }
        $this->assertSame( count( static::$bases ), $this->auditFailures( $reader ), 'one scope refusal recorded per server' );
    }
}
