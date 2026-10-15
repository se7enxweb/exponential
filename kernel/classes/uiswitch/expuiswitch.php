<?php
/**
 * File containing the expUISwitch class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The UI switch framework: a registry of providers (uiswitch.ini [UISwitchSettings] ProviderList[]), the user's
 * choices kept in the session, and the early hook that applies them in every request.
 *
 * - ezpKernelWeb::requestInit() calls beginRequest() and applyRequest() right after the session start, before the
 *   login pre-check, the request/input event, the locale and any template.
 * - A choice is applied only on a switch siteaccess (SiteAccessList[], or every siteaccess with an administration
 *   design), for a signed-in user who holds uiswitch/<provider> now, and only while the value is still in the
 *   provider's allow-list: all checked in every request, not only when switching.
 * - The session never starts for this: without a session there is nothing to apply.
 * - Persistent Velocity workers: beginRequest() forgets the providers and the applied values of the request before
 *   and asks every provider that applied something to reset() what it touched.
 * - cacheVariantKey() ('' when nothing is applied) separates caches built with a choice: {cache-block}, the content
 *   view cache, compiled templates. Generated code reads it from $GLOBALS['expUISwitchVariantKey'], so it never
 *   needs this class.
 *
 * Guide: doc/bc/6.0/ui-switch.md; settings: doc/guides/ui-switch-settings.md
 */
class expUISwitch
{
    /** the session variable: array( 'user' => <user id>, 'values' => array( <siteaccess> => array( <provider> => <value> ) ) ) */
    const SESSION_VARIABLE = 'expUISwitch';

    /** eZPreferences name prefix, for providers with RememberPreference=enabled */
    const PREFERENCE_PREFIX = 'exp_uiswitch_';

    /** the global the generated cache code reads */
    const GLOBAL_KEY = 'expUISwitchVariantKey';

    /** the policy module */
    const POLICY_MODULE = 'uiswitch';

    /** a provider identifier, also a policy function name */
    const IDENTIFIER_PATTERN = '/^[a-z][a-z0-9_]{0,31}$/';

    /** the audit event of a change */
    const AUDIT_EVENT = 'access.uiswitch.change';

    /** @var expUISwitchProviderInterface[]|null identifier => provider, for this request */
    private static $providers = null;

    /** @var array identifier => value applied in this request */
    private static $applied = array();

    /** @var expUISwitchProviderInterface[] providers that applied a value in this process, reset next request */
    private static $touched = array();

    /** @var eZINI|null settings injected by tests */
    private static $settingsOverride = null;

    // ------------------------------------------------------------------ settings and registry

    /**
     * uiswitch.ini
     *
     * @return eZINI
     */
    public static function settings()
    {
        return self::$settingsOverride !== null ? self::$settingsOverride : eZINI::instance( 'uiswitch.ini' );
    }

    /**
     * Uses other settings than uiswitch.ini (tests); null goes back to uiswitch.ini. Forgets the providers.
     *
     * @param eZINI|null $ini
     */
    public static function setSettings( ?eZINI $ini = null )
    {
        self::$settingsOverride = $ini;
        self::$providers = null;
    }

    /**
     * Whether the framework is switched on (uiswitch.ini [UISwitchSettings] Enabled, default true).
     *
     * @param eZINI|null $ini
     * @return bool
     */
    public static function isEnabled( ?eZINI $ini = null )
    {
        $ini = $ini ?: self::settings();
        if ( !$ini->hasVariable( 'UISwitchSettings', 'Enabled' ) )
            return true;
        return in_array( strtolower( (string)$ini->variable( 'UISwitchSettings', 'Enabled' ) ), array( 'true', 'enabled', '1' ), true );
    }

    /**
     * The provider identifiers ProviderList[] names, well-formed and each once, whether or not their class exists.
     * module.php builds the policy functions from it.
     *
     * @param eZINI|null $ini
     * @return string[]
     */
    public static function providerIdentifiers( ?eZINI $ini = null )
    {
        $ini = $ini ?: self::settings();
        $list = array();
        $names = $ini->hasVariable( 'UISwitchSettings', 'ProviderList' ) ? (array)$ini->variable( 'UISwitchSettings', 'ProviderList' ) : array();
        foreach ( $names as $identifier )
        {
            if ( is_string( $identifier ) && preg_match( self::IDENTIFIER_PATTERN, $identifier ) && !in_array( $identifier, $list, true ) )
                $list[] = $identifier;
        }
        return $list;
    }

