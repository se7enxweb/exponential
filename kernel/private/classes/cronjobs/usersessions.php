<?php
/**
 * @description Signed-in sessions ("Your devices"): ends the sessions not used for [Session] SessionTimeout, removes ended ones after [UserSessionSettings] RetentionDays
 *
 * The code of cronjobs/usersessions.php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Usersessions extends \Exponential\Runnable\CronjobPart
{
    /**
     * Retention of the signed-in sessions (expUserSessionTracker::cleanup()): a session not used for [Session]
     * SessionTimeout is ended as expired (the server has forgotten it too), an ended one is removed after
     * [UserSessionSettings] RetentionDays, and the rows of removed users go at once.
     */
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        if ( !\expUserSessionTracker::available() )
        {
            $cli->output( 'Skipped: the table expuser_session is not installed (update/common/scripts/6.0/createusersessiontable.php)' );
            return true;
        }
        $r = \expUserSessionTracker::cleanup();
        $cli->output( sprintf( 'Signed-in sessions: %d expired, %d ended sessions removed, %d of removed users removed',
                               $r['expired'], $r['removed'], $r['orphaned'] ) );
        return true;
    }
}

}
