<?php
/**
 * File containing the expMailCategory class.
 *
 * One category of e-mail (content notifications, newsletters, account security, ...): a value object. Categories
 * come from mailpreferences.ini [Category_<identifier>] (source 'ini' for the kernel's own, 'extension' for those an
 * extension adds) and from the category manager of the admin (source 'admin', table expmail_category); the registry
 * (expMailCategoryRegistry) merges them.
 *
 * Optional categories are switched on by the person (default off: opt-in). Essential categories are never
 * switchable and always sent.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailCategory
{
    const FREQUENCIES = array( 'immediate', 'daily', 'weekly' );

    /** @var string */
    public $identifier;
    /** @var string */
    public $name;
    /** @var string */
    public $description;
    /** @var bool */
    public $essential;
    /** @var bool always false for an essential category */
    public $defaultOn;
    /** @var string[] immediate|daily|weekly, may be empty */
    public $frequencies;
    /** @var bool */
    public $doubleOptIn;
    /** @var string ini|admin|extension */
    public $source;
    /** @var string a class implementing expMailCategoryHandler, or '' */
    public $handlerClass;

    /**
     * @param string $identifier lower case letters, digits and '_'
     * @param array $values name, description, essential, defaultOn, frequencies, doubleOptIn, source, handlerClass
     */
    public function __construct( $identifier, array $values = array() )
    {
        $this->identifier = self::cleanIdentifier( $identifier );
        $this->name = isset( $values['name'] ) && (string)$values['name'] !== '' ? (string)$values['name'] : $this->identifier;
        $this->description = isset( $values['description'] ) ? (string)$values['description'] : '';
        $this->essential = !empty( $values['essential'] ) && self::bool( $values['essential'] );
        $this->defaultOn = !$this->essential && !empty( $values['defaultOn'] ) && self::bool( $values['defaultOn'] );
        $frequencies = isset( $values['frequencies'] ) ? $values['frequencies'] : array();
        if ( is_string( $frequencies ) )
            $frequencies = explode( ',', $frequencies );
        $this->frequencies = array_values( array_intersect( self::FREQUENCIES, array_map( 'trim', (array)$frequencies ) ) );
        $this->doubleOptIn = !$this->essential && !empty( $values['doubleOptIn'] ) && self::bool( $values['doubleOptIn'] );
        $this->source = isset( $values['source'] ) && in_array( $values['source'], array( 'ini', 'admin', 'extension' ), true ) ? $values['source'] : 'ini';
        $this->handlerClass = isset( $values['handlerClass'] ) ? trim( (string)$values['handlerClass'] ) : '';
    }

    /**
     * @param string $identifier
     * @return string the identifier as stored: lower case letters, digits and '_', at most 100 characters
     */
    public static function cleanIdentifier( $identifier )
    {
        return substr( preg_replace( '/[^a-z0-9_]/', '', strtolower( trim( (string)$identifier ) ) ), 0, 100 );
    }

    /** @return bool the identifier can be used */
    public static function validIdentifier( $identifier )
    {
        return is_string( $identifier ) && preg_match( '/^[a-z][a-z0-9_]{0,99}$/', $identifier ) === 1;
    }

    protected static function bool( $value )
    {
        if ( is_bool( $value ) )
            return $value;
        return in_array( strtolower( trim( (string)$value ) ), array( '1', 'true', 'enabled', 'yes', 'on' ), true );
    }

    /** @return bool */
    public function isOptional()
    {
        return !$this->essential;
    }

    /** @return expMailCategoryHandler|null the handler object, when the class exists and implements the interface */
    public function handler()
    {
        if ( $this->handlerClass === '' || !class_exists( $this->handlerClass ) )
            return null;
        $handler = new $this->handlerClass();
        return $handler instanceof expMailCategoryHandler ? $handler : null;
    }

    /** @return array for templates and JSON */
    public function toArray()
    {
        return array( 'identifier' => $this->identifier, 'name' => $this->name, 'description' => $this->description,
                      'essential' => $this->essential, 'default_on' => $this->defaultOn, 'frequencies' => $this->frequencies,
                      'double_opt_in' => $this->doubleOptIn, 'source' => $this->source, 'handler_class' => $this->handlerClass );
    }

    // template access (eZ attribute style)
    public function attributes()
    {
        return array_keys( $this->toArray() );
    }

    public function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes(), true );
    }

    public function attribute( $name )
    {
        $a = $this->toArray();
        return isset( $a[$name] ) ? $a[$name] : null;
    }
}
