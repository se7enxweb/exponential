<?php
/**
 * Finds the native calls that give a file or directory its mode (chmod(), mkdir(), umask()) in the code of the
 * installation and tells those that bypass the limits EZP_FILE_MODE_MAX / EZP_DIR_MODE_MAX: a call whose mode does
 * not go through eZFile::fileMode(), eZDir::dirMode() or eZFile::creationUmask(). Used by expFileModeLimitsTest.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class expFileModeCalls
{
    /** What is scanned, relative to the installation root: the kernel, the libraries, the scripts, the extensions of the repository */
    const PATHS = array( 'kernel', 'lib', 'bin', 'cronjobs', 'update', 'extension/ezjscore', 'extension/ezoe',
                         'extension/ezformtoken', 'extension/expservices', 'autoload.php', 'index.php', 'index_cluster.php',
                         'index_rest.php', 'index_treemenu.php', 'runcronjobs.php', 'soap.php', 'webdav.php', 'ezpm.php' );

    /** Helpers whose result is a mode within the limits */
    const HELPERS = array( 'fileMode', 'dirMode', 'creationUmask' );

    /**
     * The calls that bypass the limits, per file: array( 'path' => array( line, ... ) ).
     *
     * @param string $root
     * @return array
     */
    public static function bypasses( $root )
    {
        $found = array();
        foreach ( self::files( $root ) as $path )
        {
            $lines = self::bypassesIn( file_get_contents( $root . '/' . $path ) );
            if ( $lines )
            {
                $found[$path] = $lines;
            }
        }
        ksort( $found );
        return $found;
    }

    /**
     * The lines of the calls in the PHP source $source that bypass the limits.
     *
     * @param string $source
     * @return int[]
     */
    public static function bypassesIn( $source )
    {
        $tokens = token_get_all( $source );
        $count = count( $tokens );
        $lines = array();
        for ( $i = 0; $i < $count; ++$i )
        {
            $token = $tokens[$i];
            // chmod() as T_STRING, \chmod() as one fully qualified name (PHP 8)
            if ( !is_array( $token ) ||
                 !( $token[0] === T_STRING || ( defined( 'T_NAME_FULLY_QUALIFIED' ) && $token[0] === T_NAME_FULLY_QUALIFIED ) ) ||
                 !in_array( strtolower( ltrim( $token[1], '\\' ) ), array( 'chmod', 'mkdir', 'umask' ), true ) )
            {
                continue;
            }
            // a method (->mkdir, ::mkdir) or a function of that name being declared is not the native call
            $previous = self::previousCode( $tokens, $i );
            if ( $previous !== null && in_array( $previous, array( '->', '::', '?->', 'function', 'new' ), true ) )
            {
                continue;
            }
            $open = self::nextCode( $tokens, $i );
            if ( $open === null || $tokens[$open] !== '(' )
            {
                continue;
            }
            $arguments = self::arguments( $tokens, $open );
            if ( !self::isWithinLimits( strtolower( ltrim( $token[1], '\\' ) ), $arguments ) )
            {
                $lines[] = $token[2];
            }
        }
        return $lines;
    }

    /**
     * Whether a call keeps to the limits: its mode argument is a call of a helper and nothing else; umask() without
     * argument only reads it; umask( $old... ) puts back what an earlier umask() returned.
     *
     * @param string $function
     * @param string[] $arguments The source of each argument
     * @return bool
     */
    protected static function isWithinLimits( $function, array $arguments )
    {
        if ( $function === 'umask' )
        {
            if ( !$arguments )
            {
                return true;
            }
            if ( preg_match( '/^\$old[A-Za-z0-9_]*$/i', trim( $arguments[0] ) ) )
            {
                return true;
            }
            return self::usesHelper( $arguments[0] );
        }
        // chmod( path, mode ), mkdir( path, mode, ... ): mkdir() without a mode takes 0777 and the umask
        if ( count( $arguments ) < 2 )
        {
            return $function === 'mkdir' ? false : true;
        }
        return self::usesHelper( $arguments[1] );
    }

    /**
     * Whether the argument $source is one call of a helper, with nothing around it ("eZFile::fileMode( 0640 ) | 0777"
     * is not).
     */
    protected static function usesHelper( $source )
    {
        $source = trim( $source );
        if ( !preg_match( '/^\\\\?(eZFile|eZDir|self|static)::(' . implode( '|', self::HELPERS ) . ')\s*\(/i', $source, $match ) )
        {
            return false;
        }
        // the parenthesis the call opens has to close at the very end of the argument
        $depth = 0;
        $length = strlen( $source );
        for ( $i = strlen( $match[0] ) - 1; $i < $length; ++$i )
        {
            if ( $source[$i] === '(' )
            {
                ++$depth;
            }
            else if ( $source[$i] === ')' && --$depth === 0 )
            {
                return $i === $length - 1;
            }
        }
        return false;
    }

    /**
     * The source of the arguments of the call whose "(" is at $open, split at the commas of its own level.
     *
     * @return string[]
     */
    protected static function arguments( array $tokens, $open )
    {
        $depth = 0;
        $arguments = array();
        $current = '';
        $count = count( $tokens );
        for ( $i = $open; $i < $count; ++$i )
        {
            $text = is_array( $tokens[$i] ) ? $tokens[$i][1] : $tokens[$i];
            if ( $text === '(' || $text === '[' || $text === '{' || ( is_array( $tokens[$i] ) && in_array( $tokens[$i][0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) )
            {
                ++$depth;
                if ( $depth === 1 )
                {
                    continue;
                }
            }
            else if ( $text === ')' || $text === ']' || $text === '}' )
            {
                --$depth;
                if ( $depth === 0 )
                {
                    break;
                }
            }
            else if ( $text === ',' && $depth === 1 )
            {
                $arguments[] = $current;
                $current = '';
                continue;
            }
            $current .= $text;
        }
        if ( trim( $current ) !== '' )
        {
            $arguments[] = $current;
        }
        return $arguments;
    }

    protected static function previousCode( array $tokens, $i )
    {
        for ( --$i; $i >= 0; --$i )
        {
            if ( is_array( $tokens[$i] ) && in_array( $tokens[$i][0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) )
            {
                continue;
            }
            return is_array( $tokens[$i] ) ? strtolower( $tokens[$i][1] ) : $tokens[$i];
        }
        return null;
    }

    protected static function nextCode( array $tokens, $i )
    {
        $count = count( $tokens );
        for ( ++$i; $i < $count; ++$i )
        {
            if ( is_array( $tokens[$i] ) && in_array( $tokens[$i][0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true ) )
            {
                continue;
            }
            return $i;
        }
        return null;
    }

    /**
     * The PHP files under PATHS, relative to $root, without vendor directories.
     *
     * @return string[]
     */
    protected static function files( $root )
    {
        $files = array();
        foreach ( self::PATHS as $path )
        {
            $full = $root . '/' . $path;
            if ( is_file( $full ) )
            {
                $files[] = $path;
                continue;
            }
            if ( !is_dir( $full ) )
            {
                continue;
            }
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $full, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
            {
                $relative = substr( $file->getPathname(), strlen( $root ) + 1 );
                if ( $file->isFile() && substr( $relative, -4 ) === '.php' && strpos( $relative, '/vendor/' ) === false &&
                     strpos( $relative, '/node_modules/' ) === false )
                {
                    $files[] = $relative;
                }
            }
        }
        sort( $files );
        return $files;
    }
}
