<?php
/**
 * The code of kernel/setup/settingstoolbar.php, moved into a class (#207 stage 1). The file kernel/setup/settingstoolbar.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/settingstoolbar.php:
 *
 *
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

        $allSettingsList = $module->actionParameter( 'AllSettingsList' );

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

        $uri = $http->postVariable( 'RedirectURI', $http->sessionVariable( 'LastAccessedModifyingURI', '/' ) );
        $module->redirectTo( $uri );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
