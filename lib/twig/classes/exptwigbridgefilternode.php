<?php
/**
 * A bridged operator used as a filter, compiled as a direct static call: \expTwigBridgeCalls::filter('<operator>',
 * <input>, [<arguments>]). The compiled template then never looks the filter up in the environment, which would make
 * Twig set up all of its extensions on the first lookup of a request. Named arguments are compiled as Twig compiles
 * them (through the filter's callable).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigBridgeFilterNode extends \Twig\Node\Expression\FilterExpression
{
    #[\Twig\Attribute\FirstClassTwigCallableReady]
    public function __construct( \Twig\Node\Node $node, $filter, \Twig\Node\Node $arguments, int $lineno )
    {
        parent::__construct( $node, $filter, $arguments, $lineno );
    }

    public function compile( \Twig\Compiler $compiler ): void
    {
        $operator = expTwigExtension::operatorFor( $this->getAttribute( 'name' ) );
        $arguments = expTwigBridgeFunctionNode::positional( $this->getNode( 'arguments' ) );
        if ( $operator === null || $arguments === null )
        {
            parent::compile( $compiler );
            return;
        }
        $compiler->raw( '\expTwigBridgeCalls::filter(' )->string( $operator )->raw( ', ' )->subcompile( $this->getNode( 'node' ) )->raw( ', [' );
        expTwigBridgeFunctionNode::compileList( $compiler, $arguments );
        $compiler->raw( '])' );
    }
}
