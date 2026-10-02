<?php
/**
 * The code of kernel/setup/toolbarlist.php, moved into a class (#207 stage 1). The file kernel/setup/toolbarlist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/toolbarlist.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

class Toolbarlist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];

        $visualModule = \eZModule::exists( 'visual' );
        if( $visualModule )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->forward( $visualModule, 'toolbarlist' ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
