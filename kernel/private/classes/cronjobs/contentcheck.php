<?php
/**
 * The code of cronjobs/contentcheck.php: builds the reference index of the content checks and keeps it in the cache
 * (expContentCheckReferences). Run it as the cronjob part contentcheck (php runcronjobs.php contentcheck) more often
 * than contentcheck.ini [UnreferencedSettings] IndexTTL when BuildOnRequest is false.
 * Guide: doc/features/6.0/content-checks.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Contentcheck extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $index = \expContentCheckReferences::build();
        \expContentCheckReferences::writeCache( $index );
        $cli->output( sprintf( 'Content checks: reference index of %d objects, %d nodes, %d remote ids, %d paths, %d collections built in %.2f s',
                               count( $index['objects'] ), count( $index['nodes'] ), count( $index['remote'] ), count( $index['aliases'] ),
                               count( $index['queries'] ), $index['seconds'] ) );
        if ( !empty( $index['failed'] ) )
            $cli->error( 'Content checks: these sources could not be read: ' . implode( ', ', $index['failed'] ) );
    }
}

}

?>
