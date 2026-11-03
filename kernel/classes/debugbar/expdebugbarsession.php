<?php
/**
 * File containing the expDebugBarSession class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The current user's own PHP session for the User tab of the Exp Debug bar (section "Session", loaded when opened by
 * the server function expdebugbar::session): every key of $_SESSION as a tree with its type, size and value, grouped by
 * the active extension whose name prefixes the key, values of secrets masked, and the frame of the session (handler,
 * lifetimes, cookie). Only ever the session of the request that asks; there is no way to read another one.
 *
 * Read only. Everything but current() works without the kernel, so the tests give it arrays.
 */
class expDebugBarSession
{
    /** Deepest level of nested arrays and objects shown; deeper ones are summarized */
    const MAX_DEPTH = 8;

    /** Children shown per array or object; the rest is counted */
    const MAX_CHILDREN = 200;

    /** Nodes in one answer at most, so a huge session cannot flood the page */
    const MAX_NODES = 3000;

    /** A string value longer than this many characters is cut (the length is given) */
    const VALUE_MAX = 300;

    /** A session larger than this many bytes (serialized) is marked */
    const LARGE_BYTES = 65536;

    /** Masks when debugbar.ini [DebugBarSettings] SessionMask[] is empty: parts of key names, case-insensitive */
    const DEFAULT_MASKS = array( 'token', 'csrf', 'nonce', 'secret', 'passw', 'hash', 'key$', 'credential', 'auth',
                                 'jwt', 'bearer', 'otp', 'saml', 'assertion', 'ticket', 'sid$' );

    /**
     * The section's data for the current request: its own session, nothing else.
     *
     * @param bool $privileged the user has setup/setup: the frame shows the save path and the handler class
     * @return array forSession()
     */
    public static function current( $privileged )
    {
        $started = class_exists( 'eZSession', false ) && eZSession::hasStarted();
        $session = $started && isset( $_SESSION ) && is_array( $_SESSION ) ? $_SESSION : array();

        $ini = eZINI::instance( 'site.ini' );
        $settings = array();
        foreach ( array( 'SessionTimeout', 'ActivityTimeout', 'CookieTimeout', 'RememberMeTimeout' ) as $name )
            $settings[$name] = $ini->hasVariable( 'Session', $name ) ? (string)$ini->variable( 'Session', $name ) : '';

        $barIni = eZINI::instance( 'debugbar.ini' );
        $masks = $barIni->hasVariable( 'DebugBarSettings', 'SessionMask' ) ? (array)$barIni->variable( 'DebugBarSettings', 'SessionMask' ) : array();

        $handler = '';
        if ( $privileged && class_exists( 'eZSession', false ) && method_exists( 'eZSession', 'getHandlerInstance' ) )
        {
            $instance = eZSession::getHandlerInstance();
            $handler = is_object( $instance ) ? get_class( $instance ) : '';
        }
        $cookie = session_get_cookie_params();
        $frame = array(
            'started'        => $started,
            'name'           => self::text( session_name() ),
            'id'             => $started ? (string)session_id() : '',
            'handler'        => $handler,
            'save_handler'   => (string)ini_get( 'session.save_handler' ),
            'save_path'      => $privileged ? (string)ini_get( 'session.save_path' ) : null,
            'gc_maxlifetime' => (int)ini_get( 'session.gc_maxlifetime' ),
            'cookie'         => array(
                'lifetime' => isset( $cookie['lifetime'] ) ? (int)$cookie['lifetime'] : 0,
                'path'     => isset( $cookie['path'] ) ? self::text( $cookie['path'] ) : '',
                'domain'   => isset( $cookie['domain'] ) ? self::text( $cookie['domain'] ) : '',
                'secure'   => !empty( $cookie['secure'] ),
                'httponly' => !empty( $cookie['httponly'] ),
                'samesite' => isset( $cookie['samesite'] ) ? (string)$cookie['samesite'] : '',
            ),
            'settings'       => $settings,
        );
        return self::forSession( $session, $frame, eZExtension::activeExtensions(), $masks );
    }

