<?php
/**
 * The code of kernel/content/move.php, moved into a class (#207 stage 1). The file kernel/content/move.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/move.php:
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

class Move extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $NodeID = $Params['NodeID'];

        $Module->setCurrentAction( 'MoveNodeRequest', 'action' );
        $Module->setActionParameter( 'NodeID', $NodeID, 'action' );
        return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->run( 'action', array( $NodeID ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
