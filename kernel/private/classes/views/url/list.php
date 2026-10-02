<?php
/**
 * The code of kernel/url/list.php, moved into a class (#207 stage 1). The file kernel/url/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/url/list.php:
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

class ListView extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $ViewMode = $Params['ViewMode'];

        if( \eZPreferences::value( 'admin_url_list_limit' ) )
        {
            switch( \eZPreferences::value( 'admin_url_list_limit' ) )
            {
                case '2': { $limit = 25; } break;
                case '3': { $limit = 50; } break;
                default:  { $limit = 10; } break;
            }
        }
        else
        {
            $limit = 10;
        }

        $offset = $Params['Offset'];
        if ( !is_numeric( $offset ) )
        {
            $offset = 0;
        }

        if( $ViewMode != 'all' && $ViewMode != 'invalid' && $ViewMode != 'valid')
        {
            $ViewMode = 'all';
        }

        if ( $Module->isCurrentAction( 'SetValid' ) )
        {
            $urlSelection = $Module->actionParameter( 'URLSelection' );
            \eZURL::setIsValid( $urlSelection, true );
        }
        else if ( $Module->isCurrentAction( 'SetInvalid' ) )
        {
            $urlSelection = $Module->actionParameter( 'URLSelection' );
            \eZURL::setIsValid( $urlSelection, false );
        }


        if( $ViewMode == 'all' )
        {
            $listParameters = array( 'is_valid'       => null,
                                     'offset'         => $offset,
                                     'limit'          => $limit,
                                     'only_published' => true );

            $countParameters = array( 'only_published' => true );
        }
        elseif( $ViewMode == 'valid' )
        {
            $listParameters = array( 'is_valid'       => true,
                                     'offset'         => $offset,
                                     'limit'          => $limit,
                                     'only_published' => true );

            $countParameters = array( 'is_valid' => true,
                                      'only_published' => true );
        }
        elseif( $ViewMode == 'invalid' )
        {
            $listParameters = array( 'is_valid'       => false,
                                     'offset'         => $offset,
                                     'limit'          => $limit,
                                     'only_published' => true );

            $countParameters = array( 'is_valid' => false,
                                      'only_published' => true );
        }

        $list = \eZURL::fetchList( $listParameters );
        $listCount = \eZURL::fetchListCount( $countParameters );

        $viewParameters = array( 'offset' => $offset, 'limit'  => $limit );


        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'url_list', $list );
        $tpl->setVariable( 'url_list_count', $listCount );
        $tpl->setVariable( 'view_mode', $ViewMode );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:url/list.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/url', 'URL' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/url', 'List' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
