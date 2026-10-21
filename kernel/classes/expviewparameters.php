<?php
/**
 * File containing the expViewParameters class.
 *
 * The options of an administration view or endpoint are view parameters, /(name)/value, never GET query
 * parameters (as the help center's, doc/eng-us/features/6.0/help-system.md). This class reads them off a view's $Params, writes
 * them into an address, and answers a filter form sent with POST (the form without scripts) with a 303 to the
 * canonical /(name)/value address of what was asked for. A ?name= in the address is never read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expViewParameters
{
    /** The longest text a view parameter carries (a search) */
    const MAX_TEXT = 200;

    /**
     * One view parameter of a request: the declared variable of module.php's unordered_params when the view
     * declares it, else the user parameter of that name.
     *
     * @param array $params the view's $Params
     * @param string $name the parameter as it is written in the address, e.g. 'q' for /(q)/...
     * @param string|false $variable the unordered_params variable, e.g. 'Query'; false: only the user parameter
     * @return string|false false when the request does not give it
     */
    public static function get( array $params, $name, $variable = false )
    {
        if ( $variable !== false && isset( $params[$variable] ) && is_scalar( $params[$variable] ) && $params[$variable] !== false )
            return (string)$params[$variable];
        $user = isset( $params['UserParameters'] ) && is_array( $params['UserParameters'] ) ? $params['UserParameters'] : array();
        return isset( $user[$name] ) && is_scalar( $user[$name] ) ? (string)$user[$name] : false;
    }

    /**
     * A text option (a search) off the request: get() trimmed and made fit for an address (text()).
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
     * A text as it can travel as a view parameter: "/", "(", ")" and control characters as spaces, white space
     * collapsed and trimmed, at most MAX_TEXT characters.
     *
     * @param mixed $value
     * @param int $max
     * @return string
     */
    public static function clean( $value, $max = self::MAX_TEXT )
    {
        if ( !is_scalar( $value ) )
            return '';
        $value = preg_replace( '/[\/()\x00-\x1F\x7F]+/u', ' ', (string)$value );
        $value = trim( preg_replace( '/\s+/u', ' ', (string)$value ) );
        return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $max, 'UTF-8' ) : substr( $value, 0, $max );
    }

    /**
     * The address part of a set of options: "/(name)/value" for each one that is not '' (or false or null), in the
     * order given. Values are cleaned (clean()) and encoded.
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
            $suffix .= '/(' . $name . ')/' . rawurlencode( $value );
        }
        return $suffix;
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
