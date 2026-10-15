<?php
/**
 * File containing the expUISwitchDesignProvider class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The design of the administration interface (the "UI chrome"): admin4l, admin4, the classic admin design, AdminUI
 * or another installed administration design, for the signed-in user who chose it. It sets site.ini [DesignSettings]
 * SiteDesign and AdditionalSiteDesignList[] in memory, for this request only.
 *
 * The allow-list is uiswitch.ini [UISwitch_design] AllowedValues[]: a design is offered only when its name is a design
 * name, it has a fallback chain DesignChain[<design>] (empty for a design that needs none) and the design exists, in
 * design/ or in the design directory of an active design extension. The siteaccess's own SiteDesign is always offered
 * and choosing it clears the switch.
 *
 * Every cache built from the design list gets the request's cache variant key: the design base cache (here), the
 * compiled templates, {cache-block} and the content view cache (expUISwitch); the template override cache is already
 * keyed by the design list. With the siteaccess's own design nothing changes, not even a cache file name.
 * Guide: doc/guides/ui-switch-settings.md
 */
class expUISwitchDesignProvider extends expUISwitchProvider
{
    /** a design directory name */
    const DESIGN_PATTERN = '/^[a-z][a-z0-9_]{0,63}$/';

    /** @var string|null the siteaccess's SiteDesign, read before any apply() */
    protected $siteDefault = null;

    /** @var string[]|null design extension names; from design.ini when null */
    protected $designExtensions = null;

    /** @var string the installation root the design paths are relative to; '' is the working directory */
    protected $root = '';

    /**
     * @param string $identifier
     * @param array $settings uiswitch.ini [UISwitch_<identifier>]
     * @param string|null $siteDefault the siteaccess's SiteDesign; read from site.ini when null
     * @param string $root
     * @param string[]|null $designExtensions extension directory names whose design/ counts; design.ini when null
     */
    public function __construct( $identifier, array $settings = array(), $siteDefault = null, $root = '', ?array $designExtensions = null )
    {
        parent::__construct( $identifier, $settings );
        $this->siteDefault = $siteDefault;
        $this->root = (string)$root;
        $this->designExtensions = $designExtensions;
    }

    public function defaultValue()
    {
        if ( $this->siteDefault === null )
            $this->siteDefault = (string)eZINI::instance()->variable( 'DesignSettings', 'SiteDesign' );
        return $this->siteDefault;
    }

    /**
     * The fallback chain configured for a design (DesignChain[<design>], designs separated by ";"), without the design
     * itself and without names that are not design names; null when the design has no chain configured.
     *
     * @param string $design
     * @return string[]|null
     */
    public function chain( $design )
    {
        $chains = $this->setting( 'DesignChain', array() );
        if ( !is_array( $chains ) || !is_string( $design ) || !array_key_exists( $design, $chains ) )
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

    protected function isAvailable( $value )
    {
        return preg_match( self::DESIGN_PATTERN, $value ) && $this->chain( $value ) !== null && $this->designExists( $value );
    }

    /**
     * Whether a design directory exists: design/<design>, or design/<design> of an active design extension.
     *
     * @param string $design a design name (DESIGN_PATTERN)
     * @return bool
     */
    protected function designExists( $design )
    {
        $prefix = $this->root !== '' ? rtrim( $this->root, '/' ) . '/' : '';
        if ( is_dir( $prefix . 'design/' . $design ) )
            return true;
        foreach ( $this->designExtensionPaths() as $path )
        {
            if ( is_dir( $prefix . $path . '/design/' . $design ) )
                return true;
        }
        return false;
    }

    /**
     * @return string[] the directories of the active design extensions, relative to the root
     */
    protected function designExtensionPaths()
    {
        $paths = array();
        if ( $this->designExtensions !== null )
        {
            foreach ( $this->designExtensions as $name )
            {
                if ( is_string( $name ) && preg_match( '/^[A-Za-z0-9_.-]+$/', $name ) )
                    $paths[] = 'extension/' . $name;
            }
            return $paths;
        }
        foreach ( (array)eZINI::instance( 'design.ini' )->variable( 'ExtensionSettings', 'DesignExtensions' ) as $name )
        {
            $path = eZExtension::extensionPath( eZExtension::extensionName( $name ) );
            if ( $path !== false && $path !== null )
                $paths[] = $path;
        }
        return $paths;
    }

    /**
     * DesignLabel[<design>], translated in the context kernel/uiswitch; the design name without one.
     */
    public function label( $value )
    {
        $labels = $this->setting( 'DesignLabel', array() );
        $label = is_array( $labels ) && is_string( $value ) && isset( $labels[$value] ) ? trim( (string)$labels[$value] ) : '';
        if ( $label === '' )
            return (string)$value;
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/uiswitch', $label ) : $label;
    }

    public function title()
    {
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/uiswitch', 'Administration design' ) : 'Administration design';
    }

    public function apply( $value )
    {
        if ( !$this->isValid( $value ) || $value === $this->defaultValue() )
            return false;
        $chain = $this->chain( $value );
        if ( $chain === null )
            return false;
        $ini = eZINI::instance();
        $ini->setVariable( 'DesignSettings', 'SiteDesign', $value );
        $ini->setVariable( 'DesignSettings', 'AdditionalSiteDesignList', $chain );
        self::resetDesignState();
        $this->current = $value;
        return true;
    }

    public function reset()
    {
        self::resetDesignState();
        parent::reset();
    }

    /**
     * Forgets what was built from the design list of the request before: the design names, the design bases of the
     * current siteaccess and the template override map (a static, which a persistent Velocity worker would otherwise
     * keep: see the compiled admin pagelayout of 2026-09-24 that included a public header).
     */
    protected static function resetDesignState()
    {
        if ( class_exists( 'eZTemplateDesignResource' ) )
            eZTemplateDesignResource::clearInMemoryCache();
        unset( $GLOBALS['eZTemplateDesignResourceBases'] );
    }
}
