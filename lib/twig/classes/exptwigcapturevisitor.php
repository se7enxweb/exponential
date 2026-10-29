<?php
/**
 * Adds expTwigCaptureNode at the end of every template's display code.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigCaptureVisitor implements \Twig\NodeVisitor\NodeVisitorInterface
{
    public function enterNode( \Twig\Node\Node $node, \Twig\Environment $env ): \Twig\Node\Node
    {
        return $node;
    }

    public function leaveNode( \Twig\Node\Node $node, \Twig\Environment $env ): ?\Twig\Node\Node
    {
        if ( $node instanceof \Twig\Node\ModuleNode )
            $node->setNode( 'display_end', new \Twig\Node\Nodes( array( $node->getNode( 'display_end' ), new expTwigCaptureNode() ) ) );
        return $node;
    }

    public function getPriority(): int
    {
        return 255;
    }
}
