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

        $role = \eZRole::fetch( $roleID );
        if ( $role )
        {
            $newRole = $role->copy();
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'edit', array( $newRole->attribute( 'id' ) ) ) );
        }
        else
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
