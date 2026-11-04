<?php
/**
 * File containing the expFrontDesignSwitch class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A front siteaccess that switches between designs: every visitor, signed in or not, picks one of the designs the
 * siteaccess lists, with one click. Off unless site.ini [DesignSettings] SwitchableDesigns[] lists designs, and never
 * on an administration siteaccess (a design list with an "admin..." design: the administration has expUISwitch).
 *
 * - The choice comes from the view parameter /(design)/<name> on any address, which also stores it in a cookie
 *   (DesignSwitchCookie, default exp_design), or from that cookie. A name not in SwitchableDesigns[], or a design
 *   that does not exist, is ignored. The siteaccess's own SiteDesign is the default; choosing it clears the cookie.
 * - Applied in ezpKernelWeb::requestInit(), right after expUISwitch and before the locale, the design and any
 *   template: SiteDesign and AdditionalSiteDesignList[] are set in memory for this request only
 *   (SwitchableDesignChain[<design>], designs separated by ";", is the fallback chain of a design; without one the
 *   siteaccess's own list stays).
 * - Caches: a chosen design adds "fd-<design>" to the request's cache variant key ($GLOBALS['expUISwitchVariantKey']),
 *   which separates the design base cache, compiled templates, {cache-block} and the content view cache. Responses of
 *   a switching siteaccess send Vary: Cookie; a page in a chosen design (not the default) is private.
 * - Persistent Velocity workers: beginRequest() puts back what the request before changed.
 *
 * Templates: fetch( 'uiswitch', 'front_designs' ) gives the current design, the default and the choices with their
 * labels (SwitchableDesignLabel[<design>], translated in the context kernel/uiswitch) and the view parameter.
 * Guide: doc/eng-us/features/6.0/front-design-switch.md
 */
class expFrontDesignSwitch
{
    /** a design directory name */
    const DESIGN_PATTERN = '/^[a-z][a-z0-9_]{0,63}$/';

    /** the view parameter that chooses a design */
    const VIEW_PARAMETER = 'design';

    /** the cookie when DesignSwitchCookie is not set */
    const DEFAULT_COOKIE = 'exp_design';

    /** the cookie's lifetime, one year */
    const COOKIE_LIFETIME = 31536000;

    /** @var string|null the design applied in this request (not the default), null when none */
    private static $applied = null;

    /** @var array|null the SiteDesign and AdditionalSiteDesignList before this request changed them */
    private static $previous = null;

    /** @var bool whether the current siteaccess switches designs */
    private static $active = false;

    /** @var string[]|null design directories that exist, injected by tests */
    private static $existingDesigns = null;

    // ------------------------------------------------------------------ settings

    /**
     * The designs a siteaccess offers: SwitchableDesigns[] that are design names, in their order, without doubles.
     * Empty on an administration siteaccess and when the setting is not there.
     *
     * @param eZINI|null $ini site.ini of the siteaccess
     * @return string[]
     */
    public static function designs( ?eZINI $ini = null )
    {
        $ini = $ini ?: eZINI::instance();
        if ( !$ini->hasVariable( 'DesignSettings', 'SwitchableDesigns' ) || self::isAdministration( $ini ) )
            return array();
        $list = array();
        foreach ( (array)$ini->variable( 'DesignSettings', 'SwitchableDesigns' ) as $name )
        {
            $name = trim( (string)$name );
            if ( preg_match( self::DESIGN_PATTERN, $name ) && !in_array( $name, $list, true ) )
                $list[] = $name;
        }
        return $list;
    }

    /**
     * Whether a design list belongs to an administration siteaccess: a SiteDesign or additional design that begins
     * with "admin" (admin, admin2 ... admin4l, adminui), as expUISwitch decides it.
     *
     * @param eZINI $ini
     * @return bool
     */
    public static function isAdministration( eZINI $ini )
    {
        $designs = array( (string)$ini->variable( 'DesignSettings', 'SiteDesign' ) );
        if ( $ini->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) )
            $designs = array_merge( $designs, (array)$ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
        foreach ( $designs as $design )
        {
            if ( is_string( $design ) && strpos( $design, 'admin' ) === 0 )
                return true;
        }
        return false;
    }

    /**
     * @param eZINI|null $ini
     * @return string the cookie name
     */
    public static function cookieName( ?eZINI $ini = null )
    {
        $ini = $ini ?: eZINI::instance();
        $name = $ini->hasVariable( 'DesignSettings', 'DesignSwitchCookie' ) ? trim( (string)$ini->variable( 'DesignSettings', 'DesignSwitchCookie' ) ) : '';
        return preg_match( '/^[A-Za-z0-9_]{1,64}$/', $name ) ? $name : self::DEFAULT_COOKIE;
    }

