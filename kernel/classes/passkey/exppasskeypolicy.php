<?php
/**
 * File containing the expPasskeyPolicy class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The passkey policy of an account (doc/eng-us/guides/passkeys.md, "Policy"): site.ini [PasskeySettings]
 *
 *   Policy=optional|preferred|required     the site's default
 *   PasswordSignIn=enabled|disabled        whether an account WITH a passkey may still sign in with its password
 *   GroupPolicy[<group object id>]=...     per user group; the strictest of the user's groups and the default wins
 *   GroupPasswordSignIn[<group id>]=...    per user group; disabled in any of the user's groups wins
 *   UserPolicy[<user object id>]=...       per account; wins over the groups and the default
 *   UserPasswordSignIn[<user id>]=...      per account
 *   PolicyHandlers[]                       expPasskeyPolicyHandler classes of extensions, asked last
 *
 * What it does, on the web pages only (REST, SOAP, WebDAV, the expservices login and the command line keep their own
 * sign-in, owner rule):
 *  - optional: nothing; the user may add passkeys.
 *  - preferred: after signing in, a user without a passkey is sent once per session to user/passkeys, which suggests
 *    adding one and lets them go on ("Not now").
 *  - required: a user without a passkey is sent to user/passkeys before any other page until they added one.
 *  - PasswordSignIn=disabled: the sign-in form refuses the password of an account that has a passkey, exactly like a
 *    wrong password (no one learns whether the account exists or has passkeys); the form says that accounts which
 *    sign in with passkeys only use the passkey button. An account without a passkey still signs in with its
 *    password, so nobody is locked out, and with "required" it then adds one.
 *  - The last passkey of an account cannot be removed by its owner while the account could not sign in without it
 *    (no password, or PasswordSignIn disabled, or the policy requires a passkey); an administrator can (Revoke).
 *
 * decide() is a pure function over the settings and the user's groups, tested without an installation.
 *
 * @since 6.0.15
 */
class expPasskeyPolicy
{
    const OPTIONAL = 'optional';
    const PREFERRED = 'preferred';
    const REQUIRED = 'required';

    /** Set once a user was sent to add a passkey in this session (preferred) */
    const SESSION_SUGGESTED = 'ExpPasskeySuggested';
    /** Where the user was going when they were sent to add a passkey */
    const SESSION_CONTINUE = 'ExpPasskeyContinue';

    /** Views that stay open while a passkey is required, when the settings name none */
    const DEFAULT_EXEMPT_VIEWS = array( 'user/passkeys', 'user/logout', 'user/login', 'user/password', 'user2fa', 'ezjscore', 'user/preferences' );

    /**
     * @param mixed $value
     * @return string optional, preferred or required (anything else: optional)
     */
    public static function policyValue( $value )
    {
        $value = strtolower( trim( (string)$value ) );
        return in_array( $value, array( self::PREFERRED, self::REQUIRED ), true ) ? $value : self::OPTIONAL;
    }

    /**
     * @param string $policy
     * @return int 0 optional, 1 preferred, 2 required
     */
    public static function rank( $policy )
    {
        return array_search( self::policyValue( $policy ), array( self::OPTIONAL, self::PREFERRED, self::REQUIRED ), true );
    }

