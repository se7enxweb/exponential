<?php
/**
 * The code of kernel/content/draft.php, moved into a class (#207 stage 1). The file kernel/content/draft.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/draft.php:
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

class Draft extends \Exponential\Runnable\ModuleView
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
        if ( !$user->isRegistered() )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        $userID = $user->id();

        if ( $http->hasPostVariable( 'RemoveButton' )  )
        {
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                $deleteIDArray = $http->postVariable( 'DeleteIDArray' );
                $db = \eZDB::instance();
                $db->begin();
                foreach ( $deleteIDArray as $deleteID )
                {
                    $version = \eZContentObjectVersion::fetch( $deleteID );
                    if ( $version instanceof \eZContentObjectVersion )
                    {
                        \eZDebug::writeNotice( $deleteID, "deleteID" );
                        $version->removeThis();
                    }
                }
                $db->commit();
            }
        }

        if ( $http->hasPostVariable( 'EmptyButton' )  )
        {
            $versions = \eZContentObjectVersion::fetchForUser( $userID );
            $db = \eZDB::instance();
            $db->begin();
            foreach ( $versions as $version )
            {
                $version->removeThis();
            }
            $db->commit();
        }

        $tpl = \eZTemplate::factory();

        $tpl->setVariable('view_parameters', $viewParameters );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/draft.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'My drafts' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
