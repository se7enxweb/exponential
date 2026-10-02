<?php
/**
 * The fetch functions of the audit module (kernel/audit/function_definition.php; doc/bc/6.0/audit.md, "Template
 * operator and fetch"):
 *
 *   fetch( 'audit', 'events', hash( 'object', hash( 'type', 'node', 'id', $node.node_id ), 'limit', 10 ) )
 *   fetch( 'audit', 'event', hash( 'id', $id ) )
 *   fetch( 'audit', 'count', hash( 'name', 'access.session.login.failed', 'from', '2026-10-01' ) )
 *   fetch( 'audit', 'chain_status', hash( 'channel', 'access' ) )
 *
 * Filters are the console's (expAuditQuery::normalise(); object and target also as hash( type, id )). Each checks
 * audit/read with its Channel limitation for the current user and returns an empty result, never an error, when
 * the user may not read: a template cannot show audit records to a user without the policy. A use is recorded
 * as system.audit.read once per request and fetch name.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditFunctionCollection
{
    /** @var array fetch name => request id it was recorded for */
    protected static $recorded = array();

    /**
     * @param array $params the fetch parameters
     * @return array normalised filters
     */
    protected static function filters( array $params )
    {
        foreach ( array( 'object', 'target' ) as $k )
            if ( isset( $params[$k] ) && is_array( $params[$k] ) )
                $params[$k] = ( isset( $params[$k]['type'] ) ? $params[$k]['type'] : '' ) . ( isset( $params[$k]['id'] ) ? ':' . $params[$k]['id'] : '' );
        return expAuditQuery::normalise( $params );
    }

    /** @return bool the audit classes and the index are there and the user may read something */
    protected static function usable( &$channels )
    {
        if ( !class_exists( 'expAuditConsole' ) || !expAuditConsole::available() )
            return false;
        $channels = expAuditConsole::allowedChannels();
        return $channels === null || count( $channels ) > 0;
    }

    protected static function record( $fetch, array $params, $count )
    {
        $request = class_exists( 'expAudit' ) ? expAudit::requestId() : '';
        if ( isset( self::$recorded[$fetch] ) && self::$recorded[$fetch] === $request )
            return;
        self::$recorded[$fetch] = $request;
        expAuditConsole::recordRead( 'fetch:audit/' . $fetch, array_filter( $params, 'is_scalar' ), $count );
    }

    /**
     * fetch( 'audit', 'events', ... )
     *
     * @return array result => array of events (expAuditConsole::view() rows)
     */
    public static function fetchEvents( $channel = false, $name = false, $user = false, $login = false, $object = false, $target = false,
                                        $result = false, $severity = false, $request = false, $job = false, $run = false, $from = false,
                                        $to = false, $q = false, $offset = 0, $limit = 10 )
    {
        $channels = null;
        if ( !self::usable( $channels ) )
            return array( 'result' => array() );
        $params = array_filter( compact( 'channel', 'name', 'user', 'login', 'object', 'target', 'result', 'severity', 'request', 'job', 'run', 'from', 'to', 'q' ),
                                function ( $v ) { return $v !== false && $v !== null && $v !== ''; } );
        // several channels: 'access,system' (the dashboard block)
        if ( isset( $params['channel'] ) && strpos( (string)$params['channel'], ',' ) !== false )
        {
            $wanted = array_values( array_filter( array_map( 'trim', explode( ',', (string)$params['channel'] ) ), 'strlen' ) );
            $channels = $channels === null ? $wanted : array_values( array_intersect( $channels, $wanted ) );
            unset( $params['channel'] );
            if ( !$channels )
                return array( 'result' => array() );
        }
        $f = self::filters( $params );
        if ( isset( $f['channel'] ) && !expAuditConsole::channelAllowed( $f['channel'], $channels ) )
            return array( 'result' => array() );
        $page = expAuditConsole::page( $f, $channels, max( 0, (int)$offset ), min( 100, max( 1, (int)$limit ) ) );
        $out = array();
        foreach ( $page['rows'] as $row )
            $out[] = expAuditConsole::view( $row );
        self::record( 'events', $params, count( $out ) );
        return array( 'result' => $out );
    }

    /**
     * fetch( 'audit', 'count', ... )
     *
     * @return array result => int
     */
    public static function fetchCount( $channel = false, $name = false, $user = false, $login = false, $object = false, $target = false,
                                       $result = false, $severity = false, $request = false, $job = false, $run = false, $from = false,
                                       $to = false, $q = false )
    {
        $channels = null;
        if ( !self::usable( $channels ) )
            return array( 'result' => 0 );
        $params = array_filter( compact( 'channel', 'name', 'user', 'login', 'object', 'target', 'result', 'severity', 'request', 'job', 'run', 'from', 'to', 'q' ),
                                function ( $v ) { return $v !== false && $v !== null && $v !== ''; } );
        $f = self::filters( $params );
        if ( isset( $f['channel'] ) && !expAuditConsole::channelAllowed( $f['channel'], $channels ) )
            return array( 'result' => 0 );
        $page = expAuditConsole::page( $f, $channels, 0, 1 );
        self::record( 'count', $params, (int)$page['total'] );
        return array( 'result' => (int)$page['total'] );
    }

    /**
     * fetch( 'audit', 'event', hash( 'id', ... ) )
     *
     * @return array result => the event, or false
     */
    public static function fetchEvent( $id )
    {
        $channels = null;
        if ( !self::usable( $channels ) || !preg_match( '/^[0-9A-HJKMNP-TV-Z]{26}$/', (string)$id ) )
            return array( 'result' => false );
        $row = expAuditConsole::indexUsable() ? ( new expAuditQuery() )->byId( $id ) : null;
        if ( !$row )
        {
            $found = ( new expAuditReader( expAuditConfig::get()['logDir'] ) )->find( $id );
            $rec = $found ? json_decode( $found['raw'], true ) : null;
            $row = is_array( $rec ) ? expAuditIndexRow::fromRecord( $rec, $found['raw'], $found['file'] ) : null;
        }
        if ( !$row || !expAuditConsole::channelAllowed( $row['channel'], $channels ) )
            return array( 'result' => false );
        self::record( 'event', array( 'id' => $id ), 1 );
        return array( 'result' => expAuditConsole::view( $row ) );
    }

    /**
     * fetch( 'audit', 'chain_status', hash( 'channel', ... ) )
     *
     * @return array result => list of channel, result, files, first_break (one channel or every readable one)
     */
    public static function fetchChainStatus( $channel = false, $verify = true )
    {
        $channels = null;
        if ( !self::usable( $channels ) )
            return array( 'result' => array() );
        // stored states only: a template never makes a page walk the files
        $states = expAuditConsole::chainStates( $channels, false );
        if ( $channel !== false && $channel !== '' )
            $states = array_values( array_filter( $states, function ( $s ) use ( $channel ) { return $s['channel'] === $channel; } ) );
        self::record( 'chain_status', array( 'channel' => $channel ?: null ), count( $states ) );
        return array( 'result' => $states );
    }

    /**
     * fetch( 'audit', 'can_read', hash( 'channel', 'content' ) ): whether the current user may read audit records
     * (of that channel; any channel without one).
     *
     * @param string|false $channel
     * @return array result => bool
     */
    public static function fetchCanRead( $channel = false )
    {
        if ( !class_exists( 'expAuditConsole' ) )
            return array( 'result' => false );
        $allowed = expAuditConsole::allowedChannels();
        if ( $channel !== false && $channel !== '' && $channel !== null )
            return array( 'result' => expAuditConsole::channelAllowed( (string)$channel, $allowed ) );
        return array( 'result' => $allowed === null || count( $allowed ) > 0 );
    }
}