    /**
     * The settings as plain values.
     *
     * @param array $raw Policy, PasswordSignIn, GroupPolicy, GroupPasswordSignIn, UserPolicy, UserPasswordSignIn,
     *                   PolicyHandlers, ExemptViews
     * @return array
     */
    public static function parse( array $raw )
    {
        $on = function ( $v ) {
            return !in_array( strtolower( trim( (string)$v ) ), array( 'disabled', 'false', '0', 'no', 'off' ), true );
        };
        $byId = function ( $list, $map ) {
            $out = array();
            foreach ( (array)$list as $id => $v )
                if ( is_numeric( $id ) && (int)$id > 0 )
                    $out[(int)$id] = $map( $v );
            return $out;
        };
        $policy = function ( $v ) {
            return self::policyValue( $v );
        };
        return array(
            'policy' => self::policyValue( isset( $raw['Policy'] ) ? $raw['Policy'] : self::OPTIONAL ),
            'password_sign_in' => $on( isset( $raw['PasswordSignIn'] ) ? $raw['PasswordSignIn'] : 'enabled' ),
            'group_policy' => $byId( isset( $raw['GroupPolicy'] ) ? $raw['GroupPolicy'] : array(), $policy ),
            'group_password_sign_in' => $byId( isset( $raw['GroupPasswordSignIn'] ) ? $raw['GroupPasswordSignIn'] : array(), $on ),
            'user_policy' => $byId( isset( $raw['UserPolicy'] ) ? $raw['UserPolicy'] : array(), $policy ),
            'user_password_sign_in' => $byId( isset( $raw['UserPasswordSignIn'] ) ? $raw['UserPasswordSignIn'] : array(), $on ),
            'handlers' => array_values( array_filter( array_map( 'trim', (array)( isset( $raw['PolicyHandlers'] ) ? $raw['PolicyHandlers'] : array() ) ), 'strlen' ) ),
            'exempt_views' => array_values( array_filter( array_map( 'trim', (array)( isset( $raw['ExemptViews'] ) ? $raw['ExemptViews'] : array() ) ), 'strlen' ) ),
        );
    }

    /** @return array parse() of this request's site.ini */
    public static function settings()
    {
        $ini = eZINI::instance();
        $raw = array();
        foreach ( array( 'Policy', 'PasswordSignIn', 'GroupPolicy', 'GroupPasswordSignIn', 'UserPolicy', 'UserPasswordSignIn', 'PolicyHandlers', 'ExemptViews' ) as $name )
            if ( $ini->hasVariable( 'PasskeySettings', $name ) )
                $raw[$name] = $ini->variable( 'PasskeySettings', $name );
        return self::parse( $raw );
    }

    /**
     * The policy of one account. Pure.
     *
     * @param array $settings parse()
     * @param int $userID
     * @param int[] $groupIDs the user's groups (content object ids)
     * @return array policy, password_sign_in
     */
    public static function decide( array $settings, $userID, array $groupIDs )
    {
        $userID = (int)$userID;
        $policy = $settings['policy'];
        $password = $settings['password_sign_in'];
        foreach ( $groupIDs as $g )
        {
            $g = (int)$g;
            if ( isset( $settings['group_policy'][$g] ) && self::rank( $settings['group_policy'][$g] ) > self::rank( $policy ) )
                $policy = $settings['group_policy'][$g];
            if ( isset( $settings['group_password_sign_in'][$g] ) && !$settings['group_password_sign_in'][$g] )
                $password = false;
        }
        if ( isset( $settings['user_policy'][$userID] ) )
            $policy = $settings['user_policy'][$userID];
        if ( isset( $settings['user_password_sign_in'][$userID] ) )
            $password = $settings['user_password_sign_in'][$userID];
        return array( 'policy' => $policy, 'password_sign_in' => (bool)$password );
    }

    /**
     * The policy of an account, with the extensions' handlers.
     *
     * @param eZUser $user
     * @param array|null $settings
     * @return array policy, password_sign_in
     */
    public static function forUser( eZUser $user, $settings = null )
    {
        $settings = $settings ?: self::settings();
        $result = self::decide( $settings, (int)$user->attribute( 'contentobject_id' ), array_map( 'intval', (array)$user->groups() ) );
        foreach ( $settings['handlers'] as $class )
        {
            if ( !class_exists( $class ) || !is_subclass_of( $class, 'expPasskeyPolicyHandler' ) )
                continue;
            $handler = new $class();
            $changed = $handler->passkeyPolicy( $user, $result );
            if ( is_array( $changed ) )
                $result = array( 'policy' => self::policyValue( isset( $changed['policy'] ) ? $changed['policy'] : $result['policy'] ),
                                 'password_sign_in' => isset( $changed['password_sign_in'] ) ? (bool)$changed['password_sign_in'] : $result['password_sign_in'] );
        }
        return $result;
    }

