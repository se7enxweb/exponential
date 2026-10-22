<?php
/**
 * File containing the expSecretKey class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The key of the kernel's secret store (expSecrets, doc/eng-us/features/6.0/secret-store.md):
 *
 *   - the environment variable EXP_SECRET_KEY when it is set (32 bytes, base64 or hex);
 *   - otherwise a key file, made once on first use with random_bytes(): var/secrets/exp_secret.key below the
 *     installation (secrets.ini [SecretSettings] KeyFile), mode 0600, outside every path the web server serves
 *     (var/<site>/storage, var/cache/public). Nothing has to be run on the command line.
 *
 * A backup of the database alone cannot decrypt the secrets: the key is not in it. The administration says where
 * the key is and warns about exactly this.
 *
 * Rotation (file key): a new key is written beside the old one (.next), every secret is encrypted again with it, then
 * the new key replaces the old one and the old one is kept as .previous until the next rotation. A secret is always
 * decrypted with the key its row names (key_id): the current key, else the previous one (the .previous file or
 * EXP_SECRET_KEY_PREVIOUS), so a rotation that stopped half way loses nothing. With an environment key the operator
 * sets the new EXP_SECRET_KEY and the old one as EXP_SECRET_KEY_PREVIOUS, and the administration encrypts every
 * secret again with the new key.
 *
 * @since 6.0.15
 * @package kernel
 */
class expSecretKey
{
    const ENV = 'EXP_SECRET_KEY';
    const ENV_PREVIOUS = 'EXP_SECRET_KEY_PREVIOUS';

    /** @var string|null the key file for tests */
    protected static $file = null;

    /** @var array|null the keys of this request: current, previous (binary or null) */
    protected static $keys = null;

    /**
     * Whether secrets can be encrypted here at all (the sodium extension with XChaCha20-Poly1305).
     *
     * @return bool
     */
    public static function supported()
    {
        return function_exists( 'sodium_crypto_aead_xchacha20poly1305_ietf_encrypt' );
    }

    /**
     * The current key (32 bytes), made when there is none yet.
     *
     * @param bool $create make the key file when it does not exist
     * @return string|null
     */
    public static function current( $create = true )
    {
        $keys = self::keys();
        if ( $keys['current'] === null && $create && self::source() === 'file' )
        {
            if ( self::writeFile( self::file(), random_bytes( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES ), true ) )
            {
                self::$keys = null;
                $keys = self::keys();
            }
        }
        return $keys['current'];
    }

    /**
     * The previous key, or null.
     *
     * @return string|null
     */
    public static function previous()
    {
        $keys = self::keys();
        return $keys['previous'];
    }

    /**
     * The key a row was encrypted with, by its id: the current or the previous one, or null.
     *
     * @param string $id
     * @return string|null
     */
    public static function byID( $id )
    {
        foreach ( array( self::current( false ), self::previous() ) as $key )
            if ( $key !== null && self::id( $key ) === (string)$id )
                return $key;
        return null;
    }

    /**
     * The id of a key: 16 hex characters of a hash of it (it says which key, never the key).
     *
     * @param string $key
     * @return string
     */
    public static function id( $key )
    {
        return substr( hash( 'sha256', "exp-secret-key\0" . $key ), 0, 16 );
    }

    /**
     * Where the key comes from: env or file.
     *
     * @return string
     */
    public static function source()
    {
        $env = getenv( self::ENV );
        return $env !== false && trim( (string)$env ) !== '' ? 'env' : 'file';
    }

    /**
     * What the administration shows about the key: where it is, whether it exists, its id, the previous key.
     *
     * @return array source, file, exists, readable, id, previous_id, previous_source, supported, problem
     */
    public static function info()
    {
        $source = self::source();
        $keys = self::keys();
        $file = self::file();
        $problem = '';
        if ( !self::supported() )
            $problem = 'The sodium extension of PHP is missing: secrets cannot be encrypted.';
        elseif ( $source === 'env' && $keys['current'] === null )
            $problem = 'EXP_SECRET_KEY is set but is not a key of 32 bytes (base64 or hex).';
        elseif ( $source === 'file' && is_file( $file ) && $keys['current'] === null )
            $problem = 'The key file cannot be read by this process, or it does not hold a key of 32 bytes.';
        return array(
            'source' => $source,
            'file' => $file,
            'relative_file' => self::relative( $file ),
            'exists' => $keys['current'] !== null,
            'id' => $keys['current'] !== null ? self::id( $keys['current'] ) : '',
            'previous_id' => $keys['previous'] !== null ? self::id( $keys['previous'] ) : '',
            'previous_source' => $keys['previous_source'],
            'supported' => self::supported(),
            'problem' => $problem,
        );
    }

    /**
     * Rotation, step 1 (file key only): a new key, kept beside the current one until commitRotation().
     *
     * @return string|null the new key
     */
    public static function prepareRotation()
    {
        if ( self::source() !== 'file' || self::current() === null )
            return null;
        $next = self::file() . '.next';
        $key = self::readFile( $next );
        if ( $key === null )
        {
            $key = random_bytes( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES );
            if ( !self::writeFile( $next, $key ) )
                return null;
        }
        return $key;
    }

