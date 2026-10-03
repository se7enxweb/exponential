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

        // The chain status comes from the last verification (the daily maintenance, `exp:audit verify`, or the
        // "Verify now" button below), not from walking every file on each view: a day's file can be megabytes.
        $verifyNow = \eZHTTPTool::instance()->hasPostVariable( 'AuditVerifyNowButton' );
        $options = array();
        if ( $verifyNow )
        {
            // as on the dashboard and the console: the password again (ReauthForManage) and, under
            // OnWriteFailure=refuse, a writable audit
            if ( class_exists( 'expAuditReauth' ) )
            {
                $form = \expAuditReauth::gate( $Params['Module'], 'AuditVerifyNowButton', 'audit/recent' . ( $channel !== null ? '/(channel)/' . $channel : '' ),
                                               \ezpI18n::tr( 'design/admin/audit', 'Verify now' ) );
                if ( $form !== null )
                    return $form;
            }
            if ( class_exists( 'expAuditGuard' ) && !\expAuditGuard::allows( 'system.audit.verify' ) )
                return \expAuditGuard::refusedResult( 'audit', 'recent' );
            $verifier = new \expAuditVerifier( $config['logDir'], new \expAuditKeys( $config ), $config['algorithm'] );
            foreach ( array_keys( $channels ) as $c )
                $verifier->verifyChannel( $c, $options );
            \expAudit::event( 'system.audit.verify', array( 'object' => array( 'type' => 'view', 'id' => 'audit/recent' ),
                                                            'after' => array( 'channels' => array_keys( $channels ) ) ) );
        }
        $state = \expAuditVerifier::loadState( $config['logDir'] );
        $chains = array();
        foreach ( array_keys( $channels ) as $c )
        {
            $v = isset( $state[$c] ) ? $state[$c] : null;
            $chains[] = array( 'channel' => $c, 'result' => $v ? $v['result'] : 'unverified',
                               'records' => $v ? $v['records'] : 0, 'files' => $v ? $v['files'] : 0,
                               'repairs' => $v ? $v['repairs'] : 0, 'breaks' => $v ? $v['breaks'] : 0,
                               'first_break' => $v ? $v['first_break'] : '',
                               'verified_at' => $v && !empty( $v['verified_at'] ) ? date( 'Y-m-d H:i:s', $v['verified_at'] ) : '',
                               'partial' => $v ? !empty( $v['partial'] ) : false );
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
