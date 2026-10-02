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
     *               effective, found
     * @throws expIniException REFUSED for an unknown siteaccess, USAGE for a bad file name
     */
    public static function where( $file, $block, $variable, $siteAccess = null )
    {
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
        return array( 'file' => $base, 'block' => $block, 'variable' => $variable, 'siteaccess' => $siteAccess ) + $result;
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
            try
            {
                $values = expIniWriter::fromFile( self::absolute( $path, $root ) )->values();
            }
            catch ( expIniException $e )
            {
                continue;
            }
            if ( !isset( $values[$block] ) || !array_key_exists( $variable, $values[$block] ) )
                continue;
            $files[] = array(
                'path' => $rel,
                'placement' => $ini->findSettingPlacement( $rel ),
                'scope' => self::scopeOf( $rel ),
                'value' => $values[$block][$variable],
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
