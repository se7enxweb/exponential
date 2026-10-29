<?php
/**
 * The native operators of expTwigNativeOperators as static methods, one for the filter form (filter_<name>) and one for
 * the function form (function_<name>) of each: a compiled Twig template calls a static callable directly, without
 * looking up the filter and calling a closure on every call. eq, ne, lt, gt, le, ge, wash and cond have a fast path for
 * the plain values most templates pass (numbers, texts), with exactly the result expTwigNativeOperators gives them; the
 * fast path also requires that the kernel class still holds the name. Anything else takes the way of before: the native
 * operator, or the template system (expTemplateBridge).
 *
 * Written by generate_twig_fast_operators_class_from_native_operator_list.py from expTwigNativeOperators::OPERATORS.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigFastOperators
{
    /** input|name( parameters ): the native operator, or the template system. */
    public static function filter( $name, $input, array $parameters )
    {
        $result = expTwigNativeOperators::filter( $name, $input, $parameters );
        if ( $result !== expTwigNativeOperators::NOT_HANDLED )
            return is_object( $result ) || is_array( $result ) ? expTemplateValues::wrap( $result, expTwigRenderer::PROXY ) : $result;
        return expTemplateBridge::callOperatorWithInput( $name, $input, $parameters, expTwigRenderer::PROXY );
    }

    /** name( parameters ): the native operator, or the template system. */
    public static function call( $name, array $parameters )
    {
        $result = expTwigNativeOperators::call( $name, $parameters );
        if ( $result !== expTwigNativeOperators::NOT_HANDLED )
            return is_object( $result ) || is_array( $result ) ? expTemplateValues::wrap( $result, expTwigRenderer::PROXY ) : $result;
        return expTemplateBridge::callOperatorDirect( $name, false, null, $parameters, expTwigRenderer::PROXY );
    }

    public static function filter_eq( $input, ...$parameters )
    {
        if ( !is_object( $input ) && !is_array( $input ) && isset( $parameters[0] ) && !isset( $parameters[1] ) && !is_object( $parameters[0] ) && !is_array( $parameters[0] ) && expTwigNativeOperators::held( 'eq' ) )
            return $input == $parameters[0];
        return self::filter( 'eq', $input, $parameters );
    }

    public static function function_eq( ...$parameters )
    {
        return self::call( 'eq', $parameters );
    }

    public static function filter_ne( $input, ...$parameters )
    {
        if ( !is_object( $input ) && !is_array( $input ) && isset( $parameters[0] ) && !isset( $parameters[1] ) && !is_object( $parameters[0] ) && !is_array( $parameters[0] ) && expTwigNativeOperators::held( 'ne' ) )
            return $input != $parameters[0];
        return self::filter( 'ne', $input, $parameters );
    }

    public static function function_ne( ...$parameters )
    {
        return self::call( 'ne', $parameters );
    }

    public static function filter_lt( $input, ...$parameters )
    {
        if ( ( is_int( $input ) || is_float( $input ) ) && isset( $parameters[0] ) && !isset( $parameters[1] ) && ( is_int( $parameters[0] ) || is_float( $parameters[0] ) ) && expTwigNativeOperators::held( 'lt' ) )
            return $input < $parameters[0];
        return self::filter( 'lt', $input, $parameters );
    }

    public static function function_lt( ...$parameters )
    {
        return self::call( 'lt', $parameters );
    }

    public static function filter_gt( $input, ...$parameters )
    {
        if ( ( is_int( $input ) || is_float( $input ) ) && isset( $parameters[0] ) && !isset( $parameters[1] ) && ( is_int( $parameters[0] ) || is_float( $parameters[0] ) ) && expTwigNativeOperators::held( 'gt' ) )
            return $input > $parameters[0];
        return self::filter( 'gt', $input, $parameters );
    }

    public static function function_gt( ...$parameters )
    {
        return self::call( 'gt', $parameters );
    }

    public static function filter_le( $input, ...$parameters )
    {
        if ( ( is_int( $input ) || is_float( $input ) ) && isset( $parameters[0] ) && !isset( $parameters[1] ) && ( is_int( $parameters[0] ) || is_float( $parameters[0] ) ) && expTwigNativeOperators::held( 'le' ) )
            return $input <= $parameters[0];
        return self::filter( 'le', $input, $parameters );
    }

    public static function function_le( ...$parameters )
    {
        return self::call( 'le', $parameters );
    }

    public static function filter_ge( $input, ...$parameters )
    {
        if ( ( is_int( $input ) || is_float( $input ) ) && isset( $parameters[0] ) && !isset( $parameters[1] ) && ( is_int( $parameters[0] ) || is_float( $parameters[0] ) ) && expTwigNativeOperators::held( 'ge' ) )
            return $input >= $parameters[0];
        return self::filter( 'ge', $input, $parameters );
    }

    public static function function_ge( ...$parameters )
    {
        return self::call( 'ge', $parameters );
    }

    public static function filter_and( $input, ...$parameters )
    {
        return self::filter( 'and', $input, $parameters );
    }

    public static function function_and( ...$parameters )
    {
        return self::call( 'and', $parameters );
    }

    public static function filter_or( $input, ...$parameters )
    {
        return self::filter( 'or', $input, $parameters );
    }

    public static function function_or( ...$parameters )
    {
        return self::call( 'or', $parameters );
    }

    public static function filter_not( $input, ...$parameters )
    {
        return self::filter( 'not', $input, $parameters );
    }

    public static function function_not( ...$parameters )
    {
        return self::call( 'not', $parameters );
    }

    public static function filter_count( $input, ...$parameters )
    {
        return self::filter( 'count', $input, $parameters );
    }

    public static function function_count( ...$parameters )
    {
        return self::call( 'count', $parameters );
    }

    public static function filter_sum( $input, ...$parameters )
    {
        return self::filter( 'sum', $input, $parameters );
    }

    public static function function_sum( ...$parameters )
    {
        return self::call( 'sum', $parameters );
    }

    public static function filter_sub( $input, ...$parameters )
    {
        return self::filter( 'sub', $input, $parameters );
    }

    public static function function_sub( ...$parameters )
    {
        return self::call( 'sub', $parameters );
    }

    public static function filter_mul( $input, ...$parameters )
    {
        return self::filter( 'mul', $input, $parameters );
    }

    public static function function_mul( ...$parameters )
    {
        return self::call( 'mul', $parameters );
    }

    public static function filter_div( $input, ...$parameters )
    {
        return self::filter( 'div', $input, $parameters );
    }

    public static function function_div( ...$parameters )
    {
        return self::call( 'div', $parameters );
    }

    public static function filter_mod( $input, ...$parameters )
    {
        return self::filter( 'mod', $input, $parameters );
    }

    public static function function_mod( ...$parameters )
    {
        return self::call( 'mod', $parameters );
    }

    public static function filter_inc( $input, ...$parameters )
    {
        return self::filter( 'inc', $input, $parameters );
    }

    public static function function_inc( ...$parameters )
    {
        return self::call( 'inc', $parameters );
    }

    public static function filter_dec( $input, ...$parameters )
    {
        return self::filter( 'dec', $input, $parameters );
    }

    public static function function_dec( ...$parameters )
    {
        return self::call( 'dec', $parameters );
    }

    public static function filter_cond( $input, ...$parameters )
    {
        return self::filter( 'cond', $input, $parameters );
    }

    public static function function_cond( ...$parameters )
    {
        if ( count( $parameters ) === 3 && ( $parameters[0] === null || is_scalar( $parameters[0] ) ) && ( $parameters[1] === null || is_scalar( $parameters[1] ) ) &&
             ( $parameters[2] === null || is_scalar( $parameters[2] ) ) && expTwigNativeOperators::held( 'cond' ) )
            return $parameters[0] ? $parameters[1] : ( $parameters[2] ? $parameters[2] : null );
        return self::call( 'cond', $parameters );
    }

    public static function filter_first_set( $input, ...$parameters )
    {
        return self::filter( 'first_set', $input, $parameters );
    }

    public static function function_first_set( ...$parameters )
    {
        return self::call( 'first_set', $parameters );
    }

    public static function filter_wash( $input, ...$parameters )
    {
        if ( ( $input === null || is_scalar( $input ) ) && ( !$parameters || ( $parameters === array( 'xhtml' ) ) ) && expTwigNativeOperators::held( 'wash' ) )
            return htmlspecialchars( (string)$input, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );
        return self::filter( 'wash', $input, $parameters );
    }

    public static function function_wash( ...$parameters )
    {
        return self::call( 'wash', $parameters );
    }

    public static function filter_implode( $input, ...$parameters )
    {
        return self::filter( 'implode', $input, $parameters );
    }

    public static function function_implode( ...$parameters )
    {
        return self::call( 'implode', $parameters );
    }

    public static function filter_contains( $input, ...$parameters )
    {
        return self::filter( 'contains', $input, $parameters );
    }

    public static function function_contains( ...$parameters )
    {
        return self::call( 'contains', $parameters );
    }

    public static function filter_concat( $input, ...$parameters )
    {
        return self::filter( 'concat', $input, $parameters );
    }

    public static function function_concat( ...$parameters )
    {
        return self::call( 'concat', $parameters );
    }
}
