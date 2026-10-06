<?php
/**
 * File containing the expApiKey class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A personal API key (publishing key) of a user: one row of expapikey (doc/guides/api-keys.md).
 *
 * A key is the string expk_<id>_<secret>. <id> is twelve random characters [a-z0-9] that identify the key in
 * lists, logs and secret scanners and are stored as they are (key_prefix = "expk_<id>"). <secret> is forty random
 * characters [A-Za-z0-9] (about 238 bits) that are shown to the owner once, when the key is made, and never stored:
 * the row keeps only HMAC-SHA-256( secret, salt ), with a salt of its own per key. A presented key is looked up by its
 * prefix and its secret compared with hash_equals(), so the comparison takes the same time whatever it finds.
 *
 * A key acts as its owner: the REST layer signs the request in as that user, so the owner's roles and policies still
 * decide what the request may do. On top of that a key carries scopes (rest.ini [ApiKeySettings] Scopes[]), and
 * each scope needs a policy of the owner (content/read for read, ...), checked when the key is made and again on
 * every use. A key can therefore never do more than its owner, and usually less.
 *
 * A key ends when it is revoked (by its owner or an administrator), when it expires, when its owner is disabled
 * or removed, or when the owner loses the policy a scope needs. last_used and last_ip are written at most once per
 * [ApiKeySettings] LastUsedInterval seconds per key, so a busy key does not write the database on every request.
 *
 * Everything that needs no database (token format, hashing, verification of the secret, status, expiry, scope
 * selection) is static and takes its settings as arguments, so it is unit tested without an installation.
 */
class expApiKey extends eZPersistentObject
{
    /** The fixed start of every key; a secret scanner can look for it. */
    const TOKEN_PREFIX = 'expk';

    /** Length of the public id after expk_. */
    const ID_LENGTH = 12;

    /** Length of the secret part. */
    const SECRET_LENGTH = 40;

    /** Names are cut to this length (the column is varchar(255)). */
    const MAX_NAME_LENGTH = 100;

    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REVOKED = 'revoked';

