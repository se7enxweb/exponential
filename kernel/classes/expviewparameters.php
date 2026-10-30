<?php
/**
 * File containing the expViewParameters class.
 *
 * The options of an administration view or endpoint are view parameters, /(name)/value, never GET query
 * parameters (as the help center's, doc/eng-us/features/6.0/help-system.md). This class reads them off a view's $Params, writes
 * them into an address, and answers a filter form sent with POST (the form without scripts) with a 303 to the
 * canonical /(name)/value address of what was asked for. A ?name= in the address is never read.
 *
 * A value travels in ONE reversible encoding (encode(), decode()): the characters the address cannot carry as they
 * are - "/" (Apache refuses %2F, and the kernel decodes the whole path before it splits it), "(" and ")" (a segment
 * "(name)" starts a view parameter), "~" (the escape itself), "%" and "+" (the kernel decodes the address twice), "\",
 * "?", "#" and control characters - are written "~" and two hexadecimal digits, "~2F" for "/"; a value that is only
 * "." or ".." is written "~2E" or "~2E~2E" (a browser would resolve it). Everything else stays readable: letters,
 * digits, spaces and other text are percent-encoded as usual (and shown as text by a browser). get() and text()
 * return the decoded value; the templates write a value with the operator view_parameter, the admin scripts with
 * window.a4ViewParameter.encode() (a4_url.js). The "~XX" form is the one the log viewer (expLogPermalink) and
 * visual/templatelist wrote before, so their links keep working.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expViewParameters
{
    /** The longest text a view parameter carries (a search) */
    const MAX_TEXT = 200;

    /** The characters encode() writes as "~XX": the escape itself, those the address or the kernel would change */
    const ESCAPED = '/[~\/()%+\\\\?#\x00-\x1F\x7F]/';

    /** Percent-encoded characters encode() writes back as they are: safe in a path segment and readable */
    const READABLE = array( '%2C' => ',', '%3A' => ':', '%40' => '@', '%3D' => '=', '%21' => '!', '%2A' => '*' );

    /**
     * One view parameter of a request as the address carries it (not decoded, see get()): the declared variable of
     * module.php's unordered_params when the view declares it, else the user parameter of that name.
     *
     * @param array $params the view's $Params
     * @param string $name the parameter as it is written in the address, e.g. 'q' for /(q)/...
     * @param string|false $variable the unordered_params variable, e.g. 'Query'; false: only the user parameter
     * @return string|false false when the request does not give it
     */
    public static function raw( array $params, $name, $variable = false )
    {
        if ( $variable !== false && isset( $params[$variable] ) && is_scalar( $params[$variable] ) && $params[$variable] !== false )
            return (string)$params[$variable];
        $user = isset( $params['UserParameters'] ) && is_array( $params['UserParameters'] ) ? $params['UserParameters'] : array();
        return isset( $user[$name] ) && is_scalar( $user[$name] ) ? (string)$user[$name] : false;
    }

    /**
     * One view parameter of a request, decoded (decode()): raw() as the value that was encoded.
     *
     * @param array $params the view's $Params
     * @param string $name
     * @param string|false $variable
     * @return string|false false when the request does not give it
     */
    public static function get( array $params, $name, $variable = false )
    {
        $value = self::raw( $params, $name, $variable );
        return $value === false ? false : self::decode( $value );
    }

    /**
     * A text option (a search) off the request: get() (decoded) and cleaned (clean()).
     *
     * @param array $params
     * @param string $name
     * @param string|false $variable
     * @return string '' when not given
     */
    public static function text( array $params, $name, $variable = false )
    {
        $value = self::get( $params, $name, $variable );
        return $value === false ? '' : self::clean( $value );
    }

    /**
     * A text option made tidy: control characters as spaces, white space collapsed and trimmed, at most MAX_TEXT
     * characters. "/", "(", ")" and the rest stay: encode() writes them so that they survive the address.
     *
     * @param mixed $value
     * @param int $max
     * @return string
     */
    public static function clean( $value, $max = self::MAX_TEXT )
    {
        if ( !is_scalar( $value ) )
            return '';
        $value = preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', (string)$value );
        $value = trim( preg_replace( '/\s+/u', ' ', (string)$value ) );
        return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $max, 'UTF-8' ) : substr( $value, 0, $max );
    }

    /**
     * The address part of a set of options: "/(name)/value" for each one that is not '' (or false or null), in the
     * order given. Values are cleaned (clean()) and encoded (encode()).
     *
     * @param array $options name => value
     * @return string '' when there is none
     */
    public static function suffix( array $options )
    {
        $suffix = '';
        foreach ( $options as $name => $value )
        {
            if ( $value === null || $value === false || is_array( $value ) )
                continue;
            if ( $value === true )
                $value = '1';
            $value = self::clean( $value );
            if ( $value === '' )
                continue;
            $suffix .= '/(' . $name . ')/' . self::encode( $value );
        }
        return $suffix;
    }

    /**
     * A value as the kernel hands it to a view after it decoded the address (what raw() returns): every character
     * of ESCAPED as "~" and its two hexadecimal digits, and a value of only "." or ".." as "~2E" each.
     *
     * @param mixed $value
     * @return string
     */
    public static function escape( $value )
    {
        if ( !is_scalar( $value ) )
            return '';
        $value = (string)$value;
        if ( $value === '.' || $value === '..' )
            return str_repeat( '~2E', strlen( $value ) );
        return preg_replace_callback( self::ESCAPED, function ( $m ) {
            return '~' . strtoupper( bin2hex( $m[0] ) );
        }, $value );
    }

    /**
     * A value as one segment of an address (/(name)/<value>): escape(), then percent-encoded, with , : @ = ! * kept
     * readable. Reversible: the kernel's decoding (once or twice) gives escape()'s string, decode() the value.
     *
     * @param mixed $value
     * @return string
     */
    public static function encode( $value )
    {
        return strtr( rawurlencode( self::escape( $value ) ), self::READABLE );
    }

    /**
     * A value as it was before encode(): every "~" and two hexadecimal digits (either case) as that byte; a "~"
     * without them stays.
     *
     * @param mixed $value
     * @return string
     */
    public static function decode( $value )
    {
        if ( !is_scalar( $value ) )
            return '';
        return preg_replace_callback( '/~([0-9A-Fa-f]{2})/', function ( $m ) {
            return chr( (int)hexdec( $m[1] ) );
        }, (string)$value );
    }

    /**
     * Whether the request is a POST.
     *
     * @param array|null $server usually $_SERVER
     * @return bool
     */
    public static function isPost( $server = null )
    {
        $server = $server === null ? $_SERVER : $server;
        return isset( $server['REQUEST_METHOD'] ) && strtoupper( (string)$server['REQUEST_METHOD'] ) === 'POST';
    }

    /**
     * Whether a request is a POST of a filter form: POST and at least one of the fields sent (empty ones count:
     * a search form sends its field with nothing typed). A GET is never redirected, its query string is not read.
     *
     * @param array $post usually $_POST
     * @param string[] $fields the form's field names
     * @param array|null $server
     * @return bool
     */
    public static function redirectsPost( array $post, array $fields, $server = null )
    {
        if ( !self::isPost( $server ) )
            return false;
        foreach ( $fields as $field )
        {
            if ( array_key_exists( $field, $post ) )
                return true;
        }
        return false;
    }

    /**
     * A field of the posted form as a string, '' when missing or not a string.
     *
     * @param array $post
     * @param string $field
     * @return string
     */
    public static function posted( array $post, $field )
    {
        return isset( $post[$field] ) && is_scalar( $post[$field] ) ? (string)$post[$field] : '';
    }

    /**
     * Answers with 303 See Other to a module address (e.g. 'role/list/(q)/editor') and ends the request.
     * Sent here, not through $Module->redirectTo(): the kernel carries a request's query string over a module
     * redirect.
     *
     * @param string $uri module-relative address
     */
    public static function redirect( $uri )
    {
        $target = '/' . ltrim( (string)$uri, '/' );
        eZURI::transformURI( $target, false, 'full', false );
        eZHTTPTool::redirect( $target, array(), '303 See Other', false );
        eZExecution::cleanExit();
    }
}

?>
