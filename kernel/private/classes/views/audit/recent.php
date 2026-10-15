<?php
/**
 * The audit/recent view: the latest 100 audit events of every channel the user may read (or of one,
 * /(channel)/access) with name, time, actor, object, result and request id, and a chain status line per channel
 * (the verifier's result over the live files). Needs the audit/read policy, whose Channel limitation narrows the
 * channels; "Verify now" needs audit/manage. Opening it is recorded (system.audit.read).
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

        // the Channel limitation of audit/read narrows the channels, as on the console
        $allowed = class_exists( 'expAuditConsole' ) ? \expAuditConsole::allowedChannels() : null;
        if ( $channel !== null && !self::channelAllowed( $channel, $allowed ) )
            return $Params['Module']->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $reader = new \expAuditReader( $config['logDir'] );
        $channels = self::allowedChannelInfo( $reader->channels(), $allowed );
        $bytes = 0;
        foreach ( $channels as $info )
            $bytes += $info['bytes'];

        // The chain status comes from the last verification (the daily maintenance, `exp:audit verify`, or the
        // "Verify now" button below), not from walking every file on each view: a day's file can be megabytes.
        $verifyNow = \eZHTTPTool::instance()->hasPostVariable( 'AuditVerifyNowButton' );
        $options = array();
        if ( $verifyNow )
        {
            // verifying is audit/manage, as on the console
            if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::canManage() )
                return $Params['Module']->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
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
        foreach ( self::latestAllowed( $reader, self::LIMIT, $channel, $allowed ) as $r )
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
        // the Verify now button only for those who may press it (audit/manage)
        $tpl->setVariable( 'can_manage', class_exists( 'expAuditConsole' ) && \expAuditConsole::canManage() );
        $tpl->setVariable( 'request_id', \expAudit::requestId() );
        // the links of the extensions' sources (audit.ini [AuditSourceSettings] Sources[]); none without one
        $tpl->setVariable( 'audit_sources', class_exists( 'expAuditConsole' ) ? \expAuditConsole::sourceLinks( 'audit/recent', $channel === null ? array() : array( 'channel' => $channel ), $allowed ) : array() );

        return array( 'content' => $tpl->fetch( 'design:audit/recent.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'design/admin/audit', 'Audit' ), 'url' => false ),
                                       array( 'text' => \ezpI18n::tr( 'design/admin/audit', 'Recent events' ), 'url' => false ) ) );
    }

    /**
     * Whether $channel may be read with the channels $allowed (null: every channel).
     *
     * @param string $channel
     * @param string[]|null $allowed expAuditConsole::allowedChannels()
     * @return bool
     */
    public static function channelAllowed( $channel, $allowed )
    {
        return $allowed === null || in_array( (string)$channel, (array)$allowed, true );
    }

    /**
     * The channels of expAuditReader::channels() the user may read.
     *
     * @param array $channels channel => info
     * @param string[]|null $allowed
     * @return array
     */
    public static function allowedChannelInfo( array $channels, $allowed )
    {
        if ( $allowed === null )
            return $channels;
        $result = array();
        foreach ( $channels as $c => $info )
            if ( self::channelAllowed( $c, $allowed ) )
                $result[$c] = $info;
        return $result;
    }

    /**
     * The latest $limit records of $channel (null: of every channel) that the channels $allowed let the user read,
     * newest first. A limited user gets the latest of the readable channels, not fewer.
     *
     * @param \expAuditReader $reader
     * @param int $limit
     * @param string|null $channel
     * @param string[]|null $allowed
     * @return array
     */
    public static function latestAllowed( $reader, $limit, $channel, $allowed )
    {
        if ( $allowed === null )
            return $reader->latest( $limit, $channel );
        if ( $channel !== null )
            return self::channelAllowed( $channel, $allowed ) ? $reader->latest( $limit, $channel ) : array();
        $all = array();
        foreach ( array_keys( self::allowedChannelInfo( $reader->channels(), $allowed ) ) as $c )
            foreach ( $reader->latest( $limit, $c ) as $r )
                $all[] = $r;
        usort( $all, function ( $a, $b ) {
            $t = strcmp( isset( $b['time'] ) ? $b['time'] : '', isset( $a['time'] ) ? $a['time'] : '' );
            return $t !== 0 ? $t : strcmp( isset( $b['id'] ) ? $b['id'] : '', isset( $a['id'] ) ? $a['id'] : '' );
        } );
        return array_slice( $all, 0, max( 1, (int)$limit ) );
    }
}

}
