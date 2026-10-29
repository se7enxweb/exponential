<?php
/**
 * File containing the expUserAudit class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The user audit log: one row per security event about a user account or a user group in the table expuser_audit,
 * kept in the site's database next to the audit files (doc/eng-us/features/6.0/user-audit.md).
 *
 * The rows are a copy of the audit records (expAudit) of the access channel: the sink expAuditUserAuditSink hands
 * every written record to storeRecords(), which keeps those that name a user account or a user group (the account
 * changed, signed in or refused, the user or group a role was assigned to). So every path the audit records is in
 * the table without a hook of its own: user/edit and content edits of user objects (access.user.change and the
 * e-mail, login and password events), registration and its approval, enable, disable, move and removal, member
 * management, password changes and resets, role assignments, sign-ins and refused attempts. Who (actor id and
 * login), when, where (siteaccess, host, address), whom (target type, id and login), what (the event name, result
 * and reason) and the fields before and after are kept; the address only as the audit's privacy rules leave it
 * (audit.ini [AuditPrivacySettings] Field[actor.ip]) and never with [UserAuditSettings] RecordIP=disabled.
 * Password hashes, passwords and secrets never reach the table: the audit does not record them, and every member
 * whose name looks like one is dropped again here (secretKey()).
 *
 * Read by the view user/audit (policy user/audit: filter, page, CSV), emptied after [UserAuditSettings]
 * RetentionDays by the cronjob part useraudit (cronjobs/useraudit.php). The table is in share/db_schema.dba and the
 * kernel schemas of every engine; update/common/scripts/6.0/createuseraudittable.php adds it to an older
 * installation. Without the table nothing is stored and the view says so. Not on MongoDB.
 */
class expUserAudit
{
    const TABLE = 'expuser_audit';

    /** The i18n context of the view and the CSV */
    const CONTEXT = 'design/admin/user/audit';

    /** The patterns kept when [UserAuditSettings] Events[] is empty */
    public static $defaultEvents = array( 'access.user.*', 'access.role.assign', 'access.role.unassign', 'access.session.*',
                                          'access.passkey.*', 'access.registration.*', 'access.permission.refused' );

    /** The columns of the CSV, in order */
    public static $csvColumns = array( 'time', 'event_name', 'result', 'reason', 'target_type', 'target_id', 'target_login',
                                       'actor_id', 'actor_login', 'siteaccess', 'host', 'client_ip', 'module_view',
                                       'request_id', 'value_before', 'value_after' );

    /** @var array|null The settings, per process (reset() forgets them) */
    protected static $settings = null;

