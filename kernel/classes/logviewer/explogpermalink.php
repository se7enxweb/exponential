<?php
/**
 * The permalinks of the log viewer (doc/features/6.0/log-viewer.md, "Permalinks"): every view of a log as a path
 * without a query string, short enough to type and safe to bookmark:
 *
 *   /setup/logs/view/<key>[/(name)/<value>]...
 *   /setup/logs/view/error
 *   /setup/logs/view/error/(level)/error,warning/(search)/timeout/(follow)/0/(offset)/20480
 *   /setup/logs/view/webserver-access/(archive)/1/(search)/~2Fadmin~2F
 *
 * A value is written as is when it is made of letters, digits, - _ . and , only; any other character is ~ and its
 * two hexadecimal digits (a space ~20, a slash ~2F, a ~ itself ~7E), so a value never holds a / or a ( that would
 * end it. The page's script writes the same (page/setup_logs.js). The names, in the order a link writes them:
 * archive (the rotation N: .N, or .N.gz when there is no plain one; (rotation)/N is read as the same), level, search,
 * regex, case, from, to, siteaccess, uri, ip, reference, scope, rotations, cursor, offset,
 * follow, wrap, unmask, done, ok. A value that is the default is left out. Pure: nothing here reads a file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogPermalink
{
    /** The view of the page */
    const VIEW = 'setup/logs/view';

    /** view parameter => state field, in the order a link writes them */
    const PARAMS = array( 'archive' => 'rotation', 'level' => 'levels', 'search' => 'q', 'regex' => 'regex', 'case' => 'case',
                          'from' => 'from', 'to' => 'to', 'siteaccess' => 'siteaccess', 'uri' => 'uri', 'ip' => 'ip',
                          'reference' => 'reference', 'scope' => 'scope', 'rotations' => 'rotations', 'cursor' => 'cursor',
                          'offset' => 'offset', 'follow' => 'follow', 'wrap' => 'wrap', 'unmask' => 'unmask', 'done' => 'done',
                          'ok' => 'ok' );

    /** The flags (1 or 0) and their defaults */
    const FLAGS = array( 'regex' => false, 'case' => false, 'rotations' => false, 'follow' => true, 'wrap' => false, 'unmask' => false, 'ok' => false );

    /** The longest value kept */
    const MAX_VALUE = 300;

    /**
     * A value as a permalink writes it.
     *
     * @param string $value
     * @return string
     */
    public static function encode( $value )
    {
        return preg_replace_callback( '/[^A-Za-z0-9_.,\-]/', function ( $m ) {
            return '~' . strtoupper( bin2hex( $m[0] ) );
        }, (string)$value );
    }

    /**
     * A value of a permalink as it was written (a ~ that is not followed by two hexadecimal digits stays).
     *
     * @param string $value
     * @return string
     */
    public static function decode( $value )
    {
        return preg_replace_callback( '/~([0-9A-Fa-f]{2})/', function ( $m ) {
            return chr( hexdec( $m[1] ) );
        }, (string)$value );
    }

    /**
     * The state of a view, with every field at its default.
     *
     * @return array
     */
    public static function defaults()
    {
        return array( 'rotation' => 0, 'levels' => array(), 'q' => '', 'regex' => false, 'case' => false, 'from' => '', 'to' => '',
                      'siteaccess' => '', 'uri' => '', 'ip' => '', 'reference' => '', 'scope' => 'file', 'rotations' => false,
                      'cursor' => '', 'offset' => -1, 'follow' => true, 'wrap' => false, 'unmask' => false, 'done' => '', 'ok' => false );
    }

    /**
     * A state from the view parameters of a request ($Params of the view: the unordered parameters by name) or from
     * any array of the same names (the legacy query string uses the state field names: q, levels ...). Values are
     * decoded when $encoded; unknown names and malformed values are dropped.
     *
     * @param array $in name => value
     * @param bool $encoded the values are written as in a permalink
     * @return array the state (defaults())
     */
    public static function state( array $in, $encoded = true )
    {
        $s = self::defaults();
        foreach ( self::PARAMS as $param => $field )
        {
            // the view parameter, else the state field of the same value ((rotation)/N for (archive)/N, the names of
            // the old query string); a parameter the request did not give is false in a view's $Params
            $raw = null;
            if ( isset( $in[$param] ) && $in[$param] !== false )
                $raw = $in[$param];
            else if ( isset( $in[$field] ) && $in[$field] !== false )
                $raw = $in[$field];
            if ( $raw === null || $raw === false )
                continue;
            if ( is_array( $raw ) )
                $raw = implode( ',', array_filter( $raw, 'is_scalar' ) );
            if ( !is_scalar( $raw ) )
                continue;
            $v = $encoded ? self::decode( (string)$raw ) : (string)$raw;
            $v = trim( preg_replace( '/[\x00-\x1F\x7F]+/', '', $v ) );
            if ( strlen( $v ) > self::MAX_VALUE )
                $v = substr( $v, 0, self::MAX_VALUE );
            if ( array_key_exists( $field, self::FLAGS ) )
                $s[$field] = in_array( strtolower( $v ), array( '1', 'yes', 'true', 'on' ), true );
            else if ( $field === 'levels' )
                $s['levels'] = array_values( array_intersect( expLogParser::$levels, array_map( 'strtolower', array_map( 'trim', explode( ',', $v ) ) ) ) );
            else if ( $field === 'rotation' )
                $s['rotation'] = preg_match( '/^-?\d{1,4}$/', $v ) ? max( -1, (int)$v ) : 0;
            else if ( $field === 'offset' )
                $s['offset'] = preg_match( '/^\d{1,15}$/', $v ) ? (int)$v : -1;
            else if ( $field === 'scope' )
                $s['scope'] = in_array( $v, array( 'file', 'kernel', 'all' ), true ) ? $v : 'file';
            else if ( $field === 'cursor' )
                $s['cursor'] = preg_match( '/^\d{1,4}:\d{1,15}$/', $v ) ? $v : '';
            else if ( $field === 'done' )
                $s['done'] = in_array( $v, array( 'rotate', 'truncate', 'delete' ), true ) ? $v : '';
            else
                $s[$field] = $v;
        }
        return $s;
    }

    /**
     * The permalink of a log and a state: "setup/logs/view/<key>/(name)/value...", for ezurl. Fields at their default
     * are left out, so a plain view is just setup/logs/view/<key>.
     *
     * @param string $key
     * @param array $state (defaults() for missing fields)
     * @param string[] $omit fields left out whatever they hold (e.g. done and ok for a link to share)
     * @return string
     */
    public static function build( $key, array $state = array(), array $omit = array() )
    {
        $s = $state + self::defaults();
        $d = self::defaults();
        $url = self::VIEW . '/' . self::encode( $key );
        foreach ( self::PARAMS as $param => $field )
        {
            if ( in_array( $field, $omit, true ) )
                continue;
            $v = $s[$field];
            if ( $v === $d[$field] || ( $field === 'levels' && !$v ) )
                continue;
            if ( is_bool( $v ) )
                $v = $v ? '1' : '0';
            else if ( is_array( $v ) )
                $v = implode( ',', $v );
            $v = (string)$v;
            if ( $v === '' )
                continue;
            $url .= '/(' . $param . ')/' . self::encode( $v );
        }
        return $url;
    }

    /**
     * A state as a link to share writes it: the request and client address filters as the masks of the settings show
     * them (expLogRedactor::fromConfig( $config, false )), so a link made from an unmasked view never carries an
     * address or a value a masked viewer is not shown. The search compares those filters with what the viewer is
     * shown, so the masked filters find the same entries, or more (203.0.x.x: every address shown so). Pure.
     *
     * @param array $state (defaults() for missing fields)
     * @param expLogRedactor $redactor with the masks of the settings, never an unmasked viewer's
     * @return array
     */
    public static function shareable( array $state, expLogRedactor $redactor )
    {
        $s = $state + self::defaults();
        if ( $s['ip'] !== '' )
            $s['ip'] = $redactor->ipFilterOf( $s['ip'] );
        if ( $s['uri'] !== '' )
            $s['uri'] = $redactor->text( $s['uri'] );
        return $s;
    }

    /**
     * Whether a state asks for a search (any criterion set).
     *
     * @param array $s
     * @return bool
     */
    public static function searching( array $s )
    {
        return $s['q'] !== '' || $s['levels'] || $s['from'] !== '' || $s['to'] !== '' || $s['siteaccess'] !== '' || $s['uri'] !== ''
               || $s['ip'] !== '' || $s['reference'] !== '';
    }

    /**
     * The search criteria of a state, as expLogSearch::criteria() reads them.
     *
     * @param array $s
     * @return array
     */
    public static function criteriaInput( array $s )
    {
        return array( 'q' => $s['q'], 'regex' => $s['regex'], 'case' => $s['case'], 'levels' => $s['levels'], 'from' => $s['from'],
                      'to' => $s['to'], 'siteaccess' => $s['siteaccess'], 'uri' => $s['uri'], 'ip' => $s['ip'], 'reference' => $s['reference'] );
    }
}
