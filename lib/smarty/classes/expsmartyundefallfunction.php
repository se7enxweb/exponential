<?php
/**
 * {expundefall}: {undef} without parameters, which removes every variable of the template's
 * root namespace.
 *
 * @package lib
 */
class expSmartyUndefAllFunction implements \Smarty\FunctionHandler\FunctionHandlerInterface
{
    public function handle( $params, \Smarty\Template $template )
    {
        list( $tpl, $rootNamespace ) = expSmartyRenderer::context() ?: array( eZTemplate::factory(), '' );
        if ( isset( $tpl->Variables[$rootNamespace] ) )
        {
            foreach ( array_keys( $tpl->Variables[$rootNamespace] ) as $name )
                $tpl->unsetVariable( $name, $rootNamespace );
        }
        foreach ( array_keys( $template->getTemplateVars( null, false ) ) as $name )
            $template->assign( $name, null );
        return '';
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
