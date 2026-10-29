<?php
/**
 * Template operator exp_smarty_render( <file> ).
 *
 * The stub every *.stpl is compiled to calls it. Running at render time, on every request and
 * on the compiled path as well, it renders the Smarty template with the template variables of
 * the namespace it is called in.
 *
 * It can also be used directly in a .tpl: {exp_smarty_render( 'extension/x/design/y/templates/z.stpl' )}
 *
 * @package lib
 */
class expSmartyOperator
{
    function operatorList()
    {
        return array( 'exp_smarty_render' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'exp_smarty_render' => array(
                'file' => array( 'type' => 'string', 'required' => true, 'default' => '' ),
            ),
        );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = expSmartyRenderer::render( $tpl, (string)$namedParameters['file'], $rootNamespace, $currentNamespace );
    }
}
