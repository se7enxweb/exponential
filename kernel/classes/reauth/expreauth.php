<?php
/**
 * File containing the expReauth class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * "Verify it's you" (doc/eng-us/guides/verify-its-you.md): before a sensitive action the signed-in user confirms who
 * they are again and is then not asked again for [ReauthSettings] Minutes. How is [ReauthSettings] Factors[]: the
 * first combination whose every way the user has, by default the password AND one of their passkeys, else the
 * password and the code of their authenticator app, else the password alone, else a passkey alone (an account
 * without a password). Another combination of the same strength is offered beside it (combinations()), never a
 * weaker one.
 *
 * Which requests ask: the rules of the groups of [ReauthSettings] Groups[] whose [ReauthGroup_<name>] Enabled=true
 * (expReauthRules). By default account (one's own password, passkeys, two-step sign-in, API keys and e-mail address),
 * users (users, roles, policies), system (settings and dangerous setup actions, packages, updates, the git manager)
 * and export (data exports) are on; content (removing and publishing content) is off and switched on setup/reauth.
 *
 * Where it is asked: ezpKernelWeb (index.php and Velocity), after the policies allowed the view and before it runs
 * (moduleResult()). Never asked: anonymous visitors, the REST interface, SOAP, WebDAV, the expservices login and the
 * command line, which do not run through ezpKernelWeb's views; and never for the views of the page itself
 * (user/login, user/logout).
 *
 * How: instead of the view, the page "Verify it's you" is shown. It posts to the same address with the original POST
 * fields as hidden fields, so after a correct answer the view runs with exactly what the user sent; a GET (a download)
 * is redirected to again. A form with a file upload is opened again instead (a browser cannot send a file twice), so
 * groups put the GET of such a view under a "*" rule. Only a file actually chosen counts as an upload (hasUploads()):
 * a form that merely has file inputs, left empty (the edit form of a user with its image), is replayed like any other
 * POST. A request made with XMLHttpRequest gets 403 with a JSON body
 * {"error": "reauth_required", "url": ...}.
 *
 * Kept in the session: the user id and the time until which a confirmation counts, and the count of wrong answers;
 * after [ReauthSettings] MaxFailures wrong answers in a row the user is signed out. Recorded through expAuditHook:
 * access.session.reauth.ask (group, view), access.session.reauth (method, group, view, minutes) and
 * access.session.reauth.failed (method, reason; never the password or the code). The password re-entry of the audit
 * console (expAuditReauth) counts a confirmation made here, and the other way round it keeps its own.
 */
class expReauth
{
    const SESSION_KEY = 'ExpReauth';
    const FAILURES_KEY = 'ExpReauthFailures';

    /** The form's fields; never passed on to the view */
    const FIELD_METHOD = 'ExpReauthMethod';
    /** The combination the form answers ("password+passkey"), one of those offered */
    const FIELD_FACTORS = 'ExpReauthFactors';
    const FIELD_PASSWORD = 'ExpReauthPassword';
    const FIELD_CODE = 'ExpReauthCode';
    const FIELD_CONFIRM = 'ExpReauthConfirmButton';
    const FIELD_CANCEL = 'ExpReauthCancelButton';
    const FIELD_GET = 'ExpReauthReplayGet';
    /** The first request carried an upload: after the answer the form opens again to choose the files once more */
    const FIELD_FILES = 'ExpReauthReplayFiles';

    const METHOD_PASSWORD = 'password';
    const METHOD_PASSKEY = 'passkey';
    const METHOD_CODE = 'code';

    /** Views that never ask, whatever a rule says: the sign-in and sign-out themselves */
    const NEVER = array( 'user/login', 'user/logout' );

    /** @var int|null a fixed clock (tests) */
    protected static $now = null;

    // ------------------------------------------------------------------------------------------------------------
    // Settings
    // ------------------------------------------------------------------------------------------------------------

