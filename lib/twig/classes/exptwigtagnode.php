<?php
/**
 * The compiled form of the tags of expTwigTagTokenParser: each calls expTwigRenderer (or the shared bridge) with
 * the template's context and writes the context back where the tag changes variables.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
#[\Twig\Attribute\YieldReady]
class expTwigTagNode extends \Twig\Node\Node
{
    public function compile( \Twig\Compiler $compiler ): void
    {
        $compiler->addDebugInfo( $this );
        $kind = $this->getAttribute( 'kind' );
        switch ( $kind )
        {
            case 'include':
                $out = $compiler->getVarName();
                $compiler->write( "[\$$out, \$context] = \\expTwigRenderer::includeTemplate(\$this->env, \$context, " );
                $this->params( $compiler );
                $compiler->raw( ");\n" )->write( "yield \$$out;\n" );
                return;
            case 'set':
                $compiler->write( "\$context = \\expTwigRenderer::setScoped(\$context, " );
                $this->params( $compiler );
                $compiler->raw( ");\n" );
                return;
            case 'undefall':
                $compiler->write( "\$context = \\expTwigRenderer::undefAll(\$context);\n" );
                return;
            case 'setblock':
            case 'appendblock':
                $content = $this->capture( $compiler );
                $compiler->write( "\$context = \\expTwigRenderer::setBlock(\$context, " );
                $this->params( $compiler );
                $compiler->raw( ", \$$content, " . ( $kind === 'appendblock' ? 'true' : 'false' ) . ");\n" );
                return;
            case 'cacheblock':
                $params = $compiler->getVarName();
                $start = $compiler->getVarName();
                $compiler->write( "\$$params = " );
                $this->params( $compiler );
                $compiler->raw( ";\n" )
                         ->write( "\$$start = \\expTwigRenderer::cacheBlockStart(\$$params, \$this->getTemplateName(), \$$params" . "['id'] ?? 0);\n" )
                         ->write( "if (\$$start" . "[0]) {\n" )->indent()
                         ->write( "yield \$$start" . "[1];\n" )->outdent()
                         ->write( "} else {\n" )->indent();
                $content = $this->capture( $compiler );
                $compiler->write( "\\expTwigRenderer::cacheBlockEnd(\$$start, \$$content);\n" )
                         ->write( "yield \$$content;\n" )->outdent()
                         ->write( "}\n" );
                return;
            case 'runonce':
                $params = $compiler->getVarName();
                $compiler->write( "\$$params = " );
                $this->params( $compiler );
                $compiler->raw( ";\n" )
                         ->write( "if (\\expTwigRenderer::runOnce(\$this->getTemplateName(), \$$params" . "['id'] ?? 0)) {\n" )->indent()
                         ->subcompile( $this->getNode( 'body' ) )->outdent()
                         ->write( "}\n" );
                return;
            case 'while':
                $rounds = $compiler->getVarName();
                $limit = $compiler->getVarName();
                $compiler->write( "\$$rounds = 0;\n" )
                         ->write( "\$$limit = \\expTwigRenderer::whileLoopLimit();\n" )
                         ->write( 'while (' )->subcompile( $this->getNode( 'condition' ) )->raw( ") {\n" )->indent()
                         ->write( "if (++\$$rounds > \$$limit) {\n" )->indent()
                         ->write( "\\expTwigRenderer::whileLimitReached(\$this->getTemplateName(), \$$limit);\n" )
                         ->write( "break;\n" )->outdent()
                         ->write( "}\n" )
                         ->subcompile( $this->getNode( 'body' ) )->outdent()
                         ->write( "}\n" );
                return;
            case 'blockfunction':
                $content = $this->capture( $compiler );
                $compiler->write( 'yield \\expTemplateBridge::callBlockFunction(' )->repr( $this->getAttribute( 'name' ) )->raw( ', ' );
                $this->params( $compiler );
                $compiler->raw( ", \$$content);\n" );
                return;
        }
    }

    /**
     * Compiles the parameters as a PHP array.
     */
    private function params( \Twig\Compiler $compiler )
    {
        $compiler->raw( 'array(' );
        $first = true;
        foreach ( $this->getNode( 'params' ) as $name => $node )
        {
            $compiler->raw( $first ? '' : ', ' )->repr( (string)$name )->raw( ' => ' )->subcompile( $node );
            $first = false;
        }
        $compiler->raw( ')' );
    }

    /**
     * Compiles the body into a variable holding its output; returns the variable's name.
     */
    private function capture( \Twig\Compiler $compiler )
    {
        $variable = $compiler->getVarName();
        $capture = new \Twig\Node\CaptureNode( $this->getNode( 'body' ), $this->getTemplateLine() );
        $capture->setAttribute( 'raw', true );
        $compiler->write( "\$$variable = " )->subcompile( $capture )->raw( "\n" );
        return $variable;
    }
}
