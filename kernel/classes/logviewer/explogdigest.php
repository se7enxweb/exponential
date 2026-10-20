<?php
/**
 * The error digest of the log viewer (Setup > Log files > Error digest, setup/logsdigest; doc/en/us/features/6.0/log-viewer.md,
 * "Error digest"): the distinct messages of the last hours of each log, normalized and grouped, with their count and
 * when they were last seen, so a flood of the same error reads as one line with a number.
 *
 * - Bounded: each file is entered at the start of the window by a binary search on its times (expLogSearch::seekTime)
 *   and at most [DigestSettings] MaxBytesPerFile bytes are read from there; a file that held more says so (stopped).
 * - Masked: messages are masked (expLogRedactor, every mask on) BEFORE they are normalized and grouped, so neither the
 *   digest nor the search link it builds ever carries an e-mail or IP address, a token or a secret setting.
 * - Normalized: numbers, hexadecimal values, ids, quoted strings, paths and error references become placeholders, so
 *   "node 12 not found" and "node 13 not found" are one group.
 * - Per log ([DigestSettings] LogLevels[<key>], GroupBy[<key>]): other levels (404.log lists its notices) and another
 *   grouping (security.log by <category>/<rule>, 404.log by the reason of the not found answer); see groupText().
 * - Known: a group's id (the first 12 hexadecimal digits of the sha1 of its normalized text) can be marked as known per
 *   user (the preference admin_logviewer_known, a comma list); known groups are left out unless asked for.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogDigest
{
    /** The user preference that keeps the groups a user marked as known */
    const KNOWN_PREFERENCE = 'admin_logviewer_known';

    /** The most known groups kept per user (the oldest are forgotten first) */
    const MAX_KNOWN = 200;

    /** The longest normalized text kept for a group */
    const MAX_TEXT = 240;

    /** The defaults of [DigestSettings] */
    public static $defaults = array( 'Hours' => 24, 'MaxMessages' => 50, 'MaxBytesPerFile' => 8388608, 'Levels' => array( 'error', 'warning' ) );

    /** @var expLogViewerConfig */
    protected $config;

    /** @var expLogRedactor every mask on, whatever the viewer chose */
    protected $redactor;

    /**
     * @param expLogViewerConfig $config
     * @param expLogRedactor|null $redactor null: the masks of the settings, all on
     */
    public function __construct( expLogViewerConfig $config, ?expLogRedactor $redactor = null )
    {
        $this->config = $config;
        $this->redactor = $redactor !== null ? $redactor : new expLogRedactor( true, true, true, expLogRedactor::settingSecrets( $config ) );
    }

    /**
     * A setting of [DigestSettings], with its default and bounds.
     *
     * @param string $name Hours | MaxMessages | MaxBytesPerFile
     * @return int
     */
    public function setting( $name )
    {
        $value = $this->config->get( 'DigestSettings', $name );
        $value = is_numeric( $value ) ? (int)$value : (int)self::$defaults[$name];
        $bounds = array( 'Hours' => array( 1, 168 ), 'MaxMessages' => array( 1, 500 ), 'MaxBytesPerFile' => array( 65536, 268435456 ) );
        return max( $bounds[$name][0], min( $bounds[$name][1], $value ) );
    }

    /**
     * The levels a digest lists ([DigestSettings] Levels[], default error and warning).
     *
     * @return string[]
     */
    public function levels()
    {
        $levels = array_values( array_intersect( expLogParser::$levels, $this->config->lst( 'DigestSettings', 'Levels' ) ) );
        return $levels ? $levels : self::$defaults['Levels'];
    }

    /** The ways a digest groups a log's entries ([DigestSettings] GroupBy[<key>]) */
    const GROUPINGS = array( 'message', 'category-rule', 'reason', 'reason-kind', 'uri', 'directive-blocked' );

    /**
     * The levels a digest lists for one log: [DigestSettings] LogLevels[<key>] (a comma list), else levels().
     *
     * @param string $key
     * @return string[]
     */
    public function levelsFor( $key )
    {
        $perLog = $this->config->hash( 'DigestSettings', 'LogLevels' );
        if ( isset( $perLog[$key] ) )
        {
            $levels = array_values( array_intersect( expLogParser::$levels, array_map( 'trim', explode( ',', $perLog[$key] ) ) ) );
            if ( $levels )
                return $levels;
        }
        return $this->levels();
    }

    /**
     * How a digest groups the entries of one log: [DigestSettings] GroupBy[<key>], one of GROUPINGS; message by default.
     *
     * @param string $key
     * @return string
     */
    public function groupingFor( $key )
    {
        $groupBy = $this->config->hash( 'DigestSettings', 'GroupBy' );
        $mode = isset( $groupBy[$key] ) ? strtolower( $groupBy[$key] ) : 'message';
        return in_array( $mode, self::GROUPINGS, true ) ? $mode : 'message';
    }

    /**
     * The text an entry is grouped by, from its masked headline: the normalized message, or the field the grouping
     * names (doc/en/us/features/6.0/request-logs.md). An entry without that field is grouped by its message. The text is
     * also what the search literal of the group is taken from, so it is a run of the line as it is written. Pure.
     *
     *   category-rule  "probe/wp-login" (security.log: the first word, <category>/<rule>)
     *   reason         "reason=module:no-such-page", "reason=node:#" (404.log: ids, numbers and paths set aside)
     *   reason-kind    "reason=module:"
     *   uri            "GET /blog/#" (the method and the path, without the query; numbers set aside)
     *   directive-blocked "csp/script-src blocked=inline", "csp/img-src blocked=https://maps.example.com" (csp.log: the
     *                  directive and what it blocked, a URI cut to its origin)
     *
     * @param string $mode one of GROUPINGS
     * @param string $masked the masked headline of the entry
     * @return string
     */
    public static function groupText( $mode, $masked )
    {
        $masked = (string)$masked;
        switch ( $mode )
        {
            case 'category-rule':
                if ( preg_match( '#^([a-z0-9_\-]+/[A-Za-z0-9_.:\-]+)(?:\s|$)#', $masked, $m ) )
                    return $m[1];
                break;
            case 'reason':
            case 'reason-kind':
                if ( preg_match( '/(?:^|\s)reason=([a-z]+):(\S*)/', $masked, $m ) )
                {
                    if ( $mode === 'reason-kind' )
                        return 'reason=' . $m[1] . ':';
                    $detail = preg_replace( '/\d+/', '#', $m[2] );
                    if ( substr_count( $detail, '/' ) >= 2 && $m[1] === 'urlalias' )
                        $detail = '<path>';
                    if ( strlen( $detail ) > 120 )
                        $detail = substr( $detail, 0, 120 );
                    return 'reason=' . $m[1] . ':' . $detail;
                }
                break;
            case 'directive-blocked':
                if ( preg_match( '~^(csp/[a-z][a-z0-9\-]*) blocked=(\S+)~', $masked, $m ) )
                {
                    $blocked = $m[2];
                    if ( preg_match( '~^([a-z][a-z0-9+.\-]*://[^/?#]+)~i', $blocked, $o ) )
                        $blocked = $o[1];
                    return $m[1] . ' blocked=' . substr( $blocked, 0, 120 );
                }
                break;
            case 'uri':
                if ( preg_match( '#(?:^|\s)([A-Z]{3,10}) (/\S*)#', $masked, $m ) )
                {
                    $path = preg_replace( '/[?#].*$/', '', $m[2] );
                    $path = preg_replace( '/\d+/', '#', $path );
                    if ( strlen( $path ) > 160 )
                        $path = substr( $path, 0, 160 );
                    return $m[1] . ' ' . $path;
                }
                break;
        }
        return self::normalize( $masked );
    }

    /**
     * The text of an entry a digest groups: its first line, and the next one when the first is only a label
     * ("eZDB:"), as eZDebug writes them. Pure.
     *
     * @param string $message
     * @return string
     */
    public static function headline( $message )
    {
        $lines = explode( "\n", str_replace( "\r", '', (string)$message ) );
        $text = trim( $lines[0] );
        if ( isset( $lines[1] ) && ( $text === '' || preg_match( '/^\S{1,60}:$/', $text ) ) )
            $text = trim( $text . ' ' . trim( $lines[1] ) );
        return $text;
    }

    /**
     * The one line of an entry a search literal is taken from: the line of the message under a label alone, else the
     * first line, so the literal is a run of text the file holds as it is (a label and its message are on two lines).
     * Pure.
     *
     * @param string $message
     * @return string
     */
    public static function searchLine( $message )
    {
        $lines = explode( "\n", str_replace( "\r", '', (string)$message ) );
        $first = trim( $lines[0] );
        if ( isset( $lines[1] ) && ( $first === '' || preg_match( '/^\S{1,60}:$/', $first ) ) )
            return trim( $lines[1] );
        return $first;
    }

    /**
     * A message with what varies between occurrences replaced by placeholders: error references (ERR-?), UUIDs (<uuid>),
     * hexadecimal values (<hex>), quoted strings ('?'), paths (<path>), numbers (#); whitespace collapsed. Pure.
     *
     * @param string $text
     * @return string
     */
    public static function normalize( $text )
    {
        $t = (string)$text;
        $t = preg_replace( '/\bERR-[0-9A-F]{10}\b/', 'ERR-?', $t );
        $t = preg_replace( '/\b[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\b/i', '<uuid>', $t );
        $t = preg_replace( '/\b0x[0-9a-f]+\b/i', '<hex>', $t );
        $t = preg_replace( '/\b(?=[0-9a-f]*\d)(?=[0-9a-f]*[a-f])[0-9a-f]{8,}\b/i', '<hex>', $t );
        $t = preg_replace( '/"[^"\n]{0,200}"/', '"?"', $t );
        $t = preg_replace( "/'[^'\\n]{0,200}'/", "'?'", $t );
        $t = preg_replace( '#(?<![\w<])(?:[A-Za-z]:)?(?:[./~]?[\w.\-]+)?(?:/[\w.\-]+){2,}/?#', '<path>', $t );
        $t = preg_replace( '/(?<![A-Za-z])-?\d+(?:[.,:]\d+)*/', '#', $t );
        $t = trim( preg_replace( '/\s+/', ' ', $t ) );
        if ( strlen( $t ) > self::MAX_TEXT )
            $t = substr( $t, 0, self::MAX_TEXT );
        return $t;
    }

    /**
     * The id of a group: the first 12 hexadecimal digits of the sha1 of its log key and normalized text. Pure.
     *
     * @param string $key
     * @param string $normalized
     * @return string
     */
    public static function groupId( $key, $normalized )
    {
        return substr( sha1( $key . "\n" . $normalized ), 0, 12 );
    }

    /**
     * A literal to search a group's entries with: the longest part of the normalized text without a placeholder (and
     * without the masks of the redactor), at most 80 characters; '' when no part has 4 characters. Pure.
     *
     * @param string $normalized
     * @return string
     */
    public static function literalOf( $normalized )
    {
        $best = '';
        foreach ( preg_split( '/ERR-\?|<uuid>|<hex>|<path>|"\?"|\'\?\'|#|\*\*\*|\.x\.x/', (string)$normalized ) as $part )
        {
            $part = trim( $part, " \t:;,.=()[]{}" );
            if ( strlen( $part ) > strlen( $best ) )
                $best = $part;
        }
        if ( strlen( $best ) > 80 )
            $best = trim( substr( $best, 0, 80 ) );
        return strlen( $best ) >= 4 ? $best : '';
    }

    /**
     * The known group ids from a preference value. Pure.
     *
     * @param mixed $value
     * @return string[]
     */
    public static function knownFrom( $value )
    {
        if ( !is_string( $value ) || $value === '' )
            return array();
        $out = array();
        foreach ( explode( ',', $value ) as $id )
            if ( preg_match( '/^[0-9a-f]{12}$/', $id ) )
                $out[$id] = true;
        return array_slice( array_keys( $out ), -self::MAX_KNOWN );
    }

    /**
     * A preference value with a group marked known (or not). Pure.
     *
     * @param mixed $value the current value
     * @param string $id
     * @param bool $known
     * @return string
     */
    public static function withKnown( $value, $id, $known )
    {
        $ids = array_values( array_diff( self::knownFrom( $value ), array( $id ) ) );
        if ( $known && preg_match( '/^[0-9a-f]{12}$/', (string)$id ) )
            $ids[] = (string)$id;
        return implode( ',', array_slice( $ids, -self::MAX_KNOWN ) );
    }

    /**
     * The digest of one file since a time.
     *
     * @param array $file a resolved file (expLogFileLocator), with its key
     * @param int $since
     * @param string[] $known group ids left out (counted in hidden)
     * @param bool $showKnown list the known groups too (marked known)
     * @return array key, id, label, groups (each: id, key, level, text (normalized, masked), sample (the first line of the
     *               newest entry, masked), literal, count, first, last (Unix times), last_offset, known), total (entries
     *               counted), hidden (known groups left out), scanned (bytes), stopped (bool: the byte cap was reached),
     *               grouping (groupingFor()), levels (levelsFor())
     */
    public function digestFile( array $file, $since, array $known = array(), $showKnown = false )
    {
        $key = isset( $file['key'] ) ? (string)$file['key'] : (string)$file['id'];
        $out = array( 'key' => $key, 'id' => $file['id'], 'label' => isset( $file['label'] ) ? $file['label'] : $file['name'],
                      'groups' => array(), 'total' => 0, 'hidden' => 0, 'scanned' => 0, 'stopped' => false,
                      'grouping' => $this->groupingFor( $key ), 'levels' => $this->levelsFor( $key ) );
        $cap = $this->setting( 'MaxBytesPerFile' );
        $levels = $out['levels'];
        $grouping = $out['grouping'];
        $knownSet = array_flip( $known );
        $groups = array();
        $handle = !empty( $file['gz'] ) ? @gzopen( $file['path'], 'rb' ) : @fopen( $file['path'], 'rb' );
        if ( $handle === false )
            return $out;
        $offset = empty( $file['gz'] ) ? expLogSearch::seekTime( $file['path'], (int)$since, (int)$file['size'] ) : 0;
        if ( $offset > 0 )
            fseek( $handle, $offset );
        $pending = '';
        $pendingStart = $offset;
        $pos = $offset;
        $flush = function ( $text, $start ) use ( &$groups, &$out, $file, $key, $since, $levels, $grouping )
        {
            if ( $text === '' )
                return;
            foreach ( expLogParser::parse( $text, $start, isset( $file['level'] ) ? $file['level'] : '' ) as $entry )
            {
                if ( $entry['cont'] || $entry['time'] === null || $entry['time'] < $since || !in_array( $entry['level'], $levels, true ) )
                    continue;
                $masked = $this->redactor->text( self::headline( $entry['message'] ) );
                $normalized = $grouping === 'message' ? self::normalize( $masked ) : self::groupText( $grouping, $masked );
                if ( $normalized === '' )
                    continue;
                $id = self::groupId( $key, $normalized );
                $out['total']++;
                if ( !isset( $groups[$id] ) )
                    $groups[$id] = array( 'id' => $id, 'key' => $key, 'level' => $entry['level'], 'text' => $normalized,
                                          'sample' => '',
                                          'literal' => $grouping === 'message'
                                                       ? self::literalOf( self::normalize( $this->redactor->text( self::searchLine( $entry['message'] ) ) ) )
                                                       : self::literalOf( $normalized ),
                                          'count' => 0,
                                          'first' => $entry['time'], 'last' => $entry['time'], 'last_offset' => $entry['offset'], 'known' => false );
                $g =& $groups[$id];
                $g['count']++;
                if ( $entry['time'] >= $g['last'] )
                {
                    $g['last'] = $entry['time'];
                    $g['last_offset'] = $entry['offset'];
                    $g['sample'] = strlen( $masked ) > 300 ? substr( $masked, 0, 300 ) : $masked;
                }
                if ( $entry['time'] < $g['first'] )
                    $g['first'] = $entry['time'];
                // the most severe level of the group
                if ( array_search( $entry['level'], expLogParser::$levels, true ) < array_search( $g['level'], expLogParser::$levels, true ) )
                    $g['level'] = $entry['level'];
                unset( $g );
            }
        };
        // read whole entries, a block at a time, never more than the cap
        while ( $out['scanned'] < $cap )
        {
            $block = !empty( $file['gz'] ) ? gzread( $handle, 262144 ) : fread( $handle, (int)min( 262144, $cap - $out['scanned'] ) );
            if ( $block === false || $block === '' )
                break;
            $out['scanned'] += strlen( $block );
            $pending .= $block;
            $pos += strlen( $block );
            // keep the last line, and the entry it may belong to, for the next block
            $cut = strrpos( $pending, "\n[" );
            if ( $cut === false )
                $cut = strrpos( $pending, "\n" );
            if ( $cut !== false && strlen( $pending ) > 1048576 )
            {
                $flush( substr( $pending, 0, $cut + 1 ), $pendingStart );
                $pendingStart += $cut + 1;
                $pending = (string)substr( $pending, $cut + 1 );
            }
        }
        $more = !empty( $file['gz'] ) ? !gzeof( $handle ) : !feof( $handle ) && ftell( $handle ) < (int)$file['size'];
        $out['stopped'] = $out['scanned'] >= $cap && $more;
        !empty( $file['gz'] ) ? gzclose( $handle ) : fclose( $handle );
        $flush( $pending, $pendingStart );
        // most frequent first, then the newest
        usort( $groups, function ( $a, $b ) {
            if ( $a['count'] !== $b['count'] )
                return $a['count'] > $b['count'] ? -1 : 1;
            return $a['last'] === $b['last'] ? 0 : ( $a['last'] > $b['last'] ? -1 : 1 );
        } );
        foreach ( $groups as $g )
        {
            if ( isset( $knownSet[$g['id']] ) )
            {
                if ( !$showKnown )
                {
                    $out['hidden']++;
                    continue;
                }
                $g['known'] = true;
            }
            if ( count( $out['groups'] ) < $this->setting( 'MaxMessages' ) )
                $out['groups'][] = $g;
        }
        return $out;
    }

    /**
     * The permalink of a group: the log, filtered by the group's literal and level, from the window's start. Pure.
     *
     * A log grouped by uri (404.log, security.log) also gets the request filter: the group's path up to its first
     * placeholder, as the masks show it, which the search compares with the request as the viewer is shown it.
     *
     * @param array $group
     * @param int $since
     * @param string $grouping the log's (groupingFor()); 'message' for the links before it was given
     * @return string a path for ezurl
     */
    public static function link( array $group, $since, $grouping = 'message' )
    {
        $state = array( 'levels' => array( $group['level'] ), 'from' => date( 'Y-m-d H:i', (int)$since ) );
        if ( $group['literal'] !== '' )
            $state['q'] = $group['literal'];
        $uri = $grouping === 'uri' ? self::uriFilterOf( (string)$group['text'] ) : '';
        if ( $uri !== '' )
            $state['uri'] = $uri;
        return expLogPermalink::build( $group['key'], $state );
    }

    /**
     * The request filter of a group's text of the uri grouping ("GET /blog/#"): the path up to its first placeholder
     * (/blog/), '' when that is no more than "/" or the text is not of that grouping. The text is masked already, so
     * the filter holds no value a masked viewer is not shown. Pure.
     *
     * @param string $text
     * @return string
     */
    public static function uriFilterOf( $text )
    {
        if ( !preg_match( '#^[A-Z]{3,10} (/\S*)$#', (string)$text, $m ) )
            return '';
        $path = $m[1];
        $cut = strcspn( $path, '#<' );
        $path = substr( $path, 0, $cut );
        return strlen( $path ) > 1 ? $path : '';
    }

    /**
     * The permalink of a group's newest entry: the log, paused at it. Pure.
     *
     * @param array $group
     * @return string
     */
    public static function entryLink( array $group )
    {
        return expLogPermalink::build( $group['key'], array( 'offset' => (int)$group['last_offset'], 'follow' => false ) );
    }
}