    /**
     * @param array    $session    the session's variables
     * @param array    $frame      see current()
     * @param string[] $extensions the active extensions, to group keys by their prefix
     * @param string[] $masks      parts of key names whose values are masked (regular expressions without delimiters,
     *                             case-insensitive); empty for DEFAULT_MASKS
     * @return array count, bytes, large, truncated, left (keys not shown once the budget was used up), groups
     *               (extension, keys, bytes, entries), frame
     */
    public static function forSession( array $session, array $frame, array $extensions = array(), array $masks = array() )
    {
        $masks = self::masks( $masks );
        $budget = self::MAX_NODES;
        $groups = array();
        $bytes = 0;
        ksort( $session, SORT_NATURAL | SORT_FLAG_CASE );
        $left = 0;
        foreach ( $session as $key => $value )
        {
            // the budget used up: the rest of the keys is only counted (count), not shown
            if ( $budget <= 0 )
            {
                $left++;
                $bytes += self::bytes( $value );
                continue;
            }
            $node = self::node( (string)$key, $value, 0, $budget, $masks, false );
            $extension = self::extensionOf( (string)$key, $extensions );
            $id = $extension === null ? '' : $extension;
            if ( !isset( $groups[$id] ) )
                $groups[$id] = array( 'extension' => $extension, 'keys' => 0, 'bytes' => 0, 'entries' => array() );
            $groups[$id]['keys']++;
            $groups[$id]['bytes'] += $node['bytes'];
            $groups[$id]['entries'][] = $node;
            $bytes += $node['bytes'];
        }
        // the kernel and keys of no extension first, then the extensions by name
        uksort( $groups, function ( $a, $b ) {
            if ( $a === '' || $b === '' )
                return $a === '' ? -1 : 1;
            return strcmp( $a, $b );
        } );
        $frame['id'] = self::shortId( isset( $frame['id'] ) ? (string)$frame['id'] : '' );
        return array(
            'count'     => count( $session ),
            'bytes'     => $bytes,
            'large'     => $bytes > self::LARGE_BYTES,
            'truncated' => $left > 0 || self::cut( $groups ),
            'left'      => $left,
            'groups'    => array_values( $groups ),
            'frame'     => $frame,
        );
    }

    /** Whether a node of the groups lists fewer children than it has (the budget or MAX_CHILDREN). */
    protected static function cut( array $groups )
    {
        $walk = function ( array $nodes ) use ( &$walk ) {
            foreach ( $nodes as $node )
            {
                if ( $node['more'] > 0 || ( is_array( $node['children'] ) && $walk( $node['children'] ) ) )
                    return true;
            }
            return false;
        };
        foreach ( $groups as $group )
        {
            if ( $walk( $group['entries'] ) )
                return true;
        }
        return false;
    }

    /**
     * One key with its value as a node: type, size (serialized), value (scalars, cut) or children.
     *
     * @param int  $budget nodes still allowed in this answer (counted down)
     * @param bool $masked a parent key matched a mask: everything below is masked too
     * @return array key, type, bytes, masked, value, length, cut, children, more, depth_cut
     */
    public static function node( $key, $value, $depth, &$budget, array $masks, $masked )
    {
        $budget--;
        $masked = $masked || self::isMasked( $key, $masks );
        $node = array( 'key' => self::text( $key ), 'type' => self::text( self::type( $value ) ), 'bytes' => self::bytes( $value ), 'masked' => $masked,
                       'value' => null, 'length' => null, 'cut' => false, 'children' => null, 'more' => 0, 'depth_cut' => false );
        if ( is_array( $value ) || is_object( $value ) )
        {
            $items = is_array( $value ) ? $value : self::objectVars( $value );
            $node['length'] = count( $items );
            if ( $masked )
                return $node;
            if ( $depth + 1 >= self::MAX_DEPTH )
            {
                $node['depth_cut'] = $node['length'] > 0;
                return $node;
            }
            $node['children'] = array();
            $shown = 0;
            foreach ( $items as $childKey => $childValue )
            {
                if ( $shown >= self::MAX_CHILDREN || $budget <= 0 )
                {
                    $node['more'] = $node['length'] - $shown;
                    break;
                }
                $node['children'][] = self::node( (string)$childKey, $childValue, $depth + 1, $budget, $masks, false );
                $shown++;
            }
            return $node;
        }
        if ( is_string( $value ) )
        {
            $node['length'] = preg_match( '//u', $value ) ? mb_strlen( $value, 'UTF-8' ) : strlen( $value );
            if ( $masked )
                return $node;
            if ( !preg_match( '//u', $value ) )
            {
                $node['value'] = '0x' . bin2hex( substr( $value, 0, (int)( self::VALUE_MAX / 2 ) ) );
                $node['cut'] = strlen( $value ) > self::VALUE_MAX / 2;
                $node['type'] = 'binary';
                return $node;
            }
            $node['cut'] = $node['length'] > self::VALUE_MAX;
            $node['value'] = $node['cut'] ? mb_substr( $value, 0, self::VALUE_MAX, 'UTF-8' ) : $value;
            return $node;
        }
        if ( $masked )
            return $node;
        if ( is_bool( $value ) )
            $node['value'] = $value ? 'true' : 'false';
        elseif ( $value === null )
            $node['value'] = 'null';
        else
            $node['value'] = (string)$value;
        return $node;
    }

