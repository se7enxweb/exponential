<?php
/**
 * File containing the expManagedConfiguration class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * site.ini [ConfigurationSettings] ManagedByDeployment=enabled: settings, templates and code come from the
 * deployment only, and the admin interface does not change them on the server.
 *
 * Two lines of defence, both only in a web request (command line scripts and the deployment keep writing):
 * - the views in LOCKED_VIEWS answer like a module switched off in [SiteAccessRules] (eZModule::accessAllowed()),
 *   so the menus, the dashboard and the tabs hide them through expViewAccess as well;
 * - eZINI::save() and expIniEditor::save() refuse to write, which also stops writers the list does not know
 *   (extension modules, ezjscore calls, the debug bar, the audit and mail settings pages, views added later).
 *
 * A web request is any SAPI but cli/phpdbg, and a CLI process that serves the web (Velocity, see
 * isWebServerInCli()).
 *
 * Read-only views (settings/view, template and package lists, cache, system info) stay open.
 */
class expManagedConfiguration
{
    /**
     * The views that write configuration, templates, overrides, generated code or packages to the disk.
     * Each entry is "module/view". Kept here, not in the INI, so the switch cannot be weakened from a
     * settings file, and guarded by expManagedConfigurationTest (every entry is a view that exists).
     */
    const LOCKED_VIEWS = array(
        // INI files
        'settings/edit',
        'setup/settingstoolbar',
        // toolbars and the menu configuration (write toolbar.ini / menu.ini overrides)
        'visual/toolbar',
        'visual/toolbarlist',
        'visual/menuconfig',
        'setup/toolbar',
        'setup/toolbarlist',
        'setup/menuconfig',
        // template editor (writes .tpl files and override.ini)
        'visual/templateedit',
        'visual/templatecreate',
        'setup/templateedit',
        'setup/templatecreate',
        // code generators, extension activation and the setup wizard
        'setup/init',
        'setup/extensions',
        'setup/kerneloverride',
        'setup/modulewizard',
        'setup/datatype',
        'setup/templateoperator',
        'setup/workflowevent',
        'setup/contentextension',
        'setup/designextension',
        'setup/handlerextension',
        'setup/moduleextension',
        'setup/settingsextension',
        'setup/rad',
        'setup/radsurvey',
        // package import and export
        'package/upload',
        'package/install',
        'package/uninstall',
        'package/create',
        'package/export',
    );

    /**
     * @var string|null the SAPI refusesWrite() decides for instead of PHP_SAPI; set by the tests only, so that
     *                  eZINI::save() can be driven as a web request from the command line
     */
    public static $sapi = null;

    /**
     * @param eZINI|null $ini site.ini of the current siteaccess when null
     * @return bool whether site.ini [ConfigurationSettings] ManagedByDeployment is enabled
     */
    public static function isEnabled( $ini = null )
    {
        if ( $ini === null )
            $ini = eZINI::instance();
        if ( !$ini->hasVariable( 'ConfigurationSettings', 'ManagedByDeployment' ) )
            return false;
        return strtolower( trim( (string)$ini->variable( 'ConfigurationSettings', 'ManagedByDeployment' ) ) ) === 'enabled';
    }

    /**
     * @param string $moduleName
     * @param string|false|null $viewName
     * @return bool whether module/view is one of LOCKED_VIEWS
     */
    public static function isLockedView( $moduleName, $viewName )
    {
        if ( !is_string( $moduleName ) || !is_string( $viewName ) || $moduleName === '' || $viewName === '' )
            return false;
        // the module name without case: on a case-insensitive filesystem /Settings/edit finds kernel/settings
        return in_array( strtolower( $moduleName ) . '/' . $viewName, self::LOCKED_VIEWS, true );
    }

    /**
     * @param eZINI|null $ini site.ini of the current siteaccess when null
     * @param string|null $sapi PHP_SAPI (or self::$sapi) when null; a parameter for the tests
     * @param array|null $server $_SERVER when null; a parameter for the tests
     * @return bool whether eZINI::save() and expIniEditor::save() have to refuse: switch enabled and not a
     *              command line script
     */
    public static function refusesWrite( $ini = null, $sapi = null, $server = null )
    {
        if ( $sapi === null )
            $sapi = self::$sapi !== null ? self::$sapi : PHP_SAPI;
        if ( ( $sapi === 'cli' || $sapi === 'phpdbg' ) && !self::isWebServerInCli( $server ) )
            return false;
        return self::isEnabled( $ini );
    }

    /**
     * Whether a process in the CLI SAPI is answering a web request: Velocity's workers are CLI processes,
     * and a request method with a Host header is what a web request has and a shell script does not
     * (the same test as eZExecution::isWebRequest()).
     *
     * @param array|null $server $_SERVER when null
     * @return bool
     */
    public static function isWebServerInCli( $server = null )
    {
        if ( $server === null )
        {
            if ( defined( 'QBIX_SERVER_VERSION' ) )
                return true;
            $server = isset( $_SERVER ) && is_array( $_SERVER ) ? $_SERVER : array();
        }
        if ( isset( $server['QBIX_WORKER'] ) || isset( $server['VELOCITY'] ) )
            return true;
        return !empty( $server['REQUEST_METHOD'] ) && !empty( $server['HTTP_HOST'] );
    }
}