    /** Seconds before expiry from which a key counts as "expiring soon" (seven days). */
    const EXPIRING_SOON = 604800;

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $definition = array(
                'fields' => array(
                    'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true,
                                        'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id',
                                        'multiplicity' => '1..*' ),
                    'name' => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'key_prefix' => array( 'name' => 'KeyPrefix', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'secret_hash' => array( 'name' => 'SecretHash', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'salt' => array( 'name' => 'Salt', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'scopes' => array( 'name' => 'Scopes', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'created_by' => array( 'name' => 'CreatedBy', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'expires' => array( 'name' => 'Expires', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'last_used' => array( 'name' => 'LastUsed', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'last_ip' => array( 'name' => 'LastIP', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'revoked' => array( 'name' => 'Revoked', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'revoked_by' => array( 'name' => 'RevokedBy', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array(
                    'status' => 'status',
                    'scope_list' => 'scopeList',
                    'owner' => 'owner',
                    'owner_name' => 'ownerName',
                    'owner_login' => 'ownerLogin',
                    'revoker_name' => 'revokerName',
                    'is_active' => 'isActive',
                    'expiring_soon' => 'isExpiringSoon',
                    'never_used' => 'isNeverUsed',
                ),
                'sort' => array( 'created' => 'desc', 'id' => 'desc' ),
                'class_name' => 'expApiKey',
                'name' => 'expapikey',
            );
        }
        return $definition;
    }

    // ------------------------------------------------------------------------------------------------------------
    // Token format, hashing and verification (no database)
    // ------------------------------------------------------------------------------------------------------------

    /**
     * Random characters from an alphabet, without modulo bias (random_bytes, rejection sampling).
     *
     * @param int $length
     * @param string $alphabet
     * @return string
     */
    public static function randomString( $length, $alphabet )
    {
        $size = strlen( $alphabet );
        // the largest multiple of $size below 256: bytes above it are thrown away, so every character is as likely
        $limit = 256 - ( 256 % $size );
        $out = '';
        while ( strlen( $out ) < $length )
        {
            foreach ( str_split( random_bytes( $length * 2 ) ) as $byte )
            {
                $value = ord( $byte );
                if ( $value >= $limit )
                    continue;
                $out .= $alphabet[$value % $size];
                if ( strlen( $out ) === $length )
                    break;
            }
        }
        return $out;
    }

    /**
     * A new key: the full token (shown once) and what is stored of it.
     *
     * @return array token, prefix, secret, salt, secret_hash
     */
    public static function newToken()
    {
        $id = self::randomString( self::ID_LENGTH, 'abcdefghijklmnopqrstuvwxyz0123456789' );
        $secret = self::randomString( self::SECRET_LENGTH, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' );
        $salt = bin2hex( random_bytes( 16 ) );
        $prefix = self::TOKEN_PREFIX . '_' . $id;
        return array( 'token' => $prefix . '_' . $secret,
                      'prefix' => $prefix,
                      'secret' => $secret,
                      'salt' => $salt,
                      'secret_hash' => self::hashSecret( $secret, $salt ) );
    }

    /**
     * Whether a string has the shape of a key at all (cheap; says nothing about whether it is valid).
     *
     * @param mixed $token
     * @return bool
     */
    public static function looksLikeKey( $token )
    {
        return is_string( $token ) && strncmp( $token, self::TOKEN_PREFIX . '_', strlen( self::TOKEN_PREFIX ) + 1 ) === 0;
    }

    /**
     * Splits a key into its stored prefix and its secret.
     *
     * @param mixed $token
     * @return array|null array( prefix, secret ), or null when the string is not a well formed key
     */
    public static function parseToken( $token )
    {
        if ( !is_string( $token ) )
            return null;
        $pattern = '/^(' . self::TOKEN_PREFIX . '_[a-z0-9]{' . self::ID_LENGTH . '})_([A-Za-z0-9]{' . self::SECRET_LENGTH . '})$/D';
        if ( !preg_match( $pattern, $token, $m ) )
            return null;
        return array( $m[1], $m[2] );
    }

    /**
     * The stored form of a secret: HMAC-SHA-256 keyed with the key's own salt, as 64 hex characters.
     *
     * A fast hash is right here (unlike for passwords): the secret is 238 random bits, so there is nothing to guess
     * from the hash, and the check runs on every API request.
     *
     * @param string $secret
     * @param string $salt
     * @return string
     */
    public static function hashSecret( $secret, $salt )
    {
        return hash_hmac( 'sha256', (string)$secret, (string)$salt );
    }

    /**
     * Compares a presented secret with a stored hash in constant time.
     *
     * @param string $secret
     * @param string $salt
     * @param string $storedHash
     * @return bool
     */
    public static function secretMatches( $secret, $salt, $storedHash )
    {
        if ( !is_string( $storedHash ) || strlen( $storedHash ) !== 64 )
        {
            // still compute, so an unusable row costs the same time as a usable one
            hash_equals( str_repeat( '0', 64 ), self::hashSecret( $secret, $salt ) );
            return false;
        }
        return hash_equals( $storedHash, self::hashSecret( $secret, $salt ) );
    }

    /**
     * The status of a key from its columns at a moment.
     *
     * @param int $revoked
     * @param int $expires
     * @param int $now
     * @return string active, expired or revoked
     */
    public static function statusFor( $revoked, $expires, $now )
    {
        if ( (int)$revoked > 0 )
            return self::STATUS_REVOKED;
        if ( (int)$expires > 0 && (int)$expires <= (int)$now )
            return self::STATUS_EXPIRED;
        return self::STATUS_ACTIVE;
    }

    /**
     * The expiry timestamp for a lifetime chosen in days, held to the configured maximum.
     *
     * @param mixed $days the chosen lifetime; 0 (or '') = does not expire
     * @param int $now
     * @param int $maxDays the longest lifetime allowed; 0 = no limit (then "never" is allowed)
     * @return int|false the timestamp (0 = never), or false when the choice is not allowed
     */
    public static function expiryFor( $days, $now, $maxDays )
    {
        if ( !( is_int( $days ) && $days >= 0 ) && !( is_string( $days ) && ctype_digit( $days ) ) )
            return false;
        $days = (int)$days;
        $maxDays = (int)$maxDays;
        if ( $days === 0 )
            return $maxDays > 0 ? false : 0;
        if ( $maxDays > 0 && $days > $maxDays )
            return false;
        return (int)$now + $days * 86400;
    }

    /**
     * Cleans a key name: one line, no control characters, at most MAX_NAME_LENGTH characters.
     *
     * @param mixed $name
     * @return string
     */
    public static function cleanName( $name )
    {
        $name = preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', (string)$name );
        $name = trim( preg_replace( '/\s+/u', ' ', (string)$name ) );
        if ( function_exists( 'mb_substr' ) )
            return mb_substr( $name, 0, self::MAX_NAME_LENGTH, 'UTF-8' );
        return substr( $name, 0, self::MAX_NAME_LENGTH );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Scopes (no database: the catalogue comes in as an argument)
    // ------------------------------------------------------------------------------------------------------------

    /**
     * The scope catalogue from rest.ini [ApiKeySettings]: id => array( policy => module/function, name => label ).
     *
     * @param eZINI|null $ini
     * @return array
     */
    public static function scopeCatalogue( $ini = null )
    {
        if ( $ini === null )
            $ini = eZINI::instance( 'rest.ini' );
        $scopes = $ini->hasVariable( 'ApiKeySettings', 'Scopes' ) ? (array)$ini->variable( 'ApiKeySettings', 'Scopes' ) : array();
        $names = $ini->hasVariable( 'ApiKeySettings', 'ScopeNames' ) ? (array)$ini->variable( 'ApiKeySettings', 'ScopeNames' ) : array();
        $catalogue = array();
        foreach ( $scopes as $id => $policy )
        {
            if ( !is_string( $id ) || !preg_match( '/^[a-z][a-z0-9_]*$/', $id ) || !preg_match( '#^[a-z0-9_]+/[a-z0-9_*]+$#i', (string)$policy ) )
                continue;
            $catalogue[$id] = array( 'id' => $id,
                                     'policy' => (string)$policy,
                                     'name' => isset( $names[$id] ) && $names[$id] !== '' ? (string)$names[$id] : $id );
        }
        return $catalogue;
    }

    /**
     * Turns a stored or posted scope list into known scope ids, in catalogue order, without repeats.
     *
     * @param mixed $scopes a space or comma separated string, or an array
     * @param array $catalogue
     * @return string[]
     */
    public static function normaliseScopes( $scopes, array $catalogue )
    {
        if ( !is_array( $scopes ) )
            $scopes = preg_split( '/[\s,]+/', (string)$scopes, -1, PREG_SPLIT_NO_EMPTY );
        $wanted = array();
        foreach ( $scopes as $scope )
            if ( is_string( $scope ) )
                $wanted[$scope] = true;
        $out = array();
        foreach ( array_keys( $catalogue ) as $id )
            if ( isset( $wanted[$id] ) )
                $out[] = $id;
        return $out;
    }

    /**
     * The scopes a request may have: those asked for that are also allowed. Unknown and not allowed scopes are
     * dropped, never added.
     *
     * @param string[] $requested
     * @param string[] $allowed
     * @return string[] in the order of $allowed
     */
    public static function limitScopes( array $requested, array $allowed )
    {
        return array_values( array_intersect( $allowed, $requested ) );
    }

    /**
     * Which scopes a holder of policies may give a key: a scope is allowed when its policy is granted (accessWord
     * yes or limited) and, when the apikey/create policy carries a Scope limitation, when the limitation names it.
     *
     * @param array $catalogue
     * @param callable $hasPolicy function( $module, $function ) returning bool
     * @param string[]|null $limitation the Scope limitation values of apikey/create, or null for none
     * @return string[]
     */
    public static function allowedScopes( array $catalogue, $hasPolicy, $limitation = null )
    {
        $allowed = array();
        foreach ( $catalogue as $id => $scope )
        {
            if ( is_array( $limitation ) && !in_array( $id, $limitation, true ) )
                continue;
            list( $module, $function ) = explode( '/', $scope['policy'], 2 );
            if ( call_user_func( $hasPolicy, $module, $function ) )
                $allowed[] = $id;
        }
        return $allowed;
    }

    /**
     * The scope a REST route needs: the [ApiKeySettings] RouteScopes[] entry of controller_action, else
     * DefaultScope for a GET or HEAD, else none (refused).
     *
     * @param string $controller the controller class of the route
     * @param string $action the route action
     * @param string $protocol the request protocol (http-get, http-post, ...)
     * @param array $routeScopes controller_action => scope
     * @param string $defaultReadScope
     * @return string|null
     */
    public static function routeScope( $controller, $action, $protocol, array $routeScopes, $defaultReadScope )
    {
        $key = $controller . '_' . $action;
        if ( isset( $routeScopes[$key] ) && $routeScopes[$key] !== '' )
            return (string)$routeScopes[$key];
        if ( in_array( strtolower( (string)$protocol ), array( 'http-get', 'http-head', 'get', 'head' ), true ) && $defaultReadScope !== '' )
            return (string)$defaultReadScope;
        return null;
    }

    /**
     * Whether a user may make keys: signed in, keys switched on, and apikey/create granted.
     *
     * @param eZUser $user
     * @return bool
     */
    public static function userCanCreate( eZUser $user )
    {
        if ( !self::enabled() || !$user->isRegistered() )
            return false;
        $access = $user->hasAccessTo( 'apikey', 'create' );
        return $access['accessWord'] !== 'no';
    }

    /**
     * The scopes a user may give a key now: the policies of the user for each scope, narrowed by the Scope
     * limitation of the user's apikey/create policies (a policy without it allows every scope).
     *
     * @param eZUser $user
     * @param array|null $catalogue
     * @return string[]
     */
    public static function scopesForUser( eZUser $user, $catalogue = null )
    {
        if ( $catalogue === null )
            $catalogue = self::scopeCatalogue();
        $access = $user->hasAccessTo( 'apikey', 'create' );
        if ( $access['accessWord'] === 'no' )
            return array();
        $limitation = null;
        if ( $access['accessWord'] === 'limited' )
        {
            $limitation = array();
            foreach ( (array)$access['policies'] as $policy )
            {
                if ( !isset( $policy['Scope'] ) )
                {
                    // one policy without the limitation allows every scope
                    $limitation = null;
                    break;
                }
                foreach ( (array)$policy['Scope'] as $value )
                    $limitation[] = (string)$value;
            }
        }
        return self::allowedScopes( $catalogue, function ( $module, $function ) use ( $user ) {
            $result = $user->hasAccessTo( $module, $function );
            return $result['accessWord'] !== 'no';
        }, $limitation );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Settings
    // ------------------------------------------------------------------------------------------------------------

    /**
     * One [ApiKeySettings] value of rest.ini, or the default when it is not set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function setting( $name, $default = null )
    {
        $ini = eZINI::instance( 'rest.ini' );
        return $ini->hasVariable( 'ApiKeySettings', $name ) ? $ini->variable( 'ApiKeySettings', $name ) : $default;
    }

    /**
     * Whether personal API keys are switched on ([ApiKeySettings] ApiKeys=enabled).
     *
     * @return bool
     */
    public static function enabled()
    {
        return self::setting( 'ApiKeys', 'enabled' ) === 'enabled';
    }

    /**
     * The REST path the API access page uses in its examples, after the ApiPrefix: [ApiKeySettings] ExamplePath,
     * else a node read of the ezp provider in the version it serves (v2 for the ezprestapi extension's provider,
     * v1 for ezprestapiprovider's).
     *
     * @return string
     */
    public static function examplePath()
    {
        $path = trim( (string)self::setting( 'ExamplePath', '' ) );
        if ( $path !== '' )
            return '/' . ltrim( $path, '/' );
        $ini = eZINI::instance( 'rest.ini' );
        $providers = $ini->hasVariable( 'ApiProvider', 'ProviderClass' ) ? (array)$ini->variable( 'ApiProvider', 'ProviderClass' ) : array();
        $version = isset( $providers['ezp'] ) && $providers['ezp'] === 'ezp7xRestApiProvider' ? 'v2' : 'v1';
        return '/ezp/' . $version . '/content/node/2';
    }

    /**
     * The lifetimes a user may choose, in days (0 = never, offered only without MaxExpiryDays).
     *
     * @return int[]
     */
    public static function expiryChoices()
    {
        $max = (int)self::setting( 'MaxExpiryDays', 365 );
        $out = array();
        foreach ( (array)self::setting( 'ExpiryChoices', array( 30, 90, 365 ) ) as $days )
        {
            if ( !is_numeric( $days ) )
                continue;
            $days = (int)$days;
            if ( self::expiryFor( $days, 0, $max ) !== false )
                $out[] = $days;
        }
        return array_values( array_unique( $out ) );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Rows
    // ------------------------------------------------------------------------------------------------------------

    /**
     * @param int $id
     * @return expApiKey|null
     */
    public static function fetch( $id )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ), true );
    }

    /**
     * @param string $prefix expk_<id>
     * @return expApiKey|null
     */
    public static function fetchByPrefix( $prefix )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'key_prefix' => (string)$prefix ), true );
    }

    /**
     * Every key of a user, newest first.
     *
     * @param int $userID
     * @return expApiKey[]
     */
    public static function fetchListForUser( $userID )
    {
        $list = eZPersistentObject::fetchObjectList( self::definition(), null, array( 'user_id' => (int)$userID ),
                                                     array( 'created' => 'desc', 'id' => 'desc' ), null, true );
        return $list ? $list : array();
    }

    /**
     * The conditions of a filter for fetchList()/fetchListCount().
     *
     * @param array $filter user_id, status (active|expired|revoked|''), search (text), now
     * @return array array( conds, custom SQL condition or null, PHP search text or null )
     */
    protected static function filterConditions( array $filter )
    {
        $now = isset( $filter['now'] ) ? (int)$filter['now'] : time();
        $conds = array();
        $custom = array();
        if ( !empty( $filter['user_id'] ) )
            $conds['user_id'] = (int)$filter['user_id'];
        $status = isset( $filter['status'] ) ? (string)$filter['status'] : '';
        if ( $status === self::STATUS_REVOKED )
            $conds['revoked'] = array( '>', 0 );
        elseif ( $status === self::STATUS_ACTIVE )
        {
            $conds['revoked'] = 0;
            $custom[] = "( expires = 0 OR expires > $now )";
        }
        elseif ( $status === self::STATUS_EXPIRED )
        {
            $conds['revoked'] = 0;
            $conds['expires'] = array( '>', 0 );
            $custom[] = "expires <= $now";
        }
        $search = isset( $filter['search'] ) ? trim( (string)$filter['search'] ) : '';
        $phpSearch = null;
        if ( $search !== '' )
        {
            $db = eZDB::instance();
            if ( $db->databaseName() === 'mongo' )
                $phpSearch = $search;
            else
            {
                $like = $db->escapeString( '%' . str_replace( array( '%', '_' ), '', strtolower( $search ) ) . '%' );
                $custom[] = "( LOWER( name ) LIKE '$like' OR LOWER( key_prefix ) LIKE '$like' OR user_id IN "
                          . "( SELECT contentobject_id FROM ezuser WHERE LOWER( login ) LIKE '$like' OR LOWER( email ) LIKE '$like' ) )";
            }
        }
        return array( $conds, $custom ? ' AND ' . implode( ' AND ', $custom ) : null, $phpSearch );
    }

    /**
     * Keys for the administration: newest first, filtered and paged.
     *
     * @param array $filter see filterConditions()
     * @param int $offset
     * @param int $limit
     * @return expApiKey[]
     */
    public static function fetchList( array $filter, $offset = 0, $limit = 25 )
    {
        list( $conds, $custom, $phpSearch ) = self::filterConditions( $filter );
        if ( $phpSearch !== null || ( $custom !== null && eZDB::instance()->databaseName() === 'mongo' ) )
        {
            $all = self::filterInPHP( $filter, $conds );
            return array_slice( $all, (int)$offset, (int)$limit );
        }
        if ( !$conds )
            $conds = array( 'id' => array( '>', 0 ) );
        $list = eZPersistentObject::fetchObjectList( self::definition(), null, $conds, array( 'created' => 'desc', 'id' => 'desc' ),
                                                     array( 'offset' => (int)$offset, 'length' => (int)$limit ), true,
                                                     false, null, null, $custom );
        return $list ? $list : array();
    }

    /**
     * @param array $filter see filterConditions()
     * @return int
     */
    public static function fetchListCount( array $filter )
    {
        list( $conds, $custom, $phpSearch ) = self::filterConditions( $filter );
        if ( $phpSearch !== null || ( $custom !== null && eZDB::instance()->databaseName() === 'mongo' ) )
            return count( self::filterInPHP( $filter, $conds ) );
        if ( !$conds )
            $conds = array( 'id' => array( '>', 0 ) );
        $rows = eZPersistentObject::fetchObjectList( self::definition(), array(), $conds, false, null, false, false,
                                                     array( array( 'operation' => 'count( id )', 'name' => 'count' ) ),
                                                     null, $custom );
        return $rows ? (int)$rows[0]['count'] : 0;
    }

    /**
     * The filter applied in PHP, for a database without SQL (MongoDB).
     *
     * @param array $filter
     * @param array $conds the equality conditions
     * @return expApiKey[]
     */
    protected static function filterInPHP( array $filter, array $conds )
    {
        $equal = array();
        if ( isset( $conds['user_id'] ) )
            $equal['user_id'] = $conds['user_id'];
        $list = eZPersistentObject::fetchObjectList( self::definition(), null, $equal ?: null,
                                                     array( 'created' => 'desc', 'id' => 'desc' ), null, true );
        $now = isset( $filter['now'] ) ? (int)$filter['now'] : time();
        $status = isset( $filter['status'] ) ? (string)$filter['status'] : '';
        $search = isset( $filter['search'] ) ? strtolower( trim( (string)$filter['search'] ) ) : '';
        $out = array();
        foreach ( $list ?: array() as $key )
        {
            if ( $status !== '' && $key->statusAt( $now ) !== $status )
                continue;
            if ( $search !== '' )
            {
                $hay = strtolower( $key->attribute( 'name' ) . ' ' . $key->attribute( 'key_prefix' ) . ' ' . $key->ownerLogin() );
                if ( strpos( $hay, $search ) === false )
                    continue;
            }
            $out[] = $key;
        }
        return $out;
    }

    /**
     * Counts of keys by status, for the overview figures; for one user when $userID is given.
     *
     * @param int $userID
     * @param int|null $now
     * @return array active, expired, revoked, total, never_used, expiring_soon
     */
    public static function statusCounts( $userID = 0, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $counts = array( 'active' => 0, 'expired' => 0, 'revoked' => 0, 'total' => 0, 'never_used' => 0, 'expiring_soon' => 0 );
        $conds = $userID ? array( 'user_id' => (int)$userID ) : null;
        $rows = eZPersistentObject::fetchObjectList( self::definition(), array( 'revoked', 'expires', 'last_used' ), $conds,
                                                     null, null, false );
        foreach ( $rows ?: array() as $row )
        {
            $status = self::statusFor( $row['revoked'], $row['expires'], $now );
            $counts[$status]++;
            $counts['total']++;
            if ( $status === self::STATUS_ACTIVE )
            {
                if ( (int)$row['last_used'] === 0 )
                    $counts['never_used']++;
                if ( (int)$row['expires'] > 0 && (int)$row['expires'] - $now <= self::EXPIRING_SOON )
                    $counts['expiring_soon']++;
            }
        }
        return $counts;
    }

    /**
     * How many keys a user has that are not revoked or expired.
     *
     * @param int $userID
     * @return int
     */
    public static function activeCountForUser( $userID )
    {
        $counts = self::statusCounts( $userID );
        return $counts['active'];
    }

    /**
     * Makes and stores a key. The secret is in the returned token only.
     *
     * @param int $userID the owner
     * @param string $name
     * @param string[] $scopes already checked against what the owner may give
     * @param int $expires timestamp, 0 = never
     * @param int $createdBy the user who made it (the owner, or an administrator)
     * @return array key (expApiKey), token (string, the only copy of the secret)
     */
    public static function create( $userID, $name, array $scopes, $expires, $createdBy )
    {
        $new = self::newToken();
        $key = new expApiKey( array(
            'user_id' => (int)$userID,
            'name' => self::cleanName( $name ),
            'key_prefix' => $new['prefix'],
            'secret_hash' => $new['secret_hash'],
            'salt' => $new['salt'],
            'scopes' => implode( ' ', $scopes ),
            'created' => time(),
            'created_by' => (int)$createdBy,
            'expires' => (int)$expires,
            'last_used' => 0,
            'last_ip' => '',
            'revoked' => 0,
            'revoked_by' => 0,
        ) );
        $key->store();
        return array( 'key' => $key, 'token' => $new['token'] );
    }

    // ------------------------------------------------------------------------------------------------------------
    // One key
    // ------------------------------------------------------------------------------------------------------------

    /**
     * @param int|null $now
     * @return string active, expired or revoked
     */
    public function statusAt( $now = null )
    {
        return self::statusFor( $this->attribute( 'revoked' ), $this->attribute( 'expires' ), $now === null ? time() : $now );
    }

    public function status()
    {
        return $this->statusAt();
    }

    public function isActive()
    {
        return $this->statusAt() === self::STATUS_ACTIVE;
    }

    public function isExpiringSoon()
    {
        $expires = (int)$this->attribute( 'expires' );
        return $this->isActive() && $expires > 0 && $expires - time() <= self::EXPIRING_SOON;
    }

    public function isNeverUsed()
    {
        return (int)$this->attribute( 'last_used' ) === 0;
    }

    /**
     * The scopes of the key, as ids.
     *
     * @return string[]
     */
    public function scopeList()
    {
        return preg_split( '/[\s,]+/', (string)$this->attribute( 'scopes' ), -1, PREG_SPLIT_NO_EMPTY );
    }

    /**
     * @return eZUser|null
     */
    public function owner()
    {
        $user = eZUser::fetch( (int)$this->attribute( 'user_id' ) );
        return $user instanceof eZUser ? $user : null;
    }

    public function ownerName()
    {
        $object = eZContentObject::fetch( (int)$this->attribute( 'user_id' ) );
        return $object ? $object->attribute( 'name' ) : '';
    }

    public function ownerLogin()
    {
        $user = $this->owner();
        return $user ? (string)$user->attribute( 'login' ) : '';
    }

    public function revokerName()
    {
        $id = (int)$this->attribute( 'revoked_by' );
        if ( !$id )
            return '';
        $object = eZContentObject::fetch( $id );
        return $object ? $object->attribute( 'name' ) : '';
    }

    /**
     * Whether a presented secret is this key's.
     *
     * @param string $secret
     * @return bool
     */
    public function verifySecret( $secret )
    {
        return self::secretMatches( $secret, $this->attribute( 'salt' ), $this->attribute( 'secret_hash' ) );
    }

    /**
     * Revokes the key. A revoked key stays in the list (so its owner and the administrators can see what it was
     * and when it ended) and is never valid again.
     *
     * @param int $byUserID
     * @return bool false when it was already revoked
     */
    public function revoke( $byUserID )
    {
        if ( (int)$this->attribute( 'revoked' ) > 0 )
            return false;
        $this->setAttribute( 'revoked', time() );
        $this->setAttribute( 'revoked_by', (int)$byUserID );
        $this->store( array( 'revoked', 'revoked_by' ) );
        return true;
    }

    /**
     * Notes a use: last_used and last_ip, at most once per $interval seconds.
     *
     * @param string $ip
     * @param int $now
     * @param int $interval
     * @return bool true when it was written
     */
    public function touch( $ip, $now, $interval )
    {
        $last = (int)$this->attribute( 'last_used' );
        if ( $last > 0 && $now - $last < (int)$interval )
            return false;
        $this->setAttribute( 'last_used', (int)$now );
        $this->setAttribute( 'last_ip', substr( (string)$ip, 0, 64 ) );
        $this->store( array( 'last_used', 'last_ip' ) );
        return true;
    }

    /**
     * The key as the audit describes it: never the secret, its hash or its salt.
     *
     * @return array
     */
    public function auditObject()
    {
        return array( 'type' => 'apikey',
                      'id' => (int)$this->attribute( 'id' ),
                      'name' => (string)$this->attribute( 'name' ),
                      'prefix' => (string)$this->attribute( 'key_prefix' ),
                      'user_id' => (int)$this->attribute( 'user_id' ) );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Rate limit
    // ------------------------------------------------------------------------------------------------------------

    /**
     * Counts a request of a key in the current minute and says whether it is over the limit. The count lives in
     * APCu (shared by the processes of one PHP-FPM pool, or of one application server); without APCu the limit is
     * not enforced, and the administration says so.
     *
     * @param int $keyID
     * @param int $limit requests per minute; 0 = no limit
     * @param int $now
     * @return bool true when the request is over the limit
     */
    public static function overRateLimit( $keyID, $limit, $now )
    {
        $limit = (int)$limit;
        if ( $limit <= 0 || !self::rateLimitAvailable() )
            return false;
        $bucket = 'expapikey:' . substr( md5( eZSys::rootDir() ), 0, 8 ) . ':' . (int)$keyID . ':' . (int)floor( $now / 60 );
        apcu_add( $bucket, 0, 120 );
        $count = apcu_inc( $bucket );
        return $count !== false && $count > $limit;
    }

    /**
     * @return bool whether the rate limit can be counted here
     */
    public static function rateLimitAvailable()
    {
        return function_exists( 'apcu_inc' ) && function_exists( 'apcu_enabled' ) && apcu_enabled();
    }
}
?>