    /**
     * Whether the values of a key are masked: a part of its name matches one of the masks (case-insensitive).
     *
     * @param string[] $masks masks() of the configuration
     */
    public static function isMasked( $key, array $masks )
    {
        foreach ( $masks as $mask )
        {
            if ( @preg_match( '~' . str_replace( '~', '\~', $mask ) . '~i', $key ) === 1 )
                return true;
        }
        return false;
    }

    /**
     * The masks in effect: the configured ones without empty entries, or DEFAULT_MASKS. An invalid expression is
     * taken as plain text.
     *
     * @return string[]
     */
    public static function masks( array $configured )
    {
        $masks = array();
        foreach ( $configured as $mask )
        {
            $mask = trim( (string)$mask );
            if ( $mask === '' )
                continue;
            $masks[] = @preg_match( '~' . str_replace( '~', '\~', $mask ) . '~i', '' ) === false ? preg_quote( $mask, '~' ) : $mask;
        }
        return $masks ? $masks : self::DEFAULT_MASKS;
    }

    /**
     * The active extension whose name (followed by "_") starts the key, the longest one; null for none.
     *
     * @param string[] $extensions
     */
    public static function extensionOf( $key, array $extensions )
    {
        $found = null;
        foreach ( $extensions as $extension )
        {
            $extension = (string)$extension;
            if ( $extension !== '' && stripos( $key, $extension . '_' ) === 0 && ( $found === null || strlen( $extension ) > strlen( $found ) ) )
                $found = $extension;
        }
        return $found;
    }

    /** The session id shortened to its first four characters, so a screenshot does not hand over the session. */
    public static function shortId( $id )
    {
        $id = (string)$id;
        return $id === '' ? '' : substr( $id, 0, 4 ) . '…';
    }

    /** The type as shown: string, int, float, bool, null, array, object <class>, resource */
    public static function type( $value )
    {
        if ( is_object( $value ) )
            return 'object ' . get_class( $value );
        $type = gettype( $value );
        $names = array( 'integer' => 'int', 'double' => 'float', 'boolean' => 'bool', 'NULL' => 'null', 'resource (closed)' => 'resource' );
        return isset( $names[$type] ) ? $names[$type] : $type;
    }

    /** Bytes of the value as the session stores it (serialized); 0 when it cannot be serialized. */
    public static function bytes( $value )
    {
        try
        {
            return strlen( serialize( $value ) );
        }
        catch ( Throwable $e )
        {
            // a closure or another object that refuses to be serialized
            return 0;
        }
    }

    /** A text as valid UTF-8 for the JSON answer: as it is, or its bytes as hex ("0x…") when it is not UTF-8. */
    public static function text( $value )
    {
        $value = (string)$value;
        return preg_match( '//u', $value ) ? $value : '0x' . bin2hex( $value );
    }

    /** The properties of an object as the session keeps them (all visibilities, as a cast to array gives them). */
    protected static function objectVars( $object )
    {
        $vars = array();
        foreach ( (array)$object as $name => $value )
        {
            // a cast marks protected ("\0*\0name") and private ("\0Class\0name") properties
            $parts = explode( "\0", (string)$name );
            $vars[end( $parts )] = $value;
        }
        return $vars;
    }
}
