<?php
/**
 * File containing the expSecrets class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The kernel's secret store (doc/eng-us/features/6.0/secret-store.md): one place for every password, token and API
 * key a tool needs (the data table connections, the import sources of 7x SQLI Import, the remote server of ezodf
 * ...). A tool stores the NAME of a secret, never its value, and asks for the value when it needs it:
 *
 *   $password = expSecrets::get( $connection['secret'] );          // null when there is none of that name
 *   $password = expSecrets::required( 'archive_db', 'Archive database' ); // RuntimeException naming the secret
 *   expSecrets::set( 'archive_db', $value, 'datatable', 'The archive database' );
 *   $message = expSecrets::mask( $error, array( 'archive_db' ) );  // a connection error without the value
 *
 * A name is resolved in this order:
 *
 *   env:<VARIABLE>   the environment variable of the PHP process
 *   <name>           a row of the table exp_secret, its value encrypted with XChaCha20-Poly1305 under the store's key
 *                    (expSecretKey: EXP_SECRET_KEY, else a key file in var/ made on first use)
 *   <name>           secrets.ini [SecretSettings] Secrets[<name>] (or the INI list a tool passes as $fallback)
 *
 * The administration (Setup > Secrets, module secrets) writes and removes values and shows names only; no page, log
 * line or run history ever gets a value. The name is bound into the encryption, so a value cannot be moved to another
 * name in the database.
 *
 * @since 6.0.15
 * @package kernel
 */
class expSecrets
{
    /** A secret's name: letters, digits, _ . : - (env: names an environment variable) */
    const NAME_PATTERN = '/^[A-Za-z0-9_.:-]{1,128}$/';

    /** A tool's scope: a short identifier */
    const SCOPE_PATTERN = '/^[a-z0-9_.-]{0,64}$/';

    /** The version byte at the start of every ciphertext */
    const VERSION = "\x01";

    /** @var array name => value|null, decrypted in this request */
    protected static $cache = array();

    /**
     * The value of a secret, or null when there is none of that name.
     *
     * @param string $ref
     * @param array $fallback name => value of a tool's own settings (its INI list of secrets), asked last
     * @return string|null
     */
    public static function get( $ref, array $fallback = array() )
    {
        $ref = trim( (string)$ref );
        if ( !self::validName( $ref ) )
            return null;
        if ( strpos( $ref, 'env:' ) === 0 )
        {
            $value = getenv( substr( $ref, 4 ) );
            return $value === false ? null : (string)$value;
        }
        if ( !array_key_exists( $ref, self::$cache ) )
        {
            $value = null;
            $row = self::row( $ref );
            if ( $row instanceof expSecret )
                $value = self::decryptRow( $row );
            self::$cache[$ref] = $value;
        }
        if ( self::$cache[$ref] !== null )
            return self::$cache[$ref];
        $ini = self::iniSecrets();
        if ( isset( $ini[$ref] ) && is_scalar( $ini[$ref] ) )
            return (string)$ini[$ref];
        return isset( $fallback[$ref] ) && is_scalar( $fallback[$ref] ) ? (string)$fallback[$ref] : null;
    }

    /**
     * The value of a secret a tool needs; an exception naming the secret (never a value) when it is missing.
     *
     * @param string $ref
     * @param string $what what it is for, for the message
     * @return string
     * @throws RuntimeException
     */
    public static function required( $ref, $what )
    {
        $value = self::get( $ref );
        if ( $value === null )
            throw new RuntimeException( "$what: the secret '$ref' is not set (Setup > Secrets, env:VARIABLE or secrets.ini)" );
        return $value;
    }

    /**
     * Whether a secret of that name can be read.
     *
     * @param string $ref
     * @return bool
     */
    public static function has( $ref )
    {
        return self::get( $ref ) !== null;
    }

    /**
     * Whether a text is a valid name.
     *
     * @param string $name
     * @return bool
     */
    public static function validName( $name )
    {
        return is_string( $name ) && preg_match( self::NAME_PATTERN, $name ) === 1 && $name !== 'env:';
    }

    /**
     * The secrets that can be chosen, by name (never the values): the store's rows and the settings' names.
     *
     * @param string|null $scope only the store's rows of one tool (the settings' names are always listed)
     * @return array name => array( name, source (store, settings), scope, description, readable, key_id, modified )
     */
    public static function names( $scope = null )
    {
        $names = array();
        if ( self::installed() )
        {
            foreach ( expSecret::fetchList( $scope ) as $row )
            {
                $names[$row->attribute( 'name' )] = array(
                    'name' => (string)$row->attribute( 'name' ), 'source' => 'store', 'scope' => (string)$row->attribute( 'scope' ),
                    'description' => (string)$row->attribute( 'description' ), 'readable' => self::decryptRow( $row ) !== null,
                    'key_id' => (string)$row->attribute( 'key_id' ), 'modified' => (int)$row->attribute( 'modified' ) );
            }
        }
        foreach ( array_keys( self::iniSecrets() ) as $name )
        {
            if ( self::validName( (string)$name ) && !isset( $names[$name] ) )
                $names[(string)$name] = array( 'name' => (string)$name, 'source' => 'settings', 'scope' => '', 'description' => '',
                                               'readable' => true, 'key_id' => '', 'modified' => 0 );
        }
        ksort( $names );
        return $names;
    }

