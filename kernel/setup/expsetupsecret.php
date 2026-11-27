<?php
/**
 * File containing the expSetupSecret class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A secret the setup wizard has to carry from one page to the next (a libSQL server's or Turso's auth token) without
 * sending it back to the browser in clear text. The wizard keeps its answers in hidden fields of every page; a secret
 * goes there only sealed: encrypted and authenticated with a key that never leaves the server (var/setup/secret.key,
 * made on first use, owner only). The page's password field is always empty, the token is never echoed, and only
 * unseal() on the server gives it back, for the connection test and for the settings the installation writes.
 *
 * libsodium (XChaCha20-Poly1305) when PHP has it, else OpenSSL's AES-256-GCM; without either there is no sealing,
 * and seal() returns null (the caller asks for the token again).
 *
 * @since 6.0.15
 */
class expSetupSecret
{
    /** The key file, relative to the installation */
    const KEY_FILE = 'var/setup/secret.key';

    /**
     * The value sealed for $purpose (bound in: a value sealed for one purpose does not open for another), or null when
     * PHP can seal nothing.
     *
     * @param string $value
     * @param string $purpose
     * @param string|null $key 32 bytes; null: the installation's key
     * @return string|null
     */
    public static function seal( $value, $purpose, $key = null )
    {
        $key = $key === null ? self::key() : $key;
        if ( $key === null )
            return null;
        if ( function_exists( 'sodium_crypto_aead_xchacha20poly1305_ietf_encrypt' ) )
        {
            $nonce = random_bytes( SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES );
            return 's' . base64_encode( $nonce . sodium_crypto_aead_xchacha20poly1305_ietf_encrypt( (string)$value, (string)$purpose, $nonce, $key ) );
        }
        if ( function_exists( 'openssl_encrypt' ) )
        {
            $iv = random_bytes( 12 );
            $tag = '';
            $cipher = openssl_encrypt( (string)$value, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, (string)$purpose );
            return $cipher === false ? null : 'o' . base64_encode( $iv . $tag . $cipher );
        }
        return null;
    }

    /**
     * The value of a sealed text, or null when it was not sealed with this key and purpose (or was changed).
     *
     * @param string $sealed
     * @param string $purpose
     * @param string|null $key
     * @return string|null
     */
    public static function unseal( $sealed, $purpose, $key = null )
    {
        $sealed = (string)$sealed;
        $key = $key === null ? self::key( false ) : $key;
        if ( $key === null || strlen( $sealed ) < 2 )
            return null;
        $raw = base64_decode( substr( $sealed, 1 ), true );
        if ( $raw === false )
            return null;
        if ( $sealed[0] === 's' && function_exists( 'sodium_crypto_aead_xchacha20poly1305_ietf_decrypt' ) )
        {
            $n = SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;
            if ( strlen( $raw ) < $n + 16 )
                return null;
            try
            {
                $value = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt( substr( $raw, $n ), (string)$purpose, substr( $raw, 0, $n ), $key );
            }
            catch ( Throwable $e )
            {
                return null;
            }
            return $value === false ? null : $value;
        }
        if ( $sealed[0] === 'o' && function_exists( 'openssl_decrypt' ) && strlen( $raw ) >= 28 )
        {
            $value = openssl_decrypt( substr( $raw, 28 ), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, substr( $raw, 0, 12 ), substr( $raw, 12, 16 ), (string)$purpose );
            return $value === false ? null : $value;
        }
        return null;
    }

    /**
     * The installation's key (32 bytes), made when $create and there is none; null when it cannot be read or made.
     *
     * @param bool $create
     * @return string|null
     */
    public static function key( $create = true )
    {
        $file = self::keyFile();
        if ( is_file( $file ) )
        {
            $key = @file_get_contents( $file );
            return is_string( $key ) && strlen( $key ) === 32 ? $key : null;
        }
        if ( !$create )
            return null;
        $directory = dirname( $file );
        if ( !is_dir( $directory ) && !@mkdir( $directory, eZDir::dirMode( 0700 ), true ) )
            return null;
        // made empty first and narrowed to the owner before the key is written into it
        $fh = @fopen( $file, 'xb' );
        if ( !$fh )
            return null;
        @chmod( $file, eZFile::fileMode( 0600 ) );
        $key = random_bytes( 32 );
        $written = fwrite( $fh, $key );
        fclose( $fh );
        return $written === 32 ? $key : null;
    }

    /** @return string the key file's absolute path */
    public static function keyFile()
    {
        $root = class_exists( 'eZSys', false ) ? rtrim( (string)eZSys::rootDir(), '/' ) : getcwd();
        return $root . '/' . self::KEY_FILE;
    }
}