    /**
     * The fallback chain of a design (SwitchableDesignChain[<design>]), null when none is configured.
     *
     * @param string $design
     * @param eZINI|null $ini
     * @return string[]|null
     */
    public static function chain( $design, ?eZINI $ini = null )
    {
        $ini = $ini ?: eZINI::instance();
        $chains = $ini->hasVariable( 'DesignSettings', 'SwitchableDesignChain' ) ? (array)$ini->variable( 'DesignSettings', 'SwitchableDesignChain' ) : array();
        if ( !is_string( $design ) || !array_key_exists( $design, $chains ) )
            return null;
        $list = array();
        foreach ( explode( ';', (string)$chains[$design] ) as $name )
        {
            $name = trim( $name );
            if ( $name !== '' && $name !== $design && preg_match( self::DESIGN_PATTERN, $name ) && !in_array( $name, $list, true ) )
                $list[] = $name;
        }
        return $list;
    }

    /**
     * The label of a design (SwitchableDesignLabel[<design>], translated), the design name without one.
     *
     * @param string $design
     * @param eZINI|null $ini
     * @return string
     */
    public static function label( $design, ?eZINI $ini = null )
    {
        $ini = $ini ?: eZINI::instance();
        $labels = $ini->hasVariable( 'DesignSettings', 'SwitchableDesignLabel' ) ? (array)$ini->variable( 'DesignSettings', 'SwitchableDesignLabel' ) : array();
        $label = isset( $labels[$design] ) ? trim( (string)$labels[$design] ) : '';
        if ( $label === '' )
            return (string)$design;
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/uiswitch', $label ) : $label;
    }

    /**
     * Uses this list of existing designs instead of looking on the disk (tests); null looks on the disk again.
     *
     * @param string[]|null $designs
     */
    public static function setExistingDesigns( ?array $designs = null )
    {
        self::$existingDesigns = $designs;
    }

    /**
     * Whether a design directory exists: design/<design>, or design/<design> of an active design extension.
     *
     * @param string $design
     * @return bool
     */
    public static function designExists( $design )
    {
        if ( self::$existingDesigns !== null )
            return in_array( $design, self::$existingDesigns, true );
        if ( is_dir( 'design/' . $design ) )
            return true;
        foreach ( (array)eZINI::instance( 'design.ini' )->variable( 'ExtensionSettings', 'DesignExtensions' ) as $name )
        {
            $path = eZExtension::extensionPath( eZExtension::extensionName( $name ) );
            if ( $path && is_dir( $path . '/design/' . $design ) )
                return true;
        }
        return false;
    }

    // ------------------------------------------------------------------ the choice

    /**
     * The design named by the view parameter /(design)/<name> in an address path, '' when there is none.
     *
     * @param string $uri the request address (path, maybe with a query)
     * @return string
     */
    public static function designFromUri( $uri )
    {
        $path = (string)parse_url( (string)$uri, PHP_URL_PATH );
        if ( preg_match( '#/\(' . self::VIEW_PARAMETER . '\)/([^/]+)#', $path, $m ) )
        {
            $name = rawurldecode( $m[1] );
            return preg_match( self::DESIGN_PATTERN, $name ) ? $name : '';
        }
        return '';
    }

    /**
     * Which design a request asks for, and whether the cookie has to change: the view parameter wins over the cookie.
     * Only a design of $designs that exists counts.
     *
     * @param string[] $designs SwitchableDesigns[]
     * @param string $default the siteaccess's SiteDesign
     * @param string $uri the request address
     * @param string|null $cookie the cookie's value
     * @return array( 'design' => <design to use, the default when none>, 'cookie' => null (leave it) | '' (clear it) | <design> )
     */
    public static function resolve( array $designs, $default, $uri, $cookie )
    {
        $fromUri = self::designFromUri( $uri );
        $valid = function ( $name ) use ( $designs, $default ) {
            return is_string( $name ) && $name !== '' && ( $name === $default || ( in_array( $name, $designs, true ) && self::designExists( $name ) ) );
        };
        if ( $fromUri !== '' && $valid( $fromUri ) )
            return array( 'design' => $fromUri, 'cookie' => $fromUri === $default ? '' : $fromUri );
        if ( $valid( $cookie ) )
            return array( 'design' => $cookie, 'cookie' => null );
        // a cookie naming a design no longer offered is cleared
        return array( 'design' => $default, 'cookie' => is_string( $cookie ) && $cookie !== '' ? '' : null );
    }

    // ------------------------------------------------------------------ request lifecycle

    /**
     * Start of a request: puts back the design settings the request before changed (persistent Velocity workers).
     */
    public static function beginRequest()
    {
        if ( self::$previous !== null )
        {
            $ini = eZINI::instance();
            $ini->setVariable( 'DesignSettings', 'SiteDesign', self::$previous['site'] );
            $ini->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', self::$previous['additional'] );
            self::resetDesignState();
        }
        self::$previous = null;
        self::$applied = null;
        self::$active = false;
    }