    /**
     * Writes a secret into the store (a new one, or a new value of an existing one).
     *
     * @param string $name
     * @param string $value
     * @param string $scope the tool it belongs to (datatable, sqliimport, ezodf)
     * @param string|null $description null keeps the existing one
     * @return bool
     */
    public static function set( $name, $value, $scope = '', $description = null )
    {
        $name = trim( (string)$name );
        if ( !self::validName( $name ) || strpos( $name, 'env:' ) === 0 || !is_string( $value ) || $value === ''
             || !preg_match( self::SCOPE_PATTERN, (string)$scope ) || !self::installed() )
            return false;
        $key = expSecretKey::current();
        if ( $key === null || !expSecretKey::supported() )
            return false;
        $now = time();
        $userID = class_exists( 'eZUser' ) ? (int)eZUser::currentUserID() : 0;
        $row = expSecret::fetchByName( $name );
        if ( !$row instanceof expSecret )
        {
            $row = new expSecret( array( 'name' => $name, 'scope' => (string)$scope, 'created' => $now, 'creator_id' => $userID,
                                         'description' => '' ) );
        }
        if ( $description !== null )
            $row->setAttribute( 'description', mb_substr( trim( (string)$description ), 0, 255 ) );
        if ( (string)$scope !== '' )
            $row->setAttribute( 'scope', (string)$scope );
        $row->setAttribute( 'ciphertext', self::encrypt( $value, $name, $key ) );
        $row->setAttribute( 'key_id', expSecretKey::id( $key ) );
        $row->setAttribute( 'modified', $now );
        $row->setAttribute( 'modifier_id', $userID );
        $row->store();
        self::$cache[$name] = $value;
        return true;
    }

    /**
     * Changes the description of a stored secret (its value stays).
     *
     * @param string $name
     * @param string $description
     * @return bool
     */
    public static function describe( $name, $description )
    {
        $row = self::installed() ? expSecret::fetchByName( (string)$name ) : null;
        if ( !$row instanceof expSecret )
            return false;
        $row->setAttribute( 'description', mb_substr( trim( (string)$description ), 0, 255 ) );
        $row->setAttribute( 'modified', time() );
        $row->store();
        return true;
    }

    /**
     * Removes a secret from the store.
     *
     * @param string $name
     * @return bool whether it is gone
     */
    public static function remove( $name )
    {
        unset( self::$cache[(string)$name] );
        if ( !self::installed() )
            return false;
        $row = expSecret::fetchByName( (string)$name );
        if ( $row instanceof expSecret )
            $row->remove();
        return true;
    }

    /**
     * A text with the values of the given secrets replaced by their names, for a message that might quote an error.
     *
     * @param string $text
     * @param string[] $refs
     * @param string[] $values more values to hide (a password typed into a form and not stored)
     * @return string
     */
    public static function mask( $text, array $refs, array $values = array() )
    {
        $text = (string)$text;
        foreach ( $refs as $ref )
        {
            $value = self::get( (string)$ref );
            if ( $value !== null && strlen( $value ) >= 3 )
                $text = str_replace( array( $value, rawurlencode( $value ) ), '[secret ' . $ref . ']', $text );
        }
        foreach ( $values as $value )
        {
            if ( is_string( $value ) && strlen( $value ) >= 3 )
                $text = str_replace( array( $value, rawurlencode( $value ) ), '[secret]', $text );
        }
        return $text;
    }

    /**
     * Encrypts every stored secret again with the current key: after a rotation, or after EXP_SECRET_KEY was changed
     * (the old key in EXP_SECRET_KEY_PREVIOUS). A secret no key here can decrypt is left as it is and counted.
     *
     * @param string|null $key the key to encrypt with (the current one when null)
     * @return array reencrypted, current (already under the key), unreadable
     */
    public static function reencryptAll( $key = null )
    {
        $result = array( 'reencrypted' => 0, 'current' => 0, 'unreadable' => 0 );
        $key = $key ?: expSecretKey::current();
        if ( $key === null || !self::installed() )
            return $result;
        $keyID = expSecretKey::id( $key );
        foreach ( expSecret::fetchList() as $row )
        {
            if ( $row->attribute( 'key_id' ) === $keyID )
            {
                $result['current']++;
                continue;
            }
            $value = self::decryptRow( $row );
            if ( $value === null )
            {
                $result['unreadable']++;
                continue;
            }
            $row->setAttribute( 'ciphertext', self::encrypt( $value, (string)$row->attribute( 'name' ), $key ) );
            $row->setAttribute( 'key_id', $keyID );
            $row->store();
            $result['reencrypted']++;
        }
        return $result;
    }

