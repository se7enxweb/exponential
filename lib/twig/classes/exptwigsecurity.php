<?php
/**
 * The sandbox policy of Twig templates ([TwigSettings] Security=enabled, twig.ini): the tags, filters and functions
 * a template may use, and no method or property of an object unless allowed. Every bridged operator, template
 * function, block function and exp* tag is allowed: they run with the template system's own rules.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigSecurity implements \Twig\Sandbox\SecurityPolicyInterface
{
    private $tags;
    private $filters;
    private $functions;
    private $methods;
    private $properties;

    public function __construct( array $tags, array $filters, array $functions, array $methods, array $properties )
    {
        $this->tags = array_fill_keys( $tags, true );
        $this->filters = array_fill_keys( $filters, true );
        $this->functions = array_fill_keys( $functions, true );
        $this->methods = array_fill_keys( array_map( 'strtolower', $methods ), true );
        $this->properties = array_fill_keys( $properties, true );
    }

    private static function lists( eZINI $ini )
    {
        $lists = array();
        foreach ( array( 'SecurityAllowedTagList', 'SecurityAllowedFilterList', 'SecurityAllowedFunctionList', 'SecurityAllowedMethodList', 'SecurityAllowedPropertyList' ) as $name )
        {
            $value = $ini->hasVariable( 'TwigSettings', $name ) ? $ini->variable( 'TwigSettings', $name ) : array();
            $lists[$name] = is_array( $value ) ? array_values( array_filter( array_map( 'trim', $value ), 'strlen' ) ) : array();
        }
        return $lists;
    }

    public static function fromSettings( eZINI $ini )
    {
        $lists = self::lists( $ini );
        return new self( $lists['SecurityAllowedTagList'], $lists['SecurityAllowedFilterList'], $lists['SecurityAllowedFunctionList'],
                         $lists['SecurityAllowedMethodList'], $lists['SecurityAllowedPropertyList'] );
    }

    /**
     * @return string what the policy is built from, for the environment's identity
     */
    public static function settingsKey( eZINI $ini )
    {
        return md5( serialize( self::lists( $ini ) ) );
    }

    /**
     * @return bool whether no method and no property is allowed (expTwigAttributeVisitor relies on it)
     */
    public function allowsNoMethodOrProperty()
    {
        return !$this->methods && !$this->properties;
    }

    public function checkSecurity( $tags, $filters, $functions ): void
    {
        foreach ( $tags as $tag )
        {
            if ( !isset( $this->tags[$tag] ) && !isset( expTwigTagTokenParser::TAGS[$tag] ) && expTwigExtension::tag( $tag ) === false )
                throw new \Twig\Sandbox\SecurityNotAllowedTagError( sprintf( 'Tag "%s" is not allowed.', $tag ), $tag );
        }
        foreach ( $filters as $filter )
        {
            if ( !isset( $this->filters[$filter] ) && expTwigExtension::filter( $filter ) === false )
                throw new \Twig\Sandbox\SecurityNotAllowedFilterError( sprintf( 'Filter "%s" is not allowed.', $filter ), $filter );
        }
        foreach ( $functions as $function )
        {
            if ( !isset( $this->functions[$function] ) && !in_array( $function, expTwigExtension::HELPERS, true ) && expTwigExtension::twigFunction( $function ) === false )
                throw new \Twig\Sandbox\SecurityNotAllowedFunctionError( sprintf( 'Function "%s" is not allowed.', $function ), $function );
        }
    }

    public function checkMethodAllowed( $obj, $method ): void
    {
        // __toString() is allowed on every object: printing a value is what a template is for. expTwigToStringVisitor
        // relies on it and leaves out Twig's run-time check of it.
        if ( $obj instanceof \Twig\Template || $obj instanceof \Twig\Markup || strtolower( $method ) === '__tostring' )
            return;
        $method = strtolower( $method );
        foreach ( array_keys( $this->methods ) as $allowed )
        {
            list( $class, $name ) = array_pad( explode( '::', $allowed, 2 ), 2, '' );
            if ( $name === $method && $obj instanceof $class )
                return;
        }
        throw new \Twig\Sandbox\SecurityNotAllowedMethodError( sprintf( 'Calling "%s" method on a "%s" object is not allowed.', $method, get_class( $obj ) ), get_class( $obj ), $method );
    }

    public function checkPropertyAllowed( $obj, $property ): void
    {
        // node.name on a value of the template system reads attribute( 'name' ): its own rules apply, as in a .tpl
        // (Twig 3.12+ checks the keys of ArrayAccess objects as properties)
        if ( $obj instanceof expTemplateObjectProxy )
            return;
        foreach ( array_keys( $this->properties ) as $allowed )
        {
            list( $class, $name ) = array_pad( explode( '::', $allowed, 2 ), 2, '' );
            if ( $name === $property && $obj instanceof $class )
                return;
        }
        throw new \Twig\Sandbox\SecurityNotAllowedPropertyError( sprintf( 'Calling "%s" property on a "%s" object is not allowed.', $property, get_class( $obj ) ), get_class( $obj ), $property );
    }
}
