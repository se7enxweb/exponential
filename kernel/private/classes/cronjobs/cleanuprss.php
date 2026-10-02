<?php
/**
 * The code of cronjobs/cleanuprss.php, moved into a class (#207 stage 1). The file cronjobs/cleanuprss.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/cleanuprss.php:
 *
 *
 * @description Trim the content the RSS import has created, keeping the newest items of each feed
 *
 * File containing the cleanuprss.php cronjob.
 *
 * Does nothing until content.ini [RSSImportCleanupSettings] names the classes
 * it may remove, so it is safe to schedule before it has been configured.
 *
 * Ported from the bccleanuprss extension by Brookins Consulting.
 *
 * @copyright Copyright (C) 1999 - 2011 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class Cleanuprss extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $cleanup = new \expCleanupRSS();

        // Said once, rather than by every run of a cronjob that has nothing to do.
        if ( !$cleanup->isEnabled() )
        {
            \eZDebug::writeNotice( 'RSS import cleanup is not running: ' . $cleanup->reason(), $this->scriptFile() );
            return;
        }

        $cli->output( 'Cleaning up imported RSS content...' );

        $cleanup->cleanup();

        $counts = $cleanup->counts();

        $cli->output( sprintf( 'Done. %d item(s) removed across %d feed(s).',
                               $counts['removed'], $counts['feeds'] ) );
    }
}

}
