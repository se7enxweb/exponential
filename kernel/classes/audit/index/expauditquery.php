<?php
/**
 * Searches the audit index (doc/bc/6.0/audit.md, "The console", URL parameters): the console's filters turned
 * into one WHERE clause that every engine understands, full-text search the way the engine has it (FTS5,
 * MySQL FULLTEXT, PostgreSQL tsvector, Oracle Text, else LIKE on the lower-cased search text), counts, pages and
 * the groupings of the charts view.
 *
 * Filters (normalise() accepts the URL parameters, console form fields and fetch parameters alike):
 * channel, name (a taxonomy pattern: access.session.*), user (user id), login, object and target (type:id),
 * result, severity (the least severe shown: notice shows notice and worse), request, job, run, parent, ip
 * (a network or its start), from and to (YYYY-MM-DD[THH:MM[:SS]] with an optional Z or +HH:MM offset; without one
 * in the zone normalise() is given: the site's time zone for the console, UTC for exp:audit; to is inclusive),
 * q (search text), legacy_file (a 4.x audit file name: its event names), domain. The channels a user may read
 * (the Channel limitation of audit/read) are given separately and always apply.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditQuery
{
    /** Most rows per page */
    const MAX_LIMIT = 500;

    /** @var eZDBInterface */
    protected $db;

    /** @var string */
    protected $type;

    /** @var string fts5, mysql, postgresql, oracle or like */
    protected $fullText;

    /**
     * @param eZDBInterface|null $db
     * @param bool|null $fullText false: LIKE only (FullText=disabled)
     */
    public function __construct( $db = null, $fullText = null )
    {
        $this->db = $db ?: eZDB::instance();
        $this->type = expAuditIndexSchema::type( $this->db );
        if ( $fullText === null )
            $fullText = expAuditIndexSettings::get()['fullText'];
        $this->fullText = $fullText ? expAuditIndexSchema::fullTextKind( $this->db ) : 'like';
    }

    /** @return string the full-text kind in use */
    public function fullTextKind()
    {
        return $this->fullText;
    }

    /**
     * Cleans the filters given by a URL, a form or a template.
     *
     * A filter that is given but malformed is not dropped (that would widen the search to everything): it is listed
     * in 'invalid' (key => why), and where() and expAuditIndexRow::matches() then match nothing. Callers that can
     * refuse (the command) check 'invalid' first; the console shows it.
     *
     * @param array $in
     * @param array|null $config expAuditConfig::get() (for legacy_file)
     * @param DateTimeZone|string|null $zone the zone of from/to without an offset (null: the site's time zone)
     * @return array the filters that are set, normalised; from_ms/to_ms added for from/to; invalid (key => why)
     */
    public static function normalise( array $in, ?array $config = null, $zone = null )
    {
        $f = array();
        $invalid = array();
        $str = function ( $k, $pattern, $max = 128 ) use ( $in, &$f ) {
            if ( !isset( $in[$k] ) || !is_scalar( $in[$k] ) )
                return;
            $v = trim( (string)$in[$k] );
            if ( $v === '' || strlen( $v ) > $max || !preg_match( $pattern, $v ) )
                return;
            $f[$k] = $v;
        };
        $str( 'channel', '/^[a-z][a-z0-9_]{0,31}$/' );
        if ( isset( $in['name'] ) && ( !is_scalar( $in['name'] ) || trim( (string)$in['name'] ) !== '' ) )
        {
            $problem = is_scalar( $in['name'] ) ? expAuditTaxonomy::patternProblem( (string)$in['name'] ) : 'not a string';
            if ( $problem === null )
                $f['name'] = trim( (string)$in['name'] );
            else
                $invalid['name'] = $problem;
        }
        $str( 'login', '/^[^\x00-\x1f]+$/u', 150 );
        $str( 'result', '/^(success|refused|failed)$/' );
        $str( 'request', '/^[A-Za-z0-9_.:-]+$/', 40 );
        $str( 'job', '/^[A-Za-z0-9_.:-]+$/', 32 );
        $str( 'run', '/^[A-Za-z0-9_.:-]+$/', 40 );
        $str( 'parent', '/^[0-9A-HJKMNP-TV-Z]{26}$/' );
        $str( 'ip', '/^[0-9a-fA-F:.\/h]+$/', 64 );
        $str( 'domain', '/^[a-z]+$/', 16 );
        $str( 'legacy_file', '/^[A-Za-z0-9_.-]+$/', 64 );
        if ( isset( $in['user'] ) && is_scalar( $in['user'] ) && preg_match( '/^\d{1,10}$/', trim( (string)$in['user'] ) ) )
            $f['user'] = (int)$in['user'];
        foreach ( array( 'object', 'target' ) as $k )
        {
            if ( isset( $in[$k] ) && is_scalar( $in[$k] ) && preg_match( '/^([a-z][a-z0-9_]{0,31})(?::(.{1,64}))?$/', trim( (string)$in[$k] ), $m ) )
                $f[$k] = array( $m[1], isset( $m[2] ) ? $m[2] : '' );
        }
        if ( isset( $in['severity'] ) && is_scalar( $in['severity'] ) && trim( (string)$in['severity'] ) !== '' )
        {
            $s = strtolower( trim( (string)$in['severity'] ) );
            $n = ctype_digit( $s ) ? (int)$s : array_search( $s, expAuditIndexRow::SEVERITIES, true );
            if ( $n !== false && $n >= 0 && $n <= 7 )
                $f['severity'] = (int)$n;
        }
        foreach ( array( 'from', 'to' ) as $k )
        {
            if ( !isset( $in[$k] ) || ( is_scalar( $in[$k] ) && trim( (string)$in[$k] ) === '' ) )
                continue;
            $v = is_scalar( $in[$k] ) ? trim( (string)$in[$k] ) : '';
            $t = $v !== '' ? self::parseTime( $v, $k === 'to', $zone ) : null;
            if ( $t === null )
            {
                $invalid[$k] = "'" . substr( $v, 0, 40 ) . "' is not a time: YYYY-MM-DD, YYYY-MM-DDTHH:MM[:SS], optionally with Z or +HH:MM";
                continue;
            }
            $f[$k] = $v;
            $f[$k . '_ms'] = $t['ms'];
        }
        if ( isset( $in['q'] ) && is_scalar( $in['q'] ) )
        {
            $q = trim( preg_replace( '/\s+/u', ' ', (string)$in['q'] ) );
            if ( $q !== '' )
                $f['q'] = function_exists( 'mb_substr' ) ? mb_substr( $q, 0, 200, 'UTF-8' ) : substr( $q, 0, 200 );
        }
        if ( isset( $f['legacy_file'] ) )
            $f['names'] = self::legacyNames( $f['legacy_file'], $config );
        if ( $invalid )
            $f['invalid'] = $invalid;
        return $f;
    }

    /**
     * Reads a from/to time of a filter. Forms: YYYY-MM-DD, YYYY-MM-DDTHH:MM, YYYY-MM-DDTHH:MM:SS (a space instead of
     * the T too), each optionally followed by Z, +HH:MM, +HHMM or +HH (or -). A time with an offset is that instant;
     * one without is read in $zone. "to" is inclusive: of the whole day, minute or second given.
     *
     * @param string $value
     * @param bool $isTo
     * @param DateTimeZone|string|null $zone null: the site's time zone (date_default_timezone_get())
     * @return array|null ms (the bound in milliseconds: from inclusive, to exclusive), utc (the bound as a UTC
     *                    time string); null when it is not a time
     */
    public static function parseTime( $value, $isTo = false, $zone = null )
    {
        if ( !is_string( $value ) || !preg_match( '/^(\d{4})-(\d{2})-(\d{2})(?:[T ](\d{2}):(\d{2})(?::(\d{2}))?)?(Z|[+-]\d{2}(?::?\d{2})?)?$/i', trim( $value ), $m ) )
            return null;
        if ( !checkdate( (int)$m[2], (int)$m[3], (int)$m[1] ) )
            return null;
        $hasTime = isset( $m[4] ) && $m[4] !== '';
        $hasSeconds = isset( $m[6] ) && $m[6] !== '';
        if ( $hasTime && ( (int)$m[4] > 23 || (int)$m[5] > 59 || ( $hasSeconds && (int)$m[6] > 59 ) ) )
            return null;
        $offset = isset( $m[7] ) && $m[7] !== '' ? strtoupper( $m[7] ) : null;
        try
        {
            if ( $offset !== null )
            {
                if ( $offset !== 'Z' )
                {
                    $digits = str_replace( ':', '', substr( $offset, 1 ) );
                    if ( strlen( $digits ) === 2 )
                        $digits .= '00';
                    if ( (int)substr( $digits, 0, 2 ) > 14 || (int)substr( $digits, 2, 2 ) > 59 )
                        return null;
                    $offset = $offset[0] . substr( $digits, 0, 2 ) . ':' . substr( $digits, 2, 2 );
                }
                $tz = new DateTimeZone( $offset === 'Z' ? 'UTC' : $offset );
            }
            elseif ( $zone instanceof DateTimeZone )
                $tz = $zone;
            else
                $tz = new DateTimeZone( is_string( $zone ) && $zone !== '' ? $zone : date_default_timezone_get() );
            $d = new DateTime( sprintf( '%s-%s-%s %02d:%02d:%02d', $m[1], $m[2], $m[3], $hasTime ? (int)$m[4] : 0,
                                        $hasTime ? (int)$m[5] : 0, $hasSeconds ? (int)$m[6] : 0 ), $tz );
        }
        catch ( Throwable $e )
        {
            return null;
        }
        $t = $d->getTimestamp();
        if ( $isTo )
            $t += $hasSeconds ? 1 : ( $hasTime ? 60 : 86400 );
        return array( 'ms' => $t * 1000, 'utc' => gmdate( 'Y-m-d\TH:i:s\Z', $t ) );
    }

    /**
     * The event names a 4.x audit file name stands for: AuditFileNames[<old name>]=<file>, then Map[<old name>].
     *
     * @param string $file
     * @param array|null $config
     * @return string[] (an impossible name when nothing maps, so the filter finds nothing)
     */
    public static function legacyNames( $file, ?array $config = null )
    {
        if ( $config === null && class_exists( 'expAuditConfig' ) )
            $config = expAuditConfig::get();
        $names = array();
        $files = isset( $config['auditFileNames'] ) ? $config['auditFileNames'] : array();
        $map = isset( $config['compatMap'] ) ? $config['compatMap'] : array();
        foreach ( $files as $old => $f )
        {
            if ( $f !== $file )
                continue;
            if ( isset( $map[$old] ) )
                $names[] = $map[$old];
            $names[] = 'system.legacy.' . str_replace( '-', '_', strtolower( $old ) );
            // one old name, several new ones (see doc/bc/6.0/audit.md "Compatibility mapping")
            if ( $old === 'content-delete' )
                $names = array_merge( $names, array( 'content.object.remove', 'content.object.purge' ) );
            if ( $old === 'content-hide' )
                $names[] = 'content.node.reveal';
            if ( $old === 'order-delete' )
                $names[] = 'commerce.order.purge';
            if ( $old === 'user-forgotpassword' )
                $names[] = 'access.user.password.reset.request';
        }
        return $names ? array_values( array_unique( $names ) ) : array( 'none.none.none' );
    }

    /**
     * The search terms of a text: lower-cased words of letters, digits and . _ - : / @.
     *
     * @param string $q
     * @return string[]
     */
    public static function terms( $q )
    {
        $q = function_exists( 'mb_strtolower' ) ? mb_strtolower( (string)$q, 'UTF-8' ) : strtolower( (string)$q );
        preg_match_all( '/[\p{L}\p{N}._:\/@-]+/u', $q, $m );
        $terms = array();
        foreach ( $m[0] as $t )
        {
            $t = trim( $t, '.-:/' );
            if ( $t !== '' )
                $terms[] = $t;
        }
        return array_slice( array_values( array_unique( $terms ) ), 0, 8 );
    }

    /**
     * The WHERE clause of filters (without "WHERE"), or "1=1".
     *
     * @param array $f normalised filters
     * @param string[]|null $channels the channels the user may read; null = all
     * @return string
     */
    public function where( array $f, ?array $channels = null )
    {
        $db = $this->db;
        $q = function ( $v ) use ( $db ) { return "'" . $db->escapeString( (string)$v ) . "'"; };
        $c = array();
        // a malformed filter finds nothing rather than everything (normalise() lists it)
        if ( !empty( $f['invalid'] ) )
            $c[] = '1=0';
        if ( $channels !== null )
            $c[] = $channels ? 'channel IN (' . implode( ', ', array_map( $q, $channels ) ) . ')' : '1=0';
        if ( isset( $f['channel'] ) )
            $c[] = 'channel = ' . $q( $f['channel'] );
        if ( isset( $f['name'] ) && $f['name'] !== '*' )
        {
            if ( substr( $f['name'], -2 ) === '.*' )
            {
                $prefix = substr( $f['name'], 0, -2 );
                $c[] = '(name = ' . $q( $prefix ) . ' OR name LIKE ' . $q( str_replace( '_', '\\_', $prefix ) . '.%' ) . ( $this->likeEscape() ) . ')';
            }
            else
                $c[] = 'name = ' . $q( $f['name'] );
        }
        if ( !empty( $f['names'] ) )
            $c[] = 'name IN (' . implode( ', ', array_map( $q, $f['names'] ) ) . ')';
        if ( isset( $f['domain'] ) )
            $c[] = 'domain_name = ' . $q( $f['domain'] );
        if ( isset( $f['user'] ) )
            $c[] = 'user_id = ' . (int)$f['user'];
        foreach ( array( 'login' => 'login', 'result' => 'result', 'request' => 'request_id', 'job' => 'job_id',
                         'run' => 'run_id', 'parent' => 'parent_id' ) as $k => $col )
            if ( isset( $f[$k] ) )
                $c[] = $col . ' = ' . $q( $f[$k] );
        foreach ( array( 'object', 'target' ) as $k )
        {
            if ( !isset( $f[$k] ) )
                continue;
            $c[] = $k . '_type = ' . $q( $f[$k][0] );
            if ( $f[$k][1] !== '' )
                $c[] = $k . '_id = ' . $q( $f[$k][1] );
        }
        if ( isset( $f['severity'] ) )
            $c[] = 'severity <= ' . (int)$f['severity'];
        if ( isset( $f['ip'] ) )
            $c[] = strpos( $f['ip'], '/' ) !== false || strpos( $f['ip'], 'h:' ) === 0
                 ? 'ip = ' . $q( $f['ip'] )
                 : 'ip LIKE ' . $q( $f['ip'] . '%' );
        if ( isset( $f['from_ms'] ) )
            $c[] = 'time_ms >= ' . (int)$f['from_ms'];
        if ( isset( $f['to_ms'] ) )
            $c[] = 'time_ms < ' . (int)$f['to_ms'];
        if ( isset( $f['q'] ) )
        {
            $text = $this->textCondition( $f['q'] );
            if ( $text !== '' )
                $c[] = $text;
        }
        return $c ? implode( ' AND ', $c ) : '1=1';
    }

    /** @return string the ESCAPE clause for LIKE with \ as escape character, where the engine needs it */
    protected function likeEscape()
    {
        return in_array( $this->type, array( 'sqlite', 'oracle' ), true ) ? " ESCAPE '\\'" : '';
    }

    /**
     * The search condition for a text, by the engine's full-text kind.
     *
     * @param string $text
     * @return string
     */
    public function textCondition( $text )
    {
        $terms = self::terms( $text );
        if ( !$terms )
            return '';
        $db = $this->db;
        switch ( $this->fullText )
        {
            case 'fts5':
                // the trigram tokenizer finds any part of the text, as LIKE does, for terms of three characters
                // or more; shorter terms are matched with LIKE. Each term is a quoted FTS5 string.
                $long = array_values( array_filter( $terms, function ( $t ) { return ( function_exists( 'mb_strlen' ) ? mb_strlen( $t, 'UTF-8' ) : strlen( $t ) ) >= 3; } ) );
                $short = array_values( array_diff( $terms, $long ) );
                $c = array();
                if ( $long )
                {
                    $match = implode( ' ', array_map( function ( $t ) { return '"' . str_replace( '"', '""', $t ) . '"'; }, $long ) );
                    $c[] = "rowid IN (SELECT rowid FROM " . expAuditIndexSchema::FTS . " WHERE " . expAuditIndexSchema::FTS
                           . " MATCH '" . $db->escapeString( $match ) . "')";
                }
                foreach ( $short as $t )
                    $c[] = $this->likeTerm( $t );
                return '(' . implode( ' AND ', $c ) . ')';
            case 'mysql':
                $match = implode( ' ', array_map( function ( $t ) { return '+"' . str_replace( '"', '', $t ) . '"'; }, $terms ) );
                return "MATCH (search_text) AGAINST ('" . $db->escapeString( $match ) . "' IN BOOLEAN MODE)";
            case 'postgresql':
                return "search_tsv @@ plainto_tsquery('simple', '" . $db->escapeString( implode( ' ', $terms ) ) . "')";
            case 'oracle':
                $match = implode( ' AND ', array_map( function ( $t ) { return '{' . str_replace( array( '{', '}' ), '', $t ) . '}'; }, $terms ) );
                return "CONTAINS (search_text, '" . $db->escapeString( $match ) . "') > 0";
        }
        $c = array();
        foreach ( $terms as $t )
            $c[] = $this->likeTerm( $t );
        return '(' . implode( ' AND ', $c ) . ')';
    }

    /**
     * @param string $term lower-cased
     * @return string search_text LIKE '%term%'
     */
    protected function likeTerm( $term )
    {
        return 'search_text LIKE ' . "'%" . $this->db->escapeString( str_replace( array( '\\', '%', '_' ), array( '\\\\', '\\%', '\\_' ), $term ) ) . "%'" . $this->likeEscape();
    }

    /**
     * Rows of the index, newest first.
     *
     * @param array $f
     * @param string[]|null $channels
     * @param int $offset
     * @param int $limit
     * @param string $columns
     * @return array[]|null null when the database refused the query
     */
    public function fetch( array $f, ?array $channels, $offset = 0, $limit = 50, $columns = '*' )
    {
        $limit = min( self::MAX_LIMIT, max( 1, (int)$limit ) );
        $sql = 'SELECT ' . $columns . ' FROM ' . expAuditIndexSchema::EVENT . ' WHERE ' . $this->where( $f, $channels )
             . ' ORDER BY time_ms DESC, id DESC';
        return expAuditIndexSchema::tryArrayQuery( $this->db, $sql, array( 'offset' => max( 0, (int)$offset ), 'limit' => $limit ) );
    }

    /**
     * @param array $f
     * @param string[]|null $channels
     * @return int|null
     */
    public function count( array $f, ?array $channels )
    {
        $rows = expAuditIndexSchema::tryArrayQuery( $this->db, 'SELECT COUNT(*) AS n FROM ' . expAuditIndexSchema::EVENT
                                                    . ' WHERE ' . $this->where( $f, $channels ) );
        return $rows === null ? null : (int)( isset( $rows[0]['n'] ) ? $rows[0]['n'] : reset( $rows[0] ) );
    }

    /**
     * One row by event id.
     *
     * @param string $id
     * @return array|null
     */
    public function byId( $id )
    {
        if ( !preg_match( '/^[0-9A-HJKMNP-TV-Z]{26}$/', (string)$id ) )
            return null;
        $rows = expAuditIndexSchema::tryArrayQuery( $this->db, 'SELECT * FROM ' . expAuditIndexSchema::EVENT
                                                    . " WHERE id = '" . $this->db->escapeString( $id ) . "'" );
        return $rows ? $rows[0] : null;
    }

    /**
     * Counts grouped by one or two columns.
     *
     * @param string[] $columns among channel, name, login, result, domain_name, severity, object_type
     * @param array $f
     * @param string[]|null $channels
     * @param int $limit
     * @return array[] rows with the columns and n, most first
     */
    public function groupCount( array $columns, array $f, ?array $channels, $limit = 10 )
    {
        $allowed = array( 'channel', 'name', 'login', 'result', 'domain_name', 'severity', 'object_type', 'user_id', 'ip', 'object_id',
                          'object_name', 'module_view', 'reason' );
        $columns = array_values( array_intersect( $columns, $allowed ) );
        if ( !$columns )
            return array();
        $cols = implode( ', ', $columns );
        $sql = "SELECT $cols, COUNT(*) AS n FROM " . expAuditIndexSchema::EVENT . ' WHERE ' . $this->where( $f, $channels )
             . " GROUP BY $cols ORDER BY n DESC";
        $rows = expAuditIndexSchema::tryArrayQuery( $this->db, $sql, array( 'limit' => max( 1, (int)$limit ) ) );
        return $rows === null ? array() : $rows;
    }

    /**
     * Counts per local day (and per one more column): the charts' time series.
     *
     * @param array $f
     * @param string[]|null $channels
     * @param string|null $by channel, result or name
     * @param int $offsetMs the site's time zone offset from UTC, milliseconds
     * @return array[] day (YYYY-MM-DD) => array( value => n )
     */
    public function perDay( array $f, ?array $channels, $by = null, $offsetMs = 0 )
    {
        $by = in_array( $by, array( 'channel', 'result', 'name', 'domain_name' ), true ) ? $by : null;
        $offsetMs = (int)$offsetMs;
        $expr = $this->dayExpression( $offsetMs );
        $rows = null;
        if ( $expr !== null )
        {
            $sql = "SELECT $expr AS d" . ( $by ? ", $by" : '' ) . ', COUNT(*) AS n FROM ' . expAuditIndexSchema::EVENT
                 . ' WHERE ' . $this->where( $f, $channels ) . " GROUP BY $expr" . ( $by ? ", $by" : '' );
            $rows = expAuditIndexSchema::tryArrayQuery( $this->db, $sql );
        }
        if ( $rows === null )
        {
            // an engine without the expression (the MongoDB emulation): the time column only, counted here
            $rows = array();
            $list = expAuditIndexSchema::tryArrayQuery( $this->db, 'SELECT time_ms' . ( $by ? ", $by" : '' ) . ' FROM '
                                                         . expAuditIndexSchema::EVENT . ' WHERE ' . $this->where( $f, $channels ),
                                                         array( 'limit' => 200000 ) );
            $tally = array();
            foreach ( (array)$list as $r )
            {
                $d = (int)floor( ( (int)$r['time_ms'] + $offsetMs ) / 86400000 );
                $k = $d . "\n" . ( $by ? (string)$r[$by] : '' );
                $tally[$k] = ( isset( $tally[$k] ) ? $tally[$k] : 0 ) + 1;
            }
            foreach ( $tally as $k => $n )
            {
                list( $d, $v ) = explode( "\n", $k, 2 );
                $rows[] = array( 'd' => $d, $by ?: 'x' => $v, 'n' => $n );
            }
        }
        $out = array();
        foreach ( $rows as $r )
        {
            $day = gmdate( 'Y-m-d', (int)$r['d'] * 86400 );
            $key = $by ? (string)$r[$by] : 'all';
            $out[$day][$key] = ( isset( $out[$day][$key] ) ? $out[$day][$key] : 0 ) + (int)$r['n'];
        }
        ksort( $out );
        return $out;
    }

    /**
     * The day number (days since 1970-01-01 in local time) of time_ms, per engine.
     *
     * @param int $offsetMs
     * @return string|null
     */
    protected function dayExpression( $offsetMs )
    {
        $t = '(time_ms + ' . (int)$offsetMs . ')';
        switch ( $this->type )
        {
            case 'sqlite':
            case 'postgresql':
                return "($t / 86400000)";
            case 'mysql':
                return "($t DIV 86400000)";
            case 'oracle':
                return "FLOOR($t / 86400000)";
        }
        return null;
    }
}
