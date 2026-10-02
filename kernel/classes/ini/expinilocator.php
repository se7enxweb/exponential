<?php
/**
 * File containing the expIniLocator class: where a setting is set, in eZINI's load order, and what it ends up as.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Every file that sets a variable, in the order eZINI loads them for a siteaccess, and the effective value.
 *
 * The order is not re-derived: it is eZINI's own. For a siteaccess, eZSiteAccess::getIni( $sa, 'site.ini' ) builds
 * the site.ini of that siteaccess on a local instance -- eZSiteAccess::load() prepends settings/siteaccess/<sa>,
 * activates the extensions (eZExtension::activateExtensions() puts every active extension's settings directory in
 * the 'extension' scope) and the extension siteaccess directories (eZExtension::prependSiteAccess(): 'sa-extension'
 * and the extension override directories) -- and its override directories (overrideDirs( false )) are copied to a
 * fresh, uncached eZINI of the requested file, exactly as getIni() does for any file other than site.ini. That
 * instance's own findInputFiles() then lists the files: settings/<file>.ini first, then the override directories
 * in eZINI's scope order sa-extension, siteaccess, extension, override (eZINI::overrideDirsByScope()), each as
 * <file>.ini.php, <file>.ini, <file>.ini.append.php, <file>.ini.append. Later files win. The effective value is
 * that instance's variable() -- eZINI itself, not a re-implementation. The cache is not used, so a write made a
 * moment ago is seen even before the ini cache is cleared.
 */
class expIniLocator
{
    /**
     * @param string $file 'site' or 'site.ini'
     * @param string $block
     * @param string $variable
     * @param string|null $siteAccess The siteaccess whose load order to use; null: the current one
     * @return array file, block, variable, siteaccess, files (path, placement, scope, value), inputFiles,
     *               effective, found, notLoaded (path, scope, reason, value: the extension files that set it but
     *               that this siteaccess does not load, see notLoadedFiles())
     * @throws expIniException REFUSED for an unknown siteaccess, USAGE for a bad file name
     */
    public static function where( $file, $block, $variable, $siteAccess = null )
    {
        $variable = self::variableName( $variable );
        $base = expIniScope::baseName( $file );
        if ( !expIniEditor::isValidName( $base ) )
            throw expIniException::usage( "Malformed INI file name '$file'" );
        if ( $siteAccess !== null && $siteAccess !== '' )
        {
            // throws REFUSED for an unknown siteaccess
            expIniEditor::scope( 'siteaccess:' . $siteAccess );
        }
        else
        {
            $siteAccess = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : null;
        }
        $ini = self::iniFor( $base . '.ini', $siteAccess );
        $result = self::whereInIni( $ini, $block, $variable );
        $result['notLoaded'] = self::notLoadedFiles( $base, $block, $variable, $result['inputFiles'], $siteAccess );
        return array( 'file' => $base, 'block' => $block, 'variable' => $variable, 'siteaccess' => $siteAccess ) + $result;
    }

    /**
     * The files of extension scopes that set a variable although the load order does not include them: the
     * extension is not active for the siteaccess (not in ActiveExtensions, nor in its ActiveAccessExtensions), so
     * eZINI never reads them. They are what someone looking for "who sets this" needs to see as well, marked as
     * not in effect. Extension siteaccess directories of other siteaccesses are not listed: no load order of this
     * siteaccess could include them.
     *
     * @param string $base File name without .ini ('cronjob')
     * @param string $block
     * @param string $variable
     * @param string[] $loaded The files of the load order, relative to the root (whereInIni()'s inputFiles)
     * @param string|null $siteAccess The siteaccess of that load order
     * @return array[] path (relative), scope (name), reason, value -- in scope order
     */
    public static function notLoadedFiles( $base, $block, $variable, array $loaded, $siteAccess )
    {
        $variable = self::variableName( $variable );
        $root = expIniEditor::root();
        $files = array();
        foreach ( expIniEditor::scopes() as $scope )
        {
            if ( !self::canBeInactive( $scope, $siteAccess ) )
                continue;
            foreach ( self::$Suffixes as $suffix )
            {
                $rel = $scope->dir() . '/' . $base . $suffix;
                if ( in_array( $rel, $loaded, true ) || !is_file( $root . $rel ) )
                    continue;
                $value = self::valueInFile( $root . $rel, $block, $variable );
                if ( $value === null )
                    continue;
                $files[] = array( 'path' => $rel, 'scope' => $scope->name(),
                                  'reason' => 'extension not active for this siteaccess', 'value' => $value );
            }
        }
        return $files;
    }

