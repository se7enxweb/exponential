<?php
/**
 * File containing the expUISwitchProvider class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The common part of a UI switch provider: its settings group uiswitch.ini [UISwitch_<identifier>], the allow-list
 * from AllowedValues[] (each value checked by the provider's isAvailable()), strict validation and the cache variant
 * key. A provider extends it with defaultValue(), apply() and, when apply() touches statics, reset().
 *
 * One instance lives for one request: the registry builds the providers again at the start of every request.
 * Guide: doc/bc/6.0/ui-switch.md
 */
abstract class expUISwitchProvider implements expUISwitchProviderInterface
{
    /** what a configured value may look like before the provider looks at it at all */
    const VALUE_PATTERN = '/^[A-Za-z0-9][A-Za-z0-9_.@-]{0,63}$/';

    /** @var string */
    protected $identifier;

    /** @var array the settings group, name => value */
    protected $settings;

    /** @var string|null the value applied in this request */
    protected $current = null;

    /** @var string[]|null */
    protected $allowed = null;

    /**
     * @param string $identifier
     * @param array $settings uiswitch.ini [UISwitch_<identifier>], name => value
     */
    public function __construct( $identifier, array $settings = array() )
    {
        $this->identifier = (string)$identifier;
        $this->settings = $settings;
    }

    public function identifier()
    {
        return $this->identifier;
    }

    /**
     * One setting of the provider's group.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function setting( $name, $default = null )
    {
        return array_key_exists( $name, $this->settings ) ? $this->settings[$name] : $default;
    }

    /**
     * Whether the user's choice is also kept in their preferences (RememberPreference=enabled), so it comes back
     * at the next sign-in. Off by default: the choice ends with the session.
     *
     * @return bool
     */
    public function remembersPreference()
    {
        return $this->setting( 'RememberPreference', 'disabled' ) === 'enabled';
    }

    /**
     * AllowedValues[] as configured, the well-formed ones only, each once.
     *
     * @return string[]
     */
    public function configuredValues()
    {
        $list = array();
        foreach ( (array)$this->setting( 'AllowedValues', array() ) as $value )
        {
            if ( is_string( $value ) && preg_match( self::VALUE_PATTERN, $value ) && !in_array( $value, $list, true ) )
                $list[] = $value;
        }
        return $list;
    }

    /**
     * Whether a well-formed configured value can be used on this installation (its files exist, ...).
     *
     * @param string $value
     * @return bool
     */
    protected function isAvailable( $value )
    {
        return true;
    }

    public function allowedValues()
    {
        if ( $this->allowed === null )
        {
            $default = (string)$this->defaultValue();
            $list = $default !== '' ? array( $default ) : array();
            foreach ( $this->configuredValues() as $value )
            {
                if ( !in_array( $value, $list, true ) && $this->isAvailable( $value ) )
                    $list[] = $value;
            }
            $this->allowed = $list;
        }
        return $this->allowed;
    }

    public function isValid( $value )
    {
        return is_string( $value ) && $value !== '' && in_array( $value, $this->allowedValues(), true );
    }

    public function label( $value )
    {
        return (string)$value;
    }

    /**
     * The name of the choice in the toolbar: [UISwitch_<identifier>] Title, translated in the context
     * kernel/uiswitch, or the identifier.
     *
     * @return string
     */
    public function title()
    {
        $title = (string)$this->setting( 'Title', '' );
        if ( $title === '' )
            return ucfirst( str_replace( '_', ' ', $this->identifier ) );
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/uiswitch', $title ) : $title;
    }

    /**
     * The language a value's label is written in, as an HTML lang attribute, or '' (the page's language).
     *
     * @param string $value
     * @return string
     */
    public function labelLanguage( $value )
    {
        return '';
    }

    public function currentValue()
    {
        return $this->current !== null ? $this->current : (string)$this->defaultValue();
    }

    public function cacheVariantKey( $value )
    {
        if ( !is_string( $value ) || $value === (string)$this->defaultValue() || !$this->isValid( $value ) )
            return '';
        return $this->identifier . '-' . preg_replace( '/[^A-Za-z0-9_.-]/', '_', $value );
    }

    public function reset()
    {
        $this->current = null;
    }
}
