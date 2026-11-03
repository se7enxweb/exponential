<?php
/**
 * The code of cronjobs/updateviewcount.php, moved into a class (#207 stage 1). The file cronjobs/updateviewcount.php is one call to it.
 * Guide: doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of cronjobs/updateviewcount.php:
 *
 *
 * @description Count the page views of the web server's access logs into the view counter (ezview_counter)
 *
 * File containing the updateviewcount.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

/**
 * The view counter as a cronjob part: the same work as ./console exp:viewcount:update without options. Which logs
 * and siteaccesses it reads is set in logfile.ini (doc/eng-us/guides/view-count-statistics.md): without
 * [ViewCountSettings] SiteAccessList[] or AllSiteAccesses, the [AccessLogFileSettings] of the siteaccess the cronjob
 * runs for, as before.
 */
class Updateviewcount extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        set_time_limit( 0 );

        $cli->output( "Update content view count..." );
        \eZDB::instance()->setIsSQLOutputEnabled( false );

        $config = \expViewCountConfiguration::fromINI();
        $report = ( new \expViewCountUpdater( $config ) )->run();
        foreach ( \expViewCountUpdater::lines( $report ) as $line )
        {
            if ( strpos( $line, 'Error: ' ) === 0 )
                $cli->error( $line );
            elseif ( strpos( $line, 'Warning: ' ) === 0 )
                $cli->warning( $line );
            else
                $cli->output( $line );
        }
        $cli->output( $report['exit_code'] === \expViewCountUpdater::EXIT_OK ? "View count has been updated." : "View count was not fully updated (see above)." );
        return $report;
    }
}

}
