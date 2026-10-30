<?php
/**
 * Appended to the end of every compiled template (expTwigCaptureVisitor): hands the template's variables to
 * expTwigRenderer::captureVariables(), which render() writes back to the template system.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
#[\Twig\Attribute\YieldReady]
class expTwigCaptureNode extends \Twig\Node\Node
{
    public function compile( \Twig\Compiler $compiler ): void
    {
        $compiler->write( "\\expTwigRenderer::captureVariables(\$context);\n" );
    }
}
