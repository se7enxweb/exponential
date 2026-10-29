<?php
/**
 * {expset name=x value=... scope=root|global|relative namespace=...}: {set}/{def} with a scope or a
 * namespace, into the template system's namespaces. When the target is the template's own root
 * namespace, the Smarty variable is set as well.
 *
 * @package lib
 */
class expSmartySetFunction implements \Smarty\FunctionHandler\FunctionHandlerInterface
{
    public function handle( $params, \Smarty\Template $template )
    {
        if ( !isset( $params['name'] ) || (string)$params['name'] === '' )
            throw new \Smarty\Exception( "expset: missing parameter 'name'" );
        expSmartyRenderer::setScoped( $template, (string)$params['name'], isset( $params['value'] ) ? $params['value'] : null,
                                         isset( $params['scope'] ) ? (string)$params['scope'] : 'relative',
                                         isset( $params['namespace'] ) ? (string)$params['namespace'] : '' );
        return '';
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
