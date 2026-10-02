<?php
/**
 * The audit/recent view: the latest 100 audit events of every channel (or of one, /(channel)/access) with name,
 * time, actor, object, result and request id, and a chain status line per channel (the verifier's result over
 * the live files). Read-only; needs the audit/read policy. Opening it is recorded (system.audit.read).
 * Guide: doc/bc/6.0/audit.md ("Stage 2 — built")
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Recent extends \Exponential\Runnable\ModuleView
{
    /** events shown */
    const LIMIT = 100;

    /** above this many bytes of live files, the chain line verifies today's files only */
    const FULL_VERIFY_BYTES = 33554432;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        // a process whose autoloads predate the audit classes (a Velocity worker before its restart): not
        // available, never a 500
        foreach ( array( 'expAudit', 'expAuditConfig', 'expAuditReader', 'expAuditVerifier', 'expAuditKeys' ) as $class )
            if ( !class_exists( $class ) )
                return $Params['Module']->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        $config = \expAuditConfig::get();
        $channel = isset( $Params['Channel'] ) && preg_match( '/^[a-z][a-z0-9_]{0,31}$/', (string)$Params['Channel'] ) ? (string)$Params['Channel'] : null;

        $reader = new \expAuditReader( $config['logDir'] );
        $channels = $reader->channels();
        $bytes = 0;
        foreach ( $channels as $info )
            $bytes += $info['bytes'];

        $verifier = new \expAuditVerifier( $config['logDir'], new \expAuditKeys( $config ), $config['algorithm'] );
        $options = $bytes > self::FULL_VERIFY_BYTES ? array( 'date' => gmdate( 'Y-m-d' ) ) : array();
        $chains = array();
        foreach ( array_keys( $channels ) as $c )
        {
            $v = $verifier->verifyChannel( $c, $options );
            $first = $v['breaks'] ? $v['breaks'][0] : null;
            $chains[] = array( 'channel' => $c, 'result' => $v['result'], 'records' => $v['records'], 'files' => $v['files'],
                               'repairs' => count( $v['repairs'] ), 'breaks' => count( $v['breaks'] ),
                               'first_break' => $first ? $first['file'] . ' line ' . $first['line'] . ': ' . $first['kind'] : '' );
        }

        $events = array();
        foreach ( $reader->latest( self::LIMIT, $channel ) as $r )
        {
            $ts = isset( $r['time'] ) ? strtotime( $r['time'] ) : false;
            $events[] = array(
                'id' => isset( $r['id'] ) ? $r['id'] : '',
                'name' => $r['name'],
                'channel' => isset( $r['channel'] ) ? $r['channel'] : '',
                'severity' => isset( $r['severity'] ) ? $r['severity'] : '',
                'time' => $ts ? date( 'Y-m-d H:i:s', $ts ) : '',
                'time_utc' => isset( $r['time'] ) ? $r['time'] : '',
                'actor' => \expAuditReader::actorText( $r ),
                'object' => \expAuditReader::objectText( $r ),
                'result' => isset( $r['result'] ) ? $r['result'] : '',
                'reason' => isset( $r['reason'] ) ? $r['reason'] : '',
                'request' => isset( $r['request']['id'] ) ? $r['request']['id'] : '',
                'engine' => isset( $r['request']['engine'] ) ? $r['request']['engine'] : '',
                'seq' => isset( $r['seq'] ) ? (int)$r['seq'] : 0,
            );
        }

        \expAudit::event( 'system.audit.read', array( 'object' => array( 'type' => 'view', 'id' => 'audit/recent' ),
                                                      'after' => array( 'channel' => $channel, 'count' => count( $events ) ) ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'events', $events );
        $tpl->setVariable( 'chains', $chains );
        $tpl->setVariable( 'channel', $channel === null ? '' : $channel );
        $tpl->setVariable( 'channel_names', array_keys( $channels ) );
        $tpl->setVariable( 'audit_enabled', $config['enabled'] );
        $tpl->setVariable( 'log_dir', \expAudit::relativePath( $config['logDir'] ) );
        $tpl->setVariable( 'verified_today_only', isset( $options['date'] ) );
        $tpl->setVariable( 'limit', self::LIMIT );
        $tpl->setVariable( 'request_id', \expAudit::requestId() );

        return array( 'content' => $tpl->fetch( 'design:audit/recent.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'design/admin/audit', 'Audit' ), 'url' => false ),
                                       array( 'text' => \ezpI18n::tr( 'design/admin/audit', 'Recent events' ), 'url' => false ) ) );
    }
}

}
