<?php
/**
 * The code of cronjobs/subtreeexpirycleanup.php, moved into a class (#207 stage 1). The file cronjobs/subtreeexpirycleanup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/subtreeexpirycleanup.php:
 *
 *
 * @description Clean up subtree expiry entries from the URL cache tables
 *
 * File containing the subtreeexpirycleanup.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class Subtreeexpirycleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        \eZSubtreeCache::removeAllExpiryCacheFromDisk();
    }
}

}
