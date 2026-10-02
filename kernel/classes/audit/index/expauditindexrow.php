<?php
/**
 * One audit record as a row of expaudit_event (doc/bc/6.0/audit.md, "Tables"): the columns the console filters
 * on, the record itself (as written, so the privacy rules of the writer apply) and the lower-cased search text.
 * Also matches a row against console filters in PHP, for the records of the live files that are not indexed yet.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditIndexRow
{
    /** RFC 5424 severity names by number */
    const SEVERITIES = array( 'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug' );

    /** Longest search text kept per row, in bytes */
    const MAX_SEARCH_TEXT = 8192;

    /**
     * @param string $name RFC 5424 name
     * @return int 0 (emergency) ... 7 (debug); 6 (info) for an unknown name
     */
    public static function severityNumber( $name )
    {
        $i = array_search( strtolower( (string)$name ), self::SEVERITIES, true );
        return $i === false ? 6 : (int)$i;
    }

    /** @return string the name of a severity number */
    public static function severityName( $number )
    {
        $number = (int)$number;
        return isset( self::SEVERITIES[$number] ) ? self::SEVERITIES[$number] : 'info';
    }

    /**
     * RFC 3339 UTC with milliseconds to epoch milliseconds.
     *
     * @param string $time
     * @return int
     */
    public static function timeMs( $time )
    {
        if ( !is_string( $time ) || $time === '' )
            return 0;
        if ( preg_match( '/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(?:\.(\d{1,6}))?Z$/', $time, $m ) )
        {
            $s = gmmktime( (int)substr( $m[2], 0, 2 ), (int)substr( $m[2], 3, 2 ), (int)substr( $m[2], 6, 2 ),
                           (int)substr( $m[1], 5, 2 ), (int)substr( $m[1], 8, 2 ), (int)substr( $m[1], 0, 4 ) );
            $ms = isset( $m[3] ) ? (int)substr( str_pad( $m[3], 3, '0' ), 0, 3 ) : 0;
            return $s * 1000 + $ms;
        }
        $t = strtotime( $time );
        return $t === false ? 0 : $t * 1000;
    }

    /**
     * The row of a record.
     *
     * @param array $r the decoded record
     * @param string $line the line as written (stored in `record`)
     * @param string $fileName the channel file (for imported records: imported/<file>)
     * @return array|null column => value; null for a line that is not a record
     */
    public static function fromRecord( array $r, $line, $fileName )
    {
        if ( !isset( $r['id'], $r['name'] ) || !is_string( $r['id'] ) || !is_string( $r['name'] ) )
            return null;
        $get = function ( $path ) use ( $r ) {
            $v = $r;
            foreach ( explode( '.', $path ) as $k )
            {
                if ( !is_array( $v ) || !array_key_exists( $k, $v ) )
                    return null;
                $v = $v[$k];
            }
            return $v;
        };
        $text = function ( $v, $max ) {
            if ( $v === null )
                return null;
            if ( is_bool( $v ) )
                $v = $v ? 'true' : 'false';
            elseif ( is_array( $v ) )
                $v = json_encode( $v, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            $v = (string)$v;
            return self::cut( $v, $max );
        };
        $userId = $get( 'actor.user_id' );
        $login = $get( 'actor.login' );
        if ( $login === null && $get( 'actor.cli.os_user' ) !== null )
            $login = 'os:' . $get( 'actor.cli.os_user' );
        $ranks = explode( '.', $r['name'] );

        $row = array(
            'id' => substr( $r['id'], 0, 26 ),
            'channel' => $text( isset( $r['channel'] ) ? $r['channel'] : 'system', 32 ),
            'seq' => isset( $r['seq'] ) ? (int)$r['seq'] : 0,
            'file_name' => self::cut( (string)$fileName, 64 ),
            'name' => self::cut( $r['name'], 128 ),
            'domain_name' => self::cut( $ranks[0], 16 ),
            'severity' => self::severityNumber( isset( $r['severity'] ) ? $r['severity'] : 'info' ),
            'time_ms' => self::timeMs( isset( $r['time'] ) ? $r['time'] : '' ),
            'request_id' => $text( $get( 'request.id' ), 40 ),
            'siteaccess' => $text( $get( 'request.siteaccess' ), 64 ),
            'engine' => $text( $get( 'request.engine' ), 16 ),
            'module_view' => $text( $get( 'request.module' ), 128 ),
            'user_id' => is_numeric( $userId ) ? (int)$userId : null,
            'login' => $text( $login, 150 ),
            'ip' => $text( $get( 'actor.ip' ), 64 ),
            'session_h' => $text( $get( 'actor.session' ), 24 ),
            'ua' => $text( $get( 'actor.ua' ), 128 ),
            'verb' => $text( isset( $r['verb'] ) ? $r['verb'] : ( isset( $ranks[2] ) ? $ranks[2] : null ), 32 ),
            'object_type' => $text( $get( 'object.type' ), 32 ),
            'object_id' => $text( $get( 'object.id' ), 64 ),
            'object_name' => $text( $get( 'object.name' ) !== null ? $get( 'object.name' ) : ( $get( 'object.login' ) !== null ? $get( 'object.login' ) : $get( 'object.attempted_login' ) ), 255 ),
            'target_type' => $text( $get( 'target.type' ), 32 ),
            'target_id' => $text( $get( 'target.id' ), 64 ),
            'result' => $text( isset( $r['result'] ) ? $r['result'] : 'success', 8 ),
            'reason' => $text( isset( $r['reason'] ) ? $r['reason'] : null, 32 ),
            'parent_id' => $text( isset( $r['parent'] ) ? $r['parent'] : null, 26 ),
            'depth' => isset( $r['depth'] ) ? (int)$r['depth'] : 0,
            'job_id' => $text( isset( $r['job'] ) ? $r['job'] : null, 32 ),
            'run_id' => $text( isset( $r['run'] ) ? $r['run'] : null, 40 ),
            'imported' => !empty( $r['imported'] ) ? 1 : 0,
            'pseudonymised' => 0,
            'record' => (string)$line,
            'search_text' => self::searchText( $r ),
        );
        return $row;
    }

    /**
     * The lower-cased words a search looks in: the name and its label, the object and target (type, id, name,
     * every scalar field), the actor's login, the reason, and the before/after values.
     *
     * @param array $r
     * @return string
     */
    public static function searchText( array $r )
    {
        $parts = array( $r['name'] );
        if ( class_exists( 'expAuditTaxonomy' ) )
        {
            $label = expAuditTaxonomy::label( $r['name'] );
            if ( is_string( $label ) && $label !== '' && $label !== $r['name'] )
                $parts[] = $label;
        }
        foreach ( array( 'object', 'target', 'before', 'after' ) as $k )
            if ( isset( $r[$k] ) && is_array( $r[$k] ) )
                self::scalars( $r[$k], $parts );
        foreach ( array( 'login', 'ip' ) as $k )
            if ( isset( $r['actor'][$k] ) && is_scalar( $r['actor'][$k] ) )
                $parts[] = (string)$r['actor'][$k];
        if ( isset( $r['actor']['cli']['command'] ) )
            $parts[] = (string)$r['actor']['cli']['command'];
        foreach ( array( 'reason', 'result' ) as $k )
            if ( isset( $r[$k] ) && is_scalar( $r[$k] ) )
                $parts[] = (string)$r[$k];
        if ( isset( $r['request']['url'] ) )
            $parts[] = (string)$r['request']['url'];
        if ( isset( $r['error']['message'] ) )
            $parts[] = (string)$r['error']['message'];
        $text = function_exists( 'mb_strtolower' ) ? mb_strtolower( implode( ' ', $parts ), 'UTF-8' ) : strtolower( implode( ' ', $parts ) );
        $text = preg_replace( '/\s+/u', ' ', $text );
        return self::cut( trim( (string)$text ), self::MAX_SEARCH_TEXT );
    }

    /** Appends every scalar value (and the keys) of a nested array. */
    protected static function scalars( array $data, array &$parts, $depth = 0 )
    {
        if ( $depth > 4 )
            return;
        foreach ( $data as $k => $v )
        {
            if ( is_array( $v ) )
                self::scalars( $v, $parts, $depth + 1 );
            elseif ( $v !== null && $v !== '' && !is_bool( $v ) )
                $parts[] = ( is_string( $k ) ? $k . ' ' : '' ) . (string)$v;
        }
    }

    /**
     * Cuts a string to at most $max bytes without splitting a UTF-8 character.
     *
     * @param string $s
     * @param int $max
     * @return string
     */
    public static function cut( $s, $max )
    {
        $s = (string)$s;
        if ( strlen( $s ) <= $max )
            return $s;
        if ( function_exists( 'mb_strcut' ) )
            return mb_strcut( $s, 0, $max, 'UTF-8' );
        return substr( $s, 0, $max );
    }

    /**
     * Whether a row matches console filters (expAuditQuery::normalise()), for the records not indexed yet.
     *
     * @param array $row
     * @param array $f
     * @return bool
     */
    public static function matches( array $row, array $f )
    {
        if ( !empty( $f['channels'] ) && !in_array( $row['channel'], $f['channels'], true ) )
            return false;
        if ( !empty( $f['names'] ) && !in_array( $row['name'], $f['names'], true ) )
            return false;
        if ( isset( $f['name'] ) && $f['name'] !== '' && class_exists( 'expAuditTaxonomy' ) && expAuditTaxonomy::match( $f['name'], $row['name'] ) < 0 )
            return false;
        foreach ( array( 'user' => 'user_id', 'login' => 'login', 'result' => 'result', 'request' => 'request_id',
                         'job' => 'job_id', 'run' => 'run_id', 'parent' => 'parent_id', 'domain' => 'domain_name' ) as $k => $col )
            if ( isset( $f[$k] ) && $f[$k] !== '' && (string)$row[$col] !== (string)$f[$k] )
                return false;
        if ( isset( $f['object'] ) && ( $row['object_type'] !== $f['object'][0] || ( $f['object'][1] !== '' && (string)$row['object_id'] !== $f['object'][1] ) ) )
            return false;
        if ( isset( $f['target'] ) && ( $row['target_type'] !== $f['target'][0] || ( $f['target'][1] !== '' && (string)$row['target_id'] !== $f['target'][1] ) ) )
            return false;
        if ( isset( $f['severity'] ) && $row['severity'] > $f['severity'] )
            return false;
        if ( isset( $f['ip'] ) && $f['ip'] !== '' && strpos( (string)$row['ip'], $f['ip'] ) !== 0 )
            return false;
        if ( isset( $f['from_ms'] ) && $row['time_ms'] < $f['from_ms'] )
            return false;
        if ( isset( $f['to_ms'] ) && $row['time_ms'] >= $f['to_ms'] )
            return false;
        if ( isset( $f['q'] ) && $f['q'] !== '' )
        {
            foreach ( expAuditQuery::terms( $f['q'] ) as $term )
                if ( strpos( $row['search_text'], $term ) === false )
                    return false;
        }
        return true;
    }
}