    /**
     * site.ini [ReauthSettings] and the groups, read for this request (or from the site.ini of another siteaccess).
     *
     * @param eZINI|null $ini site.ini to read; null: this request's
     * @return array enabled, minutes, max_failures, factors, bulk_count, trigger_handlers, groups (name => array( enabled,
     *               rules, user_groups ))
     */
    public static function settings( $ini = null )
    {
        $ini = $ini instanceof eZINI ? $ini : eZINI::instance();
        $get = function ( $block, $name, $default ) use ( $ini ) {
            return $ini->hasVariable( $block, $name ) ? $ini->variable( $block, $name ) : $default;
        };
        $groups = array();
        foreach ( (array)$get( 'ReauthSettings', 'Groups', array() ) as $name )
        {
            $name = trim( (string)$name );
            if ( $name === '' || !preg_match( '/^[A-Za-z0-9_]+$/', $name ) || isset( $groups[$name] ) )
                continue;
            $block = 'ReauthGroup_' . $name;
            $userGroups = array();
            foreach ( (array)$get( $block, 'UserGroups', array() ) as $id )
                if ( is_numeric( $id ) && (int)$id > 0 )
                    $userGroups[] = (int)$id;
            $groups[$name] = array( 'enabled' => self::isOn( $get( $block, 'Enabled', 'false' ) ),
                                    'rules' => array_values( array_filter( array_map( 'trim', (array)$get( $block, 'Rules', array() ) ), 'strlen' ) ),
                                    'user_groups' => array_values( array_unique( $userGroups ) ) );
        }
        return array(
            'enabled' => self::isOn( $get( 'ReauthSettings', 'Reauth', 'enabled' ) ),
            'minutes' => max( 1, min( 1440, (int)$get( 'ReauthSettings', 'Minutes', 10 ) ) ),
            'max_failures' => max( 1, (int)$get( 'ReauthSettings', 'MaxFailures', 5 ) ),
            'factors' => self::parseFactors( (array)$get( 'ReauthSettings', 'Factors', array() ) ),
            'bulk_count' => max( 2, (int)$get( 'ReauthSettings', 'BulkCount', 2 ) ),
            'trigger_handlers' => array_values( array_filter( array_map( 'trim', (array)$get( 'ReauthSettings', 'TriggerHandlers', array() ) ), 'strlen' ) ),
            'groups' => $groups,
        );
    }

    /**
     * The combinations of [ReauthSettings] Factors[], in order: each a list of methods ("password+passkey"). Unknown
     * methods make a line unusable; an empty or unusable list gives the shipped order (the owner's: the password and
     * a passkey, else the password and the code of the authenticator app, else the password, else a passkey).
     *
     * @param array $lines
     * @return array[] each a list of METHOD_* values
     */
    public static function parseFactors( array $lines )
    {
        $known = array( self::METHOD_PASSWORD, self::METHOD_PASSKEY, self::METHOD_CODE );
        $out = array();
        foreach ( $lines as $line )
        {
            $parts = array_values( array_unique( array_filter( array_map( 'trim', explode( '+', strtolower( (string)$line ) ) ), 'strlen' ) ) );
            if ( !$parts || array_diff( $parts, $known ) )
                continue;
            if ( !in_array( $parts, $out, true ) )
                $out[] = $parts;
        }
        if ( !$out )
            $out = array( array( self::METHOD_PASSWORD, self::METHOD_PASSKEY ), array( self::METHOD_PASSWORD, self::METHOD_CODE ),
                          array( self::METHOD_PASSWORD ), array( self::METHOD_PASSKEY ) );
        return $out;
    }

    /**
     * The combinations a user can answer, strongest first: the first line of $factors whose every method the user
     * has, and the later lines with as many methods (another way of the same strength: the code of the
     * authenticator app instead of a passkey on a browser without passkeys). Never a weaker one: a stolen password
     * cannot choose its way around the second step. Pure.
     *
     * @param string[] $available methods()
     * @param array[] $factors parseFactors()
     * @return array[] the combinations offered; empty when the user can answer none
     */
    public static function combinations( array $available, array $factors )
    {
        $out = array();
        $size = null;
        foreach ( $factors as $combination )
        {
            if ( array_diff( $combination, $available ) )
                continue;
            if ( $size === null )
                $size = count( $combination );
            if ( count( $combination ) === $size )
                $out[] = $combination;
        }
        return $out;
    }

    /**
     * The combination a form answers: the one it names (ExpReauthFactors, "password+code") when it is one of those
     * offered, else the first offered. Pure.
     *
     * @param array[] $offered combinations()
     * @param mixed $named
     * @return string[]|null null when nothing is offered
     */
    public static function chosenCombination( array $offered, $named )
    {
        if ( !$offered )
            return null;
        if ( is_string( $named ) && $named !== '' )
            foreach ( $offered as $combination )
                if ( implode( '+', $combination ) === strtolower( trim( $named ) ) )
                    return $combination;
        return $offered[0];
    }

    /**
     * @param mixed $value
     * @return bool enabled, true, 1, yes, on
     */
    public static function isOn( $value )
    {
        return in_array( strtolower( trim( (string)$value ) ), array( 'enabled', 'true', '1', 'yes', 'on' ), true );
    }

    /**
     * The group whose rules a request matches (only switched-on groups), with the rule.
     *
     * @param array $settings settings()
     * @param string $module
     * @param string $view
     * @param string $method
     * @param array $post
     * @param callable|null $whose
     * @param callable|null $credentials
     * @param callable[] $conditions the rule conditions by name (bulk => function(): bool)
     * @param callable|null $memberOf function(): int[], the user's groups (content object ids), for UserGroups[]
     * @return array|null group, rule
     */
    public static function matchGroup( array $settings, $module, $view, $method, array $post, $whose = null, $credentials = null,
                                       array $conditions = array(), $memberOf = null )
    {
        if ( empty( $settings['enabled'] ) || in_array( $module . '/' . $view, self::NEVER, true ) )
            return null;
        $groupsOfUser = null;
        foreach ( $settings['groups'] as $name => $group )
        {
            if ( empty( $group['enabled'] ) )
                continue;
            $rule = expReauthRules::match( $group['rules'], $module, $view, $method, $post, $whose, $credentials, $conditions );
            if ( $rule === null )
                continue;
            if ( !self::appliesToUser( $group, $groupsOfUser, $memberOf ) )
                continue;
            return array( 'group' => $name, 'rule' => $rule );
        }
        return null;
    }