    /**
     * The policy functions of the uiswitch module: one per provider named in ProviderList[], and language always.
     *
     * @param eZINI|null $ini
     * @return string[]
     */
    public static function policyFunctions( ?eZINI $ini = null )
    {
        try
        {
            $list = self::providerIdentifiers( $ini );
        }
        catch ( Throwable $e )
        {
            $list = array();
        }
        if ( !in_array( 'language', $list, true ) )
            array_unshift( $list, 'language' );
        return $list;
    }

    /**
     * Builds the providers from settings: each [UISwitch_<id>] Class= that exists, implements
     * expUISwitchProviderInterface and is not Enabled=false. A missing class is skipped (a Velocity worker started
     * before the class was added never fails on it).
     *
     * @param eZINI $ini
     * @return expUISwitchProviderInterface[] identifier => provider
     */
    public static function buildProviders( eZINI $ini )
    {
        $providers = array();
        foreach ( self::providerIdentifiers( $ini ) as $identifier )
        {
            $group = 'UISwitch_' . $identifier;
            if ( !$ini->hasGroup( $group ) )
                continue;
            $settings = $ini->group( $group );
            if ( isset( $settings['Enabled'] ) && in_array( strtolower( (string)$settings['Enabled'] ), array( 'false', 'disabled', '0' ), true ) )
                continue;
            $class = isset( $settings['Class'] ) ? (string)$settings['Class'] : '';
            if ( $class === '' || !preg_match( '/^[A-Za-z_][A-Za-z0-9_\\\\]*$/', $class ) || !class_exists( $class ) )
            {
                eZDebug::writeWarning( "UI switch provider '$identifier': class '$class' not found", __METHOD__ );
                continue;
            }
            if ( !is_subclass_of( $class, 'expUISwitchProviderInterface' ) )
            {
                eZDebug::writeWarning( "UI switch provider '$identifier': '$class' does not implement expUISwitchProviderInterface", __METHOD__ );
                continue;
            }
            $provider = new $class( $identifier, $settings );
            if ( $provider->identifier() !== $identifier )
                continue;
            $providers[$identifier] = $provider;
        }
        return $providers;
    }

    /**
     * The providers of this request.
     *
     * @return expUISwitchProviderInterface[] identifier => provider
     */
    public static function providers()
    {
        if ( self::$providers === null )
        {
            try
            {
                self::$providers = self::isEnabled() ? self::buildProviders( self::settings() ) : array();
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( $e->getMessage(), __METHOD__ );
                self::$providers = array();
            }
        }
        return self::$providers;
    }

    /**
     * @param string $identifier
     * @return expUISwitchProviderInterface|null
     */
    public static function provider( $identifier )
    {
        $providers = self::providers();
        return is_string( $identifier ) && isset( $providers[$identifier] ) ? $providers[$identifier] : null;
    }

    // ------------------------------------------------------------------ request lifecycle

    /**
     * Start of a request: forgets everything of the request before (persistent Velocity workers) and asks every
     * provider that applied a value earlier in this process to reset what it touched.
     */
    public static function beginRequest()
    {
        foreach ( self::$touched as $provider )
        {
            try
            {
                $provider->reset();
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( $e->getMessage(), __METHOD__ );
            }
        }
        self::$touched = array();
        self::$providers = null;
        self::$applied = array();
        $GLOBALS[self::GLOBAL_KEY] = '';
    }

