<?php
/**
 * The code of kernel/url/edit.php, moved into a class (#207 stage 1). The file kernel/url/edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/url/edit.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Url
{

class Edit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $urlID = null;
        if ( isset( $Params["ID"] ) )
            $urlID = $Params["ID"];

        if ( is_numeric( $urlID ) )
        {
            $url = \eZURL::fetch( $urlID );
            if ( !$url )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
            }
        }
        else
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        $http = \eZHTTPTool::instance();
        if ( $Module->isCurrentAction( 'Cancel' ) )
        {
            $Module->redirectToView( 'list' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $Module->isCurrentAction( 'Store' ) )
        {
            if ( $http->hasPostVariable( 'link' ) )
            {
                $link = $http->postVariable( 'link' );
                $url->setAttribute( 'url', $link );
                $url->store();
                \eZURLObjectLink::clearCacheForObjectLink( $urlID );
            }
            $Module->redirectToView( 'list' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $Module->setTitle( "Edit link " . $url->attribute( "id" ) );

        // Template handling

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( "Module", $Module );
        $tpl->setVariable( "url", $url );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:url/edit.tpl" );
        $Result['path'] = array( array( 'url' => '/url/edit/',
                                        'text' => \ezpI18n::tr( 'kernel/url', 'URL edit' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
