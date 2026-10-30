<?php
/**
 * Does at compile time what the template compiler does for a .tpl: 'text'|i18n('context'[, 'comment']) with constant
 * input, context and comment is translated once, when the template is compiled; with an arguments hash the translated
 * text is constant and only the arguments are put in at run time (expi18nargs(), the same strtr() as the compiled
 * .tpl). Compiled templates are kept per siteaccess and locale for it (expTwigEnvironment::variant()).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigConstantVisitor implements \Twig\NodeVisitor\NodeVisitorInterface
{
    public function enterNode( \Twig\Node\Node $node, \Twig\Environment $env ): \Twig\Node\Node
    {
        return $node;
    }

    public function leaveNode( \Twig\Node\Node $node, \Twig\Environment $env ): ?\Twig\Node\Node
    {
        if ( !( $node instanceof \Twig\Node\Expression\FilterExpression ) || $node->getAttribute( 'name' ) !== 'i18n' )
            return $node;
        $input = $node->getNode( 'node' );
        if ( !( $input instanceof \Twig\Node\Expression\ConstantExpression ) || !is_string( $input->getAttribute( 'value' ) ) )
            return $node;
        $args = array();
        foreach ( $node->getNode( 'arguments' ) as $key => $argument )
        {
            if ( !is_int( $key ) && !ctype_digit( (string)$key ) )
                return $node;
            $args[] = $argument;
        }
        if ( count( $args ) > 3 )
            return $node;
        $constants = array();
        foreach ( array_slice( $args, 0, 2 ) as $argument )
        {
            if ( !( $argument instanceof \Twig\Node\Expression\ConstantExpression ) )
                return $node;
            $constants[] = $argument->getAttribute( 'value' );
        }
        $context = isset( $constants[0] ) ? $constants[0] : null;
        $comment = isset( $constants[1] ) ? $constants[1] : null;
        if ( ( $context !== null && !is_string( $context ) ) || ( $comment !== null && !is_string( $comment ) ) )
            return $node;
        $text = ezpI18n::tr( $context, $input->getAttribute( 'value' ), $comment, null );
        $line = $node->getTemplateLine();
        if ( count( $args ) < 3 )
            return new \Twig\Node\Expression\ConstantExpression( $text, $line );
        // the arguments are put in at run time
        $function = $env->getFunction( 'expi18nargs' );
        return new \Twig\Node\Expression\FunctionExpression( $function, new \Twig\Node\Nodes( array(
            new \Twig\Node\Expression\ConstantExpression( $text, $line ), $args[2] ) ), $line );
    }

    public function getPriority(): int
    {
        return 0;
    }
}
