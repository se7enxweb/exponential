<?php
/**
 * The cronjob part imagealiasbacklog (cronjobs/imagealiasbacklog.php, frequent group): the safety net of the image
 * alias backlog (expImageAliasBacklog, settings/image.ini [AliasSettings] Backlog). Requests make the backlog right
 * after their answers; what is still there when this part runs (the server got no request since, or a process died
 * while it worked on an entry) is made here, waiting for a conversion slot as a job does. Each alias as the
 * siteaccess the page was shown in defines it. Nothing in render mode or with an empty backlog.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Imagealiasbacklog extends \Exponential\Runnable\CronjobPart
{
    /** Seconds this part works at most, so the next run of the frequent group is not held up */
    const MAX_SECONDS = 120;

    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        if ( !class_exists( '\expImageAliasBacklog' ) || !\expImageAliasThrottle::applies() )
            return 0;
        $recovered = \expImageAliasBacklog::recoverStale();
        $waiting = \expImageAliasBacklog::count();
        if ( $waiting === 0 )
            return 0;
        $worked = \expImageAliasBacklog::drain( self::MAX_SECONDS, \expImageAliasSettings::lockTimeout() );
        if ( !$quiet )
            $cli->output( "Image alias backlog: $waiting waiting" . ( $recovered ? " ($recovered given back)" : '' ) . ", $worked made, "
                          . \expImageAliasBacklog::count() . ' left' );
        return $worked;
    }
}

}
?>