    /**
     * Whether a group asks this user: every user, or with UserGroups[] only their members.
     *
     * @param array $group
     * @param int[]|null $groupsOfUser filled on first use
     * @param callable|null $memberOf
     * @return bool
     */
    protected static function appliesToUser( array $group, &$groupsOfUser, $memberOf )
    {
        if ( empty( $group['user_groups'] ) )
            return true;
        if ( $groupsOfUser === null )
            $groupsOfUser = $memberOf ? array_map( 'intval', (array)call_user_func( $memberOf ) ) : array();
        return (bool)array_intersect( $group['user_groups'], $groupsOfUser );
    }

    /**
     * The group an extension's trigger handler ([ReauthSettings] TriggerHandlers[], expReauthTriggerHandler) names for a
     * request the rules did not match; only a group that exists, is on and applies to the user counts.
     *
     * @param array $settings
     * @param string $module
     * @param string $view
     * @param string $method
     * @param array $post
     * @param array $params
     * @param eZUser $user
     * @param callable|null $memberOf
     * @return array|null group, rule ("handler:<class>")
     */
    public static function matchHandlers( array $settings, $module, $view, $method, array $post, array $params, eZUser $user, $memberOf = null )
    {
        if ( empty( $settings['enabled'] ) || in_array( $module . '/' . $view, self::NEVER, true ) )
            return null;
        $groupsOfUser = null;
        foreach ( isset( $settings['trigger_handlers'] ) ? $settings['trigger_handlers'] : array() as $class )
        {
            if ( !class_exists( $class ) || !is_subclass_of( $class, 'expReauthTriggerHandler' ) )
                continue;
            $handler = new $class();
            $name = $handler->reauthGroup( $module, $view, $method, $post, $params, $user );
            if ( !is_string( $name ) || !isset( $settings['groups'][$name] ) || empty( $settings['groups'][$name]['enabled'] ) )
                continue;
            if ( !self::appliesToUser( $settings['groups'][$name], $groupsOfUser, $memberOf ) )
                continue;
            return array( 'group' => $name, 'rule' => 'handler:' . $class );
        }
        return null;
    }

    /**
     * How many items the removal of this request takes away: the selection content/removeobject is asked to remove
     * (the session's DeleteIDArray, set by content/action), 0 for another view.
     *
     * @param string $module
     * @param string $view
     * @return int
     */
    public static function removalCount( $module, $view )
    {
        if ( $module . '/' . $view !== 'content/removeobject' )
            return 0;
        $http = eZHTTPTool::instance();
        $ids = $http->hasSessionVariable( 'DeleteIDArray' ) ? $http->sessionVariable( 'DeleteIDArray' ) : array();
        return is_array( $ids ) ? count( $ids ) : 0;
    }

    // ------------------------------------------------------------------------------------------------------------
    // The confirmation in the session
    // ------------------------------------------------------------------------------------------------------------

    /**
     * Whether the user confirmed who they are within the time a confirmation counts.
     *
     * @param eZUser|null $user
     * @return bool
     */
    public static function isFresh( $user = null )
    {
        $user = $user ?: eZUser::currentUser();
        if ( !$user instanceof eZUser || !$user->isRegistered() )
            return false;
        $http = eZHTTPTool::instance();
        if ( !$http->hasSessionVariable( self::SESSION_KEY ) )
            return false;
        $v = $http->sessionVariable( self::SESSION_KEY );
        return is_array( $v ) && isset( $v['user'], $v['until'] ) && (int)$v['user'] === (int)$user->attribute( 'contentobject_id' )
               && (int)$v['until'] > self::now();
    }

    /**
     * Remembers a confirmation for the settings' minutes.
     *
     * @param eZUser $user
     * @param string $method
     * @param int|null $minutes
     */
    public static function markFresh( eZUser $user, $method, $minutes = null )
    {
        $minutes = $minutes === null ? self::settings()['minutes'] : (int)$minutes;
        $http = eZHTTPTool::instance();
        $http->setSessionVariable( self::SESSION_KEY, array( 'user' => (int)$user->attribute( 'contentobject_id' ), 'method' => (string)$method,
                                                              'until' => self::now() + 60 * max( 1, $minutes ) ) );
        if ( $http->hasSessionVariable( self::FAILURES_KEY ) )
            $http->removeSessionVariable( self::FAILURES_KEY );
    }

