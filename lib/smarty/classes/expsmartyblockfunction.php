<?php
/**
 * Smarty block that runs an Exponential template function with children, e.g.
 * {explblock name='content'}...{/explblock}. The block's output is handed to the function as
 * its children (one text node), so a function that wraps or post-processes its children
 * behaves as in a .tpl.
 *
 * @package lib
 */
class expSmartyBlockFunction implements \Smarty\BlockHandler\BlockHandlerInterface
{
    /** @var string */
    private $name;

    public function __construct( $name )
    {
        $this->name = $name;
    }

    public function handle( $params, $content, \Smarty\Template $template, &$repeat )
    {
        if ( $content === null )
            return '';
        list( $tpl, $rootNamespace, $currentNamespace ) = expSmartyRenderer::context() ?: array( eZTemplate::factory(), '', '' );
        $functionParameters = array();
        foreach ( $params as $name => $value )
            $functionParameters[$name] = array( array( eZTemplate::TYPE_ARRAY, expSmartyRenderer::unwrap( $value ) ) );
        $children = array( array( eZTemplate::NODE_TEXT, false, $content ) );
        $textElements = array();
        $tpl->processFunction( $this->name, $textElements, $children, $functionParameters, false, $rootNamespace, $currentNamespace );
        return implode( '', $textElements );
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
