<?php
/**
 * The code of cronjobs/internal_drafts_cleanup.php, moved into a class (#207 stage 1). The file cronjobs/internal_drafts_cleanup.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/internal_drafts_cleanup.php:
 *
 *
 * @description Remove internal drafts that exceed the configured age limit
 *
 * File containing the internal_drafts_cleanup.php.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class InternalDraftsCleanup extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $cli->output( "Cleaning up internal drafts..." );

        // Remove all temporary internal drafts
        // The settings, the lifetime and the removal are the service's, shared with the other drafts part
        if ( !class_exists( '\Exponential\Service\DraftsCleanup' ) )
            require_once __DIR__ . '/../services/draftscleanup.php';
        $processedCount = \Exponential\Service\DraftsCleanup::cleanup( \Exponential\Service\DraftsCleanup::INTERNAL_DRAFTS );

        if ( $processedCount !== null )
        {
            $cli->output( "Cleaned up " . $processedCount . " internal drafts" );
        }
        else
        {
            $cli->output( "Lifetime is not set for internal drafts (see your ini-settings, content.ini, VersionManagement section)." );
        }
    }
}

}
