<?php
/**
 * Smarty function tag that runs an Exponential template function, e.g.
 * {attribute_view_gui attribute=$node.data_map.body} or {node_view_gui view='line' content_node=$child}.
 * With assign=<name> the output goes into that Smarty variable instead.
 *
 * @package lib
 */
class expSmartyFunction implements \Smarty\FunctionHandler\FunctionHandlerInterface
{
    /** @var string */
    private $name;

    public function __construct( $name )
    {
        $this->name = $name;
    }

    public function handle( $params, \Smarty\Template $template )
    {
        $assign = null;
        if ( isset( $params['assign'] ) )
        {
            $assign = (string)$params['assign'];
            unset( $params['assign'] );
        }
        $output = expSmartyRenderer::callFunction( $this->name, $params );
        if ( $assign !== null )
        {
            $template->assign( $assign, $output );
            return '';
        }
        return $output;
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