    /**
     * After a web sign-in: the sign-in counts as a confirmation when it asked for at least one combination "Verify
     * it's you" would offer this account (a password sign-in of an account that has only a password; the password
     * and the authenticator app's code of the two-step sign-in; a passkey of an account without a password), so the
     * user is not asked again right away for the same. A sign-in that asked for less (the password of an account with
     * a passkey) counts for nothing.
     *
     * @param eZUser $user
     * @param string[] $used the ways the sign-in asked for (METHOD_*)
     * @return bool counted
     */
    public static function markFreshAfterSignIn( eZUser $user, array $used )
    {
        $settings = self::settings();
        if ( empty( $settings['enabled'] ) )
            return false;
        if ( !self::signInCounts( self::methods( $user ), $settings['factors'], $used ) )
            return false;
        self::markFresh( $user, 'signin:' . implode( '+', $used ), $settings['minutes'] );
        return true;
    }

    /**
     * Whether a sign-in with the ways $used covers one of the combinations offered to an account with $available.
     * Pure.
     *
     * @param string[] $available methods()
     * @param array[] $factors parseFactors()
     * @param string[] $used
     * @return bool
     */
    public static function signInCounts( array $available, array $factors, array $used )
    {
        foreach ( self::combinations( $available, $factors ) as $combination )
            if ( !array_diff( $combination, $used ) )
                return true;
        return false;
    }

    /** Forgets a confirmation. */
    public static function forget()
    {
        $http = eZHTTPTool::instance();
        if ( $http->hasSessionVariable( self::SESSION_KEY ) )
            $http->removeSessionVariable( self::SESSION_KEY );
    }

    /**
     * @return int seconds the confirmation still counts, 0 when there is none
     */
    public static function secondsLeft( $user = null )
    {
        if ( !self::isFresh( $user ) )
            return 0;
        $v = eZHTTPTool::instance()->sessionVariable( self::SESSION_KEY );
        return max( 0, (int)$v['until'] - self::now() );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Methods
    // ------------------------------------------------------------------------------------------------------------

    /**
     * The ways the user can confirm: password (an account with a password), passkey (with passkeys), code (an
     * authenticator app a code handler knows).
     *
     * @param eZUser $user
     * @return string[]
     */
    public static function methods( eZUser $user )
    {
        $methods = array();
        if ( (int)$user->attribute( 'password_hash_type' ) !== eZUser::PASSWORD_HASH_EMPTY && (string)$user->attribute( 'password_hash' ) !== '' )
            $methods[] = self::METHOD_PASSWORD;
        if ( class_exists( 'expPasskeyService' ) )
        {
            $service = new expPasskeyService();
            if ( $service->available() && expPasskey::countByUser( (int)$user->attribute( 'contentobject_id' ) ) > 0 )
                $methods[] = self::METHOD_PASSKEY;
        }
        foreach ( self::codeHandlers() as $handler )
        {
            if ( $handler->hasReauthCode( $user ) )
            {
                $methods[] = self::METHOD_CODE;
                break;
            }
        }
        return $methods;
    }

    /**
     * @return expReauthCodeHandler[]
     */
    public static function codeHandlers()
    {
        $ini = eZINI::instance();
        $classes = $ini->hasVariable( 'ReauthSettings', 'CodeHandlers' ) ? (array)$ini->variable( 'ReauthSettings', 'CodeHandlers' ) : array();
        $out = array();
        foreach ( $classes as $class )
        {
            $class = trim( (string)$class );
            if ( $class === '' || !class_exists( $class ) )
                continue;
            $handler = new $class();
            if ( $handler instanceof expReauthCodeHandler )
                $out[] = $handler;
        }
        return $out;
    }

    /**
     * The combinations of ways this user is offered ([ReauthSettings] Factors[]).
     *
     * @param eZUser $user
     * @param array|null $settings settings()
     * @return array[]
     */
    public static function offeredCombinations( eZUser $user, $settings = null )
    {
        $settings = $settings ?: self::settings();
        return self::combinations( self::methods( $user ), $settings['factors'] );
    }

    /**
     * Checks an answer of the form: every way of the combination it answers (ExpReauthFactors, one of those offered;
     * the first offered when it names none). The password and the code are checked before the passkey, so a wrong
     * password does not use up the passkey's challenge for nothing.
     *
     * @param eZUser $user
     * @param array $post
     * @param array|null $settings settings()
     * @return array ok, method ("password+passkey"), reason (when not ok)
     */
    public static function verify( eZUser $user, array $post, $settings = null )
    {
        $offered = self::offeredCombinations( $user, $settings );
        $combination = self::chosenCombination( $offered, isset( $post[self::FIELD_FACTORS] ) ? $post[self::FIELD_FACTORS] : null );
        if ( $combination === null )
            return array( 'ok' => false, 'method' => '', 'reason' => 'method' );
        $order = array_values( array_intersect( array( self::METHOD_PASSWORD, self::METHOD_CODE, self::METHOD_PASSKEY ), $combination ) );
        foreach ( $order as $method )
        {
            $r = self::verifyOne( $user, $method, $post );
            if ( !$r['ok'] )
                return array( 'ok' => false, 'method' => implode( '+', $combination ), 'reason' => $r['reason'] );
        }
        return array( 'ok' => true, 'method' => implode( '+', $combination ), 'reason' => null );
    }

    /**
     * Checks one way of an answer.
     *
     * @param eZUser $user
     * @param string $method
     * @param array $post
     * @return array ok, method, reason (when not ok)
     */
    public static function verifyOne( eZUser $user, $method, array $post )
    {
        if ( !in_array( $method, self::methods( $user ), true ) )
            return array( 'ok' => false, 'method' => $method, 'reason' => 'method' );
        switch ( $method )
        {
            case self::METHOD_PASSWORD:
                $password = isset( $post[self::FIELD_PASSWORD] ) && is_string( $post[self::FIELD_PASSWORD] ) ? $post[self::FIELD_PASSWORD] : '';
                $ok = $password !== '' && (bool)eZUser::authenticateHash( (string)$user->attribute( 'login' ), $password, eZUser::site(),
                                                                          $user->attribute( 'password_hash_type' ), (string)$user->attribute( 'password_hash' ) );
                return array( 'ok' => $ok, 'method' => $method, 'reason' => $ok ? null : 'credentials' );
            case self::METHOD_PASSKEY:
                $service = new expPasskeyService();
                $r = $service->verifyForUser( $user, $post );
                return array( 'ok' => $r['ok'], 'method' => $method, 'reason' => $r['ok'] ? null : 'passkey_' . $r['error'] );
            case self::METHOD_CODE:
                $code = isset( $post[self::FIELD_CODE] ) && is_string( $post[self::FIELD_CODE] ) ? $post[self::FIELD_CODE] : '';
                foreach ( self::codeHandlers() as $handler )
                    if ( $handler->hasReauthCode( $user ) && $handler->checkReauthCode( $user, $code ) )
                        return array( 'ok' => true, 'method' => $method, 'reason' => null );
                return array( 'ok' => false, 'method' => $method, 'reason' => 'code' );
        }
        return array( 'ok' => false, 'method' => $method, 'reason' => 'method' );
    }

    // ------------------------------------------------------------------------------------------------------------
    // The gate (ezpKernelWeb)
    // ------------------------------------------------------------------------------------------------------------

    /**
     * Before a view runs: null when it may run, else the module result to answer with instead (the page "Verify it's
     * you", a redirect, or the JSON refusal).
     *
     * @param eZModule $module
     * @param string $view
     * @param array $params the view's ordered parameters
     * @return array|null
     */
    public static function moduleResult( $module, $view, $params = array() )
    {
        if ( !$module instanceof eZModule )
            return null;
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser || !$user->isRegistered() )
            return null;
        $moduleName = (string)$module->attribute( 'name' );
        $view = (string)$view;
        $settings = self::settings();
        if ( empty( $settings['enabled'] ) )
            return null;
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) : 'GET';
        $post = is_array( $_POST ) ? $_POST : array();
        $userID = (int)$user->attribute( 'contentobject_id' );

