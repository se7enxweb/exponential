<?php
/**
 * Compiles a.b as a['b'] (Twig's array lookup) where that means the same, so Twig writes the lookup of an array inline
 * ($a['b'] ?? null) instead of calling CoreExtension::getAttribute(): in a template of the bridge every value is an
 * array, a scalar or an object of the template system behind its proxy (ArrayAccess: a.b reads attribute( 'b' ) either
 * way), and the sandbox allows no method and no property, so the other forms of a.b (a property, a method a.b())
 * could only be refused. A lookup with arguments (a.b(c)) and a.b where the sandbox's policy allows methods or
 * properties ([TwigSettings] SecurityAllowedMethodList[], SecurityAllowedPropertyList[]) or no sandbox at all keep
 * Twig's own lookup.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigAttributeVisitor implements \Twig\NodeVisitor\NodeVisitorInterface
{
    public function enterNode( \Twig\Node\Node $node, \Twig\Environment $env ): \Twig\Node\Node
    {
        return $node;
    }

    public function leaveNode( \Twig\Node\Node $node, \Twig\Environment $env ): ?\Twig\Node\Node
    {
        if ( !( $node instanceof \Twig\Node\Expression\GetAttrExpression ) || $node->getAttribute( 'type' ) !== \Twig\Template::ANY_CALL ||
             ( $node->hasNode( 'arguments' ) && count( $node->getNode( 'arguments' ) ) ) || !self::onlyAttributes( $env ) )
            return $node;
        $node->setAttribute( 'type', \Twig\Template::ARRAY_CALL );
        return $node;
    }

    /** Whether the sandbox's policy is the bridge's and allows no method and no property. */
    private static function onlyAttributes( \Twig\Environment $env )
    {
        if ( !$env->hasExtension( \Twig\Extension\SandboxExtension::class ) )
            return false;
        $policy = $env->getExtension( \Twig\Extension\SandboxExtension::class )->getSecurityPolicy();
        return $policy instanceof expTwigSecurity && $policy->allowsNoMethodOrProperty();
    }

    public function getPriority(): int
    {
        return 0;
    }
}
