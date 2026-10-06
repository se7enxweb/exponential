<?php
/**
 * File containing the expSettingsTarget class: what the settings pages accept as a file, a siteaccess, a block, a
 * setting name and a place to write to.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The checks between a request and a settings file. Everything that names a file or a directory comes from a
 * list the installation itself produced (the INI files found on disk, RelatedSiteAccessList, the active
 * extensions), never from the request as it is, so no parameter can lead a write or a read outside the settings
 * tree: '../', an absolute path, a NUL byte or a name that is not on the list is refused.
 *
 * Every method is static and reads nothing but its arguments.
 */
class expSettingsTarget
{
    /**
     * The INI file name asked for when it is one of the known ones, else null.
     *
     * @param mixed $name 'site.ini'
     * @param string[] $known The file names the settings page lists
     * @return string|null
     */
    public static function iniFile( $name, array $known )
    {
        if ( !is_string( $name ) || !preg_match( '#^[A-Za-z0-9_][A-Za-z0-9_.\-]*\.ini$#D', $name ) )
            return null;
        return in_array( $name, $known, true ) ? $name : null;
    }

    /**
     * The siteaccess asked for when it is one of the listed ones, else null.
     *
     * @param mixed $name
     * @param string[] $known RelatedSiteAccessList
     * @return string|null
     */
    public static function siteAccess( $name, array $known )
    {
        if ( !is_string( $name ) || !preg_match( '#^[A-Za-z0-9_\-]+$#D', $name ) )
            return null;
        return in_array( $name, $known, true ) ? $name : null;
    }

    /**
     * Whether a block name can be written to an INI file and read back as the same block: not empty, no
     * brackets, line breaks, NUL bytes or the end of a PHP comment, at most 200 characters.
     *
     * @param mixed $block
     * @return bool
     */
    public static function isValidBlock( $block )
    {
        if ( !is_string( $block ) )
            return false;
        $trimmed = trim( $block );
        return $trimmed !== '' && $trimmed === $block && strlen( $block ) <= 200
            && strpbrk( $block, "[]\r\n\0" ) === false && strpos( $block, '*/' ) === false
            && strpos( $block, '##' ) === false;
    }

    /**
     * Whether a setting name is one eZINI reads back as a variable ([\w*@-]+, the pattern of eZINI::parseFile()).
     *
     * @param mixed $name
     * @return bool
     */
    public static function isValidName( $name )
    {
        return is_string( $name ) && strlen( $name ) <= 200 && (bool)preg_match( '#^[\w*@\-]+$#D', $name )
            && strpos( $name, '*/' ) === false;
    }

    /**
     * Whether an array key can be written as Var[key]=value and read back as the same key.
     *
     * @param mixed $key
     * @return bool
     */
    public static function isValidArrayKey( $key )
    {
        return is_string( $key ) && $key !== '' && strpbrk( $key, "[]\r\n\0" ) === false && strpos( $key, '*/' ) === false
            && strpos( $key, '##' ) === false;
    }

    /**
     * The directory, relative to the root, that a placement of the edit form writes to, or null when the
     * placement is not one the form offers.
     *
     *   siteaccess  settings/siteaccess/<siteaccess>
     *   override    settings/override
     *   <ext>       extension/<ext>/settings (an active extension only)
     *
     * @param mixed $placement The form's SettingPlacement
     * @param string $siteAccess Already checked with siteAccess()
     * @param string[] $extensions The active extensions
     * @return string|null
     */
    public static function writeDirectory( $placement, $siteAccess, array $extensions )
    {
        if ( !is_string( $placement ) || $placement === '' )
            return null;
        if ( $placement === 'siteaccess' )
            return preg_match( '#^[A-Za-z0-9_\-]+$#D', (string)$siteAccess ) ? 'settings/siteaccess/' . $siteAccess : null;
        if ( $placement === 'override' )
            return 'settings/override';
        if ( preg_match( '#^[A-Za-z0-9_\-]+$#D', $placement ) && in_array( $placement, $extensions, true ) )
            return 'extension/' . $placement . '/settings';
        return null;
    }

    /**
     * Whether a file of the chain may be written to by the settings pages: one of the override files of the
     * chain (never settings/<file>.ini itself), in settings/override, the siteaccess's own directory or an active
     * extension's settings, and named after the INI file.
     *
     * @param string $path Relative to the root, from expSettingsChain::paths()
     * @param string $iniFile 'site.ini'
     * @param string $siteAccess
     * @param string[] $extensions The active extensions
     * @return bool
     */
    public static function isWritableChainFile( $path, $iniFile, $siteAccess, array $extensions )
    {
        if ( !is_string( $path ) || strpos( $path, '..' ) !== false || strpos( $path, "\0" ) !== false || $path === '' || $path[0] === '/' )
            return false;
        $base = basename( $path );
        if ( !in_array( $base, array( $iniFile . '.append.php', $iniFile . '.append' ), true ) )
            return false;
        $dir = dirname( $path );
        if ( $dir === 'settings/override' || $dir === 'settings/siteaccess/' . $siteAccess )
            return true;
        foreach ( $extensions as $extension )
        {
            if ( $dir === 'extension/' . $extension . '/settings' || $dir === 'extension/' . $extension . '/settings/siteaccess/' . $siteAccess )
                return true;
        }
        return false;
    }

    /**
     * The lines of the edit form's array field as an array: "[key]=value" sets a key, "=value" adds an element
     * (so does a line without '='), an empty first line means an array that is emptied first. Lines that cannot be written (a key with
     * brackets) are reported, not dropped silently.
     *
     * @param string $text
     * @return array values (the array to save), invalid (the lines refused)
     */
    public static function parseArrayText( $text )
    {
        $values = array();
        $invalid = array();
        $lines = preg_split( "/\r\n|\n|\r/", (string)$text );
        foreach ( $lines as $i => $line )
        {
            if ( preg_match( '/^\[(.*?)\]=(.*)$/s', $line, $m ) )
            {
                if ( !self::isValidArrayKey( $m[1] ) )
                {
                    $invalid[] = $line;
                    continue;
                }
                $values[$m[1]] = $m[2];
                continue;
            }
            // as the form has always read it: what follows the first '=' ("=value"); a line without one is the value
            $value = strpos( $line, '=' ) !== false ? substr( $line, strpos( $line, '=' ) + 1 ) : $line;
            if ( trim( $value ) === '' )
            {
                // only an empty first line means something: the array is emptied before the elements
                if ( $i === 0 )
                    $values[] = null;
                continue;
            }
            $values[] = $value;
        }
        return array( 'values' => $values, 'invalid' => $invalid );
    }
}
