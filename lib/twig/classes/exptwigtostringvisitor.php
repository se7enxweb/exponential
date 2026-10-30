<?php
/**
 * Removes the run-time __toString() checks Twig's sandbox wraps around every print and every argument
 * (CheckToStringNode, SandboxExtension::ensureToStringAllowed()). The policy of the bridge, expTwigSecurity, allows
 * __toString() on every object (printing a value is what a template is for; the method and property checks stay), so
 * the check could never refuse anything, while it doubled the time of a render (it is called for every value printed
 * or passed, and walks every array). The tags, filters, functions, methods and properties are still checked.
 *
 * It runs after Twig's sandbox visitor and only while the sandbox's policy is expTwigSecurity (any other policy keeps
 * Twig's checks).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigToStringVisitor implements \Twig\NodeVisitor\NodeVisitorInterface
{
    public function enterNode( \Twig\Node\Node $node, \Twig\Environment $env ): \Twig\Node\Node
    {
        return $node;
    }

    public function leaveNode( \Twig\Node\Node $node, \Twig\Environment $env ): ?\Twig\Node\Node
    {
        if ( !( $node instanceof \Twig\Node\CheckToStringNode ) || !$env->hasExtension( \Twig\Extension\SandboxExtension::class ) ||
             !( $env->getExtension( \Twig\Extension\SandboxExtension::class )->getSecurityPolicy() instanceof expTwigSecurity ) )
            return $node;
        return $node->getNode( 'expr' );
    }

    /** After Twig's SandboxNodeVisitor (priority 0), which adds the checks. */
    public function getPriority(): int
    {
        return 10;
    }
}