    /**
     * Whether any setting turns the password sign-in off for someone (the sign-in form then says that accounts which
     * sign in with passkeys only use the passkey button, after every refused sign-in alike). Pure.
     *
     * @param array $settings parse()
     * @return bool
     */
    public static function passwordOffAnywhere( array $settings )
    {
        if ( !$settings['password_sign_in'] || $settings['handlers'] )
            return true;
        return in_array( false, $settings['group_password_sign_in'], true ) || in_array( false, $settings['user_password_sign_in'], true );
    }

    /**
     * Whether the password sign-in of an account is refused: the policy turns it off and the account has a passkey.
     * Pure.
     *
     * @param array $policy decide()
     * @param int $passkeyCount
     * @return bool
     */
    public static function refusesPassword( array $policy, $passkeyCount )
    {
        return !$policy['password_sign_in'] && (int)$passkeyCount > 0;
    }

    /**
     * For the web sign-in form, before the login handlers run: whether the password of the account a login names is
     * refused. Records access.passkey.password_refused. False when passkeys are off or the account is unknown, so
     * the form answers like for a wrong password.
     *
     * @param string $login
     * @return bool
     */
    public static function refusesPasswordFor( $login )
    {
        $login = trim( (string)$login );
        if ( $login === '' || !class_exists( 'expPasskeyService' ) )
            return false;
        $settings = self::settings();
        if ( !self::passwordOffAnywhere( $settings ) )
            return false;
        $service = new expPasskeyService();
        if ( !$service->available() )
            return false;
        $user = eZUser::fetchByName( $login );
        if ( !$user instanceof eZUser && strpos( $login, '@' ) !== false )
            $user = eZUser::fetchByEmail( $login );
        if ( !$user instanceof eZUser )
            return false;
        $count = expPasskey::countByUser( (int)$user->attribute( 'contentobject_id' ) );
        if ( !self::refusesPassword( self::forUser( $user, $settings ), $count ) )
            return false;
        self::audit( 'access.passkey.password_refused', $user, array( 'channel' => 'web' ) );
        return true;
    }

    /**
     * Whether the owner may remove one of their passkeys: not the last one while the account could not sign in
     * without it. Pure.
     *
     * @param array $policy decide()
     * @param int $passkeyCount the passkeys the account has now
     * @param bool $hasPassword
     * @return bool
     */
    public static function mayRemove( array $policy, $passkeyCount, $hasPassword )
    {
        if ( (int)$passkeyCount > 1 )
            return true;
        return $hasPassword && $policy['password_sign_in'] && $policy['policy'] !== self::REQUIRED;
    }

    /**
     * @param eZUser $user
     * @return bool the account has a password it can sign in with
     */
    public static function hasPassword( eZUser $user )
    {
        return (int)$user->attribute( 'password_hash_type' ) !== eZUser::PASSWORD_HASH_EMPTY && (string)$user->attribute( 'password_hash' ) !== '';
    }

    /**
     * What a request of a signed-in user without a passkey does under the policy. Pure.
     *
     * @param string $policy
     * @param int $passkeyCount
     * @param bool $suggestedAlready the user was sent once in this session (preferred)
     * @param bool $exempt the view stays open
     * @param string $method GET, POST ...
     * @return string|null 'require' (send to user/passkeys), 'suggest' (send once), null (go on)
     */
    public static function gate( $policy, $passkeyCount, $suggestedAlready, $exempt, $method )
    {
        if ( $exempt || (int)$passkeyCount > 0 )
            return null;
        if ( $policy === self::REQUIRED )
            return 'require';
        if ( $policy === self::PREFERRED && !$suggestedAlready && strtoupper( (string)$method ) === 'GET' )
            return 'suggest';
        return null;
    }

