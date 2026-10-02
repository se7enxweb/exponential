<?php
/**
 * File containing the expIniScopeProvider interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A source of INI scopes (places an INI file can be written). The built-in providers are
 * expIniCoreScopeProvider (global, default, siteaccesses) and expIniExtensionScopeProvider (extensions and
 * their siteaccess directories). More are registered in settings/ini.ini:
 *
 *   [IniCommandSettings]
 *   ScopeProviders[]=myClusterScopeProvider
 *
 * A provider is constructed without arguments.
 */
interface expIniScopeProvider
{
    /**
     * The scopes this provider knows about, for the installation root $root.
     *
     * @param string $root Installation root, with a trailing slash
     * @return expIniScope[]
     */
    public function scopes( $root );
}
