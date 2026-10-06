<?php
/**
 * The code of kernel/role/copy.php, moved into a class (#207 stage 1). The file kernel/role/copy.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/role/copy.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Role
{

class Copy extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $roleID = $Params['RoleID'];

        $role = ( is_scalar( $roleID ) && ctype_digit( (string)$roleID ) ) ? \eZRole::fetch( (int)$roleID ) : null;
        if ( $role && (int)$role->attribute( 'version' ) === 0 )
        {
            $http = \eZHTTPTool::instance();
            // The copy is made by the form of the page (a POST, with the form token), not by opening the address:
            // a link or an image on another page made copies before.
            if ( $http->hasPostVariable( 'CancelCopyButton' ) )
                return $this->viewResult( null, $Module->redirectTo( '/role/view/' . (int)$role->attribute( 'id' ) ) );
            if ( $http->hasPostVariable( 'CopyRoleButton' ) )
            {
                $newRole = $role->copy();
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'edit', array( $newRole->attribute( 'id' ) ) ) );
            }
            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'role', $role );
            $tpl->setVariable( 'module', $Module );
            $summaries = \expRolePage::summaries( array( (int)$role->attribute( 'id' ) ) );
            $tpl->setVariable( 'role_summary', $summaries ? reset( $summaries ) : false );
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:role/copy.tpl' );
            $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/role', 'Role list' ), 'url' => 'role/list' ),
                                     array( 'text' => $role->attribute( 'name' ), 'url' => 'role/view/' . (int)$role->attribute( 'id' ) ),
                                     array( 'text' => \ezpI18n::tr( 'kernel/role', 'Copy' ), 'url' => false ) );
            return $this->viewResult( $Result, null );
        }
        else
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