    /**
     * Whether a view stays open while a passkey is required: "module/view" or a whole "module".
     *
     * @param string $module
     * @param string $view
     * @param string[] $exempt
     * @return bool
     */
    public static function isExempt( $module, $view, array $exempt )
    {
        return in_array( $module, $exempt, true ) || in_array( $module . '/' . $view, $exempt, true );
    }

    /**
     * The web gate (ezpKernelWeb, after the password change and before "Verify it's you"): null to go on, else the
     * module result (a redirect to user/passkeys, or 403 JSON {"error":"passkey_required"} for a script).
     *
     * @param eZModule $module
     * @param string $view
     * @return array|null
     */
    public static function moduleResult( $module, $view )
    {
        if ( !$module instanceof eZModule || !class_exists( 'expPasskeyService' ) )
            return null;
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser || !$user->isRegistered() )
            return null;
        $settings = self::settings();
        $moduleName = (string)$module->attribute( 'name' );
        $exempt = self::isExempt( $moduleName, (string)$view, $settings['exempt_views'] ?: self::DEFAULT_EXEMPT_VIEWS );
        if ( $exempt )
            return null;
        // the common case first, without a query: a site where every policy is optional
        if ( $settings['policy'] === self::OPTIONAL && !$settings['group_policy'] && !$settings['user_policy'] && !$settings['handlers'] )
            return null;
        $policy = self::forUser( $user, $settings );
        if ( $policy['policy'] === self::OPTIONAL )
            return null;
        $service = new expPasskeyService();
        if ( !$service->available() )
            return null;
        $http = eZHTTPTool::instance();
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : 'GET';
        $action = self::gate( $policy['policy'], expPasskey::countByUser( (int)$user->attribute( 'contentobject_id' ) ),
                              $http->hasSessionVariable( self::SESSION_SUGGESTED ), false, $method );
        if ( $action === null )
            return null;
        if ( class_exists( 'ezpRequestRuleKernel' ) )
            ezpRequestRuleKernel::keepOutOfSharedCaches();
        if ( $action === 'suggest' )
            $http->setSessionVariable( self::SESSION_SUGGESTED, 1 );
        if ( strtoupper( $method ) === 'GET' && class_exists( 'expReauth' ) )
            $http->setSessionVariable( self::SESSION_CONTINUE, expReauth::currentURI() );
        self::audit( 'access.passkey.' . ( $action === 'require' ? 'required' : 'suggested' ), $user,
                     array( 'view' => $moduleName . '/' . $view, 'policy' => $policy['policy'] ) );
        $xhr = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strcasecmp( $_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest' ) === 0;
        if ( $xhr )
        {
            header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden' );
            header( 'Content-Type: application/json; charset=utf-8' );
            return array( 'content' => json_encode( array( 'error' => 'passkey_required' ) ), 'pagelayout' => false );
        }
        $module->redirectTo( '/user/passkeys' );
        return array( 'content' => '' );
    }

    /**
     * Where to go after the passkey page was shown because of the policy: the address remembered, used up; '' when
     * none (a local path only).
     *
     * @return string
     */
    public static function takeContinueURI()
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasSessionVariable( self::SESSION_CONTINUE ) )
            return '';
        $uri = (string)$http->sessionVariable( self::SESSION_CONTINUE );
        $http->removeSessionVariable( self::SESSION_CONTINUE );
        return ( $uri !== '' && $uri[0] === '/' && strpos( $uri, '//' ) !== 0 && strpos( $uri, '/user/passkeys' ) !== 0 ) ? $uri : '';
    }

    /**
     * @param string $name
     * @param eZUser $user
     * @param array $after
     */
    protected static function audit( $name, eZUser $user, array $after )
    {
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( $name, array( 'object' => expAuditHook::user( $user ), 'after' => $after ) );
    }
}
