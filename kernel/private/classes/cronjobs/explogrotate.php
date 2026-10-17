<?php
/**
 * The code of cronjobs/explogrotate.php: the log rotation cronjob part ([CronjobPart-logs], in no default part, so it
 * runs only when a site adds it to a part it runs or calls php runcronjobs.php logs). Guide:
 * doc/features/6.0/log-rotation.md, "As a cronjob part".
 *
 * Each run, through expLogManager: rotates every log over its MaxSize (logviewer.ini [RotationSettings] MaxSize[<key>],
 * else site.ini [FileSettings] LogFileMaxSize), prunes the archives beyond Keep and MaxAgeDays, and compresses the plain
 * archives from .2 on of the logs whose Compress is enabled. Every change is recorded in the audit trail.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Explogrotate extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        if ( !class_exists( 'expLogManager' ) || !class_exists( 'expLogRotator' ) )
        {
            if ( !$quiet )
                $cli->output( 'Logs: the log classes are not in the autoload array yet' );
            return null;
        }
        $manager = new \expLogManager();
        $compress = array();
        foreach ( $manager->logs() as $key => $log )
            if ( $log['limits']['compress'] )
                $compress[] = $key;
        $report = array();
        foreach ( array( array( 'rotate', 'all', array( 'force' => false ) ), array( 'prune', 'all', array() ),
                         array( 'compress', $compress, array() ) ) as $job )
        {
            list( $op, $keys, $opts ) = $job;
            if ( $keys === array() )
                continue;
            $result = $manager->apply( $manager->plan( $keys, $op, $opts ) );
            $report[$op] = $result;
            foreach ( $result['steps'] as $step )
            {
                if ( $step['done'] && $op !== 'compress' && class_exists( 'expLogViewer' ) )
                    \expLogViewer::audit( $step['op'] === 'delete' ? 'delete' : 'rotate', array( 'id' => $step['key'], 'size' => (int)$step['bytes'] ),
                                          array( 'step' => $step['op'], 'file' => $step['from'], 'source' => 'cronjob logs' ) );
                if ( !$step['done'] && !in_array( $step['error'], array( 'missing', 'moved', 'skipped' ), true ) )
                    $cli->error( 'Logs: ' . $step['op'] . ' ' . $step['key'] . ' ' . $step['from'] . ': ' . $step['error'] );
                else if ( !$quiet && $step['done'] )
                    $cli->output( 'Logs: ' . $step['op'] . ' ' . $step['key'] . ' ' . $step['from'] );
            }
        }
        return $report;
    }
}

}
