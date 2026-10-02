<?php
/**
 * The code of kernel/url/view.php, moved into a class (#207 stage 1). The file kernel/url/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/url/view.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Url
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

        $Module = $Params['Module'];
        $urlID = $Params['ID'];

        if( \eZPreferences::value( 'admin_url_view_limit' ) )
        {
            switch( \eZPreferences::value( 'admin_url_view_limit' ) )
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

        $url = \eZURL::fetch( $urlID );
        if ( !$url )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $link = $url->attribute( 'url' );
        if ( preg_match("/^(http:)/i", $link ) or
             preg_match("/^(ftp:)/i", $link ) or
             preg_match("/^(https:)/i", $link ) or
             preg_match("/^(file:)/i", $link ) or
             preg_match("/^(mailto:)/i", $link ) )
        {
            // No changes
        }
        else
        {
            $domain = getenv( 'HTTP_HOST' );
            $protocol = \eZSys::serverProtocol();

            $preFix = $protocol . "://" . $domain;
            $preFix .= \eZSys::wwwDir();

            $link = preg_replace("/^\//", "", $link );
            $link = $preFix . "/" . $link;
        }

        $viewParameters = array( 'offset' => $offset, 'limit'  => $limit );
        $http = \eZHTTPTool::instance();
        $objectList = \eZURLObjectLink::fetchObjectVersionList( $urlID, $viewParameters );
        $urlViewCount= \eZURLObjectLink::fetchObjectVersionCount( $urlID );

        if ( $Module->isCurrentAction( 'EditObject' ) )
        {
            if ( $http->hasPostVariable( 'ObjectList' ) )
            {
                $versionID = $http->postVariable( 'ObjectList' );
                $version = \eZContentObjectVersion::fetch( $versionID );
                $contentObjectID = $version->attribute( 'contentobject_id' );
                $versionNr = $version->attribute( 'version' );
                $Module->redirect( 'content', 'edit', array( $contentObjectID, $versionNr ) );
            }
        }


        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'Module', $Module );
        $tpl->setVariable( 'url_object', $url );
        $tpl->setVariable( 'full_url', $link );
        $tpl->setVariable( 'object_list', $objectList );
        $tpl->setVariable( 'view_parameters', $viewParameters );
        $tpl->setVariable( 'url_view_count', $urlViewCount );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:url/view.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/url', 'URL' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/url', 'View' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
