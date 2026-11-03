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

    /** A session larger than this many bytes (serialized) is marked, unless debugbar.ini SessionWarnBytes says otherwise */
    const LARGE_BYTES = 65536;

    /** Masks when debugbar.ini [DebugBarSettings] SessionMask[] is empty: parts of key names, case-insensitive */
    const DEFAULT_MASKS = array( 'token', 'csrf', 'nonce', 'secret', 'passw', 'hash', 'key$', 'credential', 'auth',
                                 'jwt', 'bearer', 'otp', 'saml', 'assertion', 'ticket', 'sid$' );

    /**
     * Values that are secrets whatever their key is called (always on; debugbar.ini SessionValueMask[] adds more):
     * a JSON Web Token anywhere in the text, an Authorization header's credentials, a PEM private key, and texts that
     * are a token as a whole: well-known API key prefixes, 32 or more hex digits.
     */
    const SECRET_VALUE_PATTERNS = array(
        'jwt'    => '~eyJ[A-Za-z0-9_-]{5,}\.[A-Za-z0-9_-]{5,}\.[A-Za-z0-9_-]*~',
        'bearer' => '~\b(?:bearer|basic|digest)\s+(?=[A-Za-z0-9._+/=-]*[0-9])[A-Za-z0-9._+/=-]{12,}~i',
        'pem'    => '~-----BEGIN [A-Z ]*PRIVATE KEY-----~',
        'prefix' => '~^(?:sk|pk|rk)_(?:live|test)_[A-Za-z0-9]{10,}$|^gh[pousr]_[A-Za-z0-9]{20,}$|^github_pat_[A-Za-z0-9_]{20,}$|^xox[abprs]-[A-Za-z0-9-]{10,}$|^glpat-[A-Za-z0-9_-]{16,}$|^AKIA[0-9A-Z]{16}$|^sk-[A-Za-z0-9_-]{20,}$~',
        'hex'    => '~^[A-Fa-f0-9]{32,}$~',
    );

    /** A text without spaces at least this long, of base64 characters with digits and both cases, is a random token */
    const RANDOM_TOKEN_LENGTH = 32;

    /** ... when its characters are this varied (Shannon entropy, bits per character); words and slugs stay below */
    const RANDOM_TOKEN_ENTROPY = 4.1;

    /**
     * The section's data for the current request: its own session, nothing else.
     *
     * @param bool $privileged the user may read what setup/setup reads (expDebugBarServerFunctions::canReadSetup()):
     *                         the frame shows the save path and the handler class
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
        $valueMasks = $barIni->hasVariable( 'DebugBarSettings', 'SessionValueMask' ) ? (array)$barIni->variable( 'DebugBarSettings', 'SessionValueMask' ) : array();

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
        return self::forSession( $session, $frame, eZExtension::activeExtensions(), $masks,
                                 array( 'value_masks' => $valueMasks, 'warn_bytes' => self::warnBytes() ) );
    }

    /**
     * The size from which a session is marked as large: debugbar.ini [DebugBarSettings] SessionWarnBytes, LARGE_BYTES
     * when it is not set or not a positive number. Every request of the user reads (and may write) the whole session.
     *
     * @return int
     */
    public static function warnBytes()
    {
        if ( class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance( 'debugbar.ini' );
            if ( $ini->hasVariable( 'DebugBarSettings', 'SessionWarnBytes' ) )
            {
                $value = trim( (string)$ini->variable( 'DebugBarSettings', 'SessionWarnBytes' ) );
                if ( ctype_digit( $value ) && (int)$value > 0 )
                    return (int)$value;
            }
        }
        return self::LARGE_BYTES;
    }

    /**
     * @param array    $session    the session's variables
     * @param array    $frame      see current()
     * @param string[] $extensions the active extensions, to group keys by their prefix
     * @param string[] $masks      parts of key names whose values are masked (regular expressions without delimiters,
     *                             case-insensitive); empty for DEFAULT_MASKS
     * @param array    $options    value_masks: more patterns of secret values (regular expressions without delimiters,
     *                             case-sensitive; SECRET_VALUE_PATTERNS always apply), warn_bytes: the size from which
     *                             the session is large (LARGE_BYTES)
     * @return array count, bytes, large, warn_bytes, warnings (codes: large, file_lock), truncated, left (keys not
     *               shown once the budget was used up), groups (extension, keys, bytes, entries), frame, text (the
     *               masked session as plain text, asText())
     */
    public static function forSession( array $session, array $frame, array $extensions = array(), array $masks = array(), array $options = array() )
    {
        $masks = self::masks( $masks );
        $valueMasks = self::valueMasks( isset( $options['value_masks'] ) ? (array)$options['value_masks'] : array() );
        $warnBytes = isset( $options['warn_bytes'] ) && (int)$options['warn_bytes'] > 0 ? (int)$options['warn_bytes'] : self::LARGE_BYTES;
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
            $node = self::node( (string)$key, $value, 0, $budget, $masks, false, $valueMasks );
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
        $warnings = array();
        if ( $bytes > $warnBytes )
            $warnings[] = 'large';
        // PHP's file handler locks the session file for the whole request: a user's requests run one after another
        if ( count( $session ) && isset( $frame['save_handler'] ) && $frame['save_handler'] === 'files' )
            $warnings[] = 'file_lock';
        $answer = array(
            'count'      => count( $session ),
            'bytes'      => $bytes,
            'large'      => $bytes > $warnBytes,
            'warn_bytes' => $warnBytes,
            'warnings'   => $warnings,
            'truncated'  => $left > 0 || self::cut( $groups ),
            'left'       => $left,
            'groups'     => array_values( $groups ),
            'frame'      => $frame,
        );
        $answer['text'] = self::asText( $answer );
        return $answer;
    }

    /**
     * The session as plain text for "Copy as text", from the answer's nodes only, so it is masked exactly like the
     * table: one "path = value" per line, grouped like the table; masked values as ***, the session id shortened.
     *
     * @param array $answer forSession() without text
     * @return string
     */
    public static function asText( array $answer )
    {
        $lines = array( '# session: ' . (int)$answer['count'] . ' keys, ' . (int)$answer['bytes'] . ' bytes'
                        . ( isset( $answer['frame']['id'] ) && $answer['frame']['id'] !== '' ? ', id ' . $answer['frame']['id'] : '' ) );
        $walk = function ( array $node, $path ) use ( &$walk, &$lines ) {
            $path = $path === '' ? $node['key'] : $path . '.' . $node['key'];
            $type = $node['type'];
            if ( $node['masked'] )
                $lines[] = $path . ' = *** (' . $type . ( $node['length'] !== null ? ', ' . $node['length'] : '' ) . ')';
            elseif ( is_array( $node['children'] ) )
            {
                if ( !$node['children'] && !$node['more'] )
                    $lines[] = $path . ' = (' . $type . ', empty)';
                foreach ( $node['children'] as $child )
                    $walk( $child, $path );
                if ( $node['more'] )
                    $lines[] = $path . '.… = (' . (int)$node['more'] . ' more)';
            }
            elseif ( $node['depth_cut'] )
                $lines[] = $path . ' = (' . $type . ', ' . (int)$node['length'] . ', nested too deep)';
            elseif ( $type === 'string' )
                $lines[] = $path . ' = ' . json_encode( (string)$node['value'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ( $node['cut'] ? ' … (' . (int)$node['length'] . ')' : '' );
            else
                $lines[] = $path . ' = ' . $node['value'] . ' (' . $type . ')' . ( $node['cut'] ? ' …' : '' );
        };
        foreach ( isset( $answer['groups'] ) ? $answer['groups'] : array() as $group )
        {
            $lines[] = '[' . ( $group['extension'] === null ? 'kernel' : $group['extension'] ) . ']';
            foreach ( $group['entries'] as $node )
                $walk( $node, '' );
        }
        if ( !empty( $answer['left'] ) )
            $lines[] = '# ' . (int)$answer['left'] . ' keys not listed';
        return implode( "\n", $lines ) . "\n";
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
     * @param string[]|null $valueMasks valueMasks(): patterns of secret values; null for the defaults
     * @return array key, type, bytes, masked, mask (why: key, value or parent; null when not masked), value, length,
     *               cut, children, more, depth_cut
     */
    public static function node( $key, $value, $depth, &$budget, array $masks, $masked, $valueMasks = null )
    {
        $budget--;
        if ( $valueMasks === null )
            $valueMasks = self::valueMasks( array() );
        $reason = $masked ? 'parent' : null;
        $keyText = self::text( $key );
        if ( $reason === null && self::looksSecret( (string)$key, $valueMasks ) )
        {
            // a secret used as a key (a token as an array index): neither the key nor what is below it is shown
            $keyText = self::shortId( $keyText );
            $reason = 'value';
        }
        if ( $reason === null && self::isMasked( (string)$key, $masks ) )
            $reason = 'key';
        if ( $reason === null && is_string( $value ) && self::looksSecret( $value, $valueMasks ) )
            $reason = 'value';
        $masked = $reason !== null;
        $node = array( 'key' => $keyText, 'type' => self::text( self::type( $value ) ), 'bytes' => self::bytes( $value ), 'masked' => $masked,
                       'mask' => $reason, 'value' => null, 'length' => null, 'cut' => false, 'children' => null, 'more' => 0, 'depth_cut' => false );
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
                $node['children'][] = self::node( (string)$childKey, $childValue, $depth + 1, $budget, $masks, false, $valueMasks );
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
     * The patterns of secret values in effect: SECRET_VALUE_PATTERNS and the configured ones (debugbar.ini
     * SessionValueMask[], regular expressions without delimiters, case-sensitive; an invalid one is taken as text).
     *
     * @return string[] complete regular expressions
     */
    public static function valueMasks( array $configured )
    {
        $patterns = array_values( self::SECRET_VALUE_PATTERNS );
        foreach ( $configured as $mask )
        {
            $mask = trim( (string)$mask );
            if ( $mask === '' )
                continue;
            $pattern = '~' . str_replace( '~', '\~', $mask ) . '~';
            $patterns[] = @preg_match( $pattern, '' ) === false ? '~' . preg_quote( $mask, '~' ) . '~' : $pattern;
        }
        return $patterns;
    }

    /**
     * Whether a text is or holds a secret, whatever its key is called: one of the patterns (valueMasks()) matches, or
     * the text as a whole is a random token (isRandomToken()). Only the first 4096 bytes are looked at.
     *
     * @param string[] $valueMasks valueMasks()
     */
    public static function looksSecret( $text, array $valueMasks )
    {
        $text = (string)$text;
        if ( strlen( $text ) < 12 )
            return false;
        $head = substr( $text, 0, 4096 );
        foreach ( $valueMasks as $pattern )
        {
            if ( @preg_match( $pattern, $head ) === 1 )
                return true;
        }
        return self::isRandomToken( trim( $head ) );
    }

    /**
     * A text that is a random token as a whole: RANDOM_TOKEN_LENGTH or more base64 or base64url characters, no space,
     * two or more digits and upper and lower case letters, no run of seven lower case letters (a word), at most two "/"
     * (not a path), and varied enough (entropy) not to be a
     * word or a slug.
     */
    public static function isRandomToken( $text )
    {
        $text = (string)$text;
        $length = strlen( $text );
        if ( $length < self::RANDOM_TOKEN_LENGTH || $length > 4096 || !preg_match( '~^[A-Za-z0-9+/_.-]+={0,2}$~', $text ) )
            return false;
        if ( !preg_match( '~[0-9]~', $text ) || !preg_match( '~[a-z]~', $text ) || !preg_match( '~[A-Z]~', $text ) )
            return false;
        if ( substr_count( $text, '/' ) > 2 || $text[0] === '/' || substr_count( $text, '-' ) > 4 || substr_count( $text, '.' ) > 2 )
            return false;
        // words: a run of seven lower case letters, or fewer than two digits (identifiers like ListOfItems2)
        if ( preg_match( '~[a-z]{7,}~', $text ) || preg_match_all( '~[0-9]~', $text ) < 2 )
            return false;
        $entropy = 0.0;
        foreach ( count_chars( $text, 1 ) as $count )
        {
            $p = $count / $length;
            $entropy -= $p * log( $p, 2 );
        }
        return $entropy >= self::RANDOM_TOKEN_ENTROPY;
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