        $whose = function () use ( $moduleName, $view, $params, $userID, $post ) {
            $addressed = self::addressedUser( $moduleName, $view, (array)$params, self::withoutOwnFields( $post ) );
            if ( $addressed === null )
                return null;
            return $addressed === $userID ? 'self' : 'other';
        };
        $credentials = function () use ( $moduleName, $view, $params, $post ) {
            $target = self::addressedUser( $moduleName, $view, (array)$params, self::withoutOwnFields( $post ) );
            $account = $target ? eZUser::fetch( $target ) : null;
            if ( !$account instanceof eZUser )
                return expReauthRules::changesCredentials( $post, '', '' );
            return expReauthRules::changesCredentials( $post, (string)$account->attribute( 'login' ), (string)$account->attribute( 'email' ) );
        };
        // the answer of the form itself is checked against the original request's rules: without its fields
        $original = self::withoutOwnFields( $post );
        $originalMethod = isset( $post[self::FIELD_GET] ) ? 'GET' : $method;
        $conditions = array( 'bulk' => function () use ( $moduleName, $view, $settings ) {
            return self::removalCount( $moduleName, $view ) >= $settings['bulk_count'];
        } );
        $memberOf = function () use ( $user ) {
            return (array)$user->groups();
        };
        $match = self::matchGroup( $settings, $moduleName, $view, $originalMethod, $original, $whose, $credentials, $conditions, $memberOf );
        if ( $match === null )
            $match = self::matchHandlers( $settings, $moduleName, $view, $originalMethod, $original, (array)$params, $user, $memberOf );
        if ( $match === null )
            return null;
        return self::ask( $module, $view, $match, $settings, $user );
    }

    /**
     * For a view (an extension's too) that decides itself that what it is about to do needs "Verify it's you":
     * null when the user confirmed within the window (go on), else the module result to return instead (the page,
     * which posts the request again after the answer). Anonymous visitors and a switched-off feature or group get
     * null. Call it before the view changes anything:
     *
     *     if ( ( $result = expReauth::requireFresh( $Params['Module'], 'myview', 'system' ) ) !== null )
     *         return $result;
     *
     * @param eZModule $module
     * @param string $view
     * @param string $group a group of [ReauthSettings] Groups[]
     * @return array|null
     */
    public static function requireFresh( $module, $view, $group )
    {
        if ( !$module instanceof eZModule )
            return null;
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser || !$user->isRegistered() )
            return null;
        $settings = self::settings();
        if ( empty( $settings['enabled'] ) || !isset( $settings['groups'][$group] ) || empty( $settings['groups'][$group]['enabled'] ) )
            return null;
        $groupsOfUser = null;
        $memberOf = function () use ( $user ) {
            return (array)$user->groups();
        };
        if ( !self::appliesToUser( $settings['groups'][$group], $groupsOfUser, $memberOf ) )
            return null;
        return self::ask( $module, (string)$view, array( 'group' => (string)$group, 'rule' => 'view' ), $settings, $user );
    }

    /**
     * The question itself, for a request that matched a group: null when the confirmation is fresh or was just
     * given (the view runs), else the module result.
     */
    protected static function ask( $module, $view, array $match, array $settings, eZUser $user )
    {
        $moduleName = (string)$module->attribute( 'name' );
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) : 'GET';
        $post = is_array( $_POST ) ? $_POST : array();
        $original = self::withoutOwnFields( $post );

        if ( self::isFresh( $user ) )
        {
            self::stripOwnFields();
            return null;
        }

        // from here on the answer depends on who asks: no shared cache keeps it
        if ( class_exists( 'ezpRequestRuleKernel' ) )
            ezpRequestRuleKernel::keepOutOfSharedCaches();
        $http = eZHTTPTool::instance();
        $where = $moduleName . '/' . $view;

        if ( $method === 'POST' && array_key_exists( self::FIELD_CANCEL, $post ) )
        {
            $module->redirectTo( self::cancelURI() );
            return array( 'content' => '' );
        }

        $failed = null;
        if ( $method === 'POST' && array_key_exists( self::FIELD_CONFIRM, $post ) )
        {
            $result = self::verify( $user, $post, $settings );
            if ( $result['ok'] )
            {
                self::markFresh( $user, $result['method'], $settings['minutes'] );
                self::audit( 'access.session.reauth', $user, array( 'method' => $result['method'], 'group' => $match['group'],
                                                                    'view' => $where, 'minutes' => $settings['minutes'] ) );
                // a GET (or a POST whose files are gone: the first request carried an upload) is asked for again; a
                // POST runs with what the user sent. The answer itself never carries the files (its form is not
                // multipart), so the mark of the first request decides, not the answer's $_FILES
                if ( isset( $post[self::FIELD_GET] ) || isset( $post[self::FIELD_FILES] ) || self::hasUploads() )
                {
                    $module->redirectTo( self::currentURI() );
                    return array( 'content' => '' );
                }
                self::stripOwnFields();
                return null;
            }
            $failures = (int)$http->sessionVariable( self::FAILURES_KEY, 0 ) + 1;
            $http->setSessionVariable( self::FAILURES_KEY, $failures );
            self::audit( 'access.session.reauth.failed', $user, array( 'method' => $result['method'], 'group' => $match['group'], 'view' => $where,
                                                                      'failures' => $failures ),
                         array( 'result' => 'failed', 'reason' => (string)$result['reason'] ) );
            if ( $failures >= $settings['max_failures'] )
            {
                // a stolen session guessing the password ends here
                $http->removeSessionVariable( self::FAILURES_KEY );
                self::forget();
                $user->logoutCurrent();
                $module->redirectTo( '/user/login' );
                return array( 'content' => '' );
            }
            $failed = $result['reason'];
        }
        else
        {
            self::audit( 'access.session.reauth.ask', $user, array( 'group' => $match['group'], 'view' => $where, 'method' => $method ) );
        }

        if ( self::wantsJson() )
            return self::jsonRefusal();

        $result = self::page( $user, $match, $settings, $original, $method === 'GET' || isset( $post[self::FIELD_GET] ), $failed,
                              (int)$http->sessionVariable( self::FAILURES_KEY, 0 ) );
        // the page stays in the navigation part of the view it stands in for
        $functions = isset( $module->Functions ) && is_array( $module->Functions ) ? $module->Functions : array();
        if ( isset( $functions[$view]['default_navigation_part'] ) )
            $result['navigation_part'] = $functions[$view]['default_navigation_part'];
        return $result;
    }

    /**
     * The account a request addresses, for the rules "self" and "other": the user object edited (content/edit, a
     * class with a user account field), the account of user/password, user/setting, user/edit, user/sessions, and the
     * pending account of an approval item of the event "Approve user accounts" (collaboration/action, the approve and
     * deny of a new editor account, expUserApproval), or of the plain approval event when its item holds a user account
     * or group (the section based setup, a Staff section); null for none. An ordinary content approval addresses no
     * account.
     *
     * @param string $module
     * @param string $view
     * @param array $params ordered view parameters
     * @param array $post the POST fields (collaboration/action names its item there)
     * @return int|null the content object id of the account
     */
    public static function addressedUser( $module, $view, array $params, array $post = array() )
    {
        $first = isset( $params[0] ) && is_numeric( $params[0] ) ? (int)$params[0] : 0;
        $where = $module . '/' . $view;
        $itemID = self::approvalItemID( $module, $view, $post );
        if ( $itemID > 0 )
        {
            if ( !class_exists( 'expUserApproval' ) || expUserApproval::modeOfItem( $itemID ) === null )
                return null;
            $item = eZCollaborationItem::fetch( $itemID );
            return $item instanceof eZCollaborationItem && (int)$item->attribute( 'data_int1' ) > 0 ? (int)$item->attribute( 'data_int1' ) : null;
        }
        if ( in_array( $where, array( 'user/password', 'user/setting', 'user/edit', 'user/sessions' ), true ) )
            return $first > 0 ? $first : (int)eZUser::currentUserID();
        if ( $where === 'content/edit' && $first > 0 )
        {
            $object = eZContentObject::fetch( $first );
            if ( !$object instanceof eZContentObject )
                return null;
            $class = $object->contentClass();
            if ( !$class instanceof eZContentClass )
                return null;
            foreach ( (array)$class->fetchAttributes() as $attribute )
                if ( $attribute->attribute( 'data_type_string' ) === 'ezuser' )
                    return $first;
            return null;
        }
        return null;
    }

    /**
     * The approval item a POST to collaboration/action decides on (the approval type ezapprove, CollaborationItemID),
     * 0 for none. Pure: no database.
     *
     * @param string $module
     * @param string $view
     * @param array $post
     * @return int
     */
    public static function approvalItemID( $module, $view, array $post )
    {
        if ( $module . '/' . $view !== 'collaboration/action' )
            return 0;
        if ( !isset( $post['CollaborationTypeIdentifier'] ) || $post['CollaborationTypeIdentifier'] !== 'ezapprove' )
            return 0;
        $id = isset( $post['CollaborationItemID'] ) && is_scalar( $post['CollaborationItemID'] ) ? (string)$post['CollaborationItemID'] : '';
        return ctype_digit( $id ) ? (int)$id : 0;
    }

    /**
     * @param array $post
     * @return array the POST without the form's own fields
     */
    public static function withoutOwnFields( array $post )
    {
        // the passkey fields belong to the form only in an answer of it; elsewhere they are the view's own (user/passkeys)
        $answer = array_key_exists( self::FIELD_CONFIRM, $post );
        foreach ( array( self::FIELD_METHOD, self::FIELD_FACTORS, self::FIELD_PASSWORD, self::FIELD_CODE, self::FIELD_CONFIRM, self::FIELD_CANCEL, self::FIELD_GET,
                         self::FIELD_FILES ) as $f )
            unset( $post[$f] );
        if ( $answer )
            foreach ( array( 'PasskeyCredentialID', 'PasskeyClientData', 'PasskeyAuthenticatorData', 'PasskeySignature', 'PasskeyUserHandle' ) as $f )
                unset( $post[$f] );
        return $post;
    }

    /**
     * Whether a request carries a real upload: a file chosen in at least one file input. A multipart form with file
     * inputs left empty still fills $_FILES, with UPLOAD_ERR_NO_FILE for each of them; those are no upload. Inputs
     * named as arrays (name[] or name[key][...]) give nested arrays below 'error', and every level is looked at. A file
     * that was chosen but did not arrive (too big, a partial upload) counts: it has to be chosen again as well.
     *
     * @param array|null $files $_FILES when null
     * @return bool
     */
    public static function hasUploads( $files = null )
    {
        if ( $files === null )
            $files = $_FILES;
        foreach ( (array)$files as $entry )
            if ( is_array( $entry ) && array_key_exists( 'error', $entry ) && self::chosen( $entry['error'] ) )
                return true;
        return false;
    }

    /**
     * @param mixed $error the 'error' of one $_FILES entry, an array for inputs named as arrays
     * @return bool a file was chosen in at least one of the inputs
     */
    protected static function chosen( $error )
    {
        if ( is_array( $error ) )
        {
            foreach ( $error as $e )
                if ( self::chosen( $e ) )
                    return true;
            return false;
        }
        return is_numeric( $error ) && (int)$error !== UPLOAD_ERR_NO_FILE;
    }

    /** Takes the form's fields out of the request, so the view sees exactly what the user sent first. */
    protected static function stripOwnFields()
    {
        $_POST = self::withoutOwnFields( $_POST );
    }

    /**
     * The POST fields as name => value pairs for hidden inputs: nested arrays flattened to name[key], the form token
     * left out (the page's form gets its own).
     *
     * @param array $post
     * @param string $prefix
     * @return array[] name, value
     */
    public static function flatten( array $post, $prefix = '' )
    {
        $out = array();
        foreach ( $post as $key => $value )
        {
            if ( $prefix === '' && $key === 'ezxform_token' )
                continue;
            $name = $prefix === '' ? (string)$key : $prefix . '[' . $key . ']';
            if ( is_array( $value ) )
                $out = array_merge( $out, self::flatten( $value, $name ) );
            elseif ( is_scalar( $value ) || $value === null )
                $out[] = array( 'name' => $name, 'value' => (string)$value );
        }
        return $out;
    }

    /**
     * The page "Verify it's you".
     */
    protected static function page( eZUser $user, array $match, array $settings, array $post, $isGet, $failed, $failures )
    {
        $methods = self::methods( $user );
        $combinations = array();
        $usesPasskey = false;
        foreach ( self::combinations( $methods, $settings['factors'] ) as $combination )
        {
            $combinations[] = array( 'key' => implode( '+', $combination ), 'methods' => $combination );
            $usesPasskey = $usesPasskey || in_array( self::METHOD_PASSKEY, $combination, true );
        }
        $passkeyOptions = null;
        if ( $usesPasskey )
        {
            $service = new expPasskeyService();
            $passkeyOptions = $service->reauthOptions( $user );
        }
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'methods', $methods );
        // the forms, the first the one to use; each lists the ways it asks for together (password, passkey, code)
        $tpl->setVariable( 'combinations', $combinations );
        $tpl->setVariable( 'group', $match['group'] );
        $tpl->setVariable( 'minutes', $settings['minutes'] );
        $tpl->setVariable( 'failed', $failed !== null );
        $tpl->setVariable( 'failed_reason', (string)$failed );
        $tpl->setVariable( 'failures_left', max( 0, $settings['max_failures'] - (int)$failures ) );
        $tpl->setVariable( 'replay', $isGet ? array() : self::flatten( $post ) );
        $tpl->setVariable( 'replay_get', (bool)$isGet );
        // only a file actually chosen: a form whose file inputs were left empty is replayed whole and needs no notice
        $tpl->setVariable( 'had_files', !$isGet && self::hasUploads() );
        $tpl->setVariable( 'passkey_options', $passkeyOptions ? json_encode( $passkeyOptions, JSON_UNESCAPED_SLASHES ) : '' );
        $tpl->setVariable( 'login', (string)$user->attribute( 'login' ) );
        $tpl->setVariable( 'action_uri', self::currentURI() );
        $tpl->setVariable( 'cancel_uri', self::cancelURI() );
        $title = ezpI18n::tr( 'design/standard/user/reauth', "Verify it's you" );
        // the page itself is never stored by a browser or a shared cache
        header( 'Cache-Control: no-store, private' );
        return array( 'content' => $tpl->fetch( 'design:user/reauth.tpl' ),
                      'path' => array( array( 'text' => $title, 'url' => false ) ) );
    }

    /**
     * The address of the request, as the form posts back to it (the siteaccess is added by ezurl).
     *
     * @return string
     */
    public static function currentURI()
    {
        $uri = isset( $GLOBALS['eZRequestedURI'] ) && $GLOBALS['eZRequestedURI'] instanceof eZURI
            ? '/' . ltrim( $GLOBALS['eZRequestedURI']->originalURIString( false ), '/' ) : '/';
        $query = class_exists( 'eZSys' ) ? (string)eZSys::queryString() : '';
        return $uri . $query;
    }

    /**
     * Where Cancel goes: the page the user came from on this site, else the start page.
     *
     * @return string
     */
    public static function cancelURI()
    {
        $http = eZHTTPTool::instance();
        $last = $http->hasSessionVariable( 'LastAccessesURI' ) ? (string)$http->sessionVariable( 'LastAccessesURI' ) : '';
        return ( $last !== '' && $last[0] === '/' && strpos( $last, '//' ) !== 0 ) ? $last : '/';
    }

    /** @return bool the request was made by a script that expects JSON */
    protected static function wantsJson()
    {
        $xhr = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strcasecmp( $_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest' ) === 0;
        $accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? (string)$_SERVER['HTTP_ACCEPT'] : '';
        return $xhr || ( stripos( $accept, 'application/json' ) !== false && stripos( $accept, 'text/html' ) === false );
    }

    /** @return array 403 with {"error":"reauth_required","url":...}, no page layout */
    protected static function jsonRefusal()
    {
        header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden' );
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Cache-Control: no-store, private' );
        $url = self::currentURI();
        eZURI::transformURI( $url );
        return array( 'content' => json_encode( array( 'error' => 'reauth_required', 'url' => $url ) ), 'pagelayout' => false );
    }

    /**
     * @param string $name
     * @param eZUser $user
     * @param array $after
     * @param array $extra result, reason
     */
    protected static function audit( $name, eZUser $user, array $after, array $extra = array() )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        expAuditHook::emit( $name, array( 'object' => expAuditHook::user( $user ), 'after' => $after ) + $extra );
    }

    /** Sets a fixed clock (tests): Unix seconds, or null for the real one. */
    public static function setNow( $now )
    {
        self::$now = $now === null ? null : (int)$now;
    }

    /** @return int */
    protected static function now()
    {
        return self::$now !== null ? self::$now : time();
    }
}
