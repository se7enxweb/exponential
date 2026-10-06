<?php
/**
 * File containing the eZPackageRequestGuard class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The checks every package view applies to what a request names before anything is read from disk: a package
 * name, a repository, a view mode and a file name for a download. eZPackage::fetch() joins a package name to a
 * repository path as it is, so a name such as "../7x/sevenx_classes" or "../../../somewhere" made it read (and the
 * list's removal act on) a package.xml outside the repository asked for. No database; no settings.
 */
class eZPackageRequestGuard
{
    /** The longest package or repository name accepted (a directory name; the kernel's own are far shorter). */
    const MAX_NAME_LENGTH = 200;

    /** The view modes package/view has templates for (design:package/view/<mode>.tpl). */
    static $viewModes = array( 'full', 'files' );

    /**
     * Whether $name can be a package or repository directory name: letters, digits, "_", "-" and ".", starting with
     * a letter, digit or "_", no "..", at most MAX_NAME_LENGTH long. Covers every name eZPackage::isValidName()
     * accepts and the repository names the kernel writes (local, 7x, ez-systems).
     *
     * @param mixed $name
     * @return bool
     */
    static function isSafeName( $name )
    {
        if ( !is_string( $name ) || $name === '' || strlen( $name ) > self::MAX_NAME_LENGTH )
            return false;
        if ( strpos( $name, '..' ) !== false )
            return false;
        return (bool)preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_.\-]*$/D', $name );
    }

    /**
     * The repository of $id among $repositories (as eZPackage::packageRepositories()), or false.
     *
     * @param mixed $id
     * @param array|null $repositories null reads eZPackage::packageRepositories()
     * @return array|false
     */
    static function repository( $id, $repositories = null )
    {
        if ( !self::isSafeName( $id ) )
            return false;
        if ( $repositories === null )
            $repositories = eZPackage::packageRepositories();
        foreach ( $repositories as $repository )
        {
            if ( (string)$repository['id'] === $id )
                return $repository;
        }
        return false;
    }

    /**
     * The view mode if package/view has a template for it, else false.
     *
     * @param mixed $mode
     * @return string|false
     */
    static function viewMode( $mode )
    {
        return is_string( $mode ) && in_array( $mode, self::$viewModes, true ) ? $mode : false;
    }

    /**
     * The package names of a list (the PackageSelection[] of a form), each once, the unsafe ones left out.
     *
     * @param mixed $selection
     * @return array( string[] $safe, int $refused )
     */
    static function selection( $selection )
    {
        $safe = array();
        $refused = 0;
        foreach ( is_array( $selection ) ? $selection : array( $selection ) as $name )
        {
            if ( self::isSafeName( $name ) )
                $safe[$name] = $name;
            else
                $refused++;
        }
        return array( array_values( $safe ), $refused );
    }

    /**
     * A file name for a Content-Disposition header: the base name, without control characters, quotes, back
     * slashes or path separators, never empty; and the RFC 5987 form for names that are not plain ASCII.
     *
     * @param string $name
     * @param string $fallback used when nothing of $name is left
     * @return string the whole header value, "attachment; filename=...; filename*=UTF-8''..."
     */
    static function contentDisposition( $name, $fallback = 'download.bin', $type = 'attachment' )
    {
        $name = str_replace( '\\', '/', (string)$name );
        $name = basename( $name );
        $name = preg_replace( '/[\x00-\x1F\x7F"\/;]/', '', $name );
        $name = trim( $name, " .\t" );
        if ( $name === '' )
            $name = $fallback;
        $ascii = (string)preg_replace( preg_match( '//u', $name ) ? '/[^\x20-\x7E]/u' : '/[^\x20-\x7E]/', '_', $name );
        $value = $type . '; filename="' . $ascii . '"';
        if ( $ascii !== $name )
            $value .= "; filename*=UTF-8''" . rawurlencode( $name );
        return $value;
    }
}

?>
