<?php
/**
 * The cronjob part audit (cronjobs/audit.php; [CronjobPart-audit] and the frequent group in cronjob.ini): the
 * audit's scheduled work, expAuditMaintenance::run(). Every run: the incremental index, the sink spools (webhook,
 * mail, network syslog) and the alert rules' cronjob pass; once a day after [AuditRotationSettings] RotateAfter:
 * rotation by day, verification, archiving, retention, pseudonymisation of the index and the checkpoint
 * (doc/bc/6.0/audit.md, "Rotation, archives and retention (Q8)").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Audit extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        if ( !class_exists( 'expAuditMaintenance' ) || !class_exists( 'expAudit' ) )
        {
            if ( !$quiet )
                $cli->output( 'Audit: the audit classes are not in the autoload array yet' );
            return null;
        }
        $maintenance = new \expAuditMaintenance( function ( $line ) use ( $cli, $quiet ) {
            if ( !$quiet )
                $cli->output( $line );
        } );
        $report = $maintenance->run();
        if ( !empty( $report['error'] ) )
            $cli->error( 'Audit: ' . $report['error'] );
        return $report;
    }
}

}
