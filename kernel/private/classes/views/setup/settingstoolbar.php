<?php
/**
 * The code of kernel/setup/settingstoolbar.php, moved into a class (#207 stage 1). The file kernel/setup/settingstoolbar.php is one call to it.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/settingstoolbar.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

/**
 * setup/settingstoolbar: the administration's Quick settings (debug output, template debug, SQL output ...).
 *
 * Every request ends in a page or a redirect, never in an error: opened by its address, or with a form that is not
 * one of the quick settings form, it shows the quick settings on a page of their own (with what was refused). A form
 * can only switch the settings site.ini [DebugSettings] QuickSettingsList[] offers, and only in settings/override or
 * the settings of a siteaccess the installation has; anything else in it (another setting, another file, a path in
 * the siteaccess, a value of the wrong type) is left out and named on the page. After a save the INI cache is rebuilt,
 * so the change takes effect also where EZP_INI_FILEMTIME_CHECK is false.
 */
class Settingstoolbar extends \Exponential\Runnable\ModuleView
{
    const GLOBAL_OVERRIDE = 'global_override';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];

        // Opened by its address (no form posted): the quick settings on a page of their own, to be set from there.
        if ( !$module->isCurrentAction( 'Set' ) )
            return $this->viewResult( $this->page(), null );

        $request = self::parseRequest( $module->actionParameter( 'AllSettingsList' ),
                                       $module->hasActionParameter( 'SelectedList' ) ? $module->actionParameter( 'SelectedList' ) : array(),
                                       $module->actionParameter( 'SiteAccess' ),
                                       self::allowedSettings( \eZINI::instance()->variable( 'DebugSettings', 'QuickSettingsList' ) ),
                                       self::knownSiteAccesses() );
        if ( !$request['settings'] )
            return $this->viewResult( $this->page( $request['problems'] ), null );

        $siteAccess = $request['siteaccess'];
        \eZPreferences::setValue( 'admin_quicksettings_siteaccess', $siteAccess );
        $iniPath = ( $siteAccess == self::GLOBAL_OVERRIDE ) ? "settings/override" : "settings/siteaccess/$siteAccess";

        foreach ( $request['settings'] as $fileName => $settings )
        {
            $ini = new \eZINI( $fileName . '.append', $iniPath, null, null, null, true, true );
            $baseIni = \eZINI::instance( $fileName );

            foreach ( $settings as $setting )
            {
                if ( $ini->hasVariable( $setting[0], $setting[1] ) )
                    $value = $ini->variable( $setting[0], $setting[1] );
                else
                    $value = $baseIni->hasVariable( $setting[0], $setting[1] ) ? $baseIni->variable( $setting[0], $setting[1] ) : 'disabled';

                if ( $value == 'true' || $value == 'false' )
                    $ini->setVariable( $setting[0], $setting[1], $setting[2] ? 'true' : 'false' );
                else
                    $ini->setVariable( $setting[0], $setting[1], $setting[2] ? 'enabled' : 'disabled' );
            }

            if ( !$ini->save() )
            {
                \eZDebug::writeError( "Can't save ini file: $iniPath/$fileName.append" );
            }

            unset( $baseIni );
            unset( $ini );

            // Remove variable from the global override
            if ( $siteAccess != self::GLOBAL_OVERRIDE )
            {
                $ini = new \eZINI( $fileName . '.append', "settings/override", null, null, null, true, true );
                foreach ( $settings as $setting )
                {
                    if ( $ini->hasVariable( $setting[0], $setting[1] ) )
                        $ini->removeSetting( $setting[0], $setting[1] );
                }
                if ( !$ini->save() )
                {
                    \eZDebug::writeError( "Can't save ini file: settings/override/$fileName.append" );
                }

                unset($ini);
            }
        }

        // The saved values take effect on the next request only when the INI cache is rebuilt: an installation that
        // sets EZP_INI_FILEMTIME_CHECK to false (alpha, the demo sites) never notices the changed file, so the
        // setting seemed to be forgotten (after "Verify it's you" too, which replays this form unchanged).
        \eZCache::clearByTag( 'ini' );

        if ( $request['problems'] )
            return $this->viewResult( $this->page( $request['problems'] ), null );

        $uri = self::redirectTarget( $http->hasPostVariable( 'RedirectURI' ) ? $http->postVariable( 'RedirectURI' ) : null,
                                     $http->sessionVariable( 'LastAccessedModifyingURI', '/' ) );
        $module->redirectTo( $uri );

        return $this->viewResult( null, null );
    }

    /**
     * The settings the quick settings may switch: "Section;Setting;file" keys of site.ini [DebugSettings]
     * QuickSettingsList[] (each entry "Section;Setting;file;Label"), a file name without path.
     *
     * @param mixed $list
     * @return array key => array( section, setting, file )
     */
    public static function allowedSettings( $list )
    {
        $allowed = array();
        foreach ( is_array( $list ) ? $list : array() as $entry )
        {
            $parts = explode( ';', (string)$entry );
            if ( count( $parts ) < 3 )
                continue;
            list( $section, $setting, $file ) = array_map( 'trim', array_slice( $parts, 0, 3 ) );
            if ( $section === '' || $setting === '' || !preg_match( '/^[A-Za-z0-9_.-]+\.ini$/', $file ) || strpos( $file, '..' ) !== false )
                continue;
            $allowed["$section;$setting;$file"] = array( $section, $setting, $file );
        }
        return $allowed;
    }

    /** The siteaccesses whose settings the quick settings may write: RelatedSiteAccessList[] and AvailableSiteAccessList[] */
    public static function knownSiteAccesses()
    {
        $ini = \eZINI::instance();
        $names = array();
        foreach ( array( 'RelatedSiteAccessList', 'AvailableSiteAccessList' ) as $name )
        {
            if ( $ini->hasVariable( 'SiteAccessSettings', $name ) && is_array( $ini->variable( 'SiteAccessSettings', $name ) ) )
                $names = array_merge( $names, $ini->variable( 'SiteAccessSettings', $name ) );
        }
        return array_values( array_unique( array_map( 'strval', $names ) ) );
    }

    /**
     * The posted form, checked: siteaccess (global_override or a known siteaccess), settings (file => list of
     * array( section, setting, on )) of the allowed settings only, and problems (what was left out, as text keys:
     * no-form, siteaccess, setting). Never throws, whatever the types of the values.
     *
     * @param mixed $allSettingsList AllSettingsList[]: "Section;Setting;file" entries
     * @param mixed $selectedList SelectedList[]: indexes of AllSettingsList[] that are switched on
     * @param mixed $siteAccess SiteAccess
     * @param array $allowed allowedSettings()
     * @param string[] $siteAccesses knownSiteAccesses()
     * @return array
     */
    public static function parseRequest( $allSettingsList, $selectedList, $siteAccess, array $allowed, array $siteAccesses )
    {
        $result = array( 'siteaccess' => self::GLOBAL_OVERRIDE, 'settings' => array(), 'problems' => array() );
        if ( !is_array( $allSettingsList ) || !$allSettingsList )
        {
            $result['problems'][] = 'no-form';
            return $result;
        }
        $siteAccess = is_string( $siteAccess ) ? trim( $siteAccess ) : '';
        if ( $siteAccess !== '' && $siteAccess !== self::GLOBAL_OVERRIDE )
        {
            if ( !in_array( $siteAccess, $siteAccesses, true ) || !preg_match( '/^[A-Za-z0-9_-]+$/', $siteAccess ) )
            {
                $result['problems'][] = 'siteaccess';
                return $result;
            }
            $result['siteaccess'] = $siteAccess;
        }
        $selected = array();
        foreach ( is_array( $selectedList ) ? $selectedList : array() as $index )
        {
            if ( is_scalar( $index ) && preg_match( '/^\d+$/', (string)$index ) )
                $selected[(int)$index] = true;
        }
        foreach ( $allSettingsList as $index => $entry )
        {
            $parts = is_string( $entry ) ? explode( ';', $entry ) : array();
            $key = count( $parts ) >= 3 ? implode( ';', array_map( 'trim', array_slice( $parts, 0, 3 ) ) ) : '';
            if ( !isset( $allowed[$key] ) )
            {
                if ( !in_array( 'setting', $result['problems'], true ) )
                    $result['problems'][] = 'setting';
                continue;
            }
            list( $section, $setting, $file ) = $allowed[$key];
            // SelectedList[] names entries by their index; an entry without a numeric index is never switched on
            $on = preg_match( '/^\d+$/', (string)$index ) === 1 && isset( $selected[(int)$index] );
            $result['settings'][substr( $file, 0, -4 )][] = array( $section, $setting, $on );
        }
        return $result;
    }

    /** Where Set goes back to: a path inside the site (RedirectURI of the form, else the last modifying page), never another host */
    public static function redirectTarget( $posted, $fallback )
    {
        foreach ( array( $posted, $fallback ) as $uri )
        {
            if ( is_string( $uri ) && $uri !== '' && $uri[0] === '/' && strpos( $uri, '//' ) !== 0 && strpos( $uri, '\\' ) === false
                 && !preg_match( '/[\x00-\x1f]/', $uri ) )
                return $uri;
        }
        return '/';
    }

    /**
     * The page of setup/settingstoolbar: the quick settings form (setup/quick_settings.tpl) for the siteaccess last
     * chosen there, which comes back to this page after Set, with what a form sent here had that was left out.
     *
     * @param string[] $problems parseRequest() problems
     */
    protected function page( array $problems = array() )
    {
        $tpl = \eZTemplate::factory();
        $siteAccess = \eZPreferences::value( 'admin_quicksettings_siteaccess' );
        if ( !is_string( $siteAccess ) || ( $siteAccess !== self::GLOBAL_OVERRIDE && !in_array( $siteAccess, self::knownSiteAccesses(), true ) ) )
            $siteAccess = self::GLOBAL_OVERRIDE;
        $tpl->setVariable( 'siteaccess', $siteAccess );
        $tpl->setVariable( 'select_siteaccess', true );
        $tpl->setVariable( 'redirect_uri', '/setup/settingstoolbar' );
        // a form that was not the quick settings form: what was left out goes to the debug log, the page shows the form
        foreach ( array_diff( $problems, array( 'no-form' ) ) as $problem )
            \eZDebug::writeNotice( $problem === 'siteaccess' ? 'Quick settings: the form named a siteaccess this installation does not have; nothing was changed'
                                                             : 'Quick settings: settings that are not quick settings were left out', __METHOD__ );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/settingstoolbar.tpl' );
        $Result['path'] = array( array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/pagelayout', 'Quick settings' ) ) );
        return $Result;
    }
}

}