    /**
     * Applies the signed-in user's choices for this request. Never starts a session, never throws.
     *
     * @return array identifier => value applied
     */
    public static function applyRequest()
    {
        try
        {
            if ( !class_exists( 'eZSession' ) || !eZSession::hasStarted() || !self::isEnabled() || !self::isSwitchSiteAccess() )
                return array();
            $state = eZSession::get( self::SESSION_VARIABLE, array() );
            $user = eZUser::currentUser();
            if ( !$user instanceof eZUser || !$user->isRegistered() )
                return array();
            $siteaccess = self::currentSiteAccess();

            $stored = self::valuesFromState( $state, (int)$user->id(), $siteaccess );
            $keep = self::applyValues( $stored, $user );
            // what failed the checks is dropped from the session
            if ( $stored && array_diff_assoc( $stored, $keep ) )
                self::storeValues( $state, (int)$user->id(), $siteaccess, array_intersect_key( $keep, $stored ) );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
        return self::$applied;
    }

    /**
     * Applies stored values for a user: each only when the user holds uiswitch/<provider> now, the value is in the
     * provider's allow-list and is not its default. A provider without a stored value may take the one the user's
     * preferences remember (RememberPreference=enabled). Sets the cache variant key of the request.
     *
     * @param array $stored identifier => value
     * @param eZUser $user
     * @return array identifier => value applied
     */
    public static function applyValues( array $stored, eZUser $user )
    {
        $applied = array();
        foreach ( self::providers() as $identifier => $provider )
        {
            $value = array_key_exists( $identifier, $stored ) ? $stored[$identifier] : null;
            if ( $value === null && $provider instanceof expUISwitchProvider && $provider->remembersPreference() )
                $value = self::rememberedValue( $identifier );
            if ( !is_string( $value ) )
                continue;
            // re-checked every request: the policy may be gone, the allow-list may have changed
            if ( !self::canUse( $identifier, $user ) || !$provider->isValid( $value ) || $value === $provider->defaultValue() )
                continue;
            if ( $provider->apply( $value ) )
            {
                self::$applied[$identifier] = $value;
                self::$touched[$identifier] = $provider;
                $applied[$identifier] = $value;
            }
        }
        $GLOBALS[self::GLOBAL_KEY] = self::cacheVariantKey();
        return $applied;
    }

    /**
     * The values applied in this request.
     *
     * @return array identifier => value
     */
    public static function appliedValues()
    {
        return self::$applied;
    }

    /**
     * The cache variant key of this request: '' when nothing is applied, otherwise the providers' keys sorted by
     * identifier and joined with '.'.
     *
     * @return string
     */
    public static function cacheVariantKey()
    {
        $keys = array();
        foreach ( self::$applied as $identifier => $value )
        {
            $provider = isset( self::$touched[$identifier] ) ? self::$touched[$identifier] : self::provider( $identifier );
            if ( $provider )
                $keys[$identifier] = $provider->cacheVariantKey( $value );
        }
        return self::composeVariantKey( $keys );
    }

    /**
     * @param array $keys identifier => key
     * @return string
     */
    public static function composeVariantKey( array $keys )
    {
        ksort( $keys, SORT_STRING );
        $parts = array();
        foreach ( $keys as $key )
        {
            if ( is_string( $key ) && $key !== '' )
                $parts[] = preg_replace( '/[^A-Za-z0-9_.-]/', '_', $key );
        }
        return implode( '.', $parts );
    }

    // ------------------------------------------------------------------ where and who

    /**
     * @return string the current siteaccess name, '' when there is none
     */
    public static function currentSiteAccess()
    {
        return isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
    }

    /**
     * Whether the switch works on a siteaccess: one of uiswitch.ini [UISwitchSettings] SiteAccessList[] when that is
     * not empty, otherwise one whose design list (SiteDesign, AdditionalSiteDesignList[]) has an administration
     * design (admin, admin2 ... admin4l, adminui).
     *
     * @param string|null $siteaccess the current one when null
     * @param eZINI|null $ini uiswitch.ini
     * @param array|null $designs the siteaccess's designs; from the current site.ini when null
     * @return bool
     */
    public static function isSwitchSiteAccess( $siteaccess = null, ?eZINI $ini = null, ?array $designs = null )
    {
        $siteaccess = $siteaccess === null ? self::currentSiteAccess() : (string)$siteaccess;
        if ( $siteaccess === '' )
            return false;
        $ini = $ini ?: self::settings();
        $list = $ini->hasVariable( 'UISwitchSettings', 'SiteAccessList' ) ? array_values( array_filter( (array)$ini->variable( 'UISwitchSettings', 'SiteAccessList' ), 'strlen' ) ) : array();
        if ( $list )
            return in_array( $siteaccess, $list, true );
        if ( $designs === null )
        {
            $site = eZINI::instance();
            $designs = array_merge( array( (string)$site->variable( 'DesignSettings', 'SiteDesign' ) ),
                                    $site->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) ? (array)$site->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) : array() );
        }
        foreach ( $designs as $design )
        {
            if ( is_string( $design ) && preg_match( '/^admin/', $design ) )
                return true;
        }
        return false;
    }

    /**
     * Whether a user may use a provider: signed in, and holding uiswitch/<identifier>.
     *
     * @param string $identifier
     * @param eZUser|null $user the current user when null
     * @return bool
     */
    public static function canUse( $identifier, ?eZUser $user = null )
    {
        if ( !is_string( $identifier ) || !preg_match( self::IDENTIFIER_PATTERN, $identifier ) )
            return false;
        $user = $user ?: eZUser::currentUser();
        if ( !$user instanceof eZUser || !$user->isRegistered() )
            return false;
        $access = $user->hasAccessTo( self::POLICY_MODULE, $identifier );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    // ------------------------------------------------------------------ storage

    /**
     * The values of one user and one siteaccess in a session state; the ill-formed ones and those of another user
     * are left out.
     *
     * @param mixed $state the session variable
     * @param int $userID
     * @param string $siteaccess
     * @return array identifier => value
     */
    public static function valuesFromState( $state, $userID, $siteaccess )
    {
        if ( !is_array( $state ) || !isset( $state['user'], $state['values'] ) || (int)$state['user'] !== (int)$userID || $userID <= 0 )
            return array();
        $values = isset( $state['values'][$siteaccess] ) && is_array( $state['values'][$siteaccess] ) ? $state['values'][$siteaccess] : array();
        $result = array();
        foreach ( $values as $identifier => $value )
        {
            if ( is_string( $identifier ) && preg_match( self::IDENTIFIER_PATTERN, $identifier )
                 && is_string( $value ) && preg_match( expUISwitchProvider::VALUE_PATTERN, $value ) )
                $result[$identifier] = $value;
        }
        return $result;
    }

    /**
     * A session state with the values of one siteaccess replaced; the values of another user are dropped.
     *
     * @param mixed $state
     * @param int $userID
     * @param string $siteaccess
     * @param array $values identifier => value
     * @return array
     */
    public static function stateWithValues( $state, $userID, $siteaccess, array $values )
    {
        if ( !is_array( $state ) || !isset( $state['user'] ) || (int)$state['user'] !== (int)$userID || !isset( $state['values'] ) || !is_array( $state['values'] ) )
            $state = array( 'user' => (int)$userID, 'values' => array() );
        if ( $values )
            $state['values'][$siteaccess] = $values;
        else
            unset( $state['values'][$siteaccess] );
        return $state;
    }

    private static function storeValues( $state, $userID, $siteaccess, array $values )
    {
        $state = self::stateWithValues( $state, $userID, $siteaccess, $values );
        if ( empty( $state['values'] ) )
            eZSession::unsetkey( self::SESSION_VARIABLE, false );
        else
            eZSession::set( self::SESSION_VARIABLE, $state );
    }

    /**
     * The value the user's preferences remember for a provider, or null.
     *
     * @param string $identifier
     * @return string|null
     */
    private static function rememberedValue( $identifier )
    {
        if ( !class_exists( 'eZPreferences' ) )
            return null;
        $value = eZPreferences::value( self::PREFERENCE_PREFIX . $identifier );
        return is_string( $value ) && $value !== '' ? $value : null;
    }

    /**
     * The value stored for the current user on the current siteaccess, or null.
     *
     * @param string $identifier
     * @return string|null
     */
    public static function storedValue( $identifier )
    {
        if ( !eZSession::hasStarted() )
            return null;
        $user = eZUser::currentUser();
        $values = self::valuesFromState( eZSession::get( self::SESSION_VARIABLE, array() ), (int)$user->id(), self::currentSiteAccess() );
        return isset( $values[$identifier] ) ? $values[$identifier] : null;
    }

    /**
     * Sets the current user's choice for a provider on the current siteaccess. The default value clears it.
     *
     * @param string $identifier
     * @param mixed $value
     * @return string ok | unavailable (no such provider, not a switch siteaccess) | denied | invalid
     */
    public static function set( $identifier, $value )
    {
        if ( !self::isEnabled() || !self::isSwitchSiteAccess() )
            return 'unavailable';
        $provider = self::provider( $identifier );
        if ( !$provider )
            return 'unavailable';
        $user = eZUser::currentUser();
        if ( !self::canUse( $identifier, $user ) )
            return 'denied';
        if ( !$provider->isValid( $value ) )
            return 'invalid';

        $siteaccess = self::currentSiteAccess();
        $state = eZSession::get( self::SESSION_VARIABLE, array() );
        $values = self::valuesFromState( $state, (int)$user->id(), $siteaccess );
        $before = isset( $values[$identifier] ) ? $values[$identifier] : $provider->defaultValue();
        if ( $value === $provider->defaultValue() )
            unset( $values[$identifier] );
        else
            $values[$identifier] = $value;
        self::storeValues( $state, (int)$user->id(), $siteaccess, $values );

        if ( $provider instanceof expUISwitchProvider && $provider->remembersPreference() && class_exists( 'eZPreferences' ) )
            eZPreferences::setValue( self::PREFERENCE_PREFIX . $identifier, $value === $provider->defaultValue() ? '' : $value );

        if ( class_exists( 'expAudit' ) )
            expAudit::event( self::AUDIT_EVENT, array( 'object' => array( 'type' => 'user', 'id' => (int)$user->id() ),
                                                       'target' => array( 'type' => 'uiswitch', 'id' => $identifier ),
                                                       'before' => array( 'value' => $before, 'siteaccess' => $siteaccess ),
                                                       'after' => array( 'value' => $value, 'siteaccess' => $siteaccess ),
                                                       'result' => 'success' ) );
        return 'ok';
    }

    // ------------------------------------------------------------------ request checks

    /**
     * Whether a request may change a switch, from its Sec-Fetch-Site header: refused when the browser says it comes
     * from another site, as the kernel's preference writes do. The form token is checked by the kernel for every
     * POST before the view runs.
     *
     * @param string|null $secFetchSite
     * @return bool
     */
    public static function isSameSiteRequest( $secFetchSite )
    {
        return $secFetchSite === null || $secFetchSite === '' || $secFetchSite === 'same-origin' || $secFetchSite === 'same-site';
    }

    /**
     * A local path to go back to, or the fallback: "/x/y" only, never "//host", a scheme, a backslash or control
     * characters, at most 2048 characters.
     *
     * @param mixed $uri
     * @param string $fallback
     * @return string
     */
    public static function safeRedirect( $uri, $fallback = '/' )
    {
        if ( !is_string( $uri ) )
            return $fallback;
        $uri = trim( $uri );
        if ( $uri === '' || strlen( $uri ) > 2048 )
            return $fallback;
        if ( $uri[0] !== '/' )
            $uri = '/' . $uri;
        if ( strpos( $uri, '//' ) === 0 || strpos( $uri, '\\' ) !== false || preg_match( '/[\x00-\x1F\x7F]/', $uri )
             || preg_match( '#^/+[a-z][a-z0-9+.-]*:#i', $uri ) || preg_match( '#/\.\.?(/|$)#', $uri ) )
            return $fallback;
        return $uri;
    }

    // ------------------------------------------------------------------ for templates and the state view

    /**
     * What the toolbar shows: the providers the current user may use on this siteaccess, with their choices.
     * Reached from templates through fetch( 'uiswitch', 'toolbar' ).
     *
     * @return array list of hash( identifier, current, default, values => list of hash( value, label, current ) )
     */
    public static function toolbar()
    {
        $result = array();
        try
        {
            if ( !self::isEnabled() || !self::isSwitchSiteAccess() )
                return $result;
            $user = eZUser::currentUser();
            foreach ( self::providers() as $identifier => $provider )
            {
                if ( !self::canUse( $identifier, $user ) )
                    continue;
                $allowed = $provider->allowedValues();
                if ( count( $allowed ) < 2 )
                    continue;
                $current = $provider->currentValue();
                $base = $provider instanceof expUISwitchProvider;
                $values = array();
                $currentLabel = $current;
                foreach ( $allowed as $value )
                {
                    $label = $provider->label( $value );
                    if ( $value === $current )
                        $currentLabel = $label;
                    $values[] = array( 'value' => $value, 'label' => $label, 'current' => $value === $current,
                                       'default' => $value === $provider->defaultValue(),
                                       'lang' => $base ? $provider->labelLanguage( $value ) : '' );
                }
                $result[] = array( 'identifier' => $identifier, 'title' => $base ? $provider->title() : $identifier,
                                   'current' => $current, 'current_label' => $currentLabel,
                                   'default' => $provider->defaultValue(), 'values' => $values );
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
        return $result;
    }

    /**
     * fetch( 'uiswitch', 'toolbar' )
     *
     * @return array
     */
    public static function fetchToolbar()
    {
        return array( 'result' => self::toolbar() );
    }
}
