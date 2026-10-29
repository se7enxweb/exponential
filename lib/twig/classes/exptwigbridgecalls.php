<?php
/**
 * The calls a compiled Twig template makes into the template system, as static methods: a bridged operator with an
 * input (filter()), without one (call()), a template function (templateFunction()), and the helper functions the
 * converter writes. A compiled template calls them directly (expTwigBridgeFilterNode, expTwigBridgeFunctionNode, and
 * static callables for the helpers), so rendering never looks a filter or function up in the environment: looking one
 * up makes Twig set up all of its extensions first (some 8 ms on every PHP-FPM request), which a template that was
 * compiled before never needs.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigBridgeCalls
{
    /**
     * input|operator( parameters ): as the operator takes its input (expTemplateBridge::callOperatorWithInput()).
     *
     * @param string $operator
     * @param mixed $input
     * @param array $parameters
     * @return mixed
     */
    public static function filter( $operator, $input, array $parameters )
    {
        if ( !expTwigRenderer::profiling() )
            return expTemplateBridge::callOperatorWithInput( $operator, $input, $parameters, expTwigRenderer::PROXY );
        expTwigRenderer::profileStart( 'operator', 'operators (filters)' );
        expTwigRenderer::profileStart( 'op:' . $operator, '  |' . $operator );
        try
        {
            return expTemplateBridge::callOperatorWithInput( $operator, $input, $parameters, expTwigRenderer::PROXY );
        }
        finally
        {
            expTwigRenderer::profileStop( 'op:' . $operator );
            expTwigRenderer::profileStop( 'operator' );
        }
    }

    /**
     * operator( parameters ): an operator without an input.
     *
     * @param string $operator
     * @param array $parameters
     * @return mixed
     */
    public static function call( $operator, array $parameters )
    {
        if ( !expTwigRenderer::profiling() )
            return expTemplateBridge::callOperatorDirect( $operator, false, null, $parameters, expTwigRenderer::PROXY );
        expTwigRenderer::profileStart( 'operatorfn', 'operators (functions)' );
        $detail = 'fn:' . $operator . ( $operator === 'fetch' && isset( $parameters[0], $parameters[1] ) && is_string( $parameters[0] ) && is_string( $parameters[1] )
                                         ? ':' . $parameters[0] . '/' . $parameters[1] : '' );
        expTwigRenderer::profileStart( $detail, '  ' . substr( $detail, 3 ) . '()' );
        try
        {
            return expTemplateBridge::callOperatorDirect( $operator, false, null, $parameters, expTwigRenderer::PROXY );
        }
        finally
        {
            expTwigRenderer::profileStop( $detail );
            expTwigRenderer::profileStop( 'operatorfn' );
        }
    }

    /**
     * A template function (attribute_view_gui, node_view_gui ...) with its hash of named parameters.
     *
     * @param string $name
     * @param mixed $parameters
     * @return string
     */
    public static function templateFunction( $name, $parameters = array() )
    {
        if ( !expTwigRenderer::profiling() )
            return expTemplateBridge::callFunction( $name, is_array( $parameters ) ? $parameters : array(), 'twig' );
        expTwigRenderer::profileStart( 'function', 'template functions (*_view_gui ...)' );
        try
        {
            return expTemplateBridge::callFunction( $name, is_array( $parameters ) ? $parameters : array(), 'twig' );
        }
        finally
        {
            expTwigRenderer::profileStop( 'function' );
        }
    }

    /** expvar( name, namespace ) */
    public static function globalVariable( $name, $namespace = '' )
    {
        return expTemplateBridge::globalVariable( $name, $namespace, expTwigRenderer::PROXY );
    }

    /** expattr( value, key ) */
    public static function attributeOf( $value, $key )
    {
        return expTemplateBridge::attributeOf( $value, $key, expTwigRenderer::PROXY );
    }

    /** expcolumn( list, key ) */
    public static function column( $array, $key )
    {
        return expTemplateBridge::column( $array, $key, expTwigRenderer::PROXY );
    }

    /** expsectionloop( loop, offset, max, reverse[, iterators[, sequence]] ) */
    public static function sectionLoop( $loop, $offset = 0, $max = null, $reverse = false, $iterators = false, $sequence = null )
    {
        return expTemplateBridge::sectionLoop( $loop, $offset, $max, $reverse, $iterators, $sequence, expTwigRenderer::PROXY );
    }
}
