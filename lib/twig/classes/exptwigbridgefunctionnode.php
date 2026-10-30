<?php
/**
 * A bridged operator or template function used as a Twig function, compiled as a direct static call:
 * \expTwigBridgeCalls::call('<operator>', [<arguments>]) or \expTwigBridgeCalls::templateFunction('<name>', <hash>).
 * See expTwigBridgeFilterNode for why. Named arguments are compiled as Twig compiles them.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigBridgeFunctionNode extends \Twig\Node\Expression\FunctionExpression
{
    #[\Twig\Attribute\FirstClassTwigCallableReady]
    public function __construct( $function, \Twig\Node\Node $arguments, int $lineno )
    {
        parent::__construct( $function, $arguments, $lineno );
    }

    public function compile( \Twig\Compiler $compiler ): void
    {
        $name = $this->getAttribute( 'name' );
        $arguments = self::positional( $this->getNode( 'arguments' ) );
        if ( $arguments === null )
        {
            parent::compile( $compiler );
            return;
        }
        if ( expTwigExtension::isBridgedFunction( $name ) )
        {
            $compiler->raw( '\expTwigBridgeCalls::templateFunction(' )->string( $name );
            if ( $arguments )
                $compiler->raw( ', ' )->subcompile( $arguments[0] );
            $compiler->raw( ')' );
            return;
        }
        $operator = expTwigExtension::operatorFor( $name );
        if ( $operator === null )
        {
            parent::compile( $compiler );
            return;
        }
        $compiler->raw( '\expTwigBridgeCalls::call(' )->string( $operator )->raw( ', [' );
        self::compileList( $compiler, $arguments );
        $compiler->raw( '])' );
    }

    /**
     * @return \Twig\Node\Node[]|null the arguments in order, or null when one is named
     */
    public static function positional( \Twig\Node\Node $arguments )
    {
        $list = array();
        foreach ( $arguments as $key => $argument )
        {
            if ( !is_int( $key ) && !ctype_digit( (string)$key ) )
                return null;
            $list[] = $argument;
        }
        return $list;
    }

    public static function compileList( \Twig\Compiler $compiler, array $arguments )
    {
        foreach ( $arguments as $i => $argument )
        {
            if ( $i > 0 )
                $compiler->raw( ', ' );
            $compiler->subcompile( $argument );
        }
    }
}
