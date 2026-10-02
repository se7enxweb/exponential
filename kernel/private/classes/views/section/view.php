<?php
/**
 * The code of kernel/section/view.php, moved into a class (#207 stage 1). The file kernel/section/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/section/view.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Section
{

class View extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $SectionID = $Params["SectionID"];
        $Module = $Params['Module'];
        $Offset = $Params['Offset'];
        $viewParameters = array( 'offset' => $Offset );

        $section = \eZSection::fetch( $SectionID );

        if ( !$section )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( "view_parameters", $viewParameters );
        $tpl->setVariable( "section", $section );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:section/view.tpl" );
        $Result['path'] = array( array( 'url' => 'section/list',
                                        'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ),
                                 array( 'url' => false,
                                        'text' => $section->attribute('name') ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