    /**
     * Rotation, step 2: the new key becomes the current one, the current one the previous one.
     *
     * @return bool
     */
    public static function commitRotation()
    {
        $file = self::file();
        $next = $file . '.next';
        if ( self::readFile( $next ) === null )
            return false;
        $current = self::readFile( $file );
        if ( $current !== null && !self::writeFile( $file . '.previous', $current ) )
            return false;
        if ( !@rename( $next, $file ) )
            return false;
        self::$keys = null;
        return true;
    }

    /**
     * The key file (absolute).
     *
     * @return string
     */
    public static function file()
    {
        if ( self::$file !== null )
            return self::$file;
        $file = 'var/secrets/exp_secret.key';
        try
        {
            $ini = eZINI::instance( 'secrets.ini' );
            if ( $ini->hasVariable( 'SecretSettings', 'KeyFile' ) && trim( (string)$ini->variable( 'SecretSettings', 'KeyFile' ) ) !== '' )
                $file = trim( (string)$ini->variable( 'SecretSettings', 'KeyFile' ) );
        }
        catch ( Throwable $e )
        {
        }
        if ( strpos( $file, '..' ) !== false )
            $file = 'var/secrets/exp_secret.key';
        return $file[0] === '/' ? $file : eZSys::rootDir() . '/' . ltrim( $file, '/' );
    }

    /**
     * Tests: another key file (null for the setting), and forgets the keys read.
     *
     * @param string|null $file
     */
    public static function setFile( $file )
    {
        self::$file = $file === null ? null : (string)$file;
        self::$keys = null;
    }

    /** Forgets the keys read in this request. */
    public static function reset()
    {
        self::$keys = null;
    }

    /**
     * A key from a text: 32 bytes as base64 (with or without padding) or as 64 hex characters.
     *
     * @param string $text
     * @return string|null
     */
    public static function decode( $text )
    {
        $text = trim( (string)$text );
        if ( $text === '' )
            return null;
        if ( preg_match( '/^[0-9a-fA-F]{64}$/', $text ) )
            return hex2bin( $text );
        $raw = base64_decode( strtr( $text, '-_', '+/' ), true );
        return $raw !== false && strlen( $raw ) === 32 ? $raw : null;
    }

    /** @return array current, previous, previous_source */
    protected static function keys()
    {
        if ( self::$keys !== null )
            return self::$keys;
        $current = null;
        $previous = null;
        $previousSource = '';
        if ( self::source() === 'env' )
            $current = self::decode( getenv( self::ENV ) );
        else
            $current = self::readFile( self::file() );
        $envPrevious = getenv( self::ENV_PREVIOUS );
        if ( $envPrevious !== false && ( $key = self::decode( $envPrevious ) ) !== null )
        {
            $previous = $key;
            $previousSource = 'env';
        }
        elseif ( ( $key = self::readFile( self::file() . '.previous' ) ) !== null )
        {
            $previous = $key;
            $previousSource = 'file';
        }
        if ( $previous !== null && $current !== null && $previous === $current )
        {
            $previous = null;
            $previousSource = '';
        }
        return self::$keys = array( 'current' => $current, 'previous' => $previous, 'previous_source' => $previousSource );
    }

    /**
     * @param string $file
     * @return string|null
     */
    protected static function readFile( $file )
    {
        if ( !is_file( $file ) || !is_readable( $file ) )
            return null;
        return self::decode( (string)@file_get_contents( $file ) );
    }

    /**
     * Writes a key file (base64, mode 0600; owned like its directory when written as root).
     *
     * @param string $file
     * @param string $key
     * @param bool $exclusive only when the file does not exist yet
     * @return bool
     */
    protected static function writeFile( $file, $key, $exclusive = false )
    {
        $dir = dirname( $file );
        if ( !is_dir( $dir ) && !@mkdir( $dir, 0700, true ) )
            return false;
        // a web request for the directory gets nothing, whatever the server's rules
        if ( !is_file( $dir . '/.htaccess' ) )
            @file_put_contents( $dir . '/.htaccess', "Require all denied\n" );
        $temp = $file . '.' . getmypid() . '.tmp';
        $old = umask( 0077 );
        $ok = @file_put_contents( $temp, base64_encode( $key ) . "\n", LOCK_EX ) !== false;
        umask( $old );
        if ( !$ok )
            return false;
        @chmod( $temp, 0600 );
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
        {
            // written as root (Velocity, a command): the web server's user must still read it
            $owner = @fileowner( dirname( $dir ) );
            if ( $owner !== false && $owner !== 0 )
            {
                @chown( $dir, $owner );
                @chown( $temp, $owner );
                $group = @filegroup( dirname( $dir ) );
                if ( $group !== false )
                {
                    @chgrp( $dir, $group );
                    @chgrp( $temp, $group );
                }
            }
        }
        if ( $exclusive )
        {
            // the first key: two requests making it at once must end with the same key, never one each
            $linked = @link( $temp, $file );
            @unlink( $temp );
            return $linked || self::readFile( $file ) !== null;
        }
        if ( !@rename( $temp, $file ) )
        {
            @unlink( $temp );
            return false;
        }
        return true;
    }

    /**
     * @param string $file
     * @return string the path below the installation, or the absolute one
     */
    protected static function relative( $file )
    {
        $root = rtrim( eZSys::rootDir(), '/' ) . '/';
        return strpos( $file, $root ) === 0 ? substr( $file, strlen( $root ) ) : $file;
    }
}
?>