    /**
     * The settings of [UserAuditSettings] in audit.ini, with their defaults.
     *
     * @return array enabled (bool), events (string[]), record_ip (bool), retention_days (int), max_value_length (int)
     */
    public static function settings()
    {
        if ( self::$settings !== null )
            return self::$settings;
        $s = array( 'enabled' => true, 'events' => self::$defaultEvents, 'record_ip' => true, 'retention_days' => 730,
                    'max_value_length' => 2000 );
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance( 'audit.ini' );
            $get = function ( $var ) use ( $ini ) {
                return $ini->hasVariable( 'UserAuditSettings', $var ) ? $ini->variable( 'UserAuditSettings', $var ) : null;
            };
            if ( $get( 'UserAudit' ) !== null )
                $s['enabled'] = $get( 'UserAudit' ) !== 'disabled';
            $events = $get( 'Events' );
            if ( is_array( $events ) )
            {
                $events = array_values( array_filter( array_map( 'trim', $events ), 'strlen' ) );
                if ( $events )
                    $s['events'] = $events;
            }
            if ( $get( 'RecordIP' ) !== null )
                $s['record_ip'] = $get( 'RecordIP' ) !== 'disabled';
            if ( $get( 'RetentionDays' ) !== null )
                $s['retention_days'] = max( 0, (int)$get( 'RetentionDays' ) );
            if ( $get( 'MaxValueLength' ) !== null )
                $s['max_value_length'] = max( 100, (int)$get( 'MaxValueLength' ) );
        }
        return self::$settings = $s;
    }

    /** Forgets the settings and what exists() found (tests, a persistent worker). */
    public static function reset()
    {
        self::$settings = null;
        foreach ( array_keys( $GLOBALS ) as $k )
            if ( strpos( (string)$k, 'expUserAuditExists_' ) === 0 )
                unset( $GLOBALS[$k] );
    }

    /**
     * Whether an event name is kept ([UserAuditSettings] Events[], patterns as in audit.ini).
     *
     * @param string $name
     * @param string[] $patterns
     * @return bool
     */
    public static function wantsName( $name, array $patterns )
    {
        foreach ( $patterns as $p )
        {
            $p = (string)$p;
            if ( $p === '*' || $p === $name )
                return true;
            if ( substr( $p, -2 ) === '.*' && strpos( (string)$name . '.', substr( $p, 0, -1 ) ) === 0 )
                return true;
        }
        return false;
    }

    /**
     * Whether a member name looks like a password, a hash or another secret: never stored.
     *
     * @param string|int $key
     * @return bool
     */
    public static function secretKey( $key )
    {
        return is_string( $key ) && preg_match( '/pass(word)?|hash|secret|token|salt|otp|credential|private|api_?key|signature/i', $key ) === 1;
    }

    /**
     * A before or after value without secrets, as JSON no longer than $max bytes ('' when empty).
     *
     * @param mixed $value
     * @param int $max
     * @return string
     */
    public static function encodeValue( $value, $max )
    {
        if ( $value === null || $value === array() || $value === '' )
            return '';
        $clean = function ( $v ) use ( &$clean ) {
            if ( !is_array( $v ) )
                return $v;
            $out = array();
            foreach ( $v as $k => $item )
            {
                if ( self::secretKey( $k ) )
                    continue;
                $out[$k] = $clean( $item );
            }
            return $out;
        };
        $value = $clean( $value );
        if ( $value === array() )
            return '';
        $json = json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );
        if ( !is_string( $json ) )
            return '';
        if ( strlen( $json ) > $max )
            $json = mb_strcut( $json, 0, max( 0, $max - 3 ), 'UTF-8' ) . '...';
        return $json;
    }

    /**
     * The account or group a record is about: its object when that is a user, else its target when that is a user or
     * a user group; null when the record names neither (it is not kept).
     *
     * @param array $record
     * @return array|null type ('user' or 'group'), id (0 when unknown), login
     */
    public static function subjectOf( array $record )
    {
        foreach ( array( 'object', 'target' ) as $part )
        {
            if ( !isset( $record[$part]['type'] ) || !is_array( $record[$part] ) )
                continue;
            $type = (string)$record[$part]['type'];
            if ( $type !== 'user' && !( $type === 'group' && $part === 'target' ) )
                continue;
            return array( 'type' => $type,
                          'id' => isset( $record[$part]['id'] ) && is_numeric( $record[$part]['id'] ) ? (int)$record[$part]['id'] : 0,
                          'login' => isset( $record[$part]['login'] ) && is_scalar( $record[$part]['login'] ) ? (string)$record[$part]['login'] : '' );
        }
        return null;
    }

    /**
     * The row of the table for an audit record, or null when it is not kept: audit records of the access channel
     * whose name matches Events[] and that name a user account or group. The record has been through the audit's
     * privacy rules already (address truncated, e-mail addresses hashed ...).
     *
     * @param array $record an audit record as written (expAudit::build())
     * @param array|null $settings settings(); null reads them
     * @param string $host the host the request was sent to (the record knows only the server's name)
     * @return array|null column => value
     */
    public static function rowFromRecord( array $record, ?array $settings = null, $host = '' )
    {
        $settings = $settings ?: self::settings();
        $name = isset( $record['name'] ) ? (string)$record['name'] : '';
        if ( $name === '' || empty( $settings['enabled'] ) || !self::wantsName( $name, $settings['events'] ) )
            return null;
        $subject = self::subjectOf( $record );
        if ( $subject === null )
            return null;
        // the sign-in and sign-out of a command (a cronjob runs as a user) is no session of a person
        if ( strpos( $name, 'access.session.' ) === 0 && !empty( $record['actor']['cli'] ) )
            return null;
        $max = isset( $settings['max_value_length'] ) ? (int)$settings['max_value_length'] : 2000;
        $timeMs = 0;
        if ( isset( $record['time'] ) && ( $t = strtotime( (string)$record['time'] ) ) !== false )
        {
            $timeMs = $t * 1000;
            if ( preg_match( '/\.(\d{1,3})/', (string)$record['time'], $m ) )
                $timeMs += (int)str_pad( $m[1], 3, '0' );
        }
        $actor = isset( $record['actor'] ) && is_array( $record['actor'] ) ? $record['actor'] : array();
        $request = isset( $record['request'] ) && is_array( $record['request'] ) ? $record['request'] : array();
        $cut = function ( $v, $len ) {
            $v = is_scalar( $v ) ? (string)$v : '';
            return strlen( $v ) > $len ? mb_strcut( $v, 0, $len, 'UTF-8' ) : $v;
        };
        if ( $host === '' && isset( $request['host'] ) )
            $host = (string)$request['host'];
        return array(
            'event_id' => $cut( isset( $record['id'] ) ? $record['id'] : '', 26 ),
            'created' => (int)floor( $timeMs / 1000 ),
            'time_ms' => $timeMs,
            'event_name' => $cut( $name, 128 ),
            'result' => $cut( isset( $record['result'] ) ? $record['result'] : 'success', 16 ),
            'reason' => $cut( isset( $record['reason'] ) ? $record['reason'] : '', 64 ),
            'target_type' => $subject['type'],
            'target_id' => $subject['id'],
            'target_login' => $cut( $subject['login'], 150 ),
            'actor_id' => isset( $actor['user_id'] ) && is_numeric( $actor['user_id'] ) ? (int)$actor['user_id'] : 0,
            'actor_login' => $cut( isset( $actor['login'] ) ? $actor['login'] : '', 150 ),
            'siteaccess' => $cut( isset( $request['siteaccess'] ) ? $request['siteaccess'] : '', 64 ),
            'host' => $cut( $host, 255 ),
            'client_ip' => !empty( $settings['record_ip'] ) ? $cut( isset( $actor['ip'] ) ? $actor['ip'] : '', 64 ) : '',
            'module_view' => $cut( isset( $request['module'] ) ? $request['module'] : '', 128 ),
            'request_id' => $cut( isset( $request['id'] ) ? $request['id'] : '', 40 ),
            'value_before' => self::encodeValue( isset( $record['before'] ) ? $record['before'] : null, $max ),
            'value_after' => self::encodeValue( isset( $record['after'] ) ? $record['after'] : null, $max ),
        );
    }

    /**
     * Stores the rows of the records that are kept (the sink's delivery). A record stored already (its event id) is
     * skipped, so a batch delivered again from the spool adds nothing.
     *
     * @param array[] $records
     * @return int|false how many records were handled (kept or skipped), false when the table is missing
     */
    public static function storeRecords( array $records )
    {
        $settings = self::settings();
        if ( !$settings['enabled'] )
            return count( $records );
        $currentRequest = class_exists( 'expAudit' ) ? expAudit::requestId() : null;
        $httpHost = isset( $_SERVER['HTTP_HOST'] ) ? preg_replace( '/[^A-Za-z0-9.\-:\[\]]/', '', (string)$_SERVER['HTTP_HOST'] ) : '';
        $rows = array();
        foreach ( $records as $record )
        {
            if ( !is_array( $record ) )
                continue;
            // the host the request was sent to, while it is still this request
            $host = $httpHost !== '' && isset( $record['request']['id'] ) && $record['request']['id'] === $currentRequest ? $httpHost : '';
            $row = self::rowFromRecord( $record, $settings, $host );
            if ( $row !== null && $row['event_id'] !== '' )
                $rows[$row['event_id']] = $row;
        }
        if ( !$rows )
            return count( $records );
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' || self::exists( $db ) !== true )
            return false;
        $quoted = array();
        foreach ( array_keys( $rows ) as $eventID )
            $quoted[] = "'" . $db->escapeString( (string)$eventID ) . "'";
        $known = $db->arrayQuery( 'SELECT event_id FROM ' . self::TABLE . ' WHERE event_id IN ( ' . implode( ', ', $quoted ) . ' )' );
        foreach ( (array)$known as $k )
            unset( $rows[$k['event_id']] );
        if ( !$rows )
            return count( $records );
        $db->begin();
        foreach ( $rows as $row )
        {
            $columns = array();
            $values = array();
            foreach ( $row as $column => $value )
            {
                $columns[] = $column;
                $values[] = is_int( $value ) ? (string)$value : "'" . $db->escapeString( (string)$value ) . "'";
            }
            $db->query( 'INSERT INTO ' . self::TABLE . ' ( ' . implode( ', ', $columns ) . ' ) VALUES ( ' . implode( ', ', $values ) . ' )' );
        }
        $db->commit();
        // the security alerts of the rows stored for the first time (two-step sign-in changed, recovery code used,
        // failed sign-ins, the account locked); never twice for a row delivered again from the spool
        if ( class_exists( 'expUserSecurityAlerts' ) )
        {
            try
            {
                expUserSecurityAlerts::notify( array_values( $rows ) );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'User audit: the security alerts failed: ' . $e->getMessage(), __METHOD__ );
            }
        }
        return count( $records );
    }

    /**
     * The filters of the view from a request (GET): user (an id or a login), action (an event name or its start),
     * result, from and to (Y-m-d), limit.
     *
     * @param array $input
     * @return array
     */
    public static function normaliseFilters( array $input )
    {
        $str = function ( $key, $len ) use ( $input ) {
            return isset( $input[$key] ) && is_string( $input[$key] ) ? mb_substr( trim( $input[$key] ), 0, $len ) : '';
        };
        $f = array(
            'user' => $str( 'user', 150 ),
            'action' => preg_replace( '/[^a-z0-9_.*]/', '', strtolower( $str( 'action', 128 ) ) ),
            'result' => in_array( $str( 'result', 16 ), array( 'success', 'refused', 'failed' ), true ) ? $str( 'result', 16 ) : '',
            'from' => '',
            'to' => '',
            'limit' => isset( $input['limit'] ) && in_array( (int)$input['limit'], array( 25, 50, 100 ), true ) ? (int)$input['limit'] : 50,
        );
        foreach ( array( 'from', 'to' ) as $key )
        {
            $d = $str( $key, 10 );
            if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $d ) && checkdate( (int)substr( $d, 5, 2 ), (int)substr( $d, 8, 2 ), (int)substr( $d, 0, 4 ) ) )
                $f[$key] = $d;
        }
        if ( substr( $f['action'], -1 ) === '*' )
            $f['action'] = rtrim( rtrim( $f['action'], '*' ), '.' );
        return $f;
    }

    /**
     * The query string of the filters, for links and the pager ('' when none is set).
     *
     * @param array $filters
     * @return string
     */
    public static function queryString( array $filters )
    {
        $q = array();
        foreach ( array( 'user', 'action', 'result', 'from', 'to' ) as $key )
            if ( isset( $filters[$key] ) && $filters[$key] !== '' )
                $q[$key] = $filters[$key];
        if ( isset( $filters['limit'] ) && (int)$filters['limit'] !== 50 )
            $q['limit'] = (int)$filters['limit'];
        return $q ? '?' . http_build_query( $q, '', '&' ) : '';
    }

    /**
     * The WHERE clause of the filters ('' for none). The user filter matches the account the event is about or the
     * user who acted: by id when it is a number, else by login (exactly, any case).
     *
     * @param array $filters normaliseFilters()
     * @param callable $escape string => escaped string
     * @return string
     */
    public static function whereClause( array $filters, $escape )
    {
        $where = array();
        if ( $filters['user'] !== '' )
        {
            if ( ctype_digit( $filters['user'] ) )
                $where[] = '( target_id = ' . (int)$filters['user'] . ' OR actor_id = ' . (int)$filters['user'] . ' )';
            else
            {
                $login = $escape( mb_strtolower( $filters['user'] ) );
                $where[] = "( LOWER( target_login ) = '$login' OR LOWER( actor_login ) = '$login' )";
            }
        }
        if ( $filters['action'] !== '' )
        {
            $action = $escape( $filters['action'] );
            $like = $escape( str_replace( array( '\\', '%' ), '', $filters['action'] ) );
            $where[] = "( event_name = '$action' OR event_name LIKE '$like.%' )";
        }
        if ( $filters['result'] !== '' )
            $where[] = "result = '" . $escape( $filters['result'] ) . "'";
        if ( $filters['from'] !== '' )
            $where[] = 'created >= ' . (int)gmmktime( 0, 0, 0, (int)substr( $filters['from'], 5, 2 ), (int)substr( $filters['from'], 8, 2 ), (int)substr( $filters['from'], 0, 4 ) );
        if ( $filters['to'] !== '' )
            $where[] = 'created < ' . ( (int)gmmktime( 0, 0, 0, (int)substr( $filters['to'], 5, 2 ), (int)substr( $filters['to'], 8, 2 ), (int)substr( $filters['to'], 0, 4 ) ) + 86400 );
        return $where ? ' WHERE ' . implode( ' AND ', $where ) : '';
    }

    /**
     * How many rows match.
     *
     * @param array $filters
     * @return int
     */
    public static function count( array $filters )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ' . self::TABLE . self::whereClause( $filters, array( $db, 'escapeString' ) ) );
        return isset( $rows[0]['n'] ) ? (int)$rows[0]['n'] : 0;
    }

    /**
     * A page of matching rows, newest first.
     *
     * @param array $filters
     * @param int $offset
     * @param int $limit 0: all
     * @return array[]
     */
    public static function fetch( array $filters, $offset = 0, $limit = 50 )
    {
        $db = eZDB::instance();
        $params = $limit > 0 ? array( 'offset' => max( 0, (int)$offset ), 'limit' => (int)$limit ) : array();
        $rows = $db->arrayQuery( 'SELECT * FROM ' . self::TABLE . self::whereClause( $filters, array( $db, 'escapeString' ) )
                                 . ' ORDER BY time_ms DESC, id DESC', $params );
        $out = array();
        foreach ( (array)$rows as $row )
            $out[] = self::present( $row );
        return $out;
    }

    /**
     * A row for the template: numbers as numbers, before/after decoded.
     *
     * @param array $row
     * @return array
     */
    public static function present( array $row )
    {
        foreach ( array( 'id', 'created', 'target_id', 'actor_id' ) as $k )
            $row[$k] = isset( $row[$k] ) ? (int)$row[$k] : 0;
        foreach ( array( 'value_before' => 'before', 'value_after' => 'after' ) as $column => $key )
        {
            $decoded = isset( $row[$column] ) && $row[$column] !== '' ? json_decode( (string)$row[$column], true ) : null;
            $row[$key] = is_array( $decoded ) ? self::flatten( $decoded ) : array();
        }
        return $row;
    }

    /**
     * Nested values as path => text, for the table of the view.
     *
     * @param array $value
     * @param string $prefix
     * @return string[]
     */
    public static function flatten( array $value, $prefix = '' )
    {
        $out = array();
        foreach ( $value as $k => $v )
        {
            $path = $prefix === '' ? (string)$k : $prefix . '.' . $k;
            if ( is_array( $v ) && $v && array_keys( $v ) !== range( 0, count( $v ) - 1 ) )
                $out += self::flatten( $v, $path );
            else
                $out[$path] = is_array( $v ) ? implode( ', ', array_map( function ( $x ) { return is_scalar( $x ) ? (string)$x : json_encode( $x ); }, $v ) )
                                            : ( is_bool( $v ) ? ( $v ? 'true' : 'false' ) : ( $v === null ? '' : (string)$v ) );
        }
        return $out;
    }

    /**
     * A CSV cell that a spreadsheet does not run as a formula.
     *
     * @param mixed $value
     * @return string
     */
    public static function csvCell( $value )
    {
        $value = (string)$value;
        if ( $value !== '' && strpos( "=+-@\t\r", $value[0] ) !== false )
            $value = "'" . $value;
        return $value;
    }

    /**
     * Writes the matching rows as CSV (a header line, then one line per row, newest first).
     *
     * @param resource $out
     * @param array $filters
     * @return int rows written
     */
    public static function writeCSV( $out, array $filters )
    {
        fputcsv( $out, self::$csvColumns, ',', '"', '' );
        $n = 0;
        $offset = 0;
        do
        {
            $rows = self::fetch( $filters, $offset, 500 );
            foreach ( $rows as $row )
            {
                $line = array();
                foreach ( self::$csvColumns as $column )
                    $line[] = self::csvCell( $column === 'time' ? gmdate( 'Y-m-d\TH:i:s\Z', $row['created'] ) : ( isset( $row[$column] ) ? $row[$column] : '' ) );
                fputcsv( $out, $line, ',', '"', '' );
                $n++;
            }
            $offset += 500;
        }
        while ( count( $rows ) === 500 );
        return $n;
    }

    /**
     * Removes the rows older than [UserAuditSettings] RetentionDays (0 keeps them all).
     *
     * @param int|null $days null: the setting
     * @param int|null $now
     * @return int|false rows removed, false when the table is missing
     */
    public static function cleanup( $days = null, $now = null )
    {
        $days = $days === null ? self::settings()['retention_days'] : (int)$days;
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' || self::exists( $db ) !== true )
            return false;
        if ( $days <= 0 )
            return 0;
        $limit = ( $now === null ? time() : (int)$now ) - $days * 86400;
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ' . self::TABLE . ' WHERE created < ' . (int)$limit );
        $n = isset( $rows[0]['n'] ) ? (int)$rows[0]['n'] : 0;
        if ( $n > 0 )
        {
            $db->begin();
            $db->query( 'DELETE FROM ' . self::TABLE . ' WHERE created < ' . (int)$limit );
            $db->commit();
        }
        return $n;
    }

    /**
     * @return array table name => definition, from share/db_schema.dba
     */
    public static function definition()
    {
        $schema = array();
        $file = eZSys::rootDir() . '/share/db_schema.dba';
        if ( is_readable( $file ) )
            $schema = eZDbSchema::read( $file, true );
        if ( isset( $schema['schema'] ) )
            $schema = $schema['schema'];
        return isset( $schema[self::TABLE] ) ? array( self::TABLE => $schema[self::TABLE] ) : array();
    }

    /**
     * Whether the table exists. "Yes" is kept for the database connection; reset() forgets it.
     *
     * @param eZDBInterface|null $db
     * @return bool|null null when it cannot be told (MongoDB, an unknown engine)
     */
    public static function exists( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $key = 'expUserAuditExists_' . spl_object_id( $db );
        if ( array_key_exists( $key, $GLOBALS ) )
            return $GLOBALS[$key];
        switch ( expAuditIndexSchema::type( $db ) )
        {
            case 'sqlite':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'expuser_audit'" );
                break;
            case 'mysql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'expuser_audit'" );
                break;
            case 'postgresql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT relname AS name FROM pg_class WHERE relkind = 'r' AND relname = 'expuser_audit'" );
                break;
            case 'oracle':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM user_tables WHERE table_name = 'EXPUSER_AUDIT'" );
                break;
            default:
                $rows = null;
        }
        $result = $rows === null ? null : count( $rows ) > 0;
        // only "exists" is kept, so a table created by the update script is seen by a persistent worker at once
        if ( $result === true )
            $GLOBALS[$key] = $result;
        return $result;
    }

    /**
     * Creates the table when it is missing, through the engine's schema handler.
     *
     * @param eZDBInterface|null $db
     * @param string[] $messages what was done, for the command line
     * @param bool $dryRun only say what would be done
     * @return bool whether the table exists afterwards (dry run: whether it exists now)
     */
    public static function install( $db = null, &$messages = array(), $dryRun = false )
    {
        $db = $db ?: eZDB::instance();
        self::reset();
        $exists = self::exists( $db );
        if ( $exists === true )
        {
            $messages[] = 'the table ' . self::TABLE . ' exists already';
            return true;
        }
        if ( $exists === null )
        {
            $messages[] = 'cannot tell whether the table ' . self::TABLE . ' exists on this database';
            return false;
        }
        $schema = self::definition();
        if ( !$schema )
        {
            $messages[] = 'share/db_schema.dba has no ' . self::TABLE . ' table';
            return false;
        }
        if ( $dryRun )
        {
            $messages[] = 'the table ' . self::TABLE . ' is missing and would be created (dry run, nothing changed)';
            return false;
        }
        $type = expAuditIndexSchema::type( $db );
        $handler = eZDbSchema::instance( array( 'type' => $type, 'instance' => $db, 'schema' => $schema ) );
        if ( !$handler )
        {
            $messages[] = "no schema handler for $type";
            return false;
        }
        // SQLite: inside a transaction the statement waits for the database's writer instead of failing at once
        $wrap = $type === 'sqlite';
        if ( $wrap )
            $db->begin();
        $ok = $handler->insertSchema( array( 'schema' => true, 'data' => false, 'table_type' => $type === 'mysql' ? 'innodb' : '' ) );
        if ( $wrap )
            $ok = $db->commit() && $ok;
        $messages[] = $ok ? 'created ' . self::TABLE : 'could not create ' . self::TABLE;
        self::reset();
        return $ok && self::exists( $db ) === true;
    }
}