    /**
     * A variable name as written in a setting: 'Scripts[]' and 'Scripts' both mean the variable Scripts.
     *
     * @param string $variable
     * @return string
     */
    public static function variableName( $variable )
    {
        return substr( (string)$variable, -2 ) === '[]' ? substr( $variable, 0, -2 ) : $variable;
    }

    /** The file names eZINI reads in a settings directory, in its order (eZINI::findInputFiles()). */
    protected static $Suffixes = array( '.ini.php', '.ini', '.ini.append.php', '.ini.append' );

    /**
     * Whether a scope is one whose files a siteaccess may leave out of its load order: an extension's settings,
     * or an extension's directory for that siteaccess.
     */
    protected static function canBeInactive( expIniScope $scope, $siteAccess )
    {
        if ( $scope->kind() === expIniScope::KIND_EXTENSION )
            return true;
        return $scope->kind() === expIniScope::KIND_EXTENSION_SITEACCESS
            && ( $siteAccess === null || $scope->siteAccess() === $siteAccess );
    }

    /**
     * The value one file gives a variable, null when it does not set it (or cannot be read).
     *
     * @param string $path Absolute
     * @return string|array|null
     */
    protected static function valueInFile( $path, $block, $variable )
    {
        try
        {
            $values = expIniWriter::fromFile( $path )->values();
        }
        catch ( expIniException $e )
        {
            return null;
        }
        if ( !isset( $values[$block] ) || !array_key_exists( $variable, $values[$block] ) )
            return null;
        return $values[$block][$variable];
    }

    /**
     * An uncached eZINI of $iniFile with the override directories of $siteAccess (null: the current context).
     *
     * @param string $iniFile 'site.ini'
     * @param string|null $siteAccess
     * @return eZINI
     */
    public static function iniFor( $iniFile, $siteAccess )
    {
        $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : null;
        if ( $siteAccess === null || $siteAccess === $current )
            $template = eZINI::instance( 'site.ini' );
        else
            $template = eZSiteAccess::getIni( $siteAccess, 'site.ini' );
        $ini = new eZINI( $iniFile, 'settings', null, false, true, false, false, false );
        $ini->setOverrideDirs( $template->overrideDirs( false ) );
        $ini->load();
        return $ini;
    }

    /**
     * The files of an eZINI instance that set a variable, in its load order, and its effective value.
     *
     * @param eZINI $ini
     * @param string $block
     * @param string $variable
     * @return array files, inputFiles, effective, found
     */
    public static function whereInIni( eZINI $ini, $block, $variable )
    {
        $variable = self::variableName( $variable );
        $inputFiles = array();
        $iniFile = null;
        $ini->findInputFiles( $inputFiles, $iniFile );
        $root = expIniEditor::root();
        $files = array();
        $relatives = array();
        foreach ( $inputFiles as $path )
        {
            $rel = self::relative( $path, $root );
            $relatives[] = $rel;
            $value = self::valueInFile( self::absolute( $path, $root ), $block, $variable );
            if ( $value === null )
                continue;
            $files[] = array(
                'path' => $rel,
                'placement' => $ini->findSettingPlacement( $rel ),
                'scope' => self::scopeOf( $rel ),
                'value' => $value,
            );
        }
        $found = $ini->hasVariable( $block, $variable );
        return array(
            'files' => $files,
            'inputFiles' => $relatives,
            'effective' => $found ? $ini->variable( $block, $variable ) : null,
            'found' => $found,
        );
    }

    /**
     * The name of the scope whose directory holds a file, null when none does.
     *
     * @param string $relativePath
     * @return string|null
     */
    public static function scopeOf( $relativePath )
    {
        $dir = dirname( $relativePath );
        foreach ( expIniEditor::scopes() as $scope )
        {
            if ( $scope->dir() === $dir )
                return $scope->name();
        }
        return null;
    }

    protected static function absolute( $path, $root )
    {
        return $path !== '' && $path[0] === '/' ? $path : $root . $path;
    }

    protected static function relative( $path, $root )
    {
        $abs = self::absolute( $path, $root );
        $real = realpath( $abs );
        if ( $real !== false )
            $abs = $real;
        $realRoot = realpath( $root );
        $realRoot = ( $realRoot !== false ? $realRoot : rtrim( $root, '/' ) ) . '/';
        if ( strpos( $abs, $realRoot ) === 0 )
            return substr( $abs, strlen( $realRoot ) );
        $path = preg_replace( '#/+#', '/', $path );
        return strpos( $path, './' ) === 0 ? substr( $path, 2 ) : $path;
    }
}
