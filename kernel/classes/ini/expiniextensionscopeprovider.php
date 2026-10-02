<?php
/**
 * File containing the expIniExtensionScopeProvider class: the INI scopes of extensions.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * extension:<ext> for every directory in extension/ (active or not; the label says which) and
 * extension:<ext>:siteaccess:<sa> for every extension/<ext>/settings/siteaccess/<sa> directory present.
 */
class expIniExtensionScopeProvider implements expIniScopeProvider
{
    public function scopes( $root )
    {
        $active = array_flip( expIniEditor::activeExtensions() );
        $scopes = array();
        $siteAccessScopes = array();
        $dirs = (array)glob( $root . 'extension/*', GLOB_ONLYDIR );
        sort( $dirs );
        foreach ( $dirs as $dir )
        {
            $ext = basename( $dir );
            if ( !expIniEditor::isValidName( $ext ) )
                continue;
            $isActive = isset( $active[$ext] );
            $scopes[] = new expIniScope( "extension:$ext", expIniScope::KIND_EXTENSION, "extension/$ext/settings", $root,
                                         "Extension $ext (" . ( $isActive ? 'active' : 'inactive' ) . ", extension/$ext/settings)",
                                         array( 'extension' => $ext, 'active' => $isActive ) );
            $saDirs = (array)glob( $dir . '/settings/siteaccess/*', GLOB_ONLYDIR );
            sort( $saDirs );
            foreach ( $saDirs as $saDir )
            {
                $sa = basename( $saDir );
                if ( !expIniEditor::isValidName( $sa ) )
                    continue;
                $siteAccessScopes[] = self::siteAccessScope( $root, $ext, $sa, $isActive );
            }
        }
        return array_merge( $scopes, $siteAccessScopes );
    }

    /**
     * The scope of one extension siteaccess directory (which need not exist yet).
     *
     * @param string $root
     * @param string $ext
     * @param string $sa
     * @param bool|null $isActive
     * @return expIniScope
     */
    public static function siteAccessScope( $root, $ext, $sa, $isActive = null )
    {
        return new expIniScope( "extension:$ext:siteaccess:$sa", expIniScope::KIND_EXTENSION_SITEACCESS,
                                "extension/$ext/settings/siteaccess/$sa", $root,
                                "Extension $ext, siteaccess $sa (extension/$ext/settings/siteaccess/$sa)",
                                array( 'extension' => $ext, 'siteaccess' => $sa, 'active' => $isActive ) );
    }
}
