<?php
/**
 * File containing the expIniCoreScopeProvider class: the kernel's own INI scopes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * global (settings/override), default (settings/, the shipped defaults, not writable by policy) and one scope
 * per siteaccess: every directory in settings/siteaccess/ plus every name in site.ini
 * [SiteAccessSettings] AvailableSiteAccessList[] (a listed siteaccess without a directory yet gets one on its first
 * write).
 */
class expIniCoreScopeProvider implements expIniScopeProvider
{
    public function scopes( $root )
    {
        $scopes = array(
            new expIniScope( 'global', expIniScope::KIND_GLOBAL, 'settings/override', $root,
                             'Global override (settings/override)' ),
            new expIniScope( 'default', expIniScope::KIND_DEFAULT, 'settings', $root,
                             'Kernel defaults (settings/*.ini, refused unless allowed)', array( 'suffix' => '.ini' ) ),
        );
        $names = array();
        foreach ( (array)glob( $root . 'settings/siteaccess/*', GLOB_ONLYDIR ) as $dir )
        {
            if ( expIniEditor::isValidName( basename( $dir ) ) )
                $names[basename( $dir )] = true;
        }
        foreach ( expIniEditor::knownSiteAccesses() as $sa )
        {
            if ( expIniEditor::isValidName( $sa ) )
                $names[$sa] = true;
        }
        ksort( $names );
        foreach ( array_keys( $names ) as $sa )
        {
            $sa = (string)$sa;
            $scopes[] = new expIniScope( "siteaccess:$sa", expIniScope::KIND_SITEACCESS, "settings/siteaccess/$sa", $root,
                                         "Siteaccess $sa (settings/siteaccess/$sa)", array( 'siteaccess' => $sa ) );
        }
        return $scopes;
    }
}
