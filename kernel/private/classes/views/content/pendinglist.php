<?php
/**
 * The code of kernel/content/pendinglist.php, moved into a class (#207 stage 1). The file kernel/content/pendinglist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/pendinglist.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Pendinglist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $Offset = $Params['Offset'];
        $viewParameters = array( 'offset' => $Offset );

        $user = \eZUser::currentUser();
        $userID = $user->id();


        $tpl = \eZTemplate::factory();
        $tpl->setVariable('view_parameters', $viewParameters );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/pendinglist.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'My pending list' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
