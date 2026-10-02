<?php
/**
 * The code of kernel/user/logout.php, moved into a class (#207 stage 1). The file kernel/user/logout.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/logout.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\User
{

class Logout extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();

        $user = \eZUser::instance();

        // Remove all temporary drafts
        \eZContentObject::cleanupAllInternalDrafts( $user->attribute( 'contentobject_id' ) );

        $user->logoutCurrent();

        $http->setSessionVariable( 'force_logout', 1 );

        $ini = \eZINI::instance();
        if ( $ini->variable( 'UserSettings', 'RedirectOnLogoutWithLastAccessURI' ) == 'enabled' && $http->hasSessionVariable( 'LastAccessesURI' ))
        {
            $redirectURL = $http->sessionVariable( "LastAccessesURI" );
        }
        else
        {
            $redirectURL = $http->postVariable( 'RedirectURI', $ini->variable( 'UserSettings', 'LogoutRedirect' ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( $redirectURL ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
