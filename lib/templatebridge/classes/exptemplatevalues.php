<?php
/**
 * Values between the Exponential template system and another template engine: objects with attribute() become
 * array-accessible proxies (wrap), and back (unwrap); the variables of a namespace an engine template sees, and
 * the write-back of what it defined, changed or set to null.
 *
 * The last conversion of each variable is remembered for the page (wrapNamed/unwrapNamed): the next engine
 * template of the page that sees the same $node, $module_result ... gets the converted value with one identity
 * comparison instead of a walk through it. expTemplateBridge::leave() forgets them when the page's outermost engine
 * template ends, so a persistent worker keeps none.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTemplateValues
{
    /** @var array proxy class => variable name => array( value as the template system has it, value as the engine has it ) */
    private static $pairs = array();

    /** Forgets the remembered conversions (end of a page's renders). */
    public static function reset()
    {
        self::$pairs = array();
    }

    /**
     * @param mixed $name
     * @return bool whether $name can be a variable of an engine template (Smarty and Twig alike)
     */
    public static function isVariableName( $name )
    {
        return is_string( $name ) && $name !== 'smarty' && $name !== '_self' && $name !== '_context' && $name !== '_charset' &&
               preg_match( '/^[a-z_][a-z0-9_]*$/i', $name );
    }

    /**
     * @param mixed $name
     * @return bool whether a variable of an engine template is written back to the template system: a variable
     *              name that is not a helper variable of a converted template (__name)
     */
    public static function isWriteBackName( $name )
    {
        return self::isVariableName( $name ) && strncmp( $name, '__', 2 ) !== 0;
    }

    /**
     * The template variables visible to an engine template: those of its root namespace, then those of its current
     * namespace. Names an engine cannot use as a variable are left out.
     *
     * @return array
     */
    public static function templateVariables( eZTemplate $tpl, $rootNamespace, $currentNamespace )
    {
        $variables = array();
        foreach ( array_unique( array( (string)$rootNamespace, (string)$currentNamespace ) ) as $namespace )
        {
            if ( isset( $tpl->Variables[$namespace] ) && is_array( $tpl->Variables[$namespace] ) )
            {
                foreach ( $tpl->Variables[$namespace] as $name => $value )
                {
                    if ( self::isVariableName( $name ) )
                        $variables[$name] = $value;
                }
            }
        }
        return $variables;
    }

    /**
     * Writes the variables an engine template defined, changed or set to null back to the template system's
     * namespace. Helper variables of converted templates (__name) stay in the engine.
     *
     * @param array $variables the template's variables when it ended
     * @param eZTemplate $tpl
     * @param string $namespace
     * @param array $input the variables the template was given (templateVariables()): unchanged ones are skipped
     * @param string $proxyClass
     */
    public static function writeBack( array $variables, eZTemplate $tpl, $namespace, array $input, $proxyClass = 'expTemplateObjectProxy' )
    {
        foreach ( $variables as $name => $value )
        {
            if ( !self::isWriteBackName( $name ) )
                continue;
            $value = self::unwrapNamed( $name, $value, $proxyClass );
            if ( array_key_exists( $name, $input ) && $input[$name] === $value )
                continue;
            if ( $value === null )
            {
                if ( $tpl->hasVariable( $name, $namespace ) )
                    $tpl->unsetVariable( $name, $namespace );
            }
            else
                $tpl->setVariable( $name, $value, $namespace );
        }
    }

    /**
     * Puts engine variables into a namespace of the template system (before an include that shares it).
     *
     * @param array $variables
     */
    public static function export( array $variables, eZTemplate $tpl, $namespace, $proxyClass = 'expTemplateObjectProxy' )
    {
        foreach ( $variables as $name => $value )
        {
            if ( !self::isWriteBackName( $name ) )
                continue;
            $value = self::unwrapNamed( $name, $value, $proxyClass );
            if ( $value === null )
            {
                if ( $tpl->hasVariable( $name, $namespace ) )
                    $tpl->unsetVariable( $name, $namespace );
            }
            else
                $tpl->setVariable( $name, $value, $namespace );
        }
    }

    /**
     * Wraps template values for an engine: objects with attribute() become array-accessible proxies of
     * $proxyClass, arrays are wrapped element by element.
     *
     * @param mixed $value
     * @param string $proxyClass expTemplateObjectProxy or a subclass (expSmartyObjectProxy, expTwigObjectProxy)
     * @return mixed
     */
    public static function wrap( $value, $proxyClass = 'expTemplateObjectProxy' )
    {
        if ( is_array( $value ) )
        {
            foreach ( $value as $key => $item )
            {
                if ( is_object( $item ) || is_array( $item ) )
                    $value[$key] = self::wrap( $item, $proxyClass );
            }
            return $value;
        }
        if ( is_object( $value ) && !( $value instanceof expTemplateObjectProxy ) &&
             method_exists( $value, 'attribute' ) && method_exists( $value, 'hasAttribute' ) )
        {
            return new $proxyClass( $value );
        }
        return $value;
    }

    /**
     * The reverse of wrap(), for values handed back to Exponential.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function unwrap( $value )
    {
        if ( $value instanceof expTemplateObjectProxy )
        {
            $value = $value->expObject();
            // the iterator of {section var=}: read as a value, it is its item (as the template system reads $var)
            return $value instanceof eZTemplateSectionIterator ? $value->templateValue() : $value;
        }
        // a Twig capture ({% set x %}...{% endset %}) is a Markup object: the template system gets its text
        if ( $value instanceof \Twig\Markup )
            return (string)$value;
        if ( is_array( $value ) )
        {
            foreach ( $value as $key => $item )
            {
                if ( is_object( $item ) || is_array( $item ) )
                    $value[$key] = self::unwrap( $item );
            }
        }
        return $value;
    }

    /**
     * wrap() of the variable $name, remembered for the page: the same value again (identical, which for the same
     * object or the same array is one pointer comparison) gives the same engine value without walking it.
     *
     * @param string $name
     * @param mixed $value as the template system has it
     * @param string $proxyClass
     * @return mixed
     */
    public static function wrapNamed( $name, $value, $proxyClass = 'expTemplateObjectProxy' )
    {
        if ( isset( self::$pairs[$proxyClass][$name] ) && self::$pairs[$proxyClass][$name][0] === $value )
            return self::$pairs[$proxyClass][$name][1];
        if ( !is_array( $value ) && !is_object( $value ) )
            return $value;
        $wrapped = self::wrap( $value, $proxyClass );
        self::$pairs[$proxyClass][$name] = array( $value, $wrapped );
        return $wrapped;
    }

    /**
     * unwrap() of the variable $name, with the same memory as wrapNamed().
     *
     * @param string $name
     * @param mixed $value as the engine has it
     * @param string $proxyClass
     * @return mixed
     */
    public static function unwrapNamed( $name, $value, $proxyClass = 'expTemplateObjectProxy' )
    {
        if ( isset( self::$pairs[$proxyClass][$name] ) && self::$pairs[$proxyClass][$name][1] === $value )
            return self::$pairs[$proxyClass][$name][0];
        if ( !is_array( $value ) && !is_object( $value ) )
            return $value;
        $raw = self::unwrap( $value );
        self::$pairs[$proxyClass][$name] = array( $raw, $value );
        return $raw;
    }
}
