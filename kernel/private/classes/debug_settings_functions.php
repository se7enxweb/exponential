<?php
/**
 * File containing eZUpdateDebugSettings() and eZUpdateDebugLogDirectory(), shared by every front controller
 *
 * index.php, index_rest.php, index_treemenu.php and the command line scripts
 * each used to declare their own eZUpdateDebugSettings(). A PHP process can
 * declare a function once, so this only worked while each process ran a
 * single front controller. A persistent worker (the Exponential application
 * server) runs them all, one request after another: whichever ran first kept
 * its variant, and the next front controller to load its own file died with
 * "Cannot redeclare function eZUpdateDebugSettings()".
 *
 * The function is now declared here and nowhere else. It is kept under its
 * old name because kernel code calls it (eZSiteAccess::change()). What it
 * does is chosen per request by the kernel that is running, through
 * eZDebugSettingsMode():
 *
 *  - 'web'  (index.php) reads [DebugSettings] from site.ini, as before.
 *  - 'rest' (index_rest.php) turns the debug output off and keeps AlwaysLog.
 *  - 'none' (index_treemenu.php, command line scripts) leaves eZDebug alone.
 *
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if ( !function_exists( 'eZDebugSettingsMode' ) )
{
    /**
     * Returns the eZUpdateDebugSettings() mode, and sets it when $mode is given.
     *
     * Each kernel sets its mode when it is constructed, so a worker that has
     * served another front controller before does not keep that one's mode.
     * Unset, the mode is 'web', which is what code that included
     * global_functions.php always got.
     *
     * @param string|null $mode 'web', 'rest' or 'none'
     * @return string
     */
    function eZDebugSettingsMode( $mode = null )
    {
        if ( $mode !== null )
        {
            $GLOBALS['eZDebugSettingsMode'] = (string)$mode;
        }
        return isset( $GLOBALS['eZDebugSettingsMode'] ) ? $GLOBALS['eZDebugSettingsMode'] : 'web';
    }
}

if ( !function_exists( 'eZDebugAlwaysLogLevels' ) )
{
    /**
     * Maps the AlwaysLog names in site.ini to the eZDebug levels.
     *
     * @param array|null $logList
     * @return array
     */
    function eZDebugAlwaysLogLevels( $logList )
    {
        $alwaysLog = array();
        foreach (
            array(
                'notice' => eZDebug::LEVEL_NOTICE,
                'warning' => eZDebug::LEVEL_WARNING,
                'error' => eZDebug::LEVEL_ERROR,
                'debug' => eZDebug::LEVEL_DEBUG,
                'strict' => eZDebug::LEVEL_STRICT
            ) as $name => $level )
        {
            $alwaysLog[$level] = is_array( $logList ) && in_array( $name, $logList );
        }
        return $alwaysLog;
    }
}

if ( !function_exists( 'eZUpdateDebugSettings' ) )
{
    /**
     * Reads the debug settings from site.ini and passes them to eZDebug, the
     * way the running front controller wants it (see eZDebugSettingsMode()).
     *
     * @param mixed $useDebug Ignored; accepted because command line scripts pass it.
     * @return null
     */
    function eZUpdateDebugSettings( $useDebug = null )
    {
        switch ( eZDebugSettingsMode() )
        {
            case 'none':
                return null;

            case 'rest':
                eZDebug::updateSettings(
                    array(
                        'debug-enabled' => false,
                        'always-log' => eZDebugAlwaysLogLevels( eZINI::instance()->variable( 'DebugSettings', 'AlwaysLog' ) ),
                    )
                );
                return null;

            default:
                $settings = array();
                list( $settings['debug-enabled'], $settings['debug-by-ip'], $settings['log-only'], $settings['debug-by-user'], $settings['debug-ip-list'], $logList, $settings['debug-user-list'] ) =
                    eZINI::instance()->variableMulti(
                        'DebugSettings',
                        array( 'DebugOutput', 'DebugByIP', 'DebugLogOnly', 'DebugByUser', 'DebugIPList', 'AlwaysLog', 'DebugUserIDList' ),
                        array( 'enabled', 'enabled', 'disabled', 'enabled' )
                    );
                $settings['always-log'] = eZDebugAlwaysLogLevels( $logList );
                eZDebug::updateSettings( $settings );
                return null;
        }
    }
}

if ( !function_exists( 'eZUpdateDebugLogDirectory' ) )
{
    /**
     * Points the log files of eZDebug and the default logs of eZLog at the log directory of the site when site.ini
     * [FileSettings] UseGlobalLogDir is disabled, and back at var/log otherwise.
     *
     * eZSiteAccess::change() calls it for every front controller and every script, whatever eZDebugSettingsMode()
     * says, so the logs of a site with a VarDir of its own (multi-site hosting) stay apart from those of the other
     * sites. Messages written before the siteaccess is known go to var/log.
     *
     * @return string|false The log directory used, false for var/log
     */
    function eZUpdateDebugLogDirectory()
    {
        $ini = eZINI::instance();
        $logDir = false;
        if ( $ini->hasVariable( 'FileSettings', 'UseGlobalLogDir' ) &&
             $ini->variable( 'FileSettings', 'UseGlobalLogDir' ) === 'disabled' )
        {
            $logDir = eZSys::logDirectory();
        }
        eZDebug::setLogDirectory( $logDir );
        return $logDir;
    }
}
