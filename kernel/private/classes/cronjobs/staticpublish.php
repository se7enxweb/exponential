<?php
/**
 * The cronjob part staticpublish (cronjobs/staticpublish.php; [CronjobPart-staticpublish] in cronjob.ini): pushes the
 * static publishing targets whose runs could not be pushed (exp_static_run.push_state pending) again
 * (expStaticPublisher::retry(): the branch with every commit and the target's tags, never forced). Does nothing
 * while static publishing is switched off or no run waits.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Staticpublish extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        if ( !\expStaticTarget::enabled() )
            return 0;
        $db = \eZDB::instance();
        if ( !$db || !$db->isConnected() || \expStaticStatus::exists( $db ) !== true )
            return 0;
        $pushed = 0;
        foreach ( \expStaticTarget::all() as $id => $target )
        {
            if ( !\expStaticStatus::pending( $db, $id ) )
                continue;
            $report = ( new \expStaticPublisher( $target, array( 'db' => $db, 'root' => defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : (string)getcwd() ) ) )->retry();
            if ( $report['push_state'] === \expStaticRepository::PUSHED )
                $pushed++;
            if ( !$quiet )
            {
                $cli->output( sprintf( 'Static publishing: %s, %d run(s) waited, push %s', $id, count( $report['runs'] ), $report['push_state'] ) );
                foreach ( array_merge( $report['messages'], $report['errors'] ) as $message )
                    $cli->output( '  ' . $message );
            }
        }
        return $pushed;
    }
}

}
?>
