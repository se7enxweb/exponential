<?php
/**
 * File containing the ezpRequestContext class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * What is known about one request when the request rules decide on it: a set
 * of named facts. Conditions and actions read facts and nothing else, so a rule
 * can be decided -- and tested -- without a database, a session or a kernel.
 *
 * A fact is either a value or a resolver (a callable that receives the context
 * and returns the value). A resolver runs the first time its fact is read and
 * its answer is kept, so a fact nobody asks for costs nothing: a rule about the
 * URI never loads the node.
 *
 * The kernel fills in the facts listed in doc/bc/6.0/view_full_security.md
 * (ezpRequestRuleKernel::context()); an extension adds its own with an
 * ezpRequestFactProvider, or define() at any point before the rules run.
 *
 * @see ezpRequestRuleEngine
 */
class ezpRequestContext
{
    /**
     * @var array fact name => value, for facts that are known or resolved
     */
    protected $values = array();

    /**
     * @var array fact name => callable, for facts not yet resolved
     */
    protected $resolvers = array();

    /**
     * @param array $facts fact name => value, or => callable( ezpRequestContext ) to resolve it lazily
     */
    public function __construct( array $facts = array() )
    {
        foreach ( $facts as $name => $value )
            $this->define( $name, $value );
    }

    /**
     * Sets a fact, replacing any earlier value or resolver of the same name.
     *
     * A Closure (or any object with __invoke) is a resolver. Other values,
     * strings included, are stored as they are: a string never names a
     * function, so a fact can safely hold any text a visitor sent.
     *
     * @param string $name
     * @param mixed $value value, or callable( ezpRequestContext $context ) returning it
     * @return ezpRequestContext $this
     */
    public function define( $name, $value )
    {
        $name = (string)$name;
        unset( $this->values[$name], $this->resolvers[$name] );
        if ( is_object( $value ) && is_callable( $value ) )
            $this->resolvers[$name] = $value;
        else
            $this->values[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool whether the fact is known or can be resolved
     */
    public function has( $name )
    {
        return array_key_exists( $name, $this->values ) || isset( $this->resolvers[$name] );
    }

    /**
     * Returns a fact, resolving it on first use.
     *
     * A resolver that throws gives $default and is not asked again; the error
     * is written to the debug output, so one broken fact never breaks a page.
     *
     * @param string $name
     * @param mixed $default when the fact is unknown
     * @return mixed
     */
    public function get( $name, $default = null )
    {
        if ( array_key_exists( $name, $this->values ) )
            return $this->values[$name];
        if ( !isset( $this->resolvers[$name] ) )
            return $default;

        $resolver = $this->resolvers[$name];
        unset( $this->resolvers[$name] );
        try
        {
            $this->values[$name] = $resolver( $this );
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug', false ) )
                eZDebug::writeError( "Request fact '$name' could not be resolved: " . $e->getMessage(), __METHOD__ );
            $this->values[$name] = $default;
        }
        return $this->values[$name];
    }

    /**
     * Whether the current user may use a policy function, through the
     * callable fact 'has_access' ( function( $module, $function ) : bool ).
     * Without that fact nobody has access to anything.
     *
     * @param string $module
     * @param string $function
     * @return bool
     */
    public function hasAccess( $module, $function )
    {
        $check = $this->get( 'has_access' );
        return is_callable( $check ) ? (bool)$check( (string)$module, (string)$function ) : false;
    }

    /**
     * A view parameter by name (the names of the view's 'params' in its
     * module.php: ViewMode and NodeID for content/view).
     *
     * @param string $name
     * @return string|null
     */
    public function param( $name )
    {
        $params = $this->get( 'params', array() );
        return is_array( $params ) && isset( $params[$name] ) ? (string)$params[$name] : null;
    }

    /**
     * A request header by name, case-insensitive.
     *
     * @param string $name e.g. 'X-Requested-With'
     * @return string|null
     */
    public function header( $name )
    {
        $headers = $this->get( 'headers', array() );
        $name = strtolower( $name );
        return is_array( $headers ) && isset( $headers[$name] ) ? (string)$headers[$name] : null;
    }

    /**
     * Replaces placeholders in a text with facts: {node_id}, {typed_uri},
     * {param:NodeID}, {header:Host}. A placeholder for a fact that is unknown,
     * or not a plain value, becomes empty.
     *
     * @param string $text
     * @return string
     */
    public function expand( $text )
    {
        $context = $this;
        return preg_replace_callback(
            '/\{([a-z0-9_]+)(?::([A-Za-z0-9_-]+))?\}/',
            function ( $m ) use ( $context )
            {
                if ( isset( $m[2] ) && $m[2] !== '' )
                {
                    if ( $m[1] === 'param' )
                        return (string)$context->param( $m[2] );
                    if ( $m[1] === 'header' )
                        return (string)$context->header( $m[2] );
                    return '';
                }
                $value = $context->get( $m[1] );
                return is_scalar( $value ) ? (string)$value : '';
            },
            (string)$text
        );
    }

    /**
     * The facts resolved so far, for explaining a decision. Resolvers that
     * never ran are listed as '(not needed)'; callables as '(callable)'.
     *
     * @return array
     */
    public function resolvedFacts()
    {
        $facts = array();
        foreach ( $this->values as $name => $value )
        {
            if ( is_object( $value ) && is_callable( $value ) )
                $facts[$name] = '(callable)';
            else if ( is_object( $value ) )
                $facts[$name] = '(' . get_class( $value ) . ')';
            else
                $facts[$name] = $value;
        }
        foreach ( array_keys( $this->resolvers ) as $name )
            $facts[$name] = '(not needed)';
        ksort( $facts );
        return $facts;
    }
}

?>
