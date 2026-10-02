<?php
/**
 * The code of kernel/visual/toolbarlist.php, moved into a class (#207 stage 1). The file kernel/visual/toolbarlist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/visual/toolbarlist.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Visual
{

class Toolbarlist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();

        $currentSiteAccess = false;
        if ( $http->hasSessionVariable( 'eZTemplateAdminCurrentSiteAccess' ) )
            $currentSiteAccess = $http->sessionVariable( 'eZTemplateAdminCurrentSiteAccess' );

        $module = $Params['Module'];
        if ( $Params['SiteAccess'] )
            $currentSiteAccess = $Params['SiteAccess'];

        $ini = \eZINI::instance();
        $siteAccessList = $ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' );

        if ( $http->hasPostVariable( 'CurrentSiteAccess' ) )
            $currentSiteAccess = $http->postVariable( 'CurrentSiteAccess' );

        if ( !in_array( $currentSiteAccess, $siteAccessList ) )
            $currentSiteAccess = $siteAccessList[0];

        if ( $http->hasPostVariable( 'SelectCurrentSiteAccessButton' ) )
        {
            $http->setSessionVariable( 'eZTemplateAdminCurrentSiteAccess', $currentSiteAccess );
        }

        $toolbarIni = \eZINI::instance( "toolbar.ini", null, null, null, true );
        $toolbarIni->prependOverrideDir( "siteaccess/$currentSiteAccess", false, 'siteaccess' );
        $toolbarIni->loadCache();

        if ( $toolbarIni->hasVariable( "Toolbar", "AvailableToolBarArray" ) )
        {
            $toolbarArray =  $toolbarIni->variable( "Toolbar", "AvailableToolBarArray" );
        }
        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'toolbar_list', $toolbarArray );
        $tpl->setVariable( 'siteaccess_list', $siteAccessList );
        $tpl->setVariable( 'current_siteaccess', $currentSiteAccess );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:visual/toolbarlist.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'design/standard/toolbar', 'Toolbar management' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
