<?php
/**
 * ezjscore/call/expaudit::<service> - the audit system, read only (policy audit/read, narrowed to the channels
 * of its Channel limitation): events, one event, related events, figures for the dashboard. Every use is recorded
 * as system.audit.read like the console's views. Nothing here changes or verifies anything.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expAuditServices extends expServiceBase
{
    public static $services = array(
        'available' => array( 'summary' => 'Whether the audit classes are loaded, whether audit is on and the index usable', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'available, enabled, index' ),
        'channels' => array( 'summary' => 'The channels the user may read, with record counts of today', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of channel, today' ),
        'recent' => array( 'summary' => 'The latest events, newest first, optionally of one channel', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'channel' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of events' ),
        'search' => array( 'summary' => 'Events by text, name pattern (access.*), login, result and time range', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'q' => 'string', 'name' => 'string', 'login' => 'string', 'result' => 'string', 'from' => 'string', 'to' => 'string', 'limit' => 'int', 'offset' => 'int' ),
            'returns' => 'paged list of events' ),
        'event' => array( 'summary' => 'One event in full by id', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'id' => 'string' ), 'returns' => 'event with its record' ),
        'related' => array( 'summary' => 'The events of the same request as an event', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'id' => 'string', 'limit' => 'int' ), 'returns' => 'list of events' ),
        'byrequest' => array( 'summary' => 'The events of one request id', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'request' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of events' ),
        'bylogin' => array( 'summary' => 'The events of one login', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'login' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of events' ),
        'byobject' => array( 'summary' => 'The events about one object (type or type:id, node:275)', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'object' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of events' ),
        'refused' => array( 'summary' => 'The latest refused events', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of events' ),
        'failed' => array( 'summary' => 'The latest failed events', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of events' ),
        'names' => array( 'summary' => 'The event names in the index (the name filter suggestions)', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of names' ),
        'volume' => array( 'summary' => 'Events of today and of the last 7 days per channel', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of channel, today, week' ),
        'perday' => array( 'summary' => 'Events per local day for the last N days (at most 30)', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'days' => 'int' ), 'returns' => 'list of day, count' ),
        'results' => array( 'summary' => 'Counts of success, refused and failed for today and the week', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'today, week' ),
        'topactors' => array( 'summary' => 'The most active logins of today', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of login, count' ),
        'failedlogins' => array( 'summary' => 'Failed logins of the last 24 hours: total and by address', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'total, by_ip' ),
        'chains' => array( 'summary' => 'The hash chain state of each channel as last verified (nothing is verified here)', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of channel, result, files, verified_at' ),
        'summary' => array( 'summary' => 'The dashboard figures in one answer: volume, results, security, chains', 'access' => array( 'audit', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'volume, results, failed_logins, chains' ),
    );

    protected static function need()
    {
        if ( !class_exists( 'expAuditConsole' ) || !expAuditConsole::available() )
            throw new expServiceException( 'The audit system is not available in this process', 404 );
    }

    /** @return string[]|null the channels the user may read (null: all) */
    protected static function allowed()
    {
        self::need();
        $allowed = expAuditConsole::allowedChannels();
        if ( is_array( $allowed ) && !$allowed )
            throw new expServiceException( 'No access to any audit channel', 403 );
        return $allowed;
    }

    protected static function todayMs()
    {
        return mktime( 0, 0, 0 ) * 1000;
    }

    /** Filters through the console's normaliser; invalid ones are a 400. */
    protected static function filters( array $raw )
    {
        $f = expAuditQuery::normalise( array_filter( $raw, function ( $v ) { return $v !== null && $v !== ''; } ), expAuditConfig::get() );
        if ( !empty( $f['invalid'] ) )
            throw new expServiceException( 'Invalid filter: ' . implode( '; ', $f['invalid'] ), 400 );
        return $f;
    }

    protected static function events( array $f, $allowed, $limit, $offset, $view )
    {
        $page = expAuditConsole::page( $f, $allowed, $offset, $limit );
        $items = array();
        foreach ( $page['rows'] as $row )
            $items[] = self::exportEvent( $row );
        expAuditConsole::recordRead( 'audit/service/' . $view, $f, count( $items ) );
        $res = self::page( $items, $page['total'], $offset, $limit );
        $res['meta']['source'] = $page['source'];
        return $res;
    }

    protected static function exportEvent( array $row )
    {
        $v = expAuditConsole::view( $row );
        return array( 'id' => $v['id'], 'time' => $v['time_utc'], 'name' => $v['name'], 'channel' => $v['channel'], 'severity' => $v['severity'],
                      'actor' => $v['actor'], 'login' => $v['login'], 'user_id' => $v['user_id'], 'ip' => $v['ip'],
                      'object' => $v['object'], 'object_type' => $v['object_type'], 'object_id' => $v['object_id'],
                      'target' => $v['target'], 'result' => $v['result'], 'reason' => $v['reason'], 'request' => $v['request'],
                      'engine' => $v['engine'], 'siteaccess' => $v['siteaccess'], 'module_view' => $v['module_view'] );
    }

    public static function available( $args )
    {
        self::guard( __FUNCTION__ );
        $has = class_exists( 'expAuditConsole' ) && expAuditConsole::available();
        return self::ok( array( 'available' => $has, 'enabled' => $has && class_exists( 'expAudit' ) ? (bool)expAudit::isEnabled() : false,
                                'index' => $has ? (bool)expAuditConsole::indexUsable() : false ) );
    }

    public static function channels( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        $reader = new expAuditReader( expAuditConfig::get()['logDir'] );
        $list = array();
        foreach ( $reader->channels() as $name => $info )
            if ( expAuditConsole::channelAllowed( $name, $allowed ) )
                $list[] = array( 'channel' => $name, 'files' => count( $info['files'] ), 'bytes' => (int)$info['bytes'] );
        return self::ok( $list );
    }

    public static function recent( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        return self::events( self::filters( array( 'channel' => self::arg( $args, 0, 'string', '' ) ) ), $allowed, $limit, $offset, 'recent' );
    }

    public static function search( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        list( $limit, $offset ) = self::paging( $args, 6, 7 );
        $f = self::filters( array( 'q' => self::arg( $args, 0, 'string', '' ), 'name' => self::arg( $args, 1, 'string', '' ),
                                   'login' => self::arg( $args, 2, 'string', '' ), 'result' => self::arg( $args, 3, 'string', '' ),
                                   'from' => self::arg( $args, 4, 'string', '' ), 'to' => self::arg( $args, 5, 'string', '' ) ) );
        return self::events( $f, $allowed, $limit, $offset, 'search' );
    }

    /** One event row by id: the index first, then the log files (an event not indexed yet); null when not readable. */
    protected static function rowOf( $id, $allowed )
    {
        if ( !preg_match( '/^[0-9A-HJKMNP-TV-Z]{26}$/', $id ) )
            throw new expServiceException( 'The event id is a 26 character identifier', 400 );
        $row = ( new expAuditQuery() )->byId( $id );
        if ( !$row )
        {
            $rec = ( new expAuditReader( expAuditConfig::get()['logDir'] ) )->find( $id );
            if ( $rec )
            {
                $file = isset( $rec['file'] ) ? $rec['file'] : '';
                $raw = isset( $rec['raw'] ) ? $rec['raw'] : json_encode( $rec );
                unset( $rec['file'], $rec['line'], $rec['raw'] );
                $row = expAuditIndexRow::fromRecord( $rec, $raw, $file );
            }
        }
        return $row && expAuditConsole::channelAllowed( $row['channel'], $allowed ) ? $row : null;
    }

    public static function event( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        $row = self::rowOf( self::arg( $args, 0, 'string' ), $allowed );
        if ( !$row )
            throw new expServiceException( 'No such event', 404 );
        $out = self::exportEvent( $row );
        $rec = json_decode( (string)$row['record'], true );
        $out['record'] = is_array( $rec ) ? $rec : null;
        expAuditConsole::recordRead( 'audit/service/event', array(), 1 );
        return self::ok( $out );
    }

    public static function related( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        list( $limit ) = self::paging( $args, 1, 99 );
        $row = self::rowOf( self::arg( $args, 0, 'string' ), $allowed );
        if ( !$row )
            throw new expServiceException( 'No such event', 404 );
        $list = array();
        if ( (string)$row['request_id'] !== '' )
            foreach ( ( new expAuditQuery() )->fetch( array( 'request' => $row['request_id'] ), $allowed, 0, $limit ) as $r )
                $list[] = self::exportEvent( $r );
        return self::ok( $list, array( 'request' => $row['request_id'] ) );
    }

    public static function byrequest( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        return self::events( self::filters( array( 'request' => self::arg( $args, 0, 'string' ) ) ), $allowed, $limit, $offset, 'byrequest' );
    }

    public static function bylogin( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        return self::events( self::filters( array( 'login' => self::arg( $args, 0, 'string' ) ) ), $allowed, $limit, $offset, 'bylogin' );
    }

    public static function byobject( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        return self::events( self::filters( array( 'object' => self::arg( $args, 0, 'string' ) ) ), $allowed, $limit, $offset, 'byobject' );
    }

    public static function refused( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        return self::events( self::filters( array( 'result' => 'refused' ) ), $allowed, $limit, $offset, 'refused' );
    }

    public static function failed( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        return self::events( self::filters( array( 'result' => 'failed' ) ), $allowed, $limit, $offset, 'failed' );
    }

    public static function names( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        return self::ok( expAuditConsole::knownNames( $allowed ) );
    }

    /** @return array array( query or null, allowed ) */
    protected static function indexed()
    {
        $allowed = self::allowed();
        if ( !expAuditConsole::indexUsable() )
            throw new expServiceException( 'The audit index is not usable; figures need it', 409 );
        return array( new expAuditQuery(), $allowed );
    }

    protected static function volumeFigures( $q, $allowed )
    {
        $today = array( 'from_ms' => self::todayMs() );
        $week = array( 'from_ms' => self::todayMs() - 6 * 86400000 );
        $t = array_column( $q->groupCount( array( 'channel' ), $today, $allowed, 20 ), 'n', 'channel' );
        $w = array_column( $q->groupCount( array( 'channel' ), $week, $allowed, 20 ), 'n', 'channel' );
        $out = array();
        foreach ( array_unique( array_merge( array_keys( $w ), array_keys( $t ) ) ) as $c )
            $out[] = array( 'channel' => $c, 'today' => isset( $t[$c] ) ? (int)$t[$c] : 0, 'week' => isset( $w[$c] ) ? (int)$w[$c] : 0 );
        return $out;
    }

    protected static function resultFigures( $q, $allowed )
    {
        $count = function ( $from ) use ( $q, $allowed ) {
            $by = array_column( $q->groupCount( array( 'result' ), array( 'from_ms' => $from ), $allowed, 5 ), 'n', 'result' );
            return array( 'success' => (int)( isset( $by['success'] ) ? $by['success'] : 0 ), 'refused' => (int)( isset( $by['refused'] ) ? $by['refused'] : 0 ),
                          'failed' => (int)( isset( $by['failed'] ) ? $by['failed'] : 0 ), 'total' => (int)array_sum( $by ) );
        };
        return array( 'today' => $count( self::todayMs() ), 'week' => $count( self::todayMs() - 6 * 86400000 ) );
    }

    protected static function failedLoginFigures( $q, $allowed )
    {
        $f = array( 'from_ms' => ( time() - 86400 ) * 1000, 'name' => 'access.session.login.failed' );
        $by = array();
        foreach ( $q->groupCount( array( 'ip' ), $f, $allowed, 5 ) as $r )
            $by[] = array( 'ip' => (string)$r['ip'], 'count' => (int)$r['n'] );
        return array( 'total' => (int)$q->count( $f, $allowed ), 'by_ip' => $by );
    }

    public static function volume( $args )
    {
        self::guard( __FUNCTION__ );
        list( $q, $allowed ) = self::indexed();
        return self::ok( self::volumeFigures( $q, $allowed ) );
    }

    public static function perday( $args )
    {
        self::guard( __FUNCTION__ );
        list( $q, $allowed ) = self::indexed();
        $days = max( 1, min( 30, self::arg( $args, 0, 'int', 7 ) ) );
        $perDay = $q->perDay( array( 'from_ms' => self::todayMs() - ( $days - 1 ) * 86400000 ), $allowed, null, (int)date( 'Z' ) * 1000 );
        $list = array();
        for ( $i = $days - 1; $i >= 0; $i-- )
        {
            $d = date( 'Y-m-d', mktime( 0, 0, 0, (int)date( 'n' ), (int)date( 'j' ) - $i ) );
            $list[] = array( 'day' => $d, 'count' => isset( $perDay[$d] ) ? (int)array_sum( (array)$perDay[$d] ) : 0 );
        }
        return self::ok( $list );
    }

    public static function results( $args )
    {
        self::guard( __FUNCTION__ );
        list( $q, $allowed ) = self::indexed();
        return self::ok( self::resultFigures( $q, $allowed ) );
    }

    public static function topactors( $args )
    {
        self::guard( __FUNCTION__ );
        list( $q, $allowed ) = self::indexed();
        list( $limit ) = self::paging( $args, 0, 99 );
        $list = array();
        foreach ( $q->groupCount( array( 'login' ), array( 'from_ms' => self::todayMs() ), $allowed, $limit ) as $r )
            $list[] = array( 'login' => (string)$r['login'], 'count' => (int)$r['n'] );
        return self::ok( $list );
    }

    public static function failedlogins( $args )
    {
        self::guard( __FUNCTION__ );
        list( $q, $allowed ) = self::indexed();
        return self::ok( self::failedLoginFigures( $q, $allowed ) );
    }

    public static function chains( $args )
    {
        self::guard( __FUNCTION__ );
        $allowed = self::allowed();
        return self::ok( expAuditConsole::chainStates( $allowed, false ) );
    }

    public static function summary( $args )
    {
        self::guard( __FUNCTION__ );
        list( $q, $allowed ) = self::indexed();
        expAuditConsole::recordRead( 'audit/service/summary', array(), null );
        return self::ok( array( 'volume' => self::volumeFigures( $q, $allowed ), 'results' => self::resultFigures( $q, $allowed ),
                                'failed_logins' => self::failedLoginFigures( $q, $allowed ), 'chains' => expAuditConsole::chainStates( $allowed, false ) ) );
    }
}
