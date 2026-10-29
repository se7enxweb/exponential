<?php
/**
 * {expfetch assign=children module=content function=list parent_node_id=$node.node_id limit=10}
 *
 * Runs the fetch operator with the remaining parameters and puts the result into the Smarty
 * variable named by assign.
 *
 * @package lib
 */
class expSmartyFetchFunction implements \Smarty\FunctionHandler\FunctionHandlerInterface
{
    public function handle( $params, \Smarty\Template $template )
    {
        foreach ( array( 'assign', 'module', 'function' ) as $required )
        {
            if ( !isset( $params[$required] ) || $params[$required] === '' )
                throw new \Smarty\Exception( "expfetch: missing parameter '$required'" );
        }
        $assign = (string)$params['assign'];
        $module = (string)$params['module'];
        $function = (string)$params['function'];
        unset( $params['assign'], $params['module'], $params['function'] );

        $template->assign( $assign, expSmartyRenderer::callOperator( 'fetch', $module, array( $function, $params ) ) );
        return '';
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