    /**
     * Rotates a file key: a new key, every secret encrypted again with it, then the new key in place.
     *
     * @return array|false the counts of reencryptAll(), false when the key comes from the environment or failed
     */
    public static function rotate()
    {
        if ( expSecretKey::source() !== 'file' )
            return false;
        $next = expSecretKey::prepareRotation();
        if ( $next === null )
            return false;
        $db = eZDB::instance();
        $db->begin();
        $counts = self::reencryptAllWith( $next );
        $db->commit();
        if ( !expSecretKey::commitRotation() )
            return false;
        self::$cache = array();
        return $counts;
    }

    /**
     * The value of a row, or null when no key here decrypts it.
     *
     * @param expSecret $row
     * @return string|null
     */
    public static function decryptRow( expSecret $row )
    {
        $key = expSecretKey::byID( (string)$row->attribute( 'key_id' ) );
        if ( $key === null )
        {
            // a rotation that stopped between writing the rows and the key file: the .next key
            $next = self::nextKey();
            if ( $next !== null && expSecretKey::id( $next ) === (string)$row->attribute( 'key_id' ) )
                $key = $next;
        }
        if ( $key === null )
            return null;
        return self::decrypt( (string)$row->attribute( 'ciphertext' ), (string)$row->attribute( 'name' ), $key );
    }

    /**
     * Encrypts a value (pure): version byte, nonce and ciphertext, base64.
     *
     * @param string $value
     * @param string $name bound in as associated data
     * @param string $key 32 bytes
     * @return string
     */
    public static function encrypt( $value, $name, $key )
    {
        $nonce = random_bytes( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES );
        $cipher = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt( (string)$value, self::VERSION . (string)$name, $nonce, $key );
        return base64_encode( self::VERSION . $nonce . $cipher );
    }

    /**
     * Decrypts a value (pure), or null when the key, the name or the text does not fit.
     *
     * @param string $text
     * @param string $name
     * @param string $key
     * @return string|null
     */
    public static function decrypt( $text, $name, $key )
    {
        $raw = base64_decode( (string)$text, true );
        $nonceBytes = SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;
        if ( $raw === false || strlen( $raw ) < 1 + $nonceBytes + 16 || $raw[0] !== self::VERSION )
            return null;
        try
        {
            $value = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt( substr( $raw, 1 + $nonceBytes ), self::VERSION . (string)$name,
                                                                         substr( $raw, 1, $nonceBytes ), $key );
        }
        catch ( Throwable $e )
        {
            return null;
        }
        return $value === false ? null : $value;
    }

    /**
     * Whether the table exp_secret exists on the site's database.
     *
     * @param bool $fresh
     * @return bool
     */
    public static function installed( $fresh = false )
    {
        try
        {
            return expKernelTableSchema::exists( expSecret::TABLE, null, $fresh ) === true;
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Creates the table exp_secret when it is missing.
     *
     * @param string[] $messages
     * @return bool
     */
    public static function install( &$messages = array() )
    {
        $ok = expKernelTableSchema::install( array( expSecret::TABLE ), null, null, $messages );
        self::$cache = array();
        return $ok;
    }

    /** Forgets what was read in this request (tests, another process wrote). */
    public static function reset()
    {
        self::$cache = array();
        expSecretKey::reset();
    }

    /**
     * @param string $key
     * @return array
     */
    protected static function reencryptAllWith( $key )
    {
        return self::reencryptAll( $key );
    }

    /** @return string|null the key of a rotation in progress */
    protected static function nextKey()
    {
        $file = expSecretKey::file() . '.next';
        return is_file( $file ) && is_readable( $file ) ? expSecretKey::decode( (string)@file_get_contents( $file ) ) : null;
    }

    /**
     * @param string $name
     * @return expSecret|null
     */
    protected static function row( $name )
    {
        if ( strpos( $name, 'env:' ) === 0 || !self::installed() )
            return null;
        return expSecret::fetchByName( $name );
    }

    /** @return array name => value of secrets.ini [SecretSettings] Secrets[] */
    protected static function iniSecrets()
    {
        try
        {
            $ini = eZINI::instance( 'secrets.ini' );
            return $ini->hasVariable( 'SecretSettings', 'Secrets' ) ? (array)$ini->variable( 'SecretSettings', 'Secrets' ) : array();
        }
        catch ( Throwable $e )
        {
            return array();
        }
    }
}
?>