    /**
     * Applies the visitor's choice for this request. Never throws.
     *
     * @return string|null the design applied, null when the default stays
     */
    public static function applyRequest()
    {
        try
        {
            $ini = eZINI::instance();
            $designs = self::designs( $ini );
            if ( !$designs )
                return null;
            self::$active = true;
            $default = (string)$ini->variable( 'DesignSettings', 'SiteDesign' );
            $cookieName = self::cookieName( $ini );
            $cookie = isset( $_COOKIE[$cookieName] ) ? (string)$_COOKIE[$cookieName] : null;
            $uri = isset( $_SERVER['REQUEST_URI'] ) ? (string)$_SERVER['REQUEST_URI'] : '';
            $choice = self::resolve( $designs, $default, $uri, $cookie );
            if ( $choice['cookie'] !== null )
                self::sendCookie( $cookieName, $choice['cookie'] );
            if ( $choice['design'] === $default )
                return null;
            self::apply( $choice['design'], $ini );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
        return self::$applied;
    }

    /**
     * Sets a design for this request: SiteDesign, the fallback chain when one is configured, the cache variant key.
     *
     * @param string $design
     * @param eZINI $ini
     */
    public static function apply( $design, eZINI $ini )
    {
        self::$previous = array( 'site' => (string)$ini->variable( 'DesignSettings', 'SiteDesign' ),
                                 'additional' => $ini->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) ? (array)$ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) : array() );
        $chain = self::chain( $design, $ini );
        $ini->setVariable( 'DesignSettings', 'SiteDesign', $design );
        if ( $chain !== null )
            $ini->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', $chain );
        self::resetDesignState();
        self::$applied = $design;
        $key = isset( $GLOBALS['expUISwitchVariantKey'] ) ? (string)$GLOBALS['expUISwitchVariantKey'] : '';
        $GLOBALS['expUISwitchVariantKey'] = ( $key !== '' ? $key . '.' : '' ) . 'fd-' . $design;
    }

    /**
     * Stores or clears the choice in the cookie, for the whole installation path.
     *
     * @param string $name
     * @param string $value '' clears it
     */
    protected static function sendCookie( $name, $value )
    {
        if ( headers_sent() || PHP_SAPI === 'cli' )
            return;
        $path = class_exists( 'eZSys' ) ? rtrim( eZSys::wwwDir(), '/' ) . '/' : '/';
        $secure = class_exists( 'eZSys' ) && eZSys::isSSLNow();
        setcookie( $name, $value, array( 'expires' => $value === '' ? time() - 3600 : time() + self::COOKIE_LIFETIME,
                                         'path' => $path, 'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax' ) );
        $_COOKIE[$name] = $value;
    }

    /**
     * Forgets what was built from the design list (design names, design bases, the template override map).
     */
    protected static function resetDesignState()
    {
        if ( class_exists( 'eZTemplateDesignResource' ) )
            eZTemplateDesignResource::clearInMemoryCache();
        unset( $GLOBALS['eZTemplateDesignResourceBases'] );
    }

    // ------------------------------------------------------------------ for the response and the templates

    /**
     * @return bool whether the current siteaccess switches designs (its responses send Vary: Cookie)
     */
    public static function isActive()
    {
        return self::$active;
    }

    /**
     * @return string|null the design chosen for this request, null when it is the default
     */
    public static function appliedDesign()
    {
        return self::$applied;
    }

    /**
     * The switch for a template: empty when the siteaccess does not switch.
     *
     * @return array( 'current' => <design>, 'default' => <design>, 'parameter' => 'design',
     *                'designs' => array( array( 'name', 'label', 'current' => bool, 'default' => bool ) ) )
     */
    public static function switchData()
    {
        if ( !self::$active )
            return array();
        $ini = eZINI::instance();
        $default = self::$previous !== null ? self::$previous['site'] : (string)$ini->variable( 'DesignSettings', 'SiteDesign' );
        $current = self::$applied !== null ? self::$applied : $default;
        $list = array();
        $names = self::designs( $ini );
        if ( !in_array( $default, $names, true ) )
            array_unshift( $names, $default );
        foreach ( $names as $name )
        {
            if ( $name !== $default && !self::designExists( $name ) )
                continue;
            $list[] = array( 'name' => $name, 'label' => self::label( $name, $ini ), 'current' => $name === $current, 'default' => $name === $default );
        }
        return array( 'current' => $current, 'default' => $default, 'parameter' => self::VIEW_PARAMETER, 'designs' => $list );
    }

    /**
     * fetch( 'uiswitch', 'front_designs' )
     *
     * @return array
     */
    public static function fetchSwitch()
    {
        return array( 'result' => self::switchData() );
    }
}
