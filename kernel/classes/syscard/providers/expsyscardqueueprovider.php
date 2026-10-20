<?php
/**
 * File containing the expSysCardQueueProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * exp:info: the job queue of the expqueue extension, when it is active: its service, how many workers are alive and
 * the last action on it (expQueueService::statusSummary(), which reads its own files and never asks systemd).
 */
class expSysCardQueueProvider extends expSysCardBaseProvider
{
    protected $id = 'queue';
    protected $section = 'queue';
    protected $modes = array( expSysCardContext::MODE_INFO );
    protected $priority = 10;
    protected $budget = 1.0;

    public function collect( expSysCardContext $context )
    {
        $summary = $context->get( 'queue.summary', function () {
            return class_exists( 'expQueueService' ) && method_exists( 'expQueueService', 'statusSummary' ) ? expQueueService::statusSummary() : null;
        } );
        if ( !is_array( $summary ) )
            return array( $this->fact( 'state', 'Queue', 'expqueue not active' ) );
        $workers = isset( $summary['workers'] ) ? (int)$summary['workers'] : 0;
        $installed = !empty( $summary['installed'] );
        $facts = array( $this->fact( 'service', 'Service', ( isset( $summary['unit'] ) ? $summary['unit'] . ', ' : '' ) . ( $installed ? 'installed' : 'not installed' ) ),
                        $this->fact( 'workers', 'Workers', $workers . ' alive', $installed && $workers === 0 ? expSysCardFact::WARN : expSysCardFact::INFO,
                                     $installed && $workers === 0 ? 'The queue service is installed but no worker runs: queued jobs wait (./console ext:expqueue:status).' : '' ) );
        if ( !empty( $summary['last_action'] ) )
            $facts[] = $this->fact( 'last', 'Last action', $summary['last_action'] . ( !empty( $summary['at'] ) ? ', ' . self::ago( (int)$summary['at'], $context->now ) : '' ) );
        return $facts;
    }
}
