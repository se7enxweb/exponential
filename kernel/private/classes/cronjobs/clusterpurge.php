<?php
/**
 * The code of cronjobs/clusterpurge.php, moved into a class (#207 stage 1). The file cronjobs/clusterpurge.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/clusterpurge.php:
 *
 *
 * @description Purge expired cluster storage entries from the database backend
 *
 * Cluster files purge cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class Clusterpurge extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        if ( !\eZScriptClusterPurge::isRequired() )
        {
            $cli->error( "Your current cluster handler does not require file purge" );
            $script->shutdown( 1 );
        }

        $purgeHandler = new \eZScriptClusterPurge();
        $purgeHandler->optScopes = array( 'classattridentifiers',
                                          'classidentifiers',
                                          'content',
                                          'expirycache',
                                          'statelimitations',
                                          'template-block',
                                          'user-info-cache',
                                          'viewcache',
                                          'wildcard-cache-index',
                                          'image',
                                          'media',
                                          'binaryfile' );
        $purgeHandler->optExpiry = 30;
        $purgeHandler->run();
    }
}

}
