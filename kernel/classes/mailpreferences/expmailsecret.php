<?php
/**
 * File containing the expMailSecret class.
 *
 * The site secret of the e-mail preference system: the key of the links in mail (expMailToken) and the salt of
 * the address hashes (expMailSuppression, the recipient key of an address without account).
 *
 * Read from mailpreferences.ini [SecretSettings] TokenSecret as eZINI merges it, else from
 * settings/override/mailpreferences.ini.append.php read directly (a secret another process generated a moment ago
 * is found although this process's INI cache still has the old file), else generated on first use with
 * random_bytes() and written into that file through expIniEditor, under a lock, so two processes never make two
 * different secrets. settings/override is never committed. expIniEditor::isSecret() masks "TokenSecret", so
 * exp:ini, the debug bar and the settings views never show it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailSecret
{
    const BLOCK = 'SecretSettings';
    const VARIABLE = 'TokenSecret';

    /** @var string|null raw secret bytes known to this process */
    protected static $secret = null;

    /** @var string|null test override: the settings directory the key file is in (absolute) */
    protected static $keyDir = null;

    /**
     * The raw secret (32 bytes), generated when there is none.
     *
     * @return string
     * @throws RuntimeException when no secret can be read or made
     */
    public static function get()
    {
        if ( self::$secret !== null )
            return self::$secret;
        $value = '';
        if ( self::$keyDir === null )
        {
            $ini = eZINI::instance( 'mailpreferences.ini' );
            if ( $ini->hasVariable( self::BLOCK, self::VARIABLE ) )
                $value = trim( (string)$ini->variable( self::BLOCK, self::VARIABLE ) );
        }
        $raw = self::decode( $value );
        if ( $raw === null )
            $raw = self::decode( self::readFile() );
        if ( $raw === null )
            $raw = self::generate();
        return self::$secret = $raw;
    }

    /** @return bool A secret is configured or stored (nothing is generated) */
    public static function exists()
    {
        if ( self::$secret !== null )
            return true;
        if ( self::$keyDir === null )
        {
            $ini = eZINI::instance( 'mailpreferences.ini' );
            if ( $ini->hasVariable( self::BLOCK, self::VARIABLE ) && self::decode( (string)$ini->variable( self::BLOCK, self::VARIABLE ) ) !== null )
                return true;
        }
        return self::decode( self::readFile() ) !== null;
    }

    /**
     * A key for one use, derived from the secret, so the token key and the hash salt are never the same bytes.
     *
     * @param string $purpose e.g. 'token', 'hash'
     * @return string 32 raw bytes
     */
    public static function derive( $purpose )
    {
        return hash_hmac( 'sha256', 'exp-mailpreferences:' . $purpose, self::get(), true );
    }

    /**
     * Tests only: keep the key file in another directory, or a fixed secret (raw bytes); null resets both.
     *
     * @param string|null $keyDir
     * @param string|null $secret
     */
    public static function setForTest( $keyDir = null, $secret = null )
    {
        self::$keyDir = $keyDir;
        self::$secret = $secret;
    }

    /** Forgets what this process knows (the next get() reads again). */
    public static function reset()
    {
        self::$secret = null;
    }

    /** @return string the absolute path of the key file */
    public static function keyFile()
    {
        return self::keyDir() . '/mailpreferences.ini.append.php';
    }

    protected static function keyDir()
    {
        if ( self::$keyDir !== null )
            return rtrim( self::$keyDir, '/' );
        return rtrim( getcwd(), '/' ) . '/settings/override';
    }

    /**
     * @param string $value base64 of at least 32 bytes
     * @return string|null raw bytes
     */
    protected static function decode( $value )
    {
        $value = trim( (string)$value );
        if ( $value === '' )
            return null;
        $raw = base64_decode( $value, true );
        if ( $raw === false || strlen( $raw ) < 32 )
            return null;
        return $raw;
    }

    /** @return string the value in the key file, read directly ('' without one) */
    protected static function readFile()
    {
        $file = self::keyFile();
        if ( !is_file( $file ) )
            return '';
        if ( class_exists( 'expIniWriter' ) )
        {
            try
            {
                $values = expIniWriter::fromFile( $file )->values();
                return isset( $values[self::BLOCK][self::VARIABLE] ) ? (string)$values[self::BLOCK][self::VARIABLE] : '';
            }
            catch ( Throwable $e )
            {
            }
        }
        // the plain format: TokenSecret=<base64>
        if ( preg_match( '/^\s*' . self::VARIABLE . '\s*=\s*(\S+)\s*$/m', (string)@file_get_contents( $file ), $m ) )
            return $m[1];
        return '';
    }

    /**
     * Makes the secret under a lock and writes it into the key file.
     *
     * @return string raw bytes
     */
    protected static function generate()
    {
        $dir = self::keyDir();
        $lock = @fopen( $dir . '/.mailpreferences-secret.lock', 'c' );
        if ( $lock )
            flock( $lock, LOCK_EX );
        try
        {
            // another process may have written it while this one waited
            $raw = self::decode( self::readFile() );
            if ( $raw !== null )
                return $raw;
            $raw = random_bytes( 32 );
            if ( !self::writeFile( base64_encode( $raw ) ) )
            {
                if ( class_exists( 'eZDebug' ) )
                    eZDebug::writeError( 'The mail preference secret could not be written to ' . self::keyFile() . '; links made by this process stop working when it ends', __METHOD__ );
            }
            return $raw;
        }
        finally
        {
            if ( $lock )
            {
                flock( $lock, LOCK_UN );
                fclose( $lock );
            }
        }
    }

    /**
     * @param string $value base64
     * @return bool
     */
    protected static function writeFile( $value )
    {
        $dir = self::keyDir();
        $file = self::keyFile();
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) )
            return false;
        $existed = is_file( $file );
        if ( class_exists( 'expIniEditor' ) && class_exists( 'expIniScope' ) )
        {
            try
            {
                $root = dirname( $dir ) . '/';
                // as expAuditKeys: the scope's root is the parent of the key directory
                $scope = new expIniScope( 'mailpreferences-secret', expIniScope::KIND_GLOBAL, basename( $dir ), $root, 'Mail preference secret' );
                $editor = new expIniEditor( $scope, 'mailpreferences.ini' );
                $editor->set( self::BLOCK, self::VARIABLE, $value );
                $editor->save( array( 'backup' => false ) );
                if ( !$existed )
                    self::own( $file );
                return true;
            }
            catch ( Throwable $e )
            {
                if ( class_exists( 'eZDebug' ) )
                    eZDebug::writeWarning( 'expIniEditor: ' . $e->getMessage(), __METHOD__ );
                if ( $existed )
                    return false;
            }
        }
        if ( $existed )
            return false;
        $content = "<?php /* #?ini charset=\"utf-8\"?\n\n[" . self::BLOCK . "]\n" . self::VARIABLE . '=' . $value . "\n*/ ?>\n";
        $tmp = $file . '.tmp.' . getmypid();
        if ( @file_put_contents( $tmp, $content ) === false || !@rename( $tmp, $file ) )
            return false;
        self::own( $file );
        return true;
    }

    /** mode 0640, owner and group of the directory (also when written as root) */
    protected static function own( $file )
    {
        if ( class_exists( 'expAuditWriter' ) )
            expAuditWriter::ownLikeParent( $file, 0640 );
        else
            @chmod( $file, eZFile::fileMode( 0640 ) );
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $file, true );
    }
}
