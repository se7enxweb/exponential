<?php
/**
 * File containing the expReauthRules class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The rules of "Verify it's you" (site.ini [ReauthSettings], [ReauthGroup_<name>] Rules[]) and the decision whether a
 * request matches one (doc/eng-us/guides/verify-its-you.md).
 *
 * A rule is "module/view [token ...]":
 *   module/view   the view; "module/*" every view of the module. A POST to it matches.
 *   *             every request matches, GET too (a page that downloads, or a form with a file upload, which a
 *                 repeated POST could not carry)
 *   Name          only a POST that carries one of these fields (the buttons of the action)
 *   self, other   only when the account the request addresses is the user's own, or another user's (content/edit
 *                 of a user object, user/password, user/setting); a request that addresses no account never matches
 *   credentials   only when the POST changes the login, e-mail address or password of a user
 *   !module/view  an exception: the view never matches in this group
 *
 * The decision is a pure function: what it needs to know about the request beyond module, view, method and POST
 * fields (whose account it addresses, whether credentials change) is asked through callbacks, only for rules that
 * need it, so most requests cost no database query. Tested without an installation.
 */
class expReauthRules
{
    /**
     * Reads a rule.
     *
     * @param string $rule
     * @return array|null module, view, every (bool), fields (string[]), who (self, other or null), credentials (bool),
     *                    exception (bool); null when it is malformed
     */
    public static function parse( $rule )
    {
        $tokens = preg_split( '/\s+/', trim( (string)$rule ) );
        if ( !$tokens || $tokens[0] === '' )
            return null;
        $target = array_shift( $tokens );
        $exception = false;
        if ( $target[0] === '!' )
        {
            $exception = true;
            $target = substr( $target, 1 );
        }
        if ( !preg_match( '#^([A-Za-z0-9_]+)/([A-Za-z0-9_]+|\*)$#', $target, $m ) )
            return null;
        $out = array( 'module' => $m[1], 'view' => $m[2], 'every' => false, 'fields' => array(), 'who' => null,
                      'credentials' => false, 'exception' => $exception );
        foreach ( $tokens as $t )
        {
            if ( $t === '*' )
                $out['every'] = true;
            elseif ( $t === 'self' || $t === 'other' )
                $out['who'] = $t;
            elseif ( $t === 'credentials' )
                $out['credentials'] = true;
            elseif ( preg_match( '/^[A-Za-z_][A-Za-z0-9_\-]*$/', $t ) )
                $out['fields'][] = $t;
            else
                return null;
        }
        return $out;
    }

    /**
     * Whether a request matches the rules of one group.
     *
     * @param string[] $rules
     * @param string $module
     * @param string $view
     * @param string $method GET, POST ...
     * @param array $post the POST fields
     * @param callable|null $whose function(): 'self', 'other' or null, the account the request addresses
     * @param callable|null $credentials function(): bool, whether the POST changes the credentials of a user
     * @return string|null the rule that matched, null for none
     */
    public static function match( array $rules, $module, $view, $method, array $post, $whose = null, $credentials = null )
    {
        $parsed = array();
        foreach ( $rules as $rule )
        {
            $p = self::parse( $rule );
            if ( $p === null || $p['module'] !== $module || ( $p['view'] !== '*' && $p['view'] !== $view ) )
                continue;
            // an exception for the exact view wins over the group
            if ( $p['exception'] && $p['view'] === $view )
                return null;
            if ( !$p['exception'] )
                $parsed[$rule] = $p;
        }
        $isPost = strtoupper( (string)$method ) === 'POST';
        $who = false;
        foreach ( $parsed as $rule => $p )
        {
            if ( !$p['every'] && !$isPost )
                continue;
            if ( $p['fields'] )
            {
                $carries = false;
                foreach ( $p['fields'] as $f )
                    if ( array_key_exists( $f, $post ) )
                        $carries = true;
                if ( !$carries || !$isPost )
                    continue;
            }
            if ( $p['who'] !== null )
            {
                if ( $who === false )
                    $who = $whose ? call_user_func( $whose ) : null;
                if ( $who !== $p['who'] )
                    continue;
            }
            if ( $p['credentials'] && !( $isPost && $credentials && call_user_func( $credentials ) ) )
                continue;
            return $rule;
        }
        return null;
    }

    /**
     * Whether a POST changes the login, e-mail address or password of the user account it edits: the fields of the
     * user datatype (ContentObjectAttribute_data_user_<login|email|password>_<id>) compared with the stored values.
     *
     * @param array $post
     * @param string $login the stored login
     * @param string $email the stored e-mail address
     * @return bool
     */
    public static function changesCredentials( array $post, $login, $email )
    {
        foreach ( $post as $name => $value )
        {
            if ( !is_string( $name ) || !preg_match( '/_data_user_(login|email|password)_\d+$/', $name, $m ) || !is_string( $value ) )
                continue;
            if ( $m[1] === 'password' && $value !== '' && $value !== '_ezpassword' )
                return true;
            if ( $m[1] === 'login' && trim( $value ) !== '' && trim( $value ) !== (string)$login )
                return true;
            if ( $m[1] === 'email' && strcasecmp( trim( $value ), (string)$email ) !== 0 )
                return true;
        }
        return false;
    }
}
