<?php
/**
 * The code of kernel/content/bookmark.php, moved into a class (#207 stage 1). The file kernel/content/bookmark.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/bookmark.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Bookmark extends \Exponential\Runnable\ModuleView
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

        if ( $Module->isCurrentAction( 'Remove' )  )
        {
            if ( $Module->hasActionParameter( 'DeleteIDArray' ) )
            {
                $deleteIDArray = $Module->actionParameter( 'DeleteIDArray' );

                foreach ( $deleteIDArray as $deleteID )
                {
                    $bookmark = \eZContentBrowseBookmark::fetch( $deleteID );
                    if ( $bookmark === null )
                        continue;
                    if ( $bookmark->attribute( 'user_id' ) == $userID )
                        $bookmark->remove();
                }
            }
            if ( $http->hasPostVariable( 'NeedRedirectBack' ) )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $http->postVariable( 'RedirectURI', $http->sessionVariable( 'LastAccessesURI', '/' ) ) ) );
            }
        }
        else if ( $Module->isCurrentAction( 'Add' )  )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  \eZContentBrowse::browse( array( 'action_name' => 'AddBookmark',
                                                   'description_template' => 'design:content/browse_bookmark.tpl',
                                                   'from_page' => "/content/bookmark" ),
                                            $Module ) );
        }
        else if ( $Module->isCurrentAction( 'AddBookmark' )  )
        {
            $nodeList = \eZContentBrowse::result( 'AddBookmark' );
            if ( $nodeList )
            {
                $db = \eZDB::instance();
                $db->begin();
                foreach ( $nodeList as $nodeID )
                {
                    $node = \eZContentObjectTreeNode::fetch( $nodeID );
                    if ( $node )
                    {
                        $nodeName = $node->attribute( 'name' );
                        \eZContentBrowseBookmark::createNew( $userID, $nodeID, $nodeName );
                    }
                }
                $db->commit();
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable('view_parameters', $viewParameters );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/bookmark.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'My bookmarks' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
