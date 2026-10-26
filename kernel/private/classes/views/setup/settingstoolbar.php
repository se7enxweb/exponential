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

class Settingstoolbar extends \Exponential\Runnable\ModuleView
{
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
        // Without this the view ran its save on nothing and ended the request with a fatal error.
        $allSettingsList = $module->isCurrentAction( 'Set' ) ? $module->actionParameter( 'AllSettingsList' ) : null;
        if ( !is_array( $allSettingsList ) )
            return $this->viewResult( $this->page(), null );

        if ( $module->hasActionParameter( 'SelectedList' ) )
            $selectedList = $module->actionParameter( 'SelectedList' );
        else
            $selectedList=array();

        $siteAccess = $module->actionParameter( 'SiteAccess' );
        if ( !$siteAccess )
            $siteAccess = 'global_override';

        \eZPreferences::setValue( 'admin_quicksettings_siteaccess', $siteAccess );

        $iniFiles = array();

        foreach( $allSettingsList as $index => $setting )
        {
            $settingArray = explode( ';', $setting );

            if ( !array_key_exists( $settingArray[2], $iniFiles ) )
                $iniFiles[$settingArray[2]] = array();

            $iniFiles[$settingArray[2]][] = array ( $settingArray[0], $settingArray[1], in_array( $index, $selectedList ) );
        }
        unset( $setting );

        $iniPath = ( $siteAccess == "global_override" ) ? "settings/override" : "settings/siteaccess/$siteAccess";

        foreach( $iniFiles as $fileName => $settings )
        {
            $ini = new \eZINI( $fileName . '.append', $iniPath, null, null, null, true, true );
            $baseIni = \eZINI::instance( $fileName );

            foreach( $settings as $setting )
            {
                if ( $ini->hasVariable( $setting[0], $setting[1] ) )
                    $value = $ini->variable( $setting[0], $setting[1] );
                else
                    $value = $baseIni->variable( $setting[0], $setting[1] );

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
            if ( $siteAccess != "global_override" )
            {
                $ini = new \eZINI( $fileName . '.append', "settings/override", null, null, null, true, true );
                foreach( $settings as $setting )
                {
                    if ( $ini->hasVariable( $setting[0], $setting[1] ) )
                        $ini->removeSetting( $setting[0], $setting[1] );
                }
                if ( !$ini->save() )
                {
                    \eZDebug::writeError( "Can't save ini file: $iniPath/$fileName.append" );
                }

                unset($ini);
            }
        }

        // The saved values take effect on the next request only when the INI cache is rebuilt: an installation that
        // sets EZP_INI_FILEMTIME_CHECK to false (alpha, the demo sites) never notices the changed file, so the
        // setting seemed to be forgotten (after "Verify it's you" too, which replays this form unchanged).
        \eZCache::clearByTag( 'ini' );

        $uri = $http->postVariable( 'RedirectURI', $http->sessionVariable( 'LastAccessedModifyingURI', '/' ) );
        $module->redirectTo( $uri );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The page of setup/settingstoolbar opened by its address: the quick settings form (setup/quick_settings.tpl)
     * for the siteaccess last chosen there, which comes back to this page after Set.
     */
    protected function page()
    {
        $tpl = \eZTemplate::factory();
        $siteAccess = \eZPreferences::value( 'admin_quicksettings_siteaccess' );
        $tpl->setVariable( 'siteaccess', $siteAccess ? $siteAccess : 'global_override' );
        $tpl->setVariable( 'select_siteaccess', true );
        $tpl->setVariable( 'redirect_uri', '/setup/settingstoolbar' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/settingstoolbar.tpl' );
        $Result['path'] = array( array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/pagelayout', 'Quick settings' ) ) );
        return $Result;
    }
}

}
